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
	    background-position: center;
	    width: 900px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.el-filo {
	    top: 34px;
	    left: 6px;
	    width: 428px;
	}
	.el-penol {
		top: 517px;
	    left: 611px;
	    width: 166px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(13deg) brightness(118%) contrast(119%);
	}
	.el-progreso {
	    top: 327px;
	    left: 355px;
	    width: 212px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.el-zancudo {
	    top: 490px;
	    left: 314px;
	    width: 322px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.embalse-riogrande {
	    top: 557px;
	    left: 540px;
	    width: 359px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.las-brisas {
	    top: 22px;
	    left: 315px;
	    width: 309px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.pio-xii{
		top: 157px;
	    left: 369px;
	    width: 247px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.rio-chico{
		top: 628px;
	    left: 504px;
	    width: 376px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.rio-grande {
		top: 494px;
	    left: 645px;
	    width: 215px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.tesorero {
	    top: 220px;
	    left: 492px;
	    width: 241px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.toruro {
	    top: 251px;
	    left: 170px;
	    width: 473px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.yerbabuenal {
	    top: 598px;
	    left: 421px;
	    width: 194px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-esmeralda{
	    top: 376px;
	    left: 620px;
	    width: 126px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 487px;
	    left: 609px;
	    width: 57px;
	    z-index: 99999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
