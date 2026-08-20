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
	.cardalito{
	    top: 495px;
	    left: 345px;
	    width: 175px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-60 {
	    top: 365px;
	    left: 475px;
	    width: 251px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-cano {
	    top: 4px;
	    left: 212px;
	    width: 162px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.salada-parte-alta {
		top: 300px;
	    left: 384px;
	    width: 199px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-raizal {
	    top: 148px;
	    left: 269px;
	    width: 112px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.el-potrerillo {
		top: 135px;
	    left: 208px;
	    width: 89px;
	    filter: invert(15%) sepia(19%) saturate(176%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}	
	.la-aguacatala {
	    top: 72px;
	    left: 345px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-chuscala {
		top: 158px;
	    left: 261px;
	    width: 153px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.la-corrala {
	    top: 20px;
	    left: 406px;
	    width: 308px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.la-corrala-2 {
		top: 205px;
	    left: 367px;
	    width: 46px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-raya {
	    top: 43px;
	    left: 339PX;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.la-valeria {
	    top: 26px;
	    left: 199px;
	    width: 177px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.mani-del-cardal {
	    top: 354px;
	    left: 202px;
	    width: 240px;
	    filter: invert(88%) sepia(49%) saturate(276%) hue-rotate(920deg) brightness(100%) contrast(80%);
	}
	.primavera {
	    top: 214px;
	    left: 398px;
	    width: 312px;	
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.salinas{
	    top: 266px;
	    left: 173px;
	    width: 195px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.tierradentro{
		top: 229px;
	    left: 392px;
	    width: 174px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.sinifana {
	    top: 480px;
	    left: 240px;
	    width: 155px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 45px;
	    left: 356px;
	    width: 132px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
</style>
