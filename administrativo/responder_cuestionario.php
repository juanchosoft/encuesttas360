<?php
require_once './admin/include/generic_classes.php';
include './admin/classes/Pregunta.php';

// Obtener código del cuestionario desde URL
$codigoCuestionario = isset($_GET['c']) ? $_GET['c'] : '';

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
    <title>Responder Cuestionario - <?= $nombreProyecto ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .cuestionario-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .pregunta-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .pregunta-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        .pregunta-numero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 15px;
        }
        .form-check {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            transition: background 0.2s ease;
        }
        .form-check:hover {
            background: #f8f9fa;
        }
        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 0.25rem;
        }
        .form-check-label {
            font-size: 16px;
            margin-left: 10px;
            cursor: pointer;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 50px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            color: white;
        }
        .header-cuestionario {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .badge-obligatoria {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-left: 10px;
        }
        .progress-bar-custom {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>

    <div class="cuestionario-container" id="cuestionario_container" data-codigo-cuestionario="<?= $codigoCuestionario ?>">
        <!-- Loader inicial -->
        <div class="text-center text-white" id="loader">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Cargando cuestionario...</p>
        </div>

        <!-- Contenido del cuestionario (se carga dinámicamente) -->
        <div id="cuestionario_content" style="display: none;">
            <!-- Header -->
            <div class="header-cuestionario">
                <?php if (!empty($logo)): ?>
                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo" style="height: 60px; margin-bottom: 20px;">
                <?php endif; ?>
                <h1 class="mb-3" id="cuestionario_titulo"></h1>
                <p class="text-muted" id="cuestionario_descripcion"></p>
            </div>

            <!-- Barra de progreso -->
            <div class="progress-bar-custom">
                <div class="progress-bar-fill" id="progress_bar" style="width: 0%;"></div>
            </div>

            <!-- Formulario -->
            <form id="form_cuestionario">
                <div id="preguntas_container"></div>

                <!-- Información del respondiente -->
                <div class="pregunta-card">
                    <h5 class="mb-4"><i class="fa-solid fa-user me-2"></i>Información del Respondiente</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_respondiente" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre_respondiente" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="identificacion_respondiente" class="form-label">Identificación *</label>
                            <input type="text" class="form-control" id="identificacion_respondiente" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_respondiente" class="form-label">Email (Opcional)</label>
                            <input type="email" class="form-control" id="email_respondiente">
                        </div>
                    </div>
                </div>

                <!-- Botón enviar -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fa-solid fa-paper-plane me-2"></i>Enviar Respuestas
                    </button>
                </div>
            </form>
        </div>

        <!-- Mensaje de error -->
        <div id="error_message" style="display: none;">
            <div class="alert alert-danger text-center">
                <i class="fa-solid fa-exclamation-triangle fa-3x mb-3"></i>
                <h4>Cuestionario no encontrado</h4>
                <p>El enlace del cuestionario no es válido o ha expirado.</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script del módulo -->
    <script src="admin/js/responder_cuestionario.js"></script>
</body>
</html>
