$(document).on('ready', init);
var q;

function init() {
    q = {};
}

var return_page = 'acciong.php';
var ACCIONG = {
    editData: function (id) {
        console.log(id)
        q = {};
        q.op = "spi_acciong_get";
        q.id = id;
        UTIL.callAjaxRqstPOST(q, this.editDataHandler);
    },
    editDataHandler: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            var res = data.output.response[0];
            $("#id").val(res.id);
            $("#accion").val(res.accion);
            $("#accion").focus();
        } else {
            UTIL.mostrarMensajeError(data.output.response.content);
        }
    },
    deletedata: function (id) {
        Swal.fire({
            title: "Va a eliminar información de forma irreversible!",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "Cancelar!",
            closeOnConfirm: false,
        }).then((result) => {
            if (result.value) {
                q = {};
                q.op = "spi_acciong_delete";
                q.id = id;
                UTIL.cursorBusy();
                $.ajax({
                    data: q,
                    type: "POST",
                    dataType: "json",
                    url: "admin/ajax/rqst.php",
                    success: function (data) {
                        q = {};
                        UTIL.cursorNormal();
                        if (data.output.valid) {
                            UTIL.mostrarMensajeExitoso(
                                "Información guardada correctamente"
                            );
                            window.location = return_page;
                        } else {
                            UTIL.mostrarMensajeError(data.output.response.content);
                        }
                    },
                });
            }
        });
    },
    savedata: function () {
        var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
        if (
            $("#accion").val() == ""
        ) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }

        q = {};
        q.op = "spi_acciong_save";
        q.id = $("#id").val();
        q.accion = $("#accion").val();
        console.log(q);
        // UTIL.cursorBusy();
        $.ajax({
            data: q,
            type: "POST",
            dataType: "json",
            url: "admin/ajax/rqst.php",
            success: function (data) {
                q = {};
                UTIL.cursorNormal();
                if (data.output.valid) {
                    UTIL.mostrarMensajeExitoso('Información guardada correctamente');
                    setTimeout(function () {
                        window.location = return_page;
                    }, 1500);
                } else {
                    UTIL.mostrarMensajeError(data.output.response.content);
                }
            },
        });
    },


};