<?php
include './admin/include/head.php';

$mensaje = '';

include './admin/classes/SessionData.php';
include './admin/classes/DbConection.php';
include './admin/classes/Util.php';
include './admin/classes/Usuario.php';

$departamentoPrincipal = Util::getIdentificadorDepartamentoPrincipal();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['session_user'])) {
    header('Location: dashboard.php');
    exit;
}

$rqst = $_REQUEST;
$op = $rqst['op'] ?? '';

if ($op === 'pms_usrlogin') {
    $nickname = $rqst['nickname'] ?? '';
    $hashpass = $rqst['hashpass'] ?? '';
    if (!empty($nickname) && !empty($hashpass)) {
        $hashpass = md5($hashpass);
        $arr = ['nickname' => $nickname, 'hashpass' => $hashpass];
        $res = Usuario::login($arr);
        $isvalid = $res['output']['valid'];

        if ($isvalid) {
            $_SESSION['session_user'] = $res['output']['response'][0];
            $_SESSION['session_user']['permisos'] = $res['output']['permisos'];
            $_SESSION['session_user']['configuracion'] = $res['output']['configuracion'];

            $userType = SessionData::getUserType();

            if($userType == 'Encuestador'){
                header('Location: votantes_encuestador.php');
                exit;
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $mensaje = $res['output']['response']['content'];
        }
    } else {
        $mensaje = 'Todos los campos son obligatorios.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Estadísticas 360 - Ingreso</title>

  <link rel="icon" href="./assets/img/favicon.png" type="image/x-icon">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">

  <!-- ✅ FontAwesome (tu ruta real) -->
  <link rel="stylesheet" href="../flot-old/fontawesome-free/css/all.min.css">

  <!-- IBM Plex Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

  <style>
    :root{
      --nav-blue:#20427F;
      --nav-blue-2:#132b52;

      --glass: rgba(255,255,255,.10);
      --glass-2: rgba(255,255,255,.14);
      --stroke: rgba(255,255,255,.18);

      --shadow: 0 24px 60px rgba(2,6,23,.45);
      --shadow-soft: 0 16px 40px rgba(2,6,23,.30);

      --radius-xl: 28px;
      --radius-lg: 20px;

      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.72);
    }

    *{ font-family: "IBM Plex Sans", sans-serif !important; }

    /* FontAwesome fix */
    .fa, .fas, .far, .fal, .fab,
    i[class^="fa"], i[class*=" fa-"]{ font-family: "Font Awesome 5 Free" !important; }
    .fas{ font-weight: 900 !important; }
    .far{ font-weight: 400 !important; }

    body{
      margin:0;
      min-height:100vh;
      overflow-x:hidden;
      background:#050b1a;
    }

    /* ===== VIDEO BG ===== */
    .video-background{
      position: fixed;
      inset: 0;
      z-index: -3;
      overflow: hidden;
      background:#050b1a;
    }
    .video-background video{
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: saturate(1.1) contrast(1.05) brightness(.80);
      transform: scale(1.03);
    }

    .bg-overlay{
      position: fixed;
      inset: 0;
      z-index: -2;
      background:
        radial-gradient(900px 500px at 18% 18%, rgba(32,66,127,.55), transparent 60%),
        radial-gradient(700px 520px at 85% 30%, rgba(13,110,253,.35), transparent 60%),
        radial-gradient(900px 700px at 50% 100%, rgba(0,0,0,.55), rgba(0,0,0,.76)),
        linear-gradient(180deg, rgba(2,6,23,.40), rgba(2,6,23,.80));
      backdrop-filter: blur(2px);
    }

    .noise{
      position: fixed;
      inset: 0;
      z-index: -1;
      pointer-events:none;
      opacity:.06;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)' opacity='.5'/%3E%3C/svg%3E");
    }

    /* ===== WRAPPER ===== */
    .auth-wrapper{
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 28px 14px;
    }

    .login-shell{
      width: min(1120px, 100%);
      border-radius: var(--radius-xl);
      background: linear-gradient(180deg, rgba(255,255,255,.12), rgba(255,255,255,.08));
      border: 1px solid rgba(255,255,255,.14);
      box-shadow: var(--shadow);
      overflow: hidden;
      position: relative;
    }

    .login-shell::before{
      content:"";
      position:absolute;
      inset:-2px;
      background: linear-gradient(90deg, rgba(255,255,255,.28), rgba(255,255,255,0), rgba(255,255,255,.22));
      opacity: .25;
      pointer-events:none;
      mask: linear-gradient(#000, transparent 65%);
    }

    .login-grid{
      display:grid;
      grid-template-columns: 1.05fr .95fr;
      min-height: 640px;
    }

    /* ===== LEFT PANEL ===== */
    .panel-form{
      padding: 34px 34px 28px;
    }

    .brand-logo{
      width: 230px;
      max-width: 100%;
      height:auto;
      display:block;
      filter: drop-shadow(0 8px 18px rgba(0,0,0,.35));
      margin-bottom: 6px;
    }

    .chips{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      margin: 10px 0 18px;
    }
    .chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 8px 12px;
      border-radius:999px;
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.14);
      color: rgba(255,255,255,.88);
      font-size: 12.5px;
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
      white-space: nowrap;
    }
    .chip i{ opacity:.9; }

    .headline{
      margin-top: 8px;
      color: #fff;
      font-size: 30px;
      line-height: 1.1;
      font-weight: 600;
      letter-spacing: .2px;
    }
    .subheadline{
      margin-top: 10px;
      margin-bottom: 18px;
      color: var(--muted);
      font-size: 14.5px;
      line-height: 1.45;
      max-width: 560px;
    }

    .form-card{
      padding: 18px;
      border-radius: 22px;
      background: rgba(0,0,0,.18);
      border: 1px solid rgba(255,255,255,.12);
      box-shadow: var(--shadow-soft);
    }

    .field{
      position: relative;
      margin-bottom: 14px;
    }
    .field .ico{
      position:absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(15,23,42,.85);
      z-index: 2;
      font-size: 14px;
      pointer-events:none;
    }

    .field input{
      width: 100%;
      height: 50px;
      border-radius: 14px;
      border: 1px solid rgba(32,66,127,.18);
      background: #fff;
      color: #0f172a;
      padding: 12px 14px 12px 42px;
      font-size: 14.5px;
      outline: none;
      transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
      box-shadow: 0 10px 20px rgba(2,6,23,.10);
    }

    .field input:focus{
      border-color: rgba(32,66,127,.55);
      box-shadow: 0 0 0 4px rgba(32,66,127,.18), 0 10px 22px rgba(2,6,23,.14);
      transform: translateY(-1px);
    }

    /* ===== TOGGLE PASSWORD ===== */
    .field input.with-toggle{ padding-right: 58px; }

    .toggle-pass{
      position:absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 42px;
      height: 42px;
      border-radius: 12px;
      border: 1px solid rgba(32,66,127,.18);
      background: rgba(32,66,127,.08);
      display:flex;
      align-items:center;
      justify-content:center;
      cursor:pointer;
      z-index: 3;
      transition: transform .15s ease, background .15s ease, box-shadow .15s ease, filter .15s ease;
      box-shadow: 0 8px 18px rgba(2,6,23,.10);
      padding: 0;
      outline: none;
    }

    .toggle-pass:hover{
      background: rgba(32,66,127,.14);
      filter: brightness(1.03);
      transform: translateY(-50%) scale(1.03);
    }
    .toggle-pass:active{
      transform: translateY(-50%) scale(.98);
    }

    .toggle-pass i{
      color: rgba(15,23,42,.90);
      font-size: 14px;
    }

    .helper-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      margin: 10px 2px 4px;
      flex-wrap: wrap;
    }

    .remember{
      display:flex;
      align-items:center;
      gap: 10px;
      color: rgba(255,255,255,.85);
      font-size: 13.5px;
      user-select:none;
    }

    .remember input{
      width: 16px;
      height: 16px;
      border-radius: 4px;
      accent-color: #ffffff;
    }

    .forgot a{
      color: rgba(255,255,255,.92);
      font-size: 13.5px;
      text-decoration: none;
      border-bottom: 1px dashed rgba(255,255,255,.35);
      padding-bottom: 2px;
    }
    .forgot a:hover{ border-bottom-color: rgba(255,255,255,.75); }

    /* ===== BTN ===== */
    .btn-login{
      margin-top: 14px;
      width: 100%;
      height: 52px;
      border: 0;
      border-radius: 16px;
      color: #fff;
      font-size: 15.5px;
      letter-spacing: .2px;
      background: linear-gradient(135deg, #22c55e, #16a34a);
      box-shadow: 0 18px 30px rgba(34,197,94,.22), 0 10px 22px rgba(2,6,23,.22);
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
      display:flex;
      align-items:center;
      justify-content:center;
      gap: 10px;
    }

    .btn-login:hover{
      transform: translateY(-1px);
      filter: brightness(1.03);
      box-shadow: 0 22px 40px rgba(34,197,94,.26), 0 12px 26px rgba(2,6,23,.25);
    }

    .btn-login:active{ transform: translateY(0px); }

    /* ===== ERROR ===== */
    .alert-pro{
      display:flex;
      align-items:flex-start;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 14px;
      background: rgba(239,68,68,.16);
      border: 1px solid rgba(239,68,68,.35);
      color: rgba(255,255,255,.92);
      font-size: 13px;
      line-height: 1.35;
      margin-bottom: 12px;
      box-shadow: 0 10px 22px rgba(2,6,23,.22);
    }
    .alert-pro i{ margin-top: 2px; }

    .footer-mini{
      margin-top: 16px;
      color: rgba(255,255,255,.62);
      font-size: 12.5px;
      display:flex;
      justify-content:space-between;
      gap: 10px;
      flex-wrap: wrap;
    }
    .footer-mini a{
      color: rgba(255,255,255,.82);
      text-decoration:none;
      border-bottom: 1px solid rgba(255,255,255,.18);
    }
    .footer-mini a:hover{ border-bottom-color: rgba(255,255,255,.55); }

    /* ===== RIGHT PANEL (PC) ===== */
    .panel-visual{
      position: relative;
      padding: 28px;
      background:
        radial-gradient(900px 520px at 20% 20%, rgba(255,255,255,.14), transparent 60%),
        radial-gradient(700px 520px at 80% 25%, rgba(32,66,127,.25), transparent 60%),
        linear-gradient(180deg, rgba(255,255,255,.10), rgba(0,0,0,.18));
      border-left: 1px solid rgba(255,255,255,.12);
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      gap: 18px;
    }

    .visual-title{
      color: rgba(255,255,255,.95);
      font-size: 18px;
      margin: 0;
      letter-spacing: .2px;
      font-weight: 500;
    }

    .visual-text{
      color: rgba(255,255,255,.72);
      font-size: 13.5px;
      margin: 8px 0 0;
      line-height: 1.45;
      max-width: 420px;
    }

    .visual-card{
      border-radius: 22px;
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.16);
      padding: 16px;
      box-shadow: 0 18px 40px rgba(2,6,23,.25);
    }

    .visual-img{
      width: 100%;
      height: 340px;
      object-fit: cover;
      border-radius: 18px;
      border: 1px solid rgba(255,255,255,.14);
      box-shadow: 0 16px 30px rgba(2,6,23,.25);
    }

    .kpis{
      display:grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
      margin-top: 14px;
    }
    .kpi{
      padding: 12px 12px;
      border-radius: 18px;
      background: rgba(0,0,0,.16);
      border: 1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.90);
    }
    .kpi .n{ font-size: 15px; font-weight: 500; margin:0; }
    .kpi .t{
      font-size: 11.5px;
      color: rgba(255,255,255,.68);
      margin: 2px 0 0;
      letter-spacing: .5px;
      text-transform: uppercase;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px){
      .login-grid{ grid-template-columns: 1fr; }
      .panel-visual{ display:none; }
      .panel-form{ padding: 28px 18px 20px; }
      .headline{ font-size: 26px; }
    }

    @media (max-width: 420px){
      .headline{ font-size: 24px; }
      .field input{ height: 48px; }
      .btn-login{ height: 50px; }
    }
  </style>
</head>

<body>
  <div class="video-background">
    <video autoplay muted loop playsinline>
      <source src="assets/vid/antioquia.mp4" type="video/mp4">
      Tu navegador no soporta la reproducción de videos.
    </video>
  </div>
  <div class="bg-overlay"></div>
  <div class="noise"></div>

  <div class="auth-wrapper">
    <div class="login-shell">
      <div class="login-grid">

        <!-- PANEL FORM -->
        <div class="panel-form">

          <img src="assets/img/estadistica3.png" class="brand-logo" alt="Logo">

          <div class="chips">
            <span class="chip"><i class="fas fa-poll"></i> Encuestas</span>
            <span class="chip"><i class="fas fa-chart-line"></i> Estadísticas</span>
            <span class="chip"><i class="fas fa-shield-alt"></i> Acceso seguro</span>
          </div>

          <h1 class="headline">Bienvenido 👋</h1>
          <p class="subheadline">
            Accede con tu usuario y contraseña para gestionar la información en tiempo real.
          </p>

          <div class="form-card">
            <form class="form" method="POST" action="login.php" autocomplete="off">
              <input type="hidden" name="op" value="pms_usrlogin">

              <?php if (!empty($mensaje)): ?>
                <div class="alert-pro">
                  <i class="fas fa-exclamation-triangle"></i>
                  <div><?= htmlspecialchars($mensaje); ?></div>
                </div>
              <?php endif; ?>

              <div class="field">
                <span class="ico"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="nickname" name="nickname" value="" placeholder="Usuario" required>
              </div>

              <div class="field">
                <span class="ico"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control with-toggle" id="hashpass" name="hashpass" value="" placeholder="Contraseña" required>

                <!-- ✅ Botón ver contraseña -->
                <button type="button" class="toggle-pass" id="togglePassword" aria-label="Mostrar u ocultar contraseña">
                  <i class="fas fa-eye" id="togglePasswordIcon"></i>
                </button>
              </div>

              <div class="helper-row">
                <label class="remember">
                  <input class="form-check-input" type="checkbox" id="customCheck1" checked>
                  Recuérdame
                </label>

                <div class="forgot">
                  <a href="recuperar-contrasena.php">¿Olvidaste tu contraseña?</a>
                </div>
              </div>

              <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Ingresar
              </button>

              <div class="footer-mini">
                <span>© <script>document.write(new Date().getFullYear())</script> Estadísticas 360</span>
                <a href="https://www.spidersoftware.co/" target="_blank">Spidersoftware SAS</a>
              </div>

            </form>
          </div>
        </div>

        <!-- PANEL VISUAL DERECHO (SOLO PC) -->
        <div class="panel-visual">
          <div>
            <h3 class="visual-title">Tu control, en un solo lugar</h3>
            <p class="visual-text">
              Visualiza métricas, resultados y análisis en segundos. Un panel pensado para decisiones rápidas y trazabilidad total.
            </p>
          </div>

          <div class="visual-card">
            <img src="assets/img/login_saas.png" class="visual-img" alt="Visual">

            <div class="kpis">
              <div class="kpi">
                <p class="n">100%</p>
                <p class="t">Trazabilidad</p>
              </div>
              <div class="kpi">
                <p class="n">Real-time</p>
                <p class="t">Métricas</p>
              </div>
              <div class="kpi">
                <p class="n">Seguro</p>
                <p class="t">Acceso</p>
              </div>
            </div>
          </div>

          <div class="visual-text" style="opacity:.85">
            <i class="fas fa-info-circle"></i>
            Recomendación: usa un navegador actualizado para una mejor experiencia.
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ✅ Script al final para que funcione sí o sí -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const passInput = document.getElementById('hashpass');
      const btn = document.getElementById('togglePassword');
      const icon = document.getElementById('togglePasswordIcon');

      if (!passInput || !btn || !icon) return;

      btn.addEventListener('click', function () {
        const isHidden = passInput.type === 'password';
        passInput.type = isHidden ? 'text' : 'password';

        // Cambiar ícono
        icon.classList.toggle('fa-eye', !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);

        // micro animación
        btn.style.transform = 'translateY(-50%) scale(0.96)';
        setTimeout(() => btn.style.transform = 'translateY(-50%) scale(1)', 120);

        // mantener foco
        passInput.focus();
        passInput.setSelectionRange(passInput.value.length, passInput.value.length);
      });
    });
  </script>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
</body>
</html>
