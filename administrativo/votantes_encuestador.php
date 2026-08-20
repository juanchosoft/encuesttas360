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
    $codigoDepartamento = htmlspecialchars((string)($dep['codigo_departamento'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nombreDepartamento = htmlspecialchars((string)($dep['departamento'] ?? ''), ENT_QUOTES, 'UTF-8');
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
    }

    .content{ background: var(--bg); }
    .page-shell{ padding-bottom: 30px; }

    /* Header Card */
    .hero-card{
      background: linear-gradient(135deg, rgba(19,53,123,.10), rgba(255,255,255,0));
      border: var(--border);
      border-radius: 26px;
      box-shadow: var(--shadow);
      padding: 18px;
      margin: 18px 0 14px;
    }
    .hero-title{
      margin:0;
      font-weight: 950;
      letter-spacing:.2px;
      color: var(--ink);
      display:flex;
      align-items:center;
      gap:10px;
    }
    .hero-sub{ margin:6px 0 0; color: var(--muted); }

    /* Main Card */
    .main-card{
      border: var(--border) !important;
      border-radius: 26px !important;
      box-shadow: var(--shadow) !important;
      overflow:hidden;
      background: var(--card);
    }
    .main-card .card-header{
      background: #fff !important;
      border-bottom: var(--border) !important;
    }

    /* Form */
    .form-floating>.form-control, .form-floating>.form-select{
      border-radius: 16px;
      border: 1px solid rgba(2,6,23,.12);
      box-shadow: none !important;
    }
    .form-floating>.form-control:focus, .form-floating>.form-select:focus{
      border-color: rgba(19,53,123,.35);
      box-shadow: 0 0 0 4px rgba(19,53,123,.12) !important;
    }

    /* Section titles inside card */
    .section-chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(19,53,123,.08);
      border: 1px solid rgba(19,53,123,.14);
      color: var(--brand);
      font-weight: 900;
      font-size: .82rem;
    }

    .section-block{
      border-top: 1px dashed rgba(2,6,23,.14);
      padding-top: 18px;
      margin-top: 18px;
    }

    /* Certificación UI */
    #btnIniciarCertificacion .btn{
      border-radius: 16px;
      font-weight: 950;
      padding: 12px 18px;
      box-shadow: 0 10px 26px rgba(34,197,94,.15);
    }
    #panelCertificacion .card{
      border-radius: 22px;
      border: var(--border);
      box-shadow: var(--shadow);
    }

    /* Sondeo: cards seleccionables (NO cambia backend) */
    .sondeo-opcion-btn{
      border-radius: 18px !important;
      border: 1px solid rgba(19,53,123,.20) !important;
      background: #fff !important;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
      box-shadow: 0 10px 22px rgba(2,6,23,.06);
    }
    .sondeo-opcion-btn:hover{
      transform: translateY(-1px);
      box-shadow: var(--shadow);
      border-color: rgba(19,53,123,.35) !important;
    }
    .sondeo-opcion-btn.is-selected{
      border-color: rgba(19,53,123,.55) !important;
      box-shadow: 0 0 0 4px rgba(19,53,123,.12), var(--shadow);
    }

    /* Cuestionario: cards pro */
    .pregunta-card{
      border-radius: 22px !important;
      border: var(--border) !important;
      box-shadow: 0 10px 24px rgba(2,6,23,.06);
      overflow:hidden;
    }
    .pregunta-card .card-body{ padding: 16px; }
    .opciones-container{
      margin-left: 0 !important;
      padding-left: 0 !important;
    }

    /* Opciones tipo pill */
    .form-check{
      background: #fff;
      border: 1px solid rgba(2,6,23,.10);
      border-radius: 16px;
      padding: 10px 12px;
      display:flex;
      gap:10px;
      align-items:flex-start;
      transition: box-shadow .15s ease, border-color .15s ease, transform .15s ease;
    }
    .form-check:hover{
      transform: translateY(-1px);
      box-shadow: 0 14px 30px rgba(2,6,23,.08);
      border-color: rgba(19,53,123,.25);
    }
    .form-check-input{ margin-top: .25rem; }
    .form-check-label{ font-weight: 700; color: var(--ink); }

    textarea.respuesta-texto{
      border-radius: 18px;
      border: 1px solid rgba(2,6,23,.12);
      min-height: 110px;
    }
    textarea.respuesta-texto:focus{
      border-color: rgba(19,53,123,.35);
      box-shadow: 0 0 0 4px rgba(19,53,123,.12);
    }

    /* Better spacing on mobile */
    @media (max-width: 576px){
      .hero-card{ padding: 14px; }
      .main-card .card-body{ padding: 12px !important; }
      .btn-primary, .btn-phoenix-secondary{ width: 100%; }
      .justify-end{ justify-content: stretch !important; }
    }
  </style>
  <style>
  /* ====== CTA BAR (Cancelar / Guardar) ====== */
  .cta-bar{
    margin-top: 16px;
    padding: 14px;
    border-radius: 22px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(10px);
    box-shadow: 0 18px 60px rgba(2,6,23,.12);
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap: 12px;
  }

  .btn-cta{
    border-radius: 18px !important;
    font-weight: 950 !important;
    padding: 12px 18px !important;
    display:inline-flex !important;
    align-items:center !important;
    gap: 10px !important;
    border: none !important;
    transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
  }
  .btn-cta:active{ transform: scale(.99); }

  .btn-cta-cancel{
    background: #fff !important;
    border: 1px solid rgba(2,6,23,.12) !important;
    color: #0f172a !important;
    box-shadow: 0 12px 26px rgba(2,6,23,.08);
  }
  .btn-cta-cancel:hover{
    box-shadow: 0 18px 40px rgba(2,6,23,.12);
  }

  .btn-cta-save{
    background: linear-gradient(135deg, var(--brand), var(--brand2)) !important;
    color: #fff !important;
    box-shadow: 0 16px 34px rgba(19,53,123,.22);
  }
  .btn-cta-save:hover{
    box-shadow: 0 22px 56px rgba(19,53,123,.28);
  }

  .btn-cta .cta-ico{
    width: 42px;
    height: 42px;
    border-radius: 16px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.18);
  }
  .btn-cta-cancel .cta-ico{
    background: rgba(15,23,42,.06);
    border-color: rgba(2,6,23,.08);
  }

  .cta-text{
    line-height: 1.05rem;
    text-align:left;
  }
  .cta-text b{ display:block; font-size: .95rem; }
  .cta-text small{ display:block; opacity:.85; font-weight:700; }

  /* Mobile: barra fija para UX */
  @media (max-width: 576px){
    .cta-bar{
      position: sticky;
      bottom: 10px;
      z-index: 20;
      justify-content: stretch;
    }
    .btn-cta{
      width: 100%;
      justify-content:center;
    }
    .cta-text{ text-align:center; }
  }
</style>


  <div class="content">
    <div class="col-11 col-xl-11 mx-auto">

      <!-- HERO -->
      <div class="hero-card">
        <h4 class="hero-title">
          <i class="fas fa-user-check"></i>
          Ingreso de Encuestados
        </h4>
        <p class="hero-sub">Registra el encuestado, certifica (audio + GPS) y guarda. Diseño optimizado para celular, tablet y PC ✅</p>
      </div>

      <div class="card main-card my-3" data-component-card="data-component-card">
        <div class="card-header p-4">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <span class="section-chip"><i class="fas fa-id-card"></i>Datos del encuestado</span>
            </div>
            <div class="text-muted small d-flex align-items-center gap-2">
              <i class="fas fa-shield-alt"></i>
              Registro seguro
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="p-4 code-to-copy">

           <form class="row g-3 mb-6" id="formvotantes" role="form" autocomplete="false">
    <input type="hidden" name="op" id="op" />
    <input type="hidden" name="idVotantes" id="idVotantes" />
    <input type="hidden" id="opcionActivaWeb" value="<?php echo htmlspecialchars($opcionActivaWeb); ?>">
    <input type="hidden" id="departamentoEncuestador" value="<?php echo htmlspecialchars($codigoDepartamentoEncuestador); ?>">
    <input type="hidden" id="municipioEncuestador" value="<?php echo htmlspecialchars($codigoMunicipioEncuestador); ?>">
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
                    <?php echo htmlspecialchars($sondeoActivo['sondeo'] ?? ''); ?>
                  </h5>
                  <?php if (!empty($sondeoActivo['descripcion_sondeo'])): ?>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($sondeoActivo['descripcion_sondeo']); ?></p>
                  <?php endif; ?>
                </div>

                <input type="hidden" id="sondeoActivoId" value="<?php echo htmlspecialchars($sondeoActivo['id'] ?? ''); ?>">
                <input type="hidden" id="sondeoActivoTipo" value="<?php echo htmlspecialchars($sondeoActivo['aplica_cargos_publicos'] ?? ''); ?>">
                <input type="hidden" id="sondeoRespuestaCandidato" value="">
                <input type="hidden" id="sondeoRespuestaOpcion" value="">

                <?php if (strtolower($sondeoActivo['aplica_cargos_publicos'] ?? '') === 'si' && !empty($sondeoActivo['candidatos'])): ?>
                  <div class="row g-3" id="sondeoOpcionesContainer">
                    <?php
                    $contador = 0;
                    foreach ($sondeoActivo['candidatos'] as $candidato):
                      $contador++;
                      $candidatoId = htmlspecialchars($candidato['id']);
                      $candidatoNombre = htmlspecialchars($candidato['nombre_completo']);
                      $candidatoCargo = htmlspecialchars($candidato['cargo_publico'] ?? '');
                      $candidatoPartidos = htmlspecialchars($candidato['nombres_partidos'] ?? '');
                      $candidatoFoto = htmlspecialchars("assets/img/admin/" . ($candidato['foto'] ?? 'admin/assets/img/team/avatar.png'));
                    ?>
                      <div class="col-12 col-md-6 col-xl-6">
                        <button type="button"
                          class="btn btn-outline-primary w-100 text-start py-3 sondeo-opcion-btn"
                          data-tipo="candidato"
                          data-valor="<?php echo $candidatoId; ?>"
                          data-candidato-id="<?php echo $candidatoId; ?>"
                          onclick="VOTANTES.seleccionarOpcionSondeo(this)">
                          <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-3" style="min-width:38px;"><?php echo $contador; ?></span>
                            <img src="<?php echo $candidatoFoto; ?>" alt="<?php echo $candidatoNombre; ?>"
                              class="rounded-circle me-3" style="width:54px;height:54px;object-fit:cover;">
                            <div class="flex-grow-1" style="min-width:0;">
                              <strong class="d-block text-truncate"><?php echo $candidatoNombre; ?></strong>
                              <small class="text-muted text-truncate d-block">
                                <?php echo $candidatoCargo; ?>
                                <?php if (!empty($candidatoPartidos)) echo " - " . $candidatoPartidos; ?>
                              </small>
                            </div>
                            <i class="fas fa-check-circle text-success ms-2" style="display:none;font-size:1.25rem;"></i>
                          </div>
                        </button>
                      </div>
                    <?php endforeach; ?>
                  </div>

                <?php elseif (!empty($sondeoActivo['opciones'])): ?>
                  <div class="row g-3" id="sondeoOpcionesContainer">
                    <?php
                    $contador = 0;
                    foreach ($sondeoActivo['opciones'] as $opcion):
                      $contador++;
                      $opcionId = htmlspecialchars($opcion['id']);
                      $opcionTexto = htmlspecialchars($opcion['opcion']);
                    ?>
                      <div class="col-12 col-md-6">
                        <button type="button"
                          class="btn btn-outline-primary w-100 text-center py-4 sondeo-opcion-btn"
                          data-tipo="opcion"
                          data-valor="<?php echo $opcionId; ?>"
                          onclick="VOTANTES.seleccionarOpcionSondeo(this)">
                          <div class="d-flex align-items-center justify-content-center w-100">
                            <span class="badge bg-primary me-3" style="min-width:40px;"><?php echo $contador; ?></span>
                            <strong style="font-size: .95rem;"><?php echo strtoupper($opcionTexto); ?></strong>
                            <i class="fas fa-check-circle text-success ms-auto" style="display:none;font-size:1.35rem;"></i>
                          </div>
                        </button>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <div class="alert alert-info mt-4 mb-0 d-flex align-items-center">
                  <i class="fas fa-info-circle me-2"></i>
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
                    <?php echo htmlspecialchars($cuestionarioActivo['tema'] ?? ''); ?>
                  </h5>
                  <?php if (!empty($cuestionarioActivo['realizada_por_o_encomendada_por'])): ?>
                    <p class="text-muted small mb-0">
                      Realizada por: <?php echo htmlspecialchars($cuestionarioActivo['realizada_por_o_encomendada_por']); ?>
                    </p>
                  <?php endif; ?>
                </div>

                <input type="hidden" id="cuestionarioActivoId" value="<?php echo htmlspecialchars($cuestionarioActivo['id'] ?? ''); ?>">

                <div id="preguntas_container">
                  <?php foreach ($preguntasCuestionario as $index => $pregunta): ?>
                    <div class="card mb-3 pregunta-card" data-pregunta-id="<?php echo $pregunta['id']; ?>">
                      <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                          <span class="badge bg-primary me-3" style="min-width:38px;font-size:1rem;"><?php echo $index + 1; ?></span>
                          <div class="flex-grow-1">
                            <strong class="d-block" style="font-size:.96rem;">
                              <?php echo htmlspecialchars($pregunta['texto_pregunta']); ?>
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
                                name="respuesta_texto_<?php echo $pregunta['id']; ?>"
                                placeholder="Escribe tu respuesta aquí..."
                                data-pregunta-id="<?php echo $pregunta['id']; ?>"></textarea>
                          <?php endif; else:
                            foreach ($pregunta['opciones'] as $opcion):
                          ?>
                              <div class="form-check mb-2">
                                <input class="form-check-input"
                                  type="<?php echo $inputType; ?>"
                                  name="respuesta_<?php echo $pregunta['id']; ?><?php echo $inputType === 'checkbox' ? '[]' : ''; ?>"
                                  id="opcion_<?php echo $opcion['id']; ?>"
                                  value="<?php echo $opcion['id']; ?>"
                                  data-pregunta-id="<?php echo $pregunta['id']; ?>">
                                <label class="form-check-label" for="opcion_<?php echo $opcion['id']; ?>">
                                  <?php echo htmlspecialchars($opcion['texto']); ?>
                                </label>
                              </div>
                          <?php endforeach; endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="alert alert-info mt-4 mb-0 d-flex align-items-center">
                  <i class="fas fa-info-circle me-2"></i>
                  <small class="mb-0"><strong>Obligatorio:</strong> Todas las respuestas se guardarán automáticamente al registrar el votante</small>
                </div>
              </div>
            <?php endif; ?>

            <div class="col-12 d-flex justify-content-center gap-3 mt-4">
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

      <div class="p-4">
        <div class="table-responsive">
          <!-- tu tabla va aquí -->
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
