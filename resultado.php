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

 <link rel="stylesheet" href="./css/resultado.css?v=<?= time(); ?>">
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
require_once __DIR__ . '/admin/include/perfil.php';
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
<script src="admin/js/perfil.js"></script>

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
