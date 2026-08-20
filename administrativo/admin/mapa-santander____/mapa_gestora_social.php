<?php
define("DS", DIRECTORY_SEPARATOR);
include './admin/classes/Colombia.php';

$arr = array('codigo' => Util::getDepartamentoPrincipal());
$data = Colombia::getInformacionParaMapaGestoraSocial($arr);
$isvalid = $arr['output']['valid'];
$santander =  $data['output']['response'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<title>Mapa</title>
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
	<div class="content-map infoMapa">
		<div class="titles_jurisdicciones btll">

		</div>
	</div>

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

	<div class="content-map">
		<div id="mapa">

			<svg version="1.1" id="svg2" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
				xmlns:svg="http://www.w3.org/2000/svg" xmlns:dc="http://purl.org/dc/elements/1.1/"
				xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
				xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
				xmlns:cc="http://creativecommons.org/ns#" xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1280.6 1508"
				style="enable-background:new 0 0 1280.6 1508;" xml:space="preserve" class="max-w-4xl m-auto"
				stroke="#000" stroke-whidth="1px">

				<style type="text/css">
					.st0 {
						fill: #D3D3D3;
					}

					.st1 {
						fill: #EFEFEF;
					}

					.st2 {
						fill: #939393;
					}

					.st3 {
						fill: #707070;
					}

					.st4 {
						fill: #B5B5B5;
					}

					.st5 {
						font-family: 'ArialMT';
					}

					.st6 {
						font-size: 15px;
					}
				</style>
				<sodipodi:namedview bordercolor="#666666" borderlayer="true" borderopacity="1.0" fit-margin-bottom="0"
					fit-margin-left="0" fit-margin-right="0" fit-margin-top="0" id="base"
					inkscape:current-layer="layer5" inkscape:cx="640.28082" inkscape:cy="754.00232"
					inkscape:document-units="px" inkscape:guide-bbox="true" inkscape:pageopacity="0.0"
					inkscape:pageshadow="2" inkscape:snap-bbox="true" inkscape:snap-page="false"
					inkscape:window-height="986" inkscape:window-maximized="1" inkscape:window-width="1920"
					inkscape:window-x="-11" inkscape:window-y="-11" inkscape:zoom="0.47745211" pagecolor="#ffffff"
					showgrid="false" showguides="true">
				</sodipodi:namedview>
				<g id="g1247" transform="translate(-2453.8755,-2204.8853)" inkscape:groupmode="layer"
					inkscape:label="Entidades">
					<!-- Mapa con la información de la gestora social -->
					<?php foreach ($santander as $key => $value) : ?>
						<path inkscape:connector-curvature={0} id="<?php echo $value['path']; ?>"
							inkscape:connector-curvature="0" sodipodi:nodetypes="<?php echo $value['nodetypes']; ?>"
							d=" <?php echo $value['d']; ?>"
							class="carmen-del-chucuri municipios mapaClick <?php echo getClasePorcentaje($value['porcentaje_participacion']); ?>"
							data-url="<?php echo getUrl() . 'estado_municipios_gestora.php?mun=' . $value['codigo_muncipio'] . '&dep=' . $value['codigo_departamento']; ?>"
							data-sub="<?php echo getClaseColorVeredas($value['color_calculado_de_municipio']); ?>"
							data-name="<?php echo strtolower($value['municipio']); ?>"
							title="<?php echo strtoupper(str_replace("-", " ", $value['nombre_mapa'])); ?>"
							style="fill: <?php echo getColorByNum($value["num_val"]) ?>; " ; />
						</path>
					<?php endforeach; ?>
				</g>

				<g id="layer6" inkscape:groupmode="layer" inkscape:label="Nombres">
					<text transform="matrix(1 0 0 1 733.7092 995.7245)" class="st5 st6 nombres">Palmar</text>
					<text transform="matrix(1 0 0 1 694.0212 968.894)" class="st5 st6 nombres">Hato</text>
					<text transform="matrix(1 0 0 1 759.8948 971.9391)" class="st5 st6 nombres">Cabrera</text>
					<text transform="matrix(1 0 0 1 836.4905 981.3859)" class="st5 st6 nombres">San Gil</text>
					<text transform="matrix(1 0 0 1 795.051 999.0676)" class="st5 st6 nombres fondo">Pinchote</text>
					<text transform="matrix(1 0 0 1 746.6073 1032.0092)" class="st5 st6 nombres">El Socorro</text>
					<text transform="matrix(1 0 0 1 847.0934 1037.5458)" class="st5 st6 nombres">Valle de</text>
					<text transform="matrix(1 0 0 1 847.0934 1052.5458)" class="st5 st6 nombres">San José</text>
					<text transform="matrix(1 0 0 1 826.3318 1092.3563)" class="st5 st6 nombres">Ocamonte</text>
					<text transform="matrix(1 0 0 1 798.6497 1064.6742)" class="st5 st6 nombres">Páramo</text>
					<text transform="matrix(1 0 0 1 753.251 1096.2318)" class="st5 st6 nombres">Confines</text>
					<text transform="matrix(1 0 0 1 718.3716 1061.3523)" class="st5 st6 nombres">Palmas del</text>
					<text transform="matrix(1 0 0 1 718.3716 1076.3523)" class="st5 st6 nombres">Socorro</text>
					<text transform="matrix(1 0 0 1 660.239 1085.7126)" class="st5 st6 nombres">Chimá</text>
					<text transform="matrix(1 0 0 1 925.9874 1005.9881)" class="st5 st6 nombres">Mogotes</text>
					<text transform="matrix(1 0 0 1 980.2444 1047.5112)" class="st5 st6 nombres">San</text>
					<text transform="matrix(1 0 0 1 980.2444 1062.5112)" class="st5 st6 nombres">Joaquín</text>
					<text transform="matrix(1 0 0 1 1013.463 1115.0557)" class="st5 st6 nombres">Onzaga</text>
					<text transform="matrix(1 0 0 1 907.7173 1165.9907)" class="st5 st6 nombres">Coromoro</text>
					<text transform="matrix(1 0 0 1 807.4946 1158.5704)" class="st5 st6 nombres">Charalá</text>
					<text transform="matrix(1 0 0 1 875.1703 1264.3733)" class="st5 st6 nombres">Encino</text>
					<text transform="matrix(1 0 0 1 709.7201 1353.1604)" class="st5 st6 nombres">Gámbita</text>
					<text transform="matrix(1 0 0 1 689.7747 1239.577)" class="st5 st6 nombres">Suaita</text>
					<text transform="matrix(1 0 0 1 652.0306 1174.1991)" class="st5 st6 nombres">Guadalupe</text>
					<text transform="matrix(1 0 0 1 747.7385 1175.5471)" class="st5 st6 nombres">Oiba</text>
					<text transform="matrix(1 0 0 1 703.2546 1112.8651)" class="st5 st6 nombres">Guapotá</text>
					<text transform="matrix(1 0 0 1 544.8647 1058.2711)" class="st5 st6 nombres">Santa Helena</text>
					<text transform="matrix(1 0 0 1 544.8647 1073.2711)" class="st5 st6 nombres">del Opón</text>
					<text transform="matrix(1 0 0 1 585.9786 1120.9531)" class="st5 st6 nombres">Contratación</text>
					<text transform="matrix(1 0 0 1 563.7367 1149.2611)" class="st5 st6 nombres">El Guacamayo</text>
					<text transform="matrix(1 0 0 1 590.0227 1193.0712)" class="st5 st6 nombres">Aguada</text>
					<text transform="matrix(1 0 0 1 531.3848 1182.2871)" class="st5 st6 nombres">La Paz</text>
					<text transform="matrix(1 0 0 1 574.5208 1244.9691)" class="st5 st6 nombres">San Benito</text>
					<text transform="matrix(1 0 0 1 572.4988 1281.3651)" class="st5 st6 nombres">Güepsa</text>
					<text transform="matrix(1 0 0 1 527.3407 1260.4711)" class="st5 st6 nombres">Chipatá</text>
					<text transform="matrix(1 0 0 1 477.4648 1195.0931)" class="st5 st6 nombres">Vélez</text>
					<text transform="matrix(1 0 0 1 401.9768 1145.8912)" class="st5 st6 nombres">Landázuri</text>
					<text transform="matrix(1 0 0 1 231.5428 1059.723)" class="st5 st6 nombres">Cimitarra</text>
					<text transform="matrix(1 0 0 1 191.8274 1288.6964)" class="st5 st6 nombres">Bolívar</text>
					<text transform="matrix(1 0 0 1 384.9697 1252.7321)" class="st5 st6 nombres">El Peñón</text>
					<text transform="matrix(1 0 0 1 347.6732 1309.3428)" class="st5 st6 nombres">Sucre</text>
					<text transform="matrix(1 0 0 1 282.4045 1372.6134)" class="st5 st6 nombres">La Belleza</text>
					<text transform="matrix(1 0 0 1 350.3372 1425.228)" class="st5 st6 nombres">El Florián</text>
					<text transform="matrix(1 0 0 1 400.2878 1400.5857)" class="st5 st6 nombres">Jesús María</text>
					<text transform="matrix(1 0 0 1 404.2839 1451.8684)" class="st5 st6 nombres">Albania</text>
					<text transform="matrix(1 0 0 1 495.5269 1402.5837)" class="st5 st6 nombres">Puente</text>
					<text transform="matrix(1 0 0 1 495.5269 1417.5837)" class="st5 st6 nombres">Nacional</text>
					<text transform="matrix(1 0 0 1 472.8826 1345.3071)" class="st5 st6 nombres">Guavatá</text>
					<text transform="matrix(1 0 0 1 543.4794 1332.653)" class="st5 st6 nombres">Barbosa</text>
					<text transform="matrix(1 0 0 1 312.3748 903.7441)" class="st5 st6 nombres">Puerto Parra</text>
					<text transform="matrix(1 0 0 1 398.2898 837.1433)" class="st5 st6 nombres">Simacota</text>
					<text transform="matrix(1 0 0 1 571.0319 757.9562)" class="st5 st6 nombres">San Vicente</text>
					<text transform="matrix(1 0 0 1 571.0319 772.9562)" class="st5 st6 nombres">del Chucurí</text>
					<text transform="matrix(1 0 0 1 734.5803 823.498)" class="st5 st6 nombres">Zapatoca</text>
					<text transform="matrix(1 0 0 1 714.979 904.3535)" class="st5 st6 nombres">Galán</text>
					<text transform="matrix(1 0 0 1 528.7666 908.6412)" class="st5 st6 nombres">Carmen de Chucurí</text>
					<text transform="matrix(1 0 0 1 416.6577 509.7371)" class="st5 st6 nombres">Puerto Wilches</text>
					<text transform="matrix(1 0 0 1 427.135 648.0378)" class="st5 st6 nombres">Barrancabermeja</text>
					<text transform="matrix(1 0 0 1 552.1646 481.7975)" class="st5 st6 nombres">Sabana de Torres</text>
					<text transform="matrix(1 0 0 1 774.2842 483.1945)" class="st5 st6 nombres">Rionegro</text>
					<text transform="matrix(1 0 0 1 793.1433 388.8984)" class="st5 st6 nombres">El Playón</text>
					<text transform="matrix(1 0 0 1 916.7759 421.7275)" class="st5 st6 nombres">Suratá</text>
					<text transform="matrix(1 0 0 1 969.1625 478.3052)" class="st5 st6 nombres">California</text>
					<text transform="matrix(1 0 0 1 976.1472 506.2446)" class="st5 st6 nombres">Vetas</text>
					<text transform="matrix(1 0 0 1 862.2937 494.3704)" class="st5 st6 nombres">Matanza</text>
					<text transform="matrix(1 0 0 1 726.7869 587.9679)" class="st5 st6 nombres">Lebrija</text>
					<text transform="matrix(1 0 0 1 804.2888 611.0181)" class="st5 st6 nombres">BUCARAMANGA</text>
					<text transform="matrix(1 0 0 1 946.1124 605.4302)" class="st5 st6 nombres">Tona</text>
					<text transform="matrix(1 0 0 1 905.6 543.9631)" class="st5 st6 nombres">Charta</text>
					<text transform="matrix(1 0 0 1 845.53 659.2139)" class="st5 st6 nombres">Floridablanca</text>
					<text transform="matrix(1 0 0 1 794.5403 706.7109)" class="st5 st6 nombres">Girón</text>
					<text transform="matrix(1 0 0 1 656.2394 686.4548)" class="st5 st6 nombres">Betulia</text>
					<text transform="matrix(1 0 0 1 872.771 722.0776)" class="st5 st6 nombres">Piedecuesta</text>
					<text transform="matrix(1 0 0 1 955.7261 739.0325)" class="st5 st6 nombres">Santa</text>
					<text transform="matrix(1 0 0 1 955.7261 754.0325)" class="st5 st6 nombres">Bárbara</text>
					<text transform="matrix(1 0 0 1 1019.8206 739.4541)" class="st5 st6 nombres">Guaca</text>
					<text transform="matrix(1 0 0 1 1118.0708 765.1763)" class="st5 st6 nombres">Cerrito</text>
					<text transform="matrix(1 0 0 1 994.9417 833.4877)" class="st5 st6 nombres">San Andrés</text>
					<text transform="matrix(1 0 0 1 833.8618 817.464)" class="st5 st6 nombres">Los Santos</text>
					<text transform="matrix(1 0 0 1 768.7406 931.023)" class="st5 st6 nombres">Barichara</text>
					<text transform="matrix(1 0 0 1 799.3312 893.3389)" class="st5 st6 nombres">Villanueva</text>
					<text transform="matrix(1 0 0 1 867.1625 872.5017)" class="st5 st6 nombres">Jordán</text>
					<text transform="matrix(1 0 0 1 921.2502 882.6986)" class="st5 st6 nombres">Aratoca</text>
					<text transform="matrix(1 0 0 1 947.8508 861.8616)" class="st5 st6 nombres">Cepitá</text>
					<text transform="matrix(1 0 0 1 896.4231 936.7864)" class="st5 st6 nombres">Curití</text>
					<text transform="matrix(1 0 0 1 996.175 911.8842)" class="st5 st6 nombres">Molagavita</text>
					<text transform="matrix(1 0 0 1 1119.5208 840.0955)" class="st5 st6 nombres">Concepción</text>
					<text transform="matrix(1 0 0 1 1162.5342 908.7885)" class="st5 st6 nombres">Carcasí</text>
					<text transform="matrix(1 0 0 1 1111.175 968.4935)" class="st5 st6 nombres">San Miguel</text>
					<text transform="matrix(1 0 0 1 1174.7319 999.3091)" class="st5 st6 nombres">Macaravita</text>
					<text transform="matrix(1 0 0 1 1077.7916 1002.519)" class="st5 st6 nombres">Capitanejo</text>
					<text transform="matrix(1 0 0 1 1058.5319 871.553)" class="st5 st6 nombres">Málaga</text>
					<text transform="matrix(1 0 0 1 1100.2611 911.3564)" class="st5 st6 nombres">Enciso</text>
					<text transform="matrix(1 0 0 1 1050.828 928.6902)" class="st5 st6 nombres">San José</text>
					<text transform="matrix(1 0 0 1 1050.828 943.6902)" class="st5 st6 nombres">de Miranda</text>
				</g>
			</svg>
		</div>
	</div>

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