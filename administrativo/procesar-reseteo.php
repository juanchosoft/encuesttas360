<?php

include './admin/classes/DbConection.php';
include './admin/classes/Util.php';

$db_connection = new DbConection();
$conn = null;
try {
    $conn = $db_connection->openConect();
    $db_name = $db_connection->getDbName();
    $conn->exec("USE " . $db_name);
} catch (Exception $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error interno del sistema. Intente de nuevo más tarde.");
}

// 2. Validar que el token y las contraseñas se hayan enviado por POST
if (!isset($_POST['token']) || empty($_POST['token']) || !isset($_POST['password']) || empty($_POST['password']) || !isset($_POST['confirm_password']) || empty($_POST['confirm_password'])) {
    die("Petición no válida.");
}

$token = $_POST['token'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// 3. Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
    echo "<script>alert('Las contraseñas no coinciden. Intente de nuevo.'); window.location.href = 'recuperar-contrasena.php';</script>";
    $db_connection->closeConect();
    exit;
}

// 4. Validar la complejidad de la contraseña (opcional pero recomendado)
if (strlen($password) < 5) {
    echo "<script>alert('La contraseña debe tener al menos 8 caracteres.'); window.location.href = 'recuperar-contrasena.php';</script>";

}

// 5. Buscar el usuario con el token y verificar la fecha de expiración
$query = $conn->prepare("SELECT id, token_expiration FROM tbl_usuarios WHERE reset_token = :token");
$query->bindParam(':token', $token);
$query->execute();
$usuario = $query->fetch(PDO::FETCH_ASSOC);

if (!$usuario || strtotime($usuario['token_expiration']) < time()) {
    // Si el token no es válido o ha expirado, muestra un error y sal del script
    echo "<script>alert('El enlace de recuperación no es válido o ha expirado.'); window.location.href = 'recuperar-contrasena.php';</script>";
    $db_connection->closeConect();
    exit;
}

// Primero encriptamos con md5(), igual que en login.php
$hashed_password = md5($password);
// Luego, aplicamos el segundo hash con la misma función que usa Usuario.php
$hashed_password = Util::make_hash_pass($hashed_password);


// 7. Actualizar la contraseña del usuario y anular el token
$id_usuario = $usuario['id'];
$update_query = $conn->prepare("UPDATE tbl_usuarios SET hashpass = :password, reset_token = NULL, token_expiration = NULL WHERE id = :id");
$update_query->bindParam(':password', $hashed_password);
$update_query->bindParam(':id', $id_usuario);
$update_query->execute();


$db_connection->closeConect();

echo "<script>alert('¡Contraseña actualizada con éxito! Ya puedes iniciar sesión con tu nueva contraseña.'); window.location.href = 'login.php';</script>";
exit;
?>