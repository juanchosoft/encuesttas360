<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/include/generic_info_configuracion.php';
require './admin/classes/PreguntaGrilla.php';

if (isset($_POST['registro_data']) && !empty($_POST['registro_data'])) {
    $itemJson = $_POST['registro_data'];
    $grilla = json_decode($itemJson, true);
    if ($grilla === null && json_last_error() !== JSON_ERROR_NONE) {
        die("Error al decodificar los datos JSON: " . json_last_error_msg());
    }
} else {
    echo "Error: Acceso inválido o no se recibieron datos del registro.";
    exit;
}

// Cargar preguntas y subpreguntas dinámicamente desde BD
$grilla_id = isset($grilla['id']) ? $grilla['id'] : 0;
$preguntasResponse = PreguntaGrilla::obtenerPreguntasConSubpreguntas(['grilla_id' => $grilla_id]);
$preguntasPrincipales = [];
$subpreguntas = [];

if ($preguntasResponse['output']['valid']) {
    $preguntasPrincipales = $preguntasResponse['output']['response']['preguntas'];
    $subpreguntas = $preguntasResponse['output']['response']['subpreguntas'];
}
?>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ navigation menu ] start -->
    <?php include './admin/include/navbar.php'; ?>
    <!-- [ navigation menu ] end -->

    <!-- [ Header ] start -->
    <?php include './admin/include/header.php'; ?>
    <!-- [ Header ] end -->

    <div class="content">
        <div class="col-12 mx-auto px-3">
            <!-- Header con título y botón actualizar -->
            <div class="card mb-3">
                <div class="card-header" style="background: #cbd0dd; color: #3e465b;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-chart-bar"></i>
                                Resultados en Tiempo Real: <?php echo htmlspecialchars($grilla['grilla'] ?? 'Estudio de Votaciones'); ?>
                            </h5>
                            <small><?php echo htmlspecialchars($grilla['descripcion_grilla'] ?? ''); ?></small>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-end mt-2">
                            <button class="btn btn-light" id="btnActualizarResultados">
                                <i class="fas fa-sync-alt"></i> Actualizar
                            </button>
                            <button class="btn btn-dark" onclick="window.location.href='grilla.php';">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center flex-wrap text-center text-sm-start mb-2 mb-sm-0">
                        <span class="badge badge-success me-2 mb-2 mb-sm-0">
                            <i class="fas fa-clock"></i>
                            <label class="text-muted"> Actualización automática cada 10 segundos
                        </span>
                        <small class="text-muted ms-sm-2">
                            Última actualización:
                            <strong id="ultimaActualizacion">--:--:--</strong>
                        </small>
                        </div>
                        <div class="text-center text-sm-end">
                            <span class="badge badge-primary badge-lg px-3 py-2" style="color: #31374a;">
                                <i class="fas fa-users"></i>
                                <strong id="totalVotantes">0</strong>
                                <label> personas han votado
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row principal con 3 columnas -->
            <div class="row">
                <!-- Columna 1: Estadísticas generales por candidato -->
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header" style="background: #cbd0dd;">
                            <h6 class="mb-0"><i class="fas fa-users"></i> Estadísticas por Candidato</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 text-center align-middle" id="tablaCandidatos">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Candidato</th>
                                            <th class="text-center">Total Votos Preguntas</th>
                                            <?php
                                            // Renderizar columnas dinámicamente desde BD
                                            foreach ($preguntasPrincipales as $pregunta) {
                                                $textoPregunta = htmlspecialchars($pregunta['texto_pregunta']);
                                                echo '<th class="text-center">' . $textoPregunta . '</th>';
                                            }
                                            ?>
                                            <th class="text-center">Aprobaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyCandidatos">
                                        <tr>
                                            <td colspan="<?php echo (4 + count($preguntasPrincipales)); ?>" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                                <p class="mb-0">Cargando resultados...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr id="trTotales">
                                            <td colspan="2" class="text-center"><strong>TOTALES</strong></td>
                                            <td class="text-center"><strong id="totalVotosGeneral">0</strong></td>
                                            <?php
                                            // Renderizar columnas dinámicamente para totales
                                            foreach ($preguntasPrincipales as $pregunta) {
                                                $codigoPregunta = htmlspecialchars($pregunta['codigo_pregunta']);
                                                echo '<td class="text-center" id="total_' . $codigoPregunta . '">--</td>';
                                            }
                                            ?>
                                            <td class="text-center"><strong id="totalAprobaciones">0</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Columna 2: Subpreguntas (dinámicas desde BD) -->
                <div class="col-lg-4 col-md-12">
                    <?php
                    // Renderizar subpreguntas dinámicamente
                    $colores = ['bg-info', 'bg-warning', 'bg-danger', 'bg-success', 'bg-secondary'];
                    foreach ($subpreguntas as $index => $subpregunta) {
                        $codigoPregunta = htmlspecialchars($subpregunta['codigo_pregunta']);
                        $textoPregunta = htmlspecialchars($subpregunta['texto_pregunta']);
                        $colorHeader = $colores[$index % count($colores)];
                    ?>
                    <div class="card mb-3">
                        <div class="card-header <?php echo $colorHeader; ?> text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-poll"></i>
                                <?php echo strtoupper($codigoPregunta); ?>: <?php echo $textoPregunta; ?>
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <div id="resultados<?php echo strtoupper($codigoPregunta); ?>" data-codigo="<?php echo $codigoPregunta; ?>">
                                <p class="text-center text-muted py-2 mb-0"><small>Cargando...</small></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php include './admin/include/footer.php'; ?>
        </div> 
    </div> 

  <?php include './admin/include/gerenic_script.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <!-- Sistema de Resultados en Tiempo Real -->
  <script src="admin/js/resultados_grilla.js"></script>
  <script>
    // Inicializar el sistema cuando el DOM esté listo
    $(document).ready(function() {
      // Pasar los datos de la grilla desde PHP a JavaScript
      const grillaData = <?php echo json_encode($grilla ?? []); ?>;

      // Inicializar el sistema de resultados
      ResultadosGrilla.init(grillaData);
    });
  </script>
</body>
</html>
