<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';

//Permisos
$view = SessionData::getPermission(14);
$create = SessionData::getPermission(15);
$edit = SessionData::getPermission(16);
//Validación
if (!$view) {
    require 'permiso_denegado.php';
}

include './admin/classes/Visitasg.php';
include './admin/classes/Departamento.php';


//Información de Vistas
$arr = Visitasg::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Primera Dama';


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
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
                        <h4 class="text-body mb-0">Control de actividades</h4>
                      </div>
                    </div>
                  </div>
            <!-- INICIO DE TABLA CONTROL DE ACTIVIDADES -->
                    <div class="table-responsive m-4">
                        <table id="dynamictable" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th scope="col">Ver</th>
                            <!-- <th scope="col">Provincia</th> -->
                            <th scope="col">Municipio</th>
                            <th scope="col">Población Impactada</th>
                            <th scope="col">Inversión</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Actividad</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Link</th>
                            <th scope="col">Imagen</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php if ($isvalid && !empty($arr)): ?>
                            <?php foreach ($arr as $item): ?>
                          <tr>
                              <td>
                                <form action="reporte_visitag.php" method="POST"
                                    target="_blank" style="display:inline;">
                                    <input type="hidden" id="reporte" name="reporte"
                                    value="<?= htmlspecialchars($item['id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-primary" style="margin-left: 1rem;" title="Ver"> <i class="uil uil-eye fs-8"></i></button>
                                    <button style="margin-top: 10px; margin-left: 1rem;"type="button"
                                    class="btn btn-sm btn-primary" title="Editar"
                                    onclick="VISITASG.editData(<?= $item['id'] ?>)">
                                    <i class="uil uil-edit"></i>
                                    </button>
                                </form>
                                </td>
                                    <!-- <td>= htmlspecialchars($item['provincia']); </td> -->
                                    <td><?= htmlspecialchars($item['municipio']); ?></td>
                                    <td><?= htmlspecialchars($item['poblacion']); ?></td>
                                    <td><?= htmlspecialchars($item['inversion']); ?></td>
                                    <td><?= htmlspecialchars($item['campana']); ?></td>
                                    <td><?= htmlspecialchars($item['actividad']); ?></td>
                                    <td><?= htmlspecialchars($item['date']); ?></td>
                                <td><?php if (!empty($item['link'])): ?>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        title="Ver"
                                        onclick="window.open('<?= htmlspecialchars($item['link']); ?>', '_blank')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                        <?php endif; ?>
                                </td>
                                <td class="text-primary">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <?php if (!empty($item["foto$i"])): ?>
                                    <a href="<?= htmlspecialchars($baseImgUrl . $item["foto$i"]) ?>"
                                    target="_blank" title="Imagen <?= $i ?>">
                                    <i class="fas fa-images"></i>
                                    </a>
                                <?php endif; ?>
                                <?php endfor; ?>
                                </td>
                            </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> 
            <!-- FINAL DE TABLA CONTROL DE ACTIVIDADES -->
            <!-- INICIO DE MODAL DE INFO DE AVANCES  -->
                                <div class="card-body">
                                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <form id="editForm" class="w-100">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary">
                                                                <h5 class="modal-title" id="editModalLabel" style="color: white;">
                                                                <i class="uil uil-edit"></i> Editar Visita
                                                                </h5>
                                                                <button type="button" class="btn-close p-1" data-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" id="id" name="id">
                                                                <div class="form-group" style="text-align: center; margin-top: 20px;">
                                                                    <label id="tbl_departamento_id-label" for="tbl_departamento_id">
                                                                    <i class="fas fa-map-marked-alt"></i> Departamento
                                                                    </label>
                                                                    <select class="form-control ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id">
                                                                        <?php echo $optionDep; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-6 col-md-12">
                                                                    <div class="form-floating">
                                                                        <select class="form-control" 
                                                                            id="tbl_municipio_id" 
                                                                            name="tbl_municipio_id" 
                                                                            onchange="DEPARTAMENTO.getVeredasByMunicipioId();">
                                                                        </select>
                                                                        <label for="tbl_municipio_id">
                                                                            <i class="fas fa-map-pin"></i> Municipio
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            <!-- <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <select class="form-control" id="provincia" name="provincia">
                                                                        <option value="Seleccione">Seleccione</option>
                                                                        <option value="Soto_Norte">Soto Norte</option>
                                                                        <option value="Guanenta">Guanentá</option>
                                                                        <option value="Garcia_Rovira">García Rovira</option>
                                                                        <option value="Comunera">Comunera</option>
                                                                        <option value="Velez">Vélez</option>
                                                                        <option value="Metropolitana">Metropolitana</option>
                                                                        <option value="Yariguíes">Yariguíes</option>
                                                                    </select>
                                                                    <label for="provincia">
                                                                        <i class="fas fa-map"></i> Provincia
                                                                    </label>
                                                                </div>
                                                            </div> -->
                                                            <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control" id="poblacion" name="poblacion" placeholder="Población Impactada">
                                                                    <label for="poblacion">
                                                                        <i class="fas fa-users"></i> Población Impactada
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <textarea class="form-control" placeholder="Descripción Actividad" id="desc_actividad" name="desc_actividad" style="height: 150px;"></textarea>
                                                                    <label for="desc_actividad">
                                                                        <i class="fas fa-align-left"></i> Descripción Actividad
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control" id="inversion" name="inversion" placeholder="Inversión Estimada">
                                                                    <label for="inversion">
                                                                        <i class="fas fa-dollar-sign"></i> Inversión Estimada
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <select class="form-control" id="campana" name="campana">
                                                                        <option value="Seleccione">Seleccione</option>
                                                                        <option value="Niños al estadio">Niños al estadio</option>
                                                                        <option value="Niños al cine">Niños al cine</option>
                                                                        <option value="Niños al teatro">Niños al teatro</option>
                                                                        <option value="Es tiempo de aprender">Es tiempo de aprender</option>
                                                                        <option value="Niños al estadio - Optometría">Niños al estadio - Optometría</option>
                                                                        <option value="Metale mente">Metale mente</option>
                                                                    </select>
                                                                    <label for="campana">
                                                                        <i class="fas fa-bullhorn"></i> Nombre
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control" id="actividad" name="actividad" placeholder="Actividad">
                                                                    <label for="actividad">
                                                                        <i class="fas fa-tasks"></i> Actividad
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-md-12 text-center">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control" id="link" name="link" placeholder="Link Mediático">
                                                                    <label for="link">
                                                                        <i class="fas fa-link"></i> Link Mediático
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <!-- INICIO SUBIR IMAGEN -->
                                                            <div class="col-sm-12">
                                                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                                                <div style="width: 240px;">
                                                                <p class="text-center mb-2 fw-bold">Foto 1</p>
                                                                <iframe id="ifm1" name="ifm1" src="upload.php" scrolling="no" frameborder="0"
                                                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                                                </div>

                                                                <div style="width: 240px;">
                                                                <p class="text-center mb-2 fw-bold">Foto 2</p>
                                                                <iframe id="ifm2" name="ifm2" src="upload.php" scrolling="no" frameborder="0"
                                                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                                                </div>

                                                                <div style="width: 240px;">
                                                                <p class="text-center mb-2 fw-bold">Foto 3</p>
                                                                <iframe id="ifm3" name="ifm3" src="upload.php" scrolling="no" frameborder="0"
                                                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                                                </div>

                                                                <div style="width: 240px;">
                                                                <p class="text-center mb-2 fw-bold">Foto 4</p>
                                                                <iframe id="ifm4" name="ifm4" src="upload.php" scrolling="no" frameborder="0"
                                                                        style="width: 100%; height: 250px; overflow: visible;"></iframe>
                                                                </div>
                                                            </div>
                                                            <!-- FIN SUBIR IMAGEN -->
                                                        <!-- INICIO DE BOTON CANCELAR Y GUARDAR -->
                                                                    <div class="col-12">
                                                        <div class="row g-3 justify-content-end">
                                                            <div class="col-auto">
                                                            <button type="button" onclick="UTIL.clearForm('editForm');" class="btn btn-phoenix-secondary px-5">
                                                                Cancelar
                                                            </button>
                                                            </div>
                                                            <div class="col-auto">
                                                            <button class="btn btn-primary px-5" type="button" onclick="VISITASG.saveData();">
                                                                Guardar
                                                            </button>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <!-- FIN DE BOTON DE CANCELAR Y GUARDAR -->
                                                        </div>
                                                        </form>
                                                        </div>                
                                                    </div>
                                                </div>
       <!-- FINAL DE MODAL DE INFO DE AVANCES  -->
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
    <?php include './admin/include/footer.php'; ?>
    

    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="admin/js/cuadro_control_visitasg.js"></script>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script src="admin/js/detalle_visitasg.js"></script>
    <script src="admin/js/departamentoDama.js"></script>
    <!-- prism Js -->
    <script src="assets/js/plugins/prism.js"></script>
    <script>
        setTimeout(function() {
            DEPARTAMENTO.getMunicipios();
        }, 1000);
    </script>
    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>