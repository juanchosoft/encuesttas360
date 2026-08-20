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
	.alvarado{
		top: 337px;
	    left: 466px;
	    width: 52px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.apeon {
	    top: 287px;
	    left: 459px;
	    width: 49px;
	    filter: invert(48%) sepia(19%) saturate(176%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.cabuyal {
	    top: 415px;
	    left: 371px;
	    width: 128px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.zarzal-curazao {
	    top: 181px;
	    left: 450px;
	    width: 98px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-noral {
	    top: 230px;
	    left: 379px;
	    width: 97px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.el-convento {
	    top: 446px;
	    left: 288px;
	    width: 118px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}	
	.el-salado {
	    top: 367px;
	    left: 464px;
    	width: 66px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.fontidueno {
	    top: 476px;
	    left: 245px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.granizal {
	    top: 514px;
	    left: 266px;
	    width: 137px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.la-veta {
	    top: 205px;
	    left: 255px;
	    width: 158px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.montanita {
	    top: 382px;
	    left: 403PX;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.penolcito {
	    top: 423px;
	    left: 456px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.quebrada-arriba {
	    top: 437px;
	    left: 516px;
	    width: 143px;
	    filter: invert(88%) sepia(49%) saturate(276%) hue-rotate(920deg) brightness(100%) contrast(80%);
	}
	.sabaneta {
	    top: 378px;
	    left: 498px;
	    width: 98px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.zarzal-la-luz{
	    top: 4px;
	    left: 262px;
	    width: 214px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.tierradentro{
		top: 229px;
	    left: 392px;
	    width: 174px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.zona-urbana{
	    top: 321px;
	    left: 243px;
	    width: 239px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
</style>
