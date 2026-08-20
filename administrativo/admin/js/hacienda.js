$(document).on('ready', initAreas);

let queryParams;
const returnPage = 'areas.php';

function initAreas() {
    queryParams = {};
}

const HACIENDA = {

    saveData: function () {
        Swal.fire({
            title: '¿Estás seguro de ingresar la información?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            denyButtonText: 'No guardar'
        }).then((result) => {
            if (result.isConfirmed) {
                const q = {
                    op: 'hacienda_save',
                    tbl_municipio_id: $("#tbl_municipio_id").val(),
                    date: $("#date").val(),
                    provincia: $("#provincia").val(),
                    tbl_departamento_id: $("#tbl_departamento_id").val(),
                    secretaria: $("#secretaria").val(),
                    objeto: $("#objeto").val(),
                    accion: $("#accion").val(),
                    cantidad: $("#cantidad").val(),
                    incautacion_licores: $("#incautacion_licores").val(),
                    incautacion_cigarrillos: $("#incautacion_cigarrillos").val(),
                    capacitacion_programada: $("#capacitacion_programada").val(),
                    capacitacion_ejecutada: $("#capacitacion_ejecutada").val(),
                    observaciones: $("#observaciones").val()
                };

                UTIL.cursorBusy();

                $.ajax({
                    data: q,
                    type: "POST",
                    dataType: "json",
                    url: "admin/ajax/rqst.php",
                    success: function (data) {
                        UTIL.cursorNormal();

                        if (data.output.valid) {
                            UTIL.mostrarMensajeExitoso("Información guardada correctamente");
                            HACIENDA.clearForm(); // Limpia campos al guardar con éxito
                        } else {
                            UTIL.mostrarMensajeError(data.output.response.content);
                        }
                    },
                    error: function () {
                        UTIL.cursorNormal();
                        UTIL.mostrarMensajeError("Error en la comunicación con el servidor");
                    }
                });
            }
        });
    },

    clearForm: function () {
        // Limpia todos los inputs excepto los de tipo hidden
        $('form :input').not('[type="hidden"]').each(function () {
            if ($(this).is('input') || $(this).is('textarea') || $(this).is('select')) {
                $(this).val('');
            }
        });
    }
    
};
