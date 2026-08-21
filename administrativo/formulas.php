<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Formula.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Validación de permisos para el módulo de Fórmulas
// ID 46: Ver Fórmulas
// ID 47: Crear Fórmulas
// ID 48: Editar Fórmulas
// ID 49: Permisos Fórmulas
$view    = SessionData::getPermission(46);
$create  = SessionData::getPermission(47);
$edit    = SessionData::getPermission(48);
$permits = SessionData::getPermission(49);

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Información de Fórmulas
$arrResponse = Formula::getAll(null);

$isvalid =
    $arrResponse['output']['valid']
    ?? false;

$arr =
    $arrResponse['output']['response']
    ?? [];

$modulo =
    'Fórmulas e Indicadores Estadísticos';

// Tipos de indicadores únicos
$formulas = $arr;

$tipos_indicadores =
    array_values(
        array_filter(
            array_unique(
                array_column(
                    $formulas,
                    'tipo_indicador'
                )
            ),
            function($valor){
                return trim((string)$valor) !== '';
            }
        )
    );

sort(
    $tipos_indicadores,
    SORT_NATURAL
);

function h($s){
    return htmlspecialchars(
        (string)$s,
        ENT_QUOTES,
        'UTF-8'
    );
}

// KPIs
$totalFormulas =
    is_array($arr)
    ?
    count($arr)
    :
    0;

$totalActivas = 0;
$totalInactivas = 0;
$totalTipos =
    count(
        $tipos_indicadores
    );

if (is_array($arr)) {

    foreach ($arr as $item) {

        if (
            isset(
                $item['habilitado']
            )
            &&
            $item['habilitado']
            ===
            'si'
        ) {

            $totalActivas++;

        } else {

            $totalInactivas++;

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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap"
        rel="stylesheet">

    <style>
    /* ==========================================================
       ESTADÍSTICA360
       STATISTICAL FORMULA INTELLIGENCE STUDIO
       ----------------------------------------------------------
       Diseño visual sin alterar lógica FORMULAS.
    ========================================================== */

    :root{
        --f360-navy-950:#07182F;
        --f360-navy-900:#0A2248;
        --f360-navy-800:#123A74;

        --f360-blue-700:#20427F;
        --f360-blue-600:#2D63BD;
        --f360-blue-500:#4B8CF7;

        --f360-cyan:#25B7DC;
        --f360-violet:#7867E8;

        --f360-success:#12B981;
        --f360-warning:#F59E0B;
        --f360-danger:#E5484D;

        --f360-page:#F3F6FB;
        --f360-card:#FFFFFF;
        --f360-soft-card:#F8FAFD;

        --f360-text:#101828;
        --f360-text-2:#344054;
        --f360-muted:#667085;
        --f360-soft:#98A2B3;

        --f360-line:#E5EAF1;

        --f360-r-xxl:30px;
        --f360-r-xl:24px;
        --f360-r-lg:18px;
        --f360-r-md:14px;

        --f360-shadow:
            0 24px 68px
            rgba(15,23,42,.10);

        --f360-shadow-soft:
            0 12px 34px
            rgba(15,23,42,.065);
    }

    *{
        box-sizing:border-box;
    }

    html{
        scroll-behavior:smooth;
    }

    body.f360-page{
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

        color:
            var(--f360-text);

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

    body.f360-page::before{
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
       CONTENT
    ========================================================== */

    .content{
        padding-top:
            18px !important;

        padding-bottom:
            38px !important;

        margin-top:
            0 !important;
    }

    .f360-shell{
        width:100%;

        max-width:
            1660px;

        margin:
            0 auto;

        padding:
            0 18px;
    }

    /* ==========================================================
       HERO
    ========================================================== */

    .f360-hero{
        position:relative;
        isolation:isolate;

        overflow:hidden;

        min-height:
            224px;

        margin-bottom:
            16px;

        padding:
            29px 30px;

        border:
            1px solid
            rgba(255,255,255,.12);

        border-radius:
            var(--f360-r-xxl);

        color:#fff;

        background:
            radial-gradient(
                540px 270px at 9% 0%,
                rgba(75,140,247,.35),
                transparent 66%
            ),
            radial-gradient(
                460px 260px at 95% 10%,
                rgba(120,103,232,.20),
                transparent 67%
            ),
            linear-gradient(
                135deg,
                #173E7B 0%,
                #102A56 47%,
                #07162E 100%
            );

        box-shadow:
            0 30px 80px
            rgba(8,28,63,.24);
    }

    .f360-hero::before{
        content:"";

        position:absolute;

        z-index:-1;

        width:430px;
        height:430px;

        right:-155px;
        top:-220px;

        border:
            1px solid
            rgba(255,255,255,.075);

        border-radius:50%;

        box-shadow:
            0 0 0 44px
            rgba(255,255,255,.021),
            0 0 0 90px
            rgba(255,255,255,.015),
            0 0 0 136px
            rgba(255,255,255,.010);
    }

    .f360-hero-grid{
        display:grid;

        grid-template-columns:
            minmax(0,1fr)
            auto;

        gap:28px;

        align-items:center;
    }

    .f360-eyebrow{
        display:inline-flex;

        align-items:center;

        gap:8px;

        min-height:32px;

        margin-bottom:
            13px;

        padding:
            7px 11px;

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

        font-size:.67rem;

        font-weight:800;

        letter-spacing:.62px;

        text-transform:uppercase;
    }

    .f360-live-dot{
        width:7px;
        height:7px;

        border-radius:50%;

        background:#5DE4A0;

        box-shadow:
            0 0 0 5px
            rgba(93,228,160,.11),
            0 0 16px
            rgba(93,228,160,.45);
    }

    .f360-hero h1{
        margin:0;

        color:#fff;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:
            clamp(
                1.85rem,
                3vw,
                2.95rem
            );

        line-height:1.04;

        font-weight:800;

        letter-spacing:-1.45px;
    }

    .f360-hero h1 span{
        color:#B7D0FF;
    }

    .f360-hero p{
        max-width:
            830px;

        margin:
            11px 0 0;

        color:
            rgba(255,255,255,.70);

        font-size:.91rem;

        line-height:1.67;

        font-weight:500;
    }

    .f360-hero-pills{
        display:flex;

        flex-wrap:wrap;

        gap:8px;

        margin-top:18px;
    }

    .f360-hero-pill{
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

    .f360-hero-pill i{
        color:#A7C7FF;
    }

    /* ==========================================================
       KPI
    ========================================================== */

    .f360-kpis{
        display:grid;

        grid-template-columns:
            repeat(
                4,
                minmax(92px,1fr)
            );

        gap:9px;

        min-width:
            545px;
    }

    .f360-kpi{
        min-height:
            112px;

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

    .f360-kpi:hover{
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

    .f360-kpi-icon{
        width:31px;
        height:31px;

        display:flex;

        align-items:center;

        justify-content:center;

        margin-bottom:
            13px;

        border-radius:10px;

        color:#D8E8FF;

        background:
            rgba(255,255,255,.10);

        font-size:.78rem;
    }

    .f360-kpi strong{
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

    .f360-kpi span{
        display:block;

        margin-top:5px;

        color:
            rgba(255,255,255,.58);

        font-size:.59rem;

        line-height:1.25;

        font-weight:700;
    }

    /* ==========================================================
       TOOLBAR
    ========================================================== */

    .f360-toolbar{
        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        margin-bottom:
            16px;

        padding:
            13px 15px;

        border:
            1px solid
            var(--f360-line);

        border-radius:
            18px;

        background:
            rgba(255,255,255,.92);

        box-shadow:
            var(--f360-shadow-soft);

        backdrop-filter:
            blur(12px);
    }

    .f360-toolbar-copy{
        display:flex;

        align-items:center;

        gap:10px;

        min-width:0;
    }

    .f360-toolbar-icon{
        width:38px;
        height:38px;

        flex:
            0 0 38px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            12px;

        color:
            var(--f360-blue-700);

        background:#EDF4FF;

        font-size:.9rem;
    }

    .f360-toolbar-copy strong{
        display:block;

        color:
            var(--f360-text);

        font-size:.79rem;

        font-weight:800;
    }

    .f360-toolbar-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--f360-soft);

        font-size:.66rem;

        font-weight:600;
    }

    .f360-toolbar-actions{
        display:flex;

        align-items:center;

        gap:8px;

        flex-wrap:wrap;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .f360-btn{
        min-height:43px;

        display:inline-flex;

        align-items:center;

        justify-content:center;

        gap:8px;

        padding:
            9px 15px;

        border-radius:
            12px;

        font-size:.73rem;

        font-weight:800;

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background .18s ease,
            border-color .18s ease;
    }

    .f360-btn-primary{
        border:0;

        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--f360-blue-500),
                var(--f360-blue-600) 50%,
                var(--f360-blue-700)
            );

        box-shadow:
            0 11px 23px
            rgba(32,66,127,.22);
    }

    .f360-btn-primary:hover{
        transform:
            translateY(-2px);

        box-shadow:
            0 16px 30px
            rgba(32,66,127,.29);
    }

    .f360-btn-success{
        border:0;

        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                #2FC38E,
                #0A8463
            );

        box-shadow:
            0 11px 23px
            rgba(10,132,99,.20);
    }

    .f360-btn-success:hover{
        transform:
            translateY(-2px);

        box-shadow:
            0 16px 30px
            rgba(10,132,99,.27);
    }

    .f360-btn-soft{
        border:
            1px solid
            #D7E2F2;

        color:
            var(--f360-blue-700) !important;

        background:#fff;
    }

    .f360-btn-soft:hover{
        transform:
            translateY(-1px);

        border-color:#BFD2EC;

        background:#F5F9FF;
    }

    /* ==========================================================
       CARD
    ========================================================== */

    .f360-card{
        overflow:hidden;

        margin-bottom:
            16px;

        border:
            1px solid
            var(--f360-line);

        border-radius:
            var(--f360-r-xl);

        background:
            rgba(255,255,255,.97);

        box-shadow:
            var(--f360-shadow);
    }

    .f360-card-head{
        min-height:74px;

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

    .f360-card-title{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .f360-card-icon{
        width:41px;
        height:41px;

        flex:
            0 0 41px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            13px;

        color:
            var(--f360-blue-700);

        background:#EDF4FF;

        font-size:.92rem;
    }

    .f360-card-title h2{
        margin:0;

        color:#182230;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:.97rem;

        font-weight:800;
    }

    .f360-card-title p{
        margin:
            3px 0 0;

        color:
            var(--f360-soft);

        font-size:.65rem;

        font-weight:600;
    }

    .f360-card-body{
        padding:18px;
    }

    /* ==========================================================
       SECTIONS
    ========================================================== */

    .f360-section{
        padding:16px;

        border:
            1px solid
            #E6EBF2;

        border-radius:
            18px;

        background:
            linear-gradient(
                145deg,
                #FFFFFF,
                #FBFCFF
            );
    }

    .f360-section + .f360-section{
        margin-top:
            13px;
    }

    .f360-section-head{
        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        margin-bottom:
            14px;
    }

    .f360-section-title{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .f360-section-dot{
        width:9px;
        height:9px;

        border-radius:50%;

        background:
            linear-gradient(
                135deg,
                var(--f360-blue-500),
                var(--f360-blue-700)
            );

        box-shadow:
            0 0 0 4px
            rgba(75,140,247,.09);
    }

    .f360-section-title h3{
        margin:0;

        color:
            var(--f360-text);

        font-size:.79rem;

        font-weight:800;
    }

    .f360-section-help{
        color:
            var(--f360-soft);

        font-size:.61rem;

        font-weight:600;
    }

    /* ==========================================================
       INPUTS
    ========================================================== */

    .form-floating>.form-control,
    .form-floating>.form-select{
        min-height:
            58px;

        border:
            1px solid
            #D9E0EA !important;

        border-radius:
            14px !important;

        color:
            var(--f360-text-2);

        background:
            #FBFCFE;

        font-size:.79rem;

        font-weight:650;

        box-shadow:
            none !important;

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
            var(--f360-blue-500) !important;

        background:#fff;

        box-shadow:
            0 0 0 4px
            rgba(75,140,247,.10) !important;
    }

    .form-floating>label{
        color:#667085;

        font-size:.76rem;

        font-weight:650;
    }

    /* ==========================================================
       FORMULA EDITOR
    ========================================================== */

    .f360-formula-editor{
        position:relative;

        overflow:hidden;

        padding:
            16px;

        border:
            1px solid
            #DDE6F2;

        border-radius:
            17px;

        background:
            radial-gradient(
                280px 130px at 4% 0%,
                rgba(75,140,247,.065),
                transparent 72%
            ),
            linear-gradient(
                180deg,
                #FBFDFF,
                #F7FAFD
            );
    }

    .f360-formula-editor::before{
        content:"∑";

        position:absolute;

        right:16px;
        top:5px;

        color:
            rgba(32,66,127,.045);

        font-family:
            "Manrope",
            sans-serif;

        font-size:8rem;

        font-weight:800;

        line-height:1;

        pointer-events:none;
    }

    .f360-formula-head{
        position:relative;

        z-index:1;

        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        margin-bottom:
            11px;
    }

    .f360-formula-title{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .f360-formula-icon{
        width:38px;
        height:38px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            12px;

        color:
            var(--f360-blue-700);

        background:#EEF5FF;

        font-size:.84rem;
    }

    .f360-formula-title strong{
        display:block;

        color:
            var(--f360-text-2);

        font-size:.73rem;

        font-weight:800;
    }

    .f360-formula-title span{
        display:block;

        margin-top:2px;

        color:
            var(--f360-soft);

        font-size:.59rem;

        font-weight:600;
    }

    #formula{
        position:relative;

        z-index:1;

        min-height:
            116px !important;

        padding-top:
            24px !important;

        font-family:
            "JetBrains Mono",
            ui-monospace,
            monospace;

        font-size:.78rem !important;

        font-weight:600 !important;

        line-height:1.65;
    }

    .f360-formula-example{
        position:relative;

        z-index:1;

        display:flex;

        align-items:flex-start;

        gap:8px;

        margin-top:9px;

        padding:
            9px 10px;

        border:
            1px solid
            #E2E8F1;

        border-radius:
            11px;

        color:
            #5B6F8C;

        background:#fff;

        font-size:.61rem;

        line-height:1.45;

        font-weight:600;
    }

    .f360-formula-example i{
        color:
            var(--f360-blue-600);

        margin-top:2px;
    }

    /* ==========================================================
       SWITCH
    ========================================================== */

    .f360-switch-card{
        min-height:62px;

        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        padding:
            10px 12px;

        border:
            1px solid
            #D9E0EA;

        border-radius:
            14px;

        background:
            #FBFCFE;
    }

    .f360-switch-copy strong{
        display:block;

        color:
            var(--f360-text-2);

        font-size:.70rem;

        font-weight:800;
    }

    .f360-switch-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--f360-soft);

        font-size:.58rem;

        font-weight:600;
    }

    .f360-switch-card .form-check{
        margin:0;

        padding:0;
    }

    .f360-switch-card .form-check-input{
        width:42px !important;
        height:23px !important;

        margin:0 !important;

        cursor:pointer;
    }

    .f360-switch-card .form-check-input:checked{
        border-color:
            var(--f360-success);

        background-color:
            var(--f360-success);
    }

    /* ==========================================================
       ACTION BAR
    ========================================================== */

    .f360-action-bar{
        position:sticky;

        bottom:12px;

        z-index:20;

        margin-top:
            15px;
    }

    .f360-action-inner{
        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        padding:12px;

        border:
            1px solid
            rgba(216,225,238,.94);

        border-radius:
            17px;

        background:
            rgba(255,255,255,.92);

        box-shadow:
            0 15px 35px
            rgba(15,23,42,.11);

        backdrop-filter:
            blur(16px);
    }

    .f360-action-copy{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .f360-action-state{
        width:34px;
        height:34px;

        flex:
            0 0 34px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            11px;

        color:#07845E;

        background:#ECFDF5;

        font-size:.76rem;
    }

    .f360-action-copy strong{
        display:block;

        color:
            var(--f360-text-2);

        font-size:.69rem;

        font-weight:800;
    }

    .f360-action-copy span{
        display:block;

        margin-top:2px;

        color:
            var(--f360-soft);

        font-size:.60rem;

        font-weight:600;
    }

    /* ==========================================================
       DIRECTORY
    ========================================================== */

    .f360-directory{
        overflow:hidden;

        margin-top:16px;

        border:
            1px solid
            var(--f360-line);

        border-radius:
            var(--f360-r-xl);

        background:#fff;

        box-shadow:
            var(--f360-shadow);
    }

    .f360-directory-head{
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
                rgba(75,140,247,.06),
                transparent 70%
            ),
            linear-gradient(
                180deg,
                #FFFFFF,
                #FBFCFF
            );
    }

    .f360-directory-title{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .f360-directory-icon{
        width:43px;
        height:43px;

        flex:
            0 0 43px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            13px;

        color:#fff;

        background:
            linear-gradient(
                135deg,
                var(--f360-blue-500),
                var(--f360-blue-700)
            );

        box-shadow:
            0 10px 22px
            rgba(32,66,127,.20);

        font-size:.92rem;
    }

    .f360-directory-title h2{
        margin:0;

        color:
            var(--f360-text);

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;

        font-weight:800;
    }

    .f360-directory-title p{
        margin:
            3px 0 0;

        color:
            var(--f360-soft);

        font-size:.65rem;

        font-weight:600;
    }

    .f360-count{
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

    .f360-directory-body{
        padding:15px;
    }

    /* ==========================================================
       TABLE
    ========================================================== */

    #dynamictable{
        width:100% !important;

        margin:0 !important;

        border-collapse:
            separate !important;

        border-spacing:
            0 7px !important;
    }

    #dynamictable thead th{
        padding:
            10px 11px !important;

        border:0 !important;

        color:
            #667085 !important;

        background:
            transparent !important;

        font-size:
            .59rem !important;

        font-weight:
            800 !important;

        letter-spacing:.40px;

        text-transform:uppercase;

        white-space:
            nowrap !important;
    }

    #dynamictable tbody td{
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
            .67rem !important;

        line-height:1.45;

        vertical-align:
            middle !important;

        transition:
            background .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #dynamictable tbody td:first-child{
        border-left:
            1px solid
            #E9EDF4 !important;

        border-radius:
            13px 0 0 13px;
    }

    #dynamictable tbody td:last-child{
        border-right:
            1px solid
            #E9EDF4 !important;

        border-radius:
            0 13px 13px 0;
    }

    #dynamictable tbody tr{
        transition:
            transform .18s ease;
    }

    #dynamictable tbody tr:hover{
        transform:
            translateY(-2px);
    }

    #dynamictable tbody tr:hover td{
        border-color:
            #DCE7F6 !important;

        background:
            linear-gradient(
                90deg,
                #F6FAFF,
                #FFFFFF
            ) !important;

        box-shadow:
            0 9px 23px
            rgba(15,23,42,.05);
    }

    .f360-indicator{
        min-width:
            220px;

        color:
            #1D2939 !important;

        font-weight:
            800 !important;
    }

    .f360-formula-code{
        display:inline-block;

        max-width:
            330px;

        overflow:hidden;

        padding:
            6px 8px;

        border:
            1px solid
            #E4E8EF;

        border-radius:
            8px;

        color:
            #344054;

        background:
            #F8FAFC;

        font-family:
            "JetBrains Mono",
            ui-monospace,
            monospace;

        font-size:.60rem;

        line-height:1.45;

        white-space:
            nowrap;

        text-overflow:
            ellipsis;
    }

    /* ==========================================================
       STATUS / BADGES
    ========================================================== */

    .f360-status{
        display:inline-flex;

        align-items:center;

        gap:5px;

        min-height:27px;

        padding:
            5px 8px;

        border-radius:
            8px;

        font-size:.60rem;

        font-weight:800;

        white-space:
            nowrap;
    }

    .f360-status-success{
        color:#06795B;

        border:
            1px solid
            #D1FAE5;

        background:#ECFDF5;
    }

    .f360-status-danger{
        color:#B42318;

        border:
            1px solid
            #FEE4E2;

        background:#FEF3F2;
    }

    .f360-status-blue{
        color:#175CD3;

        border:
            1px solid
            #D1E9FF;

        background:#EFF8FF;
    }

    .f360-status-violet{
        color:#6941C6;

        border:
            1px solid
            #E9D7FE;

        background:#F9F5FF;
    }

    .f360-status-neutral{
        color:#475467;

        border:
            1px solid
            #EAECF0;

        background:#F9FAFB;
    }

    /* ==========================================================
       ACTION BUTTONS
    ========================================================== */

    .f360-icon-btn{
        width:35px;
        height:35px;

        display:inline-flex;

        align-items:center;

        justify-content:center;

        padding:0;

        border:0 !important;

        border-radius:
            10px !important;

        color:#fff !important;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .f360-icon-btn:hover{
        transform:
            translateY(-2px);
    }

    .f360-edit{
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

    .f360-detail{
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
            var(--f360-muted);

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
        margin-bottom:
            13px;
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

        min-height:
            39px;

        margin-left:
            8px;

        padding:
            0 12px;

        border:
            1px solid
            #D7DEE9;

        border-radius:
            11px;

        background:#fff;

        outline:none;
    }

    .dataTables_wrapper
    .dataTables_filter input:focus{
        border-color:
            var(--f360-blue-500);

        box-shadow:
            0 0 0 4px
            rgba(75,140,247,.10);
    }

    .dataTables_wrapper
    .dataTables_length select{
        min-height:
            38px;

        border:
            1px solid
            #D7DEE9;

        border-radius:
            10px;

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
        min-width:
            34px;

        height:
            34px;

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

        box-shadow:
            none !important;
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
                var(--f360-blue-500),
                var(--f360-blue-700)
            ) !important;

        box-shadow:
            0 8px 18px
            rgba(32,66,127,.20) !important;
    }

    /* ==========================================================
       MODALS
    ========================================================== */

    .f360-modal
    .modal-content{
        overflow:hidden;

        border:
            1px solid
            rgba(15,23,42,.09) !important;

        border-radius:
            24px !important;

        box-shadow:
            0 30px 82px
            rgba(15,23,42,.25) !important;
    }

    .f360-modal
    .modal-header{
        position:relative;

        overflow:hidden;

        padding:
            18px 20px;

        border-bottom:
            0 !important;

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

    .f360-modal
    .modal-header::after{
        content:"";

        position:absolute;

        width:190px;
        height:190px;

        right:-85px;
        top:-110px;

        border:
            1px solid
            rgba(255,255,255,.08);

        border-radius:50%;

        box-shadow:
            0 0 0 30px
            rgba(255,255,255,.02);
    }

    .f360-modal-icon{
        position:relative;

        z-index:2;

        width:46px;
        height:46px;

        flex:
            0 0 46px;

        display:flex;

        align-items:center;

        justify-content:center;

        border:
            1px solid
            rgba(255,255,255,.18);

        border-radius:
            14px;

        color:#fff;

        background:
            rgba(255,255,255,.12);

        backdrop-filter:
            blur(10px);
    }

    .f360-modal
    .modal-title{
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

    .f360-modal-sub{
        position:relative;

        z-index:2;

        margin-top:3px;

        color:
            rgba(255,255,255,.62);

        font-size:.62rem;

        font-weight:600;
    }

    .f360-modal
    .modal-body{
        padding:
            18px !important;

        background:
            linear-gradient(
                180deg,
                #FBFCFE,
                #F5F8FC
            ) !important;
    }

    .f360-modal
    .modal-footer{
        padding:
            12px 18px;

        border-top:
            1px solid
            #E7EBF1;

        background:#fff;
    }

    .f360-detail-shell{
        min-height:
            180px;

        padding:
            16px;

        border:
            1px solid
            #E4E9F1;

        border-radius:
            17px;

        background:#fff;

        box-shadow:
            0 8px 20px
            rgba(15,23,42,.04);
    }

    /* ==========================================================
       CSV UPLOAD
    ========================================================== */

    .f360-csv-info{
        display:flex;

        align-items:flex-start;

        gap:10px;

        margin-bottom:
            14px;

        padding:
            12px;

        border:
            1px solid
            #D8EAFB;

        border-radius:
            14px;

        color:#175CD3;

        background:
            linear-gradient(
                145deg,
                #EFF8FF,
                #FFFFFF
            );

        font-size:.65rem;

        line-height:1.5;
    }

    .f360-csv-info i{
        width:34px;
        height:34px;

        flex:
            0 0 34px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            11px;

        background:
            #DCEEFF;
    }

    .f360-upload-zone{
        padding:
            18px;

        border:
            1px dashed
            #BED0E5;

        border-radius:
            16px;

        background:
            radial-gradient(
                240px 120px at 50% 0%,
                rgba(75,140,247,.06),
                transparent 72%
            ),
            #fff;
    }

    .f360-upload-label{
        display:flex;

        align-items:center;

        gap:10px;

        margin-bottom:
            10px;
    }

    .f360-upload-label i{
        width:39px;
        height:39px;

        display:flex;

        align-items:center;

        justify-content:center;

        border-radius:
            12px;

        color:#08785C;

        background:#ECFDF5;
    }

    .f360-upload-label strong{
        display:block;

        color:
            var(--f360-text-2);

        font-size:.72rem;

        font-weight:800;
    }

    .f360-upload-label span{
        display:block;

        margin-top:2px;

        color:
            var(--f360-soft);

        font-size:.59rem;

        font-weight:600;
    }

    #csvFile{
        min-height:
            46px;

        border:
            1px solid
            #D9E0EA;

        border-radius:
            12px;

        background:#FBFCFE;
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){

        .f360-hero-grid{
            grid-template-columns:
                1fr;
        }

        .f360-kpis{
            min-width:0;

            width:100%;
        }
    }

    @media (max-width:991px){

        .f360-shell{
            padding:
                0 13px;
        }

        .f360-hero{
            padding:
                23px;
        }
    }

    @media (max-width:767px){

        .content{
            padding-top:
                12px !important;
        }

        .f360-shell{
            padding:
                0 10px;
        }

        .f360-hero{
            min-height:0;

            padding:
                20px 17px;

            border-radius:
                22px;
        }

        .f360-hero h1{
            font-size:
                1.8rem;
        }

        .f360-hero p{
            font-size:
                .80rem;
        }

        .f360-kpis{
            grid-template-columns:
                repeat(
                    2,
                    1fr
                );
        }

        .f360-toolbar{
            align-items:
                flex-start;

            flex-direction:
                column;
        }

        .f360-toolbar-actions{
            width:100%;
        }

        .f360-toolbar-actions
        .f360-btn{
            flex:1;
        }

        .f360-card{
            border-radius:
                19px;
        }

        .f360-card-head{
            align-items:
                flex-start;

            padding:
                14px;
        }

        .f360-card-body{
            padding:
                13px;
        }

        .f360-section{
            padding:
                13px;
        }

        .f360-action-inner{
            align-items:
                stretch;

            flex-direction:
                column;
        }

        .f360-action-inner
        > .d-flex{
            width:100%;
        }

        .f360-action-inner
        .f360-btn{
            flex:1;
        }

        .f360-directory{
            border-radius:
                19px;
        }

        .f360-directory-head{
            align-items:
                flex-start;

            padding:
                14px;
        }

        .f360-directory-body{
            padding:
                10px;
        }

        .table-responsive{
            overflow-x:
                auto;

            -webkit-overflow-scrolling:
                touch;
        }

        #dynamictable{
            min-width:
                1050px;
        }

        .dataTables_wrapper
        .dataTables_filter input{
            width:100%;

            margin:
                6px 0 0;
        }

        .dataTables_wrapper
        .dataTables_paginate{
            justify-content:
                center;

            flex-wrap:
                wrap;
        }
    }

    @media (max-width:480px){

        .f360-kpis{
            gap:7px;
        }

        .f360-kpi{
            min-height:
                96px;

            padding:
                12px;
        }

        .f360-kpi strong{
            font-size:
                1.16rem;
        }

        .f360-kpi span{
            font-size:
                .56rem;
        }

        .f360-action-copy{
            display:none;
        }

        .f360-action-inner{
            padding:
                9px;
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


<body class="f360-page">

<main
    class="main"
    id="top">


    <?php include './admin/include/navbar.php'; ?>

    <?php include './admin/include/header.php'; ?>


    <div class="content">

        <div class="f360-shell">


            <!-- =================================================
                 HERO
            ================================================== -->

            <section class="f360-hero">


                <div class="f360-hero-grid">


                    <div>


                        <div class="f360-eyebrow">

                            <span class="f360-live-dot"></span>

                            Estadística360 · Formula Intelligence

                        </div>


                        <h1>

                            Fórmulas e
                            <span>Indicadores</span>

                        </h1>


                        <p>

                            Administra la capa matemática de Estadística360:
                            fórmulas, indicadores, explicaciones, comparaciones
                            temporales y observaciones que alimentan los análisis
                            estadísticos del sistema.

                        </p>


                        <div class="f360-hero-pills">


                            <span class="f360-hero-pill">

                                <i class="fas fa-square-root-variable"></i>

                                Motor matemático

                            </span>


                            <span class="f360-hero-pill">

                                <i class="fas fa-chart-line"></i>

                                Indicadores estadísticos

                            </span>


                            <span class="f360-hero-pill">

                                <i class="fas fa-file-csv"></i>

                                Importación masiva CSV

                            </span>


                        </div>


                    </div>


                    <div class="f360-kpis">


                        <div class="f360-kpi">

                            <div class="f360-kpi-icon">

                                <i class="fas fa-calculator"></i>

                            </div>


                            <strong>
                                <?= (int)$totalFormulas ?>
                            </strong>


                            <span>
                                Fórmulas registradas
                            </span>


                        </div>


                        <div class="f360-kpi">

                            <div class="f360-kpi-icon">

                                <i class="fas fa-circle-check"></i>

                            </div>


                            <strong>
                                <?= (int)$totalActivas ?>
                            </strong>


                            <span>
                                Fórmulas activas
                            </span>


                        </div>


                        <div class="f360-kpi">

                            <div class="f360-kpi-icon">

                                <i class="fas fa-tags"></i>

                            </div>


                            <strong>
                                <?= (int)$totalTipos ?>
                            </strong>


                            <span>
                                Tipos de indicador
                            </span>


                        </div>


                        <div class="f360-kpi">

                            <div class="f360-kpi-icon">

                                <i class="fas fa-ban"></i>

                            </div>


                            <strong>
                                <?= (int)$totalInactivas ?>
                            </strong>


                            <span>
                                Fórmulas inactivas
                            </span>


                        </div>


                    </div>


                </div>


            </section>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <section class="f360-toolbar">


                <div class="f360-toolbar-copy">


                    <div class="f360-toolbar-icon">

                        <i class="fas fa-compass"></i>

                    </div>


                    <div>


                        <strong>
                            Centro de administración matemática
                        </strong>


                        <span>
                            Crea una fórmula, importa datos o descarga la plantilla CSV.
                        </span>


                    </div>


                </div>


                <div class="f360-toolbar-actions">


                    <a
                        href="formulas_template.csv"
                        download
                        class="f360-btn f360-btn-soft">

                        <i class="fas fa-download"></i>

                        Plantilla CSV

                    </a>


                    <?php if ($create): ?>


                        <button
                            onclick="FORMULAS.showUploadModal()"
                            class="f360-btn f360-btn-success"
                            type="button">

                            <i class="fas fa-file-upload"></i>

                            Importar CSV

                        </button>


                        <button
                            type="button"
                            class="f360-btn f360-btn-primary"
                            id="btnNuevaFormula">

                            <i class="fas fa-plus"></i>

                            Nueva fórmula

                        </button>


                    <?php endif; ?>


                </div>


            </section>


            <!-- =================================================
                 FORM
            ================================================== -->

            <section
                class="f360-card"
                id="f360FormCard">


                <div class="f360-card-head">


                    <div class="f360-card-title">


                        <div class="f360-card-icon">

                            <i class="fas fa-square-root-variable"></i>

                        </div>


                        <div>


                            <h2>

                                <span id="spanModulo">
                                    <?= h($modulo) ?>
                                </span>

                            </h2>


                            <p>
                                Configura indicador, fórmula, contexto y estado.
                            </p>


                        </div>


                    </div>


                    <span class="f360-status f360-status-blue">

                        <i class="fas fa-asterisk"></i>

                        Campos requeridos

                    </span>


                </div>


                <div class="f360-card-body">


                    <form
                        id="formFormulas"
                        role="form"
                        autocomplete="false">


                        <input
                            type="hidden"
                            name="op"
                            id="op"
                            value="formulassave">


                        <input
                            type="hidden"
                            name="id"
                            id="id"
                            value="0">


                        <!-- =====================================
                             IDENTIFICACIÓN
                        ====================================== -->

                        <section class="f360-section">


                            <div class="f360-section-head">


                                <div class="f360-section-title">

                                    <span class="f360-section-dot"></span>

                                    <h3>
                                        Identificación del indicador
                                    </h3>

                                </div>


                                <span class="f360-section-help">

                                    Nombre, sigla y clasificación

                                </span>


                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-lg-6">


                                    <div class="form-floating">


                                        <input
                                            class="form-control"
                                            type="text"
                                            id="indicador"
                                            name="indicador"
                                            placeholder="Nombre del indicador"
                                            required>


                                        <label for="indicador">

                                            Indicador
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-md-4 col-lg-2">


                                    <div class="form-floating">


                                        <input
                                            type="text"
                                            class="form-control"
                                            id="sigla"
                                            name="sigla"
                                            placeholder="Sigla o abreviatura"
                                            required>


                                        <label for="sigla">

                                            Sigla
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-md-8 col-lg-4">


                                    <div class="form-floating">


                                        <select
                                            class="form-select"
                                            id="tipo_indicador"
                                            name="tipo_indicador">


                                            <option value="">
                                                Seleccione un tipo...
                                            </option>


                                            <?php foreach ($tipos_indicadores as $tipo): ?>


                                                <option
                                                    value="<?= h($tipo) ?>">

                                                    <?= h($tipo) ?>

                                                </option>


                                            <?php endforeach; ?>


                                        </select>


                                        <label for="tipo_indicador">

                                            Tipo de Indicador
                                            <span class="text-danger">*</span>

                                        </label>


                                    </div>


                                </div>


                            </div>


                        </section>


                        <!-- =====================================
                             FORMULA
                        ====================================== -->

                        <section class="f360-section">


                            <div class="f360-section-head">


                                <div class="f360-section-title">

                                    <span class="f360-section-dot"></span>

                                    <h3>
                                        Expresión matemática
                                    </h3>

                                </div>


                                <span class="f360-section-help">

                                    Motor de cálculo del indicador

                                </span>


                            </div>


                            <div class="f360-formula-editor">


                                <div class="f360-formula-head">


                                    <div class="f360-formula-title">


                                        <div class="f360-formula-icon">

                                            <i class="fas fa-code"></i>

                                        </div>


                                        <div>


                                            <strong>
                                                Editor de fórmula
                                            </strong>


                                            <span>
                                                Escribe la expresión matemática utilizada por el indicador.
                                            </span>


                                        </div>


                                    </div>


                                    <span class="f360-status f360-status-violet">

                                        <i class="fas fa-function"></i>

                                        Formula Engine

                                    </span>


                                </div>


                                <div class="form-floating">


                                    <textarea
                                        class="form-control"
                                        id="formula"
                                        name="formula"
                                        placeholder="Fórmula matemática"
                                        required></textarea>


                                    <label for="formula">

                                        Fórmula
                                        <span class="text-danger">*</span>

                                    </label>


                                </div>


                                <div class="f360-formula-example">


                                    <i class="fas fa-info-circle"></i>


                                    <span>

                                        Ejemplo:
                                        <strong>
                                            IF = (Respuestas Positivas / Total Respuestas) * 100
                                        </strong>

                                    </span>


                                </div>


                            </div>


                        </section>


                        <!-- =====================================
                             DOCUMENTACIÓN
                        ====================================== -->

                        <section class="f360-section">


                            <div class="f360-section-head">


                                <div class="f360-section-title">

                                    <span class="f360-section-dot"></span>

                                    <h3>
                                        Explicación y observaciones
                                    </h3>

                                </div>


                                <span class="f360-section-help">

                                    Documentación funcional del cálculo

                                </span>


                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-lg-7">


                                    <div class="form-floating">


                                        <textarea
                                            class="form-control"
                                            id="explicacion"
                                            name="explicacion"
                                            placeholder="Explicación detallada"
                                            style="height:120px;"></textarea>


                                        <label for="explicacion">

                                            Explicación

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-lg-5">


                                    <div class="form-floating">


                                        <textarea
                                            class="form-control"
                                            id="observaciones"
                                            name="observaciones"
                                            placeholder="Observaciones"
                                            style="height:120px;"></textarea>


                                        <label for="observaciones">

                                            Observaciones

                                        </label>


                                    </div>


                                </div>


                            </div>


                        </section>


                        <!-- =====================================
                             CONTEXTO TEMPORAL
                        ====================================== -->

                        <section class="f360-section">


                            <div class="f360-section-head">


                                <div class="f360-section-title">

                                    <span class="f360-section-dot"></span>

                                    <h3>
                                        Comparación y contexto
                                    </h3>

                                </div>


                                <span class="f360-section-help">

                                    Enunciados anteriores, actuales y notas

                                </span>


                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-md-6">


                                    <div class="form-floating">


                                        <textarea
                                            class="form-control"
                                            id="enunciado_antes"
                                            name="enunciado_antes"
                                            placeholder="Enunciado anterior"
                                            style="height:95px;"></textarea>


                                        <label for="enunciado_antes">

                                            Enunciado Antes

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-md-6">


                                    <div class="form-floating">


                                        <textarea
                                            class="form-control"
                                            id="enunciado_ahora"
                                            name="enunciado_ahora"
                                            placeholder="Enunciado actual"
                                            style="height:95px;"></textarea>


                                        <label for="enunciado_ahora">

                                            Enunciado Ahora

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-lg-8">


                                    <div class="form-floating">


                                        <textarea
                                            class="form-control"
                                            id="notas_adicionales"
                                            name="notas_adicionales"
                                            placeholder="Notas adicionales"
                                            style="height:95px;"></textarea>


                                        <label for="notas_adicionales">

                                            Notas Adicionales

                                        </label>


                                    </div>


                                </div>


                                <div class="col-12 col-lg-4">


                                    <div class="f360-switch-card">


                                        <div class="f360-switch-copy">


                                            <strong>
                                                Fórmula habilitada
                                            </strong>


                                            <span>
                                                Disponible para uso y análisis dentro del sistema.
                                            </span>


                                        </div>


                                        <div class="form-check form-switch">


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


                            </div>


                        </section>


                        <!-- =====================================
                             ACTIONS
                        ====================================== -->

                        <div class="f360-action-bar">


                            <div class="f360-action-inner">


                                <div class="f360-action-copy">


                                    <div class="f360-action-state">

                                        <i class="fas fa-check"></i>

                                    </div>


                                    <div>


                                        <strong>
                                            Fórmula preparada
                                        </strong>


                                        <span>
                                            Guarda o actualiza la configuración del indicador.
                                        </span>


                                    </div>


                                </div>


                                <div class="d-flex align-items-center gap-2">


                                    <button
                                        type="button"
                                        onclick="FORMULAS.emptyCells();"
                                        class="f360-btn f360-btn-soft">

                                        <i class="fas fa-rotate-left"></i>

                                        Cancelar

                                    </button>


                                    <?php if ($create && $edit): ?>


                                        <button
                                            class="f360-btn f360-btn-primary"
                                            type="button"
                                            onclick="FORMULAS.validateData();">

                                            <i class="fas fa-floppy-disk"></i>

                                            Guardar fórmula

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

            <section class="f360-directory">


                <div class="f360-directory-head">


                    <div class="f360-directory-title">


                        <div class="f360-directory-icon">

                            <i class="fas fa-table-list"></i>

                        </div>


                        <div>


                            <h2>
                                Fórmulas registradas
                            </h2>


                            <p>
                                Consulta indicadores, expresiones, explicación y estado.
                            </p>


                        </div>


                    </div>


                    <span class="f360-count">

                        <i class="fas fa-database"></i>

                        <?= (int)$totalFormulas ?>

                        <?= $totalFormulas === 1 ? 'registro' : 'registros' ?>

                    </span>


                </div>


                <div class="f360-directory-body">


                    <div class="table-responsive">


                        <table
                            id="dynamictable"
                            class="table table-sm fs-9 mb-0">


                            <thead>


                                <tr>

                                    <th>
                                        Acciones
                                    </th>

                                    <th>
                                        Sigla
                                    </th>

                                    <th>
                                        Tipo
                                    </th>

                                    <th>
                                        Indicador
                                    </th>

                                    <th>
                                        Fórmula
                                    </th>

                                    <th>
                                        Explicación
                                    </th>

                                    <th>
                                        Estado
                                    </th>

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

                                    ?>


                                    <tr>


                                        <!-- ACTIONS -->

                                        <td>


                                            <div class="d-flex align-items-center gap-2">


                                                <?php if ($edit): ?>


                                                    <button
                                                        type="button"
                                                        class="btn f360-icon-btn f360-edit f360-edit-formula"
                                                        title="Editar"
                                                        onclick="FORMULAS.editData(<?= $id ?>)">

                                                        <i class="uil uil-edit"></i>

                                                    </button>


                                                <?php endif; ?>


                                                <button
                                                    type="button"
                                                    class="btn f360-icon-btn f360-detail"
                                                    title="Ver Detalles"
                                                    onclick="FORMULAS.showDetails(
                                                        <?= htmlspecialchars(
                                                            json_encode($item),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    )">

                                                    <i class="uil uil-eye"></i>

                                                </button>


                                            </div>


                                        </td>


                                        <!-- SIGLA -->

                                        <td>


                                            <span class="f360-status f360-status-blue">

                                                <?= h($item['sigla'] ?? '') ?>

                                            </span>


                                        </td>


                                        <!-- TYPE -->

                                        <td>


                                            <?php if (!empty($item['tipo_indicador'])): ?>


                                                <span class="f360-status f360-status-violet">

                                                    <?= h($item['tipo_indicador']) ?>

                                                </span>


                                            <?php else: ?>


                                                <span class="f360-status f360-status-neutral">

                                                    N/A

                                                </span>


                                            <?php endif; ?>


                                        </td>


                                        <!-- INDICATOR -->

                                        <td class="f360-indicator">

                                            <?= h($item['indicador'] ?? '') ?>

                                        </td>


                                        <!-- FORMULA -->

                                        <td>


                                            <code
                                                class="f360-formula-code"
                                                title="<?= h($item['formula'] ?? '') ?>">

                                                <?= h(
                                                    substr(
                                                        (string)(
                                                            $item['formula']
                                                            ?? ''
                                                        ),
                                                        0,
                                                        70
                                                    )
                                                ) ?>

                                                <?= strlen(
                                                    (string)(
                                                        $item['formula']
                                                        ?? ''
                                                    )
                                                ) > 70 ? '...' : '' ?>

                                            </code>


                                        </td>


                                        <!-- EXPLANATION -->

                                        <td>


                                            <span
                                                style="
                                                    display:block;
                                                    max-width:320px;
                                                    color:#667085;
                                                    font-size:.64rem;
                                                    line-height:1.45;
                                                ">

                                                <?= h(
                                                    substr(
                                                        (string)(
                                                            $item['explicacion']
                                                            ?? ''
                                                        ),
                                                        0,
                                                        85
                                                    )
                                                ) ?>

                                                <?= strlen(
                                                    (string)(
                                                        $item['explicacion']
                                                        ?? ''
                                                    )
                                                ) > 85 ? '...' : '' ?>

                                            </span>


                                        </td>


                                        <!-- STATUS -->

                                        <td>


                                            <?php if (
                                                isset(
                                                    $item['habilitado']
                                                )
                                                &&
                                                $item['habilitado']
                                                ===
                                                'si'
                                            ): ?>


                                                <span class="f360-status f360-status-success">

                                                    <i class="fas fa-check-circle"></i>

                                                    Activo

                                                </span>


                                            <?php else: ?>


                                                <span class="f360-status f360-status-danger">

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
                                        colspan="7"
                                        class="text-center py-5 text-muted">

                                        <i class="fas fa-inbox fa-2x mb-2"></i>

                                        <p class="mb-0">
                                            No hay fórmulas registradas
                                        </p>

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
     MODAL DETALLES
========================================================== -->

<div
    class="modal fade f360-modal"
    id="modalDetalles"
    tabindex="-1"
    data-bs-backdrop="static"
    aria-labelledby="tituloModalDetalles"
    aria-hidden="true">


    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">


        <div class="modal-content">


            <div class="modal-header">


                <div class="d-flex align-items-center gap-3">


                    <div class="f360-modal-icon">

                        <i class="fas fa-calculator"></i>

                    </div>


                    <div>


                        <h5
                            class="modal-title"
                            id="tituloModalDetalles">

                            Detalles de la Fórmula

                        </h5>


                        <div class="f360-modal-sub">

                            Información completa del indicador y su expresión matemática.

                        </div>


                    </div>


                </div>


                <button
                    class="btn p-2"
                    type="button"
                    data-bs-dismiss="modal"
                    aria-label="Close">

                    <i class="fas fa-times fs-5 text-white"></i>

                </button>


            </div>


            <div
                class="modal-body"
                style="
                    max-height:68vh;
                    overflow-y:auto;
                ">


                <div class="f360-detail-shell">

                    <div id="detallesContent">

                        <!-- Se llena dinámicamente con JavaScript -->

                    </div>

                </div>


            </div>


            <div class="modal-footer">


                <button
                    class="f360-btn f360-btn-soft"
                    type="button"
                    data-bs-dismiss="modal">

                    <i class="fas fa-times"></i>

                    Cerrar

                </button>


            </div>


        </div>


    </div>


</div>


<!-- ==========================================================
     MODAL IMPORT CSV
========================================================== -->

<div
    class="modal fade f360-modal"
    id="modalUploadCSV"
    tabindex="-1"
    data-bs-backdrop="static"
    aria-labelledby="modalUploadLabel"
    aria-hidden="true">


    <div class="modal-dialog modal-lg modal-dialog-centered">


        <div class="modal-content">


            <div class="modal-header">


                <div class="d-flex align-items-center gap-3">


                    <div class="f360-modal-icon">

                        <i class="fas fa-file-upload"></i>

                    </div>


                    <div>


                        <h5
                            class="modal-title"
                            id="modalUploadLabel">

                            Importar Fórmulas desde CSV

                        </h5>


                        <div class="f360-modal-sub">

                            Carga masiva de indicadores y fórmulas estadísticas.

                        </div>


                    </div>


                </div>


                <button
                    class="btn p-2"
                    type="button"
                    data-bs-dismiss="modal"
                    aria-label="Close">

                    <i class="fas fa-times fs-5 text-white"></i>

                </button>


            </div>


            <div class="modal-body">


                <div class="f360-csv-info">


                    <i class="fas fa-info-circle"></i>


                    <div>


                        <strong>
                            Estructura requerida
                        </strong>

                        <br>

                        El archivo CSV debe incluir las columnas:

                        <br>

                        <code
                            style="
                                display:inline-block;
                                margin-top:5px;
                                color:#344054;
                                font-size:.60rem;
                            ">

                            INDICADOR, SIGLA, TIPO_INDICADOR, FORMULA,
                            EXPLICACION, OBSERVACIONES, ENUNCIADO_ANTES,
                            ENUNCIADO_AHORA, NOTAS_ADICIONALES

                        </code>


                    </div>


                </div>


                <form id="formUploadCSV">


                    <div class="f360-upload-zone">


                        <div class="f360-upload-label">


                            <i class="fas fa-file-csv"></i>


                            <div>


                                <strong>
                                    Selecciona el archivo CSV
                                </strong>


                                <span>
                                    Utiliza un archivo compatible con la estructura indicada.
                                </span>


                            </div>


                        </div>


                        <input
                            type="file"
                            class="form-control"
                            id="csvFile"
                            accept=".csv"
                            required>


                        <div class="f360-switch-card mt-3">


                            <div class="f360-switch-copy">


                                <strong>
                                    Primera fila con encabezados
                                </strong>


                                <span>
                                    Mantén activa esta opción si el CSV contiene nombres de columna.
                                </span>


                            </div>


                            <div class="form-check form-switch">


                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="csvHasHeader"
                                    checked>


                            </div>


                        </div>


                    </div>


                </form>


            </div>


            <div class="modal-footer">


                <button
                    class="f360-btn f360-btn-soft"
                    type="button"
                    data-bs-dismiss="modal">

                    <i class="fas fa-times"></i>

                    Cancelar

                </button>


                <button
                    class="f360-btn f360-btn-success"
                    type="button"
                    onclick="FORMULAS.uploadCSV()">

                    <i class="fas fa-upload"></i>

                    Cargar Archivo

                </button>


            </div>


        </div>


    </div>


</div>


<!-- ==========================================================
     REQUIRED JS
========================================================== -->

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>

<script src="assets/js/plugins/bootstrap.min.js"></script>

<script src="assets/js/pcoded.min.js"></script>

<?php include './admin/include/generic_dataTables.php'; ?>

<script
    type="text/javascript"
    src="admin/js/formulas.js">
</script>


<script>
/* ============================================================
   MICROINTERACCIONES
   No reemplazan ninguna función de FORMULAS.
============================================================ */

$(function(){


    $("#btnNuevaFormula")
        .on(
            "click",
            function(){


                /*
                  Llamada directa al método original.
                  No usamos window.FORMULAS para evitar problemas
                  si el objeto fue declarado con const/let.
                */
                FORMULAS.emptyCells();


                const card =
                    document
                        .getElementById(
                            "f360FormCard"
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


                        $("#indicador")
                            .trigger(
                                "focus"
                            );


                    },
                    420
                );


            }
        );


    $(document)
        .on(
            "click",
            ".f360-edit-formula",
            function(){


                setTimeout(
                    function(){


                        const card =
                            document
                                .getElementById(
                                    "f360FormCard"
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
