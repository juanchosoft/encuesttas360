<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';

// Verificación de permisos
$permissions = [
    'view' => SessionData::getPermission(29),
    'create' => SessionData::getPermission(30),
    'edit' => SessionData::getPermission(31),
    'delete' => SessionData::getPermission(32),
];

// Redirigir si no hay permiso de vista
if (!$permissions['view']) {
    require 'permiso_denegado.php';
    exit;
}

include './admin/classes/VisitasgAspas.php';
include './admin/classes/Departamento.php';
include './admin/classes/Secretarias.php';
include './admin/classes/Acciong.php';
include './admin/classes/Linea.php';
include './admin/classes/Estrategia.php';

// Información de Visitas
$visitas = VisitasgAspas::getAll(null);
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

// Información de línea select
$lineas = Linea::getAll(null);
$isValidLineas = $lineas['output']['valid'] ?? false;
$lineasResponse = $lineas['output']['response'] ?? [];
$optionLineas = "";
foreach ($lineasResponse as $linea) {
    $optionLineas .= "<option value='" . $linea['id'] . "'>" . $linea['nombre'] . "</option>";
}

// Información de estrategia select
$estrategias = Estrategia::getAll(null);
$isValidEstrategias = $estrategias['output']['valid'] ?? false;
$estrategiasResponse = $estrategias['output']['response'] ?? [];
$optionEstrategias = "";
foreach ($estrategiasResponse as $estrategia) {
    $optionEstrategias .= "<option value='" . $estrategia['id'] . "'>" . $estrategia['nombre'] . "</option>";
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
        <style>
#poblacion.form-control,
#inversion.form-control,
#campana.form-control,
#link.form-control {
    width: 100% !important;
    padding: 10px !important;
    border-radius: 14px !important;
    border: 1px solid green !important;
    margin-bottom: 10px !important;
    font-size: 1rem !important;
    color: #000 !important;
    background-color: #fff !important;
}

</style>
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
    <div class="content">
        <div>
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                    <div class="card-header p-4 border-bottom bg-body">
                        <div class="row g-3 justify-content-between align-items-center">
                            <div class="col-12 col-md">
                                <h4 class="text-body mb-0 d-flex align-items-center" ><i class="uil uil-clipboard-alt fs-6"></i>Formulario detalle</h4>
                            </div>
                        </div>
                    </div>
                    <!-- INICIO FORMULARIAO REGISTRO ASPAS -->
                    <div class="card-body p-0">
                        <div class="p-4 code-to-copy"> 
                            <form id="formvisitas" class="row g-3 mb-6" role="form" autocomplete="false">
                                <div class="form-row">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="form-floating">
                                            <input 
                                                class="form-control datetimepicker flatpickr-input" 
                                                name="date"     type="text"  placeholder="dd/mm/yyyy"  data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' 
                                                readonly  required
                                            >
                                            <label for="date">Fecha <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="tbl_departamento_id">Departamento<span
                                                class="text-danger mb-1">*</span></label>
                                        <select class="form-control ocultar-select"
                                            id="tbl_departamento_id" name="tbl_departamento_id">
                                            <?php echo $optionDep; ?>
                                        </select>

                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="tbl_municipio_id">Municipio<span
                                                class="text-danger mb-1">*</span></label>
                                        <select class="form-control" id="tbl_municipio_id"
                                            name="tbl_municipio_id"
                                            onchange="DEPARTAMENTO.getVeredasByMunicipioId();"></select>

                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    

                                                    <div class="form-group col-md-4">
                                                        <label for="poblacion">Población Impactada</label>
                                                        <input type="text" class="form-control" id="poblacion"
                                                            name="poblacion" placeholder="">

                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="inversion">Inversión Estimada</label>
                                                        <input type="text" onKeyPress="return soloNumeros(event);"
                                                            class="form-control" id="inversion" name="inversion"
                                                            placeholder="">

                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <label for="desc_actividad">Descripción Actividad</label>
                                                        <textarea class="form-control" id="desc_actividad"
                                                            name="desc_actividad" rows="2"
                                                            placeholder="Ingrese el motivo de la Actividad"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-3">
                                                        <label for="tbl_linea">Linea</label>
                                                        <select class="form-control" id="tbl_linea_id" name="tbl_linea_id">
                                                            <option value="">Seleccione</option>
                                                            <?php echo $optionLineas; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="tbl_estrategia">Estrategia</label>
                                                        <select class="form-control" id="tbl_estrategia_id" name="tbl_estrategia_id">
                                                            <option value="">Seleccione</option>
                                                            <?php echo $optionEstrategias; ?>
                                                        </select>
                                                    </div>
                                                     <div class="form-group col-md-3">
                                                        <label for="desc_actividad">Nombre</label>
                                                        <select class="form-control" id="campana" name="campana">
                                                            <option value="Seleccione">Seleccione</option>
                                                            <option value="Niños al estadio">Niños al estadio</option>
                                                            <option value="Niños al cine">Niños al cine</option>
                                                            <option value="Niños al teatro">Niños al teatro</option>
                                                            <option value="Es tiempo de aprender">Es tiempo de aprender
                                                            </option>
                                                            <option value="Niños al estadio - Optometría">Niños al
                                                                estadio - Optometría</option>
                                                            <option value="Metale mente">Metale mente</option>
                                                        </select>

                                                    </div>
                                                    <!-- <div class="form-group col-md-6">
                                                        <label for="">Actividad</label>
                                                        <input type="text" class="form-control" id="actividad"
                                                            name="actividad" placeholder="">
                                                    </div> -->
                                                    <div class="form-group col-md-3">
                                                        <label for="">link Mediatico</label>
                                                        <input type="text" class="form-control" id="link" name="link"
                                                            placeholder=" Ingrese link ">
                                                    </div>
                                                </div>

                                               
                                                   
                                                

                                                <div class="form-row">
                                                    <div class="form-group col-md-3">
                                                        <label for="">Foto 1</label>
                                                        <div class="controls">
                                                            <iframe id='ifm1' name='ifm' src="upload.php" width="200"
                                                                height="60" scrolling="no" frameborder="0"></iframe>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="">Foto 2</label>
                                                        <div class="controls">
                                                            <iframe id='ifm2' name='ifm' src="upload.php" width="200"
                                                                height="60" scrolling="no" frameborder="0"></iframe>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="">Foto 3</label>
                                                        <div class="controls">
                                                            <iframe id='ifm3' name='ifm' src="upload.php" width="200"
                                                                height="60" scrolling="no" frameborder="0"></iframe>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="">Foto 4</label>
                                                        <div class="controls">
                                                            <iframe id='ifm4' name='ifm' src="upload.php" width="200"
                                                                height="60" scrolling="no" frameborder="0"></iframe>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" onclick="UTIL.clearForm('formvisitas');"
                                                    class="btn  btn-danger"
                                                    style="margin-right: 18px;">Cancelar</button>

                                                <button class=" btn btn-primary" type="button"
                                                    onclick="VISITASG.validateData();">Guardar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>

    <!-- Archivos personalizados -->
    <script src="admin/js/departamentoDama.js"></script>

    <script src="admin/js/detalle_visitasg_aspas.js"></script>
    <script>
    $("#tbl_departamento_id").val('68');
    DEPARTAMENTO.getMunicipios();
    </script>

</body>

</html>