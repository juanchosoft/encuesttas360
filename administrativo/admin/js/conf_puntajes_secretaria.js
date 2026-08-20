$(document).on("ready", initpuntaje);

function initpuntaje() {
    PUNTAJES.init();
}

const PUNTAJES = {
    edit: function (id) {
        q = {};
        q.op = "getPuntajeSecretaria";
        q.id = id;
        UTIL.cursorBusy();
        $.ajax({
            data: q,
            type: "GET",
            dataType: "json",
            url: "admin/ajax/rqst.php",
            success: function (data) {
                q = {};
                UTIL.cursorNormal();
                if (data.output.valid) {
                    let res = data.output.response[0];
                    $("#idPuntaje").val(res.id);
                    $("#secretariaId").val(res.tbl_secretaria_id);
                    $("#tipo_medicion").val(res.tipo_medicion);
                    $("#color").val(res.color);
                    $("#desde").val(res.rango_desde);
                    $("#hasta").val(res.rango_hasta);
                } else {
                    UTIL.mostrarMensajeError(data.output.response.content);
                }
            },
        });
    },
    save() {
        const msj = "Falta ingresar información obligatoria, marcada con asterisco.";

        // Validar campos obligatorios
        const camposRequeridos = ["#secretariaId", "#tipo", "#tipo_medicion" , "#desde" , "#hasta", "#color"];
        if (!UTIL.validarCampos(camposRequeridos)) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }

        // Crear objeto con datos
        const datos = {
            op: "configuracionpuntajesecretariasave",
            id: $("#idPuntaje").val(),
            secretariaId: $("#secretariaId").val(),
            desde: $("#desde").val(),
            hasta: $("#hasta").val(),
            tipo_medicion: $("#tipo_medicion").val(),
            color: $("#color").val(),
        };

        // Llamada AJAX
        UTIL.callAjaxRqstPOST(datos, PUNTAJES.savehandler);
    },

    savehandler(data) {
        UTIL.cursorNormal();

        if (data.output.valid) {
            UTIL.mostrarMensajeExitoso('Información guardada correctamente');
            UTIL.clearForm('formupuntajes');
            setTimeout(() => {
                window.location = 'conf_puntajes_secretarias.php';
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al guardar la información');
        }
    }
};
