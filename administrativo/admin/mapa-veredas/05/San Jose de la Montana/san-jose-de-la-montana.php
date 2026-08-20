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
	    	<?php include "../mapa-veredas/mapa_veredas.php" ?>
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
	    background-position: center;
	    width: 900px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.cambure {
	    top: 13px;
	    left: 270px;
	    width: 342px;
	}
	.el-caribe {
	    top: 379px;
	    left: 329px;
	    width: 314px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(13deg) brightness(118%) contrast(119%);
	}
	.el-congo {
	    top: 451px;
	    left: 187px;
	    width: 210px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.la-maria {
		top: 48px;
	    left: 368px;
	    width: 303px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.la-mariela {
	    top: 123px;
	    left: 121px;
	    width: 169px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.potreritos {
	    top: 243px;
	    left: 564px;
	    width: 217px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.santa-barbara {
	    top: 8px;
	    left: 201px;
	    width: 188px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(327deg) brightness(100%) contrast(80%);
	}
	.santa-ines{
	    top: 232px;
	    left: 243px;
	    width: 83px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.zona-urbana {
		top: 198px;
	    left: 376px;
	    width: 47px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
