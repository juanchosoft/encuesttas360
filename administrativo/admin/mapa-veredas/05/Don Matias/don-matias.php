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
	.bellavista {
		top: 175px;
	    left: 588px;
    	width: 178px;
	}
	.colon {
	    top: 266px;
	    left: 124px;
	    width: 179px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.despensas {
	    top: 393px;
	    left: 281px;
	    width: 50px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.embalse-riogrande {
		top: 339px;
	    left: 78px;
	    width: 86px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.iborra {
		top: 460px;
	    left: 289px;
	    width: 103px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.la-frisolera {
	    top: 172px;
	    left: 708px;
	    width: 188px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-montera {
	    top: 287px;
	    left: 567px;
	    width: 181px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-piedrahita {
	    top: 392px;
	    left: 222px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-pradera {
	    top: 290px;
	    left: 672px;
	    width: 116px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.las-animas{
	    top: 486px;
	    left: 7px;
	    width: 265px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.miraflores {
	    top: 308px;
	    left: 320px;
	    width: 111px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.mocorongo {
	    top: 262px;
	    left: 392px;
	    width: 97px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.pan-de-azucar{
	    top: 253px;
	    left: 481px;
	    width: 114px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.quebrada-arriba {
		top: 388px;
	    left: 341px;
	    width: 121px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.riochico{
	    top: 370px;
	    left: 80px;
	    width: 149px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.riogrande {
	    top: 259px;
	    left: 284px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}	
	.riogrande-bellavista {
		top: 244px;
	    left: 559px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.romazon {
	    top: 547px;
	    left: 245px;
	    width: 85px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.santa-ana {
	    top: 332px;
	    left: 425px;
	    width: 172px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 443px;
	    left: 281px;
	    width: 54px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
