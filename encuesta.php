<?php
require './admin/include/generic_classes.php';
include './admin/classes/FichaTecnicaEncuesta.php';
require_once './admin/classes/ParticipacionPublica.php';

$config = Util::getInformacionConfiguracion();
$opcionActivaWeb = $config[0]['opcion_activa_web'] ?? '';

if ($opcionActivaWeb !== 'cuestionario' && $opcionActivaWeb !== 'ambos') {
    if ($opcionActivaWeb === 'sondeo') {
        header('Location: sondeo_new.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

include './admin/include/generic_info_configuracion.php';

$fichaTecnicaId = isset($_GET['f']) ? intval($_GET['f']) : 0;
if ($fichaTecnicaId === 0 || !ParticipacionPublica::fichaPublicaDisponible($fichaTecnicaId)) {
    header('Location: index.php');
    exit;
}

$fichaTecnicaResult = FichaTecnicaEncuesta::getAll(['id' => $fichaTecnicaId]);
$fichaTecnica = $fichaTecnicaResult['output']['response'][0] ?? null;
if (!$fichaTecnica) {
    header('Location: index.php');
    exit;
}

$geo = ParticipacionPublica::getGeoFromSession();
$tituloEncuesta = trim((string)($fichaTecnica['tema'] ?? '')) !== '' ? $fichaTecnica['tema'] : 'Encuesta';
$urlGracias = Util::getPostParticipacionUrl();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Cuestionario - <?= htmlspecialchars($tituloEncuesta) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    :root{
      --brand:#13357b;
      --brand2:#0b1a89;
      --ink:#0f172a;
      --muted:#64748b;
      --bg:#f6f8fc;
      --card:#ffffff;
      --stroke:rgba(15,23,42,.12);
      --shadow: 0 18px 45px rgba(2,6,23,.10);
      --shadow2: 0 10px 22px rgba(2,6,23,.08);
      --r-xl: 22px;
      --r-lg: 18px;
      --r-md: 14px;
    }

    body{
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: radial-gradient(1000px 600px at 20% 0%, rgba(19,53,123,.18), transparent 60%),
                  radial-gradient(900px 560px at 90% 10%, rgba(11,26,137,.14), transparent 55%),
                  var(--bg);
      color: var(--ink);
    }

    .page-wrap{ padding: 32px 0 48px; }
    .shell{ max-width: 1020px; margin: 0 auto; padding: 0 16px; }

    /* Header hero */
    .hero{
      position: relative;
      border-radius: var(--r-xl);
      overflow: hidden;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      color: #fff;
      box-shadow: var(--shadow);
      padding: 22px 22px;
      margin-bottom: 18px;
    }
    .hero:before{
      content:"";
      position:absolute; inset:-2px;
      background:
        radial-gradient(520px 200px at 15% 15%, rgba(255,255,255,.22), transparent 60%),
        radial-gradient(600px 260px at 80% 20%, rgba(255,255,255,.14), transparent 60%),
        linear-gradient(180deg, rgba(255,255,255,.08), transparent 50%);
      pointer-events:none;
    }
    .hero .row{ position:relative; z-index:1; }
    .hero-title{ font-weight: 800; letter-spacing: -.6px; margin:0; }
    .hero-sub{ opacity: .92; margin: 6px 0 0; color: rgba(255,255,255,.90); }
    .hero-chip{
      display:inline-flex; align-items:center; gap:8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(255,255,255,.14);
      border: 1px solid rgba(255,255,255,.22);
      font-weight: 700;
      font-size: 12px;
    }
    .hero-logo{
      width: 52px; height: 52px; border-radius: 14px;
      background: rgba(255,255,255,.14);
      border:1px solid rgba(255,255,255,.22);
      display:flex; align-items:center; justify-content:center;
      overflow:hidden;
    }
    .hero-logo img{ width: 44px; height:auto; display:block; }
    .hero-actions .btn{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 800;
    }
    .btn-brand{
      background:#fff; color: var(--brand2);
      border: 0;
      box-shadow: 0 10px 22px rgba(0,0,0,.18);
    }
    .btn-brand:hover{ background:#f1f5ff; color:var(--brand2); }
    .btn-ghost{
      background: rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.22);
      color:#fff;
    }
    .btn-ghost:hover{ background: rgba(255,255,255,.18); color:#fff; }

    /* Cards */
    .panel{
      background: var(--card);
      border:1px solid var(--stroke);
      border-radius: var(--r-xl);
      box-shadow: var(--shadow2);
      overflow:hidden;
    }
    .panel-header{
      padding: 16px 18px;
      border-bottom: 1px solid var(--stroke);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      background: linear-gradient(180deg, rgba(19,53,123,.07), transparent);
    }
    .panel-title{ margin:0; font-weight: 900; letter-spacing: -.4px; }
    .panel-sub{ margin:0; color: var(--muted); font-weight:600; font-size: 13px; }

    .list-card{
      padding: 16px;
      border-radius: var(--r-lg);
      border: 1px solid var(--stroke);
      background:#fff;
      box-shadow: 0 10px 22px rgba(2,6,23,.06);
    }
    .tag{
      display:inline-flex; align-items:center; gap:7px;
      padding: 6px 10px;
      border-radius: 999px;
      font-weight:800;
      font-size: 12px;
      border: 1px solid transparent;
    }
    .tag-pend{
      background: rgba(245,158,11,.16);
      border-color: rgba(245,158,11,.22);
      color: #92400e;
    }
    .tag-ok{
      background: rgba(34,197,94,.14);
      border-color: rgba(34,197,94,.22);
      color: #166534;
    }

    .btn-soft{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 900;
    }
    .btn-soft-primary{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border:0;
      color:#fff;
      box-shadow: 0 12px 25px rgba(19,53,123,.22);
    }
    .btn-soft-primary:hover{ filter: brightness(1.03); color:#fff; }
    .btn-soft-outline{
      background:#fff;
      border:1px solid rgba(19,53,123,.26);
      color: var(--brand2);
    }
    .btn-soft-outline:hover{ background: rgba(19,53,123,.06); }

    /* Scroll mode */
    .wizard{
      padding: 18px;
    }
    .wizard-top{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      flex-wrap:wrap;
      padding: 12px 14px;
      border-radius: 18px;
      border:1px solid var(--stroke);
      background: linear-gradient(180deg, rgba(19,53,123,.06), rgba(255,255,255,.92));
    }
    .progress{
      height: 10px;
      border-radius: 999px;
      background: rgba(2,6,23,.08);
    }
    .progress-bar{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border-radius: 999px;
      transition: width .35s ease;
    }
    .wizard-help{
      color: var(--muted);
      font-weight: 700;
      font-size: 13px;
    }

    /* Capítulo header separador */
    .capitulo-header{
      margin-top: 28px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .capitulo-header:first-child{ margin-top: 0; }
    .capitulo-badge{
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #fff;
      background: linear-gradient(135deg,#20427F,#132b52);
      white-space: nowrap;
    }
    .capitulo-line{
      flex: 1;
      height: 2px;
      background: linear-gradient(90deg, rgba(32,66,127,.3), transparent);
      border-radius: 999px;
    }

    .q-card{
      position: relative;
      margin-top: 16px;
      border-radius: calc(var(--r-xl) + 2px);
      border:1px solid rgba(15,23,42,.10);
      background: linear-gradient(180deg, #ffffff, #fcfdff);
      box-shadow: 0 18px 40px rgba(2,6,23,.08);
      overflow:hidden;
      transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }
    .q-card::before{
      content:"";
      position:absolute;
      inset:0 0 auto 0;
      height: 4px;
      background: linear-gradient(90deg, var(--brand), #3b82f6, #93c5fd);
      opacity:.92;
    }
    .q-card:hover{
      transform: translateY(-2px);
      box-shadow: 0 22px 44px rgba(2,6,23,.10);
    }
    .q-card.answered{
      border-color: rgba(34,197,94,.30);
      box-shadow: 0 18px 38px rgba(34,197,94,.12);
    }
    .q-head{
      padding: 0;
      border-bottom: 1px solid var(--stroke);
    }
    .q-capitulo{
      display: inline-block;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .7px;
      color: #fff;
      background: linear-gradient(135deg,#20427F,#132b52);
      padding: 4px 12px;
      border-radius: 0 0 8px 0;
    }
    .q-capitulo-wrap{
      padding: 0;
      border-bottom: 1px solid rgba(19,53,123,.1);
    }
    .q-head-meta{
      display: flex; flex-direction: column;
      padding: 14px 16px 10px;
      background: linear-gradient(180deg, rgba(19,53,123,.09), rgba(19,53,123,.03));
      border-bottom: 1px solid rgba(19,53,123,.08);
      gap: 4px;
    }
    .q-numeral{
      display:inline-flex;
      align-items:center;
      gap:8px;
      width: fit-content;
      max-width: 100%;
      font-size: 12px;
      font-weight: 900;
      color: #13357b;
      line-height: 1.3;
      background: rgba(255,255,255,.88);
      border:1px solid rgba(19,53,123,.14);
      border-radius: 999px;
      padding: 6px 12px;
      box-shadow: 0 6px 14px rgba(2,6,23,.04);
    }
    .q-kicker{
      display:flex; align-items:center; justify-content:space-between;
      color: var(--muted);
      font-weight: 800;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .8px;
    }
    .q-head-body{
      padding: 12px 16px 14px;
    }
    .q-texto-adicional{
      font-size: 13px;
      font-weight: 700;
      color: #1e293b;
      margin: 0 0 6px;
    }
    .q-enunciado{
      font-size: 13px;
      color: #000000;
      font-style: italic;
      line-height: 1.5;
      padding: 8px 12px;
      background: rgba(250, 3, 3, 0.99);
      border-left: 3px solid rgba(32,66,127,.35);
      border-radius: 0 8px 8px 0;
      margin-bottom: 2px;
    }
    .q-enunciado-comun{
      font-size: 18px;
      font-weight: 800;
      color: #020202;
  
      padding: 14px 18px;
      background: linear-gradient(135deg, rgba(19,53,123,.08), rgba(19,53,123,.03));
      border: 1px solid rgba(19,53,123,.12);
      border-left: 4px solid var(--brand);
      border-radius: 0 var(--r-lg) var(--r-lg) 0;
      margin: 18px 0 12px;
      box-shadow: 0 4px 12px rgba(2,6,23,.04);
    }
    .q-title{
      margin: 8px 0 0;
      font-weight: 900;
      letter-spacing: -.4px;
      font-size: 19px;
      line-height: 1.32;
      padding: 0 16px 16px;
      border-top: 1px solid rgba(15,23,42,.08);
      padding-top: 14px;
      color:#0f172a;
    }
    .q-body{ padding: 16px 16px 18px; }
    .hint{
      display:flex; gap:10px; align-items:flex-start;
      padding: 10px 12px;
      border-radius: 14px;
      background: rgba(19,53,123,.06);
      border:1px solid rgba(19,53,123,.12);
      color: #334155;
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 12px;
    }
    .hint i{ margin-top: 2px; color: var(--brand2); }

    /* opciones estilo pro */
    .opts-vertical{ display: flex; flex-direction: column; gap: 10px; }
    .opts-horizontal{ display: flex; flex-wrap: wrap; gap: 10px; }
    .opts-compact{
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(72px, 1fr));
      gap: 10px;
      margin-top: 4px;
    }
    .opt{
      position:relative;
      display:flex;
      gap: 10px;
      align-items: center;
      padding: 12px 14px;
      border: 1px solid rgba(15,23,42,.11);
      border-radius: 18px;
      transition: all .18s ease;
      cursor:pointer;
      background: linear-gradient(180deg, #ffffff, #f8fafc);
      user-select:none;
      min-height: 58px;
    }
    .opt-h{
      flex: 1 1 auto;
      min-width: 70px;
      justify-content: center;
      text-align: center;
      flex-direction: column;
      gap: 7px;
      padding: 14px 10px;
    }
    .opt-compact{
      min-width: 0;
      min-height: 76px;
      justify-content: center;
      text-align: center;
      flex-direction: column;
      gap: 7px;
      padding: 9px 6px 10px;
      border-radius: 20px;
      background: linear-gradient(180deg, #ffffff, #f8fbff);
      box-shadow: 0 8px 18px rgba(2,6,23,.05);
    }
    .opt-h .form-check-input{ margin: 0 auto; }
    .opt-compact .form-check-input{ position:absolute; opacity:0; pointer-events:none; width:0; height:0; }
    .opt:hover{
      border-color: rgba(19,53,123,.30);
      transform: translateY(-1px);
      box-shadow: 0 12px 22px rgba(2,6,23,.08);
    }
    .opt.selected{
      border-color: rgba(19,53,123,.35);
      background: linear-gradient(180deg, rgba(19,53,123,.10), rgba(19,53,123,.05));
      box-shadow: 0 14px 26px rgba(19,53,123,.13);
    }
    .opt .form-check-input{
      flex-shrink: 0;
      width: 18px;
      height: 18px;
      accent-color: var(--brand);
    }
    .opt .form-check-label{
      font-weight: 800;
      color: #0f172a;
      line-height: 1.25;
    }
    .opt-compact-check{
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border:2px solid rgba(148,163,184,.55);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size: 9px;
      color:#fff;
      background:#fff;
      transition: all .18s ease;
    }
    .opt-compact-body{
      display:flex;
      flex-direction:column;
      align-items:center;
      gap: 2px;
      line-height:1;
    }
    .opt-compact-value{
      font-size: 22px;
      font-weight: 900;
      color:#0f172a;
      letter-spacing:-.4px;
    }
    .opt-compact-caption{
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .6px;
      color: var(--muted);
      font-weight: 900;
    }
    .opt-compact.selected .opt-compact-check{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border-color: transparent;
      box-shadow: 0 6px 12px rgba(19,53,123,.18);
    }
    .opt-compact.selected .opt-compact-value{
      color: var(--brand2);
    }

    .respuesta-texto{
      border-radius: 16px;
      border: 1px solid rgba(15,23,42,.14);
      padding: 12px 14px;
      min-height: 110px;
      font-weight: 650;
    }
    .respuesta-texto:focus{
      border-color: rgba(19,53,123,.35);
      box-shadow: 0 0 0 .2rem rgba(19,53,123,.12);
    }

    /* bottom nav */
    .wizard-nav{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      margin-top: 14px;
      padding: 12px;
      border-radius: 18px;
      border:1px solid var(--stroke);
      background: rgba(255,255,255,.88);
      backdrop-filter: blur(10px);
      position: sticky;
      bottom: 12px;
      z-index: 20;
    }

    .nav-left, .nav-right{ display:flex; gap:10px; align-items:center; }
    .btn-nav{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 900;
      border: 1px solid rgba(15,23,42,.12);
      background: #fff;
      color: #0f172a;
    }
    .btn-nav:hover{ background: rgba(19,53,123,.06); border-color: rgba(19,53,123,.26); }
    .btn-next{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border: 0;
      color: #fff;
      box-shadow: 0 12px 25px rgba(19,53,123,.22);
    }
    .btn-next:hover{ filter: brightness(1.03); color:#fff; }
    .btn-send{
      background: linear-gradient(135deg, #16a34a, #22c55e);
      border: 0;
      color: #fff;
      box-shadow: 0 12px 25px rgba(34,197,94,.20);
    }
    .btn-send:hover{ filter: brightness(1.03); color:#fff; }

    .status-pill{
      display:inline-flex; align-items:center; gap:8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(15,23,42,.06);
      border:1px solid rgba(15,23,42,.10);
      font-weight: 900;
      color:#0f172a;
      font-size: 12px;
    }
    .status-pill .dot{
      width: 10px; height: 10px; border-radius: 50%;
      background: rgba(100,116,139,.6);
    }
    .status-pill.ok .dot{ background: rgba(34,197,94,.95); }
    .status-pill.warn .dot{ background: rgba(245,158,11,.95); }

    /* Modal */
    .modal-content{ border-radius: 18px; border: 1px solid rgba(15,23,42,.10); }
    .modal-header{ border-bottom: 1px solid rgba(255,255,255,.18); }
    .modal-body{ background: #fff; }
    .modal-footer{ border-top: 1px solid rgba(15,23,42,.10); }



    /* Navegación real por capítulos */
    .chapter-shell{
      position: relative;
    }
    .chapter-slide{
      display: none;
      animation: fadeChapter .24s ease both;
    }
    .chapter-slide.active{
      display: block;
    }
    @keyframes fadeChapter{
      from{ opacity:0; transform: translateY(10px); }
      to{ opacity:1; transform: translateY(0); }
    }
    .chapter-intro-card{
      display:grid;
      grid-template-columns: auto 1fr auto;
      gap: 14px;
      align-items:center;
      border-radius: var(--r-xl);
      border:1px solid rgba(19,53,123,.15);
      background:
        radial-gradient(500px 180px at 10% 0%, rgba(19,53,123,.12), transparent 60%),
        linear-gradient(180deg, #fff, rgba(248,250,252,.94));
      box-shadow: 0 14px 30px rgba(2,6,23,.07);
      padding: 16px;
      margin-bottom: 16px;
    }
    .chapter-intro-icon,
    .next-chapter-icon{
      width: 48px;
      height: 48px;
      border-radius: 16px;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#fff;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 12px 24px rgba(19,53,123,.20);
      flex-shrink:0;
    }
    .chapter-eyebrow{
      color: var(--brand2);
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .8px;
      margin-bottom: 3px;
    }
    .chapter-intro-content h3{
      margin:0;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: -.5px;
      color:#0f172a;
      line-height:1.15;
    }
    .chapter-intro-content p{
      margin: 6px 0 0;
      color: var(--muted);
      font-size: 13px;
      line-height:1.5;
      font-weight: 650;
    }
    .chapter-intro-count{
      min-width: 92px;
      text-align:center;
      border-radius: 18px;
      padding: 10px 12px;
      background: rgba(19,53,123,.07);
      border:1px solid rgba(19,53,123,.13);
    }
    .chapter-intro-count strong{
      display:block;
      font-size: 24px;
      line-height:1;
      color: var(--brand2);
      font-weight: 900;
    }
    .chapter-intro-count span{
      display:block;
      margin-top: 4px;
      color: var(--muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing:.5px;
    }
    .next-chapter-card{
      display:flex;
      align-items:center;
      gap: 12px;
      margin-top: 16px;
      padding: 14px;
      border-radius: var(--r-xl);
      border: 1px dashed rgba(19,53,123,.30);
      background: rgba(19,53,123,.055);
    }
    .next-chapter-card span{
      display:block;
      color: var(--muted);
      font-size: 12px;
      font-weight: 850;
      text-transform: uppercase;
      letter-spacing:.4px;
    }
    .next-chapter-card strong{
      display:block;
      margin-top: 3px;
      color:#0f172a;
      font-size: 16px;
      font-weight: 900;
      line-height:1.2;
    }
    .next-chapter-card.final{
      border-style: solid;
      background: rgba(34,197,94,.08);
      border-color: rgba(34,197,94,.22);
    }
    .next-chapter-card.final .next-chapter-icon{
      background: linear-gradient(135deg, #16a34a, #22c55e);
    }
    .wizard-nav-center{
      flex: 1;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      gap: 4px;
      text-align:center;
      min-width: 160px;
    }
    .next-title-mini{
      color: var(--muted);
      font-size: 11px;
      line-height: 1.2;
      font-weight: 800;
    }
    .q-footer-status{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      margin-top: 14px;
    }
    .q-status-left,
    .q-status-right{
      font-size: 12px;
    }

    /* Responsive tweaks */
    @media (max-width: 768px){
      .page-wrap{ padding: 18px 0 34px; }
      .shell{ padding: 0 10px; }
      .panel-header{ align-items:flex-start; flex-direction:column; }
      .hero{ padding: 18px; border-radius: 20px; }
      .hero-title{ font-size: 21px; }
      .hero-chip{ font-size: 11px; padding: 7px 10px; }
      .hero-actions{ width:100%; }
      .hero-actions .btn{ flex: 1 1 0; padding: 10px 8px; font-size: 13px; }
      .wizard{ padding: 12px; }
      .wizard-top{ padding: 12px; }
      .chapter-intro-card{
        grid-template-columns: auto 1fr;
        padding: 14px;
        gap: 12px;
      }
      .chapter-intro-count{
        grid-column: 1 / -1;
        display:flex;
        align-items:center;
        justify-content:center;
        gap: 8px;
        min-width: 0;
      }
      .chapter-intro-count strong{ font-size: 20px; }
      .chapter-intro-count span{ margin:0; }
      .chapter-intro-content h3{ font-size: 18px; }
      .chapter-intro-content p{ font-size: 12px; }
      .q-card{ border-radius: 20px; margin-top: 12px; }
      .q-title{ font-size: 16px; line-height:1.38; padding-left: 14px; padding-right: 14px; }
      .q-body{ padding: 12px 14px 14px; }
      .opts-horizontal{
        display:grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
      }
      .opts-compact{
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
      }
      .opt-h{ min-width:0; width:100%; }
      .opt{ padding: 11px 10px; border-radius: 15px; }
      .opt .form-check-label{ font-size: 13px; }
      .opt-compact{ min-height: 72px; padding: 8px 4px 10px; }
      .opt-compact-value{ font-size: 19px; }
      .opt-compact-caption{ font-size: 9px; }
      .q-footer-status{ align-items:flex-start; flex-direction:column; gap: 6px; }
      .wizard-nav{
        left: 10px;
        right: 10px;
        bottom: 8px;
        display:grid;
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 10px;
      }
      .nav-left,
      .nav-right{
        width:100%;
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
      }
      .nav-left{ grid-template-columns: 1fr; }
      .wizard-nav-center{ order:-1; width:100%; min-width:0; }
      .btn-nav{ width:100%; padding: 11px 10px; font-size: 13px; }
      .btn-send{ grid-column: 1 / -1; }
      .next-chapter-card{ align-items:flex-start; padding: 12px; }
      .next-chapter-card strong{ font-size: 14px; }
    }

    @media (max-width: 420px){
      .opts-horizontal{ grid-template-columns: 1fr; }
      .opts-compact{ grid-template-columns: repeat(5, minmax(0, 1fr)); }
      .hero-logo{ width:46px; height:46px; }
      .chapter-intro-icon,
      .next-chapter-icon{ width:42px; height:42px; border-radius: 14px; }
      .list-card{ padding: 13px; }
      .btn-soft{ width:100%; }
    }
  </style>

  <style>
    .enc-paso-inicio .form-label{ font-weight:800; font-size:18px; color:var(--ink); }
    .enc-paso-inicio .form-select{ min-height:56px; font-size:18px; border:2px solid #98A2B3; border-radius:12px; }
    .enc-btn-grande{ min-height:56px; font-size:19px; font-weight:800; border-radius:14px; padding:0 28px; }
    .enc-nav{ display:flex; justify-content:space-between; gap:12px; margin-top:18px; flex-wrap:wrap; }
    .enc-nav .btn{ min-height:56px; font-size:19px; font-weight:800; border-radius:14px; }
    .enc-progreso{ margin-bottom:16px; }
    .enc-progreso .progress{ height:12px; }
    .enc-progreso-texto{ font-weight:700; color:var(--muted); font-size:15px; margin-top:6px; }
    .cq-pregunta-card button.active{ box-shadow:0 0 0 2px rgba(45,99,189,.25); }
    #encContenedor .cq-pregunta-card, .cq-pregunta-card.cq-fullscreen{ background:#fff !important; color:#101828 !important; }
    #encContenedor .cq-pregunta-titulo{ color:#101828 !important; font-family:"Inter", system-ui, sans-serif; }
    #encContenedor .cq-pregunta-header .text-primary{ color:#13357b !important; }
    #encContenedor .cq-pregunta-header .bg-light{ background:#f2f4f7 !important; color:#344054 !important; }
    #encContenedor .cq-grilla-goto-fila{ background:#fff !important; color:#101828 !important; border:2px solid #667085 !important; min-height:44px; }
    #encContenedor .cq-grilla-goto-fila.btn-primary{ background:#13357b !important; color:#fff !important; border-color:#13357b !important; }
    #encContenedor .cq-grilla-opt-btn, #encContenedor .cq-normal-opt-btn, #encContenedor .cq-lista-opt-btn{ color:#101828 !important; width:100%; }
    #encContenedor .cq-grilla-opt-btn.active, #encContenedor .cq-normal-opt-btn.active, #encContenedor .cq-lista-opt-btn.active{ background:#ebf3ff !important; border-color:#2d63bd !important; }
    #encContenedor .btn{ min-height:52px; font-size:18px; font-weight:700; }
    #encContenedor .cq-btn-tarjeta-emociones{ background:#fff !important; color:#13357b !important; border:2px solid #13357b !important; }
    .cq-pregunta-card.cq-fullscreen .cq-btn-tarjeta-emociones{ background:#fff !important; color:#13357b !important; opacity:1 !important; }
    @media (max-width: 575px){ .enc-nav .btn{ width:100%; } }
  </style>
</head>

<body>
<?php include './admin/include/menu_registro.php'; ?>

<div class="page-wrap">
  <div class="shell" id="cuestionario_container" data-ficha-tecnica-id="<?= $fichaTecnicaId ?>">

    <div class="hero">
      <div class="row g-3 align-items-center">
        <div class="col-auto">
          <div class="hero-logo"><i class="fa-solid fa-clipboard-list" style="font-size:22px;"></i></div>
        </div>
        <div class="col">
          <h2 class="hero-title"><?= htmlspecialchars($tituloEncuesta) ?></h2>
          <p class="hero-sub mb-0">Responda con calma. Sus respuestas se guardan en este dispositivo mientras avanza.</p>
        </div>
      </div>
    </div>

    <div class="panel">
      <div id="encCargando" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3 mb-0 fw-semibold text-muted">Cargando la encuesta...</p>
      </div>

      <div id="encError" class="alert alert-danger d-none" style="border-radius:16px;font-size:18px;"></div>

      <div id="encYaParticipo" class="alert alert-info d-none" style="border-radius:16px;font-size:18px;">
        <i class="fas fa-circle-check me-2"></i>Este dispositivo ya participó en esta encuesta. ¡Gracias!
      </div>

      <div id="encPasoInicio" class="enc-paso-inicio d-none">
        <h4 class="panel-title mb-1">¿Dónde está usted?</h4>
        <p class="panel-sub mb-3" style="font-size:16px;">Esto nos permite hacerle las preguntas de su municipio.</p>
        <div class="mb-3">
          <label class="form-label" for="encMunicipio">Su municipio</label>
          <select class="form-select" id="encMunicipio"></select>
        </div>
        <div class="mb-4 d-none" id="encZonaWrap">
          <label class="form-label" for="encZona">Su comuna, sector o zona</label>
          <select class="form-select" id="encZona"></select>
        </div>
        <button type="button" class="btn btn-brand enc-btn-grande" id="encComenzar" style="background:var(--brand);color:#fff;">
          Comenzar <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>
      </div>

      <div id="encPasoPreguntas" class="d-none">
        <div class="enc-progreso">
          <div class="progress"><div class="progress-bar" id="encBarra" role="progressbar" style="width:0%"></div></div>
          <div class="enc-progreso-texto" id="encProgresoTexto"></div>
        </div>
        <div id="encContenedor"></div>
        <div class="enc-nav" id="encNav">
          <button type="button" class="btn btn-outline-secondary" id="encAnterior"><i class="fa-solid fa-arrow-left me-2"></i>Anterior</button>
          <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary" id="encContinuar">Continuar<i class="fa-solid fa-arrow-right ms-2"></i></button>
            <button type="button" class="btn btn-success d-none" id="encEnviar"><i class="fa-solid fa-paper-plane me-2"></i>Enviar mis respuestas</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include './admin/include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="admin/js/device_participacion.js"></script>
<script src="js/lib/motor_cuestionario.js?v=<?= filemtime(__DIR__ . '/js/lib/motor_cuestionario.js') ?>"></script>
<script src="js/lib/motor_cuestionario_ui.js?v=<?= filemtime(__DIR__ . '/js/lib/motor_cuestionario_ui.js') ?>"></script>
<script>
window.ENCUESTA_CONFIG = {
  fichaId: <?= (int)$fichaTecnicaId ?>,
  municipioGps: <?= json_encode((string)($geo['codigo_municipio'] ?? '')) ?>,
  urlGracias: <?= json_encode($urlGracias, JSON_UNESCAPED_SLASHES) ?>
};
</script>
<script src="js/encuesta_publica.js?v=<?= filemtime(__DIR__ . '/js/encuesta_publica.js') ?>"></script>
</body>
</html>
