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
	.arenas-altas{
	    top: 307px;
	    left: 490px;
	    width: 73px;
	}
	.arenas-bajas {
   		top: 273px;
	    left: 525px;
	    width: 115px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.bajo-del-oso {
	    top: 371px;
	    left: 206px;
	    width: 142px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.bellavista {
	    top: 479px;
	    left: 523px;
	    width: 129px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.buenos-aires {
	    top: 435px;
	    left: 546px;
	    width: 108px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.churido {
		top: 458px;
	    left: 129px;
	    width: 143px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}	
	.churido-medio {
	    top: 547px;
	    left: 279px;
	    width: 117px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.churido-puente {
		top: 529px;
	    left: 243px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.churido-sinai {
	    top: 561px;
	    left: 376px;
	    width: 107px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-cuchillo{
	    top: 442px;
	    left: 467px;
	    width: 48px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-diamante {
	    top: 395px;
	    left: 95px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-gas {
	    top: 335px;
	    left: 410px;
	    width: 76px;
    	filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-guaro {
	    top: 369px;
	    left: 88px;
	    width: 154px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-guineo {
		top: 405px;
	    left: 331px;
	    width: 153px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-osito{
	    top: 349px;
	    left: 340px;
	    width: 90px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-porvenir {
		top: 302px;
	    left: 595px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.el-salvador {
	    top: 459px;
	    left: 253px;
	    width: 111px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-salto {
	    top: 305px;
	    left: 450px;
	    width: 67px;
	}
	.el-tigre {
	    top: 442px;
	    left: 339px;
	    width: 79px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.guineo-alto {
	    top: 400px;
	    left: 464px;
	    width: 58px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-balsa {
	    top: 452px;
	    left: 397px;
	    width: 74px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-cristalina {
	    top: 538px;
	    left: 563px;
	    width: 57px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-danta{
	    top: 753px;
	    left: 493px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-esperanza {
	    top: 260px;
	    left: 650px;
	    width: 143px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-hoz {
	    top: 248px;
	    left: 757px;
	    width: 137px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-linda {
		top: 514px;
	    left: 527px;
	    width: 53px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-miranda {
		top: 618px;
	    left: 471px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-pancha {
		top: 607px;
	    left: 403px;
	    width: 77px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-pedrosa{
	    top: 688px;
	    left: 456px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-resbaloza {
	    top: 366px;
	    left: 782px;
	    width: 95px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.las-flores {
	    top: 13px;
	    left: 805px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.las-nieves {
		top: 384px;
	    left: 631px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.las-playas{
	    top: 496px;
	    left: 470px;
	    width: 130px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-union {
		top: 352px;
	    left: 500px;
	    width: 161px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-victoria {
	    top: 484px;
	    left: 435px;
	    width: 63px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.los-mandarinos {
		top: 326px;
	    left: 382px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.miramar {
	    top: 572px;
	    left: 499px;
	    width: 114px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.mulatos-cabecero {
	    top: 448px;
	    left: 634px;
	    width: 119px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.mulatos-medio {
	    top: 361px;
	    left: 721px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.playa-larga {
		top: 156px;
	    left: 593px;
	    width: 167px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.puerto-caribe{
		top: 404px;
	    left: 41px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.rodoxali {
	    top: 108px;
	    left: 739px;
	    width: 159px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.sabaleta {
	    top: 66px;
	    left: 687px;
	    width: 135px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.salsipuedes {
	    top: 488px;
	    left: 360px;
	    width: 88px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.san-martin {
	    top: 619px;
	    left: 326px;
	    width: 110px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.san-pablo {
		top: 415px;
	    left: 35px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.vijagual{
	    top: 537px;
	    left: 60px;
	    width: 271px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.zungo {
	    top: 588px;
	    left: 195px;
	    width: 108px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.zungo-abajo {
	    top: 433px;
	    left: 3px;
	    width: 129px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.zungo-arriba {
	    top: 666px;
	    left: 396px;
	    width: 109px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 461px;
	    left: 292px;
	    width: 89px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
