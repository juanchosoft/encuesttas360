<?php
/**
 * Script de migración para importar fotos de participantes desde archivos locales
 * a la base de datos como campos BLOB
 *
 * Este script lee los participantes que tienen fotos en el campo 'foto'
 * y las convierte a binario para almacenarlas en el campo 'foto_blob'
 */

require_once 'classes/DbConection.php';

echo "=== SCRIPT DE MIGRACIÓN DE FOTOS DE PARTICIPANTES ===\n\n";

$db = new DbConection();
$pdo = $db->openConect();

// Obtener todos los participantes con fotos
$q = "SELECT id, foto FROM " . $db->getTable('tbl_participantes') . " WHERE foto IS NOT NULL AND foto != ''";
$stmt = $pdo->query($q);
$participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Se encontraron " . count($participantes) . " participantes con fotos.\n\n";

$contador_exitosos = 0;
$contador_fallidos = 0;
$errores = [];

foreach ($participantes as $participante) {
    $id = $participante['id'];
    $nombre_archivo = $participante['foto'];

    // Construir ruta completa del archivo
    $ruta_foto = __DIR__ . '/../assets/img/admin/' . $nombre_archivo;

    echo "Procesando participante ID: $id - Foto: $nombre_archivo\n";

    // Verificar si el archivo existe
    if (!file_exists($ruta_foto)) {
        echo "  ✗ Archivo no encontrado: $ruta_foto\n";
        $contador_fallidos++;
        $errores[] = "ID $id: Archivo no encontrado ($nombre_archivo)";
        continue;
    }

    // Leer el contenido binario del archivo
    $foto_blob = file_get_contents($ruta_foto);

    if ($foto_blob === false) {
        echo "  ✗ Error al leer el archivo: $ruta_foto\n";
        $contador_fallidos++;
        $errores[] = "ID $id: Error al leer archivo ($nombre_archivo)";
        continue;
    }

    // Actualizar el registro con el BLOB
    try {
        $update_q = "UPDATE " . $db->getTable('tbl_participantes') . "
                     SET foto_blob = :foto_blob
                     WHERE id = :id";

        $update_stmt = $pdo->prepare($update_q);
        $update_stmt->bindValue(':foto_blob', $foto_blob, PDO::PARAM_LOB);
        $update_stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $tamanio = strlen($foto_blob);
            echo "  ✓ Foto guardada exitosamente (" . number_format($tamanio / 1024, 2) . " KB)\n";
            $contador_exitosos++;
        } else {
            echo "  ✗ Error al ejecutar el UPDATE\n";
            $contador_fallidos++;
            $errores[] = "ID $id: Error al ejecutar UPDATE";
        }

    } catch (PDOException $e) {
        echo "  ✗ Error PDO: " . $e->getMessage() . "\n";
        $contador_fallidos++;
        $errores[] = "ID $id: " . $e->getMessage();
    }

    echo "\n";
}

$db->closeConect();

// Resumen final
echo "\n=== RESUMEN DE MIGRACIÓN ===\n";
echo "Total de participantes procesados: " . count($participantes) . "\n";
echo "Fotos migradas exitosamente: $contador_exitosos\n";
echo "Fotos con errores: $contador_fallidos\n";

if (count($errores) > 0) {
    echo "\n=== ERRORES ENCONTRADOS ===\n";
    foreach ($errores as $error) {
        echo "- $error\n";
    }
}

echo "\n✓ Migración completada.\n";
?>
