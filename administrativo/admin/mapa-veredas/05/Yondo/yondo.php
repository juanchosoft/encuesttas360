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
	    width: 1000px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.barbacoas{
	    top: 503px;
	    left: 272px;
	    width: 215px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.bellavista {
	    top: 467px;
	    left: 531px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.bocas-de-barbacoas {
	    top: 665px;
	    left: 348px;
	    width: 123px;
	    filter: invert(48%) sepia(19%) saturate(176%) hue-rotate(401deg) brightness(118%) contrast(119%);
	}
	.cano-blanco {
		top: 364px;
	    left: 575px;
	    width: 111px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.bocas-de-san-francisco {
		top: 303px;
	    left: 403px;
	    width: 104px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.bocas-de-san-juan {
	    top: 202px;
	    left: 556px;
	    width: 107px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.cano-bonito {
		top: 315px;
	    left: 632px;
	    width: 44px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.cano-don-juan {
	    top: 281px;
	    left: 514px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.bellavista {
	    top: 84px;
	    left: 690px;
	    width: 42px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.cano-huila{
	    top: 467px;
	    left: 580px;
	    width: 75px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.cano-las-cruces {
	    top: 355px;
	    left: 653px;
	    width: 28px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.cano-negro {
	    top: 253px;
	    left: 663px;
	    width: 90px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.cienaga-chiquita {
	    top: 484px;
	    left: 398px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.cienaga-de-sardinata {
	    top: 393px;
	    left: 530px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.cuatro-bocas{
	    top: 72px;
	    left: 656px;
	    width: 51px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.cano-bodegas{
	    top: 401px;
	    left: 438px;
	    width: 63px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}	
	.el-amparo {
	    top: 391px;
	    left: 356px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-bagre {
	    top: 145px;
	    left: 597px;
	    width: 61px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-campo-cimitarra {
	    top: 114px;
	    left: 626px;
	    width: 56px;
	}
	.el-cedro {
	    top: 61px;
	    left: 698px;
	    width: 34px;
	    filter: invert(48%) sepia(19%) saturate(476%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.el-descanso {
	    top: 200px;
	    left: 642px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-dique {
		top: 290px;
	    left: 800px;
	    width: 45px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-raya{
		top: 295px;
	    left: 584px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-terminal {
		top: 574px;
	    left: 201px;
	    width: 41px;
	    z-index: 999;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-totumo {
	    top: 116px;
	    left: 655px;
	    width: 108px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.ite {
	    top: 440px;
	    left: 188px;
	    width: 128px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(3104deg) brightness(118%) contrast(119%);
	}
	.el-puerto {
		top: 247px;
	    left: 807px;
	    width: 24px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.jabonal {
	    top: 283px;
	    left: 452px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-porvenir {
		top: 465px;
	    left: 456px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.kilometro-cinco{
	    top: 338px;
	    left: 737px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-cabana {
	    top: 225px;
	    left: 767px;
	    width: 48px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-cascajera {
	    top: 320px;
	    left: 799px;
	    width: 28px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-concha {
	    top: 177px;
	    left: 548px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.la-condor-x10 {
	    top: 267px;
	    left: 749px;
	    width: 52px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-congoja {
	    top: 357px;
	    left: 287px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.la-felicidad {
	    top: 112px;
	    left: 720px;
	    width: 68px;
	    filter: invert(133%) sepia(59%) saturate(473%) hue-rotate(6104deg) brightness(119%) contrast(119%);
	}
	.la-ganadera {
	    top: 507px;
	    left: 454px;
	    width: 138px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.laguna-del-miedo {
	    top: 279px;
	    left: 734px;
	    width: 45px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-orquidea {
	    top: 370px;
	    left: 510px;
	    width: 59px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.la-paz {
	    top: 464px;
	    left: 497px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-represa{
		top: 347px;
	    left: 768px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-rinconada {
	    top: 5px;
	    left: 720px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-rompida-1 {
	    top: 134px;
	    left: 691px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-rompida-2 {
		top: 198px;
	    left: 720px;
	    width: 82px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.las-lomas {
	    top: 222px;
	    left: 601px;
	    width: 111px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%);
	}
	.la-soledad {
	    top: 373px;
	    left: 330px;
	    width: 42px;
	    z-index: 999;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.la-union{
		top: 427px;
	    left: 473px;
	    width: 70px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.no-te-pases {
	    top: 175px;
	    left: 476px;
	    width: 123px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.penas-blancas {
	    top: 377px;
	    left: 684px;
	    width: 73px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.puerto-nuevo {
	    top: 179px;
	    left: 725px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.rompederos {
	    top: 496px;
	    left: 551px;
	    width: 65px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-bartolo {
	    top: 584px;
	    left: 156px;
	    width: 182px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(344deg) brightness(100%) contrast(80%);
	}
	.san-francisco-alto {
	    top: 365px;
	    left: 404px;
	    width: 62px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.san-luis-beltran{
	    top: 392px;
	    left: 731px;
	    width: 34px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(410deg) brightness(118%) contrast(119%);
	}
	.san-miguel-del-tigre {
	    top: 227px;
	    left: 733px;
	    width: 42px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-clara {
	    top: 691px;
	    left: 245px;
	    width: 126px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-soledad {
	    top: 381px;
	    left: 477px;
	    width: 53px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.sardinata {
	    top: 393px;
	    left: 601px;
	    width: 102px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.vietnam {
	    top: 346px;
	    left: 429px;
	    width: 97px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.yondo-nuevo {
	    top: 375px;
	    left: 753px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.zona-urbana-vereda-el-dique {
	    top: 268px;
	    left: 788px;
	    width: 51px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 336px;
	    left: 795px;
	    width: 16px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
