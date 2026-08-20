<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';

// Verificación de permisos
$permissions = [
    'view' => SessionData::getPermission(1),
    'create' => SessionData::getPermission(1),
    'edit' => SessionData::getPermission(1),
    'delete' => SessionData::getPermission(1)
];

// Redirigir si no hay permiso de vista
if (!$permissions['view']) {
    require 'permiso_denegado.php';
    exit;
}

include './admin/classes/Visitasg.php';
include './admin/classes/Departamento.php';
include './admin/classes/Secretarias.php';
include './admin/classes/Acciong.php';

// Información de Visitas
$visitas = Visitasg::getAll(null);
$isValidVisitas = $visitas['output']['valid'] ?? false;
$visitasResponse = $visitas['output']['response'] ?? [];

// Información de Departamentos
$departamentos = Departamento::getAll(null);
$isValidDep = $departamentos['output']['valid'] ?? false;
$departamentosResponse = $departamentos['output']['response'] ?? [];
$optionDep = "";
foreach ($departamentosResponse as $dep) {
    $optionDep .= "<option value='" . $dep['codigo_departamento'] . "'>" . $dep['codigo_departamento'] . " - " . $dep['departamento'] . "</option>";
}

// Información de acciones primera dama
$arraccion = Acciong::getAll(null);
$isvalid = $arraccion['output']['valid'];
$arraccion = $arraccion['output']['response'];
$optionaccion = "";
foreach ($arraccion as $val) {
    $optionaccion .= "<option value='" . $val['id'] . "'>" . $val['accion'] . " </option>";
}

$modulo = 'Primera Dama';

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

    <!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <div class="content">
        <div class="mt-4">
          <div class="col-12 col-xl-20">
            <div class="col-11 col-xl-11 mx-auto">
              <div class="mb-9">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                  <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                      <div class="col-12 col-md">
                        <h4 class="text-body mb-0"><i class="uil uil-file-alt fs-6"></i> Formulario detalle</h4>
                      </div>
                    </div>
                  </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">   
                                    
                    <!-- INICIO FORMULARIO VISITA GESTORA -->
                                    <form id="formvisitas" class="row g-3 mb-6" role="form" autocomplete="false">
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-floating">
                                                <input class="form-control datetimepicker flatpickr-input" name="date" id="date" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly" required="">
                                                <label for="validationCustom01">Fecha<span class="text-danger mb-1">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id">
                                                <?php echo $optionDep; ?>
                                                </select>
                                                <label for="tbl_departamento_id">Departamento <span class="text-danger mb-1">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id" onchange="DEPARTAMENTO.getVeredasByMunicipioId();">
                                                </select>
                                                <label for="tbl_municipio_id">Municipio<span class="text-danger mb-1">*</span></label>
                                            </div>
                                            </div>
                                                <div class="col-sm-6 col-md-3 mb-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="provincia" name="provincia">
                                                        <option value="Seleccione">Seleccione</option>
                                                        <option value="Alto_Putumayo">Alto Putumayo</option>
                                                        <option value="Medio_Putumayo">Medio Putumayo</option>
                                                        <option value="Bajo_Putumayo">Bajo Putumayo</option>
                                                        </select>
                                                        <label for="provincia">Subregión</label>
                                                    </div>
                                                </div>


                                        <div class="col-sm-6 col-md-3 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="poblacion" name="poblacion" placeholder="Población Impactada">
                                                <label for="poblacion">Población Impactada<span class="text-danger mb-1">*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3 mb-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="inversion" name="inversion" placeholder="Inversión Estimada" onKeyPress="return soloNumeros(event);">
                                                <label for="inversion">Inversión Estimada<span class="text-danger mb-1">*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-3 mb-3">
                                            <div class="form-floating">
                                                <select class="form-select" id="campana" name="campana">
                                                <option value="Seleccione">Seleccione</option>
                                                <option value="Niños al estadio">Niños al estadio</option>
                                                <option value="Niños al cine">Niños al cine</option>
                                                <option value="Niños al teatro">Niños al teatro</option>
                                                <option value="Es tiempo de aprender">Es tiempo de aprender</option>
                                                <option value="Niños al estadio - Optometría">Niños al estadio - Optometría</option>
                                                <option value="Metale mente">Metale mente</option>
                                                </select>
                                                <label for="campana">Nombre</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3 mb-3">
                                            <div class="form-floating">
                                                 <input type="text" class="form-control" id="link" name="link" placeholder="Link Mediático">
                                                <label for="link">Link Mediático</label>
                                            </div>
                                        </div>
   

                        <!-- INICIO DE DESCRIPCIÓN DE ACTIVIDAD -->
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" placeholder="Ingrese el motivo de la Actividad" id="desc_actividad" name="desc_actividad" style="height: 100px;"></textarea>
                                            <label for="desc_actividad">Descripción Actividad<span class="text-danger mb-1">*</span></label>
                                        </div>
                                    </form>
                        <!-- FIN DE DESCRIPCIÓN DE ACTIVIDAD -->
                        <!-- INICIO DE SUBIR IMAGENES -->
                                            <div class="row text-center">
                                                <div class="form-group col-md-3">
                                                    <label class="floating-label form-label" for="foto1">Foto 1</label>
                                                        <div class="controls">
                                                            <div class="dropzone dropzone-multiple p-0 mb-5" id="dropzone-foto1" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                                                                <iframe id="ifm1" name="ifm1" src="upload.php" width="90%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class="floating-label form-label" for="foto2">Foto 2</label>
                                                        <div class="controls">
                                                            <div class="dropzone dropzone-multiple p-0 mb-5" id="dropzone-foto2" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                                                                <iframe id="ifm2" name="ifm2" src="upload.php" width="90%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class="floating-label form-label" for="foto3">Foto 3</label>
                                                        <div class="controls">
                                                            <div class="dropzone dropzone-multiple p-0 mb-5" id="dropzone-foto3" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                                                                <iframe id="ifm3" name="ifm3" src="upload.php" width="90%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class="floating-label form-label" for="foto4">Foto 4</label>
                                                        <div class="controls">
                                                            <div class="dropzone dropzone-multiple p-0 mb-5" id="dropzone-foto4" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                                                                <iframe id="ifm4" name="ifm4" src="upload.php" width="90%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        <!-- FIN DE SUBIR IMAGENES -->
                                                <!-- INICIO BOTON CANCELAR Y GUARDAR  -->
                                            <div class="col-12">
                                                <div class="row g-3 justify-content-end">
                                                    <div class="col-auto">
                                                        <button type="button" onclick="UTIL.clearForm('formvisitas');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                                                        </div>
                                                        <div class="col-auto">
                                                        <button class=" btn btn-primary" type="button" onclick="VISITASG.validateData();">Guardar</button>
                                                    </div>
                                                </div>
                                            </div>
                                    <!-- FIN BOTON CANCELAR Y GUARDAR  -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include './admin/include/footer.php';
        ?>
    </div>

    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 Bootstrap4 Theme CSS (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Otras dependencias y estilos (ya incluidos en el proyecto) -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/plugins/prism.js"></script>

    <!-- Archivos personalizados -->
    <script src="admin/js/departamentoDama.js"></script>

    <script src="admin/js/detalle_visitasg.js"></script>
    <script>
    $("#tbl_departamento_id").val('86');
    DEPARTAMENTO.getMunicipios();
    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>