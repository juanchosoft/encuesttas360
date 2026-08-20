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
	    top: 411px;
	    left: 401px;
	    width: 27px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.balsora {
	    top: 66px;
	    left: 562px;
	    width: 65px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.berlin {
	    top: 236px;
	    left: 634px;
	    width: 83px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-llano {
		top: 205px;
    	left: 459px;
    	width: 55px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}

	.damas{
	    top: 337px;
	    left: 232px;
	    width: 176px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.el-bosque {
	    top: 431px;
	    left: 400px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.campo-alegre {
	    top: 141px;
	    left: 527px;
	    width: 41px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-carano {
		top: 231px;
	    left: 504px;
	    width: 70px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-carmelo {
		top: 266px;
	    left: 398px;
	    width: 97px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-condor {
	    top: 242px;
	    left: 231px;
	    width: 140px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.el-guamito {
	    top: 202px;
	    left: 398px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-jazmin {
	    top: 187px;
	    left: 600px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-limon {
		top: 182px;
	    left: 257px;
	    width: 101px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-palmar{
		top: 140px;
	    left: 606px;
	    width: 42px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.guamito{
	    top: 139px;
	    left: 606px;
	    width: 43px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.el-pinal {
	    top: 359px;
	    left: 224px;
	    width: 183px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-recreo {
		top: 151px;
	    left: 473px;
	    width: 64px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-roble {
	    top: 97px;
	    left: 515px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(38deg) brightness(100%) contrast(80%);
	}
	.la-espanola{
	    top: 467px;
	    left: 157px;
	    width: 204px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-zafiro {
	    top: 385px;
	    left: 366px;
	    width: 45px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-aguada {
	    top: 377px;
	    left: 519px;
	    width: 55px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.guadualito {
		top: 361px;
	    left: 245px;
	    width: 156px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.guamal {
	    top: 51px;
	    left: 607px;
	    width: 163px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.la-argentina {
	    top: 306px;
	    left: 380px;
	    width: 65px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-iguana {
	    top: 348px;
	    left: 455px;
	    width: 134px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-pedrera {
	    top: 312px;
	    left: 365px;
	    width: 33px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-esperanza {
		top: 116px;
	    left: 642px;
	    width: 51px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-valvanera {
	    top: 238px;
	    left: 311px;
	    width: 154px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-naranjos {
	    top: 310px;
	    left: 534px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-hermosa {
	    top: 435px;
	    left: 444px;
	    width: 92px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-linda {
	    top: 100px;
	    left: 309px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.media-cuesta {
	    top: 108px;
	    left: 422px;
	    width: 99px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.morro-azul {
	    top: 234px;
	    left: 575px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.las-mangas {
	    top: 180px;
	    left: 350px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.puente-linda {
	    top: 326px;
	    left: 607px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.rio-arriba {
		top: 176px;
	    left: 385px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.san-andres{
	    top: 193px;
	    left: 178px;
	    width: 203px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.puerto-venus {
	    top: 445px;
	    left: 379px;
	    width: 44px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.montecristo {
	    top: 545px;
	    left: 131px;
	    width: 167px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.quebrada-negra {
	    top: 424px;
	    left: 364px;
	    width: 42px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.nechi {
	    top: 271px;
	    left: 479px;
	    width: 84px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.quiebra-de-san-juan{
	    top: 148px;
	    left: 412px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.la-pinuela {
	    top: 300px;
	    left: 486px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.quiebra-honda {
	    top: 140px;
	    left: 342px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.san-miguel {
	    top: 101px;
	    left: 194px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.san-pedro-arriba {
		top: 69px;
	    left: 195px;
	    width: 157px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.quiebra-de-san-jose{
	    top: 114px;
	    left: 403px;
	    width: 53px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-rosa {
	    top: 162px;
	    left: 642px;
	    width: 69px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.vallejuelito {
	    top: 296px;
	    left: 323px;
	    width: 89px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.viboral {
	    top: 129px;
	    left: 319px;
	    width: 83px;
		filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-trinidad {
	    top: 4px;
	    left: 248px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-vega {
	    top: 409px;
	    left: 421px;
	    width: 16px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.la-veta {
	    top: 335px;
	    left: 678px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.san-miguel {
	    top: 6px;
	    left: 200px;
	    width: 207px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.villa-hermosa {
	    top: 369px;
	    left: 354px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-vicente {
	    top: 243px;
	    left: 498px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.venecia {
	    top: 406px;
	    left: 404px;
	    width: 70px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.uvital {
	    top: 152px;
	    left: 561px;
	    width: 53px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
	    top: 353px;
	    left: 423px;
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
	.san-pedro-abajo {
	    top: 304px;
	    left: 481px;
	    width: 144px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(58deg) brightness(100%) contrast(80%);
	}
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-pablo {
	    top: 330px;
	    left: 392px;
	    width: 74px;
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
	    top: 217px;
	    left: 448px;
	    width: 28px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
