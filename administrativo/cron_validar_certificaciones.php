<?php

require_once __DIR__ . '/admin/classes/DbConection.php';
require_once __DIR__ . '/admin/classes/Util.php';
require_once __DIR__ . '/admin/classes/CertificacionAutoRevision.php';

$cli = PHP_SAPI === 'cli';
$args = [];
if ($cli) {
    foreach (array_slice($argv ?? [], 1) as $a) {
        $p = explode('=', ltrim($a, '-'), 2);
        $args[$p[0]] = $p[1] ?? '1';
    }
} else {
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Robots-Tag: noindex');
    $ini = is_file(__DIR__ . '/config.ini') ? parse_ini_file(__DIR__ . '/config.ini', true) : [];
    $token = trim((string)($ini['cron']['token'] ?? ''));
    $dado = (string)($_GET['token'] ?? '');
    if ($token === '' || !hash_equals($token, $dado)) {
        http_response_code(403);
        exit("Acceso denegado.\n");
    }
    $args = $_GET;
}

@set_time_limit(280);
@ini_set('memory_limit', '512M');

$lockPath = sys_get_temp_dir() . '/cert_auto_revision_' . md5(__DIR__) . '.lock';
$lock = fopen($lockPath, 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    exit(date('Y-m-d H:i:s') . " Ya hay una pasada en ejecución; se omite.\n");
}

$res = CertificacionAutoRevision::procesar((int)($args['max'] ?? 1));

echo date('Y-m-d H:i:s') . ' Procesadas: ' . count($res['procesadas'])
    . ' | liberadas: ' . $res['liberadas'] . ' | en cola: ' . $res['pendientes'] . "\n";
foreach ($res['procesadas'] as $p) {
    echo '  #' . $p['id'] . ' -> ' . $p['estado']
        . (isset($p['confianza']) ? ' (confianza ' . $p['confianza'] . ')' : '')
        . (!empty($p['requiere_humano']) ? ' [revisión humana]' : '')
        . (isset($p['error']) ? ' | ' . $p['error'] : '') . "\n";
}

flock($lock, LOCK_UN);
fclose($lock);
