<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Votantes.php';
include './admin/classes/Departamento.php';

// Variables de configuración - logo, municipio, departamento...
include './admin/include/generic_info_configuracion.php';

$view = (SessionData::administrador() || SessionData::superAdministrador()) ? true : false;

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Departamentos
$departamentos = Departamento::getAll(null);
$departamentosResponse = $departamentos['output']['response'] ?? [];

$optionDep = "";

foreach ($departamentosResponse as $dep) {

    $codigo = htmlspecialchars(
        $dep['codigo_departamento'],
        ENT_QUOTES,
        'UTF-8'
    );

    $nombre = htmlspecialchars(
        $dep['departamento'],
        ENT_QUOTES,
        'UTF-8'
    );

    $optionDep .=
        "<option value='{$codigo}'>{$codigo} - {$nombre}</option>";
}

// Votantes
$arrResponse = Votantes::getAll(null);

$isvalid =
    $arrResponse['output']['valid']
    ?? false;

$arr =
    $arrResponse['output']['response']
    ?? [];

$modulo = 'Votantes';

function h($s){
    return htmlspecialchars(
        (string)$s,
        ENT_QUOTES,
        'UTF-8'
    );
}

// KPIs
$totalVotantes = is_array($arr) ? count($arr) : 0;
$totalActivos = 0;
$departamentosUnicos = [];
$municipiosUnicos = [];

if (is_array($arr)) {

    foreach ($arr as $v) {

        if (
            strtolower(
                trim(
                    (string)(
                        $v['estado']
                        ?? ''
                    )
                )
            )
            ===
            'activo'
        ) {
            $totalActivos++;
        }

        $dep =
            trim(
                (string)(
                    $v['codigo_departamento']
                    ?? ''
                )
            );

        if ($dep !== '') {
            $departamentosUnicos[$dep] = true;
        }

        $mun =
            trim(
                (string)(
                    $v['codigo_municipio']
                    ?? ''
                )
            );

        if ($mun !== '') {
            $municipiosUnicos[$mun] = true;
        }
    }
}

$totalDepartamentos =
    count($departamentosUnicos);

$totalMunicipios =
    count($municipiosUnicos);

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap"
    rel="stylesheet">

<style>
/* ============================================================
   ESTADÍSTICA360 · VOTER INTELLIGENCE CENTER
============================================================ */

:root{
    --v360-navy:#0B1F43;
    --v360-navy-2:#102A56;
    --v360-brand:#20427F;
    --v360-brand-2:#3168C8;
    --v360-blue:#4F8CFF;
    --v360-cyan:#0EA5E9;

    --v360-success:#12B981;
    --v360-warning:#F59E0B;
    --v360-danger:#E5484D;
    --v360-violet:#7C5CFC;

    --v360-page:#F3F6FB;
    --v360-card:#FFFFFF;
    --v360-card-soft:#F8FAFD;

    --v360-text:#101828;
    --v360-text-2:#344054;
    --v360-muted:#667085;
    --v360-soft:#98A2B3;

    --v360-line:#E6EBF2;

    --v360-r-xxl:30px;
    --v360-r-xl:24px;
    --v360-r-lg:18px;
    --v360-r-md:14px;

    --v360-shadow:
        0 22px 60px
        rgba(15,23,42,.09);

    --v360-shadow-soft:
        0 12px 32px
        rgba(15,23,42,.065);

    --v360-shadow-hover:
        0 28px 70px
        rgba(15,23,42,.13);
}

*{
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body.v360-page{
    margin:0;

    background:
        radial-gradient(
            860px 460px at 3% -4%,
            rgba(49,104,200,.12),
            transparent 64%
        ),
        radial-gradient(
            760px 430px at 105% 8%,
            rgba(14,165,233,.075),
            transparent 64%
        ),
        linear-gradient(
            180deg,
            #F7F9FC 0%,
            #F2F5FA 100%
        );

    color:
        var(--v360-text);

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
}

body.v360-page::before{
    content:"";

    position:fixed;
    inset:0;

    z-index:-1;

    pointer-events:none;

    opacity:.33;

    background-image:
        linear-gradient(
            rgba(32,66,127,.022) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(32,66,127,.022) 1px,
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

/* ============================================================
   PAGE
============================================================ */

.content{
    padding-top:
        18px !important;

    padding-bottom:
        38px !important;
}

.v360-shell{
    width:100%;

    max-width:1660px;

    margin:
        0 auto;

    padding:
        0 18px;
}

/* ============================================================
   HERO
============================================================ */

.v360-hero{
    position:relative;
    isolation:isolate;

    overflow:hidden;

    min-height:216px;

    padding:
        28px 30px;

    margin-bottom:
        16px;

    border:
        1px solid
        rgba(255,255,255,.12);

    border-radius:
        var(--v360-r-xxl);

    color:#fff;

    background:
        radial-gradient(
            520px 260px at 10% 2%,
            rgba(79,140,255,.34),
            transparent 65%
        ),
        radial-gradient(
            470px 250px at 92% 10%,
            rgba(14,165,233,.22),
            transparent 66%
        ),
        linear-gradient(
            135deg,
            #173D79 0%,
            #102A56 45%,
            #09172F 100%
        );

    box-shadow:
        0 28px 75px
        rgba(12,31,66,.24);
}

.v360-hero::before{
    content:"";

    position:absolute;

    width:410px;
    height:410px;

    right:-145px;
    top:-205px;

    z-index:-1;

    border:
        1px solid
        rgba(255,255,255,.08);

    border-radius:50%;

    box-shadow:
        0 0 0 42px
        rgba(255,255,255,.022),
        0 0 0 86px
        rgba(255,255,255,.016),
        0 0 0 128px
        rgba(255,255,255,.011);
}

.v360-hero-grid{
    display:grid;

    grid-template-columns:
        minmax(0,1fr)
        auto;

    gap:28px;

    align-items:center;
}

.v360-eyebrow{
    display:inline-flex;

    align-items:center;

    gap:8px;

    min-height:32px;

    padding:
        7px 11px;

    margin-bottom:
        13px;

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

    font-size:.68rem;

    font-weight:800;

    letter-spacing:.62px;

    text-transform:uppercase;
}

.v360-live{
    width:7px;
    height:7px;

    border-radius:50%;

    background:#5DE4A0;

    box-shadow:
        0 0 0 5px
        rgba(93,228,160,.11),
        0 0 16px
        rgba(93,228,160,.48);
}

.v360-hero h1{
    margin:0;

    color:#fff;

    font-family:
        "Manrope",
        "Inter",
        sans-serif;

    font-size:
        clamp(
            1.8rem,
            3vw,
            2.9rem
        );

    line-height:1.05;

    font-weight:800;

    letter-spacing:-1.4px;
}

.v360-hero h1 span{
    color:#A9C7FF;
}

.v360-hero p{
    max-width:790px;

    margin:
        10px 0 0;

    color:
        rgba(255,255,255,.70);

    font-size:.91rem;

    line-height:1.65;

    font-weight:500;
}

.v360-hero-pills{
    display:flex;

    gap:8px;

    flex-wrap:wrap;

    margin-top:18px;
}

.v360-hero-pill{
    display:inline-flex;

    align-items:center;

    gap:7px;

    min-height:35px;

    padding:
        8px 11px;

    border:
        1px solid
        rgba(255,255,255,.10);

    border-radius:11px;

    color:
        rgba(255,255,255,.84);

    background:
        rgba(255,255,255,.07);

    font-size:.67rem;

    font-weight:700;
}

.v360-hero-pill i{
    color:#9EC2FF;
}

/* ============================================================
   HERO KPIs
============================================================ */

.v360-hero-metrics{
    display:grid;

    grid-template-columns:
        repeat(
            4,
            minmax(92px,1fr)
        );

    gap:9px;

    min-width:520px;
}

.v360-hero-metric{
    min-height:109px;

    padding:14px;

    border:
        1px solid
        rgba(255,255,255,.12);

    border-radius:17px;

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

.v360-hero-metric:hover{
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

.v360-hero-metric i{
    width:31px;
    height:31px;

    display:flex;

    align-items:center;

    justify-content:center;

    margin-bottom:13px;

    border-radius:10px;

    color:#D5E6FF;

    background:
        rgba(255,255,255,.10);

    font-size:.78rem;
}

.v360-hero-metric strong{
    display:block;

    color:#fff;

    font-family:
        "Manrope",
        "Inter",
        sans-serif;

    font-size:1.36rem;

    line-height:1;

    font-weight:800;

    letter-spacing:-.55px;
}

.v360-hero-metric span{
    display:block;

    margin-top:5px;

    color:
        rgba(255,255,255,.58);

    font-size:.60rem;

    line-height:1.25;

    font-weight:700;
}

/* ============================================================
   TOOLBAR
============================================================ */

.v360-toolbar{
    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:12px;

    padding:
        13px 15px;

    margin-bottom:
        16px;

    border:
        1px solid
        var(--v360-line);

    border-radius:
        18px;

    background:
        rgba(255,255,255,.92);

    box-shadow:
        var(--v360-shadow-soft);

    backdrop-filter:
        blur(12px);
}

.v360-toolbar-copy{
    display:flex;

    align-items:center;

    gap:10px;

    min-width:0;
}

.v360-toolbar-icon{
    width:38px;
    height:38px;

    flex:0 0 38px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:12px;

    color:
        var(--v360-brand);

    background:#EDF4FF;

    font-size:.9rem;
}

.v360-toolbar-copy strong{
    display:block;

    color:
        var(--v360-text);

    font-size:.79rem;

    font-weight:800;
}

.v360-toolbar-copy span{
    display:block;

    margin-top:2px;

    color:
        var(--v360-soft);

    font-size:.66rem;

    font-weight:600;
}

/* ============================================================
   BUTTONS
============================================================ */

.v360-btn{
    min-height:43px;

    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    padding:
        9px 15px;

    border-radius:12px;

    font-size:.73rem;

    font-weight:800;

    transition:
        transform .18s ease,
        box-shadow .18s ease,
        background .18s ease,
        border-color .18s ease;
}

.v360-btn-primary{
    border:0;

    color:#fff !important;

    background:
        linear-gradient(
            135deg,
            var(--v360-blue),
            var(--v360-brand-2) 48%,
            var(--v360-brand)
        );

    box-shadow:
        0 11px 23px
        rgba(32,66,127,.22);
}

.v360-btn-primary:hover{
    transform:
        translateY(-2px);

    box-shadow:
        0 16px 30px
        rgba(32,66,127,.29);
}

.v360-btn-soft{
    border:
        1px solid
        #D7E2F2;

    color:
        var(--v360-brand) !important;

    background:#fff;
}

.v360-btn-soft:hover{
    transform:
        translateY(-1px);

    border-color:#BFD2EC;

    background:#F5F9FF;
}

/* ============================================================
   CARD
============================================================ */

.v360-card{
    overflow:hidden;

    margin-bottom:16px;

    border:
        1px solid
        var(--v360-line);

    border-radius:
        var(--v360-r-xl);

    background:
        rgba(255,255,255,.96);

    box-shadow:
        var(--v360-shadow-soft);

    transition:
        border-color .22s ease,
        box-shadow .22s ease;
}

.v360-card:hover{
    border-color:#D9E3F1;

    box-shadow:
        0 18px 48px
        rgba(15,23,42,.09);
}

.v360-card-header{
    min-height:71px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:12px;

    padding:
        15px 18px;

    border-bottom:
        1px solid
        #EDF0F5;

    background:
        radial-gradient(
            280px 100px at 5% 0%,
            rgba(79,140,255,.055),
            transparent 72%
        ),
        linear-gradient(
            180deg,
            #FFFFFF,
            #FBFCFF
        );
}

.v360-card-title-wrap{
    display:flex;

    align-items:center;

    gap:11px;
}

.v360-card-icon{
    width:40px;
    height:40px;

    flex:0 0 40px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:13px;

    color:
        var(--v360-brand);

    background:#EDF4FF;

    font-size:.92rem;
}

.v360-card-title{
    margin:0;

    color:#182230;

    font-family:
        "Manrope",
        "Inter",
        sans-serif;

    font-size:.96rem;

    line-height:1.2;

    font-weight:800;

    letter-spacing:-.2px;
}

.v360-card-subtitle{
    margin:
        3px 0 0;

    color:
        var(--v360-soft);

    font-size:.66rem;

    font-weight:600;
}

.v360-card-body{
    padding:18px;
}

/* ============================================================
   FORM SECTIONS
============================================================ */

.v360-section{
    padding:16px;

    border:
        1px solid
        #E9EDF4;

    border-radius:18px;

    background:
        linear-gradient(
            145deg,
            #FFFFFF,
            #FBFCFF
        );
}

.v360-section + .v360-section{
    margin-top:13px;
}

.v360-section-heading{
    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:12px;

    margin-bottom:14px;
}

.v360-section-heading-left{
    display:flex;

    align-items:center;

    gap:9px;
}

.v360-section-dot{
    width:9px;
    height:9px;

    border-radius:50%;

    background:
        linear-gradient(
            135deg,
            var(--v360-blue),
            var(--v360-brand)
        );

    box-shadow:
        0 0 0 4px
        rgba(79,140,255,.09);
}

.v360-section-heading h3{
    margin:0;

    color:
        var(--v360-text);

    font-size:.79rem;

    font-weight:800;
}

.v360-section-help{
    color:
        var(--v360-soft);

    font-size:.62rem;

    font-weight:600;
}

/* ============================================================
   FORM FIELDS
============================================================ */

.form-floating>.form-control,
.form-floating>.form-select{
    min-height:58px;

    border:
        1px solid
        #D9E0EA;

    border-radius:14px;

    color:
        var(--v360-text-2);

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
    border-color:
        var(--v360-blue) !important;

    background:#fff;

    box-shadow:
        0 0 0 4px
        rgba(79,140,255,.10) !important;
}

.form-floating>label{
    color:#667085;

    font-size:.77rem;

    font-weight:650;
}

/* ============================================================
   PASSWORD FIELD
============================================================ */

.v360-password-wrap{
    position:relative;
}

.v360-password-wrap
.form-control{
    padding-right:48px;
}

.v360-eye{
    position:absolute;

    right:11px;
    top:50%;

    z-index:6;

    width:34px;
    height:34px;

    display:flex;

    align-items:center;

    justify-content:center;

    transform:
        translateY(-50%);

    border:
        1px solid
        #DCE3ED;

    border-radius:10px;

    color:#667085;

    background:#fff;

    cursor:pointer;

    transition:
        color .18s ease,
        background .18s ease,
        border-color .18s ease;
}

.v360-eye:hover{
    color:
        var(--v360-brand);

    border-color:#BDD0EA;

    background:#F4F8FE;
}

/* ============================================================
   ACCOUNT STATUS HINT
============================================================ */

.v360-account-hint{
    display:grid;

    grid-template-columns:
        repeat(3,1fr);

    gap:9px;

    margin-top:12px;
}

.v360-account-item{
    padding:11px;

    border:
        1px solid
        #E7ECF3;

    border-radius:13px;

    background:#FBFCFE;
}

.v360-account-item i{
    color:
        var(--v360-brand-2);

    font-size:.72rem;
}

.v360-account-item strong{
    display:block;

    margin-top:7px;

    color:
        var(--v360-text-2);

    font-size:.65rem;

    font-weight:800;
}

.v360-account-item span{
    display:block;

    margin-top:2px;

    color:
        var(--v360-soft);

    font-size:.59rem;

    line-height:1.35;

    font-weight:600;
}

/* ============================================================
   ACTION BAR
============================================================ */

.v360-action-bar{
    position:sticky;

    bottom:12px;

    z-index:20;

    margin-top:15px;
}

.v360-action-inner{
    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:12px;

    padding:12px;

    border:
        1px solid
        rgba(216,225,238,.94);

    border-radius:17px;

    background:
        rgba(255,255,255,.92);

    box-shadow:
        0 15px 35px
        rgba(15,23,42,.11);

    backdrop-filter:
        blur(16px);
}

.v360-action-copy{
    display:flex;

    align-items:center;

    gap:9px;
}

.v360-action-state{
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

.v360-action-copy strong{
    display:block;

    color:
        var(--v360-text-2);

    font-size:.69rem;

    font-weight:800;
}

.v360-action-copy span{
    display:block;

    margin-top:2px;

    color:
        var(--v360-soft);

    font-size:.61rem;

    font-weight:600;
}

/* ============================================================
   DIRECTORY
============================================================ */

.v360-directory{
    overflow:hidden;

    border:
        1px solid
        var(--v360-line);

    border-radius:
        var(--v360-r-xl);

    background:#fff;

    box-shadow:
        var(--v360-shadow);
}

.v360-directory-head{
    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:12px;

    padding:18px;

    border-bottom:
        1px solid
        #EDF0F5;

    background:
        radial-gradient(
            360px 120px at 5% 0%,
            rgba(79,140,255,.06),
            transparent 70%
        ),
        linear-gradient(
            180deg,
            #FFFFFF,
            #FBFCFF
        );
}

.v360-directory-head-left{
    display:flex;

    align-items:center;

    gap:11px;
}

.v360-directory-icon{
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
            var(--v360-blue),
            var(--v360-brand)
        );

    box-shadow:
        0 10px 22px
        rgba(32,66,127,.20);

    font-size:.92rem;
}

.v360-directory h2{
    margin:0;

    color:
        var(--v360-text);

    font-family:
        "Manrope",
        "Inter",
        sans-serif;

    font-size:1rem;

    font-weight:800;
}

.v360-directory p{
    margin:
        3px 0 0;

    color:
        var(--v360-soft);

    font-size:.66rem;

    font-weight:600;
}

.v360-badge{
    display:inline-flex;

    align-items:center;

    gap:6px;

    min-height:31px;

    padding:
        6px 10px;

    border:
        1px solid
        #DCE8FA;

    border-radius:999px;

    color:#265EA9;

    background:#EEF5FF;

    font-size:.65rem;

    font-weight:800;
}

.v360-table-body{
    padding:15px;
}

/* ============================================================
   DATATABLES
============================================================ */

.dataTables_wrapper{
    width:100% !important;

    color:
        var(--v360-muted);

    font-size:.71rem;
}

.dataTables_wrapper .row{
    margin-left:
        0 !important;

    margin-right:
        0 !important;

    align-items:center;
}

.dataTables_wrapper
.dataTables_length,
.dataTables_wrapper
.dataTables_filter{
    margin-bottom:13px;
}

.dataTables_wrapper
.dataTables_length label,
.dataTables_wrapper
.dataTables_filter label{
    color:#667085;

    font-size:.67rem;

    font-weight:700;
}

.dataTables_wrapper
.dataTables_length select{
    min-width:72px;

    min-height:38px;

    margin:0 5px;

    border:
        1px solid
        #D7DEE9;

    border-radius:10px;

    color:#344054;

    background:#fff;

    font-size:.70rem;

    font-weight:700;
}

.dataTables_wrapper
.dataTables_filter input{
    width:
        min(
            270px,
            100%
        );

    min-height:39px;

    margin-left:8px;

    padding:
        0 12px;

    border:
        1px solid
        #D7DEE9;

    border-radius:11px;

    color:#344054;

    background:#fff;

    outline:none;

    font-size:.71rem;

    transition:
        border-color .18s ease,
        box-shadow .18s ease;
}

.dataTables_wrapper
.dataTables_filter input:focus{
    border-color:
        var(--v360-blue);

    box-shadow:
        0 0 0 4px
        rgba(79,140,255,.10);
}

/* ============================================================
   TABLE
============================================================ */

.v360-directory table{
    width:100% !important;

    margin:0 !important;

    border-collapse:
        separate !important;

    border-spacing:
        0 7px !important;

    table-layout:auto;
}

.v360-directory
table thead th{
    padding:
        10px 11px !important;

    border:0 !important;

    color:
        #667085 !important;

    background:
        transparent !important;

    font-size:
        .60rem !important;

    font-weight:
        800 !important;

    letter-spacing:.42px;

    text-transform:uppercase;

    white-space:
        nowrap !important;
}

.v360-directory
table tbody td{
    padding:
        10px 11px !important;

    border-top:
        1px solid
        #E9EDF4 !important;

    border-bottom:
        1px solid
        #E9EDF4 !important;

    color:
        #344054 !important;

    background:
        #fff !important;

    font-size:
        .68rem !important;

    line-height:1.45;

    font-weight:600;

    vertical-align:
        middle !important;

    transition:
        background .18s ease,
        border-color .18s ease,
        box-shadow .18s ease;
}

.v360-directory
table tbody td:first-child{
    border-left:
        1px solid
        #E9EDF4 !important;

    border-radius:
        13px
        0
        0
        13px;
}

.v360-directory
table tbody td:last-child{
    border-right:
        1px solid
        #E9EDF4 !important;

    border-radius:
        0
        13px
        13px
        0;
}

.v360-directory
table tbody tr{
    transition:
        transform .18s ease;
}

.v360-directory
table tbody tr:hover{
    transform:
        translateY(-2px);
}

.v360-directory
table tbody tr:hover td{
    border-color:
        #DCE7F6 !important;

    background:
        linear-gradient(
            90deg,
            #F7FAFF,
            #FFFFFF
        ) !important;

    box-shadow:
        0 9px 23px
        rgba(15,23,42,.05);
}

/* ============================================================
   VOTER NAME
============================================================ */

.v360-person{
    display:flex;

    align-items:center;

    gap:9px;

    min-width:175px;
}

.v360-person-line{
    width:7px;
    height:34px;

    flex:0 0 7px;

    border-radius:999px;

    background:
        linear-gradient(
            180deg,
            var(--v360-blue),
            var(--v360-brand)
        );

    opacity:.86;
}

.v360-person strong{
    color:#1D2939;

    font-size:.71rem;

    font-weight:800;
}

/* ============================================================
   STATUS
============================================================ */

.v360-status{
    display:inline-flex;

    align-items:center;

    gap:5px;

    min-height:27px;

    padding:
        5px 8px;

    border-radius:8px;

    font-size:.60rem;

    font-weight:800;

    white-space:nowrap;
}

.v360-success{
    color:#06795B;

    border:
        1px solid
        #D1FAE5;

    background:#ECFDF5;
}

.v360-danger{
    color:#B42318;

    border:
        1px solid
        #FEE4E2;

    background:#FEF3F2;
}

.v360-warning{
    color:#B54708;

    border:
        1px solid
        #FEF0C7;

    background:#FFFAEB;
}

.v360-blue{
    color:#175CD3;

    border:
        1px solid
        #D1E9FF;

    background:#EFF8FF;
}

.v360-neutral{
    color:#475467;

    border:
        1px solid
        #EAECF0;

    background:#F9FAFB;
}

/* ============================================================
   ACTION BUTTONS
============================================================ */

.v360-icon-btn{
    width:35px;
    height:35px;

    display:inline-flex;

    align-items:center;

    justify-content:center;

    padding:0;

    border:
        0 !important;

    border-radius:
        10px !important;

    color:
        #fff !important;

    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.v360-icon-btn:hover{
    transform:
        translateY(-2px);
}

.v360-edit{
    background:
        linear-gradient(
            135deg,
            #4F8CFF,
            #2563B9
        ) !important;

    box-shadow:
        0 8px 16px
        rgba(37,99,185,.17);
}

.v360-delete{
    background:
        linear-gradient(
            135deg,
            #F36A6A,
            #D83B47
        ) !important;

    box-shadow:
        0 8px 16px
        rgba(216,59,71,.16);
}

/* ============================================================
   PAGINATION
============================================================ */

.dataTables_wrapper
.dataTables_info{
    padding-top:
        13px !important;

    color:
        #98A2B3 !important;

    font-size:
        .65rem !important;

    font-weight:600;
}

.dataTables_wrapper
.dataTables_paginate{
    display:flex;

    justify-content:flex-end;

    gap:4px;

    padding-top:
        9px !important;
}

.dataTables_wrapper
.dataTables_paginate
.paginate_button{
    min-width:34px;

    height:34px;

    display:
        inline-flex !important;

    align-items:center;

    justify-content:center;

    margin:
        0 2px !important;

    padding:
        0 9px !important;

    border:
        1px solid
        transparent !important;

    border-radius:
        9px !important;

    color:
        #667085 !important;

    background:
        transparent !important;

    font-size:.67rem;

    font-weight:800;

    box-shadow:none !important;
}

.dataTables_wrapper
.dataTables_paginate
.paginate_button:hover{
    border-color:
        #DCE8FA !important;

    color:
        #3168C8 !important;

    background:
        #EFF5FF !important;
}

.dataTables_wrapper
.dataTables_paginate
.paginate_button.current,
.dataTables_wrapper
.dataTables_paginate
.paginate_button.current:hover{
    border-color:
        transparent !important;

    color:#fff !important;

    background:
        linear-gradient(
            135deg,
            var(--v360-blue),
            var(--v360-brand)
        ) !important;

    box-shadow:
        0 8px 18px
        rgba(32,66,127,.20) !important;
}

/* ============================================================
   RESPONSIVE
============================================================ */

@media (max-width:1320px){

    .v360-hero-grid{
        grid-template-columns:
            1fr;
    }

    .v360-hero-metrics{
        min-width:0;

        width:100%;
    }
}

@media (max-width:991px){

    .v360-shell{
        padding:
            0 13px;
    }

    .v360-hero{
        padding:23px;
    }
}

@media (max-width:767px){

    .content{
        padding-top:
            12px !important;
    }

    .v360-shell{
        padding:
            0 10px;
    }

    .v360-hero{
        min-height:0;

        padding:
            20px 17px;

        border-radius:
            22px;
    }

    .v360-hero h1{
        font-size:1.8rem;
    }

    .v360-hero p{
        font-size:.80rem;
    }

    .v360-hero-metrics{
        grid-template-columns:
            repeat(2,1fr);
    }

    .v360-toolbar{
        align-items:flex-start;

        flex-direction:column;
    }

    .v360-toolbar .v360-btn{
        width:100%;
    }

    .v360-card{
        border-radius:19px;
    }

    .v360-card-header{
        padding:14px;
    }

    .v360-card-body{
        padding:14px;
    }

    .v360-section{
        padding:13px;
    }

    .v360-account-hint{
        grid-template-columns:
            1fr;
    }

    .v360-action-inner{
        align-items:stretch;

        flex-direction:column;
    }

    .v360-action-inner > .d-flex{
        width:100%;
    }

    .v360-action-inner .v360-btn{
        flex:1;
    }

    .v360-directory{
        border-radius:19px;
    }

    .v360-directory-head{
        align-items:flex-start;

        padding:14px;
    }

    .v360-table-body{
        padding:10px;
    }

    .dataTables_wrapper
    .dataTables_length,
    .dataTables_wrapper
    .dataTables_filter{
        text-align:left !important;
    }

    .dataTables_wrapper
    .dataTables_filter input{
        width:100%;

        margin:
            6px 0 0;
    }

    .dataTables_wrapper
    .dataTables_paginate{
        justify-content:center;

        flex-wrap:wrap;
    }

    .table-responsive{
        overflow-x:auto;

        -webkit-overflow-scrolling:
            touch;
    }

    .v360-directory table{
        min-width:1350px;
    }
}

@media (max-width:480px){

    .v360-hero-metrics{
        gap:7px;
    }

    .v360-hero-metric{
        min-height:95px;

        padding:12px;
    }

    .v360-hero-metric strong{
        font-size:1.17rem;
    }

    .v360-hero-metric span{
        font-size:.57rem;
    }

    .v360-action-copy{
        display:none;
    }

    .v360-action-inner{
        padding:9px;
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

<body class="v360-page">

<!-- PRELOADER -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

<?php include './admin/include/navbar.php'; ?>
<?php include './admin/include/header.php'; ?>

<div class="content">

    <div class="v360-shell">


        <!-- =====================================================
             HERO
        ====================================================== -->

        <section class="v360-hero">

            <div class="v360-hero-grid">


                <div>

                    <div class="v360-eyebrow">

                        <span class="v360-live"></span>

                        Estadística360 · Voter Intelligence

                    </div>


                    <h1>

                        Gestión de
                        <span>Votantes</span>

                    </h1>


                    <p>

                        Centraliza la información territorial, demográfica y
                        política de los votantes para fortalecer el análisis,
                        la segmentación y la toma de decisiones electorales.

                    </p>


                    <div class="v360-hero-pills">


                        <span class="v360-hero-pill">

                            <i class="fas fa-users"></i>

                            Base electoral

                        </span>


                        <span class="v360-hero-pill">

                            <i class="fas fa-map-location-dot"></i>

                            Segmentación territorial

                        </span>


                        <span class="v360-hero-pill">

                            <i class="fas fa-chart-pie"></i>

                            Perfil demográfico

                        </span>


                    </div>

                </div>


                <div class="v360-hero-metrics">


                    <div class="v360-hero-metric">

                        <i class="fas fa-users"></i>

                        <strong>
                            <?= (int)$totalVotantes ?>
                        </strong>

                        <span>
                            Votantes registrados
                        </span>

                    </div>


                    <div class="v360-hero-metric">

                        <i class="fas fa-circle-check"></i>

                        <strong>
                            <?= (int)$totalActivos ?>
                        </strong>

                        <span>
                            Cuentas activas
                        </span>

                    </div>


                    <div class="v360-hero-metric">

                        <i class="fas fa-map"></i>

                        <strong>
                            <?= (int)$totalDepartamentos ?>
                        </strong>

                        <span>
                            Departamentos presentes
                        </span>

                    </div>


                    <div class="v360-hero-metric">

                        <i class="fas fa-location-dot"></i>

                        <strong>
                            <?= (int)$totalMunicipios ?>
                        </strong>

                        <span>
                            Municipios registrados
                        </span>

                    </div>


                </div>

            </div>

        </section>


        <!-- =====================================================
             TOOLBAR
        ====================================================== -->

        <section class="v360-toolbar">


            <div class="v360-toolbar-copy">


                <div class="v360-toolbar-icon">

                    <i class="fas fa-compass"></i>

                </div>


                <div>

                    <strong>
                        Centro de gestión electoral
                    </strong>

                    <span>
                        Registra, actualiza y administra la base de votantes.
                    </span>

                </div>


            </div>


            <button
                type="button"
                class="v360-btn v360-btn-primary"
                id="btnNuevoVotante">

                <i class="fas fa-user-plus"></i>

                Nuevo votante

            </button>


        </section>


        <!-- =====================================================
             FORM
        ====================================================== -->

        <section
            class="v360-card"
            id="v360FormCard">


            <div class="v360-card-header">


                <div class="v360-card-title-wrap">


                    <div class="v360-card-icon">

                        <i class="fas fa-user-check"></i>

                    </div>


                    <div>

                        <h2 class="v360-card-title">

                            Perfil del votante

                        </h2>


                        <p class="v360-card-subtitle">

                            Información territorial, demográfica y de acceso.

                        </p>

                    </div>


                </div>


                <span class="v360-badge">

                    <i class="fas fa-asterisk"></i>

                    Campos requeridos

                </span>


            </div>


            <div class="v360-card-body">


                <div
                    class="small text-muted fw-semibold mb-3"
                    id="spanEncuesta">
                </div>


                <form
                    id="formvotantes"
                    role="form"
                    autocomplete="off">


                    <input
                        type="hidden"
                        name="op"
                        id="op">


                    <input
                        type="hidden"
                        name="idVotantes"
                        id="idVotantes">


                    <input
                        type="hidden"
                        id="password2"
                        name="password2"
                        value="">


                    <!-- =========================================
                         IDENTIDAD Y TERRITORIO
                    ========================================== -->

                    <div class="v360-section">


                        <div class="v360-section-heading">


                            <div class="v360-section-heading-left">

                                <span class="v360-section-dot"></span>

                                <h3>
                                    Identidad y territorio
                                </h3>

                            </div>


                            <span class="v360-section-help">

                                Datos básicos y ubicación geográfica

                            </span>


                        </div>


                        <div class="row g-3">


                            <div class="col-12">


                                <div class="form-floating">


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="nombre_completo"
                                        name="nombre_completo"
                                        placeholder="Nombre completo del votante"
                                        required>


                                    <label for="nombre_completo">

                                        Nombre completo
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select ocultar-select"
                                        id="tbl_departamento_id"
                                        name="tbl_departamento_id">

                                        <?= $optionDep ?>

                                    </select>


                                    <label for="tbl_departamento_id">

                                        Departamento
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="tbl_municipio_id"
                                        name="tbl_municipio_id">
                                    </select>


                                    <label for="tbl_municipio_id">

                                        Municipio
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-2">


                                <div class="form-floating">


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="comuna"
                                        name="comuna"
                                        placeholder="Comuna">


                                    <label for="comuna">
                                        Comuna
                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-2">


                                <div class="form-floating">


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="barrio"
                                        name="barrio"
                                        placeholder="Barrio">


                                    <label for="barrio">
                                        Barrio
                                    </label>


                                </div>


                            </div>


                        </div>


                    </div>


                    <!-- =========================================
                         PERFIL DEMOGRÁFICO
                    ========================================== -->

                    <div class="v360-section">


                        <div class="v360-section-heading">


                            <div class="v360-section-heading-left">

                                <span class="v360-section-dot"></span>

                                <h3>
                                    Perfil demográfico
                                </h3>

                            </div>


                            <span class="v360-section-help">

                                Variables para segmentación y análisis

                            </span>


                        </div>


                        <div class="row g-3">


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="ideologia"
                                        name="ideologia"
                                        required>


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione la tendencia ideológica política

                                        </option>


                                        <option value="izquierda">
                                            Izquierda
                                        </option>

                                        <option value="centro_izquierda">
                                            Centro izquierda
                                        </option>

                                        <option value="centro">
                                            Centro
                                        </option>

                                        <option value="centro_derecha">
                                            Centro derecha
                                        </option>

                                        <option value="derecha">
                                            Derecha
                                        </option>

                                        <option value="sin_definir">
                                            Sin definir
                                        </option>


                                    </select>


                                    <label for="ideologia">

                                        Ideología política
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="rango_edad"
                                        name="rango_edad"
                                        required>


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione el grupo etario

                                        </option>


                                        <option value="18-25">
                                            18-25
                                        </option>

                                        <option value="26-35">
                                            26-35
                                        </option>

                                        <option value="36-45">
                                            36-45
                                        </option>

                                        <option value="46-55">
                                            46-55
                                        </option>

                                        <option value="56-65">
                                            56-65
                                        </option>

                                        <option value="66+">
                                            66+
                                        </option>


                                    </select>


                                    <label for="rango_edad">

                                        Rango de edad
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="genero"
                                        name="genero"
                                        required>


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione la identidad de género

                                        </option>


                                        <option value="masculino">
                                            Masculino
                                        </option>

                                        <option value="femenino">
                                            Femenino
                                        </option>

                                        <option value="otro">
                                            Otro
                                        </option>

                                        <option value="prefiero_no_decir">
                                            Prefiero no decir
                                        </option>


                                    </select>


                                    <label for="genero">

                                        Género
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="nivel_ingresos"
                                        name="nivel_ingresos"
                                        required>


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione el nivel de ingresos

                                        </option>


                                        <option value="menos_1_salario">
                                            Menos de 1 salario
                                        </option>

                                        <option value="1-2_salarios">
                                            1-2 salarios
                                        </option>

                                        <option value="3-5_salarios">
                                            3-5 salarios
                                        </option>

                                        <option value="6-10_salarios">
                                            6-10 salarios
                                        </option>

                                        <option value="mas_10_salarios">
                                            Más de 10 salarios
                                        </option>


                                    </select>


                                    <label for="nivel_ingresos">

                                        Nivel socioeconómico
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="nivel_educacion"
                                        name="nivel_educacion">


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione el máximo nivel educativo alcanzado

                                        </option>


                                        <option value="primaria_incompleta">
                                            Primaria incompleta
                                        </option>

                                        <option value="primaria_completa">
                                            Primaria completa
                                        </option>

                                        <option value="secundaria_incompleta">
                                            Secundaria incompleta
                                        </option>

                                        <option value="secundaria_completa">
                                            Secundaria completa
                                        </option>

                                        <option value="tecnico">
                                            Técnico
                                        </option>

                                        <option value="tecnologo">
                                            Tecnólogo
                                        </option>

                                        <option value="universitario_incompleto">
                                            Universitario incompleto
                                        </option>

                                        <option value="universitario_completo">
                                            Universitario completo
                                        </option>

                                        <option value="posgrado">
                                            Posgrado
                                        </option>


                                    </select>


                                    <label for="nivel_educacion">
                                        Nivel educativo
                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-4">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="ocupacion"
                                        name="ocupacion"
                                        required>


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione la ocupación

                                        </option>


                                        <option value="Empleado">
                                            Empleado
                                        </option>

                                        <option value="Auto Empleado">
                                            Auto Empleado
                                        </option>

                                        <option value="Empresario">
                                            Empresario
                                        </option>

                                        <option value="Comerciante">
                                            Comerciante
                                        </option>

                                        <option value="Independiente">
                                            Independiente
                                        </option>


                                    </select>


                                    <label for="ocupacion">

                                        Ocupación
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                        </div>


                    </div>


                    <!-- =========================================
                         ACCESO
                    ========================================== -->

                    <div class="v360-section">


                        <div class="v360-section-heading">


                            <div class="v360-section-heading-left">

                                <span class="v360-section-dot"></span>

                                <h3>
                                    Cuenta y acceso
                                </h3>

                            </div>


                            <span class="v360-section-help">

                                Credenciales y estado de la cuenta

                            </span>


                        </div>


                        <div class="row g-3">


                            <div class="col-12 col-md-6 col-lg-3">


                                <div class="form-floating">


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Correo electrónico"
                                        onblur="VOTANTES.checkAvailability(this)">


                                    <label for="email">
                                        Correo electrónico
                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-3">


                                <div class="form-floating">


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="username"
                                        name="username"
                                        placeholder="Nombre de usuario"
                                        onblur="VOTANTES.checkAvailability(this)">


                                    <label for="username">
                                        Nombre de usuario
                                    </label>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-3">


                                <div class="form-floating v360-password-wrap">


                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="Contraseña"
                                        required>


                                    <label for="password">
                                        Contraseña
                                    </label>


                                    <button
                                        type="button"
                                        class="v360-eye"
                                        id="btnTogglePassword"
                                        aria-label="Mostrar u ocultar contraseña">

                                        <i class="fas fa-eye"></i>

                                    </button>


                                </div>


                            </div>


                            <div class="col-12 col-md-6 col-lg-3">


                                <div class="form-floating">


                                    <select
                                        class="form-select"
                                        id="estado"
                                        name="estado"
                                        required>


                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Seleccione el estado de la cuenta

                                        </option>


                                        <option value="activo">
                                            Activo
                                        </option>

                                        <option value="inactivo">
                                            Inactivo
                                        </option>

                                        <option value="suspendido">
                                            Suspendido
                                        </option>


                                    </select>


                                    <label for="estado">

                                        Estado de la cuenta
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                            </div>


                        </div>


                        <div class="v360-account-hint">


                            <div class="v360-account-item">


                                <i class="fas fa-envelope"></i>


                                <strong>
                                    Correo
                                </strong>


                                <span>
                                    Puede utilizarse para identificar al votante.
                                </span>


                            </div>


                            <div class="v360-account-item">


                                <i class="fas fa-user-shield"></i>


                                <strong>
                                    Usuario
                                </strong>


                                <span>
                                    El sistema validará disponibilidad al salir del campo.
                                </span>


                            </div>


                            <div class="v360-account-item">


                                <i class="fas fa-lock"></i>


                                <strong>
                                    Seguridad
                                </strong>


                                <span>
                                    Utiliza una contraseña segura para proteger el acceso.
                                </span>


                            </div>


                        </div>


                    </div>


                    <!-- =========================================
                         ACTION BAR
                    ========================================== -->

                    <div class="v360-action-bar">


                        <div class="v360-action-inner">


                            <div class="v360-action-copy">


                                <div class="v360-action-state">

                                    <i class="fas fa-check"></i>

                                </div>


                                <div>


                                    <strong>
                                        Formulario preparado
                                    </strong>


                                    <span>
                                        Guarda o actualiza la información del votante.
                                    </span>


                                </div>


                            </div>


                            <div class="d-flex align-items-center gap-2">


                                <button
                                    type="button"
                                    onclick="VOTANTES.emptyCells();"
                                    class="v360-btn v360-btn-soft">

                                    <i class="fas fa-rotate-left"></i>

                                    Limpiar

                                </button>


                                <button
                                    class="v360-btn v360-btn-primary"
                                    type="button"
                                    onclick="VOTANTES.validateData();">

                                    <i class="fas fa-floppy-disk"></i>

                                    Guardar votante

                                </button>


                            </div>


                        </div>


                    </div>


                </form>


            </div>


        </section>


        <!-- =====================================================
             DIRECTORY
        ====================================================== -->

        <section class="v360-directory">


            <div class="v360-directory-head">


                <div class="v360-directory-head-left">


                    <div class="v360-directory-icon">

                        <i class="fas fa-users"></i>

                    </div>


                    <div>


                        <h2>
                            Directorio de votantes
                        </h2>


                        <p>
                            Consulta, edita y administra los registros electorales.
                        </p>


                    </div>


                </div>


                <span class="v360-badge">

                    <i class="fas fa-database"></i>

                    <?= (int)$totalVotantes ?>

                    <?= $totalVotantes === 1 ? 'registro' : 'registros' ?>

                </span>


            </div>


            <div class="v360-table-body">


                <div class="table-responsive">


                    <table
                        id="dynamictable"
                        class="table table-sm fs-9 mb-0 align-middle">


                        <thead>


                            <tr>

                                <th>Editar</th>

                                <th>Eliminar</th>

                                <th>Tipo</th>

                                <th>Encuestado por</th>

                                <th>Nombre completo</th>

                                <th>Ideología</th>

                                <th>Edad</th>

                                <th>Nivel socioeconómico</th>

                                <th>Género</th>

                                <th>Departamento</th>

                                <th>Municipio</th>

                                <th>Nivel educativo</th>

                                <th>Ocupación</th>

                                <th>Estado</th>

                            </tr>


                        </thead>


                        <tbody class="list">


                        <?php if ($isvalid && count($arr) > 0): ?>


                            <?php foreach ($arr as $item): ?>


                                <?php

                                    $id =
                                        (int)(
                                            $item['id']
                                            ?? 0
                                        );

                                    $estado =
                                        strtolower(
                                            trim(
                                                (string)(
                                                    $item['estado']
                                                    ?? ''
                                                )
                                            )
                                        );

                                ?>


                                <tr>


                                    <!-- EDITAR -->

                                    <td>


                                        <button
                                            type="button"
                                            class="btn v360-icon-btn v360-edit v360-edit-votante"
                                            title="Editar"
                                            onclick="VOTANTES.editData(<?= $id ?>)">

                                            <i class="uil uil-edit"></i>

                                        </button>


                                    </td>


                                    <!-- ELIMINAR -->

                                    <td>


                                        <button
                                            type="button"
                                            class="btn v360-icon-btn v360-delete"
                                            title="Eliminar"
                                            onclick="VOTANTES.deleteData(<?= $id ?>)">

                                            <i class="uil uil-trash-alt"></i>

                                        </button>


                                    </td>


                                    <!-- TIPO -->

                                    <td>


                                        <span class="v360-status v360-blue">

                                            <i class="fas fa-user-check"></i>

                                            <?= h($item['tipo_registro'] ?? 'Encuestado') ?>

                                        </span>


                                    </td>


                                    <!-- ENCUESTADO POR -->

                                    <td>

                                        <?= h($item['encuestador_nombre_completo'] ?? 'Sin asignar') ?>

                                    </td>


                                    <!-- NOMBRE -->

                                    <td>


                                        <div class="v360-person">


                                            <span class="v360-person-line"></span>


                                            <strong>

                                                <?= h($item['nombre_completo'] ?? '') ?>

                                            </strong>


                                        </div>


                                    </td>


                                    <!-- IDEOLOGÍA -->

                                    <td>


                                        <span class="v360-status v360-neutral">

                                            <?= h($item['ideologia'] ?? '') ?>

                                        </span>


                                    </td>


                                    <!-- EDAD -->

                                    <td>

                                        <?= h($item['rango_edad'] ?? '') ?>

                                    </td>


                                    <!-- INGRESOS -->

                                    <td>

                                        <?= h($item['nivel_ingresos'] ?? '') ?>

                                    </td>


                                    <!-- GÉNERO -->

                                    <td>

                                        <?= h($item['genero'] ?? '') ?>

                                    </td>


                                    <!-- DEPARTAMENTO -->

                                    <td>

                                        <?= h($item['codigo_departamento'] ?? '') ?>

                                    </td>


                                    <!-- MUNICIPIO -->

                                    <td>

                                        <?= h($item['codigo_municipio'] ?? '') ?>

                                    </td>


                                    <!-- EDUCACIÓN -->

                                    <td>

                                        <?= h($item['nivel_educacion'] ?? '') ?>

                                    </td>


                                    <!-- OCUPACIÓN -->

                                    <td>

                                        <?= h($item['ocupacion'] ?? '') ?>

                                    </td>


                                    <!-- ESTADO -->

                                    <td>


                                        <?php if ($estado === 'activo'): ?>


                                            <span class="v360-status v360-success">

                                                <i class="fas fa-check-circle"></i>

                                                Activo

                                            </span>


                                        <?php elseif ($estado === 'suspendido'): ?>


                                            <span class="v360-status v360-warning">

                                                <i class="fas fa-pause-circle"></i>

                                                Suspendido

                                            </span>


                                        <?php else: ?>


                                            <span class="v360-status v360-danger">

                                                <i class="fas fa-times-circle"></i>

                                                Inactivo

                                            </span>


                                        <?php endif; ?>


                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <tr>


                                <td
                                    colspan="14"
                                    class="text-center py-5 text-muted">

                                    No se encontraron registros.

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
     SCRIPTS
============================================================ -->

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>

<script src="admin/js/departamentoDama.js"></script>

<script
    type="text/javascript"
    src="./admin/js/lib/data-md5.js">
</script>

<script
    type="text/javascript"
    src="admin/js/votantes.js">
</script>

<?php include './admin/include/generic_dataTables.php'; ?>

<?php include 'admin/include/scriptsgober360.php'; ?>

<script src="vendors/flatpickr/flatpickr.min.js"></script>


<script>
/* ============================================================
   VOTANTES · UI ENHANCEMENTS
============================================================ */

$(document).ready(
    function () {


        /* =====================================================
           DEPARTAMENTO CONFIGURADO
        ====================================================== */

        const departamento =
            $("#departamentoConfiguracionInput")
                .val()
            ||
            "";


        if (departamento) {


            $("#tbl_departamento_id")
                .val(departamento)
                .trigger("change");


        }


        /* =====================================================
           MUNICIPIOS
        ====================================================== */

        if (
            window.DEPARTAMENTO
            &&
            typeof DEPARTAMENTO.getMunicipios
            === "function"
        ) {


            DEPARTAMENTO
                .getMunicipios();


        }


        /* =====================================================
           NUEVO VOTANTE
        ====================================================== */

        $("#btnNuevoVotante")
            .on(
                "click",
                function () {


                    if (
                        window.VOTANTES
                        &&
                        typeof VOTANTES.emptyCells
                        === "function"
                    ) {


                        VOTANTES
                            .emptyCells();


                    }


                    const card =
                        document
                            .getElementById(
                                "v360FormCard"
                            );


                    if (card) {


                        card.scrollIntoView({

                            behavior:
                                "smooth",

                            block:
                                "start"

                        });


                    }


                    setTimeout(
                        function () {


                            $("#nombre_completo")
                                .trigger("focus");


                        },
                        420
                    );


                }
            );


        /* =====================================================
           EDITAR -> SUBIR
        ====================================================== */

        $(document)
            .on(
                "click",
                ".v360-edit-votante",
                function () {


                    setTimeout(
                        function () {


                            const card =
                                document
                                    .getElementById(
                                        "v360FormCard"
                                    );


                            if (card) {


                                card.scrollIntoView({

                                    behavior:
                                        "smooth",

                                    block:
                                        "start"

                                });


                            }


                        },
                        180
                    );


                }
            );


        /* =====================================================
           MOSTRAR / OCULTAR CONTRASEÑA
        ====================================================== */

        $("#btnTogglePassword")
            .on(
                "click",
                function () {


                    const $password =
                        $("#password");


                    const isPassword =
                        $password
                            .attr("type")
                        ===
                        "password";


                    $password
                        .attr(
                            "type",
                            isPassword
                                ?
                                "text"
                                :
                                "password"
                        );


                    $(this)
                        .find("i")
                        .toggleClass(
                            "fa-eye",
                            !isPassword
                        )
                        .toggleClass(
                            "fa-eye-slash",
                            isPassword
                        );


                }
            );


    }
);
</script>

</body>
</html>
