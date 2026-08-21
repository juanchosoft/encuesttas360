<?php
/**
 * Fase B — Index público (sustituye landing + dash_responder sin sesión).
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require './admin/include/generic_classes.php';
require_once './admin/classes/Sondeo.php';
require_once './admin/classes/FichaTecnicaEncuesta.php';
require_once './admin/classes/RespuestaCuestionario.php';
require_once './admin/classes/ParticipacionPublica.php';

ParticipacionPublica::ensureSchema();

$config       = Util::getInformacionConfiguracion();
$opcionActiva = $config[0]['opcion_activa_web'] ?? 'sondeo';
$logo360      = 'assets/img/360 Estadisticas-04.png';

$geo          = ParticipacionPublica::getGeoFromSession();
$geoOk        = !empty($geo['ok']);
$codigoDepto  = $geo['codigo_departamento'] ?? '';
$codigoMunicipio = $geo['codigo_municipio'] ?? '';

// Sondeos: sin filtro geo (D7)
$sondeosDisponibles = [];
$sondeosContestados = []; // se clasifica en cliente con device; aquí todos "disponibles" server-side

if ($opcionActiva === 'sondeo' || $opcionActiva === 'ambos') {
  $listaSondeos = ParticipacionPublica::listSondeosPublicos();
  foreach ($listaSondeos as $s) {
    $s['contestado'] = false;
    $sondeosDisponibles[] = $s;
  }
}

// Encuestas: solo si hay geo (D8)
$encuestasPendientes  = [];
$encuestasCompletadas = [];
$geoBloqueaEncuestas  = ($opcionActiva === 'cuestionario' || $opcionActiva === 'ambos') && !$geoOk;

if (($opcionActiva === 'cuestionario' || $opcionActiva === 'ambos') && $geoOk) {
  $fichasResult = FichaTecnicaEncuesta::getAll(['solo_habilitadas' => true]);
  $todasFichas  = $fichasResult['output']['response'] ?? [];

  foreach ($todasFichas as $ficha) {
    if (!ParticipacionPublica::fichaVisible((int)$ficha['id'], $codigoDepto, $codigoMunicipio)) {
      continue;
    }
    $ficha['_visible_geo'] = true;
    $encuestasPendientes[] = $ficha;
  }
}

$totalPendientes  = count($sondeosDisponibles) + count($encuestasPendientes);
$totalCompletados = 0;
$hayAlgo   = ($totalPendientes + $totalCompletados) > 0 || $geoBloqueaEncuestas || !empty($sondeosDisponibles);
$todoListo = false;

$geoLabel = '';
if ($geoOk) {
  $geoLabel = trim(($geo['municipio'] ?? '') . (($geo['departamento'] ?? '') !== '' ? ', ' . $geo['departamento'] : ''));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Participa | 360 Estadísticas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900;950&display=swap" rel="stylesheet">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="./css/responder.css?v=<?= time(); ?>">
  <style>
    .geo-banner{
      border-radius:16px;padding:14px 16px;margin-bottom:18px;
      border:1px solid rgba(2,6,23,.08);background:#fff;
      display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;
    }
    .geo-banner.warn{background:#fff8eb;border-color:#f6d98b;}
    .geo-banner.ok{background:#eefaf3;border-color:#b7e4c7;}
    .geo-banner .geo-actions{margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;}
    .item-card[data-done="1"]{opacity:.72;}
    .item-card[data-done="1"] .btn-start{pointer-events:none;opacity:.5;}
  </style>
</head>
<body>

<div class="page-wrap">
  <div class="shell">

    <div class="hero">
      <div class="hero-content">
        <div class="hero-logo">
          <img src="<?= htmlspecialchars($logo360) ?>" alt="360 Estadísticas">
        </div>
        <div>
          <span class="hero-kicker">
            <i class="fa-solid fa-list-check"></i>
            Participación ciudadana
          </span>
          <h1 class="hero-title">Formularios activos</h1>
          <p class="hero-subtitle">
            Responde sondeos y encuestas disponibles. No necesitas crear cuenta.
          </p>
        </div>
        <div class="hero-stats">
          <div class="hero-stat">
            <strong id="statPendientes"><?= (int)$totalPendientes ?></strong>
            <span>Pendientes</span>
          </div>
          <div class="hero-stat">
            <strong id="statCompletados">0</strong>
            <span>Completados</span>
          </div>
        </div>
      </div>
    </div>

    <div id="geoBanner" class="geo-banner <?= $geoOk ? 'ok' : 'warn' ?>">
      <div>
        <strong id="geoTitle">
          <?php if ($geoOk): ?>
            <i class="fas fa-location-dot me-1"></i>Ubicación detectada
          <?php else: ?>
            <i class="fas fa-location-crosshairs me-1"></i>Ubicación requerida para encuestas
          <?php endif; ?>
        </strong>
        <div class="text-muted small mt-1" id="geoMsg">
          <?php if ($geoOk): ?>
            <?= htmlspecialchars($geoLabel !== '' ? $geoLabel : 'Territorio resuelto') ?>.
            Las encuestas se filtran según tu municipio/departamento.
          <?php else: ?>
            Para ver las encuestas de tu territorio debemos conocer tu ubicación.
            Los sondeos sí están disponibles sin GPS.
          <?php endif; ?>
        </div>
      </div>
      <div class="geo-actions">
        <button type="button" class="btn btn-sm btn-primary" id="btnGeo">
          <i class="fas fa-location-arrow me-1"></i><?= $geoOk ? 'Actualizar ubicación' : 'Permitir ubicación' ?>
        </button>
      </div>
    </div>

    <?php if ($geoBloqueaEncuestas): ?>
      <div class="panel mb-3" id="panelGeoHintEncuestas">
        <div class="empty py-4">
          <i class="fas fa-map-location-dot"></i>
          <h4 style="font-weight:950;color:#061326;">Encuestas ocultas</h4>
          <p class="mb-0">Habilita la ubicación para ver las encuestas activas de tu ciudad o departamento.</p>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!$hayAlgo && !$geoBloqueaEncuestas): ?>
      <div class="panel">
        <div class="empty">
          <i class="fas fa-inbox"></i>
          <h4 style="font-weight:950;color:#061326;">No hay formularios disponibles</h4>
          <p class="mb-0">No encontramos formularios activos en este momento.</p>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($sondeosDisponibles)): ?>
      <div class="panel" id="panelSondeos">
        <div class="panel-header">
          <div>
            <h5 class="panel-title"><i class="fas fa-poll me-2"></i>Sondeos</h5>
            <p class="panel-sub">Disponibles sin restricción de ubicación.</p>
          </div>
        </div>
        <div class="panel-body">
          <?php foreach ($sondeosDisponibles as $s): ?>
            <div class="item-card js-item" data-tipo="sondeo" data-id="<?= (int)$s['id'] ?>">
              <div class="item-ico sondeo-ico"><i class="fas fa-poll"></i></div>
              <div class="item-meta">
                <div class="item-name"><?= htmlspecialchars($s['sondeo'] ?? 'Sondeo') ?></div>
                <div class="item-by"><?= htmlspecialchars($s['descripcion_sondeo'] ?? 'Selecciona tu opción y confirma tu participación.') ?></div>
                <div class="mt-2">
                  <span class="tag-pend js-tag"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                </div>
              </div>
              <a href="sondeo_new.php" class="btn btn-start js-start">
                <i class="fas fa-play"></i> Comenzar
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($encuestasPendientes)): ?>
      <div class="panel" id="panelEncuestas">
        <div class="panel-header">
          <div>
            <h5 class="panel-title"><i class="fas fa-clipboard-list me-2"></i>Encuestas</h5>
            <p class="panel-sub">Filtradas por tu ubicación.</p>
          </div>
        </div>
        <div class="panel-body">
          <?php foreach ($encuestasPendientes as $e): ?>
            <div class="item-card js-item" data-tipo="encuesta" data-id="<?= (int)$e['id'] ?>">
              <div class="item-ico encuesta-ico"><i class="fas fa-file-alt"></i></div>
              <div class="item-meta">
                <div class="item-name"><?= htmlspecialchars($e['texto_literal_de_la_encuesta_o_preguntas'] ?? $e['tema'] ?? 'Encuesta') ?></div>
                <div class="item-by">Realizada por: <?= htmlspecialchars($e['realizada_por_o_encomendada_por'] ?? '360 Estadísticas') ?></div>
                <div class="mt-2">
                  <span class="tag-pend js-tag"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                </div>
              </div>
              <a href="encuesta.php?f=<?= (int)$e['id'] ?>" class="btn btn-start js-start">
                <i class="fas fa-play"></i> Comenzar
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php include './admin/include/footer.php'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="admin/js/device_participacion.js"></script>
<script src="admin/js/geo_participacion.js"></script>
<script>
(function(){
  var geoOk = <?= $geoOk ? 'true' : 'false' ?>;

  function markDoneItems(status){
    var doneS = (status && status.done_sondeo) || [];
    var doneE = (status && status.done_encuesta) || [];
    var completed = 0;
    document.querySelectorAll('.js-item').forEach(function(el){
      var tipo = el.getAttribute('data-tipo');
      var id = parseInt(el.getAttribute('data-id'), 10);
      var done = (tipo === 'sondeo') ? doneS.indexOf(id) >= 0 : doneE.indexOf(id) >= 0;
      if (done) {
        completed++;
        el.setAttribute('data-done', '1');
        var tag = el.querySelector('.js-tag');
        if (tag) tag.innerHTML = '<i class="fas fa-check-circle"></i> Completado';
        var btn = el.querySelector('.js-start');
        if (btn) {
          btn.innerHTML = '<i class="fas fa-check"></i> Ya participaste';
          btn.classList.remove('btn-start');
          btn.classList.add('btn-view');
          btn.removeAttribute('href');
        }
      }
    });
    var pend = document.querySelectorAll('.js-item:not([data-done="1"])').length;
    var sp = document.getElementById('statPendientes');
    var sc = document.getElementById('statCompletados');
    if (sp) sp.textContent = String(pend);
    if (sc) sc.textContent = String(completed);
  }

  function refreshDeviceStatus(){
    if (!window.DeviceParticipacion) return;
    var m = DeviceParticipacion.getMeta();
    var body = new URLSearchParams();
    body.set('op', 'participaciondevicestatus');
    body.set('device_uuid', m.device_uuid);
    fetch('admin/ajax/rqst.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: body.toString(),
      credentials: 'same-origin'
    }).then(function(r){ return r.json(); }).then(function(data){
      if (data && data.output && data.output.valid) markDoneItems(data.output);
    }).catch(function(){});
  }

  function runGeo(showToast){
    if (!window.GeoParticipacion) return;
    var btn = document.getElementById('btnGeo');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Obteniendo…'; }
    GeoParticipacion.obtenerUbicacion().then(function(res){
      if (showToast && window.Swal) {
        Swal.fire({ icon:'success', title:'Ubicación lista', text:(res.municipio||'') + (res.departamento ? ', ' + res.departamento : ''), timer:1800, showConfirmButton:false });
      }
      window.location.reload();
    }).catch(function(err){
      if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-location-arrow me-1"></i>Permitir ubicación'; }
      var title = document.getElementById('geoTitle');
      var msg = document.getElementById('geoMsg');
      var banner = document.getElementById('geoBanner');
      if (banner) { banner.classList.add('warn'); banner.classList.remove('ok'); }
      if (title) title.innerHTML = '<i class="fas fa-location-crosshairs me-1"></i>Ubicación no disponible';
      if (msg) msg.textContent = (err && err.message ? err.message + ' ' : '') + 'Los sondeos siguen disponibles. Para ver encuestas, habilita la ubicación en tu navegador.';
      if (showToast && window.Swal) {
        Swal.fire({ icon:'warning', title:'Sin ubicación', text: err && err.message ? err.message : 'No se pudo obtener GPS' });
      }
    });
  }

  document.getElementById('btnGeo')?.addEventListener('click', function(){ runGeo(true); });

  // Pedir GPS al cargar si aún no hay geo (no bloquea sondeos)
  if (!geoOk) {
    setTimeout(function(){ runGeo(false); }, 400);
  }

  refreshDeviceStatus();
})();
</script>
</body>
</html>
