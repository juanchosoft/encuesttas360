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
include './admin/classes/GestoraSocial.php';

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
$santander = $departamento->getAll(["id" => 21]);
$santander = $santander["output"]["response"]["0"];
$mapa = "admin/mapa_putumayo/mapa_gestora_social.php";
$code = $santander["codigo_departamento"];



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
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<body class="">
    <?php include 'admin/include/scriptsgober360.php'; ?>
        <style>
.mapaClick:hover {
  stroke: rgb(0, 238, 255);
  stroke-width: 2px;
  filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

  .color-table {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  }

  .color-table thead {
    background-color: #f8f9fa;
  }

  .color-table th,
  .color-table td {
    vertical-align: middle;
    padding: 12px 10px;
    font-size: 14px;
    color: #333;
  }

  .color-table th {
    font-weight: 600;
  }

  .color-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1px solid #ccc;
    display: inline-block;
    box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.1);
  }

  .color-table tbody tr:hover {
    background-color: #f1f3f5;
  }
        </style>

    <?php
    include './admin/include/navbar.php';
    ?>

    <?php
    include './admin/include/header.php';
    ?>


    <div class="content">
                      <div class="row mb-4 mb-xl-6 mb-xxl-4 gy-3 justify-content-between">
                        <div class="col-auto">
                            <h2 class="mb-0 text-body-emphasis">Dashboard Desarrollo social</h2>
                            
                        </div>
                        <h5 class="text-body-tertiary fw-semibold">
                            Mapa interactivo para <strong>Acciones Desarrollo social </strong>
                        </h5>
                        <div class="col-auto">
                        </div>
                        </div>
                            <div class="row text-center gx-3 gy-2 mb-2">
              <!-- Total Visitas Departamento -->
              <div class="col-12 col-md-4">
                <div class="d-flex align-items-center justify-content-center flex-column p-2 bg-body border rounded-3 shadow-sm h-100">
                  <i class="bi bi-geo-alt-fill fs-4 text-primary mb-1"></i>
                  <h6 class="text-body-secondary mb-1 small">Total Visitas</h6>
                  <h5 class="text-body-emphasis mb-0"><?= $visitas ?></h5>
                </div>
              </div>

              <!-- Total Población Impactada -->
              <div class="col-12 col-md-4">
                <div class="d-flex align-items-center justify-content-center flex-column p-2 bg-body border rounded-3 shadow-sm h-100">
                  <i class="bi bi-people-fill fs-4 text-danger mb-1"></i>
                  <h6 class="text-body-secondary mb-1 small">Población Impactada</h6>
                  <h5 class="text-body-emphasis mb-0"><?= $impactada ?></h5>
                </div>
              </div>

              <!-- Total Inversión -->
              <div class="col-12 col-md-4">
                <div class="d-flex align-items-center justify-content-center flex-column p-2 bg-body border rounded-3 shadow-sm h-100">
                  <i class="bi bi-cash-stack fs-4 text-success mb-1"></i>
                  <h6 class="text-body-secondary mb-1 small">Total Inversión</h6>
                  <h5 class="text-body-emphasis mb-0">$ <?= number_format($inversion, 0, '.', ',') ?></h5>
                </div>
              </div>
            </div>


            <div class="row g-4">
                
              <!-- CARD: Mapa -->
              <div class="col-md-9">
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-map text-primary fs-5"></i>
                    <h5 class="mb-0">Mapa <?php echo Util::nombreDelProyecto(); ?></h5>
                  </div>

                  <div class="card-body text-center">
              <?php if (!is_null($mapa)) : ?>
                <div class="cuerpoMapa w-100" >
                  <?php echo require_once "admin/mapa_putumayo/mapa_gestora_social.php"; ?>
                </div>
              <?php endif; ?>
            </div>

                </div>
              </div>
              <!-- CARD: Tabla de Valores -->
              <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                  <div class="card-header d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-sliders text-secondary fs-5"></i>
                    <h5 class="mb-0">Tabla de Valores de Referencia</h5>
                  </div>

                  <div class="card-body px-4 py-3">
                    <div class="table-responsive">
                    <table class="table color-table text-center align-middle mb-0">
                      <thead class="bg-body">
                        <tr>
                          <th class="text-body">Desde</th>
                          <th class="text-body">Hasta</th>
                          <th class="text-body">Color</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="text-body">0</td>
                          <td class="text-body">0</td>
                          <td><div class="color-circle" style="background-color: white;"></div></td>
                        </tr>
                        <tr>
                          <td class="text-body">1</td>
                          <td class="text-body">50</td>
                          <td><div class="color-circle" style="background-color: #f7c5ae;"></div></td>
                        </tr>
                        <tr>
                          <td class="text-body">51</td>
                          <td class="text-body">100</td>
                          <td><div class="color-circle" style="background-color: #ffa5ae;"></div></td>
                        </tr>
                        <tr>
                          <td class="text-body">100</td>
                          <td class="text-body">----</td>
                          <td><div class="color-circle" style="background-color: #ea9abd;"></div></td>
                        </tr>
                      </tbody>
                    </table>
                  <div class="mt-4 d-flex justify-content-center">
                    <button class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalSvgFull">
                      <i class="bi bi-arrows-fullscreen"></i> Ver Mapa Completo
                    </button>
                  </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <!-- MODAL PANTALLA COMPLETA -->
            <div class="modal fade" id="modalSvgFull" tabindex="-1" aria-labelledby="modalSvgFullLabel" aria-hidden="true">
              <div class="modal-dialog modal-fullscreen">
                <div class="modal-content bg-white">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalSvgFullLabel" style="color:white">Vista completa del mapa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body p-0">
                    <div class="w-100 h-100 overflow-auto p-3" style="background: #f9f9f9;">
                        
                      <div id="svg-container-full" class="w-100 h-100 d-flex justify-content-center align-items-center">
                        <!-- MAPA -->
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        <!--  Script -->
        <?php if (isset($_GET["route_map"])): ?>
        <?php endif ?>

        <?php include 'admin/include/gerenic_script.php'; ?>

        <!-- Required Js -->
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>

        <!-- prism Js -->
        <script src="assets/js/plugins/prism.js"></script>
        <script src="assets/js/plugins/apexcharts.min.js"></script>

        <script src="admin/js/gestora_social.js"></script>
        <!-- Script para pantalla completa -->
        <script src="admin/js/pantalla_completa.js"></script>




</body>

</html>