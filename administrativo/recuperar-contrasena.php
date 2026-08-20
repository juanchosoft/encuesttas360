<?php
include './admin/include/head.php';
session_start();
$mensaje = '';

include './admin/classes/DbConection.php';
include './admin/classes/Util.php';

$departamentoPrincipal = Util::getIdentificadorDepartamentoPrincipal();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Estadísticas 360 - Recuperar acceso</title>

  <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">

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
      --white:#ffffff;
      --ink:#0f172a;

      --glass: rgba(255,255,255,.10);
      --glass-2: rgba(255,255,255,.14);
      --stroke: rgba(255,255,255,.18);

      --shadow: 0 24px 60px rgba(2,6,23,.45);
      --shadow-soft: 0 16px 40px rgba(2,6,23,.30);

      --radius-xl: 28px;
      --radius-lg: 20px;
    }

    body, input, button, select, textarea{
      font-family: "IBM Plex Sans", sans-serif !important;
    }

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
      width:100%;
      height:100%;
      object-fit:cover;
      filter:saturate(1.1) contrast(1.05) brightness(.80);
      transform:scale(1.03);
    }

    /* overlay */
    .bg-overlay{
      position: fixed;
      inset: 0;
      z-index: -2;
      background:
        radial-gradient(900px 500px at 20% 15%, rgba(32,66,127,.55), transparent 60%),
        radial-gradient(700px 500px at 85% 30%, rgba(13,110,253,.35), transparent 60%),
        radial-gradient(900px 700px at 50% 100%, rgba(0,0,0,.55), rgba(0,0,0,.75)),
        linear-gradient(180deg, rgba(2,6,23,.40), rgba(2,6,23,.78));
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

    .shell{
      width: min(1120px, 100%);
      border-radius: var(--radius-xl);
      background: linear-gradient(180deg, rgba(255,255,255,.12), rgba(255,255,255,.08));
      border: 1px solid rgba(255,255,255,.14);
      box-shadow: var(--shadow);
      overflow: hidden;
      position: relative;
    }

    .shell::before{
      content:"";
      position:absolute;
      inset:-2px;
      background: linear-gradient(90deg, rgba(255,255,255,.28), rgba(255,255,255,0), rgba(255,255,255,.22));
      opacity:.25;
      pointer-events:none;
      mask: linear-gradient(#000, transparent 65%);
    }

    .grid{
      display:grid;
      grid-template-columns: 1.05fr .95fr;
      min-height: 600px;
    }

    /* ===== LEFT ===== */
    .panel{
      padding: 34px 34px 28px;
      position: relative;
    }

    .brand{
      display:flex;
      align-items:center;
      gap: 14px;
      margin-bottom: 10px;
    }
    .brand img{
      width: 220px;
      max-width: 100%;
      height: auto;
      display:block;
      filter: drop-shadow(0 8px 18px rgba(0,0,0,.35));
    }

    .chips{
      display:flex;
      flex-wrap:wrap;
      gap:10px;
      margin: 12px 0 18px;
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

    .title{
      margin-top: 10px;
      color:#fff;
      font-size: 30px;
      line-height:1.1;
      font-weight: 600;
      letter-spacing:.2px;
    }
    .subtitle{
      margin-top: 10px;
      margin-bottom: 18px;
      color: rgba(255,255,255,.78);
      font-size: 14.5px;
      line-height: 1.45;
      max-width: 520px;
    }

    .card-form{
      margin-top: 10px;
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
      z-index:2;
      font-size:14px;
      pointer-events:none;
    }
    .field input{
      width:100%;
      height: 50px;
      border-radius: 14px;
      border: 1px solid rgba(32,66,127,.18);
      background:#fff;
      color:#0f172a;
      padding: 12px 14px 12px 42px;
      font-size: 14.5px;
      outline:none;
      box-shadow: 0 10px 20px rgba(2,6,23,.10);
      transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
    }
    .field input:focus{
      border-color: rgba(32,66,127,.55);
      box-shadow: 0 0 0 4px rgba(32,66,127,.18), 0 10px 22px rgba(2,6,23,.14);
      transform: translateY(-1px);
    }

    .helper{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      flex-wrap:wrap;
      margin: 8px 2px 2px;
    }
    .backlink{
      color: rgba(255,255,255,.92);
      font-size: 13.5px;
      text-decoration:none;
      border-bottom: 1px dashed rgba(255,255,255,.35);
      padding-bottom: 2px;
    }
    .backlink:hover{ border-bottom-color: rgba(255,255,255,.75); }

    .btn-action{
      margin-top: 12px;
      width:100%;
      height: 52px;
      border:0;
      border-radius: 16px;
      color:#fff;
      font-size: 15.5px;
      letter-spacing:.2px;
      background: linear-gradient(135deg, #22c55e, #16a34a);
      box-shadow: 0 18px 30px rgba(34,197,94,.22), 0 10px 22px rgba(2,6,23,.22);
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
    }
    .btn-action:hover{
      transform: translateY(-1px);
      filter: brightness(1.03);
      box-shadow: 0 22px 40px rgba(34,197,94,.26), 0 12px 26px rgba(2,6,23,.25);
    }
    .btn-action:active{ transform: translateY(0px); }

    .footer-mini{
      margin-top: 14px;
      color: rgba(255,255,255,.62);
      font-size: 12.5px;
      display:flex;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
    }
    .footer-mini a{
      color: rgba(255,255,255,.82);
      text-decoration:none;
      border-bottom: 1px solid rgba(255,255,255,.18);
    }
    .footer-mini a:hover{ border-bottom-color: rgba(255,255,255,.55); }

    /* ===== RIGHT ===== */
    .visual{
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
      gap:18px;
    }

    .vtitle{
      color: rgba(255,255,255,.95);
      font-size: 18px;
      margin:0;
      font-weight: 500;
    }
    .vtext{
      color: rgba(255,255,255,.72);
      font-size: 13.5px;
      margin: 8px 0 0;
      line-height:1.45;
      max-width: 420px;
    }

    .vcard{
      border-radius: 22px;
      background: rgba(255,255,255,.10);
      border: 1px solid rgba(255,255,255,.16);
      padding: 16px;
      box-shadow: 0 18px 40px rgba(2,6,23,.25);
    }

    .vimg{
      width:100%;
      height: 340px;
      object-fit: cover;
      border-radius: 18px;
      border: 1px solid rgba(255,255,255,.14);
      box-shadow: 0 16px 30px rgba(2,6,23,.25);
    }

    .kpis{
      display:grid;
      grid-template-columns: repeat(3, minmax(0,1fr));
      gap: 10px;
      margin-top: 14px;
    }
    .kpi{
      padding: 12px;
      border-radius: 18px;
      background: rgba(0,0,0,.16);
      border: 1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.90);
    }
    .kpi .n{ margin:0; font-size: 15px; font-weight: 500; }
    .kpi .t{ margin:2px 0 0; font-size: 11.5px; color: rgba(255,255,255,.68); text-transform: uppercase; letter-spacing:.5px; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px){
      .grid{ grid-template-columns: 1fr; }
      .visual{ display:none; }
      .panel{ padding: 28px 18px 20px; }
      .title{ font-size: 26px; }
    }
    @media (max-width: 420px){
      .title{ font-size: 24px; }
      .field input{ height: 48px; }
      .btn-action{ height: 50px; }
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
    <div class="shell">
      <div class="grid">

        <!-- LEFT -->
        <div class="panel">

          <div class="brand">
            <img src="assets/img/gob360.png" alt="Logo">
          </div>

          <div class="chips">
            <span class="chip"><i class="fas fa-envelope"></i> Recuperación</span>
            <span class="chip"><i class="fas fa-shield-alt"></i> Acceso seguro</span>
            <span class="chip"><i class="fas fa-clock"></i> Enlace temporal</span>
          </div>

          <h1 class="title">Recupera tu acceso 🔐</h1>
          <p class="subtitle">
            Ingresa tu correo registrado y te enviaremos un enlace de recuperación para restablecer el acceso de forma segura.
          </p>

          <div class="card-form">
            <form class="form" method="POST" action="enviar_enlace.php" autocomplete="off">
              <div class="field">
                <span class="ico"><i class="fas fa-at"></i></span>
                <input type="email" class="form-control" id="nickname" name="nickname" placeholder="Correo registrado" required>
              </div>

              <div class="helper">
                <span></span>
                <a class="backlink" href="login.php"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
              </div>

              <button type="submit" class="btn-action">
                <i class="fas fa-paper-plane"></i> Enviar enlace de recuperación
              </button>

              <div class="footer-mini">
                <span>© <script>document.write(new Date().getFullYear())</script> Estadísticas 360</span>
                <a href="https://www.spidersoftware.co/" target="_blank">Spidersoftware SAS</a>
              </div>
            </form>
          </div>

        </div>

        <!-- RIGHT (PC) -->
        <div class="visual">
          <div>
            <h3 class="vtitle">Recuperación rápida y segura</h3>
            <p class="vtext">
              Te enviaremos un enlace temporal al correo registrado. Si no lo ves, revisa tu bandeja de spam o correo no deseado.
            </p>
          </div>

          <div class="vcard">
            <img src="assets/img/login_saas.png" class="vimg" alt="Visual">
            <div class="kpis">
              <div class="kpi">
                <p class="n">1 min</p>
                <p class="t">Proceso</p>
              </div>
              <div class="kpi">
                <p class="n">Seguro</p>
                <p class="t">Enlace</p>
              </div>
              <div class="kpi">
                <p class="n">Soporte</p>
                <p class="t">24/7</p>
              </div>
            </div>
          </div>

          <div class="vtext" style="opacity:.85">
            <i class="fas fa-info-circle"></i>
            Recomendación: usa un navegador actualizado para una mejor experiencia.
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
</body>
</html>
