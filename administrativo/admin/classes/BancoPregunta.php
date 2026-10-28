<?php
class BancoPregunta
{
    const TIPOS_SONDEO = ['No Aplica', 'Dicotomica', 'Grilla', 'Preguntas_Ordinales', 'Preguntas_Cardinales', 'Seleccion_Multiple_unica_respuesta'];

    private static function texto($v): ?string
    {
        $v = trim((string)$v);
        return $v === '' ? null : $v;
    }

    private static function normalizar(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        return preg_replace('/\s+/u', ' ', $s);
    }

    private static function hashContenido(string $uso, string $texto, ?string $tipo, array $opciones): string
    {
        $textos = array_map(function ($o) {
            return self::normalizar((string)($o['texto_opcion'] ?? ''));
        }, $opciones);
        return sha1($uso . '|' . self::normalizar($texto) . '|' . self::normalizar((string)$tipo) . '|' . implode('~', $textos));
    }

    private static function opcionesDeRqst($rqst): array
    {
        $raw = $rqst['opciones'] ?? [];
        if (is_string($raw)) {
            $dec = json_decode($raw, true);
            $raw = is_array($dec) ? $dec : [];
        }
        $out = [];
        foreach ((array)$raw as $o) {
            if (is_string($o)) {
                $o = ['texto_opcion' => $o];
            }
            $texto = trim((string)($o['texto_opcion'] ?? $o['texto'] ?? $o['opcion'] ?? ''));
            if ($texto === '') {
                continue;
            }
            $cuenta = (string)($o['cuenta_como'] ?? 'ninguno');
            $accion = (string)($o['accion'] ?? 'continuar');
            $out[] = [
                'texto_opcion' => $texto,
                'valor_numerico' => (isset($o['valor_numerico']) && $o['valor_numerico'] !== '' && $o['valor_numerico'] !== null) ? (float)$o['valor_numerico'] : null,
                'cuenta_como' => in_array($cuenta, ['ninguno', 'si', 'no', 'favorable', 'desfavorable'], true) ? $cuenta : 'ninguno',
                'accion' => $accion === 'terminar' ? 'terminar' : 'continuar',
                'mensaje_terminar' => $accion === 'terminar' ? self::texto($o['mensaje_terminar'] ?? '') : null,
                'es_opcion_fija' => !empty($o['es_opcion_fija']) ? 1 : 0,
            ];
        }
        return $out;
    }

    public static function getAll($rqst)
    {
        $rqst = is_array($rqst) ? $rqst : [];
        $id = (int)($rqst['id'] ?? 0);
        $uso = in_array($rqst['uso'] ?? '', ['encuesta', 'sondeo'], true) ? $rqst['uso'] : '';
        $categoria = trim((string)($rqst['categoria'] ?? ''));
        $tipo = trim((string)($rqst['tipo_pregunta'] ?? ''));
        $buscar = trim((string)($rqst['buscar'] ?? ''));

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $where = ['b.eliminado = 0'];
            $params = [];
            if ($id > 0) {
                $where[] = 'b.id = :id';
                $params[':id'] = $id;
            }
            if ($uso !== '') {
                $where[] = 'b.uso = :uso';
                $params[':uso'] = $uso;
            }
            if ($categoria !== '') {
                $where[] = 'b.categoria = :categoria';
                $params[':categoria'] = $categoria;
            }
            if ($tipo !== '') {
                $where[] = '(b.tipo_pregunta = :tipo OR b.tipo_sondeo = :tipo)';
                $params[':tipo'] = $tipo;
            }
            if ($buscar !== '') {
                $where[] = '(b.texto_pregunta LIKE :q OR b.enunciado_pregunta LIKE :q OR b.categoria LIKE :q OR b.numeral LIKE :q OR b.capitulo LIKE :q)';
                $params[':q'] = '%' . $buscar . '%';
            }
            $sql = "SELECT b.*, (SELECT COUNT(*) FROM " . $db->getTable('tbl_banco_pregunta_opciones') . " o WHERE o.tbl_banco_pregunta_id = b.id) AS total_opciones
                    FROM " . $db->getTable('tbl_banco_preguntas') . " b WHERE " . implode(' AND ', $where) . " ORDER BY b.id DESC";
            $st = $pdo->prepare($sql);
            $st->execute($params);
            $filas = $st->fetchAll(PDO::FETCH_ASSOC);

            if ($id > 0 || !empty($rqst['con_opciones'])) {
                $stO = $pdo->prepare("SELECT id, texto_opcion, orden, valor_numerico, cuenta_como, accion, mensaje_terminar, es_opcion_fija
                    FROM " . $db->getTable('tbl_banco_pregunta_opciones') . " WHERE tbl_banco_pregunta_id = :id ORDER BY orden, id");
                foreach ($filas as $k => $f) {
                    $stO->execute([':id' => $f['id']]);
                    $filas[$k]['opciones'] = $stO->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            $arrjson = ['output' => ['valid' => true, 'response' => $filas]];
        } catch (PDOException $e) {
            error_log('BancoPregunta::getAll ' . $e->getMessage());
            $arrjson = Util::error_general('Al obtener las preguntas del banco.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function categorias($rqst)
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $st = $pdo->query("SELECT DISTINCT categoria FROM " . $db->getTable('tbl_banco_preguntas') . " WHERE eliminado = 0 AND categoria IS NOT NULL AND categoria <> '' ORDER BY categoria");
            $arrjson = ['output' => ['valid' => true, 'response' => $st->fetchAll(PDO::FETCH_COLUMN)]];
        } catch (PDOException $e) {
            error_log('BancoPregunta::categorias ' . $e->getMessage());
            $arrjson = Util::error_general('Al obtener las categorías.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function save($rqst)
    {
        $id = (int)($rqst['id'] ?? 0);
        $uso = in_array($rqst['uso'] ?? '', ['encuesta', 'sondeo'], true) ? $rqst['uso'] : '';
        $texto = trim((string)($rqst['texto_pregunta'] ?? ''));
        if ($uso === '') {
            return Util::error_missing_data_description('Indique el uso de la pregunta: encuesta o sondeo.');
        }
        if ($texto === '') {
            return Util::error_missing_data_description('El texto de la pregunta es requerido.');
        }
        $opciones = self::opcionesDeRqst($rqst);
        $tipo = self::texto($rqst['tipo_pregunta'] ?? '');
        $tipoSondeo = self::texto($rqst['tipo_sondeo'] ?? '');

        if ($uso === 'sondeo') {
            if (!$tipoSondeo || !in_array($tipoSondeo, self::TIPOS_SONDEO, true)) {
                return Util::error_missing_data_description('Seleccione el tipo de sondeo.');
            }
            if ($tipoSondeo === 'Dicotomica' && count($opciones) !== 2) {
                return Util::error_missing_data_description('Un sondeo dicotómico debe tener exactamente dos opciones.');
            }
            if (!in_array($tipoSondeo, ['No Aplica', 'Grilla', 'Dicotomica'], true) && count($opciones) < 2) {
                return Util::error_missing_data_description('Agregue al menos dos opciones.');
            }
            $tipo = null;
        } else {
            if (!$tipo) {
                return Util::error_missing_data_description('Seleccione el tipo de pregunta.');
            }
            $sinOpciones = ['Texto Libre', 'Grilla', 'Lista_desde_grilla'];
            if (!in_array($tipo, $sinOpciones, true) && count($opciones) < 1) {
                return Util::error_missing_data_description('Agregue al menos una opción de respuesta.');
            }
            $tipoSondeo = null;
        }
        $hash = self::hashContenido($uso, $texto, $uso === 'sondeo' ? $tipoSondeo : $tipo, $opciones);
        $usuario = isset($_SESSION['session_user']['id']) ? (int)$_SESSION['session_user']['id'] : null;

        $db = new DbConection();
        $pdo = $db->openConect();
        $tb = $db->getTable('tbl_banco_preguntas');
        $to = $db->getTable('tbl_banco_pregunta_opciones');
        try {
            if (empty($rqst['permitir_duplicado'])) {
                $stD = $pdo->prepare("SELECT id FROM $tb WHERE hash_contenido = :h AND eliminado = 0 AND id <> :id LIMIT 1");
                $stD->execute([':h' => $hash, ':id' => $id]);
                $dup = $stD->fetchColumn();
                if ($dup) {
                    return ['output' => ['valid' => false, 'duplicado' => true, 'id_existente' => (int)$dup,
                        'response' => ['content' => 'Esa pregunta ya existe en el banco (mismo texto, tipo y opciones).']]];
                }
            }
            $campos = [
                ':uso' => $uso,
                ':categoria' => self::texto($rqst['categoria'] ?? ''),
                ':texto' => $texto,
                ':enunciado' => self::texto($rqst['enunciado_pregunta'] ?? ''),
                ':capitulo' => self::texto($rqst['capitulo'] ?? ''),
                ':numeral' => self::texto($rqst['numeral'] ?? ''),
                ':tipo' => $tipo,
                ':limite' => (string)max(1, (int)($rqst['limite_respuesta_multiple'] ?? 1)),
                ':obligatoria' => isset($rqst['obligatoria']) ? ((int)$rqst['obligatoria'] ? 1 : 0) : 1,
                ':es_filtro' => !empty($rqst['es_filtro']) ? 1 : 0,
                ':libreto' => self::texto($rqst['libreto_encuestador'] ?? ''),
                ':nota' => self::texto($rqst['nota_tabulacion'] ?? ''),
                ':cruce' => !empty($rqst['es_variable_cruce']) ? 1 : 0,
                ':etiqueta' => self::texto($rqst['etiqueta_cruce'] ?? ''),
                ':copiar' => self::texto($rqst['copiar_a_votante_campo'] ?? ''),
                ':rol' => self::texto($rqst['rol_resultado'] ?? '') ?: 'ninguno',
                ':plantilla' => !empty($rqst['tbl_grilla_plantilla_id']) ? (int)$rqst['tbl_grilla_plantilla_id'] : null,
                ':desc' => self::texto($rqst['descripcion_sondeo'] ?? ''),
                ':tsondeo' => $tipoSondeo,
                ':tinf' => self::texto($rqst['tipo_inferenciales'] ?? ''),
                ':trivia' => $uso === 'sondeo' ? (($rqst['es_trivia'] ?? 'no') === 'si' ? 'si' : 'no') : null,
                ':hash' => $hash,
            ];
            $pdo->beginTransaction();
            if ($id > 0) {
                $stE = $pdo->prepare("SELECT COUNT(*) FROM $tb WHERE id = :id AND eliminado = 0");
                $stE->execute([':id' => $id]);
                if (!(int)$stE->fetchColumn()) {
                    throw new RuntimeException('La pregunta del banco no existe o fue eliminada.');
                }
                $st = $pdo->prepare("UPDATE $tb SET uso = :uso, categoria = :categoria, texto_pregunta = :texto, enunciado_pregunta = :enunciado, capitulo = :capitulo,
                    numeral = :numeral, tipo_pregunta = :tipo, limite_respuesta_multiple = :limite, obligatoria = :obligatoria, es_filtro = :es_filtro,
                    libreto_encuestador = :libreto, nota_tabulacion = :nota, es_variable_cruce = :cruce, etiqueta_cruce = :etiqueta,
                    copiar_a_votante_campo = :copiar, rol_resultado = :rol, tbl_grilla_plantilla_id = :plantilla, descripcion_sondeo = :desc,
                    tipo_sondeo = :tsondeo, tipo_inferenciales = :tinf, es_trivia = :trivia, hash_contenido = :hash, dtupdate = NOW() WHERE id = :id");
                $st->execute($campos + [':id' => $id]);
                $pdo->prepare("DELETE FROM $to WHERE tbl_banco_pregunta_id = :id")->execute([':id' => $id]);
            } else {
                $st = $pdo->prepare("INSERT INTO $tb (uso, categoria, texto_pregunta, enunciado_pregunta, capitulo, numeral, tipo_pregunta, limite_respuesta_multiple,
                    obligatoria, es_filtro, libreto_encuestador, nota_tabulacion, es_variable_cruce, etiqueta_cruce, copiar_a_votante_campo, rol_resultado,
                    tbl_grilla_plantilla_id, descripcion_sondeo, tipo_sondeo, tipo_inferenciales, es_trivia, hash_contenido, tbl_usuario_id, dtcreate)
                    VALUES (:uso, :categoria, :texto, :enunciado, :capitulo, :numeral, :tipo, :limite, :obligatoria, :es_filtro, :libreto, :nota, :cruce,
                    :etiqueta, :copiar, :rol, :plantilla, :desc, :tsondeo, :tinf, :trivia, :hash, :usuario, NOW())");
                $st->execute($campos + [':usuario' => $usuario]);
                $id = (int)$pdo->lastInsertId();
            }
            $stO = $pdo->prepare("INSERT INTO $to (tbl_banco_pregunta_id, texto_opcion, orden, valor_numerico, cuenta_como, accion, mensaje_terminar, es_opcion_fija)
                VALUES (:b, :t, :o, :v, :c, :a, :m, :f)");
            foreach ($opciones as $i => $o) {
                $stO->execute([':b' => $id, ':t' => $o['texto_opcion'], ':o' => $i + 1, ':v' => $o['valor_numerico'], ':c' => $o['cuenta_como'],
                    ':a' => $o['accion'], ':m' => $o['mensaje_terminar'], ':f' => $o['es_opcion_fija']]);
            }
            $pdo->commit();
            $arrjson = ['output' => ['valid' => true, 'id' => $id]];
        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $arrjson = Util::error_general($e->getMessage());
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('BancoPregunta::save ' . $e->getMessage());
            $arrjson = Util::error_general('No se pudo guardar la pregunta en el banco.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function delete($rqst)
    {
        $id = (int)($rqst['id'] ?? 0);
        if ($id <= 0) {
            return Util::error_missing_data();
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $st = $pdo->prepare("UPDATE " . $db->getTable('tbl_banco_preguntas') . " SET eliminado = 1, dtupdate = NOW() WHERE id = :id AND eliminado = 0");
            $st->execute([':id' => $id]);
            $arrjson = ['output' => ['valid' => true, 'response' => $st->rowCount()]];
        } catch (PDOException $e) {
            error_log('BancoPregunta::delete ' . $e->getMessage());
            $arrjson = Util::error_general('No se pudo eliminar la pregunta del banco.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function desdePregunta($rqst)
    {
        $preguntaId = (int)($rqst['pregunta_id'] ?? 0);
        if ($preguntaId <= 0) {
            return Util::error_missing_data();
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $st = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_preguntas') . " WHERE id = :id AND eliminado = 0");
            $st->execute([':id' => $preguntaId]);
            $p = $st->fetch(PDO::FETCH_ASSOC);
            if (!$p) {
                return Util::error_general('La pregunta no existe.');
            }
            $stO = $pdo->prepare("SELECT texto_opcion, valor_numerico, cuenta_como, accion, mensaje_terminar, es_opcion_fija
                FROM " . $db->getTable('tbl_opciones_respuesta') . " WHERE tbl_pregunta_id = :id AND eliminado = 0 ORDER BY orden, id");
            $stO->execute([':id' => $preguntaId]);
            $opciones = $stO->fetchAll(PDO::FETCH_ASSOC);

            $omitido = [];
            if (!empty($p['visible_si_pregunta_id'])) {
                $omitido[] = 'La condición de visibilidad (se muestra solo si otra pregunta tiene cierta respuesta)';
            }
            if (!empty($p['lista_origen_pregunta_id'])) {
                $omitido[] = 'El origen de la lista (depende de otra pregunta de la encuesta)';
            }
            $saltos = 0;
            foreach ($opciones as $o) {
                if ($o['accion'] === 'saltar') {
                    $saltos++;
                }
            }
            if ($saltos) {
                $omitido[] = $saltos . ' salto(s) de opción a otra pregunta (quedan como «continuar»)';
            }

            $datos = [
                'uso' => 'encuesta',
                'categoria' => $rqst['categoria'] ?? '',
                'texto_pregunta' => $p['texto_pregunta'],
                'enunciado_pregunta' => $p['enunciado_pregunta'],
                'capitulo' => $p['capitulo'],
                'numeral' => $p['numeral'],
                'tipo_pregunta' => $p['tipo_pregunta'],
                'limite_respuesta_multiple' => $p['limite_respuesta_multiple'],
                'obligatoria' => $p['obligatoria'],
                'es_filtro' => $p['es_filtro'],
                'libreto_encuestador' => $p['libreto_encuestador'],
                'nota_tabulacion' => $p['nota_tabulacion'],
                'es_variable_cruce' => $p['es_variable_cruce'],
                'etiqueta_cruce' => $p['etiqueta_cruce'],
                'copiar_a_votante_campo' => $p['copiar_a_votante_campo'],
                'rol_resultado' => $p['rol_resultado'],
                'tbl_grilla_plantilla_id' => $p['tbl_grilla_plantilla_id'],
                'opciones' => $opciones,
                'permitir_duplicado' => !empty($rqst['permitir_duplicado']) ? 1 : 0,
            ];
        } catch (PDOException $e) {
            error_log('BancoPregunta::desdePregunta ' . $e->getMessage());
            return Util::error_general('No se pudo leer la pregunta.');
        } finally {
            $db->closeConect();
        }
        $res = self::save($datos);
        if (!empty($res['output']['valid'])) {
            $res['output']['omitido'] = $omitido;
        }
        return $res;
    }

    public static function desdeSondeo($rqst)
    {
        $sondeoId = (int)($rqst['sondeo_id'] ?? 0);
        if ($sondeoId <= 0) {
            return Util::error_missing_data();
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $st = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_sondeo') . " WHERE id = :id");
            $st->execute([':id' => $sondeoId]);
            $s = $st->fetch(PDO::FETCH_ASSOC);
            if (!$s) {
                return Util::error_general('El sondeo no existe.');
            }
            $stO = $pdo->prepare("SELECT opcion AS texto_opcion FROM " . $db->getTable('tbl_sondeo_x_opciones') . " WHERE tbl_sondeo_id = :id ORDER BY id");
            $stO->execute([':id' => $sondeoId]);
            $opciones = $stO->fetchAll(PDO::FETCH_ASSOC);
            $datos = [
                'uso' => 'sondeo',
                'categoria' => $rqst['categoria'] ?? '',
                'texto_pregunta' => $s['sondeo'],
                'descripcion_sondeo' => $s['descripcion_sondeo'],
                'tipo_sondeo' => $s['tipo_sondeo'],
                'tipo_inferenciales' => $s['tipo_inferenciales'],
                'es_trivia' => $s['es_trivia'],
                'opciones' => $opciones,
                'permitir_duplicado' => !empty($rqst['permitir_duplicado']) ? 1 : 0,
            ];
            $omitido = [];
            if ($s['aplica_cargos_publicos'] === 'si') {
                $omitido[] = 'El cargo público, el departamento y los candidatos del sondeo';
            }
            $omitido[] = 'Las fechas de inicio y fin y el estado habilitado';
        } catch (PDOException $e) {
            error_log('BancoPregunta::desdeSondeo ' . $e->getMessage());
            return Util::error_general('No se pudo leer el sondeo.');
        } finally {
            $db->closeConect();
        }
        $res = self::save($datos);
        if (!empty($res['output']['valid'])) {
            $res['output']['omitido'] = $omitido;
        }
        return $res;
    }
}
