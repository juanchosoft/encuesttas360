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
	    	<?php include(__DIR__ . "/../../mapa_veredas.php"); ?>

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
	.cruces {
	    top: 208px;
	    left: 250px;
	    width: 140px;
	}
	.el-carbon {
	    top: 272px;
	    left: 178px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}
	.el-cerro {
	    top: 132px;
	    left: 512px;
	    width: 285px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(10deg) brightness(88%) contrast(119%) ;
	}
	.el-popo {
		z-index: 997;
	    top: 71px;
	    left: 159px;
	    width: 181px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.el-resplandor {
		z-index: 998;
	    top: 6px;
	    left: 427px;
	    width: 264px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.embalse {
	    top: 2px;
	    left: 459px;
	    width: 299px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}	
	.la-inmaculada {
	    top: 212px;
	    left: 412px;
	    width: 109px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.piedras {
	    top: 302px;
	    left: 316px;
	    width: 139px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.remolino {
	    top: 26px;
	    left: 116px;
	    width: 142px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.san-jose {
	    top: 196px;
	    left: 336px;
	    width: 167px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}	
	.san-lorenzo {
		z-index: 999;
	    top: 121px;
	    left: 272px;
	    width: 384px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.san-miguel {
	    top: 65px;
	    left: 270px;
	    width: 198px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.san-pedro {
	    top: 155px;
	    left: 4px;
	    width: 196px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.tocaima {
	    top: 146px;
	    left: 129px;
	    width: 155px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}			
	.zona-urbana {
	    top: 159px;
	    left: 127px;
	    width: 24px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}	
	.la-pava {
	    top: 306px;
	    left: 231px;
	    width: 143px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
</style>


