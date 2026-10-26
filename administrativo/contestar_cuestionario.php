<?php
require_once 'admin/include/head.php';
require_once './admin/include/generic_classes.php';
include './admin/classes/Pregunta.php';
include './admin/classes/FichaTecnicaEncuesta.php';


// Obtener ID de ficha técnica desde URL
$fichaTecnicaId = isset($_GET['f']) ? intval($_GET['f']) : 0;

// Si no viene ID, obtener todas las fichas técnicas disponibles para mostrar selector
$todasFichasTecnicas = [];
$mostrarSelector = false;

if ($fichaTecnicaId === 0) {
    $mostrarSelector = true;
    $todasFichasTecnicasResult = FichaTecnicaEncuesta::getAll(['solo_habilitados' => true]);
    if ($todasFichasTecnicasResult['output']['valid']) {
        $todasFichasTecnicas = $todasFichasTecnicasResult['output']['response'];
    }
}

// Variables para el cuestionario
$fichaTecnica = null;
$preguntas = [];

// Si viene ID de ficha técnica, cargar su información
if ($fichaTecnicaId > 0) {
    // Obtener información de la ficha técnica
    $fichaTecnicaResult = FichaTecnicaEncuesta::getAll(['id' => $fichaTecnicaId]);
    if ($fichaTecnicaResult['output']['valid'] && !empty($fichaTecnicaResult['output']['response'])) {
        $fichaTecnica = $fichaTecnicaResult['output']['response'][0];

        // Obtener preguntas de la ficha técnica
        $preguntasResult = Pregunta::getAll(['tbl_ficha_tecnica_encuesta_id' => $fichaTecnicaId]);
        if ($preguntasResult['output']['valid']) {
            $preguntas = $preguntasResult['output']['response'];
        }
    } else {
        // Si el ID no es válido, mostrar selector
        $mostrarSelector = true;
        $todasFichasTecnicasResult = FichaTecnicaEncuesta::getAll(['solo_habilitados' => true]);
        if ($todasFichasTecnicasResult['output']['valid']) {
            $todasFichasTecnicas = $todasFichasTecnicasResult['output']['response'];
        }
    }
}

// Información del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuestionario - <?= htmlspecialchars($fichaTecnica['tema'] ?? 'Sin título') ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root {
        --primary: #292F36;     /* Color profesional principal */
        --primary-light: #3b434d;
        --primary-soft: #eceff1;

        --bg-main: #F5F7FA;     /* Fondo profesional */
        --bg-card: #FFFFFF;
        --text-main: #1F2937;
        --text-muted: #6B7280;
        --border-soft: #E5E7EB;

        --shadow-soft: 0 4px 18px rgba(0,0,0,0.08);
    }

    /* === BODY === */
    body {
        background: var(--bg-main);
        min-height: 100vh;
        padding: 40px 0;
        color: var(--text-main);
        font-family: "Inter", sans-serif;
    }

    .cuestionario-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    /* === HEADER === */
    .header-cuestionario {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-soft);
        text-align: center;
        border: 1px solid var(--border-soft);
    }

    .header-cuestionario h1 {
        font-weight: 700;
        color: var(--primary);
    }

    .header-cuestionario p {
        color: var(--text-muted);
        font-size: 15px;
    }

    /* === CARD DE PREGUNTA === */
    .pregunta-card {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 28px;
        margin-bottom: 22px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-soft);
        transition: all .25s ease;
    }

    .pregunta-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
    }

    .pregunta-numero {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        font-size: 17px;
        font-weight: 600;
        margin-right: 14px;
    }

    .pregunta-texto {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-main);
    }

    /* === OPCIONES === */
    .form-check {
        padding: 16px 18px;
        border: 1.8px solid var(--border-soft);
        border-radius: 12px;
        margin-bottom: 12px;
        transition: all .25s ease;
        cursor: pointer;
        background: white;
        display: flex;
        align-items: center;
    }

    .form-check:hover {
        background: var(--primary-soft);
        border-color: var(--primary);
    }

    .form-check-input:checked + .form-check-label {
        color: var(--primary);
        font-weight: 600;
    }

    .form-check-input {
        width: 20px;
        height: 20px;
        margin-top: 0;
        cursor: pointer;
        accent-color: var(--primary);
    }

    .form-check-label {
        margin-left: 12px;
        font-size: 15px;
    }

    /* === BADGES === */
    .badge-tipo {
        background: var(--primary);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
    }

    .badge-obligatoria {
        background: #d63031;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
    }

    /* === BOTÓN SUBMIT === */
    .btn-submit {
        background: var(--primary);
        border: none;
        padding: 16px 55px;
        border-radius: 50px;
        font-size: 17px;
        font-weight: 600;
        color: white;
        box-shadow: 0 8px 20px rgba(41,47,54,0.35);
        transition: all .25s ease;
    }

    .btn-submit:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(41,47,54,0.45);
    }

    /* === PROGRESS BAR === */
    .progress-bar-custom {
        height: 9px;
        background: var(--border-soft);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--primary);
        transition: width .3s ease;
    }

    /* === INFO FICHA === */
    .info-ficha {
        background: var(--bg-card);
        padding: 22px;
        border-radius: 10px;
        border-left: 4px solid var(--primary);
        margin-bottom: 28px;
        box-shadow: var(--shadow-soft);
        font-size: 14px;
    }

    .info-ficha h6 {
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 10px;
    }

    .info-ficha p {
        color: var(--text-muted);
    }

    /* === RESPONDIENTE CARD === */
    .respondiente-card {
        background: var(--bg-card);
        padding: 28px;
        border-radius: 14px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-soft);
    }

    /* === TEXTAREA === */
    textarea.form-control {
        border-radius: 12px;
        border: 1.8px solid var(--border-soft);
        padding: 15px;
        font-size: 15px;
    }

    textarea.form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(41,47,54,0.15);
    }

    /* === SELECTOR DE FICHA === */
    .selector-ficha-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 40px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-soft);
    }

    .ficha-item {
        padding: 20px;
        border: 1.8px solid var(--border-soft);
        border-radius: 12px;
        margin-bottom: 16px;
        cursor: pointer;
        transition: all .25s ease;
    }

    .ficha-item:hover {
        border-color: var(--primary);
        background: var(--primary-soft);
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }

    .ficha-item h5 {
        color: var(--primary);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .ficha-item p {
        color: var(--text-muted);
        font-size: 14px;
    }

    .btn-seleccionar-ficha {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        transition: all .25s ease;
    }

    .btn-seleccionar-ficha:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(41,47,54,0.4);
    }
</style>

</head>
<body>

    <div class="cuestionario-container" id="cuestionario_container" data-ficha-tecnica-id="<?= $fichaTecnicaId ?>">

        <?php if ($mostrarSelector): ?>
            <!-- Selector de Ficha Técnica -->
            <div class="header-cuestionario">
                <?php if (!empty($logo)): ?>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 70px; margin-bottom: 20px;">
                <?php endif; ?>
                <h1 class="mb-3">Selecciona un Cuestionario</h1>
                <p class="text-muted">Elige la ficha técnica de encuesta que deseas responder</p>
            </div>

            <div class="selector-ficha-card">
                <?php if (count($todasFichasTecnicas) > 0): ?>
                    <?php foreach ($todasFichasTecnicas as $ficha): ?>
                        <div class="ficha-item" onclick="window.location.href='contestar_cuestionario.php?f=<?= $ficha['id'] ?>'">
                            <h5><i class="fa-solid fa-clipboard-question me-2"></i><?= htmlspecialchars($ficha['tema']) ?></h5>
                            <?php if (!empty($ficha['realizada_por_o_encomendada_por'])): ?>
                            <p><strong>Realizada por:</strong> <?= htmlspecialchars($ficha['realizada_por_o_encomendada_por']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($ficha['fecha_realizacion'])): ?>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($ficha['fecha_realizacion'])) ?></p>
                            <?php endif; ?>
                            <div class="mt-3">
                                <button class="btn btn-seleccionar-ficha" onclick="event.stopPropagation(); window.location.href='contestar_cuestionario.php?f=<?= $ficha['id'] ?>'">
                                    Comenzar Cuestionario <i class="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fa-solid fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>No hay cuestionarios disponibles</h4>
                        <p>No se encontraron fichas técnicas de encuesta en el sistema.</p>
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Header del Cuestionario -->
            <div class="header-cuestionario">
                <?php if (!empty($logo)): ?>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 70px; margin-bottom: 20px;">
                <?php endif; ?>
                <h1 class="mb-3"><?= htmlspecialchars($fichaTecnica['tema'] ?? 'Cuestionario') ?></h1>
                <p class="text-muted">Por favor, responda las siguientes preguntas</p>
            </div>
        <?php endif; ?>

        <?php if (!$mostrarSelector): ?>
        <!-- Información de la Ficha Técnica -->
        <div class="info-ficha">
            <h6><i class="fa-solid fa-info-circle me-2"></i>Información de la Encuesta</h6>
            <?php if (!empty($fichaTecnica['realizada_por_o_encomendada_por'])): ?>
            <p><strong>Realizada por:</strong> <?= htmlspecialchars($fichaTecnica['realizada_por_o_encomendada_por']) ?></p>
            <?php endif; ?>
            <?php if (!empty($fichaTecnica['fecha_realizacion'])): ?>
            <p><strong>Fecha de realización:</strong> <?= date('d/m/Y', strtotime($fichaTecnica['fecha_realizacion'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($fichaTecnica['tamano_muestra'])): ?>
            <p><strong>Tamaño de muestra:</strong> <?= htmlspecialchars($fichaTecnica['tamano_muestra']) ?> personas</p>
            <?php endif; ?>
        </div>

        <!-- Selección del Votante -->
        <div class="respondiente-card mb-4">
            <h5 class="mb-3"><i class="fa-solid fa-user-check me-2"></i>1. Selecciona el Votante</h5>
            <div class="row align-items-center">
                <div class="col-md-7 col-12 mb-2">
                    <label for="tbl_votante_id" class="form-label fw-semibold">Votante *</label>
                    <select class="form-select" id="tbl_votante_id" name="tbl_votante_id" required>
                        <option value="">Cargando votantes disponibles...</option>
                    </select>
                    <small class="text-muted mt-1 d-block">Solo se muestran personas que no han respondido esta encuesta.</small>
                </div>
                <div class="col-md-5 col-12 mb-2">
                    <label class="form-label fw-semibold">Ubicación del votante</label>
                    <div id="textoUbicacionVotante" class="form-control bg-light text-muted" style="font-size:14px;">
                        Seleccione un votante para ver su municipio
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de progreso -->
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small fw-semibold text-muted"><i class="fa-solid fa-tasks me-1"></i>Progreso del cuestionario</span>
            <span class="small fw-bold" id="progreso_texto" style="color:var(--primary);">0%</span>
        </div>
        <div class="progress-bar-custom mb-4">
            <div class="progress-bar-fill" id="progress_bar" style="width: 0%;"></div>
        </div>

        <!-- Formulario -->
        <form id="form_cuestionario">
            <input type="hidden" name="ficha_tecnica_id" id="ficha_tecnica_id" value="<?= $fichaTecnicaId ?>">
            <input type="hidden" name="cuestionarioDefinicionHash" id="cuestionarioDefinicionHash" value="">

            <div id="preguntas_container">
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-spinner fa-spin fa-2x mb-3 d-block" style="color:var(--primary);"></i>
                    <p class="mb-0">Cargando preguntas del cuestionario...</p>
                </div>
            </div>

            <!-- Botón enviar -->
            <div class="text-center mt-4 mb-5" id="contenedorBtnEnviar">
                <button type="submit" class="btn btn-submit" id="btnEnviarCuestionario">
                    <i class="fa-solid fa-paper-plane me-2"></i>Enviar Respuestas
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (!$mostrarSelector): ?>
    <!-- Motor y script del módulo -->
    <script src="admin/js/lib/motor_cuestionario.js"></script>
    <script src="admin/js/lib/motor_cuestionario_ui.js"></script>
    <script src="admin/js/contestar_cuestionario.js"></script>
    <?php endif; ?>
</body>
</html>
