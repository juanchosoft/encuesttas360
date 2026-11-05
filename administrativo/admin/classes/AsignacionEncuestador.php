<?php

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
            $usuarios = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $u) {
                $usuarios[] = [
                    'id' => (int)$u['id'],
                    'nombre' => trim($u['nombre'] . ' ' . $u['apellido']),
                    'correo' => (string)$u['nickname'],
                    'activo' => strtolower((string)$u['habilitado']) === 'si',
                    'asignado' => (int)$u['asignado'] === 1,
                ];
            }
            return ['output' => ['valid' => true, 'response' => [
                'titulo' => self::etiquetaFicha($ficha),
                'activa' => ($ficha['habilitado'] ?? '') === 'si',
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
            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => ['total' => count($validos)]]];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general('Al guardar los encuestadores de la encuesta.');
        } finally {
            $db->closeConect();
        }
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
            $items = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
                $items[] = [
                    'id' => (int)$f['id'],
                    'nombre' => self::etiquetaFicha($f),
                    'activa' => ($f['habilitado'] ?? '') === 'si',
                    'asignado' => (int)$f['asignado'] === 1,
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
            $pdo->commit();
            return ['output' => ['valid' => true, 'response' => ['total' => count($validas)]]];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Util::error_general('Al guardar las encuestas del usuario.');
        } finally {
            $db->closeConect();
        }
    }
}
