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
   NAVBAR VERTICAL PRO MAX SAAS (COPIAR Y PEGAR)
   - Fondo azul #20427F
   - Letras blancas
   - Hover: fondo blanco + letra azul
   - Activo: pill + barra izquierda
   - Submenú: tarjeta pro
   - Responsive: móvil / tablet / PC
   - CERO NEGRILLAS
========================================================== */

:root{
  --nav-blue: #20427F;
  --nav-blue-2: #132b52;
  --nav-blue-soft: rgba(32,66,127,.14);
  --nav-white: #ffffff;
  --nav-white-soft: rgba(255,255,255,.12);
  --nav-white-softer: rgba(255,255,255,.08);
  --nav-border: rgba(255,255,255,.18);
  --nav-shadow: 0 18px 45px rgba(2,6,23,.25);
  --nav-radius: 18px;
  --nav-radius-sm: 14px;
}

/* =============== CONTENEDOR NAVBAR =============== */
.navbar.navbar-vertical{
  background: linear-gradient(180deg, var(--nav-blue-2), var(--nav-blue)) !important;
  box-shadow: var(--nav-shadow);
  border-right: 1px solid rgba(255,255,255,.12);
  font-family: "IBM Plex Sans", sans-serif !important;
}

/* Todo el navbar sin negrilla */
.navbar.navbar-vertical,
.navbar.navbar-vertical *{
  font-family: "IBM Plex Sans", sans-serif !important;
  font-weight: 400 !important;
}

/* scroll suave */
.navbar-vertical-content{
  padding: 14px 12px 18px !important;
}

/* =============== LABELS (TITULOS) =============== */
.navbar-vertical-label{
  color: rgba(255,255,255,.92) !important;
  text-transform: uppercase;
  letter-spacing: 0,1px;
  font-size: 10px;
  margin: 12px 12px 5px !important;
  font-weight: 300 !important; /* NO negrilla */
}

.navbar-vertical-line{
  border-color: rgba(255,255,255,.16) !important;
  margin: 0 12px 10px !important;
  opacity: 1;
}

/* =============== LINKS PRINCIPALES =============== */
.navbar-vertical .nav-item-wrapper{
  margin: 6px 8px;
}

.navbar-vertical .nav-link{
  color: rgba(255,255,255,.95) !important;
  border-radius: 16px !important;
  padding: 10px 12px !important;
  position: relative;
  overflow: hidden;
  background: transparent;
  transition: transform .18s ease, background .22s ease, box-shadow .22s ease;
}

/* brillo suave interno */
.navbar-vertical .nav-link::before{
  content:"";
  position:absolute;
  inset:0;
  background: radial-gradient(circle at 10% 0%, rgba(255,255,255,.16), transparent 55%);
  opacity: 0;
  transition: opacity .25s ease;
}

.navbar-vertical .nav-link:hover::before{
  opacity: 1;
}

/* Icono */
.navbar-vertical .nav-link-icon,
.navbar-vertical .nav-link-icon span,
.navbar-vertical .nav-link-icon svg{
  color: rgba(255,255,255,.95) !important;
  opacity: .95;
  transition: transform .18s ease, opacity .18s ease, color .18s ease;
}

/* Texto */
.navbar-vertical .nav-link-text{
  color: rgba(255,255,255,.96) !important;
  font-size: 12px;
  letter-spacing: .50px;
  line-height: 1.2;
  transition: color .18s ease;
}

/* =============== HOVER PRO MAX =============== */
.navbar-vertical .nav-link:hover{
  background: var(--nav-white) !important;
  transform: translateX(4px);
  box-shadow: inset 0 0 0 1px rgba(32,66,127,.25);
}

.navbar-vertical .nav-link:hover .nav-link-text{
  color: var(--nav-blue) !important;
}

.navbar-vertical .nav-link:hover .nav-link-icon,
.navbar-vertical .nav-link:hover .nav-link-icon span,
.navbar-vertical .nav-link:hover svg{
  color: var(--nav-blue) !important;
  opacity: 1;
  transform: scale(1.05);
}

/* =============== ACTIVO (SIN NEGRILLA) =============== */
.navbar-vertical .nav-link.active{
  background: linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.08)) !important;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
}

/* barra izquierda del activo */
.navbar-vertical .nav-link.active::after{
  content:"";
  position:absolute;
  left: 8px;
  top: 50%;
  width: 4px;
  height: 62%;
  transform: translateY(-50%);
  border-radius: 99px;
  background: rgba(255,255,255,.85);
}

/* =============== INDICADOR (caret) =============== */
.navbar-vertical .dropdown-indicator-icon{
  color: rgba(255,255,255,.85) !important;
  transition: transform .25s ease, color .2s ease;
}

.navbar-vertical .nav-link[aria-expanded="true"] .dropdown-indicator-icon{
  transform: rotate(90deg);
}

/* =============== SUBMENÚS (TARJETA PRO) =============== */
.navbar-vertical .parent-wrapper .parent{
  margin: 8px 8px 10px;
  padding: 10px 10px;
  border-radius: 18px;
  background: linear-gradient(180deg, rgba(255,255,255,.10), rgba(255,255,255,.05));
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
}

/* items submenú */
.navbar-vertical .parent-wrapper .parent .nav-link{
  padding: 8px 10px !important;
  border-radius: 12px !important;
  margin: 4px 0;
}

/* bullet pro en submenú */
.navbar-vertical .parent-wrapper .parent .nav-link .nav-link-text{
  position: relative;
  padding-left: 14px;
}

.navbar-vertical .parent-wrapper .parent .nav-link .nav-link-text::before{
  content:"";
  position:absolute;
  left: 0;
  top: 50%;
  width: 6px;
  height: 6px;
  transform: translateY(-50%);
  border-radius: 99px;
  background: rgba(255,255,255,.65);
  transition: background .2s ease;
}

/* hover submenú -> fondo blanco + texto azul */
.navbar-vertical .parent-wrapper .parent .nav-link:hover{
  background: #ffffff !important;
  box-shadow: inset 0 0 0 1px rgba(32,66,127,.25);
  transform: translateX(3px);
}

.navbar-vertical .parent-wrapper .parent .nav-link:hover .nav-link-text{
  color: var(--nav-blue) !important;
}

.navbar-vertical .parent-wrapper .parent .nav-link:hover .nav-link-text::before{
  background: var(--nav-blue);
}

/* =============== FOOTER DEL NAVBAR =============== */
.navbar-vertical-footer{
  border-top: 1px solid rgba(255,255,255,.14);
  background: rgba(0,0,0,.10);
  padding: 10px 12px;
}

.navbar-vertical-footer .btn.navbar-vertical-toggle{
  background: rgba(255,255,255,.10) !important;
  border-radius: 14px !important;
  color: #fff !important;
  padding: 10px 12px !important;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
}

.navbar-vertical-footer .btn.navbar-vertical-toggle:hover{
  background: #fff !important;
  color: var(--nav-blue) !important;
}

/* =============== HAMBURGER BLANCA (para tu header) =============== */
.navbar-toggler-humburger-icon .navbar-toggle-icon{
  width: 42px;
  height: 42px;
  border-radius: 14px;
  background: rgba(255,255,255,.12);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.16);
  display:flex;
  align-items:center;
  justify-content:center;
}

.navbar-toggler-humburger-icon .toggle-line{
  background: #fff !important;
}

.navbar-toggler-humburger-icon .navbar-toggle-icon::before,
.navbar-toggler-humburger-icon .navbar-toggle-icon::after{
  background: #fff !important;
}

/* =============== RESPONSIVE =============== */
@media (max-width: 992px){
  .navbar-vertical-content{ padding: 12px 10px 16px !important; }
  .navbar-vertical .nav-link-text{ font-size: 14px; }
  .navbar-vertical-label{ font-size: 8px; }
}

@media (max-width: 576px){
  .navbar-vertical .nav-link{ padding: 10px 10px !important; }
  .navbar-vertical .nav-link-text{ font-size: 13.5px; }
  .navbar-vertical .parent-wrapper .parent{ padding: 10px; }
}

/* =============== ANTI-BOLD GLOBAL SOLO EN NAVBAR =============== */
.navbar-vertical b,
.navbar-vertical strong,
.navbar-vertical .fw-bold,
.navbar-vertical .fw-semibold,
.navbar-vertical .fw-medium{
  font-weight: 400 !important;
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
               data-bs-toggle="collapse" aria-expanded="true" aria-controls="nv-home">
              <div class="d-flex align-items-center">
                <div class="dropdown-indicator-icon-wrapper">
                  <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                </div>
                <span class="nav-link-icon"><span data-feather="pie-chart"></span></span>
                <span class="nav-link-text">Dashboard</span>
              </div>
            </a>

            <div class="parent-wrapper label-1">
              <ul class="nav collapse parent show" data-bs-parent="#navbarVerticalCollapse" id="nv-home">
                <li class="collapsed-nav-item-title d-none">Inicio</li>

                <?php if ($isAdmin): ?>
                  <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                      <div class="d-flex align-items-center">
                        <span class="nav-link-text">Dashboard</span>
                      </div>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if ($isEncuestador): ?>
                  <li class="nav-item">
                    <a class="nav-link active" href="votantes_encuestador.php">
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

</nav>
