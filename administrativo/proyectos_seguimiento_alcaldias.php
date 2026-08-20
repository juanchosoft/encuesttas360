<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';

require_once './admin/include/generic_classes.php';
include './admin/classes/Ministeriospro.php';
include './admin/classes/Secretarias.php';
include './admin/classes/Actores.php';

// Parametros recibidos
$codigoMunicipio = isset($_REQUEST['mun']) ? trim($_REQUEST['mun']) : null;
$secretariaId = isset($_REQUEST['secretaria']) ? trim($_REQUEST['secretaria']) : null;
$codigoDepartamento = Util::getDepartamentoPrincipal();

$requestParams = [
    'secretariaId' => $secretariaId,
    'codigoMunicipio' => $codigoMunicipio,
    'codigoDepartamento' => $codigoDepartamento
];

// Obtener proyectos no leídos
$result = Ministeriospro::getAllProyectosSinLeer($requestParams);
$isvalid = isset($result['output']['valid']) ? $result['output']['valid'] : false;
$arr = isset($result['output']['response']) ? $result['output']['response'] : [];
$modulo = 'Banco Proyectos no leidos';


// Informacion de los Secretarias
$arrSecretaria = Secretarias::getAll(null);
$isvalidSecr = $arrSecretaria['output']['valid'];
$arrSecretaria = $arrSecretaria['output']['response'];
$optionSecretarias = "";
foreach ($arrSecretaria as $val) {
    $optionSecretarias .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . "</option>";
}

// Informacion de los Actores
$parametros = ['alcaldia_id' => $codigoMunicipio];
$arrActores = Actores::getAll(NULL);
$isvalidSecr = $arrActores['output']['valid'];
$arrActores = $arrActores['output']['response'];
$optionActores = "";
foreach ($arrActores as $val) {
    $optionActores .= "<option value='" . $val['id'] . "'>" . $val['nombre'] . "</option>";
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

    <!-- INICIO DE CONTENIDO -->
    <div class="content">

        <div class="row gy-3 mb-6 justify-content-between align-items-center">
            <div class="col-md-9 col-auto d-flex align-items-center">
                <h4 class="mb-2 me-3 text-body-emphasis">
                    Proyectos Alcaldias - Seguimientos <?php echo $nombreProyecto; ?>
                </h4>

                <?php if (!empty($logo)): ?>
                    <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
                        class="img-fluid img-thumbnail">
                <?php endif; ?>
            </div>


        </div>

        <div>
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                    <!-- INICIO DE TABLA SEGUIMIENTO DE PROYECTOS DE ALCALDIAS -->
                    <div class="table-responsive tabla-informacion tabla-scroll">
                        <table class="table table-hover mb-0" id="dynamictable">
                            <thead>
                                <tr class="border-1 listado">
                                    <th>Ver Proyecto</th>
                                    <th>Fecha ingreso</th>
                                    <th>Estado</th>
                                    <th>Fecha Actualiza</th>
                                    <th>Proyecto</th>
                                    <th>Municipio</th>
                                    <th>Valor</th>
                                    <th>Secretaría</th>
                                    <th>Adjunto</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- FIN DE TABLA SEGUIMIENTO DE PROYECTOS DE ALCALDIAS -->
                </div>
            </div>
        </div>
        <?php
        include './admin/include/footer.php';
        ?>
    </div>

   <!-- Modal del formulario de proyectos -->
    <div class="modal fade" id="modalFormularioProyectos" tabindex="-1" role="dialog"
        aria-labelledby="modalFormularioProyectosLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFormularioProyectosLabel">Lectura Proyecto con Alcaldía -
                        Proyecto Leído</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" style="padding: 20px;">
                    <form id="formalcaldias" class="needs-validation" novalidate>
                        <input type="hidden" id="modalId" name="modalId">
                        <div class="row">
                            <!-- Fecha -->
                            <div class="form-group col-md-3">
                                <label for="date">Fecha <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>

                            <!-- Provincia -->
                            <div class="form-group col-md-3">
                                <label for="provincia">Provincia <span class="text-danger">*</span></label>
                                <select class="form-control" id="provincia" name="provincia">
                                    <option value="Seleccione">Seleccione</option>
                                    <option value="Soto_Norte">Soto Norte</option>
                                    <option value="Guanenta">Guanentá</option>
                                    <option value="Garcia_Rovira">García Rovira</option>
                                    <option value="Comunera">Comunera</option>
                                    <option value="Velez">Velez</option>
                                    <option value="Metropolitana">Metropolitana</option>
                                    <option value="Yariguíes">Yariguíes</option>
                                </select>
                            </div>

                            <!-- Alcaldía -->
                            <div class="form-group col-md-3">
                                <label for="tbl_municipio_id">Alcaldía <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="municipio"
                                    name="municipio">
                                <input type="hidden" class="form-control" id="tbl_municipio_id"
                                    name="tbl_municipio_id">
                            </div>

                            <!-- Objeto del Proyecto -->
                            <div class="form-group col-md-4">
                                <label for="proyecto">Objeto del proyecto <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Describa el objeto del proyecto brevemente"
                                    class="form-control" id="proyecto" name="proyecto">
                            </div>

                            <!-- Secretaria -->
                            <div class="form-group col-md-4" id="container_secretaria">
                                <label for="tbl_secretarias_id">Seleccione la Secretaria <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="tbl_secretarias_id" name="tbl_secretarias_id">
                                    <?php echo $optionSecretarias; ?>
                                </select>
                            </div>

                            <!-- Aportes -->
                            <div class="form-group col-md-4">
                                <label for="modalAporteMunicipio">Aportes Municipio</label>
                                <input type="text" class="form-control" id="modalAporteMunicipio" name="modalAporteMunicipio"
                                    placeholder="Ingrese el aporte del municipio">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="modalAporteDepartamento">Aportes Departamento</label>
                                <input type="text" class="form-control" id="modalAporteDepartamento"
                                    name="modalAporteDepartamento" placeholder="Ingrese el aporte del departamento">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="modalNacion">Aportes Nación</label>
                                <input type="text" class="form-control" id="modalNacion" name="modalNacion"
                                    placeholder="Ingrese el aporte de la nación">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="modalOtrosAportes">Otros Aportes</label>
                                <input type="text" class="form-control" id="modalOtrosAportes"
                                    name="modalOtrosAportes" placeholder="Ingrese otros aportes">
                            </div>

                            <!-- Actores -->
                            <div class="form-group col-md-4">
                                <label for="actores_id">Actor</label>
                                <select class="form-control" id="actores_id" name="actores_id">
                                    <?php echo $optionActores; ?>
                                </select>

                            </div>

                            <!-- Total Inversión -->
                            <div class="form-group col-md-4">
                                <label for="valor_proyecto">Total Inversión</label>
                                <input type="text" class="form-control" id="valor_proyecto" name="valor_proyecto"
                                    value="" disabled>
                            </div>

                            <!-- Observaciones -->
                            <div class="form-group col-md-8">
                                <label for="observaciones">Observaciones <span class="text-danger">*</span></label>
                                <textarea required placeholder="Ingrese una nueva observación del proyecto"
                                    class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>
                        </div>

                        <div class="form-row text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button style="border-radius: 12px;" type="button" class="btn btn-danger"
                                    data-dismiss="modal" onclick="location.reload();">Cancelar</button>
                                <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>

                    <div id="contenedorObservaciones" name="contenedorObservaciones"></div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="archivoModal" tabindex="-1" aria-labelledby="archivoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archivoModalLabel">Adjuntos</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="archivoModalBody">
                    <!-- Aquí se muestra imagen/pdf -->
                </div>
            </div>
        </div>
    </div>

    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/ministerios_proyectos.js"></script>
    <script type="text/javascript" src="./admin/js/datatables/jquery.dataTables.min.js"></script>
    <link href="./admin/js/datatables/jquery.dataTables.min.css" rel="stylesheet" />

</body>

</html>