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
	.alicante {
		top: 120px;
	    left: 385px;
	    width: 173px;
	}
	.alto-de-buenos-aires {
	    top: 255px;
	    left: 328px;
	    width: 168px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.cabanas-palestina {
		top: 521px;
	    left: 252px;
	    width: 112px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%) ;
	}
	.calamar {
	    top: 432px;
	    left: 191px;
	    width: 114px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%) ;
	}
	.cristalina {
		top: 447px;
	    left: 325px;
	    width: 190px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.el-jardin {
		top: 355px;
	    left: 582px;
	    width: 126px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}	
	.el-pescado{
		top: 653px;
	    left: 322px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.grecia {
	    top: 405px;
	    left: 570px;
	    width: 110px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.la-calera {
	    top: 406px;
	    left: 440px;
	    width: 148px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-carlota {
		top: 345px;
	    left: 322px;
	    width: 159px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-culebra{
	    top: 102px;
	    left: 522px;
	    width: 87px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.las-flores{
	    top: 276px;
	    left: 442px;
	    width: 166px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-suiza{
	    top: 561px;
	    left: 394px;
	    width: 197px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(634deg) brightness(100%) contrast(80%) ;
	}
	.malena {
	    top: 430px;
	    left: 450px;
	    width: 296px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%) ;
	}
	.minas-de-vapor {
	    top: 398px;
	    left: 296px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.sabaletas{
	    top: 552px;
	    left: 302px;
	    width: 133px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.san-bartolo{
		top: 6px;
	    left: 537px;
	    width: 223px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-juan-de-bedout {
	    top: 179px;
	    left: 531px;
	    width: 194px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(482deg) brightness(88%) contrast(119%) ;
	}
	.san-julian{
		top: 432px;
	    left: 141px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.santa-cruz {
	    top: 274px;
	    left: 573px;
	    width: 124px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.virginias {
	    top: 498px;
	    left: 183px;
	    width: 128px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2129deg) brightness(118%) contrast(200%) ;
	}
	.zona-urbana {
		top: 409px;
	    left: 670px;
	    width: 30px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
