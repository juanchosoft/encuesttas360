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
	.arenal {
	    top: 347px;
	    left: 291px;
	    width: 156px;
	}
	.belen {
	    top: 750px;
	    left: 663px;
	    width: 22px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.briceno {
	    top: 84px;
	    left: 157px;
	    width: 108px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.boca-de-vidri {
	    top: 720px;
	    left: 596px;
	    width: 85px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.buchado {
	    top: 389px;
	    left: 351px;
	    width: 207px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.cabecera-municipal {
	    top: 214px;
	    left: 233px;
	    width: 275px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.gengado{
	    top: 328px;
	    left: 431px;
	    width: 138px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1172deg) brightness(100%) contrast(80%);
	}
	.guaguando{
	    top: 512px;
	    left: 507px;
	    width: 158px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.isleta {
	    top: 733px;
	    left: 709px;
	    width: 45px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.jarapeto {
	    top: 316px;
	    left: 548px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-playa {
	    top: 254px;
	    left: 449px;
	    width: 117px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.loma-murri {
	    top: 239px;
	    left: 547px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.palo-blanco{
	    top: 678px;
	    left: 404px;
	    width: 95px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.partado{
		top: 501px;
	    left: 501px;
	    width: 147px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.playita{
	    top: 650px;
	    left: 519px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.pueblo-nuevo {
	    top: 229px;
	    left: 484px;
	    width: 84px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.puerto-antioquia {
	    top: 6px;
	    left: 147px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.puerto-medellin{
	    top: 695px;
	    left: 474px;
	    width: 73px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}
	.puerto-palacio{
		top: 634px;
	    left: 579px;
	    width: 89px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}	
	.salado {
	    top: 419px;
	    left: 486px;
	    width: 165px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.san-alejandro{
	    top: 137px;
	    left: 185px;
	    width: 102px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.san-antonio-de-padua {
	    top: 510px;
	    left: 357px;
	    width: 156px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.san-martin {
	    top: 264px;
	    left: 271px;
	    width: 106px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.san-miguel {
	    top: 302px;
	    left: 284px;
	    width: 156px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.santa-maria {
		top: 596px;
	    left: 391px;
	    width: 164px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.vegaez {
	    top: 729px;
	    left: 681px;
	    width: 45px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.villa-nueva{
	    top: 218px;
	    left: 248px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.vuelta-cortada{
	    top: 262px;
	    left: 369px;
	    width: 110px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	
</style>