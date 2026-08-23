<?php

/**
 * Resuelve las claves de permiso (permission_keys) de un usuario a partir de
 * su rol (tbl_usuarios.role_id -> tbl_role_has_permissions -> tbl_permissions).
 *
 * No reemplaza el pivote legacy tbl_usuarios_has_tbl_permisos (que se deja
 * intacto para no romper nada fuera de alcance) — este es el motor NUEVO,
 * usado exclusivamente por el sistema de claves.
 */
class Authorization
{
    /**
     * @param PDO $pdo Conexión ya abierta.
     * @param DbConection $db Para resolver nombres de tabla sin hardcodear la BD.
     * @param int $userId
     * @return string[] lista de permission_key del rol del usuario (vacío si
     *                   no tiene rol o el rol no tiene permisos).
     */
    public static function loadPermissionKeys(PDO $pdo, DbConection $db, int $userId): array
    {
        $q = "SELECT p.permission_key
              FROM " . $db->getTable('tbl_usuarios') . " u
              INNER JOIN " . $db->getTable('tbl_role_has_permissions') . " rhp ON rhp.role_id = u.role_id
              INNER JOIN " . $db->getTable('tbl_permissions') . " p ON p.id = rhp.permission_id AND p.is_active = 1
              WHERE u.id = :id";
        $stmt = $pdo->prepare($q);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /** Clave del rol (role_key) del usuario, o null si no tiene rol asignado. */
    public static function loadRoleKey(PDO $pdo, DbConection $db, int $userId): ?string
    {
        $q = "SELECT r.role_key
              FROM " . $db->getTable('tbl_usuarios') . " u
              INNER JOIN " . $db->getTable('tbl_roles') . " r ON r.id = u.role_id
              WHERE u.id = :id";
        $stmt = $pdo->prepare($q);
        $stmt->execute([':id' => $userId]);
        $val = $stmt->fetchColumn();
        return $val === false ? null : $val;
    }
}
