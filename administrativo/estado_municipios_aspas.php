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


include './admin/classes/VisitasgAspas.php';
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

;
// Validar los parámetros "mun" y "dep"
if (isset($_REQUEST['mun']) && !empty(trim($_REQUEST['mun'])) ) {

    $municipio = $_REQUEST["mun"];
    $arrVisitas = VisitasgAspas::getAll(["tbl_municipio_id" => $municipio]);
    $visitas = $arrVisitas["output"]["response"];
    
    $arrVisitasbuc = Visitasbuc::getAll(["tbl_municipio_id" => $municipio]);
    $visitasbuc = $arrVisitasbuc["output"]["response"];
    
    $arrCompromisos = Compromisos::getAll(["tbl_municipio_id" => $municipio]);
    $compromisos = $arrCompromisos["output"]["response"];
    
    $arrProyectos = Proyectos::getInversiontotal(["tbl_municipio_id" => $municipio]);
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
}else{
    ?>
<script type='text/javascript'>
alert('Información enviada no es correcta');
window.location =
    'gestora_social.php?depto_id=<?php echo Util::getIdentificadorDepartamentoPrincipal(); ?>';
</script>
<?php
}

?>


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
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div id="divInformacionGeneral" class="row">
                <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="card">

                        <div class="card-header">
                            <h5>Estado Municipios</h5>
                            <div class="card-header-right">
                                <div class="btn-group card-option">
                                   
                                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                        <li class="dropdown-item full-card"><a href="#!"><span><i
                                                        class="feather icon-maximize"></i> maximize</span><span
                                                    style="display:none"><i class="feather icon-minimize"></i>
                                                    Restore</span></a></li>
                                        <li class="dropdown-item minimize-card"><a href="#!"><span><i
                                                        class="feather icon-minus"></i> collapse</span><span
                                                    style="display:none"><i class="feather icon-plus"></i>
                                                    expand</span></a></li>
                                        <li class="dropdown-item reload-card"><a href="#!"><i
                                                    class="feather icon-refresh-cw"></i> reload</a></li>
                                        <li class="dropdown-item close-card"><a href="#!"><i
                                                    class="feather icon-trash"></i> remove</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <form id="formusuarios" role="form" autocomplete="false">
                                        <input type="hidden" name="op" id="op" />
                                        <input type="hidden" name="id" id="id" />
                                        <div class="card-body">
                                            <!-- <div class="form-group">
                                                <label class="bmd-label-floating">Departamento</label>
                                                <select class="form-control" style="width: 100%;" disabled id="tbl_departamento_id"
                                                    name="tbl_departamento_id"><?php echo $optionDep; ?></select>
                                            </div>
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Municipio</label>
                                                <select onchange="ESTADO_MUN_GESTORA.updateUrlMunicipio(this)"  class="form-control" style="width: 100%;" 
                                                    id="tbl_municipio_id" name="tbl_municipio_id"> </select>
                                            </div> -->
                                    </form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-4">
                                                <select class="form-control" style="width: 100%;" disabled id="tbl_departamento_id"
                                                                    name="tbl_departamento_id"><?php echo $optionDep; ?></select>
                                                <label for="tbl_departamento_id">Departamento</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-4">
                                                <select onchange="ESTADO_MUN_GESTORA.updateUrlMunicipio(this)"  class="form-control" style="width: 100%;" 
                                                                    id="tbl_municipio_id" name="tbl_municipio_id"> </select>
                                                <label for="tbl_municipio_id">Municipio</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             
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
                                <!-- fin mapa bucaramanga y datos  -->
                                <!-- ============================================================== -->
                              
                                <!-- ============================================================== -->
                                <!-- COMPROMISOS PACTADOS -->
                                <!-- ============================================================== -->
                                                             




                                    <!-- Inversión Detallada -->
                                    <div class="row">
                                        <div class="col-12">                                          
                                            <div class="section-block">
                                                <h3 class="section-title text-center" style="font-size: 16px">Actividades Primera Dama
                                                </h3>
                                            </div>
                                            
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover m-4">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Ver Detallado</th>                                                          
                                                                <th scope="col">Fecha</th>
                                                                <th scope="col">Provincia</th>
                                                                <th scope="col">Poblacion Impactada</th>
                                                                <th scope="col">Motivo Actividad</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($isvalid) : ?>
                                                            <?php foreach ($visitas as $item) : ?>
                                                            <tr>
                                                                <td>
                                                                <form action="reporte_visitag_aspas.php" method="POST"
                                                                        target="_blank" style="display:inline;">
                                                                        <input type="hidden" id="reporte" name="reporte"
                                                                            value="<?= htmlspecialchars($item['id']); ?>">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-primary" title="Ver">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                                                                                           </form>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($item["date"]); ?></td>
                                                                    <td><?php echo htmlspecialchars($item["provincia"]); ?></td>
                                                                    <td><?php echo htmlspecialchars($item["poblacion"]); ?></td>
                                                                    <td><?php echo htmlspecialchars($item["desc_actividad"]); ?></td>
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
        DEPARTAMENTO.getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio(UTIL.getDepartamentoPrincipal(),params.mun );

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


</body>

</html>