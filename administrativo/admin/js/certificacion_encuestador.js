/**
 * Módulo de Certificación - Versión iOS Compatible (COMPLETO)
 */
var CERTIFICACION_ENCUESTADOR = {
    mediaRecorder: null,
    audioChunks: [],
    audioBlob: null,
    audioDuracion: 0,
    audioIniciado: false,
    ubicacion: null,
    audioFormato: 'audio/webm',
    intervaloTimer: null,
    _onStopPromiseResolver: null,
    wakeLockSentinel: null,
    idbName: 'encuestas360_audio_recovery',
    idbStore: 'audio_chunks',
    idbMeta: 'audio_meta',
    _idbPromesa: null,

    init: function() {
        if (typeof MediaRecorder !== 'undefined') {
            if (MediaRecorder.isTypeSupported('audio/mp4')) {
                this.audioFormato = 'audio/mp4';
            } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
                this.audioFormato = 'audio/ogg';
            }
        }

        $(document).on('click', '#btnIniciarCertificacion button', function(e) {
            e.preventDefault();
            CERTIFICACION_ENCUESTADOR.iniciarCertificacion();
        });

        $(document).on('click', '#btnDetenerCertificacion', function(e) {
            e.preventDefault();
            CERTIFICACION_ENCUESTADOR.detenerGrabacion();
        });

        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible' && CERTIFICACION_ENCUESTADOR.audioIniciado && CERTIFICACION_ENCUESTADOR.mediaRecorder && CERTIFICACION_ENCUESTADOR.mediaRecorder.state === 'recording') {
                CERTIFICACION_ENCUESTADOR.solicitarWakeLock();
            }
        });

        CERTIFICACION_ENCUESTADOR.verificarCompatibilidad();
        CERTIFICACION_ENCUESTADOR.mostrarAudioPendiente();
    },

    verificarCompatibilidad: function() {
        if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) $('#alertaAudioNoSoportado').removeClass('d-none');
        if (!navigator.geolocation) $('#alertaGeoNoSoportado').removeClass('d-none');
    },

    abrirIdb: function() {
        if (CERTIFICACION_ENCUESTADOR._idbPromesa) return CERTIFICACION_ENCUESTADOR._idbPromesa;
        CERTIFICACION_ENCUESTADOR._idbPromesa = new Promise(function(resolve) {
            if (typeof indexedDB === 'undefined') return resolve(null);
            try {
                var req = indexedDB.open(CERTIFICACION_ENCUESTADOR.idbName, 2);
                req.onupgradeneeded = function(e) {
                    var db = e.target.result;
                    if (!db.objectStoreNames.contains(CERTIFICACION_ENCUESTADOR.idbStore)) {
                        db.createObjectStore(CERTIFICACION_ENCUESTADOR.idbStore, { autoIncrement: true });
                    }
                    if (!db.objectStoreNames.contains(CERTIFICACION_ENCUESTADOR.idbMeta)) {
                        db.createObjectStore(CERTIFICACION_ENCUESTADOR.idbMeta);
                    }
                };
                req.onsuccess = function(e) { resolve(e.target.result); };
                req.onerror = function() { resolve(null); };
                req.onblocked = function() { resolve(null); };
            } catch (err) {
                resolve(null);
            }
        });
        return CERTIFICACION_ENCUESTADOR._idbPromesa;
    },

    guardarMetaIdb: function(meta) {
        return CERTIFICACION_ENCUESTADOR.abrirIdb().then(function(db) {
            if (!db) return;
            try {
                db.transaction([CERTIFICACION_ENCUESTADOR.idbMeta], 'readwrite')
                    .objectStore(CERTIFICACION_ENCUESTADOR.idbMeta).put(meta, 'pendiente');
            } catch (e) {}
        });
    },

    leerMetaIdb: function() {
        return CERTIFICACION_ENCUESTADOR.abrirIdb().then(function(db) {
            if (!db) return null;
            return new Promise(function(resolve) {
                try {
                    var req = db.transaction([CERTIFICACION_ENCUESTADOR.idbMeta], 'readonly')
                        .objectStore(CERTIFICACION_ENCUESTADOR.idbMeta).get('pendiente');
                    req.onsuccess = function() { resolve(req.result || null); };
                    req.onerror = function() { resolve(null); };
                } catch (e) {
                    resolve(null);
                }
            });
        });
    },

    mostrarAudioPendiente: function() {
        return CERTIFICACION_ENCUESTADOR.leerMetaIdb().then(function(meta) {
            if (!meta || !(parseInt(meta.tbl_votante_id, 10) > 0)) return;
            return CERTIFICACION_ENCUESTADOR.recuperarChunksPrevios().then(function(blob) {
                if (!blob || !blob.size) return;
                if ($('#audioPendienteAviso').length) return;
                var $aviso = $(
                    '<div id="audioPendienteAviso" class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert" style="position:fixed;left:12px;right:12px;bottom:12px;z-index:2000;font-size:1.05rem;">' +
                        '<span><i class="fas fa-exclamation-triangle me-2"></i>Hay un audio de una encuesta anterior que no se alcanzó a subir.</span>' +
                        '<button type="button" class="btn btn-primary btn-lg" id="btnSubirAudioPendiente"><i class="fas fa-cloud-upload-alt me-2"></i>Reintentar subir audio</button>' +
                    '</div>'
                );
                $('body').append($aviso);
                $('#btnSubirAudioPendiente').on('click', function() {
                    var $b = $(this).prop('disabled', true).text('Subiendo...');
                    CERTIFICACION_ENCUESTADOR.audioDuracion = parseInt(meta.duracion, 10) || CERTIFICACION_ENCUESTADOR.audioDuracion;
                    CERTIFICACION_ENCUESTADOR.enviarCertificacion({
                        tbl_votante_id: meta.tbl_votante_id,
                        intento_id: meta.intento_id,
                        origen_tipo: meta.origen_tipo || 'cuestionario',
                        tbl_ficha_tecnica_encuesta_id: meta.tbl_ficha_tecnica_encuesta_id
                    }).then(function(data) {
                        if (data && data.output && data.output.valid) {
                            $aviso.remove();
                            if (typeof UTIL !== 'undefined' && UTIL.mostrarMensajeExitoso) UTIL.mostrarMensajeExitoso('Audio subido correctamente');
                        } else {
                            $b.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-2"></i>Reintentar subir audio');
                            if (typeof UTIL !== 'undefined' && UTIL.mostrarMensajeError) UTIL.mostrarMensajeError('No se pudo subir el audio. Revise la conexión e intente de nuevo.');
                        }
                    }).catch(function() {
                        $b.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-2"></i>Reintentar subir audio');
                        if (typeof UTIL !== 'undefined' && UTIL.mostrarMensajeError) UTIL.mostrarMensajeError('No se pudo subir el audio. Revise la conexión e intente de nuevo.');
                    });
                });
            });
        });
    },

    guardarChunkIdb: function(chunkBlob) {
        return CERTIFICACION_ENCUESTADOR.abrirIdb().then(function(db) {
            if (!db) return;
            try {
                var tx = db.transaction([CERTIFICACION_ENCUESTADOR.idbStore], 'readwrite');
                tx.objectStore(CERTIFICACION_ENCUESTADOR.idbStore).add({
                    blob: chunkBlob,
                    mime: CERTIFICACION_ENCUESTADOR.audioFormato,
                    ts: Date.now()
                });
            } catch (e) {}
        });
    },

    limpiarChunksIdb: function() {
        return CERTIFICACION_ENCUESTADOR.abrirIdb().then(function(db) {
            if (!db) return;
            try {
                var tx = db.transaction([CERTIFICACION_ENCUESTADOR.idbStore, CERTIFICACION_ENCUESTADOR.idbMeta], 'readwrite');
                tx.objectStore(CERTIFICACION_ENCUESTADOR.idbStore).clear();
                tx.objectStore(CERTIFICACION_ENCUESTADOR.idbMeta).clear();
            } catch (e) {}
        });
    },

    recuperarChunksPrevios: function() {
        return CERTIFICACION_ENCUESTADOR.abrirIdb().then(function(db) {
            if (!db) return null;
            return new Promise(function(resolve) {
                try {
                    var tx = db.transaction([CERTIFICACION_ENCUESTADOR.idbStore], 'readonly');
                    var req = tx.objectStore(CERTIFICACION_ENCUESTADOR.idbStore).getAll();
                    req.onsuccess = function() {
                        var rows = req.result || [];
                        if (rows.length > 0 && !CERTIFICACION_ENCUESTADOR.audioBlob && CERTIFICACION_ENCUESTADOR.audioChunks.length === 0) {
                            var blobs = [];
                            var mime = CERTIFICACION_ENCUESTADOR.audioFormato;
                            rows.forEach(function(r) {
                                if (r && r.blob) {
                                    blobs.push(r.blob);
                                    if (r.mime) mime = r.mime;
                                }
                            });
                            if (blobs.length > 0) {
                                CERTIFICACION_ENCUESTADOR.audioChunks = blobs;
                                CERTIFICACION_ENCUESTADOR.audioFormato = mime;
                                CERTIFICACION_ENCUESTADOR.audioBlob = new Blob(blobs, { type: mime });
                                CERTIFICACION_ENCUESTADOR.audioDuracion = Math.max(CERTIFICACION_ENCUESTADOR.audioDuracion, blobs.length * 2);
                            }
                        }
                        resolve(CERTIFICACION_ENCUESTADOR.audioBlob);
                    };
                    req.onerror = function() { resolve(null); };
                } catch (e) {
                    resolve(null);
                }
            });
        });
    },

    solicitarWakeLock: function() {
        if (navigator.wakeLock && typeof navigator.wakeLock.request === 'function') {
            navigator.wakeLock.request('screen').then(function(sentinel) {
                CERTIFICACION_ENCUESTADOR.wakeLockSentinel = sentinel;
            }).catch(function() {});
        }
    },

    liberarWakeLock: function() {
        if (CERTIFICACION_ENCUESTADOR.wakeLockSentinel && typeof CERTIFICACION_ENCUESTADOR.wakeLockSentinel.release === 'function') {
            CERTIFICACION_ENCUESTADOR.wakeLockSentinel.release().catch(function() {});
            CERTIFICACION_ENCUESTADOR.wakeLockSentinel = null;
        }
    },

    iniciarCertificacion: function() {
        if ($('#audioPendienteAviso').length && typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hay un audio sin subir',
                text: 'Si empieza otra encuesta ahora, el audio anterior se perderá. Primero toque «Reintentar subir audio».',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Empezar de todos modos',
                cancelButtonText: 'Volver',
                reverseButtons: true
            }).then(function(r) {
                if (r.value) {
                    $('#audioPendienteAviso').remove();
                    CERTIFICACION_ENCUESTADOR.comenzarCertificacion();
                }
            });
            return;
        }
        CERTIFICACION_ENCUESTADOR.comenzarCertificacion();
    },

    comenzarCertificacion: function() {
        CERTIFICACION_ENCUESTADOR.audioChunks = [];
        CERTIFICACION_ENCUESTADOR.audioBlob = null;
        CERTIFICACION_ENCUESTADOR.limpiarChunksIdb();
        $('#panelCertificacion').removeClass('d-none');
        $('#btnIniciarCertificacion').addClass('d-none');
        CERTIFICACION_ENCUESTADOR.obtenerUbicacion();
        CERTIFICACION_ENCUESTADOR.iniciarGrabacion();
    },

    obtenerUbicacion: function() {
        $('#estatusUbicacion').html('<i class="fas fa-spinner fa-spin me-2"></i>Obteniendo GPS...');
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                CERTIFICACION_ENCUESTADOR.ubicacion = { latitud: pos.coords.latitude, longitud: pos.coords.longitude, precision: pos.coords.accuracy };
                $('#estatusUbicacion').html('<i class="fas fa-check-circle text-success me-2"></i>GPS OK');
            },
            function() { $('#estatusUbicacion').html('<i class="fas fa-times-circle text-danger me-2"></i>Error GPS'); },
            { enableHighAccuracy: true, timeout: 15000 }
        );
    },

    iniciarGrabacion: function() {
        $('#estatusAudio').html('<i class="fas fa-spinner fa-spin me-2"></i>Permiso micro...');

        navigator.mediaDevices.getUserMedia({ audio: true }).then(function(stream) {
            var options = { mimeType: CERTIFICACION_ENCUESTADOR.audioFormato, audioBitsPerSecond: 24000 };
            try {
                CERTIFICACION_ENCUESTADOR.mediaRecorder = new MediaRecorder(stream, options);
            } catch (e) {
                try {
                    CERTIFICACION_ENCUESTADOR.mediaRecorder = new MediaRecorder(stream, { audioBitsPerSecond: 24000 });
                } catch (e2) {
                    CERTIFICACION_ENCUESTADOR.mediaRecorder = new MediaRecorder(stream);
                }
            }

            CERTIFICACION_ENCUESTADOR.solicitarWakeLock();

            CERTIFICACION_ENCUESTADOR.mediaRecorder.ondataavailable = function(e) {
                if (e.data && e.data.size > 0) {
                    CERTIFICACION_ENCUESTADOR.audioChunks.push(e.data);
                    CERTIFICACION_ENCUESTADOR.guardarChunkIdb(e.data);
                }
            };

            CERTIFICACION_ENCUESTADOR.mediaRecorder.onstop = function() {
                CERTIFICACION_ENCUESTADOR.liberarWakeLock();
                CERTIFICACION_ENCUESTADOR.procesarAudio().then(function(blob) {
                    if (CERTIFICACION_ENCUESTADOR._onStopPromiseResolver) {
                        CERTIFICACION_ENCUESTADOR._onStopPromiseResolver(blob);
                        CERTIFICACION_ENCUESTADOR._onStopPromiseResolver = null;
                    }
                });
            };

            CERTIFICACION_ENCUESTADOR.mediaRecorder.start(2000);
            CERTIFICACION_ENCUESTADOR.audioIniciado = true;
            $('#btnDetenerCertificacion').prop('disabled', false).removeClass('disabled');

            $(document).trigger('certification-started');

            var seg = 0;
            CERTIFICACION_ENCUESTADOR.intervaloTimer = setInterval(function() {
                seg++;
                CERTIFICACION_ENCUESTADOR.audioDuracion = seg;
                $('#estatusAudio').html('<i class="fas fa-microphone text-danger me-2"></i>GRABANDO: ' + seg + 's <span class="small text-muted ms-1">(No bloquee la pantalla)</span>');
            }, 1000);
        }).catch(function(err) {
            console.error("Error de micrófono:", err);
            $('#estatusAudio').html('<i class="fas fa-times-circle text-danger me-2"></i>Micro denegado');
        });
    },

    detenerGrabacion: function() {
        return new Promise(function(resolve) {
            if (CERTIFICACION_ENCUESTADOR.mediaRecorder && CERTIFICACION_ENCUESTADOR.mediaRecorder.state !== 'inactive') {
                CERTIFICACION_ENCUESTADOR._onStopPromiseResolver = resolve;
                CERTIFICACION_ENCUESTADOR.mediaRecorder.stop();
                clearInterval(CERTIFICACION_ENCUESTADOR.intervaloTimer);
                CERTIFICACION_ENCUESTADOR.mediaRecorder.stream.getTracks().forEach(function(t) { t.stop(); });
            } else {
                CERTIFICACION_ENCUESTADOR.procesarAudio().then(resolve);
            }
        });
    },

    procesarAudio: function() {
        return new Promise(function(resolve) {
            if (CERTIFICACION_ENCUESTADOR.audioChunks.length === 0) {
                if (CERTIFICACION_ENCUESTADOR.audioBlob) return resolve(CERTIFICACION_ENCUESTADOR.audioBlob);
                return CERTIFICACION_ENCUESTADOR.recuperarChunksPrevios().then(resolve);
            }

            CERTIFICACION_ENCUESTADOR.audioBlob = new Blob(CERTIFICACION_ENCUESTADOR.audioChunks, { type: CERTIFICACION_ENCUESTADOR.audioFormato });
            try {
                $('#audioPreview').attr('src', URL.createObjectURL(CERTIFICACION_ENCUESTADOR.audioBlob));
                $('#audioPreviewContainer').removeClass('d-none');
                CERTIFICACION_ENCUESTADOR.enlazarReproductor();
                $('#estatusAudio').html('<i class="fas fa-check-circle text-success me-2"></i>Listo');
            } catch (e) {}
            resolve(CERTIFICACION_ENCUESTADOR.audioBlob);
        });
    },

    enlazarReproductor: function() {
        var audio = document.getElementById('audioPreview');
        var $btn = $('#btnAudioPreview');
        if (!audio || $btn.data('enlazado')) return;
        $btn.data('enlazado', true);
        var pintar = function(reproduciendo) {
            $btn.find('i').attr('class', 'fas me-2 ' + (reproduciendo ? 'fa-pause' : 'fa-play'));
            $btn.find('span').text(reproduciendo ? 'Pausar' : 'Reproducir');
        };
        $btn.on('click', function() {
            if (audio.paused) { audio.play(); } else { audio.pause(); }
        });
        audio.addEventListener('play', function() { pintar(true); });
        audio.addEventListener('pause', function() { pintar(false); });
        audio.addEventListener('ended', function() { pintar(false); });
    },

    generarUuid: function() {
        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
            return window.crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    extensionPorMime: function(mime) {
        var m = String(mime || '').toLowerCase();
        if (m.indexOf('mp4') !== -1 || m.indexOf('m4a') !== -1) return 'mp4';
        if (m.indexOf('ogg') !== -1) return 'ogg';
        if (m.indexOf('wav') !== -1) return 'wav';
        if (m.indexOf('mpeg') !== -1 || m.indexOf('mp3') !== -1) return 'mp3';
        return 'webm';
    },

    subirPartesGrandes: function(blob, onProgress) {
        var maxPartBytes = 10 * 1024 * 1024;
        var totalParts = Math.ceil(blob.size / maxPartBytes);
        var uploadUuid = CERTIFICACION_ENCUESTADOR.generarUuid();
        var ext = CERTIFICACION_ENCUESTADOR.extensionPorMime(CERTIFICACION_ENCUESTADOR.audioFormato);

        var subirIndice = function(idx) {
            if (idx >= totalParts) {
                return Promise.resolve({ upload_uuid: uploadUuid, total_parts: totalParts });
            }
            var start = idx * maxPartBytes;
            var end = Math.min(blob.size, start + maxPartBytes);
            var slice = blob.slice(start, end, CERTIFICACION_ENCUESTADOR.audioFormato);
            var fd = new FormData();
            fd.append('op', 'certificacionaudioparte');
            fd.append('upload_uuid', uploadUuid);
            fd.append('part_index', String(idx));
            fd.append('total_parts', String(totalParts));
            fd.append('audio_formato', CERTIFICACION_ENCUESTADOR.audioFormato);
            fd.append('audio_part', slice, 'part_' + idx + '.' + ext);

            if (typeof onProgress === 'function') {
                onProgress(idx + 1, totalParts);
            }

            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: 'admin/ajax/rqst.php',
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    timeout: 180000,
                    success: function(resp) {
                        if (resp && resp.output && resp.output.valid) {
                            resolve();
                        } else {
                            var msg = (resp && resp.output && resp.output.response && resp.output.response.content)
                                ? resp.output.response.content
                                : 'No se pudo subir la parte ' + (idx + 1) + ' del audio.';
                            reject(new Error(msg));
                        }
                    },
                    error: function() {
                        reject(new Error('Error de red al subir la parte ' + (idx + 1) + ' del audio.'));
                    }
                });
            }).then(function() {
                return subirIndice(idx + 1);
            });
        };

        return subirIndice(0);
    },

    enviarCertificacion: function(params, onProgress) {
        return CERTIFICACION_ENCUESTADOR.procesarAudio().then(function(blob) {
            var prepararEnvio = Promise.resolve(null);
            var LimiteUnicoBytes = 18 * 1024 * 1024;
            if (blob && blob.size > LimiteUnicoBytes) {
                prepararEnvio = CERTIFICACION_ENCUESTADOR.subirPartesGrandes(blob, onProgress);
            }

            return prepararEnvio.then(function(infoPartes) {
                var fd = new FormData();
                fd.append('op', 'certificacionsave');
                fd.append('tbl_votante_id', String(params.tbl_votante_id || 0));
                if (params.intento_id) {
                    fd.append('intento_id', String(params.intento_id));
                }
                fd.append('origen_tipo', String(params.origen_tipo || 'registro_simple'));
                if (params.tbl_sondeo_id) {
                    fd.append('tbl_sondeo_id', String(params.tbl_sondeo_id));
                }
                if (params.tbl_ficha_tecnica_encuesta_id) {
                    fd.append('tbl_ficha_tecnica_encuesta_id', String(params.tbl_ficha_tecnica_encuesta_id));
                }
                fd.append('latitud', String(CERTIFICACION_ENCUESTADOR.ubicacion?.latitud || 0));
                fd.append('longitud', String(CERTIFICACION_ENCUESTADOR.ubicacion?.longitud || 0));
                fd.append('precision_metros', String(CERTIFICACION_ENCUESTADOR.ubicacion?.precision || 0));
                fd.append('audio_duracion_segundos', String(CERTIFICACION_ENCUESTADOR.audioDuracion || 0));
                fd.append('audio_formato', String(CERTIFICACION_ENCUESTADOR.audioFormato || 'audio/webm'));
                fd.append('dispositivo_info', JSON.stringify({
                    plataforma: navigator.platform || '',
                    idioma: navigator.language || '',
                    pantalla: { ancho: window.screen?.width || 0, alto: window.screen?.height || 0 },
                    userAgent: navigator.userAgent || ''
                }));

                if (infoPartes && infoPartes.upload_uuid) {
                    fd.append('upload_uuid', infoPartes.upload_uuid);
                    fd.append('total_parts', String(infoPartes.total_parts));
                } else if (blob && blob.size > 0) {
                    var ext = CERTIFICACION_ENCUESTADOR.extensionPorMime(CERTIFICACION_ENCUESTADOR.audioFormato);
                    fd.append('audio_file', blob, 'certificacion.' + ext);
                }

                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: 'admin/ajax/rqst.php',
                        type: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        timeout: 180000,
                        success: function(data) {
                            if (data && data.output && data.output.valid) {
                                CERTIFICACION_ENCUESTADOR.limpiarChunksIdb();
                            }
                            resolve(data);
                        },
                        error: function(xhr) {
                            reject(xhr);
                        }
                    });
                });
            });
        });
    },

    obtenerDatosCertificacion: function(votanteId) {
        return CERTIFICACION_ENCUESTADOR.procesarAudio().then(function(blob) {
            if (!blob) return null;
            return {
                tbl_votante_id: votanteId,
                latitud: CERTIFICACION_ENCUESTADOR.ubicacion?.latitud || 0,
                longitud: CERTIFICACION_ENCUESTADOR.ubicacion?.longitud || 0,
                audio_duracion_segundos: CERTIFICACION_ENCUESTADOR.audioDuracion,
                audio_formato: CERTIFICACION_ENCUESTADOR.audioFormato,
                dispositivo_info: navigator.userAgent
            };
        });
    }
};

$(function() { CERTIFICACION_ENCUESTADOR.init(); });