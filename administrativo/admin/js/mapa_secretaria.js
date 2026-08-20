let map;
let trafficLayer, transitLayer, bicycleLayer;
var informacionMapaSecretarias = [];

function initMap() {
    if (typeof google !== 'undefined' && google.maps) {
        // Coordenadas iniciales
        const initialLocation = {
            lat: 6.255705,
            lng: -75.577576
        };
        // Crear el mapa
        map = new google.maps.Map(document.getElementById("map"), {
            center: initialLocation,
            zoom: 12,
        });
        // Agregar evento para capturar clic en el mapa
        map.addListener("click", (event) => {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            // Mostrar las coordenadas en pantalla
            document.getElementById("lat").innerText = lat.toFixed(6);
            document.getElementById("lng").innerText = lng.toFixed(6);
            // Agregar un marcador en el punto seleccionado
            new google.maps.Marker({
                position: event.latLng,
                map: map,
            });
        });

        // Inicializar capas (una sola vez)
        trafficLayer = new google.maps.TrafficLayer();
        transitLayer = new google.maps.TransitLayer();
        bicycleLayer = new google.maps.BicyclingLayer();

        const toggleLayer = (layer, isChecked) => {
            layer.setMap(isChecked ? map : null);
        };

        document.getElementById("trafficLayerToggle").addEventListener("change", (e) => {
            toggleLayer(trafficLayer, e.target.checked);
        });

        document.getElementById("transitLayerToggle").addEventListener("change", (e) => {
            toggleLayer(transitLayer, e.target.checked);
        });

        document.getElementById("bicycleLayerToggle").addEventListener("change", (e) => {
            toggleLayer(bicycleLayer, e.target.checked);
        });

        document.getElementById("terrainToggle").addEventListener("change", (e) => {
            map.setMapTypeId(e.target.checked ? "terrain" : "roadmap");
        });


        // Agregar marcadores para los puntos del objeto
        const data = informacionMapaSecretarias;
        data.forEach(point => {
            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(point.latitud),
                    lng: parseFloat(point.longitud)
                },
                map: map,
                icon: {
                    url: point.icono ? point.icono : "assets/iconos/maps/geo.png",
                    scaledSize: new google.maps.Size(60, 60) // Ajusta el tamaño del icono
                },
                title: `${point.municipio} - ${point.nombre_vereda}`
            });
            const infoWindow = new google.maps.InfoWindow({
                content: `
                <div>
                    <h3>${point.municipio}</h3>
                    <p><strong>Vereda:</strong> ${point.nombre_vereda}</p>
                    <p><strong>Tipo:</strong> ${point.tipo}</p>
                    <p><strong>Cantidad:</strong> ${point.valor}</p>
                    <p><strong>Observaciones:</strong> ${point.observaciones}</p>
                </div>
                `
            });

            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        });

        // Inicializar las capas
        trafficLayer = new google.maps.TrafficLayer(); // Capa de tráfico
        transitLayer = new google.maps.TransitLayer(); // Capa de transporte público
        bicycleLayer = new google.maps.BicyclingLayer(); // Capa de bicicletas
    } else {
        console.error('Google Maps API no está disponible.');
    }
}
function mostrarMapaConInfoSecretaria() {
    q = {};
    q.op = "getmapainformacionsecretaria";
    q.secretariaId = $("#secretariaId").val();
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
                let res = data.output.response;
                informacionMapaSecretarias = res;
                initMap();
            } else {
                UTIL.mostrarMensajeError(data.output.response.content);
            }
        },
    });
}

// Inicializar el mapa cuando se abre el modal
$('#modalGeocalizacion').on('shown.bs.modal', function () {
    mostrarMapaConInfoSecretaria();
});



