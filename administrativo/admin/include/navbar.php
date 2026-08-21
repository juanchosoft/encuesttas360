<?php
// Validaciones de tipo de usuario usando los métodos correctos de SessionData
$isAdmin        = SessionData::administrador();
$isInvestigador = SessionData::investigador();
$isVisor        = SessionData::visor();
$isOperativo    = SessionData::operativo();
$isEncuestador  = SessionData::encuestador();
$isCliente      = SessionData::cliente();

// Configuración
$secretariaPrincipal   = SessionData::getConfiguracionAplicacionSecretaria();
$departamentoPrincipal = SessionData::getConfiguracionAplicacionDepartamento();
?>

<style>
/* ==========================================================
   ESTADÍSTICA360 · MENÚ LATERAL PREMIUM CORREGIDO
   - NO empuja el contenido hacia abajo
   - Azul uniforme de arriba a abajo
   - Activo automático según URL
   - Submenús claramente diferenciados
   - Scroll interno
   - Responsive
========================================================== */

:root{
  --s360-menu-top-height: 4rem;
  --s360-menu-blue-950:#061a38;
  --s360-menu-blue-900:#092451;
  --s360-menu-blue-800:#0d3470;
  --s360-menu-blue-700:#174b98;
  --s360-menu-blue-500:#4388f3;
  --s360-menu-cyan:#43c4ed;
  --s360-menu-white:#ffffff;
  --s360-menu-text:#edf5ff;
  --s360-menu-muted:#9eb5d4;
  --s360-menu-border:rgba(255,255,255,.10);
  --s360-menu-hover:rgba(77,143,255,.17);
  --s360-menu-shadow:14px 0 38px rgba(5,20,48,.20);
}

/* ==========================================================
   MUY IMPORTANTE:
   En escritorio el menú queda FIJO y sale del flujo del documento.
   Así NO empuja .content hacia abajo.
========================================================== */
@media (min-width: 992px){
  .navbar.navbar-vertical{
    position:fixed !important;
    top:var(--phoenix-navbar-top-height, var(--s360-menu-top-height)) !important;
    bottom:0 !important;
    height:calc(100vh - var(--phoenix-navbar-top-height, var(--s360-menu-top-height))) !important;
    min-height:0 !important;
    margin:0 !important;
  }
}

/* ==========================================================
   BASE
========================================================== */
.navbar.navbar-vertical{
  z-index:1025 !important;
  display:flex !important;
  flex-direction:column !important;
  overflow:hidden !important;

  background:
    radial-gradient(260px 170px at 8% 0%, rgba(67,136,243,.26), transparent 68%),
    radial-gradient(260px 190px at 110% 55%, rgba(67,196,237,.11), transparent 70%),
    linear-gradient(
      180deg,
      var(--s360-menu-blue-950) 0%,
      var(--s360-menu-blue-900) 38%,
      var(--s360-menu-blue-800) 100%
    ) !important;

  border-right:1px solid rgba(255,255,255,.07) !important;
  box-shadow:var(--s360-menu-shadow) !important;

  font-family:"Inter","IBM Plex Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif !important;
}

/* Cuadrícula decorativa muy sutil */
.navbar.navbar-vertical::before{
  content:"";
  position:absolute;
  inset:0;
  pointer-events:none;
  opacity:.24;
  background-image:
    linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
  background-size:28px 28px;
  mask-image:linear-gradient(to bottom,#000,transparent 95%);
}

/* ==========================================================
   COLLAPSE + SCROLL
========================================================== */
.navbar.navbar-vertical > .navbar-collapse{
  position:relative;
  z-index:1;
  flex:1 1 auto !important;
  min-height:0 !important;
  overflow:hidden !important;
}

.navbar-vertical-content{
  height:100% !important;
  min-height:0 !important;
  overflow-y:auto !important;
  overflow-x:hidden !important;
  padding:13px 11px 20px !important;

  scrollbar-width:thin;
  scrollbar-color:rgba(255,255,255,.24) transparent;
}

.navbar-vertical-content::-webkit-scrollbar{
  width:6px;
}

.navbar-vertical-content::-webkit-scrollbar-track{
  background:transparent;
}

.navbar-vertical-content::-webkit-scrollbar-thumb{
  border-radius:999px;
  background:rgba(255,255,255,.22);
}

/* ==========================================================
   TÍTULOS DE GRUPO
========================================================== */
.navbar-vertical-label{
  display:flex !important;
  align-items:center;
  gap:7px;

  margin:17px 9px 7px !important;
  padding:0 !important;

  color:#91acd0 !important;
  font-size:.60rem !important;
  line-height:1.2 !important;
  font-weight:800 !important;
  letter-spacing:.90px !important;
  text-transform:uppercase;
}

.navbar-vertical-label::before{
  content:"";
  width:6px;
  height:6px;
  flex:0 0 6px;
  border-radius:50%;
  background:#55a2ff;
  box-shadow:
    0 0 0 4px rgba(85,162,255,.09),
    0 0 12px rgba(85,162,255,.42);
}

.navbar-vertical-line{
  height:1px !important;
  margin:0 9px 8px !important;
  border:0 !important;
  opacity:1 !important;
  background:
    linear-gradient(
      90deg,
      rgba(85,162,255,.28),
      rgba(255,255,255,.055),
      transparent
    ) !important;
}

/* ==========================================================
   WRAPPERS
========================================================== */
.navbar-vertical .nav-item-wrapper{
  margin:4px 3px !important;
}

/* ==========================================================
   LINKS PRINCIPALES
========================================================== */
.navbar-vertical .nav-link{
  position:relative;
  overflow:hidden;

  min-height:49px;
  display:flex !important;
  align-items:center;

  padding:7px 9px !important;

  border:1px solid transparent !important;
  border-radius:13px !important;

  color:var(--s360-menu-text) !important;
  background:transparent !important;

  text-decoration:none !important;

  transition:
    transform .20s cubic-bezier(.2,.75,.25,1),
    background .20s ease,
    border-color .20s ease,
    box-shadow .20s ease,
    color .20s ease !important;
}

.navbar-vertical .nav-link > .d-flex{
  width:100%;
  min-width:0;
  align-items:center !important;
}

/* hover */
.navbar-vertical .nav-link:hover{
  transform:translateX(3px);
  color:#fff !important;

  border-color:rgba(122,174,255,.17) !important;

  background:
    linear-gradient(
      135deg,
      rgba(67,136,243,.20),
      rgba(255,255,255,.065)
    ) !important;

  box-shadow:
    0 9px 22px rgba(0,0,0,.11),
    inset 3px 0 0 rgba(86,157,255,.72);
}

/* ==========================================================
   ICONOS
========================================================== */
.navbar-vertical .nav-link-icon{
  width:35px !important;
  height:35px !important;
  flex:0 0 35px !important;

  display:inline-flex !important;
  align-items:center !important;
  justify-content:center !important;

  margin:0 9px 0 0 !important;

  border:1px solid rgba(255,255,255,.08);
  border-radius:10px;

  color:#c6dbf7 !important;
  background:rgba(255,255,255,.075);

  transition:
    transform .20s ease,
    color .20s ease,
    background .20s ease,
    border-color .20s ease;
}

.navbar-vertical .nav-link-icon span,
.navbar-vertical .nav-link-icon svg{
  width:16px !important;
  height:16px !important;
  color:inherit !important;
  stroke:currentColor !important;
}

.navbar-vertical .nav-link:hover .nav-link-icon{
  transform:scale(1.05);
  color:#fff !important;
  background:rgba(67,136,243,.24);
  border-color:rgba(100,164,255,.30);
}

/* ==========================================================
   TEXTO
========================================================== */
.navbar-vertical .nav-link-text{
  min-width:0;
  color:inherit !important;

  font-size:.76rem !important;
  line-height:1.28 !important;
  font-weight:680 !important;
  letter-spacing:.02px;

  white-space:normal;
}

/* ==========================================================
   FLECHA DESPLEGABLE
========================================================== */
.navbar-vertical .dropdown-indicator-icon-wrapper{
  width:24px !important;
  height:34px !important;
  flex:0 0 24px !important;

  display:flex !important;
  align-items:center !important;
  justify-content:center !important;

  order:3;
  margin-left:auto !important;
}

.navbar-vertical .dropdown-indicator-icon{
  color:#8ca9cf !important;
  font-size:.66rem !important;

  transition:
    transform .25s cubic-bezier(.2,.75,.25,1),
    color .20s ease !important;
}

/* padre abierto */
.navbar-vertical .nav-link.dropdown-indicator[aria-expanded="true"]{
  color:#fff !important;

  border-color:rgba(86,157,255,.22) !important;

  background:
    linear-gradient(
      135deg,
      rgba(46,109,205,.30),
      rgba(255,255,255,.07)
    ) !important;

  box-shadow:
    inset 3px 0 0 #5b9cff,
    0 8px 20px rgba(0,0,0,.09);
}

.navbar-vertical .nav-link.dropdown-indicator[aria-expanded="true"] .dropdown-indicator-icon{
  transform:rotate(90deg);
  color:#fff !important;
}

/* ==========================================================
   SUBMENÚ
========================================================== */
.navbar-vertical .parent-wrapper .parent,
.navbar-vertical .collapse.parent > .nav{
  position:relative;

  margin:7px 2px 9px 17px !important;
  padding:7px !important;

  border:1px solid rgba(86,148,235,.15);
  border-radius:14px;

  background:
    radial-gradient(
      180px 90px at 0% 0%,
      rgba(67,136,243,.12),
      transparent 72%
    ),
    rgba(2,14,34,.26) !important;

  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.035),
    0 8px 18px rgba(0,0,0,.07);
}

/* línea azul del submenu */
.navbar-vertical .parent-wrapper .parent::before,
.navbar-vertical .collapse.parent > .nav::before{
  content:"";
  position:absolute;

  left:-9px;
  top:9px;
  bottom:9px;

  width:2px;
  border-radius:999px;

  background:
    linear-gradient(
      180deg,
      #5a9cff,
      rgba(90,156,255,.05)
    );
}

.navbar-vertical .parent-wrapper .parent .nav-item,
.navbar-vertical .collapse.parent > .nav .nav-item{
  margin:2px 0 !important;
}

.navbar-vertical .parent-wrapper .parent .nav-link,
.navbar-vertical .collapse.parent > .nav .nav-link{
  min-height:38px !important;
  padding:7px 8px !important;

  border-radius:9px !important;

  color:#c3d3e8 !important;
  background:transparent !important;

  box-shadow:none !important;
}

.navbar-vertical .parent-wrapper .parent .nav-link .nav-link-text,
.navbar-vertical .collapse.parent > .nav .nav-link .nav-link-text{
  position:relative;
  padding-left:14px;

  font-size:.70rem !important;
  font-weight:620 !important;
}

/* bullet */
.navbar-vertical .parent-wrapper .parent .nav-link .nav-link-text::before,
.navbar-vertical .collapse.parent > .nav .nav-link .nav-link-text::before{
  content:"";
  position:absolute;

  left:0;
  top:50%;

  width:5px;
  height:5px;

  transform:translateY(-50%);

  border-radius:50%;

  background:#6f8fae;

  box-shadow:0 0 0 3px rgba(111,143,174,.08);

  transition:
    transform .20s ease,
    background .20s ease,
    box-shadow .20s ease;
}

/* hover submenu */
.navbar-vertical .parent-wrapper .parent .nav-link:hover,
.navbar-vertical .collapse.parent > .nav .nav-link:hover{
  transform:translateX(2px);

  color:#fff !important;

  border-color:rgba(255,255,255,.07) !important;

  background:rgba(67,136,243,.16) !important;
}

.navbar-vertical .parent-wrapper .parent .nav-link:hover .nav-link-text::before,
.navbar-vertical .collapse.parent > .nav .nav-link:hover .nav-link-text::before{
  transform:translateY(-50%) scale(1.20);
  background:#6ba8ff;
  box-shadow:0 0 0 4px rgba(107,168,255,.11);
}

/* ==========================================================
   ACTIVO REAL DE LA PÁGINA
========================================================== */
.navbar-vertical .nav-link.active,
.navbar-vertical .nav-link.s360-current{
  transform:none !important;

  color:#0a336e !important;

  border-color:rgba(255,255,255,.75) !important;

  background:
    linear-gradient(
      135deg,
      #ffffff,
      #edf5ff
    ) !important;

  box-shadow:
    0 11px 28px rgba(0,0,0,.17),
    inset 0 0 0 1px rgba(255,255,255,.56) !important;
}

.navbar-vertical .nav-link.active::after,
.navbar-vertical .nav-link.s360-current::after{
  content:"";
  position:absolute;

  right:9px;
  top:50%;

  width:7px;
  height:7px;

  transform:translateY(-50%);

  border-radius:50%;

  background:#3478dc;

  box-shadow:
    0 0 0 5px rgba(52,120,220,.10),
    0 0 12px rgba(52,120,220,.25);
}

.navbar-vertical .nav-link.active .nav-link-text,
.navbar-vertical .nav-link.s360-current .nav-link-text{
  color:#0a336e !important;
  font-weight:800 !important;
}

.navbar-vertical .nav-link.active .nav-link-icon,
.navbar-vertical .nav-link.s360-current .nav-link-icon{
  color:#205da9 !important;
  background:#e5f0ff;
  border-color:#d7e6fa;
}

/* activo en submenu */
.navbar-vertical .parent-wrapper .parent .nav-link.active,
.navbar-vertical .parent-wrapper .parent .nav-link.s360-current,
.navbar-vertical .collapse.parent > .nav .nav-link.active,
.navbar-vertical .collapse.parent > .nav .nav-link.s360-current{
  color:#0a336e !important;
  background:#fff !important;
}

.navbar-vertical .parent-wrapper .parent .nav-link.active .nav-link-text::before,
.navbar-vertical .parent-wrapper .parent .nav-link.s360-current .nav-link-text::before,
.navbar-vertical .collapse.parent > .nav .nav-link.active .nav-link-text::before,
.navbar-vertical .collapse.parent > .nav .nav-link.s360-current .nav-link-text::before{
  background:#3478dc;
  box-shadow:0 0 0 4px rgba(52,120,220,.11);
}

/* ==========================================================
   ANIMACIÓN DEL COLLAPSE
========================================================== */
.navbar-vertical .collapsing{
  transition:
    height .27s cubic-bezier(.2,.75,.25,1) !important;
}

/* ==========================================================
   FOOTER
========================================================== */
.navbar-vertical-footer{
  position:relative;
  z-index:2;

  flex:0 0 auto !important;

  padding:9px 11px !important;

  border-top:1px solid rgba(255,255,255,.08) !important;

  background:
    linear-gradient(
      180deg,
      rgba(4,18,42,.38),
      rgba(2,12,30,.62)
    ) !important;

  backdrop-filter:blur(10px);
}

.navbar-vertical-footer .btn.navbar-vertical-toggle{
  min-height:43px;

  border:1px solid rgba(255,255,255,.10) !important;
  border-radius:12px !important;

  color:#dceafa !important;

  background:rgba(255,255,255,.07) !important;

  font-weight:700 !important;

  transition:
    transform .20s ease,
    background .20s ease,
    border-color .20s ease,
    color .20s ease;
}

.navbar-vertical-footer .btn.navbar-vertical-toggle:hover{
  transform:translateY(-1px);

  border-color:rgba(96,158,246,.22) !important;

  color:#fff !important;

  background:rgba(67,136,243,.18) !important;
}

.navbar-vertical-footer-text{
  font-size:.74rem !important;
  font-weight:700 !important;
}

/* ==========================================================
   HAMBURGER
========================================================== */
.navbar-toggler-humburger-icon .navbar-toggle-icon{
  width:42px;
  height:42px;

  display:flex;
  align-items:center;
  justify-content:center;

  border:1px solid rgba(255,255,255,.13);
  border-radius:13px;

  background:
    linear-gradient(
      135deg,
      #245fae,
      #123f82
    );

  box-shadow:0 9px 20px rgba(12,54,116,.20);
}

.navbar-toggler-humburger-icon .toggle-line,
.navbar-toggler-humburger-icon .navbar-toggle-icon::before,
.navbar-toggler-humburger-icon .navbar-toggle-icon::after{
  background:#fff !important;
}

/* ==========================================================
   TABLET / MOBILE
========================================================== */
@media (max-width:991.98px){
  .navbar.navbar-vertical{
    background:
      linear-gradient(
        180deg,
        var(--s360-menu-blue-950),
        var(--s360-menu-blue-800)
      ) !important;
  }

  .navbar-vertical-content{
    padding:12px 10px 18px !important;
  }

  .navbar-vertical .nav-link{
    min-height:49px;
  }

  .navbar-vertical .nav-link-text{
    font-size:.80rem !important;
  }

  .navbar-vertical .parent-wrapper .parent .nav-link .nav-link-text,
  .navbar-vertical .collapse.parent > .nav .nav-link .nav-link-text{
    font-size:.75rem !important;
  }
}

@media (max-width:575.98px){
  .navbar-vertical-content{
    padding:10px 8px 16px !important;
  }

  .navbar-vertical .nav-item-wrapper{
    margin:3px 1px !important;
  }

  .navbar-vertical .nav-link{
    min-height:46px;
    padding:7px 8px !important;
  }

  .navbar-vertical .nav-link-icon{
    width:32px !important;
    height:32px !important;
    flex-basis:32px !important;
  }

  .navbar-vertical .parent-wrapper .parent,
  .navbar-vertical .collapse.parent > .nav{
    margin-left:14px !important;
  }
}

/* ==========================================================
   ACCESIBILIDAD
========================================================== */
.navbar-vertical a:focus-visible,
.navbar-vertical button:focus-visible{
  outline:2px solid #78acff !important;
  outline-offset:2px;
}

@media (prefers-reduced-motion:reduce){
  .navbar-vertical *,
  .navbar-vertical *::before,
  .navbar-vertical *::after{
    animation-duration:.01ms !important;
    animation-iteration-count:1 !important;
    transition-duration:.01ms !important;
  }
}
</style>

<nav class="navbar navbar-vertical navbar-expand-lg">

  <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
    <div class="navbar-vertical-content">

      <ul class="navbar-nav flex-column" id="navbarVerticalNav">

        <!-- DASHBOARD -->
        <li class="nav-item">
          <div class="nav-item-wrapper">
            <a class="nav-link dropdown-indicator label-1" href="#nv-home" role="button"
               data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-home">
              <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon-wrapper">
                  <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                </div>
                <span class="nav-link-icon"><span data-feather="pie-chart"></span></span>
                <span class="nav-link-text">Dashboard</span>
              </div>
            </a>

            <div class="parent-wrapper label-1">
              <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-home">
                <li class="collapsed-nav-item-title d-none">Inicio</li>

                <?php if ($isAdmin): ?>
                  <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                      <div class="d-flex align-items-center">
                        <span class="nav-link-text">Dashboard</span>
                      </div>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if ($isEncuestador): ?>
                  <li class="nav-item">
                    <a class="nav-link" href="votantes_encuestador.php">
                      <div class="d-flex align-items-center">
                        <span class="nav-link-text">Votantes</span>
                      </div>
                    </a>
                  </li>
                <?php endif; ?>

              </ul>
            </div>
          </div>
        </li>

        <!-- CONFIG POLITICA -->
        <?php if ($isAdmin): ?>
          <p class="navbar-vertical-label">Configuración Política</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link dropdown-indicator label-1" href="#nv-e-politica"
               data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-politica">
              <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon-wrapper">
                  <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                </div>
                <span class="nav-link-icon"><span data-feather="users"></span></span>
                <span class="nav-link-text">Configuración Política</span>
              </div>
            </a>

            <div class="collapse parent" id="nv-e-politica">
              <ul class="nav flex-column ms-3">
                <li class="nav-item"><a class="nav-link" href="partidos_politicos.php"><span class="nav-link-text">Partidos políticos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="participantes.php"><span class="nav-link-text">Políticos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="votantes.php"><span class="nav-link-text">Votantes</span></a></li>
              </ul>
            </div>
          </div>
        <?php endif; ?>

        <!-- CONFIG ESTUDIOS -->
        <?php if ($isAdmin): ?>
          <p class="navbar-vertical-label">Configuración Estadísticas</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link dropdown-indicator label-1" href="#nv-e-estadisticas"
               data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-estadisticas">
              <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon-wrapper">
                  <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                </div>
                <span class="nav-link-icon"><span data-feather="bar-chart"></span></span>
                <span class="nav-link-text">Configuración Estudios</span>
              </div>
            </a>

            <div class="collapse parent" id="nv-e-estadisticas">
              <ul class="nav flex-column ms-3">
                <li class="nav-item"><a class="nav-link" href="espacio_geografico.php"><span class="nav-link-text">Espacio Geográfico</span></a></li>
                <li class="nav-item"><a class="nav-link" href="ficha_tecnica_encuesta.php"><span class="nav-link-text">Ficha Técnica Encuesta</span></a></li>
                <li class="nav-item"><a class="nav-link" href="sondeos.php"><span class="nav-link-text">Sondeos</span></a></li>
                <li class="nav-item"><a class="nav-link" href="preguntas_grilla.php"><span class="nav-link-text">Preguntas Grilla</span></a></li>
                <li class="nav-item"><a class="nav-link" href="grilla.php"><span class="nav-link-text">Grilla</span></a></li>
                <li class="nav-item"><a class="nav-link" href="formulas.php"><span class="nav-link-text">Fórmulas</span></a></li>
              </ul>
            </div>
          </div>
        <?php endif; ?>

        <!-- CONFIG CUESTIONARIOS -->
        <?php if ($isAdmin): ?>
          <p class="navbar-vertical-label">Configuración Encuestas</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link dropdown-indicator label-1" href="#nv-e-encuestas"
               data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-encuestas">
              <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon-wrapper">
                  <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                </div>
                <span class="nav-link-icon"><span data-feather="clipboard"></span></span>
                <span class="nav-link-text">Configuración Cuestionarios</span>
              </div>
            </a>

            <div class="collapse parent" id="nv-e-encuestas">
              <ul class="nav flex-column ms-3">
                <li class="nav-item"><a class="nav-link" href="preguntas.php"><span class="nav-link-text">Cuestionario</span></a></li>
                <li class="nav-item"><a class="nav-link" href="contestar_cuestionario.php"><span class="nav-link-text">Contestar Cuestionario</span></a></li>
                <li class="nav-item"><a class="nav-link" href="resultados_cuestionarios.php"><span class="nav-link-text">Resultados Cuestionarios</span></a></li>
              </ul>
            </div>
          </div>
        <?php endif; ?>

        <!-- ANALISIS -->
        <?php if ($isAdmin || SessionData::getPermission(50)): ?>
          <p class="navbar-vertical-label">Análisis Electoral</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link label-1" href="analisis_estudio.php">
              <div class="d-flex align-items-center">
                <span class="nav-link-icon"><span data-feather="trending-up"></span></span>
                <span class="nav-link-text">Análisis de Estudio</span>
              </div>
            </a>
          </div>
        <?php endif; ?>

        <!-- RESULTADOS SONDEOS -->
        <?php if ($isAdmin || SessionData::getPermission(90) || SessionData::getPermission(74)): ?>
          <p class="navbar-vertical-label">Dashboard Resultados</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link label-1" href="dashboard_resultados.php">
              <div class="d-flex align-items-center">
                <span class="nav-link-icon"><span data-feather="layout"></span></span>
                <span class="nav-link-text" style="font-weight:800;">Dashboard de Resultados</span>
              </div>
            </a>
          </div>

          <div class="nav-item-wrapper">
            <a class="nav-link label-1" href="resultados_sondeos.php">
              <div class="d-flex align-items-center">
                <span class="nav-link-icon"><span data-feather="bar-chart-2"></span></span>
                <span class="nav-link-text">Resultados de Sondeos</span>
              </div>
            </a>
          </div>
        <?php endif; ?>

        <!-- RESULTADOS ENCUESTAS -->
        <?php if ($isAdmin || SessionData::getPermission(90)): ?>
          <p class="navbar-vertical-label">Resultados Encuestas</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link label-1" href="certificaciones.php">
              <div class="d-flex align-items-center">
                <span class="nav-link-icon"><span data-feather="bar-chart-2"></span></span>
                <span class="nav-link-text">Resultados de Encuestas</span>
              </div>
            </a>
          </div>
        <?php endif; ?>

        <!-- CONFIG GENERAL -->
        <?php if ($isAdmin): ?>
          <p class="navbar-vertical-label">Configuración General</p>
          <hr class="navbar-vertical-line" />

          <div class="nav-item-wrapper">
            <a class="nav-link dropdown-indicator label-1" href="#nv-e-commerce"
               data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-commerce">
              <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon-wrapper">
                  <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                </div>
                <span class="nav-link-icon"><span data-feather="settings"></span></span>
                <span class="nav-link-text">Configuración General</span>
              </div>
            </a>

            <div class="collapse parent" id="nv-e-commerce">
              <ul class="nav flex-column ms-3">
                <li class="nav-item"><a class="nav-link" href="configuracion.php"><span class="nav-link-text">Configuración</span></a></li>
                <li class="nav-item"><a class="nav-link" href="usuarios.php"><span class="nav-link-text">Usuarios</span></a></li>
                <li class="nav-item"><a class="nav-link" href="clientes.php"><span class="nav-link-text">Clientes</span></a></li>
              </ul>
            </div>
          </div>
        <?php endif; ?>

      </ul>

    </div>
  </div>

  <div class="navbar-vertical-footer">
    <button class="btn navbar-vertical-toggle border-0 w-100 white-space-nowrap d-flex align-items-center">
      <span class="uil uil-left-arrow-to-left fs-8"></span>
      <span class="uil uil-arrow-from-right fs-8"></span>
      <span class="navbar-vertical-footer-text ms-2" style="font-size: 15px;">Cerrar Menú</span>
    </button>
  </div>


<script>
/* ==========================================================
   ESTADÍSTICA360 · CONTROL DE NAVEGACIÓN
   - Detecta automáticamente la página actual
   - Abre el submenu correspondiente
   - Evita que "Dashboard" quede activo siempre
   - Mantiene un solo submenu principal desplegado
========================================================== */
(function(){
  "use strict";

  function s360FileName(value){
    return (value || "")
      .split("?")[0]
      .split("#")[0]
      .replace(/\\/g,"/")
      .split("/")
      .pop()
      .trim()
      .toLowerCase();
  }

  function s360OpenCurrentRoute(){
    var current = s360FileName(window.location.pathname);

    if (!current) return;

    var links = document.querySelectorAll(
      "#navbarVerticalNav a.nav-link[href]"
    );

    links.forEach(function(link){
      link.classList.remove("active","s360-current");
      link.removeAttribute("aria-current");
    });

    links.forEach(function(link){
      var href = link.getAttribute("href") || "";

      if (!href || href.charAt(0) === "#") return;

      var target = s360FileName(href);

      if (target && target === current){
        link.classList.add("s360-current");
        link.setAttribute("aria-current","page");

        var collapse = link.closest(".collapse");

        if (collapse && collapse.id){
          collapse.classList.add("show");

          var trigger = document.querySelector(
            '#navbarVerticalNav a[href="#' + collapse.id + '"]'
          );

          if (trigger){
            trigger.setAttribute("aria-expanded","true");
            trigger.classList.remove("collapsed");
          }
        }
      }
    });
  }

  function s360AccordionBehavior(){
    var nav = document.getElementById("navbarVerticalNav");
    if (!nav) return;

    var collapses = nav.querySelectorAll(".collapse.parent");

    collapses.forEach(function(collapse){
      collapse.addEventListener("show.bs.collapse", function(){
        collapses.forEach(function(other){
          if (other === collapse || !other.classList.contains("show")) return;

          try{
            if (window.bootstrap && bootstrap.Collapse){
              var instance = bootstrap.Collapse.getOrCreateInstance(
                other,
                {toggle:false}
              );
              instance.hide();
            }else{
              other.classList.remove("show");
            }
          }catch(e){
            other.classList.remove("show");
          }
        });
      });

      collapse.addEventListener("shown.bs.collapse", function(){
        var trigger = document.querySelector(
          '#navbarVerticalNav a[href="#' + collapse.id + '"]'
        );

        if (trigger){
          trigger.setAttribute("aria-expanded","true");
        }
      });

      collapse.addEventListener("hidden.bs.collapse", function(){
        var trigger = document.querySelector(
          '#navbarVerticalNav a[href="#' + collapse.id + '"]'
        );

        if (trigger){
          trigger.setAttribute("aria-expanded","false");
        }
      });
    });
  }

  function s360ScrollActiveIntoView(){
    var current = document.querySelector(
      "#navbarVerticalNav .s360-current"
    );

    if (!current) return;

    setTimeout(function(){
      try{
        current.scrollIntoView({
          behavior:"smooth",
          block:"center"
        });
      }catch(e){}
    },180);
  }

  document.addEventListener("DOMContentLoaded", function(){
    s360OpenCurrentRoute();
    s360AccordionBehavior();
    s360ScrollActiveIntoView();
  });
})();
</script>

</nav>
