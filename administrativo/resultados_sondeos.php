<?php
include './admin/include/head.php';
require_once 'admin/include/generic_classes.php';
include './admin/classes/Sondeo.php';
include './admin/classes/RespuestaSondeo.php';

// Validar permisos (Resultados Sondeos - Ver)
$view = SessionData::getPermission(90);
if (!$view) {
    require_once 'permiso_denegado.php';
    exit;
}

// Obtener sondeos disponibles
$sondeos = RespuestaSondeo::getSondeosDisponibles([]);
$sondeos = $sondeos['output']['response'] ?? [];
$modulo = 'Resultados de Sondeos';
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  :root{
    --nav-blue:#20427F;
    --nav-blue-2:#132b52;
    --nav-blue-3:#2e58a8;

    /* ✅ White UI */
    --page:#f5f7fb;
    --page2:#ffffff;

    --card:#ffffff;
    --card-soft:#fbfcff;

    --stroke: rgba(15, 23, 42, .08);
    --stroke2: rgba(15, 23, 42, .12);

    --text:#0f172a;
    --muted:#64748b;

    --radius-xl: 22px;
    --radius-lg: 16px;
    --radius-md: 14px;

    --shadow-1: 0 18px 45px rgba(2, 6, 23, .08);
    --shadow-2: 0 10px 24px rgba(2, 6, 23, .08);

    --ring: 0 0 0 .22rem rgba(46,88,168,.18);
  }

  /* ✅ Fondo BLANCO estilo SaaS limpio (como tu screenshot) */
  body{
    background:
      radial-gradient(900px 380px at 12% 0%, rgba(46,88,168,.10), transparent 55%),
      radial-gradient(800px 380px at 90% 10%, rgba(32,66,127,.08), transparent 55%),
      linear-gradient(180deg, var(--page) 0%, var(--page2) 55%, var(--page) 100%);
    color: var(--text);
  }

  /* content width on desktop */
  .content .container-fluid{
    max-width: 1380px;
    margin: 0 auto;
    padding-left: 12px !important;
    padding-right: 12px !important;
  }

  /* ====== HERO HEADER (blanco pro) ====== */
  .hero-wrap{
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-1);
    border: 1px solid var(--stroke);
    background:
      radial-gradient(900px 320px at 18% 0%, rgba(46,88,168,.18), transparent 60%),
      radial-gradient(900px 320px at 92% 20%, rgba(32,66,127,.14), transparent 60%),
      linear-gradient(135deg, #ffffff, #fbfcff);
  }
  .hero-inner{ padding: 22px 22px; }

  .hero-kicker{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-weight: 800;
    letter-spacing: .2px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--stroke);
    background: rgba(255,255,255,.75);
    box-shadow: 0 10px 22px rgba(2,6,23,.06);
    color: #0f172a;
  }

  .hero-title{
    font-weight: 900;
    letter-spacing: -0.6px;
    margin: 12px 0 6px 0;
    font-size: clamp(1.1rem, 2.2vw, 1.65rem);
  }

  .hero-sub{
    color: var(--muted);
    margin: 0;
    max-width: 70ch;
  }

  /* decorative orbs (suaves, no oscuras) */
  .orb{
    position:absolute;
    width: 320px; height: 320px;
    border-radius: 50%;
    filter: blur(45px);
    opacity: .28;
    pointer-events:none;
    animation: floaty 8s ease-in-out infinite;
  }
  .orb.o1{ left:-80px; top:-90px; background: rgba(46,88,168,.65); }
  .orb.o2{ right:-120px; top:-110px; background: rgba(32,66,127,.55); animation-delay: -2.2s; }
  .orb.o3{ right: 20%; bottom:-170px; background: rgba(19,43,82,.45); animation-delay: -4s; }
  @keyframes floaty{
    0%,100%{ transform: translateY(0px) translateX(0px) scale(1); }
    50%{ transform: translateY(10px) translateX(8px) scale(1.03); }
  }

  /* ====== CARD (blancas limpias) ====== */
  .card-glass{
    border-radius: var(--radius-xl);
    border: 1px solid var(--stroke);
    background: var(--card);
    box-shadow: var(--shadow-2);
    overflow: hidden;
  }
  .card-glass .card-header{
    background: linear-gradient(180deg, #ffffff, #fbfcff) !important;
    border-bottom: 1px solid var(--stroke) !important;
  }

  .section-title{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    margin-bottom: 6px;
  }
  .section-title h5, .section-title h6{
    margin:0;
    font-weight: 900;
    letter-spacing: -.2px;
    color: var(--text);
  }

  .mini-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    border: 1px solid var(--stroke);
    background: rgba(32,66,127,.06);
    padding: 6px 10px;
    border-radius: 999px;
    color: rgba(15,23,42,.78);
    font-size: .84rem;
    white-space: nowrap;
  }

  /* ====== SELECTOR BAR (como screenshot: blanco, sutil) ====== */
  .selector-bar{
    border-radius: var(--radius-xl);
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, #ffffff, #fbfcff);
    box-shadow: var(--shadow-2);
    padding: 16px;
  }

  .form-floating>.form-select{
    border-radius: 14px;
    border: 1px solid var(--stroke2);
    background: #ffffff;
    color: var(--text);
    box-shadow: inset 0 0 0 1px rgba(15,23,42,.03);
  }
  .form-floating>.form-select:focus{
    outline: none;
    box-shadow: var(--ring), inset 0 0 0 1px rgba(15,23,42,.04);
    border-color: rgba(46,88,168,.45);
  }
  .form-floating>label{
    color: rgba(15,23,42,.70);
  }

  /* ====== BOTONES PRO (azules, limpios) ====== */
  .btn-pro{
    border-radius: 14px;
    padding: 12px 14px;
    font-weight: 900;
    letter-spacing: .2px;
    border: 1px solid rgba(32,66,127,.20);
    position: relative;
    overflow: hidden;
    transform: translateZ(0);
    box-shadow: 0 12px 22px rgba(2,6,23,.10);
    min-height: 56px;
    color: #fff;
    background: linear-gradient(135deg, var(--nav-blue-3), var(--nav-blue));
  }
  .btn-pro:hover{ filter: brightness(1.03); transform: translateY(-1px); }
  .btn-pro:active{ transform: translateY(0px); }
  .btn-pro:focus{ box-shadow: var(--ring), 0 14px 28px rgba(2,6,23,.12); }

  .btn-ghost{
    border-radius: 14px;
    padding: 12px 14px;
    font-weight: 900;
    border: 1px solid var(--stroke2);
    background: #ffffff;
    color: var(--text);
    min-height: 56px;
    box-shadow: 0 12px 22px rgba(2,6,23,.06);
  }
  .btn-ghost:hover{ background: #f8fafc; }

  /* ====== STATS (blancas, icono azul) ====== */
  .stat-card{
    position: relative;
    border-radius: var(--radius-xl);
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, #ffffff, #fbfcff);
    box-shadow: var(--shadow-2);
    padding: 16px;
    overflow: hidden;
    height: 100%;
  }
  .stat-card::before{
    content:"";
    position:absolute;
    inset:-2px;
    background:
      radial-gradient(520px 190px at 20% 10%, rgba(46,88,168,.14), transparent 60%),
      radial-gradient(520px 210px at 85% 0%, rgba(32,66,127,.10), transparent 55%);
    opacity: .9;
    z-index:0;
  }
  .stat-card > *{ position: relative; z-index:1; }

  .stat-k{
    font-size: .86rem;
    color: rgba(15,23,42,.65);
    margin-bottom: 6px;
    font-weight: 800;
  }
  .stat-v{
    font-size: clamp(1.35rem, 2.8vw, 2.1rem);
    font-weight: 950;
    letter-spacing: -0.8px;
    margin: 0;
    line-height: 1.05;
    color: var(--text);
  }
  .stat-ic{
    width: 46px; height: 46px;
    border-radius: 14px;
    display:flex;
    align-items:center;
    justify-content:center;
    border: 1px solid var(--stroke);
    background: rgba(32,66,127,.06);
    box-shadow: 0 10px 18px rgba(2,6,23,.08);
    font-size: 1.15rem;
    color: var(--nav-blue);
  }

  /* ====== CHART WRAP (blanco suave) ====== */
  .chart-wrap{
    border-radius: var(--radius-xl);
    border: 1px solid var(--stroke);
    background: var(--card-soft);
    box-shadow: inset 0 0 0 1px rgba(15,23,42,.02);
    padding: 14px;
  }

  /* ====== EMPTY STATE (blanco) ====== */
  .empty-state{
    border-radius: var(--radius-xl);
    border: 1px dashed rgba(15,23,42,.16);
    background: #ffffff;
    box-shadow: var(--shadow-2);
    overflow:hidden;
    position:relative;
  }
  .empty-state::before{
    content:"";
    position:absolute;
    inset:-2px;
    background:
      radial-gradient(900px 280px at 50% 0%, rgba(46,88,168,.14), transparent 65%),
      radial-gradient(700px 260px at 0% 100%, rgba(32,66,127,.10), transparent 60%);
    opacity: .9;
    pointer-events:none;
  }
  .empty-state .card-body{ position: relative; z-index: 1; }

  .empty-ic{
    width: 92px; height: 92px;
    border-radius: 28px;
    margin: 0 auto 14px auto;
    display:flex;
    align-items:center;
    justify-content:center;
    border: 1px solid var(--stroke);
    background: rgba(32,66,127,.06);
    box-shadow: 0 18px 30px rgba(2,6,23,.10);
    font-size: 2.4rem;
    color: var(--nav-blue);
  }

  /* ====== RESPONSIVE ====== */
  .g-3 { --bs-gutter-x: 1rem; --bs-gutter-y: 1rem; }
  @media (max-width: 991.98px){
    .hero-inner{ padding: 18px; }
    .selector-bar{ padding: 14px; }
    .btn-pro, .btn-ghost{ min-height: 52px; }
  }
  @media (max-width: 575.98px){
    .content .container-fluid{ padding-left: 10px !important; padding-right: 10px !important; }
    .hero-kicker{ font-size: .82rem; }
    .mini-pill{ font-size: .80rem; }
    .stat-ic{ width: 44px; height: 44px; }
  }

  /* ====== Smooth reveal ====== */
  .reveal{ animation: reveal .35s ease both; }
  @keyframes reveal{
    from{ opacity: 0; transform: translateY(8px); }
    to{ opacity: 1; transform: translateY(0); }
  }
</style>

  


</head>

<body>
  <main class="main" id="top">
    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">
      <div class="container-fluid px-0">

        <!-- HERO HEADER -->
        <div class="hero-wrap mb-4">
          <div class="orb o1"></div>
          <div class="orb o2"></div>
          <div class="orb o3"></div>

          <div class="hero-inner">
            <div class="row g-3 align-items-center">
              <div class="col-12 col-lg-8">
                <div class="hero-kicker">
                  <i class="fas fa-sparkles"></i>
                  Panel Analítico • Estadística 360
                </div>
                <div class="hero-title d-flex align-items-center gap-2 mt-2">
                  <i class="fas fa-chart-bar" style="opacity:.92"></i>
                  <?= $modulo ?>
                </div>
                <p class="hero-sub">
                  Selecciona un sondeo y visualiza resultados en tiempo real: totales, tendencia y análisis demográfico.
                </p>
              </div>

              <div class="col-12 col-lg-4">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                  <button type="button" class="btn btn-ghost" onclick="document.getElementById('sondeo_selector')?.focus()">
                    <i class="fas fa-search me-2"></i>Buscar sondeo
                  </button>
                  <button type="button" class="btn btn-pro" onclick="RESULTADOS_SONDEO.cargarEstadisticas()">
                    <i class="fas fa-bolt me-2"></i>Actualizar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SELECTOR -->
        <div class="selector-bar mb-4 reveal">
          <div class="row g-3 align-items-stretch">
            <div class="col-12 col-lg-9">
              <div class="form-floating">
                <select class="form-select" id="sondeo_selector" onchange="RESULTADOS_SONDEO.cargarEstadisticas()">
                  <option value="">Selecciona un sondeo...</option>
                  <?php foreach ($sondeos as $sondeo): ?>
                    <option value="<?= $sondeo['id'] ?>">
                      <?= htmlspecialchars($sondeo['sondeo']) ?>
                      (<?= $sondeo['total_respuestas'] ?> respuestas)
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="sondeo_selector">
                  <i class="fas fa-poll me-2"></i>Selecciona un Sondeo
                </label>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <button class="btn btn-pro w-100" onclick="RESULTADOS_SONDEO.cargarEstadisticas()">
                <i class="fas fa-sync-alt me-2"></i>Cargar Estadísticas
              </button>
            </div>
          </div>
        </div>

        <!-- CONTENEDOR DE ESTADÍSTICAS -->
        <div id="estadisticas-container" style="display:none;" class="reveal">

          <!-- STATS -->
          <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
              <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                  <div>
                    <div class="stat-k">Total Respuestas</div>
                    <p class="stat-v" id="stat-total-respuestas">0</p>
                  </div>
                  <div class="stat-ic"><i class="fas fa-clipboard-list"></i></div>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                  <div>
                    <div class="stat-k">Votantes Únicos</div>
                    <p class="stat-v" id="stat-votantes-unicos">0</p>
                  </div>
                  <div class="stat-ic"><i class="fas fa-users"></i></div>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                  <div>
                    <div class="stat-k">Días Activo</div>
                    <p class="stat-v" id="stat-dias-activo">0</p>
                  </div>
                  <div class="stat-ic"><i class="fas fa-calendar-alt"></i></div>
                </div>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                  <div>
                    <div class="stat-k">Promedio Diario</div>
                    <p class="stat-v" id="stat-promedio-diario">0</p>
                  </div>
                  <div class="stat-ic"><i class="fas fa-chart-line"></i></div>
                </div>
              </div>
            </div>
          </div>

          <!-- RESULTADOS GENERALES -->
          <div class="card card-glass mb-4 reveal">
            <div class="card-header p-3">
              <div class="section-title">
                <h5><i class="fas fa-chart-pie me-2"></i>Resultados Generales</h5>
                <span class="mini-pill">
                  <i class="fas fa-circle-info"></i>
                  Porcentaje + Totales
                </span>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-wrap">
                <canvas id="chart-general" height="80"></canvas>
              </div>
            </div>
          </div>

          <!-- ANALÍTICA DEMOGRÁFICA -->
          <div class="row g-3">
            <!-- Ideología -->
            <div class="col-12 col-lg-6">
              <div class="card card-glass h-100 reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-landmark me-2"></i>Ideología Política</h6>
                    <span class="mini-pill"><i class="fas fa-filter"></i>Segmentación</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-ideologia" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Género -->
            <div class="col-12 col-lg-6">
              <div class="card card-glass h-100 reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-venus-mars me-2"></i>Género</h6>
                    <span class="mini-pill"><i class="fas fa-people-group"></i>Distribución</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-genero" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edad -->
            <div class="col-12 col-lg-6">
              <div class="card card-glass h-100 reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-birthday-cake me-2"></i>Rango de Edad</h6>
                    <span class="mini-pill"><i class="fas fa-chart-column"></i>Comparativo</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-edad" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ingresos -->
            <div class="col-12 col-lg-6">
              <div class="card card-glass h-100 reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-dollar-sign me-2"></i>Nivel de Ingresos</h6>
                    <span class="mini-pill"><i class="fas fa-sack-dollar"></i>Rangos</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-ingresos" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Educación -->
            <div class="col-12">
              <div class="card card-glass reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-graduation-cap me-2"></i>Nivel de Educación</h6>
                    <span class="mini-pill"><i class="fas fa-ranking-star"></i>Top categorías</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-educacion" height="150"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Departamento -->
            <div class="col-12 col-lg-6">
              <div class="card card-glass h-100 reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-map-marked-alt me-2"></i>Distribución por Departamento</h6>
                    <span class="mini-pill"><i class="fas fa-location-dot"></i>Mapa</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-departamento" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>

            <!-- Municipio -->
            <div class="col-12 col-lg-6">
              <div class="card card-glass h-100 reveal">
                <div class="card-header p-3">
                  <div class="section-title">
                    <h6><i class="fas fa-map-marker-alt me-2"></i>Distribución por Municipio (Top 10)</h6>
                    <span class="mini-pill"><i class="fas fa-trophy"></i>Top 10</span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart-wrap">
                    <canvas id="chart-municipio" height="200"></canvas>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /row -->
        </div><!-- /estadisticas -->

        <!-- EMPTY STATE -->
        <div id="empty-state" class="card empty-state shadow-none border-0 reveal">
          <div class="card-body text-center py-5">
            <div class="empty-ic"><i class="fas fa-chart-bar"></i></div>
            <h4 class="mt-2" style="font-weight:900; letter-spacing:-.3px; color: rgba(255,255,255,.92);">
              Selecciona un sondeo
            </h4>
            <p class="mb-0" style="color: var(--muted);">
              Para ver estadísticas, resultados y análisis demográfico en una sola vista.
            </p>

            <div class="d-flex justify-content-center gap-2 flex-wrap mt-4">
              <button class="btn btn-ghost" onclick="document.getElementById('sondeo_selector')?.focus()">
                <i class="fas fa-hand-pointer me-2"></i>Elegir sondeo
              </button>
              <button class="btn btn-pro" onclick="RESULTADOS_SONDEO.cargarEstadisticas()">
                <i class="fas fa-bolt me-2"></i>Ver estadísticas
              </button>
            </div>
          </div>
        </div>

      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </main>

  <!-- Scripts -->
  <?php include 'admin/include/gerenic_script.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

  <!-- Script personalizado -->
  <script type="text/javascript" src="admin/js/resultados_sondeos.js"></script>

  <?php include 'admin/include/scriptsgober360.php'; ?>

  <script>
    // init normal
    RESULTADOS_SONDEO.init();

    // UX: enter to reload when selector focused
    document.addEventListener('keydown', function(e){
      if(e.key === 'Enter'){
        const sel = document.getElementById('sondeo_selector');
        if(sel && document.activeElement === sel){
          e.preventDefault();
          RESULTADOS_SONDEO.cargarEstadisticas();
        }
      }
    });
  </script>
</body>
</html>
