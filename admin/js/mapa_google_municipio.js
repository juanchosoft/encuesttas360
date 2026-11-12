(function (window, $) {
  "use strict";

  var COLOR_SIN_DATO = "#C9CED6";
  var estado = {
    mapa: null,
    ventana: null,
    marcadores: [],
    opciones: [],
    ocultas: {},
    ultimaPeticion: null,
    secuencia: 0,
    cargandoApi: null
  };

  function esc(t) {
    return String(t == null ? "" : t).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }

  function cargarApi() {
    if (window.google && window.google.maps) return $.Deferred().resolve().promise();
    if (estado.cargandoApi) return estado.cargandoApi;
    var d = $.Deferred();
    var clave = window.GOOGLE_MAPS_API_KEY || "";
    if (!clave) {
      d.reject("No hay una llave de Google Maps configurada.");
      return d.promise();
    }
    window.__s360MapsListo = function () { d.resolve(); };
    var s = document.createElement("script");
    s.src = "https://maps.googleapis.com/maps/api/js?key=" + encodeURIComponent(clave) + "&callback=__s360MapsListo";
    s.async = true;
    s.onerror = function () { estado.cargandoApi = null; d.reject("No se pudo cargar Google Maps."); };
    document.head.appendChild(s);
    estado.cargandoApi = d.promise();
    return estado.cargandoApi;
  }

  function limpiarMarcadores() {
    estado.marcadores.forEach(function (m) { m.setMap(null); });
    estado.marcadores = [];
  }

  function aviso(html, tipo) {
    var el = $("#mgmAviso");
    if (!html) { el.addClass("d-none").empty(); return; }
    el.removeClass("d-none alert-info alert-warning alert-danger").addClass("alert-" + (tipo || "info")).html(html);
  }

  function textoOpcion(id) {
    for (var i = 0; i < estado.opciones.length; i++) {
      if (estado.opciones[i].id === id) return estado.opciones[i];
    }
    return null;
  }

  function visible(punto) {
    return punto.opciones.some(function (id) { return !estado.ocultas[id]; });
  }

  function contenidoInfo(p) {
    var lineas = p.opciones.map(function (id) {
      var o = textoOpcion(id);
      var color = o ? o.color : COLOR_SIN_DATO;
      return '<div class="mgm-info-op"><span class="mgm-dot" style="background:' + esc(color) + '"></span>' + esc(o ? o.texto : "Respuesta") + "</div>";
    }).join("");
    var meta = [];
    if (p.fecha) meta.push(esc(p.fecha));
    if (p.zona) meta.push("Zona: " + esc(p.zona));
    if (p.precision != null) meta.push("Precisión ±" + esc(p.precision) + " m");
    return '<div class="mgm-info">' + lineas + (meta.length ? '<div class="mgm-info-meta">' + meta.join(" · ") + "</div>" : "") + "</div>";
  }

  function pintarLeyenda() {
    var cont = $("#mgmLeyenda");
    if (!estado.opciones.length) { cont.empty(); return; }
    var total = estado.opciones.reduce(function (a, o) { return a + o.total; }, 0);
    var html = estado.opciones.map(function (o) {
      var pct = total > 0 ? Math.round((o.total / total) * 1000) / 10 : 0;
      var off = estado.ocultas[o.id] ? " mgm-off" : "";
      return '<button type="button" class="mgm-leg' + off + '" data-opcion="' + o.id + '" title="Mostrar u ocultar esta respuesta">'
        + '<span class="mgm-dot" style="background:' + esc(o.color) + '"></span>'
        + '<span class="mgm-leg-t">' + esc(o.texto) + "</span>"
        + '<span class="mgm-leg-n">' + o.total + " · " + String(pct).replace(".", ",") + "%</span></button>";
    }).join("");
    cont.html(html);
  }

  function aplicarVisibilidad() {
    estado.marcadores.forEach(function (m) { m.setVisible(visible(m.__punto)); });
  }

  function ajustarVista(res, bounds, hayPuntos) {
    if (hayPuntos) {
      estado.mapa.fitBounds(bounds, 40);
      google.maps.event.addListenerOnce(estado.mapa, "idle", function () {
        if (estado.mapa.getZoom() > 17) estado.mapa.setZoom(17);
      });
      return;
    }
    var nombre = res.municipio_nombre || "";
    if (nombre && google.maps.Geocoder) {
      new google.maps.Geocoder().geocode(
        { address: nombre + ", Colombia", componentRestrictions: { country: "CO" } },
        function (r, status) {
          if (status === "OK" && r && r[0] && r[0].geometry && r[0].geometry.viewport) {
            estado.mapa.fitBounds(r[0].geometry.viewport);
          } else {
            estado.mapa.setCenter({ lat: 4.57, lng: -74.3 });
            estado.mapa.setZoom(6);
          }
        }
      );
    } else {
      estado.mapa.setCenter({ lat: 4.57, lng: -74.3 });
      estado.mapa.setZoom(6);
    }
  }

  function dibujar(res) {
    estado.opciones = res.opciones || [];
    estado.ocultas = {};
    pintarLeyenda();
    if (typeof API.alColores === "function") {
      var mapaColores = {};
      estado.opciones.forEach(function (o) { mapaColores[o.id] = o.color; });
      API.alColores(mapaColores);
    }

    var el = document.getElementById("mgmCanvas");
    if (!el) return;
    if (!estado.mapa) {
      estado.mapa = new google.maps.Map(el, {
        center: { lat: 4.57, lng: -74.3 },
        zoom: 6,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true
      });
      estado.ventana = new google.maps.InfoWindow();
    }
    google.maps.event.trigger(estado.mapa, "resize");
    limpiarMarcadores();

    var bounds = new google.maps.LatLngBounds();
    (res.puntos || []).forEach(function (p) {
      var primera = textoOpcion(p.opciones[0]);
      var color = primera ? primera.color : COLOR_SIN_DATO;
      var pos = { lat: p.lat, lng: p.lng };
      var m = new google.maps.Marker({
        position: pos,
        map: estado.mapa,
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          scale: 8,
          fillColor: color,
          fillOpacity: 0.92,
          strokeColor: "#ffffff",
          strokeWeight: 2
        }
      });
      m.__punto = p;
      m.addListener("click", function () {
        estado.ventana.setContent(contenidoInfo(p));
        estado.ventana.open(estado.mapa, m);
      });
      estado.marcadores.push(m);
      bounds.extend(pos);
    });

    var n = estado.marcadores.length;
    var partes = [];
    partes.push("<strong>" + (res.total_encuestas || 0) + "</strong> encuestas en " + esc(res.municipio_nombre || "el municipio"));
    partes.push("<strong>" + n + "</strong> con ubicación");
    if (res.sin_ubicacion > 0) partes.push("<strong>" + res.sin_ubicacion + "</strong> sin ubicación (no aparecen como punto)");
    aviso(partes.join(" · "), res.sin_ubicacion > 0 && n === 0 ? "warning" : "info");
    ajustarVista(res, bounds, n > 0);
  }

  function pedir(peticion) {
    var secuencia = ++estado.secuencia;
    estado.ultimaPeticion = peticion;
    $("#mgmCargando").removeClass("d-none");
    aviso("");
    var datos = $.extend({}, window.DASH_FILTROS || {}, {
      ficha_tecnica_id: peticion.fichaId,
      pregunta_id: peticion.preguntaId,
      municipio_click: peticion.municipio
    });
    var url = window.MAPA_PUNTOS_URL || "admin/ajax/mapa_puntos_municipio.php";
    $.when(cargarApi(), $.ajax({ url: url, type: "POST", dataType: "json", data: datos, cache: false }))
      .done(function (_api, ajax) {
        if (secuencia !== estado.secuencia) return;
        var res = ajax[0];
        $("#mgmCargando").addClass("d-none");
        if (!res || !res.success) {
          limpiarMarcadores();
          estado.opciones = [];
          pintarLeyenda();
          aviso(esc((res && res.message) || "No se pudo cargar el mapa del municipio."), "warning");
          return;
        }
        dibujar(res);
      })
      .fail(function (a, b) {
        if (secuencia !== estado.secuencia) return;
        $("#mgmCargando").addClass("d-none");
        aviso(esc(typeof a === "string" ? a : "No se pudo cargar el mapa del municipio."), "danger");
      });
  }

  var API = {
    alColores: null,
    disponible: function () {
      return !!(window.GOOGLE_MAPS_API_KEY && $("#mgmWrap").length);
    },
    mostrar: function (peticion) {
      $("#mgmWrap").removeClass("d-none");
      pedir(peticion);
    },
    recargar: function (preguntaId, fichaId) {
      if (!estado.ultimaPeticion) return;
      var p = $.extend({}, estado.ultimaPeticion);
      if (preguntaId) p.preguntaId = preguntaId;
      if (fichaId) p.fichaId = fichaId;
      pedir(p);
    },
    ocultar: function () {
      estado.secuencia++;
      estado.ultimaPeticion = null;
      $("#mgmWrap").addClass("d-none");
      limpiarMarcadores();
    }
  };

  $(document).on("click", "#mgmLeyenda .mgm-leg", function () {
    var id = parseInt($(this).data("opcion"), 10);
    estado.ocultas[id] = !estado.ocultas[id];
    $(this).toggleClass("mgm-off", !!estado.ocultas[id]);
    aplicarVisibilidad();
  });

  window.MapaGoogleMunicipio = API;
})(window, jQuery);
