<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Validar permisos
$view    = SessionData::getPermission(22);
$create  = SessionData::getPermission(23);
$edit    = SessionData::getPermission(24);
$permits = SessionData::getPermission(25);

if (!$view) { require 'permiso_denegado.php'; exit; }

include './admin/classes/Departamento.php';
include './admin/classes/PartidoPolitico.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Participantes.php';

$arr = Participantes::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];

// Informacion de departamentos
$departamentos = Departamento::getAll(null);
$departamentosResponse = $departamentos['output']['response'] ?? [];
$optionDep = "";
foreach ($departamentosResponse as $dep) {
  $optionDep .= "<option value='" . htmlspecialchars($dep['codigo_departamento'], ENT_QUOTES, 'UTF-8') . "'>" .
                htmlspecialchars($dep['codigo_departamento'], ENT_QUOTES, 'UTF-8') . " - " .
                htmlspecialchars($dep['departamento'], ENT_QUOTES, 'UTF-8') . "</option>";
}

// Informacion de los cargos publicos
$arrCargosPub = CargosPublicos::getAll(null);
$arrCargosPub = $arrCargosPub['output']['response'] ?? [];
$optionCargosPub = "";
foreach ($arrCargosPub as $val) {
  $optionCargosPub .= "<option value='" . htmlspecialchars($val['id'], ENT_QUOTES, 'UTF-8') . "'>" .
                      htmlspecialchars($val['rama'], ENT_QUOTES, 'UTF-8') . " - " .
                      htmlspecialchars($val['nombre'], ENT_QUOTES, 'UTF-8') . "</option>";
}

$modulo = 'Participantes Políticos';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Indicadores visuales para el dashboard de participantes
$totalParticipantes = is_array($arr) ? count($arr) : 0;
$totalActivos = 0;
$totalFavoritos = 0;
$totalClientes = 0;

if (is_array($arr)) {
  foreach ($arr as $p) {
    if (($p['habilitado'] ?? '') === 'si') $totalActivos++;
    if (($p['favorito'] ?? '') === 'si') $totalFavoritos++;
    if (($p['cliente'] ?? '') === 'si') $totalClientes++;
  }
}

?>

<style>
:root{
  --p360-deep:#09172f;--p360-navy:#102a56;--p360-brand:#20427F;--p360-brand2:#3168c8;--p360-blue:#4f8cff;
  --p360-cyan:#0ea5e9;--p360-success:#12b981;--p360-warning:#f59e0b;--p360-danger:#e5484d;
  --p360-bg:#f3f6fb;--p360-card:#fff;--p360-text:#101828;--p360-text2:#344054;--p360-muted:#667085;--p360-soft:#98a2b3;
  --p360-line:#e6ebf2;--p360-r:24px;--p360-r2:18px;--p360-shadow:0 22px 60px rgba(15,23,42,.09);--p360-shadow-soft:0 12px 32px rgba(15,23,42,.065);
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{background:radial-gradient(850px 440px at 3% -4%,rgba(49,104,200,.12),transparent 64%),radial-gradient(720px 420px at 105% 8%,rgba(14,165,233,.07),transparent 64%),linear-gradient(180deg,#f7f9fc,#f2f5fa);color:var(--p360-text);font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;overflow-x:hidden}
.content{padding-top:18px!important;padding-bottom:38px!important}
.container-xxl-saas{width:100%!important;max-width:1640px!important;margin:0 auto!important;padding-left:18px!important;padding-right:18px!important}

/* HERO */
.saas-hero{position:relative;isolation:isolate;overflow:hidden;min-height:215px;padding:28px 30px;margin-bottom:16px;border:1px solid rgba(255,255,255,.12);border-radius:30px;color:#fff;background:radial-gradient(520px 260px at 10% 2%,rgba(79,140,255,.34),transparent 65%),radial-gradient(470px 250px at 92% 10%,rgba(14,165,233,.22),transparent 66%),linear-gradient(135deg,#173d79 0%,#102a56 45%,#09172f 100%);box-shadow:0 28px 75px rgba(12,31,66,.24)}
.saas-hero:before{content:"";position:absolute;width:410px;height:410px;right:-145px;top:-205px;z-index:-1;border:1px solid rgba(255,255,255,.08);border-radius:50%;box-shadow:0 0 0 42px rgba(255,255,255,.022),0 0 0 86px rgba(255,255,255,.016),0 0 0 128px rgba(255,255,255,.011)}
.saas-hero-grid{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:28px;align-items:center}
.saas-eyebrow{display:inline-flex;align-items:center;gap:8px;min-height:32px;padding:7px 11px;margin-bottom:13px;border:1px solid rgba(255,255,255,.14);border-radius:999px;color:rgba(255,255,255,.88);background:rgba(255,255,255,.075);font-size:.68rem;font-weight:800;letter-spacing:.62px;text-transform:uppercase}
.saas-live{width:7px;height:7px;border-radius:50%;background:#5de4a0;box-shadow:0 0 0 5px rgba(93,228,160,.11),0 0 16px rgba(93,228,160,.48)}
.saas-hero h2{margin:0;color:#fff;font-family:"Manrope","Inter",sans-serif;font-size:clamp(1.8rem,3vw,2.9rem);line-height:1.05;font-weight:800;letter-spacing:-1.4px}
.saas-hero h2 span{color:#a9c7ff}
.saas-hero .sub{max-width:780px;margin:10px 0 0;color:rgba(255,255,255,.70)!important;font-size:.91rem;line-height:1.65;font-weight:500!important}
.saas-hero-pills{display:flex;gap:8px;flex-wrap:wrap;margin-top:18px}.saas-hero-pill{display:inline-flex;align-items:center;gap:7px;min-height:35px;padding:8px 11px;border:1px solid rgba(255,255,255,.10);border-radius:11px;color:rgba(255,255,255,.84);background:rgba(255,255,255,.07);font-size:.67rem;font-weight:700}.saas-hero-pill i{color:#9ec2ff}
.saas-metrics{display:grid;grid-template-columns:repeat(4,minmax(92px,1fr));gap:9px;min-width:520px}.saas-metric{min-height:109px;padding:14px;border:1px solid rgba(255,255,255,.12);border-radius:17px;background:linear-gradient(145deg,rgba(255,255,255,.115),rgba(255,255,255,.05));backdrop-filter:blur(14px);transition:.22s}.saas-metric:hover{transform:translateY(-4px);border-color:rgba(255,255,255,.20);background:linear-gradient(145deg,rgba(255,255,255,.17),rgba(255,255,255,.07))}.saas-metric i{width:31px;height:31px;display:flex;align-items:center;justify-content:center;margin-bottom:13px;border-radius:10px;color:#d5e6ff;background:rgba(255,255,255,.10);font-size:.78rem}.saas-metric strong{display:block;color:#fff;font-family:"Manrope","Inter",sans-serif;font-size:1.36rem;line-height:1;font-weight:800;letter-spacing:-.55px}.saas-metric span{display:block;margin-top:5px;color:rgba(255,255,255,.58);font-size:.60rem;line-height:1.25;font-weight:700}

/* TOOLBAR */
.saas-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 15px;margin-bottom:16px;border:1px solid var(--p360-line);border-radius:18px;background:rgba(255,255,255,.92);box-shadow:var(--p360-shadow-soft);backdrop-filter:blur(12px)}
.saas-toolbar-copy{display:flex;align-items:center;gap:10px}.saas-toolbar-icon{width:38px;height:38px;display:flex;align-items:center;justify-content:center;flex:0 0 38px;border-radius:12px;color:var(--p360-brand);background:#edf4ff}.saas-toolbar-copy strong{display:block;color:var(--p360-text);font-size:.79rem;font-weight:800}.saas-toolbar-copy span{display:block;margin-top:2px;color:var(--p360-soft);font-size:.66rem;font-weight:600}

/* CARDS */
.card-pro,.table-wrap{border:1px solid var(--p360-line)!important;border-radius:var(--p360-r)!important;background:#fff!important;box-shadow:var(--p360-shadow-soft)!important;overflow:hidden}.card-pro .card-header{min-height:71px;display:flex;align-items:center;padding:15px 18px!important;border-bottom:1px solid #edf0f5!important;background:radial-gradient(280px 100px at 5% 0%,rgba(79,140,255,.055),transparent 72%),linear-gradient(180deg,#fff,#fbfcff)!important}.card-pro .card-body{padding:18px}.title{margin:0;color:#182230;font-family:"Manrope","Inter",sans-serif;font-weight:800!important;letter-spacing:-.2px}.sub{color:var(--p360-muted);font-weight:600;margin-top:4px}
.card-pro .card-header i{width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;border-radius:13px;color:var(--p360-brand)!important;background:#edf4ff;font-size:.92rem!important}

/* SECTIONS */
.section-card{position:relative;border:1px solid #e9edf4;border-radius:18px;background:linear-gradient(145deg,#fff,#fbfcff);padding:16px;transition:border-color .2s ease,box-shadow .2s ease}.section-card:hover{border-color:#dce5f2;box-shadow:0 12px 26px rgba(15,23,42,.045)}.section-title{display:flex;align-items:center;gap:9px;margin:0 0 14px;color:var(--p360-text);font-size:.79rem;font-weight:800}.section-title .dot{width:9px;height:9px;border-radius:50%;background:linear-gradient(135deg,var(--p360-blue),var(--p360-brand));box-shadow:0 0 0 4px rgba(79,140,255,.09)}

/* FORM */
.form-floating>.form-control,.form-floating>.form-select{min-height:58px;border:1px solid #d9e0ea;border-radius:14px;color:var(--p360-text2);background:#fbfcfe;font-size:.80rem;font-weight:650;box-shadow:none!important;transition:.18s}.form-floating>.form-control:hover,.form-floating>.form-select:hover{border-color:#bcc8d9;background:#fff}.form-floating>.form-control:focus,.form-floating>.form-select:focus{border-color:var(--p360-blue)!important;background:#fff;box-shadow:0 0 0 4px rgba(79,140,255,.10)!important}.form-floating>label{color:#667085;font-size:.77rem;font-weight:650}
.form-label{color:#475467!important;font-size:.68rem!important;font-weight:800!important}

/* SELECT2 */
.select2-container{width:100%!important}.select2-container--bootstrap4 .select2-selection--multiple{min-height:58px!important;padding:7px 9px!important;display:flex!important;align-items:center;flex-wrap:wrap;border:1px solid #d9e0ea!important;border-radius:14px!important;background:#fbfcfe!important;box-shadow:none!important}.select2-container--bootstrap4.select2-container--focus .select2-selection--multiple{border-color:var(--p360-blue)!important;background:#fff!important;box-shadow:0 0 0 4px rgba(79,140,255,.10)!important}.select2-container--bootstrap4 .select2-selection__choice{min-height:28px;display:inline-flex;align-items:center;padding:4px 9px!important;margin:3px 5px 3px 0!important;border:1px solid #dce8fa!important;border-radius:999px!important;color:#245ba7!important;background:#eef5ff!important;font-size:.64rem!important;font-weight:750!important}.select2-dropdown{border:1px solid #dce3ed!important;border-radius:12px!important;overflow:hidden;box-shadow:0 14px 34px rgba(15,23,42,.13)}.select2-results__option{padding:9px 11px!important;font-size:.70rem!important;font-weight:600}.select2-results__option--highlighted{background:var(--p360-brand2)!important}

/* SWITCH */
.form-check.form-switch{min-height:58px;display:flex;align-items:center;gap:10px;padding:10px 12px!important;margin:0!important;border:1px solid #d9e0ea;border-radius:14px;background:#fbfcfe}.form-check.form-switch .form-check-input{width:42px!important;height:23px!important;margin:0!important;flex:0 0 42px}.form-check.form-switch .form-check-input:checked{background-color:var(--p360-success);border-color:var(--p360-success)}.form-check.form-switch .form-check-label{color:var(--p360-text2);font-size:.70rem;font-weight:800!important}

/* UPLOAD */
#dropzone-foto1{overflow:hidden!important;border:1px dashed #cbd8ea!important;border-radius:18px!important;background:radial-gradient(220px 130px at 50% 0%,rgba(79,140,255,.08),transparent 72%),linear-gradient(180deg,#fbfdff,#f5f8fc)!important;padding:0!important}#ifm1{display:block;width:100%!important;height:230px;border:0!important;background:#fff}.upload-note{display:flex;align-items:center;gap:8px;margin-top:10px;padding:10px 12px;border-radius:12px;color:#667085;background:#f8fafc;border:1px solid #e7ecf3;font-size:.64rem;font-weight:650}.upload-note i{color:var(--p360-brand2)}

/* BUTTONS */
.btn-brand,.btn-soft{min-height:43px;display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:12px!important;padding:9px 15px!important;font-size:.73rem;font-weight:800!important;transition:.18s}.btn-brand{border:0!important;color:#fff!important;background:linear-gradient(135deg,var(--p360-blue),var(--p360-brand2) 48%,var(--p360-brand))!important;box-shadow:0 11px 23px rgba(32,66,127,.22)}.btn-brand:hover{transform:translateY(-2px);box-shadow:0 16px 30px rgba(32,66,127,.29)}.btn-soft{border:1px solid #d7e2f2!important;color:var(--p360-brand)!important;background:#fff!important}.btn-soft:hover{transform:translateY(-1px);background:#f5f9ff!important}
.action-bar{position:sticky;bottom:12px;z-index:20;margin-top:15px}.action-inner{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px;border:1px solid rgba(216,225,238,.94);border-radius:17px;background:rgba(255,255,255,.92);box-shadow:0 15px 35px rgba(15,23,42,.11);backdrop-filter:blur(16px)}
.chip{display:inline-flex;align-items:center;gap:6px;min-height:31px;padding:6px 10px;border:1px solid #dce8fa;border-radius:999px;color:#265ea9;background:#eef5ff;font-size:.65rem;font-weight:800}

/* TABLE */
.table-wrap{padding:0!important;margin-top:16px}.table-wrap-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px;border-bottom:1px solid #edf0f5;background:radial-gradient(360px 120px at 5% 0%,rgba(79,140,255,.06),transparent 70%),linear-gradient(180deg,#fff,#fbfcff)}.table-wrap-head-left{display:flex;align-items:center;gap:11px}.table-wrap-icon{width:43px;height:43px;display:flex;align-items:center;justify-content:center;border-radius:13px;color:#fff;background:linear-gradient(135deg,var(--p360-blue),var(--p360-brand));box-shadow:0 10px 22px rgba(32,66,127,.20)}.table-body{padding:15px}
.table-wrap table{width:100%!important;margin:0!important;border-collapse:separate!important;border-spacing:0 7px!important}.table-wrap table thead th{padding:10px 11px!important;border:0!important;color:#667085!important;background:transparent!important;font-size:.61rem!important;font-weight:800!important;letter-spacing:.43px;text-transform:uppercase;white-space:nowrap!important}.table-wrap table tbody td{padding:10px 11px!important;border-top:1px solid #e9edf4!important;border-bottom:1px solid #e9edf4!important;color:#344054!important;background:#fff!important;font-size:.69rem!important;font-weight:600;vertical-align:middle!important;transition:.18s}.table-wrap table tbody td:first-child{border-left:1px solid #e9edf4!important;border-radius:13px 0 0 13px}.table-wrap table tbody td:last-child{border-right:1px solid #e9edf4!important;border-radius:0 13px 13px 0}.table-wrap table tbody tr{transition:transform .18s ease}.table-wrap table tbody tr:hover{transform:translateY(-2px)}.table-wrap table tbody tr:hover td{border-color:#dce7f6!important;background:linear-gradient(90deg,#f7faff,#fff)!important;box-shadow:0 9px 23px rgba(15,23,42,.05)}
.avatar-mini{width:52px;height:52px;border:1px solid #e0e6ef;border-radius:14px;object-fit:cover;background:#fff;box-shadow:0 7px 17px rgba(15,23,42,.07);transition:.18s}.avatar-mini:hover{transform:scale(1.08);box-shadow:0 12px 24px rgba(15,23,42,.11)}
.table-wrap .badge{display:inline-flex;align-items:center;gap:5px;min-height:27px;padding:5px 8px!important;border-radius:8px!important;font-size:.60rem!important;font-weight:800!important}.btn-sm.btn-primary,.btn-danger-pro{width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center;padding:0!important;border:0!important;border-radius:10px!important;color:#fff!important;transition:.18s}.btn-sm.btn-primary{background:linear-gradient(135deg,#4f8cff,#2563b9)!important;box-shadow:0 8px 16px rgba(37,99,185,.17)}.btn-danger-pro{background:linear-gradient(135deg,#f36a6a,#d83b47)!important;box-shadow:0 8px 16px rgba(216,59,71,.16)}.btn-sm.btn-primary:hover,.btn-danger-pro:hover{transform:translateY(-2px);box-shadow:0 12px 22px rgba(15,23,42,.16)}

/* DATATABLE */
.dataTables_wrapper{width:100%!important;color:var(--p360-muted);font-size:.71rem}.dataTables_wrapper .row{margin-left:0!important;margin-right:0!important;align-items:center}.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{margin-bottom:13px}.dataTables_wrapper .dataTables_length label,.dataTables_wrapper .dataTables_filter label{color:#667085;font-size:.67rem;font-weight:700}.dataTables_wrapper .dataTables_length select{min-width:72px;min-height:38px;margin:0 5px;border:1px solid #d7dee9;border-radius:10px;color:#344054;background:#fff;font-size:.70rem;font-weight:700}.dataTables_wrapper .dataTables_filter input{width:min(270px,100%);min-height:39px;margin-left:8px;padding:0 12px;border:1px solid #d7dee9;border-radius:11px;color:#344054;background:#fff;outline:none;font-size:.71rem}.dataTables_wrapper .dataTables_filter input:focus{border-color:var(--p360-blue);box-shadow:0 0 0 4px rgba(79,140,255,.10)}.dataTables_wrapper .dataTables_info{padding-top:13px!important;color:#98a2b3!important;font-size:.65rem!important;font-weight:600}.dataTables_wrapper .dataTables_paginate{display:flex;justify-content:flex-end;gap:4px;padding-top:9px!important}.dataTables_wrapper .dataTables_paginate .paginate_button{min-width:34px;height:34px;display:inline-flex!important;align-items:center;justify-content:center;margin:0 2px!important;padding:0 9px!important;border:1px solid transparent!important;border-radius:9px!important;color:#667085!important;background:transparent!important;font-size:.67rem;font-weight:800;box-shadow:none!important}.dataTables_wrapper .dataTables_paginate .paginate_button:hover{border-color:#dce8fa!important;color:#3168c8!important;background:#eff5ff!important}.dataTables_wrapper .dataTables_paginate .paginate_button.current,.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{border-color:transparent!important;color:#fff!important;background:linear-gradient(135deg,var(--p360-blue),var(--p360-brand))!important;box-shadow:0 8px 18px rgba(32,66,127,.20)!important}

@media(max-width:1320px){.saas-hero-grid{grid-template-columns:1fr}.saas-metrics{min-width:0;width:100%}}
@media(max-width:991px){.container-xxl-saas{padding-left:13px!important;padding-right:13px!important}.saas-hero{padding:23px}.card-pro .card-body{padding:14px}}
@media(max-width:767px){.content{padding-top:12px!important}.container-xxl-saas{padding-left:10px!important;padding-right:10px!important}.saas-hero{min-height:0;padding:20px 17px;border-radius:22px}.saas-hero h2{font-size:1.8rem}.saas-hero .sub{font-size:.80rem}.saas-metrics{grid-template-columns:repeat(2,1fr)}.saas-toolbar{align-items:flex-start;flex-direction:column}.section-card{padding:13px}.action-inner{align-items:stretch;flex-direction:column}.action-inner>.d-flex{width:100%}.action-inner .btn{flex:1}.table-wrap{border-radius:19px!important}.table-wrap-head{align-items:flex-start;padding:14px}.table-body{padding:10px}.dataTables_wrapper .dataTables_length,.dataTables_wrapper .dataTables_filter{text-align:left!important}.dataTables_wrapper .dataTables_filter input{width:100%;margin:6px 0 0}.dataTables_wrapper .dataTables_paginate{justify-content:center;flex-wrap:wrap}.table-responsive{overflow-x:auto;-webkit-overflow-scrolling:touch}.table-wrap table{min-width:1050px}}
@media(max-width:480px){.saas-metrics{gap:7px}.saas-metric{min-height:95px;padding:12px}.saas-metric strong{font-size:1.17rem}.saas-metric span{font-size:.57rem}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important;scroll-behavior:auto!important}}
</style>

<body class="">
  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <div class="content">
    <div class="container-fluid container-xxl-saas">
      <!-- HERO -->
      <section class="saas-hero">
        <div class="saas-hero-grid">
          <div>
            <div class="saas-eyebrow"><span class="saas-live"></span> Estadística360 · Gestión Política</div>
            <h2>Participantes <span>Políticos</span></h2>
            <div class="sub">Administra candidatos y participantes desde un único centro de control: postulación, cargo público, ubicación, puntuación, estado, prioridad e identidad visual.</div>
            <div class="saas-hero-pills">
              <span class="saas-hero-pill"><i class="fas fa-users"></i> Registro centralizado</span>
              <span class="saas-hero-pill"><i class="fas fa-star"></i> Gestión de favoritos</span>
              <span class="saas-hero-pill"><i class="fas fa-map-marker-alt"></i> Inteligencia territorial</span>
            </div>
          </div>
          <div class="saas-metrics">
            <div class="saas-metric"><i class="fas fa-user-tie"></i><strong><?= (int)$totalParticipantes ?></strong><span>Participantes registrados</span></div>
            <div class="saas-metric"><i class="fas fa-check-circle"></i><strong><?= (int)$totalActivos ?></strong><span>Participantes activos</span></div>
            <div class="saas-metric"><i class="fas fa-star"></i><strong><?= (int)$totalFavoritos ?></strong><span>Marcados como favoritos</span></div>
            <div class="saas-metric"><i class="fas fa-handshake"></i><strong><?= (int)$totalClientes ?></strong><span>Registrados como clientes</span></div>
          </div>
        </div>
      </section>

      <section class="saas-toolbar">
        <div class="saas-toolbar-copy">
          <div class="saas-toolbar-icon"><i class="fas fa-compass"></i></div>
          <div><strong>Centro de gestión de participantes</strong><span>Registra, actualiza y administra participantes políticos.</span></div>
        </div>
        <?php if ($create): ?>
          <button type="button" class="btn btn-brand" id="btnNuevoParticipante"><i class="fas fa-plus"></i> Nuevo participante</button>
        <?php endif; ?>
      </section>

      <!-- FORM CARD -->
      <div class="card card-pro mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-user-tie" style="color: var(--brand); font-size: 1.2rem;"></i>
              <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">
                Listado e Ingreso de <?= h($modulo) ?>
              </h4>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:700;">
              Campos con * son obligatorios.
            </div>
          </div>
        </div>

        <div class="card-body">
          <form id="formparticipantes" class="row g-3" role="form" autocomplete="off">
            <input type="hidden" name="op" id="op" />
            <input type="hidden" name="idParticipante" id="idParticipante" />

            <!-- Sección: Postulación -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Postulación</div>

                <div class="row g-3">
                  <div class="col-12">
                    <label for="partidoPoliticoId" class="form-label fw-bold">
                      Partido Político <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="partidoPoliticoId" name="partidoPoliticoId[]" multiple="multiple" required>
                      <?php
                        $arrPartidos = PartidoPolitico::getAll(null);
                        $arrPartidos = $arrPartidos['output']['response'] ?? [];
                        foreach ($arrPartidos as $val) {
                          echo "<option value='" . h($val['id']) . "'>" . h($val['nombre_partido']) . "</option>";
                        }
                      ?>
                    </select>
                    <small class="text-muted fw-semibold d-block mt-1">Puedes seleccionar uno o varios partidos.</small>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="cargoPublicoId" name="cargoPublicoId"
                        onchange="PARTICIPANTES.handleCargoPublicoChange();">
                        <?= $optionCargosPub ?>
                      </select>
                      <label for="cargoPublicoId">Cargo público <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <!-- Modalidad -->
                  <div class="col-12 col-md-4 col-lg-2" id="div-modalidad" style="display:none;">
                    <div class="form-floating">
                      <select class="form-select" id="modalidad" name="modalidad">
                        <option value="Lista Cerrada">Lista Cerrada</option>
                        <option value="Preferente">Preferente</option>
                        <option value="Voto Por Candidato">Voto Por Candidato</option>
                      </select>
                      <label for="modalidad">Modalidad</label>
                    </div>
                  </div>

                  <!-- Número tarjetón -->
                  <div class="col-12 col-md-4 col-lg-2" id="div-numero-candidato" style="display:none;">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="numero_candidato" name="numero_candidato">
                        <option value="seleccione">Seleccione</option>
                        <?php for($i=1; $i<=1000; $i++): ?>
                          <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                      </select>
                      <label for="numero_candidato">N. Tarjetón</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="puntos" name="puntos"
                        placeholder="Puntos" onKeyPress="return soloNumeros(event);">
                      <label for="puntos">Puntos (+)</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-floating">
                      <select class="form-select" id="favorito" name="favorito">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                      </select>
                      <label for="favorito">Favorito <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" id="habilitado" name="habilitado" value="si" checked>
                      <label class="form-check-label fw-bold" for="habilitado">Participante habilitado</label>
                    </div>
                    <small class="text-muted fw-semibold">Desmarca para deshabilitar</small>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-floating">
                      <select class="form-select" id="cliente" name="cliente">
                        <option value="no">No</option>
                        <option value="si">Sí</option>
                      </select>
                      <label for="cliente">Es Cliente <span class="text-danger">*</span></label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Sección: Ubicación -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Ubicación</div>

                <div class="row g-3">
                  <div class="col-12 col-md-4 departamento-municipio-fields d-none">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id">
                        <?= $optionDep ?>
                      </select>
                      <label for="tbl_departamento_id">Departamento <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 departamento-municipio-fields d-none">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"></select>
                      <label for="tbl_municipio_id">Municipio <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-8">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                        placeholder="Nombre completo">
                      <label for="nombre_completo">Nombre completo del candidato <span class="text-danger">*</span></label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Foto -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Foto</div>

                <div class="dropzone dropzone-multiple p-0" id="dropzone-foto1" data-dropzone="data-dropzone"
                  style="min-height: 100px; padding: 10px;">
                  <iframe id="ifm1" name="ifm1" src="upload.php" width="100%" height="230" scrolling="no"
                    frameborder="0" style="border:none;"></iframe>
                </div>

                <div class="upload-note"><i class="fas fa-image"></i><span>Usa una fotografía clara en JPG/PNG. Se mostrará en el directorio del participante.</span></div>
              </div>
            </div>

            <!-- Action bar -->
            <div class="col-12">
              <div class="action-bar">
                <div class="action-inner">
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="chip"><i class="fas fa-circle-info"></i> Listo para guardar</span>
                    <span class="text-muted" style="font-size:12px;font-weight:700;">
                      Tip: marca “Favorito” para destacar en reportes.
                    </span>
                  </div>

                  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <button type="button" onclick="PARTICIPANTES.emptyCells();" class="btn btn-soft px-4">
                      <i class="fas fa-xmark me-2"></i>Cancelar
                    </button>
                      <?php if ($create && $edit): ?>
                        <button class="btn btn-brand px-4" type="button" onclick="PARTICIPANTES.validateData();">
                          <i class="fas fa-floppy-disk me-2"></i>Guardar
                        </button>
                      <?php endif; ?>


                  </div>
                </div>
              </div>
            </div>

          </form>
        </div>
      </div>

      <!-- TABLA -->
      <div class="table-wrap">
        <div class="table-wrap-head">
          <div class="table-wrap-head-left">
            <div class="table-wrap-icon"><i class="fas fa-users"></i></div>
            <div><h4 class="title mb-1">Directorio de Participantes</h4><div class="sub">Consulta, edita y administra los participantes registrados.</div></div>
          </div>
          <div class="chip"><i class="fas fa-database"></i> <?= (int)$totalParticipantes ?> <?= $totalParticipantes === 1 ? 'registro' : 'registros' ?></div>
        </div>
        <div class="table-body">
        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 align-middle">
            <thead>
              <tr class="border-1">
                <th>Editar</th>
                <th>Eliminar</th>
                <th>Postulación</th>
                <th>Nombre</th>
                <th>Cliente</th>
                <th>Favorito</th>
                <th>Puntos</th>
                <th>Foto</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if ($isvalid && count($arr) > 0): ?>
                <?php foreach ($arr as $item): ?>
                  <?php $id = (int)($item['id'] ?? 0); ?>
                  <?php $img = "admin/get_foto_participante.php?id=" . $id; ?>

                  <tr>
                    <td>
                      <?php if ($edit): ?>
                        <button type="button" class="btn btn-sm btn-primary" title="Editar"
                          onclick="PARTICIPANTES.editData(<?= $id ?>)">
                          <i class="uil uil-edit"></i>
                        </button>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if ($permits): ?>
                        <button type="button" class="btn btn-sm btn-danger-pro" title="Eliminar"
                          onclick="PARTICIPANTES.deleteData(<?= $id ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
                      <?php else: ?>
                        <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>

                    <td><?= h($item['cargo_publico'] ?? '') ?></td>
                    <td><?= h($item['nombre_completo'] ?? '') ?></td>

                    <td>
                      <?php if (($item['cliente'] ?? '') === 'si'): ?>
                        <span class="badge bg-primary-subtle text-primary fw-bold">Sí</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">No</span>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if (($item['favorito'] ?? '') === 'si'): ?>
                        <span class="badge bg-warning-subtle text-warning fw-bold"><i class="fas fa-star me-1"></i>Sí</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">No</span>
                      <?php endif; ?>
                    </td>

                    <td><?= h($item['puntos'] ?? '') ?></td>

                    <td>
                      <img class="avatar-mini"
                        src="<?= h($img) ?>"
                        alt="Foto <?= h($item['nombre_completo'] ?? '') ?>"
                        onerror="this.src='assets/img/generic/default.png'">
                    </td>

                    <td><?= h($item['nombre_departamento'] ?? '') ?></td>
                    <td><?= h($item['nombre_municipio'] ?? '') ?></td>

                    <td>
                      <?php if (($item['habilitado'] ?? '') === 'si'): ?>
                        <span class="badge bg-success-subtle text-success fw-bold">
                          <i class="fas fa-check-circle me-1"></i>Activo
                        </span>
                      <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger fw-bold">
                          <i class="fas fa-times-circle me-1"></i>Inactivo
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="11" class="text-center py-4 text-muted">No se encontraron registros.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </div>

 <?php include 'admin/include/gerenic_script.php'; ?>

<!-- Select2 (CSS arriba en el <head> ideal, pero lo dejamos aquí si quieres) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Core (si gerenic_script ya los trae, puedes quitar duplicados) -->
<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/plugins/prism.js"></script>

<!-- ✅ IMPORTANTE: primero DEPARTAMENTO, luego PARTICIPANTES -->
<script src="admin/js/departamento.js"></script>
<script src="admin/js/participantes.js"></script>

<script>
  $(document).ready(function () {

    // Select2
    const $sel = $("#partidoPoliticoId");
    if ($sel.length && typeof $sel.select2 === "function") {
      $sel.select2({
        theme: "bootstrap4",
        width: "100%",
        placeholder: "Selecciona uno o varios partidos",
        allowClear: true
      });
    }

    // Set depto config (si existe el input)
    const departamento = $("#departamentoConfiguracionInput").val() || "";
    if (departamento) {
      $("#tbl_departamento_id").val(departamento).trigger("change");
    }

    // Cargar municipios SOLO si existe DEPARTAMENTO
    if (window.DEPARTAMENTO && typeof DEPARTAMENTO.getMunicipios === "function") {
      DEPARTAMENTO.getMunicipios();
    } else {
      console.log("⚠️ DEPARTAMENTO no está definido o no tiene getMunicipios()");
    }

    // Ajuste UI por cargo SOLO si existe PARTICIPANTES
    if (window.PARTICIPANTES && typeof PARTICIPANTES.handleCargoPublicoChange === "function") {
      PARTICIPANTES.handleCargoPublicoChange();
    } else {
      console.log("⚠️ PARTICIPANTES no está definido o no tiene handleCargoPublicoChange()");
    }

    // Nuevo participante: limpia y enfoca el formulario sin alterar la lógica existente
    $("#btnNuevoParticipante").on("click", function(){
      if (window.PARTICIPANTES && typeof PARTICIPANTES.emptyCells === "function") {
        PARTICIPANTES.emptyCells();
      }
      const form = document.getElementById("formparticipantes");
      if (form) form.scrollIntoView({behavior:"smooth", block:"start"});
      setTimeout(function(){ $("#nombre_completo").trigger("focus"); }, 350);
    });

    // Al editar, desplazar suavemente al formulario
    $(document).on("click", "button[onclick^='PARTICIPANTES.editData']", function(){
      setTimeout(function(){
        const form = document.getElementById("formparticipantes");
        if (form) form.scrollIntoView({behavior:"smooth", block:"start"});
      }, 160);
    });

  });
</script>

<?php include './admin/include/generic_dataTables.php'; ?>
<?php include 'admin/include/scriptsgober360.php'; ?>

</body>
</html>
