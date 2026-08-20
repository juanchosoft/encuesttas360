$(document).on("ready", init);

var q, nombre, allFields, tips;

/**
 * Inicialización del módulo
 */
function init() {
    q = {};
    FORMULAS.get();
}

var FORMULAS = {
    /**
     * Obtiene todas las fórmulas
     */
    get: function() {
        q = {};
        q.op = "formulasget";
        UTIL.callAjaxRqstPOST(q, this.getHandler);
    },

    /**
     * Handler para obtener fórmulas
     */
    getHandler: function(data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            console.log("Fórmulas cargadas:", data.output.response);
        } else {
            UTIL.mostrarMensajeError("Error al cargar fórmulas");
        }
    },

    /**
     * Validar datos antes de guardar
     */
    validateData: function() {
        const indicador = $("#indicador").val().trim();
        const sigla = $("#sigla").val().trim();
        const formula = $("#formula").val().trim();

        if (!indicador || !sigla || !formula) {
            UTIL.mostrarMensajeError("Los campos Indicador, Sigla y Fórmula son obligatorios");
            return false;
        }

        this.savedata();
    },

    /**
     * Guardar fórmula
     */
    savedata: function() {
        q = {};
        q.op = "formulassave";
        q.id = $("#id").val();
        q.indicador = $("#indicador").val();
        q.sigla = $("#sigla").val();
        q.tipo_indicador = $("#tipo_indicador").val();
        q.formula = $("#formula").val();
        q.explicacion = $("#explicacion").val();
        q.observaciones = $("#observaciones").val();
        q.enunciado_antes = $("#enunciado_antes").val();
        q.enunciado_ahora = $("#enunciado_ahora").val();
        q.notas_adicionales = $("#notas_adicionales").val();
        q.habilitado = $("#habilitado").is(':checked') ? 'si' : 'no';

        UTIL.callAjaxRqstPOST(q, this.savedataHandler);
    },

    /**
     * Handler para guardar
     */
    savedataHandler: function(data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            UTIL.mostrarMensajeExitoso("Fórmula guardada exitosamente");
            FORMULAS.emptyCells();
            setTimeout(function() {
                window.location.reload();
            }, 1000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content || "Error al guardar la fórmula");
        }
    },

    /**
     * Editar fórmula
     */
    editData: function(id) {
        q = {};
        q.op = "formulasgetbyid";
        q.id = id;
        UTIL.callAjaxRqstPOST(q, this.editdataHandler);
    },

    /**
     * Handler para editar
     */
    editdataHandler: function(data) {
        UTIL.cursorNormal();
        if (data.output.valid) {
            const res = data.output.response;

            $("#id").val(res.id);
            $("#indicador").val(res.indicador);
            $("#sigla").val(res.sigla);
            $("#tipo_indicador").val(res.tipo_indicador || '');
            $("#formula").val(res.formula);
            $("#explicacion").val(res.explicacion || '');
            $("#observaciones").val(res.observaciones || '');
            $("#enunciado_antes").val(res.enunciado_antes || '');
            $("#enunciado_ahora").val(res.enunciado_ahora || '');
            $("#notas_adicionales").val(res.notas_adicionales || '');
            $("#habilitado").prop('checked', res.habilitado === 'si');

            // Scroll al formulario
            $('html, body').animate({
                scrollTop: $("#formFormulas").offset().top - 100
            }, 500);

        } else {
            UTIL.mostrarMensajeError("Error al cargar la fórmula");
        }
    },

    /**
     * Limpiar formulario
     */
    emptyCells: function() {
        $("#id").val(0);
        $("#indicador").val("");
        $("#sigla").val("");
        $("#tipo_indicador").val("");
        $("#formula").val("");
        $("#explicacion").val("");
        $("#observaciones").val("");
        $("#enunciado_antes").val("");
        $("#enunciado_ahora").val("");
        $("#notas_adicionales").val("");
        $("#habilitado").prop('checked', true);
    },

    /**
     * Mostrar detalles en modal
     */
    showDetails: function(item) {
        let html = `
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary fs-5 me-3">${item.sigla}</span>
                        ${item.tipo_indicador ? `<span class="badge bg-info fs-6 me-3">${item.tipo_indicador}</span>` : ''}
                        <h4 class="mb-0">${item.indicador}</h4>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-calculator me-2"></i>Fórmula
                            </h6>
                            <code class="d-block p-3 bg-white border rounded">${item.formula}</code>
                        </div>
                    </div>
                </div>

                ${item.explicacion ? `
                <div class="col-12">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-info-circle me-2"></i>Explicación
                    </h6>
                    <p class="text-muted">${item.explicacion}</p>
                </div>
                ` : ''}

                ${item.observaciones ? `
                <div class="col-12">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-clipboard-list me-2"></i>Observaciones
                    </h6>
                    <p class="text-muted">${item.observaciones}</p>
                </div>
                ` : ''}

                ${item.enunciado_antes || item.enunciado_ahora ? `
                <div class="col-md-6">
                    <h6 class="text-warning mb-2">
                        <i class="fas fa-arrow-left me-2"></i>Enunciado Antes
                    </h6>
                    <p class="text-muted">${item.enunciado_antes || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success mb-2">
                        <i class="fas fa-arrow-right me-2"></i>Enunciado Ahora
                    </h6>
                    <p class="text-muted">${item.enunciado_ahora || 'N/A'}</p>
                </div>
                ` : ''}

                ${item.notas_adicionales ? `
                <div class="col-12">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-sticky-note me-2"></i>Notas Adicionales
                    </h6>
                    <p class="text-muted">${item.notas_adicionales}</p>
                </div>
                ` : ''}

                <div class="col-12">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>Creado por: ${item.usuario_nombre || 'N/A'}
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>${new Date(item.dtcreate).toLocaleString('es-CO')}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $("#detallesContent").html(html);
        $("#modalDetalles").modal('show');
    },

    /**
     * Mostrar modal de carga CSV
     */
    showUploadModal: function() {
        $("#csvFile").val("");
        $("#csvHasHeader").prop('checked', true);
        $("#modalUploadCSV").modal('show');
    },

    /**
     * Cargar archivo CSV
     */
    uploadCSV: function() {
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];

        if (!file) {
            UTIL.mostrarMensajeError("Por favor selecciona un archivo CSV");
            return;
        }

        if (!file.name.endsWith('.csv')) {
            UTIL.mostrarMensajeError("El archivo debe ser formato CSV");
            return;
        }

        const hasHeader = $("#csvHasHeader").is(':checked');
        const reader = new FileReader();

        reader.onload = function(e) {
            const csvData = e.target.result;
            FORMULAS.processCSV(csvData, hasHeader);
        };

        reader.onerror = function() {
            UTIL.mostrarMensajeError("Error al leer el archivo");
        };

        reader.readAsText(file, 'UTF-8');
    },

    /**
     * Procesar contenido CSV
     */
    processCSV: function(csvData, hasHeader) {
        try {
            // Parsear CSV
            const lines = csvData.split('\n');
            const startIndex = hasHeader ? 1 : 0;
            const formulas = [];

            for (let i = startIndex; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;

                // Parsear línea CSV respetando comillas
                const values = this.parseCSVLine(line);

                if (values.length >= 3) { // Mínimo: indicador, sigla, formula
                    formulas.push({
                        indicador: values[0] || '',
                        sigla: values[1] || '',
                        tipo_indicador: values[2] || '',
                        formula: values[3] || '',
                        explicacion: values[4] || '',
                        observaciones: values[5] || '',
                        enunciado_antes: values[6] || '',
                        enunciado_ahora: values[7] || '',
                        notas_adicionales: values[8] || ''
                    });
                }
            }

            if (formulas.length === 0) {
                UTIL.mostrarMensajeError("El archivo CSV está vacío o no tiene el formato correcto");
                return;
            }

            // Enviar al servidor
            q = {};
            q.op = "formulasimport";
            q.formulas = JSON.stringify(formulas);

            UTIL.callAjaxRqstPOST(q, this.uploadCSVHandler);

        } catch (error) {
            UTIL.mostrarMensajeError("Error al procesar el archivo: " + error.message);
        }
    },

    /**
     * Parsear línea CSV respetando comillas y comas
     */
    parseCSVLine: function(line) {
        const result = [];
        let current = '';
        let inQuotes = false;

        for (let i = 0; i < line.length; i++) {
            const char = line[i];

            if (char === '"') {
                inQuotes = !inQuotes;
            } else if (char === ',' && !inQuotes) {
                result.push(current.trim());
                current = '';
            } else {
                current += char;
            }
        }

        result.push(current.trim());
        return result;
    },

    /**
     * Handler para carga CSV
     */
    uploadCSVHandler: function(data) {
        UTIL.cursorNormal();
        $("#modalUploadCSV").modal('hide');

        if (data.output.valid) {
            const stats = data.output.response;
            const msg = `
                Importación completada:<br>
                ✓ Exitosos: ${stats.success}<br>
                ${stats.errors > 0 ? `⚠ Errores: ${stats.errors}<br>` : ''}
                ${stats.duplicates > 0 ? `⚠ Duplicados: ${stats.duplicates}` : ''}
            `;
            UTIL.mostrarMensajeExitoso(msg);

            setTimeout(function() {
                window.location.reload();
            }, 2000);
        } else {
            UTIL.mostrarMensajeError(data.output.response.content || "Error al importar fórmulas");
        }
    }
};
