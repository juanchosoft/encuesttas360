<?php
// index.php - 360 Estadísticas
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="theme-color" content="#041020" />

  <title>Encuestas Colombia en Tiempo Real | 360 Estadísticas</title>
  <meta name="description" content="Encuestas Colombia: consulta resultados en tiempo real, análisis por tendencias y participación ciudadana. 360 Estadísticas: visualizaciones claras y listas para compartir." />
  <meta name="keywords" content="encuestas colombia, encuestas en colombia, resultados de encuestas, encuesta del momento, participación ciudadana, estadisticas colombia" />
  <meta name="robots" content="index, follow, max-image-preview:large" />
  <link rel="canonical" href="https://estadisticas360.com/" />

  <meta property="og:locale" content="es_CO" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="360 Estadísticas" />
  <meta property="og:title" content="Encuestas Colombia | 360 Estadísticas" />
  <meta property="og:description" content="Resultados de encuestas en Colombia en tiempo real. Filtra, analiza y comparte con una presentación profesional." />
  <meta property="og:url" content="https://estadisticas360.com/" />
  <meta property="og:image" content="https://estadisticas360.com/assets/img/og/estadisticas360-og.jpg?v=9" />
  <meta property="og:image:url" content="https://estadisticas360.com/assets/img/og/estadisticas360-og.jpg?v=9" />
  <meta property="og:image:secure_url" content="https://estadisticas360.com/assets/img/og/estadisticas360-og.jpg?v=9" />
  <meta property="og:image:type" content="image/jpeg" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="360 Estadísticas - Encuestas Colombia" />

  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Encuestas Colombia | 360 Estadísticas" />
  <meta name="twitter:description" content="Resultados en tiempo real y visualización profesional para compartir." />
  <meta name="twitter:image" content="https://estadisticas360.com/assets/img/og/estadisticas360-og.jpg?v=9" />

  <link rel="icon" href="assets/img/admin/favicon.ico" />
  <link rel="apple-touch-icon" href="assets/img/admin/favicon.ico" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900;950&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"WebSite",
    "name":"360 Estadísticas",
    "url":"https://estadisticas360.com/",
    "description":"Encuestas Colombia en tiempo real con análisis y visualización profesional.",
    "inLanguage":"es-CO"
  }
  </script>

<link rel="stylesheet" href="./css/index.css?v=<?= time(); ?>">
</head>

<body>
  <div class="page-bg" aria-hidden="true"></div>

  <header class="topbar">
    <div class="topbar-inner">
      <a href="index.php" class="brand" aria-label="Inicio 360 Estadísticas">
        <span class="brand-logo">
          <img src="assets/img/360 Estadisticas-04.png" alt="360 Estadísticas">
        </span>

        <span class="brand-copy">
          <b>360 Estadísticas</b>
          <span>Participación ciudadana</span>
        </span>
      </a>

      <div class="topbar-actions">
        <div class="badge-live" title="Encuesta activa">
          <span class="pulse"></span>
          <span>Encuesta activa hoy</span>
        </div>

        <div class="top-pill" title="Tiempo estimado">
          <span class="dot"></span>
          <span>~60 segundos</span>
        </div>
      </div>
    </div>
  </header>

  <main class="page">
    <section class="hero-shell" id="heroCard">
      <div class="hero-grid">

        <div class="hero-copy">
          <div class="kicker">
            <i class="fa-solid fa-chart-line"></i>
            Tu opinión mueve la información
          </div>

          <h1 class="hero-title">
            Bienvenido a
            <span>360 Estadísticas</span>
          </h1>

          <p class="lead">
            Participa en la encuesta del momento en <b>menos de 60 segundos</b>.
            Accede, responde y conoce resultados con una experiencia moderna, clara y segura.
          </p>

          <div class="hero-stats">
            <div class="hero-stat">
              <b>30s</b>
              <span>Tiempo promedio de respuesta</span>
            </div>

            <div class="hero-stat">
              <b>360°</b>
              <span>Lectura clara del comportamiento ciudadano</span>
            </div>

            <div class="hero-stat">
              <b>Real</b>
              <span>Datos listos para análisis territorial</span>
            </div>
          </div>

          <div class="cta-wrap">
            <div class="login-box">
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

            <a href="registro.php" class="btn-register">
              <span>Registrarme</span>
              <i class="fa-solid fa-user-plus"></i>
            </a>
          </div>

          <div class="hero-note">
            <b>Seguro e intuitivo:</b> inicia sesión o crea tu cuenta para continuar con la encuesta disponible.
          </div>
        </div>

        <div class="hero-panel">
          <div class="panel-stack">

            <div class="panel-card panel-main">
              <div class="panel-mini">
                <div class="mini-kpi">
                  <div class="icon">
                    <i class="fa-solid fa-clock"></i>
                  </div>
                  <b>30s</b>
                  <span>Llena el formulario y responde rápidamente.</span>
                </div>

                <div class="mini-kpi">
                  <div class="icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                  </div>
                  <b>CO</b>
                  <span>Resultados pensados para lectura territorial.</span>
                </div>
              </div>

              <div class="panel-banner">
                <small>
                  <i class="fa-solid fa-signal"></i>
                  Encuestas en tiempo real
                </small>

                <h3>Información clara para tomar mejores decisiones.</h3>

                <p>
                  360 Estadísticas permite responder encuestas, organizar datos y visualizar tendencias de forma profesional.
                </p>
              </div>
            </div>

            <div class="panel-card panel-main">
              <div class="feature-list">
                <div class="feature-item">
                  <div class="fi-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                  </div>
                  <div>
                    <b>Acceso seguro</b>
                    <span>Ingresa con tu usuario para validar tu participación.</span>
                  </div>
                </div>

                <div class="feature-item">
                  <div class="fi-icon">
                    <i class="fa-solid fa-chart-simple"></i>
                  </div>
                  <div>
                    <b>Resultados visuales</b>
                    <span>Consulta información consolidada y fácil de interpretar.</span>
                  </div>
                </div>

                <div class="feature-item">
                  <div class="fi-icon">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                  </div>
                  <div>
                    <b>Diseño responsive</b>
                    <span>Optimizado para celular, tablet y computador.</span>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </section>
  </main>

  <footer class="footer-360">
    <div class="footer-inner">
      <span id="ultima-actualizacion" class="footer-date">
        <i class="fas fa-calendar-check"></i>
        <span>Cargando fecha...</span>
      </span>

      <span class="footer-copy">
        <i class="fas fa-copyright"></i>
        <span>
          <strong>estadisticas360.com</strong>. Todos los derechos reservados.
        </span>
      </span>

      <div class="footer-social" aria-label="Redes sociales">
        <a href="#" aria-label="X">
          <span style="font-weight:950;">𝕏</span>
        </a>

        <a href="#" aria-label="Facebook">
          <i class="fab fa-facebook-f"></i>
        </a>

        <a href="#" aria-label="Instagram">
          <i class="fab fa-instagram"></i>
        </a>

        <a href="#" aria-label="YouTube">
          <i class="fab fa-youtube"></i>
        </a>
      </div>
    </div>
  </footer>

  <!-- MODAL LOGIN -->
  <div class="modal fade modal-saas" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header modal-360-header">
          <div class="d-flex align-items-center gap-3">
            <div class="modal-logo">
              <img src="assets/img/360 Estadisticas-04.png" alt="360 Estadísticas">
            </div>

            <div>
              <h5 class="modal-title text-white mb-0" style="font-weight:950;">
                Iniciar sesión
              </h5>
              <small style="font-weight:750;color:rgba(255,255,255,.86);">
                Accede para continuar
              </small>
            </div>
          </div>

          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-360-body">
          <div class="form-card">
            <form id="formLoginVotantes" autocomplete="on">
              <div class="mb-3">
                <div class="input-label">Usuario o correo</div>
                <input type="text" id="login_user" name="login_user" class="input-360" placeholder="Escribe tu usuario o correo" autocomplete="username" required>
              </div>

              <div class="mb-2">
                <div class="input-label">Contraseña</div>

                <div class="password-wrap">
                  <input type="password" id="login_password" name="login_password" class="input-360" placeholder="Escribe tu contraseña" autocomplete="current-password" required>

                  <button type="button" class="toggle-pass" id="togglePassword" aria-label="Ver u ocultar contraseña">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                  </button>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <small class="text-muted fw-bold">
                  Tus datos se validan de forma segura.
                </small>

                <a href="#" class="link-forgot" id="openForgotModal">
                  <i class="fa-solid fa-key"></i>
                  Olvidé mi contraseña
                </a>
              </div>

              <button type="button" class="btn-login" id="btnLoginSubmit">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Entrar
              </button>

              <div class="text-center mt-3">
                <small class="text-muted fw-bold">
                  ¿No tienes cuenta?
                  <a href="registro.php" style="color:#0879b8;font-weight:950;">Regístrate aquí</a>
                </small>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- MODAL RECUPERAR CONTRASEÑA -->
  <div class="modal fade modal-saas" id="forgotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">

        <div class="modal-header modal-360-header">
          <div class="d-flex align-items-center gap-3">
            <div class="modal-logo">
              <i class="fa-solid fa-key text-white" style="font-size:22px;"></i>
            </div>

            <div>
              <h5 class="modal-title text-white mb-0" style="font-weight:950;">
                Recuperar contraseña
              </h5>
              <small style="font-weight:750;color:rgba(255,255,255,.86);">
                Te enviaremos una contraseña temporal
              </small>
            </div>
          </div>

          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-360-body">
          <div class="form-card">
            <form id="formForgotPassword" autocomplete="off">
              <div class="mb-3">
                <div class="input-label">Correo o usuario</div>

                <input type="text" id="forgot_login" name="forgot_login" class="input-360" placeholder="Escribe tu correo o usuario" required>

                <small class="text-muted fw-bold d-block mt-2">
                  Si el registro existe, enviaremos la contraseña temporal al correo asociado.
                </small>
              </div>

              <div id="forgot_ok" class="alert-mini ok"></div>
              <div id="forgot_bad" class="alert-mini bad"></div>

              <button type="submit" class="btn-forgot" id="btnForgotSend">
                <i class="fa-solid fa-paper-plane"></i>
                Enviar contraseña temporal
              </button>

              <button type="button" class="btn-back mt-2" id="btnBackToLogin">
                <i class="fa-solid fa-arrow-left"></i>
                Volver al login
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>

  <script>
    (function () {
      const card = document.getElementById('heroCard');
      if (!card) return;

      const isTouch = matchMedia('(hover: none)').matches;
      if (isTouch) return;

      let raf = null;

      window.addEventListener('mousemove', (e) => {
        if (raf) cancelAnimationFrame(raf);

        raf = requestAnimationFrame(() => {
          const r = card.getBoundingClientRect();
          const x = (e.clientX - r.left) / r.width;
          const y = (e.clientY - r.top) / r.height;
          const rx = (y - 0.5) * -1.6;
          const ry = (x - 0.5) * 1.6;

          card.style.transform = `perspective(1100px) rotateX(${rx}deg) rotateY(${ry}deg)`;
        });
      });

      window.addEventListener('mouseleave', () => {
        card.style.transform = '';
      });
    })();
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const fecha = new Date();

      const meses = [
        "enero", "febrero", "marzo", "abril", "mayo", "junio",
        "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
      ];

      const contenedorFecha = document.getElementById("ultima-actualizacion");

      if (contenedorFecha) {
        contenedorFecha.innerHTML = `
          <i class="fas fa-calendar-check"></i>
          <span>Última actualización: ${fecha.getDate()} de ${meses[fecha.getMonth()]} ${fecha.getFullYear()}</span>
        `;
      }

      function getOrCreateModal(el, options = undefined) {
        if (!el || typeof bootstrap === "undefined" || !bootstrap.Modal) return null;
        return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el, options);
      }

      const loginModalEl = document.getElementById("loginModal");
      const forgotModalEl = document.getElementById("forgotModal");

      function cleanupBackdrop() {
        document.querySelectorAll(".modal-backdrop").forEach(b => b.remove());
        document.body.classList.remove("modal-open");
        document.body.style.removeProperty("padding-right");
      }

      function showLoginModal() {
        const instance = getOrCreateModal(loginModalEl, {
          backdrop: "static",
          keyboard: false
        });

        if (!instance) return;

        instance.show();

        loginModalEl.addEventListener("shown.bs.modal", function onShown() {
          loginModalEl.removeEventListener("shown.bs.modal", onShown);
          const u = document.getElementById("login_user");
          if (u) u.focus();
        });
      }

      document.addEventListener("click", function (e) {
        const btn = e.target.closest("#btnOpenLogin");
        if (!btn) return;

        e.preventDefault();
        showLoginModal();
      });

      const pass = document.getElementById("login_password");
      const toggleBtn = document.getElementById("togglePassword");
      const eyeIcon = document.getElementById("eyeIcon");

      if (toggleBtn && pass) {
        toggleBtn.addEventListener("click", () => {
          const isPass = pass.type === "password";

          pass.type = isPass ? "text" : "password";

          if (eyeIcon) {
            eyeIcon.classList.toggle("fa-eye", !isPass);
            eyeIcon.classList.toggle("fa-eye-slash", isPass);
          }
        });
      }

      const openForgot = document.getElementById("openForgotModal");
      const btnBack = document.getElementById("btnBackToLogin");

      if (openForgot && loginModalEl && forgotModalEl) {
        openForgot.addEventListener("click", (e) => {
          e.preventDefault();

          const loginInstance = getOrCreateModal(loginModalEl);
          if (!loginInstance) return;

          loginModalEl.addEventListener("hidden.bs.modal", function handler() {
            loginModalEl.removeEventListener("hidden.bs.modal", handler);

            cleanupBackdrop();

            const forgotInstance = getOrCreateModal(forgotModalEl);
            if (!forgotInstance) return;

            forgotInstance.show();

            forgotModalEl.addEventListener("shown.bs.modal", function handler2() {
              forgotModalEl.removeEventListener("shown.bs.modal", handler2);

              const inp = document.getElementById("forgot_login");
              if (inp) inp.focus();
            });
          }, { once: true });

          loginInstance.hide();
        });
      }

      if (btnBack && loginModalEl && forgotModalEl) {
        btnBack.addEventListener("click", () => {
          const forgotInstance = getOrCreateModal(forgotModalEl);
          if (!forgotInstance) return;

          forgotModalEl.addEventListener("hidden.bs.modal", function handler() {
            forgotModalEl.removeEventListener("hidden.bs.modal", handler);

            cleanupBackdrop();

            const loginInstance = getOrCreateModal(loginModalEl, {
              backdrop: "static",
              keyboard: false
            });

            if (!loginInstance) return;

            loginInstance.show();

            loginModalEl.addEventListener("shown.bs.modal", function handler2() {
              loginModalEl.removeEventListener("shown.bs.modal", handler2);

              const u = document.getElementById("login_user");
              if (u) u.focus();
            });
          }, { once: true });

          forgotInstance.hide();
        });
      }

      const formForgot = document.getElementById("formForgotPassword");
      const btnForgotSend = document.getElementById("btnForgotSend");
      const ok = document.getElementById("forgot_ok");
      const bad = document.getElementById("forgot_bad");

      const showMsg = (el, txt) => {
        if (!el) return;
        el.textContent = txt;
        el.style.display = "block";
      };

      const hideMsg = (el) => {
        if (!el) return;
        el.style.display = "none";
      };

      if (formForgot) {
        formForgot.addEventListener("submit", async (e) => {
          e.preventDefault();

          hideMsg(ok);
          hideMsg(bad);

          const login = (document.getElementById("forgot_login")?.value || "").trim();

          if (!login) {
            showMsg(bad, "Escribe tu correo o usuario.");
            return;
          }

          if (btnForgotSend) {
            btnForgotSend.disabled = true;
            btnForgotSend.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';
          }

          try {
            const fd = new FormData();
            fd.append("login", login);

            // Construye la URL base dinámicamente
            const baseUrl = window.location.pathname.includes('/Github/estadisticas360/') 
              ? '/Github/estadisticas360/' 
              : '/';
            const res = await fetch(baseUrl + "admin/ajax/auth_forgot_password.php", {
              method: "POST",
              body: fd
            });

            if (!res.ok) {
              throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }

            const data = await res.json();

            if (data && data.ok) {
              showMsg(ok, data.msg || "Si existe, te enviamos una contraseña temporal.");
              hideMsg(bad);
              formForgot.reset();
            } else {
              const errorMsg = (data && data.msg) ? data.msg : "No fue posible procesar la solicitud.";
              showMsg(bad, errorMsg);
              hideMsg(ok);
            }
          } catch (err) {
            showMsg(bad, `Error de red: ${err.message}. Intenta nuevamente.`);
            hideMsg(ok);
            console.error(err);
          } finally {
            if (btnForgotSend) {
              btnForgotSend.disabled = false;
              btnForgotSend.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar contraseña temporal';
            }
          }
        });
      }

      document.getElementById("btnLoginSubmit")?.addEventListener("click", async function () {
        const nickname = document.getElementById("login_user")?.value.trim() || "";
        const hashpass = document.getElementById("login_password")?.value.trim() || "";

        if (!nickname || !hashpass) {
          Swal.fire("Error", "Por favor completa todos los campos.", "error");
          return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Validando...';

        const formData = new FormData();
        formData.append("nickname", nickname);
        formData.append("hashpass", hashpass);

        try {
          const res = await fetch("login_process.php", {
            method: "POST",
            body: formData
          });

          const data = await res.json();

          if (data.status === "success") {
            window.location.href = data.redirect;
          } else {
            Swal.fire("Error", data.message || "Error de inicio de sesión.", "error");
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar';
          }
        } catch (err) {
          Swal.fire("Error", "Error de conexión con el servidor.", "error");
          console.error(err);

          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar';
        }
      });

      document.getElementById("formLoginVotantes")?.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          e.preventDefault();
          document.getElementById("btnLoginSubmit")?.click();
        }
      });
    });
  </script>
</body>
</html>