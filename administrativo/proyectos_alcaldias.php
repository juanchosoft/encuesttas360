<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';


// Permisos
$view = SessionData::getPermission(17);
$create = SessionData::getPermission(18);
$edit = SessionData::getPermission(19);
if (!$view) {
    require 'permiso_denegado.php';
}

include './admin/classes/Proyectos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Ministerios.php';
$modulo = 'Banco Proyectos';

// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
$departamentoPrincipal = '';
if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
  $departamentoPrincipal = $configuracionAplicacion[0]['codigo_departamento'] ?? '';
}


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = '';
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == $departamentoPrincipal ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de Ministerios: Mostrar la columna 'ministerio'
$arrMin = Ministerios::getAll(null);
$arrMin = $arrMin['output']['response'];
$optionMin = "";
foreach ($arrMin as $val) {
    $optionMin .= "<option value='" . $val['id'] . "'>" . $val['ministerio'] . "</option>";
}

// Información de secretarias
$arr = Ministerios::getAllproyectos(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
?>

<body class="">
    <style>
     .bg-aporte-nacion {
  background-color: #d6eaf8 !important; /* azul grisáceo claro */
}

.bg-aporte-gobernacion {
  background-color: #fdebd0 !important; /* beige tostado */
}

.bg-aporte-municipio {
  background-color: #d5f5e3 !important; /* verde oliva claro */
}

.bg-aporte-otros {
  background-color: #e8daef !important; /* púrpura grisáceo claro */
}


    </style>
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
                      <h4 class="text-body mb-0 d-flex align-items-center">
                          <i class="uil uil-clipboard-alt fs-6"></i> Ingreso Información Proyectos Alcaldías con ayuda de Secretarias  - <?php echo $nombreProyecto; ?>
                          <?php if (!empty($logo)): ?>
                              <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;" class="img-fluid img-thumbnail me-3">
                          <?php endif; ?>
                        </h4>
                  </div>
                </div>
              </div>
                  <!-- INICIO DE FORM DE CREACION DE PROYECTOS ALCALDIAS -->
                  <div class="card-body p-0">
                        <div class="p-4 code-to-copy"> 
                            <form id="formalcaldias" class="row g-3 mb-6" role="form" autocomplete="false">
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control datetimepicker flatpickr-input" name="date" id="date" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                                        <label for="validationCustom01">Fecha</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3" style="display: none;">
                                    <div class="form-floating">
                                        <select class="form-select" id="provincia" name="provincia" required>
                                        <option value="Soto_Norte" selected>Soto Norte</option>
                                        <option value="Guanenta">Guanentá</option>
                                        <option value="Garcia_Rovira">García Rovira</option>
                                        <option value="Comunera">Comunera</option>
                                        <option value="Velez">Vélez</option>
                                        <option value="Metropolitana">Metropolitana</option>
                                        <option value="Yariguíes">Yariguíes</option>
                                        </select>

                                        <label for="provincia">Provincia <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id" required>
                                        <?php echo $optionDep; ?>
                                        </select>
                                        <label for="tbl_departamento_id">Departamento <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" style="width: 100%;" 
                                        onchange="DEPARTAMENTO.getVeredasByMunicipioId();" 
                                        id="tbl_municipio_id" name="tbl_municipio_id[]" required>
                                        <!-- Opciones se insertan aquí dinámicamente -->
                                        </select>
                                        <label for="tbl_municipio_id">Alcaldía <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="proyecto" name="proyecto" 
                                            placeholder="Describa el objeto del proyecto brevemente" autocomplete="off" required>
                                        <label for="proyecto">Objeto del proyecto <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_ministerios_id" name="tbl_ministerios_id">
                                        <?php echo $optionMin; ?>
                                        </select>
                                        <label for="tbl_ministerios_id">Ministerio o Dependencia<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-aporte-nacion" id="aporte_nacion" name="aporte_nacion"
                                        onKeyPress="return soloNumeros(event);" placeholder="Aporte Nación" value="0">
                                        <label for="aporte_nacion">Aporte Nación</label>
                                    </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-aporte-gobernacion" id="aporte_gobernacion" name="aporte_gobernacion"
                                        onKeyPress="return soloNumeros(event);" placeholder="Aporte Gobernación" value="0">
                                        <label for="aporte_gobernacion">Aporte Gobernación</label>
                                    </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-aporte-municipio" id="aporte_municipio" name="aporte_municipio"
                                        onKeyPress="return soloNumeros(event);" placeholder="Aporte Municipio" value="0">
                                        <label for="aporte_municipio">Aporte Municipio</label>
                                    </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control bg-aporte-otros" id="aporte_otros" name="aporte_otros"
                                        onKeyPress="return soloNumeros(event);" placeholder="Aporte Otros" value="0">
                                        <label for="aporte_otros">Aporte Otros</label>
                                    </div>
                                    </div>
                                    <div class="form-group col-md-4" id="container_actores" style="display: none;">
                                        <label for="validationCustom01">Seleccione Actores de otros aportes</label>
                                        <select class="form-control" id="actores_id" name="actores_id">

                                        </select>
                                    </div>
                                     <!-- <div class="form-group col-md-4">
                                        <label for="validationCustom01">Total Inversión</label>
                                        <input type="text" class="form-control"
                                            id="valor_proyecto"
                                            name="valor_proyecto"
                                            placeholder=""
                                            value="$ 0"
                                            readonly>
                                    </div> -->

                                <!-- <div class="col-sm-6 col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="valor_proyecto" name="valor_proyecto"
                                            onKeyPress="return soloNumeros(event);" placeholder="Total Inversión" value="0">
                                        <label for="valor_proyecto">Total Inversión</label>
                                    </div>
                                </div> -->
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" placeholder="Ingrese observaciones de la obra"
                                                id="observaciones" name="observaciones" style="height: 100px" required></textarea>
                                    <label for="observaciones">Observaciones</label>
                                </div>

                            <div class="col-sm-12">
                                <div class="d-flex flex-wrap gap-3 justify-content-center">
                                    <div style="width: 240px;">
                                    <p class="text-center mb-2 fw-bold">Documento PDF</p>
                                    <iframe id="ifmPdf" name="ifmPdf" src="upload_pdf.php" scrolling="no" frameborder="0"
                                            style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                    </div>
                                </div>
                            </div>

                        <!-- INICIO BOTON CANCELAR Y GUARDAR  -->
                        <div class="col-12">
                          <div class="row g-3 justify-content-end">
                            <div class="col-auto">
                              <button  type="button" onclick="UTIL.clearForm('formalcaldias');" class="btn btn-phoenix-secondary px-5">
                                Cancelar
                              </button>
                            </div>
                            <div class="col-auto">
                              <?php if ($create && $edit) {
                                ?>
                                <button type="button" onclick="MINISTERIOSPRO.validateData();" class="btn btn-primary">Ingresar Proyecto</button>
                                <?php 
                                }
                                ?>
                            </div>
                      </div>
                      </div>
<!-- FIN BOTON CANCELAR Y GUARDAR  -->
                      </form>
                        </div>
                    </div>
                </div>
                </div>

            <!-- [ Main Content ] end -->
        </div>
        <?php
        include './admin/include/footer.php';
        ?>
    </div>

    <!-- Warning Section Ends -->

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/ministerios_proyectos.js"></script>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>

    <script>
        setTimeout(function() {
            DEPARTAMENTO.getMunicipiosConDepartamentoPrincipal();
        }, 1000);
    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>