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
	    height: 1000px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.alta-vista{
	    top: 508px;
	    left: 126px;
	    width: 24px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-de-guayaquil {
	    top: 377px;
	    left: 400px;
	    width: 19px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-del-rayo {
	    top: 458px;
	    left: 80px;
	    width: 26px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.alto-de-sabanas{
		top: 458px;
	    left: 39px;
	    width: 24px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);		
	}
	.campo-alegre {
		top: 364px;
	    left: 590px;
	    width: 107px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.arenillal {
	    top: 613px;
	    left: 74px;
	    width: 29px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.argentina {
		top: 444px;
	    left: 16px;
	    width: 30px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.argentina-magallo {
	    top: 452px;
	    left: 39px;
	    width: 51px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.aures-cartagena {
	    top: 280px;
	    left: 165px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.aures-la-morelia {
		top: 331px;
	    left: 154px;
	    width: 46px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.boqueron {
	    top: 477px;
	    left: 70px;
	    width: 34px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.brasilal {
	    top: 292px;
	    left: 392px;
	    width: 42px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.butantan {
	    top: 450px;
	    left: 866px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.campamento {
	    top: 310px;
	    left: 394px;
	    width: 88px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.caunzal{
	    top: 370px;
	    left: 401px;
	    width: 83px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-brasil{
	    top: 489px;
	    left: 10px;
	    width: 26px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.caunzal-los-medios {
	    top: 591px;
	    left: 54px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.chaverras {
	    top: 448px;
	    left: 187px;
	    width: 63px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-bosque {
		top: 508px;
	    left: 114px;
	    width: 27px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-cedro{
	    top: 249px;
	    left: 344px;
	    width: 34px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-coco {
	    top: 315px;
	    left: 344px;
	    width: 58px;
	    filter: invert(133%) sepia(59%) saturate(473%) hue-rotate(164deg) brightness(119%) contrast(119%);
	}
	.el-limon {
	    top: 468px;
	    left: 9px;
	    width: 18px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.el-llano {
	    top: 546px;
	    left: 68px;
	    width: 33px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-llano-canaveral {
		top: 543px;
	    left: 68px;
	    width: 60px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.el-popal {
	    top: 282px;
	    left: 223px;
	    width: 91px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-rodeo {
	    top: 607px;
	    left: 98px;
	    width: 49px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-salado{
	    top: 308px;
	    left: 296px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-salto {
	    top: 405px;
	    left: 62px;
	    width: 45px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.guamal {
	    top: 490px;
	    left: 92px;
	    width: 66px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.guayabal-rio-arriba {
	    top: 490px;
	    left: 20px;
	    width: 49px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.habana-arriba {
	    top: 431px;
	    left: 11px;
	    width: 21px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.hidalgo {
	    top: 469px;
	    left: 20px;
	    width: 31px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.jerusalen {
	    top: 199px;
	    left: 808px;
	    width: 11px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-capilla {
	    top: 371px;
	    left: 267px;
	    width: 90px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-cienaga {
	    top: 330px;
	    left: 376px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-danta {
	    top: 185px;
	    left: 785px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.la-falda {
	    top: 474px;
	    left: 97px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-mesa {
	    top: 337px;
	    left: 786px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.la-flor-el-tesoro {
	    top: 299px;
	    left: 677px;
	    width: 46px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-francia {
	    top: 521px;
	    left: 92px;
	    width: 37px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-giralda {
	    top: 532px;
	    left: 66px;
	    width: 40px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-hermosa {
	    top: 249px;
	    left: 712px;
	    width: 106px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.la-montanita {
	    top: 357px;
	    left: 332px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}	
	.la-honda {
	    top: 395px;
	    left: 162px;
	    width: 45px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.la-hondita{
	    top: 583px;
	    left: 121px;
	    width: 35px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.la-linda {
	    top: 202px;
	    left: 851px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-loma {
	    top: 447px;
	    left: 5px;
	    width: 19px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.la-palmera {
	    top: 411px;
	    left: 233px;
	    width: 37px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.la-paloma {
	    top: 457px;
	    left: 240px;
 	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.la-paz-san-francisco{
	    top: 381px;
	    left: 681px;
	    width: 46px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.la-quiebra-de-san-pablo {
	    top: 508px;
	    left: 241px;
	    width: 45px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.las-cruces {
	    top: 635px;
	    left: 107px;
	    width: 68px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.la-soledad {
	    top: 351px;
	    left: 355px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-torre {
		top: 259px;
	    left: 295px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.limones {
	    top: 462px;
	    left: 828px;
	    width: 61px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.llanadas-abajo {
	    top: 385px;
	    left: 120px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.llanadas-arriba {
	    top: 437px;
	    left: 92px;
	    width: 63px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-medios {
	    top: 598px;
	    left: 65px;
	    width: 34px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.los-planes {
	    top: 535px;
	    left: 95px;
	    width: 44px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.los-potreros{
	    top: 523px;
	    left: 62px;
	    width: 44px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.magallo {
	    top: 447px;
	    left: 45px;
	    width: 37px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.magallo-abajo {
	    top: 432px;
	    left: 30px;
	    width: 17px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.manzanares-abajo {
	    top: 365px;
	    left: 184px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.manzanares-arriba {
	    top: 374px;
	    left: 226px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.manzanares-centro{
	    top: 354px;
	    left: 189px;
	    width: 73px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.media-cuesta-de-san-jose {
		top: 535px;
	    left: 124px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.mulato-alto {
		top: 377px;
	    left: 702px;
	    width: 89px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.mulato-bajo {
		top: 387px;
	    left: 824px;
	    width: 41px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.murringo {
		top: 401px;
	    left: 248px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.naranjal-abajo {
	    top: 419px;
	    left: 52px;
	    width: 23px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.naranjal-arriba {
	    top: 423px;
	    left: 36px;
	    width: 44px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.nori {
	    top: 342px;
	    left: 187px;
	    width: 58px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.palestina {
	    top: 243px;
	    left: 429px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.parcelas {
	    top: 224px;
	    left: 862px;
	    width: 114px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.perrillo{
	    top: 632px;
	    left: 123px;
	    width: 122px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.piedras-blancas {
		top: 309px;
	    left: 847px;
	    width: 98px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.plancitos {
		top: 268px;
	    left: 368px;
	    width: 34px;
	    filter: invert(48%) sepia(89%) saturate(976%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.reserva-forestal {
	    top: 415px;
	    left: 517px;
	    width: 77px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.rio-arriba {
	    top: 437px;
	    left: 137px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(1339deg) brightness(100%) contrast(80%);
	}
	.roblal-abajo {
	    top: 477px;
	    left: 28px;
	    width: 31px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2106deg) brightness(88%) contrast(119%);
	}
	.roblal-abajo-chirimoyo {
	    top: 498px;
	    left: 61px;
	    width: 34px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.roblal-arriba {
	    top: 474px;
	    left: 53px;
	    width: 23px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.roblalito-a{
		top: 481px;
	    left: 159px;
	    width: 68px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.roblalito-b {
	    top: 492px;
	    left: 204px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-antonio {
		top: 321px;
	    left: 831px;
	    width: 63px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.san-francisco {
	    top: 402px;
	    left: 170px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-jeronimo {
		top: 383px;
	    left: 333px;
	    width: 74px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.san-jose-las-cruces {
	    top: 604px;
	    left: 118px;
	    width: 68px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.san-miguel {
	    top: 239px;
	    left: 918px;
	    width: 79px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-rafael{
	    top: 385px;
	    left: 755px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(2938deg) brightness(100%) contrast(80%);
	}
	.santa-ana {
		top: 333px;
	    left: 762px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.santa-clara {
	    top: 394px;
	    left: 95px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.santa-marta {
	    top: 319px;
	    left: 456px;
	    width: 76px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.santa-rosa {
	    top: 194px;
	    left: 389px;
	    width: 135px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.santa-rosa-la-danta {
		top: 353px;
	    left: 691px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.sirguita{
	    top: 514px;
	    left: 110px;
	    width: 26px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.sirigua-abajo {
		top: 554px;
	    left: 138px;
	    width: 37px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.sirigua-arriba {
	    top: 544px;
	    left: 152px;
	    width: 77px;
	    filter: invert(50%) sepia(79%) saturate(311%) hue-rotate(846deg) brightness(78%) contrast(19%);
	}
	.surrumbal {
	    top: 302px;
	    left: 299px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.tasajo {
	    top: 375px;
	    left: 140px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.ventiaderos {
	    top: 358px;
	    left: 145px;
	    width: 43px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.yarumal {
	    top: 494px;
	    left: 143px;
	    width: 60px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.zona-urbana {
		top: 468px;
	    left: 148px;
	    width: 21px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
</style>
