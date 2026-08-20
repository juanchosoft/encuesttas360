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
        $(document).on('click', '.btn-aplicar-filtros-listados', function() {
            RESULTADOS_CUESTIONARIOS.syncFiltrosFrom($(this));
            RESULTADOS_CUESTIONARIOS.refrescarListados();
        });
        $(document).on('click', '.btn-limpiar-filtros-listados', function() {
            $('.filtro-sync-tipo').val('');
            $('.filtro-sync-encuestador').val('');
            $('.filtro-sync-desde').val('');
            $('.filtro-sync-hasta').val('');
            RESULTADOS_CUESTIONARIOS.refrescarListados();
        });
        $(document).on('change', '.filtro-sync-tipo, .filtro-sync-encuestador, .filtro-sync-desde, .filtro-sync-hasta', function() {
            RESULTADOS_CUESTIONARIOS.syncFiltrosFrom($(this));
        });
        $(document).on('shown.bs.tab', '#votantesTabs button[data-bs-toggle="tab"]', function() {
            RESULTADOS_CUESTIONARIOS.ajustarTablasVisibles();
        });
    },

    syncFiltrosFrom: function($el) {
        var $root = $el.closest('.r-card-body, .row, form');
        if (!$root.length) {
            $root = $el.parent();
        }
        var tipo = $root.find('.filtro-sync-tipo').val();
        var enc = $root.find('.filtro-sync-encuestador').val();
        var desde = $root.find('.filtro-sync-desde').val();
        var hasta = $root.find('.filtro-sync-hasta').val();

        if (tipo === undefined) {
            tipo = $('.filtro-sync-tipo').first().val();
        }
        if (enc === undefined) {
            enc = $('.filtro-sync-encuestador').first().val();
        }
        if (desde === undefined) {
            desde = $('.filtro-sync-desde').first().val();
        }
        if (hasta === undefined) {
            hasta = $('.filtro-sync-hasta').first().val();
        }

        // Si el cambio viene de un control concreto, leer ese valor
        if ($el.hasClass('filtro-sync-tipo')) {
            tipo = $el.val();
        }
        if ($el.hasClass('filtro-sync-encuestador')) {
            enc = $el.val();
        }
        if ($el.hasClass('filtro-sync-desde')) {
            desde = $el.val();
        }
        if ($el.hasClass('filtro-sync-hasta')) {
            hasta = $el.val();
        }
        if ($el.hasClass('btn-aplicar-filtros-listados')) {
            var $box = $el.closest('.r-card-body');
            tipo = $box.find('.filtro-sync-tipo').val();
            enc = $box.find('.filtro-sync-encuestador').val();
            desde = $box.find('.filtro-sync-desde').val();
            hasta = $box.find('.filtro-sync-hasta').val();
        }

        $('.filtro-sync-tipo').val(tipo || '');
        $('.filtro-sync-encuestador').val(enc || '');
        $('.filtro-sync-desde').val(desde || '');
        $('.filtro-sync-hasta').val(hasta || '');
    },

    ajustarTablasVisibles: function() {
        ['#tabla_respondieron', '#tabla_no_respondieron', '#tabla_ultimas_respuestas'].forEach(function(sel) {
            if ($.fn.DataTable.isDataTable(sel)) {
                $(sel).DataTable().columns.adjust();
            }
        });
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

        // Tablas AJAX (sin LIMIT 10)
        RESULTADOS_CUESTIONARIOS.cargarFiltrosCatalogo();
        RESULTADOS_CUESTIONARIOS.initTablaUltimasRespuestas();
        RESULTADOS_CUESTIONARIOS.initTablaRespondieron();
        RESULTADOS_CUESTIONARIOS.initTablaNoRespondieron();
        setTimeout(function() {
            RESULTADOS_CUESTIONARIOS.ajustarTablasVisibles();
        }, 200);

        // Gráficas demográficas - con timeout para que el DOM sea visible
        if (typeof makeDonut === 'function') {
            setTimeout(function() {
                RESULTADOS_CUESTIONARIOS.renderDemograficas(stats);
            }, 150);
        }
    },

    getFiltrosComunes: function() {
        return {
            filtro_tipo: ($('.filtro-sync-tipo').first().val() || '').toString(),
            filtro_encuestador: ($('.filtro-sync-encuestador').first().val() || '').toString(),
            fecha_desde: ($('.filtro-sync-desde').first().val() || '').toString(),
            fecha_hasta: ($('.filtro-sync-hasta').first().val() || '').toString()
        };
    },

    formatFecha: function(valor) {
        if (!valor) {
            return '-';
        }
        const fecha = new Date(valor);
        if (isNaN(fecha.getTime())) {
            return RESULTADOS_CUESTIONARIOS.escapeHtml(valor);
        }
        return fecha.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    dtLanguage: function(emptyMsg) {
        return {
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_',
            infoEmpty: 'Sin registros',
            emptyTable: emptyMsg || 'Sin datos',
            zeroRecords: 'No se encontraron registros',
            processing: 'Cargando...',
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        };
    },

    ajaxDt: function(op) {
        return {
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            data: function(d) {
                const filtros = RESULTADOS_CUESTIONARIOS.getFiltrosComunes();
                d.op = op;
                d.ficha_tecnica_id = RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada;
                d.filtro_tipo = filtros.filtro_tipo;
                d.filtro_encuestador = filtros.filtro_encuestador;
                d.fecha_desde = filtros.fecha_desde;
                d.fecha_hasta = filtros.fecha_hasta;
                d.search_value = (d.search && d.search.value) ? d.search.value : '';
            },
            dataFilter: function(raw) {
                try {
                    const json = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    if (!json || !json.output || !json.output.valid || !json.output.response) {
                        const msg = (json && json.output && json.output.response && json.output.response.content)
                            ? json.output.response.content
                            : 'Respuesta inválida del servidor en listado';
                        console.error(msg, json);
                        return JSON.stringify({
                            draw: 1,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                    }
                    const r = json.output.response;
                    return JSON.stringify({
                        draw: r.draw || 1,
                        recordsTotal: r.recordsTotal || 0,
                        recordsFiltered: r.recordsFiltered || 0,
                        data: r.data || []
                    });
                } catch (e) {
                    console.error('Error parseando listado DataTables', e, raw);
                    return JSON.stringify({
                        draw: 1,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                }
            }
        };
    },

    cargarFiltrosCatalogo: function() {
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'cuestionariofiltrosdashboard',
                ficha_tecnica_id: RESULTADOS_CUESTIONARIOS.fichaTecnicaSeleccionada
            },
            success: function(response) {
                if (!(response.output && response.output.valid)) {
                    return;
                }
                const data = response.output.response || {};
                const tipos = data.tipos || [];
                const encuestadores = data.encuestadores || [];

                $('.filtro-sync-tipo').each(function() {
                    const $tipo = $(this);
                    const actual = $tipo.val();
                    $tipo.find('option:not(:first)').remove();
                    tipos.forEach(function(t) {
                        $tipo.append($('<option>').val(t).text(t));
                    });
                    if (actual) {
                        $tipo.val(actual);
                    }
                });

                $('.filtro-sync-encuestador').each(function() {
                    const $enc = $(this);
                    const actual = $enc.val();
                    $enc.find('option:not(:first)').remove();
                    encuestadores.forEach(function(e) {
                        $enc.append($('<option>').val(e).text(e));
                    });
                    if (actual) {
                        $enc.val(actual);
                    }
                });
            }
        });
    },

    refrescarListados: function() {
        ['#tabla_ultimas_respuestas', '#tabla_respondieron', '#tabla_no_respondieron'].forEach(function(sel) {
            if ($.fn.DataTable.isDataTable(sel)) {
                var dt = $(sel).DataTable();
                dt.ajax.reload(null, false);
                setTimeout(function() {
                    dt.columns.adjust();
                    if (dt.responsive && typeof dt.responsive.recalc === 'function') {
                        dt.responsive.recalc();
                    }
                }, 50);
            }
        });
    },

    destroyTabla: function(selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
            $(selector).find('tbody').empty();
        }
    },

    initTablaUltimasRespuestas: function() {
        RESULTADOS_CUESTIONARIOS.destroyTabla('#tabla_ultimas_respuestas');
        $('#tabla_ultimas_respuestas').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging: true,
            pageLength: 25,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            info: true,
            ordering: false,
            autoWidth: false,
            scrollX: false,
            ajax: RESULTADOS_CUESTIONARIOS.ajaxDt('cuestionarioultimasrespuestasdt'),
            columns: [
                {
                    data: 'tipo_registro',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.renderTipoBadge(data);
                    }
                },
                {
                    data: 'nombre_completo',
                    render: function(data) {
                        return '<strong>' + RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-') + '</strong>';
                    }
                },
                {
                    data: 'encuestador_nombre_completo',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'email',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'fecha_respuesta',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.formatFecha(data);
                    }
                },
                {
                    data: 'preguntas_respondidas',
                    render: function(data) {
                        return '<span class="badge bg-primary">' + RESULTADOS_CUESTIONARIOS.escapeHtml((data || 0) + ' preguntas') + '</span>';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return '<button class="btn btn-sm btn-info" onclick="RESULTADOS_CUESTIONARIOS.verDetalle(' + parseInt(data || 0, 10) + ')"><i class="fa-solid fa-eye me-1"></i>Ver detalle</button>';
                    }
                }
            ],
            language: RESULTADOS_CUESTIONARIOS.dtLanguage('No hay respuestas registradas aún')
        });
    },

    initTablaRespondieron: function() {
        RESULTADOS_CUESTIONARIOS.destroyTabla('#tabla_respondieron');
        $('#tabla_respondieron').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging: true,
            pageLength: 25,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            info: true,
            ordering: false,
            autoWidth: false,
            scrollX: false,
            ajax: RESULTADOS_CUESTIONARIOS.ajaxDt('votantesrespondierondt'),
            columns: [
                {
                    data: 'tipo_registro',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.renderTipoBadge(data);
                    }
                },
                {
                    data: 'nombre_completo',
                    render: function(data) {
                        return '<strong>' + RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-') + '</strong>';
                    }
                },
                {
                    data: 'encuestador_nombre_completo',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'email',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'genero',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'rango_edad',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'ideologia',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'fecha_respuesta',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.formatFecha(data);
                    }
                },
                {
                    data: 'preguntas_respondidas',
                    render: function(data) {
                        return '<span class="badge bg-success">' + RESULTADOS_CUESTIONARIOS.escapeHtml((data || 0) + ' preguntas') + '</span>';
                    }
                },
                {
                    data: 'intento_id',
                    orderable: false,
                    render: function(data) {
                        return '<button class="btn btn-sm btn-info" onclick="RESULTADOS_CUESTIONARIOS.verDetalle(' + parseInt(data || 0, 10) + ')"><i class="fa-solid fa-eye me-1"></i>Ver</button>';
                    }
                }
            ],
            language: RESULTADOS_CUESTIONARIOS.dtLanguage('Ningún votante ha respondido aún')
        });
    },

    initTablaNoRespondieron: function() {
        RESULTADOS_CUESTIONARIOS.destroyTabla('#tabla_no_respondieron');
        $('#tabla_no_respondieron').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            paging: true,
            pageLength: 25,
            lengthChange: true,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            info: true,
            ordering: false,
            autoWidth: false,
            scrollX: false,
            ajax: RESULTADOS_CUESTIONARIOS.ajaxDt('votantesnorespondierondt'),
            columns: [
                {
                    data: 'tipo_registro',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.renderTipoBadge(data);
                    }
                },
                {
                    data: 'nombre_completo',
                    render: function(data) {
                        return '<strong>' + RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-') + '</strong>';
                    }
                },
                {
                    data: 'encuestador_nombre_completo',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'email',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'username',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'genero',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'rango_edad',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: 'ideologia',
                    render: function(data) {
                        return RESULTADOS_CUESTIONARIOS.escapeHtml(data || '-');
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function() {
                        return '<span class="badge badge-pendiente"><i class="fa-solid fa-clock me-1"></i>Pendiente</span>';
                    }
                }
            ],
            language: RESULTADOS_CUESTIONARIOS.dtLanguage('Todos los votantes han respondido')
        });
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
