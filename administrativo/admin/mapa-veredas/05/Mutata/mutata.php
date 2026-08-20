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
	.aguas-claras {
	    top: 469px;
	    left: 461px;
	    width: 81px;
	}
	.antazales {
	    top: 387px;
	    left: 17px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.bedo-pinal {
		top: 573px;
	    left: 541px;
	    width: 98px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%) ;
	}
	.bejuquillo {
	    top: 342px;
	    left: 359px;
	    width: 90px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%) ;
	}
	.belen-de-bajira {
	    top: 332px;
	    left: 80px;
	    width: 86px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.canaduzales {
	    top: 451px;
	    left: 513px;
	    width: 77px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}	
	.caucheras{
		top: 363px;
	    left: 410px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.chado {
	    top: 231px;
	    left: 344px;
	    width: 52px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.chado-arriba {
	    top: 212px;
	    left: 378px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.chado-la-raya {
		top: 195px;
	    left: 269px;
	    width: 111px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.chontadural{
		top: 490px;
	    left: 368px;
	    width: 155px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.chontaduralito{
	    top: 582px;
	    left: 410px;
	    width: 156px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.eugenia-arriba{
		top: 280px;
	    left: 6px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(634deg) brightness(100%) contrast(80%) ;
	}
	.jurado {
	    top: 148px;
	    left: 318px;
	    width: 59px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%) ;
	}
	.jurado-arriba {
		top: 67px;
	    left: 344px;
	    width: 94px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.la-fortuna{
		top: 185px;
	    left: 241px;
	    width: 117px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.la-primavera{
	    top: 359px;
	    left: 7px;
	    width: 89px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-secreta {
	    top: 438px;
	    left: 271px;
	    width: 86px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(482deg) brightness(88%) contrast(119%) ;
	}
	.las-malvinas{
		top: 467px;
	    left: 349px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.leoncito {
		top: 266px;
	    left: 208px;
	    width: 169px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.leon-porroso {
	    top: 276px;
	    left: 271px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2129deg) brightness(118%) contrast(200%) ;
	}
	.los-cedros {
		top: 314px;
	    left: 147px;
	    width: 110px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.monteria-leon {
	    top: 189px;
	    left: 209px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.mutata {
	    top: 548px;
	    left: 514px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.mutatacito {
	    top: 502px;
	    left: 530px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}	
	.mungudo {
	    top: 386px;
	    left: 326px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.nueva-esperanza {
		top: 281px;
	    left: 95px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.nuevo-mundo {
	    top: 290px;
	    left: 336px;
	    width: 116px;
	}
	.la-urraena {
	    top: 227px;
	    left: 98px;
	    width: 143px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.palmichal {
	    top: 269px;
	    left: 182px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.parque-natural-paramillo {
	    top: 116px;
	    left: 421px;
	    width: 476px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.pavarando {
	    top: 470px;
	    left: 327px;
	    width: 55px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.pavarandocito {
	    top: 439px;
	    left: 388px;
	    width: 86px;
	    filter: invert(150%) sepia(109%) saturate(211%) hue-rotate(18046deg) brightness(118%) contrast(119%) ;
	}
	.porroso {
	    top: 253px;
	    left: 338px;
	    width: 141px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.san-jose-de-leon {
	    top: 88px;
	    left: 365px;
	    width: 103px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.surrambay {
	    top: 407px;
	    left: 459px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.tierradentro {
		top: 326px;
	    left: 6px;
	    width: 65px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.villa-arteaga {
	    top: 304px;
	    left: 424px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.villa-del-carmen {
	    top: 286px;
	    left: 51px;
	    width: 83px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.villa-luz {
	    top: 378px;
	    left: 117px;
	    width: 230px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 555px;
	    left: 527px;
	    width: 17px;
	    z-index: 9999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
