<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Departamento.php';
include './admin/classes/Grilla.php';
include './admin/classes/FichaTecnicaEncuesta.php';

// Validar permisos
$view    = SessionData::getPermission(42);
$create  = SessionData::getPermission(43);
$edit    = SessionData::getPermission(44);
$permits = SessionData::getPermission(45);

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Información de Grilla
$resp = Grilla::getAll(null);
$isvalidGrilla = $resp['output']['valid'] ?? false;
$arr = $resp['output']['response'] ?? [];

$modulo =
    'El sistema de Grilla: Pronostico, Tendencia y Probabilidad. Intenciones de Voto indirecta con condicionales de Conocimiento e imagen ';

// Información de Fichas Técnicas
$arrFichasTecnicas = FichaTecnicaEncuesta::getAll(null);
$fichas_tecnicas = $arrFichasTecnicas['output']['response'] ?? [];

// Cargos públicos
$arrCargosPub = CargosPublicos::getAll(null);
$arrCargosPub = $arrCargosPub['output']['response'] ?? [];

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
$arrDep = Departamento::getAll(null);
$arrDep = $arrDep['output']['response'] ?? [];

$optionDep = Util::getDepartamentoPrincipal();

foreach ($arrDep as $val) {

    $cod =
        $val['codigo_departamento']
        ?? '';

    $dep =
        $val['departamento']
        ?? '';

    $selected =
        (
            $cod
            ==
            Util::getDepartamentoPrincipal()
        )
        ?
        "selected"
        :
        "";

    $optionDep .=
        "<option {$selected} value='"
        .
        htmlspecialchars(
            $cod,
            ENT_QUOTES,
            'UTF-8'
        )
        .
        "'>"
        .
        htmlspecialchars(
            $cod,
            ENT_QUOTES,
            'UTF-8'
        )
        .
        " - "
        .
        htmlspecialchars(
            $dep,
            ENT_QUOTES,
            'UTF-8'
        )
        .
        "</option>";
}

function h($s){
    return htmlspecialchars(
        (string)$s,
        ENT_QUOTES,
        'UTF-8'
    );
}

// KPIs visuales
$totalGrillas = is_array($arr) ? count($arr) : 0;
$totalActivas = 0;
$totalCargoPublico = 0;
$totalConCandidatos = 0;

if (is_array($arr)) {

    foreach ($arr as $item) {

        if (
            ($item['habilitado'] ?? '')
            ===
            'si'
        ) {
            $totalActivas++;
        }

        if (
            ($item['aplica_cargos_publicos'] ?? '')
            ===
            'si'
        ) {

            $totalCargoPublico++;

            if (
                !empty(
                    $item['candidatos']
                    ?? []
                )
            ) {
                $totalConCandidatos++;
            }
        }
    }
}

$totalFichas =
    is_array($fichas_tecnicas)
    ?
    count($fichas_tecnicas)
    :
    0;

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

    <style>
    /* ==========================================================
       ESTADÍSTICA360 · GRID INTELLIGENCE STUDIO
       ----------------------------------------------------------
       Diseño visual únicamente.
       Se conservan IDs, acciones y JS del módulo GRILLA.
    ========================================================== */

    :root{
        --g360-navy-950:#07182F;
        --g360-navy-900:#0A2248;
        --g360-navy-800:#123A74;

        --g360-blue-700:#20427F;
        --g360-blue-600:#2D63BD;
        --g360-blue-500:#4B8CF7;
        --g360-cyan:#25B7DC;
        --g360-violet:#7867E8;

        --g360-success:#12B981;
        --g360-warning:#F59E0B;
        --g360-danger:#E5484D;

        --g360-page:#F3F6FB;
        --g360-card:#FFFFFF;
        --g360-card-soft:#F8FAFD;

        --g360-text:#101828;
        --g360-text-2:#344054;
        --g360-muted:#667085;
        --g360-soft:#98A2B3;

        --g360-line:#E5EAF1;

        --g360-r-xxl:30px;
        --g360-r-xl:24px;
        --g360-r-lg:18px;
        --g360-r-md:14px;

        --g360-shadow:
            0 24px 68px rgba(15,23,42,.10);

        --g360-shadow-soft:
            0 12px 34px rgba(15,23,42,.065);
    }

    *{
        box-sizing:border-box;
    }

    html{
        scroll-behavior:smooth;
    }

    body.g360-page{
        margin:0;

        background:
            radial-gradient(
                900px 500px at 3% -5%,
                rgba(75,140,247,.12),
                transparent 64%
            ),
            radial-gradient(
                760px 440px at 103% 5%,
                rgba(37,183,220,.07),
                transparent 64%
            ),
            linear-gradient(
                180deg,
                #F8FAFD 0%,
                #F2F5FA 100%
            );

        color:var(--g360-text);

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

    body.g360-page::before{
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
       CONTENT
       No se calcula la altura con .navbar.
       Esto evita que el sidebar nuevo empuje la vista.
    ========================================================== */

    .content{
        padding-top:18px !important;
        padding-bottom:38px !important;

        margin-top:0 !important;
    }

    .g360-shell{
        width:100%;

        max-width:1660px;

        margin:0 auto;

        padding:0 18px;
    }

    /* ==========================================================
       HERO
    ========================================================== */

    .g360-hero{
        position:relative;
        isolation:isolate;

        overflow:hidden;

        min-height:224px;

        margin-bottom:16px;
        padding:29px 30px;

        border:1px solid rgba(255,255,255,.12);
        border-radius:var(--g360-r-xxl);

        color:#fff;

        background:
            radial-gradient(
                540px 270px at 9% 0%,
                rgba(75,140,247,.35),
                transparent 66%
            ),
            radial-gradient(
                460px 260px at 95% 10%,
                rgba(37,183,220,.20),
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

    .g360-hero::before{
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

    .g360-hero-grid{
        display:grid;

        grid-template-columns:
            minmax(0,1fr)
            auto;

        gap:28px;

        align-items:center;
    }

    .g360-eyebrow{
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

    .g360-live-dot{
        width:7px;
        height:7px;

        border-radius:50%;

        background:#5DE4A0;

        box-shadow:
            0 0 0 5px rgba(93,228,160,.11),
            0 0 16px rgba(93,228,160,.45);
    }

    .g360-hero h1{
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

    .g360-hero h1 span{
        color:#B7D0FF;
    }

    .g360-hero p{
        max-width:830px;

        margin:11px 0 0;

        color:rgba(255,255,255,.70);

        font-size:.91rem;

        line-height:1.67;

        font-weight:500;
    }

    .g360-hero-pills{
        display:flex;

        flex-wrap:wrap;

        gap:8px;

        margin-top:18px;
    }

    .g360-hero-pill{
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

    .g360-hero-pill i{
        color:#A7C7FF;
    }

    /* ==========================================================
       KPI
    ========================================================== */

    .g360-kpis{
        display:grid;

        grid-template-columns:
            repeat(
                4,
                minmax(92px,1fr)
            );

        gap:9px;

        min-width:545px;
    }

    .g360-kpi{
        min-height:112px;

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

    .g360-kpi:hover{
        transform:translateY(-4px);

        border-color:rgba(255,255,255,.20);

        background:
            linear-gradient(
                145deg,
                rgba(255,255,255,.17),
                rgba(255,255,255,.07)
            );
    }

    .g360-kpi-icon{
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

    .g360-kpi strong{
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

    .g360-kpi span{
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

    .g360-toolbar{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        margin-bottom:16px;

        padding:13px 15px;

        border:1px solid var(--g360-line);
        border-radius:18px;

        background:rgba(255,255,255,.92);

        box-shadow:var(--g360-shadow-soft);

        backdrop-filter:blur(12px);
    }

    .g360-toolbar-copy{
        display:flex;

        align-items:center;

        gap:10px;

        min-width:0;
    }

    .g360-toolbar-icon{
        width:38px;
        height:38px;

        flex:0 0 38px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:12px;

        color:var(--g360-blue-700);

        background:#EDF4FF;

        font-size:.9rem;
    }

    .g360-toolbar-copy strong{
        display:block;

        color:var(--g360-text);

        font-size:.79rem;

        font-weight:800;
    }

    .g360-toolbar-copy span{
        display:block;

        margin-top:2px;

        color:var(--g360-soft);

        font-size:.66rem;

        font-weight:600;
    }

    /* ==========================================================
       BUTTONS
    ========================================================== */

    .g360-btn{
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

    .g360-btn-primary{
        border:0;

        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--g360-blue-500),
                var(--g360-blue-600) 50%,
                var(--g360-blue-700)
            );

        box-shadow:
            0 11px 23px rgba(32,66,127,.22);
    }

    .g360-btn-primary:hover{
        transform:translateY(-2px);

        box-shadow:
            0 16px 30px rgba(32,66,127,.29);
    }

    .g360-btn-soft{
        border:1px solid #D7E2F2;

        color:var(--g360-blue-700) !important;

        background:#fff;
    }

    .g360-btn-soft:hover{
        transform:translateY(-1px);

        border-color:#BFD2EC;

        background:#F5F9FF;
    }

    /* ==========================================================
       CARD
    ========================================================== */

    .g360-card{
        overflow:hidden;

        margin-bottom:16px;

        border:1px solid var(--g360-line);
        border-radius:var(--g360-r-xl);

        background:rgba(255,255,255,.97);

        box-shadow:var(--g360-shadow);
    }

    .g360-card-head{
        min-height:74px;

        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        padding:15px 18px;

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

    .g360-card-title{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .g360-card-icon{
        width:41px;
        height:41px;

        flex:0 0 41px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:13px;

        color:var(--g360-blue-700);

        background:#EDF4FF;

        font-size:.92rem;
    }

    .g360-card-title h2{
        margin:0;

        color:#182230;

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:.97rem;

        font-weight:800;
    }

    .g360-card-title p{
        margin:3px 0 0;

        color:var(--g360-soft);

        font-size:.65rem;

        font-weight:600;
    }

    .g360-card-body{
        padding:18px;
    }

    /* ==========================================================
       SECTIONS
    ========================================================== */

    .g360-section{
        padding:16px;

        border:1px solid #E6EBF2;
        border-radius:18px;

        background:
            linear-gradient(
                145deg,
                #FFFFFF,
                #FBFCFF
            );
    }

    .g360-section + .g360-section{
        margin-top:13px;
    }

    .g360-section-head{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        margin-bottom:14px;
    }

    .g360-section-title{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .g360-section-dot{
        width:9px;
        height:9px;

        border-radius:50%;

        background:
            linear-gradient(
                135deg,
                var(--g360-blue-500),
                var(--g360-blue-700)
            );

        box-shadow:
            0 0 0 4px rgba(75,140,247,.09);
    }

    .g360-section-title h3{
        margin:0;

        color:var(--g360-text);

        font-size:.79rem;

        font-weight:800;
    }

    .g360-section-help{
        color:var(--g360-soft);

        font-size:.61rem;

        font-weight:600;
    }

    /* ==========================================================
       FORM
    ========================================================== */

    .form-floating>.form-control,
    .form-floating>.form-select{
        min-height:58px;

        border:1px solid #D9E0EA !important;
        border-radius:14px !important;

        color:var(--g360-text-2);

        background:#FBFCFE;

        font-size:.79rem;

        font-weight:650;

        box-shadow:none !important;

        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            background .18s ease;
    }

    .form-floating>.form-control:hover,
    .form-floating>.form-select:hover{
        border-color:#BCC8D9 !important;

        background:#fff;
    }

    .form-floating>.form-control:focus,
    .form-floating>.form-select:focus{
        border-color:var(--g360-blue-500) !important;

        background:#fff;

        box-shadow:
            0 0 0 4px rgba(75,140,247,.10) !important;
    }

    .form-floating>label{
        color:#667085;

        font-size:.76rem;

        font-weight:650;
    }

    /* ==========================================================
       SWITCH
    ========================================================== */

    .g360-switch-card{
        min-height:58px;

        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        padding:9px 12px;

        border:1px solid #D9E0EA;
        border-radius:14px;

        background:#FBFCFE;
    }

    .g360-switch-copy strong{
        display:block;

        color:var(--g360-text-2);

        font-size:.70rem;

        font-weight:800;
    }

    .g360-switch-copy span{
        display:block;

        margin-top:2px;

        color:var(--g360-soft);

        font-size:.58rem;

        font-weight:600;
    }

    .g360-switch-card .form-check{
        margin:0;

        padding:0;
    }

    .g360-switch-card .form-check-input{
        width:42px !important;
        height:23px !important;

        margin:0 !important;

        cursor:pointer;
    }

    .g360-switch-card .form-check-input:checked{
        border-color:var(--g360-success);

        background-color:var(--g360-success);
    }

    /* ==========================================================
       CANDIDATE BUILDER
    ========================================================== */

    #table-container{
        position:relative;

        overflow:hidden;

        margin-top:14px !important;

        padding:16px !important;

        border:1px solid #E4EAF2 !important;
        border-radius:18px !important;

        background:
            radial-gradient(
                330px 150px at 4% 0%,
                rgba(75,140,247,.065),
                transparent 72%
            ),
            linear-gradient(
                180deg,
                #FBFDFF,
                #F8FAFD
            ) !important;

        box-shadow:
            0 12px 30px rgba(15,23,42,.055);
    }

    .g360-candidate-head{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        margin-bottom:13px;
    }

    .g360-candidate-title{
        display:flex;

        align-items:center;

        gap:10px;
    }

    .g360-candidate-icon{
        width:39px;
        height:39px;

        flex:0 0 39px;

        display:flex;

        align-items:center;
        justify-content:center;

        border-radius:12px;

        color:var(--g360-blue-700);

        background:#EEF5FF;

        font-size:.84rem;
    }

    .g360-candidate-title strong{
        display:block;

        color:var(--g360-text-2);

        font-size:.74rem;

        font-weight:800;
    }

    .g360-candidate-title span{
        display:block;

        margin-top:2px;

        color:var(--g360-soft);

        font-size:.59rem;

        font-weight:600;
    }

    /* ==========================================================
       TABLE SHELL
    ========================================================== */

    .g360-table-shell{
        overflow:hidden;

        border:1px solid #E5EAF1;
        border-radius:16px;

        background:#fff;
    }

    #candidatosTable,
    #dynamictable,
    #candidatosModalTable{
        width:100% !important;

        margin:0 !important;
    }

    #candidatosTable{
        border-collapse:separate !important;
        border-spacing:0 !important;
    }

    #candidatosTable thead th{
        padding:11px 12px !important;

        border-top:0 !important;
        border-bottom:1px solid #E6EBF2 !important;

        color:#667085 !important;
        background:#F8FAFC !important;

        font-size:.59rem !important;

        font-weight:800 !important;

        letter-spacing:.40px;

        text-transform:uppercase;

        white-space:nowrap;
    }

    #candidatosTable tbody td{
        padding:10px 12px !important;

        border-bottom:1px solid #EEF1F5 !important;

        color:#344054 !important;
        background:#fff !important;

        font-size:.67rem !important;

        vertical-align:middle !important;
    }

    #candidatosTable tbody tr:hover td{
        background:
            linear-gradient(
                90deg,
                #F5F9FF,
                #FFFFFF
            ) !important;
    }

    /* ==========================================================
       ACTION BAR
    ========================================================== */

    .g360-action-bar{
        position:sticky;

        bottom:12px;

        z-index:20;

        margin-top:15px;
    }

    .g360-action-inner{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        padding:12px;

        border:1px solid rgba(216,225,238,.94);
        border-radius:17px;

        background:rgba(255,255,255,.92);

        box-shadow:
            0 15px 35px rgba(15,23,42,.11);

        backdrop-filter:blur(16px);
    }

    .g360-action-copy{
        display:flex;

        align-items:center;

        gap:9px;
    }

    .g360-action-state{
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

    .g360-action-copy strong{
        display:block;

        color:var(--g360-text-2);

        font-size:.69rem;

        font-weight:800;
    }

    .g360-action-copy span{
        display:block;

        margin-top:2px;

        color:var(--g360-soft);

        font-size:.60rem;

        font-weight:600;
    }

    /* ==========================================================
       DIRECTORY
    ========================================================== */

    .g360-directory{
        overflow:hidden;

        margin-top:16px;

        border:1px solid var(--g360-line);
        border-radius:var(--g360-r-xl);

        background:#fff;

        box-shadow:var(--g360-shadow);
    }

    .g360-directory-head{
        display:flex;

        align-items:center;
        justify-content:space-between;

        gap:12px;

        padding:18px;

        border-bottom:1px solid #EDF0F5;

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

    .g360-directory-title{
        display:flex;

        align-items:center;

        gap:11px;
    }

    .g360-directory-icon{
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
                var(--g360-blue-500),
                var(--g360-blue-700)
            );

        box-shadow:
            0 10px 22px rgba(32,66,127,.20);

        font-size:.92rem;
    }

    .g360-directory-title h2{
        margin:0;

        color:var(--g360-text);

        font-family:
            "Manrope",
            "Inter",
            sans-serif;

        font-size:1rem;

        font-weight:800;
    }

    .g360-directory-title p{
        margin:3px 0 0;

        color:var(--g360-soft);

        font-size:.65rem;

        font-weight:600;
    }

    .g360-count{
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

    .g360-directory-body{
        padding:15px;
    }

    /* ==========================================================
       MAIN TABLE
    ========================================================== */

    #dynamictable{
        border-collapse:separate !important;
        border-spacing:0 7px !important;
    }

    #dynamictable thead th{
        padding:10px 11px !important;

        border:0 !important;

        color:#667085 !important;

        background:transparent !important;

        font-size:.59rem !important;

        font-weight:800 !important;

        letter-spacing:.40px;

        text-transform:uppercase;

        white-space:nowrap !important;
    }

    #dynamictable tbody td,
    #dynamictable tbody th{
        padding:10px 11px !important;

        border-top:1px solid #E9EDF4 !important;
        border-bottom:1px solid #E9EDF4 !important;

        color:#344054 !important;
        background:#fff !important;

        font-size:.67rem !important;

        line-height:1.45;

        vertical-align:middle !important;

        transition:
            background .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #dynamictable tbody tr > *:first-child{
        border-left:1px solid #E9EDF4 !important;

        border-radius:
            13px 0 0 13px;
    }

    #dynamictable tbody tr > *:last-child{
        border-right:1px solid #E9EDF4 !important;

        border-radius:
            0 13px 13px 0;
    }

    #dynamictable tbody tr{
        transition:transform .18s ease;
    }

    #dynamictable tbody tr:hover{
        transform:translateY(-2px);
    }

    #dynamictable tbody tr:hover td,
    #dynamictable tbody tr:hover th{
        border-color:#DCE7F6 !important;

        background:
            linear-gradient(
                90deg,
                #F6FAFF,
                #FFFFFF
            ) !important;

        box-shadow:
            0 9px 23px rgba(15,23,42,.05);
    }

    .g360-grid-name{
        min-width:210px;

        color:#1D2939 !important;

        font-weight:800 !important;
    }

    /* ==========================================================
       STATUS / TAGS
    ========================================================== */

    .g360-status{
        display:inline-flex;

        align-items:center;

        gap:5px;

        min-height:27px;

        padding:5px 8px;

        border-radius:8px;

        font-size:.60rem;

        font-weight:800;

        white-space:nowrap;
    }

    .g360-status-success{
        color:#06795B;

        border:1px solid #D1FAE5;

        background:#ECFDF5;
    }

    .g360-status-danger{
        color:#B42318;

        border:1px solid #FEE4E2;

        background:#FEF3F2;
    }

    .g360-status-blue{
        color:#175CD3;

        border:1px solid #D1E9FF;

        background:#EFF8FF;
    }

    .g360-status-neutral{
        color:#475467;

        border:1px solid #EAECF0;

        background:#F9FAFB;
    }

    .g360-status-violet{
        color:#6941C6;

        border:1px solid #E9D7FE;

        background:#F9F5FF;
    }

    /* ==========================================================
       ACTION BUTTONS
    ========================================================== */

    .g360-icon-btn{
        width:35px;
        height:35px;

        display:inline-flex;

        align-items:center;
        justify-content:center;

        padding:0;

        border:0 !important;
        border-radius:10px !important;

        color:#fff !important;

        transition:
            transform .18s ease,
            box-shadow .18s ease;
    }

    .g360-icon-btn:hover{
        transform:translateY(-2px);
    }

    .g360-edit{
        background:
            linear-gradient(
                135deg,
                #4F8CFF,
                #2563B9
            ) !important;

        box-shadow:
            0 8px 16px rgba(37,99,185,.17);
    }

    .g360-view{
        background:
            linear-gradient(
                135deg,
                #F06A6A,
                #C83245
            ) !important;

        box-shadow:
            0 8px 16px rgba(200,50,69,.15);
    }

    .g360-results{
        background:
            linear-gradient(
                135deg,
                #2DB783,
                #08785C
            ) !important;

        box-shadow:
            0 8px 16px rgba(8,120,92,.15);
    }

    .g360-candidates{
        background:
            linear-gradient(
                135deg,
                #7867E8,
                #5446B7
            ) !important;

        box-shadow:
            0 8px 16px rgba(84,70,183,.15);
    }

    /* ==========================================================
       DATATABLES
    ========================================================== */

    .dataTables_wrapper{
        width:100% !important;

        color:var(--g360-muted);

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

    .dataTables_wrapper .dataTables_filter input{
        width:min(270px,100%);

        min-height:39px;

        margin-left:8px;

        padding:0 12px;

        border:1px solid #D7DEE9;
        border-radius:11px;

        background:#fff;

        outline:none;
    }

    .dataTables_wrapper .dataTables_filter input:focus{
        border-color:var(--g360-blue-500);

        box-shadow:
            0 0 0 4px rgba(75,140,247,.10);
    }

    .dataTables_wrapper .dataTables_length select{
        min-height:38px;

        border:1px solid #D7DEE9;
        border-radius:10px;

        background:#fff;
    }

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

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        color:#fff !important;

        background:
            linear-gradient(
                135deg,
                var(--g360-blue-500),
                var(--g360-blue-700)
            ) !important;

        box-shadow:
            0 8px 18px rgba(32,66,127,.20) !important;
    }

    /* ==========================================================
       MODAL
    ========================================================== */

    #participantsModal .modal-content{
        overflow:hidden;

        border:1px solid rgba(15,23,42,.09) !important;
        border-radius:24px !important;

        box-shadow:
            0 30px 82px rgba(15,23,42,.25) !important;
    }

    #participantsModal .modal-header{
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
            linear-gradient(
                135deg,
                #173D79,
                #102A56 55%,
                #081B38
            ) !important;
    }

    #participantsModal .modal-header::after{
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

    #participantsModal .modal-title{
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

    #participantsModal .modal-body{
        padding:18px;

        background:
            linear-gradient(
                180deg,
                #FFFFFF,
                #F8FAFD
            );
    }

    #participantsModal .modal-footer{
        border-top:1px solid #E8ECF2;

        background:#fff;
    }

    #candidatosModalTable thead th{
        color:#667085;

        font-size:.60rem;

        font-weight:800;

        text-transform:uppercase;

        white-space:nowrap;
    }

    #candidatosModalTable tbody td{
        color:#344054;

        font-size:.68rem;

        vertical-align:middle;
    }

    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width:1320px){

        .g360-hero-grid{
            grid-template-columns:1fr;
        }

        .g360-kpis{
            min-width:0;

            width:100%;
        }
    }

    @media (max-width:991px){

        .g360-shell{
            padding:0 13px;
        }

        .g360-hero{
            padding:23px;
        }
    }

    @media (max-width:767px){

        .content{
            padding-top:12px !important;
        }

        .g360-shell{
            padding:0 10px;
        }

        .g360-hero{
            min-height:0;

            padding:20px 17px;

            border-radius:22px;
        }

        .g360-hero h1{
            font-size:1.8rem;
        }

        .g360-hero p{
            font-size:.80rem;
        }

        .g360-kpis{
            grid-template-columns:repeat(2,1fr);
        }

        .g360-toolbar{
            align-items:flex-start;

            flex-direction:column;
        }

        .g360-toolbar .g360-btn{
            width:100%;
        }

        .g360-card{
            border-radius:19px;
        }

        .g360-card-head{
            align-items:flex-start;

            padding:14px;
        }

        .g360-card-body{
            padding:13px;
        }

        .g360-section{
            padding:13px;
        }

        #table-container{
            padding:12px !important;
        }

        .g360-candidate-head{
            align-items:flex-start;

            flex-direction:column;
        }

        .g360-action-inner{
            align-items:stretch;

            flex-direction:column;
        }

        .g360-action-inner > .d-flex{
            width:100%;
        }

        .g360-action-inner .g360-btn{
            flex:1;
        }

        .g360-directory{
            border-radius:19px;
        }

        .g360-directory-head{
            align-items:flex-start;

            padding:14px;
        }

        .g360-directory-body{
            padding:10px;
        }

        .table-responsive{
            overflow-x:auto;

            -webkit-overflow-scrolling:touch;
        }

        #candidatosTable{
            min-width:900px;
        }

        #dynamictable{
            min-width:1120px;
        }

        .dataTables_wrapper .dataTables_filter input{
            width:100%;

            margin:6px 0 0;
        }

        .dataTables_wrapper .dataTables_paginate{
            justify-content:center;

            flex-wrap:wrap;
        }
    }

    @media (max-width:480px){

        .g360-kpis{
            gap:7px;
        }

        .g360-kpi{
            min-height:96px;

            padding:12px;
        }

        .g360-kpi strong{
            font-size:1.16rem;
        }

        .g360-kpi span{
            font-size:.56rem;
        }

        .g360-action-copy{
            display:none;
        }

        .g360-action-inner{
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

</head>


<body class="g360-page">

<main
    class="main"
    id="top">


    <?php include './admin/include/navbar.php'; ?>

    <?php include './admin/include/header.php'; ?>


    <div class="content">

        <div class="g360-shell">


            <!-- =================================================
                 HERO
            ================================================== -->

            <section class="g360-hero">

                <div class="g360-hero-grid">


                    <div>

                        <div class="g360-eyebrow">

                            <span class="g360-live-dot"></span>

                            Estadística360 · Grid Intelligence Studio

                        </div>


                        <h1>

                            Sistema de
                            <span>Grillas</span>

                        </h1>


                        <p>

                            Configura estudios de pronóstico, tendencia y
                            probabilidad, vincula fichas técnicas, candidatos
                            políticos y territorio, y consulta resultados en
                            tiempo real desde un único centro de inteligencia.

                        </p>


                        <div class="g360-hero-pills">


                            <span class="g360-hero-pill">

                                <i class="fas fa-chart-line"></i>

                                Pronóstico y tendencia

                            </span>


                            <span class="g360-hero-pill">

                                <i class="fas fa-user-tie"></i>

                                Candidatos políticos

                            </span>


                            <span class="g360-hero-pill">

                                <i class="fas fa-bolt"></i>

                                Resultados en tiempo real

                            </span>


                        </div>

                    </div>


                    <div class="g360-kpis">


                        <div class="g360-kpi">

                            <div class="g360-kpi-icon">

                                <i class="fas fa-table-cells-large"></i>

                            </div>

                            <strong>
                                <?= (int)$totalGrillas ?>
                            </strong>

                            <span>
                                Grillas registradas
                            </span>

                        </div>


                        <div class="g360-kpi">

                            <div class="g360-kpi-icon">

                                <i class="fas fa-circle-check"></i>

                            </div>

                            <strong>
                                <?= (int)$totalActivas ?>
                            </strong>

                            <span>
                                Grillas habilitadas
                            </span>

                        </div>


                        <div class="g360-kpi">

                            <div class="g360-kpi-icon">

                                <i class="fas fa-landmark"></i>

                            </div>

                            <strong>
                                <?= (int)$totalCargoPublico ?>
                            </strong>

                            <span>
                                Para cargo público
                            </span>

                        </div>


                        <div class="g360-kpi">

                            <div class="g360-kpi-icon">

                                <i class="fas fa-clipboard-check"></i>

                            </div>

                            <strong>
                                <?= (int)$totalFichas ?>
                            </strong>

                            <span>
                                Fichas técnicas
                            </span>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 TOOLBAR
            ================================================== -->

            <section class="g360-toolbar">

                <div class="g360-toolbar-copy">

                    <div class="g360-toolbar-icon">

                        <i class="fas fa-compass"></i>

                    </div>


                    <div>

                        <strong>
                            Centro de configuración de grillas
                        </strong>

                        <span>
                            Crea, actualiza y analiza estudios electorales desde un único módulo.
                        </span>

                    </div>

                </div>


                <?php if ($create): ?>

                    <button
                        type="button"
                        class="g360-btn g360-btn-primary"
                        id="btnNuevaGrilla">

                        <i class="fas fa-plus"></i>

                        Nueva grilla

                    </button>

                <?php endif; ?>

            </section>


            <div class="mb-2">

                <span
                    id="spanEncuesta"
                    class="small text-muted">
                </span>

                <span
                    id="spanModulo"
                    class="d-none">

                    <?= h($modulo) ?>

                </span>

            </div>


            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <section
                class="g360-card"
                id="g360FormCard">


                <div class="g360-card-head">

                    <div class="g360-card-title">

                        <div class="g360-card-icon">

                            <i class="fas fa-pen-ruler"></i>

                        </div>


                        <div>

                            <h2>
                                Crear / Editar Grilla
                            </h2>

                            <p>
                                Información general, analítica, territorio y candidatos.
                            </p>

                        </div>

                    </div>


                    <span class="g360-status g360-status-blue">

                        <i class="fas fa-asterisk"></i>

                        Campos requeridos

                    </span>

                </div>


                <div class="g360-card-body">


                    <form
                        id="formgrilla"
                        role="form"
                        autocomplete="false">


                        <input
                            type="hidden"
                            name="op"
                            id="op">


                        <input
                            type="hidden"
                            name="idGrilla"
                            id="idGrilla">


                        <!-- =====================================
                             IDENTIFICACIÓN
                        ====================================== -->

                        <section class="g360-section">

                            <div class="g360-section-head">

                                <div class="g360-section-title">

                                    <span class="g360-section-dot"></span>

                                    <h3>
                                        Identificación del estudio
                                    </h3>

                                </div>


                                <span class="g360-section-help">

                                    Nombre, descripción y ficha técnica

                                </span>

                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-lg-6">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="grilla"
                                            name="grilla"
                                            placeholder="Texto del grilla a realizar"
                                            value=""
                                            required>


                                        <label for="grilla">

                                            Grilla
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-lg-6">

                                    <div class="form-floating">

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="descripcion_grilla"
                                            name="descripcion_grilla"
                                            placeholder="Ingrese la descripción o pregunta del grilla"
                                            value="">


                                        <label for="descripcion_grilla">

                                            Descripción del Grilla

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12">

                                    <div class="form-floating">

                                        <select
                                            class="form-select"
                                            id="tbl_ficha_tecnica_encuesta_id"
                                            name="tbl_ficha_tecnica_encuesta_id">

                                            <option value="">
                                                Seleccione una ficha técnica
                                            </option>


                                            <?php foreach ($fichas_tecnicas as $ficha): ?>

                                                <option
                                                    value="<?= h($ficha['id'] ?? '') ?>">

                                                    <?= h(
                                                        $ficha['tipo_estudio']
                                                        ??
                                                        (
                                                            'Ficha #'
                                                            .
                                                            ($ficha['id'] ?? '')
                                                        )
                                                    ) ?>

                                                    -

                                                    <?= h(
                                                        substr(
                                                            (string)(
                                                                $ficha['realizada_por_o_encomendada_por']
                                                                ?? ''
                                                            ),
                                                            0,
                                                            100
                                                        )
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>


                                        <label for="tbl_ficha_tecnica_encuesta_id">

                                            Ficha Técnica de Encuesta
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                            </div>

                        </section>


                        <!-- =====================================
                             ANALÍTICA Y ESTADO
                        ====================================== -->

                        <section class="g360-section">

                            <div class="g360-section-head">

                                <div class="g360-section-title">

                                    <span class="g360-section-dot"></span>

                                    <h3>
                                        Configuración analítica
                                    </h3>

                                </div>


                                <span class="g360-section-help">

                                    Inferenciales, cargo público y estado

                                </span>

                            </div>


                            <div class="row g-3">


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
                                            onchange="GRILLA.handleSondeParaCargoPublicoChange();"
                                            required>

                                            <option value="no">
                                                No
                                            </option>

                                            <option value="si">
                                                Sí
                                            </option>

                                        </select>


                                        <label for="aplica_cargos_publicos">

                                            Grilla para cargo público
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-6 col-lg-4">

                                    <div class="g360-switch-card">

                                        <div class="g360-switch-copy">

                                            <strong>
                                                Grilla habilitada
                                            </strong>

                                            <span>
                                                Disponible para uso dentro del sistema
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
                             CARGO / TERRITORIO
                        ====================================== -->

                        <section class="g360-section">

                            <div class="g360-section-head">

                                <div class="g360-section-title">

                                    <span class="g360-section-dot"></span>

                                    <h3>
                                        Focalización electoral y territorio
                                    </h3>

                                </div>


                                <span class="g360-section-help">

                                    Campos dinámicos según el cargo seleccionado

                                </span>

                            </div>


                            <div class="row g-3">


                                <div class="col-12 col-md-4 cargo-publico-fields d-none">

                                    <div class="form-floating">

                                        <select
                                            class="form-select"
                                            id="tbl_cargo_publico_id"
                                            name="tbl_cargo_publico_id"
                                            onchange="GRILLA.handleCargoPublicoChange(this);"
                                            required>

                                            <?= $optionCargosPub ?>

                                        </select>


                                        <label for="tbl_cargo_publico_id">

                                            Cargo Público
                                            <span class="text-danger">*</span>

                                        </label>

                                    </div>

                                </div>


                                <div class="col-12 col-md-4 departamento-municipio-fields d-none">

                                    <div class="form-floating">

                                        <select
                                            onchange="DEPARTAMENTO.getMunicipios(), GRILLA.filterAndShowData();"
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
                                    class="col-12 col-md-4 departamento-municipio-fields d-none"
                                    id="alcaldia-container">

                                    <div class="form-floating">

                                        <select
                                            onchange="GRILLA.filterAndShowData();"
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


                            <!-- =================================
                                 CANDIDATES
                            ================================== -->

                            <div
                                id="table-container"
                                class="table-candidatos">


                                <div class="g360-candidate-head">

                                    <div class="g360-candidate-title">

                                        <div class="g360-candidate-icon">

                                            <i class="fas fa-user-tie"></i>

                                        </div>


                                        <div>

                                            <strong>
                                                Candidatos a postular
                                            </strong>

                                            <span>
                                                Selecciona los participantes que harán parte de la grilla.
                                            </span>

                                        </div>

                                    </div>


                                    <span class="g360-status g360-status-violet">

                                        <i class="fas fa-filter"></i>

                                        Selección dinámica

                                    </span>

                                </div>


                                <div class="g360-table-shell">

                                    <div class="table-responsive">


                                        <table
                                            class="table table-sm fs-9 mb-0"
                                            id="candidatosTable">

                                            <thead>

                                                <tr>

                                                    <th>
                                                        Seleccionar
                                                    </th>

                                                    <th>
                                                        Foto
                                                    </th>

                                                    <th>
                                                        Nombre Completo
                                                    </th>

                                                    <th>
                                                        Cargo Público
                                                    </th>

                                                    <th>
                                                        Partido(s) Político(s)
                                                    </th>

                                                    <th>
                                                        Municipio
                                                    </th>

                                                    <th>
                                                        Departamento
                                                    </th>

                                                </tr>

                                            </thead>


                                            <tbody class="list">

                                                <!-- Los datos se renderizarán aquí con JavaScript -->

                                            </tbody>

                                        </table>


                                    </div>

                                </div>

                            </div>


                        </section>


                        <!-- =====================================
                             ACTIONS
                        ====================================== -->

                        <div class="g360-action-bar">

                            <div class="g360-action-inner">

                                <div class="g360-action-copy">

                                    <div class="g360-action-state">

                                        <i class="fas fa-check"></i>

                                    </div>


                                    <div>

                                        <strong>
                                            Configuración preparada
                                        </strong>

                                        <span>
                                            Guarda o actualiza la grilla sin salir del módulo.
                                        </span>

                                    </div>

                                </div>


                                <div class="d-flex align-items-center gap-2">

                                    <button
                                        type="button"
                                        onclick="GRILLA.emptyCells();"
                                        class="g360-btn g360-btn-soft">

                                        <i class="fas fa-rotate-left"></i>

                                        Cancelar

                                    </button>


                                    <?php if ($create && $edit): ?>

                                        <button
                                            class="g360-btn g360-btn-primary"
                                            type="button"
                                            onclick="GRILLA.validateData();">

                                            <i class="fas fa-floppy-disk"></i>

                                            Guardar grilla

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

            <section class="g360-directory">

                <div class="g360-directory-head">

                    <div class="g360-directory-title">

                        <div class="g360-directory-icon">

                            <i class="fas fa-table-list"></i>

                        </div>


                        <div>

                            <h2>
                                Grillas registradas
                            </h2>

                            <p>
                                Edita estudios, consulta estructura y abre resultados en tiempo real.
                            </p>

                        </div>

                    </div>


                    <span class="g360-count">

                        <i class="fas fa-database"></i>

                        <?= (int)$totalGrillas ?>

                        <?= $totalGrillas === 1 ? 'registro' : 'registros' ?>

                    </span>

                </div>


                <div class="g360-directory-body">

                    <div class="table-responsive">

                        <table
                            id="dynamictable"
                            class="table table-sm fs-9 mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Editar
                                    </th>

                                    <th>
                                        Ver Estudio
                                    </th>

                                    <th>
                                        Ver Resultados
                                    </th>

                                    <th>
                                        Grilla
                                    </th>

                                    <th>
                                        Cargo público
                                    </th>

                                    <th>
                                        Candidatos
                                    </th>

                                    <th>
                                        Inferencial
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Estado
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="list">


                            <?php if ($isvalidGrilla && count($arr) > 0): ?>


                                <?php foreach ($arr as $item): ?>


                                    <?php

                                        $id =
                                            (int)(
                                                $item['id']
                                                ?? 0
                                            );

                                        $itemJson =
                                            htmlspecialchars(
                                                json_encode($item),
                                                ENT_QUOTES
                                                |
                                                ENT_HTML5,
                                                'UTF-8'
                                            );

                                    ?>


                                    <tr>


                                        <!-- EDITAR -->

                                        <td>

                                            <?php if ($edit): ?>

                                                <button
                                                    type="button"
                                                    class="btn g360-icon-btn g360-edit g360-edit-action"
                                                    title="Editar"
                                                    onclick="GRILLA.editData(<?= $id ?>)">

                                                    <i class="uil uil-edit"></i>

                                                </button>

                                            <?php endif; ?>

                                        </td>


                                        <!-- VER -->

                                        <td>

                                            <button
                                                type="button"
                                                class="btn g360-icon-btn g360-view"
                                                title="Ver Estudio"
                                                onclick="GRILLA.showGrilla('<?= $itemJson ?>')">

                                                <i class="uil uil-eye"></i>

                                            </button>

                                        </td>


                                        <!-- RESULTADOS -->

                                        <td>

                                            <button
                                                type="button"
                                                class="btn g360-icon-btn g360-results"
                                                title="Ver Resultados en Tiempo Real"
                                                onclick="GRILLA.showResultados('<?= $itemJson ?>')">

                                                <i class="fas fa-chart-bar"></i>

                                            </button>

                                        </td>


                                        <!-- GRILLA -->

                                        <th class="g360-grid-name">

                                            <?= h($item['grilla'] ?? '') ?>

                                        </th>


                                        <!-- CARGO -->

                                        <td>

                                            <?php if (($item['aplica_cargos_publicos'] ?? '') === 'si'): ?>

                                                <span class="g360-status g360-status-blue">

                                                    <i class="fas fa-landmark"></i>

                                                    Sí

                                                </span>

                                            <?php else: ?>

                                                <span class="g360-status g360-status-neutral">

                                                    No

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- CANDIDATOS -->

                                        <td>

                                            <?php if (($item['aplica_cargos_publicos'] ?? '') == 'si'): ?>

                                                <button
                                                    type="button"
                                                    class="btn g360-icon-btn g360-candidates"
                                                    title="Candidatos de la grilla"
                                                    onclick="showParticipantsModal(
                                                        <?= htmlspecialchars(
                                                            json_encode(
                                                                $item['candidatos']
                                                                ?? []
                                                            ),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>,
                                                        '<?= h($item['grilla'] ?? '') ?>'
                                                    )">

                                                    <i class="fas fa-users"></i>

                                                </button>

                                            <?php else: ?>

                                                <span class="g360-status g360-status-neutral">

                                                    No aplica

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- INFERENCIAL -->

                                        <th>

                                            <span class="g360-status g360-status-violet">

                                                <?= h($item['tipo_inferenciales'] ?? '') ?>

                                            </span>

                                        </th>


                                        <!-- FECHA -->

                                        <td>

                                            <?= h($item['dtcreate'] ?? '') ?>

                                        </td>


                                        <!-- ESTADO -->

                                        <td>

                                            <?php if (($item['habilitado'] ?? '') === 'si'): ?>

                                                <span class="g360-status g360-status-success">

                                                    <i class="fas fa-check-circle"></i>

                                                    Activo

                                                </span>

                                            <?php else: ?>

                                                <span class="g360-status g360-status-danger">

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


            <!-- =================================================
                 MODAL CANDIDATES
            ================================================== -->

            <div
                class="modal fade"
                id="participantsModal"
                tabindex="-1"
                data-bs-backdrop="static"
                aria-labelledby="modalPermisosLabel"
                aria-hidden="true">


                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">


                    <div class="modal-content">


                        <div class="modal-header justify-content-between">


                            <div>

                                <h5
                                    class="modal-title"
                                    id="modalPermisosLabel">

                                    <i class="fas fa-users me-2"></i>

                                    Candidatos de la grilla

                                </h5>


                                <div
                                    style="
                                        position:relative;
                                        z-index:2;
                                        margin-top:4px;
                                        color:rgba(255,255,255,.65);
                                        font-size:.64rem;
                                        font-weight:600;
                                    ">

                                    <span id="sondeo-title"></span>

                                </div>

                            </div>


                            <button
                                class="btn p-1"
                                type="button"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                onclick="UTIL.clearForm('formpermission');">

                                <span class="fas fa-times fs-9 text-white"></span>

                            </button>


                        </div>


                        <div
                            class="modal-body"
                            style="
                                max-height:60vh;
                                overflow-y:auto;
                            ">


                            <div class="g360-table-shell">


                                <div class="table-responsive">


                                    <table
                                        class="table table-sm table-striped w-100 mb-0"
                                        id="candidatosModalTable">


                                        <thead>

                                            <tr>

                                                <th>
                                                    Foto
                                                </th>

                                                <th>
                                                    Candidato
                                                </th>

                                                <th>
                                                    Cargo Público
                                                </th>

                                                <th>
                                                    Partido(s) Político(s)
                                                </th>

                                                <th>
                                                    Municipio
                                                </th>

                                                <th>
                                                    Departamento
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody class="list">

                                            <!-- Los datos se renderizarán aquí con JavaScript -->

                                        </tbody>


                                    </table>


                                </div>


                            </div>


                        </div>


                        <div class="modal-footer">


                            <button
                                class="g360-btn g360-btn-soft"
                                type="button"
                                onclick="hideParticipantsModal();">

                                <i class="fas fa-times"></i>

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
     REQUIRED JS
     Se conserva el orden funcional del archivo original.
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
    src="admin/js/grilla.js">
</script>


<script>
/* ============================================================
   FLUJO ORIGINAL
   No usamos window.GRILLA: llamada directa para evitar problemas
   si el objeto fue declarado con const/let en grilla.js.
============================================================ */

GRILLA.handleCargoPublicoChange();

GRILLA.handleSondeParaCargoPublicoChange();

setTimeout(
    function(){

        DEPARTAMENTO.getMunicipios();

    },
    1000
);


/* ============================================================
   MICROINTERACCIONES VISUALES
   No modifican la lógica del módulo.
============================================================ */

$(function(){


    $("#btnNuevaGrilla")
        .on(
            "click",
            function(){


                if (
                    typeof GRILLA.emptyCells
                    ===
                    "function"
                ) {

                    GRILLA.emptyCells();

                }


                const card =
                    document
                        .getElementById(
                            "g360FormCard"
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

                        $("#grilla")
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
            ".g360-edit-action",
            function(){


                setTimeout(
                    function(){

                        const card =
                            document
                                .getElementById(
                                    "g360FormCard"
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
