<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Configuracion.php';
include './admin/classes/Departamento.php';

// Permisos - SOLO ADMINISTRADOR puede acceder a Configuración
$view    = SessionData::getPermission(1);
$create  = SessionData::getPermission(2);
$edit    = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);

// Validar que tenga permiso de ver
if (!$view) {
  require 'permiso_denegado.php';
  exit;
}

// Validar que sea Administrador (solo ellos pueden gestionar configuración)
if (!SessionData::administrador()) {
  require 'permiso_denegado.php';
  exit;
}

// Información de Configuracion
$arr = Configuracion::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Configuración Aplicación';

// Información de Departamentos
$departamentos = Departamento::getAll(null);
$isValidDep = $departamentos['output']['valid'] ?? false;
$departamentosResponse = $departamentos['output']['response'] ?? [];
$optionDep = '<option value="" disabled selected>Seleccione...</option>';
foreach ($departamentosResponse as $dep) {
  $optionDep .= "<option value='" . $dep['codigo_departamento'] . "'>" . $dep['codigo_departamento'] . " - " . $dep['departamento'] . "</option>";
}

// Helper seguro
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<body class="">

  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <style>
    :root{
      --brand:#20427F;
      --brand2:#132b52;
      --brand3:#2e58a8;
      --ink:#0f172a;
      --muted:#64748b;
      --bg:#f6f8fb;
      --card:#fff;
      --line: rgba(15, 23, 42, .08);
      --radius:18px;
      --shadow: 0 14px 40px rgba(2, 6, 23, .08);
      --shadow2: 0 20px 60px rgba(2, 6, 23, .12);
    }

    body{ background: var(--bg); }

    .content{
      padding-top: 14px !important;
      padding-bottom: 180px !important;
    }

    /* Espacio para la sección de información activa */
    .info-header {
      margin-top: 30px;
    }

    #sondeo-info,
    #cuestionario-info {
      margin-bottom: 140px;
    }

    /* Asegurar que el contenedor de info activa tenga espacio */
    #active-info-container {
      padding-bottom: 60px;
    }

    /* contenedor más ancho en PC */
    @media (min-width: 1400px){
      .container-xxl-saas{ max-width: 1500px; }
    }

    .saas-hero{
      background: radial-gradient(1200px 600px at 15% 15%, rgba(46,88,168,.18), transparent 55%),
                  radial-gradient(900px 500px at 80% 30%, rgba(32,66,127,.16), transparent 55%),
                  linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.80));
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 18px;
      position: relative;
      overflow: hidden;
    }
    .saas-hero:before{
      content:"";
      position:absolute;
      inset:-2px;
      background: linear-gradient(135deg, rgba(32,66,127,.18), rgba(19,43,82,.06), rgba(46,88,168,.16));
      filter: blur(18px);
      opacity:.65;
      z-index:0;
    }
    .saas-hero > *{ position:relative; z-index:1; }

    .chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:7px 12px;
      border-radius: 999px;
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.14);
      color: var(--brand2);
      font-weight: 800;
      font-size: 12px;
    }

    .card-pro{
      border: 1px solid var(--line) !important;
      border-radius: var(--radius) !important;
      box-shadow: var(--shadow) !important;
      background: var(--card) !important;
      overflow: hidden;
    }
    .card-pro .card-body{ padding: 18px; }
    .card-pro .card-header{
      background: rgba(255,255,255,.88) !important;
      border-bottom: 1px solid var(--line) !important;
      padding: 16px 18px !important;
    }

    .section-title{
      font-weight: 900;
      color: var(--ink);
      margin: 0;
    }
    .section-sub{
      color: var(--muted);
      font-weight: 600;
      margin-top: 6px;
    }

    .form-floating>.form-control,
    .form-floating>.form-select{
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.12);
      box-shadow: none;
    }
    .form-control:focus,
    .form-select:focus{
      border-color: rgba(32,66,127,.45) !important;
      box-shadow: 0 0 0 .25rem rgba(32,66,127,.12) !important;
    }

    .help{
      color: var(--muted);
      font-size: 12px;
      font-weight: 600;
      margin-top: 6px;
    }

    .logo-card{
      border: 1px dashed rgba(32,66,127,.25);
      background: rgba(32,66,127,.04);
      border-radius: 16px;
      padding: 14px;
    }

    .logo-preview{
      width: 100%;
      max-width: 420px;
      min-height: 110px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius: 16px;
      border: 1px solid rgba(15,23,42,.10);
      background: #fff;
      box-shadow: 0 10px 26px rgba(2,6,23,.06);
      padding: 12px;
    }
    .logo-preview img{
      max-height: 120px;
      width: auto;
      max-width: 100%;
      object-fit: contain;
    }

    .iframe-uploader{
      width: 100%;
      height: 260px;
      border: 1px solid rgba(15,23,42,.10);
      border-radius: 16px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 10px 26px rgba(2,6,23,.06);
    }

    .btn-brand{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border: 0 !important;
      border-radius: 14px;
      font-weight: 900;
      padding: 12px 18px;
      box-shadow: 0 14px 30px rgba(32,66,127,.18);
      transition: transform .16s ease, box-shadow .16s ease;
    }
    .btn-brand:hover{
      transform: translateY(-2px);
      box-shadow: 0 18px 44px rgba(32,66,127,.25);
    }
    .btn-soft{
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.18) !important;
      color: var(--brand2);
      border-radius: 14px;
      font-weight: 900;
      padding: 12px 16px;
    }

    /* barra de acciones fija */
    .action-bar{
      position: sticky;
      bottom: 12px;
      z-index: 10;
      margin-top: 18px;
    }
    .action-bar .action-inner{
      border-radius: 18px;
      border: 1px solid var(--line);
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow);
      padding: 12px;
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }
  </style>

  <div class="content">
    <div class="container-fluid container-xxl-saas">

      <!-- HERO -->
      <div class="saas-hero mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div>
            <div class="chip mb-2">
              <i class="fas fa-cog"></i> Configuración de la aplicación
            </div>
            <h2 class="section-title mb-1">Configuración</h2>
            <div class="section-sub">Define el proyecto, ubicación principal y la opción activa del sitio web.</div>
          </div>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="chip"><i class="fas fa-user-shield"></i> Solo Administrador</span>
            <span class="chip"><i class="fas fa-sliders-h"></i> Ajustes Globales</span>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-xxl-11 mx-auto">

          <div class="card card-pro">
            <div class="card-header">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                  <i class="fas fa-cog" style="color: var(--brand); font-size: 1.2rem;"></i>
                  <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">Formulario de Configuración</h4>
                </div>
                <div class="text-muted" style="font-weight:700;font-size:12px;">
                  Completa la información y guarda los cambios.
                </div>
              </div>
            </div>

            <div class="card-body">
              <form action="">

                <input type="hidden" name="op" id="op" />
                <input type="hidden" name="idConfig" id="idConfig" />
                <input type="hidden" name="filtro" id="filtro" value="vereda" />
                <input type="hidden" name="filtroVeredaById" id="filtroVeredaById" value="si" />

                <div class="row g-3">

                  <!-- Nombre proyecto -->
                  <div class="col-12 col-md-6 col-xl-4">
                    <div class="form-floating">
                      <input class="form-control" type="text" id="nombre_proyecto" name="nombre_proyecto"
                             placeholder="Ingrese nombre proyecto" required />
                      <label for="nombre_proyecto">Nombre del proyecto <span class="text-danger">*</span></label>
                    </div>
                    <div class="help">Este nombre se mostrará en encabezados, reportes y panel principal.</div>
                  </div>

                  <!-- Departamento -->
                  <div class="col-12 col-md-6 col-xl-4">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="departamentoId" name="departamentoId"
                              onchange="CONFIGURACION.getMunicipios();">
                        <?php echo $optionDep; ?>
                      </select>
                      <label for="departamentoId">Departamento principal <span class="text-danger">*</span></label>
                    </div>
                    <div class="help">Define el departamento por defecto del sistema.</div>
                  </div>

                  <!-- Municipio -->
                  <div class="col-12 col-md-6 col-xl-4">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"
                              onchange="CONFIGURACION.getVeredasByMunicipioId();">
                      </select>
                      <label for="tbl_municipio_id">Municipio principal <span class="text-danger">*</span></label>
                    </div>
                    <div class="help">Se carga automáticamente según el departamento seleccionado.</div>
                  </div>

                  <!-- Vereda -->
                  <div class="col-12 col-md-6 col-xl-4">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_vereda_id" name="tbl_vereda_id"></select>
                      <label for="tbl_vereda_id">Vereda principal <span class="text-danger">*</span></label>
                    </div>
                    <div class="help">Se carga automáticamente según el municipio seleccionado.</div>
                  </div>

                  <!-- Opción activa web -->
                  <div class="col-12 col-md-6 col-xl-3">
                    <div class="form-floating">
                      <select class="form-select" id="opcion_activa_web" name="opcion_activa_web">
                        <option value="sondeo">Sondeo</option>
                        <!-- <option value="estudio">Estudio</option> -->
                        <option value="cuestionario">Cuestionario</option>
                        <option value="ambos">Ambos (Sondeo y Cuestionario)</option>
                      </select>
                      <label for="opcion_activa_web">Opción activa web</label>
                    </div>
                    <div class="help">Define qué módulo aparece activo en el portal público.</div>
                  </div>

                  <!-- Comentarios -->
                  <div class="col-12">
                    <label class="form-label" for="comentarios" style="font-weight:900;color:var(--ink);">Comentarios</label>
                    <textarea class="form-control" id="comentarios" name="comentarios" placeholder="Comentario"
                              rows="3" style="border-radius:14px;border:1px solid rgba(15,23,42,.12);"></textarea>
                    <div class="help">Notas internas sobre el uso o cambios de configuración.</div>
                  </div>

                  <!-- LOGOS -->
                  <div class="col-12">
                    <div class="row g-3">

                      <!-- Logo actual -->
                      <div class="col-12 col-lg-6">
                        <div class="logo-card h-100">
                          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                              <span class="chip"><i class="fas fa-image"></i> Logo actual</span>
                            </div>
                            <div class="text-muted" style="font-size:12px;font-weight:700;">
                              Previsualización
                            </div>
                          </div>

                          <div id="divLogoActual" style="display:block;">
                            <div class="logo-preview">
                              <img id="logoPreview" src="" alt="Logo" />
                            </div>
                          </div>

                          <div class="help mt-2">Este es el logo actual cargado en el sistema.</div>
                        </div>
                      </div>

                      <!-- Uploader (iframe) -->
                      <div class="col-12 col-lg-6">
                        <div class="logo-card h-100">
                          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <span class="chip"><i class="fas fa-upload"></i> Subir logo</span>
                            <div class="text-muted" style="font-size:12px;font-weight:700;">
                              Recomendado: PNG transparente
                            </div>
                          </div>

                          <div class="iframe-uploader">
                            <iframe id="ifm1" name="ifm1" src="upload.php" scrolling="no" frameborder="0"
                                    style="width:100%; height:100%;"></iframe>
                          </div>

                          <div class="help mt-2">Carga un logo nuevo. Luego guarda la configuración.</div>
                        </div>
                      </div>

                    </div>
                  </div>

                </div>

                <!-- Barra acciones -->
                <div class="action-bar">
                  <div class="action-inner">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="chip"><i class="fas fa-circle-check"></i> Cambios listos para guardar</span>
                      <span class="text-muted" style="font-size:12px;font-weight:700;">
                        Tip: guarda después de cargar un logo.
                      </span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                      <button type="button" class="btn btn-soft" onclick="location.reload();">
                        <i class="fas fa-rotate me-1"></i> Recargar
                      </button>

                      <?php if ($create && $edit) { ?>
                        <button type="button" onclick="CONFIGURACION.savedata();" class="btn btn-brand px-4">
                          <i class="fas fa-floppy-disk me-2"></i> Guardar cambios
                        </button>
                      <?php } ?>
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div id="active-info-container" class="mt-4">

<style>
    .info-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .info-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--brand), var(--brand2));
        color: #fff;
        font-size: 1.2rem;
        box-shadow: 0 10px 20px rgba(32, 66, 127, .2);
    }
    .info-header h4 {
        font-weight: 900;
        color: var(--ink);
        margin: 0;
    }
    .info-header .sub {
        font-weight: 600;
        color: var(--muted);
        font-size: 0.9rem;
    }

    .custom-accordion .accordion-item {
        border: 1px solid var(--line) !important;
        border-radius: 16px !important;
        margin-bottom: 12px;
        box-shadow: 0 8px 25px rgba(2, 6, 23, .05);
        overflow: hidden;
    }
    .custom-accordion .accordion-button {
        font-weight: 800;
        color: var(--ink);
        background: #fff;
    }
    .custom-accordion .accordion-button:not(.collapsed) {
        background: radial-gradient(circle at 10% 20%, rgba(46, 88, 168, .08), transparent 80%), #fff;
        color: var(--brand);
    }
    .custom-accordion .accordion-button:focus {
        box-shadow: none;
    }
    .custom-accordion .accordion-body {
        padding: 1rem 1.25rem;
        background: rgba(248, 250, 252, .6);
    }
    .custom-accordion .list-group-item {
        background: transparent;
        border-color: rgba(15, 23, 42, .07);
    }
</style>

<?php
// Incluir clases necesarias
require_once './admin/classes/Sondeo.php';
require_once './admin/classes/FichaTecnicaEncuesta.php';
require_once './admin/classes/Pregunta.php';

// Obtener sondeo activo
$sondeos = Sondeo::getAll(null)['output']['response'] ?? [];
$active_sondeo = null;
foreach ($sondeos as $sondeo) {
    if (($sondeo['habilitado'] ?? 'no') === 'si' && ($sondeo['vigente'] ?? false)) {
        $active_sondeo = $sondeo;
        break;
    }
}

// Obtener cuestionario activo
$fichas = FichaTecnicaEncuesta::getAll(null)['output']['response'] ?? [];
$active_ficha = null;
foreach ($fichas as $ficha) {
    if (($ficha['habilitado'] ?? 'no') === 'si') {
        $active_ficha = $ficha;
        break;
    }
}

$preguntas_cuestionario = [];
if ($active_ficha) {
    $preguntas_cuestionario = Pregunta::getAll(['tbl_ficha_tecnica_encuesta_id' => $active_ficha['id']])['output']['response'] ?? [];
}
?>

<div class="info-header">
    <div class="info-icon"><i class="fas fa-info-circle"></i></div>
    <div>
        <h4>Información Activa</h4>
        <div class="sub">Aquí se muestra el sondeo o cuestionario activo actualmente en el sitio público.</div>
    </div>
</div>

<!-- Contenedor para la información del Sondeo -->
<div id="sondeo-info" style="display: none;">
    <div class="card card-pro">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-poll-h" style="color: var(--brand); font-size: 1.2rem;"></i>
            <h5 class="mb-0" style="font-weight: 900; color: var(--ink);">Sondeo Activo</h5>
        </div>
        <div class="card-body">
            <?php if ($active_sondeo): ?>
                <h4 class="mb-1" style="font-weight:800; color: var(--ink);"><?= h($active_sondeo['sondeo']) ?></h4>
                <p class="text-muted mb-3"><?= h($active_sondeo['descripcion_sondeo']) ?></p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center gap-3"><i class="fas fa-list-check" style="color:var(--brand); width: 20px;"></i> <div><strong>Tipo:</strong> <?= h($active_sondeo['tipo_sondeo']) ?></div></li>
                    <li class="list-group-item d-flex align-items-center gap-3"><i class="fas fa-calendar-alt" style="color:var(--brand); width: 20px;"></i> <div><strong>Vigencia:</strong> Desde <?= h($active_sondeo['fecha_inicio']) ?> hasta <?= h($active_sondeo['fecha_fin']) ?></div></li>
                </ul>
            <?php else: ?>
                <div class="alert alert-light d-flex align-items-center gap-3" style="border: 1px solid var(--line); border-radius: 16px;">
                    <i class="fas fa-exclamation-triangle fs-3" style="color: var(--muted);"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">No hay un sondeo activo</h6>
                        <span class="text-muted small">Active un sondeo para verlo aquí.</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Contenedor para la información del Cuestionario -->
<div id="cuestionario-info" style="display: none;">
    <div class="card card-pro">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-file-alt" style="color: var(--brand); font-size: 1.2rem;"></i>
            <h5 class="mb-0" style="font-weight: 900; color: var(--ink);">Cuestionario Activo</h5>
        </div>
        <div class="card-body">
            <?php if ($active_ficha): ?>
                <h4 class="mb-1" style="font-weight:800; color: var(--ink);">Ficha: <?= h($active_ficha['texto_literal_de_la_encuesta_o_preguntas']) ?></h4>
                <p class="text-muted mb-3">Realizada por: <?= h($active_ficha['realizada_por_o_encomendada_por']) ?></p>
                
                <h5 class="mt-4 mb-3" style="font-weight: 800;">Preguntas del Cuestionario</h5>
                <?php if (!empty($preguntas_cuestionario)): ?>
                    <div class="accordion custom-accordion" id="accordionCuestionario">
                        <?php foreach ($preguntas_cuestionario as $index => $pregunta): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?= $index ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $index ?>" aria-expanded="false" aria-controls="collapse-<?= $index ?>">
                                        <i class="fas fa-question-circle me-2"></i>
                                        <span><strong><?= h($pregunta['orden']) ?>.</strong> <?= h($pregunta['texto_pregunta']) ?></span>
                                    </button>
                                </h2>
                                <div id="collapse-<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $index ?>" data-bs-parent="#accordionCuestionario">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <?php if (!empty($pregunta['opciones'])): ?>
                                                <?php foreach ($pregunta['opciones'] as $opcion): ?>
                                                    <li class="list-group-item d-flex align-items-center gap-2">
                                                        <i class="fas fa-caret-right" style="color: var(--brand3);"></i>
                                                        <?= h($opcion['texto']) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item text-muted">No hay opciones para esta pregunta.</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                     <div class="alert alert-light d-flex align-items-center gap-3" style="border: 1px solid var(--line); border-radius: 16px;">
                        <i class="fas fa-info-circle fs-3" style="color: var(--muted);"></i>
                        <div>
                            <h6 class="mb-0 fw-bold">Ficha técnica sin preguntas</h6>
                            <span class="text-muted small">Esta ficha no tiene preguntas asociadas.</span>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                 <div class="alert alert-light d-flex align-items-center gap-3" style="border: 1px solid var(--line); border-radius: 16px;">
                    <i class="fas fa-exclamation-triangle fs-3" style="color: var(--muted);"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">No hay un cuestionario activo</h6>
                        <span class="text-muted small">Habilite una ficha técnica para verla aquí.</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('opcion_activa_web');
    const sondeoInfo = document.getElementById('sondeo-info');
    const cuestionarioInfo = document.getElementById('cuestionario-info');

    function toggleInfo(selectedValue) {
        sondeoInfo.style.display = 'none';
        cuestionarioInfo.style.display = 'none';

        if (selectedValue === 'sondeo') {
            sondeoInfo.style.display = 'block';
        } else if (selectedValue === 'cuestionario') {
            cuestionarioInfo.style.display = 'block';
        } else if (selectedValue === 'ambos') {
            sondeoInfo.style.display = 'block';
            cuestionarioInfo.style.display = 'block';
        }
    }

    // Set initial state from form data if available, otherwise default
    // We check the 'opcion_activa_web' value loaded into the form initially
    setTimeout(function() {
        toggleInfo(selector.value);
    }, 1600); // Small delay to ensure CONFIGURACION.editdata() has run

    // Add change event listener
    selector.addEventListener('change', function() {
        toggleInfo(this.value);
    });
});
</script>

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

  <script type="text/javascript" src="admin/js/configuracion.js"></script>

  <script>
    // ✅ Conserva tu comportamiento, solo lo hacemos un toque más robusto:
    // Si el DOM está listo, intenta cargar configuración.
    (function(){
      var run = function(){
        try{
          if (typeof CONFIGURACION !== "undefined" && typeof CONFIGURACION.editdata === "function") {
            CONFIGURACION.editdata();
          }
        }catch(e){}
      };
      // deja el delay que ya tenías (por si tu ajax depende de cargas previas)
      setTimeout(run, 1500);
    })();
  </script>

  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
