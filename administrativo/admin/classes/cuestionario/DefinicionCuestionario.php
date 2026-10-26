<?php

class DefinicionCuestionario
{
    const MOTOR_VERSION = '1.0.0';

    public static function cargar(PDO $pdo, int $fichaId, string $dbName = ''): ?array
    {
        if ($fichaId <= 0) {
            return null;
        }

        if ($dbName === '') {
            $dbName = (new DbConection())->getDbName();
        }

        $prefix = $dbName !== '' ? ($dbName . '.') : '';
        $tblFichas = $prefix . 'tbl_ficha_tecnica_encuestas';
        $tblPreguntas = $prefix . 'tbl_preguntas';
        $tblOpciones = $prefix . 'tbl_opciones_respuesta';
        $tblExcl = $prefix . 'tbl_preguntas_lista_exclusiones';
        $tblVar = $prefix . 'tbl_preguntas_grilla_variantes';
        $tblPlantillas = $prefix . 'tbl_grilla_plantillas';
        $tblFilas = $prefix . 'tbl_grilla_filas';
        $tblCols = $prefix . 'tbl_grilla_columnas';
        $tblColOpts = $prefix . 'tbl_grilla_columna_opciones';
        $tblZonas = $prefix . 'tbl_zonas_muestreo';
        $tblEquiv = $prefix . 'tbl_equivalencias_votante';
        $tblIntentos = $prefix . 'tbl_cuestionario_intentos';

        $stmtF = $pdo->prepare("SELECT
                id,
                realizada_por_o_encomendada_por,
                temas_concretos,
                tipo_estudio,
                habilitado,
                pregunta_destacada_id,
                COALESCE(pedir_municipio_zona_inicio, 1) AS pedir_municipio_zona_inicio,
                COALESCE(audio_obligatorio, 1) AS audio_obligatorio,
                COALESCE(certificar_terminadas_filtro, 1) AS certificar_terminadas_filtro,
                COALESCE(mostrar_resultados_publicos, 1) AS mostrar_resultados_publicos,
                COALESCE(bloquear_cuota_completa, 0) AS bloquear_cuota_completa,
                mensaje_cierre_defecto
            FROM {$tblFichas}
            WHERE id = :id
            LIMIT 1");
        $stmtF->execute([':id' => $fichaId]);
        $ficha = $stmtF->fetch(PDO::FETCH_ASSOC);
        if (!$ficha) {
            return null;
        }

        $stmtP = $pdo->prepare("SELECT
                p.id,
                p.tbl_ficha_tecnica_encuesta_id,
                p.texto_pregunta,
                p.enunciado_pregunta,
                p.texto_adicional,
                p.capitulo,
                p.numeral,
                p.tipo_pregunta,
                p.limite_respuesta_multiple,
                p.orden,
                COALESCE(p.obligatoria, 1) AS obligatoria,
                COALESCE(p.es_filtro, 0) AS es_filtro,
                p.libreto_encuestador,
                p.nota_tabulacion,
                p.tbl_grilla_plantilla_id,
                p.visible_si_pregunta_id,
                p.visible_si_opcion_id,
                p.lista_origen_pregunta_id,
                p.lista_origen_columna_id,
                p.lista_origen_opcion_id,
                COALESCE(p.si_lista_vacia, 'omitir') AS si_lista_vacia,
                COALESCE(p.es_variable_cruce, 0) AS es_variable_cruce,
                p.etiqueta_cruce,
                p.copiar_a_votante_campo,
                COALESCE(p.rol_resultado, 'ninguno') AS rol_resultado,
                p.habilitado
            FROM {$tblPreguntas} p
            WHERE p.tbl_ficha_tecnica_encuesta_id = :fid
              AND p.eliminado = 0
              AND p.habilitado = 'si'
            ORDER BY COALESCE(p.orden, 999999) ASC, p.id ASC");
        $stmtP->execute([':fid' => $fichaId]);
        $preguntasRows = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        $pregIds = array_map(static function ($r) {
            return (int)$r['id'];
        }, $preguntasRows);

        $opcionesPorPregunta = [];
        $exclusionesPorPregunta = [];
        $variantesPorPregunta = [];
        $plantillaIdsRequeridas = [];

        if (!empty($pregIds)) {
            $inP = implode(',', $pregIds);

            $stmtO = $pdo->query("SELECT
                    id,
                    tbl_pregunta_id,
                    texto_opcion,
                    orden,
                    valor_numerico,
                    COALESCE(cuenta_como, 'ninguno') AS cuenta_como,
                    COALESCE(accion, 'continuar') AS accion,
                    salto_destino_pregunta_id,
                    mensaje_terminar,
                    COALESCE(es_opcion_fija, 0) AS es_opcion_fija
                FROM {$tblOpciones}
                WHERE tbl_pregunta_id IN ({$inP})
                  AND eliminado = 0
                ORDER BY COALESCE(orden, 999999) ASC, id ASC");
            foreach ($stmtO->fetchAll(PDO::FETCH_ASSOC) as $op) {
                $pid = (int)$op['tbl_pregunta_id'];
                $opcionesPorPregunta[$pid][] = [
                    'id' => (int)$op['id'],
                    'texto' => (string)$op['texto_opcion'],
                    'orden' => (int)$op['orden'],
                    'valor_numerico' => $op['valor_numerico'] !== null ? (float)$op['valor_numerico'] : null,
                    'cuenta_como' => (string)$op['cuenta_como'],
                    'accion' => (string)$op['accion'],
                    'salto_destino_pregunta_id' => $op['salto_destino_pregunta_id'] !== null ? (int)$op['salto_destino_pregunta_id'] : null,
                    'mensaje_terminar' => $op['mensaje_terminar'] !== null ? (string)$op['mensaje_terminar'] : null,
                    'es_opcion_fija' => (int)$op['es_opcion_fija'],
                ];
            }

            $stmtE = $pdo->query("SELECT tbl_pregunta_id, tbl_pregunta_excluida_id
                                  FROM {$tblExcl}
                                  WHERE tbl_pregunta_id IN ({$inP})
                                  ORDER BY id ASC");
            foreach ($stmtE->fetchAll(PDO::FETCH_ASSOC) as $ex) {
                $pid = (int)$ex['tbl_pregunta_id'];
                $exclusionesPorPregunta[$pid][] = (int)$ex['tbl_pregunta_excluida_id'];
            }

            $stmtV = $pdo->query("SELECT tbl_pregunta_id, codigo_municipio, tbl_grilla_plantilla_id
                                  FROM {$tblVar}
                                  WHERE tbl_pregunta_id IN ({$inP})
                                  ORDER BY codigo_municipio ASC");
            foreach ($stmtV->fetchAll(PDO::FETCH_ASSOC) as $va) {
                $pid = (int)$va['tbl_pregunta_id'];
                $codM = trim((string)$va['codigo_municipio']);
                $plId = (int)$va['tbl_grilla_plantilla_id'];
                $variantesPorPregunta[$pid][$codM] = $plId;
                if ($plId > 0) {
                    $plantillaIdsRequeridas[$plId] = $plId;
                }
            }
        }

        foreach ($preguntasRows as $pr) {
            if (!empty($pr['tbl_grilla_plantilla_id'])) {
                $plId = (int)$pr['tbl_grilla_plantilla_id'];
                $plantillaIdsRequeridas[$plId] = $plId;
            }
        }

        $plantillasMap = [];
        if (!empty($plantillaIdsRequeridas)) {
            $inPl = implode(',', array_map('intval', array_values($plantillaIdsRequeridas)));
            $stmtPl = $pdo->query("SELECT id, nombre, descripcion, libreto_encuestador, nota_tabulacion, tipo, origen_filas, presentacion
                                   FROM {$tblPlantillas}
                                   WHERE id IN ({$inPl}) AND eliminado = 0");
            foreach ($stmtPl->fetchAll(PDO::FETCH_ASSOC) as $pl) {
                $plId = (int)$pl['id'];
                $plantillasMap[$plId] = [
                    'id' => $plId,
                    'nombre' => (string)$pl['nombre'],
                    'descripcion' => $pl['descripcion'],
                    'libreto_encuestador' => $pl['libreto_encuestador'],
                    'nota_tabulacion' => $pl['nota_tabulacion'],
                    'tipo' => (string)$pl['tipo'],
                    'presentacion' => (string)$pl['presentacion'],
                    'filas' => [],
                    'columnas' => [],
                ];
            }

            if (!empty($plantillasMap)) {
                $inPlValid = implode(',', array_keys($plantillasMap));
                $stmtFl = $pdo->query("SELECT id, tbl_grilla_plantilla_id, codigo, etiqueta, subtitulo, tbl_participante_id, foto, orden
                                       FROM {$tblFilas}
                                       WHERE tbl_grilla_plantilla_id IN ({$inPlValid}) AND eliminado = 0
                                       ORDER BY orden ASC, id ASC");
                foreach ($stmtFl->fetchAll(PDO::FETCH_ASSOC) as $fl) {
                    $plId = (int)$fl['tbl_grilla_plantilla_id'];
                    $plantillasMap[$plId]['filas'][] = [
                        'id' => (int)$fl['id'],
                        'codigo' => $fl['codigo'],
                        'etiqueta' => (string)$fl['etiqueta'],
                        'subtitulo' => $fl['subtitulo'],
                        'tbl_participante_id' => $fl['tbl_participante_id'] !== null ? (int)$fl['tbl_participante_id'] : null,
                        'foto' => $fl['foto'],
                        'orden' => (int)$fl['orden'],
                    ];
                }

                $stmtCl = $pdo->query("SELECT id, tbl_grilla_plantilla_id, codigo, titulo, libreto_encuestador, nota_tabulacion,
                                              tipo_respuesta, limite_multiple, obligatoria, condicion_columna_id, condicion_opcion_id,
                                              mensaje_bloqueo, rol_resultado, orden
                                       FROM {$tblCols}
                                       WHERE tbl_grilla_plantilla_id IN ({$inPlValid}) AND eliminado = 0
                                       ORDER BY orden ASC, id ASC");
                $allCols = $stmtCl->fetchAll(PDO::FETCH_ASSOC);
                $colIds = array_map(static function ($c) {
                    return (int)$c['id'];
                }, $allCols);

                $optsByCol = [];
                if (!empty($colIds)) {
                    $inCols = implode(',', $colIds);
                    $stmtCo = $pdo->query("SELECT id, tbl_grilla_columna_id, codigo, texto, valor_numerico, cuenta_como, orden
                                           FROM {$tblColOpts}
                                           WHERE tbl_grilla_columna_id IN ({$inCols}) AND eliminado = 0
                                           ORDER BY orden ASC, id ASC");
                    foreach ($stmtCo->fetchAll(PDO::FETCH_ASSOC) as $co) {
                        $cid = (int)$co['tbl_grilla_columna_id'];
                        $optsByCol[$cid][] = [
                            'id' => (int)$co['id'],
                            'codigo' => $co['codigo'],
                            'texto' => (string)$co['texto'],
                            'valor_numerico' => $co['valor_numerico'] !== null ? (float)$co['valor_numerico'] : null,
                            'cuenta_como' => (string)$co['cuenta_como'],
                            'orden' => (int)$co['orden'],
                        ];
                    }
                }

                foreach ($allCols as $c) {
                    $plId = (int)$c['tbl_grilla_plantilla_id'];
                    $cid = (int)$c['id'];
                    $plantillasMap[$plId]['columnas'][] = [
                        'id' => $cid,
                        'codigo' => $c['codigo'],
                        'titulo' => (string)$c['titulo'],
                        'libreto_encuestador' => $c['libreto_encuestador'],
                        'nota_tabulacion' => $c['nota_tabulacion'],
                        'tipo_respuesta' => (string)$c['tipo_respuesta'],
                        'limite_multiple' => (int)$c['limite_multiple'],
                        'obligatoria' => (int)$c['obligatoria'],
                        'condicion_columna_id' => $c['condicion_columna_id'] !== null ? (int)$c['condicion_columna_id'] : null,
                        'condicion_opcion_id' => $c['condicion_opcion_id'] !== null ? (int)$c['condicion_opcion_id'] : null,
                        'mensaje_bloqueo' => (string)($c['mensaje_bloqueo'] ?? ''),
                        'rol_resultado' => (string)$c['rol_resultado'],
                        'orden' => (int)$c['orden'],
                        'opciones' => $optsByCol[$cid] ?? [],
                    ];
                }
            }
        }

        $preguntas = [];
        foreach ($preguntasRows as $idx => $pr) {
            $pid = (int)$pr['id'];
            $preguntas[] = [
                'id' => $pid,
                'orden' => $pr['orden'] !== null ? (int)$pr['orden'] : ($idx + 1),
                'capitulo' => $pr['capitulo'],
                'numeral' => $pr['numeral'],
                'enunciado' => $pr['enunciado_pregunta'],
                'enunciado_pregunta' => $pr['enunciado_pregunta'],
                'texto_adicional' => $pr['texto_adicional'],
                'enunciado_adicional' => $pr['texto_adicional'],
                'texto_pregunta' => (string)$pr['texto_pregunta'],
                'tipo_pregunta' => (string)$pr['tipo_pregunta'],
                'limite_respuesta_multiple' => max(1, (int)($pr['limite_respuesta_multiple'] ?: 1)),
                'obligatoria' => (int)$pr['obligatoria'],
                'es_filtro' => (int)$pr['es_filtro'],
                'libreto_encuestador' => $pr['libreto_encuestador'],
                'nota_tabulacion' => $pr['nota_tabulacion'],
                'tbl_grilla_plantilla_id' => $pr['tbl_grilla_plantilla_id'] !== null ? (int)$pr['tbl_grilla_plantilla_id'] : null,
                'variantes_municipio' => $variantesPorPregunta[$pid] ?? new stdClass(),
                'visible_si_pregunta_id' => $pr['visible_si_pregunta_id'] !== null ? (int)$pr['visible_si_pregunta_id'] : null,
                'visible_si_opcion_id' => $pr['visible_si_opcion_id'] !== null ? (int)$pr['visible_si_opcion_id'] : null,
                'lista_origen_pregunta_id' => $pr['lista_origen_pregunta_id'] !== null ? (int)$pr['lista_origen_pregunta_id'] : null,
                'lista_origen_columna_id' => $pr['lista_origen_columna_id'] !== null ? (int)$pr['lista_origen_columna_id'] : null,
                'lista_origen_opcion_id' => $pr['lista_origen_opcion_id'] !== null ? (int)$pr['lista_origen_opcion_id'] : null,
                'si_lista_vacia' => (string)$pr['si_lista_vacia'],
                'exclusiones_pregunta_ids' => $exclusionesPorPregunta[$pid] ?? [],
                'es_variable_cruce' => (int)$pr['es_variable_cruce'],
                'etiqueta_cruce' => $pr['etiqueta_cruce'],
                'copiar_a_votante_campo' => $pr['copiar_a_votante_campo'],
                'rol_resultado' => (string)$pr['rol_resultado'],
                'opciones' => $opcionesPorPregunta[$pid] ?? [],
            ];
        }

        $stmtZ = $pdo->prepare("SELECT
                z.id,
                z.codigo_departamento,
                z.codigo_municipio,
                z.tipo,
                z.codigo,
                z.nombre,
                z.cuota,
                z.orden,
                (SELECT COUNT(*) FROM {$tblIntentos} i
                 WHERE i.tbl_ficha_tecnica_encuesta_id = z.tbl_ficha_tecnica_encuesta_id
                   AND i.tbl_zona_muestreo_id = z.id
                   AND i.es_prueba = 0
                   AND i.estado = 'completo') AS realizadas
            FROM {$tblZonas} z
            WHERE z.tbl_ficha_tecnica_encuesta_id = :fid
              AND z.eliminado = 0
              AND z.habilitado = 'si'
            ORDER BY z.codigo_municipio ASC, z.orden ASC, z.id ASC");
        $stmtZ->execute([':fid' => $fichaId]);
        $zonas = [];
        foreach ($stmtZ->fetchAll(PDO::FETCH_ASSOC) as $z) {
            $cuota = (int)$z['cuota'];
            $realizadas = (int)$z['realizadas'];
            $zonas[] = [
                'id' => (int)$z['id'],
                'codigo_departamento' => $z['codigo_departamento'],
                'codigo_municipio' => (string)$z['codigo_municipio'],
                'tipo' => (string)$z['tipo'],
                'codigo' => $z['codigo'],
                'nombre' => (string)$z['nombre'],
                'cuota' => $cuota,
                'realizadas' => $realizadas,
                'restantes' => max(0, $cuota - $realizadas),
                'orden' => (int)$z['orden'],
            ];
        }

        $stmtEq = $pdo->prepare("SELECT tbl_pregunta_id, tbl_opcion_respuesta_id, campo_votante, valor_votante
                                 FROM {$tblEquiv}
                                 WHERE tbl_ficha_tecnica_encuesta_id = :fid");
        $stmtEq->execute([':fid' => $fichaId]);
        $equivalencias = $stmtEq->fetchAll(PDO::FETCH_ASSOC);

        $estructuraHashSource = [
            'ficha_id' => (int)$ficha['id'],
            'preguntas' => $preguntas,
            'plantillas' => $plantillasMap,
        ];
        $definicionHash = substr(hash('sha256', json_encode($estructuraHashSource, JSON_UNESCAPED_UNICODE)), 0, 16);

        return [
            'motor_version' => self::MOTOR_VERSION,
            'version' => $definicionHash,
            'definicion_hash' => $definicionHash,
            'ficha' => [
                'id' => (int)$ficha['id'],
                'titulo' => (string)($ficha['realizada_por_o_encomendada_por'] ?: ('Encuesta #' . $ficha['id'])),
                'temas' => $ficha['temas_concretos'],
                'pregunta_destacada_id' => $ficha['pregunta_destacada_id'] !== null ? (int)$ficha['pregunta_destacada_id'] : null,
                'pedir_municipio_zona_inicio' => (int)$ficha['pedir_municipio_zona_inicio'],
                'audio_obligatorio' => (int)$ficha['audio_obligatorio'],
                'certificar_terminadas_filtro' => (int)$ficha['certificar_terminadas_filtro'],
                'mostrar_resultados_publicos' => (int)$ficha['mostrar_resultados_publicos'],
                'bloquear_cuota_completa' => (int)$ficha['bloquear_cuota_completa'],
                'mensaje_cierre_defecto' => $ficha['mensaje_cierre_defecto'] ?: 'Muchas gracias por su tiempo. Hemos terminado la encuesta.',
            ],
            'preguntas' => $preguntas,
            'plantillas' => !empty($plantillasMap) ? $plantillasMap : new stdClass(),
            'zonas_muestreo' => $zonas,
            'equivalencias_votante' => $equivalencias,
        ];
    }
}
