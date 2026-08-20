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
	.barlovento {
	    top: 204px;
	    left: 474px;
	    width: 149px;
	}
	.bellavista {
	    top: 395px;
	    left: 385px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.cajones {
	    top: 447px;
	    left: 673px;
	    width: 122px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-contento {
	    z-index: 997;
	    top: 410px;
	    left: 397px;
	    width: 21px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-tablazo {
	    z-index: 998;
	    top: 97px;
	    left: 524px;
	    width: 61px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-troya {
	    top: 442px;
	    left: 551px;
	    width: 189px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.farallones-del-citara {
	    top: 178px;
	    left: 9px;
	    width: 411px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	    z-index: 999;
	}
	.guadualejo {
	    top: 209px;
	    left: 579px;
	    width: 166px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.guarico {
	    top: 304px;
	    left: 364px;
	    width: 92px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.hermosa {
	    top: 120px;
	    left: 548px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-irene {
	    top: 403px;
	    left: 689px;
	    width: 109px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-italia {
	    top: 352px;
	    left: 744px;
	    width: 147px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-ladera {
	    top: 330px;
	    left: 592px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-libia {
	    top: 300px;
	    left: 636px;
	    width: 136px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.la-linda {
	    top: 346px;
	    left: 516px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-rochela {
	    top: 482px;
	    left: 397px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.las-animas {
	    top: 234px;
	    left: 738px;
	    width: 98px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-sucia {
	    top: 312px;
	    left: 443px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.los-aguacates {
	    top: 347px;
	    left: 489px;
	    width: 83px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.media-luna {
	    top: 191px;
	    left: 573px;
	    width: 68px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.mercedes {
	    top: 164px;
	    left: 489px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.pedral-abajo {
	    top: 126px;
	    left: 465px;
	    width: 116px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.pedral-arriba {
	    top: 171px;
	    left: 398px;
	    width: 99px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.primavera {
	    top: 265px;
	    left: 384px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.santa-ana {
	    top: 339px;
	    left: 416px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.taparto {
	    top: 442px;
	    left: 408px;
	    width: 162px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.travesias {
	    top: 248px;
	    left: 440px;
	    width: 107px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 286px;
	    left: 431px;
	    width: 72px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
