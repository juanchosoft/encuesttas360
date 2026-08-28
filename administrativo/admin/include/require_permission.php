<?php

/**
 * Helpers de gate de página, mismo patrón que el proyecto hermano `santander`.
 * Se cargan desde admin/include/generic_classes.php (bootstrap de toda página
 * de /administrativo).
 */

if (!function_exists('requirePermission')) {
    /**
     * Corta la ejecución y muestra permiso_denegado.php si el usuario actual
     * no tiene la clave dada. SuperAdministrador siempre pasa (bypass en
     * SessionData::hasPermission()).
     */
    function requirePermission(string $permissionKey): void
    {
        if (!SessionData::hasPermission($permissionKey)) {
            require 'permiso_denegado.php';
            exit;
        }
    }
}

if (!function_exists('requireAnyPermission')) {
    /**
     * Igual que requirePermission() pero pasa con CUALQUIERA de las claves dadas.
     * @param string[] $permissionKeys
     */
    function requireAnyPermission(array $permissionKeys): void
    {
        if (!SessionData::hasAnyPermission($permissionKeys)) {
            require 'permiso_denegado.php';
            exit;
        }
    }
}
