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
	.arenales {
	    top: 480px;
	    left: 475px;
	    width: 83px;
	}
	.blanquizal {
	    top: 289px;
	    left: 540px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(13deg) brightness(118%) contrast(119%);
	}
	.bosque-naranjo {
	    top: 456px;
	    left: 191px;
	    width: 229px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.campo-alegre {
	    top: 342px;
	    left: 514px;
	    width: 38px;
	    z-index: 998;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.chachafruto {
	    top: 563px;
	    left: 166px;
	    width: 247px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.charrascal {
	    top: 411px;
	    left: 327px;
	    width: 152px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.comunidad{
		top: 457px;
	    left: 486px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-brasil{
	    top: 208px;
	    left: 599px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-cedro {
	    top: 462px;
	    left: 550px;
	    width: 119px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.el-palon {
		top: 468px;
	    left: 384px;
	    width: 118px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-retiro {
		top: 95px;
	    left: 544px;
	    width: 89px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-socorro {
	    top: 383px;
	    left: 506px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.fatima{
		top: 371px;
	    left: 461px;
	    width: 63px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.filo-de-los-arboledas{
		top: 502px;
	    left: 467px;
	    width: 93px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.filo-de-san-jose{
	    top: 193px;
	    left: 448px;
	    width: 104px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(876%) hue-rotate(2834deg) brightness(100%) contrast(80%);
	}
	.guaybal {
	    top: 5px;
	    left: 380px;
	    width: 191px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-aguada {
	    top: 494px;
	    left: 597px;
	    width: 154px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-clara{
	    top: 537px;
	    left: 486px;
	    width: 81px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.la-esmeralda {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-holanda {
	    top: 306px;
	    left: 456px;
	    width: 73px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.la-quiebra {
		top: 250px;
	    left: 534px;
	    width: 46px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.la-renta {
	    top: 176px;
	    left: 644px;
	    width: 69px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.las-brisas {
	    top: 274px;
	    left: 572px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-esmeralda{
	    top: 376px;
	    left: 620px;
	    width: 126px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-suiza {
		top: 268px;
	    left: 663px;
	    width: 59px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.llano-de-santa-barbara {
	    top: 411px;
	    left: 450px;
	    width: 92px;
	    z-index: 998;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.los-pomos {
	    top: 343px;
	    left: 657px;
	    width: 58px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.murrapal {
	    top: 536px;
	    left: 507px;
	    width: 157px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.narino{
	    top: 394px;
	    left: 544px;
	    width: 115px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.quirimara-placitas{
	    top: 285px;
	    left: 151px;
	    width: 316px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.quirimara-rodeo {
	    top: 19px;
	    left: 173px;
	    width: 291px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.sagua {
	    top: 162px;
	    left: 602px;
	    width: 68px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.santander{
	    top: 356px;
	    left: 538px;
	    width: 125px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.sevilla {
	    top: 501px;
	    left: 433px;
	    width: 17px;
	    z-index: 9999;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.zarzal{
	    top: 416px;
	    left: 513px;
	    width: 80px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.zona-urbana {
	    top: 378px;
	    left: 491px;
	    width: 38px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
