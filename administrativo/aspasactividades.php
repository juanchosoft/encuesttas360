<?php

include './admin/include/head.php';

require './admin/include/generic_classes.php';

function getUrl()
{
    $port = $_SERVER["SERVER_PORT"];
    $nameServer = $port != "80" ? $_SERVER['SERVER_NAME'] . ":" . $port : $_SERVER['SERVER_NAME'];
    $url = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $nameServer,
        $_SERVER['REQUEST_URI']
    );
    $final =  str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);
    $exists = strpos($final, "?");
    if ($exists == !false) {
        $final =  substr($final, 0, $exists);
        return $final;
    } else {
        return $final;
    }
}

require_once './admin/include/generic_classes.php';
include './admin/classes/Ciudad.php';
include './admin/classes/Estado.php';
require './admin/classes/Departamento.php';
include './admin/db/coloresg.php';
include './admin/classes/Maing.php';
include './admin/classes/Detalle.php';
include './admin/classes/Cuenta.php';
include './admin/classes/Cuentapro.php';
include './admin/classes/Secreinversion.php';
include './admin/classes/Munnovisitados.php';
include './admin/classes/GestoraSocialAspas.php';

// Obtener permisos
$permissions = [
    'view' => SessionData::getPermission(29),
    'create' => SessionData::getPermission(30),
    'edit' => SessionData::getPermission(31),
    'delete' => SessionData::getPermission(32),
];

// Validación de permiso de visualización
if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}


//informacion del mail
$arr = Maing::getDataMain(null);
$isvalid = $arr['output']['valid'];
$visitas = $arr['output']['visitas'];
$impactada = $arr['output']['impactada'];
$inversion = $arr['output']['inversion'];
$modulo = 'Primera Dama';

$departamento = new Departamento();
$santander = $departamento->getAll(["id" => Util::getIdentificadorDepartamentoPrincipal()]);
$santander = $santander["output"]["response"]["0"];
$code = null;
$mapa = null;

if (isset($_GET['depto_id']) && in_array($_GET['depto_id'], [1, 12, 21])) {
    switch ($_GET['depto_id']) {

        case Util::getIdentificadorDepartamentoPrincipal():
            $code = $santander["codigo_departamento"];
            $mapa = "admin/mapa-santander/mapa_gestora_social_aspas.php";
            break;
    }
}


if (!is_null($code)) {
    $arr = Ciudad::getAll(array('codigo_departamento' => $code));
    $finalMunicipios = $arr['output']['response'];
    $arrApoyoDep = Ciudad::getApoyoByCodigoDepartamento(array('codigo_departamento' => $code));
}

?>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.min.js"></script>
<!-- DataTables Select -->
<script src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/select/2.0.0/js/select.bootstrap4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
  <body>
    <main class="main" id="top">
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <?php
    include './admin/include/navbar.php';
    ?>
        <?php
    include './admin/include/header.php';
    ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-none border border-300" data-component-card>
                        <div class="card-header bg-body d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                            <h4 class="mb-0">Dashboard Gestora Social - Aspas</h4>
                        </div>
                            <div class="card-body px-4 py-3">
                                <!-- Indicadores principales -->
                                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start w-100">
                                    <!-- Item 1 -->
                                    <div class="card flex-fill p-3" style="min-width: 320px; max-width: 33%;">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="feather icon-map-pin text-c-blue fs-3"></i>
                                            <div>
                                                <h5 class="mb-1">Total Visitas Departamento</h5>
                                                <h2 class="mb-0"><?php echo $visitas; ?></h2>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 2 -->
                                    <div class="card flex-fill p-3" style="min-width: 320px; max-width: 33%;">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="feather icon-users text-c-red fs-3"></i>
                                            <div>
                                                <h5 class="mb-1">Total Población Impactada</h5>
                                                 <h2 class="mb-0"><?php echo $impactada; ?></h2>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item 3 -->
                                    <div class="card flex-fill p-3" style="min-width: 320px; max-width: 33%;">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="feather icon-check-circle text-c-green fs-3"></i>
                                            <div>
                                                <h5 class="mb-1">Total Inversión</h5>
                                                <h2 class="mb-0"><?php echo "$ " . number_format($inversion, 0, '.', ','); ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <!-- <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active text-uppercase" id="Soto_Norte-tab" data-toggle="tab" href="#Soto_Norte" role="tab" aria-controls="Soto_Norte" aria-selected="true">Soto Norte</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-uppercase" id="Guanenta-tab" data-toggle="tab" href="#Guanenta" role="tab" aria-controls="Guanenta" aria-selected="false">Guanentá</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-uppercase" id="Garcia_Rovira-tab" data-toggle="tab" href="#Garcia_Rovira" role="tab" aria-controls="Garcia_Rovira" aria-selected="false">García Rovira</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-uppercase" id="Comunera-tab" data-toggle="tab" href="#Comunera" role="tab" aria-controls="Comunera" aria-selected="false">Comunera</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-uppercase" id="Velez-tab" data-toggle="tab" href="#Velez" role="tab" aria-controls="Velez" aria-selected="false">Velez</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-uppercase" id="Metropolitana-tab" data-toggle="tab" href="#Metropolitana" role="tab" aria-controls="Metropolitana" aria-selected="false">Metropolitana</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-uppercase" id="Yariguíes-tab" data-toggle="tab" href="#Yariguíes" role="tab" aria-controls="Yariguíes" aria-selected="false">Yariguíes</a>
                                    </li>
                                </ul> -->
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="Soto_Norte" role="tabpanel" aria-labelledby="Soto_Norte-tab">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Total Población Impactada</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <!-- Gráfica -->
                                                        <div class="col-12 col-lg-8">
                                                            <div id="bar-chart-demo" style="min-height: 400px;"></div>
                                                        </div>

                                                        <!-- Tabla -->
                                                        <div class="col-12 col-lg-4">
                                                            <div class="mt-3 mt-lg-0">
                                                                <h6 class="text-body-highlight">Tabla de Valores de Referencia</h6>
                                                                <table class="table tabla-colores">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">Desde</th>
                                                                            <th scope="col">Hasta</th>
                                                                            <th scope="col">Color</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>0</td>
                                                                            <td>0</td>
                                                                            <td style="background-color: white;"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>1</td>
                                                                            <td>50</td>
                                                                            <td style="background-color: #f7c5ae;"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>51</td>
                                                                            <td>100</td>
                                                                            <td style="background-color: #ffa5ae;"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>100</td>
                                                                            <td>----</td>
                                                                            <td style="background-color: #ea9abd;"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div> <!-- /.row -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Mapa</h5>
                                            </div>
                                                                                
                                                                                <div class="card-body cuerpoMapa">
                                                <div class="santander munis">
                                                    <?php echo require_once "admin/mapa_putumayo/mapa_gestora_social_aspas.php"; ?>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>



        <!--  Script -->
        <?php if (isset($_GET["route_map"])): ?>
        <?php endif ?>

        <script>
            document.getElementById("btnAumentar").onclick = function() {
                aumentarTransform();
            };
            document.getElementById("btnReducir").onclick = function() {
                reducirTransform();
            };

            function aumentarTransform() {
                var elemento = document.getElementById("contenidoTransformado");
                var escalaActual = parseFloat(window.getComputedStyle(elemento).getPropertyValue("transform").split(
                    ",")[3]);
                var nuevaEscala = escalaActual + 0.1; // Aumentar la escala en 0.1
                elemento.style.transform = "scale(" + nuevaEscala + ")";
            }

            function reducirTransform() {
                var elemento = document.getElementById("contenidoTransformado");
                var escalaActual = parseFloat(window.getComputedStyle(elemento).getPropertyValue("transform").split(
                    ",")[3]);
                var nuevaEscala = escalaActual - 0.1; // Reducir la escala en 0.1
                if (nuevaEscala >= 0.1) { // Evitar escala negativa
                    elemento.style.transform = "scale(" + nuevaEscala + ")";
                }
            }
        </script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <?php include 'admin/include/gerenic_script.php'; ?>

        <!-- Required Js -->
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>

        <!-- prism Js -->
        <script src="assets/js/plugins/prism.js"></script>
        <!-- <script src="assets/js/plugins/apexcharts.min.js"></script> -->

        <script src="admin/js/gestora_social_aspas.js"></script>
        <style>
    .santander.munis path:hover,
    .santander.munis polygon:hover {
        transform: none !important;
        filter: none !important;
        stroke: none !important;
        fill: inherit !important;
        pointer-events: auto !important;
    }
</style>



</body>

</html>