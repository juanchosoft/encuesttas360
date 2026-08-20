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
	.aguadita-chiquita {
	    top: 406px;
	    left: 665px;
	    width: 196px;
	}
	.aguadita-grande {
		top: 490px;
	    left: 703px;
	    width: 192px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.alegrias {
		z-index: 999;
		top: 456px;
	    left: 631px;
	    width: 15px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1deg) brightness(20%) contrast(100%) ;
	}
	.barroblanco {
	    top: 196px;
	    left: 219px;
	    width: 125px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.buenos-aires {
		top: 271px;
	    left: 530px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.canas {
	    top: 488px;
	    left: 398px;
	    width: 156px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.chirapoto {
		top: 121px;
	    left: 711px;
	    width: 147px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.conde {
		top: 284px;
	    left: 146px;
	    width: 179px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-balso {
		top: 518px;
	    left: 611px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-cascada {
	    top: 496px;
	    left: 54px;
	    width: 341px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-esmeralda {
		z-index: 998;
		top: 440px;
	    left: 640px;
	    width: 68px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-frisolera {
		top: 526px;
	    left: 684px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-sirena {
		top: 390px;
	    left: 587px;
	    width: 183px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(77deg) brightness(100%) contrast(150%) ;
	}
	.la-union{
	    top: 441px;
	    left: 568px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.manzanares{
	    top: 178px;
	    left: 629px;
	    width: 143px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.naranjal {
	    top: 553px;
	    left: 674px;
	    width: 220px;
	}
	.olibales {
		top: 326px;
	    left: 7px;
	    width: 463px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.palmichal {
		top: 537px;
	    left: 418px;
	    width: 183px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.peladeros {
	    top: 304px;
	    left: 400px;
	    width: 156px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.san-pablo {
	    top: 438px;
	    left: 295px;
	    width: 142px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}	
	.san-antonio {
	    top: 308px;
	    left: 574px;
	    width: 202px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-jose {
		top: 358px;
	    left: 522px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.sucre {
	    top: 235px;
	    left: 597px;
	    width: 179px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(133deg) brightness(118%) contrast(200%) ;
	}
	.yarumalito {
	    top: 270px;
	    left: 467px;
	    width: 110px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}	
	.zona-urbana {
	    top: 444px;
	    left: 398px;
	    width: 76px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
