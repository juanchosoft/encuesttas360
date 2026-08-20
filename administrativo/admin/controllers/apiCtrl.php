<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$data = json_decode(file_get_contents("php://input"), true);
/* controller alcaldias */
if (isset($data['method'])) {
    switch ($data['method']) {
        case 'cargaHurto':
            require_once '../classes/ApiPolicia.php';
            $user = new ApiPolicia();
            echo json_encode($user->cargaHurto($data['data']));
            break;
        case 'cargaCategoria':
            require_once '../classes/ApiPolicia.php';
            $user = new ApiPolicia();
            echo json_encode($user->cargaCategoria($data));
            break;
        case 'cargaCategoriaGrafico':
            require_once '../classes/ApiPolicia.php';
            $user = new ApiPolicia();
            echo json_encode($user->cargaCategoriaGrafico($data));
            break;

        default:
            echo 'ninguna opción valida.';
            break;
    }
} else {
    echo 'ninguna opción valida.';
}
