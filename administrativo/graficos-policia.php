<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';

// Permisos
/* $view = SessionData::getPermission(1);
$create = SessionData::getPermission(2);
$edit = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);
if (!$view) {
    require 'permiso_denegado.php';
}
 */

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

    <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                  <h4 class="text-body mb-0 d-flex align-items-center" >
                  <i style="color:black !important;font-size: 1.9rem !important;" class="uil uil-user me-2 fs-4 text-primary"></i> Informe gobierno Putumayo
                  </div>
                </div>
              </div> 
              <div class="card-body p-0">
                            <div class="card-body table-border-style">
                                <div class="row mb-3 justify-content-end align-items-end">
                                    <div class="col-md-3">
                                        <label for="categoriaSelect"><strong>Seleccionar categoría:</strong></label>
                                        <select class="form-control" id="categoriaSelect">
                                            <option value="hurtos">Hurtos</option>
                                            <option value="amenazas">Amenazas</option>
                                            <option value="desplazamientos">Desplazamientos</option>
                                            <option value="homicidios">Homicidios</option>
                                            <option value="secuestros">Secuestros</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="loader" class="loader-overlay" style="display: none;">
                                    <div class="spinner"></div>
                                </div>
                                <canvas id="graficoCategoria" width="600" height="300"></canvas>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- 
    <?php include 'admin/include/footer.php'; ?> -->


    <!-- Warning Section Ends -->
    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <script type="text/javascript" src="admin/js/graficos-policia.js"></script>
    <script type="text/javascript" src="./admin/js/datatables/jquery.dataTables.min.js"></script>
    <link href="./admin/js/datatables/jquery.dataTables.min.css" rel="stylesheet" />
<?php include 'admin/include/scriptsgober360.php'; ?>


</body>

</html>