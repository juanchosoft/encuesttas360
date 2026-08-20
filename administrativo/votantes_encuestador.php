<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Votantes.php';
include './admin/classes/Departamento.php';
include './admin/classes/Ciudad.php';
include './admin/classes/Sondeo.php';
include './admin/classes/FichaTecnicaEncuesta.php';
include './admin/classes/Pregunta.php';

include './admin/include/generic_info_configuracion.php';

/** Escape HTML con UTF-8 explícito (evita caracteres raros en textos de encuesta) */
if (!function_exists('ve_h')) {
  function ve_h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

$config = Util::getInformacionConfiguracion();
$opcionActivaWeb = $config[0]['opcion_activa_web'] ?? '';

$view = SessionData::administrador() || SessionData::superAdministrador() || SessionData::encuestador() ? true : true;
if (!$view) { require 'permiso_denegado.php'; exit; }


$codigoDepartamentoSesion = SessionData::getCodigoDepartamento();
$codigoMunicipioSesion = SessionData::getCodigoMunicipio();
$departamentos = Departamento::getAll([]);
$departamentosResponse = $departamentos['output']['response'] ?? [];

$codigosDepartamentoDisponibles = array_map(static function ($dep) {
    return (string)($dep['codigo_departamento'] ?? '');
}, $departamentosResponse);

$codigoDepartamentoEncuestador = (string)$codigoDepartamentoSesion;
if (
    $codigoDepartamentoEncuestador === ''
    || !in_array($codigoDepartamentoEncuestador, $codigosDepartamentoDisponibles, true)
) {
    $codigoDepartamentoEncuestador = (string)($codigo_departamento ?? '');
}

$codigoMunicipioEncuestador = '';
foreach ([(string)$codigoMunicipioSesion, (string)($codigoMunicipioConfiguracion ?? '')] as $codigoMunicipioCandidato) {
    if ($codigoMunicipioCandidato === '' || $codigoDepartamentoEncuestador === '') {
        continue;
    }

    $municipioResult = Ciudad::getInformacionCiudad([
        'codigo_departamento' => $codigoDepartamentoEncuestador,
        'codigo_muncipio' => $codigoMunicipioCandidato,
    ]);

    if (($municipioResult['output']['valid'] ?? false) === true) {
        $codigoMunicipioEncuestador = $codigoMunicipioCandidato;
        break;
    }
}

$optionDep = "";
foreach ($departamentosResponse as $dep) {
    $codigoDepartamento = ve_h($dep['codigo_departamento'] ?? '');
    $nombreDepartamento = ve_h($dep['departamento'] ?? '');
    $selected = ((string)($dep['codigo_departamento'] ?? '') === $codigoDepartamentoEncuestador) ? "selected" : "";
    $optionDep .= "<option value='{$codigoDepartamento}' {$selected}>{$codigoDepartamento} - {$nombreDepartamento}</option>";
}
$arr = Votantes::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];
$modulo = 'Votantes';

$mostrarSondeo = in_array($opcionActivaWeb, ['sondeo', 'ambos'], true);
$mostrarCuestionario = in_array($opcionActivaWeb, ['cuestionario', 'ambos'], true);

$sondeoActivo = null;
if ($mostrarSondeo) {
  $arrSondeos = Sondeo::getAll(null);
  $sondeos = $arrSondeos['output']['response'] ?? [];
  foreach ($sondeos as $sondeo) {
    if (($sondeo['habilitado'] ?? 'no') === 'si' && ($sondeo['vigente'] ?? false) === true) {
      $sondeoActivo = $sondeo;
      break;
    }
  }
}

$cuestionarioActivo = null;
$preguntasCuestionario = [];
if ($mostrarCuestionario) {
  $arrCuestionarios = FichaTecnicaEncuesta::getAll([]);
  $cuestionarios = $arrCuestionarios['output']['response'] ?? [];
  foreach ($cuestionarios as $cuestionario) {
    if (($cuestionario['habilitado'] ?? 'no') === 'si') {
      $cuestionarioActivo = $cuestionario;

      $preguntasResult = Pregunta::getAll(['tbl_ficha_tecnica_encuesta_id' => $cuestionario['id']]);
      if (($preguntasResult['output']['valid'] ?? false)) {
        $preguntasCuestionario = $preguntasResult['output']['response'] ?? [];
      }
      break;
    }
  }
}
?>

<body class="">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>
  <!-- [ Pre-loader ] End -->

  <!-- [ navigation menu ] start -->
  <?php include './admin/include/navbar.php'; ?>
  <!-- [ navigation menu ] end -->

  <!-- [ Header ] start -->
  <?php include './admin/include/header.php'; ?>
  <!-- [ Header ] end -->

  <style>
    :root{
      --ink:#0f172a;
      --muted:#64748b;
      --brand:#13357b;
      --brand2:#0b2a63;
      --bg:#f6f8fc;
      --card:#ffffff;
      --border:1px solid rgba(2,6,23,.10);
      --shadow:0 14px 40px rgba(2,6,23,.10);
      --shadow2:0 18px 60px rgba(2,6,23,.16);
      --r1:18px;
      --r2:22px;
      --page-pad: 12px;
      --safe-bottom: env(safe-area-inset-bottom, 0px);
    }

    .content{ background: var(--bg); }
    .ve-page{
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 var(--page-pad) calc(88px + var(--safe-bottom));
      box-sizing: border-box;
    }

    /* Header Card */
    .hero-card{
      background: linear-gradient(135deg, rgba(19,53,123,.10), rgba(255,255,255,0));
      border: var(--border);
      border-radius: 22px;
      box-shadow: var(--shadow);
      padding: 16px;
      margin: 12px 0 10px;
    }
    .hero-title{
      margin:0;
      font-weight: 950;
      letter-spacing:.2px;
      color: var(--ink);
      display:flex;
      align-items:center;
      gap:10px;
      font-size: 1.15rem;
      line-height: 1.25;
    }
    .hero-sub{ margin:6px 0 0; color: var(--muted); font-size: .9rem; line-height: 1.35; }

    /* Main Card */
    .main-card{
      border: var(--border) !important;
      border-radius: 22px !important;
      box-shadow: var(--shadow) !important;
      overflow:hidden;
      background: var(--card);
      margin-bottom: 12px !important;
    }
    .main-card .card-header{
      background: #fff !important;
      border-bottom: var(--border) !important;
      padding: 14px 16px !important;
    }
    .main-card .code-to-copy{
      padding: 14px 16px !important;
    }

    /* Form */
    .form-floating>.form-control, .form-floating>.form-select{
      border-radius: 14px;
      border: 1px solid rgba(2,6,23,.12);
      box-shadow: none !important;
      min-height: 52px;
      font-size: 16px; /* evita zoom iOS */
    }
    .form-floating>.form-control:focus, .form-floating>.form-select:focus{
      border-color: rgba(19,53,123,.35);
      box-shadow: 0 0 0 4px rgba(19,53,123,.12) !important;
    }
    #formvotantes.row{
      --bs-gutter-x: .75rem;
      --bs-gutter-y: .75rem;
    }

    /* Section titles inside card */
    .section-chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 7px 11px;
      border-radius: 999px;
      background: rgba(19,53,123,.08);
      border: 1px solid rgba(19,53,123,.14);
      color: var(--brand);
      font-weight: 900;
      font-size: .78rem;
      max-width: 100%;
    }

    .section-block{
      border-top: 1px dashed rgba(2,6,23,.14);
      padding-top: 16px;
      margin-top: 16px;
    }
    .section-block h5{
      font-size: 1.05rem;
      line-height: 1.35;
      word-break: break-word;
    }

    /* Certificación UI */
    #btnIniciarCertificacion .btn{
      border-radius: 16px;
      font-weight: 950;
      padding: 12px 16px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 10px 26px rgba(34,197,94,.15);
      white-space: normal;
      line-height: 1.25;
    }
    #panelCertificacion .card{
      border-radius: 18px;
      border: var(--border);
      box-shadow: var(--shadow);
    }
    #panelCertificacion .card-body{ padding: 14px; }

    /* Sondeo: cards seleccionables (NO cambia backend) */
    .sondeo-opcion-btn{
      border-radius: 16px !important;
      border: 1px solid rgba(19,53,123,.20) !important;
      background: #fff !important;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
      box-shadow: 0 8px 18px rgba(2,6,23,.06);
      min-height: 56px;
      padding: 12px !important;
    }
    .sondeo-opcion-btn:hover{
      transform: translateY(-1px);
      box-shadow: var(--shadow);
      border-color: rgba(19,53,123,.35) !important;
    }
    .sondeo-opcion-btn.is-selected,
    .sondeo-opcion-btn.active{
      border-color: rgba(19,53,123,.55) !important;
      box-shadow: 0 0 0 4px rgba(19,53,123,.12), var(--shadow);
    }
    .sondeo-opcion-btn .sondeo-foto{
      width: 44px;
      height: 44px;
      object-fit: cover;
      flex-shrink: 0;
    }
    .sondeo-opcion-btn .sondeo-meta{
      min-width: 0;
      flex: 1;
    }
    .sondeo-opcion-btn .sondeo-meta strong,
    .sondeo-opcion-btn .sondeo-meta small{
      white-space: normal;
      overflow-wrap: anywhere;
      word-break: break-word;
    }

    /* Cuestionario: cards pro */
    .pregunta-card{
      border-radius: 18px !important;
      border: var(--border) !important;
      box-shadow: 0 8px 20px rgba(2,6,23,.06);
      overflow:hidden;
    }
    .pregunta-card .card-body{ padding: 14px; }
    .pregunta-card strong{
      word-break: break-word;
      overflow-wrap: anywhere;
    }
    .opciones-container{
      margin-left: 0 !important;
      padding-left: 0 !important;
    }

    /* Opciones: toda la tarjeta es clicable (móvil) */
    .opciones-container .form-check,
    .opciones-container label.opcion-tap{
      background: #fff;
      border: 1px solid rgba(2,6,23,.10);
      border-radius: 14px;
      padding: 14px 14px 14px 14px !important;
      margin-left: 0 !important;
      min-height: 52px;
      display: flex !important;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      user-select: none;
      -webkit-tap-highlight-color: rgba(19,53,123,.12);
      transition: box-shadow .15s ease, border-color .15s ease, background .15s ease;
    }
    .opciones-container .form-check:hover,
    .opciones-container label.opcion-tap:hover{
      box-shadow: 0 10px 24px rgba(2,6,23,.08);
      border-color: rgba(19,53,123,.28);
    }
    .opciones-container .form-check:has(.form-check-input:checked),
    .opciones-container label.opcion-tap:has(.form-check-input:checked){
      border-color: rgba(19,53,123,.55);
      background: rgba(19,53,123,.06);
      box-shadow: 0 0 0 3px rgba(19,53,123,.12);
    }
    .opciones-container .form-check-input{
      position: static !important;
      float: none !important;
      margin: 0 !important;
      width: 1.35rem !important;
      height: 1.35rem !important;
      flex-shrink: 0;
      pointer-events: none; /* el toque lo recibe el label entero */
    }
    .opciones-container .opcion-tap-text{
      font-weight: 700;
      color: var(--ink);
      font-size: .92rem;
      line-height: 1.35;
      word-break: break-word;
      overflow-wrap: anywhere;
      flex: 1;
      min-width: 0;
    }

    textarea.respuesta-texto{
      border-radius: 14px;
      border: 1px solid rgba(2,6,23,.12);
      min-height: 100px;
      font-size: 16px;
    }
    textarea.respuesta-texto:focus{
      border-color: rgba(19,53,123,.35);
      box-shadow: 0 0 0 4px rgba(19,53,123,.12);
    }

    /* ====== CTA BAR (Cancelar / Guardar) ====== */
    .cta-bar{
      margin-top: 16px;
      padding: 12px;
      border-radius: 18px;
      border: 1px solid rgba(2,6,23,.10);
      background: rgba(255,255,255,.96);
      backdrop-filter: blur(10px);
      box-shadow: 0 14px 40px rgba(2,6,23,.12);
      display:flex;
      align-items:stretch;
      justify-content:flex-end;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn-cta{
      border-radius: 16px !important;
      font-weight: 950 !important;
      padding: 10px 14px !important;
      display:inline-flex !important;
      align-items:center !important;
      gap: 10px !important;
      border: none !important;
      transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
      min-height: 52px;
    }
    .btn-cta:active{ transform: scale(.99); }

    .btn-cta-cancel{
      background: #fff !important;
      border: 1px solid rgba(2,6,23,.12) !important;
      color: #0f172a !important;
      box-shadow: 0 10px 22px rgba(2,6,23,.08);
    }
    .btn-cta-cancel:hover{
      box-shadow: 0 14px 30px rgba(2,6,23,.12);
    }

    .btn-cta-save{
      background: linear-gradient(135deg, var(--brand), var(--brand2)) !important;
      color: #fff !important;
      box-shadow: 0 14px 28px rgba(19,53,123,.22);
    }
    .btn-cta-save:hover{
      box-shadow: 0 18px 40px rgba(19,53,123,.28);
    }

    .btn-cta .cta-ico{
      width: 36px;
      height: 36px;
      border-radius: 12px;
      display:flex;
      align-items:center;
      justify-content:center;
      background: rgba(255,255,255,.14);
      border: 1px solid rgba(255,255,255,.18);
      flex-shrink: 0;
    }
    .btn-cta-cancel .cta-ico{
      background: rgba(15,23,42,.06);
      border-color: rgba(2,6,23,.08);
    }

    .cta-text{
      line-height: 1.1rem;
      text-align:left;
    }
    .cta-text b{ display:block; font-size: .92rem; }
    .cta-text small{ display:block; opacity:.85; font-weight:700; font-size: .72rem; }

    /* Overlay de guardado (audio puede tardar) */
    .ve-saving-overlay{
      position: fixed;
      inset: 0;
      z-index: 20000;
      background: rgba(15, 23, 42, .55);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      backdrop-filter: blur(3px);
    }
    .ve-saving-overlay.is-hidden{ display: none !important; }
    .ve-saving-card{
      width: 100%;
      max-width: 340px;
      background: #fff;
      border-radius: 18px;
      padding: 22px 20px;
      text-align: center;
      box-shadow: 0 22px 60px rgba(2,6,23,.28);
      border: 1px solid rgba(2,6,23,.08);
    }
    .ve-saving-spinner{
      width: 46px;
      height: 46px;
      margin: 0 auto 14px;
      border-radius: 50%;
      border: 4px solid rgba(19,53,123,.15);
      border-top-color: var(--brand);
      animation: ve-spin .8s linear infinite;
    }
    .ve-saving-card.is-success .ve-saving-spinner{
      display: none;
    }
    .ve-saving-check{
      display: none;
      width: 46px;
      height: 46px;
      margin: 0 auto 14px;
      border-radius: 50%;
      background: #16a34a;
      color: #fff;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
    }
    .ve-saving-card.is-success .ve-saving-check{
      display: flex;
    }
    .ve-saving-card strong{
      display: block;
      font-size: 1.05rem;
      color: var(--ink);
      margin-bottom: 6px;
    }
    .ve-saving-card p{
      margin: 0;
      color: var(--muted);
      font-size: .88rem;
      line-height: 1.35;
    }
    @keyframes ve-spin{
      to{ transform: rotate(360deg); }
    }
    body.ve-saving-lock{
      overflow: hidden;
    }
    body.ve-saving-lock .btn-cta{
      pointer-events: none;
      opacity: .7;
    }

    /* Tablet / móvil — contenedor a ancho completo */
    @media (max-width: 768px){
      :root{ --page-pad: 0; }
      .content{
        padding-left: 0 !important;
        padding-right: 0 !important;
      }
      .ve-page{
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
        padding-bottom: calc(78px + var(--safe-bottom));
      }
      .hero-card{
        border-radius: 0;
        padding: 14px 12px;
        margin: 0;
        border-left: none;
        border-right: none;
      }
      .hero-title{ font-size: 1.05rem; }
      .hero-sub{ font-size: .84rem; }
      .main-card{
        border-radius: 0 !important;
        margin: 0 !important;
        border-left: none !important;
        border-right: none !important;
        box-shadow: none !important;
      }
      .main-card .card-header{ padding: 12px !important; }
      .main-card .code-to-copy{ padding: 12px !important; }
      .section-block{ margin-top: 12px; padding-top: 12px; }
      .sondeo-opcion-btn{ text-align: left !important; }
      .cta-bar{
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1040;
        margin-top: 0;
        border-radius: 0;
        justify-content: stretch;
        flex-wrap: nowrap;
        padding-bottom: calc(10px + var(--safe-bottom));
      }
      .btn-cta{
        flex: 1 1 0;
        width: auto;
        justify-content: center;
        padding: 10px 8px !important;
      }
      .cta-text{ text-align:center; }
      .cta-text small{ display:none; }
      .btn-cta .cta-ico{ width: 32px; height: 32px; }
      #btnIniciarCertificacion .btn{ max-width: none; font-size: .95rem; }
      .alert{ font-size: .85rem; padding: .75rem .9rem; }
    }

    @media (max-width: 420px){
      .btn-cta .cta-ico{ display:none; }
      .hero-title i{ display:none; }
      .form-check{ padding: 11px; }
    }
  </style>


  <div id="veSavingOverlay" class="ve-saving-overlay is-hidden" aria-live="assertive" aria-busy="false">
    <div class="ve-saving-card" id="veSavingCard">
      <div class="ve-saving-spinner" aria-hidden="true"></div>
      <div class="ve-saving-check" aria-hidden="true"><i class="fas fa-check"></i></div>
      <strong id="veSavingTitle">Guardando...</strong>
      <p id="veSavingMsg">Por favor espera. Si el audio es largo, esto puede tardar unos segundos.</p>
    </div>
  </div>

  <div class="content">
    <div class="ve-page">

      <!-- HERO -->
      <div class="hero-card">
        <h4 class="hero-title">
          <i class="fas fa-user-check"></i>
          Ingreso de Encuestados
        </h4>
        <p class="hero-sub">Registra, certifica (audio + GPS) y guarda al encuestado.</p>
      </div>

      <div class="card main-card my-2" data-component-card="data-component-card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <span class="section-chip"><i class="fas fa-id-card"></i>Datos del encuestado</span>
            </div>
            <div class="text-muted small d-none d-sm-flex align-items-center gap-2">
              <i class="fas fa-shield-alt"></i>
              Registro seguro
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="code-to-copy">

           <form class="row g-3 mb-6" id="formvotantes" role="form" autocomplete="false">
    <input type="hidden" name="op" id="op" />
    <input type="hidden" name="idVotantes" id="idVotantes" />
    <input type="hidden" id="opcionActivaWeb" value="<?php echo ve_h($opcionActivaWeb); ?>">
    <input type="hidden" id="departamentoEncuestador" value="<?php echo ve_h($codigoDepartamentoEncuestador); ?>">
    <input type="hidden" id="municipioEncuestador" value="<?php echo ve_h($codigoMunicipioEncuestador); ?>">
    <input type="hidden" id="estado" value="activo">
    <input type="hidden" id="nombre_completo" name="nombre_completo" value="Encuestado">

    <div class="col-sm-12 section-block">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="text-muted small"><i class="fas fa-microphone me-1"></i>Audio + <i class="fas fa-map-marker-alt me-1"></i>GPS</span>
        </div>

        <div id="alertaAudioNoSoportado" class="alert alert-warning d-none">
            <i class="fas fa-exclamation-triangle me-2"></i>Tu navegador no soporta grabación de audio.
        </div>
        <div id="alertaGeoNoSoportado" class="alert alert-warning d-none">
            <i class="fas fa-exclamation-triangle me-2"></i>Tu navegador no soporta geolocalización.
        </div>

        <div id="btnIniciarCertificacion" class="text-center">
            <button type="button" class="btn btn-success btn-lg">
                <i class="fas fa-play-circle me-2"></i>Iniciar Certificación (Audio + GPS)
            </button>
            <p class="text-muted small mt-2 mb-0">Se grabará audio y ubicación GPS para certificar este registro</p>
        </div>

        <div id="panelCertificacion" class="d-none mt-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-3 fs-4"></i>
                                <div>
                                    <strong class="d-block">Ubicación GPS</strong>
                                    <small id="estatusUbicacion" class="text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Esperando...</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-microphone text-danger me-3 fs-4"></i>
                                <div>
                                    <strong class="d-block">Grabación de Audio</strong>
                                    <small id="estatusAudio" class="text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Esperando...</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-none" id="audioPreviewContainer">
                            <div class="alert alert-info mb-0">
                                <strong class="d-block mb-2">Vista previa:</strong>
                                <audio id="audioPreview" controls class="w-100"></audio>
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <button type="button" id="btnDetenerCertificacion" class="btn btn-danger" disabled>
                                <i class="fas fa-stop-circle me-2"></i>Detener Grabación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control" value="ENCUESTADO" readonly>
            <label>Encuestado</label>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-floating">
            <select class="form-select" id="tbl_departamento_id" name="tbl_departamento_id">
                <?php echo $optionDep; ?>
            </select>
            <label for="tbl_departamento_id">Departamento <span class="text-danger">*</span></label>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-floating">
            <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"></select>
            <label for="tbl_municipio_id">Municipio <span class="text-danger">*</span></label>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control" id="comuna" name="comuna">
            <label for="comuna">Comuna</label>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control" id="barrio" name="barrio">
            <label for="barrio">Barrio</label>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="form-floating">
            <select class="form-select" id="ideologia" name="ideologia" required>
              <option value="" selected disabled>Seleccione la tendencia ideológica política</option>
                <option value="izquierda">Izquierda</option>
                <option value="centro_izquierda">Centro izquierda</option>
                <option value="centro">Centro</option>
                <option value="centro_derecha">Centro derecha</option>
                <option value="derecha">Derecha</option>
                <option value="sin_definir">Sin definir</option>
            </select>
            <label for="ideologia">Ideología política<span class="text-danger">*</span></label>
        </div>
    </div>

    <div class="col-sm-12 col-md-3">
        <div class="form-floating">
            <select class="form-select" id="rango_edad" name="rango_edad" required>
              <option value="" selected disabled>Seleccione el grupo etario</option>
                <option value="18-25">18-25</option>
                <option value="26-35">26-35</option>
                <option value="36-45">36-45</option>
                <option value="46-55">46-55</option>
                <option value="56-65">56-65</option>
                <option value="66+">66+</option>
            </select>
            <label for="rango_edad">Rango de edad<span class="text-danger">*</span></label>
        </div>
    </div>

    <div class="col-sm-12 col-md-3">
        <div class="form-floating">
            <select class="form-select" id="nivel_ingresos" name="nivel_ingresos" required>
              <option value="" selected disabled>Seleccione el nivel de ingresos</option>
                <option value="menos_1_salario">Menos de 1 salario</option>
                <option value="1-2_salarios">1-2 salarios</option>
                <option value="3-5_salarios">3-5 salarios</option>
                <option value="6-10_salarios">6-10 salarios</option>
                <option value="mas_10_salarios">Más de 10 salarios</option>
            </select>
            <label for="nivel_ingresos">Nivel socioeconómico<span class="text-danger">*</span></label>
        </div>
    </div>

    <div class="col-sm-12 col-md-3">
        <div class="form-floating">
            <select class="form-select" id="genero" name="genero" required>
              <option value="" selected disabled>Seleccione la identidad de género</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="otro">Otro</option>
                <option value="prefiero_no_decir">Prefiero no decir</option>
            </select>
            <label for="genero">Género<span class="text-danger">*</span></label>
        </div>
    </div>

    <div class="col-sm-12 col-md-3">
        <div class="form-floating">
            <select class="form-select" id="nivel_educacion" name="nivel_educacion">
              <option value="" selected disabled>Seleccione el máximo nivel educativo alcanzado</option>
                <option value="primaria_incompleta">Primaria incompleta</option>
                <option value="primaria_completa">Primaria completa</option>
                <option value="secundaria_incompleta">Secundaria incompleta</option>
                <option value="secundaria_completa">Secundaria completa</option>
                <option value="tecnico">Técnico</option>
                <option value="tecnologo">Tecnólogo</option>
                <option value="universitario_incompleto">Universitario incompleto</option>
                <option value="universitario_completo">Universitario completo</option>
                <option value="posgrado">Posgrado</option>
            </select>
            <label for="nivel_educacion">Nivel educativo</label>
        </div>
    </div>

    <div class="col-sm-12 col-md-3">
        <div class="form-floating">
            <select class="form-select" id="ocupacion" name="ocupacion" required>
              <option value="" selected disabled>Seleccione la ocupación</option>
                        
                <option value="Empleado">Empleado</option>
                <option value="Auto Empleado">Auto Empleado</option>
                <option value="Empresario">Empresario</option>
                <option value="Comerciante">Comerciante</option>
                <option value="Independiente">Independiente</option>
            </select>
            <label for="ocupacion">Ocupación<span class="text-danger">*</span></label>
        </div>
    </div>

    
</form>

            <!-- Sondeo Activo -->
            <?php if ($sondeoActivo): ?>
              <div class="section-block">
                <div class="text-center mb-3">
                  <span class="section-chip"><i class="fas fa-poll"></i>Sondeo activo</span>
                  <h5 class="mt-2 mb-1" style="font-weight:950;color:var(--ink);">
                    <?php echo ve_h($sondeoActivo['sondeo'] ?? ''); ?>
                  </h5>
                  <?php if (!empty($sondeoActivo['descripcion_sondeo'])): ?>
                    <p class="text-muted small mb-0"><?php echo ve_h($sondeoActivo['descripcion_sondeo']); ?></p>
                  <?php endif; ?>
                </div>

                <input type="hidden" id="sondeoActivoId" value="<?php echo ve_h($sondeoActivo['id'] ?? ''); ?>">
                <input type="hidden" id="sondeoActivoTipo" value="<?php echo ve_h($sondeoActivo['aplica_cargos_publicos'] ?? ''); ?>">
                <input type="hidden" id="sondeoRespuestaCandidato" value="">
                <input type="hidden" id="sondeoRespuestaOpcion" value="">

                <?php if (strtolower($sondeoActivo['aplica_cargos_publicos'] ?? '') === 'si' && !empty($sondeoActivo['candidatos'])): ?>
                  <div class="row g-2 g-md-3" id="sondeoOpcionesContainer">
                    <?php
                    $contador = 0;
                    foreach ($sondeoActivo['candidatos'] as $candidato):
                      $contador++;
                      $candidatoId = ve_h($candidato['id']);
                      $candidatoNombre = ve_h($candidato['nombre_completo']);
                      $candidatoCargo = ve_h($candidato['cargo_publico'] ?? '');
                      $candidatoPartidos = ve_h($candidato['nombres_partidos'] ?? '');
                      $candidatoFoto = ve_h("assets/img/admin/" . ($candidato['foto'] ?? 'admin/assets/img/team/avatar.png'));
                    ?>
                      <div class="col-12 col-md-6">
                        <button type="button"
                          class="btn btn-outline-primary w-100 text-start sondeo-opcion-btn"
                          data-tipo="candidato"
                          data-valor="<?php echo $candidatoId; ?>"
                          data-candidato-id="<?php echo $candidatoId; ?>"
                          onclick="VOTANTES.seleccionarOpcionSondeo(this)">
                          <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary flex-shrink-0" style="min-width:32px;"><?php echo $contador; ?></span>
                            <img src="<?php echo $candidatoFoto; ?>" alt="<?php echo $candidatoNombre; ?>"
                              class="rounded-circle sondeo-foto">
                            <div class="sondeo-meta">
                              <strong class="d-block"><?php echo $candidatoNombre; ?></strong>
                              <small class="text-muted d-block">
                                <?php echo $candidatoCargo; ?>
                                <?php if (!empty($candidatoPartidos)) echo " - " . $candidatoPartidos; ?>
                              </small>
                            </div>
                            <i class="fas fa-check-circle text-success flex-shrink-0" style="display:none;font-size:1.15rem;"></i>
                          </div>
                        </button>
                      </div>
                    <?php endforeach; ?>
                  </div>

                <?php elseif (!empty($sondeoActivo['opciones'])): ?>
                  <div class="row g-2 g-md-3" id="sondeoOpcionesContainer">
                    <?php
                    $contador = 0;
                    foreach ($sondeoActivo['opciones'] as $opcion):
                      $contador++;
                      $opcionId = ve_h($opcion['id']);
                      $opcionTextoRaw = (string)($opcion['opcion'] ?? '');
                      $opcionTexto = ve_h(function_exists('mb_strtoupper') ? mb_strtoupper($opcionTextoRaw, 'UTF-8') : strtoupper($opcionTextoRaw));
                    ?>
                      <div class="col-12 col-md-6">
                        <button type="button"
                          class="btn btn-outline-primary w-100 text-start sondeo-opcion-btn"
                          data-tipo="opcion"
                          data-valor="<?php echo $opcionId; ?>"
                          onclick="VOTANTES.seleccionarOpcionSondeo(this)">
                          <div class="d-flex align-items-center w-100 gap-2">
                            <span class="badge bg-primary flex-shrink-0" style="min-width:32px;"><?php echo $contador; ?></span>
                            <strong class="flex-grow-1" style="font-size:.92rem;line-height:1.3;"><?php echo $opcionTexto; ?></strong>
                            <i class="fas fa-check-circle text-success flex-shrink-0" style="display:none;font-size:1.2rem;"></i>
                          </div>
                        </button>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <div class="alert alert-info mt-3 mb-0 d-flex align-items-start">
                  <i class="fas fa-info-circle me-2 mt-1"></i>
                  <small class="mb-0"><strong>Obligatorio:</strong> Esta respuesta se guardará automáticamente al registrar el votante</small>
                </div>
              </div>
            <?php endif; ?>

            <!-- Cuestionario Activo -->
            <?php if ($cuestionarioActivo && count($preguntasCuestionario) > 0): ?>
              <div class="section-block">
                <div class="text-center mb-3">
                  <span class="section-chip"><i class="fas fa-clipboard-list"></i>Cuestionario activo</span>
                  <h5 class="mt-2 mb-1" style="font-weight:950;color:var(--ink);">
                    <?php echo ve_h($cuestionarioActivo['tema'] ?? ''); ?>
                  </h5>
                  <?php if (!empty($cuestionarioActivo['realizada_por_o_encomendada_por'])): ?>
                    <p class="text-muted small mb-0">
                      Realizada por: <?php echo ve_h($cuestionarioActivo['realizada_por_o_encomendada_por']); ?>
                    </p>
                  <?php endif; ?>
                </div>

                <input type="hidden" id="cuestionarioActivoId" value="<?php echo ve_h($cuestionarioActivo['id'] ?? ''); ?>">

                <div id="preguntas_container">
                  <?php foreach ($preguntasCuestionario as $index => $pregunta): ?>
                    <div class="card mb-2 mb-md-3 pregunta-card" data-pregunta-id="<?php echo ve_h($pregunta['id']); ?>">
                      <div class="card-body">
                        <div class="d-flex align-items-start mb-2 mb-md-3 gap-2">
                          <span class="badge bg-primary flex-shrink-0" style="min-width:32px;font-size:.95rem;"><?php echo $index + 1; ?></span>
                          <div class="flex-grow-1" style="min-width:0;">
                            <strong class="d-block" style="font-size:.94rem;">
                              <?php echo ve_h($pregunta['texto_pregunta'] ?? ''); ?>
                            </strong>
                          </div>
                        </div>

                        <div class="opciones-container">
                          <?php
                          $tipoPreguntaOriginal = $pregunta['tipo_pregunta'];
                          $inputType = 'radio';
                          if ($tipoPreguntaOriginal === 'Seleccion_Multiple_multiple_respuesta') $inputType = 'checkbox';

                          if (empty($pregunta['opciones']) || !is_array($pregunta['opciones'])):
                            if (in_array($tipoPreguntaOriginal, ['Dicotomica','Preguntas_Ordinales','Preguntas_Cardinales','Seleccion_Multiple_unica_respuesta','Seleccion_Multiple_multiple_respuesta'])):
                          ?>
                              <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>Esta pregunta no tiene opciones configuradas.
                              </div>
                          <?php else: ?>
                              <textarea class="form-control respuesta-texto"
                                name="respuesta_texto_<?php echo ve_h($pregunta['id']); ?>"
                                placeholder="Escribe tu respuesta aquí..."
                                data-pregunta-id="<?php echo ve_h($pregunta['id']); ?>"></textarea>
                          <?php endif; else:
                            foreach ($pregunta['opciones'] as $opcion):
                              $opcionIdAttr = ve_h($opcion['id']);
                              $preguntaIdAttr = ve_h($pregunta['id']);
                          ?>
                              <label class="form-check opcion-tap mb-2">
                                <input class="form-check-input"
                                  type="<?php echo ve_h($inputType); ?>"
                                  name="respuesta_<?php echo $preguntaIdAttr; ?><?php echo $inputType === 'checkbox' ? '[]' : ''; ?>"
                                  id="opcion_<?php echo $opcionIdAttr; ?>"
                                  value="<?php echo $opcionIdAttr; ?>"
                                  data-pregunta-id="<?php echo $preguntaIdAttr; ?>">
                                <span class="opcion-tap-text"><?php echo ve_h($opcion['texto'] ?? ''); ?></span>
                              </label>
                          <?php endforeach; endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="alert alert-info mt-3 mb-0 d-flex align-items-start">
                  <i class="fas fa-info-circle me-2 mt-1"></i>
                  <small class="mb-0"><strong>Obligatorio:</strong> Todas las respuestas se guardarán automáticamente al registrar el votante</small>
                </div>
              </div>
            <?php endif; ?>

            <div class="cta-bar">
                <button type="button" onclick="VOTANTES.emptyCells();" class="btn btn-cta btn-cta-cancel" id="btnCancelarVotante">
                    <span class="cta-ico"><i class="fas fa-times"></i></span>
                    <span class="cta-text">
                        <b>Cancelar</b>
                        <small>Limpiar formulario</small>
                    </span>
                </button>

                <button type="button" id="btnGuardarVotante" onclick="VOTANTES.validateData();" class="btn btn-cta btn-cta-save">
                    <span class="cta-ico"><i class="fas fa-save"></i></span>
                    <span class="cta-text">
                        <b>Guardar</b>
                        <small>Registrar encuestado</small>
                    </span>
                </button>
            </div>


          </div>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>

    </div>
  </div>

  <?php include 'admin/include/gerenic_script.php'; ?>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
  <script src="admin/js/departamentoDama.js"></script>
  <script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>
  <script type="text/javascript" src="admin/js/certificacion_encuestador.js"></script>
  <script type="text/javascript" src="admin/js/votantes_encuestador.js"></script>
  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>
  <script src="vendors/flatpickr/flatpickr.min.js"></script>

  <script>
    // ✅ NO TOCA BACKEND: solo marca visualmente seleccionado (sin romper tu JS)
    (function(){
      $(document).on('click', '.sondeo-opcion-btn', function(){
        $('.sondeo-opcion-btn').removeClass('is-selected');
        $(this).addClass('is-selected');
      });
    })();

    const departamentoInicial = ($("#departamentoEncuestador").val() || $("#departamentoConfiguracionInput").val() || "").toString();
    const municipioInicial = ($("#municipioEncuestador").val() || "").toString();

    if (departamentoInicial !== "") {
      $("#tbl_departamento_id").val(departamentoInicial);
    }

    if ($("#tbl_departamento_id").val()) {
      DEPARTAMENTO.getMunicipios();

      if (municipioInicial !== "") {
        setTimeout(() => {
          const $municipio = $("#tbl_municipio_id");
          if ($municipio.find("option[value='" + municipioInicial + "']").length > 0) {
            $municipio.val(municipioInicial);
          }
        }, 350);
      }
    }
  </script>
</body>
</html>
