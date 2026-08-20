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
		<?php
		if (!file_exists(__DIR__ . "/../../mapa_veredas.php")) {
			die("❌ El archivo del mapa no existe. ❌");
		}else{
			include __DIR__ . "/../../mapa_veredas.php";
		}
		?>
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
	    height: 1000px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.once-de-noviembre{
		top: 382px;
	    left: 613px;
	    width: 32px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.veintisiente-de-diciembre {
	    top: 223px;
	    left: 511px;
	    width: 32px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-de-mulatos {
	    top: 277px;
	    left: 677px;
	    width: 50px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.aguas-claras {
	    top: 260px;
	    left: 547px;
	    width: 31px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.aguas-frias {
	    top: 450px;
	    left: 626px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.aguas-prietas {
	    top: 96px;
	    left: 751px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.ahuyamita {
	    top: 375px;
	    left: 739px;
	    width: 65px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.ahuyama {
	    top: 341px;
	    left: 719px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.achiote {
	    top: 217px;
	    left: 793px;
	    width: 26px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.alto-caiman {
	    top: 143px;
	    left: 487px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.alto-cirilo {
		top: 188px;
	    left: 530px;
	    width: 35px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.alto-manteca{
	    top: 340px;
	    left: 813px;
	    width: 30px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.altos-de-nueva-fe {
	    top: 270px;
	    left: 824px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.arcua-arriba {
	    top: 430px;
	    left: 642px;
	    width: 51px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.arcua-central {
	    top: 428px;
	    left: 619px;
	    width: 30px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.bajo-cirilo {
		top: 199px;
	    left: 504px;
	    width: 18px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.barro-colorado{
	    top: 387px;
	    left: 584px;
	    width: 30px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.blanquicet {
	    top: 858px;
	    left: 444px;
	    width: 8px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.boca-de-mata-de-platano {
		top: 101px;
	    left: 725px;
	    width: 36px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.boca-limon {
		top: 371px;
	    left: 688px;
	    width: 59px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.bocas-de-atrato {
		top: 333px;
	    left: 424px;
	    width: 7px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.bocas-del-rio-turbo {
		top: 295px;
	    left: 504px;
	    width: 37px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.bocas-del-roto {
	    top: 169px;
	    left: 283px;
	    width: 121px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.bocas-de-tio-lopez{
	    top: 362px;
	    left: 668px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.bonga {
	    top: 200px;
	    left: 738px;
	    width: 23px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.brazo-izquierdo {
		top: 100px;
	    left: 674px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.brunito-abajo {
	    top: 42px;
	    left: 732px;
	    width: 38px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.brunito-arriba {
		top: 86px;
	    left: 711px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.brunito-medio {
	    top: 64px;
	    left: 726px;
	    width: 31px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.buenos-aires {
		top: 944px;
	    left: 475px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.cacahual-abajo {
	    top: 252px;
	    left: 800px;
	    width: 38px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.cacahual-arriba {
		top: 226px;
	    left: 802px;
	    width: 45px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.caimancito {
	    top: 364px;
	    left: 617px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.california {
		top: 441px;
	    left: 573px;
	    width: 10px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.calle-larga {
	    top: 419px;
	    left: 521px;
	    width: 34px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.caraballito {
		top: 395px;
	    left: 683px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.caraballo {
	    top: 394px;
	    left: 647px;
	    width: 48px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.caracoli {
	    top: 294px;
	    left: 658px;
	    width: 32px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.casanova {
	    top: 332px;
	    left: 537px;
	    width: 28px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.casa-roja{
	    top: 132px;
	    left: 779px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.cerritos {
		top: 780px;
	    left: 303px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.cielo-azul {
	    top: 103px;
	    left: 626px;
	    width: 33px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.cienaguita {
	    top: 152px;
	    left: 706px;
	    width: 40px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.cirilo-medio {
		top: 202px;
	    left: 517px;
	    width: 19px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.cocuelo-san-felipe {
	    top: 725px;
	    left: 393px;
	    width: 59px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.comunal-la-suerte{
	    top: 428px;
	    left: 574px;
	    width: 59px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.comunal-san-jorge {
	    top: 403px;
	    left: 534px;
	    width: 56px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.cope {
	    top: 248px;
	    left: 515px;
	    width: 51px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.coquital {
	    top: 280px;
	    left: 804px;
	    width: 26px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.coquitalito {
	    top: 270px;
	    left: 806px;
	    width: 24px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.coquitos {
	    top: 435px;
	    left: 523px;
	    width: 32px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.currulao {
	    top: 419px;
	    left: 604px;
	    width: 28px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-algodon {
	    top: 33px;
	    left: 677px;
	    width: 23px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-azucar {
	    top: 207px;
	    left: 804px;
	    width: 24px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-barro {
	    top: 307px;
	    left: 673px;
	    width: 35px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-bongo{
	    top: 164px;
	    left: 802px;
	    width: 23px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-cahual {
		top: 237px;
	    left: 605px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-caiman {
	    top: 911px;
	    left: 456px;
	    width: 63px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-cedro {
	    top: 849px;
	    left: 443px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.el-cocuelo {
	    top: 803px;
	    left: 354px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.el-congo{
		top: 152px;
	    left: 769px;
	    width: 88px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-dos {
	    top: 288px;
	    left: 572px;
	    width: 19px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-esfuerzo {
	    top: 361px;
	    left: 573px;
	    width: 24px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-indio {
	    top: 103px;
	    left: 596px;
	    width: 36px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-limon {
		top: 342px;
	    left: 683px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-limon-guadual {
		top: 334px;
	    left: 623px;
	    width: 39px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-olleto {
		top: 47px;
	    left: 695px;
	    width: 40px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-palmito {
	    top: 166px;
	    left: 746px;
	    width: 17px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-porvenir {
		top: 290px;
	    left: 560px;
	    width: 20px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-porvenir-de-tulapa {
		top: 128px;
	    left: 622px;
	    width: 38px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-recreo{
	    top: 406px;
	    left: 547px;
	    width: 7px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.el-refugio {
	    top: 362px;
	    left: 564px;
	    width: 15px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-tagual {
	    top: 299px;
	    left: 765px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-tonel {
	    top: 360px;
	    left: 557px;
	    width: 14px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-tres {
	    top: 358px;
	    left: 593px;
	    width: 6px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.el-veinte {
	    top: 782px;
	    left: 423px;
	    width: 56px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2106deg) brightness(88%) contrast(119%);
	}
	.el-venado {
	    top: 358px;
	    left: 768px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-volcan {
	    top: 69px;
	    left: 786px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.el-volcan-2{
		top: 279px;
	    left: 622px;
	    width: 61px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.eugenia-arriba {
	    top: 973px;
	    left: 460px;
	    width: 22px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.eugenia-media {
	    top: 939px;
	    left: 441px;
	    width: 30px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.galilea {
	    top: 178px;
	    left: 694px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.guacamaya {
	    top: 909px;
	    left: 438px;
	    width: 47px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.guadualito {
		top: 374px;
	    left: 554px;
	    width: 13px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.guayabal-abajo {
	    top: 150px;
	    left: 804px;
	    width: 31px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.guayabal-arriba{
	    top: 155px;
	    left: 824px;
	    width: 28px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(2938deg) brightness(100%) contrast(80%);
	}
	.gustavo-mejia {
	    top: 448px;
	    left: 615px;
	    width: 11px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.hacienda-currulao {
	    top: 403px;
	    left: 623px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.honduras {
	    top: 429px;
	    left: 573px;
	    width: 13px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.isaias {
	    top: 138px;
	    left: 643px;
	    width: 55px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.isaias-arriba {
	    top: 158px;
	    left: 673px;
	    width: 40px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.juan-benitez{
		top: 273px;
	    left: 700px;
	    width: 45px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.juan-moreno {
		top: 355px;
	    left: 561px;
	    width: 56px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.kilometro-25 {
	    top: 768px;
	    left: 381px;
	    width: 47px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.kuwait {
		top: 139px;
	    left: 716px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-arenera {
		top: 386px;
	    left: 640px;
	    width: 23px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-arenosa {
	    top: 376px;
	    left: 604px;
	    width: 22px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.la-carbonera {
	    top: 345px;
	    left: 748px;
	    width: 30px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.la-ceniza {
	    top: 200px;
	    left: 772px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-coraza {
	    top: 88px;
	    left: 627px;
	    width: 26px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-cucaracha {
	    top: 434px;
	    left: 633px;
	    width: 24px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-deseada {
	    top: 301px;
	    left: 587px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-doncella{
		top: 294px;
	    left: 798px;
	    width: 42px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-esperanza{
	    top: 253px;
	    left: 771px;
	    width: 37px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-esperanza-puerto-rico{
	    top: 880px;
	    left: 559px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-esperanza-turbo {
		top: 293px;
	    left: 552px;
	    width: 56px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-florida {
		top: 835px;
	    left: 408px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.la-fria {
	    top: 277px;
	    left: 763px;
	    width: 22px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-ilusion{
		top: 217px;
	    left: 736px;
	    width: 45px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-islita {
		top: 82px;
	    left: 680px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-leona {
	    top: 790px;
	    left: 467px;
	    width: 94px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-llana {
	    top: 298px;
	    left: 731px;
	    width: 23px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-naranja {
		top: 150px;
	    left: 639px;
	    width: 41px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-pedregosa {
	    top: 253px;
	    left: 653px;
	    width: 36px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-pina {
		top: 437px;
	    left: 551px;
	    width: 9px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-pita {
	    top: 87px;
	    left: 645px;
	    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-pitica {
		top: 80px;
	    left: 649px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-playona {
		top: 263px;
	    left: 585px;
	    width: 41px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-pola{
	    top: 394px;
	    left: 570px;
	    width: 18px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.la-pola-primavera {
	    top: 581px;
	    left: 368px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-primavera{
	    top: 542px;
	    left: 140px;
	    width: 252px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-pujarra {
	    top: 825px;
	    left: 435px;
	    width: 37px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-rosita{
	    top: 674px;
	    left: 378px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(39deg) brightness(100%) contrast(80%);
	}
	.las-babillas {
		top: 767px;
	    left: 316px;
	    width: 83px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.las-canas{
		top: 291px;
	    left: 600px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.las-flores{
	    top: 350px;
	    left: 800px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.las-garzas{
		top: 316px;
	    left: 538px;
	    width: 41px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.las-mantecas {
	    top: 317px;
	    left: 815px;
	    width: 30px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.las-mercedes {
	    top: 184px;
	    left: 595px;
	    width: 42px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.las-monas {
		top: 342px;
	    left: 768px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.las-pavas {
	    top: 299px;
	    left: 766px;
	    width: 40px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-te {
	    top: 703px;
	    left: 329px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}	
	.la-teca {
		top: 450px;
	    left: 519px;
	    width: 36px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.la-tempestad{
	    top: 682px;
	    left: 429px;
	    width: 22px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(2938deg) brightness(100%) contrast(80%);
	}
	.la-trampa{
	    top: 303px;
	    left: 619px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-union {
	    top: 243px;
	    left: 761px;
	    width: 35px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-union-2 {
	    top: 876px;
	    left: 493px;
	    width: 35px;
	    filter: invert(98%) sepia(9%) saturate(106%) hue-rotate(274deg) brightness(70%) contrast(40%);
	}	
	.leon-abajo {
	    top: 601px;
	    left: 418px;
	    width: 31px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.leoncito {
	    top: 288px;
	    left: 232px;
	    width: 200px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.limon-medio{
		top: 330px;
	    left: 656px;
	    width: 32px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.lomas-aisladas{
	    top: 747px;
	    left: 293px;
	    width: 39px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.los-cuarenta {
	    top: 364px;
	    left: 584px;
	    width: 22px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.los-enamorados {
		top: 173px;
	    left: 751px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.los-indios{
	    top: 215px;
	    left: 564px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.los-manaties {
	    top: 529px;
	    left: 381px;
	    width: 56px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.los-mangos-medios {
	    top: 642px;
	    left: 398px;
	    width: 42px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.los-moncholos {
	    top: 61px;
	    left: 742px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.lucio{
	    top: 205px;
	    left: 688px;
	    width: 41px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.mankendal {
	    top: 123px;
	    left: 804px;
	    width: 54px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.manuel-cuello {
	    top: 245px;
	    left: 563px;
	    width: 51px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.mata-de-platano-arriba {
		top: 95px;
	    left: 715px;
	    width: 31px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.matagorda {
	    top: 75px;
	    left: 815px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.mil-pesares{
	    top: 913px;
	    left: 514px;
	    width: 46px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.monomacho {
	    top: 702px;
	    left: 416px;
	    width: 29px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.mono-macho {
	    top: 223px;
	    left: 687px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.monte-verde-1 {
	    top: 377px;
	    left: 555px;
	    width: 27px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.monte-verde-2{
	    top: 368px;
	    left: 525px;
	    width: 37px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.nueva-antioquia {
	    top: 382px;
	    left: 733px;
	    width: 7px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.nueva-colombia {
	    top: 136px;
	    left: 761px;
	    width: 29px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.nueva-colonia {
	    top: 484px;
	    left: 534px;
	    width: 13px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.nueva-esperanza {
		top: 403px;
	    left: 561px;
	    width: 13px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.nueva-estrella{
		top: 961px;
	    left: 521px;
	    width: 62px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.nueva-granada {
	    top: 135px;
	    left: 722px;
	    width: 45px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.nueva-tulapa {
	    top: 141px;
	    left: 618px;
	    width: 29px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.nueva-union{
	    top: 455px;
	    left: 511px;
	    width: 54px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.nuevo-oriente{
		top: 939px;
	    left: 540px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.oviedo {
		top: 431px;
	    left: 682px;
	    width: 45px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.palo-de-agua {
		top: 923px;
	    left: 579px;
	    width: 28px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.palos-blancos{
	    top: 481px;
	    left: 562px;
   		width: 67px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.pa-que-mas {
	    top: 353px;
	    left: 598px;
	    width: 70px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2deg) brightness(88%) contrast(119%);
	}
	.paraiso-tulapa {
	    top: 61px;
	    left: 640px;
	    width: 24px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2deg) brightness(88%) contrast(119%);
	}
	.piedrecitas {
	    top: 275px;
	    left: 509px;
	    width: 60px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.playa-larga{
	    top: 392px;
	    left: 723px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(776%) hue-rotate(938deg) brightness(100%) contrast(80%);
	}
	.pueblo-bello {
	    top: 283px;
	    left: 766px;
	    width: 50px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.pueblo-galleta {
	    top: 375px;
	    left: 650px;
	    width: 35px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.pueblo-regado {
	    top: 830px;
	    left: 320px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.puerto-cesar {
	    top: 386px;
	    left: 511px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.puertorico {
	    top: 917px;
	    left: 584px;
	    width: 16px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.puerto-rico {
		top: 585px;
	    left: 140px;
	    width: 7px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.punto-rojo {
		top: 487px;
	    left: 518px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.puya-abajo {
	    top: 59px;
	    left: 769px;
	    width: 65px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.puya-medio {
	    top: 2px;
	    left: 809px;
	    width: 21px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.puyita {
	    top: 11px;
	    left: 818px;
    	width: 41px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.rancheria-nuevo-oriente {
	    top: 864px;
	    left: 517px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.real-cocuelo {
	    top: 712px;
	    left: 383px;
	    width: 44px;
	    filter: invert(48%) sepia(9%) saturate(876%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.rio-grande {
	    top: 490px;
	    left: 626px;
	    width: 10px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.rio-turbo {
	    top: 235px;
	    left: 610px;
	    width: 81px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}
	.turbo {
	    top: 416px;
	    left: 523px;
	    width: 14px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}		
	.salsipuedes {
		top: 900px;
	    left: 472px;
	    width: 39px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.san-andres-de-tulapa {
	    top: 200px;
	    left: 668px;
	    width: 27px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.san-felipe{
	    top: 292px;
	    left: 590px;
	    width: 41px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-jose-de-mulatos {
	    top: 56px;
	    left: 762px;
	    width: 12px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.san-marcanda {
	    top: 419px;
	    left: 535px;
	    width: 12px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.san-pablo {
		top: 183px;
	    left: 625px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.santa-barbara-abajo {
	    top: 223px;
	    left: 632px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.santa-barbara-arriba {
		top: 223px;
	    left: 657px;
	    width: 35px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.santa-fe-de-la-islita {
	    top: 60px;
	    left: 652px;
	    width: 34px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.santa-fe-de-los-mangos {
		top: 674px;
	    left: 413px;
	    width: 27px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.santa-ines {
	    top: 356px;
	    left: 541px;
	    width: 22px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.santa-rosa {
	    top: 324px;
	    left: 778px;
	    width: 46px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.santiago-de-uraba{
	    top: 114px;
	    left: 817px;
	    width: 4px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.san-vicente-del-congo {
		top: 196px;
	    left: 788px;
	    width: 14px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.semana-santa {
	    top: 32px;
	    left: 690px;
	    width: 50px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.siete-de-agosto {
	    top: 118px;
	    left: 724px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.siete-vueltas {
	    top: 230px;
	    left: 538px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.sinai{
	    top: 172px;
	    left: 716px;
	    width: 41px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.suriqui {
	    top: 489px;
	    left: 396px;
	    width: 125px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.tie {
	    top: 171px;
	    left: 501px;
	    width: 48px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.tio-gil {
	    top: 396px;
	    left: 582px;
	    width: 54px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.tio-lopez-alto {
		top: 303px;
	    left: 702px;
	    width: 38px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.tio-lopez-medio {
	    top: 327px;
	    left: 681px;
	    width: 40px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.toribio-alto{
		top: 283px;
	    left: 733px;
	    width: 44px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.toribio-medio {
	    top: 261px;
	    left: 738px;
	    width: 35px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.toya {
	    top: 148px;
	    left: 595px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.tumarado {
	    top: 748px;
	    left: 428px;
	    width: 46px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.tumaradocito{
		top: 874px;
	    left: 444px;
	    width: 32px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.tuntun-abajo {
	    top: 35px;
	    left: 660px;
	    width: 26px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.tuntun-arriba{
	    top: 42px;
	    left: 678px;
	    width: 26px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.villa-maria{
	    top: 298px;
	    left: 538px;
	    width: 32px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.villa-maria-arriba {
	    top: 303px;
	    left: 525px;
	    width: 19px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.villa-rosa {
	    top: 930px;
	    left: 567px;
	    width: 53px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.zabaletas {
	    top: 429px;
	    left: 720px;
	    width: 23px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.zona-de-expansion-urbana {
	    top: 323px;
	    left: 534px;
	    width: 17px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 322px;
	    left: 514px;
	    width: 27px;
	    z-index: 9999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
