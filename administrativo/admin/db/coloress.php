<?php

function getClaseColorVeredas($color) {
    $clases = [
        "#f76060" => 'critico',
        "#d4dc27" => 'alto',
        "#33c1ff" => 'bajo',
        "#b8ff33" => 'estable'
    ];

    return $clases[$color] ?? ''; // Retorna la clase correspondiente o una cadena vacía si no coincide
}

function getClasePorcentaje($porcentaje) {
    if ($porcentaje > 0 && $porcentaje <= 0.25) {
        return "medio";
    } elseif ($porcentaje >= 0.26 && $porcentaje <= 0.5) {
        return "bajo";
    } elseif ($porcentaje > 0.51) {
        return "estable";
    }

    return "neutro"; // Valor predeterminado
}

function getColorByNum($num) {
    if ($num >= 0 && $num <= 0) {
        return "#FFFFFF"; // Blanco
    } elseif ($num >= 1 && $num <= 400) {
        return "#6079f7"; // Rojo
    } elseif ($num >= 401 && $num <= 999999) {
        return "#f76069"; // Naranja    
    }

    return ""; // Color predeterminado si no coincide con ningún rango
}

?>
