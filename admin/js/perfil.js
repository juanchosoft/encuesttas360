var PERFIL = {
    getProfileId: function() {
        const fromInput = parseInt($('#idVotantes_perfil').val(), 10);
        if (fromInput > 0) return fromInput;

        const fromModal = parseInt($('#perfilModal').data('profile-id'), 10);
        return fromModal > 0 ? fromModal : 0;
    },

    getMessage: function(value, fallback) {
        if (typeof value === 'string' && value.trim() !== '') return value;
        if (value && typeof value === 'object') {
            if (typeof value.content === 'string') return value.content;
            if (typeof value.message === 'string') return value.message;
            if (typeof value.response === 'string') return value.response;
        }
        return fallback || 'Ocurrió un error inesperado.';
    },

    passwordForSubmit: function(password) {
        return (typeof hex_md5 === 'function') ? hex_md5(password) : password;
    },

    alert: function(options) {
        if (typeof Swal !== 'undefined' && Swal.fire) {
            return Swal.fire(options);
        }

        const title = options && options.title ? options.title + '\n' : '';
        const text = PERFIL.getMessage(options && options.text, '');
        if (title || text) alert(title + text);
        return Promise.resolve({ isConfirmed: true });
    },

    confirmUpdate: function() {
        if (typeof Swal !== 'undefined' && Swal.fire) {
            return Swal.fire({
                title: '¿Confirmar actualización?',
                text: 'Se actualizará tu información personal',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b4eb9',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            });
        }

        return Promise.resolve({
            isConfirmed: window.confirm('¿Confirmar actualización?\nSe actualizará tu información personal')
        });
    },

    setUpdating: function(isUpdating) {
        const button = $('#formPerfilUpdate button[onclick*="validateAndUpdate"]');
        button.prop('disabled', isUpdating);
        if (isUpdating) {
            button.data('original-text', button.html());
            button.html('Actualizando...');
        } else if (button.data('original-text')) {
            button.html(button.data('original-text'));
        }
    },

    showModal: function() {
        const modalEl = document.getElementById('perfilModal');
        if (!modalEl) return;

        if ($.fn.modal) {
            $('#perfilModal').modal('show');
            return;
        }

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    },

    hideModal: function() {
        const modalEl = document.getElementById('perfilModal');
        if (!modalEl) return;

        if ($.fn.modal) {
            $('#perfilModal').modal('hide');
            return;
        }

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }
    },
    
    clearForm: function() {
        const profileId = $('#perfilModal').data('profile-id') || '';
        $('#formPerfilUpdate')[0].reset();
        $('#idVotantes_perfil').val(profileId);
        $('#current_password_perfil').val('');
        $('#password_perfil').val('');
        $('#password_confirm_perfil').val('');
    },

    fillProfileForm: function(data) {
        $('#idVotantes_perfil').val(data.id);
        $('#nombre_completo_perfil').val(data.nombre_completo);
        $('#email_perfil').val(data.email);
        $('#username_perfil').val(data.username);
        $('#current_password_perfil').val('');
        $('#password_perfil').val('');
        $('#password_confirm_perfil').val('');
    },

    loadProfile: function(idVotante, options) {
        options = options || {};
        const showModal = options.showModal !== false;
        idVotante = parseInt(idVotante, 10);

        if (!idVotante) {
            PERFIL.alert({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo identificar el usuario del perfil'
            });
            return;
        }

        $.ajax({
            url: './admin/ajax/rqst.php',
            type: 'POST',
            data: {
                op: 'votantesget',
                id: idVotante
            },
            dataType: 'json',
            success: function(response) {
                if (response.output && response.output.valid && response.output.response && response.output.response[0]) {
                    const data = response.output.response[0];
                    PERFIL.fillProfileForm(data);

                    if (showModal) {
                        PERFIL.showModal();
                    }
                } else {
                    PERFIL.alert({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la información del perfil'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar perfil:', error);
                PERFIL.alert({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al conectar con el servidor'
                });
            }
        });
    },

    validateAndUpdate: function() {
        const form = $('#formPerfilUpdate')[0];
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        const email = $('#email_perfil').val().trim();
        const username = $('#username_perfil').val().trim();
        const currentPassword = $('#current_password_perfil').val();
        const newPassword = $('#password_perfil').val();
        const confirmPassword = $('#password_confirm_perfil').val();

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            PERFIL.alert({
                icon: 'warning',
                title: 'Email inválido',
                text: 'Por favor ingresa un correo electrónico válido'
            });
            return false;
        }

        if (newPassword && newPassword !== confirmPassword) {
            PERFIL.alert({
                icon: 'warning',
                title: 'Contraseñas no coinciden',
                text: 'La nueva contraseña y su confirmación deben ser iguales'
            });
            return false;
        }

        PERFIL.confirmUpdate().then((result) => {
            if (result.isConfirmed) {
                PERFIL.updateProfile();
            }
        });
    },

    updateProfile: function() {
        const formData = new FormData();
        
        formData.append('op', 'votantesactualizarperfil');
        formData.append('idVotantes', PERFIL.getProfileId());
        formData.append('nombre_completo', $('#nombre_completo_perfil').val());
        formData.append('email', $('#email_perfil').val());
        formData.append('username', $('#username_perfil').val());
        
        const currentPassword = $('#current_password_perfil').val();
        if (currentPassword) {
            formData.append('current_password', PERFIL.passwordForSubmit(currentPassword));
        }
        
        const newPassword = $('#password_perfil').val();
        if (newPassword) {
            formData.append('password', PERFIL.passwordForSubmit(newPassword));
        }

        $.ajax({
            url: './admin/ajax/rqst.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                PERFIL.setUpdating(true);
                if (typeof Swal !== 'undefined' && Swal.fire) {
                    Swal.close();
                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            },
            success: function(response) {
                const isValid = (response.output && response.output.valid) || response.valid; 
                const responseData = response.output || response; 
                
                if (isValid) {
                    PERFIL.alert({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: PERFIL.getMessage(responseData.response, 'Perfil actualizado correctamente'),
                        confirmButtonColor: '#2b4eb9'
                    }).then(() => {
                        PERFIL.clearForm();
                        PERFIL.hideModal();
                        location.reload();
                    });
                } else {
                    const errorMsg = PERFIL.getMessage(responseData.response, 'Error desconocido al actualizar el perfil.');
                    
                    if (typeof Swal !== 'undefined' && Swal.close) Swal.close();
                    
                    PERFIL.alert({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                }
            },
            error: function(xhr, status, error) {
                if (typeof Swal !== 'undefined' && Swal.close) Swal.close();
                
                PERFIL.alert({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                });
            },
            complete: function() {
                PERFIL.setUpdating(false);
            }
        });
    }
};

$(document).ready(function() {
    $('#perfilModal').on('shown.bs.modal', function () {
        const id = PERFIL.getProfileId();
        const hasProfileData = $('#nombre_completo_perfil').val() || $('#email_perfil').val() || $('#username_perfil').val();

        if (id && !hasProfileData) {
            PERFIL.loadProfile(id, { showModal: false });
        }
    });

    $('#perfilModal').on('hidden.bs.modal', function () {
        PERFIL.clearForm();
    });
});
