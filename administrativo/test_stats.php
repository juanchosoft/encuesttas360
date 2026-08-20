<?php
include './admin/include/head.php';
require_once './admin/include/generic_classes.php';
include './admin/classes/Dashboard.php';

header('Content-Type: application/json');

$result = Dashboard::getEstadisticasPrincipales([]);
echo json_encode($result, JSON_PRETTY_PRINT);
