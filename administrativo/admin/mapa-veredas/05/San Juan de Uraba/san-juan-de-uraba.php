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
	    	<?php include "../mapa-veredas/mapa_veredas.php" ?>
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
	    width: 950px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.alto-bonito{
	    top: 172px;
	    left: 582px;
	    width: 65px;
	}
	.balsilla {
	    top: 313px;
	    left: 286px;
	    width: 95px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.belen {
	    top: 221px;
	    left: 297px;
	    width: 148px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.bocas-del-rio {
	    top: 97px;
	    left: 472px;
	    width: 143px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.boca-tapada{
	    top: 448px;
	    left: 505px;
	    width: 137px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.calle-larga {
		top: 323px;
	    left: 607px;
	    width: 138px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.cana-brava {
	    top: 4px;
	    left: 583px;
	    width: 238px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.chapales-de-uveros {
	    top: 267px;
	    left: 246px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.damaquiel {
		z-index: 9999;
	    top: 287px;
	    left: 194px;
	    width: 12px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-arquillo{
	    top: 708px;
	    left: 399px;
	    width: 104px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-castillo {
	    top: 216px;
	    left: 635px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-cedrito {
		top: 671px;
	    left: 324px;
	    width: 66px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-coco {
	    top: 164px;
	    left: 560px;
	    width: 151px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-descanso {
	    top: 371px;
	    left: 194px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-tigre{
	    top: 478px;
	    left: 639px;
	    width: 73px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(704deg) brightness(118%) contrast(119%) ;
	}
	.entra-si-puedes {
	    top: 294px;
	    left: 174px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%) ;
	}
	.filo-de-san-jose {
	    top: 252px;
	    left: 638px;
	    width: 208px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.islaboa {
	    top: 517px;
	    left: 199px;
	    width: 70px;
	}
	.la-mugrosa{
		top: 495px;
	    left: 319px;
	    width: 132px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.las-lajas {
	    top: 252px;
	    left: 558px;
	    width: 86px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.filo-de-damaquiel {
	    top: 326px;
	    left: 236px;
	    width: 281px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(491deg) brightness(118%) contrast(119%) ;
	}	
	.las-pachacas {
	    top: 509px;
	    left: 431px;
	    width: 152px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.los-chapales{
	    top: 272px;
	    left: 194px;
	    width: 80px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.los-volcanes{
		top: 653px;
	    left: 305px;
	    width: 109px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.montebello {
		top: 40px;
	    left: 545px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.piedra-afilada {
	    top: 637px;
	    left: 379px;
	    width: 105px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.placitas {
		top: 421px;
	    left: 284px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.pueblito {
	    top: 264px;
	    left: 271px;
	    width: 82px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.sabanilla{
		top: 289px;
	    left: 105px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.montecristo {
	    top: 226px;
	    left: 474px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(58deg) brightness(100%) contrast(80%) ;
	}
	.san-juancito {
	    top: 273px;
	    left: 385px;
	    width: 284px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.san-nicolas-del-rio {
		top: 342px;
	    left: 234px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.siete-vueltas {
	    top: 650px;
	    left: 405px;
	    width: 19px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%) ;
	}
	.sinai{
	    top: 287px;
	    left: 346px;
	    width: 69px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.subterraneo {
	    top: 188px;
	    left: 366px;
	    width: 198px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(359deg) brightness(100%) contrast(80%) ;
	}
	.uveros {
	    top: 260px;
	    left: 284px;
	    width: 16px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.vijagual {
	    top: 345px;
	    left: 168px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.villa-fatima {
	    top: 435px;
	    left: 705px;
	    width: 55px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 182px;
	    left: 467px;
	    width: 41px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
