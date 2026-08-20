<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Grilla.php';
include './admin/classes/FichaTecnicaEncuesta.php';

// Validar permisos
$view    = SessionData::getPermission(42);
$create  = SessionData::getPermission(43);
$edit    = SessionData::getPermission(44);
$permits = SessionData::getPermission(45);
if (!$view) { require 'permiso_denegado.php'; exit; }

// Información de Grilla
$resp = Grilla::getAll(null);
$isvalidGrilla = $resp['output']['valid'] ?? false;
$arr = $resp['output']['response'] ?? [];
$modulo = 'El sistema de Grilla: Pronostico, Tendencia y Probabilidad. Intenciones de Voto indirecta con condicionales de Conocimiento e imagen ';

// Información de Fichas Técnicas
$arrFichasTecnicas = FichaTecnicaEncuesta::getAll(null);
$fichas_tecnicas = $arrFichasTecnicas['output']['response'] ?? [];

// Cargos públicos
$arrCargosPub = CargosPublicos::getAll(null);
$arrCargosPub = $arrCargosPub['output']['response'] ?? [];
$optionCargosPub = "";
foreach ($arrCargosPub as $val) {
  $id = htmlspecialchars($val['id'] ?? '', ENT_QUOTES, 'UTF-8');
  $nm = htmlspecialchars($val['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
  $optionCargosPub .= "<option value='{$id}'>{$nm}</option>";
}

// Departamentos
$arrDep = Departamento::getAll(null);
$arrDep = $arrDep['output']['response'] ?? [];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
  $cod = $val['codigo_departamento'] ?? '';
  $dep = $val['departamento'] ?? '';
  $selected = ($cod == Util::getDepartamentoPrincipal() ? "selected" : "");
  $optionDep .= "<option {$selected} value='".htmlspecialchars($cod,ENT_QUOTES,'UTF-8')."'>"
              .htmlspecialchars($cod,ENT_QUOTES,'UTF-8')." - ".htmlspecialchars($dep,ENT_QUOTES,'UTF-8')
              ."</option>";
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<head>
  <style>
    :root{
      --nav-blue:#20427F;
      --nav-blue-2:#132b52;
      --nav-blue-3:#2e58a8;

      --card-radius: 18px;
      --soft-shadow: 0 18px 50px rgba(2,6,23,.10);
      --soft-shadow-2: 0 22px 70px rgba(2,6,23,.14);
      --soft-border: rgba(15,23,42,.08);

      --muted:#64748b;
      --ink:#0f172a;
    }

    /* ✅ Separación del header */
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
      display:flex; align-items:flex-start; gap:12px; flex-wrap:wrap;
    }
    .saas-icon{
      width:46px; height:46px; border-radius:16px;
      display:grid; place-items:center;
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2));
      color:#fff;
      box-shadow: 0 10px 26px rgba(32,66,127,.30);
      flex: 0 0 auto;
    }
    .saas-title h3{ margin:0; font-weight:900; letter-spacing:-.3px; color: var(--ink); }
    .saas-sub{ color:var(--muted); font-size:.92rem; margin-top:4px; max-width: 1100px; }

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
      font-weight:900;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: .82rem;
      display:inline-flex; align-items:center; gap:8px;
    }
    .section-title h5{ margin:0; font-weight:900; letter-spacing:-.2px; }

    /* ===== Inputs pro ===== */
    .form-floating > .form-control,
    .form-floating > .form-select{
      border-radius: 14px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
    }
    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus{
      box-shadow: 0 0 0 .2rem rgba(32,66,127,.12) !important;
      border-color: rgba(32,66,127,.35) !important;
    }

    /* ===== Buttons ===== */
    .btn-primary{
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2)) !important;
      border: 0 !important;
      box-shadow: 0 16px 40px rgba(32,66,127,.22);
    }
    .btn-primary:hover{
      transform: translateY(-1px);
      box-shadow: 0 22px 55px rgba(32,66,127,.28);
    }

    /* ===== Candidate Table block (sin tocar IDs ni clases tuyas) ===== */
    #table-container{
      border-radius: 16px !important;
      border: 1px solid rgba(15,23,42,.08) !important;
      background:
        radial-gradient(850px 260px at 10% 0%, rgba(32,66,127,.10), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.92)) !important;
      box-shadow: 0 18px 55px rgba(2,6,23,.10);
    }
    #table-container h5{
      font-weight: 900;
      letter-spacing: -.2px;
      margin-bottom: 14px !important;
    }
    #candidatosTable thead th{
      font-weight: 900;
      color: var(--ink);
      background: rgba(248,250,252,.8);
      border-bottom: 1px solid rgba(15,23,42,.08) !important;
      white-space: nowrap;
    }

    /* ===== Table shell ===== */
    .table-shell{
      border-radius: 16px;
      overflow:hidden;
      border: 1px solid rgba(15,23,42,.08);
      background: #fff;
      box-shadow: var(--soft-shadow);
    }
    .dt-nowrap th, .dt-nowrap td{ white-space: nowrap; }
    .table thead th{ font-weight: 900; }
    .table tbody td{ vertical-align: middle; }

    /* ===== Modal pro ===== */
    .modal-content{
      border-radius: 18px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
      overflow:hidden;
      box-shadow: 0 30px 90px rgba(2,6,23,.25);
    }
    #participantsModal .modal-header{
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2)) !important;
      border-bottom: 1px solid rgba(255,255,255,.16) !important;
    }
    #participantsModal .modal-title{
      font-weight: 900;
      letter-spacing: -.2px;
    }
    #candidatosModalTable thead th{
      font-weight: 900;
      white-space: nowrap;
    }

    /* ===== Sticky actions (mobile friendly) ===== */
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

    @media (max-width: 576px){
      .saas-pagehead{ padding: 14px; }
      .saas-icon{ width:40px; height:40px; border-radius: 13px; }
      #table-container{ padding: 14px !important; }
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
            <div class="saas-icon"><i class="fas fa-window-restore"></i></div>
            <div>
              <h3>Grillas</h3>
              <div class="saas-sub"><?= h($modulo) ?></div>
            </div>
          </div>

          <div class="chipbar">
            <div class="chip"><i class="fas fa-database"></i> Registros: <?= (int)count($arr) ?></div>
            <div class="chip"><i class="fas fa-clipboard-check"></i> Fichas técnicas: <?= (int)count($fichas_tecnicas) ?></div>
            <div class="chip"><i class="fas fa-shield-alt"></i> Permisos: <?= $view ? 'View' : '—' ?><?= $create ? ' · Create' : '' ?><?= $edit ? ' · Edit' : '' ?></div>
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
                <h5 class="mb-0">Crear / Editar Grilla</h5>
              </div>
              <div class="text-muted small d-none d-md-block">
                Consejo: si aplica cargo público, selecciona cargo y (si aplica) municipio/departamento.
              </div>
            </div>
          </div>

          <div class="card-body">
            <form class="row g-3 mb-0" id="formgrilla" role="form" autocomplete="false">
              <input type="hidden" name="op" id="op" />
              <input type="hidden" name="idGrilla" id="idGrilla" />

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="grilla" name="grilla"
                    placeholder="Texto del grilla a realizar" value="" required>
                  <label for="grilla">Grilla <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="descripcion_grilla"
                    name="descripcion_grilla" placeholder="Ingrese la descripción o pregunta del grilla" value="">
                  <label for="descripcion_grilla">Descripción del Grilla</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <select class="form-select" id="tbl_ficha_tecnica_encuesta_id" name="tbl_ficha_tecnica_encuesta_id">
                    <option value="">Seleccione una ficha técnica</option>
                    <?php foreach ($fichas_tecnicas as $ficha): ?>
                      <option value="<?= h($ficha['id'] ?? '') ?>">
                        <?= h($ficha['tipo_estudio'] ?? ('Ficha #'.($ficha['id'] ?? ''))) ?> -
                        <?= h(substr((string)($ficha['realizada_por_o_encomendada_por'] ?? ''), 0, 100)) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label for="tbl_ficha_tecnica_encuesta_id">Ficha Técnica de Encuesta <span class="text-danger">*</span></label>
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
                    onchange="GRILLA.handleSondeParaCargoPublicoChange();" required>
                    <option value="no">No</option>
                    <option value="si">Sí</option>
                  </select>
                  <label for="aplica_cargos_publicos">Grilla para cargo público <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3 cargo-publico-fields d-none">
                <div class="form-floating">
                  <select class="form-select" id="tbl_cargo_publico_id" name="tbl_cargo_publico_id"
                    onchange="GRILLA.handleCargoPublicoChange(this);" required>
                    <?php echo $optionCargosPub; ?>
                  </select>
                  <label for="tbl_cargo_publico_id">Cargo Público <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-3">
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" id="habilitado" name="habilitado" value="si" checked>
                  <label class="form-check-label fw-bold" for="habilitado">Grilla Habilitada</label>
                </div>
                <small class="text-muted">Desmarque si desea deshabilitar esta grilla</small>
              </div>

              <div class="col-sm-6 col-md-5 departamento-municipio-fields d-none">
                <div class="form-floating">
                  <select onchange="DEPARTAMENTO.getMunicipios(), GRILLA.filterAndShowData();" class="form-select"
                    id="tbl_departamento_id" name="tbl_departamento_id">
                    <?= $optionDep ?>
                  </select>
                  <label for="tbl_departamento_id">Departamento</label>
                </div>
              </div>

              <div class="col-sm-6 col-md-5 departamento-municipio-fields d-none" id="alcaldia-container">
                <div class="form-floating">
                  <select onchange="GRILLA.filterAndShowData();" class="form-select" id="tbl_municipio_id"
                    name="tbl_municipio_id"></select>
                  <label for="tbl_municipio_id">Municipio</label>
                </div>
              </div>

              <!-- Tabla candidatos (NO se cambian ids) -->
              <div id="table-container" class="table-candidatos mt-4 p-4 border border-gray-300 rounded-lg bg-gray-50">
                <h5 class="text-xl font-bold mb-4 text-center text-gray-800">Candidatos a postular.</h5>
                <div class="table-shell">
                  <div class="table-responsive">
                    <table class="table table-striped table-sm fs-9 mb-0" id="candidatosTable">
                      <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase leading-normal">
                          <th class="py-3 px-4 font-semibold text-left">
                            <div class="flex items-center"><span>Seleccionar</span></div>
                          </th>
                          <th class="py-3 px-4 font-semibold text-left">Foto</th>
                          <th class="py-3 px-4 font-semibold text-left">Nombre Completo</th>
                          <th class="py-3 px-4 font-semibold text-left">Cargo Público</th>
                          <th class="py-3 px-4 font-semibold text-left">Partido(s) Político(s)</th>
                          <th class="py-3 px-4 font-semibold text-left">Municipio</th>
                          <th class="py-3 px-4 font-semibold text-left">Departamento</th>
                        </tr>
                      </thead>
                      <tbody class="list">
                        <!-- Los datos se renderizarán aquí con JavaScript -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- Acciones -->
              <div class="col-12">
                <div class="sticky-actions">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">
                      <i class="fas fa-info-circle me-1"></i>
                      Tip: si seleccionas “Sí” en cargo público se habilitan campos y candidatos.
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                      <button type="button" onclick="GRILLA.emptyCells();" class="btn btn-phoenix-secondary px-4">
                        Cancelar
                      </button>
                      <?php if ($create && $edit): ?>
                        <button class="btn btn-primary px-4" type="button" onclick="GRILLA.validateData();">
                          Guardar
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>

      </div><!-- /col -->
    </div><!-- /content wrapper -->

    <!-- ===== TABLA LISTADO ===== -->
    <div class="content">
      <div class="col-11 col-xl-11 mx-auto">
        <div class="saas-card card mb-4">
          <div class="card-header">
            <div class="section-title mb-0">
              <div class="left">
                <span class="badge-soft"><i class="fas fa-table"></i>Listado</span>
                <h5 class="mb-0">Grillas Registradas</h5>
              </div>
              <div class="text-muted small d-none d-md-block">Usa el buscador de DataTable para filtrar rápido.</div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-shell">
              <div class="table-responsive">
                <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 dt-nowrap">
                  <thead>
                    <tr class="border-1">
                      <th>Editar</th>
                      <th>Ver Estudio</th>
                      <th>Ver Resultados</th>
                      <th>Grilla</th>
                      <th>Grilla para cargo público</th>
                      <th>Candidatos Políticos</th>
                      <th>Tipo de Inferenciales</th>
                      <th>Fecha</th>
                      <th>Habilitado</th>
                    </tr>
                  </thead>
                  <tbody class="list">
                    <?php if ($isvalidGrilla && count($arr) > 0): ?>
                      <?php foreach ($arr as $item):
                        $itemJson = htmlspecialchars(json_encode($item), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                      ?>
                        <tr>
                          <td>
                            <?php if ($edit): ?>
                              <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                onclick="GRILLA.editData(<?= htmlspecialchars($item['id']) ?>)">
                                <i class="uil uil-edit"></i>
                              </button>
                            <?php endif; ?>
                          </td>
                          <td>
                            <button type="button" class="btn btn-sm btn-danger" title="Ver Estudio"
                              onclick="GRILLA.showGrilla('<?= $itemJson ?>')">
                              <i class="uil uil-eye"></i>
                            </button>
                          </td>
                          <td>
                            <button type="button" class="btn btn-sm btn-success" title="Ver Resultados en Tiempo Real"
                              onclick="GRILLA.showResultados('<?= $itemJson ?>')">
                              <i class="fas fa-chart-bar"></i>
                            </button>
                          </td>
                          <th><?= h($item['grilla'] ?? '') ?></th>
                          <td><?= h($item['aplica_cargos_publicos'] ?? '') ?></td>
                          <td class="py-3 px-6 text-left">
                            <?php if (($item['aplica_cargos_publicos'] ?? '') == 'si'): ?>
                              <button type="button" class="btn btn-sm btn-success bg-green-500 text-white p-2 rounded-lg"
                                title="Candidatos de la grilla"
                                onclick="showParticipantsModal(<?= htmlspecialchars(json_encode($item['candidatos'] ?? []), ENT_QUOTES, 'UTF-8') ?>, '<?= h($item['grilla'] ?? '') ?>')">
                                <i class="uil-clipboard-alt"></i>
                              </button>
                            <?php endif; ?>
                          </td>
                          <th><?= h($item['tipo_inferenciales'] ?? '') ?></th>
                          <td><?= h($item['dtcreate'] ?? '') ?></td>
                          <td>
                            <?php if (($item['habilitado'] ?? '') === 'si'): ?>
                              <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Activo</span>
                            <?php else: ?>
                              <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Inactivo</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No hay registros.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal candidatos -->
        <div class="modal fade" id="participantsModal" tabindex="-1" data-bs-backdrop="static"
          aria-labelledby="modalPermisosLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">

              <div class="modal-header justify-content-between">
                <h5 class="modal-title text-white" id="modalPermisosLabel">
                  Candidatos de la grilla: <span id="sondeo-title"></span>
                </h5>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"
                  onclick="UTIL.clearForm('formpermission');">
                  <span class="fas fa-times fs-9 text-white"></span>
                </button>
              </div>

              <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div class="table-responsive">
                  <table class="table table-sm table-striped w-full border-collapse" id="candidatosModalTable">
                    <thead class="bg-gray-100">
                      <tr>
                        <th class="py-3 px-4 font-semibold text-left border-b border-gray-300">Foto</th>
                        <th class="py-3 px-4 font-semibold text-left border-b border-gray-300">Candidato</th>
                        <th class="py-3 px-4 font-semibold text-left border-b border-gray-300">Cargo Público</th>
                        <th class="py-3 px-4 font-semibold text-left border-b border-gray-300">Partido(s) Político(s)</th>
                        <th class="py-3 px-4 font-semibold text-left border-b border-gray-300">Municipio</th>
                        <th class="py-3 px-4 font-semibold text-left border-b border-gray-300">Departamento</th>
                      </tr>
                    </thead>
                    <tbody class="list">
                      <!-- Los datos se renderizarán aquí con JavaScript -->
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="modal-footer">
                <button class="btn btn-outline-secondary mr-2 py-2 px-4 rounded-lg" type="button"
                  onclick="hideParticipantsModal();">Cerrar</button>
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
  <script type="text/javascript" src="admin/js/grilla.js"></script>

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

    // ✅ tu flujo (NO se toca)
    GRILLA.handleCargoPublicoChange();
    GRILLA.handleSondeParaCargoPublicoChange();
    setTimeout(function() {
      DEPARTAMENTO.getMunicipios();
    }, 1000);
  </script>

  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
