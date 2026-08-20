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
	.campo-alegre {
		top: 68px;
	    left: 473px;
	    width: 421px;
	}
	.campo-alegre {
	    top: 558px;
	    left: 634px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.cortado {
	    top: 339px;
	    left: 510px;
	    width: 95px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.el-aguacate {
	    top: 598px;
	    left: 574px;
	    width: 94px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-carmelo {
	    top: 435px;
	    left: 591px;
	    width: 113px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.el-caunzal {
	    top: 240px;
	    left: 468px;
	    width: 156px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-churimo{
	    top: 482px;
	    left: 646px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-encenillo{
	    top: 381px;
	    left: 400px;
	    width: 83px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-gavilan {
	    top: 360px;
	    left: 639px;
	    width: 161px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.el-obispo {
	    top: 324px;
	    left: 462px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-olival {
	    top: 416px;
	    left: 484px;
	    width: 89px;
	    filter: invert(48%) sepia(137%) saturate(176%) hue-rotate(73deg) brightness(118%) contrast(119%);
	}
	.el-socorro {
	    top: 380px;
	    left: 573px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-tablazo{
		top: 345px;
	    left: 360px;
	    width: 76px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.getsemani{
	    top: 286px;
	    left: 536px;
	    width: 132px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-camelia{
	    top: 383px;
	    left: 463px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-granja {
	    top: 193px;
	    left: 387px;
	    width: 118px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(350deg) brightness(100%) contrast(150%);
	}
	.la-honda {
		top: 5px;
	    left: 101px;
	    width: 220px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-inmaculada{
	    top: 463px;
	    left: 504px;
	    width: 84px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.la-merced {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-pena {
		top: 332PX;
	    left: 417px;
	    width: 60px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.la-quiebra {
	    top: 473px;
	    left: 532px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.la-trinidad {
		top: 241px;
	    left: 307px;
	    width: 108px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.palmitas {
	    top: 586px;
	    left: 394px;
	    width: 134px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-merced{
		top: 639px;
	    left: 575px;
	    width: 186px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.piedra-galana {
		top: 558px;
	    left: 425px;
	    width: 175px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.portugal {
	    top: 519px;
	    left: 609px;
	    width: 54px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.sabaletas {
	    top: 448px;
	    left: 379px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.sabanitas {
	    top: 545px;
	    left: 584px;
	    width: 82px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.san-antonio {
	    top: 507px;
	    left: 392px;
	    width: 127px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.zarcitos{
	    top: 119px;
	    left: 148PX;
	    width: 290px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
		top: 313px;
	    left: 517px;
	    width: 25px;
	    z-index: 9999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
