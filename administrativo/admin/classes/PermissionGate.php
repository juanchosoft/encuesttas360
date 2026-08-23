<?php

/**
 * Autorización centralizada de operaciones AJAX (administrativo/admin/ajax/rqst.php).
 * Se llama UNA SOLA VEZ, antes del switch($op), con el $op recibido.
 *
 * Solo las operaciones registradas en admin/config/ajax_permissions_map.php
 * quedan con autorización real (fail-closed: sin la clave requerida, se
 * deniega). Cualquier `op` que no esté en el mapa se PERMITE sin bloquear —
 * toda operación nueva debe agregarse al mapa explícitamente.
 */
class PermissionGate
{
    /**
     * @return true si autoriza. Si no autoriza, imprime un JSON de error y
     *         termina la ejecución (die) — el caller no necesita comprobar
     *         el valor de retorno, pero se devuelve por claridad/pruebas.
     */
    public static function authorizeOperation(string $op): bool
    {
        $map = require __DIR__ . '/../config/ajax_permissions_map.php';

        if (!array_key_exists($op, $map)) {
            // Operación no registrada en el mapa: se permite (ver docblock de la clase).
            return true;
        }

        if (!isset($_SESSION['session_user'])) {
            self::deny('Sesión no iniciada.');
        }

        $required = $map[$op];
        $requiredKeys = is_array($required) ? $required : [$required];

        if (!SessionData::hasAnyPermission($requiredKeys)) {
            self::deny('No tiene permiso para realizar esta operación.');
        }

        return true;
    }

    private static function deny(string $message): void
    {
        header('Content-Type: application/json');
        echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado', 'message' => $message]]);
        exit;
    }
}
