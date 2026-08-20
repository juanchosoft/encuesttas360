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
	.alto-del-obispo {
		top: 630px;
	    left: 487px;
	    width: 69px;
	}
	.bubara {
		top: 550px;
	    left: 399px;
	    width: 46px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.buena-vista {
	    top: 220px;
	    left: 485px;
	    width: 120px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.carauquia {
		top: 419px;
	    left: 474px;
	    width: 117px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chunchunco {
	    top: 445px;
	    left: 354px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.conejos {
	    top: 30px;
	    left: 404px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.costas {
		top: 429px;
	    left: 323px;
	    width: 60px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.el-leon {
	    top: 344px;
	    left: 351px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-naranjo {
	    top: 621px;
	    left: 442px;
	    width: 49px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-puerto {
		top: 414px;
	    left: 438px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-siento {
	    top: 360px;
	    left: 392px;
	    width: 52px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.guadual {
	    top: 225px;
	    left: 478px;
	    width: 44px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.guarco {
	    top: 540px;
	    left: 337px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.higabra {
	    top: 583px;
	    left: 467px;
	    width: 51px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.la-angelina {
	    top: 602px;
	    left: 511px;
	    width: 114px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-cordillera {
	    top: 316px;
	    left: 388px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-fragua{
		top: 288px;
	    left: 443px;
	    width: 161px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-palma {
		top: 533px;
	    left: 438px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.las-brisas {
	    top: 155px;
	    left: 459px;
	    width: 81px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.las-cuatro {
		top: 218px;
	    left: 431px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}	
	.la-vega {
	    top: 161px;
	    left: 316px;
	    width: 93px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.llano-chiquito {
	    top: 331px;
	    left: 319px;
	    width: 52px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.llano-grande {
		top: 497px;
	    left: 423px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.llanos-de-urarco {
	    top: 37px;
	    left: 318px;
	    width: 174px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.los-arados {
	    top: 502px;
	    left: 388px;
	    width: 148px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.los-asientos {
	    top: 617px;
	    left: 420px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.mogotes {
		top: 535px;
	    left: 498px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.pajarito {
		top: 582px;
	    left: 377px;
	    width: 45px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.palenque {
		top: 262px;
	    left: 419px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.santa-teresa {
	    top: 352px;
	    left: 279px;
	    width: 93px;
	}
	.siara {
	    top: 582px;
	    left: 417px;
	    width: 25px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.sincierco {
		top: 488px;
	    left: 315px;
	    width: 120px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.sopetransito {
		top: 219px;
	    left: 379px;
	    width: 72px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.tabacal {
	    top: 360px;
	    left: 405px;
	    width: 88px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.unti {
	    top: 481px;
	    left: 420px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 590px;
	    left: 450px;
	    width: 9px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
