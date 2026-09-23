<?php

/**
 * Helpers de visibilidad de menú (administrativo/admin/include/navbar.php),
 * dirigidos 100% por permisos (clave de texto), no por flags de rol
 * hardcodeados. SuperAdministrador/Administrador ven todo automáticamente
 * porque SessionData::hasPermission() ya hace ese bypass/ya tiene el
 * catálogo completo.
 */
class NavAuthorization
{
    public static function can(string $permissionKey): bool
    {
        return SessionData::hasPermission($permissionKey);
    }

    /** @param string[] $permissionKeys */
    public static function canAny(array $permissionKeys): bool
    {
        return SessionData::hasAnyPermission($permissionKeys);
    }

    public static function showConfiguracionPolitica(): bool
    {
        return self::canAny(['politica.partidos.view', 'politica.personal_politico.view', 'politica.votantes.view']);
    }

    public static function showConfiguracionEstudios(): bool
    {
        return self::canAny([
            'estudios.espacio_geografico.view', 'estudios.ficha_tecnica.view', 'estudios.sondeos.view',
            'estudios.preguntas_grilla.view', 'estudios.grilla.view', 'estudios.formulas.view',
        ]);
    }

    public static function showConfiguracionEncuestas(): bool
    {
        return self::can('encuestas.preguntas.view');
    }

    public static function showAnalisisElectoral(): bool
    {
        return self::can('analisis.estudio.view');
    }

    public static function showDashboardResultados(): bool
    {
        return self::canAny(['resultados.sondeos.view', 'resultados.cuestionarios.view']);
    }

    public static function showResultadosEncuestas(): bool
    {
        return self::canAny([
            'resultados.sondeos.view',
            'certificaciones.view',
            'certificaciones.dashboard.view',
        ]);
    }

    public static function showCertificacionCalidad(): bool
    {
        return self::can('certificaciones.view');
    }

    public static function showCertificacionDashboard(): bool
    {
        return self::can('certificaciones.dashboard.view');
    }

    public static function showConfiguracionGeneral(): bool
    {
        return self::canAny(['configuracion.general.view', 'configuracion.usuarios.view', 'configuracion.clientes.view']);
    }

    public static function showAsistenteIA(): bool
    {
        return self::can('ia.informes.view');
    }
}
