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
	.alto-la-aldea {
	    top: 356px;
	    left: 369px;
	    width: 32px;
	}
	.apucarco {
	    top: 572px;
	    left: 432px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.bellavista {
		top: 383px;
	    left: 389px;
	    width: 68px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.botija-abajo {
	    top: 285px;
	    left: 242px;
	    width: 88px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.botija-arriba {
		top: 294px;
	    left: 306px;
	    width: 51px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.buena-vista{
	    top: 156px;
	    left: 433px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.buenos-aires {
	    top: 573px;
	    left: 495px;
	    width: 48px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.caracolal {
		top: 371px;
	    left: 405px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.cestillal {
		top: 301px;
	    left: 346px;
	    width: 50px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.chontaduro {
		top: 400px;
	    left: 329px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.chupadero {
	    top: 178px;
	    left: 434px;
	    width: 83px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.ciriguan{
	    top: 619px;
	    left: 542px;
	    width: 89px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.cumbarra {
		top: 330px;
	    left: 310px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-cafe {
	    top: 360px;
	    left: 424px;
	    width: 49px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.el-canelito {
		top: 562px;
	    left: 415px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-canelo {
		top: 517px;
	    left: 419px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-madero{
		top: 573px;
	    left: 529px;
	    width: 113px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-paso {
		top: 472px;
	    left: 426px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.el-retiro {
		top: 364px;
	    left: 476px;
	    width: 117px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-roble {
	    top: 414px;
	    left: 441px;
	    width: 69px;
	}
	.el-socorro {
	    top: 336px;
	    left: 406px;
	    width: 89px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.guadual {
		top: 442px;
	    left: 384px;
	    width: 43px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.guayabal {
	    top: 357px;
	    left: 345px;
	    width: 36px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.insor {
		top: 625px;
	    left: 447px;
	    width: 122px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.juntas{
	    top: 142px;
	    left: 465px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-aguada {
		top: 237px;
	    left: 383px;
	    width: 108px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-aldea {
	    top: 382px;
	    left: 376px;
	    width: 18px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-balsa {
	    top: 474px;
	    left: 369px;
	    width: 51px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-balsita {
	    top: 475px;
	    left: 397px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-campina {
		top: 554px;
	    left: 492px;
	    width: 30px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-curva{
		top: 324px;
	    left: 438px;
	    width: 60px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-cusuti {
	    top: 504px;
	    left: 516px;
	    width: 106px;
	}
	.la-esperanza {
	    top: 341px;
	    left: 250px;
	    width: 93px;
	    filter: invert(48%) sepia(57%) saturate(76%) hue-rotate(31deg) brightness(118%) contrast(119%) ;
	}
	.la-estrella {
		top: 427px;
	    right: 310px;
	    width: 122px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}	
	.la-llorona {
		top: 457px;
	    right: 380px;
	    width: 52px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}
	.la-loma {
	    top: 360px;
	    left: 390px;
	    width: 27px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}
	.la-manga {
		top: 284px;
	    left: 492px;
	    width: 158px;
	    filter: invert(48%) sepia(57%) saturate(76%) hue-rotate(31deg) brightness(118%) contrast(119%) ;
	}		
	.la-quiebra {
	    top: 241px;
	    left: 305px;
	    width: 63px;
	    filter: invert(48%) sepia(57%) saturate(796%) hue-rotate(153deg) brightness(118%) contrast(119%) ;
	}
	.la-union {
	    top: 560px;
	    left: 466px;
	    width: 39px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}		
	.leon {
		top: 460px;
	    left: 428px;
	    width: 46px;
	    filter: invert(30%) sepia(388%) saturate(302%) hue-rotate(212deg) brightness(95%) contrast(60%) ;
	}
	.llano-grande {
		top: 365px;
	    left: 464px;
	    width: 55px;
	    filter: invert(100%) sepia(33%) saturate(976%) hue-rotate(104deg) brightness(118%) contrast(59%) ;
	}
	.loma-la-alegria {
	    top: 493px;
	    left: 459px;
	    width: 24px;
	}
	.los-antioquenos {
		top: 170px;
	    left: 420px;
	    width: 28px;
	    filter: invert(48%) sepia(57%) saturate(796%) hue-rotate(214deg) brightness(118%) contrast(119%) ;
	}
	.los-naranjos {
	    top: 561px;
	    left: 508px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.macanal{
	    top: 641px;
	    left: 536px;
	    width: 39px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.media-cuesta {
		top: 535px;
	    left: 440px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.membrillal {
		top: 436px;
	    left: 421px;
	    width: 49px;
	    filter: invert(85%) sepia(190%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.moroto {
	    top: 473px;
	    left: 336px;
	    width: 50px;
	}
	.paso-arriba {
		top: 525px;
	    left: 402px;
	    width: 42px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.pernilla {
		top: 465px;
	    left: 351px;
	    width: 53px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.rubicon {
		top: 374px;
	    left: 298px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.san-jose {
	    top: 149px;
	    left: 504px;
	    width: 126px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-julian{
		top: 5px;
	    left: 480px;
	    width: 179px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.san-luis {
	    top: 213px;
	    left: 501px;
	    width: 126px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.san-luis-del-cafe {
	    top: 385px;
	    left: 443px;
	    width: 43px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.san-miguel {
	    top: 187px;
	    left: 320px;
	    width: 83px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.san-pascual {
		top: 274px;
	    left: 410px;
	    width: 130px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.santa-barbara {
		top: 409px;
	    left: 384px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.santo-cristo {
	    top: 268px;
	    left: 373px;
	    width: 73px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.santo-domingo{
		top: 182px;
	    left: 383px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.uvital {
	    top: 322px;
	    left: 367px;
	    width: 47px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 524px;
	    left: 463px;
	    width: 44px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
