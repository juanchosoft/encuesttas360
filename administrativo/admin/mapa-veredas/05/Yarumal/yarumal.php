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
	.aguacatal{
	    top: 214px;
	    left: 540px;
	    width: 71px;
	}
	.canaveral {
	    top: 498px;
	    left: 661px;
	    width: 43px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.cedeno {
	    top: 253px;
	    left: 608px;
	    width: 90px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.chorros-blancos-arriba {
	    top: 338px;
	    left: 549px;
	    width: 73px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chorros-blancos-abajo {
	    top: 472px;
	    left: 601px;
	    width: 53px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.chorros-blancos-medio {
		top: 433px;
	    left: 572px;
	    width: 84px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.corcovado {
	    top: 260px;
	    left: 522px;
	    width: 99px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-cedro {
	    top: 6px;
	    left: 627px;
	    width: 123px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-hormiguero {
	    top: 296px;
	    left: 588px;
	    width: 89px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-llano-yolombal{
	    top: 382px;
	    left: 235px;
	    width: 41px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-pueblito {
	    top: 91px;
	    left: 601px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-resplandor {
	    top: 497px;
	    left: 485px;
	    width: 84px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-retiro {
		top: 519px;
	    left: 391px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.espiritu-santo {
	    top: 406px;
	    left: 320px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.jose-maria-cordoba{
	    top: 446px;
	    left: 566px;
	    width: 90px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-argentina {
	    top: 528px;
	    left: 285px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-bella {
	    top: 567px;
	    left: 437px;
	    width: 65px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-bramadora {
		top: 362px;
	    left: 610px;
	    width: 36px;
	}
	.la-candelaria {
		top: 314px;
	    left: 463px;
	    width: 62px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-carolina {
	    top: 194px;
	    left: 590px;
	    width: 53px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-ceja {
		top: 302px;
	    left: 215px;
	    width: 42px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-conspiracion {
	    top: 130px;
	    left: 578px;
	    width: 106px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-esmeralda {
		top: 368px;
	    left: 234px;
	    width: 117px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-estrella {
		top: 513px;
	    left: 626px;
	    width: 39px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-gabriela {
	    top: 486px;
	    left: 320px;
	    width: 105px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-loma {
	    top: 328px;
	    left: 149px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-pailita {
		top: 333px;
	    left: 647px;
	    width: 40px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-piedra{
	    top: 715px;
	    left: 385px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.las-cruces {
		top: 312px;
	    left: 204px;
    	width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-siria {
		top: 517px;
	    left: 561px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-teresita {
	    top: 610px;
	    left: 437px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-torre{
	    top: 340px;
	    left: 628px;
	    width: 54px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-zorra {
	    top: 384px;
	    left: 198px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.llanos-de-cuiva {
	    top: 658px;
	    left: 410px;
	    width: 61px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.loma-de-ochali {
		top: 363px;
	    left: 225px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.mallarino {
	    top: 500px;
	    left: 612px;
	    width: 53px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.media-luna {
	    top: 61px;
	    left: 635px;
	    width: 88px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.mina-vieja {
	    top: 365px;
	    left: 457px;
	    width: 109px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.montebello {
	    top: 181px;
	    left: 567px;
	    width: 54px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.ochali{
	    top: 389px;
	    left: 221px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.rio-abajo {
	    top: 313px;
	    left: 664px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.rosarito {
	    top: 418px;
	    left: 422px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.san-antonio {
		top: 578px;
	    left: 359px;
	    width: 106px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.san-roque {
		top: 326px;
	    left: 391px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.santa-isabel {
	    top: 605px;
	    left: 257px;
	    width: 172px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.santa-juana{
	    top: 491px;
	    left: 415px;
	    width: 118px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.santa-rita {
	    top: 497px;
	    left: 566px;
	    width: 76px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.tobon {
	    top: 313px;
	    left: 469px;
	    width: 135px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.ventanas {
	    top: 269px;
	    left: 428px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.vereda-cabecera-municipal {
	    top: 438px;
	    left: 531px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.yarumalito {
		top: 452px;
	    left: 414px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1834deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
		top: 454px;
	    left: 535px;
	    width: 33px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
