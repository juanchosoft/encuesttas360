$(document).on("ready", initpuntaje);

function initpuntaje() {
    PUNTAJES.init();
}

const PUNTAJES = {
    edit: function (id) {
        q = {};
        q.op = "getPuntaje";
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
                    $("#ejeId").val(res.tbl_eje_id);
                    PUNTAJES.getPilarByEjeId();
                    $("#tipo_medicion").val(res.tipo_medicion);
                    $("#color").val(res.color);
                    $("#desde").val(res.rango_desde);
                    $("#hasta").val(res.rango_hasta);
                    setTimeout(() => {
                        $("#pilarId").val(res.tbl_pilar_id);
                    }, 1000);
                } else {
                    UTIL.mostrarMensajeError(data.output.response.content);
                }
            },
        });
    },
    getPilarByEjeId: function () {
        const ejeId = $("#ejeId").val();
        const $pilarId = $("#pilarId");

        if (ejeId <= 0 || ejeId === "seleccione") {
            $pilarId.empty().prop("disabled", true);
            return;
        }

        UTIL.cursorBusy();
        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            data: { op: "getPilar", ejeId },
            dataType: "json",
            success: function (response) {
                UTIL.cursorNormal();
                $pilarId.empty();

                if (response.output.valid && response.output.response.length > 0) {
                    const options = response.output.response
                        .map(item => `<option value="${item.id}">${item.nombre}</option>`)
                        .join("");
                    $pilarId.append(options).prop("disabled", false);
                } else {
                    $pilarId.prop("disabled", true);
                }
            },
            error: function () {
                UTIL.cursorNormal();
                $pilarId.empty().prop("disabled", true);
            },
        });
    },
    save() {
        const msj = "Falta ingresar información obligatoria, marcada con asterisco.";

        // Validar campos obligatorios
        const camposRequeridos = ["#ejeId", "#areaId", "#pilarId", "#tipo", "#tipo_medicion" , "#desde" , "#hasta"];
        if (!UTIL.validarCampos(camposRequeridos)) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }

        // Crear objeto con datos
        const datos = {
            op: "configuracionpuntajesave",
            id: $("#idPuntaje").val(),
            ejeId: $("#ejeId").val(),
            pilarId: $("#pilarId").val(),
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
                window.location = 'conf_puntajes.php';
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al guardar la información');
        }
    }
};
