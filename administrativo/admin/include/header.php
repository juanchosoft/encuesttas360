<style>
/* ==========================================================
   HEADER SAAS PRO (TOP NAVBAR) - SIN RAYA AZUL + SIN DOBLE ESPACIO
   SOLO afecta: #navbarDefault y el offset superior global
========================================================== */

:root{
  --nav-blue:#20427F;
  --nav-blue-2:#132b52;
  --nav-blue-3:#2e58a8;

  --header-h: 72px;       /* desktop */
  --header-h-md: 66px;    /* tablet */
  --header-h-sm: 62px;    /* mobile */

  --shadow: 0 14px 30px rgba(2,6,23,.25);
  --font-saas: "IBM Plex Sans", sans-serif;
}

/* ===== NAVBAR TOP ===== */
#navbarDefault.navbar{
  position: fixed !important;
  top: 0; left: 0; right: 0;
  height: var(--header-h);
  min-height: var(--header-h);
  z-index: 1040;
  font-family: var(--font-saas) !important;

  background: linear-gradient(135deg, #242f36 0%, #2d3942 55%, #1f2a33 100%) !important;
  border-bottom: 1px solid rgba(255,255,255,.10) !important;
  box-shadow: var(--shadow);
  padding: 0 14px !important;

  display:flex;
  align-items:center;
}

/* Evita “líneas” raras del tema */
#navbarDefault::before,
#navbarDefault::after{
  content: none !important;
}

/* row layout */
#navbarDefault .navbar-collapse{
  height: var(--header-h);
  align-items:center !important;
}

/* Logo */
#navbarDefault .navbar-brand{
  padding: 0 !important;
  display:flex;
  align-items:center;
}

#navbarDefault #logoGobierno{
  width: 175px;
  height: auto;
  margin-left: 10px !important;
  filter: drop-shadow(0 10px 22px rgba(0,0,0,.35));
}

/* ===== BOTÓN HAMBURGER (BLANCO) ===== */
#navbarDefault .btn.navbar-toggler,
#navbarDefault .btn.navbar-toggler-humburger-icon{
  width: 48px !important;
  height: 48px !important;
  border-radius: 16px !important;

  background: rgba(255,255,255,.08) !important;
  border: 1px solid rgba(255,255,255,.18) !important;

  display:flex !important;
  align-items:center !important;
  justify-content:center !important;

  padding: 0 !important;
  margin: 0 !important;
  box-shadow: 0 10px 25px rgba(0,0,0,.22);
  transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
}

#navbarDefault .btn.navbar-toggler:hover,
#navbarDefault .btn.navbar-toggler-humburger-icon:hover{
  background: rgba(255,255,255,.14) !important;
  transform: translateY(-1px);
  box-shadow: 0 16px 35px rgba(0,0,0,.28);
}

#navbarDefault .btn.navbar-toggler:focus,
#navbarDefault .btn.navbar-toggler-humburger-icon:focus{
  outline: none !important;
  box-shadow: 0 0 0 4px rgba(46,88,168,.35), 0 16px 35px rgba(0,0,0,.28) !important;
}

/* ===== ICONO HAMBURGER ===== */
#navbarDefault .navbar-toggle-icon{
  position: relative !important;
  width: 22px !important;
  height: 16px !important;
  display:block !important;
}

#navbarDefault .navbar-toggle-icon::before,
#navbarDefault .navbar-toggle-icon::after,
#navbarDefault .navbar-toggle-icon .toggle-line{
  content:"";
  position:absolute !important;
  left: 0 !important;
  width: 100% !important;
  height: 2px !important;
  border-radius: 999px !important;
  background: #ffffff !important;
  opacity: 1 !important;
  box-shadow: 0 1px 0 rgba(0,0,0,.18);
  transition: transform .20s ease, top .20s ease, opacity .15s ease, width .20s ease;
}

#navbarDefault .navbar-toggle-icon::before{ top: 0px !important; }
#navbarDefault .navbar-toggle-icon .toggle-line{ top: 7px !important; }
#navbarDefault .navbar-toggle-icon::after{ top: 14px !important; }

#navbarDefault .btn[aria-expanded="true"] .navbar-toggle-icon::before{
  top: 7px !important;
  transform: rotate(45deg) !important;
}
#navbarDefault .btn[aria-expanded="true"] .navbar-toggle-icon .toggle-line{
  opacity: 0 !important;
  width: 0 !important;
}
#navbarDefault .btn[aria-expanded="true"] .navbar-toggle-icon::after{
  top: 7px !important;
  transform: rotate(-45deg) !important;
}

/* ===== ICONOS DERECHA ===== */
#navbarDefault .navbar-nav .nav-link{
  color: rgba(255,255,255,.92) !important;
  border-radius: 14px;
  transition: background .18s ease, transform .18s ease;
}
#navbarDefault .navbar-nav .nav-link:hover{
  background: rgba(255,255,255,.10);
  transform: translateY(-1px);
}

/* ===== SEARCH BOX ===== */
#navbarDefault .navbar-top-search-box .search-input{
  background: rgba(255,255,255,.10) !important;
  border: 1px solid rgba(255,255,255,.18) !important;
  color: #fff !important;
}
#navbarDefault .navbar-top-search-box .search-input::placeholder{
  color: rgba(255,255,255,.70) !important;
}

/* ==========================================================
   ✅ FIX REAL DEL ESPACIADO:
   1) Elimina la “raya azul”: ocultamos navbar-bottom-line
   2) Evita doble padding: SOLO el body maneja el offset
========================================================== */

/* ✅ ADIÓS raya azul en todas las vistas */
.navbar-bottom-line{
  display: none !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
}

/* ✅ SOLO UNA FUENTE DE OFFSET */
body{
  padding-top: var(--header-h) !important;
}

/* ✅ QUITA el padding-top que estabas duplicando */
main.main,
.content,
.pcoded-main-container,
.main-content,
#top{
  padding-top: 0 !important;
  margin-top: 0 !important;
}

/* Evita huecos extra en el primer card/titulo */
.content .container-fluid:first-child,
.content .container-fluid > .card:first-child,
.content > .mt-4:first-child{
  margin-top: 0 !important;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px){
  #navbarDefault.navbar{
    height: var(--header-h-md);
    min-height: var(--header-h-md);
    padding: 0 12px !important;
  }
  #navbarDefault .navbar-collapse{ height: var(--header-h-md); }
  #navbarDefault #logoGobierno{ width: 150px; margin-left: 8px !important; }
  body{ padding-top: var(--header-h-md) !important; }
}

@media (max-width: 576px){
  #navbarDefault.navbar{
    height: var(--header-h-sm);
    min-height: var(--header-h-sm);
    padding: 0 10px !important;
  }
  #navbarDefault .navbar-collapse{ height: var(--header-h-sm); }
  #navbarDefault #logoGobierno{ width: 128px; margin-left: 6px !important; }

  #navbarDefault .btn.navbar-toggler,
  #navbarDefault .btn.navbar-toggler-humburger-icon{
    width: 44px !important;
    height: 44px !important;
    border-radius: 15px !important;
  }

  body{ padding-top: var(--header-h-sm) !important; }
}
</style>


<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
        <div class="collapse navbar-collapse justify-content-between">
          <div class="navbar-logo">

        <button class="btn navbar-toggler navbar-toggler-humburger-icon"
                type="button"
                id="btnToggleSidebar"
                data-bs-toggle="collapse"
                data-bs-target="#navbarVerticalCollapse"
                aria-controls="navbarVerticalCollapse"
                aria-expanded="false"
                aria-label="Toggle Navigation">
          <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
        </button>


            <a class="navbar-brand me-1 me-sm-3" href="dashboard.php">
              <div class="d-flex align-items-center">
                <div class="d-flex align-items-center">
                  <img style="margin-left: 27px;" id="logoGobierno" src="assets/img/estadistica3.png" alt="Gobierno" width="180" />

                  <!-- <h5 class="logo-text ms-2 d-none d-sm-block">Gob360</h5> -->
                </div>
              </div>
            </a>
          </div>
          <div class="search-box navbar-top-search-box d-none d-lg-block" data-list='{"valueNames":["title"]}' style="width:25rem;">
            <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
              <input class="form-control search-input fuzzy-search rounded-pill form-control-sm" type="search" placeholder="Buscar..." aria-label="Search" />
              <span class="fas fa-search search-box-icon"></span>

            </form>
            <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none" data-bs-dismiss="search">
              <button class="btn btn-link p-0" aria-label="Close"></button>
            </div>
            <div class="dropdown-menu border start-0 py-0 overflow-hidden w-100">
              <div class="scrollbar-overlay" style="max-height: 30rem;">
              
                <div class="text-center">
                  <p class="fallback fw-bold fs-7 d-none">No Result Found.</p>
                </div>
              </div>
            </div>
          </div>
          <ul class="navbar-nav navbar-nav-icons flex-row">
            <li class="nav-item">
              <div class="theme-control-toggle fa-icon-wait px-2">
                <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Cambiar tema" style="height:32px;width:32px;"><span class="icon" data-feather="moon"></span></label>
                <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Cambiar tema" style="height:32px;width:32px;"><span class="icon" data-feather="sun"></span></label>
              </div>
            </li>
            <li class="nav-item d-lg-none"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span data-feather="search" style="height:19px;width:19px;margin-bottom: 2px;"></span></a></li>
            <!-- <li class="nav-item dropdown">
              <a class="nav-link" href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="d-block" style="height:20px;width:20px;"><span data-feather="bell" style="height:20px;width:20px;"></span></span></a> -->

              <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret" id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                <div class="card position-relative border-0">
                  <div class="card-header p-2">
                    <div class="d-flex justify-content-between">
                      <h5 class="text-body-emphasis mb-0">Notifications</h5>
                      <button class="btn btn-link p-0 fs-9 fw-normal" type="button">Mark all as read</button>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div class="scrollbar-overlay" style="height: 27rem;">
                      <div class="px-2 px-sm-3 py-3 notification-card position-relative read border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                          <div class="d-flex">
                            <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="assets/img/team/40x40/30.webp" alt="" />
                            </div>
                            <div class="flex-1 me-sm-3">
                              <h4 class="fs-9 text-body-emphasis">Jessie Samson</h4>
                              <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>💬</span>Mentioned you in a comment.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">10m</span></p>
                              <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:41 AM </span>August 7,2021</p>
                            </div>
                          </div>
                          <div class="dropdown notification-dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                            <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                          </div>
                        </div>
                      </div>
                      <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                          <div class="d-flex">
                            <div class="avatar avatar-m status-online me-3">
                              <div class="avatar-name rounded-circle"><span>J</span></div>
                            </div>
                            <div class="flex-1 me-sm-3">
                              <h4 class="fs-9 text-body-emphasis">Jane Foster</h4>
                              <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>📅</span>Created an event.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">20m</span></p>
                              <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:20 AM </span>August 7,2021</p>
                            </div>
                          </div>
                          <div class="dropdown notification-dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                            <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                          </div>
                        </div>
                      </div>
                      <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                          <div class="d-flex">
                            <div class="avatar avatar-m status-online me-3"><img class="rounded-circle avatar-placeholder" src="assets/img/team/40x40/avatar.webp" alt="" />
                            </div>
                            <div class="flex-1 me-sm-3">
                              <h4 class="fs-9 text-body-emphasis">Jessie Samson</h4>
                              <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>👍</span>Liked your comment.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">1h</span></p>
                              <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">9:30 AM </span>August 7,2021</p>
                            </div>
                          </div>
                          <div class="dropdown notification-dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                            <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                          </div>
                        </div>
                      </div>
                      <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                          <div class="d-flex">
                          <div class="avatar avatar-xl">
                        <?php
                          $img = !empty(SessionData::getFotoUsuario()) ? "assets/img/admin/" . htmlspecialchars(SessionData::getFotoUsuario()) : 'assets/img/santander.png';
                        ?>
                        <img class="rounded-circle" src="<?= $img ?>" alt="User-Profile-Image" />
                      </div>
                            <div class="flex-1 me-sm-3">
                              <h4 class="fs-9 text-body-emphasis">Kiera Anderson</h4>
                              <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>💬</span>Mentioned you in a comment.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10"></span></p>
                              <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">9:11 AM </span>August 7,2021</p>
                            </div>
                          </div>
                          <div class="dropdown notification-dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                            <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                          </div>
                        </div>
                      </div>
                      <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                          <div class="d-flex">
                            <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="assets/img/team/40x40/59.webp" alt="" />
                            </div>
                            <div class="flex-1 me-sm-3">
                              <h4 class="fs-9 text-body-emphasis">Herman Carter</h4>
                              <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>👤</span>Tagged you in a comment.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10"></span></p>
                              <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:58 PM </span>August 7,2021</p>
                            </div>
                          </div>
                          <div class="dropdown notification-dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                            <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                          </div>
                        </div>
                      </div>
                      <div class="px-2 px-sm-3 py-3 notification-card position-relative read ">
                        <div class="d-flex align-items-center justify-content-between position-relative">
                          <div class="d-flex">
                            <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="assets/img/team/40x40/58.webp" alt="" />
                            </div>
                            <div class="flex-1 me-sm-3">
                              <h4 class="fs-9 text-body-emphasis">Benjamin Button</h4>
                              <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span class='me-1 fs-10'>👍</span>Liked your comment.<span class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10"></span></p>
                              <p class="text-body-secondary fs-9 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:18 AM </span>August 7,2021</p>
                            </div>
                          </div>
                          <div class="dropdown notification-dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                            <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer p-0 border-top border-translucent border-0">
                    <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85"><a class="fw-bolder" href="pages/notifications.html">Notification history</a></div>
                  </div>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link" id="navbarDropdownNindeDots" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">
                <svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="2" cy="2" r="2" fill="currentColor"></circle>
                  <circle cx="2" cy="8" r="2" fill="currentColor"></circle>
                  <circle cx="2" cy="14" r="2" fill="currentColor"></circle>
                  <circle cx="8" cy="8" r="2" fill="currentColor"></circle>
                  <circle cx="8" cy="14" r="2" fill="currentColor"></circle>
                  <circle cx="14" cy="8" r="2" fill="currentColor"></circle>
                  <circle cx="14" cy="14" r="2" fill="currentColor"></circle>
                  <circle cx="8" cy="2" r="2" fill="currentColor"></circle>
                  <circle cx="14" cy="2" r="2" fill="currentColor"></circle>
                </svg></a>

              <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-nine-dots shadow border" aria-labelledby="navbarDropdownNindeDots">
                <div class="card bg-body-emphasis position-relative border-0">
                  <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height: 20rem;">
                    <div class="row text-center align-items-center gx-0 gy-0">
                      <div class="col-4"><a class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3" href="#!"><img src="assets/img/nav-icons/google-cloud.webp" alt="" width="30" />
                          <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Cloud</p>
                        </a></div>
                      <div class="col-4"><a class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3" href="#!"><img src="assets/img/nav-icons/google-drive.webp" alt="" width="30" />
                          <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Drive</p>
                        </a></div>
                      <div class="col-4"><a class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3" href="#!"><img src="assets/img/nav-icons/google-maps.webp" alt="" width="30" />
                          <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Maps</p>
                        </a></div>
                      <div class="col-4"><a class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3" href="#!"><img src="assets/img/nav-icons/google-photos.webp" alt="" width="30" />
                          <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Photos</p>
                        </a></div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
            <div class="avatar avatar-xl">
                        <?php
                          $img = !empty(SessionData::getFotoUsuario()) ? "assets/img/admin/" . htmlspecialchars(SessionData::getFotoUsuario()) : 'assets/img/santander.png';
                        ?>
                        <img class="rounded-circle" src="<?= $img ?>" alt="User-Profile-Image" />
                      </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
              <div class="card position-relative border-0">
                  <div class="card-body p-0">
                    <div class="text-center pt-4 pb-3">
                    <div class="avatar avatar-xl">
                        <?php
                          $img = !empty(SessionData::getFotoUsuario()) ? "assets/img/admin/" . htmlspecialchars(SessionData::getFotoUsuario()) : 'assets/img/santander.png';
                        ?>
                        <img class="rounded-circle" src="<?= $img ?>" alt="User-Profile-Image" />
                      </div>
                      <h6 class="mt-2 text-body-emphasis"><?= htmlspecialchars(SessionData::getNombreUsuario()); ?></h6>
                    </div>
                  </div>

                  <div class="overflow-auto scrollbar" style="height: 10rem;">
                    <ul class="nav d-flex flex-column mb-2 pb-1">
                      <li class="nav-item">
                        <a class="nav-link px-3 d-block" href="#" onclick="PROFILE.editData(<?= SessionData::getUserId(); ?>)" data-bs-toggle="modal" data-bs-target="#exampleModalLive">
                          <span class="me-2 text-body align-bottom" data-feather="user"></span>
                          <span>Perfil</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link px-3 d-block" href="#">
                          <span class="me-2 text-body align-bottom" data-feather="mail"></span>
                          <span>Rol: <?= htmlspecialchars(SessionData::getUserType()); ?></span>
                        </a>
                      </li>
                    </ul>
                  </div>

                  <div class="card-footer p-0 border-top border-translucent">
                    <hr />
                    <div class="px-3">
                      <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="logout.php">
                        <span class="me-2" data-feather="log-out"></span> Cerrar sesión
                      </a>
                    </div>
                    <div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
                      <a class="text-body-quaternary me-1" href="#!"></a>&bull;
                      <a class="text-body-quaternary mx-1" href="#!"></a>&bull;
                      <a class="text-body-quaternary ms-1" href="#!"></a>
                    </div>
                  </div>
                </div>

              </div>
            </li>
          </ul>
        </div>
      </nav>
      <div class="navbar-bottom-line"></div>

      <div class="modal fade" id="exampleModalLive" tabindex="-1" data-bs-backdrop="static" aria-labelledby="exampleModalLiveLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header bg-primary justify-content-between">
        <h5 class="modal-title text-white" id="exampleModalLiveLabel">Perfil</h5>
        <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
          <span class="fas fa-times fs-9 text-white"></span>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
        <form id="formusuarios" role="form" autocomplete="off">
          <input type="hidden" name="op" id="op" />
          <input type="hidden" name="id" id="id" />

          <span id="mensajes" class="text-danger mb-2 d-block"></span>

          <div class="row g-3">
            <div class="col-md-4">
              <label for="nombre_perfil" class="form-label">Nombres Completos <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nombre_perfil" name="nombre_perfil" placeholder="Ingrese nombres" required>
            </div>

            <div class="col-md-4">
              <label for="apellido_perfil" class="form-label">Apellidos <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="apellido_perfil" name="apellido_perfil" placeholder="Ingrese apellidos" required>
            </div>

            <div class="col-12">
              <label class="form-label">Foto</label>
              <div class="dropzone dropzone-multiple p-0 mb-2" id="my-awesome-dropzone" data-dropzone="data-dropzone" style="min-height: 100px; padding: 10px;">
                <iframe id="ifm" name="ifm" src="upload.php" width="40%" height="200" scrolling="no" frameborder="0" style="border: none;"></iframe>
              </div>
            </div>

            <div class="col-md-4">
              <label for="nickname_perfil" class="form-label">Usuario <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="nickname_perfil" name="nickname_perfil" placeholder="usuario@correo.com" required>
            </div>

            <div class="col-md-4">
              <label for="hashpass_perfil" class="form-label">Contraseña <span class="text-danger">*</span></label>
              <input type="password" class="form-control" id="hashpass_perfil" name="hashpass_perfil" placeholder="Ingrese contraseña" required>
            </div>

            <div class="col-md-4">
              <label for="hashpass1_perfil" class="form-label">Repita la Contraseña <span class="text-danger">*</span></label>
              <input type="password" class="form-control" id="hashpass1_perfil" name="hashpass1_perfil" placeholder="Repita contraseña" required>
            </div>
          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cerrar</button>
        <button class="btn btn-primary" type="button" onclick="PROFILE.validateData();">Actualizar Datos</button>
      </div>

    </div>
  </div>
</div>
<?php
    include './admin/include/asistentevirtual.php';
    ?>

    <style>
  /* Modo claro (por defecto) *//* Logo por defecto (tema claro) */
#logoGobierno {
  content: url("assets/img/estadistica4.png");
}

/* Logo para tema oscuro */
html[data-bs-theme="dark"] #logoGobierno {
  content: url("assets/img/estadistica4.png");
}


</style>   
<script>
function togglebot() {
    const botPopup = document.getElementById('botPopup');
    if (botPopup.style.display === 'none' || botPopup.style.display === '') {
        botPopup.style.display = 'block';
    } else {
        botPopup.style.display = 'none';
    }
}

// Agregar funcionalidad para enviar con Enter
document.addEventListener("DOMContentLoaded", function() {
    const userInput = document.getElementById("user-input");
    userInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Evita envío de formulario (si aplica)
            sendMessage(event);
        }
    });
});
// Agregar funcionalidad para que al undir al boton de necesito ayuda se envíe
document.getElementById('ayuda-rapida').addEventListener('click', function (e) {
    e.preventDefault();
    const input = document.getElementById('user-input');
    input.value = 'Necesito ayuda con algo';
    sendMessage(e);
  });
const chatBox = document.getElementById('chat-box');
// Función para manejar el envío del mensaje
async function sendMessage(event) {
    // Evitar que el evento se propague y cause el cierre del modal
    event.stopPropagation();
    // Obtener el mensaje del usuario
    const userInput = document.getElementById('user-input').value;
    if (!userInput.trim()) {
        // No enviar si el campo está vacío
        return;
    }
    // Ocultar saludo inicial
    const intro = document.getElementById('intro-message');
    if (intro && intro.style.display !== 'none') {
      intro.style.display = 'none';
    }

    // Ocultar botón de ayuda rápida 
    const ayudaRapida = document.getElementById('ayuda-rapida');
    if (ayudaRapida && ayudaRapida.style.display !== 'none') {
      ayudaRapida.parentElement.style.display = 'none'; 
    }

    // Mostrar el mensaje del usuario en el chat
    const userMessage = document.createElement('div');
    userMessage.classList.add('chat-message', 'user');
    userMessage.textContent = `Tú: ${userInput}`;
    chatBox.appendChild(userMessage);
    // Limpiar el campo de entrada
    document.getElementById('user-input').value = '';
    try {
        // Hacer la solicitud al backend
        const response = await fetch('chatgpt_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message: userInput
            })
        });
        if (!response.ok) {
            throw new Error(`Error en la respuesta: ${response.statusText}`);
        }
        const data = await response.json();
        // Mostrar la respuesta de ChatGPT en el chat
        const botMessage = document.createElement('div');
        botMessage.classList.add('chat-message', 'bot');
        botMessage.textContent = `Asistente Virtual: ${data.response}`;
        chatBox.appendChild(botMessage);
        // Desplazar el chat al final
        chatBox.scrollTop = chatBox.scrollHeight;

        let speech = new SpeechSynthesisUtterance(data.response);
            speech.lang = "es-MX";
            speech.rate = 1;
            window.speechSynthesis.speak(speech);


    } catch (error) {
        console.error('Error en la solicitud:', error);
        // Manejo de errores de la solicitud
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('chat-message', 'error');
        errorMessage.textContent = `Error: No se pudo obtener la respuesta del servidor.`;
        chatBox.appendChild(errorMessage);
    }
}
</script>
<script>
  //para actualizar la imagen de perfil cuando cargue
window.addEventListener("message", function(event) {
  if (event.data.newImage) {
    const newImagePath = 'assets/img/admin/' + event.data.newImage + '?t=' + new Date().getTime();
    document.querySelectorAll('img[alt="User-Profile-Image"]').forEach(img => {
      img.src = newImagePath;
    });
  }
});
</script>

<?php include 'admin/include/gerenic_script.php'; ?>
<script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>
<script type="text/javascript" src="admin/js/profile.js"></script>
