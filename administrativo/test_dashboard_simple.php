<?php
// Script simple para probar Dashboard sin depender de head.php
require_once './admin/classes/DbConection.php';
require_once './admin/classes/Util.php';
require_once './admin/classes/Dashboard.php';

echo "<h1>Test Dashboard Stats</h1>";

echo "<h2>Probando getEstadisticasPrincipales()</h2>";
try {
    $result = Dashboard::getEstadisticasPrincipales([]);
    echo "<pre>";
    print_r($result);
    echo "</pre>";

    if ($result['output']['valid']) {
        echo "<h3>✅ Método funciona correctamente</h3>";
        $stats = $result['output']['response'];
        echo "<ul>";
        echo "<li>Total Votantes: " . $stats['total_votantes'] . "</li>";
        echo "<li>Total Grillas: " . $stats['total_grillas'] . "</li>";
        echo "<li>Total Análisis: " . $stats['total_analisis'] . "</li>";
        echo "<li>Total Candidatos: " . $stats['total_candidatos'] . "</li>";
        echo "</ul>";
    } else {
        echo "<h3>❌ Error en la respuesta</h3>";
    }
} catch (Exception $e) {
    echo "<h3>❌ Excepción: " . $e->getMessage() . "</h3>";
}

echo "<h2>Probando getGrillas()</h2>";
try {
    $result = Dashboard::getGrillas([]);
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "<h3>❌ Excepción: " . $e->getMessage() . "</h3>";
}
