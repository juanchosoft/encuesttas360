<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Formula.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Validación de permisos para el módulo de Fórmulas
// ID 46: Ver Fórmulas, ID 47: Crear Fórmulas, ID 48: Editar Fórmulas, ID 49: Permisos Fórmulas
$view = SessionData::getPermission(46);
$create = SessionData::getPermission(47);
$edit = SessionData::getPermission(48);
$permits = SessionData::getPermission(49);

// Si no tiene permiso de ver, redirigir a página de permiso denegado
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Información de Fórmulas
$arr = Formula::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Fórmulas e Indicadores Estadísticos';

// Obtener tipos de indicadores únicos
$arrFormulas = Formula::getAll(null);
$formulas = $arrFormulas['output']['response'];
$tipos_indicadores = array_unique(array_column($formulas, 'tipo_indicador'));
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">

<body>
  <main class="main" id="top">
    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">
      <div class="mt-4">
        <div class="col-12 col-xl-12">
          <div class="col-11 col-xl-11 mx-auto">
            <div class="mb-9">

              <!-- FORMULARIO DE FÓRMULAS -->
              <div class="card shadow-none border my-4" data-component-card="data-component-card">
                <div class="card-header p-4 border-bottom bg-body">
                  <div class="row g-3 justify-content-between align-items-center">
                    <div class="col-12 col-md">
                      <h4 class="text-body mb-0">
                        <i class="fas fa-calculator me-2" style="color:black !important;font-size: 1.9rem !important;"></i>
                        <span id="spanModulo"><?php echo $modulo; ?></span>
                      </h4>
                      <p class="text-muted small mb-0 mt-2">Gestiona las fórmulas matemáticas e indicadores del sistema</p>
                    </div>
                    <?php if ($create): ?>
                    <div class="col-auto">
                      <button onclick="FORMULAS.showUploadModal()" class="btn btn-success px-4" type="button">
                        <i class="fas fa-file-upload me-2"></i>Importar CSV
                      </button>
                    </div>
                    <?php endif; ?>
                    <div class="col-auto">
                      <a href="formulas_template.csv" download class="btn btn-outline-primary px-4">
                        <i class="fas fa-download me-2"></i>Plantilla CSV
                      </a>
                    </div>
                  </div>
                </div>

                <div class="card-body p-0">
                  <div class="p-4 code-to-copy">
                    <form class="row g-3 mb-6" id="formFormulas" role="form" autocomplete="false">
                      <input type="hidden" name="op" id="op" value="formulassave" />
                      <input type="hidden" name="id" id="id" value="0" />

                      <!-- Indicador -->
                      <div class="col-sm-12 col-md-6">
                        <div class="form-floating">
                          <input class="form-control" type="text" id="indicador" name="indicador"
                            placeholder="Nombre del indicador" required />
                          <label for="indicador">Indicador <span class="text-danger">*</span></label>
                        </div>
                      </div>

                      <!-- Sigla -->
                      <div class="col-sm-12 col-md-3">
                        <div class="form-floating">
                          <input type="text" class="form-control" id="sigla" name="sigla"
                            placeholder="Sigla o abreviatura" required />
                          <label for="sigla">Sigla <span class="text-danger">*</span></label>
                        </div>
                      </div>

                      <!-- Tipo de Indicador -->
                      <div class="col-sm-12 col-md-3">
                        <div class="form-floating">
                          <select class="form-select" id="tipo_indicador" name="tipo_indicador"">
                            <option value="">Seleccione un tipo...</option>
                            <?php foreach ($tipos_indicadores as $tipo): ?>
                              <?php if (!empty($tipo)): ?>
                                <option value="<?= htmlspecialchars($tipo) ?>">
                                  <?= htmlspecialchars($tipo) ?>
                                </option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                          <label for="tipo_indicador">Tipo de Indicador<span class="text-danger">*</span></label>
                        </div>
                      </div>

                      <!-- Fórmula -->
                      <div class="col-12">
                        <div class="form-floating">
                          <textarea class="form-control" id="formula" name="formula"
                            placeholder="Fórmula matemática" style="height: 80px;" required></textarea>
                          <label for="formula">Fórmula <span class="text-danger">*</span></label>
                        </div>
                        <small class="text-muted">
                          <i class="fas fa-info-circle me-1"></i>Ejemplo: IF = (Respuestas Positivas / Total Respuestas) * 100
                        </small>
                      </div>

                      <!-- Explicación -->
                      <div class="col-12">
                        <div class="form-floating">
                          <textarea class="form-control" id="explicacion" name="explicacion"
                            placeholder="Explicación detallada" style="height: 100px;"></textarea>
                          <label for="explicacion">Explicación</label>
                        </div>
                      </div>

                      <!-- Observaciones -->
                      <div class="col-12">
                        <div class="form-floating">
                          <textarea class="form-control" id="observaciones" name="observaciones"
                            placeholder="Observaciones" style="height: 80px;"></textarea>
                          <label for="observaciones">Observaciones</label>
                        </div>
                      </div>

                      <!-- Enunciado Antes -->
                      <div class="col-sm-12 col-md-6">
                        <div class="form-floating">
                          <textarea class="form-control" id="enunciado_antes" name="enunciado_antes"
                            placeholder="Enunciado anterior" style="height: 70px;"></textarea>
                          <label for="enunciado_antes">Enunciado Antes</label>
                        </div>
                      </div>

                      <!-- Enunciado Ahora -->
                      <div class="col-sm-12 col-md-6">
                        <div class="form-floating">
                          <textarea class="form-control" id="enunciado_ahora" name="enunciado_ahora"
                            placeholder="Enunciado actual" style="height: 70px;"></textarea>
                          <label for="enunciado_ahora">Enunciado Ahora</label>
                        </div>
                      </div>

                      <!-- Notas Adicionales -->
                      <div class="col-12">
                        <div class="form-floating">
                          <textarea class="form-control" id="notas_adicionales" name="notas_adicionales"
                            placeholder="Notas adicionales" style="height: 80px;"></textarea>
                          <label for="notas_adicionales">Notas Adicionales</label>
                        </div>
                      </div>

                      <!-- Habilitado -->
                      <div class="col-sm-12 col-md-6">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="habilitado"
                            name="habilitado" value="si" checked>
                          <label class="form-check-label" for="habilitado">
                            Fórmula Habilitada
                          </label>
                        </div>
                        <small class="text-muted">Desmarque si desea deshabilitar esta fórmula</small>
                      </div>

                      <!-- Botones -->
                      <div class="col-12">
                        <div class="row g-3 justify-end">
                          <div class="col-auto">
                            <button type="button" onclick="FORMULAS.emptyCells();"
                              class="btn btn-phoenix-secondary px-5">Cancelar</button>
                          </div>
                          <?php if ($create && $edit): ?>
                          <div class="col-auto">
                            <button class="btn btn-primary px-5" type="button"
                              onclick="FORMULAS.validateData();">Guardar</button>
                          </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- TABLA DE FÓRMULAS -->
              <div class="p-4 code-to-copy">
                <div class="table-responsive">
                  <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                    <thead>
                      <tr class="border-1">
                        <th>Acciones</th>
                        <th>Sigla</th>
                        <th>Tipo</th>
                        <th>Indicador</th>
                        <th>Fórmula</th>
                        <th>Explicación</th>
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <?php if ($isvalid && count($arr) > 0): ?>
                        <?php foreach ($arr as $item): ?>
                          <tr>
                            <td>
                              <?php if ($edit): ?>
                              <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                onclick="FORMULAS.editData(<?= htmlspecialchars($item['id']) ?>)">
                                <i class="uil uil-edit"></i>
                              </button>
                              <?php endif; ?>
                              <button type="button" class="btn btn-sm btn-success" title="Ver Detalles"
                                onclick="FORMULAS.showDetails(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                                <i class="uil uil-eye"></i>
                              </button>
                            </td>
                            <td>
                              <span class="badge bg-primary"><?= htmlspecialchars($item['sigla']) ?></span>
                            </td>
                            <td>
                              <?php if (!empty($item['tipo_indicador'])): ?>
                                <span class="badge bg-info"><?= htmlspecialchars($item['tipo_indicador']) ?></span>
                              <?php else: ?>
                                <span class="text-muted small">N/A</span>
                              <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['indicador']) ?></td>
                            <td>
                              <code class="small"><?= htmlspecialchars(substr($item['formula'], 0, 50)) ?><?= strlen($item['formula']) > 50 ? '...' : '' ?></code>
                            </td>
                            <td class="small text-muted">
                              <?= htmlspecialchars(substr($item['explicacion'] ?? '', 0, 60)) ?><?= strlen($item['explicacion'] ?? '') > 60 ? '...' : '' ?>
                            </td>
                            <td>
                              <?php if (isset($item['habilitado']) && $item['habilitado'] === 'si'): ?>
                                <span class="badge bg-success">
                                  <i class="fas fa-check-circle me-1"></i>Activo
                                </span>
                              <?php else: ?>
                                <span class="badge bg-danger">
                                  <i class="fas fa-times-circle me-1"></i>Inactivo
                                </span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No hay fórmulas registradas</p>
                          </td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- MODAL: Detalles de Fórmula -->
      <div class="modal fade" id="modalDetalles" tabindex="-1" data-bs-backdrop="static" aria-labelledby="tituloModalDetalles" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content border-0 shadow-lg">

            <!-- Header -->
            <div class="modal-header modal-header-gradient-purple">
              <div class="d-flex align-items-center">
                <div class="modal-icon-circle">
                  <i class="fas fa-calculator text-white fs-4"></i>
                </div>
                <div>
                  <h5 class="modal-title text-white mb-0" id="tituloModalDetalles">
                    <i class="fas fa-info-circle me-2"></i>Detalles de la Fórmula
                  </h5>
                  <p class="text-white-50 small mb-0 mt-1">Información completa del indicador</p>
                </div>
              </div>
              <button class="btn btn-link p-2" type="button" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times fs-5 text-white"></i>
              </button>
            </div>

            <!-- Body con scroll interno -->
            <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto; background-color: #f8f9fa;">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-4" id="detallesContent">
                  <!-- Se llena dinámicamente con JavaScript -->
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer modal-footer-light">
              <button class="btn btn-outline-secondary px-4" type="button" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cerrar
              </button>
            </div>

          </div>
        </div>
      </div>

      <!-- MODAL: Importar CSV -->
      <div class="modal fade" id="modalUploadCSV" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">

            <!-- Header -->
            <div class="modal-header modal-header-gradient-purple">
              <div class="d-flex align-items-center">
                <div class="modal-icon-circle">
                  <i class="fas fa-file-upload text-white fs-4"></i>
                </div>
                <div>
                  <h5 class="modal-title text-white mb-0" id="modalUploadLabel">
                    <i class="fas fa-upload me-2"></i>Importar Fórmulas desde CSV
                  </h5>
                  <p class="text-white-50 small mb-0 mt-1">Carga masiva de indicadores y fórmulas</p>
                </div>
              </div>
              <button class="btn btn-link p-2" type="button" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times fs-5 text-white"></i>
              </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4" style="background-color: #f8f9fa;">
              <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Instrucciones:</strong> El archivo CSV debe tener las columnas:
                <br>
                <code class="small">INDICADOR, SIGLA, TIPO_INDICADOR, FORMULA, EXPLICACION, OBSERVACIONES, ENUNCIADO_ANTES, ENUNCIADO_AHORA, NOTAS_ADICIONALES</code>
              </div>

              <form id="formUploadCSV">
                <div class="mb-3">
                  <label for="csvFile" class="form-label fw-bold">
                    <i class="fas fa-file-csv me-2 text-success"></i>Selecciona el archivo CSV
                  </label>
                  <input type="file" class="form-control form-control-lg" id="csvFile" accept=".csv" required>
                </div>
                <div class="form-check form-switch form-switch-lg mb-3">
                  <input class="form-check-input" type="checkbox" id="csvHasHeader" checked>
                  <label class="form-check-label fw-semibold" for="csvHasHeader">
                    <i class="fas fa-check-circle me-1 text-primary"></i>
                    El archivo tiene encabezados en la primera fila
                  </label>
                </div>
              </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer modal-footer-light">
              <button class="btn btn-outline-secondary px-4" type="button" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cancelar
              </button>
              <button class="btn btn-success px-4" type="button" onclick="FORMULAS.uploadCSV()">
                <i class="fas fa-upload me-2"></i>Cargar Archivo
              </button>
            </div>

          </div>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </main>

  <!-- Required Js -->
  <?php include 'admin/include/gerenic_script.php'; ?>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
  <?php include './admin/include/generic_dataTables.php'; ?>

  <script type="text/javascript" src="admin/js/formulas.js"></script>
  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>
