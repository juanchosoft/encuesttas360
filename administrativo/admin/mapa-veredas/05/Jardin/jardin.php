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
	.alto-del-indio {
	    top: 329px;
	    left: 73px;
	    width: 112px;
	}
	.caramanta {
	    top: 210px;
	    left: 84px;
	    width: 160px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.cristiania {
	    top: 93px;
	    left: 74px;
	    width: 109px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%) ;
	}
	.el-tormento {
	    top: 273px;
	    z-index: 888;
	    left: 311px;
	    width: 47px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%) ;
	}
	.gibraltar {
	    top: 371px;
	    left: 181px;
	    width: 226px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.el-tapado {
	    top: 197px;
	    left: 219px;
	    width: 115px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}	
	.la-arboleda{
	    top: 292px;
	    left: 157px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-casiana {
	    top: 164px;
	    left: 156px;
	    width: 112px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.la-herrera {
	    top: 307px;
	    left: 382px;
	    width: 193px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-linda {
	    top: 3px;
	    left: 294px;
	    width: 319px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-mesenia {
	    top: 645px;
	    left: 52px;
	    width: 159px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-salada{
	    top: 135px;
	    left: 405px;
	    width: 336px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-selva{
	    top: 78px;
	    left: 348px;
	    width: 391px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.las-macanas{
	    top: 565px;
	    z-index: 9999;
	    left: 135px;
	    width: 11px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.macanas {
	    top: 573px;
	    left: 87px;
	    width: 377px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%) ;
	}
	.morro-amarillo {
	    top: 122px;
	    left: 231px;
	    width: 107px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.quebrada-bonita{
		top: 234px;
	    left: 398px;
	    width: 451px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.rio-claro {
		top: 270px;
	    left: 69px;
	    width: 113px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.san-bartolo{
	    top: 104px;
	    left: 152px;
	    width: 106px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.santa-gertrudis{
	    top: 448px;
	    left: 114px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.serranias {
	    top: 289px;
	    left: 301px;
	    width: 140px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.verdun {
	    top: 290px;
	    left: 217px;
	    width: 126px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.zona-urbana {
		top: 265px;
	    left: 352px;
	    width: 81px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
