<?php
session_start();
require_once __DIR__ . '/admin/classes/DbConection.php';
require_once __DIR__ . '/admin/classes/SessionData.php';

header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function pagina($titulo, $cuerpo)
{
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="robots" content="noindex"><title>' . h($titulo) . '</title><style>'
        . 'body{font-family:system-ui,Segoe UI,Arial,sans-serif;background:#f4f6f9;color:#1f2937;margin:0;padding:24px 16px}'
        . '.c{max-width:920px;margin:0 auto;background:#fff;border:1px solid #d9dee5;border-radius:10px;padding:24px}'
        . 'h1{font-size:22px;margin:0 0 6px}p.s{color:#6b7280;margin:0 0 18px}'
        . 'table{width:100%;border-collapse:collapse;margin:14px 0}th,td{text-align:left;padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:14px}'
        . '.box{border-radius:8px;padding:12px 14px;margin:14px 0;font-size:14px}.warn{background:#fffbeb;border:1px solid #fcd34d}.bad{background:#fef2f2;border:1px solid #fca5a5}.good{background:#ecfdf5;border:1px solid #6ee7b7}'
        . 'button,a.btn{display:inline-block;background:#1d4ed8;color:#fff;border:0;border-radius:8px;padding:9px 16px;font-size:14px;cursor:pointer;text-decoration:none}button:disabled{background:#9ca3af;cursor:not-allowed}'
        . '.rojo{background:#b91c1c}.sec{background:#fff;color:#374151;border:1px solid #9ca3af}code{background:#f3f4f6;padding:1px 5px;border-radius:4px}label{display:block;margin:8px 0}input[type=text]{padding:8px;border:1px solid #9ca3af;border-radius:6px;font-size:15px}'
        . '</style></head><body><div class="c">' . $cuerpo . '</div></body></html>';
    exit;
}

if (!SessionData::superAdministrador()) {
    http_response_code(403);
    pagina('Acceso restringido', '<h1>Acceso restringido</h1><p class="s">Este asistente solo lo puede usar un SuperAdministrador con la sesión iniciada.</p><p><a href="login.php">Iniciar sesión</a></p>');
}

if (empty($_SESSION['limpiar_token'])) {
    $_SESSION['limpiar_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['limpiar_token'];

$db = new DbConection();
$pdo = $db->openConect();
$pdo->exec('USE `' . $db->getDbName() . '`');
$pdo->exec('SET NAMES utf8mb4');

function idsIntentos(PDO $pdo, int $fichaId, string $alcance): array
{
    $sql = 'SELECT id FROM tbl_cuestionario_intentos WHERE tbl_ficha_tecnica_encuesta_id = :f';
    if ($alcance === 'prueba') {
        $sql .= ' AND es_prueba = 1';
    }
    $st = $pdo->prepare($sql);
    $st->execute([':f' => $fichaId]);
    return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
}

function fichaActiva(PDO $pdo, int $fichaId): bool
{
    $st = $pdo->prepare("SELECT COUNT(*) FROM tbl_ficha_tecnica_encuestas WHERE id = :f AND habilitado = 'si' AND eliminado = 'no'");
    $st->execute([':f' => $fichaId]);
    return (bool)$st->fetchColumn();
}

function marcadores(array $ids): string
{
    return implode(',', array_fill(0, count($ids), '?'));
}

function filasSql(PDO $pdo, string $tabla, string $where, array $ids): string
{
    $st = $pdo->prepare("SELECT * FROM `$tabla` WHERE $where IN (" . marcadores($ids) . ')');
    $st->execute($ids);
    $out = '';
    while ($f = $st->fetch(PDO::FETCH_ASSOC)) {
        $vals = array_map(function ($v) use ($pdo) {
            return $v === null ? 'NULL' : $pdo->quote((string)$v);
        }, $f);
        $out .= "INSERT INTO `$tabla` (`" . implode('`,`', array_keys($f)) . '`) VALUES (' . implode(',', $vals) . ");\n";
    }
    return $out;
}

$accion = $_POST['accion'] ?? ($_GET['accion'] ?? '');
$fichaId = (int)($_POST['ficha'] ?? ($_GET['ficha'] ?? 0));
if ($fichaId > 0 && !fichaActiva($pdo, $fichaId)) {
    pagina('Ficha no permitida', '<div class="box bad">Este asistente solo trabaja con fichas activas (habilitadas y no eliminadas).</div><p><a href="limpiar_respuestas.php">Volver</a></p>');
}
$alcance = (($_POST['alcance'] ?? ($_GET['alcance'] ?? 'todas')) === 'prueba') ? 'prueba' : 'todas';

if ($accion === 'respaldo' && $fichaId > 0 && hash_equals($token, (string)($_GET['token'] ?? ''))) {
    $ids = idsIntentos($pdo, $fichaId, $alcance);
    if (!$ids) {
        pagina('Sin datos', '<div class="box warn">No hay respuestas para respaldar.</div><p><a href="limpiar_respuestas.php">Volver</a></p>');
    }
    header('Content-Type: application/sql; charset=utf-8');
    header('Content-Disposition: attachment; filename="respaldo-respuestas-ficha-' . $fichaId . '-' . date('Ymd-His') . '.sql"');
    echo "-- Respaldo de respuestas, ficha $fichaId, alcance $alcance\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n";
    echo filasSql($pdo, 'tbl_cuestionario_intentos', 'id', $ids);
    echo filasSql($pdo, 'tbl_cuestionario_respuestas', 'tbl_intento_id', $ids);
    echo filasSql($pdo, 'tbl_certificacion_encuestador', 'tbl_cuestionario_intento_id', $ids);
    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    exit;
}

if ($accion === 'eliminar') {
    if (!hash_equals($token, (string)($_POST['token'] ?? '')) || empty($_POST['respaldo']) || trim((string)($_POST['confirmacion'] ?? '')) !== 'ELIMINAR' || $fichaId < 1) {
        pagina('Solicitud inválida', '<div class="box bad">Solicitud inválida. Marque la casilla del respaldo y escriba ELIMINAR.</div><p><a href="limpiar_respuestas.php">Volver</a></p>');
    }
    $ids = idsIntentos($pdo, $fichaId, $alcance);
    if (!$ids) {
        pagina('Sin datos', '<div class="box warn">No hay respuestas que eliminar.</div><p><a href="limpiar_respuestas.php">Volver</a></p>');
    }
    $conCert = !empty($_POST['certificaciones']);
    $raiz = realpath(__DIR__);
    $archivos = [];
    try {
        $pdo->beginTransaction();
        $stR = $pdo->prepare('DELETE FROM tbl_cuestionario_respuestas WHERE tbl_intento_id IN (' . marcadores($ids) . ')');
        $stR->execute($ids);
        $nResp = $stR->rowCount();
        $nCert = 0;
        if ($conCert) {
            $stA = $pdo->prepare('SELECT audio_ruta FROM tbl_certificacion_encuestador WHERE tbl_cuestionario_intento_id IN (' . marcadores($ids) . ') AND audio_ruta IS NOT NULL AND audio_ruta <> \'\'');
            $stA->execute($ids);
            $archivos = $stA->fetchAll(PDO::FETCH_COLUMN);
            $stC = $pdo->prepare('DELETE FROM tbl_certificacion_encuestador WHERE tbl_cuestionario_intento_id IN (' . marcadores($ids) . ')');
            $stC->execute($ids);
            $nCert = $stC->rowCount();
        } else {
            $stC = $pdo->prepare('UPDATE tbl_certificacion_encuestador SET tbl_cuestionario_intento_id = NULL WHERE tbl_cuestionario_intento_id IN (' . marcadores($ids) . ')');
            $stC->execute($ids);
        }
        $stI = $pdo->prepare('DELETE FROM tbl_cuestionario_intentos WHERE id IN (' . marcadores($ids) . ')');
        $stI->execute($ids);
        $nInt = $stI->rowCount();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        pagina('Error', '<div class="box bad">No se eliminó nada. Error: ' . h($e->getMessage()) . '</div><p><a href="limpiar_respuestas.php">Volver</a></p>');
    }
    $nArch = 0;
    foreach ($archivos as $ruta) {
        $abs = realpath($raiz . '/' . ltrim((string)$ruta, '/\\'));
        if ($abs && strpos($abs, $raiz . DIRECTORY_SEPARATOR) === 0 && is_file($abs) && @unlink($abs)) {
            $nArch++;
        }
    }
    pagina('Resultado', '<h1>Eliminación terminada</h1><div class="box good">Encuestas eliminadas: <b>' . $nInt . '</b> · Respuestas: <b>' . $nResp . '</b>'
        . ($conCert ? ' · Certificaciones: <b>' . $nCert . '</b> · Audios borrados del disco: <b>' . $nArch . '</b>' : '') . '</div>'
        . '<form method="post"><input type="hidden" name="token" value="' . h($token) . '"><input type="hidden" name="accion" value="borrar_archivo">'
        . '<p>Por seguridad, elimine este asistente del servidor al terminar.</p><button type="submit" class="rojo">Eliminar este archivo del servidor</button></form>'
        . '<p><a href="limpiar_respuestas.php">Volver al listado</a></p>');
}

if ($accion === 'borrar_archivo' && hash_equals($token, (string)($_POST['token'] ?? ''))) {
    $ok = @unlink(__FILE__);
    pagina('Asistente eliminado', $ok ? '<h1>Asistente eliminado</h1><p class="s">El archivo <code>limpiar_respuestas.php</code> se borró del servidor.</p>'
        : '<h1>No se pudo borrar</h1><p class="s">Elimine <code>administrativo/limpiar_respuestas.php</code> manualmente.</p>');
}

if ($accion === 'confirmar' && $fichaId > 0) {
    $ids = idsIntentos($pdo, $fichaId, $alcance);
    $ficha = $pdo->prepare('SELECT id, tipo_estudio FROM tbl_ficha_tecnica_encuestas WHERE id = :f');
    $ficha->execute([':f' => $fichaId]);
    $fRow = $ficha->fetch(PDO::FETCH_ASSOC);
    if (!$fRow || !$ids) {
        pagina('Sin datos', '<div class="box warn">No hay respuestas para esa ficha y ese alcance.</div><p><a href="limpiar_respuestas.php">Volver</a></p>');
    }
    $nResp = $pdo->prepare('SELECT COUNT(*) FROM tbl_cuestionario_respuestas WHERE tbl_intento_id IN (' . marcadores($ids) . ')');
    $nResp->execute($ids);
    $nCert = $pdo->prepare('SELECT COUNT(*) FROM tbl_certificacion_encuestador WHERE tbl_cuestionario_intento_id IN (' . marcadores($ids) . ')');
    $nCert->execute($ids);
    $cuerpo = '<h1>Confirmar eliminación · Ficha ' . $fichaId . '</h1>'
        . '<div class="box bad">Se eliminarán <b>' . count($ids) . '</b> encuestas diligenciadas y <b>' . (int)$nResp->fetchColumn() . '</b> respuestas '
        . ($alcance === 'prueba' ? '(solo las marcadas como prueba)' : '(todas las de la ficha)') . '. Esta acción es definitiva.</div>'
        . '<p><a class="btn sec" href="?accion=respaldo&ficha=' . $fichaId . '&alcance=' . h($alcance) . '&token=' . h($token) . '">1. Descargar respaldo (.sql) de lo que se eliminará</a></p>'
        . '<form method="post" onsubmit="return confirm(\'¿Eliminar definitivamente estas respuestas?\')">'
        . '<input type="hidden" name="token" value="' . h($token) . '"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="ficha" value="' . $fichaId . '"><input type="hidden" name="alcance" value="' . h($alcance) . '">'
        . '<label><input type="checkbox" name="certificaciones" value="1"> También eliminar las certificaciones de encuestador ligadas (' . (int)$nCert->fetchColumn() . ': audio y GPS). Si no la marca, se conservan sin ligar a una encuesta.</label>'
        . '<label><input type="checkbox" name="respaldo" value="1"> Ya descargué el respaldo</label>'
        . '<label>2. Escriba <b>ELIMINAR</b> para confirmar: <input type="text" name="confirmacion" autocomplete="off"></label>'
        . '<button type="submit" class="rojo">Eliminar definitivamente</button> <a class="btn sec" href="limpiar_respuestas.php">Cancelar</a></form>';
    pagina('Confirmar eliminación', $cuerpo);
}

$filas = '';
$rs = $pdo->query('SELECT f.id, f.eliminado, f.habilitado,
        (SELECT COUNT(*) FROM tbl_cuestionario_intentos i WHERE i.tbl_ficha_tecnica_encuesta_id = f.id) AS total,
        (SELECT COUNT(*) FROM tbl_cuestionario_intentos i WHERE i.tbl_ficha_tecnica_encuesta_id = f.id AND i.es_prueba = 1) AS pruebas
    FROM tbl_ficha_tecnica_encuestas f WHERE f.habilitado = "si" AND f.eliminado = "no" ORDER BY f.id');
foreach ($rs->fetchAll(PDO::FETCH_ASSOC) as $f) {
    if ((int)$f['total'] === 0) {
        continue;
    }
    $filas .= '<tr><td>Ficha ' . (int)$f['id'] . ($f['eliminado'] === 'si' ? ' <small>(eliminada)</small>' : '') . '</td><td>' . (int)$f['total'] . '</td><td>' . (int)$f['pruebas'] . '</td><td>'
        . '<form method="get" style="margin:0"><input type="hidden" name="accion" value="confirmar"><input type="hidden" name="ficha" value="' . (int)$f['id'] . '">'
        . '<select name="alcance"><option value="todas">Todas las encuestas</option>' . ((int)$f['pruebas'] > 0 ? '<option value="prueba">Solo las de prueba</option>' : '') . '</select> '
        . '<button type="submit">Continuar</button></form></td></tr>';
}
$cuerpo = '<h1>Eliminar respuestas de encuestas</h1><p class="s">Base de datos: <code>' . h($db->getDbName()) . '</code> · Usuario: ' . h(SessionData::getUserFullName()) . '</p>'
    . '<p>Sirve para quitar las encuestas diligenciadas de la ficha activa (por ejemplo las de prueba) y poder editar sus preguntas y opciones. Los votantes y las preguntas no se tocan.</p>'
    . ($filas ? '<table><tr><th>Ficha</th><th>Encuestas</th><th>De prueba</th><th>Acción</th></tr>' . $filas . '</table>'
        : '<div class="box good">No hay encuestas diligenciadas en ninguna ficha.</div>')
    . '<form method="post"><input type="hidden" name="token" value="' . h($token) . '"><input type="hidden" name="accion" value="borrar_archivo"><button type="submit" class="sec">Eliminar este archivo del servidor</button></form>';
pagina('Eliminar respuestas', $cuerpo);
