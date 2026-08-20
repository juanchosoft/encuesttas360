<?php
$svgPath = $_GET['path'] ?? '';

$realPath = __DIR__ . '/' . $svgPath;

if (!file_exists($realPath) || pathinfo($realPath, PATHINFO_EXTENSION) !== 'svg') {
    http_response_code(404);
    exit("Archivo no encontrado.");
}

$svg = file_get_contents($realPath);

// Elimina todos los fill existentes
$svg = preg_replace('/fill="[^"]*"/i', '', $svg);

// Agrega fill gris
$svg = preg_replace('/<path/i', '<path fill="#9e9e9e"', $svg);
$svg = preg_replace('/<polygon/i', '<polygon fill="#9e9e9e"', $svg);
$svg = preg_replace('/<rect/i', '<rect fill="#9e9e9e"', $svg);

header('Content-Type: image/svg+xml');
echo $svg;
