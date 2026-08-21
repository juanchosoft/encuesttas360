wy<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/EspacioGeografico.php';
include './admin/classes/Departamento.php';

// Variables config
include './admin/include/generic_info_configuracion.php';

// Permisos
$view    = SessionData::getPermission(14);
$create  = SessionData::getPermission(15);
$edit    = SessionData::getPermission(16);
$permits = SessionData::getPermission(17);

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Espacios geográficos
$resp = EspacioGeografico::getAll(null);
$isvalidEspacio = $resp['output']['valid'] ?? false;
$arr = $resp['output']['response'] ?? [];

$modulo = 'Espacio Geográfico';

// Departamentos
$arrDepResp = Departamento::getAll(null);
$arrDep = $arrDepResp['output']['response'] ?? [];

$optionDep = '<option value="" selected disabled>Seleccione un departamento</option>';

foreach ($arrDep as $dep) {

    $cd = htmlspecialchars(
        $dep['codigo_departamento'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $nm = htmlspecialchars(
        $dep['departamento'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $optionDep .=
        "<option value='{$cd}'>{$cd} - {$nm}</option>";
}

$optionDep .=
    "<option value='00'>00 - Estudio Nacional (Todos los departamentos)</option>";

function h($s){
    return htmlspecialchars(
        (string)$s,
        ENT_QUOTES,
        'UTF-8'
    );
}

function tipoBadge($tipo){

    $t = strtolower(
        trim(
            (string)$tipo
        )
    );

    if ($t === 'nacional') {
        return [
            'bg' => 'geo-badge-national',
            'icon' => 'fa-globe-americas',
            'txt' => 'Nacional'
        ];
    }

    if ($t === 'departamental') {
        return [
            'bg' => 'geo-badge-department',
            'icon' => 'fa-layer-group',
            'txt' => 'Departamental'
        ];
    }

    if ($t === 'municipal') {
        return [
            'bg' => 'geo-badge-municipal',
            'icon' => 'fa-city',
            'txt' => 'Municipal'
        ];
    }

    return [
        'bg' => 'geo-badge-muted',
        'icon' => 'fa-circle-info',
        'txt' => ($tipo ?: 'N/A')
    ];
}

// KPIs ejecutivos
$totalEstudios = is_array($arr) ? count($arr) : 0;
$totalNacionales = 0;
$totalDepartamentales = 0;
$totalMunicipales = 0;
$totalVotantes = 0;
$totalPoblacion = 0;

if (is_array($arr)) {

    foreach ($arr as $item) {

        $tipo = strtolower(
            trim(
                (string)(
                    $item['tipo_estudio']
                    ?? ''
                )
            )
        );

        if ($tipo === 'nacional') {
            $totalNacionales++;
        }

        if ($tipo === 'departamental') {
            $totalDepartamentales++;
        }

        if ($tipo === 'municipal') {
            $totalMunicipales++;
        }

        $totalVotantes +=
            (float)(
                $item['numero_votantes']
                ?? 0
            );

        $totalPoblacion +=
            (float)(
                $item['cantidad_poblacion']
                ?? 0
            );
    }
}

$coberturaPorcentaje =
    $totalPoblacion > 0
        ? round(
            ($totalVotantes / $totalPoblacion) * 100,
            1
        )
        : 0;

?>

<!DOCTYPE html>
<html lang="es">

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

    <!-- Choices -->
    <link
        rel="stylesheet"
        href="admin/js/lib/choices.min.css">

    <style>
    /* ==========================================================
       ESTADÍSTICA360 · GEOGRAPHIC INTELLIGENCE STUDIO
    ========================================================== */

    :root{
        --geo-navy:#071B3B;
        --geo-navy-2:#0C2D5E;

        --geo-brand:#20427F;
        --geo-brand-2:#3269C8;
        --geo-blue:#4F8CFF;
        --geo-cyan:#11B4DC;

        --geo-success:#13B981;
        --geo-warning:#F59E0B;
        --geo-danger:#E5484D;
        --geo-violet:#7C5CFC;

        --geo-page:#F3F6FB;
        --geo-card:#FFFFFF;
        --geo-card-soft:#F8FAFD;

        --geo-text:#101828;
        --geo-text-2:#344054;
        --geo-muted:#667085;
        --geo-soft:#98A2B3;

        --geo-line:#E6EBF2;

        --geo-r-xxl:30px;
        --geo-r-xl:24px;
        --geo-r-lg:18px;
        --geo-r-md:14px;

        --geo-shadow:
            0 22px 60px
            rgba(15,23,42,.09);

        --geo-shadow-soft:
            0 12px 32px
            rgba(15,23,42,.065);

        --geo-shadow-hover:
            0 28px 70px
            rgba(15,23,42,.13);
    }

    *{
        box-sizing:border-box;
    }

    html{
        scroll-behavior:smooth;
    }

    body.geo-page{
        margin:0;

        background:
            radial-gradient(
                880px 460px at 4% -4%,
                rgba(49,104,200,.12),
                transparent 65%
            ),
            radial-gradient(
                760px 430px at 104% 8%,
                rgba(17,180,220,.075),
                transparent 64%
            ),
            linear-gradient(
                180deg,
                #F7F9FC 0%,
                #F2F5FA 100%
            );

        color:
            var(--geo-text);

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

    body.geo-page::before{
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
       FIX DE CONTENIDO
    ========================================================== */

    .main .content{
        padding-top:
            18px !important;

        padding-bottom:
            38px !important;

        margin-top:
            0 !important;

        border-top:
            0 !important;

        box-shadow:
            none !important;
    }

    .main .content::before,
    .main .content::after{
        content:none !important;
        display:none !important;
    }

    .geo-shell{
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

    .geo-hero{
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
            var(--geo-r-xxl);

        color:#fff;

        background:
            radial-gradient(
                520px 260px at 10% 2%,
                rgba(79,140,255,.34),
                transparent 65%
            ),
            radial-gradient(
                480px 255px at 93% 11%,
                rgba(17,180,220,.22),
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

    .geo-hero::before{
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

    .geo-hero-grid{
        display:grid;

        grid-template-columns:
            minmax(0,1fr)
            auto;

        gap:28px;

        align-items:center;
    }

    .geo-eyebrow{
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

    .geo-live{
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

    .geo-hero h1{
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

    .geo-hero h1 span{
        color:#A9C7FF;
    }

    .geo-hero p{
        max-width:800px;

        margin:
            10px 0 0;

        color:
            rgba(255,255,255,.70);

        font-size:.91rem;

        line-height:1.65;

        font-weight:500;
    }

    .geo-hero-pills{
        display:flex;

        gap:8px;

        flex-wrap:wrap;

        margin-top:18px;
    }

    .geo-hero-pill{
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

    .geo-hero-pill i{
        color:#9EC2FF;
    }

    /* ==========================================================
       HERO METRICS
    ========================================================== */

    .geo-hero-metrics{
        display:grid;

        grid-template-columns:
            repeat(
                4,
                minmax(92px,1fr)
            );

        gap:9px;

        min-width:530px;
    }

    .geo-hero-metric{
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

    .geo-hero-metric:hover{
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

    .geo-hero-metric i{
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

    .geo-hero-metric strong{
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

    .geo-hero-metric span{
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

    .geo-toolbar{
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
            var(--geo-line);

        border-radius:
            18px;

        background:
            rgba(255,255,255,.92);

        box-shadow:
            var(--geo-shadow-soft);

        backdrop-filter:
            blur(12px);
    }

    .geo-toolbar-copy{
        display:flex;

        align-items:center;

        gap:10px;

        min-width:0;
    }

    .geo-toolbar-icon{
        width:38px;
        height:38px;

        flex:
            0 0 38px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:12px;

        color:
            var(--geo-brand);

        background:#EDF4FF;

        font-size:.9rem;
    }

    .geo-toolbar-copy strong{
        display:block;

        color:
            var(--geo-text);

        font-size:.79rem;

        font-weight:800;
    }

    .geo-toolbar-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--geo-soft);

        font-size:.66rem;

        font-weight:600;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .geo-btn{
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

    .geo-btn-primary{
        border:0;

        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--geo-blue),
                var(--geo-brand-2) 48%,
                var(--geo-brand)
            );

        box-shadow:
            0 11px 23px
            rgba(32,66,127,.22);
    }

    .geo-btn-primary:hover{
        transform:
            translateY(-2px);

        box-shadow:
            0 16px 30px
            rgba(32,66,127,.29);
    }

    .geo-btn-soft{
        border:
            1px solid
            #D7E2F2;

        color:
            var(--geo-brand) !important;

        background:#fff;
    }

    .geo-btn-soft:hover{
        transform:
            translateY(-1px);

        border-color:#BFD2EC;

        background:#F5F9FF;
    }

    /* ==========================================================
       CARD
    ========================================================== */

    .geo-card{
        overflow:hidden;

        margin-bottom:16px;

        border:
            1px solid
            var(--geo-line);

        border-radius:
            var(--geo-r-xl);

        background:
            rgba(255,255,255,.96);

        box-shadow:
            var(--geo-shadow-soft);

        transition:
            border-color .22s ease,
            box-shadow .22s ease;
    }

    .geo-card:hover{
        border-color:#D9E3F1;

        box-shadow:
            0 18px 48px
            rgba(15,23,42,.09);
    }

    .geo-card-header{
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

    .geo-card-title-wrap{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .geo-card-icon{
        width:40px;
        height:40px;

        flex:0 0 40px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:13px;

        color:
            var(--geo-brand);

        background:#EDF4FF;

        font-size:.92rem;
    }

    .geo-card-title{
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

    .geo-card-subtitle{
        margin:
            3px 0 0;

        color:
            var(--geo-soft);

        font-size:.66rem;

        font-weight:600;
    }

    .geo-card-body{
        padding:18px;
    }

    /* ==========================================================
       FORM SECTIONS
    ========================================================== */

    .geo-section{
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

    .geo-section + .geo-section{
        margin-top:13px;
    }

    .geo-section-heading{
        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        margin-bottom:14px;
    }

    .geo-section-heading-left{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .geo-section-dot{
        width:9px;
        height:9px;

        border-radius:50%;

        background:
            linear-gradient(
                135deg,
                var(--geo-blue),
                var(--geo-brand)
            );

        box-shadow:
            0 0 0 4px
            rgba(79,140,255,.09);
    }

    .geo-section-heading h3{
        margin:0;

        color:
            var(--geo-text);

        font-size:.79rem;

        font-weight:800;
    }

    .geo-section-help{
        color:
            var(--geo-soft);

        font-size:.62rem;

        font-weight:600;
    }

    /* ==========================================================
       STUDY TYPE SELECTOR
    ========================================================== */

    .geo-scope-strip{
        display:grid;

        grid-template-columns:
            repeat(3,1fr);

        gap:9px;

        margin-top:12px;
    }

    .geo-scope-option{
        min-height:76px;

        padding:12px;

        border:
            1px solid
            #E3E9F2;

        border-radius:14px;

        background:#FBFCFE;

        transition:
            transform .18s ease,
            border-color .18s ease,
            background .18s ease,
            box-shadow .18s ease;
    }

    .geo-scope-option.active{
        transform:
            translateY(-2px);

        border-color:
            #BDD4F4;

        background:
            linear-gradient(
                145deg,
                #EFF6FF,
                #FFFFFF
            );

        box-shadow:
            0 9px 22px
            rgba(32,66,127,.08);
    }

    .geo-scope-option i{
        color:
            var(--geo-brand-2);

        font-size:.78rem;
    }

    .geo-scope-option strong{
        display:block;

        margin-top:7px;

        color:
            var(--geo-text-2);

        font-size:.67rem;

        font-weight:800;
    }

    .geo-scope-option span{
        display:block;

        margin-top:2px;

        color:
            var(--geo-soft);

        font-size:.58rem;

        line-height:1.35;

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
            #D9E0EA;

        border-radius:14px;

        color:
            var(--geo-text-2);

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
            var(--geo-blue) !important;

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
       GEO DYNAMIC AREA
    ========================================================== */

    .geo-dynamic-shell{
        position:relative;

        min-height:110px;

        padding:14px;

        border:
            1px dashed
            #C9D8EB;

        border-radius:16px;

        background:
            radial-gradient(
                250px 120px at 50% 0%,
                rgba(79,140,255,.07),
                transparent 72%
            ),
            linear-gradient(
                180deg,
                #FBFDFF,
                #F7FAFE
            );
    }

    .geo-dynamic-shell::before{
        content:"";

        position:absolute;

        inset:0;

        pointer-events:none;

        opacity:.30;

        background-image:
            linear-gradient(
                rgba(32,66,127,.025) 1px,
                transparent 1px
            ),
            linear-gradient(
                90deg,
                rgba(32,66,127,.025) 1px,
                transparent 1px
            );

        background-size:
            24px 24px;
    }

    #dynamic-geo-container{
        position:relative;

        z-index:1;
    }

    .geo-dynamic-placeholder{
        min-height:82px;

        display:flex;

        align-items:center;

        justify-content:center;

        gap:12px;

        padding:14px;

        text-align:left;
    }

    .geo-dynamic-placeholder i{
        width:46px;
        height:46px;

        flex:
            0 0 46px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:14px;

        color:
            var(--geo-brand);

        background:#EEF5FF;

        font-size:1rem;
    }

    .geo-dynamic-placeholder strong{
        display:block;

        color:
            var(--geo-text-2);

        font-size:.70rem;

        font-weight:800;
    }

    .geo-dynamic-placeholder span{
        display:block;

        max-width:520px;

        margin-top:3px;

        color:
            var(--geo-soft);

        font-size:.61rem;

        line-height:1.45;

        font-weight:600;
    }

    /* ==========================================================
       CHOICES
    ========================================================== */

    .choices{
        margin-bottom:0 !important;
    }

    .choices__inner{
        min-height:
            58px !important;

        padding:
            9px 10px !important;

        border:
            1px solid
            #D9E0EA !important;

        border-radius:
            14px !important;

        background:
            #FBFCFE !important;

        font-size:.74rem !important;
    }

    .choices.is-focused
    .choices__inner{
        border-color:
            var(--geo-blue) !important;

        box-shadow:
            0 0 0 4px
            rgba(79,140,255,.10) !important;
    }

    .choices__list--multiple
    .choices__item{
        border:
            1px solid
            #DCE8FA !important;

        border-radius:
            999px !important;

        color:
            #245BA7 !important;

        background:
            #EEF5FF !important;

        font-size:
            .62rem !important;

        font-weight:
            750 !important;
    }

    .choices__list--dropdown{
        border:
            1px solid
            #DCE3ED !important;

        border-radius:
            12px !important;

        overflow:hidden;

        box-shadow:
            0 14px 34px
            rgba(15,23,42,.13);
    }

    /* ==========================================================
       METRIC CARDS
    ========================================================== */

    .geo-metric-grid{
        display:grid;

        grid-template-columns:
            repeat(2,1fr);

        gap:10px;
    }

    .geo-metric-card{
        min-height:94px;

        padding:13px;

        border:
            1px solid
            #E6EBF2;

        border-radius:15px;

        background:
            linear-gradient(
                145deg,
                #FFFFFF,
                #FBFCFE
            );
    }

    .geo-metric-card i{
        color:
            var(--geo-brand-2);

        font-size:.74rem;
    }

    .geo-metric-card strong{
        display:block;

        margin-top:8px;

        color:
            var(--geo-text-2);

        font-size:.69rem;

        font-weight:800;
    }

    .geo-metric-card span{
        display:block;

        margin-top:2px;

        color:
            var(--geo-soft);

        font-size:.59rem;

        line-height:1.35;

        font-weight:600;
    }

    /* ==========================================================
       ACTION BAR
    ========================================================== */

    .geo-action-bar{
        position:sticky;

        bottom:12px;

        z-index:20;

        margin-top:15px;
    }

    .geo-action-inner{
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

    .geo-action-copy{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .geo-action-state{
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

    .geo-action-copy strong{
        display:block;

        color:
            var(--geo-text-2);

        font-size:.69rem;

        font-weight:800;
    }

    .geo-action-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--geo-soft);

        font-size:.61rem;

        font-weight:600;
    }

    /* ==========================================================
       DIRECTORY
    ========================================================== */

    .geo-directory{
        overflow:hidden;

        border:
            1px solid
            var(--geo-line);

        border-radius:
            var(--geo-r-xl);

        background:#fff;

        box-shadow:
            var(--geo-shadow);
    }

    .geo-directory-head{
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

    .geo-directory-head-left{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .geo-directory-icon{
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
                var(--geo-blue),
                var(--geo-brand)
            );

        box-shadow:
            0 10px 22px
            rgba(32,66,127,.20);

        font-size:.92rem;
    }

    .geo-directory h2{
        margin:0;

        color:
            var(--geo-text);

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;

        font-weight:800;
    }

    .geo-directory p{
        margin:
            3px 0 0;

        color:
            var(--geo-soft);

        font-size:.66rem;

        font-weight:600;
    }

    .geo-count-badge{
        display:inline-flex;

        align-items:center;

        gap:6px;

        min-height:31px;

        padding:
            6px 10px;

        border:
            1px solid
            #DCE8FA;

        border-radius:
            999px;

        color:#265EA9;

        background:#EEF5FF;

        font-size:.65rem;

        font-weight:800;
    }

    .geo-table-body{
        padding:15px;
    }

    /* ==========================================================
       BADGES
    ========================================================== */

    .geo-badge{
        display:inline-flex;

        align-items:center;

        gap:6px;

        min-height:28px;

        padding:
            5px 8px;

        border-radius:8px;

        font-size:.60rem;

        font-weight:800;

        white-space:nowrap;
    }

    .geo-badge-national{
        color:#175CD3;

        border:
            1px solid
            #D1E9FF;

        background:#EFF8FF;
    }

    .geo-badge-department{
        color:#6941C6;

        border:
            1px solid
            #E9D7FE;

        background:#F9F5FF;
    }

    .geo-badge-municipal{
        color:#B54708;

        border:
            1px solid
            #FEF0C7;

        background:#FFFAEB;
    }

    .geo-badge-muted{
        color:#475467;

        border:
            1px solid
            #EAECF0;

        background:#F9FAFB;
    }

    .geo-metric-pill{
        display:inline-flex;

        align-items:center;

        gap:6px;

        min-height:27px;

        padding:
            5px 8px;

        border:
            1px solid
            #E8ECF2;

        border-radius:8px;

        color:#475467;

        background:#F9FAFB;

        font-size:.60rem;

        font-weight:750;

        white-space:nowrap;
    }

    .geo-metric-pill i{
        color:#607EA8;
    }

    /* ==========================================================
       ACTION BUTTONS
    ========================================================== */

    .geo-icon-btn{
        min-height:35px;

        display:inline-flex;

        align-items:center;
        justify-content:center;

        gap:6px;

        padding:
            0 10px;

        border:
            0 !important;

        border-radius:
            10px !important;

        color:#fff !important;

        font-size:.64rem;

        font-weight:800;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .geo-icon-btn:hover{
        transform:
            translateY(-2px);
    }

    .geo-edit{
        width:35px;

        padding:0;

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

    .geo-view{
        color:
            var(--geo-brand) !important;

        border:
            1px solid
            #D7E3F3 !important;

        background:#fff !important;

        box-shadow:
            0 6px 14px
            rgba(15,23,42,.05);
    }

    .geo-copy{
        width:35px;

        padding:0;

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
            var(--geo-muted);

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
            var(--geo-blue);

        box-shadow:
            0 0 0 4px
            rgba(79,140,255,.10);
    }

    /* ==========================================================
       TABLE
    ========================================================== */

    .geo-directory table{
        width:100% !important;

        margin:0 !important;

        border-collapse:
            separate !important;

        border-spacing:
            0 7px !important;
    }

    .geo-directory
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

    .geo-directory
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

    .geo-directory
    table tbody td:first-child{
        border-left:
            1px solid
            #E9EDF4 !important;

        border-radius:
            13px 0 0 13px;
    }

    .geo-directory
    table tbody td:last-child{
        border-right:
            1px solid
            #E9EDF4 !important;

        border-radius:
            0 13px 13px 0;
    }

    .geo-directory
    table tbody tr{
        transition:
            transform .18s ease;
    }

    .geo-directory
    table tbody tr:hover{
        transform:
            translateY(-2px);
    }

    .geo-directory
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

    /* ==========================================================
       PAGINATION
    ========================================================== */

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
                var(--geo-blue),
                var(--geo-brand)
            ) !important;

        box-shadow:
            0 8px 18px
            rgba(32,66,127,.20) !important;
    }

    /* ==========================================================
       MODAL
    ========================================================== */

    .geo-modal
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

    .geo-modal
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

    .geo-modal
    .modal-header::after{
        content:"";

        position:absolute;

        width:180px;
        height:180px;

        right:-80px;
        top:-100px;

        border:
            1px solid
            rgba(255,255,255,.09);

        border-radius:50%;

        box-shadow:
            0 0 0 30px
            rgba(255,255,255,.02);
    }

    .geo-modal
    .modal-title{
        position:relative;

        z-index:1;

        margin:0;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;

        font-weight:800;
    }

    .geo-modal
    .modal-sub{
        position:relative;

        z-index:1;

        margin-top:4px;

        color:
            rgba(255,255,255,.65);

        font-size:.65rem;

        font-weight:600;
    }

    .geo-modal
    .btn-close{
        position:relative;

        z-index:2;

        filter:invert(1);

        opacity:.88;
    }

    .geo-modal
    .modal-body{
        padding:18px;

        background:
            linear-gradient(
                180deg,
                #FFFFFF,
                #F8FAFD
            );
    }

    .geo-modal-scroll{
        max-height:
            calc(
                100vh - 230px
            );

        overflow:auto;
    }

    .geo-modal
    .modal-footer{
        border-top:
            1px solid
            #E9EDF3;

        background:#fff;
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){

        .geo-hero-grid{
            grid-template-columns:
                1fr;
        }

        .geo-hero-metrics{
            min-width:0;

            width:100%;
        }
    }

    @media (max-width:991px){

        .geo-shell{
            padding:
                0 13px;
        }

        .geo-hero{
            padding:23px;
        }
    }

    @media (max-width:767px){

        .main .content{
            padding-top:
                12px !important;
        }

        .geo-shell{
            padding:
                0 10px;
        }

        .geo-hero{
            min-height:0;

            padding:
                20px 17px;

            border-radius:
                22px;
        }

        .geo-hero h1{
            font-size:1.8rem;
        }

        .geo-hero p{
            font-size:.80rem;
        }

        .geo-hero-metrics{
            grid-template-columns:
                repeat(2,1fr);
        }

        .geo-toolbar{
            align-items:flex-start;

            flex-direction:column;
        }

        .geo-toolbar
        .geo-btn{
            width:100%;
        }

        .geo-card{
            border-radius:19px;
        }

        .geo-card-header{
            padding:14px;
        }

        .geo-card-body{
            padding:14px;
        }

        .geo-section{
            padding:13px;
        }

        .geo-scope-strip{
            grid-template-columns:
                1fr;
        }

        .geo-metric-grid{
            grid-template-columns:
                1fr;
        }

        .geo-action-inner{
            align-items:stretch;

            flex-direction:column;
        }

        .geo-action-inner
        > .d-flex{
            width:100%;
        }

        .geo-action-inner
        .geo-btn{
            flex:1;
        }

        .geo-directory{
            border-radius:19px;
        }

        .geo-directory-head{
            align-items:flex-start;

            padding:14px;
        }

        .geo-table-body{
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

        .geo-directory table{
            min-width:980px;
        }
    }

    @media (max-width:480px){

        .geo-hero-metrics{
            gap:7px;
        }

        .geo-hero-metric{
            min-height:95px;

            padding:12px;
        }

        .geo-hero-metric strong{
            font-size:1.17rem;
        }

        .geo-hero-metric span{
            font-size:.57rem;
        }

        .geo-action-copy{
            display:none;
        }

        .geo-action-inner{
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


<body class="geo-page">

<!-- PRELOADER -->
<div class="loader-bg">

    <div class="loader-track">

        <div class="loader-fill"></div>

    </div>

</div>


<main
    class="main"
    id="top">


    <?php include './admin/include/navbar.php'; ?>

    <?php include './admin/include/header.php'; ?>


    <div class="content">


        <div class="geo-shell">


            <!-- =================================================
                 HERO
            ================================================== -->

            <section class="geo-hero">


                <div class="geo-hero-grid">


                    <div>


                        <div class="geo-eyebrow">

                            <span class="geo-live"></span>

                            Estadística360 · Geographic Intelligence

                        </div>


                        <h1>

                            Espacio
                            <span>Geográfico</span>

                        </h1>


                        <p>

                            Define el alcance territorial de los estudios,
                            administra departamentos y municipios, y configura
                            las métricas de población y votantes desde una sola
                            vista de inteligencia geográfica.

                        </p>


                        <div class="geo-hero-pills">


                            <span class="geo-hero-pill">

                                <i class="fas fa-globe-americas"></i>

                                Estudios nacionales

                            </span>


                            <span class="geo-hero-pill">

                                <i class="fas fa-map"></i>

                                Cobertura territorial

                            </span>


                            <span class="geo-hero-pill">

                                <i class="fas fa-chart-area"></i>

                                Métricas poblacionales

                            </span>


                        </div>


                    </div>


                    <div class="geo-hero-metrics">


                        <div class="geo-hero-metric">

                            <i class="fas fa-layer-group"></i>

                            <strong>
                                <?= (int)$totalEstudios ?>
                            </strong>

                            <span>
                                Estudios configurados
                            </span>

                        </div>


                        <div class="geo-hero-metric">

                            <i class="fas fa-globe-americas"></i>

                            <strong>
                                <?= (int)$totalNacionales ?>
                            </strong>

                            <span>
                                Alcance nacional
                            </span>

                        </div>


                        <div class="geo-hero-metric">

                            <i class="fas fa-map-marked-alt"></i>

                            <strong>
                                <?= (int)($totalDepartamentales + $totalMunicipales) ?>
                            </strong>

                            <span>
                                Estudios territoriales
                            </span>

                        </div>


                        <div class="geo-hero-metric">

                            <i class="fas fa-users"></i>

                            <strong>
                                <?= h(number_format($totalVotantes, 0, ',', '.')) ?>
                            </strong>

                            <span>
                                Votantes configurados
                            </span>

                        </div>


                    </div>


                </div>


            </section>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <section class="geo-toolbar">


                <div class="geo-toolbar-copy">


                    <div class="geo-toolbar-icon">

                        <i class="fas fa-compass"></i>

                    </div>


                    <div>

                        <strong>
                            Centro de configuración territorial
                        </strong>

                        <span>
                            Crea nuevos alcances o administra estudios geográficos existentes.
                        </span>

                    </div>


                </div>


                <?php if ($create): ?>


                    <button
                        type="button"
                        class="geo-btn geo-btn-primary"
                        id="btnNuevoEspacio">

                        <i class="fas fa-plus"></i>

                        Nuevo espacio

                    </button>


                <?php endif; ?>


            </section>


            <!-- =================================================
                 FORM
            ================================================== -->

            <section
                class="geo-card"
                id="geoFormCard">


                <div class="geo-card-header">


                    <div class="geo-card-title-wrap">


                        <div class="geo-card-icon">

                            <i class="fas fa-map-location-dot"></i>

                        </div>


                        <div>

                            <h2 class="geo-card-title">

                                Configuración del estudio

                            </h2>


                            <p class="geo-card-subtitle">

                                Alcance, geografía y métricas del espacio seleccionado.

                            </p>

                        </div>


                    </div>


                    <span class="geo-count-badge">

                        <i class="fas fa-asterisk"></i>

                        Campos requeridos

                    </span>


                </div>


                <div class="geo-card-body">


                    <form
                        id="formespacioGeografico"
                        role="form"
                        autocomplete="off">


                        <input
                            type="hidden"
                            name="op"
                            id="op">


                        <input
                            type="hidden"
                            name="idEspacioGeografico"
                            id="idEspacioGeografico">


                        <!-- =====================================
                             INFORMACIÓN GENERAL
                        ====================================== -->

                        <div class="geo-section">


                            <div class="geo-section-heading">


                                <div class="geo-section-heading-left">

                                    <span class="geo-section-dot"></span>

                                    <h3>
                                        Información general
                                    </h3>

                                </div>


                                <span class="geo-section-help">
                                    Descripción y alcance del estudio
                                </span>


                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-lg-7">


                                    <div class="form-floating">


                                        <input
                                            type="text"
                                            class="form-control"
                                            id="observaciones"
                                            name="observaciones"
                                            placeholder="Ingrese observaciones"
                                            value="">


                                        <label for="observaciones">

                                            Observaciones
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-lg-5">


                                    <div class="form-floating">


                                        <select
                                            class="form-select"
                                            id="tipo_estudio"
                                            name="tipo_estudio"
                                            required>


                                            <option
                                                value=""
                                                selected
                                                disabled>

                                                Seleccione el tipo de estudio

                                            </option>


                                            <option value="Nacional">
                                                Nacional
                                            </option>

                                            <option value="Departamental">
                                                Departamental
                                            </option>

                                            <option value="Municipal">
                                                Municipal
                                            </option>


                                        </select>


                                        <label for="tipo_estudio">

                                            Tipo de estudio
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                            </div>


                            <div class="geo-scope-strip">


                                <div
                                    class="geo-scope-option"
                                    data-geo-scope="Nacional">

                                    <i class="fas fa-globe-americas"></i>

                                    <strong>
                                        Nacional
                                    </strong>

                                    <span>
                                        Cobertura amplia con múltiples departamentos.
                                    </span>

                                </div>


                                <div
                                    class="geo-scope-option"
                                    data-geo-scope="Departamental">

                                    <i class="fas fa-layer-group"></i>

                                    <strong>
                                        Departamental
                                    </strong>

                                    <span>
                                        Estudio focalizado por departamentos y municipios.
                                    </span>

                                </div>


                                <div
                                    class="geo-scope-option"
                                    data-geo-scope="Municipal">

                                    <i class="fas fa-city"></i>

                                    <strong>
                                        Municipal
                                    </strong>

                                    <span>
                                        Permite comunas, zonas y veredas.
                                    </span>

                                </div>


                            </div>


                        </div>


                        <!-- =====================================
                             TERRITORIO DINÁMICO
                        ====================================== -->

                        <div class="geo-section">


                            <div class="geo-section-heading">


                                <div class="geo-section-heading-left">

                                    <span class="geo-section-dot"></span>

                                    <h3>
                                        Cobertura territorial
                                    </h3>

                                </div>


                                <span class="geo-section-help">
                                    Departamentos y municipios incluidos
                                </span>


                            </div>


                            <div class="geo-dynamic-shell">


                                <div
                                    class="row g-3"
                                    id="dynamic-geo-container">
                                </div>


                                <div
                                    class="geo-dynamic-placeholder"
                                    id="geoDynamicPlaceholder">


                                    <i class="fas fa-map-marked-alt"></i>


                                    <div>

                                        <strong>
                                            Selecciona primero el tipo de estudio
                                        </strong>

                                        <span>
                                            El sistema habilitará automáticamente los campos
                                            territoriales correspondientes al alcance elegido.
                                        </span>

                                    </div>


                                </div>


                            </div>


                            <div
                                class="text-end mt-3"
                                id="add-geo-button-container"
                                style="display:none;">


                                <button
                                    type="button"
                                    class="geo-btn geo-btn-soft"
                                    id="add-departamento-btn">

                                    <i class="fas fa-plus"></i>

                                    Agregar departamento

                                </button>


                            </div>


                        </div>


                        <!-- =====================================
                             MÉTRICAS
                        ====================================== -->

                        <div class="geo-section">


                            <div class="geo-section-heading">


                                <div class="geo-section-heading-left">

                                    <span class="geo-section-dot"></span>

                                    <h3>
                                        Métricas del estudio
                                    </h3>

                                </div>


                                <span class="geo-section-help">
                                    Población objetivo y potencial electoral
                                </span>


                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-md-6">


                                    <div class="form-floating">


                                        <input
                                            type="number"
                                            class="form-control"
                                            id="numero_votantes"
                                            name="numero_votantes"
                                            placeholder="Número de votantes"
                                            value=""
                                            required>


                                        <label for="numero_votantes">

                                            Número de votantes
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-md-6">


                                    <div class="form-floating">


                                        <input
                                            type="number"
                                            class="form-control"
                                            id="cantidad_poblacion"
                                            name="cantidad_poblacion"
                                            placeholder="Cantidad de población"
                                            value=""
                                            required>


                                        <label for="cantidad_poblacion">

                                            Cantidad de población
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                            </div>


                            <!-- MUNICIPAL -->

                            <div
                                class="row g-3 mt-1"
                                id="municipal-fields"
                                style="display:none;">


                                <div class="col-12 col-md-4">


                                    <div class="form-floating">


                                        <input
                                            type="number"
                                            class="form-control"
                                            id="numero_comunas"
                                            name="numero_comunas"
                                            placeholder="Comunas">


                                        <label for="numero_comunas">
                                            Número de comunas
                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-md-4">


                                    <div class="form-floating">


                                        <input
                                            type="number"
                                            class="form-control"
                                            id="numero_zonas"
                                            name="numero_zonas"
                                            placeholder="Zonas">


                                        <label for="numero_zonas">
                                            Número de zonas
                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-md-4">


                                    <div class="form-floating">


                                        <input
                                            type="number"
                                            class="form-control"
                                            id="numero_veredas"
                                            name="numero_veredas"
                                            placeholder="Veredas">


                                        <label for="numero_veredas">
                                            Número de veredas
                                        </label>


                                    </div>


                                </div>


                            </div>


                            <div class="geo-metric-grid mt-3">


                                <div class="geo-metric-card">

                                    <i class="fas fa-users"></i>

                                    <strong>
                                        Población objetivo
                                    </strong>

                                    <span>
                                        Registra la base poblacional total del estudio.
                                    </span>

                                </div>


                                <div class="geo-metric-card">

                                    <i class="fas fa-check-to-slot"></i>

                                    <strong>
                                        Potencial electoral
                                    </strong>

                                    <span>
                                        El número de votantes permite comparar cobertura.
                                    </span>

                                </div>


                            </div>


                        </div>


                        <!-- =====================================
                             ACTION BAR
                        ====================================== -->

                        <div class="geo-action-bar">


                            <div class="geo-action-inner">


                                <div class="geo-action-copy">


                                    <div class="geo-action-state">

                                        <i class="fas fa-check"></i>

                                    </div>


                                    <div>

                                        <strong>
                                            Configuración preparada
                                        </strong>

                                        <span>
                                            Guarda o actualiza el espacio geográfico.
                                        </span>

                                    </div>


                                </div>


                                <div class="d-flex align-items-center gap-2">


                                    <button
                                        type="button"
                                        onclick="ESPACIOGEOGRAFICO.reload();"
                                        class="geo-btn geo-btn-soft">

                                        <i class="fas fa-rotate-left"></i>

                                        Limpiar

                                    </button>


                                    <?php if ($create && $edit): ?>


                                        <button
                                            type="button"
                                            onclick="ESPACIOGEOGRAFICO.validateData();"
                                            class="geo-btn geo-btn-primary">

                                            <i class="fas fa-floppy-disk"></i>

                                            Guardar espacio

                                        </button>


                                    <?php endif; ?>


                                </div>


                            </div>


                        </div>


                    </form>


                </div>


            </section>


            <!-- =================================================
                 DIRECTORIO
            ================================================== -->

            <section class="geo-directory">


                <div class="geo-directory-head">


                    <div class="geo-directory-head-left">


                        <div class="geo-directory-icon">

                            <i class="fas fa-map"></i>

                        </div>


                        <div>


                            <h2>
                                Espacios geográficos configurados
                            </h2>


                            <p>
                                Consulta alcances, métricas y geografías asociadas.
                            </p>


                        </div>


                    </div>


                    <span class="geo-count-badge">

                        <i class="fas fa-database"></i>

                        <?= (int)$totalEstudios ?>

                        <?= $totalEstudios === 1 ? 'registro' : 'registros' ?>

                    </span>


                </div>


                <div class="geo-table-body">


                    <div class="table-responsive">


                        <table
                            id="dynamictable"
                            class="table table-sm fs-9 mb-0 align-middle">


                            <thead>


                                <tr>

                                    <th>Acciones</th>

                                    <th>Observaciones</th>

                                    <th>Tipo</th>

                                    <th>Comunas</th>

                                    <th>Zonas</th>

                                    <th>Veredas</th>

                                    <th>Población</th>

                                    <th>Votantes</th>

                                    <th>Creación</th>

                                </tr>


                            </thead>


                            <tbody class="list">


                            <?php if ($isvalidEspacio && count($arr) > 0): ?>


                                <?php foreach ($arr as $item): ?>


                                    <?php

                                        $id =
                                            (int)(
                                                $item['id']
                                                ?? 0
                                            );

                                        $tb =
                                            tipoBadge(
                                                $item['tipo_estudio']
                                                ?? ''
                                            );

                                    ?>


                                    <tr>


                                        <!-- ACCIONES -->

                                        <td>


                                            <div class="d-flex align-items-center gap-2">


                                                <?php if ($edit): ?>


                                                    <button
                                                        type="button"
                                                        class="btn geo-icon-btn geo-edit geo-edit-space"
                                                        title="Editar"
                                                        onclick="ESPACIOGEOGRAFICO.editData(<?= $id ?>)">

                                                        <i class="uil uil-edit"></i>

                                                    </button>


                                                <?php endif; ?>


                                                <button
                                                    type="button"
                                                    class="btn geo-icon-btn geo-view"
                                                    title="Ver geografías"
                                                    onclick="ESPACIOGEOGRAFICO.verGeografias(<?= $id ?>)">

                                                    <i class="fas fa-map-location-dot"></i>

                                                    Ver

                                                </button>


                                                <?php if ($create): ?>


                                                    <button
                                                        type="button"
                                                        class="btn geo-icon-btn geo-copy"
                                                        title="Duplicar como nuevo registro"
                                                        onclick="ESPACIOGEOGRAFICO.duplicar(<?= $id ?>)">

                                                        <i class="fas fa-copy"></i>

                                                    </button>


                                                <?php endif; ?>


                                            </div>


                                        </td>


                                        <!-- OBSERVACIONES -->

                                        <td>

                                            <?= h($item['observaciones'] ?? '') ?>

                                        </td>


                                        <!-- TIPO -->

                                        <td>


                                            <span class="geo-badge <?= h($tb['bg']) ?>">

                                                <i class="fas <?= h($tb['icon']) ?>"></i>

                                                <?= h($tb['txt']) ?>

                                            </span>


                                        </td>


                                        <!-- COMUNAS -->

                                        <td>


                                            <span class="geo-metric-pill">

                                                <i class="fas fa-building"></i>

                                                <?= h(
                                                    ($item['numero_comunas'] ?? '') === ''
                                                        ? '—'
                                                        : number_format(
                                                            (float)$item['numero_comunas'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                ) ?>

                                            </span>


                                        </td>


                                        <!-- ZONAS -->

                                        <td>


                                            <span class="geo-metric-pill">

                                                <i class="fas fa-draw-polygon"></i>

                                                <?= h(
                                                    ($item['numero_zonas'] ?? '') === ''
                                                        ? '—'
                                                        : number_format(
                                                            (float)$item['numero_zonas'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                ) ?>

                                            </span>


                                        </td>


                                        <!-- VEREDAS -->

                                        <td>


                                            <span class="geo-metric-pill">

                                                <i class="fas fa-tree"></i>

                                                <?= h(
                                                    ($item['numero_veredas'] ?? '') === ''
                                                        ? '—'
                                                        : number_format(
                                                            (float)$item['numero_veredas'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                ) ?>

                                            </span>


                                        </td>


                                        <!-- POBLACIÓN -->

                                        <td>


                                            <span class="geo-metric-pill">

                                                <i class="fas fa-users"></i>

                                                <?= h(
                                                    ($item['cantidad_poblacion'] ?? '') === ''
                                                        ? '—'
                                                        : number_format(
                                                            (float)$item['cantidad_poblacion'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                ) ?>

                                            </span>


                                        </td>


                                        <!-- VOTANTES -->

                                        <td>


                                            <span class="geo-metric-pill">

                                                <i class="fas fa-check-to-slot"></i>

                                                <?= h(
                                                    ($item['numero_votantes'] ?? '') === ''
                                                        ? '—'
                                                        : number_format(
                                                            (float)$item['numero_votantes'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                ) ?>

                                            </span>


                                        </td>


                                        <!-- FECHA -->

                                        <td>

                                            <?= h($item['dtcreate'] ?? '') ?>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <tr>


                                    <td
                                        colspan="9"
                                        class="text-center py-5 text-muted">

                                        No hay registros.

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


</main>


<!-- ==========================================================
     MODAL
========================================================== -->

<div
    class="modal fade geo-modal"
    id="modalGeografias"
    tabindex="-1"
    aria-hidden="true">


    <div class="modal-dialog modal-xl modal-dialog-centered">


        <div class="modal-content">


            <div class="modal-header">


                <div>


                    <h5 class="modal-title">

                        <i class="fas fa-map-marked-alt me-2"></i>

                        Geografías del estudio

                    </h5>


                    <div class="geo-modal-sub">

                        Detalle de departamentos y municipios seleccionados.

                    </div>


                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>


            </div>


            <div class="modal-body">


                <div
                    id="modalGeografiasBody"
                    class="geo-modal-scroll">


                    <div class="text-center py-5">


                        <i
                            class="fas fa-spinner fa-spin fa-3x"
                            style="color:var(--geo-brand);">
                        </i>


                        <p
                            class="mt-3"
                            style="
                                color:#667085;
                                font-size:.72rem;
                                font-weight:700;
                            ">

                            Cargando información...

                        </p>


                    </div>


                </div>


            </div>


            <div class="modal-footer">


                <button
                    type="button"
                    class="geo-btn geo-btn-soft"
                    data-bs-dismiss="modal">

                    <i class="fas fa-xmark"></i>

                    Cerrar

                </button>


            </div>


        </div>


    </div>


</div>


<!-- ==========================================================
     SCRIPTS
========================================================== -->

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/jquery.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>

<?php include './admin/include/generic_dataTables.php'; ?>

<script src="admin/js/lib/choices.min.js"></script>


<script>

const DEPARTAMENTO_OPTIONS_HTML =
    <?= json_encode($optionDep) ?>;


$(function(){


    /* ======================================================
       DATATABLE
    ====================================================== */

    if (
        $.fn.DataTable
        &&
        $("#dynamictable").length
    ) {


        if (
            !$.fn.DataTable.isDataTable(
                "#dynamictable"
            )
        ) {


            $("#dynamictable")
                .DataTable({

                    pageLength:
                        25,

                    order:
                        [
                            [
                                8,
                                "desc"
                            ]
                        ],

                    language:{
                        url:
                            "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                    }

                });


        }


    }


    /* ======================================================
       ESTADO VISUAL DE TIPO DE ESTUDIO
    ====================================================== */

    function updateScopeVisual(){


        const value =
            $("#tipo_estudio")
                .val()
            ||
            "";


        $(".geo-scope-option")
            .removeClass(
                "active"
            );


        if (value) {


            $('.geo-scope-option[data-geo-scope="' + value + '"]')
                .addClass(
                    "active"
                );


        }


        const hasDynamic =
            $("#dynamic-geo-container")
                .children()
                .length
            >
            0;


        if (
            value
            ||
            hasDynamic
        ) {


            $("#geoDynamicPlaceholder")
                .hide();


        } else {


            $("#geoDynamicPlaceholder")
                .show();


        }


    }


    $("#tipo_estudio")
        .on(
            "change",
            function(){


                updateScopeVisual();


                setTimeout(
                    updateScopeVisual,
                    120
                );


            }
        );


    $(".geo-scope-option")
        .on(
            "click",
            function(){


                const value =
                    $(this)
                        .data(
                            "geo-scope"
                        );


                $("#tipo_estudio")
                    .val(value)
                    .trigger(
                        "change"
                    );


            }
        );


    /* ======================================================
       OBSERVAR CONTENEDOR DINÁMICO
    ====================================================== */

    const dynamicContainer =
        document
            .getElementById(
                "dynamic-geo-container"
            );


    if (
        dynamicContainer
        &&
        window.MutationObserver
    ) {


        const observer =
            new MutationObserver(
                function(){


                    updateScopeVisual();


                }
            );


        observer.observe(
            dynamicContainer,
            {
                childList:true,
                subtree:true
            }
        );


    }


    /* ======================================================
       NUEVO ESPACIO
    ====================================================== */

    $("#btnNuevoEspacio")
        .on(
            "click",
            function(){


                if (
                    window.ESPACIOGEOGRAFICO
                    &&
                    typeof ESPACIOGEOGRAFICO.reload
                    ===
                    "function"
                ) {


                    ESPACIOGEOGRAFICO
                        .reload();


                }


                const card =
                    document
                        .getElementById(
                            "geoFormCard"
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


                        $("#observaciones")
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
            ".geo-edit-space",
            function(){


                setTimeout(
                    function(){


                        updateScopeVisual();


                        const card =
                            document
                                .getElementById(
                                    "geoFormCard"
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


    updateScopeVisual();


});

</script>


<script src="admin/js/espacio_geografico.js"></script>

<?php include 'admin/include/scriptsgober360.php'; ?>


</body>

</html>
