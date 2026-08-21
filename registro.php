<?php
/**
 * Fase B — Datos del participante (post-respuesta). Sin cuenta / sin login.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require './admin/include/generic_classes.php';
require_once './admin/classes/Departamento.php';
require_once './admin/classes/ParticipacionPublica.php';

$token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
$draft = ParticipacionPublica::getDraft($token !== '' ? $token : null);

if (!$draft) {
  header('Location: index.php');
  exit;
}

$token = $draft['token'];
$geo = ParticipacionPublica::getGeoFromSession();

$departamentos = Departamento::getAll(null);
$departamentosResponse = $departamentos['output']['response'] ?? [];
$optionDep = '';
foreach ($departamentosResponse as $dep) {
  $codigo = htmlspecialchars($dep['codigo_departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $nombre = htmlspecialchars($dep['departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $sel = (($geo['codigo_departamento'] ?? '') !== '' && (string)$geo['codigo_departamento'] === (string)$dep['codigo_departamento']) ? ' selected' : '';
  $optionDep .= "<option value='{$codigo}'{$sel}>{$codigo} - {$nombre}</option>";
}

$logo360 = 'assets/img/360 Estadisticas-04.png';
$tipoLabel = ($draft['tipo'] ?? '') === 'sondeo' ? 'sondeo' : 'encuesta';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tus datos | 360 Estadísticas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900;950&display=swap" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./css/registro.css?v=<?= time(); ?>">
</head>
<body>
<?php include './admin/include/loading.php'; ?>
<?php include './admin/include/menu_registro.php'; ?>

<div class="registro-page">
  <div class="registro-container">
    <div class="registro-card">

      <div class="registro-hero">
        <div class="hero-content">
          <div class="hero-logo">
            <img src="<?= htmlspecialchars($logo360) ?>" alt="360 Estadísticas">
          </div>
          <div>
            <span class="hero-kicker">
              <i class="fa-solid fa-user-check"></i>
              Último paso
            </span>
            <h1 class="hero-title">Completa tus datos para guardar</h1>
            <p class="hero-subtitle">
              Ya respondiste el <?= htmlspecialchars($tipoLabel) ?>.
              Estos datos se usan solo para estadísticas agregadas. No crearás una cuenta.
            </p>
          </div>
        </div>
      </div>

      <form id="formParticipacionDatos" class="m-0" autocomplete="off">
        <input type="hidden" id="participacion_token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" id="device_token" name="device_token" value="">
        <input type="hidden" id="device_fingerprint" name="device_fingerprint" value="">
        <input type="hidden" id="device_user_agent" name="device_user_agent" value="">
        <input type="hidden" id="device_platform" name="device_platform" value="">
        <input type="hidden" id="device_language" name="device_language" value="">
        <input type="hidden" id="device_timezone" name="device_timezone" value="">
        <input type="hidden" id="estado" name="estado" value="activo">

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-location-dot"></i> Ubicación</h3>
            <small>Clasificación estadística</small>
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Departamento <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-map input-ic"></i>
                <select id="tbl_departamento_id" name="tbl_departamento_id" class="form-select" required>
                  <option value="">Selecciona tu departamento</option>
                  <?= $optionDep ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Municipio <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-location-crosshairs input-ic"></i>
                <select id="tbl_municipio_id" name="tbl_municipio_id" class="form-select" required>
                  <option value="">Primero elige un departamento</option>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Barrio <span style="color:#64748b;font-weight:850;">(Opcional)</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-house input-ic"></i>
                <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Ej: La Esperanza">
              </div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-chart-pie"></i> Perfil estadístico</h3>
            <small>Obligatorio</small>
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Ideología política <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-scale-balanced input-ic"></i>
                <select class="form-select" id="ideologia" name="ideologia" required>
                  <option value="">Selecciona una opción</option>
                  <option value="izquierda">Izquierda</option>
                  <option value="centro_izquierda">Centro izquierda</option>
                  <option value="centro">Centro</option>
                  <option value="centro_derecha">Centro derecha</option>
                  <option value="derecha">Derecha</option>
                  <option value="sin_definir">Sin definir</option>
                  <option value="prefiero_no_decir">Prefiero no decir</option>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Rango de edad <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-hourglass-half input-ic"></i>
                <select class="form-select" id="rango_edad" name="rango_edad" required>
                  <option value="">Selecciona tu grupo</option>
                  <option value="18-25">18-25</option>
                  <option value="26-35">26-35</option>
                  <option value="36-45">36-45</option>
                  <option value="46-55">46-55</option>
                  <option value="56-65">56-65</option>
                  <option value="66+">66+</option>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Género <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-venus-mars input-ic"></i>
                <select class="form-select" id="genero" name="genero" required>
                  <option value="">Selecciona una opción</option>
                  <option value="femenino">Femenino</option>
                  <option value="masculino">Masculino</option>
                  <option value="no_binario">No binario</option>
                  <option value="otro">Otro</option>
                  <option value="prefiero_no_decir">Prefiero no decir</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-briefcase"></i> Educación y ocupación</h3>
            <small>Ocupación obligatoria</small>
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Nivel educativo <span style="color:#64748b;font-weight:850;">(Opcional)</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-graduation-cap input-ic"></i>
                <select class="form-select" id="nivel_educacion" name="nivel_educacion">
                  <option value="">Selecciona (opcional)</option>
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
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Ocupación <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-briefcase input-ic"></i>
                <select class="form-select" id="ocupacion" name="ocupacion" required>
                  <option value="">Selecciona</option>
                  <option value="Estudiante">Estudiante</option>
                  <option value="Empleado">Empleado</option>
                  <option value="Empresario">Empresario</option>
                  <option value="Comerciante">Comerciante</option>
                  <option value="Independiente">Independiente</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Privacidad: oculta y no obligatoria (D4) -->
        <div class="section" style="display:none !important;" aria-hidden="true">
          <input class="form-check-input" type="checkbox" id="politica" checked tabindex="-1">
        </div>

        <div class="actions">
          <div class="btn-row">
            <button type="button" class="btn-create" id="btnGuardarDatos">
              <i class="fa-solid fa-floppy-disk"></i>
              Guardar datos
            </button>
            <a href="index.php" class="btn btn-clear">
              <i class="fa-solid fa-arrow-left"></i>
              Volver
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include './admin/include/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin/js/lib/util.js"></script>
<script src="js/main.js"></script>
<script src="admin/js/departamentoDama.js"></script>
<script src="admin/js/device_participacion.js"></script>
<script>
(function(){
  DeviceParticipacion.fillHiddenInputs(document);

  var geoMun = <?= json_encode((string)($geo['codigo_municipio'] ?? ''), JSON_UNESCAPED_UNICODE) ?>;

  function loadMunicipiosThenSelect(){
    if (typeof DEPARTAMENTO !== 'undefined' && typeof DEPARTAMENTO.getMunicipios === 'function') {
      DEPARTAMENTO.getMunicipios();
    }
    if (geoMun) {
      setTimeout(function(){ $('#tbl_municipio_id').val(geoMun); }, 600);
    }
  }

  if ($('#tbl_departamento_id').val()) {
    loadMunicipiosThenSelect();
  }

  $('#tbl_departamento_id').on('change', function(){
    if (typeof DEPARTAMENTO !== 'undefined' && typeof DEPARTAMENTO.getMunicipios === 'function') {
      DEPARTAMENTO.getMunicipios();
    }
  });

  document.getElementById('btnGuardarDatos')?.addEventListener('click', async function(){
    var required = ['tbl_departamento_id','tbl_municipio_id','ideologia','rango_edad','genero','ocupacion'];
    for (var i=0;i<required.length;i++){
      var el = document.getElementById(required[i]);
      if (!el || !String(el.value||'').trim()){
        Swal.fire({icon:'warning', title:'Faltan datos', text:'Completa los campos obligatorios marcados con *.'});
        return;
      }
    }

    DeviceParticipacion.fillHiddenInputs(document);
    var btn = document.getElementById('btnGuardarDatos');
    if (btn) btn.disabled = true;

    var body = new URLSearchParams();
    body.set('op', 'participacioncommit');
    body.set('token', document.getElementById('participacion_token').value);
    body.set('codigo_departamento', document.getElementById('tbl_departamento_id').value);
    body.set('codigo_municipio', document.getElementById('tbl_municipio_id').value);
    body.set('tbl_departamento_id', document.getElementById('tbl_departamento_id').value);
    body.set('tbl_municipio_id', document.getElementById('tbl_municipio_id').value);
    body.set('barrio', document.getElementById('barrio').value || '');
    body.set('ideologia', document.getElementById('ideologia').value);
    body.set('rango_edad', document.getElementById('rango_edad').value);
    body.set('genero', document.getElementById('genero').value);
    body.set('nivel_educacion', document.getElementById('nivel_educacion').value || '');
    body.set('ocupacion', document.getElementById('ocupacion').value);
    DeviceParticipacion.appendToUrlSearchParams(body);

    try {
      var res = await fetch('admin/ajax/rqst.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString(),
        credentials: 'same-origin'
      });
      var data = await res.json();
      if (data && data.output && data.output.valid) {
        window.location.href = data.output.redirect || 'agradecimiento.php';
        return;
      }
      var msg = data?.output?.response?.content || data?.output?.message || 'No se pudo guardar.';
      Swal.fire({icon:'error', title:'Error', text: msg});
    } catch (e) {
      Swal.fire({icon:'error', title:'Error', text: e.message || 'Error de red'});
    } finally {
      if (btn) btn.disabled = false;
    }
  });
})();
</script>
</body>
</html>
