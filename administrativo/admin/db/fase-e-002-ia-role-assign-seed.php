<?php

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';

const MIGRACION_NOMBRE = 'fase-e-002-ia-role-assign';

const ROLE_PERMISSION_KEYS = [
    'super_administrador' => [
        'ia.asistente.use', 'ia.web.use', 'ia.consulta_avanzada.use',
        'ia.informes.view', 'ia.informes.create', 'ia.informes.update', 'ia.informes.manage', 'ia.informes.delete',
        'ia.logs.view',
    ],
    'administrador' => [
        'ia.asistente.use', 'ia.web.use', 'ia.consulta_avanzada.use',
        'ia.informes.view', 'ia.informes.create', 'ia.informes.update', 'ia.informes.manage', 'ia.informes.delete',
        'ia.logs.view',
    ],
    'investigador' => [
        'ia.asistente.use', 'ia.web.use',
        'ia.informes.view', 'ia.informes.create', 'ia.informes.update',
    ],
    'visor' => [
        'ia.asistente.use', 'ia.informes.view',
    ],
    'operativo' => [
        'ia.asistente.use',
    ],
    'encuestador' => [
        'ia.asistente.use',
    ],
    'cliente' => [],
];

function out($msg)
{
    echo $msg . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
}

$db = new DbConection();
$pdo = $db->openConect();

$q = "SELECT id FROM " . $db->getTable('tbl_migraciones_ejecutadas') . " WHERE nombre_migracion = " . $pdo->quote(MIGRACION_NOMBRE);
$already = $pdo->query($q)->fetch();
if ($already) {
    out('[OK] La migración "' . MIGRACION_NOMBRE . '" ya se había ejecutado (id=' . $already['id'] . '). No se hace nada.');
    $db->closeConect();
    exit(0);
}

$roleTable = $db->getTable('tbl_roles');
$permTable = $db->getTable('tbl_permissions');
$rhpTable = $db->getTable('tbl_role_has_permissions');

$roleIdByKey = [];
foreach ($pdo->query("SELECT id, role_key FROM $roleTable") as $row) {
    $roleIdByKey[$row['role_key']] = (int) $row['id'];
}

$permIdByKey = [];
foreach ($pdo->query("SELECT id, permission_key FROM $permTable") as $row) {
    $permIdByKey[$row['permission_key']] = (int) $row['id'];
}

$pdo->beginTransaction();

try {
    $insRhp = $pdo->prepare("INSERT IGNORE INTO $rhpTable (role_id, permission_id, dt_create) VALUES (:role_id, :permission_id, NOW())");
    $total = 0;

    foreach (ROLE_PERMISSION_KEYS as $roleKey => $permissionKeys) {
        if (!isset($roleIdByKey[$roleKey])) {
            throw new Exception("Rol no encontrado: $roleKey");
        }
        $roleId = $roleIdByKey[$roleKey];

        foreach ($permissionKeys as $permKey) {
            if (!isset($permIdByKey[$permKey])) {
                throw new Exception("Permiso no encontrado: $permKey");
            }
            $insRhp->execute([':role_id' => $roleId, ':permission_id' => $permIdByKey[$permKey]]);
            $total++;
        }
        out("  $roleKey: " . count($permissionKeys) . ' permisos asignados.');
    }

    $migTable = $db->getTable('tbl_migraciones_ejecutadas');
    $pdo->prepare("INSERT INTO $migTable (nombre_migracion, dtejecutada) VALUES (:n, NOW())")
        ->execute([':n' => MIGRACION_NOMBRE]);

    $pdo->commit();
    out("Total asignaciones: $total.");
    out('Migración completada y registrada correctamente.');
} catch (Exception $e) {
    $pdo->rollBack();
    out('[ERROR] Migración revertida: ' . $e->getMessage());
    $db->closeConect();
    exit(1);
}

$db->closeConect();
