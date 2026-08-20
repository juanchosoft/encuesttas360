<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define("DS", DIRECTORY_SEPARATOR);
require_once __DIR__ . '/../classes/Colombia.php';
require_once __DIR__ . '/../db/colores.php';

$colombia = Colombia::getInformacionMapaColombia(NULL);
$isvalidColombia = $colombia['output']['valid'];
$responseColombia = $colombia['output']['response'];
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title> MAPA DE COLOMBIA</title>
</head>

  <script>
    $(function() {
      // Activar tooltips para todas las imágenes (por compatibilidad)
      $("img").each(function() {
        $(this).attr("data-bs-toggle", "tooltip");
        $(this).attr("data-bs-placement", "left");
        new bootstrap.Tooltip(this);
      });

      // Click sobre departamentos habilitados
      $(".mapaClick").on("click", function() {
        const url = $(this).data("url");
        if (url) location.href = url;
      });
    });
  </script>
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
  <style>
    .mapaClick {
  transition: all 0.2s ease-in-out;
  transform-origin: center;
}

.mapaClick:hover {
  stroke: rgb(0, 238, 255);
  stroke-width: 2px;
  filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.7));
  cursor: pointer;
}

  </style>
<body>
  <?php include __DIR__ . '/../include/nombres_colombia.php'; ?>

<!-- Generator: Adobe Illustrator 25.4.1, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
       x="0px" y="0px"
       viewBox="150 55 800 1020" style="enable-background:new 0 0 1080 1080;" xml:space="preserve">

    <style type="text/css">
      .st0{fill-rule:evenodd;clip-rule:evenodd;fill:#85B66F;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st1{font-family:'Roboto';}
      .st2{font-size:13.3251px;}
      .st3{fill-rule:evenodd;clip-rule:evenodd;fill:#EABC60;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st4{font-size:13.0068px;}
      .st5{fill-rule:evenodd;clip-rule:evenodd;fill:#E269B4;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st6{font-size:13.8544px;}
      .st7{fill-rule:evenodd;clip-rule:evenodd;fill:#5064D8;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st8{font-size:13.0653px;}
      .st9{fill-rule:evenodd;clip-rule:evenodd;fill:#0C965B;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st10{font-size:13.5816px;}
      .st11{fill-rule:evenodd;clip-rule:evenodd;fill:#1037DD;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st12{font-size:13.099px;}
      .st13{fill-rule:evenodd;clip-rule:evenodd;fill:#D68441;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st14{font-size:13px;}
      .st15{fill-rule:evenodd;clip-rule:evenodd;fill:#E8E156;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st16{fill-rule:evenodd;clip-rule:evenodd;fill:#D33120;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st17{font-size:13.0165px;}
      .st18{fill-rule:evenodd;clip-rule:evenodd;fill:#D89766;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st19{font-size:13.606px;}
      .st20{fill-rule:evenodd;clip-rule:evenodd;fill:#0ABCF2;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st21{font-size:13.3833px;}
      .st22{fill-rule:evenodd;clip-rule:evenodd;fill:#EF89AB;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st23{font-size:13.2547px;}
      .st24{fill-rule:evenodd;clip-rule:evenodd;fill:#B976DD;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st25{font-size:13px;}
      .st26{fill-rule:evenodd;clip-rule:evenodd;fill:#EAA542;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st27{font-size:13.2946px;}
      .st28{fill-rule:evenodd;clip-rule:evenodd;fill:#3EDD7F;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st29{font-size:13.8654px;}
      .st30{fill-rule:evenodd;clip-rule:evenodd;fill:#F4DF21;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st31{font-size:13.8335px;}
      .st32{fill-rule:evenodd;clip-rule:evenodd;fill:#44B23B;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st33{font-size:13.3265px;}
      .st34{fill-rule:evenodd;clip-rule:evenodd;fill:#F77F08;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st35{font-size:13.267px;}
      .st36{fill-rule:evenodd;clip-rule:evenodd;fill:#DCF44D;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st37{fill-rule:evenodd;clip-rule:evenodd;fill:#EFBC4B;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st38{font-size:13.8655px;}
      .st39{fill-rule:evenodd;clip-rule:evenodd;fill:#1CEACC;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st40{font-size:13.9371px;}
      .st41{fill-rule:evenodd;clip-rule:evenodd;fill:#FCF510;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st42{font-size:13.2352px;}
      .st43{fill-rule:evenodd;clip-rule:evenodd;fill:#F7860B;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st44{font-size:13.2124px;}
      .st45{fill-rule:evenodd;clip-rule:evenodd;fill:#D49CFF;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st46{font-size:13.2484px;}
      .st47{fill-rule:evenodd;clip-rule:evenodd;fill:#F79623;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st48{font-size:13.7194px;}
      .st49{fill-rule:evenodd;clip-rule:evenodd;fill:#F93E3E;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st50{font-size:13.1011px;}
      .st51{fill-rule:evenodd;clip-rule:evenodd;fill:#04B799;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st52{font-size:13.2107px;}
      .st53{fill-rule:evenodd;clip-rule:evenodd;fill:#2CEBFF;stroke:#FFFFFF;stroke-width:0.75;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st54{font-size:13.4449px;}
      .st55{fill-rule:evenodd;clip-rule:evenodd;fill:#FFB8E1;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st56{font-size:13.0099px;}
      .st57{fill-rule:evenodd;clip-rule:evenodd;fill:#FC1515;stroke:#FFFFFF;stroke-width:0.75;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st58{font-size:13.6172px;}
      .st59{fill-rule:evenodd;clip-rule:evenodd;fill:#AA74F7;stroke:#FFFFFF;stroke-width:0.75;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st60{font-size:13.7288px;}
      .st61{fill-rule:evenodd;clip-rule:evenodd;fill:#369305;stroke:#FFFFFF;stroke-linecap:square;stroke-linejoin:bevel;stroke-miterlimit:10;}
      .st62{font-size:13.4172px;}
    </style>

    <!-- Informacion de Colombia -->
    <?php foreach ($responseColombia as $key => $value) : ?>
      <?php if ($value['habilitado'] === 'si'): ?>

        <?php
          $urlMapaDepartamentoCod = 'dashboard_colombia.php?dep=' . urlencode($value['codigo_departamento']);
          $departamentosPermitidos = [68, 05, 86, 52, 41, 73, 94, 47, 95, 97, 50]; 
          $claseOnClick = 'mapaClick';

          $num = isset($value['num_val']) ? (int)$value['num_val'] : 0;
          $fill = getColorByNum($num);
          $claseColor = getClaseColorVeredas($fill);
        ?>

        <g id="<?php echo $value['departamento']; ?>">
          <path vector-effect="none" class="municipios <?php echo $claseOnClick; ?> <?php echo $value['class']; ?> <?php echo $claseColor ?: 'neutro'; ?>"
                d="<?php echo $value['d']; ?>" data-url="<?php echo $urlMapaDepartamentoCod; ?>" />

          <?php
$dep = $value['departamento'];
if (isset($nombresDepartamentos[$dep])) {
    echo $nombresDepartamentos[$dep];
}
?>


        </g>

      <?php endif; ?>
    <?php endforeach; ?>
<g id="labels-departamentos" style="pointer-events:none">
    <?php foreach ($nombresDepartamentos as $txt): ?>
        <?php echo $txt; ?>
    <?php endforeach; ?>
</g>

</svg>

