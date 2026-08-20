const CONTESTAR_CUESTIONARIO = {
    fichaTecnicaId: 0,

    init: function() {
        console.log('Iniciando módulo Contestar Cuestionario');

        // Obtener fichaTecnicaId desde el atributo data
        CONTESTAR_CUESTIONARIO.fichaTecnicaId = parseInt($('#cuestionario_container').data('ficha-tecnica-id')) || 0;

        // Verificar que exista el formulario
        if ($('#form_cuestionario').length === 0) {
            console.log('No hay formulario de cuestionario en esta página');
            return;
        }

        // Cargar votantes disponibles
        CONTESTAR_CUESTIONARIO.cargarVotantesDisponibles();

        // Setup form submit
        $('#form_cuestionario').on('submit', function(e) {
            e.preventDefault();
            CONTESTAR_CUESTIONARIO.enviarRespuestas();
        });

        // Track progress
        $('input[type="radio"], input[type="checkbox"], textarea.respuesta-texto').on('change keyup', function() {
            CONTESTAR_CUESTIONARIO.actualizarProgreso();
        });

        // Trigger progress on load
        CONTESTAR_CUESTIONARIO.actualizarProgreso();
    },

    cargarVotantesDisponibles: function() {
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'votantesdisponiblesget',
                ficha_tecnica_id: CONTESTAR_CUESTIONARIO.fichaTecnicaId
            },
            success: function(response) {
                console.log('Response votantes:', response);

                if (response.output && response.output.valid) {
                    const votantes = response.output.response;
                    let options = '<option value="">Selecciona un votante...</option>';

                    if (votantes.length === 0) {
                        options = '<option value="">No hay votantes disponibles</option>';
                    } else {
                        votantes.forEach(function(votante) {
                            options += `<option value="${votante.id}">${votante.nombre_completo}</option>`;
                        });
                    }

                    $('#tbl_votante_id').html(options);
                } else {
                    console.error('Respuesta inválida:', response);
                    $('#tbl_votante_id').html('<option value="">Error al cargar votantes</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                console.error('Response:', xhr.responseText);
                $('#tbl_votante_id').html('<option value="">Error al cargar votantes</option>');
            }
        });
    },

    actualizarProgreso: function() {
        const totalPreguntas = $('.pregunta-card').length;
        let contestadas = 0;

        $('.pregunta-card').each(function() {
            const preguntaId = $(this).data('pregunta-id');

            // Verificar si tiene respuesta
            const tieneRadioCheck = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked').length > 0;

            // Verificar si tiene textarea y si tiene texto
            const textarea = $(this).find('textarea.respuesta-texto');
            const tieneTexto = textarea.length > 0 && textarea.val() && textarea.val().trim() !== '';

            if (tieneRadioCheck || tieneTexto) {
                contestadas++;
            }
        });

        const porcentaje = totalPreguntas > 0 ? Math.round((contestadas / totalPreguntas) * 100) : 0;
        $('#progress_bar').css('width', porcentaje + '%');
    },

    enviarRespuestas: function() {
        // Validar que todas las preguntas estén contestadas
        const totalPreguntas = $('.pregunta-card').length;
        let contestadas = 0;

        $('.pregunta-card').each(function() {
            const tieneRadioCheck = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked').length > 0;

            // Verificar si tiene textarea y si tiene texto
            const textarea = $(this).find('textarea.respuesta-texto');
            const tieneTexto = textarea.length > 0 && textarea.val() && textarea.val().trim() !== '';

            if (tieneRadioCheck || tieneTexto) {
                contestadas++;
            }
        });

        if (contestadas < totalPreguntas) {
            Swal.fire({
                icon: 'warning',
                title: 'Preguntas sin responder',
                text: `Por favor, responde todas las preguntas. Faltan ${totalPreguntas - contestadas} pregunta(s) por contestar.`,
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Validar que se haya seleccionado un votante
        const votanteId = $('#tbl_votante_id').val();
        if (!votanteId) {
            Swal.fire({
                icon: 'warning',
                title: 'Votante no seleccionado',
                text: 'Por favor, selecciona un votante antes de enviar las respuestas.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Recopilar todas las respuestas
        const respuestas = {
            ficha_tecnica_id: CONTESTAR_CUESTIONARIO.fichaTecnicaId,
            tbl_votante_id: votanteId,
            preguntas: []
        };

        // Recorrer cada pregunta y recopilar respuestas
        $('.pregunta-card').each(function() {
            const preguntaId = $(this).data('pregunta-id');
            const respuestaPregunta = {
                pregunta_id: preguntaId,
                opciones: [],
                texto: ''
            };

            // Verificar si hay opciones seleccionadas
            $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked').each(function() {
                respuestaPregunta.opciones.push($(this).val());
            });

            // Verificar si hay respuesta de texto
            const textoRespuesta = $(this).find('textarea.respuesta-texto').val();
            if (textoRespuesta) {
                respuestaPregunta.texto = textoRespuesta;
            }

            respuestas.preguntas.push(respuestaPregunta);
        });

        // Enviar via AJAX
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'respuestasave',
                data: JSON.stringify(respuestas)
            },
            success: function(response) {
                console.log('Response guardar:', response);

                if (response.output && response.output.valid) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Respuestas enviadas!',
                        text: 'Gracias por completar el cuestionario. Tus respuestas han sido guardadas exitosamente.',
                        confirmButtonText: 'Cerrar',
                        allowOutsideClick: false
                    }).then(() => {
                        // Limpiar formulario
                        $('#form_cuestionario')[0].reset();
                        $('#progress_bar').css('width', '0%');

                        // Recargar votantes disponibles
                        CONTESTAR_CUESTIONARIO.cargarVotantesDisponibles();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al enviar',
                        text: response.output.message || 'Ocurrió un error al enviar las respuestas. Por favor, intenta de nuevo.',
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar:', status, error);
                console.error('Response:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Por favor, verifica tu conexión e intenta de nuevo.',
                    confirmButtonText: 'Entendido'
                });
            }
        });
    }
};

// Auto-inicialización
$(document).ready(function() {
    CONTESTAR_CUESTIONARIO.init();
});
