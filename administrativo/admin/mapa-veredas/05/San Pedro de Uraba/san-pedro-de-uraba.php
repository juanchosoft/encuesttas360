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
	    width: 950px;
	    height: 1000px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.almagras{
	    top: 445px;
	    left: 401px;
	    width: 64px;
	}
	.almagritas {
	    top: 542px;
	    left: 391px;
	    width: 117px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.alto-del-rosario {
	    top: 681px;
	    left: 287px;
	    width: 62px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.alto-san-juan {
	    top: 875px;
	    left: 274px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.angostura{
	    top: 706px;
	    left: 281px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.arenas-monas {
	    top: 187px;
	    left: 606px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.barbasco {
	    top: 404px;
	    left: 289px;
	    width: 68px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.betania {
	    top: 229px;
	    left: 529px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.botella-de-oro {
	    top: 480px;
	    left: 279px;
	    width: 103px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.buenavista{
	    top: 348px;
	    left: 625px;
	    width: 91px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.cabecera-municipal {
	    top: 594px;
	    left: 313px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.caiman-san-pablo {
	    top: 344px;
	    left: 266px;
	    width: 65px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.cantagallo {
	    top: 193px;
	    left: 574px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.caracoli {
	    top: 263px;
	    left: 548px;
	    width: 118px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-aji{
	    top: 799px;
	    left: 336px;
	    width: 66px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(704deg) brightness(118%) contrast(119%) ;
	}
	.el-brasil {
	    top: 387px;
	    left: 334px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.el-caiman {
		top: 354px;
	    left: 312px;
	    width: 49px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-cano {
	    top: 547px;
	    left: 289px;
	    width: 72px;
	}
	.el-pozon{
		top: 294px;
	    left: 348px;
	    width: 122px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-pueblito {
	    top: 662px;
	    left: 392px;
	    width: 49px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-tomate {
	    top: 280px;
	    left: 269px;
	    width: 111px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(491deg) brightness(118%) contrast(119%) ;
	}	
	.el-zumbido {
	    top: 509px;
	    left: 338px;
	    width: 106px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.filo-pancho{
	    top: 624px;
	    left: 425px;
	    width: 48px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.guartinajo{
	    top: 576px;
	    left: 397px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-rosita {
	    top: 793px;
	    left: 317px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-ceiba {
	    top: 154px;
	    left: 526px;
	    width: 69px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-florida {
	    top: 317px;
	    left: 234px;
	    width: 61px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-nevada {
		top: 454px;
	    left: 374px;
    	width: 45px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-rosita {
	    top: 358px;
	    left: 558px;
	    width: 45px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-rula{
	    top: 840px;
	    left: 304px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-cabana {
	    top: 793px;
	    left: 269px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(58deg) brightness(100%) contrast(80%) ;
	}
	.las-pavas {
	    top: 573px;
	    left: 416px;
	    width: 72px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.los-almendros {
	    top: 327px;
	    left: 582px;
	    width: 110px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.los-burros {
	    top: 318px;
	    left: 574px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%) ;
	}
	.macondo{
	    top: 288px;
    	left: 454px;
    	width: 72px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.mayupa {
	    top: 846px;
	    left: 258px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(359deg) brightness(100%) contrast(80%) ;
	}
	.molinillo {
	    top: 398px;
	    left: 512px;
	    width: 141px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.morroa {
	    top: 109px;
	    left: 438px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.palma-de-vino {
		top: 118px;
	    left: 557px;
	    width: 97px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.parcelas-de-macondo {
	    top: 303px;
	    left: 511px;
	    width: 75px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.patio-bonito {
	    top: 214px;
	    left: 513px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.pelayito {
		top: 4px;
	    left: 444px;
	    width: 120px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.piru{
	    top: 671px;
	    left: 327px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.quebrada-del-medio {
	    top: 643px;
	    left: 364px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(254deg) brightness(100%) contrast(80%) ;
	}
	.pollo-flaco {
		top: 142px;
	    left: 491px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(329deg) brightness(100%) contrast(80%) ;
	}
	.santa-rosa {
		top: 615px;
	    left: 823px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.ralito {
	    top: 95px;
	    left: 582px;
	    width: 90px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.san-antonio {
		top: 235px;
	    left: 604px;
	    width: 66px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.san-jacinto-abajo {
	    top: 751px;
	    left: 350px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.san-jacinto-arriba{
	    top: 702px;
	    left: 370px;
	    width: 47px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.san-miguel {
	    top: 255px;
	    left: 429px;
	    width: 96px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.santa-catalina {
	    top: 299px;
	    left: 381px;
	    width: 118px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.santa-rosa {
		top: 373px;
	    left: 496px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(52deg) brightness(100%) contrast(80%) ;
	}
	.santa-rosa-arriba {
	    top: 622px;
	    left: 265px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.tacanal {
	    top: 724px;
	    left: 322px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1834deg) brightness(100%) contrast(80%) ;
	}
	.tatono {
	    top: 335px;
	    left: 347px;
	    width: 54px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.tinajon {
	    top: 503px;
	    left: 456px;
	    width: 67px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(85deg) brightness(88%) contrast(119%) ;
	}
	.tio-docto{
	    top: 414px;
	    left: 274px;
	    width: 91px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.trementino {
	    top: 399px;
	    left: 439px;
	    width: 112px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.tres-esquinas {
	    top: 726px;
	    left: 387px;
	    width: 35px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.zapindonga {
	    top: 48px;
	    left: 473px;
	    width: 124px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.zapindonga-arriba {
		top: 25px;
	    left: 547px;
	    width: 100px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.zona-urbana {
		top: 615px;
	    left: 297px;
	    width: 52px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
