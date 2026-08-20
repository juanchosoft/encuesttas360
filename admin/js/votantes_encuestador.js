/**
 * Gestión de Votantes - Versión iOS Compatible (COMPLETO)
 */
$(document).ready(function () {
    // 1. Bloqueo inicial
    // Usamos selectores específicos para asegurar que el iPhone reconozca el bloqueo
    var $elementosA_Bloquear = $('#formvotantes input, #formvotantes select, #formvotantes textarea, .btn-cta-save, .btn-cta-cancel');

    $elementosA_Bloquear
        .not('#op, #idVotantes, #opcionActivaWeb')
        .prop('disabled', true)
        .addClass('disabled'); // Refuerzo visual para CSS

    // 2. Escuchar el evento de activación desde certificacion_encuestador.js
    $(document).on('certification-started', function () {
        $elementosA_Bloquear.prop('disabled', false).removeClass('disabled');
        console.log("Formulario desbloqueado para iOS");

        // Forzamos un refresco visual para iPhone
        $('.btn-cta-save, .btn-cta-cancel').css('cursor', 'pointer').css('opacity', '1');
    });
});

var return_page = "votantes_encuestador.php";
var VOTANTES = {
    _guardando: false,

    tieneSondeoActivo: function () {
        return ($("#sondeoActivoId").val() || "").toString().trim() !== "";
    },

    tieneCuestionarioActivo: function () {
        return ($("#cuestionarioActivoId").val() || "").toString().trim() !== "";
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
        $("#veSavingMsg").text(mensaje || "El registro y la certificación se completaron.");
        $("#veSavingOverlay").attr("aria-busy", "false");
    },

    ocultarGuardando: function () {
        $("#veSavingOverlay").addClass("is-hidden").attr("aria-busy", "false");
        $("#veSavingCard").removeClass("is-success");
        $("body").removeClass("ve-saving-lock");
        $("#btnGuardarVotante, #btnCancelarVotante").prop("disabled", false);
        VOTANTES._guardando = false;
    },

    // Función para limpiar celdas (el botón cancelar que mencionas)
    emptyCells: function () {
        if (VOTANTES._guardando) {
            return;
        }
        if (confirm("¿Estás seguro de limpiar el formulario?")) {
            $('#formvotantes')[0].reset();
            // Si el sondeo está activo, limpiar valores ocultos
            $('#sondeoRespuestaCandidato, #sondeoRespuestaOpcion').val('');
            $('.sondeo-opcion-btn').removeClass('active btn-primary is-selected').addClass('btn-outline-primary');
            $('.sondeo-opcion-btn .fa-check-circle').hide();
            // Limpiar cuestionario
            $('#preguntas_container input[type="radio"], #preguntas_container input[type="checkbox"]').prop('checked', false);
            $('#preguntas_container textarea').val('');
            $('.pregunta-card').removeClass('is-invalid border-danger');
        }
    },

    // Función para seleccionar opción del sondeo
    seleccionarOpcionSondeo: function (btn) {
        var $btn = $(btn);
        var tipo = $btn.data('tipo');
        var valor = $btn.data('valor');

        // Quitar selección anterior
        $('.sondeo-opcion-btn').removeClass('active btn-primary is-selected').addClass('btn-outline-primary');
        $('.sondeo-opcion-btn .fa-check-circle').hide();

        // Marcar como seleccionado
        $btn.removeClass('btn-outline-primary').addClass('active btn-primary is-selected');
        $btn.find('.fa-check-circle').show();

        // Guardar valor en campos ocultos
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

        // Validar todos los campos requeridos
        var errores = [];

        $("#nombre_completo").val('Encuestado');

        // Departamento
        if ($("#tbl_departamento_id").val() === "" || $("#tbl_departamento_id").val() === null) {
            errores.push("Departamento");
            $("#tbl_departamento_id").addClass("is-invalid");
        } else {
            $("#tbl_departamento_id").removeClass("is-invalid");
        }

        // Municipio
        if ($("#tbl_municipio_id").val() === "" || $("#tbl_municipio_id").val() === null) {
            errores.push("Municipio");
            $("#tbl_municipio_id").addClass("is-invalid");
        } else {
            $("#tbl_municipio_id").removeClass("is-invalid");
        }

        // Ideología política
        if ($("#ideologia").val() === "" || $("#ideologia").val() === null) {
            errores.push("Ideología política");
            $("#ideologia").addClass("is-invalid");
        } else {
            $("#ideologia").removeClass("is-invalid");
        }

        // Rango de edad
        if ($("#rango_edad").val() === "" || $("#rango_edad").val() === null) {
            errores.push("Rango de edad");
            $("#rango_edad").addClass("is-invalid");
        } else {
            $("#rango_edad").removeClass("is-invalid");
        }

        // Nivel socioeconómico (nivel_ingresos)
        if ($("#nivel_ingresos").val() === "" || $("#nivel_ingresos").val() === null) {
            errores.push("Nivel socioeconómico");
            $("#nivel_ingresos").addClass("is-invalid");
        } else {
            $("#nivel_ingresos").removeClass("is-invalid");
        }

        // Género
        if ($("#genero").val() === "" || $("#genero").val() === null) {
            errores.push("Género");
            $("#genero").addClass("is-invalid");
        } else {
            $("#genero").removeClass("is-invalid");
        }

        // Nivel educativo
        if ($("#nivel_educacion").val() === "" || $("#nivel_educacion").val() === null) {
            errores.push("Nivel educativo");
            $("#nivel_educacion").addClass("is-invalid");
        } else {
            $("#nivel_educacion").removeClass("is-invalid");
        }

        // Ocupación
        if ($("#ocupacion").val() === "" || $("#ocupacion").val() === null) {
            errores.push("Ocupación");
            $("#ocupacion").addClass("is-invalid");
        } else {
            $("#ocupacion").removeClass("is-invalid");
        }

        // Estado de la cuenta
        if ($("#estado").val() === "" || $("#estado").val() === null) {
            errores.push("Estado de la cuenta");
            $("#estado").addClass("is-invalid");
        } else {
            $("#estado").removeClass("is-invalid");
        }

        // ========================================
        // VALIDACIÓN SONDEO ACTIVO (OBLIGATORIO)
        // ========================================
        if (VOTANTES.tieneSondeoActivo()) {
            var sondeoId = $("#sondeoActivoId").val();
            if (sondeoId) {
                var candidatoSeleccionado = $("#sondeoRespuestaCandidato").val();
                var opcionSeleccionada = $("#sondeoRespuestaOpcion").val();

                if (!candidatoSeleccionado && !opcionSeleccionada) {
                    errores.push("Debe seleccionar una opción del sondeo");
                    // Resaltar visualmente el contenedor del sondeo
                    $("#sondeoOpcionesContainer").addClass("border border-danger rounded p-2");
                } else {
                    $("#sondeoOpcionesContainer").removeClass("border border-danger rounded p-2");
                }
            }
        }

        // ========================================
        // VALIDACIÓN CUESTIONARIO (TODAS LAS PREGUNTAS OBLIGATORIAS)
        // ========================================
        if (VOTANTES.tieneCuestionarioActivo()) {
            var cuestionarioId = $("#cuestionarioActivoId").val();
            if (cuestionarioId) {
                var preguntasSinResponder = [];

                // Recorrer cada pregunta
                $(".pregunta-card").each(function (index) {
                    var $pregunta = $(this);
                    var numeroPregunta = index + 1;
                    var respondida = false;

                    // Verificar si tiene radio buttons o checkboxes seleccionados
                    var $radios = $pregunta.find('input[type="radio"]');
                    var $checkboxes = $pregunta.find('input[type="checkbox"]');
                    var $textarea = $pregunta.find('textarea.respuesta-texto');

                    if ($radios.length > 0) {
                        // Es pregunta de selección única
                        respondida = $radios.filter(':checked').length > 0;
                    } else if ($checkboxes.length > 0) {
                        // Es pregunta de selección múltiple
                        respondida = $checkboxes.filter(':checked').length > 0;
                    } else if ($textarea.length > 0) {
                        // Es pregunta abierta
                        respondida = $textarea.val().trim() !== '';
                    }

                    if (!respondida) {
                        preguntasSinResponder.push(numeroPregunta);
                        $pregunta.addClass("is-invalid border-danger");
                    } else {
                        $pregunta.removeClass("is-invalid border-danger");
                    }
                });

                if (preguntasSinResponder.length > 0) {
                    errores.push("Debe responder todas las preguntas del cuestionario (faltan: " + preguntasSinResponder.join(", ") + ")");
                }
            }
        }

        // Mostrar errores si existen
        if (errores.length > 0) {
            UTIL.mostrarMensajeValidacion("Faltan campos obligatorios: " + errores.join(", "));
            return;
        }

        VOTANTES.savedata();
    },

    savedata: function () {
        if (VOTANTES._guardando) {
            return;
        }
        VOTANTES._guardando = true;

        UTIL.cursorBusy();
        VOTANTES.mostrarGuardando(
            "Preparando registro...",
            "Finalizando audio y preparando los datos. No cierres esta pantalla."
        );

        // Detener audio y esperar promesa
        CERTIFICACION_ENCUESTADOR.detenerGrabacion().then(function () {
            VOTANTES.actualizarGuardando(
                "Guardando encuestado...",
                "Enviando respuestas. Luego se subirá el audio y el GPS."
            );

            var respuestas = [];
            if (VOTANTES.tieneCuestionarioActivo()) {
                $('.pregunta-card').each(function () {
                    var pId = $(this).data('pregunta-id');
                    var opts = [];
                    $(this).find('input:checked').each(function () { opts.push($(this).val()); });
                    respuestas.push({ pregunta_id: pId, opciones: opts, texto: $(this).find('textarea').val() || '' });
                });
            }

            var q = {
                op: "votantessave",
                id: $("#idVotantes").val(),
                nombre_completo: "Encuestado",
                ideologia: $("#ideologia").val(),
                rango_edad: $("#rango_edad").val(),
                genero: $("#genero").val(),
                codigo_departamento: $("#tbl_departamento_id").val(),
                codigo_municipio: $("#tbl_municipio_id").val(),
                comuna: $("#comuna").val(),
                barrio: $("#barrio").val(),
                estado: $("#estado").val() || 'activo',
                nivel_ingresos: $("#nivel_ingresos").val(),
                nivel_educacion: $("#nivel_educacion").val(),
                ocupacion: $("#ocupacion").val(),
                validar: 'no',
                sondeo_id: $("#sondeoActivoId").val(),
                cuestionario_id: $("#cuestionarioActivoId").val(),
                sondeo_candidato_id: $('#sondeoRespuestaCandidato').val(),
                sondeo_opcion_id: $('#sondeoRespuestaOpcion').val(),
                cuestionario_respuestas: JSON.stringify(respuestas)
            };

            $.ajax({
                data: q,
                type: "POST",
                dataType: "json",
                url: "admin/ajax/rqst.php",
                timeout: 120000,
                success: function (data) {
                    if (data.output.valid) {
                        VOTANTES.guardarCertificacion(data.output.response);
                    } else {
                        UTIL.cursorNormal();
                        VOTANTES.ocultarGuardando();
                        UTIL.mostrarMensajeError(data.output.response.content);
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

    guardarCertificacion: function (votanteId) {
        VOTANTES.actualizarGuardando(
            "Subiendo audio y GPS...",
            "Esto puede tardar si la grabación es larga. Mantén la pantalla abierta."
        );

        CERTIFICACION_ENCUESTADOR.obtenerDatosCertificacion(votanteId).then(function (datos) {
            var origenTipo = 'registro_simple';
            if (VOTANTES.tieneSondeoActivo()) {
                origenTipo = 'sondeo';
            }
            if (VOTANTES.tieneCuestionarioActivo()) {
                origenTipo = 'cuestionario';
            }

            datos.op = 'certificacionsave';
            datos.origen_tipo = origenTipo;
            datos.tbl_sondeo_id = $("#sondeoActivoId").val();
            datos.tbl_ficha_tecnica_encuesta_id = $("#cuestionarioActivoId").val();
            $.ajax({
                data: datos,
                type: "POST",
                dataType: "json",
                url: "admin/ajax/rqst.php",
                timeout: 180000,
                success: function (data) {
                    UTIL.cursorNormal();
                    if (data && data.output && data.output.valid) {
                        VOTANTES.mostrarGuardadoExitoso(
                            "¡Guardado correctamente!",
                            "Registro y certificación completados. Redirigiendo..."
                        );
                        if (typeof UTIL.mostrarMensajeExitoso === "function") {
                            UTIL.mostrarMensajeExitoso("Registro guardado y certificado");
                        }
                        setTimeout(function () { window.location = return_page; }, 1600);
                        return;
                    }

                    VOTANTES.ocultarGuardando();
                    var mensaje = (data && data.output && data.output.response && data.output.response.content)
                        ? data.output.response.content
                        : "El registro se guardó, pero la certificación no se pudo completar.";
                    UTIL.mostrarMensajeError(mensaje);
                },
                error: function () {
                    UTIL.cursorNormal();
                    VOTANTES.ocultarGuardando();
                    UTIL.mostrarMensajeError("El registro se guardó, pero la certificación no se pudo completar. Revisa la conexión.");
                }
            });
        }).catch(function () {
            UTIL.cursorNormal();
            VOTANTES.ocultarGuardando();
            UTIL.mostrarMensajeError("El registro se guardó, pero no se pudieron preparar los datos de certificación.");
        });
    }
};
