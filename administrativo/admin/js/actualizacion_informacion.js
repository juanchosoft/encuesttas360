$(document).on("ready", initactualizarinformacion);

function initactualizarinformacion() {
    ACTUALIZACION_INFORMACION.init();
}

const ACTUALIZACION_INFORMACION = {
    init() {
        $("#factorId").on("change", function () {
            ACTUALIZACION_INFORMACION.obtenerRegistrosExistentes(true);
        });

        $("#tbl_departamento_id, #tbl_municipio_id, #tbl_vereda_id").on("change", function () {
            ACTUALIZACION_INFORMACION.obtenerRegistrosExistentes();
        });
    },

    obtenerRegistrosExistentes(forzarConsulta = false) {
        const codDepartamento_id = $("#tbl_departamento_id").val();
        const codMunicipio_id = $("#tbl_municipio_id").val();
        const vereda_id = $("#tbl_vereda_id").val();
        const factorId = $("#factorId").val();

        if (!codDepartamento_id || !codMunicipio_id || !vereda_id || !factorId) {
            if (!forzarConsulta) {
                console.warn("Faltan datos para la consulta");
                return;
            }
        }

        // console.log("Ejecutando consulta para factorId:", factorId);

        const datos = {
            op: "obtener_registros_existentes",
            codDepartamento_id: codDepartamento_id,
            codMunicipio_id: codMunicipio_id,
            vereda_id: vereda_id,
            factorId: factorId
        };

        fetch("admin/ajax/rqst.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.output.valid) {
                ACTUALIZACION_INFORMACION.actualizarTablaRegistros(data.output.data);
            } else {
                console.error("Error:", data.output.error);
            }
        })
        .catch(error => console.error("Error:", error));
    },

    actualizarTablaRegistros(data) {
        const tabla = document.getElementById("tablaRegistros");
        let tbody = tabla.querySelector("tbody");
    
        // Reparar tbody si falta
        if (!tbody) {
            tabla.innerHTML += "<tbody></tbody>";
            tbody = tabla.querySelector("tbody");
        }
    
        $(tabla).removeClass('table-bordered table-striped');
        $(tabla).css('display', 'table');
        $(tbody).css('overflow', 'auto');
    
        tbody.innerHTML = "";
    
        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td class="text-center" colspan="3" style="text-align:center; color:gray; font-weight:bold;">
                        <i class="fas fa-info-circle"></i> No hay registros disponibles
                    </td>
                </tr>
            `;
            return;
        }
    
        data.forEach((registro, index) => {
            let row = document.createElement("tr");
            row.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" name="registroSeleccionado" value="${registro.id}" class="form-check-input">
                </td>
                <td class="text-center">${formatNumero(registro.valor)}</td>
                <td class="text-center">${registro.tipo_medicion || 'N/A'}</td>
            `;
    
            let checkbox = row.querySelector("input");
    
            checkbox.addEventListener("change", function () {
                document.querySelectorAll("#tablaRegistros tbody tr").forEach(tr => {
                    tr.classList.remove("selected-row");
                    const input = tr.querySelector("input");
                    if (input) input.checked = false;
                });
    
                this.checked = true;
                row.classList.add("selected-row");
                ACTUALIZACION_INFORMACION.registroSeleccionado = registro.id;
                ACTUALIZACION_INFORMACION.registroSeleccionadoCantidadActual = registro.valor;
            });
    
            tbody.appendChild(row);
    
            if (index === 0) {
                checkbox.checked = true;
                row.classList.add("selected-row");
                ACTUALIZACION_INFORMACION.registroSeleccionado = registro.id;
                ACTUALIZACION_INFORMACION.registroSeleccionadoCantidadActual = registro.valor;
            }
        });
    },

    registroSeleccionado: null, // Almacena el ID del registro elegido
    registroSeleccionadoCantidadActual: 0, // Almacena el ID del registro elegido
    save() {
        const msj = "Falta ingresar información obligatoria, marcada con asterisco.";

        // Validar campos obligatorios
        const camposRequeridos = [ "#tbl_departamento_id", "#tbl_municipio_id", "#tbl_vereda_id", "#actoresId", "#cantidad_nueva", "#actoresId", "#accion_realizada", "#accion_realizada"];

        if (!this.validarCampos(camposRequeridos)) {
            UTIL.mostrarMensajeValidacion(msj);
            return;
        }
        if (!this.registroSeleccionado) {
            UTIL.mostrarMensajeError("Debe seleccionar un registro antes de guardar.");
            return;
        }
    
        const iframe1 = $("#ifm1").attr("data-url") || null;
        const iframe2 = $("#ifm2").attr("data-url") || null;
        const iframe3 = $("#ifm3").attr("data-url") || null;
        const iframe4 = $("#ifm4").attr("data-url") || null;

        // Crear objeto con datos
        const datos = {
            op: "actualizacioninformacionsave",
            id: $("#id").val(),
            tbl_ingreso_informacion_id: this.registroSeleccionado,
            codDepartamento_id: $("#tbl_departamento_id").val(),
            codMunicipio_id: $("#tbl_municipio_id").val(),
            vereda_id: $("#tbl_vereda_id").val(),
            factorId: $("#factorId").val(),
            actoresId: $("#actoresId").val(),
            accion_realizada: $("#accion_realizada").val(),
            valor_actualizacion: $("#cantidad_nueva").val(),
            valor_actual: this.registroSeleccionadoCantidadActual, 
            foto1: iframe1,
            foto2: iframe2,
            foto3: iframe3,
            foto4: iframe4
        };
        console.log(datos);

        // Llamada AJAX
        UTIL.callAjaxRqstPOST(datos, ACTUALIZACION_INFORMACION.savehandler);
    },

    savehandler(data) {
        UTIL.cursorNormal();

        if (data.output.valid) {
            UTIL.mostrarMensajeExitoso('Información guardada correctamente');
            setTimeout(() => {
                window.location = '';
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content);
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
// UNIDADES DE MIL PARA LOS NUMEROS 
function formatNumero(num) {
    if (!num) return "0";
    
    num = parseFloat(num);
    if (isNaN(num)) return num;

    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
