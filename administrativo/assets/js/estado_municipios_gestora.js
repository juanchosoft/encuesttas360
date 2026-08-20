$(document).on('ready', init);
var q;
let MUN;
let selectedMunicipio;
function init() {
    q = {};
}

var ESTADO_MUN_GESTORA = {
    updateUrlMunicipio: function (item) {

        const selectedMunicipio = item.value || MUN;

        // Validar si el valor seleccionado ya está en la URL
        const currentUrl = new URL(window.location.href);
        const actualMunicipio = currentUrl.searchParams.get('mun');
        if (selectedMunicipio === actualMunicipio) return; // Evitar cambios innecesarios

        currentUrl.searchParams.set('mun', selectedMunicipio);
        window.history.pushState({}, '', currentUrl);

        window.location = currentUrl;
    }
}

