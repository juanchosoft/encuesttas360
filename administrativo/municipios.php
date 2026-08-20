<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Colombia.php';
include './admin/classes/Departamento.php';
include './admin/classes/Ciudad.php';
include './admin/classes/Pilar.php';
include './admin/classes/Puntaje.php';
include './admin/classes/Area.php';
require './admin/include/georeferenciacion.php';
// Permisos
$view = SessionData::getPermission(40);
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}
// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';


// Validar los parámetros "mun" y "dep"
if (isset($_REQUEST['mun'], $_REQUEST['dep'], $_REQUEST['pilar']) && !empty(trim($_REQUEST['mun'])) && !empty(trim($_REQUEST['dep'])) && !empty(trim($_REQUEST['pilar']))) {

    $municipio = trim($_REQUEST['mun']);
    $departamento = trim($_REQUEST['dep']);
    $pilar = trim($_REQUEST['pilar']);

    $_SESSION['municipio'] = $municipio;
    $_SESSION['departamento'] = $departamento;
    $_SESSION['pilar'] = $pilar;

    // Validar si el municipio es válido cuando es un ALCALDE
    $municipioUsuarioLogueado = SessionData::getCodigoMunicipio();
    // Validacion por Alcalde
    if (SessionData::getUserType() ===  Util::Alcalde()) {
        if ($municipioUsuarioLogueado != $municipio) { ?>
<script type='text/javascript'>
    alert('No tiene permisos para ver este municipio');
    window.location =
        'municipios.php?mun=<?php echo SessionData::getCodigoMunicipio(); ?>&dep=<?php echo $codigo_departamento; ?>&pilar=<?php echo $pilarConfiguracion; ?>';
    exit();
</script>
<?php
        }
    }

    // Obtener información de mapa
    $arr = ['codigo_departamento' => $codigo_departamento, 'codigo_municipio' => $municipio, 'pilar' => $pilar];
    $data = Colombia::calcularColorPorMunicipioByPilarId($arr);
    $isvalid = $arr['output']['valid'] ?? false;
    $municipiosDepartamento = $data['output']['response'] ?? null;

    // Información de Municipio
    $municipio = Ciudad::getInformacionCiudad(array('codigo_muncipio' => $municipio));
    $informacionMunicipio = $municipio['output']['response'][0] ?? null;
    $nombreMunicipio = isset($informacionMunicipio['municipio']) ? ($informacionMunicipio['municipio']) : '';
    $latitud = isset($informacionMunicipio['latitud']) ? ($informacionMunicipio['latitud']) : '';
    $longitud = isset($informacionMunicipio['longitud']) ? ($informacionMunicipio['longitud']) : '';

    // Información de consolidado por municipio de pilar, factor, eje
    $dataConsolidado = Colombia::consultarConsolidadPilaresFactores($arr);

    function getUrl($urlMapa = "")
    {
        $port = $_SERVER["SERVER_PORT"];
        $nameServer = $port != "80" ? $_SERVER['SERVER_NAME'] . ":" . $port : $_SERVER['SERVER_NAME'];

        $url = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $nameServer,
            dirname($_SERVER['PHP_SELF']) . '/'
        );

        if (!empty($urlMapa)) {
            $partsUrl = explode("/", $urlMapa);
            unset($partsUrl[(count($partsUrl) - 1)]);
            $url .= $partsUrl[1] . "/";
        }

        return $url;
    }
    $webroot = getUrl("../mapa-veredas/" . $_REQUEST["dep"]);

    $isvalidConsolidado = $dataConsolidado['output']['valid'] ?? false;
    $responseConsolidadoPilares = $dataConsolidado['output']['response'] ?? null;
    $tabs = $dataConsolidado['output']['tabs'] ?? null;
    $veredas = $dataConsolidado['output']['veredas'] ?? null;
    $_SESSION['veredas'] = $veredas;
    $_SESSION['webroot'] = $webroot;
} else { ?>
<script type='text/javascript'>
    alert('Información enviada no es correcta');
    window.location =
        'municipios.php?mun=<?php echo $codigoMunicipioConfiguracion; ?>&dep=<?php echo $codigo_departamento; ?>&pilar=<?php echo $pilarConfiguracion; ?>';
</script>
<?php
}

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = '';
foreach ($arrDep as $val) {
    if ($val["codigo_departamento"] == $codigo_departamento) {
        $optionDep .= "<option selected value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
        break;
    }
}


// Información de Pilares
$response = Pilar::getAll(null);
if (!empty($response['output']['valid'])) {
    $arrPilar = $response['output']['response'];

    // Generar las opciones con selección
    $optionPilar = array_reduce($arrPilar, function ($carry, $val) use ($pilar) {
        $selected = ($val['id'] == $pilar) ? ' selected' : '';
        return $carry . "<option value='{$val['id']}'{$selected}>{$val['nombre']}</option>";
    }, '');
} else {
    $optionPilar = '';
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<body class="">
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <style>
        #contenido-mapa polygon:hover,
        #contenido-mapa path:hover {
            stroke: rgb(0, 238, 255);
            stroke-width: 2px;
            filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        #contenido-mapa svg {
            width: 100%;
            height: auto;
            max-width: 100%;
            display: block;
        }

        #modalSvgFull .modal-body {
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        #svg-container-full {
            width: 100vw;
            height: 100vh;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #svg-container-full svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .tspan-lupa-hover {
            font-size: 3.8em !important;
            font-weight: bold;
            fill: #000;
            transition: all 0.2s ease-in-out;
        }

        @keyframes blink {
            0% {
                text-decoration-color: red;
                opacity: 1;
            }

            50% {
                opacity: 0.1;
            }

            100% {
                opacity: 1;
            }
        }

        .tspan-highlight {
            fill: red !important;
            font-weight: bold;
            text-decoration: underline;
            text-decoration-color: red;
            text-decoration-thickness: 2px;
            text-underline-offset: 2px;
            animation: blink 1s infinite;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--bs-body-color);
            background-color: transparent;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            border-color: rgba(0, 123, 255, 0.3);
            color: var(--bs-primary);
        }

        .nav-tabs .nav-link.active {
            border-color: #0d6efd;
            color: var(--bs-primary);
            background-color: transparent;
        }
    </style>

    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">

        <div class="row gy-3 mb-6 justify-content-between align-items-center">
            <div class="col-md-9 col-auto d-flex align-items-center">
                <h2 class="mb-2 me-3 text-body-emphasis">
                    Acción Unificada <?php echo $nombreProyecto; ?>
                </h2>

                <?php if (!empty($logo)): ?>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                    class="img-fluid img-thumbnail">
                <?php endif; ?>
            </div>

            <div class="col-12">
                <h5 class="text-body-tertiary fw-semibold">
                    Mapa interactivo por <strong>Pilar</strong>. Visualiza niveles de <strong>ejecución</strong> y
                    <strong>afectación</strong> por municipio.
                </h5>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <!-- Columna del Mapa -->
            <div class="col-lg-9">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 class="text-body d-flex align-items-center gap-2 mb-0">
                                <i class="uil uil-map-marker fs-4 text-primary"></i>
                                Estado Municipio por Pilar
                            </h4>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalSvgFull">
                                    <i class="bi bi-arrows-fullscreen"></i> Pantalla completa
                                </button>
                                <button id="btnToggleLupa"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                    title="Activar lupa">
                                    <i class="bi bi-search"></i> Lupa
                                </button>
                            </div>

                        </div>
                    </div>
                    <!-- AQUI SE TRAEN LOS VIEWBOX PARA LOS MUNICIPIOS -->
                    <?php
        $viewBoxActual = '10 800 1900 2'; // valor por defecto
        if (!empty($informacionMunicipio['viewbox_svg'])) {
            $viewBoxActual = $informacionMunicipio['viewbox_svg'];
        }
        // valor por defecto
        ?>

                    <div class="card-body p-2 position-relative" id="mapa-container" style="min-height: 500px;">
                        <div id="contenido-mapa" class="w-100 h-100" style="overflow: hidden;">
                            <svg id="b" xmlns="http://www.w3.org/2000/svg" viewBox="<?php echo $viewBoxActual; ?>"
                                stroke-width="1.2px" stroke="#fff" width="1000" height="1000"
                                preserveAspectRatio="xMidYMid meet">

                                <?php foreach ($municipiosDepartamento as $value) : ?>
                                <g id="<?php echo $value['nombre_svg']; ?>">
                                    <?php if (!empty($value['points'])): ?>
                                    <!-- Mostrar <polygon> -->
                                    <polygon points="<?php echo strtoupper($value['points']); ?>"
                                        fill="<?php echo strtolower($value['color_calculado']); ?>" fill-rule="evenodd"
                                        stroke="#000" data-name="<?php echo strtolower($value['nombre_vereda']); ?>"
                                        data-tooltip-id="my-tooltip"
                                        data-tippy-content="<?php echo strtolower($value['nombre_vereda']); ?>"
                                        onClick="handlePolygonClick(this)"
                                        data-url="<?php echo 'veredas.php?id=' . $value['id'] . '&mun=' . $value['municipio_id'] . '&dep=' . $value['departamento_id']; ?>&pilar=<?php echo $pilar; ?>"
                                        stroke-miterlimit="10" stroke-width="0.3px" />
                                    <?php elseif (!empty($value['path'])): ?>
                                    <!-- Mostrar <path> -->
                                    <path d="<?php echo $value['path']; ?>"
                                        title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_vereda'])); ?>"
                                        style="fill:<?php echo strtolower($value['color_calculado']); ?>" stroke="#000"
                                        data-tooltip-id="my-tooltip"
                                        data-tippy-content="<?php echo strtolower($value['nombre_vereda']); ?>"
                                        onClick="handlePolygonClick(this)"
                                        data-url="<?php echo 'veredas.php?id=' . $value['id'] . '&mun=' . $value['municipio_id'] . '&dep=' . $value['departamento_id']; ?>&pilar=<?php echo $pilar; ?>"
                                        stroke-miterlimit="10" stroke-width="0.2px" />
                                    <?php endif; ?>
                                </g>
                                <?php endforeach; ?>

                                <?php foreach ($municipiosDepartamento as $value2) : ?>
                                <?php
                                        // Aqui se modifica letra en los mapas
                                        echo preg_replace_callback(
                                            '/<tspan([^>]*)>(.*?)<\/tspan>/i',
                                            function ($matches) {
                                                $atributos = $matches[1];
                                                $contenido = mb_strtolower($matches[2], 'UTF-8'); // Soporta tildes
                                                $style = 'fill: black; font-family: sans-serif; stroke: black; stroke-width: 0px;';
                                                return "<tspan style=\"$style\"$atributos>$contenido</tspan>";
                                            },
                                            $value2['tspan']
                                        );

                                        ?>
                                <?php endforeach; ?>
                            </svg>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de Selects -->
            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h6
                            class="text-body fw-bold text-uppercase text-center d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="bi bi-sliders2-vertical fs-5 text-primary"></i> Control del Mapa
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- SELECTORES -->
                        <div class="form-floating mb-3">
                            <select class="form-select" id="tbl_departamento_id" name="tbl_departamento_id"
                                onchange="DEPARTAMENTO.getMunicipios()">
                                <?php echo $optionDep; ?>
                            </select>
                            <label for="tbl_departamento_id">Departamento</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"
                                onchange="MUNICIPIO.updateUrlMunicipio(this)">
                                <!-- Opciones municipio -->
                            </select>
                            <label for="tbl_municipio_id">Municipio</label>
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select" id="pilarId" name="pilarId"
                                onchange="MUNICIPIO.updateUrlPilar(this)">
                                <?php echo $optionPilar; ?>
                            </select>
                            <label for="pilarId">Pilar</label>
                        </div>
                        <div class="input-group mb-4">
                            <input type="text" class="form-control" placeholder="Vereda" id="svgSearchInput">
                            <button class="btn btn-outline-secondary" type="button" id="svgSearchBtn">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                        <!-- RANGOS POR COLOR -->
                        <div class="table-responsive text-center">
                            <?php include './admin/classes/tabla_rangos_colores.php'; ?>
                        </div>
                        <div class="card-body d-flex flex-column gap-2">
                            <button class="btn btn-outline-primary w-100" id="btnAccion1">
                                <i class="bi bi-bank me-1"></i> Tabla por Pilares

                            </button>

                        </div>
                    </div>
                </div>

            </div>
            <!-- Tablas Temporales -->
            <div class="col-sm-12" id="divConsolidado">
                <div class="text-center mt-5 mb-3 d-flex justify-content-center align-items-center gap-2"
                    id="tablaPilares">
                    <i class="bi bi-bank fs-4 text-primary"></i>
                    <h4 class="m-0 fw-bold text-uppercase text-dark">Pilares</h4>
                </div>
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <?php foreach ($tabs as $index => $tab): ?>
                            <?php if ($tab['enable'] === 'si'): // Mostrar solo los tabs habilitados 
                                                $img = !empty($tab["icono"]) ?  htmlspecialchars($tab["icono"]) : 'assets/iconos/gobierno.png';
                                            ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $index === 0 ? 'active' : '' ?>"
                                    id="tab-<?= htmlspecialchars($tab['id']) ?>" data-toggle="pill"
                                    href="#content-<?= htmlspecialchars($tab['id']) ?>" role="tab"
                                    aria-controls="content-<?= htmlspecialchars($tab['id']) ?>"
                                    aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                    <img src="<?= htmlspecialchars($img) ?>" alt="" width="30px">
                                    <?= htmlspecialchars($tab['nombre']) ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <?php foreach ($tabs as $index => $tab): ?>
                            <?php if ($tab['enable'] === 'si'): ?>
                            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                                id="content-<?= htmlspecialchars($tab['id']) ?>" role="tabpanel"
                                aria-labelledby="tab-<?= htmlspecialchars($tab['id']) ?>">

                                <!-- Verificar si hay datos para este área específica -->
                                <?php
                                                    $areaData = array_filter(
                                                        $responseConsolidadoPilares,
                                                        fn($item) => $item['area_id'] == $tab['id']
                                                    );
                                                    ?>

                                <?php if (!empty($areaData)): ?>
                                <div class="table-wrapper">
                                    <table class="table table-striped table-municipio">
                                        <thead>
                                            <tr>
                                                <th>Factor</th>
                                                <th>Vereda</th>
                                                <th>Cantidad</th>
                                                <th>Unidad Medida</th>
                                                <th>Geolocalización</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($areaData as $data): ?>
                                            <tr>
                                                <td style="text-align: center;">
                                                    <div
                                                        style="display: flex; flex-direction: column; align-items: center;">
                                                        <img src="<?= htmlspecialchars($data['icono']) ?>"
                                                            alt="Factor Icono" width="30px" />
                                                        <span
                                                            style="font-weight: bold; color: red;"><?= htmlspecialchars($data['factor']) ?></span>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($data['nombre_vereda']) ?></td>
                                                <td><?= htmlspecialchars($data['total_cantidad']) ?></td>
                                                <td><?= htmlspecialchars($data['tipo_medicion']) ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-geo"
                                                        data-toggle="modal" data-target="#modalGeocalizacion"
                                                        onclick="mostrarInformacionPilarByMunicipio()">
                                                        <img src="assets/iconos/geo.png" alt="" width="30px">
                                                    </button>

                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p>No hay datos disponible para esta área.</p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>

            <!-- MODAL PANTALLA COMPLETA -->
            <div class="modal fade" id="modalSvgFull" tabindex="-1" aria-labelledby="modalSvgFullLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-fullscreen" style="width: 100%">
                    <div class="modal-content bg-white">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalSvgFullLabel" style="color:white">Vista completa del mapa
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="w-100 h-100 overflow-auto p-3" style="background: #f9f9f9;">

                                <div id="svg-container-full"
                                    class="w-100 h-100 d-flex justify-content-center align-items-center">
                                    <!-- MAPA -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de Geolocalización -->
            <div class="modal fade" id="modalGeocalizacion" tabindex="-1" role="dialog"
                aria-labelledby="modalGeocalizacionTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <div class="w-100 text-center">
                                <h5 class="modal-title text-white m-0" id="modalGeocalizacionTitle">
                                    Geolocalización: <span id="nombrePilar"></span>
                                </h5>
                            </div>
                            <button type="button" class="close btn-close-white text-white position-absolute end-0 me-3"
                                data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div id="map" style="height: 600px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript" src="admin/js/departamento.js"></script>
            <script type="text/javascript" src="admin/js/municipios.js"></script>
            <script src="admin/js/pantalla_completa.js"></script>
            <script>
                MUNICIPIO.init();

                function handlePolygonClick(element) {
                    const url = element.getAttribute('data-url'); // Obtén la URL del atributo data-url
                    if (url) {
                        window.location.href = url; // Redirige al enlace
                    } else {
                        console.error('No se encontró una URL válida.');
                    }
                }
                document.addEventListener("DOMContentLoaded", function() {
                    const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
                    tabLinks.forEach(tab => {
                        tab.addEventListener('click', function(event) {
                            event.preventDefault();
                            tabLinks.forEach(link => link.classList.remove('active'));
                            const tabPanes = document.querySelectorAll('.tab-pane');
                            tabPanes.forEach(pane => pane.classList.remove('show', 'active'));
                            this.classList.add('active');
                            const targetPane = document.querySelector(this.getAttribute(
                            'href'));
                            if (targetPane) {
                                targetPane.classList.add('show', 'active');
                            }
                        });
                    });
                });
                document.getElementById('btnAccion1').addEventListener('click', function() {
                    const seccion = document.getElementById('tablaPilares');
                    if (seccion) {
                        seccion.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            </script>

            <!-- Google Maps JavaScript API -->
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap">
            </script>
            <script type="text/javascript" src="admin/js/mapa_municipio_geo.js"></script>

            <script>
                let mapaModalIniciado = false;
                $('#modalGeocalizacion').on('shown.bs.modal', function() {
                    if (!mapaModalIniciado) {
                        initMap();
                        mapaModalIniciado = true;
                    } else {
                        google.maps.event.trigger(map, 'resize');
                        const lat = parseFloat($("#latitud").val()) || 1.146794;
                        const lng = parseFloat($("#longitud").val()) || -76.647874;
                        map.setCenter({
                            lat: lat,
                            lng: lng
                        });
                    }
                });
            </script>

            <script>
                let lupaActiva = false;
                document.getElementById('btnToggleLupa').addEventListener('click', function() {
                    lupaActiva = !lupaActiva;
                    this.classList.toggle('active', lupaActiva);
                });
                document.addEventListener('mouseover', function(e) {
                    if (!lupaActiva) return;
                    if (e.target.nodeName.toLowerCase() !== 'tspan') return;
                    const style = window.getComputedStyle(e.target);
                    const fontSizePx = parseFloat(style.fontSize);
                    if (!fontSizePx || isNaN(fontSizePx)) return;
                    if (e.target.dataset.originalFontSize) return;
                    e.target.dataset.originalFontSize = e.target.style.fontSize;
                    let factor = 48 / fontSizePx;
                    factor = Math.min(Math.max(factor, 1), 8); // limitar entre 1.2x y 5x
                    e.target.style.fontSize = (fontSizePx * factor) + 'px';
                    e.target.style.fontWeight = 'bold';
                    e.target.style.transition = 'all 0.2s ease-in-out';
                });
                document.addEventListener('mouseout', function(e) {
                    if (!lupaActiva) return;
                    if (e.target.nodeName.toLowerCase() !== 'tspan') return;
                    if (e.target.dataset.originalFontSize !== undefined) {
                        e.target.style.fontSize = e.target.dataset.originalFontSize;
                        e.target.style.fontWeight = '';
                        e.target.style.transition = '';
                        delete e.target.dataset.originalFontSize;
                    }
                });
            </script>
            <script>
                document.getElementById('svgSearchBtn').addEventListener('click', () => {
                    const input = document.getElementById('svgSearchInput');
                    const searchText = input.value.trim().toLowerCase();
                    document.querySelectorAll('tspan.tspan-highlight').forEach(el => {
                        el.classList.remove('tspan-highlight');
                    });
                    if (!searchText) return;
                    const tspans = document.querySelectorAll('#contenido-mapa svg tspan');
                    tspans.forEach(t => {
                        const text = t.textContent.trim().toLowerCase();
                        if (text.includes(searchText)) {
                            t.classList.add('tspan-highlight');
                        }
                    });
                });
            </script>

</body>

</html>