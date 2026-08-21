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
      width:80%;
      max-width:100%;
      margin:0 auto 16px;
      background:#fff;
      border-radius:16px;
      border:1px solid rgba(2,6,23,.08);
      padding:12px;
    }
    #mapaContainer svg{ max-width:100%; height:auto; display:block; margin:0 auto; }
    .vt-charts .block{
      background:#fff;
      border-radius:16px;
      border:1px solid rgba(2,6,23,.08);
      padding:12px;
      height:100%;
    }
    #resultadosCard{ z-index:20; }
    @media (max-width:768px){
      .vt-wrap{ padding:8px; }
      .vt-map-block{ width:100%; }
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
  <p class="vt-note mb-2">
    Vista territorial del <?= $modo === 'sondeo' ? 'sondeo' : 'cuestionario' ?>
    seleccionado<?= $itemId > 0 ? ' (ID ' . (int)$itemId . ')' : '' ?>.
    Haz clic en un departamento del mapa.
  </p>

  <!-- Mapa solo (80% ancho) -->
  <div class="vt-map-block">
    <h3 class="h6 text-center mb-2">Mapa territorial de Colombia</h3>
    <div id="mapaContainer">
      <?php require_once __DIR__ . '/../admin/mapa_colombia/mapa_index.php'; ?>
    </div>
  </div>

  <!-- Recuadros debajo del mapa -->
  <div class="row g-3 vt-charts">
    <?php if ($modo === 'cuestionario'): ?>
      <div class="col-12 col-lg-4">
        <div class="block" id="panelSelectorPregunta">
          <h3 class="h6 text-center">Pregunta</h3>
          <p class="small text-muted text-center mb-2" id="fichaTecnicaNombre">Cargando...</p>
          <div id="infoPreguntaCtx" class="mb-2 small"></div>
          <label for="selectorPregunta" class="form-label small">Selecciona una pregunta</label>
          <select id="selectorPregunta" class="form-select form-select-sm"></select>
        </div>
      </div>
    <?php endif; ?>

    <div class="col-12 <?= $modo === 'cuestionario' ? 'col-lg-4' : 'col-lg-6' ?>">
      <div class="block">
        <h3 class="h6 text-center">Resumen nacional</h3>
        <div id="chartWrapGeneral"><canvas id="graficoGeneral" height="180"></canvas></div>
      </div>
    </div>
    <div class="col-12 <?= $modo === 'cuestionario' ? 'col-lg-4' : 'col-lg-6' ?>">
      <div class="block">
        <h3 class="h6 text-center">Detalle territorial</h3>
        <canvas id="graficoVotos" height="180"></canvas>
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
