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
	}
	.barbacoas{
	    top: 601px;
	    left: 495px;
	    width: 79px;
	}
	.bellavista {
	    top: 579px;
	    left: 430px;
	    width: 101px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cabecera-municipal {
	    top: 421px;
	    left: 370px;
	    width: 29px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-aura {
	    top: 402px;
	    left: 429px;
	    width: 99px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-paramo{
	    top: 263px;
	    left: 376px;
	    width: 81px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.candelaria {
	    top: 535px;
	    left: 394px;
	    width: 73px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.el-agrio {
	    top: 630px;
	    left: 410px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-popal {
	    top: 385px;
	    left: 388px;
	    width: 60px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.faldas-del-cafe {
	    top: 469px;
	    left: 288px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.guayabal {
	    top: 375px;
	    left: 286px;
	    width: 112px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.guayabal-pena{
	    top: 274px;
	    left: 487px;
	    width: 94px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.jerigua {
	    top: 467px;
	    left: 390px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-armenia {
		top: 4px;
	    left: 373px;
	    width: 78px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-bastilla {
	    top: 363px;
	    left: 556px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.la-guadua {
	    top: 334px;
	    left: 457px;
	    width: 120px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.las-faldas{
		top: 382px;
	    left: 368px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-lomas {
	    top: 432px;
	    left: 390px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.llano-del-pueblo {
		top: 444px;
	    left: 305px;
	    width: 70px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.lomitas {
	    top: 298px;
	    left: 559px;
	    width: 84px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.loma-del-sauce{
	    top: 662px;
	    left: 456px;
	    width: 70px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);	
	}
	.los-chorros {
	    top: 75px;
	    left: 286px;
	    width: 183px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.los-llanos {
	    top: 267px;
	    left: 284px;
	    width: 126px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.maderal {
	    top: 566px;
	    left: 384px;
	    width: 54px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.nueva-llanada {
		top: 460px;
	    left: 449px;
	    width: 158px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.portachuela {
	    top: 140px;
	    left: 249px;
	    width: 234px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.renegado-valle {
	    top: 476px;
	    left: 461px;
    	width: 155px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.montarron {
	    top: 296px;
	    left: 489px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.romeral-chamizo {
	    top: 456px;
	    left: 218px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.san-juan-de-renegado {
		top: 477px;
	    left: 432px;
	    width: 37px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.san-julian{
		top: 630px;
	    left: 493px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-juliancito {
	    top: 462px;
	    left: 342px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.san-mateo {
	    top: 317px;
	    left: 237px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.san-miguel {
	    top: 477px;
	    left: 362px;
	    width: 45px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.san-pablo{
	    top: 281px;
	    left: 401px;
	    width: 97px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.santa-agueda {
	    top: 488px;
	    left: 332px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.toldas {
	    top: 430px;
	    left: 266px;
	    width: 64px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.vega-del-ingles {
	    top: 49px;
	    left: 354px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 448px;
		left: 379px;
	    width: 13px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
