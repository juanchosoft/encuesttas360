<?php

include './admin/include/head.php';

require './admin/include/generic_classes.php';

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
include './admin/classes/Colombia.php';
include './admin/classes/Ciudad.php';
include './admin/classes/Estado.php';
require './admin/classes/Departamento.php';
require './admin/classes/Maing.php';
require './admin/classes/Pilar.php';
require './admin/classes/Mapa.php';
include './admin/db/coloress.php';
include './admin/include/menu_movil_vistas.php';

// Obtener permisos
$permissions = [
    'view' => SessionData::getPermission(39),
    'create' => SessionData::getPermission(39),
    'edit' => SessionData::getPermission(39),
    'delete' => SessionData::getPermission(39),
];

// Validación de permiso de visualización
if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';


// Informacion del Main
$arr = Maing::getDataMain(null);
$visitas = $arr['output']['visitas'];
$impactada = $arr['output']['impactada'];
$inversion = $arr['output']['inversion'];


$pilar = $_REQUEST['pilar'];
$codigoTodos = Util::codigoTodos();

// Información de Pilares
$response = Pilar::getAll(null);
if (!empty($response['output']['valid'])) {
    $arrPilar = $response['output']['response'];
    
    // Agregar opción "Todos" al inicio
    $optionPilar = "<option value='$codigoTodos'" . ($pilar == $codigoTodos ? " selected" : "") . ">Todos</option>";

    // Generar las demás opciones
    $optionPilar .= array_reduce($arrPilar, function ($carry, $val) use ($pilar) {
        $selected = ($val['id'] == $pilar) ? ' selected' : '';
        return $carry . "<option value='{$val['id']}'{$selected}>{$val['nombre']}</option>";
    }, '');
} else {
    // Solo la opción "Todos" si no hay datos
    $optionPilar = "<option value='$codigoTodos '" . ($pilar == $codigoTodos ? " selected" : "") . ">Todos</option>";
}
$codigo_departamento = Util::getDepartamentoPrincipal();

// Informacion del departmento
if (isset($pilar) && !empty(trim($pilar))) {
    // Obtener información de mapa
    $arr = ['codigo_departamento' => $codigo_departamento, 'pilarId' => $pilar];
    if ($pilar == $codigoTodos) {
        $dataDepartamento = Colombia::calcularColorDelDepartamentoTodosLosPilares($arr);
    } else {
        $dataDepartamento = Colombia::calcularColorDelDepartamentoByPilarId($arr);
    }

    $isvalidDepartamento = $arr['output']['valid'] ?? false;
    $mapaDepartamento = $dataDepartamento['output']['response'] ?? null;
} else { ?>
<script type='text/javascript'>
  alert('Información enviada no es correcta');
  window.location =
    'departamentos.php?pilar=<?php echo $pilar ; ?>';
</script>
<?php
}

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<body class="">
  <?php include 'admin/include/scriptsgober360.php'; ?>
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <?php
    include './admin/include/navbar.php';
    ?>
  <?php
    include './admin/include/header.php';
    ?>

  <div class="content">
    <div class="row gy-3 mb-6 justify-content-between align-items-center">
      <div class="col-md-9 col-auto d-flex align-items-center">
        <h2 class="mb-2 me-3 text-body-emphasis">
          Acción Unificada <?php echo $nombreProyecto; ?>
        </h2>

        <?php if (!empty($logo)): ?>
        <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;"
          class="img-fluid img-thumbnail">
        <?php endif; ?>
      </div>

      <div class="col-12">
        <h5 class="text-body-tertiary fw-semibold">
          Mapa interactivo por <strong>Pilar</strong>. Visualiza niveles de <strong>ejecución</strong> y
          <strong>afectación</strong> por municipio.
        </h5>
      </div>
    </div>

    <!-- CARD PADRE -->
    <div class="card border-0 shadow-none bg-transparent">
      <div class="card-body p-0 bg-transparent">
        <div class="row g-4 align-items-stretch">
          <!-- CARD DEL MAPA -->
          <div class="col-12 col-xl-8">
            <div class="card h-100 border">
              <div class="bg-body p-3 rounded shadow-sm border h-100 d-flex flex-column">
                <h5 class="text-center mb-3 d-flex justify-content-center align-items-center gap-2">
                  <i class="bi bi-map fs-5 text-primary"></i> Mapa Interactivo
                </h5>
          
            <div id="contenido-mapa" class="cuerpoMapa flex-grow-1" style="border: 1px solid #ccc; border-radius: 10px; overflow: hidden;">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1595.26 974.44" width="100%" height="auto">
                
                    <?php foreach ($mapaDepartamento as $key => $value) : ?>
                  <g id="<?php echo strtoupper($value['path']); ?>">
                    <path id="<?php echo strtoupper($value['path']); ?>"
                      d="<?php echo $value['d']; ?>"
                      fill="<?php echo ($value['color_calculado']); ?>"
                      class="municipios mapaClick <?php echo getClasePorcentaje(0.2); ?>"
                      data-url="<?php echo getUrl() . 'municipios.php?mun=' . $value['codigo_muncipio'] . '&dep=' . $value['codigo_departamento']; ?>&pilar=<?php echo $pilar; ?>"
                      data-name="<?php echo strtolower($value['municipio']); ?>"
                      title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_mapa'])); ?>"
                      stroke="#000" stroke-miterlimit="10" stroke-width="0.4px"></path>
                  </g>
                    <?php endforeach; ?>
                    <!-- Informacion de los nombres de los municipios -->
                    <?php
                    if ($codigo_departamento === '86') {
                      require_once 'nombres_mapa_putumayo.php';
                    } elseif ($codigo_departamento === '05') {
                      require_once 'nombres_mapa_antioquia.php';
                    }
                    ?>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- CARD DEL CONTROL DEL MAPA -->
          <div class="col-12 col-xl-4">
            <div class="card h-100 bg-light border">
              <div
                class="bg-light-subtle dark:bg-dark-subtle rounded shadow-sm border h-100 d-flex flex-column justify-content-between"
                style="padding: 40px !important;">
                <h6
                  class="text-body fw-bold text-uppercase text-center d-flex align-items-center justify-content-center gap-2 mb-3">
                  <i class="bi bi-sliders2-vertical fs-5 text-primary"></i> Control del Mapa
                </h6>

                <div class="form-floating mb-4">
                  <select class="form-select" id="pilarId" name="pilarId" onchange="updateUrlPilar(this)">
                    <?php echo $optionPilar ?>
                  </select>
                  <label for="pilarId">Selecciona un Pilar</label>
                </div>

                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-toggle="modal"
                  data-target="#modalGeocalizacion">
                  <img src="assets/images/geoloca.png" alt="Geolocalización"
                    style="width: 30px; height: 30px; object-fit: contain;">
                  <span>Geolocalización</span>
                </button>
                <button type="button" class="btn btn-danger d-flex align-items-center gap-2 mt-3" data-toggle="modal"
                  data-target="#modalMapaCalor">
                  <img src="assets/images/calor.png" alt="Mapa Calor"
                    style="width: 30px; height: 30px; object-fit: contain;">
                  <span>Mapa Calor</span>
                </button>

                <div class="text-center mt-auto">
                  <div class="d-flex justify-content-center">
                    <div class="table-responsive content-center">
                      <?php include './admin/classes/tabla_rangos_colores.php'; ?>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!--  MODAL GEOLOCALIZACION -->
    <div class="modal fade" id="modalGeocalizacion" tabindex="-1" aria-labelledby="modalGeocalizacionTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary justify-content-between align-items-center">
            <h5 class="modal-title text-white m-0 w-100 text-center" id="modalGeocalizacionTitle">
              Geolocalización para pilar: <span id="nombrePilar"></span>
            </h5>
            <button type="button" class="close btn-close-white text-white position-absolute end-0 me-3"
              data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>

          </div>

          <div class="modal-body">
            <div id="map" style="height: 600px; width: 100%;"></div>
          </div>
        </div>
      </div>
    </div>
    <!--  Script -->
    <?php if (isset($_GET["route_map"])): ?>
    <?php endif ?>

    <script>
      document.getElementById("btnAumentar").onclick = function() {
        aumentarTransform();
      };
      document.getElementById("btnReducir").onclick = function() {
        reducirTransform();
      };

      function aumentarTransform() {
        var elemento = document.getElementById("contenidoTransformado");
        var escalaActual = parseFloat(window.getComputedStyle(elemento).getPropertyValue("transform").split(
          ",")[3]);
        var nuevaEscala = escalaActual + 0.1; // Aumentar la escala en 0.1
        elemento.style.transform = "scale(" + nuevaEscala + ")";
      }

      function reducirTransform() {
        var elemento = document.getElementById("contenidoTransformado");
        var escalaActual = parseFloat(window.getComputedStyle(elemento).getPropertyValue("transform").split(
          ",")[3]);
        var nuevaEscala = escalaActual - 0.1; // Reducir la escala en 0.1
        if (nuevaEscala >= 0.1) { // Evitar escala negativa
          elemento.style.transform = "scale(" + nuevaEscala + ")";
        }
      }
    </script>

    <?php include 'admin/include/gerenic_script.php'; ?>

    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <!-- Google Maps JavaScript API -->
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap">
    </script>
    <script>
      const defaultLocation = {
        lat: <?php echo Util::getLatitudDepartamentoPrincipal(); ?>,
        lng : <?php echo Util::getLongitudDepartamentoPrincipal(); ?>
      };
    </script>

    <script type="text/javascript" src="admin/js/mapa_departamento.js"></script>
    <script>
      //Cuando se selecciona un pilar se recarga para no tener que hacer doble click en el mapa 
      document.getElementById("pilarId").addEventListener("change", function() {
        var selectedValue = this.value;
        var url = new URL(window.location.href);
        url.searchParams.set('pilar', selectedValue);
        window.location.href = url.toString();
      });
    </script>
    <script>
      $(document).on("ready", function() {
        cargarColoresVeredas(); // Cargar colores al iniciar
      });

      function pintarVeredas(data) {
        $(".veredaMun").removeClass("color-critico color-alto color-estable color-grave").addClass("color-vacio");
        data.forEach((vereda) => {
          const clase = vereda.nombre_svg?.toLowerCase();
          const color = vereda.color_calculado;
          if (!clase || !color) return;
          const $img = $("." + clase);
          $img.removeClass("color-vacio");
          switch (color) {
            case "#ff0000":
              $img.addClass("color-critico");
              break;
            case "#ffa500":
              $img.addClass("color-alto");
              break;
            case "#00ff00":
              $img.addClass("color-estable");
              break;
            case "#800080":
              $img.addClass("color-grave");
              break;
          }
        });
      }
    </script>

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
    </style>

    </html>