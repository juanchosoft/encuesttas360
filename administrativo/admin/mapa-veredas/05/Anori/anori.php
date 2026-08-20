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
	    width: 1000px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.bellavista{
	    top: 568px;
	    left: 360px;
	    width: 23px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.bolivar {
	    top: 415px;
	    left: 449px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.brisas-de-nechi {
	    top: 587px;
	    left: 290px;
	    width: 29px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.chagualo-arriba {
	    top: 526px;
	    left: 319px;
	    width: 50px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.concha-abajo{
	    top: 232px;
	    left: 453px;
	    width: 113px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.chagualito {
	    top: 510px;
	    left: 289px;
	    width: 42px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.chagualo-abajo {
	    top: 557px;
	    left: 309px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.concha-arriba {
	    top: 340px;
	    left: 441px;
	    width: 101px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.concha-media {
	    top: 307px;
	    left: 458px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.cristalina {
		top: 573px;
	    left: 297px;
	    width: 22px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.cruces{
	    top: 354px;
	    left: 536px;
	    width: 52px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-banco {
	    top: 466px;
	    left: 522px;
	    width: 38px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-carmen {
		top: 412px;
	    left: 514px;
	    width: 57px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-carmin {
	    top: 282px;
	    left: 268px;
	    width: 76px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}	
	.cachumbal {
	    top: 244px;
	    left: 289px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-limon {
	    top: 715px;
	    left: 406px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(2171deg) brightness(100%) contrast(80%);
	}
	.el-retiro{
	    top: 644px;
	    left: 457px;
	    width: 92px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-roble {
	    top: 690px;
	    left: 427px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-zafiro {
	    top: 550px;
	    left: 437px;
	    width: 134px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-casita {
	    top: 738px;
	    left: 374px;
	    width: 29px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-esperanza {
	    top: 338px;
	    left: 578px;
	    width: 82px;
	    filter: invert(48%) sepia(19%) saturate(476%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.la-guayana {
	    top: 372px;
	    left: 312px;
	    width: 55px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-meseta {
	    top: 559px;
	    left: 327px;
	    width: 44px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-plancha {
		top: 443px;
	    left: 300px;
	    width: 99px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.providencia{
	    top: 42px;
	    left: 483px;
	    width: 167px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.las-animas {
	    top: 597px;
	    left: 378px;
	    width: 76px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.las-lomitas {
	    top: 498px;
	    left: 361px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.la-soledad {
	    top: 549px;
	    left: 375px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.las-nieves {
	    top: 315px;
	    left: 189px;
	    width: 89px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(24deg) brightness(118%) contrast(119%);
	}
	.la-primavera {
	    top: 397px;
	    left: 274px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.la-teresita{
		top: 621px;
	    left: 323px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-trinidad {
	    top: 398px;
	    left: 248px;
	    width: 50px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(46deg) brightness(88%) contrast(119%);
	}
	.liberia {
		top: 5px;
	    left: 628px;
	    width: 184px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.los-trozos {
	    top: 159px;
	    left: 604px;
	    width: 169px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.madreseca{
	    top: 237px;
	    left: 546px;
	    width: 77px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.medias-faldas {
	    top: 664px;
	    left: 318px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.miraflores {
	    top: 529px;
	    left: 275px;
	    width: 37px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.retiro {
	    top: 76px;
	    left: 779px;
	    width: 219px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.montebello {
	    top: 633px;
	    left: 316px;
	    width: 79px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.montefrio {
	    top: 656px;
	    left: 389px;
 	    width: 48px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.pajonal {
		top: 753px;
	    left: 357px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.puerto-rico{
	    top: 388px;
	    left: 514px;
	    width: 152px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.roble-arriba {
	    top: 644px;
	    left: 406px;
	    width: 63px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.san-isidro {
	    top: 293px;
	    left: 226px;
	    width: 76px;
	    filter: invert(8%) sepia(99%) saturate(876%) hue-rotate(316deg) brightness(100%) contrast(80%);
	}
	.san-juan{
	    top: 543px;
	    left: 366px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-lorenzo {
	    top: 598px;
	    left: 356px;
	    width: 36px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(46deg) brightness(88%) contrast(119%);
	}
	.santa-gertrudis {
	    top: 358px;
	    left: 390px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(38deg) brightness(100%) contrast(80%);
	}
	.santa-ines {
	    top: 299px;
	    left: 313px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.santiago{
	    top: 351px;
	    left: 349px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.santo-domingo {
		top: 443px;
	    left: 271px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.solano {
	    top: 105px;
	    left: 341px;
	    width: 181px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.tabacal {
	    top: 575px;
	    left: 315px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.travesias {
		top: 602px;
	    left: 328px;
	    width: 38px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.villa-fatima {
	    top: 593px;
	    left: 426px;
	    width: 85px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.zona-urbana {
	    top: 524px;
	    left: 425px;
	    width: 42px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
