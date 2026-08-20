<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/include/generic_info_configuracion.php';
require './admin/classes/PreguntaGrilla.php';
require './admin/classes/Votantes.php';

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

// Cargar preguntas y subpreguntas desde la base de datos FILTRADAS por grilla_id
$grilla_id = isset($grilla['id']) ? $grilla['id'] : 0;
$preguntasResponse = PreguntaGrilla::obtenerPreguntasConSubpreguntas(['grilla_id' => $grilla_id]);
$preguntasData = [];
$subpreguntasData = [];

if ($preguntasResponse['output']['valid']) {
    $preguntasData = $preguntasResponse['output']['response']['preguntas'];
    $subpreguntasData = $preguntasResponse['output']['response']['subpreguntas'];
}

// Si no hay preguntas configuradas, mostrar error
if (empty($preguntasData)) {
    die("Error: No se encontraron preguntas configuradas en el sistema. Por favor, configure las preguntas desde el panel de administración.");
}

// Cargar votantes activos que NO han votado hoy en esta grilla
$votantesData = [];
$db = new DbConection();
$pdo = $db->openConect();

try {
    $qVotantes = "SELECT v.*
                  FROM " . $db->getTable('tbl_votantes') . " v
                  WHERE v.estado = 'activo'
                    AND v.id NOT IN (
                        SELECT gsv.tbl_votante_id
                        FROM " . $db->getTable('tbl_grilla_sesion_votacion') . " gsv
                        WHERE gsv.tbl_grilla_id = :grilla_id
                          AND DATE(gsv.dtcreate) = CURDATE()
                    )
                  ORDER BY v.nombre_completo ASC";

    $stmt = $pdo->prepare($qVotantes);
    $stmt->execute([':grilla_id' => $grilla_id]);
    $votantesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $votantesData = [];
} finally {
    $db->closeConect();
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
        <div class="row">
            <!-- Columna principal: Tabla de votaciones -->
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($grilla['grilla'] ?? 'Estudio de Votaciones'); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($grilla['descripcion_grilla'] ?? ''); ?></p>
                            </div>
                            <div class="col-md-4">
                                <label for="votanteSelector" class="form-label mb-1"><strong>Simular como votante:</strong></label>
                                <?php if (empty($votantesData)): ?>
                                    <div class="alert alert-warning py-2 mb-0" style="font-size: 12px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>No hay votantes disponibles.</strong><br>
                                        Todos los votantes activos ya han votado hoy en esta grilla.
                                    </div>
                                <?php else: ?>
                                    <select id="votanteSelector" class="form-select form-select-sm" required>
                                        <option value="">-- Seleccione un votante (<?php echo count($votantesData); ?> disponibles) --</option>
                                        <?php foreach ($votantesData as $votante): ?>
                                            <option value="<?php echo $votante['id']; ?>"
                                                    data-ideologia="<?php echo htmlspecialchars($votante['ideologia']); ?>"
                                                    data-genero="<?php echo htmlspecialchars($votante['genero']); ?>"
                                                    data-edad="<?php echo htmlspecialchars($votante['rango_edad']); ?>">
                                                <?php echo htmlspecialchars($votante['nombre_completo']); ?>
                                                (<?php echo htmlspecialchars($votante['ideologia']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="tabla_grilla">
                    <thead class="table-header">
                        <tr>
                            <th>CANDIDATOS</th>
                            <?php
                            // Renderizar headers dinámicamente desde la BD
                            foreach ($preguntasData as $pregunta) {
                                echo '<th>' . htmlspecialchars(strtoupper($pregunta['texto_pregunta'])) . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($grilla['candidatos']) && is_array($grilla['candidatos'])) {
                            foreach ($grilla['candidatos'] as $index => $candidato) {
                                $candidatoId = $candidato['id'];
                                $nombreCompleto = htmlspecialchars($candidato['nombre_completo']);
                                $fotoUrl = !empty($candidato['foto'])
                                    ? 'assets/img/admin/' . htmlspecialchars($candidato['foto'])
                                    : 'assets/img/candidato.png';
                                $partidosPoliticos = htmlspecialchars($candidato['nombres_partidos'] ?? '');
                                $cargoPublico = htmlspecialchars($candidato['cargo_publico'] ?? '');
                        ?>
                        <tr data-candidato-id="<?php echo $candidatoId; ?>">
                            <!-- Columna del candidato -->
                            <td class="candidato-info">
                                <div class="candidato-container">
                                    <img src="<?php echo $fotoUrl; ?>" alt="Foto <?php echo $nombreCompleto; ?>" class="candidato-foto">
                                    <div class="candidato-detalles">
                                        <strong><?php echo $nombreCompleto; ?></strong>
                                    </div>
                                </div>
                            </td>

                            <?php
                            // Renderizar columnas de preguntas dinámicamente
                            foreach ($preguntasData as $indexPregunta => $pregunta) {
                                $codigoPregunta = $pregunta['codigo_pregunta'];
                                $opcionesRespuesta = json_decode($pregunta['opciones_respuesta'], true);
                                $esPrimera = ($indexPregunta === 0);

                                // Determinar si es la primera pregunta (activa por defecto) o siguiente (NO APLICA por defecto)
                                $claseInicial = $esPrimera ? '' : 'no-aplica';
                                $toggleDisplay = $esPrimera ? 'flex' : 'none';
                                $mostrarNoAplica = !$esPrimera;
                            ?>
                            <td data-pregunta="<?php echo $codigoPregunta; ?>"
                                data-orden="<?php echo $pregunta['orden']; ?>"
                                data-habilita-subpreguntas="<?php echo $pregunta['habilita_subpreguntas'] ? '1' : '0'; ?>"
                                data-condicion="<?php echo htmlspecialchars($pregunta['condicion_habilitacion'] ?? ''); ?>"
                                class="<?php echo $claseInicial; ?>">
                                <div class="toggle" style="display: <?php echo $toggleDisplay; ?>;">
                                    <?php
                                    // Renderizar botones según las opciones configuradas
                                    if ($opcionesRespuesta && is_array($opcionesRespuesta)) {
                                        foreach ($opcionesRespuesta as $indexOpcion => $opcion) {
                                            $esSegundaOpcion = ($indexOpcion === 1);
                                            $claseActiva = ($esPrimera && $esSegundaOpcion) ? 'active' : ''; // Segunda opción activa en primera pregunta
                                            $icono = '';

                                            // Determinar icono según la opción
                                            if ($opcion === 'si') {
                                                $icono = '<i class="fas fa-check"></i>';
                                                $claseBoton = 'si';
                                            } elseif ($opcion === 'no') {
                                                $icono = '<i class="fas fa-times"></i>';
                                                $claseBoton = 'no';
                                            } elseif ($opcion === 'favorable') {
                                                $texto = 'SÍ';
                                                $claseBoton = 'si';
                                            } elseif ($opcion === 'desfavorable') {
                                                $texto = 'NO';
                                                $claseBoton = 'no';
                                            } else {
                                                $texto = strtoupper($opcion);
                                                $claseBoton = $indexOpcion === 0 ? 'si' : 'no';
                                            }
                                    ?>
                                    <button class="toggle-btn <?php echo $claseBoton; ?> <?php echo $claseActiva; ?>"
                                            data-value="<?php echo $opcion; ?>">
                                        <?php echo !empty($icono) ? $icono : $texto; ?>
                                    </button>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php if ($mostrarNoAplica): ?>
                                <span class="no-aplica-text">--</span>
                                <?php endif; ?>
                            </td>
                            <?php
                            } // fin foreach preguntas
                            ?>
                        </tr>
                        <?php
                            }
                        } else {
                            $totalColumnas = count($preguntasData) + 1; // +1 para la columna de candidatos
                            echo '<tr><td colspan="' . $totalColumnas . '" class="text-center">No hay candidatos disponibles</td></tr>';
                        }
                        ?>
                    </tbody>
                        </table>

                        <div class="mt-4 text-right">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='grilla.php';">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <button type="button" class="btn btn-primary" id="btnGuardarRespuestas">
                                <i class="fas fa-save"></i> Guardar Respuestas
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna lateral derecha: Candidatos que pasaron todo -->
            <div class="col-lg-4 col-md-12">
                <div class="card card-candidatos-aprobados h-100">
                    <div class="card-header bg-success text-white py-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0">
                                    <i class="fas fa-trophy"></i> CANDIDATOS APROBADOS
                                </h6>
                                <small style="font-size: 10px;">Pasaron todas las preguntas</small>
                            </div>
                            <div class="badge badge-light badge-pill" style="font-size: 16px; padding: 8px 12px;">
                                <strong id="totalAprobados">0</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2" id="candidatosAprobadosContainer" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                        <!-- Aquí se insertarán dinámicamente los candidatos aprobados -->
                        <div class="text-center text-muted py-4" id="mensajeVacio">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p class="mb-0" style="font-size: 12px;">Selecciona las respuestas para ver los candidatos aprobados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include './admin/include/footer.php'; ?>
  </div>

  <?php include './admin/include/gerenic_script.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <!-- Sistema de Votaciones Grilla -->
  <script src="admin/js/votaciones_grilla.js"></script>
  <script>
    // Inicializar el sistema cuando el DOM esté listo
    $(document).ready(function() {
      // Pasar los datos de la grilla desde PHP a JavaScript
      const grillaData = <?php echo json_encode($grilla ?? []); ?>;

      // Pasar configuración de preguntas y subpreguntas desde PHP
      const preguntasConfig = <?php echo json_encode($preguntasData ?? []); ?>;
      const subpreguntasConfig = <?php echo json_encode($subpreguntasData ?? []); ?>;

      // Inicializar el sistema de votaciones con configuración dinámica
      EstudioVotaciones.init(grillaData, preguntasConfig, subpreguntasConfig);
    });
  </script>
</body>
</html>
