<?php
include './admin/include/head.php';
include './admin/classes/Departamento.php';
include './admin/classes/Factores.php';
include './admin/classes/Actores.php';
require './admin/include/generic_classes.php';

// Permisos
$view = SessionData::getPermission(67);
$create = SessionData::getPermission(68);
$edit = SessionData::getPermission(69);

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de Factores
$arrFactores = Factores::getAll(null);
$isvalid = $arrFactores['output']['valid'];
$arrFactores = $arrFactores['output']['response'];
$optionFactores = '<option value="seleccione">Seleccione...</option>';
foreach ($arrFactores as $val) {
    $optionFactores .= "<option value='" . $val['id'] . "'>" . $val['tipo'] . "</option>";
}
// Información de Actores
$arrActores = Actores::getAll(null);
$isvalid = $arrActores['output']['valid'];
$arrActores = $arrActores['output']['response'];
$optionActores = '<option value="seleccione">Seleccione...</option>';
foreach ($arrActores as $val) {
    $optionActores .= "<option value='" . $val['id'] . "'>" . $val['nombre'] . "</option>";
}

?>

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
                    <i style="color:black !important;font-size: 1.9rem !important;" class="uil uil-user me-2 fs-4 text-primary"></i>  Factores de Inestabilidad Actualización</div>
                  </div>
                </div>
                <!-- INICIO DEL FORMUALRIO ACTUALIZAR INFORMACION -->
                <div class="card-body p-0">
                    <div class="p-4 code-to-copy">
                        <form class="row g-3 mb-6" id="formactualizarinformacion" autocomplete="off">
                            <input type="hidden" name="op" id="op" />
                            <input type="hidden" name="id" id="id" />
                            <input type="hidden" name="filtro" id="filtro" value="vereda" />
                            <input type="hidden" name="filtroVeredaById" id="filtroVeredaById"
                            value="si" />
                          
                                    <select class="form-select" style="display:none" onchange="DEPARTAMENTO.getMunicipios(); ACTUALIZACION_INFORMACION.obtenerRegistrosExistentes();"
                                         id="tbl_departamento_id" name="tbl_departamento_id">
                                        <?php echo $optionDep; ?>
                                    </select>  
                                    <label style="display:none" for="validationCustom05">Departamento</label>
                                
                            <div class="col-sm-6 col-md-4">
                                <div class="form-floating">
                                    <select class="form-select"  id="tbl_municipio_id"
                                        onchange="DEPARTAMENTO.getVeredasByMunicipioId(); ACTUALIZACION_INFORMACION.obtenerRegistrosExistentes();"
                                        name="tbl_municipio_id">
                                    </select>
                                    <label for="validationCustom05">Municipio</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="tbl_vereda_id" name="tbl_vereda_id"
                                        onchange="ACTUALIZACION_INFORMACION.obtenerRegistrosExistentes();">
                                    </select>
                                    <label for="exampleFormControlSelect1">Vereda<span
                                    class="text-danger mb-1">*</span></label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="form-floating">
                                    <select class="form-select"  id="factorId" name="factorId"
                                        onchange="ACTUALIZACION_INFORMACION.obtenerRegistrosExistentes(true);">
                                        <?php echo $optionFactores; ?>
                                    </select>
                                    <label for="exampleFormControlSelect1">Factores<span
                                    class="text-danger mb-1">*</span></label>
                                </div>
                            </div>

                            
                            <!-- INICIO DE REGISTROS DISPONIBLES  -->
                            <div class="p-4 code-to-copy">      
                                <div class="table-responsive scrollbar">
                                    <div style="display: flex; justify-content: center; align-items: center; height: 40px;">
                                        <label style="font-size: 18px; font-weight: bold; text-align: center;" for="tablaRegistros">
                                            Registros Disponibles
                                        </label>
                                    </div>
                                    <div class="table-responsive" >
                                        <table class="table table-striped table-sm fs-9 mb-0" id="tablaRegistros">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Seleccionar</th>
                                                    <th class="text-center">Valor</th>
                                                    <th class="text-center">Unidad de medida</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    <!-- FIN DE REGISTRO DISPONIBLES -->
                            <div class="row justify-content-center">
                                <div class="col-sm-4 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cantidad_nueva" name="cantidad_nueva" 
                                            placeholder="123" onKeyPress="return soloNumeros(event);">
                                        <label for="cantidad_nueva">Cantidad nueva<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div style="display:none" class="col-sm-4">
                                <div class="form-group">
                                    <label style="display:none" for="exampleFormControlSelect1">Actores Responsables
                                        <span class="text-danger mb-1">*</span>
                                    </label>
                                    <select style="display:none" class="form-control" id="actoresId" name="actoresId">
                                        <?php 
                                            // Asegurar que la primera opción tenga el atributo "selected"
                                            $opciones = explode("\n", trim($optionActores));
                                            foreach ($opciones as $index => $opcion) {
                                                echo $index === 0 ? str_replace('<option', '<option selected', $opcion) : $opcion;
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mb-4">
                                <div class="form-floating" style="width: 100%; max-width: 800px;">
                                    <textarea id="accion_realizada" name="accion_realizada"
                                    placeholder="Ingrese las acciones realizadas"
                                    class="form-control" id="exampleFormControlTextarea1"
                                    rows="5"></textarea>                    
                                    <label for="exampleFormControlTextarea1">Acción
                                    Realizada</label>
                                </div>
                            </div>
                            <!--INICIO TABLA CON INFORMACION DE RESGISTROS DISPONIBLES -->
                            <div id="divInformacion" class="card-body" style="display:none;">
                                <h5>Información ingresada con los parametros seleccionados</h5>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="floating-label" for="Text">Eje</label>
                                        <div class="form-group">
                                            <input id="eje" name="eje" class="form-control"
                                                type="text" placeholder="" readonly="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="floating-label" for="Text">Pilar</label>
                                        <div class="form-group">
                                            <input id="pilar" name="pilar" class="form-control"
                                                type="text" placeholder="" readonly="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="floating-label" for="Text">Area</label>
                                        <div class="form-group">
                                            <input id="area" name="area" class="form-control"
                                                type="text" placeholder="" readonly="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="floating-label" for="Text">Tipo
                                            Medición</label>
                                        <div class="form-group">
                                            <input id="tipo_medicion" name="tipo_medicion"
                                                class="form-control" type="text" placeholder=""
                                                readonly="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tabla-seleccion"></div>
                            <!-- FIN TABLA DE INFORMACION REGISTROS DISPONIBLES -->
                            <!-- INICIO DE SUBIR IMAGENES -->
                            <div class="col-sm-12">
                                <div class="d-flex flex-wrap gap-3 justify-content-center">
                                    <div style="width: 240px;">
                                        <p class="text-center mb-2 fw-bold">Foto 1</p>
                                        <iframe id="ifm1" name="ifm1" src="upload.php" scrolling="no" frameborder="0"
                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                    </div>
                                    <div style="width: 240px;">
                                        <p class="text-center mb-2 fw-bold">Foto 2</p>
                                        <iframe id="ifm2" name="ifm2" src="upload.php" scrolling="no" frameborder="0"
                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                    </div>
                                    <div style="width: 240px;">
                                        <p class="text-center mb-2 fw-bold">Foto 3</p>
                                        <iframe id="ifm3" name="ifm3" src="upload.php" scrolling="no" frameborder="0"
                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                    </div>
                                    <div style="width: 240px;">
                                        <p class="text-center mb-2 fw-bold">Foto 4</p>
                                        <iframe id="ifm4" name="ifm4" src="upload.php" scrolling="no" frameborder="0"
                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                    </div>
                                </div>
                            </div>
                            <!-- FINAL SUBIR IMGENES -->
                            <!-- INICIO BOTON CANCELAR Y GUARDAR -->
                            <div class="col-12 p-3">
                                <div class="row g-3 justify-content-end">
                                    <div class="col-auto">
                                        <button type="button" onclick="UTIL.clearForm('formactualizarinformacion');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                                    </div>
                                    <?php if ($create && $edit): ?>
                                    <div class="col-auto">
                                        <button class="btn btn-primary px-5" type="button" onclick="ACTUALIZACION_INFORMACION.save();">Guardar</button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- FIN BOTON CANCELAR Y GUARDAR -->
                        </form>
              </div>          
          </div>
        </div>
        <?php include './admin/include/footer.php';?>
    </div>
    </main>

    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/actualizacion_informacion.js"></script>
    <script>
    setTimeout(function() {
        DEPARTAMENTO.getMunicipios();
    }, 1000);
    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>

    </body>

    </html>