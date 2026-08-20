$(document).on("ready", init);
var q, filtro;
var res = {};

function init() {
    q = {};
}

var VEREDA_CRITICAS = {
 
};

// VEREDAS CRITICAS POR PILAR SCRIPTS
$(document).ready(function () {
    console.log("Cargando rangos...");

    $.ajax({
        url: "admin/ajax/rqst.php",
        type: "GET",
        data: { op: "getPuntaje" },
        dataType: "json",
        success: function (response) {
            console.log("Datos de rangos recibidos:", response);

            let selectRango = $("#rangoId");
            selectRango.empty(); 
            selectRango.append(`<option value="" style="background-color: white; color: black;">Seleccione un rango</option>`);

            if (response.output.valid) {
                response.output.response.forEach(function (item) {
                    let option = `<option value="${item.id}" data-color="${item.color}" 
                                   style="background-color:${item.color}; color:white; font-weight:bold;">
                                    ${item.rango_desde} - ${item.rango_hasta}
                                  </option>`;
                    selectRango.append(option);
                });
            } else {
                console.warn("No hay datos de rangos disponibles.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud de rangos:", status, error);
        }
    });
    // Cuando el usuario seleccione un rango, cambiar el color del SELECT
    $("#rangoId").change(function () {
        let color = $(this).find("option:selected").data("color");
        $("#rangoId").css({
            "background-color": color,
            "color": "white",
            "font-weight": "bold"
        });
        // Cambiar el color del cuadro de selección también
        $("#colorBox").css("background-color", color).show();
    });
    // Limpiar la tabla cuando cambie algún select antes de presionar "Buscar"
    $("#tbl_departamento_id, #tbl_municipio_id, #pilarId, #rangoId").change(function () {
        let tbody = $("#tablaVeredas tbody");
        tbody.empty(); // Vacía la tabla
        // Mostrar el mensaje inicial
        tbody.append(`
            <tr id="mensajeInicial">
                <td style="font-size:15px" colspan="3" class="text-center text-muted">
                    🔍 Consulta para conocer las veredas críticas según el departamento, municipio y pilar seleccionados.
                </td>
            </tr>
        `);
    });

});

$("#btnSeleccionar").click(function () {
    let departamento = $("#tbl_departamento_id").val();
    let municipio = $("#tbl_municipio_id").val();
    let pilar = $("#pilarId").val();
    let rango = $("#rangoId").val();

    console.log("Datos enviados:", { departamento, municipio, pilar, rango });

    if (!departamento || !municipio || !pilar || !rango) {
    Swal.fire({
            icon: "error",
            title: "Faltan datos requeridos",
            text: "Por favor, selecciona todos los campos antes de continuar.",
            confirmButtonText: "Entendido",
            confirmButtonColor: "#d33",
        });
        return;
    }
    $.ajax({
        url: "admin/ajax/rqst.php",
        type: "GET",
        data: {
            op: "getVeredasCriticas",
            departamento: departamento,
            municipio: municipio,
            pilar: pilar,
            rango: rango
        },
        dataType: "json",
        success: function (response) {
            console.log("Respuesta del servidor:", response);
            if (response.output.valid) {
                actualizarTabla(response.output.response);
            } else {
                console.warn("⚠ No se encontraron veredas críticas .");
                $("#tablaVeredas tbody").html("<tr><td colspan='3' class='text-center'>No se encontraron veredas críticas.</td></tr>");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la consulta:", status, error);
        },
        complete: function () {
            // Volver el cursor a normal después de la consulta
            document.body.style.cursor = "default";
        }

    });
});


function actualizarTabla(data) {
    let tbody = $("#tablaVeredas tbody");
    tbody.empty();

    if (data.length === 0) {
        tbody.append(`<tr><td colspan="3" class="text-center">No se encontraron veredas críticas para este municipio en el rango seleccionado.</td></tr>`);
        return;
    }

    data.forEach(vereda => {
        let color = vereda.color_calculado ? vereda.color_calculado : "#CCCCCC"; 

        let fila = `
            <tr>
                <td>${vereda.nombre_vereda}</td>
                <td>
                    <button style= "background-color:rgb(0, 174, 255);color:blue !important" type="button" class="btn btn-icon btn-info btnVerDetalles"
                        data-id="${vereda.id}" data-nombre="${vereda.nombre_vereda}" data-color="${color}">
                        <i  class="fas fa-eye"></i>
                    </button>

                </td>
            </tr>
        `;
        tbody.append(fila);
    });

    // Asignar evento de clic a los botones "Ver"
    $(".btnVerDetalles").click(function () {
        let veredaId = $(this).data("id");
        let nombreVereda = $(this).data("nombre");
        let colorVereda = $(this).data("color");

        // Llenar la información básica en el modal
        $("#modalSeleccionar .modal-title").text(`Detalles de la Vereda - ${nombreVereda}`);
        $("#modalSeleccionar #veredaColor").css("background-color", colorVereda);
        $("#modalSeleccionar #veredaId").text(`ID: ${veredaId}`);

        // Hacer una petición AJAX para obtener los datos de la tabla temporal
        $.ajax({
            url: "veredas_criticas.php",
            type: "GET",
            data: { veredaId: veredaId },
            success: function (response) {
                let modalBody = $(response).find(".modal-body").html();
                $("#modalSeleccionar .modal-body").html(modalBody);
                $("#modalSeleccionar").modal("show");  // Mostrar el modal
            },
            error: function () {
                alert("Error al cargar los datos.");
            }
        });
    });
}
$(document).on("click", ".btnVerDetalles", function () {
    var veredaId = $(this).data("id");
    console.log("Botón presionado - Vereda ID:", veredaId); // Depuración

    // Hacer una petición AJAX para obtener los datos de la vereda
    $.ajax({
        url: "admin/ajax/rqst.php",
        type: "GET",
        data: {
            op: "getFactoresVereda",
            veredaId: veredaId
        },
        dataType: "json",
        success: function (response) {
            console.log("Respuesta del servidor:", response); // Depuración

            if (response.output.valid) {
                let tablaBody = $("#tablaFactoresBody");
                tablaBody.empty(); // Limpiar la tabla antes de agregar nuevos datos

                // Iterar sobre los datos y agregarlos a la tabla
                response.output.response.forEach(function (item) {
                    let cantidadFormateada = Number(item.cantidad).toLocaleString('es-ES'); // Formatear con puntos como separadores de miles

                    let row = `
                        <tr>
                            <td>${item.factor}</td>
                            <td>${cantidadFormateada}</td>
                            <td>${item.unidad_medida}</td>
                        </tr>`;
                    tablaBody.append(row);
                });

                // Abrir el modal
                $("#modalVeredaDetalles").modal("show");
            } else {
                alert("No se encontraron datos.");
            }
        },
        error: function () {
            alert("Error al obtener los datos.");
        }
    });
});