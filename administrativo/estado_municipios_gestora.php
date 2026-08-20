<!-- ESTE ES EL MAPA DE LOS MUNICIPIOS DENTRO DEL MAPA PARA LA GESTORA SOCIAL SE DEBE MODIFICAR SEGUN LO REQUIERAN LOS DATOS DE GESTORA SOCIAL-->
<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
//Permisos
$view = SessionData::getPermission(11);
$create = SessionData::getPermission(12);
$edit = SessionData::getPermission(13);
//Validación
if (!$create) {
    require 'permiso_denegado.php';
}

include './admin/classes/Visitasg.php';
include './admin/classes/Departamento.php';
include './admin/classes/Proyectos.php';
include './admin/classes/Compromisos.php';

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
    return str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);
}

$arrVisitas = Visitasg::getAll(["tbl_municipio_id" => $_REQUEST["mun"]]);
$visitas = $arrVisitas["output"]["response"];

$arrCompromisos = Compromisos::getAll(["tbl_municipio_id" => $_REQUEST["mun"]]);
$compromisos = $arrCompromisos["output"]["response"];

$arrProyectos = Proyectos::getInversiontotal(["tbl_municipio_id" => $_REQUEST["mun"]]);
$proyectos = $arrProyectos["output"]["response"];


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de compromisos
$arrCom = Compromisos::getAll($_REQUEST);
$isvalid = $arrCom['output']['valid'];
$compromiso = $arrCom['output']['response'];

// Información de secretarias
$arr = Proyectos::getAllproyectosxsecre($_REQUEST);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$arrData = $arr;

$totalEjecutado = 0;

if (!empty($arrData)) {
    foreach ($arrData as $key => $value) {
        $totalEjecutado += is_null($value["porcentaje_ejecucion"]) ? 0 : doubleval($value["porcentaje_ejecucion"]);
    }

    $totalEjecutado = $totalEjecutado == 0 ? 0 : round($totalEjecutado / count($arrData), 2);
}


$datosSecre = Proyectos::getInversionBySecre($_REQUEST);
$isvalidSecre = $datosSecre['output']['valid'];
$arrSecre      = $datosSecre['output']['response'];

// =======================PROYECTOS========================
$arrTotal = Proyectos::getInversiontotal($_REQUEST);
$isvalid = $arrTotal['output']['valid'];
$arrTotal = $arrTotal['output']['response'];
$arrTotalData = $arrTotal;

$total_invertido = 0;

if (!empty($arrTotalData)) {
    foreach ($arrTotalData as $key => $value) {
        $total_invertido += is_null($value["SumaDevalor_proyecto"]) ? 0 : doubleval($value["SumaDevalor_proyecto"]);
    }

    $total_invertido = $total_invertido == 0 ? 0 : round($total_invertido / count($arrTotalData), 2);
}

?>

<body class="">
    <?php include 'admin/include/scriptsgober360.php'; ?>
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

    <div class="content">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mb-0 text-primary">Estado Municipios</h2>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Formulario de filtros -->
                        <form id="formusuarios" autocomplete="off">
                            <input type="hidden" name="op" id="op">
                            <input type="hidden" name="id" id="id">

                            <div class="row g-3 mb-4">
                                <!-- Departamento -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_departamento_id" name="tbl_departamento_id"
                                            onchange="DEPARTAMENTO.getMunicipios();">
                                            <?= $optionDep ?>
                                        </select>
                                        <label for="tbl_departamento_id">Departamento <span
                                                class="text-danger">*</span></label>
                                    </div>
                                </div>

                                <!-- Municipio -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"
                                            onchange="DEPARTAMENTO.getVeredasByMunicipioId(true);"></select>
                                        <label for="tbl_municipio_id">Municipio <span
                                                class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Actividades de la Primera Dama -->
                        <div class="mt-12">
                            <h3 class="text-center fw-bold mb-3">Actividades de Desarrollo social</h3>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered align-middle text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Ver Detalle</th>
                                            <th>Fecha</th>
                                            <th>Provincia</th>
                                            <th>Población Impactada</th>
                                            <th>Motivo de la Actividad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($visitas)) : ?>
                                        <?php foreach ($visitas as $item) : ?>
                                        <tr>
                                            <td>
                                                <form action="reporte_visitag.php" method="POST" target="_blank"
                                                    style="display:inline;">
                                                    <input type="hidden" name="reporte"
                                                        value="<?= htmlspecialchars($item['id']) ?>">
                                                    <button type="submit" class="btn btn-sm btn-primary"
                                                        title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td><?= htmlspecialchars($item["date"]) ?></td>
                                            <td><?= htmlspecialchars($item["municipio"]) ?></td>
                                            <td><?= number_format($item["poblacion"]) ?></td>
                                            <td><?= htmlspecialchars($item["desc_actividad"]) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else : ?>
                                        <tr>
                                            <td colspan="5">No se encontraron actividades registradas para este
                                                municipio.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- fin actividades -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_REQUEST["mun"]) && $_REQUEST["mun"] == '68001'): ?>
        <img src="assets/img/bucaramangaok.png" alt="" class="mapa">
        <?php endif; ?>
    </div>


    <!-- ============================================================== -->
    <!-- end Visitas Realizadas  -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- COMPROMISOS PACTADOS -->
    <!-- ============================================================== -->












    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/estado_municipios_gestora.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Morris.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>

    <!-- // Variables para mostrrar en los graficos -->
    <script>
    const TOTAL_EJECUTADO = <?= $totalEjecutado ?>;
    const TOTAL_POR_EJECUTAR = <?= 100 - $totalEjecutado ?>;
    const LABELS_SECRETARIA = <?php echo json_encode($arrSecre["labels"]) ?>;
    const DATA_SECRETARIA = <?php echo json_encode($arrSecre["data"]) ?>;

    // Agregamos la información al select
    const params = UTIL.getParamsFromUrlDepartamentoMunicipio();
    selectedMunicipio = params.mun;
    DEPARTAMENTO.getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio(UTIL.getDepartamentoPrincipal(), params.mun);


    $(function() {
        "use strict";
        // ============================================================== 
        // Gender Js
        // ============================================================== 
        Morris.Donut({
            element: 'gender_donut',
            data: [{
                    value: parseFloat(TOTAL_EJECUTADO),
                    label: 'Ejecutado'
                },
                {
                    value: parseFloat(TOTAL_POR_EJECUTAR),
                    label: 'Por Ejecutar'
                }
            ],
            labelColor: '#5969ff',
            colors: [
                '#5969ff',
                '#ff407b',
            ],
            formatter: function(x) {
                return x + "%"
            }
        });
        // ============================================================== 
        //  chart bar horizontal
        // ============================================================== 
        var ctx = document.getElementById("chartjs_bar_horizontal").getContext('2d');

        new Chart(ctx, {
            type: 'bar', // Tipo de gráfica
            data: {
                labels: LABELS_SECRETARIA, // Etiquetas
                datasets: [{
                    label: 'Total Invertido',
                    data: DATA_SECRETARIA, // Datos
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(201, 203, 207, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Cambia el eje para barras horizontales
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$ ' + context.raw.toLocaleString(); // Formato en moneda
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true, // Comienza en 0
                        ticks: {
                            callback: function(value) {
                                return '$ ' + value.toLocaleString(); // Formato en moneda
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 12,
                                family: 'Arial'
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
    <style>
    .table-responsive {
        display: flex;
        justify-content: center;
    }

    .table {
        width: auto;
        max-width: 90%;
        min-width: 1000px;
    }

    .table td:last-child {
        white-space: normal !important;
        word-wrap: break-word;
        max-width: 400px;
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //función que carga los municipios
            DEPARTAMENTO.getMunicipios();
        });
    </script>

</body>

</html>