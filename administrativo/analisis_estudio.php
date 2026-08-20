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
$grillas = $arrGrillas['output']['response'];

// Obtener tipos de indicadores únicos
$arrFormulas = Formula::getAll(null);
$formulas = $arrFormulas['output']['response'];
$tipos_indicadores = array_unique(array_column($formulas, 'tipo_indicador'));

$modulo = 'Análisis de Estudio Electoral';
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --nav-blue:#20427F;
      --nav-blue-2:#132b52;
      --nav-blue-3:#2e58a8;

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

    body{
      background:
        radial-gradient(900px 380px at 12% 0%, rgba(46,88,168,.10), transparent 55%),
        radial-gradient(800px 380px at 90% 10%, rgba(32,66,127,.08), transparent 55%),
        linear-gradient(180deg, var(--page) 0%, var(--page2) 55%, var(--page) 100%);
      color: var(--text);
    }

    .content .container-fluid{
      max-width: 1420px;
      margin: 0 auto;
      padding-left: 12px !important;
      padding-right: 12px !important;
    }

    /* ===== HERO ===== */
    .hero{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background:
        radial-gradient(900px 320px at 18% 0%, rgba(46,88,168,.18), transparent 60%),
        radial-gradient(900px 320px at 92% 20%, rgba(32,66,127,.14), transparent 60%),
        linear-gradient(135deg, #ffffff, #fbfcff);
      box-shadow: var(--shadow-1);
      overflow: hidden;
      position: relative;
    }
    .hero .orb{
      position:absolute;
      width: 320px; height: 320px;
      border-radius: 50%;
      filter: blur(45px);
      opacity: .22;
      pointer-events:none;
      animation: floaty 8s ease-in-out infinite;
    }
    .hero .o1{ left:-90px; top:-110px; background: rgba(46,88,168,.70); }
    .hero .o2{ right:-130px; top:-120px; background: rgba(32,66,127,.62); animation-delay: -2.2s; }
    .hero .o3{ right: 18%; bottom:-190px; background: rgba(19,43,82,.52); animation-delay: -4s; }
    @keyframes floaty{
      0%,100%{ transform: translateY(0px) translateX(0px) scale(1); }
      50%{ transform: translateY(10px) translateX(8px) scale(1.03); }
    }
    .hero-inner{ padding: 22px; position: relative; z-index: 2; }
    .hero-kicker{
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(255,255,255,.75);
      border: 1px solid var(--stroke);
      box-shadow: 0 10px 22px rgba(2,6,23,.06);
      font-weight: 900;
      color: var(--text);
      letter-spacing: .2px;
    }
    .hero-title{
      margin: 12px 0 6px;
      font-weight: 950;
      letter-spacing: -.6px;
      font-size: clamp(1.15rem, 2.3vw, 1.75rem);
    }
    .hero-sub{ margin:0; color: var(--muted); max-width: 80ch; }

    /* ===== CARD PRO ===== */
    .card-pro{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background: var(--card);
      box-shadow: var(--shadow-2);
      overflow: hidden;
    }
    .card-pro .card-header{
      background: linear-gradient(180deg, #ffffff, #fbfcff) !important;
      border-bottom: 1px solid var(--stroke) !important;
    }

    /* ===== STEP HEADER ===== */
    .step-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      padding: 14px 16px;
      border-radius: 18px;
      border: 1px solid var(--stroke);
      background: linear-gradient(180deg, #ffffff, #fbfcff);
      box-shadow: 0 14px 26px rgba(2,6,23,.06);
    }
    .step-left{
      display:flex;
      align-items:center;
      gap: 12px;
      min-width: 0;
    }
    .step-pill{
      width: 36px; height: 36px;
      border-radius: 12px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight: 950;
      color: #fff;
      background: linear-gradient(135deg, var(--nav-blue-3), var(--nav-blue));
      box-shadow: 0 10px 18px rgba(32,66,127,.18);
      flex: 0 0 auto;
    }
    .step-title{
      margin:0;
      font-weight: 950;
      letter-spacing: -.2px;
      font-size: 1.03rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .step-sub{
      margin:0;
      color: var(--muted);
      font-size: .88rem;
    }
    .mini-actions{
      display:flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content:flex-end;
    }

    /* ===== Inputs pro ===== */
    .form-floating>.form-control,
    .form-floating>.form-select{
      border-radius: 14px;
      border: 1px solid var(--stroke2);
      background: #fff;
      color: var(--text);
      box-shadow: inset 0 0 0 1px rgba(15,23,42,.03);
    }
    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus{
      outline: none;
      box-shadow: var(--ring), inset 0 0 0 1px rgba(15,23,42,.04);
      border-color: rgba(46,88,168,.45);
    }
    .form-floating>label{ color: rgba(15,23,42,.70); }

    /* ===== Buttons ===== */
    .btn-pro{
      border-radius: 14px;
      padding: 12px 14px;
      font-weight: 950;
      border: 1px solid rgba(32,66,127,.20);
      box-shadow: 0 12px 22px rgba(2,6,23,.10);
      color: #fff;
      background: linear-gradient(135deg, var(--nav-blue-3), var(--nav-blue));
      min-height: 52px;
    }
    .btn-pro:hover{ filter: brightness(1.03); transform: translateY(-1px); }
    .btn-pro:active{ transform: translateY(0); }

    .btn-soft{
      border-radius: 14px;
      padding: 12px 14px;
      font-weight: 950;
      border: 1px solid var(--stroke2);
      background: #fff;
      color: var(--text);
      box-shadow: 0 12px 22px rgba(2,6,23,.06);
      min-height: 52px;
    }
    .btn-soft:hover{ background: #f8fafc; }

    /* ===== Sections divider ===== */
    .soft-hr{
      height: 1px;
      border: 0;
      background: linear-gradient(90deg, transparent, rgba(15,23,42,.14), transparent);
      margin: 18px 0;
    }

    /* ===== Banner Analisis (upgrade) ===== */
    .analisis-banner-gradient{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background:
        radial-gradient(900px 320px at 15% 0%, rgba(46,88,168,.16), transparent 60%),
        radial-gradient(900px 320px at 85% 20%, rgba(32,66,127,.12), transparent 60%),
        linear-gradient(135deg, #ffffff, #fbfcff);
      box-shadow: var(--shadow-2);
      overflow:hidden;
    }
    .analisis-badge-active{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 6px 12px;
      border-radius: 999px;
      font-weight: 950;
      background: rgba(46,88,168,.10);
      border: 1px solid rgba(46,88,168,.18);
    }
    .analisis-candidato-card{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background: #fff;
      box-shadow: 0 16px 30px rgba(2,6,23,.08);
    }
    .analisis-candidato-foto{
      border: 5px solid rgba(46,88,168,.65) !important;
    }
    .analisis-info-card{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      box-shadow: 0 14px 26px rgba(2,6,23,.06);
      overflow:hidden;
    }

    /* ===== Result cards ===== */
    .result-card{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background: #fff;
      box-shadow: 0 14px 26px rgba(2,6,23,.06);
      overflow:hidden;
      height:100%;
    }
    .result-card .topline{
      height: 5px;
      width: 100%;
      background: linear-gradient(90deg, var(--nav-blue-3), var(--nav-blue));
    }
    .result-card h6{ font-weight: 950; letter-spacing: -.2px; }
    .result-card .display-6{ font-weight: 950; letter-spacing: -.5px; }

    /* ===== Compact modules (demografia/calculadora) ===== */
    .mini-module{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background: #fff;
      box-shadow: 0 14px 26px rgba(2,6,23,.06);
      overflow:hidden;
    }
    .mini-module .module-head{
      padding: 12px 14px;
      border-bottom: 1px solid var(--stroke);
      background: linear-gradient(180deg, #ffffff, #fbfcff);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }
    .mini-module .module-body{ padding: 14px; }

    /* ===== Table Pro ===== */
    table.table{
      border-color: rgba(15,23,42,.08) !important;
    }
    .table thead th{
      font-weight: 950 !important;
      color: rgba(15,23,42,.82);
      background: #f8fafc !important;
      border-bottom: 1px solid rgba(15,23,42,.10) !important;
    }
    .table-hover tbody tr:hover{
      background: rgba(46,88,168,.05) !important;
    }

    /* ===== Sticky action bar (mejor UX) ===== */
    .actionbar{
      position: sticky;
      bottom: 12px;
      z-index: 20;
    }
    .actionbar-inner{
      border-radius: var(--radius-xl);
      border: 1px solid var(--stroke);
      background: rgba(255,255,255,.78);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 18px 45px rgba(2,6,23,.10);
      padding: 12px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      flex-wrap: wrap;
    }
    .actionbar .hint{
      color: var(--muted);
      font-size: .9rem;
    }

    /* ===== Responsive ===== */
    @media (max-width: 991.98px){
      .hero-inner{ padding: 18px; }
      .btn-pro, .btn-soft{ min-height: 50px; }
      .actionbar{ bottom: 8px; }
    }
    @media (max-width: 575.98px){
      .content .container-fluid{ padding-left: 10px !important; padding-right: 10px !important; }
      .step-head{ padding: 12px; }
      .step-title{ font-size: .98rem; }
    }

    /* Smooth reveal */
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
      <div class="container-fluid px-0 mt-4">

        <!-- HERO -->
        <div class="hero mb-4 reveal">
          <div class="orb o1"></div>
          <div class="orb o2"></div>
          <div class="orb o3"></div>

          <div class="hero-inner">
            <div class="row g-3 align-items-center">
              <div class="col-12 col-lg-8">
                <div class="hero-kicker">
                  <i class="fas fa-sparkles"></i>
                  Módulo de Analítica • Estudio Electoral
                </div>
                <div class="hero-title">
                  <i class="fas fa-chart-line me-2" style="color: var(--nav-blue)"></i>
                  <span id="spanModulo"><?php echo $modulo; ?></span>
                </div>
                <p class="hero-sub">
                  Selecciona grilla, candidato e indicador. Documenta operaciones y guarda el análisis con trazabilidad completa.
                </p>
              </div>
              <div class="col-12 col-lg-4">
                <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                  <button type="button" class="btn btn-soft" onclick="document.getElementById('tbl_grilla_id')?.focus()">
                    <i class="fas fa-search me-2"></i>Buscar grilla
                  </button>
                  <button type="button" class="btn btn-pro" onclick="ANALISIS_ESTUDIO.verResultadosGrilla();">
                    <i class="fas fa-eye me-2"></i>Resultados grilla
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD PRINCIPAL -->
        <div class="card card-pro my-4 reveal">
          <div class="card-header p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div>
                <h4 class="mb-1" style="font-weight:950; letter-spacing:-.4px;">
                  <i class="fas fa-diagram-project me-2" style="color: var(--nav-blue);"></i>
                  Flujo de Análisis (Wizard)
                </h4>
                <p class="text-muted small mb-0">Sigue los pasos en orden: grilla → candidato → tipo → indicador → registro.</p>
              </div>
              <span class="badge" style="background: rgba(46,88,168,.10); border:1px solid rgba(46,88,168,.16); color: var(--nav-blue); padding:.55rem .75rem; border-radius:999px;">
                <i class="fas fa-shield-alt me-1"></i>Seguro • No altera backend
              </span>
            </div>
          </div>

          <div class="card-body p-4">

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
            <div class="step-head mb-3" id="seccion-tipo-indicador" style="display:none;">
              <div class="step-left">
                <div class="step-pill" style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">3</div>
                <div>
                  <p class="step-title mb-0">Seleccione el Tipo de Indicador</p>
                  <p class="step-sub">Filtra las fórmulas disponibles para el cálculo.</p>
                </div>
              </div>
            </div>

            <div class="row g-3 mb-4" id="seccion-tipo-indicador-body" style="display:none;">
              <div class="col-12">
                <div class="form-floating">
                  <select class="form-select" id="tipo_indicador" name="tipo_indicador" onchange="ANALISIS_ESTUDIO.onTipoIndicadorChange();">
                    <option value="">Seleccione un tipo...</option>
                    <?php foreach ($tipos_indicadores as $tipo): ?>
                      <?php if (!empty($tipo)): ?>
                        <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </select>
                  <label for="tipo_indicador">Tipo de Indicador<span class="text-danger">*</span></label>
                </div>
              </div>
            </div>

            <hr class="soft-hr" id="hr-formulas" style="display:none;">

            <!-- PASO 4 -->
            <div class="step-head mb-3" id="seccion-formulas" style="display:none;">
              <div class="step-left">
                <div class="step-pill" style="background: linear-gradient(135deg,#0ea5e9,#38bdf8);">4</div>
                <div>
                  <p class="step-title mb-0">Seleccione el Indicador a Calcular</p>
                  <p class="step-sub">Elige la fórmula; el contexto aparecerá en el banner.</p>
                </div>
              </div>
              <span class="badge" style="background: rgba(14,165,233,.10); border:1px solid rgba(14,165,233,.20); color:#0369a1; border-radius:999px;">
                <i class="fas fa-list-check me-1"></i>Tabla dinámica
              </span>
            </div>

            <div class="row g-3 mb-4" id="seccion-formulas-body" style="display:none;">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-sm table-hover align-middle" id="tabla-formulas">
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
                              <h4 class="mb-0 fw-bold" id="info-candidato" style="font-size: 1.35rem; line-height: 1.25; color: var(--nav-blue);">-</h4>
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
        </div>

        <!-- TABLA HISTORIAL -->
        <div class="card card-pro my-4 reveal">
          <div class="card-header p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h5 class="mb-0" style="font-weight:950;">
                <i class="fas fa-history me-2" style="color: var(--nav-blue);"></i>
                Historial de Análisis Realizados
              </h5>
              <span class="badge" style="background: rgba(46,88,168,.10); border:1px solid rgba(46,88,168,.16); color: var(--nav-blue); border-radius:999px;">
                <i class="fas fa-database me-1"></i>Registro persistente
              </span>
            </div>
          </div>

          <div class="card-body p-4">
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
        </div>

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
    // init normal
    ANALISIS_ESTUDIO.init();

    // ✅ Ajuste visual: reusar secciones sin tocar tu JS
    // Mantiene IDs originales, solo mejora layout cuando se muestran.
    const _syncSections = () => {
      const s3 = document.getElementById('seccion-tipo-indicador');
      const s3b = document.getElementById('seccion-tipo-indicador-body');
      if (s3 && s3b) {
        const isVisible = s3.style.display !== 'none';
        s3b.style.display = isVisible ? '' : 'none';
      }

      const s4 = document.getElementById('seccion-formulas');
      const s4b = document.getElementById('seccion-formulas-body');
      if (s4 && s4b) {
        const isVisible = s4.style.display !== 'none';
        s4b.style.display = isVisible ? '' : 'none';
      }
    };

    // corre al cargar y cuando el DOM cambie (por tu JS)
    _syncSections();
    const obs = new MutationObserver(_syncSections);
    obs.observe(document.body, { attributes:true, childList:true, subtree:true });
  </script>
</body>
</html>
