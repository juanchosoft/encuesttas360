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
	.bonilla {
	    top: 591px;
	    left: 538px;
	    width: 162px;
	}
	.chiquinquira {
	    top: 616px;
	    left: 450px;
	    width: 87px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(13deg) brightness(118%) contrast(119%);
	}
	.concordia {
	    top: 694px;
	    left: 363px;
	    width: 138px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.despensas {
	    top: 4px;
	    left: 345px;
	    width: 202px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-carmelo {
		top: 503px;
	    left: 293px;
	    width: 92px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.el-chilco {
		top: 642px;
	    left: 518px;
	    width: 93px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-marial{
	    top: 268px;
	    left: 394px;
	    width: 188px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-morro{
	    top: 494px;
	    left: 434px;
	    width: 127px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-salto {
	    top: 409px;
	    left: 249px;
	    width: 214px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.el-uvital {
		top: 447px;
	    left: 463px;
	    width: 94px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.embalse {
	    top: 524px;
	    left: 574px;
	    width: 78px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.embalse-penol-guatape {
	    top: 95px;
	    left: 287px;
	    width: 391px;
	    filter: invert(148%) sepia(89%) saturate(276%) hue-rotate(180deg) brightness(100%) contrast(80%);
	}
	.guamito{
	    top: 523px;
	    left: 356px;
	    width: 118px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.horizontes{
	    top: 496px;
	    left: 321px;
	    width: 123px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-chapa{
	    top: 385px;
	    left: 332px;
	    width: 97px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(876%) hue-rotate(2834deg) brightness(100%) contrast(80%);
	}
	.la-cristalina {
	    top: 453px;
	    left: 378px;
	    width: 41px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-culebra {
	    top: 375px;
	    left: 271px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-helida{
	    top: 621px;
	    left: 336px;
	    width: 108px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.la-meseta {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.magdalena {
	    top: 120px;
	    left: 519px;
	    width: 135px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.palestina {
	    top: 516px;
	    left: 548px;
	    width: 58px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.palmira {
	    top: 265px;
	    left: 331px;
	    width: 109px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.primavera {
	    top: 298px;
	    left: 267px;
	    width: 93px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(17deg) brightness(118%) contrast(119%);
	}
	.la-meseta{
	    top: 628px;
	    left: 416px;
	    width: 105px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.santa-ana {
		top: 374px;
	    left: 409px;
	    width: 74px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.santa-ines {
		top: 333px;
	    left: 202px;
	    width: 87px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
	    top: 514px;
	    left: 341px;
	    width: 62px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
