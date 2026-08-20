<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Ministerios.php';

// Permisos
$view = SessionData::getPermission(1);
$create = SessionData::getPermission(2);
$edit = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);
if (!$view) {
    require 'permiso_denegado.php';
}
$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if(!$isAdmin){
    require 'permiso_denegado.php';
}

//Información de Ministerios
$arr = Ministerios::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Ministerios';
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
                    <h4 class="text-body mb-0 d-flex align-items-center">
                      <i class="fas fa-file-invoice me-2" style="color: #3e465b !important;font-size: 1.3rem !important;"></i> Ingreso y listado de ministerios o entidades
                    </h4>
                  </div>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="p-4 code-to-copy">

                  <form class="row g-3 mb-6" id="formusuarios" role="form" autocomplete="false">
                  <input type="hidden" name="op" id="op" />
                  <input type="hidden" name="id" id="id" />

                    <div class="col-sm-6 col-md-8">
                      <div class="form-floating">
                      <input type="text" class="form-control" id="ministerio" name="ministerio"
                      placeholder="Ingrese nombres" value="" required>
                        <label for="validationCustom03">Nombres del ministerio<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-sm-6 col-md-8">
                      <div class="form-floating">
                        <input type="text" class="form-control" id="ministro" name="ministro"
                        placeholder="Ingrese el nobre del ministro" value="" required>
                        <label for="validationCustom04">Nombre ministro<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-8">
                      <div class="form-floating">
                        <input type="email" class="form-control" id="correo" name="correo"
                        placeholder="Ingrese un formato de email valido" value="" required>
                        <label for="validationCustom05">correo<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <?php if ($create && $edit): ?>
                    <div class="col-12">
                      <label class="form-label">Foto</label>
                      <div class="dropzone dropzone-multiple p-0 mb-5" id="my-awesome-dropzone" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                        <iframe id="ifm" name="ifm" src="upload.php" width="100%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                      </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-12">
                      <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                          <button type="button" onclick="UTIL.clearForm('formusecretarias');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                        </div>
                        <?php if ($create && $edit): ?>
                        <div class="col-auto">
                          <button class="btn btn-primary px-5" type="button" onclick="MINISTERIOS.validateData();">Guardar</button>
                        </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>

<!-- TABLA DE USUARIOS -->
        <div class="p-4 code-to-copy">      
           <div class="table-responsive">
                          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                            <thead>
                            <tr class="border-1">
                                <th>Editar</th>
                                <th>ministerio</th>
                                <th>ministro</th>
                                <th>correo</th>
                                <th>Foto</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                                <?php if ($isvalid && count($arr) > 0): ?>
                                <?php foreach ($arr as $item): ?>
                                <?php
                                        $img = !empty($item['image']) ? "assets/img/admin/" . $item['image'] : 'assets/img/santander.png';
                                        ?>
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                            onclick="MINISTERIOS.editData(<?= htmlspecialchars($item['id']) ?>)">
                                            <i class="uil uil-edit"></i>
                                        </button>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['ministerio'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['ministro'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['correo'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td class="text-primary">
                                        <img width="60" height="60"
                                            src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>"
                                            alt="Imagen" />
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    
        <?php
        include './admin/include/footer.php';
        ?>
      </div>
    </main>

            <!-- Warning Section Ends -->
            <?php include 'admin/include/gerenic_script.php'; ?>
            <!-- Required Js -->
            <script src="assets/js/vendor-all.min.js"></script>
            <script src="assets/js/plugins/bootstrap.min.js"></script>
            <script src="assets/js/pcoded.min.js"></script>
            <script type="text/javascript" src="admin/js/ministerios.js"></script>
            <?php include './admin/include/generic_dataTables.php'; ?>
            <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>