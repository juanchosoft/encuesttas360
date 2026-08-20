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
    $final = str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);
    $exists = strpos($final, "?");
    if ($exists !== false) {
        $final = substr($final, 0, $exists);
    }
    return $final;
}

// ------------------------- MAPA 1: GestoraSocial -------------------------
include './admin/classes/Ciudad.php';
include './admin/classes/Estado.php';
include './admin/classes/Departamento.php';
include './admin/db/coloresg.php';
include './admin/classes/Maing.php';
include './admin/classes/Detalle.php';
include './admin/classes/Cuenta.php';
include './admin/classes/Cuentapro.php';
include './admin/classes/Secreinversion.php';
include './admin/classes/Munnovisitados.php';
include './admin/classes/GestoraSocial.php';
include './admin/classes/Colombia.php';
$permissions = [
    'view' => SessionData::getPermission(29),
    'create' => SessionData::getPermission(30),
    'edit' => SessionData::getPermission(31),
    'delete' => SessionData::getPermission(32),
];

if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}
// Obtener datos de GESTORA SOCIAL
$datosGestora = Maing::getDataMain(['modulo' => 'gestora']);
$validGestora = $datosGestora['output']['valid'];

$visitasGestora = $validGestora ? intval($datosGestora['output']['visitas']) : 0;
$impactadaGestora = $validGestora ? intval($datosGestora['output']['impactada']) : 0;
$inversionGestora = $validGestora ? floatval($datosGestora['output']['inversion']) : 0;

// Obtener datos de ASPAS
$datosAspas = Maing::getDataMain(['modulo' => 'aspas']);
$validAspas = $datosAspas['output']['valid'];

$visitasAspas = $validAspas ? intval($datosAspas['output']['visitas']) : 0;
$impactadaAspas = $validAspas ? intval($datosAspas['output']['impactada']) : 0;
$inversionAspas = $validAspas ? floatval($datosAspas['output']['inversion']) : 0;

// Sumar ambos
$visitas = $visitasGestora + $visitasAspas;
$impactada = $impactadaGestora + $impactadaAspas;
$inversion = $inversionGestora + $inversionAspas;

$arrgestora = array('codigo' => Util::getDepartamentoPrincipal());

$datagestora = Colombia::getInformacionParaMapaGestoraSocial($arrgestora);
$isvalid = $datagestora['output']['valid'];
$santandergestora =  $datagestora['output']['response'];


?>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<!-- DataTables -->
<!-- <script src="https://cdn.datatables.net/2.0.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.min.js"></script> -->
<!-- DataTables Select -->
<!-- <script src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.min.js"></script> -->
<script src="https://cdn.datatables.net/select/2.0.0/js/select.bootstrap4.min.js"></script>
<!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<style>
    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 0.5rem;
    }

    .card-header {
        font-weight: bold;
    }

    .progress {
        height: 20px;
        border-radius: 10px;
    }

    .progress-bar {
        line-height: 20px;
        font-size: 12px;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .bg-cumplidos {
        background-color: #0d5fa7 !important;
        /* azul oscuro */
        color: white !important;
    }

    .bg-cumplidos small {
        color: white !important;
    }
</style>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ navigation menu ] start -->
    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->


    <!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- [ breadcrumb ] start -->
                            <div class="page-header">
                                <div class="page-block">
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="m-b-10">Dashboard Gestión Social</h5>
<?php include './admin/include/btn_back.php'; ?>
                                            </div>
                                            <ul class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                                <li class="breadcrumb-item"><a href="#!">Mapa visitas</a></li>
                                                <li class="breadcrumb-item"><a href="#!">Gestión cumplimiento</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Main Content ] start -->
                            <div class="card">
                                <div class="card-header text-white text-center">
                                    <h5 class="mb-0">Gestión de Cumplimiento</h5>
                                </div>
                                <div class="card-body">

                                    <div class="row">

                                        <!-- Indicadores IZQUIERDA -->
                                        <div class="col-md-3">
                                            <div class="card text-center mb-3">
                                                <div class="card-body p-2">
                                                    <h3 class="font-weight-bold mb-0 text-dark" id="total-compromisos">0</h3>
                                                    <small class="text-uppercase text-muted">Compromisos</small>
                                                </div>
                                            </div>

                                            <div class="card text-white bg-success mb-3">
                                                <div class="card-body p-2 text-center">
                                                    <h4 class="mb-0" id="compromisos-cumplidos">0</h4>
                                                    <small id="porcentaje-cumplidos">Cumplidos (0%)</small>
                                                </div>
                                            </div>

                                            <div class="card text-dark bg-warning mb-3">
                                                <div class="card-body p-2 text-center">
                                                    <h4 class="mb-0" id="compromisos-tramite">0</h4>
                                                    <small id="porcentaje-tramite">En trámite (0%)</small>
                                                </div>
                                            </div>

                                            <div class="card text-white bg-danger mb-3">
                                                <div class="card-body p-2 text-center">
                                                    <h4 class="mb-0" id="compromisos-sincumplir">0</h4>
                                                    <small id="porcentaje-sincumplir">Sin cumplir (0%)</small>
                                                </div>
                                            </div>

                                            <div class="card text-center mb-3">
                                                <div class="card-body p-2">
                                                    <h6 class="mb-1" id="total-provincias">0</h6>
                                                    <small class="text-muted">Provincias</small>
                                                </div>
                                            </div>

                                            <div class="card text-center mb-3">
                                                <div class="card-body p-2">
                                                    <h6 class="mb-1" id="total-municipios">0</h6>
                                                    <small class="text-muted">Municipios</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- MAPA CENTRO -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <div class="cuerpoMapa w-12">
                                                        <div id="contenido-mapa" class="cuerpoMapa w-12">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 788.66 885.68">
                                                                <?php foreach ($santandergestora as $key => $value) : ?>
                                                                    <g id="<?php echo strtoupper($value['path']); ?>">
                                                                        <path id="<?php echo strtoupper($value['path']); ?>"
                                                                            d="<?php echo $value['d']; ?>"
                                                                            fill="#f7fbff"
                                                                            class="municipios"
                                                                            data-name="<?php echo strtolower($value['municipio']); ?>"
                                                                            stroke="#000" stroke-miterlimit="10" stroke-width="0.1px">
                                                                        </path>
                                                                    </g>
                                                                <?php endforeach; ?>
                                                                <?php require_once 'nombres_mapa_santander.php' ?>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Indicadores DERECHA -->
                                        <div class="col-md-3">

                                            <div class="card mb-3">
                                                <div class="card-body py-2 px-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-12 col-xs-12">
                                                            <label for="tbl_secretarias_id" class="form-label fw-bold mb-1">
                                                                Seleccionar Secretaría
                                                            </label>
                                                            <select name="tbl_secretarias_id" id="tbl_secretarias_id" class="form-select form-control">
                                                                <option value="">Seleccione</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-3">
                                                <div class="card-header text-white p-2 text-center">
                                                    <h6 class="mb-0">Compromisos por Provincia</h6>
                                                </div>
                                                <div class="card-body p-2">
                                                    <canvas id="graficoProvincias" height="260"></canvas>
                                                </div>
                                            </div>

                                            <div class="card text-center mb-3 bg-cumplidos text-white">
                                                <div class="card-body p-2">
                                                    <h5 class="mb-0 text-white" id="nivel-cumplimiento">0</h5>
                                                    <small class="text-white">Nivel de cumplimiento</small>
                                                </div>
                                            </div>

                                            <div class="card text-center">
                                                <div class="card-body p-2">
                                                    <h5 class="text-dark mb-0" id="porcentaje-total-compromisos">0</h5>
                                                    <small class="text-muted">Total compromisos</small>
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
            <?php include 'admin/include/footer.php'; ?>


            <?php include 'admin/include/gerenic_script.php'; ?>

            <!-- Required Js -->
            <script src="assets/js/vendor-all.min.js"></script>
            <script src="assets/js/plugins/bootstrap.min.js"></script>
            <script src="assets/js/pcoded.min.js"></script>

            <!-- prism Js -->
            <script src="assets/js/plugins/prism.js"></script>
            <script src="assets/js/plugins/apexcharts.min.js"></script>

            <script src="admin/js/gestora_social.js"></script>
            <script src="admin/js/gestora_social_aspas.js"></script>
            <script src="admin/js/gestion-cumplimiento.js"></script>

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
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"
                integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
            </script>

            <script>
                $("img").each(function(index, el) {
                    $(this).attr("data-bs-toggle", "tooltip");
                    $(this).attr("data-bs-placement", "left");
                    tooltip = new bootstrap.Tooltip($(this)[0], {})
                });
                $(".mapaClick").click(function(event) {
                    location.href = $(this).data("url");
                });
            </script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>

<style>
    .content-map {
        background-color: #ffffff !important;
        padding: 20px 0;
    }


    #mapa {
        background-color: transparent;
        background-repeat: no-repeat;
        background-position: center;
        width: 100%;
        height: auto;
        margin: 0 auto;
        text-align: center;
        padding: 0.1px 0;
    }

    #mapa svg {
        max-width: 950px;

        width: 100%;

    }

    #mapa svg path {
        fill: #fff;
        transition: all .4s;
    }

    #mapa svg path:hover {
        fill: #636363
    }

    #mapa img {
        position: absolute;
    }
</style>


</body>

</html>