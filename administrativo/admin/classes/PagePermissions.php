<?php

/**
 * Deriva automáticamente el prefijo de clave de permiso ("modulo.recurso")
 * a partir del NOMBRE DE ARCHIVO de la página actual, para que las páginas
 * de /administrativo no tengan que escribir su propia clave a mano — un solo
 * mapa central.
 *
 * Agregar un módulo nuevo = una línea en PAGE_CRUD_PREFIX, no tocar la página.
 */
class PagePermissions
{
    /** Nombre de archivo (sin ruta) => prefijo "modulo.recurso" en PermissionCatalog. */
    const PAGE_CRUD_PREFIX = [
        'usuarios.php' => 'configuracion.usuarios',
        'configuracion.php' => 'configuracion.general',
        'clientes.php' => 'configuracion.clientes',
        'partidos_politicos.php' => 'politica.partidos',
        'participantes.php' => 'politica.personal_politico',
        'votantes.php' => 'politica.votantes',
        'votantes_encuestador.php' => 'politica.votantes',
        'espacio_geografico.php' => 'estudios.espacio_geografico',
        'ficha_tecnica_encuesta.php' => 'estudios.ficha_tecnica',
        'sondeos.php' => 'estudios.sondeos',
        'preguntas_grilla.php' => 'estudios.preguntas_grilla',
        'grilla.php' => 'estudios.grilla',
        'formulas.php' => 'estudios.formulas',
        'preguntas.php' => 'encuestas.preguntas',
        'analisis_estudio.php' => 'analisis.estudio',
        'resultados_sondeos.php' => 'resultados.sondeos',
        'roles_permisos.php' => 'configuracion.roles',
    ];

    /** Páginas cuyo acceso depende de cualquiera de varias claves (OR), no de un solo módulo CRUD. */
    const PAGE_VIEW_ANY = [
        'dashboard.php' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
        'dashboard_resultados.php' => ['resultados.sondeos.view', 'resultados.cuestionarios.view'],
        'certificaciones.php' => ['resultados.sondeos.view'],
    ];

    private static function currentFile(): string
    {
        return basename($_SERVER['SCRIPT_FILENAME'] ?? $_SERVER['PHP_SELF'] ?? '');
    }

    /**
     * @return array{view:bool,create:bool,update:bool,delete:bool,manage:bool,prefix:?string}
     */
    public static function crudForCurrentPage(): array
    {
        $file = self::currentFile();
        $prefix = self::PAGE_CRUD_PREFIX[$file] ?? null;

        if ($prefix === null) {
            return ['view' => false, 'create' => false, 'update' => false, 'delete' => false, 'manage' => false, 'prefix' => null];
        }

        return [
            'view' => SessionData::hasPermission("$prefix.view"),
            'create' => SessionData::hasPermission("$prefix.create"),
            'update' => SessionData::hasPermission("$prefix.update"),
            'delete' => SessionData::hasPermission("$prefix.delete"),
            'manage' => SessionData::hasPermission("$prefix.manage"),
            'prefix' => $prefix,
        ];
    }

    /** Clave ".view" del módulo de la página actual (o null si no está mapeada). */
    public static function viewKeyForCurrentPage(): ?string
    {
        $file = self::currentFile();
        return isset(self::PAGE_CRUD_PREFIX[$file]) ? self::PAGE_CRUD_PREFIX[$file] . '.view' : null;
    }

    /** Claves ".view" aceptables (OR) para páginas con más de una clave posible. */
    public static function viewKeysForCurrentPage(): array
    {
        $file = self::currentFile();
        if (isset(self::PAGE_VIEW_ANY[$file])) {
            return self::PAGE_VIEW_ANY[$file];
        }
        $single = self::viewKeyForCurrentPage();
        return $single ? [$single] : [];
    }
}
