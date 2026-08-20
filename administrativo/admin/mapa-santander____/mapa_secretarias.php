<?php
define("DS", DIRECTORY_SEPARATOR);
include './admin/classes/Colombia.php';

$arr = array('codigo' => Util::getDepartamentoPrincipal());
$data = Colombia::getInformacionParaMapaAccionUnificada($arr);
$isvalid = $arr['output']['valid'];
$santander =  $data['output']['response'];
?>

<title>Mapa Santander</title>
<meta charset="UTF-8">
<meta name="title" content="">
<meta name="description" content="">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
	href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
	rel="stylesheet">
</head>

<body>
	<style>
		.nombres {
			font-family: "IBM Plex Sans", sans-serif !important;
		}

		.fondo {
			background-color: #FC0707;
			padding: 2px 4px;
			/* Añade un poco de espacio alrededor del texto */
			color: white;
			/* Asegura que el texto sea legible */
			display: inline-block;
			/* Asegura que el fondo solo cubra el texto */
		}
	</style>
	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 788.66 885.68" width="900" height="900">

		<?php foreach ($santander as $key => $value) : ?>
		<g id="<?php echo strtoupper($value['path']); ?>"> 
			<path id="<?php echo strtoupper($value['path']); ?>" d="<?php echo $value['d']; ?>" fill="<?php echo Util::getColorNeutroMapa(); ?>"
				class="municipios mapaClick <?php echo getClasePorcentaje(0.2); ?>"
				data-url="<?php echo getUrl() . 'municipios_secretarias.php?mun=' . $value['codigo_muncipio'] . '&dep=' . $value['codigo_departamento']; ?>&pilar=<?php echo Util::getIdentificadorPilarPrincipal(); ?>"
				data-sub="<?php echo isset($value['color_calculado_de_municipio']) ? getClaseColorVeredas($value['color_calculado_de_municipio']) : ''; ?>"
				data-name="<?php echo strtolower($value['municipio']); ?>"
				title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_mapa'])); ?>" stroke="#000"
				stroke-miterlimit="10"></path><text transform="translate(264.48 382.8)" font-family="IBM Plex Sans"
				font-size="10" font-weight="500">
			</text>
		</g>
		<?php endforeach; ?>

		<!-- Coordenadas de los nombres de los municipios -->
		<text transform="translate(697.21 568.83) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.19"
			font-weight="500">
			<tspan x="0" y="0">SAN MIGUEL</tspan>
		</text>
		</text><text transform="translate(712.2 592.92) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.84"
			font-weight="500">
			<tspan x="0" y="0">MACARAVITA</tspan>
			<tspan x="13.38" y="0" letter-spacing="-.04em"></tspan>
			<tspan x="15.79" y="0" letter-spacing="0em">VI</tspan>
			<tspan x="19.35" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="21.46" y="0">A</tspan>
		</text><text transform="translate(682.43 572.31) rotate(60.41)" font-family="IBM Plex Sans" font-size="5.82"
			font-weight="500">
			<tspan x="0" y="0">CAPI</tspan>
			<tspan x="13.04" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="16.24" y="0" letter-spacing="0em">ANEJO</tspan>
		</text><text transform="translate(664.02 548.22) rotate(1.24)" font-family="IBM Plex Sans" font-size="2.99"
			font-weight="500">
			<tspan x="0" y="0">SAN JOSE</tspan>
			<tspan x="-1.95" y="3.59">DE MIRANDA</tspan>
		</text><text transform="translate(624.08 546.14) rotate(1.24)" font-family="IBM Plex Sans" font-size="5.67"
			font-weight="500">
			<tspan x="0" y="0">MO</tspan>
			<tspan x="8.88" y="0" letter-spacing="0em">L</tspan>
			<tspan x="12" y="0">AG</tspan>
			<tspan x="19.63" y="0" letter-spacing="-.04em">A</tspan>
			<tspan x="23.19" y="0">VI</tspan>
			<tspan x="28.46" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="31.57" y="0">A</tspan>
		</text><text transform="translate(621.62 656.75) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.08"
			font-weight="500">
			<tspan x="0" y="0">ON</tspan>
			<tspan x="12.72" y="0" letter-spacing="0em">Z</tspan>
			<tspan x="18.25" y="0" letter-spacing="0em">AGA</tspan>
		</text><text transform="translate(553.15 691.59) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
			font-weight="500">
			<tspan x="0" y="0">COROMORO</tspan>
		</text><text transform="translate(446.6 792.5) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
			font-weight="500">
			<tspan x="0" y="0">GÁMBITA</tspan>
		</text>><text transform="translate(432.89 731.2) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.09"
			font-weight="500">
			<tspan x="0" y="0">S</tspan>
			<tspan x="5.49" y="0" letter-spacing="-.01em">U</tspan>
			<tspan x="11.32" y="0" letter-spacing="0em">AI</tspan>
			<tspan x="19.94" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="24.92" y="0">A</tspan>
		</text><text transform="translate(511.78 733.12) rotate(-77.32)" font-family="IBM Plex Sans" font-size="10"
			font-weight="500">
			<tspan x="0" y="0">C</tspan>
			<tspan x="7.14" y="0" letter-spacing="0em">H</tspan>
			<tspan x="15" y="0">ARALÁ</tspan>
		</text><text transform="translate(546.17 745.79) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
			font-weight="500">
			<tspan x="0" y="0">ENCINO</tspan>
		</text><text transform="translate(384.19 738.47) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.16"
			font-weight="500">
			<tspan x="0" y="0">SAN</tspan>
			<tspan x="-3.11" y="5">BENI</tspan>
			<tspan x="6" y="5" letter-spacing="-.01em">T</tspan>
			<tspan x="8.47" y="5">O</tspan>
		</text><text transform="translate(369.98 760.54)" font-family="IBM Plex Sans" font-size="4.55"
			font-weight="500">
			<tspan x="0" y="0">GUPSA</tspan>
		</text><text transform="translate(356.4 793.43) rotate(-50.41)" font-family="IBM Plex Sans" font-size="4.77"
			font-weight="500">
			<tspan x="0" y="0">BARBOSA</tspan>
		</text><text transform="translate(325.63 826.51)" font-family="IBM Plex Sans" font-size="8.09"
			font-weight="500">
			<tspan x="0" y="0">PUENTE</tspan>
			<tspan x="-4.77" y="9.71">NACIONAL</tspan>
		</text><text transform="translate(271.14 856.5)" font-family="IBM Plex Sans" font-size="8.09" font-weight="500">
			<tspan x="0" y="0">ALBANIA</tspan>
		</text><text transform="translate(568.28 599.1) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.08"
			font-weight="500">
			<tspan x="0" y="0">MOG</tspan>
			<tspan x="20.4" y="0" letter-spacing="-.04em">O</tspan>
			<tspan x="26.3" y="0">TES</tspan>
		</text><text transform="translate(684.54 538.87) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.8"
			font-weight="500">
			<tspan x="0" y="0">ENCISO</tspan>
		</text><text transform="translate(449.52 538.28) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
			font-weight="500">
			<tspan x="0" y="0">GALÁN</tspan>
		</text><text transform="translate(484.17 577.71) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.09"
			font-weight="500">
			<tspan x="0" y="0">CABRERA</tspan>
		</text><text transform="translate(506.25 588.58) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.4"
			font-weight="500">
			<tspan x="0" y="0">PINCH</tspan>
			<tspan x="13.19" y="0" letter-spacing="-.04em">O</tspan>
			<tspan x="16.05" y="0">TE</tspan>
		</text><text transform="translate(523.94 642.11) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
			font-weight="500">
			<tspan x="0" y="0">OCAMONTE</tspan>
		</text><text transform="translate(479.45 609.59) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.82"
			font-weight="500">
			<tspan x="0" y="0">SOCORRO</tspan>
		</text><text transform="translate(555.99 560.6) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.75"
			font-weight="500">
			<tspan x="0" y="0">CURITÍ</tspan>
		</text><text transform="translate(529.31 520.52) rotate(1.24)" font-family="IBM Plex Sans" font-size="5.37"
			font-weight="500">
			<tspan x="0" y="0">JORDÁN</tspan>
		</text><text transform="translate(491.51 548.86) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.09"
			font-weight="500">
			<tspan x="0" y="0">BARIC</tspan>
			<tspan x="11.67" y="0" letter-spacing="0em">H</tspan>
			<tspan x="14.6" y="0">ARA</tspan>
		</text><text transform="translate(471.39 692.17) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
			font-weight="500">
			<tspan x="0" y="0">OIBA</tspan>
		</text><text transform="translate(420.11 694.32) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.85"
			font-weight="500">
			<tspan x="0" y="0">G</tspan>
			<tspan x="4.66" y="0" letter-spacing="-.01em">U</tspan>
			<tspan x="9.05" y="0">ADA</tspan>
			<tspan x="22.64" y="0" letter-spacing="-.01em">L</tspan>
			<tspan x="26.26" y="0">UPE</tspan>
		</text><text transform="translate(452.85 658.95) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
			font-weight="500">
			<tspan x="0" y="0">G</tspan>
			<tspan x="3.13" y="0" letter-spacing="-.01em">U</tspan>
			<tspan x="6.08" y="0" letter-spacing="0em">AP</tspan>
			<tspan x="12.08" y="0" letter-spacing="-.04em">O</tspan>
			<tspan x="15.07" y="0" letter-spacing="0em">TÁ</tspan>
		</text><text transform="translate(388.85 668.66) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.92"
			font-weight="500">
			<tspan x="0" y="0">CONTR</tspan>
			<tspan x="16.15" y="0" letter-spacing="-.06em">AT</tspan>
			<tspan x="21.84" y="0">ACIÓN</tspan>
		</text><text transform="translate(424.19 643.17) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.5"
			font-weight="500">
			<tspan x="0" y="0">CHIMA</tspan>
		</text><text transform="translate(270.58 499.18)" font-family="IBM Plex Sans" font-size="10.52"
			font-weight="500">
			<tspan x="0" y="0">SIMACOTA</tspan>
		</text><text transform="translate(440.44 575.54) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="0em">H</tspan>
			<tspan x="6.45" y="0" letter-spacing="-.06em">A</tspan>
			<tspan x="11.88" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="17.2" y="0">O</tspan>
		</text><text transform="translate(533.46 584.55) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.75"
			font-weight="500">
			<tspan x="0" y="0">SAN GIL</tspan>
		</text><text transform="translate(563.27 531.71) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.75"
			font-weight="500">
			<tspan x="0" y="0">AR</tspan>
			<tspan x="8.7" y="0" letter-spacing="-.06em">A</tspan>
			<tspan x="12.8" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="16.8" y="0">OCA</tspan>
		</text><text transform="translate(492.39 425)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">GIRÓN</tspan>
		</text><text transform="translate(545.15 402.82) rotate(-49.28)" font-family="IBM Plex Sans" font-size="4.39"
			font-weight="500">
			<tspan x="0" y="0">F</tspan>
			<tspan x="2.41" y="0" letter-spacing="-.03em">L</tspan>
			<tspan x="4.65" y="0" letter-spacing="0em">ORIDAB</tspan>
			<tspan x="20.22" y="0" letter-spacing="0em">L</tspan>
			<tspan x="22.64" y="0">ANCA</tspan>
		</text><text transform="translate(257.59 741.03)" font-family="IBM Plex Sans" font-size="7.44"
			font-weight="500">
			<tspan x="0" y="0">EL PEÑON</tspan>
		</text><text transform="translate(402.5 686.36) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.3"
			font-weight="500">
			<tspan x="0" y="0">EL </tspan>
			<tspan x="-7.76" y="3.96">G</tspan>
			<tspan x="-5.52" y="3.96" letter-spacing="-.01em">U</tspan>
			<tspan x="-3.4" y="3.96">ACAM</tspan>
			<tspan x="6.04" y="3.96" letter-spacing="-.07em">A</tspan>
			<tspan x="7.99" y="3.96" letter-spacing="-.01em">Y</tspan>
			<tspan x="9.96" y="3.96">O</tspan>
		</text><text transform="translate(217.11 767.84)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">SUCRE</tspan>
		</text><text transform="translate(528.8 615.77) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="-.04em">V</tspan>
			<tspan x="2.81" y="0">ALLE DE</tspan>
			<tspan x="-.58" y="5.52">SAN JOSE</tspan>
		</text><text transform="translate(514.78 534.3) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.58"
			font-weight="500">
			<tspan x="0" y="0">VIL</tspan>
			<tspan x="5.26" y="0" letter-spacing="0em">L</tspan>
			<tspan x="7.23" y="0">ANUE</tspan>
			<tspan x="16.51" y="0" letter-spacing="-.04em">V</tspan>
			<tspan x="18.69" y="0">A</tspan>
		</text><text transform="translate(229.67 839.7)" font-family="IBM Plex Sans" font-size="8.09" font-weight="500">
			<tspan x="0" y="0">F</tspan>
			<tspan x="4.44" y="0" letter-spacing="-.03em">L</tspan>
			<tspan x="8.57" y="0">ORIÁN</tspan>
		</text><text transform="translate(191.01 809.71)" font-family="IBM Plex Sans" font-size="8.09"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="0em">L</tspan>
			<tspan x="4.45" y="0">A BELLE</tspan>
			<tspan x="34.85" y="0" letter-spacing="0em">Z</tspan>
			<tspan x="39.77" y="0">A</tspan>
		</text><text transform="translate(711.66 538.09) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.03"
			font-weight="500">
			<tspan x="0" y="0">CARCASÍ</tspan>
		</text><text transform="translate(474.73 291.74)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">RIONEGRO</tspan>
		</text><text transform="translate(574.14 256.77)" font-family="IBM Plex Sans" font-size="7.3" font-weight="500">
			<tspan x="0" y="0">SUR</tspan>
			<tspan x="13.72" y="0" letter-spacing="-.06em">A</tspan>
			<tspan x="18.15" y="0">TÁ</tspan>
		</text><text transform="translate(585.53 358.74)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0" letter-spacing="-.01em">TONA</tspan>
		</text><text transform="translate(629.67 441.16) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.63"
			font-weight="500">
			<tspan x="0" y="0">G</tspan>
			<tspan x="5.87" y="0" letter-spacing="-.01em">U</tspan>
			<tspan x="11.4" y="0">ACA</tspan>
		</text><text transform="translate(682.67 450.67) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.48"
			font-weight="500">
			<tspan x="0" y="0">CERRI</tspan>
			<tspan x="26.04" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="31.67" y="0">O</tspan>
		</text><text transform="translate(678.18 499.63) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.48"
			font-weight="500">
			<tspan x="0" y="0">CONCEPCIÓN</tspan>
		</text><text transform="translate(216.75 527.97)" font-family="IBM Plex Sans" font-size="10.52"
			font-weight="500">
			<tspan x="0" y="0">PUE</tspan>
			<tspan x="19.52" y="0" letter-spacing="-.02em">R</tspan>
			<tspan x="25.83" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="32.07" y="0">O</tspan>
			<tspan x="3.2" y="12.62" letter-spacing="-.09em">P</tspan>
			<tspan x="9.01" y="12.62">ARRA</tspan>
		</text><text transform="translate(277.65 303.83)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">PUERTO</tspan>
			<tspan x="0" y="14.02">WILCHES</tspan>
		</text><text transform="translate(144.44 758.12)" font-family="IBM Plex Sans" font-size="8.09"
			font-weight="500">
			<tspan x="0" y="0">BOLI</tspan>
			<tspan x="17.35" y="0" letter-spacing="-.04em">V</tspan>
			<tspan x="22.28" y="0">AR</tspan>
		</text><text transform="translate(111.36 613.34)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">CIMITARRA</tspan>
		</text><text transform="translate(264.48 382.8)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">BARRANCABERMEJA</tspan>
		</text><text transform="translate(482.7 242.55)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">EL PLAYÓN</tspan>
		</text><text transform="translate(634.65 629.27) rotate(-88.76)" font-family="IBM Plex Sans" font-size="7.54"
			font-weight="500">
			<tspan x="0" y="0">SAN J</tspan>
			<tspan x="21" y="0" letter-spacing="-.01em">O</tspan>
			<tspan x="26.13" y="0">AQUÍN</tspan>
		</text><text transform="translate(526.98 372.21)" font-family="IBM Plex Sans" font-size="4.39"
			font-weight="500">
			<tspan x="0" y="0">BUCARAMANGA</tspan>
		</text>

		<text transform="translate(354.76 624.89) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.5"
			font-weight="500">
			<tspan x="0" y="0">SAN</tspan>
			<tspan x="12.87" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="16.43" y="0">A HELENA</tspan>
			<tspan x="8" y="7.8">DEL OPÓN</tspan>
		</text><text transform="translate(324.39 734.71)" font-family="IBM Plex Sans" font-size="6.41"
			font-weight="500">
			<tspan x="0" y="0">VÉLEZ</tspan>
		</text><text transform="translate(346.65 705.03) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.01"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="0em">L</tspan>
			<tspan x="4.96" y="0">A </tspan>
			<tspan x="13.2" y="0" letter-spacing="-.09em">P</tspan>
			<tspan x="18.18" y="0">AZ</tspan>
		</text><text transform="translate(345.87 746.58)" font-family="IBM Plex Sans" font-size="5.99"
			font-weight="500">
			<tspan x="0" y="0">CHI</tspan>
			<tspan x="9.86" y="0" letter-spacing="-.09em">P</tspan>
			<tspan x="13.17" y="0" letter-spacing="-.06em">A</tspan>
			<tspan x="16.81" y="0">TÁ</tspan>
		</text><text transform="translate(312.51 793.5)" font-family="IBM Plex Sans" font-size="6.41" font-weight="500">
			<tspan x="0" y="0">G</tspan>
			<tspan x="4.36" y="0" letter-spacing="-.01em">U</tspan>
			<tspan x="8.47" y="0" letter-spacing="-.04em">A</tspan>
			<tspan x="12.5" y="0" letter-spacing="-.04em">V</tspan>
			<tspan x="16.41" y="0" letter-spacing="-.06em">AT</tspan>
			<tspan x="23.81" y="0">A</tspan>
		</text><text transform="translate(385.5 708.6) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.93"
			font-weight="500">
			<tspan x="0" y="0">AG</tspan>
			<tspan x="6.64" y="0" letter-spacing="-.01em">U</tspan>
			<tspan x="9.8" y="0">ADA</tspan>
		</text>
		<text transform="translate(517.87 638.61) rotate(-86.51)" font-family="IBM Plex Sans" font-size="4.6"
			font-weight="500">
			<tspan x="0" y="0">SAN BENI</tspan>
			<tspan x="20.32" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="23.05" y="0">O</tspan>
		</text>
		<text transform="translate(468.91 630.26) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.03"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="-.09em">P</tspan>
			<tspan x="2.22" y="0">PALMAS DEL</tspan>
			<tspan x="2.69" y="4.83">SOCORRO</tspan>
		</text>><text transform="translate(481.15 646.94) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
			font-weight="500">
			<tspan x="0" y="0">CONFINES</tspan>
		</text><text transform="translate(480.93 592.45) rotate(-88.77)" font-family="IBM Plex Sans" font-size="3.65"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="-.09em">P</tspan>
			<tspan x="2.02" y="0">ALMAR</tspan>
		</text><text transform="translate(631.79 486.87) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.63"
			font-weight="500">
			<tspan x="0" y="0">SAN</tspan>
			<tspan x="-7.95" y="10.35">ANDRES</tspan>
		</text><text transform="translate(592.42 506.87) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.38"
			font-weight="500">
			<tspan x="0" y="0">CEPI</tspan>
			<tspan x="13.64" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="17.14" y="0" letter-spacing="0em">A</tspan>
		</text><text transform="translate(605.99 450.22) rotate(-66.31)" font-family="IBM Plex Sans" font-size="7.38"
			font-weight="500">
			<tspan x="0" y="0">SAN</tspan>
			<tspan x="14.6" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="18.64" y="0">A BÁRBARA</tspan>
		</text><text transform="translate(663.01 517.25) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.8"
			font-weight="500">
			<tspan x="0" y="0">MA</tspan>
			<tspan x="5.86" y="0" letter-spacing="0em">L</tspan>
			<tspan x="7.95" y="0">AGA</tspan>
		</text><text transform="translate(460.87 356.61)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">LEBRIJA</tspan>
		</text><text transform="translate(543.86 295.13)" font-family="IBM Plex Sans" font-size="7" font-weight="500">
			<tspan x="0" y="0">MATANZA</tspan>
		</text><text transform="translate(602.33 295.16) rotate(-36.23)" font-family="IBM Plex Sans" font-size="2.83"
			font-weight="500">
			<tspan x="0" y="0">CALIFORNIA</tspan>
		</text><text transform="translate(609.16 308.36)" font-family="IBM Plex Sans" font-size="7.3" font-weight="500">
			<tspan x="0" y="0">V</tspan>
			<tspan x="4.72" y="0" letter-spacing="0em">E</tspan>
			<tspan x="8.92" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="12.93" y="0">AS</tspan>
		</text><text transform="translate(570.21 326.35)" font-family="IBM Plex Sans" font-size="7.3" font-weight="500">
			<tspan x="0" y="0">C</tspan>
			<tspan x="4.77" y="0" letter-spacing="0em">H</tspan>
			<tspan x="10.02" y="0">A</tspan>
			<tspan x="14.88" y="0" letter-spacing="-.02em">R</tspan>
			<tspan x="19.25" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="23.26" y="0" letter-spacing="0em">A</tspan>
		</text><text transform="translate(545.73 427.21)" font-family="IBM Plex Sans" font-size="7.84"
			font-weight="500">
			<tspan x="0" y="0">PIEDECUES</tspan>
			<tspan x="40.61" y="0" letter-spacing="-.06em">T</tspan>
			<tspan x="44.91" y="0" letter-spacing="0em">A</tspan>
		</text><text transform="translate(516.8 488.84) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="-.03em">L</tspan>
			<tspan x="4.57" y="0" letter-spacing="0em">OS SAN</tspan>
			<tspan x="36.13" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="41.45" y="0" letter-spacing="0em">OS</tspan>
		</text><text transform="translate(283.17 835.75) rotate(-38.72)" font-family="IBM Plex Sans" font-size="6.41"
			font-weight="500">
			<tspan x="0" y="0">JESÚS MARÍA</tspan>
		</text><text transform="translate(347.03 530.08)" font-family="IBM Plex Sans" font-size="10.52"
			font-weight="500">
			<tspan x="0" y="0">EL CARMEN DE</tspan>
			<tspan x="14.15" y="12.62">CHUCURÍ</tspan>
		</text>
		<text transform="translate(355.48 453.93)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">SAN VICENTE</tspan>
			<tspan x="3.29" y="14.02">DE CHUCURÍ</tspan>
		</text><text transform="translate(459.57 492.52) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
			font-weight="500">
			<tspan x="0" y="0" letter-spacing="0em">Z</tspan>
			<tspan x="5.45" y="0">A</tspan>
			<tspan x="11.42" y="0" letter-spacing="-.09em">P</tspan>
			<tspan x="16.36" y="0" letter-spacing="-.06em">A</tspan>
			<tspan x="21.8" y="0" letter-spacing="-.01em">T</tspan>
			<tspan x="27.12" y="0" letter-spacing="0em">OCA</tspan>
		</text><text transform="translate(407.33 409.54)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">B</tspan>
			<tspan x="7.37" y="0" letter-spacing="0em">E</tspan>
			<tspan x="14.09" y="0">TULIA</tspan>
		</text><text transform="translate(344.4 272.54)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0">SABANA DE </tspan>
			<tspan x="16.9" y="14.02" letter-spacing="0em">TORRES</tspan>
		</text><text transform="translate(245.77 686.26)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
			<tspan x="0" y="0" letter-spacing="0em">L</tspan>
			<tspan x="6.94" y="0" letter-spacing="0em">LANDAZURI</tspan>
		</text>
		</g>
	</svg>

	<!-- 	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 788.66 885.68" width="900" height="900">
		<g id="SAN_MIGUEL">
			<path
				d="m439.95,217.34c-1.47-.84-.97-1.37-.96-2.87,1.91-.3,3.85-1.71,5.76-.52,1.9,1.19,7.62,9.47,8.28,12.1l26.6-.79,8.82,5.53c1.26-.27,2.62-8.54,2.43-10.01-.27-2.08-3.82-5.33-3.51-7.77.24-1.91,4.63-4.7,5.46-5.95,2.01-3.04,2.95-9.31,7.59-11.42,2.22-.99,3.74,1.84,5.32,2.07,1.13.16,2.58-.79,4.55-.33,1.55.37,3.21,2.33,5.32,2.52,2.62.23,4.03-2.08,6.25-1.6,1.1.24,2.22,1.89,3.58,2.27,1.69.48,10.31,3.07,10.87,3.09,3.76.11,6.02-3.32,7.04-3.52,1.74-.33,2.99,3.02,4.87,3.58.65.19,12.5,1.07,13.15.95,1.86-.36,2.58-3.7,3.87-4.66,3.69-2.73,15.68,2.2,20.65.13,2.61,4.61-2.82,6.53-2.84,10.96-.02,7.54,3.24,14.59,5.33,21.62,1.39,2.82,8.09,2.56,10.97,4.41,2.04,1.31,3,4.73,5.31,6.22,1.66,1.07,3.5.91,5.38,2.3,1.48,1.09,2.74,4.09,2.96,4.22.39.22,2.47-1.37,3.25.13,1.6,2.41-1.48,8.68-1.55,12.06-.11,5.6,4.13,7.97,6.83,12.45,1.26,2.09,1.51,5.37,2.79,6.82,1.13,1.29,3.44,1.47,4.93,2.76,1.85,1.59,2.16,4.22,3.32,5.33s3.7.98,4.24,2.55c.46,1.35-1.73,12.32-1.44,15.29.19,1.89,2.62,3.29,2.39,4.64-.48,2.87-15.18,8.83-14.39,14.48.2,2.35,2.88,3.06,3.46,4.4.77,1.77-.5,3.03-.3,4.18.12.67,6.43,9.31,7.48,9.74,1.45.6,4.02-1.36,4.74,1.01.78,2.55-2.17,4.31-1.89,7.51.28,3.24,5.07,7.69,4.81,8.74-9.36,4.23-5.32,10.48-4.05,17.53.24,1.35-1.07,3.24-.37,4.14,1,1.29,5.39.66,6.15,3.31l-1.73,3.83,1.99,1.08c5.51-2.45,8.43,3.23,8.59,8.06,3.18,1.04,4.43-3.21,6.42-3.7,1.84-.45,6.37,1.74,8.94.82.42.29-2.92,3.99-2.9,5.29.02,1.76,1.95,2.32,2.35,3.31.36.89-.45,2.19-.14,3.12,1.07,3.22,3.76,6.49,3.59,10.36,2.94.1,3.39-3.37,6.29-3.85,3.47-.58,6.24,2.05,7.5,1.8,1.09-.22,1.91-2.25,3.23-2.62,6.39-1.81,6.26,8.08,7,12.36,2.16.49,1.56-.29,2.21-1.2,1.69-2.36,2.08-5.96,5.14-7.3,1.47-.27,5.54,3.69,9.47,3.22.99-.12,2.03-1.46,3.82-1.54,1.65-.07,3.55,1.06,4.71.83,1.24-.24,4.49-3.78,8.16-4.08,2.74-.23,8.03,3.6,8.63,5.96.54,2.11-1.19,4.01-.67,6.26.32,1.4,2.38,3.06,3.1,4.67,3.43,7.68-1.84,8.2-2.3,12.95-.09.97,1.1,8.68,1.39,9.1,4.18,2.67,6.94,1.36,11.05-.12l-.05-4.1c8.3-4.74,7.51,5.61,5.28,10.91.56,2.75,2.84,1.42,3.54,2.46.72,1.06.6,3.57,1.42,5.14s3.08,2.19,3.31,4.41c.06.53-1.87,9.61-2.13,10.38-.44,1.28-5.14,4.11-5.27,4.89-.08.52.53,9.74.69,10.44.16.7,1.58.77,1.81,1.42,1.66,4.56,1.99,8.97-2.79,11.27,3.88,2.8,5.25,7.76,4.76,12.42-.16,1.48-1.54,2.9-1.54,3.91,0,2.04,2.23,5.14,1.63,9.66-.06.45-3.85,7.53-4.16,7.85-.86.91-3.15.91-4.48,1.77-5.61,3.63-2.75,9.72-5.77,13.21-1.35,1.56-4.43.48-5.62,3.27-2.67,6.24,2.51,7.66,2.58,11.29.07,3.79-4.37,6.98-4.46,9.42-.09,2.55,1.18,7.69,4.08,8.34,1.7,6.21-5.43,4.33-9.21,5.24-1.41.34-6.03,2.55-7.14,3.42-5.9,4.61-5.68,9.68-14.43,12.46-1.66.53-6.84,1.77-8.25,1.46-.32-.07-5.49-2.8-5.7-3.04-.41-.48-5.33-10.19-5.75-11.3-2.61-6.78-4.71-16.4-9.86-22.33-3.97-4.58-10.75-3.98-13.2-6.56-1.22-1.28-.06-2.64-.48-3.37-.19-.32-5.45-4.47-5.81-4.54-.83-.16-5.16.81-6.1,1.26-.37.18-6.53,4.4-6.84,4.68-2.56,2.34-1.5,5.13-2.69,8.13,3.55,3.29,4.96,1.77,7.84,2.23,4.75.75,3.97,5.77,5.43,9.04,2.2,4.91,8.02,4.43,12.54,4.23-4.19,6.59,3.22,10,3.69,13.14.24,1.58-2.59,3.58-3.17,5.15-1.46,3.96-.45,14.18.55,18.58.47,2.09,2.97,4.96,2.59,6.71-.21.97-1.71,1.64-2.18,2.6-.56,1.16-.25,2.97-.73,3.56s-3.88.51-5.05,3.11c-4.37,9.68,9.82,8.69,8.14,18.24-3.63-2.51-6.69,3.97-7.26,6.74-.21,1.01-1.44,8.59-1.44,9.06-.03,1.91,1.91,4.48.62,6.33-1.02,1.46-4.67,1.15-6.04,2.16-1.2.89-1.97,4.86-3.79,5.4l-8.96-2.36c-4.97,7.13-7.32,15.46-10.1,23.57-4.09,2.89-9.27,1.73-13.91,1.93l-1.11,6.58c-7.97-1.6-8.16,3.69-11.87,6-1.53.95-2.98-.1-3.84.79-1.4,1.46-1.4,4.97-2.33,6.21-.38.51-6.42,3.78-7.27,4.4-4.2,3.1-3.32,11.52-6.25,16.64-.89,1.55-6.02,4.87-7.07,6.54-1.41,2.26-1.75,5.36-3.11,7.51-1.69,2.67-5.21,5.34-6.66,8.73-5.72-1.28-3.71-6.4-4.5-8.91-.43-1.36-2.1-1.97-2.5-3.3-.76-2.5.29-6.17-1.18-8.91-1.15-.79-8.44,3.01-9.3,4.08-1.35,1.69-1.69,6.92-3.78,10.2-.35.54-5.28,5.53-5.78,5.8-.26.14-5.33,1.56-5.63,1.58-.89.08-1.93-1.12-2.8-1.07-4.42.29-6.38,2.59-10.62,1.54-6.71-1.66-4.01-3.03-6.17-6.37-.21-.32-5.28-5.02-5.6-5.24-1.42-.94-7.6-3.23-8.88-2.44-.72,2.69-6.16,6.11-6.61,7.94-.5,2.03,1.44,3.27,1.42,4.6,0,.31-1.14,5.67-1.26,5.96-.29.72-5.16,6.75-5.99,7.54-1.87,1.77-4.67,2.15-6.28,4.29-.45.6-6.81,12.43-7.02,13.15-.65,2.22.33,3.93-1.11,6.58-4.13,7.55-21.27,1.66-23.81,4.51-2.67,4.66-2.54,11.41-4.63,16.03-.98,2.18-4.55,6.07-6.03,8.37-1.33,2.06-2.32,7.94-5.59,7.85-2.46-.07-9.88-7.59-14.73-4.64l-.1-9.44-13.35-8.33-.98-1.87,3.14-9.41c.55-1.44,4.38-7.59,4.47-8.06.18-.92-3.14-4.71-1.83-7.07l2.8.92,4.4-7.64c4.24,7.62,8.85,8.12,12.59.01,1.02-2.22.44-4.83,1.26-6.05.64-.96,2.53-1.06,2.86-2.25s-.82-2.63-.45-4.24c.41-1.76,3.56-5.13,4.14-7.42.29-1.12,1.82-11.64,1.74-12.25-.1-.81-7.72-12.63-8.74-13.86-3.44-4.12-2.38-1.75-4.78-6.71-1.83-3.78-5.5-2.14-9.06-3.41-5.01-1.8-7.13-5.41-10.68-7.62-1.55-.97-10.41-5.35-11.41-4.93-4.25,1.77-5.5,14.72-9.11,19.78-3.39,4.76-6.28,4.74-9.86,6.95-.68.42-1.83,3.3-3.62,4.55-6.59,4.59-6.33.92-8.06,11.15-1.24,7.34-.37,10.17-4.9,17.24-2.43,3.8-3.81,2.78-6.63,4.85-3.09,2.28-4.82,5.05-2.76,8.78,1.15,2.08,10.39,5.02,10.41,10.29.02,4.43-6.02,12.87-7.52,17.56-2.78,8.7-.24,16.96-2.6,25.26-.21.74-4.14,9.5-4.47,9.93-6.3,8.1-10.44-10.03-12.66-11.62-1.41-1-4.07-.65-6-2.17s-3.96-6.19-6.7-7.71c-2.08,5.1-6.11,2.3-8.61,2.3-1.38,0-2.62,1.15-3.99,1.24-1.62.11-3.3-1.21-4.95-.44-.54.25-7.16,7.81-7.94,8.86-1.07,1.43-.79,3.44-1.83,4.89-.28.39-6.93,5.91-7.4,6-5.46,1.03-2.3-7.42-3.28-8.4-7.52-4.28-11.36,7.57-15.03,8.45-6.95,1.66-2.27-6.69-3.16-7.54-4.87-.87-10.29.03-14.93-.16-4.89-.19-3.49-.73-9.68-.13-1.92.19-3.52-1.07-6.36-.45-3.21.71-4.53,4.62-6.97,6.47-3.53-2.3-4.4-5.8-6.4-8.95-1.07-1.69-3.23-3.04-3.93-4.71-.94-2.25-.34-5.48-.87-6.34-.34-.56-3.36-.64-4.2-2.04-1.37-2.29.65-7.02-1.91-9.62-.39-.4-8.33-3.65-8.8-3.68-1.35-.08-4.48,3.03-8.02.32-1.61-1.23-.52-3.94-1.14-5.58-.46-1.22-1.76-1.84-2.11-2.69-1.67-4.03-2.52-8.16-5.31-11.99-3.53.42-7.95,1.34-11.36-.09-1.54-.64-1.97-3.04-3.55-4.2-3.38-2.47-9.51-2.81-11.57-7.65-2.57,3.65-1.01,10.87-6.24,11.54,3.9,7.56,1.89,10.61-6.55,8.8l-4.63,7.39c-10.22.17-16.59-.51-18.46-11.75,3.19-3.16,6.69-7.27,9.92-10.25,2.25-2.08,4.96-1.95,7.08-5.41,5.53-9.01-2.66-7.62-2.73-11.36l5.61-10.45c-.27-1.44-10.17-9.15-11.34-11-3.3-5.2-1.74-11.16-2.47-16.02-.28-1.84-3.14-1.88,1.27-3.53l.1-2.64c-3.8-.3-8.17-5.64-11.43-5.86,1.81-.09,4.02-1.24,6.09-.04,1.06.61.37,2.16.83,2.66.75.81,4.92.69,6.12,3.15-1.39,5.73-2.31,12.79-.33,18.5,1.4,4.05,12.85,12.67,12.88,15.59-.1,3.02-7.27,8.47-5.01,11.12.91,1.07,2.7-.05,3.63,2.24,1.58,3.9-1.49,8.74-4.52,11.14-5.17,3.05-9.55,7.79-13.78,12.14,1.24,2.75,1.8,8.18,4.68,9.45l10.13,1.15c3.47-5.03,5.76-10.34,12.9-7.38,1.36-2.32-2.09-6.36-1.91-7.15.2-.88,2.72-1.4,3.63-3.14,1.06-2.05,1.35-8.58,1.63-8.94,2.84-3.65,6.66,2.38,8.82,3.71s4.88,1.72,6.61,3c1.43,1.06,2.08,3.34,2.59,3.64.41.24,6.21,1.22,6.94,1.17,1.67-.11,3.35-2,4.38-1.49,2.23,1.11,6.93,13.88,9.37,16.66l-.75,4.06c3.91-.03,16.84,1.06,18.99,4.08s-1.09,11.91,5.99,11.31c-3.11,6.3,1.68,7.04,3.85,10.31,1.92,2.9,1.12,6.47,5.44,7.8,1.14.37,4.45-4.82,6.78-5.47,2.84-.8,9.93,1.67,14.06-1.17,1.29,3.84,3.44.97,5.88,1.19,4.42.4,8.12,2.13,12.96.78,1.33,1.32-2.97,8.28.5,7.7,3.94-.66,8.95-13.69,17.34-7.76,1.19,1.7-.86,8.86,1.85,7.74.79-1.84,5.49-4.25,5.99-5.06.59-.96.18-2.66,1.17-4.1,2.23-3.27,7.57-6.35,8.85-10.91,3.79,1.64,6.47.33,9.96.12,1.08-.06,2.31.66,3.64.53,3.1-.31,3-4.05,4.96-3.62,3.32,1.92,6,6.69,8.66,8.65,2.05,1.51,4.79,1.74,6.31,3.29,2.94,3.02,1.41,12.75,8.95,11.18.51-.11,3.87-8.36,4.18-9.46,2.41-8.47.48-16.14,2.66-24.24,1.36-5.07,7.94-15.14,7.99-18.92.04-3.56-9.18-7.57-10.77-9.88-.17-.24-1.22-5.57-1.09-5.84.72-1.51,7.29-5.41,8.71-6.65,2.11-1.84,6.02-9.03,6.53-11.73.9-4.78-1.09-14.24,4.57-17.51,4.59-2.64,5.09-4.1,7.67-5.78,1.15-.75,3.01-.49,4.3-1.46,7.16-5.41,7.79-10.43,10.53-18.3,1.02-2.93.35-6.21,5.02-7.52,1.45-.41,10,3.22,11.71,4.24,5.42,3.24,10.42,12.67,18.78,7.13.57.1,3.02,8.34,3.64,9.35.4.64,2.75,1.4,3.99,3.21,1.54,2.25,8.58,12.73,8.82,14.31.36,2.31-1.2,3.35-1.51,5.37-1.49,9.64-4.87,17.78-8.35,26.22-1.2,2.91-3.88,4.36-3.23,7.84-5.89.23-7.4-.75-11.5-4.82-.13,2.64-3.7,4.72-4.35,6.77-.54,1.69.72,3.11.29,4.95-.65,2.82-7.82,16.77-6.53,18.05,1.93-.25,3.37.34,5.01,1.25.49.27,7.62,6.08,7.9,6.5,1.36,2.07,1.89,6.37.54,8.58,5.65-1.03,9.7,3.47,14.8,4.89,3.98-7.79,9.13-14.57,11.89-22.95.45-1.38-.53-2.8-.17-4.05,3.03-10.63,15.58-2.4,23.71-6.91,4.97-2.76,1.95-4.92,2.75-7.73.41-1.44,2.35-2.28,3.12-3.69,1.58-2.91,3.79-9.87,5.33-11.71,1.21-1.46,3.65-1.31,5.38-2.56.48-.35,5.58-6.38,5.77-6.79.83-1.86-.19-2.98-.03-4.3.12-.91,1.33-1.61,1.25-2.72-.09-1.22-1.62-2.09-1.33-3.6.12-.64,7.67-9.75,8.24-10.06,1.25-.68,7.19,1.26,8.57,2.05.47.27,7.24,6.02,7.49,6.44,1.83,2.99.2,6.52,7.36,6.37,1.71-.04,15.23-2.17,16.34-2.9.31-.2,5.02-5.03,5.2-5.36,1.4-2.44,1.39-8.97,3.07-10.38,2.14-1.79,11.52-5.64,12.46-.94.96,4.8,1.83,10.2,3.34,14.91.47,1.47,1.54,3.22,3.39,2.84-.05-3.71,3.76-5.27,5.23-7.75,1.33-2.26,1.35-5.21,2.98-7.58,1.41-2.05,6.19-4.64,7.16-6.57.85-1.7.1-4.29.57-5.02.41-.64,2.1-.61,2.36-1.59.51-2-.66-4.42.47-7,.82-1.86,7.76-6.4,9.9-6.86-1.02-7.65,4.56-6.12,7.45-8.45,1.43-1.15,1.11-4.13,2.92-4.76l7.97-.19.22-6,1.4-1.56c4.45,1.44,8.4.29,12.89-.5,1.76-8.81,6.43-15.9,9.48-24.08,4.07-1.75,7.27.62,11.01,1.55,2.31-4.13,3.89-6.91,9.26-6.81-1.54-5.62-.99-15.22,2.43-20.13,1.11-1.59,4.29-3.08,4.37-3.55.94-6.12-9.01-3.32-8.78-11.33.23-7.83,5.45-6.86,6.65-8.34.56-.69-.16-2.81.14-4.02.2-.8,1.88-.65,1.97-1.24.36-2.42-2.05-3.86-2.43-5.69-.55-2.57-1.43-17.32-.7-19.39.61-1.72,3.11-4.22,2.93-5.39-.35-2.32-6.91-6.99-3.69-11.21-4.93-.86-9.75-.54-11.94-5.85-.8-1.93-.63-6.11-2.46-7.15-2.27-1.29-8.84-.14-11.54-4.79l1.99-2c-3.05-5.96,2.24-6.88,6.56-9.64.79-.51,1.01-1.9,1.75-2.36,1.1-.67,5.54-.2,7.33-1.7.98,2.39,6.92,4.45,7.21,4.78.89,1.05-1.95,5.29,4.63,6.31,1.32.2,3.16-1.14,4-.56.32.22.61,1.94,1.55,2.78,12.24,10.94,10.47,23.35,18.35,34.52,3.4,4.82,7.8,3.81,12.72,2.26,9.08-2.86,11.28-11.76,20.38-15.18,3.27-1.23,6.19-1.24,9.6-1.44,1.07-3.37-2.07-2.69-2.72-4.46-.22-.59-1.08-5.97-1.08-6.73-.02-3.99,5.35-5.71,3.61-11.18-.47-1.46-2.5-2.09-2.71-4.05-.08-.8.86-5.66,1.2-6.49,1.25-2.97,4.86-2.18,6.56-5.92,1.32-2.89.51-5.28,2.36-8.2s4.65-2.82,5.95-4.13c.92-.92,1.36-3.51,2.3-4.94.75-1.14,2.73-1.4,2.77-2.1.16-2.35-1.88-8-1.88-9.52,0-1.85,1.95-3.31,1.81-6.58-.23-5.51-4.23-6.99-4.77-9.82-.61-3.21,1.82-2.23,2.98-3.69,2.88-3.63-2.45-7-2.96-9.71-.15-.78-.03-8.6.13-9.63.58-3.72,5.03-4.57,5.54-5.34.11-.17-1.03-2.28-.73-3.98.32-1.83,2.91-4.27,2.59-5.87-.22-1.13-3.05-2.93-3.93-4.71-.68-1.38-.44-3.91-.92-4.41s-2.16.52-3-1.28c-2.45-5.25,1.14-5.7,1.23-6.18.31-1.54-.82-6.59-3.3-6.12l-1.51,5.71c-2.35-.7-4.96,1.7-6.36,1.79-.45.03-4.85-1.81-6.57-1.7-.44-.55-2.33-10.34-2.12-11.29.78-3.56,4.4-5.48,3.58-10.32-.38-2.23-2.79-3.24-3.44-5.27-1.36-4.31,1.42-9.54-4.56-11.85-5.03-1.95-8.28,2.8-10.8,3.19-2.08.32-4-.92-5.56-.7-.93.13-2.05,1.53-3.99,1.72-4.06.4-5.21-1.87-8.62-2.97-1.07.26-4.28,7.84-5.11,9.33-2.57,4.59-3.08-4.22-3.76-6.26-.44-1.33-2.8-7.79-3.65-7.85-4.15,5.19-8.99.61-13.33,2.06-1.3.44-2.32,4.66-5.78,3.63-2.32-1.55-1.3-4.69-2.28-7.38-1.27-3.49-1.64-4.22-2.66-7.87-.25-.9-1.72-1.85-1.97-2.87-.45-1.76,1.97-2.96,1.51-4.88-6.1-1.67-7.32,2.63-11.94,3.81-2-.63-2.1-8.12-6.26-9.18-1.73-.44-4.15,1.43-5.38-.36l.54-5.79c-8.79-1.45-5.04-3.28-5.78-8.09-.31-2.02-2.6-4.27-2.6-7,0-1.92,3.49-10.28,6.45-9.91v-.93c-5.78-2.39-4.99-9.47-2.88-14.43-1.83,1.44-7.25-2.6-8.09-3.91-2.15-3.33-2.66-8.62-3.92-11.46-.63-1.42-3.22-2.08-3.4-4.38-.54-6.73,10.75-9.97,14.4-14.45-3.46-4.84-1.46-5.37-1.23-9.66.16-2.85-1.81-5.53,1.18-8.52-4.28-1.55-4.92-6.08-7.05-8.19-1.36-1.35-4.04-1.63-5.37-3.27-1.16-1.43-4.73-7.97-6.69-10.86-5.89-8.71-3.28-10.99-.96-20.4-4.33,2.1-4.01-2.3-6.18-3.89-1.8-1.32-4.31-.82-6.03-2.15-1.24-.96-2.48-4.59-4.19-5.88-3.7-2.78-10.53-1.47-12.84-6.37-1.62-3.45-5.33-19.8-5.39-23.55-.02-1.41,5.14-4.16,3.9-7.14l-18.57-1.01c-3.56,8.34-5.08,4.21-10.75,3.85-2.59-.17-6.04,1.18-7.47.85-1.77-.41-2.88-3.84-4.79-3.54l-7.72,3.5c-3.07-1.72-10.66-4.8-13.86-5.35-2.43-.42-4.7,1.55-7.1,1.21-1.77-.25-3.04-1.81-4.43-2.14-3.09-.74-6.16-.51-9.15-2.08-4.77,2.42-5.19,7.1-7.56,10.84-.93,1.47-4.06,2.65-4.49,5.16-.45,2.65,3.44,5.15,3.55,8.35.08,2.42-1.83,11.22-5.16,11.16-2.15-.04-1.86-2.29-2.62-2.73-1.42-.83-3.76.8-4.57-2.58l-27.18.28-9.44-13.2c-4.02.34-3.02,1.78-3.73,2.79Z"
				stroke-width="0" </path> </g> <g id="SAN_MIGUEL">
				<g id="Layer_2">
					<g id="svg2">
						<path id="path1183"
							d="m719.43,572.33l-1.92,3.72.93,2.23-1.49,2.54.19,2.04-1.49,1.92-6.13,3.59-3.28,3.41-5.2-2.04,1.8-3.53.12-1.8-2.85-2.79-1.36-.74-.19-5.45-3.41-5.94-2.72-2.23-3.16-1.3-2.35-.19-.93-2.85-3.16-1.8-.62-2.29,2.04-1.05,2.41-.19,2.54-3.16,2.85-1.67,2.97.68,4.02-2.35h3.47l2.35,2.23-.62,2.66.93,2.54,1.92,1.36,3.16,2.85,3.1-.12,1.61-2.04,2.35-.87,1.05,2.79.19,4.46-.56,3.22,1.42,2.17Z"
							fill="#cd7d16" stroke="#000" stroke-miterlimit="10">
						</path>
					</g>
				</g><text transform="translate(664.02 548.22) rotate(1.24)" font-family="IBM Plex Sans" font-size="2.99"
					font-weight="500">
					<tspan x="0" y="0">SAN JOSE</tspan>
					<tspan x="-1.95" y="3.59">DE MIRANDA</tspan>
				</text>
		</g>

		<g id="MARACAVITA">
			<path id="path1185"
				d="m738.32,566.78l-.23,3.2-.63.46,1.76,2.25,2.18,4.67-1.43,4.92-3.49,2.75.34,2.06.75.97,1.65,6.23,2.98,1.6v1.43l-2.75,1.2h-2.06l-2.29.86-3.2.11-6.81,2.86-5.26,5.03-2.06,3.95-7.78,4.17-1.43.11-3.26,1.43-3.89-.06,3.32-6.35-.57-3.55,2.46-.4,3.89-2.57-.8-2.46.4-2.63,1.49-2.06-1.14-1.37.11-3.26-1.83-.11-1.37-1.77,3.03-3.15,5.66-3.32,1.37-1.77-.17-1.89,1.37-2.34-.86-2.06,1.77-3.43,2.34-1.94h3.72l2.86-4.35,1.94-.29.11-1.49,3.15-3.43,3.2.69,3.95,2.63-2.52,2.46Z"
				fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(712.2 592.92) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.84"
				font-weight="500">
				<tspan x="0" y="0">MACARAVITA</tspan>
				<tspan x="13.38" y="0" letter-spacing="-.04em"></tspan>
				<tspan x="15.79" y="0" letter-spacing="0em">VI</tspan>
				<tspan x="19.35" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="21.46" y="0">A</tspan>
			</text>
		</g>

		<g id="CAPITANEJO">
			<g id="Layer_2-2">
				<g id="svg2-2">
					<path id="path1187"
						d="m701.13,617.42l-5.41-4.55.12-3.22-2.99-3.11v-1.09l-1.55-1.78-.98-6.85-1.21-1.32.12-1.96-6.28-9.38.35-1.55-1.44-1.96-2.88-.86-1.21-2.99-3.11.86-5.41-3.05,6.85-7.08.06-1.38,2.42-.86.35-1.61,3.17-2.19,2.42-1.38.58,2.13,2.94,1.67.86,2.65,2.19.17,2.94,1.21,2.53,2.07,3.17,5.53.17,5.07,1.27.69,2.65,2.59-.12,1.67-1.67,3.28,4.89,1.96h0l1.27,1.67,1.84.12-.12,3.28,1.15,1.38-1.5,2.07-.4,2.65.81,2.48-3.91,2.59-2.48.4.58,3.57-2.99,6.45Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(682.43 572.31) rotate(60.41)" font-family="IBM Plex Sans" font-size="5.82"
				font-weight="500">
				<tspan x="0" y="0">CAPI</tspan>
				<tspan x="13.04" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="16.24" y="0" letter-spacing="0em">ANEJO</tspan>
			</text>
		</g>

		<g id="SAN_JOSE_DE_MIRANDA">
			<g id="Layer_2-3">
				<g id="svg2-3">
					<path id="path1189"
						d="m662.81,566.59l.48,2.83,3.98,1.99,1.93.36.44,2.11,5.89-6.81.18-3.32.84-2.65,2.65-3.8.36-2.11,1.63-2.35.66-4.16-1.15-2.95.48-1.51-.54-1.33v-2.77l.78-4.58-.48-4.4h0l-3.01.78-3.62-.96-2.71-1.93.48-2.11-.3-1.63-5.97,1.99-2.53-.12-1.81-.96-.48,1.81-2.29,1.27v2.89l1.33,2.47v2.47l-.72,2.59.12,4.16-.9,4.58.3,4.88,2.17,2.59-.54,2.17-2.05,2.89.9,5.49,3.5.12Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(664.02 548.22) rotate(1.24)" font-family="IBM Plex Sans" font-size="2.99"
				font-weight="500">
				<tspan x="0" y="0">SAN JOSE</tspan>
				<tspan x="-1.95" y="3.59">DE MIRANDA</tspan>
			</text>
		</g>

		<g id="MOLAGAVITA">
			<path id="path1191"
				d="m647.6,579.14l.42-2.58-1.8-1.08-.06-.9,6.11-4.44.9.3,2.82-.66-.66-1.8,2.76-1.98.66,1.08h1.8l.84-.9-.9-5.52,2.04-2.88.54-2.16-2.16-2.58-.3-4.86.9-4.56-.12-4.14.72-2.58v-2.46l-1.32-2.46v-2.88l-1.32-6.17.3-5.34-1.2-4.26h0l-2.28-1.74-6.53-.12-2.28,1.32-3.9,4.02-3.06,1.32-2.16,1.56-.42,3.12-1.56,4.56-2.76.42-1.2-1.74-1.32-.3-2.64,1.98h-3.48l-3.6-3.48-2.46-1.02-2.16,1.56-.3,1.32-2.28.72-2.16,5.04,2.4,1.2,2.4,2.46,3.48,3,1.98,4.38,1.26,4.74,2.16,3.24.84,3.48,4.44,3.72,2.82.72,2.22,2.28.54,3.66,1.68,3.48.54,4.98,1.5,3.42,3.18,2.46,4.14.06Z"
				fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(624.08 546.14) rotate(1.24)" font-family="IBM Plex Sans" font-size="5.67"
				font-weight="500">
				<tspan x="0" y="0">MO</tspan>
				<tspan x="8.88" y="0" letter-spacing="0em">L</tspan>
				<tspan x="12" y="0">AG</tspan>
				<tspan x="19.63" y="0" letter-spacing="-.04em">A</tspan>
				<tspan x="23.19" y="0">VI</tspan>
				<tspan x="28.46" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="31.57" y="0">A</tspan>
			</text>
		</g>

		<g id="ONZAGA">
			<g id="Layer_2-4">
				<g id="svg2-4">
					<path id="path1193"
						d="m647.66,578.53l-.12.82-1.29,1.4,4.18,1.54h3.6s4.49,2.84,4.89,2.84-.23,5.2-.23,5.2l2.51,4.32,2.51.53,2.31.31.61.8,2.04.29.95-1.09,3.03.92.29,1.11-1.29,1.11.93,6.02,4.61,3.97-.23,1.4-2.92,1.81-1.11,3.8-1.64,4.73.53,8.24,1.52,1.29-.23,4.44,2.4,1.52.93,2.45-.29,1.64-1.75,1.11-.88,1.34.88,2.63-5.43,2.16-1.17,5.14.18,4.03,3.8,2.69,2.98.41.23,2.86,1.99,2.16-.18,1.75-2.75-.88-3.15,2.22-.47,5.37-1.29.88-.7,7.36,1.93,5.61-1.05,2.28-1.52.41-2.63-.47-4.44,3.27-1.58,3.86-2.51-.7-1.52-1.64-6.25.18.06,2.51-2.51,1.93-.41,4.09-3.27,4.03,1.17,2.28-2.69,2.63.23,3.1-1.75,3.15-3.27.82-1.52,1.17-7.36-.29-.06-1.11-1.11.41,1.93-4.56-.53-2.34.99-1.99-.41-2.1-1.58-1.81-1.99,1.23-1.81-.88,1.4-2.4-1.58-2.51,2.98-1.99-.88-1.23-1.52-.58.41-2.34.82-2.22,2.28-1.23-.41-4.21-1.17-1.64-2.92-.53-2.69-1.99h-1.81l1.29-1.69.18-4.15-.7-1.69,1.58-1.81,2.1,1.11,1.52-1.81-1.4-2.22-1.4-4.73-1.4-3.33-2.92-2.4-3.27.12-2.1-3.39-.12-2.51-1.99-1.93.58-3.04-2.1-1.99-.82-2.69,2.92-3.62,1.69-3.8,1.81-.41,2.28,1.52,4.38-4.5,3.68-.18,3.39-1.23,1.58.41,1.99,1.29,2.69.29,2.98-1.81,4.61-5.02,2.92.53.58-.88-1.11-1.93,1.17-1.23v-2.34l-.99-1.81.99-.99.12-1.99-1.17-2.1.88-2.4-.41-3.04v-4.85l-.53-2.63.88-2.63-.7-1.23.88-3.1,1.81-1.93-1.11-1.64.58-1.69-.29-1.29-1.29-.99-.88-1.69.7-.88-1.11-1.4.41-1.4-.99-1.81.88-2.28,4.5-.06Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(621.62 656.75) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.08"
				font-weight="500">
				<tspan x="0" y="0">ON</tspan>
				<tspan x="12.72" y="0" letter-spacing="0em">Z</tspan>
				<tspan x="18.25" y="0" letter-spacing="0em">AGA</tspan>
			</text>
		</g>

		<g id="COROMORO">
			<g id="Layer_2-5">
				<g id="svg2-5">
					<path id="path1195"
						d="m607.86,734.06l1.33-2.82,2.14-1.06v.7l1.04.06,2.95-3.11.17-1.94,1.62-.82,7.01.41,1.51-2-.06-2.76-1.04-1.17,1.27-1.53,1.97-4.64-.52-2.35.98-2-.41-2.11-1.56-1.82-1.97,1.23-1.8-.88,1.39-2.41-1.56-2.53,2.95-2-.87-1.23-1.51-.59.41-2.35.81-2.23,2.26-1.23-.41-4.23-1.16-1.64-2.9-.53-2.66-2h-1.8l1.27-1.7.17-4.17-.7-1.7,1.56-1.82,2.09,1.12,1.51-1.82-1.39-2.23-1.39-4.76-1.39-3.35-2.9-2.41-3.24.12-2.09-3.41-.12-2.53-1.97-1.94.58-3.05-2.09-2-.81-2.7,2.9-3.64,1.68-3.82-1.1-2-1.51.12-3.48-1.64-2.26,2.23-2.49,1.64-3.36-.41-2.9,1.29-1.51,1.12-1.51.29-1.51,1.64h-1.39l-1.56,1.41-4.63.18-2.9-1.41-1.27.7-1.8.12-2.26-.7-1.1.59-2.2-1-1.16-1.12-1.33,1-.52,1.47-1.16,1.47-.23,1.7.58,1.76-.87,1.76.12,1.82-2.26,2.41-3.53,1.94-.52,4.64-2.9,2-3.65,4.17.12,3.41.98,2.64-4.17,2.64-3.65.18-3.94-.53.52,3.52,1.27,1.12.12,1.82-1.56,1.41-2.2,2.53-.64,1.88-1.62,1.29-1.51,2.11h-2.2l-1.1,4.88-1.16,2,1.68,3.94.12,2.64,1.16.12,2.37-1.64,1.16.29,1.1,1.41.58,2.82,2.9-2.35,2.49.88,1.39,2.7,1.91,2.11,2.95,1.12,1.8,2.47v2.82l4.17,1,5.21.53.81,2,3.65,1.59,1.74-2.11,2.9-1,.58,1.53,1.68.7.98,1.7,2.55,1.12,3.48.82,1.85-1.12h3.19l2.26,1.82,1.85-.41.98-1.94,2.66.18,1.8,3.11,3.94,3.35,3.07-1,1.27,4.05,3.36,2.06h0Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(553.15 691.59) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
				font-weight="500">
				<tspan x="0" y="0">COROMORO</tspan>
			</text>
		</g>

		<g id="GÁMBITA">
			<path id="path1201"
				d="m509.97,767.6l-1.43,2.73-6.15,4.19.06,4.65,2.03,2.73-1.97.81-.72,1.28,1.67.81-1.08,2.68c-.7.01-1.4.07-2.09.17-.3.06-.72,3.55-.72,3.55l-4.84,4.83-2.33-.06-3.11,3.9-1.49,5.23-1.25.81-.84,3.14-2.75,1.1-.54,4.88,1.31.58-.42,1.86-2.93,3.31-4.78,1.34h-8.06l-.36-.87-5.67.81-.42-.52-3.4,1.34-1.55,3.84-.54,3.55.96.76-1.91,1.98-1.55,7.33-2.15,1.34-4.54,6.75.06,2.91-4.12,3.95-1.49-2.09-5.26-1.86-.3-1.05-3.7-.29-2.39,1.22-1.49-2.21,1.61-1.28-.18-1.57-1.55-.76,1.02-1.45-1.37-3.31-4.78-1.4-4.9-5.64-3.4-.52-.66-3.72,2.99-4.13-.12-2.73,2.57-1.28.36-2.33-1.31-.58.36-1.1,2.81-1.8-.06-2.79-1.67-1.63,1.25-1.98,4.33-5.97,3.07,2.48,2.57.47.36,1.34h3.17l2.57-1.34-.06-2.91,2.69-1.92,1.37-2.62-.18-1.63-.96-.58,1.13-2.09,3.11-1.1-.18-3.31-.96-.52.66-1.92,2.15-1.45.42-4.13,2.21-1.16.06-2.62-.78-.64v-2.97l1.67-2.27v-2.85l.84-2.62-1.19-.93-1.19-.06-1.13-1.22-.72-3.2-1.73-.7-1.02-.87.42-2.85-4.3-3.2-.9-1.1,3.76-1.28,3.34-3.02,4.18-.87,3.17,1.63,4.18.17,2.87,1.8,1.85-1.98,1.85-3.37-.3-3.02.72-2.33,2.15.17,1.55-.81,3.34.52,2.33,1.28,2.33-.29,1.61,1.4,2.15-.81,3.46,2.62.72,1.92h1.13l2.33,2.21,1.61-.17,1.73,1.98,2.63-1.22,1.55,1.1,4.36.52,2.75-.7.84,2.38,3.34,3.31,2.03,2.91.54,2.33,3.58.52-1.91,2.09h0v4.07Z"
				fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(446.6 792.5) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
				font-weight="500">
				<tspan x="0" y="0">GÁMBITA</tspan>
			</text>
		</g>

		<g id="SUAITA">
			<g id="Layer_2-6">
				<g id="svg2-6">
					<path id="path1203"
						d="m405.5,730.86l2.83.23,1.04,1.79,4.62,2.72,2.77-.12,5.32,5.2,2.81,2.37,2.51.4,3.99.64,1.21-.92,2.31-.23-.42,1.32.25,1.17,2.15,4.83,1.2,1.2,1.9.67,3.7-1.27,3.24-3,4.04-.87,3.06,1.62,4.04.17,2.77,1.79,1.79-1.96,1.79-3.35-.29-3,.69-2.31,2.08.17,1.5-.81,3.24.52,2.25,1.27,2.25-.29,1.56,1.39,2.08-.81,3.35,2.6.69,1.91h1.1l2.25,2.2,1.56-.17,1.68,1.96,2.54-1.21,1.5,1.1,2.2.23.46-4.74v-7.74l1.5-3.06,1.27-3.87-.17-3.58-2.25-2.77-3.76-1.27-2.89-.4-1.1-.58-2.37.58-.4-1.5-1.79-1.79s-.98-2.08-1.39-2.2-1.68.98-1.68.98l-2.2-1.5-.29,2.31-2.66,1.68-2.25.17-3.47-1.96-1.96-.12-1.68-.98-.69-2.31.98-.98v-1.91l-.81-1.39-.12-1.27-1.96-1.68-2.48,1.79.52,1.5-1.56,1.21-1.1,1.79-1.96.17-1.79,2.08-2.54-.4-2.54.58-2.25-1.39,2.66-5.26,1.56-4.16-1.5-1.96-2.95-.17-3.87-3.7-1.68-3.06-2.2,1.27.69,4.97-1.39,2.77-.35,3.24-1.5.98-2.77-1.27-.87-2.6-3.52-.4-4.16.98-5.43-.52-3.76,2.37-.17,2.89-1.68,1.27-2.25.4-2.02-.29-.29,2.6-1.73,2.31.23,1.39-.58,1.04-1.62,1.1-1.68,1.62.46,1.96-1.85,2.6.4,3.06Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(432.89 731.2) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.09"
				font-weight="500">
				<tspan x="0" y="0">S</tspan>
				<tspan x="5.49" y="0" letter-spacing="-.01em">U</tspan>
				<tspan x="11.32" y="0" letter-spacing="0em">AI</tspan>
				<tspan x="19.94" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="24.92" y="0">A</tspan>
			</text>
		</g>

		<g id="CHARALÁ">
			<g id="Layer_2-7">
				<g id="svg2-7">
					<path id="path1199"
						d="m510.6,767.25l1.98-.52.29,1.51,6.29,1.57.93,1.98,6.52,4.08-.29,3.32,3.79,2.68,12.99.17,3.73-2.39,3.09-.23.41,1.34.52.35.93-2.5-.58-2.8-3.9-.87-3.55,1.11-5.18-4.43h-3.79l-2.68-1.92-1.81-2.62.7-3.03-1.57-1.4-.99-3.9.99-1.81-.29-3.09,1.69-2.5,1.98-.82-2.8-1.22-1.81-1.51,2.1-3.09v-2.33l-1.16-1.69v-4.48l-2.5-.41-2.68-2.1h-3.38l-1.81-2.5.12-3.2,1.81-5.71,2.21-3.73-.52-3.61,1.4-4.89-2.91-2.39,3.14-.17,2.91-2.62.12-2.1,1.69-3.09,2.68-3.73,1.16-1.98,1.11-4.83h2.21l1.51-2.1,1.63-1.28.64-1.86,2.21-2.5,1.57-1.4-.12-1.81-1.28-1.11-.52-3.49,3.96.52,3.67-.17,4.19-2.62-.99-2.62-.12-3.38,3.67-4.13,2.91-1.98.52-4.6,3.55-1.92,2.27-2.39-.12-1.81.87-1.75-.58-1.75.23-1.69,1.16-1.46.52-1.46,1.34-.99,1.16,1.11,2.21.99,1.11-.58,2.27.7,1.81-.12,1.28-.7v-2.21l-.82-.58v-1.98l-1.51-1.4.12-3.03-1.11-1.51-1.4.29-.87-1.22-1.16.29-2.27,2.5-2.91-.41-.7-1.22-1.46,1.46-1.75.99-1.28-.52-.99,1.86-1.69-.35-1.75.23-1.51-.41-1.63.76-1.11,2.15-1.86,4.43-1.51,2.8-.87,2.62-.12,2.8-2.21,3.09-2.21,1.22.12,3.09-2.39.58-3.38,2.68-1.4,3.09,1.28,3.32-.99.82-1.86-1.28-1.86-.52-2.91.12-2.21-1.81,1.11-2.21-.41-3.49,1.4-2.1v-3.38l-1.4-2.39-4.08-3.09-2.56-3.32-2.27,2.5-4.19.35-4.72,4.25-1.57-1.63-1.11,2.21h-1.81l-.29,2.33-2.39,2.5-3.26-.17-2.21,1.63-.52,1.69.52,1.4,4.89-.41,2.97,3.2.52,3.73,2.5,2.8-.29,2.5.82,1.63-1.16,1.22,1.69.99-2.5,4.02-.41,5.71-1.4,1.92,1.57,1.92-.52,3.09v5.12l-2.56,1.69-2.68,3.79-.17,4.31-1.98,3.32-3.49,1.28,2.27,4.31,1.69,1.63-1.98,2.1.87,2.1-1.16,1.4,2.27,2.8.17,3.61-1.28,3.9-1.51,3.09v7.8l-.47,4.78,2.1.23,2.68-.7.82,2.39,3.26,3.32,1.98,2.91.52,2.33,3.49.52-1.86,2.1.47,4.19h0Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(511.78 733.12) rotate(-77.32)" font-family="IBM Plex Sans" font-size="10"
				font-weight="500">
				<tspan x="0" y="0">C</tspan>
				<tspan x="7.14" y="0" letter-spacing="0em">H</tspan>
				<tspan x="15" y="0">ARALÁ</tspan>
			</text>
		</g>

		<g id="ENCINO">
			<g id="Layer_2-8">
				<g id="svg2-8">
					<path id="path1197"
						d="m553.73,778.35l1.22-1.22h1.22l1.22-1.22v-3.67l2.43-7.35,4.86-1.22,2.43-1.22h3.65l1.22,11.02,3.65,7.35,1.22,2.45,1.81-1.51.41-2.27,1.83-.89v-2.03l2.54-3.16.89.42,2.49-3.81-.83-3.46,3.02-1.55.06-1.67,3.43-3.64,2.54-.42,1.12-3.58-1.12-.48v-1.67l1.6-.72.89-2.21-.71-1.19,1.42-1.79-1.36-1.07-.06-1.19,3.61-1.07-.47-2.21,1.3-1.19,1.3.48,1.3-.95-.18-1.19,1.18-1.55,1.3.3,1.83-1.07-.83-1.19v-1.19l1.89-1.37-3.25-2.03-1.3-4.11-3.14,1.01-4.02-3.4-1.83-3.16-2.72-.18-1.01,1.97-1.89.42-2.31-1.85h-3.25l-1.89,1.13-3.55-.83-2.6-1.13-1.01-1.73-1.72-.72-.59-1.55-2.96,1.01-1.78,2.15-3.73-1.61-.83-2.03-5.33-.54-4.26-1.01v-2.86l-1.83-2.5-3.02-1.13-1.95-2.15-1.42-2.74-2.54-.89-2.96,2.38-.59-2.86-1.12-1.43-1.18-.3-2.43,1.67-1.18-.12-.12-2.68-1.72-3.99-2.72,3.81-1.72,3.16-.12,2.15-2.96,2.68-3.2.18,2.96,2.44-1.42,5.01.53,3.7-2.25,3.81-1.83,5.84-.12,3.28,1.83,2.56h3.43l2.72,2.15,2.54.42v4.59l1.18,1.73v2.38l-2.13,3.16,1.83,1.55,2.84,1.25-2.01.83-1.72,2.56.3,3.16-1.01,1.85,1.01,3.99,1.6,1.43-.71,3.1,1.83,2.68,2.72,1.97h3.85l5.27,4.53,3.61-1.13,3.96.89.38.04,1.22,1.22Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(546.17 745.79) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
				font-weight="500">
				<tspan x="0" y="0">ENCINO</tspan>
			</text>
		</g>

		<g id="SAN_BENITO">
			<path id="path1205"
				d="m393.37,751.35l1.42-1.71.63-1.59.34-2.56.91-1.48.34-2.67,3.47-3.24,1.02-2.56-1.08-1.31.57-1.19,4.38-2.05-.45-3.01,1.82-2.56-.45-1.93,1.65-1.59,1.59-1.08.57-1.02-.23-1.36,1.71-2.27.28-2.56-.91-.28-1.31-1.25-.33-.19h-1.2l-1.2,1.2h-2.4l-1.2,1.2-1.2,2.4h-1.2l-1.2,1.2h-1.2l-1.2,1.2-.95,1.97-1.39-.04.17,1.82-2.27,1.42-.74,1.25h0l-.45,2.9-2.22,1.54-1.36,1.25-2.78-.11-1.2,2.4h-1.2l-.05,1.23-.45.91-1.89.26.75,1.16-1.19,1.25-2.56.06.45,1.82,1.31,2.5.17,2.16.74,1.42-.06.97,3.64,1.71v1.08l1.48.17,1.36,1.25,1.88.51,2.84-.10-1.14,1.36.68Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(384.19 738.47) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.16"
				font-weight="500">
				<tspan x="0" y="0">SAN</tspan>
				<tspan x="-3.11" y="5">BENI</tspan>
				<tspan x="6" y="5" letter-spacing="-.01em">T</tspan>
				<tspan x="8.47" y="5">O</tspan>
			</text>
		</g>

		<g id="GUPSA">
			<g id="Layer_2-9">
				<g id="svg2-9">
					<path id="path1207"
						d="m374,770.46l1.06-2.36,2.11-2.24.81-.06,1.87-1.31,5.66-2.86.81-2.18,1.18-1.74,1.37-.37,2.24-2.49,1.62-2.67.68-.93-1.37-.68-.75,1.24-3.11.12-2.05-.56-1.49-1.37-1.62-.19v-1.18l-3.98-1.87.06-1.06-.81-1.55-.19-2.36-1.43-2.74-1.99,1.74-.5,2.3-1.18.62.87,1.8-2.86,3.23-.81,2.11.25,2.74.68,1.55-4.85,1.55,1.8,3.36,1.99,2.3-1.06,2.11-2.49.68-2.55,2.24-1.43.19-.75,1.06.56,2.24-1.93,1.55,1.49-.06,2.74-.5,2.18-1.18,3.73-.5,3.42.25Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(369.98 760.54)" font-family="IBM Plex Sans" font-size="4.55"
				font-weight="500">
				<tspan x="0" y="0">GUPSA</tspan>
			</text>
		</g>

		<g id="BARBOSA">
			<g id="Layer_2-10">
				<g id="svg2-10">
					<path id="path1209"
						d="m357.45,799.42l.59-1.12.82.12,1.65-1.35,2.35-2.88,1.82-.88,1,.06,2.41-1.65,1.77-2.47,3-5.59.53-3.82-.24-2.06.76-1.12-.06-3.29,1.12-1.24-.41-1.59-3.41-.29h0l-3.53.47-2.06,1.12-2.59.47-1.41.06-1.12,1.65-2.12.71-2.12,2.35-1.12.53-1.29,2.06-2.77,7.12-1.29,1.88-1.18,3.29-1.12,1.41-.06,2.06,2.06,1.29,1.41-.18,2.47,1.35,1.06,1.47,1.94,1.88,1.35-.12-.24-1.71Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(356.4 793.43) rotate(-50.41)" font-family="IBM Plex Sans" font-size="4.77"
				font-weight="500">
				<tspan x="0" y="0">BARBOSA</tspan>
			</text>
		</g>

		<g id="PUENTE_NACIONAL">
			<g id="Layer_2-11">
				<g id="svg2-11">
					<path id="path1211"
						d="m313.32,848.63l8.12-1.13,4.28,1.56,1.79-1.56.46-1.62,1.79-.75.58.52v1.33l2.26.98,4.4,5.5,2.31,1.74,6.07,2.49.33,1.75,1.93,5.14-.4,1.16,3.24,3.07,2.02.06,2.31-1.85.98-4.86,2.6-2.43.75-5.5-.35-1.56,1.39-2.31.4-3.64-1.56-.98-.52-3.07,1.16-1.91,1.27-4.69.98-6.02,3.76-6.07,1.85-5.15.98-.4-.69-5.44-1.85-3.01-3.64-.52-4.51-3.24-.17-3.64-1.33-1.27-.17-.46-1.45.12-1.91-1.85-1.04-1.45-2.43-1.33-1.39.17-2.02-1.27-2.72,2.02-1.56-.17-5.15,3.76-2.89,4.16-3.18-.12-2.49-5.26-1.68.29-1.56,2.31-1.97-.58-3.88,4.57-2.89.4,3.88,5.38-2.26.87.12,2.2,2.95-1.27.87,1.21-.12,2.31-1.79,1.62.29,2.6,1.27,1.79.17,2.43-2.08,1.62-2.26,3.18-.98,3.59-.23,2.49-.98,1.1.23,1.04-1.1,1.04.4,1.91,1.62,1.97-2.62,4.95Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(325.63 826.51)" font-family="IBM Plex Sans" font-size="8.09"
				font-weight="500">
				<tspan x="0" y="0">PUENTE</tspan>
				<tspan x="-4.77" y="9.71">NACIONAL</tspan>
			</text>
		</g>

		<g id="ALBANIA">
			<path id="path1213"
				d="m246.14,860.63h2.4l3.6-1.2h2.4l3.16,1.43,8.38-.45,3.34-.74,2.49.74.85,2.77v4.81l1.87,1.58,3.06-1.7,1.87-2.32,7.85-4.65,5.43.91.1,3.62v2.4h4.8l1.2-1.2.93-1.92,6-8.88,2.67-1.19,1.2-3.6,2.4-2.4,1.65-1.02,1.64-3.85-1.58-1.92-.4-1.87,1.08-1.02-.23-1.02.96-1.08.23-2.43.96-3.51,2.21-3.11,2.04-1.58-.17-2.38-1.24-1.75-.28-2.55,1.75-1.58.11-2.26-.85-1.19-2.89,1.24h0l-3.17,2.04-2.32-.96v3.11l-1.75,1.36h-2.89l-4.36,3.11-2.49.17-1.87,2.89v2.77l-.96,2.94-2.94.4-2.38,3.28-2.66,2.21-1.3,1.92-.28,2.83-2.21,1.24-3.06-.34-1.47-1.53-1.58-1.87-2.09-.06,1.02,3.17-.17,1.58.34,2.04-2.72-1.7-1.13-1.36-2.6,1.02-4.64-.28-1.24-1.02-1.19-2.66-2.15.68-2.09-.28-2.62,1.37-.1,1.29-.85,2.09-3.81,1.16-.35,3.91-4.05,2.23-5.21,3.23.96,2.04,1.4.85Z"
				fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path><text transform="translate(271.14 856.5)"
				font-family="IBM Plex Sans" font-size="8.09" font-weight="500">
				<tspan x="0" y="0">ALBANIA</tspan>
			</text>
		</g>

		<g id="MOGOTES">
			<g id="Layer_2-12">
				<g id="svg2-12">
					<path id="path1121"
						d="m623.11,548.86l-3.67.63-5.33-1.26.83-3.27-1.6-1.66-1.54,1.26-1.42-1.09-.3-1.38h-2.43l.12,3.73.71,2.06-3.26,4.76.18,1.95-2.43.17-1.42,2.75-1.6.8h-2.13l-1.6-1.26.12-2.64-1.6-.97-1.83,2.06-4.32.52-2.01.69-2.6,3.04.89,2.87-.41,1.2.83,1.49-.18,2.98-4.85,4.64-.3,2.64.83,1.26-.41,1.09.71,1.26-2.72,2.46-2.55.57-1.83-.69-.41-1.26-1.54.57-1.42-.97-1.18,1.49-1.18-.11-.12,1.95-2.43.57-3.02,2.75-.18,1.89-2.31,2.35-3.14,1.26-1.72,1.66-.59,2.29-1.42,1.66.12,1.61-1.83.57,1.18,1.49.3,3.15,1.12,1.61.18,4.24-.83,3.04.3,4.82-1.18,1.26-.89,3.04-.3,2.29-1.54,1.49-.53,1.61-.36,1.38.59,2.46,1.12,1.03.18,2.41h2.01l1.66-.75,1.54.4,1.78-.23,1.72.34,1.01-1.83,1.3.52,1.78-.97,1.48-1.43.71,1.2,2.96.4,2.31-2.46,1.18-.29.89,1.2,1.42-.29,1.12,1.49-.12,2.98,1.54,1.38v1.95l.83.57v2.18l2.96,1.38,4.73-.17,1.6-1.38h1.42l1.54-1.61,1.54-.29,1.54-1.09,2.96-1.26,3.43.4,2.55-1.61,2.31-2.18,3.55,1.61,1.54-.11-.18-2.35.83-1.09-.59-1.66-.3-3.04.53-3.15,1.3-1.95,6.69-6.13.41-1.95,2.72-2.58,2.25-2.46-.65-2.52,1.12-3.55v-2.35l-1.6-.52-1.42-.57-.18-1.66,1.18-1.49.12-2.06-2.55-.4-2.25-2.58,3.73-2.98,1.89-.57,2.01.69,1.12-3.04.3-3.55,1.6-1.38.18-2.18,2.55-2.64-.18-2.64,2.6-2.06,2.31.4,2.55-2.35-1.66-3.32-.53-3.5-2.19-2.18-2.78-.69-4.38-3.55-.83-3.32-2.37-3.38h0Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(568.28 599.1) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.08"
				font-weight="500">
				<tspan x="0" y="0">MOG</tspan>
				<tspan x="20.4" y="0" letter-spacing="-.04em">O</tspan>
				<tspan x="26.3" y="0">TES</tspan>
			</text>
		</g>

		<g id="ENCISO">
			<g id="Layer_2-13">
				<g id="svg2-13">
					<path id="path1181"
						d="m708.68,522.46l.68.85-1.52,1.24-.45,2.03-.28,3.49-1.69,2.76s-3.1,2.42-3.27,2.76-2.03,3.72-2.03,3.72l-2.48,3.27-1.24,3.1-.51,2.76-.28,3.49-5.13,3.04-2.31,2.87-2.2.17-1.86.96-2.37,1.35-3.1,2.14-.34,1.58-2.37.85.11-1.75.79-2.48,2.48-3.55.34-1.97,1.52-2.2.62-3.89-1.07-2.76.45-1.41-.51-1.24v-2.59l.73-4.28-.45-4.11-.28-2.31.62-2.93-.73-2.48.62-1.97-.73-1.52.56-4.23,3.94.68-.68,1.63,3.27,2.99,2.42.28.11,2.25,1.92.96.85-.79,1.63-.17h3.94l2.7-1.58h1.63l2.2.79,3.72-1.8Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(684.54 538.87) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.8"
				font-weight="500">
				<tspan x="0" y="0">ENCISO</tspan>
			</text>
		</g>

		<g id="GALÁN">
			<g id="Layer_2-14">
				<g id="svg2-14">
					<path id="path1157"
						d="m481.17,570.68l.71-2.84.12-2.96,2.25-3.55.18-2.54-1.12-1.3.3-1.66,1.54-1.66.83-2.01-.18-2.13.89-1.66,1.3-2.25-.59-2.84.89-2.01-.71-1.42-1.72-2.01.3-4.08,3.61-.71.53-1.89-1.42-1.48-.95-3.31,1.42-1.24-4.44.18-1.42-1.36-3.73-1.66-.53-2.6-3.43-2.42-1.83-3.13.18-2.66-.89-2.54-3.96-2.84h-2.54l-3.02-.89-3.31-2.37-.41-2.84,1.6-1.66-.12-1.3-2.54-1.95-2.6-1.24-3.43-.71-.83,3.08-1.83,2.42-1.18,5.68,1.3,2.66-.83,3.08,1.54,2.66-.3,2.54-2.9,1.48-3.02,4.26.3,6.09.89,5.62-.18,3.19-1.95,3.96-4.85,6.57,2.78,1.48,2.96.41,2.54,1.6,1.95,2.07,3.67.35,1.66,1.36,1.36.06,3.02,1.06,3.08,2.01,2.31.06,2.37,1.18,2.6-.06,2.01,2.42,2.13.12,1.72,1.77.77,2.42-.41,2.07-1.3,2.07-.47,1.89,3.37.18,1.77.71,2.25-1.01Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(449.52 538.28) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
				font-weight="500">
				<tspan x="0" y="0">GALÁN</tspan>
			</text>
		</g>

		<g id="CABRERA">
			<g id="Layer_2-15">
				<g id="svg2-15">
					<path id="path1159"
						d="m483.41,589.62l2.22-.17,1.54.51,2.05-.97,2.5.17,1.59-1.71,3.3-1.25,1.37-1.93,1.76-.28,1.82-1.65,2.45-.28.57-1.88,1.71-1.76,1.39-.92-1.2-1.2h0l-.99-1.4-1.65-1.99-2.11-.28-1.54.74-.51-3.07-1.48-2.73-1.08-2.62-2.62-2.96-.28-3.53.8-3.13,1.93-1.08-1.54-2.33-2.16-.11-1.37-.4-.97-1.08-1.93,1.25-3.02-2.73-.85,1.59.17,2.05-.8,1.93-1.48,1.59-.28,1.59,1.08,1.25-.17,2.45-2.16,3.41-.11,2.84-.68,2.73.11,5.29,2.16,3.93.51,3.13-.4,3.41.34,3.58Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(484.17 577.71) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.09"
				font-weight="500">
				<tspan x="0" y="0">CABRERA</tspan>
			</text>
		</g>

		<g id="PINCHOTE">
			<path id="path1161"
				d="m527.9,595.72l-.22,2.61-2.78,3.89-.42,2.86-2.4,1.2-4.8-1.2v-2.4l-1.2-1.2-1.47-2.49-2.34-.61-1.61-1.98.06-2.73-.64-.59-2.58-1.9-2.22-1.69-1.2-1.2-1.2-2.4-1.2-1.2v-1.2l2.4-1.2,1.2-2.4,2.4-2.4,2.42,1.87,1.06.61,2.28-.11,1.22-1.45h1.22l1.22-1.22,2,.28,2-.83,1.22-2.23h1.61l2-.95.11,2.95,2.17,1.22.78,2.23,1.61,1.61-.5,1.45.95,1.56-1.78,1.22.67,2-1.22,2.28-.17,3.23-.67,2.5Z"
				fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(506.25 588.58) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.4"
				font-weight="500">
				<tspan x="0" y="0">PINCH</tspan>
				<tspan x="13.19" y="0" letter-spacing="-.04em">O</tspan>
				<tspan x="16.05" y="0">TE</tspan>
			</text>
		</g>

		<g id="OCAMONTE">
			<path id="path1163"
				d="m521.95,630.3l-.62,2.61-.45,2.17v6l.27,1.2-.43,2.61,2.73,3.54,4.35,3.29,1.49,2.55v3.6l-1.49,2.23.43,3.72-1.18,2.36,2.36,1.92,3.1-.12,1.99.56,1.99,1.37,1.06-.87.14-3.96-.02-2.87,3.6-2.86,2.55-.62-.12-3.29,2.36-1.3,2.36-3.29.12-2.98.93-2.79,1.61-2.98,1.99-4.72,1.18-2.3-.96-1.2-1.34-1.41-1.18-1.12-1.08.13-1.9-1.18-1.55-.19-1.55-1.18-3.72,2.98-3.6,1.61-2.55-1.92-1.06-2.48-3.91-3.54-1.76,1.1-2.28,1.26-2.52-.06-1.33,2.42Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(523.94 642.11) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
				font-weight="500">
				<tspan x="0" y="0">OCAMONTE</tspan>
			</text>
		</g>

		<g id="SOCORRO">
			<g id="Layer_2-16">
				<g id="svg2-16">
					<path id="path1165"
						d="m518.27,606.44l-1.3,2.72-2.19.06-1.71,1.18-1.42,3.19.89,2.66-2.78,2.13-1.42,1.89.18,2.6-1.36,3.31.41,1.42-1.89-.24.12-1.54.59-1.42-3.72-.41-1.59-.77-1,1.95-1.89-.89h-3.13l-4.02-2.42-2.72-.89-2.72.59-2.3-1.54-5.14-.53-3.54,1.3-5.73,3.01-.65-2.6,2.24-3.07,1.06-3.07.12-3.31,1.59-2.72.35-1.71-.53-1.83,1.36-2.78,1.83-2.84,1.18-1.48,3.37-3.13,1.3-2.66,1.89-2.72,2.3-.18,1.59.53,2.13-1,2.6.18,1.65-1.77,3.43-1.3,1.42-2.01,1.83-.3.12,1.42,2.78,1.48,1.71,1.65,1.12,1.89,2.19,1.06,1.06,1.54-.06,2.89,1.59,2.78,2.48.65,1.42.77,2.36,2.3s.53,3.96.53,3.96Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(479.45 609.59) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.82"
				font-weight="500">
				<tspan x="0" y="0">SOCORRO</tspan>
			</text>
		</g>

		<g id="CURITI">
			<g id="Layer_2-17">
				<g id="svg2-17">
					<path id="path1129"
						d="m601.75,527.79l-3.15,1.92-2.97,3.09-1.11,2.22-3.56,2.92-1.71,3.57-3.6,2.4-3.15.04-1.87-.41-1.4-2.33-2.39,1.22-1.69-.7-4.49,1.11-1.87-.99-2.27,1.22-3.91-3.32-.52-3.32-1.87-1.52-2.68.52-2.51-1.11-.82-1.69-1.98.17-.87-1.11.29-2.51-1.17-1.69-2.22.17-1.11-.87-2.22-.29-1.29.6h-4.8l-.68,1.91.12,1.63-.52,3.21,1.52,1.28-1.17,1.81,2.22,4.43,1.69,1.52.87,2.33,1.52,1.52.82,2.8-1.69,3.91-.12,1.81-1.11.87.87,2.22-1.28,1.4.41,1.52-1.52,2.39.58,4.2,1.17,4.43,1.4.58,1.87-1.52,3.5-1.69,1.81.29.58,2.39,2.68,3.03,3.91.17,2.68,3.09h1.81l3.27,2.1,2.1-.12,1.57,1.28,1.17.12,1.17-1.52,1.4.99,1.52-.58.41,1.28,1.81.7,2.51-.58,2.68-2.51-.7-1.28.41-1.11-.82-1.28.29-2.68,4.78-4.72.17-3.03-.82-1.52.41-1.22-.87-2.92,2.57-3.09,1.98-.7,4.26-.52,1.81-2.1,1.57.99-.12,2.68,1.57,1.28h2.1l1.57-.82,1.4-2.8,2.39-.17-.17-1.98,3.21-4.84-.7-2.1-.12-3.79h2.39l.29,1.4,1.4,1.11,1.52-1.28,1.57,1.69-.82,3.32,5.25,1.28,3.62-.64-1.22-4.61-1.92-4.26-3.38-2.92-1.69-3.5-4.72-.87-4.49-1.75-1.92-2.27-2.68-.99Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(555.99 560.6) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.75"
				font-weight="500">
				<tspan x="0" y="0">CURITÍ</tspan>
			</text>
		</g>

		<g id="JORDAN">
			<path id="path1131"
				d="m535.06,514.33l-2.29-.67-4.3-.11.11,1.51-1.12,3.4,1.12,3.52,1.06.73,1.73,2.46,4.74,2.01,1.55,1.14.13-.36,2.29-.28,1.79.39,1.84-1.06.56-1.45.5-1.73,2.85-2.23,2.12.5,5.13-1.9,2.4,1.34,3.24-1.17,5.96,3.15v-3.6l-3.84-3.62-3.4-1.34-4.69-4.46-1.28,1.79-8.37.84-2.62,2.01-2.51-1.62-4.69.84Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(529.31 520.52) rotate(1.24)" font-family="IBM Plex Sans" font-size="5.37"
				font-weight="500">
				<tspan x="0" y="0">JORDÁN</tspan>
			</text>
		</g>

		<g id="BARICHARA">
			<path id="path1137"
				d="m525.03,558.42l-2.4,1.81-1.99-.41-2.8.29-2.8,1.4.58,2.4-4.09,4.21-2.1.99-3.45,3.56-1.34.47-2.16-.29-1.58.76-.53-3.15-1.52-2.8-1.11-2.69-2.69-3.04-.29-3.62.82-3.21,1.99-1.11-1.58-2.4-2.22-.12-1.4-.41-.99-1.11-1.99,1.29-3.1-2.8,1.29-2.22-.58-2.8.88-1.99-.7-1.4-1.69-1.99.29-4.03,3.56-.7.53-1.87-1.4-1.46-.93-3.27,1.4-1.23.82-2.51.12-2.57,2.57-2.1,2.51-.41,2.1-1.23,1.03-1.55,1.2-1.2,2.4-1.2.57-.02h.99l1.29,1.29,1.87,1.11,3.39.88,1.52,1.81-1.52,1.4-.58,2.1.12,2.22,2.92,1.52,1.4,3.21-1.23,1.46.06,1.75-.88.99-1.4,3.39,1.4.41,1.4,1.81-.99.88-.29,2.86,1.64.58,1.75,2.04,1.64.23,1.23,1.75h1.69l1.17,2.16,2.57,2.8,1.4.29,1.4,1.81-1.58,3.5.41,1.46Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(491.51 548.86) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.09"
				font-weight="500">
				<tspan x="0" y="0">BARIC</tspan>
				<tspan x="11.67" y="0" letter-spacing="0em">H</tspan>
				<tspan x="14.6" y="0">ARA</tspan>
			</text>
		</g>

		<g id="OIBA">
			<path id="path1143"
				d="m498.29,659.88h-2.96l-2.96-3.02-2.21-.7-1.86-1.98-2.5-.52-2.1-.58-.63.7-1.8,1.57-1.57,1.8-1.98,4.42-3.55-.17-.7,2.38-2.56,2.79-2.27-1.8-2.67-.41.58,2.21-.99,1.98,1.8,2.21-2.5.7-2.27-.12-1.28,2.09-1.8.7-.87-1.4-2.21-.17-2.09-.7-.17,3.02-2.27,3.02.59,3.97,3.6,2.4,2.4,2.4v3.6l-1.2,2.4-1.2,2.4,1.2,3.6,1.12,3.36-1.8.12-2.5,4.01-1.57,4.19-2.67,5.29,2.27,1.4,2.56-.58,2.56.41,1.8-2.09,1.98-.17,1.1-1.8,1.57-1.22-.52-1.51,2.5-1.8,1.98,1.69.12,1.28.81,1.4v1.92l-.99.99.7,2.33,1.69.99,1.98.12,3.49,1.98,2.27-.17,2.67-1.69.29-2.33,2.21,1.51,1.69-.99,1.4,2.21,1.8,1.8.41,1.51,2.38-.58,1.1.58,2.91.41,3.78,1.28,1.16-1.4-.87-2.09,1.98-2.09-1.69-1.63-2.27-4.3,3.49-1.28,1.98-3.31.17-4.3,2.67-3.78,2.56-1.69v-5.12l.52-3.08-1.57-1.92,1.4-1.92.41-5.7,2.5-4.01-1.69-.99,1.16-1.22-.81-1.63.29-2.5-2.5-2.79-.52-3.72-2.96-3.2-4.88.41-.58-1.34Z"
				fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(471.39 692.17) rotate(1.24)" font-family="IBM Plex Sans" font-size="10"
				font-weight="500">
				<tspan x="0" y="0">OIBA</tspan>
			</text>
		</g>

		<g id="GUADALUPE">
			<path id="path1145"
				d="m453.69,673.47l-1.2-2.4-1.34-.5-1.17-1.62-3.41-1.17-1.84-1.34,1.79-2.68-.22-2.91-.95-.11-.89-.84-1.06.84h-1.29l-1.51-1.17h-1.4l-1.12-.95-1.17.06-.34.89.11,1.4-1.01.95-2.68,1.56-.34,1.73-1.4.11-2.46,2.79-1.51,2.01-.5,1.73-1.23-.06-.67,1.79.06,1.23-1.06,2.63v3.35l-1.56,2.29.89,1.17-.84,2.46-1.56.84h-.78l-.34.45-1.51,3.13.28.78-1.34,1.17-.39,1.17-1.12,1.01-.28,1.06-2.57,2.91-.39,1.4-.89.28-1.62,1.51.17,1.9-1.29,2.46.39,1.29.45,2.29.84,1.51,1.29,1.23.89.28,1.96.28,2.18-.39,1.62-1.23.17-2.79,3.63-2.29,5.25.5,4.02-.95,3.41.39.84,2.52,2.68,1.23,1.45-.95.34-3.13,1.34-2.68-.67-4.81,2.12-1.23,1.62,2.96,3.74,3.58,2.85.17,1.45,1.9,2.4-3.86,1.73-.11,1.9-4.02,1.73-1.62,1.51-3.63-1.34-.67-.5-3.07-1.73-2.12-3.13-2.57-2.69-2.9-1.2-2.4,1.37-3.09,1.03-2.91Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(420.11 694.32) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.85"
				font-weight="500">
				<tspan x="0" y="0">G</tspan>
				<tspan x="4.66" y="0" letter-spacing="-.01em">U</tspan>
				<tspan x="9.05" y="0">ADA</tspan>
				<tspan x="22.64" y="0" letter-spacing="-.01em">L</tspan>
				<tspan x="26.26" y="0">UPE</tspan>
			</text>
		</g>

		<g id="GUAPOTA">
			<path id="path1147"
				d="m481.16,651.87h-2.94l-1.47-1.69-1.69-.17-1.92-1.58-1.13-1.64-1.13-3.22-2.03-1.69-1.69,1.3-4.23-3.22-2.2,1.64-1.07,2.15-1.81,1.64-1.41.28-2.32,2.77-.11,1.81-1.81,2.82-.34,1.64.4,2.77-1.81,2.48-1.92-.17-1.69-.51.23,2.94-1.77,4.06,2.4,1.2,2.4,1.2,1.2,1.2.68,1.09,1.58.96,2.03.68,2.15.17.85,1.36,1.75-.68,1.24-2.03,2.2.11,2.43-.68-1.75-2.15.96-1.92-.56-2.15,2.6.4,2.2,1.75,2.48-2.71.68-2.32,3.44.17,1.92-4.29,1.52-1.75,1.75-1.52,1.04-1.28-1.32-1.2Z"
				fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(452.85 658.95) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
				font-weight="500">
				<tspan x="0" y="0">G</tspan>
				<tspan x="3.13" y="0" letter-spacing="-.01em">U</tspan>
				<tspan x="6.08" y="0" letter-spacing="0em">AP</tspan>
				<tspan x="12.08" y="0" letter-spacing="-.04em">O</tspan>
				<tspan x="15.07" y="0" letter-spacing="0em">TÁ</tspan>
			</text>
		</g>

		<g id="CONTRATACIÓN">
			<g id="Layer_2-18">
				<g id="svg2-18">
					<path id="path1149"
						d="m384.88,664.95l.71,1.59,1.7.88.65.59.24.76,1.29,1,.47.06.29.35.59.06.59.47.94.41.35.59,2.12.18.12.71h.71l.59.35.71.88.47-.35.24.59.47.59,1.7,1.06,1.88.29,1.53-1.41,1.28.08h3.6l.83-.84,2,.41,1.23.53,1.82.35.06,1.23.71,1.06.35.82.06,1-.88,2.47-.18,1-.71,1.53.18,1.12.59.94,1.18.59h1.35l1.06-.29.94-.12.94.29.53.71.06,1,1.65-.88.88-2.59-.94-1.23,1.65-2.41v-3.53l1.12-2.76-.06-1.29.71-1.88,1.29.06.53-1.82,1.59-2.12,2.59-2.94,1.47-.12.35-1.82,2.82-1.65,1.12-.94-1.65-.88-2.76-.76-2,2.12h-2.94l-1.94,2.35-1.82.94-4.82-3.35-2.06-2.12-.29-2.23.65-2.35-.65-1.35.88-2.18-1.82-1.76-1.82-.35-.88-1.76.06-2.47-1.29-2.94v-1.41l1.06-1v-2.35l-3.17.76-2.41,1.94-4.29-1.59-2.18.59-2,1.06-1.47,1.7-.06,3-1.18,2.59-1.23,2-.29,2.53-1.23,1.94-1.06.53-.06,1.82-2.59,2.35-.53,1.7-2.12,1.82-1.94.88-1.29-.88-1.76-.06-1.53-1.18.06-1.7-2-.35-1.18-1.59-1,.12-.71-.88-2.41.82-.82.82-1.76-.06-1.35.41-.29,1.82.12,1.7,1.12,2.06,3.06,2,.12,1.76,2.47,1.06h1.65l.76.94,1.65.12,1.23-1.7,1.06-.59.24-1.41,2.41-.59Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(388.85 668.66) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.92"
				font-weight="500">
				<tspan x="0" y="0">CONTR</tspan>
				<tspan x="16.15" y="0" letter-spacing="-.06em">AT</tspan>
				<tspan x="21.84" y="0">ACIÓN</tspan>
			</text>
		</g>

		<g id="CHIMA">
			<g id="Layer_2-19">
				<g id="svg2-19">
					<path id="path1151"
						d="m462.62,640.36l.58-2.44.58-1.8-.7-1.8,1.39-2.09,1.69-1.1.12-1.92-1.28-.81-1.16-1.63-1.98,1.1-2.5-.41-2.96,1.63-.81,2.09-2.79,2.21h-2.09l-.58-2.21-1.1-.58-1.39,1.51-1.39-.29v-.87l1.1-.87-1.86-1.92-2.56-1.63-1.39.29-2.09,1.69-2.09-1.39-.81,1.69-1.8.12-2.5-1.22-1.28.87-1.51-.41.87-1.39-1.69-1.98-2.56,1.22-1.16-1.1.58-2.5-1.1-1.39-.17-2.91.52-2.62,1.28-1.28-.17-3.6-2.09-2.62-3.02-1.98-1.63,1.39-1.86.06-.41,2.21-1.92,4.36-.52,3.84-2.27,5-1.05,11.1,1.45,2.85v2.32l-1.05.99v1.39l1.28,2.91-.06,2.44.87,1.74,1.8.35,1.8,1.74-.87,2.15.64,1.34-.64,2.32.29,2.21,2.03,2.09,4.77,3.31,1.8-.93,1.92-2.32h2.91l1.98-2.09,2.73.76,1.63.87-.12-1.45.35-.93,1.22-.06,1.16.99h1.45l1.57,1.22h1.34l1.1-.87.93.87.99.12,1.74.52,1.98.17,1.86-2.56-.41-2.85.35-1.69,1.86-2.91.12-1.86,2.38-2.85,1.45-.29,1.86-1.69,1.1-2.21s1.98-1.74,1.98-1.74Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(424.19 643.17) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.5"
				font-weight="500">
				<tspan x="0" y="0">CHIMA</tspan>
			</text>
		</g>

		<g id="SIMACOTA">
			<g id="Layer_2-20">
				<g id="svg2-20">
					<path id="path1153"
						d="m421.03,605.5l3.05,1.99,2.11,2.64.18,3.63-1.29,1.29-.53,2.64.18,2.93,1.11,1.41-.59,2.52,1.17,1.11,2.58-1.23,1.7,1.99-.88,1.41,1.52.41,1.29-.88,2.52,1.23,1.82-.12.82-1.7,2.11,1.41,2.11-1.7,1.41-.29,2.58,1.64,1.87,1.93-1.11.88v.88l1.41.29,1.41-1.52,1.11.59.59,2.23h2.11l2.81-2.23.82-2.11,2.99-1.64,2.52.41,1.99-1.11,1.17,1.64,1.29.82h0l1.52-2.4.12-2.46-.64-2.58,2.23-3.05,1.05-3.05.12-3.28,1.58-2.69.35-1.7-.53-1.82,1.35-2.75,1.82-2.81,1.17-1.46-1.52-.06-1.99-.7-1.58-1.64-2.34,1.05-2.11.41-2.75.88-2.4-2.11-1.82.18-2.52-2.4-1.17-.59-1.64-1.46v-.88l-2.87-1.82-1.41.47-2.28-1.93-.12-1.29-.82-1.05-2.28.29-2.05-1.64-1.46-.18-1.99-1.93-2.4-.12-2.58.76-2.46,2.17-.12,1.23-1.64,1.23.12,1.35-3.28,2.28-.59-4.04-1.46-2.75-2.4,1.35h-5.39l-3.28-1.05-3.57-3.05-1.35,2.11-.29,2.28-3.28.47-2.11,1.99-4.8-1.23h-3.75l-1.05,2.58-2.87-.29-2.11,1.35-3.16-.12.18-4.74-1.58-4.92-4.22.59-2.4-2.52-2.69-.18-2.28-1.64-3.28-.88-2.69.88-2.99-1.93-4.22,3.05-4.22-2.34-3.22,1.11-2.99-1.11-1.99.82-1.52-1.11-2.52.82-3.4-4.16,2.28-4.45-.41-2.23,1.82-.82-1.41-1.41-5.62-.59-.29-2.93-2.11-2.4-.88,2.34-4.39.18-1.82-1.23.88-1.99-5.68-3.34-.41-7.44,3.69-1.29.53-1.99-1.99-.41-.59-1.52,2.11-1.11-3.98-1.23-.59-4.04,1-2.23-1.87-2.64.88-1.99-2.4-2.34,1.87-2.11-1.58-3.22.41-3.05,1.17-.53,1.58,1,1.41-1.99.18-3.34-1.87-1.64.53-1.64-2.52-1.99,1.58-1.29-1.11-1.29,1.58-.59,1.11-2.23-1-2.93.59-1.99-2.23-1.29-.12-2.11-1.7-1.29-2.69-.7.88-2.23-1-2.93-1.99-.53-1.82.88-1.58-3.51,2.58-2.11-1.11-2.4,1.17-1.99-.41-3.81,2.93-1.64.12-2.93-3.57.12-2.11,1.29-3.92-4.33-1.11-2.81-4.28.41-2.23-2.64-.82-.25,2.4-2.4-4.27-1.39-3.1,2.23-1.99-1.11-1.11,1.23-1.58-.82-.59,1.23-2.81-.12,1.06-2.45,1.2-2.4-1.2-3.6-1-.87-2.23-3.4-.37-1.73v-2.4l-.51-2.31-1.99-2.4-1.87.18-1.29-1.82-1.41.59-2.99-2.52-.7-2.23-2.58.12-.29,2.34-1.29.41,1.11,2.23-.29,1.93h-3.28l-2.52,1.23-.53,2.64-1.58,1.99-2.28.29-3.69,3.22-.53,1.7-2.52-1.29-.88,1.11.41,1.7-1.52,1.52-2.81-.18-2.81,3.1,3.4,2.4,3.4,2.11-1.87.82,2.23,1.11-2.52,2.69,2.23.88.41,3.4,1.58,2.93-3.28,2.4,1.52,2.69-1.87.29,1.7,2.4-2.58.18-1.29,3.1-1.58-.29.7,2.69-2.52,1.93,2.11,2.69.7,3.1,2.11-1.29.7,1.11-1.99,1.64,1,1.41,1.17-.82,1.99,3.1,1.52-.12,3.4,2.69,1.99.12.88,1.93,2.4,1.29.82,2.4,2.52.82v1.93l1.82,2.64,1.11-1.11,1.99.18,1.41-1.82.82,1.52-.41,2.4.59,2.23,1.41-2.34.88,2.69h2.28l.41,1-2.23.41-.18,1.64,3.4.88-1.82,1.52,3.57,1.23.18,1.93,2.11,1-.82,2.69,2.4,1.99-2.99,2.93,3.81,2.34,3.4,3.4-1.87,2.34,2.28,2.69-.59,2.4,2.23,1.41,1.99,2.52,3.51-.88.7-2.64,2.23.7,2.52-1.23,2.52,2.34-.41,3.81,2.28-1.99.7,2.69,1.17,1.52-2.28,3.22-5.1,2.34,2.28,3.4.29,3.34,2.11-.7.82,1.64,2.4.82h5.21l3.57,3.05-2.58,4.22h0l-1.58,3.51-.29,4.33,2.28-.12,2.99-1.29,2.99,2.64h2.28l-.29,2.64h4.63l2.99-.59.41,4.04,2.28-.82,1.41,1.64,3.28,1.29,1.52-.7,1.82,1.82-1.11,2.64,2.11,1.23-1,4.51,2.28-1.23.59-1.64,2.52-2.23,1.29,1.64,5.62.41,3.4-5.15-.29-2.34,4.22-4.33.53-2.11,3.92,1.82,2.81,2.34,2.28-3.22,4.22-.29,5.97,2.23,4.39,3.63-1.23,3.22.29,2.52,4.16,3.92,2.11-.88,3.57.35,3.57,1.35.7,1.82,2.69.41,2.11,1.7.7,1.76,3.05.82,3.34,2.34,3.81.06,4.57-2.23.41-2.23,1.87-.06,1.23-1.29h0Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(270.58 499.18)" font-family="IBM Plex Sans" font-size="10.52"
				font-weight="500">
				<tspan x="0" y="0">SIMACOTA</tspan>
			</text>
		</g>

		<g id="HATO">
			<g id="Layer_2-21">
				<g id="svg2-21">
					<path id="path1155"
						d="m472.47,596.33v-1.18l-1.07-2.31,1.95-.71.83-1.07,2.13-1.07.77-1.36-.24-2.55-1.07-4.32-.41-3.97-.59-1.89.06-3.26-.59-2.55.47-1.89,1.3-2.07.41-2.07-.77-2.43-1.72-1.78-2.13-.12-2.01-2.43-2.6.06-2.37-1.18-2.31-.06-3.08-2.01-3.02-1.07-1.36-.06-1.66-1.36-3.67-.36-1.95-2.07-2.55-1.6-2.96-.41-2.78-1.48-2.13,2.72v3.67l-1.66.89-2.6,3.37v3.97l-2.43,2.01-2.25,3.2.89,3.85-1.36,2.13.18,2.6-2.43,2.31-.59,3.08.89,2.6-.47,2.01,1.48,2.78.59,4.08,3.31-2.31-.12-1.36,1.66-1.24.12-1.24,2.49-2.19,2.6-.77,2.43.12,2.01,1.95,1.48.18,2.07,1.66,2.31-.3.83,1.07.12,1.3,2.31,1.95,1.42-.47,2.9,1.83v.89l1.66,1.48,1.18.59,2.55,2.43,1.83-.18,2.43,2.13,2.49-.95,2.43-.36,2.37-.89Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(440.44 575.54) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="0em">H</tspan>
				<tspan x="6.45" y="0" letter-spacing="-.06em">A</tspan>
				<tspan x="11.88" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="17.2" y="0">O</tspan>
			</text>
		</g>

		<g id="SAN_GIL">
			<path id="path1141"
				d="m539.69,600.04l-1.53-2.55-.79-2.84-2.33-.68-1.65.4-2.16,1.93-3.29-.17-1.07-1.84,1.81-4.01,1.25-2.33-.68-2.04,1.82-1.25-.96-1.59.51-1.48-1.65-1.65-.79-2.27-2.21-1.25-.11-3.01-2.04.96h-1.65l-1.25,2.27-2.04.85-2.04-.28-1.25,1.25h-1.25l-1.25,1.48-2.33.11-1.08-.62-.8-.75-1.2-1.2-1.2-1.2-.6-2.63,1.3-.45,3.35-3.46,2.04-.96,3.97-4.08-.57-2.33,2.72-1.36,2.72-.28,1.93.4,2.33-1.76,1.25-1.25.4-2.04,1.08-1.3,1.7-.34.96-1.7,1.36-1.02,1.64-.6,2.4-2.4,1.23-3.75-.11-2.44,2.04-.68,1.65,1.48.85,2.27,1.48,1.48.79,2.72-1.65,3.8-.11,1.76-1.08.85.85,2.16-1.25,1.36.4,1.48-1.48,2.33.57,4.08,1.13,4.31,1.36.57,1.82-1.48,3.4-1.65,1.76.28.57,2.33,2.61,2.95,3.8.17,2.61,3.01h1.76l3.18,2.04,2.04-.11,1.53,1.25-.11,1.93-2.33.57-2.89,2.72-.17,1.87-2.21,2.33-3.01,1.25-1.65,1.65-.57,2.27-1.36,1.65.11,1.59-1.76.57h-4.26l-3.4-.28-3.29,1.19-1.65-.51Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(533.46 584.55) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.75"
				font-weight="500">
				<tspan x="0" y="0">SAN GIL</tspan>
			</text>
		</g>

		<g id="ARATOCA">
			<path id="path1127"
				d="m555.49,510.52l4.84,4.61,3.51,1.38,3.34,3-.52,4.26-5.01-3.05-3.34,1.21-2.48-1.38-5.3,1.96-2.19-.52-2.94,2.3-.52,1.79,2.19.29,1.09.86,2.19-.17,1.15,1.67-.29,2.48.86,1.09,1.96-.17.81,1.67,2.48,1.09,2.65-.52,1.84,1.5.52,3.28,3.86,3.28,2.25-1.21,1.84.98,4.44-1.09,1.67.69,2.36-1.21,1.38,2.3,1.84.4,2.47.6,4.8-2.4,1.08-4.13,3.51-2.88,1.09-2.19,2.94-3.05,3.11-1.9v-4.84l-4.61-3.8-3.69-1.15-2.71-2.88-1.04-3.11,1.15-3.63-2.02-1.9-2.65-.35.12-3.05,1.79-2.65-3.17-2.71-.35-3.8.98-1.67-.52-5.99-3.63-2.19-3.51-3.23-1.32,1.5-.12,2.3-1.61,1.38-.86,2.71-2.82,5.18.06,3-3.63,2.71-2.07,3.8-2.48.58-2.94,3.97-2.13.63-1.73,2.36Z"
				fill="#f99506" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(563.27 531.71) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.75"
				font-weight="500">
				<tspan x="0" y="0">AR</tspan>
				<tspan x="8.7" y="0" letter-spacing="-.06em">A</tspan>
				<tspan x="12.8" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="16.8" y="0">OCA</tspan>
			</text>
		</g>

		<g id="GIRON">
			<g id="Layer_2-22">
				<g id="svg2-22">
					<path id="path1119"
						d="m519.17,459.42l-5.1-6.68-3.75-.59-1.76-1.52-5.39-6.5-2.4-3.51-1.52-3.81-3.4-3.81-4.63-1.64-3.28-1.99-5.8-6.15-1.82-5.86-3.1-4.04-2.69-2.81-2.93-1.23-2.81-.53-2.4-1.82h-2.52l-4.39-2.34-1.58-2.69v-1.99l1.41-1.82-1.82-2.81-2.58,1-5.68-.29-4.8-3.81-3.51-4.45.18-2.64-2.58-1.99-3.1-.7-4.51-3.63-3.81-1.93-1.11-2.64v-1.93l-1.52-1.52-2.28.7-4.28-2.93-4.1-1.41-3.51-.29-1.99-1.52-5.1,1-4.39,1.99-3.51-3.63-2.52-1-2.99.12,5.21-2.52.53-1.7,2.81-.7,1.29,1,4.1-1,3.98,2.23,2.81-2.52,2.28,1.64,2.23-1.99,1,2.81,2.69,1,1-3.34,1.87,1.41,2.69-.53-.7-2.69,1.11-2.23,2.11-.29,1.46,1.29-.12,1.23,1.52,1.99-.53,2.11,1.64,1.11-.88,1.82,3.05,1.23,2.34-3.92,1.58,2.4,1.41,1.29,1.82-1.23h2.87l2.81,1.41,1.35,1.23,2.4.06,2.23,1.41,3.69.59.94.82.18,2.87,1.99,2.34,3.46,1.99,2.87.53,3.1,2.11.18-2.4,1.93-1.23,2.69-.12,2.87,2.11-.59,2.34,2.11-.12,4.69,5.04,4.39,2.69v1.82l1.41-.06.18,3.57.88.59.12,2.28,1.52,1.93.64,2.23,3.57.41,2.58,1.23,1.58,2.34,2.4.29,2.46-1.29,2.23.53,1.82.18,2.23-1.23-.64-1.41.64-1-.59-1.64.82-1.41-1-2.34,1.35-1.23-.18-3.4,1.35-3.05,3.22-5.97,1.29-3.4v-4.04l-1.17-3.63,1.99-1.05,3.92-3.4,3.05-3.69-.88-2.75-.12-3.28,1.35-3.28-2.34-2.93-.94-6.03,2.69-1.58.88,2.93.76.41-.29,2.28.64.53-.41,2.17,1.05,3.05-.59,1.52.41,1.64,1.29,3.22h1.11l-.18,2.93-3.81,4.51.06,2.34-.82,2.34-1.41,2.81.18,1.99-1.35,2.17-.06,1.76-2.34,2.46,2.81,2.69,3.92,1.11.53,2.34,2.87.64,2.69,1.7,2.05-1.29,2.23.23-.7,1.87.29,1.64,1.05.64-1.82,1.87-2.11.41-.53,1,1.82,1.23.94,1.99-.88,1.05,1.29.59-.41,2.69.94.94,1.41-.76,1.05.76-1.64,1.41,1.58,1.82.88,1.11-.35.82.88,1.7.12,5.15,1.41,4.74-2.28,3.22.12,2.4,1.52,1.52,1.7-1.29.82.41-1.17,1.93.53.88v3.4l1.29.7.41,1.52,2.28.53,1.52,1.41-3.51,1.7-.41,3.22,1.17,2.4-2.58,2.52-1.99.41-6.21,3.81-1.7-.7-2.99,1.7-5.8,1.99-4.69,2.93h0Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(492.39 425)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">GIRÓN</tspan>
			</text>

		</g>
		<g id="FLORIDABLANCA">
			<g id="Layer_2-23">
				<g id="svg2-23">
					<path id="path1115"
						d="m565.29,369.29l-3.25,1.3-1.3,1.12.3,1.3-1.83,1.01-.12,3.08-2.6.3-1.72-1.66-2.54-.3-1.72,2.54-.12,2.43-1.18-.41-4.44,4.79-2.13.89-.41,1.12-2.96,3.79-.89,2.37-3.43,3.55-2.13.41-.53,1.01,1.83,1.24.95,2.01-.89,1.07,1.3.59-.41,2.72.95.95,1.42-.77,1.07.77-1.66,1.42,1.6,1.83.89,1.12-.36.83.89,1.72,2.31-2.01,3.14-.18-.18-2.37,1.18-.71.71-1.83,4.67,4.2,1.72-2.72-.89-2.37,3.02-.18,3.02-.89,1.83-1.66,1.89-.53.12-2.13,2.6-2.37,2.13-3.43,2.72-1.83.83-4.56.12-3.37,1.01-1.12,1.42.3,1.89-1.72,1.89.71h2.13l.12-1.54,1.01-1.42-1.89-.83-.89-2.96-4.02-2.84-1.12-2.43-.3-2.54-2.43-.83-2.13-1.42-.71,2.01-1.48,1.42Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(545.15 402.82) rotate(-49.28)" font-family="IBM Plex Sans" font-size="4.39"
				font-weight="500">
				<tspan x="0" y="0">F</tspan>
				<tspan x="2.41" y="0" letter-spacing="-.03em">L</tspan>
				<tspan x="4.65" y="0" letter-spacing="0em">ORIDAB</tspan>
				<tspan x="20.22" y="0" letter-spacing="0em">L</tspan>
				<tspan x="22.64" y="0">ANCA</tspan>
			</text>
		</g>

		<g id="EL_PEÑON">
			<g id="Layer_2-24">
				<g id="svg2-24">
					<path id="path1085"
						d="m254.12,716.91l.12,2.43,2.11,2.95.32.49,2.61-.71,2.73-1.43,2.14-1.43,2.14-2.38,2.61-.71,1.72,1.31,2.43.42,1.31,2.14.12,2.97,1.5.56,2.45,1.23,2.1,1.3,2.43.59,1.9,1.78,2.38.36,1.84-.89,2.85-4.37,2.91-.14,5.68,1.37,1.23,4.91v3.68l-.44,3.46,1.66,1.45,1.23,2.45,3.68,1.23,2.45,6.14-1.23,3.68-2.45,6.14-2.45,4.91-5.27-1.83-2.85-.12-4.04,1.96-3.27,1.9-6.35-.06-1.84-2.08-2.2.06-2.67-1.72-3.27-.83-4.1-3.33-.53-1.66-2.08-1.19-.06-2.08-4.28-4.39-3.09-1.84-3.03-2.85v-2.61l-1.07-2.2-.3-2.85-3.27-3.33-1.96,2.67-2.61,2.43,1.6,7.72-1.6,1.31-1.96-1.13-2.73-.3-1.72.95-1.13-.89-2.08,2.2-2.08.95-.3-1.37-1.54-2.61-2.85-.06-4.1,1.07-.89,1.66-2.49-.18-3.03-1.01-2.14-1.07-1.31-2.2.36-2.32,1.72-1.96,1.43-2.2.48-2.2-1.66-1.78-2.02-.65-2.08-3.56-.24-2.02,2.2-2.32-.12-1.48-2.32-1.13-1.19.24-2.67-1.9-3.15-.77-3.5.89-2.49-1.19.24-3.21,2.61-2.02,2.02,1.13,2.61-1.31.3-2.26,2.26-.12,1.6-1.54,2.55-.42,7.48-5.23,5.17.18,1.9-1.31,4.99.3,1.6-1.31,2.14,1.96,1.6-1.13,2.14,1.96-1.13,2.73,3.44,2.14,1.01,2.73v3.56l2.26.53,1.6-1.31,1.13,2.67,1.6.18,1.43-1.13,1.07,1.54Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(257.59 741.03)" font-family="IBM Plex Sans" font-size="7.44"
				font-weight="500">
				<tspan x="0" y="0">EL PEÑON</tspan>
			</text>
		</g>

		<g id="EL_GUACAMAYO">
			<g id="Layer_2-25">
				<g id="svg2-25">
					<path id="path1123"
						d="m387.27,683.78l2.17-.54.67-.18,1.2,1.2,1.38,2.77.96,1.87-.36,2.11,1.62,1.66.84.75,3.61,2.11,2.75.74,2.4,2.4,3.6,3.6.94,3h0l1.74-1.62.96-.3.42-1.5,2.77-3.13.3-1.14,1.2-1.08.42-1.26,1.44-1.26-.3-.84,1.62-3.37.36-.48h.84l-.06-1.02-.54-.72-.96-.3-.96.12-1.08.3h-1.38l-1.2-.6-.6-.96-.18-1.14.72-1.56.18-1.02.9-2.53-.06-1.02s-.3-.66-.36-.84c-.22-.37-.46-.74-.72-1.08l-.06-1.26-1.87-.36-1.26-.54-2.05-.42-2.05-.66-.72-.6-.9-.06-.66.84-1.5,1.26-1.56,1.44-1.93-.3-1.74-1.08-.48-.6-.24-.6-.48.36-.72-.9-.6-.36h-.72l-.12-.72-2.17-.18-.36-.6-.96-.42-.6-.48-.6-.06-.3-.36-.48-.06-1.32-1.02-.24-.78-.66-.6-1.74-.9-.72-1.62-.36-2.35-.54,1.14-1.56,1.8-.24,1.44-1.08.6-1.26,1.74-1.68-.12-.78-.96h-1.68l-2.53-1.08-.12-1.8-3.13-2.05-1.14-2.11h0l-.12-1.74.3-1.87-1.44-.9-2.53,2.11-1.93.48-.9-.84-4.15,2.35-1.93-.66v1.32l-.84.6.48,1.74-.84.06.12,2.59.12,2.59,1.08-1.2,2.35,1.08.9,1.68,1.02-.18,1.87,1.74,1.56.3,2.23,3.43,1.8.54,1.5,1.62,3.79,1.32,1.99,1.5,1.5-.06,2.83,2.11,2.29.78,1.32.9,1.44,2.17,1.5-.9,1.38-2.35Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(402.5 686.36) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.3"
				font-weight="500">
				<tspan x="0" y="0">EL </tspan>
				<tspan x="-7.76" y="3.96">G</tspan>
				<tspan x="-5.52" y="3.96" letter-spacing="-.01em">U</tspan>
				<tspan x="-3.4" y="3.96">ACAM</tspan>
				<tspan x="6.04" y="3.96" letter-spacing="-.07em">A</tspan>
				<tspan x="7.99" y="3.96" letter-spacing="-.01em">Y</tspan>
				<tspan x="9.96" y="3.96">O</tspan>
			</text>
		</g>

		<g id="SUCRE">
			<g id="Layer_2-26">
				<g id="svg2-26">
					<path id="path1083"
						d="m303.15,789.45l4.6.58,2.56.47,1.57,4.19.12,2.33,1.75.64.17,1.46-.41,2.5h-2.27l-1.98-1.69-3.08-1.05-1.63.93-.81,1.75-1.46,1.57-2.62,1.51-1.28,1.69-3.32.81,1.98,2.5,1.11,3.03-2.85.81-1.51,1.28-1.51,4.02-2.27,1.51-1.51,1.69-2.15,1.28-2.44.29-1.28-1.63-2.04-1.05v-1.75l-1.86-1.16-2.68.93-1.92-.87-.93-1.69-1.86-.58-1.98.64-1.75-1.51-2.68.58-2.5,2.15-1.75.41-4.19-1.28-1.8-3.32-4.95-2.79-.64-2.1,1.75-1.98v-1.8l2.1-.17-1.63-2.33.81-1.63,1.4-.41.58-1.05-1.46.06-2.04-2.1,2.04-1.22-.17-1.46-2.27-.52-2.68,1.11-1.86-.52-1.8.99h-2.27l-1.8,1.92-3.08-2.33-2.97-.7-.99-2.5,1.51-3.2.12-4.02-1.86-2.79-2.27-2.1-3.2-.12-2.1-1.63-3.49-.87-1.98-2.21-1.28.41.12,1.98-2.5,4.13v3.61l-5.35,5.41-2.21,4.48-2.68-1.51-2.5,1.22-2.68-.99-2.39,1.51-6.75-5.41-1.8-1.22,3.49-4.19-.41-2.79,2.21-1.92,1.51-2.5-1.4-2.33-.17-2.5-2.27-1.8-1.69-3.08-.7-3.03,1.11-1.69-1.98-.99v-2.62l-1.98.99-2.15-2.79,3.32-2.74,1.11-1.11,2.97.17,1.34-.47-.06-.58-1.46-1.51-1.11-.81.06-1.69-.64-1.11-.17-1.16,1.46-.76.29-1.28-.87-1.28-1.75-.99-1.28-1.05.29-3.03.58-1.4,1.16-1.28,1.57-.99.87-1.11-3.08-2.44-.76-1.05v-1.92l1.28-2.15,2.21-.7,3.32-.23.99-1.28,1.16-1.05-.58-.81-1.34-1.05-1.57-1.11-1.34-1.11-.17-.99,1.11-1.16.29-1.69-.06-1.4-.99-.58-.29-1.16,1.4-.23.93.29,1.75-1.22v3.08l.99,2.33,4.19-.17-.23,3.14,2.44,1.16,3.43-.87,3.08.76,2.62,1.86,1.16-.23,2.27,1.11.12,1.46-2.15,2.27.23,1.98,2.04,3.49,1.98.64,1.63,1.75-.47,2.15-1.4,2.15-1.69,1.92-.35,2.27,1.28,2.15,2.1,1.05,2.97.99,2.44.17.87-1.63,4.02-1.05,2.79.06,1.51,2.56.29,1.34,2.04-.93,2.04-2.15,1.11.87,1.69-.93,2.68.29,1.92,1.11,1.57-1.28-1.57-7.57,2.56-2.39,1.92-2.62,3.2,3.26.29,2.79,1.05,2.15v2.56l2.97,2.79,3.03,1.8,4.19,4.31.06,2.04,2.04,1.16.52,1.63,4.02,3.26,3.2.81,2.62,1.69,2.15-.06,1.8,2.04-.99,3.84.76,1.28-.58,2.33-2.79,2.21.99,1.63h1.28l2.97,3.32,3.2,4.02-.12,1.98,3.9-1.63,1.51,1.22,2.79.81,2.21,1.8,3.99-.02,1.83,2Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(217.11 767.84)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">SUCRE</tspan>
			</text>
		</g>

		<g id="VALLE_DE_SAN_JOSE">
			<g id="Layer_2-27">
				<g id="svg2-27">
					<path id="path1139"
						d="m552.07,631.48h-1.2l-5.58-1.36-1.41-1.07-3.39,2.71-3.27,1.47-2.32-1.75-.96-2.26-3.56-3.22-2.3.68-1.37,1.46-3.67-1.19-.68-2.43,2.15-4.46.11-4.86,2.43-5.25,1.81-1.24,1.64-2.6,4.07-2.15,1.13-1.64,2.88-2.03,1.75.51,3.27-1.19,3.39.28h4.23l1.13,1.47.28,3.11,1.07,1.58.17,4.18.6,2.04-.79,5.03-1.45,1.91-.85,2.99-.28,2.26-1.47,1.47-.51,1.58,2.94,3.96Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(528.8 615.77) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="-.04em">V</tspan>
				<tspan x="2.81" y="0">ALLE DE</tspan>
				<tspan x="-.58" y="5.52">SAN JOSE</tspan>
			</text>
		</g>

		<g id="VILLANUEVA">
			<g id="Layer_2-28">
				<g id="svg2-28">
					<path id="path1133"
						d="m540.37,541.55l-2.16.72.12,2.58-1.86,3.85-1.2,1.2-2.52,2.09-1.44,1.08-1.02,1.8-1.8.36-1.14,1.38-.42,2.16-2.45-.48v-1.2l1.2-1.2-.31-2.7-1.44-.3-2.64-2.88-1.2-2.22h-1.74l-1.26-1.8-1.68-.24-1.8-2.1-1.68-.6.3-2.94,1.02-.9-1.44-1.86-1.44-.42,1.2-3.54,1.14-1.02-.06-1.8,1.26-1.5-1.44-3.3-3-1.56-.12-2.28.6-2.16-2.26.14-1.2-2.4-.02-1.95-1.92-1.14-1.32-1.32h-1.02l-1.32-1.56,1.86-2.58v-3.3l1.44-1.74v-2.04l.72-1.86,1.62-.84,2.52.12,1.08,2.34h3.24l1.68,2.46,2.16-.18,1.98,6.66,5.1,1.62,3.66,1.14.12,1.62-1.2,3.66,1.2,3.78,1.14.78,1.86,2.64,2.76,1.2,2.34.9,1.5.38.3.46.12,1.68-.54,3.3,1.56,1.32-1.2,1.86s2.46,4.62,2.46,4.62Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(514.78 534.3) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.58"
				font-weight="500">
				<tspan x="0" y="0">VIL</tspan>
				<tspan x="5.26" y="0" letter-spacing="0em">L</tspan>
				<tspan x="7.23" y="0">ANUE</tspan>
				<tspan x="16.51" y="0" letter-spacing="-.04em">V</tspan>
				<tspan x="18.69" y="0">A</tspan>
			</text>
		</g>

		<g id="FLORIAN">
			<path id="path1215"
				d="m241.41,861.5l-2.18.92-1.61,2.35-2.28.65-2.4-1.2-2.4-3.6-3.6-3.6-2.4-3.6-3.6-8.4-1.2-6v-3.6l-4.52-1.62-1.49-1.78-2.38-.2-4.22-.09-.46,1.32-5.51-2.41.11-2.64,2.99-1.38,1.09-.98.8-1.95.8,1.15,1.38.23,2.87-.75,2.47-1.15,1.49,1.95,3.39.23,1.67,2.64,2.24-.4.98,1.03,3.22-.06,1.03-1.15.98.69,1.61-1.03-.11-1.03,1.21.29,2.13-1.44,1.32.29-.4-.86,1.49-.52,1.38.69,1.38-1.38,2.18-.34.8.75,3.1-.34.75,1.44,2.24.69,1.26,1.84,1.26-.86h.98l1.9,2.35,1.03.23.86-.92,1.95-.57,2.47.17,1.03-.69,1.15-1.55,1.49.98,1.26.17.34.63,1.95-.52.23-5.05.98-.06,1.32-.98,1.09,1.26.29,1.9-.75,1.03.98,1.49-.29,3.68.92,1.72.11,1.67.8,1.15,3.16-2.07,1.44-.11.86,2.01-.06,1.72,1.84-.57,1.26,1.32,2.7.52.96,3.03,2.4,2.4-.89,1.52-2.24,1.26-3.1-.34-1.54-2.99-2.13-.06.29,3.01,1.2,1.2v2.4l-4.61-1.89-1.15-1.38-2.64,1.03-4.71-.29-1.26-1.03-1.21-2.7-2.18.69-2.13-.29-.51,1.05-1.2,2.4-1.91,1.37-1.95,2.13-1.09,3.27-5.28,2.01-2.96,3.22-1.35,2.13-.98,1.15h0Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text transform="translate(229.67 839.7)"
				font-family="IBM Plex Sans" font-size="8.09" font-weight="500">
				<tspan x="0" y="0">F</tspan>
				<tspan x="4.44" y="0" letter-spacing="-.03em">L</tspan>
				<tspan x="8.57" y="0">ORIÁN</tspan>
			</text>
		</g>

		<g id="LA_BELLEZA">
			<g id="Layer_2-29">
				<g id="svg2-29">
					<path id="path1217"
						d="m200.99,829.17l-2.98-1.25-1.93-4.84-1.28-6.84h-1.23l-1.23-3.6-6.16-1.2h-6.16l-8.63-6-2.47-1.2-1.23-2.4-1.13-1.07-2-1.33,1.9-3.6.57-1.44-1.26.24,1.2-2.4.12-.92.61-3.88.97-1.47-.97-2.13-.08-2.02-.58-.97,2.34-1.81,1.93-.64-.18-2.33,3.27-2.33.18-2.05,1.52-1.08,1.11-2.45,2.4-.17.18-1.48h2.22l1.87-1.65.12-1.65-1.58-2.73.41-1.99,1.29-.68,2.16,2.73,1.99-.97v2.56l1.99.97-1.11,1.65.7,2.96,1.7,3.02,2.28,1.76.18,2.45,1.4,2.28-1.52,2.45-2.22,1.88.41,2.73-3.51,4.1,1.81,1.2,6.78,5.29,2.4-1.48,2.69.97,2.51-1.2,2.69,1.48,2.22-4.38,5.38-5.29v-3.53l2.51-4.04-.12-1.93,1.29-.4,1.99,2.16,3.51.85,2.1,1.59,3.22.11,2.28,2.05,1.87,2.73-.12,3.93-1.52,3.13.99,2.45,2.98.68,3.1,2.28,1.81-1.88h2.28l1.81-.97,1.87.51,2.69-1.08,2.28.51.18,1.42-2.05,1.2,2.05,2.05,1.46-.06-.58,1.02-1.4.4-.82,1.59,1.64,2.28-2.1.17v1.76l-1.75,1.93.64,2.05,4.97,2.73,1.81,3.24,4.21,1.25h0l1.75-.4,2.51-2.11,2.69-.57,1.75,1.48,1.99-.63,1.87.57.94,1.65,1.93.85-.35,1.71-1.58-.46-2.1.51-.58,1.02-1.11-1.25-1.34.97-.99.06-.23,5.01-1.99.51-.35-.63-1.29-.17-1.52-.97-1.17,1.54-1.05.68-2.51-.17-1.99.57-.88.91-1.05-.23-1.93-2.33h-.99l-1.29.85-1.29-1.82-2.28-.68-.76-1.42-3.16.34-.82-.74-2.22.34-1.4,1.37-1.4-.68-1.52.51.41.85-1.34-.28-2.16,1.42-1.23-.28.12,1.02-1.64,1.02-.99-.68-1.05,1.14-3.27.06-.99-1.02-2.28.4-1.7-2.62-3.45-.23-1.52-1.93-2.51,1.14-2.92.74-1.4-.23-.82-1.14-.82,1.93-1.11.97-3.04,1.37.35,2.16h0Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(191.01 809.71)" font-family="IBM Plex Sans" font-size="8.09"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="0em">L</tspan>
				<tspan x="4.45" y="0">A BELLE</tspan>
				<tspan x="34.85" y="0" letter-spacing="0em">Z</tspan>
				<tspan x="39.77" y="0">A</tspan>
			</text>

		</g>
		<g id="CARCASI">
			<g id="Layer_2-30">
				<g id="svg2-30">
					<path id="path1219"
						d="m743.04,563.78l4.09-3.86.47-6.82,2.02-3.2,5.22-1.48.77-1.01-1.84-1.25.59-1.48,3.09-1.01.59-2.85,1.13-.12v-7.95l-1.84-1.48v-2.25l1.84-1.01.65-3.98-1.36-2.73-.24-3.09-3.41-3.11-1.2-1.2-1.98.04-3.14-.18-2.61-2.14-2.55.3-1.6-2.97-1.13-3.26-4.87-.18-2.02,1.25-3.56-.3-1.9,1.25-5.87-6.11-.59,4.57,1.01,2.67-.53,2.25-2.25,2.02-1.72-.12-2.02,1.54h-4.27v2.37l-1.72.3-1.13,1.13v2.61l.89.83-1.6,1.31-.47,2.14-.3,3.68-1.78,2.91-3.44,2.91-2.14,3.92-2.61,3.44-1.31,3.26-.53,2.91-.3,3.68-2.67,1.54,2.85.65,3.86-2.25h3.32l2.25,2.14-.59,2.55.89,2.43,1.84,1.31,3.03,2.73,2.97-.12,1.54-1.96,2.25-.83,1.01,2.67s.42,3.86.18,4.27c-.23,1.02-.41,2.05-.53,3.09l1.42,1.96,2.43-2.02h3.86l2.97-4.51,2.02-.3.12-1.54,3.26-3.56,3.32.71,4.03,2.73,1.84.18Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(711.66 538.09) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.03"
				font-weight="500">
				<tspan x="0" y="0">CARCASÍ</tspan>
			</text>
		</g>

		<g id="RIONEGRO">
			<g id="Layer_2-31">
				<g id="svg2-31">
					<path id="path1225"
						d="m313.32,151.59v-3.6h2.4s0,0,4.8-2.4c2.4-3.6,0-4.8-1.2-6-8.4-12,6-14.4,7.2-15.6,0-2.4,2.4-6,4.8-8.4,1.2-3.6,9.6-2.4,9.6-2.4v3.6l3.6,6,1.2,9.6v4.8l2.4,3.6v3.6l1.2,3.6,3.6,6,1.2,1.2,3.6,1.2v1.2l1.2,1.2,2.4,1.2,1.2,1.2,1.04,2.09,1.59.59,3.24-.3,3.07,1.71,1.18,1.3,3.48-.71.59-1.71,2.48,2.31v2.6l2.36.12.59.89,1,2.31h1.3l1,2.31-.41,1.3,1.89,3.49,1,.12-1,1.89-2.18.12-.88.71-1.47.41,1.89,1.6,2.77.59,1.77,2.31,1.18-.3,1.47,2.01-.59,1,2.18,2.31,3.18,1.6.88,2.01-.12,3.49h-.59l-.12,1,1,1.89,1.18.12.88,1.89,1.53.53.12,1.65-1.24.65.94,1.06.12,1.77.94.53.71-1.54h.53l.29,1.65,1.47.24.94-.53.29,1.24,1.24-.65.53.3.65,2.07,1.89.71,1.24-.24,2,1.54-.29,1.24,4.07.65v.95l1.59.24,1.59-.95-.12-.71,1.47-.41,1.65,1.48h1.89l.71-1.06,1.06,1.06-.29.95,1.77.95,1.65-.3,1.47.24,2,1.65,4.07-.3.41-.95,1.06.53,1.59-.65-.12-.65-2.18-1.36.24-.65,1.77-.95,1.06-1.24,2.12.24.83,1.95,2.53,3.01,2.42,1.54-.41,1.24.83,1.48,1.47.71-.12,1.12-.71,1.24.94.53,2.42-.71,1.18.53.41,1.54,1.59-.95v-.95l.83-.41,2.95.12.65,1.65,1.18-1.54,2.83-.3.12-.83h2.12l.94,1.06h1.47v1.24l2-.83,1.36-1.89,1.59.41,1.18,1.42-.77,1.48.29,1.65-4.07,1.24-3.6,2.9-1.18,2.9.88,1.24-2.12,1.54-.59,2.01,1.41,2.19,2.3.89,1.12,1.3.12,1.42,1.12.12,2.42,2.96.29,1.24,2.12,1.83.29,1.65-1.18,1.12h-2.24l-1.53,1.42v2.84l1.3,1.54,1.3-.12.59,2.25h2.83l2.42.71.88-1,2.3-.3,1.18-.71,2.59-.18.88.83,2,.12,1.3,1,1.18-.18.41-1.42,2.24-3.43,1.3-1.24-.41-1.71,2.42-1.54,3.01.3,3.6,3.96,1.59-.89,2.12.3,1.71,1.3,1.53,5.5,1.59,4.67,1.71.18,1.18-.83,1.83.71,1.3-.41.18-3.13,1.18-1.95,1.83.41,1.41-1.71,1.53.41.83-1,1.83.41.53-1.12,1.71.18,2.71-.59,1.41-1.3,2.3-1,.53-1.65,1.18-.18,2.3-4.2,1.71-.89,1.59-2.01,2.24-1,2.3-.18,1.83,1.12.88,1-1.7,2.18-1.42,2.31.59,1.83,2.24,2.13.41,2.54-1.18,3.78-2.59,3.78-.41,2.25-2.42,4.2-2.95,2.96-.12,3.43-1.18,1.3-.41,1.71-1.53,1.71-.18,1.65-1.59,1.12.29,4.26-1.3,3.13,1.59-.59,2,.71.41,1.3,2.3,2.54v1.42l-.88,1.54-.18,4.2,1.89,2.84-2,2.42,1.12.89,4.72.12,2.24,1,3.24,4.08.29,2.96-.88,1.83-1,1.3-1.59-.3-2.12.59-4.95,4.91v2.01l-1.53,2.66-2.59-.12-.88,2.01.29,1.54-3.12,2.66h-1.83l-.88,2.13-1.71-.3-3.6-2.36-1.89-.59-2.59,1.65-2.3-.3-2.3,1.83-1.89-1.24-1.89-.3-2.3-1.95-2.71-.83-2.59-.12-2.59.3-1.53-1.3-1.41-.3-1.89-2.72-2.71-2.13-1.18-.18-1,.71-1-.59-.41-1.54.18-4.55-.88-3.37.29-2.42-1.83-2.96-.53-1.95-2.42-.59-2.3-2.84-.59-3.13-1.12-1.24-.12-3.66-1.18-1.3-.18-3.07-1.12-1.42-2.12-.83-.29-1.54,1.83-.89.29-1.65-1-1.24-2,.3-1.3-1.3-2.24.18-.29-2.01-1.41.12-.18,2.13-2.53.89-2.95,2.96-1.53,2.36-4.13-.18-1.83-.89-.71-1.65.71-1.71,1.53-1.3.12-1.3-1.59-1-.29-2.72-.88-2.13.18-1.83,1.53-1.95.53-2.42-.71-2.36-1.41-1-.41-2.66.29-3.96-1-2.66-1.59-1-.71,1.71-2.83-.3-1.41.41-.71,1,.41,1.3-.59,1.12-1.83.83-1.41-.41-2.24,1.65-5.13,2.36-1.83-4.49-4.3,2.72-4.95-6.03-1.41-3.84-2.59-2.54,1.71-3.25-.83-1.3-2.53.71-1.18-1.83,1.18-2.25-.88-1.65-2.95,1.24-1.71-1.12.29-1.54,2.24-2.72-1.59-1.24-2.59,1-1.41-1,1.59-2.72-5.31-5.2-5.84-1.42-2.83,1.95-2-3.07-1.89-.12-2.12-1.95-.29-2.72-2.24-2.72-2.83-1.83,1.41-2.66-1.18-.83-2.42.18-1.59-1.54-.41-4.08.83-1.54-1.53-1.24-3.54-6.03-6.66-.89-4.13-1.24-1.59-1.71h-3.01l-3.42-2.96-2.24,2.13-1.83-1.42-1.71,1.24-2.24-1.3-3.24.71.18-3.25-3.01-.18v-3.07l2,.41.59-1.54-1.53-3.66-1.53,1.24-1.12-1.65,1.18-2.66-.88-1.3-2.24,2.25-1.41-1.83-2.53,1.12.53-3.25-2.59.59.41-1.95-2-.18-.83-2.84-1.89-3.07-3.83-.71-1.12-2.42.53-2.01-2.59-1.65-5.13,2.36-1.12,2.54-2.59-.53-1.83-1.54-2.53.89-1.71-3.84v-3.43l1.83-2.9-1.65-1.36,1.18-2.31-1.06-1.54,1.83-2.13-.12-4.96-.89-3.12Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(474.73 291.74)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">RIONEGRO</tspan>
			</text>
		</g>

		<g id="SURATA">
			<g id="Layer_2-32">
				<g id="svg2-32">
					<path id="path1229"
						d="m562.06,205.03l1.62-.24.3-1.8-.42-.96h1.62l.3-.84-.66-1.08,1.2-.66.96.72.66-.42,2.34.42.54,1.56,1.5-.72.24-1.08,2.76.12,1.68,1.08-.12.84,1.2.42,1.68-.66-.24-.84,1.2.24.66,1.14,1.2-.24.54-1.08,1.26-.42.96.54-.42,1.26.84.24.12.84-1.2,1.92-1.08.24-.66,1.8.42.84-1.5.54.12,1.08-.84.3.24.84,1.92,1.08v1.98l-.72.42,1.5,3.6,1.08,1.26-1.08.24.54,1.92,1.5,2.76.06,2.58-.42.6,2.76,1.98.06,4.14-.66.3,3.18,3,2.82-.36,4.68,2.16.9-.66,2.16,2.64-.72,1.2,3.54,2.4.36,2.22,2.94-.3.12-.54,1.2.36,3.48,3.6-.36,1.2,1.68,1.26,2.22-.96,1.14,1.98-1.14,5.76-2.22,3.42-.12,2.7,3.72,6.9,3.12,1.26,2.22,4.2-.54,2.04-1.74.6-1.02-2.46-1.86-.84-.9.72-.9-.42-.6.84-1.32-.12-1.62,1.74h-1.92l-1.86,2.28-.6,1.32-1.32-.72-1.32,1.56-1.02,1.44-2.4,1.86-1.02,3.3-2.46,4.44-1.86,1.14h-3.66l-1.86-1.02-1.44-.72-1.62-.6-2.16,1.14.3,2.4-.42.6,3.9,5.16h1.02l2.58,2.4,2.58,2.88,1.92.3,2.88,3,1.14.18,1.44,1.56h1.14l3.66,3.3-3.78-.3-.9-.6-1.2.42-2.76-3.18-1.2-.6-.3-1.26-2.04-.6-1.02.54-1.32-1.74-3-2.28-1.86-.18-2.28,1.56.42-2.58-.6-1.56.72-1.98-1.86-1.74-1.92-.3-1.56-1.32-.9,1.14-1.92,1.02,1.2-1.98-2.28-3.3-3.6-3,.18-1.98,1.86-1.44,1.56-2.46,1.14-2.76-.42-2.46,1.14-2.28-.12-6,1.02-1.86-1.56-.84-1.44-1.44-1.14-.18.12-1.44-.84-1.86-1.44-.12-3-2.7-1.92-.42-4.32-4.62-1.32-.72-.12-3-1.02-1.26-1.92-5.34-2.28-.12-2.16-1.56-2.43-.72-2.4-1.2,1.47-1.62,2.58-4.32-.54-1.74-4.02,1.26-.3-2.16-3.18-4.02-1.92-.54-.3-1.56,2.46-2.88,1.56-5.34,1.86-.6,2.46-1.14.9-4.02,2.76-.42,2.46-3.9.9-2.88,1.08-1.26.96-1.8Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(574.14 256.77)" font-family="IBM Plex Sans" font-size="7.3"
				font-weight="500">
				<tspan x="0" y="0">SUR</tspan>
				<tspan x="13.72" y="0" letter-spacing="-.06em">A</tspan>
				<tspan x="18.15" y="0">TÁ</tspan>
			</text>
		</g>

		<g id="TONA">
			<path id="path1231"
				d="m615.65,331.55l2.4-3.6h6l2.72,4.78-.32,1.22,1.2,1.2v1.2l2.4,2.4v2.4l1.2,1.2,2.4,2.4,1.2,1.2h2.4l1.2,1.2-1.05,2.58-1.35,2.22.89,2.32,4.45,6.88-1.8.6-1.15,1-2.4,2.4v3.6l-2.4,3.6-3.6,3.6-4.22,4.96-2.36,5.73-1.36,1.48-.89,1.89h-5.19l-1.53.71h-4.55l-1.3-2.54-4.66-2.01-3.96-2.95-3.01,4.49.83,2.54-.71,2.36h-2.6l-2.54-2.66,1.18-1.95-.71-1.65.12-2.95-.83-.89.83-1.95-1.89.71-2.24-.59-1.18-1.3-2.83-.12-1.42.89h-1.3l-1.3-1.42-1.3,1.12-1.89-.83-.89-2.95-4.01-2.83-1.12-2.42-.3-2.54-2.42-.83-2.13-1.42-.71,2.01-1.53,1.3-3.25-2.36-3.6-.3-2.13-1-.41-4.19.12-3.54,1.89-2.54,1.18-2.95,2.3-2.42,1.3-.12,1.59,2.13h1.42l2.01-3.07,1.83-.18,2.72-3.36,1.42-4.07.12-3.25.71-.83,2.01-.71.12-1.65,1.3-1.95,1.89-.41,4.96-4.07,3.13.18,1,1.95,2.01,2.54,1.59.3,1.3,1.12,2.61.59,2.4-1.2,3.6-1.2,3.6-1.2h10.8Z"
				fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path><text transform="translate(585.53 358.74)"
				font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0" letter-spacing="-.01em">TONA</tspan>
			</text>
		</g>

		<g id="GUACA">
			<path id="path1235"
				d="m634.7,396.05l.93,2.47,1.79-.12,1.42-.68,2.6.49,1.81-5.48h6l2.4,6,1.53,3.75,6.87-4.95h7.2l-2.4,4.8,1.2,3.6,1.2,4.8,1.07,4.73,2.35,3.77.74,4.02,1.05.12-.62.99-.56,3.71.43,2.53h0l-.56,2.66-2.72.27v6l-1.2,2.4-1.2,2.4-1.2,2.4-2.68,2.41-.92,1.19-1.2-1.2h-2.4l-2.4,1.2h-3.6l-2.4,1.2-3.6,2.4-3.6,1.2-1.2,4.8-1.2,2.4-.3,1.39-.9,5.81-2.4,1.2v2.4h-2.4l-1.2,2.4h-1.2l-1.2-1.2-1.2-1.2h-4.8l-2.4-1.2-2.4,1.2-1.2,2.4.03,3.77.93,2.66-1.67.31-2.22,3.4-1.17.12-3.09,4.14-1.48-.12-2.84-3.09-5.87-.43,1.67-3.34.49-3.65,4.26-5.81,2.29-5.13-1.24-9.21,1.36-2.16,3.27.19,3.21-2.78,1.98-.49,2.41-2.78.12-3.03-.56-3.21,3.03-2.22,2.04-3.27.25-5.31-1.67-1.79,1.92-4.94,4.63-7.04-1.28-4.75.35-3.29-.43-3.34,3.95-1.73,2.29-3.89-1.79-3.34.43-3.71,1.05-.19Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(629.67 441.16) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.63"
				font-weight="500">
				<tspan x="0" y="0">G</tspan>
				<tspan x="5.87" y="0" letter-spacing="-.01em">U</tspan>
				<tspan x="11.4" y="0">ACA</tspan>
			</text>
		</g>

		<g id="CERRITO">
			<g id="Layer_2-33">
				<g id="svg2-33">
					<path id="path1237"
						d="m669.07,425.43l2.81-4.04,2.56-2.27,1.77-1.94,1.58-.99,4.91.12.76,1.23,2.69-.64,1.35-1.7,2.22-.88,2.69.99-.64,3.57,1.7,2.69,2.11,7.31,1.11-.23,1.35-3.92,1.11-1.87,1.35-3.45,2.81-.47,4.04,3.22,5.03-.47.35-1.58,2.22-.12,3.16,1.11,2.46-.23,2.92-2.81,5.38-1.52,2.69.76.94,1.93,2.87,2.16-.64,1.35.82,2.05-.47,2.28-.76.99v1.75l1.87,1.17,2.05.82.47,2.87,1.11,4.21-1.75,1.52.06,1.35-2.22,1.99-.12,5.56-.88,1.46,1.35.88.94,3.39-.58,3.28-1.64,1.17-.53,3.51,1.4,2.4-.41,1.93-1.58.82.58,2.4-1.17.88.12,1.93-.58,1.64.99,2.4-2.52,2.52-2.11.7-.58,2.4-3.28,4.45-4.39-.99-3.28.41-1.87-.99-1.99-.29-2.69,3.74-1.11-2.52-.7-3.51-2.4-2.69-2.11,2.22h-1.52l-.58-1.7-2.92-2.63-3.22-.41-2.69.58-2.22-1.11-2.57,2.34.99,2.11-.7,1.52-2.22.58-.58,2.34h-1.99l-5.21-2.52-1.87-.41-1.7-2.81-.99-2.4-1.81-2.52-1.58.12-.82,1.7-1.99-1.81-2.22.7-3.69,1.99,1.29-3.33,2.81-3.92,2.69-1.64.18-1.52-2.57-4.74,2.69-3.22.12-3.33-.82-1.93.53-3.39-.82-4.04,1.4-1.93,1.11-2.4-1.11-3.22.7-2.81-.58-1.81.58-2.11-3.22-.88-.41-2.4.53-3.51.64-.99.7.06Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(682.67 450.67) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.48"
				font-weight="500">
				<tspan x="0" y="0">CERRI</tspan>
				<tspan x="26.04" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="31.67" y="0">O</tspan>
			</text>
		</g>

		<g id="CONCEPCION">
			<g id="Layer_2-34">
				<g id="svg2-34">
					<path id="path1239"
						d="m738.03,457.52l3.07-.04,1.73,1.24,1.74.65,2.59-2.12,2.94.65-.07-.38-.58-2.39,1.78-3.61h2.4l2.12,2.79.12,4.41.88,1-1,1.12-1.12-.35-1,3.03,1.2,1.2,2.5.82,1.47,1.59.35,5.29,3.47,2.94-.12,3.47-2.71,2.59-.24,2.35,1.24,2.71-2.35.35-2.94,3.71-.88,1.47,1.12.88v9.35l3.47,1.35-1.71.59,1.59,7.12-4.65,2.24-2,1.24-3.12-.18-2.59-2.12-2.53.29-1.59-2.94-1.12-3.23-4.82-.18-2,1.24-3.53-.29-1.88,1.24-5.82-6.06-.59,4.53,1,2.65-.53,2.24-2.24,2-1.71-.12-2,1.53h-4.23v2.35l-1.71.29-1.12,1.12.12,2.53-3.82,1.94-2.29-.82h-1.71l-2.82,1.65h-4.12l-1.71.18-.88.82-2-1-.12-2.35-2.53-.29-3.41-3.12.71-1.71-4.12-.71-.18-2.35,1.59-2.94-5.23-5.18-5.71-4.35-1.82-.59-1.71-2-1.29.12-1.41-1.65.71-1.94-1.18-1.29-.53-3.76-.53-2.35v-2.41l-1.12-1.82-.41-4.35,3.71-2,2.24-.71,2,1.82.82-1.71,1.59-.12,1.82,2.53,1,2.41,1.71,2.82,1.88.41,5.23,2.53h2l.59-2.35,2.24-.59.71-1.53-1-2.12,2.59-2.35,2.24,1.12,2.71-.59,3.23.41,2.94,2.65.59,1.71h1.53l2.12-2.24,2.41,2.71.71,3.53,1.12,2.53,2.71-3.76,2,.29,1.88,1,3.29-.41,4.41,1,3.29-4.47.59-2.41,2.12-.71,2.53-2.53-1-2.41.59-1.65-.12-1.94,1.18-.88-.59-2.41,1.59-.82.41-1.94-1.41-2.41.53-3.53,1.65-1.18.87-1.55Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(678.18 499.63) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.48"
				font-weight="500">
				<tspan x="0" y="0">CONCEPCIÓN</tspan>
			</text>
		</g>

		<g id="PUERTO_PARRA">
			<g id="Layer_2-35">
				<g id="svg2-35">
					<path id="path1243"
						d="m206.33,560.13l-.58-1.75-.99-2.51-1.69-1.23-1.23.12-1.05-1.05.06-1.05,1.58.06,1.4.76,1.69-1.05-.41-3.74.58-.82v-.82l-1.17-.76-.12-.7,1.23-1.11-.29-.7-1.58-1.46.29-1.34-.41-1.58-1.23-.58-2.22,1.23-1.46,1.52-1.75-.12-1.05-.47-.99.99-.47,1.05-1.58-.23-1.46-1.69.58-1.75,1.05-.23,1.58.47.93-.82-.12-2.75,1.87-.93.41-2.45-2.04-1.69.58-1.58,1.93-1.23-.93-1.58-2.92-.64-.7-.82.47-1.52,2.28-.29.47-1.69-.64-.99-2.86.12-1.4-1.17.35-1.75.64-.47-.64-1.17-2.28.12-.82-1.23.58-1.58,2.51-1.46,1.52.82,2.04-.23.7-.93-.06-1.17-1.58-.41-2.04-1.34-2.28.35-1.52,1.17-1.52-.93.82-1.4,2.1-.35.76-.76v-.7l-1.05-.41.06-1.34,1.4-.99,1.87-.12-.12-1.58-2.22-.41-.47-1.75-1.4-1.23-1.52-.12,3.15-4.44,3.62-1.99.35-1.93,5.14-1.11,3.1-2.8,1.99.53-1.99,2.69,2.22,1.93,2.98-.99-1.87-1.52.29-1.93,2.98-.18,2.1-2.1,3.21.41v-1.28l-2.39-1.4-1.11-3.91,3.5.29-1.52-1.52.7-1.52,1.69.58,1.87.88,1.11-3.39,1.69-1.52,4.67-.29,8.59-4.73,12.38-8.53,3.39,2.1-1.87.82,2.22,1.11-2.51,2.69,2.22.88.41,3.39,1.58,2.92-3.27,2.39,1.52,2.69-1.87.29,1.69,2.39-2.57.18-1.28,3.1-1.58-.29.7,2.69-2.51,1.93,2.1,2.69.7,3.1,2.1-1.28.7,1.11-1.99,1.64.99,1.4,1.17-.82,1.99,3.1,1.52-.12,3.39,2.69,1.99.12.88,1.93,2.39,1.28.82,2.39,2.51.82v1.93l1.81,2.63,1.11-1.11,1.99.18,1.4-1.81.82,1.52-.41,2.39.58,2.22,1.4-2.34.88,2.69h2.28l.41.99-2.22.41-.18,1.64,3.39.88-1.81,1.52,3.56,1.23.18,1.93,2.1.99-.82,2.69,2.39,1.99-2.98,2.92,3.8,2.34,3.39,3.39-1.87,2.34,2.28,2.69-.58,2.39,2.22,1.4,1.99,2.51,3.5-.88.7-2.63,2.22.7,2.51-1.23,2.51,2.34-.41,3.8,2.28-1.99.7,2.69,1.17,1.52-2.28,3.21-5.08,2.34,2.28,3.39.29,3.33,2.1-.7.82,1.64,2.39.82h5.2l3.56,3.04-2.57,4.21-1.52-.41-1.4,1.93-3.91-1.11-2.22-2.22-3.91-1.4-4.5.58-.99,1.64.12,2.69-6.31,5.84-5.2,6.72-1.87-2.1-2.1.58-2.22-1.64-2.39,3.21-1.87-1.58,1.46-3.5-1.46-2.39,1.11-2.51-.53-2.86-2.8.06-3.5-2.75-1.64.53-2.34-1.4-3.33,2.69-3.15.23-2.39,2.04-2.39-.18-1.93-2.39.12-3.1-2.28-1.69-2.1-.18v-1.93l-2.8-2.22-3.68-.53-3.1-2.8-3.39.12-4.79-3.04-4.21-.29-1.52-1.4-4.91,1.4-2.8-.12-1.99-1.81h-2.22l-1.28,1.46-2.34.18Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(216.75 527.97)" font-family="IBM Plex Sans" font-size="10.52"
				font-weight="500">
				<tspan x="0" y="0">PUE</tspan>
				<tspan x="19.52" y="0" letter-spacing="-.02em">R</tspan>
				<tspan x="25.83" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="32.07" y="0">O</tspan>
				<tspan x="3.2" y="12.62" letter-spacing="-.09em">P</tspan>
				<tspan x="9.01" y="12.62">ARRA</tspan>
			</text>
		</g>

		<g id="PUERTO">
			<g id="Layer_2-36">
				<g id="svg2-36">
					<path id="path1223"
						d="m256.86,338.76l-2.33-2.48,1.14-1.83.48-3.01-.84-1.83v-6.5l2.68-4.02-.18-2.3-2.07-4.59,1.22-4.82h0l3.65-2.41v-7.22l1.22-1.2v-3.61l.4-2.19-.66-4.43.95-1.18v-2.07l1.73-4.58,2.43-4.82,1.22-1.2v-1.2l-1.26-3.43-1.01-2.78-1.43-1.12-.84-3.72-2.75-.99v-4.82l.48-1.99-1.7-.42-.4-2.62.78-5.14,2.06-1.87,5.35-.28,4.38-5.74,3.65-7.22,3.65-3.61,1.22-2.41v-3.61l2.43-2.41,4.48-2.52,1.79-1.89,1.55-3.54.24-2.78,1.19-.77,1.69-1.75v-1.2l-1.22-1.2,1.22-3.61,1.65-2.09-.78-.41-.3-3.25.66-.12.24-2.36,2.74-3.66-.18-4.49-3.22-5.02-1.34-2.03v-1.2l-2.4-2.4.49-1.86v-3.61l2.43-3.61,2.43-3.61h2.43l2.43-7.22-1.82-6.47-2.4-1.2-1.2-4.8-2.93-5.21,1.05-2.78,2.01-3.47-1.33-.53.54-3.23-2.43-4.82-.51-3.96-2.26-4.66-.88-1.57-1.66-3.36-.77-3.3-4.02-2.7-4.8-6,1.2-4.8,3.6-6-5.7-8.2-1.49-8.6-.94-8.26,1.22-2.41,3.13-6.57,2.67-11.62,1.13-2.89,2.8-1.79,3.42-.94,5.09-3.88,1.22-2.41,1.22-2.41,1.22-2.41,4.86-1.3h.66l1.13.89,1.25-1.06,1.61,1.06,2.56.94-.6,2.6.84,2.01-1.37,1.89-.84,3.72-2.39.18-1.55-1.18-1.19.65.18,1.18,1.37,1.54,2.39.18,1.85.65.66,3.72,1.73,2.01-.36,1.71,1.19,3.25,2.21,1.18,1.55.35,1.19.53,1.49,2.36h2.39l-.66,1-2.21.65,1.85,2.54,2.03,1,1.19-.35,1.01.18-.36,3.37-2.03,1.18.18,1h2.74l2.21,2.19.36,2.89,1.37,2.19-1.55,2.54.18,1.36-1.19,1.18,1.85,1,.36,2.54-.66,2.54,1.55.83,3.58-.83h1.37v2.19l1.37,2.89,2.56,1.36,1.37.18,1.85.53.54,3.25,1.85.18.66,2.01,1.43,1.83-2.92,3.66.42,2.48,2.68,2.13.66,3.48-1.79,2.6-4,1.48-1.55,2.72-.36,2.66-3.7,1.06-2.21.83-2.21,3.37-1.19,3.25-2.21,2.24-3.16.65-3.04,2.19-.84,1.36-2.03,2.01v2.19l.78,3.9,2.74.35-.24,2.24.85,1.11.52,3.32-2.39,2.19-3.83,1.26v1.2l-2.31-.1.84,2.36-.36,1.54,1.37,1.71.36.94.12,5.08-1.85,2.13,1.07,1.54-1.19,2.3,1.67,1.36-1.85,2.89v3.43l-1.13,2.24.84,1.95-2.45,3.78,2.56,2.66-.3,4.55-1.13,2.83,1.91,3.07,2.86,1.3,3.34,6.73-.72,2.54,6.8,7.09-1.73,2.54-3.64,1.95-4,5.26-3.88.59-.6,2.36-3.58-.89.54,4.19-1.61,2.24,2.15,4.78-1.01,5.79.89,5.08-1.31,3.25.72,8.15-.72,10.57,2.15,2.42,3.64.41,3.46,2.72v3.37l3.76,1.42,3.34-.71,6.5,4.67,2.45.41,1.19,3.43-2.15,2.42,4.77,3.25,3.88-1.54,3.88-.12,1.19,3.37,4.35,4.25,3.28.71-1.85,2.72,1.73,1.3-1.55,4.37,2.27,3.43,2.98-.41,1.43,2.83,3.16,1.54-.42,1.71,3.64,3.43,2.15.59.12,2.24,2.56,2.66,3.64,8.45,2.62,9.15,5.73,12.7h0l-2.45-1.83-2.62,1.54-2.15.18-3.76-1.83-1.73.71-3.04-1.65.72-1.71-1.61-2.54-1.73-1.24-2.27.53-.72-2.95-1.13-.83-1.91.59-1.85-2.66-2.03.89-3.28-2.95h-2.56l-1.13-1v-3.07l-2.15-.59-2.03-2.66-2.74.18-3.16-3.54-3.88,1.12-4-.53-4.89,5.08-4.35,2.95-2.74.18-2.15-3.66-1.73-.59-1.85.89-2.15-1.24h-3.16l-4,.71-.3,4.37-2.03,3.07-3.76,2.95-2.27-.53v-2.01l3.16-3.37-1.13-1.71-5.31.83-11.39,7.09-3.58,3.37-3.28,3.84-6.62.71-4-1.24-5.96-9.33-.6-.89,1.85-1.77Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(277.65 303.83)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">PUERTO</tspan>
				<tspan x="0" y="14.02">WILCHES</tspan>
			</text>
		</g>

		<g id="BOLIVAR">
			<g id="Layer_2-37">
				<g id="svg2-37">
					<path id="path1241"
						d="m163.35,801.84v1.2l-2.4,7.2-3.6,2.4,1.2,2.4s1.2,1.2,1.2,3.6c0,3.6-2.4,2.4-2.4,2.4l-2.4-2.4-7.6,8.4s-6.8,1.2-10.4,0c-13.2-6,0-16.8,1.2-18,0-3.6,6-4.8,6-4.8l3.6-4.8v-3.6h1.2l-1.2-2.4h-1.2v-1.95s-.62.32-.62.32h-1.23l-.47-1.52-.76-.76v-1.64l.88-.88.58-1.99,3.15-3.15.47-1.23h.76l.47-1.23-2.39-2.57-.76-1.81-2.39-.58-.88-2.69-3.45-1.52v-1.99l-2.28-2.57-1.23-2.28-.58-1.99,1.34-.58v-.58l-1.34-1.05v-.88l-1.05-1.23.88-1.23.18-1.34-.76-.76.47-1.64.58-.29-.18-1.23-.76-.18h-.58l.76-1.64,1.64-1.05-.18-2.39-2.1-.47-1.05-.88h-1.64l-1.52-1.23.47-3.04-2.1.58-1.52-1.05h-.47l-.18,1.23h-1.52l-.88-1.52h-1.23l-.47.58-1.34.18v1.99l-1.23,1.34-.29-1.52-.76,1.05-.76.18-.58,1.23-1.05.76-.58-1.4-.93.35-.47,1.28-.23-.58-1.17.35-.93,1.28-1.4-.82-1.28.35-1.05,2.57-1.28,1.28-.93,4.85-1.75.58-.82-1.05-.47,1.05-1.87.12-1.4-.93-.12-.82-.47-1.28-1.17.23-.7.93-1.28.23-1.75,1.28-1.05-.23.12-1.64,1.17-.82-.93-1.05-.82-.23-.23-.93-3.33-1.28-1.17-1.05-.47-1.75-1.05-.47.58-.7.35-1.99-.47-2.1-1.75-.47-1.17-1.87-.35-.7-1.17.47-.12.7-1.4-.12-1.28,1.05-.7-1.28h-.58l-1.52.12-.82-1.05-2.1-.23-1.52-1.52-2.45-.82-1.05-1.64.82-1.4v-1.99l-1.17-1.4-.23-3.56-.82-.23.23-1.64,1.17-.93-.7-1.52-4.61-3.33-.12-1.99-2.57-.7-1.17-2.74,1.17-2.45,1.64-3.1-.82-4.5,1.87-2.45-2.57-1.05-1.17.47h-2.57l-3.68-4.15-3.1-.23-1.99-3.1-.23-3.45-1.52-.58-2.34-4.03.58-3.45-.93-2.22-1.75-.7.76-2.39,2.16-2.04.76-.76,3.21-2.22,5.2,5.08,6.89,7.24,7.18,2.1,4.2,4.2,1.69-.53,1.11,1.93,1.52-.29,3.2.09,2.94,1.83,1.23,1.4-.35,2.39-1.23.47-1.11,2.39-.12,2.04-.93,1.58-1.58.35.23,1.05,2.51,1.05,1.34,1.93-.7,2.51-.58.58-.47,2.74,1.05.35v3.1l.82.58,1.58,3.39-.7.93v1.81l1.11,1.05.23,1.23h3.5l2.28,2.74,2.39.12.82-.58,1.46.23.82,1.93,3.1.47,1.93.7,2.28-1.58,1.46.35,1.11-.82,4.2,2.28.35.35,2.39-.12,1.34,1.46h.82l1.46-1.46,1.11-.23.58.35h2.74l1.46-2.04,3.62-3.27h.7l2.04,1.81,3.21-.12,1.93,1.11,1.46.12,2.16,2.16,1.11,6.95,2.51,1.69,1.58-1.23,3.39,1.05,1.93,6.6,2.39.47,3.74-1.93,1.93-3.27,2.86-.35-.23-1.05.47-1.23-.35-2.86,1.58-.23-.12-1.93-.93-1.46,1.93-2.63.23-3.21,1.23-1.23-.23-4.32-1.58-1.23-.58.82-2.39-1.05-4.67-6.6.35-4.32,3.1-1.58,1.58-.23v-1.81l-2.86-1.34,1.46-.93.47-2.51.58-.58,1.11,1.93,7.83.35-.7-1.93.47-1.46,1.23-.23.93.82,1.81-.47.06-4.6,3.6-3.6,3.6-2.4,2.67-3.18,1.4,1.69,1.34,1.17,1.11.82.53,2.04.15,1.06h1.2l2.4-1.2,1.33,1.72,1.69,1.17.58,1.58.47,3.45.76,1.05,1.58,2.45.99,1.52,2.04-.41.41,1.52.99.29,1.11-1.4,1.52.12,1.17,1.99h1.81l1.58,1.23,2.69-.41,4.5,1.11,9.05-8.12,3.56.7,3.39-1.99.29-1.4,5.78-3.74,1.28.7,2.69-1.4,1.4,2.51,1.99-.88,1.17.88,5.26-4.09,2.57,1.58,3.56-2.22,4.09,2.51v3.39l1.17,2.63,1.28,3.8,2.63,1.4,2.98.76,1.52,2.34,2.74.41.41,4.09,1.81,2.39,1.58-.58.18-2.1,2.1-1.93,3.1-.41,9.23-8.7,6.37-2.92,3.39-.58,1.87,1.28,2.1-.12,2.98,1.23,1.11,1.58.12,2.1-.93,3.97h0l.82,3.56,3.39,1.4,1.58,2.22.99,3.04,2.39.93,3.8-.78.93,3.06.06,2.98.21,1.16v1.2l-1.2,2.4-1.2,1.2-3.6,2.4-.7,3.44.29,1.52-.82,2.92.58,1.28.88-.18.88,1.23-.82,1.52,1.87,2.63-.12,2.39.7,4.32.99,2.92.82,3.27,2.45.93,1.99,2.04-2.16,4.15-1.34,1.46-1.17-.53-1.52.76-1.05,1.93-3.45,1.93-2.16.23-.18,3.8-.82,1.93.7,2.69.12,2.51,2.22,4.2-2.04,1.69-1.23,2.8-1.99,1.52-7.18-1.05-6.07-2.39-2.22-1.81-2.8-.82-1.52-1.23-3.91,1.64.12-1.99-3.21-4.03-2.98-3.33h-1.28l-.99-1.64,2.8-2.22.58-2.34-.76-1.28.99-3.85,6.25.06,3.21-1.87,3.97-1.93,2.8.12,3.1-.99,3.39-3.1.29-4.61.82-4.61-1.11-2.34-2.63-1.81-1.81-2.8.29-3.5-1.81-3.33v-2.63l-2.57-3.21-1.99-4.03-5.66,4.44-1.81.88-2.34-.35-1.87-1.75-2.39-.58-2.28.23-2.34-.93-1.34-2.34-.12-2.92-1.28-2.1-2.39-.41-1.69-1.28-2.57.7-2.1,2.34-1.99,1.28-2.8,1.52-2.57.7-1.4.23-.99-3.62-.12-2.39-.99-.99-1.4,1.11-1.58-.18-1.11-2.63-1.58,1.28s-2.28-.12-2.22-.53,0-3.5,0-3.5l-.99-2.69-3.39-2.1,1.11-2.69-2.1-1.93-1.58,1.11-2.1-1.93-1.58,1.28-4.91-.29-1.87,1.28-5.08-.18-7.36,5.14-2.51.41-1.58,1.52-2.22.12-.29,2.22-2.57,1.28-1.99-1.11-2.57,1.99-4.2.18-.99-2.34v-3.1l-1.75,1.23-.93-.29h0l-1.4.23.29,1.17.99.58.06,1.4-.29,1.69-1.11,1.17.18.99,1.34,1.11,1.58,1.11,1.34,1.05.58.82-1.17,1.05-.93,1.28-1.99.23h-1.34l-2.22.7-1.28,2.16v1.93l.76,1.05,1.87,1.4,1.28,1.05-.88,1.11-1.58.99-1.17,1.28-.58,1.4-.18,1.58-.18,1.52,1.28,1.05,1.75.99.88,1.28-.29,1.28-1.46.76.18,1.17.64,1.11-.06,1.69,1.11.82,1.46,1.52.06.58-1.34.47-1.46-.06-1.46-.06-1.11,1.11-1.99,1.69-1.34,1.05-1.28.7-.41,2.04,1.58,2.8-.12,1.69-1.87,1.69h-2.22l-.18,1.52-2.39.18-1.11,2.51-1.52,1.11-.18,2.1-3.27,2.39.18,2.39-1.28,1.52-2.98.99.58.99,2.22.88-1.17,3.39.29,2.1-1.87,3.39-2.28,1.52v1.64h2.22l.88,1.52-3.8,2.22-.74,2.44"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(144.44 758.12)" font-family="IBM Plex Sans" font-size="8.09"
				font-weight="500">
				<tspan x="0" y="0">BOLI</tspan>
				<tspan x="17.35" y="0" letter-spacing="-.04em">V</tspan>
				<tspan x="22.28" y="0">AR</tspan>
			</text>
		</g>

		<g id="CIMITARRA">
			<g id="Layer_2-38">
				<g id="svg2-38">
					<path id="path1245"
						d="m54.24,665.38l8.15-4.72,4.54-3.79,2.56-5.53.27-4.27,1.2-4.8,6.16-7.94,3.67-3.32,3.09-.99,2.68-.17.7.82,6.41-.06,3.09-1.92,1.05-4.95-1.51-4.25-5.82-5.24-3.84-7.34.35-4.43.79-3.39,2.24-2.67-1.04.27v-3.6l1.27-4.71-1.22-3.2-2.56-1.92-2.28-2.16-3.6-2.4v-6l2.4-2.4,1.2-1.2,1-3.37.2-5.02,4.8-2.4h3.6l1.01-2.89.35-1.46.82-.82.47.64,2.27-1.16,1.08-3.92,1.2-1.2,5.41.22,3.44.82.99,1.34,2.8-.17v-1.81l.47-1.34,1.46-.47.82-1.34v-1.34l2.27-2.97,1.56-2.54,2.4-3.6,3.6,1.2h3.6l1.2,1.2,3.31-.63,2.62-.64,3.96-5.59,2.1-6.34,3.6-3.6,7.2-6,4.8,2.4,1.2,1.2,2.48-.19,3.52-1.01,1.61-1.61,2.62-1.46.57-1.73,2.4-3.6,1.2-3.6,2.4-2.4h4.8l3.6-1.37,2.33-1.63,1.51.17,1.4,1.16.47,1.75,2.21.41.12,1.57-1.86.12-1.4.99-.06,1.34,1.05.41v.7l-.76.76-2.1.35-.82,1.4,1.51.93,1.51-1.16,2.27-.35,2.04,1.34,1.57.41.06,1.16-.7.93-2.04.23-1.51-.82-2.5,1.46-.58,1.57.82,1.22,2.27-.12.64,1.16-.64.47-.35,1.75,1.4,1.16,2.85-.12.64.99-.47,1.69-2.27.29-.47,1.51.7.82,2.91.64.93,1.57-1.92,1.22-.58,1.57,2.04,1.69-.41,2.45-1.86.93.12,2.74-.93.82-1.57-.47-1.05.23-.58,1.75,1.46,1.69,1.57.23.47-1.05.99-.99,1.05.47,1.75.12,1.46-1.51,2.21-1.22,1.22.58.41,1.57-.29,1.34,1.57,1.46.29.7-1.22,1.11.12.7,1.16.76v.82l-.58.82.41,3.73-1.69,1.05-1.4-.76-1.57-.06-.06,1.05,1.05,1.05,1.22-.12,1.69,1.22.99,2.5.58,1.75,2.16.29,1.28-1.46h2.21l1.98,1.81,2.8.12,4.89-1.4,1.51,1.4,4.19.29,4.78,3.03,3.79-.49,2.4,2.4,3.95,1.3,3.25,1.1-.46,3.03,2.1.17,1.96.39.2,4.38,3.4,1.61.91.95,2.39-2.04,3.15-.23,3.32-2.68,2.24.4,1.72.47,4.27,1.93,2.02.75.52,2.85-.14,2.39.49,2.5-.49,3.5.9,1.57,7.05,8.91,15.49,12.52.87,2.33-3.15,3.38-.35,2.33-2.39,4.02v4.37l-3.73,6,1.05,2.74-3.44,4.14-3.67,1.98-2.56,3.38-1.46,4.49.76,3.26-.06,5.13-2.16,1.57-.35,2.16-3.15,1.22-7.16-2.5-.76,4.02-1.51,2.04-2.91-3.09-1.28,3.03-3.49,2.5-4.25,1.57-2.74-.17-.58,3.2-5.88,5.59-5.01,1.28-5.18,4.19-3.26,1.22-1.28,2.33-3.09-.23-2.74,1.81-2.16-.06-2.56,2.1-5.53.23-3.15.64-1.63,1.51-1.69,2.45-4.37-1.98-2.8.17-2.04-2.5-2.04.06-.58-1.57-1.69-1.16-1.4-.52-2.39,1.57-.82-.76-.47-1.86-.52-2.04-1.11-.82-1.34-1.16-1.4-1.69v2.68l-4.66,4.89-5.65,3.79.41,2.39-1.81.47-.93-.82-1.22.23-.47,1.46.7,1.92-7.81-.35-1.11-1.92-.58.58-.47,2.5-1.46.93,2.85,1.34v1.81l-1.57.23-3.09,1.57-.35,4.31,4.66,6.58,2.39,1.05.58-.82,1.57,1.22.23,4.31-1.22,1.22-.23,3.2-1.92,2.62.93,1.46.12,1.92-1.57.23.35,2.85-.47,1.22.23,1.05-2.85.35-1.92,3.26-3.73,1.92-2.39-.47-1.92-6.58-3.38-1.05-1.57,1.22-2.5-1.69-1.11-6.93-2.16-2.16-1.46-.12-1.92-1.11-3.2.12-2.04-1.81h-.7l-3.61,3.26-1.46,2.04h-2.74l-.58-.35-1.11.23-1.46,1.46h-.82l-1.34-1.46-2.39.12-.35-.35-4.19-2.27-1.11.82-1.46-.35-2.27,1.57-1.92-.7-3.09-.47-.82-1.92-1.46-.23-.82.58-2.39-.12-2.27-2.74h-3.49l-.23-1.22-1.11-1.05v-1.81l.7-.93-1.57-3.38-.82-.58v-3.09l-1.05-.35.47-2.74.58-.58.7-2.5-1.34-1.92-2.5-1.05-.23-1.05,1.57-.35.93-1.57.12-2.04,1.11-2.39,1.22-.47.35-2.39-1.22-1.4-5.59.29-.52-2.21-1.51.29-1.11-1.92-1.69.52-4.19-4.19-7.16-2.1-6.87-7.22h0l-5.13-5.13,4.49-4.72Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(111.36 613.34)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">CIMITARRA</tspan>
			</text>
		</g>

		<g id="BARRANCABERMEJA">
			<path id="path1221"
				d="m196.52,489.59l.42-3.28,2.91-5.98-.2-3.61,3.29-3.6,3.31-5.29,1.36-1.65,3.24-.77,1.69-3.09,3.6-3.6,3.6-3.6,4.86-2.22,2.65-2.83,1.95-1.36,2.54-3.19,6-4.8,11.75-5.28,2.65-1.92,2.4-1.2,2.92-2.19,4.84-3.3.65-1.71,1.71-1.3,2.65-2.65,1.63-3.24,1.2-3.6,2.12-4.24-2.3-4.48-.83-6.66-.35-5.49-3.44-3.12-1.22-2.37-4.31-2.83-.47-4.31-.65-2.83-2.01-2.3v-5.01l.83-1.47-1.77-4.07-1.2-3.6,1.2-4.8v-2.4l-1.29-4.95-2.31-5.85,2.4-6,8.11,12.5,3.95,1.24,6.55-.71,3.24-3.83,3.54-3.36,11.26-7.08,5.25-.83,1.12,1.71-3.13,3.36v2.01l2.24.53,3.72-2.95,2.01-3.07.29-4.36,3.95-.71h3.13l2.12,1.24,1.83-.88,1.71.59,2.12,3.66,2.71-.18,4.31-2.95,4.84-5.07,3.95.53,3.83-1.12,3.13,3.54,2.71-.18,2.01,2.65,2.12.59v3.07l1.12,1h2.54l3.24,2.95,2.01-.88,1.83,2.65,1.89-.59,1.12.83.71,2.95,2.24-.53,1.71,1.24,1.59,2.54-.71,1.71,3.01,1.65,1.71-.71,3.72,1.83,2.12-.18,2.6-1.53.91,4.34,2.39,1.32,4.25,2.24,4.42,3.36h0l-56.62,74.43,2.24,2.3,1.59.18,1.18,2.71,2.71,2.36,2.6,1.3v1.83l3.54,1.65-2.54,1.12-.18,1.42-2.95.18,1.12,2.01-1.83,1.42-2.83,3.13-2.12-1.71-1.12.29-.88-2.12-1.59,1.83-.83-2.71-2.24.29-2.71-1.42-2.95.18-1.18-1.12-2.01.59.53,1.95-1.53.83.41,1.3-1,1.24,2.3,2.42-1.71,1.83.18,2.71-1.83-.12-1.83,3.13-3.6.12-2.12,1.3-3.95-4.36-1.12-2.83-4.31.41-2.24-2.65-1.71-.59,1.59-3.36-2.6-.12-3.13,2.24-2.01-1.12-1.12,1.24-1.59-.83-.59,1.24-2.83-.12-1.18-2.01,2.24-3.24-1.53-2.01.53-2.12-2.24-3.42-2.01-1.42v-2.65l1.12-2.42-2.01-2.42-1.89.18-1.3-1.83-1.42.59-3.01-2.54-.71-2.24-2.6.12-.29,2.36-1.3.41,1.12,2.24-.29,1.95h-3.3l-2.54,1.24-.53,2.65-1.59,2.01-2.3.29-3.72,3.24-.53,1.71-2.54-1.3-.88,1.12.41,1.71-1.53,1.53-2.83-.18-2.83,3.13,3.42,2.42-12.5,8.61-8.67,4.78-4.72.29-1.71,1.53-1.12,3.42-1.89-.88-1.71-.59-.71,1.53,1.53,1.53-3.54-.29,1.12,3.95,1.92-1.11v1.2l-2.75,2.22-2.12,2.12-3.01.18-.29,1.95,1.89,1.53-3.01,1-2.24-1.95,2.01-2.71-2.01-.53-3.13,2.83-5.13,1.18.18-.94Z"
				fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path><text transform="translate(264.48 382.8)"
				font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">BARRANCABERMEJA</tspan>
			</text>
		</g>

		<g id="EL_PLAYON">
			<g id="Layer_2-39">
				<g id="svg2-39">
					<path id="path1227"
						d="m474.76,228.5l.3,1.66-4.09,1.24-3.61,2.9-1.18,2.9.89,1.24-2.13,1.54-.59,2.01,1.42,2.19,2.31.89,1.13,1.3.12,1.42,1.13.12,2.43,2.96.3,1.24,2.13,1.84.3,1.66-1.18,1.13h-2.25l-1.54,1.42v2.84l1.3,1.54,1.3-.12.59,2.25h2.84l2.43.71.89-1.01,2.31-.3,1.18-.71,2.61-.18.89.83,2.01.12,1.3,1.01,1.18-.18.41-1.42,2.25-3.43,1.3-1.24-.41-1.72,2.43-1.54,3.02.3,3.61,3.97,1.6-.89,2.13.3,1.72,1.3,1.54,5.51,1.6,4.68,1.72.18,1.18-.83,1.84.71,1.3-.41.18-3.14,1.18-1.95,1.84.41,1.42-1.72,1.54.41.83-1.01,1.84.41.53-1.13,1.72.18,2.72-.59,1.42-1.3,2.31-1.01.53-1.66,1.18-.18,2.31-4.2,1.72-.89,1.6-2.01,2.25-1.01,2.31-.18,1.84,1.13.89,1.01.02.35v1.2l1.2,1.2h1.2l1.2,3.6h8.4l.89-5.34-1.01-1.24-1.89-5.27-2.25-.12-2.13-1.54-5.6-.88,1.2-1.2,1.09-1.41,2.55-4.26-.53-1.72-3.97,1.24-.3-2.13-3.14-3.97-1.89-.53-.3-1.54,2.43-2.84c.77-2.78,1.34-3.48,4.06-4.43l1.68-2.62,1.92-2.18,1.69-2.2,2.43-3.85.89-2.84,1.07-1.24-1.66-.12-.53-1.07-2.96-.65-1.07.83-.71-1.54-3.67-.12-.83,1.07-4.2-.24-2.43-1.89.65-.95-2.01-1.54-.24,1.48-2.61,2.07-2.31.24-1.07.53-.83,2.19-1.66-1.95.65-.41-2.43-1.54h-3.08l-3.91-1.13-.83.24-2.19-.65.24-1.36-3.2-1.48-1.07.53-.12.83-3.61,2.31v-1.48l-3.49-.53-.71-1.66-3.26-.3-.83.65.41.83-1.18.71-1.66-1.07-.3-1.89-2.31-.95-1.48.53-.71,2.19-3.08,1.66-.53,1.54.12.83-1.89,4.38-3.14,2.07-.3.71.65.41-.3,1.13-2.13.24-1.07,1.89.71,1.13,2.19,4.26,1.24.3.3,1.78.95.12.3.95-2.55,2.07-.83,3.55.83,2.07-.83,1.48-1.07-.41-.3,1.36-1.24.3-2.01-1.48.65-.53-.71-1.89h-1.89l-.3.71-2.43.41,1.07-1.24-1.24-2.61-2.61-.41-.65,1.24-1.07.24-.65,1.3Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(482.7 242.55)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">EL PLAYÓN</tspan>
			</text>
		</g>

		<g id="SAN_JOAQUIN">
			<path id="path1125"
				d="m638.45,570.29l-2.65.56-2.24-.4-2.53,2.07.17,2.64-2.47,2.64-.17,2.18-1.55,1.38-.29,3.56-1.09,3.05-1.95-.69-1.84.57-3.62,2.99,2.18,2.59,2.47.4-.11,2.07-1.15,1.49.17,1.67,1.38.57,1.55.52v2.36l-1.09,3.56.63,2.53-2.18,2.47-2.64,2.59-.4,1.95-6.49,6.15-1.26,1.95-.52,3.16.29,3.05.57,1.67-.8,1.09.17,2.36,1.09,1.95,1.78-.4,2.24,1.49,4.31-4.42,3.62-.17,3.33-1.21,1.55.4,1.95,1.26,2.64.29,2.93-1.78,4.54-4.94,2.87.52.57-.86-1.09-1.9,1.15-1.21v-2.3l-.98-1.78.98-.98.11-1.95-1.15-2.07.86-2.36-.4-2.99v-4.77l-.52-2.59.86-2.59-.69-1.21.86-3.05,1.78-1.9-1.09-1.61.57-1.67-.29-1.26-1.26-.98-.86-1.67.69-.86-1.09-1.38.4-1.38-.98-1.78.92-2.3-3.1-2.36-1.44-3.28-.22-2.74Z"
				fill="#fff200" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(634.65 629.27) rotate(-88.76)" font-family="IBM Plex Sans" font-size="7.54"
				font-weight="500">
				<tspan x="0" y="0">SAN J</tspan>
				<tspan x="21" y="0" letter-spacing="-.01em">O</tspan>
				<tspan x="26.13" y="0">AQUÍN</tspan>
			</text>
		</g>

		<g id="BUCARAMANGA">
			<g id="Layer_2-40">
				<g id="svg2-40">
					<path id="path1117"
						d="m523.28,342.55l.89,2.96.77.41-.3,2.31.65.53-.41,2.19,1.06,3.08-.59,1.54.41,1.66,1.3,3.25h1.12l-.18,2.96-3.85,4.56.06,2.37-.83,2.37-1.42,2.84.18,2.01-1.36,2.19-.06,1.77-2.37,2.48,2.84,2.72,3.96,1.12.53,2.37,2.9.65,2.72,1.72,2.07-1.3,2.25.24-.71,1.89.3,1.66,1.06.65,1.6-1.66.89-2.37,2.96-3.79.41-1.12,2.13-.89,4.44-4.79,1.18.41.12-2.43,1.72-2.54,2.54.3,1.72,1.66,2.6-.3.12-3.08,1.83-1.01-.3-1.3,1.3-1.12,3.25-1.3-3.25-2.37-3.61-.3-2.13-1.01-.41-4.2.12-3.55,1.89-2.54,1.18-2.96-1.83-.3-3.14-1.42-.18-2.01,1.12-2.01-.95-1.24h-1.18l-4.5-3.14-.18-1.48.89-1.83,1.48-1.54.65-1.83-.3-2.78.65-1.24-.12-3.25-1.12-1.83-1.66-.24-2.13.59-4.97,4.91v2.01l-1.54,2.66-2.6-.12-.89,2.01.3,1.54-3.14,2.66h-1.83l-.89,2.13-1.72-.3-3.61-2.37-1.95-.53Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(526.98 372.21)" font-family="IBM Plex Sans" font-size="4.39"
				font-weight="500">
				<tspan x="0" y="0">BUCARAMANGA</tspan>
			</text>
		</g>

		<g id="SANTA_HELENA_DEL_OPON">
			<g id="Layer_2-41">
				<g id="svg2-41">
					<path id="path1087"
						d="m370.51,656.61l-1.36.41-1.42-.89-2.48,2.07-1.89.47-.89-.83-4.07,2.3-1.89-.65v1.3l-.83.59.47,1.71-.83.06.12,2.54.18,2.54-2.24-.3-1.18.77-2.6-.18-1.12,2.42-.18-1.89-1.3-2.36-.41-2.83,1.36-1.71-1.83-1.06-1.54-.41-.53-1.54.83-2.66,1.3-3.13,1.89-2.24v-2.72l-1.83-2.42-.12-2.95,1.3-2.36-1.18-5.67-1.42-.41-2.01-1.3,1.71-1.12,1.89.3.71-5.67-1.18-2.42.3-2.72,1.71-2.42,1.54-.18.18-1.71,4.31-5.08-1.59-2.83,1.83-.71-2.83-1.42-.53-3.07-2.6-2.72-2.95.18-3.72-1.24.59-1.65,2.54-2.24,1.3,1.65,5.67.41,3.43-5.2-.3-2.36,4.25-4.37.53-2.13,3.96,1.83,2.83,2.36,2.3-3.25,4.25-.3,6.02,2.24,4.43,3.66-1.24,3.25.3,2.54,4.19,3.96,2.13-.89,3.6.35,3.6,1.36.71,1.83,2.72.41,2.13,1.71.71,1.77,3.07.83,3.37,2.36,3.84.06,4.61-2.24-1.95,4.43-.53,3.9-2.3,5.08-1.06,11.28,1.48,2.89-3.19.77-2.42,1.95-4.31-1.59-2.18.59-2.01,1.06-1.48,1.71-.06,3.01-1.18,2.6-1.24,2.01-.3,2.54-1.24,1.95-1.06.53-.06,1.83-2.6,2.36-.53,1.71-2.13,1.83-1.95.89-1.3-.89-1.77-.06-1.54-1.18.06-1.71-2.01-.35-1.18-1.59-1,.12-.71-.89-2.42.83-.83.83-1.59-.24Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(354.76 624.89) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.5"
				font-weight="500">
				<tspan x="0" y="0">SAN</tspan>
				<tspan x="12.87" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="16.43" y="0">A HELENA</tspan>
				<tspan x="8" y="7.8">DEL OPÓN</tspan>
			</text>
		</g>

		<g id="VELEZ">
			<g id="Layer_2-42">
				<g id="svg2-42">
					<path id="path1089"
						d="m327.72,775.45h0l.93.58,2.8,1.23.64-.76,1.52,1.46,4.03-.18,1.11-.12.82,1.11.12,2.51,3.1,3.91.47,2.1,1.4.35-.14,2.19.78,2.36-.58,1.46,2.74,1.58.12-2.1,1.11-1.4,1.17-3.27,1.28-1.87,2.74-7.07,1.28-2.04,1.11-.53,2.1-2.34,2.1-.7,1.11-1.64,1.81-1.46-.53-2.1.7-.99,1.34-.18,2.39-2.1,2.34-.64.99-1.99-1.87-2.16-1.69-3.15-2.8,1.34-2.98,2.8-3.04.93-3.1-.18-5.66-3.8-3.79.02-3.06-2.35-4.09-.23-1.25-1.02,2.4-2.4v-2.4l3.6-6,2.4-3.6,1.2-1.2,2.41-3.66,2.1-4.73.64-2.57.84-2.23,1.2-2.4,1.58-.68,2.86-3.04-.76-.41-3.27.53-2.34-.41-1.87-.82-1.93-.06-2.86-2.28-1.46-2.04-1.81-.18-1.23-1.81-.82-3.39-.99-2.57.47-.93-.53-4.5.23-2.69,1.28-3.56,1.11-2.45-.58-2.8.41-3.8-.47-2.45-.82-2.22.47-1.46h-2.57l-1.11-2.34-.41-5.14-2.28-.58-3.21-2.51-1.52-4.2-1.11,1.52-2.1-.99,2.51-3.62-.58-2.34,1.52-2.39.29-2.22,1.28-.53,1.28-3.1v-2.39l2.57-2.22-.12-1.81,3.1-2.51.29-1.52,1.4-.18,5.49-6.66-1.99-1.28,1.69-1.11,1.87.29.7-5.61-1.17-2.39.29-2.69,1.69-2.39,1.52-.18.18-1.69,4.26-5.02-1.58-2.8,1.81-.7-2.8-1.4-.53-3.04-2.57-2.69-2.92.18-3.68-1.23-2.28,1.23.99-4.5-2.1-1.23,1.11-2.63-1.81-1.81-1.52.7-3.27-1.28-1.4-1.64-2.28.82-.41-4.03-2.98.58h-4.61l.29-2.63h-2.28l-2.98-2.63-2.98,1.28-2.28.12.29-4.32,1.58-3.5-1.52-.41-1.4,1.93-3.91-1.11-.93,1.17.99,2.1-.12,2.51-1.58,6.95.88,2.51-.88,3.21,1.52,4.03,2.28-.99,1.87-2.22,1.81.41.88,4.61,2.1.7,2.69,6.13,2.57,3.8,3.97,3.21.7,1.52,3.33,2.69,2.22-.12,2.16,2.16,6.48,3.15,1.17,2.51-.18,2.51-2.51,1.93-2.16-1.52-2.39,7.94-2.8.7.58,2.8-.99,2.51-2.51,2.51-2.28,4.91-2.51,1.81-4.09,6.72-.7,4.2-2.16,3.74,2.51.99,2.1,2.69.7-1.93,2.98-.58.41,2.39-1.58,1.58-1.17-.41-2.28,2.69-.29,2.63,2.39,1.11,1.69,1.99v2.8l1.58,3.21-.58,3.1-2.74,2.63-1.18.47-1.2,2.4-1.7,1.69-.69,1.91-2.23.78-1.37,1.62-2.19.3-2.61,4.49,1.2,3.6,3.6,2.4,1.2,2.4,2.4,2.4,2.45-.17,2.92-.23,1.23,2.2v6l-.6,1.8v1.2l-1.2,1.2-1.2,1.2-3.6,2.4v2.4l-.6,3,.6,1.8h1.2l.6.6-.6,3,1.2,1.2v2.4l.6,5.4,1.2,3.6.6,1.8,2.4,2.4,1.8.6-1.2,3.6-.85,1.91-1.23-.29-1.46.53-1.05,1.93-3.45,1.93.64,1.46,4.4,2.74,2.4,1.2,2.4,1.2h1.2Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(324.39 734.71)" font-family="IBM Plex Sans" font-size="6.41"
				font-weight="500">
				<tspan x="0" y="0">VÉLEZ</tspan>
			</text>
		</g>

		<g id="LA_PAZ">
			<path id="path1091"
				d="m354.67,723.66l.11.75,1.49-.11,1.95.52-.11,1.09,1.84,1.21.92-.86,1.38.23,1.49,1.61,1.72.57,1.61,3.44,1.38.75,2.53.52.17,1.66,1.44,3.04,2.47,1.03,2.58-.06,1.21-1.26,1.66-.06-.52-1.38.46-.92,1.78.17-.17-1.21,1.78-1.78.52-1.52,3.18-.81.17-1.63-1.2-1.2-1.2-2.4-1.2-1.2v-1.2l-.1-2.78-1.1-2.01h0l.12-1.14-.06-.86-.07-1.6-1.2-1.2.52-2.77.11-1.15,1.03-.17.73-.71.3-2.05-.06-1.44-1.72-1.84-.17-1.26.45-.61v-3.6l1.2-2.4-1.2-3.6-2-1.96-2.12-.34h-1.55l-.8-3.27-.92-.98.92-2.58-.63-2.53-1.44.06-1.89-1.44-3.62-1.26-1.44-1.55-1.72-.52-2.12-3.27-1.49-.29-1.78-1.66-.98.17-.86-1.61-2.24-1.03-.92,1.15-2.18-.29-1.15.75-2.53-.17-1.09,2.35-.17-1.84-1.26-2.3-.4-2.76,1.32-1.66-1.78-1.03-1.49-.4-.52-1.49.8-2.58,1.26-3.04,1.84-2.18v-2.64l-1.78-2.35-.11-2.87,1.26-2.3-1.15-5.51-1.38-.4-5.4,6.54-1.38.17-.29,1.49-3.04,2.47.11,1.78-2.53,2.18v2.35l-1.26,3.04-1.26.52-.29,2.18-1.49,2.35.57,2.3-2.47,3.56,2.07.98,1.09-1.49,1.49,4.13,3.16,2.47,2.24.57.4,5.05,1.09,2.3h2.53l-.46,1.44.8,2.18.46,2.41-.4,3.73.57,2.76-1.09,2.41-1.15,3.1-.4,3.04.52,4.42-.46.92.98,2.53.8,3.33,1.21,1.78,1.78.17,1.44,2.01,2.81,2.24,1.89.06,1.84.8,2.3.4,3.21-.52.75.4-2.87,3.27Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(346.65 705.03) rotate(1.24)" font-family="IBM Plex Sans" font-size="9.01"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="0em">L</tspan>
				<tspan x="4.96" y="0">A </tspan>
				<tspan x="13.2" y="0" letter-spacing="-.09em">P</tspan>
				<tspan x="18.18" y="0">AZ</tspan>
			</text>
		</g>

		<g id="CHIPATA">
			<path id="path1093"
				d="m375.11,741.63l-1.73,1.55-.43,2.05-1.03.55.76,1.61-2.48,2.88.71,2.38v2.4l-.61.92-4.21,1.38-2.59,1.27-2.75,2.66-2.81.89-2.86-.17-5.24-3.6-3.89.5-3.02-2.38-3.78-.22-1.24-1.33,1.62-2.49.38-1.99,3.51-6.76,3.02-3.05.49-1.05,2.75-2.88,1.94-4.49.59-2.44.86-1,1.03-4.38,1.46.33.11.72,1.4-.11,1.84.5-.11,1.05,1.73,1.16.86-.83,1.3.22,1.4,1.55,1.62.55,1.51,3.32,1.3.72,2.38.5.16,1.61,1.35,2.94,2.32,1,.38,1.94Z"
				fill="#fff200" stroke="#000" stroke-miterlimit="10"></path><text transform="translate(345.87 746.58)"
				font-family="IBM Plex Sans" font-size="5.99" font-weight="500">
				<tspan x="0" y="0">CHI</tspan>
				<tspan x="9.86" y="0" letter-spacing="-.09em">P</tspan>
				<tspan x="13.17" y="0" letter-spacing="-.06em">A</tspan>
				<tspan x="16.81" y="0">TÁ</tspan>
			</text>
		</g>

		<g id="GUAVATA">
			<g id="Layer_2-43">
				<g id="svg2-43">
					<path id="path1097"
						d="m315.37,807.24l3.03-.42,4.06-4.79,2.06.61,1.64-2.43,1.76-.3,2.61,5.52,3.33.12,3.03-4.37,5.4-3.94,1.64.18,2.85-2.12-2.97-1.58.61-1.52.55-3.15-1.21-1.58-1.46-.36-.49-2.18-3.21-4.06-.12-2.61-.85-1.15-1.15.12-4.18.18-1.58-1.52-.67.79-2.91-1.27.12-2.24-1.58-1.03-1.09.42-1.7-.91-2-1.52-5.03-.97-.67-1.52-2.24.24-.18,3.94-.85,2,.73,2.79.12,2.61,2.3,4.37-2.12,1.76-1.27,2.91-2.06,1.58,1.64,4.37.12,2.43,1.82.67.18,1.52-.42,2.61,1.03,2.61,1.39.79v2.43Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(312.51 793.5)" font-family="IBM Plex Sans" font-size="6.41"
				font-weight="500">
				<tspan x="0" y="0">G</tspan>
				<tspan x="4.36" y="0" letter-spacing="-.01em">U</tspan>
				<tspan x="8.47" y="0" letter-spacing="-.04em">A</tspan>
				<tspan x="12.5" y="0" letter-spacing="-.04em">V</tspan>
				<tspan x="16.41" y="0" letter-spacing="-.06em">AT</tspan>
				<tspan x="23.81" y="0">A</tspan>
			</text>
		</g>

		<g id="AGUADA">
			<g id="Layer_2-44">
				<g id="svg2-44">
					<path id="path1099"
						d="m389.03,682.52l2.03.78,1.2,1.14.96,1.85-.36,2.09.42,2.63,1.79,2.27,3.59,2.09,2.27.06,3.17,1.43,2.33,4.18,2.09,1.49.18,2.03-.6,2.49.57,1.52.48,2.45-1.05.83-1.2,1.2-1.72.48-1.88.72-1.2,1.2v1.2l-2.4,1.2h-1.2l-.26-.02-.94,1.22-1.2,2.4-1.2,1.2v1.2h-1.2l-1.2,2.4v1.2l-2.27,2.34-2.33,1.61-1.26-1.73-1.08-2.03v-1.43l-2.15-1.91-.12-1.14,1.37-2.93-1.85-1.55-.42-1.32,1.26-.42-.06-.9-1.85-1.73-.24-1.08,1.32-2.99.12-1.2,1.08-.18.3-1.85.78-1.02-.06-1.49-1.79-1.91-.18-1.32-1.08-.84-.24-3.05,1.32-2.99-.12-3.83-1.49-1.97-2.21-.36h-1.61l-.84-3.41-.96-1.02.96-2.69.13-.48,2.03-.06,2.27.78,1.32.9,1.43,2.15,1.49-.9,1.43-2.27,2.15-.6v1.85Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(385.5 708.6) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.93"
				font-weight="500">
				<tspan x="0" y="0">AG</tspan>
				<tspan x="6.64" y="0" letter-spacing="-.01em">U</tspan>
				<tspan x="9.8" y="0">ADA</tspan>
			</text>
		</g>

		<g id="SAN_BENITO-2">
			<g id="Layer_2-45">
				<g id="svg2-45">
					<path id="path1167"
						d="m505.88,652.47l1.32-3.47-1.02-3.83,1.02-4.43,2.75-3.29.54-2.57-1.2-1.73.6-1.97-1.32-3.17-2.33-1.5-.42-1.44,1.38-3.35-.18-2.63,1.44-1.91,2.81-2.15-.9-2.69,1.44-3.23,1.73-1.2,2.21-.06,1.32-2.75,3.29-.9,2.39-.06,1.02-2.39,2.99-4.19.24-2.81,3.47.18,2.27-2.03,1.73-.42,2.45.72.84,2.99,1.61,2.69-3.05,2.15-1.2,1.73-4.31,2.27-1.73,2.75-1.91,1.32-2.57,5.56-.12,5.14-2.27,4.72.72,2.57.24,3.41-.6,2.51-1.02,1.08.36,2.39-.42,3.35.9,2.21-.42,2.51-2.33,2.57-4.31.36-4.84,4.37-1.61-1.67-1.14,2.27h-1.85Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(517.87 638.61) rotate(-86.51)" font-family="IBM Plex Sans" font-size="4.6"
				font-weight="500">
				<tspan x="0" y="0">SAN BENI</tspan>
				<tspan x="20.32" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="23.05" y="0">O</tspan>
			</text>
		</g>

		<g id="PALMAS_DEL_SOCORRO">
			<g id="Layer_2-46">
				<g id="svg2-46">
					<path id="path1169"
						d="m498.51,625l.17,3.17-2.19,3.17.12,2.36-3.46,4.55-3.51.98-1.38-.58-1.96,1.09-2.53-.12-1.84-.86-1.96,1.09-2.07-.12-1.15-.69-1.96,1.27.86,3.69-2.3,4.15-1.15-1.67-1.15-3.28-2.07-1.73-1.73,1.32-4.32-3.28.58-2.42.58-1.79-.69-1.79,1.38-2.07,1.67-1.09.12-1.9,1.5-2.36.12-2.42,5.59-2.94,3.46-1.27,5.01.52,2.25,1.5,2.65-.58,2.65.86,3.92,2.36h3.05s1.79.86,1.79.86Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(468.91 630.26) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.03"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="-.09em">P</tspan>
				<tspan x="2.22" y="0">PALMAS DEL</tspan>
				<tspan x="2.69" y="4.83">SOCORRO</tspan>
			</text>
		</g>

		<g id="COFINES">
			<g id="Layer_2-47">
				<g id="svg2-47">
					<path id="path1171"
						d="m499.01,625.76l.17,3.06-2.19,3.17.12,2.36-3.46,4.56-3.52.98-1.38-.58-1.96,1.1-2.54-.12-1.85-.86-1.96,1.1-2.08-.12-1.15-.69-1.96,1.27.86,3.69-2.31,4.15,1.96,1.61,1.73.17,1.5,1.73h3l3.06,2.36,2.48.52,1.85,1.96,2.19.69,2.94,3h2.94l.52-1.67,2.19-1.61,3.23.17,2.36-2.48.29-2.31,1.27-3.34-.98-3.69.98-4.27,2.65-3.17.52-2.48-1.15-1.67.58-1.9-1.27-3.06-2.25-1.44-1.85-.23.12-1.5.58-1.38-3.63-.4-1.56-.75-1.04,2.08Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(481.15 646.94) rotate(1.24)" font-family="IBM Plex Sans" font-size="4.6"
				font-weight="500">
				<tspan x="0" y="0">CONFINES</tspan>
			</text>
		</g>

		<g id="PALMAR">
			<g id="Layer_2-48">
				<g id="svg2-48">
					<path id="path1173"
						d="m476.09,598.43l1.48.06,3.26-3.03,1.26-2.57,1.83-2.63-.29-3.66s.4-2.97.4-3.43-.51-3.14-.51-3.14l-2.17-3.94-.11-5.31-2.06.97-1.71-.69-3.26-.17.57,2.46-.06,3.14.57,1.83.17,1.2.23,2.63,1.03,4.17.23,2.46-.74,1.31-2.06,1.03-.8,1.03-1.88.69,1.03,2.23v1.14l1.54,1.6,2.06.63Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(480.93 592.45) rotate(-88.77)" font-family="IBM Plex Sans" font-size="3.65"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="-.09em">P</tspan>
				<tspan x="2.02" y="0">ALMAR</tspan>
			</text>
		</g>

		<g id="SAN_ANDRES">
			<path id="path1175"
				d="m664.47,476.97l.42,4.48,1.15,1.88v2.48l.54,2.42.54,3.87-1.57,2.3-.42,3.33.42,3.63.91,3.45-5.87,6.23-1.45,2.72-2.7-1.07h-4.8l-3.7.52-3.93,4.06-1.96,1.42-3.3,1.49-.42,3.15-1.57,4.6-2.78.42-1.21-1.76-1.33-.3-2.66,2h-3.51l-3.63-3.51-2.36-.09-2.3.63-.3,1.33-2.3.73-2.29,4.51h-2.4l1.43-8.08,1.15-2,.91-2.72,3.69-.3s2.36-.54,2.78-.61,1.88-1.69,1.88-1.69l-.18-2.72-5.39-7.51-.18-4.9-.61-1.15,2.3-2.48.61-3.51-.54-3.03.91-3.93.54-4.06-.85-3.63-.91-2.6.42-2.91,1.45-1.69,2.06-.73,1.88,1.03,1.88-.12,2.6,1.03,1.63.18,2.66,2.18,1.21-.12.42-2.42,2.18-.18,1.57-1.88.18-2.48,1.88-2.6.18-4.66.85-1.03.18-2.18,2.91-3.51,1.88-.61,4.24-3.87,2.36.12,2.3-.85,1.57.3,2.36-1.15,2.36.42,2.91-.73,2.78-3.51.3-2.48,1.76-.85.28-4.56v-2.4l1.2-1.2h1.2l1.2,1.2v2.4l.3.02,1.15,3.33-1.15,2.48-1.45,2,.85,4.18-.54,3.51.85,2-.12,3.45-1.07,3.03.95,5.2-.18,1.57-3.17,2.82-2.52,2.93s-1.45,3.33-1.45,3.33Z"
				fill="#fff200" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(631.79 486.87) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.63"
				font-weight="500">
				<tspan x="0" y="0">SAN</tspan>
				<tspan x="-7.95" y="10.35">ANDRES</tspan>
			</text>
		</g>

		<g id="CEPITA">
			<path id="path1177"
				d="m610.64,531.3l-4.49-1.59-1.88-2.22-2.73-1.08v-4.78l-4.55-3.75-3.64-1.14-2.67-2.84-1.02-3.07,1.14-3.58-1.99-1.88-2.62-.34.11-3.01,1.76-2.62-3.13-2.67-.34-3.75.97-1.65,4.55.85,3.87-.45,1.42-3.3,1.54-.63.91-3.98,3.13,2.79,5.4.4,2.62,2.84,1.36.11,2.84-3.81,1.08-.11,2.05-3.13,1.54-.28.8,3.41-.51,3.81-.85,3.7.51,2.84-.57,3.3-2.16,2.33.57,1.08.17,4.61,5.06,7.05.17,2.56-1.76,1.59-2.62.57-3.47.28-.85,2.56-1.08,1.88.57,3.13-1.19,3.98Z"
				fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(592.42 506.87) rotate(1.24)" font-family="IBM Plex Sans" font-size="6.38"
				font-weight="500">
				<tspan x="0" y="0">CEPI</tspan>
				<tspan x="13.64" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="17.14" y="0" letter-spacing="0em">A</tspan>
			</text>
		</g>

		<g id="SANTA_BARBARA">
			<path
				d="m621.22,389.98l.89-1.89,1.38-1.53,2.36-5.23,8.29-9.74,1.31,2.54,1.2,2.4s1.2,2.4,0,4.8,0,3.6,3.6,3.6c1.2,0,3.6,1.2,3.6,1.2l-2.4,6,2.4,1.2-2.41,4.88-2.6-.49-1.66.7-1.13.31-1.2-3.6-1.2,4.8,1.36,3.35-2.11,3.59-4.13,2.03.08,3.02v3.6l1.22,4.53-4.41,7.01-2.08,5.19,1.51,1.63-.28,5.78-2.08,3.14-2.28,1.51v6l-2.33,3.38-2.14.69-2.73,3.14-3.97-.21-1.08,2.39-.98,2.04-4.79,3.34-2.23-.46-1.67,1.67-1.72-.41.41-1.95,1.3-1.95-2.55-2.55-3.14-1.9,2.93-6.26-.97-1.02-3.15.08-1.52-1.62,1.2-1.54-.39-1.46.44-1.74-3.32,1.56s-1.86-4.14-6.93-3.87c.66-.66,3.33-3.33,4.53-9.33,1.2,1.2,1.93.68,1.93.68l2.04,2.72,4.12.02,3.56-2.47,1.45.48,2.76-6.84,5.03-8.05-.48-4.52,2.62-4.18-.18-5.39.83-2.84-1.28-2.79,2.56-4.32.85-2.9-.48-1.21h3.52l6.73-.71Z"
				fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path><text
				transform="translate(605.99 450.22) rotate(-66.31)" font-family="IBM Plex Sans" font-size="7.38"
				font-weight="500">
				<tspan x="0" y="0">SAN</tspan>
				<tspan x="14.6" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="18.64" y="0">A BÁRBARA</tspan>
			</text>
		</g>

		<g id="MALAGA">
			<g id="Layer_2-49">
				<g id="svg2-49">
					<path id="path1179"
						d="m680.9,516.02l-.53,4,.69,1.44-.59,1.87.69,2.35-.59,2.78.27,2.19-2.67.69-3.2-.85-2.4-1.71.43-1.87-.27-1.44-5.29,1.76-1.41,1.07h-1.2l-1.66-.43-3.14.43-.07-4.81.27-4.75-1.07-3.79,1.28-2.4,5.18-5.5-.8-3.04-.37-3.2.37-2.94,1.39-2.03,1.07,1.17.21,1.19,1.79.73-.18,1.23,1.55,1.82,1.66.53,5.18,3.95,4.75,4.7-1.44,2.67.11,2.19Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(663.01 517.25) rotate(1.24)" font-family="IBM Plex Sans" font-size="3.8"
				font-weight="500">
				<tspan x="0" y="0">MA</tspan>
				<tspan x="5.86" y="0" letter-spacing="0em">L</tspan>
				<tspan x="7.95" y="0">AGA</tspan>
			</text>
		</g>

		<g id="LEBRIJA">
			<g id="Layer_2-50">
				<g id="svg2-50">
					<path id="path1101"
						d="m522.27,343.73l.83,6.11,2.37,2.97-1.37,3.32.12,3.32.89,2.79-3.09,3.74-3.98,3.44-2.02,1.07,1.19,3.68v4.1l-1.31,3.44-3.26,6.06-1.37,3.09.18,3.44-1.37,1.25,1.01,2.37-.83,1.42.59,1.66-.65,1.01.65,1.42-2.26,1.25-1.84-.18-2.26-.53-2.49,1.31-2.43-.3-1.6-2.37-2.61-1.25-3.62-.42-.65-2.26-1.54-1.96-.12-2.32-.89-.59-.18-3.62-1.42.06v-1.84l-4.45-2.73-4.75-5.11-2.14.12.59-2.37-2.91-2.14-2.73.12-1.96,1.25-.18,2.43-3.15-2.14-2.91-.53-3.5-2.02-2.02-2.37-.18-2.91-.95-.83-3.74-.59-2.26-1.42-2.43-.06-1.37-1.25-2.85-1.42h-2.91l-1.84,1.25-1.42-1.31-1.6-2.43-2.37,3.98-3.09-1.25.89-1.84-1.66-1.13.53-2.14-1.54-2.02.12-1.25-1.48-1.31,1.31-3.38,3.26-2.43,3.32-.83,1.6-1.72,3.32-1.31,15.02-25.82-1.6-4.57-.18-3.15-3.26-4.21-.18-4.57,1.9-3.26-1.31-2.26.53-2.02-2.02-1.96,1.19-1.13-1.31-2.26.71-3.98-2.14,1.31-2.14-2.37,2.61-4.57,1.9-1.13-6.06-5.22,2.26-1.66,1.42.42,1.84-.83.59-1.13-.42-1.31.71-1.01,1.42-.42,2.85.3.71-1.72,1.6,1.01,1.01,2.67-.3,3.98.42,2.67,1.42,1.01.71,2.37-.53,2.43-1.54,1.96-.18,1.84.89,2.14.3,2.73,1.6,1.01-.12,1.31-1.54,1.31-.71,1.72.71,1.66,1.84.89,4.16.18,1.54-2.37,2.97-2.97,2.55-.89.18-2.14,1.42-.12.3,2.02,2.26-.18,1.31,1.31,2.02-.3,1.01,1.25-.3,1.66-1.84.89.3,1.54,2.14.83,1.13,1.42.18,3.09,1.19,1.31.12,3.68,1.13,1.25.59,3.15,2.32,2.85,2.43.59.53,1.96,1.84,2.97-.3,2.43.89,3.38-.18,4.57.42,1.54,1.01.59,1.01-.71,1.19.18,2.73,2.14,1.9,2.73,1.42.3,1.54,1.31,2.61-.3,2.61.12,2.73.83,2.32,1.96,1.9.3,1.9,1.25,2.32-1.84,2.61.47h0Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(460.87 356.61)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">LEBRIJA</tspan>
			</text>
		</g>

		<g id="LEBRIJA-2">
			<g id="Layer_2-51">
				<g id="svg2-51">
					<path id="path1103"
						d="m585.1,307.31l-2.17,2.35-3.15,5.09-3.03-.46-2,1.37-2.86.34-4.4,4.29-2.4.34-2-.92-1.37.46-1.89,2.29-1.37,2.63.17,3.43-.74.97,1.32,2.17,1.72,1.26-.29,1.66.51,1.09-1.32,1.83.4,3.89-1.43,1.2-1.89.51-.51,2.52-1.6.92-.92-1.2h-1.14l-4.35-3.03-.17-1.43.86-1.77,1.43-1.49.63-1.77-.29-2.69.63-1.2-.11-3.15-1.14-1.66.97-1.26.86-1.77-.29-2.86-3.15-3.95-2.17-.97-4.58-.11-1.09-.86,1.94-2.35-1.83-2.75.17-4.06.86-1.49v-1.37l-2.23-2.46-.4-1.26-1.94-.69-1.54.57,1.26-3.03-.29-4.12,1.54-1.09.17-1.6,1.49-1.66.4-1.66,1.14-1.26.11-3.32,2.86-2.86,2.35-4.06.4-2.17,2.52-3.66,1.14-3.66-.4-2.46-2.17-2.06-.57-1.77.29-2.17,2.75-2.17,1.2-1.09.34,1.37,1.09.11.51,1.94,1.14,1.26,3.49-.29h3.32l1.49.51,1.26.69,4.12,4.4,1.83.4,2.86,2.57,1.37.11.8,1.77-.11,1.37,1.09.17,1.37,1.37,1.49.8-.97,1.77.11,5.72-1.09,2.17.4,2.35-1.09,2.63-1.49,2.35-1.77,1.37-.17,1.89,3.43,2.86,2.17,3.15-1.14,1.89,1.83-.97.86-1.09,1.49,1.26,1.83.29,1.77,1.66-.69,1.89.57,1.49s-.4,2.4-.4,2.4Z"
						fill="#f99506" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(543.86 295.13)" font-family="IBM Plex Sans" font-size="7" font-weight="500">
				<tspan x="0" y="0">MATANZA</tspan>
			</text>
		</g>

		<g id="CALIFORNIA">
			<g id="Layer_2-52">
				<g id="svg2-52">
					<path id="path1105"
						d="m625.45,286.74l-1.65.4-3.3-1.37-2.22.4.97,1.93-.97,1.59h-1.65l-4.15,3.81-4.27,1.08-2.45,2.16-.4,2.62-1.82.17v1.93l-.97,1.88.57,4.32,2.33.97,2.33,2.33v4.72l-3.47-3.13h-1.08l-1.37-1.48-1.08-.17-2.73-2.85-1.82-.28-2.45-2.73-2.45-2.28h-.97l-3.7-4.89-1.04-1.14v-1.2l3.2-1.58,1.54.57,3.13,1.65h3.47l1.76-1.08,2.33-4.21.97-3.13,2.28-1.76.97-1.37,1.25-1.48,1.25.68.57-1.25,1.76-2.16h1.82l1.54-1.65,1.25.11.57-.8.85.4.68-.93,2.4,1.2.51,2.18,1.89-.98,3.6,3.6,2.4,1.2-2.2,1.99Z"
						fill="#2774f1" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(602.33 295.16) rotate(-36.23)" font-family="IBM Plex Sans" font-size="2.83"
				font-weight="500">
				<tspan x="0" y="0">CALIFORNIA</tspan>
			</text>
		</g>

		<g id="VETAS">
			<g id="Layer_2-53">
				<g id="svg2-53">
					<path id="path1107"
						d="m618.63,325.4l-3.55-3.61-1.8-.56-1.47-2.71-1.24-.28-.56-1.58-1.52-1.63v-4.68l-2.31-2.31-2.31-.96-.56-4.28.96-1.86v-1.92l1.8-.17.39-2.59,2.42-2.14,4.23-1.07,4.11-3.78h1.63l.96-1.58-.96-1.92,2.2-.39,3.27,1.35,1.63-.39.39-1.58,2.42.62,2.87,3.49-.62,2.14,2.76,1.35,1.01-.39,1.24,1.01.17,2.25-1.97,1.52,1.01,10.48-1.47.62,1.52,3.44,2.03.73.51,1.24-3.1,1.86-1.01.06-2.87,2.65-.45,1.52-1.18.56-2.2-.39-2.76,2.31-.62,2.76-1.01.9-4-.11Z"
						fill="#f99506" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(609.16 308.36)" font-family="IBM Plex Sans" font-size="7.3"
				font-weight="500">
				<tspan x="0" y="0">V</tspan>
				<tspan x="4.72" y="0" letter-spacing="0em">E</tspan>
				<tspan x="8.92" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="12.93" y="0">AS</tspan>
			</text>
		</g>

		<g id="CHARTA">
			<g id="Layer_2-54">
				<g id="svg2-54">
					<path id="path1109"
						d="m622.19,326.9l-4.22,2.17-1.58-.53-1.17,2.23-.77.77h-4.8l-4.05-.3-4.4.88-3.93,2.4-2.29.12-1.11.7-1.29-1.11-1.58-.29-2.96-1.19-1.2-2.4-1.94-1.04-4.92,4.04-1.88.41-1.29,1.93-.12,1.64-1.99.7-.7.82-.12,3.22-1.41,4.04-2.7,3.34-1.82.18-1.99,3.05h-1.41l-1.58-2.11-1.29.12-2.29,2.4-1.82-.29-3.11-1.41-.18-1.99,1.11-1.99,1.64-.94.53-2.58,1.93-.53,1.47-1.23-.41-3.99,1.35-1.88-.53-1.11.29-1.7-1.76-1.29-1.35-2.23.76-1-.18-3.52,1.41-2.7,1.93-2.34,1.41-.47,2.05.94,2.46-.35,4.51-4.4,2.93-.35,2.05-1.41,3.11.47,3.22-5.22,2.23-2.4,1.56.34h2.4l2.4,1.2,1.91,1.04.49.16,2.4,1.2h0l1.2,1.2,2.4,2.4h2.4l4,.49,1.58,1.7.59,1.64,1.29.29,1.52,2.81,1.88.59,3.69,3.75h4.04l-.06.88Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(570.21 326.35)" font-family="IBM Plex Sans" font-size="7.3"
				font-weight="500">
				<tspan x="0" y="0">C</tspan>
				<tspan x="4.77" y="0" letter-spacing="0em">H</tspan>
				<tspan x="10.02" y="0">A</tspan>
				<tspan x="14.88" y="0" letter-spacing="-.02em">R</tspan>
				<tspan x="19.25" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="23.26" y="0" letter-spacing="0em">A</tspan>
			</text>
		</g>

		<g id="PIEDECUESTA">
			<g id="Layer_2-55">
				<g id="svg2-55">
					<path id="path1111"
						d="m587.35,445.38l1.72-.12,2.31-1.3-.53,2.13.59,1.3-1.3,1.3,1.42,1.72,3.26-.18,1.18,1.24-3.14,6.04,1.3,1.3,1.84.59,2.55,2.55-1.3,1.95-.41,1.95,1.72.41,1.72-1.72,1.84.71,4.15-2.43,2.25-2.55,1.18,8.82-2.19,4.92-4.09,5.57-.47,3.49-1.6,3.2-3.26-2.9-.95,4.15-1.6.65-1.48,3.43-4.03.47-4.74-.89-.53-6.16-3.73-2.25-3.49-3.61.06-2.55-1.9-.18-.89-1.3-1.13-2.25-1.42-.18-3.43-2.13.12-2.37-1.01-.3-.89,1.24-2.25-.71-1.42-2.43-.41-1.95-2.55-2.37-2.13.12-2.61-2.84-3.43-1.3-1.18.12-1.3-1.01-1.01.12-1.42-1.3.18-1.54-2.01-1.3-2.31-1.84-1.13-1.95,2.61-2.55-1.18-2.43.41-3.26,3.55-1.72-1.54-1.42-2.31-.53-.41-1.54-1.3-.71v-3.43l-.53-.89,1.18-1.95-.83-.41-1.72,1.3-1.54-1.54-.12-2.43,2.31-3.26-1.42-4.8-.12-5.21,2.31-2.01,3.14-.18-.18-2.37,1.18-.71.71-1.84,4.68,4.2,1.72-2.72-.89-2.37,3.02-.18,3.02-.89,1.84-1.66,1.9-.53.12-2.13,2.61-2.37,2.13-3.43,2.72-1.84.83-4.56.12-3.38,1.01-1.13,1.42.3,1.9-1.72,1.9.71h2.13l.12-1.54,1.01-1.42,1.3-1.13,1.3,1.42h1.3l1.42-.89,2.84.12,1.18,1.3,2.25.59,1.9-.71-.83,1.95.83.89-.12,2.96.71,1.66-1.18,1.95,2.55,2.67h2.61l.71-2.37-.83-2.55,3.02-4.5,3.97,2.96,4.68,2.01,1.3,2.55,1.01,2.55-.53,2.37-3.02,5.09,1.42,2.55-.83,2.84.18,5.39-2.72,4.92.59,3.79-5.03,8.05-2.84,7.05-1.42-.71-3.73,2.67-3.61.18-2.31-3.08-2.13-.12-1.9,5.09-2.43,3.67c3.87-.27,6.22,3.73,6.22,3.73Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(545.73 427.21)" font-family="IBM Plex Sans" font-size="7.84"
				font-weight="500">
				<tspan x="0" y="0">PIEDECUES</tspan>
				<tspan x="40.61" y="0" letter-spacing="-.06em">T</tspan>
				<tspan x="44.91" y="0" letter-spacing="0em">A</tspan>
			</text>
		</g>

		<g id="LOS_SANTOS">
			<g id="Layer_2-56">
				<g id="svg2-56">
					<path id="path1113"
						d="m519.19,459.59l4.59-2.73,5.75-1.97,2.96-1.68,1.59-.48,6.25-2.59,1.97-.41,1.1,1.92,4.24,3.08-.17,1.51,1.39,1.28.99-.12,1.28.99,1.16-.12,3.37,1.28,2.55,2.79,2.09-.12,2.5,2.32.41,1.92,1.39,2.38,2.21.7.87-1.22.99.29-.12,2.32,3.37,2.09,1.39.17,1.1,2.21.87,1.28,1.86.17-.06,2.5-1.34,1.51-.12,2.32-1.63,1.39-.87,2.73-2.84,5.22.06,3.02-3.66,2.73-2.09,3.83-2.5.58-2.96,4.01-2.15.64-3.08,4.35-8.71.87-2.73,2.09-2.61-1.68-4.88.81-2.38-.7-4.99-.12-8.01-2.67-1.92-6.44-2.09.17-1.63-2.38h-3.13l-1.04-2.26-.46-5.69.87-4.93,4.76-4.06,1.16-4.93,1.92-.75.46-2.38,1.8-2.26.29-4.82-.75-4.18-.64-5.75Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(516.8 488.84) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="-.03em">L</tspan>
				<tspan x="4.57" y="0" letter-spacing="0em">OS SAN</tspan>
				<tspan x="36.13" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="41.45" y="0" letter-spacing="0em">OS</tspan>
			</text>
		</g>

		<g id="JESUS_MARIA">
			<g id="Layer_2-57">
				<g id="svg2-57">
					<path id="path1095"
						d="m313.6,801.97l.99,2.51,1.35.76.06,2.16,3.92,5.44-2.28.88.12,2.22-3.27,2.11-2.4-.99v3.22l-1.81,1.4h-2.98l-4.5,3.22-2.57.18-1.93,2.98v2.87l-.99,3.04-3.04.41-2.46,3.39-2.75,2.28-1.35,1.99-3.57-1.52.76-2.63-2.75-.53-1.29-1.35-1.87.58.06-1.75-.88-2.05-1.46.12-3.22,2.11-.82-1.17-.12-1.7-.94-1.75.18-1.75.12-1.93-.99-1.52.76-1.05-.29-1.93.52-1.8,2.11-.53,1.65,1.22.35-1.75,2.69-.94,1.87,1.17v1.75l2.05,1.05,1.29,1.64,2.46-.29,1.99-1.05,1.7-1.87,2.28-1.52,1.52-4.04,1.52-1.29,2.87-.82-1.11-3.04-1.99-2.51,3.33-.82,1.29-1.7,2.63-1.52,1.46-1.58.82-1.75,1.64-.94,3.1,1.05,1.99,1.7,2.22-.06Z"
						fill="#62af0a" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(283.17 835.75) rotate(-38.72)" font-family="IBM Plex Sans" font-size="6.41"
				font-weight="500">
				<tspan x="0" y="0">JESÚS MARÍA</tspan>
			</text>
		</g>

		<g id="EL_CARMEN_DE_CHUCURI">
			<g id="Layer_2-58">
				<g id="svg2-58">
					<path id="path1073"
						d="m386.18,589.83l3.17.12,2.11-1.35,2.88.29,1.06-2.58h3.76l4.81,1.23,2.11-2,3.29-.47.29-2.29,1.35-2.11,3.58,3.05,3.29,1.06h5.4l2.41-1.35.47-2-.88-2.58.59-3.05,2.41-2.29-.18-2.58,1.35-2.11-.88-3.82,2.23-3.17,2.41-2v-3.93l2.58-3.35,1.64-.88v-3.64l2.11-2.7,4.81-6.52,1.94-3.93.18-3.17-.88-5.58-.29-6.05,2.99-4.23h-3.93l-2.88-.76-5.11.88-4.34-2.58-3.76-1.06-3.46-2.58-2.7-.18-3.93,2.58-.88-2.29v-3.64l-1.82-5.46,2.11-1.82.47-3.64-1.82-.88-2.41,1.53-3.58.76-3.76-2.29-.76-1.82-6.16-4.23-.47-1.82-2.88,2.11-2.58.76v1.35l-2.11,1.82-2.41-.18,1.06-3.05-1.23-2-1.88-.7,1.12-3.52-7.34,3.11-3.7-2.35-1.7,1-8.22-5.99-1.82-.59-1.53-2.82-3.52.53-.12,1.53-2.11-1-1.53.12-1.17-1.23-1.12,1.23-1.59-.59,5.28,22.72-1,4.46-2.7,2.64h-2.41l-3.93-2.82-4.11-.18-.41,2.35-3.58,2.52-3.58-1.41h-3.52l-2.41-2.82-1.7.29-.88-2.94-2.58-.12-2.11-.41-1.12,2.23-1.59.59,1.12,1.29-1.59,1.29,2.52,2-.53,1.64,1.88,1.64-.18,3.35-1.41,2-1.59-1-1.17.53-.41,3.05,1.59,3.23-1.88,2.11,2.41,2.35-.88,2,1.88,2.64-1,2.23.59,4.05,3.99,1.23-2.11,1.12.59,1.53,2,.41-.53,2-3.7,1.29.41,7.46,5.69,3.35-.88,2,1.82,1.23,4.4-.18.88-2.35,2.11,2.41.29,2.94,5.64.59,1.41,1.41-1.82.82.41,2.23-2.29,4.46,3.41,4.17,2.52-.82,1.53,1.12,2-.82,2.99,1.12,3.23-1.12,4.23,2.35,4.23-3.05,2.99,1.94,2.7-.88,3.29.88,2.29,1.64,2.7.18,2.41,2.52,4.23-.59,1.59,4.93-.18,4.7h0Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(347.03 530.08)" font-family="IBM Plex Sans" font-size="10.52"
				font-weight="500">
				<tspan x="0" y="0">EL CARMEN DE</tspan>
				<tspan x="14.15" y="12.62">CHUCURÍ</tspan>
			</text>
		</g>

		<g id="SAN_VICENTE_DE_CHUCURI">
			<g id="Layer_2-59">
				<g id="svg2-59">
					<path id="path1075"
						d="m446.82,516.78l2.84-1.45.29-2.5-1.51-2.61.81-3.02-1.28-2.61,1.16-5.57,1.8-2.38.81-3.02.17-2.21,2.21-4.82-.12-5.81,1.97-5.69,2.96-2.9.17-4.3,1.28-9.7,1.28-2.79-2.38-4.3-.17-3.08-2.96-1.51-2.38,1.1-2.55-2.21,1.8-1.63.41-2.61-2.21-1.22,1.51-2.67-6.44-.12-1.8-1.92-.52-2.32-2.79-1.8-.12,2.21-2.79,1.28-1.51-1.22-1.68.52-2.96-1.97-3.48,1.63-4.18.7-4.76-1.51-2.21-1.39-2.5-.17-7.43-6.21-2.09.12-2.67-2.79-.52-9.17,2.67-5.28-.29-5.81,1.51-5.11-4.24,1.92-.81,1.97-4.24,2.5-2.67-.12-.41-11.21-.99-3.48,1.97-7.78v-5.57l-1.68-4.59-2.38-4.12-2.26-2.21-55.74,73.27,2.21,2.26,1.57.17,1.16,2.67,2.67,2.32,2.55,1.28v1.8l3.48,1.63-2.5,1.1-.17,1.39-2.9.17,1.1,1.97-1.8,1.39-2.79,3.08-2.09-1.68-1.1.29-.87-2.09-1.57,1.8-.81-2.67-2.21.29-2.67-1.39-2.9.17-1.16-1.1-1.97.58.52,1.92-1.51.81.41,1.28-.99,1.22,2.26,2.38-1.68,1.8.17,2.67-1.8-.12-1.8,3.08-.12,2.9-2.9,1.63.41,3.77-1.16,1.97,1.1,2.38-2.55,2.09,1.57,3.48,1.8-.87,1.97.52.99,2.9-.87,2.21,2.67.7,1.68,1.28.12,2.09,2.21,1.28-.58,1.97.99,2.9,2.09.41,2.55.12.87,2.9,1.68-.29,2.38,2.79h3.48l3.54,1.39,3.54-2.5.41-2.32,4.06.17,3.89,2.79h2.38l2.67-2.61.99-4.41-5.23-22.47,1.57.58,1.1-1.22,1.16,1.22,1.51-.12,2.09.99.12-1.51,3.48-.52,1.51,2.79,1.8.58,8.13,5.92,1.68-.99,3.66,2.32,7.26-3.08-1.1,3.48,1.86.7,1.22,1.97-1.05,3.02,2.38.17,2.09-1.8v-1.34l2.55-.75,2.84-2.09.46,1.8,6.1,4.18.75,1.8,3.72,2.26,3.54-.75,2.38-1.51,1.8.87-.46,3.6-2.09,1.8,1.8,5.4v3.6l.87,2.26,3.89-2.55,2.67.17,3.43,2.55,3.72,1.05,4.3,2.55,5.05-.87,2.84.75,4.01.29h0Z"
						fill="#cd162c" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(355.48 453.93)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">SAN VICENTE</tspan>
				<tspan x="3.29" y="14.02">DE CHUCURÍ</tspan>
			</text>
		</g>

		<g id="ZAPATOCA">
			<g id="Layer_2-60">
				<g id="svg2-60">
					<path id="path1135"
						d="m500.92,510.87l-1.19,2.21-1.91,1.28-.9,1.98-2.15,1.22-2.57.41-2.63,2.09-.12,2.56-.84,2.5-4.48.17-1.43-1.34-3.76-1.63-.54-2.56-3.46-2.39-1.85-3.08.18-2.62-.9-2.5-4-2.79h-2.57l-3.05-.87-3.35-2.33-.42-2.79,1.61-1.63-.12-1.28-2.57-1.92-2.63-1.22-3.46-.7.18-2.21,2.27-4.83-.12-5.82,2.03-5.7,3.05-2.91.18-4.31,1.31-9.72,1.31-2.79-2.45-4.31-.18-3.08-3.05-1.51-2.45,1.11-2.63-2.21,1.85-1.63.42-2.62-2.27-1.22,1.55-2.68-6.63-.12-1.85-1.92-.54-2.33-2.87-1.8-.12,2.21-2.87,1.28-1.55-1.22-1.73.52-3.05-1.98-3.58,1.63-4.3.7-4.9-1.51.84-1.69,2.15-.29,2.33-3.32.18-2.68,1.55-1.4,2.45.29,3.76-1.22,2.87.58,2.57-1.11,6.33-4.19,2.33,1.11,3.05-.52,2.75,1.8,2.45.17,3.29,1.98,1.73,2.33-.84,2.5-1.61.87,1.31,2.21-4,6.81-.6,2.5,1.43.99,4,.87,2.03,2.5.18,3.2,1.55,1.22,1.55.7.12,3.72,2.03,1.8.54,5.41,2.63-.12,3.17-1.51-.9,2.21,2.99,4.42,2.75-.58,1.31,1.4,2.15.7,3.29,4.6,4.72-6.69.18-4.42,4.18-4.89,1.13-3.03,3.17-.87,1.73-2.33.42-1.69,2.33-3.32,5.5,6.46,1.79,1.51,3.82.58,5.2,6.63.78,5.88.78,4.19-.3,4.83-1.85,2.27-.48,2.39-1.97.76-1.19,4.95-4.9,4.07-.9,4.95.48,5.7-2.51-.12-1.61.81-.72,1.8v1.98l-1.43,1.69v3.2l-1.97,2.62h0Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(459.57 492.52) rotate(1.24)" font-family="IBM Plex Sans" font-size="8.96"
				font-weight="500">
				<tspan x="0" y="0" letter-spacing="0em">Z</tspan>
				<tspan x="5.45" y="0">A</tspan>
				<tspan x="11.42" y="0" letter-spacing="-.09em">P</tspan>
				<tspan x="16.36" y="0" letter-spacing="-.06em">A</tspan>
				<tspan x="21.8" y="0" letter-spacing="-.01em">T</tspan>
				<tspan x="27.12" y="0" letter-spacing="0em">OCA</tspan>
			</text>
		</g>

		<g id="BETULIA">
			<g id="Layer_2-61">
				<g id="svg2-61">
					<path id="path1077"
						d="m419.9,432.62l.82-1.71,2.12-.29,2.29-3.35.18-2.71,1.53-1.41,2.41.29,3.71-1.24,2.82.59,2.53-1.12,6.24-4.24,2.29,1.12,3-.53,2.71,1.82,2.41.18,3.24,2,1.71,2.35-.82,2.53-1.59.88,1.29,2.24-1.59,2.71-2.29,4.18-.59,2.53,1.41,1,3.94.88,2,2.53.18,3.24,1.53,1.24,1.53.71.12,3.77,2,1.82.53,5.47,2.59-.12,3.12-1.53-.88,2.24,2.94,4.47,2.71-.59,1.29,1.41,2.12.71,3.24,4.65,4.65-6.77.18-4.47,4.12-4.94,1.12-3.06,3.12-.88,1.71-2.35.41-1.71,2.29-3.35-2.41-3.53-1.53-3.82-3.41-3.82-4.65-1.65-3.29-2-5.83-6.18-1.82-5.88-3.12-4.06-2.71-2.82-2.94-1.24-2.82-.53-2.41-1.82h-2.53l-4.41-2.35-1.59-2.71v-2l1.41-1.82-1.82-2.82-2.59,1-5.71-.29-4.82-3.82-3.53-4.47.18-2.65-2.59-2-3.12-.71-4.53-3.65-3.82-1.94-1.12-2.65v-1.94l-1.53-1.53-2.29.71-4.3-2.94-4.12-1.41-3.53-.29-2-1.53-5.12,1-4.41,2-3.53-3.65-2.53-1-3,.12,2.29,2.24,2.41,4.18,1.71,4.65v5.65l-2,7.88,1,3.53.41,11.36,2.71.12,4.3-2.53.82-2,4.3-1.94-1.53,5.18.29,5.88-2.71,5.35.53,9.3,2.71,2.82,2.12-.12,7.53,6.3,2.53.18s2.12,1.53,2.12,1.53Z"
						fill="#fff200" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(407.33 409.54)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">B</tspan>
				<tspan x="7.37" y="0" letter-spacing="0em">E</tspan>
				<tspan x="14.09" y="0">TULIA</tspan>
			</text>
		</g>

		<g id="SABANA_DE_TORRES">
			<g id="Layer_2-62">
				<g id="svg2-62">
					<path id="path1079"
						d="m389.35,363.08l5.2-2.51.53-1.69,2.8-.7,1.29.99,4.09-.99,3.97,2.22,2.8-2.51,2.28,1.64,2.22-1.99.99,2.8,2.69.99.99-3.33,1.87,1.4,2.69-.53-.7-2.69,1.11-2.22,2.1-.29,1.29-3.33,3.21-2.4,3.27-.82,1.58-1.69,3.27-1.29,14.78-25.42-1.58-4.5-.18-3.1-3.21-4.15-.18-4.5,1.87-3.21-1.29-2.22.53-1.99-1.99-1.93,1.17-1.11-1.29-2.22.7-3.91-2.1,1.29-2.1-2.34,2.57-4.5,1.87-1.11-5.96-5.14-5.08,2.34-1.81-4.44-4.32.04-4.85-3.31-1.4-3.8-2.57-2.51,1.69-3.21-.82-1.29-2.51.7-1.17-1.81,1.17-2.22-.88-1.64-2.92,1.23-1.69-1.11.29-1.52,2.22-2.69-1.58-1.23-2.57.99-1.4-.99,1.58-2.69-5.26-5.14-5.78-1.4-2.8,1.93-1.99-3.04-1.87-.12-2.1-1.93-.29-2.69-2.22-2.69-2.8-1.81,1.4-2.63-1.17-.82-2.4.18-1.58-1.52-.41-4.03.82-1.52-1.52-1.23-3.51-5.96-6.6-.88-4.09-1.23-1.58-1.69h-2.98l-3.39-2.92-2.22,2.1-1.81-1.4-1.69,1.23-2.22-1.29-3.21.7.18-3.21-2.98-.18v-3.04l1.99.41.58-1.52-1.52-3.62-1.52,1.23-1.11-1.64,1.17-2.63-.88-1.29-2.22,2.22-1.4-1.81-2.51,1.11.53-3.21-2.57.58.41-1.93-1.99-.18-.82-2.8-1.87-3.04-3.8-.7-1.11-2.4.53-1.99-2.57-1.64-5.08,2.34-1.11,2.51-2.57-.53-1.81-1.52-2.51.88-1.69-3.8-1.11,2.22.82,1.93-2.4,3.74,2.51,2.63-.29,4.5-1.11,2.8,1.87,3.04,2.8,1.29,3.27,6.66-.7,2.51,6.66,7.01-1.69,2.51-3.56,1.93-3.91,5.2-3.8.58-.58,2.34-3.51-.88.53,4.15-1.58,2.22,2.1,4.73-.99,5.73.88,5.02-1.29,3.21.7,8.06-.7,10.46,2.1,2.4,3.56.41,3.39,2.69v3.33l3.68,1.4,3.27-.7,6.37,4.62,2.4.41,1.17,3.39-2.1,2.4,4.67,3.21,3.8-1.52,3.8-.12,1.17,3.33,4.27,4.21,3.21.7-1.81,2.69,1.69,1.29-1.52,4.32,2.22,3.39,2.92-.41,1.4,2.8,3.1,1.52-.41,1.69,3.56,3.39,2.1.58.12,2.22,2.51,2.63,3.56,8.35,2.57,9.06,5.61,12.56.88,3.8,4.21,2.22,4.62,3.45h0Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(344.4 272.54)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0">SABANA DE </tspan>
				<tspan x="16.9" y="14.02" letter-spacing="0em">TORRES</tspan>
			</text>
		</g>

		<g id="LANDAZURI">
			<g id="Layer_2-63">
				<g id="svg2-63">
					<path id="path1081"
						d="m299.93,571.81l3.93,1.41,2.23,2.23-.94,1.17,1,2.11-.12,2.52-1.58,6.98.88,2.52-.88,3.22,1.52,4.04,2.29-1,1.88-2.23,1.82.41.88,4.63,2.11.7,2.7,6.15,3.49,2.6,4.8,3.6,2.4,2.4,1.2,1.2,2.4,1.2,3.6,2.4,3.63,3.07,1.17,2.52-.18,2.52-2.52,1.93-2.17-1.52-2.4,7.97-2.81.7.59,2.81-1,2.52-2.52,2.52-2.29,4.92-2.52,1.82-4.1,6.74-.7,4.22-2.17,3.75,2.52,1,2.11,2.7.7-1.93,2.99-.59.41,2.4-.74,2.7-2.4,1.2-1.2,1.2-1,1.4,2.4,1.11,1.7,1.99v2.81l1.58,3.22-.59,3.11-2.76,2.64-1.34,1.7-1.2,2.4h0l-1.2,1.2-4.8,3.6h-2.4l-.9-3.39-2.99-1.23-2.11.12-1.88-1.29-3.4.59-6.39,2.93-9.26,8.73-3.07.74-2.15,1.6-.18,2.11-1.58.59-3.29-.7-1.2-2.4-2.4-2.4-1.2-2.4-1.4-2.13-2.64-1.41-1.29-3.81-1.17-2.64v-3.4l-4.1-2.52-3.58,2.23-2.58-1.58-5.28,4.1-1.17-.88-1.99.88-1.41-2.52-2.7,1.41-1.29-.7-5.8,3.75-.29,1.41-3.4,1.99-3.58-.7-9.09,8.15-4.51-1.11-2.7.41-1.58-1.23h-1.82l-1.17-1.99-1.52-.12-1.11,1.41-1-.29-.41-1.52-2.05.41-3.28-5.04-.47-3.46,2.05-.06,2.05,2.52,2.81-.18,4.4,1.99,1.7-2.46,1.64-1.52,3.17-.64,5.57-.23,2.58-2.11,2.17.06,2.76-1.82,3.11.23,1.29-2.34,3.28-1.23,5.22-4.22,5.04-1.29,5.92-5.63.59-3.22,2.76.18,4.28-1.58,3.52-2.52,1.29-3.05,2.93,3.11,1.52-2.05.76-4.04,7.21,2.52,3.17-1.23.35-2.17,2.17-1.58.06-5.16-.76-3.28,1.47-4.51,2.58-3.4,3.69-1.99,3.46-4.16-1.06-2.76,3.65-6.54v-3.6l2.5-4.34.35-2.34,3.17-3.4-2.42-1.39-15-12.6-7.2-8.4,3.45-4.76,2.23,1.64,2.11-.59,1.88,2.11,5.22-6.74,6.33-5.86-.12-2.7,1-1.64,4.1-.35Z"
						fill="#cd7d16" stroke="#000" stroke-miterlimit="10"></path>
				</g>
			</g><text transform="translate(245.77 686.26)" font-family="IBM Plex Sans" font-size="10" font-weight="500">
				<tspan x="0" y="0" letter-spacing="0em">L</tspan>
				<tspan x="6.94" y="0" letter-spacing="0em">LANDAZURI</tspan>
			</text>
		</g>
	</svg> -->

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"
		integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous">
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
</body>

</html>

<style>
	.content-map {
		background-color: #ffffff !important;
		padding: 20px 0;
	}


	#mapa {
		background-color: transparent;
		background-repeat: no-repeat;
		background-position: center;
		width: 100%;
		height: auto;
		margin: 0 auto;
		text-align: center;
		padding: 0.1px 0;
	}

	#mapa svg {
		max-width: 950px;

		width: 100%;

	}

	#mapa svg path {
		fill: #fff;
		transition: all .4s;
	}

	#mapa svg path:hover {
		fill: #636363
	}

	#mapa img {
		position: absolute;
	}
</style>