<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';
// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';


include './admin/classes/Departamento.php';
include './admin/classes/Pregunta.php';
include './admin/classes/FichaTecnicaEncuesta.php';

// Validar permisos
$view = SessionData::getPermission(30);
$create = SessionData::getPermission(31);
$edit = SessionData::getPermission(32);
$permits = SessionData::getPermission(33);
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

$arrPregunta = Pregunta::getAll(null);
$isvalidPregunta = $arrPregunta['output']['valid'];
$arrPregunta = $arrPregunta['output']['response'];


// Informacion de FichaTecnicaEncuesta
$arrFichaTecnicaEncuesta = FichaTecnicaEncuesta::getAll(null);
$arrFichaTecnicaEncuesta = $arrFichaTecnicaEncuesta['output']['response'];
$optionEncuentas = "";
foreach ($arrFichaTecnicaEncuesta as $val) {
    $optionEncuentas .= "<option value='" . $val['id'] . "'>" . $val['realizada_por_o_encomendada_por'] . " </option>";
}

$modulo = 'Cuestionario De Preguntas';
?>
<!-- *******************inicio body************************* -->

<body class="">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <!-- [ navigation menu ] start -->
  <?php include './admin/include/navbar.php'; ?>
  <!-- [ navigation menu ] end -->

  <!-- [ Header ] start -->
  <?php include './admin/include/header.php'; ?>
  <!-- [ Header ] end -->

  <style>
    :root{
      --nav-blue:#20427F;
      --nav-blue-2:#132b52;
      --nav-blue-3:#2e58a8;

      --ink:#0f172a;
      --muted:#64748b;

      --card-radius: 18px;
      --soft-border: rgba(15,23,42,.08);
      --soft-shadow: 0 18px 55px rgba(2,6,23,.10);
      --soft-shadow-2: 0 26px 80px rgba(2,6,23,.14);
    }

    /* ✅ Ajuste para que no quede pegado al header */
    .content{ padding-top: 34px !important; }

    /* ===== Page Head (SaaS) ===== */
    .saas-pagehead{
      border-radius: var(--card-radius);
      background:
        radial-gradient(900px 280px at 10% 0%, rgba(32,66,127,.22), transparent 55%),
        radial-gradient(700px 240px at 90% 0%, rgba(46,88,168,.16), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.97), rgba(255,255,255,.92));
      border: 1px solid var(--soft-border);
      box-shadow: var(--soft-shadow);
      padding: 16px 16px;
      margin: 18px 0 16px;
    }
    .saas-title{ display:flex; align-items:flex-start; gap:12px; flex-wrap:wrap; }
    .saas-icon{
      width:46px; height:46px; border-radius:16px;
      display:grid; place-items:center;
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2));
      color:#fff;
      box-shadow: 0 12px 30px rgba(32,66,127,.28);
      flex: 0 0 auto;
    }
    .saas-title h3{
      margin:0; font-weight:900; letter-spacing:-.35px; color:var(--ink);
      line-height:1.1;
    }
    .saas-sub{ color: var(--muted); font-size:.92rem; margin-top:4px; }

    .chipbar{ display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:7px 10px;
      border-radius:999px;
      border:1px solid rgba(2,6,23,.08);
      background: rgba(255,255,255,.86);
      font-size:.82rem;
      color: var(--ink);
    }
    .chip i{ color: var(--nav-blue); }

    /* ===== Cards ===== */
    .saas-card{
      border-radius: var(--card-radius);
      border: 1px solid var(--soft-border);
      box-shadow: var(--soft-shadow-2);
      overflow:hidden;
      background:#fff;
    }
    .saas-card .card-header{
      background: rgba(255,255,255,.94) !important;
      border-bottom: 1px solid var(--soft-border) !important;
      padding: 14px 16px !important;
    }
    .saas-card .card-header.card-header-grupo{
      background: linear-gradient(135deg,#20427F,#132b52) !important;
      border-bottom: none !important;
      padding: 10px 12px !important;
    }
    .saas-card .card-body{ padding: 16px !important; }

    /* ===== Buttons (SaaS) ===== */
    .btn-primary{
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2)) !important;
      border: 0 !important;
      box-shadow: 0 16px 40px rgba(32,66,127,.22);
      border-radius: 12px;
    }
    .btn-primary:hover{ transform: translateY(-1px); box-shadow: 0 22px 55px rgba(32,66,127,.28); }
    .btn-success, .btn-danger, .btn-outline-success, .btn-phoenix-secondary, .btn-phoenix-primary{
      border-radius: 12px !important;
    }

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

    /* ===== Accordion pro ===== */
    .accordion-item{
      border-radius: 16px !important;
      overflow:hidden;
      border: 1px solid rgba(15,23,42,.08) !important;
      box-shadow: 0 12px 28px rgba(2,6,23,.06);
      margin-bottom: 10px;
    }
    .accordion-button{
      font-weight: 900;
      letter-spacing: -.2px;
      background: rgba(255,255,255,.92);
    }
    .accordion-button:not(.collapsed){
      background:
        radial-gradient(900px 240px at 10% 0%, rgba(32,66,127,.14), transparent 55%),
        rgba(255,255,255,.92);
      color: var(--ink);
    }
    .accordion-body{ background: rgba(255,255,255,.92); }

    /* ===== Visual cards dentro de ayuda ===== */
    .help-grid .card{
      border-radius: 16px !important;
      overflow:hidden;
      border: 1px solid rgba(15,23,42,.08) !important;
      box-shadow: 0 16px 40px rgba(2,6,23,.08);
    }

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

    @media (max-width: 576px){
      .saas-icon{ width:40px; height:40px; border-radius: 13px; }
      .sticky-actions .btn{ width: 100%; }
    }

    /* Checkboxes de selección en tabla de preguntas */
    .pregunta-chk,
    .grupo-chk-all {
      width: 18px !important;
      height: 18px !important;
      border-radius: 5px !important;
      cursor: pointer;
      border: 2px solid rgba(32,66,127,.35) !important;
      transition: background .15s, border-color .15s;
      display: block;
      margin: 0 auto;
    }
    .pregunta-chk:checked,
    .grupo-chk-all:checked {
      background-color: #20427F !important;
      border-color: #20427F !important;
    }
    .pregunta-chk:hover,
    .grupo-chk-all:hover {
      border-color: #20427F !important;
    }
  </style>

  <!-- [ Main Content ] start -->
  <div class="content">
    <div class="col-11 col-xl-11 mx-auto">

      <!-- ===== PageHead SaaS ===== -->
      <div class="saas-pagehead">
        <div class="saas-title">
          <div class="saas-icon"><i class="uil uil-question-circle"></i></div>
          <div>
            <h3>Ingreso de <span id="spanModulo"><?php echo $modulo; ?></span></h3>
            <div class="saas-sub">
              Gestión de preguntas, opciones y carga masiva por CSV/Excel para tu ficha técnica.
            </div>
            <div class="small text-muted mt-1" id="spanEncuesta"></div>
          </div>
        </div>

        <div class="chipbar">
          <div class="chip"><i class="fas fa-file-csv"></i> Importación CSV</div>
          <div class="chip"><i class="fas fa-list-ol"></i> Orden y tipos</div>
          <div class="chip"><i class="fas fa-toggle-on"></i> Habilitado / Visualización</div>
        </div>

        <div class="d-flex gap-2 flex-wrap mt-3">
          <?php if ($create || $edit): ?>
          <button onclick="PREGUNTAS.openCreateModal()" class="btn px-4" type="button"
                  style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;border:0;border-radius:12px;box-shadow:0 12px 30px rgba(32,66,127,.28);">
            <i class="fas fa-plus me-2"></i>Nueva Pregunta
          </button>
          <?php endif; ?>
          <a href="preguntas.csv" download class="btn btn-primary px-4">
            <i class="fas fa-download me-2"></i>Descargar Plantilla
          </a>
          <button onclick="PREGUNTAS.showUploadModal()" class="btn btn-success px-4" type="button">
            <i class="fas fa-upload me-2"></i>Subir Preguntas
          </button>
          <button onclick="PREGUNTAS.showBatchModal()" class="btn px-4" type="button"
                  style="background:linear-gradient(135deg,#1e7e34,#155724);color:#fff;border:0;border-radius:12px;box-shadow:0 12px 30px rgba(21,87,36,.28);">
            <i class="fas fa-layer-group me-2"></i>Ingreso Masivo
          </button>
        </div>
      </div>

      <!-- ===== Ayuda Visual (Accordion) ===== -->
      <div class="card saas-card mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge" style="background: rgba(32,66,127,.12); color: var(--nav-blue); border:1px solid rgba(32,66,127,.18); font-weight:900; border-radius:999px;">
                <i class="fas fa-life-ring me-1"></i> Guía rápida
              </span>
              <strong style="letter-spacing:-.2px;">Ayuda visual para crear e importar preguntas</strong>
            </div>
            <div class="text-muted small d-none d-md-block">
              Tip: si vas a cargar muchas preguntas, usa CSV UTF-8.
            </div>
          </div>
        </div>

        <div class="card-body" style="background: rgba(248,250,252,.7);">
          <div class="accordion" id="accordionAyuda">

            <!-- Acordeón: Cómo usar el formulario -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingFormulario">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFormulario" aria-expanded="false">
                  <i class="fas fa-question-circle me-2 text-primary"></i>
                  <strong>¿Cómo usar el formulario de preguntas?</strong>
                </button>
              </h2>
              <div id="collapseFormulario" class="accordion-collapse collapse"
                   aria-labelledby="headingFormulario" data-bs-parent="#accordionAyuda">
                <div class="accordion-body">
                  <div class="row">
                    <div class="col-md-6">
                      <h6 class="text-primary mb-3"><i class="fas fa-list-ol me-2"></i>Pasos para agregar una pregunta:</h6>
                      <ol class="mb-0">
                        <li><strong>Seleccione la Ficha Técnica:</strong> Elija la encuesta a la que pertenece la pregunta</li>
                        <li><strong>Tipo de Pregunta:</strong> Seleccione el tipo según las opciones que necesita</li>
                        <li><strong>Escriba la Pregunta:</strong> Redacte la pregunta de forma clara</li>
                        <li><strong>Orden:</strong> Indique en qué posición aparecerá (1, 2, 3...)</li>
                        <li><strong>Opciones de Respuesta:</strong> Agregue las opciones haciendo clic en "Añadir Opción"</li>
                        <li><strong>Habilitado:</strong> Marque si desea que la pregunta esté activa</li>
                        <li><strong>Guardar:</strong> Haga clic en "Guardar Pregunta"</li>
                      </ol>
                    </div>
                    <div class="col-md-6">
                      <div class="alert alert-info mb-0">
                        <h6 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Consejos útiles:</h6>
                        <ul class="mb-0 small">
                          <li>Las preguntas se muestran según el <strong>orden</strong> que asigne</li>
                          <li>Puede <strong>editar</strong> cualquier pregunta haciendo clic en el botón de editar</li>
                          <li>El campo <strong>"Habilitado"</strong> le permite desactivar preguntas sin eliminarlas</li>
                          <li>Use opciones claras y concisas para facilitar el trabajo del encuestador</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Acordeón: Tipos de Preguntas -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTipos">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTipos" aria-expanded="false">
                  <i class="fas fa-th-list me-2 text-success"></i>
                  <strong>Tipos de preguntas disponibles</strong>
                </button>
              </h2>
              <div id="collapseTipos" class="accordion-collapse collapse"
                   aria-labelledby="headingTipos" data-bs-parent="#accordionAyuda">
                <div class="accordion-body">
                  <div class="row g-3 help-grid">
                    <!-- Dicotómica -->
                    <div class="col-md-6">
                      <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white"><strong>Dicotómica</strong></div>
                        <div class="card-body">
                          <p class="small mb-2"><strong>¿Cuándo usarla?</strong> Para preguntas de SÍ/NO o dos opciones excluyentes</p>
                          <p class="small mb-2"><strong>Ejemplo:</strong></p>
                          <div class="bg-light p-2 rounded small">
                            <strong>P:</strong> ¿Votaría por este candidato?<br>
                            <span class="text-muted">□ Sí &nbsp;&nbsp; □ No</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Selección Múltiple Única -->
                    <div class="col-md-6">
                      <div class="card border-success h-100">
                        <div class="card-header bg-success text-white"><strong>Selección Múltiple (Única Respuesta)</strong></div>
                        <div class="card-body">
                          <p class="small mb-2"><strong>¿Cuándo usarla?</strong> Varias opciones, se elige solo una</p>
                          <p class="small mb-2"><strong>Ejemplo:</strong></p>
                          <div class="bg-light p-2 rounded small">
                            <strong>P:</strong> ¿Cómo califica la gestión?<br>
                            <span class="text-muted">□ Excelente &nbsp; □ Buena &nbsp; □ Regular &nbsp; □ Mala</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Selección Múltiple Múltiple -->
                    <div class="col-md-6">
                      <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark"><strong>Selección Múltiple (Múltiple Respuesta)</strong></div>
                        <div class="card-body">
                          <p class="small mb-2"><strong>¿Cuándo usarla?</strong> Se pueden elegir varias opciones</p>
                          <p class="small mb-2"><strong>Ejemplo:</strong></p>
                          <div class="bg-light p-2 rounded small">
                            <strong>P:</strong> ¿Qué temas son prioritarios?<br>
                            <span class="text-muted">☑ Salud &nbsp; ☑ Educación &nbsp; □ Seguridad</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Preguntas Cardinales -->
                    <div class="col-md-6">
                      <div class="card border-info h-100">
                        <div class="card-header bg-info text-white"><strong>Preguntas Cardinales</strong></div>
                        <div class="card-body">
                          <p class="small mb-2"><strong>¿Cuándo usarla?</strong> Escala de intensidad o nivel</p>
                          <p class="small mb-2"><strong>Ejemplo:</strong></p>
                          <div class="bg-light p-2 rounded small">
                            <strong>P:</strong> ¿Qué tan satisfecho está?<br>
                            <span class="text-muted">□ Muy Satisfecho → Satisfecho → Neutral → Insatisfecho</span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Preguntas Ordinales -->
                    <div class="col-md-6">
                      <div class="card border-secondary h-100">
                        <div class="card-header bg-secondary text-white"><strong>Preguntas Ordinales</strong></div>
                        <div class="card-body">
                          <p class="small mb-2"><strong>¿Cuándo usarla?</strong> Opciones con orden jerárquico</p>
                          <p class="small mb-2"><strong>Ejemplo:</strong></p>
                          <div class="bg-light p-2 rounded small">
                            <strong>P:</strong> ¿Nivel de estudios?<br>
                            <span class="text-muted">□ Primaria → Secundaria → Universitaria → Postgrado</span>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div><!-- /row -->
                </div>
              </div>
            </div>

            <!-- Acordeón: Importación CSV -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingCSV">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseCSV" aria-expanded="false">
                  <i class="fas fa-file-csv me-2 text-danger"></i>
                  <strong>¿Cómo importar preguntas desde CSV/Excel?</strong>
                </button>
              </h2>
              <div id="collapseCSV" class="accordion-collapse collapse"
                   aria-labelledby="headingCSV" data-bs-parent="#accordionAyuda">
                <div class="accordion-body">
                  <div class="row">
                    <div class="col-md-8">
                      <h6 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Pasos para importar preguntas masivamente:</h6>
                      <div class="timeline">
                        <div class="timeline-item mb-3">
                          <div class="d-flex">
                            <div class="badge bg-primary rounded-circle me-3" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;">1</div>
                            <div>
                              <strong>Descargue la plantilla</strong>
                              <p class="text-muted small mb-0">Haga clic en "Descargar Plantilla" arriba</p>
                            </div>
                          </div>
                        </div>
                        <div class="timeline-item mb-3">
                          <div class="d-flex">
                            <div class="badge bg-primary rounded-circle me-3" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;">2</div>
                            <div>
                              <strong>Abra el archivo en Excel</strong>
                              <p class="text-muted small mb-0">Verá columnas como: ID Encuesta, Pregunta, Tipo, Orden, Opciones...</p>
                            </div>
                          </div>
                        </div>
                        <div class="timeline-item mb-3">
                          <div class="d-flex">
                            <div class="badge bg-primary rounded-circle me-3" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;">3</div>
                            <div>
                              <strong>Complete las filas</strong>
                              <p class="text-muted small mb-0">Una fila = una pregunta. Mínimo: ID, Pregunta, y 2 opciones</p>
                            </div>
                          </div>
                        </div>
                        <div class="timeline-item mb-3">
                          <div class="d-flex">
                            <div class="badge bg-primary rounded-circle me-3" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;">4</div>
                            <div>
                              <strong>Guarde como CSV UTF-8</strong>
                              <p class="text-muted small mb-0">Excel: Archivo → Guardar como → CSV UTF-8 (delimitado por comas)</p>
                            </div>
                          </div>
                        </div>
                        <div class="timeline-item mb-3">
                          <div class="d-flex">
                            <div class="badge bg-success rounded-circle me-3" style="width:30px;height:30px;display:flex;align-items:center;justify-content:center;">5</div>
                            <div>
                              <strong>Súbalo al sistema</strong>
                              <p class="text-muted small mb-0">Botón "Subir Preguntas" → Seleccionar archivo → Cargar</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Importante:</h6>
                        <ul class="small mb-0">
                          <li><strong>ID de Encuesta:</strong> Debe existir en el sistema</li>
                          <li><strong>Mínimo 2 opciones:</strong> opc_1 y opc_2</li>
                          <li><strong>Formato UTF-8:</strong> para tildes</li>
                          <li><strong>Máximo 5 opciones:</strong> por pregunta</li>
                        </ul>
                      </div>
                      <div class="alert alert-info mb-0">
                        <h6 class="alert-heading"><i class="fas fa-book me-2"></i>Guía completa:</h6>
                        <p class="small mb-0">Consulte <strong>GUIA_USUARIO_IMPORTACION_PREGUNTAS.md</strong> en el proyecto.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /accordion -->
        </div>
      </div>


 

 
<!-- ****************** fin body*************************** -->
        <!-- Vista agrupada por ficha técnica -->
        <div class="p-3">
          <?php if ($isvalidPregunta && count($arrPregunta) > 0):
            // Agrupar: ficha → capítulo → enunciado → preguntas
            $grupos = [];
            foreach ($arrPregunta as $item) {
              $fichaKey     = $item['tbl_ficha_tecnica_encuesta_id'];
              $capituloKey  = !empty($item['capitulo'])          ? $item['capitulo']          : '__sin_capitulo__';
              $enunciadoKey = !empty($item['enunciado_pregunta']) ? $item['enunciado_pregunta'] : '__sin_enunciado__';
              if (!isset($grupos[$fichaKey])) {
                $grupos[$fichaKey]['ficha']           = $item['realizada_por_o_encomendada_por'];
                $grupos[$fichaKey]['temas_concretos'] = $item['temas_concretos'];
                $grupos[$fichaKey]['eg_tipo_estudio'] = $item['espacio_geografico_tipo_estudio'];
                $grupos[$fichaKey]['eg_observaciones']= $item['espacio_geografico_observaciones'];
                $grupos[$fichaKey]['capitulos']       = [];
              }
              if (!isset($grupos[$fichaKey]['capitulos'][$capituloKey])) {
                $grupos[$fichaKey]['capitulos'][$capituloKey] = ['enunciados' => []];
              }
              $grupos[$fichaKey]['capitulos'][$capituloKey]['enunciados'][$enunciadoKey][] = $item;
            }
          ?>

          <?php
          $totalPreguntas = count($arrPregunta);
          foreach ($grupos as $fichaId => $grupo):
            $totalFicha = 0;
            foreach ($grupo['capitulos'] as $cap) {
              foreach ($cap['enunciados'] as $eItems) { $totalFicha += count($eItems); }
            }
            // Obtener el primer enunciado no vacío para el botón "Enunciado"
            $enunciadoActualGrupo = '';
            foreach ($grupo['capitulos'] as $cap) {
              foreach ($cap['enunciados'] as $eKey => $eItems) {
                if ($eKey !== '__sin_enunciado__') { $enunciadoActualGrupo = $eKey; break 2; }
              }
            }
          ?>
          <div class="card saas-card mb-4">
            <!-- Header de la ficha técnica -->
            <div class="card-header card-header-grupo px-3 py-2" style="border-radius:var(--card-radius) var(--card-radius) 0 0;">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <!-- Info de la ficha -->
                <div class="d-flex align-items-center flex-wrap gap-2">
                  <i class="fas fa-clipboard-list text-white opacity-75"></i>
                  <span class="fw-bold text-white" style="font-size:.92rem;">
                    <?php echo htmlspecialchars($grupo['ficha'], ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                  <?php if (!empty($grupo['temas_concretos'])): ?>
                  <span class="badge" style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;padding:4px 10px;font-size:.75rem;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                        title="<?= htmlspecialchars($grupo['temas_concretos'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="fas fa-tag me-1 opacity-75"></i><?= htmlspecialchars($grupo['temas_concretos'], ENT_QUOTES, 'UTF-8') ?>
                  </span>
                  <?php endif; ?>
                  <?php if (!empty($grupo['eg_tipo_estudio']) || !empty($grupo['eg_observaciones'])): ?>
                  <span class="badge" style="background:rgba(255,255,255,.12);color:#cfe2ff;border-radius:8px;padding:4px 10px;font-size:.75rem;">
                    <i class="fas fa-map-marker-alt me-1 opacity-75"></i>
                    <?= htmlspecialchars(trim(($grupo['eg_tipo_estudio'] ?? '') . ' - ' . ($grupo['eg_observaciones'] ?? ''), ' -'), ENT_QUOTES, 'UTF-8') ?>
                  </span>
                  <?php endif; ?>
                  <span class="badge" style="background:rgba(255,255,255,.18);color:#fff;border-radius:8px;font-size:.73rem;">
                    <?= $totalFicha ?> preg.
                  </span>
                </div>
                <!-- Acciones -->
                <div class="d-flex gap-2 flex-wrap align-items-center">
                  <?php if ($edit): ?>
                  <button type="button" class="btn btn-sm"
                          style="border-radius:8px;background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25);font-size:.78rem;"
                          title="Editar nombre de la ficha técnica"
                           onclick='PREGUNTAS.editFichaTemas(<?= $fichaId ?>, <?= json_encode($grupo['temas_concretos'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)'>
                    <i class="fas fa-pen me-1"></i>Ficha
                  </button>
                  <button type="button" class="btn btn-sm"
                          style="border-radius:8px;background:rgba(255,193,7,.2);color:#ffc107;border:1px solid rgba(255,193,7,.35);font-size:.78rem;"
                          title="Editar Enunciado en preguntas seleccionadas"
                          data-ficha-id="<?= $fichaId ?>"
                          onclick="PREGUNTAS.editEnunciadoGrupo(this)">
                    <i class="fas fa-align-left me-1"></i>Enunciado <span class="badge bg-warning ms-1" style="font-size:.65rem;color:#000;">Check</span>
                  </button>
                  <button type="button" class="btn btn-sm"
                          style="border-radius:8px;background:rgba(13,202,240,.2);color:#0dcaf0;border:1px solid rgba(13,202,240,.35);font-size:.78rem;"
                          title="Editar Numeral / Texto Adicional en preguntas seleccionadas"
                          onclick="PREGUNTAS.editNumeralAdicionalMasivo(<?= $fichaId ?>)">
                    <i class="fas fa-hashtag me-1"></i>Numeral <span class="badge bg-info ms-1" style="font-size:.65rem;color:#000;">Check</span>
                  </button>
                  <?php endif; ?>
                  <button type="button" class="btn btn-sm"
                          style="border-radius:8px;background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);font-size:.78rem;"
                          onclick="PREGUNTAS.toggleGrupo(<?= $fichaId ?>)">
                    <i class="fas fa-chevron-down me-1" id="chevronGrupo<?= $fichaId ?>"></i>Ver/Ocultar
                  </button>
                </div>
              </div>
            </div>

            <div id="grupoBody<?= $fichaId ?>">
              <?php foreach ($grupo['capitulos'] as $capituloKey => $capData):
                $esSinCapitulo  = ($capituloKey === '__sin_capitulo__');
                $capituloTexto  = $esSinCapitulo ? null : $capituloKey;
                $capituloIdx    = md5($fichaId . $capituloKey);
              ?>

              <?php if (!$esSinCapitulo): ?>
              <!-- Header de capítulo con collapse -->
              <div class="px-3 pt-3 pb-0">
                <div class="d-flex align-items-center gap-0 rounded-3 overflow-hidden"
                     style="background:linear-gradient(135deg,#20427F,#132b52);">
                  <div class="d-flex align-items-center gap-2 px-3 py-2 flex-grow-1 cursor-pointer"
                       onclick="PREGUNTAS.toggleCapitulo('<?= $capituloIdx ?>')">
                    <i class="fas fa-layer-group text-white opacity-75" style="font-size:.85rem;"></i>
                    <span class="fw-bold text-white" style="font-size:.88rem;flex:1;">
                      <?= htmlspecialchars($capituloTexto, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php
                      $totalCap = 0;
                      foreach ($capData['enunciados'] as $eItems) { $totalCap += count($eItems); }
                    ?>
                    <span class="badge" style="background:rgba(255,255,255,.18);color:#fff;border-radius:8px;font-size:.72rem;">
                      <?= $totalCap ?> preg.
                    </span>
                    <?php if ($edit): ?>
                    <label class="d-flex align-items-center gap-1 mb-0 ms-1"
                           style="cursor:pointer;"
                           title="Seleccionar todas las preguntas de este capítulo"
                           onclick="event.stopPropagation()">
                      <input type="checkbox" class="form-check-input capitulo-chk-all mt-0"
                             data-capitulo="<?= $capituloIdx ?>"
                             style="width:15px;height:15px;">
                      <span class="text-white" style="font-size:.75rem;opacity:.85;">Todos</span>
                    </label>
                    <?php endif; ?>
                    <i class="fas fa-chevron-down text-white ms-2" id="chevronCapitulo<?= $capituloIdx ?>" style="font-size:.75rem;transition:transform .2s;"></i>
                  </div>
                  <?php if ($edit): ?>
                  <button type="button"
                          class="btn btn-sm px-3 py-2 h-100 border-0 border-start"
                          style="background:rgba(255,255,255,.12);border-left:1px solid rgba(255,255,255,.2)!important;color:#fff;border-radius:0;font-size:.72rem;"
                          title="Renombrar capítulo"
                          onclick="PREGUNTAS.renameCapitulo('<?= trim(json_encode($capituloTexto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), '"') ?>', <?= $fichaId ?>)">
                    <i class="fas fa-pencil-alt me-1"></i>Renombrar
                  </button>
                  <?php endif; ?>
                </div>
              </div>
              <div id="capituloBody<?= $capituloIdx ?>" style="display:none;">
              <?php endif; ?>

              <?php foreach ($capData['enunciados'] as $enunciadoKey => $items):
                $esSinEnunciado = ($enunciadoKey === '__sin_enunciado__');
                $enunciadoTexto = $esSinEnunciado ? null : $enunciadoKey;
                $enunciadoId = md5($fichaId . $capituloKey . $enunciadoKey);
              ?>
              <!-- Sub-grupo por enunciado -->
              <?php if (!$esSinEnunciado): ?>
              <?php $idsGrupo = implode(',', array_column($items, 'id')); ?>
              <div class="px-3 pt-2 pb-1 <?= !$esSinCapitulo ? 'ps-4' : '' ?>">
                <div class="p-2 rounded-3 d-flex align-items-center gap-2 flex-wrap"
                     style="background:rgba(32,66,127,.06);border-left:3px solid #20427F;">
                  <i class="fas fa-quote-left text-primary mt-1" style="font-size:.75rem;opacity:.6;"></i>
                  <span class="small fw-semibold text-primary" style="line-height:1.4;" id="enunciadoGrupoTexto<?= $fichaId ?>_<?= $enunciadoId ?>">
                    <?= htmlspecialchars($enunciadoTexto, ENT_QUOTES, 'UTF-8') ?>
                  </span>
                  <span class="badge bg-secondary ms-auto flex-shrink-0" style="border-radius:8px;font-size:.7rem;">
                    <?= count($items) ?> preg.
                  </span>
                  <?php if ($edit): ?>
                  <button type="button" class="btn btn-sm btn-outline-warning flex-shrink-0"
                          style="border-radius:8px;font-size:.72rem;"
                          title="Editar enunciado de este grupo"
                           onclick='PREGUNTAS.editEnunciadoGrupo("<?= $idsGrupo ?>", <?= json_encode($enunciadoTexto, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)'>
                    <i class="fas fa-pen me-1"></i>Editar Enunciado
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                          style="border-radius:8px;font-size:.75rem;"
                          title="Reasignar este grupo a otra ficha técnica"
                          onclick="PREGUNTAS.reasignarGrupo('<?= $idsGrupo ?>')">
                    <i class="fas fa-exchange-alt me-1"></i>Reasignar
                  </button>
                  <?php endif; ?>
                </div>
              </div>
              <?php endif; ?>

              <div class="table-responsive <?= !$esSinEnunciado ? 'ps-3 pe-1 pb-2' : '' ?>">
                <table class="table table-sm mb-0 fs-9">
                  <?php if ($esSinEnunciado): ?>
                  <thead style="background:rgba(32,66,127,.05);">
                    <tr>
                      <?php if ($edit): ?><th style="width:32px;"><input type="checkbox" class="form-check-input grupo-chk-all" data-grupo="<?= $fichaId ?>_sin" title="Seleccionar todo"></th><?php endif; ?>
                      <th style="width:130px;">Acciones</th>
                      <th>Texto de la Pregunta</th>
                      <th style="min-width:240px;">Enunciado</th>
                      <th style="min-width:90px;">Numeral</th>
                      <th style="min-width:180px;">Texto Adicional</th>
                      <th style="display:none;">Tipo</th>
                      <th>Opciones</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <?php else: ?>
                  <thead style="background:rgba(32,66,127,.03);">
                    <tr>
                      <?php if ($edit): ?><th style="width:32px;"><input type="checkbox" class="form-check-input grupo-chk-all" data-grupo="<?= $fichaId ?>_<?= $enunciadoId ?>" title="Seleccionar todo"></th><?php endif; ?>
                      <th style="width:130px;">Acciones</th>
                      <th>Texto de la Pregunta</th>
                      <th style="min-width:220px;">Enunciado</th>
                      <th style="min-width:90px;">Numeral</th>
                      <th style="min-width:180px;">Texto Adicional</th>
                      <th style="display:none;">Tipo</th>
                      <th>Opciones</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <?php endif; ?>
                  <tbody>
                    <?php foreach ($items as $item):
                      $esActivo = isset($item['habilitado']) && $item['habilitado'] === 'si';
                    ?>
                    <?php
                      $grupoChkKey = $esSinEnunciado
                        ? $fichaId . '_sin'
                        : $fichaId . '_' . $enunciadoId;
                    ?>
                    <tr id="fila<?= $item['id'] ?>">
                      <!-- Checkbox selección -->
                      <?php if ($edit): ?>
                      <td class="text-center" style="vertical-align:middle;">
                        <input type="checkbox" class="form-check-input pregunta-chk"
                               data-grupo="<?= $grupoChkKey ?>"
                               data-capitulo="<?= $capituloIdx ?>"
                               value="<?= $item['id'] ?>">
                      </td>
                      <?php endif; ?>

                      <!-- Acciones -->
                      <td>
                        <div class="d-flex gap-1">
                          <?php if ($edit): ?>
                          <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                  style="border-radius:8px;"
                                  onclick="PREGUNTAS.editData(<?= $item['id'] ?>)">
                            <i class="uil uil-edit"></i>
                          </button>
                          <button type="button"
                                  class="btn btn-sm <?= $esActivo ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                  title="<?= $esActivo ? 'Deshabilitar' : 'Habilitar' ?>"
                                  style="border-radius:8px;"
                                  id="btnHab<?= $item['id'] ?>"
                                  onclick="PREGUNTAS.toggleHabilitado(<?= $item['id'] ?>, <?= $esActivo ? 'true' : 'false' ?>)">
                            <i class="fas <?= $esActivo ? 'fa-toggle-off' : 'fa-toggle-on' ?>"></i>
                          </button>
                          <?php if ($permits): ?>
                          <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                  style="border-radius:8px;"
                                                   onclick='PREGUNTAS.deletePregunta(<?= $item['id'] ?>, <?= json_encode($item['texto_pregunta'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)'>
                            <i class="fas fa-trash-alt"></i>
                          </button>
                          <?php endif; ?>
                          <?php endif; ?>
                        </div>
                      </td>

                      <td class="fw-semibold"><?= htmlspecialchars($item['texto_pregunta'], ENT_QUOTES, 'UTF-8') ?></td>

                      <?php if ($esSinEnunciado): ?>
                      <!-- Enunciado editable en línea (solo para sin-enunciado) -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <span class="small" id="enunciadoSpan<?= $item['id'] ?>">
                            <em class="text-muted">Sin enunciado</em>
                          </span>
                          <?php if ($edit): ?>
                          <button type="button" class="btn btn-link btn-sm p-0 ms-1 flex-shrink-0"
                                  title="Editar enunciado" style="font-size:.75rem;color:#20427F;"
                                  onclick="PREGUNTAS.editEnunciado(<?= $item['id'] ?>, '')">
                            <i class="fas fa-pen"></i>
                          </button>
                          <?php endif; ?>
                        </div>
                      </td>
                      <?php else: ?>
                      <!-- Enunciado visible en filas de grupo con enunciado -->
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          <span class="small text-primary" style="line-height:1.4;" id="enunciadoSpan<?= $item['id'] ?>">
                            <?= htmlspecialchars($item['enunciado_pregunta'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                          </span>
                          <?php if ($edit): ?>
                          <button type="button" class="btn btn-link btn-sm p-0 ms-1 flex-shrink-0"
                                  title="Editar enunciado" style="font-size:.75rem;color:#20427F;"
                                  onclick='PREGUNTAS.editEnunciado(<?= $item['id'] ?>, <?= json_encode($item['enunciado_pregunta'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)'>
                            <i class="fas fa-pen"></i>
                          </button>
                          <?php endif; ?>
                        </div>
                      </td>
                      <?php endif; ?>

                      <!-- Numeral -->
                      <td id="numeralCell<?= $item['id'] ?>">
                        <div class="d-flex align-items-center gap-1">
                          <span class="small fw-semibold text-secondary" id="numeralSpan<?= $item['id'] ?>">
                            <?php if (!empty($item['numeral'])): ?>
                              <span class="badge" style="background:rgba(32,66,127,.12);color:#20427F;border:1px solid rgba(32,66,127,.2);border-radius:6px;font-size:.8rem;"><?= htmlspecialchars($item['numeral'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php else: ?>
                              <em class="text-muted" style="font-size:.75rem;">—</em>
                            <?php endif; ?>
                          </span>
                          <?php if ($edit): ?>
                          <button type="button" class="btn btn-link btn-sm p-0 flex-shrink-0"
                                  title="Editar numeral y texto adicional"
                                  style="font-size:.72rem;color:#0ea5e9;"
                                   onclick='PREGUNTAS.editNumeralAdicionalUno(<?= $item['id'] ?>, <?= json_encode($item['numeral'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>, <?= json_encode($item['texto_adicional'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)'>
                            <i class="fas fa-pen"></i>
                          </button>
                          <?php endif; ?>
                        </div>
                      </td>

                      <!-- Texto Adicional -->
                      <td id="textoAdicionalCell<?= $item['id'] ?>">
                        <span class="small text-muted" id="textoAdicionalSpan<?= $item['id'] ?>">
                          <?php if (!empty($item['texto_adicional'])): ?>
                            <?= htmlspecialchars($item['texto_adicional'], ENT_QUOTES, 'UTF-8') ?>
                          <?php else: ?>
                            <em style="font-size:.75rem;">—</em>
                          <?php endif; ?>
                        </span>
                      </td>

                      <td style="display:none;"><span class="badge bg-light text-dark border" style="border-radius:8px;font-size:.75rem;"><?= htmlspecialchars($item['tipo_pregunta'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>

                      <td>
                        <?php if (!empty($item['opciones'])): ?>
                        <button type="button" class="btn btn-outline-info p-0" style="border-radius:6px;width:28px;height:28px;font-size:.75rem;"
                                onclick="PREGUNTAS.showOptionsModal('<?= htmlspecialchars(json_encode($item['opciones']), ENT_QUOTES, 'UTF-8') ?>','<?= htmlspecialchars($item['texto_pregunta'], ENT_QUOTES, 'UTF-8') ?>')">
                          <i class="uil uil-eye" style="font-size:.85rem;"></i>
                        </button>
                        <?php else: ?>
                        <span class="text-muted" style="font-size:.75rem;">—</span>
                        <?php endif; ?>
                      </td>

                      <td id="estadoFila<?= $item['id'] ?>">
                        <?php if ($esActivo): ?>
                          <span class="badge bg-success" style="border-radius:8px;">Activo</span>
                        <?php else: ?>
                          <span class="badge bg-danger" style="border-radius:8px;">Inactivo</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <?php endforeach; // enunciados ?>
              <?php if (!$esSinCapitulo): ?></div><?php endif; ?>
              <?php endforeach; // capítulos ?>
            </div>
          </div>
          <?php endforeach; // fichas ?>

          <?php else: ?>
          <div class="alert alert-info text-center">No hay preguntas registradas.</div>
          <?php endif; ?>
        </div>

        <!-- Modal edición de temas concretos (nombre de la ficha técnica) -->
        <div class="modal fade" id="editFichaTemasModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0"><i class="fas fa-tag me-2"></i>Editar Temas Concretos</h5>
                  <small class="opacity-75">Nombre identificador de la ficha técnica</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <input type="hidden" id="editFichaTemasId">
                <label class="form-label fw-bold">Temas Concretos <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editFichaTemasTexto"
                       placeholder="Ej: Evaluación de candidatos presidenciales 2026"
                       style="border-radius:12px;">
                <div class="text-muted small mt-2">
                  <i class="fas fa-info-circle me-1"></i>
                  Este campo identifica el tema principal de la ficha técnica encuesta.
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveFichaTemas()">
                  <i class="fas fa-save me-2"></i>Guardar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal edición rápida de enunciado - Diseño Vistoso -->
        <div class="modal fade" id="editEnunciadoModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;border:0;box-shadow:0 25px 60px rgba(2,6,23,.18);">
              <!-- Header gradiente -->
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;border-bottom:0;padding:16px 24px;">
                <div>
                  <h5 class="modal-title mb-0" style="font-weight:800;letter-spacing:-.3px;">
                    <i class="fas fa-align-left me-2"></i>Editar Enunciado
                  </h5>
                  <small class="opacity-75" id="editEnunciadoSubtitle" style="font-size:.78rem;">Edición en lote</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="filter:brightness(2);"></button>
              </div>

              <div class="modal-body p-4" style="background:rgba(248,250,252,.6);">
                <input type="hidden" id="editEnunciadoPreguntaId">
                <input type="hidden" id="editEnunciadoFichaId">
                <input type="hidden" id="editEnunciadoModo">

                <!-- Icono visual -->
                <div class="text-center mb-3">
                  <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#20427F,#2e58a8);display:inline-grid;place-items:center;box-shadow:0 12px 30px rgba(32,66,127,.28);">
                    <i class="fas fa-quote-left text-white" style="font-size:1.4rem;"></i>
                  </div>
                </div>

                <label class="form-label fw-bold" style="font-size:.88rem;color:#0f172a;">
                  <i class="fas fa-pen me-1 text-primary"></i> Enunciado / Contexto
                </label>
                <textarea class="form-control" id="editEnunciadoTexto" rows="4"
                          placeholder="Ej: Califique los principales problemas que afectan hoy a Santander, siendo 1 'poco importante' y 5 'muy importante'..."
                          style="border-radius:12px;border:1px solid rgba(15,23,42,.12);font-size:.9rem;"></textarea>

                <!-- Ayuda visual -->
                <div class="mt-3 p-3 rounded-3" style="background:rgba(32,66,127,.05);border-left:3px solid #20427F;">
                  <div class="d-flex gap-2 align-items-start">
                    <i class="fas fa-lightbulb text-primary mt-1" style="font-size:.85rem;opacity:.7;"></i>
                    <div>
                      <div class="small fw-semibold text-primary" style="font-size:.82rem;">¿Qué es un enunciado?</div>
                      <div class="text-muted" style="font-size:.78rem;line-height:1.4;">
                        Es un texto que aparece <strong>antes</strong> de las preguntas para dar contexto al encuestado.
                        Ejemplo: "Califique del 1 al 5 la gestión del alcalde actual..."
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Info de edición (oculto ahora) -->
                <div id="editEnunciadoOpcionGrupo" style="display:none;"></div>
              </div>

              <div class="modal-footer" style="background:#fff;border-top:1px solid rgba(15,23,42,.08);">
                <button type="button" class="btn btn-phoenix-secondary px-4" data-bs-dismiss="modal" style="border-radius:10px;">
                  <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveEnunciado()" style="border-radius:10px;box-shadow:0 12px 30px rgba(32,66,127,.25);">
                  <i class="fas fa-save me-1"></i>Guardar Enunciado
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal: Reasignar grupo a otra ficha técnica -->
        <div class="modal fade" id="reasignarModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0"><i class="fas fa-exchange-alt me-2"></i>Reasignar a otra ficha técnica</h5>
                  <small class="opacity-75" id="reasignarSubtitle">Todas las preguntas del grupo se moverán</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <input type="hidden" id="reasignarIds">
                <label class="form-label fw-bold">Selecciona la ficha técnica destino</label>
                <select class="form-select" id="reasignarFichaId" style="border-radius:12px;">
                  <option value="">-- Selecciona --</option>
                  <?php foreach ($arrFichaTecnicaEncuesta as $f): ?>
                  <option value="<?= (int)$f['id'] ?>"><?= htmlspecialchars($f['realizada_por_o_encomendada_por'], ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.confirmarReasignar()">
                  <i class="fas fa-exchange-alt me-2"></i>Reasignar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal para mostrar opciones de pregunta -->
        <div class="modal fade" id="optionsModal" tabindex="-1" aria-labelledby="optionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="optionsModalLabel">Opciones de la pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí se inyectará el título de la pregunta -->
                        <p class="fw-semibold mb-3" id="modalQuestionText"></p>
                        <!-- Aquí se inyectarán las opciones de respuesta -->
                        <div id="modalOptionsList" class="row">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Numeral / Texto Adicional (individual o masivo) -->
        <div class="modal fade" id="editNumeralAdicionalModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0"><i class="fas fa-hashtag me-2"></i>Numeral / Texto Adicional</h5>
                  <small class="opacity-75" id="editNumeralAdicionalSubtitle">Editar campos</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <input type="hidden" id="editNumeralAdicionalIds"> <!-- CSV de IDs -->

                <div class="mb-3">
                  <label class="form-label fw-bold">Numeral <span class="text-muted fw-normal small">(ej: 1.1, 2.3.a)</span></label>
                  <input type="text" class="form-control" id="editNumeralInput"
                         placeholder="Numeral jerárquico del enunciado"
                         style="border-radius:12px;">
                  <div class="text-muted small mt-1"><i class="fas fa-info-circle me-1"></i>Déjalo vacío para borrar el valor actual.</div>
                </div>

                <div class="mb-1">
                  <label class="form-label fw-bold">Texto Adicional <span class="text-muted fw-normal small">(contexto o instrucción extra)</span></label>
                  <textarea class="form-control" id="editTextoAdicionalInput" rows="3"
                            placeholder="Texto de contexto o instrucción extra del enunciado"
                            style="border-radius:12px;"></textarea>
                  <div class="text-muted small mt-1"><i class="fas fa-info-circle me-1"></i>Déjalo vacío para borrar el valor actual.</div>
                </div>

                <div id="editNumeralAdicionalMasivoInfo" class="mt-3 p-2 rounded-3" style="display:none;background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.2);">
                  <i class="fas fa-layer-group me-1 text-info"></i>
                  <span class="small text-info fw-semibold" id="editNumeralAdicionalMasivoCount"></span>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveNumeralAdicional()">
                  <i class="fas fa-save me-2"></i>Guardar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal: Crear / Editar Pregunta -->
        <div class="modal fade" id="preguntaFormModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;border:0;box-shadow:0 25px 60px rgba(2,6,23,.18);">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;border-bottom:0;padding:16px 24px;">
                <div>
                  <h5 class="modal-title mb-0" style="font-weight:800;letter-spacing:-.3px;">
                    <i class="fas fa-pen-nib me-2"></i>Crear / Editar Pregunta
                  </h5>
                  <small class="opacity-75" style="font-size:.78rem;">Complete los campos y guarde</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="filter:brightness(2);"></button>
              </div>

              <div class="modal-body p-4" style="background:rgba(248,250,252,.6);">
                <form id="formPreguntas" class="row g-3 mb-0" role="form" autocomplete="false">
                  <input type="hidden" name="op" id="op" />
                  <input type="hidden" name="id" id="id" />

                  <div class="col-sm-12 col-md-6">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_ficha_tecnica_encuesta_id"
                              name="tbl_ficha_tecnica_encuesta_id" required>
                        <?php echo $optionEncuentas; ?>
                      </select>
                      <label for="tbl_ficha_tecnica_encuesta_id">
                        Ficha Técnica de la Encuesta <span class="text-danger">*</span>
                      </label>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-6">
                    <div class="form-floating">
                      <select onchange="OPCIONES.handleTipoPreguntaChange('tipo_pregunta');"
                              class="form-select" id="tipo_pregunta" name="tipo_pregunta">
                        <option value="">Seleccione</option>
                        <option value="Dicotomica">Dicotómica</option>
                        <option value="Preguntas_Ordinales">Preguntas Ordinales</option>
                        <option value="Preguntas_Cardinales">Preguntas Cardinales</option>
                        <option value="Seleccion_Multiple_unica_respuesta">Selección múltiple con única respuesta</option>
                        <option value="Seleccion_Multiple_multiple_respuesta">Selección múltiple con múltiple respuesta</option>
                      </select>
                      <label for="tipo_pregunta">Tipo de Pregunta</label>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="enunciado_pregunta"
                             name="enunciado_pregunta" placeholder="Enunciado de la pregunta" value="">
                      <label for="enunciado_pregunta">Enunciado de la Pregunta</label>
                    </div>
                    <small class="text-muted">Contexto o introducción opcional que acompaña la pregunta</small>
                  </div>

                  <div class="col-sm-12 col-md-9">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="texto_pregunta"
                             name="texto_pregunta" placeholder="Ingrese el texto de la pregunta" value="" required>
                      <label for="texto_pregunta">Texto de la Pregunta <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-3">
                    <div class="form-floating">
                      <input type="number" class="form-control" id="orden"
                             name="orden" placeholder="Orden" value="1" min="1">
                      <label for="orden">Orden</label>
                    </div>
                  </div>

                  <div id="divLimiteRespuesta" class="col-sm-12 col-md-3" style="display: none;">
                    <div class="form-floating">
                      <input type="number" class="form-control" id="limite_respuesta_multiple"
                             name="limite_respuesta_multiple" value="1" min="1">
                      <label for="limite_respuesta_multiple">Límite Respuesta - Opción Múltiple</label>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="habilitado"
                             name="habilitado" value="si" checked>
                      <label class="form-check-label" for="habilitado">Pregunta Habilitada</label>
                    </div>
                    <small class="text-muted">Desmarque si desea deshabilitar esta pregunta</small>
                  </div>

                  <div class="col-sm-12 col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="visualizacion"
                             name="visualizacion" value="si" checked>
                      <label class="form-check-label" for="visualizacion">Visualización en el estudio</label>
                    </div>
                    <small class="text-muted">Desmarque si desea ocultar esta pregunta en el estudio</small>
                  </div>

                  <div class="col-12">
                    <h5 class="mt-3 mb-2" style="font-weight:900; letter-spacing:-.2px;">Opciones de Respuesta</h5>
                    <div id="opcionesContainer">
                      <p id="noOptionsMessage" class="text-muted" style="display:none;">
                        No hay opciones de respuesta. ¡Añade una!
                      </p>
                    </div>
                    <button type="button" class="btn btn-phoenix-primary btn-sm mt-2"
                            id="addOptionBtn" onclick="PREGUNTAS.addOptionBtnClick();">
                      <span class="fas fa-plus me-1"></span> Añadir Opción
                    </button>
                  </div>

                </form>
              </div>

              <div class="modal-footer" style="background:#fff;border-top:1px solid rgba(15,23,42,.08);">
                <div class="text-muted small me-auto">
                  <i class="fas fa-info-circle me-1"></i>
                  Tip: Define bien el tipo para que el sistema genere opciones correctamente.
                </div>
                <button type="button"
                        onclick="PREGUNTAS.clearForm('formPreguntas');"
                        class="btn btn-phoenix-secondary px-4" style="border-radius:10px;">
                  Cancelar
                </button>
                <?php if ($create && $edit): ?>
                  <button class="btn btn-primary px-4" type="button"
                          onclick="PREGUNTAS.validateData();"
                          style="border-radius:10px;box-shadow:0 12px 30px rgba(32,66,127,.25);">
                    <i class="fas fa-save me-2"></i>Guardar Pregunta
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== Modal: Ingreso Masivo de Preguntas ===== -->
        <div class="modal fade" id="batchModal" tabindex="-1" aria-labelledby="batchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0" id="batchModalLabel">
                    <i class="fas fa-layer-group me-2"></i>Ingreso Masivo de Preguntas
                  </h5>
                  <small class="opacity-75">Ingresa todas las preguntas del cuestionario de una sola vez</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Paso 1: configuración -->
              <div class="modal-body" id="batchStep1">
                <div class="row justify-content-center py-3">
                  <div class="col-md-7">

                    <div class="text-center mb-4">
                      <div style="width:60px;height:60px;border-radius:18px;background:linear-gradient(135deg,#20427F,#132b52);display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                        <i class="fas fa-list-ol text-white" style="font-size:1.6rem;"></i>
                      </div>
                      <h5 style="font-weight:900;color:#0f172a;">Configura tu cuestionario</h5>
                      <p class="text-muted small">Cada enunciado agrupa N preguntas que comparten el mismo contexto y escala de respuesta.</p>
                    </div>

                    <!-- Ficha técnica -->
                    <div class="form-floating mb-3">
                      <select class="form-select" id="batchEncuesta">
                        <?php echo $optionEncuentas; ?>
                      </select>
                      <label>Ficha Técnica de la Encuesta <span class="text-danger">*</span></label>
                    </div>

                    <!-- Tipo por defecto -->
                    <div class="form-floating mb-3">
                      <select class="form-select" id="batchTipoDefault">
                        <option value="">Sin tipo por defecto</option>
                        <option value="Dicotomica">Dicotómica</option>
                        <option value="Preguntas_Ordinales" selected>Preguntas Ordinales</option>
                        <option value="Preguntas_Cardinales">Preguntas Cardinales</option>
                        <option value="Seleccion_Multiple_unica_respuesta">Selección múltiple única respuesta</option>
                        <option value="Seleccion_Multiple_multiple_respuesta">Selección múltiple múltiple respuesta</option>
                      </select>
                      <label>Tipo de pregunta (aplica a todas)</label>
                    </div>

                    <!-- Opciones por defecto -->
                    <div class="mb-4">
                      <label class="form-label small fw-bold mb-1" style="color:#64748b;">
                        <i class="fas fa-list me-1"></i> Opciones de respuesta (aplica a todas) — separadas por coma
                      </label>
                      <input type="text" class="form-control" id="batchOpcionesDefault"
                             value="1, 2, 3, 4, 5"
                             placeholder="Ej: 1, 2, 3, 4, 5  ó  Sí, No"
                             style="border-radius:12px;border:1.5px solid rgba(32,66,127,.2);">
                      <div style="font-size:.76rem;color:#94a3b8;margin-top:4px;">
                        Puedes editarlas individualmente por enunciado después de generar.
                      </div>
                    </div>

                    <hr style="border-color:rgba(32,66,127,.1);">

                    <!-- Enunciados dinámicos -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <strong style="color:#0f172a;">Capítulos del cuestionario</strong>
                      <button type="button" class="btn btn-sm btn-outline-primary" onclick="PREGUNTAS.batchAddCapitulo()" style="border-radius:10px;">
                        <i class="fas fa-plus me-1"></i> Añadir capítulo
                      </button>
                    </div>
                    <div id="batchEnunciadosList"></div>

                    <div class="d-grid mt-4">
                      <button type="button" class="btn btn-primary btn-lg" onclick="PREGUNTAS.batchGenerate()">
                        <i class="fas fa-magic me-2"></i>Generar Formulario
                      </button>
                    </div>

                  </div>
                </div>
              </div>

              <!-- Paso 2: formulario generado -->
              <div class="modal-body" id="batchStep2" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                  <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="PREGUNTAS.batchGoBack()">
                      <i class="fas fa-arrow-left me-1"></i> Volver
                    </button>
                  </div>
                  <div class="text-muted small">
                    <span id="batchProgress">0 de 0 completas</span>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="PREGUNTAS.batchCollapseAll()">
                      <i class="fas fa-compress-alt me-1"></i>Colapsar todo
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="PREGUNTAS.batchExpandAll()">
                      <i class="fas fa-expand-alt me-1"></i>Expandir todo
                    </button>
                  </div>
                </div>
                <div id="batchQuestionsContainer"></div>
              </div>

              <div class="modal-footer" id="batchFooter" style="display:none;">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                  <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Solo se guardarán las preguntas con texto diligenciado.
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.batchSave()">
                      <i class="fas fa-save me-2"></i>Guardar Todas las Preguntas
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal para subir preguntas por excel -->
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Encabezado del modal -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Subir Preguntas por Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Cuerpo del modal -->
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <!-- Sección de carga de archivo -->
                                <div class="mb-3">
                                    <label class="floating-label form-label" for="foto1">Archivo Excel</label>
                                    <div class="controls">
                                        <div class="dropzone dropzone-multiple p-0 mb-3" id="dropzone-foto1"
                                            data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                                            <iframe id="ifm1" name="ifm1" src="upload_csv.php" width="100%" height="200"
                                                scrolling="no" frameborder="0" style="border: none;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pie del modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="PREGUNTAS.uploadFile()">Cargar Archivo</button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include './admin/include/footer.php';
        ?>
    </div>

    <?php include 'admin/include/gerenic_script.php'; ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 Bootstrap4 Theme CSS (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Otras dependencias y estilos (ya incluidos en el proyecto) -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/plugins/prism.js"></script>

    <!-- Archivos personalizados -->
    <script type="text/javascript" src="admin/js/opcion_preguntas.js"></script>
    <script type="text/javascript" src="admin/js/preguntas.js"></script>
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
  </script>


    <script>
        OPCIONES.handleTipoPreguntaChange();
    </script>

    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>