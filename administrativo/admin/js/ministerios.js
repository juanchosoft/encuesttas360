$(document).on('ready', init);
var q;

function init() {
    q = {};
}
var return_page = 'ministerios.php';
var MINISTERIOS = {
    editData: function (id) {
        q = {};
        q.op = "ministerioget";
        q.id = id;
        UTIL.callAjaxRqstPOST(q, this.editdaHandler);
    },
    editdaHandler: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            var res = data.output.response[0];
            $("#id").val(res.id);
            $("#ministerio").val(res.ministerio);
            $("#ministro").val(res.ministro);
            $("#correo").val(res.correo);

        } else {
            UTIL.mostrarMensajeError(data.output.response.content);
        }
    },
    validateData: function () {
        var bValid = true;
        var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
        if (
            $("#ministerio").val() == "" ||
            $("#ministro").val() == "" ||
            $("#correo").val() == ""

        ) {
            UTIL.mostrarMensajeValidacion(msj);
            bValid = false;
            return;
        }
        if (bValid) {
            MINISTERIOS.savedata();
        }
    },
    savedata: function () {
        q = {};
        q.op = "ministeriosave";
        q.id = $("#id").val();
        q.ministerio = $("#ministerio").val();
        q.ministro = $("#ministro").val();
        q.correo = $("#correo").val();
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
                    }, 1500);
                } else {
                    UTIL.mostrarMensajeError(data.output.response.content);
                }
            },
        });
    },
    getMinisterios: function () {
        if ($("#tbl_ministerios_id").val() != "seleccione") {
            q = {};
            q.op = "ministerioget";
            q.codigo_ministerios = $("#tbl_ministerios_id").val();
            UTIL.callAjaxRqstPOST(q, this.getMinisteriosHandler);
            MINISTERIOS.emptyTable();
        } else {
            $("#tbl_ministerios_id").empty().append('');
        }
    },
};