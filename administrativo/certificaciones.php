<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/CertificacionEncuestador.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// ✅ PON TU KEY EN EL CONFIG:
// $GOOGLE_MAPS_API_KEY = 'TU_KEY_AQUI';

$permissions = [
  'view' => SessionData::administrador() || SessionData::superAdministrador(),
];

if (!$permissions['view']) {
  require 'permiso_denegado.php';
  exit;
}

// Obtener todas las certificaciones
$arr = CertificacionEncuestador::getAll([]);
$isvalid = $arr['output']['valid'] ?? false;
$certificaciones = $arr['output']['response'] ?? [];

$modulo = 'Detalles Encuestas';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$GOOGLE_MAPS_API_KEY = $GOOGLE_MAPS_API_KEY ?? '';

// KPIs visuales del centro de evidencias. Solo lectura.
$totalCertificaciones = is_array($certificaciones) ? count($certificaciones) : 0;
$totalGps = 0;
$totalAudio = 0;
$totalSondeos = 0;
$totalCuestionarios = 0;
$totalRegistroSimple = 0;

if (is_array($certificaciones)) {
  foreach ($certificaciones as $certKpi) {
    if (!empty($certKpi['latitud']) && !empty($certKpi['longitud'])) {
      $totalGps++;
    }
    if (!empty($certKpi['audio_duracion_segundos'])) {
      $totalAudio++;
    }

    $origenKpi = $certKpi['origen_tipo'] ?? '';
    if ($origenKpi === 'sondeo') {
      $totalSondeos++;
    } elseif ($origenKpi === 'cuestionario') {
      $totalCuestionarios++;
    } else {
      $totalRegistroSimple++;
    }
  }
}

$porcentajeGps = $totalCertificaciones > 0 ? round(($totalGps / $totalCertificaciones) * 100) : 0;
$porcentajeAudio = $totalCertificaciones > 0 ? round(($totalAudio / $totalCertificaciones) * 100) : 0;
?>

<style>
:root{
  --ev-navy:#07182f;--ev-navy2:#102a56;--ev-brand:#20427f;--ev-blue:#4b8cf7;
  --ev-cyan:#1db6db;--ev-green:#12b981;--ev-violet:#7568e8;--ev-red:#e5484d;
  --ev-bg:#f3f6fb;--ev-card:#fff;--ev-text:#101828;--ev-text2:#344054;
  --ev-muted:#667085;--ev-soft:#98a2b3;--ev-line:#e5eaf1;
  --ev-shadow:0 24px 68px rgba(15,23,42,.10);--ev-shadow-soft:0 12px 34px rgba(15,23,42,.065);
}
*{box-sizing:border-box}html{scroll-behavior:smooth}
body.ev-page{margin:0;color:var(--ev-text);font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;overflow-x:hidden;-webkit-font-smoothing:antialiased;background:radial-gradient(920px 500px at 3% -5%,rgba(75,140,247,.12),transparent 64%),radial-gradient(760px 440px at 103% 5%,rgba(29,182,219,.07),transparent 64%),linear-gradient(180deg,#f8fafd 0%,#f2f5fa 100%)}
body.ev-page:before{content:"";position:fixed;inset:0;z-index:-1;pointer-events:none;opacity:.3;background-image:linear-gradient(rgba(32,66,127,.023) 1px,transparent 1px),linear-gradient(90deg,rgba(32,66,127,.023) 1px,transparent 1px);background-size:36px 36px;mask-image:linear-gradient(to bottom,#000,transparent 84%)}
.content{padding-top:18px!important;padding-bottom:38px!important;margin-top:0!important}.container-xxl-saas{width:100%;max-width:1660px;margin:0 auto;padding-left:18px!important;padding-right:18px!important}
.ev-hero{position:relative;isolation:isolate;overflow:hidden;min-height:230px;margin-bottom:16px;padding:30px;border:1px solid rgba(255,255,255,.12);border-radius:30px;color:#fff;background:radial-gradient(570px 280px at 9% 0%,rgba(75,140,247,.36),transparent 66%),radial-gradient(480px 270px at 94% 10%,rgba(29,182,219,.19),transparent 67%),linear-gradient(135deg,#173e7b 0%,#102a56 47%,#07162e 100%);box-shadow:0 30px 80px rgba(8,28,63,.24)}
.ev-hero:before{content:"";position:absolute;z-index:-1;width:440px;height:440px;right:-160px;top:-225px;border:1px solid rgba(255,255,255,.075);border-radius:50%;box-shadow:0 0 0 45px rgba(255,255,255,.021),0 0 0 92px rgba(255,255,255,.015),0 0 0 138px rgba(255,255,255,.010)}
.ev-hero-grid{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:28px;align-items:center}.ev-eyebrow{display:inline-flex;align-items:center;gap:8px;min-height:32px;margin-bottom:13px;padding:7px 11px;border:1px solid rgba(255,255,255,.14);border-radius:999px;color:rgba(255,255,255,.88);background:rgba(255,255,255,.075);backdrop-filter:blur(12px);font-size:.67rem;font-weight:800;letter-spacing:.62px;text-transform:uppercase}.ev-dot{width:7px;height:7px;border-radius:50%;background:#5de4a0;box-shadow:0 0 0 5px rgba(93,228,160,.11),0 0 16px rgba(93,228,160,.45)}
.ev-hero h1{margin:0;color:#fff;font-family:Manrope,Inter,sans-serif;font-size:clamp(1.9rem,3vw,3rem);line-height:1.04;font-weight:800;letter-spacing:-1.5px}.ev-hero h1 span{color:#b7d0ff}.ev-hero p{max-width:860px;margin:11px 0 0;color:rgba(255,255,255,.70);font-size:.91rem;line-height:1.67;font-weight:500}.ev-pills{display:flex;flex-wrap:wrap;gap:8px;margin-top:18px}.ev-pill{display:inline-flex;align-items:center;gap:7px;min-height:35px;padding:8px 11px;border:1px solid rgba(255,255,255,.10);border-radius:11px;color:rgba(255,255,255,.84);background:rgba(255,255,255,.07);font-size:.67rem;font-weight:700}.ev-pill i{color:#a7c7ff}
.ev-kpis{display:grid;grid-template-columns:repeat(4,minmax(92px,1fr));gap:9px;min-width:550px}.ev-kpi{min-height:112px;padding:14px;border:1px solid rgba(255,255,255,.12);border-radius:17px;background:linear-gradient(145deg,rgba(255,255,255,.115),rgba(255,255,255,.05));backdrop-filter:blur(14px);transition:.22s ease}.ev-kpi:hover{transform:translateY(-4px);border-color:rgba(255,255,255,.20);background:linear-gradient(145deg,rgba(255,255,255,.17),rgba(255,255,255,.07))}.ev-kpi i{width:31px;height:31px;display:flex;align-items:center;justify-content:center;margin-bottom:13px;border-radius:10px;color:#d8e8ff;background:rgba(255,255,255,.10);font-size:.78rem}.ev-kpi strong{display:block;color:#fff;font:800 1.36rem/1 Manrope,Inter,sans-serif;letter-spacing:-.55px}.ev-kpi span{display:block;margin-top:5px;color:rgba(255,255,255,.58);font-size:.59rem;line-height:1.25;font-weight:700}
.ev-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}.ev-summary-card{position:relative;overflow:hidden;min-height:94px;padding:14px;border:1px solid var(--ev-line);border-radius:16px;background:#fff;box-shadow:var(--ev-shadow-soft);transition:.18s ease}.ev-summary-card:hover{transform:translateY(-3px);box-shadow:0 18px 38px rgba(15,23,42,.085)}.ev-summary-top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px}.ev-summary-icon{width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:11px;color:var(--ev-brand);background:#edf4ff;font-size:.78rem}.ev-summary-card strong{display:block;color:var(--ev-text);font:800 1.05rem Manrope,Inter,sans-serif}.ev-summary-card span{display:block;color:var(--ev-soft);font-size:.58rem;font-weight:650}.ev-progress{overflow:hidden;height:5px;margin-top:9px;border-radius:999px;background:#eef2f7}.ev-progress>div{height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--ev-blue),var(--ev-brand))}
.card-pro{overflow:hidden;margin-bottom:16px;border:1px solid var(--ev-line)!important;border-radius:24px!important;background:#fff!important;box-shadow:var(--ev-shadow)!important}.card-pro .card-header{min-height:74px;padding:15px 18px!important;border-bottom:1px solid #edf0f5!important;background:radial-gradient(320px 120px at 4% 0%,rgba(75,140,247,.06),transparent 72%),linear-gradient(180deg,#fff,#fbfcff)!important}.ev-card-title{display:flex;align-items:center;gap:11px}.ev-card-icon{width:42px;height:42px;flex:0 0 42px;display:flex;align-items:center;justify-content:center;border-radius:13px;color:var(--ev-brand);background:#edf4ff;font-size:.92rem}.title{margin:0;color:#182230;font:800 .98rem Manrope,Inter,sans-serif}.sub{margin-top:3px;color:var(--ev-soft);font-size:.63rem;font-weight:600}.ev-count{display:inline-flex;align-items:center;gap:6px;min-height:31px;padding:6px 10px;border:1px solid #dce8fa;border-radius:999px;color:#265ea9;background:#eef5ff;font-size:.64rem;font-weight:800}
.table-wrap{overflow:hidden;padding:12px;border:1px solid #e6ebf2;border-radius:18px;background:linear-gradient(180deg,#fff,#fbfcff)}#tblCertificaciones{width:100%!important;margin:0!important;border-collapse:separate!important;border-spacing:0 7px!important}#tblCertificaciones thead th{padding:9px 10px!important;border:0!important;color:#667085!important;background:transparent!important;font-size:.58rem!important;font-weight:800!important;letter-spacing:.38px;text-transform:uppercase;white-space:nowrap}#tblCertificaciones tbody td{padding:10px!important;border-top:1px solid #e9edf4!important;border-bottom:1px solid #e9edf4!important;color:#344054!important;background:#fff!important;font-size:.65rem!important;line-height:1.45;vertical-align:middle!important;transition:.18s ease}#tblCertificaciones tbody td:first-child{border-left:1px solid #e9edf4!important;border-radius:12px 0 0 12px}#tblCertificaciones tbody td:last-child{border-right:1px solid #e9edf4!important;border-radius:0 12px 12px 0}#tblCertificaciones tbody tr{transition:transform .18s ease}#tblCertificaciones tbody tr:hover{transform:translateY(-2px)}#tblCertificaciones tbody tr:hover td{border-color:#dce7f6!important;background:linear-gradient(90deg,#f6faff,#fff)!important;box-shadow:0 9px 23px rgba(15,23,42,.045)}
#tblCertificaciones .badge{display:inline-flex;align-items:center;gap:4px;min-height:27px;padding:5px 8px;border-radius:8px;font-size:.59rem;font-weight:800}#tblCertificaciones .badge.bg-primary{color:#175cd3!important;border:1px solid #d1e9ff;background:#eff8ff!important}#tblCertificaciones .badge.bg-info{color:#176b87!important;border:1px solid #cdedf5;background:#edf9fc!important}#tblCertificaciones .badge.bg-secondary,#tblCertificaciones .bg-secondary-subtle{color:#475467!important;border:1px solid #eaecf0;background:#f9fafb!important}#tblCertificaciones .badge.bg-success,#tblCertificaciones .bg-success-subtle{color:#06795b!important;border:1px solid #d1fae5;background:#ecfdf5!important}
.ev-action{min-height:34px;display:inline-flex;align-items:center;justify-content:center;gap:5px;margin:2px;padding:6px 9px!important;border-radius:9px!important;font-size:.60rem!important;font-weight:800!important;transition:.18s ease}.ev-action:hover{transform:translateY(-2px)}.ev-detail{border:0!important;color:#fff!important;background:linear-gradient(135deg,var(--ev-blue),var(--ev-brand))!important;box-shadow:0 7px 14px rgba(32,66,127,.16)}.ev-map{color:var(--ev-brand)!important;border:1px solid #ccddf3!important;background:#f8fbff!important}
.dataTables_wrapper{width:100%!important;color:#667085;font-size:.70rem}.dataTables_wrapper .row{margin-left:0!important;margin-right:0!important;align-items:center}.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{margin-bottom:12px}.dataTables_wrapper .dataTables_length label,.dataTables_wrapper .dataTables_filter label{color:#667085;font-size:.65rem;font-weight:700}.dataTables_wrapper .dataTables_filter input{min-height:38px;margin-left:8px;padding:0 11px;border:1px solid #d7dee9;border-radius:10px;background:#fff;outline:none}.dataTables_wrapper .dataTables_filter input:focus{border-color:var(--ev-blue);box-shadow:0 0 0 4px rgba(75,140,247,.10)}.dataTables_wrapper .dataTables_length select{min-height:37px;border:1px solid #d7dee9;border-radius:9px;background:#fff}.dataTables_wrapper .dataTables_info{color:#98a2b3!important;font-size:.63rem!important;font-weight:600}.dataTables_wrapper .dataTables_paginate .paginate_button{min-width:33px;height:33px;display:inline-flex!important;align-items:center;justify-content:center;padding:0 8px!important;border:1px solid transparent!important;border-radius:9px!important;color:#667085!important;background:transparent!important;font-size:.64rem;font-weight:800;box-shadow:none!important}.dataTables_wrapper .dataTables_paginate .paginate_button.current,.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{color:#fff!important;background:linear-gradient(135deg,var(--ev-blue),var(--ev-brand))!important;box-shadow:0 8px 18px rgba(32,66,127,.20)!important}
.modal-pro .modal-content{overflow:hidden;border:1px solid rgba(15,23,42,.09)!important;border-radius:24px!important;box-shadow:0 30px 82px rgba(15,23,42,.25)!important}.modal-pro .modal-header{position:relative;overflow:hidden;padding:18px 20px!important;border-bottom:0!important;color:#fff;background:radial-gradient(410px 190px at 5% 0%,rgba(75,140,247,.28),transparent 72%),radial-gradient(340px 170px at 100% 0%,rgba(29,182,219,.17),transparent 70%),linear-gradient(135deg,#173d79,#102a56 55%,#081b38)!important}.modal-pro .modal-header:after{content:"";position:absolute;width:190px;height:190px;right:-85px;top:-110px;border:1px solid rgba(255,255,255,.08);border-radius:50%;box-shadow:0 0 0 30px rgba(255,255,255,.02)}.modal-pro .modal-title{position:relative;z-index:2;color:#fff!important;font:800 1rem Manrope,Inter,sans-serif}.ev-modal-icon{position:relative;z-index:2;width:44px;height:44px;flex:0 0 44px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.18);border-radius:14px;color:#fff;background:rgba(255,255,255,.12);backdrop-filter:blur(10px)}.ev-modal-sub{position:relative;z-index:2;margin-top:3px;color:rgba(255,255,255,.63);font-size:.62rem;font-weight:600}.modal-pro .btn-close{position:relative;z-index:3;filter:invert(1) brightness(2);opacity:.9}.modal-pro .modal-body{padding:18px!important;background:linear-gradient(180deg,#fbfcfe,#f5f8fc)!important}.modal-pro .modal-footer{padding:12px 18px!important;border-top:1px solid #e7ebf1!important;background:#fff}
.kpi{min-height:82px;padding:12px 14px;border:1px solid #e4e9f1;border-radius:15px;background:#fff;box-shadow:0 8px 20px rgba(15,23,42,.04);transition:.18s ease}.kpi:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(15,23,42,.07)}.kpi .label{color:var(--ev-soft);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em}.kpi .value{margin-top:3px;color:var(--ev-text);font-size:14px;line-height:1.35;font-weight:800}
.map-box{position:relative;overflow:hidden;border:1px solid #d9e4f1;border-radius:18px;background:#fff;box-shadow:0 12px 30px rgba(15,23,42,.055);transition:transform .28s ease,box-shadow .28s ease,border-color .28s ease}.map-box:after{content:"";position:absolute;inset:0;pointer-events:none;border:1px solid rgba(75,140,247,0);border-radius:inherit;transition:.28s ease}.map-box:hover{transform:translateY(-4px) scale(1.004);border-color:#bfd4f0;box-shadow:0 22px 48px rgba(32,66,127,.14)}.map-box:hover:after{border-color:rgba(75,140,247,.40);box-shadow:inset 0 0 0 3px rgba(75,140,247,.06)}#mapCanvas{width:100%;height:360px;filter:saturate(.96) contrast(1.02);transition:filter .28s ease}.map-box:hover #mapCanvas{filter:saturate(1.12) contrast(1.04)}#modalDetalleCertificacionBody audio{width:100%;min-height:42px;border:1px solid #e0e7f0;border-radius:12px;background:#f8fafc}#modalDetalleCertificacionBody img{max-width:100%;border-radius:14px}#modalDetalleCertificacionBody .card{border:1px solid #e4e9f1!important;border-radius:17px!important;box-shadow:0 9px 22px rgba(15,23,42,.045)!important}
.btn-soft{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:8px 13px;border:1px solid #d7e2f2!important;border-radius:11px!important;color:var(--ev-brand)!important;background:#fff!important;font-size:.68rem;font-weight:800}
.ev-footer{margin-top:18px;padding:10px 12px;text-align:center;color:#98a2b3;font-size:.62rem;font-weight:650}
@media(max-width:1320px){.ev-hero-grid{grid-template-columns:1fr}.ev-kpis{min-width:0;width:100%}}@media(max-width:991px){.container-xxl-saas{padding-left:13px!important;padding-right:13px!important}.ev-hero{padding:23px}.ev-summary{grid-template-columns:repeat(2,1fr)}}@media(max-width:767px){.content{padding-top:12px!important}.container-xxl-saas{padding-left:10px!important;padding-right:10px!important}.ev-hero{min-height:0;padding:20px 17px;border-radius:22px}.ev-hero h1{font-size:1.8rem}.ev-hero p{font-size:.80rem}.ev-kpis{grid-template-columns:repeat(2,1fr)}.card-pro{border-radius:19px!important}.card-pro .card-header{padding:14px!important}.card-pro .card-body{padding:12px!important}.table-wrap{padding:8px}#tblCertificaciones{min-width:980px}#mapCanvas{height:280px}.dataTables_wrapper .dataTables_filter input{width:100%;margin:6px 0 0}}@media(max-width:480px){.ev-kpis{gap:7px}.ev-kpi{min-height:96px;padding:12px}.ev-kpi strong{font-size:1.16rem}.ev-kpi span{font-size:.56rem}.ev-summary{grid-template-columns:1fr}}@media(prefers-reduced-motion:reduce){*,*:before,*:after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important;scroll-behavior:auto!important}}
</style>

<body class="ev-page">
  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <div class="content">
    <div class="container-fluid container-xxl-saas">

      <section class="ev-hero">
        <div class="ev-hero-grid">
          <div>
            <div class="ev-eyebrow"><span class="ev-dot"></span>Estadística360 · Survey Evidence Center</div>
            <h1>Evidencias y <span>Certificación de Encuestas</span></h1>
            <p>Audita cada registro de campo con trazabilidad de encuestador, encuestado, origen, ubicación GPS y evidencia de audio desde una vista diseñada para verificación, control y seguimiento.</p>
            <div class="ev-pills">
              <span class="ev-pill"><i class="fas fa-location-crosshairs"></i>Evidencia geográfica</span>
              <span class="ev-pill"><i class="fas fa-wave-square"></i>Soporte de audio</span>
              <span class="ev-pill"><i class="fas fa-shield-halved"></i>Trazabilidad de campo</span>
            </div>
          </div>

          <div class="ev-kpis">
            <div class="ev-kpi"><i class="fas fa-clipboard-check"></i><strong><?= (int)$totalCertificaciones ?></strong><span>Encuestas certificadas</span></div>
            <div class="ev-kpi"><i class="fas fa-location-dot"></i><strong><?= (int)$totalGps ?></strong><span>Con evidencia GPS</span></div>
            <div class="ev-kpi"><i class="fas fa-microphone-lines"></i><strong><?= (int)$totalAudio ?></strong><span>Con evidencia de audio</span></div>
            <div class="ev-kpi"><i class="fas fa-layer-group"></i><strong><?= (int)($totalSondeos + $totalCuestionarios) ?></strong><span>Registros vinculados</span></div>
          </div>
        </div>
      </section>

      <section class="ev-summary">
        <article class="ev-summary-card"><div class="ev-summary-top"><div class="ev-summary-icon"><i class="fas fa-location-crosshairs"></i></div><strong><?= (int)$porcentajeGps ?>%</strong></div><span>Cobertura GPS de las certificaciones</span><div class="ev-progress"><div style="width:<?= (int)$porcentajeGps ?>%"></div></div></article>
        <article class="ev-summary-card"><div class="ev-summary-top"><div class="ev-summary-icon"><i class="fas fa-wave-square"></i></div><strong><?= (int)$porcentajeAudio ?>%</strong></div><span>Cobertura de evidencia de audio</span><div class="ev-progress"><div style="width:<?= (int)$porcentajeAudio ?>%"></div></div></article>
        <article class="ev-summary-card"><div class="ev-summary-top"><div class="ev-summary-icon"><i class="fas fa-poll"></i></div><strong><?= (int)$totalSondeos ?></strong></div><span>Certificaciones provenientes de sondeos</span><div class="ev-progress"><div style="width:<?= $totalCertificaciones > 0 ? round(($totalSondeos/$totalCertificaciones)*100) : 0 ?>%"></div></div></article>
        <article class="ev-summary-card"><div class="ev-summary-top"><div class="ev-summary-icon"><i class="fas fa-clipboard-list"></i></div><strong><?= (int)$totalCuestionarios ?></strong></div><span>Certificaciones provenientes de cuestionarios</span><div class="ev-progress"><div style="width:<?= $totalCertificaciones > 0 ? round(($totalCuestionarios/$totalCertificaciones)*100) : 0 ?>%"></div></div></article>
      </section>

      <section class="card card-pro">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="ev-card-title">
              <div class="ev-card-icon"><i class="fas fa-shield-alt"></i></div>
              <div>
                <h3 class="title"><?= h($modulo) ?></h3>
                <div class="sub">Todas las encuestas registradas con evidencia de audio y geolocalización.</div>
              </div>
            </div>
            <span class="ev-count"><i class="fas fa-database"></i><?= (int)$totalCertificaciones ?> <?= $totalCertificaciones === 1 ? 'registro' : 'registros' ?></span>
          </div>
        </div>

        <div class="card-body p-3 p-lg-4">
          <div class="table-wrap">
            <div class="table-responsive">
              <table id="tblCertificaciones" class="table table-striped table-hover dt-compact align-middle">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Encuestador</th>
                    <th>Encuestado</th>
                    <th>Origen</th>
                    <th>GPS</th>
                    <th>Audio</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($certificaciones as $cert): ?>
                  <?php
                    $id = (int)($cert['id'] ?? 0);
                    $fechaTxt = '';
                    if (!empty($cert['fecha_certificacion'])) {
                      try {
                        $fecha = new DateTime($cert['fecha_certificacion']);
                        $fechaTxt = $fecha->format('d/m/Y H:i');
                      } catch(Exception $e){ $fechaTxt = h($cert['fecha_certificacion']); }
                    }

                    // Origen badge
                    $origenTipo = $cert['origen_tipo'] ?? '';
                    $badge = 'secondary';
                    $icon  = 'fa-user';
                    $texto = 'Registro Simple';

                    if ($origenTipo === 'sondeo') {
                      $badge = 'primary';
                      $icon = 'fa-poll';
                      $texto = 'Sondeo: ' . h($cert['sondeo_nombre'] ?? 'N/A');
                    } elseif ($origenTipo === 'cuestionario') {
                      $badge = 'info';
                      $icon = 'fa-clipboard-list';
                      $texto = 'Cuestionario: ' . h($cert['cuestionario_nombre'] ?? 'N/A');
                    }

                    $hasGps = !empty($cert['latitud']) && !empty($cert['longitud']);
                    $hasAudio = !empty($cert['audio_duracion_segundos']);
                  ?>
                  <tr>
                    <td><span class="fw-bold"><?= $id ?></span></td>
                    <td class="text-nowrap"><?= h($fechaTxt) ?></td>
                    <td><?= h(($cert['encuestador_nombre'] ?? '') . ' ' . ($cert['encuestador_apellido'] ?? '')) ?></td>
                    <td><?= h($cert['votante_nombre'] ?? '') ?></td>
                    <td class="text-nowrap">
                      <span class="badge bg-<?= h($badge) ?>">
                        <i class="fas <?= h($icon) ?> me-1"></i><?= $texto ?>
                      </span>
                    </td>
                    <td class="text-nowrap">
                      <?php if ($hasGps): ?>
                        <span class="badge bg-success-subtle text-success fw-bold">
                          <i class="fas fa-location-dot me-1"></i>OK
                        </span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                      <?php if ($hasAudio): ?>
                        <span class="badge bg-success">
                          <i class="fas fa-microphone me-1"></i><?= (int)$cert['audio_duracion_segundos'] ?>s
                        </span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Sin</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                      <button type="button"
                        class="btn ev-action ev-detail"
                        onclick="CERTIFICACIONES.verDetalle(<?= $id ?>)">
                        <i class="fas fa-eye me-1"></i>Detalle
                      </button>

                      <?php if ($hasGps): ?>
                        <a class="btn ev-action ev-map"
                           target="_blank"
                           href="https://www.google.com/maps?q=<?= h($cert['latitud']) ?>,<?= h($cert['longitud']) ?>">
                          <i class="fas fa-map-marker-alt me-1"></i>Mapa
                        </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>


      <div class="ev-footer">Desarrollado por SpiderSoftware S.A.S.</div>
    </div>
  </div>

  <!-- ✅ Modal Detalle PRO -->
  <div class="modal fade modal-pro" id="modalDetalleCertificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-3">
            <div class="ev-modal-icon"><i class="fas fa-shield-alt"></i></div>
            <div>
              <h5 class="modal-title">Detalle de la Encuesta</h5>
              <div class="ev-modal-sub">Evidencia, trazabilidad, ubicación y soporte de campo.</div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" id="modalDetalleCertificacionBody">
          <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x" style="color:var(--brand)"></i>
            <p class="mt-3 mb-0" style="font-weight:800;color:var(--muted)">Cargando información...</p>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-soft" data-bs-dismiss="modal">
            <i class="fas fa-xmark me-1"></i>Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  

  <?php include 'admin/include/gerenic_script.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>

  <!-- ✅ KEY para Maps en JS (se administra desde config) -->
  <script>
    window.GOOGLE_MAPS_API_KEY = "<?= h($GOOGLE_MAPS_API_KEY) ?>";
  </script>

  <!-- ✅ Carga Google Maps JS API (NO pegues la key directa aquí; va por la variable) -->
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= h($GOOGLE_MAPS_API_KEY) ?>&callback=initMap">
  </script>

  <script type="text/javascript" src="admin/js/certificaciones.js"></script>
</body>
</html>
