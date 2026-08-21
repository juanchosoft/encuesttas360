<?php
include './admin/include/head.php';
require_once 'admin/include/generic_classes.php';
include './admin/classes/Sondeo.php';
include './admin/classes/RespuestaSondeo.php';
include './admin/classes/FichaTecnicaEncuesta.php';

// Permisos
$viewSondeo      = SessionData::getPermission(90);
$viewCuestionario = SessionData::getPermission(74);

// Datos para Sondeos
$sondeos = [];
if ($viewSondeo) {
  $res = RespuestaSondeo::getSondeosDisponibles([]);
  $sondeos = $res['output']['response'] ?? [];
}

// Datos para Cuestionarios
$fichasTecnicas = [];
if ($viewCuestionario) {
  $res2 = FichaTecnicaEncuesta::getAll([]);
  $fichasTecnicas = $res2['output']['response'] ?? [];
}

$modulo = 'Dashboard de Resultados';
$totalSondeosDisponibles = count($sondeos);
$totalCuestionariosDisponibles = count($fichasTecnicas);
$totalModulosDisponibles = ($viewSondeo ? 1 : 0) + ($viewCuestionario ? 1 : 0);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">


  <style>
    :root{
      --s360-navy:#0b1730;
      --s360-navy-2:#10264d;
      --s360-brand:#20427F;
      --s360-brand-2:#3168c8;
      --s360-electric:#4f8cff;
      --s360-cyan:#13b8d8;
      --s360-mint:#16b981;
      --s360-violet:#7c5cff;
      --s360-amber:#f59e0b;
      --s360-rose:#ef5d8f;
      --s360-page:#f3f6fb;
      --s360-surface:#ffffff;
      --s360-surface-2:#f8faff;
      --s360-text:#101828;
      --s360-text-2:#344054;
      --s360-muted:#667085;
      --s360-soft:#98a2b3;
      --s360-line:#e7ebf2;
      --s360-line-2:rgba(15,23,42,.08);
      --s360-radius-xxl:30px;
      --s360-radius-xl:24px;
      --s360-radius-lg:18px;
      --s360-radius-md:14px;
      --s360-shadow:0 22px 60px rgba(15,23,42,.10);
      --s360-shadow-soft:0 12px 32px rgba(15,23,42,.075);
      --s360-shadow-hover:0 30px 75px rgba(15,23,42,.15);
    }

    *{ box-sizing:border-box; }

    html{ scroll-behavior:smooth; }

    body{
      margin:0;
      color:var(--s360-text);
      background:
        radial-gradient(900px 500px at 4% -5%, rgba(49,104,200,.12), transparent 64%),
        radial-gradient(760px 460px at 105% 8%, rgba(19,184,216,.09), transparent 62%),
        linear-gradient(180deg,#f7f9fd 0,#f2f5fa 100%);
      font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased;
      text-rendering:optimizeLegibility;
    }

    body::before{
      content:"";
      position:fixed;
      inset:0;
      pointer-events:none;
      opacity:.35;
      background-image:
        linear-gradient(rgba(32,66,127,.022) 1px, transparent 1px),
        linear-gradient(90deg, rgba(32,66,127,.022) 1px, transparent 1px);
      background-size:36px 36px;
      mask-image:linear-gradient(to bottom,rgba(0,0,0,.8),transparent 80%);
      z-index:-1;
    }

    .content{
      box-sizing:border-box;
      overflow-x:hidden;
      padding-left:2.2% !important;
      padding-right:2.2% !important;
      padding-top:20px !important;
    }

    .content .container-fluid{
      width:100% !important;
      max-width:1680px !important;
      margin:0 auto !important;
      padding:0 !important;
      box-sizing:border-box;
    }

    /* =========================
       HERO / COMMAND CENTER
       ========================= */
    .dash-hero{
      position:relative;
      isolation:isolate;
      overflow:hidden;
      min-height:218px;
      padding:28px 30px;
      margin-bottom:18px;
      border-radius:var(--s360-radius-xxl);
      border:1px solid rgba(255,255,255,.12);
      color:#fff;
      background:
        radial-gradient(520px 260px at 12% 2%, rgba(79,140,255,.35), transparent 66%),
        radial-gradient(460px 260px at 92% 10%, rgba(19,184,216,.22), transparent 65%),
        linear-gradient(135deg,#173d79 0%,#102a56 42%,#09172f 100%);
      box-shadow:0 28px 75px rgba(12,31,66,.26);
    }

    .dash-hero::before{
      content:"";
      position:absolute;
      width:420px;
      height:420px;
      border:1px solid rgba(255,255,255,.08);
      border-radius:50%;
      right:-145px;
      top:-210px;
      box-shadow:
        0 0 0 42px rgba(255,255,255,.022),
        0 0 0 88px rgba(255,255,255,.017),
        0 0 0 132px rgba(255,255,255,.012);
      z-index:-1;
    }

    .dash-hero::after{
      content:"";
      position:absolute;
      inset:auto auto -90px 37%;
      width:340px;
      height:180px;
      background:radial-gradient(circle,rgba(79,140,255,.24),transparent 68%);
      filter:blur(10px);
      z-index:-1;
    }

    .hero-grid{
      display:grid;
      grid-template-columns:minmax(0,1fr) auto;
      gap:30px;
      align-items:center;
    }

    .hero-eyebrow{
      display:inline-flex;
      align-items:center;
      gap:9px;
      padding:8px 12px;
      margin-bottom:14px;
      border:1px solid rgba(255,255,255,.15);
      border-radius:999px;
      color:rgba(255,255,255,.90);
      background:rgba(255,255,255,.08);
      backdrop-filter:blur(12px);
      font-size:.73rem;
      font-weight:800;
      letter-spacing:.65px;
      text-transform:uppercase;
    }

    .hero-live{
      width:8px;
      height:8px;
      border-radius:50%;
      background:#63f0a6;
      box-shadow:0 0 0 5px rgba(99,240,166,.12),0 0 18px rgba(99,240,166,.62);
      animation:s360Live 2s ease-in-out infinite;
    }

    @keyframes s360Live{
      0%,100%{transform:scale(1);opacity:1}
      50%{transform:scale(.72);opacity:.6}
    }

    .dash-hero h2{
      margin:0;
      max-width:800px;
      color:#fff;
      font-family:"Manrope","Inter",sans-serif;
      font-size:clamp(1.8rem,3vw,3rem);
      line-height:1.05;
      font-weight:800;
      letter-spacing:-1.4px;
    }

    .dash-hero h2 span{
      color:#a9c7ff;
    }

    .dash-hero p{
      max-width:760px;
      margin:10px 0 0;
      color:rgba(255,255,255,.70);
      font-size:.94rem;
      line-height:1.65;
      font-weight:500;
    }

    .hero-pills{
      display:flex;
      gap:9px;
      flex-wrap:wrap;
      margin-top:20px;
    }

    .hero-pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      min-height:36px;
      padding:8px 12px;
      border-radius:11px;
      color:rgba(255,255,255,.85);
      background:rgba(255,255,255,.075);
      border:1px solid rgba(255,255,255,.11);
      font-size:.73rem;
      font-weight:700;
      backdrop-filter:blur(8px);
    }

    .hero-pill i{ color:#9fc2ff; }

    .hero-metrics{
      display:grid;
      grid-template-columns:repeat(3,minmax(105px,1fr));
      gap:10px;
      min-width:390px;
    }

    .hero-metric{
      position:relative;
      overflow:hidden;
      min-height:118px;
      padding:15px;
      border:1px solid rgba(255,255,255,.13);
      border-radius:18px;
      background:linear-gradient(145deg,rgba(255,255,255,.12),rgba(255,255,255,.055));
      backdrop-filter:blur(14px);
      transition:transform .25s ease,background .25s ease,border-color .25s ease;
    }

    .hero-metric:hover{
      transform:translateY(-4px);
      background:linear-gradient(145deg,rgba(255,255,255,.17),rgba(255,255,255,.07));
      border-color:rgba(255,255,255,.20);
    }

    .hero-metric-icon{
      width:34px;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:center;
      margin-bottom:14px;
      border-radius:11px;
      color:#d7e6ff;
      background:rgba(255,255,255,.10);
      font-size:.9rem;
    }

    .hero-metric-value{
      font-family:"Manrope","Inter",sans-serif;
      color:#fff;
      font-size:1.45rem;
      line-height:1;
      font-weight:800;
      letter-spacing:-.6px;
    }

    .hero-metric-label{
      margin-top:5px;
      color:rgba(255,255,255,.62);
      font-size:.68rem;
      font-weight:700;
      line-height:1.25;
    }

    /* =========================
       CHIPS / BADGES
       ========================= */
    .chip{
      display:inline-flex;
      align-items:center;
      gap:7px;
      min-height:31px;
      padding:6px 10px;
      border-radius:999px;
      color:#1f4f9c;
      background:#eef5ff;
      border:1px solid #dceafe;
      font-size:.68rem;
      font-weight:800;
      letter-spacing:.1px;
      white-space:nowrap;
    }

    .chip i{font-size:.72rem}

    .chip.success{
      color:#08795b;
      background:#ecfdf5;
      border-color:#d1fae5;
    }

    .chip.dark{
      color:#344054;
      background:#f6f7f9;
      border-color:#eaecf0;
    }

    /* =========================
       SELECTOR
       ========================= */
    .sel-bar{
      position:relative;
      overflow:hidden;
      padding:18px;
      margin-bottom:18px;
      border:1px solid var(--s360-line);
      border-radius:var(--s360-radius-xl);
      background:rgba(255,255,255,.93);
      box-shadow:var(--s360-shadow-soft);
      backdrop-filter:blur(14px);
    }

    .sel-bar::before{
      content:"";
      position:absolute;
      top:0;
      left:0;
      width:5px;
      height:100%;
      background:linear-gradient(180deg,var(--s360-electric),var(--s360-cyan));
    }

    .selector-topline{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin:0 0 13px 3px;
    }

    .selector-title{
      display:flex;
      align-items:center;
      gap:10px;
      color:var(--s360-text);
      font-size:.82rem;
      font-weight:800;
    }

    .selector-title-icon{
      width:34px;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:11px;
      color:#fff;
      background:linear-gradient(135deg,var(--s360-electric),var(--s360-brand));
      box-shadow:0 8px 18px rgba(32,66,127,.22);
    }

    .selector-help{
      color:var(--s360-soft);
      font-size:.7rem;
      font-weight:600;
    }

    .form-floating>.form-select{
      height:58px;
      min-height:58px;
      border:1px solid #d8dee9;
      border-radius:14px;
      color:var(--s360-text-2);
      background-color:#fbfcfe;
      font-size:.82rem;
      font-weight:700;
      box-shadow:none;
      transition:border-color .2s ease,box-shadow .2s ease,background .2s ease,transform .2s ease;
    }

    .form-floating>.form-select:hover{
      border-color:#bdc9dd;
      background:#fff;
    }

    .form-floating>.form-select:focus{
      border-color:#4f8cff;
      box-shadow:0 0 0 4px rgba(79,140,255,.11);
      background:#fff;
    }

    .form-floating>label{
      color:#667085;
      font-size:.78rem;
      font-weight:700;
    }

    .btn-brand{
      position:relative;
      overflow:hidden;
      min-height:58px;
      border:0;
      border-radius:14px;
      color:#fff !important;
      background:linear-gradient(135deg,#4f8cff 0%,#3168c8 48%,#20427F 100%);
      box-shadow:0 13px 28px rgba(32,66,127,.24);
      font-size:.82rem;
      font-weight:800;
      transition:transform .2s ease,box-shadow .2s ease,filter .2s ease;
    }

    .btn-brand::after{
      content:"";
      position:absolute;
      top:-100%;
      left:-40%;
      width:40%;
      height:300%;
      transform:rotate(24deg);
      background:linear-gradient(90deg,transparent,rgba(255,255,255,.28),transparent);
      transition:left .55s ease;
    }

    .btn-brand:hover{
      transform:translateY(-2px);
      box-shadow:0 18px 34px rgba(32,66,127,.31);
      filter:saturate(1.06);
    }

    .btn-brand:hover::after{ left:120%; }

    .btn-outline-brand{
      border-radius:12px;
      border:1px solid #cfdbed;
      background:#fff;
      color:var(--s360-brand);
      font-weight:800;
    }

    /* =========================
       GENERIC CARDS
       ========================= */
    .r-card,
    .table-wrapper,
    .progress-wrapper{
      position:relative;
      border:1px solid var(--s360-line);
      background:rgba(255,255,255,.96);
      box-shadow:var(--s360-shadow-soft);
    }

    .r-card{
      overflow:hidden;
      border-radius:var(--s360-radius-xl);
      transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;
    }

    .r-card:hover{
      border-color:#d9e3f2;
      box-shadow:0 18px 48px rgba(15,23,42,.10);
    }

    .r-card-header{
      min-height:69px;
      padding:15px 18px;
      border-bottom:1px solid #edf0f5;
      background:
        radial-gradient(300px 100px at 6% 0%,rgba(79,140,255,.055),transparent 70%),
        linear-gradient(180deg,#ffffff,#fbfcff);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
    }

    .r-card-header h5{
      display:flex;
      align-items:center;
      margin:0;
      color:#182230;
      font-family:"Manrope","Inter",sans-serif;
      font-size:.94rem;
      font-weight:800;
      letter-spacing:-.2px;
    }

    .r-card-header h5 i{
      width:34px;
      height:34px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      margin-right:9px !important;
      border-radius:10px;
      color:var(--s360-brand);
      background:#edf4ff;
      font-size:.82rem;
    }

    .r-card-body{ padding:18px; }

    /* =========================
       TERRITORIAL MAP PANEL
       ========================= */
    #panel-territorio{
      overflow:visible;
      border:0;
      background:transparent;
      box-shadow:none;
    }

    #panel-territorio .r-card-header{
      border:1px solid var(--s360-line);
      border-bottom:0;
      border-radius:var(--s360-radius-xl) var(--s360-radius-xl) 0 0;
      background:#fff;
      box-shadow:0 8px 24px rgba(15,23,42,.045);
    }

    .map-title-wrap{
      display:flex;
      align-items:center;
      gap:12px;
    }

    .map-title-icon{
      position:relative;
      width:45px;
      height:45px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:14px;
      color:#fff;
      background:linear-gradient(135deg,var(--s360-electric),var(--s360-brand));
      box-shadow:0 10px 24px rgba(32,66,127,.22);
    }

    .map-title-icon::after{
      content:"";
      position:absolute;
      inset:-5px;
      border:1px solid rgba(79,140,255,.20);
      border-radius:18px;
      animation:s360MapPulse 2.6s ease-in-out infinite;
    }

    @keyframes s360MapPulse{
      0%,100%{transform:scale(.96);opacity:.4}
      50%{transform:scale(1.08);opacity:.08}
    }

    .map-title-copy h5{
      margin:0;
      font-size:1rem;
    }

    .map-title-copy p{
      margin:3px 0 0;
      color:var(--s360-soft);
      font-size:.69rem;
      font-weight:600;
    }

    .map-tools{
      display:flex;
      align-items:center;
      gap:8px;
      flex-wrap:wrap;
    }

    .map-shell{
      position:relative;
      overflow:hidden;
      border:1px solid var(--s360-line);
      border-radius:0 0 var(--s360-radius-xxl) var(--s360-radius-xxl);
      background:
        radial-gradient(540px 240px at 50% 0%,rgba(79,140,255,.07),transparent 70%),
        #f8fafe;
      box-shadow:var(--s360-shadow);
    }

    .map-shell::before{
      content:"";
      position:absolute;
      inset:0;
      pointer-events:none;
      background:
        linear-gradient(rgba(32,66,127,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(32,66,127,.025) 1px,transparent 1px);
      background-size:28px 28px;
      opacity:.55;
      z-index:0;
    }

    .map-iframe-wrap{
      position:relative;
      z-index:1;
      padding:10px;
    }

    #dashTerritorioFrame{
      display:block;
      width:100%;
      min-height:110vh;
      border:0;
      border-radius:20px;
      background:#fff;
      box-shadow:inset 0 0 0 1px rgba(15,23,42,.045);
    }

    .map-floating-tip{
      position:absolute;
      left:24px;
      bottom:24px;
      z-index:3;
      display:flex;
      align-items:center;
      gap:9px;
      padding:10px 13px;
      border:1px solid rgba(255,255,255,.85);
      border-radius:12px;
      color:#344054;
      background:rgba(255,255,255,.88);
      box-shadow:0 12px 28px rgba(15,23,42,.12);
      backdrop-filter:blur(16px);
      font-size:.7rem;
      font-weight:800;
      pointer-events:none;
    }

    .map-floating-tip i{
      color:#3168c8;
      animation:s360Pointer 1.7s ease-in-out infinite;
    }

    @keyframes s360Pointer{
      0%,100%{transform:translateY(0)}
      50%{transform:translateY(-3px)}
    }

    /* =========================
       KPI CARDS
       ========================= */
    .kpi-grid{
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:14px;
      margin-bottom:18px;
    }

    .kpi{
      position:relative;
      overflow:hidden;
      min-height:126px;
      padding:18px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      border:1px solid var(--s360-line);
      border-radius:20px;
      background:linear-gradient(145deg,#fff,#fbfcff);
      box-shadow:var(--s360-shadow-soft);
      transition:transform .25s cubic-bezier(.2,.75,.25,1),box-shadow .25s ease,border-color .25s ease;
    }

    .kpi::before{
      content:"";
      position:absolute;
      width:110px;
      height:110px;
      right:-54px;
      top:-55px;
      border-radius:50%;
      background:radial-gradient(circle,rgba(79,140,255,.10),transparent 70%);
    }

    .kpi::after{
      content:"";
      position:absolute;
      left:0;
      bottom:0;
      width:100%;
      height:3px;
      opacity:.75;
      background:linear-gradient(90deg,#4f8cff,#13b8d8);
      transform:scaleX(.18);
      transform-origin:left;
      transition:transform .28s ease;
    }

    .kpi:hover{
      transform:translateY(-5px);
      border-color:#d6e2f3;
      box-shadow:var(--s360-shadow-hover);
    }

    .kpi:hover::after{ transform:scaleX(1); }

    .kpi-label{
      margin-bottom:7px;
      color:var(--s360-muted);
      font-size:.72rem;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.45px;
    }

    .kpi-value{
      margin:0;
      color:#101828;
      font-family:"Manrope","Inter",sans-serif;
      font-size:1.85rem;
      line-height:1;
      font-weight:800;
      letter-spacing:-.9px;
    }

    .kpi-ico{
      position:relative;
      z-index:1;
      width:50px;
      height:50px;
      flex:0 0 50px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:15px;
      color:#fff;
      font-size:1.05rem;
      box-shadow:0 12px 24px rgba(15,23,42,.15);
      transition:transform .25s ease;
    }

    .kpi:hover .kpi-ico{
      transform:rotate(-5deg) scale(1.06);
    }

    .metric-changed{ animation:s360Metric .55s ease; }

    @keyframes s360Metric{
      0%{transform:scale(1)}
      42%{transform:scale(1.11);color:#3168c8}
      100%{transform:scale(1)}
    }

    /* =========================
       CHARTS
       ========================= */
    .chart-box{
      position:relative;
      overflow:hidden;
      min-height:300px;
      padding:16px;
      border:1px solid #e9edf4;
      border-radius:18px;
      background:
        radial-gradient(360px 150px at 10% 0%,rgba(79,140,255,.055),transparent 72%),
        linear-gradient(180deg,#fbfcff,#f8faff);
    }

    .chart-box::after{
      content:"";
      position:absolute;
      width:100px;
      height:100px;
      right:-44px;
      top:-44px;
      border:1px solid rgba(49,104,200,.07);
      border-radius:50%;
      pointer-events:none;
    }

    .chart-box > div{
      position:relative;
      z-index:1;
      min-height:270px;
    }

    .apexcharts-canvas,
    .apexcharts-svg{
      font-family:"Inter",sans-serif !important;
    }

    .apexcharts-tooltip{
      border:0 !important;
      border-radius:12px !important;
      box-shadow:0 14px 35px rgba(15,23,42,.14) !important;
      overflow:hidden;
    }

    .apexcharts-legend-text{
      color:#475467 !important;
      font-weight:600 !important;
    }

    /* =========================
       FILTERS
       ========================= */
    #filtros_listados_card,
    #filtros_listados_card_t2{
      border:1px solid #e6ebf3 !important;
      background:
        linear-gradient(135deg,#f8faff,#f2f6fc) !important;
      box-shadow:inset 0 1px 0 rgba(255,255,255,.92) !important;
    }

    #filtros_listados_card .r-card-body,
    #filtros_listados_card_t2 .r-card-body{
      padding:14px !important;
    }

    .form-label{
      color:#475467;
      font-size:.66rem !important;
      font-weight:800 !important;
      text-transform:uppercase;
      letter-spacing:.35px;
    }

    .form-select,
    .form-control{
      min-height:40px;
      border:1px solid #d6dce7;
      border-radius:11px !important;
      color:#344054;
      background-color:#fff;
      font-size:.76rem !important;
      font-weight:600;
      box-shadow:none !important;
    }

    .form-select:focus,
    .form-control:focus{
      border-color:#4f8cff;
      box-shadow:0 0 0 4px rgba(79,140,255,.10) !important;
    }

    .btn-sm.btn-primary,
    .btn-aplicar-filtros-listados{
      min-height:38px;
      border:0;
      border-radius:10px;
      background:linear-gradient(135deg,#4f8cff,#20427F);
      box-shadow:0 8px 18px rgba(32,66,127,.18);
      font-size:.72rem;
      font-weight:800;
    }

    .btn-sm.btn-outline-secondary,
    .btn-limpiar-filtros-listados{
      min-width:40px;
      min-height:38px;
      border:1px solid #d5dbe6;
      border-radius:10px;
      color:#667085;
      background:#fff;
    }

    /* =========================
       TABLES / DATATABLES
       ========================= */
    .table-wrapper{
      width:100%;
      max-width:100%;
      margin-bottom:16px;
      padding:18px;
      overflow:hidden;
      border-radius:var(--s360-radius-xl);
    }

    .table-wrapper > h5{
      display:flex;
      align-items:center;
      gap:9px;
      margin:0 0 15px !important;
      color:#182230;
      font-family:"Manrope","Inter",sans-serif;
      font-size:.96rem;
      font-weight:800;
    }

    .table-wrapper > h5 i{
      width:34px;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:10px;
      color:#3168c8;
      background:#edf4ff;
      font-size:.8rem;
    }

    .table-responsive{
      width:100%;
      max-width:100%;
      overflow-x:auto;
      border-radius:15px;
      -webkit-overflow-scrolling:touch;
    }

    .table-wrapper table{
      width:100% !important;
      max-width:100% !important;
      margin:0 !important;
      border-collapse:separate !important;
      border-spacing:0 !important;
      table-layout:auto;
    }

    .table-wrapper table thead th{
      position:relative;
      padding:12px 13px !important;
      border-top:1px solid #e9edf4 !important;
      border-bottom:1px solid #e4e9f1 !important;
      color:#475467 !important;
      background:linear-gradient(180deg,#f8faff,#f3f6fb) !important;
      font-size:.63rem !important;
      font-weight:800 !important;
      text-transform:uppercase;
      letter-spacing:.45px;
      white-space:nowrap !important;
      vertical-align:middle;
    }

    .table-wrapper table thead th:first-child{
      border-left:1px solid #e9edf4 !important;
      border-radius:12px 0 0 12px;
    }

    .table-wrapper table thead th:last-child{
      border-right:1px solid #e9edf4 !important;
      border-radius:0 12px 12px 0;
    }

    .table-wrapper table tbody td{
      padding:12px 13px !important;
      border-top:0 !important;
      border-bottom:1px solid #edf0f5 !important;
      color:#344054 !important;
      background:#fff !important;
      font-size:.72rem !important;
      line-height:1.45;
      font-weight:600;
      vertical-align:middle !important;
      white-space:normal;
      word-break:normal;
      overflow-wrap:anywhere;
      transition:background .18s ease,color .18s ease,box-shadow .18s ease;
    }

    .table-wrapper table tbody tr:last-child td{
      border-bottom:0 !important;
    }

    .table-wrapper table tbody tr:hover td{
      color:#1d2939 !important;
      background:#f6f9ff !important;
      box-shadow:inset 0 1px 0 #e3ecfa,inset 0 -1px 0 #e3ecfa;
    }

    .table-wrapper table tbody tr:hover td:first-child{
      box-shadow:inset 3px 0 0 #4f8cff,inset 0 1px 0 #e3ecfa,inset 0 -1px 0 #e3ecfa;
    }

    .table-wrapper table .btn{
      border-radius:9px !important;
      font-size:.67rem !important;
      font-weight:800 !important;
      transition:transform .18s ease,box-shadow .18s ease;
    }

    .table-wrapper table .btn:hover{
      transform:translateY(-1px);
      box-shadow:0 7px 15px rgba(15,23,42,.12);
    }

    .dataTables_wrapper{
      width:100% !important;
      max-width:100%;
      color:#667085;
      font-size:.72rem;
    }

    .dataTables_wrapper .row{
      width:100%;
      margin-left:0 !important;
      margin-right:0 !important;
      align-items:center;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter{
      margin-bottom:13px;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label{
      color:#667085;
      font-size:.68rem;
      font-weight:700;
    }

    .dataTables_wrapper .dataTables_length select{
      min-width:72px;
      min-height:38px;
      margin:0 5px;
      border:1px solid #d7dde8;
      border-radius:10px;
      background:#fff;
      color:#344054;
      font-size:.72rem;
      font-weight:700;
    }

    .dataTables_wrapper .dataTables_filter input{
      width:min(270px,100%);
      min-height:39px;
      margin-left:8px;
      padding:0 12px;
      border:1px solid #d7dde8;
      border-radius:11px;
      background:#fff;
      color:#344054;
      outline:none;
      font-size:.73rem;
      transition:border-color .2s ease,box-shadow .2s ease;
    }

    .dataTables_wrapper .dataTables_filter input:focus{
      border-color:#4f8cff;
      box-shadow:0 0 0 4px rgba(79,140,255,.10);
    }

    .dataTables_wrapper .dataTables_info{
      padding-top:14px !important;
      color:#98a2b3 !important;
      font-size:.66rem !important;
      font-weight:600;
    }

    .dataTables_wrapper .dataTables_paginate{
      display:flex;
      justify-content:flex-end;
      gap:4px;
      padding-top:10px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button{
      min-width:34px;
      height:34px;
      display:inline-flex !important;
      align-items:center;
      justify-content:center;
      margin:0 2px !important;
      padding:0 9px !important;
      border:1px solid transparent !important;
      border-radius:9px !important;
      color:#667085 !important;
      background:transparent !important;
      font-size:.68rem;
      font-weight:800;
      box-shadow:none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
      border-color:#dce8fa !important;
      color:#3168c8 !important;
      background:#eff5ff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
      border-color:transparent !important;
      color:#fff !important;
      background:linear-gradient(135deg,#4f8cff,#20427F) !important;
      box-shadow:0 8px 18px rgba(32,66,127,.20) !important;
    }

    /* =========================
       PARTICIPATION TABS
       ========================= */
    #votantesTabs{
      gap:7px;
      padding:6px;
      border:1px solid #e4e9f2;
      border-radius:14px;
      background:#f7f9fd;
    }

    #votantesTabs .nav-item{ flex:1; }

    #votantesTabs .nav-link{
      width:100%;
      min-height:42px;
      border:0 !important;
      border-radius:10px !important;
      color:#667085;
      font-size:.74rem;
      font-weight:800;
      transition:background .2s ease,color .2s ease,transform .2s ease,box-shadow .2s ease;
    }

    #votantesTabs .nav-link:hover{
      color:#20427F;
      background:#eef4ff;
      transform:translateY(-1px);
    }

    #votantesTabs .nav-link.active{
      color:#fff;
      background:linear-gradient(135deg,#4f8cff,#20427F);
      box-shadow:0 9px 18px rgba(32,66,127,.18);
    }

    /* =========================
       EMPTY STATES
       ========================= */
    .empty-zone{
      position:relative;
      overflow:hidden;
      min-height:270px;
      padding:54px 24px;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      text-align:center;
      color:#667085;
      font-size:.82rem;
      font-weight:700;
    }

    .empty-zone::before{
      content:"";
      position:absolute;
      width:220px;
      height:220px;
      border-radius:50%;
      background:radial-gradient(circle,rgba(79,140,255,.085),transparent 67%);
      pointer-events:none;
    }

    .empty-zone i{
      position:relative;
      width:66px;
      height:66px;
      display:flex;
      align-items:center;
      justify-content:center;
      margin-bottom:16px;
      border:1px solid #dfe9f8;
      border-radius:20px;
      color:#3168c8;
      background:linear-gradient(145deg,#f2f7ff,#fff);
      box-shadow:0 12px 28px rgba(32,66,127,.09);
      font-size:1.45rem;
      opacity:1;
    }

    /* legacy classes retained */
    .stats-card{
      border-radius:18px;
      padding:20px;
      color:#fff;
      box-shadow:var(--s360-shadow-soft);
      margin-bottom:14px;
    }
    .stats-card.blue{background:linear-gradient(135deg,#4f8cff,#20427F)}
    .stats-card.green{background:linear-gradient(135deg,#16b981,#087f5b)}
    .stats-card.orange{background:linear-gradient(135deg,#f59e0b,#d97706)}
    .stats-card.purple{background:linear-gradient(135deg,#8b5cf6,#5b3cc4)}
    .stats-card .stats-number{font-size:2.4rem;font-weight:900;margin:6px 0}
    .stats-card .stats-label{font-size:.88rem;opacity:.9;text-transform:uppercase;letter-spacing:1px}
    .stats-card i{font-size:2rem;opacity:.3;float:right}

    .progress-wrapper{
      padding:20px;
      margin-bottom:14px;
      border-radius:18px;
    }

    .progress-bar-custom{
      height:28px;
      border-radius:999px;
      background:linear-gradient(90deg,#4f8cff,#20427F);
      transition:width .6s ease;
    }

    .grilla-card{
      padding:18px;
      cursor:pointer;
      border:1px solid var(--s360-line);
      border-radius:20px;
      background:#fff;
      box-shadow:var(--s360-shadow-soft);
      transition:box-shadow .2s ease,transform .2s ease,border-color .2s ease;
    }

    .grilla-card:hover{
      transform:translateY(-4px);
      border-color:#d7e3f3;
      box-shadow:var(--s360-shadow-hover);
    }

    .grilla-ico{
      width:48px;height:48px;border-radius:14px;
      background:#eef4ff;display:flex;align-items:center;
      justify-content:center;color:var(--s360-brand);font-size:1.2rem;
    }
    .grilla-title{font-weight:900;color:var(--s360-text);margin:0}
    .grilla-sub{font-size:.85rem;color:var(--s360-muted);margin:4px 0 0}

    /* =========================
       SCROLLBAR
       ========================= */
    *::-webkit-scrollbar{width:10px;height:10px}
    *::-webkit-scrollbar-track{background:#eef2f7;border-radius:999px}
    *::-webkit-scrollbar-thumb{
      background:linear-gradient(180deg,#b9c8dd,#8ca4c6);
      border:2px solid #eef2f7;
      border-radius:999px;
    }

    /* =========================
       RESPONSIVE
       ========================= */
    @media (min-width:1400px){
      .content{padding-left:2.5% !important;padding-right:2.5% !important}
    }

    @media (min-width:1800px){
      .content{padding-left:3% !important;padding-right:3% !important}
    }

    @media (max-width:1200px){
      .hero-grid{grid-template-columns:1fr}
      .hero-metrics{min-width:0;width:100%;grid-template-columns:repeat(3,1fr)}
      .kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    }

    @media (max-width:991px){
      .content{
        padding-left:2% !important;
        padding-right:2% !important;
        padding-top:14px !important;
      }
      .dash-hero{padding:24px}
      #dashTerritorioFrame{min-height:68vh}
      .r-card-body{padding:15px}
      .table-wrapper{padding:14px}
      .chart-box{min-height:280px}
    }

    @media (max-width:767px){
      .dash-hero{
        min-height:0;
        padding:21px 18px;
        border-radius:22px;
      }
      .dash-hero h2{font-size:1.8rem}
      .dash-hero p{font-size:.82rem}
      .hero-pills{gap:6px}
      .hero-pill{font-size:.66rem;padding:7px 9px}
      .hero-metrics{grid-template-columns:repeat(3,1fr);gap:7px}
      .hero-metric{min-height:103px;padding:12px;border-radius:15px}
      .hero-metric-icon{width:30px;height:30px;margin-bottom:11px}
      .hero-metric-value{font-size:1.18rem}
      .hero-metric-label{font-size:.59rem}
      .selector-topline{align-items:flex-start;flex-direction:column}
      .selector-help{display:none}
      .sel-bar{padding:14px;border-radius:18px}
      .kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}
      .kpi{min-height:108px;padding:14px}
      .kpi-value{font-size:1.45rem}
      .kpi-label{font-size:.62rem}
      .kpi-ico{width:42px;height:42px;flex-basis:42px}
      #panel-territorio .r-card-header{border-radius:18px 18px 0 0}
      .map-shell{border-radius:0 0 20px 20px}
      .map-tools .chip:nth-child(2){display:none}
      .map-floating-tip{left:16px;bottom:16px;font-size:.62rem}
      #dashTerritorioFrame{min-height:60vh;border-radius:14px}
      .table-wrapper{border-radius:18px;padding:12px}
      .dataTables_wrapper .dataTables_filter,
      .dataTables_wrapper .dataTables_length{
        text-align:left !important;
      }
      .dataTables_wrapper .dataTables_filter input{
        width:100%;
        margin:7px 0 0;
      }
      .dataTables_wrapper .dataTables_paginate{
        justify-content:center;
        flex-wrap:wrap;
      }
    }

    @media (max-width:480px){
      .content{padding-left:10px !important;padding-right:10px !important}
      .dash-hero{padding:18px 15px}
      .hero-eyebrow{font-size:.63rem}
      .hero-metrics{grid-template-columns:1fr 1fr}
      .hero-metric:last-child{grid-column:1/-1}
      .kpi-grid{grid-template-columns:1fr 1fr}
      .kpi{min-height:102px}
      .kpi-ico{display:none}
      .chart-box{padding:9px;min-height:260px}
      .map-title-copy p{display:none}
      .map-tools{width:100%}
      .map-tools .chip{flex:1;justify-content:center}
      .map-iframe-wrap{padding:6px}
      #dashTerritorioFrame{min-height:120vh}
    }

    @media (prefers-reduced-motion:reduce){
      *,*::before,*::after{
        animation-duration:.01ms !important;
        animation-iteration-count:1 !important;
        transition-duration:.01ms !important;
        scroll-behavior:auto !important;
      }
    }
  </style>

</head>

<body>
<main class="main" id="top">
  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <div class="content">
    <div class="container-fluid px-0">

      <!-- HERO -->
      <section class="dash-hero" aria-label="Resumen del dashboard">
        <div class="hero-grid">
          <div>
            <div class="hero-eyebrow">
              <span class="hero-live"></span>
              Estadística360 · Intelligence Center
            </div>

            <h2>Dashboard de <span>Resultados</span></h2>

            <p>
              Analiza sondeos y cuestionarios desde una vista ejecutiva unificada:
              comportamiento territorial, participación, composición demográfica y
              resultados listos para interpretación.
            </p>

            <div class="hero-pills">
              <span class="hero-pill"><i class="fas fa-bolt"></i> Datos dinámicos</span>
              <span class="hero-pill"><i class="fas fa-map-marked-alt"></i> Inteligencia territorial</span>
              <span class="hero-pill"><i class="fas fa-shield-alt"></i> Acceso administrativo</span>
            </div>
          </div>

          <div class="hero-metrics" aria-label="Resumen de módulos">
            <div class="hero-metric">
              <div class="hero-metric-icon"><i class="fas fa-poll"></i></div>
              <div class="hero-metric-value"><?= (int)$totalSondeosDisponibles ?></div>
              <div class="hero-metric-label">Sondeos disponibles</div>
            </div>

            <div class="hero-metric">
              <div class="hero-metric-icon"><i class="fas fa-clipboard-check"></i></div>
              <div class="hero-metric-value"><?= (int)$totalCuestionariosDisponibles ?></div>
              <div class="hero-metric-label">Cuestionarios</div>
            </div>

            <div class="hero-metric">
              <div class="hero-metric-icon"><i class="fas fa-layer-group"></i></div>
              <div class="hero-metric-value"><?= (int)$totalModulosDisponibles ?></div>
              <div class="hero-metric-label">Módulos habilitados</div>
            </div>
          </div>
        </div>
      </section>

      <!-- SELECTOR PRINCIPAL -->
      <section class="sel-bar" aria-label="Selector de resultados">
        <div class="selector-topline">
          <div class="selector-title">
            <span class="selector-title-icon"><i class="fas fa-sliders-h"></i></span>
            <span>Centro de consulta y análisis</span>
          </div>
          <div class="selector-help">
            Elige el módulo y el estudio que deseas analizar.
          </div>
        </div>

        <div class="row g-3 align-items-stretch">
          <div class="col-12 col-lg-4">
            <div class="form-floating">
              <select class="form-select" id="tipo_selector" onchange="DashResultados.cambiarTipo()">
                <?php if ($viewSondeo): ?>
                  <option value="sondeo">Sondeo</option>
                <?php endif; ?>
                <?php if ($viewCuestionario): ?>
                  <option value="cuestionario">Cuestionario</option>
                <?php endif; ?>
              </select>
              <label for="tipo_selector"><i class="fas fa-layer-group me-2"></i>Tipo de análisis</label>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="form-floating">
              <select class="form-select" id="item_selector">
                <option value="">Selecciona...</option>
              </select>
              <label for="item_selector" id="item_selector_label">
                <i class="fas fa-list me-2"></i>Selecciona un ítem
              </label>
            </div>
          </div>

          <div class="col-12 col-lg-2">
            <button class="btn btn-brand w-100 h-100" onclick="DashResultados.cargar()">
              <i class="fas fa-chart-line me-2"></i>Analizar
            </button>
          </div>
        </div>
      </section>

      <!-- Datos PHP para JS -->
      <script>
        var DR_SONDEOS = <?= json_encode($sondeos) ?>;
        var DR_FICHAS  = <?= json_encode($fichasTecnicas) ?>;
      </script>

      <!-- Inputs ocultos que usan los módulos JS internamente -->
      <select id="sondeo_selector" style="display:none;"><option value=""></option></select>
      <select id="ficha_tecnica_select" style="display:none;"><option value=""></option></select>
      <button id="btn_cargar_datos" style="display:none;"></button>

      <!-- ══════════════════════════════════════
           PANEL SONDEO
      ══════════════════════════════════════ -->
      <div id="panel-sondeo" style="display:none;">

        <?php if (!$viewSondeo): ?>
          <div class="r-card"><div class="r-card-body empty-zone">
            <i class="fas fa-lock"></i> Sin permiso para ver sondeos.
          </div></div>
        <?php else: ?>

        <!-- KPIs Sondeo -->
        <div id="estadisticas-container" style="display:none;">
          <div class="kpi-grid mb-4">
            <div class="kpi">
              <div>
                <div class="kpi-label">Total Respuestas</div>
                <p class="kpi-value" id="stat-total-respuestas">0</p>
              </div>
              <div class="kpi-ico" style="background:linear-gradient(135deg,#20427F,#132b52);"><i class="fas fa-clipboard-list"></i></div>
            </div>
            <div class="kpi">
              <div>
                <div class="kpi-label">Votantes Únicos</div>
                <p class="kpi-value" id="stat-votantes-unicos">0</p>
              </div>
              <div class="kpi-ico" style="background:linear-gradient(135deg,#2ca02c,#1a7a1a);"><i class="fas fa-users"></i></div>
            </div>
            <div class="kpi">
              <div>
                <div class="kpi-label">Días Activo</div>
                <p class="kpi-value" id="stat-dias-activo">0</p>
              </div>
              <div class="kpi-ico" style="background:linear-gradient(135deg,#e377c2,#a0336e);"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div class="kpi">
              <div>
                <div class="kpi-label">Promedio Diario</div>
                <p class="kpi-value" id="stat-promedio-diario">0</p>
              </div>
              <div class="kpi-ico" style="background:linear-gradient(135deg,#ff7f0e,#c05a00);"><i class="fas fa-chart-line"></i></div>
            </div>
          </div>

          <!-- Gráfica general -->
          <div class="r-card mb-4">
            <div class="r-card-header">
              <h5><i class="fas fa-chart-pie me-2"></i>Resultados generales del sondeo</h5>
              <span class="chip"><i class="fas fa-info-circle"></i> Porcentaje + Totales</span>
            </div>
            <div class="r-card-body">
              <div class="chart-box"><div id="chart-general"></div></div>
            </div>
          </div>

          <!-- Ancla: mapa tras KPIs (flujo sondeo) -->
          <div id="slot-territorio-sn"></div>

          <!-- Gráficas demográficas -->
          <div class="row g-3">
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-landmark me-2"></i>Ideología Política</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-ideologia"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-venus-mars me-2"></i>Género</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-genero"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-birthday-cake me-2"></i>Rango de Edad</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-edad"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-dollar-sign me-2"></i>Nivel de Ingresos</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-ingresos"></div></div></div>
              </div>
            </div>
            <div class="col-12">
              <div class="r-card">
                <div class="r-card-header"><h5><i class="fas fa-graduation-cap me-2"></i>Nivel de Educación</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-educacion"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-map-marked-alt me-2"></i>Por Departamento</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-departamento"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-map-marker-alt me-2"></i>Por Municipio (Top 10)</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="chart-municipio"></div></div></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty sondeo -->
        <div id="empty-state">
          <div class="r-card"><div class="r-card-body empty-zone">
            <i class="fas fa-poll"></i>
            Selecciona un sondeo para ver estadísticas, resultados y análisis demográfico.
          </div></div>
        </div>

        <?php endif; ?>
      </div><!-- /panel-sondeo -->


      <!-- ══════════════════════════════════════
           PANEL CUESTIONARIO
      ══════════════════════════════════════ -->
      <div id="panel-cuestionario" style="display:none;">

        <?php if (!$viewCuestionario): ?>
          <div class="r-card"><div class="r-card-body empty-zone">
            <i class="fas fa-lock"></i> Sin permiso para ver cuestionarios.
          </div></div>
        <?php else: ?>

        <!-- Estadísticas cuestionario -->
        <div id="estadisticas_container" style="display:none;">

          <!-- Filtros listados -->
          <div class="r-card mb-3" id="filtros_listados_card">
            <div class="r-card-body">
              <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                  <label class="form-label small mb-1" for="filtro_tipo_listados">Tipo</label>
                  <select class="form-select form-select-sm filtro-sync-tipo" id="filtro_tipo_listados">
                    <option value="">Todos</option>
                  </select>
                </div>
                <div class="col-12 col-md-3">
                  <label class="form-label small mb-1" for="filtro_encuestador_listados">Encuestador</label>
                  <select class="form-select form-select-sm filtro-sync-encuestador" id="filtro_encuestador_listados">
                    <option value="">Todos</option>
                  </select>
                </div>
                <div class="col-6 col-md-2">
                  <label class="form-label small mb-1" for="filtro_fecha_desde_listados">Desde</label>
                  <input type="date" class="form-control form-control-sm filtro-sync-desde" id="filtro_fecha_desde_listados">
                </div>
                <div class="col-6 col-md-2">
                  <label class="form-label small mb-1" for="filtro_fecha_hasta_listados">Hasta</label>
                  <input type="date" class="form-control form-control-sm filtro-sync-hasta" id="filtro_fecha_hasta_listados">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-primary flex-fill btn-aplicar-filtros-listados" id="btn_aplicar_filtros_listados">
                    <i class="fas fa-filter me-1"></i>Filtrar
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-limpiar-filtros-listados" id="btn_limpiar_filtros_listados" title="Limpiar">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Últimas respuestas -->
          <div class="table-wrapper">
            <h5 class="mb-3"><i class="fas fa-history me-2"></i>Actividad reciente</h5>
            <div class="table-responsive">
              <table id="tabla_ultimas_respuestas" class="table table-striped table-sm fs-9 mb-0">
                <thead>
                  <tr>
                    <th>Tipo</th><th>Encuestado</th><th>Encuestador</th><th>Email</th><th>Fecha</th><th>Preguntas</th><th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <!-- Ancla: mapa justo debajo de Actividad reciente -->
          <div id="slot-territorio-cq"></div>

          <!-- Gráficas demográficas cuestionario -->
          <div class="row g-3 mb-3">
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-landmark me-2"></i>Ideología Política</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-ideologia"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-venus-mars me-2"></i>Género</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-genero"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-birthday-cake me-2"></i>Rango de Edad</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-edad"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-dollar-sign me-2"></i>Nivel de Ingresos</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-ingresos"></div></div></div>
              </div>
            </div>
            <div class="col-12">
              <div class="r-card">
                <div class="r-card-header"><h5><i class="fas fa-graduation-cap me-2"></i>Nivel de Educación</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-educacion"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-map-marked-alt me-2"></i>Por Departamento</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-departamento"></div></div></div>
              </div>
            </div>
            <div class="col-12 col-lg-6">
              <div class="r-card h-100">
                <div class="r-card-header"><h5><i class="fas fa-map-marker-alt me-2"></i>Por Municipio (Top 10)</h5></div>
                <div class="r-card-body"><div class="chart-box"><div id="cq-chart-municipio"></div></div></div>
              </div>
            </div>
          </div>

          <!--
               Estado de participación oculto visualmente.
               Se conserva en el DOM para no romper resultados_cuestionarios.js,
               que puede seguir utilizando los IDs de filtros, tabs y tablas.
          -->
          <div class="table-wrapper" id="s360-participacion-compat" style="display:none !important;" aria-hidden="true">
            <h5 class="mb-3"><i class="fas fa-users me-2"></i>Estado de participación</h5>

            <!-- Filtros (mismos que Últimas respuestas) -->
            <div class="r-card mb-3 border-0 shadow-none bg-light" id="filtros_listados_card_t2">
              <div class="r-card-body py-2 px-2">
                <div class="row g-2 align-items-end">
                  <div class="col-12 col-md-3">
                    <label class="form-label small mb-1" for="filtro_tipo_listados_t2">Tipo</label>
                    <select class="form-select form-select-sm filtro-sync-tipo" id="filtro_tipo_listados_t2">
                      <option value="">Todos</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-3">
                    <label class="form-label small mb-1" for="filtro_encuestador_listados_t2">Encuestador</label>
                    <select class="form-select form-select-sm filtro-sync-encuestador" id="filtro_encuestador_listados_t2">
                      <option value="">Todos</option>
                    </select>
                  </div>
                  <div class="col-6 col-md-2">
                    <label class="form-label small mb-1" for="filtro_fecha_desde_listados_t2">Desde</label>
                    <input type="date" class="form-control form-control-sm filtro-sync-desde" id="filtro_fecha_desde_listados_t2">
                  </div>
                  <div class="col-6 col-md-2">
                    <label class="form-label small mb-1" for="filtro_fecha_hasta_listados_t2">Hasta</label>
                    <input type="date" class="form-control form-control-sm filtro-sync-hasta" id="filtro_fecha_hasta_listados_t2">
                  </div>
                  <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary flex-fill btn-aplicar-filtros-listados">
                      <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-limpiar-filtros-listados" title="Limpiar">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <ul class="nav nav-tabs mb-4" id="votantesTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="respondieron-tab"
                  data-bs-toggle="tab" data-bs-target="#respondieron" type="button" role="tab">
                  <i class="fas fa-check-circle me-2"></i>Han Respondido
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="noRespondieron-tab"
                  data-bs-toggle="tab" data-bs-target="#noRespondieron" type="button" role="tab">
                  <i class="fas fa-clock me-2"></i>Pendientes
                </button>
              </li>
            </ul>
            <div class="tab-content" id="votantesTabsContent">
              <div class="tab-pane fade show active" id="respondieron" role="tabpanel">
                <div class="table-responsive">
                  <table id="tabla_respondieron" class="table table-striped table-sm fs-9 mb-0 w-100">
                    <thead>
                      <tr>
                        <th>Tipo</th><th>Encuestado</th><th>Encuestador</th><th>Email</th><th>Género</th><th>Edad</th>
                        <th>Ideología</th><th>Fecha</th><th>Preguntas</th><th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="noRespondieron" role="tabpanel">
                <div class="table-responsive">
                  <table id="tabla_no_respondieron" class="table table-striped table-sm fs-9 mb-0 w-100">
                    <thead>
                      <tr>
                        <th>Tipo</th><th>Encuestado</th><th>Encuestador</th><th>Email</th><th>Username</th>
                        <th>Género</th><th>Edad</th><th>Ideología</th><th>Estado</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty cuestionario -->
        <div id="empty_state">
          <div class="r-card"><div class="r-card-body empty-zone">
            <i class="fas fa-clipboard-list"></i>
            Selecciona un cuestionario para ver estadísticas y resultados.
          </div></div>
        </div>

        <?php endif; ?>
      </div><!-- /panel-cuestionario -->

      <!-- Vista territorial: se mueve a #slot-territorio-cq o #slot-territorio-sn al cargar -->
      <section id="panel-territorio" class="r-card mb-4" style="display:none;">
        <div class="r-card-header">
          <div class="map-title-wrap">
            <div class="map-title-icon">
              <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="map-title-copy">
              <h5>Explorador territorial interactivo</h5>
              <p>Analiza el comportamiento geográfico y explora cada territorio visualmente.</p>
            </div>
          </div>

          <div class="map-tools">
            <span class="chip success"><i class="fas fa-circle"></i> Mapa activo</span>
            <span class="chip dark"><i class="fas fa-mouse-pointer"></i> Hover interactivo</span>
          </div>
        </div>

        <div class="map-shell">
          <div class="map-iframe-wrap">
            <iframe id="dashTerritorioFrame"
              title="Vista territorial"
              src="about:blank"
              loading="eager"></iframe>
          </div>

          <div class="map-floating-tip">
            <i class="fas fa-hand-pointer"></i>
            Pasa el cursor sobre el mapa para resaltar el territorio
          </div>
        </div>
      </section>

      <!-- Empty state global -->
      <div id="panel-empty">
        <div class="r-card"><div class="r-card-body empty-zone">
          <i class="fas fa-chart-bar"></i>
          Selecciona el tipo y el ítem para ver resultados.
        </div></div>
      </div>

    </div><!-- /container -->

    <?php include './admin/include/footer.php'; ?>
  </div><!-- /content -->
</main>

<!-- Scripts base -->
<?php include 'admin/include/gerenic_script.php'; ?>
<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
window.Apex = {
  chart: {
    fontFamily: '"Inter", system-ui, sans-serif',
    toolbar: { show: false },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 650
    }
  },
  grid: {
    borderColor: '#edf1f6',
    strokeDashArray: 4
  },
  dataLabels: {
    style: {
      fontFamily: '"Inter", system-ui, sans-serif',
      fontWeight: 700
    }
  },
  legend: {
    fontFamily: '"Inter", system-ui, sans-serif',
    fontWeight: 600,
    labels: { colors: '#667085' }
  },
  tooltip: {
    theme: 'light'
  }
};
</script>

<!-- JS de cada módulo -->
<script src="admin/js/dashboard_stats.js"></script>
<script src="admin/js/resultados_sondeos.js"></script>
<script src="admin/js/resultados_cuestionarios.js"></script>

<?php include 'admin/include/scriptsgober360.php'; ?>

<script>

// ─── MICROINTERACCIONES PREMIUM ──────────────────────────────────────────────
function S360_animarCambioMetrica() {
  document.querySelectorAll('.kpi-value').forEach(function(el) {
    if (el.dataset.s360Observed === '1') return;
    el.dataset.s360Observed = '1';

    var observer = new MutationObserver(function() {
      el.classList.remove('metric-changed');
      void el.offsetWidth;
      el.classList.add('metric-changed');
    });

    observer.observe(el, {
      childList: true,
      characterData: true,
      subtree: true
    });
  });
}

function S360_potenciarMapaTerritorial() {
  var iframe = document.getElementById('dashTerritorioFrame');
  if (!iframe || iframe.dataset.s360Enhanced === '1') return;

  iframe.dataset.s360Enhanced = '1';

  iframe.addEventListener('load', function() {
    if (!iframe.src || iframe.src === 'about:blank') return;

    try {
      var doc = iframe.contentDocument || iframe.contentWindow.document;
      if (!doc || !doc.head || !doc.body) return;

      if (!doc.getElementById('s360-map-premium-style')) {
        var style = doc.createElement('style');
        style.id = 's360-map-premium-style';
        style.textContent = `
          html, body {
            scrollbar-width: thin;
            scrollbar-color: #9db0cc #eef3f8;
          }

          body {
            background:
              radial-gradient(560px 320px at 50% 0%, rgba(79,140,255,.065), transparent 72%),
              linear-gradient(180deg,#ffffff,#f8faff) !important;
          }

          svg {
            overflow: visible !important;
          }

          svg path,
          svg polygon,
          svg rect,
          svg circle {
            cursor: pointer !important;
            transform-box: fill-box;
            transform-origin: center;
            transition:
              transform .26s cubic-bezier(.2,.78,.25,1),
              filter .26s ease,
              opacity .26s ease,
              stroke-width .26s ease,
              stroke .26s ease !important;
          }

          svg g {
            transform-box: fill-box;
            transform-origin: center;
          }

          svg path:hover,
          svg polygon:hover,
          svg rect:hover {
            transform: scale(1.035);
            filter:
              brightness(1.08)
              saturate(1.16)
              drop-shadow(0 10px 11px rgba(32,66,127,.30)) !important;
            stroke: rgba(255,255,255,.98) !important;
            stroke-width: 2.2px !important;
            opacity: 1 !important;
          }

          svg g:hover > path,
          svg g:hover > polygon,
          svg g:hover > rect {
            filter:
              brightness(1.08)
              saturate(1.14)
              drop-shadow(0 10px 11px rgba(32,66,127,.27)) !important;
          }

          svg text {
            font-family: "Inter", system-ui, sans-serif !important;
            font-weight: 750 !important;
            letter-spacing: .18px;
            paint-order: stroke fill;
            stroke: rgba(255,255,255,.90);
            stroke-width: 1.8px;
            stroke-linejoin: round;
            transition:
              fill .2s ease,
              font-size .2s ease,
              letter-spacing .2s ease,
              filter .2s ease !important;
          }

          svg g:hover text {
            fill: #173f7d !important;
            letter-spacing: .45px;
            filter: drop-shadow(0 2px 3px rgba(255,255,255,.95));
          }

          .s360-map-cursor-glow {
            position: fixed;
            left: 0;
            top: 0;
            width: 180px;
            height: 180px;
            margin-left: -90px;
            margin-top: -90px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 999999;
            opacity: 0;
            background: radial-gradient(circle, rgba(79,140,255,.13), rgba(79,140,255,.04) 36%, transparent 70%);
            transition: opacity .18s ease;
            mix-blend-mode: multiply;
          }

          body.s360-map-hovering .s360-map-cursor-glow {
            opacity: 1;
          }

          @media (max-width: 768px) {
            .s360-map-cursor-glow { display:none !important; }

            svg path:hover,
            svg polygon:hover,
            svg rect:hover {
              transform: scale(1.018);
            }
          }

          @media (prefers-reduced-motion: reduce) {
            svg path,
            svg polygon,
            svg rect,
            svg circle,
            svg text {
              transition: none !important;
            }
          }
        `;
        doc.head.appendChild(style);
      }

      if (!doc.querySelector('.s360-map-cursor-glow')) {
        var glow = doc.createElement('div');
        glow.className = 's360-map-cursor-glow';
        doc.body.appendChild(glow);

        doc.addEventListener('pointermove', function(ev) {
          glow.style.transform = 'translate3d(' + ev.clientX + 'px,' + ev.clientY + 'px,0)';
        }, { passive: true });

        doc.addEventListener('pointerover', function(ev) {
          if (ev.target && ev.target.closest && ev.target.closest('svg')) {
            doc.body.classList.add('s360-map-hovering');
          }
        });

        doc.addEventListener('pointerout', function(ev) {
          if (ev.target && ev.target.closest && ev.target.closest('svg')) {
            doc.body.classList.remove('s360-map-hovering');
          }
        });
      }

      doc.querySelectorAll('svg path, svg polygon, svg rect').forEach(function(region) {
        if (region.dataset.s360RegionFx === '1') return;
        region.dataset.s360RegionFx = '1';
        region.setAttribute('tabindex', region.getAttribute('tabindex') || '0');

        region.addEventListener('focus', function() {
          region.style.filter = 'brightness(1.08) saturate(1.14) drop-shadow(0 10px 11px rgba(32,66,127,.28))';
          region.style.stroke = '#ffffff';
          region.style.strokeWidth = '2.2px';
        });

        region.addEventListener('blur', function() {
          region.style.filter = '';
          region.style.stroke = '';
          region.style.strokeWidth = '';
        });
      });

    } catch (e) {
      console.warn('Estadística360: no fue posible aplicar el efecto premium al mapa.', e);
    }
  });
}

// ─── DASHBOARD RESULTADOS ────────────────────────────────────────────────────
var DashResultados = {

  // Opciones para cada tipo
  _opciones: {
    sondeo: DR_SONDEOS.map(function(s) {
      return { value: s.id, label: s.sondeo + ' (' + s.total_respuestas + ' respuestas)' };
    }),
    cuestionario: DR_FICHAS.map(function(f) {
      return { value: f.id, label: f.realizada_por_o_encomendada_por || 'Ficha #' + f.id };
    })
  },

  init: function() {
    this.cambiarTipo();
  },

  cambiarTipo: function() {
    var tipo  = document.getElementById('tipo_selector').value;
    var sel   = document.getElementById('item_selector');
    var label = document.getElementById('item_selector_label');
    var opts  = DashResultados._opciones[tipo] || [];

    // Actualizar label
    label.innerHTML = tipo === 'sondeo'
      ? '<i class="fas fa-poll me-2"></i>Selecciona un Sondeo'
      : '<i class="fas fa-clipboard-list me-2"></i>Selecciona un Cuestionario';

    // Repoblar select
    sel.innerHTML = '<option value="">Selecciona...</option>';
    opts.forEach(function(o) {
      var opt = document.createElement('option');
      opt.value = o.value;
      opt.textContent = o.label;
      sel.appendChild(opt);
    });

    // Ocultar paneles
    DashResultados._ocultarPaneles();
  },

  cargar: function() {
    var tipo = document.getElementById('tipo_selector').value;
    var id   = document.getElementById('item_selector').value;

    if (!id) {
      document.getElementById('panel-empty').style.display = '';
      document.getElementById('panel-sondeo').style.display = 'none';
      document.getElementById('panel-cuestionario').style.display = 'none';
      document.getElementById('panel-territorio').style.display = 'none';
      document.getElementById('dashTerritorioFrame').src = 'about:blank';
      return;
    }

    document.getElementById('panel-empty').style.display = 'none';

    var panelTerr = document.getElementById('panel-territorio');
    var slotId = (tipo === 'cuestionario') ? 'slot-territorio-cq' : 'slot-territorio-sn';
    var slot = document.getElementById(slotId);
    if (slot && panelTerr && panelTerr.parentNode !== slot) {
      slot.appendChild(panelTerr);
    }
    panelTerr.style.display = '';
    document.getElementById('dashTerritorioFrame').src =
      'vista_territorio.php?modo=' + encodeURIComponent(tipo) + '&id=' + encodeURIComponent(id);

    if (tipo === 'sondeo') {
      document.getElementById('panel-sondeo').style.display = '';
      document.getElementById('panel-cuestionario').style.display = 'none';
      var selS = $('#sondeo_selector');
      if (!selS.find('option[value="' + id + '"]').length) {
        selS.append('<option value="' + id + '">' + id + '</option>');
      }
      selS.val(id);
      // Esperar a que el DOM sea visible antes de renderizar ApexCharts
      setTimeout(function() { RESULTADOS_SONDEO.cargarEstadisticas(); }, 50);

    } else if (tipo === 'cuestionario') {
      document.getElementById('panel-cuestionario').style.display = '';
      document.getElementById('panel-sondeo').style.display = 'none';
      var selC = $('#ficha_tecnica_select');
      if (!selC.find('option[value="' + id + '"]').length) {
        selC.append('<option value="' + id + '">' + id + '</option>');
      }
      selC.val(id).trigger('change');
      setTimeout(function() { RESULTADOS_CUESTIONARIOS.cargarEstadisticas(); }, 50);
    }
  },

  _ocultarPaneles: function() {
    document.getElementById('panel-sondeo').style.display = 'none';
    document.getElementById('panel-cuestionario').style.display = 'none';
    document.getElementById('panel-territorio').style.display = 'none';
    document.getElementById('dashTerritorioFrame').src = 'about:blank';
    document.getElementById('panel-empty').style.display = '';
    // Resetear estados internos de los módulos
    $('#estadisticas-container').hide();
    $('#empty-state').show();
    $('#estadisticas_container').hide();
    $('#empty_state').show();
    // Destruir todos los charts de ApexCharts
    if (typeof apexCharts !== 'undefined' && apexCharts) {
      Object.keys(apexCharts).forEach(function(k) {
        try { apexCharts[k].destroy(); } catch(e) {}
      });
      apexCharts = {};
    }
  }
};

// ─── INIT ─────────────────────────────────────────────────────────────────────
$(document).ready(function() {
  if (typeof DASHBOARD !== 'undefined') DASHBOARD.init();
  RESULTADOS_SONDEO.init();
  RESULTADOS_CUESTIONARIOS.init();
  S360_animarCambioMetrica();
  S360_potenciarMapaTerritorial();
  DashResultados.init();
});
</script>
</body>
</html>
