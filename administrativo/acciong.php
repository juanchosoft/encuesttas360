<?php

include './admin/include/head.php';

require './admin/include/generic_classes.php';

//Permisos
$view = SessionData::getPermission(5);
$create = SessionData::getPermission(6);
$edit = SessionData::getPermission(7);
//Validación
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}
include './admin/classes/Acciong.php';

//Información de Acciong
$arr = Acciong::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Acciones Primera Dama';

?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">


  <body>
    <main class="main" id="top">
    <?php
    include './admin/include/navbar.php';
    ?>
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
                      <i class="fas fa-clipboard-list me-2" style="color: #3e465b !important;font-size: 1.3rem !important;"></i> Ingreso y listado de acciones
                    </h4>
                  </div>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="p-4 code-to-copy">

                  <form class="row g-3 mb-6" id="formaccion" role="form" autocomplete="false">
                    <input type="hidden" name="op" id="op" />
                    <input type="hidden" name="id" id="id" />

                    <div class="col-sm-6 col-md-8">
                      <div class="form-floating">
                      <textarea  placeholder="Ingrese el nombre de la acción a desarrollar" type="text" class="form-control" id="accion"
                      name="accion"></textarea>
                      <label for="Text">Tipo de Acción<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                          <button type="button" onclick="UTIL.clearForm('formaccion');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                        </div>
                        <?php if ($create && $edit): ?>
                        <div class="col-auto">
                          <button class="btn btn-primary px-5" type="button" onclick="ACCIONG.savedata();">Guardar</button>
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
                                <th scope="col">Acciones</th>
                                <th scope="col">Id</th>
                                <th scope="col">Acción</th>
                                <th scope="col">Fecha Creación</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                                <?php if ($isvalid && !empty($arr)): ?>
                                <?php foreach ($arr as $item): ?>
                                <tr>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            title="Editar item <?= htmlspecialchars($item['id']); ?>"
                                            onclick="ACCIONG.editData(<?= htmlspecialchars($item['id']); ?>)">
                                            <i class="uil uil-edit"></i>

                                        </button>

                                        <button type="button" class="btn btn-sm btn-danger"
                                            title="Eliminar item <?= htmlspecialchars($item['id']); ?>"
                                            onclick="ACCIONG.deletedata(<?= htmlspecialchars($item['id']); ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                            </button>
                                    </td>
                                    <td><?= htmlspecialchars($item['id']); ?></td>
                                    <td><?= htmlspecialchars($item['accion']); ?></td>
                                    <td><?= htmlspecialchars($item['dtcreate']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay datos disponibles
                                    </td>
                                </tr>
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

    <?php include 'admin/include/gerenic_script.php'; ?>

    <script type="text/javascript" src="admin/js/acciong.js"></script>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>

</body>

</html>