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
        . '.c{max-width:880px;margin:0 auto;background:#fff;border:1px solid #d9dee5;border-radius:10px;padding:24px}'
        . 'h1{font-size:22px;margin:0 0 6px}p.s{color:#6b7280;margin:0 0 18px}'
        . 'table{width:100%;border-collapse:collapse;margin:14px 0}th,td{text-align:left;padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:14px}'
        . '.ok{color:#047857;font-weight:600}.pend{color:#b45309;font-weight:600}.err{color:#b91c1c;font-weight:600}'
        . '.box{border-radius:8px;padding:12px 14px;margin:14px 0;font-size:14px}.warn{background:#fffbeb;border:1px solid #fcd34d}.bad{background:#fef2f2;border:1px solid #fca5a5}.good{background:#ecfdf5;border:1px solid #6ee7b7}'
        . 'button{background:#1d4ed8;color:#fff;border:0;border-radius:8px;padding:10px 18px;font-size:15px;cursor:pointer}button:disabled{background:#9ca3af;cursor:not-allowed}'
        . 'button.sec{background:#fff;color:#b91c1c;border:1px solid #b91c1c}code{background:#f3f4f6;padding:1px 5px;border-radius:4px}'
        . '</style></head><body><div class="c">' . $cuerpo . '</div></body></html>';
    exit;
}

if (!SessionData::superAdministrador()) {
    http_response_code(403);
    pagina('Acceso restringido', '<h1>Acceso restringido</h1><p class="s">Este asistente solo lo puede usar un SuperAdministrador con la sesión iniciada.</p><p><a href="login.php">Iniciar sesión</a></p>');
}

if (empty($_SESSION['migrar_token'])) {
    $_SESSION['migrar_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['migrar_token'];

$dirSql = __DIR__ . '/admin/db/produccion/';
$archivos = array_merge(glob($dirSql . 'fase-i-*.sql') ?: [], glob($dirSql . 'fase-j-*.sql') ?: []);
sort($archivos, SORT_STRING);

function dividirSentencias(string $sql): array
{
    $res = [];
    $act = '';
    $n = strlen($sql);
    $comilla = '';
    for ($i = 0; $i < $n; $i++) {
        $c = $sql[$i];
        $sig = $i + 1 < $n ? $sql[$i + 1] : '';
        if ($comilla !== '') {
            $act .= $c;
            if ($c === '\\' && $comilla !== '`') {
                $act .= $sig;
                $i++;
            } elseif ($c === $comilla) {
                if ($sig === $comilla) {
                    $act .= $sig;
                    $i++;
                } else {
                    $comilla = '';
                }
            }
            continue;
        }
        if ($c === '-' && $sig === '-') {
            $fin = strpos($sql, "\n", $i);
            $i = $fin === false ? $n : $fin;
            $act .= "\n";
            continue;
        }
        if ($c === '#') {
            $fin = strpos($sql, "\n", $i);
            $i = $fin === false ? $n : $fin;
            $act .= "\n";
            continue;
        }
        if ($c === '/' && $sig === '*') {
            $fin = strpos($sql, '*/', $i + 2);
            $i = $fin === false ? $n : $fin + 1;
            continue;
        }
        if ($c === "'" || $c === '"' || $c === '`') {
            $comilla = $c;
            $act .= $c;
            continue;
        }
        if ($c === ';') {
            if (trim($act) !== '') {
                $res[] = trim($act);
            }
            $act = '';
            continue;
        }
        $act .= $c;
    }
    if (trim($act) !== '') {
        $res[] = trim($act);
    }
    return $res;
}

$db = new DbConection();
$pdo = $db->openConect();
$pdo->exec('USE `' . $db->getDbName() . '`');
$pdo->exec('SET NAMES utf8mb4');

$tablaMig = (bool)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_migraciones_ejecutadas'")->fetchColumn();
$registradas = [];
if ($tablaMig) {
    $registradas = array_flip($pdo->query('SELECT nombre_migracion FROM tbl_migraciones_ejecutadas')->fetchAll(PDO::FETCH_COLUMN));
}

$faltantes = [];
if ($tablaMig && is_file($dirSql . 'fase-i-000-verificar-prerrequisitos.sql')) {
    foreach (dividirSentencias(file_get_contents($dirSql . 'fase-i-000-verificar-prerrequisitos.sql')) as $sentencia) {
        $faltantes = $pdo->query($sentencia)->fetchAll(PDO::FETCH_COLUMN);
    }
} elseif (!$tablaMig) {
    $faltantes = ['tbl_migraciones_ejecutadas (ejecute primero fase-d-001-rbac-schema.sql)'];
}

$lista = [];
foreach ($archivos as $ruta) {
    $nombre = basename($ruta, '.sql');
    if ($nombre === 'fase-i-000-verificar-prerrequisitos') {
        continue;
    }
    $lista[] = ['nombre' => $nombre, 'ruta' => $ruta, 'aplicada' => isset($registradas[$nombre])];
}
$pendientes = array_filter($lista, function ($m) { return !$m['aplicada']; });

if (($_POST['accion'] ?? '') === 'borrar_archivo' && hash_equals($token, (string)($_POST['token'] ?? ''))) {
    $ok = @unlink(__FILE__);
    pagina('Asistente eliminado', $ok
        ? '<h1>Asistente eliminado</h1><p class="s">El archivo <code>migrar_produccion.php</code> se borró del servidor.</p>'
        : '<h1>No se pudo borrar</h1><p class="s">Elimine <code>administrativo/migrar_produccion.php</code> manualmente.</p>');
}

if (($_POST['accion'] ?? '') === 'ejecutar') {
    if (!hash_equals($token, (string)($_POST['token'] ?? '')) || empty($_POST['respaldo'])) {
        pagina('Solicitud inválida', '<div class="box bad">Solicitud inválida. Vuelva e intente de nuevo confirmando el respaldo.</div><p><a href="migrar_produccion.php">Volver</a></p>');
    }
    if ($faltantes) {
        pagina('Faltan prerrequisitos', '<div class="box bad">No se puede continuar: faltan migraciones previas (' . h(implode(', ', $faltantes)) . ').</div><p><a href="migrar_produccion.php">Volver</a></p>');
    }
    @set_time_limit(0);
    $filas = '';
    $fallo = false;
    foreach ($pendientes as $m) {
        $t0 = microtime(true);
        $hechas = 0;
        $msg = '';
        try {
            foreach (dividirSentencias(file_get_contents($m['ruta'])) as $sentencia) {
                $pdo->exec($sentencia);
                $hechas++;
            }
        } catch (Throwable $e) {
            $fallo = true;
            $msg = $e->getMessage();
        }
        $filas .= '<tr><td><code>' . h($m['nombre']) . '</code></td><td>' . $hechas . '</td><td>'
            . ($msg === '' ? '<span class="ok">Aplicada</span> (' . round((microtime(true) - $t0) * 1000) . ' ms)' : '<span class="err">Error</span><br><small>' . h($msg) . '</small>')
            . '</td></tr>';
        if ($fallo) {
            break;
        }
    }
    $cuerpo = '<h1>Resultado</h1><table><tr><th>Migración</th><th>Sentencias</th><th>Estado</th></tr>' . $filas . '</table>';
    if ($fallo) {
        $cuerpo .= '<div class="box bad">Se detuvo en el primer error. Las migraciones anteriores quedaron aplicadas y las siguientes no se ejecutaron. Corrija la causa y vuelva a abrir esta página: continúa desde la que falló.</div>';
    } else {
        $cuerpo .= '<div class="box good">Todas las migraciones pendientes se aplicaron correctamente.</div>'
            . '<form method="post"><input type="hidden" name="token" value="' . h($token) . '"><input type="hidden" name="accion" value="borrar_archivo">'
            . '<p>Por seguridad, elimine este asistente del servidor ahora que terminó.</p><button type="submit" class="sec">Eliminar este archivo del servidor</button></form>';
    }
    $cuerpo .= '<p><a href="migrar_produccion.php">Ver estado</a></p>';
    pagina('Resultado de migraciones', $cuerpo);
}

$filas = '';
foreach ($lista as $m) {
    $filas .= '<tr><td><code>' . h($m['nombre']) . '</code></td><td>'
        . ($m['aplicada'] ? '<span class="ok">Ya aplicada</span>' : '<span class="pend">Pendiente</span>') . '</td></tr>';
}
$cuerpo = '<h1>Migraciones pendientes</h1><p class="s">Base de datos: <code>' . h($db->getDbName()) . '</code> · Usuario: ' . h(SessionData::getUserFullName()) . '</p>';
if (!$lista) {
    $cuerpo .= '<div class="box bad">No se encontraron archivos en <code>admin/db/produccion/</code>.</div>';
}
if ($faltantes) {
    $cuerpo .= '<div class="box bad"><b>Faltan migraciones previas (fases D a H):</b><br>' . h(implode(', ', $faltantes))
        . '<br>Ejecute antes los archivos <code>fase-d-*</code>, <code>fase-e-*</code>, <code>fase-f-*</code> y <code>fase-h-*</code> de <code>admin/db</code>.</div>';
}
$cuerpo .= '<table><tr><th>Migración</th><th>Estado</th></tr>' . $filas . '</table>';
if ($pendientes && !$faltantes) {
    $cuerpo .= '<div class="box warn"><b>Antes de continuar</b>: haga una copia de seguridad completa de la base de datos. Estas migraciones cambian la estructura de tablas y no se pueden deshacer desde aquí.</div>'
        . '<form method="post" onsubmit="return confirm(\'¿Ejecutar ' . count($pendientes) . ' migraciones en esta base de datos?\')">'
        . '<input type="hidden" name="token" value="' . h($token) . '"><input type="hidden" name="accion" value="ejecutar">'
        . '<p><label><input type="checkbox" name="respaldo" value="1" onchange="document.getElementById(\'go\').disabled=!this.checked"> Ya hice la copia de seguridad</label></p>'
        . '<button id="go" type="submit" disabled>Ejecutar ' . count($pendientes) . ' migraciones pendientes</button></form>';
} elseif (!$pendientes && $lista) {
    $cuerpo .= '<div class="box good">La base de datos está al día. No hay migraciones pendientes.</div>'
        . '<form method="post"><input type="hidden" name="token" value="' . h($token) . '"><input type="hidden" name="accion" value="borrar_archivo">'
        . '<button type="submit" class="sec">Eliminar este archivo del servidor</button></form>';
}
pagina('Migraciones de producción', $cuerpo);
