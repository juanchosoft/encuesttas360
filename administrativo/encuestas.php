<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Encuesta.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

$view = SessionData::getPermission(1);
$create = SessionData::getPermission(2);
$edit = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);
if (!$view) {
  require 'permiso_denegado.php';
}
$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if (!$isAdmin) {
  require 'permiso_denegado.php';
}

//Información de Encuesta
$arr = Encuesta::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Encuestas';
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
                  <i class="fas fa-vote-yea me-2" style="color: #3e465b !important;font-size: 1.3rem !important;"></i>
                  <span id="spanModulo">Ingreso y listado de Encuestas</span> 
                  <hr>
                  <span id="spanEncuesta"></span>
                </h4>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="p-4 code-to-copy">

              <form class="row g-3 mb-6" id="formencuesta" role="form" autocomplete="false">
                <input type="hidden" name="op" id="op" />
                <input type="hidden" name="idEncuesta" id="idEncuesta" />

                <div class="col-sm-12 col-md-6">
                  <div class="form-floating">
                    <input class="form-control" name="fecha_realizacion" id="fecha_realizacion" type="text"
                      placeholder="" required="true">
                    <label for="validationCustom01">Fecha Realización<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating">
                    <input class="form-control" name="fecha_publicacion" id="fecha_publicacion" type="text"
                      placeholder="" required="true">
                    <label for="validationCustom01">Fecha de publicación<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating">
                    <input class="form-control" name="fecha_de_recibo" id="fecha_de_recibo" type="text" placeholder=""
                      required="true">
                    <label for="validationCustom01">Fecha de recibo<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><input type="text" class="form-control" id="fuente_financiamiento"
                      name="fuente_financiamiento" placeholder="Fuente de financiamiento" value="" required><label
                      for="fuente_financiamiento">Fuente de financiamiento<span class="text-danger">*</span></label>
                  </div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><input type="text" class="form-control" id="tema" name="tema"
                      placeholder="Ingrese el tema" value="" required><label for="tema">Tema<span
                        class="text-danger">*</span></label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><input type="text" class="form-control" id="tamano_de_la_muestra"
                      name="tamano_de_la_muestra" placeholder="Ingrese el tamaño de la muestra" value="" required><label
                      for="tamano_de_la_muestra">Tamaño de la muestra<span class="text-danger">*</span></label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><input type="text" class="form-control" id="cumple_con_reglamentacion"
                      name="cumple_con_reglamentacion" placeholder="Ingrese si las cumple con la reglamentación"
                      value="" required><label for="cumple_con_reglamentacion">Cumple con la reglamentación<span
                        class="text-danger">*</span></label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><select class="form-select" id="tipo_muestra" name="tipo_muestra" required>
                      <option value="" selected disabled>Tipo de muestra</option>
                      <option value="Probabilístico">Probabilístico</option>
                      <option value="otro">Otro</option>
                    </select><label for="tipo_muestra">Tipo de muestra<span class="text-danger">*</span></label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><select class="form-select" id="tecnica_de_recoleccion"
                      name="tecnica_de_recoleccion" required>
                      <option value="" selected disabled>Seleccione la técnica de recolección</option>
                      <option value="encuesta telefonica">Encuesta telefónica</option>
                      <option value="encuesta web">Encuesta web</option>
                      <option value="otro">Otro</option>
                    </select><label for="tecnica_de_recoleccion">Tecnica de recolección<span
                        class="text-danger">*</span></label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><input type="link" class="form-control" id="enlace_documento"
                      name="enlace_documento" placeholder="Ingrese enlace del documento" value=""><label
                      for="enlace_documento">Enlace documento</label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating"><select class="form-select" id="habilitado" name="habilitado" required>
                      <option value="si">Si</option>
                      <option value="no">No</option>
                    </select><label for="habilitado">Habilitado<span class="text-danger">*</span></label></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div class="form-floating">
                    <textarea class="form-control" id="observaciones" name="observaciones"
                      placeholder="Ingrese las observaciones" style="height: 50px;" required></textarea>
                    <label for="observaciones">Observaciones <span class="text-danger">*</span></label>
                  </div>
                </div>

                <div class="col-12">
                  <div class="row g-3 justify-end">
                    <div class="col-auto">
                      <button type="button" onclick="UTIL.clearForm('formencuesta');"
                        class="btn btn-phoenix-secondary px-5">Cancelar</button>
                    </div>
                    <div class="col-auto">
                      <button class="btn btn-primary px-5" type="button"
                        onclick="ENCUESTA.validateData();">Guardar</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla con los datos de las encuestas -->
    <div class="p-4">
      <div class="table-responsive">
        <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
          <thead>
            <tr class="border-1">
              <th>Acciones</th>
              <th>N°</th>
              <th>Habilitado</th>
              <th>Fecha de Realización</th>
              <th>Fecha de publicación</th>
              <th>Fecha de recibo</th>
              <th>Fuente de financiamiento</th>
              <th>Tema</th>
              <th>Tamaño de la muestra</th>
              <th>Observaciones</th>
              <th>Cumple con la reglamentación</th>
              <th>Tipo de muestra</th>
              <th>Tecnica de recolección</th>
              <th>Enlace documento</th>
            </tr>
          </thead>
          <tbody class="list">
            <?php if ($isvalid && count($arr) > 0): ?>
            <?php foreach ($arr as $item): ?>
            <tr>
              <td>
                <button type="button" class="btn btn-sm btn-primary" title="Editar"
                  onclick="ENCUESTA.editData(<?= htmlspecialchars($item['id']) ?>)">
                  <i class="uil uil-edit"></i>
                </button>
              </td>
              <td><?= htmlspecialchars($item['id']) ?></td>
              <td><?= htmlspecialchars($item['habilitado']) ?></td>
              <td><?= htmlspecialchars($item['fecha_realizacion']) ?></td>
              <td><?= htmlspecialchars($item['fecha_publicacion']) ?></td>
              <td><?= htmlspecialchars($item['fecha_de_recibo']) ?></td>
              <td><?= htmlspecialchars($item['fuente_financiamiento']) ?></td>
              <td><?= htmlspecialchars($item['tema']) ?></td>
              <td><?= htmlspecialchars($item['tamano_de_la_muestra']) ?></td>
              <td><?= htmlspecialchars($item['observaciones']) ?></td>
              <td><?= htmlspecialchars($item['cumple_con_reglamentacion']) ?></td>
              <td><?= htmlspecialchars($item['tipo_muestra']) ?></td>
              <td><?= htmlspecialchars($item['tecnica_de_recoleccion']) ?></td>
              <td>
                <a href="<?= htmlspecialchars($item['enlace_documento']) ?>" target="_blank">
                  Documento Link
                </a>
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
  <script type="text/javascript" src="admin/js/encuesta.js"></script>
  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>
  <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>