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

include './admin/classes/Visitas.php';
include './admin/classes/Visitasbuc.php';
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

$arrVisitas = Visitas::getAll(["tbl_municipio_id" => $_REQUEST["mun"]]);
$visitas = $arrVisitas["output"]["response"];

$arrVisitasbuc = Visitasbuc::getAll(["tbl_municipio_id" => $_REQUEST["mun"]]);
$visitasbuc = $arrVisitasbuc["output"]["response"];

$arrCompromisos = Compromisos::getAll(["tbl_municipio_id" => $_REQUEST["mun"]]);
$compromisos = $arrCompromisos["output"]["response"];

$arrProyectos = Proyectos::getInversiontotal(["tbl_municipio_id" => $_REQUEST["mun"]]);
$proyectos = $arrProyectos["output"]["response"];


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$depSeleccionado = $_REQUEST["dep"] ?? Util::getDepartamentoPrincipal();
$optionDep = "";
foreach ($arrDep as $val) {
    $selected = ($val["codigo_departamento"] == $depSeleccionado) ? "selected" : "";
    $optionDep .= "<option $selected value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
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

// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
}
?>
<?php include 'admin/include/scriptsgober360.php'; ?>

<body>

    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="content">
        <div class="pcoded-content">

            <div id="divEstadoMunicipio" class="row">
                <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="card">

                        <div class="card-header">
                            <?php if (!empty($logo)): ?>
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                                class="img-fluid img-thumbnail me-2">
                            <?php endif; ?>
                            <h5 style="font-size: 20px; display: inline-block; vertical-align: middle;">
                                <i style="color:black !important;font-size: 1.9rem !important;"
                                    class="uil uil-location-pin-alt me-2 fs-4 text-primary"></i>
                                Estado Municipio - <?php echo htmlspecialchars($nombreProyecto); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <form id="formusuarios" role="form" autocomplete="false">
                                        <input type="hidden" name="op" id="op" />
                                        <input type="hidden" name="id" id="id" />
                                        <div class="card-body">
                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="tbl_departamento_id"
                                                    name="tbl_departamento_id" disabled>
                                                    <?php echo $optionDep; ?>
                                                </select>
                                                <label for="tbl_departamento_id">Departamento<span
                                                        class="text-danger">*</span></label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="tbl_municipio_id"
                                                    name="tbl_municipio_id"
                                                    onchange="ESTADO_MUN_GOBER.updateUrlMunicipio(this)">
                                                </select>
                                                <label for="tbl_municipio_id">Municipio<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                    </form>
                                </div>
                                <div class="col-12">
                                    <div class="section-block">
                                        <h3 class="section-title" style="font-size: 16px; text-align: center">Visitas
                                            Realizadas</h3>
                                    </div>
                                    <div class="card">
                                        <div class="table-responsive">
                                            <table class="table table-striped m-4">
                                                <thead class="thead-dark">
                                                    <tr>

                                                        <th>Imagen</th>
                                                        <th>Fecha</th>
                                                        <th>Municipio</th>
                                                        <th>Motivo Visita</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($visitas as $item) :
                                                    $imgBasePath = "assets/img/admin/";
                                                    $img = !empty($item["img"]) ? $imgBasePath . htmlspecialchars($item["img"]) : 'dist/img/logorelsinf.png';
                                            ?>
                                                    <tr>
                                                        <td class="text-primary">
                                                            <img width="60" height="60" src="<?php echo $img; ?>"
                                                                alt="Imagen líder" data-toggle="modal"
                                                                data-target="#imageModal<?php echo $item['id']; ?>"
                                                                style="cursor: pointer;">

                                                            <!-- Modal -->
                                                            <div class="modal fade"
                                                                id="imageModal<?php echo $item['id']; ?>" tabindex="-1"
                                                                role="dialog"
                                                                aria-labelledby="imageModalLabel<?php echo $item['id']; ?>"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="imageModalLabel<?php echo $item['id']; ?>">
                                                                                Imagen de Visita del municipio de
                                                                                <?php echo htmlspecialchars($item['municipio']); ?>
                                                                            </h5>
                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body text-center">
                                                                            <img src="<?php echo $img; ?>"
                                                                                alt="Imagen líder" class="img-fluid">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($item['date']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['municipio']); ?></td>
                                                        <td><?php echo htmlspecialchars($item['compromisos']); ?></td>
                                                    </tr>

                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- ============================================================== -->
                                <!-- end Visitas Realizadas  -->
                                <!-- ============================================================== -->
                                <!-- ============================================================== -->
                                <!-- mapa bucaramanga y datos  -->
                                <!-- ============================================================== -->
                                <div class="section-block">
                                    <?php if (isset($_REQUEST["mun"]) && $_REQUEST["mun"] == '68001'): ?>
                                    <h3 class="section-title">Mapa Bucaramanga</h3>
                                    <?php endif; ?>
                                </div>

                                <div class="container">
                                    <?php if (isset($_REQUEST["mun"]) && $_REQUEST["mun"] == '68001'): ?>
                                    <img src="assets/img/bucaramangaok.png" alt="" class="mapa">
                                    <?php endif; ?>
                                </div>

                                <!-- ============================================================== -->
                                <!-- COMPROMISOS PACTADOS -->
                                <!-- ============================================================== -->
                                <div class="container-fluid">
                                    <!-- Título Principal -->
                                    <div class="row">
                                        <div class="col-12">

                                            <div class=" section-block">
                                                <h3 class="section-title text-center" style="font-size:16px">Compromisos
                                                    Pactados en el
                                                    Municipio</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Tabla de Compromisos -->
                                    <div class=" row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Fecha</th>
                                                                <th>Compromiso</th>
                                                                <th>Estado</th>
                                                                <th>Respuesta</th>
                                                                <th>Secretaría</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($compromisos)) : ?>
                                                                <?php foreach ($compromisos as $value) : ?>
                                                                <tr
                                                                    style="background-color: <?= isset($colorFila[$value['cumplimiento']]) ? htmlspecialchars($colorFila[$value['cumplimiento']]) : '' ?>">
                                                                    <td><?= htmlspecialchars($value["date"]) ?>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($value["compromisos"]) ?>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($value["cumplimiento"]) ?>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($value["respuesta"]) ?>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($value["secretaria"]) ?>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            <?php else : ?>
                                                                <tr>
                                                                    <td colspan="5" class="text-center">Sin resultados</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Estado de Proyectos e Inversión por Secretarías -->
                                    <div class="row">
                                        <!-- Primera gráfica -->
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <div class="card h-100">
                                                <h5 style="color:black; font-size:16px" class="card-header text-center">
                                                    Estado
                                                    de
                                                    proyectos en general</h5>
                                                <div class=" card-body">
                                                    <div id="gender_donut" style="height: 230px;"></div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-between">
                                                    <div>
                                                        <h2 class="mb-0" style="font-size:16px">
                                                            <?= htmlspecialchars($totalEjecutado) ?>%</h2>
                                                        <p>Ejecutado</p>
                                                    </div>
                                                    <div>
                                                        <h2 class=" mb-0" style="font-size:16px">
                                                            <?= htmlspecialchars(100 - $totalEjecutado) ?>%
                                                        </h2>
                                                        <p>Faltante</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Segunda gráfica -->
                                        <div class=" col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <div class="card h-100">
                                                <h5 style="color:black; font-size:16px" class="card-header text-center">
                                                    Inversión por
                                                    Secretarías</h5>
                                                <div class=" card-body">
                                                    <canvas id="chartjs_bar_horizontal"></canvas>
                                                </div>
                                                <div class="card-footer">
                                                    <!-- Mantén este footer en blanco -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Inversión Detallada -->
                                    <div class="row">
                                        <div class="col-12">
                                            <hr>
                                            <h3 class="section-title text-center" style="font-size:16px">
                                                Valor total inversión en el municipio en general:
                                                <?= htmlspecialchars('$ ' . number_format($total_invertido, 2, ',', '.')) ?>
                                            </h3>
                                            <hr>
                                            <div class="section-block">
                                                <h3 class="section-title text-center" style="font-size:16px">Inversión
                                                    detallada
                                                    por
                                                    Secretaría
                                                </h3>
                                            </div>
                                            <div class="card">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover m-4">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Ver Detallado</th>
                                                                <th>Secretaría</th>
                                                                <th>Valor Proyecto</th>
                                                                <th>Nombre Proyecto</th>
                                                                <th>Porcentaje Ejecución</th>
                                                                <th>Fecha Entrega</th>
                                                                <th>Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($isvalid) : ?>
                                                            <?php foreach ($arr as $item) : ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="reporte_secretarias.php?reporte=<?= htmlspecialchars($item['id']) ?>"
                                                                        target="_blank" title="Ver">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                                <td><?= htmlspecialchars($item['secretaria']) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars('$ ' . number_format($item['valor_proyecto'], 2, ',', '.')) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($item['proyecto']) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($item['porcentaje_ejecucion']) ?>%
                                                                </td>
                                                                <td><?= htmlspecialchars($item['fecha_entrega']) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($item['estado']) ?>
                                                                </td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- [ sample-page ] end -->
                </div>
                <!-- [ Main Content ] end -->
            </div>
        </div>
        <!-- [ Main Content ] end -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

        <!-- Required Js -->
        <?php include 'admin/include/gerenic_script.php'; ?>
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>

        <script type="text/javascript" src="admin/js/departamento.js"></script>
        <script type="text/javascript" src="admin/js/estado_municipios_gobernador.js"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Morris.js -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>

         <!-- // Variables para mostrrar en los graficos -->
        <script>
        const TOTAL_EJECUTADO = <?= json_encode($totalEjecutado) ?>;
        const TOTAL_POR_EJECUTAR = <?= json_encode(100 - $totalEjecutado) ?>;
        const LABELS_SECRETARIA = <?= json_encode($arrSecre["labels"]) ?>;
        const DATA_SECRETARIA = <?= json_encode($arrSecre["data"]) ?>;
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
                                    return '$ ' + context.raw
                                        .toLocaleString(); // Formato en moneda
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true, // Comienza en 0
                            ticks: {
                                callback: function(value) {
                                    return '$ ' + value
                                        .toLocaleString(); // Formato en moneda
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

        // Agregamos la información al select
        const params = UTIL.getParamsFromUrlDepartamentoMunicipio();
        selectedMunicipio = params.mun;
        DEPARTAMENTO.getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio(UTIL.getDepartamentoPrincipal(),params.mun );

        function loadContentidoMapa() {
            const currentUrl = new URL(window.location.href);
            $.ajax({
                url: currentUrl.toString(),
                type: "GET",
                success: function(response) {
                    const updatedContent = $(response).find("#divEstadoMunicipio").html();
                    $("#divEstadoMunicipio").html(updatedContent);
                },
                error: function(error) {
                    console.error("Error al cargar contenido:", error);
                }
            });
        }
        </script>

</body>

</html>