<?php

require_once __DIR__ . '/EspacioGeografico.php';

class AsignacionEncuestador
{
    private const ROLE_KEY = 'encuestador';

    private static function ids($valor): array
    {
        if (is_string($valor)) {
            $decodificado = json_decode($valor, true);
            $valor = is_array($decodificado) ? $decodificado : ($valor === '' ? [] : explode(',', $valor));
        }
        if (!is_array($valor)) {
            return [];
        }
        $ids = [];
        foreach ($valor as $v) {
            $n = (int)$v;
            if ($n > 0) {
                $ids[$n] = $n;
            }
        }
        return array_values($ids);
    }

    private static function etiquetaFicha(array $f): string
    {
        $t = trim((string)($f['temas_concretos'] ?? ''));
        if ($t === '') {
            $t = trim((string)($f['realizada_por_o_encomendada_por'] ?? ''));
        }
        return $t !== '' ? $t : ('Encuesta #' . (int)$f['id']);
    }

    public static function usuarioEsEncuestador(int $usuarioId): bool
    {
        if ($usuarioId <= 0) {
            return false;
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM " . $db->getTable('tbl_usuarios') . " u
                 INNER JOIN " . $db->getTable('tbl_roles') . " r ON r.id = u.role_id
                 WHERE u.id = :id AND r.role_key = :rk"
            );
            $stmt->execute([':id' => $usuarioId, ':rk' => self::ROLE_KEY]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        } finally {
            $db->closeConect();
        }
    }

    public static function fichasActivasDeUsuario(int $usuarioId): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare(
                "SELECT f.* FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f
                 INNER JOIN " . $db->getTable('tbl_ficha_encuestadores') . " a
                    ON a.tbl_ficha_tecnica_encuesta_id = f.id
                 WHERE a.tbl_usuario_id = :u
                   AND (f.eliminado = 'no' OR f.eliminado IS NULL)
                   AND f.habilitado = 'si'
                 ORDER BY f.id DESC"
            );
            $stmt->execute([':u' => $usuarioId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        } finally {
            $db->closeConect();
        }
    }

    public static function municipiosDeUsuario(int $usuarioId, int $fichaId): array
    {
        if ($usuarioId <= 0 || $fichaId <= 0) {
            return [];
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare(
                "SELECT codigo_municipio FROM " . $db->getTable('tbl_ficha_encuestador_municipios') . "
                 WHERE tbl_usuario_id = :u AND tbl_ficha_tecnica_encuesta_id = :f"
            );
            $stmt->execute([':u' => $usuarioId, ':f' => $fichaId]);
            $codigos = [];
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $c) {
                $n = Util::normalizeCodigoMunicipio($c);
                if ($n !== '' && $n !== '00000') {
                    $codigos[$n] = $n;
                }
            }
            return array_values($codigos);
        } catch (PDOException $e) {
            return [];
        } finally {
            $db->closeConect();
        }
    }

    public static function ambitoParaUsuario(int $usuarioId, int $fichaId, array $ambitoFicha): array
    {
        if (!self::usuarioEsEncuestador($usuarioId)) {
            return $ambitoFicha;
        }
        $permitidos = [];
        foreach (self::municipiosDeUsuario($usuarioId, $fichaId) as $m) {
            if (EspacioGeografico::ambitoPermite($ambitoFicha, substr($m, 0, 2), $m)) {
                $permitidos[] = $m;
            }
        }
        if (!$permitidos) {
            return $ambitoFicha;
        }
        $deps = [];
        foreach ($permitidos as $m) {
            $deps[substr($m, 0, 2)] = substr($m, 0, 2);
        }
        return array_merge($ambitoFicha, [
            'restringido' => true,
            'departamentos' => array_values($deps),
            'municipios' => $permitidos,
            'asignado' => true,
        ]);
    }

    public static function usuarioPuedeEnMunicipio(int $usuarioId, int $fichaId, $codigoDepartamento, $codigoMunicipio): bool
    {
        $ambitoFicha = EspacioGeografico::ambitoDeFicha($fichaId);
        $ambito = self::ambitoParaUsuario($usuarioId, $fichaId, $ambitoFicha);
        return EspacioGeografico::ambitoPermite($ambito, $codigoDepartamento, $codigoMunicipio);
    }

    private static function catalogoMunicipios(int $fichaId): array
    {
        require_once __DIR__ . '/Pregunta.php';
        $res = Pregunta::getMunicipiosCatalogo(['ficha_id' => $fichaId]);
        $filas = $res['output']['response'] ?? [];
        $db = new DbConection();
        $pdo = $db->openConect();
        $nombres = [];
        try {
            foreach ($pdo->query("SELECT codigo_departamento, departamento FROM " . $db->getTable('tbl_departamentos'))->fetchAll(PDO::FETCH_ASSOC) as $d) {
                $nombres[Util::normalizeCodigoDepartamento($d['codigo_departamento'])] = (string)$d['departamento'];
            }
        } catch (PDOException $e) {
        } finally {
            $db->closeConect();
        }
        $items = [];
        foreach ($filas as $m) {
            $cod = Util::normalizeCodigoMunicipio($m['codigo_municipio']);
            $dep = Util::normalizeCodigoDepartamento($m['codigo_departamento']);
            $items[$cod] = [
                'codigo' => $cod,
                'municipio' => (string)$m['municipio'],
                'codigo_departamento' => $dep,
                'departamento' => $nombres[$dep] ?? $dep,
            ];
        }
        return array_values($items);
    }

    private static function municipiosSeleccionados($valor): array
    {
        if (is_string($valor)) {
            $valor = json_decode($valor, true);
        }
        $res = [];
        if (!is_array($valor)) {
            return $res;
        }
        foreach ($valor as $uid => $codigos) {
            $uid = (int)$uid;
            if ($uid <= 0 || !is_array($codigos)) {
                continue;
            }
            foreach ($codigos as $c) {
                $n = Util::normalizeCodigoMunicipio($c);
                if ($n !== '' && $n !== '00000') {
                    $res[$uid][$n] = $n;
                }
            }
        }
        return $res;
    }
    public static function usuarioPuedeEncuestar(int $usuarioId, int $fichaId): bool
    {
        if (!self::usuarioEsEncuestador($usuarioId)) {
            return true;
        }
        foreach (self::fichasActivasDeUsuario($usuarioId) as $f) {
            if ((int)$f['id'] === $fichaId) {
                return true;
            }
        }
        return false;
    }

    public static function getPorFicha($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : 0;
        if ($fichaId <= 0) {
            return Util::error_missing_data();
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmtF = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id = :id AND (eliminado = 'no' OR eliminado IS NULL)");
            $stmtF->execute([':id' => $fichaId]);
            $ficha = $stmtF->fetch(PDO::FETCH_ASSOC);
            if (!$ficha) {
                return Util::error_general('La encuesta no existe.');
            }
            $stmt = $pdo->prepare(
                "SELECT u.id, u.nombre, u.apellido, u.nickname, u.habilitado,
                        (a.id IS NOT NULL) AS asignado
                 FROM " . $db->getTable('tbl_usuarios') . " u
                 INNER JOIN " . $db->getTable('tbl_roles') . " r ON r.id = u.role_id AND r.role_key = :rk
                 LEFT JOIN " . $db->getTable('tbl_ficha_encuestadores') . " a
                    ON a.tbl_usuario_id = u.id AND a.tbl_ficha_tecnica_encuesta_id = :f
                 ORDER BY u.nombre, u.apellido"
            );
            $stmt->execute([':rk' => self::ROLE_KEY, ':f' => $fichaId]);
            $stmtM = $pdo->prepare("SELECT tbl_usuario_id, codigo_municipio FROM " . $db->getTable('tbl_ficha_encuestador_municipios') . " WHERE tbl_ficha_tecnica_encuesta_id = :f");
            $stmtM->execute([':f' => $fichaId]);
            $munPorUsuario = [];
            foreach ($stmtM->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $munPorUsuario[(int)$r['tbl_usuario_id']][] = Util::normalizeCodigoMunicipio($r['codigo_municipio']);
            }
            $usuarios = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
                $usuarios[] = [
                    'id' => (int)$u['id'],
                    'nombre' => trim($u['nombre'] . ' ' . $u['apellido']),
                    'correo' => (string)$u['nickname'],
                    'activo' => strtolower((string)$u['habilitado']) === 'si',
                    'asignado' => (int)$u['asignado'] === 1,
                    'municipios' => $munPorUsuario[(int)$u['id']] ?? [],
                ];
            }
            $ambitoFicha = EspacioGeografico::ambitoDeFicha($fichaId);
            return ['output' => ['valid' => true, 'response' => [
                'titulo' => self::etiquetaFicha($ficha),
                'activa' => ($ficha['habilitado'] ?? '') === 'si',
                'tipo_estudio' => $ambitoFicha['tipo'] !== '' ? $ambitoFicha['tipo'] : 'nacional',
                'catalogo' => self::catalogoMunicipios($fichaId),
                'items' => $usuarios,
            ]]];
        } catch (PDOException $e) {
            return Util::error_general('Al obtener los encuestadores de la encuesta.');
        } finally {
            $db->closeConect();
        }
    }

    public static function savePorFicha($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : 0;
        if ($fichaId <= 0) {
            return Util::error_missing_data();
        }
        $seleccion = self::ids($rqst['usuarios_ids'] ?? []);
        $municipios = self::municipiosSeleccionados($rqst['municipios'] ?? []);
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmtF = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id = :id AND (eliminado = 'no' OR eliminado IS NULL)");
            $stmtF->execute([':id' => $fichaId]);
            if (!$stmtF->fetch()) {
                return Util::error_general('La encuesta no existe.');
            }
            $validos = [];
            if ($seleccion) {
                $in = implode(',', array_fill(0, count($seleccion), '?'));
                $stmtU = $pdo->prepare(
                    "SELECT u.id FROM " . $db->getTable('tbl_usuarios') . " u
                     INNER JOIN " . $db->getTable('tbl_roles') . " r ON r.id = u.role_id AND r.role_key = ?
                     WHERE u.id IN ($in)"
                );
                $stmtU->execute(array_merge([self::ROLE_KEY], $seleccion));
                $validos = array_map('intval', $stmtU->fetchAll(PDO::FETCH_COLUMN));
            }
            $pdo->beginTransaction();
            $tabla = $db->getTable('tbl_ficha_encuestadores');
            $pdo->prepare("DELETE FROM $tabla WHERE tbl_ficha_tecnica_encuesta_id = :f")->execute([':f' => $fichaId]);
            $ins = $pdo->prepare("INSERT INTO $tabla (tbl_ficha_tecnica_encuesta_id, tbl_usuario_id, tbl_usuario_asigno_id) VALUES (:f, :u, :a)");
            $asigno = (int)SessionData::getUserId();
            foreach ($validos as $uid) {
                $ins->execute([':f' => $fichaId, ':u' => $uid, ':a' => $asigno > 0 ? $asigno : null]);
            }
            $tMun = $db->getTable('tbl_ficha_encuestador_municipios');
            $pdo->prepare("DELETE FROM $tMun WHERE tbl_ficha_tecnica_encuesta_id = :f")->execute([':f' => $fichaId]);
            $permitidos = [];
            foreach (self::catalogoMunicipios($fichaId) as $m) {
                $permitidos[$m['codigo']] = $m['codigo_departamento'];
            }
            $insMun = $pdo->prepare("INSERT INTO $tMun (tbl_ficha_tecnica_encuesta_id, tbl_usuario_id, codigo_departamento, codigo_municipio) VALUES (:f, :u, :d, :m)");
            foreach ($validos as $uid) {
                foreach ($municipios[$uid] ?? [] as $cod) {
                    if (!isset($permitidos[$cod])) {
                        throw new RuntimeException('El municipio ' . $cod . ' no pertenece al espacio geográfico de la encuesta.');
                    }
                    $insMun->execute([':f' => $fichaId, ':u' => $uid, ':d' => $permitidos[$cod], ':m' => $cod]);
                }
            }
            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => ['total' => count($validos)]]];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general('Al guardar los encuestadores de la encuesta.');
        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general($e->getMessage());
        } finally {
            $db->closeConect();
        }
    }

    public static function getCatalogoMunicipios($rqst)
    {
        $fichaId = isset($rqst['ficha_id']) ? (int)$rqst['ficha_id'] : 0;
        if ($fichaId <= 0) {
            return Util::error_missing_data();
        }
        return ['output' => ['valid' => true, 'response' => ['catalogo' => self::catalogoMunicipios($fichaId)]]];
    }

    public static function getPorUsuario($rqst)
    {
        $usuarioId = isset($rqst['usuario_id']) ? (int)$rqst['usuario_id'] : 0;
        if ($usuarioId <= 0) {
            return Util::error_missing_data();
        }
        if (!self::usuarioEsEncuestador($usuarioId)) {
            return Util::error_general('Solo se asignan encuestas a usuarios con rol Encuestador.');
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->prepare(
                "SELECT f.*, (a.id IS NOT NULL) AS asignado
                 FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f
                 LEFT JOIN " . $db->getTable('tbl_ficha_encuestadores') . " a
                    ON a.tbl_ficha_tecnica_encuesta_id = f.id AND a.tbl_usuario_id = :u
                 WHERE (f.eliminado = 'no' OR f.eliminado IS NULL)
                 ORDER BY f.id DESC"
            );
            $stmt->execute([':u' => $usuarioId]);
            $stmtM = $pdo->prepare("SELECT tbl_ficha_tecnica_encuesta_id, codigo_municipio FROM " . $db->getTable('tbl_ficha_encuestador_municipios') . " WHERE tbl_usuario_id = :u");
            $stmtM->execute([':u' => $usuarioId]);
            $munPorFicha = [];
            foreach ($stmtM->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $munPorFicha[(int)$r['tbl_ficha_tecnica_encuesta_id']][] = Util::normalizeCodigoMunicipio($r['codigo_municipio']);
            }
            $items = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
                $ambitoFicha = EspacioGeografico::ambitoDeFicha((int)$f['id']);
                $items[] = [
                    'id' => (int)$f['id'],
                    'nombre' => self::etiquetaFicha($f),
                    'activa' => ($f['habilitado'] ?? '') === 'si',
                    'asignado' => (int)$f['asignado'] === 1,
                    'tipo_estudio' => $ambitoFicha['tipo'] !== '' ? $ambitoFicha['tipo'] : 'nacional',
                    'municipios' => $munPorFicha[(int)$f['id']] ?? [],
                ];
            }
            return ['output' => ['valid' => true, 'response' => ['items' => $items]]];
        } catch (PDOException $e) {
            return Util::error_general('Al obtener las encuestas del usuario.');
        } finally {
            $db->closeConect();
        }
    }

    public static function savePorUsuario($rqst)
    {
        $usuarioId = isset($rqst['usuario_id']) ? (int)$rqst['usuario_id'] : 0;
        if ($usuarioId <= 0) {
            return Util::error_missing_data();
        }
        if (!self::usuarioEsEncuestador($usuarioId)) {
            return Util::error_general('Solo se asignan encuestas a usuarios con rol Encuestador.');
        }
        $seleccion = self::ids($rqst['fichas_ids'] ?? []);
        $municipiosPorFicha = array_key_exists('municipios', $rqst) ? self::municipiosSeleccionados($rqst['municipios']) : null;
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $validas = [];
            if ($seleccion) {
                $in = implode(',', array_fill(0, count($seleccion), '?'));
                $stmtF = $pdo->prepare("SELECT id FROM " . $db->getTable('tbl_ficha_tecnica_encuestas') . " WHERE id IN ($in) AND (eliminado = 'no' OR eliminado IS NULL)");
                $stmtF->execute($seleccion);
                $validas = array_map('intval', $stmtF->fetchAll(PDO::FETCH_COLUMN));
            }
            $pdo->beginTransaction();
            $tabla = $db->getTable('tbl_ficha_encuestadores');
            $pdo->prepare("DELETE FROM $tabla WHERE tbl_usuario_id = :u")->execute([':u' => $usuarioId]);
            $ins = $pdo->prepare("INSERT INTO $tabla (tbl_ficha_tecnica_encuesta_id, tbl_usuario_id, tbl_usuario_asigno_id) VALUES (:f, :u, :a)");
            $asigno = (int)SessionData::getUserId();
            foreach ($validas as $fid) {
                $ins->execute([':f' => $fid, ':u' => $usuarioId, ':a' => $asigno > 0 ? $asigno : null]);
            }
            $tMun = $db->getTable('tbl_ficha_encuestador_municipios');
            if ($validas) {
                $inV = implode(',', array_fill(0, count($validas), '?'));
                $pdo->prepare("DELETE FROM $tMun WHERE tbl_usuario_id = ? AND tbl_ficha_tecnica_encuesta_id NOT IN ($inV)")->execute(array_merge([$usuarioId], $validas));
            } else {
                $pdo->prepare("DELETE FROM $tMun WHERE tbl_usuario_id = ?")->execute([$usuarioId]);
            }
            if ($municipiosPorFicha !== null) {
                $delMun = $pdo->prepare("DELETE FROM $tMun WHERE tbl_usuario_id = :u AND tbl_ficha_tecnica_encuesta_id = :f");
                $insMun = $pdo->prepare("INSERT INTO $tMun (tbl_ficha_tecnica_encuesta_id, tbl_usuario_id, codigo_departamento, codigo_municipio) VALUES (:f, :u, :d, :m)");
                foreach ($validas as $fid) {
                    $delMun->execute([':u' => $usuarioId, ':f' => $fid]);
                    $codigos = $municipiosPorFicha[$fid] ?? [];
                    if (!$codigos) {
                        continue;
                    }
                    $permitidos = [];
                    foreach (self::catalogoMunicipios($fid) as $m) {
                        $permitidos[$m['codigo']] = $m['codigo_departamento'];
                    }
                    foreach ($codigos as $cod) {
                        if (!isset($permitidos[$cod])) {
                            throw new RuntimeException('El municipio ' . $cod . ' no pertenece al espacio geográfico de la encuesta #' . $fid . '.');
                        }
                        $insMun->execute([':f' => $fid, ':u' => $usuarioId, ':d' => $permitidos[$cod], ':m' => $cod]);
                    }
                }
            }
            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => ['total' => count($validas)]]];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general('Al guardar las encuestas del usuario.');
        } catch (RuntimeException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general($e->getMessage());
        } finally {
            $db->closeConect();
        }
    }
}
