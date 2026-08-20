<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['method']) && $_POST['method'] === 'actualizarCompromiso') {
    require_once '../classes/Visitas.php';
    $user = new Visitas();
    echo json_encode($user->actualizarCompromiso($_POST, $_FILES));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['method']) && $_POST['method'] === 'guardaVisita') {
    require_once '../classes/Visitas.php';
    $user = new Visitas();
    echo json_encode($user->save($_POST, $_FILES));
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);


if (isset($data['method'])) {
    switch ($data['method']) {
        case 'getAllCompromise':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->getAllCompromise($data['data']));
            break;
        case 'getAllVisitas':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->getAllVisitas($data['data']));
            break;
        case 'indicadores':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->indicadores());
            break;
        case 'indicadoresTipoVisita':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->indicadoresTipoVisita());
            break;
        case 'getVisitaId':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->getVisitaId($data['data']));
            break;
        case 'getCompromisoId':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->getCompromisoId($data['data']));
            break;
        case 'mapa':
            require_once '../classes/Colombia.php';
            $user = new Colombia();
            echo json_encode($user->getInformacionParaMapaGestoraSocial($data['data']));
            break;
        case 'graficoSeguimiento':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->graficoSeguimiento(
                $data['idSecretaria'] ?? null,
                $data['componente'] ?? null,
                $data['tipo_ejecucion'] ?? null
            ));
            break;
        case 'porcentaje':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->porcentaje(
                $data['idSecretaria'] ?? null,
                $data['componente'] ?? null,
                $data['tipo_ejecucion'] ?? null
            ));
            break;
        case 'dataMapa':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->dataMapa(
                $data['idSecretaria'] ?? null,
                $data['componente'] ?? null,
                $data['tipo_ejecucion'] ?? null
            ));
            break;
        case 'dataPorMunicipioSecretaria':
            require_once '../classes/Visitas.php';
            $user = new Visitas();
            echo json_encode($user->dataPorMunicipioSecretaria($data));
            break;

        default:
            echo 'ninguna opción valida.';
            break;
    }
} else {
    echo 'ninguna opción valida.';
}
