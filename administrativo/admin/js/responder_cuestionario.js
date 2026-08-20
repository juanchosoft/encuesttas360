const RESPONDER_CUESTIONARIO = {
    codigoCuestionario: '',
    cuestionarioData: null,
    preguntasData: {},
    respuestas: {},

    init: function() {
        // Obtener código del cuestionario desde el atributo data
        RESPONDER_CUESTIONARIO.codigoCuestionario = $('#cuestionario_container').data('codigo-cuestionario') || '';

        // Cargar cuestionario desde localStorage
        RESPONDER_CUESTIONARIO.cargarCuestionario();

        // Setup form submit
        $('#form_cuestionario').on('submit', function(e) {
            e.preventDefault();
            RESPONDER_CUESTIONARIO.enviarRespuestas();
        });

        // Track progress
        $(document).on('change', 'input[type="radio"], input[type="checkbox"]', function() {
            RESPONDER_CUESTIONARIO.actualizarProgreso();
        });
    },

    cargarCuestionario: function() {
        const key = `cuestionario_${RESPONDER_CUESTIONARIO.codigoCuestionario}`;
        const dataStr = localStorage.getItem(key);

        if (!dataStr) {
            $('#loader').hide();
            $('#error_message').fadeIn();
            return;
        }

        try {
            RESPONDER_CUESTIONARIO.cuestionarioData = JSON.parse(dataStr);
            RESPONDER_CUESTIONARIO.cargarPreguntas();
        } catch (error) {
            console.error('Error parsing cuestionario:', error);
            $('#loader').hide();
            $('#error_message').fadeIn();
        }
    },

    cargarPreguntas: function() {
        const cuestionario = RESPONDER_CUESTIONARIO.cuestionarioData;

        // Mostrar título y descripción
        $('#cuestionario_titulo').text(cuestionario.titulo);
        $('#cuestionario_descripcion').text(cuestionario.descripcion || '');

        // Cargar cada pregunta vía AJAX
        const preguntasIds = cuestionario.preguntas.map(p => p.id);

        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'preguntaget',
                ids: preguntasIds.join(',')
            },
            success: function(response) {
                if (response.output && response.output.valid) {
                    const preguntas = response.output.response;

                    // Ordenar según el cuestionario
                    const preguntasOrdenadas = [];
                    cuestionario.preguntas.forEach(function(pConfig) {
                        const pregunta = preguntas.find(p => p.id == pConfig.id);
                        if (pregunta) {
                            pregunta.obligatoria = pConfig.obligatoria;
                            pregunta.puntos = pConfig.puntos;
                            pregunta.orden = pConfig.orden;
                            preguntasOrdenadas.push(pregunta);
                        }
                    });

                    // Renderizar preguntas
                    RESPONDER_CUESTIONARIO.renderizarPreguntas(preguntasOrdenadas);

                    // Mostrar cuestionario
                    $('#loader').hide();
                    $('#cuestionario_content').fadeIn();
                } else {
                    $('#loader').hide();
                    $('#error_message').fadeIn();
                }
            },
            error: function() {
                $('#loader').hide();
                $('#error_message').fadeIn();
            }
        });
    },

    renderizarPreguntas: function(preguntas) {
        let html = '';

        preguntas.forEach(function(pregunta, index) {
            const obligatoria = pregunta.obligatoria === 'si';
            const numero = index + 1;

            html += `
                <div class="pregunta-card" data-pregunta-id="${pregunta.id}">
                    <div class="d-flex align-items-start mb-4">
                        <div class="pregunta-numero">${numero}</div>
                        <div class="flex-grow-1">
                            <h5 class="mb-0">
                                ${pregunta.texto_pregunta}
                                ${obligatoria ? '<span class="badge-obligatoria">Obligatoria</span>' : ''}
                            </h5>
                            <small class="text-muted">${pregunta.tipo_pregunta}</small>
                        </div>
                    </div>

                    <div class="opciones-container">
                        ${RESPONDER_CUESTIONARIO.renderizarOpciones(pregunta)}
                    </div>
                </div>
            `;
        });

        $('#preguntas_container').html(html);
    },

    renderizarOpciones: function(pregunta) {
        if (!pregunta.opciones_str) {
            return '<textarea class="form-control" rows="3" placeholder="Escribe tu respuesta aquí..."></textarea>';
        }

        const opciones = pregunta.opciones_str.split(';');
        let html = '';
        const tipo = pregunta.tipo_pregunta.toLowerCase();
        const isMultiple = tipo.includes('múltiple') && tipo.includes('múltiple respuesta');
        const inputType = isMultiple ? 'checkbox' : 'radio';
        const name = `pregunta_${pregunta.id}`;

        opciones.forEach(function(opcion, index) {
            const partes = opcion.split(':');
            if (partes.length >= 2) {
                const opcionId = partes[0];
                const opcionTexto = partes[1];

                html += `
                    <div class="form-check">
                        <input class="form-check-input" type="${inputType}"
                               name="${name}" id="opcion_${opcionId}"
                               value="${opcionId}" data-pregunta-id="${pregunta.id}">
                        <label class="form-check-label" for="opcion_${opcionId}">
                            ${opcionTexto}
                        </label>
                    </div>
                `;
            }
        });

        return html;
    },

    actualizarProgreso: function() {
        const totalPreguntas = $('.pregunta-card').length - 1; // -1 por la card de info
        let contestadas = 0;

        $('.pregunta-card').each(function() {
            const preguntaId = $(this).data('pregunta-id');
            if (preguntaId) {
                const hasAnswer = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked').length > 0 ||
                                $(this).find('textarea').val().trim() !== '';
                if (hasAnswer) contestadas++;
            }
        });

        const porcentaje = Math.round((contestadas / totalPreguntas) * 100);
        $('#progress_bar').css('width', porcentaje + '%');
    },

    enviarRespuestas: function() {
        // Recopilar respuestas
        const respuestas = {
            codigo_cuestionario: RESPONDER_CUESTIONARIO.codigoCuestionario,
            nombre: $('#nombre_respondiente').val(),
            identificacion: $('#identificacion_respondiente').val(),
            email: $('#email_respondiente').val(),
            preguntas: []
        };

        $('.pregunta-card').each(function() {
            const preguntaId = $(this).data('pregunta-id');
            if (!preguntaId) return;

            const respuestasSeleccionadas = [];
            $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked').each(function() {
                respuestasSeleccionadas.push($(this).val());
            });

            const respuestaTexto = $(this).find('textarea').val() || '';

            if (respuestasSeleccionadas.length > 0 || respuestaTexto) {
                respuestas.preguntas.push({
                    pregunta_id: preguntaId,
                    opciones: respuestasSeleccionadas,
                    texto: respuestaTexto
                });
            }
        });

        // Guardar en localStorage (temporal)
        const keyRespuestas = `respuestas_${RESPONDER_CUESTIONARIO.codigoCuestionario}_${Date.now()}`;
        localStorage.setItem(keyRespuestas, JSON.stringify(respuestas));

        // Mostrar mensaje de éxito
        Swal.fire({
            icon: 'success',
            title: '¡Respuestas enviadas!',
            text: 'Gracias por completar el cuestionario',
            confirmButtonText: 'Cerrar',
            allowOutsideClick: false
        }).then(() => {
            // Limpiar formulario
            $('#form_cuestionario')[0].reset();
            $('#progress_bar').css('width', '0%');

            setTimeout(() => {
                // Redirigir a la página principal
                window.location.href = "contestar_cuestionario.php";
            }, 500);
        });
    }
};

// Auto-inicialización
$(document).ready(function() {
    RESPONDER_CUESTIONARIO.init();
});
