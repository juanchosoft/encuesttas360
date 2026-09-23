<?php

require_once __DIR__ . '/../DbConection.php';

class IaDbConsulta
{
    private const LIMITE_FILAS = 250;

    private const TABLAS_PERMITIDAS = [
        'tbl_sondeo', 'tbl_respuestas_sondeos', 'tbl_sondeo_x_opciones', 'tbl_sondeo_x_tbl_participantes',
        'tbl_ficha_tecnica_encuestas', 'tbl_preguntas', 'tbl_opciones_respuesta',
        'tbl_analisis_estudio', 'tbl_analisis_calculos',
        'tbl_grilla', 'tbl_grilla_x_preguntas', 'tbl_preguntas_sub_preguntas_grilla',
        'tbl_grilla_sesion_votacion', 'tbl_grilla_respuestas',
        'tbl_participantes', 'tbl_partidos_politicos', 'tbl_votantes', 'tbl_espacio_geografico',
        'tbl_certificacion_encuestador', 'tbl_certificacion_revision_historial', 'tbl_cuestionario_intentos', 'tbl_cuestionario_respuestas',
        'tbl_formulas', 'tbl_clientes',
    ];

    private const PALABRAS_PROHIBIDAS = [
        'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE', 'GRANT', 'REVOKE',
        'CREATE', 'REPLACE', 'CALL', 'EXEC', 'EXECUTE', 'INTO', 'LOAD_FILE', 'OUTFILE',
        'INFORMATION_SCHEMA', 'PERFORMANCE_SCHEMA', 'MYSQL', 'SLEEP', 'BENCHMARK',
    ];

    public const DESCRIPCION_TOOL = <<<TXT
Ejecuta una única sentencia SELECT de solo lectura contra las tablas del dominio de encuestas y estadística, cuando ninguna otra herramienta estructurada resuelve la pregunta. Solo se permite SELECT (sin subconsultas hacia tablas fuera de la whitelist, sin punto y coma múltiple, sin comentarios SQL). Se fuerza automáticamente LIMIT 250. Tablas permitidas: tbl_sondeo, tbl_respuestas_sondeos, tbl_sondeo_x_opciones, tbl_sondeo_x_tbl_participantes, tbl_ficha_tecnica_encuestas, tbl_preguntas, tbl_opciones_respuesta, tbl_analisis_estudio, tbl_analisis_calculos, tbl_grilla, tbl_grilla_x_preguntas, tbl_preguntas_sub_preguntas_grilla, tbl_grilla_sesion_votacion, tbl_grilla_respuestas, tbl_participantes, tbl_partidos_politicos, tbl_votantes, tbl_espacio_geografico, tbl_certificacion_encuestador, tbl_certificacion_revision_historial, tbl_cuestionario_intentos, tbl_cuestionario_respuestas, tbl_formulas, tbl_clientes. tbl_grilla_candidato_respuestas NO existe en la base de datos. Nunca selecciones columnas de identificación personal exacta de votantes (cédula, teléfono, dirección) — solo agregados o campos demográficos ya anonimizados.
TXT;

    public static function ejecutar(string $sql): array
    {
        $sql = trim($sql);
        $validacion = self::validar($sql);
        if ($validacion !== null) {
            return ['error' => 'sql_no_permitido', 'mensaje' => $validacion];
        }

        $db = new DbConection();
        $sqlCalificada = self::calificarTablas($sql, $db->getDbName());
        $sqlLimitada = self::forzarLimite($sqlCalificada);

        $pdo = $db->openConect();
        try {
            $stmt = $pdo->query($sqlLimitada);
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($filas) > self::LIMITE_FILAS) {
                $filas = array_slice($filas, 0, self::LIMITE_FILAS);
            }
            return ['filas' => $filas, 'total' => count($filas)];
        } catch (PDOException $e) {
            return ['error' => 'error_sql', 'mensaje' => $e->getMessage()];
        } finally {
            $db->closeConect();
        }
    }

    private static function validar(string $sql): ?string
    {
        if ($sql === '') {
            return 'La consulta no puede estar vacía.';
        }
        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            return 'Solo se permiten sentencias SELECT.';
        }
        if (str_contains($sql, '--') || str_contains($sql, '/*') || str_contains($sql, '#')) {
            return 'No se permiten comentarios SQL en la consulta.';
        }

        $sinFinal = rtrim($sql);
        $sinFinal = rtrim($sinFinal, ';');
        if (str_contains($sinFinal, ';')) {
            return 'No se permite más de una sentencia por consulta.';
        }

        foreach (self::PALABRAS_PROHIBIDAS as $palabra) {
            if (preg_match('/\b' . preg_quote($palabra, '/') . '\b/i', $sql)) {
                return "La palabra clave '{$palabra}' no está permitida en esta consulta.";
            }
        }

        preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?/i', $sql, $coincidencias);
        $tablasUsadas = array_unique($coincidencias[1] ?? []);
        if (empty($tablasUsadas)) {
            return 'No se pudo identificar ninguna tabla en la consulta.';
        }
        foreach ($tablasUsadas as $tabla) {
            if (!in_array(strtolower($tabla), self::TABLAS_PERMITIDAS, true)) {
                return "La tabla '{$tabla}' no está permitida para esta herramienta.";
            }
        }

        return null;
    }

    private static function calificarTablas(string $sql, string $dbName): string
    {
        return preg_replace_callback(
            '/\b(FROM|JOIN)\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?/i',
            fn ($m) => $m[1] . ' ' . $dbName . '.' . $m[2],
            $sql
        );
    }

    private static function forzarLimite(string $sql): string
    {
        $sql = rtrim(rtrim($sql), ';');
        if (preg_match('/\bLIMIT\s+(\d+)\b/i', $sql, $m)) {
            if ((int) $m[1] > self::LIMITE_FILAS) {
                return preg_replace('/\bLIMIT\s+\d+\b/i', 'LIMIT ' . self::LIMITE_FILAS, $sql, 1);
            }
            return $sql;
        }
        return $sql . ' LIMIT ' . self::LIMITE_FILAS;
    }
}
