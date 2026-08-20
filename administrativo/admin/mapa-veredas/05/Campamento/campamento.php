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
	.canaveral {
	    top: 607px;
	    left: 261px;
	    width: 143px;
	}
	.capotal {
		top: 31px;
	    left: 340px;
	    width: 49px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.caracolal {
	    top: 557px;
	    left: 498px;
	    width: 53px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.chaquiral {
	    top: 673px;
	    left: 562px;
	    width: 72px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.cordillera {
	    top: 593px;
	    left: 531px;
	    width: 31px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-barcino{
	    top: 487px;
	    left: 518px;
	    width: 122px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.el-bosque {
	    top: 337px;
	    left: 490px;
	    width: 61px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.el-carriel {
	    top: 242px;
	    left: 455px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-guadual {
	    top: 184px;
	    left: 335px;
	    width: 102px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-limon {
	    top: 232px;
	    left: 338px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-manzanillo {
		top: 553px;
	    left: 441px;
	    width: 72px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-oso{
		top: 612px;
	    left: 427px;
	    width: 48px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-pinal {
	    top: 434px;
	    left: 368px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-reposo {
	    top: 438px;
	    left: 394px;
	    width: 84px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.el-yerbal {
	    top: 458px;
	    left: 518px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.guaduas {
	    top: 512px;
	    left: 487px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-ceiba{
	    top: 144px;
	    left: 356px;
	    width: 84px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-chiquita {
	    top: 613px;
	    left: 504px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-colmena {
		top: 581px;
	    left: 435px;
	    width: 42px;
	    filter: invert(85%) sepia(190%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-concha {
	    top: 351px;
	    left: 388px;
	    width: 70px;
	}
	.la-frisolera {
	    top: 613px;
	    left: 542px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-irlanda {
		top: 715px;
	    left: 581px;
	    width: 54px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-luz {
	    top: 432px;
	    left: 307px;
	    width: 94px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-polka {
		top: 311px;
	    left: 441px;
	    width: 113px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-primavera{
		top: 466px;
	    left: 504px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-quiebra {
		top: 291px;
	    left: 371px;
	    width: 92px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-solita {
	    top: 411px;
	    left: 470px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-travesia {
	    top: 638px;
	    left: 386px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.llanadas {
	    top: 444px;
	    left: 486px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.los-chorros {
		top: 537px;
	    left: 330px;
	    width: 112px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.los-mangos{
		top: 556px;
	    left: 574px;
	    width: 54px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.los-ranchos {
	    top: 638px;
	    right: 427px;
	    width: 51px;
	}
	.montanita {
	    top: 553px;
	    left: 542px;
	    width: 48px;
	    filter: invert(48%) sepia(57%) saturate(76%) hue-rotate(31deg) brightness(118%) contrast(119%) ;
	}
	.naranjal {
	    top: 451px;
	    right: 404px;
	    width: 20px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}	
	.norizal {
	    top: 400px;
	    right: 402px;
	    width: 66px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}
	.plan-de-la-rosa {
		top: 523px;
	    left: 451px;
	    width: 44px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}
	.quebrada-negra {
		top: 464px;
	    left: 392px;
	    width: 105px;
	    filter: invert(48%) sepia(57%) saturate(76%) hue-rotate(31deg) brightness(118%) contrast(119%) ;
	}		
	.quebradona {
	    top: 254px;
	    left: 386px;
	    width: 33px;
	    filter: invert(48%) sepia(57%) saturate(796%) hue-rotate(153deg) brightness(118%) contrast(119%) ;
	}
	.rio-abajo {
	    top: 376px;
	    left: 319px;
	    width: 78px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}		
	.san-antonio {
	    top: 222px;
	    left: 414px;
	    width: 62px;
	    filter: invert(30%) sepia(388%) saturate(302%) hue-rotate(212deg) brightness(95%) contrast(60%) ;
	}
	.san-jose-de-la-gloria {
		top: 414px;
	    left: 396px;
	    width: 48px;
	    filter: invert(100%) sepia(33%) saturate(976%) hue-rotate(104deg) brightness(118%) contrast(59%) ;
	}
	.san-pablo {
	    top: 601px;
	    left: 579px;
	    width: 51px;
	}
	.tierra-fria {
		top: 274px;
	    left: 344px;
	    width: 75px;
	    filter: invert(48%) sepia(57%) saturate(796%) hue-rotate(214deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 637px;
	    left: 426px;
	    width: 22px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
