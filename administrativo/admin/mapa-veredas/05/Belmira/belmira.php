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
	    width: 800px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.el-salazar {
	    top: 490px;
	    left: 370px;
	    width: 145px;
	}
	.el-valle {
	    top: 80px;
	    left: 431px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-yuyal {
	    top: 500px;
	    left: 278px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(
	918deg) brightness(818%) contrast(30%) ;
	}
	.la-amoladora {
	    z-index: 997;
	    top: 711px;
	    left: 400px;
	    width: 99px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.labores {
	    z-index: 998;
	    top: 180px;
	    left: 385px;
	    width: 133px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-candelaria {
	    top: 218px;
	    left: 225px;
	    width: 333px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-miel {
	    top: 635px;
	    left: 404px;
	    width: 49px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	    z-index: 999;
	}
	.las-playas {
	    top: 598px;
	    left: 386px;
	    width: 159px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.playitas {
	    top: 69px;
	    left: 474px;
	    width: 53px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.quebraditas {
	    top: 14px;
	    left: 190px;
	    width: 291px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.rio-arriba {
	    top: 296px;
	    left: 219px;
	    width: 211px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.san-jose {
	    top: 622px;
	    left: 358px;
	    width: 62px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.santo-domingo {
	    top: 545px;
	    left: 331px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.zafra {
	    top: 670px;
	    left: 527px;
	    width: 83px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.zancudito {
	    top: 664px;
	    left: 478px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 499px;
	    left: 342px;
	    width: 25px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>


