<!-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input[type="password"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Restablecer Contraseña</h2>
        <form action="procesar-reseteo.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

            <label for="password">Nueva Contraseña:</label><br>
            <input type="password" id="password" name="password" required><br>
            
            <label for="confirm_password">Confirmar Contraseña:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br>
            
            <button type="submit">Cambiar Contraseña</button>
        </form>
    </div>
</body>
</html> -->

<?php
include './admin/classes/Util.php';
include './admin/include/head.php';
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Acción Unificada - Gobernación de Santander</title>
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="../assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/libs/css/style.css">
    <link rel="stylesheet" href="../assets/libs/css/stylenew.css">
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="icon" href="./assets/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/custom-styles.css">
</head>

<body>
    <div class="video-background">
        <video autoplay muted loop>
            <source src="assets/vid/antioquia.mp4" type="video/mp4">
            Tu navegador no soporta la reproducción de videos.
        </video>
    </div>
    <div class="auth-wrapper login-wrapper">
        <div class="auth-content text-center">

            <div class="card borderless login-card">
                <div class="row">
                    <div class="col-md-6 login-form">

                        
                        <form class="form" method="POST" action="procesar-reseteo.php">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                            <img src="assets/img/estadistica3.png" alt="" width="200px" style="display: block; margin: 0 auto; width: 40%;">
                            <h1 style="display: block;text-align: center; color:white;" class="welcome-title">Cambia tu contraseña</h1>
                            
                            <hr>
                            <div class="form-group mb-3">
                                <input style="border-radius: 10px;" type="password" class="form-control" id="password" name="password" placeholder="Nueva Contraseña" required>
                            </div>

                            <div class="form-group mb-4">
                                <input style="border-radius: 10px;" type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmar Contraseña" required>
                            </div>

                            <div class="form-check text-left mb-4 mt-2 d-flex justify-content-between align-items-center">
                                <a></a>
                                <a href="login.php" style="font-size: 13px; color: white; text-decoration: none;">Volver al inicio</a>
                            </div>                            
                            
                            <button type="submit" style="background-color: #28a745;color: white; font-size: 16px; margin-top: 15px !important;display: block; margin: 0 auto; width: 280px;" class="btn btn-primary1 mb-4">
                            Cambiar contraseña
                            </button>

                            <!-- <hr> -->
                            <!-- <p class="mb-2 text-muted">¿Olvidaste tu Contraseña? <a href="auth-reset-password.html" class="f-w-400 red-text">Renuevala</a></p> -->
                        </form>
                    </div>
                    <div class="col-md-6 login-image">
                        <img src="assets/images/putumayoimg.png" alt="Imagen Derecha" class="img-right">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
</body>

</html>