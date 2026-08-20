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
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.alta-vista {
	    top: 719px;
	    left: 385px;
	    width: 60px;
	}
	.cativo {
	    top: 6px;
	    left: 511px;
	    width: 177px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.las-azules {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.coloradas {
	    top: 406px;
	    left: 313px;
	    width: 129px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.chaparral {
	    top: 329px;
	    left: 469px;
	    width: 145px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(818deg) brightness(100%) contrast(119%);
	}	
	.cordillera {
	    top: 473px;
	    left: 392px;
	    width: 107px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.el-carmen {
	    top: 269px;
	    left: 338px;
	    width: 65px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-chorrillo{
	    top: 450px;
	    left: 483px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-churimbo{
	    top: 174px;
	    left: 159px;
	    width: 193px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-espinal {
		top: 344px;
	    left: 575px;
	    width: 152px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.el-filo {
	    top: 649px;
	    left: 381px;
	    width: 69px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-guasimo {
	    top: 310px;
	    left: 465px;
	    width: 84px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.el-jaque {
	    top: 505px;
	    left: 501px;
	    width: 135px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-madero{
	    top: 540px;
	    left: 398px;
	    width: 52px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(464deg) brightness(119%) contrast(119%);
	}
	.el-pedregal{
	    top: 400px;
	    left: 537px;
	    width: 127px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-pescado{
	    top: 473px;
	    left: 445px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-plan {
	    top: 692px;
	    left: 394px;
	    width: 51px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.el-rodeo {
	    top: 84px;
	    left: 412px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(2162deg) brightness(118%) contrast(119%);
	}
	.el-tunal{
	    top: 90px;
	    left: 562px;
	    width: 206px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.guasabra {
	    top: 552px;
	    left: 314px;
	    width: 98px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.guasimal {
	    top: 578px;
	    left: 502px;
	    width: 146px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.kilometro-2 {
	    top: 245px;
	    left: 550px;
	    width: 167px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.kilometro-14 {
	    top: 224px;
	    left: 521px;
	    width: 55px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(2162deg) brightness(118%) contrast(119%);
	}
	.fatima{
	    top: 581px;
	    left: 376px;
	    width: 81px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-aldea{
		top: 206px;
	    left: 399px;
	    width: 170px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-mesa{
	    top: 547px;
	    left: 412px;
	    width: 54px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-noque{
	    top: 604px;
	    left: 479px;
	    width: 244px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.las-azules {
	    top: 300px;
	    left: 143px;
	    width: 221px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-tolda {
	    top: 551px;
	    left: 466px;
	    width: 98px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.laureles{
	    top: 518px;
	    left: 313px;
	    width: 110px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%);
	}
	.mariana {
	    top: 416px;
	    left: 433px;
	    width: 106px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1082deg) brightness(88%) contrast(119%);
	}
	.milagrosa-alta {
	    top: 369px;
	    left: 118px;
	    width: 163px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(1894deg) brightness(118%) contrast(119%);
	}
	.milagrosa-baja {
		top: 383px;
	    left: 266px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(10deg) brightness(100%) contrast(80%);
	}
	.moraditas {
	    top: 647px;
	    left: 427px;
	    width: 110px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.nuqui{
	    top: 441px;
	    left: 384px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(22deg) brightness(118%) contrast(119%);
	}
	.obregon{
	    top: 489px;
	    left: 628px;
	    width: 90px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.ogasco{
		top: 250px;
	    left: 376px;
	    width: 125px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(964deg) brightness(119%) contrast(119%);
	}
	.paso-real {
		top: 242px;
	    left: 694px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.san-antonio {
		top: 329px;
	    left: 309px;
    	width: 159px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(1418deg) brightness(100%) contrast(119%);
	}	
	.san-carlos {
	    top: 341px;
	    left: 403px;
	    width: 79px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.socorro-de-sabanas {
	    top: 568px;
	    left: 421px;
	    width: 139px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.tonusco-arriba{
	    top: 131px;
	    left: 195px;
	    width: 190px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.yerbabuenal{
	    top: 170px;
	    left: 359px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.zona-urbana {
	    top: 329px;
	    left: 676px;
	    width: 51px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>