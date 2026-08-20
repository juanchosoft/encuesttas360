<!DOCTYPE html>
<html lang="es">  
  <head>    
    <title>Mapa</title>    
    <meta charset="UTF-8">
    <meta name="title" content="">
    <meta name="description" content="">    
  </head>  
  <body>    
    <div class="content-map">
    	<div id="mapa">
<?php include(__DIR__ . "/../../mapa_veredas.php"); ?>    	</div>
    </div>
  </body>  
</html>
<style>
	.content-map {
	    background-color: #efefef;
	    padding: 20px 0;
	}	
	#mapa {
	    position: relative;
	    background: url(img/base.png);
	    background-size: contain;
	    background-repeat: no-repeat;
	    background-position: center;
	    width: 900px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.alto-bonito {
	    top: 200px;
	    left: 748px;
	    width: 124px;
	}
	.belencito {
	    top: 374px;
	    left: 779px;
	    width: 68px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.bocas-de-chigorodo {
	    top: 405px;
	    left: 18px;
	    width: 154px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.campamento {
		top: 404px;
	    left: 750px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.caracoli {
	    top: 469px;
	    left: 701px;
	    width: 119px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.carepita-canal-4{
	    top: 365px;
	    left: 80px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.carepita-km4 {
		top: 314px;
	    left: 81px;
	    width: 72px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.casa-verde {
		top: 388px;
	    left: 354px;
	    width: 127px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.chirido {
	    top: 493px;
	    left: 260px;
	    width: 86px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-cerro {
	    top: 392px;
	    left: 788px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-encanto {
		top: 470px;
	    left: 223px;
	    width: 156px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-palmar{
	    top: 426px;
	    left: 560px;
	    width: 79px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-tagual {
	    top: 375px;
	    left: 706px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.ipankay {
	    top: 505px;
	    left: 291px;
	    width: 247px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.la-cadena-1 {
		top: 478px;
	    left: 368px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-cadena-2 {
	    top: 438px;
	    left: 410px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1971deg) brightness(100%) contrast(80%) ;
	}
	.la-cristalina{
	    top: 272px;
	    left: 747px;
	    width: 110px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-danta {
	    top: 402px;
	    left: 618px;
	    width: 117px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.las-quinientas {
		top: 347px;
	    left: 26px;
	    width: 78px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.las-trecientas {
	    top: 427px;
	    left: 48px;
	    width: 58px;
	}
	.la-union {
	    top: 213px;
	    left: 818px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-union-15 {
	    top: 479px;
	    left: 145px;
	    width: 141px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.miramar {
	    top: 326px;
	    left: 727px;
	    width: 61px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(110deg) brightness(88%) contrast(119%) ;
	}
	.nueva-esperanza {
		top: 260px;
	    left: 94px;
	    width: 70px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.piedras-blancas{
	    top: 482px;
	    left: 601px;
	    width: 198px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.polines-san-sebastian {
		top: 513px;
	    left: 513px;
	    width: 122px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.pronexcol {
	    top: 332px;
	    left: 87px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.remedia-pobre {
	    top: 465px;
	    left: 482px;
	    width: 110px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.zarabanda {
	    top: 308px;
	    left: 151px;
	    width: 220px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.vijagual-medio {
		top: 379px;
	    left: 452px;
	    width: 147px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.zungo{
	    top: 178px;
	    left: 3px;
	    width: 242px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.zungo-carepita {
	    top: 242px;
	    left: 131px;
	    width: 60px;
	}
	.zona-urbana {
	    top: 479px;
	    left: 400px;
	    width: 36px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
