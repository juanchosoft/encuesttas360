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
	.briceno{
	    top: 327px;
	    left: 775px;
	    width: 62px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(946deg) brightness(118%) contrast(119%);
	}
	.brillantina {
	    top: 293px;
	    left: 690px;
	    width: 102px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.casamora {
	    top: 382px;
	    left: 21px;
	    width: 109px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-zancudo {
		top: 318px;
	    left: 377px;
	    width: 104px;
        filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-cinismo {
		top: 263px;
	    left: 604px;
	    width: 128px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.el-jardin {
		top: 508px;
	    left: 252px;
	    width: 135px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.hatillo {
	    top: 351px;
	    left: 84px;
	    width: 92px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-alondra {
	    top: 403px;
	    left: 742px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-argentina {
	    top: 412px;
	    left: 562px;
	    width: 123px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-clarita{
		top: 377px;
	    left: 270px;
	    width: 109px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-honda {
	    top: 348px;
	    left: 799px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-mariana {
		top: 216px;
	    left: 500px;
	    width: 35px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-retiro {
		top: 519px;
	    left: 391px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-mascota {
		top: 472px;
	    left: 68px;
	    width: 212px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-playa{
	    top: 318px;
	    left: 283px;
	    width: 180px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.las-aguitas {
	    top: 367px;
	    left: 621px;
	    width: 144px;	   
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.las-dantas {
		top: 218px;
	    left: 581px;
	    width: 138px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-mascara {
		top: 270px;
	    left: 472px;
	    width: 186px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}	
	.las-margaritas {
	    top: 208px;
	    left: 718px;
	    width: 178px;
	}
	.montanita {
		top: 452px;
	    left: 153px;
	    width: 106px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.montebello {
		top: 400px;
	    left: 117px;
	    width: 136px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.puerto-estafa {
	    top: 216px;
	    left: 454px;
	    width: 142px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(82deg) brightness(88%) contrast(119%) ;
	}
	.san-jorge {
	    top: 366px;
	    left: 164px;
	    width: 138px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-mauricio{
		top: 406px;
	    left: 368px;
	    width: 230px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.san-pedrito {
	    top: 420px;
	    left: 2px;
	    width: 155px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.san-rafael {
	    top: 221px;
	    left: 370px;
	    width: 136px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(164deg) brightness(118%) contrast(119%) ;
	}
	.santa-lucia {
	    top: 304px;
	    left: 453px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.villanita {
		top: 443px;
	    left: 205px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
		top: 490px;
	    left: 251px;
	    width: 46px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
