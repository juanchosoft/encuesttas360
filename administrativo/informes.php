<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';

include './admin/classes/Informes.php';

//Información de Vistas
$arr = Informes::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Informes';

?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">


  <body>
    <main class="main" id="top">
    <?php
    include './admin/include/navbar.php';
    ?>
        <?php
    include './admin/include/header.php';
    ?>
      <div class="content">
            
            <div class="row">
                <!-- [ sample-page ] start -->
                <div class="col-sm-12">
                    <div class="card">

                        <div class="card-header">
                            <h5>Listado</h5>
                            <div class="card-header-right">
                                <div class="btn-group card-option">
                                    <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="feather icon-more-horizontal"></i>
                                    </button>
                                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                        <li class="dropdown-item full-card"><a href="#!"><span><i
                                                        class="feather icon-maximize"></i> maximize</span><span
                                                    style="display:none"><i class="feather icon-minimize"></i>
                                                    Restore</span></a></li>
                                        <li class="dropdown-item minimize-card"><a href="#!"><span><i
                                                        class="feather icon-minus"></i> collapse</span><span
                                                    style="display:none"><i class="feather icon-plus"></i>
                                                    expand</span></a></li>
                                        <li class="dropdown-item reload-card"><a href="#!"><i
                                                    class="feather icon-refresh-cw"></i> reload</a></li>
                                        <li class="dropdown-item close-card"><a href="#!"><i
                                                    class="feather icon-trash"></i> remove</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">

                                <div class="card-body table-border-style">
                                    <!-- Tabla de datos -->
                                    <div class="table-responsive">
                                        <table id="dynamictable" class="table table-hover mb-0">
                                            <thead>
                                                <tr class="border-1">
                                                     <th>id</th>
                                                    <th>Mapa</th>
                                                    <th>Municipio</th>
                                                    <th>Vereda</th>
                                                    <th>Tipo</th>
                                                    <th>Zona</th>
                                                    <th>Nombre</th>
                                                    <th>Modo Reporte</th>
                                                    <th>Secretaria</th>
                                                    <th>Secretario</th>
                                                    <th>Observaciones </th>
                                                    <th>Fotos </th>
                                                    <th>Fecha </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($isvalid): ?>
                                                <?php
                                                    $imgBasePath = "assets/img/admin/";
                                                    foreach ($arr as $item):
                                                        $img = !empty($item["img"]) ? $imgBasePath . htmlspecialchars($item["img"]) : 'dist/img/santander.png';
                                                    ?>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-geo">
                                                            <img src="assets/iconos/geo.png" alt="" width="30px">
                                                        </button>
                                                        
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['municipio']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['nombre_vereda']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['tipo']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['zona']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['modoReporte']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['secretaria']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['secretario']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['observaciones']); ?></td>
                                                    <td>
                                                        <?php if (!empty($item['imagenes']) && is_array($item['imagenes'])): ?>
                                                        <?php foreach ($item['imagenes'] as $img): ?>
                                                        <img src="<?php echo htmlspecialchars($img['ruta_imagen']); ?>"
                                                            alt="Imagen informe" width="40"
                                                            class="img-thumbnail img-click"
                                                            data-img="<?php echo htmlspecialchars($img['ruta_imagen']); ?>">
                                                        <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ============================================================== -->
                        <!-- end campaign activities   -->
                        <!-- ============================================================== -->
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- footer -->
                <!-- ============================================================== -->

            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->
    </div>
    </div>
    <!-- [ Main Content ] end -->

    <!-- modal de geocalizacion -->
    <div class="card-body">
        <div id="modalGeocalizacion" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="modalGeocalizacionTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGeocalizacionTitle">Geolocalización Informe<span
                                id="nombrePilar"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="map" style="height: 600px; width: 100%;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal para mostrar imagen -->
    <div class="modal fade" id="modalImagen" tabindex="-1" role="dialog" aria-labelledby="modalImagenLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Vista previa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenModal" src="" alt="Imagen ampliada" class="img-fluid rounded shadow-sm"
                        style="max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script type="text/javascript" src="admin/js/detalle_visitas.js"></script>

    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <?php include './admin/include/generic_dataTables.php'; ?>
    <!-- Google Maps JavaScript API -->
    <!-- Google Maps JavaScript API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0">
    </script>
    <script>
        
        let map;
        let trafficLayer, transitLayer, bicycleLayer;
        var informacionMapaFactores = [];

        function initMap(longitud, latitud, icono) {
            if (typeof google !== 'undefined' && google.maps) {
                // Coordenadas por defecto
                const defaultLocation = {
                    lat: 1.146794,
                    lng: -76.64787
                };
                // Si las coordenadas están definidas, usarlas; sino, usar las coordenadas por defecto
                const initialLocation = {
                    lat: latitud ? parseFloat(latitud) : defaultLocation.lat,
                    lng: longitud ? parseFloat(longitud) : defaultLocation.lng
                };
                // Determinar el icono a usar
                let iconUrl = "assets/iconos/maps/geo.png";
                if (icono && icono.trim() !== "") {
                    iconUrl = icono;
                }
                // Crear el mapa
                map = new google.maps.Map(document.getElementById("map"), {
                    center: initialLocation,
                    zoom: 15,
                });
                // Agregar un solo marcador en el punto seleccionado
                const marker = new google.maps.Marker({
                    position: initialLocation,
                    map: map,
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(60, 60)
                    }
                });
            } else {
                console.error('Google Maps API no está disponible.');
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.img-click').forEach(function(img) {
                img.addEventListener('click', function() {
                    const src = this.getAttribute('data-img');
                    document.getElementById('imagenModal').src = src;
                    $('#modalImagen').modal('show');
                });
            });
        });
        $('#modalGeocalizacion').on('shown.bs.modal', function () {
    if (typeof google !== 'undefined' && map) {
        google.maps.event.trigger(map, 'resize');
        map.setZoom(15); // fuerza el re-centro
    }
});

    </script>
        <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>