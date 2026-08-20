$(document).on("ready", initingresofactores);

function initingresofactores() {
    INGRESO_FACTORES.init();
}

const INGRESO_FACTORES = {
    init: function () {
        this.setupEventHandlers();
    },

    setupEventHandlers: function () {
        $("#ejeId").on("change", this.getPilarByEjeId);
        $("#pilarId").on("change", this.getAreaByPilarId);
    },
    edit: function (id) {
        q = {};
        q.op = "getFactores";
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
                    $("#ejeId").val(res.tbl_eje_id);
                    INGRESO_FACTORES.getPilarByEjeId();
                    $("#id").val(res.id);
                    $("#tipo").val(res.tipo);
                    $("#tipo_medicion").val(res.tipo_medicion);
                    $("#puntaje").val(res.puntaje);
                    $("#secretariaId").val(res.tbl_secretaria_id);

                    setTimeout(function () {
                        $("#pilarId").val(res.tec_pilar_id);
                        INGRESO_FACTORES.getAreaByPilarId();
                    }, 1000);

                    setTimeout(function () {
                        $("#areaId").val(res.tec_area_id);
                    }, 1500);
                } else {
                    UTIL.mostrarMensajeError(data.output.response.content);
                }
            },
        });
    },

    getPilarByEjeId: function () {
        const ejeId = $("#ejeId").val();
        const $pilarId = $("#pilarId");
        const $areaId = $("#areaId");

        if (ejeId <= 0 || ejeId === "seleccione") {
            $pilarId.empty().prop("disabled", true);
            $areaId.empty().prop("disabled", true);
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
                    $areaId.empty().prop("disabled", true);
                } else {
                    $pilarId.prop("disabled", true);
                    $areaId.empty().prop("disabled", true);
                }
            },
            error: function () {
                UTIL.cursorNormal();
                $pilarId.empty().prop("disabled", true);
                $areaId.empty().prop("disabled", true);
            },
        });
    },

    getAreaByPilarId: function () {
        const pilarId = $("#pilarId").val();
        const $areaId = $("#areaId");

        if (pilarId <= 0 || pilarId === "seleccione") {
            $areaId.empty().prop("disabled", true);
            return;
        }

        UTIL.cursorBusy();
        $.ajax({
            url: "admin/ajax/rqst.php",
            type: "POST",
            data: { op: "getArea", pilarId },
            dataType: "json",
            success: function (response) {
                UTIL.cursorNormal();
                $areaId.empty();

                if (response.output.valid && response.output.response.length > 0) {
                    const options = response.output.response
                        .map(item => `<option value="${item.id}">${item.nombre}</option>`)
                        .join("");
                    $areaId.append(options).prop("disabled", false);
                } else {
                    $areaId.prop("disabled", true);
                }
            },
            error: function () {
                UTIL.cursorNormal();
                $areaId.empty().prop("disabled", true);
            },
        });
    },
    save() {
        const msj = "Falta ingresar información obligatoria, marcada con asterisco.";

        // Validar campos obligatorios
        const camposRequeridos = ["#ejeId", "#areaId", "#pilarId", "#tipo", "#tipo_medicion", "#secretariaId"];
        if (!this.validarCampos(camposRequeridos)) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }
        const ifm = $("#ifm1").attr("data-url") || null;

        // Crear objeto con datos
        const datos = {
            op: "factoressave",
            id: $("#id").val(),
            ejeId: $("#ejeId").val(),
            pilarId: $("#pilarId").val(),
            areaId: $("#areaId").val(),
            tipo: $("#tipo").val(),
            tipo_medicion: $("#tipo_medicion").val(),
            puntaje: $("#puntaje").val(),
            secretariaId: $("#secretariaId").val(),
            icono: ifm
        };

        // Llamada AJAX
        UTIL.callAjaxRqstPOST(datos, INGRESO_FACTORES.savehandler);
    },

    savehandler(data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            UTIL.clearForm('formfactores');
            UTIL.mostrarMensajeExitoso('Información guardada correctamente');
            setTimeout(() => {
                window.location = 'ingreso_factores.php';
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al guardar la información');
        }
    },

    // Función auxiliar para validar campos
    validarCampos(campos) {
        for (const campo of campos) {
            if ($(campo).val() === "") {
                return false;
            }
        }
        return true;
    }

};