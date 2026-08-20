<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Departamento.php';
include './admin/classes/Proyectos.php';
include './admin/classes/MunicipioSecretaria.php';
require './admin/include/georeferenciacion.php';
// Permisos
$view = SessionData::getPermission(40);
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Validar los parámetros "mun" y "dep"
if (isset($_REQUEST['mun'], $_REQUEST['dep'], $_REQUEST['pilar']) && !empty(trim($_REQUEST['mun'])) && !empty(trim($_REQUEST['dep'])) && !empty(trim($_REQUEST['pilar']))) {
    $municipio = trim($_REQUEST['mun']);
    $departamento = trim($_REQUEST['dep']);
    $pilar = trim($_REQUEST['pilar']);

    // Información de secretarias y proyectos del municipio
    $secretariasMunicipioProyectos = MunicipioSecretaria::getProyectosPorSecretariaByMunicipioId(array('municipioId' => $municipio));
} else { ?>
<script type='text/javascript'>
    alert('Información enviada no es correcta');
    window.location =
        'municipios_secretarias.php?mun=<?php echo Util::getCodigoMunicipioPrincipal(); ?>&dep=<?php echo Util::getDepartamentoPrincipal(); ?>';
</script>
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

?>

<main class="main" id="top">
    <?php
    include './admin/include/navbar.php';
    ?>
        <?php
    include './admin/include/header.php';
    ?>
      <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                  <h4 class="text-body mb-0 d-flex align-items-center" >
                  <i style="color:black !important;font-size: 1.9rem !important;" class="uil uil-building me-2 fs-4 text-primary"></i> Estado Municipios-Secretarias
                  </div>
                </div>
              </div>
                            <div class="card-body p-0">
                                    <input type="hidden" name="op" id="op" />
                                    <input type="hidden" name="id" id="id" />
                                    <input type="hidden" name="filtro" id="filtro" value="no" />
                                    <input type="hidden" name="filtroVeredaById" id="filtroVeredaById" value="no" />
                                    <div class="row">

                                    <div class="p-4 code-to-copy">
                                    <form class="row g-3 mb-6" id="formusuarios" role="form" autocomplete="false">
            
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-floating">
                                            <select class="form-select" onchange="DEPARTAMENTO.getMunicipios()" 
                                                    id="tbl_departamento_id" name="tbl_departamento_id">
                                                    <?php echo $optionDep; ?>
                                                </select>
                                                <label>Departamento<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" onchange="MUNICIPIO.updateUrlMunicipio(this)"
                                                     id="tbl_municipio_id"
                                                    name="tbl_municipio_id"></select>
                                                <label >Municipio<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                       
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                <!-- <div class="card-body p-0" id="divConsolidado">
                                    <div class="row g-3 justify-content-between align-items-center">
                                        <div class="col-12 col-md">
                                            <h4 class="text-body mb-0 d-flex align-items-center" >
                                            Secretarias
                                        </div>
                                </div> -->
        

                                    <?php if ($secretariasMunicipioProyectos['output']['valid']): ?>
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
                                                    <table  class="table table-striped table-sm fs-9 mb-0">
                                                        <thead>
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
                                                        <tbody class="list">
                                                            <?php foreach ($filteredProyectos as $proyecto): ?>
                                                            <tr>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary"
                                                                        title="Editar"
                                                                        onclick="location.href='detalle_proyectos_Secretarias.php?id=<?= htmlspecialchars($proyecto['tbl_proyecto_id'], ENT_QUOTES, 'UTF-8') ?>&nombre=<?= htmlspecialchars($proyecto['proyecto'], ENT_QUOTES, 'UTF-8') ?>'">
                                                                        <i class="uil uil-edit"></i>
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
                                    <p style="margin-left: 10px">No hay datos disponibles.</p>
                                    <?php endif; ?>

                                </div>
                        </div>
                    
                </div>
                <!-- [ sample-page ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <!-- Warning Section Ends -->

    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
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