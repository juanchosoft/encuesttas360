<?php

require_once __DIR__ . '/DefinicionCuestionario.php';
require_once __DIR__ . '/MotorReglasCuestionario.php';
require_once __DIR__ . '/RespuestaLegible.php';

class DetalleIntento
{
    public static function cargar(PDO $pdo, DbConection $db, int $intentoId): ?array
    {
        $st = $pdo->prepare("SELECT i.*, f.realizada_por_o_encomendada_por AS ficha_titulo
            FROM " . $db->getTable('tbl_cuestionario_intentos') . " i
            LEFT JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON f.id = i.tbl_ficha_tecnica_encuesta_id
            WHERE i.id = :id LIMIT 1");
        $st->execute([':id' => $intentoId]);
        $intento = $st->fetch(PDO::FETCH_ASSOC);
        if (!$intento) {
            return null;
        }

        $stR = $pdo->prepare("SELECT r.tbl_pregunta_id, r.tbl_opcion_respuesta_id, r.tbl_grilla_fila_id, r.tbl_grilla_columna_id,
                r.tbl_grilla_columna_opcion_id, r.respuesta_texto, r.valor_numerico, r.texto_snapshot,
                o.texto_opcion, gf.etiqueta AS fila_etiqueta, gco.texto AS celda_opcion_etiqueta
            FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
            LEFT JOIN " . $db->getTable('tbl_opciones_respuesta') . " o ON o.id = r.tbl_opcion_respuesta_id
            LEFT JOIN " . $db->getTable('tbl_grilla_filas') . " gf ON gf.id = r.tbl_grilla_fila_id
            LEFT JOIN " . $db->getTable('tbl_grilla_columna_opciones') . " gco ON gco.id = r.tbl_grilla_columna_opcion_id
            WHERE r.tbl_intento_id = :id ORDER BY r.id ASC");
        $stR->execute([':id' => $intentoId]);
        $filas = $stR->fetchAll(PDO::FETCH_ASSOC);

        $def = DefinicionCuestionario::cargar($pdo, (int)$intento['tbl_ficha_tecnica_encuesta_id'], $db->getDbName());
        $ctx = [
            'codigo_municipio' => (string)($intento['codigo_municipio'] ?? ''),
            'codigo_departamento' => (string)($intento['codigo_departamento'] ?? ''),
        ];

        $legible = $def
            ? RespuestaLegible::formatearIntento($def, $filas, $ctx)
            : ['lineas_texto' => [], 'texto_completo' => '', 'bloques' => [], 'grillas' => []];

        $meta = [];
        foreach ($def['preguntas'] ?? [] as $p) {
            $meta[(int)$p['id']] = ['numeral' => (string)($p['numeral'] ?? ''), 'capitulo' => (string)($p['capitulo'] ?? ''), 'texto' => (string)$p['texto_pregunta']];
        }
        foreach ($legible['bloques'] as &$b) {
            $m = $meta[(int)$b['pregunta_id']] ?? ['numeral' => '', 'capitulo' => '', 'texto' => ''];
            $b['numeral'] = $m['numeral'];
            $b['capitulo'] = $m['capitulo'];
            $b['enunciado'] = $m['texto'];
        }
        unset($b);

        return [
            'intento_id' => (int)$intento['id'],
            'ficha_id' => (int)$intento['tbl_ficha_tecnica_encuesta_id'],
            'ficha_titulo' => (string)$intento['ficha_titulo'],
            'estado' => (string)($intento['estado'] ?? 'completo'),
            'valido_para_pago' => (int)($intento['valido_para_pago'] ?? 1),
            'origen' => (string)($intento['origen'] ?? ''),
            'codigo_municipio' => (string)($intento['codigo_municipio'] ?? ''),
            'zona' => (string)($intento['zona_texto'] ?? ''),
            'terminado_en_pregunta_id' => $intento['terminado_en_pregunta_id'] !== null ? (int)$intento['terminado_en_pregunta_id'] : null,
            'bloques' => $legible['bloques'],
            'lineas_texto' => $legible['lineas_texto'],
            'texto_completo' => $legible['texto_completo'],
        ];
    }
}
