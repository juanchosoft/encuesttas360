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
	.alto-de-medina {
	    top: 519px;
	    left: 397px;
	    width: 170px;
	}
	.cerezales {
	    top: 358px;
	    left: 316px;
	    width: 261px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.el-espinal {
	    top: 429px;
	    left: 461px;
	    width: 265px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.el-rano {
	    top: 294px;
	    left: 537px;
	    width: 140px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-tambo {
	    top: 349px;
	    left: 173px;
	    width: 173px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.embalse-riogrande {
	    top: 152px;
	    left: 500px;
	    width: 361px;
	    filter: invert(95%) sepia(109%) saturate(576%) hue-rotate(185deg) brightness(100%) contrast(119%);
	}
	.espiritu-santo{
		top: 312px;
	    left: 27px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-apretel {
	    top: 266px;
	    left: 561px;
	    width: 334px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(330deg) brightness(100%) contrast(80%);
	}
	.la-clarita {
	    top: 472px;
	    left: 15px;
	    width: 83px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.la-cuchilla {
		top: 485px;
	    left: 140px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-empalizada {
	    top: 494px;
	    left: 70px;
	    width: 110px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-lana {
	    top: 229px;
	    left: 82px;
	    width: 167px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-palma{
	    top: 178px;
	    left: 432px;
	    width: 297px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-pulgarina{
	    top: 332px;
	    left: 293px;
	    width: 148px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.llano-de-ovejas{
	    top: 480px;
	    left: 5px;
	    width: 302px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.pantanillo {
	    top: 406px;
	    left: 18px;
	    width: 275px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.rio-chico {
	    top: 170px;
	    left: 695px;
	    width: 200px;
	    filter: invert(8%) sepia(109%) saturate(176%) hue-rotate(88deg) brightness(118%) contrast(119%);
	}
	.san-francisco{
	    top: 211px;
	    left: 230px;
	    width: 228px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.santa-barbara {
	    top: 207px;
	    left: 407px;
	    width: 153px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.zafra {
	    top: 83px;
	    left: 351px;
	    width: 184px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.san-juan{
	    top: 157px;
	    left: 94px;
	    width: 327px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 318px;
	    left: 413px;
	    width: 61px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
