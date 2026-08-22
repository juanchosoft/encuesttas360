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
   GLOBAL NAVIGATION · SaaS Intelligence Header
========================================================== */

:root{
  --e360-header-h:72px;
  --e360-header-h-md:66px;
  --e360-header-h-sm:62px;

  --e360-nav-950:#07172F;
  --e360-nav-900:#0B2347;
  --e360-nav-850:#102E5C;
  --e360-nav-800:#163B73;

  --e360-blue:#4A89F3;
  --e360-blue-2:#2F66C2;
  --e360-cyan:#28B6DA;

  --e360-line:rgba(255,255,255,.11);
  --e360-white:#FFFFFF;
  --e360-text-soft:rgba(255,255,255,.68);

  --e360-shadow:
    0 15px 38px rgba(5,18,43,.24);

  --e360-pop-shadow:
    0 24px 65px rgba(15,23,42,.20);

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
   NAVBAR
========================================================== */

#navbarDefault.e360-topbar{
  position:fixed !important;
  top:0;
  left:0;
  right:0;

  width:100%;
  height:var(--e360-header-h);
  min-height:var(--e360-header-h);

  z-index:1050;

  padding:0 14px !important;

  border:0 !important;
  border-bottom:1px solid var(--e360-line) !important;

  font-family:var(--e360-font) !important;

  background:
    radial-gradient(
      460px 130px at 8% -35%,
      rgba(74,137,243,.35),
      transparent 72%
    ),
    radial-gradient(
      340px 130px at 92% -40%,
      rgba(40,182,218,.14),
      transparent 72%
    ),
    linear-gradient(
      135deg,
      var(--e360-nav-850) 0%,
      var(--e360-nav-900) 47%,
      var(--e360-nav-950) 100%
    ) !important;

  box-shadow:var(--e360-shadow);

  display:flex;
  align-items:center;
}

#navbarDefault.e360-topbar::before,
#navbarDefault.e360-topbar::after{
  content:none !important;
}

.e360-nav-shell{
  width:100%;
  height:100%;

  display:grid;
  grid-template-columns:auto minmax(260px,470px) auto;
  align-items:center;

  gap:20px;
}

/* ==========================================================
   LEFT · MENU + BRAND
========================================================== */

.e360-nav-left{
  min-width:0;

  display:flex;
  align-items:center;

  gap:12px;
}

#navbarDefault .e360-menu-btn{
  width:46px !important;
  height:46px !important;

  flex:0 0 46px;

  margin:0 !important;
  padding:0 !important;

  border:1px solid rgba(255,255,255,.16) !important;
  border-radius:14px !important;

  color:#fff !important;
  background:rgba(255,255,255,.075) !important;

  display:flex !important;
  align-items:center !important;
  justify-content:center !important;

  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.06),
    0 10px 22px rgba(0,0,0,.16);

  transition:
    transform .18s ease,
    background .18s ease,
    border-color .18s ease,
    box-shadow .18s ease;
}

#navbarDefault .e360-menu-btn:hover{
  transform:translateY(-1px);

  border-color:rgba(255,255,255,.25) !important;
  background:rgba(255,255,255,.13) !important;

  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.08),
    0 14px 28px rgba(0,0,0,.20);
}

#navbarDefault .e360-menu-btn:focus{
  outline:none !important;

  box-shadow:
    0 0 0 4px rgba(74,137,243,.22),
    0 14px 28px rgba(0,0,0,.20) !important;
}

/* hamburger */
.e360-hamburger{
  position:relative;

  width:20px;
  height:16px;

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

  border-radius:999px;

  background:#fff;

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
  top:7px;
}

.e360-hamburger::after{
  top:14px;
}

.e360-menu-btn[aria-expanded="true"]
.e360-hamburger::before{
  top:7px;
  transform:rotate(45deg);
}

.e360-menu-btn[aria-expanded="true"]
.e360-hamburger span{
  width:0;
  opacity:0;
}

.e360-menu-btn[aria-expanded="true"]
.e360-hamburger::after{
  top:7px;
  transform:rotate(-45deg);
}

/* brand */
.e360-brand{
  min-width:0;

  display:flex;
  align-items:center;

  gap:11px;

  padding:0 !important;
  margin:0 !important;

  text-decoration:none !important;
}

#navbarDefault #logoGobierno{
  display:block;

  width:168px;
  max-width:100%;
  height:auto;

  margin:0 !important;

  object-fit:contain;

  filter:
    drop-shadow(
      0 8px 18px rgba(0,0,0,.24)
    );
}

.e360-brand-divider{
  width:1px;
  height:30px;

  flex:0 0 1px;

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
  max-width:180px;

  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;

  color:#fff;

  font-size:.68rem;
  line-height:1.15;

  font-weight:800;

  letter-spacing:.01em;
}

.e360-brand-context span{
  color:rgba(255,255,255,.48);

  font-size:.56rem;

  font-weight:650;
}

/* ==========================================================
   SEARCH
========================================================== */

.e360-search{
  position:relative;

  width:100%;
}

.e360-search .search-input{
  width:100%;
  min-height:42px;

  padding:
    8px 52px 8px 42px !important;

  border:1px solid rgba(255,255,255,.14) !important;
  border-radius:13px !important;

  color:#fff !important;

  background:
    rgba(255,255,255,.075) !important;

  font-size:.70rem;
  font-weight:650;

  outline:none !important;

  backdrop-filter:blur(12px);

  box-shadow:
    inset 0 1px 0 rgba(255,255,255,.035) !important;

  transition:
    border-color .18s ease,
    background .18s ease,
    box-shadow .18s ease;
}

.e360-search .search-input::placeholder{
  color:rgba(255,255,255,.52) !important;
}

.e360-search .search-input:focus{
  border-color:rgba(121,167,255,.55) !important;

  background:
    rgba(255,255,255,.11) !important;

  box-shadow:
    0 0 0 4px rgba(74,137,243,.12) !important;
}

.e360-search-icon{
  position:absolute;

  top:50%;
  left:15px;

  z-index:2;

  transform:translateY(-50%);

  color:rgba(255,255,255,.55);

  font-size:.72rem;

  pointer-events:none;
}

.e360-search-key{
  position:absolute;

  top:50%;
  right:10px;

  z-index:2;

  transform:translateY(-50%);

  min-width:30px;
  height:24px;

  display:flex;
  align-items:center;
  justify-content:center;

  padding:0 7px;

  border:1px solid rgba(255,255,255,.10);
  border-radius:7px;

  color:rgba(255,255,255,.48);
  background:rgba(0,0,0,.12);

  font-size:.52rem;
  font-weight:750;

  pointer-events:none;
}

#navbarDefault
.navbar-top-search-box
.dropdown-menu{
  overflow:hidden;

  margin-top:9px !important;

  border:1px solid #E4E9F1 !important;
  border-radius:14px !important;

  background:#fff !important;

  box-shadow:var(--e360-pop-shadow) !important;
}

/* ==========================================================
   RIGHT CONTROLS
========================================================== */

.e360-nav-right{
  display:flex;
  align-items:center;
  justify-content:flex-end;

  gap:6px;
}

.e360-control{
  position:relative;

  width:40px;
  height:40px;

  display:flex !important;
  align-items:center;
  justify-content:center;

  padding:0 !important;
  margin:0 !important;

  border:1px solid transparent !important;
  border-radius:12px !important;

  color:rgba(255,255,255,.88) !important;
  background:transparent !important;

  transition:
    transform .18s ease,
    border-color .18s ease,
    background .18s ease;
}

.e360-control:hover{
  transform:translateY(-1px);

  border-color:rgba(255,255,255,.11) !important;
  background:rgba(255,255,255,.085) !important;
}

/* theme */
.e360-theme-wrap{
  width:40px;
  height:40px;

  display:flex;
  align-items:center;
  justify-content:center;

  padding:0 !important;

  border-radius:12px;

  transition:
    background .18s ease;
}

.e360-theme-wrap:hover{
  background:rgba(255,255,255,.085);
}

#navbarDefault
.theme-control-toggle-label{
  width:40px !important;
  height:40px !important;

  display:flex !important;
  align-items:center;
  justify-content:center;

  margin:0 !important;

  color:rgba(255,255,255,.90) !important;

  border-radius:12px;

  cursor:pointer;
}

/* app launcher */
.e360-app-grid{
  width:310px;

  padding:12px;

  border:1px solid #E5EAF1 !important;
  border-radius:18px !important;

  background:#fff !important;

  box-shadow:var(--e360-pop-shadow) !important;
}

.e360-app-header{
  display:flex;
  align-items:center;
  justify-content:space-between;

  gap:10px;

  padding:
    5px 4px 12px;

  border-bottom:1px solid #EEF1F5;
}

.e360-app-header strong{
  color:#101828;

  font-size:.71rem;

  font-weight:800;
}

.e360-app-header span{
  color:#98A2B3;

  font-size:.56rem;

  font-weight:650;
}

.e360-app-list{
  display:grid;

  grid-template-columns:
    repeat(2,1fr);

  gap:8px;

  padding-top:11px;
}

.e360-app-item{
  display:flex;
  align-items:center;

  gap:9px;

  min-height:58px;

  padding:9px;

  border:1px solid #E7EBF1;
  border-radius:12px;

  color:#344054 !important;
  background:#FBFCFE;

  text-decoration:none !important;

  transition:
    transform .18s ease,
    border-color .18s ease,
    background .18s ease,
    box-shadow .18s ease;
}

.e360-app-item:hover{
  transform:translateY(-2px);

  border-color:#D3E1F4;

  background:#F5F9FF;

  box-shadow:
    0 9px 20px rgba(15,23,42,.055);
}

.e360-app-icon{
  width:34px;
  height:34px;

  flex:0 0 34px;

  display:flex;
  align-items:center;
  justify-content:center;

  border-radius:10px;

  color:#285FAF;
  background:#EDF4FF;

  font-size:.76rem;
}

.e360-app-item strong{
  display:block;

  color:#344054;

  font-size:.62rem;

  font-weight:800;
}

.e360-app-item span{
  display:block;

  margin-top:1px;

  color:#98A2B3;

  font-size:.51rem;

  font-weight:600;
}

/* ==========================================================
   USER
========================================================== */

.e360-user-trigger{
  min-height:46px;

  display:flex !important;
  align-items:center;

  gap:9px;

  padding:4px 5px 4px 7px !important;

  border:1px solid rgba(255,255,255,.10) !important;
  border-radius:14px !important;

  color:#fff !important;
  background:rgba(255,255,255,.065) !important;

  text-decoration:none !important;

  transition:
    transform .18s ease,
    border-color .18s ease,
    background .18s ease;
}

.e360-user-trigger:hover{
  transform:translateY(-1px);

  border-color:rgba(255,255,255,.18) !important;
  background:rgba(255,255,255,.10) !important;
}

.e360-user-copy{
  max-width:120px;

  display:flex;
  flex-direction:column;

  gap:1px;

  text-align:right;
}

.e360-user-copy strong{
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;

  color:#fff;

  font-size:.62rem;

  font-weight:800;
}

.e360-user-copy span{
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;

  color:rgba(255,255,255,.46);

  font-size:.52rem;

  font-weight:650;
}

.e360-user-avatar{
  position:relative;

  width:36px;
  height:36px;

  flex:0 0 36px;
}

.e360-user-avatar img{
  width:100%;
  height:100%;

  object-fit:cover;

  border:2px solid rgba(255,255,255,.62);
  border-radius:11px;

  box-shadow:
    0 6px 14px rgba(0,0,0,.20);
}

.e360-user-avatar::after{
  content:"";

  position:absolute;

  right:-1px;
  bottom:-1px;

  width:9px;
  height:9px;

  border:2px solid #0B2347;
  border-radius:50%;

  background:#33D58A;
}

/* profile dropdown */
.e360-profile-menu{
  width:310px;

  overflow:hidden;

  margin-top:10px !important;

  padding:0 !important;

  border:1px solid #E5EAF1 !important;
  border-radius:18px !important;

  background:#fff !important;

  box-shadow:var(--e360-pop-shadow) !important;
}

.e360-profile-hero{
  padding:18px 16px 14px;

  text-align:center;

  background:
    radial-gradient(
      200px 100px at 50% -10%,
      rgba(74,137,243,.12),
      transparent 70%
    ),
    linear-gradient(
      180deg,
      #FFFFFF,
      #F8FAFD
    );
}

.e360-profile-large-avatar{
  width:64px;
  height:64px;

  margin:
    0 auto 9px;
}

.e360-profile-large-avatar img{
  width:100%;
  height:100%;

  object-fit:cover;

  border:3px solid #DDE9F9;
  border-radius:18px;

  box-shadow:
    0 9px 24px rgba(32,66,127,.12);
}

.e360-profile-hero strong{
  display:block;

  color:#101828;

  font-size:.75rem;

  font-weight:800;
}

.e360-profile-hero span{
  display:block;

  margin-top:2px;

  color:#98A2B3;

  font-size:.57rem;

  font-weight:650;
}

.e360-profile-actions{
  padding:8px;
}

.e360-profile-link{
  display:flex;

  align-items:center;

  gap:9px;

  min-height:43px;

  padding:8px 10px;

  border-radius:10px;

  color:#344054 !important;

  text-decoration:none !important;

  font-size:.64rem;

  font-weight:700;

  transition:
    color .18s ease,
    background .18s ease;
}

.e360-profile-link:hover{
  color:#245BA7 !important;
  background:#F1F6FF;
}

.e360-profile-link i,
.e360-profile-link .profile-feather{
  width:29px;
  height:29px;

  display:flex;
  align-items:center;
  justify-content:center;

  border-radius:9px;

  color:#285FAF;
  background:#EDF4FF;

  font-size:.68rem;
}

.e360-profile-footer{
  padding:10px;

  border-top:1px solid #EEF1F5;

  background:#FBFCFE;
}

.e360-logout{
  min-height:41px;

  display:flex;

  align-items:center;
  justify-content:center;

  gap:7px;

  border:1px solid #F0D4D5;
  border-radius:11px;

  color:#B42318 !important;
  background:#FFF7F7;

  font-size:.64rem;

  font-weight:800;

  text-decoration:none !important;

  transition:
    transform .18s ease,
    background .18s ease;
}

.e360-logout:hover{
  transform:translateY(-1px);

  background:#FFF0F0;
}

/* ==========================================================
   GLOBAL OFFSET
========================================================== */

/* la línea del tema queda completamente anulada */
.navbar-bottom-line{
  display:none !important;

  height:0 !important;

  margin:0 !important;
  padding:0 !important;

  border:0 !important;

  background:transparent !important;

  box-shadow:none !important;
}

/*
   Solo el body establece el offset superior.
   Así evitamos sumar header + content + main.
*/
body{
  padding-top:
    var(--e360-header-h) !important;
}

main.main,
.pcoded-main-container,
.main-content,
#top{
  margin-top:0 !important;
  padding-top:0 !important;
}

/*
  Las vistas pueden conservar un padding-top pequeño propio
  (.content { padding-top: 12/18px; }), pero nunca otro header.
*/
.content{
  margin-top:0 !important;
}

/* ==========================================================
   PROFILE MODAL
========================================================== */

#exampleModalLive .modal-content{
  overflow:hidden;

  border:1px solid rgba(15,23,42,.09) !important;
  border-radius:22px !important;

  box-shadow:
    0 30px 82px rgba(15,23,42,.25) !important;
}

#exampleModalLive .modal-header{
  position:relative;

  overflow:hidden;

  padding:17px 19px !important;

  border:0 !important;

  color:#fff !important;

  background:
    radial-gradient(
      320px 130px at 8% -15%,
      rgba(74,137,243,.36),
      transparent 72%
    ),
    linear-gradient(
      135deg,
      #173D79,
      #102A56 55%,
      #081B38
    ) !important;
}

#exampleModalLive .modal-title{
  position:relative;

  z-index:2;

  color:#fff !important;

  font-family:var(--e360-font);

  font-size:.90rem;

  font-weight:800;
}

.e360-modal-title-wrap{
  display:flex;

  align-items:center;

  gap:10px;
}

.e360-modal-icon{
  width:38px;
  height:38px;

  display:flex;

  align-items:center;
  justify-content:center;

  border:1px solid rgba(255,255,255,.16);
  border-radius:12px;

  background:rgba(255,255,255,.10);
}

#exampleModalLive .modal-body{
  padding:17px !important;

  background:
    linear-gradient(
      180deg,
      #FBFCFE,
      #F5F8FC
    );
}

.e360-profile-form-card{
  padding:14px;

  border:1px solid #E5EAF1;
  border-radius:16px;

  background:#fff;

  box-shadow:
    0 8px 20px rgba(15,23,42,.04);
}

#exampleModalLive .form-label{
  margin-bottom:6px;

  color:#475467;

  font-size:.64rem;

  font-weight:800;
}

#exampleModalLive .form-control{
  min-height:44px;

  border:1px solid #D9E0EA !important;
  border-radius:11px !important;

  color:#344054;

  background:#FBFCFE;

  font-size:.71rem;

  font-weight:600;

  box-shadow:none !important;
}

#exampleModalLive .form-control:focus{
  border-color:#4A89F3 !important;

  background:#fff;

  box-shadow:
    0 0 0 4px rgba(74,137,243,.10) !important;
}

.e360-photo-uploader{
  overflow:hidden;

  border:1px dashed #BFCFE3;
  border-radius:13px;

  background:#fff;
}

#ifm{
  display:block;

  width:100% !important;
  height:175px !important;

  border:0 !important;
}

.e360-password-wrap{
  position:relative;
}

.e360-password-wrap .form-control{
  padding-right:42px;
}

.e360-pass-toggle{
  position:absolute;

  top:50%;
  right:7px;

  width:31px;
  height:31px;

  transform:translateY(-50%);

  display:flex;
  align-items:center;
  justify-content:center;

  padding:0;

  border:0;
  border-radius:9px;

  color:#667085;
  background:transparent;

  transition:
    color .18s ease,
    background .18s ease;
}

.e360-pass-toggle:hover{
  color:#245BA7;
  background:#EEF5FF;
}

#exampleModalLive .modal-footer{
  padding:11px 17px !important;

  border-top:1px solid #E7EBF1 !important;

  background:#fff;
}

.e360-modal-btn{
  min-height:41px;

  display:inline-flex;
  align-items:center;
  justify-content:center;

  gap:7px;

  padding:8px 13px;

  border-radius:11px !important;

  font-size:.65rem;

  font-weight:800;
}

.e360-modal-save{
  border:0 !important;

  color:#fff !important;

  background:
    linear-gradient(
      135deg,
      #4A89F3,
      #285FAF
    ) !important;

  box-shadow:
    0 9px 18px rgba(40,95,175,.18);
}

/* ==========================================================
   DARK MODE
========================================================== */

html[data-bs-theme="dark"]
#navbarDefault.e360-topbar{
  background:
    radial-gradient(
      460px 130px at 8% -35%,
      rgba(74,137,243,.23),
      transparent 72%
    ),
    linear-gradient(
      135deg,
      #0A1528,
      #07111F
    ) !important;
}

html[data-bs-theme="dark"]
.e360-profile-menu,
html[data-bs-theme="dark"]
.e360-app-grid{
  border-color:#293548 !important;
  background:#111A29 !important;
}

html[data-bs-theme="dark"]
.e360-profile-hero,
html[data-bs-theme="dark"]
.e360-profile-footer,
html[data-bs-theme="dark"]
.e360-app-item{
  background:#141F30 !important;
}

html[data-bs-theme="dark"]
.e360-profile-hero strong,
html[data-bs-theme="dark"]
.e360-app-header strong,
html[data-bs-theme="dark"]
.e360-app-item strong{
  color:#F8FAFC !important;
}

html[data-bs-theme="dark"]
.e360-profile-link{
  color:#D8E0EB !important;
}

/* logo consistente */
#logoGobierno{
  content:
    url("assets/img/estadistica4.png");
}

html[data-bs-theme="dark"]
#logoGobierno{
  content:
    url("assets/img/estadistica4.png");
}

/* ==========================================================
   RESPONSIVE
========================================================== */

@media (max-width:1199.98px){

  .e360-nav-shell{
    grid-template-columns:
      auto
      minmax(210px,1fr)
      auto;

    gap:12px;
  }

  .e360-brand-context{
    display:none;
  }

  .e360-brand-divider{
    display:none;
  }

  #navbarDefault #logoGobierno{
    width:150px;
  }

  .e360-user-copy{
    display:none;
  }
}


@media (max-width:991.98px){

  :root{
    --e360-header-h:
      var(--e360-header-h-md);
  }

  #navbarDefault.e360-topbar{
    height:var(--e360-header-h-md);
    min-height:var(--e360-header-h-md);

    padding:
      0 11px !important;
  }

  body{
    padding-top:
      var(--e360-header-h-md) !important;
  }

  .e360-nav-shell{
    grid-template-columns:
      minmax(0,1fr)
      auto;

    gap:10px;
  }

  .e360-search-column{
    display:none;
  }

  .e360-nav-left{
    min-width:0;
  }

  #navbarDefault #logoGobierno{
    width:142px;
  }
}


@media (max-width:575.98px){

  :root{
    --e360-header-h:
      var(--e360-header-h-sm);
  }

  #navbarDefault.e360-topbar{
    height:var(--e360-header-h-sm);
    min-height:var(--e360-header-h-sm);

    padding:
      0 8px !important;
  }

  body{
    padding-top:
      var(--e360-header-h-sm) !important;
  }

  .e360-nav-shell{
    gap:5px;
  }

  .e360-nav-left{
    gap:7px;
  }

  #navbarDefault .e360-menu-btn{
    width:42px !important;
    height:42px !important;

    flex:
      0 0 42px;

    border-radius:
      12px !important;
  }

  #navbarDefault #logoGobierno{
    width:118px;
  }

  .e360-nav-right{
    gap:1px;
  }

  .e360-control,
  .e360-theme-wrap,
  #navbarDefault
  .theme-control-toggle-label{
    width:36px !important;
    height:36px !important;
  }

  .e360-user-trigger{
    min-height:40px;

    padding:
      2px !important;

    border-color:
      transparent !important;

    background:
      transparent !important;
  }

  .e360-user-avatar{
    width:34px;
    height:34px;

    flex:
      0 0 34px;
  }

  .e360-profile-menu{
    width:
      min(
        300px,
        calc(100vw - 18px)
      );
  }

  .e360-app-grid{
    width:
      min(
        300px,
        calc(100vw - 18px)
      );
  }
}


@media (prefers-reduced-motion:reduce){

  *,
  *::before,
  *::after{
    animation-duration:.01ms !important;
    animation-iteration-count:1 !important;
    transition-duration:.01ms !important;
    scroll-behavior:auto !important;
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
      <div class="dropdown">


        <a
            class="nav-link e360-control"
            id="navbarDropdownNindeDots"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            data-bs-auto-close="outside"
            aria-expanded="false"
            aria-label="Herramientas rápidas">


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
      <div class="dropdown">


        <a
            class="nav-link e360-user-trigger"
            id="navbarDropdownUser"
            href="#!"
            role="button"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-haspopup="true"
            aria-expanded="false">


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


<?php
include './admin/include/asistentevirtual.php';
?>


<!-- ==========================================================
     CHAT + HEADER HELPERS
========================================================== -->

<script>
/* ============================================================
   ASISTENTE VIRTUAL
   Misma funcionalidad, con validaciones para evitar errores JS.
============================================================ */

function togglebot(){

  const botPopup =
    document.getElementById(
      'botPopup'
    );

  if (!botPopup) {
    return;
  }

  const hidden =
    botPopup.style.display === 'none'
    ||
    botPopup.style.display === '';

  botPopup.style.display =
    hidden
      ? 'block'
      : 'none';
}


async function sendMessage(event){

  if (
    event
    &&
    typeof event.stopPropagation === 'function'
  ) {

    event.stopPropagation();

  }

  const input =
    document.getElementById(
      'user-input'
    );

  const chatBox =
    document.getElementById(
      'chat-box'
    );

  if (
    !input
    ||
    !chatBox
  ) {

    return;

  }

  const userInput =
    input.value
      .trim();

  if (!userInput) {
    return;
  }


  const intro =
    document.getElementById(
      'intro-message'
    );

  if (intro) {
    intro.style.display =
      'none';
  }


  const ayudaRapida =
    document.getElementById(
      'ayuda-rapida'
    );

  if (
    ayudaRapida
    &&
    ayudaRapida.parentElement
  ) {

    ayudaRapida
      .parentElement
      .style
      .display =
      'none';
  }


  const userMessage =
    document.createElement(
      'div'
    );

  userMessage
    .classList
    .add(
      'chat-message',
      'user'
    );

  userMessage.textContent =
    `Tú: ${userInput}`;

  chatBox.appendChild(
    userMessage
  );


  input.value = '';

  chatBox.scrollTop =
    chatBox.scrollHeight;


  try{

    const response =
      await fetch(
        'chatgpt_handler.php',
        {
          method:'POST',
          headers:{
            'Content-Type':
              'application/json'
          },
          body:JSON.stringify({
            message:userInput
          })
        }
      );


    if (!response.ok){

      throw new Error(
        `Error en la respuesta: ${response.statusText}`
      );

    }


    const data =
      await response.json();


    const botMessage =
      document.createElement(
        'div'
      );

    botMessage
      .classList
      .add(
        'chat-message',
        'bot'
      );

    botMessage.textContent =
      `Asistente Virtual: ${data.response}`;

    chatBox.appendChild(
      botMessage
    );

    chatBox.scrollTop =
      chatBox.scrollHeight;


    if (
      'speechSynthesis'
      in
      window
      &&
      data.response
    ) {

      const speech =
        new SpeechSynthesisUtterance(
          data.response
        );

      speech.lang =
        'es-MX';

      speech.rate =
        1;

      window
        .speechSynthesis
        .speak(
          speech
        );

    }


  } catch(error){

    console.error(
      'Error en la solicitud:',
      error
    );


    const errorMessage =
      document.createElement(
        'div'
      );

    errorMessage
      .classList
      .add(
        'chat-message',
        'error'
      );

    errorMessage.textContent =
      'Error: No se pudo obtener la respuesta del servidor.';

    chatBox.appendChild(
      errorMessage
    );

    chatBox.scrollTop =
      chatBox.scrollHeight;

  }

}


document.addEventListener(
  'DOMContentLoaded',
  function(){

    /* envío con Enter */

    const userInput =
      document.getElementById(
        'user-input'
      );

    if (userInput){

      userInput.addEventListener(
        'keypress',
        function(event){

          if (event.key === 'Enter'){

            event.preventDefault();

            sendMessage(
              event
            );

          }

        }
      );

    }


    /* ayuda rápida */

    const ayudaRapida =
      document.getElementById(
        'ayuda-rapida'
      );

    if (ayudaRapida){

      ayudaRapida.addEventListener(
        'click',
        function(event){

          event.preventDefault();

          const input =
            document.getElementById(
              'user-input'
            );

          if (!input) {
            return;
          }

          input.value =
            'Necesito ayuda con algo';

          sendMessage(
            event
          );

        }
      );

    }


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
