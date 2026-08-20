<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Sondeo.php';

// Validar permisos
$view    = SessionData::getPermission(34);
$create  = SessionData::getPermission(35);
$edit    = SessionData::getPermission(36);
$permits = SessionData::getPermission(37);
if (!$view) { require 'permiso_denegado.php'; exit; }

//Información de Sondeos
$arr = Sondeo::getAll(null);
$isvalidSondeo = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];
$modulo = 'Ingreso de Información de Sondeos';

// Informacion de los cargos publicos
$arrCargosPubResp = CargosPublicos::getAll(null);
$arrCargosPub = $arrCargosPubResp['output']['response'] ?? [];
$optionCargosPub = "";
foreach ($arrCargosPub as $val) {
  $id = htmlspecialchars($val['id'] ?? '', ENT_QUOTES, 'UTF-8');
  $nm = htmlspecialchars($val['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
  $optionCargosPub .= "<option value='{$id}'>{$nm}</option>";
}

// Información de Departamentos
$arrDepResp = Departamento::getAll(null);
$arrDep = $arrDepResp['output']['response'] ?? [];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
  $cd = htmlspecialchars($val['codigo_departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $dp = htmlspecialchars($val['departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $sel = ($val["codigo_departamento"] == Util::getDepartamentoPrincipal()) ? "selected" : "";
  $optionDep .= "<option {$sel} value='{$cd}'>{$cd} - {$dp}</option>";
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<head>
  <style>
    :root{
      --nav-blue:#20427F;
      --nav-blue-2:#132b52;
      --nav-blue-3:#2e58a8;

      --card-radius: 18px;
      --soft-shadow: 0 18px 50px rgba(2,6,23,.10);
      --soft-shadow-2: 0 22px 65px rgba(2,6,23,.14);
      --soft-border: rgba(15,23,42,.08);

      --muted:#64748b;
      --ink:#0f172a;
    }

    /* ✅ respiro (luego lo ajusta JS con la altura real del header) */
    .content{ padding-top: 34px !important; }

    /* ===== Page Head (SaaS) ===== */
    .saas-pagehead{
      margin-top: 18px;
      border-radius: var(--card-radius);
      background:
        radial-gradient(900px 260px at 10% 0%, rgba(32,66,127,.20), transparent 55%),
        radial-gradient(700px 240px at 90% 0%, rgba(46,88,168,.16), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.90));
      border: 1px solid var(--soft-border);
      box-shadow: var(--soft-shadow);
      padding: 18px 18px;
    }
    .saas-title{
      display:flex; align-items:center; gap:12px; flex-wrap:wrap;
    }
    .saas-icon{
      width:46px; height:46px; border-radius:16px;
      display:grid; place-items:center;
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2));
      color:#fff;
      box-shadow: 0 10px 26px rgba(32,66,127,.30);
    }
    .saas-title h3{ margin:0; font-weight:900; letter-spacing:-.3px; color: var(--ink); }
    .saas-sub{ color:var(--muted); font-size:.92rem; margin-top:2px; }

    .chipbar{ display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:7px 10px;
      border-radius: 999px;
      border:1px solid rgba(2,6,23,.08);
      background: rgba(255,255,255,.86);
      font-size:.82rem;
      color:#0f172a;
    }
    .chip i{ color: var(--nav-blue); }

    /* ===== Cards ===== */
    .saas-card{
      border-radius: var(--card-radius);
      border: 1px solid var(--soft-border);
      box-shadow: var(--soft-shadow-2);
      overflow:hidden;
    }
    .saas-card .card-header{
      background: rgba(255,255,255,.92) !important;
      border-bottom: 1px solid var(--soft-border) !important;
      padding: 14px 16px !important;
    }
    .saas-card .card-body{ padding: 16px !important; }

    .section-title{
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      margin: 6px 0 12px;
    }
    .section-title .left{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .badge-soft{
      background: rgba(32,66,127,.10);
      color: var(--nav-blue);
      border: 1px solid rgba(32,66,127,.18);
      font-weight:800;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: .8rem;
      display:inline-flex; align-items:center; gap:8px;
    }
    .section-title h5{ margin:0; font-weight:900; letter-spacing:-.2px; }

    /* ===== Inputs ===== */
    .form-floating > .form-control,
    .form-floating > .form-select{
      border-radius: 14px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
    }
    .form-control:focus, .form-select:focus{
      box-shadow: 0 0 0 .2rem rgba(32,66,127,.12) !important;
      border-color: rgba(32,66,127,.35) !important;
    }
    .help-mini{ color:var(--muted); font-size:.82rem; margin-top:6px; }

    /* ===== Sticky actions ===== */
    .sticky-actions{
      position: sticky;
      bottom: 12px;
      z-index: 20;
      background: rgba(255,255,255,.88);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(15,23,42,.08);
      border-radius: 16px;
      padding: 10px;
      box-shadow: 0 18px 40px rgba(2,6,23,.12);
      margin-top: 12px;
    }

    /* ===== Options + Candidates boxes ===== */
    .soft-box{
      border: 1px solid rgba(15,23,42,.10);
      border-radius: 16px;
      background:
        radial-gradient(700px 210px at 12% 0%, rgba(32,66,127,.10), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.92));
      box-shadow: 0 14px 40px rgba(2,6,23,.08);
    }

    /* ===== Table ===== */
    .table-shell{
      border-radius: var(--card-radius);
      overflow:hidden;
      border: 1px solid var(--soft-border);
    }
    .dt-nowrap th, .dt-nowrap td{ white-space: nowrap; }
    .table thead th{ font-weight:900; color: var(--ink); }
    .table tbody td, .table tbody th{ vertical-align: middle; }

    /* ===== Modal pro ===== */
    .modal-content{
      border-radius: 18px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
      overflow:hidden;
      box-shadow: 0 30px 90px rgba(2,6,23,.25);
    }
    .modal-header.bg-primary{
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2)) !important;
      border-bottom: 1px solid rgba(255,255,255,.16) !important;
    }

    /* small screens */
    @media (max-width: 576px){
      .saas-pagehead{ padding: 14px; }
      .saas-icon{ width:40px; height:40px; border-radius: 13px; }
      .content{ padding-left: 10px; padding-right: 10px; }
    }
  </style>
</head>

<body>
  <main class="main" id="top">
    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">
      <div class="col-11 col-xl-11 mx-auto">

        <!-- ===== PageHead SaaS ===== -->
        <div class="saas-pagehead">
          <div class="saas-title">
            <div class="saas-icon"><i class="fas fa-poll-h"></i></div>
            <div>
              <h3><?= h($modulo) ?></h3>
              <div class="saas-sub">Crea, configura y administra sondeos. Opciones dinámicas, candidatos y control de vigencia.</div>
            </div>
          </div>

          <div class="chipbar">
            <div class="chip"><i class="fas fa-shield-alt"></i> Permisos: <?= $view ? 'View' : '—' ?><?= $create ? ' · Create' : '' ?><?= $edit ? ' · Edit' : '' ?></div>
            <div class="chip"><i class="fas fa-database"></i> Sondeos: <?= (int)count($arr) ?></div>
            <div class="chip"><i class="fas fa-briefcase"></i> Cargos: <?= (int)count($arrCargosPub) ?></div>
          </div>

          <div class="mt-2">
            <span id="spanEncuesta" class="small text-muted"></span>
            <span id="spanModulo" class="d-none"><?= h($modulo) ?></span>
          </div>
        </div>

        <!-- ===== FORM CARD ===== -->
        <div class="saas-card card my-4" data-component-card="data-component-card">
          <div class="card-header">
            <div class="section-title">
              <div class="left">
                <span class="badge-soft"><i class="fas fa-pen-nib"></i>Formulario</span>
                <h5 class="mb-0">Crear / Editar Sondeo</h5>
              </div>
              <div class="text-muted small d-none d-md-block">
                Tip: define el tipo de sondeo y luego agrega las opciones.
              </div>
            </div>
          </div>

          <div class="card-body">
            <form class="row g-3" id="formsondeo" role="form" autocomplete="off">
              <input type="hidden" name="op" id="op" />
              <input type="hidden" name="idSondeo" id="idSondeo" />

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="sondeo" name="sondeo"
                    placeholder="Texto del sondeo a realizar" value="" required>
                  <label for="sondeo">Pregunta<span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="descripcion_sondeo" name="descripcion_sondeo"
                    placeholder="Ingrese la descripción o pregunta del sondeo" value="">
                  <label for="descripcion_sondeo">Descripción del Sondeo</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-floating">
                  <select onchange="OPCIONES.handleTipoPreguntaChange('tipo_sondeo');" class="form-select" id="tipo_sondeo" name="tipo_sondeo" required>
                    <option value="No Aplica">No Aplica</option>
                    <option value="Dicotomica">Dicotómica</option>
                    <option value="Grilla">Tipo Grilla</option>
                    <option value="Preguntas_Ordinales">Preguntas Ordinales</option>
                    <option value="Preguntas_Cardinales">Preguntas Cardinales</option>
                    <option value="Seleccion_Multiple_unica_respuesta">Selección múltiple con única respuesta</option>
                  </select>
                  <label for="tipo_sondeo">Tipo de Sondeo <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-floating">
                  <select class="form-select" id="tipo_inferenciales" name="tipo_inferenciales" required>
                    <option value="Pronostico">Pronóstico</option>
                    <option value="Tendencia">Tendencia</option>
                    <option value="Probabilidad">Probabilidad</option>
                    <option value="Otro">Otro</option>
                  </select>
                  <label for="tipo_inferenciales">Tipo de Inferenciales <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-floating">
                  <select class="form-select" id="aplica_cargos_publicos" name="aplica_cargos_publicos"
                    onchange="SONDEO.handleSondeParaCargoPublicoChange();" required>
                    <option value="no">No</option>
                    <option value="si">Sí</option>
                  </select>
                  <label for="aplica_cargos_publicos">Sondeo para cargo público <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3 cargo-publico-fields d-none">
                <div class="form-floating">
                  <select class="form-select" id="tbl_cargo_publico_id" name="tbl_cargo_publico_id"
                    onchange="SONDEO.handleCargoPublicoChange(this);" required>
                    <?= $optionCargosPub; ?>
                  </select>
                  <label for="tbl_cargo_publico_id">Cargo Público <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" id="habilitado" name="habilitado" value="si" checked>
                  <label class="form-check-label fw-bold" for="habilitado">Sondeo Habilitado</label>
                </div>
                <small class="text-muted">Desmarque si desea deshabilitar este sondeo</small>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-floating">
                  <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" placeholder="Fecha inicio del sondeo" value="">
                  <label for="fecha_inicio">Fecha Inicio</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-floating">
                  <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" placeholder="Fecha fin del sondeo" value="">
                  <label for="fecha_fin">Fecha Fin</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" id="es_trivia" name="es_trivia" value="si">
                  <label class="form-check-label fw-bold" for="es_trivia">Es Trivia</label>
                </div>
                <small class="text-muted">Marque si este sondeo es una trivia</small>
              </div>

              <div class="col-sm-6 col-md-5 departamento-municipio-fields d-none">
                <div class="form-floating">
                  <select onchange="DEPARTAMENTO.getMunicipios(), SONDEO.filterAndShowData();" class="form-select"
                    id="tbl_departamento_id" name="tbl_departamento_id">
                    <?= $optionDep ?>
                  </select>
                  <label for="tbl_departamento_id">Departamento</label>
                </div>
              </div>

              <div class="col-sm-6 col-md-5 departamento-municipio-fields d-none" id="alcaldia-container">
                <div class="form-floating">
                  <select onchange="SONDEO.filterAndShowData();" class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"></select>
                  <label for="tbl_municipio_id">Municipio</label>
                </div>
              </div>

              <!-- Opciones -->
              <div class="col-12">
                <div id="opciones-preguntas" class="soft-box mt-3 p-4">
                  <div class="section-title">
                    <div class="left">
                      <span class="badge-soft"><i class="fas fa-list"></i>Opciones</span>
                      <h5 class="mb-0">Opciones de respuesta</h5>
                    </div>
                    <div class="text-muted small d-none d-md-block">Agrega las opciones según el tipo de sondeo.</div>
                  </div>

                  <div id="opcionesContainer">
                    <p id="noOptionsMessage" class="text-muted" style="display:none;">No hay opciones de respuesta. ¡Añade una!</p>
                  </div>

                  <button type="button" class="btn btn-phoenix-primary btn-sm mt-2" id="addOptionBtn" onclick="OPCIONES.addOptionBtnClick();">
                    <span class="fas fa-plus me-1"></span> Añadir Opción
                  </button>
                </div>
              </div>

              <!-- Tabla candidatos -->
              <div class="col-12">
                <div id="table-container" class="soft-box mt-3 p-4">
                  <div class="section-title">
                    <div class="left">
                      <span class="badge-soft"><i class="fas fa-users"></i>Candidatos</span>
                      <h5 class="mb-0">Candidatos a postular</h5>
                    </div>
                    <div class="text-muted small d-none d-md-block">Filtra por departamento/municipio si aplica.</div>
                  </div>

                  <div class="table-shell">
                    <div class="table-responsive">
                      <table class="table table-striped table-sm fs-9 mb-0" id="candidatosTable">
                        <thead>
                          <tr>
                            <th>Seleccionar</th>
                            <th>Foto</th>
                            <th>Nombre Completo</th>
                            <th>Cargo Público</th>
                            <th>Partido(s) Político(s)</th>
                            <th>Municipio</th>
                            <th>Departamento</th>
                          </tr>
                        </thead>
                        <tbody class="list">
                          <!-- Render JS -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sticky actions -->
              <div class="col-12">
                <div class="sticky-actions">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">
                      <i class="fas fa-info-circle me-1"></i> Tip: activa “cargo público” para mostrar candidatos.
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                      <button type="button" onclick="SONDEO.emptyCells();" class="btn btn-phoenix-secondary px-4">Cancelar</button>
                      <?php if ($create && $edit): ?>
                        <button class="btn btn-primary px-4" type="button" onclick="SONDEO.validateData();">Guardar</button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>

        <!-- ===== TABLE CARD (Listado) ===== -->
        <div class="saas-card card mb-4">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2">
                <span class="badge-soft"><i class="fas fa-table"></i>Listado</span>
                <h5 class="mb-0 fw-bold">Sondeos Registrados</h5>
              </div>
              <div class="text-muted small">Tip: usa buscar para filtrar (DataTable).</div>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-shell">
              <div class="table-responsive">
                <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                  <thead>
                    <tr class="border-1">
                      <th>Vigencia</th>
                      <th>Habilitado</th>
                      <th>Acciones</th>
                      <th>Sondeo</th>
                      <th>Candidatos Políticos</th>
                      <th>Tipo de Inferenciales</th>
                      <th>Fecha</th>
                    </tr>
                  </thead>
                  <tbody class="list">
                    <?php if ($isvalidSondeo && count($arr) > 0): ?>
                      <?php foreach ($arr as $item): ?>
                        <tr>
                          <td>
                            <?php if (isset($item['vigente']) && $item['vigente']): ?>
                              <span class="badge bg-success"><i class="fas fa-calendar-check me-1"></i>Vigente</span>
                            <?php else: ?>
                              <span class="badge bg-secondary"><i class="fas fa-calendar-times me-1"></i>No Vigente</span>
                            <?php endif; ?>
                          </td>

                          <td>
                            <div class="form-check form-switch">
                              <input class="form-check-input toggle-sondeo-habilitado"
                                     type="checkbox"
                                     role="switch"
                                     id="toggleSondeo_<?php echo (int)$item['id']; ?>"
                                     data-sondeo-id="<?php echo (int)$item['id']; ?>"
                                     <?php echo (isset($item['habilitado']) && $item['habilitado'] === 'si') ? 'checked' : ''; ?>>
                              <label class="form-check-label" for="toggleSondeo_<?php echo (int)$item['id']; ?>">
                                <?php if (isset($item['habilitado']) && $item['habilitado'] === 'si'): ?>
                                  <span class="badge bg-success" id="badgeSondeo_<?php echo (int)$item['id']; ?>">Activo</span>
                                <?php else: ?>
                                  <span class="badge bg-danger" id="badgeSondeo_<?php echo (int)$item['id']; ?>">Inactivo</span>
                                <?php endif; ?>
                              </label>
                            </div>
                          </td>

                          <td>
                            <?php if ($edit): ?>
                              <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                onclick="SONDEO.editData(<?= (int)($item['id'] ?? 0) ?>)">
                                <i class="uil uil-edit"></i>
                              </button>
                            <?php endif; ?>
                          </td>

                          <th><?= h($item['sondeo'] ?? '') ?></th>

                          <td>
                            <?php if (($item['aplica_cargos_publicos'] ?? '') == 'si'): ?>
                              <button type="button" class="btn btn-sm btn-success"
                                title="Candidatos del sondeo"
                                onclick="showParticipantsModal(<?= htmlspecialchars(json_encode($item['candidatos'] ?? []), ENT_QUOTES, 'UTF-8') ?>, '<?= h($item['sondeo'] ?? '') ?>')">
                                <i class="uil-clipboard-alt"></i>
                              </button>
                            <?php endif; ?>
                          </td>

                          <th><?= h($item['tipo_inferenciales'] ?? '') ?></th>
                          <td><?= h($item['dtcreate'] ?? '') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL -->
        <div class="modal fade" id="participantsModal" tabindex="-1" data-bs-backdrop="static"
          aria-labelledby="modalPermisosLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">

              <div class="modal-header bg-primary justify-content-between">
                <h5 class="modal-title text-white" id="modalPermisosLabel">
                  Candidatos del sondeo: <span id="sondeo-title"></span>
                </h5>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"
                  onclick="UTIL.clearForm('formpermission');">
                  <span class="fas fa-times fs-9 text-white"></span>
                </button>
              </div>

              <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div class="table-shell">
                  <div class="table-responsive">
                    <table class="table table-sm table-striped w-full border-collapse mb-0" id="candidatosModalTable">
                      <thead>
                        <tr>
                          <th>Foto</th>
                          <th>Candidato</th>
                          <th>Cargo Público</th>
                          <th>Partido(s) Político(s)</th>
                          <th>Municipio</th>
                          <th>Departamento</th>
                        </tr>
                      </thead>
                      <tbody class="list">
                        <!-- Render JS -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button class="btn btn-outline-secondary py-2 px-4 rounded-lg" type="button" onclick="hideParticipantsModal();">
                  Cerrar
                </button>
              </div>

            </div>
          </div>
        </div>

        <?php include './admin/include/footer.php'; ?>
      </div>
    </div>
  </main>

  <!-- Required Js -->
  <?php include 'admin/include/gerenic_script.php'; ?>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <?php include './admin/include/generic_dataTables.php'; ?>

  <script type="text/javascript" src="admin/js/departamento.js"></script>
  <script type="text/javascript" src="admin/js/opcion_preguntas.js"></script>
  <script type="text/javascript" src="admin/js/sondeo.js"></script>

  <script>
    // ✅ Ajuste dinámico para que NO quede pegado al header (sin tocar tu layout)
    $(function(){
      setTimeout(function(){
        var headerH = 0;
        headerH = headerH || ($("header").first().outerHeight() || 0);
        headerH = headerH || ($(".pcoded-header").first().outerHeight() || 0);
        headerH = headerH || ($(".navbar").first().outerHeight() || 0);
        if (headerH > 0) $(".content").css("padding-top", (headerH + 18) + "px");
      }, 60);
    });

    // ✅ Tus llamadas existentes (NO se cambian)
    SONDEO.handleCargoPublicoChange();
    SONDEO.handleSondeParaCargoPublicoChange();
    OPCIONES.handleTipoPreguntaChange("tipo_sondeo");

    setTimeout(function() {
      DEPARTAMENTO.getMunicipios();
    }, 1000);
  </script>

  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
