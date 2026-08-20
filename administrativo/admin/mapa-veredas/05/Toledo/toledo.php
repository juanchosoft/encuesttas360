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
<?php include(__DIR__ . "/../../mapa_veredas.php"); ?>    </div>
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
	.barrancas {
	    top: 218px;
	    left: 473px;
	    width: 178px;
	}
	.biogui {
	    top: 660px;
	    left: 312px;
	    width: 134px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.brugo {
	    top: 43px;
	    left: 183px;
	    width: 200px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%) ;
	}
	.buenavista {
		top: 653px;
	    left: 423px;
	    width: 91px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%) ;
	}
	.el-moral {
		top: 109px;
	    left: 309px;
	    width: 114px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.el-cantaro {
		top: 319px;
	    left: 633px;
	    width: 78px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}	
	.el-naranjo{
	    top: 557px;
	    left: 373px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-valle {
	    top: 153px;
	    left: 593px;
	    width: 81px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.guayabal {
	    top: 560px;
	    left: 299px;
	    width: 192px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.helechales {
	    top: 429px;
	    left: 281px;
	    width: 188px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-cascarela {
		top: 2px;
	    left: 326px;
	    width: 311px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-florida{
	    top: 246px;
	    left: 413px;
	    width: 117px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-linda{
	    top: 455px;
	    left: 446px;
	    width: 99px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.las-margaritas{
	    top: 403px;
	    left: 565px;
	    width: 121px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.mena {
	    top: 362px;
	    left: 440px;
	    width: 112px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%) ;
	}
	.miraflores {
	    top: 250px;
	    left: 537px;
	    width: 183px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(962deg) brightness(118%) contrast(119%) ;
	}
	.paloblanco{
		top: 307px;
	    left: 250px;
	    width: 199px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.santa-maria {
	    top: 649px;
	    left: 312px;
	    width: 69px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.santo-domingo{
	    top: 570px;
	    left: 481px;
	    width: 137px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.taque {
	    top: 433px;
	    left: 514px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.zona-urbana {
	    top: 374px;
	    left: 533px;
	    width: 23px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
