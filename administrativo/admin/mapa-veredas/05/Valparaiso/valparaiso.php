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
	.comuna-la-virgen {
	    top: 513px;
	    left: 304px;
	    width: 119px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(42deg) brightness(118%) contrast(119%);
	}
	.el-bosque {
	    top: 582px;
	    left: 350px;
	    width: 89px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.el-guayabo {
	    top: 681px;
	    left: 224px;
	    width: 79px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-libano {
	    top: 689px;
	    left: 391px;
	    width: 117px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.el-pescadero {
	    top: 304px;
	    left: 287px;
	    width: 57px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.itima{
	    top: 101px;
	    left: 294px;
	    width: 173px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-aurora{
	    top: 516px;
	    left: 252px;
	    width: 115px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.la-barca {
	    top: 335px;
	    left: 474px;
	    width: 202px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.la-bocana {
		top: 193px;
	    left: 388px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-fabiana {
		top: 289px;
	    left: 333px;
	    width: 198px;
	    filter: invert(48%) sepia(137%) saturate(176%) hue-rotate(73deg) brightness(118%) contrast(119%);
	}
	.la-graciela {
	    top: 618px;
	    left: 441px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-meseta{
	    top: 574px;
	    left: 338px;
	    width: 50px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-miel{
	    top: 611px;
	    left: 259px;
	    width: 52px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-paz{
	    top: 585px;
	    left: 302px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-placita {
		top: 626px;
	    left: 321px;
	    width: 98px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(350deg) brightness(100%) contrast(150%);
	}
	.las-sardinas {
	    top: 494px;
	    left: 410px;
	    width: 66px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.machonta{
	    top: 464px;
	    left: 445px;
	    width: 181px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.mallarino {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.parcelacion-montenegro {
	    top: 7px;
	    left: 328px;
	    width: 137px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.playa-rica {
	    top: 620px;
	    left: 225px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 351px;
	    left: 286px;
	    width: 78px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.santa-ana {
	    top: 622px;
	    left: 396px;
	    width: 72px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.mallarino{
		top: 663px;
	    left: 277px;
	    width: 166px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 590px;
	    left: 422px;
	    width: 46px;
	    z-index: 9999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
