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
	}
	.agua-bonita{
	    top: 463px;
	    left: 393px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.agualinda {
		top: 636px;
	    left: 479px;
	    width: 93px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-bonito {
	    top: 486px;
	    left: 464px;
	    width: 40px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-coco {
	    top: 261px;
	    left: 435px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-de-la-virgen {
	    top: 307px;
	    left: 371px;
	    width: 70px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.balcones{
	    top: 360px;
	    left: 606px;
	    width: 60px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.buenos-aires {
	    top: 116px;
	    left: 368px;
	    width: 103px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.campo-alegre {
	    top: 209px;
	    left: 486px;
	    width: 46px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.caracoli {
	    top: 609px;
	    left: 519px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.cebadero {
	    top: 486px;
	    left: 513px;
	    width: 97px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.cruces {
	    top: 42px;
	    left: 184px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.cuchilla-del-rejo {
		top: 642px;
	    left: 639px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-choco {
		top: 146px;
	    left: 421px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-cipres {
	    top: 316px;
	    left: 294px;
	    width: 51px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-cocuyo{
	    top: 563px;
	    left: 522px;
	    width: 41px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-molino{
	    top: 185px;
	    left: 439px;
	    width: 73px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.el-entablado {
	    top: 559px;
	    left: 546px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-estio {
		top: 653px;
	    left: 468px;
	    width: 43px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-higueron {
	    top: 488px;
	    left: 484px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-palmar{
	    top: 413px;
	    left: 604px;
	    width: 36px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-porvenir {
	    top: 583px;
	    left: 632px;
	    width: 61px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-recreo {
		top: 343px;
	    left: 355px;
	    width: 36px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.el-retiro {
	    top: 546px;
	    left: 508px;
	    width: 35px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-roblal {
	    top: 535px;
	    left: 486px;
	    width: 28px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.el-salado {
	    top: 306px;
	    left: 361px;
	    width: 76px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-sinai {
		top: 400px;
	    left: 443px;
	    width: 44px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-suspiro{
	    top: 623px;
	    left: 692px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-tesoro {
	    top: 248px;
	    left: 273px;
	    width: 85px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-viadal {
	    top: 66px;
	    left: 344px;
	    width: 115px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-viao {
	    top: 86px;
	    left: 124px;
	    width: 197px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.guayabal {
		top: 325px;
	    left: 535px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-aurora {
	    top: 278px;
	    left: 511px;
	    width: 55px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-chonta {
	    top: 135px;
	    left: 317px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-chorrera {
	    top: 188px;
	    left: 303px;
	    width: 51px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-cima {
	    top: 387px;
	    left: 475px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-florida {
		top: 569px;
	    left: 565px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.la-granja {
	    top: 320px;
	    left: 639px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-primavera {
	    top: 374px;
	    left: 389px;
	    width: 45px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.la-quiebra{
		top: 353px;
	    left: 571px;
	    width: 55px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.la-inmaculada {
	    top: 253px;
	    left: 545px;
	    width: 71px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-milagrosa {
	    top: 199px;
	    left: 238px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-paila {
	    top: 417px;
	    left: 527px;
	    width: 52px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-solita {
	    top: 431px;
	    left: 420px;
	    width: 35px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.la-palma {
	    top: 407px;
	    left: 406px;
	    width: 18px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.la-pena{
		top: 178px;
	    left: 394px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.la-pinuela {
	    top: 300px;
	    left: 486px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-placeta {
	    top: 214px;
	    left: 295px;
	    width: 50px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-secreta {
	    top: 669px;
	    left: 526px;
	    width: 83px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.las-mercedes {
	    top: 249px;
	    left: 348px;
	    width: 41px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.la-solita{
		top: 430px;
	    left: 420px;
	    width: 35px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-antonio {
		top: 204px;
	    left: 427px;
	    width: 39px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.las-playas {
	    top: 132px;
	    left: 439px;
	    width: 35px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.la-tolda {
	    top: 366px;
	    left: 531px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-trinidad {
	    top: 4px;
	    left: 248px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-vega {
	    top: 409px;
	    left: 421px;
	    width: 16px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.la-veta {
	    top: 335px;
	    left: 678px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.san-miguel {
	    top: 307px;
	    left: 332px;
	    width: 48px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-cedros {
		top: 242px;
	    left: 380px;
	    width: 83px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.los-limones {
	    top: 396px;
	    left: 475px;
	    width: 76px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.los-mangos{
	    top: 229px;
	    left: 507px;
	    width: 73px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.los-potreros {
		top: 58px;
	    left: 289px;
	    width: 109px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.majagual {
	    top: 376px;
	    left: 459px;
 	    width: 65px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.mazotes {
	    top: 214px;
	    left: 338px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.mediacuesta {
	    top: 130px;
	    left: 301px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.montanita{
		top: 218px;
	    left: 389px;
	    width: 47px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.morritos {
	    top: 426px;
	    left: 480px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.villa-hermosa {
	    top: 369px;
	    left: 354px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-vicente {
	    top: 242px;
	    left: 273px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.santo-domingo {
		top: 314px;
	    left: 419px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-rita {
	    top: 700px;
	    left: 451px;
	    width: 95px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
	    top: 353px;
	    left: 423px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.santa-barbara {
	    top: 217px;
	    left: 458px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.san-martin{
	    top: 642px;
	    left: 592px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-lorenzo {
		top: 257px;
	    left: 542px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 252px;
	    left: 361px;
	    width: 44px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.palmirita {
	    top: 334px;
	    left: 379px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.pailania {
	    top: 402px;
	    left: 562px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.zona-urbana{
	    top: 195px;
	    left: 375px;
	    width: 19px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
