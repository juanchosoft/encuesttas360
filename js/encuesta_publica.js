(function ($) {
    "use strict";

    var CFG = window.ENCUESTA_CONFIG || {};
    var ES = {
        def: null,
        respuestas: {},
        uiState: { filaActivaPorPregunta: {} },
        municipio: "",
        zona: "",
        curPid: 0,
        clientUuid: "",
        enviando: false
    };

    function uuid() {
        if (window.crypto && typeof window.crypto.randomUUID === "function") {
            return window.crypto.randomUUID();
        }
        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
            var r = (Math.random() * 16) | 0;
            return (c === "x" ? r : (r & 0x3) | 0x8).toString(16);
        });
    }

    function mostrarError(msg) {
        $("#encCargando").addClass("d-none");
        $("#encError").removeClass("d-none").text(msg);
    }

    function claveBorrador() {
        return "enc360_pub_" + CFG.fichaId + "_" + (ES.def ? ES.def.version : "v0");
    }

    function guardarBorrador() {
        try {
            window.localStorage.setItem(claveBorrador(), JSON.stringify({
                municipio: ES.municipio,
                zona: ES.zona,
                respuestas: ES.respuestas,
                curPid: ES.curPid,
                clientUuid: ES.clientUuid,
                ts: Date.now()
            }));
        } catch (e) {}
    }

    function recuperarBorrador() {
        try {
            var raw = window.localStorage.getItem(claveBorrador());
            if (!raw) return false;
            var d = JSON.parse(raw);
            if (!d || !d.respuestas) return false;
            ES.municipio = d.municipio || "";
            ES.zona = d.zona || "";
            ES.respuestas = d.respuestas;
            ES.curPid = d.curPid || 0;
            ES.clientUuid = d.clientUuid || "";
            return Object.keys(ES.respuestas).length > 0 || ES.municipio !== "";
        } catch (e) {
            return false;
        }
    }

    function borrarBorrador() {
        try { window.localStorage.removeItem(claveBorrador()); } catch (e) {}
    }

    function municipioActual() {
        var lista = (ES.def && ES.def.municipios_disponibles) || [];
        for (var i = 0; i < lista.length; i++) {
            if (lista[i].codigo_municipio === ES.municipio) return lista[i];
        }
        return null;
    }

    function contexto() {
        var m = municipioActual();
        return {
            codigo_municipio: ES.municipio,
            nombre_municipio: m ? m.municipio : (ES.municipio || "su municipio"),
            codigo_departamento: m ? m.codigo_departamento : "",
            publico: true
        };
    }

    function arregloRespuestas() {
        var lista = [];
        Object.keys(ES.respuestas).forEach(function (pidKey) {
            var item = ES.respuestas[pidKey];
            if (!item) return;
            var grilla = [];
            if (item.grilla && typeof item.grilla === "object") {
                Object.keys(item.grilla).forEach(function (fKey) {
                    var cols = item.grilla[fKey] || {};
                    Object.keys(cols).forEach(function (cKey) {
                        var opts = cols[cKey];
                        if (Array.isArray(opts) && opts.length > 0) {
                            grilla.push({ fila_id: parseInt(fKey, 10), columna_id: parseInt(cKey, 10), opciones: opts });
                        }
                    });
                });
            }
            lista.push({
                pregunta_id: parseInt(pidKey, 10),
                opciones: Array.isArray(item.opciones) ? item.opciones : [],
                texto: item.texto || "",
                grilla: grilla,
                lista: item.lista || null
            });
        });
        return lista;
    }

    function llenarMunicipios() {
        var lista = ES.def.municipios_disponibles || [];
        var html = '<option value="">Seleccione su municipio...</option>';
        lista.forEach(function (m) {
            html += '<option value="' + MotorCuestionarioUI.esc(m.codigo_municipio) + '">' + MotorCuestionarioUI.esc(m.municipio) + "</option>";
        });
        $("#encMunicipio").html(html);
        if (ES.municipio) {
            $("#encMunicipio").val(ES.municipio);
        } else if (CFG.municipioGps && lista.some(function (m) { return m.codigo_municipio === CFG.municipioGps; })) {
            ES.municipio = CFG.municipioGps;
            $("#encMunicipio").val(ES.municipio);
        }
        llenarZonas();
    }

    function llenarZonas() {
        var zonas = (ES.def.zonas_muestreo || []).filter(function (z) {
            return z.codigo_municipio === ES.municipio;
        });
        if (zonas.length === 0) {
            $("#encZonaWrap").addClass("d-none");
            ES.zona = "";
            return;
        }
        var html = '<option value="">Seleccione...</option>';
        zonas.forEach(function (z) {
            html += '<option value="' + z.id + '">' + MotorCuestionarioUI.esc(z.nombre) + "</option>";
        });
        $("#encZona").html(html);
        $("#encZonaWrap").removeClass("d-none");
        if (ES.zona && zonas.some(function (z) { return String(z.id) === String(ES.zona); })) {
            $("#encZona").val(ES.zona);
        } else {
            ES.zona = "";
        }
    }

    function zonaRequerida() {
        return !$("#encZonaWrap").hasClass("d-none");
    }

    function comenzar() {
        if (!ES.municipio) {
            Swal.fire({ icon: "warning", title: "Falta su municipio", text: "Escoja el municipio donde vive.", confirmButtonText: "Entendido" });
            return;
        }
        if (zonaRequerida() && !ES.zona) {
            Swal.fire({ icon: "warning", title: "Falta su zona", text: "Escoja su comuna, sector o zona.", confirmButtonText: "Entendido" });
            return;
        }
        guardarBorrador();
        $("#encPasoInicio").addClass("d-none");
        $("#encPasoPreguntas").removeClass("d-none");
        render();
    }

    function render() {
        var ctx = contexto();
        var ev = MotorCuestionario.evaluar(ES.def, arregloRespuestas(), ctx);
        var visibles = ev.visibles || [];
        var porId = {};
        (ES.def.preguntas || []).forEach(function (p) { porId[parseInt(p.id, 10)] = p; });

        var idxSig = visibles.length;
        if (ev.siguiente_pregunta_id) {
            var s = visibles.indexOf(ev.siguiente_pregunta_id);
            if (s >= 0) idxSig = s;
        }

        var idx = visibles.indexOf(ES.curPid);
        if (idx < 0 || idx > idxSig) {
            idx = Math.min(idxSig, visibles.length - 1);
        }
        if (idx < 0) idx = 0;
        ES.curPid = visibles[idx] || 0;

        var html = "";
        if (ev.terminado) {
            html = MotorCuestionarioUI.renderCierreFiltro(ev.mensaje_cierre || (ES.def.ficha && ES.def.ficha.mensaje_cierre_defecto), "", true);
        } else if (ES.curPid && porId[ES.curPid]) {
            var rPreg = ES.respuestas[ES.curPid] || { pregunta_id: ES.curPid, opciones: [], texto: "", grilla: {}, lista: null };
            html = MotorCuestionarioUI.renderPreguntaCard(ES.def, porId[ES.curPid], ev, rPreg, ctx, ES.uiState);
        }
        $("#encContenedor").html(html);

        var respondidas = ev.terminado ? visibles.length : Math.min(idxSig, visibles.length);
        var total = Math.max(visibles.length, 1);
        var pct = Math.round((respondidas / total) * 100);
        $("#encBarra").css("width", pct + "%");
        $("#encProgresoTexto").text(ev.terminado
            ? "Gracias, ya puede enviar"
            : "Pregunta " + (idx + 1) + " de " + visibles.length);

        var completaActual = ev.terminado || idx < idxSig;
        var esUltima = idx >= visibles.length - 1;
        $("#encAnterior").prop("disabled", idx === 0 && !ev.terminado).toggleClass("d-none", ev.terminado && idx === 0 && visibles.length <= 1);
        $("#encContinuar").toggleClass("d-none", ev.terminado || (esUltima && completaActual)).prop("disabled", !completaActual);
        $("#encEnviar").toggleClass("d-none", !(ev.terminado || (esUltima && completaActual)));
        $("#encEnviar").text("").html('<i class="fa-solid fa-paper-plane me-2"></i>' + (ev.terminado ? "Enviar y terminar" : "Enviar mis respuestas"));
        guardarBorrador();
    }

    function irA(delta) {
        var ev = MotorCuestionario.evaluar(ES.def, arregloRespuestas(), contexto());
        var visibles = ev.visibles || [];
        var idx = visibles.indexOf(ES.curPid);
        var nuevo = idx + delta;
        if (nuevo < 0 || nuevo >= visibles.length) return;
        ES.curPid = visibles[nuevo];
        render();
        var top = document.getElementById("cuestionario_container");
        if (top) top.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function enviar() {
        if (ES.enviando) return;
        var m = municipioActual();
        if (!ES.clientUuid) ES.clientUuid = uuid();
        var payload = {
            ficha_tecnica_id: CFG.fichaId,
            definicion_hash: ES.def.version,
            client_uuid: ES.clientUuid,
            codigo_departamento: m ? m.codigo_departamento : "",
            codigo_municipio: ES.municipio,
            tbl_zona_muestreo_id: ES.zona || "",
            respuestas: arregloRespuestas()
        };

        ES.enviando = true;
        $("#encEnviar").prop("disabled", true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i>Enviando...');

        var body = new URLSearchParams();
        body.set("op", "participaciondraft");
        body.set("tipo", "encuesta");
        body.set("data", JSON.stringify(payload));
        if (window.DeviceParticipacion) {
            DeviceParticipacion.appendToUrlSearchParams(body);
        }

        fetch("admin/ajax/rqst.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
            body: body.toString(),
            credentials: "same-origin"
        }).then(function (r) { return r.text(); }).then(function (raw) {
            var res = null;
            try { res = JSON.parse(raw); } catch (e) { throw new Error("No se pudo leer la respuesta del servidor."); }
            if (res && res.output && res.output.valid) {
                borrarBorrador();
                window.location.href = res.output.redirect || CFG.urlGracias;
                return;
            }
            var code = res && res.output && res.output.response && res.output.response.code;
            var msg = (res && res.output && res.output.response && res.output.response.content) || (res && res.output && res.output.message) || "No fue posible guardar la encuesta.";
            if (code === "ALREADY_PARTICIPATED") {
                borrarBorrador();
                $("#encPasoPreguntas").addClass("d-none");
                $("#encYaParticipo").removeClass("d-none");
                return;
            }
            return Swal.fire({ icon: "error", title: "No se pudo guardar", text: msg, confirmButtonText: "Entendido" });
        }).catch(function (err) {
            return Swal.fire({ icon: "error", title: "Sin conexión", text: (err && err.message) || "Revise su conexión e intente de nuevo. Sus respuestas siguen guardadas en este dispositivo.", confirmButtonText: "Reintentar" });
        }).then(function () {
            ES.enviando = false;
            if (!$("#encPasoPreguntas").hasClass("d-none")) render();
        });
    }

    function iniciar() {
        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            dataType: "json",
            data: { op: "cuestionariodefinicionpublicoget", ficha_id: CFG.fichaId },
            success: function (res) {
                if (!(res && res.output && res.output.valid && res.output.response)) {
                    mostrarError("Esta encuesta no está disponible por ahora.");
                    return;
                }
                ES.def = res.output.response;
                $("#encCargando").addClass("d-none");
                $(".hero-title").text(ES.def.ficha.temas || ES.def.ficha.titulo || "Encuesta");

                var body = new URLSearchParams();
                body.set("op", "participaciondevicestatus");
                if (window.DeviceParticipacion) DeviceParticipacion.appendToUrlSearchParams(body);
                fetch("admin/ajax/rqst.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                    body: body.toString(),
                    credentials: "same-origin"
                }).then(function (r) { return r.json(); }).then(function (st) {
                    var hechas = (st && st.output && st.output.encuestas_hechas) || (st && st.output && st.output.done_encuesta) || [];
                    if (hechas.map(Number).indexOf(Number(CFG.fichaId)) !== -1) {
                        $("#encYaParticipo").removeClass("d-none");
                        return;
                    }
                    arrancar();
                }).catch(arrancar);
            },
            error: function () {
                mostrarError("No pudimos cargar la encuesta. Revise su conexión e intente de nuevo.");
            }
        });
    }

    function arrancar() {
        var hayBorrador = recuperarBorrador();
        var pedirInicio = parseInt(ES.def.ficha.pedir_municipio_zona_inicio, 10) !== 0;
        llenarMunicipios();
        MotorCuestionarioUI.vincularEventos({
            respuestas: ES.respuestas,
            uiState: ES.uiState,
            guardar: guardarBorrador,
            render: render,
            aviso: function (t) { Swal.fire({ icon: "info", text: t, confirmButtonText: "Entendido" }); },
            alEscribir: function () {
                var ev = MotorCuestionario.evaluar(ES.def, arregloRespuestas(), contexto());
                var vis = ev.visibles || [];
                var idxSig = ev.siguiente_pregunta_id ? vis.indexOf(ev.siguiente_pregunta_id) : vis.length;
                $("#encContinuar, #encEnviar").prop("disabled", !(vis.indexOf(ES.curPid) < idxSig || ev.terminado));
            }
        });

        if (ES.municipio && (!pedirInicio || hayBorrador) && (!zonaRequerida() || ES.zona)) {
            $("#encPasoPreguntas").removeClass("d-none");
            render();
            return;
        }
        $("#encPasoInicio").removeClass("d-none");
    }

    $(function () {
        if (typeof MotorCuestionario === "undefined" || typeof MotorCuestionarioUI === "undefined") {
            mostrarError("No se pudo cargar la encuesta.");
            return;
        }
        $("#encMunicipio").on("change", function () {
            ES.municipio = $(this).val() || "";
            ES.zona = "";
            llenarZonas();
        });
        $("#encZona").on("change", function () {
            ES.zona = $(this).val() || "";
        });
        $("#encComenzar").on("click", comenzar);
        $("#encAnterior").on("click", function () { irA(-1); });
        $("#encContinuar").on("click", function () { irA(1); });
        $("#encEnviar").on("click", enviar);
        iniciar();
    });
})(jQuery);
