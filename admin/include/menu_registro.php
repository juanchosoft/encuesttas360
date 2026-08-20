<style>
  :root{
    --h360-bg:#041020;
    --h360-bg-2:#061326;
    --h360-bg-3:#082342;
    --h360-blue:#2378ff;
    --h360-cyan:#00d4ff;
    --h360-green:#30e6b1;
    --h360-white:#ffffff;
    --h360-text:#eaf6ff;
    --h360-muted:#8fb4d6;
    --h360-stroke:rgba(148,210,255,.22);
    --h360-shadow:0 14px 42px rgba(0,0,0,.30);
  }

  #mainNavbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:1050;
    padding:.55rem 0 !important;
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

  #mainNavbar::before{
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

  #mainNavbar::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    bottom:-1px;
    height:2px;
    background:linear-gradient(90deg, transparent, var(--h360-blue), var(--h360-cyan), var(--h360-green), transparent);
    opacity:.95;
  }

  #mainNavbar.scrolled{
    box-shadow:0 18px 48px rgba(0,0,0,.38) !important;
    border-bottom-color:rgba(0,212,255,.32) !important;
  }

  #mainNavbar .navbar-inner{
    position:relative;
    z-index:2;
    width:100%;
    display:flex;
    align-items:center;
    padding-left:18px;
    padding-right:18px;
  }

  .navbar-brand{
    padding:0 !important;
    margin:0 !important;
    display:flex;
    align-items:center;
    min-width:0;
    text-decoration:none !important;
  }

  .brand-pill{
    display:flex;
    align-items:center;
    gap:12px;
    min-width:0;
  }

  .brand-logo-box{
    width:152px;
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

  .logo-chip{
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

  #mainNavbar .navbar-nav{
    align-items:center;
    gap:6px;
  }

  #mainNavbar .nav-link{
    position:relative;
    display:flex;
    align-items:center;
    gap:8px;
    color:rgba(234,246,255,.88) !important;
    font-weight:900;
    padding:.62rem .92rem !important;
    border-radius:16px;
    transition:background .18s ease, color .18s ease, transform .18s ease;
  }

  #mainNavbar .nav-link i{
    color:var(--h360-green);
    font-size:.9rem;
  }

  #mainNavbar .nav-link:hover,
  #mainNavbar .nav-link:focus{
    color:#fff !important;
    background:rgba(255,255,255,.10);
    transform:translateY(-1px);
    outline:none;
  }

  #mainNavbar .nav-link.active{
    color:#061326 !important;
    background:linear-gradient(135deg, var(--h360-cyan), var(--h360-green));
    box-shadow:0 12px 28px rgba(0,212,255,.20);
  }

  #mainNavbar .nav-link.active i{
    color:#061326;
  }

  .btn-menu-login{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:9px;
    min-height:42px;
    padding:.62rem 1rem;
    border-radius:16px;
    color:#061326 !important;
    font-weight:950;
    text-decoration:none !important;
    background:linear-gradient(135deg, var(--h360-cyan), var(--h360-green));
    border:1px solid rgba(255,255,255,.24);
    box-shadow:0 14px 32px rgba(0,212,255,.22);
    transition:transform .18s ease, filter .18s ease, box-shadow .18s ease;
    white-space:nowrap;
  }

  .btn-menu-login:hover{
    transform:translateY(-2px);
    filter:brightness(1.04);
    box-shadow:0 18px 42px rgba(48,230,177,.26);
  }

  @media (max-width:991.98px){
    #mainNavbar{
      padding:.52rem 0 !important;
    }

    #mainNavbar .navbar-inner{
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
      margin-top:12px;
      padding:14px;
      border-radius:24px;
      background:rgba(255,255,255,.10);
      border:1px solid rgba(148,210,255,.22);
      box-shadow:inset 0 1px 0 rgba(255,255,255,.10);
      max-height:calc(100vh - 82px);
      overflow-y:auto;
    }

    #mainNavbar .navbar-nav{
      align-items:stretch;
      gap:10px;
    }

    #mainNavbar .nav-link{
      width:100%;
      justify-content:space-between;
      padding:14px !important;
      border-radius:18px;
      background:rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.14);
    }

    #mainNavbar .nav-link::after{
      content:"\f054";
      font-family:"Font Awesome 6 Free";
      font-weight:900;
      font-size:.72rem;
      color:rgba(234,246,255,.65);
    }

    .btn-menu-login{
      width:100%;
      min-height:50px;
      margin-top:4px;
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
  }
</style>

<div class="container-fluid p-0">
  <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar" aria-label="Menú principal">
    <div class="navbar-inner">

      <!-- LOGO -->
      <a href="index.php" class="navbar-brand" aria-label="Ir al inicio">
        <div class="brand-pill">
          <span class="brand-logo-box">
            <img src="assets/img/360 Estadisticas-04.png" alt="360 Estadísticas" class="logo-chip">
          </span>

          <span class="brand-copy">
            <strong>360 Estadísticas</strong>
            <span>Participación ciudadana</span>
          </span>
        </div>
      </a>

      <!-- Toggler -->
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

      <!-- Menú -->
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-2 py-lg-0">

          <a href="index.php" class="nav-item nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            Inicio
          </a>

          <a href="nosotros.php" class="nav-item nav-link <?= basename($_SERVER['PHP_SELF']) === 'nosotros.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-info"></i>
            Quiénes somos
          </a>

          <a href="contacto.php" class="nav-item nav-link <?= basename($_SERVER['PHP_SELF']) === 'contacto.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope"></i>
            Contacto
          </a>

          <a href="registro.php" class="btn-menu-login">
            <i class="fa-solid fa-user-plus"></i>
            Participar
          </a>

        </div>
      </div>

    </div>
  </nav>
</div>

<script>
  document.addEventListener("scroll", function() {
    const nav = document.getElementById("mainNavbar");
    if (!nav) return;
    nav.classList.toggle("scrolled", window.scrollY > 10);
  });

  function aplicarOffsetNavbar() {
    const nav = document.getElementById("mainNavbar");
    if (!nav) return;
    document.body.style.paddingTop = nav.offsetHeight + "px";
  }

  document.addEventListener("DOMContentLoaded", aplicarOffsetNavbar);
  window.addEventListener("resize", aplicarOffsetNavbar);

  document.addEventListener("DOMContentLoaded", () => {
    const navbarCollapse = document.getElementById("navbarCollapse");
    if (!navbarCollapse || typeof bootstrap === "undefined") return;

    navbarCollapse.querySelectorAll("a.nav-link, a.btn-menu-login").forEach(a => {
      a.addEventListener("click", () => {
        if (window.innerWidth < 992) {
          const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse)
            || new bootstrap.Collapse(navbarCollapse, { toggle: false });

          bsCollapse.hide();
        }
      });
    });
  });
</script>