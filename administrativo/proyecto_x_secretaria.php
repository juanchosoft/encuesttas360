<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Proyectos.php';
$modulo = 'Banco Proyectos';


// Información de secretarias
$arr = Proyectos::getAllproyectosxsecre($_REQUEST);
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

    <!-- [ Main Content ] start -->
        <div class="content">
            <div>
                <div class="col-11 col-xl-11 mx-auto">
                    <div class="card shadow-none border my-4" data-component-card="data-component-card">
                        <div class="p-4 code-to-copy">
                            <div class="table-responsive">
                                <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Acciones</th>
                                                    <th>Item</th>
                                                    <th>Secretaría</th>
                                                    <th>Valor Proyecto</th>
                                                    <th>Nombre Proyecto</th>
                                                    <th>Porcentaje Ejecución</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Estado</th>
                                                    <th>Ejecución</th>
                                                    <th>Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $c = count($arr);
                                                if ($isvalid) {
                                                    for ($i = 0; $i < $c; $i++) {
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <button type="button" id="<?php echo $arr[$i]['id']; ?>" style="margin-left: 1rem;" title="Editar"
                                                                    onclick="location.href='detalle_proyectos_Secretarias.php?id=<?php echo $arr[$i]['id']; ?>&nombre=<?php echo $arr[$i]['nombre']; ?>'"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class="uil uil-edit"></i>
                                                                </button>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($arr[$i]['id']); ?></td>
                                                            <td><?php echo htmlspecialchars($arr[$i]['secretaria']); ?></td>
                                                            <td>$ <?php echo number_format($arr[$i]['valor_proyecto'], 2); ?></td>
                                                            <td><?php echo htmlspecialchars($arr[$i]['proyecto']); ?></td>
                                                            <td><?php echo htmlspecialchars($arr[$i]['porcentaje_ejecucion']); ?> %</td>
                                                            <td><?php echo htmlspecialchars($arr[$i]['fecha_entrega']); ?></td>
                                                            <td><?php echo htmlspecialchars($arr[$i]['estado']); ?></td>
                                                            <td>
                                                                <div class="progress progress-lg">
                                                                    <div class="progress-bar progress-bar-danger linevertical"
                                                                        style="width: <?php echo round($arr[$i]['porcentaje_ejecucion'], 0); ?>%">
                                                                        <?php echo round($arr[$i]['porcentaje_ejecucion'], 0); ?>%
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-danger"><?php echo round($arr[$i]['porcentaje_ejecucion'], 0); ?>%</span>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>                             
            </div>
            <?php include './admin/include/footer.php'; ?>
        </div>

        <?php include 'admin/include/gerenic_script.php'; ?>

        <!-- Required Js -->
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>
        <?php include 'admin/include/scriptsgober360.php'; ?>
        <?php include './admin/include/generic_dataTables.php'; ?>

</body>

</html>