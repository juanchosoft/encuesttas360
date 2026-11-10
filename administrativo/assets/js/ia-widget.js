(function () {
  "use strict";

  let conversacionActual = null;
  let enviando = false;
  let grabador = null;
  let fragmentosAudio = [];
  let grabando = false;

  function h(s) {
    return String(s == null ? "" : s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  function textoAHtml(texto) {
    return h(texto).replace(/\n/g, "<br>");
  }

  function formatoEnLinea(s) {
    s = s.replace(/`([^`\n]+)`/g, "<code>$1</code>");
    s = s.replace(/\*\*([^*\n]+)\*\*/g, "<strong>$1</strong>");
    s = s.replace(/(^|[^*])\*([^*\n]+)\*(?!\*)/g, "$1<em>$2</em>");
    return s;
  }

  function celdasDeFilaMarkdown(fila) {
    const partes = fila.split("|");
    if (partes.length && partes[0].trim() === "") partes.shift();
    if (partes.length && partes[partes.length - 1].trim() === "") partes.pop();
    return partes.map(function (c) { return c.trim(); });
  }

  function esFilaTabla(linea) {
    return /^\s*\|.*\|\s*$/.test(linea);
  }

  function esSeparadorTabla(linea) {
    return /^\s*\|?[\s:|-]+\|?\s*$/.test(linea) && linea.indexOf("-") !== -1;
  }

  // Convierte el texto de Yamil (ya escapado con h(), nunca HTML crudo del
  // modelo) a un subconjunto seguro de Markdown -> HTML, para que el chat no
  // muestre asteriscos/almohadillas literales. Las etiquetas insertadas acá
  // son siempre las nuestras, nunca las del texto original.
  function markdownSeguro(textoOriginal) {
    const lineas = h(textoOriginal).split("\n");
    const bloques = [];
    let i = 0;

    while (i < lineas.length) {
      const linea = lineas[i];

      if (/^\s*$/.test(linea)) { i++; continue; }

      const encabezado = linea.match(/^\s{0,3}(#{1,4})\s+(.*)$/);
      if (encabezado) {
        bloques.push("<h4>" + formatoEnLinea(encabezado[2]) + "</h4>");
        i++;
        continue;
      }

      if (esFilaTabla(linea) && lineas[i + 1] && esSeparadorTabla(lineas[i + 1])) {
        const encabezados = celdasDeFilaMarkdown(linea);
        let j = i + 2;
        const filas = [];
        while (j < lineas.length && esFilaTabla(lineas[j])) {
          filas.push(celdasDeFilaMarkdown(lineas[j]));
          j++;
        }
        let tabla = "<table class=\"yamil-md-table\"><thead><tr>";
        encabezados.forEach(function (c) { tabla += "<th>" + formatoEnLinea(c) + "</th>"; });
        tabla += "</tr></thead><tbody>";
        filas.forEach(function (fila) {
          tabla += "<tr>";
          fila.forEach(function (c) { tabla += "<td>" + formatoEnLinea(c) + "</td>"; });
          tabla += "</tr>";
        });
        tabla += "</tbody></table>";
        bloques.push(tabla);
        i = j;
        continue;
      }

      const esItemLista = /^\s*([-*]|\d+\.)\s+/.test(linea);
      if (esItemLista) {
        const ordenada = /^\s*\d+\.\s+/.test(linea);
        const items = [];
        let j = i;
        while (j < lineas.length && /^\s*([-*]|\d+\.)\s+/.test(lineas[j])) {
          items.push("<li>" + formatoEnLinea(lineas[j].replace(/^\s*([-*]|\d+\.)\s+/, "")) + "</li>");
          j++;
        }
        bloques.push((ordenada ? "<ol>" : "<ul>") + items.join("") + (ordenada ? "</ol>" : "</ul>"));
        i = j;
        continue;
      }

      const parrafo = [];
      let j = i;
      while (
        j < lineas.length &&
        !/^\s*$/.test(lineas[j]) &&
        !/^\s{0,3}#{1,4}\s+/.test(lineas[j]) &&
        !/^\s*([-*]|\d+\.)\s+/.test(lineas[j]) &&
        !esFilaTabla(lineas[j])
      ) {
        parrafo.push(lineas[j]);
        j++;
      }
      bloques.push("<p>" + formatoEnLinea(parrafo.join("<br>")) + "</p>");
      i = j;
    }

    return bloques.join("");
  }

  function agregarMensaje(rol, texto, mensajeId) {
    const lista = document.getElementById("yamilMensajes");
    if (!lista) return;

    const fila = document.createElement("div");
    fila.className = "yamil-msg yamil-msg-" + (rol === "user" ? "user" : "bot");
    fila.innerHTML = rol === "user" ? textoAHtml(texto) : markdownSeguro(texto);

    if (rol !== "user" && mensajeId && window.YAMIL_CFG && window.YAMIL_CFG.voz) {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "yamil-play-btn";
      btn.innerHTML = '<i class="fas fa-volume-high"></i> Escuchar';
      btn.addEventListener("click", function () { reproducirTTS(mensajeId, btn); });
      fila.appendChild(btn);
    }

    lista.appendChild(fila);
    lista.scrollTop = lista.scrollHeight;
    return fila;
  }

  function reproducirTTS(mensajeId, btnEl) {
    if (btnEl) { btnEl.disabled = true; btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando audio...'; }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "admin/ajax/ia_tts.php");
    xhr.responseType = "blob";
    xhr.onload = function () {
      if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = '<i class="fas fa-volume-high"></i> Escuchar'; }
      if (xhr.status !== 200) return;
      const url = URL.createObjectURL(xhr.response);
      const audio = new Audio(url);
      audio.play().catch(function () {});
    };
    xhr.onerror = function () {
      if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = '<i class="fas fa-volume-high"></i> Escuchar'; }
    };
    const params = new URLSearchParams();
    params.set("mensaje_id", mensajeId);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(params.toString());
  }

  function mostrarTyping() {
    const lista = document.getElementById("yamilMensajes");
    if (!lista) return null;
    const fila = document.createElement("div");
    fila.className = "yamil-msg yamil-msg-bot yamil-typing";
    fila.innerHTML = "<span></span><span></span><span></span>";
    lista.appendChild(fila);
    lista.scrollTop = lista.scrollHeight;
    return fila;
  }

  const adjuntos = [];
  const MAX_ADJUNTOS = 4;
  const MAX_BYTES_ADJUNTO = 8 * 1024 * 1024;

  function pintarAdjuntos() {
    const caja = document.getElementById("yamilAdjuntos");
    if (!caja) return;
    caja.innerHTML = "";
    caja.style.display = adjuntos.length ? "flex" : "none";
    adjuntos.forEach(function (a, i) {
      const chip = document.createElement("div");
      chip.className = "yamil-adjunto";
      const nombre = document.createElement("span");
      nombre.textContent = a.archivo.name;
      const tipo = document.createElement("select");
      [["foto", "Foto"], ["logo", "Logo"]].forEach(function (o) {
        const op = document.createElement("option");
        op.value = o[0];
        op.textContent = o[1];
        if (a.tipo === o[0]) op.selected = true;
        tipo.appendChild(op);
      });
      tipo.addEventListener("change", function () { a.tipo = tipo.value; });
      const quitar = document.createElement("button");
      quitar.type = "button";
      quitar.innerHTML = "&times;";
      quitar.title = "Quitar";
      quitar.addEventListener("click", function () { adjuntos.splice(i, 1); pintarAdjuntos(); });
      chip.appendChild(nombre);
      chip.appendChild(tipo);
      chip.appendChild(quitar);
      caja.appendChild(chip);
    });
  }

  function agregarAdjuntos(lista) {
    Array.prototype.forEach.call(lista, function (f) {
      if (adjuntos.length >= MAX_ADJUNTOS) return;
      if (!/^image\/(png|jpeg|webp|gif)$/.test(f.type)) {
        agregarMensaje("assistant", "Solo puedo recibir imágenes JPG, PNG, WEBP o GIF.");
        return;
      }
      if (f.size > MAX_BYTES_ADJUNTO) {
        agregarMensaje("assistant", "La imagen «" + f.name + "» supera el máximo de 8 MB.");
        return;
      }
      adjuntos.push({ archivo: f, tipo: /logo/i.test(f.name) ? "logo" : "foto" });
    });
    pintarAdjuntos();
  }

  function enviarMensaje() {
    if (enviando) return;

    const input = document.getElementById("yamilInput");
    if (!input) return;

    let texto = input.value.trim();
    if (!texto && !adjuntos.length) return;
    if (!texto) texto = "Te adjunto " + (adjuntos.length > 1 ? "estas imágenes." : "esta imagen.");

    input.value = "";
    agregarMensaje("user", texto + (adjuntos.length ? "\n📎 " + adjuntos.map(function (a) { return a.archivo.name; }).join(", ") : ""));

    enviando = true;
    const btn = document.getElementById("yamilEnviar");
    if (btn) btn.disabled = true;

    const datos = new FormData();
    datos.set("accion", "iniciar");
    datos.set("mensaje", texto);
    datos.set("client_request_id", nuevoId());
    if (conversacionActual) datos.set("conversacion_id", conversacionActual);
    adjuntos.forEach(function (a) {
      datos.append("imagen[]", a.archivo);
      datos.append("tipo_imagen[]", a.tipo);
    });
    adjuntos.length = 0;
    pintarAdjuntos();

    const caja = crearCajaProgreso();
    seguirEjecucion(datos, caja, function () {
      enviando = false;
      if (btn) btn.disabled = false;
    });
  }

  function nuevoId() {
    if (window.crypto && window.crypto.randomUUID) return window.crypto.randomUUID().replace(/-/g, "");
    return String(Date.now()) + Math.random().toString(36).slice(2, 10);
  }

  function crearCajaProgreso() {
    const lista = document.getElementById("yamilMensajes");
    const fila = document.createElement("div");
    fila.className = "yamil-msg yamil-msg-bot yamil-progreso";
    fila.innerHTML = '<div class="yamil-prog-cab"><span class="yamil-prog-spin"></span><div class="yamil-prog-txt"><div class="yamil-prog-titulo">Yamil está trabajando…</div><div class="yamil-prog-detalle"></div></div><button type="button" class="yamil-prog-cancelar">Cancelar</button></div><ul class="yamil-prog-pasos"></ul>';
    lista.appendChild(fila);
    lista.scrollTop = lista.scrollHeight;
    return fila;
  }

  function pintarProgreso(caja, e) {
    const titulos = { queued: "En cola…", llamada: "Pensando…", pensando: "Pensando…", herramienta: "Consultando…", esperando: "Continuando…" };
    caja.querySelector(".yamil-prog-titulo").textContent = (e.detalle && e.fase !== "queued") ? e.detalle : (titulos[e.fase] || "Yamil está trabajando…");
    let det = e.transcurrido_s + " s";
    if (e.sin_senales_s > 90) det += " · el servidor tarda en responder, sigo intentando";
    caja.querySelector(".yamil-prog-detalle").textContent = det;
    const ul = caja.querySelector(".yamil-prog-pasos");
    ul.innerHTML = "";
    (e.pasos || []).slice(-5).forEach(function (p) {
      const li = document.createElement("li");
      li.textContent = p.x;
      ul.appendChild(li);
    });
    const lista = document.getElementById("yamilMensajes");
    if (lista) lista.scrollTop = lista.scrollHeight;
  }

  function postRun(accion, extra) {
    const f = new FormData();
    f.set("accion", accion);
    Object.keys(extra || {}).forEach(function (k) { f.set(k, extra[k]); });
    return fetch("admin/ajax/ia_run.php", { method: "POST", body: f }).then(function (r) { return r.json(); });
  }

  function seguirEjecucion(datosInicio, caja, alTerminar) {
    let token = null;
    let temporizador = null;
    let conduciendo = false;
    let detenido = false;
    let fallosRed = 0;

    const terminar = function (fn) {
      detenido = true;
      clearTimeout(temporizador);
      caja.remove();
      fn();
      alTerminar();
    };

    const mostrarError = function (mensaje, reintentable) {
      terminar(function () {
        const fila = agregarMensaje("assistant", mensaje);
        if (reintentable && token && fila) {
          const b = document.createElement("button");
          b.type = "button";
          b.className = "yamil-play-btn";
          b.textContent = "Reintentar";
          b.addEventListener("click", function () {
            b.disabled = true;
            postRun("reintentar", { run: token }).then(function (r) {
              if (r && r.output && r.output.valid) {
                enviando = true;
                const btn = document.getElementById("yamilEnviar");
                if (btn) btn.disabled = true;
                seguirEjecucion(null, crearCajaProgreso(), function () { enviando = false; if (btn) btn.disabled = false; }, token);
              }
            });
          });
          fila.appendChild(b);
        }
      });
    };

    const conducir = function () {
      if (conduciendo || detenido) return;
      conduciendo = true;
      postRun("conducir", { run: token })
        .catch(function () { return null; })
        .then(function () { conduciendo = false; });
    };

    const consultar = function () {
      if (detenido) return;
      fetch("admin/ajax/ia_run.php?accion=estado&run=" + encodeURIComponent(token), { cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          fallosRed = 0;
          const e = data && data.output ? data.output.response : null;
          if (!e || e.estado === "no_encontrada") {
            mostrarError("No se encontró la consulta en proceso. Revisa el historial antes de reenviar el mensaje.", false);
            return;
          }
          if (e.estado === "done") {
            terminar(function () {
              conversacionActual = e.conversacion_id;
              agregarMensaje("assistant", e.respuesta || "(sin respuesta)", e.mensaje_id);
            });
            return;
          }
          if (e.estado === "failed" || e.estado === "cancelled") {
            mostrarError((e.error && e.error.mensaje) || "La consulta no se pudo completar.", !!e.reintentable);
            return;
          }
          pintarProgreso(caja, e);
          if (e.conducir) conducir();
          temporizador = setTimeout(consultar, document.hidden ? 6000 : 2500);
        })
        .catch(function () {
          fallosRed++;
          if (fallosRed > 12) {
            mostrarError("Se perdió la conexión con el servidor. Revisa el historial o Informes IA antes de reenviar el mensaje: puede que Yamil haya terminado.", false);
            return;
          }
          temporizador = setTimeout(consultar, 4000);
        });
    };

    caja.querySelector(".yamil-prog-cancelar").addEventListener("click", function () {
      postRun("cancelar", { run: token });
    });

    if (datosInicio === null) {
      token = arguments[3];
      consultar();
      return;
    }

    fetch("admin/ajax/ia_run.php", { method: "POST", body: datosInicio })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        const r = data && data.output ? data.output.response : null;
        if (!r || !r.run_token) {
          mostrarError((r && r.mensaje) || "No se pudo iniciar la consulta.", false);
          return;
        }
        token = r.run_token;
        conversacionActual = r.conversacion_id || conversacionActual;
        conducir();
        consultar();
      })
      .catch(function () {
        mostrarError("No se pudo conectar con Yamil. Antes de reenviar el mensaje, revisa el historial: puede que ya se esté procesando.", false);
      });
  }

  function saludoInicial() {
    agregarMensaje("assistant", "Hola, soy Yamil. ¿En qué puedo ayudarte con tus encuestas y estadísticas hoy?");
  }

  function nuevaConversacion() {
    conversacionActual = null;
    const lista = document.getElementById("yamilMensajes");
    if (lista) lista.innerHTML = "";
    saludoInicial();
  }

  function cargarHistorialReciente() {
    const lista = document.getElementById("yamilMensajes");
    if (!lista) return;
    lista.innerHTML = "";

    fetch("admin/ajax/ia_historial.php?accion=listar")
      .then(function (r) { return r.json(); })
      .then(function (data) {
        const conversaciones = data && data.output && data.output.valid ? data.output.response : [];
        if (!conversaciones || !conversaciones.length) {
          saludoInicial();
          return;
        }

        const ultimaId = conversaciones[0].id;
        return fetch("admin/ajax/ia_historial.php?accion=cargar&conversacion_id=" + encodeURIComponent(ultimaId))
          .then(function (r) { return r.json(); })
          .then(function (data2) {
            const mensajes = data2 && data2.output && data2.output.valid ? data2.output.response : [];
            if (!mensajes || !mensajes.length) {
              saludoInicial();
              return;
            }
            conversacionActual = ultimaId;
            mensajes.forEach(function (m) {
              agregarMensaje(m.rol === "user" ? "user" : "assistant", m.contenido || "", m.id);
            });
          });
      })
      .catch(function () {
        saludoInicial();
      });
  }

  function tipoAudioSoportado() {
    if (window.MediaRecorder && MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported("audio/webm")) return "audio/webm";
    if (window.MediaRecorder && MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported("audio/mp4")) return "audio/mp4";
    return "";
  }

  function iniciarGrabacion() {
    const estado = document.getElementById("yamilVozEstado");
    const boton = document.getElementById("yamilMic");

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || !window.MediaRecorder) {
      if (estado) estado.textContent = "Tu navegador no soporta grabación de audio. Usa el modo Texto.";
      return;
    }

    navigator.mediaDevices.getUserMedia({ audio: true })
      .then(function (stream) {
        const tipo = tipoAudioSoportado();
        fragmentosAudio = [];
        grabador = tipo ? new MediaRecorder(stream, { mimeType: tipo }) : new MediaRecorder(stream);
        grabador.addEventListener("dataavailable", function (e) {
          if (e.data && e.data.size > 0) fragmentosAudio.push(e.data);
        });
        grabador.addEventListener("stop", function () {
          stream.getTracks().forEach(function (t) { t.stop(); });
          const blob = new Blob(fragmentosAudio, { type: grabador.mimeType || "audio/webm" });
          enviarAudio(blob);
        });
        grabador.start();
        grabando = true;
        if (boton) boton.classList.add("yamil-grabando");
        if (estado) estado.textContent = "Grabando... toca de nuevo para enviar.";
      })
      .catch(function () {
        if (estado) estado.textContent = "No se pudo acceder al micrófono. Revisa los permisos del navegador.";
      });
  }

  function detenerGrabacion() {
    if (grabador && grabando) {
      grabador.stop();
    }
    grabando = false;
    const boton = document.getElementById("yamilMic");
    if (boton) boton.classList.remove("yamil-grabando");
  }

  function enviarAudio(blob) {
    const estado = document.getElementById("yamilVozEstado");
    if (estado) estado.textContent = "Transcribiendo y pensando...";

    const datos = new FormData();
    datos.append("audio", blob, "grabacion.webm");
    if (conversacionActual) datos.append("conversacion_id", conversacionActual);

    fetch("admin/ajax/ia_stt.php", { method: "POST", body: datos })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        const res = data && data.output ? data.output.response : null;
        if (res && res.valid) {
          conversacionActual = res.conversacion_id;
          agregarMensaje("user", res.transcripcion || "(audio)");
          agregarMensaje("assistant", res.respuesta || "(sin respuesta)", res.mensaje_id);
          if (estado) estado.textContent = "Toca el micrófono para hablar con Yamil";
          if (res.mensaje_id) reproducirTTS(res.mensaje_id, null);
        } else {
          if (estado) estado.textContent = (res && res.message) || "Ocurrió un error procesando el audio.";
        }
      })
      .catch(function () {
        if (estado) estado.textContent = "No se pudo conectar con Yamil. Intenta de nuevo.";
      });
  }

  function activarModoTexto() {
    const textoBtn = document.getElementById("yamilModoTexto");
    const vozBtn = document.getElementById("yamilModoVoz");
    const inputbar = document.getElementById("yamilInputbarTexto");
    const vozBar = document.getElementById("yamilVozBar");
    if (textoBtn) textoBtn.classList.add("yamil-modo-activo");
    if (vozBtn) vozBtn.classList.remove("yamil-modo-activo");
    if (inputbar) inputbar.classList.remove("yamil-oculto");
    if (vozBar) vozBar.classList.remove("yamil-voz-activa");
    if (grabando) detenerGrabacion();
  }

  function activarModoVoz() {
    const textoBtn = document.getElementById("yamilModoTexto");
    const vozBtn = document.getElementById("yamilModoVoz");
    const inputbar = document.getElementById("yamilInputbarTexto");
    const vozBar = document.getElementById("yamilVozBar");
    if (vozBtn) vozBtn.classList.add("yamil-modo-activo");
    if (textoBtn) textoBtn.classList.remove("yamil-modo-activo");
    if (inputbar) inputbar.classList.add("yamil-oculto");
    if (vozBar) vozBar.classList.add("yamil-voz-activa");
  }

  function togglePanel() {
    const panel = document.getElementById("yamilPanel");
    if (!panel) return;
    const abierto = panel.classList.toggle("yamil-open");
    if (abierto && !document.getElementById("yamilMensajes").children.length) {
      cargarHistorialReciente();
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    const boton = document.getElementById("yamilBoton");
    const cerrar = document.getElementById("yamilCerrar");
    const enviar = document.getElementById("yamilEnviar");
    const nuevo = document.getElementById("yamilNuevo");
    const input = document.getElementById("yamilInput");
    const modoTexto = document.getElementById("yamilModoTexto");
    const modoVoz = document.getElementById("yamilModoVoz");
    const mic = document.getElementById("yamilMic");

    if (boton) boton.addEventListener("click", togglePanel);
    if (cerrar) cerrar.addEventListener("click", togglePanel);
    if (enviar) enviar.addEventListener("click", enviarMensaje);
    const clip = document.getElementById("yamilClip");
    const archivo = document.getElementById("yamilArchivo");
    if (clip && archivo) {
      clip.addEventListener("click", function () { archivo.click(); });
      archivo.addEventListener("change", function () { agregarAdjuntos(archivo.files); archivo.value = ""; });
    }
    if (nuevo) nuevo.addEventListener("click", nuevaConversacion);
    if (input) {
      input.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          enviarMensaje();
        }
      });
    }
    if (modoTexto) modoTexto.addEventListener("click", activarModoTexto);
    if (modoVoz) modoVoz.addEventListener("click", activarModoVoz);
    if (mic) {
      mic.addEventListener("click", function () {
        if (grabando) {
          detenerGrabacion();
        } else {
          iniciarGrabacion();
        }
      });
    }
  });
})();
