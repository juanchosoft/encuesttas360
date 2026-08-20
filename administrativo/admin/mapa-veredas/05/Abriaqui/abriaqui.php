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
		<?php
		if (!file_exists(__DIR__ . "/../../mapa_veredas.php")) {
			die("❌ El archivo del mapa no existe. ❌");
		}else{
			include __DIR__ . "/../../mapa_veredas.php";
		}
		?>
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
	.corcovado {
	    top: 376px;
	    left: 349px;
	    width: 213px;
		filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}
	.el-cejen {
		top: 384px;
    	left: 353px;
    	width: 60px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}
	.la-antigua {
	    top: 59px;
	    left: 295px;
	    width: 154px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(146deg) brightness(88%) contrast(119%) ;
	}	
	.la-nancui {
	    top: 92px;
	    left: 4px;
	    width: 302px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.las-juntas {
	    top: 235px;
	    left: 205px;
	    width: 247px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-timotea {
	    top: 4px;
	    left: 308px;
	    width: 115px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1846deg) brightness(100%) contrast(80%) ;
	}	
	.monos {
	    top: 270px;
	    left: 380px;
	    width: 192px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.piedras {
	    top: 221px;
	    left: 43px;
	    width: 253px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.potreros {
	    top: 151px;
	    left: 244px;
	    width: 238px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.san-jose {
	    top: 399px;
	    left: 64px;
	    width: 237px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%) ;
	}	
	.san-ruperto {
		top: 401px;
	    left: 159px;
	    width: 206px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.santa-teresa {
	    top: 101px;
	    left: 288px;
	    width: 202px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}	
	.zona-urbana {
	    top: 358px;
	    left: 368px;
	    width: 35px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}	
</style>


