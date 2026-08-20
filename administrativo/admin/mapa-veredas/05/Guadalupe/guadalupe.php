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
	.alto-de-san-juan {
		top: 583px;
	    left: 137px;
	    width: 223px;
	}
	.bromadora {
		top: 126px;
	    left: 612px;
	    width: 152px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.el-mango{
	    top: 678px;
	    left: 335px;
	    width: 126px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.el-morro {
		top: 565px;
	    left: 216px;
	    width: 178px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.guadalupe {
		top: 414px;
	    left: 520px;
	    width: 193px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.guadual{
		top: 323px;
	    left: 540px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.guanteros {
		top: 467px;
	    left: 469px;
	    width: 114px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.malabrigo {
	    top: 283px;
	    left: 532px;
	    width: 99px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.montanita {
	    top: 549px;
	    left: 386px;
	    width: 100px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.morron {
		top: 77px;
	    left: 541px;
	    width: 183px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.patio-bonito {
		top: 676px;
	    left: 406px;
	    width: 98px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.san-basilio-abajo{
	    top: 264px;
	    left: 269px;
	    width: 219px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.san-basilio-arriba{
		top: 366px;
	    left: 187px;
	    width: 296px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.san-juan {
		top: 175px;
	    left: 369px;
	    width: 203px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.san-julian {
		top: 575px;
	    left: 448px;
	    width: 135px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.san-pablo-caney{
		top: 24px;
	    left: 411px;
	    width: 167px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.san-vicente-el-kiosko {
		top: 7px;
	    left: 351px;
	    width: 108px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.san-vicente-la-susana {
	    top: 153px;
	    left: 315px;
	    width: 160px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-vicente-los-sauces {
	    top: 212px;
	    left: 270px;
	    width: 98px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(94deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
		top: 648px;
	    left: 351px;
	    width: 61px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
