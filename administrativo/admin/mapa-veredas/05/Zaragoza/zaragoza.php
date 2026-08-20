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
	}
	.aguas-prietas{
	    top: 162px;
	    left: 444px;
	    width: 99px;
	}
	.aqui-si {
	    top: 172px;
	    left: 293px;
	    width: 63px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.arenales {
	    top: 349px;
	    left: 45px;
	    width: 55px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.bocas-de-la-zorra {
	    top: 504px;
	    left: 611px;
	    width: 98px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.bocas-de-maestro-esteban{
	    top: 199px;
	    left: 258px;
	    width: 88px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.bagre-medio {
	    top: 623px;
	    left: 619px;
	    width: 135px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.bocas-de-cana {
		top: 627px;
	    left: 360px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.bocas-de-pocune {
	    top: 482px;
	    left: 558px;
	    width: 83px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.buenos-aires {
	    top: 188px;
	    left: 486px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.campo-alegre {
		top: 529px;
	    left: 699px;
	    width: 70px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.cana-medio{
	    top: 657px;
	    left: 347px;
	    width: 91px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.cano-la-ocho {
	    top: 104px;
	    left: 515px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.cano-la-tres {
	    top: 240px;
	    left: 456px;
	    width: 80px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.chilona-abajo {
	    top: 281px;
	    left: 371px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.chilona-medio {
	    top: 310px;
	    left: 265px;
	    width: 123px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.cimarron{
	    top: 376px;
	    left: 360px;
	    width: 40px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.cimarroncito {
		top: 388px;
	    left: 524px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.corderito {
	    top: 329px;
	    left: 537px;
	    width: 40px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.cordero {
	    top: 369px;
	    left: 482px;
	    width: 64px;
	}
	.cordero-icacales {
	    top: 308px;
	    left: 506px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-cincuenta {
	    top: 69px;
	    left: 316px;
	    width: 135px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-limon {
	    top: 454px;
	    left: 396px;
	    width: 64px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-pato {
	    top: 372px;
	    left: 281px;
	    width: 93px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-saltillo {
	    top: 708px;
	    left: 388px;
	    width: 110px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-tigre {
	    top: 358px;
	    left: 538px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-tigre-dos {
	    top: 501px;
	    left: 171px;
	    width: 102px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-retiro {
	    top: 558px;
	    left: 362px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-veinte {
	    top: 640px;
	    left: 401px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.escarralao {
	    top: 188px;
	    left: 357px;
	    width: 80px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.jala-jala{
		top: 495px;
	    left: 257px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-arenosa {
	    top: 439px;
	    left: 490px;
	    width: 134px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-doce {
	    top: 438px;
	    left: 322px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-maturana {
		top: 234px;
	    left: 321px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.la-porquera{
		top: 542px;
	    left: 409px;
	    width: 80px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-rebatina {
	    top: 592px;
	    left: 667px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.las-parcelas {
	    top: 93px;
	    left: 409px;
	    width: 81px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-tabla {
	    top: 372px;
	    left: 253px;
	    width: 34px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.limon-adentro {
		top: 461px;
	    left: 351px;
	    width: 58px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.maestro-esteban-cabecera {
	    top: 303px;
	    left: 130px;
	    width: 78px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.maestro-esteban-central {
	    top: 202px;
	    left: 130px;
	    width: 146px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.maestro-esteban-occidental {
	    top: 301px;
	    left: 185px;
	    width: 89px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.naranjal {
		top: 282px;
	    left: 428px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.nueva-ilusion {
	    top: 568px;
	    left: 463px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.pablo-muera {
	    top: 701px;
	    left: 486px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.pocune-abajo {
	    top: 581px;
	    left: 517px;
	    width: 93px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.pocune-medio {
	    top: 632px;
	    left: 523px;
	    width: 113px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%);
	}
	.porce-medio {
	    top: 728px;
	    left: 349px;
	    width: 74px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.pueblo-nuevo{
	    top: 505px;
	    left: 346px;
	    width: 28px;
	    z-index: 999;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.puerto-jobo {
	    top: 190px;
	    left: 426px;
	    width: 79px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.quebrada-pato {
	    top: 363px;
	    left: 166px;
	    width: 99px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.quebradona-dos {
	    top: 621px;
	    left: 462px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.quebradona-uno {
	    top: 371px;
	    left: 409px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.quinientos-cinco {
	    top: 65px;
	    left: 516px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(344deg) brightness(100%) contrast(80%);
	}
	.rio-viejo{
		top: 347px;
	    left: 382px;
	    width: 46px;
	}
	.san-acevedo{
	    top: 283px;
	    left: 55px;
	    width: 119px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-antonio {
		top: 490px;
	    left: 440px;
	    width: 97px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-juan-de-peluza {
	    top: 433px;
	    left: 207px;
	    width: 143px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.san-juan-de-popales {
		top: 435px;
	    left: 146px;
	    width: 88px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.san-pedro {
	    top: 590px;
	    left: 582px;
	    width: 86px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.tosnovan-dos {
		top: 112px;
	    left: 539px;
	    width: 68px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.tosnovan-uno {
	    top: 91px;
	    left: 468px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.vegas-de-segovia {
	    top: 404px;
	    left: 60px;
	    width: 124px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.vegas-de-zaragoza{
	    top: 4px;
	    left: 561px;
	    width: 63px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.vijagual-medio {
	    top: 286px;
	    left: 85px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.villa-amara {
		top: 538px;
	    left: 727px;
	    width: 109px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.villa-severa {
	    top: 604px;
	    left: 741px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    left: 408px;
	    width: 25px;
	    top: 435px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
