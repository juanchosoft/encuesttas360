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
	.aguacatal{
	    top: 281px;
	    left: 572px;
	    width: 127px;
	}
	.alto-los-fernandez {
		top: 4px;
	    left: 523px;
	    width: 129px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.buenos-aires {
		top: 187px;
	    left: 382px;
	    width: 96px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.combia-chiquita {
	    top: 207px;
	    left: 437px;
	    width: 87px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.combia-grande {
		top: 249px;
	    left: 480px;
	    width: 51px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.cadenas {
		top: 281px;
	    left: 278px;
	    width: 71px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.chamuscados {
		top: 254px;
	    left: 305px;
	    width: 86px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-calvario {
		top: 306px;
	    left: 502px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-carretero {
		top: 155px;
	    left: 532px;
	    width: 51px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-cinco{
	    top: 108px;
	    left: 457px;
	    width: 72px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-mango {
	    top: 162px;
	    left: 610px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-molino {
	    top: 289px;
	    left: 362px;
	    width: 74px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-plan {
		top: 185px;
	    left: 530px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-uvital {
	    top: 109px;
	    left: 592px;
	    width: 33px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-vainillo{
	    top: 213px;
	    left: 643px;
	    width: 34px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-zancudo {
		top: 259px;
	    left: 526px;
	    width: 148px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.hoyo-frio {
		top: 328px;
	    left: 448px;
	    width: 91px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.jonas {
	    top: 6px;
	    left: 439px;
	    width: 113px;
	}
	.la-garrucha {
	    top: 266px;
	    left: 386px;
	    width: 88px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-loma {
	    top: 184px;
	    left: 585px;
	    width: 56px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-maria {
	    top: 391px;
	    left: 554px;
	    width: 86px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-cordillera{
		top: 214px;
	    left: 336px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-mina {
	    top: 223px;
	    left: 231px;
	    width: 111px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-quiebra {
		top: 98px;
	    left: 619px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-toscana {
	    top: 106px;
	    left: 557px;
	    width: 39px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(54deg) brightness(118%) contrast(119%) ;
	}
	.marsella {
	    top: 381px;
	    left: 394px;
	    width: 172px;
	    filter: invert(85%) sepia(19%) saturate(276%) hue-rotate(229deg) brightness(118%) contrast(119%) ;
	}
	.morron {
	    top: 251px;
	    left: 185px;
	    width: 114px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(52deg) brightness(100%) contrast(80%) ;
	}
	.murrapal {
	    top: 287px;
	    left: 410px;
	    width: 71px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.naranjal {
		top: 229px;
	    left: 538px;
	    width: 139px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.palomos {
	    top: 73px;
	    left: 480px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.piedra-verde{
	    top: 36px;
	    left: 564px;
	    width: 151px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.porvenir {
	    top: 303px;
	    left: 418px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.puente-iglesias {
	    top: 343px;
	    left: 243px;
	    width: 472px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.raicero {
	    top: 348px;
	    left: 393px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.travesias {
		top: 151px;
	    left: 565px;
	    width: 33px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.zabaletas {
	    top: 144px;
	    left: 503px;
	    width: 38px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.zona-urbana {
	    top: 204px;
	    left: 477px;
	    width: 71px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
