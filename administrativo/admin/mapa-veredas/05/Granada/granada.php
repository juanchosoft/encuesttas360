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
	.bella-maria{
	    top: 475px;
	    left: 609px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.buena-vista {
	    top: 501px;
	    left: 584px;
	    width: 109px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.calderas {
	    top: 5px;
	    left: 518px;
	    width: 216px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cristalina-cruces {
	    top: 394px;
	    left: 596px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-chuscal{
	    top: 97px;
	    left: 503px;
	    width: 100px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.campo-alegre {
	    top: 443px;
	    left: 559px;
	    width: 60px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.cristalina-cebadero {
	    top: 311px;
	    left: 375px;
	    width: 42px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-concilio {
	    top: 178px;
	    left: 397px;
	    width: 83px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-eden {
	    top: 412px;
	    left: 428px;
	    width: 98px;
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
	.el-jardin {
	    top: 330px;
	    left: 499px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-libertador {
	    top: 524px;
	    left: 573px;
	    width: 78px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-morro {
	    top: 346px;
	    left: 745px;
	    width: 49px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}	
	.cachumbal {
	    top: 244px;
	    left: 289px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-oso {
	    top: 400px;
	    left: 609px;
	    width: 115px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(2171deg) brightness(100%) contrast(80%);
	}
	.el-roblal{
	    top: 390px;
	    left: 677px;
	    width: 69px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-roble {
	    top: 371px;
	    left: 384px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-tablazo {
	    top: 568px;
	    left: 605px;
	    width: 157px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-tabor {
	    top: 402px;
	    left: 484px;
	    width: 75px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-vergel {
	    top: 296px;
	    left: 287px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(476%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.galilea {
	    top: 517px;
	    left: 506px;
	    width: 88px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-aguada {
	    top: 203px;
	    left: 707px;
	    width: 80px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-arenosa {
	    top: 612px;
	    left: 552px;
	    width: 84px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.las-palmas{
	    top: 494px;
	    left: 732px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-cascada {
	    top: 372px;
	    left: 454px;
	    width: 49px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-estrella {
	    top: 477px;
	    left: 658px;
	    width: 63px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(1379deg) brightness(118%) contrast(200%);
	}
	.la-gaviota {
		top: 370px;
	    left: 743px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.la-florida {
		top: 463px;
	    left: 704px;
	    width: 52px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(24deg) brightness(118%) contrast(119%);
	}
	.la-aurora {
	    top: 259px;
	    left: 445px;
	    width: 84px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.la-honda{
	    top: 159px;
	    left: 454px;
    	width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-linda {
	    top: 364px;
	    left: 681px;
	    width: 80px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(46deg) brightness(88%) contrast(119%);
	}
	.la-maria {
	    top: 538px;
	    left: 688px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-maria-el-progreso {
	    top: 257px;
	    left: 308px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.la-merced{
	    top: 304px;
	    left: 788px;
	    width: 64px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-milagrosa {
	    top: 213px;
	    left: 149px;
	    width: 140px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.la-primavera {
	    top: 251px;
	    left: 525px;
	    width: 100px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.retiro {
	    top: 76px;
	    left: 779px;
	    width: 219px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-quiebra {
	    top: 249px;
	    left: 612px;
	    width: 104px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-selva {
	    top: 586px;
	    left: 559px;
	    width: 54px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.las-faldas {
	    top: 567px;
	    left: 499px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.las-vegas{
		top: 265px;
	    left: 372px;
	    width: 111px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.los-medios {
	    top: 277px;
	    left: 700px;
	    width: 116px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.los-planes {
	    top: 475px;
	    left: 436px;
	    width: 72px;
	    filter: invert(8%) sepia(99%) saturate(876%) hue-rotate(316deg) brightness(100%) contrast(80%);
	}
	.malpaso{
	    top: 468px;
	    left: 506px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(176%) hue-rotate(772deg) brightness(100%) contrast(80%);
	}
	.minitas {
	    top: 187px;
	    left: 253px;
	    width: 60px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(46deg) brightness(88%) contrast(119%);
	}
	.quebradona-abajo {
	    top: 530px;
	    left: 461px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(38deg) brightness(100%) contrast(80%);
	}
	.quebradona-arriba {
	    top: 527px;
	    left: 497px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.reyes{
		top: 309px;
	    left: 472px;
    	width: 40px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-esteban {
	    top: 335px;
	    left: 340px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.san-francisco {
	    top: 423px;
	    left: 719px;
	    width: 98px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.san-matias {
	    top: 260px;
	    left: 228px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-miguel {
	    top: 148px;
	    left: 539px;
	    width: 214px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(38deg) brightness(100%) contrast(80%);
	}
	.santa-ana {
	    top: 572px;
	    left: 685px;
	    width: 11px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.tafetanes {
		top: 302px;
	    left: 525px;
	    width: 140px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}
	.vahitos {
		top: 172px;
	    left: 287px;
	    width: 114px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}		
	.zona-urbana {
	    top: 286px;
	    left: 326px;
	    width: 70px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
