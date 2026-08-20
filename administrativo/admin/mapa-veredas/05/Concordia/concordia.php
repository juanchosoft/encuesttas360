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
	.burgos {
		top: 179px;
	    left: 264px;
	    width: 103px;
	}
	.caunzal {
	    top: 288px;
	    left: 288px;
	    width: 137px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.el-cascajo{
	    top: 600px;
	    left: 437px;
	    width: 143px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.el-chocho {
	    top: 305px;
	    left: 261px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-golpe {
	    top: 515px;
	    left: 633px;
	    width: 116px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.el-higueron{
	    top: 199px;
	    left: 533px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-socorro {
	    top: 79px;
	    left: 436px;
	    width: 204px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.la-comia{
	    top: 286px;
	    left: 153px;
	    width: 131px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-costa {
	    top: 538px;
	    left: 529px;
	    width: 78px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-cristalina {
	    top: 172px;
	    left: 351px;
	    width: 215px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-fotuta {
	    top: 629px;
	    left: 451px;
	    width: 204px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-herradura{
	    top: 117px;
	    left: 607px;
	    width: 86px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.las-animas{
	    top: 511px;
	    left: 357px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-selva {
		top: 606px;
	    left: 566px;
	    width: 178px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.llanaditas {
	    top: 582px;
	    left: 340px;
	    width: 122px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.morelia{
	    top: 360px;
	    left: 186px;
	    width: 140px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.moritos {
	    top: 8px;
	    left: 483px;
	    width: 233px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(82deg) brightness(88%) contrast(119%) ;
	}
	.morron{
	    top: 249px;
	    left: 532px;
	    width: 160px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.pueblo-rico{
	    top: 433px;
	    left: 466px;
	    width: 98px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(94deg) brightness(118%) contrast(119%) ;
	}
	.rumbadero {
	    top: 354px;
	    left: 507px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.salazar {
	    top: 471px;
	    left: 469px;
	    width: 7px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.san-luis {
	    top: 427px;
	    left: 357px;
	    width: 107px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.santa-rita {
	    top: 458px;
	    left: 423px;
	    width: 101px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.ventanas {
	    top: 359px;
	    left: 398px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.yarumal {
		top: 265px;
	    left: 433px;
	    width: 132px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 433px;
	    left: 451px;
	    width: 48px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
