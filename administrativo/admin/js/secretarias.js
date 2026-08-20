$(document).on('ready', init);
var q;
let isUpdating = false; // Flag para evitar loop infinito
let updateHistoryDebounce;
function init() {
    q = {};
}

var return_page = 'secretarias.php';
var SECRETARIAS = {
    editData: function (id) {
        q = {};
        q.op = "secretariaget";
        q.id = id;
        UTIL.callAjaxRqstPOST(q, this.editdaHandler);
    },
    editdaHandler: function (data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            var res = data.output.response[0];
            $("#id").val(res.id);
            $("#secretaria").val(res.secretaria);
            $("#secretario").val(res.secretario);
            $("#correo").val(res.correo);

        } else {
            UTIL.mostrarMensajeError(data.output.response.content);
        }
    },
    validateData: function () {
        var bValid = true;
        var msj = "Falta ingresar información obligatoria, marcada con asterisco.";
        if (
            $("#secretaria").val() == "" ||
            $("#secretario").val() == "" ||
            $("#correo").val() == ""

        ) {
            UTIL.mostrarMensajeValidacion(msj);
            bValid = false;
            return;
        }
        if (bValid) {
            SECRETARIAS.savedata();
        }
    },
    savedata: function () {
        q = {};
        q.op = "secretariasave";
        q.id = $("#id").val();
        q.secretaria = $("#secretaria").val();
        q.secretario = $("#secretario").val();
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
    getSecretarias: function () {
        if ($("#tbl_secretarias_id").val() != "seleccione") {
            q = {};
            q.op = "secretariaget";
            q.codigo_secretarias = $("#tbl_secretarias_id").val();
            UTIL.callAjaxRqstPOST(q, this.getSecretariasHandler);
            SECRETARIAS.emptyTable();
        } else {
            $("#tbl_secretarias_id").empty().append('');
        }
    },
};

function updateSelectWithoutTrigger(selectId, value) {
    const selectElement = $(selectId);
    isUpdating = true; 
    selectElement.off("change"); 
    selectElement.val(value); 
    selectElement.trigger("change");
    setTimeout(() => {
        isUpdating = false; 
    }, 300);
}


function updateUrlSecretaria(item) {
    if (isUpdating) return; // Evitar que se ejecute mientras está en proceso de actualización
    const selectedSecretaria = item.value || 2;
    // Validar si el valor seleccionado ya está en la URL
    const currentUrl = new URL(window.location.href);
    // Actualizar el valor del select y evitar loop infinito
    updateSelectWithoutTrigger("#secretariaId", selectedSecretaria);
    // Debounce para limitar llamadas a pushState
    clearTimeout(updateHistoryDebounce);
    updateHistoryDebounce = setTimeout(() => {
        currentUrl.searchParams.set('secretaria', selectedSecretaria);
        window.history.pushState({}, '', currentUrl);
        $.ajax({
            url: currentUrl.toString(),
            type: "GET",
            success: function (response) {
                const updatedContent = $(response).find("#contenido-mapa").html();
                const divPuntajes = $(response).find("#divPuntajes").html();
                $("#contenido-mapa").html(updatedContent);
                $("#divPuntajes").html(divPuntajes);
            },
            error: function (error) {
                console.error("Error al cargar contenido:", error);
            }
        });
    }, 500);
}