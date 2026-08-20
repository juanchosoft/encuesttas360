<?php
// Prueba rápida de los métodos del Dashboard
require_once './admin/classes/DbConection.php';
require_once './admin/classes/Util.php';
require_once './admin/classes/Dashboard.php';

echo "<h1>Test Dashboard</h1>";

echo "<h2>1. Test getGrillas()</h2>";
$grillas = Dashboard::getGrillas([]);
echo "<pre>";
print_r($grillas);
echo "</pre>";

echo "<h2>2. Test getEstadisticasPrincipales()</h2>";
$stats = Dashboard::getEstadisticasPrincipales([]);
echo "<pre>";
print_r($stats);
echo "</pre>";

echo "<h2>3. Test getVotantesPorIdeologia()</h2>";
$ideologia = Dashboard::getVotantesPorIdeologia([]);
echo "<pre>";
print_r($ideologia);
echo "</pre>";
