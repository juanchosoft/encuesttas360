<?php

/**
 * Fuente única de verdad del catálogo de permisos de /administrativo.
 *
 * Cada permiso se identifica por una CLAVE de texto ("permission_key"),
 * formato "{modulo}.{recurso}.{accion}" — nunca por un ID numérico suelto.
 * `legacy_id`, cuando existe, es el ID numérico equivalente en la tabla
 * legacy `tbl_permisos`; sirve exclusivamente de puente de compatibilidad
 * para que `SessionData::getPermission($id)` (deprecado) siga funcionando
 * en código que aún compare por número. Varias claves nuevas pueden
 * compartir el mismo `legacy_id` cuando el sistema legacy reutilizaba un
 * mismo ID para más de un módulo/página (ej. `usuarios.php` y
 * `configuracion.php` comparten hoy los IDs 1-4).
 *
 * Este archivo es la única fuente para: el seed de `tbl_permissions`
 * (administrativo/admin/db/sync_permissions_catalog.php), el mapa AJAX
 * (administrativo/admin/config/ajax_permissions_map.php) y las claves que
 * usan las páginas/menú. No duplicar esta lista en otro lugar.
 */
class PermissionCatalog
{
    /**
     * Nombre humano de cada módulo (clave técnica "modulo.recurso" ->
     * etiqueta legible), usado como encabezado de grupo en la matriz de
     * permisos de Roles y Permisos. No confundir con `name` de cada permiso
     * individual (ese ya es humano, ej. "Análisis de Estudio - Ver").
     */
    const MODULE_LABELS = [
        'configuracion.usuarios' => 'Usuarios',
        'configuracion.general' => 'Configuración General',
        'configuracion.clientes' => 'Clientes',
        'configuracion.roles' => 'Roles y Permisos',
        'politica.partidos' => 'Partidos Políticos',
        'politica.personal_politico' => 'Personal Político',
        'politica.votantes' => 'Votantes',
        'estudios.espacio_geografico' => 'Espacio Geográfico',
        'estudios.ficha_tecnica' => 'Ficha Técnica de Encuesta',
        'estudios.sondeos' => 'Sondeos',
        'estudios.preguntas_grilla' => 'Preguntas de Grilla',
        'estudios.grilla' => 'Grilla',
        'estudios.formulas' => 'Fórmulas',
        'encuestas.preguntas' => 'Preguntas de Encuestas',
        'analisis.estudio' => 'Análisis de Estudio',
        'resultados.sondeos' => 'Resultados de Sondeos',
        'resultados.cuestionarios' => 'Resultados de Cuestionarios',
        'legacy.secretarias' => 'Secretarías (módulo antiguo)',
        'legacy.mapa' => 'Mapa (módulo antiguo)',
        'legacy.contestar_cuestionario' => 'Contestar Cuestionario (módulo antiguo)',
        'ia.asistente' => 'Asistente IA',
        'ia.web' => 'Asistente IA - Búsqueda Web',
        'ia.consulta_avanzada' => 'Asistente IA - Consulta Avanzada',
        'ia.informes' => 'Informes IA',
        'ia.logs' => 'Asistente IA - Auditoría',
        'ia.voz' => 'Asistente IA - Voz',
    ];

    /** Etiqueta humana de un módulo; si no está mapeado, la "humaniza" a partir de la clave técnica. */
    public static function moduleLabel(string $module): string
    {
        if (isset(self::MODULE_LABELS[$module])) {
            return self::MODULE_LABELS[$module];
        }
        $palabras = preg_split('/[._]+/', $module);
        return implode(' ', array_map('ucfirst', $palabras));
    }

    /**
     * @return array[] cada elemento: key, module, action, name, description, legacy_id
     */
    public static function definitions(): array
    {
        $defs = [];

        // --- Módulos con página propia en el panel ---

        $defs = array_merge($defs, self::crudBlock('configuracion.usuarios', 'Usuarios', [1, 2, 3, 4]));
        $defs = array_merge($defs, self::crudBlock('configuracion.general', 'Configuración General', [1, 2, 3, 4]));
        $defs = array_merge($defs, self::crudBlock('politica.partidos', 'Partidos Políticos', [10, 11, 12, 13]));
        $defs = array_merge($defs, self::crudBlock('estudios.espacio_geografico', 'Espacio Geográfico', [14, 15, 16, 17]));
        $defs = array_merge($defs, self::crudBlock('estudios.ficha_tecnica', 'Ficha Técnica Encuesta', [18, 19, 20, 21]));
        $defs = array_merge($defs, self::crudBlock('politica.personal_politico', 'Personal Político', [22, 23, 24, 25]));
        $defs = array_merge($defs, self::crudBlock('politica.votantes', 'Votantes', [26, 27, 28, 29]));
        $defs = array_merge($defs, self::crudBlock('encuestas.preguntas', 'Preguntas', [30, 31, 32, 33]));
        $defs = array_merge($defs, self::crudBlock('estudios.sondeos', 'Sondeos', [34, 35, 36, 37]));
        $defs = array_merge($defs, self::crudBlock('estudios.preguntas_grilla', 'Preguntas Grilla', [38, 39, 40, 41]));
        $defs = array_merge($defs, self::crudBlock('estudios.grilla', 'Grilla', [42, 43, 44, 45]));
        $defs = array_merge($defs, self::crudBlock('estudios.formulas', 'Fórmulas', [46, 47, 48, 49]));
        $defs = array_merge($defs, self::crudBlock('analisis.estudio', 'Análisis de Estudio', [50, 51, 52, 53]));
        $defs = array_merge($defs, self::crudBlock('resultados.cuestionarios', 'Resultados Cuestionarios', [74, 75, 76, 77]));
        $defs = array_merge($defs, self::crudBlock('configuracion.clientes', 'Clientes', [86, 87, 88, 89]));
        $defs = array_merge($defs, self::crudBlock('resultados.sondeos', 'Resultados Sondeos', [90, 91, 92, 93]));

        // Claves de eliminación dedicadas por módulo (sin equivalente legacy).
        foreach ([
            'politica.votantes' => 'Votantes',
            'estudios.sondeos' => 'Sondeos',
            'estudios.ficha_tecnica' => 'Ficha Técnica Encuesta',
            'estudios.preguntas_grilla' => 'Preguntas Grilla',
            'estudios.grilla' => 'Grilla',
            'estudios.formulas' => 'Fórmulas',
            'encuestas.preguntas' => 'Preguntas',
            'analisis.estudio' => 'Análisis de Estudio',
            'configuracion.clientes' => 'Clientes',
        ] as $prefix => $label) {
            $defs[] = self::def("{$prefix}.delete", $prefix, 'delete', "{$label} - Eliminar", null, null);
        }

        // Módulo de administración de roles.
        $defs[] = self::def('configuracion.roles.view', 'configuracion.roles', 'view', 'Roles y Permisos - Ver', null, null);
        $defs[] = self::def('configuracion.roles.manage', 'configuracion.roles', 'manage', 'Roles y Permisos - Administrar', null, null);

        // --- Módulos legacy sin página propia en este catálogo — se incluyen
        //     para que el puente legacy_id siga resolviendo
        //     SessionData::getPermission($id) en código que compare por número. ---

        $defs = array_merge($defs, self::crudBlock('legacy.secretarias', 'Secretarias', [5, 6, 7, null]));
        $defs[] = self::def('legacy.mapa.view', 'legacy.mapa', 'view', 'Mapa - Ver', null, 8);
        $defs[] = self::def('legacy.mapa.create', 'legacy.mapa', 'create', 'Mapa - Crear', null, 9);
        $defs = array_merge($defs, self::crudBlock('legacy.contestar_cuestionario', 'Contestar Cuestionario', [70, 71, 72, 73]));

        $defs[] = self::def('ia.asistente.use', 'ia.asistente', 'use', 'Asistente IA - Usar', null, null);
        $defs[] = self::def('ia.web.use', 'ia.web', 'use', 'Asistente IA - Búsqueda en Internet', null, null);
        $defs[] = self::def('ia.consulta_avanzada.use', 'ia.consulta_avanzada', 'use', 'Asistente IA - Consulta SQL avanzada', null, null);
        $defs = array_merge($defs, self::crudBlock('ia.informes', 'Informes IA', [null, null, null, null]));
        $defs[] = self::def('ia.informes.delete', 'ia.informes', 'delete', 'Informes IA - Eliminar', null, null);
        $defs[] = self::def('ia.logs.view', 'ia.logs', 'view', 'Asistente IA - Ver Auditoría', null, null);
        $defs[] = self::def('ia.voz.use', 'ia.voz', 'use', 'Asistente IA - Modo de voz (dictado y respuesta hablada)', null, null);

        return $defs;
    }

    /**
     * Bloque de 4 acciones (view/create/update/manage) reutilizando IDs legacy.
     * $legacyIds = [idVer, idCrear, idEditar, idPermisos] (usar null si ese
     * tier no existe en el sistema legacy).
     */
    private static function crudBlock(string $prefix, string $label, array $legacyIds): array
    {
        $acciones = ['view' => 'Ver', 'create' => 'Crear', 'update' => 'Editar', 'manage' => 'Permisos'];
        $i = 0;
        $out = [];
        foreach ($acciones as $accion => $accionLabel) {
            $legacyId = $legacyIds[$i] ?? null;
            $out[] = self::def("{$prefix}.{$accion}", $prefix, $accion, "{$label} - {$accionLabel}", null, $legacyId);
            $i++;
        }
        return $out;
    }

    private static function def(string $key, string $module, string $action, string $name, ?string $description, ?int $legacyId): array
    {
        return [
            'key' => $key,
            'module' => $module,
            'action' => $action,
            'name' => $name,
            'description' => $description,
            'legacy_id' => $legacyId,
        ];
    }

    /** Todas las claves (con alias) que corresponden a un ID legacy dado. */
    public static function keysForLegacyId(int $legacyId): array
    {
        $keys = [];
        foreach (self::definitions() as $d) {
            if ($d['legacy_id'] === $legacyId) {
                $keys[] = $d['key'];
            }
        }
        return $keys;
    }

    /** Todas las claves (con alias) que corresponden a cualquiera de varios IDs legacy. */
    public static function keysForLegacyIds(array $legacyIds): array
    {
        $keys = [];
        foreach ($legacyIds as $id) {
            $keys = array_merge($keys, self::keysForLegacyId((int) $id));
        }
        return array_values(array_unique($keys));
    }

    /**
     * Clave canónica para un ID legacy (usada por el wrapper deprecado
     * SessionData::getPermission($id)). Si varias claves comparten el ID,
     * se devuelve la primera definida (la del módulo "original", no los alias).
     */
    public static function legacyIdToKey(int $legacyId): ?string
    {
        foreach (self::definitions() as $d) {
            if ($d['legacy_id'] === $legacyId) {
                return $d['key'];
            }
        }
        return null;
    }

    /** Definiciones agrupadas por módulo, para la matriz de checkboxes de Roles CRUD. */
    public static function groupedByModule(): array
    {
        $out = [];
        foreach (self::definitions() as $d) {
            $out[$d['module']][] = $d;
        }
        return $out;
    }
}
