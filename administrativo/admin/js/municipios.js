$(document).on("ready", initmunicipio);

let MUN, DEP, PILAR;
let updateHistoryDebounce;
let isUpdating = false; // Flag para evitar loop infinito

function initmunicipio() { }

const MUNICIPIO = {
    init: function () {
        console.log("Initializing MUNICIPIO...");
        // Agregamos la información al select
        const params = UTIL.getParamsFromUrlDepartamentoMunicipio();
        selectedMunicipio = params.mun;
        DEPARTAMENTO.getMunicipiosByDepartamentoIdV2SeteraCodigoMunicipio(params.dep, params.mun);
    },

    updateUrlMunicipio: function (item) {
        if (isUpdating) return; // Prevenir loop infinito

        const selectedMunicipio = item.value || MUN;

        // Validar si el valor seleccionado ya está en la URL
        const currentUrl = new URL(window.location.href);
        const actualMunicipio = currentUrl.searchParams.get('mun');
        if (selectedMunicipio === actualMunicipio) return; // Evitar cambios innecesarios

        // Actualizar el valor del select y evitar loop infinito
        MUNICIPIO.updateSelectWithoutTrigger("#tbl_municipio_id", selectedMunicipio);

        // Debounce para limitar llamadas a pushState
        clearTimeout(updateHistoryDebounce);
        updateHistoryDebounce = setTimeout(() => {
            currentUrl.searchParams.set('mun', selectedMunicipio);
            window.history.pushState({}, '', currentUrl);

            MUNICIPIO.loadContentidoMapa(currentUrl);
        }, 500);
    },
    updateUrlPilar: function (item) {
        if (isUpdating) return; // Prevenir loop infinito

        const selectedPilar = item.value || PILAR;

        // Validar si el valor seleccionado ya está en la URL
        const currentUrl = new URL(window.location.href);
        const actualPilar = currentUrl.searchParams.get('pilar');
        if (selectedPilar === actualPilar) return; // Evitar cambios innecesarios

        // Actualizar el valor del select y evitar loop infinito
        MUNICIPIO.updateSelectWithoutTrigger("#pilarId", selectedPilar);

        // Debounce para limitar llamadas a pushState
        clearTimeout(updateHistoryDebounce);
        updateHistoryDebounce = setTimeout(() => {
            currentUrl.searchParams.set('pilar', selectedPilar);
            window.history.pushState({}, '', currentUrl);

            MUNICIPIO.loadContentidoMapa(currentUrl);
        }, 500);
    },
    updateSelectWithoutTrigger: function (selectId, value) {
        const selectElement = $(selectId);

        isUpdating = true; // Activar flag para evitar loop
        selectElement.off("change"); // Desactivar temporalmente eventos onchange
        selectElement.val(value).trigger("change"); // Actualizar valor
        selectElement.on("change", function () {
            MUNICIPIO.updateUrlMunicipio(this); // Restaurar evento
        });

        setTimeout(() => {
            isUpdating = false; // Desactivar flag después de un breve retraso
        }, 300);
    },
    loadContentidoMapa: function (url) {
        $.ajax({
            url: url.toString(),
            type: "GET",
            success: function (response) {
                const updatedContent = $(response).find("#contenido-mapa").html();
                $("#contenido-mapa").html(updatedContent);

                const divConsolidado = $(response).find("#divConsolidado").html();
                $("#divConsolidado").html(divConsolidado);

                const cardHeaderCompleto = $(response).find("#cardHeaderCompleto").html();
                $("#cardHeaderCompleto").html(cardHeaderCompleto);
            },
            error: function (error) {
                console.error("Error al cargar contenido:", error);
            }
        });
    }
};

document.addEventListener("DOMContentLoaded", function () {
    aplicarColorVeredasDesdeTabla();
});
