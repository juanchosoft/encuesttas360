<?php

session_start();
require_once __DIR__ . '/admin/classes/DbConection.php';
require_once __DIR__ . '/admin/classes/SessionData.php';

$cli = PHP_SAPI === 'cli';
$args = [];
if ($cli) {
    foreach (array_slice($argv, 1) as $a) {
        $p = explode('=', ltrim($a, '-'), 2);
        $args[$p[0]] = $p[1] ?? '1';
    }
} else {
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Robots-Tag: noindex');
    if (!SessionData::superAdministrador()) {
        http_response_code(403);
        exit("Acceso restringido: inicie sesión como SuperAdministrador.
");
    }
    $args = $_GET;
}
$aplicar = !empty($args['aplicar']);
$hasta = isset($args['hasta']) ? trim($args['hasta']) : '';
if ($hasta !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', $hasta)) {
    exit("Parámetro 'hasta' inválido (use YYYY-MM-DD HH:MM:SS).\n");
}

$nombre = 'fase-n-001-corregir-hora-bogota';
$db = new DbConection();
$pdo = $db->openConect();
$pdo->exec("SET time_zone = 'SYSTEM'");

$ya = $pdo->prepare("SELECT 1 FROM " . $db->getTable('tbl_migraciones_ejecutadas') . " WHERE nombre_migracion = :n");
$ya->execute([':n' => $nombre]);
if ($ya->fetchColumn()) {
    exit("Ya se ejecutó anteriormente ($nombre). No se hace nada.\n");
}

$ahoraServidor = new DateTime($pdo->query("SELECT NOW()")->fetchColumn(), new DateTimeZone('UTC'));
$ahoraBogota = new DateTime('now', new DateTimeZone('America/Bogota'));
$bogotaComoUtc = new DateTime($ahoraBogota->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
$segundos = (int)(round(($bogotaComoUtc->getTimestamp() - $ahoraServidor->getTimestamp()) / 3600) * 3600);
$horas = $segundos / 3600;

echo "Hora MySQL (servidor): " . $ahoraServidor->format('Y-m-d H:i:s') . "\n";
echo "Hora Bogotá          : " . $ahoraBogota->format('Y-m-d H:i:s') . "\n";
echo "Desfase a aplicar    : " . ($horas >= 0 ? '+' : '') . $horas . " hora(s)\n";
echo "Modo                 : " . ($aplicar ? 'APLICAR' : 'SIMULACIÓN') . "\n";
if ($hasta !== '') {
    echo "Solo registros anteriores a: $hasta\n";
}
echo "\n";

if ($horas == 0) {
    echo "El servidor ya está en hora de Bogotá; no hay nada que corregir.\n";
    if ($aplicar) {
        $pdo->prepare("INSERT INTO " . $db->getTable('tbl_migraciones_ejecutadas') . " (nombre_migracion, dtejecutada) VALUES (:n, NOW())")->execute([':n' => $nombre]);
    }
    exit;
}

$objetivos = [
    'tbl_votantes'                         => ['dtcreate', 'dtupdate'],
    'tbl_cuestionario_intentos'            => ['fecha_respuesta', 'dtcreate'],
    'tbl_cuestionario_respuestas'          => ['dtcreate'],
    'tbl_certificacion_encuestador'        => ['dtcreate', 'dtupdate', 'revision_fecha'],
    'tbl_certificacion_revision_historial' => ['dtcreate'],
];

$tablaCol = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :s AND TABLE_NAME = :t");
$corte = $hasta !== '' ? $hasta : $ahoraServidor->format('Y-m-d H:i:s');
$total = 0;

try {
    if ($aplicar) {
        $pdo->beginTransaction();
    }
    foreach ($objetivos as $tabla => $cols) {
        $tablaCol->execute([':s' => $db->getDbName(), ':t' => $tabla]);
        $existentes = $tablaCol->fetchAll(PDO::FETCH_COLUMN);
        if (!$existentes) {
            echo "$tabla: no existe, se omite.\n";
            continue;
        }
        $cols = array_values(array_intersect($cols, $existentes));
        if (!in_array('dtcreate', $existentes, true)) {
            echo "$tabla: sin dtcreate, se omite.\n";
            continue;
        }
        $t = $db->getTable($tabla);
        $sets = [];
        foreach ($cols as $c) {
            $sets[] = "`$c` = IF(`$c` IS NULL, NULL, DATE_ADD(`$c`, INTERVAL $segundos SECOND))";
        }
        $where = "dtcreate < :corte";
        $n = $pdo->prepare("SELECT COUNT(*) FROM $t WHERE $where");
        $n->execute([':corte' => $corte]);
        $filas = (int)$n->fetchColumn();
        echo str_pad($tabla, 40) . $filas . " fila(s) [" . implode(', ', $cols) . "]\n";
        if ($aplicar && $filas > 0) {
            $u = $pdo->prepare("UPDATE $t SET " . implode(', ', $sets) . " WHERE $where");
            $u->execute([':corte' => $corte]);
        }
        $total += $filas;
    }
    if ($aplicar) {
        $pdo->prepare("INSERT INTO " . $db->getTable('tbl_migraciones_ejecutadas') . " (nombre_migracion, dtejecutada) VALUES (:n, DATE_ADD(NOW(), INTERVAL $segundos SECOND))")->execute([':n' => $nombre]);
        $pdo->commit();
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    exit("ERROR, no se modificó nada: " . $e->getMessage() . "\n");
}

echo "\n" . ($aplicar ? "Corregidas" : "Se corregirían") . " $total fila(s) en total.\n";
if (!$aplicar) {
    echo "Para aplicar: agregue aplicar=1\n";
}
