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
$arr = Secretarias::getAllproyectos(null);
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
                        <h4 class="text-body mb-0 d-flex align-items-center" ><i class="uil uil-clipboard-alt fs-6"></i> Proyectos secretarías</h4>
                  </div>
                </div>
              </div>
                  <!-- INICIO DE FORM DE CREACION DE PROYECTOS SECRETARÍAS -->
                  <div class="card-body p-0">
                        <div class="p-4 code-to-copy"> 
                            <form id="formsecretaria" class="row g-3 mb-6" role="form" autocomplete="false">
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="date" id="date" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3" style="display: none;">
                                    <div class="form-floating">
                                        <select class="form-select" id="provincia" name="provincia">
                                            <option selected value="Seleccione">Seleccione</option>
                                            <option value="Soto Norte">Soto Norte</option>
                                            <option value="Guanentá">Guanentá</option>
                                            <option value="García Rovira">García Rovira</option>
                                            <option value="Comunera">Comunera</option>
                                            <option value="Vélez">Velez</option>
                                            <option value="Metropolitana">Metropolitana</option>
                                            <option value="Yariguíes">Yariguíes</option>
                                        </select>
                                        <label for="validationCustom02">Provincia<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" style="width: 100%;" style="display: none;" onchange="DEPARTAMENTO.getMunicipios();" id="tbl_departamento_id" name="tbl_departamento_id"> <?php echo $optionDep; ?>
                                        </select>
                                        <label for="validationCustom01">Departamento<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <select multiple class="form-select" id="tbl_municipio_id" name="tbl_municipio_id[]" onchange="DEPARTAMENTO.getVeredasByMunicipioId();" aria-label="Municipio Beneficiado">
                                        <!-- Opciones aquí -->
                                        </select>
                                        <label for="tbl_municipio_id">Municipio Beneficiado <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="proyecto" name="proyecto" 
                                        placeholder="Describa el objeto del proyecto brevemente" autocomplete="off" required/>
                                        <label for="proyecto">Objeto del proyecto<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_secretarias_id" name="tbl_secretarias_id"><?php echo $optionSec; ?>
                                        </select>
                                        <label for="validationCustom05">Secretaria o Dependencia Encargada<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="valor_proyecto" name="valor_proyecto" placeholder="0" 
                                        value="" required onKeyPress="return soloNumeros(event);"/>
                                        <label for="valor_proyecto">Total Inversión <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="date_inicio" id="date_inicio" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha inicio<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="fecha_entrega" id="fecha_entrega" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha entrega<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control flatpickr-input" id="contratista"  name="contratista" 
                                        placeholder="Ingrese el nombre de o los contratistas" 
                                        autocomplete="off" required/>
                                        <label for="validationCustom01">Contratista</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control flatpickr-input" id="nit" name="nit" 
                                        placeholder="Ingrese el nit del contratista, si es más de uno sepárelo con guión" 
                                        autocomplete="off" required/>
                                        <label for="nit">Nit Contratista</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control flatpickr-input" id="interventoria" 
                                        name="interventoria" placeholder="Ingrese el nombre de o los Interventores" 
                                        autocomplete="off" required/>
                                        <label for="interventoria">Interventoria</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="plazo_construccion" name="plazo_construccion" placeholder="Tiempo de proyecto en meses" autocomplete="off">
                                            <label for="plazo_construccion">Plazo meses</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="estado" name="estado">
                                        <option value="Estudios Previos">Estudios Previos</option>
                                        <option value="Pliego">Pliego</option>
                                        <option value="Contratado">Contratado</option>
                                        <option value="Ejecución">Ejecución</option>
                                        <option value="Terminado">Terminado</option>
                                        <option value="Liquidación">Liquidación</option>
                                        </select>
                                        <label for="estado">Estado Proyecto <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="porcentaje_ejecucion" name="porcentaje_ejecucion" 
                                        placeholder="0%" required />
                                        <label for="porcentaje_ejecucion">Ejecución de la obra</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="date_prorroga" id="date_prorroga" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha prorroga</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="dias_prorroga" name="dias_prorroga" placeholder="Días de prórroga">
                                            <label for="dias_prorroga">Días Prórroga</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="adicion" name="adicion" placeholder="Ingrese el Valor de la adición" autocomplete="off">
                                        <label for="adicion">Adición Presupuestal</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-mb-3" style="max-width: 600px;">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Ingrese observaciones de la obra" id="observaciones" name="observaciones" style="height: 100px;"></textarea>
                                        <label for="observaciones">Observaciones</label>
                                    </div>
                                </div>
                                <!-- INICIO BOTON CANCELAR Y GUARDAR  -->
                                    <div class="col-12">
                                        <div class="row g-3 justify-content-end">
                                            <div class="col-auto">
                                                <button  type="button" onclick="UTIL.clearForm('formsecretaria');" class="btn btn-phoenix-secondary px-5">Cancelar </button>
                                            </div>
                                            <div class="col-auto">
                                                <?php if ($create && $edit) {?>
                                                <button type="button" onclick="PROYECTOS.validateData();" class="btn btn-primary">Ingresar Proyecto</button><?php }?>
                                            </div>
                                        </div>
                                    </div>
                                <!-- FIN BOTON CANCELAR Y GUARDAR  -->
                            </form>
                        </div>
                    </div>
            <!-- FIN DE FORM DE CREACION DE PROYECTOS SECRETARÍAS -->
                    
            </div>
          </div>
        </div>
        <?php include './admin/include/footer.php';?>
    </div>

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/proyectos.js"></script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const municipioSelect = document.getElementById('tbl_municipio_id');

            //ESTE ES EL SCRIPT DE SELECCION MULTIPLE Y LOS COLORES QUE SE LE APLICA
            function updateOptionColors() {
                const selectedOptions = municipioSelect.selectedOptions;
                const colors = ['#FFD700', '#FF4500', '#32CD32', '#1E90FF', '#FF1493']; 

                
                Array.from(selectedOptions).forEach((option, index) => {
                    if (index < colors.length) {
                        option.style.backgroundColor = colors[index];
                        option.style.color = '#FFFFFF'; 
                    } else {
                        option.style.backgroundColor = '#808080'; 
                        option.style.color = '#FFFFFF';
                    }
                });

                
                Array.from(municipioSelect.options).forEach(option => {
                    if (!option.selected) {
                        option.style.backgroundColor = ''; 
                        option.style.color = '';
                    }
                });
            }

            
            municipioSelect.addEventListener('mousedown', function (e) {
                e.preventDefault();
                const option = e.target;
                if (option.tagName === 'OPTION') {
                    option.selected = !option.selected; 
                    updateOptionColors(); 
                }
            });
        });
    </script>
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script>
        setTimeout(function() {$("#tbl_departamento_id").val(UTIL.getDepartamentoPrincipal())}, 500);
        setTimeout(function() { DEPARTAMENTO.getMunicipios() }, 1000);
    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>