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
	.alto-de-la-virgen {
	    top: 79px;
	    left: 327px;
	    width: 111px;
	}
	.barro-blanco {
	    top: 582px;
	    left: 185px;
	    width: 134px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.canoas {
	    top: 585px;
	    left: 393px;
	    width: 115px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.brizuela {
	    top: 347px;
	    left: 200px;
	    width: 253px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.chaparral {
	    top: 522px;
	    left: 620px;
	    width: 136px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.colorado {
		top: 442px;
	    left: 588px;
	    width: 104px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-molino{
	    top: 136px;
	    left: 371px;
	    width: 124px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-palmar{
		top: 59px;
	    left: 533px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-salado {
		top: 278px;
	    left: 315px;
	    width: 104px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.garrido {
	    top: 630px;
	    left: 663px;
	    width: 108px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.guamito {
	    top: 578px;
	    left: 727px;
	    width: 39px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.guapante {
	    top: 184px;
	    left: 508px;
	    width: 223px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.hojas-anchas{
	    top: 496px;
	    left: 456px;
	    width: 118px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.juan-xxiii{
	    top: 439px;
	    left: 666px;
	    width: 70px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-charanga{
	    top: 304px;
	    left: 458px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-clara {
	    top: 350px;
	    left: 465px;
	    width: 152px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-enea {
	    top: 4px;
	    left: 576px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-honda{
	    top: 429px;
	    left: 262px;
	    width: 142px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}
	.la-mejia {
	    top: 165px;
	    left: 464px;
	    width: 92px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.la-mosca {
		top: 488px;
	    left: 539px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.la-mosquita {
		top: 622px;
	    left: 377px;
	    width: 216px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-pastorcita {
	    top: 146px;
	    left: 197px;
	    width: 151px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-hondita{
	    top: 400px;
	    left: 386px;
	    width: 83px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.montanez{
	    top: 237px;
	    left: 392px;
	    width: 128px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.piedras-blancas{
	    top: 266px;
	    left: 127px;
	    width: 217px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.romeral{
	    top: 146px;
	    left: 274px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-ignacio {
	    top: 667px;
	    left: 200px;
	    width: 186px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(10deg) brightness(100%) contrast(150%);
	}
	.san-isidro {
	    top: 225px;
	    left: 198px;
	    width: 135px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.san-jose{
		top: 486px;
	    left: 391px;
	    width: 105px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.toldas {
	    top: 588px;
	    left: 557px;
	    width: 133px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.yolombal {
		top: 76px;
	    left: 580px;
	    width: 153px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(1894deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 213px;
	    left: 354px;
	    width: 183px;
        filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>