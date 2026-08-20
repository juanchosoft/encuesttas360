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
	    height: 750px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.alto-de-cenizas{
	    top: 459px;
	    left: 641px;
	    width: 36px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-san-agustin {
	    top: 294px;
	    left: 561px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.bajo-ingles {
	    top: 484px;
	    left: 567px;
	    width: 24px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.buenavista {
	    top: 533px;
	    left: 613px;
	    width: 19px;
	    z-index: 9999;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.camelia-alta {
	    top: 399px;
	    left: 723px;
	    width: 46px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.camelia-baja {
	    top: 407px;
	    left: 754px;
	    width: 59px;
	    z-index: 99;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.candelaria-alta {
	    top: 421px;
	    left: 693px;
	    width: 41px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.candelaria-baja {
	    top: 429px;
	    left: 683px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.canoas {
	    top: 539px;
	    left: 566px;
	    width: 40px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.chapinero{
	    top: 513px;
	    left: 645px;
	    width: 10px;
	    z-index: 9998
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.chontaduro {
	    top: 418px;
	    left: 625px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.concordia {
	    top: 321px;
	    left: 736px;
	    width: 30px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.conguital {
	    top: 197px;
	    left: 683px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.cortaderal {
	    top: 539px;
	    left: 629px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.el-amparo{
	    top: 507px;
	    left: 558px;
	    width: 35px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-aro {
	    top: 410px;
	    left: 894px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-barranco {
	    top: 344px;
	    left: 626px;
	    width: 71px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.el-capote {
	    top: 373px;
	    left: 635px;
	    width: 64px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-castillo {
	    top: 268px;
	    left: 578px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.el-cedral {
	    top: 490px;
	    left: 547px;
	    width: 25px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-ceibo {
	    top: 411px;
	    left: 806px;
	    width: 44px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-chirimbolo{
	    top: 422px;
	    left: 596px;
	    width: 30px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-chocho {
		top: 423px;
	    left: 575px;
	    width: 25px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-chuscal {
		top: 379px;
	    left: 599px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-guadual {
	    top: 432px;
	    left: 662px;
	    width: 31px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-herrero {
	    top: 391px;
	    left: 641px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-indio {
	    top: 315px;
	    left: 758px;
	    width: 45px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-naranjo {
	    top: 520px;
	    left: 629px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.el-olivar {
	    top: 349px;
	    left: 694px;
	    width: 27px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-quindio {
	    top: 385px;
	    left: 534px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-recreo {
	    top: 354px;
	    left: 783px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.el-rio{
	    top: 479px;
	    left: 619px;
	    width: 69px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.el-socorro {
	    top: 86px;
	    left: 625px;
	    width: 255px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-tejar {
	    top: 369px;
	    left: 823px;
	    width: 32px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-tinto {
	    top: 403px;
	    left: 831px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-tinto-2 {
	    top: 560px;
	    left: 594px;
	    width: 31px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.el-zancudo {
	    top: 414px;
	    left: 694px;
	    width: 30px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.filadelfia {
	    top: 436px;
	    left: 878px;
	    width: 44px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.finlandia{
	    top: 409px;
	    left: 772px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.georgia {
	    top: 463px;
	    left: 579px;
	    width: 18px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.guacharaquero {
	    top: 549px;
	    left: 618px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-america {
		top: 344px;
	    left: 851px;
	    width: 105px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.la-cabana {
	    top: 378px;
	    left: 817px;
	    width: 11px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.la-ceiba {
	    top: 343px;
	    left: 719px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.la-cienaga{
		top: 267px;
	    left: 708px;
	    width: 66px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-cristalina {
		top: 370px;
	    left: 737px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.la-cueva {
	    top: 367px;
	    left: 811px;
	    width: 16px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.la-esperanza {
	    top: 257px;
	    left: 782px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-florida {
	    top: 573px;
	    left: 579px;
	    width: 46px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-francia {
	    top: 200px;
	    left: 783px;
	    width: 71px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.la-hermosa {
	    top: 322px;
	    left: 801px;
	    width: 30px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-honda {
	    top: 590px;
	    left: 619px;
	    width: 49px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-hundida {
	    top: 542px;
	    left: 598px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-maria {
	    top: 342px;
	    left: 821px;
	    width: 38px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-miranda{
	    top: 462px;
	    left: 544px;
	    width: 39px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-palizada {
	    top: 429px;
	    left: 722px;
	    width: 38px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-perla {
	    top: 244px;
	    left: 656px;
	    width: 73px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-prensa {
	    top: 338px;
	    left: 459px;
	    width: 108px;
	    z-index: 999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.la-rica {
	    top: 455px;
	    left: 863px;
	    width: 23px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.las-aguitas{
	    top: 523px;
	    left: 748px;
	    width: 74px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-aranas {
	    top: 424px;
	    left: 530px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.las-brisas {
	    top: 393px;
	    left: 697px;
	    width: 12px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.las-cuatro {
	    top: 463px;
	    left: 587px;
	    width: 27px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-soledad {
	    top: 255px;
	    left: 755px;
	    width: 38px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.los-chorritos {
	    top: 429px;
	    left: 738px;
	    width: 70px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.los-galgos {
	    top: 497px;
	    left: 672px;
	    width: 91px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.los-sauces {
	    top: 444px;
	    left: 555px;
	    width: 37px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.los-venados {
	    top: 388px;
	    left: 829px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.mandarino {
		top: 411px;
	    left: 675px;
	    width: 33px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.maniceros{
	    top: 304px;
	    left: 822px;
	    width: 49px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.manzanares {
		top: 365px;
	    left: 722px;
	    width: 45px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.media-falda {
	    top: 347px;
	    left: 765px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.monte-negro {
	    top: 237px;
	    left: 583px;
	    width: 51px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.organi {
		top: 407px;
	    left: 928px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.palmichal {
	    top: 371px;
	    left: 850px;
	    width: 50px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.palo-blanco {
	    top: 494px;
	    left: 611px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.parque-nacional {
	    top: 306px;
	    left: 5px;
	    width: 570px;
	    z-index: 0;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.pascuita{
	    top: 464px;
	    left: 752px;
	    width: 62px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.pena {
		top: 555px;
	    left: 542px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.quebrada-del-medio {
	    top: 433px;
	    left: 584px;
	    width: 32px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.quebradona {
	    top: 294px;
	    left: 770px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.quebradona-2 {
	    top: 377px;
	    left: 722px;
	    width: 42px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.quebradoncita {
	    top: 439px;
	    left: 606px;
	    width: 27px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.reventon {
	    top: 365px;
	    left: 693px;
	    width: 37px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-agustin-de-leones{
	    top: 143px;
	    left: 612px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(2938deg) brightness(100%) contrast(80%);
	}
	.san-isidro {
	    top: 455px;
	    left: 565px;
	    width: 29px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-luis {
	    top: 418px;
	    left: 841px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.san-luis-1 {
	    top: 428px;
	    left: 648px;
	    width: 24px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.san-luis-santa-ana {
	    top: 536px;
	    left: 530px;
	    width: 38px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.san-marcos {
	    top: 191px;
	    left: 830px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.santa-ana{
	    top: 443px;
	    left: 520px;
	    width: 42px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.santa-lucia {
		top: 355px;
	    left: 561px;
	    width: 56px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.singo {
	    top: 484px;
	    left: 588px;
	    width: 37px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.tinajas {
	    top: 447px;
	    left: 781px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.torrente {
		top: 448px;
	    left: 839px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.travesias {
	    top: 367px;
	    left: 694px;
	    width: 21px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.turco {
	    top: 499px;
	    left: 653px;
	    width: 25px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.villegas {
	    top: 388px;
	    left: 768px;
	    width: 35px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 524px;
	    left: 649px;
	    width: 8px;
	    z-index: 9999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
