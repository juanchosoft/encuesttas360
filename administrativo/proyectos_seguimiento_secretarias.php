<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
// Permisos
$view = SessionData::getPermission(73);
$create = SessionData::getPermission(74);
$edit = SessionData::getPermission(75);
if (!$view) {
    require 'permiso_denegado.php';
}

$modulo = 'Banco Proyectos';

include './admin/classes/Proyectos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Secretarias.php';

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de secretarias
$arr = Secretarias::getAllproyectos(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$optionSec = "";
foreach ($arr as $val) {
    $optionSec .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . " </option>";
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
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="card-body table-border-style">
                                    <!-- Tabla de datos -->
                                    <div class="table-responsive">
                                        <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Ver</th>
                                                    <th>Secretaría</th>
                                                    <th>Suma Proyectos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($isvalid && !empty($arr)) : ?>
                                                    <?php foreach ($arr as $item) : ?>
                                                        <tr>
                                                            <td>
                                                                <form action="proyecto_x_secretaria.php" method="post" style="display:inline;">
                                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['tbl_secretarias_id']); ?>">
                                                                    <input type="hidden" name="secretaria" value="<?= htmlspecialchars($item['tbl_secretarias_id']); ?>">
                                                                    <button type="submit" class="btn btn-sm btn-primary" style="margin-left: 1rem;" title="Ver">
                                                                        <i class="uil uil-eye fs-8"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            <td><?= htmlspecialchars($item['secretaria']); ?></td>
                                                            <td>$ <?= number_format($item['sumaproyectos'], 2); ?></td>
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
        <?php
        include './admin/include/footer.php';
        ?>
    </div>

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>



    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/proyectos.js"></script>


    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
    <script>
        setTimeout(function() {
            $("#tbl_departamento_id").val('68')
        }, 500);
        setTimeout(function() {
            DEPARTAMENTO.getMunicipios();
        }, 1000);
    </script>

</body>

</html>