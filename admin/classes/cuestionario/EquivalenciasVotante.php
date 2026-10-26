<?php

class EquivalenciasVotante
{
    public static function extraer(array $def, array $respuestasNormalizadas): array
    {
        $resultado = [];
        $normMap = [];
        foreach ($respuestasNormalizadas as $rn) {
            $normMap[(int)$rn['pregunta_id']] = $rn;
        }

        $eqPorPregOpt = [];
        foreach ($def['equivalencias_votante'] ?? [] as $eq) {
            $pid = (int)$eq['tbl_pregunta_id'];
            $oid = (int)$eq['tbl_opcion_respuesta_id'];
            $campo = trim((string)$eq['campo_votante']);
            $eqPorPregOpt[$pid][$oid][$campo] = trim((string)$eq['valor_votante']);
        }

        foreach ($def['preguntas'] ?? [] as $p) {
            $pid = (int)$p['id'];
            if (!isset($normMap[$pid])) {
                continue;
            }
            $elegidas = $normMap[$pid]['opciones'] ?? [];
            if (empty($elegidas)) {
                continue;
            }
            $oid = (int)$elegidas[0];

            if (isset($eqPorPregOpt[$pid][$oid])) {
                foreach ($eqPorPregOpt[$pid][$oid] as $c => $val) {
                    if ($val !== '') {
                        $resultado[$c] = $val;
                    }
                }
            }

            $campoDirecto = !empty($p['copiar_a_votante_campo']) ? trim((string)$p['copiar_a_votante_campo']) : '';
            if ($campoDirecto === 'ocupacion' && !isset($resultado['ocupacion'])) {
                foreach ($p['opciones'] ?? [] as $opDef) {
                    if ((int)$opDef['id'] === $oid) {
                        $resultado['ocupacion'] = mb_substr(trim((string)$opDef['texto']), 0, 100, 'UTF-8');
                        break;
                    }
                }
            }
        }

        return $resultado;
    }
}
