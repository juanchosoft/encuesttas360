<?php
session_start();
set_time_limit(300);

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/ia/ClaudeService.php';
require_once __DIR__ . '/../classes/ia/IaToolRegistry.php';
require_once __DIR__ . '/../classes/ia/AsistenteIA.php';
require_once __DIR__ . '/../classes/ia/ElevenLabsService.php';

header('Content-Type: application/json; charset=utf-8');

if (!SessionData::hasPermission('ia.voz.use')) {
    http_response_code(403);
    echo json_encode(['output' => ['valid' => false, 'error' => 'permiso_denegado', 'message' => 'No tienes permiso para usar el modo de voz.']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['output' => ['valid' => false, 'message' => 'Método no permitido.']]);
    exit;
}

const TAMANIO_MAX_BYTES = 25 * 1024 * 1024;
const MIME_PERMITIDOS = ['audio/webm', 'video/webm', 'audio/mp4', 'audio/ogg', 'audio/mpeg', 'audio/wav'];

$archivo = $_FILES['audio'] ?? null;
if (!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['output' => ['valid' => false, 'message' => 'No se recibió ningún archivo de audio válido.']]);
    exit;
}

if ($archivo['size'] > TAMANIO_MAX_BYTES) {
    http_response_code(400);
    echo json_encode(['output' => ['valid' => false, 'message' => 'El audio supera el tamaño máximo permitido (25MB).']]);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeReal = finfo_file($finfo, $archivo['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeReal, MIME_PERMITIDOS, true)) {
    http_response_code(400);
    echo json_encode(['output' => ['valid' => false, 'message' => 'Formato de audio no soportado.']]);
    exit;
}

$carpetaTmp = __DIR__ . '/../uploads/ia_tmp/';
$rutaTmp = $carpetaTmp . bin2hex(random_bytes(16)) . '.audio';

if (!move_uploaded_file($archivo['tmp_name'], $rutaTmp)) {
    http_response_code(500);
    echo json_encode(['output' => ['valid' => false, 'message' => 'No se pudo procesar el archivo de audio.']]);
    exit;
}

$conversacionId = isset($_POST['conversacion_id']) && $_POST['conversacion_id'] !== '' ? (int) $_POST['conversacion_id'] : null;

try {
    $transcripcion = ElevenLabsService::transcribir($rutaTmp);
} catch (RuntimeException $e) {
    @unlink($rutaTmp);
    http_response_code(502);
    echo json_encode(['output' => ['valid' => false, 'error' => 'error_stt', 'message' => $e->getMessage()]]);
    exit;
}

@unlink($rutaTmp);

$asistente = new AsistenteIA(
    (int) SessionData::getUserId(),
    SessionData::getPermissionKeys(),
    SessionData::superAdministrador()
);

$resultado = $asistente->enviarMensaje($conversacionId, $transcripcion, 'voz');
$resultado['transcripcion'] = $transcripcion;

echo json_encode(['output' => ['valid' => $resultado['valid'], 'response' => $resultado]], JSON_UNESCAPED_UNICODE);
