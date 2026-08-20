// Inicializar cuando el documento esté listo
$(document).on('ready', null);

const RESULTADOS_CUESTIONARIOS = {
    fichaTecnicaSeleccionada: null,

    escapeHtml: function(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    },

    renderTipoBadge: function(tipo) {
        const valor = (tipo || 'Autoregistro').toString();
        let clases = 'bg-secondary-subtle text-secondary';

        if (valor === 'Encuestado') {
            clases = 'bg-primary-subtle text-primary';
        } else if (valor === 'Autoregistro') {
            clases = 'bg-success-subtle text-success';
        } else if (valor === 'Registro interno') {
            clases = 'bg-warning-subtle text-dark';
        }

        return '<span class="badge ' + clases + '">' + RESULTADOS_CUESTIONARIOS.escapeHtml(valor) + '</span>';
    },

    init: function() {
        console.log('Inicializando módulo de resultados de cuestionarios');

        // Event listeners
        $('#ficha_tecnica_select').on('change', RESULTADOS_CUESTIONARIOS.onFichaTecnicaChange);
        $('#btn_cargar_datos').on('click', RESULTADOS_CUESTIONARIOS.cargarEstadisticas);
    },

    onFichaTecnicaChange: function() {
        const fichaTecnicaId = $('#ficha_tecnica_select').val();

        if (fichaTecnicaId) {
            $('#btn_cargar_datos').prop('disabled', false);
            RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada = parseInt(fichaTecnicaId);
        } else {
            $('#btn_cargar_datos').prop('disabled', true);
            RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada = null;
            $('#estadisticas_container').hide();
            $('#empty_state').show();
        }
    },

    cargarEstadisticas: function() {
        if (!RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor selecciona una ficha técnica primero.'
            });
            return;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Cargando...',
            text: 'Obteniendo estadísticas',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Cargar estadísticas
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'estadisticascuestionario',
                ficha_tecnica_id: RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada
            },
            success: function(response) {
                console.log('Estadísticas:', response);

                if (response.output && response.output.valid) {
                    const stats = response.output.response;

                    $('#empty_state').hide();
                    $('#estadisticas_container').show();

                    RESULTADOS_CUESTIONARIOS.renderEstadisticas(stats);

                    // Cargar las listas de votantes
                    RESULTADOS_CUESTIONARIOS.cargarVotantesQueRespondieron();
                    RESULTADOS_CUESTIONARIOS.cargarVotantesQueNoRespondieron();

                    Swal.close();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.output.message || 'No se pudieron cargar las estadísticas'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            }
        });
    },

    renderEstadisticas: function(stats) {
        const totalVotantes = parseInt(stats.total_votantes || 0, 10);
        const totalRespondieron = parseInt(stats.total_respondieron || 0, 10);
        const totalNoRespondieron = parseInt(stats.total_no_respondieron || 0, 10);
        const porcentaje = Number(stats.porcentaje_respuestas || 0);
        const porcentajeTexto = porcentaje % 1 === 0 ? `${porcentaje.toFixed(0)}%` : `${porcentaje.toFixed(2)}%`;

        $('#total_votantes').text(totalVotantes);
        $('#total_respondieron').text(totalRespondieron);
        $('#total_no_respondieron').text(totalNoRespondieron);
        $('#porcentaje_respuestas').text(porcentajeTexto);
        $('#progress_bar')
            .css('width', `${porcentaje}%`)
            .attr('aria-valuenow', porcentaje);
        $('#progress_text').text(porcentajeTexto);

        // Renderizar últimas respuestas
        RESULTADOS_CUESTIONARIOS.renderUltimasRespuestas(stats.ultimas_respuestas);

        // Gráficas demográficas - con timeout para que el DOM sea visible
        if (typeof makeDonut === 'function') {
            setTimeout(function() {
                RESULTADOS_CUESTIONARIOS.renderDemograficas(stats);
            }, 150);
        }
    },

    renderDemograficas: function(stats) {
        // Ideología
        if (stats.ideologia && stats.ideologia.length > 0) {
            makeDonut('cq-chart-ideologia',
                stats.ideologia.map(function(i){ return parseInt(i.cantidad); }),
                stats.ideologia.map(function(i){ return i.ideologia || 'Sin definir'; }),
                ['#2E93fA','#66DA26','#546E7A','#E91E63','#FF9800','#00BCD4','#9C27B0'],
                'Total'
            );
        }
        // Género
        if (stats.genero && stats.genero.length > 0) {
            makeDonut('cq-chart-genero',
                stats.genero.map(function(i){ return parseInt(i.cantidad); }),
                stats.genero.map(function(i){ return i.genero || 'Sin definir'; }),
                ['#008FFB','#FF4560','#775DD0','#00E396'],
                'Total'
            );
        }
        // Edad
        if (stats.edad && stats.edad.length > 0) {
            makeDonut('cq-chart-edad',
                stats.edad.map(function(i){ return parseInt(i.cantidad); }),
                stats.edad.map(function(i){ return i.rango_edad || 'Sin definir'; }),
                ['#00E396','#FEB019','#FF4560','#775DD0','#546E7A','#2E93fA'],
                'Total'
            );
        }
        // Ingresos
        if (stats.ingresos && stats.ingresos.length > 0) {
            makeDonut('cq-chart-ingresos',
                stats.ingresos.map(function(i){ return parseInt(i.cantidad); }),
                stats.ingresos.map(function(i){ return i.nivel_ingresos || 'Sin definir'; }),
                ['#20427F','#2e58a8','#4facfe','#00f2fe','#a8edea'],
                'Total'
            );
        }
        // Educación
        if (stats.educacion && stats.educacion.length > 0) {
            makeBar('cq-chart-educacion',
                stats.educacion.map(function(i){ return i.nivel_educacion || 'Sin definir'; }),
                stats.educacion.map(function(i){ return parseInt(i.cantidad); }),
                ['#11998e','#38ef7d','#43b89c','#1a7a1a','#2ca02c'],
                true
            );
        }
        // Departamento
        if (stats.departamento && stats.departamento.length > 0) {
            makeBar('cq-chart-departamento',
                stats.departamento.map(function(i){ return i.departamento || i.codigo_departamento || 'Sin definir'; }),
                stats.departamento.map(function(i){ return parseInt(i.cantidad); }),
                ['#20427F','#2e58a8','#4facfe','#FEB019','#FF4560','#00E396','#775DD0','#546E7A','#FF9800','#00BCD4'],
                true
            );
        }
        // Municipio Top 10
        if (stats.municipio && stats.municipio.length > 0) {
            makeBar('cq-chart-municipio',
                stats.municipio.map(function(i){
                    var dep = i.departamento || '';
                    return dep ? (i.municipio||'?') + ', ' + dep : (i.municipio||'Sin definir');
                }),
                stats.municipio.map(function(i){ return parseInt(i.cantidad); }),
                ['#20427F','#2e58a8','#4facfe','#FEB019','#FF4560','#00E396','#775DD0','#546E7A','#FF9800','#00BCD4'],
                true
            );
        }
    },

    renderUltimasRespuestas: function(respuestas) {
        // Destruir DataTable si existe
        if ($.fn.DataTable.isDataTable('#tabla_ultimas_respuestas')) {
            $('#tabla_ultimas_respuestas').DataTable().destroy();
        }

        const filas = (respuestas || []).map(function(respuesta) {
            const fecha = new Date(respuesta.fecha_respuesta);
            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            return {
                tipo: RESULTADOS_CUESTIONARIOS.renderTipoBadge(respuesta.tipo_registro),
                encuestado: '<strong>' + RESULTADOS_CUESTIONARIOS.escapeHtml(respuesta.nombre_completo || '-') + '</strong>',
                encuestador: RESULTADOS_CUESTIONARIOS.escapeHtml(respuesta.encuestador_nombre_completo || '-'),
                email: RESULTADOS_CUESTIONARIOS.escapeHtml(respuesta.email || '-'),
                fecha: fechaFormateada,
                preguntas: '<span class="badge bg-primary">' + RESULTADOS_CUESTIONARIOS.escapeHtml((respuesta.preguntas_respondidas || 0) + ' preguntas') + '</span>',
                acciones: '<button class="btn btn-sm btn-info" onclick="RESULTADOS_CUESTIONARIOS.verDetalle(' + parseInt(respuesta.id || 0, 10) + ')"><i class="fa-solid fa-eye me-1"></i>Ver detalle</button>'
            };
        });

        $('#tabla_ultimas_respuestas tbody').empty();

        $('#tabla_ultimas_respuestas').DataTable({
            data: filas,
            columns: [
                { data: 'tipo' },
                { data: 'encuestado' },
                { data: 'encuestador' },
                { data: 'email' },
                { data: 'fecha' },
                { data: 'preguntas' },
                { data: 'acciones' }
            ],
            searching: true,
            paging: true,
            pageLength: 10,
            lengthChange: false,
            info: false,
            ordering: true,
            order: [[4, 'desc']], // Ordenar por fecha descendente
            columnDefs: [
                { targets: [0, 1, 5, 6], orderable: false },
                { targets: '_all', defaultContent: '-' }
            ],
            language: {
                search: "Buscar:",
                emptyTable: "No hay respuestas registradas aún",
                zeroRecords: "No se encontraron registros",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    },

    cargarVotantesQueRespondieron: function() {
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'votantesrespondieron',
                ficha_tecnica_id: RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada
            },
            success: function(response) {
                console.log('Votantes que respondieron:', response);

                if (response.output && response.output.valid) {
                    RESULTADOS_CUESTIONARIOS.renderVotantesRespondieron(response.output.response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar votantes que respondieron:', status, error);
            }
        });
    },

    renderVotantesRespondieron: function(votantes) {
        // Destruir DataTable si existe
        if ($.fn.DataTable.isDataTable('#tabla_respondieron')) {
            $('#tabla_respondieron').DataTable().destroy();
        }

        const filas = (votantes || []).map(function(votante) {
            const fecha = new Date(votante.fecha_respuesta);
            const fechaFormateada = fecha.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            return {
                tipo: RESULTADOS_CUESTIONARIOS.renderTipoBadge(votante.tipo_registro),
                encuestado: '<strong>' + RESULTADOS_CUESTIONARIOS.escapeHtml(votante.nombre_completo || '-') + '</strong>',
                encuestador: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.encuestador_nombre_completo || '-'),
                email: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.email || '-'),
                genero: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.genero || '-'),
                edad: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.rango_edad || '-'),
                ideologia: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.ideologia || '-'),
                fecha: fechaFormateada,
                preguntas: '<span class="badge bg-success">' + RESULTADOS_CUESTIONARIOS.escapeHtml((votante.preguntas_respondidas || 0) + ' preguntas') + '</span>',
                acciones: '<button class="btn btn-sm btn-info" onclick="RESULTADOS_CUESTIONARIOS.verDetalle(' + parseInt(votante.intento_id || 0, 10) + ')"><i class="fa-solid fa-eye me-1"></i>Ver</button>'
            };
        });

        $('#tabla_respondieron tbody').empty();

        $('#tabla_respondieron').DataTable({
            data: filas,
            columns: [
                { data: 'tipo' },
                { data: 'encuestado' },
                { data: 'encuestador' },
                { data: 'email' },
                { data: 'genero' },
                { data: 'edad' },
                { data: 'ideologia' },
                { data: 'fecha' },
                { data: 'preguntas' },
                { data: 'acciones' }
            ],
            searching: true,
            paging: true,
            pageLength: 25,
            lengthChange: false,
            info: false,
            ordering: true,
            order: [[7, 'desc']], // Ordenar por fecha descendente
            columnDefs: [
                { targets: [0, 1, 8, 9], orderable: false },
                { targets: '_all', defaultContent: '-' }
            ],
            language: {
                search: "Buscar:",
                emptyTable: "Ningún votante ha respondido aún",
                zeroRecords: "No se encontraron registros",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    },

    cargarVotantesQueNoRespondieron: function() {
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'votantesnorespondieron',
                ficha_tecnica_id: RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada
            },
            success: function(response) {
                console.log('Votantes que NO respondieron:', response);

                if (response.output && response.output.valid) {
                    RESULTADOS_CUESTIONARIOS.renderVotantesNoRespondieron(response.output.response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar votantes que no respondieron:', status, error);
            }
        });
    },

    renderVotantesNoRespondieron: function(votantes) {
        // Destruir DataTable si existe
        if ($.fn.DataTable.isDataTable('#tabla_no_respondieron')) {
            $('#tabla_no_respondieron').DataTable().destroy();
        }

        const filas = (votantes || []).map(function(votante) {
            return {
                tipo: RESULTADOS_CUESTIONARIOS.renderTipoBadge(votante.tipo_registro),
                encuestado: '<strong>' + RESULTADOS_CUESTIONARIOS.escapeHtml(votante.nombre_completo || '-') + '</strong>',
                encuestador: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.encuestador_nombre_completo || '-'),
                email: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.email || '-'),
                username: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.username || '-'),
                genero: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.genero || '-'),
                edad: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.rango_edad || '-'),
                ideologia: RESULTADOS_CUESTIONARIOS.escapeHtml(votante.ideologia || '-'),
                estado: '<span class="badge badge-pendiente"><i class="fa-solid fa-clock me-1"></i>Pendiente</span>'
            };
        });

        $('#tabla_no_respondieron tbody').empty();

        $('#tabla_no_respondieron').DataTable({
            data: filas,
            columns: [
                { data: 'tipo' },
                { data: 'encuestado' },
                { data: 'encuestador' },
                { data: 'email' },
                { data: 'username' },
                { data: 'genero' },
                { data: 'edad' },
                { data: 'ideologia' },
                { data: 'estado' }
            ],
            searching: true,
            paging: true,
            pageLength: 25,
            lengthChange: false,
            info: false,
            ordering: true,
            order: [[0, 'asc']], // Ordenar por nombre ascendente
            columnDefs: [
                { targets: [0, 1, 8], orderable: false },
                { targets: '_all', defaultContent: '-' }
            ],
            language: {
                search: "Buscar:",
                emptyTable: "Todos los votantes han respondido",
                zeroRecords: "No se encontraron registros",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    },

    verDetalle: function(intentoId) {
        console.log('Ver detalle del intento:', intentoId);

        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'respuestadetalle',
                intento_id: intentoId
            },
            success: function(response) {
                console.log('Detalle de respuestas:', response);

                if (response.output && response.output.valid) {
                    RESULTADOS_CUESTIONARIOS.mostrarModalDetalle(response.output.response);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el detalle de las respuestas'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar detalle:', status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo conectar con el servidor'
                });
            }
        });
    },

    mostrarModalDetalle: function(respuestas) {
        let htmlContent = '<div class="respuestas-detalle">';

        // Agrupar respuestas por pregunta
        const preguntasMap = {};

        respuestas.forEach(function(resp) {
            if (!preguntasMap[resp.tbl_pregunta_id]) {
                preguntasMap[resp.tbl_pregunta_id] = {
                    texto_pregunta: resp.texto_pregunta,
                    tipo_pregunta: resp.tipo_pregunta,
                    opciones: [],
                    texto: null
                };
            }

            if (resp.texto_opcion) {
                preguntasMap[resp.tbl_pregunta_id].opciones.push(resp.texto_opcion);
            }

            if (resp.respuesta_texto) {
                preguntasMap[resp.tbl_pregunta_id].texto = resp.respuesta_texto;
            }
        });

        // Renderizar cada pregunta
        Object.values(preguntasMap).forEach(function(pregunta, index) {
            htmlContent += `
                <div class="mb-4 p-3 border rounded">
                    <h6 class="fw-bold mb-2">
                        <i class="fa-solid fa-question-circle me-2"></i>
                        Pregunta ${index + 1}:
                    </h6>
                    <p class="mb-2">${pregunta.texto_pregunta}</p>
                    <div class="respuesta-contenido p-3 bg-light rounded">
                        <strong>Respuesta:</strong><br>
            `;

            if (pregunta.opciones.length > 0) {
                htmlContent += '<ul class="mb-0 mt-2">';
                pregunta.opciones.forEach(function(opcion) {
                    htmlContent += `<li><i class="fa-solid fa-check-circle text-success me-2"></i>${opcion}</li>`;
                });
                htmlContent += '</ul>';
            }

            if (pregunta.texto) {
                htmlContent += `<p class="mt-2 mb-0"><em>"${pregunta.texto}"</em></p>`;
            }

            htmlContent += `
                    </div>
                </div>
            `;
        });

        htmlContent += '</div>';

        Swal.fire({
            title: 'Detalle de Respuestas',
            html: htmlContent,
            width: '800px',
            confirmButtonText: 'Cerrar',
            customClass: {
                container: 'detalle-respuestas-modal'
            }
        });
    }
};
