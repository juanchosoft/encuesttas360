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

include './admin/classes/Secretarias.php';
$modulo = 'Dashboard Secretarías';
// Informacion de los proyectos en ejecucion por seretaria Id
$responseDashboardSecretariaGraficas = Secretarias::getDashboardSecretariaGraficas(NULL);
$responseGrafica =  $responseDashboardSecretariaGraficas['output']['response'];

// Hacienda 
$responseDashboardSecretariaGraficasHacienda = Secretarias::getDashboardSecretariaGraficasHacienda(NULL);
$responseGraficaHacienda =  $responseDashboardSecretariaGraficasHacienda['output']['response'];
?>
<script>
    const secretariasData = <?= json_encode($responseGrafica) ?>;
    const secretariasDataHacienda = <?= json_encode($responseGraficaHacienda) ?>;
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">


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
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">



                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div style="gap:5px" class="card-header d-flex justify-content-center align-items-center">
                            <i class="bi bi-bar-chart-fill text-primary me-2" style="font-size: 1.6rem;"></i>
                            <h4 class="mb-0 fw-bold" style="font-size: 1.5rem;">DashBoard Secretarías</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($responseGrafica as $item): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <!-- Título grande -->
                                            <h4 class="fw-bold mb-1" style="font-size: 1.4rem;">
                                                <?= htmlspecialchars($item['secretaria']) ?>
                                            </h4>

                                            <!-- Botón pequeño con estilo de cuadrito y texto verde -->
                                            <?php if (!empty($item['url'])): ?>
                                                <a href="<?= htmlspecialchars($item['url']) ?>" class="btn btn-outline-success btn-sm mt-1">
                                                    <i class="bi bi-arrow-right-circle"></i> Ver más
                                                </a>
                                            <?php endif; ?>

                                            <!-- Última actualización -->
                                            <?php if (!empty($item['ultima_fecha'])): ?>
                                                <div class="d-flex align-items-center justify-content-center mt-2" style="gap: 5px;">
                                                    <i class="bi bi-calendar-check" style="font-size: 0.9rem; color: #000;"></i>
                                                    <span class="ms-2 text-dark" style="font-size: 0.75rem;">
                                                        Última actualización: <strong><?= htmlspecialchars($item['ultima_fecha']) ?></strong>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body">
                                            <div id="chart_<?= $item['secretaria_id'] ?>" style="width:100%"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <!-- Título grande -->
                                        <h4 class="fw-bold mb-1" style="font-size: 1.5rem;">Hacienda</h4>

                                        <!-- Botón Ver más -->
                                        <a href="hacienda.php?depto_id=21" class="btn btn-outline-success btn-sm mt-2">
                                            <i class="bi bi-arrow-right-circle"></i> Ver más
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="grafica_cantidad" style="width:100%; height: 450px;"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="grafica_valores" style="width:100%; height: 450px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            </div> <!-- end row -->
                        </div> <!-- end card-body -->
                    </div>
                </div>
            </div>

        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script type="text/javascript" src="admin/js/dash_secretarias.js"></script>

    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
</body>

</html>