<?php
session_start();

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';

header('Content-Type: application/json; charset=utf-8');

if (!SessionData::hasPermission('ia.asistente.use')) {
    http_response_code(403);
    echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado', 'message' => 'No tienes permiso para usar el asistente IA.']]);
    exit;
}

$accion = $_REQUEST['accion'] ?? 'listar';
$userId = (int) SessionData::getUserId();

$db = new DbConection();
$pdo = $db->openConect();

if ($accion === 'cargar') {
    $conversacionId = isset($_REQUEST['conversacion_id']) ? (int) $_REQUEST['conversacion_id'] : 0;

    $stmt = $pdo->prepare("SELECT tbl_usuario_id FROM " . $db->getTable('tbl_ia_conversaciones') . " WHERE id = :id");
    $stmt->execute([':id' => $conversacionId]);
    $conv = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conv || (int) $conv['tbl_usuario_id'] !== $userId) {
        http_response_code(404);
        echo json_encode(['output' => ['valid' => false, 'message' => 'Conversación no encontrada.']]);
        $db->closeConect();
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, rol, contenido, dt_create FROM " . $db->getTable('tbl_ia_mensajes') . "
                            WHERE tbl_ia_conversacion_id = :id AND contenido IS NOT NULL AND contenido <> ''
                            ORDER BY id ASC");
    $stmt->execute([':id' => $conversacionId]);
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['output' => ['valid' => true, 'response' => $mensajes]], JSON_UNESCAPED_UNICODE);
} else {
    $stmt = $pdo->prepare("SELECT id, titulo, dt_update FROM " . $db->getTable('tbl_ia_conversaciones') . "
                            WHERE tbl_usuario_id = :usuario ORDER BY dt_update DESC LIMIT 20");
    $stmt->execute([':usuario' => $userId]);
    $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['output' => ['valid' => true, 'response' => $conversaciones]], JSON_UNESCAPED_UNICODE);
}

$db->closeConect();
