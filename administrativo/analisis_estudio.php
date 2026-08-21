<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/AnalisisEstudio.php';
include './admin/classes/Grilla.php';
include './admin/classes/Formula.php';

// Variables de configuración
include './admin/include/generic_info_configuracion.php';

// Validación de permisos - Módulo de Análisis de Estudio
$permissions = [
    'view' => SessionData::getPermission(50),   // Ver Análisis de Estudio
    'create' => SessionData::getPermission(51), // Crear Análisis de Estudio
    'edit' => SessionData::getPermission(52),   // Editar Análisis de Estudio
    'delete' => SessionData::getPermission(53)  // Eliminar Análisis de Estudio
];

if (!$permissions['view']) {
    require 'permiso_denegado.php';
    exit;
}

// Obtener grillas activas
$arrGrillas = Grilla::getAll(null);
$grillas = $arrGrillas['output']['response'] ?? [];

// Obtener tipos de indicadores únicos
$arrFormulas = Formula::getAll(null);
$formulas = $arrFormulas['output']['response'] ?? [];

$tipos_indicadores = array_values(
    array_filter(
        array_unique(
            array_column($formulas, 'tipo_indicador')
        ),
        function($valor){
            return trim((string)$valor) !== '';
        }
    )
);

sort($tipos_indicadores, SORT_NATURAL);

$modulo = 'Análisis de Estudio Electoral';

// KPIs visuales. Solo lectura; no modifica el flujo del módulo.
$totalGrillasKpi = is_array($grillas) ? count($grillas) : 0;
$totalFormulasKpi = is_array($formulas) ? count($formulas) : 0;
$totalTiposKpi = count($tipos_indicadores);
$totalPasosKpi = 5;
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* ==========================================================
       ESTADÍSTICA360
       ELECTORAL INTELLIGENCE COMMAND CENTER
       ----------------------------------------------------------
       UI / UX solamente.
       IDs y llamadas de ANALISIS_ESTUDIO permanecen intactos.
    ========================================================== */

    :root{
      --ae-navy-950:#06172D;
      --ae-navy-900:#092147;
      --ae-navy-800:#123A74;

      --ae-blue-700:#20427F;
      --ae-blue-600:#2D63BD;
      --ae-blue-500:#4B8CF7;
      --ae-cyan:#1DB6DB;
      --ae-violet:#7568E8;

      --ae-green:#12B981;
      --ae-orange:#F59E0B;
      --ae-red:#E5484D;

      --ae-page:#F3F6FB;
      --ae-card:#FFFFFF;
      --ae-soft:#F8FAFD;

      --ae-text:#101828;
      --ae-text-2:#344054;
      --ae-muted:#667085;
      --ae-light:#98A2B3;

      --ae-line:#E5EAF1;

      --ae-r-xxl:30px;
      --ae-r-xl:24px;
      --ae-r-lg:18px;
      --ae-r-md:14px;

      --ae-shadow:0 24px 68px rgba(15,23,42,.10);
      --ae-shadow-soft:0 12px 34px rgba(15,23,42,.065);
    }

    *{ box-sizing:border-box; }

    html{ scroll-behavior:smooth; }

    body.ae-page{
      margin:0;
      color:var(--ae-text);
      font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased;

      background:
        radial-gradient(900px 500px at 3% -5%,rgba(75,140,247,.12),transparent 64%),
        radial-gradient(760px 440px at 103% 5%,rgba(29,182,219,.07),transparent 64%),
        linear-gradient(180deg,#F8FAFD 0%,#F2F5FA 100%);
    }

    body.ae-page::before{
      content:"";
      position:fixed;
      inset:0;
      z-index:-1;
      pointer-events:none;
      opacity:.30;
      background-image:
        linear-gradient(rgba(32,66,127,.023) 1px,transparent 1px),
        linear-gradient(90deg,rgba(32,66,127,.023) 1px,transparent 1px);
      background-size:36px 36px;
      mask-image:linear-gradient(to bottom,#000,transparent 84%);
    }

    .content{
      padding-top:18px !important;
      padding-bottom:38px !important;
      margin-top:0 !important;
    }

    .content .container-fluid{
      width:100%;
      max-width:1660px;
      margin:0 auto;
      padding-left:18px !important;
      padding-right:18px !important;
    }

    /* ==========================================================
       HERO
    ========================================================== */

    .ae-hero{
      position:relative;
      isolation:isolate;
      overflow:hidden;
      min-height:232px;
      margin-bottom:16px;
      padding:30px;
      border:1px solid rgba(255,255,255,.12);
      border-radius:var(--ae-r-xxl);
      color:#fff;

      background:
        radial-gradient(570px 280px at 9% 0%,rgba(75,140,247,.36),transparent 66%),
        radial-gradient(480px 270px at 94% 10%,rgba(29,182,219,.19),transparent 67%),
        linear-gradient(135deg,#173E7B 0%,#102A56 47%,#07162E 100%);

      box-shadow:0 30px 80px rgba(8,28,63,.24);
    }

    .ae-hero::before{
      content:"";
      position:absolute;
      z-index:-1;
      width:440px;
      height:440px;
      right:-160px;
      top:-225px;
      border:1px solid rgba(255,255,255,.075);
      border-radius:50%;
      box-shadow:
        0 0 0 45px rgba(255,255,255,.021),
        0 0 0 92px rgba(255,255,255,.015),
        0 0 0 138px rgba(255,255,255,.010);
    }

    .ae-hero-grid{
      display:grid;
      grid-template-columns:minmax(0,1fr) auto;
      gap:28px;
      align-items:center;
    }

    .ae-eyebrow{
      display:inline-flex;
      align-items:center;
      gap:8px;
      min-height:32px;
      margin-bottom:13px;
      padding:7px 11px;
      border:1px solid rgba(255,255,255,.14);
      border-radius:999px;
      color:rgba(255,255,255,.88);
      background:rgba(255,255,255,.075);
      backdrop-filter:blur(12px);
      font-size:.67rem;
      font-weight:800;
      letter-spacing:.62px;
      text-transform:uppercase;
    }

    .ae-live-dot{
      width:7px;
      height:7px;
      border-radius:50%;
      background:#5DE4A0;
      box-shadow:
        0 0 0 5px rgba(93,228,160,.11),
        0 0 16px rgba(93,228,160,.45);
    }

    .ae-hero h1{
      margin:0;
      color:#fff;
      font-family:"Manrope","Inter",sans-serif;
      font-size:clamp(1.9rem,3vw,3rem);
      line-height:1.04;
      font-weight:800;
      letter-spacing:-1.5px;
    }

    .ae-hero h1 span{ color:#B7D0FF; }

    .ae-hero p{
      max-width:860px;
      margin:11px 0 0;
      color:rgba(255,255,255,.70);
      font-size:.91rem;
      line-height:1.67;
      font-weight:500;
    }

    .ae-hero-pills{
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:18px;
    }

    .ae-hero-pill{
      display:inline-flex;
      align-items:center;
      gap:7px;
      min-height:35px;
      padding:8px 11px;
      border:1px solid rgba(255,255,255,.10);
      border-radius:11px;
      color:rgba(255,255,255,.84);
      background:rgba(255,255,255,.07);
      font-size:.67rem;
      font-weight:700;
    }

    .ae-hero-pill i{ color:#A7C7FF; }

    .ae-kpis{
      display:grid;
      grid-template-columns:repeat(4,minmax(92px,1fr));
      gap:9px;
      min-width:550px;
    }

    .ae-kpi{
      min-height:112px;
      padding:14px;
      border:1px solid rgba(255,255,255,.12);
      border-radius:17px;
      background:
        linear-gradient(145deg,rgba(255,255,255,.115),rgba(255,255,255,.05));
      backdrop-filter:blur(14px);
      transition:
        transform .22s ease,
        border-color .22s ease,
        background .22s ease;
    }

    .ae-kpi:hover{
      transform:translateY(-4px);
      border-color:rgba(255,255,255,.20);
      background:
        linear-gradient(145deg,rgba(255,255,255,.17),rgba(255,255,255,.07));
    }

    .ae-kpi-icon{
      width:31px;
      height:31px;
      display:flex;
      align-items:center;
      justify-content:center;
      margin-bottom:13px;
      border-radius:10px;
      color:#D8E8FF;
      background:rgba(255,255,255,.10);
      font-size:.78rem;
    }

    .ae-kpi strong{
      display:block;
      color:#fff;
      font-family:"Manrope","Inter",sans-serif;
      font-size:1.36rem;
      line-height:1;
      font-weight:800;
      letter-spacing:-.55px;
    }

    .ae-kpi span{
      display:block;
      margin-top:5px;
      color:rgba(255,255,255,.58);
      font-size:.59rem;
      line-height:1.25;
      font-weight:700;
    }

    .ae-hero-actions{
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:18px;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .btn-pro,
    .btn-soft{
      min-height:43px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:9px 14px;
      border-radius:12px !important;
      font-size:.71rem;
      font-weight:800;
      transition:
        transform .18s ease,
        box-shadow .18s ease,
        border-color .18s ease,
        background .18s ease;
    }

    .btn-pro{
      border:0 !important;
      color:#fff !important;
      background:
        linear-gradient(135deg,var(--ae-blue-500),var(--ae-blue-600) 50%,var(--ae-blue-700)) !important;
      box-shadow:0 11px 23px rgba(32,66,127,.22);
    }

    .btn-pro:hover{
      transform:translateY(-2px);
      box-shadow:0 16px 30px rgba(32,66,127,.29);
    }

    .btn-soft{
      border:1px solid #D7E2F2 !important;
      color:var(--ae-blue-700) !important;
      background:#fff !important;
      box-shadow:none !important;
    }

    .btn-soft:hover{
      transform:translateY(-1px);
      border-color:#BFD2EC !important;
      background:#F5F9FF !important;
    }

    /* Hero inverted buttons */
    .ae-hero .btn-soft{
      color:#fff !important;
      border-color:rgba(255,255,255,.17) !important;
      background:rgba(255,255,255,.08) !important;
      backdrop-filter:blur(10px);
    }

    .ae-hero .btn-pro{
      color:#173A70 !important;
      background:#fff !important;
      box-shadow:0 12px 28px rgba(0,0,0,.15);
    }

    /* ==========================================================
       FLOW / WIZARD
    ========================================================== */

    .ae-flow-card{
      overflow:hidden;
      margin-bottom:16px;
      border:1px solid var(--ae-line);
      border-radius:var(--ae-r-xl);
      background:#fff;
      box-shadow:var(--ae-shadow);
    }

    .ae-flow-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:17px 18px;
      border-bottom:1px solid #EDF0F5;
      background:
        radial-gradient(330px 120px at 4% 0%,rgba(75,140,247,.06),transparent 72%),
        linear-gradient(180deg,#FFFFFF,#FBFCFF);
    }

    .ae-flow-title{
      display:flex;
      align-items:center;
      gap:11px;
    }

    .ae-flow-icon{
      width:42px;
      height:42px;
      flex:0 0 42px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:13px;
      color:var(--ae-blue-700);
      background:#EDF4FF;
      font-size:.92rem;
    }

    .ae-flow-title h2{
      margin:0;
      color:#182230;
      font-family:"Manrope","Inter",sans-serif;
      font-size:.98rem;
      font-weight:800;
    }

    .ae-flow-title p{
      margin:3px 0 0;
      color:var(--ae-light);
      font-size:.64rem;
      font-weight:600;
    }

    .ae-flow-badge{
      display:inline-flex;
      align-items:center;
      gap:6px;
      min-height:30px;
      padding:6px 10px;
      border:1px solid #DCE8FA;
      border-radius:999px;
      color:#265EA9;
      background:#EEF5FF;
      font-size:.63rem;
      font-weight:800;
    }

    .ae-flow-body{ padding:16px; }

    /* Progress rail */
    .ae-progress-rail{
      display:grid;
      grid-template-columns:repeat(5,1fr);
      gap:7px;
      margin-bottom:14px;
      padding:7px;
      border:1px solid #E7ECF3;
      border-radius:16px;
      background:#F7F9FC;
    }

    .ae-progress-item{
      min-height:48px;
      display:flex;
      align-items:center;
      gap:8px;
      padding:8px 10px;
      border:1px solid transparent;
      border-radius:11px;
      color:#667085;
      background:transparent;
      font-size:.62rem;
      font-weight:800;
    }

    .ae-progress-number{
      width:25px;
      height:25px;
      flex:0 0 25px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:8px;
      color:var(--ae-blue-700);
      background:#EAF2FF;
      font-size:.62rem;
      font-weight:800;
    }

    .ae-progress-item:first-child{
      color:#fff;
      background:
        linear-gradient(135deg,var(--ae-blue-500),var(--ae-blue-700));
      box-shadow:0 9px 19px rgba(32,66,127,.15);
    }

    .ae-progress-item:first-child .ae-progress-number{
      color:#fff;
      background:rgba(255,255,255,.15);
    }

    /* Step blocks */
    .step-head{
      min-height:70px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:12px !important;
      padding:13px 14px;
      border:1px solid #E5EAF1;
      border-radius:16px;
      background:
        linear-gradient(145deg,#FFFFFF,#FBFCFF);
      box-shadow:0 8px 20px rgba(15,23,42,.04);
    }

    .step-left{
      display:flex;
      align-items:center;
      gap:11px;
      min-width:0;
    }

    .step-pill{
      width:36px;
      height:36px;
      flex:0 0 36px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:12px;
      color:#fff;
      background:
        linear-gradient(135deg,var(--ae-blue-500),var(--ae-blue-700));
      box-shadow:0 8px 18px rgba(32,66,127,.16);
      font-size:.72rem;
      font-weight:800;
    }

    .step-title{
      margin:0;
      color:var(--ae-text);
      font-size:.77rem;
      font-weight:800;
      letter-spacing:-.1px;
    }

    .step-sub{
      margin:3px 0 0;
      color:var(--ae-light);
      font-size:.61rem;
      font-weight:600;
    }

    .mini-actions{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      justify-content:flex-end;
    }

    .ae-step-block{
      margin-top:2px;
    }

    .soft-hr{
      height:1px;
      margin:14px 0;
      border:0;
      background:
        linear-gradient(90deg,transparent,#E5EAF1,transparent);
    }

    /* ==========================================================
       INPUTS
    ========================================================== */

    .form-floating>.form-control,
    .form-floating>.form-select{
      min-height:58px;
      border:1px solid #D9E0EA !important;
      border-radius:14px !important;
      color:var(--ae-text-2);
      background:#FBFCFE;
      font-size:.78rem;
      font-weight:650;
      box-shadow:none !important;
      transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background .18s ease;
    }

    .form-floating>.form-control:hover,
    .form-floating>.form-select:hover{
      border-color:#BCC8D9 !important;
      background:#fff;
    }

    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus{
      border-color:var(--ae-blue-500) !important;
      background:#fff;
      box-shadow:0 0 0 4px rgba(75,140,247,.10) !important;
    }

    .form-floating>label{
      color:#667085;
      font-size:.75rem;
      font-weight:650;
    }

    /* ==========================================================
       CANDIDATES DYNAMIC CONTENT
    ========================================================== */

    #candidatos-container{
      padding:3px 0;
    }

    #candidatos-container .card,
    #candidatos-container [class*="card"]{
      overflow:hidden;
      border:1px solid #E5EAF1 !important;
      border-radius:16px !important;
      background:#fff !important;
      box-shadow:0 10px 25px rgba(15,23,42,.055) !important;
      transition:
        transform .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
    }

    #candidatos-container .card:hover{
      transform:translateY(-3px);
      border-color:#D6E2F2 !important;
      box-shadow:0 16px 32px rgba(15,23,42,.08) !important;
    }

    #candidatos-container img{
      object-fit:cover;
    }

    /* ==========================================================
       FORMULAS TABLE
    ========================================================== */

    #tabla-formulas{
      width:100%;
      margin:0 !important;
      border-collapse:separate !important;
      border-spacing:0 6px !important;
    }

    #tabla-formulas thead th{
      padding:9px 10px !important;
      border:0 !important;
      color:#667085 !important;
      background:transparent !important;
      font-size:.58rem !important;
      font-weight:800 !important;
      letter-spacing:.38px;
      text-transform:uppercase;
      white-space:nowrap;
    }

    #tabla-formulas tbody td{
      padding:10px !important;
      border-top:1px solid #E9EDF4 !important;
      border-bottom:1px solid #E9EDF4 !important;
      color:#344054;
      background:#fff;
      font-size:.66rem;
      line-height:1.45;
      vertical-align:middle;
    }

    #tabla-formulas tbody td:first-child{
      border-left:1px solid #E9EDF4 !important;
      border-radius:12px 0 0 12px;
    }

    #tabla-formulas tbody td:last-child{
      border-right:1px solid #E9EDF4 !important;
      border-radius:0 12px 12px 0;
    }

    #tabla-formulas tbody tr{
      transition:transform .18s ease;
    }

    #tabla-formulas tbody tr:hover{
      transform:translateY(-1px);
    }

    #tabla-formulas tbody tr:hover td{
      border-color:#DCE7F6 !important;
      background:linear-gradient(90deg,#F6FAFF,#FFFFFF) !important;
    }

    /* ==========================================================
       ANALYSIS CONTEXT
    ========================================================== */

    #seccion-analisis{
      margin-top:4px;
    }

    .analisis-banner-gradient{
      position:relative;
      overflow:hidden;
      border:1px solid #DDE7F3 !important;
      border-radius:20px !important;
      background:
        radial-gradient(420px 190px at 5% 0%,rgba(75,140,247,.08),transparent 70%),
        linear-gradient(145deg,#F8FBFF,#FFFFFF) !important;
      box-shadow:0 13px 30px rgba(15,23,42,.06);
    }

    .analisis-badge-active{
      display:inline-flex;
      align-items:center;
      gap:7px;
      padding:6px 10px;
      border:1px solid #D1E9FF;
      border-radius:999px;
      color:#175CD3;
      background:#EFF8FF;
      font-size:.67rem;
      font-weight:800;
      letter-spacing:.02em;
    }

    .analisis-candidato-card{
      overflow:hidden;
      border:1px solid #E3EAF3 !important;
      border-radius:18px !important;
      background:#fff !important;
      box-shadow:0 10px 25px rgba(15,23,42,.055) !important;
    }

    .analisis-candidato-foto{
      border:4px solid #D7E7FF !important;
      box-shadow:0 12px 26px rgba(32,66,127,.14) !important;
    }

    .analisis-info-card{
      overflow:hidden;
      border:1px solid #E3EAF3 !important;
      border-radius:16px !important;
      box-shadow:none !important;
    }

    /* ==========================================================
       RESULTS
    ========================================================== */

    .result-card{
      position:relative;
      overflow:hidden;
      height:100%;
      border:1px solid #E4E9F1 !important;
      border-radius:18px !important;
      background:#fff !important;
      box-shadow:0 10px 24px rgba(15,23,42,.055);
      transition:
        transform .18s ease,
        box-shadow .18s ease;
    }

    .result-card:hover{
      transform:translateY(-3px);
      box-shadow:0 16px 32px rgba(15,23,42,.08);
    }

    .result-card .topline{
      height:4px;
      width:100%;
      background:
        linear-gradient(90deg,var(--ae-blue-500),var(--ae-blue-700));
    }

    .result-card h6{
      color:#475467 !important;
      font-size:.65rem;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.05em;
    }

    .result-card .display-6{
      color:#101828;
      font-family:"Manrope","Inter",sans-serif;
      font-size:1.8rem;
      font-weight:800;
      letter-spacing:-.8px;
    }

    .result-card small{
      color:#98A2B3 !important;
      font-size:.60rem;
      font-weight:650;
    }

    /* ==========================================================
       MINI MODULES
    ========================================================== */

    .mini-module{
      overflow:hidden;
      border:1px solid #E4E9F1;
      border-radius:18px;
      background:#fff;
      box-shadow:0 10px 24px rgba(15,23,42,.05);
    }

    .mini-module .module-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      padding:13px 14px;
      border-bottom:1px solid #EDF0F5;
      background:
        linear-gradient(180deg,#FFFFFF,#FBFCFF);
    }

    .mini-module .module-head h6{
      color:#344054 !important;
      font-size:.71rem;
      font-weight:800 !important;
    }

    .mini-module .module-head small{
      color:#98A2B3 !important;
      font-size:.59rem;
      font-weight:600;
    }

    .mini-module .module-body{
      padding:14px;
      background:#FCFDFE;
    }

    /* ==========================================================
       STICKY ACTION BAR
    ========================================================== */

    .actionbar{
      position:sticky;
      bottom:12px;
      z-index:20;
    }

    .actionbar-inner{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      padding:11px 12px;
      border:1px solid rgba(216,225,238,.94);
      border-radius:17px;
      background:rgba(255,255,255,.92);
      box-shadow:0 15px 35px rgba(15,23,42,.11);
      backdrop-filter:blur(16px);
    }

    .actionbar .hint{
      display:flex;
      align-items:center;
      gap:8px;
      color:#667085;
      font-size:.62rem;
      font-weight:650;
    }

    /* ==========================================================
       HISTORY
    ========================================================== */

    .ae-history{
      overflow:hidden;
      margin-top:16px;
      border:1px solid var(--ae-line);
      border-radius:var(--ae-r-xl);
      background:#fff;
      box-shadow:var(--ae-shadow);
    }

    .ae-history-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:17px 18px;
      border-bottom:1px solid #EDF0F5;
      background:
        radial-gradient(330px 120px at 4% 0%,rgba(75,140,247,.06),transparent 72%),
        linear-gradient(180deg,#FFFFFF,#FBFCFF);
    }

    .ae-history-title{
      display:flex;
      align-items:center;
      gap:11px;
    }

    .ae-history-icon{
      width:42px;
      height:42px;
      flex:0 0 42px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:13px;
      color:#fff;
      background:
        linear-gradient(135deg,var(--ae-blue-500),var(--ae-blue-700));
      box-shadow:0 10px 22px rgba(32,66,127,.20);
    }

    .ae-history-title h2{
      margin:0;
      color:#182230;
      font-family:"Manrope","Inter",sans-serif;
      font-size:.96rem;
      font-weight:800;
    }

    .ae-history-title p{
      margin:3px 0 0;
      color:#98A2B3;
      font-size:.61rem;
      font-weight:600;
    }

    .ae-history-body{ padding:15px; }

    #tabla-historial{
      width:100%;
      margin:0 !important;
      border-collapse:separate !important;
      border-spacing:0 6px !important;
    }

    #tabla-historial thead th{
      padding:9px 10px !important;
      border:0 !important;
      color:#667085 !important;
      background:transparent !important;
      font-size:.58rem !important;
      font-weight:800 !important;
      letter-spacing:.38px;
      text-transform:uppercase;
      white-space:nowrap;
    }

    #tabla-historial tbody td{
      padding:10px !important;
      border-top:1px solid #E9EDF4 !important;
      border-bottom:1px solid #E9EDF4 !important;
      color:#344054;
      background:#fff;
      font-size:.65rem;
      vertical-align:middle;
    }

    #tabla-historial tbody td:first-child{
      border-left:1px solid #E9EDF4 !important;
      border-radius:12px 0 0 12px;
    }

    #tabla-historial tbody td:last-child{
      border-right:1px solid #E9EDF4 !important;
      border-radius:0 12px 12px 0;
    }

    #tabla-historial tbody tr:hover td{
      border-color:#DCE7F6 !important;
      background:linear-gradient(90deg,#F6FAFF,#FFFFFF) !important;
    }

    /* ==========================================================
       DATATABLES
    ========================================================== */

    .dataTables_wrapper{
      width:100% !important;
      color:#667085;
      font-size:.70rem;
    }

    .dataTables_wrapper .row{
      margin-left:0 !important;
      margin-right:0 !important;
      align-items:center;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter{
      margin-bottom:12px;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label{
      color:#667085;
      font-size:.65rem;
      font-weight:700;
    }

    .dataTables_wrapper .dataTables_filter input{
      min-height:38px;
      margin-left:8px;
      padding:0 11px;
      border:1px solid #D7DEE9;
      border-radius:10px;
      background:#fff;
      outline:none;
    }

    .dataTables_wrapper .dataTables_filter input:focus{
      border-color:var(--ae-blue-500);
      box-shadow:0 0 0 4px rgba(75,140,247,.10);
    }

    /* ==========================================================
       REVEAL
    ========================================================== */

    .reveal{
      animation:aeReveal .35s ease both;
    }

    @keyframes aeReveal{
      from{
        opacity:0;
        transform:translateY(8px);
      }
      to{
        opacity:1;
        transform:translateY(0);
      }
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){
      .ae-hero-grid{
        grid-template-columns:1fr;
      }

      .ae-kpis{
        min-width:0;
        width:100%;
      }
    }

    @media (max-width:991px){
      .content .container-fluid{
        padding-left:13px !important;
        padding-right:13px !important;
      }

      .ae-hero{
        padding:23px;
      }

      .ae-progress-rail{
        grid-template-columns:repeat(5,minmax(145px,1fr));
        overflow-x:auto;
      }
    }

    @media (max-width:767px){
      .content{
        padding-top:12px !important;
      }

      .content .container-fluid{
        padding-left:10px !important;
        padding-right:10px !important;
      }

      .ae-hero{
        min-height:0;
        padding:20px 17px;
        border-radius:22px;
      }

      .ae-hero h1{
        font-size:1.8rem;
      }

      .ae-hero p{
        font-size:.80rem;
      }

      .ae-kpis{
        grid-template-columns:repeat(2,1fr);
      }

      .ae-hero-actions .btn{
        flex:1 1 calc(50% - 8px);
      }

      .ae-flow-card,
      .ae-history{
        border-radius:19px;
      }

      .ae-flow-head,
      .ae-history-head{
        align-items:flex-start;
        padding:14px;
      }

      .ae-flow-body{
        padding:12px;
      }

      .step-head{
        align-items:flex-start;
      }

      .step-sub{
        line-height:1.45;
      }

      .actionbar-inner{
        align-items:stretch;
        flex-direction:column;
      }

      .actionbar-inner > .d-flex{
        width:100%;
      }

      .actionbar-inner .btn{
        flex:1;
      }

      #tabla-historial{
        min-width:850px;
      }

      #tabla-formulas{
        min-width:760px;
      }
    }

    @media (max-width:480px){
      .ae-kpis{
        gap:7px;
      }

      .ae-kpi{
        min-height:96px;
        padding:12px;
      }

      .ae-kpi strong{
        font-size:1.16rem;
      }

      .ae-kpi span{
        font-size:.56rem;
      }

      .ae-hero-actions .btn{
        flex:1 1 100%;
      }

      .actionbar .hint{
        display:none;
      }
    }

    @media (prefers-reduced-motion:reduce){
      *,
      *::before,
      *::after{
        animation-duration:.01ms !important;
        animation-iteration-count:1 !important;
        transition-duration:.01ms !important;
        scroll-behavior:auto !important;
      }
    }
  </style>
</head>

<body class="ae-page">
  <main class="main" id="top">
    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">
      <div class="container-fluid px-0 mt-4">

        <!-- =================================================
             ELECTORAL INTELLIGENCE HERO
        ================================================== -->
        <section class="ae-hero reveal">

          <div class="ae-hero-grid">

            <div>

              <div class="ae-eyebrow">
                <span class="ae-live-dot"></span>
                Estadística360 · Electoral Intelligence
              </div>

              <h1>
                Centro de
                <span>Análisis Electoral</span>
              </h1>

              <p>
                Selecciona grilla, candidato e indicador, aplica la fórmula
                correspondiente y documenta el resultado con cálculos,
                demografía y trazabilidad completa.
              </p>

              <div class="ae-hero-pills">
                <span class="ae-hero-pill">
                  <i class="fas fa-diagram-project"></i>
                  Flujo guiado de 5 pasos
                </span>
                <span class="ae-hero-pill">
                  <i class="fas fa-square-root-variable"></i>
                  Motor de indicadores
                </span>
                <span class="ae-hero-pill">
                  <i class="fas fa-users-viewfinder"></i>
                  Contexto demográfico
                </span>
              </div>

              <div class="ae-hero-actions">
                <button
                    type="button"
                    class="btn btn-soft"
                    onclick="document.getElementById('tbl_grilla_id')?.focus(); document.getElementById('tbl_grilla_id')?.scrollIntoView({behavior:'smooth',block:'center'});">
                  <i class="fas fa-search"></i>
                  Buscar grilla
                </button>

                <button
                    type="button"
                    class="btn btn-pro"
                    onclick="ANALISIS_ESTUDIO.verResultadosGrilla();">
                  <i class="fas fa-chart-column"></i>
                  Resultados de grilla
                </button>
              </div>

              <span id="spanModulo" class="d-none"><?php echo $modulo; ?></span>

            </div>

            <div class="ae-kpis">

              <div class="ae-kpi">
                <div class="ae-kpi-icon">
                  <i class="fas fa-table-cells-large"></i>
                </div>
                <strong><?= (int)$totalGrillasKpi ?></strong>
                <span>Grillas disponibles</span>
              </div>

              <div class="ae-kpi">
                <div class="ae-kpi-icon">
                  <i class="fas fa-calculator"></i>
                </div>
                <strong><?= (int)$totalFormulasKpi ?></strong>
                <span>Fórmulas configuradas</span>
              </div>

              <div class="ae-kpi">
                <div class="ae-kpi-icon">
                  <i class="fas fa-chart-pie"></i>
                </div>
                <strong><?= (int)$totalTiposKpi ?></strong>
                <span>Tipos de indicador</span>
              </div>

              <div class="ae-kpi">
                <div class="ae-kpi-icon">
                  <i class="fas fa-route"></i>
                </div>
                <strong><?= (int)$totalPasosKpi ?></strong>
                <span>Pasos del análisis</span>
              </div>

            </div>

          </div>

        </section>

        <!-- CARD PRINCIPAL -->
        <section class="ae-flow-card reveal">

          <div class="ae-flow-head">

            <div class="ae-flow-title">

              <div class="ae-flow-icon">
                <i class="fas fa-diagram-project"></i>
              </div>

              <div>
                <h2>Flujo de Análisis Electoral</h2>
                <p>Grilla → candidato → tipo → indicador → registro del análisis.</p>
              </div>

            </div>

            <span class="ae-flow-badge">
              <i class="fas fa-shield-halved"></i>
              Flujo controlado
            </span>

          </div>

          <div class="ae-flow-body">

            <div class="ae-progress-rail" aria-hidden="true">
              <div class="ae-progress-item">
                <span class="ae-progress-number">1</span>
                Grilla
              </div>
              <div class="ae-progress-item">
                <span class="ae-progress-number">2</span>
                Candidato
              </div>
              <div class="ae-progress-item">
                <span class="ae-progress-number">3</span>
                Tipo
              </div>
              <div class="ae-progress-item">
                <span class="ae-progress-number">4</span>
                Indicador
              </div>
              <div class="ae-progress-item">
                <span class="ae-progress-number">5</span>
                Resultado
              </div>
            </div>

            <!-- PASO 1 -->
            <div class="step-head mb-3">
              <div class="step-left">
                <div class="step-pill">1</div>
                <div>
                  <p class="step-title mb-0">Seleccione la Grilla de Estudio</p>
                  <p class="step-sub">Esto define el universo de candidatos e inferenciales.</p>
                </div>
              </div>
              <div class="mini-actions">
                <button type="button" class="btn btn-soft" onclick="ANALISIS_ESTUDIO.verResultadosGrilla();">
                  <i class="fas fa-eye me-2"></i>Ver Resultados
                </button>
              </div>
            </div>

            <div class="row g-3 mb-2">
              <div class="col-12 col-lg-8">
                <div class="form-floating">
                  <select class="form-select" id="tbl_grilla_id" name="tbl_grilla_id" onchange="ANALISIS_ESTUDIO.onGrillaChange();">
                    <option value="">Seleccione una grilla...</option>
                    <?php foreach ($grillas as $grilla): ?>
                      <option value="<?= htmlspecialchars($grilla['id']) ?>">
                        <?= htmlspecialchars($grilla['grilla']) ?> - <?= htmlspecialchars($grilla['tipo_inferenciales']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label for="tbl_grilla_id">Grilla<span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-12 col-lg-4">
                <button type="button" class="btn btn-pro w-100" onclick="ANALISIS_ESTUDIO.verResultadosGrilla();">
                  <i class="fas fa-eye me-2"></i>Ver Resultados Completos
                </button>
              </div>
            </div>

            <hr class="soft-hr">

            <!-- PASO 2 -->
            <div class="step-head mb-3" id="step2-head" style="opacity:.95;">
              <div class="step-left">
                <div class="step-pill" style="background: linear-gradient(135deg,#16a34a,#22c55e);">2</div>
                <div>
                  <p class="step-title mb-0">Seleccione el Candidato a Analizar</p>
                  <p class="step-sub">Al elegir candidato se habilitan indicadores y contexto.</p>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-4" id="seccion-candidatos" style="display:none;">
              <div class="col-12" id="candidatos-container">
                <div class="alert alert-info mb-0" style="border-radius:16px;">
                  <i class="fas fa-info-circle me-2"></i>
                  Seleccione una grilla para ver los candidatos disponibles
                </div>
              </div>
            </div>

            <hr class="soft-hr" id="hr-indicadores" style="display:none;">

            <!-- PASO 3 -->
            <div id="seccion-tipo-indicador" class="ae-step-block" style="display:none;">

              <div class="step-head mb-3">
                <div class="step-left">
                  <div class="step-pill" style="background:linear-gradient(135deg,#E99A16,#F7B84B);">3</div>
                  <div>
                    <p class="step-title mb-0">Seleccione el Tipo de Indicador</p>
                    <p class="step-sub">Filtra las fórmulas disponibles para el cálculo.</p>
                  </div>
                </div>
              </div>

              <div class="row g-3 mb-4" id="seccion-tipo-indicador-body">
                <div class="col-12">
                  <div class="form-floating">
                    <select
                        class="form-select"
                        id="tipo_indicador"
                        name="tipo_indicador"
                        onchange="ANALISIS_ESTUDIO.onTipoIndicadorChange();">
                      <option value="">Seleccione un tipo...</option>
                      <?php foreach ($tipos_indicadores as $tipo): ?>
                        <?php if (!empty($tipo)): ?>
                          <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </select>
                    <label for="tipo_indicador">
                      Tipo de Indicador<span class="text-danger">*</span>
                    </label>
                  </div>
                </div>
              </div>

            </div>

            <hr class="soft-hr" id="hr-formulas" style="display:none;">

            <!-- PASO 4 -->
            <div id="seccion-formulas" class="ae-step-block" style="display:none;">

              <div class="step-head mb-3">
                <div class="step-left">
                  <div class="step-pill" style="background:linear-gradient(135deg,#16A7CE,#51C9E5);">4</div>
                  <div>
                    <p class="step-title mb-0">Seleccione el Indicador a Calcular</p>
                    <p class="step-sub">Elige la fórmula; el contexto aparecerá en el banner.</p>
                  </div>
                </div>

                <span class="ae-flow-badge">
                  <i class="fas fa-list-check"></i>
                  Tabla dinámica
                </span>
              </div>

              <div class="row g-3 mb-4" id="seccion-formulas-body">
                <div class="col-12">
                  <div class="table-responsive">
                    <table
                        class="table table-sm table-hover align-middle"
                        id="tabla-formulas">
                      <thead>
                        <tr>
                          <th width="10%">Acción</th>
                          <th width="10%">Sigla</th>
                          <th width="40%">Indicador</th>
                          <th width="40%">Fórmula</th>
                        </tr>
                      </thead>
                      <tbody id="formulas-container">
                        <tr>
                          <td colspan="4" class="text-center text-muted py-4">
                            Seleccione un tipo de indicador para ver las fórmulas disponibles
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>

            <hr class="soft-hr" id="hr-analisis" style="display:none;">

            <!-- PASO 5 -->
            <div id="seccion-analisis" style="display:none;" class="reveal">
              <form id="formAnalisis" class="row g-3">
                <input type="hidden" id="id" name="id" value="0">
                <input type="hidden" id="tbl_participante_id" name="tbl_participante_id">
                <input type="hidden" id="tbl_formula_id" name="tbl_formula_id">

                <div class="step-head mb-2">
                  <div class="step-left">
                    <div class="step-pill" style="background: linear-gradient(135deg,#ef4444,#f97316);">5</div>
                    <div>
                      <p class="step-title mb-0">Registre el Resultado del Análisis</p>
                      <p class="step-sub">Deja trazabilidad y guarda el resultado con soporte de cálculos.</p>
                    </div>
                  </div>
                  <span class="badge" style="background: rgba(239,68,68,.10); border:1px solid rgba(239,68,68,.18); color:#b91c1c; border-radius:999px;">
                    <i class="fas fa-clipboard-check me-1"></i>Registro oficial
                  </span>
                </div>

                <!-- Banner Contexto -->
                <div class="col-12">
                  <div class="card analisis-banner-gradient">
                    <div class="card-body p-4">
                      <div class="text-center mb-3">
                        <h5 class="text-dark mb-2" style="font-weight:950;">
                          <i class="fas fa-clipboard-check me-2"></i>
                          <span class="analisis-badge-active">ANÁLISIS EN CURSO</span>
                        </h5>
                        <small class="text-dark">Revise candidato + indicador + fórmula antes de registrar el resultado.</small>
                      </div>

                      <div class="row g-3">
                        <div class="col-md-5">
                          <div class="card analisis-candidato-card h-100">
                            <div class="card-body text-center p-4">
                              <div class="mb-3">
                                <img id="foto-candidato-banner"
                                     src="assets/img/default-avatar.png"
                                     alt="Candidato"
                                     class="rounded-circle shadow-lg analisis-candidato-foto"
                                     style="width: 110px; height: 110px; object-fit: cover; display: none;">
                                <i class="fas fa-user-circle"
                                   id="icono-candidato-default"
                                   style="font-size: 4.5rem; color: rgba(46,88,168,.75);"></i>
                              </div>
                              <small class="text-muted d-block mb-2 fw-bold" style="letter-spacing: 2px;">CANDIDATO</small>
                              <h4 class="mb-0 fw-bold" id="info-candidato" style="font-size:1.35rem;line-height:1.25;color:var(--ae-blue-700);">-</h4>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-7">
                          <div class="row g-3 h-100">
                            <div class="col-12">
                              <div class="card analisis-info-card" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
                                <div class="card-body p-3">
                                  <div class="d-flex align-items-center">
                                    <div class="me-3">
                                      <i class="fas fa-calculator text-success" style="font-size: 2.2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                      <small class="text-muted d-block fw-bold" style="letter-spacing: 1px;">INDICADOR</small>
                                      <h5 class="mb-0 text-success fw-bold" id="info-indicador" style="font-size: 1.12rem;">-</h5>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="col-12">
                              <div class="card analisis-info-card" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
                                <div class="card-body p-3">
                                  <div class="d-flex align-items-center">
                                    <div class="me-3">
                                      <i class="fas fa-chart-bar text-warning" style="font-size: 2.2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                      <small class="text-muted d-block fw-bold" style="letter-spacing: 1px;">FÓRMULA</small>
                                      <p class="mb-0 fw-bold text-dark" id="info-formula" style="font-size: 0.95rem; line-height: 1.35;">-</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- Resultados de la grilla -->
                <div class="col-12 mt-2">
                  <h6 class="text-muted mb-2">
                    <i class="fas fa-poll me-2"></i>Resultados de Votación para este Candidato
                  </h6>
                  <div class="row g-3">
                    <div class="col-12 col-md-4">
                      <div class="result-card">
                        <div class="topline"></div>
                        <div class="card-body text-center p-3">
                          <h6 class="text-primary mb-1">¿Lo Conoce?</h6>
                          <p class="display-6 mb-0" id="resultado-conoce-si">0</p>
                          <small class="text-muted">Sí / <span id="resultado-conoce-no">0</span> No</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="result-card">
                        <div class="topline" style="background: linear-gradient(90deg,#16a34a,#22c55e);"></div>
                        <div class="card-body text-center p-3">
                          <h6 class="text-success mb-1">Imagen Favorable</h6>
                          <p class="display-6 mb-0" id="resultado-imagen-favorable">0</p>
                          <small class="text-muted">vs <span id="resultado-imagen-desfavorable">0</span> Desfavorable</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="result-card">
                        <div class="topline" style="background: linear-gradient(90deg,#ef4444,#f97316);"></div>
                        <div class="card-body text-center p-3">
                          <h6 class="text-danger mb-1">¿Votaría por él?</h6>
                          <p class="display-6 mb-0" id="resultado-votaria-si">0</p>
                          <small class="text-muted">Sí / <span id="resultado-votaria-no">0</span> No</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Demografía -->
                <div class="col-12" id="seccion-demografia" style="display:none;">
                  <div class="mini-module">
                    <div class="module-head">
                      <div>
                        <h6 class="mb-0" style="font-weight:950; color: var(--nav-blue);">
                          <i class="fas fa-users me-2"></i>Perfil Demográfico de Votantes
                        </h6>
                        <small class="text-muted">Características de los votantes que participaron en la grilla</small>
                      </div>
                      <button class="btn btn-sm btn-soft" type="button" data-bs-toggle="collapse" data-bs-target="#demografia-content">
                        <i class="fas fa-chevron-down"></i>
                      </button>
                    </div>
                    <div class="module-body">
                      <div class="collapse show" id="demografia-content">
                        <div class="row g-2" id="demografia-container">
                          <div class="col-12 text-center text-muted py-2 small">
                            Cargando datos demográficos...
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Resultado calculado -->
                <div class="col-12 col-lg-6">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="resultado_calculado" name="resultado_calculado" placeholder="Ingrese el resultado" required>
                    <label for="resultado_calculado">Resultado Calculado<span class="text-danger">*</span></label>
                  </div>
                  <small class="text-muted">Ej: 45.5%, 1250 votos, etc.</small>
                </div>

                <!-- Texto -->
                <div class="col-12 col-lg-6">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="texto_resultado_calculado" name="texto_resultado_calculado" placeholder="Texto descriptivo" maxlength="20">
                    <label for="texto_resultado_calculado">Texto Descriptivo del Resultado</label>
                  </div>
                  <small class="text-muted">Máximo 20 caracteres. <span id="contador-caracteres">0/20</span></small>
                </div>

                <!-- Calculadora -->
                <div class="col-12">
                  <div class="mini-module">
                    <div class="module-head">
                      <div>
                        <h6 class="mb-0" style="font-weight:950; color:#16a34a;">
                          <i class="fas fa-calculator me-2"></i>Calculadora de Operaciones
                        </h6>
                        <small class="text-muted">Documente los cálculos para justificar el resultado final</small>
                      </div>
                      <button class="btn btn-sm btn-soft" type="button" data-bs-toggle="collapse" data-bs-target="#calculadora-content">
                        <i class="fas fa-chevron-down"></i>
                      </button>
                    </div>

                    <div class="module-body">
                      <div class="collapse show" id="calculadora-content">
                        <div id="operaciones-container" class="mb-2"></div>

                        <button type="button" class="btn btn-sm btn-success" onclick="ANALISIS_ESTUDIO.agregarOperacion()">
                          <i class="fas fa-plus me-1"></i>Agregar Operación
                        </button>

                        <div class="mt-3 p-2 bg-white border rounded" id="resumen-calculos" style="display:none;">
                          <small class="text-muted d-block mb-1"><strong>Resumen de Cálculos:</strong></small>
                          <div id="resumen-operaciones" class="small"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Observaciones -->
                <div class="col-12">
                  <div class="form-floating">
                    <textarea class="form-control" id="observaciones" name="observaciones" placeholder="Observaciones" style="height: 110px;"></textarea>
                    <label for="observaciones">Observaciones y Notas del Investigador</label>
                  </div>
                </div>

                <!-- Action bar sticky (mejor UX) -->
                <div class="col-12 actionbar mt-2">
                  <div class="actionbar-inner">
                    <div class="hint">
                      <i class="fas fa-circle-info me-2"></i>
                      Consejo: revisa indicador y fórmula antes de guardar.
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                      <button type="button" onclick="ANALISIS_ESTUDIO.limpiarFormulario();" class="btn btn-soft px-4">
                        <i class="fas fa-times me-2"></i>Cancelar
                      </button>
                      <button type="button" onclick="ANALISIS_ESTUDIO.guardarAnalisis();" class="btn btn-pro px-4">
                        <i class="fas fa-save me-2"></i>Guardar Análisis
                      </button>
                    </div>
                  </div>
                </div>

              </form>
            </div>

          </div>

        </section>

        <!-- TABLA HISTORIAL -->
        <section class="ae-history reveal">

          <div class="ae-history-head">

            <div class="ae-history-title">

              <div class="ae-history-icon">
                <i class="fas fa-history"></i>
              </div>

              <div>
                <h2>Historial de Análisis Realizados</h2>
                <p>Consulta resultados previamente registrados y su trazabilidad.</p>
              </div>

            </div>

            <span class="ae-flow-badge">
              <i class="fas fa-database"></i>
              Registro persistente
            </span>

          </div>

          <div class="ae-history-body">

            <div class="table-responsive">
              <table id="tabla-historial" class="table table-striped table-sm align-middle fs-9 mb-0">
                <thead>
                  <tr>
                    <th>Acciones</th>
                    <th>Grilla</th>
                    <th>Candidato</th>
                    <th>Indicador</th>
                    <th>Resultado</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody id="historial-container">
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                      No hay análisis registrados
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>

        </section>

      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </main>

  <!-- Required Js -->
  <?php include 'admin/include/gerenic_script.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
  <?php include './admin/include/generic_dataTables.php'; ?>

  <script type="text/javascript" src="admin/js/analisis_estudio.js"></script>
  <?php include 'admin/include/scriptsgober360.php'; ?>

  <script>
    /*
      Inicialización original del módulo.
      Se mantiene llamada directa para no interferir con la forma
      en que analisis_estudio.js declara ANALISIS_ESTUDIO.
    */
    ANALISIS_ESTUDIO.init();
  </script>
</body>
</html>
