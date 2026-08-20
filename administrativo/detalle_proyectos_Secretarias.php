<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';

include './admin/classes/Proyectos.php';

// Información de Proyectos por Id
$arr = Proyectos::getAll(["id" => $_REQUEST["id"]]);
$isvalid = $arr['output']['valid'];
$proyecto = $arr['output']['response'][0];

//Información de Proyectos
$arrobser = Proyectos::getAllobser(null);
$isvalid = $arrobser['output']['valid'];
$arrobser = $arrobser['output']['response'];
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
                    <h5>Detalle y actualización del proyecto <?php echo $sigla ?></h5>
                    </div>
                </div>
              </div>
                    <!-- INICIO DE FORMULARIO DETALLE Y ACTUALIZACIÓN DEL PROYECTO -->
                    <div class="card-body p-0">
                        <div class="p-4 code-to-copy">
                                    <form class="needs-validation row g-3 mb-6" novalidate>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="hidden" name="idProyecto" id="idProyecto" value="<?php echo $proyecto['id'] ?>">
                                                    <input class="form-control datetimepicker flatpickr-input" 
                                                        id="date" 
                                                        name="date" 
                                                        type="date" 
                                                        placeholder="dd/mm/yyyy" 
                                                        value="<?php echo $proyecto['date'] ?>" 
                                                        data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' 
                                                        readonly="readonly">
                                                    <label for="date">Fecha</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="provincia" name="provincia"
                                                        value="<?php echo $proyecto['provincia'] ?>" placeholder="Provincia" readonly>
                                                    <label for="provincia">Provincia</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="tbl_departamento_id"
                                                        name="tbl_departamento_id" 
                                                        value="<?php echo $proyecto['tbl_departamento_id'] ?>" 
                                                        placeholder="Departamento" readonly>
                                                    <label for="tbl_departamento_id">Departamento</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="municipio" 
                                                        name="municipio" 
                                                        value="<?php echo $proyecto['municipio'] ?>" 
                                                        placeholder="Municipio Beneficiado" readonly>
                                                    <label for="municipio">Municipio Beneficiado</label>
                                                    <input type="hidden" class="form-control" id="tbl_municipio_id"
                                                        name="tbl_municipio_id" 
                                                        value="<?php echo $proyecto['tbl_municipio_id'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input autocomplete="off" type="text" 
                                                        class="form-control" id="proyecto" name="proyecto"
                                                        value="<?php echo $proyecto['proyecto'] ?>" 
                                                        placeholder="Objeto del proyecto" readonly>
                                                    <label for="proyecto">Objeto del proyecto</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="secretaria"
                                                        name="secretaria" 
                                                        value="<?php echo $proyecto['secretaria'] ?>" 
                                                        placeholder="Secretaría o Dependencia Encargada" readonly>
                                                    <label for="secretaria">Secretaría o Dependencia Encargada</label>
                                                    <input type="hidden" class="form-control" id="tbl_secretarias_id"
                                                        name="tbl_secretarias_id" 
                                                        value="<?php echo $proyecto['tbl_secretarias_id'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="number" class="form-control" 
                                                        onKeyPress="return soloNumeros(event);" 
                                                        id="valor_proyecto" 
                                                        name="valor_proyecto" 
                                                        value="<?php echo $proyecto['valor_proyecto'] ?>" 
                                                        placeholder="Total Inversión" readonly>
                                                    <label for="valor_proyecto">Total Inversión</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control datetimepicker flatpickr-input" 
                                                        id="date_inicio" 
                                                        name="date_inicio" 
                                                        value="<?php echo $proyecto['date_inicio'] ?>" 
                                                        placeholder="Fecha Inicio" 
                                                        data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' 
                                                        readonly="readonly">
                                                    <label for="date_inicio">Fecha Inicio</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input autocomplete="off" type="text" 
                                                        class="form-control" id="contratista" 
                                                        name="contratista" 
                                                        value="<?php echo $proyecto['contratista'] ?>" 
                                                        placeholder="Ingrese el nombre de o los contratistas">
                                                    <label for="contratista">Contratista</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input autocomplete="off" type="text" 
                                                        class="form-control" id="interventoria" 
                                                        name="interventoria" 
                                                        value="<?php echo $proyecto['interventoria'] ?>" 
                                                        placeholder="Ingrese el nombre de o los Interventores">
                                                    <label for="interventoria">Interventoría</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input autocomplete="off" type="number" 
                                                        class="form-control" id="plazo_construccion" 
                                                        name="plazo_construccion" 
                                                        value="<?php echo $proyecto['plazo_construccion'] ?>" 
                                                        placeholder="Tiempo de proyecto en meses" readonly>
                                                    <label for="plazo_construccion">Plazo meses</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control datetimepicker flatpickr-input" 
                                                        id="fecha_entrega" 
                                                        name="fecha_entrega" 
                                                        value="<?php echo $proyecto['fecha_entrega'] ?>" 
                                                        placeholder="Fecha Entrega" 
                                                        data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}'>
                                                    <label for="fecha_entrega">Fecha Entrega</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" 
                                                        id="estado" 
                                                        name="estado" 
                                                        value="<?php echo $proyecto['estado'] ?>" 
                                                        placeholder="Estado Actual" readonly>
                                                    <label for="estado">Estado Actual</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-control" id="estado" name="estado">
                                                        <option value="Seleccione">Seleccione</option>
                                                        <option value="Estudios Previos">Estudios Previos</option>
                                                        <option value="Pliego">Pliego</option>
                                                        <option value="Contratado">Contratado</option>
                                                        <option value="Ejecución">Ejecución</option>
                                                        <option value="Terminado">Terminado</option>
                                                        <option value="Liquidación">Liquidación</option>
                                                    </select>
                                                    <label for="estado">Nuevo Estado Proyecto</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" 
                                                        id="porcentaje_ejecucion" 
                                                        name="porcentaje_ejecucion" 
                                                        value="<?php echo $proyecto['porcentaje_ejecucion'] ?> %" 
                                                        placeholder="Estado porcentaje de obra o contrato" readonly>
                                                    <label for="porcentaje_ejecucion">Estado porcentaje de obra o contrato</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input onKeyPress="return soloNumeros(event);" type="text" 
                                                        class="form-control" 
                                                        id="porcentaje_ejecucion" 
                                                        name="porcentaje_ejecucion" 
                                                        placeholder="0%" required>
                                                    <label for="porcentaje_ejecucion">Nuevo porcentaje de ejecución de la obra</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control datetimepicker flatpickr-input" 
                                                        id="date_prorroga" 
                                                        name="date_prorroga" 
                                                        value="2024-02-06" 
                                                        placeholder="Fecha Prórroga" 
                                                        data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}'>
                                                    <label for="date_prorroga">Fecha Prórroga</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input onKeyPress="return soloNumeros(event);" type="number" 
                                                        class="form-control" 
                                                        id="dias_prorroga" 
                                                        name="dias_prorroga" 
                                                        placeholder="Días Prórroga">
                                                    <label for="dias_prorroga">Días Prórroga</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-floating">
                                                    <input autocomplete="off" type="number" 
                                                        class="form-control" 
                                                        id="adicion" 
                                                        name="adicion" 
                                                        placeholder="Ingrese el Valor de la adición">
                                                    <label for="adicion">Adición Presupuestal</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-8">
                                                <div class="form-floating">
                                                    <textarea required placeholder="Ingrese observaciones de la obra" 
                                                            class="form-control" 
                                                            id="observaciones" 
                                                            name="observaciones"></textarea>
                                                    <label for="observaciones">Observaciones</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="row g-3 justify-content-end">
                                                    <div class="col-auto">
                                                        <button class="btn btn-primary" type="button" onclick="DETALLE_PROYECTO.updatedata();">Actualizar
                                                        Información</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- INICIO DE TABLA -->
                                        <div class="px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                                            <div class="p-4 code-to-copy">
                                                <div class="table-responsive">
                                                    <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Item</th>
                                                                <th>Observación</th>
                                                                <th>Fecha</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $c = count($arrobser);
                                                                if ($isvalid) {
                                                                    for ($i = 0; $i < $c; $i++) {
                                                                ?>
                                                                    <tr>
                                                                        <td> <?php echo $arrobser[$i]['id']; ?></td>
                                                                        <td> <?php echo $arrobser[$i]['observaciones']; ?></td>
                                                                        <td> <?php echo $arrobser[$i]['dtcreate']; ?></td>

                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                        </div>
                    </div>
                   
          </div>            
        </div>
        <?php include './admin/include/footer.php';?>
    </div>

    <?php include 'admin/include/gerenic_script.php'; ?>

    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>


    <script type="text/javascript" src="admin/js/detalle_proyectos.js"></script>

    <?php if (isset($_REQUEST["tec_proyecto_id"])) : ?>
        <script>
            var id = "<?php echo $_REQUEST["tec_proyecto_id"] ?>";
            DETALLE_PROYECTO.edit(id);
        </script>
    <?php endif ?>
    <script>
        new DataTable('#example', {
            select: true
        });
    </script>

</body>

</html>