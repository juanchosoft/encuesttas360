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
	.el-encanto {
	    z-index: 99;
	    top: 655px;
	    left: 104px;
	    width: 160px;
	}
	.el-pedrero {
	    top: 387px;
	    left: 345px;
	    width: 184px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.higuina {
	    top: 3px;
	    left: 417px;
	    width: 299px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(10deg) brightness(88%) contrast(119%) ;
	}
	.la-cejita {
	    z-index: 997;
	    top: 283px;
	    left: 273px;
	    width: 141px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-choclina {
	    z-index: 998;
	    top: 700px;
	    left: 322px;
	    width: 78px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-chuscalito {
	    top: 237px;
	    left: 59px;
	    width: 243px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-cienaga {
	    top: 387px;
	    left: 8px;
	    width: 195px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-cordillera {
	    top: 353px;
	    left: 165px;
	    width: 211px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.la-mata {
		top: 28px;
	    left: 233px;
	    width: 303px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-quiebra {
	    top: 466px;
	    left: 260px;
	    width: 187px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.las-lomitas {
	    top: 624px;
	    left: 220px;
	    width: 168px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.los-llanos {
	    top: 206px;
	    left: 291px;
	    width: 195px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.monterredondo {
	    top: 725px;
	    left: 253px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.nudillo {
	    top: 284px;
	    left: 331px;
	    width: 95px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}		
	.quiuna {
	    top: 535px;
	    left: 72px;
	    width: 315px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.valerio {
	    top: 335px;
	    left: 385px;
	    width: 111px;
	}
	.vendiagual {
	    top: 420px;
	    left: 349px;
	    width: 349px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 443px;
	    left: 614px;
	    width: 18px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>


