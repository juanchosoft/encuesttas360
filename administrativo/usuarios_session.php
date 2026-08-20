<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';

// Permisos
$view = SessionData::getPermission(1);
$create = SessionData::getPermission(2);
$edit = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);
if (!$view) {
    require 'permiso_denegado.php';
}

$arr = Usuario::getAllInicioSesion(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Usuarios del sistema';
?>

<body>

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
<?php
include './admin/include/navbar.php';
?>
    <?php
include './admin/include/header.php';
?>
<!-- ===============================================-->
<!--  CONTENT-->
<!-- ===============================================-->
  <div class="content">
    <h2 class="mb-2">Sesión de Usuarios</h2>
    <h5 class="text-body-tertiary fw-semibold">Aquí encontrarás toda la información sobre los inicios de sesión de Usuarios</h5>
    <div class="p-4 code-to-copy">
      <div class="table-responsive">
        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
            <thead>
              <tr>
                <th>Item</th>
                <th>Fecha</th>
                <th>Nickname</th>
                <th>Usuario</th>
                <th>Ip</th>
                <th>Navegdor</th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if ($isvalid && !empty($arr)): ?>
                  <?php foreach ($arr as $item): ?>
                      <tr>
                          <td style="text-align: center;" ><?= htmlspecialchars($item['id']) ?></td>
                          <td><?= htmlspecialchars($item['dtcreate']) ?></td>
                          <td><?= htmlspecialchars($item['nickname']) ?></td>
                          <td><?= htmlspecialchars($item['nombre']. " " . $item['apellido']) ?></td>
                          <td><?= htmlspecialchars($item['ip']) ?></td>
                          <td><?= htmlspecialchars($item['navegador']) ?></td>
                      </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="9" class="text-center">No se encontraron registros.</td>
                  </tr>
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
    </div>  

    <?php include './admin/include/footer.php'; ?>
  </div>
  
  </main>

    <!-- Warning Section Ends -->
    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>