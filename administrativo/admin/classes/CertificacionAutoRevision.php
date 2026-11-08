<?php

require_once __DIR__ . '/DbConection.php';
require_once __DIR__ . '/Util.php';
require_once __DIR__ . '/CertificacionEncuestador.php';

class CertificacionAutoRevision
{
    private const MAX_FALLOS = 3;
    private const CONFIANZA_MINIMA = 0.7;
    private const MINUTOS_BLOQUEO = 20;
    private const PREFIJO_FALLO = 'Fallo IA automática';

    public static function procesar(int $max = 1): array
    {
        $max = max(1, min(5, $max));
        $resultado = ['procesadas' => [], 'liberadas' => 0, 'pendientes' => 0];

        $resultado['liberadas'] = self::liberarBloqueosVencidos();

        for ($i = 0; $i < $max; $i++) {
            $id = self::tomarSiguiente();
            if ($id === 0) {
                break;
            }
            $resultado['procesadas'][] = self::validar($id);
        }

        $resultado['pendientes'] = self::contarPendientes();
        return $resultado;
    }

    private static function condicionElegible(): string
    {
        return "c.estado_revision = 'pendiente'
                AND c.revision_metodo = 'ninguno'
                AND (c.audio_ruta IS NOT NULL AND c.audio_ruta <> ''
                     OR c.audio_base64 IS NOT NULL AND c.audio_base64 <> '')
                AND (i.id IS NULL OR i.es_prueba = 0)"
            . CertificacionEncuestador::condicionValidas()
            . " AND ((c.origen_tipo = 'cuestionario' AND EXISTS (SELECT 1 FROM " . (new DbConection())->getTable('tbl_ficha_tecnica_encuestas') . " ff WHERE ff.id = c.tbl_ficha_tecnica_encuesta_id AND ff.eliminado = 'no'))
                  OR (c.origen_tipo = 'sondeo' AND EXISTS (SELECT 1 FROM " . (new DbConection())->getTable('tbl_sondeo') . " ss WHERE ss.id = c.tbl_sondeo_id AND ss.eliminado = 'no')))";
    }

    private static function fromElegibles(DbConection $db): string
    {
        return "FROM " . $db->getTable('tbl_certificacion_encuestador') . " c
                INNER JOIN " . $db->getTable('tbl_usuarios') . " u ON u.id = c.tbl_usuario_id
                INNER JOIN " . $db->getTable('tbl_votantes') . " v ON v.id = c.tbl_votante_id
                LEFT JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON i.id = c.tbl_cuestionario_intento_id";
    }

    private static function contarPendientes(): int
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $stmt = $pdo->query("SELECT COUNT(*) " . self::fromElegibles($db) . " WHERE " . self::condicionElegible());
            return (int)$stmt->fetchColumn();
        } finally {
            $db->closeConect();
        }
    }

    private static function historial(PDO $pdo, DbConection $db, int $id, string $antes, string $despues, string $comentario, ?string $transcripcion = null, ?string $iaJson = null): void
    {
        $pdo->prepare(
            "INSERT INTO " . $db->getTable('tbl_certificacion_revision_historial') . "
             (tbl_certificacion_id, estado_anterior, estado_nuevo, metodo, comentario, transcripcion, ia_json, tbl_usuario_id, dtcreate)
             VALUES (:cid, :ant, :nuevo, 'ia', :comentario, :transcripcion, :ia_json, NULL, NOW())"
        )->execute([
            ':cid' => $id,
            ':ant' => $antes,
            ':nuevo' => $despues,
            ':comentario' => $comentario,
            ':transcripcion' => $transcripcion,
            ':ia_json' => $iaJson,
        ]);
    }

    private static function liberarBloqueosVencidos(): int
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $liberadas = 0;
        try {
            $tabla = $db->getTable('tbl_certificacion_encuestador');
            $stmt = $pdo->prepare(
                "SELECT id FROM $tabla
                 WHERE estado_revision = 'en_revision' AND revision_metodo = 'ia' AND revision_ia_json IS NULL
                   AND revision_fecha < DATE_SUB(NOW(), INTERVAL " . self::MINUTOS_BLOQUEO . " MINUTE)"
            );
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $id) {
                self::registrarFallo((int)$id, 'tiempo de espera agotado en la pasada anterior', 'en_revision');
                $liberadas++;
            }
        } finally {
            $db->closeConect();
        }
        return $liberadas;
    }

    private static function tomarSiguiente(): int
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $pdo->beginTransaction();
            $tabla = $db->getTable('tbl_certificacion_encuestador');
            $hist = $db->getTable('tbl_certificacion_revision_historial');
            $stmt = $pdo->prepare(
                "SELECT c.id " . self::fromElegibles($db) . "
                 WHERE " . self::condicionElegible() . "
                   AND (SELECT COUNT(*) FROM $hist h WHERE h.tbl_certificacion_id = c.id AND h.comentario LIKE :pref) < " . self::MAX_FALLOS . "
                 ORDER BY c.fecha_certificacion ASC, c.id ASC
                 LIMIT 1 FOR UPDATE"
            );
            $stmt->execute([':pref' => self::PREFIJO_FALLO . '%']);
            $id = (int)$stmt->fetchColumn();
            if ($id === 0) {
                $pdo->commit();
                return 0;
            }
            $pdo->prepare(
                "UPDATE $tabla SET estado_revision = 'en_revision', revision_metodo = 'ia', revision_comentario = NULL,
                        revision_ia_json = NULL, revision_usuario_id = NULL, revision_fecha = NOW(), dtupdate = NOW()
                 WHERE id = :id"
            )->execute([':id' => $id]);
            self::historial($pdo, $db, $id, 'pendiente', 'en_revision', 'Cola automática: validación con IA en curso');
            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return 0;
        } finally {
            $db->closeConect();
        }
    }

    private static function validar(int $id): array
    {
        try {
            $res = CertificacionEncuestador::validarConIa(['id' => $id]);
        } catch (Throwable $e) {
            return self::registrarFallo($id, $e->getMessage());
        }
        if (!($res['output']['valid'] ?? false)) {
            $msg = $res['output']['response']['content'] ?? ($res['output']['message'] ?? 'respuesta inválida de la IA');
            $sinAudio = ($res['output']['error'] ?? '') === 'sin_audio';
            return self::registrarFallo($id, (string)$msg, 'en_revision', $sinAudio);
        }

        $r = $res['output']['response'];
        $estado = ($r['veredicto'] ?? '') === 'mal' ? 'mal' : 'bien';
        $comentario = trim((string)($r['comentario'] ?? ''));
        $confianza = (float)($r['confianza'] ?? 0);
        $requiereHumano = $confianza < self::CONFIANZA_MINIMA;
        if ($requiereHumano) {
            $comentario = 'Confianza baja de la IA (' . $confianza . ' de ' . self::CONFIANZA_MINIMA . ' requerido), sugiere "' . $estado . '". Requiere revisión humana.' . ($comentario !== '' ? "\n" . $comentario : '');
            $estado = 'en_revision';
        }
        if ($estado === 'mal' && $comentario === '') {
            $comentario = 'Marcada como Mal por la validación automática con IA.';
        }
        $transcripcion = (string)($r['transcripcion'] ?? '');
        $iaJson = json_encode($r['ia_json'] ?? [], JSON_UNESCAPED_UNICODE);

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $pdo->beginTransaction();
            $pdo->prepare(
                "UPDATE " . $db->getTable('tbl_certificacion_encuestador') . "
                 SET estado_revision = :estado, revision_metodo = 'ia', revision_comentario = :comentario,
                     revision_transcripcion = :transcripcion, revision_ia_json = :ia_json,
                     revision_usuario_id = NULL, revision_fecha = NOW(), dtupdate = NOW()
                 WHERE id = :id AND estado_revision = 'en_revision' AND revision_metodo = 'ia'"
            )->execute([
                ':estado' => $estado,
                ':comentario' => $comentario !== '' ? $comentario : null,
                ':transcripcion' => $transcripcion !== '' ? $transcripcion : null,
                ':ia_json' => $iaJson !== false ? $iaJson : null,
                ':id' => $id,
            ]);
            self::historial($pdo, $db, $id, 'en_revision', $estado, $comentario, $transcripcion !== '' ? $transcripcion : null, $iaJson !== false ? $iaJson : null);
            $pdo->commit();
            return ['id' => $id, 'estado' => $estado, 'confianza' => $r['confianza'] ?? null, 'requiere_humano' => $requiereHumano];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return self::registrarFallo($id, 'no se pudo guardar el veredicto: ' . $e->getMessage());
        } finally {
            $db->closeConect();
        }
    }

    private static function registrarFallo(int $id, string $mensaje, string $estadoActual = 'en_revision', bool $definitivo = false): array
    {
        $mensaje = mb_substr(trim($mensaje), 0, 300);
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $tabla = $db->getTable('tbl_certificacion_encuestador');
            $hist = $db->getTable('tbl_certificacion_revision_historial');
            $n = $pdo->prepare("SELECT COUNT(*) FROM $hist WHERE tbl_certificacion_id = :id AND comentario LIKE :pref");
            $n->execute([':id' => $id, ':pref' => self::PREFIJO_FALLO . '%']);
            $intento = (int)$n->fetchColumn() + 1;
            $manual = $definitivo || $intento >= self::MAX_FALLOS;

            $pdo->beginTransaction();
            if ($manual) {
                $pdo->prepare(
                    "UPDATE $tabla SET estado_revision = 'en_revision', revision_metodo = 'ninguno', revision_ia_json = NULL,
                            revision_comentario = :c, revision_fecha = NOW(), dtupdate = NOW() WHERE id = :id"
                )->execute([':c' => 'Requiere revisión manual: la IA automática no pudo validarla. ' . $mensaje, ':id' => $id]);
                $destino = 'en_revision';
            } else {
                $pdo->prepare(
                    "UPDATE $tabla SET estado_revision = 'pendiente', revision_metodo = 'ninguno', revision_ia_json = NULL,
                            revision_comentario = NULL, revision_fecha = NULL, dtupdate = NOW() WHERE id = :id"
                )->execute([':id' => $id]);
                $destino = 'pendiente';
            }
            self::historial($pdo, $db, $id, $estadoActual, $destino, self::PREFIJO_FALLO . ' (intento ' . $intento . '): ' . $mensaje);
            $pdo->commit();
            return ['id' => $id, 'estado' => $destino, 'error' => $mensaje, 'intento' => $intento];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['id' => $id, 'estado' => $estadoActual, 'error' => $mensaje];
        } finally {
            $db->closeConect();
        }
    }
}
