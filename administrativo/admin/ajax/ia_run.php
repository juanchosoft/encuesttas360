<?php
session_start();

require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/PermissionCatalog.php';
require_once __DIR__ . '/../classes/SessionData.php';
require_once __DIR__ . '/../classes/ia/ClaudeService.php';
require_once __DIR__ . '/../classes/ia/IaToolRegistry.php';
require_once __DIR__ . '/../classes/ia/AsistenteIA.php';
require_once __DIR__ . '/../classes/IaRecursos.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function responderRun(array $datos, int $http = 200): void
{
    http_response_code($http);
    echo json_encode(['output' => ['valid' => (bool) ($datos['valid'] ?? true), 'response' => $datos]], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!SessionData::hasPermission('ia.asistente.use')) {
    responderRun(['valid' => false, 'mensaje' => 'No tienes permiso para usar el asistente IA.'], 403);
}

$userId = (int) SessionData::getUserId();
$permisos = SessionData::getPermissionKeys();
$superAdmin = SessionData::superAdministrador();
$puedeAdjuntar = SessionData::hasPermission('ia.informes.create');
session_write_close();

$accion = (string) ($_REQUEST['accion'] ?? '');
$token = preg_replace('/[^a-f0-9]/', '', (string) ($_REQUEST['run'] ?? ''));
$asistente = new AsistenteIA($userId, $permisos, $superAdmin);

if ($accion === 'iniciar') {
    $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
    $conversacionId = isset($_POST['conversacion_id']) && $_POST['conversacion_id'] !== '' ? (int) $_POST['conversacion_id'] : null;
    $clientRequestId = (string) ($_POST['client_request_id'] ?? '');

    $duplicado = $asistente->mensajeDuplicadoReciente($mensaje);
    if ($duplicado !== null && !$asistente->solicitudConocida($clientRequestId)) {
        responderRun(['valid' => false, 'mensaje' => 'Ese mismo mensaje ya se procesó ' . ($duplicado['minutos'] < 1 ? 'hace menos de un minuto' : 'hace ' . $duplicado['minutos'] . ' minuto(s)') . '. Revisa Informes IA o el historial del chat antes de reenviarlo; así se evita generar el informe dos veces.']);
    }

    $adjuntos = [];
    if (!empty($_FILES['imagen']['name']) && is_array($_FILES['imagen']['name'])) {
        if (!$puedeAdjuntar) {
            responderRun(['valid' => false, 'mensaje' => 'No tienes permiso para adjuntar imágenes.']);
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
                responderRun(['valid' => false, 'mensaje' => $e->getMessage()]);
            }
        }
    }
    $informeId = isset($_POST['informe_id']) && $_POST['informe_id'] !== '' ? (int) $_POST['informe_id'] : null;
    responderRun($asistente->iniciarEjecucion($conversacionId, $mensaje, $clientRequestId, $adjuntos, $informeId));
}

if ($token === '') {
    responderRun(['valid' => false, 'mensaje' => 'Falta la ejecución.'], 400);
}

if ($accion === 'estado') {
    $estado = $asistente->estadoEjecucion($token);
    if ($estado === null) {
        responderRun(['valid' => false, 'estado' => 'no_encontrada', 'mensaje' => 'No se encontró la ejecución.'], 404);
    }
    responderRun(['valid' => true] + $estado);
}

if ($accion === 'cancelar') {
    responderRun(['valid' => $asistente->cancelarEjecucion($token)]);
}

if ($accion === 'reintentar') {
    responderRun(['valid' => $asistente->reintentarEjecucion($token)]);
}

if ($accion === 'conducir') {
    $adquirida = $asistente->adquirirEjecucion($token);
    if ($adquirida === null) {
        responderRun(['valid' => true, 'conduciendo' => false]);
    }
    ignore_user_abort(true);
    @set_time_limit(0);
    @ini_set('memory_limit', '768M');
    try {
        $asistente->avanzarEjecucion($adquirida['ejecucion'], $adquirida['lease']);
    } catch (Throwable $e) {
        $asistente->fallarEjecucion($adquirida['ejecucion'], $adquirida['lease'], 'interno', 'Ocurrió un error inesperado al procesar la consulta.');
    }
    responderRun(['valid' => true, 'conduciendo' => true]);
}

responderRun(['valid' => false, 'mensaje' => 'Acción no válida.'], 400);
