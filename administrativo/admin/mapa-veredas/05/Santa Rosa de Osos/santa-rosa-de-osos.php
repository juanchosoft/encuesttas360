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
	    width: 950px;
	    height: 1000px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.aguaditas{
	    top: 736px;
	    left: 631px;
	    width: 51px;
	}
	.aragon {
		top: 212px;
	    left: 83px;
	    width: 203px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.barrancas {
	    top: 662px;
	    left: 862px;
	    width: 51px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.caruquia {
		top: 557px;
	    left: 610px;
	    width: 121px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chilimaco{
		top: 686px;
	    left: 891px;
	    width: 55px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.cucurucho {
	    top: 546px;
	    left: 227px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.dos-quebradas {
		top: 669px;
	    left: 634px;
	    width: 66px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-ahiton {
		top: 620px;
	    left: 655px;
	    width: 132px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-barro {
		top: 691px;
	    left: 725px;
	    width: 53px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-boton{
		top: 427px;
	    left: 102px;
	    width: 93px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-caney {
		top: 693px;
	    left: 724px;
	    width: 212px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-chagualo {
		top: 522px;
	    left: 263px;
	    width: 40px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-chamizo {
	    top: 305px;
	    left: 145px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-chaquiro {
	    top: 294px;
	    left: 259px;
	    width: 164px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-congo{
		top: 595px;
	    left: 724px;
	    width: 62px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(704deg) brightness(118%) contrast(119%) ;
	}
	.el-guayabo {
	    top: 471px;
	    left: 241px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.el-hato {
		top: 544px;
	    left: 442px;
	    width: 119px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-llano {
	    top: 659px;
	    left: 826px;
	    width: 50px;
	}
	.el-quince{
	    top: 121px;
	    left: 4px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-roble {
	    top: 464px;
	    left: 280px;
	    width: 174px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-sauce {
		top: 722px;
	    left: 685px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-topacio{
	    top: 315px;
	    left: 75px;
	    width: 104px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-vergel{
	    top: 466px;
	    left: 445px;
	    width: 137px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.embalse {
	    top: 793px;
	    left: 317px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.guanacas {
	    top: 416px;
	    left: 541px;
	    width: 194px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.guanaquitas {
		top: 529px;
	    left: 672px;
	    width: 86px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.hoyo-rico {
	    top: 634px;
	    left: 432px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-cabana {
		top: 665px;
	    left: 443px;
	    width: 90px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-cejita{
		top: 677px;
	    left: 498px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-francesa {
		top: 305px;
	    left: 414px;
	    width: 88px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-lomita {
	    top: 700px;
	    left: 850px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-mina {
	    top: 491px;
	    left: 547px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%) ;
	}
	.la-munoz{
	    top: 669px;
	    left: 270px;
	    width: 84px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-pava-salamina {
		top: 679px;
	    left: 741px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-planta {
		top: 588px;
	    left: 281px;
	    width: 73px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-ruiz {
		top: 325px;
	    left: 192px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.las-animas {
		top: 730px;
	    left: 538px;
	    width: 95px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.las-cruces {
		top: 534px;
	    left: 377px;
	    width: 120px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.los-salados {
		top: 743px;
	    left: 355px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.malambo {
	    top: 601px;
	    left: 410px;
	    width: 74px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.mina-vieja{
	    top: 331px;
	    left: 461px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.montanita {
	    top: 752px;
	    left: 693px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(329deg) brightness(100%) contrast(80%) ;
	}
	.montefrio {
		top: 615px;
	    left: 823px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.mortinal {
		top: 684px;
	    left: 597px;
	    width: 50px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.orobajo-riogrande {
		top: 749px;
	    left: 288px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.orobajo-santa-ines {
	    top: 709px;
	    left: 342px;
	    width: 39px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.palestina{
	    top: 570px;
	    left: 711px;
	    width: 57px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.playa-larga {
	    top: 617px;
	    left: 482px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.pontezuela {
	    top: 684px;
	    left: 375px;
	    width: 130px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.quebrada-del-medio {
	    top: 465px;
	    left: 172px;
	    width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(52deg) brightness(100%) contrast(80%) ;
	}
	.quebradona {
		top: 460px;
	    left: 70px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.quitasol {
		top: 356px;
	    left: 400px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1834deg) brightness(100%) contrast(80%) ;
	}
	.rio-grande {
	    top: 765px;
	    left: 456px;
	    width: 106px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.rio-negrito {
	    top: 626px;
	    left: 584px;
	    width: 46px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(85deg) brightness(88%) contrast(119%) ;
	}
	.sabanazo{
		top: 398px;
	    left: 180px;
	    width: 106px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.samaria {
		top: 191px;
	    left: 32px;
	    width: 175px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.san-antonio {
	    top: 687px;
	    left: 695px;
	    width: 54px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(83deg) brightness(118%) contrast(119%) ;
	}
	.san-bernardo {
	    top: 126px;
	    left: 7px;
	    width: 178px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.san-felipe {
	    top: 636px;
	    left: 555px;
	    width: 29px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.san-francisco{
	    top: 657px;
	    left: 527px;
	    width: 50px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.san-isidro {
	    top: 740px;
	    left: 635px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.san-isidro-parte-baja {
	    top: 775px;
	    left: 641px;
	    width: 65px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.san-jose {
	    top: 596px;
	    left: 342px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.san-jose-de-la-ahumada {
	    top: 568px;
	    left: 520px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.san-pablo{
		top: 598px;
	    left: 753px;
	    width: 90px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.san-ramon {
	    top: 639px;
	    left: 564px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(304deg) brightness(100%) contrast(80%) ;
	}
	.santa-ana {
	    top: 657px;
	    left: 317px;
	    width: 86px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.santa-barbara {
	    top: 526px;
	    left: 567px;
	    width: 101px;
	}
	.santa-gertrudis{
		top: 598px;
	    left: 504px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.santana {
		top: 724px;
	    left: 590px;
	    width: 60px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.vallecitos{
	    top: 335px;
	    left: 261px;
	    width: 184px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.ventiadero{
		top: 691px;
	    left: 568px;
	    width: 38px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.verbenal{
		top: 784px;
	    left: 376px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.yarumito {
	    top: 407px;
	    left: 487px;
	    width: 77px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 579px;
	    left: 362px;
	    width: 55px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
