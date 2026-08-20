<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';

//Permisos
$view = SessionData::getPermission(14);
$create = SessionData::getPermission(15);
$edit = SessionData::getPermission(16);

//Validación
if (!$view) {
    require 'permiso_denegado.php';
}

include './admin/classes/Departamento.php';
include './admin/classes/Secretarias.php';


$modulo = 'Registro Visitas';

// Información de secretarias
$arrSec = Secretarias::getAll(null);
$isvalid = $arrSec['output']['valid'];
$arrSec = $arrSec['output']['response'];
$optionSec = "";
foreach ($arrSec as $val) {
    $optionSec .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . " </option>";
}

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}


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
                  <h4 class="text-body mb-0 d-flex align-items-center" >
                  <i style="color:black !important;font-size: 1.9rem !important;" class="uil uil-user me-2 fs-4 text-primary"></i> Registro Visitas
                  </div>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="p-4 code-to-copy">

                  <form class="row g-3 mb-6" id="formusuarios" role="form" autocomplete="false">
                    <input type="hidden" name="op" id="op" />
                    <input type="hidden" name="id" id="id" />
                   
                    <div class="col-sm-6 col-md-4">
                 
                    <div class="form-floating">
                    <input class="form-control datetimepicker flatpickr-input" name="date" id="date" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                        <label for="validationCustom01">Fecha<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="Seleccione">Seleccione</option>
                            <option value="Visita">Visita</option>
                            <option value="Compromiso">Compromiso</option>
                        </select>
                        <label for="validationCustom01">Tipo De Registro</label>
                      </div>
                    </div>


                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" style="width: 100%;" style="display: none;" onchange="DEPARTAMENTO.getMunicipios();" id="tbl_departamento_id" name="tbl_departamento_id"> <?php echo $optionDep; ?>
                        </select>
                        <label for="validationCustom01">Departamento<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <!-- INICIO DE SELECT PROVINCIAS -->
                    <div class="col-sm-6 col-md-4">
                        <div class="form-floating">
                          <select class="form-select" id="subregion" name="subregion">
                            <option value="Seleccione">Seleccione</option>
                            <option value="Alto Putumayo">Alto Putumayo</option>
                            <option value="Medio Putumayo">Medio Putumayo</option>
                            <option value="Bajo Putumayo">Bajo Putumayo</option>
                          </select>
                          <label for="subregion">Subregión</label>
                        </div>

                      </div>

                    <!-- FIN DE SELECT PROVINCIAS -->
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" style="width: 100%;" onchange="DEPARTAMENTO.getVeredasByMunicipioId();" id="tbl_municipio_id" name="tbl_municipio_id"> 
                        </select>
                        <label for="validationCustom01">Municipio<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="entidad" name="entidad">
                            <option value="Seleccione">Seleccione</option>
                            <option value="Reunión">Reunión</option>
                            <option value="en Trámite">Ruta 25</option>
                            <option value="Brigada Civico Social">Brigada Civico Social</option>
                            <option value="Consejo de Seguridad">Concejo de Seguridad</option>
                            <option value="Concejos y/o Juntas Directivas">Concejos y/o Juntas Directivas</option>
                            <option value="Inauguración de festividades">Inauguración de festividades</option>
                            <option value="Seguimiento de Obras">Seguimiento de Obras</option>
                            <option value="Seguimiento de Planes, Programas y Proyectos">Seguimiento de Planes, Programas y Proyectos</option>
                        </select>
                        <label for="validationCustom01">Tipo Visita</label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="tbl_secretarias_id" name="tbl_secretarias_id"><?php echo $optionSec; ?>
                        </select>
                        <label for="validationCustom05">Secretaria o Dependencia Encargada</label>
                      </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <input class="form-control"  id="cargo" name="cargo" placeholder="Ingrese el cargo de la persona que convoco" value="">
                        <label for="validationCustom01">Consecuencias</label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="beneficiario" name="beneficiario">
                            <option value="Seleccione">Seleccione</option>
                            <option value="Sin Cumplir">Sin Cumplir</option>
                            <option value="En Trámite">En Trámite</option>
                            <option value="Cumplido">Cumplido</option>
                        </select>
                        <label for="validationCustom01">Estado</label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                      <input autocomplete="false" type="text" placeholder="Ingrese el motivo de la reunión" class="form-control" id="observaciones" name="observaciones">
                      <label for="validationCustom01">Descripción del Hecho</label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                      <textarea required="" placeholder="Ingrese el compromiso de la reunion" type="text" class="form-control" id="compromisos" name="compromisos"></textarea>
                      <label for="apellido">Acciones Tomadas<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                      <textarea required="" placeholder="Ingrese el compromiso de la reunion" type="text" class="form-control" id="compromisopac" name="compromisopac"></textarea>
                      <label >Compromisos Pactados</label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                      <textarea required="" placeholder="Ingrese el compromiso de la reunion" type="text" class="form-control" id="respuesta" name="respuesta"></textarea>
                      <label >Respuesta al Compromiso<span class="text-danger">*</span></label>
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
                          <button type="button" onclick="UTIL.clearForm('formusuarios');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                        </div>
                        <?php if ($create && $edit): ?>
                        <div class="col-auto">
                          <button class="btn btn-primary px-5" type="button" onclick="VISITAS.validateData();">Guardar</button>
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

                    <?php
        include './admin/include/footer.php';
        ?>
      </div>
    </main>

    <!-- Required Js -->
    <script>
        setTimeout(function() {
            $("#tbl_departamento_id").val('86')
        }, 500);

        setTimeout(function() {
            DEPARTAMENTO.getMunicipios();
        }, 1000);
    </script>
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/detalle_visitas.js"></script>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>


</body>

</html>