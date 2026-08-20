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
	}
	.barcino{
	    top: 226px;
	    left: 333px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cabilda {
	    top: 311px;
	    left: 408px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.chorro-claro {
	    top: 259px;
	    left: 56px;
	    width: 123px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.encarnaciones {
		top: 187px;
	    left: 231px;
	    width: 37px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-brasil {
	    top: 292px;
	    left: 459px;
	    width: 81px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.efe-gomez {
		top: 217px;
	    left: 750px;
	    width: 87px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-diluvio {
	    top: 171px;
	    left: 568px;
	    width: 119px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-jardin {
	    top: 360px;
	    left: 455px;
	    width: 106px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-piramo {
	    top: 83px;
	    left: 213px;
	    width: 100px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-porvenir {
		top: 377px;
	    left: 222px;
	    width: 136px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.el-tachira {
	    top: 333px;
	    left: 142px;
	    width: 121px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-vesubio {
		top: 131px;
	    left: 223px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.frailes{
		top: 247px;
	    left: 390px;
	    width: 72px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.inmaculada{
	    top: 245px;
	    left: 482px;
	    width: 124px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(309deg) brightness(100%) contrast(80%);
	}	
	.el-diamante {
	    top: 182px;
	    left: 298px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.guacas {
	    top: 107px;
	    left: 366px;
	    width: 236px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.guacas-arriba {
	    top: 59px;
	    left: 247px;
	    width: 194px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.ite{
	    top: 538px;
	    left: 561px;
	    width: 104px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-candelaria {
	    top: 232px;
	    left: 236px;
	    width: 81px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-ceiba {
		top: 426px;
	    left: 314px;
	    width: 116px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-chinca {
	    top: 288px;
	    left: 711px;
	    width: 78px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-floresta {
	    top: 236px;
	    left: 94px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.la-florida {
	    top: 333px;
	    left: 244px;
	    width: 141px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-guzmana {
	    top: 274px;
	    left: 298px;
	    width: 125px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-ica{
	    top: 204px;
	    left: 615px;
	    width: 188px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-jota {
	    top: 194px;
	    left: 258px;
	    width: 51px;
	    filter: invert(78%) sepia(99%) saturate(96%) hue-rotate(706deg) brightness(88%) contrast(119%);
	}
	.la-linda {
	    top: 169px;
	    left: 647px;
	    width: 133px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-maria {
	    top: 159px;
	    left: 571px;
	    width: 69px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-bella {
	    top: 141px;
	    left: 299px;
	    width: 109px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(38deg) brightness(100%) contrast(80%);
	}
	.la-mora {
	    top: 243px;
	    left: 163px;
	    width: 82px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-pureza {
	    top: 158px;
	    left: 243px;
	    width: 105px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.marbella {
		top: 286px;
	    left: 577px;
	    width: 174px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.mulatal {
	    top: 335px;
	    left: 538px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.san-jose-del-nare {
	    top: 289px;
	    left: 205px;
	    width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.montemar{
	    top: 388px;
	    left: 639px;
	    width: 142px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.penas-azules {
	    top: 181px;
	    left: 497px;
 	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.playa-rica {
	    top: 401px;
	    left: 178px;
	    width: 157px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.quiebra-honda {
	    top: 239px;
	    left: 430px;
	    width: 88px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-antonio{
		top: 247px;
	    left: 517px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.san-javier {
	    top: 151px;
	    left: 121px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-joaquin {
	    top: 230px;
	    left: 636px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.san-jose-del-nus {
	    top: 206px;
	    left: 775px;
	    width: 31px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.san-juan {
	    top: 257px;
	    left: 118px;
    	width: 68px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.providencia{
	    top: 148px;
	    left: 547px;
	    width: 37px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-matias {
	    top: 172px;
	    left: 195px;
	    width: 53px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.san-pablo {
	    top: 345px;
	    left: 339px;
    	width: 181px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.santa-barbara{
	    top: 206px;
	    left: 6px;
	    width: 127px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.santa-isabel{
		top: 470px;
	    left: 632px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.santa-teresa {
	    top: 463px;
	    left: 688px;
	    width: 207px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.villanueva {
	    top: 192px;
	    left: 526px;
	    width: 79px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.palmas {
	    top: 320px;
	    left: 509px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-vicente {
	    top: 242px;
	    left: 273px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.santo-domingo {
		top: 314px;
	    left: 419px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-rita {
	    top: 700px;
	    left: 451px;
	    width: 95px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
		top: 297px;
	    left: 715px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.san-martin{
	    top: 642px;
	    left: 592px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-lorenzo {
		top: 257px;
	    left: 542px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 252px;
	    left: 361px;
	    width: 44px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.zona-urbana{
	    top: 228px;
	    left: 217px;
	    width: 33px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
