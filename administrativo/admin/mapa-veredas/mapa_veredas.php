<?php 
session_start();
require_once __DIR__ . '/../classes/Util.php';

$veredas = $_SESSION['veredas'] ?? [];
$colores = $_SESSION['colores_veredas'] ?? [];

$dep = $_SESSION['departamento'] ?? Util::getDepartamentoPrincipal();
$codigo = $_GET['mun'] ?? Util::getCodigoMunicipioPrincipal();
$mun = $_SESSION['municipio'] ?? Util::getNombreMunicipioPorCodigoUnificado($codigo);

// Sincronizar color_calculado
foreach ($veredas as &$vereda) {
    foreach ($colores as $municipio) {
        if (
            isset($vereda['nombre_vereda'], $municipio['nombre_vereda']) &&
            trim(mb_strtolower($vereda['nombre_vereda'])) === trim(mb_strtolower($municipio['nombre_vereda']))
        ) {
            $vereda['color_calculado'] = $municipio['color_calculado'];
            break;
        }
    }
}
unset($vereda);

foreach ($veredas as $value): 
    $nombreVereda = $value["nombre_vereda"] ?? 'NOMBRE DESCONOCIDO';
    $nombreSVG = $value["nombre_svg"] ?? '';
$nombreClase = strtolower($nombreSVG); 


    if (empty($nombreSVG)) continue;

    $imgName = $nombreSVG . ".svg";
    $realPath = __DIR__ . "/{$dep}/{$mun}/img/{$imgName}";

    $claseColor = 'veredaMun';
    switch (strtolower($value["color_calculado"] ?? '')) {
        case 'verde':   $claseColor .= ' estable'; break;
        case 'amarillo':$claseColor .= ' medio';   break;
        case 'naranja': $claseColor .= ' alto';    break;
        case 'rojo':    $claseColor .= ' critico'; break;
        case 'gris':    $claseColor .= ' bajo';    break;
    }
    $hasData = false;
    if (!empty($value['total_cantidad']) && $value['total_cantidad'] > 0) {
        $hasData = true;
    }

    $colorRaw = strtolower(trim($value["color_calculado"] ?? ''));
    $hexColor = '#bdbdbd'; 

    if ($hasData && in_array($colorRaw, ['verde', 'amarillo', 'naranja', 'rojo', 'gris'])) {
        $hexColor = match ($colorRaw) {
            'verde'    => '#4caf50',
            'amarillo' => '#ffeb3b',
            'naranja'  => '#ff9800',
            'rojo'     => '#f44336',
            'gris'     => '#9e9e9e'
        };
    }
$url = '';
if ($hasData) {
    $veredaId = (int)($value['id'] ?? 0);
    $url = "/veredas.php?id={$veredaId}&mun={$codigo}&dep={$dep}&pilar=1";
}

    if (file_exists($realPath)):
        $svgContent = file_get_contents($realPath);
    $svgContent = preg_replace('/class="cls-\d+"/i', '', $svgContent);

        // Eliminar estilos embebidos que bloquean fill personalizado
        $svgContent = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $svgContent);
// if ($hasData) {
//     $svgContent = '<div onclick="console.log(\'Redirigiendo a: ' . $url . '\'); window.location.href=\'' . $url . '\';" class="vereda-link" style="cursor:pointer;">' . $svgContent . '</div>';
// }




        $fillColor = '#6261618a'; // Gris oscuro por defecto
        if ($hasData && in_array($colorRaw, ['verde', 'amarillo', 'naranja', 'rojo', 'gris'])) {
            $fillColor = match ($colorRaw) {
                'verde'    => '#4caf50',
                'amarillo' => '#ffeb3b',
                'naranja'  => '#ff9800',
                'rojo'     => '#f44336',
                'gris'     => '#9e9e9e'
            };
        }

        $svgContent = preg_replace('/fill="[^"]*"/i', '', $svgContent);
        $svgContent = preg_replace('/<path/i', '<path style="fill:' . $fillColor . ' !important;"', $svgContent);
        $svgContent = preg_replace('/<polygon/i', '<polygon style="fill:' . $fillColor . ' !important;"', $svgContent);
        $svgContent = preg_replace('/<rect/i', '<rect style="fill:' . $fillColor . ' !important;"', $svgContent);
        $svgContent = preg_replace('/<polyline/i', '<polyline style="fill:' . $fillColor . ' !important;"', $svgContent);
        

$veredaId = (int)($value['id'] ?? 0);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
           "://" . $_SERVER['HTTP_HOST'] .
           dirname($_SERVER['SCRIPT_NAME']);

$url = rtrim($baseUrl, "/") . "/veredas.php?id={$veredaId}&mun={$codigo}&dep={$dep}&pilar=1";


// Contenedor principal con clase y atributos
$contenedor = '<div class="vereda-container ' . htmlspecialchars($nombreSVG . ' veredaMun') . '" 
    data-dept="' . htmlspecialchars($dep) . '" 
    data-mun="' . htmlspecialchars($codigo) . '" 
    data-id="' . htmlspecialchars($nombreVereda) . '" 
    data-color="' . $fillColor . '" 
    title="' . htmlspecialchars(str_replace("-", " ", $nombreVereda)) . '"';

$contenedor .= ' data-url="' . $url . '" onclick="handlePolygonClick(this)"';
  echo "<script>console.log('✅ URL generada para {$nombreVereda}: {$url}');</script>";
echo $contenedor . ">" . $svgContent . "</div>";



    endif;
    endforeach;
    ?>
<script>
function handlePolygonClick(element) {
    const url = element.getAttribute('data-url');
    if (url) {
        console.log("Redirigiendo a:", url);
        window.location.href = url;
    } else {
        console.warn("No se encontró URL en el elemento");
    }
}
document.querySelectorAll('.vereda-container').forEach(el => {
    el.addEventListener('click', () => {
        console.log("🟢 Click en:", el.dataset.url);
    });
});

</script>


<style>
    .vereda-link svg {
  pointer-events: none !important;
}
.vereda-link {
  pointer-events: auto;
}
.vereda-container svg {
    pointer-events: none !important;
}

.vereda-link {
  position: absolute;
  display: block;
  width: 100%;
  height: 100%;
  text-decoration: none;
}

.vereda-link svg {
  pointer-events: none;
}

.veredaMun {
  filter: grayscale(100%) brightness(2.2) !important;
  transition: all 0.3s ease;
  position: absolute;
  pointer-events: auto !important;}

.veredaMun svg {
  pointer-events: auto !important;
}
.veredaMun path,
.veredaMun polygon,
.veredaMun rect,
.veredaMun polyline {
  pointer-events: visiblePainted !important;
}

.color-con-data {
filter: sepia(1) hue-rotate(341deg) saturate(200%) brightness(1.5) !important; /* Amarillo de relleno de colores */


  z-index: 1000;
}
.color-critico  { filter: brightness(1) sepia(1) hue-rotate(0deg) saturate(10000%) !important; } /* rojo */
.color-alto     { filter: brightness(1) sepia(1) hue-rotate(30deg) saturate(3000%) !important; } /* naranja */
.color-estable  { filter: brightness(1.2) sepia(1) hue-rotate(90deg) saturate(3000%) !important; } /* verde */
.color-grave    { filter: brightness(1) sepia(1) hue-rotate(320deg) saturate(5000%) !important; } /* morado */
.color-vacio    { filter: grayscale(100%) brightness(1.8) !important; } /* gris claro */

</style>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const veredasDeTabla = [];


    document.querySelectorAll(".table-municipio tbody tr").forEach(tr => {
        const tdVereda = tr.children[1]; 
        if (tdVereda) {
            const nombreVereda = tdVereda.textContent.trim().toLowerCase().replace(/\s+/g, '-');
            veredasDeTabla.push(nombreVereda);
        }
    });


    veredasDeTabla.forEach(clase => {
let elemento = document.querySelector(`.vereda-container.${clase}`);

if (!elemento) {
    elemento = document.querySelector(`#mapa .${clase}`);
}

if (elemento) {
    elemento.classList.remove("color-vacio");
    elemento.classList.add("color-con-data");
} else {
    console.warn("Vereda no encontrada en mapa:", clase);
}
        if (elemento) {
            const color = elemento.dataset.color || "#ff0"; 
elemento.classList.remove("color-vacio");
elemento.classList.add("color-con-data");
        } else {
            console.warn("Vereda no encontrada en mapa:", clase);
        }
    });
});
</script>
