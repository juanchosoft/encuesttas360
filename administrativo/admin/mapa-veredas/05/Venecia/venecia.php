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
	}
	.bolombolo {
	    top: 72px;
	    left: 8px;
	    width: 455px;
	}
	.cerro-tusa {
		top: 111px;
	    left: 177px;
	    width: 417px;
	    filter: invert(48%) sepia(78%) saturate(876%) hue-rotate(713deg) brightness(88%) contrast(109%);
	}
	.el-cerro {
	    top: 318px;
	    left: 731px;
	    width: 128px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(510deg) brightness(98%) contrast(140%);
	}
	.el-limon {
	    top: 434px;
	    left: 571px;
	    width: 61px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-recreo {
	    top: 97px;
	    left: 569px;
	    width: 136px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.el-rincon {
		top: 365px;
	    left: 687px;
	    width: 94px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-ventiadero{
	    top: 287px;
	    left: 546px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-vergel{
	    top: 390px;
	    left: 601px;
	    width: 45px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.la-amalia {
		top: 318px;
	    left: 584px;
	    width: 128px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.la-arabia {
	    top: 369px;
	    left: 325px;
	    width: 241px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-mina {
	    top: 480px;
	    left: 573px;
	    width: 54px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.melindres {
	    top: 475px;
	    left: 542px;
	    z-index: 999;
	    width: 35px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(185deg) brightness(100%) contrast(80%);
	}
	.miraflores{
	    top: 395px;
	    left: 615px;
	    width: 152px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.palenque {
		top: 214px;
	    left: 746px;
	    width: 149px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);

	}
	.palmichal {
	    top: 144px;
	    left: 634px;
	    width: 130px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(13deg) brightness(118%) contrast(119%);
	}
	.rita-penas-azules {
	    top: 280px;
	    left: 692px;
	    width: 79px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.villa-silvia {
	    top: 336px;
	    left: 524px;
	    width: 104px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(848deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
		top: 261px;
	    left: 597px;
	    width: 56px;
	    z-index: 99999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
