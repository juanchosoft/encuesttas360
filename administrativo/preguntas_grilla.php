<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/PreguntaGrilla.php';
include './admin/classes/Grilla.php';

// Validar permisos
$view    = SessionData::getPermission(38);
$create  = SessionData::getPermission(39);
$edit    = SessionData::getPermission(40);
$permits = SessionData::getPermission(41);
if (!$view) {
  require 'permiso_denegado.php';
  exit;
}

$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if(!$isAdmin){
  require '../permiso_denegado.php';
  exit;
}

$modulo = 'Administración de Preguntas y Subpreguntas de Grilla';

// Obtener todas las grillas para selector
$arrGrillas = Grilla::getAll(null);
$arrGrillas = $arrGrillas['output']['response'] ?? [];
$optionGrillas = "";
foreach ($arrGrillas as $val) {
  $id = htmlspecialchars($val['id'] ?? '', ENT_QUOTES, 'UTF-8');
  $gr = htmlspecialchars($val['grilla'] ?? '', ENT_QUOTES, 'UTF-8');
  $optionGrillas .= "<option value='{$id}'>{$gr}</option>";
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="es-CO" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
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

    /* ✅ separa contenido del header (luego JS lo ajusta al alto real) */
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

    /* ===== Tabs pro ===== */
    .nav-tabs{
      border-bottom: 1px solid rgba(15,23,42,.08) !important;
    }
    .nav-tabs .nav-link{
      border: 0 !important;
      border-radius: 14px 14px 0 0 !important;
      padding: 10px 14px !important;
      color: var(--muted) !important;
      font-weight: 800 !important;
      background: transparent !important;
    }
    .nav-tabs .nav-link:hover{
      color: var(--ink) !important;
      background: rgba(32,66,127,.06) !important;
    }
    .nav-tabs .nav-link.active{
      color: var(--ink) !important;
      background:
        radial-gradient(550px 160px at 10% 0%, rgba(32,66,127,.14), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.92)) !important;
      border: 1px solid rgba(15,23,42,.08) !important;
      border-bottom-color: transparent !important;
      box-shadow: 0 14px 40px rgba(2,6,23,.08);
    }
    .tab-pane{
      border: 1px solid rgba(15,23,42,.08);
      border-radius: 0 16px 16px 16px;
      padding: 14px;
      background: rgba(255,255,255,.94);
    }

    /* ===== Table shell ===== */
    .table-shell{
      border-radius: 16px;
      overflow:hidden;
      border: 1px solid rgba(15,23,42,.08);
      background: #fff;
    }
    .table thead th{
      font-weight: 900;
      color: var(--ink);
      background: rgba(248,250,252,.8);
      border-bottom: 1px solid rgba(15,23,42,.08) !important;
      white-space: nowrap;
    }
    .table tbody td{
      vertical-align: middle;
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

    /* ===== Modal pro ===== */
    .modal-content{
      border-radius: 18px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
      overflow:hidden;
      box-shadow: 0 30px 90px rgba(2,6,23,.25);
    }

    /* ✅ tu clase existente, la dejamos pero la definimos pro */
    .modal-header-gradient-purple{
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2)) !important;
      border-bottom: 1px solid rgba(255,255,255,.16) !important;
    }
    .modal-icon-circle{
      width:46px; height:46px; border-radius: 16px;
      display:grid; place-items:center;
      background: rgba(255,255,255,.16);
      border: 1px solid rgba(255,255,255,.25);
      margin-right: 12px;
      box-shadow: 0 14px 35px rgba(2,6,23,.20);
    }
    .modal-footer-light{
      background: rgba(248,250,252,.92);
      border-top: 1px solid rgba(15,23,42,.08);
    }

    /* ===== Inputs modal ===== */
    .modal-body .form-control,
    .modal-body .form-select{
      border-radius: 14px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
    }
    .modal-body .form-control:focus,
    .modal-body .form-select:focus{
      box-shadow: 0 0 0 .2rem rgba(32,66,127,.12) !important;
      border-color: rgba(32,66,127,.35) !important;
    }
    .font-monospace{ font-size: .9rem; }

    /* ===== Choices.js ===== */
    .choices__inner{
      border-radius: 14px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
      background: #fff !important;
      min-height: 46px;
      padding-top: 6px;
    }
    .choices__list--multiple .choices__item{
      border-radius: 999px !important;
      font-weight: 800;
      background: rgba(32,66,127,.10) !important;
      border: 1px solid rgba(32,66,127,.18) !important;
      color: var(--nav-blue) !important;
    }

    @media (max-width: 576px){
      .saas-pagehead{ padding: 14px; }
      .saas-icon{ width:40px; height:40px; border-radius: 13px; }
      .tab-pane{ padding: 12px; }
    }
  </style>
</head>

<body>
  <main class="main" id="top">
    <?php
      include './admin/include/navbar.php';
      include './admin/include/header.php';
    ?>

    <!-- Incluimos Choices.js para los selects mejorados (se deja igual) -->
    <link rel="stylesheet" href="admin/js/lib//choices.min.css" />
    <script src="admin/js/lib/choices.min.js"></script>

    <div class="content">
      <div class="col-11 col-xl-11 mx-auto">

        <!-- ===== PageHead SaaS ===== -->
        <div class="saas-pagehead">
          <div class="saas-title">
            <div class="saas-icon"><i class="fas fa-question-circle"></i></div>
            <div>
              <h3><?= h($modulo) ?></h3>
              <div class="saas-sub">Gestiona preguntas principales, subpreguntas y valida la vista previa en tiempo real.</div>
            </div>
          </div>

          <div class="chipbar">
            <div class="chip"><i class="fas fa-shield-alt"></i> Admin: <?= $isAdmin ? 'Sí' : 'No' ?></div>
            <div class="chip"><i class="fas fa-layer-group"></i> Grillas: <?= (int)count($arrGrillas) ?></div>
            <div class="chip"><i class="fas fa-bolt"></i> Módulo dinámico</div>
          </div>
        </div>

        <!-- ===== Card principal ===== -->
        <div class="saas-card card my-4">
          <div class="card-header">
            <div class="section-title">
              <div class="left">
                <span class="badge-soft"><i class="fas fa-sliders-h"></i>Gestión</span>
                <h5 class="mb-0">Preguntas y Subpreguntas</h5>
              </div>

              <div class="d-flex align-items-center gap-2">
                <div class="text-muted small d-none d-md-block">
                  Crea preguntas, define condición, opciones y grillas asociadas.
                </div>
                <?php if ($create) { ?>
                  <button class="btn btn-primary" id="btnNuevaPregunta">
                    <i class="fas fa-plus me-2"></i>Nueva Pregunta
                  </button>
                <?php } ?>
              </div>
            </div>
          </div>

          <div class="card-body">

            <!-- Pestañas -->
            <ul class="nav nav-tabs mb-3" id="pestanasPreguntas" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-principales" data-bs-toggle="tab"
                        data-bs-target="#preguntas-principales" type="button" role="tab">
                  <i class="fas fa-list-ol me-2"></i>Preguntas Principales
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-subpreguntas" data-bs-toggle="tab"
                        data-bs-target="#subpreguntas" type="button" role="tab">
                  <i class="fas fa-indent me-2"></i>Subpreguntas
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-preview" data-bs-toggle="tab"
                        data-bs-target="#preview" type="button" role="tab">
                  <i class="fas fa-eye me-2"></i>Vista Previa
                </button>
              </li>
            </ul>

            <!-- Contenido tabs -->
            <div class="tab-content" id="contenidoPestanas">

              <!-- TAB 1 -->
              <div class="tab-pane fade show active" id="preguntas-principales" role="tabpanel">
                <div class="table-shell">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaPreguntasPrincipales">
                      <thead>
                        <tr>
                          <th style="width: 50px;">Orden</th>
                          <th>Código</th>
                          <th>Texto de la Pregunta</th>
                          <th>Opciones</th>
                          <th>Condición</th>
                          <th>Estado</th>
                          <th style="width: 150px;">Acciones</th>
                        </tr>
                      </thead>
                      <tbody id="tbodyPreguntasPrincipales">
                        <tr>
                          <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Cargando...</span>
                            </div>
                            <div class="text-muted small mt-2">Cargando preguntas principales…</div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- TAB 2 -->
              <div class="tab-pane fade" id="subpreguntas" role="tabpanel">
                <div class="table-shell">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaSubpreguntas">
                      <thead>
                        <tr>
                          <th style="width: 50px;">Orden</th>
                          <th>Código</th>
                          <th>Texto de la Subpregunta</th>
                          <th>Pregunta Padre</th>
                          <th>Estado</th>
                          <th style="width: 150px;">Acciones</th>
                        </tr>
                      </thead>
                      <tbody id="tbodySubpreguntas">
                        <tr>
                          <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Cargando...</span>
                            </div>
                            <div class="text-muted small mt-2">Cargando subpreguntas…</div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- TAB 3 -->
              <div class="tab-pane fade" id="preview" role="tabpanel">
                <div class="alert alert-info mb-3">
                  <i class="fas fa-info-circle me-2"></i>
                  <strong>Vista previa de cómo se verán las preguntas en el estudio de votaciones</strong>
                </div>

                <div id="previewContenido">
                  <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Cargando vista previa...</span>
                    </div>
                    <div class="text-muted small mt-2">Generando vista previa…</div>
                  </div>
                </div>
              </div>

            </div><!-- /tab-content -->
          </div><!-- /card-body -->
        </div><!-- /saas-card -->

      </div>
    </div>

    <?php include './include/footer.php'; ?>
  </main>

  <!-- MODAL: Crear/Editar Pregunta -->
  <div class="modal fade" id="modalPregunta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header modal-header-gradient-purple">
          <div class="d-flex align-items-center">
            <div class="modal-icon-circle">
              <i class="fas fa-question-circle text-white fs-4"></i>
            </div>
            <div>
              <h5 class="modal-title text-white mb-0" id="tituloModalPregunta">
                <i class="fas fa-plus-circle me-2"></i>Nueva Pregunta
              </h5>
              <p class="text-white-50 small mb-0 mt-1">Administración de preguntas para grillas</p>
            </div>
          </div>
          <button type="button" class="btn btn-link p-2" data-bs-dismiss="modal" aria-label="Cerrar">
            <i class="fas fa-times fs-5 text-white"></i>
          </button>
        </div>

        <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto; background-color: #f8f9fa;">
          <form id="formPregunta" autocomplete="off">
            <input type="hidden" name="id" id="preguntaId" value="0">
            <input type="hidden" name="op" id="preguntaOp" value="preguntasgrillasave">

            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <div class="row g-3">

                  <!-- Tipo de Pregunta -->
                  <div class="col-md-6">
                    <label for="tipoPregunta" class="form-label">
                      Tipo <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="tipoPregunta" name="tipo_pregunta" required>
                      <option value="pregunta">Pregunta Principal</option>
                      <option value="subpregunta">Subpregunta</option>
                    </select>
                    <small class="text-muted">Las preguntas principales se hacen por cada candidato</small>
                  </div>

                  <!-- Código de Pregunta -->
                  <div class="col-md-6">
                    <label for="codigoPregunta" class="form-label">
                      Código Único <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="codigoPregunta" name="codigo_pregunta"
                           placeholder="Ej: conoce, imagen, votaria" required>
                    <small class="text-muted">Identificador único usado en el código</small>
                  </div>

                  <!-- Texto de la Pregunta -->
                  <div class="col-12">
                    <label for="textoPregunta" class="form-label">
                      Texto de la Pregunta <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="textoPregunta" name="texto_pregunta" rows="2"
                              placeholder="Ej: ¿CONOCE O NO LO CONOCE?" required></textarea>
                  </div>

                  <!-- Orden -->
                  <div class="col-md-4">
                    <label for="ordenPregunta" class="form-label">
                      Orden <span class="text-danger">*</span>
                    </label>
                    <input type="number" class="form-control" id="ordenPregunta" name="orden"
                           min="1" value="1" required>
                  </div>

                  <!-- Pregunta Padre (solo para subpreguntas) -->
                  <div class="col-md-8" id="contenedorPreguntaPadre" style="display: none;">
                    <label for="preguntaPadreId" class="form-label">
                      Pregunta Principal Asociada
                    </label>
                    <select class="form-select" id="preguntaPadreId" name="pregunta_padre_id">
                      <option value="">Ninguna</option>
                    </select>
                  </div>

                  <!-- Opciones de Respuesta (solo para preguntas principales) -->
                  <div class="col-12" id="contenedorOpciones">
                    <label for="opcionesRespuesta" class="form-label">
                      Opciones de Respuesta (JSON)
                    </label>
                    <input type="text" class="form-control font-monospace" id="opcionesRespuesta"
                           name="opciones_respuesta" placeholder='["si", "no"]'>
                    <small class="text-muted">
                      Formato JSON. Ejemplos: ["si", "no"], ["favorable", "desfavorable"]
                    </small>
                  </div>

                  <!-- Habilita Subpreguntas -->
                  <div class="col-md-6" id="contenedorHabilitaSubpreguntas">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="habilitaSubpreguntas"
                             name="habilita_subpreguntas" value="1">
                      <label class="form-check-label" for="habilitaSubpreguntas">
                        Habilita siguientes preguntas
                      </label>
                    </div>
                    <small class="text-muted">Al responder esta pregunta se habilitan las siguientes</small>
                  </div>

                  <!-- Condición de Habilitación -->
                  <div class="col-md-6" id="contenedorCondicion">
                    <label for="condicionHabilitacion" class="form-label">Condición</label>
                    <select class="form-select" id="condicionHabilitacion" name="condicion_habilitacion">
                      <option value="">Ninguna</option>
                      <option value="si">Si responde SÍ</option>
                      <option value="favorable">Si responde FAVORABLE</option>
                      <option value="todas_si">Si todas las anteriores son SÍ</option>
                    </select>
                  </div>

                  <!-- Requiere Todas SI -->
                  <div class="col-md-6" id="contenedorRequiereTodasSi">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="requiereTodasSi"
                             name="requiere_todas_si" value="1">
                      <label class="form-check-label" for="requiereTodasSi">
                        Requiere todas anteriores en SÍ
                      </label>
                    </div>
                  </div>

                  <!-- Activa Sección de Subpreguntas -->
                  <div class="col-md-6" id="contenedorActivaSubpreguntas">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="activaSeccionSubpreguntas"
                             name="activa_seccion_subpreguntas" value="1">
                      <label class="form-check-label" for="activaSeccionSubpreguntas">
                        Activa sección de subpreguntas (PA, PB, PC...)
                      </label>
                    </div>
                    <small class="text-muted">Si responde SÍ a esta pregunta, se muestran las subpreguntas</small>
                  </div>

                  <!-- Estado -->
                  <div class="col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="habilitado"
                             name="habilitado" value="1" checked>
                      <label class="form-check-label" for="habilitado">
                        Activa (visible en la interfaz)
                      </label>
                    </div>
                  </div>

                  <!-- Grillas Asociadas -->
                  <div class="col-12" id="contenedorGrillasAsociadas">
                    <label for="grillasAsociadas" class="form-label">
                      <i class="fas fa-table me-2"></i>Grillas Asociadas
                    </label>
                    <select class="form-select" id="grillasAsociadas" name="grillas_asociadas[]" multiple="multiple">
                      <?= $optionGrillas ?>
                    </select>
                    <small class="text-muted">
                      <i class="fas fa-info-circle me-1"></i>
                      Selecciona las grillas donde se usará esta pregunta. Si no seleccionas ninguna, será global.
                    </small>
                  </div>

                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer modal-footer-light">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancelar
          </button>
          <button type="button" class="btn btn-primary px-4" id="btnGuardarPregunta">
            <i class="fas fa-save me-2"></i>Guardar Pregunta
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Required Js -->
  <?php include 'admin/include/gerenic_script.php'; ?>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
  <script type="text/javascript" src="admin/js/preguntas_grilla.js"></script>

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

    // ✅ tu init (NO se toca)
    PREGUNTAS_GRILLA.init();
  </script>

  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>

</body>
</html>
