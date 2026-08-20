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
	    height: 700px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.el-guayabo{
	    top: 212px;
	    left: 76px;
	    width: 342px;
	    filter: invert(48%) sepia(79%) saturate(106%) hue-rotate(331deg) brightness(118%) contrast(119%);
	}
	.la-bermejala {
	    top: 377px;
	    left: 149px;
	    width: 361px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-culebra {
		top: 444px;
	    left: 250px;
	    width: 238px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.la-matica {
		top: 200px;
	    left: 340px;
	    width: 149px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-raya {
	    top: 558px;
	    left: 435px;
	    width: 81px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.la-tablacita{
	    top: 243px;
	    left: 545px;
	    width: 255px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.la-tablaza {
	    top: 475px;
	    left: 449px;
	    width: 83px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.penas-blancas {
	    top: 410px;
	    left: 450px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.pueblo-viejo {
	    top: 219px;
	    left: 92px;
	    width: 448px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.sagrada-familia {
	    top: 545px;
	    left: 514px;
	    width: 234px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-isidro {
	    top: 524px;
	    left: 578px;
	    width: 259px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.san-jose {
	    top: 4px;
	    left: 63px;
	    width: 355px;
	    filter: invert(48%) sepia(19%) saturate(1576%) hue-rotate(334deg) brightness(118%) contrast(200%);
	}	
	.san-miguel {
	    top: 516px;
	    left: 204px;
	    width: 287px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.tierra-amarilla {
	    top: 161px;
	    left: 63px;
	    width: 314px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.zona-urbana {
	    top: 44px;
	    left: 353px;
	    width: 280px;
	    filter: invert(13%) sepia(49%) saturate(273%) hue-rotate(165deg) brightness(119%) contrast(119%);
	}
</style>
