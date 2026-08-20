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
	.aguacatal{
	    top: 237px;
	    left: 505px;
	    width: 64px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);		
	}
	.alto-los-gomez {
	    top: 201px;
	    left: 396px;
	    width: 89px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.buena-vista {
	    top: 123px;
	    left: 478px;
    	width: 66px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.camino-a-la-planta {
		top: 315px;
	    left: 513px;
	    width: 67px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(356deg) brightness(118%) contrast(119%);
	}
	.atanasio {
		top: 362px;
	    left: 503px;
	    width: 48px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.bellavista {
	    top: 410px;
	    left: 338px;
	    width: 153px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.cordoncillal {
	    top: 470px;
	    left: 466px;
	    width: 39px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.corozal {
	    top: 352px;
	    left: 453px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.cristo-rey {
	    top: 498px;
	    left: 497px;
	    width: 49px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.damasco{
		top: 525px;
	    left: 409px;
	    width: 131px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-buey {
	    top: 458px;
	    left: 479px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-guayabo {
	    top: 170px;
	    left: 430px;
	    width: 70px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-mango {
	    top: 174px;
	    left: 318px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-vergel {
	    top: 229px;
	    left: 433px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.guamal{
	    top: 411px;
	    left: 474px;
	    width: 41px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.guasimo {
	    top: 519px;
	    left: 494px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.helechal {
		top: 281px;
	    left: 402px;
	    width: 60px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-esperanza {
	    top: 460px;
	    left: 487px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-arcadia{
	    top: 34px;
	    left: 295px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);	
	}
	.la-liboriana {
	    top: 84px;
	    left: 446px;
	    width: 31px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.las-mercedes-2 {
	    top: 169px;
	    left: 332px;
	    width: 108px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.las-mercedes-1 {
	    top: 164px;
	    left: 372px;
	    width: 47px;
	    filter: invert(65%) sepia(89%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.loma-de-don-santos {
	    top: 378px;
	    left: 508px;
	    width: 98px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1206deg) brightness(88%) contrast(119%);
	}
	.loma-larga {
	    top: 298px;
	    left: 443px;
	    width: 59px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.los-charcos {
	    top: 276px;
	    left: 489px;
	    width: 67px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-umbria {
	    top: 543px;
	    left: 493px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.morro-plancho {
	    top: 123px;
	    left: 326px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.ojo-de-agua {
	    top: 141px;
	    left: 450px;
	    width: 70px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.palo-coposo{
	    top: 236px;
	    left: 487px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.paso-de-la-palma {
		top: 274px;
	    left: 532px;
	    width: 44px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.pitayo {
	    top: 100px;
	    left: 387px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.poblanco {
		top: 271px;
	    left: 329px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.primavera {
	    top: 348px;
	    left: 540px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2229deg) brightness(118%) contrast(200%);
	}	
	.quiebra-de-guamito {
		top: 371px;
	    left: 489px;
	    width: 29px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.quiebra-del-barro {
	    top: 125px;
	    left: 438px;
	    width: 50px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.san-isidro-parte-baja{
	    top: 201px;
	    left: 477px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-jose {
		top: 304px;
	    left: 490px;
	    width: 80px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.san-miguelito {
		top: 327px;
	    left: 520px;
	    width: 75px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.tablaza {
	    top: 160px;
	    left: 489px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.ursula {
	    top: 271px;
	    left: 447px;
	    width: 46px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.versalles {
	    top: 3px;
	    left: 368px;
	    width: 115px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.yarumalito {
		top: 93px;
	    left: 469px;
	    width: 38px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.zona-urbana {
	    top: 305px;
	    left: 473px;
	    width: 59px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
