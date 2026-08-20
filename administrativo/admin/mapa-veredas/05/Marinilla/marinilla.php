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
	.alto-del-mercado{
	    top: 376px;
	    left: 319px;
	    width: 145px;
	}
	.belen {
	    top: 381px;
	    left: 109px;
	    width: 104px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.campo-alegre {
	    top: 494px;
	    left: 205px;
	    width: 90px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.chagualo {
	    top: 495px;
	    left: 315px;
	    width: 76px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.cascajo-abajo {
	    top: 593px;
	    left: 187px;
	    width: 115px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.cascajo-arriba {
		top: 701px;
	    left: 197px;
	    width: 226px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.chocho-mayo {
	    top: 257px;
	    left: 365px;
    	width: 136px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.cimarronas {
		top: 537px;
	    left: 167px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-porvenir {
		top: 100px;
	    left: 526px;
	    width: 108px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-rosario{
	    top: 134px;
	    left: 474px;
	    width: 134px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-socorro {
	    top: 337px;
	    left: 280px;
	    width: 114px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.gaviria {
	    top: 469px;
	    left: 320px;
	    width: 269px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-retiro {
		top: 519px;
	    left: 391px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-esmeralda {
		top: 645px;
	    left: 280px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-esperanza{
		top: 496px;
	    left: 259px;
	    width: 93px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-inmaculada {
	    top: 239px;
	    left: 585px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-milagrosa {
	    top: 260px;
	    left: 479px;
	    width: 116px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-montanita {
	    top: 366px;
	    left: 563px;
	    width: 135px;
	}
	.la-pena {
	    top: 151px;
	    left: 396px;
	    width: 106px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-primavera {
	    top: 328px;
	    left: 180px;
	    width: 121px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.las-mercedes {
	    top: 593px;
	    left: 328px;
	    width: 76px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(82deg) brightness(88%) contrast(119%) ;
	}
	.llanadas {
	    top: 130px;
	    left: 284px;
	    width: 178px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.los-alpes{
	    top: 430px;
	    left: 672px;
	    width: 121px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.pozo {
		top: 184px;
	    left: 544px;
	    width: 123px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.salto-abajo {
		top: 3px;
	    left: 437px;
	    width: 134px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.salto-arriba {
		top: 49px;
	    left: 345px;
	    width: 148px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.san-jose {
	    top: 330px;
	    left: 425px;
	    width: 149px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.san-juan-bosco {
	    top: 487px;
	    left: 320px;
	    width: 171px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.santa-cruz {
		top: 406px;
	    left: 446px;
	    width: 188px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.yarumos {
		top: 308px;
	    left: 588px;
	    width: 140px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.zona-expansion-urbana{
	    top: 412px;
	    left: 154px;
	    width: 173px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-asuncion {
		top: 242px;
	    left: 292px;
	    width: 122px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1834deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 434px;
	    left: 184px;
	    width: 126px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
