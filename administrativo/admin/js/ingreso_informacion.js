$(document).on("ready", initingresoinformacion);

function initingresoinformacion() {
}

const INGRESO_INFORMACION = {
    openImage: function (src) {
        // Abre la imagen en una nueva ventana o pestaña
        window.open(src, '_blank');
    },
    delete: function(id) {
        Swal.fire({
            title: "Está completamente que desea eliminar el registtro?",
            text: "¿Desea continuar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "Cancelar!",
            closeOnConfirm: false,
        }).then((result) => {
            if (result.value) {
                q = {};
                q.op = 'deleteingresoinformacion';
                q.id = id;
                UTIL.cursorBusy();
                $.ajax({
                    data: q,
                    type: "GET",
                    dataType: "json",
                    url: "admin/ajax/rqst.php",
                    success: function(data) {
                        q = {};
                        UTIL.cursorNormal();
                        if (data.output.valid) {
                            UTIL.mostrarMensajeExitoso(
                                "Información eliminada correctamente"
                            );
                            const fila = document.getElementById('fila_' + id);
                            if (fila) {
                                fila.remove();
                            }

                        } else {
                            UTIL.mostrarMensajeError(data.output.response.content);
                        }
                    },
                });
            }
        });
    },
    save() {
        const msj = "Falta ingresar información obligatoria, marcada con asterisco.";

        // Validar campos obligatorios
        const camposRequeridos = [ "#tbl_departamento_id", "#tbl_municipio_id", "#tbl_vereda_id", "#factorId", "#valor"];

        if (!this.validarCampos(camposRequeridos)) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }

        const iframe1 = $("#ifm1").attr("data-url") || null;
        const iframe2 = $("#ifm2").attr("data-url") || null;
        const iframe3 = $("#ifm3").attr("data-url") || null;
        const iframe4 = $("#ifm4").attr("data-url") || null;

        // Crear objeto con datos
        const datos = {
            op: "ingresoinformacionsave",
            id: $("#id").val(),
            codDepartamento_id: $("#tbl_departamento_id").val(),
            codMunicipio_id: $("#tbl_municipio_id").val(),
            vereda_id: $("#tbl_vereda_id").val(),
            factorId: $("#factorId").val(),
            longitud: $("#longitud").val(),
            observaciones: $("#observaciones").val(),
            latitud: $("#latitud").val(),
            valor: $("#valor").val(),
            foto1: iframe1,
            foto2: iframe2,
            foto3: iframe3,
            foto4: iframe4
        };

        // Llamada AJAX
        UTIL.callAjaxRqstPOST(datos, INGRESO_INFORMACION.savehandler);
    },

    savehandler(data) {
        UTIL.cursorNormal();

        if (data.output.valid) {
            UTIL.mostrarMensajeExitoso('Información guardada correctamente');
            setTimeout(() => {
                window.location = '';
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content || 'Error al guardar la información');
        }
    },

    showInfoGetFactores: function () {
        let id = $("#factorId").val();
        if(id > 0){
            q = {};
            q.op = "getFactores";
            q.id =  $("#factorId").val();
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
                        $("#eje").val(res.eje);
                        $("#pilar").val(res.pilar);
                        $("#area").val(res.area);
                        $("#tipo_medicion").val(res.tipo_medicion);
    
                        $('#divInformacion').show(); 
                    } else {
                        $('#divInformacion').hide(); 
                    }
                },
            });
        }else{
            $('#divInformacion').hide(); 
            $("#eje").val('');
            $("#pilar").val('');
            $("#area").val('');
            $("#tipo_medicion").val('');
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
function mostrarAlertaModal(tipo, mensaje, contenedor = "alerta-modal") {
    let alertDiv = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    document.getElementById(contenedor).innerHTML = alertDiv;
}

function guardarEdicion() {
    let id = $("#modalId").val();
    let nuevoValor = $("#modalValor").val();
    let modalLogitud = $("#modalLogitud").val();
    let modalLatitud = $("#modalLatitud").val();

    if (!id || isNaN(nuevoValor)) {
        mostrarAlertaModal("danger", "❌ ID no válido o valor incorrecto.", "alerta-modal");
        return;
    }

    if (!id || modalLogitud  == '' || modalLatitud == '') {
        mostrarAlertaModal("danger", "❌  Latitud y/o Longitud son campos obligatorios. ", "alerta-modal");
        return;
    }
    const datos = {
        op: "editarInformacion",
        id: id,
        valor: nuevoValor,
        longitud: modalLogitud,
        latitud: modalLatitud
    };
    $.ajax({
        url: "admin/ajax/rqst.php",
        type: "POST",
        data: datos,
        dataType: "json",
        success: function(response) {

            if (response.output.valid) {
                mostrarAlertaModal("success", "✅ Información actualizada correctamente. Espere porfavor...", "alerta-modal");
                // Esperar un segundo y recargar la página para reflejar los cambios
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                mostrarAlertaModal("danger", "❌ Error al actualizar la información.", "alerta-modal");
            }
        },
        error: function(xhr, status, error) {
            mostrarAlertaModal("danger", "❌ Error editando la información.", "alerta-modal");
        }
    });
}


$(document).ready(function() {
    $('.editar-informacion').on('click', function() {
        let id = $(this).data('id');
        let fecha = $(this).data('fecha');
        let departamento = $(this).data('departamento');
        let municipio = $(this).data('municipio');
        let vereda = $(this).data('vereda');
        let factor = $(this).data('factor');
        let valor = $(this).data('valor');
        let longitud = $(this).data('longitud');
        let latitud = $(this).data('latitud');
        // Asignar valores a los campos del modal
        $('#modalId').val(id);
        $('#modalFecha').val(fecha);
        $('#modalDepartamento').val(departamento);
        $('#modalMunicipio').val(municipio);
        $('#modalVereda').val(vereda);
        $('#modalFactor').val(factor);
        $('#modalValor').val(valor);
        $("#modalLogitud").val(longitud);
        $("#modalLatitud").val(latitud);
    });
});

// Detectar cuando la página ha terminado de recargarse
$(document).ready(function () {
    if ($("#modalEdicion").hasClass("show")) {
        $("#modalEdicion").modal("hide");
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open");
    }
});