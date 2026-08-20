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
	.alto-del-chiri {
	    top: 471px;
	    left: 64px;
	    width: 140px;
	}
	.berlin {
		top: 439px;
	    left: 230px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.buena-vista {
		top: 493px;
	    left: 113px;
	    width: 222px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.campo-alegre {
		top: 225px;
    	left: 564px;
    	width: 153px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.chorrillos {
	    top: 120px;
	    left: 676px;
	    width: 77px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.cucurucho {
	    top: 363px;
	    left: 294px;
	    width: 118px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-anime {
		top: 595px;
	    left: 468px;
	    width: 188px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-cedral {
	    top: 521px;
	    left: 324px;
	    width: 179px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%);
	}
	.el-guaico {
	    top: 268px;
	    left: 389px;
	    width: 70px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-gurri {
		top: 128px;
	    left: 522px;
	    width: 96px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}	
	.el-pescado {
	    top: 427px;
	    left: 320px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-polvillo {
		top: 228px;
	    left: 389px;
	    width: 111px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-resplandor {
	    top: 336px;
	    left: 413px;
	    width: 108px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-roblal {
		top: 283px;
	    left: 341px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.guriman {
	    top: 197px;
	    left: 297px;
	    width: 106px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%);
	}
	.la-america {
		top: 413px;
	    left: 303px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-calera {
	    top: 333px;
	    left: 124px;
	    width: 189px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-correa {
		top: 488px;
	    left: 378px;
	    width: 104px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-cristalina {
	    top: 6px;
	    left: 631px;
	    width: 206px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.laderas {
	    top: 156px;
	    left: 603px;
	    width: 75px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-meseta {
		top: 434px;
	    left: 400px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}	
	.la-mina {
	    top: 437px;
    	left: 268px;
    	width: 65px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-rodriguez {
		top: 152px;
	    left: 483px;
	    width: 57px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.las-auras {
		top: 176px;
	    left: 494px;
	    width: 150px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-velez {
		top: 260px;
	    left: 465px;
	    width: 160px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.los-naranjos {
	    top: 382px;
	    left: 367px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.moravia {
		top: 438px;
	    left: 455px;
	    width: 130px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.morron {
		top: 344px;
	    left: 491px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.orejon {
		top: 434px;
	    left: 110px;
	    width: 126px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.palestina {
		top: 153px;
	    left: 381px;
	    width: 139px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%);
	}
	.palmichal {
	    top: 268px;
	    left: 232px;
	    width: 123px;
	}
	.quebraditas {
		top: 334px;
	    left: 471px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-epifanio {
		top: 535px;
	    left: 566px;
	    width: 126px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-francisco {
		top: 373px;
	    left: 536px;
	    width: 111px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.san-pedro {
	    top: 473px;
	    left: 237px;
	    width: 46px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.santa-ana {
	    top: 452px;
	    left: 569px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.travesias {
	    top: 298px;
	    left: 386px;
	    width: 57px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.turco {
	    top: 69px;
	    left: 558px;
	    width: 139px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%);
	}
	.zona-urbana {
		top: 414px;
	    left: 466px;
	    width: 39px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
