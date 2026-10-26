<?php

class MotorReglasCuestionario
{
    const MOTOR_VERSION = '1.0.0';

    public static function indexarRespuestas(array $respuestasRaw): array
    {
        $map = [];
        foreach ($respuestasRaw as $k => $v) {
            if (is_array($v) && isset($v['pregunta_id'])) {
                $pid = (int)$v['pregunta_id'];
                $map[$pid] = self::normalizarEntradaPregunta($pid, $v);
            } elseif (is_numeric($k) && is_array($v)) {
                $pid = (int)$k;
                $map[$pid] = self::normalizarEntradaPregunta($pid, $v);
            }
        }
        return $map;
    }

    private static function normalizarEntradaPregunta(int $pid, array $item): array
    {
        $opciones = [];
        if (isset($item['opciones']) && is_array($item['opciones'])) {
            foreach ($item['opciones'] as $opId) {
                $val = (int)$opId;
                if ($val > 0 && !in_array($val, $opciones, true)) {
                    $opciones[] = $val;
                }
            }
        } elseif (!empty($item['opcion_id'])) {
            $opciones[] = (int)$item['opcion_id'];
        }

        $texto = isset($item['texto']) ? trim((string)$item['texto']) : '';

        $grilla = [];
        if (isset($item['grilla']) && is_array($item['grilla'])) {
            foreach ($item['grilla'] as $kCell => $cell) {
                if (is_array($cell) && isset($cell['fila_id']) && isset($cell['columna_id'])) {
                    $fid = (int)$cell['fila_id'];
                    $cid = (int)$cell['columna_id'];
                    $cOpts = [];
                    if (isset($cell['opciones']) && is_array($cell['opciones'])) {
                        foreach ($cell['opciones'] as $coId) {
                            $v = (int)$coId;
                            if ($v > 0 && !in_array($v, $cOpts, true)) {
                                $cOpts[] = $v;
                            }
                        }
                    } elseif (!empty($cell['opcion_id'])) {
                        $cOpts[] = (int)$cell['opcion_id'];
                    }
                    if ($fid > 0 && $cid > 0 && !empty($cOpts)) {
                        $grilla[$fid][$cid] = $cOpts;
                    }
                } elseif (is_numeric($kCell) && is_array($cell)) {
                    $fid = (int)$kCell;
                    foreach ($cell as $cidKey => $colVal) {
                        $cid = (int)$cidKey;
                        $cOpts = [];
                        if (is_array($colVal)) {
                            foreach ($colVal as $coId) {
                                $v = (int)$coId;
                                if ($v > 0 && !in_array($v, $cOpts, true)) {
                                    $cOpts[] = $v;
                                }
                            }
                        } else {
                            $v = (int)$colVal;
                            if ($v > 0) {
                                $cOpts[] = $v;
                            }
                        }
                        if ($fid > 0 && $cid > 0 && !empty($cOpts)) {
                            $grilla[$fid][$cid] = $cOpts;
                        }
                    }
                }
            }
        }

        $lista = null;
        if (isset($item['lista']) && is_array($item['lista'])) {
            $lf = !empty($item['lista']['fila_id']) ? (int)$item['lista']['fila_id'] : null;
            $lo = !empty($item['lista']['opcion_id']) ? (int)$item['lista']['opcion_id'] : null;
            if ($lf !== null || $lo !== null) {
                $lista = ['fila_id' => $lf, 'opcion_id' => $lo];
            }
        }

        return [
            'pregunta_id' => $pid,
            'opciones' => $opciones,
            'texto' => $texto,
            'grilla' => $grilla,
            'lista' => $lista,
        ];
    }

    private static function limiteColumna(array $c): int
    {
        return max(1, (int)($c['limite_multiple'] ?? 1));
    }

    private static function valorNumericoTexto(string $txt): ?float
    {
        if (preg_match('/^-?\d{1,8}(\.\d+)?$/', $txt)) {
            return (float)$txt;
        }
        return null;
    }

    private static function sanearRespuestas(array $def, array $respMap): array
    {
        $columnasPorId = [];
        foreach ((array)($def['plantillas'] ?? []) as $pl) {
            foreach ($pl['columnas'] ?? [] as $c) {
                $validas = [];
                foreach ($c['opciones'] ?? [] as $co) {
                    $validas[] = (int)$co['id'];
                }
                $columnasPorId[(int)$c['id']] = [
                    'validas' => $validas,
                    'multiple' => ($c['tipo_respuesta'] ?? '') === 'multiple',
                    'limite' => self::limiteColumna($c),
                ];
            }
        }

        foreach ($def['preguntas'] ?? [] as $p) {
            $pid = (int)$p['id'];
            if (!isset($respMap[$pid])) {
                continue;
            }
            $validas = [];
            foreach ($p['opciones'] ?? [] as $op) {
                $validas[] = (int)$op['id'];
            }
            $elegidas = [];
            foreach ($respMap[$pid]['opciones'] as $oid) {
                if (in_array($oid, $validas, true)) {
                    $elegidas[] = $oid;
                }
            }
            $lim = max(1, (int)($p['limite_respuesta_multiple'] ?? 1));
            $respMap[$pid]['opciones'] = array_slice($elegidas, 0, $lim);

            $grilla = [];
            foreach ($respMap[$pid]['grilla'] as $fid => $celdas) {
                foreach ($celdas as $cid => $opts) {
                    if (!isset($columnasPorId[$cid])) {
                        continue;
                    }
                    $info = $columnasPorId[$cid];
                    $ok = [];
                    foreach ($opts as $coId) {
                        if (in_array($coId, $info['validas'], true)) {
                            $ok[] = $coId;
                        }
                    }
                    $ok = array_slice($ok, 0, $info['multiple'] ? $info['limite'] : 1);
                    if (!empty($ok)) {
                        $grilla[$fid][$cid] = $ok;
                    }
                }
            }
            $respMap[$pid]['grilla'] = $grilla;
        }
        return $respMap;
    }

    public static function resolverPlantillaEfectiva(array $def, array $pregunta, array $ctx): ?array
    {
        $plantillas = isset($def['plantillas']) ? (array)$def['plantillas'] : [];
        $codMun = isset($ctx['codigo_municipio']) ? trim((string)$ctx['codigo_municipio']) : '';
        $variantes = isset($pregunta['variantes_municipio']) ? (array)$pregunta['variantes_municipio'] : [];

        if (!empty($variantes)) {
            if ($codMun === '' || !isset($variantes[$codMun])) {
                return null;
            }
            $plId = (int)$variantes[$codMun];
            return isset($plantillas[$plId]) ? (array)$plantillas[$plId] : (isset($plantillas[(string)$plId]) ? (array)$plantillas[(string)$plId] : null);
        }

        $plId = !empty($pregunta['tbl_grilla_plantilla_id']) ? (int)$pregunta['tbl_grilla_plantilla_id'] : 0;
        if ($plId <= 0) {
            return null;
        }
        return isset($plantillas[$plId]) ? (array)$plantillas[$plId] : (isset($plantillas[(string)$plId]) ? (array)$plantillas[(string)$plId] : null);
    }

    public static function evaluar(array $def, array $respuestasRaw, array $ctx = []): array
    {
        $preguntas = $def['preguntas'] ?? [];
        $respMap = self::sanearRespuestas($def, self::indexarRespuestas($respuestasRaw));

        $pregById = [];
        $posPorId = [];
        foreach ($preguntas as $i => $p) {
            $pregById[(int)$p['id']] = $p;
            $posPorId[(int)$p['id']] = $i;
        }

        $visibles = [];
        $omitidas = [];
        $plantillaEfectivaPorPregunta = [];
        $filasBloqueadasPorPregunta = [];
        $columnasActivasPorFila = [];
        $listasCalculadas = [];

        $terminado = false;
        $terminadoPorFiltro = false;
        $terminadoEnPreguntaId = null;
        $terminadoOpcionId = null;
        $mensajeCierre = $def['ficha']['mensaje_cierre_defecto'] ?? 'Muchas gracias por su tiempo. Hemos terminado la encuesta.';

        $saltoActivoHastaPreguntaId = null;

        foreach ($preguntas as $posActual => $p) {
            $pid = (int)$p['id'];

            if ($terminado) {
                $omitidas[] = $pid;
                continue;
            }

            if ($saltoActivoHastaPreguntaId !== null) {
                if ($pid !== $saltoActivoHastaPreguntaId) {
                    $omitidas[] = $pid;
                    continue;
                }
                $saltoActivoHastaPreguntaId = null;
            }

            if (!empty($p['visible_si_pregunta_id']) && !empty($p['visible_si_opcion_id'])) {
                $depPid = (int)$p['visible_si_pregunta_id'];
                $depOid = (int)$p['visible_si_opcion_id'];
                if (!in_array($depPid, $visibles, true)) {
                    $omitidas[] = $pid;
                    continue;
                }
                $respDep = $respMap[$depPid]['opciones'] ?? [];
                if (!in_array($depOid, $respDep, true)) {
                    $omitidas[] = $pid;
                    continue;
                }
            }

            $tipo = (string)$p['tipo_pregunta'];

            if ($tipo === 'Grilla') {
                $plantilla = self::resolverPlantillaEfectiva($def, $p, $ctx);
                if (!$plantilla) {
                    $omitidas[] = $pid;
                    continue;
                }
                $plantillaEfectivaPorPregunta[$pid] = (int)$plantilla['id'];
                $visibles[] = $pid;

                $filasBloqueadasPorPregunta[$pid] = [];
                $columnasActivasPorFila[$pid] = [];

                $filas = $plantilla['filas'] ?? [];
                $columnas = $plantilla['columnas'] ?? [];

                foreach ($filas as $f) {
                    $fid = (int)$f['id'];
                    $activasFila = [];
                    $bloqueadaFila = false;

                    foreach ($columnas as $c) {
                        $cid = (int)$c['id'];
                        $condColId = !empty($c['condicion_columna_id']) ? (int)$c['condicion_columna_id'] : 0;
                        $condOptId = !empty($c['condicion_opcion_id']) ? (int)$c['condicion_opcion_id'] : 0;

                        if ($condColId > 0 && $condOptId > 0) {
                            $chosenInCond = $respMap[$pid]['grilla'][$fid][$condColId] ?? [];
                            if (!in_array($condOptId, $chosenInCond, true)) {
                                if (!empty($chosenInCond)) {
                                    $bloqueadaFila = true;
                                }
                                unset($respMap[$pid]['grilla'][$fid][$cid]);
                                continue;
                            }
                        }
                        $activasFila[] = $cid;
                    }

                    $columnasActivasPorFila[$pid][$fid] = $activasFila;
                    if ($bloqueadaFila) {
                        $filasBloqueadasPorPregunta[$pid][] = $fid;
                    }
                }
                continue;
            }

            if ($tipo === 'Lista_desde_grilla') {
                $origenPid = !empty($p['lista_origen_pregunta_id']) ? (int)$p['lista_origen_pregunta_id'] : 0;
                $origenColId = !empty($p['lista_origen_columna_id']) ? (int)$p['lista_origen_columna_id'] : 0;
                $origenOptId = !empty($p['lista_origen_opcion_id']) ? (int)$p['lista_origen_opcion_id'] : 0;

                if ($origenPid <= 0 || !in_array($origenPid, $visibles, true) || !isset($plantillaEfectivaPorPregunta[$origenPid])) {
                    $omitidas[] = $pid;
                    continue;
                }

                $plIdOrigen = $plantillaEfectivaPorPregunta[$origenPid];
                $plantillasAll = (array)($def['plantillas'] ?? []);
                $plOrigen = isset($plantillasAll[$plIdOrigen]) ? (array)$plantillasAll[$plIdOrigen] : (isset($plantillasAll[(string)$plIdOrigen]) ? (array)$plantillasAll[(string)$plIdOrigen] : null);
                if (!$plOrigen) {
                    $omitidas[] = $pid;
                    continue;
                }

                $colEfectivaId = $origenColId;
                $optEfectivaId = $origenOptId;
                $pregOrigenDef = $pregById[$origenPid] ?? null;
                $plBaseOrigenId = !empty($pregOrigenDef['tbl_grilla_plantilla_id']) ? (int)$pregOrigenDef['tbl_grilla_plantilla_id'] : 0;
                if ($plBaseOrigenId <= 0 && $origenColId > 0) {
                    foreach ($plantillasAll as $plCand) {
                        foreach ((array)($plCand['columnas'] ?? []) as $cCand) {
                            if ((int)$cCand['id'] === $origenColId) {
                                $plBaseOrigenId = (int)$plCand['id'];
                                break 2;
                            }
                        }
                    }
                }

                if ($plBaseOrigenId > 0 && $plBaseOrigenId !== $plIdOrigen) {
                    $plBase = isset($plantillasAll[$plBaseOrigenId]) ? (array)$plantillasAll[$plBaseOrigenId] : (isset($plantillasAll[(string)$plBaseOrigenId]) ? (array)$plantillasAll[(string)$plBaseOrigenId] : null);
                    if ($plBase) {
                        $ordenColBuscado = null;
                        $ordenOptBuscado = null;
                        foreach ($plBase['columnas'] ?? [] as $cb) {
                            if ((int)$cb['id'] === $origenColId) {
                                $ordenColBuscado = (int)$cb['orden'];
                                foreach ($cb['opciones'] ?? [] as $ob) {
                                    if ((int)$ob['id'] === $origenOptId) {
                                        $ordenOptBuscado = (int)$ob['orden'];
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                        if ($ordenColBuscado !== null) {
                            foreach ($plOrigen['columnas'] ?? [] as $cv) {
                                if ((int)$cv['orden'] === $ordenColBuscado) {
                                    $colEfectivaId = (int)$cv['id'];
                                    if ($ordenOptBuscado !== null) {
                                        foreach ($cv['opciones'] ?? [] as $ov) {
                                            if ((int)$ov['orden'] === $ordenOptBuscado) {
                                                $optEfectivaId = (int)$ov['id'];
                                                break;
                                            }
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }

                $excluidosFilaIds = [];
                foreach ($p['exclusiones_pregunta_ids'] ?? [] as $exPid) {
                    $exPid = (int)$exPid;
                    $elegidoFila = (int)($respMap[$exPid]['lista']['fila_id'] ?? 0);
                    if ($elegidoFila <= 0) {
                        continue;
                    }
                    foreach ($listasCalculadas[$exPid] ?? [] as $itEx) {
                        if ($itEx['tipo_item'] === 'fila' && (int)$itEx['fila_id'] === $elegidoFila) {
                            $excluidosFilaIds[] = $elegidoFila;
                            break;
                        }
                    }
                }

                $respGrillaOrigen = $respMap[$origenPid]['grilla'] ?? [];
                $candidatosDisponibles = [];
                $totalBase = 0;
                foreach ($plOrigen['filas'] ?? [] as $fl) {
                    $fid = (int)$fl['id'];
                    if ($colEfectivaId > 0 && $optEfectivaId > 0) {
                        $marcadas = $respGrillaOrigen[$fid][$colEfectivaId] ?? [];
                        if (!in_array($optEfectivaId, $marcadas, true)) {
                            continue;
                        }
                    }
                    $totalBase++;
                    if (in_array($fid, $excluidosFilaIds, true)) {
                        continue;
                    }
                    $candidatosDisponibles[] = [
                        'tipo_item' => 'fila',
                        'fila_id' => $fid,
                        'opcion_id' => null,
                        'etiqueta' => (string)$fl['etiqueta'],
                        'subtitulo' => $fl['subtitulo'] ?? null,
                        'foto' => $fl['foto'] ?? null,
                    ];
                }

                $tieneFijas = false;
                foreach ($p['opciones'] ?? [] as $opFijaChk) {
                    if (!empty($opFijaChk['es_opcion_fija'])) {
                        $tieneFijas = true;
                        break;
                    }
                }
                if (empty($candidatosDisponibles) && !($totalBase > 0 && $tieneFijas)) {
                    $siVacia = $p['si_lista_vacia'] ?? 'omitir';
                    if ($siVacia === 'terminar') {
                        $visibles[] = $pid;
                        $terminado = true;
                        $terminadoEnPreguntaId = $pid;
                    } else {
                        $omitidas[] = $pid;
                    }
                    continue;
                }

                $opcionesFijas = [];
                foreach ($p['opciones'] ?? [] as $opFija) {
                    $opcionesFijas[] = [
                        'tipo_item' => 'opcion_fija',
                        'fila_id' => null,
                        'opcion_id' => (int)$opFija['id'],
                        'etiqueta' => (string)$opFija['texto'],
                        'accion' => $opFija['accion'] ?? 'continuar',
                        'salto_destino_pregunta_id' => $opFija['salto_destino_pregunta_id'] ?? null,
                        'mensaje_terminar' => $opFija['mensaje_terminar'] ?? null,
                    ];
                }

                $listasCalculadas[$pid] = array_merge($candidatosDisponibles, $opcionesFijas);
                $visibles[] = $pid;

                $elegidoFilaLista = (int)($respMap[$pid]['lista']['fila_id'] ?? 0);
                $filaListaValida = false;
                foreach ($candidatosDisponibles as $cd) {
                    if ($cd['fila_id'] === $elegidoFilaLista) {
                        $filaListaValida = true;
                        break;
                    }
                }
                $elegidoOptFija = $filaListaValida ? null : ($respMap[$pid]['lista']['opcion_id'] ?? null);
                if ($elegidoOptFija !== null) {
                    foreach ($p['opciones'] ?? [] as $opDef) {
                        if ((int)$opDef['id'] === (int)$elegidoOptFija) {
                            $acc = $opDef['accion'] ?? 'continuar';
                            if ($acc === 'terminar') {
                                $terminado = true;
                                $terminadoPorFiltro = ((int)($p['es_filtro'] ?? 0)) === 1;
                                $terminadoEnPreguntaId = $pid;
                                $terminadoOpcionId = (int)$opDef['id'];
                                if (!empty($opDef['mensaje_terminar'])) {
                                    $mensajeCierre = (string)$opDef['mensaje_terminar'];
                                }
                            } elseif ($acc === 'saltar' && !empty($opDef['salto_destino_pregunta_id'])) {
                                $destinoId = (int)$opDef['salto_destino_pregunta_id'];
                                if (isset($posPorId[$destinoId]) && $posPorId[$destinoId] > $posActual) {
                                    $saltoActivoHastaPreguntaId = $destinoId;
                                }
                            }
                            break;
                        }
                    }
                }
                continue;
            }

            $visibles[] = $pid;

            $respOpciones = $respMap[$pid]['opciones'] ?? [];
            if (!empty($respOpciones) && !empty($p['opciones'])) {
                foreach ($p['opciones'] as $opDef) {
                    $oid = (int)$opDef['id'];
                    if (in_array($oid, $respOpciones, true)) {
                        $acc = $opDef['accion'] ?? 'continuar';
                        if ($acc === 'terminar') {
                            $terminado = true;
                            $terminadoPorFiltro = ((int)($p['es_filtro'] ?? 0)) === 1;
                            $terminadoEnPreguntaId = $pid;
                            $terminadoOpcionId = $oid;
                            if (!empty($opDef['mensaje_terminar'])) {
                                $mensajeCierre = (string)$opDef['mensaje_terminar'];
                            }
                            break;
                        } elseif ($acc === 'saltar' && !empty($opDef['salto_destino_pregunta_id'])) {
                            $destinoId = (int)$opDef['salto_destino_pregunta_id'];
                            if (isset($posPorId[$destinoId]) && $posPorId[$destinoId] > $posActual) {
                                $saltoActivoHastaPreguntaId = $destinoId;
                            }
                        }
                    }
                }
            }
        }

        $siguientePreguntaId = null;
        if (!$terminado) {
            foreach ($visibles as $vPid) {
                $pDef = $pregById[$vPid];
                if (!self::preguntaEstaCompleta($def, $pDef, $respMap[$vPid] ?? null, $plantillaEfectivaPorPregunta[$vPid] ?? null, $columnasActivasPorFila[$vPid] ?? [], $listasCalculadas[$vPid] ?? [])) {
                    $siguientePreguntaId = $vPid;
                    break;
                }
            }
        }

        return [
            'visibles' => $visibles,
            'omitidas' => $omitidas,
            'plantilla_efectiva' => $plantillaEfectivaPorPregunta,
            'filas_bloqueadas' => $filasBloqueadasPorPregunta,
            'columnas_activas_por_fila' => $columnasActivasPorFila,
            'listas_calculadas' => $listasCalculadas,
            'terminado' => $terminado,
            'terminado_por_filtro' => $terminadoPorFiltro,
            'terminado_en_pregunta_id' => $terminadoEnPreguntaId,
            'terminado_opcion_id' => $terminadoOpcionId,
            'mensaje_cierre' => $mensajeCierre,
            'siguiente_pregunta_id' => $siguientePreguntaId,
        ];
    }

    private static function preguntaEstaCompleta(array $def, array $pDef, ?array $rItem, ?int $plEfectivaId, array $colsActivasFila, array $listaItems): bool
    {
        $obligatoria = ((int)($pDef['obligatoria'] ?? 1)) === 1;
        if (!$obligatoria) {
            return true;
        }
        if (!$rItem) {
            return false;
        }

        $tipo = (string)$pDef['tipo_pregunta'];

        if ($tipo === 'Grilla') {
            if (!$plEfectivaId) {
                return true;
            }
            $plantillasAll = (array)($def['plantillas'] ?? []);
            $pl = isset($plantillasAll[$plEfectivaId]) ? (array)$plantillasAll[$plEfectivaId] : (isset($plantillasAll[(string)$plEfectivaId]) ? (array)$plantillasAll[(string)$plEfectivaId] : null);
            if (!$pl) {
                return true;
            }
            $colById = [];
            foreach ($pl['columnas'] ?? [] as $c) {
                $colById[(int)$c['id']] = $c;
            }
            foreach ($pl['filas'] ?? [] as $f) {
                $fid = (int)$f['id'];
                $activas = $colsActivasFila[$fid] ?? [];
                foreach ($activas as $cid) {
                    $cDef = $colById[$cid] ?? null;
                    if ($cDef && ((int)($cDef['obligatoria'] ?? 1)) === 1) {
                        $chosen = $rItem['grilla'][$fid][$cid] ?? [];
                        if (empty($chosen)) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        if ($tipo === 'Lista_desde_grilla') {
            if (empty($listaItems)) {
                return true;
            }
            $lf = (int)($rItem['lista']['fila_id'] ?? 0);
            $lo = (int)($rItem['lista']['opcion_id'] ?? 0);
            foreach ($listaItems as $item) {
                if ($lf > 0 && (int)($item['fila_id'] ?? 0) === $lf) {
                    return true;
                }
                if ($lf <= 0 && $lo > 0 && (int)($item['opcion_id'] ?? 0) === $lo) {
                    return true;
                }
            }
            return false;
        }

        if (!empty($pDef['opciones'])) {
            return !empty($rItem['opciones']);
        }

        return trim((string)($rItem['texto'] ?? '')) !== '';
    }

    public static function validar(array $def, array $respuestasRaw, array $ctx = []): array
    {
        $eval = self::evaluar($def, $respuestasRaw, $ctx);
        $respMap = self::sanearRespuestas($def, self::indexarRespuestas($respuestasRaw));
        $pregById = [];
        foreach ($def['preguntas'] ?? [] as $p) {
            $pregById[(int)$p['id']] = $p;
        }

        $errores = [];
        $normalizadas = [];
        $filasRespuestasPlanas = [];

        foreach ($eval['visibles'] as $pid) {
            $pDef = $pregById[$pid];
            $rItem = $respMap[$pid] ?? ['pregunta_id' => $pid, 'opciones' => [], 'texto' => '', 'grilla' => [], 'lista' => null];
            $tipo = (string)$pDef['tipo_pregunta'];
            $obligatoria = ((int)($pDef['obligatoria'] ?? 1)) === 1;
            $etiquetaHumana = trim((string)(($pDef['numeral'] ? ($pDef['numeral'] . '. ') : '') . $pDef['texto_pregunta']));

            if ($tipo === 'Grilla') {
                $plId = $eval['plantilla_efectiva'][$pid] ?? 0;
                $plantillasAll = (array)($def['plantillas'] ?? []);
                $pl = isset($plantillasAll[$plId]) ? (array)$plantillasAll[$plId] : (isset($plantillasAll[(string)$plId]) ? (array)$plantillasAll[(string)$plId] : null);
                if (!$pl) {
                    continue;
                }

                $colById = [];
                $validOptsByCol = [];
                foreach ($pl['columnas'] ?? [] as $c) {
                    $cid = (int)$c['id'];
                    $colById[$cid] = $c;
                    $validOptsByCol[$cid] = [];
                    foreach ($c['opciones'] ?? [] as $co) {
                        $validOptsByCol[$cid][(int)$co['id']] = $co;
                    }
                }

                $grillaNorm = [];
                foreach ($pl['filas'] ?? [] as $f) {
                    $fid = (int)$f['id'];
                    $activas = $eval['columnas_activas_por_fila'][$pid][$fid] ?? [];

                    foreach ($activas as $cid) {
                        $cDef = $colById[$cid];
                        $rawChosen = $rItem['grilla'][$fid][$cid] ?? [];
                        $validChosen = [];
                        foreach ($rawChosen as $coId) {
                            $coId = (int)$coId;
                            if (isset($validOptsByCol[$cid][$coId]) && !in_array($coId, $validChosen, true)) {
                                $validChosen[] = $coId;
                            }
                        }

                        if ($cDef['tipo_respuesta'] !== 'multiple' && count($validChosen) > 1) {
                            $validChosen = [$validChosen[0]];
                        } elseif ($cDef['tipo_respuesta'] === 'multiple') {
                            $lim = self::limiteColumna($cDef);
                            if (count($validChosen) > $lim) {
                                $validChosen = array_slice($validChosen, 0, $lim);
                            }
                        }

                        if (empty($validChosen) && $obligatoria && ((int)($cDef['obligatoria'] ?? 1)) === 1) {
                            $errores[] = [
                                'pregunta_id' => $pid,
                                'fila_id' => $fid,
                                'columna_id' => $cid,
                                'mensaje' => "Por favor responda «{$cDef['titulo']}» para {$f['etiqueta']}.",
                            ];
                        }

                        if (!empty($validChosen)) {
                            $grillaNorm[] = [
                                'fila_id' => $fid,
                                'columna_id' => $cid,
                                'opciones' => $validChosen,
                            ];
                            foreach ($validChosen as $coId) {
                                $coObj = $validOptsByCol[$cid][$coId];
                                $filasRespuestasPlanas[] = [
                                    'tbl_pregunta_id' => $pid,
                                    'tbl_opcion_respuesta_id' => null,
                                    'tbl_grilla_fila_id' => $fid,
                                    'tbl_grilla_columna_id' => $cid,
                                    'tbl_grilla_columna_opcion_id' => $coId,
                                    'valor_numerico' => $coObj['valor_numerico'] ?? null,
                                    'respuesta_texto' => $coObj['texto'],
                                    'texto_snapshot' => $f['etiqueta'] . ' | ' . $cDef['titulo'] . ': ' . $coObj['texto'],
                                ];
                            }
                        }
                    }
                }

                $normalizadas[] = [
                    'pregunta_id' => $pid,
                    'opciones' => [],
                    'texto' => '',
                    'grilla' => $grillaNorm,
                    'lista' => null,
                ];
                continue;
            }

            if ($tipo === 'Lista_desde_grilla') {
                $itemsLista = $eval['listas_calculadas'][$pid] ?? [];
                if (empty($itemsLista)) {
                    continue;
                }

                $validFilaIds = [];
                $validOptFijaIds = [];
                foreach ($itemsLista as $it) {
                    if ($it['tipo_item'] === 'fila' && !empty($it['fila_id'])) {
                        $validFilaIds[(int)$it['fila_id']] = $it;
                    } elseif ($it['tipo_item'] === 'opcion_fija' && !empty($it['opcion_id'])) {
                        $validOptFijaIds[(int)$it['opcion_id']] = $it;
                    }
                }

                $chosenFila = !empty($rItem['lista']['fila_id']) ? (int)$rItem['lista']['fila_id'] : null;
                $chosenOpt = !empty($rItem['lista']['opcion_id']) ? (int)$rItem['lista']['opcion_id'] : null;

                if ($chosenFila !== null && !isset($validFilaIds[$chosenFila])) {
                    $chosenFila = null;
                }
                if ($chosenOpt !== null && !isset($validOptFijaIds[$chosenOpt])) {
                    $chosenOpt = null;
                }
                if ($chosenFila !== null && $chosenOpt !== null) {
                    $chosenOpt = null;
                }

                if ($chosenFila === null && $chosenOpt === null) {
                    if ($obligatoria) {
                        $errores[] = [
                            'pregunta_id' => $pid,
                            'mensaje' => "Por favor seleccione una opción en «{$etiquetaHumana}».",
                        ];
                    }
                    continue;
                }

                $normalizadas[] = [
                    'pregunta_id' => $pid,
                    'opciones' => $chosenOpt !== null ? [$chosenOpt] : [],
                    'texto' => '',
                    'grilla' => [],
                    'lista' => [
                        'fila_id' => $chosenFila,
                        'opcion_id' => $chosenOpt,
                    ],
                ];

                if ($chosenFila !== null) {
                    $flObj = $validFilaIds[$chosenFila];
                    $filasRespuestasPlanas[] = [
                        'tbl_pregunta_id' => $pid,
                        'tbl_opcion_respuesta_id' => null,
                        'tbl_grilla_fila_id' => $chosenFila,
                        'tbl_grilla_columna_id' => null,
                        'tbl_grilla_columna_opcion_id' => null,
                        'valor_numerico' => null,
                        'respuesta_texto' => $flObj['etiqueta'],
                        'texto_snapshot' => $etiquetaHumana . ': ' . $flObj['etiqueta'],
                    ];
                } else {
                    $opObj = $validOptFijaIds[$chosenOpt];
                    $filasRespuestasPlanas[] = [
                        'tbl_pregunta_id' => $pid,
                        'tbl_opcion_respuesta_id' => $chosenOpt,
                        'tbl_grilla_fila_id' => null,
                        'tbl_grilla_columna_id' => null,
                        'tbl_grilla_columna_opcion_id' => null,
                        'valor_numerico' => null,
                        'respuesta_texto' => null,
                        'texto_snapshot' => $etiquetaHumana . ': ' . $opObj['etiqueta'],
                    ];
                }
                continue;
            }

            $validOpts = [];
            foreach ($pDef['opciones'] ?? [] as $opDef) {
                $validOpts[(int)$opDef['id']] = $opDef;
            }

            if (!empty($validOpts)) {
                $chosenOpts = [];
                foreach ($rItem['opciones'] as $oid) {
                    $oid = (int)$oid;
                    if (isset($validOpts[$oid]) && !in_array($oid, $chosenOpts, true)) {
                        $chosenOpts[] = $oid;
                    }
                }
                $lim = max(1, (int)($pDef['limite_respuesta_multiple'] ?? 1));
                if (count($chosenOpts) > $lim) {
                    $chosenOpts = array_slice($chosenOpts, 0, $lim);
                }

                if (empty($chosenOpts)) {
                    if ($obligatoria) {
                        $errores[] = [
                            'pregunta_id' => $pid,
                            'mensaje' => "Por favor responda la pregunta «{$etiquetaHumana}».",
                        ];
                    }
                    continue;
                }

                $normalizadas[] = [
                    'pregunta_id' => $pid,
                    'opciones' => $chosenOpts,
                    'texto' => '',
                    'grilla' => [],
                    'lista' => null,
                ];

                foreach ($chosenOpts as $oid) {
                    $opObj = $validOpts[$oid];
                    $filasRespuestasPlanas[] = [
                        'tbl_pregunta_id' => $pid,
                        'tbl_opcion_respuesta_id' => $oid,
                        'tbl_grilla_fila_id' => null,
                        'tbl_grilla_columna_id' => null,
                        'tbl_grilla_columna_opcion_id' => null,
                        'valor_numerico' => $opObj['valor_numerico'] ?? null,
                        'respuesta_texto' => null,
                        'texto_snapshot' => $etiquetaHumana . ': ' . $opObj['texto'],
                    ];
                }
            } else {
                $txt = trim((string)($rItem['texto'] ?? ''));
                if ($txt === '') {
                    if ($obligatoria) {
                        $errores[] = [
                            'pregunta_id' => $pid,
                            'mensaje' => "Por favor escriba su respuesta en «{$etiquetaHumana}».",
                        ];
                    }
                    continue;
                }

                $normalizadas[] = [
                    'pregunta_id' => $pid,
                    'opciones' => [],
                    'texto' => $txt,
                    'grilla' => [],
                    'lista' => null,
                ];

                $filasRespuestasPlanas[] = [
                    'tbl_pregunta_id' => $pid,
                    'tbl_opcion_respuesta_id' => null,
                    'tbl_grilla_fila_id' => null,
                    'tbl_grilla_columna_id' => null,
                    'tbl_grilla_columna_opcion_id' => null,
                    'valor_numerico' => self::valorNumericoTexto($txt),
                    'respuesta_texto' => $txt,
                    'texto_snapshot' => $etiquetaHumana . ': ' . $txt,
                ];
            }
        }

        $estadoIntento = $eval['terminado_por_filtro'] ? 'terminado_filtro' : 'completo';
        $validoParaPago = $eval['terminado_por_filtro'] ? 0 : 1;

        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'estado_intento' => $estadoIntento,
            'valido_para_pago' => $validoParaPago,
            'terminado_en_pregunta_id' => $eval['terminado_en_pregunta_id'],
            'terminado_opcion_id' => $eval['terminado_opcion_id'],
            'mensaje_cierre' => $eval['mensaje_cierre'],
            'respuestas_normalizadas' => $normalizadas,
            'filas_bd' => $filasRespuestasPlanas,
            'evaluacion' => $eval,
        ];
    }

    public static function validarConfiguracion(array $def): array
    {
        $alertas = [];
        $preguntas = $def['preguntas'] ?? [];
        $plantillas = (array)($def['plantillas'] ?? []);

        $ordenPorId = [];
        $pregPorId = [];
        foreach ($preguntas as $idx => $p) {
            $pid = (int)$p['id'];
            $ordenPorId[$pid] = $idx + 1;
            $pregPorId[$pid] = $p;
        }

        $tieneEdad = false;
        foreach ($preguntas as $p) {
            if (($p['copiar_a_votante_campo'] ?? '') === 'rango_edad') {
                $tieneEdad = true;
                break;
            }
        }
        if (!$tieneEdad) {
            $alertas[] = [
                'tipo' => 'error',
                'codigo' => 'FALTA_EDAD',
                'pregunta_id' => 0,
                'mensaje' => 'Esta encuesta no tiene la pregunta de edad. Agréguela y márquela como «Rango de edad».',
            ];
        }

        foreach ($preguntas as $idx => $p) {
            $pid = (int)$p['id'];
            $pos = $idx + 1;
            $label = trim((string)(($p['numeral'] ? ($p['numeral'] . ' ') : '') . $p['texto_pregunta']));

            if (preg_match('/XXX/i', (string)$p['texto_pregunta']) || preg_match('/XXX/i', (string)($p['libreto_encuestador'] ?? ''))) {
                $alertas[] = [
                    'tipo' => 'error',
                    'codigo' => 'TEXTO_PENDIENTE_XXX',
                    'pregunta_id' => $pid,
                    'mensaje' => "La pregunta «{$label}» todavía tiene texto pendiente por completar («XXX»).",
                ];
            }

            if (!empty($p['visible_si_pregunta_id'])) {
                $depId = (int)$p['visible_si_pregunta_id'];
                if (!isset($ordenPorId[$depId])) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'CONDICION_PREGUNTA_INEXISTENTE',
                        'pregunta_id' => $pid,
                        'mensaje' => "La condición de «{$label}» apunta a una pregunta que no existe o está deshabilitada.",
                    ];
                } elseif ($ordenPorId[$depId] >= $pos) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'CONDICION_HACIA_ADELANTE',
                        'pregunta_id' => $pid,
                        'mensaje' => "La pregunta condicional «{$label}» depende de una pregunta que aparece después en el cuestionario.",
                    ];
                }
            }

            foreach ($p['opciones'] ?? [] as $op) {
                if (preg_match('/XXX/i', (string)$op['texto'])) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'TEXTO_PENDIENTE_XXX',
                        'pregunta_id' => $pid,
                        'mensaje' => "Una opción de «{$label}» todavía contiene «XXX».",
                    ];
                }
                if (($op['accion'] ?? '') === 'saltar') {
                    $destId = (int)($op['salto_destino_pregunta_id'] ?? 0);
                    if ($destId <= 0 || !isset($ordenPorId[$destId])) {
                        $alertas[] = [
                            'tipo' => 'error',
                            'codigo' => 'SALTO_DESTINO_INVALIDO',
                            'pregunta_id' => $pid,
                            'mensaje' => "La opción «{$op['texto']}» de «{$label}» tiene un salto sin pregunta destino válida.",
                        ];
                    } elseif ($ordenPorId[$destId] <= $pos) {
                        $alertas[] = [
                            'tipo' => 'error',
                            'codigo' => 'SALTO_HACIA_ATRAS',
                            'pregunta_id' => $pid,
                            'mensaje' => "La opción «{$op['texto']}» de «{$label}» intenta saltar hacia atrás. Los saltos deben ir hacia preguntas posteriores.",
                        ];
                    }
                }
            }

            if ($p['tipo_pregunta'] === 'Grilla') {
                $plId = (int)($p['tbl_grilla_plantilla_id'] ?? 0);
                $vars = (array)($p['variantes_municipio'] ?? []);
                $idsTabla = $plId > 0 ? [$plId] : [];
                foreach ($vars as $plVar) {
                    $idsTabla[] = (int)$plVar;
                }
                foreach ($idsTabla as $idTabla) {
                    if ($idTabla > 0 && !isset($plantillas[$idTabla]) && !isset($plantillas[(string)$idTabla])) {
                        $alertas[] = [
                            'tipo' => 'error',
                            'codigo' => 'TABLA_NO_DISPONIBLE',
                            'pregunta_id' => $pid,
                            'mensaje' => "La pregunta «{$label}» usa una tabla de candidatos que fue eliminada. Elija otra tabla.",
                        ];
                        break;
                    }
                }
                if ($plId <= 0 && empty($vars)) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'GRILLA_SIN_PLANTILLA',
                        'pregunta_id' => $pid,
                        'mensaje' => "La pregunta «{$label}» es de tipo Tabla de candidatos pero no tiene ninguna tabla asociada.",
                    ];
                }
            }

            if ($p['tipo_pregunta'] === 'Lista_desde_grilla') {
                $origPid = (int)($p['lista_origen_pregunta_id'] ?? 0);
                if ($origPid <= 0 || !isset($ordenPorId[$origPid])) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'LISTA_SIN_ORIGEN',
                        'pregunta_id' => $pid,
                        'mensaje' => "La pregunta de lista «{$label}» no indica de qué Tabla de candidatos provienen sus opciones.",
                    ];
                } elseif ($ordenPorId[$origPid] >= $pos) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'LISTA_ORIGEN_POSTERIOR',
                        'pregunta_id' => $pid,
                        'mensaje' => "La pregunta «{$label}» debe ir después de la Tabla de candidatos de donde toma los nombres.",
                    ];
                }
            }
        }

        foreach ($plantillas as $pl) {
            $pl = (array)$pl;
            $textos = [];
            foreach ($pl['filas'] ?? [] as $fl) {
                $textos[] = (string)($fl['etiqueta'] ?? '');
            }
            foreach ($pl['columnas'] ?? [] as $cl) {
                $textos[] = (string)($cl['titulo'] ?? '');
                $textos[] = (string)($cl['libreto_encuestador'] ?? '');
                foreach ($cl['opciones'] ?? [] as $co) {
                    $textos[] = (string)($co['texto'] ?? '');
                }
            }
            foreach ($textos as $tx) {
                if (preg_match('/XXX/i', $tx)) {
                    $alertas[] = [
                        'tipo' => 'error',
                        'codigo' => 'TEXTO_PENDIENTE_XXX',
                        'pregunta_id' => 0,
                        'mensaje' => 'La tabla «' . trim((string)($pl['nombre'] ?? '')) . '» todavía tiene texto pendiente por completar («' . trim($tx) . '»).',
                    ];
                }
            }
        }

        $total = count($preguntas);
        foreach ($preguntas as $idx => $p) {
            $opciones = $p['opciones'] ?? [];
            $pos = $idx + 1;
            if (empty($opciones) || empty($p['obligatoria']) || !empty($p['visible_si_pregunta_id'])
                || in_array($p['tipo_pregunta'], ['Grilla', 'Lista_desde_grilla'], true)
                || (int)($p['limite_respuesta_multiple'] ?? 1) > 1) {
                continue;
            }
            $primeraAlcanzable = PHP_INT_MAX;
            foreach ($opciones as $op) {
                $accion = $op['accion'] ?? 'continuar';
                if ($accion === 'terminar') {
                    continue;
                }
                if ($accion === 'saltar') {
                    $destId = (int)($op['salto_destino_pregunta_id'] ?? 0);
                    $primeraAlcanzable = min($primeraAlcanzable, $ordenPorId[$destId] ?? ($pos + 1));
                } else {
                    $primeraAlcanzable = min($primeraAlcanzable, $pos + 1);
                }
            }
            if ($primeraAlcanzable <= $pos + 1) {
                continue;
            }
            $hasta = min($primeraAlcanzable - 1, $total);
            for ($q = $pos + 1; $q <= $hasta; $q++) {
                $qp = $preguntas[$q - 1];
                $alertas[] = [
                    'tipo' => 'error',
                    'codigo' => 'PREGUNTA_INALCANZABLE',
                    'pregunta_id' => (int)$qp['id'],
                    'mensaje' => 'La pregunta «' . trim((string)(($qp['numeral'] ? ($qp['numeral'] . ' ') : '') . $qp['texto_pregunta'])) . '» nunca se mostrará: todas las respuestas de «' . trim((string)(($p['numeral'] ? ($p['numeral'] . ' ') : '') . $p['texto_pregunta'])) . '» saltan más allá o terminan la encuesta.',
                ];
            }
        }

        return [
            'valido' => empty($alertas),
            'alertas' => $alertas,
            'errores' => $alertas,
        ];
    }
}
