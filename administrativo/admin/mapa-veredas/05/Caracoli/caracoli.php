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
	.botijas {
	    top: 224px;
	    left: 139px;
	    width: 189px;
	}
	.canalones {
	    top: 16px;
	    left: 92px;
	    width: 187px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.canutillo {
	    top: 469px;
	    left: 670px;
	    width: 140px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.cascaron {
		top: 130px;
	    left: 226px;
	    width: 121px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-62 {
	    top: 139px;
	    left: 310px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-bagre {
		top: 327px;
	    left: 332px;
	    width: 215px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.el-buey {
		top: 393px;
	    left: 246px;
	    width: 125px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-pital {
		top: 585px;
	    left: 360px;
	    width: 166px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-cortada {
	    top: 346px;
	    left: 501px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-maria {
		top: 186px;
	    left: 364px;
	    width: 205px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-mesa {
	    top: 422px;
	    left: 572px;
	    width: 195px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.las-aguilas {
	    top: 243px;
	    left: 472px;
	    width: 118px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.quebradona {
	    top: 283px;
	    left: 189px;
	    width: 138px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(77deg) brightness(100%) contrast(150%) ;
	}
	.santa-isabel {
		top: 40px;
	    left: 201px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.sardinas{
		top: 211px;
	    left: 283px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
		top: 183px;
	    left: 358px;
	    width: 22px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
