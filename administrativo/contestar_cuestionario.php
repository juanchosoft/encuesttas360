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
    $todasFichasTecnicasResult = FichaTecnicaEncuesta::getAll([]);
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
        $todasFichasTecnicasResult = FichaTecnicaEncuesta::getAll([]);
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

        <!-- Barra de progreso -->
        <div class="progress-bar-custom">
            <div class="progress-bar-fill" id="progress_bar" style="width: 0%;"></div>
        </div>

        <!-- Formulario -->
        <form id="form_cuestionario">
            <input type="hidden" name="ficha_tecnica_id" value="<?= $fichaTecnicaId ?>">

            <div id="preguntas_container">
                <?php if (count($preguntas) > 0): ?>
                    <?php foreach ($preguntas as $index => $pregunta): ?>
                        <div class="pregunta-card" data-pregunta-id="<?= $pregunta['id'] ?>">
                            <div class="d-flex align-items-start mb-3">
                                <div class="pregunta-numero"><?= $index + 1 ?></div>
                                <div class="flex-grow-1">
                                    <div class="pregunta-texto">
                                        <?= htmlspecialchars($pregunta['texto_pregunta']) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones de respuesta -->
                            <div class="opciones-container">
                                <?php
                                // Determinar tipo de input según tipo_pregunta
                                $tipoPreguntaOriginal = $pregunta['tipo_pregunta'];
                                $inputType = 'radio'; // Por defecto radio button

                                // Detectar si debe usar checkbox (solo para Seleccion_Multiple_multiple_respuesta)
                                if ($tipoPreguntaOriginal === 'Seleccion_Multiple_multiple_respuesta') {
                                    $inputType = 'checkbox';
                                }

                                // Si no hay opciones, validar si el tipo requiere opciones
                                if (empty($pregunta['opciones']) || !is_array($pregunta['opciones'])):
                                    // Si el tipo de pregunta requiere opciones pero no tiene, mostrar advertencia
                                    if (in_array($tipoPreguntaOriginal, ['Dicotomica', 'Preguntas_Ordinales', 'Preguntas_Cardinales', 'Seleccion_Multiple_unica_respuesta', 'Seleccion_Multiple_multiple_respuesta'])):
                                ?>
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        <strong>Esta pregunta no tiene opciones de respuesta configuradas.</strong>
                                        <br><small>Por favor, contacta al administrador para que agregue las opciones desde el módulo de Preguntas.</small>
                                    </div>
                                <?php
                                    else:
                                        // Para preguntas abiertas, mostrar textarea
                                ?>
                                    <textarea class="form-control respuesta-texto"
                                              name="respuesta_texto_<?= $pregunta['id'] ?>"
                                              placeholder="Escribe tu respuesta aquí..."
                                              data-pregunta-id="<?= $pregunta['id'] ?>"></textarea>
                                <?php
                                    endif;
                                else:
                                    // Mostrar opciones desde la base de datos (ya vienen como array procesado)
                                    foreach ($pregunta['opciones'] as $opcion):
                                ?>
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input"
                                               type="<?= $inputType ?>"
                                               name="respuesta_<?= $pregunta['id'] ?><?= $inputType === 'checkbox' ? '[]' : '' ?>"
                                               id="opcion_<?= $opcion['id'] ?>"
                                               value="<?= $opcion['id'] ?>"
                                               data-pregunta-id="<?= $pregunta['id'] ?>">
                                        <label class="form-check-label" for="opcion_<?= $opcion['id'] ?>">
                                            <?= htmlspecialchars($opcion['texto']) ?>
                                        </label>
                                    </div>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fa-solid fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>No hay preguntas disponibles</h4>
                        <p>Este cuestionario aún no tiene preguntas asociadas.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($preguntas) > 0): ?>
            <!-- Selección del Votante -->
            <div class="respondiente-card">
                <h5 class="mb-4"><i class="fa-solid fa-user-check me-2"></i>Selecciona el Votante</h5>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="tbl_votante_id" class="form-label fw-semibold">Votante *</label>
                        <select class="form-select" id="tbl_votante_id" name="tbl_votante_id" required>
                            <option value="">Cargando votantes...</option>
                        </select>
                        <small class="text-muted mt-1 d-block">Solo se muestran votantes que no han contestado este cuestionario</small>
                    </div>
                </div>
            </div>

            <!-- Botón enviar -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-submit">
                    <i class="fa-solid fa-paper-plane me-2"></i>Enviar Respuestas
                </button>
            </div>
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (!$mostrarSelector): ?>
    <!-- Script del módulo -->
    <script src="admin/js/contestar_cuestionario.js"></script>
    <?php endif; ?>
</body>
</html>
