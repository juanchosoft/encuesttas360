<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Ejes.php';
include './admin/classes/Configuracion_Puntaje.php';

// Permisos
$view = SessionData::getPermission(67);
$create = SessionData::getPermission(68);
$edit = SessionData::getPermission(69);
if (!$view) {
    require 'permiso_denegado.php';
}
$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if(!$isAdmin){
    require 'permiso_denegado.php';
}

// Información de Factores
$arr = Configuracion_Puntaje::getAll(null);
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
$modulo = 'Configuracion Puntajes';
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
    <style>
        /* Estilos del select */
        select {
            padding: 10px;
            font-size: 16px;
        }

        .color-option {
            display: flex;
            align-items: center;
        }

        .color-box {
            width: 280px;
            height: 40px;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            border: 1px solid #fff;
        }
    </style>
      <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                    <h4 class="text-body mb-0 d-flex align-items-center">
                      <i class="fas fa-list-ol me-2" style="color: #3e465b !important;font-size: 1.3rem !important;"></i>Configuración Puntajes
                    </h4>
                  </div>
                </div>
              </div> 
              <div class="card-body p-0">
                <div class="p-4 code-to-copy">

                  <form class="row g-3 mb-6" id="formupuntajes" role="form" autocomplete="false">
                  <input type="hidden" name="op" id="op" />
                  <input type="hidden" name="idPuntaje" id="idPuntaje" />
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="ejeId" name="ejeId"
                            onchange="PUNTAJES.getPilarByEjeId();">
                            <?php echo $optionEjes; ?>
                        </select>
                        <label for="Eje">Eje<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="pilarId" name="pilarId"></select>
                        <label for="inputState">Pilar<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="tipo_medicion" name="tipo_medicion" ?>
                            <option selected>Seleccione</option>
                            <option value="Cantidad">Cantidad</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                            <option value="Creación">Creación</option>
                        </select>
                        <label  for="">Tipo Medición<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating">
                        <input type="text"class="form-control"  id="desde" name="desde">
                        <label for="">Desde<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-floating">
                        <input type="text" autocomplete="new-password" class="form-control"  id="hasta" name="hasta">
                        <label for="">Hasta<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="color" name="color" onchange="updateColorBox()">
                            <option selected>Seleccione</option>
                            <option value="#cd162c">Rojo</option>
                            <option value="#cd7d16">Naranja</option>
                            <option value="#dbd509">Amarillo</option>
                            <option value="#2774f1">Azul</option>
                            <option value="#62af0a">Verde</option>
                        </select>
                        <label for="">Color<span class="text-danger">*</span></label>
                      </div>
                    </div>
                   
                      <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                          <button type="button" onclick="UTIL.clearForm('formusuarios');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                        </div>
                        <?php if ($create && $edit): ?>
                        <div class="col-auto">
                          <button class="btn btn-primary px-5" type="button" onclick="PUNTAJES.save();">Guardar</button>
                        </div>
                        <?php endif; ?>
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
                            <tr>
                                <th>Editar</th>
                                <th>Item</th>
                                <th>Eje</th>
                                <th>Pilar</th>
                                <th>Tipo medición</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Color</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                            <?php if ($isvalid && count($arr) > 0): ?>
                                <?php foreach ($arr as $item): ?>
                                    <tr>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                                onclick="PUNTAJES.edit(<?= htmlspecialchars($item['id']) ?>)">
                                                <i class="uil uil-edit"></i>
                                            </button>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?> </td>
                                        <td><?php echo htmlspecialchars($item['eje'], ENT_QUOTES, 'UTF-8'); ?> </td>
                                        <td><?php echo htmlspecialchars($item['pilar'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['tipo_medicion'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['rango_desde'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['rango_hasta'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td
                                            style="background-color: <?php echo htmlspecialchars($item['color'], ENT_QUOTES, 'UTF-8'); ?>;">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <!-- MODAL DE PERMISOS -->
                    <div class="modal fade" id="myModalPermisos" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalPermisosLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                        <div class="modal-content">

                          <!-- Header -->
                          <div class="modal-header bg-primary justify-content-between">
                            <h5 class="modal-title text-white" id="modalPermisosLabel">Permisos de Usuario</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close" onclick="UTIL.clearForm('formpermission');">
                              <span class="fas fa-times fs-9 text-white"></span>
                            </button>
                          </div>

                          <!-- Body con scroll interno -->
                          <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                            <form id="formpermission" autocomplete="off">
                              <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                  <thead class="bg-light">
                                    <tr>
                                      <th style="width: 50px;">
                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox" id="check_permisos" onchange="PERMISOS.checkAll();">
                                          <label class="form-check-label" for="check_permisos"></label>
                                        </div>
                                      </th>
                                      <th>Permisos</th>
                                    </tr>
                                  </thead>
                                  <tbody id="permission">

                                  </tbody>
                                </table>
                              </div>
                            </form>
                          </div>

                          <!-- Footer -->
                          <div class="modal-footer">
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cerrar</button>
                            <button class="btn btn-success" type="button" onclick="PERMISOS.savepermission();">Asignar</button>
                          </div>

                        </div>
                      </div>
                    </div>
        <?php
        include './admin/include/footer.php';
        ?>
      </div>
    </main>
            <script>
                // Actualiza el cuadro de color y el texto según la opción seleccionada
                function updateColorBox() {
                    const select = document.getElementById('color');
                    const colorBox = document.getElementById('colorBox');
                    const colorText = document.getElementById('colorText');
                    // Obtén el color y el texto seleccionados
                    const selectedOption = select.options[select.selectedIndex];
                    const color = selectedOption.value;
                    const text = selectedOption.text;
                    // Aplica el color al cuadro y actualiza el texto
                    colorBox.style.backgroundColor = color;
                    // colorText.textContent = text;
                }
                // Establece el color inicial
                updateColorBox();
            </script>

            <!-- Warning Section Ends -->
            <?php include 'admin/include/gerenic_script.php'; ?>
            <!-- Required Js -->
            <script src="assets/js/vendor-all.min.js"></script>
            <script src="assets/js/plugins/bootstrap.min.js"></script>
            <script src="assets/js/pcoded.min.js"></script>
            <?php include './admin/include/generic_dataTables.php'; ?>
            <script type="text/javascript" src="admin/js/conf_puntajes.js"></script>
            <script>
                setTimeout(function() {
                    PUNTAJES.getPilarByEjeId();
                }, 1000);
            </script>
              <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>