<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/IngresoInformacion.php';

// Permisos
$view = SessionData::getPermission(61);
if (!$view) {
    require 'permiso_denegado.php';
}

//Información de Ingreso de Informacion
$arr = IngresoInformacion::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];

?>

  <body>
    <main class="main" id="top">
    <?php
    include './admin/include/navbar.php';
    ?>
    <?php include './admin/include/header.php';
    ?>
      <div class="content">
        <div class="mt-4">
            <div class="row g-4">
                <div class="col-11 col-xl-11 mx-auto">
                    <div class="mb-9">
                        <div class="card shadow-none border my-4" data-component-card="data-component-card">
                            
                            <!--INICIO TABLA DE DATOS DE INGRESO INFORMACION -->
                              <div class="col-md-12 p-4">
                                <div style="overflow-x: auto;">
                                  <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                                    <thead>
                                        <tr class="border-1">
                                            <th>Editar</th>
                                            <th>Eliminar</th>
                                            <th>Fecha</th>
                                            <th>Departamento</th>
                                            <th>Municipio</th>
                                            <th>Vereda</th>
                                            <th>Latitud</th>
                                            <th>Longitud</th>
                                            <th>Factor</th>
                                            <th>Valor</th>
                                            <th>Fotos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($isvalid && !empty($arr)): ?>
                                        <?php foreach ($arr as $item): ?>
                                        <tr id="fila_<?= htmlspecialchars($item['id']) ?>">
                                        <td>
                                            <button class="btn btn-sm btn-primary editar-informacion" style="margin-left: 1rem;" title="Editar"
                                                data-toggle="modal" data-target="#modalEditarInformacion"
                                                data-id="<?= htmlspecialchars($item['id']) ?>"
                                                data-longitud="<?= htmlspecialchars($item['longitud']) ?>"
                                                data-latitud="<?= htmlspecialchars($item['latitud']) ?>"
                                                data-fecha="<?= htmlspecialchars($item['dtcreate']) ?>"
                                                data-departamento="<?= htmlspecialchars($item['departamento']) ?>"
                                                data-municipio="<?= htmlspecialchars($item['municipio']) ?>"
                                                data-vereda="<?= htmlspecialchars($item['vereda']) ?>"
                                                data-factor="<?= htmlspecialchars($item['factor']) ?>"
                                                data-valor="<?= htmlspecialchars($item['valor']) ?>">
                                                <i class="uil uil-edit"></i>
                                                </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar"
                                                        onclick="INGRESO_INFORMACION.delete(<?= htmlspecialchars($item['id']) ?>)">
                                                        <i class="uil uil-trash-alt"></i>
                                                    </button>
                                                </td>
                                            <td class="columna-fecha"><?= htmlspecialchars($item['dtcreate']) ?></td>
                                            <td class="columna-departamento"><?= htmlspecialchars($item['departamento']) ?></td>
                                            <td class="columna-municipio"><?= htmlspecialchars($item['municipio']) ?></td>
                                            <td class="columna-vereda"><?= htmlspecialchars($item['vereda']) ?></td>
                                            <td class="columna-longitud"><?= htmlspecialchars($item['latitud']) ?></td>
                                            <td class="columna-longitud"><?= htmlspecialchars($item['longitud']) ?></td>
                                            <td class="columna-factor"><?= htmlspecialchars($item['factor']) ?></td>
                                            <td class="columna-valor"><?= htmlspecialchars($item['valor']) ?></td><td>
                                                <?php if ($item['foto1']): ?>
                                                    <button type="button" class="btn btn-sm btn-primary" title="Imagen 1"
                                                        onclick="INGRESO_INFORMACION.openImage('<?= htmlspecialchars($item['foto1']) ?>')">
                                                        <i class="uil uil uil-image fs-9"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No se encontraron registros.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                              <!-- FIN TABLA DE DATOS DE INGRESO INFORMACION -->
                        </div>
                    </div>
                </div>
            </div>
                <!-- INICIO MODAL PARA EDITAR INFORMACION -->
                <div class="modal fade" id="modalEditarInformacion" tabindex="-1" aria-labelledby="modalEditarInformacionLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="modalEditarInformacionLabel" style="color:white;">Editar Información</h5>
                        <button type="button" class="close border-0 bg-transparent text-white" data-dismiss="modal" aria-label="Cerrar" style="margin-left: 550px;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form id="formEditarInformacion">
                          <input type="hidden" id="modalId" name="id">
                          <div class="mb-3">
                            <label for="modalFecha" class="form-label">Fecha</label>
                            <input type="text" class="form-control" id="modalFecha" name="modalFecha" readonly>
                          </div>
                          <div class="mb-3">
                            <label for="modalDepartamento" class="form-label">Departamento</label>
                            <input type="text" class="form-control" id="modalDepartamento" name="modalDepartamento" readonly>
                          </div>
                          <div class="mb-3">
                            <label for="modalMunicipio" class="form-label">Municipio</label>
                            <input type="text" class="form-control" id="modalMunicipio" name="modalMunicipio" readonly>
                          </div>
                          <div class="mb-3">
                            <label for="modalVereda" class="form-label">Vereda</label>
                            <input type="text" class="form-control" id="modalVereda" name="modalVereda" readonly>
                          </div>
                          <div class="mb-3">
                            <label for="modalFactor" class="form-label">Factor</label>
                            <input type="text" class="form-control" id="modalFactor" name="modalFactor" readonly>
                          </div>
                          <div class="mb-3">
                            <label for="modalValor" class="form-label">Valor</label>
                            <input type="number" class="form-control" id="modalValor" name="modalValor">
                          </div>
                          <div class="mb-3">
                            <label class="form-label"for="modalLogitud">Logitud</label>
                            <input style="text-align: center" type="text" class="form-control input-icon"
                                                    id="modalLogitud" name="modalLogitud">
                          </div>
                          <div class="mb-3">
                            <label class="form-label" for="modalLatitud">Latitud</label>
                            <input style="text-align: center" type="text" class="form-control input-icon"
                                                    id="modalLatitud" name="modalLatitud">
                          </div>
                        </form>
                      </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                          <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar Cambios</button>
                        </div>
                    </div>
                  </div>
                </div>
   <!-- FIN MODAL EDITAR INFORMACION -->
    <script>
    $(document).ready(function() {
        $('.editar-informacion').on('click', function() {
            let id = $(this).data('id');
            let fecha = $(this).data('fecha');
            let departamento = $(this).data('departamento');
            let municipio = $(this).data('municipio');
            let vereda = $(this).data('vereda');
            let factor = $(this).data('factor');
            let valor = $(this).data('valor');
            let longitud = $(this).data('longitud');
            let latitud = $(this).data('latitud');
            $('#modalId').val(id);
            $('#modalFecha').val(fecha);
            $('#modalDepartamento').val(departamento);
            $('#modalMunicipio').val(municipio);
            $('#modalVereda').val(vereda);
            $('#modalFactor').val(factor);
            $('#modalValor').val(valor);
            $('#modalLogitud').val(longitud);
            $('#modalLatitud').val(latitud);
        });
    });
    </script>
    </div>
    <?php
      include './admin/include/footer.php';
    ?>
    </div>
    <!-- Warning Section Ends -->
    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- EDITAR EN LISTADO-->
   
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <?php include './admin/include/generic_dataTables.php'; ?>

    <script type="text/javascript" src="admin/js/ingreso_informacion.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5/css/bootstrap.min.css">
    <!-- Iconos de caja -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>
</body>

</html>