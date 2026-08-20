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
	.abejas {
	    top: 239px;
	    left: 543px;
	    width: 292px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.cabecera-municipal {
	    top: 572px;
	    left: 333px;
	    width: 59px;
	    z-index: 999;
	    filter: invert(48%) sepia(59%) saturate(876%) hue-rotate(104deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.chachafruto {
	    top: 435px;
	    left: 300px;
	    width: 143px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.carmen-de-la-venta {
	    top: 591px;
	    left: 638px;
	    width: 18px;
	    z-index: 999;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.cristobal {
	    top: 310px;
	    left: 301px;
	    width: 90px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(324deg) brightness(100%) contrast(119%);
	}
	.curiti {
	    top: 609px;
	    left: 364px;
	    width: 103px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.danzante{
	    top: 514px;
	    left: 369px;
	    width: 142px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(872deg) brightness(100%) contrast(80%);
	}
	.el-guamal{
	    top: 558px;
	    left: 443px;
	    width: 95px;
	    z-index: 9999;
	    filter: invert(88%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-morro {
	    top: 616px;
	    left: 451px;
	    width: 80px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.el-porvenir {
	    top: 217px;
	    left: 257px;
	    width: 169px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-socorro {
	    top: 459px;
	    left: 589px;
	    width: 228px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.encenillos {
	    top: 5px;
	    left: 578px;
	    width: 207px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(265deg) brightness(100%) contrast(80%);
	}
	.estancias{
	    top: 514px;
	    left: 478px;
	    width: 149px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.granadillos{
	    top: 274px;
	    left: 572px;
	    width: 66px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-aldea{
	    top: 173px;
	    left: 611px;
	    width: 203px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.labraderos {
	    top: 86px;
	    left: 356px;
	    width: 195px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-ceja {
	    top: 463px;
	    left: 536px;
	    width: 84px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-florida{
	    top: 575px;
	    left: 470px;
	    width: 195px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}
	.la-hacienda{
		top: 360px;
	    left: 356px;
	    width: 111px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}	
	.la-honda {
	    top: 96px;
	    left: 200px;
	    width: 125px;
	    z-index: 999;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.la-montanita{
		top: 640px;
	    left: 505px;
	    width: 42px;
	    filter: invert(98%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-palma {
	    top: 415px;
	    left: 535px;
	    width: 94px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-pedrona {
	    top: 67px;
	    left: 322px;
	    width: 177px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-penola {
		top: 364px;
	    left: 602px;
	    width: 120px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-sucia{
		top: 84px;
	    left: 65px;
	    width: 273px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(1276deg) brightness(118%) contrast(119%);		
	}
	.llano-grande {
	    top: 551px;
	    left: 233px;
	    width: 156px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.malvaza {
	    top: 601px;
	    left: 621px;
	    width: 193px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.montenegro {
	    top: 302px;
	    left: 375px;
	    width: 110px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.naranjal {
	    top: 128px;
	    left: 235px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.pamplona {
	    top: 586px;
	    left: 596px;
	    width: 49px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(158deg) brightness(119%) contrast(119%);
	}
	.peregrino {
	    top: 538px;
	    left: 638px;
	    width: 190px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(158deg) brightness(119%) contrast(119%);
	}	
	.penoles{
		top: 363px;
	    left: 550px;
	    width: 273px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(609deg) brightness(100%) contrast(80%);
	}
	.rodas{
		top: 161px;
	    left: 277px;
	    width: 146px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.provincial{
	    top: 372px;
	    left: 542px;
	    width: 72px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}		
	.san-dieguito {
	    top: 398px;
	    left: 531px;
	    width: 31px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.san-miguel {
	    top: 112px;
	    left: 309px;
	    width: 140px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.san-pablo {
	    top: 303px;
	    left: 432px;
	    width: 147px;
	    filter: invert(48%) sepia(77%) saturate(476%) hue-rotate(1358deg) brightness(118%) contrast(119%);
	}
	.sobresabanas {
	    top: 398px;
	    left: 302px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(265deg) brightness(100%) contrast(80%);
	}
	.volador{
	    top: 41px;
	    left: 492px;
	    width: 144px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.zona-urbana {
	    top: 597px;
	    left: 333px;
	    width: 40px;
	    z-index: 999;
        filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>