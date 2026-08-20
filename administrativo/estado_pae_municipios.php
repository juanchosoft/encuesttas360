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

include './admin/classes/Departamento.php';
include './admin/classes/IngresoPae.php';


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


$arrPae = IngresoPae::getIngresoPaeByMunicipioCodigo(["tbl_municipio_id" => $_REQUEST["mun"]]);
$pae = $arrPae["output"]["response"];


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}


?>

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
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                        <h4 class="text-body mb-0 d-flex align-items-center">Caracterización Pae Municipios</h4>
                  </div>
                </div>
              </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div id="divInformacionGeneral" class="row">
                <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="card">


                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <form id="formusuarios" role="form" autocomplete="false">
                                        <input type="hidden" name="op" id="op" />
                                        <input type="hidden" name="id" id="id" />
                                        <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="tbl_departamento_id" name="tbl_departamento_id" disabled>
                                                        <?php echo $optionDep; ?>
                                                    </select>
                                                    <label for="tbl_departamento_id">Departamento<span class="text-danger mb-1">*</span></label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id" onchange="ESTADO_MUN_PAE.updateUrlMunicipio(this)">
                                                    </select>
                                                    <label for="tbl_municipio_id">Municipio<span class="text-danger mb-1">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Inversión Detallada -->
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-10">
                                        <div class="section-block text-center mb-4">
                                            <h3 class="section-title" style="font-size: 16px;">
                                                Caracterización detallada por Sede Educativa
                                            </h3>
                                        </div>

                                        <div id="tablaContenidoPae" class="table-responsive">
                                            <table class="table table-striped table-hover text-center">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>id</th>
                                                        <th>Ver Detallado</th>
                                                        <th>Provincia</th>
                                                        <th>Municipio</th>
                                                        <th>Vereda</th>
                                                        <th>Nombre Institución</th>
                                                        <th>Nombre Sede Educativa</th>
                                                        <th>Año</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($pae)) : ?>
                                                        <?php foreach ($pae as $value) : ?>
                                                            <tr>
                                                            <td><?= htmlspecialchars($value['id']) ?></td>
                                                                <td>
                                                                    <a href="reporte_pae.php?reporte=<?= htmlspecialchars($value['id']) ?>"
                                                                        target="_blank" title="Ver">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                                <td><?= htmlspecialchars($value['provincia']) ?></td>
                                                                <td><?= htmlspecialchars($value['municipio']) ?></td>
                                                                <td><?= htmlspecialchars($value['nombre_vereda']) ?></td>
                                                                <td><?= htmlspecialchars($value['nombre_institucion']) ?></td>
                                                                <td><?= htmlspecialchars($value['nombre']) ?></td>
                                                                <td><?= htmlspecialchars($value['ano']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else : ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center text-muted">No se encontraron registros.</td>
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
                </div>
                <!-- [ sample-page ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
        <?php
    include './admin/include/footer.php';
    ?>
    </div>
    <!-- [ Main Content ] end -->


    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/estado_municipios_pae.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Morris.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>

    <!-- // Variables para mostrrar en los graficos -->

    <script>
    setTimeout(function() {
        $("#tbl_departamento_id").val('86')
    }, 500);
    setTimeout(function() {
        DEPARTAMENTO.getMunicipios();
    }, 1000);
    </script>

</body>

</html>