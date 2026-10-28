$(document).ready(function () {
    var esModoPrueba = $("#esModoPrueba").val() === "1";

    var $elementosA_Bloquear = $('#formvotantes input, #formvotantes select, #formvotantes textarea, .btn-cta-save, .btn-cta-cancel');

    if (!esModoPrueba) {
        $elementosA_Bloquear
            .not('#op, #idVotantes, #opcionActivaWeb, #esModoPrueba, #departamentoEncuestador, #municipioEncuestador')
            .prop('disabled', true)
            .addClass('disabled');
    } else {
        VOTANTES._desbloqueado = true;
    }

    $(document).on('certification-started', function () {
        VOTANTES._desbloqueado = true;
        $elementosA_Bloquear.prop('disabled', false).removeClass('disabled');
        $('#preguntas_container button, #preguntas_container textarea, #preguntas_container input').prop('disabled', false);
        $('.btn-cta-save, .btn-cta-cancel').css('cursor', 'pointer').css('opacity', '1');
    });

    VOTANTES.initMotorCuestionario();
});

var return_page = "votantes_encuestador.php";
var VOTANTES = {
    _guardando: false,
    _desbloqueado: false,
    _clientUuid: null,
    _ultimoVotanteId: 0,
    _ultimoIntentoId: 0,
    _def: null,
    _respuestas: {},
    _uiState: {
        filaActivaPorPregunta: {}
    },

    esModoPrueba: function () {
        return $("#esModoPrueba").val() === "1";
    },

    obtenerClientUuid: function () {
        if (!VOTANTES._clientUuid) {
            if (window.crypto && typeof window.crypto.randomUUID === "function") {
                VOTANTES._clientUuid = window.crypto.randomUUID();
            } else {
                VOTANTES._clientUuid = "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
                    var r = (Math.random() * 16) | 0,
                        v = c === "x" ? r : (r & 0x3) | 0x8;
                    return v.toString(16);
                });
            }
        }
        return VOTANTES._clientUuid;
    },

    tieneSondeoActivo: function () {
        return ($("#sondeoActivoId").val() || "").toString().trim() !== "";
    },

    tieneCuestionarioActivo: function () {
        return ($("#cuestionarioActivoId").val() || "").toString().trim() !== "";
    },

    obtenerClaveBorrador: function () {
        var fId = ($("#cuestionarioActivoId").val() || "0").toString();
        var hash = (VOTANTES._def && VOTANTES._def.version) ? VOTANTES._def.version : "v0";
        return "enc360_draft_int_" + fId + "_" + hash;
    },

    guardarBorradorLocal: function () {
        if (!VOTANTES._def || typeof window.localStorage === "undefined") {
            return;
        }
        try {
            var payload = {
                version: VOTANTES._def.version,
                codigo_departamento: $("#tbl_departamento_id").val() || "",
                codigo_municipio: $("#tbl_municipio_id").val() || "",
                tbl_zona_muestreo_id: $("#tbl_zona_muestreo_id").val() || "",
                comuna: $("#comuna").val() || "",
                barrio: $("#barrio").val() || "",
                respuestas: VOTANTES._respuestas,
                ts: Date.now()
            };
            window.localStorage.setItem(VOTANTES.obtenerClaveBorrador(), JSON.stringify(payload));
        } catch (e) {}
    },

    intentarRecuperarBorradorLocal: function () {
        if (!VOTANTES._def || typeof window.localStorage === "undefined") {
            return;
        }
        try {
            var raw = window.localStorage.getItem(VOTANTES.obtenerClaveBorrador());
            if (!raw) return;
            var parsed = JSON.parse(raw);
            if (!parsed || parsed.version !== VOTANTES._def.version || !parsed.respuestas) {
                return;
            }
            var keys = Object.keys(parsed.respuestas);
            if (keys.length === 0) return;
            VOTANTES._respuestas = parsed.respuestas;
            if (parsed.comuna) $("#comuna").val(parsed.comuna);
            if (parsed.barrio) $("#barrio").val(parsed.barrio);
            $("#avisoBorradorRecuperado").removeClass("d-none").addClass("d-flex");
        } catch (e) {}
    },

    descartarBorradorLocal: function () {
        if (typeof window.localStorage !== "undefined" && VOTANTES._def) {
            try {
                window.localStorage.removeItem(VOTANTES.obtenerClaveBorrador());
            } catch (e) {}
        }
        VOTANTES._respuestas = {};
        VOTANTES._uiState.filaActivaPorPregunta = {};
        $("#avisoBorradorRecuperado").addClass("d-none").removeClass("d-flex");
        VOTANTES.renderizarCuestionario();
    },

    initMotorCuestionario: function () {
        if (!VOTANTES.tieneCuestionarioActivo()) {
            return;
        }
        var fichaId = parseInt($("#cuestionarioActivoId").val(), 10) || 0;
        if (fichaId <= 0) {
            return;
        }

        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            dataType: "json",
            data: {
                op: "cuestionariodefinicionget",
                ficha_id: fichaId
            },
            success: function (res) {
                if (res && res.output && res.output.valid && res.output.response) {
                    VOTANTES._def = res.output.response;
                    $("#cuestionarioDefinicionHash").val(VOTANTES._def.version || "");

                    if (VOTANTES._def.ficha && parseInt(VOTANTES._def.ficha.audio_obligatorio, 10) === 0) {
                        VOTANTES._desbloqueado = true;
                        $('#formvotantes input, #formvotantes select, #formvotantes textarea, .btn-cta-save, .btn-cta-cancel')
                            .prop('disabled', false)
                            .removeClass('disabled');
                    }

                    VOTANTES.ocultarDemograficosCubiertosPorCuestionario();
                    VOTANTES.intentarRecuperarBorradorLocal();
                    VOTANTES.actualizarZonasMunicipio();
                    VOTANTES.renderizarCuestionario();
                    VOTANTES.vincularEventosMotor();
                } else {
                    $("#preguntas_container").html(
                        '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>No se pudo cargar el cuestionario activo.</div>'
                    );
                }
            },
            error: function () {
                $("#preguntas_container").html(
                    '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Error de red al cargar el cuestionario.</div>'
                );
            }
        });
    },

    ocultarDemograficosCubiertosPorCuestionario: function () {
        if (!VOTANTES._def) return;
        var camposCubiertos = {};
        (VOTANTES._def.preguntas || []).forEach(function (p) {
            var c = (p.copiar_a_votante_campo || "").trim();
            if (c && c !== "ninguno") {
                if (c === "ingresos" || c === "estrato") c = "nivel_ingresos";
                camposCubiertos[c] = true;
            }
        });
        (VOTANTES._def.equivalencias_votante || []).forEach(function (eq) {
            var c = (eq.campo_votante || "").trim();
            if (c) {
                if (c === "ingresos" || c === "estrato") c = "nivel_ingresos";
                camposCubiertos[c] = true;
            }
        });

        $(".ve-campo-demografico-votante").each(function () {
            $(this).addClass("d-none");
            $(this).find("select, input").prop("required", false);
        });
    },

    obtenerContextoMotor: function () {
        var codMpio = ($("#tbl_municipio_id").val() || $("#municipioEncuestador").val() || "").toString().trim();
        var nomMpio = $("#tbl_municipio_id option:selected").text() || "";
        nomMpio = nomMpio.replace(/^\d+\s*-\s*/, "").trim();
        return {
            codigo_municipio: codMpio,
            nombre_municipio: nomMpio || codMpio || "su municipio",
            codigo_departamento: ($("#tbl_departamento_id").val() || "").toString().trim()
        };
    },

    actualizarZonasMunicipio: function () {
        if (!VOTANTES._def) return;
        var codMpio = ($("#tbl_municipio_id").val() || "").toString().trim();
        var zonasAll = VOTANTES._def.zonas_muestreo || [];
        var zonasMpio = zonasAll.filter(function (z) {
            return String(z.codigo_municipio || "").trim() === codMpio;
        });

        var $contSel = $("#contenedorZonaMuestreoSelect");
        var $contTxt = $("#contenedorComunaTexto");
        var $selZona = $("#tbl_zona_muestreo_id");
        var $infoCuota = $("#infoCuotaZonaMuestreo");

        if (zonasMpio.length > 0) {
            var valPrevio = $selZona.val();
            var htmlOpts = '<option value="">Seleccione comuna / zona...</option>';
            var blqCuota = VOTANTES._def.ficha && parseInt(VOTANTES._def.ficha.bloquear_cuota_completa, 10) === 1;

            zonasMpio.forEach(function (z) {
                var rest = parseInt(z.restantes || 0, 10);
                var cuota = parseInt(z.cuota || 0, 10);
                var dis = (blqCuota && cuota > 0 && rest <= 0) ? " disabled" : "";
                var txtCuota = cuota > 0 ? " (Faltan " + rest + " de " + cuota + ")" : "";
                htmlOpts += '<option value="' + z.id + '" data-nombre="' + MotorCuestionarioUI.esc(z.nombre) + '" data-restantes="' + rest + '" data-cuota="' + cuota + '"' + dis + '>' +
                    MotorCuestionarioUI.esc(z.nombre) + txtCuota +
                    '</option>';
            });
            $selZona.html(htmlOpts);
            if (valPrevio) $selZona.val(valPrevio);
            $contSel.removeClass("d-none");
            $contTxt.addClass("d-none");
            $infoCuota.text("Seleccione la zona o comuna para ver la meta disponible.");
        } else {
            $selZona.html('<option value="">Sin zonas predefinidas</option>');
            $contSel.addClass("d-none");
            $contTxt.removeClass("d-none");
            $infoCuota.text("");
        }
    },

    obtenerArrayRespuestas: function () {
        var lista = [];
        Object.keys(VOTANTES._respuestas).forEach(function (pidKey) {
            var item = VOTANTES._respuestas[pidKey];
            if (!item) return;
            var grillaArr = [];
            if (item.grilla && typeof item.grilla === "object") {
                Object.keys(item.grilla).forEach(function (fKey) {
                    var colsMap = item.grilla[fKey];
                    if (!colsMap) return;
                    Object.keys(colsMap).forEach(function (cKey) {
                        var opts = colsMap[cKey];
                        if (Array.isArray(opts) && opts.length > 0) {
                            grillaArr.push({
                                fila_id: parseInt(fKey, 10),
                                columna_id: parseInt(cKey, 10),
                                opciones: opts
                            });
                        }
                    });
                });
            }
            lista.push({
                pregunta_id: parseInt(pidKey, 10),
                opciones: Array.isArray(item.opciones) ? item.opciones : [],
                texto: item.texto || "",
                grilla: grillaArr,
                lista: item.lista || null
            });
        });
        return lista;
    },

    renderizarCuestionario: function () {
        if (!VOTANTES._def || typeof MotorCuestionario === "undefined" || typeof MotorCuestionarioUI === "undefined") {
            return;
        }
        var ctx = VOTANTES.obtenerContextoMotor();
        var evalState = MotorCuestionario.evaluar(VOTANTES._def, VOTANTES.obtenerArrayRespuestas(), ctx);
        var visibles = evalState.visibles || [];
        var preguntasById = {};
        (VOTANTES._def.preguntas || []).forEach(function (p) {
            preguntasById[parseInt(p.id, 10)] = p;
        });

        var html = MotorCuestionarioUI.renderLista(VOTANTES._def, visibles, evalState, VOTANTES._respuestas, ctx, VOTANTES._uiState);

        if (evalState.terminado) {
            html += MotorCuestionarioUI.renderCierreFiltro(
                evalState.mensaje_cierre,
                'onclick="VOTANTES.validateData();"'
            );
        }

        if (html === "") {
            html = '<div class="alert alert-secondary">Seleccione el municipio para visualizar las preguntas aplicables.</div>';
        }

        $("#preguntas_container").html(html);

        var previa = VOTANTES._siguientePrevia;
        var siguiente = evalState.siguiente_pregunta_id || null;
        VOTANTES._siguientePrevia = siguiente;
        if (VOTANTES._avanzarAlResponder && previa && siguiente !== previa) {
            var $previa = $('#preguntas_container .cq-pregunta-card[data-pregunta-id="' + previa + '"]');
            var esMultiple = $previa.find('[data-multiple="1"]').length > 0;
            var $destino = siguiente
                ? $('#preguntas_container .cq-pregunta-card[data-pregunta-id="' + siguiente + '"]')
                : $("#preguntas_container .cq-pregunta-card").last();
            if (!esMultiple && $destino.length) {
                $destino[0].scrollIntoView({ behavior: "smooth", block: "start" });
            }
        }
        VOTANTES._avanzarAlResponder = false;

        if (!VOTANTES.esModoPrueba() && !VOTANTES._desbloqueado) {
            $("#preguntas_container button, #preguntas_container textarea, #preguntas_container input").prop("disabled", true);
        }

        var totalVisibles = visibles.length;
        var completadas = 0;
        visibles.forEach(function (pid) {
            if (evalState.siguiente_pregunta_id !== pid) {
                var rItem = VOTANTES._respuestas[pid];
                if (rItem && ((rItem.opciones && rItem.opciones.length > 0) || (rItem.texto && rItem.texto.trim() !== "") || (rItem.lista && (rItem.lista.fila_id || rItem.lista.opcion_id)) || (rItem.grilla && Object.keys(rItem.grilla).length > 0))) {
                    completadas++;
                }
            }
        });
        if (evalState.terminado) {
            completadas = totalVisibles;
        }
        var pct = totalVisibles > 0 ? Math.min(100, Math.round((completadas / totalVisibles) * 100)) : 0;
        $("#veBarraProgresoMotor").css("width", pct + "%");
        $("#veTextoProgresoMotor").text(
            evalState.terminado
                ? "Finalizada por filtro (" + completadas + " de " + totalVisibles + ")"
                : (completadas + " de " + totalVisibles + " preguntas visibles (" + pct + "%)")
        );
    },

    asegurarEntradaPregunta: function (pid) {
        if (!VOTANTES._respuestas[pid]) {
            VOTANTES._respuestas[pid] = {
                pregunta_id: pid,
                opciones: [],
                texto: "",
                grilla: {},
                lista: null
            };
        }
        return VOTANTES._respuestas[pid];
    },

    vincularEventosMotor: function () {
        $(document).off(".veMotor");

        $(document).on("change.veMotor", "#tbl_municipio_id", function () {
            VOTANTES.actualizarZonasMunicipio();
            VOTANTES.guardarBorradorLocal();
            VOTANTES.renderizarCuestionario();
        });

        $(document).on("change.veMotor", "#tbl_zona_muestreo_id", function () {
            var $opt = $(this).find("option:selected");
            var nom = $opt.data("nombre") || "";
            var cuota = parseInt($opt.data("cuota") || 0, 10);
            var rest = parseInt($opt.data("restantes") || 0, 10);
            if (nom) {
                $("#comuna").val(nom);
                if (cuota > 0) {
                    $("#infoCuotaZonaMuestreo").text("Meta en " + nom + ": faltan " + rest + " de " + cuota + " encuestas.");
                } else {
                    $("#infoCuotaZonaMuestreo").text("Zona seleccionada: " + nom);
                }
            } else {
                $("#infoCuotaZonaMuestreo").text("");
            }
            VOTANTES.guardarBorradorLocal();
        });

        $(document).on("click.veMotor", ".cq-normal-opt-btn", function () {
            var $btn = $(this);
            var pid = parseInt($btn.data("pregunta"), 10);
            var oid = parseInt($btn.data("opcion"), 10);
            var esMult = String($btn.data("multiple")) === "1";
            var limite = parseInt($btn.data("limite") || 1, 10);

            var entry = VOTANTES.asegurarEntradaPregunta(pid);
            if (!esMult) {
                entry.opciones = [oid];
            } else {
                var idx = entry.opciones.indexOf(oid);
                if (idx !== -1) {
                    entry.opciones.splice(idx, 1);
                } else {
                    if (entry.opciones.length >= limite) {
                        UTIL.mostrarMensajeValidacion("Puede elegir máximo " + limite + " opciones en esta pregunta.");
                        return;
                    }
                    entry.opciones.push(oid);
                }
            }
            VOTANTES.guardarBorradorLocal();
            VOTANTES._avanzarAlResponder = true;
            VOTANTES.renderizarCuestionario();
        });

        $(document).on("click.veMotor", ".cq-lista-opt-btn", function () {
            var $btn = $(this);
            var pid = parseInt($btn.data("pregunta"), 10);
            var tipoItem = String($btn.data("tipo-item") || "");
            var idVal = parseInt($btn.data("id"), 10);

            var entry = VOTANTES.asegurarEntradaPregunta(pid);
            if (tipoItem === "fila") {
                entry.lista = { fila_id: idVal, opcion_id: null };
                entry.opciones = [];
            } else {
                entry.lista = { fila_id: null, opcion_id: idVal };
                entry.opciones = [idVal];
            }
            VOTANTES.guardarBorradorLocal();
            VOTANTES._avanzarAlResponder = true;
            VOTANTES.renderizarCuestionario();
        });

        $(document).on("click.veMotor", ".cq-grilla-opt-btn", function () {
            var $btn = $(this);
            var pid = parseInt($btn.data("pregunta"), 10);
            var fid = parseInt($btn.data("fila"), 10);
            var cid = parseInt($btn.data("columna"), 10);
            var coId = parseInt($btn.data("opcion"), 10);
            var esMult = String($btn.data("multiple")) === "1";
            var limite = parseInt($btn.data("limite") || 1, 10);

            var entry = VOTANTES.asegurarEntradaPregunta(pid);
            if (!entry.grilla[fid]) entry.grilla[fid] = {};
            if (!Array.isArray(entry.grilla[fid][cid])) entry.grilla[fid][cid] = [];

            if (!esMult) {
                entry.grilla[fid][cid] = [coId];
            } else {
                var arr = entry.grilla[fid][cid];
                var idx = arr.indexOf(coId);
                if (idx !== -1) {
                    arr.splice(idx, 1);
                } else {
                    if (arr.length >= limite) {
                        UTIL.mostrarMensajeValidacion("Puede escoger máximo " + limite + " opciones en esta columna.");
                        return;
                    }
                    arr.push(coId);
                }
            }
            VOTANTES.guardarBorradorLocal();
            VOTANTES._avanzarAlResponder = true;
            VOTANTES.renderizarCuestionario();
        });

        $(document).on("click.veMotor", ".cq-grilla-goto-fila", function () {
            var pid = parseInt($(this).data("pregunta"), 10);
            var idx = parseInt($(this).data("fila-idx"), 10);
            VOTANTES._uiState.filaActivaPorPregunta[pid] = idx;
            VOTANTES.renderizarCuestionario();
        });

        $(document).on("click.veMotor", ".cq-grilla-prev-fila", function () {
            var pid = parseInt($(this).data("pregunta"), 10);
            var cur = VOTANTES._uiState.filaActivaPorPregunta[pid] || 0;
            if (cur > 0) {
                VOTANTES._uiState.filaActivaPorPregunta[pid] = cur - 1;
                VOTANTES._uiState.animarFila = "atras";
                VOTANTES.renderizarCuestionario();
            }
        });

        $(document).on("click.veMotor", ".cq-grilla-next-fila", function () {
            var pid = parseInt($(this).data("pregunta"), 10);
            var cur = VOTANTES._uiState.filaActivaPorPregunta[pid] || 0;
            VOTANTES._uiState.filaActivaPorPregunta[pid] = cur + 1;
            VOTANTES._uiState.animarFila = "adelante";
            VOTANTES.renderizarCuestionario();
        });

        $(document).on("input.veMotor change.veMotor", ".cq-texto-input", function () {
            var pid = parseInt($(this).data("pregunta"), 10);
            var val = $(this).val() || "";
            var entry = VOTANTES.asegurarEntradaPregunta(pid);
            entry.texto = val;
            VOTANTES.guardarBorradorLocal();
        });
    },

    mostrarGuardando: function (titulo, mensaje) {
        var $overlay = $("#veSavingOverlay");
        var $card = $("#veSavingCard");
        if (!$overlay.length) {
            return;
        }
        $card.removeClass("is-success");
        $("#veSavingTitle").text(titulo || "Guardando...");
        $("#veSavingMsg").text(
            mensaje || "Por favor espera. Si el audio es largo, esto puede tardar unos segundos."
        );
        $("#veRetryAudioContainer").remove();
        $overlay.removeClass("is-hidden").attr("aria-busy", "true");
        $("body").addClass("ve-saving-lock");
        $("#btnGuardarVotante, #btnCancelarVotante").prop("disabled", true);
    },

    actualizarGuardando: function (titulo, mensaje) {
        if (titulo) {
            $("#veSavingTitle").text(titulo);
        }
        if (mensaje) {
            $("#veSavingMsg").text(mensaje);
        }
    },

    mostrarGuardadoExitoso: function (titulo, mensaje) {
        var $card = $("#veSavingCard");
        $card.addClass("is-success");
        $("#veSavingTitle").text(titulo || "¡Guardado correctamente!");
        $("#veSavingMsg").text(mensaje || "El registro y la validación se completaron.");
        $("#veRetryAudioContainer").remove();
        $("#veSavingOverlay").attr("aria-busy", "false");
    },

    mostrarReintentarAudio: function (mensajeError) {
        var $overlay = $("#veSavingOverlay");
        var $card = $("#veSavingCard");
        VOTANTES._guardando = false;
        UTIL.cursorNormal();
        if ($overlay.length && $card.length) {
            $card.removeClass("is-success");
            $("#veSavingTitle").text("Respuestas guardadas — Falta subir el audio");
            $("#veSavingMsg").text(
                mensajeError || "Tus respuestas ya quedaron guardadas. Revisa tu conexión y toca el botón para subir el audio sin volver a llenar la encuesta."
            );
            $("#veRetryAudioContainer").remove();
            var $retryBox = $(
                '<div id="veRetryAudioContainer" class="mt-3 d-flex justify-content-center gap-2">' +
                '<button type="button" id="btnReintentarSubirAudio" class="btn btn-primary fw-bold px-4 py-2">' +
                '<i class="fas fa-cloud-upload-alt me-2"></i>Reintentar subir audio' +
                '</button>' +
                '</div>'
            );
            $card.append($retryBox);
            $overlay.removeClass("is-hidden").attr("aria-busy", "false");
            $("#btnReintentarSubirAudio").off("click").on("click", function () {
                VOTANTES._guardando = true;
                $("#veRetryAudioContainer").remove();
                VOTANTES.guardarCertificacion(VOTANTES._ultimoVotanteId, VOTANTES._ultimoIntentoId);
            });
        } else {
            VOTANTES.ocultarGuardando();
            UTIL.mostrarMensajeError(mensajeError || "El registro se guardó, pero el audio no pudo subirse. Toca Guardar para reintentar subir el audio.");
        }
    },

    ocultarGuardando: function () {
        $("#veSavingOverlay").addClass("is-hidden").attr("aria-busy", "false");
        $("#veSavingCard").removeClass("is-success");
        $("#veRetryAudioContainer").remove();
        $("body").removeClass("ve-saving-lock");
        $("#btnGuardarVotante, #btnCancelarVotante").prop("disabled", false);
        VOTANTES._guardando = false;
    },

    emptyCells: function () {
        if (VOTANTES._guardando) {
            return;
        }
        if (confirm("¿Estás seguro de limpiar el formulario?")) {
            $('#formvotantes')[0].reset();
            VOTANTES._clientUuid = null;
            VOTANTES._ultimoVotanteId = 0;
            VOTANTES._ultimoIntentoId = 0;
            $('#sondeoRespuestaCandidato, #sondeoRespuestaOpcion').val('');
            $('.sondeo-opcion-btn').removeClass('active btn-primary is-selected').addClass('btn-outline-primary');
            $('.sondeo-opcion-btn .fa-check-circle').hide();
            VOTANTES.descartarBorradorLocal();
        }
    },

    seleccionarOpcionSondeo: function (btn) {
        var $btn = $(btn);
        var tipo = $btn.data('tipo');
        var valor = $btn.data('valor');

        $('.sondeo-opcion-btn').removeClass('active btn-primary is-selected').addClass('btn-outline-primary');
        $('.sondeo-opcion-btn .fa-check-circle').hide();

        $btn.removeClass('btn-outline-primary').addClass('active btn-primary is-selected');
        $btn.find('.fa-check-circle').show();

        if (tipo === 'candidato') {
            $('#sondeoRespuestaCandidato').val(valor);
            $('#sondeoRespuestaOpcion').val('');
        } else {
            $('#sondeoRespuestaOpcion').val(valor);
            $('#sondeoRespuestaCandidato').val('');
        }
    },

    validateData: function () {
        if (VOTANTES._guardando) {
            return;
        }

        if (VOTANTES._ultimoVotanteId > 0) {
            VOTANTES._guardando = true;
            VOTANTES.mostrarGuardando("Reintentando subir audio...", "Las respuestas ya están guardadas. Subiendo grabación...");
            VOTANTES.guardarCertificacion(VOTANTES._ultimoVotanteId, VOTANTES._ultimoIntentoId);
            return;
        }

        var errores = [];
        $("#nombre_completo").val('Encuestado');

        if ($("#tbl_departamento_id").val() === "" || $("#tbl_departamento_id").val() === null) {
            errores.push("Departamento");
            $("#tbl_departamento_id").addClass("is-invalid");
        } else {
            $("#tbl_departamento_id").removeClass("is-invalid");
        }

        if ($("#tbl_municipio_id").val() === "" || $("#tbl_municipio_id").val() === null) {
            errores.push("Municipio");
            $("#tbl_municipio_id").addClass("is-invalid");
        } else {
            $("#tbl_municipio_id").removeClass("is-invalid");
        }

        if (!$("#contenedorZonaMuestreoSelect").hasClass("d-none")) {
            if (!$("#tbl_zona_muestreo_id").val()) {
                errores.push("Zona / Comuna / Sector");
                $("#tbl_zona_muestreo_id").addClass("is-invalid");
            } else {
                $("#tbl_zona_muestreo_id").removeClass("is-invalid");
            }
        }

        var evalMotor = null;
        if (VOTANTES.tieneCuestionarioActivo() && VOTANTES._def && typeof MotorCuestionario !== "undefined") {
            var ctx = VOTANTES.obtenerContextoMotor();
            var valRes = MotorCuestionario.validar(VOTANTES._def, VOTANTES.obtenerArrayRespuestas(), ctx);
            evalMotor = valRes.evaluacion;
            if (!valRes.valido && valRes.errores && valRes.errores.length > 0) {
                errores.push(valRes.errores[0].mensaje);
                var firstErrPid = valRes.errores[0].pregunta_id;
                if (firstErrPid) {
                    var $cardErr = $('.cq-pregunta-card[data-pregunta-id="' + firstErrPid + '"]');
                    if ($cardErr.length) {
                        $cardErr.css("border-color", "#dc3545");
                        $('html, body').animate({ scrollTop: Math.max(0, $cardErr.offset().top - 90) }, 350);
                    }
                }
            }
        }

        var terminadoPorFiltro = evalMotor && evalMotor.terminado_por_filtro;

        if (!terminadoPorFiltro) {
            $(".ve-campo-demografico-votante:not(.d-none)").each(function () {
                var $sel = $(this).find("select[required], input[required]");
                if ($sel.length && (!$sel.val() || $sel.val() === "")) {
                    var lbl = $(this).find("label").text().replace("*", "").trim();
                    errores.push(lbl);
                    $sel.addClass("is-invalid");
                } else {
                    $sel.removeClass("is-invalid");
                }
            });
        }

        if (VOTANTES.tieneSondeoActivo() && !VOTANTES.esModoPrueba()) {
            var sondeoId = $("#sondeoActivoId").val();
            if (sondeoId) {
                var candidatoSeleccionado = $("#sondeoRespuestaCandidato").val();
                var opcionSeleccionada = $("#sondeoRespuestaOpcion").val();

                if (!candidatoSeleccionado && !opcionSeleccionada) {
                    errores.push("Debe seleccionar una opción del sondeo");
                    $("#sondeoOpcionesContainer").addClass("border border-danger rounded p-2");
                } else {
                    $("#sondeoOpcionesContainer").removeClass("border border-danger rounded p-2");
                }
            }
        }

        if (errores.length > 0) {
            UTIL.mostrarMensajeValidacion(errores[0]);
            return;
        }

        VOTANTES.savedata();
    },

    savedata: function () {
        if (VOTANTES._guardando) {
            return;
        }
        VOTANTES._guardando = true;

        var respuestasPayload = VOTANTES.tieneCuestionarioActivo() ? VOTANTES.obtenerArrayRespuestas() : [];
        var zonaId = (!$("#contenedorZonaMuestreoSelect").hasClass("d-none") && $("#tbl_zona_muestreo_id").val())
            ? $("#tbl_zona_muestreo_id").val()
            : "";
        var zonaNombre = zonaId
            ? ($("#tbl_zona_muestreo_id option:selected").data("nombre") || $("#comuna").val() || "")
            : ($("#comuna").val() || "");

        var q = {
            op: "votantessave",
            id: $("#idVotantes").val(),
            client_uuid: VOTANTES.obtenerClientUuid(),
            nombre_completo: "Encuestado",
            ideologia: $("#ideologia").val() || "",
            rango_edad: $("#rango_edad").val() || "",
            genero: $("#genero").val() || "",
            codigo_departamento: $("#tbl_departamento_id").val(),
            codigo_municipio: $("#tbl_municipio_id").val(),
            tbl_zona_muestreo_id: zonaId,
            zona_texto: zonaNombre,
            comuna: zonaNombre,
            barrio: $("#barrio").val(),
            estado: $("#estado").val() || 'activo',
            nivel_ingresos: $("#nivel_ingresos").val() || "",
            nivel_educacion: $("#nivel_educacion").val() || "",
            ocupacion: $("#ocupacion").val() || "",
            validar: 'no',
            sondeo_id: $("#sondeoActivoId").val(),
            cuestionario_id: $("#cuestionarioActivoId").val(),
            definicion_hash: $("#cuestionarioDefinicionHash").val(),
            es_prueba: VOTANTES.esModoPrueba() ? 1 : 0,
            simular_solo_validacion: VOTANTES.esModoPrueba() ? 1 : 0,
            sondeo_candidato_id: $('#sondeoRespuestaCandidato').val(),
            sondeo_opcion_id: $('#sondeoRespuestaOpcion').val(),
            cuestionario_respuestas: JSON.stringify(respuestasPayload)
        };

        if (VOTANTES.esModoPrueba()) {
            UTIL.cursorBusy();
            $.ajax({
                data: q,
                type: "POST",
                dataType: "json",
                url: "admin/ajax/rqst.php",
                timeout: 60000,
                success: function (data) {
                    UTIL.cursorNormal();
                    VOTANTES._guardando = false;
                    if (data && data.output && data.output.valid) {
                        var est = data.output.estado_intento === "terminado_filtro"
                            ? "Terminada por pregunta filtro (no cuenta para pago)"
                            : "Completa y válida";
                        UTIL.mostrarMensajeExitoso(
                            "¡Prueba exitosa! Estado: " + est + " (" + (data.output.filas_normalizadas || 0) + " respuestas validadas). No se guardaron datos reales."
                        );
                    } else {
                        var msg = (data && data.output && data.output.response && data.output.response.content)
                            ? data.output.response.content
                            : "El cuestionario reportó errores de validación.";
                        UTIL.mostrarMensajeError(msg);
                    }
                },
                error: function () {
                    UTIL.cursorNormal();
                    VOTANTES._guardando = false;
                    UTIL.mostrarMensajeError("No se pudo conectar con el servidor para validar la prueba.");
                }
            });
            return;
        }

        UTIL.cursorBusy();
        VOTANTES.mostrarGuardando(
            "Preparando registro...",
            "Finalizando audio y preparando los datos. No cierres esta pantalla."
        );

        var requiereAudio = !(VOTANTES._def && VOTANTES._def.ficha && parseInt(VOTANTES._def.ficha.audio_obligatorio, 10) === 0);
        var promGrabacion = (requiereAudio && typeof CERTIFICACION_ENCUESTADOR !== "undefined")
            ? CERTIFICACION_ENCUESTADOR.detenerGrabacion()
            : Promise.resolve();

        promGrabacion.then(function () {
            if (VOTANTES._ultimoVotanteId > 0) {
                VOTANTES.guardarCertificacion(VOTANTES._ultimoVotanteId, VOTANTES._ultimoIntentoId);
                return;
            }

            VOTANTES.actualizarGuardando(
                "Guardando encuestado...",
                "Enviando respuestas. Luego se subirá el audio y el GPS."
            );

            $.ajax({
                data: q,
                type: "POST",
                dataType: "json",
                url: "admin/ajax/rqst.php",
                timeout: 120000,
                success: function (data) {
                    if (data && data.output && data.output.valid) {
                        VOTANTES._ultimoVotanteId = parseInt(data.output.response, 10) || 0;
                        VOTANTES._ultimoIntentoId = parseInt(data.output.intento_id, 10) || 0;
                        if (requiereAudio && typeof CERTIFICACION_ENCUESTADOR !== "undefined") {
                            CERTIFICACION_ENCUESTADOR.guardarMetaIdb({
                                tbl_votante_id: VOTANTES._ultimoVotanteId,
                                intento_id: VOTANTES._ultimoIntentoId,
                                tbl_ficha_tecnica_encuesta_id: parseInt($("#cuestionarioActivoId").val(), 10) || 0,
                                origen_tipo: VOTANTES.tieneCuestionarioActivo() ? "cuestionario" : "registro_simple",
                                duracion: CERTIFICACION_ENCUESTADOR.audioDuracion || 0
                            });
                        }
                        if (typeof window.localStorage !== "undefined" && VOTANTES._def) {
                            try { window.localStorage.removeItem(VOTANTES.obtenerClaveBorrador()); } catch (e) {}
                        }
                        if (!requiereAudio) {
                            UTIL.cursorNormal();
                            VOTANTES.mostrarGuardadoExitoso("¡Guardado correctamente!", "Registro completado exitosamente.");
                            setTimeout(function () { window.location = return_page; }, 1400);
                            return;
                        }
                        VOTANTES.guardarCertificacion(VOTANTES._ultimoVotanteId, VOTANTES._ultimoIntentoId);
                    } else {
                        UTIL.cursorNormal();
                        VOTANTES.ocultarGuardando();
                        var msg = (data && data.output && data.output.response && data.output.response.content)
                            ? data.output.response.content
                            : "No se pudo guardar el registro.";
                        UTIL.mostrarMensajeError(msg);
                    }
                },
                error: function () {
                    UTIL.cursorNormal();
                    VOTANTES.ocultarGuardando();
                    UTIL.mostrarMensajeError("No se pudo guardar el registro. Revisa la conexión e inténtalo de nuevo.");
                }
            });
        }).catch(function () {
            UTIL.cursorNormal();
            VOTANTES.ocultarGuardando();
            UTIL.mostrarMensajeError("No se pudo finalizar la grabación de audio. Inténtalo de nuevo.");
        });
    },

    guardarCertificacion: function (votanteId, intentoId) {
        VOTANTES.actualizarGuardando(
            "Subiendo audio y GPS...",
            "Esto puede tardar si la grabación es larga. Mantén la pantalla abierta."
        );

        var origenTipo = 'registro_simple';
        if (VOTANTES.tieneSondeoActivo()) {
            origenTipo = 'sondeo';
        }
        if (VOTANTES.tieneCuestionarioActivo()) {
            origenTipo = 'cuestionario';
        }

        CERTIFICACION_ENCUESTADOR.enviarCertificacion(
            {
                tbl_votante_id: votanteId,
                intento_id: intentoId || VOTANTES._ultimoIntentoId || 0,
                origen_tipo: origenTipo,
                tbl_sondeo_id: $("#sondeoActivoId").val(),
                tbl_ficha_tecnica_encuesta_id: $("#cuestionarioActivoId").val()
            },
            function (parteActual, totalPartes) {
                VOTANTES.actualizarGuardando(
                    "Subiendo audio (" + parteActual + " de " + totalPartes + ")...",
                    "Subiendo grabación larga por partes seguras. No cierres esta pantalla."
                );
            }
        ).then(function (data) {
            UTIL.cursorNormal();
            if (data && data.output && data.output.valid) {
                VOTANTES.mostrarGuardadoExitoso(
                    "¡Guardado correctamente!",
                    "Guardado ✓ / Audio subido ✓ / Ubicación ✓"
                );
                if (typeof UTIL.mostrarMensajeExitoso === "function") {
                    UTIL.mostrarMensajeExitoso("Registro guardado y validado");
                }
                setTimeout(function () { window.location = return_page; }, 1600);
                return;
            }

            var mensaje = (data && data.output && data.output.response && data.output.response.content)
                ? data.output.response.content
                : "Las respuestas se guardaron, pero el audio no se pudo subir.";
            VOTANTES.mostrarReintentarAudio(mensaje);
        }).catch(function (err) {
            var msg = (err && err.message) ? err.message : "Las respuestas se guardaron, pero falló la conexión al subir el audio.";
            VOTANTES.mostrarReintentarAudio(msg);
        });
    }
};
