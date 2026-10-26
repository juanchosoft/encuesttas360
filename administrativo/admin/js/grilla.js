(function ($) {
    "use strict";

    var state = {
        plantillas: [],
        pasoActual: 1,
        modoPreview: "celular",
        previewFilaIdx: 0,
        previewRespuestas: {},
        bloqueoEstructura: false,
        editando: {
            id: 0,
            nombre: "",
            descripcion: "",
            libreto_encuestador: "",
            nota_tabulacion: "",
            tipo: "candidatos",
            origen_filas: "manual",
            presentacion: "tarjetas",
            habilitado: "si",
            filas: [],
            columnas: []
        }
    };

    var QUICK_OPTIONS = {
        sino: [
            { texto: "SÍ", cuenta_como: "si", valor_numerico: null },
            { texto: "NO", cuenta_como: "no", valor_numerico: null }
        ],
        imagen: [
            { texto: "Favorable", cuenta_como: "favorable", valor_numerico: null },
            { texto: "Desfavorable", cuenta_como: "desfavorable", valor_numerico: null },
            { texto: "No sabe / No responde", cuenta_como: "ninguno", valor_numerico: null }
        ],
        votaria: [
            { texto: "SÍ votaría", cuenta_como: "si", valor_numerico: null },
            { texto: "NO votaría", cuenta_como: "no", valor_numerico: null },
            { texto: "No sabe / No responde", cuenta_como: "ninguno", valor_numerico: null }
        ],
        ideologia: [
            { texto: "Izquierda", cuenta_como: "ninguno", valor_numerico: null },
            { texto: "Centro izquierda", cuenta_como: "ninguno", valor_numerico: null },
            { texto: "Centro", cuenta_como: "ninguno", valor_numerico: null },
            { texto: "Centro derecha", cuenta_como: "ninguno", valor_numerico: null },
            { texto: "Derecha", cuenta_como: "ninguno", valor_numerico: null },
            { texto: "No sabe / No responde", cuenta_como: "ninguno", valor_numerico: null }
        ],
        escala15: [
            { texto: "1 - Muy malo", cuenta_como: "desfavorable", valor_numerico: 1 },
            { texto: "2 - Malo", cuenta_como: "desfavorable", valor_numerico: 2 },
            { texto: "3 - Regular", cuenta_como: "ninguno", valor_numerico: 3 },
            { texto: "4 - Bueno", cuenta_como: "favorable", valor_numerico: 4 },
            { texto: "5 - Excelente", cuenta_como: "favorable", valor_numerico: 5 }
        ],
        emociones: [
            { texto: "Alegría", cuenta_como: "favorable", valor_numerico: null },
            { texto: "Esperanza", cuenta_como: "favorable", valor_numerico: null },
            { texto: "Orgullo", cuenta_como: "favorable", valor_numerico: null },
            { texto: "Tranquilidad", cuenta_como: "favorable", valor_numerico: null },
            { texto: "Miedo", cuenta_como: "desfavorable", valor_numerico: null },
            { texto: "Rabia", cuenta_como: "desfavorable", valor_numerico: null },
            { texto: "Tristeza", cuenta_como: "desfavorable", valor_numerico: null },
            { texto: "Vergüenza", cuenta_como: "desfavorable", valor_numerico: null }
        ]
    };

    var BUSINESS_PRESETS = {
        gobernacion: {
            tipo: "candidatos",
            presentacion: "tarjetas",
            libreto: "Ahora le voy a mencionar varias personalidades para que me diga si las conoce, qué imagen tiene de ellas y si votaría por alguna.",
            columnas: [
                {
                    id: 0,
                    titulo: "¿Lo conoce o ha oído hablar de él/ella?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "conocimiento",
                    condicion_activa: false,
                    condicion_columna_orden: null,
                    condicion_opcion_texto: "",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.sino))
                },
                {
                    id: 0,
                    titulo: "¿Tiene una imagen favorable o desfavorable?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "imagen",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.imagen))
                },
                {
                    id: 0,
                    titulo: "¿En qué posición política lo ubica?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "ideologia",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.ideologia))
                },
                {
                    id: 0,
                    titulo: "¿Votaría por él/ella?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "intencion",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.votaria))
                },
                {
                    id: 0,
                    titulo: "Asociación positiva (puede escoger varias)",
                    tipo_respuesta: "multiple",
                    limite_multiple: 3,
                    obligatoria: 0,
                    rol_resultado: "asociacion_positiva",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: [
                        { texto: "Honestidad", cuenta_como: "favorable", valor_numerico: null },
                        { texto: "Experiencia", cuenta_como: "favorable", valor_numerico: null },
                        { texto: "Resultados", cuenta_como: "favorable", valor_numerico: null }
                    ]
                },
                {
                    id: 0,
                    titulo: "Asociación negativa (puede escoger varias)",
                    tipo_respuesta: "multiple",
                    limite_multiple: 3,
                    obligatoria: 0,
                    rol_resultado: "asociacion_negativa",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: [
                        { texto: "Corrupción", cuenta_como: "desfavorable", valor_numerico: null },
                        { texto: "Incumplimiento", cuenta_como: "desfavorable", valor_numerico: null },
                        { texto: "Politiquería", cuenta_como: "desfavorable", valor_numerico: null }
                    ]
                }
            ]
        },
        alcaldia: {
            tipo: "candidatos",
            presentacion: "tarjetas",
            libreto: "Respecto a las personalidades del municipio que le voy a mencionar, dígame si las conoce, qué imagen tiene y si votaría por ellas.",
            columnas: [
                {
                    id: 0,
                    titulo: "¿Lo conoce o ha oído hablar de él/ella?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "conocimiento",
                    condicion_activa: false,
                    condicion_columna_orden: null,
                    condicion_opcion_texto: "",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.sino))
                },
                {
                    id: 0,
                    titulo: "¿Tiene una imagen favorable o desfavorable?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "imagen",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.imagen))
                },
                {
                    id: 0,
                    titulo: "¿Votaría por él/ella?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "intencion",
                    condicion_activa: true,
                    condicion_columna_orden: 1,
                    condicion_opcion_texto: "SÍ",
                    mensaje_bloqueo: "No lo conoce, pase al siguiente candidato",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.votaria))
                }
            ]
        },
        escala15: {
            tipo: "escala",
            presentacion: "tabla",
            libreto: "Califique de 1 a 5 cada uno de los siguientes aspectos, donde 1 es muy malo y 5 es excelente.",
            columnas: [
                {
                    id: 0,
                    titulo: "Calificación de 1 a 5",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "escala",
                    condicion_activa: false,
                    condicion_columna_orden: null,
                    condicion_opcion_texto: "",
                    mensaje_bloqueo: "",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.escala15))
                }
            ]
        },
        emociones: {
            tipo: "libre",
            presentacion: "tarjetas",
            libreto: "Mire la tarjeta de emociones y dígame qué emoción principal le produce cada uno.",
            columnas: [
                {
                    id: 0,
                    titulo: "¿Qué emoción le produce?",
                    tipo_respuesta: "unica",
                    limite_multiple: 1,
                    obligatoria: 1,
                    rol_resultado: "otro",
                    condicion_activa: false,
                    condicion_columna_orden: null,
                    condicion_opcion_texto: "",
                    mensaje_bloqueo: "",
                    opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.emociones))
                }
            ]
        }
    };

    function esc(str) {
        return String(str == null ? "" : str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function mostrarMensaje(texto, tipo) {
        var $box = $("#grMensajeGlobal");
        $box.removeClass("d-none gr-alert-success gr-alert-error");
        $box.addClass(tipo === "error" ? "gr-alert-error" : "gr-alert-success");
        $box.html((tipo === "error" ? '<i class="fas fa-exclamation-triangle me-2"></i>' : '<i class="fas fa-check-circle me-2"></i>') + esc(texto));
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    function cargarListado() {
        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            dataType: "json",
            data: { op: "grillaplantillaget" },
            success: function (res) {
                if (res && res.output && res.output.valid) {
                    state.plantillas = res.output.response || [];
                    renderTarjetasListado();
                    actualizarKpis();
                } else {
                    mostrarMensaje((res && res.output && res.output.response && res.output.response.content) || "No se pudieron cargar las tablas.", "error");
                }
            },
            error: function () {
                mostrarMensaje("Error de conexión al consultar las tablas de candidatos.", "error");
            }
        });
    }

    function actualizarKpis() {
        var total = state.plantillas.length;
        var activas = 0;
        var candidatos = 0;
        var enUso = 0;
        state.plantillas.forEach(function (p) {
            if (p.habilitado === "si") activas++;
            if (p.tipo === "candidatos") candidatos++;
            if (parseInt(p.total_preguntas_uso || 0, 10) > 0) enUso++;
        });
        $("#kpiTotalPlantillas").text(total);
        $("#kpiTotalActivas").text(activas);
        $("#kpiTotalCandidatos").text(candidatos);
        $("#kpiTotalEnUso").text(enUso);
    }

    function etiquetaTipo(tipo) {
        if (tipo === "escala") return "Calificación / Escala";
        if (tipo === "libre") return "Tabla personalizada";
        return "Tabla de candidatos";
    }

    function renderTarjetasListado() {
        var $cont = $("#grCardsContainer");
        $cont.empty();

        if (!state.plantillas.length) {
            $cont.html(
                '<div class="gr-item-card" style="grid-column: 1 / -1; text-align:center; padding:40px;">' +
                '<h3>Aún no hay tablas de candidatos creadas</h3>' +
                '<p class="text-muted">Pulse el botón "Crear nueva tabla de candidatos" para armar la primera en 4 pasos.</p>' +
                '</div>'
            );
            return;
        }

        var permisos = window.GRILLA_PERMISOS || { edit: true, create: true, delete: true };

        state.plantillas.forEach(function (p) {
            var id = parseInt(p.id, 10);
            var numFilas = parseInt(p.total_filas || 0, 10);
            var numCols = parseInt(p.total_columnas || 0, 10);
            var numPregs = parseInt(p.total_preguntas_uso || 0, 10);
            var numResps = parseInt(p.total_respuestas || 0, 10);

            var badgeEstado = p.habilitado === "si"
                ? '<span class="gr-badge gr-badge-green"><i class="fas fa-check-circle"></i> Activa</span>'
                : '<span class="gr-badge gr-badge-gray"><i class="fas fa-pause-circle"></i> Inactiva</span>';

            var badgeTipo = '<span class="gr-badge gr-badge-blue">' + esc(etiquetaTipo(p.tipo)) + '</span>';

            var textoUso = numPregs > 0
                ? '<span class="gr-badge gr-badge-amber"><i class="fas fa-link"></i> Se usa en ' + numPregs + ' pregunta(s)' + (numResps > 0 ? ' · ' + numResps + ' respuesta(s)' : '') + '</span>'
                : '<span class="gr-badge gr-badge-gray">Aún no asignada a una encuesta</span>';

            var htmlBotones = '<div class="d-flex flex-wrap gap-2 pt-2 border-top">';
            if (permisos.edit) {
                htmlBotones += '<button type="button" class="gr-btn gr-btn-outline btn-editar-plantilla" data-id="' + id + '"><i class="fas fa-edit"></i> Editar</button>';
            }
            if (permisos.create) {
                htmlBotones += '<button type="button" class="gr-btn gr-btn-outline btn-duplicar-plantilla" data-id="' + id + '" data-nombre="' + esc(p.nombre) + '"><i class="fas fa-copy"></i> Duplicar</button>';
            }
            if (permisos.delete) {
                htmlBotones += '<button type="button" class="gr-btn gr-btn-danger btn-eliminar-plantilla" data-id="' + id + '" data-nombre="' + esc(p.nombre) + '" data-pregs="' + numPregs + '" data-resps="' + numResps + '"><i class="fas fa-trash-alt"></i> Eliminar</button>';
            }
            htmlBotones += '</div>';

            var card = $(
                '<article class="gr-item-card">' +
                    '<div>' +
                        '<div class="d-flex flex-wrap gap-2 mb-2">' + badgeEstado + badgeTipo + textoUso + '</div>' +
                        '<h3>' + esc(p.nombre) + '</h3>' +
                        (p.descripcion ? '<p class="text-muted mb-2">' + esc(p.descripcion) + '</p>' : '') +
                        '<div class="mt-3 p-3 rounded" style="background:#F8FAFC;border:1px solid #E4E7EC;">' +
                            '<div><strong><i class="fas fa-users me-2 text-primary"></i>' + numFilas + '</strong> candidatos o renglones</div>' +
                            '<div class="mt-1"><strong><i class="fas fa-question-circle me-2 text-primary"></i>' + numCols + '</strong> pregunta(s) por candidato</div>' +
                        '</div>' +
                    '</div>' +
                    htmlBotones +
                '</article>'
            );
            $cont.append(card);
        });
    }

    function reiniciarEstadoEdicion() {
        state.bloqueoEstructura = false;
        state.editando = {
            id: 0,
            nombre: "",
            descripcion: "",
            libreto_encuestador: "",
            nota_tabulacion: "",
            tipo: "candidatos",
            origen_filas: "manual",
            presentacion: "tarjetas",
            habilitado: "si",
            filas: [
                { id: 0, etiqueta: "", subtitulo: "", tbl_participante_id: null, foto: "" }
            ],
            columnas: JSON.parse(JSON.stringify(BUSINESS_PRESETS.alcaldia.columnas))
        };
        $("#grPlantillaId").val("0");
        $("#grNombre").val("");
        $("#grDescripcion").val("");
        $("#grLibreto").val("");
        $("#grNotaTabulacion").val("");
        $("#grTipo").val("candidatos");
        $("#grPresentacion").val("tarjetas");
        $("#grHabilitado").val("si");
        $("#grAvisoEnUso").addClass("d-none").empty();
        $(".gr-tpl-btn").removeClass("selected");
    }

    function abrirWizardNuevo() {
        reiniciarEstadoEdicion();
        $("#grWizardTitulo").text("Crear nueva tabla de candidatos");
        $("#grWizardContainer").removeClass("d-none");
        irAPaso(1);
        window.scrollTo({ top: $("#grWizardContainer").offset().top - 20, behavior: "smooth" });
    }

    function abrirWizardEdicion(id) {
        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            dataType: "json",
            data: { op: "grillaplantilladetalle", id: id },
            success: function (res) {
                if (!res || !res.output || !res.output.valid) {
                    mostrarMensaje("No se pudo cargar la tabla seleccionada.", "error");
                    return;
                }
                var pl = res.output.response;
                var uso = pl.uso || { total_preguntas: 0, total_respuestas: 0 };
                state.bloqueoEstructura = parseInt(uso.total_respuestas || 0, 10) > 0;

                state.editando.id = parseInt(pl.id, 10);
                state.editando.nombre = pl.nombre || "";
                state.editando.descripcion = pl.descripcion || "";
                state.editando.libreto_encuestador = pl.libreto_encuestador || "";
                state.editando.nota_tabulacion = pl.nota_tabulacion || "";
                state.editando.tipo = pl.tipo || "candidatos";
                state.editando.origen_filas = pl.origen_filas || "manual";
                state.editando.presentacion = pl.presentacion || "tarjetas";
                state.editando.habilitado = pl.habilitado || "si";

                state.editando.filas = (pl.filas || []).map(function (f) {
                    return {
                        id: parseInt(f.id, 10) || 0,
                        etiqueta: f.etiqueta || "",
                        subtitulo: f.subtitulo || "",
                        tbl_participante_id: f.tbl_participante_id ? parseInt(f.tbl_participante_id, 10) : null,
                        foto: f.foto || ""
                    };
                });

                var idColToOrden = {};
                var idOptToTexto = {};
                (pl.columnas || []).forEach(function (c, idx) {
                    idColToOrden[parseInt(c.id, 10)] = idx + 1;
                    (c.opciones || []).forEach(function (op) {
                        idOptToTexto[parseInt(op.id, 10)] = op.texto || "";
                    });
                });

                state.editando.columnas = (pl.columnas || []).map(function (c) {
                    var condColId = c.condicion_columna_id ? parseInt(c.condicion_columna_id, 10) : null;
                    var condOptId = c.condicion_opcion_id ? parseInt(c.condicion_opcion_id, 10) : null;
                    return {
                        id: parseInt(c.id, 10) || 0,
                        titulo: c.titulo || "",
                        libreto_encuestador: c.libreto_encuestador || "",
                        nota_tabulacion: c.nota_tabulacion || "",
                        tipo_respuesta: c.tipo_respuesta || "unica",
                        limite_multiple: parseInt(c.limite_multiple || 1, 10),
                        obligatoria: parseInt(c.obligatoria != null ? c.obligatoria : 1, 10),
                        rol_resultado: c.rol_resultado || "ninguno",
                        condicion_activa: !!(condColId && condOptId),
                        condicion_columna_id: condColId,
                        condicion_columna_orden: condColId && idColToOrden[condColId] ? idColToOrden[condColId] : null,
                        condicion_opcion_id: condOptId,
                        condicion_opcion_texto: condOptId && idOptToTexto[condOptId] ? idOptToTexto[condOptId] : "",
                        mensaje_bloqueo: c.mensaje_bloqueo || "Esta pregunta no aplica. Pase al siguiente candidato.",
                        opciones: (c.opciones || []).map(function (op) {
                            return {
                                id: parseInt(op.id, 10) || 0,
                                texto: op.texto || "",
                                valor_numerico: op.valor_numerico != null ? op.valor_numerico : null,
                                cuenta_como: op.cuenta_como || "ninguno"
                            };
                        })
                    };
                });

                $("#grPlantillaId").val(state.editando.id);
                $("#grNombre").val(state.editando.nombre);
                $("#grDescripcion").val(state.editando.descripcion);
                $("#grLibreto").val(state.editando.libreto_encuestador);
                $("#grNotaTabulacion").val(state.editando.nota_tabulacion);
                $("#grTipo").val(state.editando.tipo);
                $("#grPresentacion").val(state.editando.presentacion);
                $("#grHabilitado").val(state.editando.habilitado);

                if (state.bloqueoEstructura) {
                    $("#grAvisoEnUso")
                        .removeClass("d-none")
                        .html('<i class="fas fa-info-circle me-2"></i>Esta tabla ya tiene <strong>' + uso.total_respuestas + ' respuesta(s)</strong> registrada(s). Puede corregir nombres o textos y agregar nuevos elementos, pero no quitar los existentes para conservar las respuestas.');
                } else {
                    $("#grAvisoEnUso").addClass("d-none").empty();
                }

                $("#grWizardTitulo").text("Editar tabla: " + state.editando.nombre);
                $("#grWizardContainer").removeClass("d-none");
                irAPaso(1);
                window.scrollTo({ top: $("#grWizardContainer").offset().top - 20, behavior: "smooth" });
            }
        });
    }

    function sincronizarPaso1DesdeDOM() {
        state.editando.nombre = $.trim($("#grNombre").val());
        state.editando.descripcion = $.trim($("#grDescripcion").val());
        state.editando.libreto_encuestador = $.trim($("#grLibreto").val());
        state.editando.nota_tabulacion = $.trim($("#grNotaTabulacion").val());
        state.editando.tipo = $("#grTipo").val() || "candidatos";
        state.editando.presentacion = $("#grPresentacion").val() || "tarjetas";
        state.editando.habilitado = $("#grHabilitado").val() || "si";
    }

    function validarPaso(paso) {
        if (paso === 1) {
            sincronizarPaso1DesdeDOM();
            if (!state.editando.nombre) {
                mostrarMensaje("Por favor escriba un nombre para la tabla antes de continuar.", "error");
                $("#grNombre").trigger("focus");
                return false;
            }
            return true;
        }
        if (paso === 2) {
            sincronizarFilasDesdeDOM();
            var conTexto = state.editando.filas.filter(function (f) {
                return $.trim(f.etiqueta) !== "";
            });
            if (!conTexto.length) {
                mostrarMensaje("Agregue al menos un candidato o renglón en la lista para continuar.", "error");
                return false;
            }
            return true;
        }
        if (paso === 3) {
            sincronizarColumnasDesdeDOM();
            if (!state.editando.columnas.length) {
                mostrarMensaje("Debe incluir al menos una pregunta por candidato.", "error");
                return false;
            }
            for (var i = 0; i < state.editando.columnas.length; i++) {
                var col = state.editando.columnas[i];
                if (!$.trim(col.titulo)) {
                    mostrarMensaje("La pregunta #" + (i + 1) + " no tiene enunciado. Escriba qué desea preguntar.", "error");
                    return false;
                }
                if (!col.opciones || !col.opciones.length) {
                    mostrarMensaje("La pregunta «" + col.titulo + "» debe tener al menos una opción de respuesta.", "error");
                    return false;
                }
                if (col.condicion_activa && col.condicion_columna_orden) {
                    var destino = state.editando.columnas[col.condicion_columna_orden - 1];
                    var existeResp = destino && (destino.opciones || []).some(function (o) {
                        return String(o.texto || "").toLowerCase() === String(col.condicion_opcion_texto || "").toLowerCase();
                    });
                    if (!existeResp) {
                        mostrarMensaje("La pregunta «" + col.titulo + "» solo se hace si responden «" + col.condicion_opcion_texto + "», pero esa respuesta ya no existe en la pregunta de la que depende. Elija de nuevo cuándo se hace.", "error");
                        return false;
                    }
                }
            }
            return true;
        }
        return true;
    }

    function irAPaso(paso) {
        state.pasoActual = paso;
        $("#grPasoIndicadorTexto").text("Paso " + paso + " de 4");

        $("#grStepsNav .gr-step-pill").each(function () {
            var s = parseInt($(this).attr("data-step"), 10);
            $(this).removeClass("active completed");
            if (s === paso) {
                $(this).addClass("active");
            } else if (s < paso) {
                $(this).addClass("completed");
            }
        });

        $(".gr-step-pane").addClass("d-none");
        $("#grStep" + paso).removeClass("d-none");

        if (paso === 2) {
            renderFilasEditor();
        } else if (paso === 3) {
            renderColumnasEditor();
        } else if (paso === 4) {
            state.previewFilaIdx = 0;
            state.previewRespuestas = {};
            renderVistaPrevia();
        }

        if (paso === 1) {
            $("#btnPasoAnterior").addClass("d-none");
        } else {
            $("#btnPasoAnterior").removeClass("d-none");
        }

        if (paso === 4) {
            $("#btnPasoSiguiente").addClass("d-none");
            $("#btnGuardarPlantilla").removeClass("d-none");
        } else {
            $("#btnPasoSiguiente").removeClass("d-none");
            $("#btnGuardarPlantilla").addClass("d-none");
        }
    }

    function renderFilasEditor() {
        var $cont = $("#grFilasContainer");
        $cont.empty();

        if (!state.editando.filas.length) {
            state.editando.filas.push({ id: 0, etiqueta: "", subtitulo: "", tbl_participante_id: null, foto: "" });
        }

        $("#grContadorFilas").text(state.editando.filas.length);

        state.editando.filas.forEach(function (f, idx) {
            var disableDel = state.bloqueoEstructura && f.id > 0 ? " disabled title='No se puede quitar porque ya tiene respuestas'" : "";
            var row = $(
                '<div class="gr-fila-row" data-idx="' + idx + '">' +
                    '<div class="d-flex flex-wrap align-items-center gap-2">' +
                        '<span class="gr-badge gr-badge-blue">#' + (idx + 1) + '</span>' +
                        '<div style="flex:1; min-width:220px;">' +
                            '<input type="text" class="gr-input inp-fila-etiqueta" value="' + esc(f.etiqueta) + '" placeholder="Nombre del candidato o elemento">' +
                        '</div>' +
                        '<div style="width:200px;">' +
                            '<input type="text" class="gr-input inp-fila-subtitulo" value="' + esc(f.subtitulo || "") + '" placeholder="Partido o nota (opcional)">' +
                        '</div>' +
                        '<div class="d-flex gap-1">' +
                            '<button type="button" class="gr-btn gr-btn-outline btn-subir-fila" data-idx="' + idx + '" title="Subir"' + (idx === 0 ? " disabled" : "") + '><i class="fas fa-arrow-up"></i></button>' +
                            '<button type="button" class="gr-btn gr-btn-outline btn-bajar-fila" data-idx="' + idx + '" title="Bajar"' + (idx === state.editando.filas.length - 1 ? " disabled" : "") + '><i class="fas fa-arrow-down"></i></button>' +
                            '<button type="button" class="gr-btn gr-btn-danger btn-quitar-fila" data-idx="' + idx + '" title="Quitar"' + disableDel + '><i class="fas fa-times"></i></button>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            $cont.append(row);
        });
    }

    function sincronizarFilasDesdeDOM() {
        var nuevas = [];
        $("#grFilasContainer .gr-fila-row").each(function () {
            var idx = parseInt($(this).attr("data-idx"), 10);
            var orig = state.editando.filas[idx] || { id: 0, tbl_participante_id: null, foto: "" };
            nuevas.push({
                id: orig.id || 0,
                etiqueta: $.trim($(this).find(".inp-fila-etiqueta").val()),
                subtitulo: $.trim($(this).find(".inp-fila-subtitulo").val()),
                tbl_participante_id: orig.tbl_participante_id || null,
                foto: orig.foto || ""
            });
        });
        if (nuevas.length) {
            state.editando.filas = nuevas;
        }
    }

    function construirMetaOpciones(col) {
        var opciones = col.opciones || [];
        if (!opciones.length) {
            return "";
        }
        var tipos = [
            ["ninguno", "Normal"],
            ["si", "Sí / Positivo"],
            ["no", "No / Negativo"],
            ["favorable", "Imagen favorable"],
            ["desfavorable", "Imagen desfavorable"]
        ];
        var html = '<div class="gr-opt-meta mt-2"><div class="small text-muted fw-bold mb-1">En los resultados, cada respuesta cuenta como:</div>';
        opciones.forEach(function (o) {
            var cuenta = o.cuenta_como || "ninguno";
            html += '<div class="row g-2 align-items-center mb-1 gr-opt-meta-row" data-texto="' + esc(String(o.texto || "").toLowerCase()) + '">' +
                '<div class="col-5 text-truncate">' + esc(o.texto) + '</div>' +
                '<div class="col-4"><select class="gr-select sel-opt-cuenta">' +
                tipos.map(function (t) { return '<option value="' + t[0] + '"' + (cuenta === t[0] ? " selected" : "") + '>' + t[1] + '</option>'; }).join("") +
                '</select></div>' +
                '<div class="col-3"><input type="number" step="0.01" class="gr-input inp-opt-valor" placeholder="Nota" title="Valor numérico opcional (por ejemplo, la nota de 1 a 5)" value="' + (o.valor_numerico != null && o.valor_numerico !== "" ? esc(String(o.valor_numerico)) : "") + '"></div>' +
                '</div>';
        });
        return html + '</div>';
    }

    function renderColumnasEditor() {
        var $cont = $("#grColumnasContainer");
        $cont.empty();

        state.editando.columnas.forEach(function (col, idx) {
            var numCol = idx + 1;
            var textoOpciones = (col.opciones || []).map(function (o) {
                return o.texto;
            }).join("\n");

            var opcionesAnterioresHtml = '<option value="">Seleccione pregunta anterior...</option>';
            for (var pIdx = 0; pIdx < idx; pIdx++) {
                var prevCol = state.editando.columnas[pIdx];
                var sel = parseInt(col.condicion_columna_orden || 0, 10) === (pIdx + 1) ? " selected" : "";
                opcionesAnterioresHtml += '<option value="' + (pIdx + 1) + '"' + sel + '>Pregunta ' + (pIdx + 1) + ': ' + esc(prevCol.titulo || "Sin título") + '</option>';
            }

            var valoresCondicionHtml = '<option value="">Seleccione respuesta...</option>';
            var targetOrden = parseInt(col.condicion_columna_orden || 0, 10);
            if (targetOrden > 0 && state.editando.columnas[targetOrden - 1]) {
                var targetCol = state.editando.columnas[targetOrden - 1];
                (targetCol.opciones || []).forEach(function (op) {
                    var selOp = (col.condicion_opcion_texto || "").toLowerCase() === (op.texto || "").toLowerCase() ? " selected" : "";
                    valoresCondicionHtml += '<option value="' + esc(op.texto) + '"' + selOp + '>' + esc(op.texto) + '</option>';
                });
            }

            var disableDelCol = state.bloqueoEstructura && col.id > 0 ? " disabled title='Ya tiene respuestas registradas'" : "";

            var card = $(
                '<div class="gr-col-card" data-idx="' + idx + '">' +
                    '<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 pb-2 border-bottom">' +
                        '<div class="d-flex align-items-center gap-2">' +
                            '<span class="gr-badge gr-badge-blue">Pregunta por candidato ' + numCol + '</span>' +
                            '<strong>' + esc(col.titulo || "Nueva pregunta") + '</strong>' +
                        '</div>' +
                        '<button type="button" class="gr-btn gr-btn-danger btn-quitar-columna" data-idx="' + idx + '"' + disableDelCol + '><i class="fas fa-trash-alt"></i> Quitar pregunta</button>' +
                    '</div>' +
                    '<div class="row g-3">' +
                        '<div class="col-12 col-lg-7">' +
                            '<label class="gr-label">¿Qué se le pregunta al encuestado? <span class="text-danger">*</span></label>' +
                            '<input type="text" class="gr-input inp-col-titulo" value="' + esc(col.titulo) + '" placeholder="Ej: ¿Lo conoce o ha oído hablar de él/ella?">' +
                        '</div>' +
                        '<div class="col-12 col-md-6 col-lg-3">' +
                            '<label class="gr-label">Tipo de respuesta</label>' +
                            '<select class="gr-select sel-col-tipo">' +
                                '<option value="unica"' + (col.tipo_respuesta !== "multiple" ? " selected" : "") + '>Escoge una sola opción</option>' +
                                '<option value="multiple"' + (col.tipo_respuesta === "multiple" ? " selected" : "") + '>Puede escoger varias</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-12 col-md-6 col-lg-2">' +
                            '<label class="gr-label">Uso en resultados</label>' +
                            '<select class="gr-select sel-col-rol">' +
                                '<option value="conocimiento"' + (col.rol_resultado === "conocimiento" ? " selected" : "") + '>Conocimiento (Sí/No)</option>' +
                                '<option value="imagen"' + (col.rol_resultado === "imagen" ? " selected" : "") + '>Imagen (Favorable/Desfav.)</option>' +
                                '<option value="intencion"' + (col.rol_resultado === "intencion" ? " selected" : "") + '>Intención de voto</option>' +
                                '<option value="ideologia"' + (col.rol_resultado === "ideologia" ? " selected" : "") + '>Ideología / Ubicación</option>' +
                                '<option value="asociacion_positiva"' + (col.rol_resultado === "asociacion_positiva" ? " selected" : "") + '>Asociación positiva</option>' +
                                '<option value="asociacion_negativa"' + (col.rol_resultado === "asociacion_negativa" ? " selected" : "") + '>Asociación negativa</option>' +
                                '<option value="escala"' + (col.rol_resultado === "escala" ? " selected" : "") + '>Escala / Promedio</option>' +
                                '<option value="otro"' + (col.rol_resultado === "otro" ? " selected" : "") + '>Otro indicador</option>' +
                                '<option value="ninguno"' + (col.rol_resultado === "ninguno" ? " selected" : "") + '>Ninguno especial</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-12 col-lg-6">' +
                            '<div class="d-flex flex-wrap justify-content-between align-items-center mb-1">' +
                                '<label class="gr-label mb-0">Opciones de respuesta (una por renglón) <span class="text-danger">*</span></label>' +
                                '<div class="d-flex flex-wrap gap-1">' +
                                    '<button type="button" class="badge bg-light text-dark border btn-plantilla-opciones" data-idx="' + idx + '" data-tpl="sino">Sí / No</button>' +
                                    '<button type="button" class="badge bg-light text-dark border btn-plantilla-opciones" data-idx="' + idx + '" data-tpl="imagen">Favorable / Desfavorable</button>' +
                                    '<button type="button" class="badge bg-light text-dark border btn-plantilla-opciones" data-idx="' + idx + '" data-tpl="votaria">Sí / No votaría</button>' +
                                    '<button type="button" class="badge bg-light text-dark border btn-plantilla-opciones" data-idx="' + idx + '" data-tpl="ideologia">Izquierda / Derecha</button>' +
                                    '<button type="button" class="badge bg-light text-dark border btn-plantilla-opciones" data-idx="' + idx + '" data-tpl="escala15">Escala 1 a 5</button>' +
                                '</div>' +
                            '</div>' +
                            '<textarea class="gr-textarea inp-col-opciones" rows="4" placeholder="Escriba una opción por renglón">' + esc(textoOpciones) + '</textarea>' +
                            construirMetaOpciones(col) +
                        '</div>' +
                        '<div class="col-12 col-lg-6">' +
                            '<label class="gr-label">Lo que lee el encuestador (opcional)</label>' +
                            '<textarea class="gr-textarea inp-col-libreto" rows="2">' + esc(col.libreto_encuestador || "") + '</textarea>' +
                            '<label class="gr-label mt-2">Nota para la tabulación (opcional)</label>' +
                            '<input type="text" class="gr-input inp-col-nota" value="' + esc(col.nota_tabulacion || "") + '">' +
                            '<div class="d-flex flex-wrap align-items-center gap-3 mt-3">' +
                                '<label class="d-flex align-items-center gap-2 mb-0"><input type="checkbox" class="chk-col-oblig" style="width:1.4em;height:1.4em;"' + (col.obligatoria == 0 ? "" : " checked") + '> Es obligatoria</label>' +
                                '<label class="d-flex align-items-center gap-2 mb-0 box-col-limite ' + (col.tipo_respuesta === "multiple" ? "" : "d-none") + '">Máximo de opciones a escoger: <input type="number" min="1" class="gr-input inp-col-limite" style="width:90px;" value="' + (parseInt(col.limite_multiple, 10) > 1 ? parseInt(col.limite_multiple, 10) : 3) + '"></label>' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-12 col-lg-6">' +
                            '<label class="gr-label">¿Cuándo se hace esta pregunta?</label>' +
                            (idx === 0
                                ? '<div class="p-3 rounded bg-white border text-muted">Esta es la primera pregunta por candidato y <strong>siempre se muestra</strong>.</div>'
                                : '<select class="gr-select sel-col-cond-modo mb-2">' +
                                      '<option value="siempre"' + (!col.condicion_activa ? " selected" : "") + '>Siempre para todos los candidatos</option>' +
                                      '<option value="condicional"' + (col.condicion_activa ? " selected" : "") + '>Solo si en una pregunta anterior respondió algo específico</option>' +
                                  '</select>' +
                                  '<div class="box-condicion-detalle ' + (col.condicion_activa ? "" : "d-none") + ' p-3 rounded bg-white border">' +
                                      '<div class="row g-2">' +
                                          '<div class="col-12 col-md-6">' +
                                              '<label class="small fw-bold">Solo si en:</label>' +
                                              '<select class="gr-select sel-col-cond-col" data-idx="' + idx + '">' + opcionesAnterioresHtml + '</select>' +
                                          '</div>' +
                                          '<div class="col-12 col-md-6">' +
                                              '<label class="small fw-bold">Respondió:</label>' +
                                              '<select class="gr-select sel-col-cond-opt">' + valoresCondicionHtml + '</select>' +
                                          '</div>' +
                                          '<div class="col-12 mt-2">' +
                                              '<label class="small fw-bold">Si NO cumple, mostrar este aviso al encuestador:</label>' +
                                              '<input type="text" class="gr-input inp-col-msg-bloqueo" value="' + esc(col.mensaje_bloqueo || "Esta pregunta no aplica. Pase al siguiente candidato.") + '">' +
                                          '</div>' +
                                      '</div>' +
                                  '</div>'
                            ) +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            $cont.append(card);
        });
    }

    function sincronizarColumnasDesdeDOM() {
        var nuevas = [];
        $("#grColumnasContainer .gr-col-card").each(function () {
            var idx = parseInt($(this).attr("data-idx"), 10);
            var orig = state.editando.columnas[idx] || { id: 0, opciones: [] };
            var titulo = $.trim($(this).find(".inp-col-titulo").val());
            var tipoResp = $(this).find(".sel-col-tipo").val() || "unica";
            var rol = $(this).find(".sel-col-rol").val() || "ninguno";
            var rawOpciones = $(this).find(".inp-col-opciones").val() || "";
            var lineas = rawOpciones.split(/\r\n|\r|\n/).map($.trim).filter(Boolean);

            var lineasMin = lineas.map(function (l) { return l.toLowerCase(); });
            var previas = orig.opciones || [];
            var previaPorTexto = {};
            previas.forEach(function (po) {
                previaPorTexto[String(po.texto || "").toLowerCase()] = po;
            });
            var metaDom = {};
            $(this).find(".gr-opt-meta-row").each(function () {
                var valRaw = $.trim($(this).find(".inp-opt-valor").val());
                metaDom[$(this).attr("data-texto")] = {
                    cuenta_como: $(this).find(".sel-opt-cuenta").val() || "ninguno",
                    valor_numerico: valRaw !== "" ? parseFloat(valRaw) : null
                };
            });
            var opcionesNuevas = lineas.map(function (txt, oIdx) {
                var prevOpt = previaPorTexto[txt.toLowerCase()] || null;
                if (!prevOpt && previas[oIdx] && lineasMin.indexOf(String(previas[oIdx].texto || "").toLowerCase()) === -1) {
                    prevOpt = previas[oIdx];
                }
                var meta = metaDom[txt.toLowerCase()] || prevOpt || {};
                return {
                    id: prevOpt && prevOpt.id ? prevOpt.id : 0,
                    texto: txt,
                    cuenta_como: meta.cuenta_como || "ninguno",
                    valor_numerico: meta.valor_numerico != null ? meta.valor_numerico : null
                };
            });

            var condModo = $(this).find(".sel-col-cond-modo").val();
            var condActiva = idx > 0 && condModo === "condicional";
            var condColOrden = condActiva ? parseInt($(this).find(".sel-col-cond-col").val() || "0", 10) : null;
            var condOptTexto = condActiva ? $.trim($(this).find(".sel-col-cond-opt").val() || "") : "";
            var msgBloqueo = $.trim($(this).find(".inp-col-msg-bloqueo").val()) || "No hay más preguntas para esta fila. Pase a la siguiente.";

            nuevas.push({
                id: orig.id || 0,
                titulo: titulo,
                libreto_encuestador: $.trim($(this).find(".inp-col-libreto").val()),
                nota_tabulacion: $.trim($(this).find(".inp-col-nota").val()),
                tipo_respuesta: tipoResp,
                limite_multiple: tipoResp === "multiple" ? Math.max(1, parseInt($(this).find(".inp-col-limite").val(), 10) || 3) : 1,
                obligatoria: $(this).find(".chk-col-oblig").is(":checked") ? 1 : 0,
                rol_resultado: rol,
                condicion_activa: condActiva && condColOrden > 0 && condOptTexto !== "",
                condicion_columna_orden: condActiva && condColOrden > 0 ? condColOrden : null,
                condicion_opcion_texto: condActiva ? condOptTexto : "",
                mensaje_bloqueo: msgBloqueo,
                opciones: opcionesNuevas
            });
        });

        state.editando.columnas = nuevas;
    }

    function columnaHabilitadaEnPreview(filaIdx, colIdx) {
        var col = state.editando.columnas[colIdx];
        if (!col || !col.condicion_activa || !col.condicion_columna_orden) {
            return true;
        }
        var targetIdx = col.condicion_columna_orden - 1;
        var respFila = state.previewRespuestas[filaIdx] || {};
        var valorRespondido = respFila[targetIdx] || "";
        return String(valorRespondido).toLowerCase() === String(col.condicion_opcion_texto || "").toLowerCase();
    }

    function renderVistaPrevia() {
        var $area = $("#grPreviewArea");
        $area.empty();

        var filas = state.editando.filas.filter(function (f) {
            return $.trim(f.etiqueta) !== "";
        });
        var cols = state.editando.columnas;

        if (!filas.length || !cols.length) {
            $area.html('<div class="text-muted">Complete los candidatos y preguntas en los pasos anteriores para ver la vista previa.</div>');
            return;
        }

        var libretoHtml = state.editando.libreto_encuestador
            ? '<div class="p-3 mb-3 rounded" style="background:#FFFBEB;border:2px solid #F59E0B;color:#92400E;">' +
              '<strong><i class="fas fa-bullhorn me-2"></i>Lea al encuestado:</strong> ' + esc(state.editando.libreto_encuestador) +
              '</div>'
            : '';

        if (state.modoPreview === "celular") {
            if (state.previewFilaIdx >= filas.length) {
                state.previewFilaIdx = 0;
            }
            var fActual = filas[state.previewFilaIdx];
            var respFila = state.previewRespuestas[state.previewFilaIdx] || {};

            var htmlPreguntas = "";
            var bloqueadaPorCondicion = false;
            var mensajeBloqueo = "";

            cols.forEach(function (col, cIdx) {
                if (!columnaHabilitadaEnPreview(state.previewFilaIdx, cIdx)) {
                    if (respFila[0] !== undefined && !bloqueadaPorCondicion) {
                        bloqueadaPorCondicion = true;
                        mensajeBloqueo = col.mensaje_bloqueo || "No hay más preguntas para esta fila. Pase a la siguiente.";
                    }
                    return;
                }

                var valActual = respFila[cIdx] || "";
                htmlPreguntas += '<div class="mb-3 pt-2 border-top">';
                htmlPreguntas += '<div class="fw-bold mb-2" style="font-size:18px;">' + esc(col.titulo) + '</div>';
                (col.opciones || []).forEach(function (op) {
                    var isAct = String(valActual).toLowerCase() === String(op.texto).toLowerCase();
                    htmlPreguntas +=
                        '<button type="button" class="gr-choice-btn btn-preview-choice' + (isAct ? ' active' : '') + '" data-fila="' + state.previewFilaIdx + '" data-col="' + cIdx + '" data-val="' + esc(op.texto) + '">' +
                            '<span>' + esc(op.texto) + '</span>' +
                            '<i class="fas ' + (isAct ? 'fa-check-circle text-primary' : 'fa-circle text-muted') + '"></i>' +
                        '</button>';
                });
                htmlPreguntas += '</div>';
            });

            if (bloqueadaPorCondicion) {
                htmlPreguntas +=
                    '<div class="p-3 rounded mb-3" style="background:#F2F4F7;border:2px solid #D0D5DD;color:#344054;font-weight:700;">' +
                        '<i class="fas fa-forward me-2"></i>' + esc(mensajeBloqueo) +
                    '</div>';
            }

            var navCandidatos =
                '<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">' +
                    '<button type="button" class="gr-btn gr-btn-outline" id="btnPrevCandidatoPreview"' + (state.previewFilaIdx === 0 ? ' disabled' : '') + '>Anterior</button>' +
                    '<span class="fw-bold">Candidato ' + (state.previewFilaIdx + 1) + ' de ' + filas.length + '</span>' +
                    '<button type="button" class="gr-btn gr-btn-primary" id="btnNextCandidatoPreview"' + (state.previewFilaIdx >= filas.length - 1 ? ' disabled' : '') + '>Siguiente</button>' +
                '</div>';

            $area.html(
                libretoHtml +
                '<div class="gr-preview-mobile">' +
                    '<div class="gr-badge gr-badge-blue mb-2">Candidato ' + (state.previewFilaIdx + 1) + ' de ' + filas.length + '</div>' +
                    '<h4 style="font-size:24px;font-weight:800;margin-bottom:4px;">' + esc(fActual.etiqueta) + '</h4>' +
                    (fActual.subtitulo ? '<div class="text-muted mb-3">' + esc(fActual.subtitulo) + '</div>' : '<div class="mb-3"></div>') +
                    htmlPreguntas +
                    navCandidatos +
                '</div>'
            );
        } else {
            var ths = '<th>Candidato / Renglón</th>';
            cols.forEach(function (c) {
                ths += '<th>' + esc(c.titulo) + '</th>';
            });

            var trs = '';
            filas.forEach(function (f, fIdx) {
                var respFila = state.previewRespuestas[fIdx] || {};
                trs += '<tr>';
                trs += '<td class="fw-bold">' + esc(f.etiqueta) + (f.subtitulo ? '<div class="small text-muted">' + esc(f.subtitulo) + '</div>' : '') + '</td>';

                cols.forEach(function (c, cIdx) {
                    if (!columnaHabilitadaEnPreview(fIdx, cIdx)) {
                        trs += '<td class="text-muted bg-light"><em>' + esc(c.mensaje_bloqueo || "Bloqueado") + '</em></td>';
                    } else {
                        var valActual = respFila[cIdx] || "";
                        var optsHtml = '<select class="gr-select sel-preview-table" data-fila="' + fIdx + '" data-col="' + cIdx + '"><option value="">Seleccione...</option>';
                        (c.opciones || []).forEach(function (op) {
                            var sel = String(valActual).toLowerCase() === String(op.texto).toLowerCase() ? " selected" : "";
                            optsHtml += '<option value="' + esc(op.texto) + '"' + sel + '>' + esc(op.texto) + '</option>';
                        });
                        optsHtml += '</select>';
                        trs += '<td>' + optsHtml + '</td>';
                    }
                });
                trs += '</tr>';
            });

            $area.html(
                libretoHtml +
                '<div class="table-responsive bg-white p-3 rounded border">' +
                    '<table class="table table-bordered align-middle mb-0">' +
                        '<thead class="table-light"><tr>' + ths + '</tr></thead>' +
                        '<tbody>' + trs + '</tbody>' +
                    '</table>' +
                '</div>'
            );
        }
    }

    function guardarPlantillaCompleta() {
        sincronizarPaso1DesdeDOM();

        var payload = {
            op: "grillaplantillasave",
            id: state.editando.id,
            nombre: state.editando.nombre,
            descripcion: state.editando.descripcion,
            libreto_encuestador: state.editando.libreto_encuestador,
            nota_tabulacion: state.editando.nota_tabulacion,
            tipo: state.editando.tipo,
            origen_filas: state.editando.origen_filas,
            presentacion: state.editando.presentacion,
            habilitado: state.editando.habilitado,
            filas: JSON.stringify(state.editando.filas),
            columnas: JSON.stringify(state.editando.columnas)
        };

        var $btn = $("#btnGuardarPlantilla");
        $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            dataType: "json",
            data: payload,
            success: function (res) {
                $btn.prop("disabled", false).html('<i class="fas fa-check-circle"></i> Guardar tabla de candidatos');
                if (res && res.output && res.output.valid) {
                    $("#grWizardContainer").addClass("d-none");
                    mostrarMensaje("Guardado ✓ La tabla «" + state.editando.nombre + "» quedó lista para usarse en sus cuestionarios.", "success");
                    cargarListado();
                } else {
                    var msg = (res && res.output && res.output.response && res.output.response.content) || "No se pudo guardar la tabla.";
                    mostrarMensaje(msg, "error");
                }
            },
            error: function () {
                $btn.prop("disabled", false).html('<i class="fas fa-check-circle"></i> Guardar tabla de candidatos');
                mostrarMensaje("Error de red al guardar la tabla.", "error");
            }
        });
    }

    function init() {
        cargarListado();

        $("#btnNuevaTabla").on("click", function () {
            abrirWizardNuevo();
        });

        $("#btnCancelarWizard").on("click", function () {
            $("#grWizardContainer").addClass("d-none");
        });

        $(".gr-tpl-btn").on("click", function () {
            var presetKey = $(this).attr("data-preset");
            var preset = BUSINESS_PRESETS[presetKey];
            if (!preset) return;

            $(".gr-tpl-btn").removeClass("selected");
            $(this).addClass("selected");

            $("#grTipo").val(preset.tipo);
            $("#grPresentacion").val(preset.presentacion);
            if (!$("#grLibreto").val()) {
                $("#grLibreto").val(preset.libreto);
            }
            state.editando.columnas = JSON.parse(JSON.stringify(preset.columnas));
        });

        $("#btnPasoSiguiente").on("click", function () {
            if (!validarPaso(state.pasoActual)) return;
            irAPaso(state.pasoActual + 1);
        });

        $("#btnPasoAnterior").on("click", function () {
            if (state.pasoActual === 2) sincronizarFilasDesdeDOM();
            if (state.pasoActual === 3) sincronizarColumnasDesdeDOM();
            if (state.pasoActual > 1) {
                irAPaso(state.pasoActual - 1);
            }
        });

        $("#grStepsNav").on("click", ".gr-step-pill", function () {
            var destino = parseInt($(this).attr("data-step"), 10);
            if (destino === state.pasoActual) return;
            if (state.pasoActual === 2) sincronizarFilasDesdeDOM();
            if (state.pasoActual === 3) sincronizarColumnasDesdeDOM();
            if (destino > state.pasoActual && !validarPaso(state.pasoActual)) return;
            irAPaso(destino);
        });

        $("#btnAgregarFilaIndividual").on("click", function () {
            sincronizarFilasDesdeDOM();
            state.editando.filas.push({ id: 0, etiqueta: "", subtitulo: "", tbl_participante_id: null, foto: "" });
            renderFilasEditor();
        });

        $("#btnAgregarPegados").on("click", function () {
            sincronizarFilasDesdeDOM();
            var raw = $("#grPegarFilas").val() || "";
            var lineas = raw.split(/\r\n|\r|\n/).map($.trim).filter(Boolean);
            if (!lineas.length) return;

            state.editando.filas = state.editando.filas.filter(function (f) {
                return $.trim(f.etiqueta) !== "";
            });

            lineas.forEach(function (nombre) {
                state.editando.filas.push({
                    id: 0,
                    etiqueta: nombre,
                    subtitulo: "",
                    tbl_participante_id: null,
                    foto: ""
                });
            });
            $("#grPegarFilas").val("");
            renderFilasEditor();
        });

        $("#btnTraerCandidatos").on("click", function () {
            sincronizarFilasDesdeDOM();
            var cargo = $("#grFiltroCargo").val() || "";
            var dep = $("#grFiltroDep").val() || "";
            $.ajax({
                url: "admin/ajax/rqst.php",
                type: "POST",
                dataType: "json",
                data: {
                    op: "grillaparticipantesget",
                    cargo_publico_id: cargo,
                    codigo_departamento: dep
                },
                success: function (res) {
                    var lista = (res && res.output && res.output.response) || [];
                    if (!lista.length) {
                        mostrarMensaje("No se encontraron candidatos registrados con ese filtro.", "error");
                        return;
                    }
                    state.editando.origen_filas = "participantes";
                    state.editando.filas = state.editando.filas.filter(function (f) {
                        return $.trim(f.etiqueta) !== "";
                    });
                    lista.forEach(function (cand) {
                        var yaExiste = state.editando.filas.some(function (f) {
                            return parseInt(f.tbl_participante_id || 0, 10) === parseInt(cand.id, 10) ||
                                   f.etiqueta.toLowerCase() === (cand.nombre_completo || "").toLowerCase();
                        });
                        if (!yaExiste) {
                            state.editando.filas.push({
                                id: 0,
                                etiqueta: cand.nombre_completo || "",
                                subtitulo: cand.partidos || cand.cargo_publico || "",
                                tbl_participante_id: parseInt(cand.id, 10),
                                foto: cand.foto || ""
                            });
                        }
                    });
                    renderFilasEditor();
                }
            });
        });

        $("#grFilasContainer").on("click", ".btn-subir-fila", function () {
            sincronizarFilasDesdeDOM();
            var idx = parseInt($(this).attr("data-idx"), 10);
            if (idx > 0) {
                var tmp = state.editando.filas[idx - 1];
                state.editando.filas[idx - 1] = state.editando.filas[idx];
                state.editando.filas[idx] = tmp;
                renderFilasEditor();
            }
        });

        $("#grFilasContainer").on("click", ".btn-bajar-fila", function () {
            sincronizarFilasDesdeDOM();
            var idx = parseInt($(this).attr("data-idx"), 10);
            if (idx < state.editando.filas.length - 1) {
                var tmp = state.editando.filas[idx + 1];
                state.editando.filas[idx + 1] = state.editando.filas[idx];
                state.editando.filas[idx] = tmp;
                renderFilasEditor();
            }
        });

        $("#grFilasContainer").on("click", ".btn-quitar-fila", function () {
            sincronizarFilasDesdeDOM();
            var idx = parseInt($(this).attr("data-idx"), 10);
            state.editando.filas.splice(idx, 1);
            renderFilasEditor();
        });

        $("#btnAgregarColumna").on("click", function () {
            sincronizarColumnasDesdeDOM();
            var tienePrimera = state.editando.columnas.length > 0;
            state.editando.columnas.push({
                id: 0,
                titulo: "",
                tipo_respuesta: "unica",
                limite_multiple: 1,
                obligatoria: 1,
                rol_resultado: "ninguno",
                condicion_activa: tienePrimera,
                condicion_columna_orden: tienePrimera ? 1 : null,
                condicion_opcion_texto: tienePrimera && state.editando.columnas[0].opciones[0] ? state.editando.columnas[0].opciones[0].texto : "",
                mensaje_bloqueo: "Esta pregunta no aplica. Pase al siguiente candidato.",
                opciones: JSON.parse(JSON.stringify(QUICK_OPTIONS.sino))
            });
            renderColumnasEditor();
        });

        $("#grColumnasContainer").on("click", ".btn-quitar-columna", function () {
            sincronizarColumnasDesdeDOM();
            var idx = parseInt($(this).attr("data-idx"), 10);
            state.editando.columnas.splice(idx, 1);
            renderColumnasEditor();
        });

        $("#grColumnasContainer").on("click", ".btn-plantilla-opciones", function () {
            sincronizarColumnasDesdeDOM();
            var idx = parseInt($(this).attr("data-idx"), 10);
            var tpl = $(this).attr("data-tpl");
            if (QUICK_OPTIONS[tpl] && state.editando.columnas[idx]) {
                state.editando.columnas[idx].opciones = JSON.parse(JSON.stringify(QUICK_OPTIONS[tpl]));
                renderColumnasEditor();
            }
        });

        $("#grColumnasContainer").on("change", ".inp-col-opciones", function () {
            sincronizarColumnasDesdeDOM();
            renderColumnasEditor();
        });

        $("#grColumnasContainer").on("change", ".sel-col-tipo", function () {
            $(this).closest(".gr-col-card").find(".box-col-limite").toggleClass("d-none", $(this).val() !== "multiple");
        });

        $("#grColumnasContainer").on("change", ".sel-col-cond-modo", function () {
            sincronizarColumnasDesdeDOM();
            renderColumnasEditor();
        });

        $("#grColumnasContainer").on("change", ".sel-col-cond-col", function () {
            sincronizarColumnasDesdeDOM();
            renderColumnasEditor();
        });

        $("#btnVistaCelular").on("click", function () {
            state.modoPreview = "celular";
            $("#btnVistaCelular").addClass("active");
            $("#btnVistaComputador").removeClass("active");
            renderVistaPrevia();
        });

        $("#btnVistaComputador").on("click", function () {
            state.modoPreview = "computador";
            $("#btnVistaComputador").addClass("active");
            $("#btnVistaCelular").removeClass("active");
            renderVistaPrevia();
        });

        $("#grPreviewArea").on("click", ".btn-preview-choice", function () {
            var fIdx = parseInt($(this).attr("data-fila"), 10);
            var cIdx = parseInt($(this).attr("data-col"), 10);
            var val = $(this).attr("data-val");
            if (!state.previewRespuestas[fIdx]) {
                state.previewRespuestas[fIdx] = {};
            }
            state.previewRespuestas[fIdx][cIdx] = val;
            renderVistaPrevia();
        });

        $("#grPreviewArea").on("change", ".sel-preview-table", function () {
            var fIdx = parseInt($(this).attr("data-fila"), 10);
            var cIdx = parseInt($(this).attr("data-col"), 10);
            var val = $(this).val();
            if (!state.previewRespuestas[fIdx]) {
                state.previewRespuestas[fIdx] = {};
            }
            state.previewRespuestas[fIdx][cIdx] = val;
            renderVistaPrevia();
        });

        $("#grPreviewArea").on("click", "#btnPrevCandidatoPreview", function () {
            if (state.previewFilaIdx > 0) {
                state.previewFilaIdx--;
                renderVistaPrevia();
            }
        });

        $("#grPreviewArea").on("click", "#btnNextCandidatoPreview", function () {
            state.previewFilaIdx++;
            renderVistaPrevia();
        });

        $("#btnGuardarPlantilla").on("click", function () {
            guardarPlantillaCompleta();
        });

        $("#grCardsContainer").on("click", ".btn-editar-plantilla", function () {
            var id = parseInt($(this).attr("data-id"), 10);
            abrirWizardEdicion(id);
        });

        $("#grCardsContainer").on("click", ".btn-duplicar-plantilla", function () {
            var id = parseInt($(this).attr("data-id"), 10);
            var nombreOrig = $(this).attr("data-nombre") || "Tabla";
            Swal.fire({
                title: "Duplicar la tabla",
                text: "Escriba el nombre de la copia (por ejemplo, para otro municipio).",
                input: "text",
                inputValue: nombreOrig + " (Copia)",
                showCancelButton: true,
                confirmButtonText: "Crear copia",
                cancelButtonText: "Cancelar",
                reverseButtons: true,
                inputValidator: function (v) {
                    return $.trim(v) === "" ? "Escriba un nombre para la copia." : null;
                }
            }).then(function (result) {
                if (!result.value) return;
                var nuevoNombre = $.trim(result.value);
                $.ajax({
                    url: "admin/ajax/rqst.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        op: "grillaplantilladuplicar",
                        id: id,
                        nombre: nuevoNombre
                    },
                    success: function (res) {
                        if (res && res.output && res.output.valid) {
                            mostrarMensaje("Guardado ✓ Se creó la copia «" + nuevoNombre + "».", "success");
                            cargarListado();
                        } else {
                            mostrarMensaje((res && res.output && res.output.response && res.output.response.content) || "No se pudo duplicar la tabla.", "error");
                        }
                    }
                });
            });
        });

        $("#grCardsContainer").on("click", ".btn-eliminar-plantilla", function () {
            var id = parseInt($(this).attr("data-id"), 10);
            var nombre = $(this).attr("data-nombre") || "";
            var pregs = parseInt($(this).attr("data-pregs") || "0", 10);
            var resps = parseInt($(this).attr("data-resps") || "0", 10);

            if (resps > 0) {
                mostrarMensaje("La tabla «" + nombre + "» ya tiene " + resps + " respuesta(s) registrada(s) y no se puede eliminar para proteger los resultados. Puede editarla y cambiar su estado a Inactiva.", "error");
                return;
            }

            var msgConfirm = "Se retirará la tabla «" + nombre + "» de la lista.";
            if (pregs > 0) {
                msgConfirm += " Hoy la usa " + pregs + (pregs === 1 ? " pregunta" : " preguntas") + ", así que primero debe quitarla de allí.";
            }
            Swal.fire({
                title: "¿Eliminar esta tabla?",
                text: msgConfirm,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "No, dejarla",
                reverseButtons: true
            }).then(function (result) {
                if (!result.value) return;
                $.ajax({
                    url: "admin/ajax/rqst.php",
                    type: "POST",
                    dataType: "json",
                    data: { op: "grillaplantilladelete", id: id },
                    success: function (res) {
                        if (res && res.output && res.output.valid) {
                            mostrarMensaje("La tabla «" + nombre + "» fue retirada de la lista.", "success");
                            cargarListado();
                        } else {
                            mostrarMensaje((res && res.output && res.output.response && res.output.response.content) || "No se pudo eliminar la tabla.", "error");
                        }
                    }
                });
            });
        });
    }

    $(init);
})(jQuery);
