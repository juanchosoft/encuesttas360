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

$arr = Ministeriospro::getAll(["id" => $_REQUEST["id"]]);
$isvalid = $arr['output']['valid'];
$proyecto = $arr['output']['response'][0];


// Información de secretarias
$arrobser = Ministeriospro::getAllobser(["proyecto" => $_REQUEST["id"]]);
$isvalidObser = $arrobser['output']['valid'];
$responseObservaciones = $arrobser['output']['response'];
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
        <div>
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                    <div class="card-header p-4 border-bottom bg-body">
                        <div class="row g-3 justify-content-between align-items-center">
                            <div class="col-12 col-md">
                                <h4 class="text-body mb-0 d-flex align-items-center">
                                    <i class="uil uil-clipboard-alt fs-6"></i>
                                    Detalle Proyectos Alcaldias con ayuda de Secretarias -
                                    <?php echo $nombreProyecto; ?>
                                    <?php if (!empty($logo)): ?>
                                    <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                                        class="img-fluid img-thumbnail me-3">
                                    <?php endif; ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <!-- INICIO DE FORM DE CREACION DE PROYECTOS ALCALDIAS -->
                    <div class="card-body p-0">
                        <div class="p-4 code-to-copy">
                            <form id="formalcaldias" class="row g-3 mb-6" role="form" autocomplete="false">

                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" name="date" id="date" type="date"
                                            value="<?= htmlspecialchars($proyecto['date']) ?>" readonly>
                                        <label for="date">Fecha</label>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($proyecto['municipio']) ?>" disabled>
                                        <label for="proyecto">Alcaldía</label>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($proyecto['proyecto']) ?>" disabled>
                                        <label for="proyecto">Objeto del proyecto</label>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                            value="<?= htmlspecialchars($proyecto['ministerio']) ?>" disabled>
                                        <label for="tbl_ministerios_id">Ministerio o Dependencia</label>
                                    </div>
                                </div>

                                <?php
                                $campos_aportes = [
                                    'aporte_nacion' => 'Aporte Nación',
                                    'aporte_gobernacion' => 'Aporte Gobernación',
                                    'aporte_municipio' => 'Aporte Municipio',
                                    'aporte_otros' => 'Aporte Otros',
                                    'valor_proyecto' => 'Total Inversión'
                                ];

                                foreach ($campos_aportes as $id => $label) {
                                ?>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="<?= $id ?>" name="<?= $id ?>"
                                            value="<?= htmlspecialchars($proyecto[$id]) ?>" readonly>
                                        <label for="<?= $id ?>"><?= $label ?></label>
                                    </div>
                                </div>
                                <?php } ?>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="observaciones" name="observaciones"
                                        style="height: 100px"
                                        readonly><?= htmlspecialchars($proyecto['observaciones']) ?></textarea>
                                    <label for="observaciones">Observaciones</label>
                                </div>

                                <!-- PDF viewer + descarga -->
                                <div class="col-sm-12">
                                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                                        <div style="width: 900px;">
                                            <p class="text-center mb-2 fw-bold">Documento PDF</p>
                                            <iframe id="ifmPdf" src="<?= htmlspecialchars($proyecto['pdf']) ?>"
                                                scrolling="no" frameborder="0"
                                                style="width: 100%; height: 650px;"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($isvalidObser && !empty($responseObservaciones)): ?>
                                        <?php foreach ($responseObservaciones as $i => $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['dtcreate']) ?></td>
                                            <td><?= htmlspecialchars($row['observaciones']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No hay observaciones disponibles.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- [ Main Content ] end -->
        </div>
        <?php
        include './admin/include/footer.php';
        ?>
    </div>

    <!-- FIN DE CONTENIDO -->

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>

</body>

</html>