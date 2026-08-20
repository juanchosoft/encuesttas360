<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';

// Permisos
$view = SessionData::getPermission(49);
$create = SessionData::getPermission(50);
$edit = SessionData::getPermission(51);

if (!$view) {
    require 'permiso_denegado.php';
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
                        <h4 class="text-body mb-0" >Avances por tiempo</h4>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
    <!-- ================================== INICIO CONTENIDO================================================ -->
    <!-- INICIO DE FORMULARIO DE AVANCES -->
        <form class="row g-3 mb-6" role="form" autocomplete="false">
            <div class="row">
                </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-floating">
                            <select class="form-select" id="inputGroupSelect03" name="pilar" >
                            <option selected>Seleccione</option>
                            <option value="Justicia y Derecho">Justicia y Derecho</option>
                            <option value="Salud y Proteccion Social">Salud y Protección Social</option>
                            <option value="Educación">Educación</option>
                            <option value="Trabajo">Trabajo</option>
                            <option value="Vivienda, Ciudad y Territorio">Vivienda, Ciudad y Territorio</option>
                            <option value="Deporte y Recración">Deporte y Recreación</option>
                            <option value="Gobierno Territorial">Gobierno Territorial</option>
                            <option value="Agricultura y Desarrollo Rural">Agricultura y Desarrollo Rural</option>
                            <option value="Minas y Energia">Minas y Energía</option>
                            <option value="Ambiente y Desarrollo Sostenible">Ambiente y Desarrollo Sostenible</option>
                            <option value="Información y Estadística">Información y Estadística</option>
                            <option value="Cultura">Cultura</option>
                            <option value="Tecnologías de la Información">Tecnologías de la Información</option>
                            <option value="Comercio, Industria y Turismo">Comercio, Industria y Turismo</option>
                            </select>
                            <label for="inputGroupSelect03">Pilar</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 mb-3">
                        <div class="form-floating">
                            <select class="form-select" id="tbl_municipio_id" name="municipio" onchange="DEPARTAMENTO.getVeredasByMunicipioId();" >
                            </select>
                            <label for="tbl_municipio_id">Municipio <span class="text-danger mb-1">*</span></label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-floating">
                            <input class="form-control datetimepicker flatpickr-input" id="basic-form-dob" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                            <label for="validationCustom01">Fecha inicial</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-floating">
                            <input class="form-control datetimepicker flatpickr-input" name="date_inicio" id="basic-form-dob" type="date" placeholder="dd/mm/yyyy" data-options="{&quot;disableMobile&quot;:true,&quot;dateFormat&quot;:&quot;d/m/Y&quot;}" readonly="readonly">
                            <label for="validationCustom01">Fecha final</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <button class="btn btn-primary m-4" type="button">Seleccionar</button>
                    </div>
        </form>
    <!-- FIN DE FORMUALRIO DE AVANCES -->
    <!-- INICIO DE LA TABLA DE MUESTRA DE AVANCES -->
                    <div class="col-md-12">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table id="dynamictable" class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Provincia</th>
                                        <th>Municipio</th>
                                        <th>Estado Inicial</th>
                                        <th>Estado Final</th>
                                        <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Guanentá</td>
                                        <td>Barichara</td>
                                        <td><span class="color-circle color-orange"></span></td>
                                        <td><span class="color-circle color-blue"></span></td>
                                        <td><button type="button" class="btn btn-icon btn-info" data-toggle="modal" data-target="#exampleModalCenter">
                                            <i class="uil uil-eye fs-7"></i>
                                        </button></td>
                                    </tr>
                                    <tr>
                                        <td>Yariguíes</td>
                                        <td>Barrancabermeja</td>
                                        <td><span class="color-circle color-orange"></span></td>
                                        <td ><span class="color-circle color-green"></span></td>
                                        <td><button type="button" class="btn btn-icon btn-info" data-toggle="modal" data-target="#exampleModalCenter">
                                            <i class="uil uil-eye fs-7"></i>
                                        </button></td>
                                    </tr>
                                    <tr>
                                        <td>García Rovira</td>
                                        <td>Guaca</td>
                                        <td style=" justify-content: center; align-items: center;"><span class="color-circle color-orange"></span></td>
                                        <td><span class="color-circle color-yellow"></span></td>
                                        <td><button type="button" class="btn btn-icon btn-info" data-toggle="modal" data-target="#exampleModalCenter">
                                            <i class="uil uil-eye fs-7"></i>
                                        </button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                    </div>
        <!-- INICIO DE MODAL DE INFO DE AVANCES  -->
            <div class="card-body">
                <div id="exampleModalCenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header bg-primary">
								<h5 class="modal-title" id="exampleModalCenterTitle" style="color: white;">Factores Inestabilidad Vereda</h5>
								<button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
							</div>
                            <div class="modal-body">
                                <div class="table-container">
                                    <table class="table table-striped table-sm fs-9 mb-0">
                                        <thead>
                                            <tr>
                                                <th>Factor</th>
                                                <th>Acción Realizada</th>
                                                <th>Cantidad</th>
                                                <th>Responsables</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="factor-icon">
                                                        <img src="assets/iconos/agua.png" alt="Escasez de agua Icono" width="30px">
                                                        <span>Escasez de agua</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    Recolección y aprovechamiento de aguas lluvias, práctica que permite disminuir la presión sobre fuentes tradicionales de abastecimiento y mitigar los efectos de la escasez de agua. Además, es esencial la conservación de ecosistemas y procesos hidrológicos que sustentan la oferta de agua.
                                                </td>
                                                <td>30</td>
                                                <td>
                                                    Ministerio de Ambiente y Desarrollo Sostenible, Corporaciones Autónomas Regionales (CAR), Comisión de Regulación de Agua Potable y Saneamiento Básico (CRA), Alcaldía, Gobernación
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="factor-icon">
                                                        <img src="assets/iconos/agricola.png" alt="Conflictos por uso del suelo Icono" width="30px">
                                                        <span>Conflictos por uso del suelo</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    Actualizar el POT en consulta con comunidades locales, expertos ambientales y sectores productivos. Establecer zonas de protección estricta en ecosistemas sensibles, como microcuencas y áreas de recarga hídrica.
                                                </td>
                                                <td>20</td>
                                                <td>
                                                    Alcaldía de Barichara, Concejo Municipal, Empresas agroindustriales y turísticas responsables
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="factor-icon">
                                                        <img src="assets/iconos/incendios_forestales.png" alt="Incendios forestales Icono" width="30px">
                                                        <span>Incendios forestales</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    Establecer cortafuegos en áreas estratégicas de las veredas con mayor riesgo de incendios. Promover reforestación con especies nativas resistentes al fuego en zonas degradadas. Dotar al Cuerpo de Bomberos de Barichara con vehículos adecuados, bombas de agua portátiles y herramientas especializadas para combatir incendios forestales.
                                                </td>
                                                <td>50</td>
                                                <td>
                                                    Alcaldía de Barichara, Cuerpo de Bomberos de Barichara, Juntas de Acción Comunal (JAC)
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>         
                                <!-- FIN DE LA TABLA DE MUESTRA DE AVANCES -->    
                            </div>                
                        </div>
                    </div>
                </div>
            </div>
       <!-- FINAL DE MODAL DE INFO DE AVANCES  -->
               </div>
            </div>
        </div>
    </div>
</div>
<!-- ================================== FIN CONTENIDO================================================ -->
<!-- [ Main Content ] end -->
    <?php
    include './admin/include/footer.php';
    ?>
    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>

</body>

</html>
