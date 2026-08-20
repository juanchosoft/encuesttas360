<?php
/**
 * REUSABLE SVG MAP RENDERER COMPONENT
 * Renders the municipality map based on the $responseMapa array provided in the scope.
 *
 * NOTE: This component assumes the following functions/classes are available in the calling scope:
 * - getColorByNum($num)
 * - getClaseColorVeredas($fill)
 * - Util::generarUrlMapaGeneralCiudadesPorCodDepartamentoYCodMunicipio(...)
 */

if (empty($responseMapa) || !is_array($responseMapa)) {
    echo '<div class="text-center p-4 text-gray-500">No hay datos de municipios para mostrar.</div>';
    return;
}

?>
<style type="text/css">

  .cuerpoMapa {
      max-width: 100%;
      height: auto;
  }
  .mapaClick {
      cursor: pointer;
      transition: fill 0.2s ease;
  }
  .mapaClick:hover {
      opacity: 0.8;
  }
</style>
<div id="contenido-mapa" class="cuerpoMapa w-12">
<?php

$viewBoxMapa = $responseMapa[0]['viewbox_svg'] ?? '0 0 1300 1400';
?>

    <svg 
        xmlns="http://www.w3.org/2000/svg" 
        version="1.1" 
        viewBox="<?= $viewBoxMapa ?>"

    >

        <?php foreach ($responseMapa as $value) :
            $num = (int)($value['num_val'] ?? 0);
            $fill = getColorByNum($num);
            $claseColor = getClaseColorVeredas($fill);

            $archivo = 'listado_lideres.php';
            $url = Util::generarUrlMapaGeneralCiudadesPorCodDepartamentoYCodMunicipio(
                $archivo,
                urlencode($value['codigo_departamento'] ?? ''),
                urlencode($value['codigo_muncipio'] ?? ''),
                urlencode($value['municipio'] ?? '')
            );
        ?>

            <g id="<?= strtoupper($value['id'] ?? 'G_ERR') ?>">

                <path 
                    id="<?= $value['id'] ?? 'PATH_ERR' ?>"
                    class="<?= $value['class'] ?? '' ?> municipios mapaClick <?= $claseColor ?: 'neutro' ?>"
                    data-url="<?= $url ?>"
                    d="<?= $value['d'] ?? '' ?>"
                />

                <text 
                    transform="matrix(<?= $value['transform'] ?? '' ?>)"
                    class="<?= $value['class2'] ?? '' ?>"
                >
                    <?= $value['municipio'] ?? 'S/N' ?>
                </text>

            </g>

        <?php endforeach; ?>

    </svg>

</div>

<script>
  // Script de interacción (asume jQuery y Bootstrap están cargados)
  $(function() {
    // Inicialización de Tooltips (para elementos IMG, siguiendo tu código original)
    $("img").each(function(index, el) {
      $(this).attr("data-bs-toggle", "tooltip");
      $(this).attr("data-bs-placement", "left");
      if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      new bootstrap.Tooltip($(this)[0], {});
      }
    });

    // Manejador de clic para las rutas SVG de los municipios
    $(".mapaClick").on('click', function(event) {
      const url = $(this).data("url");
      if (url) {
        // Redirige a la URL generada
        location.href = url;
      }
    });
  });
</script>
