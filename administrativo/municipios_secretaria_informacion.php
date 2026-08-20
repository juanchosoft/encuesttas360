<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Departamento.php';
include './admin/classes/Proyectos.php';
include './admin/classes/MunicipioSecretaria.php';
require './admin/include/georeferenciacion.php';
include './admin/classes/SecretariasMunicipio.php';

// Permisos
$view = SessionData::getPermission(40);
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}
// Validar los parámetros "mun" y "dep"
if (isset($_REQUEST['mun'], $_REQUEST['dep'], $_REQUEST['secretaria']) && !empty(trim($_REQUEST['mun'])) && !empty(trim($_REQUEST['dep'])) && !empty(trim($_REQUEST['secretaria']))) {
    $municipio = trim($_REQUEST['mun']);
    $departamento = trim($_REQUEST['dep']);
    $secretaria = trim($_REQUEST['secretaria']);

    // Información de secretarias y proyectos del municipio
    $secretariasMunicipioProyectos = MunicipioSecretaria::getProyectosPorSecretariaByMunicipioId(array('municipioId' => $municipio, 'secretariaId' => $secretaria));

    $informacionPorSecretaria = SecretariasMunicipio::getAllSecretariaByCodigoMunicipio(array('secretariaId' => $secretaria, 'codigoMunicipio'=> $municipio));

} else { ?>
<!-- <script type='text/javascript'>
    alert('Información enviada no es correcta');
    window.location =
        'municipios_secretarias.php?mun=<?php echo Util::getCodigoMunicipioPrincipal(); ?>&dep=<?php echo Util::getDepartamentoPrincipal(); ?>';
</script> -->
<?php 
}

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
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

    <div class="content">
        <div class="row mb-4 mb-xl-6 mb-xxl-4 gy-3 justify-content-between">
            <div class="col-auto">
                <?php if (!empty($logo)): ?>
                    <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                        class="img-fluid img-thumbnail me-2 align-middle">
                <?php endif; ?>
                <h2 class="mb-0 text-body-emphasis d-inline align-middle">
                    Información secretarias - Municipio  -  <?php echo htmlspecialchars($nombreProyecto); ?>
                </h2>
            </div>
            <div class="col-auto">
            </div>
        </div>

        <div class="col-xxl-20">
            <div class="row gx-7 pe-xxl-3">
                <div class="col-12 col-xl-12 col-xxl-12">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-4">
                                <select onchange="DEPARTAMENTO.getMunicipios()" class="form-control"
                                    id="tbl_departamento_id" name="tbl_departamento_id">
                                    <?php echo $optionDep; ?>
                                </select>
                                <label for="tbl_departamento_id">Departamento</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-4">
                                <select onchange="MUNICIPIO.updateUrlMunicipio(this)" class="form-control"
                                    id="tbl_municipio_id" name="tbl_municipio_id"></select>
                                <label for="tbl_municipio_id">Municipio</label>
                            </div>
                        </div>
                    </div>


                    <div class="p-4 code-to-copy" id="divConsolidado">

                        <?php if ($secretariasMunicipioProyectos['output']['valid']): ?>
                        <h5 class="mb-3">Secretarias</h5>
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                    <?php
                                            $secretarias = $secretariasMunicipioProyectos['output']['response']['secretarias'];
                                            foreach ($secretarias as $index => $secretaria): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $index === 0 ? 'active' : '' ?>"
                                            id="tab-<?= htmlspecialchars($secretaria['id']) ?>" data-toggle="pill"
                                            href="#content-<?= htmlspecialchars($secretaria['id']) ?>" role="tab"
                                            aria-controls="content-<?= htmlspecialchars($secretaria['id']) ?>"
                                            aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                            <?= htmlspecialchars($secretaria['nombre']) ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>

                                <div class="tab-content" id="myTabContent">
                                    <?php
                                            $proyectos = $secretariasMunicipioProyectos['output']['response']['proyectos'];
                                            foreach ($secretarias as $index => $secretaria):
                                                // Filtrar los proyectos por la secretaría actual
                                                $filteredProyectos = array_filter($proyectos, fn($p) => $p['tbl_secretarias_id'] == $secretaria['id']);
                                            ?>
                                    <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                                        id="content-<?= htmlspecialchars($secretaria['id']) ?>" role="tabpanel"
                                        aria-labelledby="tab-<?= htmlspecialchars($secretaria['id']) ?>">

                                        <?php if (!empty($filteredProyectos)): ?>
                                        <table class="table table-bordered table-hover m-4">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Editar</th>
                                                    <th>ID Proyecto</th>
                                                    <th>Nombre del Proyecto</th>
                                                    <th>Valor</th>
                                                    <th>Porcentaje de Ejecución</th>
                                                    <th>Fecha de Entrega</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($filteredProyectos as $proyecto): ?>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            title="Editar"
                                                            onclick="location.href='detalle_proyectos_Secretarias.php?id=<?= htmlspecialchars($proyecto['tbl_proyecto_id'], ENT_QUOTES, 'UTF-8') ?>&nombre=<?= htmlspecialchars($proyecto['proyecto'], ENT_QUOTES, 'UTF-8') ?>'">
                                                            <i class="feather icon-edit"></i>
                                                        </button>
                                                    </td>
                                                    <td><?= htmlspecialchars($proyecto['tbl_proyecto_id']) ?></td>
                                                    <td><?= htmlspecialchars($proyecto['proyecto']) ?></td>
                                                    <td><?= htmlspecialchars(number_format($proyecto['valor_proyecto'], 2)) ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($proyecto['porcentaje_ejecucion']) ?>%
                                                    </td>
                                                    <td><?= htmlspecialchars($proyecto['fecha_entrega']) ?></td>
                                                    <td><?= htmlspecialchars($proyecto['estado']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                        <?php else: ?>
                                        <p>No hay proyectos disponibles para esta secretaría.</p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <p></p>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <?php
                                $responseAccionSecretarias = $informacionPorSecretaria['output']['response']['proyectos'];
                                $isAccionSecretaria = $informacionPorSecretaria['output']['valid'];
                            ?>
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Ciudad</th>
                                        <th scope="col">Vereda</th>
                                        <th scope="col">Eje</th>
                                        <th scope="col">Factor de Inestabilidad</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Medida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($isAccionSecretaria && !empty($responseAccionSecretarias)): ?>
                                    <div class="col-auto">
                                        <h3 class="text-center mb-3 mt-3">Información proyectos </h3>
                                    </div>
                                    <?php foreach ($responseAccionSecretarias as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['id_info']) ?></td>
                                        <td><?= htmlspecialchars($item['municipio']) ?></td>
                                        <td><?= htmlspecialchars($item['nombre_vereda']) ?></td>
                                        <td><?= htmlspecialchars($item['nombre_eje']) ?></td>
                                        <td><?= htmlspecialchars($item['tipo']) ?></td>
                                        <td><?= htmlspecialchars($item['valor']) ?></td>
                                        <td><?= htmlspecialchars($item['tipo_medicion']) ?></td>

                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No se encontraron registros.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include './admin/include/footer.php'; ?>

        </main>

        <!-- MODAL DE GEOLOCALIZACIÓN -->
        <div class="modal fade" id="modalGeolocalizacion" tabindex="-1" data-bs-backdrop="static"
            aria-labelledby="modalGeolocalizacionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header bg-primary justify-content-between">
                        <h5 class="modal-title text-white" id="modalGeolocalizacionLabel">Geolocalización</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <span class="fas fa-times fs-9 text-white"></span>
                        </button>
                    </div>
                    <!-- Body -->
                    <div class="modal-body" style="max-height: 70vh;">
                        <div id="map" style="width: 100%; height: 500px; background-color: #f0f0f0;">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>

        <script type="text/javascript" src="admin/js/departamento.js"></script>
        <script type="text/javascript" src="admin/js/municipios.js"></script>
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
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
                tabLinks.forEach(tab => {
                    tab.addEventListener('click', function(event) {
                        event.preventDefault();
                        tabLinks.forEach(link => link.classList.remove('active'));
                        const tabPanes = document.querySelectorAll('.tab-pane');
                        tabPanes.forEach(pane => pane.classList.remove('show', 'active'));
                        this.classList.add('active');
                        const targetPane = document.querySelector(this.getAttribute('href'));
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                        }
                    });
                });
            });
        </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
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