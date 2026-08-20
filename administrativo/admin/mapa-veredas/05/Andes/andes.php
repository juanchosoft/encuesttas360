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
	}
	.alto-canaveral{
		top: 269px;
	    left: 676px;
	    width: 122px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-del-rayo {
	    top: 239px;
	    left: 559px;
	    width: 37px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-senon {
		top: 332px;
	    left: 696px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-crucero {
	    top: 501px;
	    left: 550px;
	    width: 73px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.buenos-aires {
	    top: 230px;
	    left: 718px;
	    width: 13px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.bajo-canaveral{
	    top: 171px;
	    left: 622px;
	    width: 72px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.california {
		top: 197px;
	    left: 472px;
	    width: 62px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.cascajero {
		top: 270px;
	    left: 477px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.chaparralito {
	    top: 292px;
	    left: 563px;
	    width: 30px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.egipto {
	    top: 420px;
	    left: 497px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-barcino {
	    top: 28px;
	    left: 614px;
	    width: 38px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.el-cardal {
		top: 264px;
	    left: 394px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-cedron {
		top: 550px;
	    left: 548px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-chispero {
	    top: 284px;
	    left: 469px;
	    width: 34px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-ignacio{
		top: 6px;
	    left: 560px;
	    width: 56px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.guaimaral{
	    top: 356px;
	    left: 558px;
	    width: 47px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.el-libano {
	    top: 287px;
	    left: 494px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-rojo {
	    top: 255px;
	    left: 650px;
	    width: 35px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-tapado {
	    top: 215px;
	    left: 514px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-aguada{
		top: 318px;
	    left: 559px;
	    width: 33px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-argentina {
		top: 217px;
	    left: 671px;
	    width: 112px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-avanzada {
	    top: 592px;
	    left: 536px;
	    width: 38px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-bodega {
	    top: 171px;
	    left: 591px;
	    width: 47px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-borraja {
	    top: 565px;
	    left: 433px;
	    width: 116px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.la-cedrona {
	    top: 334px;
	    left: 557px;
	    width: 60px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-clara {
	    top: 420px;
	    left: 522px;
	    width: 52px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-comuna{
	    top: 241px;
	    left: 542px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-cristalina {
	    top: 599px;
	    left: 490px;
	    width: 133px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-ermita {
	    top: 213px;
	    left: 347px;
    	width: 80px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-esperanza {
	    top: 4px;
	    left: 561px;
	    width: 76px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-legia {
	    top: 186px;
	    left: 412px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-manuela {
	    top: 63px;
	    left: 560px;
	    width: 91px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-mesenia {
	    top: 659px;
	    left: 491px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-piedra {
	    top: 289px;
	    left: 254px;
	    width: 207px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-pradera {
	    top: 151px;
	    left: 566px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-rochela {
	    top: 177px;
	    left: 267px;
	    width: 139px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.las-colonias {
	    top: 253px;
	    left: 575px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.palestina {
	    top: 223px;
	    left: 610px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.reserva-forestal{
		top: 265px;
	    left: 103px;
	    width: 445px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.las-flores {
		top: 517px;
	    left: 438px;
	    width: 114px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-siria {
	    top: 189px;
	    left: 305px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-soledad {
		top: 423px;
	    left: 417px;
	    width: 82px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-solita {
	    top: 160px;
	    left: 522px;
	    width: 51px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.media-luna {
	    top: 383px;
	    left: 541px;
	    width: 60px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.monte-blanco{
	    top: 274px;
	    left: 611px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.monte-verde {
	    top: 237px;
	    left: 434px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.orizaba {
	    top: 114px;
	    left: 587px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.rio-claro {
		top: 413px;
	    left: 555px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.risaralda {
	    top: 295px;
	    left: 646px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.san-agustin{
	    top: 355px;
	    left: 256px;
	    width: 266px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-antonio {
	    top: 487px;
	    left: 461px;
	    width: 101px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.san-bartolo {
	    top: 322px;
	    left: 610px;
	    width: 99px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.san-carlos {
	    top: 142px;
	    left: 654px;
	    width: 45px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-fernando {
	    top: 192px;
	    left: 652px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-gregorio {
	    top: 429px;
	    left: 482px;
	    width: 39px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.san-julian {
		top: 564px;
	    left: 520px;
	    width: 41px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.san-miguel {
	    top: 240px;
	    left: 434px;
	    width: 71px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-pedro-abajo {
	    top: 378px;
	    left: 510px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.san-pedro-arriba {
		top: 348px;
	    left: 472px;
	    width: 91px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.san-perucho{
	    top: 294px;
	    left: 428px;
	    width: 77px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.santa-elena {
	    top: 446px;
	    left: 516px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.santa-ines {
	    top: 563px;
	    left: 536px;
	    width: 14px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.santa-isabel {
	    top: 569px;
	    left: 347px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.santa-rita {
	    top: 424px;
	    left: 491px;
	    width: 10px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.valle-umbria{
	    top: 129px;
	    left: 672px;
	    width: 90px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.yarumal {
		top: 55px;
	    left: 619px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.zona-urbana{
		top: 274px;
	    left: 582px;
	    width: 36px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
