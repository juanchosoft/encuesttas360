<?php
include './admin/include/head.php';
function getUrl()
{
    $port = $_SERVER["SERVER_PORT"];
    $nameServer = $port != "80" ? $_SERVER['SERVER_NAME'] . ":" . $port : $_SERVER['SERVER_NAME'];
    $url = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $nameServer,
        $_SERVER['REQUEST_URI']
    );
    $final =  str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);
    $exists = strpos($final, "?");
    if ($exists == !false) {
        $final =  substr($final, 0, $exists);
        return $final;
    } else {
        return $final;
    }
}

require_once './admin/include/generic_classes.php';
require './admin/classes/Departamento.php';
include './admin/db/colores.php';
include './admin/classes/MainTic.php';

// Obtener permisos
$permissions = [
    'view' => SessionData::getPermission(70),
    'create' => SessionData::getPermission(71),
    'edit' => SessionData::getPermission(72),
];

// Validación de permiso de visualización
if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}


// Información de Departamentos
$arrDep = Departamento::getAll(null);
$isvalid = $arrDep['output']['valid'];
$arrDep = $arrDep['output']['response'];
$optionDep = Util::getDepartamentoPrincipal();

foreach ($arrDep as $val) {
    $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}


$codigoMunicipio = $_REQUEST['mun'];
$opcionFiltro = $_REQUEST['opcion'];
$parametrosTic = ['codigoMunicipio' => $codigoMunicipio, 'departamentoId' => Util::getDepartamentoPrincipal(), 'opcion' => $opcionFiltro];

//informacion del main
$arr = MainTic::getDataMain($parametrosTic);
$isvalid = $arr['output']['valid'];
$robotica = $arr['output']['robotica'];
$institucion = $arr['output']['institucion'];
$alumno = $arr['output']['alumno'];
$laboratorio = $arr['output']['laboratorio'];
$inversionsec = $arr['output']['inversionsec'];
$valorproyectos = $arr['output']['valorproyectos'];
$secretaria = $arr['output']['secretaria'];
$sumaproyectos = $arr['output']['sumaproyectos'];

$santander =  $arr['output']['response'];
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">


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
                                    <h4 class="text-body mb-0 d-flex align-items-center" ><i class="uil uil-monitor fs-6"></i>Dashboard TIC</h4>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="card-body">
                            <h4 class="text-center mb-4" style="font-size: 28px;">
                                <i style="color:red" class="fas fa-map-marker-alt mr-2"></i> Filtrar por Municipios
                            </h4>
                                <input type="hidden" name="op" id="op" />
                                <input type="hidden" name="id" id="id" />
                                <input type="hidden" name="filtro" id="filtro" value="vereda" />
                                <input type="hidden" name="filtroVeredaById" id="filtroVeredaById" value="si" />

                               
                                <div class="row g-3 mx-0 px-2">
                                     <div class="col-sm-6 col-md-6">
                                            <div class="form-floating">
                                                <select onchange="DEPARTAMENTO.getMunicipios();" class="form-control"
                                                 id="tbl_departamento_id"
                                                name="tbl_departamento_id">
                                                <?php echo $optionDep; ?>
                                            </select>
                                                <label for="tbl_departamento_id">Departamento<span class="text-danger">*</span></label>
                                            </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                            <div class="form-floating">
                                                <select class="form-control" style="width: 100%;" id="tbl_municipio_id"
                                                onchange="TIC_DASHBOARD.updateUrlMunicipio(this);"
                                                name="tbl_municipio_id"></select>
                                                <label for="tbl_municipio_id">Municipio<span class="text-danger">*</span></label>
                                            </div>
                                    </div>
                                </div>
                            </div>

                <div class="col-md-12 col-xl-12">
                    <center>
                        <div>
                        <div class="d-flex justify-content-center" id="containerDataTic" name="containerDataTic">
                        <div class="card" style="max-width: 90rem; width: 100%;">
                        <div class="card-header text-center">
                                    <h4 class="mb-0" style="font-size:27px">Resumen General TIC</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center justify-content-center">
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/robot.png" alt="" width="60px">
                                        <h6 class="mt-2">Kits Robótica Entregados</h6>
                                        <h4><?php echo number_format($robotica, 0); ?></h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/computadoresi.png" alt="" width="60px">
                                        <h6 class="mt-2">Computadores Instituciones</h6>
                                        <h4><?php echo number_format($institucion, 0); ?></h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/computadoresa.png" alt="" width="60px">
                                        <h6 class="mt-2">Computadores Alumnos</h6>
                                        <h4><?php echo number_format($alumno, 0); ?></h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/laboratoriosi.png" alt="" width="60px">
                                        <h6 class="mt-2">Laboratorios Innovación</h6>
                                        <h4><?php echo number_format($laboratorio, 0); ?></h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/optica.png" alt="" width="60px">
                                        <h6 class="mt-2">KM Fibra Óptica Instalada</h6>
                                        <h4>0</h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/internet.png" alt="" width="60px">
                                        <h6 class="mt-2">Instituciones con Internet</h6>
                                        <h4>0</h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/proyectospen.png" alt="" width="60px">
                                        <h6 class="mt-2">Proyectos Pendientes</h6>
                                        <h4>0</h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/contratoseje.png" alt="" width="60px">
                                        <h6 class="mt-2">Contratos en Ejecución</h6>
                                        <h4>0</h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/inversion.png" alt="" width="60px">
                                        <h6 class="mt-2">Inversión Secretaría</h6>
                                        <h4><?php echo number_format($inversionsec, 0); ?></h4>
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <img src="assets/img/totalpro.png" alt="" width="60px">
                                        <h6 class="mt-2">Valor Total Proyectos</h6>
                                        <h4><?php echo number_format($valorproyectos, 0); ?></h4>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>

            <!--             <div class="col-md-12 col-xl-12">
                <center>
                    <h5 class="card-header">Tabla de Valores de Referencia</h5>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Desde</th>
                                        <th scope="col">Hasta</th>
                                        <th scope="col">Color</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>0</td>
                                        <td>0</td>
                                        <td><span class="color-circle color-white"></span></td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>400</td>
                                        <td><span class="color-circle color-pink"></span></td>
                                    </tr>
                                    <tr>
                                        <td>401</td>
                                        <td>-></td>
                                        <td><span class="color-circle color-green"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </center>
            </div>
 -->
 </center>
            <div class="row">
                <div class="col-md-12" style="position: static; overflow-x: auto ">
                    <div id="contenidoTransformado" class="contenido-transformado">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-12 d-flex justify-content-between align-items-center">
                                            <h4 class="text-body mb-0">Mapa <?php echo Util::nombreDelProyecto(); ?></h4>
                                        <button type="button" class="btn btn-primary mb-3  d-flex align-items-center gap-2" data-toggle="modal" data-target="#modalGeocalizacion">
                                            Geolocalización
                                            <img src="assets/images/geoloca.png" alt="Geolocalización" style="width: 30px; height: 30px; object-fit: contain;">
                                        </button>
                                    </div>
                                    <center>
                                        <h4 class="mb-3">
                                            <i class="bi bi-funnel-fill me-2"></i>Filtrar por opciones
                                        </h4>
                                        <div class="row">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-floating">
                                                    <select onchange="TIC_DASHBOARD.updateUrlOpcion(this)"
                                                            class="form-control" id="opcion" name="opcion">
                                                        <option value="robotica">Robótica</option>
                                                        <option value="computadores_institucion">Computadores Institución</option>
                                                        <option value="computador_alumno">Computador Alumno</option>
                                                        <option value="laboratorio_innovacion">Laboratorio Innovación</option>
                                                        <option value="contratos">Contratos</option>
                                                        <option value="todos">Todos</option>
                                                    </select>
                                                    <label for="opcion">Opciones<span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </center>
                                </div>


                                <!-- Main content -->
                                <div class="cuerpoMapa w-12">
                                    <div class="santander munis">
                                        <div id="contenido-mapa" class="cuerpoMapa w-12">

                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-20 100 1600 974.44"
                                                width="900" height="900">

                                                <?php foreach ($santander as $key => $value) : ?>
                                                <g id="<?php echo strtoupper($value['path']); ?>">
                                                    <path id="<?php echo strtoupper($value['path']); ?>"
                                                        d="<?php echo $value['d']; ?>"
                                                        fill="<?php echo getColorOpcion($value["cantidad_mostrar"]) ?>"
                                                        class="municipios mapaClick <?php echo getClasePorcentaje(0.2); ?>"
                                                        data-url="<?php echo getUrl() . 'municipios_secretarias_tic.php?mun=' . $value['codigo_muncipio'] . '&dep=' . $value['codigo_departamento']; ?>"
                                                        data-name="<?php echo strtolower($value['municipio']); ?>"
                                                        title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_mapa'])); ?>"
                                                        stroke="#000" stroke-miterlimit="10" stroke-width="0.1px">
                                                    </path><text transform="translate(264.48 382.8)"
                                                        font-family="IBM Plex Sans" font-size="10" font-weight="500">
                                                    </text>
                                                </g>
                                                <?php endforeach; ?>

                                                <!-- Coordenadas de los nombres de los municipios -->
                                                <?php require_once 'nombres_mapa_putumayo.php' ?>
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card-body">
        <div id="modalGeocalizacion" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="modalGeocalizacionTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header bg-primary justify-content-between align-items-center">
                    <h5 class="modal-title text-white m-0 w-100 text-center" id="exampleModalCenterTitle">
                    Geolocalización
                    </h5>
                    <button type="button" class="close btn-close-white text-white position-absolute end-0 me-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
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
</div>
    <?php include 'admin/include/footer.php'; ?>
    <?php include 'admin/include/gerenic_script.php'; ?>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <!-- prism Js -->
    <script src="assets/js/plugins/prism.js"></script>
    <script src="assets/js/plugins/apexcharts.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Cargar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Maps JavaScript API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap">
    </script>
    <script type="text/javascript" src="admin/js/tic_dash.js"></script>
    <script type="text/javascript" src="admin/js/departamento.js"></script>
    <script>
        setTimeout(function() {
            DEPARTAMENTO.getMunicipiosOpcionSelectTodos();
        }, 500);
        $("img").each(function(index, el) {
            $(this).attr("data-bs-toggle", "tooltip");
            $(this).attr("data-bs-placement", "left");
            tooltip = new bootstrap.Tooltip($(this)[0], {})
        });
        $(document).off("click", ".mapaClick").on("click", ".mapaClick", function () {
            window.location.href = $(this).data("url");
        });

        function initMap() {
    if (typeof google !== 'undefined' && google.maps) {
        // Coordenadas iniciales
        const initialLocation = {
            lat: <?php echo Util::getLatitudDepartamentoPrincipal(); ?>,
            lng: <?php echo Util::getLongitudDepartamentoPrincipal(); ?>
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
        const data = [];
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
        </script>
        
</body>

</html>
<style>
      #contenido-mapa path:hover,
  #contenido-mapa polygon:hover {
    stroke: rgb(0, 238, 255);
    stroke-width: 2px;
    filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
    cursor: pointer;
    transition: all 0.2s ease-in-out;
  }
    .content-map {
        background-color: #ffffff !important;
        padding: 20px 0;
    }


    #mapa {
        background-color: transparent;
        background-repeat: no-repeat;
        background-position: center;
        width: 100%;
        height: auto;
        margin: 0 auto;
        text-align: center;
        padding: 0.1px 0;
    }

    #mapa svg {
        max-width: 950px;

        width: 100%;

    }

    #mapa svg path {
        fill: #fff;
        transition: all .4s;
    }

    #mapa svg path:hover {
        fill: #636363
    }

    #mapa img {
        position: absolute;
    }
</style>
