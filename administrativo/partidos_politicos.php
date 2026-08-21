<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/PartidoPolitico.php';

// Variables de configuración - logo, municipio, departamento...
include './admin/include/generic_info_configuracion.php';

// Permisos
$view    = SessionData::getPermission(10);
$create  = SessionData::getPermission(11);
$edit    = SessionData::getPermission(12);
$permits = SessionData::getPermission(13);

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Información de PartidoPolitico
$arrResponse = PartidoPolitico::getAll(null);
$isvalid     = $arrResponse['output']['valid'] ?? false;
$arr         = $arrResponse['output']['response'] ?? [];
$modulo      = 'Partidos políticos';

function h($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Indicadores visuales de la vista
$totalPartidos = is_array($arr) ? count($arr) : 0;
$totalConLogo = 0;
$totalConResolucion = 0;
$totalConEmail = 0;

if (is_array($arr)) {
    foreach ($arr as $p) {
        if (!empty($p['logo'])) {
            $totalConLogo++;
        }

        if (!empty($p['resolucion'])) {
            $totalConResolucion++;
        }

        if (!empty($p['email_contacto'])) {
            $totalConEmail++;
        }
    }
}
?>

<body class="s360-partidos-page">

<!-- PRELOADER -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

<?php include './admin/include/navbar.php'; ?>
<?php include './admin/include/header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ============================================================
   ESTADÍSTICA360 · PARTIDOS POLÍTICOS
   Vista SaaS Premium
============================================================ */

:root{
    --s360-primary:#20427F;
    --s360-primary-2:#2F67C4;
    --s360-primary-3:#4F8CFF;
    --s360-deep:#0B1F43;

    --s360-success:#12B981;
    --s360-warning:#F59E0B;
    --s360-danger:#E5484D;
    --s360-info:#0EA5E9;

    --s360-page:#F3F6FB;
    --s360-card:#FFFFFF;
    --s360-card-soft:#F8FAFD;

    --s360-text:#101828;
    --s360-text-2:#344054;
    --s360-muted:#667085;
    --s360-soft:#98A2B3;

    --s360-line:#E6EBF2;
    --s360-line-2:rgba(15,23,42,.075);

    --s360-r-xxl:30px;
    --s360-r-xl:24px;
    --s360-r-lg:18px;
    --s360-r-md:14px;

    --s360-shadow:
        0 22px 60px rgba(15,23,42,.09);

    --s360-shadow-soft:
        0 12px 32px rgba(15,23,42,.065);

    --s360-shadow-hover:
        0 28px 70px rgba(15,23,42,.13);
}

*{
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body.s360-partidos-page{
    margin:0;
    background:
        radial-gradient(820px 440px at 4% -4%, rgba(47,103,196,.11), transparent 65%),
        radial-gradient(720px 420px at 105% 8%, rgba(14,165,233,.07), transparent 64%),
        linear-gradient(180deg,#F7F9FC 0%,#F2F5FA 100%);
    color:var(--s360-text);
    font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    -webkit-font-smoothing:antialiased;
    overflow-x:hidden;
}

body.s360-partidos-page::before{
    content:"";
    position:fixed;
    inset:0;
    z-index:-1;
    pointer-events:none;
    opacity:.32;
    background-image:
        linear-gradient(rgba(32,66,127,.022) 1px,transparent 1px),
        linear-gradient(90deg,rgba(32,66,127,.022) 1px,transparent 1px);
    background-size:36px 36px;
    mask-image:linear-gradient(to bottom,#000,transparent 82%);
}

/* ============================================================
   CONTENIDO
============================================================ */
.content{
    padding-top:18px !important;
    padding-bottom:38px !important;
}

.s360-shell{
    width:100%;
    max-width:1640px;
    margin:0 auto;
    padding:0 18px;
}

/* ============================================================
   HERO
============================================================ */
.s360-hero{
    position:relative;
    isolation:isolate;
    overflow:hidden;

    min-height:210px;
    padding:28px 30px;
    margin-bottom:16px;

    border:1px solid rgba(255,255,255,.12);
    border-radius:var(--s360-r-xxl);

    color:#fff;

    background:
        radial-gradient(520px 260px at 11% 3%, rgba(79,140,255,.34), transparent 65%),
        radial-gradient(480px 260px at 93% 12%, rgba(14,165,233,.22), transparent 66%),
        linear-gradient(135deg,#173D79 0%,#102A56 44%,#09172F 100%);

    box-shadow:
        0 28px 75px rgba(12,31,66,.24);
}

.s360-hero::before{
    content:"";
    position:absolute;
    width:390px;
    height:390px;
    right:-135px;
    top:-190px;
    z-index:-1;

    border:1px solid rgba(255,255,255,.08);
    border-radius:50%;

    box-shadow:
        0 0 0 42px rgba(255,255,255,.022),
        0 0 0 86px rgba(255,255,255,.016),
        0 0 0 126px rgba(255,255,255,.011);
}

.s360-hero-grid{
    display:grid;
    grid-template-columns:minmax(0,1fr) auto;
    gap:28px;
    align-items:center;
}

.s360-eyebrow{
    display:inline-flex;
    align-items:center;
    gap:8px;

    min-height:32px;
    padding:7px 11px;
    margin-bottom:13px;

    border:1px solid rgba(255,255,255,.14);
    border-radius:999px;

    color:rgba(255,255,255,.88);
    background:rgba(255,255,255,.075);
    backdrop-filter:blur(12px);

    font-size:.68rem;
    font-weight:800;
    letter-spacing:.62px;
    text-transform:uppercase;
}

.s360-live-dot{
    width:7px;
    height:7px;
    border-radius:50%;
    background:#5DE4A0;
    box-shadow:
        0 0 0 5px rgba(93,228,160,.11),
        0 0 16px rgba(93,228,160,.48);
}

.s360-hero h1{
    margin:0;
    color:#fff;

    font-family:"Manrope","Inter",sans-serif;
    font-size:clamp(1.75rem,3vw,2.8rem);
    line-height:1.05;
    font-weight:800;
    letter-spacing:-1.4px;
}

.s360-hero h1 span{
    color:#A9C7FF;
}

.s360-hero p{
    max-width:760px;
    margin:10px 0 0;

    color:rgba(255,255,255,.70);

    font-size:.91rem;
    line-height:1.65;
    font-weight:500;
}

.s360-hero-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    margin-top:18px;
}

.s360-hero-pill{
    display:inline-flex;
    align-items:center;
    gap:7px;

    min-height:35px;
    padding:8px 11px;

    border:1px solid rgba(255,255,255,.10);
    border-radius:11px;

    color:rgba(255,255,255,.84);
    background:rgba(255,255,255,.07);

    font-size:.68rem;
    font-weight:700;
}

.s360-hero-pill i{
    color:#9EC2FF;
}

/* HERO METRICS */
.s360-hero-metrics{
    display:grid;
    grid-template-columns:repeat(4,minmax(92px,1fr));
    gap:9px;
    min-width:510px;
}

.s360-hero-metric{
    min-height:108px;
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

.s360-hero-metric:hover{
    transform:translateY(-4px);
    border-color:rgba(255,255,255,.20);
    background:
        linear-gradient(145deg,rgba(255,255,255,.17),rgba(255,255,255,.07));
}

.s360-hero-metric i{
    width:31px;
    height:31px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin-bottom:13px;

    border-radius:10px;

    color:#D5E6FF;
    background:rgba(255,255,255,.10);

    font-size:.78rem;
}

.s360-hero-metric strong{
    display:block;
    color:#fff;

    font-family:"Manrope","Inter",sans-serif;
    font-size:1.35rem;
    line-height:1;
    font-weight:800;
    letter-spacing:-.55px;
}

.s360-hero-metric span{
    display:block;
    margin-top:5px;

    color:rgba(255,255,255,.58);

    font-size:.61rem;
    line-height:1.25;
    font-weight:700;
}

/* ============================================================
   TOOLBAR SUPERIOR
============================================================ */
.s360-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    padding:13px 15px;
    margin-bottom:16px;

    border:1px solid var(--s360-line);
    border-radius:18px;

    background:rgba(255,255,255,.92);
    box-shadow:var(--s360-shadow-soft);
    backdrop-filter:blur(12px);
}

.s360-toolbar-copy{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
}

.s360-toolbar-icon{
    width:38px;
    height:38px;
    flex:0 0 38px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:12px;

    color:var(--s360-primary);
    background:#EDF4FF;

    font-size:.9rem;
}

.s360-toolbar-copy strong{
    display:block;
    color:var(--s360-text);

    font-size:.79rem;
    font-weight:800;
}

.s360-toolbar-copy span{
    display:block;
    margin-top:2px;

    color:var(--s360-soft);

    font-size:.66rem;
    font-weight:600;
}

/* ============================================================
   BOTONES
============================================================ */
.s360-btn{
    min-height:43px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;

    padding:9px 15px;

    border-radius:12px;

    font-size:.74rem;
    font-weight:800;

    transition:
        transform .18s ease,
        box-shadow .18s ease,
        background .18s ease,
        border-color .18s ease;
}

.s360-btn-primary{
    border:0;
    color:#fff !important;

    background:
        linear-gradient(
            135deg,
            var(--s360-primary-3),
            var(--s360-primary-2) 48%,
            var(--s360-primary)
        );

    box-shadow:
        0 11px 23px rgba(32,66,127,.22);
}

.s360-btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:
        0 16px 30px rgba(32,66,127,.29);
}

.s360-btn-soft{
    border:1px solid #D7E2F2;
    color:var(--s360-primary) !important;
    background:#fff;
}

.s360-btn-soft:hover{
    transform:translateY(-1px);
    border-color:#BFD2EC;
    background:#F5F9FF;
}

/* ============================================================
   MAIN GRID
============================================================ */
.s360-main-grid{
    display:grid;
    grid-template-columns:minmax(0,1.45fr) minmax(300px,.55fr);
    gap:16px;
    align-items:start;
    margin-bottom:16px;
}

/* ============================================================
   CARD GENERAL
============================================================ */
.s360-card{
    position:relative;
    overflow:hidden;

    border:1px solid var(--s360-line);
    border-radius:var(--s360-r-xl);

    background:rgba(255,255,255,.96);

    box-shadow:var(--s360-shadow-soft);

    transition:
        box-shadow .22s ease,
        border-color .22s ease;
}

.s360-card:hover{
    border-color:#D9E3F1;
    box-shadow:
        0 18px 48px rgba(15,23,42,.09);
}

.s360-card-header{
    min-height:71px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    padding:15px 18px;

    border-bottom:1px solid #EDF0F5;

    background:
        radial-gradient(280px 100px at 5% 0%,rgba(79,140,255,.055),transparent 72%),
        linear-gradient(180deg,#FFFFFF,#FBFCFF);
}

.s360-card-title-wrap{
    display:flex;
    align-items:center;
    gap:11px;
}

.s360-card-icon{
    width:40px;
    height:40px;
    flex:0 0 40px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:13px;

    color:var(--s360-primary);
    background:#EDF4FF;

    font-size:.92rem;
}

.s360-card-title{
    margin:0;

    color:#182230;

    font-family:"Manrope","Inter",sans-serif;
    font-size:.96rem;
    line-height:1.2;
    font-weight:800;
    letter-spacing:-.2px;
}

.s360-card-subtitle{
    margin:3px 0 0;

    color:var(--s360-soft);

    font-size:.66rem;
    font-weight:600;
}

.s360-card-body{
    padding:18px;
}

/* ============================================================
   FORM
============================================================ */
.s360-section{
    padding:16px;

    border:1px solid #E9EDF4;
    border-radius:18px;

    background:
        linear-gradient(145deg,#FFFFFF,#FBFCFF);
}

.s360-section + .s360-section{
    margin-top:13px;
}

.s360-section-heading{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    margin-bottom:14px;
}

.s360-section-heading-left{
    display:flex;
    align-items:center;
    gap:9px;
}

.s360-section-dot{
    width:9px;
    height:9px;

    border-radius:50%;

    background:
        linear-gradient(
            135deg,
            var(--s360-primary-3),
            var(--s360-primary)
        );

    box-shadow:
        0 0 0 4px rgba(79,140,255,.09);
}

.s360-section-heading h3{
    margin:0;

    color:var(--s360-text);

    font-size:.79rem;
    font-weight:800;
}

.s360-required{
    color:var(--s360-soft);

    font-size:.62rem;
    font-weight:600;
}

/* floating fields */
.form-floating>.form-control,
.form-floating>.form-select{
    min-height:58px;

    border:1px solid #D9E0EA;
    border-radius:14px;

    color:var(--s360-text-2);
    background:#FBFCFE;

    font-size:.80rem;
    font-weight:650;

    box-shadow:none !important;

    transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background .18s ease;
}

.form-floating>.form-control:hover,
.form-floating>.form-select:hover{
    border-color:#BCC8D9;
    background:#fff;
}

.form-floating>.form-control:focus,
.form-floating>.form-select:focus{
    border-color:var(--s360-primary-3) !important;
    background:#fff;

    box-shadow:
        0 0 0 4px rgba(79,140,255,.10) !important;
}

.form-floating>label{
    color:#667085;
    font-size:.77rem;
    font-weight:650;
}

/* ============================================================
   IDENTIDAD VISUAL
============================================================ */
.s360-visual-card{
    position:sticky;
    top:84px;
}

.s360-visual-preview{
    position:relative;
    overflow:hidden;

    min-height:180px;

    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;

    padding:22px;

    border:1px dashed #CBD8EA;
    border-radius:18px;

    background:
        radial-gradient(220px 130px at 50% 0%,rgba(79,140,255,.08),transparent 72%),
        linear-gradient(180deg,#FBFDFF,#F5F8FC);
}

.s360-visual-preview::before{
    content:"";
    position:absolute;

    width:130px;
    height:130px;

    right:-55px;
    bottom:-65px;

    border:1px solid rgba(47,103,196,.09);
    border-radius:50%;

    box-shadow:
        0 0 0 26px rgba(47,103,196,.025);
}

.s360-visual-icon{
    width:66px;
    height:66px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin-bottom:12px;

    border:1px solid #D9E6F7;
    border-radius:20px;

    color:var(--s360-primary);

    background:
        linear-gradient(145deg,#EFF5FF,#FFFFFF);

    box-shadow:
        0 12px 28px rgba(32,66,127,.10);

    font-size:1.35rem;
}

.s360-visual-preview strong{
    color:var(--s360-text);

    font-size:.79rem;
    font-weight:800;
}

.s360-visual-preview span{
    max-width:260px;
    margin-top:5px;

    color:var(--s360-soft);

    text-align:center;

    font-size:.64rem;
    line-height:1.5;
    font-weight:600;
}

.s360-upload-shell{
    overflow:hidden;

    margin-top:12px;

    border:1px solid #E1E7F0;
    border-radius:16px;

    background:#fff;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.9);
}

#my-awesome-dropzone{
    min-height:0 !important;
    padding:0 !important;
}

#ifm{
    display:block;

    width:100% !important;
    height:220px;

    border:0 !important;

    background:#fff;
}

/* info */
.s360-mini-info{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:9px;
    margin-top:12px;
}

.s360-mini-info-item{
    padding:11px;

    border:1px solid #E7ECF3;
    border-radius:13px;

    background:#FBFCFE;
}

.s360-mini-info-item i{
    color:var(--s360-primary-2);
    font-size:.72rem;
}

.s360-mini-info-item strong{
    display:block;
    margin-top:7px;

    color:var(--s360-text-2);

    font-size:.66rem;
    font-weight:800;
}

.s360-mini-info-item span{
    display:block;
    margin-top:2px;

    color:var(--s360-soft);

    font-size:.60rem;
    line-height:1.35;
    font-weight:600;
}

/* ============================================================
   ACTION BAR
============================================================ */
.s360-action-bar{
    position:sticky;
    bottom:12px;
    z-index:20;

    margin-top:15px;
}

.s360-action-inner{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    padding:12px;

    border:1px solid rgba(216,225,238,.94);
    border-radius:17px;

    background:rgba(255,255,255,.91);

    box-shadow:
        0 15px 35px rgba(15,23,42,.11);

    backdrop-filter:blur(16px);
}

.s360-action-copy{
    display:flex;
    align-items:center;
    gap:9px;
    min-width:0;
}

.s360-action-state{
    width:34px;
    height:34px;
    flex:0 0 34px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:11px;

    color:#07845E;
    background:#ECFDF5;

    font-size:.76rem;
}

.s360-action-copy strong{
    display:block;

    color:var(--s360-text-2);

    font-size:.69rem;
    font-weight:800;
}

.s360-action-copy span{
    display:block;
    margin-top:2px;

    color:var(--s360-soft);

    font-size:.61rem;
    font-weight:600;
}

/* ============================================================
   DIRECTORY / TABLE
============================================================ */
.s360-directory{
    overflow:hidden;

    border:1px solid var(--s360-line);
    border-radius:var(--s360-r-xl);

    background:#fff;

    box-shadow:var(--s360-shadow);
}

.s360-directory-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    padding:18px;

    border-bottom:1px solid #EDF0F5;

    background:
        radial-gradient(360px 120px at 5% 0%,rgba(79,140,255,.06),transparent 70%),
        linear-gradient(180deg,#FFFFFF,#FBFCFF);
}

.s360-directory-head-left{
    display:flex;
    align-items:center;
    gap:11px;
}

.s360-directory-icon{
    width:43px;
    height:43px;
    flex:0 0 43px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:13px;

    color:#fff;

    background:
        linear-gradient(
            135deg,
            var(--s360-primary-3),
            var(--s360-primary)
        );

    box-shadow:
        0 10px 22px rgba(32,66,127,.20);

    font-size:.92rem;
}

.s360-directory h2{
    margin:0;

    color:var(--s360-text);

    font-family:"Manrope","Inter",sans-serif;
    font-size:1rem;
    font-weight:800;
}

.s360-directory p{
    margin:3px 0 0;

    color:var(--s360-soft);

    font-size:.66rem;
    font-weight:600;
}

.s360-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;

    min-height:31px;
    padding:6px 10px;

    border:1px solid #DCE8FA;
    border-radius:999px;

    color:#265EA9;
    background:#EEF5FF;

    font-size:.65rem;
    font-weight:800;
}

.s360-table-body{
    padding:15px;
}

/* DataTables top */
.dataTables_wrapper{
    width:100% !important;
    color:var(--s360-muted);
    font-size:.71rem;
}

.dataTables_wrapper .row{
    margin-left:0 !important;
    margin-right:0 !important;
    align-items:center;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter{
    margin-bottom:13px;
}

.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label{
    color:#667085;
    font-size:.67rem;
    font-weight:700;
}

.dataTables_wrapper .dataTables_length select{
    min-width:72px;
    min-height:38px;
    margin:0 5px;

    border:1px solid #D7DEE9;
    border-radius:10px;

    color:#344054;
    background:#fff;

    font-size:.70rem;
    font-weight:700;
}

.dataTables_wrapper .dataTables_filter input{
    width:min(270px,100%);
    min-height:39px;

    margin-left:8px;
    padding:0 12px;

    border:1px solid #D7DEE9;
    border-radius:11px;

    color:#344054;
    background:#fff;

    outline:none;

    font-size:.71rem;

    transition:
        border-color .18s ease,
        box-shadow .18s ease;
}

.dataTables_wrapper .dataTables_filter input:focus{
    border-color:var(--s360-primary-3);
    box-shadow:
        0 0 0 4px rgba(79,140,255,.10);
}

/* table */
.s360-directory table{
    width:100% !important;
    margin:0 !important;

    border-collapse:separate !important;
    border-spacing:0 7px !important;

    table-layout:auto;
}

.s360-directory table thead th{
    padding:10px 12px !important;

    border:0 !important;

    color:#667085 !important;
    background:transparent !important;

    font-size:.63rem !important;
    font-weight:800 !important;
    letter-spacing:.45px;
    text-transform:uppercase;

    white-space:nowrap !important;
}

.s360-directory table tbody td{
    padding:11px 12px !important;

    border-top:1px solid #E9EDF4 !important;
    border-bottom:1px solid #E9EDF4 !important;

    color:#344054 !important;
    background:#fff !important;

    font-size:.71rem !important;
    line-height:1.45;
    font-weight:600;

    vertical-align:middle !important;

    transition:
        background .18s ease,
        border-color .18s ease,
        box-shadow .18s ease,
        transform .18s ease;
}

.s360-directory table tbody td:first-child{
    border-left:1px solid #E9EDF4 !important;
    border-radius:13px 0 0 13px;
}

.s360-directory table tbody td:last-child{
    border-right:1px solid #E9EDF4 !important;
    border-radius:0 13px 13px 0;
}

.s360-directory table tbody tr{
    transition:transform .18s ease;
}

.s360-directory table tbody tr:hover{
    transform:translateY(-2px);
}

.s360-directory table tbody tr:hover td{
    border-color:#DCE7F6 !important;
    background:
        linear-gradient(90deg,#F7FAFF,#FFFFFF) !important;

    box-shadow:
        0 9px 23px rgba(15,23,42,.05);
}

.s360-party-name{
    display:flex;
    align-items:center;
    gap:10px;

    min-width:190px;
}

.s360-party-mark{
    width:8px;
    height:34px;
    flex:0 0 8px;

    border-radius:999px;

    background:
        linear-gradient(
            180deg,
            var(--s360-primary-3),
            var(--s360-primary)
        );

    opacity:.85;
}

.s360-party-name strong{
    color:#1D2939;
    font-size:.73rem;
    font-weight:800;
}

/* logo */
.logo-mini{
    width:54px;
    height:54px;

    border:1px solid #E0E6EF;
    border-radius:13px;

    object-fit:contain;

    background:#fff;

    box-shadow:
        0 7px 17px rgba(15,23,42,.07);

    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.logo-mini:hover{
    transform:scale(1.07);
    box-shadow:
        0 12px 24px rgba(15,23,42,.11);
}

/* status tags */
.s360-cell-tag{
    display:inline-flex;
    align-items:center;
    gap:5px;

    min-height:27px;
    padding:5px 8px;

    border:1px solid #E1E8F1;
    border-radius:8px;

    color:#475467;
    background:#F8FAFC;

    font-size:.61rem;
    font-weight:750;
}

.s360-cell-tag i{
    color:#5C7FAE;
    font-size:.60rem;
}

/* action buttons */
.s360-actions-cell{
    display:flex;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
}

.s360-icon-btn{
    width:35px;
    height:35px;

    display:inline-flex;
    align-items:center;
    justify-content:center;

    padding:0;

    border-radius:10px !important;

    transition:
        transform .18s ease,
        box-shadow .18s ease,
        filter .18s ease;
}

.s360-icon-btn:hover{
    transform:translateY(-2px);
}

.s360-edit-btn{
    border:0 !important;

    color:#fff !important;

    background:
        linear-gradient(
            135deg,
            #4F8CFF,
            #2563B9
        ) !important;

    box-shadow:
        0 8px 16px rgba(37,99,185,.17);
}

.s360-delete-btn{
    border:0 !important;

    color:#fff !important;

    background:
        linear-gradient(
            135deg,
            #F36A6A,
            #D83B47
        ) !important;

    box-shadow:
        0 8px 16px rgba(216,59,71,.16);
}

.s360-icon-btn:hover{
    box-shadow:
        0 12px 22px rgba(15,23,42,.16);
}

/* pagination */
.dataTables_wrapper .dataTables_info{
    padding-top:13px !important;

    color:#98A2B3 !important;

    font-size:.65rem !important;
    font-weight:600;
}

.dataTables_wrapper .dataTables_paginate{
    display:flex;
    justify-content:flex-end;
    gap:4px;

    padding-top:9px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button{
    min-width:34px;
    height:34px;

    display:inline-flex !important;
    align-items:center;
    justify-content:center;

    margin:0 2px !important;
    padding:0 9px !important;

    border:1px solid transparent !important;
    border-radius:9px !important;

    color:#667085 !important;
    background:transparent !important;

    font-size:.67rem;
    font-weight:800;

    box-shadow:none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover{
    border-color:#DCE8FA !important;

    color:#3168C8 !important;
    background:#EFF5FF !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
    border-color:transparent !important;

    color:#fff !important;

    background:
        linear-gradient(
            135deg,
            var(--s360-primary-3),
            var(--s360-primary)
        ) !important;

    box-shadow:
        0 8px 18px rgba(32,66,127,.20) !important;
}

/* empty */
.s360-empty-row{
    padding:45px 20px !important;
    text-align:center;
}

.s360-empty-inline{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
}

.s360-empty-inline i{
    width:52px;
    height:52px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin-bottom:10px;

    border-radius:16px;

    color:var(--s360-primary);
    background:#EEF5FF;

    font-size:1.1rem;
}

.s360-empty-inline strong{
    color:#344054;
    font-size:.76rem;
}

.s360-empty-inline span{
    margin-top:3px;

    color:#98A2B3;
    font-size:.64rem;
}

/* ============================================================
   SWEETALERT
============================================================ */
.swal2-popup{
    border-radius:22px !important;

    box-shadow:
        0 28px 70px rgba(15,23,42,.20) !important;
}

.swal2-title{
    color:var(--s360-text) !important;
    font-family:"Manrope","Inter",sans-serif !important;
    font-weight:800 !important;
}

.swal2-confirm,
.swal2-cancel{
    border-radius:11px !important;
    font-weight:800 !important;
}

/* ============================================================
   RESPONSIVE
============================================================ */
@media (max-width:1300px){
    .s360-hero-grid{
        grid-template-columns:1fr;
    }

    .s360-hero-metrics{
        min-width:0;
        width:100%;
    }

    .s360-main-grid{
        grid-template-columns:minmax(0,1.2fr) minmax(280px,.8fr);
    }
}

@media (max-width:991px){
    .s360-shell{
        padding:0 13px;
    }

    .s360-hero{
        padding:23px;
    }

    .s360-main-grid{
        grid-template-columns:1fr;
    }

    .s360-visual-card{
        position:static;
    }

    .s360-upload-shell{
        max-width:620px;
    }
}

@media (max-width:767px){
    .content{
        padding-top:12px !important;
    }

    .s360-shell{
        padding:0 10px;
    }

    .s360-hero{
        min-height:0;
        padding:20px 17px;
        border-radius:22px;
    }

    .s360-hero h1{
        font-size:1.8rem;
    }

    .s360-hero p{
        font-size:.80rem;
    }

    .s360-hero-metrics{
        grid-template-columns:repeat(2,1fr);
    }

    .s360-toolbar{
        align-items:flex-start;
        flex-direction:column;
    }

    .s360-toolbar .s360-btn{
        width:100%;
    }

    .s360-card{
        border-radius:19px;
    }

    .s360-card-header{
        padding:14px;
    }

    .s360-card-body{
        padding:14px;
    }

    .s360-section{
        padding:13px;
    }

    .s360-action-inner{
        align-items:stretch;
        flex-direction:column;
    }

    .s360-action-inner > .d-flex{
        width:100%;
    }

    .s360-action-inner .s360-btn{
        flex:1;
    }

    .s360-directory{
        border-radius:19px;
    }

    .s360-directory-head{
        align-items:flex-start;
        padding:14px;
    }

    .s360-table-body{
        padding:10px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter{
        text-align:left !important;
    }

    .dataTables_wrapper .dataTables_filter input{
        width:100%;
        margin:6px 0 0;
    }

    .dataTables_wrapper .dataTables_paginate{
        justify-content:center;
        flex-wrap:wrap;
    }

    .table-responsive{
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
    }

    .s360-directory table{
        min-width:880px;
    }
}

@media (max-width:480px){
    .s360-hero-metrics{
        gap:7px;
    }

    .s360-hero-metric{
        min-height:95px;
        padding:12px;
    }

    .s360-hero-metric strong{
        font-size:1.17rem;
    }

    .s360-hero-metric span{
        font-size:.57rem;
    }

    .s360-mini-info{
        grid-template-columns:1fr;
    }

    .s360-action-copy{
        display:none;
    }

    .s360-action-inner{
        padding:9px;
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

<div class="content">
    <div class="s360-shell">

        <!-- =====================================================
             HERO
        ====================================================== -->
        <section class="s360-hero" aria-label="Resumen de partidos políticos">

            <div class="s360-hero-grid">

                <div>

                    <div class="s360-eyebrow">
                        <span class="s360-live-dot"></span>
                        Estadística360 · Gestión Política
                    </div>

                    <h1>
                        Partidos <span>Políticos</span>
                    </h1>

                    <p>
                        Administra el directorio institucional de partidos políticos,
                        representantes, resoluciones e identidad visual desde una
                        experiencia centralizada y preparada para análisis electoral.
                    </p>

                    <div class="s360-hero-actions">
                        <span class="s360-hero-pill">
                            <i class="fas fa-shield-alt"></i>
                            Acceso por permisos
                        </span>

                        <span class="s360-hero-pill">
                            <i class="fas fa-database"></i>
                            Directorio centralizado
                        </span>

                        <span class="s360-hero-pill">
                            <i class="fas fa-image"></i>
                            Identidad visual
                        </span>
                    </div>

                </div>


                <div class="s360-hero-metrics">

                    <div class="s360-hero-metric">
                        <i class="fas fa-flag"></i>
                        <strong><?= (int)$totalPartidos ?></strong>
                        <span>Partidos registrados</span>
                    </div>

                    <div class="s360-hero-metric">
                        <i class="fas fa-image"></i>
                        <strong><?= (int)$totalConLogo ?></strong>
                        <span>Con logo cargado</span>
                    </div>

                    <div class="s360-hero-metric">
                        <i class="fas fa-file-signature"></i>
                        <strong><?= (int)$totalConResolucion ?></strong>
                        <span>Con resolución</span>
                    </div>

                    <div class="s360-hero-metric">
                        <i class="fas fa-envelope"></i>
                        <strong><?= (int)$totalConEmail ?></strong>
                        <span>Con contacto</span>
                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             TOOLBAR
        ====================================================== -->
        <section class="s360-toolbar">

            <div class="s360-toolbar-copy">

                <div class="s360-toolbar-icon">
                    <i class="fas fa-compass"></i>
                </div>

                <div>
                    <strong>Directorio político institucional</strong>
                    <span>
                        Registra nuevos partidos o administra los existentes.
                    </span>
                </div>

            </div>

            <?php if ($create): ?>
                <button
                    type="button"
                    class="s360-btn s360-btn-primary"
                    id="btnNuevoPartido">

                    <i class="fas fa-plus"></i>
                    Nuevo partido

                </button>
            <?php endif; ?>

        </section>


        <!-- =====================================================
             FORMULARIO + IDENTIDAD VISUAL
        ====================================================== -->
        <form
            id="formpartidospoliticos"
            role="form"
            autocomplete="off">

            <input type="hidden" name="op" id="op">
            <input type="hidden" name="idPartido" id="idPartido">


            <div class="s360-main-grid">


                <!-- =========================
                     INFORMACIÓN
                ========================== -->
                <section class="s360-card" id="s360FormCard">

                    <div class="s360-card-header">

                        <div class="s360-card-title-wrap">

                            <div class="s360-card-icon">
                                <i class="fas fa-flag"></i>
                            </div>

                            <div>
                                <h2 class="s360-card-title">
                                    Información del partido
                                </h2>

                                <p class="s360-card-subtitle">
                                    Datos principales de identificación institucional.
                                </p>
                            </div>

                        </div>

                        <span class="s360-badge">
                            <i class="fas fa-asterisk"></i>
                            Campos requeridos
                        </span>

                    </div>


                    <div class="s360-card-body">


                        <div class="s360-section">

                            <div class="s360-section-heading">

                                <div class="s360-section-heading-left">

                                    <span class="s360-section-dot"></span>

                                    <h3>
                                        Identificación oficial
                                    </h3>

                                </div>

                                <span class="s360-required">
                                    Los campos con * son obligatorios
                                </span>

                            </div>


                            <div class="row g-3">

                                <div class="col-12">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="nombre_partido"
                                            name="nombre_partido"
                                            placeholder="Ingrese el nombre del partido"
                                            required>

                                        <label for="nombre_partido">
                                            Nombre del Partido
                                            <span class="text-danger">*</span>
                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="representante_legal"
                                            name="representante_legal"
                                            placeholder="Ingrese el nombre del representante legal"
                                            required>

                                        <label for="representante_legal">
                                            Representante Legal
                                            <span class="text-danger">*</span>
                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="resolucion"
                                            name="resolucion"
                                            placeholder="Ingrese la resolución de reconocimiento">

                                        <label for="resolucion">
                                            Resolución de Reconocimiento
                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="s360-section">

                            <div class="s360-section-heading">

                                <div class="s360-section-heading-left">

                                    <span class="s360-section-dot"></span>

                                    <h3>
                                        Recomendaciones de registro
                                    </h3>

                                </div>

                            </div>


                            <div class="s360-mini-info">

                                <div class="s360-mini-info-item">

                                    <i class="fas fa-spell-check"></i>

                                    <strong>
                                        Nombre institucional
                                    </strong>

                                    <span>
                                        Utiliza la denominación oficial del partido.
                                    </span>

                                </div>


                                <div class="s360-mini-info-item">

                                    <i class="fas fa-user-tie"></i>

                                    <strong>
                                        Representante
                                    </strong>

                                    <span>
                                        Registra el nombre completo del representante legal.
                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- ACTION BAR -->
                        <div class="s360-action-bar">

                            <div class="s360-action-inner">

                                <div class="s360-action-copy">

                                    <div class="s360-action-state">
                                        <i class="fas fa-check"></i>
                                    </div>

                                    <div>
                                        <strong>
                                            Formulario listo
                                        </strong>

                                        <span>
                                            Guarda o actualiza la información registrada.
                                        </span>
                                    </div>

                                </div>


                                <div class="d-flex align-items-center gap-2">

                                    <button
                                        type="button"
                                        onclick="UTIL.clearForm('formpartidospoliticos');"
                                        class="s360-btn s360-btn-soft">

                                        <i class="fas fa-rotate-left"></i>
                                        Limpiar

                                    </button>


                                    <?php if ($create && $edit): ?>

                                        <button
                                            class="s360-btn s360-btn-primary"
                                            type="button"
                                            onclick="PARTIDOS_POLITICOS.validateData();">

                                            <i class="fas fa-floppy-disk"></i>
                                            Guardar partido

                                        </button>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>


                    </div>

                </section>


                <!-- =========================
                     IDENTIDAD VISUAL
                ========================== -->
                <aside class="s360-card s360-visual-card">

                    <div class="s360-card-header">

                        <div class="s360-card-title-wrap">

                            <div class="s360-card-icon">
                                <i class="fas fa-image"></i>
                            </div>

                            <div>
                                <h2 class="s360-card-title">
                                    Identidad visual
                                </h2>

                                <p class="s360-card-subtitle">
                                    Logo utilizado en el sistema.
                                </p>
                            </div>

                        </div>

                    </div>


                    <div class="s360-card-body">


                        <div class="s360-visual-preview">

                            <div class="s360-visual-icon">
                                <i class="fas fa-landmark"></i>
                            </div>

                            <strong>
                                Logo institucional
                            </strong>

                            <span>
                                Utiliza preferiblemente archivos PNG o JPG
                                con fondo limpio y buena resolución.
                            </span>

                        </div>


                        <div class="s360-upload-shell">

                            <div
                                class="dropzone dropzone-multiple p-0"
                                id="my-awesome-dropzone"
                                data-dropzone="data-dropzone">

                                <iframe
                                    id="ifm"
                                    name="ifm"
                                    src="upload.php"
                                    width="100%"
                                    height="220"
                                    scrolling="no"
                                    frameborder="0"
                                    title="Carga del logo del partido">
                                </iframe>

                            </div>

                        </div>


                        <div class="s360-mini-info">

                            <div class="s360-mini-info-item">

                                <i class="fas fa-expand"></i>

                                <strong>
                                    Buena resolución
                                </strong>

                                <span>
                                    Evita logos pixelados o demasiado pequeños.
                                </span>

                            </div>


                            <div class="s360-mini-info-item">

                                <i class="fas fa-file-image"></i>

                                <strong>
                                    PNG / JPG
                                </strong>

                                <span>
                                    Formatos recomendados para mejor compatibilidad.
                                </span>

                            </div>

                        </div>


                    </div>

                </aside>

            </div>

        </form>


        <!-- =====================================================
             DIRECTORIO
        ====================================================== -->
        <section class="s360-directory">

            <div class="s360-directory-head">

                <div class="s360-directory-head-left">

                    <div class="s360-directory-icon">
                        <i class="fas fa-building-columns"></i>
                    </div>

                    <div>

                        <h2>
                            Directorio de partidos
                        </h2>

                        <p>
                            Consulta y administra los partidos políticos registrados.
                        </p>

                    </div>

                </div>


                <span class="s360-badge">

                    <i class="fas fa-database"></i>

                    <?= (int)$totalPartidos ?>
                    <?= $totalPartidos === 1 ? 'registro' : 'registros' ?>

                </span>

            </div>


            <div class="s360-table-body">

                <div class="table-responsive">

                    <table
                        id="dynamictable"
                        class="table table-sm fs-9 mb-0 align-middle">

                        <thead>

                            <tr>
                                <th>Acciones</th>
                                <th>Partido</th>
                                <th>Representante</th>
                                <th>Resolución</th>
                                <th>Email</th>
                                <th>Logo</th>
                            </tr>

                        </thead>


                        <tbody class="dt-list">

                        <?php if ($isvalid && count($arr) > 0): ?>

                            <?php foreach ($arr as $item): ?>

                                <?php
                                    $logoFile = $item['logo'] ?? '';

                                    $img = !empty($logoFile)
                                        ? "assets/img/admin/" . h($logoFile)
                                        : 'assets/img/generic/default.png';

                                    $nombre = $item['nombre_partido'] ?? '';
                                    $representante = $item['representante_legal'] ?? '';
                                    $resolucion = $item['resolucion'] ?? '';
                                    $email = $item['email_contacto'] ?? '';
                                ?>

                                <tr>

                                    <td>

                                        <div class="s360-actions-cell">

                                            <button
                                                type="button"
                                                class="btn s360-icon-btn s360-edit-btn"
                                                title="Editar partido"
                                                onclick="PARTIDOS_POLITICOS.editData(<?= (int)$item['id'] ?>)">

                                                <i class="uil uil-edit"></i>

                                            </button>


                                            <?php if ($permits): ?>

                                                <button
                                                    type="button"
                                                    class="btn s360-icon-btn s360-delete-btn btn-delete-partido"
                                                    title="Eliminar partido"
                                                    data-id="<?= (int)$item['id'] ?>"
                                                    data-nombre="<?= h($nombre) ?>">

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            <?php endif; ?>

                                        </div>

                                    </td>


                                    <td>

                                        <div class="s360-party-name">

                                            <span class="s360-party-mark"></span>

                                            <strong>
                                                <?= h($nombre) ?>
                                            </strong>

                                        </div>

                                    </td>


                                    <td>
                                        <?= h($representante) ?>
                                    </td>


                                    <td>

                                        <?php if (!empty($resolucion)): ?>

                                            <span class="s360-cell-tag">

                                                <i class="fas fa-file-signature"></i>

                                                <?= h($resolucion) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?php if (!empty($email)): ?>

                                            <span class="s360-cell-tag">

                                                <i class="fas fa-envelope"></i>

                                                <?= h($email) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <img
                                            class="logo-mini"
                                            src="<?= $img ?>"
                                            alt="Logo de <?= h($nombre) ?>"
                                            loading="lazy">

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="6"
                                    class="s360-empty-row">

                                    <div class="s360-empty-inline">

                                        <i class="fas fa-flag"></i>

                                        <strong>
                                            No hay partidos registrados
                                        </strong>

                                        <span>
                                            Utiliza el formulario superior para crear el primer registro.
                                        </span>

                                    </div>

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>


        <?php include './admin/include/footer.php'; ?>

    </div>
</div>


<!-- ============================================================
     SCRIPTS BASE
============================================================ -->
<?php include 'admin/include/gerenic_script.php'; ?>

<?php include './admin/include/generic_dataTables.php'; ?>

<script src="admin/js/partidos_politicos.js"></script>

<?php include 'admin/include/scriptsgober360.php'; ?>


<script>
/* ============================================================
   ESTADÍSTICA360 · MICROINTERACCIONES
============================================================ */

(function(){

    "use strict";


    /* --------------------------------------------------------
       NUEVO PARTIDO
    --------------------------------------------------------- */

    var btnNuevo =
        document.getElementById(
            "btnNuevoPartido"
        );


    if (btnNuevo) {

        btnNuevo.addEventListener(
            "click",
            function(){

                try {

                    UTIL.clearForm(
                        "formpartidospoliticos"
                    );

                } catch(e) {}


                var card =
                    document.getElementById(
                        "s360FormCard"
                    );


                if (card) {

                    card.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });

                }


                setTimeout(
                    function(){

                        var nombre =
                            document.getElementById(
                                "nombre_partido"
                            );

                        if (nombre) {
                            nombre.focus();
                        }

                    },
                    450
                );

            }
        );

    }


    /* --------------------------------------------------------
       AL EDITAR, LLEVAR AL FORMULARIO
    --------------------------------------------------------- */

    document.addEventListener(
        "click",
        function(e){

            var btn =
                e.target.closest(
                    ".s360-edit-btn"
                );


            if (!btn) return;


            setTimeout(
                function(){

                    var card =
                        document.getElementById(
                            "s360FormCard"
                        );


                    if (card) {

                        card.scrollIntoView({
                            behavior: "smooth",
                            block: "start"
                        });

                    }

                },
                160
            );

        }
    );


    /* --------------------------------------------------------
       CONFIRMACIÓN DE ELIMINACIÓN
    --------------------------------------------------------- */

    $(document).on(
        "click",
        ".btn-delete-partido",
        function(){

            const id =
                $(this).data("id");

            const nombre =
                $(this).data("nombre")
                || "Partido";


            if (
                !window.PARTIDOS_POLITICOS
                ||
                typeof window.PARTIDOS_POLITICOS.deleteData
                !== "function"
            ) {

                if (
                    typeof Swal
                    !== "undefined"
                ) {

                    Swal.fire({

                        icon: "info",

                        title:
                            "Acción no disponible",

                        text:
                            "No se encontró PARTIDOS_POLITICOS.deleteData(id). Revisa que partidos_politicos.js esté cargando correctamente."

                    });

                } else {

                    alert(
                        "No existe PARTIDOS_POLITICOS.deleteData(id)."
                    );

                }

                return;

            }


            if (
                typeof Swal
                !== "undefined"
            ) {

                Swal.fire({

                    title:
                        "¿Eliminar partido?",

                    html:
                        '<div style="text-align:left;">'
                        +
                        '<div style="font-size:.78rem;color:#667085;margin-bottom:8px;">'
                        +
                        'Estás a punto de eliminar:'
                        +
                        '</div>'
                        +
                        '<div style="font-weight:800;color:#101828;font-size:1rem;margin-bottom:8px;">'
                        +
                        nombre
                        +
                        '</div>'
                        +
                        '<div style="color:#b42318;font-size:.75rem;font-weight:700;">'
                        +
                        'Esta acción no se puede deshacer.'
                        +
                        '</div>'
                        +
                        '</div>',

                    icon:
                        "warning",

                    showCancelButton:
                        true,

                    confirmButtonText:
                        "Sí, eliminar",

                    cancelButtonText:
                        "Cancelar",

                    reverseButtons:
                        true,

                    focusCancel:
                        true,

                    confirmButtonColor:
                        "#D83B47",

                    cancelButtonColor:
                        "#667085"

                }).then(
                    function(r){

                        if (
                            r.isConfirmed
                        ) {

                            window
                                .PARTIDOS_POLITICOS
                                .deleteData(id);

                        }

                    }
                );

            } else {

                if (
                    confirm(
                        "¿Está seguro que desea eliminar este partido?\n\n"
                        +
                        nombre
                    )
                ) {

                    window
                        .PARTIDOS_POLITICOS
                        .deleteData(id);

                }

            }

        }
    );


    /* --------------------------------------------------------
       FALLBACK DE IMÁGENES
    --------------------------------------------------------- */

    document
        .querySelectorAll(
            ".logo-mini"
        )
        .forEach(
            function(img){

                img.addEventListener(
                    "error",
                    function(){

                        if (
                            img.dataset
                                .fallbackApplied
                            === "1"
                        ) {
                            return;
                        }


                        img.dataset
                            .fallbackApplied
                            =
                            "1";


                        img.src =
                            "assets/img/generic/default.png";

                    }
                );

            }
        );

})();
</script>

</body>
</html>
