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
	.astilleros{
	    top: 360px;
	    left: 347px;
	    width: 57px;
	}
	.cachirime {
		top: 236px;
	    left: 399px;
	    width: 172px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.caracoli {
	    top: 464px;
	    left: 425px;
	    width: 14px;
	    z-index: 9999;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.colmenas {
	    top: 376px;
	    left: 486px;
	    width: 113px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chorros-blancos {
	    top: 637px;
	    left: 297px;
	    width: 148px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.clavellino {
	    top: 533px;
	    left: 423px;
	    width: 93px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.colombia {
	    top: 426px;
	    left: 467px;
	    width: 98px;
        filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-catorce {
	    top: 7px;
	    left: 657px;
	    width: 20px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-higueron {
	    top: 405px;
	    left: 334px;
	    width: 70px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-pital{
	    top: 348px;
	    left: 435px;
	    width: 70px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-quince {
	    top: 20px;
	    left: 654px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.juntas {
	    top: 103px;
	    left: 359px;
	    width: 256px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.la-alemania {
	    top: 178px;
	    left: 626px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(257deg) brightness(100%) contrast(80%) ;
	}
	.la-america {
		top: 286px;
	    left: 308px;
	    width: 157px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-coposa{
	    top: 34px;
	    left: 632px;
	    width: 99px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-habana {
	    top: 423px;
	    left: 417px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-paulina {
		top: 176px;
	    left: 532px;
	    width: 100px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.las-camelias {
	    top: 51px;
	    left: 662px;
	    width: 32px;
	}
	.la-siberia {
	    top: 191px;
	    left: 472px;
	    width: 69px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(342deg) brightness(118%) contrast(119%) ;
	}
	.las-palomas {
	    top: 84px;
	    left: 616px;
	    width: 47px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.los-pomos {
	    top: 255px;
	    left: 570px;
	    width: 122px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(158deg) brightness(88%) contrast(119%) ;
	}
	.monte-blanco {
	    top: 222px;
	    left: 473px;
	    width: 80px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.montefrio {
	    top: 266px;
	    left: 291px;
	    width: 199px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(57deg) brightness(100%) contrast(80%) ;
	}
	.morron {
	    top: 582px;
	    left: 302px;
	    width: 95px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.nevado {
	    top: 464px;
	    left: 310px;
	    width: 136px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(1229deg) brightness(118%) contrast(200%) ;
	}
	.pensilvania {
		top: 288px;
	    left: 264px;
	    width: 91px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.playa-rica {
	    top: 6px;
	    left: 607px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.puerto-raudal {
		top: 120px;
	    left: 570px;
	    width: 64px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.puerto-valdivia {
	    top: 305px;
	    left: 462px;
	    width: 28px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
	.raudal {
	    top: 182px;
	    left: 652px;
	    width: 15px;
	    z-index: 9999;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.raudal-viejo {
		top: 131px;
	    left: 603px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.san-fermin {
	    top: 649px;
	    left: 246px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.san-jose-de-genova {
	    top: 112px;
	    left: 172px;
	    width: 248px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(60deg) brightness(118%) contrast(119%) ;
	}
	.santa-ana {
	    top: 498px;
	    left: 237px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.santa-barbara {
		top: 324px;
	    left: 343px;
	    width: 134px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.santa-ines {
		top: 428px;
	    left: 371px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.vizcaya {
	    top: 470px;
	    left: 442px;
	    width: 83px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.zapatillo {
	    top: 581px;
	    left: 361px;
	    width: 127px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.zona-urbana {
		z-index: 9999;
	    top: 590px;
	    left: 364px;
	    width: 15px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
