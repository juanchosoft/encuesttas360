<?php
session_start();
set_time_limit(280);

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/ia/ClaudeService.php';
require_once __DIR__ . '/../classes/ia/IaToolRegistry.php';
require_once __DIR__ . '/../classes/ia/AsistenteIA.php';
require_once __DIR__ . '/../classes/IaRecursos.php';

header('Content-Type: application/json; charset=utf-8');

if (!SessionData::hasPermission('ia.asistente.use')) {
    http_response_code(403);
    echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado', 'message' => 'No tienes permiso para usar el asistente IA.']]);
    exit;
}

$userId = (int) SessionData::getUserId();
$permisos = SessionData::getPermissionKeys();
$superAdmin = SessionData::superAdministrador();
$puedeAdjuntar = SessionData::hasPermission('ia.informes.create');
session_write_close();

$mensaje = trim((string) ($_POST['mensaje'] ?? ''));
$conversacionId = isset($_POST['conversacion_id']) && $_POST['conversacion_id'] !== '' ? (int) $_POST['conversacion_id'] : null;

$asistente = new AsistenteIA($userId, $permisos, $superAdmin);

$adjuntos = [];
if (!empty($_FILES['imagen']['name']) && is_array($_FILES['imagen']['name'])) {
    if (!$puedeAdjuntar) {
        echo json_encode(['output' => ['valid' => false, 'response' => ['valid' => false, 'mensaje' => 'No tienes permiso para adjuntar imágenes.']]]);
        exit;
    }
    $tipos = (array) ($_POST['tipo_imagen'] ?? []);
    foreach (array_slice(array_keys($_FILES['imagen']['name']), 0, 4) as $i) {
        $archivo = [];
        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $campo) {
            $archivo[$campo] = $_FILES['imagen'][$campo][$i];
        }
        try {
            $r = IaRecursos::guardarArchivoSubido($userId, $archivo, (string) ($tipos[$i] ?? 'foto'));
            $recurso = IaRecursos::cargar($r['id'], $userId, false);
            if ($recurso) {
                $adjuntos[] = $recurso;
            }
        } catch (RuntimeException $e) {
            echo json_encode(['output' => ['valid' => false, 'response' => ['valid' => false, 'mensaje' => $e->getMessage()]]], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}

$resultado = $asistente->enviarMensaje($conversacionId, $mensaje, 'texto', $adjuntos);

echo json_encode(['output' => ['valid' => $resultado['valid'], 'response' => $resultado]], JSON_UNESCAPED_UNICODE);
