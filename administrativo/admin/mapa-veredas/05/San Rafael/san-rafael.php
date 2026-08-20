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
	.agua-bonita{
	    top: 243px;
	    left: 568px;
	    width: 98px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-de-maria {
	    top: 317px;
	    left: 701px;
	    width: 74px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.balsas {
	    top: 401px;
	    left: 364px;
	    width: 34px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-charco {
	    top: 397px;
	    left: 246px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.camas {
	    top: 321px;
	    left: 184px;
	    width: 44px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.camelias{
	    top: 171px;
	    left: 642px;
	    width: 145px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.boqueron {
	    top: 387px;
	    left: 84px;
	    width: 88px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.cirpes {
	    top: 455px;
	    left: 4px;
	    width: 70px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.cuervos {
	    top: 355px;
	    left: 238px;
	    width: 99px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.dantas {
	    top: 296px;
	    left: 298px;
	    width: 72px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.danticas {
	    top: 309px;
	    left: 270px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.el-arenal {
	    top: 459px;
	    left: 323px;
	    width: 129px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-bizcocho {
	    top: 372px;
	    left: 157px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-brasil {
	    top: 435px;
	    left: 348px;
	    width: 94px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-chico{
	    top: 194px;
	    left: 503px;
	    width: 81px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-ingenio{
	    top: 116px;
	    left: 569px;
	    width: 149px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(309deg) brightness(100%) contrast(80%);
	}	
	.el-diamante {
	    top: 186px;
	    left: 541px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-golgota {
	    top: 249px;
	    left: 538px;
	    width: 78px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-guadual {
	    top: 454px;
	    left: 393px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.ite{
	    top: 538px;
	    left: 561px;
	    width: 104px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-silencio {
	    top: 374px;
	    left: 347px;
	    width: 57px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-topacio {
	    top: 168px;
	    left: 467px;
	    width: 45px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.el-totumito {
	    top: 444px;
	    left: 305px;
	    width: 59px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.embalse-playas {
	    top: 250px;
	    left: 365px;
	    width: 216px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.falditas {
	    top: 443px;
	    left: 161px;
	    width: 86px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.farallones {
	    top: 439px;
	    left: 103px;
	    width: 121px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.guadualito{
		top: 354px;
	    left: 142px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-clara {
	    top: 501px;
	    left: 85px;
	    width: 62px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-cumbre {
	    top: 352px;
	    left: 316px;
	    width: 55px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-dorada {
		top: 292px;
	    left: 768px;
	    width: 127px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-jague {
	    top: 217px;
	    left: 290px;
	    width: 145px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-estrella {
	    top: 493px;
	    left: 274px;
	    width: 71px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-florida {
	    top: 299px;
	    left: 521px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-granja {
	    top: 386px;
	    left: 621px;
	    width: 70px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-honda {
	    top: 436px;
	    left: 234px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-luz {
	    top: 202px;
	    left: 397px;
	    width: 148px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.manila {
		top: 361px;
	    left: 198px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.la-iraca{
		top: 262px;
	    left: 642px;
	    width: 47px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.la-pradera {
	    top: 438px;
	    left: 447px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-rapida {
	    top: 554px;
	    left: 241px;
	    width: 89px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.las-flores {
	    top: 218px;
	    left: 624px;
	    width: 115px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.los-centros{
	    top: 419px;
	    left: 417px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.los-medios {
	    top: 337px;
	    left: 98px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.macanal {
	    top: 520px;
	    left: 263px;
	    width: 83px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.media-cuesta {
	    top: 512px;
	    left: 16px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.penoles {
	    top: 495px;
	    left: 127px;
	    width: 68px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.las-divisas{
	    top: 228px;
	    left: 669px;
	    width: 75px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.piedras-arriba {
	    top: 268px;
	    left: 210px;
	    width: 101px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.playas {
	    top: 324px;
	    left: 362px;
	    width: 297px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.puente-tierra{
	    top: 129px;
	    left: 496px;
	    width: 92px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.quebradona{
	    top: 473px;
	    left: 126px;
	    width: 154px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.samaria {
	    top: 429px;
	    left: 8px;
	    width: 124px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.san-agustin {
	    top: 295px;
	    left: 344px;
	    width: 110px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-julian{
	    top: 220px;
	    left: 707px;
	    width: 180px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.tesorito{
	    top: 412px;
	    left: 436px;
	    width: 62px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.la-mesa {
	    top: 294px;
	    left: 574px;
	    width: 147px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-vicente {
	    top: 242px;
	    left: 273px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.santo-domingo {
		top: 314px;
	    left: 419px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-rita {
	    top: 700px;
	    left: 451px;
	    width: 95px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
		top: 297px;
	    left: 715px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.santa-barbara {
	    top: 217px;
	    left: 458px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.san-martin{
	    top: 642px;
	    left: 592px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-lorenzo {
		top: 257px;
	    left: 542px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 252px;
	    left: 361px;
	    width: 44px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.palmirita {
	    top: 334px;
	    left: 379px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.pailania {
	    top: 402px;
	    left: 562px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.zona-urbana{
	    top: 431px;
	    left: 314px;
	    width: 54px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
