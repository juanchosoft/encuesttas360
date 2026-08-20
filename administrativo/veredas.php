<?php
include './admin/include/head.php';
require './admin/include/georeferenciacion.php';
require './admin/include/generic_classes.php';
include './admin/classes/Colombia.php';
include './admin/classes/Departamento.php';
include './admin/classes/Vereda.php';
include './admin/classes/Pilar.php';
include './admin/classes/Area.php';
include './admin/classes/Actores.php';
include './admin/classes/CompromisosFactorPilar.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

$view = $create = $edit = true;
if (
    isset($_REQUEST['id'], $_REQUEST['dep'], $_REQUEST['mun'], $_REQUEST['pilar']) &&
    !empty(trim($_REQUEST['id'])) &&
    !empty(trim($_REQUEST['dep'])) &&
    !empty(trim($_REQUEST['pilar'])) &&
    !empty(trim($_REQUEST['mun']))
) {
    $vereda = trim($_REQUEST['id']);
    $municipio = trim($_REQUEST['mun']);
    $departamento = trim($_REQUEST['dep']);
    $pilar = trim($_REQUEST['pilar']);

    // Validar si el municipio y la vereda es válido cuando es un ALCALDE
    $municipioUsuarioLogueado = SessionData::getCodigoMunicipio();
    // Validacion por Alcalde
    if (SessionData::getUserType() ===  Util::Alcalde()) {
        if ($municipioUsuarioLogueado != $municipio) { ?>
<script type='text/javascript'>
    alert('No tiene permisos para ver este municipio y/o vereda.');
    window.location =
        'municipios.php?mun=<?php echo SessionData::getCodigoMunicipio(); ?>&dep=<?php echo $codigo_departamento; ?>&pilar=<?php echo $pilarConfiguracion; ?>';
    exit();
</script>
<?php
        }
    }

    // Obtener información de mapa
    $arr = ['codigo_departamento' => $codigo_departamento, 'codigo_municipio' => $municipio, 'veredaId' => $vereda, 'pilar' => $pilar, 'veredaId' => $vereda];
    $data = Colombia::calcularColorPorVeredaByPilarId($arr);
    $isvalid = $arr['output']['valid'] ?? false;
    $veredaMapa = $data['output']['response'] ?? null;
    $cantidadResultadoVereda = $veredaMapa[0]['cantidad_mostrar'] ?? 0;

    // Informacion de Veredas    
    $veredaResponse = Vereda::getAll(array('id' => $vereda));
    $informacionVereda = $veredaResponse['output']['response'][0] ?? null;
    $nombreVereda = isset($informacionVereda['nombre_vereda']) ? ($informacionVereda['nombre_vereda']) : '';

    // Información de consolidado por municipio de pilar, factor, eje
    $dataConsolidado = Colombia::consultarConsolidadPilaresFactoresByVeredaId($arr);
    $isvalidConsolidado = $dataConsolidado['output']['valid'] ?? false;
    $responseConsolidadoPilares = $dataConsolidado['output']['response'] ?? null;
    $tabs = $dataConsolidado['output']['pilares'] ?? null;
} else {
    require 'parametros_no_son_correctos.php';
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
    // Generar las opciones en un solo paso
    $optionPilar = array_reduce($arrPilar, function ($carry, $val) {
        return $carry . "<option value='{$val['id']}'>{$val['nombre']}</option>";
    }, '');
} else {
    $optionPilar = '';
}

// Información de Actores
$responseActors = Actores::getAll(null);
if (!empty($responseActors['output']['valid'])) {
    $arrActores = $responseActors['output']['response'];
    // Generar las opciones en un solo paso
    $optionActores = array_reduce($arrActores, function ($carry, $val) {
        return $carry . "<option value='{$val['id']}'>{$val['nombre']}</option>";
    }, '');
} else {
    $optionActores = '';
}

// Información de compromisos
$parametrosCompromisoPilarId = array('pilarId' => $pilar, 'veredaId' => $vereda);
$responseCompromisosFactores = CompromisosFactorPilar::getCompromisosFactores($parametrosCompromisoPilarId);
$compromosisoIsValid = $responseCompromisosFactores['output']['valid'];
$responseCompromisos = $responseCompromisosFactores['output']['response'];
?>

<body class="">
    <style>
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
    <?php include 'admin/include/scriptsgober360.php'; ?>

    <?php
    include './admin/include/navbar.php';
    ?>
    <?php
    include './admin/include/header.php';
    ?>
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
                    <strong>afectación</strong> por vereda.
                </h5>
            </div>
        </div>

        <div class="pcoded-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- SVG pequeño a la izquierda -->
                                <div class="col-md-4">
                                    <div class="border rounded p-2 bg-light text-center">
                                        <div style="width: 100%; height: auto; overflow: hidden;">
                                            <div id="contenido-mapa"
                                                style="width: 100%; height: 250px; overflow: hidden; display: flex; align-items: center; justify-content: center;">

                                                <?php
                                                // Verificar si todos los campos 'points' están vacíos
                                                $usePath = true;
                                                foreach ($veredaMapa as $value) {
                                                    if (!empty($value['points'])) {
                                                        $usePath = false;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <!-- // AJUSTAR VEREDA AL CUADRO SEGUN TAMAÑO -->
                                                <?php
                                                $minX = $minY = PHP_INT_MAX;
                                                $maxX = $maxY = PHP_INT_MIN;

                                                if ($usePath) {
                                                    foreach ($veredaMapa as $value) {
                                                        if (!empty($value['path'])) {
                                                            preg_match_all('/([0-9]+(?:\.[0-9]+)?),([0-9]+(?:\.[0-9]+)?)/', $value['path'], $matches, PREG_SET_ORDER);
                                                            foreach ($matches as $match) {
                                                                $x = floatval($match[1]);
                                                                $y = floatval($match[2]);
                                                                $minX = min($minX, $x);
                                                                $maxX = max($maxX, $x);
                                                                $minY = min($minY, $y);
                                                                $maxY = max($maxY, $y);
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    foreach ($veredaMapa as $value) {
                                                        if (!empty($value['points'])) {
                                                            $points = explode(' ', trim($value['points']));
                                                            foreach ($points as $point) {
                                                                [$x, $y] = explode(',', $point);
                                                                $x = floatval($x);
                                                                $y = floatval($y);
                                                                $minX = min($minX, $x);
                                                                $maxX = max($maxX, $x);
                                                                $minY = min($minY, $y);
                                                                $maxY = max($maxY, $y);
                                                            }
                                                        }
                                                    }
                                                }

                                                $padding = 90;
                                                $viewBoxX = $minX - $padding;
                                                $viewBoxY = $minY - $padding;
                                                $viewBoxWidth = ($maxX - $minX) + ($padding * 10);
                                                $viewBoxHeight = ($maxY - $minY) + ($padding * 10);

                                                $viewBoxFinal = "$viewBoxX $viewBoxY $viewBoxWidth $viewBoxHeight";
                                                ?>

                                                <?php if ($usePath): ?>
                                                <!-- Mostrar SVG con <path> -->
                                                <svg id="b" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="<?= $viewBoxFinal ?>"
                                                    style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;"
                                                    preserveAspectRatio="xMidYMid meet" stroke-width="1.2px"
                                                    stroke="#fff">
                                                    <?php foreach ($veredaMapa as $key => $value) : ?>
                                                    <g id="<?php echo $value['name']; ?>">
                                                        <path d="<?php echo $value['path']; ?>"
                                                            title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_vereda'])); ?>"
                                                            style="fill:<?php echo $value['color_calculado']; ?>"
                                                            stroke="#f3c5c5" data-tooltip-id="my-tooltip"
                                                            data-tippy-content="<?php echo strtolower($value['nombre_vereda']); ?>"
                                                            onClick="handlePolygonClick(this)"
                                                            data-url="<?php echo 'veredas.php?id=' . $value['id'] . '&mun=' . $value['municipio_id'] . '&dep=' . $value['departamento_id']; ?>"
                                                            stroke-miterlimit="10" stroke-width="3px" />
                                                    </g>
                                                    <?php endforeach; ?>

                                                    <?php foreach ($veredaMapa as $key => $value2) : ?>
                                                    <?php
                                                            // Aplicar estilo a las etiquetas <tspan>
                                                            echo str_replace(
                                                                '<tspan',
                                                                '<tspan style="fill: black; font-weight: bold; font-size: 13.5px; stroke: black; stroke-width: 0.2px;"',
                                                                $value2['tspan']
                                                            );
                                                            ?>
                                                    <?php endforeach; ?>
                                                </svg>

                                                <?php else: ?>

                                                <!-- Mostrar SVG con <polygon> -->
                                                <svg id="b" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="<?= $viewBoxFinal ?>"
                                                    style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;"
                                                    preserveAspectRatio="xMidYMid meet" stroke-width="1.2px"
                                                    stroke="#fff">
                                                    <?php foreach ($veredaMapa as $key => $value) : ?>
                                                    <g id="<?php echo strtoupper($value['name']); ?>">
                                                        <polygon points="<?php echo strtoupper($value['points']); ?>"
                                                            fill="<?php echo strtolower($value['color_calculado']); ?>"
                                                            fill-rule="evenodd" stroke="#fff"
                                                            data-name="<?php echo strtolower($value['nombre_vereda']); ?>"
                                                            data-tooltip-id="my-tooltip"
                                                            data-tippy-content="<?php echo strtolower($value['nombre_vereda']); ?>"
                                                            onClick="handlePolygonClick(this)"
                                                            data-url="<?php echo 'veredas.php?id=' . $value['id'] . '&mun=' . $value['municipio_id'] . '&dep=' . $value['departamento_id']; ?>"
                                                            stroke-miterlimit="10" stroke-width="2" />
                                                    </g>
                                                    <?php endforeach; ?>

                                                    <?php foreach ($municipiosDepartamento as $key => $value2) : ?>
                                                    <?php
                                                            // Aplicar estilo a las etiquetas <tspan>
                                                            echo str_replace(
                                                                '<tspan',
                                                                '<tspan style="fill: black; font-weight: bold; font-size: 13.5px; stroke: black; stroke-width: 0.2px;"',
                                                                $value2['tspan']
                                                            );
                                                            ?>
                                                    <?php endforeach; ?>
                                                </svg>

                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información y controles a la derecha -->
                                <div class="col-md-8">
                                    <div class="d-flex flex-column justify-content-start">
                                        <div class="mb-3 text-center">
                                            <div
                                                class="d-inline-flex flex-column align-items-center border-bottom pb-2 mb-2">
                                                <i class="uil uil-map-marker me-1"
                                                    style="font-size: 24px; color: red;"></i>
                                                <span><strong>Vereda:</strong> <?php echo $nombreVereda; ?></span>
                                                <span><strong>Puntaje:</strong>
                                                    <?php echo $cantidadResultadoVereda; ?></span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Aquí tus selects de departamento, municipio, vereda y pilar -->
                                            <div class="col-sm-6 col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="tbl_departamento_id"
                                                        name="tbl_departamento_id">
                                                        <?php echo $optionDep; ?>
                                                    </select>
                                                    <label>Departamento</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="tbl_municipio_id"
                                                        name="tbl_municipio_id"></select>
                                                    <label>Municipio</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" onchange="VEREDAS.updateUrlVereda(this)"
                                                        id="tbl_vereda_id" name="tbl_vereda_id"></select>
                                                    <label>Vereda</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="pilarId" name="pilarId"
                                                        onchange="VEREDAS.updateUrlPilar(this)">
                                                        <?php echo $optionPilar; ?>
                                                    </select>
                                                    <label>Pilar</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12" id="divConsolidado">

                            <div class="card">

                                <div class="card-body">
                                    <h3 class="text-center border-bottom pb-2 mb-4 text-body-emphasis fw-semibold">
                                        <i class="fas fa-landmark me-2"></i> Factores de Inestabilidad
                                    </h3>

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
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-sm table-striped fs-9 mb-0 text-center align-middle">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Factor</th>
                                                            <th>Cantidad</th>
                                                            <th>Unidad Medida</th>
                                                            <th>Geolocalización</th>
                                                            <th>Compromiso</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($areaData as $data): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <img src="<?= htmlspecialchars($data['icono']) ?>"
                                                                        alt="Icono Factor" width="30" class="mb-1">
                                                                    <span
                                                                        class="fw-semibold text-danger"><?= htmlspecialchars($data['factor']) ?></span>
                                                                    <span class="text-muted small">Puntaje
                                                                        <?= htmlspecialchars($data['puntaje']) ?></span>
                                                                </div>
                                                            </td>
                                                            <td><?= htmlspecialchars($data['total_cantidad']) ?></td>
                                                            <td><?= htmlspecialchars($data['tipo_medicion']) ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                    data-toggle="modal"
                                                                    data-target="#modalGeocalizacion"
                                                                    onclick="mostrarInformacionPilarByVereda('<?= htmlspecialchars($data['longitud']) ?>','<?= htmlspecialchars($data['latitud']) ?>')">
                                                                    <img src="assets/iconos/geo.png" alt="Geo"
                                                                        width="18">
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <button style="background-color:#3874ff" type="button"
                                                                    class="btn btn-sm btn-outline-success"
                                                                    data-toggle="modal" data-target="#modalSeleccionar"
                                                                    data-pilar="<?= htmlspecialchars($data['pilar']) ?>"
                                                                    data-cantidad="<?= htmlspecialchars($data['total_cantidad']) ?>"
                                                                    onclick="VEREDAS.abrirModalCompromiso(<?= htmlspecialchars($data['tbl_factor_id']) ?>, <?= htmlspecialchars($data['total_cantidad']) ?>)">
                                                                    <i class="fas fa-handshake" style="color:black"
                                                                        width="18"></i>
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
                                <div class="card-body">
                                    <h3 class="text-center border-bottom pb-2 mb-4 text-body-emphasis fw-semibold">
                                        <i class="fas fa-handshake me-2"></i> Compromisos
                                    </h3>

                                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="table-responsive">
                                            <table
                                                class="table table-sm table-striped fs-9 mb-0 text-center align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Cantidad en su momento</th>
                                                        <th>Cantidad compromiso</th>
                                                        <th>Actor</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyCompromisos">
                                                    <?php if ($compromosisoIsValid && !empty($responseCompromisos)): ?>
                                                    <?php foreach ($responseCompromisos as $item): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($item['dtcreate']) ?></td>
                                                        <td><?= htmlspecialchars($item['cantidad_instante']) ?></td>
                                                        <td><?= htmlspecialchars($item['cantidad']) ?></td>
                                                        <td><?= htmlspecialchars($item['actor']) ?></td>
                                                        <td class="text-start">
                                                            <?= htmlspecialchars($item['observaciones']) ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">No se encontraron
                                                            registros.</td>
                                                    </tr>
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

                <!-- [ sample-page ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- Modal para Asignar Compromiso -->
    <div class="modal fade" id="modalSeleccionar" tabindex="-1" data-bs-backdrop="static"
        aria-labelledby="modalSeleccionarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">

                <!-- Encabezado del modal -->
                <div class="modal-header bg-primary justify-content-between">
                    <h5 class="modal-title text-white" id="modalSeleccionarLabel">
                        <i class="fa-solid fa-tasks me-2"></i> Asignar Compromiso
                    </h5>
                    <button class="btn p-1" type="button" data-dismiss="modal" aria-label="Cerrar">
                        <span class="fas fa-times fs-9 text-white"></span>
                    </button>

                </div>

                <!-- Alerta -->
                <div id="alertaCompromiso" class="w-100 text-center p-2" style="display: none;"></div>

                <!-- Cuerpo con scroll -->
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <form id="formCompromiso" class="row g-3 mb-6">

                        <input type="hidden" id="factorIdModal" name="factorIdModal">
                        <input type="hidden" id="veredaId" name="veredaId" value="<?php echo $vereda; ?>">
                        <input type="hidden" id="municipioId" name="municipioId" value="<?php echo $municipio; ?>">
                        <input type="hidden" id="departamentoId" name="departamentoId"
                            value="<?php echo $departamento; ?>">

                        <div class="col-sm-6 col-md-8">
                            <div class="form-floating">
                                <input class="form-control" type="number" id="cantidadActual" name="cantidadActual" />
                                <label for="cantidadActual">Cantidad Actual<span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div div class="col-sm-6 col-md-8">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="cantidadCompromiso"
                                    name="cantidadCompromiso" placeholder="Ingrese la cantidad">
                                <label for="cantidadCompromiso">Cantidad Nueva<span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <!-- Actor -->
                        <!-- <div class="mb-3">
            <label for="actoresId" class="form-label fw-bold">Seleccione un Actor</label>
            <select class="form-select text-center" id="actoresId" name="actoresId">
              <?php echo $optionActores; ?>
            </select>
          </div> -->
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select " id="actoresId" name="actoresId">
                                    <?php echo $optionActores; ?>
                                </select>
                                <label for="actoresId">Seleccione un Actor<span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <!-- Observaciones -->

                        <div class="d-flex justify-content-center mb-4">
                            <div class="form-floating" style="width: 100%; max-width: 800px;">
                                <textarea class="form-control " id="observacionesCompromiso"
                                    name="observacionesCompromiso" rows="3"
                                    placeholder="Ingrese las observaciones"></textarea>
                                <label for="observacionesCompromiso">Observaciones</label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Pie del modal -->
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success" type="button"
                        onclick="VEREDAS.guardarCompromiso();">Guardar</button>
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
                            Geolocalización <span id="nombrePilar"></span>
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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5/css/bootstrap.min.css">
    <!-- Iconos de caja -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Warning Section Ends -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/veredas.js"></script>
    <!-- Google Maps JavaScript API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap">
    </script>
    <script type="text/javascript" src="admin/js/mapa_veredas_geo.js"></script>
    <script>
        function mostrarAlerta(tipo, mensaje) {
            const alerta = $("#alertaCompromiso");
            if (tipo === "error") {
                alerta.removeClass("bg-success").addClass("bg-danger text-white");
            } else {
                alerta.removeClass("bg-danger").addClass("bg-success text-white");
            }
            alerta.html(mensaje).fadeIn();
            setTimeout(() => {
                alerta.fadeOut();
            }, 3000);
        }
    </script>
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
</body>

</html>