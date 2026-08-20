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
	.camilo-c {
	    top: 407px;
	    left: 363px;
	    width: 208px;
	}
	.el-cedro {
	    top: 56px;
	    left: 147px;
	    width: 296px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.el-morro {
	    top: 410px;
	    left: 521px;
	    width: 110px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(10deg) brightness(88%) contrast(119%);
	}
	.guaimaral {
	    z-index: 997;
	    top: 517px;
	    left: 312px;
	    width: 233px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.guali {
	    z-index: 998;
	    top: 191px;
	    left: 422px;
	    width: 108px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-clarita-1 {
	    top: 26px;
	    left: 488px;
	    width: 130px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}	
	.la-clarita-2 {
	    top: 38px;
	    left: 439px;
	    width: 75px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-delgadita {
	    top: 557px;
	    left: 664px;
	    width: 83px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%);
	}
	.la-ferreria {
	    top: 135px;
	    left: 373px;
	    width: 111px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-penas {
	    top: 314px;
	    left: 589px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.los-bolivares {
	    z-index: 999;
	    top: 331px;
	    left: 202px;
	    width: 166px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.los-sanchez {
	    top: 494px;
	    left: 260px;
	    width: 251px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.mal-abrigo {
	    top: 264px;
	    left: 325px;
	    width: 137px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.mani-del-cardal {
	    top: 335px;
	    left: 630px;
	    width: 110px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}			
	.minas {
	    top: 4px;
	    left: 475px;
	    width: 212px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.nechi {
	    z-index: 997;
	    top: 502px;
	    left: 521px;
	    width: 156px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.nicanor-restrepo {
	    z-index: 998;
	    top: 47px;
	    left: 568px;
	    width: 157px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.pie-de-cuesta {
	    top: 181px;
	    left: 493px;
	    width: 173px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}	
	.san-jose {
	    top: 393px;
	    left: 7px;
	    width: 381px;
	    filter: invert(78%) sepia(99%) saturate(396%) hue-rotate(2006deg) brightness(88%) contrast(119%);
	}
	.travesias {
	    top: 181px;
	    left: 216px;
	    width: 137px;
	    filter: invert(78%) sepia(99%) saturate(596%) hue-rotate(1206deg) brightness(88%) contrast(119%);
	}
	.yarumal {
	    top: 431px;
	    left: 621px;
	    width: 168px;
	    filter: invert(78%) sepia(99%) saturate(196%) hue-rotate(106deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
	    top: 297px;
	    left: 434px;
	    width: 88px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}

</style>


