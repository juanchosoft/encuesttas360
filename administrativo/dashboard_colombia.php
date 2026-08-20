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
  $final = str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);
  $exists = strpos($final, "?");
  if ($exists == !false) {
    $final = substr($final, 0, $exists);
    return $final;
  } else {
    return $final;
  }
}

require_once './admin/include/generic_classes.php';
include './admin/db/colores.php';
include './admin/classes/Main.php';
include './admin/classes/MapaConfig.php';

// Permisos
$permissions = [
  'view' => SessionData::getPermission(1),
  'create' => SessionData::getPermission(2),
  'edit' => SessionData::getPermission(3),
];

// Validación de permiso de visualización
if (!$permissions['view']) {
  require_once 'permiso_denegado.php';
  exit;
}


try {
  $codigoDepartamento = isset($_REQUEST['dep']) ? $_REQUEST['dep'] : Util::getDepartamentoPrincipal();
  $mapaMostrar = MapaConfig::obtenerRutaMapa($codigoDepartamento);
} catch (InvalidArgumentException $e) {
  echo "<script>
      alert('Información enviada no es correcta');
      window.location = 'dashboard.php';
  </script>";
  exit;
}



// Información del main
$arr = Main::getDataMain(['codigoDepartamento' => $codigoDepartamento]);
$isvalid = $arr['output']['valid'];
$visitas = $arr['output']['visitas'];
$lideres = $arr['output']['lideres'];
$municipios = $arr['output']['municipios'];
$inscritos = $arr['output']['inscritos'];
$reuniones = $arr['output']['reuniones'];
$departamentoInfo = $arr['output']['departamento'];

// Información del proyecto
$config = Util::getInformacionConfiguracion();
$nombreProyecto = $config[0]['nombre_proyecto'] ?? '';
$logo = $config[0]['logo'] ?? '';
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<body>
<main class="main" id="top">
      <?php include './admin/include/navbar.php'; ?>
      <?php include './admin/include/header.php'; ?>

  <div class="content" style="padding: 50px !important;">
    <div class="row mt-5">
        <div class="col-12 mb-4">
          <h3 class="mb-3 d-flex justify-content-center align-items-center" style="font-weight:600; gap:10px;">
            <i class="bi bi-speedometer2" style="font-size:1.5rem;"></i>
            Dashboard — Departamento de <?php echo $departamentoInfo; ?>
          </h3>
          <hr style="
              border: none;
              border-top: 3px solid #0d6efd;
              width: 100%;
              margin: 0;
              opacity: 1;
          ">
        </div>
        <!-- Columna izquierda: tarjetas de métricas -->
        <div class="col-12 col-lg-2">
          <div class="card  shadow-none border">
            <div class="card-body">
              <div class="d-flex flex-column gap-3">

                <!-- Participación ciudadana -->
                <div class="card shadow-sm border-0">
                  <div class="card-body p-2 text-center">
                    <i class="bi bi-people text-info fs-3"></i>
                    <p class="mb-0 fs-10 text-muted">Participación Ciudadana</p>
                    <h5 class="mb-0">1,254</h5>
                  </div>
                </div>

                <!-- Votaciones registradas -->
                <div class="card shadow-sm border-0">
                  <div class="card-body p-2 text-center">
                    <i class="bi bi-check2-square text-success fs-3"></i>
                    <p class="mb-0 fs-10 text-muted">Votaciones Registradas</p>
                    <h5 class="mb-0">842</h5>
                  </div>
                </div>

                <!-- Proyectos activos -->
                <div class="card shadow-sm border-0">
                  <div class="card-body p-2 text-center">
                    <i class="bi bi-bar-chart-fill text-primary fs-3"></i>
                    <p class="mb-0 fs-10 text-muted">Proyectos Activos</p>
                    <h5 class="mb-0">37</h5>
                  </div>
                </div>


                <!-- Inscritos -->
                <div class="card shadow-sm border-0">
                  <div class="card-body p-2 text-center">
                    <i class="bi bi-people-fill text-warning fs-3"></i>
                    <p class="mb-0 fs-10 text-muted">Inscritos</p>
                    <h5 class="mb-0"><?php echo (int)$inscritos; ?></h5>
                  </div>
                </div>

                <!-- Líderes -->
                <div class="card shadow-sm border-0">
                  <div class="card-body p-2 text-center">
                    <i class="bi bi-person-badge-fill text-danger fs-3"></i>
                    <p class="mb-0 fs-10 text-muted">Líderes</p>
                    <h5 class="mb-0"><?php echo (int)$lideres; ?></h5>
                  </div>
                </div>

                <!-- Reuniones -->
                <div class="card shadow-sm border-0">
                  <div class="card-body p-2 text-center">
                    <i class="bi bi-calendar-event-fill text-primary fs-3"></i>
                    <p class="mb-0 fs-10 text-muted">Reuniones</p>
                    <h5 class="mb-0"><?php echo (int)$reuniones; ?></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Columna derecha-->
        <div class="col-12 col-lg-10">
          <div class="card shadow-none border">
            <div class="card-body" style="padding:0px">

              <div class="cuerpoMapa w-100">
                <div class="santander munis">
                  <?php require_once $mapaMostrar; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 mt-5">

          <hr style="
              border: none;
              border-top: 3px solid #0d6efd;
              width: 100%;
              opacity: 1;
              margin-bottom: 15px;
          ">
        </div>
    </div>
  </div>
</main>

<?php include 'admin/include/gerenic_script.php'; ?>
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
<script>
var navbarTopStyle = window.config?.config?.phoenixNavbarTopStyle;
var navbarTop = document.querySelector('.navbar-top');
if (navbarTop && navbarTopStyle === 'darker') {
  navbarTop.setAttribute('data-navbar-appearance', 'darker');
}
var navbarVerticalStyle = window.config?.config?.phoenixNavbarVerticalStyle;
var navbarVertical = document.querySelector('.navbar-vertical');
if (navbarVertical && navbarVerticalStyle === 'darker') {
  navbarVertical.setAttribute('data-navbar-appearance', 'darker');
}
</script>
<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>

<!-- Luego tu script -->
<script src="admin/js/dashboard.js"></script>

<!-- prism / charts -->
<script src="assets/js/plugins/prism.js"></script>
<script src="assets/js/plugins/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<?php include 'admin/include/scriptsgober360.php'; ?>

<script>
  loadChartData();
</script>
</html>
