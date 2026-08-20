<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
// Permisos
$view = SessionData::getPermission(73);
$create = SessionData::getPermission(74);
$edit = SessionData::getPermission(75);
if (!$view) {
    require 'permiso_denegado.php';
}

$modulo = 'Banco Proyectos';

include './admin/classes/Proyectos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Secretarias.php';

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de secretarias
$arr = Secretarias::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$optionSec = "";
foreach ($arr as $val) {
    $optionSec .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . " </option>";
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
                                <h4 class="text-body mb-0 d-flex align-items-center" >
                                <is class="uil uil-invoice me-2 fs-6"></i>Ejecución Hacienda
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 code-to-copy"> 
                            <form id="formsecretaria" class="row g-3 mb-6 needs-validation" role="form" autocomplete="false" novalidate>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="date" id="date" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha<span
                                        class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                               <div class="col-sm-6 col-md-3" >
                                    <div class="form-floating">
                                        <select class="form-select" id="provincia" name="provincia">
                                        <option selected value="Alto_Putumayo">Alto Putumayo</option>
                                        <option value="Medio_Putumayo">Medio Putumayo</option>
                                        <option value="Bajo_Putumayo">Bajo Putumayo</option>
                                        </select>
                                        <label for="provincia">Subregión <span class="text-danger">*</span></label>
                                    </div>
                                    </div>

                                <div class="col-sm-6 col-md-3 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id" required>
                                        <?php echo $optionDep; ?>
                                        </select>
                                        <label for="tbl_departamento_id">Departamento<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"
                                        onchange="DEPARTAMENTO.getVeredasByMunicipioId(); INGRESOPAE.getSedesEducativasByCodigoMunicipio(this.value);">
                                        </select>
                                        <label for="tbl_municipio_id">Municipio<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="form-floating col-md-3">
                                    <input type="text"
                                        class="form-control"
                                        id="secretaria"
                                        name="secretaria"
                                        placeholder="Secretaría o Dependencia"
                                        value="Hacienda"
                                        readonly
                                        required>
                                    <label for="secretaria">Secretaría o Dependencia Encargada <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <textarea 
                                        class="form-control"
                                        placeholder="Describa el objeto de la actividad brevemente"
                                        id="objeto"
                                        name="objeto"
                                        required
                                        autocomplete="off"
                                        style="height: 50px;"></textarea>
                                    <label for="objeto">Objeto <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <select class="form-select" id="accion" name="accion" required>
                                        <option value="" disabled selected>Seleccione una opción</option>
                                        <option value="Capacitación Fiscal y Financiera">Capacitación Fiscal y Financiera</option>
                                        <option value="Operativos Contrabando">Operativos Contrabando</option>
                                        <option value="Impuesto Vehicular Recaudado">Impuesto Vehicular Recaudado</option>
                                        <option value="Impuesto Estampillas Recaudado">Impuesto Estampillas Recaudado</option>
                                    </select>
                                    <label for="accion">Tipo de Acción <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <input type="number" class="form-control" id="cantidad"name="cantidad"  placeholder="0" value=""
                                        onkeypress="return soloNumeros(event);">
                                    <label for="cantidad">Cantidad</label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <input type="number" class="form-control" id="incautacion_licores" name="incautacion_licores" placeholder="0" value="">
                                    <label for="incautacion_licores">Incautación Licores</label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <input type="number" class="form-control" id="incautacion_cigarrillos" name="incautacion_cigarrillos" placeholder="0"
                                        value="" onkeypress="return soloNumeros(event);">
                                    <label for="incautacion_cigarrillos">Incautación Cigarrillos</label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <input type="number" class="form-control" id="capacitacion_programada" name="capacitacion_programada" placeholder="0"
                                        value="" onkeypress="return soloNumeros(event);" required>
                                    <label for="capacitacion_programada">Capacitaciones Programadas <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating col-md-3">
                                    <input type="number"  class="form-control" id="capacitacion_ejecutada" name="capacitacion_ejecutada" placeholder="0"
                                        value="" onkeypress="return soloNumeros(event);" required>
                                    <label for="capacitacion_ejecutada">Capacitaciones Ejecutadas <span class="text-danger">*</span></label>
                                </div>  
                                <div class="form-floating col-md-6">
                                    <textarea class="form-control" placeholder="Ingrese observaciones de la obra" id="observaciones"
                                            name="observaciones" required style="height: 150px;"></textarea>
                                    <label for="observaciones">Observaciones</label>
                                </div>
                                <div class="col-12">
                                    <div class="row g-3 justify-content-end">
                                        <div class="col-auto">
                                        <button type="button" onclick="UTIL.clearForm('formsecretaria');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                                        </div>
                                        <?php if ($create && $edit): ?>
                                        <div class="col-auto">
                                        <button class="btn btn-primary px-5" type="button" onclick="HACIENDA.saveData();">Ingresar Proyecto</button>
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
    <?php include './admin/include/footer.php'; ?>
    </div>

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>



    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/hacienda.js"></script> 

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script>
        setTimeout(function() {
            $("#tbl_departamento_id").val('86')
        }, 500);

        setTimeout(function() {
            DEPARTAMENTO.getMunicipios();
        }, 1000);
    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>