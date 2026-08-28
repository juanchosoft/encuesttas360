<?php

/**
 * Sincroniza tbl_permissions con administrativo/admin/classes/PermissionCatalog.php.
 *
 * A diferencia de fase-d-002-rbac-seed.php (migración de UNA sola vez, sella
 * roles/usuarios), este script es de USO REPETIDO: cada vez que un
 * desarrollador agrega permisos nuevos a `PermissionCatalog::definitions()`
 * (para un módulo nuevo o uno existente), se corre este script para que esos
 * permisos existan en la base de datos. Es 100% seguro de re-ejecutar en
 * cualquier momento: usa `INSERT ... ON DUPLICATE KEY UPDATE` sobre
 * `permission_key` (única), nunca borra filas, y NO toca `tbl_roles` ni
 * `tbl_role_has_permissions` (asignar el permiso nuevo a un rol se hace
 * después, a mano, desde la pantalla Roles y Permisos).
 *
 * Uso: php admin/db/sync_permissions_catalog.php
 *
 * Ver docs/GUIA_NUEVOS_MODULOS_PERMISOS.md para el flujo completo de cómo
 * agregar un módulo nuevo al sistema de permisos.
 */

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';

function out($msg)
{
    echo $msg . (php_sapi_name() === 'cli' ? "\n" : "<br>\n");
}

$db = new DbConection();
$pdo = $db->openConect();

$permTable = $db->getTable('tbl_permissions');
$ins = $pdo->prepare(
    "INSERT INTO $permTable (permission_key, module, action, name, description, legacy_id, dt_create, is_active)
     VALUES (:key, :module, :action, :name, :description, :legacy_id, NOW(), 1)
     ON DUPLICATE KEY UPDATE module = VALUES(module), action = VALUES(action), name = VALUES(name),
         description = VALUES(description), legacy_id = VALUES(legacy_id)"
);

$nuevos = 0;
$actualizados = 0;

$stmtExiste = $pdo->prepare("SELECT id FROM $permTable WHERE permission_key = :key");

foreach (PermissionCatalog::definitions() as $d) {
    $stmtExiste->execute([':key' => $d['key']]);
    $existia = (bool) $stmtExiste->fetch();

    $ins->execute([
        ':key' => $d['key'],
        ':module' => $d['module'],
        ':action' => $d['action'],
        ':name' => $d['name'],
        ':description' => $d['description'],
        ':legacy_id' => $d['legacy_id'],
    ]);

    if ($existia) {
        $actualizados++;
    } else {
        $nuevos++;
        out("  [NUEVO] {$d['key']} ({$d['name']})");
    }
}

out("");
out("=== Sincronización completa: $nuevos permisos nuevos, $actualizados ya existían (actualizados por si cambió su nombre/módulo). ===");
if ($nuevos > 0) {
    out("Recuerda: los permisos nuevos NO están asignados a ningún rol todavía.");
    out("Asígnalos desde la pantalla \"Roles y Permisos\" (roles_permisos.php) del panel.");
}

$db->closeConect();
