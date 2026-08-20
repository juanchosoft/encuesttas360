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
|<?php include(__DIR__ . "/../../mapa_veredas.php"); ?>
    	</div>
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
	    width: 800px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.cienaguita {
	    top: 406px;
	    left: 93px;
	    width: 266px;
	}
	.el-barro {
	    top: 555px;
	    left: 94px;
	    width: 188px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	    z-index: 999;
	}
	.el-nudillo {
	    top: 637px;
	    left: 128px;
	    width: 169px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(10deg) brightness(88%) contrast(119%) ;
	}
	.la-cascajala {
	    z-index: 997;
	    top: 306px;
	    left: 28px;
	    width: 320px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(69deg) brightness(88%) contrast(119%) ;
	}
	.la-estacion {
	    z-index: 998;
	    top: 558px;
	    left: 382px;
	    width: 224px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.promision {
	    top: 238px;
	    left: 165px;
	    width: 164px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.romeral {
	    top: 13px;
	    left: 298px;
	    width: 314px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.san-isidro {
	    top: 477px;
	    left: 48px;
	    width: 297px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.santa-ana {
	    top: 379px;
	    left: 19px;
	    width: 116px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.santa-barbara {
	    top: 617px;
	    left: 269px;
	    width: 143px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.santa-rita {
	    z-index: 999;
	    top: 518px;
	    left: 310px;
	    width: 97px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(224deg) brightness(119%) contrast(119%) ;
	}	
	.zona-urbana {
	    top: 468px;
	    left: 326px;
	    width: 83px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
	.la-clara {
	    top: 442px;
	    left: 374px;
	    width: 210px;
	    filter: invert(48%) sepia(99%) saturate(276%) hue-rotate(350deg) brightness(118%) contrast(50%) ;
	}
</style>


