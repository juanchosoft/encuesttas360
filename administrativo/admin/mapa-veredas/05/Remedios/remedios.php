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
	.altos-de-manila{
		top: 4px;
	    left: 477px;
	    width: 398px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.belen {
	    top: 210px;
	    left: 138px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.bominas {
	    top: 192px;
	    left: 341px;
	    width: 88px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-porvenir {
	    top: 259px;
	    left: 443px;
	    width: 84px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cabuyal {
	    top: 323px;
	    left: 307px;
	    width: 135px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.camelia-quintana{
	    top: 456px;
	    left: 171px;
	    width: 84px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.buenos-aires {
	    top: 568px;
	    left: 395px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.campo-vijao {
	    top: 392px;
	    left: 629px;
	    width: 108px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.canaveral {
	    top: 111px;
	    left: 362px;
	    width: 82px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.chorro-de-lagrimas {
		top: 549px;
	    left: 462px;
	    width: 210px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.chorrolindo {
		top: 279px;
	    left: 148px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.costenal {
	    top: 527px;
	    left: 443px;
	    width: 128px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-chiquillo {
	    top: 562px;
	    left: 469px;
	    width: 123px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-popero {
	    top: 392px;
	    left: 431px;
	    width: 81px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-puna{
	    top: 174px;
	    left: 391px;
	    width: 126px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-silencio{
	    top: 227px;
	    left: 28px;
	    width: 69px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(309deg) brightness(100%) contrast(80%);
	}	
	.el-recreo {
	    top: 455px;
	    left: 481px;
	    width: 163px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-retiro {
	    top: 477px;
	    left: 102px;
	    width: 59px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-salado {
	    top: 257px;
	    left: 307px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.ite{
	    top: 538px;
	    left: 561px;
	    width: 104px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-argentina {
	    top: 73px;
	    left: 412px;
	    width: 130px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-bonita {
	    top: 387px;
	    left: 116px;
	    width: 42px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-brava {
	    top: 360px;
	    left: 169px;
	    width: 50px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-ceiba {
		top: 493px;
	    left: 442px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.la-cianurada {
	    top: 197px;
	    left: 292px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-cruz {
	    top: 467px;
	    left: 385px;
	    width: 81px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-gorgona{
	    top: 377px;
	    left: 487px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-honda-pescado {
	    top: 693px;
	    left: 405px;
	    width: 80px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-mariposa {
		top: 398px;
	    left: 150px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.las-brisas {
	    top: 422px;
	    left: 240px;
	    width: 115px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.juan-brand {
	    top: 270px;
	    left: 224px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-sonadora {
	    top: 299px;
	    left: 372px;
	    width: 186px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.las-palomas {
		top: 261px;
	    left: 131px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.los-lagos {
	    top: 415px;
	    left: 182px;
	    width: 60px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.mani-santana {
	    top: 213px;
	    left: 54px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.mata-arriba {
	    top: 461px;
	    left: 60px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.oca {
	    top: 385px;
	    left: 80px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-mateo {
		top: 278px;
	    left: 71px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.martana{
	    top: 333px;
	    left: 234px;
	    width: 91px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.otu {
		top: 324px;
	    left: 204px;
	    width: 58px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.paso-de-la-mula {
	    top: 292px;
	    left: 545px;
	    width: 144px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.platanares {
	    top: 238px;
	    left: 142px;
	    width: 88px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.san-antonio-el-rio {
	    top: 427px;
	    left: 124px;
	    width: 42px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-bartolo{
		top: 500px;
	    left: 193px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.san-cristobal {
	    top: 457px;
	    left: 132px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-juan-de-capotal {
	    top: 423px;
	    left: 86px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.santa-lucia {
		top: 389px;
	    left: 278px;
	    width: 70px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.santa-marta {
		top: 153px;
	    left: 491px;
	    width: 98px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.rio-bagre{
	    top: 136px;
	    left: 297px;
	    width: 96px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.tias-la-aurora {
	    top: 286px;
	    left: 123px;
	    width: 118px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.ocasito {
	    top: 351px;
	    left: 72px;
	    width: 66px;
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
	    top: 353px;
	    left: 423px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.santa-barbara {
	    top: 217px;
	    left: 458px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
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
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 252px;
	    left: 361px;
	    width: 44px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.palmirita {
	    top: 334px;
	    left: 379px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.pailania {
	    top: 402px;
	    left: 562px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.zona-urbana{
		top: 337px;
	    left: 245px;
	    width: 22px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
