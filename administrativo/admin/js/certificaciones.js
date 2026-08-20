$(document).on("ready", init);

let MAPS_READY = false;
let LAST_CERT_FOR_MAP = null;
let MAP_INSTANCE = null;
let MAP_MARKER = null;

function init() {
  // ✅ DataTable PRO (más ancha, más usable)
  if ($("#tblCertificaciones").length > 0 && $.fn.DataTable) {
    if (!$.fn.DataTable.isDataTable("#tblCertificaciones")) {
      $("#tblCertificaciones").DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
        order: [[0, "desc"]],
        pageLength: 25,
        responsive: true,
        autoWidth: false,
        // Si algún día se te vuelve a romper el ancho, activa esto:
        // scrollX: true,
        columnDefs: [
          { targets: [0,1,5,6,7], className: "text-nowrap" }
        ],
      });
    }
  }

  // ✅ Cuando el modal termine de mostrarse: si hay cert pendiente, pinta mapa
  const modalEl = document.getElementById("modalDetalleCertificacion");
  if (modalEl) {
    modalEl.addEventListener("shown.bs.modal", function () {
      if (LAST_CERT_FOR_MAP) {
        CERTIFICACIONES.renderMap(LAST_CERT_FOR_MAP);
      }
    });

    modalEl.addEventListener("hidden.bs.modal", function () {
      LAST_CERT_FOR_MAP = null;
      MAP_INSTANCE = null;
      MAP_MARKER = null;
    });
  }
}

/**
 * ✅ Google Maps callback (tiene que existir global)
 * Google llamará initMap() cuando cargue su script
 */
window.initMap = function () {
  MAPS_READY = true;
};

// Helpers
function escapeHtml(str) {
  return String(str ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function formatFecha(cert) {
  try {
    const fecha = new Date(cert.fecha_certificacion);
    return fecha.toLocaleDateString("es-CO", {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (e) {
    return cert.fecha_certificacion || "N/A";
  }
}

/**
 * ✅ Acepta audio en:
 * - data URL: "data:audio/webm;base64,...."
 * - base64 crudo: "AAAAIGZ0eXB3ZWJt...."
 */
function normalizeAudioSrc(cert) {
  const raw = cert.audio_base64 || "";
  if (!raw) return "";

  if (raw.startsWith("data:")) return raw;

  const mime = (cert.audio_formato || "audio/webm").trim();
  return `data:${mime};base64,${raw}`;
}


const CERTIFICACIONES = {
  verDetalle: function (id) {
    // Abrir modal
    const modalEl = document.getElementById("modalDetalleCertificacion");
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    $("#modalDetalleCertificacionBody").html(`
      <div class="text-center py-5">
        <i class="fas fa-spinner fa-spin fa-3x" style="color:#20427F"></i>
        <p class="mt-3 mb-0" style="font-weight:800;color:#64748b">Cargando información...</p>
      </div>
    `);

    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: { op: "certificaciondetalle", id: id },
      success: function (response) {
        if (response?.output?.valid) {
          const cert = response.output.response || {};
          CERTIFICACIONES.renderDetalle(cert);
        } else {
          UTIL.mostrarMensajeError("Error al cargar certificación");
          $("#modalDetalleCertificacionBody").html(`
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-circle me-2"></i>
              Error al cargar la certificación
            </div>
          `);
        }
      },
      error: function () {
        UTIL.mostrarMensajeError("Error de conexión");
        $("#modalDetalleCertificacionBody").html(`
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            Error de conexión al servidor
          </div>
        `);
      },
    });
  },

  renderDetalle: function (cert) {
    const fechaFormato = formatFecha(cert);

    // Origen
    let origenHtml = "";
    if (cert.origen_tipo === "sondeo") {
      origenHtml = `
        <div class="alert alert-primary mb-0">
          <div class="d-flex gap-3">
            <div><i class="fas fa-poll fa-2x"></i></div>
            <div>
              <div style="font-weight:900">Sondeo</div>
              <div style="font-weight:800">${escapeHtml(cert.sondeo_nombre || "N/A")}</div>
              ${cert.sondeo_descripcion ? `<div class="small mt-1">${escapeHtml(cert.sondeo_descripcion)}</div>` : ""}
            </div>
          </div>
        </div>
      `;
    } else if (cert.origen_tipo === "cuestionario") {
      origenHtml = `
        <div class="alert alert-info mb-0">
          <div class="d-flex gap-3">
            <div><i class="fas fa-clipboard-list fa-2x"></i></div>
            <div>
              <div style="font-weight:900">Cuestionario</div>
              <div style="font-weight:800">${escapeHtml(cert.cuestionario_nombre || "N/A")}</div>
              ${cert.cuestionario_realizada_por ? `<div class="small mt-1">Realizada por: ${escapeHtml(cert.cuestionario_realizada_por)}</div>` : ""}
            </div>
          </div>
        </div>
      `;
    } else {
      origenHtml = `
        <div class="alert alert-secondary mb-0">
          <div class="d-flex gap-3">
            <div><i class="fas fa-user fa-2x"></i></div>
            <div>
              <div style="font-weight:900">Registro Simple</div>
              <div class="small">Registro de votante sin sondeo ni cuestionario</div>
            </div>
          </div>
        </div>
      `;
    }

    // GPS
    const hasGps = cert.latitud && cert.longitud;
    const lat = hasGps ? parseFloat(cert.latitud) : null;
    const lng = hasGps ? parseFloat(cert.longitud) : null;

    const gpsKpi = hasGps
      ? `
        <div class="kpi">
          <div class="label">Ubicación</div>
          <div class="value">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
          <div class="small text-muted mt-1">Precisión: ±${Math.round(cert.precision_metros || 0)}m ${cert.altitud ? `• Altitud: ${Math.round(cert.altitud)}m` : ""}</div>
          <div class="mt-2 d-flex gap-2 flex-wrap">
            <a class="btn btn-sm btn-primary" target="_blank"
              href="https://www.google.com/maps?q=${encodeURIComponent(lat + "," + lng)}">
              <i class="fas fa-map-marker-alt me-1"></i> Abrir en Google Maps
            </a>
          </div>
        </div>
      `
      : `
        <div class="alert alert-warning mb-0">
          <i class="fas fa-exclamation-triangle me-2"></i>No se capturó ubicación GPS
        </div>
      `;

    // Audio
    const audioSrc = normalizeAudioSrc(cert);
    const audioHtml = audioSrc
      ? `
        <div class="kpi">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
              <div class="label">Audio</div>
              <div class="value">Duración: ${escapeHtml(cert.audio_duracion_segundos || "0")}s</div>
              <div class="small text-muted">Formato: ${escapeHtml(cert.audio_formato || "audio/webm")}</div>
            </div>
            <span class="badge bg-success fs-6">
              <i class="fas fa-check-circle me-1"></i> Disponible
            </span>
          </div>

          <audio controls preload="metadata" class="w-100 mt-3" style="height:54px;">
            <source src="${audioSrc}" type="${escapeHtml(cert.audio_formato || "audio/webm")}">
            Tu navegador no soporta el audio.
          </audio>
        </div>
      `
      : `
        <div class="alert alert-warning mb-0">
          <i class="fas fa-exclamation-triangle me-2"></i>No se capturó audio
        </div>
      `;

    // Dispositivo
    let dispositivoHtml = "";
    if (cert.dispositivo_info) {
      try {
        const dispositivo = JSON.parse(cert.dispositivo_info);
        dispositivoHtml = `
          <div class="kpi">
            <div class="label">Dispositivo</div>
            <div class="value">${escapeHtml(dispositivo.plataforma || "N/A")}</div>
            <div class="small text-muted mt-1">
              Idioma: ${escapeHtml(dispositivo.idioma || "N/A")} •
              Resolución: ${dispositivo.pantalla ? escapeHtml(dispositivo.pantalla.ancho + "x" + dispositivo.pantalla.alto) : "N/A"}
            </div>
            <div class="small text-muted mt-2"><strong>IP:</strong> ${escapeHtml(cert.ip_address || "N/A")}</div>
            <div class="small text-muted"><strong>User-Agent:</strong> ${escapeHtml(cert.user_agent || "N/A")}</div>
          </div>
        `;
      } catch (e) {
        // si falla, lo ignoramos
      }
    }

    // ========================================
    // RESPUESTAS DEL SONDEO
    // ========================================
    let respuestasSondeoHtml = "";
    if (cert.origen_tipo === "sondeo" && cert.respuestas_sondeo && cert.respuestas_sondeo.length > 0) {
      const respSondeo = cert.respuestas_sondeo[0];

      let contenidoRespuesta = "";
      if (respSondeo.candidato_nombre) {
        const fotoUrl = respSondeo.candidato_foto
          ? `assets/img/admin/${escapeHtml(respSondeo.candidato_foto)}`
          : 'assets/img/team/avatar.png';
        contenidoRespuesta = `
          <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
            <img src="${fotoUrl}" alt="${escapeHtml(respSondeo.candidato_nombre)}"
              class="rounded-circle" style="width:60px;height:60px;object-fit:cover;border:3px solid #20427F;">
            <div>
              <div style="font-weight:900;font-size:1.1rem;color:#0f172a;">
                ${escapeHtml(respSondeo.candidato_nombre)}
              </div>
              ${respSondeo.candidato_cargo ? `<div class="text-muted small">${escapeHtml(respSondeo.candidato_cargo)}</div>` : ""}
            </div>
            <span class="badge bg-success ms-auto" style="font-size:.9rem;">
              <i class="fas fa-check-circle me-1"></i>Seleccionado
            </span>
          </div>
        `;
      } else if (respSondeo.opcion_texto) {
        contenidoRespuesta = `
          <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
            <div class="d-flex align-items-center justify-content-center"
              style="width:50px;height:50px;border-radius:12px;background:#20427F;">
              <i class="fas fa-check text-white"></i>
            </div>
            <div style="font-weight:800;font-size:1rem;color:#0f172a;">
              ${escapeHtml(respSondeo.opcion_texto)}
            </div>
            <span class="badge bg-success ms-auto" style="font-size:.9rem;">
              <i class="fas fa-check-circle me-1"></i>Seleccionado
            </span>
          </div>
        `;
      }

      respuestasSondeoHtml = `
        <div class="kpi" style="border:2px solid #20427F;background:rgba(32,66,127,.03);">
          <div class="label mb-2" style="color:#20427F;font-weight:900;">
            <i class="fas fa-clipboard-check me-1"></i> LO QUE CONTESTÓ EL ENCUESTADO
          </div>
          ${contenidoRespuesta}
          <div class="small text-muted mt-2">
            <i class="fas fa-clock me-1"></i>
            Registrado: ${respSondeo.fecha_respuesta ? new Date(respSondeo.fecha_respuesta).toLocaleString('es-CO') : 'N/A'}
          </div>
        </div>
      `;
    } else if (cert.origen_tipo === "sondeo") {
      respuestasSondeoHtml = `
        <div class="alert alert-warning mb-0">
          <i class="fas fa-exclamation-triangle me-2"></i>
          No se encontró respuesta registrada para este sondeo
        </div>
      `;
    }

    // ========================================
    // RESPUESTAS DEL CUESTIONARIO
    // ========================================
    let respuestasCuestionarioHtml = "";
    if (cert.origen_tipo === "cuestionario" && cert.respuestas_cuestionario && cert.respuestas_cuestionario.length > 0) {
      let preguntasHtml = "";

      cert.respuestas_cuestionario.forEach((pregunta, index) => {
        const respuestasTexto = pregunta.respuestas && pregunta.respuestas.length > 0
          ? pregunta.respuestas.map(r => `<span class="badge bg-primary me-1 mb-1">${escapeHtml(r)}</span>`).join("")
          : '<span class="text-muted">Sin respuesta</span>';

        preguntasHtml += `
          <div class="mb-3 p-3 bg-light rounded-3">
            <div class="d-flex align-items-start gap-2 mb-2">
              <span class="badge bg-secondary" style="min-width:28px;">${index + 1}</span>
              <div style="font-weight:700;color:#0f172a;">${escapeHtml(pregunta.texto_pregunta)}</div>
            </div>
            <div class="ms-4">
              <div class="small text-muted mb-1">Respuesta:</div>
              <div>${respuestasTexto}</div>
            </div>
          </div>
        `;
      });

      respuestasCuestionarioHtml = `
        <div class="kpi" style="border:2px solid #20427F;background:rgba(32,66,127,.03);max-height:400px;overflow-y:auto;">
          <div class="label mb-3" style="color:#20427F;font-weight:900;">
            <i class="fas fa-clipboard-check me-1"></i> LO QUE CONTESTÓ EL ENCUESTADO
            <span class="badge bg-primary ms-2">${cert.respuestas_cuestionario.length} preguntas</span>
          </div>
          ${preguntasHtml}
        </div>
      `;
    } else if (cert.origen_tipo === "cuestionario") {
      respuestasCuestionarioHtml = `
        <div class="alert alert-warning mb-0">
          <i class="fas fa-exclamation-triangle me-2"></i>
          No se encontraron respuestas registradas para este cuestionario
        </div>
      `;
    }

    const html = `
      <div class="row g-3">

        <div class="col-12">
          <div class="kpi">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
              <div>
                <div class="label">Certificación</div>
                <div class="value">#${escapeHtml(cert.id || "")}</div>
                <div class="small text-muted mt-1">${escapeHtml(fechaFormato)}</div>
              </div>
              <div class="text-end">
                <span class="badge bg-primary-subtle text-primary fw-bold">
                  <i class="fas fa-user-tie me-1"></i>
                  ${escapeHtml((cert.encuestador_nombre || "") + " " + (cert.encuestador_apellido || ""))}
                </span>
                <div class="small text-muted mt-2">
                  ${escapeHtml(cert.encuestador_email || "N/A")}
                </div>
              </div>
            </div>

            <hr class="my-3">

            <div class="row g-3">
              <div class="col-md-6">
                <div class="label">Votante</div>
                <div class="value">${escapeHtml(cert.votante_nombre || "N/A")}</div>
                <div class="small text-muted mt-1">${escapeHtml(cert.votante_email || "N/A")}</div>
              </div>
              <div class="col-md-6">
                <div class="label">Perfil</div>
                <div class="value">${escapeHtml(cert.votante_genero || "N/A")}</div>
                <div class="small text-muted mt-1">Rango edad: ${escapeHtml(cert.votante_rango_edad || "N/A")}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <h6 class="mb-2" style="font-weight:900;color:#0f172a">
            <i class="fas fa-tag me-2"></i>Origen
          </h6>
          ${origenHtml}
        </div>

        ${cert.origen_tipo === "sondeo" ? `
        <div class="col-12">
          <h6 class="mb-2" style="font-weight:900;color:#0f172a">
            <i class="fas fa-clipboard-check me-2" style="color:#20427F"></i>Respuesta del Sondeo
          </h6>
          ${respuestasSondeoHtml}
        </div>
        ` : ""}

        ${cert.origen_tipo === "cuestionario" ? `
        <div class="col-12">
          <h6 class="mb-2" style="font-weight:900;color:#0f172a">
            <i class="fas fa-clipboard-check me-2" style="color:#20427F"></i>Respuestas del Cuestionario
          </h6>
          ${respuestasCuestionarioHtml}
        </div>
        ` : ""}

        <div class="col-12 col-lg-6">
          <h6 class="mb-2" style="font-weight:900;color:#0f172a">
            <i class="fas fa-map-marker-alt me-2"></i>Ubicación GPS
          </h6>
          ${gpsKpi}

          ${hasGps ? `
            <div class="map-box mt-3">
              <div id="mapCanvas"></div>
            </div>
            <div class="small text-muted mt-2">
              Si el mapa no carga, revisa restricciones de la API KEY (dominio/referrer).
            </div>
          ` : ""}
        </div>

        <div class="col-12 col-lg-6">
          <h6 class="mb-2" style="font-weight:900;color:#0f172a">
            <i class="fas fa-microphone me-2"></i>Audio
          </h6>
          ${audioHtml}

          ${dispositivoHtml ? `
            <h6 class="mb-2 mt-3" style="font-weight:900;color:#0f172a">
              <i class="fas fa-mobile-alt me-2"></i>Dispositivo
            </h6>
            ${dispositivoHtml}
          ` : ""}
        </div>

      </div>
    `;

    $("#modalDetalleCertificacionBody").html(html);

    // ✅ Guardar cert para pintar mapa cuando el modal esté visible
    LAST_CERT_FOR_MAP = cert;
  },

  renderMap: function (cert) {
    const hasGps = cert.latitud && cert.longitud;
    if (!hasGps) return;

    // Si aún no cargó Google Maps
    if (!MAPS_READY || typeof google === "undefined" || !google.maps) {
      // reintento suave
      setTimeout(() => CERTIFICACIONES.renderMap(cert), 400);
      return;
    }

    const mapDiv = document.getElementById("mapCanvas");
    if (!mapDiv) return;

    const lat = parseFloat(cert.latitud);
    const lng = parseFloat(cert.longitud);
    const center = { lat, lng };

    // Crear mapa una sola vez por apertura
    MAP_INSTANCE = new google.maps.Map(mapDiv, {
      center,
      zoom: 15,
      mapTypeControl: true,
      streetViewControl: false,
      fullscreenControl: true,
    });

    MAP_MARKER = new google.maps.Marker({
      position: center,
      map: MAP_INSTANCE,
      title: (cert.votante_nombre || "Ubicación").toString(),
    });
  },
};
