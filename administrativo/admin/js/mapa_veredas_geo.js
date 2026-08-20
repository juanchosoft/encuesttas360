let map;
let trafficLayer, transitLayer, bicycleLayer;
var informacionMapaFactores = [];

function initMap(longitud, latitud) {
    if (typeof google !== 'undefined' && google.maps) {

        // // Coordenadas por defecto
        // const defaultLocation = {
        //     lat: 1.146794,
        //     lng: -76.647874
        // };

        // Si las coordenadas están definidas, usarlas; sino, usar las coordenadas por defecto
        const initialLocation = {
            lat: latitud ? parseFloat(latitud) : defaultLocation.lat,
            lng: longitud ? parseFloat(longitud) : defaultLocation.lng
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
    } else {
        console.error('Google Maps API no está disponible.');
    }
}

/**
 * Metodo para mostrar la informacion de pilares por vereda id
 * @param {*} longitud 
 * @param {*} latitude 
 */
function mostrarInformacionPilarByVereda(longitud, latitude) {
    q = {};
    q.op = "getmapapilaresbymunicipioId";
    q.pilarId = $("#pilarId").val();
    q.codigoMunicipio = $("#tbl_municipio_id").val();
    q.veredaId = $("#veredaId").val();
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
                initMap(longitud, latitude);
            } else {
                UTIL.mostrarMensajeError(data.output.response.content);
            }
        },
    });
}