<?php
/**
 * Helpers seguros para render de mapas municipales.
 */
function mapa_muni_label(array $value): string
{
    $label = $value['nombre_mapa'] ?? null;
    if ($label === null || $label === '') {
        $label = $value['municipio'] ?? '';
    }
    return strtoupper(str_replace('-', ' ', (string)$label));
}

function mapa_muni_name(array $value): string
{
    return strtolower((string)($value['municipio'] ?? $value['nombre_mapa'] ?? ''));
}

function mapa_muni_num($value): int
{
    if (is_array($value)) {
        return (int)($value['num_val'] ?? 0);
    }
    return (int)$value;
}
