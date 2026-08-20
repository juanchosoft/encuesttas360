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
	.alto-caceri {
	    top: 432px;
	    left: 530px;
	    width: 64px;
	}
	.alto-tamana {
	    top: 414px;
	    left: 451px;
	    width: 105px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.anara {
	    top: 475px;
	    left: 298px;
	    width: 118px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.asturias {
	    top: 437px;
	    left: 285px;
	    width: 62px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.bejuquillo {
	    top: 491px;
	    left: 397px;
	    width: 363px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.campamento {
	    top: 386px;
	    left: 627px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.campanario {
	    top: 441px;
	    left: 376px;
	    width: 108px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.candilejas {
	    top: 464px;
	    left: 291px;
	    width: 65px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.cano-prieto {
		top: 475px;
	    left: 638px;
	    width: 89px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.corrales {
	    top: 437px;
	    left: 337px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-cacucho {
	    top: 412px;
	    left: 571px;
	    width: 70px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-calvario {
	    top: 513px;
	    left: 419px;
	    width: 69px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-deseo {
	    top: 270px;
	    left: 401px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-jardin {
	    top: 175px;
	    left: 296px;
	    width: 190px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.el-tigre {
	    top: 454px;
	    left: 464px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-toro {
	    top: 99px;
	    left: 491px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.guarumo{
	    top: 88px;
	    left: 333px;
	    width: 167px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.isla-la-amargura {
		top: 94px;
	    left: 486px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.juan-martin {
		top: 288px;
	    left: 575px;
	    width: 86px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-porcelana {
		top: 497px;
	    left: 390px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}	
	.la-reversa {
		top: 204px;
	    left: 555px;
	    width: 129px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.las-mojosas {
	    top: 278px;
	    left: 516px;
	    width: 55px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.las-pampas {
		top: 288px;
	    left: 306px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.los-comuneros {
	    top: 312px;
	    left: 304px;
	    width: 102px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.los-conchos {
	    top: 322px;
	    left: 482px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.los-delirios {
		top: 249px;
	    left: 664px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.los-lloros {
		top: 340px;
	    left: 387px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.manizales {
		top: 132px;
	    left: 141px;
	    width: 159px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.naranjal {
		top: 439px;
	    left: 635px;
	    width: 82px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.nicaragua {
	    top: 224px;
	    left: 173px;
	    width: 143px;
	}
	.piamonte {
		top: 190px;
	    left: 449px;
	    width: 146px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.ponciano-abajo {
		top: 350px;
    	left: 425px;
    	width: 106px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.pueblo-plano {
	    top: 297px;
	    left: 646px;
	    width: 47px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.puerto-belgica {
	    top: 295px;
	    left: 171px;
	    width: 226px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.puerto-lindo {
	    top: 343px;
	    left: 592px;
	    width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.puerto-santo {
		top: 165px;
	    left: 425px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}	
	.rio-man {
	    top: 8px;
	    left: 421px;
	    width: 115px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.san-jose-del-man {
	    top: 147px;
	    left: 257px;
	    width: 124px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.san-lorenzo {
	    top: 385px;
	    left: 342px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.san-marcos {
	    top: 352px;
	    left: 561px;
	    width: 77px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.santa-lucia {
		top: 53px;
	    left: 246px;
	    width: 130px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.vijagual {
		top: 465px;
	    left: 588px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    top: 494px;
	    left: 291px;
	    width: 19px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
