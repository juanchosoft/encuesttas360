<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/CertificacionEncuestador.php';
include './admin/classes/RespuestaSondeo.php';
include './admin/classes/FichaTecnicaEncuesta.php';
include './admin/include/generic_info_configuracion.php';

$canView = SessionData::hasPermission('certificaciones.dashboard.view');
$canExport = SessionData::hasPermission('certificaciones.dashboard.export');
$canRevisar = SessionData::hasPermission('certificaciones.revisar');
$canValidarIa = SessionData::hasPermission('certificaciones.validar_ia');
$canRevalidar = SessionData::hasPermission('certificaciones.revalidar');
$canHistorial = SessionData::hasPermission('certificaciones.historial.view')
  || SessionData::hasPermission('certificaciones.dashboard.view');

if (!$canView) {
  require 'permiso_denegado.php';
  exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$GOOGLE_MAPS_API_KEY = $GOOGLE_MAPS_API_KEY ?? '';

$sondeosDisp = [];
$resSondeos = RespuestaSondeo::getSondeosDisponibles([]);
if ($resSondeos['output']['valid'] ?? false) {
  $sondeosDisp = $resSondeos['output']['response'] ?? [];
}
$fichasDisp = [];
$resFichas = FichaTecnicaEncuesta::getAll(['solo_habilitados' => true]);
if ($resFichas['output']['valid'] ?? false) {
  $fichasDisp = $resFichas['output']['response'] ?? [];
}

$kpisResp = CertificacionEncuestador::getDashboardKpis([]);
$kpis = ($kpisResp['output']['valid'] ?? false) ? ($kpisResp['output']['response'] ?? []) : [];

$listResp = CertificacionEncuestador::getDashboardList([]);
$rows = ($listResp['output']['valid'] ?? false) ? ($listResp['output']['response'] ?? []) : [];

$optsEncuestadores = [];
$mapPoints = [];
foreach ($rows as $cOpt) {
  $uidOpt = intval($cOpt['tbl_usuario_id'] ?? 0);
  if ($uidOpt > 0) {
    $nombreOpt = trim(($cOpt['encuestador_nombre'] ?? '') . ' ' . ($cOpt['encuestador_apellido'] ?? ''));
    if ($nombreOpt === '') $nombreOpt = 'Encuestador #' . $uidOpt;
    $optsEncuestadores[$uidOpt] = $nombreOpt;
  }
  $lat = $cOpt['latitud'] ?? null;
  $lng = $cOpt['longitud'] ?? null;
  if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '' && is_numeric($lat) && is_numeric($lng)) {
    $mapPoints[] = [
      'id' => (int)($cOpt['id'] ?? 0),
      'lat' => (float)$lat,
      'lng' => (float)$lng,
      'estado' => (string)($cOpt['estado_revision'] ?? 'pendiente'),
      'origen' => (string)($cOpt['origen_tipo'] ?? ''),
      'encuestador' => trim(($cOpt['encuestador_nombre'] ?? '') . ' ' . ($cOpt['encuestador_apellido'] ?? '')),
      'votante' => (string)($cOpt['votante_nombre'] ?? ''),
      'fecha' => (string)($cOpt['fecha_certificacion'] ?? ''),
      'audio' => (int)($cOpt['audio_duracion_segundos'] ?? 0),
      'metodo' => (string)($cOpt['revision_metodo'] ?? 'ninguno'),
      'nombre_origen' => ($cOpt['origen_tipo'] ?? '') === 'sondeo'
        ? (string)($cOpt['sondeo_nombre'] ?? 'Sondeo')
        : (($cOpt['origen_tipo'] ?? '') === 'cuestionario'
          ? (string)($cOpt['cuestionario_nombre'] ?? 'Encuesta')
          : 'Sin tipo'),
    ];
  }
}
asort($optsEncuestadores, SORT_NATURAL | SORT_FLAG_CASE);

$porEstado = $kpis['por_estado'] ?? [];
$estadoLabel = [
  'pendiente' => 'Pendiente',
  'bien' => 'Bien',
  'mal' => 'Mal',
  'en_revision' => 'En revisión',
  'anulada' => 'Anulada',
];
$estadoBadge = [
  'pendiente' => 'secondary',
  'bien' => 'success',
  'mal' => 'danger',
  'en_revision' => 'warning',
  'anulada' => 'dark',
];
?>
<style>
.cd-wrap{max-width:1660px;margin:0 auto;padding:18px}
.cd-hero{padding:22px 24px;border-radius:22px;color:#fff;background:radial-gradient(420px 180px at 8% 0%,rgba(75,140,247,.28),transparent 70%),linear-gradient(135deg,#173e7b,#102a56 55%,#07162e);margin-bottom:16px}
.cd-hero h1{margin:0;font:800 1.6rem Manrope,Inter,sans-serif}
.cd-hero p{margin:8px 0 0;opacity:.78;font-size:.88rem}
.cd-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:16px}
.cd-kpi{padding:14px;border:1px solid #e5eaf1;border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(15,23,42,.05)}
.cd-kpi .l{font-size:.65rem;font-weight:800;color:#667085;text-transform:uppercase;letter-spacing:.03em}
.cd-kpi .v{margin-top:4px;font:800 1.35rem Manrope,Inter,sans-serif;color:#101828}
.cd-card{border:1px solid #e5eaf1;border-radius:18px;background:#fff;box-shadow:0 12px 28px rgba(15,23,42,.06);margin-bottom:14px;overflow:hidden}
.cd-card .card-body{padding:14px 16px}
.cd-card-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}
.cd-card-head h3{margin:0;font:800 .92rem Manrope,Inter,sans-serif;color:#182230}
.cd-card-head .sub{margin:2px 0 0;color:#98a2b3;font-size:.68rem;font-weight:600}
.cd-charts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:14px}
.cd-chart-box{min-height:280px;position:relative}
.cd-chart-box canvas{max-height:240px}
.cd-map-wrap{position:relative;border:1px solid #d9e4f1;border-radius:16px;overflow:hidden;background:#e9eef5}
#cdMapCanvas{width:100%;height:460px}
.cd-map-empty{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:#98a2b3;background:#f4f7fb;z-index:2}
.cd-infowin{min-width:240px;max-width:300px;font-family:Inter,system-ui,sans-serif}
.cd-infowin strong{display:block;color:#142b50;font-size:.78rem;margin-bottom:4px}
.cd-infowin .meta{color:#68788e;font-size:.68rem;line-height:1.45;margin-bottom:8px}
.cd-infowin .btn{font-size:.68rem;font-weight:800}
.cd-badge{display:inline-flex;align-items:center;min-height:24px;padding:3px 8px;border-radius:8px;font-size:.62rem;font-weight:800}
#cdTabla_wrapper .dataTables_filter input{min-height:36px;border-radius:10px;border:1px solid #d7dee9}
#cdTabla_wrapper .dataTables_length select{min-height:34px;border-radius:8px}
@media(max-width:1100px){.cd-charts{grid-template-columns:1fr}}
@media(max-width:767px){#cdMapCanvas{height:340px}}
</style>
<body>
<?php include './admin/include/navbar.php'; ?>
<?php include './admin/include/header.php'; ?>
<div class="content">
  <div class="container-fluid cd-wrap">
    <section class="cd-hero">
      <h1>Dashboard validación de encuestadores</h1>
      <p>KPIs, gráficas, mapa territorial y listado filtrable de evidencias de campo.</p>
    </section>

    <div class="cd-card card" id="cdFiltrosCard">
      <div class="card-body">
        <div class="cd-card-head mb-2">
          <div>
            <h3>Filtros</h3>
            <div class="sub">Tipo e ítem definen el universo; el resto afina KPIs, gráficas, mapa y tabla.</div>
          </div>
        </div>
        <div class="row g-2 align-items-end mb-2">
          <div class="col-md-3">
            <label class="form-label small" for="cd_tipo">Tipo</label>
            <select id="cd_tipo" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="sondeo">Sondeo</option>
              <option value="cuestionario">Encuesta</option>
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label small" for="cd_item" id="cd_item_label">Sondeo / Encuesta</label>
            <select id="cd_item" class="form-select form-select-sm" disabled>
              <option value="">Selecciona un tipo primero…</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small" for="cd_estado">Estado</label>
            <select id="cd_estado" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="pendiente">Pendiente</option>
              <option value="bien">Bien</option>
              <option value="mal">Mal</option>
              <option value="en_revision">En revisión</option>
              <option value="anulada">Anulada</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small" for="cd_encuestador">Encuestador</label>
            <select id="cd_encuestador" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($optsEncuestadores as $uid => $nom): ?>
                <option value="<?= (int)$uid ?>"><?= h($nom) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="row g-2 align-items-end">
          <div class="col-md-2">
            <label class="form-label small" for="cd_metodo">Método</label>
            <select id="cd_metodo" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="manual">Manual</option>
              <option value="ia">IA</option>
              <option value="ninguno">Ninguno</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small" for="cd_audio">Audio</label>
            <select id="cd_audio" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="1">Con</option>
              <option value="0">Sin</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small" for="cd_desde">Desde</label>
            <input type="date" id="cd_desde" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label small" for="cd_hasta">Hasta</label>
            <input type="date" id="cd_hasta" class="form-control form-control-sm">
          </div>
          <div class="col-md-2 d-flex gap-1">
            <button type="button" class="btn btn-sm btn-primary flex-fill" id="cd_filtrar">Filtrar</button>
          </div>
        </div>
        <div class="mt-2 d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" id="cd_limpiar">Limpiar</button>
          <?php if ($canExport): ?>
          <button type="button" class="btn btn-sm btn-success" id="cd_export"><i class="fas fa-file-excel me-1"></i>Excel / CSV</button>
          <?php endif; ?>
          <a class="btn btn-sm btn-outline-primary" href="certificaciones.php">Ir a revisión</a>
          <span class="align-self-center small text-muted" id="cdCountHint"><?= count($rows) ?> registros en tabla · <?= count($mapPoints) ?> con GPS</span>
        </div>
      </div>
    </div>

    <div class="cd-kpis" id="cdKpis">
      <div class="cd-kpi"><div class="l">Total listado</div><div class="v" data-k="total"><?= (int)($kpis['total'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">Pendiente</div><div class="v" data-k="pendiente"><?= (int)($porEstado['pendiente'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">Bien</div><div class="v" data-k="bien"><?= (int)($porEstado['bien'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">Mal</div><div class="v" data-k="mal"><?= (int)($porEstado['mal'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">En revisión</div><div class="v" data-k="en_revision"><?= (int)($porEstado['en_revision'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">Anulada</div><div class="v" data-k="anulada"><?= (int)($porEstado['anulada'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">% Bien</div><div class="v" data-k="pct"><?= h((string)($kpis['pct_bien_sobre_revisadas'] ?? 0)) ?>%</div></div>
      <div class="cd-kpi"><div class="l">Con audio</div><div class="v" data-k="con_audio"><?= (int)($kpis['con_audio'] ?? 0) ?></div></div>
      <div class="cd-kpi"><div class="l">Con GPS</div><div class="v" data-k="con_gps"><?= (int)($kpis['con_gps'] ?? count($mapPoints)) ?></div></div>
      <div class="cd-kpi"><div class="l">Método IA</div><div class="v" data-k="ia"><?= (int)($kpis['metodo_ia'] ?? 0) ?></div></div>
    </div>

    <div class="cd-charts">
      <div class="cd-card card">
        <div class="card-body">
          <div class="cd-card-head"><div><h3>Por estado</h3><div class="sub">Distribución de calidad</div></div></div>
          <div class="cd-chart-box"><canvas id="chartEstado"></canvas></div>
        </div>
      </div>
      <div class="cd-card card">
        <div class="card-body">
          <div class="cd-card-head"><div><h3>Por tipo</h3><div class="sub">Encuesta / sondeo</div></div></div>
          <div class="cd-chart-box"><canvas id="chartOrigen"></canvas></div>
        </div>
      </div>
      <div class="cd-card card">
        <div class="card-body">
          <div class="cd-card-head"><div><h3>Top encuestadores</h3><div class="sub">Volumen de evidencias</div></div></div>
          <div class="cd-chart-box"><canvas id="chartEncuestadores"></canvas></div>
        </div>
      </div>
    </div>

    <div class="cd-card card">
      <div class="card-body">
        <div class="cd-card-head">
          <div>
            <h3>Mapa territorial</h3>
            <div class="sub">Todos los puntos GPS del filtro actual. Clic en marcador → ficha y detalle.</div>
          </div>
          <span class="badge bg-primary-subtle text-primary" id="cdMapCount"><?= count($mapPoints) ?> puntos</span>
        </div>
        <div class="cd-map-wrap">
          <div id="cdMapEmpty" class="cd-map-empty<?= count($mapPoints) ? ' d-none' : '' ?>">
            <i class="fas fa-map-location-dot fa-2x"></i>
            <div>Sin coordenadas GPS en el filtro actual</div>
          </div>
          <div id="cdMapCanvas"></div>
        </div>
      </div>
    </div>

    <div class="cd-card card">
      <div class="card-body">
        <div class="cd-card-head">
          <div>
            <h3>Listado de evidencias</h3>
            <div class="sub">DataTable con búsqueda, orden y acceso al detalle.</div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle w-100" id="cdTabla">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Método</th>
                <th>Encuestador</th>
                <th>Encuestado</th>
                <th>Origen</th>
                <th>Audio</th>
                <th>GPS</th>
                <th>Revisión</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="cdTablaBody">
            <?php foreach ($rows as $r): ?>
              <?php
                $est = (string)($r['estado_revision'] ?? 'pendiente');
                $hasGpsRow = !empty($r['latitud']) && !empty($r['longitud']) && is_numeric($r['latitud']) && is_numeric($r['longitud']);
              ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td class="text-nowrap"><?= h($r['fecha_certificacion'] ?? '') ?></td>
                <td><span class="cd-badge badge bg-<?= h($estadoBadge[$est] ?? 'secondary') ?>"><?= h($estadoLabel[$est] ?? $est) ?></span></td>
                <td><?= h($r['revision_metodo'] ?? 'ninguno') ?></td>
                <td><?= h(trim(($r['encuestador_nombre'] ?? '') . ' ' . ($r['encuestador_apellido'] ?? ''))) ?></td>
                <td><?= h($r['votante_nombre'] ?? '') ?></td>
                <td><?= h($r['origen_tipo'] ?? '') ?></td>
                <td><?= (int)($r['audio_duracion_segundos'] ?? 0) ?>s</td>
                <td><?= $hasGpsRow ? 'Sí' : 'No' ?></td>
                <td class="text-nowrap"><?= h($r['revision_fecha'] ?? '') ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary cd-btn-detalle" data-id="<?= (int)$r['id'] ?>">
                    <i class="fas fa-eye me-1"></i>Detalle
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle (reutiliza CERTIFICACIONES.js) -->
<div class="modal fade" id="modalDetalleCertificacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white">Detalle de validación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="modalDetalleCertificacionBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
window.CERT_DASH_EXPORT = <?= $canExport ? 'true' : 'false' ?>;
window.CERT_DASH_KPIS = <?= json_encode($kpis, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.CERT_DASH_POINTS = <?= json_encode($mapPoints, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.CERT_DASH_SONDEOS = <?= json_encode(array_map(function ($s) {
  return [
    'id' => (int)($s['id'] ?? 0),
    'label' => trim((string)($s['sondeo'] ?? ('Sondeo #' . ($s['id'] ?? '')))),
  ];
}, $sondeosDisp), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.CERT_DASH_FICHAS = <?= json_encode(array_map(function ($f) {
  $label = trim((string)($f['tema'] ?? ''));
  if ($label === '') {
    $label = trim((string)($f['realizada_por_o_encomendada_por'] ?? ''));
  }
  if ($label === '') {
    $label = 'Encuesta #' . (int)($f['id'] ?? 0);
  }
  return [
    'id' => (int)($f['id'] ?? 0),
    'label' => $label,
  ];
}, $fichasDisp), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.GOOGLE_MAPS_API_KEY = <?= json_encode($GOOGLE_MAPS_API_KEY) ?>;
window.CERT_PERMS = <?= json_encode([
  'revisar' => (bool)$canRevisar,
  'validar_ia' => (bool)$canValidarIa,
  'revalidar' => (bool)$canRevalidar,
  'historial' => (bool)$canHistorial,
], JSON_UNESCAPED_UNICODE) ?>;
window.initMap = function () { window.__GMAPS_READY = true; if (window.CertDash && typeof window.CertDash.onMapsReady === 'function') window.CertDash.onMapsReady(); };
</script>
<script type="text/javascript" src="admin/js/datatables/jquery.dataTables.min.js"></script>
<link href="admin/js/datatables/jquery.dataTables.min.css" rel="stylesheet" />
<script src="admin/js/certificaciones.js?v=<?= rawurlencode((string)@filemtime(__DIR__ . '/admin/js/certificaciones.js')) ?>"></script>
<script src="admin/js/certificaciones_dashboard.js?v=<?= rawurlencode((string)@filemtime(__DIR__ . '/admin/js/certificaciones_dashboard.js')) ?>"></script>
<?php if ($GOOGLE_MAPS_API_KEY !== ''): ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= h($GOOGLE_MAPS_API_KEY) ?>&callback=initMap"></script>
<?php endif; ?>
<?php include './admin/include/footer.php'; ?>
</body>
</html>
