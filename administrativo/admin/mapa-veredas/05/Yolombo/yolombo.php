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
	    width: 1000px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.aguabonita{
	    top: 311px;
	    left: 342px;
	    width: 88px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.aguabonita-2 {
	    top: 467px;
	    left: 531px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.altavista {
	    top: 510px;
	    left: 292px;
	    width: 31px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.barbasca {
	    top: 433px;
	    left: 250px;
	    width: 51px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.bareno{
	    top: 537px;
	    left: 65px;
	    width: 30px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.alto-del-potrero {
	    top: 477px;
	    left: 262px;
	    width: 37px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.alto-de-mendez {
	    top: 486px;
	    left: 299px;
	    width: 26px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.barro-blanco {
	    top: 365px;
	    left: 294px;
	    width: 52px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.belgica {
	    top: 405px;
	    left: 609px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.bellavista {
	    top: 435px;
	    left: 515px;
	    width: 41px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.bengala{
	    top: 486px;
	    left: 89px;
	    width: 78px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.brazuelos {
		top: 389px;
	    left: 368px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.buenos-aires {
		top: 401px;
	    left: 366px;
	    width: 32px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.cachumbal {
	    top: 244px;
	    left: 289px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.cimarrona {
	    top: 464px;
	    left: 551px;
	    width: 30px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.cuatro-esquinas{
	    top: 558px;
	    left: 396px;
	    width: 59px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.dona-ana {
	    top: 435px;
	    left: 573px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-bosque {
	    top: 515px;
	    left: 241px;
	    width: 62px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-cairo {
	    top: 469px;
	    left: 539px;
	    width: 39px;
	}
	.el-chuscal {
	    top: 512px;
	    left: 449px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(476%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.el-comino {
	    top: 303px;
	    left: 193px;
	    width: 78px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-homiguero {
	    top: 493px;
	    left: 39px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-iris {
	    top: 478px;
	    left: 482px;
	    width: 68px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
	    top: 227px;
	    left: 254px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-olivo {
	    top: 483px;
	    left: 444px;
	    width: 60px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-oso {
	    top: 402px;
	    left: 331px;
	    width: 33px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-palmar {
	    top: 530px;
	    left: 363px;
	    width: 44px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(3104deg) brightness(118%) contrast(119%);
	}
	.el-jardin {
	    top: 445px;
	    left: 452px;
	    width: 21px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-pichon {
	    top: 425px;
	    left: 64px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-porvenir {
		top: 476px;
	    left: 391px;
	    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-rosario{
	    top: 525px;
	    left: 299px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.el-rubi {
	    top: 529px;
	    left: 328px;
	    width: 50px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-rubi-la-floresta {
	    top: 443px;
	    left: 468px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-tapon {
		top: 371px;
	    left: 88px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.estacion-sofia{
	    top: 563px;
	    left: 253px;
	    width: 51px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.guacabe {
	    top: 239px;
	    left: 114px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.guacharacas {
		top: 581px;
	    left: 374px;
	    width: 182px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-abisinia {
	    top: 270px;
	    left: 247px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-argentina {
	    top: 398px;
	    left: 668px;
	    width: 97px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-cancana {
	    top: 225px;
	    left: 137px;
	    width: 122px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.la-ceiba {
	    top: 463px;
	    left: 492px;
	    width: 17px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-cruz{
	    top: 146px;
	    left: 239px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-cumbre {
	    top: 527px;
	    left: 5px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-esmeralda {
	    top: 477px;
	    left: 86px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-esmeralda-2 {
		top: 538px;
	    left: 410px;
	    width: 106px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.la-floresta {
	    top: 442px;
	    left: 511px;
	    width: 22px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%);
	}
	.la-gergona {
	    top: 373px;
	    left: 330px;
	    width: 42px;
	    z-index: 999;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.la-indiana{
		top: 365px;
	    left: 163px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.la-josefina {
	    top: 517px;
	    left: 125px;
	    width: 45px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.la-maria {
	    top: 501px;
	    left: 399px;
	    width: 32px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.la-marquesa {
	    top: 472px;
	    left: 215px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-pajita {
	    top: 470px;
	    left: 310px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-palmera {
	    top: 463px;
	    left: 458px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(344deg) brightness(100%) contrast(80%);
	}
	.la-reina{
	    top: 484px;
	    left: 340px;
	    width: 70px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.las-cabanas {
	    top: 317px;
	    left: 257px;
	    width: 43px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.las-frias {
	    top: 349px;
	    left: 226px;
    	width: 102px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.las-margaritas {
		top: 445px;
	    left: 288px;
	    width: 73px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-soledad {
	    top: 581px;
	    left: 437px;
	    width: 38px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.la-verduguita {
	    top: 325px;
	    left: 263px;
	    width: 94px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.los-aceites {
		top: 499px;
	    left: 308px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-andes {
	    top: 354px;
	    left: 732px;
	    width: 180px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.los-totumos {
	    top: 328px;
	    left: 887px;
	    width: 110px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.maracaibo{
	    top: 200px;
	    left: 317px;
	    width: 117px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.montebello {
	    top: 514px;
	    left: 417px;
	    width: 38px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.mulatos {
	    top: 413px;
	    left: 388px;
	    width: 73px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.pantanillo {
	    top: 410px;
	    left: 325px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.perico {
	    top: 427px;
	    left: 372px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.pocoro {
	    top: 436px;
	    left: 340px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1344deg) brightness(100%) contrast(80%);
	}
	.sabanitas{
		top: 471px;
	    left: 133px;
	    width: 132px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.san-agustin{
	    top: 211px;
	    left: 278px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-antonio {
	    top: 430px;
	    left: 538px;
	    width: 39px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-jacinto {
	    top: 558px;
	    left: 373px;
	    width: 31px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.santana {
	    top: 443px;
	    left: 616px;
	    width: 63px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.santo-tomas {
		top: 276px;
	    left: 391px;
	    width: 59px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.tenorio {
	    top: 462px;
	    left: 498px;
	    width: 27px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 500px;
	    left: 273px;
	    width: 27px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
