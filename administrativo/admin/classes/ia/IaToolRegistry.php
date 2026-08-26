<?php

require_once __DIR__ . '/../DbConection.php';
require_once __DIR__ . '/../Util.php';
require_once __DIR__ . '/../Sondeo.php';
require_once __DIR__ . '/../RespuestaSondeo.php';
require_once __DIR__ . '/../FichaTecnicaEncuesta.php';
require_once __DIR__ . '/../AnalisisEstudio.php';
require_once __DIR__ . '/../Dashboard.php';
require_once __DIR__ . '/../RespuestaCuestionario.php';
require_once __DIR__ . '/../PartidoPolitico.php';
require_once __DIR__ . '/../Participantes.php';
require_once __DIR__ . '/../EspacioGeografico.php';
require_once __DIR__ . '/../InformeIA.php';
require_once __DIR__ . '/IaDbConsulta.php';

class IaToolRegistry
{
    public const DESCRIPCION_GENERAR_INFORME = <<<TXT
Genera un informe HTML guardado en el módulo Informes IA (con descarga a PDF con membrete institucional). Debe leer como un informe de un analista experto en encuestas y estadística electoral: bien estructurado, con hallazgos concretos e interpretación, nunca una simple lista de cifras. Antes de llamar a esta tool, ya debiste haber consultado con otras tools todos los datos reales que vas a citar — nunca inventes una cifra aquí.

Etiquetas permitidas: h1 h2 h3 h4 p div span table thead tbody tr th td ul ol li b strong i em small blockquote br hr. Cualquier otra etiqueta (incluida img, a, script, style, caption, tfoot) se elimina automáticamente — para titular una tabla usa un <h4> o un <p><b>texto</b></p> justo antes del <table>, nunca dentro de él; para una fila de "Total", agrégala como una <tr> más dentro de <tbody> con las celdas en <b>negrita</b>, nunca uses <tfoot>. Mantén cada celda de tabla corta (una cifra, un nombre, un porcentaje): si necesitas enumerar muchos elementos (por ejemplo una lista larga de candidatos), hazlo en un <ul> aparte, nunca amontonado dentro de una sola celda — una celda muy larga rompe el ancho de columnas del PDF. Todas las filas de un <table> deben tener exactamente el mismo número de <td>/<th> que el encabezado — nunca omitas ni agregues columnas de más en una fila. El único atributo permitido es class, y solo con estos valores exactos (cualquier otro se elimina):

- s360-kpis (en un <table>) / s360-kpi (en cada <td>) / s360-kpi-value / s360-kpi-label — para mostrar 2 a 4 cifras clave en tarjetas, así:
  <table class="s360-kpis"><tr>
    <td class="s360-kpi"><div class="s360-kpi-value">66.7%</div><div class="s360-kpi-label">Intención de voto</div></td>
    <td class="s360-kpi"><div class="s360-kpi-value">6</div><div class="s360-kpi-label">Respuestas registradas</div></td>
  </tr></table>
- s360-callout + uno de: s360-callout-info / s360-callout-warning / s360-callout-danger / s360-callout-success — para un párrafo destacado (advertencia metodológica, hallazgo importante, dato positivo). Ejemplo: <div class="s360-callout s360-callout-warning">Con solo 6 respuestas, ningún porcentaje es estadísticamente representativo.</div>
- s360-badge-dato / s360-badge-interpretacion (en un <span>) — etiqueta corta antes de una frase, para distinguir explícitamente un dato verificado de tu propia lectura analítica. Ejemplo: <span class="s360-badge-dato">DATO</span> El 66.7% de las respuestas favorecen a X. <span class="s360-badge-interpretacion">INTERPRETACIÓN</span> Esto no es representativo dado el tamaño de muestra.
- s360-quote (en un <div> o <blockquote>) — para resaltar la conclusión o recomendación más importante del informe, una sola vez, cerca del final.
- s360-table-highlight (en un <table>) — para la tabla de resultados más importante del informe, cuando quieras que destaque visualmente sobre las demás.

Estructura recomendada de un informe completo: título (usar el campo titulo, no repetirlo como h1 salvo que quieras un subtítulo), un párrafo de resumen ejecutivo, una fila de s360-kpis con las cifras más importantes, secciones con h2/h3 (hallazgos, metodología si aplica, desglose demográfico), al menos una tabla de datos real, callouts para advertencias metodológicas (muestra pequeña, sesgo geográfico, margen de error alto, ficha técnica faltante — así como ya haces en el chat), y una s360-quote final con la recomendación u observación principal. Sé generoso en detalle y extensión si los datos lo ameritan — no comprimas un hallazgo rico en una sola frase por ahorrar espacio.
TXT;

    private const HERRAMIENTAS = [
        'consultar_sondeos' => [
            'permiso' => 'estudios.sondeos.view',
            'description' => 'Lista los sondeos definidos en el sistema (nombre, tipo, fechas, si está habilitado). Si se da un id, devuelve el detalle de ese sondeo con sus candidatos vinculados.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un sondeo específico (opcional)']],
            ],
            'handler' => 'handleConsultarSondeos',
        ],
        'consultar_resultados_sondeo' => [
            'permiso' => 'resultados.sondeos.view',
            'description' => 'Devuelve las estadísticas completas de un sondeo: totales por opción/candidato con porcentajes, y desglose por ideología, género, edad, nivel de ingresos, nivel educativo, departamento y municipio de los votantes que respondieron.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['tbl_sondeo_id' => ['type' => 'integer', 'description' => 'id del sondeo (obligatorio, ver consultar_sondeos)']],
                'required' => ['tbl_sondeo_id'],
            ],
            'handler' => 'handleConsultarResultadosSondeo',
        ],
        'consultar_ficha_tecnica' => [
            'permiso' => 'estudios.ficha_tecnica.view',
            'description' => 'Lista las fichas técnicas de encuesta: margen de error, nivel de confiabilidad, tamaño de muestra, método de recolección, propósito del estudio, universo representado, estadísticos responsables y fuente de financiación. Usar siempre antes de opinar sobre la significancia estadística de un resultado.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'id de una ficha técnica específica (opcional)']],
            ],
            'handler' => 'handleConsultarFichaTecnica',
        ],
        'consultar_analisis_estudio' => [
            'permiso' => 'analisis.estudio.view',
            'description' => 'Lista los análisis de estudio existentes (candidato, grilla, fórmula usada, resultado calculado y observaciones del investigador).',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un análisis específico (opcional)']],
            ],
            'handler' => 'handleConsultarAnalisisEstudio',
        ],
        'consultar_dashboard_resumen' => [
            'permiso' => 'resultados.sondeos.view',
            'permiso_alt' => 'resultados.cuestionarios.view',
            'description' => 'Devuelve los KPIs generales del sistema (total de votantes, grillas, análisis, candidatos, sondeos, fórmulas), el desglose de votantes por ideología/género/edad/ingresos, los análisis por mes, el top de candidatos por número de análisis, y el estado de las grillas (habilitadas/deshabilitadas).',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['grilla_id' => ['type' => 'integer', 'description' => 'filtrar por una grilla específica (opcional)']],
            ],
            'handler' => 'handleConsultarDashboardResumen',
        ],
        'consultar_cuestionarios' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Devuelve las estadísticas de respuestas de un cuestionario (ficha técnica): total de intentos, respuestas por pregunta, filtros disponibles y KPIs. Es el conjunto de datos con más respuestas reales del sistema.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica del cuestionario (obligatorio)']],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleConsultarCuestionarios',
        ],
        'consultar_partidos_politicos' => [
            'permiso' => 'politica.partidos.view',
            'description' => 'Lista los partidos políticos registrados.',
            'input_schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un partido específico (opcional)']]],
            'handler' => 'handleConsultarPartidosPoliticos',
        ],
        'consultar_personal_politico' => [
            'permiso' => 'politica.personal_politico.view',
            'description' => 'Lista los candidatos/personal político registrado, con su partido, cargo público y ubicación geográfica.',
            'input_schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un candidato específico (opcional)']]],
            'handler' => 'handleConsultarPersonalPolitico',
        ],
        'consultar_espacio_geografico' => [
            'permiso' => 'estudios.espacio_geografico.view',
            'description' => 'Lista los espacios geográficos configurados en el sistema.',
            'input_schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un espacio específico (opcional)']]],
            'handler' => 'handleConsultarEspacioGeografico',
        ],
        'consultar_votantes' => [
            'permiso' => 'politica.votantes.view',
            'description' => 'Devuelve conteos agregados de votantes activos por dimensión demográfica (ideología, rango de edad, nivel de ingresos, género, nivel educativo). Nunca devuelve datos de identificación individual (nombre, cédula, teléfono, dirección) — solo totales agrupados.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['dimension' => [
                    'type' => 'string',
                    'enum' => ['ideologia', 'rango_edad', 'nivel_ingresos', 'genero', 'nivel_educacion', 'todas'],
                    'description' => 'dimensión a agrupar; "todas" devuelve el conteo por las 5 dimensiones a la vez (por defecto)',
                ]],
            ],
            'handler' => 'handleConsultarVotantes',
        ],
        'generar_informe_html' => [
            'permiso' => 'ia.informes.create',
            'description' => self::DESCRIPCION_GENERAR_INFORME,
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'titulo' => ['type' => 'string', 'description' => 'título del informe (máx. 150 caracteres)'],
                    'contenido_html' => ['type' => 'string', 'description' => 'cuerpo del informe en HTML restringido a la whitelist de etiquetas indicada'],
                ],
                'required' => ['titulo', 'contenido_html'],
            ],
            'handler' => 'handleGenerarInformeHtml',
        ],
        'consultar_base_de_datos' => [
            'permiso' => 'ia.consulta_avanzada.use',
            'description' => IaDbConsulta::DESCRIPCION_TOOL,
            'input_schema' => [
                'type' => 'object',
                'properties' => ['sql' => ['type' => 'string', 'description' => 'una única sentencia SELECT sobre las tablas de dominio permitidas (ver descripción de la tool). Se le forzará un LIMIT 250 automáticamente.']],
                'required' => ['sql'],
            ],
            'handler' => 'handleConsultarBaseDeDatos',
        ],
    ];

    public static function definiciones(array $permisos, bool $superAdmin): array
    {
        $tools = [];
        foreach (self::HERRAMIENTAS as $nombre => $def) {
            $tienePermiso = $superAdmin
                || in_array($def['permiso'], $permisos, true)
                || (isset($def['permiso_alt']) && in_array($def['permiso_alt'], $permisos, true));
            if ($tienePermiso) {
                $tools[] = [
                    'name' => $nombre,
                    'description' => $def['description'],
                    'input_schema' => $def['input_schema'],
                ];
            }
        }
        return $tools;
    }

    public static function ejecutar(string $nombre, array $input, int $userId, array $permisos, bool $superAdmin): array
    {
        $inicio = microtime(true);
        $def = self::HERRAMIENTAS[$nombre] ?? null;

        if ($def === null) {
            return ['error' => 'herramienta_desconocida', 'mensaje' => "La herramienta '$nombre' no existe."];
        }

        $tienePermiso = $superAdmin
            || in_array($def['permiso'], $permisos, true)
            || (isset($def['permiso_alt']) && in_array($def['permiso_alt'], $permisos, true));

        if (!$tienePermiso) {
            self::registrarLog($userId, $nombre, $input, null, 0, false, 'sin_permiso');
            return ['error' => 'sin_permiso', 'mensaje' => "El usuario no tiene el permiso requerido ({$def['permiso']}) para usar esta herramienta."];
        }

        try {
            $resultado = self::{$def['handler']}($input, $userId);
            $duracionMs = (int) round((microtime(true) - $inicio) * 1000);
            $filas = is_array($resultado) && array_is_list($resultado) ? count($resultado) : null;
            self::registrarLog($userId, $nombre, $input, $filas, $duracionMs, true, null);
            return $resultado;
        } catch (\Throwable $e) {
            $duracionMs = (int) round((microtime(true) - $inicio) * 1000);
            self::registrarLog($userId, $nombre, $input, null, $duracionMs, false, $e->getMessage());
            return ['error' => 'error_interno', 'mensaje' => 'No fue posible ejecutar la consulta.'];
        }
    }

    private static function registrarLog(int $userId, string $tool, array $input, ?int $filas, int $duracionMs, bool $exito, ?string $error): void
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "INSERT INTO " . $db->getTable('tbl_ia_tool_logs')
            . " (tbl_usuario_id, tool_nombre, tool_input, filas_devueltas, duracion_ms, exito, error, dt_create)
                VALUES (:usuario, :tool, :input, :filas, :duracion, :exito, :error, NOW())";
        $stmt = $pdo->prepare($q);
        $stmt->execute([
            ':usuario' => $userId,
            ':tool' => $tool,
            ':input' => json_encode($input),
            ':filas' => $filas,
            ':duracion' => $duracionMs,
            ':exito' => $exito ? 1 : 0,
            ':error' => $error,
        ]);
        $db->closeConect();
    }

    private static function handleConsultarSondeos(array $input): array
    {
        $res = Sondeo::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarResultadosSondeo(array $input): array
    {
        $id = (int) ($input['tbl_sondeo_id'] ?? 0);
        if ($id <= 0) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'tbl_sondeo_id es obligatorio.'];
        }
        $res = RespuestaSondeo::getEstadisticasCompletas(['tbl_sondeo_id' => $id]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarFichaTecnica(array $input): array
    {
        $res = FichaTecnicaEncuesta::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarAnalisisEstudio(array $input): array
    {
        $res = AnalisisEstudio::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarDashboardResumen(array $input): array
    {
        $rqst = ['grilla_id' => (int) ($input['grilla_id'] ?? 0)];
        return [
            'principales' => Dashboard::getEstadisticasPrincipales($rqst)['output']['response'] ?? null,
            'ideologia' => Dashboard::getVotantesPorIdeologia($rqst)['output']['response'] ?? null,
            'genero' => Dashboard::getVotantesPorGenero($rqst)['output']['response'] ?? null,
            'edad' => Dashboard::getVotantesPorEdad($rqst)['output']['response'] ?? null,
            'ingresos' => Dashboard::getVotantesPorIngresos($rqst)['output']['response'] ?? null,
            'analisis_por_mes' => Dashboard::getAnalisisPorMes($rqst)['output']['response'] ?? null,
            'top_candidatos' => Dashboard::getTopCandidatos($rqst)['output']['response'] ?? null,
            'grillas_por_estado' => Dashboard::getGrillasPorEstado($rqst)['output']['response'] ?? null,
        ];
    }

    private static function handleConsultarCuestionarios(array $input): array
    {
        $id = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($id <= 0) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'ficha_tecnica_id es obligatorio.'];
        }
        $res = RespuestaCuestionario::getEstadisticas(['ficha_tecnica_id' => $id]);
        return $res['output']['response'] ?? $res;
    }

    private static function handleConsultarPartidosPoliticos(array $input): array
    {
        $res = PartidoPolitico::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarPersonalPolitico(array $input): array
    {
        $res = Participantes::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarEspacioGeografico(array $input): array
    {
        $res = EspacioGeografico::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleGenerarInformeHtml(array $input, int $userId): array
    {
        $titulo = trim((string) ($input['titulo'] ?? ''));
        $contenido = (string) ($input['contenido_html'] ?? '');

        if ($titulo === '' || trim($contenido) === '') {
            return ['error' => 'parametro_faltante', 'mensaje' => 'titulo y contenido_html son obligatorios.'];
        }

        $id = InformeIA::crear($userId, $titulo, $contenido);

        return [
            'id' => $id,
            'titulo' => $titulo,
            'mensaje' => 'El informe se guardó correctamente en el módulo Informes IA (id ' . $id . '). El usuario puede verlo y descargarlo en PDF desde ahí.',
        ];
    }

    private static function handleConsultarBaseDeDatos(array $input): array
    {
        $sql = (string) ($input['sql'] ?? '');
        return IaDbConsulta::ejecutar($sql);
    }

    private const DIMENSIONES_VOTANTES = ['ideologia', 'rango_edad', 'nivel_ingresos', 'genero', 'nivel_educacion'];

    private static function handleConsultarVotantes(array $input): array
    {
        $dimension = (string) ($input['dimension'] ?? 'todas');
        $dimensiones = $dimension === 'todas' || !in_array($dimension, self::DIMENSIONES_VOTANTES, true)
            ? self::DIMENSIONES_VOTANTES
            : [$dimension];

        $db = new DbConection();
        $pdo = $db->openConect();
        $resultado = [];
        foreach ($dimensiones as $col) {
            $q = "SELECT {$col} AS valor, COUNT(*) AS total FROM " . $db->getTable('tbl_votantes') . "
                  WHERE estado = 'activo' GROUP BY {$col} ORDER BY total DESC";
            $stmt = $pdo->query($q);
            $resultado[$col] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $db->closeConect();

        return $resultado;
    }
}
