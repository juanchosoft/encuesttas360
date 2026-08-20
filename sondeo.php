<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/include/app_bootstrap.php';

/**
 * ✅ Verificar sesión - Solo usuarios logueados
 */
if (empty($_SESSION['session_user']['id'])) {
    header('Location: registro.php');
    exit;
}

/**
 * ✅ Includes con rutas absolutas
 */
require_once __DIR__ . '/admin/classes/DbConection.php';
require_once __DIR__ . '/admin/classes/Util.php';
require_once __DIR__ . '/admin/classes/Sondeo.php';
require_once __DIR__ . '/admin/classes/RespuestaCuestionario.php';

/**
 * ✅ Configuración
 */
$config = Util::getInformacionConfiguracion();
$opcionActivaWeb = $config[0]['opcion_activa_web'] ?? 'sondeo';

/**
 * ✅ Verificar si el usuario ya ha votado en la opción activa
 */
$usuarioId = $_SESSION['session_user']['id'];
$haVotado = false;

if ($opcionActivaWeb === 'sondeo') {
    $dbConnection = new DbConection();
    $sondeo = new Sondeo($dbConnection);
    $haVotado = $sondeo->verificarSiUsuarioVoto($usuarioId);
} elseif ($opcionActivaWeb === 'cuestionario') {
    $dbConnection = new DbConection();
    $cuestionario = new RespuestaCuestionario($dbConnection);
    $haVotado = $cuestionario->verificarSiUsuarioRespondio($usuarioId);
} elseif ($opcionActivaWeb === 'ambos') {
    $sondeo = new Sondeo(new DbConection());
    $cuestionario = new RespuestaCuestionario(new DbConection());

    $haVotado = $sondeo->verificarSiUsuarioVoto($usuarioId)
             || $cuestionario->verificarSiUsuarioRespondio($usuarioId);
}

/**
 * ✅ Redirigir si no ha votado
 */
if (!$haVotado) {
    if ($opcionActivaWeb === 'sondeo') {
        header('Location: sondeo.php');
    } elseif ($opcionActivaWeb === 'cuestionario') {
        header('Location: encuesta.php');
    } elseif ($opcionActivaWeb === 'ambos') {
        header('Location: dash_responder.php');
    } else {
        header('Location: dash_responder.php');
    }
    exit;
}

$logo360 = 'assets/img/360 Estadisticas-04.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resultados territoriales | 360 Estadísticas</title>

  <!-- ✅ CSRF disponible para JS -->
  <meta name="csrf-token" content="<?= e($CSRF_TOKEN) ?>">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900;950&display=swap" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Libs -->
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

  <!-- Bootstrap + template -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

  <!-- SaaS global -->
  <link href="admin/css/app.css" rel="stylesheet">

  <style>
    :root{
      --c360-bg:#041020;
      --c360-bg2:#061326;
      --c360-bg3:#082342;
      --c360-blue:#2378ff;
      --c360-cyan:#00d4ff;
      --c360-green:#30e6b1;
      --c360-white:#ffffff;
      --c360-text:#eaf6ff;
      --c360-muted:#8fb4d6;
      --c360-dark:#07111f;
      --c360-card:#ffffff;
      --c360-card-soft:#f8fbff;
      --c360-border:rgba(148,210,255,.22);
      --c360-border-dark:rgba(15,23,42,.10);
      --c360-shadow:0 28px 90px rgba(0,0,0,.36);
      --c360-shadow-card:0 22px 50px rgba(3,18,38,.14);
      --radius-xl:34px;
      --radius-lg:28px;
      --radius-md:20px;
    }

    *{
      box-sizing:border-box;
    }

    html{
      min-height:100%;
      scroll-behavior:smooth;
    }

    body{
      min-height:100vh;
      margin:0;
      font-family:"Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color:var(--c360-dark);
      background:
        radial-gradient(850px 420px at 8% 0%, rgba(35,120,255,.30), transparent 62%),
        radial-gradient(760px 420px at 92% 4%, rgba(48,230,177,.20), transparent 62%),
        linear-gradient(145deg, #041020 0%, #061426 42%, #081a33 100%);
      overflow-x:hidden;
    }

    body::before{
      content:"";
      position:fixed;
      inset:0;
      pointer-events:none;
      background-image:
        linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
      background-size:42px 42px;
      mask-image:linear-gradient(to bottom, rgba(0,0,0,.78), transparent 82%);
      z-index:0;
    }

    body::after{
      content:"";
      position:fixed;
      inset:auto -160px -160px auto;
      width:420px;
      height:420px;
      border-radius:999px;
      pointer-events:none;
      background:conic-gradient(from 180deg, var(--c360-blue), var(--c360-cyan), var(--c360-green), var(--c360-blue));
      opacity:.10;
      filter:blur(10px);
      z-index:0;
    }

    .app-shell{
      position:relative;
      z-index:1;
      padding-top:calc(var(--altura-menu, 0px) + 22px);
      padding-bottom:50px;
    }

    .app-container{
      width:min(1320px, calc(100% - 24px));
      margin:0 auto;
    }

    .hero-wrap{
      margin-bottom:20px;
    }

    .hero{
      position:relative;
      overflow:hidden;
      border-radius:34px;
      color:#fff;
      background:
        radial-gradient(620px 240px at 8% 8%, rgba(0,212,255,.34), transparent 64%),
        radial-gradient(620px 260px at 95% 0%, rgba(48,230,177,.22), transparent 64%),
        linear-gradient(135deg, rgba(6,19,38,.98), rgba(8,35,66,.96));
      border:1px solid rgba(148,210,255,.24);
      box-shadow:var(--c360-shadow);
    }

    .hero::before{
      content:"";
      position:absolute;
      inset:0;
      pointer-events:none;
      background:
        linear-gradient(115deg, rgba(255,255,255,.14), transparent 35%),
        radial-gradient(430px 170px at 75% 18%, rgba(35,120,255,.28), transparent 62%);
      z-index:1;
    }

    .hero::after{
      content:"";
      position:absolute;
      right:-90px;
      bottom:-115px;
      width:310px;
      height:310px;
      border-radius:50%;
      background:conic-gradient(from 180deg, var(--c360-blue), var(--c360-cyan), var(--c360-green), var(--c360-blue));
      opacity:.18;
      filter:blur(7px);
      pointer-events:none;
      z-index:1;
    }

    .hero-content{
      position:relative;
      z-index:2;
      padding:28px;
      display:grid;
      grid-template-columns:auto 1fr auto;
      gap:20px;
      align-items:center;
    }

    .hero-logo{
      width:78px;
      height:78px;
      border-radius:24px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      background:rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.18);
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.16),
        0 18px 40px rgba(0,0,0,.28),
        0 0 34px rgba(0,212,255,.18);
      backdrop-filter:blur(16px);
      flex-shrink:0;
    }

    .hero-logo img{
      width:66px;
      max-height:66px;
      object-fit:contain;
      filter:drop-shadow(0 10px 18px rgba(0,0,0,.28));
    }

    .hero-kicker{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:8px 13px;
      border-radius:999px;
      font-weight:900;
      font-size:12px;
      color:#dffbff;
      background:rgba(255,255,255,.10);
      border:1px solid rgba(148,210,255,.22);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.14);
      margin-bottom:9px;
    }

    .hero-kicker i{
      color:var(--c360-green);
    }

    .hero-title{
      margin:0;
      color:#fff;
      font-size:clamp(26px, 3.8vw, 46px);
      font-weight:950;
      line-height:1.04;
      letter-spacing:-1.2px;
    }

    .hero-subtitle{
      margin:11px 0 0;
      max-width:820px;
      color:rgba(234,246,255,.86);
      font-size:15px;
      line-height:1.65;
      font-weight:650;
    }

    .hero-subtitle strong{
      color:#fff;
      font-weight:950;
    }

    .hero-actions{
      display:flex;
      flex-direction:column;
      gap:10px;
      align-items:stretch;
      min-width:190px;
    }

    .btn-hero{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:9px;
      min-height:46px;
      padding:12px 16px;
      border-radius:17px;
      font-weight:950;
      text-decoration:none !important;
      border:1px solid rgba(255,255,255,.18);
      transition:transform .18s ease, filter .18s ease, box-shadow .18s ease;
      white-space:nowrap;
    }

    .btn-hero-primary{
      color:#061326 !important;
      background:linear-gradient(135deg, var(--c360-cyan), var(--c360-green));
      box-shadow:0 16px 38px rgba(0,212,255,.24);
    }

    .btn-hero-soft{
      color:#fff !important;
      background:rgba(255,255,255,.10);
      backdrop-filter:blur(14px);
    }

    .btn-hero:hover{
      transform:translateY(-2px);
      filter:brightness(1.04);
    }

    .quick-guide{
      position:relative;
      z-index:2;
      display:grid;
      grid-template-columns:repeat(3, minmax(0, 1fr));
      gap:12px;
      padding:0 28px 28px;
    }

    .qg{
      position:relative;
      overflow:hidden;
      border-radius:22px;
      padding:15px;
      color:#fff;
      background:rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.16);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.12);
      backdrop-filter:blur(14px);
    }

    .qg::before{
      content:"";
      position:absolute;
      inset:0 0 auto 0;
      height:3px;
      background:linear-gradient(90deg, var(--c360-blue), var(--c360-cyan), var(--c360-green));
    }

    .qg .t{
      margin:0 0 5px;
      font-weight:950;
      font-size:13px;
      color:#fff;
    }

    .qg .d{
      margin:0;
      font-size:12px;
      color:rgba(234,246,255,.78);
      font-weight:650;
      line-height:1.45;
    }

    .main-card{
      position:relative;
      overflow:hidden;
      border-radius:34px;
      background:rgba(255,255,255,.96);
      border:1px solid rgba(148,210,255,.24);
      box-shadow:var(--c360-shadow);
    }

    .main-card::before{
      content:"";
      position:absolute;
      inset:0 0 auto 0;
      height:5px;
      background:linear-gradient(90deg, var(--c360-blue), var(--c360-cyan), var(--c360-green));
      z-index:2;
    }

    .block{
      position:relative;
      overflow:hidden;
      border-radius:28px;
      background:
        radial-gradient(420px 150px at 0% 0%, rgba(35,120,255,.10), transparent 62%),
        linear-gradient(180deg, #ffffff, #f8fbff);
      border:1px solid rgba(15,23,42,.10);
      box-shadow:var(--c360-shadow-card);
    }

    .block::before{
      content:"";
      position:absolute;
      inset:0 0 auto 0;
      height:4px;
      background:linear-gradient(90deg, var(--c360-blue), var(--c360-cyan), var(--c360-green));
      opacity:.92;
      z-index:1;
    }

    .block-head{
      position:relative;
      z-index:2;
      padding:18px 18px 13px;
      border-bottom:1px solid rgba(15,23,42,.08);
      background:
        radial-gradient(320px 110px at 15% 0%, rgba(0,212,255,.13), transparent 64%),
        linear-gradient(180deg, #ffffff, #f8fbff);
    }

    .block-title{
      margin:0;
      color:#061326;
      font-weight:950;
      font-size:18px;
      letter-spacing:-.45px;
    }

    .block-sub{
      margin-top:5px;
      color:#64748b;
      font-size:13px;
      font-weight:700;
      line-height:1.45;
    }

    .block-body{
      position:relative;
      z-index:2;
      padding:18px;
    }

    #mapaContainer{
      width:100%;
      min-height:620px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:24px;
      overflow:hidden;
      background:
        radial-gradient(520px 220px at 50% 15%, rgba(0,212,255,.11), transparent 62%),
        linear-gradient(180deg, #f8fbff, #eef7ff);
      border:1px solid rgba(35,120,255,.12);
    }

    #mapaContainer svg,
    #mapaContainer img{
      max-width:100%;
      height:auto;
    }

    #fingerClick{
      position:absolute;
      top:18px;
      right:18px;
      width:54px;
      height:auto;
      z-index:5;
      filter:drop-shadow(0 12px 18px rgba(0,0,0,.25));
      animation:fingerPulse 1.8s ease-in-out infinite;
      pointer-events:none;
    }

    @keyframes fingerPulse{
      0%,100%{
        transform:translateY(0) scale(1);
        opacity:.92;
      }
      50%{
        transform:translateY(-6px) scale(1.04);
        opacity:1;
      }
    }

    .mode-card{
      position:relative;
      z-index:10;
      overflow:hidden;
      border-radius:24px;
      padding:14px;
      background:
        radial-gradient(300px 110px at 0% 0%, rgba(0,212,255,.13), transparent 64%),
        linear-gradient(180deg, #ffffff, #f8fbff);
      border:1px solid rgba(15,23,42,.10);
      box-shadow:0 14px 32px rgba(3,18,38,.10);
    }

    .mode-buttons{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:10px;
    }

    .mode-btn{
      cursor:pointer;
      border-radius:16px;
      border:1px solid rgba(35,120,255,.18);
      font-weight:950;
      padding:12px 14px;
      font-size:14px;
      transition:all .18s ease;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
    }

    .mode-btn.is-active{
      color:#061326;
      background:linear-gradient(135deg, var(--c360-cyan), var(--c360-green));
      box-shadow:0 14px 30px rgba(0,212,255,.22);
      border-color:rgba(255,255,255,.30);
    }

    .mode-btn.is-soft{
      color:#075985;
      background:rgba(35,120,255,.07);
    }

    .mode-btn:hover{
      transform:translateY(-1px);
      filter:brightness(1.03);
    }

    .info-question{
      display:none;
      padding:12px 14px;
      margin-bottom:12px;
      font-size:.82rem;
      color:#0f172a;
      border-radius:16px;
      background:rgba(0,212,255,.08);
      border:1px solid rgba(0,212,255,.16);
      border-left:4px solid var(--c360-cyan);
    }

    .form-label{
      color:#061326 !important;
      font-weight:900 !important;
      font-size:13px;
    }

    .form-select{
      min-height:46px;
      border-radius:16px;
      border:1px solid rgba(15,23,42,.14);
      font-weight:700;
      color:#0f172a;
      background-color:#fff;
    }

    .form-select:focus{
      border-color:rgba(0,212,255,.50);
      box-shadow:0 0 0 .22rem rgba(0,212,255,.14);
    }

    .chart-wrap{
      position:relative;
      width:100%;
      border-radius:22px;
      padding:10px;
      background:
        radial-gradient(320px 120px at 0% 0%, rgba(35,120,255,.08), transparent 62%),
        linear-gradient(180deg, #ffffff, #f7fbff);
      border:1px solid rgba(15,23,42,.08);
    }

    .hint{
      display:flex;
      align-items:flex-start;
      gap:12px;
      padding:13px 14px;
      border-radius:18px;
      background:rgba(0,212,255,.08);
      border:1px solid rgba(0,212,255,.16);
    }

    .hint .h-title{
      margin:0 0 3px;
      color:#061326;
      font-weight:950;
      font-size:13px;
    }

    .hint .h-desc{
      margin:0;
      color:#64748b;
      font-weight:700;
      font-size:12px;
      line-height:1.45;
    }

    #resultadosCard{
      width:min(430px, calc(100vw - 22px));
      right:18px;
      top:calc(var(--altura-menu, 0px) + 20px);
      border-radius:28px !important;
      overflow:hidden;
      background:#fff;
      box-shadow:0 28px 90px rgba(0,0,0,.34);
      border:1px solid rgba(148,210,255,.28) !important;
    }

    #resultadosCard .card-header{
      border:0;
      color:#fff;
      background:
        radial-gradient(360px 120px at 0% 0%, rgba(0,212,255,.28), transparent 64%),
        linear-gradient(135deg, #061326, #0b2342, #0879b8);
    }

    #resultadosCard .card-header .fw-bold{
      color:#fff !important;
      font-weight:950 !important;
    }

    #resultadosCard .btn-close{
      filter:invert(1) grayscale(100%);
      opacity:.9;
    }

    #resultadosCard .badge{
      border-radius:999px;
      padding:8px 11px;
      font-weight:900;
    }

    #resultadosCard .card-body{
      background:#fff;
    }

    .spinner-border{
      color:var(--c360-cyan) !important;
    }

    .bg-primary-soft{
      color:#061326 !important;
      background:linear-gradient(135deg, var(--c360-cyan), var(--c360-green)) !important;
      border:1px solid rgba(255,255,255,.22);
      border-radius:999px;
      padding:9px 13px;
      font-weight:950;
      box-shadow:0 12px 28px rgba(0,212,255,.20);
    }

    .text-primary{
      color:#075985 !important;
    }

    .text-muted{
      color:#64748b !important;
    }

    @media (max-width:991.98px){
      .app-shell{
        padding-top:calc(var(--altura-menu, 0px) + 16px);
      }

      .hero-content{
        grid-template-columns:auto 1fr;
        padding:22px;
      }

      .hero-actions{
        grid-column:1 / -1;
        flex-direction:row;
        flex-wrap:wrap;
      }

      .quick-guide{
        grid-template-columns:1fr;
        padding:0 22px 22px;
      }

      #mapaContainer{
        min-height:500px;
      }

      #resultadosCard{
        top:calc(var(--altura-menu, 0px) + 12px);
        right:11px;
      }
    }

    @media (max-width:767.98px){
      .app-container{
        width:min(100% - 18px, 1320px);
      }

      .hero{
        border-radius:26px;
      }

      .hero-content{
        grid-template-columns:1fr;
        padding:18px;
      }

      .hero-logo{
        width:62px;
        height:62px;
        border-radius:20px;
      }

      .hero-logo img{
        width:54px;
        max-height:54px;
      }

      .hero-title{
        font-size:27px;
      }

      .hero-subtitle{
        font-size:14px;
      }

      .hero-actions{
        display:grid;
        grid-template-columns:1fr;
        width:100%;
      }

      .btn-hero{
        width:100%;
      }

      .quick-guide{
        padding:0 18px 18px;
      }

      .main-card,
      .block{
        border-radius:24px;
      }

      .block-head{
        padding:16px 15px 12px;
      }

      .block-body{
        padding:14px;
      }

      #mapaContainer{
        min-height:390px;
        border-radius:20px;
      }

      #fingerClick{
        width:44px;
        top:14px;
        right:14px;
      }

      .mode-buttons{
        grid-template-columns:1fr;
      }

      .chart-wrap{
        height:300px !important;
        min-height:280px !important;
      }

      #resultadosCard{
        left:9px;
        right:9px;
        width:auto;
        max-height:calc(100vh - var(--altura-menu, 0px) - 24px);
        overflow:auto;
      }
    }
  </style>
</head>

<body>

<!-- SVG con patrones para compatibilidad cross-browser Firefox -->
<svg style="position:absolute;width:0;height:0;overflow:hidden;" aria-hidden="true">
  <defs>
    <pattern id="rayasAzules" width="8" height="8" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
      <rect width="8" height="8" fill="#cfe2ff"/>
      <rect width="4" height="8" fill="#0d6efd"/>
    </pattern>
  </defs>
</svg>

<?php
require_once __DIR__ . '/admin/include/loading.php';
require_once __DIR__ . '/admin/include/menusecond.php';
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const menu = document.querySelector(
    '.menu-fixed, .navbar.fixed-top, header.fixed-top, #mainNavbar.fixed-top, #navbarDefault.fixed-top, .sticky-top'
  );

  let altura = 0;

  if (menu) {
    const st = window.getComputedStyle(menu);
    const isFixedOrSticky = (st.position === 'fixed' || st.position === 'sticky');

    if (isFixedOrSticky) {
      altura = menu.offsetHeight || 0;
    }
  }

  document.documentElement.style.setProperty("--altura-menu", altura + "px");

  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const form = document.querySelector('#loginForm, form[action*="login"], form[data-login="1"]');

  if (form && token && !form.querySelector('input[name="csrf_token"]')) {
    const inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'csrf_token';
    inp.value = token;
    form.appendChild(inp);
  }

  const btn = document.getElementById('btnIrResultados');
  const target = document.getElementById('panelResultados');

  if (btn && target) {
    btn.addEventListener('click', function(){
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }
});

window.addEventListener("resize", function () {
  const menu = document.querySelector(
    '.menu-fixed, .navbar.fixed-top, header.fixed-top, #mainNavbar.fixed-top, #navbarDefault.fixed-top, .sticky-top'
  );

  if (!menu) return;

  const st = window.getComputedStyle(menu);
  const altura = (st.position === 'fixed' || st.position === 'sticky') ? (menu.offsetHeight || 0) : 0;

  document.documentElement.style.setProperty("--altura-menu", altura + "px");
});
</script>

<div class="app-shell">

  <!-- HERO -->
  <div class="app-container hero-wrap">
    <div class="hero" role="region" aria-label="Panel principal de resultados">

      <div class="hero-content">
        <div class="hero-logo">
          <img src="<?= htmlspecialchars($logo360) ?>" alt="360 Estadísticas">
        </div>

        <div>
          <span class="hero-kicker">
            <i class="fa-solid fa-chart-line"></i>
            Estadística en tiempo real
          </span>

          <h1 class="hero-title">
            Resultados territoriales de las encuestas
          </h1>

          <p class="hero-subtitle">
            Explora el mapa, selecciona un departamento y consulta en segundos el
            <strong>resumen nacional</strong>, el <strong>detalle territorial</strong> y las tendencias consolidadas.
          </p>
        </div>

        <div class="hero-actions">
          <a href="#panelResultados" class="btn-hero btn-hero-primary" id="btnIrResultados">
            <i class="fa-solid fa-map-location-dot"></i>
            Ir al mapa
          </a>

          <a href="dash_responder.php" class="btn-hero btn-hero-soft">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
          </a>
        </div>
      </div>

      <div class="quick-guide">
        <div class="qg">
          <p class="t">1) Selecciona un departamento</p>
          <p class="d">Haz clic en el mapa para abrir el detalle territorial.</p>
        </div>

        <div class="qg">
          <p class="t">2) Revisa el comparativo</p>
          <p class="d">Compara la lectura nacional con el resultado seleccionado.</p>
        </div>

        <div class="qg">
          <p class="t">3) Consulta el detalle</p>
          <p class="d">Se abrirá una tarjeta con la información específica.</p>
        </div>
      </div>

    </div>
  </div>

  <!-- PANEL PRINCIPAL -->
  <div class="app-container mb-5" id="panelResultados">
    <div class="main-card">
      <div class="p-3 p-lg-4">
        <div class="row g-3 g-lg-4 align-items-stretch">

          <!-- MAPA -->
          <div class="col-lg-7 col-md-7">
            <div class="block h-100" style="position:relative;">
              <div class="block-head">
                <h3 class="block-title text-center">
                  Mapa territorial de Colombia
                </h3>
                <p class="block-sub text-center mb-0">
                  Haz clic sobre un departamento para consultar el resultado consolidado.
                </p>
              </div>

              <div class="block-body" style="position:relative; z-index:2;">
                <img id="fingerClick" src="assets/img/admin/finger.png" alt="Indicador de clic">

                <div id="mapaContainer">
                  <?php require_once __DIR__ . '/admin/mapa_colombia/mapa_index.php'; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- GRAFICAS -->
          <div class="col-lg-5 col-md-5">
            <div class="d-grid gap-3 gap-lg-4">

              <?php if ($opcionActivaWeb === 'ambos'): ?>
                <div class="mode-card">
                  <div class="mode-buttons">
                    <button type="button" id="btnModoSondeo" class="mode-btn is-active">
                      <i class="fas fa-poll"></i>
                      Sondeo
                    </button>

                    <button type="button" id="btnModoCuestionario" class="mode-btn is-soft">
                      <i class="fas fa-clipboard-list"></i>
                      Cuestionario
                    </button>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($opcionActivaWeb === 'cuestionario' || $opcionActivaWeb === 'ambos'): ?>
                <div class="block" id="panelSelectorPregunta" <?= $opcionActivaWeb === 'ambos' ? 'style="display:none;"' : '' ?>>
                  <div class="block-head">
                    <h3 class="block-title text-center">Cuestionario activo</h3>
                    <p class="block-sub text-center mb-0" id="fichaTecnicaNombre">
                      Cargando ficha técnica...
                    </p>
                  </div>

                  <div class="block-body">
                    <div id="infoPreguntaCtx" class="info-question">
                      <div id="infoPreguntaCapitulo" style="font-weight:900;margin-bottom:4px;"></div>
                      <div id="infoPreguntaNumeral" style="font-weight:800;color:#075985;margin-bottom:2px;font-size:.79rem;"></div>
                      <div id="infoPreguntaEnunciado" style="color:#334155;font-style:italic;margin-bottom:2px;"></div>
                      <div id="infoPreguntaTextoAdicional" style="color:#64748b;font-size:.78rem;margin-top:2px;"></div>
                    </div>

                    <label for="selectorPregunta" class="form-label">
                      Selecciona una pregunta:
                    </label>

                    <select id="selectorPregunta" class="form-select">
                      <option value="">Cargando preguntas...</option>
                    </select>
                  </div>
                </div>
              <?php endif; ?>

              <div class="block">
                <div class="block-head">
                  <h3 class="block-title text-center">Resumen nacional</h3>
                  <p class="block-sub text-center mb-0">
                    Vista general para entender la tendencia actual.
                  </p>
                </div>

                <div class="block-body" style="overflow-y:auto; max-height:440px; padding:12px;">
                  <div class="chart-wrap" id="chartWrapGeneral" style="height:auto; min-height:320px;">
                    <canvas id="graficoGeneral" aria-label="Gráfico resumen nacional"></canvas>
                  </div>
                </div>
              </div>

              <div class="block">
                <div class="block-head">
                  <h3 class="block-title text-center">Detalle por departamento</h3>
                  <p class="block-sub text-center mb-0">
                    Selecciona un departamento en el mapa para actualizar esta gráfica.
                  </p>
                </div>

                <div class="block-body">
                  <div class="chart-wrap" style="height:340px;">
                    <canvas id="graficoVotos" aria-label="Gráfico por departamento"></canvas>
                  </div>

                  <div class="mt-3 hint">
                    <i class="bi bi-cursor-fill" style="font-size:1.15rem; color:#0891b2;"></i>
                    <div>
                      <p class="h-title">Aún no seleccionas un departamento</p>
                      <p class="h-desc">
                        Elige uno en el mapa y verás los resultados aquí de inmediato.
                      </p>
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

<!-- CARD DETALLE FLOTANTE -->
<div id="resultadosCard" class="card position-fixed d-none" style="z-index:9999;" aria-live="polite">
  <div class="card-header py-3">
    <div class="d-flex justify-content-between align-items-center">
      <div class="fw-bold">
        <i class="fa-solid fa-location-dot me-2"></i>
        Detalle del departamento
      </div>

      <button type="button" class="btn-close" id="closeCard" aria-label="Cerrar detalle"></button>
    </div>

    <div class="mt-3 d-flex align-items-center justify-content-between gap-2 flex-wrap">
      <span class="badge bg-light text-dark border" id="badgeElectoral">
        Resultados del sondeo
      </span>

      <span class="text-white-50 fw-bold" style="font-size:.82rem;">
        Registros disponibles
      </span>
    </div>

    <div class="mt-2 text-center">
      <span class="text-white-50 fw-bold" style="font-size:.84rem;">
        Pronóstico elecciones 2026 • Vista por territorio
      </span>
    </div>
  </div>

  <div class="card-body p-0">
    <div id="resultadosContent">
      <div class="text-center p-4">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Cargando...</span>
        </div>

        <p class="mt-2 mb-0 text-muted fw-bold">
          Cargando resultados del departamento…
        </p>

        <small class="text-muted fw-bold d-block mt-1">
          Esto puede tardar unos segundos.
        </small>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/admin/include/footer.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/lightbox/js/lightbox.min.js"></script>

<script src="admin/js/lib/util.js"></script>
<script src="js/main.js"></script>
<script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>

<script src="js/login.js"></script>

<script>
  window.OPCION_ACTIVA_WEB = "<?= addslashes($opcionActivaWeb); ?>";
</script>

<script src="admin/js/index.js"></script>

<?php
/**
 * ⚠️ Recomendación: NO incluir cron en página pública.
 * Se deja activo solo para admin.
 */
$cronFile = __DIR__ . '/cron_exportar_fotos.php';

if (is_file($cronFile)) {
  $isAdmin = !empty($_SESSION['session_user']['id'])
    && !empty($_SESSION['session_user']['rol'])
    && ($_SESSION['session_user']['rol'] === 'admin');

  if ($isAdmin) {
    require_once $cronFile;
  }
}
?>

<script>
document.addEventListener("DOMContentLoaded", function(){
  const card = document.getElementById('resultadosCard');
  const close = document.getElementById('closeCard');

  if (close && card) {
    close.addEventListener('click', function(){
      card.classList.add('d-none');
    });
  }

  const btnSondeo = document.getElementById('btnModoSondeo');
  const btnCuest  = document.getElementById('btnModoCuestionario');
  const panelSelectorPregunta = document.getElementById('panelSelectorPregunta');

  function pintarBotonModo(modo){
    if (!btnSondeo || !btnCuest) return;

    if (modo === 'sondeo') {
      btnSondeo.classList.add('is-active');
      btnSondeo.classList.remove('is-soft');

      btnCuest.classList.remove('is-active');
      btnCuest.classList.add('is-soft');

      if (panelSelectorPregunta) {
        panelSelectorPregunta.style.display = 'none';
      }
    } else {
      btnCuest.classList.add('is-active');
      btnCuest.classList.remove('is-soft');

      btnSondeo.classList.remove('is-active');
      btnSondeo.classList.add('is-soft');

      if (panelSelectorPregunta) {
        panelSelectorPregunta.style.display = '';
      }
    }
  }

  if (btnSondeo && btnCuest) {
    btnSondeo.addEventListener('click', function() {
      pintarBotonModo('sondeo');
      window._aplicarModo && window._aplicarModo('sondeo');
    });

    btnCuest.addEventListener('click', function() {
      pintarBotonModo('cuestionario');
      window._aplicarModo && window._aplicarModo('cuestionario');
    });
  }
});
</script>

</body>
</html>