<?php
/* ==========================================================
   ESTADÍSTICA360 · HEADER GLOBAL SAAS PRO
   ----------------------------------------------------------
   Se conservan:
   - #navbarDefault
   - #btnToggleSidebar
   - #navbarVerticalCollapse
   - #themeControlToggle
   - #navbarDropdownNindeDots
   - #navbarDropdownUser
   - #exampleModalLive
   - #formusuarios
   - PROFILE.editData()
   - PROFILE.validateData()
   - iframe upload.php
   - asistente virtual
========================================================== */

$headerProfileImg =
    !empty(SessionData::getFotoUsuario())
        ? 'assets/img/admin/' . htmlspecialchars(
            SessionData::getFotoUsuario(),
            ENT_QUOTES,
            'UTF-8'
          )
        : 'assets/img/santander.png';

$headerNombreUsuario =
    htmlspecialchars(
        SessionData::getNombreUsuario(),
        ENT_QUOTES,
        'UTF-8'
    );

$headerTipoUsuario =
    htmlspecialchars(
        SessionData::getUserType(),
        ENT_QUOTES,
        'UTF-8'
    );

$headerUserId =
    (int) SessionData::getUserId();
?>


<style>
/* ==========================================================
   ESTADÍSTICA360
   HEADER GLOBAL · CENTERED RESPONSIVE PRO
   ----------------------------------------------------------
   PC:
   - Buscador centrado realmente en el viewport.
   - Logo y usuario no empujan el centro.
   - Altura compacta.

   TABLET:
   - Se elimina contexto secundario.
   - Buscador reduce tamaño.

   MOBILE:
   - Buscador desktop desaparece.
   - Controles compactos.
   - Logo no desborda.
========================================================== */

:root{
  --e360-header-h:64px;
  --e360-header-h-tablet:62px;
  --e360-header-h-mobile:58px;

  --e360-navy-950:#06162c;
  --e360-navy-900:#092246;
  --e360-navy-850:#10315f;
  --e360-blue:#4b8cf7;

  --e360-white:#fff;

  --e360-line:
    rgba(255,255,255,.10);

  --e360-shadow:
    0 10px 30px rgba(2,12,31,.24);

  --e360-popup-shadow:
    0 24px 64px rgba(15,23,42,.20);

  --e360-font:
    "Inter",
    "IBM Plex Sans",
    system-ui,
    -apple-system,
    BlinkMacSystemFont,
    "Segoe UI",
    sans-serif;
}


*{
  box-sizing:border-box;
}


/* ==========================================================
   TOPBAR RESET
========================================================== */

#navbarDefault.e360-topbar{
  position:fixed !important;

  inset:
    0 0 auto 0 !important;

  width:
    100% !important;

  max-width:
    none !important;

  height:
    var(--e360-header-h) !important;

  min-height:
    var(--e360-header-h) !important;

  margin:
    0 !important;

  padding:
    0 16px !important;

  transform:
    none !important;

  z-index:
    1050;

  display:flex !important;

  align-items:center !important;

  overflow:
    visible !important;

  border:
    0 !important;

  border-bottom:
    1px solid
    var(--e360-line) !important;

  border-radius:
    0 !important;

  font-family:
    var(--e360-font) !important;

  background:

    radial-gradient(
      420px 110px at 7% -35%,
      rgba(75,140,247,.30),
      transparent 72%
    ),

    radial-gradient(
      340px 110px at 93% -35%,
      rgba(40,182,218,.11),
      transparent 72%
    ),

    linear-gradient(
      135deg,
      var(--e360-navy-850) 0%,
      var(--e360-navy-900) 48%,
      var(--e360-navy-950) 100%
    ) !important;

  box-shadow:
    var(--e360-shadow) !important;
}


#navbarDefault.e360-topbar::before,
#navbarDefault.e360-topbar::after{
  content:
    none !important;
}


/* ==========================================================
   MAIN SHELL
========================================================== */

.e360-nav-shell{
  position:relative;

  width:100%;
  height:100%;

  display:flex;

  align-items:center;

  justify-content:space-between;

  gap:14px;

  margin:0 auto;
}


/* ==========================================================
   LEFT
========================================================== */

.e360-nav-left{
  position:relative;

  z-index:4;

  min-width:0;

  display:flex;

  align-items:center;

  gap:10px;

  flex:
    0 1 auto;
}


#navbarDefault .e360-menu-btn{
  width:
    42px !important;

  height:
    42px !important;

  min-width:
    42px !important;

  flex:
    0 0 42px;

  display:flex !important;

  align-items:center !important;

  justify-content:center !important;

  margin:
    0 !important;

  padding:
    0 !important;

  border:
    1px solid
    rgba(255,255,255,.14) !important;

  border-radius:
    12px !important;

  color:
    #fff !important;

  background:
    rgba(255,255,255,.075) !important;

  box-shadow:

    inset 0 1px 0
    rgba(255,255,255,.045),

    0 7px 16px
    rgba(0,0,0,.12);

  transition:

    transform .18s ease,

    background .18s ease,

    border-color .18s ease;
}


#navbarDefault .e360-menu-btn:hover{
  transform:
    translateY(-1px);

  border-color:
    rgba(255,255,255,.23) !important;

  background:
    rgba(255,255,255,.13) !important;
}


#navbarDefault .e360-menu-btn:focus{
  outline:
    none !important;

  box-shadow:
    0 0 0 4px
    rgba(75,140,247,.20) !important;
}


/* hamburger */

.e360-hamburger{
  position:relative;

  width:19px;
  height:15px;

  display:block;
}


.e360-hamburger span,
.e360-hamburger::before,
.e360-hamburger::after{
  content:"";

  position:absolute;

  left:0;

  width:100%;
  height:2px;

  border-radius:
    999px;

  background:
    #fff;

  transition:

    top .20s ease,

    transform .20s ease,

    opacity .15s ease,

    width .20s ease;
}


.e360-hamburger::before{
  top:0;
}


.e360-hamburger span{
  top:6.5px;
}


.e360-hamburger::after{
  top:13px;
}


.e360-menu-btn[aria-expanded="true"]
.e360-hamburger::before{
  top:6.5px;

  transform:
    rotate(45deg);
}


.e360-menu-btn[aria-expanded="true"]
.e360-hamburger span{
  width:0;

  opacity:0;
}


.e360-menu-btn[aria-expanded="true"]
.e360-hamburger::after{
  top:6.5px;

  transform:
    rotate(-45deg);
}


/* ==========================================================
   BRAND
========================================================== */

.e360-brand{
  min-width:0;

  display:flex !important;

  align-items:center;

  gap:9px;

  margin:
    0 !important;

  padding:
    0 !important;

  text-decoration:
    none !important;
}


#navbarDefault #logoGobierno{
  display:block;

  width:
    146px !important;

  max-width:
    146px !important;

  height:
    42px !important;

  margin:
    0 !important;

  object-fit:
    contain;

  object-position:
    left center;

  flex:
    0 0 auto;

  filter:
    drop-shadow(
      0 6px 14px
      rgba(0,0,0,.18)
    );
}


.e360-brand-divider{
  width:1px;
  height:27px;

  flex:
    0 0 1px;

  background:
    rgba(255,255,255,.13);
}


.e360-brand-context{
  min-width:0;

  display:flex;

  flex-direction:column;

  gap:1px;
}


.e360-brand-context strong{
  max-width:
    150px;

  overflow:hidden;

  text-overflow:
    ellipsis;

  white-space:
    nowrap;

  color:
    #fff;

  font-size:
    .63rem;

  line-height:
    1.15;

  font-weight:
    800;
}


.e360-brand-context span{
  color:
    rgba(255,255,255,.46);

  font-size:
    .52rem;

  font-weight:
    650;
}


/* ==========================================================
   CENTER SEARCH
   Este bloque queda centrado respecto al VIEWPORT,
   no respecto a las columnas laterales.
========================================================== */

.e360-search-column{
  position:absolute;

  top:50%;
  left:50%;

  z-index:3;

  width:
    clamp(
      300px,
      32vw,
      470px
    );

  transform:
    translate(-50%,-50%);
}


.e360-search{
  position:relative;

  width:100%;
}


.e360-search .search-input{
  width:100%;

  min-height:
    40px;

  padding:
    8px 49px 8px 40px !important;

  border:
    1px solid
    rgba(255,255,255,.13) !important;

  border-radius:
    12px !important;

  color:
    #fff !important;

  background:
    rgba(255,255,255,.072) !important;

  font-size:
    .68rem;

  font-weight:
    650;

  outline:
    none !important;

  backdrop-filter:
    blur(12px);

  box-shadow:

    inset 0 1px 0
    rgba(255,255,255,.035) !important;

  transition:

    border-color .18s ease,

    background .18s ease,

    box-shadow .18s ease;
}


.e360-search .search-input::placeholder{
  color:
    rgba(255,255,255,.50) !important;
}


.e360-search .search-input:focus{
  border-color:
    rgba(121,167,255,.52) !important;

  background:
    rgba(255,255,255,.11) !important;

  box-shadow:
    0 0 0 4px
    rgba(75,140,247,.11) !important;
}


.e360-search-icon{
  position:absolute;

  top:50%;
  left:14px;

  z-index:2;

  transform:
    translateY(-50%);

  color:
    rgba(255,255,255,.54);

  font-size:
    .70rem;

  pointer-events:none;
}


.e360-search-key{
  position:absolute;

  top:50%;
  right:8px;

  z-index:2;

  transform:
    translateY(-50%);

  min-width:
    32px;

  height:
    23px;

  display:flex;

  align-items:center;

  justify-content:center;

  padding:
    0 6px;

  border:
    1px solid
    rgba(255,255,255,.09);

  border-radius:
    7px;

  color:
    rgba(255,255,255,.43);

  background:
    rgba(0,0,0,.12);

  font-size:
    .48rem;

  font-weight:
    800;

  pointer-events:none;
}


#navbarDefault
.navbar-top-search-box
.dropdown-menu{
  overflow:hidden;

  width:100% !important;

  margin-top:
    8px !important;

  border:
    1px solid
    #e4e9f1 !important;

  border-radius:
    13px !important;

  background:
    #fff !important;

  box-shadow:
    var(--e360-popup-shadow) !important;
}


/* ==========================================================
   RIGHT
========================================================== */

.e360-nav-right{
  position:relative;

  z-index:4;

  min-width:0;

  display:flex;

  align-items:center;

  justify-content:flex-end;

  gap:3px;

  flex:
    0 0 auto;
}


.e360-control{
  position:relative;

  width:
    38px;

  height:
    38px;

  min-width:
    38px;

  display:flex !important;

  align-items:center;

  justify-content:center;

  margin:
    0 !important;

  padding:
    0 !important;

  border:
    1px solid
    transparent !important;

  border-radius:
    11px !important;

  color:
    rgba(255,255,255,.88) !important;

  background:
    transparent !important;

  transition:

    transform .18s ease,

    border-color .18s ease,

    background .18s ease;
}


.e360-control:hover{
  transform:
    translateY(-1px);

  border-color:
    rgba(255,255,255,.10) !important;

  background:
    rgba(255,255,255,.08) !important;
}


/* ==========================================================
   THEME
========================================================== */

.e360-theme-wrap{
  position:relative;

  width:
    38px;

  height:
    38px;

  flex:
    0 0 38px;

  display:flex;

  align-items:center;

  justify-content:center;

  margin:0;

  padding:0 !important;

  border-radius:
    11px;
}


#navbarDefault
.theme-control-toggle-input{
  position:absolute;

  opacity:0;

  pointer-events:none;
}


#navbarDefault
.theme-control-toggle-label{
  width:
    38px !important;

  height:
    38px !important;

  display:flex !important;

  align-items:center;

  justify-content:center;

  margin:
    0 !important;

  border-radius:
    11px;

  color:
    rgba(255,255,255,.90) !important;

  cursor:pointer;
}


/* ==========================================================
   USER TRIGGER
========================================================== */

.e360-user-trigger{
  min-height:
    42px;

  display:flex !important;

  align-items:center;

  gap:8px;

  margin:
    0 !important;

  padding:
    3px 4px 3px 8px !important;

  border:
    1px solid
    rgba(255,255,255,.095) !important;

  border-radius:
    12px !important;

  color:
    #fff !important;

  background:
    rgba(255,255,255,.058) !important;

  text-decoration:
    none !important;

  transition:

    transform .18s ease,

    border-color .18s ease,

    background .18s ease;
}


.e360-user-trigger:hover{
  transform:
    translateY(-1px);

  border-color:
    rgba(255,255,255,.16) !important;

  background:
    rgba(255,255,255,.095) !important;
}


.e360-user-copy{
  max-width:
    112px;

  display:flex;

  flex-direction:column;

  gap:0;

  text-align:
    right;
}


.e360-user-copy strong,
.e360-user-copy span{
  overflow:hidden;

  text-overflow:
    ellipsis;

  white-space:
    nowrap;
}


.e360-user-copy strong{
  color:
    #fff;

  font-size:
    .59rem;

  line-height:
    1.15;

  font-weight:
    800;
}


.e360-user-copy span{
  margin-top:
    2px;

  color:
    rgba(255,255,255,.43);

  font-size:
    .49rem;

  font-weight:
    650;
}


.e360-user-avatar{
  position:relative;

  width:
    34px;

  height:
    34px;

  flex:
    0 0 34px;
}


.e360-user-avatar img{
  width:100%;
  height:100%;

  display:block;

  object-fit:cover;

  border:
    2px solid
    rgba(255,255,255,.56);

  border-radius:
    10px;

  box-shadow:
    0 5px 12px
    rgba(0,0,0,.18);
}


.e360-user-avatar::after{
  content:"";

  position:absolute;

  right:-1px;
  bottom:-1px;

  width:9px;
  height:9px;

  border:
    2px solid
    var(--e360-navy-900);

  border-radius:
    50%;

  background:
    #33d58a;
}


/* ==========================================================
   APP LAUNCHER
========================================================== */

.e360-app-grid{
  width:
    min(
      310px,
      calc(100vw - 18px)
    );

  max-width:
    calc(100vw - 18px);

  padding:
    11px;

  border:
    1px solid
    #e5eaf1 !important;

  border-radius:
    17px !important;

  background:
    #fff !important;

  box-shadow:
    var(--e360-popup-shadow) !important;
}


.e360-app-header{
  display:flex;

  align-items:center;

  justify-content:space-between;

  gap:10px;

  padding:
    5px 4px 11px;

  border-bottom:
    1px solid
    #eef1f5;
}


.e360-app-header strong{
  display:block;

  color:
    #101828;

  font-size:
    .69rem;

  font-weight:
    800;
}


.e360-app-header span{
  display:block;

  margin-top:
    1px;

  color:
    #98a2b3;

  font-size:
    .53rem;

  font-weight:
    650;
}


.e360-app-list{
  display:grid;

  grid-template-columns:
    repeat(2,minmax(0,1fr));

  gap:7px;

  padding-top:
    10px;
}


.e360-app-item{
  min-width:0;

  min-height:
    55px;

  display:flex;

  align-items:center;

  gap:8px;

  padding:
    8px;

  border:
    1px solid
    #e7ebf1;

  border-radius:
    11px;

  color:
    #344054 !important;

  background:
    #fbfcfe;

  text-decoration:
    none !important;

  transition:

    transform .18s ease,

    border-color .18s ease,

    background .18s ease;
}


.e360-app-item:hover{
  transform:
    translateY(-1px);

  border-color:
    #d3e1f4;

  background:
    #f5f9ff;
}


.e360-app-icon{
  width:32px;
  height:32px;

  flex:
    0 0 32px;

  display:flex;

  align-items:center;

  justify-content:center;

  border-radius:
    9px;

  color:
    #285faf;

  background:
    #edf4ff;

  font-size:
    .70rem;
}


.e360-app-item strong{
  display:block;

  color:
    #344054;

  font-size:
    .59rem;

  font-weight:
    800;
}


.e360-app-item span{
  display:block;

  margin-top:
    1px;

  color:
    #98a2b3;

  font-size:
    .49rem;

  font-weight:
    600;
}


/* ==========================================================
   PROFILE DROPDOWN
========================================================== */

.e360-profile-menu{
  width:
    min(
      300px,
      calc(100vw - 18px)
    );

  max-width:
    calc(100vw - 18px);

  overflow:hidden;

  margin-top:
    8px !important;

  padding:
    0 !important;

  border:
    1px solid
    #e5eaf1 !important;

  border-radius:
    17px !important;

  background:
    #fff !important;

  box-shadow:
    var(--e360-popup-shadow) !important;
}


.e360-profile-hero{
  padding:
    16px 14px 13px;

  text-align:center;

  background:

    radial-gradient(
      200px 95px at 50% -10%,
      rgba(75,140,247,.12),
      transparent 70%
    ),

    linear-gradient(
      180deg,
      #fff,
      #f8fafd
    );
}


.e360-profile-large-avatar{
  width:60px;
  height:60px;

  margin:
    0 auto 8px;
}


.e360-profile-large-avatar img{
  width:100%;
  height:100%;

  object-fit:cover;

  border:
    3px solid
    #dde9f9;

  border-radius:
    17px;

  box-shadow:
    0 8px 22px
    rgba(32,66,127,.11);
}


.e360-profile-hero strong{
  display:block;

  color:
    #101828;

  font-size:
    .72rem;

  font-weight:
    800;
}


.e360-profile-hero span{
  display:block;

  margin-top:
    2px;

  color:
    #98a2b3;

  font-size:
    .54rem;

  font-weight:
    650;
}


.e360-profile-actions{
  padding:
    7px;
}


.e360-profile-link{
  min-height:
    41px;

  display:flex;

  align-items:center;

  gap:8px;

  padding:
    7px 9px;

  border-radius:
    9px;

  color:
    #344054 !important;

  text-decoration:
    none !important;

  font-size:
    .61rem;

  font-weight:
    700;
}


.e360-profile-link:hover{
  color:
    #245ba7 !important;

  background:
    #f1f6ff;
}


.e360-profile-link i,
.e360-profile-link .profile-feather{
  width:28px;
  height:28px;

  display:flex;

  align-items:center;

  justify-content:center;

  border-radius:
    8px;

  color:
    #285faf;

  background:
    #edf4ff;

  font-size:
    .64rem;
}


.e360-profile-footer{
  padding:
    9px;

  border-top:
    1px solid
    #eef1f5;

  background:
    #fbfcfe;
}


.e360-logout{
  min-height:
    39px;

  display:flex;

  align-items:center;

  justify-content:center;

  gap:7px;

  border:
    1px solid
    #f0d4d5;

  border-radius:
    10px;

  color:
    #b42318 !important;

  background:
    #fff7f7;

  font-size:
    .61rem;

  font-weight:
    800;

  text-decoration:
    none !important;
}


/* ==========================================================
   GLOBAL OFFSET
========================================================== */

.navbar-bottom-line{
  display:
    none !important;

  height:
    0 !important;

  margin:
    0 !important;

  padding:
    0 !important;

  border:
    0 !important;
}


/*
  Importante:
  una sola compensación vertical para el header fijo.
*/
body{
  padding-top:
    var(--e360-header-h) !important;
}


main.main,
.pcoded-main-container,
.main-content,
#top{
  margin-top:
    0 !important;

  padding-top:
    0 !important;
}


.content{
  margin-top:
    0 !important;
}


/* ==========================================================
   PROFILE MODAL
========================================================== */

#exampleModalLive .modal-dialog{
  width:
    min(
      760px,
      calc(100vw - 24px)
    );

  max-width:
    760px;
}


#exampleModalLive .modal-content{
  overflow:hidden;

  border:
    1px solid
    rgba(15,23,42,.09) !important;

  border-radius:
    21px !important;

  box-shadow:
    0 30px 82px
    rgba(15,23,42,.25) !important;
}


#exampleModalLive .modal-header{
  position:relative;

  overflow:hidden;

  padding:
    15px 17px !important;

  border:
    0 !important;

  color:
    #fff !important;

  background:

    radial-gradient(
      320px 130px at 8% -15%,
      rgba(75,140,247,.34),
      transparent 72%
    ),

    linear-gradient(
      135deg,
      #173d79,
      #102a56 55%,
      #081b38
    ) !important;
}


#exampleModalLive .modal-title{
  color:
    #fff !important;

  font-family:
    var(--e360-font);

  font-size:
    .86rem;

  font-weight:
    800;
}


.e360-modal-title-wrap{
  display:flex;

  align-items:center;

  gap:9px;
}


.e360-modal-icon{
  width:37px;
  height:37px;

  flex:
    0 0 37px;

  display:flex;

  align-items:center;

  justify-content:center;

  border:
    1px solid
    rgba(255,255,255,.15);

  border-radius:
    11px;

  background:
    rgba(255,255,255,.10);
}


#exampleModalLive .modal-body{
  padding:
    15px !important;

  background:

    linear-gradient(
      180deg,
      #fbfcfe,
      #f5f8fc
    );
}


.e360-profile-form-card{
  padding:
    13px;

  border:
    1px solid
    #e5eaf1;

  border-radius:
    15px;

  background:
    #fff;

  box-shadow:
    0 8px 20px
    rgba(15,23,42,.04);
}


#exampleModalLive .form-label{
  margin-bottom:
    5px;

  color:
    #475467;

  font-size:
    .62rem;

  font-weight:
    800;
}


#exampleModalLive .form-control{
  min-height:
    42px;

  border:
    1px solid
    #d9e0ea !important;

  border-radius:
    10px !important;

  color:
    #344054;

  background:
    #fbfcfe;

  font-size:
    .68rem;

  font-weight:
    600;

  box-shadow:
    none !important;
}


#exampleModalLive .form-control:focus{
  border-color:
    #4b8cf7 !important;

  background:
    #fff;

  box-shadow:
    0 0 0 4px
    rgba(75,140,247,.09) !important;
}


.e360-photo-uploader{
  overflow:hidden;

  border:
    1px dashed
    #bfcfe3;

  border-radius:
    12px;

  background:
    #fff;
}


#ifm{
  display:block;

  width:
    100% !important;

  height:
    165px !important;

  border:
    0 !important;
}


.e360-password-wrap{
  position:relative;
}


.e360-password-wrap .form-control{
  padding-right:
    41px;
}


.e360-pass-toggle{
  position:absolute;

  top:50%;
  right:6px;

  width:30px;
  height:30px;

  transform:
    translateY(-50%);

  display:flex;

  align-items:center;

  justify-content:center;

  padding:0;

  border:0;

  border-radius:
    8px;

  color:
    #667085;

  background:
    transparent;
}


.e360-pass-toggle:hover{
  color:
    #245ba7;

  background:
    #eef5ff;
}


#exampleModalLive .modal-footer{
  padding:
    10px 15px !important;

  border-top:
    1px solid
    #e7ebf1 !important;

  background:
    #fff;
}


.e360-modal-btn{
  min-height:
    39px;

  display:inline-flex;

  align-items:center;

  justify-content:center;

  gap:7px;

  padding:
    7px 12px;

  border-radius:
    10px !important;

  font-size:
    .62rem;

  font-weight:
    800;
}


.e360-modal-save{
  border:
    0 !important;

  color:
    #fff !important;

  background:

    linear-gradient(
      135deg,
      #4b8cf7,
      #285faf
    ) !important;
}


/* ==========================================================
   DARK MODE
========================================================== */

html[data-bs-theme="dark"]
#navbarDefault.e360-topbar{
  background:

    radial-gradient(
      420px 110px at 8% -35%,
      rgba(75,140,247,.20),
      transparent 72%
    ),

    linear-gradient(
      135deg,
      #0a1528,
      #07111f
    ) !important;
}


html[data-bs-theme="dark"]
.e360-profile-menu,
html[data-bs-theme="dark"]
.e360-app-grid{
  border-color:
    #293548 !important;

  background:
    #111a29 !important;
}


html[data-bs-theme="dark"]
.e360-profile-hero,
html[data-bs-theme="dark"]
.e360-profile-footer,
html[data-bs-theme="dark"]
.e360-app-item{
  background:
    #141f30 !important;
}


html[data-bs-theme="dark"]
.e360-profile-hero strong,
html[data-bs-theme="dark"]
.e360-app-header strong,
html[data-bs-theme="dark"]
.e360-app-item strong{
  color:
    #f8fafc !important;
}


html[data-bs-theme="dark"]
.e360-profile-link{
  color:
    #d8e0eb !important;
}


/* logo consistente */

#logoGobierno,
html[data-bs-theme="dark"]
#logoGobierno{
  content:
    url("assets/img/estadistica4.png");
}


/* ==========================================================
   LARGE LAPTOPS
========================================================== */

@media (
  max-width:1399.98px
){

  .e360-brand-context{
    display:
      none;
  }


  .e360-brand-divider{
    display:
      none;
  }


  .e360-search-column{
    width:
      clamp(
        290px,
        30vw,
        420px
      );
  }


  .e360-user-copy{
    display:
      none;
  }
}


/* ==========================================================
   TABLET / SMALL LAPTOP
========================================================== */

@media (
  max-width:991.98px
){

  :root{
    --e360-header-h:
      var(--e360-header-h-tablet);
  }


  #navbarDefault.e360-topbar{
    height:
      var(--e360-header-h-tablet) !important;

    min-height:
      var(--e360-header-h-tablet) !important;

    padding:
      0 11px !important;
  }


  body{
    padding-top:
      var(--e360-header-h-tablet) !important;
  }


  .e360-search-column{
    display:
      none;
  }


  #navbarDefault #logoGobierno{
    width:
      132px !important;

    max-width:
      132px !important;

    height:
      39px !important;
  }


  .e360-nav-left{
    gap:
      8px;
  }


  .e360-nav-right{
    gap:
      2px;
  }
}


/* ==========================================================
   MOBILE
========================================================== */

@media (
  max-width:575.98px
){

  :root{
    --e360-header-h:
      var(--e360-header-h-mobile);
  }


  #navbarDefault.e360-topbar{
    height:
      var(--e360-header-h-mobile) !important;

    min-height:
      var(--e360-header-h-mobile) !important;

    padding:
      0 7px !important;
  }


  body{
    padding-top:
      var(--e360-header-h-mobile) !important;
  }


  .e360-nav-shell{
    gap:
      5px;
  }


  .e360-nav-left{
    gap:
      6px;

    max-width:
      calc(100% - 140px);
  }


  #navbarDefault .e360-menu-btn{
    width:
      38px !important;

    height:
      38px !important;

    min-width:
      38px !important;

    flex:
      0 0 38px;

    border-radius:
      10px !important;
  }


  .e360-hamburger{
    width:
      17px;

    height:
      14px;
  }


  .e360-hamburger span{
    top:
      6px;
  }


  .e360-hamburger::after{
    top:
      12px;
  }


  #navbarDefault #logoGobierno{
    width:
      104px !important;

    max-width:
      104px !important;

    height:
      35px !important;
  }


  .e360-nav-right{
    gap:
      0;
  }


  .e360-control,
  .e360-theme-wrap,
  #navbarDefault
  .theme-control-toggle-label{
    width:
      34px !important;

    height:
      34px !important;

    min-width:
      34px;

    flex:
      0 0 34px;
  }


  .e360-user-trigger{
    min-height:
      36px;

    padding:
      1px !important;

    border:
      0 !important;

    background:
      transparent !important;
  }


  .e360-user-avatar{
    width:
      32px;

    height:
      32px;

    flex:
      0 0 32px;
  }


  .e360-profile-menu,
  .e360-app-grid{
    width:
      min(
        292px,
        calc(100vw - 12px)
      );

    max-width:
      calc(100vw - 12px);
  }


  #exampleModalLive .modal-dialog{
    width:
      calc(100vw - 12px);

    max-width:
      none;

    margin:
      6px auto;
  }


  #exampleModalLive .modal-content{
    border-radius:
      17px !important;
  }


  #exampleModalLive .modal-body{
    max-height:
      72vh !important;

    padding:
      11px !important;
  }


  #exampleModalLive .e360-profile-form-card{
    padding:
      10px;
  }
}


/* ==========================================================
   VERY SMALL PHONES
========================================================== */

@media (
  max-width:390px
){

  #navbarDefault.e360-topbar{
    padding:
      0 5px !important;
  }


  #navbarDefault #logoGobierno{
    width:
      90px !important;

    max-width:
      90px !important;
  }


  .e360-nav-left{
    gap:
      4px;

    max-width:
      calc(100% - 132px);
  }


  .e360-nav-right{
    gap:
      0;
  }


  .e360-control,
  .e360-theme-wrap,
  #navbarDefault
  .theme-control-toggle-label{
    width:
      31px !important;

    height:
      31px !important;

    min-width:
      31px;

    flex:
      0 0 31px;
  }


  .e360-user-avatar{
    width:
      30px;

    height:
      30px;

    flex:
      0 0 30px;
  }
}


/* ==========================================================
   ACCESSIBILITY
========================================================== */

@media (
  prefers-reduced-motion:reduce
){

  *,
  *::before,
  *::after{
    animation-duration:
      .01ms !important;

    animation-iteration-count:
      1 !important;

    transition-duration:
      .01ms !important;

    scroll-behavior:
      auto !important;
  }
}



/* ==========================================================
   FINAL HEADER ALIGNMENT FIX
   Perfil + tema + dropdowns
========================================================== */

/* SOLO UN ICONO DE TEMA A LA VEZ */
#navbarDefault .e360-theme-wrap{
  position:relative !important;
  overflow:hidden !important;
}

#navbarDefault .theme-control-toggle-label{
  position:absolute !important;
  inset:0 !important;
  width:100% !important;
  height:100% !important;
  margin:0 !important;
  display:none !important;
  align-items:center !important;
  justify-content:center !important;
  border-radius:inherit !important;
}

#navbarDefault
#themeControlToggle:not(:checked)
~ .theme-control-toggle-light{
  display:flex !important;
}

#navbarDefault
#themeControlToggle:checked
~ .theme-control-toggle-dark{
  display:flex !important;
}

/* PARENTS */
#navbarDefault .e360-user-dropdown,
#navbarDefault .e360-apps-dropdown{
  position:relative !important;
  display:flex !important;
  align-items:center !important;
  flex:0 0 auto !important;
  margin:0 !important;
  padding:0 !important;
}

/* PERFIL DESKTOP */
#navbarDefault
.e360-user-dropdown
.e360-profile-menu{
  position:absolute !important;
  top:calc(100% + 9px) !important;
  right:0 !important;
  left:auto !important;
  bottom:auto !important;
  transform:none !important;
  width:300px !important;
  min-width:300px !important;
  max-width:300px !important;
  margin:0 !important;
  padding:0 !important;
  overflow:hidden !important;
  z-index:1085 !important;
}

#navbarDefault
.e360-user-dropdown
.e360-profile-menu.show{
  right:0 !important;
  left:auto !important;
  transform:none !important;
}

/* APPS DESKTOP */
#navbarDefault
.e360-apps-dropdown
.e360-app-grid{
  position:absolute !important;
  top:calc(100% + 9px) !important;
  right:0 !important;
  left:auto !important;
  transform:none !important;
  width:310px !important;
  min-width:310px !important;
  max-width:310px !important;
  margin:0 !important;
  z-index:1084 !important;
}

/* CONTENIDO PERFIL */
.e360-profile-menu .e360-profile-hero,
.e360-profile-menu .e360-profile-actions,
.e360-profile-menu .e360-profile-footer{
  width:100% !important;
}

.e360-profile-menu .e360-profile-link{
  width:100% !important;
  min-width:0 !important;
  white-space:normal !important;
}

.e360-profile-menu .e360-logout{
  width:100% !important;
  min-height:42px !important;
  white-space:nowrap !important;
}

/* TABLET */
@media (max-width:991.98px){

  #navbarDefault
  .e360-user-dropdown
  .e360-profile-menu{
    width:286px !important;
    min-width:286px !important;
    max-width:286px !important;
  }

  #navbarDefault
  .e360-apps-dropdown
  .e360-app-grid{
    width:292px !important;
    min-width:292px !important;
    max-width:292px !important;
  }
}

/* MOBILE */
@media (max-width:575.98px){

  #navbarDefault .e360-nav-right{
    position:relative !important;
    flex:0 0 auto !important;
    width:auto !important;
    max-width:150px !important;
    margin-left:auto !important;
    padding:0 !important;
    overflow:visible !important;
  }

  #navbarDefault .e360-user-dropdown{
    position:static !important;
  }

  #navbarDefault
  .e360-user-dropdown
  .e360-profile-menu,
  #navbarDefault
  .e360-user-dropdown
  .e360-profile-menu.show{
    position:fixed !important;
    top:calc(var(--e360-header-h-mobile) + 7px) !important;
    right:7px !important;
    left:7px !important;
    bottom:auto !important;
    width:auto !important;
    min-width:0 !important;
    max-width:none !important;
    max-height:calc(100dvh - var(--e360-header-h-mobile) - 18px) !important;
    margin:0 !important;
    padding:0 !important;
    transform:none !important;
    overflow-x:hidden !important;
    overflow-y:auto !important;
    border:1px solid rgba(15,23,42,.12) !important;
    border-radius:18px !important;
    background:#fff !important;
    box-shadow:0 24px 65px rgba(2,12,31,.28) !important;
    z-index:20000 !important;
  }

  #navbarDefault
  .e360-user-dropdown
  .dropdown-menu[data-popper-placement]{
    inset:auto 7px auto 7px !important;
  }

  .e360-profile-menu .e360-profile-hero{
    display:grid !important;
    grid-template-columns:56px minmax(0,1fr) !important;
    grid-template-rows:auto auto !important;
    column-gap:11px !important;
    align-items:center !important;
    padding:14px !important;
    text-align:left !important;
  }

  .e360-profile-menu .e360-profile-large-avatar{
    grid-row:1 / span 2 !important;
    width:54px !important;
    height:54px !important;
    margin:0 !important;
  }

  .e360-profile-menu .e360-profile-large-avatar img{
    border-radius:14px !important;
  }

  .e360-profile-menu .e360-profile-hero > strong{
    align-self:end !important;
    margin:0 !important;
    font-size:.72rem !important;
    text-align:left !important;
  }

  .e360-profile-menu .e360-profile-hero > span{
    align-self:start !important;
    margin-top:3px !important;
    font-size:.56rem !important;
    text-align:left !important;
  }

  .e360-profile-menu .e360-profile-actions{
    padding:8px !important;
  }

  .e360-profile-menu .e360-profile-link{
    min-height:44px !important;
    padding:8px 10px !important;
    border-radius:10px !important;
    font-size:.64rem !important;
  }

  .e360-profile-menu .e360-profile-footer{
    padding:9px !important;
  }

  .e360-profile-menu .e360-logout{
    min-height:44px !important;
    border-radius:11px !important;
    font-size:.65rem !important;
    font-weight:850 !important;
  }

  /* Apps contained in viewport */
  #navbarDefault .e360-apps-dropdown{
    position:static !important;
  }

  #navbarDefault
  .e360-apps-dropdown
  .e360-app-grid,
  #navbarDefault
  .e360-apps-dropdown
  .e360-app-grid.show{
    position:fixed !important;
    top:calc(var(--e360-header-h-mobile) + 7px) !important;
    right:7px !important;
    left:7px !important;
    width:auto !important;
    min-width:0 !important;
    max-width:none !important;
    margin:0 !important;
    transform:none !important;
    z-index:19999 !important;
  }

  #navbarDefault,
  #navbarDefault .e360-nav-shell,
  #navbarDefault .e360-nav-right{
    overflow:visible !important;
  }
}

/* iPhone class viewport ~402px */
@media (min-width:391px) and (max-width:430px){

  #navbarDefault #logoGobierno{
    width:100px !important;
    max-width:100px !important;
  }

  #navbarDefault .e360-nav-right{
    max-width:142px !important;
  }

  #navbarDefault .e360-control,
  #navbarDefault .e360-theme-wrap,
  #navbarDefault .theme-control-toggle-label{
    width:32px !important;
    height:32px !important;
    min-width:32px !important;
    flex:0 0 32px !important;
  }

  #navbarDefault .e360-user-avatar{
    width:31px !important;
    height:31px !important;
    flex:0 0 31px !important;
  }
}

/* very small phones */
@media (max-width:390px){

  #navbarDefault .e360-apps-dropdown{
    display:none !important;
  }

  #navbarDefault .e360-nav-right{
    max-width:108px !important;
  }

  #navbarDefault #logoGobierno{
    width:88px !important;
    max-width:88px !important;
  }

  #navbarDefault
  .e360-user-dropdown
  .e360-profile-menu,
  #navbarDefault
  .e360-user-dropdown
  .e360-profile-menu.show{
    top:calc(var(--e360-header-h-mobile) + 5px) !important;
    right:5px !important;
    left:5px !important;
  }
}

/* desktop safety */
@media (min-width:992px){

  #navbarDefault
  .e360-user-dropdown
  .e360-profile-menu{
    right:0 !important;
    left:auto !important;
  }

  #navbarDefault
  .e360-apps-dropdown
  .e360-app-grid{
    right:0 !important;
    left:auto !important;
  }
}

</style>



<!-- ==========================================================
     TOP NAVBAR
========================================================== -->

<nav
    class="navbar navbar-top fixed-top e360-topbar"
    id="navbarDefault">


  <div class="e360-nav-shell">


    <!-- LEFT -->
    <div class="e360-nav-left">


      <button
          class="btn navbar-toggler navbar-toggler-humburger-icon e360-menu-btn"
          type="button"
          id="btnToggleSidebar"
          data-bs-toggle="collapse"
          data-bs-target="#navbarVerticalCollapse"
          aria-controls="navbarVerticalCollapse"
          aria-expanded="false"
          aria-label="Abrir o cerrar menú lateral">

        <span
            class="e360-hamburger"
            aria-hidden="true">

          <span></span>

        </span>

      </button>


      <a
          class="navbar-brand e360-brand"
          href="dashboard.php"
          aria-label="Ir al dashboard de Estadística360">


        <img
            id="logoGobierno"
            src="assets/img/estadistica4.png"
            alt="Estadística360">


        <span class="e360-brand-divider"></span>


        <span class="e360-brand-context">

          <strong>
            Intelligence Center
          </strong>

          <span>
            Analítica & Estudios
          </span>

        </span>


      </a>


    </div>


    <!-- SEARCH -->
    <div class="e360-search-column">


      <div
          class="search-box navbar-top-search-box e360-search"
          data-list='{"valueNames":["title"]}'>


        <form
            class="position-relative"
            data-bs-toggle="search"
            data-bs-display="static">


          <span
              class="fas fa-search e360-search-icon">
          </span>


          <input
              class="form-control search-input fuzzy-search"
              type="search"
              placeholder="Buscar dentro de Estadística360..."
              aria-label="Buscar">


          <span class="e360-search-key">
            CTRL K
          </span>


        </form>


        <div
            class="dropdown-menu start-0 py-0 overflow-hidden w-100">


          <div
              class="scrollbar-overlay"
              style="max-height:30rem;">


            <div class="text-center py-3">


              <p class="fallback fw-bold fs-9 d-none mb-0">

                No se encontraron resultados.

              </p>


            </div>


          </div>


        </div>


      </div>


    </div>


    <!-- RIGHT -->
    <div class="e360-nav-right">


      <!-- MOBILE SEARCH -->
      <a
          class="nav-link e360-control d-lg-none"
          href="#"
          data-bs-toggle="modal"
          data-bs-target="#searchBoxModal"
          aria-label="Buscar">

        <span
            data-feather="search"
            style="width:18px;height:18px;">
        </span>

      </a>


      <!-- THEME -->
      <div class="theme-control-toggle fa-icon-wait e360-theme-wrap">


        <input
            class="form-check-input ms-0 theme-control-toggle-input"
            type="checkbox"
            data-theme-control="phoenixTheme"
            value="dark"
            id="themeControlToggle">


        <label
            class="mb-0 theme-control-toggle-label theme-control-toggle-light"
            for="themeControlToggle"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="Cambiar a tema oscuro">

          <span
              class="icon"
              data-feather="moon">
          </span>

        </label>


        <label
            class="mb-0 theme-control-toggle-label theme-control-toggle-dark"
            for="themeControlToggle"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="Cambiar a tema claro">

          <span
              class="icon"
              data-feather="sun">
          </span>

        </label>


      </div>


      <!-- APPS -->
      <div class="dropdown e360-apps-dropdown">


        <a
            class="nav-link e360-control"
            id="navbarDropdownNindeDots"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            data-bs-auto-close="outside"
            aria-expanded="false"
            aria-label="Herramientas rápidas"
            title="Herramientas rápidas">


          <svg
              width="16"
              height="16"
              viewBox="0 0 16 16"
              fill="none"
              xmlns="http://www.w3.org/2000/svg">

            <circle cx="2" cy="2" r="1.55" fill="currentColor"></circle>
            <circle cx="2" cy="8" r="1.55" fill="currentColor"></circle>
            <circle cx="2" cy="14" r="1.55" fill="currentColor"></circle>

            <circle cx="8" cy="2" r="1.55" fill="currentColor"></circle>
            <circle cx="8" cy="8" r="1.55" fill="currentColor"></circle>
            <circle cx="8" cy="14" r="1.55" fill="currentColor"></circle>

            <circle cx="14" cy="2" r="1.55" fill="currentColor"></circle>
            <circle cx="14" cy="8" r="1.55" fill="currentColor"></circle>
            <circle cx="14" cy="14" r="1.55" fill="currentColor"></circle>

          </svg>


        </a>


        <div
            class="dropdown-menu dropdown-menu-end e360-app-grid"
            aria-labelledby="navbarDropdownNindeDots">


          <div class="e360-app-header">

            <div>

              <strong>
                Herramientas rápidas
              </strong>

              <span>
                Accesos del ecosistema
              </span>

            </div>


            <i
                class="fas fa-grid-2"
                style="color:#98A2B3;">
            </i>

          </div>


          <div class="e360-app-list">


            <a
                class="e360-app-item"
                href="#!">

              <span class="e360-app-icon">

                <i class="fas fa-cloud"></i>

              </span>

              <span>

                <strong>
                  Cloud
                </strong>

                <span>
                  Recursos
                </span>

              </span>

            </a>


            <a
                class="e360-app-item"
                href="#!">

              <span class="e360-app-icon">

                <i class="fas fa-folder-open"></i>

              </span>

              <span>

                <strong>
                  Drive
                </strong>

                <span>
                  Documentos
                </span>

              </span>

            </a>


            <a
                class="e360-app-item"
                href="#!">

              <span class="e360-app-icon">

                <i class="fas fa-map-location-dot"></i>

              </span>

              <span>

                <strong>
                  Maps
                </strong>

                <span>
                  Territorio
                </span>

              </span>

            </a>


            <a
                class="e360-app-item"
                href="#!">

              <span class="e360-app-icon">

                <i class="fas fa-images"></i>

              </span>

              <span>

                <strong>
                  Media
                </strong>

                <span>
                  Recursos visuales
                </span>

              </span>

            </a>


          </div>


        </div>


      </div>


      <!-- USER -->
      <div class="dropdown e360-user-dropdown">


        <a
            class="nav-link e360-user-trigger"
            id="navbarDropdownUser"
            href="#!"
            role="button"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-haspopup="true"
            aria-expanded="false"
            title="Perfil de usuario">


          <span class="e360-user-copy">

            <strong>
              <?= $headerNombreUsuario ?>
            </strong>

            <span>
              <?= $headerTipoUsuario ?>
            </span>

          </span>


          <span class="e360-user-avatar">

            <img
                src="<?= $headerProfileImg ?>"
                alt="User-Profile-Image">

          </span>


        </a>


        <div
            class="dropdown-menu dropdown-menu-end e360-profile-menu"
            aria-labelledby="navbarDropdownUser">


          <div class="e360-profile-hero">


            <div class="e360-profile-large-avatar">

              <img
                  src="<?= $headerProfileImg ?>"
                  alt="User-Profile-Image">

            </div>


            <strong>
              <?= $headerNombreUsuario ?>
            </strong>


            <span>
              <?= $headerTipoUsuario ?>
            </span>


          </div>


          <div class="e360-profile-actions">


            <a
                class="e360-profile-link"
                href="#"
                onclick="PROFILE.editData(<?= $headerUserId ?>)"
                data-bs-toggle="modal"
                data-bs-target="#exampleModalLive">


              <span class="profile-feather">

                <i class="fas fa-user"></i>

              </span>


              <span>
                Editar mi perfil
              </span>


            </a>


            <div class="e360-profile-link">


              <span class="profile-feather">

                <i class="fas fa-id-badge"></i>

              </span>


              <span>
                Rol: <?= $headerTipoUsuario ?>
              </span>


            </div>


          </div>


          <div class="e360-profile-footer">


            <a
                class="e360-logout"
                href="logout.php">

              <i class="fas fa-arrow-right-from-bracket"></i>

              Cerrar sesión

            </a>


          </div>


        </div>


      </div>


    </div>


  </div>


</nav>


<div class="navbar-bottom-line"></div>


<!-- ==========================================================
     PROFILE MODAL
========================================================== -->

<div
    class="modal fade"
    id="exampleModalLive"
    tabindex="-1"
    data-bs-backdrop="static"
    aria-labelledby="exampleModalLiveLabel"
    aria-hidden="true">


  <div
      class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">


    <div class="modal-content">


      <div class="modal-header justify-content-between">


        <div class="e360-modal-title-wrap">


          <div class="e360-modal-icon">

            <i class="fas fa-user-gear"></i>

          </div>


          <div>


            <h5
                class="modal-title"
                id="exampleModalLiveLabel">

              Perfil de usuario

            </h5>


            <div
                style="
                  margin-top:2px;
                  color:rgba(255,255,255,.60);
                  font-size:.58rem;
                  font-weight:600;
                ">

              Actualiza tus datos personales y credenciales.

            </div>


          </div>


        </div>


        <button
            class="btn p-2"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Cerrar">

          <span class="fas fa-times text-white"></span>

        </button>


      </div>


      <div
          class="modal-body"
          style="
            max-height:68vh;
            overflow-y:auto;
          ">


        <form
            id="formusuarios"
            role="form"
            autocomplete="off">


          <input
              type="hidden"
              name="op"
              id="op">


          <input
              type="hidden"
              name="id"
              id="id">


          <span
              id="mensajes"
              class="text-danger mb-2 d-block">
          </span>


          <div class="e360-profile-form-card">


            <div class="row g-3">


              <div class="col-12 col-md-6">


                <label
                    for="nombre_perfil"
                    class="form-label">

                  Nombres completos

                  <span class="text-danger">*</span>

                </label>


                <input
                    type="text"
                    class="form-control"
                    id="nombre_perfil"
                    name="nombre_perfil"
                    placeholder="Ingrese nombres"
                    required>


              </div>


              <div class="col-12 col-md-6">


                <label
                    for="apellido_perfil"
                    class="form-label">

                  Apellidos

                  <span class="text-danger">*</span>

                </label>


                <input
                    type="text"
                    class="form-control"
                    id="apellido_perfil"
                    name="apellido_perfil"
                    placeholder="Ingrese apellidos"
                    required>


              </div>


              <div class="col-12">


                <label class="form-label">

                  Foto de perfil

                </label>


                <div
                    class="e360-photo-uploader"
                    id="my-awesome-dropzone"
                    data-dropzone="data-dropzone">


                  <iframe
                      id="ifm"
                      name="ifm"
                      src="upload.php"
                      scrolling="no"
                      frameborder="0">
                  </iframe>


                </div>


              </div>


              <div class="col-12">


                <label
                    for="nickname_perfil"
                    class="form-label">

                  Usuario / correo

                  <span class="text-danger">*</span>

                </label>


                <input
                    type="email"
                    class="form-control"
                    id="nickname_perfil"
                    name="nickname_perfil"
                    placeholder="usuario@correo.com"
                    required>


              </div>


              <div class="col-12 col-md-6">


                <label
                    for="hashpass_perfil"
                    class="form-label">

                  Contraseña

                  <span class="text-danger">*</span>

                </label>


                <div class="e360-password-wrap">


                  <input
                      type="password"
                      class="form-control"
                      id="hashpass_perfil"
                      name="hashpass_perfil"
                      placeholder="Ingrese contraseña"
                      required>


                  <button
                      class="e360-pass-toggle"
                      type="button"
                      data-password-target="hashpass_perfil"
                      aria-label="Mostrar u ocultar contraseña">

                    <i class="fas fa-eye"></i>

                  </button>


                </div>


              </div>


              <div class="col-12 col-md-6">


                <label
                    for="hashpass1_perfil"
                    class="form-label">

                  Repita la contraseña

                  <span class="text-danger">*</span>

                </label>


                <div class="e360-password-wrap">


                  <input
                      type="password"
                      class="form-control"
                      id="hashpass1_perfil"
                      name="hashpass1_perfil"
                      placeholder="Repita contraseña"
                      required>


                  <button
                      class="e360-pass-toggle"
                      type="button"
                      data-password-target="hashpass1_perfil"
                      aria-label="Mostrar u ocultar contraseña">

                    <i class="fas fa-eye"></i>

                  </button>


                </div>


              </div>


            </div>


          </div>


        </form>


      </div>


      <div class="modal-footer">


        <button
            class="btn btn-outline-secondary e360-modal-btn"
            type="button"
            data-bs-dismiss="modal">

          <i class="fas fa-xmark"></i>

          Cerrar

        </button>


        <button
            class="btn e360-modal-btn e360-modal-save"
            type="button"
            onclick="PROFILE.validateData();">

          <i class="fas fa-floppy-disk"></i>

          Actualizar datos

        </button>


      </div>


    </div>


  </div>


</div>


<!-- ==========================================================
     HEADER HELPERS
========================================================== -->

<script>

document.addEventListener(
  'DOMContentLoaded',
  function(){


    /* mostrar / ocultar password */

    document
      .querySelectorAll(
        '.e360-pass-toggle'
      )
      .forEach(
        function(button){

          button.addEventListener(
            'click',
            function(){

              const targetId =
                button.getAttribute(
                  'data-password-target'
                );

              const field =
                document.getElementById(
                  targetId
                );

              if (!field) {
                return;
              }

              const show =
                field.type ===
                'password';

              field.type =
                show
                  ? 'text'
                  : 'password';

              const icon =
                button.querySelector(
                  'i'
                );

              if (icon){

                icon.classList.toggle(
                  'fa-eye',
                  !show
                );

                icon.classList.toggle(
                  'fa-eye-slash',
                  show
                );

              }

            }
          );

        }
      );



    /* ========================================================
       NORMALIZAR DROPDOWNS EN MOBILE
       Bootstrap/Popper puede inyectar transform inline.
    ======================================================== */

    const profileTrigger =
      document.getElementById(
        'navbarDropdownUser'
      );

    const profileMenu =
      document.querySelector(
        '.e360-user-dropdown .e360-profile-menu'
      );

    const appsTrigger =
      document.getElementById(
        'navbarDropdownNindeDots'
      );

    const appsMenu =
      document.querySelector(
        '.e360-apps-dropdown .e360-app-grid'
      );


    const normalizeMobileDropdown =
      function(menu){

        if (
          !menu
          ||
          window.innerWidth > 575
        ) {
          return;
        }

        menu.style.removeProperty(
          'transform'
        );

        menu.style.removeProperty(
          'inset'
        );

        menu.style.removeProperty(
          'left'
        );

        menu.style.removeProperty(
          'right'
        );

        menu.style.removeProperty(
          'top'
        );
      };


    if (
      profileTrigger
      &&
      profileMenu
    ) {

      profileTrigger.addEventListener(
        'shown.bs.dropdown',
        function(){

          normalizeMobileDropdown(
            profileMenu
          );

        }
      );
    }


    if (
      appsTrigger
      &&
      appsMenu
    ) {

      appsTrigger.addEventListener(
        'shown.bs.dropdown',
        function(){

          normalizeMobileDropdown(
            appsMenu
          );

        }
      );
    }


    /* atajo CTRL/CMD + K para enfocar búsqueda */

    document.addEventListener(
      'keydown',
      function(event){

        const isShortcut =
          (
            event.ctrlKey
            ||
            event.metaKey
          )
          &&
          event.key.toLowerCase()
          ===
          'k';

        if (!isShortcut) {
          return;
        }

        const search =
          document.querySelector(
            '#navbarDefault .search-input'
          );

        if (!search) {
          return;
        }

        event.preventDefault();

        search.focus();

      }
    );

  }
);


/* ============================================================
   ACTUALIZAR FOTO DE PERFIL DESDE upload.php
============================================================ */

window.addEventListener(
  'message',
  function(event){

    /*
      upload.php está en el mismo sitio.
      Si por alguna razón tu uploader se sirve desde otro dominio,
      elimina esta validación.
    */
    if (
      event.origin
      &&
      event.origin !== window.location.origin
    ) {

      return;

    }


    if (
      !event.data
      ||
      !event.data.newImage
    ) {

      return;

    }


    const newImagePath =
      'assets/img/admin/'
      +
      event.data.newImage
      +
      '?t='
      +
      Date.now();


    document
      .querySelectorAll(
        'img[alt="User-Profile-Image"]'
      )
      .forEach(
        function(img){

          img.src =
            newImagePath;

        }
      );

  }
);
</script>


<?php include 'admin/include/gerenic_script.php'; ?>

<script
    type="text/javascript"
    src="./admin/js/lib/data-md5.js">
</script>

<script
    type="text/javascript"
    src="admin/js/profile.js">
</script>
