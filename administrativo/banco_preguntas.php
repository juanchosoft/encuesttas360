<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/include/generic_info_configuracion.php';

$view = SessionData::hasPermission('encuestas.banco_preguntas.view');
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}
$create = SessionData::hasPermission('encuestas.banco_preguntas.create');
$edit = SessionData::hasPermission('encuestas.banco_preguntas.update');
$delete = SessionData::hasPermission('encuestas.banco_preguntas.delete');
$modulo = 'Banco de preguntas';
?>
<body class="qst-page">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <style>
    body.qst-page{ background:linear-gradient(180deg,#F8FAFD 0%,#F2F5FA 100%); color:#101828; }
    .bnc-shell{ max-width:1280px; margin:0 auto; padding:8px 4px 40px; }
    .bnc-hero{ background:linear-gradient(135deg,#0A2248,#20427F); color:#fff; border-radius:24px; padding:26px 28px; margin-bottom:18px; display:flex; flex-wrap:wrap; gap:18px; align-items:center; justify-content:space-between; }
    .bnc-hero h1{ font-size:1.7rem; font-weight:800; margin:0 0 6px; color:#fff; }
    .bnc-hero p{ margin:0; color:#D0DDF5; max-width:640px; }
    .bnc-kpis{ display:flex; gap:12px; flex-wrap:wrap; }
    .bnc-kpi{ background:rgba(255,255,255,.12); border-radius:16px; padding:10px 18px; text-align:center; min-width:96px; }
    .bnc-kpi strong{ display:block; font-size:1.5rem; line-height:1.1; }
    .bnc-kpi span{ font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; color:#D0DDF5; }
    .bnc-panel{ background:#fff; border:1px solid #E5EAF1; border-radius:22px; box-shadow:0 12px 34px rgba(15,23,42,.065); }
    .bnc-filtros{ display:flex; flex-wrap:wrap; gap:10px; padding:16px 18px; border-bottom:1px solid #EEF2F7; align-items:center; }
    .bnc-filtros .form-control, .bnc-filtros .form-select{ max-width:240px; }
    .bnc-filtros .bnc-buscar{ flex:1 1 220px; max-width:360px; }
    .bnc-tabla{ width:100%; margin:0; }
    .bnc-tabla thead th{ font-size:.7rem; text-transform:uppercase; letter-spacing:.4px; color:#475467; background:#F8FAFD; border-bottom:1px solid #E8EDF5; padding:10px 14px; }
    .bnc-tabla tbody td{ vertical-align:middle; padding:12px 14px; border-color:#EEF2F7; font-size:.86rem; }
    .bnc-texto{ max-width:520px; font-weight:600; }
    .bnc-uso{ display:inline-block; padding:3px 11px; border-radius:999px; font-size:.7rem; font-weight:800; text-transform:uppercase; }
    .bnc-uso-encuesta{ background:#EBF3FF; color:#20427F; border:1px solid #B2CCFF; }
    .bnc-uso-sondeo{ background:#FFF6E5; color:#93370D; border:1px solid #FEDF89; }
    .bnc-vacio{ padding:44px 18px; text-align:center; color:#667085; }
    .bnc-opciones-editor .bnc-opcion{ display:grid; grid-template-columns:1fr 110px 150px 150px 40px; gap:8px; margin-bottom:8px; align-items:start; }
    .bnc-opciones-editor .bnc-opcion .bnc-msg{ grid-column:1 / -1; }
    .bnc-detalle dt{ font-size:.72rem; text-transform:uppercase; letter-spacing:.4px; color:#667085; margin-top:12px; }
    .bnc-detalle dd{ margin:2px 0 0; font-size:.95rem; }
    @media (max-width:768px){ .bnc-opciones-editor .bnc-opcion{ grid-template-columns:1fr 1fr; } .bnc-opciones-editor .bnc-opcion input.bnc-o-texto{ grid-column:1 / -1; } .bnc-hero{ padding:20px; } }
  </style>

  <div class="content">
    <div class="bnc-shell">
      <section class="bnc-hero">
        <div>
          <h1><i class="fas fa-database me-2"></i>Banco de preguntas</h1>
          <p>Preguntas completas listas para reutilizar en encuestas y sondeos sin volver a escribirlas. Al usarlas se crea una copia: cambiar el banco no modifica lo que ya se publicó.</p>
          <span id="spanModulo" class="d-none"><?= htmlspecialchars($modulo, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="bnc-kpis">
          <div class="bnc-kpi"><strong id="kpiTotal">0</strong><span>Preguntas</span></div>
          <div class="bnc-kpi"><strong id="kpiEncuesta">0</strong><span>Encuesta</span></div>
          <div class="bnc-kpi"><strong id="kpiSondeo">0</strong><span>Sondeo</span></div>
        </div>
      </section>

      <section class="bnc-panel">
        <div class="bnc-filtros">
          <input type="search" id="fBuscar" class="form-control bnc-buscar" placeholder="Buscar por texto, categoría, capítulo o numeral">
          <select id="fUso" class="form-select" aria-label="Uso">
            <option value="">Uso: todos</option>
            <option value="encuesta">Uso: encuesta</option>
            <option value="sondeo">Uso: sondeo</option>
          </select>
          <select id="fCategoria" class="form-select" aria-label="Categoría"><option value="">Categoría: todas</option></select>
          <?php if ($create): ?>
          <button type="button" class="btn btn-primary ms-auto fw-bold" id="btnNuevaBanco"><i class="fas fa-plus me-1"></i>Nueva pregunta</button>
          <?php endif; ?>
        </div>
        <div class="table-responsive">
          <table class="table bnc-tabla" id="tblBanco">
            <thead>
              <tr>
                <th style="width:60px">ID</th>
                <th>Pregunta</th>
                <th>Uso</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th class="text-center">Opciones</th>
                <th class="text-end" style="width:150px">Acciones</th>
              </tr>
            </thead>
            <tbody id="bncCuerpo"></tbody>
          </table>
        </div>
        <div id="bncVacio" class="bnc-vacio d-none">
          <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
          <strong>No hay preguntas en el banco con ese filtro.</strong>
          <p class="mb-0 mt-2 small">Cree una con «Nueva pregunta» o use «Agregar a banco de preguntas» desde una encuesta o un sondeo.</p>
        </div>
      </section>
    </div>

    <div class="modal fade" id="bncModalDetalle" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Detalle de la pregunta</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
          <div class="modal-body" id="bncDetalleCuerpo"></div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="bncModalForm" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title" id="bncFormTitulo">Pregunta del banco</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
          <div class="modal-body">
            <input type="hidden" id="bId">
            <div class="row g-3">
              <div class="col-md-3">
                <div class="form-floating">
                  <select class="form-select" id="bUso">
                    <option value="encuesta">Encuesta</option>
                    <option value="sondeo">Sondeo</option>
                  </select>
                  <label for="bUso">Uso <span class="text-danger">*</span></label>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-floating">
                  <input type="text" class="form-control" id="bCategoria" list="bCategoriasLista" autocomplete="off" placeholder="Categoría">
                  <label for="bCategoria">Categoría (opcional)</label>
                  <datalist id="bCategoriasLista"></datalist>
                </div>
              </div>
              <div class="col-md-4 bnc-solo-encuesta">
                <div class="form-floating">
                  <select class="form-select" id="bTipo">
                    <option value="">Seleccione cómo responde la persona</option>
                    <option value="Dicotomica">Sí / No (Dos opciones)</option>
                    <option value="Seleccion_Multiple_unica_respuesta">Escoge una sola opción</option>
                    <option value="Seleccion_Multiple_multiple_respuesta">Puede escoger varias opciones</option>
                    <option value="Preguntas_Ordinales">Escala de calificación (ej. 1 a 5)</option>
                    <option value="Preguntas_Cardinales">Nivel de satisfacción / intensidad</option>
                    <option value="Grilla">Tabla de candidatos o ítems</option>
                    <option value="Lista_desde_grilla">Escoger de los candidatos de una tabla (Voto escalonado)</option>
                    <option value="Texto Libre">Respuesta escrita abierta</option>
                  </select>
                  <label for="bTipo">Tipo de pregunta <span class="text-danger">*</span></label>
                </div>
              </div>
              <div class="col-md-4 bnc-solo-sondeo d-none">
                <div class="form-floating">
                  <select class="form-select" id="bTipoSondeo">
                    <option value="No Aplica">No Aplica</option>
                    <option value="Dicotomica">Dicotómica</option>
                    <option value="Grilla">Tipo Grilla</option>
                    <option value="Preguntas_Ordinales">Preguntas Ordinales</option>
                    <option value="Preguntas_Cardinales">Preguntas Cardinales</option>
                    <option value="Seleccion_Multiple_unica_respuesta">Selección múltiple con única respuesta</option>
                  </select>
                  <label for="bTipoSondeo">Tipo de sondeo <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="bTexto" style="height:84px" placeholder="Texto"></textarea>
                  <label for="bTexto">Texto de la pregunta <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-md-6 bnc-solo-sondeo d-none">
                <div class="form-floating">
                  <input type="text" class="form-control" id="bDescripcion" maxlength="255" placeholder="Descripción">
                  <label for="bDescripcion">Descripción del sondeo (opcional)</label>
                </div>
              </div>
              <div class="col-md-3 bnc-solo-sondeo d-none">
                <div class="form-floating">
                  <select class="form-select" id="bInferenciales">
                    <option value="Pronostico">Pronóstico</option>
                    <option value="Tendencia">Tendencia</option>
                    <option value="Probabilidad">Probabilidad</option>
                    <option value="Otro">Otro</option>
                  </select>
                  <label for="bInferenciales">Tipo de inferenciales</label>
                </div>
              </div>
              <div class="col-md-3 bnc-solo-sondeo d-none">
                <div class="form-floating">
                  <select class="form-select" id="bTrivia"><option value="no">No</option><option value="si">Sí</option></select>
                  <label for="bTrivia">¿Es trivia?</label>
                </div>
              </div>

              <div class="col-md-6 bnc-solo-encuesta">
                <div class="form-floating"><input type="text" class="form-control" id="bCapitulo" placeholder="Capítulo"><label for="bCapitulo">Capítulo o sección sugerido (opcional)</label></div>
              </div>
              <div class="col-md-3 bnc-solo-encuesta">
                <div class="form-floating"><input type="text" class="form-control" id="bNumeral" placeholder="Numeral"><label for="bNumeral">Numeral sugerido (ej. 5A)</label></div>
              </div>
              <div class="col-md-3 bnc-solo-encuesta" id="bLimiteWrap" style="display:none">
                <div class="form-floating"><input type="number" class="form-control" id="bLimite" min="1" value="2"><label for="bLimite">Máximo de opciones a marcar</label></div>
              </div>
              <div class="col-12 bnc-solo-encuesta">
                <div class="form-floating"><input type="text" class="form-control" id="bEnunciado" placeholder="Enunciado"><label for="bEnunciado">Enunciado de la sección (se muestra grande, antes de las subpreguntas)</label></div>
              </div>
              <div class="col-md-6 bnc-solo-encuesta">
                <div class="form-floating"><input type="text" class="form-control" id="bLibreto" placeholder="Instrucción"><label for="bLibreto">Instrucción para el encuestador (opcional)</label></div>
              </div>
              <div class="col-md-6 bnc-solo-encuesta">
                <div class="form-floating"><input type="text" class="form-control" id="bNota" placeholder="Nota"><label for="bNota">Nota de análisis / tabulación (opcional)</label></div>
              </div>
              <div class="col-md-6 bnc-solo-encuesta d-flex flex-wrap gap-4 align-items-center">
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="bObligatoria" checked><label class="form-check-label" for="bObligatoria">Obligatoria</label></div>
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="bFiltro"><label class="form-check-label" for="bFiltro">Es pregunta filtro</label></div>
              </div>
              <div class="col-md-6 bnc-solo-encuesta" id="bPlantillaWrap" style="display:none">
                <div class="form-floating"><select class="form-select" id="bPlantilla"><option value="">Seleccione la tabla de candidatos</option></select><label for="bPlantilla">Tabla de candidatos (Grilla)</label></div>
              </div>

              <div class="col-12" id="bOpcionesWrap">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <strong>Opciones de respuesta</strong>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="bAgregarOpcion"><i class="fas fa-plus me-1"></i>Agregar opción</button>
                </div>
                <div class="small text-muted mb-2 bnc-solo-encuesta">«Terminar» cierra la encuesta con ese mensaje. Los saltos a otras preguntas no se guardan en el banco porque dependen de cada encuesta.</div>
                <div class="bnc-opciones-editor" id="bOpciones"></div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary fw-bold" id="bGuardar">Guardar en el banco</button>
          </div>
        </div>
      </div>
    </div>

    <?php include './admin/include/footer.php'; ?>
  </div>

  <?php include 'admin/include/gerenic_script.php'; ?>
  <script>
    window.BANCO_PERMISOS = { crear: <?= $create ? 'true' : 'false' ?>, editar: <?= $edit ? 'true' : 'false' ?>, eliminar: <?= $delete ? 'true' : 'false' ?> };
  </script>
  <script type="text/javascript" src="admin/js/banco_preguntas.js"></script>
  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
