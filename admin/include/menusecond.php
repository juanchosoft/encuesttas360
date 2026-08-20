<?php
  $nombreCompleto = $_SESSION['session_user']['nombre_completo']
    ?? $_SESSION['session_user']['usuario']
    ?? 'Usuario';

  $p = preg_split('/\s+/', trim($nombreCompleto));
  $nombreCorto = trim(($p[0] ?? 'Usuario') . ' ' . ($p[1] ?? ''));
  $userId = (int)($_SESSION['session_user']['id'] ?? 0);

  $logoHeader360 = 'assets/img/360 Estadisticas-04.png';

  // Verificar si el usuario puede ver los registros
  $puedeVerRegistros = false;

  if ($userId > 0) {
    require_once __DIR__ . '/../classes/DbConection.php';
    require_once __DIR__ . '/../classes/Util.php';
    require_once __DIR__ . '/../classes/Sondeo.php';
    require_once __DIR__ . '/../classes/RespuestaCuestionario.php';

    $config = Util::getInformacionConfiguracion();
    $opcionActivaWeb = $config[0]['opcion_activa_web'] ?? 'sondeo';

    $dbConnection = new DbConection();

    if ($opcionActivaWeb === 'sondeo') {
      $sondeo = new Sondeo($dbConnection);
      $puedeVerRegistros = $sondeo->verificarSiUsuarioVoto($userId);

    } elseif ($opcionActivaWeb === 'cuestionario') {
      $cuestionario = new RespuestaCuestionario($dbConnection);
      $puedeVerRegistros = $cuestionario->verificarSiUsuarioRespondio($userId);

    } elseif ($opcionActivaWeb === 'ambos') {
      $sondeo = new Sondeo($dbConnection);
      $cuestionario = new RespuestaCuestionario($dbConnection);

      $puedeVerRegistros = $sondeo->verificarSiUsuarioVoto($userId)
        || $cuestionario->verificarSiUsuarioRespondio($userId);

    } else {
      $cuestionario = new RespuestaCuestionario($dbConnection);
      $puedeVerRegistros = $cuestionario->verificarSiUsuarioRespondio($userId);
    }
  }
?>

<style>
  :root{
    --h360-bg:#041020;
    --h360-bg2:#061326;
    --h360-bg3:#082342;
    --h360-blue:#2378ff;
    --h360-cyan:#00d4ff;
    --h360-green:#30e6b1;
    --h360-white:#ffffff;
    --h360-text:#eaf6ff;
    --h360-muted:#8fb4d6;
    --h360-stroke:rgba(148,210,255,.22);
    --h360-shadow:0 14px 42px rgba(0,0,0,.30);
    --h360-danger:#ef4444;
  }

  #mainNavbar.navbar-saas{
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:1050;
    padding:.56rem 0 !important;
    background:
      radial-gradient(520px 160px at 10% 0%, rgba(35,120,255,.26), transparent 65%),
      radial-gradient(520px 160px at 92% 0%, rgba(48,230,177,.16), transparent 65%),
      linear-gradient(135deg, rgba(4,16,32,.98), rgba(6,19,38,.98) 48%, rgba(8,35,66,.98)) !important;
    border-bottom:1px solid var(--h360-stroke) !important;
    box-shadow:var(--h360-shadow) !important;
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    transition:box-shadow .22s ease, border-color .22s ease, padding .22s ease;
  }

  #mainNavbar.navbar-saas::before{
    content:"";
    position:absolute;
    inset:0;
    pointer-events:none;
    background-image:
      linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
    background-size:38px 38px;
    opacity:.48;
    mask-image:linear-gradient(90deg, transparent, #000 18%, #000 82%, transparent);
  }

  #mainNavbar.navbar-saas::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    bottom:-1px;
    height:2px;
    background:linear-gradient(90deg, transparent, var(--h360-blue), var(--h360-cyan), var(--h360-green), transparent);
    opacity:.95;
  }

  #mainNavbar.navbar-saas.scrolled{
    box-shadow:0 18px 48px rgba(0,0,0,.38) !important;
    border-bottom-color:rgba(0,212,255,.32) !important;
  }

  #mainNavbar .navbar-shell{
    position:relative;
    z-index:2;
    width:100%;
    display:flex;
    align-items:center;
    padding-left:18px;
    padding-right:18px;
  }

  .brand-360{
    display:flex;
    align-items:center;
    gap:12px;
    text-decoration:none !important;
    min-width:0;
  }

  .brand-logo-box{
    width:154px;
    height:52px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    background:rgba(255,255,255,.075);
    border:1px solid rgba(255,255,255,.16);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,.14),
      0 12px 28px rgba(0,0,0,.22),
      0 0 28px rgba(0,212,255,.12);
    overflow:hidden;
    flex:0 0 auto;
  }

  .brand-logo-box img{
    width:100%;
    height:100%;
    object-fit:contain;
    display:block;
    filter:drop-shadow(0 8px 16px rgba(0,0,0,.28));
  }

  .brand-copy{
    display:flex;
    flex-direction:column;
    line-height:1.1;
    min-width:0;
  }

  .brand-copy strong{
    color:#fff;
    font-size:.94rem;
    font-weight:950;
    letter-spacing:-.25px;
    white-space:nowrap;
  }

  .brand-copy span{
    color:rgba(234,246,255,.72);
    font-size:.72rem;
    font-weight:850;
    text-transform:uppercase;
    letter-spacing:.65px;
    white-space:nowrap;
  }

  #mainNavbar .navbar-toggler{
    border:1px solid rgba(148,210,255,.26) !important;
    border-radius:16px !important;
    padding:.48rem .58rem !important;
    background:rgba(255,255,255,.09) !important;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.12);
  }

  #mainNavbar .navbar-toggler:focus{
    box-shadow:0 0 0 .22rem rgba(0,212,255,.16) !important;
  }

  .hamburger{
    width:28px;
    height:22px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
  }

  .hamburger span{
    height:3px;
    border-radius:999px;
    background:linear-gradient(90deg, var(--h360-cyan), var(--h360-green));
    display:block;
  }

  #mainNavbar .navbar-collapse{
    position:relative;
    z-index:3;
  }

  #mainNavbar .nav-link{
    color:rgba(234,246,255,.88) !important;
    font-weight:900;
    border-radius:16px;
    padding:.58rem .86rem !important;
    transition:background .18s ease, color .18s ease, transform .18s ease;
  }

  #mainNavbar .nav-link:hover,
  #mainNavbar .nav-link:focus{
    color:#fff !important;
    background:rgba(255,255,255,.10);
    transform:translateY(-1px);
    outline:none;
  }

  .btn-registros{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:9px;
    min-height:44px;
    padding:.68rem 1.08rem;
    border-radius:17px;
    color:#061326 !important;
    font-weight:950;
    text-decoration:none !important;
    background:linear-gradient(135deg, var(--h360-cyan), var(--h360-green)) !important;
    border:1px solid rgba(255,255,255,.24);
    box-shadow:0 14px 32px rgba(0,212,255,.22);
    transition:transform .18s ease, filter .18s ease, box-shadow .18s ease;
    white-space:nowrap;
  }

  .btn-registros:hover{
    transform:translateY(-2px);
    filter:brightness(1.04);
    box-shadow:0 18px 42px rgba(48,230,177,.26);
  }

  .avatar{
    width:43px;
    height:43px;
    display:grid;
    place-items:center;
    border-radius:16px;
    color:#061326;
    background:linear-gradient(135deg, #ffffff, #c9faff);
    border:1px solid rgba(255,255,255,.28);
    box-shadow:0 12px 28px rgba(0,212,255,.16);
    flex-shrink:0;
  }

  .avatar-lg{
    width:54px;
    height:54px;
    border-radius:19px;
  }

  .user-link{
    padding:.48rem .7rem !important;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(148,210,255,.18);
  }

  .dropdown-menu{
    z-index:2000 !important;
  }

  .dropdown-pro{
    min-width:315px;
    overflow:hidden;
    border-radius:22px;
    background:#fff;
    border:1px solid rgba(15,23,42,.08);
    box-shadow:0 24px 70px rgba(0,0,0,.22);
    padding:.55rem;
  }

  .dropdown-pro::before{
    content:"";
    display:block;
    height:4px;
    margin:-.55rem -.55rem .55rem;
    background:linear-gradient(90deg, var(--h360-blue), var(--h360-cyan), var(--h360-green));
  }

  .dropdown-pro .dropdown-item{
    border-radius:15px;
    padding:.75rem .85rem;
    font-weight:850;
    color:#0f172a;
  }

  .dropdown-pro .dropdown-item:hover{
    background:rgba(0,212,255,.09);
  }

  .dropdown-pro .text-danger{
    color:#dc2626 !important;
  }

  .mobile-panel{
    margin-top:14px;
    padding:14px;
    border-radius:24px;
    background:rgba(255,255,255,.10);
    border:1px solid rgba(148,210,255,.22);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.10);
  }

  .mobile-user{
    display:flex;
    align-items:center;
    gap:14px;
    padding:14px;
    margin-bottom:12px;
    border-radius:20px;
    background:rgba(255,255,255,.12);
    border:1px solid rgba(255,255,255,.16);
  }

  .mobile-link{
    display:flex;
    align-items:center;
    gap:14px;
    width:100%;
    padding:14px;
    border-radius:18px;
    margin-bottom:12px;
    color:#fff !important;
    font-weight:950;
    text-decoration:none !important;
    border:1px solid rgba(255,255,255,.18);
    transition:transform .16s ease, filter .16s ease;
  }

  .mobile-link:hover{
    transform:translateY(-1px);
    filter:brightness(1.04);
  }

  .mobile-link i:first-child{
    width:22px;
    text-align:center;
  }

  .mobile-link.primary{
    color:#061326 !important;
    background:linear-gradient(135deg, var(--h360-cyan), var(--h360-green));
    box-shadow:0 14px 34px rgba(0,212,255,.22);
  }

  .mobile-link.profile{
    background:linear-gradient(135deg, rgba(35,120,255,.95), rgba(0,212,255,.80));
    box-shadow:0 14px 34px rgba(35,120,255,.22);
  }

  .mobile-link.danger{
    background:linear-gradient(135deg, #ef4444, #991b1b);
    box-shadow:0 14px 34px rgba(239,68,68,.20);
  }

  @media (max-width:991.98px){
    #mainNavbar.navbar-saas{
      padding:.52rem 0 !important;
    }

    #mainNavbar .navbar-shell{
      padding-left:12px;
      padding-right:12px;
    }

    .brand-logo-box{
      width:142px;
      height:50px;
      border-radius:17px;
    }

    .brand-copy{
      display:none;
    }

    #navbarCollapse{
      max-height:calc(100vh - 82px);
      overflow-y:auto;
    }
  }

  @media (max-width:576px){
    .brand-logo-box{
      width:128px;
      height:46px;
      padding:5px 8px;
      border-radius:16px;
    }

    #mainNavbar .navbar-toggler{
      border-radius:14px !important;
    }

    .mobile-user{
      padding:12px;
    }

    .mobile-link{
      padding:13px;
      margin-bottom:10px;
    }
  }
</style>

<div class="container-fluid p-0">
  <nav class="navbar navbar-expand-lg fixed-top navbar-saas" id="mainNavbar" aria-label="Menú principal">
    <div class="navbar-shell">

      <!-- LOGO -->
      <a href="dash_responder.php" class="brand-360" aria-label="Ir al inicio">
        <span class="brand-logo-box">
          <img src="<?= htmlspecialchars($logoHeader360) ?>" alt="360 Estadísticas">
        </span>

        <span class="brand-copy">
          <strong>360 Estadísticas</strong>
          <span>Panel ciudadano</span>
        </span>
      </a>

      <!-- TOGGLER -->
      <button class="navbar-toggler ms-auto" type="button"
              data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
              aria-controls="navbarCollapse" aria-expanded="false"
              aria-label="Abrir/Cerrar menú">
        <span class="hamburger" aria-hidden="true">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>

      <div class="collapse navbar-collapse" id="navbarCollapse">

        <!-- DESKTOP -->
        <div class="navbar-nav ms-auto d-none d-lg-flex align-items-center gap-2">

          <?php if ($puedeVerRegistros): ?>
            <a href="resultado.php" class="btn-registros">
              <i class="fas fa-chart-bar"></i>
              Ver resultados
            </a>
          <?php endif; ?>

          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle user-link d-flex align-items-center gap-2"
               href="#"
               id="userDropdown"
               role="button"
               data-bs-toggle="dropdown"
               data-bs-auto-close="outside"
               aria-expanded="false">
              <span class="avatar">
                <i class="fas fa-user"></i>
              </span>

              <div class="lh-sm text-start">
                <div class="fw-bold text-white" style="font-size:.92rem;">
                  <?= htmlspecialchars($nombreCorto) ?>
                </div>
                <div class="text-white-50" style="font-size:.75rem;">
                  Mi cuenta
                </div>
              </div>
            </a>

            <ul class="dropdown-menu dropdown-menu-end dropdown-pro" aria-labelledby="userDropdown">
              <li class="px-3 pt-3 pb-2">
                <div class="d-flex align-items-center gap-3">
                  <span class="avatar avatar-lg">
                    <i class="fas fa-user"></i>
                  </span>

                  <div class="lh-sm">
                    <div class="fw-bold text-dark">
                      <?= htmlspecialchars($nombreCompleto) ?>
                    </div>
                    <div class="text-muted" style="font-size:.85rem;">
                      Acceso autorizado
                    </div>
                  </div>
                </div>
              </li>

              <li><hr class="dropdown-divider my-2"></li>

              <li>
                <a class="dropdown-item d-flex align-items-center gap-2"
                   href="#"
                   onclick="PERFIL.loadProfile(<?= $userId ?>); return false;">
                  <i class="fas fa-user"></i>
                  Mi perfil
                </a>
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center gap-2"
                   href="dash_responder.php">
                  <i class="fas fa-list-check"></i>
                  Mis formularios
                </a>
              </li>

              <?php if ($puedeVerRegistros): ?>
                <li>
                  <a class="dropdown-item d-flex align-items-center gap-2"
                     href="resultado.php">
                    <i class="fas fa-chart-bar"></i>
                    Ver resultados
                  </a>
                </li>
              <?php endif; ?>

              <li><hr class="dropdown-divider my-2"></li>

              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 text-danger fw-bold"
                   href="logout.php">
                  <i class="fas fa-sign-out-alt"></i>
                  Cerrar sesión
                </a>
              </li>
            </ul>
          </div>
        </div>

        <!-- MOBILE / TABLET -->
        <div class="navbar-nav d-lg-none w-100 mobile-panel">

          <div class="mobile-user">
            <span class="avatar avatar-lg">
              <i class="fas fa-user"></i>
            </span>

            <div class="flex-grow-1">
              <div class="fw-bold text-white">
                <?= htmlspecialchars($nombreCorto) ?>
              </div>
              <div class="text-white-50" style="font-size:.85rem;">
                Menú de usuario
              </div>
            </div>
          </div>

          <a href="dash_responder.php" class="mobile-link profile">
            <i class="fas fa-list-check"></i>
            <span>Mis formularios</span>
            <i class="fas fa-chevron-right ms-auto"></i>
          </a>

          <?php if ($puedeVerRegistros): ?>
            <a href="resultado.php" class="mobile-link primary">
              <i class="fas fa-chart-bar"></i>
              <span>Ver resultados</span>
              <i class="fas fa-chevron-right ms-auto"></i>
            </a>
          <?php endif; ?>

          <a href="#" class="mobile-link profile"
             onclick="PERFIL.loadProfile(<?= $userId ?>); return false;">
            <i class="fas fa-user"></i>
            <span>Mi perfil</span>
            <i class="fas fa-chevron-right ms-auto"></i>
          </a>

          <a href="logout.php" class="mobile-link danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar sesión</span>
            <i class="fas fa-chevron-right ms-auto"></i>
          </a>

        </div>
      </div>

    </div>
  </nav>
</div>

<script>
  function initNavbarUserDropdown() {
    const toggle = document.getElementById("userDropdown");
    if (!toggle) return;

    const menu = toggle.parentElement
      ? toggle.parentElement.querySelector(".dropdown-menu")
      : null;

    if (!menu) return;

    if (typeof bootstrap !== "undefined" && bootstrap.Dropdown) {
      bootstrap.Dropdown.getOrCreateInstance(toggle);
      return;
    }

    if (toggle.dataset.dropdownFallbackInit === "1") return;
    toggle.dataset.dropdownFallbackInit = "1";

    toggle.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();

      const expanded = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", expanded ? "false" : "true");
      toggle.classList.toggle("show", !expanded);
      menu.classList.toggle("show", !expanded);
    });

    menu.addEventListener("click", function(e) {
      e.stopPropagation();
    });

    document.addEventListener("click", function() {
      toggle.setAttribute("aria-expanded", "false");
      toggle.classList.remove("show");
      menu.classList.remove("show");
    });
  }

  document.addEventListener("scroll", function() {
    const nav = document.getElementById("mainNavbar");
    if (!nav) return;
    nav.classList.toggle("scrolled", window.scrollY > 10);
  });

  function aplicarOffsetNavbar() {
    const nav = document.getElementById("mainNavbar");
    if (!nav) return;

    document.body.style.paddingTop = nav.offsetHeight + "px";
    document.documentElement.style.setProperty("--altura-menu", nav.offsetHeight + "px");
  }

  document.addEventListener("DOMContentLoaded", aplicarOffsetNavbar);
  window.addEventListener("resize", aplicarOffsetNavbar);
  window.addEventListener("load", initNavbarUserDropdown);

  document.addEventListener("DOMContentLoaded", function() {
    const navbarCollapse = document.getElementById("navbarCollapse");

    initNavbarUserDropdown();

    if (!navbarCollapse || typeof bootstrap === "undefined") return;

    navbarCollapse.querySelectorAll("a.mobile-link").forEach(function(link) {
      link.addEventListener("click", function() {
        if (window.innerWidth < 992) {
          const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse)
            || new bootstrap.Collapse(navbarCollapse, { toggle: false });

          bsCollapse.hide();
        }
      });
    });
  });
</script>
