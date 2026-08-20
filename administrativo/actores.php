<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Actores.php';


// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Permisos
$view = SessionData::getPermission(58);
$create = SessionData::getPermission(59);
$edit = SessionData::getPermission(60);

if (!$view) {
    require 'permiso_denegado.php';
}

$arr = Actores::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Actores';
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
                <h4 class="text-body mb-0">Creación de Actores en la Región</h4>
              </div>
            </div>
          </div>
          <!-------- INICIO DE FORMULARIO ACTORES ------->
          <div class="card-body p-0">
            <div class="p-4 code-to-copy">
              <form id="formuactores" class="row g-3 mb-6" role="form" autocomplete="false>
                <input type=" hidden" name="op" id="op" />
              <input type="hidden" name="id" id="id" />

              <div class="col-sm-6 col-md-12">
                <div class="form-floating">
                  <select id="tbl_municipio_id" name="tbl_municipio_id" class="form-select">
                  </select>
                  <label for="pertenece">Alcaldía a:<span class="text-danger mb-1">*</span></label>
                </div>
              </div>

              <div class="col-sm-6 col-md-12">
                <div class="form-floating">
                  <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese un nombre"
                    required />
                  <label for="Email">Nombre<span class="text-danger mb-1">*</span></label>
                </div>
              </div>
              <div class="col-sm-6 col-md-12">
                <div class="form-floating">
                  <select id="pertenece" name="pertenece" class="form-select">
                    <option value="" selected>Seleccione</option>
                    <option value="Empresa Privada">Empresa Privada</option>
                    <option value="Gobernacion">Gobernación</option>
                    <option value="Alcaldia">Alcaldía</option>
                    <option value="Gobierno Nacional">Gobierno Nacional</option>
                    <option value="Policia Nacional">Policía Nacional</option>
                    <option value="Ejercíto Nacional">Ejército Nacional</option>
                  </select>
                  <label for="pertenece">Pertenece a:<span class="text-danger mb-1">*</span></label>
                </div>
              </div>
              <!------- INICIO BOTON CANCELAR Y GUARDAR  ------->
              <div class="col-12">
                <div class="row g-3 justify-content-end">
                  <div class="col-auto">
                    <button type="button" onclick="UTIL.clearForm('formuactores');"
                      class="btn btn-phoenix-secondary px-5">Cancelar</button>
                  </div>
                  <?php if ($create && $edit): ?>
                  <div class="col-auto">
                    <button class="btn btn-primary px-5" type="button"
                      onclick="ACTORES.validateData();">Guardar</button>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              <!----- FIN BOTON CANCELAR Y GUARDAR  ------>
              </form>
            </div>
            <!---------- FIN DE FORMULARIO ACTORES --------------->
          </div>
        </div>
        <div>
          <div>
            <!-- LISTADO ACTORES -->
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="mb-9 m-4">
                <div class="row g-3 mb-4">
                  <div class="col-auto">
                    <h2 class="mb-0">Listado Actores</h2>
                    <div class="scrollbar overflow-hidden-y">
                      <div class="btn-group position-static" role="group">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- TABLA DE ACTORES -->
                <div
                  class="px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                  <!-- Solo mostrar la tabla si hay registros -->
                  <?php if ($isvalid && count($arr) > 0): ?>
                  <div class="p-4 code-to-copy">
                    <div class="table-responsive">
                      <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                        <thead>
                          <tr>
                            <th>Editar</th>
                            <th>Nombre</th>
                            <th>Pertenece a</th>
                            <th>Alcadía</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($arr as $item): ?>
                          <tr>
                            <td>
                              <button type="button" class="btn btn-sm btn-primary" style="margin-left: 1rem;"
                                title="Editar" onclick="ACTORES.editData(<?= htmlspecialchars($item['id']) ?>)">
                                <span data-feather="edit"></span>
                              </button>
                            </td>
                            <td><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['pertenece'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['municipio'], ENT_QUOTES, 'UTF-8'); ?></td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <?php else: ?>
                <!-- Mostrar el mensaje cuando no hay registros -->
                <p class="text-center">No se encontraron registros</p>
                <?php endif; ?>
              </div>
            </div>
            <?php
  include './admin/include/footer.php';
  ?>
            <?php include 'admin/include/gerenic_script.php'; ?>
            <script type="text/javascript" src="admin/js/departamento.js"></script>

            <script src="assets/js/vendor-all.min.js"></script>
            <script src="assets/js/plugins/bootstrap.min.js"></script>
            <script src="assets/js/pcoded.min.js"></script>
            <?php include './admin/include/generic_dataTables.php'; ?>
            <script type="text/javascript" src="admin/js/actores.js"></script>
            <?php include 'admin/include/scriptsgober360.php'; ?>
            <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

    <script>
        setTimeout(function() {
            DEPARTAMENTO.getMunicipiosConDepartamentoPrincipal();
        }, 1000);
    </script>

</html>