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

  function enviarMensaje() {
    if (enviando) return;

    const input = document.getElementById("yamilInput");
    if (!input) return;

    const texto = input.value.trim();
    if (!texto) return;

    input.value = "";
    agregarMensaje("user", texto);

    enviando = true;
    const typing = mostrarTyping();
    const btn = document.getElementById("yamilEnviar");
    if (btn) btn.disabled = true;

    const params = new URLSearchParams();
    params.set("mensaje", texto);
    if (conversacionActual) params.set("conversacion_id", conversacionActual);

    fetch("admin/ajax/ia_chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString()
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (typing) typing.remove();
        const res = data && data.output ? data.output.response : null;

        if (res && res.valid) {
          conversacionActual = res.conversacion_id;
          agregarMensaje("assistant", res.respuesta || "(sin respuesta)", res.mensaje_id);
        } else {
          agregarMensaje("assistant", (res && res.mensaje) || "Ocurrió un error al hablar con Yamil.");
        }
      })
      .catch(function () {
        if (typing) typing.remove();
        agregarMensaje("assistant", "No se pudo conectar con Yamil. Intenta de nuevo.");
      })
      .finally(function () {
        enviando = false;
        if (btn) btn.disabled = false;
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
