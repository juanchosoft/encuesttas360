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

// Validar que sea Administrador
if (!SessionData::administrador()) {
  require 'permiso_denegado.php';
  exit;
}

// Helper seguro
function h($s){
  return htmlspecialchars(
    (string)$s,
    ENT_QUOTES,
    'UTF-8'
  );
}

// Información de Configuración
$configResponse = Configuracion::getAll(null);

$isvalid =
  $configResponse['output']['valid']
  ?? false;

$arr =
  $configResponse['output']['response']
  ?? [];

$configActual =
  (
    is_array($arr)
    &&
    !empty($arr)
  )
  ?
  $arr[0]
  :
  [];

// Información de Departamentos
$departamentos =
  Departamento::getAll(null);

$isValidDep =
  $departamentos['output']['valid']
  ?? false;

$departamentosResponse =
  $departamentos['output']['response']
  ?? [];

$optionDep =
  '<option value="" disabled selected>Seleccione...</option>';

foreach ($departamentosResponse as $dep) {

  $codigo =
    h(
      $dep['codigo_departamento']
      ?? ''
    );

  $nombre =
    h(
      $dep['departamento']
      ?? ''
    );

  $optionDep .=
    "<option value='{$codigo}'>{$codigo} - {$nombre}</option>";
}

$modulo =
  'Configuración Aplicación';

$totalDepartamentos =
  is_array($departamentosResponse)
  ?
  count($departamentosResponse)
  :
  0;

$totalConfiguraciones =
  is_array($arr)
  ?
  count($arr)
  :
  0;

$opcionActivaActual =
  $configActual['opcion_activa_web']
  ?? 'Sin definir';

$nombreProyectoActual =
  trim(
    (string)(
      $configActual['nombre_proyecto']
      ?? ''
    )
  );

if ($nombreProyectoActual === '') {
  $nombreProyectoActual = 'Estadística360';
}

/* ============================================================
   INFORMACIÓN ACTIVA
============================================================ */

require_once './admin/classes/Sondeo.php';
require_once './admin/classes/FichaTecnicaEncuesta.php';
require_once './admin/classes/Pregunta.php';

// Obtener sondeo activo
$sondeos =
  Sondeo::getAll(null)['output']['response']
  ?? [];

$active_sondeo = null;

foreach ($sondeos as $sondeo) {

  if (
    ($sondeo['habilitado'] ?? 'no') === 'si'
    &&
    ($sondeo['vigente'] ?? false)
  ) {

    $active_sondeo =
      $sondeo;

    break;
  }
}

// Obtener cuestionario activo
$fichas =
  FichaTecnicaEncuesta::getAll(null)['output']['response']
  ?? [];

$active_ficha = null;

foreach ($fichas as $ficha) {

  if (
    ($ficha['habilitado'] ?? 'no')
    ===
    'si'
  ) {

    $active_ficha =
      $ficha;

    break;
  }
}

$preguntas_cuestionario = [];

if ($active_ficha) {

  $preguntasResponse =
    Pregunta::getAll([
      'tbl_ficha_tecnica_encuesta_id'
      =>
      $active_ficha['id']
    ]);

  $preguntas_cuestionario =
    $preguntasResponse['output']['response']
    ?? [];
}

$totalPreguntasActivas =
  count(
    $preguntas_cuestionario
  );
?>

<body class="config360-page">

  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>


  <?php include './admin/include/navbar.php'; ?>

  <?php include './admin/include/header.php'; ?>


  <style>

    /* ==========================================================
       ESTADÍSTICA360
       APPLICATION CONTROL CENTER
       ----------------------------------------------------------
       Diseño visual sin modificar el flujo de CONFIGURACION.js
    ========================================================== */

    :root{

      --cfg-navy-950:#06172D;
      --cfg-navy-900:#092147;
      --cfg-navy-800:#123A74;

      --cfg-blue-700:#20427F;
      --cfg-blue-600:#2D63BD;
      --cfg-blue-500:#4B8CF7;

      --cfg-cyan:#1DB6DB;
      --cfg-violet:#7568E8;

      --cfg-success:#12B981;
      --cfg-warning:#F59E0B;
      --cfg-danger:#E5484D;

      --cfg-page:#F3F6FB;
      --cfg-card:#FFFFFF;
      --cfg-soft:#F8FAFD;

      --cfg-text:#101828;
      --cfg-text-2:#344054;
      --cfg-muted:#667085;
      --cfg-light:#98A2B3;

      --cfg-line:#E5EAF1;

      --cfg-r-xxl:30px;
      --cfg-r-xl:24px;
      --cfg-r-lg:18px;
      --cfg-r-md:14px;

      --cfg-shadow:
        0 24px 68px
        rgba(15,23,42,.10);

      --cfg-shadow-soft:
        0 12px 34px
        rgba(15,23,42,.065);
    }


    *{
      box-sizing:border-box;
    }


    html{
      scroll-behavior:smooth;
    }


    body.config360-page{

      margin:0;

      color:
        var(--cfg-text);

      font-family:
        "Inter",
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

      overflow-x:hidden;

      -webkit-font-smoothing:
        antialiased;

      background:

        radial-gradient(
          920px 500px at 3% -5%,
          rgba(75,140,247,.12),
          transparent 64%
        ),

        radial-gradient(
          760px 440px at 103% 5%,
          rgba(117,104,232,.07),
          transparent 64%
        ),

        linear-gradient(
          180deg,
          #F8FAFD 0%,
          #F2F5FA 100%
        );
    }


    body.config360-page::before{

      content:"";

      position:fixed;

      inset:0;

      z-index:-1;

      pointer-events:none;

      opacity:.30;

      background-image:

        linear-gradient(
          rgba(32,66,127,.023) 1px,
          transparent 1px
        ),

        linear-gradient(
          90deg,
          rgba(32,66,127,.023) 1px,
          transparent 1px
        );

      background-size:
        36px 36px;

      mask-image:
        linear-gradient(
          to bottom,
          #000,
          transparent 84%
        );
    }


    /* ==========================================================
       LAYOUT
       Se eliminan los espacios gigantes del archivo anterior.
    ========================================================== */

    .content{

      padding-top:
        18px !important;

      padding-bottom:
        38px !important;

      margin-top:
        0 !important;
    }


    .container-xxl-saas{

      width:100%;

      max-width:
        1660px;

      margin:
        0 auto;

      padding-left:
        18px !important;

      padding-right:
        18px !important;
    }


    /* ==========================================================
       HERO
    ========================================================== */

    .cfg-hero{

      position:relative;

      isolation:isolate;

      overflow:hidden;

      min-height:
        232px;

      margin-bottom:
        16px;

      padding:
        30px;

      border:
        1px solid
        rgba(255,255,255,.12);

      border-radius:
        var(--cfg-r-xxl);

      color:#fff;

      background:

        radial-gradient(
          570px 280px at 9% 0%,
          rgba(75,140,247,.36),
          transparent 66%
        ),

        radial-gradient(
          480px 270px at 94% 10%,
          rgba(117,104,232,.18),
          transparent 67%
        ),

        linear-gradient(
          135deg,
          #173E7B 0%,
          #102A56 47%,
          #07162E 100%
        );

      box-shadow:
        0 30px 80px
        rgba(8,28,63,.24);
    }


    .cfg-hero::before{

      content:"";

      position:absolute;

      z-index:-1;

      width:440px;
      height:440px;

      right:-160px;
      top:-225px;

      border:
        1px solid
        rgba(255,255,255,.075);

      border-radius:50%;

      box-shadow:

        0 0 0 45px
        rgba(255,255,255,.021),

        0 0 0 92px
        rgba(255,255,255,.015),

        0 0 0 138px
        rgba(255,255,255,.010);
    }


    .cfg-hero-grid{

      display:grid;

      grid-template-columns:
        1fr;

      gap:22px;

      align-items:start;
    }


    .cfg-eyebrow{

      display:inline-flex;

      align-items:center;

      gap:8px;

      min-height:
        32px;

      margin-bottom:
        13px;

      padding:
        7px 11px;

      border:
        1px solid
        rgba(255,255,255,.14);

      border-radius:
        999px;

      color:
        rgba(255,255,255,.88);

      background:
        rgba(255,255,255,.075);

      backdrop-filter:
        blur(12px);

      font-size:
        .67rem;

      font-weight:
        800;

      letter-spacing:
        .62px;

      text-transform:
        uppercase;
    }


    .cfg-live-dot{

      width:7px;
      height:7px;

      border-radius:50%;

      background:#5DE4A0;

      box-shadow:

        0 0 0 5px
        rgba(93,228,160,.11),

        0 0 16px
        rgba(93,228,160,.45);
    }


    .cfg-hero h1{

      margin:0;

      color:#fff;

      font-family:
        "Manrope",
        "Inter",
        sans-serif;

      font-size:
        clamp(
          1.9rem,
          3vw,
          3rem
        );

      line-height:
        1.04;

      font-weight:
        800;

      letter-spacing:
        -1.5px;
    }


    .cfg-hero h1 span{

      color:#B7D0FF;
    }


    .cfg-hero p{

      max-width:
        980px;

      margin:
        11px 0 0;

      color:
        rgba(255,255,255,.70);

      font-size:
        .91rem;

      line-height:
        1.67;

      font-weight:
        500;
    }


    .cfg-hero-pills{

      display:flex;

      flex-wrap:wrap;

      gap:8px;

      margin-top:
        18px;
    }


    .cfg-hero-pill{

      display:inline-flex;

      align-items:center;

      gap:7px;

      min-height:
        35px;

      padding:
        8px 11px;

      border:
        1px solid
        rgba(255,255,255,.10);

      border-radius:
        11px;

      color:
        rgba(255,255,255,.84);

      background:
        rgba(255,255,255,.07);

      font-size:
        .67rem;

      font-weight:
        700;
    }


    .cfg-hero-pill i{

      color:#A7C7FF;
    }

    .cfg-hero-copy{
      max-width:1100px;
    }


    /* ==========================================================
       KPI
    ========================================================== */

    .cfg-kpis{

      display:grid;

      grid-template-columns:

        repeat(
          4,
          minmax(
            0,
            1fr
          )
        );

      gap:12px;

      min-width:0;

      width:100%;
    }


    .cfg-kpi{

      min-height:
        112px;

      padding:
        14px;

      border:
        1px solid
        rgba(255,255,255,.12);

      border-radius:
        17px;

      background:

        linear-gradient(
          145deg,
          rgba(255,255,255,.115),
          rgba(255,255,255,.05)
        );

      backdrop-filter:
        blur(14px);

      transition:

        transform .22s ease,

        border-color .22s ease,

        background .22s ease;
    }


    .cfg-kpi:hover{

      transform:
        translateY(-4px);

      border-color:
        rgba(255,255,255,.20);

      background:

        linear-gradient(
          145deg,
          rgba(255,255,255,.17),
          rgba(255,255,255,.07)
        );
    }


    .cfg-kpi-icon{

      width:31px;
      height:31px;

      display:flex;

      align-items:center;

      justify-content:center;

      margin-bottom:
        13px;

      border-radius:
        10px;

      color:#D8E8FF;

      background:
        rgba(255,255,255,.10);

      font-size:
        .78rem;
    }


    .cfg-kpi strong{

      display:block;

      color:#fff;

      font-family:
        "Manrope",
        "Inter",
        sans-serif;

      font-size:
        1.21rem;

      line-height:
        1.12;

      font-weight:
        800;

      letter-spacing:
        -.45px;

      overflow:hidden;

      text-overflow:
        ellipsis;

      white-space:
        nowrap;
    }


    .cfg-kpi span{

      display:block;

      margin-top:
        5px;

      color:
        rgba(255,255,255,.58);

      font-size:
        .59rem;

      line-height:
        1.25;

      font-weight:
        700;
    }


    /* ==========================================================
       SYSTEM BAR
    ========================================================== */

    .cfg-system-bar{

      display:flex;

      align-items:center;

      justify-content:space-between;

      gap:12px;

      margin-bottom:
        16px;

      padding:
        13px 15px;

      border:
        1px solid
        var(--cfg-line);

      border-radius:
        18px;

      background:
        rgba(255,255,255,.92);

      box-shadow:
        var(--cfg-shadow-soft);

      backdrop-filter:
        blur(12px);
    }


    .cfg-system-copy{

      display:flex;

      align-items:center;

      gap:10px;
    }


    .cfg-system-icon{

      width:38px;
      height:38px;

      flex:
        0 0 38px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        12px;

      color:
        var(--cfg-blue-700);

      background:
        #EDF4FF;
    }


    .cfg-system-copy strong{

      display:block;

      color:
        var(--cfg-text);

      font-size:
        .78rem;

      font-weight:
        800;
    }


    .cfg-system-copy span{

      display:block;

      margin-top:
        2px;

      color:
        var(--cfg-light);

      font-size:
        .63rem;

      font-weight:
        600;
    }


    .cfg-status{

      display:inline-flex;

      align-items:center;

      gap:6px;

      min-height:
        31px;

      padding:
        6px 10px;

      border:
        1px solid
        #D1FAE5;

      border-radius:
        999px;

      color:#06795B;

      background:
        #ECFDF5;

      font-size:
        .63rem;

      font-weight:
        800;
    }


    /* ==========================================================
       MAIN CARD
    ========================================================== */

    .card-pro{

      overflow:hidden;

      border:
        1px solid
        var(--cfg-line) !important;

      border-radius:
        var(--cfg-r-xl) !important;

      background:
        #fff !important;

      box-shadow:
        var(--cfg-shadow) !important;
    }


    .card-pro .card-header{

      min-height:
        74px;

      padding:
        15px 18px !important;

      border-bottom:
        1px solid
        #EDF0F5 !important;

      background:

        radial-gradient(
          320px 120px at 4% 0%,
          rgba(75,140,247,.06),
          transparent 72%
        ),

        linear-gradient(
          180deg,
          #FFFFFF,
          #FBFCFF
        ) !important;
    }


    .cfg-card-title{

      display:flex;

      align-items:center;

      gap:11px;
    }


    .cfg-card-icon{

      width:42px;
      height:42px;

      flex:
        0 0 42px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        13px;

      color:
        var(--cfg-blue-700);

      background:
        #EDF4FF;

      font-size:
        .92rem;
    }


    .cfg-card-title h2{

      margin:0;

      color:
        #182230;

      font-family:
        "Manrope",
        "Inter",
        sans-serif;

      font-size:
        .98rem;

      font-weight:
        800;
    }


    .cfg-card-title p{

      margin:
        3px 0 0;

      color:
        var(--cfg-light);

      font-size:
        .64rem;

      font-weight:
        600;
    }


    .card-pro .card-body{

      padding:
        18px;
    }


    /* ==========================================================
       CONFIG SECTIONS
    ========================================================== */

    .cfg-section{

      padding:
        16px;

      border:
        1px solid
        #E6EBF2;

      border-radius:
        18px;

      background:

        linear-gradient(
          145deg,
          #FFFFFF,
          #FBFCFF
        );
    }


    .cfg-section + .cfg-section{

      margin-top:
        13px;
    }


    .cfg-section-head{

      display:flex;

      align-items:center;

      justify-content:space-between;

      gap:12px;

      margin-bottom:
        14px;
    }


    .cfg-section-title{

      display:flex;

      align-items:center;

      gap:9px;
    }


    .cfg-section-dot{

      width:9px;
      height:9px;

      border-radius:
        50%;

      background:

        linear-gradient(
          135deg,
          var(--cfg-blue-500),
          var(--cfg-blue-700)
        );

      box-shadow:

        0 0 0 4px
        rgba(75,140,247,.09);
    }


    .cfg-section-title h3{

      margin:0;

      color:
        var(--cfg-text);

      font-size:
        .78rem;

      font-weight:
        800;
    }


    .cfg-section-help{

      color:
        var(--cfg-light);

      font-size:
        .60rem;

      font-weight:
        600;
    }


    /* ==========================================================
       FORM
    ========================================================== */

    .form-floating>.form-control,
    .form-floating>.form-select{

      min-height:
        58px;

      border:
        1px solid
        #D9E0EA !important;

      border-radius:
        14px !important;

      color:
        var(--cfg-text-2);

      background:
        #FBFCFE;

      font-size:
        .78rem;

      font-weight:
        650;

      box-shadow:
        none !important;

      transition:

        border-color .18s ease,

        box-shadow .18s ease,

        background .18s ease;
    }


    .form-floating>.form-control:hover,
    .form-floating>.form-select:hover{

      border-color:
        #BCC8D9 !important;

      background:
        #fff;
    }


    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus{

      border-color:
        var(--cfg-blue-500) !important;

      background:
        #fff;

      box-shadow:

        0 0 0 4px
        rgba(75,140,247,.10) !important;
    }


    .form-floating>label{

      color:
        #667085;

      font-size:
        .75rem;

      font-weight:
        650;
    }


    .help{

      margin-top:
        6px;

      color:
        var(--cfg-light);

      font-size:
        .59rem;

      line-height:
        1.45;

      font-weight:
        600;
    }


    #comentarios{

      min-height:
        92px;

      border:
        1px solid
        #D9E0EA !important;

      border-radius:
        14px !important;

      color:
        var(--cfg-text-2);

      background:
        #FBFCFE;

      font-size:
        .76rem;

      box-shadow:
        none !important;
    }


    #comentarios:focus{

      border-color:
        var(--cfg-blue-500) !important;

      background:
        #fff;

      box-shadow:

        0 0 0 4px
        rgba(75,140,247,.10) !important;
    }


    /* ==========================================================
       BRANDING / LOGO
    ========================================================== */

    .cfg-brand-grid{

      display:grid;

      grid-template-columns:
        repeat(
          2,
          minmax(0,1fr)
        );

      gap:12px;
    }


    .logo-card{

      position:relative;

      overflow:hidden;

      height:100%;

      padding:
        14px;

      border:
        1px solid
        #DDE7F3;

      border-radius:
        17px;

      background:

        radial-gradient(
          260px 130px at 5% 0%,
          rgba(75,140,247,.055),
          transparent 72%
        ),

        linear-gradient(
          180deg,
          #FBFDFF,
          #F7FAFD
        );

      box-shadow:
        0 10px 24px
        rgba(15,23,42,.045);
    }


    .cfg-brand-head{

      display:flex;

      align-items:center;

      justify-content:space-between;

      gap:10px;

      margin-bottom:
        11px;
    }


    .cfg-brand-title{

      display:flex;

      align-items:center;

      gap:9px;
    }


    .cfg-brand-icon{

      width:36px;
      height:36px;

      flex:
        0 0 36px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        11px;

      color:
        var(--cfg-blue-700);

      background:
        #EDF4FF;
    }


    .cfg-brand-title strong{

      display:block;

      color:
        var(--cfg-text-2);

      font-size:
        .70rem;

      font-weight:
        800;
    }


    .cfg-brand-title span{

      display:block;

      margin-top:
        2px;

      color:
        var(--cfg-light);

      font-size:
        .57rem;

      font-weight:
        600;
    }


    .logo-preview{

      width:100%;

      min-height:
        190px;

      display:flex;

      align-items:center;

      justify-content:center;

      padding:
        20px;

      border:
        1px solid
        #E2E8F0;

      border-radius:
        15px;

      background:

        linear-gradient(
          45deg,
          #F8FAFC 25%,
          transparent 25%,
          transparent 75%,
          #F8FAFC 75%,
          #F8FAFC
        ),

        linear-gradient(
          45deg,
          #F8FAFC 25%,
          #fff 25%,
          #fff 75%,
          #F8FAFC 75%,
          #F8FAFC
        );

      background-size:
        18px 18px;

      background-position:
        0 0,
        9px 9px;
    }


    .logo-preview img{

      max-height:
        130px;

      width:auto;

      max-width:
        100%;

      object-fit:
        contain;

      filter:

        drop-shadow(
          0 8px 18px
          rgba(15,23,42,.08)
        );
    }


    .iframe-uploader{

      width:100%;

      height:
        190px;

      overflow:hidden;

      border:
        1px dashed
        #BFCFE3;

      border-radius:
        15px;

      background:
        #fff;
    }


    #ifm1{

      display:block;

      width:100%;

      height:100%;

      border:0;

      background:#fff;
    }


    /* ==========================================================
       BUTTONS
    ========================================================== */

    .btn-brand,
    .btn-soft{

      min-height:
        43px;

      display:inline-flex;

      align-items:center;

      justify-content:center;

      gap:7px;

      padding:
        9px 15px;

      border-radius:
        12px !important;

      font-size:
        .70rem;

      font-weight:
        800;

      transition:

        transform .18s ease,

        box-shadow .18s ease,

        border-color .18s ease,

        background .18s ease;
    }


    .btn-brand{

      border:0 !important;

      color:#fff !important;

      background:

        linear-gradient(
          135deg,
          var(--cfg-blue-500),
          var(--cfg-blue-600) 50%,
          var(--cfg-blue-700)
        ) !important;

      box-shadow:

        0 11px 23px
        rgba(32,66,127,.22);
    }


    .btn-brand:hover{

      transform:
        translateY(-2px);

      box-shadow:

        0 16px 30px
        rgba(32,66,127,.29);
    }


    .btn-soft{

      border:
        1px solid
        #D7E2F2 !important;

      color:
        var(--cfg-blue-700) !important;

      background:
        #fff !important;
    }


    .btn-soft:hover{

      transform:
        translateY(-1px);

      border-color:
        #BFD2EC !important;

      background:
        #F5F9FF !important;
    }


    /* ==========================================================
       ACTION BAR
    ========================================================== */

    .action-bar{

      position:sticky;

      bottom:12px;

      z-index:20;

      margin-top:
        15px;
    }


    .action-inner{

      display:flex;

      align-items:center;

      justify-content:space-between;

      gap:12px;

      padding:
        11px 12px;

      border:
        1px solid
        rgba(216,225,238,.94);

      border-radius:
        17px;

      background:
        rgba(255,255,255,.92);

      box-shadow:

        0 15px 35px
        rgba(15,23,42,.11);

      backdrop-filter:
        blur(16px);
    }


    .cfg-save-state{

      display:flex;

      align-items:center;

      gap:9px;
    }


    .cfg-save-icon{

      width:34px;
      height:34px;

      flex:
        0 0 34px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        11px;

      color:#07845E;

      background:
        #ECFDF5;
    }


    .cfg-save-state strong{

      display:block;

      color:
        var(--cfg-text-2);

      font-size:
        .68rem;

      font-weight:
        800;
    }


    .cfg-save-state span{

      display:block;

      margin-top:
        2px;

      color:
        var(--cfg-light);

      font-size:
        .59rem;

      font-weight:
        600;
    }


    /* ==========================================================
       ACTIVE INFORMATION CENTER
    ========================================================== */

    #active-info-container{

      margin-top:
        16px;

      padding-bottom:
        0;
    }


    .cfg-active-head{

      display:flex;

      align-items:center;

      justify-content:space-between;

      gap:12px;

      margin-bottom:
        12px;

      padding:
        13px 15px;

      border:
        1px solid
        var(--cfg-line);

      border-radius:
        17px;

      background:
        rgba(255,255,255,.94);

      box-shadow:
        var(--cfg-shadow-soft);
    }


    .cfg-active-title{

      display:flex;

      align-items:center;

      gap:10px;
    }


    .info-icon{

      width:39px;
      height:39px;

      flex:
        0 0 39px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        12px;

      color:#fff;

      background:

        linear-gradient(
          135deg,
          var(--cfg-blue-500),
          var(--cfg-blue-700)
        );

      box-shadow:

        0 9px 20px
        rgba(32,66,127,.16);
    }


    .cfg-active-title h4{

      margin:0;

      color:
        var(--cfg-text);

      font-family:
        "Manrope",
        "Inter",
        sans-serif;

      font-size:
        .88rem;

      font-weight:
        800;
    }


    .cfg-active-title .sub{

      margin-top:
        2px;

      color:
        var(--cfg-light);

      font-size:
        .60rem;

      font-weight:
        600;
    }


    .cfg-active-grid{

      display:grid;

      grid-template-columns:
        minmax(320px,.9fr)
        minmax(420px,1.1fr);

      gap:12px;

      align-items:start;
    }


    #sondeo-info,
    #cuestionario-info{

      margin:0 !important;
    }


    #sondeo-info .card-pro,
    #cuestionario-info .card-pro{

      height:100%;
    }


    .cfg-active-card-head{

      display:flex;

      align-items:center;

      justify-content:space-between;

      gap:10px;
    }


    .cfg-active-card-title{

      display:flex;

      align-items:center;

      gap:9px;
    }


    .cfg-active-card-icon{

      width:36px;
      height:36px;

      flex:
        0 0 36px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        11px;

      color:
        var(--cfg-blue-700);

      background:
        #EDF4FF;
    }


    .cfg-active-card-title h5{

      margin:0;

      color:
        var(--cfg-text);

      font-size:
        .76rem;

      font-weight:
        800;
    }

    .cfg-accordion-toggle{
      width:100%;
      min-height:48px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:12px 14px;
      border:1px solid #E5EAF1;
      border-radius:14px;
      background:linear-gradient(180deg,#FFFFFF,#FBFCFF);
      color:var(--cfg-text-2);
      font-size:.68rem;
      font-weight:800;
      box-shadow:0 7px 18px rgba(15,23,42,.035);
      transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .cfg-accordion-toggle:hover{
      transform:translateY(-1px);
      border-color:#D4E1F1;
      box-shadow:0 12px 24px rgba(15,23,42,.055);
    }

    .cfg-accordion-toggle .left{
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
      text-align:left;
    }

    .cfg-accordion-toggle .left i{
      width:30px;
      height:30px;
      flex:0 0 30px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:10px;
      color:var(--cfg-blue-700);
      background:#EEF5FF;
      font-size:.68rem;
    }

    .cfg-accordion-toggle .meta{
      display:flex;
      flex-direction:column;
      gap:2px;
      min-width:0;
    }

    .cfg-accordion-toggle .meta strong{
      color:var(--cfg-text-2);
      font-size:.68rem;
      font-weight:800;
      line-height:1.25;
    }

    .cfg-accordion-toggle .meta span{
      color:var(--cfg-light);
      font-size:.58rem;
      font-weight:600;
    }

    .cfg-accordion-toggle .toggle-icon{
      color:var(--cfg-blue-600);
      font-size:.86rem;
      transition:transform .18s ease;
    }

    .cfg-accordion-toggle[aria-expanded="true"] .toggle-icon{
      transform:rotate(45deg);
    }

    .cfg-collapsible-block{
      margin-top:12px;
    }

    .cfg-collapsible-body{
      margin-top:10px;
      padding:10px;
      border:1px solid #E8EDF3;
      border-radius:16px;
      background:#FBFCFE;
    }



    .cfg-live-badge{

      display:inline-flex;

      align-items:center;

      gap:5px;

      padding:
        5px 8px;

      border:
        1px solid
        #D1FAE5;

      border-radius:
        999px;

      color:#06795B;

      background:
        #ECFDF5;

      font-size:
        .58rem;

      font-weight:
        800;
    }


    .cfg-active-name{

      margin:
        0 0 5px;

      color:
        #1D2939;

      font-family:
        "Manrope",
        "Inter",
        sans-serif;

      font-size:
        .88rem;

      line-height:
        1.35;

      font-weight:
        800;
    }


    .cfg-active-description{

      margin:
        0 0 12px;

      color:
        #667085;

      font-size:
        .65rem;

      line-height:
        1.55;
    }


    .cfg-data-list{

      overflow:hidden;

      border:
        1px solid
        #E7ECF3;

      border-radius:
        13px;
    }


    .cfg-data-row{

      display:flex;

      align-items:flex-start;

      gap:10px;

      padding:
        10px 11px;

      border-bottom:
        1px solid
        #EEF1F5;

      background:#fff;
    }


    .cfg-data-row:last-child{

      border-bottom:0;
    }


    .cfg-data-row i{

      width:28px;
      height:28px;

      flex:
        0 0 28px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        9px;

      color:
        var(--cfg-blue-700);

      background:
        #EEF5FF;

      font-size:
        .63rem;
    }


    .cfg-data-row strong{

      color:
        #344054;

      font-size:
        .64rem;

      font-weight:
        800;
    }


    .cfg-data-row span{

      color:
        #667085;

      font-size:
        .63rem;

      line-height:
        1.45;
    }


    /* ==========================================================
       ACCORDION
    ========================================================== */

    .custom-accordion
    .accordion-item{

      overflow:hidden;

      margin-bottom:
        8px;

      border:
        1px solid
        #E5EAF1 !important;

      border-radius:
        13px !important;

      box-shadow:

        0 7px 18px
        rgba(15,23,42,.035);
    }


    .custom-accordion
    .accordion-item:last-child{

      margin-bottom:0;
    }


    .custom-accordion
    .accordion-button{

      min-height:
        50px;

      color:
        var(--cfg-text-2) !important;

      background:
        #fff !important;

      font-size:
        .66rem;

      font-weight:
        800;

      box-shadow:
        none !important;
    }


    .custom-accordion
    .accordion-button:not(.collapsed){

      color:
        var(--cfg-blue-700) !important;

      background:

        linear-gradient(
          90deg,
          #F2F7FF,
          #FFFFFF
        ) !important;
    }


    .custom-accordion
    .accordion-body{

      padding:
        10px 12px;

      background:
        #FBFCFE;
    }


    .custom-accordion
    .list-group-item{

      padding:
        8px 10px;

      color:
        #475467;

      border-color:
        #E8ECF2;

      background:
        transparent;

      font-size:
        .63rem;
    }


    /* ==========================================================
       EMPTY STATE
    ========================================================== */

    .cfg-empty{

      display:flex;

      align-items:center;

      gap:11px;

      padding:
        13px;

      border:
        1px dashed
        #D3DCE8;

      border-radius:
        14px;

      background:
        #FBFCFE;
    }


    .cfg-empty-icon{

      width:40px;
      height:40px;

      flex:
        0 0 40px;

      display:flex;

      align-items:center;

      justify-content:center;

      border-radius:
        12px;

      color:
        #667085;

      background:
        #F2F4F7;
    }


    .cfg-empty strong{

      display:block;

      color:
        #344054;

      font-size:
        .68rem;

      font-weight:
        800;
    }


    .cfg-empty span{

      display:block;

      margin-top:
        2px;

      color:
        #98A2B3;

      font-size:
        .59rem;

      font-weight:
        600;
    }


    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){

      .cfg-hero-grid{

        grid-template-columns:
          1fr;
      }


      .cfg-kpis{

        min-width:0;

        width:100%;
      }
    }


    @media (max-width:991px){

      .container-xxl-saas{

        padding-left:
          13px !important;

        padding-right:
          13px !important;
      }


      .cfg-hero{

        padding:
          23px;
      }


      .cfg-brand-grid,
      .cfg-active-grid{

        grid-template-columns:
          1fr;
      }
    }


    @media (max-width:767px){

      .content{

        padding-top:
          12px !important;
      }


      .container-xxl-saas{

        padding-left:
          10px !important;

        padding-right:
          10px !important;
      }


      .cfg-hero{

        min-height:0;

        padding:
          20px 17px;

        border-radius:
          22px;
      }


      .cfg-hero h1{

        font-size:
          1.8rem;
      }


      .cfg-hero p{

        font-size:
          .80rem;
      }


      .cfg-kpis{

        grid-template-columns:

          repeat(
            2,
            1fr
          );
      }


      .cfg-system-bar{

        align-items:
          flex-start;

        flex-direction:
          column;
      }


      .card-pro{

        border-radius:
          19px !important;
      }


      .card-pro .card-header{

        padding:
          14px !important;
      }


      .card-pro .card-body{

        padding:
          12px;
      }


      .cfg-section{

        padding:
          13px;
      }


      .action-inner{

        align-items:
          stretch;

        flex-direction:
          column;
      }


      .action-inner
      > .d-flex{

        width:100%;
      }


      .action-inner
      .btn{

        flex:1;
      }


      .cfg-active-head{

        align-items:
          flex-start;

        flex-direction:
          column;
      }
    }


    @media (max-width:480px){

      .cfg-kpis{

        gap:7px;
      }


      .cfg-kpi{

        min-height:
          96px;

        padding:
          12px;
      }


      .cfg-kpi strong{

        font-size:
          1.05rem;
      }


      .cfg-kpi span{

        font-size:
          .56rem;
      }


      .cfg-save-state{

        display:none;
      }


      .action-inner{

        padding:
          9px;
      }
    }


    @media (prefers-reduced-motion:reduce){

      *,
      *::before,
      *::after{

        animation-duration:
          .01ms !important;

        animation-iteration-count:
          1 !important;

        transition-duration:
          .01ms !important;

        scroll-behavior:
          auto !important;
      }
    }

  </style>


  <div class="content">


    <div class="container-fluid container-xxl-saas">


      <!-- =====================================================
           HERO
      ====================================================== -->

      <section class="cfg-hero">


        <div class="cfg-hero-grid">


          <div class="cfg-hero-copy">


            <div class="cfg-eyebrow">

              <span class="cfg-live-dot"></span>

              Estadística360 · Application Control Center

            </div>


            <h1>

              Configuración
              <span>Global</span>

            </h1>


            <p>

              Controla la identidad del proyecto, territorio principal,
              módulo público activo y recursos de marca desde un único
              centro administrativo.

            </p>


            <div class="cfg-hero-pills">


              <span class="cfg-hero-pill">

                <i class="fas fa-shield-halved"></i>

                Acceso administrativo

              </span>


              <span class="cfg-hero-pill">

                <i class="fas fa-location-dot"></i>

                Territorio predeterminado

              </span>


              <span class="cfg-hero-pill">

                <i class="fas fa-window-maximize"></i>

                Portal público configurable

              </span>


            </div>


          </div>


          <div class="cfg-kpis">


            <div class="cfg-kpi">


              <div class="cfg-kpi-icon">

                <i class="fas fa-cube"></i>

              </div>


              <strong title="<?= h($nombreProyectoActual) ?>">

                <?= h($nombreProyectoActual) ?>

              </strong>


              <span>
                Proyecto configurado
              </span>


            </div>


            <div class="cfg-kpi">


              <div class="cfg-kpi-icon">

                <i class="fas fa-map"></i>

              </div>


              <strong>

                <?= (int)$totalDepartamentos ?>

              </strong>


              <span>
                Departamentos disponibles
              </span>


            </div>


            <div class="cfg-kpi">


              <div class="cfg-kpi-icon">

                <i class="fas fa-display"></i>

              </div>


              <strong>

                <?= h(ucfirst((string)$opcionActivaActual)) ?>

              </strong>


              <span>
                Modo público actual
              </span>


            </div>


            <div class="cfg-kpi">


              <div class="cfg-kpi-icon">

                <i class="fas fa-user-shield"></i>

              </div>


              <strong>

                Admin

              </strong>


              <span>
                Nivel de acceso
              </span>


            </div>


          </div>


        </div>


      </section>


      <!-- =====================================================
           SYSTEM STATUS
      ====================================================== -->

      <section class="cfg-system-bar">


        <div class="cfg-system-copy">


          <div class="cfg-system-icon">

            <i class="fas fa-sliders"></i>

          </div>


          <div>


            <strong>
              Centro de control de la aplicación
            </strong>


            <span>
              Cambios globales que afectan la experiencia administrativa y pública.
            </span>


          </div>


        </div>


        <span class="cfg-status">

          <i class="fas fa-circle-check"></i>

          Configuración disponible

        </span>


      </section>


      <!-- =====================================================
           CONFIGURATION FORM
      ====================================================== -->

      <section class="card card-pro">


        <div class="card-header">


          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">


            <div class="cfg-card-title">


              <div class="cfg-card-icon">

                <i class="fas fa-gears"></i>

              </div>


              <div>


                <h2>
                  Formulario de Configuración
                </h2>


                <p>
                  Define identidad, territorio, portal público y marca.
                </p>


              </div>


            </div>


            <span
                style="
                  color:#667085;
                  font-size:.62rem;
                  font-weight:700;
                ">

              <i class="fas fa-asterisk me-1"></i>

              Campos principales requeridos

            </span>


          </div>


        </div>


        <div class="card-body">


          <form action="">


            <input
                type="hidden"
                name="op"
                id="op">


            <input
                type="hidden"
                name="idConfig"
                id="idConfig">


            <input
                type="hidden"
                name="filtro"
                id="filtro"
                value="vereda">


            <input
                type="hidden"
                name="filtroVeredaById"
                id="filtroVeredaById"
                value="si">


            <!-- ===============================================
                 IDENTITY
            ================================================ -->

            <section class="cfg-section">


              <div class="cfg-section-head">


                <div class="cfg-section-title">

                  <span class="cfg-section-dot"></span>

                  <h3>
                    Identidad del proyecto
                  </h3>

                </div>


                <span class="cfg-section-help">

                  Nombre y notas administrativas

                </span>


              </div>


              <div class="row g-3">


                <div class="col-12 col-lg-7">


                  <div class="form-floating">


                    <input
                        class="form-control"
                        type="text"
                        id="nombre_proyecto"
                        name="nombre_proyecto"
                        placeholder="Ingrese nombre proyecto"
                        required>


                    <label for="nombre_proyecto">

                      Nombre del proyecto

                      <span class="text-danger">*</span>

                    </label>


                  </div>


                  <div class="help">

                    Este nombre se utiliza en encabezados, reportes y vistas principales.

                  </div>


                </div>


                <div class="col-12 col-lg-5">


                  <div class="form-floating">


                    <select
                        class="form-select"
                        id="opcion_activa_web"
                        name="opcion_activa_web">


                      <option value="sondeo">
                        Sondeo
                      </option>


                      <option value="cuestionario">
                        Cuestionario
                      </option>


                      <option value="ambos">
                        Ambos (Sondeo y Cuestionario)
                      </option>


                    </select>


                    <label for="opcion_activa_web">

                      Opción activa web

                    </label>


                  </div>


                  <div class="help">

                    Define qué experiencia estará disponible en el portal público.

                  </div>


                </div>


                <div class="col-12">


                  <label
                      class="form-label"
                      for="comentarios"
                      style="
                        color:#344054;
                        font-size:.68rem;
                        font-weight:800;
                      ">

                    Comentarios internos

                  </label>


                  <textarea
                      class="form-control"
                      id="comentarios"
                      name="comentarios"
                      placeholder="Notas internas sobre esta configuración"
                      rows="3"></textarea>


                  <div class="help">

                    Puedes documentar cambios, criterios o información administrativa.

                  </div>


                </div>


              </div>


            </section>


            <!-- ===============================================
                 TERRITORY
            ================================================ -->

            <section class="cfg-section">


              <div class="cfg-section-head">


                <div class="cfg-section-title">

                  <span class="cfg-section-dot"></span>

                  <h3>
                    Territorio predeterminado
                  </h3>

                </div>


                <span class="cfg-section-help">

                  Departamento → municipio → vereda

                </span>


              </div>


              <div class="row g-3">


                <div class="col-12 col-md-4">


                  <div class="form-floating">


                    <select
                        class="form-select ocultar-select"
                        id="departamentoId"
                        name="departamentoId"
                        onchange="CONFIGURACION.getMunicipios();">

                      <?php echo $optionDep; ?>

                    </select>


                    <label for="departamentoId">

                      Departamento principal

                      <span class="text-danger">*</span>

                    </label>


                  </div>


                  <div class="help">

                    Define el departamento cargado inicialmente por el sistema.

                  </div>


                </div>


                <div class="col-12 col-md-4">


                  <div class="form-floating">


                    <select
                        class="form-select"
                        id="tbl_municipio_id"
                        name="tbl_municipio_id"
                        onchange="CONFIGURACION.getVeredasByMunicipioId();">
                    </select>


                    <label for="tbl_municipio_id">

                      Municipio principal

                      <span class="text-danger">*</span>

                    </label>


                  </div>


                  <div class="help">

                    Se carga automáticamente según el departamento seleccionado.

                  </div>


                </div>


                <div class="col-12 col-md-4">


                  <div class="form-floating">


                    <select
                        class="form-select"
                        id="tbl_vereda_id"
                        name="tbl_vereda_id">
                    </select>


                    <label for="tbl_vereda_id">

                      Vereda principal

                      <span class="text-danger">*</span>

                    </label>


                  </div>


                  <div class="help">

                    Se carga automáticamente según el municipio seleccionado.

                  </div>


                </div>


              </div>


            </section>


            <!-- ===============================================
                 BRAND
            ================================================ -->

            <section class="cfg-section">


              <div class="cfg-section-head">


                <div class="cfg-section-title">

                  <span class="cfg-section-dot"></span>

                  <h3>
                    Identidad visual
                  </h3>

                </div>


                <span class="cfg-section-help">

                  Logo actual y carga de nueva imagen

                </span>


              </div>


              <div class="cfg-brand-grid">


                <!-- LOGO ACTUAL -->

                <article class="logo-card">


                  <div class="cfg-brand-head">


                    <div class="cfg-brand-title">


                      <div class="cfg-brand-icon">

                        <i class="fas fa-image"></i>

                      </div>


                      <div>


                        <strong>
                          Logo actual
                        </strong>


                        <span>
                          Previsualización de la identidad activa
                        </span>


                      </div>


                    </div>


                    <span
                        style="
                          color:#98A2B3;
                          font-size:.57rem;
                          font-weight:700;
                        ">

                      Preview

                    </span>


                  </div>


                  <div
                      id="divLogoActual"
                      style="display:block;">


                    <div class="logo-preview">


                      <img
                          id="logoPreview"
                          src=""
                          alt="Logo">


                    </div>


                  </div>


                  <div class="help mt-2">

                    Este es el recurso de marca que actualmente utiliza la aplicación.

                  </div>


                </article>


                <!-- UPLOADER -->

                <article class="logo-card">


                  <div class="cfg-brand-head">


                    <div class="cfg-brand-title">


                      <div class="cfg-brand-icon">

                        <i class="fas fa-cloud-arrow-up"></i>

                      </div>


                      <div>


                        <strong>
                          Subir nuevo logo
                        </strong>


                        <span>
                          Recomendado: PNG con fondo transparente
                        </span>


                      </div>


                    </div>


                  </div>


                  <div class="iframe-uploader">


                    <iframe
                        id="ifm1"
                        name="ifm1"
                        src="upload.php"
                        scrolling="no"
                        frameborder="0">
                    </iframe>


                  </div>


                  <div class="help mt-2">

                    Después de cargar el archivo, guarda la configuración.

                  </div>


                </article>


              </div>


            </section>


            <!-- ===============================================
                 ACTION BAR
            ================================================ -->

            <div class="action-bar">


              <div class="action-inner">


                <div class="cfg-save-state">


                  <div class="cfg-save-icon">

                    <i class="fas fa-circle-check"></i>

                  </div>


                  <div>


                    <strong>
                      Configuración preparada
                    </strong>


                    <span>
                      Guarda los cambios después de modificar ubicación, portal o logo.
                    </span>


                  </div>


                </div>


                <div class="d-flex align-items-center gap-2">


                  <button
                      type="button"
                      class="btn btn-soft"
                      onclick="location.reload();">

                    <i class="fas fa-rotate"></i>

                    Recargar

                  </button>


                  <?php if ($create && $edit) { ?>


                    <button
                        type="button"
                        onclick="CONFIGURACION.savedata();"
                        class="btn btn-brand">

                      <i class="fas fa-floppy-disk"></i>

                      Guardar cambios

                    </button>


                  <?php } ?>


                </div>


              </div>


            </div>


          </form>


        </div>


      </section>


      <!-- =====================================================
           ACTIVE INFORMATION
      ====================================================== -->

      <section id="active-info-container">


        <div class="cfg-active-head">


          <div class="cfg-active-title">


            <div class="info-icon">

              <i class="fas fa-broadcast-tower"></i>

            </div>


            <div>


              <h4>
                Información Activa
              </h4>


              <div class="sub">

                Vista previa del contenido disponible actualmente en el portal público.

              </div>


            </div>


          </div>


          <span
              id="cfgActiveModeBadge"
              style="
                display:inline-flex;
                align-items:center;
                gap:6px;
                padding:6px 10px;
                border-radius:999px;
                color:#245BA7;
                background:#EEF5FF;
                border:1px solid #DCE8FA;
                font-size:.61rem;
                font-weight:800;
              ">

            <i class="fas fa-display"></i>

            Modo público

          </span>


        </div>


        <div class="cfg-active-grid">


          <!-- ===============================================
               SONDEO ACTIVO
          ================================================ -->

          <div
              id="sondeo-info"
              style="display:none;">


            <div class="card card-pro">


              <div class="card-header">


                <div class="cfg-active-card-head">


                  <div class="cfg-active-card-title">


                    <div class="cfg-active-card-icon">

                      <i class="fas fa-poll"></i>

                    </div>


                    <h5>
                      Sondeo Activo
                    </h5>


                  </div>


                  <?php if ($active_sondeo): ?>


                    <span class="cfg-live-badge">

                      <i class="fas fa-circle"></i>

                      En línea

                    </span>


                  <?php endif; ?>


                </div>


              </div>


              <div class="card-body">


                <?php if ($active_sondeo): ?>


                  <h4 class="cfg-active-name">

                    <?= h($active_sondeo['sondeo'] ?? '') ?>

                  </h4>


                  <p class="cfg-active-description">

                    <?= h($active_sondeo['descripcion_sondeo'] ?? '') ?>

                  </p>


                  <div class="cfg-data-list">


                    <div class="cfg-data-row">


                      <i class="fas fa-list-check"></i>


                      <div>


                        <strong>
                          Tipo
                        </strong>

                        <br>

                        <span>

                          <?= h($active_sondeo['tipo_sondeo'] ?? '') ?>

                        </span>


                      </div>


                    </div>


                    <div class="cfg-data-row">


                      <i class="fas fa-calendar-days"></i>


                      <div>


                        <strong>
                          Vigencia
                        </strong>

                        <br>

                        <span>

                          Desde
                          <?= h($active_sondeo['fecha_inicio'] ?? '') ?>

                          hasta
                          <?= h($active_sondeo['fecha_fin'] ?? '') ?>

                        </span>


                      </div>


                    </div>


                  </div>


                <?php else: ?>


                  <div class="cfg-empty">


                    <div class="cfg-empty-icon">

                      <i class="fas fa-triangle-exclamation"></i>

                    </div>


                    <div>


                      <strong>
                        No hay un sondeo activo
                      </strong>


                      <span>
                        Active un sondeo vigente para visualizarlo aquí.
                      </span>


                    </div>


                  </div>


                <?php endif; ?>


              </div>


            </div>


          </div>


          <!-- ===============================================
               CUESTIONARIO ACTIVO
          ================================================ -->

          <div
              id="cuestionario-info"
              style="display:none;">


            <div class="card card-pro">


              <div class="card-header">


                <div class="cfg-active-card-head">


                  <div class="cfg-active-card-title">


                    <div class="cfg-active-card-icon">

                      <i class="fas fa-file-lines"></i>

                    </div>


                    <h5>
                      Cuestionario Activo
                    </h5>


                  </div>


                  <?php if ($active_ficha): ?>


                    <span class="cfg-live-badge">

                      <i class="fas fa-circle"></i>

                      <?= (int)$totalPreguntasActivas ?>

                      preguntas

                    </span>


                  <?php endif; ?>


                </div>


              </div>


              <div class="card-body">


                <?php if ($active_ficha): ?>


                  <h4 class="cfg-active-name">

                    Ficha:

                    <?= h(
                      $active_ficha['texto_literal_de_la_encuesta_o_preguntas']
                      ?? ''
                    ) ?>

                  </h4>


                  <p class="cfg-active-description">

                    Realizada por:

                    <?= h(
                      $active_ficha['realizada_por_o_encomendada_por']
                      ?? ''
                    ) ?>

                  </p>


                  <?php if (!empty($preguntas_cuestionario)): ?>


                    <div class="cfg-collapsible-block">


                      <button
                          class="cfg-accordion-toggle"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#cuestionarioPreguntasCollapse"
                          aria-expanded="false"
                          aria-controls="cuestionarioPreguntasCollapse">


                        <div class="left">


                          <i class="fas fa-layer-group"></i>


                          <div class="meta">


                            <strong>
                              Ver preguntas del cuestionario activo
                            </strong>


                            <span>
                              Haz clic para desplegar u ocultar el listado completo
                            </span>


                          </div>


                        </div>


                        <i class="fas fa-plus toggle-icon"></i>


                      </button>


                      <div
                          class="collapse"
                          id="cuestionarioPreguntasCollapse">


                        <div class="cfg-collapsible-body">


                          <div
                              class="accordion custom-accordion"
                              id="accordionCuestionario">


                            <?php foreach ($preguntas_cuestionario as $index => $pregunta): ?>


                              <div class="accordion-item">


                                <h2
                                    class="accordion-header"
                                    id="heading-<?= $index ?>">


                                  <button
                                      class="accordion-button collapsed"
                                      type="button"
                                      data-bs-toggle="collapse"
                                      data-bs-target="#collapse-<?= $index ?>"
                                      aria-expanded="false"
                                      aria-controls="collapse-<?= $index ?>">


                                    <i class="fas fa-circle-question me-2"></i>


                                    <span>


                                      <strong>

                                        <?= h($pregunta['orden'] ?? '') ?>.

                                      </strong>


                                      <?= h($pregunta['texto_pregunta'] ?? '') ?>


                                    </span>


                                  </button>


                                </h2>


                                <div
                                    id="collapse-<?= $index ?>"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="heading-<?= $index ?>"
                                    data-bs-parent="#accordionCuestionario">


                                  <div class="accordion-body">


                                    <ul class="list-group">


                                      <?php if (!empty($pregunta['opciones'])): ?>


                                        <?php foreach ($pregunta['opciones'] as $opcion): ?>


                                          <li class="list-group-item d-flex align-items-center gap-2">


                                            <i
                                                class="fas fa-caret-right"
                                                style="color:var(--cfg-blue-600);">
                                            </i>


                                            <?= h($opcion['texto'] ?? '') ?>


                                          </li>


                                        <?php endforeach; ?>


                                      <?php else: ?>


                                        <li class="list-group-item text-muted">

                                          No hay opciones para esta pregunta.

                                        </li>


                                      <?php endif; ?>


                                    </ul>


                                  </div>


                                </div>


                              </div>


                            <?php endforeach; ?>


                          </div>


                        </div>


                      </div>


                    </div>


                  <?php else: ?>


                    <div class="cfg-empty">


                      <div class="cfg-empty-icon">

                        <i class="fas fa-circle-info"></i>

                      </div>


                      <div>


                        <strong>
                          Ficha técnica sin preguntas
                        </strong>


                        <span>
                          Esta ficha todavía no tiene preguntas asociadas.
                        </span>


                      </div>


                    </div>


                  <?php endif; ?>


                <?php else: ?>


                  <div class="cfg-empty">


                    <div class="cfg-empty-icon">

                      <i class="fas fa-triangle-exclamation"></i>

                    </div>


                    <div>


                      <strong>
                        No hay un cuestionario activo
                      </strong>


                      <span>
                        Habilite una ficha técnica para visualizarla aquí.
                      </span>


                    </div>


                  </div>


                <?php endif; ?>


              </div>


            </div>


          </div>


        </div>


      </section>


    </div>


  </div>


  <?php include './admin/include/footer.php'; ?>


  <?php include 'admin/include/gerenic_script.php'; ?>


  <!-- Required Js -->

  <script src="assets/js/vendor-all.min.js"></script>

  <script src="assets/js/plugins/bootstrap.min.js"></script>

  <script src="assets/js/pcoded.min.js"></script>


  <script
      type="text/javascript"
      src="admin/js/configuracion.js">
  </script>


  <script>

    /* ==========================================================
       CARGA ORIGINAL DE CONFIGURACIÓN
       Conservamos CONFIGURACION.editdata().
    ========================================================== */

    (function(){

      var run =
        function(){

          try{

            if (
              typeof CONFIGURACION
              !==
              "undefined"
              &&
              typeof CONFIGURACION.editdata
              ===
              "function"
            ) {

              CONFIGURACION.editdata();

            }

          } catch(e){

            console.error(
              "No fue posible cargar la configuración:",
              e
            );

          }
        };


      setTimeout(
        run,
        1500
      );

    })();

  </script>


  <script>

    /* ==========================================================
       SINCRONIZACIÓN VISUAL DEL MÓDULO ACTIVO
       ----------------------------------------------------------
       Antes dependía solamente de esperar 1600ms.
       Ahora escucha cambios y revisa temporalmente el valor
       durante la carga AJAX de CONFIGURACION.editdata().
    ========================================================== */

    document.addEventListener(
      'DOMContentLoaded',
      function(){

        const selector =
          document.getElementById(
            'opcion_activa_web'
          );

        const sondeoInfo =
          document.getElementById(
            'sondeo-info'
          );

        const cuestionarioInfo =
          document.getElementById(
            'cuestionario-info'
          );

        const badge =
          document.getElementById(
            'cfgActiveModeBadge'
          );


        if (
          !selector
          ||
          !sondeoInfo
          ||
          !cuestionarioInfo
        ) {

          return;

        }


        let lastValue =
          null;


        function getModeLabel(value){

          if (value === 'sondeo') {
            return 'Sondeo';
          }

          if (value === 'cuestionario') {
            return 'Cuestionario';
          }

          if (value === 'ambos') {
            return 'Sondeo + Cuestionario';
          }

          return 'Sin definir';

        }


        function toggleInfo(selectedValue){

          sondeoInfo.style.display =
            'none';

          cuestionarioInfo.style.display =
            'none';


          if (
            selectedValue
            ===
            'sondeo'
          ) {

            sondeoInfo.style.display =
              'block';

          }
          else if (
            selectedValue
            ===
            'cuestionario'
          ) {

            cuestionarioInfo.style.display =
              'block';

          }
          else if (
            selectedValue
            ===
            'ambos'
          ) {

            sondeoInfo.style.display =
              'block';

            cuestionarioInfo.style.display =
              'block';

          }


          if (badge) {

            badge.innerHTML =
              '<i class="fas fa-display"></i> '
              +
              getModeLabel(
                selectedValue
              );

          }

        }


        function syncActiveInfo(){

          const currentValue =
            selector.value
            ||
            '';


          if (
            currentValue
            !==
            lastValue
          ) {

            lastValue =
              currentValue;

            toggleInfo(
              currentValue
            );

          }

        }


        selector.addEventListener(
          'change',
          syncActiveInfo
        );


        /*
          Primera sincronización inmediata.
        */

        syncActiveInfo();


        /*
          CONFIGURACION.editdata() puede rellenar el select
          mediante AJAX sin disparar "change".
          Por eso revisamos brevemente durante la carga.
          Se detiene automáticamente después de 6 segundos.
        */

        let attempts =
          0;

        const syncTimer =
          setInterval(
            function(){

              syncActiveInfo();

              attempts++;


              if (
                attempts
                >=
                24
              ) {

                clearInterval(
                  syncTimer
                );

              }

            },
            250
          );

      }
    );

  </script>


  <?php include 'admin/include/scriptsgober360.php'; ?>


</body>

</html>
