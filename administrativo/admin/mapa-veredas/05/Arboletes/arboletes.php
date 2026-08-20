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
	    width: 800px;
	    height: 900px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.atoyosa{
		top: 361px;
	    left: 337px;
	    width: 77px;
	}
	.bajo-grande {
		top: 72px;
	    left: 256px;
	    width: 218px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.bajos-la-arenosa {
		top: 236px;
	    left: 385px;
	    width: 45px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.buenos-aires {
	    top: 520px;
	    left: 629px;
	    width: 81px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.barrancuda {
		top: 5px;
	    left: 377px;
	    width: 72px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.boca-al-reves {
	    top: 744px;
	    left: 271px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.cajones {
	    top: 541px;
	    left: 88px;
	    width: 125px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.calabozo {
		top: 539px;
	    left: 560px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.campanito {
	    top: 695px;
	    left: 296px;
	    width: 101px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.canime{
	    top: 170px;
	    left: 379px;
	    width: 65px;
    	filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.cerro-las-lajas {
	    top: 166px;
	    left: 434px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-bonguito {
		top: 505px;
	    left: 291px;
	    width: 84px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-carmelo {
	    top: 451px;
	    left: 386px;
	    width: 122px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-caucho {
		top: 536px;
	    left: 160px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-coco{
		top: 220px;
	    left: 310px;
	    width: 70px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-guadual {
		top: 775px;
	    left: 408px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.el-guaimaro {
	    top: 69px;
	    left: 384px;
	    width: 81px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-inglesito {
	    top: 586px;
	    left: 375px;
	    width: 38px;
	}
	.el-porvenir {
		top: 811px;
	    left: 409px;
	    width: 88px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-socorro {
		top: 199px;
	    left: 325px;
	    width: 60px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-tambo {
	    top: 787px;
	    left: 248px;
	    width: 69px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-volcan {
	    top: 600px;
	    left: 222px;
	    width: 115px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-volcancito{
	    top: 684px;
	    left: 246px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.el-yeso {
		top: 500px;
	    left: 238px;
	    width: 78px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.filo-de-venus {
		top: 311px;
	    left: 301px;
	    width: 75px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.garrapata {
	    top: 440px;
	    left: 451px;
	    width: 102px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.guadual-abajo {
	    top: 673px;
	    left: 409px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.guadual-del-medio {
		top: 742px;
	    left: 421px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.holanda{
	    top: 419px;
	    left: 358px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.holandita {
	    top: 419px;
	    left: 342px;
	    width: 79px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-arenosa {
	    top: 603px;
	    left: 380px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-arenosa-baja {
		top: 752px;
	    left: 493px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-candelaria{
	    top: 411px;
	    left: 243px;
	    width: 136px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-caridad {
	    top: 559px;
	    left: 520px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-mesa {
	    top: 683px;
	    left: 391px;
	    width: 35px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(128deg) brightness(119%) contrast(119%) ;
	}
	.la-mesita {
		top: 622px;
	    left: 394px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.las-lanas {
	    top: 575px;
	    left: 448px;
	    width: 109px;
	    filter: invert(180%) sepia(89%) saturate(276%) hue-rotate(355deg) brightness(100%) contrast(100%) ;
	}
	.las-naranjitas {
		top: 618px;
	    left: 480px;
	    width: 121px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.las-parcelas {
	    top: 540px;
	    left: 692px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.las-patillas {
	    top: 424px;
	    left: 286px;
	    width: 32px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.las-pavitas{
	    top: 150px;
	    left: 323px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.las-platas {
		top: 571px;
	    left: 292px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.la-trinidad {
		top: 453px;
	    left: 143px;
	    width: 158px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.la-velez {
	    top: 727px;
	    left: 484px;
	    width: 64px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.marsella {
	    top: 634px;
	    left: 441px;
	    width: 34px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(242deg) brightness(118%) contrast(119%) ;
	}
	.nueva-florida {
		top: 345px;
	    left: 451px;
	    width: 64px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.nuevo-oriente{
		top: 727px;
	    left: 239px;
	    width: 45px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.pajillal {
	    top: 205px;
	    left: 272px;
	    width: 126px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.pangola {
	    top: 383px;
	    left: 271px;
	    width: 80px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.paraiso {
		top: 443px;
	    left: 222px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.pelayo {
	    top: 560px;
	    left: 572px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.piedrecitas {
		top: 295px;
	    left: 393px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1834deg) brightness(100%) contrast(80%) ;
	}
	.plan-parejo {
	    top: 467px;
	    left: 612px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.platas-arriba {
		top: 726px;
	    left: 309px;
	    width: 117px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.platas-del-medio {
	    top: 617px;
	    left: 324px;
	    width: 79px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.pueblo-chino {
		top: 379px;
	    left: 387px;
	    width: 132px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.san-carlos {
		top: 607px;
	    left: 166px;
	    width: 69px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.san-jose{
	    top: 556px;
	    left: 397px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.san-juancito-vijao{
		top: 645px;
	    left: 187px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.san-rafael {
		top: 614px;
	    left: 116px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.siete-hermanas{
	    top: 296px;
	    left: 363px;
	    width: 132px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.trementino {
	    top: 469px;
	    left: 483px;
	    width: 152px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
		top: 59px;
	    left: 355px;
	    width: 36px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
