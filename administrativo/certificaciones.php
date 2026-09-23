<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/CertificacionEncuestador.php';
include './admin/classes/RespuestaSondeo.php';
include './admin/classes/FichaTecnicaEncuesta.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// ✅ PON TU KEY EN EL CONFIG:
// $GOOGLE_MAPS_API_KEY = 'TU_KEY_AQUI';

// Módulo Validación de encuestadores
$permissions = [
  'view' => SessionData::hasPermission('certificaciones.view'),
  'revisar' => SessionData::hasPermission('certificaciones.revisar'),
  'validar_ia' => SessionData::hasPermission('certificaciones.validar_ia'),
  'revalidar' => SessionData::hasPermission('certificaciones.revalidar'),
  'historial' => SessionData::hasPermission('certificaciones.historial.view'),
];

if (!$permissions['view']) {
  require 'permiso_denegado.php';
  exit;
}

// Obtener todas las certificaciones
$arr = CertificacionEncuestador::getAll([]);
$isvalid = $arr['output']['valid'] ?? false;
$certificaciones = $arr['output']['response'] ?? [];

$arrEnc = CertificacionEncuestador::getEncuestasVinculadas([]);
$encuestasVinculadas = ($arrEnc['output']['valid'] ?? false) ? ($arrEnc['output']['response'] ?? []) : [];

$modulo = 'Validación de encuestadores';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$GOOGLE_MAPS_API_KEY = $GOOGLE_MAPS_API_KEY ?? '';

/* ==========================================================
   MAPA POR ENCUESTADOR
   Agrupa todos los registros GPS por encuestador.
========================================================== */
if (!function_exists('cert_encuestador_nombre')) {
  function cert_encuestador_nombre(array $cert): string {
    $nombre = trim((string)($cert['encuestador_nombre'] ?? '') . ' ' . (string)($cert['encuestador_apellido'] ?? ''));
    return $nombre !== '' ? $nombre : 'Encuestador';
  }
}

if (!function_exists('cert_encuestador_key')) {
  function cert_encuestador_key(array $cert): string {
    foreach (['encuestador_id','tbl_encuestador_id','tbl_usuario_id','usuario_id','user_id'] as $campo) {
      if (isset($cert[$campo]) && (string)$cert[$campo] !== '') {
        return 'id_' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$cert[$campo]);
      }
    }
    foreach (['encuestador_email','email_encuestador','email'] as $campo) {
      $correo = trim((string)($cert[$campo] ?? ''));
      if ($correo !== '') return 'mail_' . sha1(strtolower($correo));
    }
    return 'name_' . sha1(strtolower(cert_encuestador_nombre($cert)));
  }
}

$mapasEncuestadores = [];
foreach ((array)$certificaciones as $certMapa) {
  $latRaw = trim((string)($certMapa['latitud'] ?? ''));
  $lngRaw = trim((string)($certMapa['longitud'] ?? ''));
  if ($latRaw === '' || $lngRaw === '' || !is_numeric($latRaw) || !is_numeric($lngRaw)) continue;

  $key = cert_encuestador_key($certMapa);
  if (!isset($mapasEncuestadores[$key])) {
    $mapasEncuestadores[$key] = [
      'key' => $key,
      'nombre' => cert_encuestador_nombre($certMapa),
      'puntos' => []
    ];
  }

  $fechaMapa = '';
  if (!empty($certMapa['fecha_certificacion'])) {
    try { $fechaMapa = (new DateTime($certMapa['fecha_certificacion']))->format('d/m/Y H:i'); }
    catch (Exception $e) { $fechaMapa = (string)$certMapa['fecha_certificacion']; }
  }

  $origenMapa = 'Sin tipo';
  $origenTipoMapa = $certMapa['origen_tipo'] ?? '';
  if ($origenTipoMapa === 'sondeo') $origenMapa = 'Sondeo: ' . ($certMapa['sondeo_nombre'] ?? 'N/A');
  elseif ($origenTipoMapa === 'cuestionario') $origenMapa = 'Encuesta: ' . ($certMapa['cuestionario_nombre'] ?? 'N/A');

  $mapasEncuestadores[$key]['puntos'][] = [
    'id' => (int)($certMapa['id'] ?? 0),
    'lat' => (float)$latRaw,
    'lng' => (float)$lngRaw,
    'fecha' => $fechaMapa,
    'encuestado' => trim((string)($certMapa['votante_nombre'] ?? '')),
    'origen' => $origenMapa,
    'audio_segundos' => (int)($certMapa['audio_duracion_segundos'] ?? 0)
  ];
}

$mapasEncuestadoresJson = json_encode(
  $mapasEncuestadores,
  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);

// KPIs visuales del centro de evidencias. Solo lectura.
$totalCertificaciones = is_array($certificaciones) ? count($certificaciones) : 0;
$totalEncuestadores = 0;
$encuestadoresUnicos = [];

if (is_array($certificaciones)) {
  foreach ($certificaciones as $certKpi) {
    $uid = intval($certKpi['tbl_usuario_id'] ?? 0);
    if ($uid > 0) {
      $encuestadoresUnicos[$uid] = true;
    }
  }
}
$totalEncuestadores = count($encuestadoresUnicos);

$optsEncuestadores = [];
if (is_array($certificaciones)) {
  foreach ($certificaciones as $cOpt) {
    $uidOpt = intval($cOpt['tbl_usuario_id'] ?? 0);
    if ($uidOpt <= 0) {
      continue;
    }
    $nombreOpt = trim(($cOpt['encuestador_nombre'] ?? '') . ' ' . ($cOpt['encuestador_apellido'] ?? ''));
    if ($nombreOpt === '') {
      $nombreOpt = 'Encuestador #' . $uidOpt;
    }
    $optsEncuestadores[$uidOpt] = $nombreOpt;
  }
  asort($optsEncuestadores, SORT_NATURAL | SORT_FLAG_CASE);
}

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
.ev-hero{position:relative;isolation:isolate;overflow:hidden;min-height:0;margin-bottom:16px;padding:30px;border:1px solid rgba(255,255,255,.12);border-radius:30px;color:#fff;background:radial-gradient(570px 280px at 9% 0%,rgba(75,140,247,.36),transparent 66%),radial-gradient(480px 270px at 94% 10%,rgba(29,182,219,.19),transparent 67%),linear-gradient(135deg,#173e7b 0%,#102a56 47%,#07162e 100%);box-shadow:0 30px 80px rgba(8,28,63,.24)}
.ev-hero:before{content:"";position:absolute;z-index:-1;width:440px;height:440px;right:-160px;top:-225px;border:1px solid rgba(255,255,255,.075);border-radius:50%;box-shadow:0 0 0 45px rgba(255,255,255,.021),0 0 0 92px rgba(255,255,255,.015),0 0 0 138px rgba(255,255,255,.010)}
.ev-hero-grid{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:28px;align-items:center}.ev-eyebrow{display:inline-flex;align-items:center;gap:8px;min-height:32px;margin-bottom:13px;padding:7px 11px;border:1px solid rgba(255,255,255,.14);border-radius:999px;color:rgba(255,255,255,.88);background:rgba(255,255,255,.075);backdrop-filter:blur(12px);font-size:.67rem;font-weight:800;letter-spacing:.62px;text-transform:uppercase}.ev-dot{width:7px;height:7px;border-radius:50%;background:#5de4a0;box-shadow:0 0 0 5px rgba(93,228,160,.11),0 0 16px rgba(93,228,160,.45)}
.ev-hero h1{margin:0;color:#fff;font-family:Manrope,Inter,sans-serif;font-size:clamp(1.9rem,3vw,3rem);line-height:1.04;font-weight:800;letter-spacing:-1.5px}.ev-hero h1 span{color:#b7d0ff}.ev-hero p{max-width:860px;margin:11px 0 0;color:rgba(255,255,255,.70);font-size:.91rem;line-height:1.67;font-weight:500}.ev-pills{display:flex;flex-wrap:wrap;gap:8px;margin-top:18px}.ev-pill{display:inline-flex;align-items:center;gap:7px;min-height:35px;padding:8px 11px;border:1px solid rgba(255,255,255,.10);border-radius:11px;color:rgba(255,255,255,.84);background:rgba(255,255,255,.07);font-size:.67rem;font-weight:700}.ev-pill i{color:#a7c7ff}
.ev-kpis{display:none}
.kpi-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin:0 0 16px;max-width:720px}
.kpi-dash{position:relative;overflow:hidden;min-height:118px;padding:18px;display:flex;align-items:center;justify-content:space-between;gap:14px;border:1px solid var(--ev-line);border-radius:20px;background:linear-gradient(145deg,#fff,#fbfcff);box-shadow:var(--ev-shadow-soft);transition:transform .22s ease,box-shadow .22s ease}
.kpi-dash:hover{transform:translateY(-3px);box-shadow:0 18px 40px rgba(15,23,42,.10)}
.kpi-dash::after{content:"";position:absolute;left:0;bottom:0;width:100%;height:3px;background:linear-gradient(90deg,#4f8cff,#13b8d8);transform:scaleX(.2);transform-origin:left;transition:transform .25s ease}
.kpi-dash:hover::after{transform:scaleX(1)}
.kpi-dash .kpi-label{font-size:.72rem;font-weight:800;color:var(--ev-muted);text-transform:uppercase;letter-spacing:.04em}
.kpi-dash .kpi-value{margin:4px 0 0;font:800 1.7rem/1.1 Manrope,Inter,sans-serif;color:var(--ev-text);letter-spacing:-.5px}
.kpi-dash .kpi-ico{width:48px;height:48px;flex:0 0 48px;display:flex;align-items:center;justify-content:center;border-radius:14px;color:#fff;font-size:1rem}
.filtros-card{margin-bottom:14px;border:1px solid var(--ev-line);border-radius:18px;background:#fff;box-shadow:var(--ev-shadow-soft)}
.filtros-card .card-body{padding:14px 16px}
.filtros-card .form-label{color:var(--ev-muted);font-weight:700}
.filtros-card .form-select,.filtros-card .form-control{min-height:38px;border-radius:10px;border-color:#d7dee9;font-size:.82rem}
.filtros-card .btn-primary{background:linear-gradient(135deg,#4b8cf7,#20427f);border:0;font-weight:800}
.filtros-card .btn-outline-secondary{font-weight:800}
.ev-summary{display:none}
.card-pro{overflow:hidden;margin-bottom:16px;border:1px solid var(--ev-line)!important;border-radius:24px!important;background:#fff!important;box-shadow:var(--ev-shadow)!important}.card-pro .card-header{min-height:74px;padding:15px 18px!important;border-bottom:1px solid #edf0f5!important;background:radial-gradient(320px 120px at 4% 0%,rgba(75,140,247,.06),transparent 72%),linear-gradient(180deg,#fff,#fbfcff)!important}.ev-card-title{display:flex;align-items:center;gap:11px}.ev-card-icon{width:42px;height:42px;flex:0 0 42px;display:flex;align-items:center;justify-content:center;border-radius:13px;color:var(--ev-brand);background:#edf4ff;font-size:.92rem}.title{margin:0;color:#182230;font:800 .98rem Manrope,Inter,sans-serif}.sub{margin-top:3px;color:var(--ev-soft);font-size:.63rem;font-weight:600}.ev-count{display:inline-flex;align-items:center;gap:6px;min-height:31px;padding:6px 10px;border:1px solid #dce8fa;border-radius:999px;color:#265ea9;background:#eef5ff;font-size:.64rem;font-weight:800}
.table-wrap{overflow:hidden;padding:12px;border:1px solid #e6ebf2;border-radius:18px;background:linear-gradient(180deg,#fff,#fbfcff)}#tblCertificaciones{width:100%!important;margin:0!important;border-collapse:separate!important;border-spacing:0 7px!important}#tblCertificaciones thead th{padding:9px 10px!important;border:0!important;color:#667085!important;background:transparent!important;font-size:.58rem!important;font-weight:800!important;letter-spacing:.38px;text-transform:uppercase;white-space:nowrap}#tblCertificaciones tbody td{padding:10px!important;border-top:1px solid #e9edf4!important;border-bottom:1px solid #e9edf4!important;color:#344054!important;background:#fff!important;font-size:.65rem!important;line-height:1.45;vertical-align:middle!important;transition:.18s ease}#tblCertificaciones tbody td:first-child{border-left:1px solid #e9edf4!important;border-radius:12px 0 0 12px}#tblCertificaciones tbody td:last-child{border-right:1px solid #e9edf4!important;border-radius:0 12px 12px 0}#tblCertificaciones tbody tr{transition:transform .18s ease}#tblCertificaciones tbody tr:hover{transform:translateY(-2px)}#tblCertificaciones tbody tr:hover td{border-color:#dce7f6!important;background:linear-gradient(90deg,#f6faff,#fff)!important;box-shadow:0 9px 23px rgba(15,23,42,.045)}
#tblCertificaciones .badge{display:inline-flex;align-items:center;gap:4px;min-height:27px;padding:5px 8px;border-radius:8px;font-size:.59rem;font-weight:800}#tblCertificaciones .badge.bg-primary{color:#175cd3!important;border:1px solid #d1e9ff;background:#eff8ff!important}#tblCertificaciones .badge.bg-info{color:#176b87!important;border:1px solid #cdedf5;background:#edf9fc!important}#tblCertificaciones .badge.bg-secondary,#tblCertificaciones .bg-secondary-subtle{color:#475467!important;border:1px solid #eaecf0;background:#f9fafb!important}#tblCertificaciones .badge.bg-success,#tblCertificaciones .bg-success-subtle{color:#06795b!important;border:1px solid #d1fae5;background:#ecfdf5!important}
.ev-action{min-height:34px;display:inline-flex;align-items:center;justify-content:center;gap:5px;margin:2px;padding:6px 9px!important;border-radius:9px!important;font-size:.60rem!important;font-weight:800!important;transition:.18s ease}.ev-action:hover{transform:translateY(-2px)}.ev-detail{border:0!important;color:#fff!important;background:linear-gradient(135deg,var(--ev-blue),var(--ev-brand))!important;box-shadow:0 7px 14px rgba(32,66,127,.16)}.ev-map{color:var(--ev-brand)!important;border:1px solid #ccddf3!important;background:#f8fbff!important}
.dataTables_wrapper{width:100%!important;color:#667085;font-size:.70rem}.dataTables_wrapper .row{margin-left:0!important;margin-right:0!important;align-items:center}.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{margin-bottom:12px}.dataTables_wrapper .dataTables_length label,.dataTables_wrapper .dataTables_filter label{color:#667085;font-size:.65rem;font-weight:700}.dataTables_wrapper .dataTables_filter input{min-height:38px;margin-left:8px;padding:0 11px;border:1px solid #d7dee9;border-radius:10px;background:#fff;outline:none}.dataTables_wrapper .dataTables_filter input:focus{border-color:var(--ev-blue);box-shadow:0 0 0 4px rgba(75,140,247,.10)}.dataTables_wrapper .dataTables_length select{min-height:37px;border:1px solid #d7dee9;border-radius:9px;background:#fff}.dataTables_wrapper .dataTables_info{color:#98a2b3!important;font-size:.63rem!important;font-weight:600}.dataTables_wrapper .dataTables_paginate .paginate_button{min-width:33px;height:33px;display:inline-flex!important;align-items:center;justify-content:center;padding:0 8px!important;border:1px solid transparent!important;border-radius:9px!important;color:#667085!important;background:transparent!important;font-size:.64rem;font-weight:800;box-shadow:none!important}.dataTables_wrapper .dataTables_paginate .paginate_button.current,.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{color:#fff!important;background:linear-gradient(135deg,var(--ev-blue),var(--ev-brand))!important;box-shadow:0 8px 18px rgba(32,66,127,.20)!important}
.modal-pro .modal-content{overflow:hidden;border:1px solid rgba(15,23,42,.09)!important;border-radius:24px!important;box-shadow:0 30px 82px rgba(15,23,42,.25)!important}.modal-pro .modal-header{position:relative;overflow:hidden;padding:18px 20px!important;border-bottom:0!important;color:#fff;background:radial-gradient(410px 190px at 5% 0%,rgba(75,140,247,.28),transparent 72%),radial-gradient(340px 170px at 100% 0%,rgba(29,182,219,.17),transparent 70%),linear-gradient(135deg,#173d79,#102a56 55%,#081b38)!important}.modal-pro .modal-header:after{content:"";position:absolute;width:190px;height:190px;right:-85px;top:-110px;border:1px solid rgba(255,255,255,.08);border-radius:50%;box-shadow:0 0 0 30px rgba(255,255,255,.02)}.modal-pro .modal-title{position:relative;z-index:2;color:#fff!important;font:800 1rem Manrope,Inter,sans-serif}.ev-modal-icon{position:relative;z-index:2;width:44px;height:44px;flex:0 0 44px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.18);border-radius:14px;color:#fff;background:rgba(255,255,255,.12);backdrop-filter:blur(10px)}.ev-modal-sub{position:relative;z-index:2;margin-top:3px;color:rgba(255,255,255,.63);font-size:.62rem;font-weight:600}.modal-pro .btn-close{position:relative;z-index:3;filter:invert(1) brightness(2);opacity:.9}.modal-pro .modal-body{padding:18px!important;background:linear-gradient(180deg,#fbfcfe,#f5f8fc)!important}.modal-pro .modal-footer{padding:12px 18px!important;border-top:1px solid #e7ebf1!important;background:#fff}
.kpi{min-height:82px;padding:12px 14px;border:1px solid #e4e9f1;border-radius:15px;background:#fff;box-shadow:0 8px 20px rgba(15,23,42,.04);transition:.18s ease}.kpi:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(15,23,42,.07)}.kpi .label{color:var(--ev-soft);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em}.kpi .value{margin-top:3px;color:var(--ev-text);font-size:14px;line-height:1.35;font-weight:800}
.map-box{position:relative;overflow:hidden;border:1px solid #d9e4f1;border-radius:18px;background:#fff;box-shadow:0 12px 30px rgba(15,23,42,.055);transition:transform .28s ease,box-shadow .28s ease,border-color .28s ease}.map-box:after{content:"";position:absolute;inset:0;pointer-events:none;border:1px solid rgba(75,140,247,0);border-radius:inherit;transition:.28s ease}.map-box:hover{transform:translateY(-4px) scale(1.004);border-color:#bfd4f0;box-shadow:0 22px 48px rgba(32,66,127,.14)}.map-box:hover:after{border-color:rgba(75,140,247,.40);box-shadow:inset 0 0 0 3px rgba(75,140,247,.06)}#mapCanvas{width:100%;height:360px;filter:saturate(.96) contrast(1.02);transition:filter .28s ease}.map-box:hover #mapCanvas{filter:saturate(1.12) contrast(1.04)}#modalDetalleCertificacionBody audio{width:100%;min-height:42px;border:1px solid #e0e7f0;border-radius:12px;background:#f8fafc}#modalDetalleCertificacionBody img{max-width:100%;border-radius:14px}#modalDetalleCertificacionBody .card{border:1px solid #e4e9f1!important;border-radius:17px!important;box-shadow:0 9px 22px rgba(15,23,42,.045)!important}
.btn-soft{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:8px 13px;border:1px solid #d7e2f2!important;border-radius:11px!important;color:var(--ev-brand)!important;background:#fff!important;font-size:.68rem;font-weight:800}


/* ===== MODAL DE AUDITORÍA: audio visible + preguntas limpias ===== */
#modalDetalleCertificacion .modal-dialog{width:min(1440px,96vw);max-width:1440px;margin-left:auto;margin-right:auto}
#modalDetalleCertificacion .modal-content{max-height:94vh}
#modalDetalleCertificacion .modal-body{position:relative;padding:16px!important;overflow-y:auto;scroll-behavior:smooth}
.ev-audio-dock{position:sticky;top:-16px;z-index:25;margin:-2px -2px 14px;padding:12px;border:1px solid #d7e5f5;border-radius:16px;background:radial-gradient(350px 110px at 0% 0%,rgba(75,140,247,.12),transparent 72%),rgba(255,255,255,.96);box-shadow:0 14px 34px rgba(15,23,42,.12);backdrop-filter:blur(16px)}
.ev-audio-dock-inner{display:grid;grid-template-columns:auto minmax(0,1fr);gap:12px;align-items:center}
.ev-audio-dock-icon{width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:13px;color:#fff;background:linear-gradient(135deg,#4b8cf7,#20427f);box-shadow:0 10px 20px rgba(32,66,127,.20)}
.ev-audio-dock-copy{min-width:0}.ev-audio-dock-copy strong{display:block;margin-bottom:2px;color:#172b4d;font-size:.72rem;font-weight:900}.ev-audio-dock-copy span{display:block;margin-bottom:7px;color:#7b8da8;font-size:.57rem;font-weight:650}.ev-audio-player-slot audio{width:100%!important;min-height:40px!important;display:block;border:0!important;border-radius:10px!important;background:#f2f6fb!important}
.ev-responses-title{display:flex!important;align-items:center;gap:8px;margin:16px 0 10px!important;color:#142b50!important;font-size:.78rem!important;font-weight:900!important}.ev-responses-title:before{content:"\\f46d";width:30px;height:30px;flex:0 0 30px;display:flex;align-items:center;justify-content:center;border-radius:9px;font-family:"Font Awesome 5 Free","Font Awesome 6 Free";font-weight:900;color:#285faf;background:#edf4ff}
#modalDetalleCertificacionBody .ev-question-card{position:relative;margin-bottom:10px!important;padding:13px 14px!important;border:1px solid #dce5f0!important;border-left:4px solid #4b8cf7!important;border-radius:14px!important;background:linear-gradient(135deg,#fff,#f8fbff)!important;box-shadow:0 8px 22px rgba(15,23,42,.055)!important;transition:.18s ease}
#modalDetalleCertificacionBody .ev-question-card:hover{transform:translateY(-1px);border-color:#bfd4ef!important;box-shadow:0 13px 28px rgba(15,23,42,.085)!important}
#modalDetalleCertificacionBody .ev-question-card .badge{min-height:24px;display:inline-flex;align-items:center;padding:4px 8px;border-radius:7px;font-size:.60rem;font-weight:800}
#modalDetalleCertificacionBody .ev-question-card strong{color:#101828;line-height:1.45}#modalDetalleCertificacionBody .ev-question-card p,#modalDetalleCertificacionBody .ev-question-card .text-muted{line-height:1.45}
#modalDetalleCertificacionBody .ev-answer-label{margin-top:7px;color:#7c8ca3!important;font-size:.58rem!important;font-weight:800!important;letter-spacing:.04em;text-transform:uppercase}

/* ===== Un solo botón de mapa por encuestador ===== */
.ev-map-group{color:#20427f!important;border:1px solid #c8dbf3!important;background:linear-gradient(180deg,#fff,#f4f8ff)!important;box-shadow:0 7px 14px rgba(32,66,127,.07)}.ev-map-group .ev-map-count{min-width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;border-radius:6px;color:#fff;background:#285faf;font-size:.53rem;font-weight:900}

/* ===== Modal mapa con todos los puntos ===== */
#modalMapaEncuestador .modal-dialog{width:min(1500px,96vw);max-width:1500px}#modalMapaEncuestador .modal-content{overflow:hidden;border:1px solid rgba(15,23,42,.10);border-radius:24px;box-shadow:0 30px 90px rgba(15,23,42,.28)}#modalMapaEncuestador .modal-header{position:relative;overflow:hidden;padding:17px 20px;border:0;color:#fff;background:radial-gradient(420px 190px at 5% 0%,rgba(75,140,247,.28),transparent 72%),linear-gradient(135deg,#173d79,#102a56 55%,#081b38)}
.ev-map-modal-title{display:flex;align-items:center;gap:11px}.ev-map-modal-icon{width:43px;height:43px;flex:0 0 43px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.16);border-radius:13px;background:rgba(255,255,255,.10)}.ev-map-modal-title h5{margin:0;color:#fff;font-size:.92rem;font-weight:900}.ev-map-modal-title p{margin:2px 0 0;color:rgba(255,255,255,.62);font-size:.58rem;font-weight:650}
.ev-map-modal-grid{display:grid;grid-template-columns:minmax(0,1fr) 330px;min-height:610px}#mapEncuestadorCanvas{width:100%;min-height:610px;background:#e9eef5}.ev-map-side{overflow-y:auto;max-height:610px;padding:13px;border-left:1px solid #e4eaf1;background:linear-gradient(180deg,#f9fbfe,#f4f7fb)}.ev-map-side-head{position:sticky;top:-13px;z-index:5;margin:-13px -13px 11px;padding:13px;border-bottom:1px solid #e7ecf3;background:rgba(249,251,254,.95);backdrop-filter:blur(10px)}.ev-map-side-head strong{display:block;color:#1d2939;font-size:.70rem;font-weight:900}.ev-map-side-head span{display:block;margin-top:2px;color:#98a2b3;font-size:.56rem;font-weight:650}
.ev-map-point{width:100%;display:grid;grid-template-columns:32px minmax(0,1fr);gap:9px;margin-bottom:8px;padding:9px;border:1px solid #e1e7ef;border-radius:12px;color:#344054;background:#fff;text-align:left;cursor:pointer;transition:.16s ease}.ev-map-point:hover{transform:translateY(-1px);border-color:#bdd2ec;box-shadow:0 9px 20px rgba(15,23,42,.06)}.ev-map-point-num{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:10px;color:#fff;background:linear-gradient(135deg,#4b8cf7,#20427f);font-size:.63rem;font-weight:900}.ev-map-point strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#253653;font-size:.63rem;font-weight:850}.ev-map-point span{display:block;margin-top:2px;color:#8a98ad;font-size:.54rem;line-height:1.35;font-weight:600}.ev-map-empty{padding:40px 20px;text-align:center;color:#98a2b3}.ev-map-empty i{display:block;margin-bottom:10px;color:#b0bfd2;font-size:2rem}
@media(max-width:991px){.ev-map-modal-grid{grid-template-columns:1fr;min-height:0}#mapEncuestadorCanvas{min-height:420px}.ev-map-side{max-height:280px;border-left:0;border-top:1px solid #e4eaf1}}
@media(max-width:767px){#modalDetalleCertificacion .modal-dialog,#modalMapaEncuestador .modal-dialog{width:100%;max-width:none;margin:0}#modalDetalleCertificacion .modal-content,#modalMapaEncuestador .modal-content{min-height:100vh;border-radius:0!important}.ev-audio-dock-inner{grid-template-columns:1fr}.ev-audio-dock-icon{display:none}#mapEncuestadorCanvas{min-height:360px}}

.ev-footer{margin-top:18px;padding:10px 12px;text-align:center;color:#98a2b3;font-size:.62rem;font-weight:650}
@media(max-width:1320px){.ev-hero-grid{grid-template-columns:1fr}.kpi-grid{max-width:100%}}@media(max-width:991px){.container-xxl-saas{padding-left:13px!important;padding-right:13px!important}.ev-hero{padding:23px}.ev-summary{grid-template-columns:repeat(2,1fr)}}@media(max-width:767px){.content{padding-top:12px!important}.container-xxl-saas{padding-left:10px!important;padding-right:10px!important}.ev-hero{min-height:0;padding:20px 17px;border-radius:22px}.ev-hero h1{font-size:1.8rem}.ev-hero p{font-size:.80rem}.kpi-grid{grid-template-columns:1fr 1fr}.card-pro{border-radius:19px!important}.card-pro .card-header{padding:14px!important}.card-pro .card-body{padding:12px!important}.table-wrap{padding:8px}#tblCertificaciones{min-width:980px}#mapCanvas{height:280px}.dataTables_wrapper .dataTables_filter input{width:100%;margin:6px 0 0}}@media(max-width:480px){.kpi-dash{min-height:100px;padding:14px}.kpi-dash .kpi-value{font-size:1.4rem}.ev-summary{grid-template-columns:1fr}}@media(prefers-reduced-motion:reduce){*,*:before,*:after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important;scroll-behavior:auto!important}}
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
        <div class="ev-eyebrow"><span class="ev-dot"></span>Estadística360 · Validación de encuestadores</div>
        <h1>Validación de <span>encuestadores</span></h1>
        <p>Revisa evidencias de campo (audio, GPS y respuestas). Marca Bien/Mal o valida con IA y confirma con Guardar.</p>
        <div class="ev-pills">
          <span class="ev-pill"><i class="fas fa-location-crosshairs"></i>Evidencia geográfica</span>
          <span class="ev-pill"><i class="fas fa-wave-square"></i>Soporte de audio</span>
          <span class="ev-pill"><i class="fas fa-robot"></i>Validación con IA</span>
        </div>
      </section>

      <div class="kpi-grid" aria-label="Indicadores principales">
        <div class="kpi-dash">
          <div>
            <div class="kpi-label">Total de encuestas</div>
            <p class="kpi-value" id="kpiTotalEncuestas"><?= (int)$totalCertificaciones ?></p>
          </div>
          <div class="kpi-ico" style="background:linear-gradient(135deg,#20427F,#132b52);"><i class="fas fa-clipboard-list"></i></div>
        </div>
        <div class="kpi-dash">
          <div>
            <div class="kpi-label">Total de encuestadores</div>
            <p class="kpi-value" id="kpiTotalEncuestadores"><?= (int)$totalEncuestadores ?></p>
          </div>
          <div class="kpi-ico" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);"><i class="fas fa-user-tie"></i></div>
        </div>
      </div>

      <div class="filtros-card card">
        <div class="card-body">
          <div class="row g-2 align-items-end">
            <div class="col-12 col-md-2">
              <label class="form-label small mb-1" for="filtro_estado">Estado</label>
              <select class="form-select form-select-sm" id="filtro_estado">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="bien">Bien</option>
                <option value="mal">Mal</option>
                <option value="en_revision">En revisión</option>
                <option value="anulada">Anulada</option>
              </select>
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label small mb-1" for="filtro_tipo">Tipo</label>
              <select class="form-select form-select-sm" id="filtro_tipo">
                <option value="">Todos</option>
                <option value="sondeo">Sondeo</option>
                <option value="cuestionario">Encuesta</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label small mb-1" for="filtro_item" id="filtro_item_label">Sondeo / Encuesta</label>
              <select class="form-select form-select-sm" id="filtro_item" disabled>
                <option value="">Selecciona un tipo primero…</option>
              </select>
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label small mb-1" for="filtro_encuestador">Encuestador</label>
              <select class="form-select form-select-sm" id="filtro_encuestador">
                <option value="">Todos</option>
                <?php foreach ($optsEncuestadores as $uidF => $nombreF): ?>
                  <option value="<?= (int)$uidF ?>"><?= h($nombreF) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6 col-md-2">
              <label class="form-label small mb-1" for="filtro_fecha_desde">Desde</label>
              <input type="date" class="form-control form-control-sm" id="filtro_fecha_desde">
            </div>
            <div class="col-6 col-md-2">
              <label class="form-label small mb-1" for="filtro_fecha_hasta">Hasta</label>
              <input type="date" class="form-control form-control-sm" id="filtro_fecha_hasta">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
              <button type="button" class="btn btn-sm btn-primary flex-fill" id="btn_aplicar_filtros_cert">
                <i class="fas fa-filter me-1"></i>Filtrar
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="btn_limpiar_filtros_cert" title="Limpiar">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <section class="card card-pro">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="ev-card-title">
              <div class="ev-card-icon"><i class="fas fa-shield-alt"></i></div>
              <div>
                <h3 class="title">A · Evidencias</h3>
                <div class="sub">Validaciones de campo con audio, GPS y estado de calidad.</div>
              </div>
            </div>
            <span class="ev-count" id="certCountLabel"><i class="fas fa-database"></i><?= (int)$totalCertificaciones ?> <?= $totalCertificaciones === 1 ? 'registro' : 'registros' ?></span>
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
                    <th>Estado</th>
                    <th>Encuestador</th>
                    <th>Encuestado</th>
                    <th>Origen</th>
                    <th>GPS</th>
                    <th>Audio</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                <?php $mapButtonShown = []; ?>
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
                    $icon  = 'fa-layer-group';
                    $texto = 'Sin tipo';
                    $sondeoIdRow = (int)($cert['tbl_sondeo_id'] ?? 0);
                    $fichaIdRow = (int)($cert['tbl_ficha_tecnica_encuesta_id'] ?? 0);

                    if ($origenTipo === 'sondeo') {
                      $badge = 'primary';
                      $icon = 'fa-poll';
                      $texto = 'Sondeo: ' . h($cert['sondeo_nombre'] ?? 'N/A');
                    } elseif ($origenTipo === 'cuestionario') {
                      $badge = 'info';
                      $icon = 'fa-clipboard-list';
                      $texto = 'Encuesta: ' . h($cert['cuestionario_nombre'] ?? 'N/A');
                    }

                    $hasGps = !empty($cert['latitud']) && !empty($cert['longitud']);
                    $hasAudio = !empty($cert['audio_duracion_segundos']);

                    $fechaIso = '';
                    if (!empty($cert['fecha_certificacion'])) {
                      try {
                        $fechaIso = (new DateTime($cert['fecha_certificacion']))->format('Y-m-d');
                      } catch (Exception $e) {
                        $fechaIso = substr((string)$cert['fecha_certificacion'], 0, 10);
                      }
                    }
                    $uidRow = (int)($cert['tbl_usuario_id'] ?? 0);
                    $origenRow = (string)(($origenTipo === 'sondeo' || $origenTipo === 'cuestionario') ? $origenTipo : '');
                    $estadoRow = (string)($cert['estado_revision'] ?? 'pendiente');
                    $estadoBadge = [
                      'pendiente' => 'secondary',
                      'bien' => 'success',
                      'mal' => 'danger',
                      'en_revision' => 'warning',
                      'anulada' => 'dark',
                    ][$estadoRow] ?? 'secondary';
                    $estadoLabel = [
                      'pendiente' => 'Pendiente',
                      'bien' => 'Bien',
                      'mal' => 'Mal',
                      'en_revision' => 'En revisión',
                      'anulada' => 'Anulada',
                    ][$estadoRow] ?? $estadoRow;
                  ?>
                  <tr data-origen="<?= h($origenRow) ?>" data-sondeo-id="<?= $sondeoIdRow ?>" data-ficha-id="<?= $fichaIdRow ?>" data-encuestador="<?= $uidRow ?>" data-fecha="<?= h($fechaIso) ?>" data-estado="<?= h($estadoRow) ?>">
                    <td><span class="fw-bold"><?= $id ?></span></td>
                    <td class="text-nowrap"><?= h($fechaTxt) ?></td>
                    <td class="text-nowrap"><span class="badge bg-<?= h($estadoBadge) ?>"><?= h($estadoLabel) ?></span></td>
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

                      <?php if ($showMapButton): ?>
                        <button
                            type="button"
                            class="btn ev-action ev-map-group"
                            title="Ver todos los puntos registrados por este encuestador"
                            onclick='E360_MAPAS.abrir(<?= json_encode($encuestadorMapKey, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                          <i class="fas fa-map-location-dot"></i>Mapa
                          <span class="ev-map-count"><?= count($puntosEncuestador) ?></span>
                        </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </section>

      <section class="card card-pro mt-3">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="ev-card-title">
              <div class="ev-card-icon"><i class="fas fa-clipboard-list"></i></div>
              <div>
                <h3 class="title">B · Encuestas / intentos vinculados</h3>
                <div class="sub">Cuestionarios (último intento) y sondeos asociados a cada evidencia.</div>
              </div>
            </div>
            <span class="ev-count"><i class="fas fa-link"></i><?= count($encuestasVinculadas) ?> vínculos</span>
          </div>
        </div>
        <div class="card-body p-3 p-lg-4">
          <div class="table-wrap">
            <div class="table-responsive">
              <table id="tblEncuestasVinculadas" class="table table-striped table-hover dt-compact align-middle">
                <thead>
                  <tr>
                    <th>Cert. ID</th>
                    <th>Tipo</th>
                    <th>Encuesta</th>
                    <th>Estado</th>
                    <th>Encuestador</th>
                    <th>Encuestado</th>
                    <th>Fecha</th>
                    <th>Intento</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($encuestasVinculadas as $ev): ?>
                  <?php
                    $estEv = (string)($ev['estado_revision'] ?? 'pendiente');
                    $badgeEv = [
                      'pendiente' => 'secondary', 'bien' => 'success', 'mal' => 'danger',
                      'en_revision' => 'warning', 'anulada' => 'dark',
                    ][$estEv] ?? 'secondary';
                  ?>
                  <tr>
                    <td><?= (int)($ev['certificacion_id'] ?? 0) ?></td>
                    <td><?= h($ev['tipo'] ?? '') ?></td>
                    <td><?= h($ev['encuesta_nombre'] ?? ('#' . ($ev['encuesta_id'] ?? ''))) ?></td>
                    <td><span class="badge bg-<?= h($badgeEv) ?>"><?= h($estEv) ?></span></td>
                    <td><?= h(trim(($ev['encuestador_nombre'] ?? '') . ' ' . ($ev['encuestador_apellido'] ?? ''))) ?></td>
                    <td><?= h($ev['votante_nombre'] ?? '') ?></td>
                    <td class="text-nowrap"><?= h($ev['fecha_certificacion'] ?? '') ?></td>
                    <td><?= $ev['intento_id'] ? (int)$ev['intento_id'] : '—' ?></td>
                    <td>
                      <button type="button" class="btn ev-action ev-detail"
                        onclick="CERTIFICACIONES.verDetalle(<?= (int)($ev['certificacion_id'] ?? 0) ?>)">
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
      </section>


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
  


  <!-- Modal mapa completo por encuestador -->
  <div class="modal fade" id="modalMapaEncuestador" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div class="ev-map-modal-title">
            <div class="ev-map-modal-icon"><i class="fas fa-map-location-dot"></i></div>
            <div>
              <h5 id="mapEncuestadorTitle">Mapa del encuestador</h5>
              <p id="mapEncuestadorSubtitle">Todos los puntos GPS registrados.</p>
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-0">
          <div class="ev-map-modal-grid">
            <div id="mapEncuestadorCanvas"><div class="ev-map-empty"><i class="fas fa-spinner fa-spin"></i>Preparando mapa...</div></div>
            <aside class="ev-map-side">
              <div class="ev-map-side-head"><strong id="mapEncuestadorCount">Puntos registrados</strong><span>Selecciona un punto para centrarlo en el mapa.</span></div>
              <div id="mapEncuestadorPoints"></div>
            </aside>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-soft" data-bs-dismiss="modal"><i class="fas fa-xmark"></i>Cerrar</button></div>
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
    window.E360_MAP_DATA = <?= $mapasEncuestadoresJson ?: '{}' ?>;
  </script>


  <script>
  window.E360_MAPAS=(function(){
    let activeKey=null,map=null,markers=[],infoWindow=null;
    const esc=v=>{const d=document.createElement('div');d.textContent=v==null?'':String(v);return d.innerHTML};
    const group=k=>(window.E360_MAP_DATA&&window.E360_MAP_DATA[k])?window.E360_MAP_DATA[k]:null;

    function list(g){
      const box=document.getElementById('mapEncuestadorPoints');
      const count=document.getElementById('mapEncuestadorCount');
      if(!box)return; box.innerHTML=''; const pts=Array.isArray(g?.puntos)?g.puntos:[];
      if(count) count.textContent=pts.length+(pts.length===1?' punto registrado':' puntos registrados');
      pts.forEach((p,i)=>{
        const b=document.createElement('button'); b.type='button'; b.className='ev-map-point';
        b.innerHTML='<span class="ev-map-point-num">'+(i+1)+'</span><span><strong>'+esc(p.encuestado||'Encuestado')+'</strong><span>'+esc(p.fecha||'')+' · Validación #'+esc(p.id)+'</span><span>'+esc(p.origen||'Registro')+'</span></span>';
        b.addEventListener('click',()=>{if(!map||!markers[i])return;map.panTo(markers[i].getPosition());map.setZoom(Math.max(map.getZoom()||14,16));google.maps.event.trigger(markers[i],'click')});
        box.appendChild(b);
      });
    }

    function render(k,tries=0){
      const g=group(k),canvas=document.getElementById('mapEncuestadorCanvas'); if(!g||!canvas)return;
      const pts=Array.isArray(g.puntos)?g.puntos:[];
      if(!pts.length){canvas.innerHTML='<div class="ev-map-empty"><i class="fas fa-map-marker-alt"></i>Sin puntos GPS.</div>';return}
      if(!window.google||!google.maps){if(tries<50){setTimeout(()=>render(k,tries+1),120);return}canvas.innerHTML='<div class="ev-map-empty"><i class="fas fa-triangle-exclamation"></i>No fue posible cargar Google Maps.</div>';return}
      canvas.innerHTML='';
      map=new google.maps.Map(canvas,{center:{lat:Number(pts[0].lat),lng:Number(pts[0].lng)},zoom:14,mapTypeControl:true,streetViewControl:false,fullscreenControl:true});
      infoWindow=new google.maps.InfoWindow(); markers=[]; const bounds=new google.maps.LatLngBounds();
      pts.forEach((p,i)=>{
        const pos={lat:Number(p.lat),lng:Number(p.lng)};
        const m=new google.maps.Marker({position:pos,map,title:'Validación #'+p.id,label:{text:String(i+1),color:'#fff',fontWeight:'800',fontSize:'11px'}}); markers.push(m); bounds.extend(pos);
        m.addListener('click',()=>{
          const aud=Number(p.audio_segundos||0)>0?'<div style="margin-top:5px;color:#68788e;font-size:11px"><b>Audio:</b> '+Number(p.audio_segundos)+' segundos</div>':'';
          infoWindow.setContent('<div style="min-width:220px;max-width:290px;font-family:Inter,Arial,sans-serif"><div style="font-size:13px;font-weight:800;color:#142b50;margin-bottom:6px">'+esc(p.encuestado||'Encuestado')+'</div><div style="font-size:11px;color:#68788e;line-height:1.45"><b>Validación:</b> #'+esc(p.id)+'<br><b>Fecha:</b> '+esc(p.fecha||'')+'<br><b>Origen:</b> '+esc(p.origen||'')+'</div>'+aud+'</div>');
          infoWindow.open(map,m);
        });
      });
      if(pts.length===1){map.setCenter(bounds.getCenter());map.setZoom(16)}else map.fitBounds(bounds,55);
      if(markers[0]) google.maps.event.trigger(markers[0],'click');
    }

    function abrir(k){
      const g=group(k);if(!g)return;activeKey=k;list(g);
      const t=document.getElementById('mapEncuestadorTitle'),s=document.getElementById('mapEncuestadorSubtitle');
      if(t)t.textContent=g.nombre||'Mapa del encuestador';
      const n=Array.isArray(g.puntos)?g.puntos.length:0;if(s)s.textContent='Trazabilidad territorial · '+n+(n===1?' punto GPS registrado':' puntos GPS registrados');
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMapaEncuestador')).show();
    }

    document.addEventListener('DOMContentLoaded',()=>{
      const el=document.getElementById('modalMapaEncuestador');if(!el)return;
      el.addEventListener('shown.bs.modal',()=>{if(activeKey)setTimeout(()=>render(activeKey),90)});
      el.addEventListener('hidden.bs.modal',()=>{map=null;markers=[];infoWindow=null});
    });
    return{abrir};
  })();
  </script>

  <script>
  (function(){
    let timer=null;
    const body=()=>document.getElementById('modalDetalleCertificacionBody');
    function card(el){
      const c=el?.closest('.card,.border,.rounded-3,.rounded,.p-3,.mb-3');
      if(c&&c.id!=='modalDetalleCertificacionBody'&&!c.classList.contains('ev-audio-dock'))c.classList.add('ev-question-card');
    }
    function enhance(){
      const b=body();if(!b)return;
      const audio=b.querySelector('audio');
      if(audio&&!audio.closest('.ev-audio-dock')){
        const dock=document.createElement('div');dock.className='ev-audio-dock';
        dock.innerHTML='<div class="ev-audio-dock-inner"><div class="ev-audio-dock-icon"><i class="fas fa-headphones"></i></div><div class="ev-audio-dock-copy"><strong>Audio de validación</strong><span>El reproductor permanece visible mientras revisas las respuestas.</span><div class="ev-audio-player-slot"></div></div></div>';
        b.insertBefore(dock,b.firstChild);dock.querySelector('.ev-audio-player-slot')?.appendChild(audio);
      }
      b.querySelectorAll('h1,h2,h3,h4,h5,h6,strong').forEach(e=>{const t=(e.textContent||'').trim();if(/respuestas?\s+del\s+cuestionario/i.test(t)||/^respuestas$/i.test(t))e.classList.add('ev-responses-title')});
      b.querySelectorAll('*').forEach(e=>{if(e.children.length>3)return;const t=(e.textContent||'').trim();if(/^respuesta\s*:/i.test(t)){e.classList.add('ev-answer-label');card(e)}});
      b.querySelectorAll('.badge').forEach(e=>{if(/^\d+$/.test((e.textContent||'').trim()))card(e)});
    }
    document.addEventListener('DOMContentLoaded',()=>{
      const b=body(),m=document.getElementById('modalDetalleCertificacion');if(!b)return;
      new MutationObserver(()=>{clearTimeout(timer);timer=setTimeout(enhance,55)}).observe(b,{childList:true,subtree:true});
      m?.addEventListener('shown.bs.modal',()=>setTimeout(enhance,90));
      m?.addEventListener('hidden.bs.modal',()=>b.querySelectorAll('audio').forEach(a=>{try{a.pause()}catch(_){}}));
    });
  })();
  </script>

  <!-- Stub antes del script async: evita callback perdido si Maps carga antes que certificaciones.js -->
  <script>
    window.initMap = function () { window.__GMAPS_READY = true; };
  </script>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= h($GOOGLE_MAPS_API_KEY) ?>&callback=initMap">
  </script>

  <script>
    window.CERT_PERMS = <?= json_encode([
      'revisar' => (bool)$permissions['revisar'],
      'validar_ia' => (bool)$permissions['validar_ia'],
      'revalidar' => (bool)$permissions['revalidar'],
      'historial' => (bool)$permissions['historial'],
    ], JSON_UNESCAPED_UNICODE) ?>;
    window.CERT_SONDEOS = <?= json_encode(array_map(function ($s) {
      return [
        'id' => (int)($s['id'] ?? 0),
        'label' => trim((string)($s['sondeo'] ?? ('Sondeo #' . ($s['id'] ?? '')))),
      ];
    }, $sondeosDisp), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.CERT_FICHAS = <?= json_encode(array_map(function ($f) {
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
  </script>
  <script type="text/javascript" src="admin/js/certificaciones.js?v=<?= rawurlencode((string)@filemtime(__DIR__ . '/admin/js/certificaciones.js')) ?>"></script>
</body>
</html>
