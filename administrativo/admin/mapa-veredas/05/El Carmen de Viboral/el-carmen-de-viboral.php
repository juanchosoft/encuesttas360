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
	.agua-bonita{
	    top: 442px;
	    left: 634px;
	    width: 33px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.aguas-claras {
		top: 148px;
	    left: 158px;
	    width: 66px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.aldana {
	    top: 7px;
	    left: 341px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-brasil {
	    top: 379px;
	    left: 609px;
	    width: 60px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-grande {
	    top: 84px;
	    left: 289px;
	    width: 32px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.belen-chaverras{
	    top: 310px;
	    left: 383px;
	    width: 146px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.betania {
		top: 155px;
	    left: 289px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.campo-alegre {
		top: 174px;
	    left: 253px;
	    width: 64px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.boqueron {
		top: 222px;
	    left: 350px;
	    width: 41px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.camargo {
	    top: 154px;
	    left: 245px;
	    width: 34px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.cipres {
		top: 326px;
	    left: 571px;
	    width: 35px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.corales {
	    top: 292px;
	    left: 501px;
	    width: 78px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.cristo-rey {
		top: 75px;
	    left: 213px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.dos-quebradas {
	    top: 337px;
	    left: 594px;
	    width: 93px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-cerro{
	    top: 181px;
	    left: 209px;
	    width: 56px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.guamito{
	    top: 197px;
	    left: 153px;
	    width: 46px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.el-cocuyo {
	    top: 498px;
	    left: 666px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-estio {
		top: 570px;
	    left: 656px;
	    width: 52px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-mazorcal {
	    top: 351px;
	    left: 280px;
	    width: 100px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(38deg) brightness(100%) contrast(80%);
	}
	.guarino{
	    top: 167px;
	    left: 353px;
	    width: 139px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-porvenir {
	    top: 568px;
	    left: 572px;
	    width: 129px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-aguada {
	    top: 377px;
	    left: 519px;
	    width: 55px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.el-retiro {
	    top: 475px;
	    left: 697px;
	    width: 56px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-roblal {
	    top: 438px;
	    left: 655px;
	    width: 59px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.el-salado {
	    top: 68px;
	    left: 165px;
	    width: 53px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-cascada {
		top: 425px;
	    left: 593px;
	    width: 75px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-chapa{
	    top: 197px;
	    left: 297px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-cristalina {
		top: 508px;
	    left: 591px;
	    width: 97px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-esperanza {
	    top: 215px;
	    left: 443px;
	    width: 105px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-honda {
	    top: 462px;
	    left: 374px;
	    width: 135px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-hundida {
	    top: 228px;
	    left: 518px;
	    width: 14px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-aurora {
	    top: 119px;
	    left: 285px;
	    width: 50px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-linda {
	    top: 368px;
	    left: 365px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-madera {
	    top: 240px;
	    left: 239px;
	    width: 96px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-palizada {
	    top: 419px;
	    left: 452px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-florida {
		top: 250px;
	    left: 294px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.la-represa {
	    top: 412px;
	    left: 548px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.quirama {
		top: 77px;
	    left: 147px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.rivera{
	    top: 70px;
	    left: 281px;
	    width: 96px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.las-acacias {
		top: 350px;
	    left: 266px;
	    width: 48px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-milagrosa {
		top: 209px;
	    left: 190px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.las-garzonas {
	    top: 5px;
	    left: 227px;
	    width: 37px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-palma {
	    top: 60px;
	    left: 252px;
	    width: 48px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.mirasol{
	    top: 348px;
	    left: 521px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.la-pinuela {
	    top: 300px;
	    left: 486px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.morros {
		top: 347px;
	    left: 557px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.samaria {
	    top: 101px;
	    left: 194px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.santa-ana {
	    top: 58px;
	    left: 182px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.la-sonadora{
	    top: 149px;
	    left: 202px;
	    width: 63px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-ines {
	    top: 381px;
	    left: 559px;
	    width: 61px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.vallejuelito {
	    top: 296px;
	    left: 323px;
	    width: 89px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.viboral {
	    top: 129px;
	    left: 319px;
	    width: 83px;
		filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-trinidad {
	    top: 4px;
	    left: 248px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-vega {
	    top: 409px;
	    left: 421px;
	    width: 16px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.la-veta {
	    top: 335px;
	    left: 678px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.san-miguel {
	    top: 307px;
	    left: 332px;
	    width: 48px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.villa-hermosa {
	    top: 369px;
	    left: 354px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-vicente {
	    top: 243px;
	    left: 498px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.santo-domingo {
	    top: 493px;
	    left: 390px;
	    width: 141px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-rita {
		top: 478px;
	    left: 507px;
	    width: 108px;
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
	    top: 247px;
	    left: 355px;
	    width: 157px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 369px;
	    left: 429px;
	    width: 113px;
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
	    top: 113px;
	    left: 249px;
	    width: 52px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
