<?php

class Pregunta
{
    public function __construct()
    {
    }

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $ids = isset($rqst['ids']) ? $rqst['ids'] : '';
        $tbl_ficha_tecnica_encuesta_id = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? intval($rqst['tbl_ficha_tecnica_encuesta_id']) : 0;
        $incluir_eliminadas = !empty($rqst['incluir_eliminadas']);

        $db = new DbConection();
        $pdo = $db->openConect();

        $tblPreguntas = $db->getTable('tbl_preguntas');
        $tblOpciones = $db->getTable('tbl_opciones_respuesta');
        $tblFichas = $db->getTable('tbl_ficha_tecnica_encuestas');
        $tblEspacio = $db->getTable('tbl_espacio_geografico');
        $tblRespuestas = $db->getTable('tbl_cuestionario_respuestas');
        $tblExclusiones = $db->getTable('tbl_preguntas_lista_exclusiones');
        $tblVariantes = $db->getTable('tbl_preguntas_grilla_variantes');

        $q = "SELECT
                p.id,
                p.tbl_ficha_tecnica_encuesta_id,
                p.texto_pregunta,
                p.enunciado_pregunta,
                p.numeral,
                p.texto_adicional,
                p.capitulo,
                p.tipo_pregunta,
                p.orden,
                p.limite_respuesta_multiple,
                p.habilitado,
                p.visualizacion,
                p.obligatoria,
                p.es_filtro,
                p.libreto_encuestador,
                p.nota_tabulacion,
                p.tbl_grilla_plantilla_id,
                p.visible_si_pregunta_id,
                p.visible_si_opcion_id,
                p.lista_origen_pregunta_id,
                p.lista_origen_columna_id,
                p.lista_origen_opcion_id,
                p.si_lista_vacia,
                p.es_variable_cruce,
                p.etiqueta_cruce,
                p.copiar_a_votante_campo,
                p.rol_resultado,
                p.eliminado,
                p.tbl_usuario_id,
                p.dtcreate,
                e.temas_concretos,
                e.realizada_por_o_encomendada_por,
                e.tbl_espacio_geografico_id,
                eg.tipo_estudio AS espacio_geografico_tipo_estudio,
                eg.observaciones AS espacio_geografico_observaciones,
                (SELECT COUNT(*) FROM {$tblRespuestas} r WHERE r.tbl_pregunta_id = p.id) AS total_respuestas
            FROM {$tblPreguntas} p
                LEFT JOIN {$tblFichas} e ON p.tbl_ficha_tecnica_encuesta_id = e.id
                LEFT JOIN {$tblEspacio} eg ON e.tbl_espacio_geografico_id = eg.id";

        $params = [];
        $conditions = [];

        if (!$incluir_eliminadas) {
            $conditions[] = "p.eliminado = 0";
        }

        if ($id > 0) {
            $conditions[] = "p.id = :id";
            $params[':id'] = $id;
        } elseif (!empty($ids)) {
            $idsArray = array_filter(array_map('intval', explode(',', $ids)), function ($val) {
                return $val > 0;
            });
            if (!empty($idsArray)) {
                $placeholders = [];
                foreach ($idsArray as $index => $val) {
                    $key = ":id_list_" . $index;
                    $placeholders[] = $key;
                    $params[$key] = $val;
                }
                $conditions[] = "p.id IN (" . implode(',', $placeholders) . ")";
            }
        }

        if ($tbl_ficha_tecnica_encuesta_id > 0) {
            $conditions[] = "p.tbl_ficha_tecnica_encuesta_id = :encuesta_id";
            $params[':encuesta_id'] = $tbl_ficha_tecnica_encuesta_id;
        }

        if (!empty($conditions)) {
            $q .= " WHERE " . implode(' AND ', $conditions);
        }

        $q .= " ORDER BY COALESCE(p.orden, 999999) ASC, p.id ASC";

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($arr)) {
                $pIds = array_map(function ($row) {
                    return (int)$row['id'];
                }, $arr);
                $inPlaceholders = implode(',', array_fill(0, count($pIds), '?'));

                $qOpt = "SELECT
                            o.id,
                            o.tbl_pregunta_id,
                            o.texto_opcion,
                            o.orden,
                            o.valor_numerico,
                            o.cuenta_como,
                            o.accion,
                            o.salto_destino_pregunta_id,
                            o.mensaje_terminar,
                            o.es_opcion_fija,
                            o.eliminado
                         FROM {$tblOpciones} o
                         WHERE o.tbl_pregunta_id IN ({$inPlaceholders})
                           AND o.eliminado = 0
                         ORDER BY COALESCE(o.orden, 999999) ASC, o.id ASC";
                $stmtOpt = $pdo->prepare($qOpt);
                $stmtOpt->execute($pIds);
                $opcionesRows = $stmtOpt->fetchAll(PDO::FETCH_ASSOC);

                $opcionesPorPregunta = [];
                foreach ($opcionesRows as $opt) {
                    $pid = (int)$opt['tbl_pregunta_id'];
                    $opcionesPorPregunta[$pid][] = [
                        'id' => (int)$opt['id'],
                        'texto' => $opt['texto_opcion'],
                        'texto_opcion' => $opt['texto_opcion'],
                        'orden' => $opt['orden'] !== null ? (int)$opt['orden'] : null,
                        'valor_numerico' => $opt['valor_numerico'] !== null ? (float)$opt['valor_numerico'] : null,
                        'cuenta_como' => $opt['cuenta_como'] ?? 'ninguno',
                        'accion' => $opt['accion'] ?? 'continuar',
                        'salto_destino_pregunta_id' => $opt['salto_destino_pregunta_id'] !== null ? (int)$opt['salto_destino_pregunta_id'] : null,
                        'mensaje_terminar' => $opt['mensaje_terminar'],
                        'es_opcion_fija' => (int)($opt['es_opcion_fija'] ?? 0)
                    ];
                }

                $stmtExcl = $pdo->prepare("SELECT tbl_pregunta_id, tbl_pregunta_excluida_id FROM {$tblExclusiones} WHERE tbl_pregunta_id IN ({$inPlaceholders}) ORDER BY id ASC");
                $stmtExcl->execute($pIds);
                $exclPorPregunta = [];
                foreach ($stmtExcl->fetchAll(PDO::FETCH_ASSOC) as $ex) {
                    $exclPorPregunta[(int)$ex['tbl_pregunta_id']][] = (int)$ex['tbl_pregunta_excluida_id'];
                }

                $stmtVar = $pdo->prepare("SELECT id, tbl_pregunta_id, codigo_municipio, tbl_grilla_plantilla_id FROM {$tblVariantes} WHERE tbl_pregunta_id IN ({$inPlaceholders}) ORDER BY codigo_municipio ASC");
                $stmtVar->execute($pIds);
                $varPorPregunta = [];
                foreach ($stmtVar->fetchAll(PDO::FETCH_ASSOC) as $vr) {
                    $varPorPregunta[(int)$vr['tbl_pregunta_id']][] = [
                        'id' => (int)$vr['id'],
                        'codigo_municipio' => $vr['codigo_municipio'],
                        'tbl_grilla_plantilla_id' => (int)$vr['tbl_grilla_plantilla_id']
                    ];
                }

                foreach ($arr as &$pregunta) {
                    $pid = (int)$pregunta['id'];
                    $pregunta['opciones'] = $opcionesPorPregunta[$pid] ?? [];
                    $pregunta['exclusiones'] = $exclPorPregunta[$pid] ?? [];
                    $pregunta['variantes_municipio'] = $varPorPregunta[$pid] ?? [];
                    $pregunta['grilla_variantes'] = $pregunta['variantes_municipio'];
                    $pregunta['grilla_por_municipio'] = empty($pregunta['variantes_municipio']) ? 0 : 1;
                    $pregunta['tiene_respuestas'] = ((int)($pregunta['total_respuestas'] ?? 0)) > 0;
                }
                unset($pregunta);
            }

            $arrjson = array('output' => array('valid' => true, 'response' => $arr));
        } catch (PDOException $e) {
            error_log('Pregunta::getAll error: ' . $e->getMessage());
            $arrjson = Util::error_general('Error al obtener los datos de preguntas.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function sincronizarOpcionesPregunta(PDO $pdo, DbConection $db, int $preguntaId, array $opcionesEntrada, bool $tieneRespuestas = false): void
    {
        $tblOpciones = $db->getTable('tbl_opciones_respuesta');
        $stmtActuales = $pdo->prepare("SELECT id, texto_opcion, orden FROM {$tblOpciones} WHERE tbl_pregunta_id = :pid AND eliminado = 0 ORDER BY COALESCE(orden, 999999) ASC, id ASC");
        $stmtActuales->execute([':pid' => $preguntaId]);
        $actuales = $stmtActuales->fetchAll(PDO::FETCH_ASSOC);

        $mapaActualPorId = [];
        $listaActualOrdenada = [];
        foreach ($actuales as $row) {
            $oid = (int)$row['id'];
            $mapaActualPorId[$oid] = $row;
            $listaActualOrdenada[] = $row;
        }

        $idsConservados = [];
        $ordenCounter = 1;
        $idsExplicitos = [];
        foreach ($opcionesEntrada as $itemExp) {
            if (is_array($itemExp) && !empty($itemExp['id'])) {
                $idsExplicitos[(int)$itemExp['id']] = true;
            }
        }
        $idPorPosicion = function (int $pos) use ($listaActualOrdenada, $idsExplicitos, &$idsConservados): int {
            if (!empty($idsExplicitos) || !isset($listaActualOrdenada[$pos])) {
                return 0;
            }
            $cand = (int)$listaActualOrdenada[$pos]['id'];
            return isset($idsConservados[$cand]) ? 0 : $cand;
        };

        $stmtUpdate = $pdo->prepare("
            UPDATE {$tblOpciones}
            SET texto_opcion = :texto,
                orden = :orden,
                valor_numerico = :valor_numerico,
                cuenta_como = COALESCE(:cuenta_como, cuenta_como),
                accion = COALESCE(:accion, accion),
                salto_destino_pregunta_id = :salto_destino,
                mensaje_terminar = :mensaje_terminar,
                es_opcion_fija = COALESCE(:es_opcion_fija, es_opcion_fija),
                eliminado = 0
            WHERE id = :id AND tbl_pregunta_id = :pid
        ");

        $stmtInsert = $pdo->prepare("
            INSERT INTO {$tblOpciones}
            (tbl_pregunta_id, texto_opcion, orden, valor_numerico, cuenta_como, accion, salto_destino_pregunta_id, mensaje_terminar, es_opcion_fija, eliminado, dtcreate)
            VALUES (:pid, :texto, :orden, :valor_numerico, :cuenta_como, :accion, :salto_destino, :mensaje_terminar, :es_opcion_fija, 0, NOW())
        ");

        foreach ($opcionesEntrada as $idx => $item) {
            $optId = 0;
            $texto = '';
            $valorNum = null;
            $cuentaComo = null;
            $accion = null;
            $saltoDestino = null;
            $msgTerminar = null;
            $esFija = null;
            $tieneReglasExplicitas = false;

            if (is_array($item)) {
                $optId = isset($item['id']) ? (int)$item['id'] : 0;
                $texto = trim((string)($item['texto_opcion'] ?? $item['texto'] ?? ''));
                if (array_key_exists('valor_numerico', $item)) {
                    $valorNum = ($item['valor_numerico'] !== '' && $item['valor_numerico'] !== null) ? (float)$item['valor_numerico'] : null;
                    $tieneReglasExplicitas = true;
                }
                if (!empty($item['cuenta_como']) && in_array((string)$item['cuenta_como'], self::CUENTA_COMO, true)) {
                    $cuentaComo = (string)$item['cuenta_como'];
                    $tieneReglasExplicitas = true;
                }
                if (!empty($item['accion']) && in_array((string)$item['accion'], ['continuar', 'saltar', 'terminar'], true)) {
                    $accion = (string)$item['accion'];
                    $tieneReglasExplicitas = true;
                }
                if (array_key_exists('salto_destino_pregunta_id', $item)) {
                    $saltoDestino = !empty($item['salto_destino_pregunta_id']) ? (int)$item['salto_destino_pregunta_id'] : null;
                    $tieneReglasExplicitas = true;
                }
                if (array_key_exists('mensaje_terminar', $item)) {
                    $msgTerminar = $item['mensaje_terminar'] !== null && trim((string)$item['mensaje_terminar']) !== '' ? trim((string)$item['mensaje_terminar']) : null;
                    $tieneReglasExplicitas = true;
                }
                if (array_key_exists('es_opcion_fija', $item)) {
                    $esFija = (int)$item['es_opcion_fija'] ? 1 : 0;
                    $tieneReglasExplicitas = true;
                }
                if ($optId <= 0) {
                    $optId = $idPorPosicion((int)$idx);
                }
            } else {
                $texto = trim((string)$item);
                $optId = $idPorPosicion((int)$idx);
            }

            if ($texto === '') {
                continue;
            }

            if ($optId > 0 && isset($mapaActualPorId[$optId])) {
                if (!$tieneReglasExplicitas) {
                    $stmtUpdSimple = $pdo->prepare("
                        UPDATE {$tblOpciones}
                        SET texto_opcion = :texto,
                            orden = :orden,
                            valor_numerico = COALESCE(:valor_numerico, valor_numerico),
                            cuenta_como = COALESCE(:cuenta_como, cuenta_como),
                            es_opcion_fija = COALESCE(:es_opcion_fija, es_opcion_fija),
                            eliminado = 0
                        WHERE id = :id AND tbl_pregunta_id = :pid
                    ");
                    $stmtUpdSimple->execute([
                        ':texto' => $texto,
                        ':orden' => $ordenCounter++,
                        ':valor_numerico' => $valorNum,
                        ':cuenta_como' => $cuentaComo,
                        ':es_opcion_fija' => $esFija,
                        ':id' => $optId,
                        ':pid' => $preguntaId
                    ]);
                } else {
                    $stmtUpdate->execute([
                        ':texto' => $texto,
                        ':orden' => $ordenCounter++,
                        ':valor_numerico' => $valorNum,
                        ':cuenta_como' => $cuentaComo,
                        ':accion' => $accion,
                        ':salto_destino' => $saltoDestino,
                        ':mensaje_terminar' => $msgTerminar,
                        ':es_opcion_fija' => $esFija,
                        ':id' => $optId,
                        ':pid' => $preguntaId
                    ]);
                }
                $idsConservados[$optId] = true;
            } else {
                if ($tieneRespuestas) {
                    throw new RuntimeException('Esta pregunta ya tiene respuestas registradas; solo puede editar los textos de sus opciones existentes, no agregar ni quitar opciones.');
                }
                $stmtInsert->execute([
                    ':pid' => $preguntaId,
                    ':texto' => $texto,
                    ':orden' => $ordenCounter++,
                    ':valor_numerico' => $valorNum,
                    ':cuenta_como' => $cuentaComo ?? 'ninguno',
                    ':accion' => $accion ?? 'continuar',
                    ':salto_destino' => $saltoDestino,
                    ':mensaje_terminar' => $msgTerminar,
                    ':es_opcion_fija' => $esFija ?? 0
                ]);
                $nuevoId = (int)$pdo->lastInsertId();
                $idsConservados[$nuevoId] = true;
            }
        }

        foreach ($mapaActualPorId as $oid => $row) {
            if (!isset($idsConservados[$oid])) {
                if ($tieneRespuestas) {
                    throw new RuntimeException('Esta pregunta ya tiene respuestas registradas; no se pueden eliminar opciones existentes.');
                }
                $pdo->prepare("UPDATE {$tblOpciones} SET eliminado = 1 WHERE id = :id AND tbl_pregunta_id = :pid")
                    ->execute([':id' => $oid, ':pid' => $preguntaId]);
            }
        }
    }

    private static function sincronizarExclusionesYVariantes(PDO $pdo, DbConection $db, int $preguntaId, $exclusionesEntrada, $variantesEntrada): void
    {
        $tblExcl = $db->getTable('tbl_preguntas_lista_exclusiones');
        $tblVar = $db->getTable('tbl_preguntas_grilla_variantes');

        if ($exclusionesEntrada !== null) {
            if (is_string($exclusionesEntrada)) {
                $decoded = json_decode($exclusionesEntrada, true);
                if (is_array($decoded)) {
                    $exclusionesEntrada = $decoded;
                } else {
                    $exclusionesEntrada = array_filter(array_map('intval', explode(',', $exclusionesEntrada)));
                }
            }
            if (is_array($exclusionesEntrada)) {
                $pdo->prepare("DELETE FROM {$tblExcl} WHERE tbl_pregunta_id = :pid")->execute([':pid' => $preguntaId]);
                $stInsEx = $pdo->prepare("INSERT INTO {$tblExcl} (tbl_pregunta_id, tbl_pregunta_excluida_id) VALUES (:pid, :exid)");
                $vistos = [];
                foreach ($exclusionesEntrada as $exId) {
                    $exIdInt = (int)$exId;
                    if ($exIdInt > 0 && $exIdInt !== $preguntaId && !isset($vistos[$exIdInt])) {
                        $vistos[$exIdInt] = true;
                        $stInsEx->execute([':pid' => $preguntaId, ':exid' => $exIdInt]);
                    }
                }
            }
        }

        if ($variantesEntrada !== null) {
            if (is_string($variantesEntrada)) {
                $decoded = json_decode($variantesEntrada, true);
                $variantesEntrada = is_array($decoded) ? $decoded : [];
            }
            if (is_array($variantesEntrada)) {
                $pdo->prepare("DELETE FROM {$tblVar} WHERE tbl_pregunta_id = :pid")->execute([':pid' => $preguntaId]);
                $stInsVar = $pdo->prepare("INSERT INTO {$tblVar} (tbl_pregunta_id, codigo_municipio, tbl_grilla_plantilla_id, dtcreate) VALUES (:pid, :mun, :plid, NOW())");
                $vistosMun = [];
                foreach ($variantesEntrada as $k => $v) {
                    $codMun = '';
                    $plId = 0;
                    if (is_array($v)) {
                        $codMun = trim((string)($v['codigo_municipio'] ?? ''));
                        $plId = (int)($v['tbl_grilla_plantilla_id'] ?? 0);
                    } else {
                        $codMun = trim((string)$k);
                        $plId = (int)$v;
                    }
                    if ($codMun !== '' && $plId > 0 && !isset($vistosMun[$codMun])) {
                        $vistosMun[$codMun] = true;
                        $stInsVar->execute([':pid' => $preguntaId, ':mun' => $codMun, ':plid' => $plId]);
                    }
                }
            }
        }
    }

    const ROLES_RESULTADO = ['ninguno', 'conocimiento', 'imagen', 'intencion', 'ideologia', 'asociacion_positiva', 'asociacion_negativa', 'escala', 'otro'];
    const CUENTA_COMO = ['ninguno', 'si', 'no', 'favorable', 'desfavorable'];

    private static function normalizarRolResultado($valor): string
    {
        $v = trim((string)$valor);
        return in_array($v, self::ROLES_RESULTADO, true) ? $v : 'ninguno';
    }

    private static function normalizarCampoVotante($valor): ?string
    {
        require_once __DIR__ . '/Votantes.php';
        $v = trim((string)$valor);
        return array_key_exists($v, Votantes::catalogoCamposEquivalencia()) ? $v : null;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tbl_ficha_tecnica_encuesta_id = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? intval($rqst['tbl_ficha_tecnica_encuesta_id']) : 0;
        $texto_pregunta = isset($rqst['texto_pregunta']) ? trim($rqst['texto_pregunta']) : '';
        $enunciado_pregunta = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : null;
        $numeral = array_key_exists('numeral', $rqst) ? (trim((string)$rqst['numeral']) !== '' ? trim((string)$rqst['numeral']) : null) : null;
        $texto_adicional = array_key_exists('texto_adicional', $rqst) ? (trim((string)$rqst['texto_adicional']) !== '' ? trim((string)$rqst['texto_adicional']) : null) : null;
        $capitulo = array_key_exists('capitulo', $rqst) ? (trim((string)$rqst['capitulo']) !== '' ? trim((string)$rqst['capitulo']) : null) : null;
        $tipo_pregunta = isset($rqst['tipo_pregunta']) ? trim($rqst['tipo_pregunta']) : null;
        $orden = isset($rqst['orden']) && $rqst['orden'] !== '' ? intval($rqst['orden']) : null;
        $limite_respuesta_multiple = isset($rqst['limite_respuesta_multiple']) && $rqst['limite_respuesta_multiple'] !== '' ? intval($rqst['limite_respuesta_multiple']) : 1;
        $habilitado = isset($rqst['habilitado']) ? trim($rqst['habilitado']) : 'si';
        $visualizacion = isset($rqst['visualizacion']) ? trim($rqst['visualizacion']) : 'si';
        $tbl_usuario_id = isset($_SESSION['session_user']['id']) ? intval($_SESSION['session_user']['id']) : 1;

        $obligatoria = isset($rqst['obligatoria']) ? ((int)$rqst['obligatoria'] ? 1 : 0) : 1;
        $es_filtro = isset($rqst['es_filtro']) ? ((int)$rqst['es_filtro'] ? 1 : 0) : 0;
        $libreto_encuestador = array_key_exists('libreto_encuestador', $rqst) ? (trim((string)$rqst['libreto_encuestador']) !== '' ? trim((string)$rqst['libreto_encuestador']) : null) : null;
        $nota_tabulacion = array_key_exists('nota_tabulacion', $rqst) ? (trim((string)$rqst['nota_tabulacion']) !== '' ? trim((string)$rqst['nota_tabulacion']) : null) : null;
        $tbl_grilla_plantilla_id = !empty($rqst['tbl_grilla_plantilla_id']) ? (int)$rqst['tbl_grilla_plantilla_id'] : null;
        $visible_si_pregunta_id = !empty($rqst['visible_si_pregunta_id']) ? (int)$rqst['visible_si_pregunta_id'] : null;
        $visible_si_opcion_id = !empty($rqst['visible_si_opcion_id']) ? (int)$rqst['visible_si_opcion_id'] : null;
        $lista_origen_pregunta_id = !empty($rqst['lista_origen_pregunta_id']) ? (int)$rqst['lista_origen_pregunta_id'] : null;
        $lista_origen_columna_id = !empty($rqst['lista_origen_columna_id']) ? (int)$rqst['lista_origen_columna_id'] : null;
        $lista_origen_opcion_id = !empty($rqst['lista_origen_opcion_id']) ? (int)$rqst['lista_origen_opcion_id'] : null;
        $si_lista_vacia = (!empty($rqst['si_lista_vacia']) && in_array($rqst['si_lista_vacia'], ['omitir', 'terminar'], true)) ? $rqst['si_lista_vacia'] : 'omitir';
        $es_variable_cruce = isset($rqst['es_variable_cruce']) ? ((int)$rqst['es_variable_cruce'] ? 1 : 0) : 0;
        $etiqueta_cruce = !empty($rqst['etiqueta_cruce']) ? trim((string)$rqst['etiqueta_cruce']) : null;
        $copiar_a_votante_campo = self::normalizarCampoVotante($rqst['copiar_a_votante_campo'] ?? null);
        $rol_resultado = self::normalizarRolResultado($rqst['rol_resultado'] ?? null);

        $opcionesRaw = $rqst['opciones'] ?? [];
        if (is_string($opcionesRaw)) {
            $dec = json_decode($opcionesRaw, true);
            $opciones_respuesta = is_array($dec) ? $dec : [];
        } elseif (is_array($opcionesRaw)) {
            $opciones_respuesta = $opcionesRaw;
        } else {
            $opciones_respuesta = [];
        }

        if ($es_filtro === 0) {
            foreach ($opciones_respuesta as $opItem) {
                if (is_array($opItem) && ($opItem['accion'] ?? '') === 'terminar') {
                    $es_filtro = 1;
                    break;
                }
            }
        }

        $esTipoSinOpcionesPropias = in_array($tipo_pregunta, ['Grilla', 'Lista_desde_grilla', 'Texto Libre', 'Abierta'], true);

        if ($tbl_ficha_tecnica_encuesta_id == 0) {
            return Util::error_missing_data_description('La encuesta es requerida.');
        }
        if (empty($texto_pregunta)) {
            return Util::error_missing_data_description('El texto de la pregunta es requerido.');
        }
        if (empty($opciones_respuesta) && !$esTipoSinOpcionesPropias) {
            return Util::error_missing_data_description('Se requiere al menos una opción de respuesta.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $check_q = "SELECT id FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id = :id LIMIT 1";
            $check_stmt = $pdo->prepare($check_q);
            $check_stmt->bindValue(':id', $tbl_ficha_tecnica_encuesta_id, PDO::PARAM_INT);
            $check_stmt->execute();

            if ($check_stmt->rowCount() == 0) {
                $db->closeConect();
                return Util::error_missing_data_description('La encuesta seleccionada no existe en la base de datos. Por favor, seleccione una encuesta válida.');
            }
        } catch (PDOException $e) {
            $db->closeConect();
            error_log('Pregunta::save check ficha error: ' . $e->getMessage());
            return Util::error_general('Error al validar la encuesta.');
        }

        $pdo->beginTransaction();

        try {
            $pregunta_id = $id;
            $tieneRespuestas = false;

            if ($id > 0) {
                $stAct = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_preguntas') . " WHERE id = :id LIMIT 1");
                $stAct->execute([':id' => $id]);
                $pregActual = $stAct->fetch(PDO::FETCH_ASSOC);
                if (!$pregActual) {
                    throw new RuntimeException('La pregunta que intenta editar no existe.');
                }

                $stResp = $pdo->prepare("SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_respuestas') . " WHERE tbl_pregunta_id = :id");
                $stResp->execute([':id' => $id]);
                $tieneRespuestas = ((int)$stResp->fetchColumn()) > 0;

                if ($tieneRespuestas && $tipo_pregunta !== null && $pregActual['tipo_pregunta'] !== $tipo_pregunta) {
                    throw new RuntimeException('Esta pregunta ya tiene respuestas registradas; no se puede cambiar su tipo de pregunta.');
                }

                if ($orden === null) {
                    $orden = $pregActual['orden'] !== null ? (int)$pregActual['orden'] : null;
                }
                if (!array_key_exists('numeral', $rqst)) {
                    $numeral = $pregActual['numeral'];
                }
                if (!array_key_exists('texto_adicional', $rqst)) {
                    $texto_adicional = $pregActual['texto_adicional'];
                }
                if (!array_key_exists('capitulo', $rqst)) {
                    $capitulo = $pregActual['capitulo'];
                }
                if (!array_key_exists('obligatoria', $rqst)) {
                    $obligatoria = (int)($pregActual['obligatoria'] ?? 1);
                }
                if (!array_key_exists('es_filtro', $rqst) && $es_filtro === 0) {
                    $es_filtro = (int)($pregActual['es_filtro'] ?? 0);
                }
                if (!array_key_exists('libreto_encuestador', $rqst)) {
                    $libreto_encuestador = $pregActual['libreto_encuestador'];
                }
                if (!array_key_exists('nota_tabulacion', $rqst)) {
                    $nota_tabulacion = $pregActual['nota_tabulacion'];
                }
                if (!array_key_exists('tbl_grilla_plantilla_id', $rqst)) {
                    $tbl_grilla_plantilla_id = $pregActual['tbl_grilla_plantilla_id'] !== null ? (int)$pregActual['tbl_grilla_plantilla_id'] : null;
                }
                if (!array_key_exists('visible_si_pregunta_id', $rqst)) {
                    $visible_si_pregunta_id = $pregActual['visible_si_pregunta_id'] !== null ? (int)$pregActual['visible_si_pregunta_id'] : null;
                }
                if (!array_key_exists('visible_si_opcion_id', $rqst)) {
                    $visible_si_opcion_id = $pregActual['visible_si_opcion_id'] !== null ? (int)$pregActual['visible_si_opcion_id'] : null;
                }
                if (!array_key_exists('lista_origen_pregunta_id', $rqst)) {
                    $lista_origen_pregunta_id = $pregActual['lista_origen_pregunta_id'] !== null ? (int)$pregActual['lista_origen_pregunta_id'] : null;
                }
                if (!array_key_exists('lista_origen_columna_id', $rqst)) {
                    $lista_origen_columna_id = $pregActual['lista_origen_columna_id'] !== null ? (int)$pregActual['lista_origen_columna_id'] : null;
                }
                if (!array_key_exists('lista_origen_opcion_id', $rqst)) {
                    $lista_origen_opcion_id = $pregActual['lista_origen_opcion_id'] !== null ? (int)$pregActual['lista_origen_opcion_id'] : null;
                }
                if (!array_key_exists('si_lista_vacia', $rqst)) {
                    $si_lista_vacia = $pregActual['si_lista_vacia'] ?? 'omitir';
                }
                if (!array_key_exists('es_variable_cruce', $rqst)) {
                    $es_variable_cruce = (int)($pregActual['es_variable_cruce'] ?? 0);
                }
                if (!array_key_exists('etiqueta_cruce', $rqst)) {
                    $etiqueta_cruce = $pregActual['etiqueta_cruce'];
                }
                if (!array_key_exists('copiar_a_votante_campo', $rqst)) {
                    $copiar_a_votante_campo = $pregActual['copiar_a_votante_campo'];
                }
                if (!array_key_exists('rol_resultado', $rqst)) {
                    $rol_resultado = $pregActual['rol_resultado'] ?? 'ninguno';
                }

                $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                      SET tbl_ficha_tecnica_encuesta_id = :tbl_ficha_tecnica_encuesta_id,
                          texto_pregunta = :texto_pregunta,
                          enunciado_pregunta = :enunciado_pregunta,
                          numeral = :numeral,
                          texto_adicional = :texto_adicional,
                          capitulo = :capitulo,
                          tipo_pregunta = :tipo_pregunta,
                          orden = :orden,
                          tbl_usuario_id = :tbl_usuario_id,
                          limite_respuesta_multiple = :limite_respuesta_multiple,
                          habilitado = :habilitado,
                          visualizacion = :visualizacion,
                          obligatoria = :obligatoria,
                          es_filtro = :es_filtro,
                          libreto_encuestador = :libreto_encuestador,
                          nota_tabulacion = :nota_tabulacion,
                          tbl_grilla_plantilla_id = :tbl_grilla_plantilla_id,
                          visible_si_pregunta_id = :visible_si_pregunta_id,
                          visible_si_opcion_id = :visible_si_opcion_id,
                          lista_origen_pregunta_id = :lista_origen_pregunta_id,
                          lista_origen_columna_id = :lista_origen_columna_id,
                          lista_origen_opcion_id = :lista_origen_opcion_id,
                          si_lista_vacia = :si_lista_vacia,
                          es_variable_cruce = :es_variable_cruce,
                          etiqueta_cruce = :etiqueta_cruce,
                          copiar_a_votante_campo = :copiar_a_votante_campo,
                          rol_resultado = :rol_resultado
                      WHERE id = :id";
                $stmt = $pdo->prepare($q);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            } else {
                if ($orden === null || $orden <= 0) {
                    $stMax = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) + 1 FROM " . $db->getTable('tbl_preguntas') . " WHERE tbl_ficha_tecnica_encuesta_id = :fid AND eliminado = 0");
                    $stMax->execute([':fid' => $tbl_ficha_tecnica_encuesta_id]);
                    $orden = (int)$stMax->fetchColumn();
                }

                $q = "INSERT INTO " . $db->getTable('tbl_preguntas') . "
                      (tbl_ficha_tecnica_encuesta_id, texto_pregunta, enunciado_pregunta, numeral, texto_adicional, capitulo,
                       tipo_pregunta, orden, tbl_usuario_id, dtcreate, limite_respuesta_multiple, habilitado, visualizacion,
                       obligatoria, es_filtro, libreto_encuestador, nota_tabulacion, tbl_grilla_plantilla_id,
                       visible_si_pregunta_id, visible_si_opcion_id, lista_origen_pregunta_id, lista_origen_columna_id, lista_origen_opcion_id,
                       si_lista_vacia, es_variable_cruce, etiqueta_cruce, copiar_a_votante_campo, rol_resultado)
                      VALUES (:tbl_ficha_tecnica_encuesta_id, :texto_pregunta, :enunciado_pregunta, :numeral, :texto_adicional, :capitulo,
                              :tipo_pregunta, :orden, :tbl_usuario_id, NOW(), :limite_respuesta_multiple, :habilitado, :visualizacion,
                              :obligatoria, :es_filtro, :libreto_encuestador, :nota_tabulacion, :tbl_grilla_plantilla_id,
                              :visible_si_pregunta_id, :visible_si_opcion_id, :lista_origen_pregunta_id, :lista_origen_columna_id, :lista_origen_opcion_id,
                              :si_lista_vacia, :es_variable_cruce, :etiqueta_cruce, :copiar_a_votante_campo, :rol_resultado)";
                $stmt = $pdo->prepare($q);
            }

            $stmt->bindValue(':tbl_ficha_tecnica_encuesta_id', $tbl_ficha_tecnica_encuesta_id, PDO::PARAM_INT);
            $stmt->bindValue(':texto_pregunta', $texto_pregunta, PDO::PARAM_STR);
            $stmt->bindValue(':enunciado_pregunta', $enunciado_pregunta, is_null($enunciado_pregunta) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':numeral', $numeral, is_null($numeral) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':texto_adicional', $texto_adicional, is_null($texto_adicional) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':capitulo', $capitulo, is_null($capitulo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':tipo_pregunta', $tipo_pregunta, is_null($tipo_pregunta) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':orden', $orden, is_null($orden) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':tbl_usuario_id', $tbl_usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':limite_respuesta_multiple', $limite_respuesta_multiple, is_null($limite_respuesta_multiple) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':habilitado', $habilitado, PDO::PARAM_STR);
            $stmt->bindValue(':visualizacion', $visualizacion, PDO::PARAM_STR);
            $stmt->bindValue(':obligatoria', $obligatoria, PDO::PARAM_INT);
            $stmt->bindValue(':es_filtro', $es_filtro, PDO::PARAM_INT);
            $stmt->bindValue(':libreto_encuestador', $libreto_encuestador, is_null($libreto_encuestador) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':nota_tabulacion', $nota_tabulacion, is_null($nota_tabulacion) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':tbl_grilla_plantilla_id', $tbl_grilla_plantilla_id, is_null($tbl_grilla_plantilla_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':visible_si_pregunta_id', $visible_si_pregunta_id, is_null($visible_si_pregunta_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':visible_si_opcion_id', $visible_si_opcion_id, is_null($visible_si_opcion_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':lista_origen_pregunta_id', $lista_origen_pregunta_id, is_null($lista_origen_pregunta_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':lista_origen_columna_id', $lista_origen_columna_id, is_null($lista_origen_columna_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':lista_origen_opcion_id', $lista_origen_opcion_id, is_null($lista_origen_opcion_id) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':si_lista_vacia', $si_lista_vacia, PDO::PARAM_STR);
            $stmt->bindValue(':es_variable_cruce', $es_variable_cruce, PDO::PARAM_INT);
            $stmt->bindValue(':etiqueta_cruce', $etiqueta_cruce, is_null($etiqueta_cruce) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':copiar_a_votante_campo', $copiar_a_votante_campo, is_null($copiar_a_votante_campo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':rol_resultado', $rol_resultado, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al guardar la pregunta principal.');
            }

            if ($id === 0) {
                $pregunta_id = (int)$pdo->lastInsertId();
            }

            if (!empty($opciones_respuesta) || !$esTipoSinOpcionesPropias) {
                self::sincronizarOpcionesPregunta($pdo, $db, (int)$pregunta_id, $opciones_respuesta, $tieneRespuestas);
            }

            self::sincronizarExclusionesYVariantes(
                $pdo,
                $db,
                (int)$pregunta_id,
                $rqst['exclusiones'] ?? null,
                $rqst['variantes_municipio'] ?? $rqst['grilla_variantes'] ?? null
            );

            $pdo->commit();
            $arrjson = array('output' => array('valid' => true, 'id' => (int)$pregunta_id));
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::save error: ' . $e->getMessage());
            error_log('Pregunta: ' . $e->getMessage());
            $arrjson = Util::error_general('Ocurrió un error al procesar la solicitud. Intente de nuevo.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function saveReglas($rqst)
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data_description('El ID de la pregunta es requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblPreguntas = $db->getTable('tbl_preguntas');
            $tblOpciones = $db->getTable('tbl_opciones_respuesta');
            $st = $pdo->prepare("SELECT * FROM {$tblPreguntas} WHERE id = :id AND eliminado = 0 LIMIT 1");
            $st->execute([':id' => $id]);
            $actual = $st->fetch(PDO::FETCH_ASSOC);
            if (!$actual) {
                return Util::error_general('La pregunta no existe.');
            }

            $pdo->beginTransaction();

            $obligatoria = isset($rqst['obligatoria']) ? ((int)$rqst['obligatoria'] ? 1 : 0) : (int)$actual['obligatoria'];
            $es_filtro = isset($rqst['es_filtro']) ? ((int)$rqst['es_filtro'] ? 1 : 0) : (int)$actual['es_filtro'];
            $libreto = array_key_exists('libreto_encuestador', $rqst) ? (trim((string)$rqst['libreto_encuestador']) ?: null) : $actual['libreto_encuestador'];
            $nota = array_key_exists('nota_tabulacion', $rqst) ? (trim((string)$rqst['nota_tabulacion']) ?: null) : $actual['nota_tabulacion'];
            $plantillaId = array_key_exists('tbl_grilla_plantilla_id', $rqst) ? (!empty($rqst['tbl_grilla_plantilla_id']) ? (int)$rqst['tbl_grilla_plantilla_id'] : null) : ($actual['tbl_grilla_plantilla_id'] !== null ? (int)$actual['tbl_grilla_plantilla_id'] : null);
            $visPregId = array_key_exists('visible_si_pregunta_id', $rqst) ? (!empty($rqst['visible_si_pregunta_id']) ? (int)$rqst['visible_si_pregunta_id'] : null) : ($actual['visible_si_pregunta_id'] !== null ? (int)$actual['visible_si_pregunta_id'] : null);
            $visOptId = array_key_exists('visible_si_opcion_id', $rqst) ? (!empty($rqst['visible_si_opcion_id']) ? (int)$rqst['visible_si_opcion_id'] : null) : ($actual['visible_si_opcion_id'] !== null ? (int)$actual['visible_si_opcion_id'] : null);
            $listOrigPregId = array_key_exists('lista_origen_pregunta_id', $rqst) ? (!empty($rqst['lista_origen_pregunta_id']) ? (int)$rqst['lista_origen_pregunta_id'] : null) : ($actual['lista_origen_pregunta_id'] !== null ? (int)$actual['lista_origen_pregunta_id'] : null);
            $listOrigColId = array_key_exists('lista_origen_columna_id', $rqst) ? (!empty($rqst['lista_origen_columna_id']) ? (int)$rqst['lista_origen_columna_id'] : null) : ($actual['lista_origen_columna_id'] !== null ? (int)$actual['lista_origen_columna_id'] : null);
            $listOrigOptId = array_key_exists('lista_origen_opcion_id', $rqst) ? (!empty($rqst['lista_origen_opcion_id']) ? (int)$rqst['lista_origen_opcion_id'] : null) : ($actual['lista_origen_opcion_id'] !== null ? (int)$actual['lista_origen_opcion_id'] : null);
            $siListaVacia = (!empty($rqst['si_lista_vacia']) && in_array($rqst['si_lista_vacia'], ['omitir', 'terminar'], true)) ? $rqst['si_lista_vacia'] : ($actual['si_lista_vacia'] ?: 'omitir');
            $esCruce = isset($rqst['es_variable_cruce']) ? ((int)$rqst['es_variable_cruce'] ? 1 : 0) : (int)$actual['es_variable_cruce'];
            $etiquetaCruce = array_key_exists('etiqueta_cruce', $rqst) ? (trim((string)$rqst['etiqueta_cruce']) ?: null) : $actual['etiqueta_cruce'];
            $copiarCampo = array_key_exists('copiar_a_votante_campo', $rqst) ? self::normalizarCampoVotante($rqst['copiar_a_votante_campo']) : $actual['copiar_a_votante_campo'];
            $rolResultado = !empty($rqst['rol_resultado']) ? self::normalizarRolResultado($rqst['rol_resultado']) : ($actual['rol_resultado'] ?: 'ninguno');

            $opcionesReglas = $rqst['opciones_reglas'] ?? $rqst['opciones'] ?? null;
            if (is_string($opcionesReglas)) {
                $opcionesReglas = json_decode($opcionesReglas, true);
            }
            if (is_array($opcionesReglas)) {
                $stUpdOpt = $pdo->prepare("UPDATE {$tblOpciones}
                    SET accion = :accion,
                        salto_destino_pregunta_id = :salto_destino,
                        mensaje_terminar = :mensaje_terminar,
                        valor_numerico = :valor_numerico,
                        cuenta_como = :cuenta_como,
                        es_opcion_fija = COALESCE(:es_opcion_fija, es_opcion_fija)
                    WHERE id = :id AND tbl_pregunta_id = :pid");
                foreach ($opcionesReglas as $opR) {
                    if (!is_array($opR) || empty($opR['id'])) {
                        continue;
                    }
                    $accionOp = in_array($opR['accion'] ?? '', ['continuar', 'saltar', 'terminar'], true) ? $opR['accion'] : 'continuar';
                    if ($accionOp === 'terminar') {
                        $es_filtro = 1;
                    }
                    $stUpdOpt->execute([
                        ':accion' => $accionOp,
                        ':salto_destino' => ($accionOp === 'saltar' && !empty($opR['salto_destino_pregunta_id'])) ? (int)$opR['salto_destino_pregunta_id'] : null,
                        ':mensaje_terminar' => ($accionOp === 'terminar' && !empty($opR['mensaje_terminar'])) ? trim((string)$opR['mensaje_terminar']) : null,
                        ':valor_numerico' => (isset($opR['valor_numerico']) && $opR['valor_numerico'] !== '' && $opR['valor_numerico'] !== null) ? (float)$opR['valor_numerico'] : null,
                        ':cuenta_como' => !empty($opR['cuenta_como']) ? (string)$opR['cuenta_como'] : 'ninguno',
                        ':es_opcion_fija' => isset($opR['es_opcion_fija']) ? ((int)$opR['es_opcion_fija'] ? 1 : 0) : null,
                        ':id' => (int)$opR['id'],
                        ':pid' => $id
                    ]);
                }
            }

            $stUpdP = $pdo->prepare("UPDATE {$tblPreguntas}
                SET obligatoria = :obligatoria,
                    es_filtro = :es_filtro,
                    libreto_encuestador = :libreto,
                    nota_tabulacion = :nota,
                    tbl_grilla_plantilla_id = :plantilla_id,
                    visible_si_pregunta_id = :vis_preg,
                    visible_si_opcion_id = :vis_opt,
                    lista_origen_pregunta_id = :list_preg,
                    lista_origen_columna_id = :list_col,
                    lista_origen_opcion_id = :list_opt,
                    si_lista_vacia = :si_vacia,
                    es_variable_cruce = :es_cruce,
                    etiqueta_cruce = :etiq_cruce,
                    copiar_a_votante_campo = :copiar_campo,
                    rol_resultado = :rol_res
                WHERE id = :id");
            $stUpdP->execute([
                ':obligatoria' => $obligatoria,
                ':es_filtro' => $es_filtro,
                ':libreto' => $libreto,
                ':nota' => $nota,
                ':plantilla_id' => $plantillaId,
                ':vis_preg' => $visPregId,
                ':vis_opt' => $visOptId,
                ':list_preg' => $listOrigPregId,
                ':list_col' => $listOrigColId,
                ':list_opt' => $listOrigOptId,
                ':si_vacia' => $siListaVacia,
                ':es_cruce' => $esCruce,
                ':etiq_cruce' => $etiquetaCruce,
                ':copiar_campo' => $copiarCampo,
                ':rol_res' => $rolResultado,
                ':id' => $id
            ]);

            self::sincronizarExclusionesYVariantes(
                $pdo,
                $db,
                $id,
                $rqst['exclusiones'] ?? null,
                $rqst['variantes_municipio'] ?? $rqst['grilla_variantes'] ?? null
            );

            $pdo->commit();
            return ['output' => ['valid' => true, 'id' => $id, 'response' => 'Reglas guardadas correctamente.']];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::saveReglas error: ' . $e->getMessage());
            error_log('Pregunta: ' . $e->getMessage());
            return Util::error_general('Ocurrió un error al procesar la solicitud. Intente de nuevo.');
        } finally {
            $db->closeConect();
        }
    }

    public static function crearVotoEscalonado($rqst)
    {
        $fichaId = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? (int)$rqst['tbl_ficha_tecnica_encuesta_id'] : (int)($rqst['ficha_id'] ?? 0);
        $origenPreguntaId = isset($rqst['lista_origen_pregunta_id']) ? (int)$rqst['lista_origen_pregunta_id'] : 0;
        $origenColumnaId = isset($rqst['lista_origen_columna_id']) ? (int)$rqst['lista_origen_columna_id'] : 0;
        $origenOpcionId = isset($rqst['lista_origen_opcion_id']) ? (int)$rqst['lista_origen_opcion_id'] : 0;
        $capitulo = !empty($rqst['capitulo']) ? trim((string)$rqst['capitulo']) : null;
        $enunciado = !empty($rqst['enunciado_pregunta']) ? trim((string)$rqst['enunciado_pregunta']) : null;
        $siListaVacia = (!empty($rqst['si_lista_vacia']) && in_array($rqst['si_lista_vacia'], ['omitir', 'terminar'], true)) ? $rqst['si_lista_vacia'] : 'omitir';

        if ($fichaId <= 0 || $origenPreguntaId <= 0 || $origenColumnaId <= 0 || $origenOpcionId <= 0) {
            return Util::error_missing_data_description('Seleccione la encuesta, la pregunta de tabla de candidatos, la pregunta por candidato y la respuesta que habilita la lista.');
        }

        $pasos = $rqst['pasos'] ?? null;
        if (is_string($pasos)) {
            $pasos = json_decode($pasos, true);
        }
        if (!is_array($pasos) || empty($pasos)) {
            $pasos = [
                [
                    'numeral' => $rqst['numeral_a'] ?? null,
                    'texto_pregunta' => $rqst['texto_a'] ?? '',
                    'libreto_encuestador' => $rqst['libreto_a'] ?? null
                ],
                [
                    'numeral' => $rqst['numeral_b'] ?? null,
                    'texto_pregunta' => $rqst['texto_b'] ?? '',
                    'libreto_encuestador' => $rqst['libreto_b'] ?? null
                ],
                [
                    'numeral' => $rqst['numeral_c'] ?? null,
                    'texto_pregunta' => $rqst['texto_c'] ?? '',
                    'libreto_encuestador' => $rqst['libreto_c'] ?? null
                ]
            ];
        }

        $opcionesFijasGlobal = $rqst['opciones_fijas'] ?? null;
        if (is_string($opcionesFijasGlobal)) {
            $opcionesFijasGlobal = json_decode($opcionesFijasGlobal, true);
        }
        $armarFijas = function ($textos) {
            $fijas = [];
            foreach ((array)$textos as $txtFija) {
                $txtFija = trim((string)$txtFija);
                if ($txtFija !== '') {
                    $fijas[] = ['texto' => $txtFija, 'es_opcion_fija' => 1, 'accion' => 'continuar'];
                }
            }
            return $fijas;
        };

        $creadasIds = [];
        foreach ($pasos as $paso) {
            $textoPaso = trim((string)($paso['texto_pregunta'] ?? ''));
            if ($textoPaso === '') {
                continue;
            }
            $res = self::save([
                'tbl_ficha_tecnica_encuesta_id' => $fichaId,
                'texto_pregunta' => $textoPaso,
                'enunciado_pregunta' => $paso['enunciado_pregunta'] ?? $enunciado,
                'capitulo' => $paso['capitulo'] ?? $capitulo,
                'numeral' => $paso['numeral'] ?? null,
                'libreto_encuestador' => $paso['libreto_encuestador'] ?? null,
                'nota_tabulacion' => $paso['nota_tabulacion'] ?? null,
                'tipo_pregunta' => 'Lista_desde_grilla',
                'obligatoria' => 1,
                'lista_origen_pregunta_id' => $origenPreguntaId,
                'lista_origen_columna_id' => $origenColumnaId,
                'lista_origen_opcion_id' => $origenOpcionId,
                'si_lista_vacia' => $siListaVacia,
                'exclusiones' => $creadasIds,
                'opciones' => $armarFijas(isset($paso['opciones_fijas']) ? $paso['opciones_fijas'] : $opcionesFijasGlobal)
            ]);
            if (empty($res['output']['valid']) || empty($res['output']['id'])) {
                foreach ($creadasIds as $idCreada) {
                    self::delete(['id' => $idCreada]);
                }
                return $res;
            }
            $creadasIds[] = (int)$res['output']['id'];
        }

        if (empty($creadasIds)) {
            return Util::error_missing_data_description('Escriba el texto de al menos una de las preguntas.');
        }

        return [
            'output' => [
                'valid' => true,
                'ids' => $creadasIds,
                'response' => count($creadasIds) . ' preguntas de voto escalonado creadas y conectadas en cascada.'
            ]
        ];
    }

    public static function getDefinicion($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        require_once __DIR__ . '/cuestionario/DefinicionCuestionario.php';
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $def = DefinicionCuestionario::cargar($pdo, $fichaId);
            if (!$def) {
                return Util::error_no_result();
            }
            return ['output' => ['valid' => true, 'response' => $def]];
        } catch (Exception $e) {
            error_log('Pregunta::getDefinicion error: ' . $e->getMessage());
            return Util::error_general('Error al cargar la definición del cuestionario.');
        } finally {
            $db->closeConect();
        }
    }

    public static function validarConfiguracion($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        require_once __DIR__ . '/cuestionario/DefinicionCuestionario.php';
        require_once __DIR__ . '/cuestionario/MotorReglasCuestionario.php';
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $def = DefinicionCuestionario::cargar($pdo, $fichaId);
            if (!$def) {
                return Util::error_no_result();
            }
            $val = MotorReglasCuestionario::validarConfiguracion($def);
            return ['output' => ['valid' => true, 'response' => $val]];
        } catch (Exception $e) {
            error_log('Pregunta::validarConfiguracion error: ' . $e->getMessage());
            return Util::error_general('Error al validar la configuración del cuestionario.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getMapa($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        require_once __DIR__ . '/cuestionario/DefinicionCuestionario.php';
        require_once __DIR__ . '/cuestionario/MotorReglasCuestionario.php';
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $def = DefinicionCuestionario::cargar($pdo, $fichaId);
            if (!$def) {
                return Util::error_no_result();
            }
            $validacion = MotorReglasCuestionario::validarConfiguracion($def);

            $pregPorId = [];
            foreach ($def['preguntas'] as $p) {
                $pregPorId[(int)$p['id']] = $p;
            }
            $plantillas = is_array($def['plantillas']) ? $def['plantillas'] : (array)$def['plantillas'];

            $pasos = [];
            foreach ($def['preguntas'] as $idx => $p) {
                $pid = (int)$p['id'];
                $etiqueta = trim(($p['numeral'] ? $p['numeral'] . '. ' : '') . $p['texto_pregunta']);
                $condicionTexto = 'Siempre se muestra';
                if (!empty($p['visible_si_pregunta_id']) && !empty($p['visible_si_opcion_id'])) {
                    $pPadre = $pregPorId[(int)$p['visible_si_pregunta_id']] ?? null;
                    $opTexto = '';
                    if ($pPadre) {
                        foreach ($pPadre['opciones'] as $op) {
                            if ((int)$op['id'] === (int)$p['visible_si_opcion_id']) {
                                $opTexto = $op['texto'];
                                break;
                            }
                        }
                    }
                    $padreLabel = $pPadre ? (($pPadre['numeral'] ?: ('#' . $pPadre['orden'])) . ' "' . mb_substr($pPadre['texto_pregunta'], 0, 45) . '"') : 'pregunta previa';
                    $condicionTexto = 'Solo se muestra si en ' . $padreLabel . ' respondió "' . ($opTexto ?: 'opción seleccionada') . '"';
                }

                $reglasOpciones = [];
                foreach ($p['opciones'] as $op) {
                    $acc = $op['accion'] ?? 'continuar';
                    if ($acc === 'terminar') {
                        $reglasOpciones[] = [
                            'opcion' => $op['texto'],
                            'tipo' => 'terminar',
                            'resumen' => 'Si responde "' . $op['texto'] . '" → Termina la encuesta' . (!empty($op['mensaje_terminar']) ? ' ("' . $op['mensaje_terminar'] . '")' : '')
                        ];
                    } elseif ($acc === 'saltar' && !empty($op['salto_destino_pregunta_id'])) {
                        $pDest = $pregPorId[(int)$op['salto_destino_pregunta_id']] ?? null;
                        $destLabel = $pDest ? (($pDest['numeral'] ?: ('Orden ' . $pDest['orden'])) . ' — ' . mb_substr($pDest['texto_pregunta'], 0, 50)) : 'pregunta destino';
                        $reglasOpciones[] = [
                            'opcion' => $op['texto'],
                            'tipo' => 'saltar',
                            'resumen' => 'Si responde "' . $op['texto'] . '" → Salta directo a ' . $destLabel
                        ];
                    }
                }

                $detalleTipo = $p['tipo_pregunta'];
                $resumenGrillaLista = null;
                if ($p['tipo_pregunta'] === 'Grilla') {
                    $detalleTipo = 'Tabla de candidatos';
                    $plId = (int)($p['tbl_grilla_plantilla_id'] ?? 0);
                    $vars = (array)($p['variantes_municipio'] ?? []);
                    if ($plId > 0 && isset($plantillas[$plId])) {
                        $pl = $plantillas[$plId];
                        $resumenGrillaLista = 'Tabla: "' . $pl['nombre'] . '" (' . count($pl['filas']) . ' filas × ' . count($pl['columnas']) . ' preguntas por fila)';
                    } elseif (!empty($vars)) {
                        $resumenGrillaLista = 'Tabla cambia según el municipio (' . count($vars) . ' municipio(s) configurado(s))';
                    } else {
                        $resumenGrillaLista = 'Sin tabla asignada';
                    }
                } elseif ($p['tipo_pregunta'] === 'Lista_desde_grilla') {
                    $detalleTipo = 'Escoger de los candidatos de una tabla';
                    $pOrig = !empty($p['lista_origen_pregunta_id']) ? ($pregPorId[(int)$p['lista_origen_pregunta_id']] ?? null) : null;
                    $origLabel = $pOrig ? ($pOrig['numeral'] ?: $pOrig['texto_pregunta']) : 'tabla origen';
                    $exclLabels = [];
                    foreach ($p['exclusiones_pregunta_ids'] ?? [] as $exId) {
                        if (isset($pregPorId[(int)$exId])) {
                            $exclLabels[] = $pregPorId[(int)$exId]['numeral'] ?: ('Orden ' . $pregPorId[(int)$exId]['orden']);
                        }
                    }
                    $resumenGrillaLista = 'Trae candidatos de "' . $origLabel . '"'
                        . (!empty($exclLabels) ? ' (excluyendo el elegido en ' . implode(', ', $exclLabels) . ')' : '')
                        . ' · Si queda vacía: ' . (($p['si_lista_vacia'] ?? 'omitir') === 'terminar' ? 'Termina la encuesta' : 'Se omite y continúa');
                }

                $pasos[] = [
                    'numero_paso' => $idx + 1,
                    'id' => $pid,
                    'orden' => (int)$p['orden'],
                    'capitulo' => $p['capitulo'],
                    'numeral' => $p['numeral'],
                    'enunciado' => $p['enunciado'],
                    'texto_pregunta' => $p['texto_pregunta'],
                    'etiqueta' => $etiqueta,
                    'tipo_pregunta' => $p['tipo_pregunta'],
                    'tipo_legible' => $detalleTipo,
                    'es_filtro' => (int)$p['es_filtro'],
                    'obligatoria' => (int)$p['obligatoria'],
                    'es_variable_cruce' => (int)$p['es_variable_cruce'],
                    'etiqueta_cruce' => $p['etiqueta_cruce'],
                    'condicion_visibilidad' => $condicionTexto,
                    'reglas_opciones' => $reglasOpciones,
                    'resumen_grilla_lista' => $resumenGrillaLista,
                    'libreto_encuestador' => $p['libreto_encuestador'],
                    'nota_tabulacion' => $p['nota_tabulacion']
                ];
            }

            return [
                'output' => [
                    'valid' => true,
                    'response' => [
                        'ficha' => $def['ficha'],
                        'pasos' => $pasos,
                        'validacion' => $validacion
                    ]
                ]
            ];
        } catch (Exception $e) {
            error_log('Pregunta::getMapa error: ' . $e->getMessage());
            return Util::error_general('Error al construir el mapa del cuestionario.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getConfigEncuesta($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblFichas = $db->getTable('tbl_ficha_tecnica_encuestas');
            $st = $pdo->prepare("SELECT
                    id,
                    realizada_por_o_encomendada_por,
                    temas_concretos,
                    pregunta_destacada_id,
                    COALESCE(pedir_municipio_zona_inicio, 1) AS pedir_municipio_zona_inicio,
                    COALESCE(audio_obligatorio, 1) AS audio_obligatorio,
                    COALESCE(certificar_terminadas_filtro, 1) AS certificar_terminadas_filtro,
                    COALESCE(mostrar_resultados_publicos, 1) AS mostrar_resultados_publicos,
                    COALESCE(bloquear_cuota_completa, 0) AS bloquear_cuota_completa,
                    COALESCE(mensaje_cierre_defecto, 'Muchas gracias por su tiempo. Hemos terminado la encuesta.') AS mensaje_cierre_defecto
                FROM {$tblFichas}
                WHERE id = :id LIMIT 1");
            $st->execute([':id' => $fichaId]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return Util::error_no_result();
            }
            return ['output' => ['valid' => true, 'response' => $row]];
        } catch (Exception $e) {
            error_log('Pregunta::getConfigEncuesta error: ' . $e->getMessage());
            return Util::error_general('Error al obtener las opciones de la encuesta.');
        } finally {
            $db->closeConect();
        }
    }

    public static function saveConfigEncuesta($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        $pedirMun = isset($rqst['pedir_municipio_zona_inicio']) ? ((int)$rqst['pedir_municipio_zona_inicio'] ? 1 : 0) : 1;
        $audioOblig = isset($rqst['audio_obligatorio']) ? ((int)$rqst['audio_obligatorio'] ? 1 : 0) : 1;
        $certFiltro = isset($rqst['certificar_terminadas_filtro']) ? ((int)$rqst['certificar_terminadas_filtro'] ? 1 : 0) : 1;
        $mostrarPub = isset($rqst['mostrar_resultados_publicos']) ? ((int)$rqst['mostrar_resultados_publicos'] ? 1 : 0) : 1;
        $bloqCuota = isset($rqst['bloquear_cuota_completa']) ? ((int)$rqst['bloquear_cuota_completa'] ? 1 : 0) : 0;
        $pregDestacada = !empty($rqst['pregunta_destacada_id']) ? (int)$rqst['pregunta_destacada_id'] : null;
        $msgCierre = isset($rqst['mensaje_cierre_defecto']) && trim((string)$rqst['mensaje_cierre_defecto']) !== ''
            ? trim((string)$rqst['mensaje_cierre_defecto'])
            : 'Muchas gracias por su tiempo. Hemos terminado la encuesta.';

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblFichas = $db->getTable('tbl_ficha_tecnica_encuestas');
            if ($pregDestacada !== null) {
                $stDest = $pdo->prepare("SELECT COUNT(*) FROM " . $db->getTable('tbl_preguntas') . " WHERE id = :pid AND tbl_ficha_tecnica_encuesta_id = :fid AND eliminado = 0");
                $stDest->execute([':pid' => $pregDestacada, ':fid' => $fichaId]);
                if ((int)$stDest->fetchColumn() === 0) {
                    $pregDestacada = null;
                }
            }
            $st = $pdo->prepare("UPDATE {$tblFichas}
                SET pedir_municipio_zona_inicio = :pedir_mun,
                    audio_obligatorio = :audio_ob,
                    certificar_terminadas_filtro = :cert_f,
                    mostrar_resultados_publicos = :most_pub,
                    bloquear_cuota_completa = :bloq_c,
                    pregunta_destacada_id = :preg_dest,
                    mensaje_cierre_defecto = :msg_cierre,
                    dtupdate = NOW()
                WHERE id = :id");
            $st->execute([
                ':pedir_mun' => $pedirMun,
                ':audio_ob' => $audioOblig,
                ':cert_f' => $certFiltro,
                ':most_pub' => $mostrarPub,
                ':bloq_c' => $bloqCuota,
                ':preg_dest' => $pregDestacada,
                ':msg_cierre' => $msgCierre,
                ':id' => $fichaId
            ]);
            return ['output' => ['valid' => true, 'response' => 'Opciones de la encuesta guardadas correctamente.']];
        } catch (Exception $e) {
            error_log('Pregunta::saveConfigEncuesta error: ' . $e->getMessage());
            return Util::error_general('Error al guardar las opciones de la encuesta.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getZonasMuestreo($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblZonas = $db->getTable('tbl_zonas_muestreo');
            $tblCiudades = $db->getTable('tbl_ciudades');
            $tblIntentos = $db->getTable('tbl_cuestionario_intentos');

            $st = $pdo->prepare("SELECT
                    z.id,
                    z.tbl_ficha_tecnica_encuesta_id,
                    z.codigo_departamento,
                    z.codigo_municipio,
                    c.municipio AS nombre_municipio,
                    z.tipo,
                    z.codigo,
                    z.nombre,
                    z.descripcion,
                    z.cuota,
                    z.orden,
                    z.habilitado,
                    (SELECT COUNT(*) FROM {$tblIntentos} i
                     WHERE i.tbl_ficha_tecnica_encuesta_id = z.tbl_ficha_tecnica_encuesta_id
                       AND i.tbl_zona_muestreo_id = z.id
                       AND i.es_prueba = 0
                       AND i.estado = 'completo') AS realizadas
                FROM {$tblZonas} z
                LEFT JOIN {$tblCiudades} c ON c.codigo_muncipio = z.codigo_municipio
                WHERE z.tbl_ficha_tecnica_encuesta_id = :fid
                  AND z.eliminado = 0
                ORDER BY z.codigo_municipio ASC, z.orden ASC, z.id ASC");
            $st->execute([':fid' => $fichaId]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$r) {
                $r['id'] = (int)$r['id'];
                $r['cuota'] = (int)$r['cuota'];
                $r['realizadas'] = (int)$r['realizadas'];
                $r['restantes'] = max(0, $r['cuota'] - $r['realizadas']);
            }
            unset($r);
            return ['output' => ['valid' => true, 'response' => $rows]];
        } catch (Exception $e) {
            error_log('Pregunta::getZonasMuestreo error: ' . $e->getMessage());
            return Util::error_general('Error al consultar las zonas y metas.');
        } finally {
            $db->closeConect();
        }
    }

    public static function saveZonasMuestreo($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        $zonasRaw = $rqst['zonas'] ?? [];
        if (is_string($zonasRaw)) {
            $zonasRaw = json_decode($zonasRaw, true);
        }
        if (!is_array($zonasRaw)) {
            return Util::error_missing_data_description('Formato de zonas inválido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblZonas = $db->getTable('tbl_zonas_muestreo');
            $pdo->beginTransaction();

            $stUpd = $pdo->prepare("UPDATE {$tblZonas}
                SET codigo_departamento = :dep,
                    codigo_municipio = :mun,
                    tipo = :tipo,
                    codigo = :cod,
                    nombre = :nom,
                    descripcion = :desc,
                    cuota = :cuota,
                    orden = :orden,
                    habilitado = :hab,
                    eliminado = 0,
                    dtupdate = NOW()
                WHERE id = :id AND tbl_ficha_tecnica_encuesta_id = :fid");

            $stIns = $pdo->prepare("INSERT INTO {$tblZonas}
                (tbl_ficha_tecnica_encuesta_id, codigo_departamento, codigo_municipio, tipo, codigo, nombre, descripcion, cuota, orden, habilitado, eliminado, dtcreate)
                VALUES (:fid, :dep, :mun, :tipo, :cod, :nom, :desc, :cuota, :orden, :hab, 0, NOW())");

            $guardadas = 0;
            foreach ($zonasRaw as $idx => $z) {
                if (!is_array($z)) {
                    continue;
                }
                $zid = isset($z['id']) ? (int)$z['id'] : 0;
                $codMun = trim((string)($z['codigo_municipio'] ?? ''));
                $nombre = trim((string)($z['nombre'] ?? ''));
                if ($codMun === '' || $nombre === '') {
                    continue;
                }
                $codDep = trim((string)($z['codigo_departamento'] ?? substr($codMun, 0, 2)));
                $tipo = in_array($z['tipo'] ?? '', ['comuna', 'sector', 'corregimiento', 'zona'], true) ? $z['tipo'] : 'comuna';
                $codigo = isset($z['codigo']) && trim((string)$z['codigo']) !== '' ? trim((string)$z['codigo']) : null;
                $descripcion = isset($z['descripcion']) && trim((string)$z['descripcion']) !== '' ? trim((string)$z['descripcion']) : null;
                $cuota = max(0, (int)($z['cuota'] ?? 0));
                $orden = isset($z['orden']) ? (int)$z['orden'] : ($idx + 1);
                $hab = (isset($z['habilitado']) && $z['habilitado'] === 'no') ? 'no' : 'si';

                if ($zid > 0) {
                    $stUpd->execute([
                        ':dep' => $codDep,
                        ':mun' => $codMun,
                        ':tipo' => $tipo,
                        ':cod' => $codigo,
                        ':nom' => $nombre,
                        ':desc' => $descripcion,
                        ':cuota' => $cuota,
                        ':orden' => $orden,
                        ':hab' => $hab,
                        ':id' => $zid,
                        ':fid' => $fichaId
                    ]);
                } else {
                    $stIns->execute([
                        ':fid' => $fichaId,
                        ':dep' => $codDep,
                        ':mun' => $codMun,
                        ':tipo' => $tipo,
                        ':cod' => $codigo,
                        ':nom' => $nombre,
                        ':desc' => $descripcion,
                        ':cuota' => $cuota,
                        ':orden' => $orden,
                        ':hab' => $hab
                    ]);
                }
                $guardadas++;
            }

            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => "{$guardadas} zona(s) guardada(s) correctamente."]];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::saveZonasMuestreo error: ' . $e->getMessage());
            return Util::error_general('Error al guardar las zonas de muestreo.');
        } finally {
            $db->closeConect();
        }
    }

    public static function deleteZonaMuestreo($rqst)
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data_description('ID de zona requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblZonas = $db->getTable('tbl_zonas_muestreo');
            $fichaZona = isset($rqst['tbl_ficha_tecnica_encuesta_id']) ? (int)$rqst['tbl_ficha_tecnica_encuesta_id'] : (int)($rqst['ficha_id'] ?? 0);
            if ($fichaZona <= 0) {
                return Util::error_missing_data_description('ID de encuesta requerido.');
            }
            $pdo->prepare("UPDATE {$tblZonas} SET eliminado = 1, habilitado = 'no', dtupdate = NOW() WHERE id = :id AND tbl_ficha_tecnica_encuesta_id = :fid")
                ->execute([':id' => $id, ':fid' => $fichaZona]);
            return ['output' => ['valid' => true, 'response' => 'Zona eliminada.']];
        } catch (Exception $e) {
            error_log('Pregunta::deleteZonaMuestreo error: ' . $e->getMessage());
            return Util::error_general('Error al eliminar la zona.');
        } finally {
            $db->closeConect();
        }
    }

    public static function copiarZonasMuestreo($rqst)
    {
        $fichaDestino = isset($rqst['ficha_destino_id']) ? (int)$rqst['ficha_destino_id'] : (int)($rqst['ficha_id'] ?? 0);
        $fichaOrigen = isset($rqst['ficha_origen_id']) ? (int)$rqst['ficha_origen_id'] : 0;

        if ($fichaDestino <= 0 || $fichaOrigen <= 0 || $fichaDestino === $fichaOrigen) {
            return Util::error_missing_data_description('Seleccione una encuesta de origen válida y distinta de la actual.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblZonas = $db->getTable('tbl_zonas_muestreo');
            $stOrig = $pdo->prepare("SELECT codigo_departamento, codigo_municipio, tipo, codigo, nombre, cuota, orden
                FROM {$tblZonas}
                WHERE tbl_ficha_tecnica_encuesta_id = :orig AND eliminado = 0 AND habilitado = 'si'
                ORDER BY codigo_municipio ASC, orden ASC, id ASC");
            $stOrig->execute([':orig' => $fichaOrigen]);
            $rows = $stOrig->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                return Util::error_general('La encuesta seleccionada no tiene zonas de muestreo registradas.');
            }

            $pdo->beginTransaction();
            $stIns = $pdo->prepare("INSERT INTO {$tblZonas}
                (tbl_ficha_tecnica_encuesta_id, codigo_departamento, codigo_municipio, tipo, codigo, nombre, cuota, orden, habilitado, eliminado, dtcreate)
                VALUES (:fid, :dep, :mun, :tipo, :cod, :nom, :cuota, :orden, 'si', 0, NOW())");
            foreach ($rows as $r) {
                $stIns->execute([
                    ':fid' => $fichaDestino,
                    ':dep' => $r['codigo_departamento'],
                    ':mun' => $r['codigo_municipio'],
                    ':tipo' => $r['tipo'],
                    ':cod' => $r['codigo'],
                    ':nom' => $r['nombre'],
                    ':cuota' => (int)$r['cuota'],
                    ':orden' => (int)$r['orden']
                ]);
            }
            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => count($rows) . ' zona(s) copiada(s) correctamente.']];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::copiarZonasMuestreo error: ' . $e->getMessage());
            return Util::error_general('Error al copiar las zonas de muestreo.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getEquivalenciasVotante($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblEquiv = $db->getTable('tbl_equivalencias_votante');
            $st = $pdo->prepare("SELECT id, tbl_ficha_tecnica_encuesta_id, tbl_pregunta_id, tbl_opcion_respuesta_id, campo_votante, valor_votante
                FROM {$tblEquiv}
                WHERE tbl_ficha_tecnica_encuesta_id = :fid
                ORDER BY tbl_pregunta_id ASC, tbl_opcion_respuesta_id ASC");
            $st->execute([':fid' => $fichaId]);
            return ['output' => ['valid' => true, 'response' => $st->fetchAll(PDO::FETCH_ASSOC)]];
        } catch (Exception $e) {
            error_log('Pregunta::getEquivalenciasVotante error: ' . $e->getMessage());
            return Util::error_general('Error al consultar equivalencias del votante.');
        } finally {
            $db->closeConect();
        }
    }

    public static function saveEquivalenciasVotante($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : (int)($rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha requerido.');
        }

        require_once __DIR__ . '/Votantes.php';
        $catalogoCampos = Votantes::catalogoCamposEquivalencia();
        $camposPermitidos = array_keys($catalogoCampos);

        $variablesCruce = $rqst['variables_cruce'] ?? null;
        if (is_string($variablesCruce)) {
            $variablesCruce = json_decode($variablesCruce, true);
        }

        $equivalencias = $rqst['equivalencias'] ?? null;
        if (is_string($equivalencias)) {
            $equivalencias = json_decode($equivalencias, true);
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblPreguntas = $db->getTable('tbl_preguntas');
            $tblEquiv = $db->getTable('tbl_equivalencias_votante');
            $pdo->beginTransaction();

            if (is_array($variablesCruce)) {
                $stUpdP = $pdo->prepare("UPDATE {$tblPreguntas}
                    SET es_variable_cruce = :es_cruce,
                        etiqueta_cruce = :etiq,
                        copiar_a_votante_campo = :campo
                    WHERE id = :pid AND tbl_ficha_tecnica_encuesta_id = :fid");
                foreach ($variablesCruce as $vc) {
                    if (!is_array($vc) || empty($vc['pregunta_id'])) {
                        continue;
                    }
                    $campo = trim((string)($vc['copiar_a_votante_campo'] ?? ''));
                    if (!in_array($campo, $camposPermitidos, true)) {
                        $campo = null;
                    }
                    $stUpdP->execute([
                        ':es_cruce' => !empty($vc['es_variable_cruce']) ? 1 : 0,
                        ':etiq' => !empty($vc['etiqueta_cruce']) ? trim((string)$vc['etiqueta_cruce']) : null,
                        ':campo' => $campo,
                        ':pid' => (int)$vc['pregunta_id'],
                        ':fid' => $fichaId
                    ]);
                }
            }

            if (is_array($equivalencias)) {
                $pdo->prepare("DELETE FROM {$tblEquiv} WHERE tbl_ficha_tecnica_encuesta_id = :fid")->execute([':fid' => $fichaId]);
                $stInsEq = $pdo->prepare("INSERT INTO {$tblEquiv}
                    (tbl_ficha_tecnica_encuesta_id, tbl_pregunta_id, tbl_opcion_respuesta_id, campo_votante, valor_votante, dtcreate)
                    VALUES (:fid, :pid, :oid, :campo, :valor, NOW())");
                foreach ($equivalencias as $eq) {
                    if (!is_array($eq)) {
                        continue;
                    }
                    $pid = (int)($eq['tbl_pregunta_id'] ?? 0);
                    $oid = (int)($eq['tbl_opcion_respuesta_id'] ?? 0);
                    $campo = trim((string)($eq['campo_votante'] ?? ''));
                    $valor = trim((string)($eq['valor_votante'] ?? ''));
                    $valorValido = in_array($campo, $camposPermitidos, true)
                        && (empty($catalogoCampos[$campo]['valores']) || isset($catalogoCampos[$campo]['valores'][$valor]));
                    if ($pid > 0 && $oid > 0 && $valorValido && $valor !== '') {
                        $stInsEq->execute([
                            ':fid' => $fichaId,
                            ':pid' => $pid,
                            ':oid' => $oid,
                            ':campo' => $campo,
                            ':valor' => $valor
                        ]);
                    }
                }
            }

            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => 'Variables de cruce y equivalencias guardadas correctamente.']];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::saveEquivalenciasVotante error: ' . $e->getMessage());
            return Util::error_general('Error al guardar las variables de cruce y equivalencias.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getMunicipiosCatalogo($rqst)
    {
        $dep = !empty($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : '';
        $fichaId = (int)($rqst['ficha_id'] ?? $rqst['tbl_ficha_tecnica_encuesta_id'] ?? 0);
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tblCiudades = $db->getTable('tbl_ciudades');
            $sql = "SELECT codigo_muncipio AS codigo_municipio, municipio, codigo_departamento FROM {$tblCiudades}";
            $params = [];
            if ($dep !== '') {
                $sql .= " WHERE codigo_departamento = :dep";
                $params[':dep'] = $dep;
            } elseif ($fichaId > 0) {
                $conv = function ($c) { return "CONVERT({$c} USING utf8mb4) COLLATE utf8mb4_unicode_ci"; };
                $tXd = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');
                $tFte = $db->getTable('tbl_ficha_tecnica_encuestas');
                $stTipo = $pdo->prepare("SELECT eg.tipo_estudio FROM " . $db->getTable('tbl_espacio_geografico') . " eg JOIN {$tFte} f ON f.tbl_espacio_geografico_id = eg.id WHERE f.id = :f LIMIT 1");
                $stTipo->execute([':f' => $fichaId]);
                $tipo = strtolower((string)$stTipo->fetchColumn());
                $ya = "SELECT z.codigo_municipio FROM " . $db->getTable('tbl_zonas_muestreo') . " z WHERE z.tbl_ficha_tecnica_encuesta_id = {$fichaId} AND z.eliminado = 0"
                    . " UNION SELECT v.codigo_municipio FROM " . $db->getTable('tbl_preguntas_grilla_variantes') . " v JOIN " . $db->getTable('tbl_preguntas') . " p ON p.id = v.tbl_pregunta_id WHERE p.tbl_ficha_tecnica_encuesta_id = {$fichaId}";
                if ($tipo === 'municipal') {
                    $sql .= " WHERE {$conv('codigo_muncipio')} IN (SELECT {$conv('xd.codigo_ciudad')} FROM {$tXd} xd JOIN {$tFte} f ON f.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id WHERE f.id = {$fichaId})"
                        . " OR {$conv('codigo_muncipio')} IN (SELECT {$conv('x.codigo_municipio')} FROM ({$ya}) x)";
                } elseif ($tipo === 'departamental') {
                    $sql .= " WHERE {$conv('codigo_departamento')} IN (SELECT {$conv('xd.codigo_departamento')} FROM {$tXd} xd JOIN {$tFte} f ON f.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id WHERE f.id = {$fichaId})"
                        . " OR {$conv('codigo_muncipio')} IN (SELECT {$conv('x.codigo_municipio')} FROM ({$ya}) x)";
                }
            }
            $sql .= " ORDER BY municipio ASC";
            $st = $pdo->prepare($sql);
            $st->execute($params);
            return ['output' => ['valid' => true, 'response' => $st->fetchAll(PDO::FETCH_ASSOC)]];
        } catch (Exception $e) {
            error_log('Pregunta::getMunicipiosCatalogo error: ' . $e->getMessage());
            return Util::error_general('Error al obtener el catálogo de municipios.');
        } finally {
            $db->closeConect();
        }
    }

    public static function saveHabilitado($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $habilitado = isset($rqst['habilitado']) && $rqst['habilitado'] === 'si' ? 'si' : 'no';

        if ($id <= 0) return Util::error_missing_data_description('ID requerido');

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare("UPDATE " . $db->getTable('tbl_preguntas') . " SET habilitado = :habilitado WHERE id = :id");
            $stmt->execute([':habilitado' => $habilitado, ':id' => $id]);
            $arrjson = ['output' => ['valid' => true, 'response' => 'Estado actualizado']];
        } catch (Exception $e) {
            error_log('Pregunta::saveHabilitado error: ' . $e->getMessage());
            $arrjson = Util::error_general('No se pudo actualizar el estado de la pregunta.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        if ($id <= 0) return Util::error_missing_data_description('ID requerido');

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE " . $db->getTable('tbl_opciones_respuesta') . " SET eliminado = 1 WHERE tbl_pregunta_id = :id");
            $stmt->execute([':id' => $id]);

            $stmt = $pdo->prepare("UPDATE " . $db->getTable('tbl_preguntas') . " SET eliminado = 1, habilitado = 'no' WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $pdo->commit();
            $arrjson = ['output' => ['valid' => true, 'response' => 'Pregunta eliminada']];
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::delete error: ' . $e->getMessage());
            $arrjson = Util::error_general('No se pudo eliminar la pregunta.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function saveEnunciado($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $enunciado = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : '';

        if ($id <= 0) {
            return Util::error_missing_data_description('ID de pregunta requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET enunciado_pregunta = :enunciado_pregunta
                  WHERE id = :id";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':enunciado_pregunta' => $enunciado ?: null, ':id' => $id]);
            $arrjson = ['output' => ['valid' => true, 'response' => 'Enunciado actualizado']];
        } catch (Exception $e) {
            error_log('Pregunta::saveEnunciado error: ' . $e->getMessage());
            $arrjson = Util::error_general('Error al actualizar enunciado.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function saveEnunciadoGroup($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? intval($rqst['ficha_id']) : 0;
        $enunciado = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : '';
        $soloSinEnunciado = isset($rqst['solo_sin_enunciado']) && $rqst['solo_sin_enunciado'] === '1';

        if ($fichaId <= 0) {
            return Util::error_missing_data_description('ID de ficha técnica requerido');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            if ($soloSinEnunciado) {
                $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                      SET enunciado_pregunta = :enunciado_pregunta
                      WHERE tbl_ficha_tecnica_encuesta_id = :ficha_id
                        AND eliminado = 0
                        AND (enunciado_pregunta IS NULL OR enunciado_pregunta = '')";
            } else {
                $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                      SET enunciado_pregunta = :enunciado_pregunta
                      WHERE tbl_ficha_tecnica_encuesta_id = :ficha_id
                        AND eliminado = 0";
            }
            $stmt = $pdo->prepare($q);
            $stmt->execute([':enunciado_pregunta' => $enunciado ?: null, ':ficha_id' => $fichaId]);
            $affected = $stmt->rowCount();
            $arrjson = ['output' => ['valid' => true, 'response' => "Enunciado actualizado en $affected preguntas"]];
        } catch (Exception $e) {
            error_log('Pregunta::saveEnunciadoGroup error: ' . $e->getMessage());
            $arrjson = Util::error_general('Error al actualizar enunciado del grupo.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function saveEnunciadoSelected($rqst)
    {
        $ids_raw   = isset($rqst['ids'])               ? trim($rqst['ids'])               : '';
        $enunciado = isset($rqst['enunciado_pregunta']) ? trim($rqst['enunciado_pregunta']) : '';

        if (empty($ids_raw)) {
            return Util::error_missing_data_description('IDs de preguntas requeridos');
        }

        $ids = array_filter(array_map('intval', explode(',', $ids_raw)), fn($v) => $v > 0);

        if (empty($ids)) {
            return Util::error_missing_data_description('IDs de preguntas inválidos');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET enunciado_pregunta = ?
                  WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($q);
            $params = array_merge([$enunciado ?: null], array_values($ids));
            $stmt->execute($params);
            $affected = $stmt->rowCount();
            $arrjson  = ['output' => ['valid' => true, 'response' => "Enunciado actualizado en $affected preguntas"]];
        } catch (Exception $e) {
            error_log('Pregunta::saveEnunciadoSelected error: ' . $e->getMessage());
            $arrjson = Util::error_general('Error al actualizar enunciado.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function reasignar($rqst)
    {
        $ids_raw   = isset($rqst['ids'])   ? trim($rqst['ids'])   : '';
        $ficha_id  = isset($rqst['ficha_id']) ? intval($rqst['ficha_id']) : 0;

        if (empty($ids_raw) || $ficha_id <= 0) {
            return Util::error_missing_data_description('IDs de preguntas y ficha técnica destino son requeridos.');
        }

        $ids = array_filter(array_map('intval', explode(',', $ids_raw)), fn($v) => $v > 0);
        if (empty($ids)) {
            return Util::error_missing_data_description('No se encontraron IDs válidos.');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge([$ficha_id], array_values($ids));
            $stmt = $pdo->prepare(
                "UPDATE " . $db->getTable('tbl_preguntas') .
                " SET tbl_ficha_tecnica_encuesta_id = ? WHERE id IN ($placeholders)"
            );
            $stmt->execute($params);
            $arrjson = array('output' => array('valid' => true, 'response' => $stmt->rowCount() . ' pregunta(s) reasignada(s)'));
        } catch (PDOException $e) {
            error_log('Pregunta::reasignar error: ' . $e->getMessage());
            $arrjson = Util::error_general('Error al reasignar preguntas.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function saveBatch($rqst)
    {
        $preguntasJson = isset($rqst['preguntas']) ? $rqst['preguntas'] : '';

        if (empty($preguntasJson)) {
            return Util::error_missing_data_description('No se recibieron preguntas para guardar');
        }

        $preguntas = json_decode($preguntasJson, true);
        if ($preguntas === null || !is_array($preguntas)) {
            return Util::error_general('Error al decodificar las preguntas: ' . json_last_error_msg());
        }

        if (count($preguntas) === 0) {
            return Util::error_missing_data_description('El array de preguntas está vacío');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $preguntasGuardadas = 0;
        $errores = [];

        try {
            $pdo->beginTransaction();

            foreach ($preguntas as $index => $preguntaData) {
                $preguntaNum = $index + 1;

                if (empty($preguntaData['tbl_ficha_tecnica_encuesta_id']) || empty($preguntaData['texto_pregunta'])) {
                    $errores[] = "Pregunta $preguntaNum: Faltan datos requeridos";
                    continue;
                }

                $id = isset($preguntaData['id']) ? intval($preguntaData['id']) : 0;
                $tbl_ficha_tecnica_encuesta_id = intval($preguntaData['tbl_ficha_tecnica_encuesta_id']);
                $texto_pregunta = trim($preguntaData['texto_pregunta']);
                $tipo_pregunta = isset($preguntaData['tipo_pregunta']) ? trim($preguntaData['tipo_pregunta']) : null;
                $orden = isset($preguntaData['orden']) && $preguntaData['orden'] !== '' ? intval($preguntaData['orden']) : null;
                $limite_respuesta_multiple = isset($preguntaData['limite_respuesta_multiple']) ? intval($preguntaData['limite_respuesta_multiple']) : 1;
                $tbl_usuario_id = isset($_SESSION['session_user']['id']) ? intval($_SESSION['session_user']['id']) : 1;
                $opciones_respuesta = isset($preguntaData['opciones']) && is_array($preguntaData['opciones']) ? $preguntaData['opciones'] : [];
                $esTipoSinOpcionesPropias = in_array($tipo_pregunta, ['Grilla', 'Lista_desde_grilla', 'Texto Libre', 'Abierta'], true);

                $tieneRespuestas = false;

                if ($id > 0) {
                    $stAct = $pdo->prepare("SELECT tipo_pregunta, orden FROM " . $db->getTable('tbl_preguntas') . " WHERE id = :id LIMIT 1");
                    $stAct->execute([':id' => $id]);
                    $pregActual = $stAct->fetch(PDO::FETCH_ASSOC);
                    if (!$pregActual) {
                        $errores[] = "Pregunta $preguntaNum: ID inexistente";
                        continue;
                    }

                    $stResp = $pdo->prepare("SELECT COUNT(*) FROM " . $db->getTable('tbl_cuestionario_respuestas') . " WHERE tbl_pregunta_id = :id");
                    $stResp->execute([':id' => $id]);
                    $tieneRespuestas = ((int)$stResp->fetchColumn()) > 0;

                    if ($tieneRespuestas && $tipo_pregunta !== null && $pregActual['tipo_pregunta'] !== $tipo_pregunta) {
                        throw new RuntimeException("Pregunta $preguntaNum: ya tiene respuestas registradas y no se puede cambiar su tipo.");
                    }

                    if ($orden === null) {
                        $orden = $pregActual['orden'] !== null ? (int)$pregActual['orden'] : null;
                    }

                    $qUpdate = "UPDATE " . $db->getTable('tbl_preguntas') . "
                               SET tbl_ficha_tecnica_encuesta_id = :tbl_ficha_tecnica_encuesta_id,
                                   texto_pregunta = :texto_pregunta,
                                   tipo_pregunta = :tipo_pregunta,
                                   orden = :orden,
                                   limite_respuesta_multiple = :limite_respuesta_multiple
                               WHERE id = :id";

                    $stmtUpdate = $pdo->prepare($qUpdate);
                    $stmtUpdate->execute([
                        ':id' => $id,
                        ':tbl_ficha_tecnica_encuesta_id' => $tbl_ficha_tecnica_encuesta_id,
                        ':texto_pregunta' => $texto_pregunta,
                        ':tipo_pregunta' => $tipo_pregunta,
                        ':orden' => $orden,
                        ':limite_respuesta_multiple' => $limite_respuesta_multiple
                    ]);

                    $pregunta_id = $id;
                } else {
                    if ($orden === null || $orden <= 0) {
                        $stMax = $pdo->prepare("SELECT COALESCE(MAX(orden), 0) + 1 FROM " . $db->getTable('tbl_preguntas') . " WHERE tbl_ficha_tecnica_encuesta_id = :fid AND eliminado = 0");
                        $stMax->execute([':fid' => $tbl_ficha_tecnica_encuesta_id]);
                        $orden = (int)$stMax->fetchColumn();
                    }

                    $enunciado_pregunta = isset($preguntaData['enunciado_pregunta']) ? trim($preguntaData['enunciado_pregunta']) : null;
                    $numeral            = isset($preguntaData['numeral'])         ? trim($preguntaData['numeral'])         : null;
                    $texto_adicional    = isset($preguntaData['texto_adicional']) ? trim($preguntaData['texto_adicional']) : null;
                    $capitulo           = isset($preguntaData['capitulo'])        ? trim($preguntaData['capitulo'])        : null;
                    $habilitado         = isset($preguntaData['habilitado'])      ? trim($preguntaData['habilitado'])      : 'si';
                    $visualizacion      = isset($preguntaData['visualizacion'])   ? trim($preguntaData['visualizacion'])   : 'si';

                    $qInsert = "INSERT INTO " . $db->getTable('tbl_preguntas') . "
                               (tbl_ficha_tecnica_encuesta_id, texto_pregunta, enunciado_pregunta, numeral, texto_adicional, capitulo,
                                tipo_pregunta, orden, limite_respuesta_multiple, tbl_usuario_id, dtcreate, habilitado, visualizacion)
                               VALUES (:tbl_ficha_tecnica_encuesta_id, :texto_pregunta, :enunciado_pregunta, :numeral, :texto_adicional, :capitulo,
                                       :tipo_pregunta, :orden, :limite_respuesta_multiple, :tbl_usuario_id, NOW(), :habilitado, :visualizacion)";

                    $stmtInsert = $pdo->prepare($qInsert);
                    $stmtInsert->execute([
                        ':tbl_ficha_tecnica_encuesta_id' => $tbl_ficha_tecnica_encuesta_id,
                        ':texto_pregunta' => $texto_pregunta,
                        ':enunciado_pregunta' => $enunciado_pregunta,
                        ':numeral' => $numeral !== '' ? $numeral : null,
                        ':texto_adicional' => $texto_adicional !== '' ? $texto_adicional : null,
                        ':capitulo' => $capitulo !== '' ? $capitulo : null,
                        ':tipo_pregunta' => $tipo_pregunta,
                        ':orden' => $orden,
                        ':limite_respuesta_multiple' => $limite_respuesta_multiple,
                        ':tbl_usuario_id' => $tbl_usuario_id,
                        ':habilitado' => $habilitado,
                        ':visualizacion' => $visualizacion
                    ]);

                    $pregunta_id = (int)$pdo->lastInsertId();
                }

                if (!empty($opciones_respuesta) || !$esTipoSinOpcionesPropias) {
                    self::sincronizarOpcionesPregunta($pdo, $db, (int)$pregunta_id, $opciones_respuesta, $tieneRespuestas);
                }

                $preguntasGuardadas++;
            }

            $pdo->commit();

            if (count($errores) > 0) {
                $mensaje = "$preguntasGuardadas pregunta(s) guardada(s). Errores: " . implode(', ', $errores);
                $arrjson = array('output' => array('valid' => true, 'response' => $mensaje));
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => "$preguntasGuardadas pregunta(s) guardada(s) correctamente"));
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Pregunta::saveBatch error: ' . $e->getMessage());
            error_log('Pregunta: ' . $e->getMessage());
            $arrjson = Util::error_general('Ocurrió un error al procesar la solicitud. Intente de nuevo.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function renameCapitulo($rqst)
    {
        $fichaId      = isset($rqst['ficha_id'])       ? intval($rqst['ficha_id'])           : 0;
        $capituloViejo = isset($rqst['capitulo_viejo']) ? trim($rqst['capitulo_viejo'])       : '';
        $capituloNuevo = isset($rqst['capitulo_nuevo']) ? trim($rqst['capitulo_nuevo'])       : '';

        if (!$fichaId || $capituloViejo === '' || $capituloNuevo === '') {
            return Util::error_missing_data_description('Datos incompletos para renombrar el capítulo.');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET capitulo = :nuevo
                  WHERE tbl_ficha_tecnica_encuesta_id = :ficha AND TRIM(capitulo) = TRIM(:viejo) AND eliminado = 0";
            $stmt = $pdo->prepare($q);
            $stmt->execute([':nuevo' => $capituloNuevo, ':ficha' => $fichaId, ':viejo' => $capituloViejo]);
            $affected = $stmt->rowCount();
        } catch (Exception $e) {
            $db->closeConect();
            error_log('Pregunta::renameCapitulo error: ' . $e->getMessage());
            return Util::error_general('Error al renombrar el capítulo.');
        }

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => "$affected pregunta(s) actualizada(s)."]];
    }

    public static function updateNumeralAdicional($rqst)
    {
        $idsRaw          = isset($rqst['ids'])             ? $rqst['ids']             : '';
        $numeral         = isset($rqst['numeral'])         ? trim($rqst['numeral'])   : null;
        $texto_adicional = isset($rqst['texto_adicional']) ? trim($rqst['texto_adicional']) : null;

        if (is_array($idsRaw)) {
            $ids = array_map('intval', $idsRaw);
        } else {
            $ids = array_filter(array_map('intval', explode(',', $idsRaw)));
        }
        $ids = array_values(array_filter($ids, fn($v) => $v > 0));

        if (empty($ids)) {
            return Util::error_missing_data_description('No se proporcionaron IDs de preguntas.');
        }

        $db  = new DbConection();
        $pdo = $db->openConect();

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $q = "UPDATE " . $db->getTable('tbl_preguntas') . "
                  SET numeral = ?, texto_adicional = ?
                  WHERE id IN ($placeholders)";

            $params = [
                ($numeral         !== '' ? $numeral         : null),
                ($texto_adicional !== '' ? $texto_adicional : null),
            ];
            foreach ($ids as $id) { $params[] = $id; }

            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $affected = $stmt->rowCount();

        } catch (Exception $e) {
            $db->closeConect();
            error_log('Pregunta::updateNumeralAdicional error: ' . $e->getMessage());
            return Util::error_general('Error al actualizar numeral.');
        }

        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => "$affected pregunta(s) actualizada(s)."]];
    }
}