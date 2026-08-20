let map;
let trafficLayer, transitLayer, bicycleLayer;
var informacionMapaFactores = [];

function initMap() {
    if (typeof google !== 'undefined' && google.maps) {
        // Obtener las coordenadas de los inputs
        // Coordenadas por defecto (Medellín)
const defaultLocation = {
    lat: 6.255705,
    lng: -75.577576
};

// Verifica si hay datos y el primer punto tiene coordenadas válidas
let initialLocation = defaultLocation;
if (informacionMapaFactores.length > 0) {
    const primerPunto = informacionMapaFactores.find(p =>
        parseFloat(p.latitud) && parseFloat(p.longitud)
    );
    if (primerPunto) {
        initialLocation = {
            lat: parseFloat(primerPunto.latitud),
            lng: parseFloat(primerPunto.longitud)
        };
    }
}

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
        // Agregar marcadores para los puntos del objeto
        const data = informacionMapaFactores;
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
        // Eventos para los checkboxes
        document.getElementById("trafficLayerToggle").addEventListener("change", (e) => {
            if (e.target.checked) {
                trafficLayer.setMap(map);
            } else {
                trafficLayer.setMap(null);
            }
        });
        document.getElementById("transitLayerToggle").addEventListener("change", (e) => {
            if (e.target.checked) {
                transitLayer.setMap(map);
            } else {
                transitLayer.setMap(null);
            }
        });
        document.getElementById("bicycleLayerToggle").addEventListener("change", (e) => {
            if (e.target.checked) {
                bicycleLayer.setMap(map);
            } else {
                bicycleLayer.setMap(null);
            }
        });
        document.getElementById("terrainToggle").addEventListener("change", (e) => {
            if (e.target.checked) {
                map.setMapTypeId("terrain"); // Cambia el tipo de mapa a terreno
            } else {
                map.setMapTypeId("roadmap"); // Cambia el tipo de mapa a carreteras
            }
        });
    } else {
        console.error('Google Maps API no está disponible.');
    }
}

function mostrarInformacionPilarByMunicipio() {
    q = {};
    q.op = "getmapapilaresbymunicipioId";
    q.pilarId = $("#pilarId").val();
    q.codigoMunicipio = $("#tbl_municipio_id").val();
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
                informacionMapaFactores = res;
                if (informacionMapaFactores.length > 0) {
                    $("#nombrePilar").empty().append(informacionMapaFactores[0]['pilar']);
                }
                initMap();
            } else {
                UTIL.mostrarMensajeError(data.output.response.content);
            }
        },
    });
}