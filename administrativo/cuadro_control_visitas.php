<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
//Permisos
$view = SessionData::getPermission(1);
$create = SessionData::getPermission(1);
$edit = SessionData::getPermission(1);
//Validación
if (!$view) {
    require 'permiso_denegado.php';
}

include './admin/classes/Visitas.php';


//Información de Vistas
$arr = Visitas::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Registro Visitas';

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

<h2 class="mb-2">Cuadro Control Visitas</h2>
    <h5 class="text-body-tertiary fw-semibold">Aquí encontrarás toda la información sobre visitas</h5>

              <div style="margin-top:20px" class="table-responsive">
                    <div class="table-responsive">
                        <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                          <thead>
                            <tr class="border-1">
                                <th>Id</th>
                                <th>Ver</th>
                                <th>Foto</th>
                                <th>Fecha</th>
                                <th>SubRegion</th>
                                <th>Municipio</th>
                                <th>Motivo Visita</th>
                            </tr>
                          </thead>
                          <tbody class="list">
                          <?php if ($isvalid): ?>
                            <?php
                            $imgBasePath = "assets/img/admin/";
                            foreach ($arr as $item):
                                $img = !empty($item["img"]) ? $imgBasePath . htmlspecialchars($item["img"]) : 'dist/img/logorelsinf.png';
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                                    <td>
                                    <form action="reporte_visita.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="reporte" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                            <!--<?php if ($edit): ?>
                                            <button class="btn btn-sm btn-primary editar-informacion"
                                                title="Editar" data-toggle="modal"
                                                data-target="#modalEditarInformacion"
                                                data-id="<?= htmlspecialchars($item['id']) ?>"
                                                data-longitud="<?= htmlspecialchars($item['longitud']) ?>"
                                                data-latitud="<?= htmlspecialchars($item['latitud']) ?>"
                                                data-fecha="<?= htmlspecialchars($item['dtcreate']) ?>"
                                                data-departamento="<?= htmlspecialchars($item['departamento']) ?>"
                                                data-municipio="<?= htmlspecialchars($item['municipio']) ?>"
                                                data-vereda="<?= htmlspecialchars($item['vereda']) ?>"
                                                data-factor="<?= htmlspecialchars($item['factor']) ?>"
                                                data-valor="<?= htmlspecialchars($item['valor']) ?>">
                                                <i class="feather icon-edit"></i>
                                            </button>
                                        <?php endif; ?> -->

                                    </form>
                                    </td>
                                    <td class="text-primary">
                                        <img 
                                            width="60" 
                                            height="60" 
                                            src="<?php echo $img; ?>" 
                                            alt="Imagen líder" 
                                            data-toggle="modal" 
                                            data-target="#imageModal<?php echo $item['id']; ?>" 
                                            style="cursor: pointer;">
                                        
                                        <!-- Modal -->
                                        <div class="modal fade" id="imageModal<?php echo $item['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel<?php echo $item['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <h5 class="modal-title" id="imageModalLabel<?php echo $item['id']; ?>">
                                                        Foto del municipio de <?php echo htmlspecialchars($item['municipio']); ?>, provincia de <?php echo htmlspecialchars($item['provincia']); ?>
                                                    </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="<?php echo $img; ?>" alt="Imagen líder" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td><?php echo htmlspecialchars($item['date']); ?></td>
                                    <td><?php echo htmlspecialchars($item['provincia']); ?></td>
                                    <td><?php echo htmlspecialchars($item['municipio']); ?></td>
                                    <td><?php echo htmlspecialchars($item['compromisos']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                      <!-- <div class="d-flex justify-content-center mt-3">
                        <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                        <ul class="mb-0 pagination"></ul>
                        <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                      </div> -->
                    </div>  
      <?php
      include './admin/include/footer.php';
      ?>
    </div>

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/detalle_visitas.js"></script>
    
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>