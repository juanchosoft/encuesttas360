<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Secretarias.php';
include './admin/classes/Departamento.php';
include './admin/classes/Vereda.php';
require './admin/classes/CompromisosFactorPilar.php';

require './admin/classes/Pilar.php';

// Permisos
$view = SessionData::getPermission(1);
$create = SessionData::getPermission(2);
$edit = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);
if (!$view) {
    require 'permiso_denegado.php';
}
//Información de Secretarias
$arr = Secretarias::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Secretarias';
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

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- MENU FIN -->
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

    <!-- [ Main Content ] start -->
<div class="content">
        <div class="mt-4">
          <div class="row g-4">
            <div class="col-11 col-xl-11 mx-auto">
              <div class="mb-9">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                  <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                      <div class="col-12 col-md">
                        <h4 class="text-body mb-0" ><i class="uil uil-setting"></i>Acción unificada
                        </h4>
                      </div>
                    </div>
                </div>
            <div class="row">
            <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
    <!-- ================================== INICIO CONTENIDO================================================ -->
    <!-- INICIO DE FORMULARIO DE AVANCES -->
                    <div class="card-body">
                        <form id="formuconfiguracion" role="form" autocomplete="false">
                            <input type="hidden" name="op" id="op" />
                            <input type="hidden" name="idConfig" id="idConfig" /></div>   
                            <div class="row">
                                <div id="formFiltroVeredas" class="row">
                                <!-- Sección de Departamento, Municipio y Rango alineados horizontalmente -->
                                    <div class="col-md-4" style="display: none;">
                                        <div class="form-floating">
                                            <select class="form-select" id="tbl_departamento_id" name="departamento"
                                                    onchange="DEPARTAMENTO.getMunicipios();" >
                                            <?php echo $optionDep; ?>
                                            </select>
                                            <label for="tbl_departamento_id">Departamento <span class="text-danger mb-1">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select" id="tbl_municipio_id"
                                                    name="municipio" onchange="DEPARTAMENTO.getVeredasByMunicipioId();"
                                                    >
                                            </select>
                                            <label for="tbl_municipio_id">Municipio <span class="text-danger mb-1">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select class="form-select"id="tbl_vereda_id"name="tbl_vereda_id" required></select>
                                            <label for="tbl_vereda_id">Vereda <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                            </form>
                            </div>
                        </div>
                    <div class="col-12 d-flex justify-content-center">
                    <button class="btn btn-primary m-4" type="button">Buscar</button>
                    </div>
 <!-- INICIO DE TABLA DE DATOS REGISTRADOS -->
            <!-- <h5 style="font-size: 22px; font-weight: bold; text-align: center; display: flex; align-items: center; justify-content: center;">
            <i class="uil uil-clipboard-alt"></i> Datos Registrados</h5> -->
            <div style="overflow-x: auto; width: 100%;">
                    <table  id="tablaDinamica" class="table table-striped table-sm fs-9 mb-0 table-municipio">
                        <thead>
                            <tr>
                                <th rowspan="2">Eje</th>
                                <th rowspan="2">Pilar</th>
                                <th rowspan="2">Factores</th>
                                <th rowspan="2">Cantidad</th>
                                <th rowspan="2">Unidad de medida</th>
                                <th colspan="3">Acción unificada</th>
                            </tr>
                            <tr>
                                <th>Actores</th>
                                <th>Cantidad</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Seguridad Multidimensional</td>
                                <td>Educación</td>
                                <td>Infraestructura Escolar</td>
                                <td>120</td>
                                <td>Aulas</td>
                                <td>
                                    <select class="form-control accion">
                                        <option value="provisionalmente">Batallon de ingenieros</option>
                                        <option value="batallon_ingenieros">Estación de policía </option>
                                        <option value="batallon2">Incubadora Santander</option>
                                        <option value="batallon3">Junta accion comunal</option>
                                        <option value="batallon4">Quinta brigada</option>
                                    </select>
                                </td>
                                <td>120</td>
                                <td><input type="text" class="form-control" placeholder="Ingrese observación">
                                </td>
                                </tr>
                                    <tr>
                                        <td>Seguridad Multidimensional</td>
                                        <td>Salud y Protección Social</td>
                                        <td>Centros de Salud Construidos</td>
                                        <td>15</td>
                                        <td>Unidades</td>
                                        <td>
                                            <select class="form-control accion">
                                                <option value="provisionalmente">Batallon de ingenieros</option>
                                                <option value="batallon_ingenieros">Estación de policía </option>
                                                <option value="batallon2">Incubadora Santander</option>
                                                <option value="batallon3">Junta accion comunal</option>
                                                <option value="batallon4">Quinta brigada</option>
                                            </select>
                                        </td>
                                        <td>15</td>
                                        <td><input type="text" class="form-control" placeholder="Ingrese observación">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Seguridad Multidimensional</td>
                                        <td>Comercio, Industria y Turismo</td>
                                        <td>Inversiones Empresariales</td>
                                        <td>500</td>
                                        <td>Unidades</td>
                                        <td>
                                            <select class="form-control accion">
                                                <option value="provisionalmente">Batallon de ingenieros</option>
                                                <option value="batallon_ingenieros">Estación de policía </option>
                                                <option value="batallon2">Incubadora Santander</option>
                                                <option value="batallon3">Junta accion comunal</option>
                                                <option value="batallon4">Quinta brigada</option>
                                            </select>
                                        </td>
                                        <td>500</td>
                                        <td><input type="text" class="form-control" placeholder="Ingrese observación">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Prosperidad</td>
                                        <td>Ambiente y Desarrollo Sostenible</td>
                                        <td>Proyectos de Reforestación</td>
                                        <td>2000</td>
                                        <td>Kilometros</td>
                                        <td>
                                            <select class="form-control accion">
                                                <option value="provisionalmente">Batallon de ingenieros</option>
                                                <option value="batallon_ingenieros">Estación de policía </option>
                                                <option value="batallon2">Incubadora Santander</option>
                                                <option value="batallon3">Junta accion comunal</option>
                                                <option value="batallon4">Quinta brigada</option>
                                            </select>
                                        </td>
                                        <td>2000</td>
                                        <td><input type="text" class="form-control" placeholder="Ingrese observación">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Prosperidad</td>
                                        <td>Tecnologías de la Información</td>
                                        <td>Instalaciones de Internet</td>
                                        <td>50</td>
                                        <td>Unidades</td>
                                        <td>
                                            <select class="form-control accion">
                                                <option value="provisionalmente">Batallon de ingenieros</option>
                                                <option value="batallon_ingenieros">Estación de policía </option>
                                                <option value="batallon2">Incubadora Santander</option>
                                                <option value="batallon3">Junta accion comunal</option>
                                                <option value="batallon4">Quinta brigada</option>
                                            </select>
                                        </td>
                                        <td>50</td>
                                        <td><input type="text" class="form-control" placeholder="Ingrese observación">
                                        </td>
                                    </tr>
                                    <!-- Aquí se llenarán los datos dinámicamente con JS -->
                        </tbody>
                    </table>
            </div>                   
  <!-- FINAL DE TABLA DATOS REGISTRADOS -->
                    </div>   
                </div>   
            </div> 
        </div>   
    </div>     
   <?php include './admin/include/footer.php'; ?>

    <!-- Warning Section Ends -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script type="text/javascript" src="admin/js/configuracion.js"></script>
    <script>
    CONFIGURACION.editdata();
    </script>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script>
    setTimeout(function () {
        DEPARTAMENTO.getMunicipios();
        setTimeout(function () {
            const municipioSelect = document.getElementById("tbl_municipio_id");
            if (municipioSelect && municipioSelect.options.length > 0) {
                municipioSelect.dispatchEvent(new Event('change'));
            }
        }, 500);
    }, 500);
    </script>

    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>