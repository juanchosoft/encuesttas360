var CONTESTAR_CUESTIONARIO = {
    fichaTecnicaId: 0,
    _def: null,
    _respuestas: {},
    _uiState: {
        filaActivaPorPregunta: {}
    },
    _clientUuid: null,
    _enviando: false,

    obtenerClientUuid: function () {
        if (!CONTESTAR_CUESTIONARIO._clientUuid) {
            if (window.crypto && typeof window.crypto.randomUUID === "function") {
                CONTESTAR_CUESTIONARIO._clientUuid = window.crypto.randomUUID();
            } else {
                CONTESTAR_CUESTIONARIO._clientUuid = "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
                    var r = (Math.random() * 16) | 0,
                        v = c === "x" ? r : (r & 0x3) | 0x8;
                    return v.toString(16);
                });
            }
        }
        return CONTESTAR_CUESTIONARIO._clientUuid;
    },

    init: function () {
        var fId = parseInt($('#ficha_tecnica_id').val() || $('#cuestionario_container').data('ficha-tecnica-id'), 10) || 0;
        CONTESTAR_CUESTIONARIO.fichaTecnicaId = fId;

        if (fId <= 0 || $('#form_cuestionario').length === 0) {
            return;
        }

        $('#form_cuestionario').on('submit', function (e) {
            e.preventDefault();
            CONTESTAR_CUESTIONARIO.enviarRespuestas();
        });

        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'cuestionariodefinicionget',
                ficha_id: fId
            },
            success: function (res) {
                if (res && res.output && res.output.valid && res.output.response) {
                    CONTESTAR_CUESTIONARIO._def = res.output.response;
                    $('#cuestionarioDefinicionHash').val(CONTESTAR_CUESTIONARIO._def.version || '');
                    CONTESTAR_CUESTIONARIO.cargarVotantesDisponibles();
                    CONTESTAR_CUESTIONARIO.vincularEventosMotor();
                    CONTESTAR_CUESTIONARIO.renderizarCuestionario();
                } else {
                    $('#preguntas_container').html(
                        '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>No se pudo cargar la definición del cuestionario.</div>'
                    );
                }
            },
            error: function () {
                $('#preguntas_container').html(
                    '<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Error de red al cargar el cuestionario.</div>'
                );
            }
        });
    },

    cargarVotantesDisponibles: function () {
        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'votantesdisponiblesget',
                ficha_tecnica_id: CONTESTAR_CUESTIONARIO.fichaTecnicaId
            },
            success: function (response) {
                if (response.output && response.output.valid) {
                    var votantes = response.output.response || [];
                    var options = '<option value="">Selecciona un votante...</option>';

                    if (votantes.length === 0) {
                        options = '<option value="">No hay votantes disponibles (todos han respondido)</option>';
                        $('#btnEnviarCuestionario').prop('disabled', true);
                    } else {
                        $('#btnEnviarCuestionario').prop('disabled', false);
                        votantes.forEach(function (v) {
                            var nom = (v.nombre_completo || v.username || ('Votante #' + v.id));
                            var mun = v.codigo_municipio || '';
                            var dep = v.codigo_departamento || '';
                            var com = v.comuna || '';
                            options += '<option value="' + v.id + '" data-municipio="' + mun + '" data-departamento="' + dep + '" data-comuna="' + com + '">' +
                                nom + (mun ? ' (' + mun + ')' : '') +
                                '</option>';
                        });
                    }

                    $('#tbl_votante_id').html(options);
                    CONTESTAR_CUESTIONARIO.actualizarTextoUbicacion();
                } else {
                    $('#tbl_votante_id').html('<option value="">Error al cargar votantes</option>');
                }
            },
            error: function () {
                $('#tbl_votante_id').html('<option value="">Error de conexión al cargar votantes</option>');
            }
        });
    },

    actualizarTextoUbicacion: function () {
        var $opt = $('#tbl_votante_id option:selected');
        var mun = $opt.data('municipio') || '';
        var dep = $opt.data('departamento') || '';
        var com = $opt.data('comuna') || '';

        if (!$opt.val()) {
            $('#textoUbicacionVotante').text('Seleccione un votante para ver su municipio').addClass('text-muted');
        } else if (mun || dep || com) {
            var txt = [];
            if (mun) txt.push('Municipio: ' + mun);
            if (dep) txt.push('Depto: ' + dep);
            if (com) txt.push('Zona/Comuna: ' + com);
            $('#textoUbicacionVotante').text(txt.join(' | ')).removeClass('text-muted');
        } else {
            $('#textoUbicacionVotante').text('Sin municipio registrado').addClass('text-muted');
        }
    },

    obtenerContextoMotor: function () {
        var $opt = $('#tbl_votante_id option:selected');
        var codMpio = ($opt.data('municipio') || '').toString().trim();
        var codDepto = ($opt.data('departamento') || '').toString().trim();
        var zonaTxt = ($opt.data('comuna') || '').toString().trim();
        return {
            codigo_municipio: codMpio,
            nombre_municipio: codMpio || 'su municipio',
            codigo_departamento: codDepto,
            zona_texto: zonaTxt
        };
    },

    obtenerArrayRespuestas: function () {
        var lista = [];
        Object.keys(CONTESTAR_CUESTIONARIO._respuestas).forEach(function (pidKey) {
            var item = CONTESTAR_CUESTIONARIO._respuestas[pidKey];
            if (!item) return;
            var grillaArr = [];
            if (item.grilla && typeof item.grilla === 'object') {
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
                texto: item.texto || '',
                grilla: grillaArr,
                lista: item.lista || null
            });
        });
        return lista;
    },

    asegurarEntradaPregunta: function (pid) {
        if (!CONTESTAR_CUESTIONARIO._respuestas[pid]) {
            CONTESTAR_CUESTIONARIO._respuestas[pid] = {
                pregunta_id: pid,
                opciones: [],
                texto: '',
                grilla: {},
                lista: null
            };
        }
        return CONTESTAR_CUESTIONARIO._respuestas[pid];
    },

    renderizarCuestionario: function () {
        if (!CONTESTAR_CUESTIONARIO._def || typeof MotorCuestionario === 'undefined' || typeof MotorCuestionarioUI === 'undefined') {
            return;
        }

        var ctx = CONTESTAR_CUESTIONARIO.obtenerContextoMotor();
        var evalState = MotorCuestionario.evaluar(CONTESTAR_CUESTIONARIO._def, CONTESTAR_CUESTIONARIO.obtenerArrayRespuestas(), ctx);
        var visibles = evalState.visibles || [];
        var preguntasById = {};
        (CONTESTAR_CUESTIONARIO._def.preguntas || []).forEach(function (p) {
            preguntasById[parseInt(p.id, 10)] = p;
        });

        var html = '';
        visibles.forEach(function (pid) {
            var pDef = preguntasById[pid];
            if (!pDef) return;
            var rPreg = CONTESTAR_CUESTIONARIO._respuestas[pid] || { pregunta_id: pid, opciones: [], texto: '', grilla: {}, lista: null };
            html += MotorCuestionarioUI.renderPreguntaCard(CONTESTAR_CUESTIONARIO._def, pDef, evalState, rPreg, ctx, CONTESTAR_CUESTIONARIO._uiState);
        });

        if (evalState.terminado) {
            html += MotorCuestionarioUI.renderCierreFiltro(
                evalState.mensaje_cierre,
                'onclick="CONTESTAR_CUESTIONARIO.enviarRespuestas();"'
            );
        }

        if (html === '') {
            html = '<div class="alert alert-secondary text-center py-4">No hay preguntas aplicables para el contexto actual.</div>';
        }

        $('#preguntas_container').html(html);

        var totalVisibles = visibles.length;
        var completadas = 0;
        visibles.forEach(function (pid) {
            if (evalState.siguiente_pregunta_id !== pid) {
                var rItem = CONTESTAR_CUESTIONARIO._respuestas[pid];
                if (rItem && ((rItem.opciones && rItem.opciones.length > 0) || (rItem.texto && rItem.texto.trim() !== '') || (rItem.lista && (rItem.lista.fila_id || rItem.lista.opcion_id)) || (rItem.grilla && Object.keys(rItem.grilla).length > 0))) {
                    completadas++;
                }
            }
        });
        if (evalState.terminado) {
            completadas = totalVisibles;
        }
        var pct = totalVisibles > 0 ? Math.min(100, Math.round((completadas / totalVisibles) * 100)) : 0;
        $('#progress_bar').css('width', pct + '%');
        $('#progreso_texto').text(
            evalState.terminado
                ? 'Finalizada por filtro (' + completadas + ' de ' + totalVisibles + ')'
                : (completadas + ' de ' + totalVisibles + ' respondidas (' + pct + '%)')
        );
    },

    vincularEventosMotor: function () {
        $(document).off('.ccMotor');

        $(document).on('change.ccMotor', '#tbl_votante_id', function () {
            CONTESTAR_CUESTIONARIO.actualizarTextoUbicacion();
            CONTESTAR_CUESTIONARIO.renderizarCuestionario();
        });

        $(document).on('click.ccMotor', '.cq-normal-opt-btn', function () {
            var $btn = $(this);
            var pid = parseInt($btn.data('pregunta'), 10);
            var oid = parseInt($btn.data('opcion'), 10);
            var esMult = String($btn.data('multiple')) === '1';
            var limite = parseInt($btn.data('limite') || 1, 10);

            var entry = CONTESTAR_CUESTIONARIO.asegurarEntradaPregunta(pid);
            if (!esMult) {
                entry.opciones = [oid];
            } else {
                var idx = entry.opciones.indexOf(oid);
                if (idx !== -1) {
                    entry.opciones.splice(idx, 1);
                } else {
                    if (entry.opciones.length >= limite) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Límite alcanzado',
                            text: 'Puede elegir máximo ' + limite + ' opciones en esta pregunta.',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                    entry.opciones.push(oid);
                }
            }
            CONTESTAR_CUESTIONARIO.renderizarCuestionario();
        });

        $(document).on('click.ccMotor', '.cq-lista-opt-btn', function () {
            var $btn = $(this);
            var pid = parseInt($btn.data('pregunta'), 10);
            var tipoItem = String($btn.data('tipo-item') || '');
            var idVal = parseInt($btn.data('id'), 10);

            var entry = CONTESTAR_CUESTIONARIO.asegurarEntradaPregunta(pid);
            if (tipoItem === 'fila') {
                entry.lista = { fila_id: idVal, opcion_id: null };
                entry.opciones = [];
            } else {
                entry.lista = { fila_id: null, opcion_id: idVal };
                entry.opciones = [idVal];
            }
            CONTESTAR_CUESTIONARIO.renderizarCuestionario();
        });

        $(document).on('click.ccMotor', '.cq-grilla-opt-btn', function () {
            var $btn = $(this);
            var pid = parseInt($btn.data('pregunta'), 10);
            var fid = parseInt($btn.data('fila'), 10);
            var cid = parseInt($btn.data('columna'), 10);
            var coId = parseInt($btn.data('opcion'), 10);
            var esMult = String($btn.data('multiple')) === '1';
            var limite = parseInt($btn.data('limite') || 1, 10);

            var entry = CONTESTAR_CUESTIONARIO.asegurarEntradaPregunta(pid);
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
                        Swal.fire({
                            icon: 'warning',
                            title: 'Límite alcanzado',
                            text: 'Puede escoger máximo ' + limite + ' opciones en esta columna.',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                    arr.push(coId);
                }
            }
            CONTESTAR_CUESTIONARIO.renderizarCuestionario();
        });

        $(document).on('click.ccMotor', '.cq-grilla-fila-step-btn', function () {
            var $btn = $(this);
            var pid = parseInt($btn.data('pregunta'), 10);
            var step = parseInt($btn.data('step'), 10);
            var total = parseInt($btn.data('total'), 10);
            var cur = CONTESTAR_CUESTIONARIO._uiState.filaActivaPorPregunta[pid] || 0;
            var next = cur + step;
            if (next >= 0 && next < total) {
                CONTESTAR_CUESTIONARIO._uiState.filaActivaPorPregunta[pid] = next;
                CONTESTAR_CUESTIONARIO.renderizarCuestionario();
            }
        });

        $(document).on('change.ccMotor keyup.ccMotor', '.cq-texto-input', function () {
            var $txt = $(this);
            var pid = parseInt($txt.data('pregunta'), 10);
            var entry = CONTESTAR_CUESTIONARIO.asegurarEntradaPregunta(pid);
            entry.texto = $txt.val();
        });
    },

    enviarRespuestas: function () {
        if (CONTESTAR_CUESTIONARIO._enviando) {
            return;
        }

        var votanteId = parseInt($('#tbl_votante_id').val(), 10) || 0;
        if (votanteId <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Votante no seleccionado',
                text: 'Por favor, selecciona un votante antes de enviar las respuestas.',
                confirmButtonText: 'Entendido'
            });
            $('#tbl_votante_id').focus();
            return;
        }

        if (!CONTESTAR_CUESTIONARIO._def || typeof MotorCuestionario === 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de cuestionario',
                text: 'No se pudo cargar la definición del cuestionario.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        var ctx = CONTESTAR_CUESTIONARIO.obtenerContextoMotor();
        var valRes = MotorCuestionario.validar(
            CONTESTAR_CUESTIONARIO._def,
            CONTESTAR_CUESTIONARIO.obtenerArrayRespuestas(),
            ctx
        );

        if (!valRes.valido && valRes.errores && valRes.errores.length > 0) {
            var primerError = valRes.errores[0].mensaje;
            var errPid = valRes.errores[0].pregunta_id;

            Swal.fire({
                icon: 'warning',
                title: 'Respuesta pendiente',
                text: primerError,
                confirmButtonText: 'Revisar'
            }).then(function () {
                if (errPid) {
                    var $card = $('.cq-pregunta-card[data-pregunta-id="' + errPid + '"]');
                    if ($card.length) {
                        $card.css('border-color', '#dc3545');
                        $('html, body').animate({ scrollTop: Math.max(0, $card.offset().top - 80) }, 300);
                    }
                }
            });
            return;
        }

        CONTESTAR_CUESTIONARIO._enviando = true;
        $('#btnEnviarCuestionario').prop('disabled', true);

        Swal.fire({
            title: 'Guardando respuestas...',
            text: 'Por favor espere mientras se registran las respuestas en el sistema.',
            allowOutsideClick: false,
            didOpen: function () {
                Swal.showLoading();
            }
        });

        var payload = {
            ficha_tecnica_id: CONTESTAR_CUESTIONARIO.fichaTecnicaId,
            tbl_votante_id: votanteId,
            client_uuid: CONTESTAR_CUESTIONARIO.obtenerClientUuid(),
            definicion_hash: CONTESTAR_CUESTIONARIO._def.version || '',
            codigo_departamento: ctx.codigo_departamento,
            codigo_municipio: ctx.codigo_municipio,
            zona_texto: ctx.zona_texto,
            respuestas: CONTESTAR_CUESTIONARIO.obtenerArrayRespuestas()
        };

        $.ajax({
            url: 'admin/ajax/rqst.php',
            type: 'POST',
            dataType: 'json',
            data: {
                op: 'respuestasave',
                data: JSON.stringify(payload)
            },
            success: function (response) {
                CONTESTAR_CUESTIONARIO._enviando = false;
                $('#btnEnviarCuestionario').prop('disabled', false);

                if (response.output && response.output.valid) {
                    var msgExito = 'Tus respuestas han sido guardadas exitosamente.';
                    if (response.output.estado_intento === 'terminado_filtro') {
                        msgExito = 'La encuesta finalizó por la pregunta filtro. El registro quedó asentado correctamente.';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Respuestas enviadas!',
                        text: msgExito,
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false
                    }).then(function () {
                        CONTESTAR_CUESTIONARIO._respuestas = {};
                        CONTESTAR_CUESTIONARIO._uiState.filaActivaPorPregunta = {};
                        CONTESTAR_CUESTIONARIO._clientUuid = null;
                        $('#progress_bar').css('width', '0%');
                        $('#progreso_texto').text('0%');
                        CONTESTAR_CUESTIONARIO.cargarVotantesDisponibles();
                        CONTESTAR_CUESTIONARIO.renderizarCuestionario();
                    });
                } else {
                    var errMsg = (response.output && response.output.message)
                        ? response.output.message
                        : 'Ocurrió un error al enviar las respuestas. Por favor, intenta de nuevo.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al enviar',
                        text: errMsg,
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            error: function (xhr, status, error) {
                CONTESTAR_CUESTIONARIO._enviando = false;
                $('#btnEnviarCuestionario').prop('disabled', false);
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

$(document).ready(function () {
    CONTESTAR_CUESTIONARIO.init();
});
