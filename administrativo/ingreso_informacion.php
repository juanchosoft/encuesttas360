<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Departamento.php';
include './admin/classes/Factores.php';

// Permisos
$view = SessionData::getPermission(61);
$create = SessionData::getPermission(62);
$edit = SessionData::getPermission(63);

if (!$view) {
    require 'permiso_denegado.php';
}


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

// Información de Factores
$arrFactores = Factores::getAll(null);
$isvalid = $arrFactores['output']['valid'];
$arrFactores = $arrFactores['output']['response'];
$optionFactores = '<option value="seleccione">Seleccione...</option>';
foreach ($arrFactores as $val) {
    $optionFactores .= "<option  class='" . $val['icono'] . "'  value='" . $val['id'] . "'>" . $val['puntaje'] . "  - " . $val['tipo'] . "</option>";
}
?>
<style>
    .controls {
        margin-top: 10px;
        font-family: Arial, sans-serif;
        font-size: 16px;
    }
</style>

  <body>
    <main class="main" id="top">
        <?php include 'admin/include/scriptsgober360.php'; ?>
    <?php
    include './admin/include/navbar.php';
    ?>
        <?php
    include './admin/include/header.php';
    ?>
      <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                    <h4 class="text-body mb-0" data-anchor="data-anchor">
                        <i class="uil uil-file me-2"></i> Ingreso información
                    </h4>
                  </div>
                </div>
              </div>
              <!----- INICIO FORMULARIO INGRESO DE INFORMACION ------>
                <div class="card-body p-0">       
                    <div class="p-4 code-to-copy m-4">    
                    <form id="formingresoinformacion" class="row g-3 mb-6" role="form" autocomplete="false">
                        <input type="hidden" name="op" id="op" />
                        <input type="hidden" name="id" id="id" />
                        <input type="hidden" name="filtro" id="filtro" value="vereda" />
                        <input type="hidden" name="filtroVeredaById" id="filtroVeredaById" value="si" />
                        <div class="col-sm-6 col-md-4">
                            <div class="form-floating">
                                <select class="form-select" onchange="DEPARTAMENTO.getMunicipios();"
                                        id="tbl_departamento_id"
                                    name="tbl_departamento_id">
                                    <?php echo $optionDep; ?>
                                </select>
                        <label for="validationCustom05">Departamento<span class="text-danger mb-1">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select"  id="tbl_municipio_id"
                            onchange="DEPARTAMENTO.getVeredasByMunicipioId();"
                            name="tbl_municipio_id">
                        </select>
                        <label for="validationCustom05">Municipio<span class="text-danger mb-1">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select"  id="tbl_vereda_id"
                            name="tbl_vereda_id">
                        </select>
                        <label for="exampleFormControlSelect1">Vereda<span class="text-danger mb-1">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select"  id="factorId" name="factorId"
                            onchange="INGRESO_INFORMACION.showInfoGetFactores();">
                            <?php echo $optionFactores; ?>
                        </select>
                        <label for="exampleFormControlSelect1">Factores<span class="text-danger mb-1">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                      <input type="text" class="form-control" id="valor" name="valor"
                      placeholder="Ingrese el valor">
                        <label for="Text">Valor<span class="text-danger mb-1">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                      <div class="form-floating">
                      <input type="email" class="form-control" id="longitud"
                        name="longitud" placeholder="" value="">
                        <label  for="validationCustom05">Longitud<span class="text-danger mb-1">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                      <div class="form-floating">
                      <input type="email" class="form-control" id="latitud" name="latitud"
                        placeholder="" value="">
                        <label  for="validationCustom05">Latitud<span class="text-danger mb-1">*</span></label>
                      </div>

                    </div>
                    <div class="col-sm-10 col-md-12 d-flex justify-content-center">
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2" onclick="abrirModal();">
                            <img src="assets/images/geoloca.png" alt="Geolocalización"
                                style="width: 40px; height: 40px; object-fit: contain;">
                            <span style="font-size: 1rem;">Geolocalización</span>
                        </button>
                    </div>
                        <div id="divInformacion" class="card-body"
                            style="display: none; padding: 10px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                            <div class="row"
                                style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <div class="col-sm-3" style="flex: 1; min-width: 150px;">
                                    <label class="floating-label" for="Text"
                                        style="font-size: 14px; font-weight: bold;">Eje</label>
                                    <div class="form-group">
                                        <input id="eje" name="eje" class="form-control"
                                            type="text" placeholder="" readonly=""
                                            style="font-size: 14px; padding: 5px; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-sm-3" style="flex: 1; min-width: 150px;">
                                    <label class="floating-label" for="Text"
                                        style="font-size: 14px; font-weight: bold;">Pilar</label>
                                    <div class="form-group">
                                        <input id="pilar" name="pilar" class="form-control"
                                            type="text" placeholder="" readonly=""
                                            style="font-size: 14px; padding: 5px; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-sm-3" style="flex: 1; min-width: 150px;">
                                    <label class="floating-label" for="Text"
                                        style="font-size: 14px; font-weight: bold;">Área</label>
                                    <div class="form-group">
                                        <input id="area" name="area" class="form-control"
                                            type="text" placeholder="" readonly=""
                                            style="font-size: 14px; padding: 5px; border-radius: 4px;">
                                    </div>
                                </div>
                                <div class="col-sm-3" style="flex: 1; min-width: 150px;">
                                    <label class="floating-label" for="Text"
                                        style="font-size: 14px; font-weight: bold;">Tipo
                                        Medición</label>
                                    <div class="form-group">
                                        <input id="tipo_medicion" name="tipo_medicion"
                                            class="form-control" type="text" placeholder=""
                                            readonly=""
                                            style="font-size: 14px; padding: 5px; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <div class="form-floating" style="width: 100%; max-width: 800px;">
                            <textarea class="form-control" id="observaciones"
                            name="observaciones" placeholder="" value=""></textarea>                       
                            <label for="validationCustom05">Observaciones</label>
                        </div>
                    </div>
                    <!-- FIN DE INGRESO DE INFORMACIÓN -->
                    <!-- INICIO DE SUBIR IMAGENES -->
                    <div class="col-sm-12">
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <div style="width: 240px;">
                            <p class="text-center mb-2 fw-bold">Foto 1</p>
                            <iframe id="ifm1" name="ifm1" src="upload.php" scrolling="no" frameborder="0"
                                    style="width: 100%; height: 250px; overflow: visible;"></iframe>
                            </div>

                            <div style="width: 240px;">
                            <p class="text-center mb-2 fw-bold">Foto 2</p>
                            <iframe id="ifm2" name="ifm2" src="upload.php" scrolling="no" frameborder="0"
                                    style="width: 100%; height: 250px; overflow: visible;"></iframe>
                            </div>

                            <div style="width: 240px;">
                            <p class="text-center mb-2 fw-bold">Foto 3</p>
                            <iframe id="ifm3" name="ifm3" src="upload.php" scrolling="no" frameborder="0"
                                    style="width: 100%; height: 250px; overflow: visible;"></iframe>
                            </div>

                            <div style="width: 240px;">
                            <p class="text-center mb-2 fw-bold">Foto 4</p>
                            <iframe id="ifm4" name="ifm4" src="upload.php" scrolling="no" frameborder="0"
                                    style="width: 100%; height: 250px; overflow: visible;"></iframe>
                            </div>
                        </div>
                    <!-- FIN DE SUBIR IMAGENES -->
                     <!-- INICIO BOTON CANCELAR Y GUARDAR  -->
                        <div class="col-12 p-3">
                            <div class="row g-3 justify-content-end">
                                <div class="col-auto">
                                    <button type="button" onclick="UTIL.clearForm('formingresoinformacion');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                                </div>
                                <?php if ($create && $edit): ?>
                                <div class="col-auto">
                                    <button class="btn btn-primary px-5" type="button" onclick="INGRESO_INFORMACION.save();">Guardar</button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <!-- FIN BOTON GUARDAR Y CANCELAR -->
               </form> 
            </div>   
         </div>
        </div>
      </div>
    </div>
    <?php
    include './admin/include/footer.php';
    ?>
        <!-- MODAL DE GEOLOCALIZACIÓN -->
        <div class="modal fade" id="modalGeocalizacion" tabindex="-1" aria-labelledby="modalGeocalizacionTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

            <!-- Header estilo Phoenix -->
            <div class="modal-header bg-primary justify-content-between align-items-center position-relative" style="padding-right: 3rem;">
                <h5 class="modal-title text-white m-0 w-100 text-center" id="modalGeocalizacionTitle">Geolocalización</h5>
                <button type="button" class="btn-close btn-close-white position-absolute" style="top: 1rem; right: 1rem; transform: scale(0.7);" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo con mapa y controles -->
            <div class="modal-body">
                <div id="map" style="height: 550px; width: 100%; margin-bottom: 1rem;"></div>

                <!-- Checkboxes horizontales -->
                <div class="controls mb-3 d-flex justify-content-center flex-wrap gap-3" style="gap: 1rem;">
                <div class="form-check" style="display: flex; align-items: center; gap: 0.4rem;">
                    <input class="form-check-input" type="checkbox" id="trafficLayerToggle">
                    <label class="form-check-label" for="trafficLayerToggle" style="color: black;">Capa de Tráfico</label>
                </div>
                <div class="form-check" style="display: flex; align-items: center; gap: 0.4rem;">
                    <input class="form-check-input" type="checkbox" id="transitLayerToggle">
                    <label class="form-check-label" for="transitLayerToggle" style="color: black;">Capa de Transporte Público</label>
                </div>
                <div class="form-check" style="display: flex; align-items: center; gap: 0.4rem;">
                    <input class="form-check-input" type="checkbox" id="bicycleLayerToggle">
                    <label class="form-check-label" for="bicycleLayerToggle" style="color: black;">Capa de Bicicleta</label>
                </div>
                <div class="form-check" style="display: flex; align-items: center; gap: 0.4rem;">
                    <input class="form-check-input" type="checkbox" id="terrainToggle">
                    <label class="form-check-label" for="terrainToggle" style="color: black;">Mostrar Terreno</label>
                </div>
                </div>

                <!-- Coordenadas en línea -->
                <div class="border-top pt-3 mt-3 d-flex justify-content-center gap-4 text-center" style="font-weight: bold; font-size: 1rem;">
                <div><strong>Latitud:</strong> <span id="lat">N/A</span></div>
                <div><strong>Longitud:</strong> <span id="lng">N/A</span></div>
                </div>
            </div>

            </div>
        </div>
        </div>


        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0">
        </script>

        <script>
            let map;
            let marker = null; // marcador global
            let trafficLayer, transitLayer, bicycleLayer;
            let LATITUD = <?php echo Util::getLatitudDepartamentoPrincipal(); ?>;
            let LONGITUD = <?php echo Util::getLongitudDepartamentoPrincipal(); ?>;

            function initMap(lat, lng, icono = "assets/iconos/maps/geo.png") {
                if (typeof google !== 'undefined' && google.maps) {
                    if (lat !== undefined || lng !== undefined) {
                        LATITUD = +lat;
                        LONGITUD = +lng;
                    }

                    const initialLocation = {
                        lat: LATITUD,
                        lng: LONGITUD,
                    };

                    map = new google.maps.Map(document.getElementById("map"), {
                        center: initialLocation,
                        zoom: 12,
                    });

                    map.addListener("click", (event) => {
                        const lat = event.latLng.lat();
                        const lng = event.latLng.lng();

                        $("#latitud").val(lat);
                        $("#longitud").val(lng);

                        document.getElementById("lat").innerText = lat.toFixed(6);
                        document.getElementById("lng").innerText = lng.toFixed(6);

                        const iconUrl = icono;

                        // 🔁 Eliminar marcador anterior si existe
                        if (marker !== null) {
                            marker.setMap(null);
                        }

                        // 🆕 Crear nuevo marcador
                        marker = new google.maps.Marker({
                            position: event.latLng,
                            map: map,
                            icon: iconUrl,
                        });
                    });

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
                } else {
                    console.error('Google Maps API no está disponible.');
                }
            }

            function abrirModal() {
                const msj = "Debes seleccionar todas la opciones para poder abrir la geocalización";
                const camposRequeridos = ["#tbl_departamento_id", "#tbl_municipio_id", "#tbl_vereda_id", "#factorId"];

                if (!UTIL.validarCampos(camposRequeridos)) {
                    UTIL.mostrarMensajeValidacion(msj);
                    return;
                }

                $('#modalGeocalizacion').modal('show');

                let latitud = LATITUD;
                let longitud = LONGITUD;

                if (typeof informacionMunicipio !== 'undefined') {
                    latitud = informacionMunicipio.latitud || LATITUD;
                    longitud = informacionMunicipio.longitud || LONGITUD;
                }

                const $select = $('#tbl_municipio_id');
                const municipioSelectedOption = $select.find('option:selected');
                latitud = municipioSelectedOption.data('latitud') || latitud;
                longitud = municipioSelectedOption.data('longitud') || longitud;

                LATITUD = latitud;
                LONGITUD = longitud;

                const factorClass = $("#factorId").find(":selected").attr("class");
                initMap(latitud, longitud, factorClass);
            }
        </script>

    </div>


    <!-- Required Js -->


    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/ingreso_informacion.js"></script>
    <script>
    setTimeout(function() {
        DEPARTAMENTO.getMunicipios();
    }, 1000);
    </script>
</body>

</html>