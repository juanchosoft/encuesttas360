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
    audioFormato: 'audio/webm', // Safari cambiará esto automáticamente si no es soportado
    intervaloTimer: null,
    _onStopPromiseResolver: null,

    init: function() {
        // Detectar formato soportado por el navegador (Safari prefiere mp4)
        if (MediaRecorder.isTypeSupported('audio/mp4')) {
            this.audioFormato = 'audio/mp4';
        } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
            this.audioFormato = 'audio/ogg';
        }

        $(document).on('click', '#btnIniciarCertificacion button', function(e) {
            e.preventDefault();
            CERTIFICACION_ENCUESTADOR.iniciarCertificacion();
        });

        $(document).on('click', '#btnDetenerCertificacion', function(e) {
            e.preventDefault();
            CERTIFICACION_ENCUESTADOR.detenerGrabacion();
        });

        CERTIFICACION_ENCUESTADOR.verificarCompatibilidad();
    },

    verificarCompatibilidad: function() {
        if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) $('#alertaAudioNoSoportado').removeClass('d-none');
        if (!navigator.geolocation) $('#alertaGeoNoSoportado').removeClass('d-none');
    },

    iniciarCertificacion: function() {
        CERTIFICACION_ENCUESTADOR.audioChunks = [];
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
            // Configuración para MediaRecorder compatible
            var options = { mimeType: CERTIFICACION_ENCUESTADOR.audioFormato };
            try {
                CERTIFICACION_ENCUESTADOR.mediaRecorder = new MediaRecorder(stream, options);
            } catch (e) {
                // Fallback si el mimeType falla en iPhone
                CERTIFICACION_ENCUESTADOR.mediaRecorder = new MediaRecorder(stream);
            }

            CERTIFICACION_ENCUESTADOR.mediaRecorder.ondataavailable = function(e) {
                if (e.data.size > 0) CERTIFICACION_ENCUESTADOR.audioChunks.push(e.data);
            };

            CERTIFICACION_ENCUESTADOR.mediaRecorder.onstop = function() {
                CERTIFICACION_ENCUESTADOR.procesarAudio().then(function(base64) {
                    if (CERTIFICACION_ENCUESTADOR._onStopPromiseResolver) {
                        CERTIFICACION_ENCUESTADOR._onStopPromiseResolver(base64);
                        CERTIFICACION_ENCUESTADOR._onStopPromiseResolver = null;
                    }
                });
            };

            CERTIFICACION_ENCUESTADOR.mediaRecorder.start(1000);
            CERTIFICACION_ENCUESTADOR.audioIniciado = true;
            $('#btnDetenerCertificacion').prop('disabled', false).removeClass('disabled');

            // DISPARAR EVENTO DE DESBLOQUEO
            $(document).trigger('certification-started');

            var seg = 0;
            CERTIFICACION_ENCUESTADOR.intervaloTimer = setInterval(function() {
                seg++;
                CERTIFICACION_ENCUESTADOR.audioDuracion = seg;
                $('#estatusAudio').html('<i class="fas fa-microphone text-danger me-2"></i>GRABANDO: ' + seg + 's');
            }, 1000);
        }).catch(function(err) {
            console.error("Error en iPhone:", err);
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
                resolve(null); 
            }
        });
    },

    procesarAudio: function() {
        return new Promise(function(resolve) {
            if (CERTIFICACION_ENCUESTADOR.audioChunks.length === 0) return resolve(null);
            
            CERTIFICACION_ENCUESTADOR.audioBlob = new Blob(CERTIFICACION_ENCUESTADOR.audioChunks, { type: CERTIFICACION_ENCUESTADOR.audioFormato });
            var reader = new FileReader();
            reader.onloadend = function() {
                $('#audioPreview').attr('src', URL.createObjectURL(CERTIFICACION_ENCUESTADOR.audioBlob));
                $('#audioPreviewContainer').removeClass('d-none');
                $('#estatusAudio').html('<i class="fas fa-check-circle text-success me-2"></i>Listo');
                resolve(reader.result);
            };
            reader.readAsDataURL(CERTIFICACION_ENCUESTADOR.audioBlob);
        });
    },

    obtenerDatosCertificacion: function(votanteId) {
        return new Promise(function(resolve) {
            if (!CERTIFICACION_ENCUESTADOR.audioBlob) return resolve(null);
            
            var reader = new FileReader();
            reader.onloadend = function() {
                resolve({
                    tbl_votante_id: votanteId,
                    latitud: CERTIFICACION_ENCUESTADOR.ubicacion?.latitud || 0,
                    longitud: CERTIFICACION_ENCUESTADOR.ubicacion?.longitud || 0,
                    audio_base64: reader.result,
                    audio_duracion_segundos: CERTIFICACION_ENCUESTADOR.audioDuracion,
                    audio_formato: CERTIFICACION_ENCUESTADOR.audioFormato,
                    dispositivo_info: navigator.userAgent
                });
            };
            reader.readAsDataURL(CERTIFICACION_ENCUESTADOR.audioBlob);
        });
    }
};

$(document).ready(function() { CERTIFICACION_ENCUESTADOR.init(); });