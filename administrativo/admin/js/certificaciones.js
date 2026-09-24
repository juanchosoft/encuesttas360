$(init);

let MAPS_READY = false;
let LAST_CERT_FOR_MAP = null;
let MAP_INSTANCE = null;
let MAP_MARKER = null;
let DT_CERT = null;

function updateCertCountLabel(dt) {
  const n = dt ? dt.rows({ search: "applied" }).count() : 0;
  const label = n === 1 ? "registro" : "registros";
  $("#certCountLabel").html('<i class="fas fa-database"></i>' + n + " " + label);
}

function getCertFiltros() {
  return {
    estado: ($("#filtro_estado").val() || "").toString().trim(),
    tipo: ($("#filtro_tipo").val() || "").toString().trim(),
    item: ($("#filtro_item").val() || "").toString().trim(),
    encuestador: ($("#filtro_encuestador").val() || "").toString().trim(),
    desde: ($("#filtro_fecha_desde").val() || "").toString().trim(),
    hasta: ($("#filtro_fecha_hasta").val() || "").toString().trim(),
  };
}

function fillFiltroItem() {
  const tipo = ($("#filtro_tipo").val() || "").toString();
  const $item = $("#filtro_item");
  if (!$item.length) return;
  $item.empty();
  if (!tipo) {
    $item.prop("disabled", true);
    $item.append('<option value="">Selecciona un tipo primero…</option>');
    $("#filtro_item_label").text("Sondeo / Encuesta");
    return;
  }
  $item.prop("disabled", false);
  $item.append('<option value="">Todos</option>');
  if (tipo === "sondeo") {
    $("#filtro_item_label").text("Sondeo");
    (window.CERT_SONDEOS || []).forEach(function (s) {
      if (!s || !s.id) return;
      $item.append(
        $("<option></option>").val(String(s.id)).text(s.label || ("Sondeo #" + s.id))
      );
    });
  } else if (tipo === "cuestionario") {
    $("#filtro_item_label").text("Encuesta");
    (window.CERT_FICHAS || []).forEach(function (f) {
      if (!f || !f.id) return;
      $item.append(
        $("<option></option>").val(String(f.id)).text(f.label || ("Encuesta #" + f.id))
      );
    });
  }
}

function aplicarFiltrosCert() {
  if (!DT_CERT && $.fn.DataTable && $.fn.DataTable.isDataTable("#tblCertificaciones")) {
    DT_CERT = $("#tblCertificaciones").DataTable();
  }
  if (DT_CERT) {
    DT_CERT.draw();
  }
}

function init() {
  if ($("#tblCertificaciones").length > 0 && $.fn.DataTable) {
    // Registrar filtro una sola vez (soporta re-init)
    if (!window._certExtSearchOk) {
      $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const tableId = (settings.nTable && settings.nTable.id) || settings.sTableId || "";
        if (tableId !== "tblCertificaciones") {
          return true;
        }

        const row = settings.aoData[dataIndex] && settings.aoData[dataIndex].nTr;
        if (!row) {
          return true;
        }

        const f = getCertFiltros();
        const rowOrigen = (row.getAttribute("data-origen") || "").trim();
        const rowEnc = (row.getAttribute("data-encuestador") || "").trim();
        const rowFecha = (row.getAttribute("data-fecha") || "").trim();
        const rowEstado = (row.getAttribute("data-estado") || "").trim();
        const rowSondeo = (row.getAttribute("data-sondeo-id") || "").trim();
        const rowFicha = (row.getAttribute("data-ficha-id") || "").trim();

        if (f.estado && rowEstado !== f.estado) return false;
        if (f.tipo && rowOrigen !== f.tipo) return false;
        if (f.tipo === "sondeo" && f.item && rowSondeo !== f.item) return false;
        if (f.tipo === "cuestionario" && f.item && rowFicha !== f.item) return false;
        if (f.encuestador && rowEnc !== f.encuestador) return false;
        if (f.desde && (!rowFecha || rowFecha < f.desde)) return false;
        if (f.hasta && (!rowFecha || rowFecha > f.hasta)) return false;
        return true;
      });
      window._certExtSearchOk = true;
    }

    if ($.fn.DataTable.isDataTable("#tblCertificaciones")) {
      DT_CERT = $("#tblCertificaciones").DataTable();
    } else {
      DT_CERT = $("#tblCertificaciones").DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
        order: [[0, "desc"]],
        pageLength: 25,
        responsive: false,
        autoWidth: false,
        columnDefs: [
          { targets: [0, 1, 2, 6, 7, 8], className: "text-nowrap" },
        ],
      });
    }

    DT_CERT.on("draw", function () {
      updateCertCountLabel(DT_CERT);
    });
    updateCertCountLabel(DT_CERT);
  }

  $(document)
    .off("click.certFiltros", "#btn_aplicar_filtros_cert")
    .on("click.certFiltros", "#btn_aplicar_filtros_cert", function (e) {
      e.preventDefault();
      aplicarFiltrosCert();
    });

  $(document)
    .off("click.certFiltros", "#btn_limpiar_filtros_cert")
    .on("click.certFiltros", "#btn_limpiar_filtros_cert", function (e) {
      e.preventDefault();
      $("#filtro_estado").val("");
      $("#filtro_tipo").val("");
      $("#filtro_item").val("");
      fillFiltroItem();
      $("#filtro_encuestador").val("");
      $("#filtro_fecha_desde").val("");
      $("#filtro_fecha_hasta").val("");
      aplicarFiltrosCert();
    });

  $(document)
    .off("change.certFiltros", "#filtro_estado, #filtro_tipo, #filtro_item, #filtro_encuestador, #filtro_fecha_desde, #filtro_fecha_hasta")
    .on("change.certFiltros", "#filtro_estado, #filtro_tipo, #filtro_item, #filtro_encuestador, #filtro_fecha_desde, #filtro_fecha_hasta", function (e) {
      if (e && e.target && e.target.id === "filtro_tipo") {
        fillFiltroItem();
      }
      aplicarFiltrosCert();
    });

  fillFiltroItem();

  if ($("#tblEncuestasVinculadas").length > 0 && $.fn.DataTable && !$.fn.DataTable.isDataTable("#tblEncuestasVinculadas")) {
    $("#tblEncuestasVinculadas").DataTable({
      language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
      order: [[0, "desc"]],
      pageLength: 25,
      responsive: false,
      autoWidth: false,
    });
  }

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
      CERTIFICACIONES.limpiarBackdropModal();
    });
  }

  // Cierre explícito: evita fallos con instancias BS duplicadas / data-bs-dismiss vs data-dismiss
  $(document)
    .off("click.certModalClose", "#modalDetalleCertificacion [data-bs-dismiss='modal'], #modalDetalleCertificacion [data-dismiss='modal']")
    .on("click.certModalClose", "#modalDetalleCertificacion [data-bs-dismiss='modal'], #modalDetalleCertificacion [data-dismiss='modal']", function (e) {
      e.preventDefault();
      e.stopPropagation();
      CERTIFICACIONES.cerrarDetalle();
    });
}

/**
 * Google Maps callback (global). Si el stub de la página ya corrió, sincroniza el flag.
 */
window.initMap = function () {
  MAPS_READY = true;
  window.__GMAPS_READY = true;
};
if (window.__GMAPS_READY || (typeof google !== "undefined" && google.maps)) {
  MAPS_READY = true;
}

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
  getDetalleModalInstance: function () {
    const modalEl = document.getElementById("modalDetalleCertificacion");
    if (!modalEl) return null;
    if (window.bootstrap && bootstrap.Modal) {
      if (typeof bootstrap.Modal.getOrCreateInstance === "function") {
        return bootstrap.Modal.getOrCreateInstance(modalEl);
      }
      return bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    }
    return null;
  },

  limpiarBackdropModal: function () {
    const abiertos = document.querySelectorAll(".modal.show").length;
    if (abiertos > 0) return;
    document.querySelectorAll(".modal-backdrop").forEach(function (el) {
      el.remove();
    });
    document.body.classList.remove("modal-open");
    document.body.style.removeProperty("overflow");
    document.body.style.removeProperty("padding-right");
  },

  cerrarDetalle: function () {
    const modalEl = document.getElementById("modalDetalleCertificacion");
    if (!modalEl) return;
    const inst = this.getDetalleModalInstance();
    if (inst && typeof inst.hide === "function") {
      inst.hide();
    } else if (window.jQuery) {
      jQuery(modalEl).modal("hide");
    } else {
      modalEl.classList.remove("show");
      modalEl.style.display = "none";
      modalEl.setAttribute("aria-hidden", "true");
      this.limpiarBackdropModal();
    }
    // Por si quedan backdrops huérfanos tras instancias duplicadas
    setTimeout(function () {
      CERTIFICACIONES.limpiarBackdropModal();
    }, 200);
  },

  verDetalle: function (id) {
    // Abrir modal (una sola instancia)
    const modalEl = document.getElementById("modalDetalleCertificacion");
    if (!modalEl) return;
    const modal = this.getDetalleModalInstance();
    if (modal && typeof modal.show === "function") {
      modal.show();
    } else if (window.jQuery) {
      jQuery(modalEl).modal("show");
    } else {
      modalEl.classList.add("show");
      modalEl.style.display = "block";
      modalEl.removeAttribute("aria-hidden");
    }

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
          UTIL.mostrarMensajeError("Error al cargar la validación");
          $("#modalDetalleCertificacionBody").html(`
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-circle me-2"></i>
              Error al cargar la validación
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
              <div style="font-weight:900">Encuesta</div>
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
            <div><i class="fas fa-layer-group fa-2x"></i></div>
            <div>
              <div style="font-weight:900">Sin tipo</div>
              <div class="small">Evidencia sin sondeo ni encuesta asociados</div>
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
                <div class="label">Validación</div>
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

        <div class="col-12">
          ${CERTIFICACIONES.buildRevisionPanel(cert)}
        </div>

      </div>
    `;

    $("#modalDetalleCertificacionBody").html(html);
    CERTIFICACIONES.bindRevisionHandlers(cert);

    // El modal ya suele estar visible (shown disparó con el spinner); pintar mapa tras insertar #mapCanvas.
    LAST_CERT_FOR_MAP = cert;
    if (cert.latitud && cert.longitud) {
      setTimeout(function () {
        CERTIFICACIONES.renderMap(cert);
      }, 60);
    }
  },

  buildRevisionPanel: function (cert) {
    const perms = window.CERT_PERMS || {};
    const estado = (cert.estado_revision || "pendiente").toString();
    const tieneAudio = !!(cert.audio_base64 && String(cert.audio_base64).trim() !== "");
    const puedeRevisar = !!perms.revisar;
    const puedeIa = !!perms.validar_ia && tieneAudio;
    const puedeHistorial = !!perms.historial;

    if (!puedeRevisar && !puedeIa && !puedeHistorial) {
      return `<div class="alert alert-light border mb-0"><strong>Estado:</strong> ${escapeHtml(estado)}</div>`;
    }

    const opts = ["pendiente", "bien", "mal", "en_revision", "anulada"].map(function (e) {
      const labels = { pendiente: "Pendiente", bien: "Bien", mal: "Mal", en_revision: "En revisión", anulada: "Anulada" };
      return `<option value="${e}" ${estado === e ? "selected" : ""}>${labels[e]}</option>`;
    }).join("");

    return `
      <div class="kpi" style="border:2px solid #20427F;background:rgba(32,66,127,.03);">
        <div class="label mb-2" style="color:#20427F;font-weight:900;">
          <i class="fas fa-clipboard-check me-1"></i> Revisión de calidad
        </div>
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label small mb-1" for="rev_estado">Estado</label>
            <select id="rev_estado" class="form-select form-select-sm" ${puedeRevisar ? "" : "disabled"}>${opts}</select>
          </div>
          <div class="col-md-9">
            <label class="form-label small mb-1" for="rev_comentario">Comentario (obligatorio si Mal)</label>
            <textarea id="rev_comentario" class="form-control form-control-sm" rows="2" ${puedeRevisar ? "" : "disabled"}>${escapeHtml(cert.revision_comentario || "")}</textarea>
          </div>
        </div>
        <div class="mt-2" id="rev_ia_preview" style="display:none;"></div>
        <div class="mt-2" id="rev_transcripcion_box" style="display:none;">
          <label class="form-label small mb-1">Transcripción</label>
          <textarea id="rev_transcripcion" class="form-control form-control-sm" rows="3" readonly></textarea>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          ${puedeIa ? `<button type="button" class="btn btn-sm btn-outline-primary" id="btnValidarIa"><i class="fas fa-robot me-1"></i>Validar con IA</button>` : (!tieneAudio && perms.validar_ia ? `<button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Sin audio">Validar con IA</button>` : "")}
          ${puedeRevisar ? `<button type="button" class="btn btn-sm btn-primary" id="btnGuardarRevision"><i class="fas fa-save me-1"></i>Guardar</button>` : ""}
          ${puedeHistorial ? `<button type="button" class="btn btn-sm btn-outline-secondary" id="btnVerHistorial"><i class="fas fa-history me-1"></i>Historial</button>` : ""}
        </div>
        <div id="rev_historial" class="mt-3" style="display:none;"></div>
        <input type="hidden" id="rev_ia_json" value="">
        <input type="hidden" id="rev_metodo" value="${escapeHtml(cert.revision_metodo || "ninguno")}">
        <input type="hidden" id="rev_cert_id" value="${escapeHtml(cert.id || "")}">
      </div>
    `;
  },

  bindRevisionHandlers: function (cert) {
    const self = this;
    $("#btnValidarIa").off("click").on("click", function () {
      const $btn = $(this);
      $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-1"></i>Validando…');
      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        timeout: 180000,
        data: { op: "certificacionvalidaria", id: cert.id },
        success: function (resp) {
          if (!resp || !resp.output || !resp.output.valid) {
            const msg = (resp && resp.output && resp.output.response && resp.output.response.content)
              ? resp.output.response.content
              : "No se pudo validar con IA";
            UTIL.mostrarMensajeError(msg);
            return;
          }
          const r = resp.output.response;
          $("#rev_estado").val(r.veredicto === "mal" ? "mal" : "bien");
          $("#rev_comentario").val(r.comentario || "");
          $("#rev_transcripcion").val(r.transcripcion || "");
          $("#rev_transcripcion_box").show();
          $("#rev_ia_json").val(JSON.stringify(r.ia_json || r));
          $("#rev_metodo").val("ia");
          let coincHtml = "";
          (r.coincidencias || []).slice(0, 12).forEach(function (c) {
            coincHtml += `<li><strong>${escapeHtml(c.pregunta || "")}</strong>: ${escapeHtml(c.respuesta_registrada || "")}
              <span class="badge ${c.mencionado_en_audio ? "bg-success" : "bg-warning text-dark"}">${c.mencionado_en_audio ? "en audio" : "No escuchado"}</span>
              <span class="text-muted small">${escapeHtml(c.observacion || "")}</span></li>`;
          });
          $("#rev_ia_preview").html(
            `<div class="alert alert-info mb-0 small"><strong>Propuesta IA</strong> (confirma con Guardar). Confianza: ${escapeHtml(String(r.confianza ?? ""))}
             <ul class="mb-0 mt-2">${coincHtml || "<li>Sin coincidencias detalladas</li>"}</ul></div>`
          ).show();
        },
        error: function (xhr) {
          UTIL.mostrarMensajeError("Error de red al validar con IA (" + (xhr.status || "?") + ")");
        },
        complete: function () {
          $btn.prop("disabled", false).html('<i class="fas fa-robot me-1"></i>Validar con IA');
        }
      });
    });

    $("#btnGuardarRevision").off("click").on("click", function () {
      const estado = ($("#rev_estado").val() || "").toString();
      const comentario = ($("#rev_comentario").val() || "").toString().trim();
      if (estado === "mal" && !comentario) {
        UTIL.mostrarMensajeError("El comentario es obligatorio cuando el estado es Mal");
        return;
      }
      const $btn = $(this);
      $btn.prop("disabled", true);
      const payload = {
        op: "certificacionrevisarguardar",
        id: $("#rev_cert_id").val() || cert.id,
        estado_revision: estado,
        revision_comentario: comentario,
        revision_metodo: ($("#rev_metodo").val() === "ia") ? "ia" : "manual",
        revision_transcripcion: ($("#rev_transcripcion").val() || "").toString(),
        revision_ia_json: ($("#rev_ia_json").val() || "").toString()
      };
      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: payload,
        success: function (resp) {
          if (!resp || !resp.output || !resp.output.valid) {
            const msg = (resp && resp.output && resp.output.response && resp.output.response.content)
              ? resp.output.response.content
              : "No se pudo guardar la revisión";
            UTIL.mostrarMensajeError(msg);
            return;
          }
          UTIL.mostrarMensajeExitoso("Revisión guardada");
          setTimeout(function () { window.location.reload(); }, 700);
        },
        error: function () {
          UTIL.mostrarMensajeError("Error de red al guardar");
        },
        complete: function () {
          $btn.prop("disabled", false);
        }
      });
    });

    $("#btnVerHistorial").off("click").on("click", function () {
      const $box = $("#rev_historial");
      $box.show().html('<div class="text-muted small">Cargando historial…</div>');
      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: { op: "certificacionhistorial", id: cert.id },
        success: function (resp) {
          if (!resp || !resp.output || !resp.output.valid) {
            $box.html('<div class="alert alert-warning mb-0">No se pudo cargar el historial</div>');
            return;
          }
          const rows = resp.output.response || [];
          if (!rows.length) {
            $box.html('<div class="text-muted small">Sin historial aún.</div>');
            return;
          }
          let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Fecha</th><th>De</th><th>A</th><th>Método</th><th>Revisor</th><th>Comentario</th></tr></thead><tbody>';
          rows.forEach(function (h) {
            html += `<tr>
              <td class="text-nowrap">${escapeHtml(h.dtcreate || "")}</td>
              <td>${escapeHtml(h.estado_anterior || "")}</td>
              <td>${escapeHtml(h.estado_nuevo || "")}</td>
              <td>${escapeHtml(h.metodo || "")}</td>
              <td>${escapeHtml(((h.revisor_nombre || "") + " " + (h.revisor_apellido || "")).trim())}</td>
              <td>${escapeHtml(h.comentario || "")}</td>
            </tr>`;
          });
          html += "</tbody></table></div>";
          $box.html(html);
        },
        error: function () {
          $box.html('<div class="alert alert-danger mb-0">Error de red</div>');
        }
      });
    });

    if (cert.revision_transcripcion) {
      $("#rev_transcripcion").val(cert.revision_transcripcion);
      $("#rev_transcripcion_box").show();
    }
  },

  renderMap: function (cert, tries) {
    const hasGps = cert.latitud && cert.longitud;
    if (!hasGps) return;

    tries = typeof tries === "number" ? tries : 0;
    if (typeof google !== "undefined" && google.maps) {
      MAPS_READY = true;
    }

    if (!MAPS_READY || typeof google === "undefined" || !google.maps) {
      if (tries < 50) {
        setTimeout(function () {
          CERTIFICACIONES.renderMap(cert, tries + 1);
        }, 200);
      }
      return;
    }

    const mapDiv = document.getElementById("mapCanvas");
    if (!mapDiv) {
      if (tries < 50) {
        setTimeout(function () {
          CERTIFICACIONES.renderMap(cert, tries + 1);
        }, 100);
      }
      return;
    }

    const lat = parseFloat(cert.latitud);
    const lng = parseFloat(cert.longitud);
    if (!isFinite(lat) || !isFinite(lng)) return;
    const center = { lat, lng };

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
