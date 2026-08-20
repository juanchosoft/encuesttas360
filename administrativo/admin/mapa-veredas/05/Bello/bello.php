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
	    height: 700px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.buenavista{
		top: 222px;
	    left: 688px;
	    width: 120px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cerezales {
		top: 51px;
	    left: 470px;
	    width: 142px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.charco-verde {
		top: 266px;
	    left: 135px;
	    width: 329px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.la-union {
		top: 146px;
	    left: 92px;
	    width: 287px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.cuartas {
	    top: 77px;
	    left: 382px;
	    width: 133px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.croacia {
	    top: 499px;
	    left: 658px;
	    width: 91px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}	
	.el-carmelo {
	    top: 509px;
	    left: 360px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-tambo {
		top: 6px;
	    left: 405px;
	    width: 98px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.granizal {
	    top: 595px;
	    left: 699px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.hato-viejo {
		top: 420px;
	    left: 410px;
	    width: 130px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.jalisco {
	    top: 528px;
	    left: 406PX;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.la-china {
	    top: 105px;
	    left: 324px;
	    width: 103px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.la-palma {
		top: 474px;
	    left: 406px;
	    width: 80px;
	    filter: invert(88%) sepia(49%) saturate(276%) hue-rotate(920deg) brightness(100%) contrast(80%);
	}
	.la-primavera {
	    top: 349px;
	    left: 411px;
	    width: 136px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.los-espejos{
	    top: 408px;
	    left: 409px;
	    width: 108px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.tierradentro{
		top: 229px;
	    left: 392px;
	    width: 174px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.potrerito {
	    top: 469px;
	    left: 455px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.quitasol {
		top: 210px;
	    left: 521px;
	    width: 184px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.sabanalarga {
	    top: 378px;
	    left: 173px;
	    width: 256px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.zona-urbana{
	    top: 333px;
	    left: 464px;
	    width: 337px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
</style>
