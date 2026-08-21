<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/PreguntaGrilla.php';
include './admin/classes/Grilla.php';

// Validar permisos
$view    = SessionData::getPermission(38);
$create  = SessionData::getPermission(39);
$edit    = SessionData::getPermission(40);
$permits = SessionData::getPermission(41);

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

$userType = SessionData::getUserType();
$isAdmin = (
    $userType === Util::Administrador()
    ||
    $userType === Util::SuperAdministrador()
);

if (!$isAdmin) {
    require '../permiso_denegado.php';
    exit;
}

$modulo = 'Administración de Preguntas y Subpreguntas de Grilla';

// Obtener todas las grillas para selector
$arrGrillasResp = Grilla::getAll(null);
$arrGrillas = $arrGrillasResp['output']['response'] ?? [];

$optionGrillas = "";

foreach ($arrGrillas as $val) {

    $id = htmlspecialchars(
        $val['id'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $gr = htmlspecialchars(
        $val['grilla'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $optionGrillas .= "<option value='{$id}'>{$gr}</option>";
}

function h($s){
    return htmlspecialchars(
        (string)$s,
        ENT_QUOTES,
        'UTF-8'
    );
}

$totalGrillas = count($arrGrillas);
?>

<!DOCTYPE html>
<html
    lang="es-CO"
    dir="ltr"
    data-navigation-type="default"
    data-navbar-horizontal-shape="default">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Choices.js: se conserva para el selector múltiple -->
    <link
        rel="stylesheet"
        href="admin/js/lib/choices.min.css">

    <style>
    /* ==========================================================
       ESTADÍSTICA360
       QUESTION ARCHITECTURE STUDIO · WOW UI
       ----------------------------------------------------------
       IMPORTANTE:
       - No modifica IDs.
       - No modifica nombres de campos.
       - No modifica acciones de PREGUNTAS_GRILLA.
       - No modifica estructura de tabs/modal esperada por JS.
    ========================================================== */

    :root{
        --qa-navy-950:#07182F;
        --qa-navy-900:#0A2248;
        --qa-navy-800:#123A74;

        --qa-blue-700:#20427F;
        --qa-blue-600:#2D63BD;
        --qa-blue-500:#4B8CF7;
        --qa-cyan:#26B7DB;
        --qa-violet:#7867E8;

        --qa-success:#12B981;
        --qa-warning:#F59E0B;
        --qa-danger:#E5484D;

        --qa-page:#F3F6FB;
        --qa-card:#FFFFFF;
        --qa-soft:#F8FAFD;

        --qa-text:#101828;
        --qa-text-2:#344054;
        --qa-muted:#667085;
        --qa-light:#98A2B3;

        --qa-line:#E5EAF1;

        --qa-radius-xxl:30px;
        --qa-radius-xl:24px;
        --qa-radius-lg:18px;
        --qa-radius-md:14px;

        --qa-shadow:
            0 24px 68px rgba(15,23,42,.10);

        --qa-shadow-soft:
            0 12px 34px rgba(15,23,42,.065);
    }

    *{
        box-sizing:border-box;
    }

    html{
        scroll-behavior:smooth;
    }

    body.qa-page{
        margin:0;

        background:
            radial-gradient(
                900px 500px at 3% -5%,
                rgba(75,140,247,.12),
                transparent 64%
            ),
            radial-gradient(
                760px 440px at 103% 5%,
                rgba(120,103,232,.07),
                transparent 64%
            ),
            linear-gradient(
                180deg,
                #F8FAFD 0%,
                #F2F5FA 100%
            );

        color:var(--qa-text);

        font-family:
            "Inter",
            system-ui,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            sans-serif;

        overflow-x:hidden;
        -webkit-font-smoothing:antialiased;
    }

    body.qa-page::before{
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

        background-size:36px 36px;

        mask-image:
            linear-gradient(
                to bottom,
                #000,
                transparent 84%
            );
    }

    /* ==========================================================
       CONTENIDO
       No usar cálculo automático con .navbar:
       evita que el sidebar lateral genere espacios gigantes.
    ========================================================== */

    .content{
        padding-top:18px !important;
        padding-bottom:38px !important;
        margin-top:0 !important;
    }

    .qa-shell{
        width:100%;
        max-width:1660px;
        margin:0 auto;
        padding:0 18px;
    }

    /* ==========================================================
       HERO
    ========================================================== */

    .qa-hero{
        position:relative;
        isolation:isolate;
        overflow:hidden;

        min-height:222px;
        margin-bottom:16px;
        padding:29px 30px;

        border:1px solid rgba(255,255,255,.12);
        border-radius:var(--qa-radius-xxl);

        color:#fff;

        background:
            radial-gradient(
                540px 270px at 9% 0%,
                rgba(75,140,247,.35),
                transparent 66%
            ),
            radial-gradient(
                460px 260px at 95% 10%,
                rgba(38,183,219,.18),
                transparent 67%
            ),
            linear-gradient(
                135deg,
                #173E7B 0%,
                #102A56 47%,
                #07162E 100%
            );

        box-shadow:
            0 30px 80px rgba(8,28,63,.24);
    }

    .qa-hero::before{
        content:"";
        position:absolute;
        z-index:-1;

        width:430px;
        height:430px;

        right:-155px;
        top:-220px;

        border:1px solid rgba(255,255,255,.075);
        border-radius:50%;

        box-shadow:
            0 0 0 44px rgba(255,255,255,.021),
            0 0 0 90px rgba(255,255,255,.015),
            0 0 0 136px rgba(255,255,255,.010);
    }

    .qa-hero::after{
        content:"";
        position:absolute;
        z-index:-1;

        width:270px;
        height:270px;

        left:-165px;
        bottom:-210px;

        border-radius:50%;

        background:
            radial-gradient(
                circle,
                rgba(66,203,236,.20),
                transparent 68%
            );
    }

    .qa-hero-grid{
        display:grid;
        grid-template-columns:minmax(0,1fr) auto;
        gap:28px;
        align-items:center;
    }

    .qa-eyebrow{
        display:inline-flex;
        align-items:center;
        gap:8px;

        min-height:32px;
        margin-bottom:13px;
        padding:7px 11px;

        border:1px solid rgba(255,255,255,.14);
        border-radius:999px;

        color:rgba(255,255,255,.88);
        background:rgba(255,255,255,.075);

        backdrop-filter:blur(12px);

        font-size:.67rem;
        font-weight:800;
        letter-spacing:.62px;
        text-transform:uppercase;
    }

    .qa-live-dot{
        width:7px;
        height:7px;
        border-radius:50%;
        background:#5DE4A0;

        box-shadow:
            0 0 0 5px rgba(93,228,160,.11),
            0 0 16px rgba(93,228,160,.45);
    }

    .qa-hero h1{
        margin:0;

        color:#fff;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:clamp(1.85rem,3vw,2.95rem);
        line-height:1.04;
        font-weight:800;
        letter-spacing:-1.45px;
    }

    .qa-hero h1 span{
        color:#B7D0FF;
    }

    .qa-hero p{
        max-width:820px;
        margin:11px 0 0;

        color:rgba(255,255,255,.70);

        font-size:.91rem;
        line-height:1.67;
        font-weight:500;
    }

    .qa-hero-pills{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        margin-top:18px;
    }

    .qa-hero-pill{
        display:inline-flex;
        align-items:center;
        gap:7px;

        min-height:35px;
        padding:8px 11px;

        border:1px solid rgba(255,255,255,.10);
        border-radius:11px;

        color:rgba(255,255,255,.84);
        background:rgba(255,255,255,.07);

        font-size:.67rem;
        font-weight:700;
    }

    .qa-hero-pill i{
        color:#A7C7FF;
    }

    /* ==========================================================
       KPIs
    ========================================================== */

    .qa-hero-metrics{
        display:grid;
        grid-template-columns:repeat(4,minmax(92px,1fr));
        gap:9px;
        min-width:540px;
    }

    .qa-kpi{
        min-height:111px;
        padding:14px;

        border:1px solid rgba(255,255,255,.12);
        border-radius:17px;

        background:
            linear-gradient(
                145deg,
                rgba(255,255,255,.115),
                rgba(255,255,255,.05)
            );

        backdrop-filter:blur(14px);

        transition:
            transform .22s ease,
            border-color .22s ease,
            background .22s ease;
    }

    .qa-kpi:hover{
        transform:translateY(-4px);
        border-color:rgba(255,255,255,.20);

        background:
            linear-gradient(
                145deg,
                rgba(255,255,255,.17),
                rgba(255,255,255,.07)
            );
    }

    .qa-kpi-icon{
        width:31px;
        height:31px;

        display:flex;
        align-items:center;
        justify-content:center;

        margin-bottom:13px;

        border-radius:10px;

        color:#D8E8FF;
        background:rgba(255,255,255,.10);

        font-size:.78rem;
    }

    .qa-kpi strong{
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

    .qa-kpi span{
        display:block;
        margin-top:5px;

        color:rgba(255,255,255,.58);

        font-size:.59rem;
        line-height:1.25;
        font-weight:700;
    }

    /* ==========================================================
       TOOLBAR
    ========================================================== */

    .qa-toolbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;

        margin-bottom:16px;
        padding:13px 15px;

        border:1px solid var(--qa-line);
        border-radius:18px;

        background:rgba(255,255,255,.92);
        box-shadow:var(--qa-shadow-soft);

        backdrop-filter:blur(12px);
    }

    .qa-toolbar-copy{
        display:flex;
        align-items:center;
        gap:10px;
        min-width:0;
    }

    .qa-toolbar-icon{
        width:38px;
        height:38px;
        flex:0 0 38px;

        display:flex;
        align-items:center;
        justify-content:center;

        border-radius:12px;

        color:var(--qa-blue-700);
        background:#EDF4FF;

        font-size:.9rem;
    }

    .qa-toolbar-copy strong{
        display:block;

        color:var(--qa-text);

        font-size:.79rem;
        font-weight:800;
    }

    .qa-toolbar-copy span{
        display:block;
        margin-top:2px;

        color:var(--qa-light);

        font-size:.66rem;
        font-weight:600;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .qa-btn{
        min-height:43px;

        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;

        padding:9px 15px;

        border-radius:12px;

        font-size:.73rem;
        font-weight:800;

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background .18s ease,
            border-color .18s ease;
    }

    .qa-btn-primary{
        border:0;
        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--qa-blue-500),
                var(--qa-blue-600) 50%,
                var(--qa-blue-700)
            );

        box-shadow:
            0 11px 23px rgba(32,66,127,.22);
    }

    .qa-btn-primary:hover{
        transform:translateY(-2px);

        box-shadow:
            0 16px 30px rgba(32,66,127,.29);
    }

    .qa-btn-soft{
        border:1px solid #D7E2F2;

        color:var(--qa-blue-700) !important;
        background:#fff;
    }

    .qa-btn-soft:hover{
        transform:translateY(-1px);

        border-color:#BFD2EC;
        background:#F5F9FF;
    }

    /* ==========================================================
       WORKSPACE CARD
    ========================================================== */

    .qa-workspace{
        overflow:hidden;

        border:1px solid var(--qa-line);
        border-radius:var(--qa-radius-xl);

        background:rgba(255,255,255,.97);

        box-shadow:var(--qa-shadow);
    }

    .qa-workspace-head{
        min-height:75px;

        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;

        padding:16px 18px;

        border-bottom:1px solid #EDF0F5;

        background:
            radial-gradient(
                300px 110px at 4% 0%,
                rgba(75,140,247,.06),
                transparent 72%
            ),
            linear-gradient(
                180deg,
                #FFFFFF,
                #FBFCFF
            );
    }

    .qa-workspace-title{
        display:flex;
        align-items:center;
        gap:11px;
    }

    .qa-workspace-icon{
        width:42px;
        height:42px;
        flex:0 0 42px;

        display:flex;
        align-items:center;
        justify-content:center;

        border-radius:13px;

        color:var(--qa-blue-700);
        background:#EDF4FF;

        font-size:.94rem;
    }

    .qa-workspace-title h2{
        margin:0;

        color:#182230;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:.98rem;
        font-weight:800;
    }

    .qa-workspace-title p{
        margin:3px 0 0;

        color:var(--qa-light);

        font-size:.65rem;
        font-weight:600;
    }

    .qa-workspace-body{
        padding:16px;
    }

    /* ==========================================================
       TABS - MUY DIFERENCIADAS
    ========================================================== */

    .qa-tabs{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:9px;

        margin-bottom:14px;
        padding:7px;

        border:1px solid #E6EBF2;
        border-radius:17px;

        background:#F6F8FC;
    }

    .qa-tabs .nav-item{
        width:100%;
    }

    .qa-tabs .nav-link{
        position:relative;

        width:100%;
        min-height:58px;

        display:flex;
        align-items:center;
        justify-content:center;
        gap:9px;

        padding:9px 12px !important;

        border:1px solid transparent !important;
        border-radius:12px !important;

        color:#667085 !important;
        background:transparent !important;

        font-size:.70rem;
        font-weight:800 !important;

        transition:
            transform .18s ease,
            background .18s ease,
            border-color .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .qa-tabs .nav-link::before{
        content:"";

        width:8px;
        height:8px;

        border-radius:50%;

        background:#C6D0DD;

        box-shadow:
            0 0 0 4px rgba(152,162,179,.08);

        transition:
            background .18s ease,
            box-shadow .18s ease;
    }

    .qa-tabs .nav-link:hover{
        transform:translateY(-1px);

        border-color:#E0E6EF !important;

        color:var(--qa-blue-700) !important;
        background:#fff !important;
    }

    .qa-tabs .nav-link.active{
        color:#fff !important;

        border-color:transparent !important;

        background:
            linear-gradient(
                135deg,
                var(--qa-blue-500),
                var(--qa-blue-700)
            ) !important;

        box-shadow:
            0 11px 24px rgba(32,66,127,.20);
    }

    .qa-tabs .nav-link.active::before{
        background:#BEEBD8;

        box-shadow:
            0 0 0 4px rgba(190,235,216,.11);
    }

    .qa-tab-content{
        min-height:320px;

        padding:14px;

        border:1px solid #E6EBF2;
        border-radius:18px;

        background:
            radial-gradient(
                300px 150px at 4% 0%,
                rgba(75,140,247,.045),
                transparent 72%
            ),
            #fff;
    }

    /* ==========================================================
       TABLES
    ========================================================== */

    .qa-table-shell{
        overflow:hidden;

        border:1px solid #E5EAF1;
        border-radius:16px;

        background:#fff;
    }

    .qa-table-shell table{
        width:100%;
        margin:0 !important;

        border-collapse:separate !important;
        border-spacing:0 !important;
    }

    .qa-table-shell table thead th{
        padding:12px 12px !important;

        border-top:0 !important;
        border-bottom:1px solid #E6EBF2 !important;

        color:#667085 !important;
        background:#F8FAFC !important;

        font-size:.60rem !important;
        font-weight:800 !important;
        letter-spacing:.42px;
        text-transform:uppercase;

        white-space:nowrap;
    }

    .qa-table-shell table tbody td{
        padding:11px 12px !important;

        border-bottom:1px solid #EEF1F5 !important;

        color:#344054 !important;
        background:#fff !important;

        font-size:.68rem !important;
        line-height:1.45;
        vertical-align:middle !important;

        transition:
            background .18s ease,
            box-shadow .18s ease;
    }

    .qa-table-shell table tbody tr:last-child td{
        border-bottom:0 !important;
    }

    .qa-table-shell table tbody tr:hover td{
        background:
            linear-gradient(
                90deg,
                #F5F9FF,
                #FFFFFF
            ) !important;

        box-shadow:
            inset 3px 0 0 rgba(75,140,247,.42);
    }

    /* Botones que el JS inserte dentro de las tablas */
    .qa-table-shell table .btn{
        min-height:33px;

        border-radius:9px !important;

        font-size:.64rem;
        font-weight:750;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .qa-table-shell table .btn:hover{
        transform:translateY(-1px);
    }

    /* ==========================================================
       LOADING
    ========================================================== */

    .qa-loading{
        min-height:190px;

        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:10px;

        color:var(--qa-light);
    }

    .qa-loading-orb{
        width:48px;
        height:48px;

        display:flex;
        align-items:center;
        justify-content:center;

        border:1px solid #DBE7F8;
        border-radius:15px;

        color:var(--qa-blue-700);
        background:#EEF5FF;

        box-shadow:
            0 10px 24px rgba(32,66,127,.08);
    }

    .qa-loading span{
        font-size:.64rem;
        font-weight:650;
    }

    /* ==========================================================
       PREVIEW
    ========================================================== */

    .qa-preview-banner{
        display:flex;
        align-items:flex-start;
        gap:11px;

        margin-bottom:13px;
        padding:13px;

        border:1px solid #D8E9FC;
        border-radius:14px;

        color:#175CD3;

        background:
            linear-gradient(
                145deg,
                #EFF8FF,
                #FFFFFF
            );
    }

    .qa-preview-banner-icon{
        width:35px;
        height:35px;
        flex:0 0 35px;

        display:flex;
        align-items:center;
        justify-content:center;

        border-radius:11px;

        color:#175CD3;
        background:#DCEEFF;
    }

    .qa-preview-banner strong{
        display:block;

        color:#1849A9;

        font-size:.70rem;
        font-weight:800;
    }

    .qa-preview-banner span{
        display:block;
        margin-top:2px;

        color:#5475A1;

        font-size:.61rem;
        line-height:1.45;
        font-weight:600;
    }

    #previewContenido{
        min-height:230px;

        padding:14px;

        border:1px dashed #CBD8E8;
        border-radius:16px;

        background:
            radial-gradient(
                260px 140px at 50% 0%,
                rgba(75,140,247,.06),
                transparent 72%
            ),
            linear-gradient(
                180deg,
                #FCFDFF,
                #F7FAFD
            );
    }

    /* ==========================================================
       MODAL
    ========================================================== */

    .qa-modal .modal-content{
        overflow:hidden;

        border:1px solid rgba(15,23,42,.09) !important;
        border-radius:24px !important;

        box-shadow:
            0 30px 82px rgba(15,23,42,.25) !important;
    }

    .qa-modal .modal-header{
        position:relative;
        overflow:hidden;

        padding:18px 20px;

        border-bottom:0 !important;

        color:#fff;

        background:
            radial-gradient(
                410px 190px at 5% 0%,
                rgba(75,140,247,.28),
                transparent 72%
            ),
            radial-gradient(
                340px 170px at 100% 0%,
                rgba(120,103,232,.20),
                transparent 70%
            ),
            linear-gradient(
                135deg,
                #173D79,
                #102A56 55%,
                #081B38
            ) !important;
    }

    .qa-modal .modal-header::after{
        content:"";
        position:absolute;

        width:190px;
        height:190px;

        right:-85px;
        top:-110px;

        border:1px solid rgba(255,255,255,.08);
        border-radius:50%;

        box-shadow:
            0 0 0 30px rgba(255,255,255,.02);
    }

    .qa-modal-icon{
        position:relative;
        z-index:2;

        width:46px;
        height:46px;
        flex:0 0 46px;

        display:flex;
        align-items:center;
        justify-content:center;

        border:1px solid rgba(255,255,255,.18);
        border-radius:14px;

        color:#fff;
        background:rgba(255,255,255,.12);

        backdrop-filter:blur(10px);
    }

    .qa-modal .modal-title{
        position:relative;
        z-index:2;

        margin:0;

        color:#fff;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;
        font-weight:800;
    }

    .qa-modal-sub{
        position:relative;
        z-index:2;

        margin-top:3px;

        color:rgba(255,255,255,.62);

        font-size:.62rem;
        font-weight:600;
    }

    .qa-modal .modal-body{
        padding:18px !important;

        background:
            linear-gradient(
                180deg,
                #FBFCFE,
                #F5F8FC
            ) !important;
    }

    .qa-modal-section{
        padding:16px;

        border:1px solid #E4E9F1;
        border-radius:17px;

        background:#fff;

        box-shadow:
            0 8px 20px rgba(15,23,42,.04);
    }

    .qa-modal-section + .qa-modal-section{
        margin-top:12px;
    }

    .qa-modal-section-title{
        display:flex;
        align-items:center;
        gap:8px;

        margin-bottom:13px;

        color:var(--qa-text-2);

        font-size:.72rem;
        font-weight:800;
    }

    .qa-modal-section-title i{
        width:30px;
        height:30px;

        display:flex;
        align-items:center;
        justify-content:center;

        border-radius:10px;

        color:var(--qa-blue-700);
        background:#EEF5FF;
    }

    /* Modal form controls */
    .qa-modal .form-label{
        margin-bottom:6px;

        color:#475467;

        font-size:.68rem;
        font-weight:800;
    }

    .qa-modal .form-control,
    .qa-modal .form-select{
        min-height:45px;

        border:1px solid #D9E0EA !important;
        border-radius:12px !important;

        color:var(--qa-text-2);
        background:#FBFCFE;

        font-size:.75rem;
        font-weight:600;

        box-shadow:none !important;
    }

    .qa-modal textarea.form-control{
        min-height:96px;
        resize:vertical;
    }

    .qa-modal .form-control:focus,
    .qa-modal .form-select:focus{
        border-color:var(--qa-blue-500) !important;
        background:#fff;

        box-shadow:
            0 0 0 4px rgba(75,140,247,.10) !important;
    }

    .qa-modal small{
        color:var(--qa-light) !important;

        font-size:.60rem;
        line-height:1.4;
        font-weight:600;
    }

    /* ==========================================================
       SWITCH CARDS
    ========================================================== */

    .qa-switch-grid{
        display:grid;
        grid-template-columns:repeat(2,1fr);
        gap:9px;
    }

    .qa-switch-card{
        min-height:70px;

        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;

        padding:11px 12px;

        border:1px solid #E3E8F0;
        border-radius:13px;

        background:#FBFCFE;
    }

    .qa-switch-copy{
        min-width:0;
    }

    .qa-switch-copy strong{
        display:block;

        color:var(--qa-text-2);

        font-size:.65rem;
        font-weight:800;
    }

    .qa-switch-copy span{
        display:block;
        margin-top:2px;

        color:var(--qa-light);

        font-size:.57rem;
        line-height:1.35;
        font-weight:600;
    }

    .qa-switch-card .form-check{
        flex:0 0 auto;
        margin:0;
    }

    .qa-switch-card .form-check-input{
        width:42px;
        height:23px;

        margin:0;

        cursor:pointer;
    }

    .qa-switch-card .form-check-input:checked{
        border-color:var(--qa-success);
        background-color:var(--qa-success);
    }

    /* ==========================================================
       CHOICES
    ========================================================== */

    .choices{
        margin-bottom:0 !important;
    }

    .choices__inner{
        min-height:47px !important;

        padding:7px 9px !important;

        border:1px solid #D9E0EA !important;
        border-radius:12px !important;

        background:#FBFCFE !important;

        font-size:.72rem !important;
    }

    .choices.is-focused .choices__inner{
        border-color:var(--qa-blue-500) !important;

        box-shadow:
            0 0 0 4px rgba(75,140,247,.10) !important;
    }

    .choices__list--multiple .choices__item{
        border:1px solid #DCE8FA !important;
        border-radius:999px !important;

        color:#245BA7 !important;
        background:#EEF5FF !important;

        font-size:.61rem !important;
        font-weight:750 !important;
    }

    .choices__list--dropdown{
        overflow:hidden;

        border:1px solid #DCE3ED !important;
        border-radius:12px !important;

        box-shadow:
            0 14px 34px rgba(15,23,42,.13);
    }

    /* ==========================================================
       MODAL FOOTER
    ========================================================== */

    .qa-modal .modal-footer{
        padding:12px 18px;

        border-top:1px solid #E7EBF1;

        background:#fff;
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){

        .qa-hero-grid{
            grid-template-columns:1fr;
        }

        .qa-hero-metrics{
            min-width:0;
            width:100%;
        }
    }

    @media (max-width:991px){

        .qa-shell{
            padding:0 13px;
        }

        .qa-hero{
            padding:23px;
        }

        .qa-switch-grid{
            grid-template-columns:1fr;
        }
    }

    @media (max-width:767px){

        .content{
            padding-top:12px !important;
        }

        .qa-shell{
            padding:0 10px;
        }

        .qa-hero{
            min-height:0;
            padding:20px 17px;
            border-radius:22px;
        }

        .qa-hero h1{
            font-size:1.8rem;
        }

        .qa-hero p{
            font-size:.80rem;
        }

        .qa-hero-metrics{
            grid-template-columns:repeat(2,1fr);
        }

        .qa-toolbar{
            align-items:flex-start;
            flex-direction:column;
        }

        .qa-toolbar .qa-btn{
            width:100%;
        }

        .qa-workspace{
            border-radius:19px;
        }

        .qa-workspace-head{
            align-items:flex-start;
            padding:14px;
        }

        .qa-workspace-body{
            padding:12px;
        }

        .qa-tabs{
            grid-template-columns:1fr;
        }

        .qa-tabs .nav-link{
            min-height:46px;
            justify-content:flex-start;
        }

        .qa-tab-content{
            min-height:260px;
            padding:10px;
        }

        .table-responsive{
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }

        .qa-table-shell table{
            min-width:850px;
        }

        .qa-modal .modal-body{
            padding:12px !important;
        }

        .qa-modal-section{
            padding:12px;
        }
    }

    @media (max-width:480px){

        .qa-hero-metrics{
            gap:7px;
        }

        .qa-kpi{
            min-height:96px;
            padding:12px;
        }

        .qa-kpi strong{
            font-size:1.16rem;
        }

        .qa-kpi span{
            font-size:.56rem;
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

</head>


<body class="qa-page">

<main
    class="main"
    id="top">

    <?php
        include './admin/include/navbar.php';
        include './admin/include/header.php';
    ?>


    <div class="content">

        <div class="qa-shell">


            <!-- =================================================
                 HERO
            ================================================== -->

            <section class="qa-hero">

                <div class="qa-hero-grid">


                    <div>

                        <div class="qa-eyebrow">

                            <span class="qa-live-dot"></span>

                            Estadística360 · Question Architecture Studio

                        </div>


                        <h1>

                            Preguntas y
                            <span>Subpreguntas</span>

                        </h1>


                        <p>

                            Construye la lógica de preguntas de grilla,
                            administra relaciones padre-hijo, opciones de
                            respuesta, condiciones y grillas asociadas desde
                            una experiencia visual más clara y controlada.

                        </p>


                        <div class="qa-hero-pills">


                            <span class="qa-hero-pill">

                                <i class="fas fa-diagram-project"></i>

                                Lógica condicional

                            </span>


                            <span class="qa-hero-pill">

                                <i class="fas fa-code-branch"></i>

                                Dependencias dinámicas

                            </span>


                            <span class="qa-hero-pill">

                                <i class="fas fa-eye"></i>

                                Preview en tiempo real

                            </span>


                        </div>

                    </div>


                    <div class="qa-hero-metrics">


                        <div class="qa-kpi">

                            <div class="qa-kpi-icon">

                                <i class="fas fa-layer-group"></i>

                            </div>

                            <strong>
                                <?= (int)$totalGrillas ?>
                            </strong>

                            <span>
                                Grillas disponibles
                            </span>

                        </div>


                        <div class="qa-kpi">

                            <div class="qa-kpi-icon">

                                <i class="fas fa-list-ol"></i>

                            </div>

                            <strong id="qaKpiPrincipales">
                                0
                            </strong>

                            <span>
                                Preguntas principales
                            </span>

                        </div>


                        <div class="qa-kpi">

                            <div class="qa-kpi-icon">

                                <i class="fas fa-code-branch"></i>

                            </div>

                            <strong id="qaKpiSubpreguntas">
                                0
                            </strong>

                            <span>
                                Subpreguntas cargadas
                            </span>

                        </div>


                        <div class="qa-kpi">

                            <div class="qa-kpi-icon">

                                <i class="fas fa-user-shield"></i>

                            </div>

                            <strong>
                                <?= $isAdmin ? 'Admin' : '—' ?>
                            </strong>

                            <span>
                                Nivel de acceso actual
                            </span>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <section class="qa-toolbar">

                <div class="qa-toolbar-copy">

                    <div class="qa-toolbar-icon">

                        <i class="fas fa-compass"></i>

                    </div>


                    <div>

                        <strong>
                            Arquitectura de cuestionarios
                        </strong>

                        <span>
                            Administra preguntas, reglas y estructura desde un único workspace.
                        </span>

                    </div>

                </div>


                <?php if ($create): ?>

                    <button
                        class="qa-btn qa-btn-primary"
                        id="btnNuevaPregunta"
                        type="button">

                        <i class="fas fa-plus"></i>

                        Nueva Pregunta

                    </button>

                <?php endif; ?>

            </section>


            <!-- =================================================
                 WORKSPACE
            ================================================== -->

            <section class="qa-workspace">


                <div class="qa-workspace-head">

                    <div class="qa-workspace-title">

                        <div class="qa-workspace-icon">

                            <i class="fas fa-sliders-h"></i>

                        </div>


                        <div>

                            <h2>
                                Gestión de preguntas de grilla
                            </h2>

                            <p>
                                Preguntas principales, subpreguntas y vista previa.
                            </p>

                        </div>

                    </div>


                    <div
                        class="d-none d-md-flex align-items-center gap-2">

                        <span
                            class="badge rounded-pill"
                            style="
                                color:#245BA7;
                                background:#EEF5FF;
                                border:1px solid #DCE8FA;
                                font-size:.62rem;
                                padding:7px 10px;
                            ">

                            <i class="fas fa-bolt me-1"></i>

                            Módulo dinámico

                        </span>

                    </div>

                </div>


                <div class="qa-workspace-body">


                    <!-- =========================================
                         TABS
                    ========================================== -->

                    <ul
                        class="nav qa-tabs"
                        id="pestanasPreguntas"
                        role="tablist">


                        <li
                            class="nav-item"
                            role="presentation">

                            <button
                                class="nav-link active"
                                id="tab-principales"
                                data-bs-toggle="tab"
                                data-bs-target="#preguntas-principales"
                                type="button"
                                role="tab">

                                <i class="fas fa-list-ol"></i>

                                Preguntas Principales

                            </button>

                        </li>


                        <li
                            class="nav-item"
                            role="presentation">

                            <button
                                class="nav-link"
                                id="tab-subpreguntas"
                                data-bs-toggle="tab"
                                data-bs-target="#subpreguntas"
                                type="button"
                                role="tab">

                                <i class="fas fa-code-branch"></i>

                                Subpreguntas

                            </button>

                        </li>


                        <li
                            class="nav-item"
                            role="presentation">

                            <button
                                class="nav-link"
                                id="tab-preview"
                                data-bs-toggle="tab"
                                data-bs-target="#preview"
                                type="button"
                                role="tab">

                                <i class="fas fa-eye"></i>

                                Vista Previa

                            </button>

                        </li>

                    </ul>


                    <!-- =========================================
                         TAB CONTENT
                    ========================================== -->

                    <div
                        class="tab-content qa-tab-content"
                        id="contenidoPestanas">


                        <!-- =====================================
                             TAB PRINCIPALES
                        ====================================== -->

                        <div
                            class="tab-pane fade show active"
                            id="preguntas-principales"
                            role="tabpanel">


                            <div class="qa-table-shell">

                                <div class="table-responsive">

                                    <table
                                        class="table table-hover mb-0"
                                        id="tablaPreguntasPrincipales">

                                        <thead>

                                            <tr>

                                                <th style="width:70px;">
                                                    Orden
                                                </th>

                                                <th>
                                                    Código
                                                </th>

                                                <th>
                                                    Texto de la Pregunta
                                                </th>

                                                <th>
                                                    Opciones
                                                </th>

                                                <th>
                                                    Condición
                                                </th>

                                                <th>
                                                    Estado
                                                </th>

                                                <th style="width:150px;">
                                                    Acciones
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody
                                            id="tbodyPreguntasPrincipales">

                                            <tr>

                                                <td colspan="7">

                                                    <div class="qa-loading">

                                                        <div class="qa-loading-orb">

                                                            <i class="fas fa-spinner fa-spin"></i>

                                                        </div>

                                                        <span>
                                                            Cargando preguntas principales…
                                                        </span>

                                                    </div>

                                                </td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             TAB SUBPREGUNTAS
                        ====================================== -->

                        <div
                            class="tab-pane fade"
                            id="subpreguntas"
                            role="tabpanel">


                            <div class="qa-table-shell">

                                <div class="table-responsive">

                                    <table
                                        class="table table-hover mb-0"
                                        id="tablaSubpreguntas">

                                        <thead>

                                            <tr>

                                                <th style="width:70px;">
                                                    Orden
                                                </th>

                                                <th>
                                                    Código
                                                </th>

                                                <th>
                                                    Texto de la Subpregunta
                                                </th>

                                                <th>
                                                    Pregunta Padre
                                                </th>

                                                <th>
                                                    Estado
                                                </th>

                                                <th style="width:150px;">
                                                    Acciones
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody
                                            id="tbodySubpreguntas">

                                            <tr>

                                                <td colspan="6">

                                                    <div class="qa-loading">

                                                        <div class="qa-loading-orb">

                                                            <i class="fas fa-spinner fa-spin"></i>

                                                        </div>

                                                        <span>
                                                            Cargando subpreguntas…
                                                        </span>

                                                    </div>

                                                </td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             TAB PREVIEW
                        ====================================== -->

                        <div
                            class="tab-pane fade"
                            id="preview"
                            role="tabpanel">


                            <div class="qa-preview-banner">

                                <div class="qa-preview-banner-icon">

                                    <i class="fas fa-eye"></i>

                                </div>


                                <div>

                                    <strong>
                                        Vista previa del estudio de votaciones
                                    </strong>

                                    <span>
                                        Revisa cómo se presentan las preguntas,
                                        opciones y relaciones antes de utilizarlas
                                        dentro de la experiencia final.
                                    </span>

                                </div>

                            </div>


                            <div id="previewContenido">

                                <div class="qa-loading">

                                    <div class="qa-loading-orb">

                                        <i class="fas fa-spinner fa-spin"></i>

                                    </div>

                                    <span>
                                        Generando vista previa…
                                    </span>

                                </div>

                            </div>

                        </div>


                    </div>

                </div>

            </section>


        </div>

    </div>


    <?php include './include/footer.php'; ?>

</main>


<!-- ==========================================================
     MODAL: CREAR / EDITAR PREGUNTA
========================================================== -->

<div
    class="modal fade qa-modal"
    id="modalPregunta"
    tabindex="-1"
    aria-hidden="true">


    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">


        <div class="modal-content">


            <div class="modal-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="qa-modal-icon">

                        <i class="fas fa-question-circle"></i>

                    </div>


                    <div>

                        <h5
                            class="modal-title"
                            id="tituloModalPregunta">

                            <i class="fas fa-plus-circle me-2"></i>

                            Nueva Pregunta

                        </h5>


                        <div class="qa-modal-sub">

                            Configura identificación, opciones, condiciones y grillas asociadas.

                        </div>

                    </div>

                </div>


                <button
                    type="button"
                    class="btn p-2"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="fas fa-times fs-5 text-white"></i>

                </button>

            </div>


            <div
                class="modal-body"
                style="
                    max-height:72vh;
                    overflow-y:auto;
                ">


                <form
                    id="formPregunta"
                    autocomplete="off">


                    <input
                        type="hidden"
                        name="id"
                        id="preguntaId"
                        value="0">


                    <input
                        type="hidden"
                        name="op"
                        id="preguntaOp"
                        value="preguntasgrillasave">


                    <!-- =========================================
                         1. IDENTIFICACIÓN
                    ========================================== -->

                    <section class="qa-modal-section">

                        <div class="qa-modal-section-title">

                            <i class="fas fa-fingerprint"></i>

                            Identificación de la pregunta

                        </div>


                        <div class="row g-3">


                            <div class="col-12 col-md-6">

                                <label
                                    for="tipoPregunta"
                                    class="form-label">

                                    Tipo
                                    <span class="text-danger">*</span>

                                </label>


                                <select
                                    class="form-select"
                                    id="tipoPregunta"
                                    name="tipo_pregunta"
                                    required>

                                    <option value="pregunta">
                                        Pregunta Principal
                                    </option>

                                    <option value="subpregunta">
                                        Subpregunta
                                    </option>

                                </select>


                                <small>
                                    Las preguntas principales se realizan por cada candidato.
                                </small>

                            </div>


                            <div class="col-12 col-md-6">

                                <label
                                    for="codigoPregunta"
                                    class="form-label">

                                    Código Único
                                    <span class="text-danger">*</span>

                                </label>


                                <input
                                    type="text"
                                    class="form-control"
                                    id="codigoPregunta"
                                    name="codigo_pregunta"
                                    placeholder="Ej: conoce, imagen, votaria"
                                    required>


                                <small>
                                    Identificador único utilizado por la lógica del sistema.
                                </small>

                            </div>


                            <div class="col-12">

                                <label
                                    for="textoPregunta"
                                    class="form-label">

                                    Texto de la Pregunta
                                    <span class="text-danger">*</span>

                                </label>


                                <textarea
                                    class="form-control"
                                    id="textoPregunta"
                                    name="texto_pregunta"
                                    rows="3"
                                    placeholder="Ej: ¿CONOCE O NO LO CONOCE?"
                                    required></textarea>

                            </div>


                            <div class="col-12 col-md-4">

                                <label
                                    for="ordenPregunta"
                                    class="form-label">

                                    Orden
                                    <span class="text-danger">*</span>

                                </label>


                                <input
                                    type="number"
                                    class="form-control"
                                    id="ordenPregunta"
                                    name="orden"
                                    min="1"
                                    value="1"
                                    required>

                            </div>


                            <div
                                class="col-12 col-md-8"
                                id="contenedorPreguntaPadre"
                                style="display:none;">

                                <label
                                    for="preguntaPadreId"
                                    class="form-label">

                                    Pregunta Principal Asociada

                                </label>


                                <select
                                    class="form-select"
                                    id="preguntaPadreId"
                                    name="pregunta_padre_id">

                                    <option value="">
                                        Ninguna
                                    </option>

                                </select>

                            </div>


                        </div>

                    </section>


                    <!-- =========================================
                         2. OPCIONES
                    ========================================== -->

                    <section
                        class="qa-modal-section"
                        id="contenedorOpciones">

                        <div class="qa-modal-section-title">

                            <i class="fas fa-list-check"></i>

                            Opciones de respuesta

                        </div>


                        <label
                            for="opcionesRespuesta"
                            class="form-label">

                            Opciones de Respuesta (JSON)

                        </label>


                        <input
                            type="text"
                            class="form-control font-monospace"
                            id="opcionesRespuesta"
                            name="opciones_respuesta"
                            placeholder='["si", "no"]'>


                        <small class="d-block mt-2">

                            Ejemplos válidos:
                            ["si", "no"] o ["favorable", "desfavorable"].

                        </small>

                    </section>


                    <!-- =========================================
                         3. LÓGICA CONDICIONAL
                    ========================================== -->

                    <section class="qa-modal-section">

                        <div class="qa-modal-section-title">

                            <i class="fas fa-code-branch"></i>

                            Lógica y comportamiento

                        </div>


                        <div class="row g-3">


                            <div
                                class="col-12 col-lg-6"
                                id="contenedorCondicion">

                                <label
                                    for="condicionHabilitacion"
                                    class="form-label">

                                    Condición de Habilitación

                                </label>


                                <select
                                    class="form-select"
                                    id="condicionHabilitacion"
                                    name="condicion_habilitacion">

                                    <option value="">
                                        Ninguna
                                    </option>

                                    <option value="si">
                                        Si responde SÍ
                                    </option>

                                    <option value="favorable">
                                        Si responde FAVORABLE
                                    </option>

                                    <option value="todas_si">
                                        Si todas las anteriores son SÍ
                                    </option>

                                </select>

                            </div>


                            <div class="col-12">

                                <div class="qa-switch-grid">


                                    <div
                                        class="qa-switch-card"
                                        id="contenedorHabilitaSubpreguntas">

                                        <div class="qa-switch-copy">

                                            <strong>
                                                Habilita siguientes preguntas
                                            </strong>

                                            <span>
                                                Permite abrir preguntas dependientes al responder.
                                            </span>

                                        </div>


                                        <div class="form-check form-switch">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="habilitaSubpreguntas"
                                                name="habilita_subpreguntas"
                                                value="1">

                                        </div>

                                    </div>


                                    <div
                                        class="qa-switch-card"
                                        id="contenedorRequiereTodasSi">

                                        <div class="qa-switch-copy">

                                            <strong>
                                                Requiere todas anteriores en SÍ
                                            </strong>

                                            <span>
                                                La regla se cumple solo si las anteriores son afirmativas.
                                            </span>

                                        </div>


                                        <div class="form-check form-switch">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="requiereTodasSi"
                                                name="requiere_todas_si"
                                                value="1">

                                        </div>

                                    </div>


                                    <div
                                        class="qa-switch-card"
                                        id="contenedorActivaSubpreguntas">

                                        <div class="qa-switch-copy">

                                            <strong>
                                                Activa sección de subpreguntas
                                            </strong>

                                            <span>
                                                Muestra la sección PA, PB, PC... cuando aplique.
                                            </span>

                                        </div>


                                        <div class="form-check form-switch">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="activaSeccionSubpreguntas"
                                                name="activa_seccion_subpreguntas"
                                                value="1">

                                        </div>

                                    </div>


                                    <div class="qa-switch-card">

                                        <div class="qa-switch-copy">

                                            <strong>
                                                Pregunta activa
                                            </strong>

                                            <span>
                                                Define si estará visible en la interfaz.
                                            </span>

                                        </div>


                                        <div class="form-check form-switch">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="habilitado"
                                                name="habilitado"
                                                value="1"
                                                checked>

                                        </div>

                                    </div>


                                </div>

                            </div>


                        </div>

                    </section>


                    <!-- =========================================
                         4. GRILLAS
                    ========================================== -->

                    <section
                        class="qa-modal-section"
                        id="contenedorGrillasAsociadas">

                        <div class="qa-modal-section-title">

                            <i class="fas fa-table"></i>

                            Grillas asociadas

                        </div>


                        <label
                            for="grillasAsociadas"
                            class="form-label">

                            Selecciona una o varias grillas

                        </label>


                        <select
                            class="form-select"
                            id="grillasAsociadas"
                            name="grillas_asociadas[]"
                            multiple="multiple">

                            <?= $optionGrillas ?>

                        </select>


                        <small class="d-block mt-2">

                            Si no seleccionas ninguna, la pregunta será global.

                        </small>

                    </section>


                </form>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="qa-btn qa-btn-soft"
                    data-bs-dismiss="modal">

                    <i class="fas fa-times"></i>

                    Cancelar

                </button>


                <button
                    type="button"
                    class="qa-btn qa-btn-primary"
                    id="btnGuardarPregunta">

                    <i class="fas fa-save"></i>

                    Guardar Pregunta

                </button>

            </div>

        </div>

    </div>

</div>


<!-- ==========================================================
     REQUIRED JS
     Se conserva el flujo funcional.
========================================================== -->

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>

<script src="assets/js/plugins/bootstrap.min.js"></script>

<script src="assets/js/pcoded.min.js"></script>

<!-- Choices debe estar disponible antes de preguntas_grilla.js -->
<script src="admin/js/lib/choices.min.js"></script>

<script
    type="text/javascript"
    src="admin/js/preguntas_grilla.js">
</script>


<script>
/* ============================================================
   UI AUXILIAR
   No reemplaza ni interfiere con PREGUNTAS_GRILLA.
============================================================ */

(function(){

    "use strict";


    /* --------------------------------------------------------
       INIT ORIGINAL
       Se conserva exactamente el punto de entrada del módulo.
    --------------------------------------------------------- */

    /*
       IMPORTANTE:
       Se llama DIRECTAMENTE, igual que en el archivo original.
       Si preguntas_grilla.js declara PREGUNTAS_GRILLA con const/let,
       no necesariamente existe como window.PREGUNTAS_GRILLA.
    */
    PREGUNTAS_GRILLA.init();


    /* --------------------------------------------------------
       KPIs dinámicos.
       Solo observan los TBODY que ya llena preguntas_grilla.js.
    --------------------------------------------------------- */

    function countRealRows(tbodyId){

        var tbody =
            document.getElementById(
                tbodyId
            );

        if (!tbody) {
            return 0;
        }

        var rows =
            Array.prototype.slice.call(
                tbody.children
            ).filter(
                function(node){
                    return node.tagName === "TR";
                }
            );

        return rows.filter(
            function(row){

                var colspan =
                    row.querySelector(
                        'td[colspan]'
                    );

                return !colspan;

            }
        ).length;

    }


    function updateQuestionKpis(){

        var principales =
            document.getElementById(
                "qaKpiPrincipales"
            );

        var subpreguntas =
            document.getElementById(
                "qaKpiSubpreguntas"
            );

        if (principales) {

            principales.textContent =
                countRealRows(
                    "tbodyPreguntasPrincipales"
                );

        }

        if (subpreguntas) {

            subpreguntas.textContent =
                countRealRows(
                    "tbodySubpreguntas"
                );

        }

    }


    [
        "tbodyPreguntasPrincipales",
        "tbodySubpreguntas"
    ]
    .forEach(
        function(id){

            var el =
                document.getElementById(id);

            if (
                el
                &&
                window.MutationObserver
            ) {

                new MutationObserver(
                    updateQuestionKpis
                )
                .observe(
                    el,
                    {
                        childList:true,
                        subtree:true
                    }
                );

            }

        }
    );


    updateQuestionKpis();


    /* --------------------------------------------------------
       UX: foco suave al abrir una nueva pregunta.
       El click real sigue siendo manejado por preguntas_grilla.js.
    --------------------------------------------------------- */

    var btnNueva =
        document.getElementById(
            "btnNuevaPregunta"
        );

    if (btnNueva) {

        btnNueva.addEventListener(
            "click",
            function(){

                setTimeout(
                    function(){

                        var codigo =
                            document.getElementById(
                                "codigoPregunta"
                            );

                        if (codigo) {
                            codigo.focus();
                        }

                    },
                    380
                );

            }
        );

    }

})();
</script>


<?php include './admin/include/generic_dataTables.php'; ?>

<?php include 'admin/include/scriptsgober360.php'; ?>


</body>

</html>
