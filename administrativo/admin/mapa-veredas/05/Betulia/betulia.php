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
	.altamira {
	    top: 240px;
	    left: 415px;
	    width: 37px;
	}
	.buenavista {
		top: 619px;
	    left: 198px;
	    width: 116px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.cangrejo {
	    top: 137px;
	    left: 594px;
	    width: 253px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.claro-verde {
	    top: 123px;
	    left: 263px;
	    width: 62px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.cuchillon {
	    z-index: 998;
	    top: 8px;
	    left: 144px;
	    width: 149px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-cuchuco {
		top: 188px;
    	left: 169px;
    	width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-guadual {
		top: 176px;
	    left: 338px;
	    width: 116px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.el-indio {
	    top: 110px;
	    left: 55px;
	    width: 234px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.el-leon {
	    top: 536px;
	    left: 174px;
	    width: 155px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-retiro {
	    top: 339px;
	    left: 350px;
	    width: 55px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}	
	.el-tarqui {
		top: 417px;
		left: 485px;
		width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-tostado {
		top: 184px;
	    left: 274px;
	    width: 78px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-yerbal {
		top: 430px;
	    left: 244px;
	    width: 55px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.guamalita {
		top: 164px;
	    left: 201px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-asomadera {
	    top: 234px;
	    left: 416px;
	    width: 183px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(115deg) brightness(100%) contrast(150%) ;
	}
	.la-ceibala {
	    top: 331px;
    	left: 318px;
    	width: 57px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-cibeles {
	    top: 534px;
	    left: 380px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.las-animas {
		top: 693px;
	    left: 210px;
	    width: 115px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-corazonada {
	    top: 508px;
	    left: 334px;
	    width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-falda {
	    top: 575px;
	    left: 306px;
	    width: 44px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.santa-rita {
	    top: 330px;
	    left: 592px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}	
	.la-favorita {
	    top: 323px;
	    left: 524px;
	    width: 126px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.la-florida {
	    top: 499px;
	    left: 294px;
	    width: 89px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.la-iracala {
		top: 340px;
	    left: 239px;
	    width: 111px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-mina {
	    top: 128px;
	    left: 60px;
	    width: 124px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.la-miranda {
	    top: 343px;
	    left: 394px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-padilla {
	    top: 507px;
	    left: 515px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.la-quiebra {
	    top: 295px;
	    left: 93px;
	    width: 236px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-raya {
    	top: 721px;
	    left: 272px;
	    width: 100px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.la-sucia {
	    top: 650px;
	    left: 303px;
	    width: 74px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.la-sucre {
	    top: 459px;
	    left: 431px;
	    width: 126px;
	}
	.la-urraena {
	    top: 227px;
	    left: 98px;
	    width: 143px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-vargas {
		top: 248px;
	    left: 108px;
	    width: 170px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.los-animes {
	    z-index: 997;
	    top: 420px;
	    left: 202px;
	    width: 148px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.luciano-restrepo {
	    z-index: 998;
	    top: 135px;
	    left: 147px;
	    width: 74px;
	    filter: invert(150%) sepia(109%) saturate(211%) hue-rotate(18046deg) brightness(118%) contrast(119%) ;
	}
	.pinonal {
		top: 310px;
	    left: 185px;
	    width: 179px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.pueblo-duro {
		top: 368px;
	    left: 444px;
	    width: 113px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.purco {
		top: 207px;
	    left: 499px;
	    width: 129px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.quebrada-arriba {
	    top: 68px;
	    left: 54px;
	    width: 184px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.saladitos {
	    top: 388px;
	    left: 292px;
	    width: 187px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.san-antonio {
	    top: 448px;
	    left: 173px;
	    width: 162px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.san-mateo {
	    top: 213px;
	    left: 212px;
	    width: 94px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.santa-rita {
	    top: 557px;
	    left: 435px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
		top: 642px;
	    left: 280px;
	    width: 34px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
