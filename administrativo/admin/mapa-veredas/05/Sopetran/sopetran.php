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
	.alta-miranda{
	    top: 255px;
	    left: 412px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(356deg) brightness(118%) contrast(119%);
	}
	.chachafruto {
	    top: 353px;
	    left: 566px;
	    width: 197px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-pomar {
	    top: 178px;
	    left: 483px;
	    width: 94px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-rayo {
	    top: 106px;
	    left: 354px;
	    width: 83px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(356deg) brightness(118%) contrast(119%);
	}
	.ciruelar {
	    top: 278px;
	    left: 222px;
	    width: 153px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.cordoba {
	    top: 328px;
	    left: 293px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-rodeo {
    	top: 307px;
	    left: 217px;
	    width: 197px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.filo-del-medio {
	    top: 183px;
	    left: 537px;
	    width: 138px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.filo-grande {
	    top: 153px;
	    left: 531px;
	    width: 116px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.guaimaral{
	    top: 422px;
	    left: 147px;
	    width: 131px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.juntas {
	    top: 584px;
	    left: 282px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-aguada {
	    top: 314px;
	    left: 464px;
	    width: 128px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-isleta {
		top: 7px;
	    left: 449px;
	    width: 157px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.la-miranda {
	    top: 313px;
	    left: 345px;
	    width: 100px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-puerta{
	    top: 465px;
	    left: 197px;
	    width: 164px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.llano-de-montana {
	    top: 421px;
	    left: 304px;
	    width: 149px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.loma-del-medio {
	    top: 81px;
	    left: 426px;
	    width: 49px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.los-almendros {
	    top: 325px;
	    left: 152px;
	    width: 102px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.los-aguacates{
		top: 8px;
	    left: 399px;
	    width: 144px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);	
	}
	.montegrande {
	    top: 251px;
	    left: 507px;
	    width: 193px;
	    filter: invert(80%) sepia(79%) saturate(116%) hue-rotate(95deg) brightness(88%) contrast(80%);
	}
	.morron {
	    top: 268px;
	    left: 615px;
	    width: 150px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);	
	}	
	.monteires {
	    top: 62px;
	    left: 362px;
	    width: 83px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.los-cedros {
	    top: 10px;
	    left: 480px;
	    width: 232px;
	    filter: invert(65%) sepia(89%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.palo-grande {
		top: 154px;
	    left: 357px;
	    width: 83px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1206deg) brightness(88%) contrast(119%);
	}
	.pomos {
	    top: 395px;
	    left: 530px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.potrero{
	    top: 276px;
	    left: 421px;
	    width: 138px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);		
	}
	.monte-frio {
	    top: 556px;
	    left: 555px;
	    width: 151px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.otrabanda {
	    top: 356px;
	    left: 401px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.rojas {
	    top: 369px;
	    left: 432px;
	    width: 182px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.san-nicolas {
	    top: 490px;
	    left: 136px;
	    width: 156px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.santa-barbara{
		top: 32px;
	    left: 462px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.santa-rita {
	    top: 241px;
	    left: 429px;
	    width: 75px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.tafetanes {
	    top: 440px;
	    left: 326px;
	    width: 143px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 400px;
	    left: 397px;
	    width: 60px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
