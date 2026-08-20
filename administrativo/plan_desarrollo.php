<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Desarrollo.php';
include './admin/classes/Secretarias.php';

$modulo = 'Metas Plan de Desarrollo';

$arr = [];
if (isset($_SESSION['session_user']['secretaria'])) {
    $id = intval($_SESSION['session_user']['secretaria']);
    // Información de secretarias
    $arr = Desarrollo::getAll(array('secretaria_id' => $id));
} else {
    $arr = Desarrollo::getAll(null);
}
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$arrData = $arr;


//Información de Secretarias
$arrSecretarias = Secretarias::getAll(null);
$isvalidProv = $arrSecretarias['output']['valid'];
$arrSecretarias = $arrSecretarias['output']['response'];
$option = '<option value="seleccione">Seleccione...</option>';
foreach ($arrSecretarias as $val) {
    $option .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . "-" . $val['secretaria'] . "</option>";
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
<div class="content">

<div class="p-4 code-to-copy">
    <h2 class="mb-2">Metas Plan de Desarrollo</h2>
    <h5 class="text-body-tertiary fw-semibold">Aquí encontrarás toda la información sobre el plan de desarrollo</h5>
    
              <div style="margin-top:20px" class="table-responsive">
                    <div class="table-responsive">
                        <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                          <thead>
                          <tr>
                            <th>ID</th>
                            <th>EJE ESTRATÉGICO</th>
                           
                            <th>PRODUCTO, BIEN O SERVICIO PDD</th>
                            <th>SECRETARIA RESPONSABLE</th>
                            <th>DIRECCIÓN RESPONSABLE</th>
                            <th>2024</th>
                            <th>Avance 2025</th>
                            <th>Editar Avance</th>
                            <th>2025</th>
                        </tr>
                          </thead>
                          <tbody class="list">
                          <?php if ($isvalid): ?>
                                <?php foreach ($arr as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['id']); ?></td>
                                    <td><?= htmlspecialchars($item['eje_estrategico']); ?></td>
                                   
                                    <td style="font-size: 10px; line-height: 1.1; max-width: 120px; white-space: normal; word-wrap: break-word;"><?= htmlspecialchars($item['producto_servicio_pdd']); ?></td>
                                    <td><?= htmlspecialchars($item['secretaria']); ?></td>
                                    <td><?= htmlspecialchars($item['direccion_resp']); ?></td>
                                    <td><?= htmlspecialchars($item['ps2024']); ?></td>
                                    <td><?= htmlspecialchars($item['avance_2025']); ?></td>
                                    <td>
                                        <input onKeyPress="return soloNumeros(event);" type="text"
                                            class="form-control"
                                            id="avance_2025_<?= htmlspecialchars($item['id']); ?>"
                                            name="avance_2025_<?= htmlspecialchars($item['id']); ?>"
                                            onblur="DESARROLLO.updateAvance(<?= htmlspecialchars($item['id']); ?>)">
                                    </td>
                                    <td><?= htmlspecialchars($item['ps2025']); ?></td>
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
                  </div>       
      <?php
      include './admin/include/footer.php';
      ?>
    </div>

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?> 
    <script type="text/javascript" src="admin/js/plan_desarrollo.js"></script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>