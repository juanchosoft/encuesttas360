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
	.alto-rhin {
	    top: 330px;
	    left: 571px;
	    width: 58px;
	}
	.batea-seca {
		top: 169px;
	    left: 578px;
	    width: 59px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.canaveral-abajo {
		top: 97px;
	    left: 580px;
	    width: 105px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.canaveral-arriba {
		top: 121px;
	    left: 529px;
	    width: 65px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chocho-escuela {
		top: 303px;
	    left: 671px;
	    width: 49px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.chocho-loma {
	    top: 338px;
	    left: 688px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.chocho-rio {
		top: 310px;
	    left: 739px;
	    width: 26px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.concepcion {
	    top: 535px;
	    left: 383px;
	    width: 268px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.dolores {
		top: 355px;
	    left: 264px;
	    width: 224px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(145deg) brightness(118%) contrast(119%) ;
	}
	.el-olivo {
		top: 178px;
	    left: 512px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-socorro {
		top: 248px;
	    left: 729px;
	    width: 58px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-tambo {
	    top: 587px;
	    left: 135px;
	    width: 306px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.guajira-abajo {
		top: 138px;
	    left: 779px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.guajira-arriba {
	    top: 187px;
	    left: 776px;
	    width: 60px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.guanteros {
	    top: 80px;
	    left: 714px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.guasimo {
	    top: 322px;
	    left: 127px;
	    width: 176px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-culebra{
	    top: 387px;
	    left: 432px;
	    width: 159px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-milagrosa {
	    top: 72px;
	    left: 760px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-montana {
		top: 104px;
	    left: 819px;
	    width: 66px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-munoz {
		top: 150px;
	    left: 815px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-quiebrita {
	    top: 315px;
	    left: 618px;
	    width: 45px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.la-quinta {
	    top: 242px;
	    left: 469px;
	    width: 96px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.llanos-de-cuiva {
		top: 474px;
	    left: 8px;
	    width: 346px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.los-pantanos {
	    top: 408px;
	    left: 738px;
	    width: 33px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.los-pinos {
		top: 320px;
	    left: 521px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.maldonado {
		top: 178px;
	    left: 430px;
	    width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.manzanillo {
		top: 51px;
	    left: 746px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.matablanco {
	    top: 226px;
	    left: 540px;
	    width: 139px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.montanita {
	    top: 197px;
	    left: 518px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.morelia {
	    top: 254px;
	    left: 219px;
	    width: 288px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.oriente{
		top: 242px;
	    left: 809px;
	    width: 76px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.pacora {
	    top: 437px;
	    left: 476px;
	    width: 98px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.pajarito-abajo {
		top: 155px;
	    left: 619px;
	    width: 54px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.pajarito-arriba {
		top: 320px;
	    left: 479px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.palmas {
	    top: 232px;
	    left: 667px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.quiebra-abajo {
		top: 271px;
	    left: 618px;
	    width: 51px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(94deg) brightness(100%) contrast(80%) ;
	}
	.quiebra-arriba {
	    top: 300px;
	    left: 648px;
	    width: 45px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.rio-arriba {
	    top: 455px;
	    left: 726px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.san-alejandro {
		top: 56px;
	    left: 645px;
	    width: 112px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.san-antonio {
		top: 378px;
	    left: 674px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.santa-ana {
		top: 372px;
	    left: 569px;
	    width: 133px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.santa-rita {
		top: 480px;
	    left: 549px;
	    width: 192px;
	}
	.santa-teresa {
		top: 265px;
	    left: 753px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.tenche-algodon {
	    top: 122px;
	    left: 716px;
	    width: 80px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.tenche-viejo {
	    top: 290px;
	    left: 705px;
	    width: 53px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.trinidad {
	    top: 176px;
	    left: 659px;
	    width: 74px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
		top: 329px;
	    left: 548px;
	    width: 20px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
