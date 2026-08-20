<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define("DS", DIRECTORY_SEPARATOR);

require_once __DIR__ . '/../classes/Util.php';
require_once __DIR__ . '/../classes/DbConection.php';
require_once __DIR__ . '/../classes/Colombia.php';

$codigoDepartamento = isset($_REQUEST['dep']) ? $_REQUEST['dep'] : Util::getDepartamentoPrincipal();
$arr = array('codigo' => $codigoDepartamento);
$data = Colombia::getDepartamentoByCodigoCiudadesAccionUnificada($arr);
$isvalid = isset($arr['output']['valid']) ? $arr['output']['valid'] : false;
$responseMapa = isset($data['output']['response']) ? $data['output']['response'] : [];

// -----------------------------------------------------------
// 1. DICCIONARIO DE EXCEPCIONES (Mapeo de Municipios con Coordenadas Especiales)
// -----------------------------------------------------------
// Usa el 'codigo_muncipio' como clave para buscar las coordenadas especiales.
// Esto es mucho más limpio que usar una serie de 'if' o 'switch' anidados.
$excepciones_municipio = [
    '68572' => [ // Puente Nacional
        'transform1' => 'matrix(1 0 0 1 433.3751 1150.5498)',
        'text1'      => 'Puente',
        'class1'      => 'st1 st2',
        'transform2' => 'matrix(1 0 0 1 429.3541 1162.498)',
        'text2'      => 'Nacional',
        'class2'      => 'st1 st2',
    ],
    '68689' => [ // San Vicente de Chucurí
        'transform1' => 'matrix(1 0 0 1 502.4714 640.8279)',
        'text1'      => 'San Vicente',
        'class1'      => 'st1 st2',
        'transform2' => 'matrix(1 0 0 1 502.4714 652.7761)',
        'text2'      => 'de Chucurí',
        'class2'      => 'st1 st2',
    ],
    '68705' => [ // Santa Bárbara
        'transform1' => 'matrix(1 0 0 1 795.2102 620.7677)',
        'text1'      => 'Santa',
        'class1'      => 'st1 st2',
        'transform2' => 'matrix(1 0 0 1 790.0247 632.7169)',
        'text2'      => 'Bárbara',
        'class2'      => 'st1 st2',
    ],
    '68720' => [ // Santa Helena del Opón
        'transform1' => 'matrix(1 0 0 1 472.1025 873.8613)',
        'text1'      => 'Santa Helena',
        'class1'      => 'st1 st2',
        'transform2' => 'matrix(1 0 0 1 481.7031 885.8095)',
        'text2'      => 'del Opón',
        'class2'     => 'st1 st2',
    ],
    '68524' => [ // Palmas del Socorro
        'transform1' => 'matrix(1 0 0 1 617.3425 877.2178)',
        'text1'      => 'Puerto',
        'class1'     => 'st1 st2',
        'transform2' => 'matrix(1 0 0 1 623.5076 889.1671)',
        'text2'      => 'Parra',
        'class2'     => 'st1 st2',
    ],
    '68855' => [ // Valle de San José
        'transform1' => 'matrix(1 0 0 1 701.455 858.9909)',
        'text1'      => 'Valle de',
        'class1'     => 'st1 st2',
        'transform2' => 'matrix(1 0 0 1 701.455 870.9401)',
        'text2'      => 'San José',
        'class2'     => 'st1 st2',
    ],
];

// Verificamos una sola vez si hay excepciones para ahorrar comprobaciones en el bucle.
$hay_excepciones = !empty($excepciones_municipio);
?>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="<?= $responseMapa[0]['viewbox_svg'] ?>">

<style type="text/css">
	.st0{fill:#FF8F33;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st1{font-family:'MyriadPro-Regular';}
	.st2{font-size:11.9488px;}
	.st3{fill:#57A3FF;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st4{font-size:13.6629px;}
	.st5{fill:#D57BFF;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st6{font-size:13.1384px;}
	.st7{fill:#F1FF66;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st8{fill:#FFD400;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st9{fill:#89F7F1;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st10{font-size:10.302px;}
	.st11{fill:#2FB524;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st12{fill:#2DEA9D;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st13{fill:#FCAF70;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st14{fill:#ACDD50;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st15{fill:#344CF7;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st16{fill:#EBCEF2;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st17{font-size:10.4888px;}
	.st18{fill:#F95D77;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st19{font-size:8.7099px;}
	.st20{font-size:13.0442px;}
	.st21{font-size:11px;}
	.st22{font-size:14.7048px;}
	.st23{font-size:13.6539px;}
	.st24{font-size:17.3519px;}
	.st25{font-size:13.7798px;}
	.st26{font-size:13px;}
	.st27{font-size:10.4072px;}
	.st28{font-size:10px;}
	.st29{fill:#E8F2AD;stroke:#FFFFFF;stroke-miterlimit:10;}
	.st30{font-size:12px;}
	.st31{font-size:10.5514px;}
</style>

<?php foreach ($responseMapa as $key => $value) :
  $num = 0;
  $fill = getColorByNum($num);
  $claseColor = getClaseColorVeredas($fill);

  $archivo = 'listado_lideres.php';
  $url = Util::generarUrlMapaGeneralCiudadesPorCodDepartamentoYCodMunicipio(
      $archivo,
      urlencode($value['codigo_departamento']),
      urlencode($value['codigo_muncipio']),
      urlencode($value['municipio'])
  );

  $codigo_muncipio = $value['codigo_muncipio'];
  $es_excepcion = array_key_exists($codigo_muncipio, $excepciones_municipio);
  $excepcion_data = $es_excepcion ? $excepciones_municipio[$codigo_muncipio] : null;

  $municipio_capitalizado = ucwords(mb_strtolower($value['municipio'], 'UTF-8'));
?>

  <g id="<?php echo strtoupper($value['id']); ?>">

    <path id="<?php echo ($value['id']); ?>"
      class="<?php echo ($value['class']); ?> municipios mapaClick <?php echo $claseColor ?: 'neutro'; ?>"
      data-url="<?php echo $url; ?>" 
      d="<?php echo ($value['d']); ?>" />

    <?php if ($es_excepcion): ?>

      <text transform="<?php echo htmlspecialchars($excepcion_data['transform1']); ?>" 
            class="<?php echo htmlspecialchars($excepcion_data['class1']); ?>">
        <?php echo htmlspecialchars($excepcion_data['text1']); ?>
      </text>

      <text transform="<?php echo htmlspecialchars($excepcion_data['transform2']); ?>" 
            class="<?php echo htmlspecialchars($excepcion_data['class2']); ?>">
        <?php echo htmlspecialchars($excepcion_data['text2']); ?>
      </text>

    <?php else: ?>

      <text transform="matrix(<?php echo ($value['transform']); ?>)" 
            class="<?php echo ($value['class2']); ?>">
        <?php echo ($municipio_capitalizado); ?>
      </text>

    <?php endif; ?>

  </g>

<?php endforeach; ?>

</svg>

<script>
  $("img").each(function(index, el) {
    $(this).attr("data-bs-toggle", "tooltip");
    $(this).attr("data-bs-placement", "left");
    tooltip = new bootstrap.Tooltip($(this)[0], {})
  });
  $(".mapaClick").click(function(event) {
    location.href = $(this).data("url");
  });
</script>