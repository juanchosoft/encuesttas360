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
include './admin/classes/Colombia.php'; // incluir la clase

include './admin/classes/Ciudad.php';
include './admin/classes/Pilar.php';
require './admin/classes/Departamento.php';
include './admin/db/coloress.php';
include './admin/classes/Maing.php';
include './admin/classes/Secretarias.php';

// Obtener permisos
$permissions = [
    'view' => SessionData::getPermission(39),
    'create' => SessionData::getPermission(39),
    'edit' => SessionData::getPermission(39),
    'delete' => SessionData::getPermission(39),
];

// Validación de permiso de visualización
if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}


// Informacion del Main
$arr = Maing::getDataMain(null);
$isvalid = $arr['output']['valid'];
$visitas = $arr['output']['visitas'];
$impactada = $arr['output']['impactada'];
$inversion = $arr['output']['inversion'];


$departamento = new Departamento();
$santander = $departamento->getAll(["id" => Util::getIdentificadorDepartamentoPrincipal()]);
$santander = $santander["output"]["response"]["0"];
$code = $santander["codigo_departamento"];
$mapa = "admin/mapa_putumayo/mapa_secretarias.php";

// Informacion de los pilares
$arr = Secretarias::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$optionSecretarias = "";
foreach ($arr as $val) {
    $optionSecretarias .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . " </option>";
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

<body class="">
    <style>
        #contenido-mapa polygon:hover,
        #contenido-mapa path:hover {
        stroke:rgb(0, 238, 255);
        stroke-width: 2px;
        filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        }

    </style>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
 <div class="content">
        <div class="row mb-4 mb-xl-6 mb-xxl-4 gy-3 justify-content-between">
          <div class="col-auto">
            <h2 class="mb-0 text-body-emphasis">Informes Secretarias</h2>
          </div>
          <div class="col-auto">
          </div>
        </div>
        
          <div class="col-xxl-20">
            <div class="row gx-7 pe-xxl-3">
            <div class="col-12 col-xl-8 col-xxl-8">
            <div class="col-sm-6 col-md-4">
                <div class="form-floating mb-4">
                    <select class="form-select" id="secretaria" name="secretaria">
                    <?php echo $optionSecretarias; ?>
                    </select>
                    <label for="Eje">Secretaria<span class="text-danger mb-1">*</span></label>
                </div>
                </div>
                <!-- SUBMENU DE ESTADOS -->
                <div class="card-body p-0">
                    <div class="pt-2 ps-1">
                        
                    </div>
                    </div>


                  <!-- DATOS DINAMICOS -->
                <!-- SOTO NORTE -->
                <div class="tab-content" id="myTabContent">
                
                <!-- SOTO NORTE -->
                <div class="tab-pane fade show active" id="Soto_Norte" role="tabpanel" aria-labelledby="Soto_Norte-tab">
  <div class="row gx-3 gy-3 mb-4">

    <div class="col-12">
      <h3 class="text-center my-4">Total Población Impactada</h3>
    </div>

    <!-- Total Inversión Departamento -->
    <div class="col-12 col-md-4">
      <div class="d-flex align-items-center justify-content-center flex-column p-3 bg-body border rounded-3 shadow-sm h-100">
        <i class="bi bi-bank fs-4 text-primary mb-1"></i>
        <h6 class="text-body-secondary mb-1 small">Total Inversión Departamento</h6>
        <h5 class="text-body-emphasis mb-0" id="inversion-depto-Soto_Norte">$0</h5>
        <span class="badge badge-phoenix badge-phoenix-primary mt-1 fs-10">
          <i class="fa-solid fa-plus me-1"></i><span id="variacion-inv-depto-Soto_Norte">0%</span>
        </span>
        <span class="fs-9 text-body-secondary mt-1">Comparado al mes anterior</span>
      </div>
    </div>

    <!-- Porcentaje Ejecución Presupuesto -->
    <div class="col-12 col-md-4">
      <div class="d-flex align-items-center justify-content-center flex-column p-3 bg-body border rounded-3 shadow-sm h-100">
        <i class="fa-solid fa-cloud-bolt fs-4 text-warning mb-1"></i>
        <h6 class="text-body-secondary mb-1 small">Porcentaje Ejecución Presupuesto</h6>
        <h5 class="text-body-emphasis mb-0" id="porcentaje-ejecucion-Soto_Norte">0%</h5>
        <span class="badge badge-phoenix badge-phoenix-success mt-1 fs-10">
          <i class="fa-solid fa-plus me-1"></i><span id="variacion-ejecucion-Soto_Norte">0%</span>
        </span>
        <span class="fs-9 text-body-secondary mt-1">Comparado al mes anterior</span>
        <div class="fs-9 mt-1 text-muted" id="valor-ejecutado-Soto_Norte">0</div>
      </div>
    </div>

    <!-- Total Inversión -->
    <div class="col-12 col-md-4">
      <div class="d-flex align-items-center justify-content-center flex-column p-3 bg-body border rounded-3 shadow-sm h-100">
        <i class="bi bi-cash-stack fs-4 text-success mb-1"></i>
        <h6 class="text-body-secondary mb-1 small">Total Inversión</h6>
        <h5 class="text-body-emphasis mb-0" id="total-inversion-Soto_Norte">
          <?= "$ " . number_format($inversion, 0, '.', ',') ?>
        </h5>
        <span class="badge badge-phoenix badge-phoenix-danger mt-1 fs-10">
          <i class="fa-solid fa-minus me-1"></i><span id="variacion-total-inv-Soto_Norte">0%</span>
        </span>
        <span class="fs-9 text-body-secondary mt-1">Comparado al mes anterior</span>
      </div>
    </div>

  </div>
</div>


                <!-- GUANENTA -->
                <div class="tab-pane fade" id="Guanenta" role="tabpanel" aria-labelledby="Guanenta-tab">
                <div class="row g-0">
                    <div class="col-12 my-4"> <!-- aumenta espacio arriba y abajo -->
                        <h3 class="text-center mb-3 mt-3">Total Población Impactada Guanentá</h3>
                    </div>
                    <!-- Total Inversión Departamento -->
                    <div class="col-6 col-xl-12 col-xxl-6 border-bottom border-end border-end-xl-0 border-end-xxl pb-4 pt-4 pt-xl-0 pt-xxl-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                    <h5 class="text-body mb-4">Total Inversión Departamento</h5>
                    <div class="d-md-flex flex-between-center">
                        <div class="echart-booking-value order-1 order-sm-0 order-md-1" style="height:54px; width: 90px"></div>
                        <div class="mt-4 mt-md-0 ">
                        <h3 class="text-body-highlight mb-2" id="inversion-depto-Guanenta">$0</h3>
                        <span class="badge badge-phoenix badge-phoenix-primary me-2 fs-10">
                            <span class="fa-solid fa-plus me-1"></span><span id="variacion-inv-depto-Guanenta">0%</span>
                        </span>
                        <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                        </div>
                    </div>
                    </div>

                    <!-- Porcentaje Ejecución Presupuesto -->
                    <div class="col-6 col-xl-12 col-xxl-6 border-bottom py-4 ps-4 ps-sm-5 ps-xl-0 ps-xxl-5">
                    <h5 class="text-body mb-4">Porcentaje Ejecución Presupuesto</h5>
                    <div class="d-md-flex flex-between-center">
                        <div class="d-md-flex align-items-center gap-2 order-sm-0 order-md-1">
                        <span class="fa-solid fa-cloud-bolt fs-5 text-warning-light dark__text-opacity-75"></span>
                        <div class="d-flex d-md-block gap-2 align-items-center mt-1 mt-md-0">
                            <p class="fs-9 mb-0 mb-md-2 text-body-tertiary text-nowrap">Ejecución</p>
                            <h4 class="text-body-highlight mb-0" id="porcentaje-ejecucion-Guanenta">0%</h4>
                        </div>
                        </div>
                        <div class="mt-3 mt-md-0">
                        <h3 class="text-body-highlight mb-2" id="valor-ejecutado-Guanenta">0</h3>
                        <span class="badge badge-phoenix badge-phoenix-success me-2 fs-10">
                            <span class="fa-solid fa-plus me-1"></span><span id="variacion-ejecucion-Guanenta">0%</span>
                        </span>
                        <span class="fs-9 text-body-secondary text-nowrap d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                        </div>
                    </div>
                    </div>

                    <!-- Total Inversión -->
                    <div class="col-6 col-xl-12 col-xxl-6 border-bottom-xl border-bottom-xxl-0 border-end border-end-xl-0 border-end-xxl py-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                    <h5 class="text-body mb-4">Total Inversión</h5>
                    <div class="d-md-flex flex-between-center">
                        <div class="echart-commission order-sm-0 order-md-1" style="height: 54px; width: 54px"></div>
                        <div class="mt-3 mt-md-0 ">
                        <h3 class="text-body-highlight mb-2" id="total-inversion-Guanenta">
                            <?php echo "$ " . number_format($inversion, 0, '.', ','); ?>
                        </h3>
                        <span class="badge badge-phoenix badge-phoenix-danger me-2 fs-10">
                            <span class="fa-solid fa-minus me-1"></span><span id="variacion-total-inv-Guanenta">0%</span>
                        </span>
                        <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Gráfico de barras -->
        <div id="bar-chart-Guanenta" class="mt-4"></div>
        </div>


            <!-- GARCÍA ROVIRA -->
            <div class="tab-pane fade" id="Garcia_Rovira" role="tabpanel" aria-labelledby="Garcia_Rovira-tab">
            <div class="row g-0">
                    <div class="col-12 my-4"> <!-- aumenta espacio arriba y abajo -->
                        <h3 class="text-center mb-3 mt-3">Total Población Impactada García Rovira</h3>
                    </div>
                <!-- Total Inversión Departamento -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom border-end border-end-xl-0 border-end-xxl pb-4 pt-4 pt-xl-0 pt-xxl-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión Departamento</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-booking-value order-1 order-sm-0 order-md-1" style="height:54px; width: 90px"></div>
                    <div class="mt-4 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="inversion-depto-Garcia_Rovira">$0</h3>
                    <span class="badge badge-phoenix badge-phoenix-primary me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-inv-depto-Garcia_Rovira">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Porcentaje Ejecución Presupuesto -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom py-4 ps-4 ps-sm-5 ps-xl-0 ps-xxl-5">
                <h5 class="text-body mb-4">Porcentaje Ejecución Presupuesto</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="d-md-flex align-items-center gap-2 order-sm-0 order-md-1">
                    <span class="fa-solid fa-cloud-bolt fs-5 text-warning-light dark__text-opacity-75"></span>
                    <div class="d-flex d-md-block gap-2 align-items-center mt-1 mt-md-0">
                        <p class="fs-9 mb-0 mb-md-2 text-body-tertiary text-nowrap">Ejecución</p>
                        <h4 class="text-body-highlight mb-0" id="porcentaje-ejecucion-Garcia_Rovira">0%</h4>
                    </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                    <h3 class="text-body-highlight mb-2" id="valor-ejecutado-Garcia_Rovira">0</h3>
                    <span class="badge badge-phoenix badge-phoenix-success me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-ejecucion-Garcia_Rovira">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary text-nowrap d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Total Inversión -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom-xl border-bottom-xxl-0 border-end border-end-xl-0 border-end-xxl py-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-commission order-sm-0 order-md-1" style="height: 54px; width: 54px"></div>
                    <div class="mt-3 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="total-inversion-Garcia_Rovira">
                        <?php echo "$ " . number_format($inversion, 0, '.', ','); ?>
                    </h3>
                    <span class="badge badge-phoenix badge-phoenix-danger me-2 fs-10">
                        <span class="fa-solid fa-minus me-1"></span><span id="variacion-total-inv-Garcia_Rovira">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>
            </div>

            <!-- Gráfico de barras -->
            <div id="bar-chart-Garcia_Rovira" class="mt-4"></div>
            </div>


            <!-- COMUNERA -->
            <div class="tab-pane fade" id="Comunera" role="tabpanel" aria-labelledby="Comunera-tab">
            <div class="row g-0">
                    <div class="col-12 my-4"> <!-- aumenta espacio arriba y abajo -->
                        <h3 class="text-center mb-3 mt-3">Total Población Impactada Comunera</h3>
                    </div>
                <!-- Total Inversión Departamento -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom border-end border-end-xl-0 border-end-xxl pb-4 pt-4 pt-xl-0 pt-xxl-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión Departamento</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-booking-value order-1 order-sm-0 order-md-1" style="height:54px; width: 90px"></div>
                    <div class="mt-4 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="inversion-depto-Comunera">$0</h3>
                    <span class="badge badge-phoenix badge-phoenix-primary me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-inv-depto-Comunera">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Porcentaje Ejecución Presupuesto -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom py-4 ps-4 ps-sm-5 ps-xl-0 ps-xxl-5">
                <h5 class="text-body mb-4">Porcentaje Ejecución Presupuesto</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="d-md-flex align-items-center gap-2 order-sm-0 order-md-1">
                    <span class="fa-solid fa-cloud-bolt fs-5 text-warning-light dark__text-opacity-75"></span>
                    <div class="d-flex d-md-block gap-2 align-items-center mt-1 mt-md-0">
                        <p class="fs-9 mb-0 mb-md-2 text-body-tertiary text-nowrap">Ejecución</p>
                        <h4 class="text-body-highlight mb-0" id="porcentaje-ejecucion-Comunera">0%</h4>
                    </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                    <h3 class="text-body-highlight mb-2" id="valor-ejecutado-Comunera">0</h3>
                    <span class="badge badge-phoenix badge-phoenix-success me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-ejecucion-Comunera">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary text-nowrap d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Total Inversión -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom-xl border-bottom-xxl-0 border-end border-end-xl-0 border-end-xxl py-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-commission order-sm-0 order-md-1" style="height: 54px; width: 54px"></div>
                    <div class="mt-3 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="total-inversion-Comunera">
                        <?php echo "$ " . number_format($inversion, 0, '.', ','); ?>
                    </h3>
                    <span class="badge badge-phoenix badge-phoenix-danger me-2 fs-10">
                        <span class="fa-solid fa-minus me-1"></span><span id="variacion-total-inv-Comunera">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>
            </div>

            <!-- Gráfico de barras -->
            <div id="bar-chart-Comunera" class="mt-4"></div>
            </div>


            <!-- VELEZ -->
            <div class="tab-pane fade" id="Velez" role="tabpanel" aria-labelledby="Velez-tab">
            <div class="row g-0">
                    <div class="col-12 my-4"> <!-- aumenta espacio arriba y abajo -->
                        <h3 class="text-center mb-3 mt-3">Total Población Impactada Vélez </h3>
                    </div>
                <!-- Total Inversión Departamento -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom border-end border-end-xl-0 border-end-xxl pb-4 pt-4 pt-xl-0 pt-xxl-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión Departamento</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-booking-value order-1 order-sm-0 order-md-1" style="height:54px; width: 90px"></div>
                    <div class="mt-4 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="inversion-depto-Velez">$0</h3>
                    <span class="badge badge-phoenix badge-phoenix-primary me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-inv-depto-Velez">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Porcentaje Ejecución Presupuesto -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom py-4 ps-4 ps-sm-5 ps-xl-0 ps-xxl-5">
                <h5 class="text-body mb-4">Porcentaje Ejecución Presupuesto</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="d-md-flex align-items-center gap-2 order-sm-0 order-md-1">
                    <span class="fa-solid fa-cloud-bolt fs-5 text-warning-light dark__text-opacity-75"></span>
                    <div class="d-flex d-md-block gap-2 align-items-center mt-1 mt-md-0">
                        <p class="fs-9 mb-0 mb-md-2 text-body-tertiary text-nowrap">Ejecución</p>
                        <h4 class="text-body-highlight mb-0" id="porcentaje-ejecucion-Velez">0%</h4>
                    </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                    <h3 class="text-body-highlight mb-2" id="valor-ejecutado-Velez">0</h3>
                    <span class="badge badge-phoenix badge-phoenix-success me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-ejecucion-Velez">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary text-nowrap d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Total Inversión -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom-xl border-bottom-xxl-0 border-end border-end-xl-0 border-end-xxl py-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-commission order-sm-0 order-md-1" style="height: 54px; width: 54px"></div>
                    <div class="mt-3 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="total-inversion-Velez">
                        <?php echo "$ " . number_format($inversion, 0, '.', ','); ?>
                    </h3>
                    <span class="badge badge-phoenix badge-phoenix-danger me-2 fs-10">
                        <span class="fa-solid fa-minus me-1"></span><span id="variacion-total-inv-Velez">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>
            </div>

            <!-- Gráfico de barras -->
            <div id="bar-chart-Velez" class="mt-4"></div>
            </div>


            <!-- METROPOLITANA -->
            <div class="tab-pane fade" id="Metropolitana" role="tabpanel" aria-labelledby="Metropolitana-tab">
            <div class="row g-0">
                     <div class="col-12 my-4"> <!-- aumenta espacio arriba y abajo -->
                        <h3 class="text-center mb-3 mt-3">Total Población Impactada Metropolitana </h3>
                    </div>
                <!-- Total Inversión Departamento -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom border-end border-end-xl-0 border-end-xxl pb-4 pt-4 pt-xl-0 pt-xxl-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión Departamento</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-booking-value order-1 order-sm-0 order-md-1" style="height:54px; width: 90px"></div>
                    <div class="mt-4 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="inversion-depto-Metropolitana">$0</h3>
                    <span class="badge badge-phoenix badge-phoenix-primary me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-inv-depto-Metropolitana">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Porcentaje Ejecución Presupuesto -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom py-4 ps-4 ps-sm-5 ps-xl-0 ps-xxl-5">
                <h5 class="text-body mb-4">Porcentaje Ejecución Presupuesto</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="d-md-flex align-items-center gap-2 order-sm-0 order-md-1">
                    <span class="fa-solid fa-cloud-bolt fs-5 text-warning-light dark__text-opacity-75"></span>
                    <div class="d-flex d-md-block gap-2 align-items-center mt-1 mt-md-0">
                        <p class="fs-9 mb-0 mb-md-2 text-body-tertiary text-nowrap">Ejecución</p>
                        <h4 class="text-body-highlight mb-0" id="porcentaje-ejecucion-Metropolitana">0%</h4>
                    </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                    <h3 class="text-body-highlight mb-2" id="valor-ejecutado-Metropolitana">0</h3>
                    <span class="badge badge-phoenix badge-phoenix-success me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-ejecucion-Metropolitana">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary text-nowrap d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Total Inversión -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom-xl border-bottom-xxl-0 border-end border-end-xl-0 border-end-xxl py-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-commission order-sm-0 order-md-1" style="height: 54px; width: 54px"></div>
                    <div class="mt-3 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="total-inversion-Metropolitana">
                        <?php echo "$ " . number_format($inversion, 0, '.', ','); ?>
                    </h3>
                    <span class="badge badge-phoenix badge-phoenix-danger me-2 fs-10">
                        <span class="fa-solid fa-minus me-1"></span><span id="variacion-total-inv-Metropolitana">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>
            </div>

            <!-- Gráfico de barras -->
            <div id="bar-chart-Metropolitana" class="mt-4"></div>
            </div>


            <!-- YARIGUIES -->
            <div class="tab-pane fade" id="Yariguíes" role="tabpanel" aria-labelledby="Yariguíes-tab">
            <div class="row g-0">
            <div class="col-12 my-4"> <!-- aumenta espacio arriba y abajo -->
                        <h3 class="text-center mb-3 mt-3">Total Población Impactada Yariguíes</h3>
                    </div>
                <!-- Total Inversión Departamento -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom border-end border-end-xl-0 border-end-xxl pb-4 pt-4 pt-xl-0 pt-xxl-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión Departamento</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-booking-value order-1 order-sm-0 order-md-1" style="height:54px; width: 90px"></div>
                    <div class="mt-4 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="inversion-depto-Yariguies">$0</h3>
                    <span class="badge badge-phoenix badge-phoenix-primary me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-inv-depto-Yariguies">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Porcentaje Ejecución Presupuesto -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom py-4 ps-4 ps-sm-5 ps-xl-0 ps-xxl-5">
                <h5 class="text-body mb-4">Porcentaje Ejecución Presupuesto</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="d-md-flex align-items-center gap-2 order-sm-0 order-md-1">
                    <span class="fa-solid fa-cloud-bolt fs-5 text-warning-light dark__text-opacity-75"></span>
                    <div class="d-flex d-md-block gap-2 align-items-center mt-1 mt-md-0">
                        <p class="fs-9 mb-0 mb-md-2 text-body-tertiary text-nowrap">Ejecución</p>
                        <h4 class="text-body-highlight mb-0" id="porcentaje-ejecucion-Yariguies">0%</h4>
                    </div>
                    </div>
                    <div class="mt-3 mt-md-0">
                    <h3 class="text-body-highlight mb-2" id="valor-ejecutado-Yariguies">0</h3>
                    <span class="badge badge-phoenix badge-phoenix-success me-2 fs-10">
                        <span class="fa-solid fa-plus me-1"></span><span id="variacion-ejecucion-Yariguies">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary text-nowrap d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>

                <!-- Total Inversión -->
                <div class="col-6 col-xl-12 col-xxl-6 border-bottom-xl border-bottom-xxl-0 border-end border-end-xl-0 border-end-xxl py-4 pe-4 pe-sm-5 pe-xl-0 pe-xxl-5">
                <h5 class="text-body mb-4">Total Inversión</h5>
                <div class="d-md-flex flex-between-center">
                    <div class="echart-commission order-sm-0 order-md-1" style="height: 54px; width: 54px"></div>
                    <div class="mt-3 mt-md-0 ">
                    <h3 class="text-body-highlight mb-2" id="total-inversion-Yariguies">
                        <?php echo "$ " . number_format($inversion, 0, '.', ','); ?>
                    </h3>
                    <span class="badge badge-phoenix badge-phoenix-danger me-2 fs-10">
                        <span class="fa-solid fa-minus me-1"></span><span id="variacion-total-inv-Yariguies">0%</span>
                    </span>
                    <span class="fs-9 text-body-secondary d-block d-sm-inline mt-1">Comparado al mes anterior</span>
                    </div>
                </div>
                </div>
            </div>

            <!-- Gráfico de barras -->
            <div id="bar-chart-Yariguíes" class="mt-4"></div>
            </div>

        </div>
        </div>
<!-- Tabla de Colores y Puntaje con Estética Mejorada -->
<div class="col-12 col-xl-4 col-xxl-4">
  <div class="card shadow-sm border rounded-3 mt-5 mb-4">
    <div class="card-header text-center bg-body-tertiary">
      <h5 class="mb-0 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-palette2 text-secondary fs-5"></i> Colores y Puntaje
      </h5>
    </div>
    <div class="card-body p-3">
      <table class="table table-bordered tabla-colores text-center align-middle mb-0">
        <thead class="table-light">
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
            <td><span class="color-circle d-inline-block rounded-circle" style="background-color: white; width: 20px; height: 20px; border: 1px solid #ccc;"></span></td>
          </tr>
          <tr>
            <td>1</td>
            <td>400</td>
            <td><span class="color-circle d-inline-block rounded-circle" style="background-color: #FF00FF; width: 20px; height: 20px;"></span></td>
          </tr>
          <tr>
            <td>401</td>
            <td>&rarr;</td>
            <td><span class="color-circle color-green d-inline-block rounded-circle" style="background-color: #28a745; width: 20px; height: 20px;"></span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

          <div class="row g-4 mb-9 mt-2">
            <div class="col-12">
                <div class="card shadow-none border" data-component-card="data-component-card">
                    <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                        <h4 class="text-body mb-0">Mapa <?php echo Util::nombreDelProyecto(); ?></h4>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-toggle="modal" data-target="#exampleModalCenter">
                            <img src="assets/images/geoloca.png" alt="Geolocalización" style="width: 30px; height: 30px; object-fit: contain;">
                            <span>Geolocalización</span>
                        </button>
                        </div>
                        <div class="col-md-12" style="position: static; overflow-x: auto;">
                        <div class="cuerpoMapa w-12">
                            <div class="santander munis">
                            <?php if (!is_null($mapa)) : ?>
                            <?php echo require_once $mapa; ?>
                            <?php endif ?>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
    </div>

    </main>
    <!-- MODAL GEOLOCALIZACIÓN -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

        <!-- Encabezado estilizado -->
        <div class="modal-header bg-primary justify-content-between align-items-center">
            <h5 class="modal-title text-white m-0 w-100 text-center" id="exampleModalCenterTitle">
            Geolocalización
            </h5>
            <button type="button" class="close btn-close-white text-white position-absolute end-0 me-3" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Cuerpo con mapa -->
        <div class="modal-body">
            <div id="map" style="height: 600px; width: 100%;"></div>
        </div>

        </div>
    </div>



    <!-- Google Maps JavaScript API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap"></script>
    <script>
        let map;
        let trafficLayer, transitLayer, bicycleLayer;

        function initMap() {
            if (typeof google !== 'undefined' && google.maps) {
                // Coordenadas iniciales
                const initialLocation = { lat:  6.255705, lng: -75.577576};

                // Crear el mapa
                map = new google.maps.Map(document.getElementById("map"), {
                    center: initialLocation,
                    zoom: 12,
                });

                // Agregar evento para capturar clic en el mapa
                map.addListener("click", (event) => {
                    const lat = event.latLng.lat();
                    const lng = event.latLng.lng();

                    // Mostrar las coordenadas en pantalla
                    document.getElementById("lat").innerText = lat.toFixed(6);
                    document.getElementById("lng").innerText = lng.toFixed(6);

                    // Agregar un marcador en el punto seleccionado
                    new google.maps.Marker({
                        position: event.latLng,
                        map: map,
                    });
                });

                // Inicializar las capas
                trafficLayer = new google.maps.TrafficLayer(); // Capa de tráfico
                transitLayer = new google.maps.TransitLayer(); // Capa de transporte público
                bicycleLayer = new google.maps.BicyclingLayer(); // Capa de bicicletas

                // Eventos para los checkboxes
                document.getElementById("trafficLayerToggle").addEventListener("change", (e) => {
                    if (e.target.checked) {
                        trafficLayer.setMap(map);
                    } else {
                        trafficLayer.setMap(null);
                    }
                });

                document.getElementById("transitLayerToggle").addEventListener("change", (e) => {
                    if (e.target.checked) {
                        transitLayer.setMap(map);
                    } else {
                        transitLayer.setMap(null);
                    }
                });

                document.getElementById("bicycleLayerToggle").addEventListener("change", (e) => {
                    if (e.target.checked) {
                        bicycleLayer.setMap(map);
                    } else {
                        bicycleLayer.setMap(null);
                    }
                });

                document.getElementById("terrainToggle").addEventListener("change", (e) => {
                    if (e.target.checked) {
                        map.setMapTypeId("terrain"); // Cambia el tipo de mapa a terreno
                    } else {
                        map.setMapTypeId("roadmap"); // Cambia el tipo de mapa a carreteras
                    }
                });
            } else {
                console.error('Google Maps API no está disponible.');
            }
        }

        // Inicializar el mapa cuando se abre el modal
        $('#exampleModalCenter').on('shown.bs.modal', function () {
            initMap();
        });
    </script>
</div>


        <!--  Script -->
        <?php if (isset($_GET["route_map"])): ?>
        <?php endif ?>
        <?php include 'admin/include/footer.php'; ?>
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

        <?php include 'admin/include/gerenic_script.php'; ?>

        <!-- Required Js -->
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>
</body>

</html>