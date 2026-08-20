$(document).on('ready', init);
var q;

function init() {
    q = {};
}

var return_page = 'linea.php';
var LINEA = {
    editData: function (id) {
        console.log(id)
        q = {};
        q.op = "getlinea";
        q.id = id;
        UTIL.callAjaxRqstPOST(q, this.editHandler);
    },
    editHandler: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            var res = data.output.response[0];
            $("#id").val(res.id);
            $("#nombre").val(res.nombre);
            $("#descripcion").val(res.descripcion);
            $("#nombre").focus();
        } else {
            UTIL.mostrarMensajeError(data.output.response.content);
        }
    },

    savedata: function () {
        var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
        if (
            $("#nombre").val() == ""
        ) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }
        q = {};
        q.op = "savelinea";
        q.id = $("#id").val();
        q.nombre = $("#nombre").val();
        q.descripcion = $("#descripcion").val();
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
                    UTIL.mostrarMensajeExitoso('Información guardada correctamente');
                    setTimeout(function () {
                        window.location = return_page;
                    }, 1000);
                } else {
                    UTIL.mostrarMensajeError(data.output.response.content);
                }
            },
        });
    },


};