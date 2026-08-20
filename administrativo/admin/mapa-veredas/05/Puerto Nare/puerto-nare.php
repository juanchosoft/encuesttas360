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
	.cano-seco {
	    top: 73px;
	    left: 365px;
	    width: 219px;
	}
	.canteras {
		top: 5px;
	    left: 381px;
	    width: 104px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.cominales {
	    top: 207px;
	    left: 218px;
	    width: 146px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.el-oro {
	    top: 515px;
	    left: 268px;
	    width: 105px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-paraiso {
	    top: 497px;
	    left: 113px;
	    width: 117px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}
	.el-patino {
		top: 396px;
	    left: 291px;
	    width: 292px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-porvenir{
		top: 590px;
	    left: 207px;
	    width: 146px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.hoyo-rico{
	    top: 381px;
	    left: 191px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.la-angelita {
		top: 542px;
	    left: 614px;
	    width: 127px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.la-arabia {
	    top: 313px;
	    left: 213px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-clara {
	    top: 136px;
	    left: 326px;
	    width: 101px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-esmeralda {
	    top: 580px;
	    left: 265px;
	    width: 191px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-mina{
	    top: 48px;
	    left: 557px;
	    width: 260px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-sierra{
	    top: 159px;
	    left: 707px;
	    z-index: 999;
	    width: 45px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-union{
	    top: 232px;
	    left: 319px;
	    width: 140px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.los-delirios {
	    top: 470px;
	    left: 210px;
	    width: 122px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.los-limones {
	    top: 241px;
	    left: 147px;
	    width: 99px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.monte-cristo{
		top: 320px;
	    left: 355px;
	    width: 119px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.mulas {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.pena-flor {
	    top: 163px;
	    left: 413px;
	    width: 295px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.playas {
	    top: 295px;
	    left: 249px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.porvenir {
	    top: 528px;
	    left: 399px;
	    width: 250px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.santa-rita {
	    top: 660px;
	    left: 200px;
	    width: 162px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.serranias{
	    top: 364px;
	    left: 84px;
	    width: 162px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.tambores {
	    top: 336px;
	    left: 124px;
	    width: 104px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.mulas{
	    top: 358px;
	    left: 457px;
	    width: 203px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 269px;
	    left: 687px;
	    width: 14px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
