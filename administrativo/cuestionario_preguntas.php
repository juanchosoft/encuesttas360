<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';
// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';


include './admin/classes/Departamento.php';
include './admin/classes/Pregunta.php';
include_once './admin/classes/Votantes.php';
include './admin/classes/FichaTecnicaEncuesta.php';
include './admin/classes/GrillaPlantilla.php';

$view = SessionData::hasPermission('encuestas.preguntas.view');
$create = SessionData::hasPermission('encuestas.preguntas.create');
$edit = SessionData::hasPermission('encuestas.preguntas.update');
$permits = SessionData::hasPermission('encuestas.preguntas.manage');
$puedeUsarBanco = $create || $edit;
$puedeGuardarBanco = SessionData::hasPermission('encuestas.banco_preguntas.create');
$delete = SessionData::hasPermission('encuestas.preguntas.delete');
$reglasPerm = SessionData::hasPermission('encuestas.preguntas.reglas') || $edit;
$cuotasPerm = SessionData::hasPermission('encuestas.cuotas.edit') || $edit;
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function nombreLegibleTipoPregunta($tipo) {
    $map = [
        'Dicotomica' => 'Sí / No (Dos opciones)',
        'Seleccion_Multiple_unica_respuesta' => 'Escoge una opción',
        'Seleccion_Multiple_multiple_respuesta' => 'Puede escoger varias',
        'Preguntas_Ordinales' => 'Escala con orden (ej. 1 a 5)',
        'Preguntas_Cardinales' => 'Nivel de satisfacción / intensidad',
        'Grilla' => 'Tabla de candidatos / ítems',
        'Lista_desde_grilla' => 'Escoger de los candidatos de una tabla',
        'Texto Libre' => 'Respuesta escrita abierta',
        'Abierta' => 'Respuesta escrita abierta'
    ];
    return $map[$tipo] ?? ($tipo ?: 'Sin tipo');
}

$fichaId = isset($_GET['ficha_id']) ? (int)$_GET['ficha_id'] : 0;
if ($fichaId <= 0) {
    header('Location: preguntas.php');
    exit;
}

$fichaResp = FichaTecnicaEncuesta::getAll(['id' => $fichaId]);
if (!($fichaResp['output']['valid'] ?? false) || empty($fichaResp['output']['response'])) {
    header('Location: preguntas.php?err=ficha');
    exit;
}

$fichaActual = $fichaResp['output']['response'][0];
if (($fichaActual['eliminado'] ?? 'no') === 'si') {
    header('Location: preguntas.php?err=eliminada');
    exit;
}

$todasFichasResp = FichaTecnicaEncuesta::getAll([]);
$arrFichaTecnicaEncuesta = $todasFichasResp['output']['response'] ?? [];

$plantillasResp = GrillaPlantilla::getAll([]);
$arrPlantillasGrilla = $plantillasResp['output']['response'] ?? [];
$plantillasPorIdMap = [];
foreach ($arrPlantillasGrilla as $plRow) {
    $plantillasPorIdMap[(int)$plRow['id']] = $plRow;
}

$arrPregunta = Pregunta::getAll(['tbl_ficha_tecnica_encuesta_id' => $fichaId]);
$isvalidPregunta = $arrPregunta['output']['valid'] ?? false;
$arrPregunta = $arrPregunta['output']['response'] ?? [];
$preguntasPorIdMap = [];
foreach ($arrPregunta as $prRow) {
    $preguntasPorIdMap[(int)$prRow['id']] = $prRow;
}

$fichaLabel = trim(
    ($fichaActual['temas_concretos'] ?? '') !== ''
        ? ($fichaActual['temas_concretos'] ?? '')
        : ($fichaActual['realizada_por_o_encomendada_por'] ?? 'Ficha #' . $fichaId)
);

$optionEncuentas = "<option value='" . (int)$fichaId . "' selected>"
    . h($fichaLabel) . "</option>";

$modulo = 'Preguntas del cuestionario';

$totalPreguntasKpi = is_array($arrPregunta) ? count($arrPregunta) : 0;
$totalActivasKpi = 0;
$totalFichasKpi = 1;
$totalCapitulosKpi = 0;
$totalConEnunciadoKpi = 0;
$capitulosUnicosKpi = [];

if (is_array($arrPregunta)) {
    foreach ($arrPregunta as $preguntaKpi) {
        if (($preguntaKpi['habilitado'] ?? '') === 'si') {
            $totalActivasKpi++;
        }
        if (!empty($preguntaKpi['capitulo'])) {
            $capitulosUnicosKpi[trim((string)$preguntaKpi['capitulo'])] = true;
        }
        if (!empty($preguntaKpi['enunciado_pregunta'])) {
            $totalConEnunciadoKpi++;
        }
    }
}
$totalCapitulosKpi = count($capitulosUnicosKpi);

$maxOrden = 0;
if (is_array($arrPregunta)) {
    foreach ($arrPregunta as $preguntaOrden) {
        $o = (int)($preguntaOrden['orden'] ?? 0);
        if ($o > $maxOrden) {
            $maxOrden = $o;
        }
    }
}
$siguienteOrden = $totalPreguntasKpi > 0 ? $maxOrden + 1 : 1;
?>
<!-- *******************inicio body************************* -->

<body class="qst-page">
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
    /* ==========================================================
       ESTADÍSTICA360 · QUESTIONNAIRE INTELLIGENCE STUDIO
       ----------------------------------------------------------
       Diseño visual. Se conservan IDs, funciones PREGUNTAS,
       OPCIONES, modales y estructura funcional existente.
    ========================================================== */

    :root{
      --qst-navy-950:#07182F;
      --qst-navy-900:#0A2248;
      --qst-navy-800:#123A74;

      --qst-blue-700:#20427F;
      --qst-blue-600:#2D63BD;
      --qst-blue-500:#4B8CF7;
      --qst-cyan:#20B8DB;
      --qst-violet:#7568E8;

      --qst-success:#12B981;
      --qst-warning:#F59E0B;
      --qst-danger:#E5484D;

      --qst-page:#F3F6FB;
      --qst-card:#FFFFFF;
      --qst-soft:#F8FAFD;

      --qst-text:#101828;
      --qst-text-2:#344054;
      --qst-muted:#667085;
      --qst-light:#98A2B3;

      --qst-line:#E5EAF1;

      --qst-r-xxl:30px;
      --qst-r-xl:24px;
      --qst-r-lg:18px;
      --qst-r-md:14px;

      --qst-shadow:0 24px 68px rgba(15,23,42,.10);
      --qst-shadow-soft:0 12px 34px rgba(15,23,42,.065);
    }

    *{ box-sizing:border-box; }

    html{ scroll-behavior:smooth; }

    body.qst-page{
      margin:0;
      color:var(--qst-text);
      font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased;

      background:
        radial-gradient(920px 500px at 3% -5%,rgba(75,140,247,.12),transparent 64%),
        radial-gradient(760px 440px at 103% 5%,rgba(117,104,232,.07),transparent 64%),
        linear-gradient(180deg,#F8FAFD 0%,#F2F5FA 100%);
    }

    body.qst-page::before{
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

    /* No calculamos altura desde .navbar: evita huecos gigantes */
    .content{
      padding-top:18px !important;
      padding-bottom:38px !important;
      margin-top:0 !important;
    }

    .qst-shell{
      width:100%;
      max-width:1660px;
      margin:0 auto;
      padding:0 18px;
    }

    /* ==========================================================
       HERO
    ========================================================== */

    .qst-hero{
      position:relative;
      isolation:isolate;
      overflow:hidden;
      min-height:226px;
      margin-bottom:16px;
      padding:29px 30px;
      border:1px solid rgba(255,255,255,.12);
      border-radius:var(--qst-r-xxl);
      color:#fff;
      background:
        radial-gradient(550px 275px at 9% 0%,rgba(75,140,247,.35),transparent 66%),
        radial-gradient(470px 270px at 95% 10%,rgba(32,184,219,.18),transparent 67%),
        linear-gradient(135deg,#173E7B 0%,#102A56 47%,#07162E 100%);
      box-shadow:0 30px 80px rgba(8,28,63,.24);
    }

    .qst-hero::before{
      content:"";
      position:absolute;
      z-index:-1;
      width:430px;
      height:430px;
      right:-155px;
      top:-220px;
      border:1px solid rgba(255,255,255,.075);
      border-radius:50%;
      box-shadow:
        0 0 0 44px rgba(255,255,255,.021),
        0 0 0 90px rgba(255,255,255,.015),
        0 0 0 136px rgba(255,255,255,.010);
    }

    .qst-hero-grid{
      display:grid;
      grid-template-columns:minmax(0,1fr) auto;
      gap:28px;
      align-items:center;
    }

    .qst-eyebrow{
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

    .qst-live-dot{
      width:7px;
      height:7px;
      border-radius:50%;
      background:#5DE4A0;
      box-shadow:
        0 0 0 5px rgba(93,228,160,.11),
        0 0 16px rgba(93,228,160,.45);
    }

    .qst-hero h1{
      margin:0;
      color:#fff;
      font-family:"Manrope","Inter",sans-serif;
      font-size:clamp(1.85rem,3vw,2.95rem);
      line-height:1.04;
      font-weight:800;
      letter-spacing:-1.45px;
    }

    .qst-hero h1 span{ color:#B7D0FF; }

    .qst-hero p{
      max-width:850px;
      margin:11px 0 0;
      color:rgba(255,255,255,.70);
      font-size:.91rem;
      line-height:1.67;
      font-weight:500;
    }

    .qst-hero-pills{
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:18px;
    }

    .qst-hero-pill{
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

    .qst-hero-pill i{ color:#A7C7FF; }

    /* ==========================================================
       HERO KPI
    ========================================================== */

    .qst-kpis{
      display:grid;
      grid-template-columns:repeat(4,minmax(92px,1fr));
      gap:9px;
      min-width:550px;
    }

    .qst-kpi{
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

    .qst-kpi:hover{
      transform:translateY(-4px);
      border-color:rgba(255,255,255,.20);
      background:
        linear-gradient(145deg,rgba(255,255,255,.17),rgba(255,255,255,.07));
    }

    .qst-kpi-icon{
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

    .qst-kpi strong{
      display:block;
      color:#fff;
      font-family:"Manrope","Inter",sans-serif;
      font-size:1.36rem;
      line-height:1;
      font-weight:800;
      letter-spacing:-.55px;
    }

    .qst-kpi span{
      display:block;
      margin-top:5px;
      color:rgba(255,255,255,.58);
      font-size:.59rem;
      line-height:1.25;
      font-weight:700;
    }

    /* ==========================================================
       COMMAND BAR
    ========================================================== */

    .qst-command{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:16px;
      padding:13px 15px;
      border:1px solid var(--qst-line);
      border-radius:18px;
      background:rgba(255,255,255,.92);
      box-shadow:var(--qst-shadow-soft);
      backdrop-filter:blur(12px);
    }

    .qst-command-copy{
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
    }

    .qst-command-icon{
      width:38px;
      height:38px;
      flex:0 0 38px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:12px;
      color:var(--qst-blue-700);
      background:#EDF4FF;
      font-size:.9rem;
    }

    .qst-command-copy strong{
      display:block;
      color:var(--qst-text);
      font-size:.79rem;
      font-weight:800;
    }

    .qst-command-copy span{
      display:block;
      margin-top:2px;
      color:var(--qst-light);
      font-size:.66rem;
      font-weight:600;
    }

    .qst-command-actions{
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      align-items:center;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .qst-btn{
      min-height:43px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:9px 15px;
      border-radius:12px;
      font-size:.72rem;
      font-weight:800;
      text-decoration:none !important;
      transition:
        transform .18s ease,
        box-shadow .18s ease,
        border-color .18s ease,
        background .18s ease;
    }

    .qst-btn-primary{
      border:0;
      color:#fff !important;
      background:
        linear-gradient(135deg,var(--qst-blue-500),var(--qst-blue-600) 50%,var(--qst-blue-700));
      box-shadow:0 11px 23px rgba(32,66,127,.22);
    }

    .qst-btn-primary:hover{
      transform:translateY(-2px);
      box-shadow:0 16px 30px rgba(32,66,127,.29);
    }

    .qst-btn-success{
      border:0;
      color:#fff !important;
      background:linear-gradient(135deg,#2FC38E,#0A8463);
      box-shadow:0 11px 23px rgba(10,132,99,.20);
    }

    .qst-btn-violet{
      border:0;
      color:#fff !important;
      background:linear-gradient(135deg,#7C6AF1,#5145B5);
      box-shadow:0 11px 23px rgba(81,69,181,.20);
    }

    .qst-btn-soft{
      border:1px solid #D7E2F2;
      color:var(--qst-blue-700) !important;
      background:#fff;
    }

    .qst-btn-soft:hover{
      transform:translateY(-1px);
      border-color:#BFD2EC;
      background:#F5F9FF;
    }

    /* ==========================================================
       HELP CENTER
    ========================================================== */

    .saas-card,
    .qst-help-card{
      overflow:hidden;
      border:1px solid var(--qst-line) !important;
      border-radius:var(--qst-r-xl) !important;
      background:#fff !important;
      box-shadow:var(--qst-shadow-soft) !important;
    }

    .qst-help-card{
      margin-bottom:16px;
    }

    .qst-help-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:15px 18px;
      border-bottom:1px solid #EDF0F5;
      background:
        radial-gradient(300px 110px at 4% 0%,rgba(75,140,247,.06),transparent 72%),
        linear-gradient(180deg,#FFFFFF,#FBFCFF);
    }

    .qst-help-title{
      display:flex;
      align-items:center;
      gap:11px;
    }

    .qst-help-icon{
      width:40px;
      height:40px;
      flex:0 0 40px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:13px;
      color:var(--qst-blue-700);
      background:#EDF4FF;
    }

    .qst-help-title strong{
      display:block;
      color:#182230;
      font-size:.79rem;
      font-weight:800;
    }

    .qst-help-title span{
      display:block;
      margin-top:2px;
      color:var(--qst-light);
      font-size:.61rem;
      font-weight:600;
    }

    .qst-help-body{
      padding:14px;
      background:#FBFCFE;
    }

    .accordion-item{
      overflow:hidden;
      margin-bottom:9px;
      border:1px solid #E5EAF1 !important;
      border-radius:14px !important;
      box-shadow:0 7px 18px rgba(15,23,42,.035);
    }

    .accordion-item:last-child{ margin-bottom:0; }

    .accordion-button{
      min-height:52px;
      color:var(--qst-text-2) !important;
      background:#fff !important;
      font-size:.70rem;
      font-weight:800;
      box-shadow:none !important;
    }

    .accordion-button:not(.collapsed){
      color:var(--qst-blue-700) !important;
      background:
        linear-gradient(90deg,#F2F7FF,#FFFFFF) !important;
    }

    .accordion-body{
      color:#475467;
      background:#fff;
      font-size:.72rem;
      line-height:1.6;
    }

    .help-grid .card{
      overflow:hidden;
      border:1px solid #E4EAF2 !important;
      border-radius:14px !important;
      box-shadow:0 9px 22px rgba(15,23,42,.05);
    }

    .help-grid .card-header{
      padding:10px 12px !important;
      font-size:.68rem;
      font-weight:800;
    }

    .help-grid .card-body{
      padding:12px !important;
    }

    /* ==========================================================
       FICHA GROUPS
    ========================================================== */

    .qst-groups-area{
      padding:0 !important;
      margin-top:0;
    }

    .saas-card.mb-4{
      margin-bottom:14px !important;
    }

    .saas-card .card-header-grupo{
      padding:12px 14px !important;
      border-bottom:0 !important;
      background:
        radial-gradient(280px 120px at 5% 0%,rgba(75,140,247,.30),transparent 72%),
        linear-gradient(135deg,#183F7D,#0B244A) !important;
    }

    .saas-card .card-header-grupo .fw-bold.text-white{
      font-family:"Manrope","Inter",sans-serif;
      font-size:.83rem !important;
      letter-spacing:-.1px;
    }

    .saas-card .card-header-grupo .btn{
      min-height:31px;
      border-radius:9px !important;
      font-size:.63rem !important;
      font-weight:750 !important;
      transition:transform .18s ease,background .18s ease;
    }

    .saas-card .card-header-grupo .btn:hover{
      transform:translateY(-1px);
      background:rgba(255,255,255,.20) !important;
    }

    /* Chapter bars (the existing inline background remains functional) */
    [id^="grupoBody"] > .px-3{
      padding-left:14px !important;
      padding-right:14px !important;
    }

    [id^="grupoBody"] .rounded-3.overflow-hidden{
      border-radius:13px !important;
      box-shadow:0 8px 22px rgba(32,66,127,.12);
    }

    [id^="capituloBody"]{
      padding-top:5px;
    }

    /* Enunciado bar */
    [id^="enunciadoGrupoTexto"]{
      color:#275FAF !important;
    }

    [id^="capituloBody"] .rounded-3:not(.overflow-hidden),
    [id^="grupoBody"] > .px-3 > .p-2.rounded-3{
      border-radius:12px !important;
    }

    /* ==========================================================
       QUESTION TABLES
    ========================================================== */

    .saas-card .table-responsive{
      padding:0 12px 11px;
      overflow-x:auto;
      -webkit-overflow-scrolling:touch;
    }

    .saas-card table.table{
      width:100%;
      min-width:1040px;
      margin:0 !important;
      border-collapse:separate !important;
      border-spacing:0 6px !important;
    }

    .saas-card table.table thead th{
      padding:9px 10px !important;
      border:0 !important;
      color:#667085 !important;
      background:transparent !important;
      font-size:.57rem !important;
      font-weight:800 !important;
      letter-spacing:.38px;
      text-transform:uppercase;
      white-space:nowrap;
    }

    .saas-card table.table tbody td{
      padding:10px !important;
      border-top:1px solid #E9EDF4 !important;
      border-bottom:1px solid #E9EDF4 !important;
      color:#344054 !important;
      background:#fff !important;
      font-size:.65rem !important;
      line-height:1.45;
      vertical-align:middle !important;
      transition:
        background .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
    }

    .saas-card table.table tbody td:first-child{
      border-left:1px solid #E9EDF4 !important;
      border-radius:12px 0 0 12px;
    }

    .saas-card table.table tbody td:last-child{
      border-right:1px solid #E9EDF4 !important;
      border-radius:0 12px 12px 0;
    }

    .saas-card table.table tbody tr{
      transition:transform .18s ease;
    }

    .saas-card table.table tbody tr:hover{
      transform:translateY(-1px);
    }

    .saas-card table.table tbody tr:hover td{
      border-color:#DCE7F6 !important;
      background:linear-gradient(90deg,#F6FAFF,#FFFFFF) !important;
      box-shadow:0 8px 20px rgba(15,23,42,.045);
    }

    .saas-card table.table tbody td.fw-semibold{
      color:#1D2939 !important;
      font-weight:750 !important;
    }

    /* ==========================================================
       CHECKBOXES
    ========================================================== */

    .pregunta-chk,
    .grupo-chk-all,
    .capitulo-chk-all{
      width:17px !important;
      height:17px !important;
      margin:0 auto !important;
      border:2px solid rgba(32,66,127,.34) !important;
      border-radius:5px !important;
      cursor:pointer;
      box-shadow:none !important;
      transition:
        background .15s ease,
        border-color .15s ease,
        transform .15s ease;
    }

    .pregunta-chk:hover,
    .grupo-chk-all:hover,
    .capitulo-chk-all:hover{
      transform:scale(1.05);
      border-color:var(--qst-blue-700) !important;
    }

    .pregunta-chk:checked,
    .grupo-chk-all:checked,
    .capitulo-chk-all:checked{
      border-color:var(--qst-blue-700) !important;
      background-color:var(--qst-blue-700) !important;
    }

    /* ==========================================================
       GENERIC BUTTONS INSIDE GROUPS
    ========================================================== */

    .saas-card .btn-sm{
      border-radius:8px !important;
      font-size:.62rem !important;
      font-weight:750;
    }

    .saas-card .btn-primary{
      border:0 !important;
      background:linear-gradient(135deg,#4F8CFF,#2563B9) !important;
      box-shadow:0 7px 14px rgba(37,99,185,.13);
    }

    .saas-card .btn-outline-warning,
    .saas-card .btn-outline-success,
    .saas-card .btn-outline-danger,
    .saas-card .btn-outline-info,
    .saas-card .btn-outline-secondary{
      background:#fff;
    }

    /* ==========================================================
       FORMS
    ========================================================== */

    .form-floating>.form-control,
    .form-floating>.form-select,
    .modal .form-control,
    .modal .form-select{
      border:1px solid #D9E0EA !important;
      border-radius:12px !important;
      color:var(--qst-text-2);
      background:#FBFCFE;
      font-size:.75rem;
      font-weight:600;
      box-shadow:none !important;
      transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background .18s ease;
    }

    .form-floating>.form-control,
    .form-floating>.form-select{
      min-height:56px;
    }

    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus,
    .modal .form-control:focus,
    .modal .form-select:focus{
      border-color:var(--qst-blue-500) !important;
      background:#fff;
      box-shadow:0 0 0 4px rgba(75,140,247,.10) !important;
    }

    .form-floating>label{
      color:#667085;
      font-size:.75rem;
      font-weight:650;
    }

    /* ==========================================================
       MODALS
    ========================================================== */

    .modal .modal-content{
      overflow:hidden;
      border:1px solid rgba(15,23,42,.09) !important;
      border-radius:22px !important;
      box-shadow:0 30px 82px rgba(15,23,42,.25) !important;
    }

    .modal .modal-header{
      position:relative;
      overflow:hidden;
      padding:17px 20px !important;
      border-bottom:0 !important;
      color:#fff !important;
      background:
        radial-gradient(400px 190px at 5% 0%,rgba(75,140,247,.28),transparent 72%),
        radial-gradient(330px 170px at 100% 0%,rgba(117,104,232,.20),transparent 70%),
        linear-gradient(135deg,#173D79,#102A56 55%,#081B38) !important;
    }

    .modal .modal-header::after{
      content:"";
      position:absolute;
      width:180px;
      height:180px;
      right:-85px;
      top:-110px;
      pointer-events:none;
      border:1px solid rgba(255,255,255,.08);
      border-radius:50%;
      box-shadow:0 0 0 28px rgba(255,255,255,.02);
    }

    .modal .modal-title{
      position:relative;
      z-index:2;
      color:#fff !important;
      font-family:"Manrope","Inter",sans-serif;
      font-size:.96rem !important;
      font-weight:800 !important;
    }

    .modal .modal-header small{
      position:relative;
      z-index:2;
      color:rgba(255,255,255,.66) !important;
      font-size:.61rem !important;
    }

    .modal .btn-close,
    .modal .btn-close-white{
      position:relative;
      z-index:3;
      filter:invert(1) brightness(2);
      opacity:.88;
    }

    .modal .modal-body{
      background:linear-gradient(180deg,#FBFCFE,#F5F8FC) !important;
    }

    .modal .modal-footer{
      padding:11px 17px !important;
      border-top:1px solid #E7EBF1 !important;
      background:#fff !important;
    }

    /* Modal options */
    #modalOptionsList .card,
    #modalOptionsList .alert{
      border-radius:12px !important;
    }

    /* Batch modal */
    #batchModal .modal-dialog{
      max-width:1360px;
    }

    #batchStep1,
    #batchStep2{
      background:
        radial-gradient(350px 170px at 50% 0%,rgba(75,140,247,.055),transparent 70%),
        #F8FAFD !important;
    }

    #batchQuestionsContainer > *{
      border-radius:14px;
    }

    /* Upload iframe */
    #dropzone-foto1{
      overflow:hidden;
      border:1px dashed #BFCFE3 !important;
      border-radius:15px;
      background:#fff;
    }

    #ifm1{
      display:block;
      border-radius:14px;
      background:#fff;
    }

    /* ==========================================================
       BADGES
    ========================================================== */

    .badge{
      font-weight:750;
      letter-spacing:.02em;
    }

    .badge.bg-success{
      color:#06795B !important;
      border:1px solid #D1FAE5;
      background:#ECFDF5 !important;
    }

    .badge.bg-danger{
      color:#B42318 !important;
      border:1px solid #FEE4E2;
      background:#FEF3F2 !important;
    }

    .badge.bg-secondary{
      color:#475467 !important;
      border:1px solid #EAECF0;
      background:#F9FAFB !important;
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){
      .qst-hero-grid{
        grid-template-columns:1fr;
      }

      .qst-kpis{
        min-width:0;
        width:100%;
      }
    }

    @media (max-width:991px){
      .qst-shell{
        padding:0 13px;
      }

      .qst-hero{
        padding:23px;
      }

      .qst-command{
        align-items:flex-start;
        flex-direction:column;
      }

      .qst-command-actions{
        width:100%;
      }
    }

    @media (max-width:767px){
      .content{
        padding-top:12px !important;
      }

      .qst-shell{
        padding:0 10px;
      }

      .qst-hero{
        min-height:0;
        padding:20px 17px;
        border-radius:22px;
      }

      .qst-hero h1{
        font-size:1.8rem;
      }

      .qst-hero p{
        font-size:.80rem;
      }

      .qst-kpis{
        grid-template-columns:repeat(2,1fr);
      }

      .qst-command-actions .qst-btn{
        flex:1 1 calc(50% - 8px);
      }

      .qst-help-head{
        align-items:flex-start;
      }

      .saas-card{
        border-radius:18px !important;
      }

      .saas-card .card-header-grupo{
        padding:12px !important;
      }

      .saas-card .table-responsive{
        padding:0 8px 8px;
      }

      .modal .modal-body{
        padding:13px !important;
      }
    }

    @media (max-width:480px){
      .qst-kpis{
        gap:7px;
      }

      .qst-kpi{
        min-height:96px;
        padding:12px;
      }

      .qst-kpi strong{
        font-size:1.16rem;
      }

      .qst-kpi span{
        font-size:.56rem;
      }

      .qst-command-actions .qst-btn{
        flex:1 1 100%;
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

  <!-- [ Main Content ] start -->
  <div class="content">
    <div class="qst-shell">

      <!-- =================================================
           QUESTIONNAIRE INTELLIGENCE HERO
      ================================================== -->
      <section class="qst-hero">
        <div class="qst-hero-grid">

          <div>
            <div class="qst-eyebrow">
              <span class="qst-live-dot"></span>
              Estadística360 · Questionnaire Intelligence
            </div>

            <div class="mb-2">
              <a href="preguntas.php" class="btn btn-sm btn-light" style="border-radius:999px;font-weight:700;">
                <i class="fas fa-arrow-left me-1"></i>Volver al listado
              </a>
            </div>
            <h1>
              <span><?= h($fichaLabel) ?></span>
            </h1>

            <p>
              Diseña y administra cuestionarios por ficha técnica, capítulo y
              enunciado; controla opciones, numerales, visibilidad y estado,
              y ejecuta cargas masivas sin perder la trazabilidad del estudio.
            </p>

            <div class="qst-hero-pills">
              <span class="qst-hero-pill">
                <i class="fas fa-layer-group"></i>
                Organización por capítulos
              </span>
              <span class="qst-hero-pill">
                <i class="fas fa-file-csv"></i>
                Importación CSV / Excel
              </span>
              <span class="qst-hero-pill">
                <i class="fas fa-list-check"></i>
                Gestión masiva
              </span>
            </div>

            <div class="small mt-3" id="spanEncuesta"
                 style="color:rgba(255,255,255,.58);font-weight:650;"></div>
            <span id="spanModulo" class="d-none"><?php echo $modulo; ?></span>
          </div>

          <div class="qst-kpis">

            <div class="qst-kpi">
              <div class="qst-kpi-icon">
                <i class="fas fa-circle-question"></i>
              </div>
              <strong><?= (int)$totalPreguntasKpi ?></strong>
              <span>Preguntas registradas</span>
            </div>

            <div class="qst-kpi">
              <div class="qst-kpi-icon">
                <i class="fas fa-circle-check"></i>
              </div>
              <strong><?= (int)$totalActivasKpi ?></strong>
              <span>Preguntas activas</span>
            </div>

            <div class="qst-kpi">
              <div class="qst-kpi-icon">
                <i class="fas fa-clipboard-list"></i>
              </div>
              <strong>#<?= (int)$fichaId ?></strong>
              <span>ID ficha técnica</span>
            </div>

            <div class="qst-kpi">
              <div class="qst-kpi-icon">
                <i class="fas fa-layer-group"></i>
              </div>
              <strong><?= (int)$totalCapitulosKpi ?></strong>
              <span>Capítulos configurados</span>
            </div>

          </div>
        </div>
      </section>

      <!-- =================================================
           COMMAND CENTER
      ================================================== -->
      <section class="qst-command">

        <div class="qst-command-copy">
          <div class="qst-command-icon">
            <i class="fas fa-compass"></i>
          </div>

          <div>
            <strong>Centro de administración del cuestionario</strong>
            <span>Crea preguntas, agrega tablas de candidatos, configura reglas y prueba cómo se verá la encuesta.</span>
          </div>
        </div>

        <div class="qst-command-actions">

          <?php if ($create || $edit): ?>
          <button
              onclick="PREGUNTAS.openCreateModal()"
              class="qst-btn qst-btn-primary"
              type="button">
            <i class="fas fa-plus"></i>
            Nueva Pregunta
          </button>

          <?php if ($puedeUsarBanco): ?>
          <button
              onclick="PREGUNTAS.abrirDesdeBanco()"
              class="qst-btn qst-btn-soft"
              type="button"
              title="Inserta una pregunta ya guardada en el banco de preguntas">
            <i class="fas fa-database"></i>
            Agregar desde el banco
          </button>
          <?php endif; ?>

          <button
              onclick="PREGUNTAS.openVotoEscalonadoModal()"
              class="qst-btn qst-btn-violet"
              type="button"
              title="Crea en un clic las 3 preguntas conectadas (Primera, Segunda y Tercera opción) desde una tabla de candidatos">
            <i class="fas fa-list-ol"></i>
            Agregar voto escalonado (Plan A/B/C)
          </button>
          <?php endif; ?>

          <button
              onclick="PREGUNTAS.openMapaModal()"
              class="qst-btn qst-btn-soft"
              type="button">
            <i class="fas fa-route"></i>
            Mapa y Validador
          </button>

          <a
              href="votantes_encuestador.php?prueba=1&ficha=<?= (int)$fichaId ?>"
              target="_blank"
              class="qst-btn qst-btn-success"
              title="Probar el cuestionario paso a paso sin guardar datos reales ni exigir audio">
            <i class="fas fa-mobile-alt"></i>
            Probar el cuestionario
          </a>

          <?php if ($create || $edit): ?>
          <button
              onclick="PREGUNTAS.showBatchModal()"
              class="qst-btn qst-btn-soft"
              type="button">
            <i class="fas fa-layer-group"></i>
            Ingreso Masivo
          </button>

          <button
              onclick="PREGUNTAS.showUploadModal()"
              class="qst-btn qst-btn-soft"
              type="button">
            <i class="fas fa-upload"></i>
            Subir CSV/Excel
          </button>
          <?php endif; ?>

          <a
              href="preguntas.csv"
              download
              class="qst-btn qst-btn-soft">
            <i class="fas fa-download"></i>
            Plantilla CSV
          </a>

        </div>
      </section>

      <!-- =================================================
           BARRA DE CONFIGURACIÓN DE LA ENCUESTA (100% CONFIGURABLE)
      ================================================== -->
      <section class="qst-command mb-3" style="background:linear-gradient(90deg,#F5F9FF,#FFFFFF);border-color:#D5E3F7;">
        <div class="qst-command-copy">
          <div class="qst-command-icon" style="background:#E0ECFF;color:#183F7D;">
            <i class="fas fa-sliders-h"></i>
          </div>
          <div>
            <strong>Configuración general de esta encuesta</strong>
            <span>Opciones de inicio y audio, municipios/zonas con metas, variables para cruces demográficos, tablas de candidatos y fórmulas.</span>
          </div>
        </div>
        <div class="qst-command-actions">
          <button type="button" class="qst-btn qst-btn-soft" onclick="PREGUNTAS.openConfigEncuestaModal()">
            <i class="fas fa-cog text-primary"></i>
            Opciones de la encuesta
          </button>
          <button type="button" class="qst-btn qst-btn-soft" onclick="PREGUNTAS.openZonasMuestreoModal()">
            <i class="fas fa-map-marked-alt text-success"></i>
            Municipios, zonas y metas
          </button>
          <button type="button" class="qst-btn qst-btn-soft" onclick="PREGUNTAS.openCrucesEquivalenciasModal()">
            <i class="fas fa-project-diagram text-info"></i>
            Variables para cruces y equivalencias
          </button>
          <a href="grilla.php" class="qst-btn qst-btn-soft">
            <i class="fas fa-table text-warning"></i>
            Tablas de candidatos
          </a>
          <button type="button" class="qst-btn qst-btn-soft" onclick="PREGUNTAS.openFormulasModal()">
            <i class="fas fa-calculator text-secondary"></i>
            Fórmulas e indicadores
          </button>
        </div>
      </section>

      <!-- ===== Ayuda Visual (Accordion) ===== -->
      <div class="qst-help-card">
        <div class="qst-help-head">

          <div class="qst-help-title">
            <div class="qst-help-icon">
              <i class="fas fa-life-ring"></i>
            </div>
            <div>
              <strong>Guía rápida del cuestionario</strong>
              <span>Formulario, tipos de pregunta e importación masiva.</span>
            </div>
          </div>

          <span class="badge rounded-pill d-none d-md-inline-flex"
                style="color:#245BA7;background:#EEF5FF;border:1px solid #DCE8FA;padding:7px 10px;">
            <i class="fas fa-lightbulb me-1"></i>
            CSV UTF-8 recomendado
          </span>

        </div>

        <div class="qst-help-body">
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
        <div class="qst-groups-area">
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
                          style="display:none;" onclick="PREGUNTAS.reasignarGrupo('<?= $idsGrupo ?>')">
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
                      <th style="width:52px;" class="text-center">Orden</th>
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
                      <th style="width:52px;" class="text-center">Orden</th>
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

                      <!-- Orden -->
                      <td class="text-center qst-orden-cell" style="vertical-align:middle;"
                          data-orden="<?= (int)($item['orden'] ?? 0) ?>">
                        <?php if (!empty($item['orden'])): ?>
                        <span class="badge rounded-pill"
                              style="background:#EEF4FF;color:#20427F;border:1px solid #C7D9F7;font-size:.72rem;font-weight:800;min-width:28px;">
                          <?= (int)$item['orden'] ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted small">—</span>
                        <?php endif; ?>
                      </td>

                      <!-- Acciones -->
                      <td>
                        <div class="d-flex gap-1">
                          <?php if ($puedeGuardarBanco): ?>
                          <button type="button" class="btn btn-sm btn-outline-secondary" title="Agregar a banco de preguntas"
                                  style="border-radius:8px;"
                                  onclick="BANCO_PICKER.agregarAlBanco('encuesta', <?= (int)$item['id'] ?>)">
                            <i class="fas fa-database"></i>
                          </button>
                          <?php endif; ?>
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

                      <td class="fw-semibold">
                        <div><?= htmlspecialchars($item['texto_pregunta'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                          <span class="badge bg-light text-dark border" style="font-size:.68rem;">
                            <?= h(nombreLegibleTipoPregunta($item['tipo_pregunta'] ?? '')) ?>
                          </span>
                          <?php if (!empty($item['es_filtro'])): ?>
                            <span class="badge bg-danger" style="font-size:.68rem;"><i class="fas fa-hand-paper me-1"></i>FILTRO</span>
                          <?php endif; ?>
                          <?php if (!empty($item['visible_si_pregunta_id'])):
                            $pCond = $preguntasPorIdMap[(int)$item['visible_si_pregunta_id']] ?? null;
                            $labelCond = $pCond ? ($pCond['numeral'] ?: ('#' . ($pCond['orden'] ?? $pCond['id']))) : ('#' . (int)$item['visible_si_pregunta_id']);
                          ?>
                            <span class="badge bg-info text-dark" style="font-size:.68rem;"><i class="fas fa-code-branch me-1"></i>Condicional de <?= h($labelCond) ?></span>
                          <?php endif; ?>
                          <?php if (($item['tipo_pregunta'] ?? '') === 'Grilla'):
                            if (!empty($item['grilla_por_municipio'])): ?>
                              <span class="badge bg-warning text-dark" style="font-size:.68rem;"><i class="fas fa-city me-1"></i>Tabla por municipio (<?= count($item['grilla_variantes'] ?? []) ?>)</span>
                            <?php elseif (!empty($item['tbl_grilla_plantilla_id'])):
                              $plRef = $plantillasPorIdMap[(int)$item['tbl_grilla_plantilla_id']] ?? null;
                            ?>
                              <span class="badge" style="background:#EEF4FF;color:#20427F;border:1px solid #C7D9F7;font-size:.68rem;"><i class="fas fa-table me-1"></i>Tabla: <?= h($plRef['nombre'] ?? ('#' . (int)$item['tbl_grilla_plantilla_id'])) ?></span>
                            <?php endif;
                          endif; ?>
                          <?php if (($item['tipo_pregunta'] ?? '') === 'Lista_desde_grilla'): ?>
                            <span class="badge" style="background:#F3E8FF;color:#6B21A8;border:1px solid #D8B4FE;font-size:.68rem;"><i class="fas fa-list-ol me-1"></i>Lista desde tabla<?= !empty($item['exclusiones']) ? ' (-' . count($item['exclusiones']) . ' excl.)' : '' ?></span>
                          <?php endif; ?>
                          <?php if (!empty($item['es_variable_cruce'])): ?>
                            <span class="badge bg-secondary" style="font-size:.68rem;"><i class="fas fa-project-diagram me-1"></i>Cruce: <?= h($item['etiqueta_cruce'] ?: 'Sí') ?></span>
                          <?php endif; ?>
                        </div>
                      </td>

                      <?php if ($esSinEnunciado): ?>
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
                                data-opciones="<?= htmlspecialchars(json_encode($item['opciones']), ENT_QUOTES, 'UTF-8') ?>"
                                data-pregunta="<?= htmlspecialchars($item['texto_pregunta'], ENT_QUOTES, 'UTF-8') ?>"
                                onclick="PREGUNTAS.showOptionsModal(this.dataset.opciones, this.dataset.pregunta)">
                          <i class="uil uil-eye" style="font-size:.85rem;"></i>
                        </button>
                        <?php elseif (($item['tipo_pregunta'] ?? '') === 'Grilla'): ?>
                        <a href="grilla.php" class="badge bg-light text-primary border text-decoration-none" title="Ver tablas de candidatos">Tabla</a>
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
              <?php endforeach; ?>
              <?php if (!$esSinCapitulo): ?></div><?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>

          <?php else: ?>
          <div class="alert alert-info text-center">No hay preguntas registradas.</div>
          <?php endif; ?>
        </div>

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

        <div class="modal fade" id="editEnunciadoModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;border:0;box-shadow:0 25px 60px rgba(2,6,23,.18);">
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

                <div class="mt-3 p-3 rounded-3" style="background:rgba(32,66,127,.05);border-left:3px solid #20427F;">
                  <div class="d-flex gap-2 align-items-start">
                    <i class="fas fa-lightbulb text-primary mt-1" style="font-size:.85rem;opacity:.7;"></i>
                    <div>
                      <div class="small fw-semibold text-primary" style="font-size:.82rem;">¿Qué es un enunciado?</div>
                      <div class="text-muted" style="font-size:.78rem;line-height:1.4;">
                        Es un texto que aparece <strong>antes</strong> de las preguntas para dar contexto al encuestado.
                      </div>
                    </div>
                  </div>
                </div>

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

        <div class="modal fade" id="optionsModal" tabindex="-1" aria-labelledby="optionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="optionsModalLabel">Opciones de la pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-semibold mb-3" id="modalQuestionText"></p>
                        <div id="modalOptionsList" class="row"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

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
                <input type="hidden" id="editNumeralAdicionalIds">
                <div class="mb-3">
                  <label class="form-label fw-bold">Numeral <span class="text-muted fw-normal small">(ej: 1.1, 2.3.a, 5A)</span></label>
                  <input type="text" class="form-control" id="editNumeralInput"
                         placeholder="Código o numeral de la pregunta"
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
          <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;border:0;box-shadow:0 25px 60px rgba(2,6,23,.18);">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;border-bottom:0;padding:16px 24px;">
                <div>
                  <h5 class="modal-title mb-0" style="font-weight:800;letter-spacing:-.3px;">
                    <i class="fas fa-pen-nib me-2"></i>Crear / Editar Pregunta
                  </h5>
                  <small class="opacity-75" style="font-size:.78rem;">Configure el texto, tipo de pregunta, opciones y reglas en lenguaje sencillo</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="filter:brightness(2);"></button>
              </div>

              <div class="modal-body p-4" style="background:rgba(248,250,252,.6);">
                <form id="formPreguntas" class="row g-3 mb-0" role="form" autocomplete="false">
                  <input type="hidden" name="op" id="op" />
                  <input type="hidden" name="id" id="id" />
                  <input type="hidden" id="ficha_id_locked" value="<?= (int)$fichaId ?>" />

                  <div class="col-sm-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_ficha_tecnica_encuesta_id"
                              name="tbl_ficha_tecnica_encuesta_id" required disabled>
                        <?php echo $optionEncuentas; ?>
                      </select>
                      <label for="tbl_ficha_tecnica_encuesta_id">
                        Encuesta <span class="text-danger">*</span>
                      </label>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-5">
                    <div class="form-floating">
                      <select onchange="OPCIONES.handleTipoPreguntaChange('tipo_pregunta');"
                              class="form-select" id="tipo_pregunta" name="tipo_pregunta">
                        <option value="">Seleccione cómo responde la persona</option>
                        <option value="Dicotomica">Sí / No (Dos opciones)</option>
                        <option value="Seleccion_Multiple_unica_respuesta">Escoge una sola opción</option>
                        <option value="Seleccion_Multiple_multiple_respuesta">Puede escoger varias opciones</option>
                        <option value="Preguntas_Ordinales">Escala de calificación (ej. 1 a 5)</option>
                        <option value="Preguntas_Cardinales">Nivel de satisfacción / intensidad</option>
                        <option value="Grilla">Tabla de candidatos o ítems</option>
                        <option value="Lista_desde_grilla">Escoger de los candidatos de una tabla (Voto escalonado)</option>
                        <option value="Texto Libre">Respuesta escrita abierta</option>
                      </select>
                      <label for="tipo_pregunta">Tipo de Pregunta <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-3">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="numeral"
                             name="numeral" placeholder="Ej: F1, 1A, 5A">
                      <label for="numeral">Código / Numeral (ej. 5A)</label>
                    </div>
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
                             name="orden" placeholder="Orden" value="<?= (int)$siguienteOrden ?>" min="1">
                      <label for="orden">Orden en la encuesta</label>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="p-3 rounded-3 border" style="background:#F8FAFC;">
                      <div class="fw-bold mb-1" style="color:#20427F;"><i class="fas fa-layer-group me-1"></i>Contexto de la sección</div>
                      <div class="small text-muted mb-3">Lo que escriba aquí se muestra una sola vez, al inicio de la sección, y no se repite en cada subpregunta. Al elegir un capítulo que ya existe se rellena con el contexto de esa sección.</div>
                      <div class="row g-3">
                        <div class="col-12">
                          <div class="form-floating">
                            <input type="text" class="form-control" id="capitulo" name="capitulo" list="listaCapitulosExistentes" autocomplete="off" placeholder="Ej: Pregunta 1 · Problemas del país">
                            <label for="capitulo">Capítulo o sección (opcional)</label>
                          </div>
                          <datalist id="listaCapitulosExistentes"></datalist>
                        </div>
                        <div class="col-12">
                          <div class="form-floating">
                            <input type="text" class="form-control" id="enunciado_pregunta" name="enunciado_pregunta" placeholder="Enunciado o contexto" value="">
                            <label for="enunciado_pregunta">Enunciado de la sección (se muestra grande, antes de las subpreguntas)</label>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                          <div class="form-floating">
                            <input type="text" class="form-control" id="libreto_encuestador" name="libreto_encuestador" placeholder="Instrucción en voz alta o nota para el encuestador">
                            <label for="libreto_encuestador"><i class="fas fa-microphone-alt text-danger me-1"></i>Instrucción para el encuestador (opcional)</label>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                          <div class="form-floating">
                            <input type="text" class="form-control" id="nota_tabulacion" name="nota_tabulacion" placeholder="Nota interna de análisis o tabulación">
                            <label for="nota_tabulacion"><i class="fas fa-chart-pie text-primary me-1"></i>Nota de análisis / tabulación (opcional)</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div id="divLimiteRespuesta" class="col-sm-12 col-md-4" style="display: none;">
                    <div class="form-floating">
                      <input type="number" class="form-control" id="limite_respuesta_multiple"
                             name="limite_respuesta_multiple" value="2" min="1">
                      <label for="limite_respuesta_multiple">Máximo de opciones que puede marcar</label>
                    </div>
                  </div>

                  <!-- Panel exclusivo para tipo Grilla -->
                  <div id="qstPanelGrillaConfig" class="col-12" style="display:none;">
                    <div class="p-3 rounded-3" style="background:#EEF4FF;border:1.5px solid #C7D9F7;">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <h6 class="mb-0 fw-bold" style="color:#183F7D;">
                          <i class="fas fa-table me-2"></i>Configuración de la Tabla de candidatos / ítems
                        </h6>
                        <a href="grilla.php" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                          <i class="fas fa-external-link-alt me-1"></i>Crear o editar tablas de candidatos
                        </a>
                      </div>

                      <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="grilla_por_municipio" onchange="PREGUNTAS.toggleGrillaPorMunicipio()">
                        <label class="form-check-label fw-semibold" for="grilla_por_municipio">
                          ¿La tabla de candidatos cambia según el municipio donde se aplica la encuesta? (ej. Alcaldía por municipio)
                        </label>
                      </div>

                      <div id="qstGrillaUnicaWrap">
                        <label class="form-label small fw-bold">Seleccione la tabla de candidatos o ítems que se mostrará en esta pregunta:</label>
                        <select class="form-select" id="tbl_grilla_plantilla_id">
                          <option value="">-- Seleccione una tabla --</option>
                          <?php foreach ($arrPlantillasGrilla as $pl): ?>
                            <option value="<?= (int)$pl['id'] ?>">
                              <?= h($pl['nombre']) ?> (<?= (int)($pl['total_filas'] ?? 0) ?> filas · <?= (int)($pl['total_columnas'] ?? 0) ?> preguntas)
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>

                      <div id="qstGrillaPorMunicipioWrap" style="display:none;">
                        <p class="small text-muted mb-2">
                          Asigne qué tabla de candidatos verá el encuestador en cada municipio. Si un municipio no está en esta lista, esta pregunta se omitirá automáticamente en ese municipio.
                        </p>
                        <div id="qstGrillaVariantesRows"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="PREGUNTAS.addVarianteMunicipioRow()">
                          <i class="fas fa-plus me-1"></i>Agregar municipio con su tabla
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Panel exclusivo para tipo Lista_desde_grilla -->
                  <div id="qstPanelListaGrillaConfig" class="col-12" style="display:none;">
                    <div class="p-3 rounded-3" style="background:#F5F3FF;border:1.5px solid #DDD6FE;">
                      <h6 class="mb-2 fw-bold" style="color:#5B21B6;">
                        <i class="fas fa-list-ol me-2"></i>Escoger entre los candidatos aprobados de una tabla anterior
                      </h6>
                      <div class="row g-2">
                        <div class="col-md-4">
                          <label class="form-label small fw-bold">1. ¿De cuál pregunta de tabla vienen los candidatos?</label>
                          <select class="form-select" id="lista_origen_pregunta_id" onchange="PREGUNTAS.onChangeListaOrigenPregunta()">
                            <option value="">-- Seleccione pregunta de tabla --</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label small fw-bold">2. ¿En qué pregunta dentro de esa tabla se filtra?</label>
                          <select class="form-select" id="lista_origen_columna_id" onchange="PREGUNTAS.onChangeListaOrigenColumna()">
                            <option value="">-- Todas las filas de la tabla --</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label small fw-bold">3. Mostrar solo los candidatos donde respondió:</label>
                          <select class="form-select" id="lista_origen_opcion_id">
                            <option value="">-- Cualquier respuesta --</option>
                          </select>
                        </div>
                        <div class="col-md-7 mt-2">
                          <label class="form-label small fw-bold">4. Quitar de la lista al candidato que la persona ya eligió en:</label>
                          <div id="qstListaExclusionesContainer" class="p-2 rounded border bg-white" style="max-height:120px;overflow-y:auto;">
                            <span class="small text-muted">No hay preguntas anteriores para excluir.</span>
                          </div>
                        </div>
                        <div class="col-md-5 mt-2">
                          <label class="form-label small fw-bold">5. Si ningún candidato cumple la condición:</label>
                          <select class="form-select" id="si_lista_vacia">
                            <option value="omitir">Omitir esta pregunta y seguir con la encuesta</option>
                            <option value="terminar">Terminar la encuesta</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Sección de Opciones Manuales -->
                  <div class="col-12" id="qstSeccionOpcionesManuales">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2 mb-2">
                      <h6 class="mb-0 fw-bold" style="color:#0f172a;">
                        <i class="fas fa-check-square text-primary me-1"></i>Opciones de Respuesta y Reglas de Salto / Filtro
                      </h6>
                      <div class="d-flex flex-wrap gap-1">
                        <span class="small text-muted align-self-center me-1">Plantillas rápidas:</span>
                        <button type="button" class="btn btn-xs btn-outline-primary" style="font-size:.75rem;border-radius:8px;" onclick="OPCIONES.aplicarPlantillaOpciones('sino')">Sí / No</button>
                        <button type="button" class="btn btn-xs btn-outline-danger" style="font-size:.75rem;border-radius:8px;" onclick="OPCIONES.aplicarPlantillaOpciones('sino_filtro')">Filtro: Sí continúa / No termina</button>
                        <button type="button" class="btn btn-xs btn-outline-info" style="font-size:.75rem;border-radius:8px;" onclick="OPCIONES.aplicarPlantillaOpciones('imagen')">Favorable / Desfavorable</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" style="font-size:.75rem;border-radius:8px;" onclick="OPCIONES.aplicarPlantillaOpciones('escala15')">Escala 1 a 5</button>
                        <button type="button" class="btn btn-xs btn-outline-dark" style="font-size:.75rem;border-radius:8px;" onclick="$('#qstPegarOpcionesBox').slideToggle(150);">
                          <i class="fas fa-paste me-1"></i>Pegar varias (una por renglón)
                        </button>
                      </div>
                    </div>

                    <div id="qstPegarOpcionesBox" class="p-3 mb-2 rounded-3" style="display:none;background:#FFFBEB;border:1px solid #FDE68A;">
                      <label class="form-label small fw-bold mb-1">Pegue o escriba una opción por cada renglón:</label>
                      <textarea id="qstPegarOpcionesTextarea" class="form-control form-control-sm mb-2" rows="4" placeholder="Opción 1&#10;Opción 2&#10;No sabe / No responde"></textarea>
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="qstReemplazarOpcionesChk" checked>
                          <label class="form-check-label small" for="qstReemplazarOpcionesChk">Reemplazar las opciones actuales</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="OPCIONES.pegarOpcionesPorRenglon()">
                          <i class="fas fa-check me-1"></i>Agregar opciones
                        </button>
                      </div>
                    </div>

                    <div id="opcionesContainer">
                      <p id="noOptionsMessage" class="text-muted small mb-2" style="display:none;">
                        No hay opciones de respuesta. ¡Añade una o usa una plantilla rápida!
                      </p>
                    </div>
                    <button type="button" class="btn btn-phoenix-primary btn-sm mt-1" id="addOptionBtn">
                      <span class="fas fa-plus me-1"></span> Añadir Opción
                    </button>
                  </div>

                  <!-- Condición de visibilidad y opciones avanzadas en lenguaje humano -->
                  <div class="col-12 mt-3">
                    <div class="p-3 rounded-3" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label small fw-bold text-primary mb-1">
                            <i class="fas fa-code-branch me-1"></i>¿Cuándo se muestra esta pregunta?
                          </label>
                          <select class="form-select form-select-sm mb-2" id="qstModoVisibilidad" onchange="PREGUNTAS.onChangeModoVisibilidad()">
                            <option value="siempre">Siempre (a todas las personas que lleguen a este punto)</option>
                            <option value="condicional">Solo si en una pregunta anterior respondió algo específico…</option>
                          </select>
                          <div id="qstVisibilidadCondicionalWrap" class="row g-2" style="display:none;">
                            <div class="col-6">
                              <select class="form-select form-select-sm" id="visible_si_pregunta_id" onchange="PREGUNTAS.onChangeVisibleSiPregunta()">
                                <option value="">-- Pregunta anterior --</option>
                              </select>
                            </div>
                            <div class="col-6">
                              <select class="form-select form-select-sm" id="visible_si_opcion_id">
                                <option value="">-- Respuesta elegida --</option>
                              </select>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <label class="form-label small fw-bold text-primary mb-1">
                            <i class="fas fa-sliders-h me-1"></i>Comportamiento y cruces de resultados
                          </label>
                          <div class="d-flex flex-wrap gap-3">
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="obligatoria" checked>
                              <label class="form-check-label small" for="obligatoria">Obligatoria</label>
                            </div>
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="es_filtro">
                              <label class="form-check-label small fw-bold text-danger" for="es_filtro">Es pregunta filtro</label>
                            </div>
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="es_variable_cruce" onchange="$('#qstCruceExtraWrap').toggle(this.checked);">
                              <label class="form-check-label small fw-bold text-info" for="es_variable_cruce">Usar para cruzar resultados (ej. Sexo, Edad)</label>
                            </div>
                          </div>
                          <div class="row g-2 mt-2">
                            <div class="col-12 col-md-6">
                              <label class="form-label small fw-bold mb-1" for="rol_resultado">Uso en resultados</label>
                              <select class="form-select" id="rol_resultado">
                                <option value="ninguno">Ninguno especial</option>
                                <option value="conocimiento">Conocimiento (Sí / No)</option>
                                <option value="imagen">Imagen (Favorable / Desfavorable)</option>
                                <option value="intencion">Intención de voto</option>
                                <option value="ideologia">Ideología / Ubicación</option>
                                <option value="asociacion_positiva">Asociación positiva</option>
                                <option value="asociacion_negativa">Asociación negativa</option>
                                <option value="escala">Escala / Promedio</option>
                                <option value="otro">Otro indicador</option>
                              </select>
                            </div>
                          </div>
                          <div id="qstCruceExtraWrap" class="row g-2 mt-1" style="display:none;">
                            <div class="col-6">
                              <input type="text" class="form-control form-control-sm" id="etiqueta_cruce" placeholder="Nombre corto (ej. Sexo, Estrato)">
                            </div>
                            <div class="col-6">
                              <select class="form-select form-select-sm" id="copiar_a_votante_campo">
                                <option value="ninguno">No copiar a ficha del votante</option>
                                <option value="genero">Copiar a Género del votante</option>
                                <option value="rango_edad">Copiar a Rango de edad del votante</option>
                                <option value="ideologia">Copiar a Ideología del votante</option>
                                <option value="nivel_ingresos">Copiar a Nivel de ingresos del votante</option>
                                <option value="nivel_educacion">Copiar a Nivel de educación del votante</option>
                                <option value="ocupacion">Copiar a Ocupación del votante</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="habilitado"
                             name="habilitado" value="si" checked>
                      <label class="form-check-label" for="habilitado">Pregunta Habilitada</label>
                    </div>
                  </div>

                  <div class="col-sm-12 col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="visualizacion"
                             name="visualizacion" value="si" checked>
                      <label class="form-check-label" for="visualizacion">Visualización en el estudio</label>
                    </div>
                  </div>

                </form>
              </div>

              <div class="modal-footer" style="background:#fff;border-top:1px solid rgba(15,23,42,.08);">
                <div class="text-muted small me-auto">
                  <i class="fas fa-info-circle me-1"></i>
                  Todas las reglas y opciones quedan guardadas al pulsar Guardar Pregunta.
                </div>
                <button type="button"
                        onclick="PREGUNTAS.clearForm('formPreguntas');"
                        class="btn btn-phoenix-secondary px-4" style="border-radius:10px;">
                  Cancelar
                </button>
                <?php if ($create || $edit): ?>
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

        <!-- Modal 1: Agregar Voto Escalonado en 1 clic (Plan A / B / C) -->
        <div class="modal fade" id="votoEscalonadoModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#4C1D95,#2E1065);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0 fw-bold"><i class="fas fa-list-ol me-2"></i>Agregar voto escalonado (Plan A / B / C)</h5>
                  <small class="opacity-75">Crea automáticamente 3 preguntas conectadas donde se descarta al candidato ya elegido</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-bold small">1. ¿De cuál pregunta de tabla vienen los candidatos?</label>
                    <select class="form-select" id="ve_origen_pregunta_id" onchange="PREGUNTAS.onChangeVotoEscalonadoOrigen()">
                      <option value="">-- Seleccione una pregunta de tabla de candidatos --</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-bold small">2. Pregunta filtro en la tabla</label>
                    <select class="form-select" id="ve_origen_columna_id" onchange="PREGUNTAS.onChangeVotoEscalonadoColumna()">
                      <option value="">-- Columna --</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-bold small">3. Respuesta que aprueba</label>
                    <select class="form-select" id="ve_origen_opcion_id">
                      <option value="">-- Opción --</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold small">Capítulo (opcional)</label>
                    <input type="text" class="form-control" id="ve_capitulo" placeholder="Ej: Intención de voto">
                  </div>
                  <div class="col-12"><hr class="my-1"><div class="fw-bold">Primera pregunta <span class="text-muted fw-normal">(la que pide la primera opción)</span></div></div>
                  <div class="col-md-3">
                    <label class="form-label fw-bold" for="ve_numeral_1">Número</label>
                    <input type="text" class="form-control form-control-lg" id="ve_numeral_1" placeholder="Ej: 7.1">
                  </div>
                  <div class="col-md-9">
                    <label class="form-label fw-bold" for="ve_texto_1">Texto que lee el encuestador</label>
                    <input type="text" class="form-control form-control-lg" id="ve_texto_1">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold" for="ve_fijas_1">Opciones que siempre aparecen al final (separadas por coma; puede dejarlas vacías)</label>
                    <input type="text" class="form-control form-control-lg" id="ve_fijas_1" placeholder="Ej: Ninguno, No sabe / No responde">
                  </div>
                  <div class="col-12"><hr class="my-1"><div class="fw-bold">Segunda pregunta <span class="text-muted fw-normal">(excluye a quien se eligió en la primera)</span></div></div>
                  <div class="col-md-3">
                    <label class="form-label fw-bold" for="ve_numeral_2">Número</label>
                    <input type="text" class="form-control form-control-lg" id="ve_numeral_2" placeholder="Ej: 7.2">
                  </div>
                  <div class="col-md-9">
                    <label class="form-label fw-bold" for="ve_texto_2">Texto que lee el encuestador</label>
                    <input type="text" class="form-control form-control-lg" id="ve_texto_2">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold" for="ve_fijas_2">Opciones que siempre aparecen al final (separadas por coma; puede dejarlas vacías)</label>
                    <input type="text" class="form-control form-control-lg" id="ve_fijas_2" placeholder="Ej: Ninguno, No sabe / No responde">
                  </div>
                  <div class="col-12"><hr class="my-1"><div class="fw-bold">Tercera pregunta <span class="text-muted fw-normal">(excluye a quienes se eligieron en la primera y la segunda)</span></div></div>
                  <div class="col-md-3">
                    <label class="form-label fw-bold" for="ve_numeral_3">Número</label>
                    <input type="text" class="form-control form-control-lg" id="ve_numeral_3" placeholder="Ej: 7.3">
                  </div>
                  <div class="col-md-9">
                    <label class="form-label fw-bold" for="ve_texto_3">Texto que lee el encuestador</label>
                    <input type="text" class="form-control form-control-lg" id="ve_texto_3">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold" for="ve_fijas_3">Opciones que siempre aparecen al final (separadas por coma; puede dejarlas vacías)</label>
                    <input type="text" class="form-control form-control-lg" id="ve_fijas_3" value="Ninguno, No sabe / No responde" placeholder="Ej: Ninguno, No sabe / No responde">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveVotoEscalonado()">
                  <i class="fas fa-magic me-2"></i>Crear las 3 preguntas conectadas
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal 2: Mapa del Cuestionario y Validador -->
        <div class="modal fade" id="mapaCuestionarioModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0 fw-bold"><i class="fas fa-route me-2"></i>Mapa del cuestionario y Validador de calidad</h5>
                  <small class="opacity-75">Verifica automáticamente saltos, filtros, tablas y textos pendientes</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div id="mapaValidadorAlertas" class="mb-3"></div>
                <div id="mapaFlujoContainer"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" onclick="PREGUNTAS.openMapaModal()">
                  <i class="fas fa-sync-alt me-1"></i>Volver a validar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal 3: Opciones generales de la encuesta -->
        <div class="modal fade" id="configEncuestaModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#20427F,#132b52);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0 fw-bold"><i class="fas fa-cog me-2"></i>Opciones de la encuesta</h5>
                  <small class="opacity-75">Configure cómo inicia la encuesta, el audio y la pregunta principal del mapa</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" id="cfg_pedir_municipio_zona_inicio" checked>
                      <label class="form-check-label fw-bold" for="cfg_pedir_municipio_zona_inicio">Pedir el municipio y la zona al inicio de la encuesta</label>
                    </div>
                    <small class="text-muted d-block">Necesario cuando hay tablas de candidatos por municipio o metas por zona.</small>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" id="cfg_audio_obligatorio" checked>
                      <label class="form-check-label fw-bold" for="cfg_audio_obligatorio">Exigir grabación de audio a los encuestadores</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" id="cfg_certificar_terminadas_filtro" checked>
                      <label class="form-check-label fw-bold" for="cfg_certificar_terminadas_filtro">Grabar y revisar también las encuestas que terminan por la pregunta filtro</label>
                    </div>
                    <small class="text-muted d-block">Esas encuestas nunca cuentan para pago; se guardan para control de calidad.</small>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" id="cfg_mostrar_resultados_publicos" checked>
                      <label class="form-check-label fw-bold" for="cfg_mostrar_resultados_publicos">Mostrar resultados en el sitio público</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" id="cfg_bloquear_cuota_completa">
                      <label class="form-check-label fw-bold" for="cfg_bloquear_cuota_completa">No dejar hacer más encuestas en una zona que ya llegó a su meta</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold" for="cfg_mensaje_cierre_defecto">Mensaje de despedida al terminar</label>
                    <textarea class="form-control" id="cfg_mensaje_cierre_defecto" rows="2"></textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold small">Pregunta destacada para el mapa territorial</label>
                    <select class="form-select" id="cfg_pregunta_destacada_id">
                      <option value="">-- Automática (primera pregunta activa que no sea filtro) --</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveConfigEncuesta()">
                  <i class="fas fa-save me-2"></i>Guardar opciones
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal 4: Municipios, zonas y metas de la encuesta -->
        <div class="modal fade" id="zonasMuestreoModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#065F46,#064E3B);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0 fw-bold"><i class="fas fa-map-marked-alt me-2"></i>Municipios, zonas y metas de la encuesta</h5>
                  <small class="opacity-75">Defina los municipios, sus comunas/zonas y la meta de encuestas completas para cada una</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 p-3 rounded-3" style="background:#F0FDF4;border:1px solid #BBF7D0;">
                  <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-success" onclick="PREGUNTAS.addZonaMuestreoRow()">
                      <i class="fas fa-plus me-1"></i>Agregar fila de zona
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="$('#zonasPegadoMasivoBox').slideToggle(150);">
                      <i class="fas fa-paste me-1"></i>Pegar varias zonas de un municipio
                    </button>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="small fw-semibold text-muted">Copiar zonas de otra encuesta:</span>
                    <select class="form-select" id="zonasCopiarFichaOrigen" style="width:auto;min-width:200px;">
                      <option value="">-- Elija encuesta --</option>
                      <?php foreach ($arrFichaTecnicaEncuesta as $f): if ((int)$f['id'] === (int)$fichaId) continue; ?>
                        <option value="<?= (int)$f['id'] ?>">#<?= (int)$f['id'] ?> · <?= h($f['temas_concretos'] ?: $f['realizada_por_o_encomendada_por']) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-outline-primary" onclick="PREGUNTAS.copiarZonasDeOtraEncuesta()">
                      <i class="fas fa-copy me-1"></i>Copiar
                    </button>
                  </div>
                </div>

                <div id="zonasPegadoMasivoBox" class="p-3 mb-3 rounded-3" style="display:none;background:#FFFBEB;border:1px solid #FDE68A;">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <label class="form-label small fw-bold">Municipio para estas zonas:</label>
                      <select class="form-select" id="zonasMasivoMunicipio"></select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label small fw-bold">Meta por defecto (si no se indica):</label>
                      <input type="number" class="form-control" id="zonasMasivoCuotaDefault" value="50" min="0">
                    </div>
                    <div class="col-md-5">
                      <label class="form-label small fw-bold">Zonas (una por renglón; opcional: Nombre | Meta):</label>
                      <textarea class="form-control" id="zonasMasivoTextarea" rows="3" placeholder="Comuna 1 - Norte | 80&#10;Comuna 2 - Nororiental | 70"></textarea>
                    </div>
                  </div>
                  <div class="text-end mt-2 d-flex flex-wrap gap-2 justify-content-end">
                    <button type="button" class="btn btn-outline-success" onclick="PREGUNTAS.agregarMunicipioSinZonas()">
                      <i class="fas fa-city me-1"></i>Este municipio no tiene zonas
                    </button>
                    <button type="button" class="btn btn-primary" onclick="PREGUNTAS.aplicarPegadoZonas()">
                      <i class="fas fa-check me-1"></i>Agregar a la tabla
                    </button>
                  </div>
                  <div class="small text-muted mt-2">Si el municipio se encuesta completo, use «Este municipio no tiene zonas»: se crea una sola fila «Todo el municipio» con la meta por defecto.</div>
                </div>

                <div id="zonasResumenMunicipios" class="d-flex flex-wrap gap-2 mb-3"></div>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                      <tr>
                        <th style="width:26%;">Municipio</th>
                        <th style="width:150px;">Clase de zona</th>
                        <th>Nombre de la zona</th>
                        <th style="width:26%;">Barrios o descripción (opcional)</th>
                        <th style="width:140px;">Meta de encuestas</th>
                        <th style="width:90px;" class="text-center">Activa</th>
                        <th style="width:60px;"></th>
                      </tr>
                    </thead>
                    <tbody id="zonasMuestreoTbody"></tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <div class="me-auto">
                  <div class="fw-bold text-success" id="zonasTotalMetaLabel">Meta total: 0 encuestas</div>
                  <div class="small d-flex align-items-center gap-2 mt-1">
                    <label for="zonasMetaEsperada" class="mb-0">Meta total que espera (opcional):</label>
                    <input type="number" min="0" class="form-control" id="zonasMetaEsperada" style="width:110px;" oninput="PREGUNTAS.recalcZonasTotalMeta()">
                  </div>
                  <div class="small text-warning-emphasis fw-semibold d-none" id="zonasAvisoMeta"></div>
                </div>
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4" onclick="PREGUNTAS.saveZonasMuestreo()">
                  <i class="fas fa-save me-2"></i>Guardar zonas y metas
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal 5: Variables para cruces y equivalencias con el votante -->
        <div class="modal fade" id="crucesEquivalenciasModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#0369A1,#0C4A6E);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0 fw-bold"><i class="fas fa-project-diagram me-2"></i>Variables para cruces y equivalencias con el votante</h5>
                  <small class="opacity-75">Preguntas marcadas para cruzar resultados (ej. Sexo, Edad, Estrato) y cómo alimentan la ficha del votante</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div id="crucesEquivalenciasContainer"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-phoenix-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveCrucesEquivalencias()">
                  <i class="fas fa-save me-2"></i>Guardar equivalencias
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal 6: Fórmulas e indicadores del estudio -->
        <div class="modal fade" id="formulasIndicadoresModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:18px;overflow:hidden;">
              <div class="modal-header" style="background:linear-gradient(135deg,#334155,#0F172A);color:#fff;">
                <div>
                  <h5 class="modal-title mb-0 fw-bold"><i class="fas fa-calculator me-2"></i>Fórmulas e indicadores del estudio</h5>
                  <small class="opacity-75">Configure indicadores calculados para el panel de resultados</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4">
                <div class="row g-3 mb-4 p-3 rounded-3 border bg-light">
                  <input type="hidden" id="formula_edit_id" value="">
                  <input type="hidden" id="formula_sigla" value="">
                  <input type="hidden" id="formula_tipo" value="">
                  <div class="col-12 fw-bold" id="formulaTituloForm">Nuevo indicador</div>
                  <div class="col-md-5">
                    <label class="form-label fw-bold" for="formula_nombre">Nombre del indicador</label>
                    <input type="text" class="form-control form-control-lg" id="formula_nombre" placeholder="Ej: Diferencial de imagen">
                  </div>
                  <div class="col-md-7">
                    <label class="form-label fw-bold" for="formula_expresion">Cómo se calcula</label>
                    <input type="text" class="form-control form-control-lg" id="formula_expresion" placeholder="Ej: porcentaje de favorables menos porcentaje de desfavorables">
                    <div class="d-flex flex-wrap gap-2 mt-2">
                      <button type="button" class="btn btn-outline-secondary" onclick="PREGUNTAS.insertarParteFormula('porcentaje de ')">porcentaje de</button>
                      <button type="button" class="btn btn-outline-secondary" onclick="PREGUNTAS.insertarParteFormula(' dividido por el total de ')">dividido por el total de</button>
                      <button type="button" class="btn btn-outline-secondary" onclick="PREGUNTAS.insertarParteFormula(' menos ')">menos</button>
                      <button type="button" class="btn btn-outline-secondary" onclick="PREGUNTAS.insertarParteFormula('nota sobre cinco de ')">nota sobre cinco de</button>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold" for="formula_descripcion">Qué significa (opcional)</label>
                    <input type="text" class="form-control form-control-lg" id="formula_descripcion" placeholder="Explique en una frase cómo leer este indicador">
                  </div>
                  <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                    <button type="button" class="btn btn-outline-secondary" onclick="PREGUNTAS.limpiarFormularioFormula()">Limpiar</button>
                    <button type="button" class="btn btn-primary px-4" onclick="PREGUNTAS.saveFormulaItem()">
                      <i class="fas fa-save me-1"></i>Guardar indicador
                    </button>
                  </div>
                </div>
                <div id="formulasListaContainer"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

                    <div class="form-floating mb-3">
                      <select class="form-select" id="batchEncuesta" disabled>
                        <?php echo $optionEncuentas; ?>
                      </select>
                      <label>Ficha Técnica de la Encuesta <span class="text-danger">*</span></label>
                    </div>

                    <div class="form-floating mb-3">
                      <select class="form-select" id="batchTipoDefault">
                        <option value="">Sin tipo por defecto</option>
                        <option value="Dicotomica">Sí / No (Dos opciones)</option>
                        <option value="Preguntas_Ordinales" selected>Escala de calificación (ej. 1 a 5)</option>
                        <option value="Preguntas_Cardinales">Nivel de satisfacción / intensidad</option>
                        <option value="Seleccion_Multiple_unica_respuesta">Escoge una sola opción</option>
                        <option value="Seleccion_Multiple_multiple_respuesta">Puede escoger varias opciones</option>
                      </select>
                      <label>Tipo de pregunta (aplica a todas)</label>
                    </div>

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

                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <strong style="color:#0f172a;">Capítulos del cuestionario</strong>
                      <button type="button" class="btn btn-outline-primary" onclick="PREGUNTAS.batchAddCapitulo()" style="border-radius:10px;">
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

              <div class="modal-body" id="batchStep2" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                  <div>
                    <button type="button" class="btn btn-outline-secondary" onclick="PREGUNTAS.batchGoBack()">
                      <i class="fas fa-arrow-left me-1"></i> Volver
                    </button>
                  </div>
                  <div class="text-muted small">
                    <span id="batchProgress">0 de 0 completas</span>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="PREGUNTAS.batchCollapseAll()">
                      <i class="fas fa-compress-alt me-1"></i>Colapsar todo
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="PREGUNTAS.batchExpandAll()">
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

        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Subir Preguntas por Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="floating-label form-label" for="foto1">Archivo Excel</label>
                                    <div class="controls">
                                        <div class="dropzone dropzone-multiple p-0 mb-3" id="dropzone-foto1"
                                            style="min-height: 100px; padding: 10px;">
                                            <iframe id="ifm1" name="ifm1" src="upload_csv.php" width="100%" height="200"
                                                scrolling="no" frameborder="0" style="border: none;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="assets/js/plugins/prism.js"></script>

    <script>
      window.CUESTIONARIO_CAMPOS_VOTANTE = <?= json_encode(Votantes::catalogoCamposEquivalencia(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
      window.CUESTIONARIO_PREGUNTAS_CATALOGO = <?= json_encode($arrPregunta, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
      window.CUESTIONARIO_PLANTILLAS_GRILLA = <?= json_encode($arrPlantillasGrilla, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
      window.CUESTIONARIO_CTX = {
        fichaId: <?= (int)$fichaId ?>,
        fichaLabel: <?= json_encode($fichaLabel, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        returnPage: "cuestionario_preguntas.php?ficha_id=<?= (int)$fichaId ?>",
        lockFicha: true,
        siguienteOrden: <?= (int)$siguienteOrden ?>
      };
    </script>
    <script type="text/javascript" src="admin/js/opcion_preguntas.js"></script>
<?php
$seccionesCtx = [];
if (is_array($arrPregunta)) {
    foreach ($arrPregunta as $pSec) {
        $c = trim((string)($pSec['capitulo'] ?? ''));
        $fk = (string)($pSec['tbl_ficha_tecnica_encuesta_id'] ?? '');
        if ($c === '') { continue; }
        if (!isset($seccionesCtx[$fk][$c])) { $seccionesCtx[$fk][$c] = ['enunciado' => '', 'libreto' => '', 'nota' => '']; }
        foreach (['enunciado' => 'enunciado_pregunta', 'libreto' => 'libreto_encuestador', 'nota' => 'nota_tabulacion'] as $k => $col) {
            if ($seccionesCtx[$fk][$c][$k] === '' && trim((string)($pSec[$col] ?? '')) !== '') { $seccionesCtx[$fk][$c][$k] = trim((string)$pSec[$col]); }
        }
    }
}
?>
<script>window.SECCIONES_CTX = <?= json_encode($seccionesCtx, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;</script>
    <script type="text/javascript" src="admin/js/banco_picker.js"></script>
    <script type="text/javascript" src="admin/js/preguntas.js"></script>
    <script>
      OPCIONES.handleTipoPreguntaChange();
    </script>

    <?php include './admin/include/generic_dataTables.php'; ?>
    <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>