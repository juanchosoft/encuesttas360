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
	.altavista {
	    top: 427px;
	    left: 707px;
	    width: 172px;
	}
	.anocozca {
	    top: 16px;
	    left: 22px;
	    width: 434px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.asesi {
		top: 352px;
	    left: 232px;
	    width: 231px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.bella-aguada {
		top: 420px;
	    left: 504px;
	    width: 95px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chochal {
		top: 289px;
	    left: 548px;
	    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-encanto {
		top: 491px;
	    left: 460px;
	    width: 186px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-hato {
		top: 243px;
	    left: 572px;
	    width: 161px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.el-playon {
	    top: 252px;
	    left: 479px;
	    width: 112px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-tambor {
	    top: 443px;
	    left: 248px;
	    width: 254px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-cascajala {
		top: 518px;
	    left: 645px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-cortada {
		top: 273px;
	    left: 162px;
	    width: 303px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-garcia {
	    top: 59px;
	    left: 355px;
	    width: 252px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-manga {
	    top: 481px;
	    left: 419px;
	    width: 187px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-noque {
	    top: 513px;
	    left: 219px;
	    width: 234px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.la-salazar {
		top: 532px;
	    left: 708px;
	    width: 158px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-soledad {
		top: 513px;
	    left: 587px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.los-sauces{
	    top: 278px;
	    left: 356px;
	    width: 169px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.san-juan {
		top: 226px;
	    left: 582px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.los-pinos {
	    top: 364px;
	    left: 418px;
	    width: 141px;
	    filter: invert(85%) sepia(190%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
		top: 487px;
	    left: 489px;
	    width: 28px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
