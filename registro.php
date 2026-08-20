<?php
$_REQUEST["route_map"] = true;

require './admin/include/generic_classes.php';
include './admin/classes/Votantes.php';
include './admin/classes/Departamento.php';

// Información de departamentos
$departamentos = Departamento::getAll(null);
$departamentosResponse = $departamentos['output']['response'] ?? [];

$optionDep = "";
foreach ($departamentosResponse as $dep) {
  $codigo = htmlspecialchars($dep['codigo_departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $nombre = htmlspecialchars($dep['departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $optionDep .= "<option value='{$codigo}'>{$codigo} - {$nombre}</option>";
}

$logo360 = 'assets/img/360 Estadisticas-04.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro | 360 Estadísticas</title>

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
              Registro ciudadano
            </span>

            <h1 class="hero-title">
              Regístrate para votar en la encuesta
            </h1>

            <p class="hero-subtitle">
              Crea tu cuenta, completa tu perfil básico y participa en los sondeos o cuestionarios activos de 360 Estadísticas.
            </p>
          </div>

          <a href="#" class="login-cta" id="btnOpenLogin" role="button" aria-label="Ya tengo una cuenta, iniciar sesión">
            <span class="login-cta-ic">
              <i class="fa-solid fa-right-to-bracket"></i>
            </span>

            <span class="login-cta-txt">
              <b>Ya tengo una cuenta</b>
              <small>Entrar para votar</small>
            </span>

            <span class="login-cta-go">
              <i class="fa-solid fa-arrow-right"></i>
            </span>
          </a>
        </div>

        <div class="steps">
          <div class="step">
            <div class="n">1</div>
            <div>
              <b>Datos de acceso</b>
              <span>Correo y contraseña. Tu correo será el usuario para ingresar.</span>
            </div>
          </div>

          <div class="step">
            <div class="n">2</div>
            <div>
              <b>Ubicación</b>
              <span>Departamento y municipio para clasificar resultados.</span>
            </div>
          </div>

          <div class="step">
            <div class="n">3</div>
            <div>
              <b>Perfil estadístico</b>
              <span>Datos básicos para mejorar los reportes agregados.</span>
            </div>
          </div>
        </div>
      </div>

      <form id="formvotantes" class="m-0">
        <input type="hidden" name="op" id="op">
        <input type="hidden" name="idVotantes" id="idVotantes">
        <input type="hidden" id="estado" name="estado" value="activo">

        <input type="hidden" id="device_token" name="device_token" value="">
        <input type="hidden" id="device_fingerprint" name="device_fingerprint" value="">
        <input type="hidden" id="device_user_agent" name="device_user_agent" value="">
        <input type="hidden" id="device_platform" name="device_platform" value="">
        <input type="hidden" id="device_language" name="device_language" value="">
        <input type="hidden" id="device_timezone" name="device_timezone" value="">

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-id-card"></i> Paso 1 • Datos de acceso</h3>
            <small>Obligatorio</small>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Correo electrónico <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-envelope input-ic"></i>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Ej: correo@dominio.com" onblur="VOTANTES.checkAvailability(this)">
              </div>
              <div class="help">Este correo será también tu usuario para iniciar sesión y recuperar acceso.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Contraseña <span class="req">*</span></label>
              <div class="input-wrap pw-wrap">
                <i class="fa-solid fa-lock input-ic"></i>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Crea una contraseña">
                <button type="button" class="pw-eye" id="btnTogglePw" aria-label="Ver contraseña" aria-pressed="false">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
              <div class="help">Pulsa el ojo para ver lo que escribes.</div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-location-dot"></i> Paso 2 • Ubicación</h3>
            <small>Obligatorio</small>
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
              <div class="help">Primero elige el departamento.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Municipio <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-location-crosshairs input-ic"></i>
                <select id="tbl_municipio_id" name="tbl_municipio_id" class="form-select" required>
                  <option value="">Primero elige un departamento</option>
                </select>
              </div>
              <div class="help">Se carga automáticamente.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Barrio <span style="color:#64748b;font-weight:850;">(Opcional)</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-house input-ic"></i>
                <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Ej: La Esperanza">
              </div>
              <div class="help">Si no lo sabes, déjalo en blanco.</div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-chart-pie"></i> Paso 3 • Perfil estadístico</h3>
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
              <div class="help">Si no estás seguro, elige “Sin definir”.</div>
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
            <h3><i class="fa-solid fa-briefcase"></i> Paso 4 • Educación y ocupación</h3>
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
              <div class="help">Elige la que mejor te describa hoy.</div>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="sec-head">
            <h3><i class="fa-solid fa-shield-halved"></i> Privacidad</h3>
            <small>Obligatorio</small>
          </div>

          <div class="privacy">
            <input class="form-check-input" type="checkbox" id="politica" required>
            <label for="politica">
              Acepto la
              <a href="politica.php" target="_blank">política de privacidad</a>
              y autorizo el tratamiento de datos.
              <p class="politica">
                Declaro que he leído y acepto la Política de Privacidad y Tratamiento de Datos Personales de Encuestas360.com,
                y autorizo de manera previa, expresa e informada el tratamiento de mis datos personales para las finalidades allí descritas.
              </p>
              <div class="help" style="margin-top:6px;">Sin esta aceptación no podemos completar el registro.</div>
            </label>
          </div>
        </div>

        <div class="actions">
          <div class="btn-row">
            <button type="button" class="btn-create" id="btnCrearCuenta" onclick="VOTANTES.validateData();">
              <i class="fa-solid fa-user-check"></i>
              Crear mi cuenta
            </button>

            <button type="button" onclick="VOTANTES.emptyCells();" class="btn btn-clear">
              <i class="fa-solid fa-eraser"></i>
              Limpiar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include './admin/include/footer.php'; ?>

<!-- Modal Login -->
<div class="modal fade modal-360" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header px-3 py-3">
        <div class="d-flex gap-3 align-items-center">
          <div class="modal-logo">
            <img src="<?= htmlspecialchars($logo360) ?>" alt="360 Estadísticas">
          </div>
          <div>
            <b style="font-weight:950;color:#fff;font-size:18px;">Iniciar sesión</b><br>
            <small style="font-weight:750;color:rgba(255,255,255,.82);">Accede para continuar</small>
          </div>
        </div>

        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body p-3">
        <div class="modal-form-card">
          <form id="formLoginVotantes" autocomplete="on">
            <div class="mb-3">
              <label class="form-label">Correo o usuario</label>
              <input type="text" class="form-control" id="login_user" name="login_user" placeholder="Escribe tu usuario o correo" required style="padding-left:14px;">
            </div>

            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <div style="position:relative;">
                <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Escribe tu contraseña" required style="padding-left:14px;padding-right:58px;">
                <button type="button" id="toggleLoginPassword" class="pw-eye" style="right:8px;">
                  <i class="fa-solid fa-eye" id="loginEyeIcon"></i>
                </button>
              </div>
            </div>

            <button type="button" class="btn-login-360" id="btnLoginSubmit">
              <i class="fa-solid fa-arrow-right-to-bracket"></i>
              Entrar
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="admin/js/lib/util.js"></script>
<script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>

<script src="js/main.js"></script>
<script src="admin/js/departamentoDama.js"></script>
<script src="<?php echo Util::versionar('./admin/js/votantes.js'); ?>"></script>

<script>
  const departamento = $("#departamentoConfiguracionInput").val();

  if (departamento) {
    $("#tbl_departamento_id").val(departamento);

    if (typeof DEPARTAMENTO !== "undefined" && typeof DEPARTAMENTO.getMunicipios === "function") {
      DEPARTAMENTO.getMunicipios();
    }
  }

  $("#tbl_departamento_id").on("change", function(){
    if (typeof DEPARTAMENTO !== "undefined" && typeof DEPARTAMENTO.getMunicipios === "function") {
      DEPARTAMENTO.getMunicipios();
    }
  });

  (function(){
    const btn = document.getElementById('btnTogglePw');
    const input = document.getElementById('password');

    if (!btn || !input) return;

    btn.addEventListener('click', function(){
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      btn.setAttribute('aria-pressed', isPass ? 'true' : 'false');

      const icon = btn.querySelector('i');

      if (icon) {
        icon.classList.toggle('fa-eye', !isPass);
        icon.classList.toggle('fa-eye-slash', isPass);
      }

      input.focus();
    });
  })();

  (function(){
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');
    const loginPasswordInput = document.getElementById('login_password');
    const loginEyeIcon = document.getElementById('loginEyeIcon');

    if (toggleLoginPassword && loginPasswordInput && loginEyeIcon) {
      toggleLoginPassword.addEventListener('click', function() {
        const type = loginPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        loginPasswordInput.setAttribute('type', type);

        loginEyeIcon.classList.toggle('fa-eye', type !== 'text');
        loginEyeIcon.classList.toggle('fa-eye-slash', type === 'text');
      });
    }
  })();

  (function(){
    function showLoginModal(){
      const el = document.getElementById('loginModal');
      if (!el || typeof bootstrap === "undefined" || !bootstrap.Modal) return;

      const instance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el, {
        backdrop:'static',
        keyboard:false
      });

      instance.show();

      el.addEventListener('shown.bs.modal', function(){
        const u = document.getElementById('login_user');
        if (u) u.focus();
      }, { once:true });
    }

    document.addEventListener('click', function(e){
      const btn = e.target.closest('#btnOpenLogin');
      if (!btn) return;

      e.preventDefault();
      showLoginModal();
    });
  })();

  document.getElementById('btnLoginSubmit')?.addEventListener('click', async function () {
    const nickname = document.getElementById('login_user')?.value.trim() || '';
    const hashpass = document.getElementById('login_password')?.value.trim() || '';

    if (!nickname || !hashpass) {
      Swal.fire('Error', 'Por favor completa todos los campos.', 'error');
      return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Validando...';

    const formData = new FormData();
    formData.append('nickname', nickname);
    formData.append('hashpass', hashpass);

    try {
      const res = await fetch('login_process.php', {
        method: 'POST',
        body: formData
      });

      const data = await res.json();

      if (data.status === 'success') {
        window.location.href = data.redirect;
      } else {
        Swal.fire('Error', data.message || 'Error de inicio de sesión.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar';
      }
    } catch (err) {
      Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
      console.error(err);

      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar';
    }
  });

  document.getElementById('formLoginVotantes')?.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
      e.preventDefault();
      document.getElementById('btnLoginSubmit')?.click();
    }
  });

  // Datos de dispositivo
  (function(){
    const setVal = (id, value) => {
      const el = document.getElementById(id);
      if (el) el.value = value || '';
    };

    setVal('device_user_agent', navigator.userAgent);
    setVal('device_platform', navigator.platform);
    setVal('device_language', navigator.language);
    setVal('device_timezone', Intl.DateTimeFormat().resolvedOptions().timeZone);

    const raw = [
      navigator.userAgent,
      navigator.platform,
      navigator.language,
      screen.width,
      screen.height,
      Intl.DateTimeFormat().resolvedOptions().timeZone
    ].join('|');

    let hash = 0;
    for (let i = 0; i < raw.length; i++) {
      hash = ((hash << 5) - hash) + raw.charCodeAt(i);
      hash |= 0;
    }

    setVal('device_fingerprint', 'fp_' + Math.abs(hash));
    setVal('device_token', 'tk_' + Math.abs(hash) + '_' + Date.now());
  })();
</script>

</body>
</html>
