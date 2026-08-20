<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
//Permisos
$view = SessionData::getPermission(14);
$create = SessionData::getPermission(15);
$edit = SessionData::getPermission(16);
//Validación
if (!$view) {
    require 'permiso_denegado.php';
}

include './admin/classes/Compromisos.php';
include './admin/classes/Departamento.php';


//Información de Vistas
$arr = Compromisos::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Registro Visitas';


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = "68";
foreach ($arrDep as $val) {
    $optionDep .= "<option value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

?>

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
    <script src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.0/js/select.bootstrap4.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <style>
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            max-width: 800px;
            margin: 1em auto;
        }

        #container {
            height: 400px;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>

    <div class="content">
        <div class="pcoded-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                    <div class="card-header d-flex justify-content-center align-items-center">
                        <h3 class="mb-0 text-center d-flex align-items-center">
                        <i style="color:black !important; font-size: 1.9rem !important;" class="uil uil-presentation-line me-2 fs-4 text-primary"></i>
                         Gráfico de Compromisos
                        </h3>
                    </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h4>Total de compromisos adquiridos por la Gobernación a través de sus secretarías:
                                    TOTAL = 143</h4>
                                <figure class="highcharts-figure">
                                    <p class="highcharts-description">
                                        Secretarias y/o Entidades con compromisos
                                    </p>
                                    <div id="container"></div>
                                </figure>
                            </div>
                            <center>
                                <a><button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#exampleModalCenter"> Ver Compromisos</button></a>
                            </center>
                            <hr>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <figure class="highcharts-figure">
                                    <p class="highcharts-description">
                                        Secretarias y/o Entidades con compromisos en estado sin cumplir
                                    </p>
                                    <div id="container1"></div>
                                </figure>
                            </div>
                            <center>
                                <a><button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#exampleModalCenter1"> Ver Compromisos</button></a>
                            </center>
                            <hr>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <figure class="highcharts-figure">
                                    <p class="highcharts-description">
                                        Secretarias y/o Entidades con compromisos en estado En Trámite
                                    </p>
                                    <div id="container2"></div>
                                </figure>
                            </div>
                            <center>
                                <a><button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#exampleModalCenter2"> Ver Compromisos</button></a>
                            </center>
                            <hr>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <figure class="highcharts-figure">
                                    <p class="highcharts-description">
                                        Secretarias y/o Entidades con compromisos en estado Cumplido
                                    </p>
                                    <div id="container3"></div>
                                </figure>
                            </div>
                            <center>
                                <a><button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#exampleModalCenter3"> Ver Compromisos</button></a>
                            </center>
                        </div>
                    <!-- Modal -->
                    <!-- // ===================Información modal de total compromisos================= -->
                    <?php
                    $arr = Compromisos::getAll(null);
                    $isvalid = $arr['output']['valid'];
                    $arr = $arr['output']['response'];
                    ?>
                    <div class="modal fade bd-example-modal-lg" id="exampleModalCenter" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Cantidad de visitas a municipios
                                    </h5>
                                    <button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fecha</th>
                                                <th scope="col">Municipio</th>
                                                <th scope="col">Compromiso</th>
                                                <th scope="col">Secretaria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = count($arr);
                                            if ($isvalid) {
                                                for ($i = 0; $i < $c; $i++) { ?>
                                            <tr>
                                                <td> <?php echo $arr[$i]['date']; ?></td>
                                                <td> <?php echo $arr[$i]['municipio']; ?></td>
                                                <td> <?php echo $arr[$i]['compromisos']; ?></td>
                                                <td> <?php echo $arr[$i]['secretaria']; ?></td>
                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                  </div>  
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal total de compromisos sin cumplir -->

                    <!-- // ===================Información modal de total compromisos================= -->
                    <?php
                    $arr = Compromisos::getAllsinc(null);
                    $isvalid = $arr['output']['valid'];
                    $arr = $arr['output']['response'];
                    ?>
                    <div class="modal fade bd-example-modal-lg" id="exampleModalCenter1" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Detalle Compromisos sin Cumplir
                                    </h5>
                                    <button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fecha</th>
                                                <th scope="col">Municipio</th>
                                                <th scope="col">Compromiso</th>
                                                <th scope="col">Secretaria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = count($arr);
                                            if ($isvalid) {
                                                for ($i = 0; $i < $c; $i++) { ?>
                                            <tr>

                                                <td> <?php echo $arr[$i]['date']; ?></td>
                                                <td> <?php echo $arr[$i]['municipio']; ?></td>
                                                <td> <?php echo $arr[$i]['compromisos']; ?></td>
                                                <td> <?php echo $arr[$i]['secretaria']; ?></td>

                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal visitas realizadas fin -->

                    <!-- // ===================Información modal de total compromisos en tramite================= -->
                    <?php
                    $arr = Compromisos::getAlltram(null);
                    $isvalid = $arr['output']['valid'];
                    $arr = $arr['output']['response'];
                    ?>
                    <div class="modal fade bd-example-modal-lg" id="exampleModalCenter2" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Detalle Compromisos en Trámite
                                    </h5>
                                    <button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fecha</th>
                                                <th scope="col">Municipio</th>
                                                <th scope="col">Compromiso</th>
                                                <th scope="col">Secretaria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = count($arr);
                                            if ($isvalid) {
                                                for ($i = 0; $i < $c; $i++) { ?>
                                            <tr>

                                                <td> <?php echo $arr[$i]['date']; ?></td>
                                                <td> <?php echo $arr[$i]['municipio']; ?></td>
                                                <td> <?php echo $arr[$i]['compromisos']; ?></td>
                                                <td> <?php echo $arr[$i]['secretaria']; ?></td>

                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                  </div>  
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal visitas realizadas fin -->

                    <!-- // ===================Información modal de total compromisos Cumplidos================= -->
                    <?php
                    $arr = Compromisos::getAllcum(null);
                    $isvalid = $arr['output']['valid'];
                    $arr = $arr['output']['response'];
                    ?>
                    <div class="modal fade bd-example-modal-lg" id="exampleModalCenter3" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Detalle Compromisos Cumplidos
                                    </h5>
                                    <button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Fecha</th>
                                                <th scope="col">Municipio</th>
                                                <th scope="col">Compromiso</th>
                                                <th scope="col">Secretaria</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $c = count($arr);
                                            if ($isvalid) {
                                                for ($i = 0; $i < $c; $i++) { ?>
                                            <tr>

                                                <td> <?php echo $arr[$i]['date']; ?></td>
                                                <td> <?php echo $arr[$i]['municipio']; ?></td>
                                                <td> <?php echo $arr[$i]['compromisos']; ?></td>
                                                <td> <?php echo $arr[$i]['secretaria']; ?></td>

                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                  </div>  
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal visitas realizadas fin -->

                    <!-- ============================================================== -->
                    <!-- footer -->
                    <!-- ============================================================== -->
                    <?php include 'admin/include/gerenic_script.php'; ?>
                    <script type="text/javascript" src="admin/js/departamento.js"></script>
                    <script type="text/javascript" src="admin/js/compromisos.js"></script>
                    <!-- Incluir la biblioteca de ApexCharts -->
                    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

                    <!-- Contenedor del gráfico -->
                    <!-- <div id="containerProvincias"></div> -->

                    <script>
                        // Función para crear el gráfico de ApexCharts
                        function createChart(container, title, seriesData) {
                            const options = {
                                chart: {
                                    height: 350,
                                    type: 'bar',
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded',
                                    },
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: (val) => val.toFixed(1), // Formato numérico legible
                                },
                                colors: ['#0e9e4a', '#1abc9c', '#e74c3c', '#3498db', '#9b59b6'],
                                stroke: {
                                    show: true,
                                    width: 2,
                                    colors: ['transparent'],
                                },
                                series: [{
                                    name: title,
                                    data: seriesData.map(item => item.y),
                                }],
                                xaxis: {
                                    categories: seriesData.map(item => item.name),
                                },
                                yaxis: {
                                    title: {
                                        text: 'Total de Compromisos',
                                    },
                                },
                                fill: {
                                    opacity: 1,
                                },
                                tooltip: {
                                    y: {
                                        formatter: (val) => val.toLocaleString(), // Formato numérico legible
                                    },
                                },
                            };
                            const chart = new ApexCharts(document.querySelector(`#${container}`), options);
                            chart.render();
                        }
                        // Simulación de obtención de datos, podrías reemplazar esto con fetch() si obtienes datos de un API
                        function fetchData() {
                            // Datos simulados para el ejemplo
                            const seriesData = [{
                                    name: 'Ambiental',
                                    y: 0
                                },
                                {
                                    name: 'CAS',
                                    y: 0
                                },
                                {
                                    name: 'Competividad',
                                    y: 0
                                },
                                {
                                    name: 'Cultura y Turismo',
                                    y: 0
                                },
                                {
                                    name: 'Desarrollo Social',
                                    y: 0
                                },
                                {
                                    name: 'Educación',
                                    y: 0
                                },
                                {
                                    name: 'Esant',
                                    y: 0
                                },
                                {
                                    name: 'Gestión del Riesgo',
                                    y: 0
                                },
                                {
                                    name: 'InderSantader',
                                    y: 0
                                },
                                {
                                    name: 'Infraestructura',
                                    y: 0
                                },
                                {
                                    name: 'Interior',
                                    y: 0
                                },
                                {
                                    name: 'Mujer y Genero',
                                    y: 0
                                },
                                {
                                    name: 'Oficina Juridica',
                                    y: 0
                                },
                                {
                                    name: 'Privada',
                                    y: 0
                                },
                                {
                                    name: 'Salud',
                                    y: 0
                                },
                            ];
                            return seriesData;
                        }
                        // Función para obtener y mostrar los datos del gráfico
                        function getCompromisosCumplidosPorSecretaria() {
                            const data = fetchData();
                            createChart("container3", "Compromisos pactados por Secretaria en estado Cumplido", data);
                        }
                        // Llama a la función al cargar la página
                        
                        document.addEventListener('DOMContentLoaded', function() {
                            getCompromisosCumplidosPorSecretaria();
                        });
                    </script>

                    <script>
                        // Función para crear el gráfico de ApexCharts
                        function createChart(container, title, seriesData) {
                            const options = {
                                chart: {
                                    height: 350,
                                    type: 'bar',
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded',
                                    },
                                },
                                dataLabels: {
                                    enabled: false,
                                },
                                colors: ['#0e9e4a', '#1abc9c', '#e74c3c'],
                                stroke: {
                                    show: true,
                                    width: 2,
                                    colors: ['transparent'],
                                },
                                series: [{
                                    name: title,
                                    data: seriesData.map(item => item.y),
                                }],
                                xaxis: {
                                    categories: seriesData.map(item => item.name),
                                },
                                yaxis: {
                                    title: {
                                        text: 'Total de visitas',
                                    },
                                },
                                fill: {
                                    opacity: 1,
                                },
                                tooltip: {
                                    y: {
                                        formatter: (val) => val.toLocaleString(), // Formato numérico legible
                                    },
                                },
                            };
                            const chart = new ApexCharts(document.querySelector(`#${container}`), options);
                            chart.render();
                        }
                        // Simulación de obtención de datos, podrías reemplazar esto con fetch() si obtienes datos de un API
                        function fetchData() {
                            // Datos simulados para el ejemplo
                            const seriesData = [{
                                    name: 'Ambiental',
                                    y: 5
                                },
                                {
                                    name: 'CAS',
                                    y: 3
                                },
                                {
                                    name: 'Competividad',
                                    y: 8
                                },
                                {
                                    name: 'Cultura y Turismo',
                                    y: 2
                                },
                                {
                                    name: 'Desarrollo Social',
                                    y: 7
                                },
                                {
                                    name: 'Educación',
                                    y: 4
                                },
                                {
                                    name: 'Esant',
                                    y: 6
                                },
                                {
                                    name: 'Gestión del Riesgo',
                                    y: 1
                                },
                                {
                                    name: 'InderSantader',
                                    y: 9
                                },
                                {
                                    name: 'Infraestructura',
                                    y: 2
                                },
                                {
                                    name: 'Interior',
                                    y: 10
                                },
                                {
                                    name: 'Mujer y Genero',
                                    y: 3
                                },
                                {
                                    name: 'Oficina Juridica',
                                    y: 4
                                },
                                {
                                    name: 'Privada',
                                    y: 5
                                },
                                {
                                    name: 'Salud',
                                    y: 8
                                },
                            ];
                            return seriesData;
                        }
                        // Función para obtener y mostrar los datos del gráfico
                        function getTotalVisitasPorProvincia() {
                            const data = fetchData();
                            createChart("containerProvincias", "Visitas realizadas a Provincias", data);
                        }
                        // Llama a la función al cargar la página
                        document.addEventListener('DOMContentLoaded', function() {
                            getTotalVisitasPorProvincia();
                        });
                    </script>

                    <script>
                        // Función para crear el gráfico de ApexCharts
                        function createChart(container, title, seriesData) {
                            const options = {
                                chart: {
                                    height: 350,
                                    type: 'bar',
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded',
                                    },
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: (val) => val.toFixed(1), // Formato numérico legible
                                },
                                colors: ['#e74c3c', '#f39c12', '#8e44ad', '#3498db', '#1abc9c'],
                                stroke: {
                                    show: true,
                                    width: 2,
                                    colors: ['transparent'],
                                },
                                series: [{
                                    name: title,
                                    data: seriesData.map(item => item.y),
                                }],
                                xaxis: {
                                    categories: seriesData.map(item => item.name),
                                },
                                yaxis: {
                                    title: {
                                        text: 'Total de Compromisos',
                                    },
                                },
                                fill: {
                                    opacity: 1,
                                },
                                tooltip: {
                                    y: {
                                        formatter: (val) => val.toLocaleString(), // Formato numérico legible
                                    },
                                },
                            };
                            const chart = new ApexCharts(document.querySelector(`#${container}`), options);
                            chart.render();
                        }
                        // Simulación de obtención de datos, puedes reemplazar esto con fetch() si obtienes datos de un API
                        function fetchData() {
                            // Datos simulados para el ejemplo
                            const seriesData = [{
                                    name: 'Ambiental',
                                    y: 1
                                },
                                {
                                    name: 'CAS',
                                    y: 2
                                },
                                {
                                    name: 'Competividad',
                                    y: 3
                                },
                                {
                                    name: 'Cultura y Turismo',
                                    y: 11
                                },
                                {
                                    name: 'Desarrollo Social',
                                    y: 9
                                },
                                {
                                    name: 'Educación',
                                    y: 26
                                },
                                {
                                    name: 'Esant',
                                    y: 12
                                },
                                {
                                    name: 'Gestión del Riesgo',
                                    y: 2
                                },
                                {
                                    name: 'InderSantader',
                                    y: 26
                                },
                                {
                                    name: 'Infraestructura',
                                    y: 34
                                },
                                {
                                    name: 'Interior',
                                    y: 4
                                },
                                {
                                    name: 'Mujer y Genero',
                                    y: 9
                                },
                                {
                                    name: 'Oficina Juridica',
                                    y: 1
                                },
                                {
                                    name: 'Privada',
                                    y: 1
                                },
                                {
                                    name: 'Salud',
                                    y: 6
                                },
                            ];
                            return seriesData;
                        }
                        // Función para obtener y mostrar los datos del gráfico
                        function getCompromisosSinCumplirPorSecretaria() {
                            const data = fetchData();
                            createChart("container1", "Compromisos pactados por Secretaria en estado Sin Cumplir",
                            data);
                        }
                        // Llama a la función al cargar la página
                        document.addEventListener('DOMContentLoaded', function() {
                            getCompromisosSinCumplirPorSecretaria();
                        });
                    </script>

                    <script>
                        // Función para crear el gráfico de ApexCharts
                        function createChart(container, title, seriesData) {
                            const options = {
                                chart: {
                                    height: 350,
                                    type: 'bar',
                                },
                                plotOptions: {
                                    bar: {
                                        horizontal: false,
                                        columnWidth: '55%',
                                        endingShape: 'rounded',
                                    },
                                },
                                dataLabels: {
                                    enabled: true,
                                    formatter: (val) => val.toFixed(1), // Formato numérico legible
                                },
                                colors: ['#0e9e4a', '#1abc9c', '#e74c3c', '#3498db', '#9b59b6'],
                                stroke: {
                                    show: true,
                                    width: 2,
                                    colors: ['transparent'],
                                },
                                series: [{
                                    name: title,
                                    data: seriesData.map(item => item.y),
                                }],
                                xaxis: {
                                    categories: seriesData.map(item => item.name),
                                },
                                yaxis: {
                                    title: {
                                        text: 'Total de Compromisos',
                                    },
                                },
                                fill: {
                                    opacity: 1,
                                },
                                tooltip: {
                                    y: {
                                        formatter: (val) => val.toLocaleString(), // Formato numérico legible
                                    },
                                },
                            };
                            const chart = new ApexCharts(document.querySelector(`#${container}`), options);
                            chart.render();
                        }
                        // Simulación de obtención de datos, podrías reemplazar esto con fetch() si obtienes datos de un API
                        function fetchData() {
                            // Datos simulados para el ejemplo
                            const seriesData = [{
                                    name: 'Ambiental',
                                    y: 1
                                },
                                {
                                    name: 'CAS',
                                    y: 2
                                },
                                {
                                    name: 'Competividad',
                                    y: 3
                                },
                                {
                                    name: 'Cultura y Turismo',
                                    y: 11
                                },
                                {
                                    name: 'Desarrollo Social',
                                    y: 9
                                },
                                {
                                    name: 'Educación',
                                    y: 26
                                },
                                {
                                    name: 'Esant',
                                    y: 12
                                },
                                {
                                    name: 'Gestión del Riesgo',
                                    y: 2
                                },
                                {
                                    name: 'InderSantader',
                                    y: 26
                                },
                                {
                                    name: 'Infraestructura',
                                    y: 34
                                },
                                {
                                    name: 'Interior',
                                    y: 4
                                },
                                {
                                    name: 'Mujer y Genero',
                                    y: 9
                                },
                                {
                                    name: 'Oficina Juridica',
                                    y: 1
                                },
                                {
                                    name: 'Privada',
                                    y: 1
                                },
                                {
                                    name: 'Salud',
                                    y: 6
                                },
                            ];
                            return seriesData;
                        }
                        // Función para obtener y mostrar los datos del gráfico
                        function getTotalCompromisosPorSecretaria() {
                            const data = fetchData();
                            createChart("container", "Total de Compromisos pactados por Secretaria", data);
                        }
                        // Llama a la función al cargar la página
                        document.addEventListener('DOMContentLoaded', function() {
                            getTotalCompromisosPorSecretaria();
                        });
                    </script>

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
    <script src="../assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <script src="../assets/libs/js/main-js.js"></script>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>