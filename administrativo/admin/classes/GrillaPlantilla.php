<?php

class GrillaPlantilla
{
    public static function getAll($rqst = [])
    {
        $soloHabilitados = !empty($rqst['solo_habilitados']);
        $tipo = isset($rqst['tipo']) ? trim((string)$rqst['tipo']) : '';

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $tblP = $db->getTable('tbl_grilla_plantillas');
            $tblF = $db->getTable('tbl_grilla_filas');
            $tblC = $db->getTable('tbl_grilla_columnas');
            $tblCp = $db->getTable('tbl_cargos_publicos');
            $tblDep = $db->getTable('tbl_departamentos');
            $tblCiu = $db->getTable('tbl_ciudades');
            $tblPreg = $db->getTable('tbl_preguntas');
            $tblVar = $db->getTable('tbl_preguntas_grilla_variantes');
            $tblResp = $db->getTable('tbl_cuestionario_respuestas');
            $tblInt = $db->getTable('tbl_cuestionario_intentos');

            $where = ["p.eliminado = 0"];
            $params = [];

            if ($soloHabilitados) {
                $where[] = "p.habilitado = 'si'";
            }
            if ($tipo !== '' && in_array($tipo, ['candidatos', 'escala', 'libre'], true)) {
                $where[] = "p.tipo = :tipo";
                $params[':tipo'] = $tipo;
            }

            $sql = "SELECT
                        p.id,
                        p.nombre,
                        p.descripcion,
                        p.libreto_encuestador,
                        p.nota_tabulacion,
                        p.tipo,
                        p.origen_filas,
                        p.tbl_cargo_publico_id,
                        p.codigo_departamento,
                        p.codigo_municipio,
                        p.presentacion,
                        p.habilitado,
                        p.dtcreate,
                        p.dtupdate,
                        cp.nombre AS cargo_nombre,
                        d.departamento AS departamento_nombre,
                        ciu.municipio AS municipio_nombre,
                        (SELECT COUNT(*) FROM {$tblF} f WHERE f.tbl_grilla_plantilla_id = p.id AND f.eliminado = 0) AS total_filas,
                        (SELECT COUNT(*) FROM {$tblC} c WHERE c.tbl_grilla_plantilla_id = p.id AND c.eliminado = 0) AS total_columnas
                    FROM {$tblP} p
                    LEFT JOIN {$tblCp} cp ON p.tbl_cargo_publico_id = cp.id
                    LEFT JOIN {$tblDep} d ON p.codigo_departamento = d.codigo_departamento
                    LEFT JOIN {$tblCiu} ciu ON p.codigo_municipio = ciu.codigo_muncipio
                    WHERE " . implode(' AND ', $where) . "
                    ORDER BY p.id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as &$r) {
                $pid = (int)$r['id'];
                $uso = self::consultarUsoInterno($pdo, $db, $pid);
                $r['total_preguntas_uso'] = $uso['total_preguntas'];
                $r['total_respuestas'] = $uso['total_respuestas'];
                $r['preguntas_uso'] = $uso['preguntas'];
            }
            unset($r);

            return ['output' => ['valid' => true, 'response' => $rows]];
        } catch (PDOException $e) {
            error_log('GrillaPlantilla::getAll error: ' . $e->getMessage());
            return Util::error_general('Error al consultar las tablas de candidatos.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getDetalle($rqst = [])
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $plantilla = self::cargarPlantillaCompleta($pdo, $db, $id);
            if (!$plantilla) {
                return Util::error_no_result();
            }
            $plantilla['uso'] = self::consultarUsoInterno($pdo, $db, $id);
            return ['output' => ['valid' => true, 'response' => $plantilla]];
        } catch (PDOException $e) {
            error_log('GrillaPlantilla::getDetalle error: ' . $e->getMessage());
            return Util::error_general('Error al cargar el detalle de la tabla de candidatos.');
        } finally {
            $db->closeConect();
        }
    }

    public static function cargarPlantillaCompleta(PDO $pdo, DbConection $db, int $id): ?array
    {
        $tblP = $db->getTable('tbl_grilla_plantillas');
        $tblF = $db->getTable('tbl_grilla_filas');
        $tblC = $db->getTable('tbl_grilla_columnas');
        $tblO = $db->getTable('tbl_grilla_columna_opciones');

        $stmt = $pdo->prepare("SELECT * FROM {$tblP} WHERE id = :id AND eliminado = 0 LIMIT 1");
        $stmt->execute([':id' => $id]);
        $plantilla = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$plantilla) {
            return null;
        }

        $stmtF = $pdo->prepare("SELECT id, tbl_grilla_plantilla_id, codigo, etiqueta, subtitulo, tbl_participante_id, foto, orden
                                FROM {$tblF}
                                WHERE tbl_grilla_plantilla_id = :id AND eliminado = 0
                                ORDER BY orden ASC, id ASC");
        $stmtF->execute([':id' => $id]);
        $plantilla['filas'] = $stmtF->fetchAll(PDO::FETCH_ASSOC);

        $stmtC = $pdo->prepare("SELECT id, tbl_grilla_plantilla_id, codigo, titulo, libreto_encuestador, nota_tabulacion,
                                       tipo_respuesta, limite_multiple, obligatoria, condicion_columna_id, condicion_opcion_id,
                                       mensaje_bloqueo, rol_resultado, orden
                                FROM {$tblC}
                                WHERE tbl_grilla_plantilla_id = :id AND eliminado = 0
                                ORDER BY orden ASC, id ASC");
        $stmtC->execute([':id' => $id]);
        $columnas = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($columnas)) {
            $colIds = array_map(static function ($c) {
                return (int)$c['id'];
            }, $columnas);
            $inCols = implode(',', $colIds);
            $stmtO = $pdo->query("SELECT id, tbl_grilla_columna_id, codigo, texto, valor_numerico, cuenta_como, orden
                                  FROM {$tblO}
                                  WHERE tbl_grilla_columna_id IN ({$inCols}) AND eliminado = 0
                                  ORDER BY orden ASC, id ASC");
            $optsByCol = [];
            foreach ($stmtO->fetchAll(PDO::FETCH_ASSOC) as $op) {
                $cid = (int)$op['tbl_grilla_columna_id'];
                $optsByCol[$cid][] = $op;
            }
            foreach ($columnas as &$col) {
                $cid = (int)$col['id'];
                $col['opciones'] = $optsByCol[$cid] ?? [];
            }
            unset($col);
        }

        $plantilla['columnas'] = $columnas;
        return $plantilla;
    }

    private static function consultarUsoInterno(PDO $pdo, DbConection $db, int $plantillaId): array
    {
        $tblPreg = $db->getTable('tbl_preguntas');
        $tblVar = $db->getTable('tbl_preguntas_grilla_variantes');
        $tblFichas = $db->getTable('tbl_ficha_tecnica_encuestas');
        $tblResp = $db->getTable('tbl_cuestionario_respuestas');
        $tblInt = $db->getTable('tbl_cuestionario_intentos');
        $tblFilas = $db->getTable('tbl_grilla_filas');
        $tblCols = $db->getTable('tbl_grilla_columnas');

        $sqlPreg = "SELECT DISTINCT p.id, p.texto_pregunta, p.tbl_ficha_tecnica_encuesta_id, f.realizada_por_o_encomendada_por
                    FROM {$tblPreg} p
                    LEFT JOIN {$tblFichas} f ON p.tbl_ficha_tecnica_encuesta_id = f.id
                    LEFT JOIN {$tblVar} v ON v.tbl_pregunta_id = p.id
                    WHERE p.eliminado = 0
                      AND (p.tbl_grilla_plantilla_id = :pid1 OR v.tbl_grilla_plantilla_id = :pid2)";
        $stmtP = $pdo->prepare($sqlPreg);
        $stmtP->execute([':pid1' => $plantillaId, ':pid2' => $plantillaId]);
        $pregs = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        $sqlResp = "SELECT COUNT(*) FROM {$tblResp} r
                    INNER JOIN {$tblInt} i ON r.tbl_intento_id = i.id AND i.es_prueba = 0
                    WHERE r.tbl_grilla_fila_id IN (SELECT id FROM {$tblFilas} WHERE tbl_grilla_plantilla_id = :pid1)
                       OR r.tbl_grilla_columna_id IN (SELECT id FROM {$tblCols} WHERE tbl_grilla_plantilla_id = :pid2)";
        $stmtR = $pdo->prepare($sqlResp);
        $stmtR->execute([':pid1' => $plantillaId, ':pid2' => $plantillaId]);
        $totalResp = (int)$stmtR->fetchColumn();

        return [
            'total_preguntas' => count($pregs),
            'total_respuestas' => $totalResp,
            'preguntas' => $pregs,
        ];
    }

    private static function nombresPreguntasEnUso(array $uso): string
    {
        $nombres = [];
        foreach (array_slice($uso['preguntas'], 0, 3) as $p) {
            $nombres[] = '«' . trim((string)$p['texto_pregunta']) . '»';
        }
        $extra = $uso['total_preguntas'] - count($nombres);
        return implode(', ', $nombres) . ($extra > 0 ? " y {$extra} más" : '');
    }

    public static function getUso($rqst = [])
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $uso = self::consultarUsoInterno($pdo, $db, $id);
            return ['output' => ['valid' => true, 'response' => $uso]];
        } catch (PDOException $e) {
            error_log('GrillaPlantilla::getUso error: ' . $e->getMessage());
            return Util::error_general('Error al verificar el uso de la tabla.');
        } finally {
            $db->closeConect();
        }
    }

    public static function getParticipantes($rqst = [])
    {
        $cargoPublicoId = isset($rqst['cargo_publico_id']) ? (int)$rqst['cargo_publico_id'] : 0;
        $codigoDep = isset($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : '';
        $codigoMun = isset($rqst['codigo_municipio']) ? trim((string)$rqst['codigo_municipio']) : '';

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $tblPart = $db->getTable('tbl_participantes');
            $tblPxp = $db->getTable('tbl_participantes_x_partidos_politicos');
            $tblPp = $db->getTable('tbl_partidos_politicos');
            $tblCp = $db->getTable('tbl_cargos_publicos');

            $where = ["p.habilitado = 'si'"];
            $params = [];

            if ($cargoPublicoId > 0) {
                $where[] = "p.tbl_cargo_publico_id = :cargo";
                $params[':cargo'] = $cargoPublicoId;
            }
            if ($codigoDep !== '') {
                $where[] = "p.codigo_departamento = :dep";
                $params[':dep'] = $codigoDep;
            }
            if ($codigoMun !== '') {
                $where[] = "p.codigo_municipio = :mun";
                $params[':mun'] = $codigoMun;
            }

            $sql = "SELECT
                        p.id,
                        p.nombre_completo,
                        p.foto,
                        p.tbl_cargo_publico_id,
                        p.codigo_departamento,
                        p.codigo_municipio,
                        cp.nombre AS cargo_publico,
                        GROUP_CONCAT(pp.nombre_partido SEPARATOR ', ') AS partidos
                    FROM {$tblPart} p
                    LEFT JOIN {$tblCp} cp ON p.tbl_cargo_publico_id = cp.id
                    LEFT JOIN {$tblPxp} pxp ON p.id = pxp.tbl_participante_id
                    LEFT JOIN {$tblPp} pp ON pxp.tbl_partido_politico_id = pp.id
                    WHERE " . implode(' AND ', $where) . "
                    GROUP BY p.id
                    ORDER BY p.nombre_completo ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['output' => ['valid' => true, 'response' => $rows]];
        } catch (PDOException $e) {
            error_log('GrillaPlantilla::getParticipantes error: ' . $e->getMessage());
            return Util::error_general('Error al obtener candidatos registrados.');
        } finally {
            $db->closeConect();
        }
    }

    public static function save($rqst = [])
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        $nombre = isset($rqst['nombre']) ? trim((string)$rqst['nombre']) : '';
        $descripcion = isset($rqst['descripcion']) ? trim((string)$rqst['descripcion']) : null;
        $libreto = isset($rqst['libreto_encuestador']) ? trim((string)$rqst['libreto_encuestador']) : null;
        $notaTab = isset($rqst['nota_tabulacion']) ? trim((string)$rqst['nota_tabulacion']) : null;
        $tipo = isset($rqst['tipo']) && in_array($rqst['tipo'], ['candidatos', 'escala', 'libre'], true) ? $rqst['tipo'] : 'candidatos';
        $origenFilas = isset($rqst['origen_filas']) && in_array($rqst['origen_filas'], ['manual', 'participantes'], true) ? $rqst['origen_filas'] : 'manual';
        $cargoId = !empty($rqst['tbl_cargo_publico_id']) ? (int)$rqst['tbl_cargo_publico_id'] : null;
        $codDep = !empty($rqst['codigo_departamento']) ? trim((string)$rqst['codigo_departamento']) : null;
        $codMun = !empty($rqst['codigo_municipio']) ? trim((string)$rqst['codigo_municipio']) : null;
        $presentacion = isset($rqst['presentacion']) && in_array($rqst['presentacion'], ['tarjetas', 'tabla', 'auto'], true) ? $rqst['presentacion'] : 'tarjetas';
        $habilitado = isset($rqst['habilitado']) && $rqst['habilitado'] === 'no' ? 'no' : 'si';
        $usuarioId = isset($_SESSION['session_user']['id']) ? (int)$_SESSION['session_user']['id'] : 1;

        $filas = $rqst['filas'] ?? [];
        if (is_string($filas)) {
            $decoded = json_decode($filas, true);
            $filas = is_array($decoded) ? $decoded : [];
        }
        $columnas = $rqst['columnas'] ?? [];
        if (is_string($columnas)) {
            $decoded = json_decode($columnas, true);
            $columnas = is_array($decoded) ? $decoded : [];
        }

        if ($nombre === '') {
            return Util::error_general('Por favor escriba un nombre para la tabla de candidatos.');
        }

        $filasLimpias = [];
        foreach ($filas as $idx => $f) {
            if (!is_array($f)) {
                continue;
            }
            $etiqueta = trim((string)($f['etiqueta'] ?? ''));
            if ($etiqueta === '') {
                continue;
            }
            $filasLimpias[] = [
                'id' => isset($f['id']) ? (int)$f['id'] : 0,
                'codigo' => !empty($f['codigo']) ? trim((string)$f['codigo']) : null,
                'etiqueta' => $etiqueta,
                'subtitulo' => !empty($f['subtitulo']) ? trim((string)$f['subtitulo']) : null,
                'tbl_participante_id' => !empty($f['tbl_participante_id']) ? (int)$f['tbl_participante_id'] : null,
                'foto' => !empty($f['foto']) ? trim((string)$f['foto']) : null,
                'orden' => count($filasLimpias) + 1,
            ];
        }

        if (empty($filasLimpias)) {
            return Util::error_general('Debe incluir al menos un candidato o renglón en la tabla.');
        }

        $columnasLimpias = [];
        foreach ($columnas as $idx => $c) {
            if (!is_array($c)) {
                continue;
            }
            $titulo = trim((string)($c['titulo'] ?? ''));
            if ($titulo === '') {
                continue;
            }
            $opts = $c['opciones'] ?? [];
            if (is_string($opts)) {
                $opts = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $opts)));
            }
            $optsLimpias = [];
            if (is_array($opts)) {
                foreach ($opts as $oIdx => $op) {
                    if (is_string($op)) {
                        $txt = trim($op);
                        if ($txt === '') {
                            continue;
                        }
                        $optsLimpias[] = [
                            'id' => 0,
                            'codigo' => (string)(count($optsLimpias) + 1),
                            'texto' => $txt,
                            'valor_numerico' => null,
                            'cuenta_como' => 'ninguno',
                            'orden' => count($optsLimpias) + 1,
                        ];
                    } elseif (is_array($op)) {
                        $txt = trim((string)($op['texto'] ?? ''));
                        if ($txt === '') {
                            continue;
                        }
                        $cuentaComo = isset($op['cuenta_como']) && in_array($op['cuenta_como'], ['ninguno', 'si', 'no', 'favorable', 'desfavorable'], true)
                            ? $op['cuenta_como']
                            : 'ninguno';
                        $valNum = isset($op['valor_numerico']) && $op['valor_numerico'] !== '' && $op['valor_numerico'] !== null
                            ? (float)$op['valor_numerico']
                            : null;
                        $optsLimpias[] = [
                            'id' => isset($op['id']) ? (int)$op['id'] : 0,
                            'codigo' => !empty($op['codigo']) ? trim((string)$op['codigo']) : (string)(count($optsLimpias) + 1),
                            'texto' => $txt,
                            'valor_numerico' => $valNum,
                            'cuenta_como' => $cuentaComo,
                            'orden' => count($optsLimpias) + 1,
                        ];
                    }
                }
            }

            if (empty($optsLimpias)) {
                return Util::error_general("La pregunta por candidato «{$titulo}» debe tener al menos una opción de respuesta.");
            }

            $rolResultado = isset($c['rol_resultado']) && in_array($c['rol_resultado'], ['ninguno', 'conocimiento', 'imagen', 'intencion', 'ideologia', 'asociacion_positiva', 'asociacion_negativa', 'escala', 'otro'], true)
                ? $c['rol_resultado']
                : 'ninguno';
            $tipoResp = isset($c['tipo_respuesta']) && $c['tipo_respuesta'] === 'multiple' ? 'multiple' : 'unica';
            $limMult = $tipoResp === 'multiple' ? max(1, (int)($c['limite_multiple'] ?? 3)) : 1;

            $columnasLimpias[] = [
                'id' => isset($c['id']) ? (int)$c['id'] : 0,
                'temp_key' => $c['temp_key'] ?? (string)count($columnasLimpias),
                'codigo' => !empty($c['codigo']) ? trim((string)$c['codigo']) : 'C' . (count($columnasLimpias) + 1),
                'titulo' => $titulo,
                'libreto_encuestador' => !empty($c['libreto_encuestador']) ? trim((string)$c['libreto_encuestador']) : null,
                'nota_tabulacion' => !empty($c['nota_tabulacion']) ? trim((string)$c['nota_tabulacion']) : null,
                'tipo_respuesta' => $tipoResp,
                'limite_multiple' => $limMult,
                'obligatoria' => isset($c['obligatoria']) ? ((int)$c['obligatoria'] ? 1 : 0) : 1,
                'condicion_columna_id' => !empty($c['condicion_columna_id']) ? (int)$c['condicion_columna_id'] : null,
                'condicion_columna_orden' => isset($c['condicion_columna_orden']) && $c['condicion_columna_orden'] !== '' ? (int)$c['condicion_columna_orden'] : null,
                'condicion_opcion_id' => !empty($c['condicion_opcion_id']) ? (int)$c['condicion_opcion_id'] : null,
                'condicion_opcion_orden' => isset($c['condicion_opcion_orden']) && $c['condicion_opcion_orden'] !== '' ? (int)$c['condicion_opcion_orden'] : null,
                'condicion_opcion_texto' => isset($c['condicion_opcion_texto']) ? trim((string)$c['condicion_opcion_texto']) : '',
                'mensaje_bloqueo' => !empty($c['mensaje_bloqueo']) ? trim((string)$c['mensaje_bloqueo']) : 'Esta pregunta no aplica. Pase al siguiente candidato.',
                'rol_resultado' => $rolResultado,
                'orden' => count($columnasLimpias) + 1,
                'opciones' => $optsLimpias,
            ];
        }

        if (empty($columnasLimpias)) {
            return Util::error_general('Debe agregar al menos una pregunta por candidato.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $tblP = $db->getTable('tbl_grilla_plantillas');
            $tblF = $db->getTable('tbl_grilla_filas');
            $tblC = $db->getTable('tbl_grilla_columnas');
            $tblO = $db->getTable('tbl_grilla_columna_opciones');

            $tieneRespuestas = false;
            if ($id > 0) {
                $uso = self::consultarUsoInterno($pdo, $db, $id);
                $tieneRespuestas = $uso['total_respuestas'] > 0;
                if ($habilitado === 'no' && $uso['total_preguntas'] > 0) {
                    return Util::error_general('No se puede deshabilitar esta tabla porque la usan estas preguntas: ' . self::nombresPreguntasEnUso($uso) . '.');
                }
            }

            $pdo->beginTransaction();

            if ($id > 0) {
                $stmtUpd = $pdo->prepare("UPDATE {$tblP}
                    SET nombre = :nombre,
                        descripcion = :descripcion,
                        libreto_encuestador = :libreto,
                        nota_tabulacion = :nota,
                        tipo = :tipo,
                        origen_filas = :origen,
                        tbl_cargo_publico_id = :cargo,
                        codigo_departamento = :dep,
                        codigo_municipio = :mun,
                        presentacion = :pres,
                        habilitado = :hab
                    WHERE id = :id AND eliminado = 0");
                $stmtUpd->execute([
                    ':nombre' => $nombre,
                    ':descripcion' => $descripcion,
                    ':libreto' => $libreto,
                    ':nota' => $notaTab,
                    ':tipo' => $tipo,
                    ':origen' => $origenFilas,
                    ':cargo' => $cargoId,
                    ':dep' => $codDep,
                    ':mun' => $codMun,
                    ':pres' => $presentacion,
                    ':hab' => $habilitado,
                    ':id' => $id,
                ]);
            } else {
                $stmtIns = $pdo->prepare("INSERT INTO {$tblP}
                    (nombre, descripcion, libreto_encuestador, nota_tabulacion, tipo, origen_filas, tbl_cargo_publico_id, codigo_departamento, codigo_municipio, presentacion, habilitado, eliminado, tbl_usuario_id)
                    VALUES (:nombre, :descripcion, :libreto, :nota, :tipo, :origen, :cargo, :dep, :mun, :pres, :hab, 0, :usr)");
                $stmtIns->execute([
                    ':nombre' => $nombre,
                    ':descripcion' => $descripcion,
                    ':libreto' => $libreto,
                    ':nota' => $notaTab,
                    ':tipo' => $tipo,
                    ':origen' => $origenFilas,
                    ':cargo' => $cargoId,
                    ':dep' => $codDep,
                    ':mun' => $codMun,
                    ':pres' => $presentacion,
                    ':hab' => $habilitado,
                    ':usr' => $usuarioId,
                ]);
                $id = (int)$pdo->lastInsertId();
            }

            $stmtExF = $pdo->prepare("SELECT id, orden FROM {$tblF} WHERE tbl_grilla_plantilla_id = :pid AND eliminado = 0 ORDER BY orden ASC, id ASC");
            $stmtExF->execute([':pid' => $id]);
            $existFilas = $stmtExF->fetchAll(PDO::FETCH_ASSOC);
            $existFilaIds = array_map(static function ($r) {
                return (int)$r['id'];
            }, $existFilas);

            if ($tieneRespuestas && count($filasLimpias) < count($existFilas)) {
                $pdo->rollBack();
                return Util::error_general('Esta tabla ya tiene respuestas registradas. No se pueden quitar candidatos; solo puede editar sus nombres o textos.');
            }

            $keptFilaIds = [];
            $filasConId = count(array_filter(array_column($filasLimpias, 'id'), static function ($v) {
                return (int)$v > 0;
            })) > 0;
            foreach ($filasLimpias as $idx => $fl) {
                $targetFilaId = 0;
                if ($fl['id'] > 0 && in_array($fl['id'], $existFilaIds, true)) {
                    $targetFilaId = $fl['id'];
                } elseif (!$filasConId && isset($existFilas[$idx]) && !in_array((int)$existFilas[$idx]['id'], $keptFilaIds, true)) {
                    $targetFilaId = (int)$existFilas[$idx]['id'];
                }

                if ($targetFilaId > 0) {
                    $stmtUF = $pdo->prepare("UPDATE {$tblF}
                        SET codigo = :cod, etiqueta = :eti, subtitulo = :sub, tbl_participante_id = :part, foto = :foto, orden = :ord, eliminado = 0
                        WHERE id = :id AND tbl_grilla_plantilla_id = :pid");
                    $stmtUF->execute([
                        ':cod' => $fl['codigo'],
                        ':eti' => $fl['etiqueta'],
                        ':sub' => $fl['subtitulo'],
                        ':part' => $fl['tbl_participante_id'],
                        ':foto' => $fl['foto'],
                        ':ord' => $fl['orden'],
                        ':id' => $targetFilaId,
                        ':pid' => $id,
                    ]);
                    $keptFilaIds[] = $targetFilaId;
                } else {
                    $stmtIF = $pdo->prepare("INSERT INTO {$tblF}
                        (tbl_grilla_plantilla_id, codigo, etiqueta, subtitulo, tbl_participante_id, foto, orden, eliminado)
                        VALUES (:pid, :cod, :eti, :sub, :part, :foto, :ord, 0)");
                    $stmtIF->execute([
                        ':pid' => $id,
                        ':cod' => $fl['codigo'],
                        ':eti' => $fl['etiqueta'],
                        ':sub' => $fl['subtitulo'],
                        ':part' => $fl['tbl_participante_id'],
                        ':foto' => $fl['foto'],
                        ':ord' => $fl['orden'],
                    ]);
                    $keptFilaIds[] = (int)$pdo->lastInsertId();
                }
            }

            $removedFilaIds = array_diff($existFilaIds, $keptFilaIds);
            if (!empty($removedFilaIds)) {
                $inRemF = implode(',', array_map('intval', $removedFilaIds));
                $pdo->exec("UPDATE {$tblF} SET eliminado = 1 WHERE id IN ({$inRemF}) AND tbl_grilla_plantilla_id = {$id}");
            }

            $stmtExC = $pdo->prepare("SELECT id, orden FROM {$tblC} WHERE tbl_grilla_plantilla_id = :pid AND eliminado = 0 ORDER BY orden ASC, id ASC");
            $stmtExC->execute([':pid' => $id]);
            $existCols = $stmtExC->fetchAll(PDO::FETCH_ASSOC);
            $existColIds = array_map(static function ($r) {
                return (int)$r['id'];
            }, $existCols);

            if ($tieneRespuestas && count($columnasLimpias) < count($existCols)) {
                $pdo->rollBack();
                return Util::error_general('Esta tabla ya tiene respuestas registradas. No se pueden quitar preguntas por candidato; solo puede editar sus textos.');
            }

            $keptColIds = [];
            $colOrdenToId = [];
            $colOptionsMap = [];
            $pendingConditions = [];

            $columnasConId = count(array_filter(array_column($columnasLimpias, 'id'), static function ($v) {
                return (int)$v > 0;
            })) > 0;
            foreach ($columnasLimpias as $idx => $cl) {
                $targetColId = 0;
                if ($cl['id'] > 0 && in_array($cl['id'], $existColIds, true)) {
                    $targetColId = $cl['id'];
                } elseif (!$columnasConId && isset($existCols[$idx]) && !in_array((int)$existCols[$idx]['id'], $keptColIds, true)) {
                    $targetColId = (int)$existCols[$idx]['id'];
                }

                if ($targetColId > 0) {
                    $stmtUC = $pdo->prepare("UPDATE {$tblC}
                        SET codigo = :cod, titulo = :tit, libreto_encuestador = :lib, nota_tabulacion = :nota,
                            tipo_respuesta = :tipo, limite_multiple = :lim, obligatoria = :obli,
                            mensaje_bloqueo = :msg, rol_resultado = :rol, orden = :ord, eliminado = 0
                        WHERE id = :id AND tbl_grilla_plantilla_id = :pid");
                    $stmtUC->execute([
                        ':cod' => $cl['codigo'],
                        ':tit' => $cl['titulo'],
                        ':lib' => $cl['libreto_encuestador'],
                        ':nota' => $cl['nota_tabulacion'],
                        ':tipo' => $cl['tipo_respuesta'],
                        ':lim' => $cl['limite_multiple'],
                        ':obli' => $cl['obligatoria'],
                        ':msg' => $cl['mensaje_bloqueo'],
                        ':rol' => $cl['rol_resultado'],
                        ':ord' => $cl['orden'],
                        ':id' => $targetColId,
                        ':pid' => $id,
                    ]);
                } else {
                    $stmtIC = $pdo->prepare("INSERT INTO {$tblC}
                        (tbl_grilla_plantilla_id, codigo, titulo, libreto_encuestador, nota_tabulacion, tipo_respuesta, limite_multiple, obligatoria, mensaje_bloqueo, rol_resultado, orden, eliminado)
                        VALUES (:pid, :cod, :tit, :lib, :nota, :tipo, :lim, :obli, :msg, :rol, :ord, 0)");
                    $stmtIC->execute([
                        ':pid' => $id,
                        ':cod' => $cl['codigo'],
                        ':tit' => $cl['titulo'],
                        ':lib' => $cl['libreto_encuestador'],
                        ':nota' => $cl['nota_tabulacion'],
                        ':tipo' => $cl['tipo_respuesta'],
                        ':lim' => $cl['limite_multiple'],
                        ':obli' => $cl['obligatoria'],
                        ':msg' => $cl['mensaje_bloqueo'],
                        ':rol' => $cl['rol_resultado'],
                        ':ord' => $cl['orden'],
                    ]);
                    $targetColId = (int)$pdo->lastInsertId();
                }

                $keptColIds[] = $targetColId;
                $colOrdenToId[$cl['orden']] = $targetColId;

                $stmtExO = $pdo->prepare("SELECT id, orden, texto FROM {$tblO} WHERE tbl_grilla_columna_id = :cid AND eliminado = 0 ORDER BY orden ASC, id ASC");
                $stmtExO->execute([':cid' => $targetColId]);
                $existOpts = $stmtExO->fetchAll(PDO::FETCH_ASSOC);
                $existOptIds = array_map(static function ($r) {
                    return (int)$r['id'];
                }, $existOpts);

                if ($tieneRespuestas && count($cl['opciones']) < count($existOpts)) {
                    $pdo->rollBack();
                    return Util::error_general("La pregunta «{$cl['titulo']}» ya tiene respuestas registradas y no se le pueden quitar opciones.");
                }

                $keptOptIds = [];
                $colOptionsMap[$targetColId] = [];

                $opcionesConId = count(array_filter(array_column($cl['opciones'], 'id'), static function ($v) {
                    return (int)$v > 0;
                })) > 0;
                foreach ($cl['opciones'] as $oIdx => $op) {
                    $targetOptId = 0;
                    if ($op['id'] > 0 && in_array($op['id'], $existOptIds, true)) {
                        $targetOptId = $op['id'];
                    } elseif (!$opcionesConId && isset($existOpts[$oIdx]) && !in_array((int)$existOpts[$oIdx]['id'], $keptOptIds, true)) {
                        $targetOptId = (int)$existOpts[$oIdx]['id'];
                    }

                    if ($targetOptId > 0) {
                        $stmtUO = $pdo->prepare("UPDATE {$tblO}
                            SET codigo = :cod, texto = :txt, valor_numerico = :val, cuenta_como = :cta, orden = :ord, eliminado = 0
                            WHERE id = :id AND tbl_grilla_columna_id = :cid");
                        $stmtUO->execute([
                            ':cod' => $op['codigo'],
                            ':txt' => $op['texto'],
                            ':val' => $op['valor_numerico'],
                            ':cta' => $op['cuenta_como'],
                            ':ord' => $op['orden'],
                            ':id' => $targetOptId,
                            ':cid' => $targetColId,
                        ]);
                    } else {
                        $stmtIO = $pdo->prepare("INSERT INTO {$tblO}
                            (tbl_grilla_columna_id, codigo, texto, valor_numerico, cuenta_como, orden, eliminado)
                            VALUES (:cid, :cod, :txt, :val, :cta, :ord, 0)");
                        $stmtIO->execute([
                            ':cid' => $targetColId,
                            ':cod' => $op['codigo'],
                            ':txt' => $op['texto'],
                            ':val' => $op['valor_numerico'],
                            ':cta' => $op['cuenta_como'],
                            ':ord' => $op['orden'],
                        ]);
                        $targetOptId = (int)$pdo->lastInsertId();
                    }

                    $keptOptIds[] = $targetOptId;
                    $colOptionsMap[$targetColId][] = [
                        'id' => $targetOptId,
                        'orden' => $op['orden'],
                        'texto' => $op['texto'],
                    ];
                }

                $removedOptIds = array_diff($existOptIds, $keptOptIds);
                if (!empty($removedOptIds)) {
                    $inRemO = implode(',', array_map('intval', $removedOptIds));
                    $pdo->exec("UPDATE {$tblO} SET eliminado = 1 WHERE id IN ({$inRemO}) AND tbl_grilla_columna_id = {$targetColId}");
                }

                $pendingConditions[] = [
                    'col_id' => $targetColId,
                    'cond_col_id' => $cl['condicion_columna_id'],
                    'cond_col_orden' => $cl['condicion_columna_orden'],
                    'cond_opt_id' => $cl['condicion_opcion_id'],
                    'cond_opt_orden' => $cl['condicion_opcion_orden'],
                    'cond_opt_texto' => $cl['condicion_opcion_texto'],
                ];
            }

            $removedColIds = array_diff($existColIds, $keptColIds);
            if (!empty($removedColIds)) {
                $inRemC = implode(',', array_map('intval', $removedColIds));
                $pdo->exec("UPDATE {$tblC} SET eliminado = 1 WHERE id IN ({$inRemC}) AND tbl_grilla_plantilla_id = {$id}");
            }

            foreach ($pendingConditions as $pc) {
                $resolvedColId = null;
                $resolvedOptId = null;

                if ($pc['cond_col_id'] !== null && in_array((int)$pc['cond_col_id'], $keptColIds, true)) {
                    $resolvedColId = (int)$pc['cond_col_id'];
                } elseif ($pc['cond_col_orden'] !== null && isset($colOrdenToId[(int)$pc['cond_col_orden']])) {
                    $resolvedColId = $colOrdenToId[(int)$pc['cond_col_orden']];
                }

                if ($resolvedColId !== null && $resolvedColId !== $pc['col_id']) {
                    $optsOfTarget = $colOptionsMap[$resolvedColId] ?? [];
                    if ($pc['cond_opt_id'] !== null) {
                        foreach ($optsOfTarget as $ot) {
                            if ((int)$ot['id'] === (int)$pc['cond_opt_id']) {
                                $resolvedOptId = (int)$ot['id'];
                                break;
                            }
                        }
                    }
                    if ($resolvedOptId === null && $pc['cond_opt_orden'] !== null) {
                        foreach ($optsOfTarget as $ot) {
                            if ((int)$ot['orden'] === (int)$pc['cond_opt_orden']) {
                                $resolvedOptId = (int)$ot['id'];
                                break;
                            }
                        }
                    }
                    if ($resolvedOptId === null && $pc['cond_opt_texto'] !== '') {
                        foreach ($optsOfTarget as $ot) {
                            if (mb_strtolower(trim($ot['texto'])) === mb_strtolower(trim($pc['cond_opt_texto']))) {
                                $resolvedOptId = (int)$ot['id'];
                                break;
                            }
                        }
                    }
                } else {
                    $resolvedColId = null;
                }

                if ($resolvedColId === null || $resolvedOptId === null) {
                    $resolvedColId = null;
                    $resolvedOptId = null;
                }

                $stmtCond = $pdo->prepare("UPDATE {$tblC} SET condicion_columna_id = :ccid, condicion_opcion_id = :coid WHERE id = :id");
                $stmtCond->execute([
                    ':ccid' => $resolvedColId,
                    ':coid' => $resolvedOptId,
                    ':id' => $pc['col_id'],
                ]);
            }

            $pdo->commit();

            $saved = self::cargarPlantillaCompleta($pdo, $db, $id);
            return [
                'output' => [
                    'valid' => true,
                    'id' => $id,
                    'response' => $saved,
                ],
            ];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('GrillaPlantilla::save error: ' . $e->getMessage());
            return Util::error_general('No se pudo guardar la tabla de candidatos.');
        } finally {
            $db->closeConect();
        }
    }

    public static function duplicar($rqst = [])
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        $nuevoNombre = isset($rqst['nombre']) ? trim((string)$rqst['nombre']) : '';
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $orig = self::cargarPlantillaCompleta($pdo, $db, $id);
            if (!$orig) {
                return Util::error_no_result();
            }
        } finally {
            $db->closeConect();
        }

        $nombreCopia = $nuevoNombre !== '' ? $nuevoNombre : ($orig['nombre'] . ' (Copia)');

        $colIdToOrden = [];
        $optIdToOrden = [];
        foreach ($orig['columnas'] as $c) {
            $colIdToOrden[(int)$c['id']] = (int)$c['orden'];
            foreach ($c['opciones'] as $op) {
                $optIdToOrden[(int)$op['id']] = (int)$op['orden'];
            }
        }

        $filasPayload = [];
        foreach ($orig['filas'] as $f) {
            $filasPayload[] = [
                'codigo' => $f['codigo'],
                'etiqueta' => $f['etiqueta'],
                'subtitulo' => $f['subtitulo'],
                'tbl_participante_id' => $f['tbl_participante_id'],
                'foto' => $f['foto'],
            ];
        }

        $colsPayload = [];
        foreach ($orig['columnas'] as $c) {
            $optsPayload = [];
            foreach ($c['opciones'] as $op) {
                $optsPayload[] = [
                    'codigo' => $op['codigo'],
                    'texto' => $op['texto'],
                    'valor_numerico' => $op['valor_numerico'],
                    'cuenta_como' => $op['cuenta_como'],
                ];
            }
            $condColId = !empty($c['condicion_columna_id']) ? (int)$c['condicion_columna_id'] : null;
            $condOptId = !empty($c['condicion_opcion_id']) ? (int)$c['condicion_opcion_id'] : null;

            $colsPayload[] = [
                'codigo' => $c['codigo'],
                'titulo' => $c['titulo'],
                'libreto_encuestador' => $c['libreto_encuestador'],
                'nota_tabulacion' => $c['nota_tabulacion'],
                'tipo_respuesta' => $c['tipo_respuesta'],
                'limite_multiple' => $c['limite_multiple'],
                'obligatoria' => $c['obligatoria'],
                'condicion_columna_orden' => $condColId && isset($colIdToOrden[$condColId]) ? $colIdToOrden[$condColId] : null,
                'condicion_opcion_orden' => $condOptId && isset($optIdToOrden[$condOptId]) ? $optIdToOrden[$condOptId] : null,
                'mensaje_bloqueo' => $c['mensaje_bloqueo'],
                'rol_resultado' => $c['rol_resultado'],
                'opciones' => $optsPayload,
            ];
        }

        return self::save([
            'nombre' => $nombreCopia,
            'descripcion' => $orig['descripcion'],
            'libreto_encuestador' => $orig['libreto_encuestador'],
            'nota_tabulacion' => $orig['nota_tabulacion'],
            'tipo' => $orig['tipo'],
            'origen_filas' => $orig['origen_filas'],
            'tbl_cargo_publico_id' => $orig['tbl_cargo_publico_id'],
            'codigo_departamento' => $orig['codigo_departamento'],
            'codigo_municipio' => $orig['codigo_municipio'],
            'presentacion' => $orig['presentacion'],
            'habilitado' => $orig['habilitado'],
            'filas' => $filasPayload,
            'columnas' => $colsPayload,
        ]);
    }

    public static function delete($rqst = [])
    {
        $id = isset($rqst['id']) ? (int)$rqst['id'] : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        try {
            $uso = self::consultarUsoInterno($pdo, $db, $id);
            if ($uso['total_preguntas'] > 0) {
                return Util::error_general('No se puede eliminar esta tabla porque la usan estas preguntas: ' . self::nombresPreguntasEnUso($uso) . '. Quítela de esas preguntas primero.');
            }
            if ($uso['total_respuestas'] > 0) {
                return Util::error_general(
                    'No se puede eliminar esta tabla porque ya tiene ' . $uso['total_respuestas'] . ' respuesta(s) registrada(s) en cuestionarios. Puede deshabilitarla si no desea usarla en nuevas encuestas.'
                );
            }

            $tblP = $db->getTable('tbl_grilla_plantillas');
            $stmt = $pdo->prepare("UPDATE {$tblP} SET eliminado = 1, habilitado = 'no' WHERE id = :id");
            $stmt->execute([':id' => $id]);

            return ['output' => ['valid' => true, 'id' => $id]];
        } catch (PDOException $e) {
            error_log('GrillaPlantilla::delete error: ' . $e->getMessage());
            return Util::error_general('No se pudo eliminar la tabla de candidatos.');
        } finally {
            $db->closeConect();
        }
    }
}
