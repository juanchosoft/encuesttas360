<?php

include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Ejes.php';
include './admin/classes/Factores.php';
include './admin/classes/Secretarias.php';


// Permisos
$view = SessionData::getPermission(55);
$create = SessionData::getPermission(56);
$edit = SessionData::getPermission(57);

if (!$view) {
    require 'permiso_denegado.php';
}

// Información de Factores
$arr = Factores::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];

//Información de Ejes
$arrEjes = Ejes::getAll(null);
$isvalidEje = $arrEjes['output']['valid'];
$arrEjes = $arrEjes['output']['response'];
$optionEjes = '<option value="seleccione">Seleccione...</option>';
foreach ($arrEjes as $val) {
    $optionEjes .= "<option value='" . $val['id'] . "'>" . $val['nombre'] . "</option>";
}
//Información de Secretarias
$arrSecreatrias = Secretarias::getAll(null);
$isvalidSecre = $arrSecreatrias['output']['valid'];
$arrSecreatrias = $arrSecreatrias['output']['response'];
$optionSecretaria = '<option value="seleccione">Seleccione...</option>';
foreach ($arrSecreatrias as $val) {
    $optionSecretaria .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . "</option>";
}

// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
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
  <!-- <h4 class="text-body mb-0" data-anchor="data-anchor">
                        <i class="uil uil-shield-check me-2"></i> Creación factores
                    </h4> -->
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
                <h4 class="text-body mb-0">
                  <i class="uil uil-shield-check me-2"></i> Creación factores - <?php echo $nombreProyecto; ?>
                  <?php if (!empty($logo)): ?>
                  <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                    class="img-fluid img-thumbnail">
                  <?php endif; ?>
                </h4>
              </div>
            </div>
          </div>
          <!-- INICIO DE FORMULARIO DE AREAS -->
          <div class="card-body p-0">
            <div class="p-4 code-to-copy">
              <form id="formfactores" class="row g-3 mb-6" role="form" autocomplete="false">
                <input type="hidden" name="op" id="op" />
                <input type="hidden" name="id" id="id" />
                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <select class="form-select" id="ejeId" name="ejeId" onchange="INGRESO_FACTORES.getPilarByEjeId();">
                      <?php echo $optionEjes; ?>
                    </select>
                    <label for="ejeId">Eje</label>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <select class="form-select" id="pilarId" name="pilarId"
                      onchange="INGRESO_FACTORES.getAreaByPilarId();"></select>
                    <label for="inputState">Pilar<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <select class="form-select" id="areaId" name="areaId">
                    </select>
                    <label for="inputState">Área</label>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="tipo" name="tipo" aria-describedby="" value="">
                    <label for="inputState">Tipo<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <select class="form-select" id="tipo_medicion" name="tipo_medicion">
                      <option value="Unidad">Unidad</option>
                      <option value="Metros">Metros</option>
                      <option value="Kilometros">Kilometros</option>
                      <option value="Km2">Km2</option>
                      <option value="Hectareas">Hectareas</option>
                      <option value="Porcentaje">Porcentaje</option>
                    </select>
                    <label for="Tipo">Tipo Medición<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="puntaje" onKeyPress="return soloNumeros(event);"
                      name="puntaje" aria-describedby="" value="">
                    <label for="inputState">Puntaje<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>

                <div class="col-sm-6 col-md-6">
                  <div class="form-floating">
                    <select class="form-select" id="secretariaId" name="secretariaId">
                      <?php echo $optionSecretaria; ?>
                    </select>
                    <label for="Tipo">Secretaría<span class="text-danger mb-1">*</span></label>
                  </div>
                </div>

                <div class="col-12">
                  <label for="inputState">Foto</label>
                  <div class="dropzone dropzone-multiple p-0 mb-5" id="my-awesome-dropzone"
                    data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                    <iframe id='ifm1' name='ifm' src="upload.php" width="100%" height="200" scrolling="no"
                      frameborder="0" style="border: none;"></iframe>
                  </div>
                </div>
                <!-- BOTON GUARDAR Y CANCELAR -->
                <div class="col-12">
                  <div class="row g-3 justify-content-end">
                    <div class="col-auto">
                      <button type="button" onclick="UTIL.clearForm('formfactores');"
                        class="btn btn-phoenix-secondary px-5">Cancelar</button>
                    </div>
                    <?php if ($create && $edit): ?>
                    <div class="col-auto">
                      <button class="btn btn-primary px-5" type="button"
                        onclick="INGRESO_FACTORES.save();">Guardar</button>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
              </form>
              <!-- FIN BOTON GUARDAR Y CANCELAR -->
            </div>
          </div>
        </div>
      </div>
      <!-- TABLA FACTORES -->
      <div class="p-4 code-to-copy">
        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0"">
                            <thead>
                              <tr>
                                <th>ITEM</th>
                                <th>EDITAR</th>
                                <th>TIPO</th>
                                <th>ICONO</th>
                                <th>PUNTAJE</th>
                                <th>EJE</th>
                                <th>PILAR</th>
                                <th>ÁREA</th>
                                <th>TIPO MEDICIÓN</th>
                              </tr>
                            </thead>
                              <tbody>
                                <?php if ($isvalid && !empty($arr)): ?>
                                <?php foreach ($arr as $item): ?>
                                <?php
                                  $img = !empty($item["icono"]) ?  htmlspecialchars($item["icono"]) : 'assets/iconos/gobierno.png';
                                    ?>
                                  <tr>
                                  <th><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></th>
                                  <td>
                                    <button type=" button" class="btn btn-sm btn-primary" title="Editar"
            onclick="INGRESO_FACTORES.edit(<?= htmlspecialchars($item['id']) ?>)">
            <span data-feather="edit"></span>
            </button>
            </td>
            <td class="text-primary">
              <img width="40" height="40" src="<?= $img ?>" alt="Imagen">
            </td>
            <td><?php echo htmlspecialchars($item['tipo'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
            <th><?php echo htmlspecialchars($item['puntaje'], ENT_QUOTES, 'UTF-8'); ?></th>
            <td><?php echo htmlspecialchars($item['eje'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($item['pilar'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($item['area'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($item['tipo_medicion'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
              <td colspan="9" class="text-center">No se encontraron registros</td>
            </tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Footer -->
      <?php
        include './admin/include/footer.php';
        ?>
    </div>
    <!-- FIN TABLA FACTORES -->
    <!-- [ Main Content ] end -->

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <?php include './admin/include/generic_dataTables.php'; ?>
    <script type="text/javascript" src="admin/js/ingreso_factores.js"></script>
    <script>
      setTimeout(function() {
        INGRESO_FACTORES.getPilarByEjeId();
      }, 1000);
    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>