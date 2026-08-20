<?php
/**
 * Endpoint para servir fotos de participantes desde la base de datos (BLOB)
 *
 * Parámetros GET:
 * - id: ID del participante
 *
 * Retorna la imagen en formato binario con el Content-Type apropiado
 */

require_once 'classes/DbConection.php';

// Obtener ID del participante
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    // Servir imagen por defecto si no hay ID válido
    $default_image = __DIR__ . '/../assets/img/generic/default.png';
    if (file_exists($default_image)) {
        header('Content-Type: image/png');
        readfile($default_image);
        exit;
    }
    http_response_code(404);
    exit;
}

$db = new DbConection();
$pdo = $db->openConect();

try {
    // Obtener la foto del participante
    $q = "SELECT foto_blob FROM " . $db->getTable('tbl_participantes') . " WHERE id = :id";
    $stmt = $pdo->prepare($q);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && !empty($resultado['foto_blob'])) {
        // Detectar tipo MIME de la imagen
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($resultado['foto_blob']);

        // Configurar headers para la imagen
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . strlen($resultado['foto_blob']));
        header('Cache-Control: public, max-age=31536000'); // Cache por 1 año

        // Enviar la imagen
        echo $resultado['foto_blob'];
    } else {
        // No hay foto en BLOB, servir imagen por defecto
        $default_image = __DIR__ . '/../assets/img/generic/default.png';
        if (file_exists($default_image)) {
            header('Content-Type: image/png');
            readfile($default_image);
        } else {
            http_response_code(404);
        }
    }

} catch (PDOException $e) {
    error_log("Error al obtener foto de participante: " . $e->getMessage());
    http_response_code(500);
} finally {
    $db->closeConect();
}
?>
