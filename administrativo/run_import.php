<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/ImportadorPreguntas.php';

// Reemplaza 'preguntas.csv' con la ruta real a tu archivo CSV
$csvFile = __DIR__ . '/preguntas.csv'; // Si el CSV está en el mismo directorio que este script
// O si está en una carpeta de uploads:
// $csvFile = '/var/www/html/uploads/preguntas_encuesta.csv'; 

$result = ImportadorPreguntas::importFromCsv($csvFile);

if ($result['output']['valid']) {
    echo "<h1>Importación Exitosa</h1>";
    echo "<p>" . $result['output']['response'] . "</p>";
} else {
    echo "<h1>Error en la Importación</h1>";
    echo "<p>" . $result['output']['response'] . "</p>";
}