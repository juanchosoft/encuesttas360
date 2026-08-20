<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';


// Permisos
$view = SessionData::getPermission(17);
$create = SessionData::getPermission(18);
$edit = SessionData::getPermission(19);
if (!$view) {
    require 'permiso_denegado.php';
}

include './admin/classes/Proyectos.php';
include './admin/classes/Ministeriospro.php';
$modulo = 'Banco Proyectos';

// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
if (!empty($configuracionAplicacion[0])) {
    $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
    $logo = $configuracionAplicacion[0]['logo'] ?? '';
}

// Información de secretarias
$arr = Ministeriospro::getAllproyectosxalcal($_REQUEST);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$arrData = $arr;


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

    <!-- INICIO DE CONTENIDO -->
    <div class="content">
        <div class="row gy-3 mb-6 justify-content-between align-items-center">
            <div class="col-md-9 col-auto d-flex align-items-center">
                <h4 class="mb-2 me-3 text-body-emphasis">
                    Detalle Proyectos Alcaldias con ayuda de Secretarias - <?php echo $nombreProyecto; ?>
                </h4>

                <?php if (!empty($logo)): ?>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                    class="img-fluid img-thumbnail">
                <?php endif; ?>
            </div>

            <div class="col-12">

            </div>
        </div>
        <div>
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                    <!-- INICIO DE TABLA SEGUIMIENTO DE PROYECTOS DE ALCALDIAS -->
                    <div class="table-responsive m-4">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Detalle</th>
                                    <th>Ministerio</th>
                                    <th>Valor Proyecto</th>
                                    <th>Nombre Proyecto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($isvalid && !empty($arr)): ?>
                                    <?php foreach ($arr as $i => $row): ?>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" title="Ver Detalle"
                                                    onclick="location.href='detalle_proyectos_alcaldias.php?id=<?= urlencode($arrData[$i]['id']) ?>&nombre=<?= urlencode($arrData[$i]['nombre']) ?>'">
                                                    <i class="uil uil-eye"></i>
                                                </button>
                                            </td>
                                            <td><?= htmlspecialchars($row['ministerio']) ?></td>
                                            <td>$ <?= number_format($row['valor_proyecto'], 2) ?></td>
                                            <td><?= htmlspecialchars($row['proyecto']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No hay proyectos disponibles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- FIN DE TABLA SEGUIMIENTO DE PROYECTOS DE ALCALDIAS -->
                </div>
            </div>
        </div>
        <?php
        include './admin/include/footer.php';
        ?>
    </div>

    <!-- FIN DE CONTENIDO -->

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/ministerios_proyectos.js"></script>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>

</body>

</html>