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
	.claritas {
	    top: 515px;
	    left: 516px;
	    width: 240px;
	}
	.embalse-de-troneras {
	    top: 220px;
	    left: 651px;
	    width: 202px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.embalse-miraflores {
	    top: 220px;
	    left: 232px;
	    width: 261px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	    z-index: 999;
	}
	.la-camelia {
	    top: 423px;
	    left: 472px;
	    width: 134px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-granja {
	    top: 518px;
	    left: 329px;
	    width: 293px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-herradura {
	    top: 121px;
	    left: 480px;
	    width: 418px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-vega {
	    top: 556px;
	    left: 453px;
	    width: 171px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.tenche {
	    top: 19px;
	    left: 4px;
	    width: 707px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(102deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 508px;
	    left: 597px;
	    width: 58px;
	    filter: invert(78%) sepia(99%) saturate(296%) hue-rotate(69deg) brightness(68%) contrast(119%) ;
	}
</style>
