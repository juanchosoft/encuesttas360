<?php

function getClaseColorVeredas($color) {
    $clases = [
        "#FC0707" => 'critico',
        "#15DA01" => 'alto',
        "#FEE300" => 'bajo',
        "#15DA01" => 'estable'
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
        return "#ffffffff"; // Blanco
    } elseif ($num >= 1 && $num <= 50) {
        return "#00f0a0ff"; // Verde agua marina Claro
    } elseif ($num >= 51 && $num <= 100) {
        return "#00cf6eff"; // Verde agua marina más oscuro
    } elseif ($num >= 101 && $num <= 99999999) {
        return "#006effff"; // Verde agua marina
    }
    return "#cccccc"; // Color predeterminado si no coincide con ningún rango
}

?>
