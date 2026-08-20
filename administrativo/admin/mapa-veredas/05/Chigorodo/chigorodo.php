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
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.barranquillita {
	    top: 441px;
	    left: 292px;
	    width: 126px;
	}
	.bocas-de-guapa {
	    top: 588px;
	    left: 406px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.bocas-de-chigorodo {
	    top: 405px;
	    left: 18px;
	    width: 154px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.champitas {
	    top: 288px;
	    left: 150px;
	    width: 272px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chigorodocito {
		top: 267px;
	    left: 483px;
	    width: 114px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.chirido{
	    top: 74px;
	    left: 159px;
	    width: 249px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-coco {
	    top: 118px;
	    left: 481px;
	    width: 188px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.el-congo {
		top: 211px;
	    left: 523px;
	    width: 166px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-dos {
	    top: 472px;
	    left: 375px;
	    width: 141px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-guineo {
		top: 376px;
	    left: 371px;
	    width: 205px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-platano {
		top: 284px;
	    left: 562px;
	    width: 127px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-tigre{
		top: 429px;
	    left: 410px;
	    width: 198px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-venado {
		top: 321px;
	    left: 258px;
	    width: 275px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-vijao {
		top: 122px;
	    left: 339px;
	    width: 118px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.guaguas{
		top: 120px;
	    left: 157px;
	    width: 143px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.guapa {
	    top: 482px;
	    left: 382px;
	    width: 208px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.guapa-arriba {
	    top: 441px;
	    left: 559px;
	    width: 187px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.jurado {
	    top: 618px;
	    left: 470px;
	    width: 204px;
		filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.jurado-arriba {
	    top: 547px;
	    left: 623px;
	    width: 98px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-candelaria {
		top: 7px;
	    left: 24px;
	    width: 140px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-fe {
	    top: 209px;
	    left: 253px;
	    width: 127px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(110deg) brightness(88%) contrast(119%) ;
	}
	.la-india {
		top: 542px;
	    left: 339px;
	    width: 86px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-lucita{
		top: 119px;
	    left: 13px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-maporita {
	top: 291px;
    left: 421px;
    width: 90px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-rivera {
	    top: 264px;
	    left: 408px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.las-mercedes {
		top: 577px;
	    left: 451px;
	    width: 148px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.malagon {
		top: 185px;
	    left: 44px;
	    width: 250px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.polines {
	    top: 102px;
	    left: 644px;
	    width: 197px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(100%) contrast(89%) ;
	}
	.quebrada-honda{
	    top: 266px;
	    left: 80px;
	    width: 199px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.remigio{
		top: 341px;
	    left: 524px;
	    width: 170px;
	}
	.ripea{
	    top: 143px;
	    left: 386px;
	    width: 142px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.sadem {
		top: 92px;
	    left: 61px;
	    width: 135px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.serrania-abibe {
		top: 140px;
	    left: 669px;
	    width: 219px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.zona-urbana {
		top: 260px;
	    left: 361px;
	    width: 55px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
