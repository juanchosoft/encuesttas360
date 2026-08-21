<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Sondeo.php';

// Permisos
$view    = SessionData::getPermission(34);
$create  = SessionData::getPermission(35);
$edit    = SessionData::getPermission(36);
$permits = SessionData::getPermission(37);

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Sondeos
$arrResponse = Sondeo::getAll(null);
$isvalidSondeo = $arrResponse['output']['valid'] ?? false;
$arr = $arrResponse['output']['response'] ?? [];

$modulo = 'Ingreso de Información de Sondeos';

// Cargos públicos
$arrCargosPubResp = CargosPublicos::getAll(null);
$arrCargosPub = $arrCargosPubResp['output']['response'] ?? [];

$optionCargosPub = "";

foreach ($arrCargosPub as $val) {

    $id = htmlspecialchars(
        $val['id'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $nm = htmlspecialchars(
        $val['nombre'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $optionCargosPub .=
        "<option value='{$id}'>{$nm}</option>";
}

// Departamentos
$arrDepResp = Departamento::getAll(null);
$arrDep = $arrDepResp['output']['response'] ?? [];

$optionDep = Util::getDepartamentoPrincipal();

foreach ($arrDep as $val) {

    $cd = htmlspecialchars(
        $val['codigo_departamento'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $dp = htmlspecialchars(
        $val['departamento'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $sel =
        ($val["codigo_departamento"] == Util::getDepartamentoPrincipal())
            ? "selected"
            : "";

    $optionDep .=
        "<option {$sel} value='{$cd}'>{$cd} - {$dp}</option>";
}

function h($s){
    return htmlspecialchars(
        (string)$s,
        ENT_QUOTES,
        'UTF-8'
    );
}

// KPIs
$totalSondeos = is_array($arr) ? count($arr) : 0;
$totalVigentes = 0;
$totalActivos = 0;
$totalConCandidatos = 0;

if (is_array($arr)) {

    foreach ($arr as $item) {

        if (!empty($item['vigente'])) {
            $totalVigentes++;
        }

        if (($item['habilitado'] ?? '') === 'si') {
            $totalActivos++;
        }

        if (
            ($item['aplica_cargos_publicos'] ?? '') === 'si'
            &&
            !empty($item['candidatos'])
        ) {
            $totalConCandidatos++;
        }
    }
}

?>

<!DOCTYPE html>
<html
    lang="es"
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

    <style>
    /* ==========================================================
       ESTADÍSTICA360 · SURVEY INTELLIGENCE STUDIO
    ========================================================== */

    :root{
        --s360-navy:#071B3B;
        --s360-navy-2:#0C2D5E;
        --s360-brand:#20427F;
        --s360-brand-2:#3269C8;
        --s360-blue:#4F8CFF;
        --s360-cyan:#10B6DC;

        --s360-success:#12B981;
        --s360-warning:#F59E0B;
        --s360-danger:#E5484D;
        --s360-violet:#7C5CFC;

        --s360-page:#F3F6FB;
        --s360-card:#FFFFFF;
        --s360-soft-card:#F8FAFD;

        --s360-text:#101828;
        --s360-text-2:#344054;
        --s360-muted:#667085;
        --s360-soft:#98A2B3;

        --s360-line:#E6EBF2;

        --s360-r-xxl:30px;
        --s360-r-xl:24px;
        --s360-r-lg:18px;
        --s360-r-md:14px;

        --s360-shadow:
            0 22px 60px
            rgba(15,23,42,.09);

        --s360-shadow-soft:
            0 12px 32px
            rgba(15,23,42,.065);
    }

    *{
        box-sizing:border-box;
    }

    html{
        scroll-behavior:smooth;
    }

    body.sondeo-page{
        margin:0;

        background:
            radial-gradient(
                860px 460px at 4% -4%,
                rgba(49,104,200,.12),
                transparent 64%
            ),
            radial-gradient(
                760px 430px at 104% 8%,
                rgba(16,182,220,.075),
                transparent 64%
            ),
            linear-gradient(
                180deg,
                #F7F9FC 0%,
                #F2F5FA 100%
            );

        color:
            var(--s360-text);

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

    body.sondeo-page::before{
        content:"";

        position:fixed;
        inset:0;

        z-index:-1;

        pointer-events:none;

        opacity:.32;

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

    /* ==========================================================
       CONTENIDO
       IMPORTANTE:
       No se calcula padding según .navbar para evitar espacios enormes.
    ========================================================== */

    .content{
        padding-top:
            18px !important;

        padding-bottom:
            38px !important;

        margin-top:
            0 !important;
    }

    .sondeo-shell{
        width:100%;

        max-width:1660px;

        margin:
            0 auto;

        padding:
            0 18px;
    }

    /* ==========================================================
       HERO
    ========================================================== */

    .sondeo-hero{
        position:relative;
        isolation:isolate;

        overflow:hidden;

        min-height:220px;

        padding:
            28px 30px;

        margin-bottom:
            16px;

        border:
            1px solid
            rgba(255,255,255,.12);

        border-radius:
            var(--s360-r-xxl);

        color:#fff;

        background:
            radial-gradient(
                520px 260px at 10% 2%,
                rgba(79,140,255,.34),
                transparent 65%
            ),
            radial-gradient(
                470px 250px at 92% 10%,
                rgba(16,182,220,.22),
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

    .sondeo-hero::before{
        content:"";

        position:absolute;

        width:420px;
        height:420px;

        right:-150px;
        top:-210px;

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
            0 0 0 130px
            rgba(255,255,255,.011);
    }

    .sondeo-hero-grid{
        display:grid;

        grid-template-columns:
            minmax(0,1fr)
            auto;

        gap:28px;

        align-items:center;
    }

    .sondeo-eyebrow{
        display:inline-flex;

        align-items:center;

        gap:8px;

        min-height:32px;

        padding:
            7px 11px;

        margin-bottom:13px;

        border:
            1px solid
            rgba(255,255,255,.14);

        border-radius:999px;

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

    .sondeo-live{
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

    .sondeo-hero h1{
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

    .sondeo-hero h1 span{
        color:#A9C7FF;
    }

    .sondeo-hero p{
        max-width:800px;

        margin:
            10px 0 0;

        color:
            rgba(255,255,255,.70);

        font-size:.91rem;

        line-height:1.65;

        font-weight:500;
    }

    .sondeo-hero-pills{
        display:flex;

        gap:8px;

        flex-wrap:wrap;

        margin-top:18px;
    }

    .sondeo-hero-pill{
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

    .sondeo-hero-pill i{
        color:#9EC2FF;
    }

    /* ==========================================================
       HERO KPIs
    ========================================================== */

    .sondeo-metrics{
        display:grid;

        grid-template-columns:
            repeat(
                4,
                minmax(92px,1fr)
            );

        gap:9px;

        min-width:530px;
    }

    .sondeo-metric{
        min-height:110px;

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

    .sondeo-metric:hover{
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

    .sondeo-metric i{
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

    .sondeo-metric strong{
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

    .sondeo-metric span{
        display:block;

        margin-top:5px;

        color:
            rgba(255,255,255,.58);

        font-size:.60rem;

        line-height:1.25;

        font-weight:700;
    }

    /* ==========================================================
       TOOLBAR
    ========================================================== */

    .sondeo-toolbar{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        padding:
            13px 15px;

        margin-bottom:16px;

        border:
            1px solid
            var(--s360-line);

        border-radius:18px;

        background:
            rgba(255,255,255,.92);

        box-shadow:
            var(--s360-shadow-soft);

        backdrop-filter:
            blur(12px);
    }

    .sondeo-toolbar-copy{
        display:flex;

        align-items:center;

        gap:10px;

        min-width:0;
    }

    .sondeo-toolbar-icon{
        width:38px;
        height:38px;

        flex:
            0 0 38px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:12px;

        color:
            var(--s360-brand);

        background:#EDF4FF;

        font-size:.9rem;
    }

    .sondeo-toolbar-copy strong{
        display:block;

        color:
            var(--s360-text);

        font-size:.79rem;

        font-weight:800;
    }

    .sondeo-toolbar-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--s360-soft);

        font-size:.66rem;

        font-weight:600;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .sondeo-btn{
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

    .sondeo-btn-primary{
        border:0;

        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--s360-blue),
                var(--s360-brand-2) 48%,
                var(--s360-brand)
            );

        box-shadow:
            0 11px 23px
            rgba(32,66,127,.22);
    }

    .sondeo-btn-primary:hover{
        transform:
            translateY(-2px);

        box-shadow:
            0 16px 30px
            rgba(32,66,127,.29);
    }

    .sondeo-btn-soft{
        border:
            1px solid
            #D7E2F2;

        color:
            var(--s360-brand) !important;

        background:#fff;
    }

    .sondeo-btn-soft:hover{
        transform:
            translateY(-1px);

        border-color:#BFD2EC;

        background:#F5F9FF;
    }

    /* ==========================================================
       CARD
    ========================================================== */

    .sondeo-card{
        overflow:hidden;

        margin-bottom:16px;

        border:
            1px solid
            var(--s360-line);

        border-radius:
            var(--s360-r-xl);

        background:
            rgba(255,255,255,.96);

        box-shadow:
            var(--s360-shadow-soft);
    }

    .sondeo-card-header{
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

    .sondeo-card-title-wrap{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .sondeo-card-icon{
        width:40px;
        height:40px;

        flex:
            0 0 40px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:13px;

        color:
            var(--s360-brand);

        background:#EDF4FF;

        font-size:.92rem;
    }

    .sondeo-card-title{
        margin:0;

        color:#182230;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:.96rem;

        font-weight:800;
    }

    .sondeo-card-subtitle{
        margin:
            3px 0 0;

        color:
            var(--s360-soft);

        font-size:.66rem;

        font-weight:600;
    }

    .sondeo-card-body{
        padding:18px;
    }

    /* ==========================================================
       SECTIONS
    ========================================================== */

    .sondeo-section{
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

    .sondeo-section + .sondeo-section{
        margin-top:13px;
    }

    .sondeo-section-heading{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        margin-bottom:14px;
    }

    .sondeo-section-heading-left{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .sondeo-section-dot{
        width:9px;
        height:9px;

        border-radius:50%;

        background:
            linear-gradient(
                135deg,
                var(--s360-blue),
                var(--s360-brand)
            );

        box-shadow:
            0 0 0 4px
            rgba(79,140,255,.09);
    }

    .sondeo-section-heading h3{
        margin:0;

        color:
            var(--s360-text);

        font-size:.79rem;

        font-weight:800;
    }

    .sondeo-section-help{
        color:
            var(--s360-soft);

        font-size:.62rem;

        font-weight:600;
    }

    /* ==========================================================
       INPUTS
    ========================================================== */

    .form-floating>.form-control,
    .form-floating>.form-select{
        min-height:58px;

        border:
            1px solid
            #D9E0EA !important;

        border-radius:
            14px !important;

        color:
            var(--s360-text-2);

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
        border-color:
            #BCC8D9 !important;

        background:#fff;
    }

    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus{
        border-color:
            var(--s360-blue) !important;

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

    /* ==========================================================
       SWITCH CARD
    ========================================================== */

    .sondeo-switch-card{
        min-height:58px;

        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        padding:
            9px 12px;

        border:
            1px solid
            #D9E0EA;

        border-radius:14px;

        background:#FBFCFE;
    }

    .sondeo-switch-copy strong{
        display:block;

        color:
            var(--s360-text-2);

        font-size:.71rem;

        font-weight:800;
    }

    .sondeo-switch-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--s360-soft);

        font-size:.59rem;

        font-weight:600;
    }

    .sondeo-switch-card
    .form-check-input{
        width:42px !important;
        height:23px !important;

        margin:0 !important;

        cursor:pointer;
    }

    .sondeo-switch-card
    .form-check-input:checked{
        border-color:
            var(--s360-success);

        background-color:
            var(--s360-success);
    }

    /* ==========================================================
       OPTIONS / CANDIDATES PANELS
    ========================================================== */

    .sondeo-builder{
        position:relative;

        overflow:hidden;

        margin-top:13px;

        padding:16px;

        border:
            1px solid
            #E5EBF3;

        border-radius:18px;

        background:
            radial-gradient(
                280px 120px at 4% 0%,
                rgba(79,140,255,.07),
                transparent 70%
            ),
            linear-gradient(
                180deg,
                #FBFDFF,
                #F8FAFD
            );
    }

    .sondeo-builder-head{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        margin-bottom:12px;
    }

    .sondeo-builder-title{
        display:flex;

        align-items:center;

        gap:10px;
    }

    .sondeo-builder-icon{
        width:38px;
        height:38px;

        flex:
            0 0 38px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:12px;

        color:
            var(--s360-brand);

        background:#EEF5FF;

        font-size:.84rem;
    }

    .sondeo-builder-title strong{
        display:block;

        color:
            var(--s360-text-2);

        font-size:.75rem;

        font-weight:800;
    }

    .sondeo-builder-title span{
        display:block;

        margin-top:2px;

        color:
            var(--s360-soft);

        font-size:.60rem;

        font-weight:600;
    }

    #opcionesContainer{
        position:relative;

        z-index:1;
    }

    #noOptionsMessage{
        margin:8px 0 0 !important;

        padding:12px;

        border:
            1px dashed
            #D6DFEB;

        border-radius:12px;

        color:
            var(--s360-soft) !important;

        background:#fff;

        font-size:.66rem;

        font-weight:650;
    }

    #addOptionBtn{
        min-height:38px;

        border:
            1px solid
            #D8E4F4 !important;

        border-radius:11px !important;

        color:
            var(--s360-brand) !important;

        background:#fff !important;

        font-size:.68rem;

        font-weight:800;

        box-shadow:
            0 6px 14px
            rgba(15,23,42,.05);
    }

    /* ==========================================================
       CANDIDATES TABLE
    ========================================================== */

    #candidatosTable{
        width:100%;

        margin:0 !important;

        border-collapse:
            separate !important;

        border-spacing:
            0 6px !important;
    }

    #candidatosTable thead th{
        padding:
            9px 10px !important;

        border:0 !important;

        color:
            #667085 !important;

        background:
            transparent !important;

        font-size:
            .59rem !important;

        font-weight:
            800 !important;

        text-transform:uppercase;

        white-space:nowrap;
    }

    #candidatosTable tbody td{
        padding:
            9px 10px !important;

        border-top:
            1px solid
            #E8EDF4 !important;

        border-bottom:
            1px solid
            #E8EDF4 !important;

        background:#fff !important;

        color:#344054;

        font-size:.67rem;

        vertical-align:middle;
    }

    #candidatosTable tbody td:first-child{
        border-left:
            1px solid
            #E8EDF4 !important;

        border-radius:
            12px 0 0 12px;
    }

    #candidatosTable tbody td:last-child{
        border-right:
            1px solid
            #E8EDF4 !important;

        border-radius:
            0 12px 12px 0;
    }

    #candidatosTable tbody tr:hover td{
        background:
            linear-gradient(
                90deg,
                #F7FAFF,
                #FFFFFF
            ) !important;
    }

    /* ==========================================================
       ACTION BAR
    ========================================================== */

    .sondeo-action-bar{
        position:sticky;

        bottom:12px;

        z-index:20;

        margin-top:15px;
    }

    .sondeo-action-inner{
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

    .sondeo-action-copy{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .sondeo-action-state{
        width:34px;
        height:34px;

        flex:
            0 0 34px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:11px;

        color:#07845E;

        background:#ECFDF5;

        font-size:.76rem;
    }

    .sondeo-action-copy strong{
        display:block;

        color:
            var(--s360-text-2);

        font-size:.69rem;

        font-weight:800;
    }

    .sondeo-action-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--s360-soft);

        font-size:.61rem;

        font-weight:600;
    }

    /* ==========================================================
       DIRECTORY
    ========================================================== */

    .sondeo-directory{
        overflow:hidden;

        border:
            1px solid
            var(--s360-line);

        border-radius:
            var(--s360-r-xl);

        background:#fff;

        box-shadow:
            var(--s360-shadow);
    }

    .sondeo-directory-head{
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

    .sondeo-directory-head-left{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .sondeo-directory-icon{
        width:43px;
        height:43px;

        flex:
            0 0 43px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:13px;

        color:#fff;

        background:
            linear-gradient(
                135deg,
                var(--s360-blue),
                var(--s360-brand)
            );

        box-shadow:
            0 10px 22px
            rgba(32,66,127,.20);

        font-size:.92rem;
    }

    .sondeo-directory h2{
        margin:0;

        color:
            var(--s360-text);

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;

        font-weight:800;
    }

    .sondeo-directory p{
        margin:
            3px 0 0;

        color:
            var(--s360-soft);

        font-size:.66rem;

        font-weight:600;
    }

    .sondeo-count{
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

    .sondeo-table-body{
        padding:15px;
    }

    /* ==========================================================
       TABLE
    ========================================================== */

    .sondeo-directory table{
        width:100% !important;

        margin:0 !important;

        border-collapse:
            separate !important;

        border-spacing:
            0 7px !important;
    }

    .sondeo-directory
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

    .sondeo-directory
    table tbody td,
    .sondeo-directory
    table tbody th{
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

    .sondeo-directory
    table tbody tr > *:first-child{
        border-left:
            1px solid
            #E9EDF4 !important;

        border-radius:
            13px 0 0 13px;
    }

    .sondeo-directory
    table tbody tr > *:last-child{
        border-right:
            1px solid
            #E9EDF4 !important;

        border-radius:
            0 13px 13px 0;
    }

    .sondeo-directory
    table tbody tr{
        transition:
            transform .18s ease;
    }

    .sondeo-directory
    table tbody tr:hover{
        transform:
            translateY(-2px);
    }

    .sondeo-directory
    table tbody tr:hover td,
    .sondeo-directory
    table tbody tr:hover th{
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

    /* status */
    .sondeo-status{
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

    .sondeo-status-success{
        color:#06795B;

        border:
            1px solid
            #D1FAE5;

        background:#ECFDF5;
    }

    .sondeo-status-neutral{
        color:#475467;

        border:
            1px solid
            #EAECF0;

        background:#F9FAFB;
    }

    .sondeo-status-danger{
        color:#B42318;

        border:
            1px solid
            #FEE4E2;

        background:#FEF3F2;
    }

    .sondeo-status-blue{
        color:#175CD3;

        border:
            1px solid
            #D1E9FF;

        background:#EFF8FF;
    }

    .sondeo-name{
        min-width:230px;

        color:#1D2939;

        font-weight:800 !important;
    }

    /* actions */
    .sondeo-icon-btn{
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

        color:#fff !important;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .sondeo-icon-btn:hover{
        transform:
            translateY(-2px);
    }

    .sondeo-edit{
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

    .sondeo-candidates{
        background:
            linear-gradient(
                135deg,
                #2DB783,
                #08785C
            ) !important;

        box-shadow:
            0 8px 16px
            rgba(8,120,92,.15);
    }

    /* ==========================================================
       DATATABLES
    ========================================================== */

    .dataTables_wrapper{
        width:100% !important;

        color:
            var(--s360-muted);

        font-size:.71rem;
    }

    .dataTables_wrapper .row{
        margin-left:0 !important;
        margin-right:0 !important;

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

        background:#fff;

        outline:none;
    }

    .dataTables_wrapper
    .dataTables_filter input:focus{
        border-color:
            var(--s360-blue);

        box-shadow:
            0 0 0 4px
            rgba(79,140,255,.10);
    }

    .dataTables_wrapper
    .dataTables_length select{
        min-height:38px;

        border:
            1px solid
            #D7DEE9;

        border-radius:10px;

        background:#fff;
    }

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
    .paginate_button.current,
    .dataTables_wrapper
    .dataTables_paginate
    .paginate_button.current:hover{
        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--s360-blue),
                var(--s360-brand)
            ) !important;

        box-shadow:
            0 8px 18px
            rgba(32,66,127,.20) !important;
    }

    /* ==========================================================
       MODAL
    ========================================================== */

    .sondeo-modal
    .modal-content{
        overflow:hidden;

        border:
            1px solid
            rgba(15,23,42,.09);

        border-radius:
            24px;

        box-shadow:
            0 30px 78px
            rgba(15,23,42,.25);
    }

    .sondeo-modal
    .modal-header{
        position:relative;

        overflow:hidden;

        padding:
            18px 20px;

        border-bottom:0;

        color:#fff;

        background:
            radial-gradient(
                400px 180px at 5% 0%,
                rgba(79,140,255,.28),
                transparent 72%
            ),
            linear-gradient(
                135deg,
                #173D79,
                #102A56 55%,
                #081B38
            );
    }

    .sondeo-modal
    .modal-title{
        margin:0;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;

        font-weight:800;
    }

    .sondeo-modal-sub{
        margin-top:4px;

        color:
            rgba(255,255,255,.65);

        font-size:.64rem;

        font-weight:600;
    }

    .sondeo-modal
    .modal-body{
        padding:18px;

        background:
            linear-gradient(
                180deg,
                #FFFFFF,
                #F8FAFD
            );
    }

    .sondeo-modal
    #candidatosModalTable{
        width:100%;

        margin:0;
    }

    .sondeo-modal
    #candidatosModalTable thead th{
        color:#667085;

        font-size:.60rem;

        font-weight:800;

        text-transform:uppercase;
    }

    .sondeo-modal
    #candidatosModalTable tbody td{
        font-size:.68rem;

        vertical-align:middle;
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){

        .sondeo-hero-grid{
            grid-template-columns:
                1fr;
        }

        .sondeo-metrics{
            min-width:0;

            width:100%;
        }
    }

    @media (max-width:991px){

        .sondeo-shell{
            padding:
                0 13px;
        }

        .sondeo-hero{
            padding:23px;
        }
    }

    @media (max-width:767px){

        .content{
            padding-top:
                12px !important;
        }

        .sondeo-shell{
            padding:
                0 10px;
        }

        .sondeo-hero{
            min-height:0;

            padding:
                20px 17px;

            border-radius:22px;
        }

        .sondeo-hero h1{
            font-size:1.8rem;
        }

        .sondeo-hero p{
            font-size:.80rem;
        }

        .sondeo-metrics{
            grid-template-columns:
                repeat(2,1fr);
        }

        .sondeo-toolbar{
            align-items:flex-start;

            flex-direction:column;
        }

        .sondeo-toolbar
        .sondeo-btn{
            width:100%;
        }

        .sondeo-card{
            border-radius:19px;
        }

        .sondeo-card-header{
            padding:14px;
        }

        .sondeo-card-body{
            padding:14px;
        }

        .sondeo-section{
            padding:13px;
        }

        .sondeo-builder{
            padding:13px;
        }

        .sondeo-builder-head{
            align-items:flex-start;

            flex-direction:column;
        }

        .sondeo-action-inner{
            align-items:stretch;

            flex-direction:column;
        }

        .sondeo-action-inner
        > .d-flex{
            width:100%;
        }

        .sondeo-action-inner
        .sondeo-btn{
            flex:1;
        }

        .sondeo-directory{
            border-radius:19px;
        }

        .sondeo-directory-head{
            align-items:flex-start;

            padding:14px;
        }

        .sondeo-table-body{
            padding:10px;
        }

        .table-responsive{
            overflow-x:auto;

            -webkit-overflow-scrolling:
                touch;
        }

        .sondeo-directory table{
            min-width:920px;
        }

        #candidatosTable{
            min-width:900px;
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
    }

    @media (max-width:480px){

        .sondeo-metrics{
            gap:7px;
        }

        .sondeo-metric{
            min-height:95px;

            padding:12px;
        }

        .sondeo-metric strong{
            font-size:1.17rem;
        }

        .sondeo-metric span{
            font-size:.57rem;
        }

        .sondeo-action-copy{
            display:none;
        }

        .sondeo-action-inner{
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

</head>

<body class="sondeo-page">

<main
    class="main"
    id="top">

    <?php include './admin/include/navbar.php'; ?>

    <?php include './admin/include/header.php'; ?>


    <div class="content">

        <div class="sondeo-shell">


            <!-- =================================================
                 HERO
            ================================================== -->

            <section class="sondeo-hero">

                <div class="sondeo-hero-grid">

                    <div>

                        <div class="sondeo-eyebrow">

                            <span class="sondeo-live"></span>

                            Estadística360 · Survey Intelligence

                        </div>


                        <h1>

                            Gestión de
                            <span>Sondeos</span>

                        </h1>


                        <p>

                            Diseña sondeos, configura opciones de respuesta,
                            vincula candidatos políticos, controla vigencias y
                            prepara la información que alimentará los análisis
                            estadísticos e inferenciales.

                        </p>


                        <div class="sondeo-hero-pills">

                            <span class="sondeo-hero-pill">

                                <i class="fas fa-list-check"></i>

                                Opciones dinámicas

                            </span>


                            <span class="sondeo-hero-pill">

                                <i class="fas fa-users"></i>

                                Candidatos vinculados

                            </span>


                            <span class="sondeo-hero-pill">

                                <i class="fas fa-chart-line"></i>

                                Inferencia estadística

                            </span>

                        </div>

                    </div>


                    <div class="sondeo-metrics">

                        <div class="sondeo-metric">

                            <i class="fas fa-poll"></i>

                            <strong>
                                <?= (int)$totalSondeos ?>
                            </strong>

                            <span>
                                Sondeos registrados
                            </span>

                        </div>


                        <div class="sondeo-metric">

                            <i class="fas fa-calendar-check"></i>

                            <strong>
                                <?= (int)$totalVigentes ?>
                            </strong>

                            <span>
                                Actualmente vigentes
                            </span>

                        </div>


                        <div class="sondeo-metric">

                            <i class="fas fa-toggle-on"></i>

                            <strong>
                                <?= (int)$totalActivos ?>
                            </strong>

                            <span>
                                Sondeos habilitados
                            </span>

                        </div>


                        <div class="sondeo-metric">

                            <i class="fas fa-user-tie"></i>

                            <strong>
                                <?= (int)$totalConCandidatos ?>
                            </strong>

                            <span>
                                Con candidatos asociados
                            </span>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <section class="sondeo-toolbar">

                <div class="sondeo-toolbar-copy">

                    <div class="sondeo-toolbar-icon">

                        <i class="fas fa-compass"></i>

                    </div>


                    <div>

                        <strong>
                            Constructor de sondeos
                        </strong>

                        <span>
                            Crea un nuevo sondeo o administra los existentes.
                        </span>

                    </div>

                </div>


                <?php if ($create): ?>

                    <button
                        type="button"
                        class="sondeo-btn sondeo-btn-primary"
                        id="btnNuevoSondeo">

                        <i class="fas fa-plus"></i>

                        Nuevo sondeo

                    </button>

                <?php endif; ?>

            </section>


            <span
                id="spanEncuesta"
                class="small text-muted">
            </span>

            <span
                id="spanModulo"
                class="d-none">

                <?= h($modulo) ?>

            </span>


            <!-- =================================================
                 FORM
            ================================================== -->

            <section
                class="sondeo-card"
                id="sondeoFormCard">

                <div class="sondeo-card-header">

                    <div class="sondeo-card-title-wrap">

                        <div class="sondeo-card-icon">

                            <i class="fas fa-pen-ruler"></i>

                        </div>


                        <div>

                            <h2 class="sondeo-card-title">

                                Configuración del sondeo

                            </h2>


                            <p class="sondeo-card-subtitle">

                                Pregunta, tipo, vigencia, opciones y candidatos.

                            </p>

                        </div>

                    </div>

                </div>


                <div class="sondeo-card-body">

                    <form
                        id="formsondeo"
                        role="form"
                        autocomplete="off">


                        <input
                            type="hidden"
                            name="op"
                            id="op">


                        <input
                            type="hidden"
                            name="idSondeo"
                            id="idSondeo">


                        <!-- =====================================
                             INFORMACIÓN PRINCIPAL
                        ====================================== -->

                        <div class="sondeo-section">

                            <div class="sondeo-section-heading">

                                <div class="sondeo-section-heading-left">

                                    <span class="sondeo-section-dot"></span>

                                    <h3>
                                        Información principal
                                    </h3>

                                </div>


                                <span class="sondeo-section-help">

                                    Pregunta y descripción

                                </span>

                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-lg-7">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="sondeo"
                                            name="sondeo"
                                            placeholder="Texto del sondeo"
                                            value=""
                                            required>


                                        <label for="sondeo">

                                            Pregunta
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-lg-5">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="descripcion_sondeo"
                                            name="descripcion_sondeo"
                                            placeholder="Descripción"
                                            value="">


                                        <label for="descripcion_sondeo">

                                            Descripción del Sondeo

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             TIPO Y ANALÍTICA
                        ====================================== -->

                        <div class="sondeo-section">

                            <div class="sondeo-section-heading">

                                <div class="sondeo-section-heading-left">

                                    <span class="sondeo-section-dot"></span>

                                    <h3>
                                        Tipo y configuración analítica
                                    </h3>

                                </div>


                                <span class="sondeo-section-help">

                                    Estructura de respuesta e inferencia

                                </span>

                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-md-6 col-lg-4">

                                    <div class="form-floating">

                                        <select
                                            onchange="OPCIONES.handleTipoPreguntaChange('tipo_sondeo');"
                                            class="form-select"
                                            id="tipo_sondeo"
                                            name="tipo_sondeo"
                                            required>

                                            <option value="No Aplica">
                                                No Aplica
                                            </option>

                                            <option value="Dicotomica">
                                                Dicotómica
                                            </option>

                                            <option value="Grilla">
                                                Tipo Grilla
                                            </option>

                                            <option value="Preguntas_Ordinales">
                                                Preguntas Ordinales
                                            </option>

                                            <option value="Preguntas_Cardinales">
                                                Preguntas Cardinales
                                            </option>

                                            <option value="Seleccion_Multiple_unica_respuesta">
                                                Selección múltiple con única respuesta
                                            </option>

                                        </select>


                                        <label for="tipo_sondeo">

                                            Tipo de Sondeo
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-4">

                                    <div class="form-floating">

                                        <select
                                            class="form-select"
                                            id="tipo_inferenciales"
                                            name="tipo_inferenciales"
                                            required>

                                            <option value="Pronostico">
                                                Pronóstico
                                            </option>

                                            <option value="Tendencia">
                                                Tendencia
                                            </option>

                                            <option value="Probabilidad">
                                                Probabilidad
                                            </option>

                                            <option value="Otro">
                                                Otro
                                            </option>

                                        </select>


                                        <label for="tipo_inferenciales">

                                            Tipo de Inferenciales
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-4">

                                    <div class="form-floating">

                                        <select
                                            class="form-select"
                                            id="aplica_cargos_publicos"
                                            name="aplica_cargos_publicos"
                                            onchange="SONDEO.handleSondeParaCargoPublicoChange();"
                                            required>

                                            <option value="no">
                                                No
                                            </option>

                                            <option value="si">
                                                Sí
                                            </option>

                                        </select>


                                        <label for="aplica_cargos_publicos">

                                            Sondeo para cargo público
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-4 cargo-publico-fields d-none">

                                    <div class="form-floating">

                                        <select
                                            class="form-select"
                                            id="tbl_cargo_publico_id"
                                            name="tbl_cargo_publico_id"
                                            onchange="SONDEO.handleCargoPublicoChange(this);"
                                            required>

                                            <?= $optionCargosPub ?>

                                        </select>


                                        <label for="tbl_cargo_publico_id">

                                            Cargo Público
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-4 departamento-municipio-fields d-none">

                                    <div class="form-floating">

                                        <select
                                            onchange="DEPARTAMENTO.getMunicipios(), SONDEO.filterAndShowData();"
                                            class="form-select"
                                            id="tbl_departamento_id"
                                            name="tbl_departamento_id">

                                            <?= $optionDep ?>

                                        </select>


                                        <label for="tbl_departamento_id">

                                            Departamento

                                        </label>

                                    </div>

                                </div>


                                <div
                                    class="col-12 col-md-6 col-lg-4 departamento-municipio-fields d-none"
                                    id="alcaldia-container">

                                    <div class="form-floating">

                                        <select
                                            onchange="SONDEO.filterAndShowData();"
                                            class="form-select"
                                            id="tbl_municipio_id"
                                            name="tbl_municipio_id">
                                        </select>


                                        <label for="tbl_municipio_id">

                                            Municipio

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             VIGENCIA
                        ====================================== -->

                        <div class="sondeo-section">

                            <div class="sondeo-section-heading">

                                <div class="sondeo-section-heading-left">

                                    <span class="sondeo-section-dot"></span>

                                    <h3>
                                        Vigencia y comportamiento
                                    </h3>

                                </div>


                                <span class="sondeo-section-help">

                                    Fechas y estados operativos

                                </span>

                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-md-6 col-lg-3">

                                    <div class="form-floating">

                                        <input
                                            type="date"
                                            class="form-control"
                                            id="fecha_inicio"
                                            name="fecha_inicio"
                                            placeholder="Fecha inicio"
                                            value="">


                                        <label for="fecha_inicio">

                                            Fecha Inicio

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-3">

                                    <div class="form-floating">

                                        <input
                                            type="date"
                                            class="form-control"
                                            id="fecha_fin"
                                            name="fecha_fin"
                                            placeholder="Fecha fin"
                                            value="">


                                        <label for="fecha_fin">

                                            Fecha Fin

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-3">

                                    <div class="sondeo-switch-card">

                                        <div class="sondeo-switch-copy">

                                            <strong>
                                                Sondeo habilitado
                                            </strong>

                                            <span>
                                                Disponible para uso
                                            </span>

                                        </div>


                                        <div class="form-check form-switch m-0 p-0">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="habilitado"
                                                name="habilitado"
                                                value="si"
                                                checked>

                                        </div>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-3">

                                    <div class="sondeo-switch-card">

                                        <div class="sondeo-switch-copy">

                                            <strong>
                                                Es trivia
                                            </strong>

                                            <span>
                                                Marca si usa modalidad trivia
                                            </span>

                                        </div>


                                        <div class="form-check form-switch m-0 p-0">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="es_trivia"
                                                name="es_trivia"
                                                value="si">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =====================================
                             OPTIONS
                        ====================================== -->

                        <div
                            id="opciones-preguntas"
                            class="sondeo-builder">

                            <div class="sondeo-builder-head">

                                <div class="sondeo-builder-title">

                                    <div class="sondeo-builder-icon">

                                        <i class="fas fa-list"></i>

                                    </div>


                                    <div>

                                        <strong>
                                            Opciones de respuesta
                                        </strong>

                                        <span>
                                            Agrega opciones según el tipo de sondeo.
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div id="opcionesContainer">

                                <p
                                    id="noOptionsMessage"
                                    style="display:none;">

                                    No hay opciones de respuesta. ¡Añade una!

                                </p>

                            </div>


                            <button
                                type="button"
                                id="addOptionBtn"
                                onclick="OPCIONES.addOptionBtnClick();">

                                <span class="fas fa-plus me-1"></span>

                                Añadir Opción

                            </button>

                        </div>


                        <!-- =====================================
                             CANDIDATES
                        ====================================== -->

                        <div
                            id="table-container"
                            class="sondeo-builder">

                            <div class="sondeo-builder-head">

                                <div class="sondeo-builder-title">

                                    <div class="sondeo-builder-icon">

                                        <i class="fas fa-user-tie"></i>

                                    </div>


                                    <div>

                                        <strong>
                                            Candidatos a postular
                                        </strong>

                                        <span>
                                            Filtra y selecciona participantes para este sondeo.
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="table-responsive">

                                <table
                                    class="table table-sm fs-9 mb-0"
                                    id="candidatosTable">

                                    <thead>

                                        <tr>

                                            <th>Seleccionar</th>

                                            <th>Foto</th>

                                            <th>Nombre Completo</th>

                                            <th>Cargo Público</th>

                                            <th>Partido(s) Político(s)</th>

                                            <th>Municipio</th>

                                            <th>Departamento</th>

                                        </tr>

                                    </thead>


                                    <tbody class="list">
                                        <!-- Render JS -->
                                    </tbody>

                                </table>

                            </div>

                        </div>


                        <!-- =====================================
                             ACTIONS
                        ====================================== -->

                        <div class="sondeo-action-bar">

                            <div class="sondeo-action-inner">

                                <div class="sondeo-action-copy">

                                    <div class="sondeo-action-state">

                                        <i class="fas fa-check"></i>

                                    </div>


                                    <div>

                                        <strong>
                                            Sondeo preparado
                                        </strong>

                                        <span>
                                            Guarda o actualiza la configuración.
                                        </span>

                                    </div>

                                </div>


                                <div class="d-flex align-items-center gap-2">

                                    <button
                                        type="button"
                                        onclick="SONDEO.emptyCells();"
                                        class="sondeo-btn sondeo-btn-soft">

                                        <i class="fas fa-rotate-left"></i>

                                        Limpiar

                                    </button>


                                    <?php if ($create && $edit): ?>

                                        <button
                                            class="sondeo-btn sondeo-btn-primary"
                                            type="button"
                                            onclick="SONDEO.validateData();">

                                            <i class="fas fa-floppy-disk"></i>

                                            Guardar sondeo

                                        </button>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </section>


            <!-- =================================================
                 DIRECTORY
            ================================================== -->

            <section class="sondeo-directory">

                <div class="sondeo-directory-head">

                    <div class="sondeo-directory-head-left">

                        <div class="sondeo-directory-icon">

                            <i class="fas fa-chart-column"></i>

                        </div>


                        <div>

                            <h2>
                                Sondeos registrados
                            </h2>


                            <p>
                                Vigencia, estado, candidatos e información inferencial.
                            </p>

                        </div>

                    </div>


                    <span class="sondeo-count">

                        <i class="fas fa-database"></i>

                        <?= (int)$totalSondeos ?>

                        <?= $totalSondeos === 1 ? 'registro' : 'registros' ?>

                    </span>

                </div>


                <div class="sondeo-table-body">

                    <div class="table-responsive">

                        <table
                            id="dynamictable"
                            class="table table-sm fs-9 mb-0">

                            <thead>

                                <tr>

                                    <th>Vigencia</th>

                                    <th>Habilitado</th>

                                    <th>Acciones</th>

                                    <th>Sondeo</th>

                                    <th>Candidatos</th>

                                    <th>Inferencial</th>

                                    <th>Fecha</th>

                                </tr>

                            </thead>


                            <tbody class="list">

                            <?php if ($isvalidSondeo && count($arr) > 0): ?>

                                <?php foreach ($arr as $item): ?>

                                    <?php
                                    $id =
                                        (int)(
                                            $item['id']
                                            ?? 0
                                        );
                                    ?>

                                    <tr>

                                        <td>

                                            <?php if (!empty($item['vigente'])): ?>

                                                <span class="sondeo-status sondeo-status-success">

                                                    <i class="fas fa-calendar-check"></i>

                                                    Vigente

                                                </span>

                                            <?php else: ?>

                                                <span class="sondeo-status sondeo-status-neutral">

                                                    <i class="fas fa-calendar-times"></i>

                                                    No Vigente

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <div class="form-check form-switch">

                                                <input
                                                    class="form-check-input toggle-sondeo-habilitado"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="toggleSondeo_<?= $id ?>"
                                                    data-sondeo-id="<?= $id ?>"
                                                    <?= (($item['habilitado'] ?? '') === 'si') ? 'checked' : '' ?>>


                                                <label
                                                    class="form-check-label"
                                                    for="toggleSondeo_<?= $id ?>">


                                                    <?php if (($item['habilitado'] ?? '') === 'si'): ?>

                                                        <span
                                                            class="sondeo-status sondeo-status-success"
                                                            id="badgeSondeo_<?= $id ?>">

                                                            Activo

                                                        </span>

                                                    <?php else: ?>

                                                        <span
                                                            class="sondeo-status sondeo-status-danger"
                                                            id="badgeSondeo_<?= $id ?>">

                                                            Inactivo

                                                        </span>

                                                    <?php endif; ?>

                                                </label>

                                            </div>

                                        </td>


                                        <td>

                                            <?php if ($edit): ?>

                                                <button
                                                    type="button"
                                                    class="btn sondeo-icon-btn sondeo-edit sondeo-edit-action"
                                                    title="Editar"
                                                    onclick="SONDEO.editData(<?= $id ?>)">

                                                    <i class="uil uil-edit"></i>

                                                </button>

                                            <?php endif; ?>

                                        </td>


                                        <th class="sondeo-name">

                                            <?= h($item['sondeo'] ?? '') ?>

                                        </th>


                                        <td>

                                            <?php if (($item['aplica_cargos_publicos'] ?? '') === 'si'): ?>

                                                <button
                                                    type="button"
                                                    class="btn sondeo-icon-btn sondeo-candidates"
                                                    title="Candidatos del sondeo"
                                                    onclick="showParticipantsModal(
                                                        <?= htmlspecialchars(
                                                            json_encode(
                                                                $item['candidatos']
                                                                ?? []
                                                            ),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>,
                                                        '<?= h($item['sondeo'] ?? '') ?>'
                                                    )">

                                                    <i class="fas fa-users"></i>

                                                </button>

                                            <?php else: ?>

                                                <span class="sondeo-status sondeo-status-neutral">

                                                    No aplica

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <th>

                                            <span class="sondeo-status sondeo-status-blue">

                                                <?= h($item['tipo_inferenciales'] ?? '') ?>

                                            </span>

                                        </th>


                                        <td>

                                            <?= h($item['dtcreate'] ?? '') ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5 text-muted">

                                        No hay sondeos registrados.

                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 MODAL PARTICIPANTS
            ================================================== -->

            <div
                class="modal fade sondeo-modal"
                id="participantsModal"
                tabindex="-1"
                data-bs-backdrop="static"
                aria-labelledby="modalPermisosLabel"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">

                    <div class="modal-content">

                        <div class="modal-header">

                            <div>

                                <h5
                                    class="modal-title"
                                    id="modalPermisosLabel">

                                    <i class="fas fa-users me-2"></i>

                                    Candidatos del sondeo

                                </h5>


                                <div class="sondeo-modal-sub">

                                    <span id="sondeo-title"></span>

                                </div>

                            </div>


                            <button
                                class="btn p-1"
                                type="button"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                onclick="UTIL.clearForm('formpermission');">

                                <span class="fas fa-times text-white"></span>

                            </button>

                        </div>


                        <div
                            class="modal-body"
                            style="
                                max-height:62vh;
                                overflow-y:auto;
                            ">

                            <div class="table-responsive">

                                <table
                                    class="table table-sm table-striped mb-0"
                                    id="candidatosModalTable">

                                    <thead>

                                        <tr>

                                            <th>Foto</th>

                                            <th>Candidato</th>

                                            <th>Cargo Público</th>

                                            <th>Partido(s)</th>

                                            <th>Municipio</th>

                                            <th>Departamento</th>

                                        </tr>

                                    </thead>


                                    <tbody class="list">
                                        <!-- Render JS -->
                                    </tbody>

                                </table>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                class="sondeo-btn sondeo-btn-soft"
                                type="button"
                                onclick="hideParticipantsModal();">

                                <i class="fas fa-xmark"></i>

                                Cerrar

                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <?php include './admin/include/footer.php'; ?>

        </div>

    </div>

</main>


<!-- ==========================================================
     SCRIPTS
========================================================== -->

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>

<script src="assets/js/plugins/bootstrap.min.js"></script>

<script src="assets/js/pcoded.min.js"></script>

<?php include './admin/include/generic_dataTables.php'; ?>

<script
    type="text/javascript"
    src="admin/js/departamento.js">
</script>

<script
    type="text/javascript"
    src="admin/js/opcion_preguntas.js">
</script>

<script
    type="text/javascript"
    src="admin/js/sondeo.js">
</script>


<script>
$(function(){


    /* ======================================================
       FUNCIONES EXISTENTES
    ====================================================== */

    if (
        window.SONDEO
        &&
        typeof SONDEO.handleCargoPublicoChange
        === "function"
    ) {

        SONDEO.handleCargoPublicoChange();

    }


    if (
        window.SONDEO
        &&
        typeof SONDEO.handleSondeParaCargoPublicoChange
        === "function"
    ) {

        SONDEO.handleSondeParaCargoPublicoChange();

    }


    if (
        window.OPCIONES
        &&
        typeof OPCIONES.handleTipoPreguntaChange
        === "function"
    ) {

        OPCIONES.handleTipoPreguntaChange(
            "tipo_sondeo"
        );

    }


    setTimeout(
        function(){

            if (
                window.DEPARTAMENTO
                &&
                typeof DEPARTAMENTO.getMunicipios
                === "function"
            ) {

                DEPARTAMENTO.getMunicipios();

            }

        },
        700
    );


    /* ======================================================
       NUEVO SONDEO
    ====================================================== */

    $("#btnNuevoSondeo")
        .on(
            "click",
            function(){

                if (
                    window.SONDEO
                    &&
                    typeof SONDEO.emptyCells
                    === "function"
                ) {

                    SONDEO.emptyCells();

                }


                const card =
                    document.getElementById(
                        "sondeoFormCard"
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
                    function(){

                        $("#sondeo")
                            .trigger(
                                "focus"
                            );

                    },
                    420
                );

            }
        );


    /* ======================================================
       EDITAR -> SUBIR
    ====================================================== */

    $(document)
        .on(
            "click",
            ".sondeo-edit-action",
            function(){

                setTimeout(
                    function(){

                        const card =
                            document.getElementById(
                                "sondeoFormCard"
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


});
</script>


<?php include 'admin/include/scriptsgober360.php'; ?>

</body>

</html>
