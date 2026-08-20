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
include './admin/classes/Colombia.php';
include './admin/classes/Ciudad.php';
require './admin/classes/Departamento.php';
include './admin/db/colores.php';
include './admin/classes/Main.php';
include './admin/classes/Detalle.php';
include './admin/classes/Cuenta.php';
include './admin/classes/Cuentapro.php';
include './admin/classes/Munnovisitados.php';
include './admin/include/menu_movil_vistas.php';




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

//informacion del mail
$arr = Main::getDataMain(null);
$isvalid = $arr['output']['valid'];
$visitas = $arr['output']['visitas'];
$apoyos = $arr['output']['apoyos'];
$municipios = $arr['output']['municipios'];
$veredas = $arr['output']['veredas'];
$provincia = $arr['output']['provincia'];
$porcentaje_veredas = $arr['output']['porcentaje_veredas'];
$porcentaje_municipios = $arr['output']['porcentaje_municipios'];
$inversionsec = $arr['output']['inversionsec'];
$valorproyectos = $arr['output']['valorproyectos'];
$secretaria = $arr['output']['secretaria'];
$sumaproyectos = $arr['output']['sumaproyectos'];
$visitarpendiente = 125 - $municipios;


$departamento = new Departamento();
$santander = $departamento->getAll(["id" => 27]);
$santander = $santander["output"]["response"]["0"];
$code = null;
$mapa = null;

if (isset($_GET['depto_id']) && in_array($_GET['depto_id'], [1, 12, 27])) {
    switch ($_GET['depto_id']) {

        case '21':
            $code = $santander["codigo_departamento"];
            $mapa = "admin/mapa-santander/mapa.php";
            break;
    }
}
if (!is_null($code)) {
    $arr = Ciudad::getAll(array('codigo_departamento' => $code));
    $finalMunicipios = $arr['output']['response'];
    $arrApoyoDep = Ciudad::getApoyoByCodigoDepartamento(array('codigo_departamento' => $code));
}

// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<body class="dashboard-body">
  <style>
    .mapaClick {
  transition: all 0.2s ease-in-out;
  transform-origin: center;
}

.mapaClick:hover {
  stroke: rgb(0, 238, 255);
  stroke-width: 2px;
  filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
  cursor: pointer;
}

  </style>
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ navigation menu ] start -->
  <?php
    include './admin/include/navbar.php';
    ?>
  <!-- [ navigation menu ] end -->
  <!-- [ Header ] start -->
  <?php
    include './admin/include/header.php';
    ?>
  <!-- [ Header ] end -->
  <div class="content">
    <div class="pb-5">
      <div class="col-12 col-xxl-20">
        <div class="mb-8">
            <div class="d-flex align-items-center mb-2">
            <h2 class="mb-0 me-3">Información Visitas - <?php echo $nombreProyecto; ?></h2>
            <?php if (!empty($logo)): ?>
              <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 55px;" class="img-fluid img-thumbnail">
            <?php endif; ?>
            </div>
          <!-- <h5 class="text-body-tertiary fw-semibold">Aquí encontrarás información Importante sobre tu métricas</h5> -->
        </div>
        <div class="row align-items-center g-4">
          <!-- ITEM 1 -->
          <div class="col-12 col-md-auto text-center">
            <div class="d-flex flex-column align-items-center">
              <span class="fa-stack" style="min-height: 46px;min-width: 46px; cursor: pointer;" data-bs-toggle="modal"
                data-bs-target="#modalVisitas">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-success-light"
                  data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-success"
                  data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-star text-success"
                  data-fa-transform="shrink-2 up-8 right-6"></span>
              </span>
              <small class="text-body-secondary mt-2">Ver más</small>
              <h4 class="mt-2">Total Visitas Departamento</h4>
            </div>
          </div>

          <!-- ITEM 2 -->
          <div class="col-12 col-md-auto text-center">
            <div class="d-flex flex-column align-items-center">
              <span class="fa-stack" style="min-height: 46px;min-width: 46px; cursor: pointer;" data-bs-toggle="modal"
                data-bs-target="#modalPendientes">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-warning-light"
                  data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-warning"
                  data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-pause text-warning"
                  data-fa-transform="shrink-2 up-8 right-6"></span>
              </span>
              <small class="text-body-secondary mt-2">Ver más</small>
              <h4 class="mt-2">Visitas realizadas a Municipios</h4>
            </div>
          </div>

          <!-- ITEM 3 -->
          <div class="col-12 col-md-auto text-center">
            <div class="d-flex flex-column align-items-center">
              <span class="fa-stack" style="min-height: 46px;min-width: 46px; cursor: pointer;" data-bs-toggle="modal"
                data-bs-target="#modalSinSeguimiento">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-danger-light"
                  data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-danger"
                  data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-xmark text-danger"
                  data-fa-transform="shrink-2 up-8 right-6"></span>
              </span>
              <small class="text-body-secondary mt-2">Ver más</small>
              <h4 class="mt-2">Municipios Restantes por visitar</h4>
            </div>
          </div>

          <!-- ITEM 4 -->
          <div class="col-12 col-md-auto text-center">
            <div class="d-flex flex-column align-items-center">
              <span class="fa-stack" style="min-height: 46px; min-width: 46px; cursor: pointer;"
                onclick="window.location.href='plan_desarrollo.php'">
                <span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-primary-light"
                  data-fa-transform="down-4 rotate--10 left-4"></span>
                <span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-primary"
                  data-fa-transform="up-4 right-3 grow-2"></span>
                <span class="fa-stack-1x fa-solid fa-chart-line text-primary"
                  data-fa-transform="shrink-2 up-8 right-6"></span>
              </span>
              <small class="text-body-secondary mt-2">Ver más</small>
              <h4 class="mt-2">Metas Plan Desarrollo</h4>
            </div>
          </div>

          <!-- Gráfica principales de provincias y municipios -->
          <div class="row g-4 mt-4">
            <!-- Gráfica: Visitas realizadas a provincias -->
            <div class="col-12 col-md-6">
              <div class="card h-100 shadow-none border">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h5 class="mb-1">Visitas realizadas a subregiones</h5>

                    </div>
                    <!-- Aquí podrías mostrar un total si lo tienes -->
                    <!-- <h4>2.345</h4> -->
                  </div>
                  <div class="pt-4">
                    <div id="containerProvincias" style="height: 520px; width: 100%;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Gráfica: Visitas por mes a municipios -->
            <div class="col-12 col-md-6">
              <div class="card h-100 shadow-none border">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h5 class="mb-1">Visitas por mes a municipios</h5>

                    </div>
                    <!-- <h4>1.982</h4> -->
                  </div>
                  <div class="pt-4">
                    <div id="containerMunicipios" style="height: 520px; width: 100%;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- TABLA PUNTAJES-->
          <!-- FILA CON TABLA Y MAPA LADO A LADO -->
          <div class="row g-4 mt-4 align-items-stretch">
            <!-- Tabla de Puntajes -->
            <div class="col-xl-3">
              <div class="card shadow-sm border h-100" data-component-card="data-component-card">
                <div class="card-header p-3 bg-body-tertiary border-bottom">
                  <h5 class="mb-0 text-center d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-sliders2-vertical text-secondary fs-5"></i> Tabla de Puntajes
                  </h5>
                </div>
                <div class="card-body p-3">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle text-center mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th>Color</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>0</td>
                          <td>0</td>
                          <td><span class="color-circle rounded-circle d-inline-block" style="background-color: #ffffff; width: 20px; height: 20px; border: 1px solid #ccc;"></span></td>
                        </tr>
                        <tr>
                          <td>1</td>
                          <td>2</td>
                          <td><span class="color-circle rounded-circle d-inline-block" style="background-color: #dc3545; width: 20px; height: 20px;"></span></td>
                        </tr>
                        <tr>
                          <td>3</td>
                          <td>4</td>
                          <td><span class="color-circle rounded-circle d-inline-block" style="background-color: #ffc107; width: 20px; height: 20px;"></span></td>
                        </tr>
                        <tr>
                          <td>5</td>
                          <td>6</td>
                          <td><span class="color-circle rounded-circle d-inline-block" style="background-color: #0d6efd; width: 20px; height: 20px;"></span></td>
                        </tr>
                        <tr>
                          <td>7</td>
                          <td>+</td>
                          <td><span class="color-circle rounded-circle d-inline-block" style="background-color: #198754; width: 20px; height: 20px;"></span></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="mt-4 d-flex justify-content-center">
                      <button class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalSvgFull">
                      <i class="bi bi-arrows-fullscreen"></i> Ver Mapa Completo
                      </button>
                  </div>
                </div>
              </div>
            </div>
         <!-- ESPACIO PARA LLAMAR EL MAPA-->
          <div class="col-xl-9">
            <div class="card shadow-none border" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                  <h4 class="text-body mb-0">Mapa  <?php echo Util::nombreDelProyecto(); ?></h4>
                  </div>
                  <div class="col col-md-auto">
                    <nav class="nav justify-content-end doc-tab-nav align-items-center" role="tablist">
                    </nav>
                  </div>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="p-4 code-to-copy">
                  <div class="position-absolute z-2" style="right:16px">
                    <button class="btn btn-phoenix-secondary btn-sm usa-map-reset"><span
                        class="fas fa-sync-alt fs-9"></span></button>
                  </div>

                    <div class="row">
                                    <div class="col-md-12" style="position: static; overflow-x: auto ">
                                        <div id="contenidoTransformado" class="contenido-transformado">

                                            <!-- Main content -->
                                            <div class="cuerpoMapa w-12">
                                                <div class="santander munis">
                                                    <?php echo require_once "admin/mapa_putumayo/mapa_visitas_gobernador.php"; ?>
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

          <!-- Modal: Total Visitas Departamento -->
          <div id="modalVisitas" class="modal fade" tabindex="-1" aria-labelledby="modalVisitasTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalVisitasTitle">Municipios Visitados</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <?php
      $arr = Detalle::getAll(null);
      $isvalid = $arr['output']['valid'] ?? false;
      $data = $arr['output']['response'] ?? [];
      ?>
                <div class="modal-body">
                  <?php
        //Municipios Restantes por visitar
        $arrMunicipiosRestante = Munnovisitados::getAll(null);
        $isvalid = $arrMunicipiosRestante['output']['valid'];
        $arrMunicipiosRestante = $arrMunicipiosRestante['output']['response'];
        ?>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">Municipio</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                if (isset($arrMunicipiosRestante) && isset($isvalid) && $isvalid) {
                    foreach ($arrMunicipiosRestante as $item) { ?>
                      <tr>
                        <td><?php echo htmlspecialchars($item['municipio']); ?>
                        </td>
                      </tr>
                      <?php }
                } else { ?>
                      <tr>
                        <td>No hay datos disponibles.</td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <?php
include './admin/include/footer.php';
?>
          <!-- Modal: Visitas realizadas a Municipios -->
          <div id="modalPendientes" class="modal fade" tabindex="-1" aria-labelledby="modalPendientesTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalPendientesTitle">Cantidad de visitas a municipios</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <?php
        //Información de visitas muncipios
        $arrVisitasMunicipios = Cuenta::getAll(null);
        $isvalid = $arrVisitasMunicipios['output']['valid'];
        $arrVisitasMunicipios = $arrVisitasMunicipios['output']['response'];
        ?>

                <div class="modal-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">Municipio</th>
                        <th scope="col">Veces Visitado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                    if (isset($arrVisitasMunicipios) && isset($isvalid) && $isvalid) {
                        foreach ($arrVisitasMunicipios as $item) { ?>
                      <tr>
                        <td><?php echo htmlspecialchars($item['municipio']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['CuentaDeid']); ?>
                        </td>
                      </tr>
                      <?php }
                    } else { ?>
                      <tr>
                        <td colspan="2">No hay datos disponibles.</td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal: Municipios restantes por visitar -->
          <div id="modalSinSeguimiento" class="modal fade" tabindex="-1" aria-labelledby="modalSinSeguimientoTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalSinSeguimientoTitle">Municipios Restantes</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Municipio</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($arrMunicipiosRestante as $item): ?>
                      <tr>
                        <td><?= htmlspecialchars($item['municipio']); ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
         

          
      </div>

<!-- MODAL PANTALLA COMPLETA -->
<div class="modal fade" id="modalSvgFull" tabindex="-1" aria-labelledby="modalSvgFullLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen m-0" style="width: 100%;">
    <div class="modal-content bg-white border-0 rounded-0">
      <div class="modal-header bg-primary text-white justify-content-between align-items-center">
        <h5 class="modal-title text-white m-0" id="modalSvgFullLabel">Vista completa del mapa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0">
        <div class="w-100 h-100 overflow-auto p-3" style="background: #f9f9f9;">
          <div id="svg-container-full" class="w-100 h-100 d-flex align-items-center justify-content-start px-4">
            <!-- MAPA -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
      <?php
    include './admin/include/asistentevirtual.php';
    ?>
      </main>

      <!-- Warning Section Ends -->

      <?php include 'admin/include/gerenic_script.php'; ?>
      <!-- jQuery (necesario para AJAX y modales si usas Bootstrap JS) -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      <!-- Bootstrap para modales, botones, etc. -->
      <script src="assets/js/plugins/bootstrap.min.js"></script>

      <!-- FontAwesome y Feather para íconos -->
      <script src="vendors/fontawesome/all.min.js"></script>
      <script src="vendors/feather-icons/feather.min.js"></script>
<!-- Script para pantalla completa -->
<script src="admin/js/pantalla_completa.js"></script>
      <!-- ECharts para las gráficas -->
      <script src="vendors/echarts/echarts.min.js"></script>

      <!-- Phoenix JS para estilos y helpers -->
      <script src="assets/js/phoenix.js"></script>

      <!-- Script de gráficas personalizadas -->
      <script src="admin/js/estado_general.js"></script>

      <script type="text/javascript" src="admin/js/graficos_mapa.js"></script>
      <script src="admin/js/estado_general.js"></script>
      <?php include 'admin/include/scriptsgober360.php'; ?>
      <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
      <!-- Inicialización de gráficas -->
     <script>
        $(document).ready(function() {
          //ESTADO_GENERAL.mostrarGraficas();
          // ESTADO_GENERAL.getTotalVisitasPorProvinciaCopia();
          // ESTADO_GENERAL.getTotalVisitasPorMesDelDepartamentoCopia();
          // ESTADO_GENERAL.mostrarGraficasMunicipiosVeredasPorColor();
          // ESTADO_GENERAL.getPromedioPs2025PorSecretaria();
        });
      </script> 

</body>

</html>