<?php

require_once 'vendors/phpmailer/PHPMailer.php';
require_once 'vendors/phpmailer/Exception.php';
require_once 'vendors/phpmailer/SMTP.php';

include './admin/classes/DbConection.php';
include './admin/classes/Util.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


$db_connection = new DbConection();

try {
    $conn = $db_connection->openConect();
    $db_name = $db_connection->getDbName();
    $conn->exec("USE " . $db_name);
    
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}


if (!isset($_POST['nickname']) || empty($_POST['nickname'])) {
    echo "<script>alert('Por favor, ingrese un correo electrónico.'); window.location.href = 'recuperar-contrasena.php';</script>";
    exit;
}

$correo_del_usuario = $_POST['nickname'];
//validar si existe el usuario
$consulta_correo = $conn->prepare("SELECT id FROM tbl_usuarios WHERE nickname = :nickname LIMIT 1");
$consulta_correo->bindParam(':nickname', $correo_del_usuario);
$consulta_correo->execute();
$usuario_existe = $consulta_correo->fetch(PDO::FETCH_ASSOC);

if (!$usuario_existe) {
    echo "<script>alert('El correo electrónico no está registrado en Gob360 Putumayo, intente otra vez.'); window.location.href = 'recuperar-contrasena.php';</script>";
    exit;
   
} else {


    $token = bin2hex(random_bytes(32)); // Genera un token aleatorio de 64 caracteres
    $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

    //Se crean las dos filas en la base de datos para lamacenar token y fecha de expiracion
    //ALTER TABLE tbl_usuarios
    //ADD COLUMN reset_token VARCHAR(255) NULL,
    //ADD COLUMN token_expiration DATETIME NULL;

    $id_usuario = $usuario_existe['id'];
    $query = $conn->prepare("UPDATE tbl_usuarios SET reset_token = :token, token_expiration = :expiration WHERE id = :id");
    $query->bindParam(':token', $token);
    $query->bindParam(':expiration', $fecha_expiracion);
    $query->bindParam(':id', $id_usuario);
    $query->execute();

    // el enlace de recuperación
    $enlace_recuperacion = "http://localhost/gob360_antioquia/resetear-contrasena.php?token=" . $token;


    $email = $correo_del_usuario; 
    $mail = new PHPMailer(true);

    try {

        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ilichfernandopradaariza@gmail.com'; // dirección de Gmail
        $mail->Password   = 'mmxb bjdm pnip fnlr'; // contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587; 

        $mail->setFrom('ilichfernandopradaariza@gmail.com', 'Gob360_Putumayo'); 
        $mail->addAddress($email);


        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña Gob360 Putumayo';
        $mail->Body    = '
            <html>
            <body>
                <p>Hola,</p>
                <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:</p>
                <p><a href="' . $enlace_recuperacion . '">Restablecer mi contraseña</a></p>
                <p>Este enlace expirará en 1 hora.</p>
                <p>Si no solicitaste este cambio, ignora este correo.</p>
                <p>Gob360_Putumayo.</p>
            </body>
            </html>';

        $mail->send();
        echo "<script>alert('Si el correo electrónico está registrado, recibirás un enlace de recuperación. Revisa tu bandeja de entrada y Spam.'); window.location.href = 'login.php';</script>";
        exit;

    } catch (Exception $e) {
        echo "<script>alert('Ocurrió un error al enviar el correo. Error: {$mail->ErrorInfo}'); window.location.href = 'enviar_enlace.php';</script>";
        exit;
    }
    
}


