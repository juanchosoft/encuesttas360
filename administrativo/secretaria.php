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
include './admin/classes/Colombia.php';
include './admin/classes/Ciudad.php';
require './admin/classes/Departamento.php';
include './admin/db/coloress.php';
include './admin/classes/Secretarias.php';
include './admin/classes/AccionSecretaria.php';
include './admin/classes/SecretariasMunicipio.php';

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

$secretaria = $_REQUEST['secretaria'] ??  Util::getSecretariaPrincipal();
$responseAccionSecretarias = [];
$isAccionSecretaria = false;
if (isset($secretaria) && !empty(trim($secretaria))) {
    $accionSecretaria = AccionSecretaria::getAll(array('id' => $secretaria));
    $isAccionSecretaria = $accionSecretaria['output']['valid'] ?? false;
    $responseAccionSecretarias = $accionSecretaria['output']['response'] ?? null;
} else { ?>
<script type='text/javascript'>
    alert('Información enviada no es correcta');
    window.location = 'dashboard.php';
</script>
<?php
}

// Informacion de los pilares
$arr = Secretarias::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$optionSecretarias = "";
foreach ($arr as $val) {
    $selected = ($val['id'] == $secretaria) ? "selected" : "";
    $optionSecretarias .= "<option value='" . $val['id'] . "' $selected>" . $val['secretaria'] . "</option>";
}

// Informacion del mapa y las secretarias
$arr = array('codigoMunicipio' => Util::getDepartamentoPrincipal(), 'secretariaId' => $secretaria);
$data = Colombia::getInformacionSecretariaColoresMapa($arr);
$santander =  $data['output']['response'];
$puntajes =  $data['output']['puntajes'];


// Informacion de los proyectos en ejecucion por seretaria Id
$responseTotalEjecucionSecretarias = Secretarias::getTotalEjecucionSecretaria($arr);
$dataTotalEjecucionSecretarias =  $responseTotalEjecucionSecretarias['output']['response'];
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<!-- DataTables -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<body class="">
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->

    <div class="content">
        <div class="row mb-4 mb-xl-6 mb-xxl-4 gy-3 justify-content-between">
                <div class="col-auto">
                    <h2 class="mb-0 text-body-emphasis">Resumen Secretarias</h2>
                </div>
                <h5 class="text-body-tertiary fw-semibold">
                            Resumen de ejecución por <strong>Secretarias</strong>
                        </h5>
                <div class="col-auto">
                </div>
                </div>
                <div class="col-sm-4 mb-4">
                    <div class="form-floating"> <!-- Cambiado de form-group a form-floating -->
                        <select class="form-select" id="secretariaId" name="secretariaId" onchange="updateUrlSecretaria(this)">
                            <?php echo $optionSecretarias; ?>
                        </select>
                        <label for="secretariaId">Secretaría<span class="text-danger mb-1">*</span></label>
                    </div>
                </div>
                <div class="row g-4 align-items-start">
                            <div class="col-lg-9">
                                <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="text-center mb-0 d-flex justify-content-center align-items-center gap-2">
                                        <i class="bi bi-bar-chart-line-fill text-primary fs-5"></i> Resumen Total Ejecución
                                    </h5>

                                    </div>
                                    <div class="card-body">
                                        <div class="tabs" id="DivTotalEjecucionSecretarias">
                                            <!-- <ul class="tab-list">
                                                <?php foreach($dataTotalEjecucionSecretarias as $index => $provinciaData): ?>
                                                <li class="tab <?= $index === 0 ? 'active' : '' ?>" data-tab="tab-<?= $index ?>">
                                                    <?= htmlspecialchars(str_replace('_', ' ', $provinciaData['provincia'])) ?>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul> -->
                                            <?php foreach($dataTotalEjecucionSecretarias as $index => $provinciaData): ?>
                                            <div class="tab-content <?= $index === 0 ? 'active' : '' ?>" id="tab-<?= $index ?>">
                                                <h4 class="mb-4 text-center">
                                                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                                   
                                                </h4>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm thead-dark">
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    <th>Concepto</th>
                                                                    <th>Valor</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <i class="bi bi-pie-chart-fill text-success"></i>
                                                                        <strong> Valor Proyecto Total</strong>
                                                                    </td>
                                                                    <td>$ <?= number_format($provinciaData['valor_proyecto_total'], 2, ',', '.') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <i class="bi bi-bank text-primary"></i>
                                                                        <strong> Valor Aporte Municipio Total</strong>
                                                                    </td>
                                                                    <td>$ <?= number_format($provinciaData['valor_municipio_total'], 2, ',', '.') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <i class="bi bi-flag text-warning"></i>
                                                                        <strong> Valor Aporte Nación</strong>
                                                                    </td>
                                                                    <td>$ <?= number_format($provinciaData['valor_nacion_total'], 2, ',', '.') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <i class="bi bi-building text-danger"></i>
                                                                        <strong> Valor Aporte Departamento</strong>
                                                                    </td>
                                                                    <td>$ <?= number_format($provinciaData['valor_departamento_total'], 2, ',', '.') ?></td>
                                                                </tr>
                                                            </tbody>

                                                        </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="table-responsive">
                                                        <table class="table table-bordered table-sm">
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    <th>Estado de Contrato</th>
                                                                    <th>Cantidad</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><i class="bi bi-pause-circle text-warning"></i> Suspendido</td>
                                                                    <td><?= $provinciaData['suspendido'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="bi bi-check2-circle text-success"></i> Terminado</td>
                                                                    <td><?= $provinciaData['terminado'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="bi bi-play-circle text-primary"></i> Ejecutado</td>
                                                                    <td><?= $provinciaData['ejecutado'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="bi bi-clipboard-data text-info"></i> En contratación</td>
                                                                    <td><?= $provinciaData['en_contratacion'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="bi bi-pencil-square text-secondary"></i> En formulación</td>
                                                                    <td><?= $provinciaData['en_formulacion'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="bi bi-box-arrow-in-down text-dark"></i> Entregado</td>
                                                                    <td><?= $provinciaData['entregado'] ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="bi bi-hourglass-split text-warning"></i> En ejecución</td>
                                                                    <td><?= $provinciaData['en_ejecucion'] ?></td>
                                                                </tr>
                                                            </tbody>

                                                        </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($provinciaData['detalle']) && is_array($provinciaData['detalle'])): ?>
                                                <div class="mt-4">
                                                    <h5>Detalle por Municipio</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered table-sm">
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    <th>Municipio</th>
                                                                    <th>Valor Proyecto</th>
                                                                    <th>Aporte Municipio</th>
                                                                    <th>Aporte Nación</th>
                                                                    <th>Aporte Departamento</th>
                                                                    <th>Suspendido</th>
                                                                    <th>Terminado</th>
                                                                    <th>Ejecutado</th>
                                                                    <th>En contratación</th>
                                                                    <th>En formulación</th>
                                                                    <th>Entregado</th>
                                                                    <th>En ejecución</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($provinciaData['detalle'] as $detalle): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($detalle['municipio']) ?></td>
                                                                    <td>$ <?= number_format($detalle['valor_proyecto'], 2, ',', '.') ?></td>
                                                                    <td>$ <?= number_format($detalle['valor_municipio'], 2, ',', '.') ?></td>
                                                                    <td>$ <?= number_format($detalle['valor_nacion'], 2, ',', '.') ?></td>
                                                                    <td>$ <?= number_format($detalle['valor_departamento'], 2, ',', '.') ?></td>
                                                                    <td><?= $detalle['suspendido'] ?></td>
                                                                    <td><?= $detalle['terminado'] ?></td>
                                                                    <td><?= $detalle['ejecutado'] ?></td>
                                                                    <td><?= $detalle['en_contratacion'] ?></td>
                                                                    <td><?= $detalle['en_formulacion'] ?></td>
                                                                    <td><?= $detalle['entregado'] ?></td>
                                                                    <td><?= $detalle['en_ejecucion'] ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="card shadow-sm border" id="divPuntajes" data-component-card="data-component-card">
                                    <!-- Encabezado del Card -->
                                    <div class="card-header p-4 border-bottom bg-body">
                                    <h4 class="mb-0 text-body d-flex align-items-center gap-2">
                                        <i class="bi bi-sliders2-vertical text-secondary fs-5"></i> Tabla de Valores de Referencia
                                    </h4>
                                    </div>

                                    <!-- Cuerpo del Card -->
                                    <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered align-middle text-center mb-0">
                                        <thead class="table-light">
                                            <tr>
                                            <th scope="col">Desde</th>
                                            <th scope="col">Hasta</th>
                                            <th scope="col">Color</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($puntajes) > 0): ?>
                                            <?php foreach ($puntajes as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['rango_desde'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?= htmlspecialchars($item['rango_hasta'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                <div style="width: 40px; height: 20px; margin: auto; border: 1px solid #ccc; background-color: <?= htmlspecialchars($item['color'], ENT_QUOTES, 'UTF-8'); ?>;"></div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="3">No hay valores para mostrar.</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                                </div>

                            <div class="row">
                                <!-- [ sample-page ] start -->
                                <div class="col-12">
                                    <div class="card shadow-none border" data-component-card="data-component-card">
                                        <div class="card-header p-4 border-bottom bg-body">
                                        <div class="row g-3 justify-content-between align-items-center">
                                            <div class="col-12 d-flex justify-content-between align-items-center">
                                            <h4 class="text-body mb-0">Mapa</h4>
                                            <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-toggle="modal" data-target="#modalGeocalizacion">
                                                <img src="assets/images/geoloca.png" alt="Geolocalización" style="width: 30px; height: 30px; object-fit: contain;">
                                                <span>Geolocalización</span>
                                            </button>
                                            </div>

                                            <div class="col-md-12" style="position: static; overflow-x: auto">
                                            <div id="contenido-mapa" class="cuerpoMapa w-12">
                                                <!-- SVG del mapa -->
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 250 1600 974.44" width="900" height="900">
                                                <?php foreach ($santander as $key => $value) : ?>
                                                <g id="<?php echo strtoupper($value['path']); ?>">
                                                    <path id="<?php echo strtoupper($value['path']); ?>"
                                                        d="<?php echo $value['d']; ?>"
                                                        fill="<?php echo $value["color"]; ?>"
                                                        class="municipios mapaClick <?php echo getClasePorcentaje(0.2); ?>"
                                                        data-base-url="<?php echo getUrl() . 'municipios_secretaria_informacion.php?mun=' . $value['codigo_muncipio'] . '&dep=' . $value['codigo_departamento']; ?>"
                                                        data-url="<?php echo getUrl() . 'municipios_secretaria_informacion.php?mun=' . $value['codigo_muncipio'] . '&dep=' . $value['codigo_departamento']; ?>"
                                                        data-name="<?php echo strtolower($value['municipio']); ?>"
                                                        title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_mapa'])); ?>"
                                                        stroke="#000" stroke-miterlimit="10" stroke-width="0.3px">
                                                    </path>
                                                </g>
                                                <?php endforeach; ?>

                                                <!-- Coordenadas de los nombres de los municipios de antioquia -->
                                                <?php require_once 'nombres_mapa_putumayo.php' ?>
                                                </svg>
                                            </div>
                                            </div>

                                            <div class="col col-md-auto">
                                            <nav class="nav justify-content-end doc-tab-nav align-items-center" role="tablist">
                                                <!-- puedes añadir botones extra aquí si los necesitas -->
                                            </nav>
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

       <div class="modal fade" id="modalGeocalizacion" tabindex="-1" aria-labelledby="modalGeocalizacionTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <!-- Header estilo Phoenix -->
                    <div class="modal-header bg-primary justify-content-between align-items-center position-relative" style="padding-right: 3rem;">
                        <h5 class="modal-title text-white m-0 w-100 text-center" id="modalGeocalizacionTitle">
                            Geolocalización por secretaria <span id="secretariaId"></span>
                        </h5>
                        <button type="button" class="close btn-close-white text-white position-absolute end-0 me-3"
                                data-dismiss="modal" aria-label="Cerrar" style="top: 1rem; right: 1rem; font-size: 1.5rem;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Cuerpo del modal -->
                    <div class="modal-body">
                        <div id="map" style="height: 600px; width: 100%;"></div>
                    </div>

                </div>
            </div>
        </div>


            <!-- Google Maps JavaScript API -->
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap">
            </script>
        </div>


        <!-- Required Js -->
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>
        <script type="text/javascript" src="admin/js/mapa_secretaria.js"></script>
        <script type="text/javascript" src="admin/js/secretarias.js"></script>
        <style>
            .mapaClick:hover {
            stroke: rgb(0, 238, 255);
            stroke-width: 2px;
            filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            }
                    /* Tabs estéticas */
            .tabs {
                width: 100%;
            }

            .tab-list {
                display: flex;
                flex-wrap: wrap;
                list-style: none;
                padding: 0;
                margin-bottom: 1rem;
                border-bottom: 2px solid #dee2e6;
                gap: 0.5rem;
            }

            .tab-list .tab {
                padding: 0.6rem 1rem;
                cursor: pointer;
                background-color: #f8f9fa;
                border-radius: 8px 8px 0 0;
                border: 1px solid #dee2e6;
                border-bottom: none;
                transition: background-color 0.3s, color 0.3s;
                font-weight: 500;
                font-size: 0.95rem;
                white-space: nowrap;
            }

            .tab-list .tab.active {
                background-color: #fff;
                color: #212529;
                border-bottom: 2px solid #fff;
                font-weight: 600;
                background-image: linear-gradient(to top, #1abc9c 2px, rgba(255, 255, 255, 0) 2px);
            }

            .tab-content {
                display: none;
                padding: 1rem 0.5rem;
                animation: fadeIn 0.3s ease-in-out;
                background-color: #fff;
            }

            .tab-content.active {
                display: block;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* Tablas dentro de la tarjeta */
            .table {
                margin-bottom: 1rem;
            }

            .table thead th {
                background-color: #e9ecef;
                font-weight: bold;
                text-align: center;
            }

            .table td, .table th {
                vertical-align: middle;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .tab-list {
                    flex-direction: column;
                    align-items: stretch;
                }

                .tab-list .tab {
                    border-radius: 8px;
                    border-bottom: 1px solid #dee2e6 !important;
                }

                .row .col-md-6 {
                    width: 100%;
                }
            }

        </style>
        <!-- JS para actualizar data-url dinámicamente al cambiar el select -->
        <script>
            // Evento de click en el mapa
            document.addEventListener('click', function(e) {
                const target = e.target.closest('.mapaClick');
                if (target) {
                    const baseUrl = target.getAttribute('data-base-url'); // siempre desde base
                    const secretaria = document.getElementById('secretariaId').value;
                    const separator = baseUrl.includes('?') ? '&' : '?';
                    const newUrl = `${baseUrl}${separator}secretaria=${secretaria}`;
                    if (newUrl) {
                        location.href = newUrl;
                    }
                }
            });
            $("img").each(function(index, el) {
                $(this).attr("data-bs-toggle", "tooltip");
                $(this).attr("data-bs-placement", "left");
                tooltip = new bootstrap.Tooltip($(this)[0], {})
            });
            document.querySelectorAll('.tab-list .tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    // Quitar active de todos
                    document.querySelectorAll('.tab-list .tab').forEach(t => t.classList.remove(
                        'active'));
                    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove(
                        'active'));
                    // Activar el tab y su contenido
                    tab.classList.add('active');
                    document.getElementById(tab.getAttribute('data-tab')).classList.add('active');
                });
            });
        </script>
</body>

</html>
