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
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.bella-palmira{
	    top: 316px;
	    left: 514px;
	    width: 107px;
	}
	.caceri {
		top: 406px;
	    left: 555px;
	    width: 125px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.campo-alegre {
	    top: 103px;
	    left: 137px;
	    width: 150px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.cuturu {
	    top: 488px;
	    left: 810px;
	    width: 53px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-almendro{
	    top: 711px;
	    left: 562px;
	    width: 50px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.el-brasil {
	    top: 187px;
	    left: 606px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-descanso {
	    top: 506px;
	    left: 642px;
	    width: 104px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-man {
	    top: 227px;
	    left: 146px;
	    width: 121px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-palomar {
	    top: 147px;
	    left: 546px;
	    width: 138px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-pando{
	    top: 451px;
	    left: 418px;
	    width: 93px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-tigre {
	    top: 178px;
	    left: 336px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-tigre-1 {
	    top: 462px;
	    left: 487px;
	    width: 86px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-tigre-2 {
	    top: 507px;
	    left: 506px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-tigre-3 {
	    top: 583px;
	    left: 515px;
	    width: 37px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.el-toro{
	    top: 373px;
	    left: 307px;
	    width: 89px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(704deg) brightness(118%) contrast(119%);
	}
	.el-brasil {
	    top: 243px;
	    left: 599px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.jagua-arriba {
	    top: 633px;
	    left: 508px;
	    width: 77px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.kilometro-18 {
	    top: 323px;
	    left: 392px;
	    width: 117px;
	}
	.la-arenosa{
	    top: 482px;
	    left: 718px;
	    width: 74px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-caseta {
	    top: 618px;
	    left: 526px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.guatinajo {
	    top: 282px;
	    left: 310px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(491deg) brightness(118%) contrast(119%);
	}	
	.la-catalina {
	    top: 308px;
	    left: 393px;
	    width: 274px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-corcobada{
	    top: 227px;
	    left: 454px;
	    width: 164px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-escuela{
	    top: 483px;
	    left: 732px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-jagua {
	    top: 793px;
	    left: 317px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-esmeralda {
	    top: 128px;
	    left: 372px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-gloria {
		top: 691px;
	    left: 557px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-ilusion {
	    top: 37px;
	    left: 384px;
	    width: 191px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-jagua {
	    top: 617px;
	    left: 562px;
	    width: 99px;
	    filter: invert(75%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-raya{
	    top: 436px;
	    left: 464px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-garrapata {
	    top: 601px;
	    left: 639px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(58deg) brightness(100%) contrast(80%);
	}
	.las-batatas {
		top: 678px;
	    left: 606px;
	    width: 67px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.las-malvinas {
	    top: 161px;
	    left: 288px;
	    width: 139px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.las-negras {
	    top: 726px;
	    left: 509px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%);
	}
	.las-parcelas{
		top: 501px;
	    left: 562px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-virgen {
	    top: 434px;
	    left: 386px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(359deg) brightness(100%) contrast(80%);
	}
	.los-mangos {
	    top: 229px;
	    left: 274px;
	    width: 99px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.margento {
	    top: 6px;
	    left: 550px;
	    width: 151px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.palanca {
	    top: 167px;
	    left: 432px;
	    width: 124px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.puerto-colombia {
	    top: 765px;
	    left: 522px;
	    width: 49px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.puerto-triana {
	    top: 556px;
	    left: 673px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.quebradona-arriba {
	    top: 558px;
	    left: 536px;
	    width: 84px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.quebradona-del-medio{
		top: 503px;
	    left: 576px;
	    width: 114px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.santa-rosita {
		top: 209px;
	    left: 88px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(254deg) brightness(100%) contrast(80%);
	}
	.quitasol {
		top: 465px;
	    left: 656px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(329deg) brightness(100%) contrast(80%);
	}
	.rio-viejo {
	    top: 58px;
	    left: 243px;
	    width: 149px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.veracruz {
	    top: 705px;
	    left: 519px;
	    width: 46px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.zona-urbana {
	    top: 197px;
	    left: 260px;
	    width: 36px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
