<?php
/**
 * Vista territorial embebible para el dashboard admin (mapa + charts).
 * Params: modo=sondeo|cuestionario, id=ítem seleccionado
 */
require_once 'admin/include/generic_classes.php';

$viewSondeo = SessionData::getPermission(90);
$viewCuestionario = SessionData::getPermission(74);
if (!$viewSondeo && !$viewCuestionario) {
  http_response_code(403);
  echo 'Sin permiso';
  exit;
}

$modo = isset($_GET['modo']) ? strtolower(trim((string)$_GET['modo'])) : 'sondeo';
if ($modo !== 'sondeo' && $modo !== 'cuestionario') {
  $modo = 'sondeo';
}
$itemId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$opcionActivaWeb = ($modo === 'sondeo') ? 'sondeo' : 'cuestionario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vista territorial</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <link href="../css/resultado.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body{ margin:0; background:#f5f7fb; }
    .vt-wrap{ padding:12px; }
    .vt-note{ font-size:.82rem; color:#64748b; margin-bottom:10px; }
    .vt-map-block{
      width:72%;
      max-width:720px;
      margin:0 auto 12px;
      background:#fff;
      border-radius:16px;
      border:1px solid rgba(2,6,23,.08);
      padding:8px 10px 4px;
    }
    #mapaContainer{
      display:block !important;
      width:100% !important;
      min-height:0 !important;
      height:auto !important;
      margin:0 !important;
      padding:0 !important;
      overflow:visible !important;
      background:transparent !important;
      border:0 !important;
      border-radius:0 !important;
    }
    #mapaContainer svg{
      display:block !important;
      width:100% !important;
      max-width:100% !important;
      height:auto !important;
      max-height:none !important;
      margin:0 auto !important;
      overflow:visible !important;
    }

    .vt-analytics{
      background:#fff;
      border-radius:16px;
      border:1px solid rgba(2,6,23,.08);
      padding:16px;
    }
    .vt-analytics .vt-section-title{
      font-size:.95rem;
      font-weight:800;
      color:#0f172a;
      margin:0 0 6px;
    }
    .vt-analytics .vt-section-sub{
      font-size:.78rem;
      color:#64748b;
      margin:0 0 12px;
    }
    .vt-divider{
      border:0;
      border-top:1px solid rgba(15,23,42,.08);
      margin:18px 0;
    }
    .vt-chart-panel{
      border:1px solid rgba(15,23,42,.08);
      border-radius:14px;
      background:#fbfcfe;
      padding:14px;
    }
    .vt-chart-box{
      position:relative;
      width:100%;
      min-height:280px;
      height:320px;
    }
    .vt-territory-badge{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:6px 12px;
      border-radius:999px;
      background:rgba(32,66,127,.08);
      color:#20427F;
      font-weight:800;
      font-size:.78rem;
      border:1px solid rgba(32,66,127,.16);
    }
    .vt-empty-chart{
      min-height:220px;
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
      color:#64748b;
      font-size:.9rem;
      background:rgba(2,6,23,.02);
      border-radius:12px;
      border:1px dashed rgba(15,23,42,.12);
      padding:18px;
    }
    #resultadosCard{ z-index:20; }
    @media (max-width:768px){
      .vt-wrap{ padding:8px; }
      .vt-map-block{ width:100%; max-width:100%; padding:6px; }
      #mapaContainer{ min-height:0 !important; }
      #mapaContainer svg{ width:100% !important; max-height:none !important; }
      .vt-chart-box{ height:280px; min-height:240px; }
    }
  </style>
</head>
<body>
<svg style="position:absolute;width:0;height:0;overflow:hidden;" aria-hidden="true">
  <defs>
    <pattern id="rayasAzules" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
      <rect width="8" height="8" fill="#cfe2ff"/>
      <rect width="4" height="8" fill="#0d6efd"/>
    </pattern>
  </defs>
</svg>

<div class="vt-wrap" id="panelResultados">
  <div class="vt-map-block">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
      <h3 class="h6 mb-0" id="tituloMapaNivel">Mapa territorial de Colombia</h3>
      <nav id="breadcrumbTerritorio" class="small mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item active" id="bcPais">Colombia</li>
          <li class="breadcrumb-item d-none" id="bcDepto"></li>
          <li class="breadcrumb-item d-none" id="bcMuni"></li>
        </ol>
      </nav>
      <button type="button" class="btn btn-sm btn-outline-primary d-none" id="btnVolverColombia">
        <i class="fas fa-arrow-left me-1"></i>Volver a Colombia
      </button>
    </div>
    <div id="mapaMunicipalMsg" class="alert alert-info py-2 px-3 d-none small mb-2" role="status"></div>
    <div id="mapaContainer">
      <?php
        $_GET['modo_mapa'] = $modo;
        require_once __DIR__ . '/../admin/mapa_colombia/mapa_index.php';
      ?>
    </div>
  </div>

  <!-- Un solo panel: pregunta 100% + 2 gráficas lado a lado -->
  <div class="vt-analytics" id="panelAnalyticsUnificado">
    <?php if ($modo === 'cuestionario'): ?>
      <div id="panelSelectorPregunta" class="mb-3">
        <h3 class="vt-section-title"><i class="fas fa-circle-question me-2 text-primary"></i>Pregunta</h3>
        <p class="vt-section-sub mb-2" id="fichaTecnicaNombre">Cargando...</p>
        <div id="infoPreguntaCtx" class="mb-2 small"></div>
        <label for="selectorPregunta" class="form-label small fw-bold">Selecciona una pregunta</label>
        <select id="selectorPregunta" class="form-select form-select-sm"></select>
      </div>
      <hr class="vt-divider">
    <?php endif; ?>

    <div class="row g-3 vt-charts-row">
      <div class="col-12 col-lg-6">
        <div class="vt-chart-panel h-100">
          <h3 class="vt-section-title" id="tituloResumenNivel"><i class="fas fa-chart-column me-2 text-primary"></i>Resumen nacional</h3>
          <p class="vt-section-sub" id="subResumenNivel">Distribución de respuestas a nivel país<?= $modo === 'cuestionario' ? ' para la pregunta seleccionada' : '' ?>.</p>
          <div class="vt-chart-box" id="chartWrapGeneral">
            <canvas id="graficoGeneral"></canvas>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="vt-chart-panel h-100">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div>
              <h3 class="vt-section-title mb-1"><i class="fas fa-map-location-dot me-2 text-primary"></i>Detalle territorial</h3>
              <p class="vt-section-sub mb-0" id="subDetalleTerritorio">Respuestas por departamento (color = opción líder).</p>
            </div>
            <span class="vt-territory-badge" id="badgeTerritorioActivo">
              <i class="fas fa-location-dot"></i>
              <span id="tituloDetalleTerritorio">Todos los departamentos</span>
            </span>
          </div>
          <div id="detalleTerritorioEmpty" class="vt-empty-chart">
            Cargando información territorial…
          </div>
          <div class="vt-chart-box" id="chartWrapTerritorio" style="display:none;">
            <canvas id="graficoVotos"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="resultadosCard" class="card shadow" style="display:none;position:fixed;right:16px;bottom:16px;max-width:320px;">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
      <span id="badgeElectoral" class="badge bg-primary">Detalle</span>
      <button type="button" class="btn btn-sm btn-link" id="closeCard">Cerrar</button>
    </div>
    <div class="card-body py-2" id="resultadosContent"></div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  window.OPCION_ACTIVA_WEB = <?= json_encode($opcionActivaWeb) ?>;
  window.DASH_TERRITORIO_MODO = <?= json_encode($modo) ?>;
  window.DASH_TERRITORIO_ID = <?= (int)$itemId ?>;
  window.MAPA_MUNICIPAL_DEPTOS = <?= json_encode(Util::getMapaMunicipalDeptosHabilitados()) ?>;
  (function(){
    var originalAjax = $.ajax;
    $.ajax = function(options) {
      if (options && typeof options.url === 'string' && options.url.indexOf('admin/ajax/rqst.php') === 0) {
        options.url = '../' + options.url;
      }
      return originalAjax.apply(this, arguments);
    };
  })();
</script>
<script src="../admin/js/lib/util.js"></script>
<script src="../admin/js/index.js"></script>
<script>
  document.getElementById('closeCard')?.addEventListener('click', function(){
    document.getElementById('resultadosCard').style.display = 'none';
  });
</script>
</body>
</html>
