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
	    width: 1000px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.alto-del-pital{
		top: 523px;
	    left: 443px;
	    width: 94px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.ambalema {
		top: 409px;
	    left: 68px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.arenales {
	    top: 385px;
	    left: 326px;
	    width: 160px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.caliche {
	    top: 500px;
	    left: 38px;
	    width: 71px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.caracolal{
	    top: 225px;
	    left: 256px;
	    width: 83px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.balcon {
		top: 426px;
	    left: 502px;
	    width: 51px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.cabana {
	    top: 594px;
	    left: 411px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.caunce {
		top: 168px;
	    left: 258px;
	    width: 197px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.chachafrutal {
	    top: 165px;
	    left: 281px;
	    width: 83px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.chontadural {
	    top: 326px;
	    left: 538px;
	    width: 73px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.cienaga{
	    top: 226px;
	    left: 28px;
	    width: 103px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.cominal {
	    top: 248px;
	    left: 441px;
	    width: 172px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.corozo {
		top: 459px;
	    left: 173px;
	    width: 40px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.cumbre {
	    top: 498px;
	    left: 434px;
	    width: 102px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}	
	.cachumbal {
	    top: 244px;
	    left: 289px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-llano {
	    top: 251px;
	    left: 803px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(2171deg) brightness(100%) contrast(80%);
	}
	.el-palon{
		top: 596px;
	    left: 191px;
	    width: 62px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-pital {
	    top: 524px;
	    left: 414px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.encalichada {
	    top: 225px;
	    left: 199px;
	    width: 79px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.esperanza-botija {
	    top: 542px;
	    left: 432px;
	    width: 106px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.frontinito {
		top: 487px;
	    left: 358px;
	    width: 93px;
	    filter: invert(48%) sepia(19%) saturate(476%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.guayabal {
		top: 377px;
	    left: 440px;
	    width: 128px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.iracal {
	    top: 419px;
	    left: 127px;
	    width: 103px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-aguada {
		top: 243px;
	    left: 655px;
	    width: 132px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.toronjo{
	    top: 253px;
	    left: 762px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.limon-chupadero {
		top: 294px;
	    left: 351px;
	    width: 202px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.madero {
	    top: 583px;
	    left: 362px;
	    width: 64px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.monos {
		top: 212px;
	    left: 41px;
	    width: 144px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.mediacuesta {
		top: 277px;
	    left: 595px;
	    width: 97px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(24deg) brightness(118%) contrast(119%);
	}
	.limon-cabuyal {
		top: 436px;
	    left: 217px;
	    width: 122px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.meseta {
	    top: 480px;
	    left: 200px;
	    width: 57px;
	    filter: invert(8%) sepia(99%) saturate(876%) hue-rotate(316deg) brightness(100%) contrast(80%);
	}
	.murrapal{
	    top: 227px;
	    left: 128px;
	    width: 167px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.nudillales {
	    top: 227px;
	    left: 375px;
	    width: 77px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(46deg) brightness(88%) contrast(119%);
	}
	.oro-bajo {
		top: 427px;
	    left: 266px;
	    width: 166px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.oso {
	    top: 588px;
	    left: 246px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.palmas{
	    top: 577px;
	    left: 325px;
	    width: 59px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.paramillo {
	    top: 523px;
	    left: 116px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.penas-blancas {
	    top: 375px;
	    left: 67px;
	    width: 51px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.retiro {
	    top: 76px;
	    left: 779px;
	    width: 219px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.rio-verde {
	    top: 428px;
	    left: 5px;
	    width: 89px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.san-benito {
		top: 373px;
	    left: 586px;
	    width: 110px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.san-francisco {
	    top: 244px;
	    left: 69px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.travesias{
		top: 221px;
	    left: 423px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 425px;
	    left: 246px;
	    width: 52px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
