<?php

class RespuestaLegible
{
    public static function formatearIntento(array $def, array $filasRespuestas, array $ctx = []): array
    {
        $pregById = [];
        foreach ($def['preguntas'] ?? [] as $p) {
            $pregById[(int)$p['id']] = $p;
        }
        $plantillasAll = (array)($def['plantillas'] ?? []);

        $porPregunta = [];
        foreach ($filasRespuestas as $r) {
            $pid = (int)($r['tbl_pregunta_id'] ?? $r['pregunta_id'] ?? 0);
            if ($pid <= 0) {
                continue;
            }
            $porPregunta[$pid][] = $r;
        }

        $lineasTexto = [];
        $bloquesPregunta = [];
        $resumenGrillas = [];

        foreach ($def['preguntas'] ?? [] as $p) {
            $pid = (int)$p['id'];
            $num = trim((string)($p['numeral'] ?? ''));
            $prefijo = $num !== '' ? ($num . '. ') : '';
            $tituloPregunta = $prefijo . $p['texto_pregunta'];
            $tipo = (string)$p['tipo_pregunta'];
            $rowsP = $porPregunta[$pid] ?? [];

            if ($tipo === 'Grilla') {
                $pl = MotorReglasCuestionario::resolverPlantillaEfectiva($def, $p, $ctx);
                if (!$pl && !empty($rowsP)) {
                    foreach ($plantillasAll as $plCand) {
                        $plCand = (array)$plCand;
                        foreach ($plCand['filas'] ?? [] as $flC) {
                            if ((int)$flC['id'] === (int)($rowsP[0]['tbl_grilla_fila_id'] ?? 0)) {
                                $pl = $plCand;
                                break 2;
                            }
                        }
                    }
                }
                if (!$pl) {
                    continue;
                }

                $colById = [];
                $optById = [];
                foreach ($pl['columnas'] ?? [] as $c) {
                    $cid = (int)$c['id'];
                    $colById[$cid] = $c;
                    foreach ($c['opciones'] ?? [] as $op) {
                        $optById[(int)$op['id']] = $op;
                    }
                }

                $celdasPorFilaCol = [];
                foreach ($rowsP as $r) {
                    $fid = (int)($r['tbl_grilla_fila_id'] ?? 0);
                    $cid = (int)($r['tbl_grilla_columna_id'] ?? 0);
                    $coid = (int)($r['tbl_grilla_columna_opcion_id'] ?? 0);
                    if ($fid > 0 && $cid > 0) {
                        $txtOpt = isset($optById[$coid]) ? $optById[$coid]['texto'] : (string)($r['celda_opcion_etiqueta'] ?? $r['respuesta_texto'] ?? '');
                        if ($txtOpt !== '') {
                            $celdasPorFilaCol[$fid][$cid][] = $txtOpt;
                        }
                    }
                }

                if (empty($celdasPorFilaCol)) {
                    continue;
                }

                $filasFormateadas = [];
                foreach ($pl['filas'] ?? [] as $fl) {
                    $fid = (int)$fl['id'];
                    $partesCandidato = [];
                    $valoresColumnas = [];
                    $bloqueada = false;

                    foreach ($pl['columnas'] ?? [] as $c) {
                        $cid = (int)$c['id'];
                        $condCol = !empty($c['condicion_columna_id']) ? (int)$c['condicion_columna_id'] : 0;
                        $condOpt = !empty($c['condicion_opcion_id']) ? (int)$c['condicion_opcion_id'] : 0;

                        if ($condCol > 0 && $condOpt > 0) {
                            $txtCondEsperada = isset($optById[$condOpt]) ? $optById[$condOpt]['texto'] : '';
                            $respondidasCond = $celdasPorFilaCol[$fid][$condCol] ?? [];
                            if ($txtCondEsperada !== '' && !in_array($txtCondEsperada, $respondidasCond, true)) {
                                $bloqueada = true;
                                $valoresColumnas[] = '';
                                continue;
                            }
                        }

                        $vals = $celdasPorFilaCol[$fid][$cid] ?? [];
                        $valoresColumnas[] = implode(', ', $vals);
                        if (!empty($vals)) {
                            $partesCandidato[] = $c['titulo'] . ': ' . implode(', ', $vals);
                        }
                    }

                    if (!empty($partesCandidato)) {
                        $lineaCandidato = ($num !== '' ? ($num . '. ') : '') . $fl['etiqueta'] . ' — ' . implode(' — ', $partesCandidato);
                        $lineasTexto[] = $lineaCandidato;
                        $filasFormateadas[] = [
                            'fila_id' => $fid,
                            'candidato' => $fl['etiqueta'],
                            'bloqueada_condicion' => $bloqueada,
                            'resumen' => implode(' — ', $partesCandidato),
                            'valores' => $valoresColumnas,
                            'celdas' => $celdasPorFilaCol[$fid] ?? [],
                        ];
                    }
                }

                $resumenGrillas[] = [
                    'pregunta_id' => $pid,
                    'numeral' => $num,
                    'pregunta' => $p['texto_pregunta'],
                    'plantilla_nombre' => $pl['nombre'] ?? '',
                    'filas' => $filasFormateadas,
                ];
                $bloquesPregunta[] = [
                    'pregunta_id' => $pid,
                    'tipo' => 'Grilla',
                    'titulo' => $tituloPregunta,
                    'columnas' => array_map(function ($c) { return (string)$c['titulo']; }, $pl['columnas'] ?? []),
                    'filas' => $filasFormateadas,
                ];
                continue;
            }

            if (empty($rowsP)) {
                continue;
            }

            $optMap = [];
            foreach ($p['opciones'] ?? [] as $op) {
                $optMap[(int)$op['id']] = $op['texto'];
            }

            $filaMap = [];
            foreach ($plantillasAll as $plCand) {
                $plCand = (array)$plCand;
                foreach ($plCand['filas'] ?? [] as $flC) {
                    $filaMap[(int)$flC['id']] = $flC['etiqueta'];
                }
            }

            $textosResp = [];
            foreach ($rowsP as $r) {
                $oid = (int)($r['tbl_opcion_respuesta_id'] ?? 0);
                $fid = (int)($r['tbl_grilla_fila_id'] ?? 0);
                if ($oid > 0 && isset($optMap[$oid])) {
                    $textosResp[] = $optMap[$oid];
                } elseif ($fid > 0 && isset($filaMap[$fid])) {
                    $textosResp[] = $filaMap[$fid];
                } elseif (!empty($r['texto_opcion'])) {
                    $textosResp[] = (string)$r['texto_opcion'];
                } elseif (!empty($r['fila_etiqueta'])) {
                    $textosResp[] = (string)$r['fila_etiqueta'];
                } elseif (isset($r['respuesta_texto']) && trim((string)$r['respuesta_texto']) !== '') {
                    $textosResp[] = trim((string)$r['respuesta_texto']);
                }
            }

            if (!empty($textosResp)) {
                $respUnica = implode(', ', array_unique($textosResp));
                $linea = $tituloPregunta . ': ' . $respUnica;
                $lineasTexto[] = $linea;
                $bloquesPregunta[] = [
                    'pregunta_id' => $pid,
                    'tipo' => $tipo,
                    'titulo' => $tituloPregunta,
                    'respuesta' => $respUnica,
                ];
            }
        }

        return [
            'lineas_texto' => $lineasTexto,
            'texto_completo' => implode("\n", $lineasTexto),
            'bloques' => $bloquesPregunta,
            'grillas' => $resumenGrillas,
        ];
    }
}
