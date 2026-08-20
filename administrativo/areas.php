<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Area.php';
include './admin/classes/Pilar.php';

// Permisos
$view = SessionData::getPermission(76);
$create = SessionData::getPermission(77);
$edit = SessionData::getPermission(78);

if (!$view) {
    require 'permiso_denegado.php';
}

$arr = Area::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];


//Información de Pilar
$arrPilar = Pilar::getAll(null);
$isvalidPilar = $arrPilar['output']['valid'];
$arrPilar = $arrPilar['output']['response'];
$optionPilar = '<option value="seleccione">Seleccione...</option>';
foreach ($arrPilar as $val) {
    $optionPilar .= "<option value='" . $val['id'] . "'>" . $val['nombre'] . "</option>";
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
    <!-- [ Main Content ] start -->
 <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                        <h4 class="text-body mb-0 d-flex align-items-center">Creación de Áreas</h4>
                  </div>
                </div>
              </div>
              <!-- INICIO DE FORMULARIO DE AREAS -->
                  <div class="card-body p-0">
                    <div class="p-4 code-to-copy">
                      <form id="formarea" role="form" class="row g-3 mb-6" role="form" autocomplete="false">
                        <input type="hidden" name="op" id="op" />
                        <input type="hidden" name="id" id="id" />
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese un nombre" required />
                              <label for="nombre">Nombre <span class="text-danger mb-1">*</span></label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                              <select class="form-select" id="pilarId" name="pilarId" aria-label="Default select example" required>
                                <?php echo $optionPilar; ?>
                              </select>
                              <label for="pilarId">Pilar <span class="text-danger mb-1">*</span></label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                          <div class="form-floating">
                            <select class="form-select" id="enable" name="enable">
                              <option value="si">Sí</option>
                              <option value="no">No</option>
                            </select>
                            <label for="validationCustom05">Habilitado</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-12">
                          <div class="form-floating">
                            <input type="text" class="form-control" id="descripcion" name="descripcion"
                            placeholder="Ingrese una descripcion">
                            <label for="descripcion">Descripcion</label>
                          </div>
                        </div>
                        <div class="col-12">
                          <label for="inputState">Foto</label>
                          <div class="dropzone dropzone-multiple p-0 mb-5" id="my-awesome-dropzone" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                            <iframe id='ifm1' name='ifm' src="upload.php" width="100%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                          </div>
                        </div>
<!-- INICIO BOTON CANCELAR Y GUARDAR  -->
                        <div class="col-12">
                          <div class="row g-3 justify-content-end">
                            <div class="col-auto">
                              <button type="button" onclick="UTIL.clearForm('formarea');" class="btn btn-phoenix-secondary px-5">
                                Cancelar
                              </button>
                            </div>
                            <div class="col-auto">
                              <button class="btn btn-primary px-5" type="button" onclick="AREAS.save();">
                                Guardar
                              </button>
                            </div>
                          </div>
                        </div>
<!-- FIN BOTON CANCELAR Y GUARDAR  -->
                  </div>
                </div>
              </div>
              <!-- FIN DE FORMULARIO DE AREAS -->
          <div>
        <div>
  <!-- LISTADO DE AREAS -->
      <div class="card shadow-none border my-4" data-component-card="data-component-card">
        <div class="mb-9 m-4">
          <div class="row g-3 mb-4">
            <div class="col-auto">
              <h2 class="mb-0">Listado áreas</h2>
            </div>
          </div>

  <!-- TABLA DE AREAS -->
        <div class="p-4 code-to-copy">
          <div class="table-responsive">
              <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                <thead>
                    <tr class="border-1">
                        <th>Editar</th>
                        <th>Icono</th>
                        <th>Nombre</th>
                        <th>Pilar</th>
                        <th>Descripción</th>
                        <th>Habilitado</th>
                    </tr>
                </thead>
                <tbody class="list">
                    <?php if ($isvalid && !empty($arr)): ?>
                        <?php foreach ($arr as $item): ?>
                            <?php
                            $img = !empty($item["icono"]) ?  htmlspecialchars($item["icono"]) : 'assets/iconos/gobierno.png';
                            ?>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                        onclick="AREAS.editData(<?= htmlspecialchars($item['id']) ?>)">
                                        <i class="uil uil-edit"></i>
                                    </button>
                                </td>
                                <td class="text-primary">
                                    <img width="40" height="40" src="<?= $img ?>" alt="Imagen">
                                </td>
                                <td><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['tbl_pilar_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['enable'], ENT_QUOTES, 'UTF-8'); ?></td>
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
      </div>
          <!-- Footer -->
                        </div>
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
    <script type="text/javascript" src="admin/js/areas.js"></script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>