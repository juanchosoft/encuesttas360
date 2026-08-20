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
	.aguacates{
		top: 579px;
	    left: 729px;
	    width: 44px;
	}
	.aguas-chiquitas {
	    top: 577px;
	    left: 539px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(401deg) brightness(118%) contrast(119%);
	}
	.area-sin-levantar {
	    top: 3px;
	    left: 71px;
	    width: 476px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-chuscal {
	    top: 152px;
	    left: 644px;
	    width: 123px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1082deg) brightness(88%) contrast(119%);
	}
	.el-esclavo{
	    top: 281px;
	    left: 499px;
	    width: 75px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.arenales {
	    top: 540px;
	    left: 685px;
	    width: 90px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.chaque {
	    top: 303px;
	    left: 565px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-escobero {
	    top: 260px;
	    left: 477px;
	    width: 32px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-hato {
	    top: 469px;
	    left: 504px;
	    width: 143px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-indio {
		top: 359px;
	    left: 637px;
	    width: 27px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-llano{
	    top: 343px;
	    left: 607px;
	    width: 61px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-maravillo {
	    top: 39px;
	    left: 604px;
	    width: 137px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-narcizo {
	    top: 282px;
	    left: 442px;
	    width: 60px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-paso {
		top: 270px;
	    left: 647px;
	    width: 29px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-porvenir {
	    top: 452px;
	    left: 585px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.el-saladito {
		top: 280px;
	    left: 625px;
	    width: 43px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-salado {
	    top: 183px;
	    left: 547px;
	    width: 133px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-salvador {
	    top: 445px;
	    left: 652px;
	    width: 60px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-topacio {
	    top: 282px;
	    left: 495px;
	    width: 19px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-volcan {
	    top: 402px;
	    left: 597px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.guapante {
	    top: 270px;
	    left: 669px;
	    width: 36px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-ana {
	    top: 288px;
	    left: 690px;
	    width: 136px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-cartagena {
	    top: 511px;
	    left: 624px;
	    width: 56px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-despensa {
		top: 549px;
	    left: 528px;
	    width: 111px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-florida {
	    top: 342px;
	    left: 584px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-guayabala {
	    top: 306px;
	    left: 500px;
	    width: 21px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-clara {
	    top: 7px;
	    left: 533px;
	    width: 141px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2209deg) brightness(118%) contrast(200%);
	}	
	.la-lucia {
	    top: 347px;
	    left: 640px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-magdalena {
		top: 371px;
	    left: 539px;
	    width: 100px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-matanza{
	    top: 340px;
	    left: 503px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-primavera {
		top: 480px;
	    left: 587px;
	    width: 61px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.las-mercedes {
	    top: 459px;
	    left: 666px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-venta {
	    top: 305px;
	    left: 600px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.llano-grande{
	    top: 610px;
	    left: 543px;
	    width: 136px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-animes {
	    top: 233px;
	    left: 437px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.los-barrancos {
		top: 223px;
	    left: 506px;
	    width: 44px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.mande {
		top: 203px;
	    left: 177px;
	    width: 7px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.nendo {
	    top: 349px;
	    left: 339px;
	    width: 6px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.orobugo-alto {
	    top: 336px;
	    left: 450px;
	    width: 102px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.orobugo-bajo {
	    top: 284px;
	    left: 500px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.orobugo-medio {
	    top: 318px;
	    left: 493px;
	    width: 55px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.parque-natural-las-orquideas{
	    top: 14px;
	    left: 344px;
	    width: 238px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.penderisco-arriba {
	    top: 561px;
	    left: 734px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.pringamosal {
	    top: 462px;
	    left: 642px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.quebradona {
	    top: 493px;
	    left: 695px;
	    width: 81px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.sabanas {
	    top: 264px;
	    left: 505px;
	    width: 51px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%);
	}
	.san-agustin {
	    top: 383px;
	    left: 636px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.san-bartolo{
	    top: 166px;
	    left: 537px;
	    width: 97px;
	    z-index: 999;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-carlos {
	    top: 535px;
	    left: 670px;
	    width: 41px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.san-joaquin {
	    top: 655px;
	    left: 611px;
	    width: 67px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.san-jose {
	    top: 397px;
	    left: 635px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-jose-limite-sin-definir{
	    top: 143px;
	    left: 535px;
	    width: 27px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-jose-montanitas {
	    top: 335px;
	    left: 707px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-matias {
	    top: 234px;
	    left: 504px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(344deg) brightness(100%) contrast(80%);
	}
	.san-rafael{
	    top: 208px;
	    left: 530px;
	    width: 52px;
	}
	.santa-ana{
	    top: 632px;
	    left: 618px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.santa-catalina {
	    top: 529px;
	    left: 514px;
	    width: 128px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-isabel {
	    top: 585px;
	    left: 673px;
	    width: 71px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
	    top: 356px;
	    left: 628px;
	    width: 25px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
