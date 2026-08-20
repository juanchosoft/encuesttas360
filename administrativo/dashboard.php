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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>

  <style>
    :root{
      --brand:#20427F;
      --brand2:#132b52;
      --brand3:#2e58a8;
      --page:#f5f7fb;
      --card:#ffffff;
      --stroke:rgba(15,23,42,.09);
      --text:#0f172a;
      --muted:#64748b;
      --r-xl:22px;
      --r-lg:16px;
      --shadow:0 18px 45px rgba(2,6,23,.09);
      --shadow2:0 10px 24px rgba(2,6,23,.08);
    }

    body{ background: var(--page); color: var(--text); }

    .content .container-fluid{
      max-width: 1420px;
      margin: 0 auto;
      padding-left: 14px !important;
      padding-right: 14px !important;
    }

    /* ── Hero ── */
    .dash-hero{
      border-radius: var(--r-xl);
      overflow: hidden;
      background:
        radial-gradient(900px 280px at 12% 0%, rgba(46,88,168,.18), transparent 55%),
        radial-gradient(800px 280px at 90% 10%, rgba(32,66,127,.14), transparent 55%),
        linear-gradient(135deg,#fff,#f8faff);
      border: 1px solid var(--stroke);
      box-shadow: var(--shadow);
      padding: 22px;
      margin-bottom: 22px;
    }
    .dash-hero h2{ font-weight:900; letter-spacing:-.5px; margin:0; }
    .dash-hero p{ color:var(--muted); margin:6px 0 0; }

    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:7px 12px; border-radius:999px;
      background:rgba(32,66,127,.08); border:1px solid rgba(32,66,127,.14);
      color:var(--brand2); font-weight:800; font-size:12px;
    }

    /* ── Tabs ── */
    .tab-nav{
      display:flex; gap:6px; flex-wrap:wrap;
      background:var(--card);
      border:1px solid var(--stroke);
      border-radius: var(--r-xl);
      box-shadow: var(--shadow2);
      padding: 10px;
      margin-bottom: 22px;
    }
    .tab-btn{
      display:flex; align-items:center; gap:9px;
      padding:11px 18px; border-radius:14px;
      border:1px solid transparent;
      background:transparent;
      font-weight:800; font-size:.93rem; color:var(--muted);
      cursor:pointer; transition: all .18s ease;
      flex:1; justify-content:center;
    }
    .tab-btn:hover{ background:rgba(32,66,127,.06); color:var(--brand2); }
    .tab-btn.active{
      background:linear-gradient(135deg,var(--brand3),var(--brand));
      color:#fff; border-color:rgba(32,66,127,.20);
      box-shadow:0 12px 28px rgba(32,66,127,.22);
    }
    .tab-btn .tab-badge{
      display:inline-flex; align-items:center; justify-content:center;
      min-width:20px; height:20px; border-radius:999px;
      padding:0 6px; font-size:11px; font-weight:900;
      background:rgba(255,255,255,.22);
    }
    .tab-btn:not(.active) .tab-badge{
      background:rgba(32,66,127,.12); color:var(--brand2);
    }

    .tab-pane-content{ display:none; }
    .tab-pane-content.active{ display:block; animation: fadeIn .28s ease; }
    @keyframes fadeIn{ from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

    /* ── Cards ── */
    .r-card{
      background:var(--card); border:1px solid var(--stroke);
      border-radius:var(--r-xl); box-shadow:var(--shadow2); overflow:hidden;
    }
    .r-card-header{
      padding:16px 18px; border-bottom:1px solid var(--stroke);
      background:linear-gradient(180deg,#fff,#f8faff);
      display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    }
    .r-card-header h5{ margin:0; font-weight:900; }
    .r-card-body{ padding:18px; }

    /* ── Selector bar ── */
    .sel-bar{
      background:var(--card); border:1px solid var(--stroke);
      border-radius:var(--r-xl); box-shadow:var(--shadow2);
      padding:16px; margin-bottom:18px;
    }
    .form-floating>.form-select{
      border-radius:14px; border:1px solid rgba(15,23,42,.13); background:#fff;
    }
    .form-floating>.form-select:focus{
      border-color:rgba(32,66,127,.45);
      box-shadow:0 0 0 .22rem rgba(32,66,127,.14);
    }

    /* ── Botones ── */
    .btn-brand{
      border-radius:14px; padding:12px 16px; font-weight:900;
      background:linear-gradient(135deg,var(--brand3),var(--brand));
      border:0; color:#fff;
      box-shadow:0 12px 22px rgba(32,66,127,.20);
      transition: filter .16s ease, transform .16s ease;
    }
    .btn-brand:hover{ filter:brightness(1.05); transform:translateY(-1px); color:#fff; }
    .btn-outline-brand{
      border-radius:14px; padding:12px 16px; font-weight:900;
      background:#fff; border:1px solid rgba(32,66,127,.25); color:var(--brand2);
    }
    .btn-outline-brand:hover{ background:rgba(32,66,127,.06); color:var(--brand2); }

    /* ── KPIs ── */
    .kpi-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:18px; }
    .kpi{
      background:var(--card); border:1px solid var(--stroke);
      border-radius:var(--r-xl); box-shadow:var(--shadow2); padding:16px;
      display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .kpi-ico{
      width:44px; height:44px; border-radius:14px; flex:0 0 44px;
      display:flex; align-items:center; justify-content:center; font-size:1.1rem; color:#fff;
    }
    .kpi-label{ font-size:.83rem; font-weight:800; color:var(--muted); }
    .kpi-value{ font-size:1.65rem; font-weight:950; color:var(--text); letter-spacing:-.5px; margin:0; line-height:1.1; }

    /* ── Charts ── */
    .chart-box{
      background:#fafbff; border:1px solid var(--stroke); border-radius:var(--r-lg); padding:14px;
    }

    /* ── Cuestionario stats ── */
    .stats-card{
      border-radius:var(--r-lg); padding:20px; color:#fff;
      box-shadow:var(--shadow2); margin-bottom:14px;
    }
    .stats-card.blue{ background:linear-gradient(135deg,#4facfe,#00f2fe); }
    .stats-card.green{ background:linear-gradient(135deg,#11998e,#38ef7d); }
    .stats-card.orange{ background:linear-gradient(135deg,#fa709a,#fee140); }
    .stats-card.purple{ background:linear-gradient(135deg,#667eea,#764ba2); }
    .stats-card .stats-number{ font-size:2.4rem; font-weight:900; margin:6px 0; }
    .stats-card .stats-label{ font-size:.88rem; opacity:.9; text-transform:uppercase; letter-spacing:1px; }
    .stats-card i{ font-size:2rem; opacity:.3; float:right; }

    .progress-wrapper{
      background:#fff; border-radius:var(--r-lg); padding:20px;
      border:1px solid var(--stroke); box-shadow:var(--shadow2); margin-bottom:14px;
    }
    .table-wrapper{
      background:#fff; border-radius:var(--r-lg); padding:20px;
      border:1px solid var(--stroke); box-shadow:var(--shadow2); margin-bottom:14px;
    }
    .progress-bar-custom{
      height:28px; border-radius:999px;
      background:linear-gradient(90deg,var(--brand3),var(--brand));
      transition:width .6s ease;
    }

    /* ── Grilla cards ── */
    .grilla-card{
      background:var(--card); border:1px solid var(--stroke);
      border-radius:var(--r-xl); box-shadow:var(--shadow2);
      padding:18px; cursor:pointer;
      transition:box-shadow .18s ease, transform .18s ease, border-color .18s ease;
    }
    .grilla-card:hover{ box-shadow:var(--shadow); border-color:rgba(32,66,127,.22); transform:translateY(-2px); }
    .grilla-ico{
      width:48px; height:48px; border-radius:14px;
      background:rgba(32,66,127,.10); display:flex; align-items:center;
      justify-content:center; color:var(--brand); font-size:1.2rem;
    }
    .grilla-title{ font-weight:900; color:var(--text); margin:0; }
    .grilla-sub{ font-size:.85rem; color:var(--muted); margin:4px 0 0; }

    /* Empty state */
    .empty-zone{
      text-align:center; padding:60px 20px;
      color:var(--muted); font-weight:700;
    }
    .empty-zone i{ font-size:3rem; opacity:.25; display:block; margin-bottom:14px; }

    @media (max-width:575px){
      .tab-btn{ font-size:.82rem; padding:9px 12px; }
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
      <div class="dash-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div>
            <div class="chip mb-2"><i class="fas fa-chart-bar"></i> Panel Analítico</div>
            <h2><?= $modulo ?></h2>
            <p>Visualiza métricas, gráficas y resultados de Sondeos y Cuestionarios en un solo lugar.</p>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <span class="chip"><i class="fas fa-clock"></i> Tiempo real</span>
            <span class="chip"><i class="fas fa-shield-alt"></i> Solo administrador</span>
          </div>
        </div>
      </div>

      <!-- SELECTOR PRINCIPAL -->
      <div class="sel-bar">
        <div class="row g-3 align-items-stretch">
          <!-- Tipo -->
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
              <label for="tipo_selector"><i class="fas fa-layer-group me-2"></i>Tipo</label>
            </div>
          </div>
          <!-- Item dinámico -->
          <div class="col-12 col-lg-6">
            <div class="form-floating">
              <select class="form-select" id="item_selector">
                <option value="">Selecciona...</option>
              </select>
              <label for="item_selector" id="item_selector_label"><i class="fas fa-list me-2"></i>Selecciona un ítem</label>
            </div>
          </div>
          <!-- Cargar -->
          <div class="col-12 col-lg-2">
            <button class="btn btn-brand w-100 h-100" onclick="DashResultados.cargar()">
              <i class="fas fa-sync-alt me-2"></i>Cargar
            </button>
          </div>
        </div>
      </div>

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
              <h5><i class="fas fa-chart-pie me-2"></i>Resultados Generales</h5>
              <span class="chip"><i class="fas fa-circle-info"></i> Porcentaje + Totales</span>
            </div>
            <div class="r-card-body">
              <div class="chart-box"><div id="chart-general"></div></div>
            </div>
          </div>

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

          <!-- Últimas respuestas -->
          <div class="table-wrapper">
            <h5 class="mb-3"><i class="fas fa-history me-2"></i>Últimas 10 Respuestas</h5>
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

          <!-- Tabs votantes -->
          <div class="table-wrapper">
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
                  <table id="tabla_respondieron" class="table table-striped table-sm fs-9 mb-0">
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
                  <table id="tabla_no_respondieron" class="table table-striped table-sm fs-9 mb-0">
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

<!-- JS de cada módulo -->
<script src="admin/js/dashboard_stats.js"></script>
<script src="admin/js/resultados_sondeos.js"></script>
<script src="admin/js/resultados_cuestionarios.js"></script>

<?php include 'admin/include/scriptsgober360.php'; ?>

<script>
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
      return;
    }

    document.getElementById('panel-empty').style.display = 'none';

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
    document.getElementById('panel-empty').style.display = '';
    // Resetear estados internos de los módulos
    $('#estadisticas-container').hide();
    $('#empty-state').show();
    $('#estadisticas_container').hide();
    $('#empty_state').show();
    // Destruir todos los charts de ApexCharts
    Object.keys(apexCharts).forEach(function(k) {
      try { apexCharts[k].destroy(); } catch(e) {}
    });
    apexCharts = {};
  }
};

// ─── INIT ─────────────────────────────────────────────────────────────────────
$(document).ready(function() {
  if (typeof DASHBOARD !== 'undefined') DASHBOARD.init();
  RESULTADOS_SONDEO.init();
  RESULTADOS_CUESTIONARIOS.init();
  DashResultados.init();
});
</script>
</body>
</html>
