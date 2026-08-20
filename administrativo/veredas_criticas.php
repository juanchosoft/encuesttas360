<?php
include './admin/include/head.php';
include './admin/classes/Departamento.php';
include './admin/classes/Vereda.php';
require './admin/classes/CompromisosFactorPilar.php';
require './admin/include/generic_classes.php';
require './admin/classes/Pilar.php';
require_once './admin/classes/Ciudad.php';

// Permisos
$view = SessionData::getPermission(46);
$create = SessionData::getPermission(47);
$edit = SessionData::getPermission(48);
$baseUrl = dirname($_SERVER['SCRIPT_NAME']); 
if (!$view) {
    require 'permiso_denegado.php';
}
$pilar = $_REQUEST['pilar'];

// Información de Pilares
$response = Pilar::getAll(null);
if (!empty($response['output']['valid'])) {
    $arrPilar = $response['output']['response'];
    
    // Generar las opciones con selección
    $optionPilar = array_reduce($arrPilar, function ($carry, $val) use ($pilar) {
        $selected = ($val['id'] == $pilar) ? ' selected' : '';
        return $carry . "<option value='{$val['id']}'{$selected}>{$val['nombre']}</option>";
    }, '');
} else {
    $optionPilar = '';
}
// Obtén todos los departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
// Obtener la información de los compromisos de la vereda seleccionada
$veredaId = isset($_REQUEST['veredaId']) ? intval($_REQUEST['veredaId']) : 0;
$parametrosCompromisoPilarId = array('veredaId' => $veredaId);
$responseCompromisosFactores = CompromisosFactorPilar::getCompromisosFactores($parametrosCompromisoPilarId);
$compromisoIsValid = $responseCompromisosFactores['output']['valid'];
$responseCompromisos = $responseCompromisosFactores['output']['response'];

foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

$optionMunicipio = $optionDep;

?>

<body>
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
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                  <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                      <div class="col-12 col-md">
                        <h4 class="text-body mb-0" >
                        <i class="uil uil-map-marker text-danger fs-5"></i> Selecione el municipio de su interés</h4>
                      </div>
                    </div>
                  </div>
     <!-- ================================== INICIO CONTENIDO================================================ -->
         <!-- INICIO DEL FORMUALRIO DE VEREDAS CRITICAS -->
            <div class="card-body p-0">  
                <div class="p-4 code-to-copy">  
                    <form class="row g-3 mb-6">
                        <div  id="formFiltroVeredas">
                            <div class="col-12">
                                <!-- Sección de Departamento, Municipio y Rango alineados horizontalmente -->
                                <div class="col-sm-6 col-md-6" style="display: none;">
                                    <div class="form-floating">
                                        <select class="form-select" onchange="DEPARTAMENTO.getMunicipios();" id="tbl_departamento_id" name="departamento" style="width: 100%; text-align: center; text-align-last: center;">
                                        <?php echo $optionDep; ?>
                                        </select>
                                        <label for="tbl_departamento_id">Departamento<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-8 mb-3" style="display: block;">
                                    <div class="form-floating">
                                        <select class="form-select" id="pilarId" name="pilar" onchange="updateUrlPilar(this)" >
                                        <?php echo $optionPilar; ?>
                                        </select>
                                        <label for="pilarId">Pilar <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-8 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="tbl_municipio_id" name="municipio" onchange="DEPARTAMENTO.getVeredasByMunicipioId();" >
                                        </select>
                                        <label for="tbl_municipio_id">Municipio <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-8 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="rangoId" name="rango" >
                                        <option value="">Seleccione un rango</option>
                                        </select>
                                        <label for="rangoId">Rango <span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 gy-6">
                            <div class="row g-3 justify-content-center">
                                <div class="col-auto">
                                    <button id="btnSeleccionar" class="btn btn-primary" type="button">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <!-- FIN DEL FORMUALRIO DE VEREDAS CRITICAS -->

                  <!-- INICIO TABLA RESULTADO DE VEREDAS CRÍTICAS -->
                    <div class="p-4 code-to-copy">
                        <div class="text-center mb-3">
                            <h5 class="mb-0 d-flex justify-content-center align-items-center gap-2">
                                <i class="uil uil-exclamation-triangle text-danger fs-6"></i>
                                Resultado de veredas críticas
                            </h5>
                        </div>
                        <div class="table-responsive d-flex justify-content-center">
                            <div style="min-width: 500px; max-width: 800px; width: 100%;">
                                <table id="tablaVeredas" class="table table-striped table-sm fs-9 mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Vereda</th>
                                            <th >Ver</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <tr id="mensajeInicial" class="text-center">
                                            <td colspan="2" class="text-center" style="font-size:15px;">
                                                🔍 Consulta para conocer las veredas críticas según el departamento, municipio y pilar seleccionados.
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- FIN TABLA RESULTADO DE VEREDAS CRITICAS -->
                <!-- ================================== FIN CONTENIDO================================================ -->
                <!-- Modal para mostrar la tabla de Factores por Vereda -->
                    <div class="modal fade modalVeredaDetalles" id="modalVeredaDetalles" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title" id="exampleModalLabel" style="color: white;"><i class="uil uil-shield-check me-2"></i> Factores de inestabilidad</h5>
                                    <button class="btn btn-close p-1" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table tablecriticas">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;">Factor</th>
                                                <th style="text-align:center;">Cantidad</th>
                                                <th style="text-align:center;">Unidad Medida</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaFactoresBody" style="text-align:center;">
                                        <!-- Los datos se agregarán aquí con JavaScript -->
                                        </tbody>
                                    </table>
                                    <!-- INICIO DE BOTON CANCELAR MODAL -->
                                    <div class="modal-footer">
                                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                    <!-- FIN DE BOTON CANCELAR MODAL -->
                                </div>
                            </div>
                        </div>
                    </div>
                <!--  FIN Modal para mostrar la tabla de Factores por Vereda -->
                </div>
    <?php
    include './admin/include/footer.php';
    ?>
    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="admin/js/veredas_criticas.js"></script>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        setTimeout(function() {
            DEPARTAMENTO.getMunicipios();
        }, 1000);

    </script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>