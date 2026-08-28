<?php

require_once __DIR__ . '/PermissionCatalog.php';

/**
 * CRUD de roles del RBAC por clave. Mismo patrón que el resto de clases de
 * administrativo/admin/classes (DbConection::getTable(), sin hardcodear el
 * nombre de la base de datos).
 */
class Role
{
    public static function getAll($rqst = null)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT r.*,
                (SELECT COUNT(*) FROM " . $db->getTable('tbl_role_has_permissions') . " WHERE role_id = r.id) AS total_permisos,
                (SELECT COUNT(*) FROM " . $db->getTable('tbl_usuarios') . " WHERE role_id = r.id) AS total_usuarios
              FROM " . $db->getTable('tbl_roles') . " r
              ORDER BY r.is_system DESC, r.name ASC";
        $result = $pdo->query($q);
        $arr = $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => $arr]];
    }

    public static function getById($rqst)
    {
        $id = isset($rqst['id']) ? (int) $rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_roles') . " WHERE id = :id";
        $stmt = $pdo->prepare($q);
        $stmt->execute([':id' => $id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            $db->closeConect();
            return Util::error_no_result();
        }

        $q2 = "SELECT permission_id FROM " . $db->getTable('tbl_role_has_permissions') . " WHERE role_id = :id";
        $stmt2 = $pdo->prepare($q2);
        $stmt2->execute([':id' => $id]);
        $role['permission_ids'] = array_map('intval', $stmt2->fetchAll(PDO::FETCH_COLUMN));

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => $role]];
    }

    /** Catálogo completo de permisos agrupado por módulo, para la matriz de checkboxes. */
    public static function getPermissionsCatalog($rqst = null)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT id, permission_key, module, action, name, description
              FROM " . $db->getTable('tbl_permissions') . "
              WHERE is_active = 1
              ORDER BY module ASC, action ASC";
        $result = $pdo->query($q);
        $rows = $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

        $grouped = [];
        foreach ($rows as $r) {
            $r['module_label'] = PermissionCatalog::moduleLabel($r['module']);
            $grouped[$r['module']][] = $r;
        }

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => $grouped]];
    }

    /**
     * Crea o actualiza un rol y su matriz de permisos (transaccional).
     * $rqst: id (0 = nuevo), name, role_key, description, chk (ids de permiso
     * separados por "-", igual convención que Permiso::savePermisos()).
     * Roles de sistema (is_system=1): no se permite cambiar su role_key,
     * pero sí sus permisos.
     */
    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? (int) $rqst['id'] : 0;
        $name = trim($rqst['name'] ?? '');
        $description = trim($rqst['description'] ?? '');
        $chk = $rqst['chk'] ?? '';

        if ($name === '') {
            return Util::error_missing_data();
        }

        $permissionIds = array_filter(array_map('intval', explode('-', (string) $chk)), function ($v) {
            return $v > 0;
        });

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $pdo->beginTransaction();

            if ($id > 0) {
                $stmt = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_roles') . " WHERE id = :id");
                $stmt->execute([':id' => $id]);
                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $pdo->rollBack();
                    $db->closeConect();
                    return Util::error_no_result();
                }

                // role_key ya NO es editable por el usuario (es técnico, se
                // genera automáticamente solo al crear el rol) — al editar,
                // se actualiza nombre/descripción y se deja el role_key tal
                // cual quedó desde su creación, sin importar si es de sistema
                // o personalizado.
                $upd = $pdo->prepare(
                    "UPDATE " . $db->getTable('tbl_roles') . "
                     SET name = :name, description = :description, dt_update = NOW()
                     WHERE id = :id"
                );
                $upd->execute([':name' => $name, ':description' => $description, ':id' => $id]);
                $roleId = $id;
            } else {
                // role_key automático: nombre "humanizado" (minúsculas, sin
                // acentos, espacios -> "_") + "_" + el id autoincremental del
                // rol, para que quede único sin que la persona que lo crea
                // tenga que pensar en una clave técnica.
                $slugBase = self::slugify($name);
                $placeholder = 'tmp_' . uniqid();

                $ins = $pdo->prepare(
                    "INSERT INTO " . $db->getTable('tbl_roles') . " (role_key, name, description, is_system, dt_create)
                     VALUES (:role_key, :name, :description, 0, NOW())"
                );
                $ins->execute([':role_key' => $placeholder, ':name' => $name, ':description' => $description]);
                $roleId = (int) $pdo->lastInsertId();

                $finalRoleKey = ($slugBase !== '' ? $slugBase : 'rol') . '_' . $roleId;
                $pdo->prepare("UPDATE " . $db->getTable('tbl_roles') . " SET role_key = :role_key WHERE id = :id")
                    ->execute([':role_key' => $finalRoleKey, ':id' => $roleId]);
            }

            $del = $pdo->prepare("DELETE FROM " . $db->getTable('tbl_role_has_permissions') . " WHERE role_id = :id");
            $del->execute([':id' => $roleId]);

            $insPerm = $pdo->prepare(
                "INSERT INTO " . $db->getTable('tbl_role_has_permissions') . " (role_id, permission_id, dt_create)
                 VALUES (:role_id, :permission_id, NOW())"
            );
            foreach ($permissionIds as $permId) {
                $insPerm->execute([':role_id' => $roleId, ':permission_id' => $permId]);
            }

            $pdo->commit();
            $db->closeConect();
            return ['output' => ['valid' => true, 'response' => $roleId]];
        } catch (Exception $e) {
            $pdo->rollBack();
            $db->closeConect();
            return Util::error_general('Guardando el rol: ' . $e->getMessage());
        }
    }

    /**
     * "coordinador de campo" / "Coordinador De Campo" -> "coordinador_de_campo".
     * Quita acentos, pasa a minúsculas, y reemplaza cualquier carácter que no
     * sea letra/número por "_" (colapsando repetidos y recortando extremos).
     */
    private static function slugify(string $texto): string
    {
        $texto = trim($texto);
        if (function_exists('transliterator_transliterate')) {
            $texto = transliterator_transliterate('Any-Latin; Latin-ASCII;', $texto);
        } else {
            $texto = strtr($texto, [
                'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u',
                'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü' => 'U',
            ]);
        }
        $texto = strtolower($texto);
        $texto = preg_replace('/[^a-z0-9]+/', '_', $texto);
        return trim($texto, '_');
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? (int) $rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $stmt = $pdo->prepare("SELECT is_system FROM " . $db->getTable('tbl_roles') . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            $db->closeConect();
            return Util::error_no_result();
        }
        if ((int) $role['is_system'] === 1) {
            $db->closeConect();
            return Util::error_general('No se puede eliminar un rol de sistema.');
        }

        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM " . $db->getTable('tbl_usuarios') . " WHERE role_id = :id");
        $stmt2->execute([':id' => $id]);
        if ((int) $stmt2->fetchColumn() > 0) {
            $db->closeConect();
            return Util::error_general('No se puede eliminar: hay usuarios asignados a este rol.');
        }

        $pdo->prepare("DELETE FROM " . $db->getTable('tbl_role_has_permissions') . " WHERE role_id = :id")->execute([':id' => $id]);
        $pdo->prepare("DELETE FROM " . $db->getTable('tbl_roles') . " WHERE id = :id")->execute([':id' => $id]);

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => $id]];
    }

    /**
     * role_key -> valor legacy de tbl_usuarios.tipo, para los 7 roles de
     * sistema (mantiene funcionando SessionData::administrador(), etc., que
     * siguen comparando por ese string). Roles personalizados (no listados
     * aquí) usan su propio "name" como tipo, sin equivalente legacy.
     */
    const ROLE_KEY_TO_LEGACY_TIPO = [
        'super_administrador' => 'SuperAdministrador',
        'administrador' => 'Administrador',
        'investigador' => 'Investigador',
        'visor' => 'Visor',
        'operativo' => 'Operativo',
        'encuestador' => 'Encuestador',
        'cliente' => 'Cliente',
    ];

    public static function legacyTipoForRoleKey(string $roleKey, string $fallbackName): string
    {
        return self::ROLE_KEY_TO_LEGACY_TIPO[$roleKey] ?? $fallbackName;
    }

    /** HTML de <option> para el <select> de rol en usuarios.php (reemplaza el listado fijo de 6 tipos). */
    public static function buildUsuarioRoleOptionsHtml(?string $selectedRoleKey = null): string
    {
        $res = self::getAll();
        $roles = $res['output']['response'] ?? [];
        $html = '<option value="">-- Seleccione rol --</option>';
        foreach ($roles as $r) {
            $sel = ($selectedRoleKey !== null && $selectedRoleKey === $r['role_key']) ? 'selected' : '';
            $html .= '<option ' . $sel . ' value="' . htmlspecialchars($r['role_key'], ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
        return $html;
    }
}
