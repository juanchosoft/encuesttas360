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
	    	<!-- <?php include "../mapa-veredas/mapa_veredas.php" ?>	     -->
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
	}
	.agua-bonita{
	    top: 441px;
	    left: 468px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.agua-linda {
	    top: 184px;
	    left: 449px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.arenosas {
		top: 457px;
	    left: 172px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-cerro {
	    top: 112px;
	    left: 465px;
	    width: 119px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.betulia {
		top: 491px;
	    left: 175px;
	    width: 48px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.calderas {
	    top: 293px;
	    left: 3px;
	    width: 125px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.canafisto {
	    top: 354px;
	    left: 667px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.canaveral {
	    top: 328px;
	    left: 298px;
	    width: 93px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.capotal {
	    top: 534px;
	    left: 154px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.cocalito {
	    top: 519px;
	    left: 336px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.dinamarca {
	    top: 425px;
	    left: 212px;
	    width: 54px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.dos-quebradas {
	    top: 440px;
	    left: 158px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-cardal {
		top: 206px;
	    left: 442px;
	    width: 27px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-charcon{
		top: 229px;
	    left: 465px;
	    width: 19px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-popo{
	    top: 378px;
	    left: 304px;
	    width: 107px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.el-choco {
		top: 480px;
	    left: 131px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-contento {
	    top: 603px;
	    left: 427px;
	    width: 117px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-quebradon{
	    top: 436px;
	    left: 536px;
	    width: 98px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-tigre{
	    top: 191px;
	    left: 652px;
	    width: 40px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}	
	.guadualito{
	    top: 278px;
	    left: 829px;
	    width: 69px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(104deg) brightness(118%) contrast(119%);
	}		
	.el-silencio {
	    top: 478px;
	    left: 349px;
	    width: 38px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-tabor {
	    top: 331px;
	    left: 239px;
	    width: 70px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.embalse-punchina {
	    top: 262px;
	    left: 494px;
	    width: 145px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.fronteritas {
	    top: 182px;
	    left: 300px;
	    width: 159px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.hortona{
	    top: 536px;
	    left: 145px;
	    width: 30px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.juanes {
	    top: 331px;
	    left: 653px;
	    width: 22px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.juanos {
	    top: 312px;
	    left: 603px;
	    width: 133px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.juan-xxiii {
	    top: 239px;
	    left: 412px;
	    width: 122px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-aguada {
		top: 213px;
	    left: 410px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-cabana {
		top: 402px;
	    left: 307px;
	    width: 92px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cascada {
		top: 235px;
	    left: 681px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-cienaga {
	    top: 264px;
	    left: 767px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-esperanza {
		top: 292px;
	    left: 345px;
	    width: 105px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-florida {
	    top: 256px;
	    left: 248px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.la-garrucha {
	    top: 345px;
	    left: 721px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.las-flores {
	    top: 435px;
	    left: 596px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.la-holanda {
	    top: 207px;
	    left: 476px;
	    width: 74px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-hondita {
	    top: 400px;
	    left: 94px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-ilusion {
	    top: 83px;
	    left: 665px;
	    width: 127px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-leona {
	    top: 514px;
	    left: 290px;
	    width: 49px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.la-luz {
		top: 184px;
	    left: 674px;
	    width: 51px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.la-maria{
	    top: 402px;
	    left: 344px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.la-rapida {
	    top: 230px;
	    left: 151px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.las-camelias {
		top: 255px;
	    left: 214px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-tupiada {
	    top: 477px;
	    left: 189px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.la-villa {
	    top: 417px;
	    left: 272px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.las-frias {
	    top: 61px;
	    left: 609px;
	    width: 87px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.las-palmas {
	    top: 581px;
	    left: 652px;
	    width: 40px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}		
	.llanadas{
	    top: 115px;
	    left: 634px;
	    width: 86px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.miraflores {
	    top: 611px;
	    left: 598px;
	    width: 93px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.mirandita {
	    top: 444px;
	    left: 370px;
	    width: 126px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.norcasia {
	    top: 440px;
	    left: 657px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.pabellon {
	    top: 492px;
	    left: 151px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.palmichal {
	    top: 340px;
	    left: 118px;
    	width: 71px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.paraguas {
	    top: 236px;
	    left: 625px;
	    width: 105px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.bellavista {
	    top: 451px;
	    left: 228px;
	    width: 58px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.patio-bonito {
		top: 277px;
	    left: 459px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.penoles {
	    top: 409px;
	    left: 251px;
	    width: 59px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.penol-grande{
	    top: 332px;
	    left: 547px;
	    width: 140px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(2109deg) brightness(100%) contrast(80%);
	}
	.pio-xii {
	    top: 212px;
	    left: 274px;
	    width: 153px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.pocitos {
	    top: 284px;
	    left: 722px;
	    width: 67px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.portugal {
	    top: 188px;
	    left: 717px;
	    width: 127px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.prado {
	    top: 556px;
	    left: 566px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.puerto-garza-narices{
	    top: 329px;
	    left: 798px;
	    width: 65px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.puerto-rico {
		top: 350px;
	    left: 153px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.samana {
	    top: 484px;
	    left: 621px;
	    width: 83px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.san-blas {
	    top: 301px;
	    left: 406px;
	    width: 96px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.san-jose {
	    top: 542px;
	    left: 450px;
	    width: 184px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-miguel-parte-alta {
	    top: 543px;
	    left: 332px;
	    width: 106px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.san-miguel-parte-baja {
		top: 621px;
	    left: 399px;
	    width: 45px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}	
	.santa-barbara {
	    top: 369px;
	    left: 682px;
	    width: 83px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.santa-elena {
	    top: 512px;
	    left: 399px;
	    width: 97px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.santa-ines {
	    top: 485px;
	    left: 199px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.santa-isabel {
	    top: 88px;
	    left: 536px;
	    width: 125px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.santa-rita{
	    top: 480px;
	    left: 295px;
	    width: 67px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.sardina-grande {
		top: 433px;
	    left: 357px;
	    width: 65px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.sardinita {
	    top: 439px;
	    left: 302px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(976%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.tinajas {
	    top: 186px;
	    left: 533px;
	    width: 127px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.vallejuelo {
	    top: 338px;
	    left: 207px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(1339deg) brightness(100%) contrast(80%);
	}
	.vergel {
		top: 516px;
	    left: 132px;
	    width: 47px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2106deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
	    top: 393px;
	    left: 269px;
	    width: 39px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(8%) contrast(119%);
	}
</style>
