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
	.aldana {
	    top: 372px;
	    left: 98px;
	    width: 130px;
	}
	.alto-del-palmar {
	    top: 230px;
	    left: 468px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.pantanillo {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.bodeguitas {
		top: 53px;
	    left: 262px;
	    width: 51px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.bodegas {
	    top: 57px;
	    left: 257px;
	    width: 134px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.buenavista {
	    top: 214px;
	    left: 344px;
	    width: 101px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.campo-alegre {
	    top: 422px;
	    left: 739px;
	    width: 89px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.cuchillas{
		top: 119px;
	    left: 33px;
	    width: 184px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-carmelo{
	    top: 275px;
	    left: 150px;
	    width: 139px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-morro {
	    top: 381px;
	    left: 211px;
	    width: 136px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.el-retiro {
	    top: 272px;
	    left: 266px;
	    width: 90px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-saladito {
	    top: 240px;
	    left: 143px;
	    width: 119px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-salto {
	    top: 114px;
	    left: 338px;
	    width: 133px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-senor-caido{
	    top: 213px;
	    left: 192px;
	    width: 81px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-socorro{
	    top: 309px;
	    left: 544px;
	    width: 97px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.guadualito{
	    top: 407px;
	    left: 809px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-aurora {
	    top: 121px;
	    left: 314px;
	    width: 90px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-chapa {
	    top: 232px;
	    left: 337px;
	    width: 55px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-floresta{
	    top: 287px;
	    left: 339px;
	    width: 62px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}

	.la-serrania {
		top: 375px;
	    left: 414px;
	    width: 90px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.las-lajas {
		top: 387px;
	    left: 200px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.las-palmas {
	    top: 303px;
	    left: 617px;
	    width: 162px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-teneria {
		top: 162px;
	    left: 282px;
    	width: 70px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-paz{
	    top: 243px;
	    left: 545px;
	    width: 159px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.lourdes{
	    top: 265px;
	    left: 109px;
	    width: 86px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.morritos{
	    top: 343px;
	    left: 315px;
	    width: 117px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.palmarcito{
		top: 90px;
	    left: 438px;
	    width: 177px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.pantanillo {
		top: 146px;
	    left: 156px;
	    width: 115px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.pavas {
	    top: 79px;
	    left: 152px;
	    width: 119px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.portachuelo{
		top: 236px;
	    left: 349px;
	    width: 122px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.potreritos {
		top: 211px;
	    left: 59px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.san-eusebio {
	    top: 555px;
	    left: 103px;
	    width: 199px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(1894deg) brightness(118%) contrast(119%);
	}
	.san-matias {
	    top: 209px;
	    left: 604px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.san-matias-la-trinidad {
	    top: 394px;
	    left: 594px;
	    width: 50px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.valle-de-maria{
	    top: 276px;
	    left: 379px;
	    width: 146px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(22deg) brightness(118%) contrast(119%);
	}
	.valle-luna{
	    top: 347px;
	    left: 761px;
	    width: 90px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.vargas{
		top: 144px;
	    left: 5px;
	    width: 177px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.zona-urbana {
	    top: 173px;
	    left: 252px;
	    width: 147px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>