window.CertDash = (function () {
  let dt = null;
  let map = null;
  let markers = [];
  let infoWindow = null;
  let chartEstado = null;
  let chartOrigen = null;
  let chartEnc = null;
  let currentPoints = window.CERT_DASH_POINTS || [];
  let mapsReady = !!(window.__GMAPS_READY || (typeof google !== "undefined" && google.maps));

  const ESTADO_LABEL = {
    pendiente: "Pendiente",
    bien: "Bien",
    mal: "Mal",
    en_revision: "En revisión",
    anulada: "Anulada"
  };
  const ESTADO_BADGE = {
    pendiente: "secondary",
    bien: "success",
    mal: "danger",
    en_revision: "warning",
    anulada: "dark"
  };

  function esc(s) {
    return String(s ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function filtros() {
    const tipo = ($("#cd_tipo").val() || "").toString();
    const itemId = parseInt($("#cd_item").val() || "0", 10) || 0;
    const f = {
      estado_revision: ($("#cd_estado").val() || "").toString(),
      origen_tipo: "",
      tbl_sondeo_id: "",
      tbl_ficha_tecnica_encuesta_id: "",
      tbl_usuario_id: ($("#cd_encuestador").val() || "").toString(),
      revision_metodo: ($("#cd_metodo").val() || "").toString(),
      con_audio: ($("#cd_audio").val() || "").toString(),
      fecha_desde: ($("#cd_desde").val() || "").toString(),
      fecha_hasta: ($("#cd_hasta").val() || "").toString()
    };
    if (tipo === "sondeo") {
      f.origen_tipo = "sondeo";
      if (itemId > 0) f.tbl_sondeo_id = String(itemId);
    } else if (tipo === "cuestionario") {
      f.origen_tipo = "cuestionario";
      if (itemId > 0) f.tbl_ficha_tecnica_encuesta_id = String(itemId);
    }
    return f;
  }

  function fillItemSelector() {
    const tipo = ($("#cd_tipo").val() || "").toString();
    const $item = $("#cd_item");
    $item.empty();
    if (!tipo) {
      $item.prop("disabled", true);
      $item.append('<option value="">Selecciona un tipo primero…</option>');
      $("#cd_item_label").text("Sondeo / Encuesta");
      return;
    }
    $item.prop("disabled", false);
    $item.append('<option value="">Todos</option>');
    if (tipo === "sondeo") {
      $("#cd_item_label").text("Sondeo");
      (window.CERT_DASH_SONDEOS || []).forEach(function (s) {
        if (!s.id) return;
        $item.append('<option value="' + esc(s.id) + '">' + esc(s.label) + "</option>");
      });
    } else if (tipo === "cuestionario") {
      $("#cd_item_label").text("Encuesta");
      (window.CERT_DASH_FICHAS || []).forEach(function (f) {
        if (!f.id) return;
        $item.append('<option value="' + esc(f.id) + '">' + esc(f.label) + "</option>");
      });
    }
  }

  function pintarKpis(k) {
    if (!k) return;
    $('[data-k="total"]').text(k.total || 0);
    const pe = k.por_estado || {};
    $('[data-k="pendiente"]').text(pe.pendiente || 0);
    $('[data-k="bien"]').text(pe.bien || 0);
    $('[data-k="mal"]').text(pe.mal || 0);
    $('[data-k="en_revision"]').text(pe.en_revision || 0);
    $('[data-k="anulada"]').text(pe.anulada || 0);
    $('[data-k="pct"]').text((k.pct_bien_sobre_revisadas || 0) + "%");
    $('[data-k="con_audio"]').text(k.con_audio || 0);
    $('[data-k="con_gps"]').text(k.con_gps || 0);
    $('[data-k="ia"]').text(k.metodo_ia || 0);
  }

  function chartDefaults() {
    if (typeof Chart === "undefined") return;
    // Chart.js v2 (plugins/chart.js) vs v3+
    if (Chart.defaults && Chart.defaults.global) {
      Chart.defaults.global.defaultFontFamily = "Inter, system-ui, sans-serif";
      Chart.defaults.global.defaultFontSize = 11;
      Chart.defaults.global.defaultFontColor = "#667085";
    } else if (Chart.defaults && Chart.defaults.font) {
      Chart.defaults.font.family = "Inter, system-ui, sans-serif";
      Chart.defaults.font.size = 11;
      Chart.defaults.color = "#667085";
    }
  }

  function isChartV2() {
    return !!(typeof Chart !== "undefined" && Chart.defaults && Chart.defaults.global);
  }

  function pintarGraficas(k) {
    if (typeof Chart === "undefined" || !k) return;
    chartDefaults();
    const pe = k.por_estado || {};
    const labelsEstado = ["Pendiente", "Bien", "Mal", "En revisión", "Anulada"];
    const dataEstado = [
      pe.pendiente || 0,
      pe.bien || 0,
      pe.mal || 0,
      pe.en_revision || 0,
      pe.anulada || 0
    ];
    const colorsEstado = ["#98a2b3", "#12b981", "#e5484d", "#f5a524", "#344054"];

    const elEstado = document.getElementById("chartEstado");
    const elOrigen = document.getElementById("chartOrigen");
    const elEnc = document.getElementById("chartEncuestadores");
    if (!elEstado || !elOrigen || !elEnc) return;

    if (chartEstado) chartEstado.destroy();
    chartEstado = new Chart(elEstado.getContext("2d"), {
      type: "doughnut",
      data: {
        labels: labelsEstado,
        datasets: [{ data: dataEstado, backgroundColor: colorsEstado, borderWidth: 0 }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { position: "bottom" },
        plugins: { legend: { position: "bottom" } }
      }
    });

    const po = k.por_origen || {};
    const labelsOrigen = ["Encuesta", "Sondeo"];
    const dataOrigen = [po.cuestionario || 0, po.sondeo || 0];
    if (chartOrigen) chartOrigen.destroy();
    chartOrigen = new Chart(elOrigen.getContext("2d"), {
      type: "bar",
      data: {
        labels: labelsOrigen,
        datasets: [{
          label: "Evidencias",
          data: dataOrigen,
          backgroundColor: ["#4b8cf7", "#20427f"],
          borderWidth: 0
        }]
      },
      options: isChartV2()
        ? {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
              yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
              xAxes: [{ maxBarThickness: 42 }]
            }
          }
        : {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
          }
    });

    const top = k.top_encuestadores || [];
    if (chartEnc) chartEnc.destroy();
    const encOpts = isChartV2()
      ? {
          type: "horizontalBar",
          options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
              xAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
            }
          }
        }
      : {
          type: "bar",
          options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
          }
        };
    chartEnc = new Chart(elEnc.getContext("2d"), {
      type: encOpts.type,
      data: {
        labels: top.map(function (t) { return t.nombre || ("#" + t.uid); }),
        datasets: [{
          label: "Evidencias",
          data: top.map(function (t) { return t.total || 0; }),
          backgroundColor: "#1db6db",
          borderWidth: 0
        }]
      },
      options: encOpts.options
    });
  }

  function badgeEstado(est) {
    const e = est || "pendiente";
    return '<span class="cd-badge badge bg-' + esc(ESTADO_BADGE[e] || "secondary") + '">' +
      esc(ESTADO_LABEL[e] || e) + "</span>";
  }

  function pointsFromRows(rows) {
    const pts = [];
    (rows || []).forEach(function (r) {
      const lat = r.latitud;
      const lng = r.longitud;
      if (lat === null || lng === null || lat === "" || lng === "") return;
      if (isNaN(parseFloat(lat)) || isNaN(parseFloat(lng))) return;
      pts.push({
        id: parseInt(r.id, 10) || 0,
        lat: parseFloat(lat),
        lng: parseFloat(lng),
        estado: r.estado_revision || "pendiente",
        origen: r.origen_tipo || "",
        encuestador: ((r.encuestador_nombre || "") + " " + (r.encuestador_apellido || "")).trim(),
        votante: r.votante_nombre || "",
        fecha: r.fecha_certificacion || "",
        audio: parseInt(r.audio_duracion_segundos, 10) || 0,
        metodo: r.revision_metodo || "ninguno",
        nombre_origen: r.origen_tipo === "sondeo"
          ? (r.sondeo_nombre || "Sondeo")
          : (r.origen_tipo === "cuestionario" ? (r.cuestionario_nombre || "Encuesta") : "Sin tipo")
      });
    });
    return pts;
  }

  function pintarTabla(rows) {
    const data = (rows || []).map(function (r) {
      const hasGps = r.latitud && r.longitud && !isNaN(parseFloat(r.latitud)) && !isNaN(parseFloat(r.longitud));
      return [
        r.id || "",
        r.fecha_certificacion || "",
        badgeEstado(r.estado_revision),
        r.revision_metodo || "ninguno",
        ((r.encuestador_nombre || "") + " " + (r.encuestador_apellido || "")).trim(),
        r.votante_nombre || "",
        r.origen_tipo || "",
        (r.audio_duracion_segundos || 0) + "s",
        hasGps ? "Sí" : "No",
        r.revision_fecha || "",
        '<button type="button" class="btn btn-sm btn-primary cd-btn-detalle" data-id="' +
          esc(r.id || "") + '"><i class="fas fa-eye me-1"></i>Detalle</button>'
      ];
    });

    if (dt) {
      dt.clear();
      dt.rows.add(data);
      dt.draw();
      return;
    }

    if ($.fn.DataTable && $.fn.DataTable.isDataTable("#cdTabla")) {
      dt = $("#cdTabla").DataTable();
      dt.clear();
      dt.rows.add(data);
      dt.draw();
      return;
    }

    const $tb = $("#cdTablaBody").empty();
    data.forEach(function (cols) {
      $tb.append("<tr>" + cols.map(function (c) { return "<td>" + c + "</td>"; }).join("") + "</tr>");
    });
    if ($.fn.DataTable) {
      dt = $("#cdTabla").DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
        order: [[0, "desc"]],
        pageLength: 25,
        responsive: false,
        autoWidth: false,
        columnDefs: [
          { targets: [0, 1, 8, 10], className: "text-nowrap" },
          { targets: 10, orderable: false, searchable: false }
        ]
      });
    }
  }

  function markerColor(estado) {
    const mapColors = {
      pendiente: "#98a2b3",
      bien: "#12b981",
      mal: "#e5484d",
      en_revision: "#f5a524",
      anulada: "#344054"
    };
    return mapColors[estado] || "#4b8cf7";
  }

  function clearMarkers() {
    markers.forEach(function (m) { m.setMap(null); });
    markers = [];
    if (infoWindow) infoWindow.close();
  }

  function infoHtml(p) {
    return '<div class="cd-infowin">' +
      "<strong>#" + esc(p.id) + " · " + esc(p.votante || "Encuestado") + "</strong>" +
      '<div class="meta">' +
      "<b>Estado:</b> " + esc(ESTADO_LABEL[p.estado] || p.estado) + "<br>" +
      "<b>Encuestador:</b> " + esc(p.encuestador || "—") + "<br>" +
      "<b>Origen:</b> " + esc(p.nombre_origen || p.origen || "—") + "<br>" +
      "<b>Fecha:</b> " + esc(p.fecha || "—") + "<br>" +
      "<b>Audio:</b> " + esc(String(p.audio || 0)) + "s · <b>Método:</b> " + esc(p.metodo || "ninguno") +
      "</div>" +
      '<button type="button" class="btn btn-sm btn-primary" onclick="window.CertDash.openDetalle(' +
      parseInt(p.id, 10) + ')">Ver detalle</button>' +
      "</div>";
  }

  function renderMap(points) {
    currentPoints = points || [];
    $("#cdMapCount").text(currentPoints.length + " puntos");
    if (!currentPoints.length) {
      $("#cdMapEmpty").removeClass("d-none");
      clearMarkers();
      return;
    }
    $("#cdMapEmpty").addClass("d-none");

    if (!mapsReady || typeof google === "undefined" || !google.maps) {
      return;
    }

    const el = document.getElementById("cdMapCanvas");
    if (!el) return;

    if (!map) {
      map = new google.maps.Map(el, {
        center: { lat: currentPoints[0].lat, lng: currentPoints[0].lng },
        zoom: 6,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true
      });
      infoWindow = new google.maps.InfoWindow();
    }

    clearMarkers();
    const bounds = new google.maps.LatLngBounds();
    currentPoints.forEach(function (p, idx) {
      const pos = { lat: p.lat, lng: p.lng };
      const marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: "#" + p.id + " " + (p.votante || ""),
        label: {
          text: String(idx + 1),
          color: "#fff",
          fontWeight: "800",
          fontSize: "11px"
        },
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          scale: 12,
          fillColor: markerColor(p.estado),
          fillOpacity: 1,
          strokeColor: "#fff",
          strokeWeight: 2
        }
      });
      marker.addListener("click", function () {
        infoWindow.setContent(infoHtml(p));
        infoWindow.open(map, marker);
      });
      markers.push(marker);
      bounds.extend(pos);
    });

    if (currentPoints.length === 1) {
      map.setCenter(bounds.getCenter());
      map.setZoom(15);
    } else {
      map.fitBounds(bounds, 48);
    }
  }

  function openDetalle(id) {
    if (typeof CERTIFICACIONES !== "undefined" && typeof CERTIFICACIONES.verDetalle === "function") {
      CERTIFICACIONES.verDetalle(id);
      return;
    }
    alert("No se pudo abrir el detalle (#" + id + ")");
  }

  function refrescar() {
    const f = filtros();
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: Object.assign({ op: "certificaciondashboardkpis" }, f),
      success: function (resp) {
        if (resp && resp.output && resp.output.valid) {
          pintarKpis(resp.output.response);
          pintarGraficas(resp.output.response);
        }
      }
    });
    $.ajax({
      url: "admin/ajax/rqst.php",
      type: "POST",
      dataType: "json",
      data: Object.assign({ op: "certificaciondashboardlist" }, f),
      success: function (resp) {
        if (!resp || !resp.output || !resp.output.valid) return;
        const rows = resp.output.response || [];
        pintarTabla(rows);
        const pts = pointsFromRows(rows);
        renderMap(pts);
        $("#cdCountHint").text(rows.length + " registros en tabla · " + pts.length + " con GPS");
      }
    });
  }

  function onMapsReady() {
    mapsReady = true;
    renderMap(currentPoints);
  }

  function init() {
    pintarKpis(window.CERT_DASH_KPIS || {});
    pintarGraficas(window.CERT_DASH_KPIS || {});

    if ($.fn.DataTable) {
      if ($.fn.DataTable.isDataTable("#cdTabla")) {
        dt = $("#cdTabla").DataTable();
      } else {
        dt = $("#cdTabla").DataTable({
          language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
          order: [[0, "desc"]],
          pageLength: 25,
          responsive: false,
          autoWidth: false,
          columnDefs: [
            { targets: [0, 1, 8, 10], className: "text-nowrap" },
            { targets: 10, orderable: false, searchable: false }
          ]
        });
      }
    }

    $(document).on("click", ".cd-btn-detalle", function () {
      const id = parseInt($(this).data("id"), 10);
      if (id > 0) openDetalle(id);
    });

    $("#cd_tipo").on("change", function () {
      fillItemSelector();
    });

    $("#cd_filtrar").on("click", refrescar);
    $("#cd_limpiar").on("click", function () {
      $("#cd_tipo,#cd_item,#cd_estado,#cd_encuestador,#cd_metodo,#cd_audio,#cd_desde,#cd_hasta").val("");
      fillItemSelector();
      refrescar();
    });

    fillItemSelector();

    $("#cd_export").on("click", function () {
      if (!window.CERT_DASH_EXPORT) return;
      const f = filtros();
      $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        dataType: "json",
        data: Object.assign({ op: "certificaciondashboardexport" }, f),
        success: function (resp) {
          if (!resp || !resp.output || !resp.output.valid) {
            alert("No se pudo exportar");
            return;
          }
          const r = resp.output.response;
          const bin = atob(r.content_base64 || "");
          const bytes = new Uint8Array(bin.length);
          for (let i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
          const blob = new Blob([bytes], { type: r.mime || "text/csv" });
          const a = document.createElement("a");
          a.href = URL.createObjectURL(blob);
          a.download = r.filename || "certificaciones.csv";
          document.body.appendChild(a);
          a.click();
          a.remove();
        },
        error: function () {
          alert("Error de red al exportar");
        }
      });
    });

    if (mapsReady) {
      renderMap(currentPoints);
    } else if (!window.GOOGLE_MAPS_API_KEY) {
      $("#cdMapEmpty").removeClass("d-none").html(
        '<i class="fas fa-key fa-2x"></i><div>Falta configurar la API key de Google Maps</div>'
      );
    }
  }

  return {
    init: init,
    onMapsReady: onMapsReady,
    openDetalle: openDetalle,
    refrescar: refrescar
  };
})();

$(function () {
  const prevInitMap = window.initMap;
  window.initMap = function () {
    if (typeof prevInitMap === "function") {
      try { prevInitMap(); } catch (e) { /* ignore */ }
    }
    window.__GMAPS_READY = true;
    if (window.CertDash && typeof window.CertDash.onMapsReady === "function") {
      window.CertDash.onMapsReady();
    }
  };
  if (window.__GMAPS_READY || (typeof google !== "undefined" && google.maps)) {
    window.CertDash.onMapsReady();
  }
  window.CertDash.init();
});
