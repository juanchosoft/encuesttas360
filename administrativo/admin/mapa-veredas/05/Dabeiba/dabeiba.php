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
	.agualinda{
	    top: 409px;
	    left: 838px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-bonito {
		top: 357px;
	    left: 367px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.amparrado {
	    top: 176px;
	    left: 108px;
	    width: 204px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.chachafrutal {
	    top: 335px;
	    left: 760px;
	    width: 37px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.antado {
	    top: 328px;
	    left: 495px;
	    width: 53px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.baldios-de-la-nacion {
	    top: 149px;
	    left: 562px;
	    width: 434px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.barrancas {
	    top: 317px;
	    left: 851px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.barrancon {
	    top: 382px;
	    left: 610px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.barrancon-antado {
	    top: 288px;
	    left: 527px;
    	width: 25px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.betania {
	    top: 456px;
	    left: 788px;
	    width: 19px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.canaverales {
	    top: 304px;
	    left: 630px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.canaverales-antado {
	    top: 647px;
	    left: 125px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.carra {
	    top: 351px;
	    left: 473px;
	    width: 48px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.chamuscados{
	    top: 183px;
	    left: 811px;
	    width: 73px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.chimurro-nendo{
		top: 370px;
	    left: 353px;
	    width: 35px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.chever {
	    top: 140px;
	    left: 302px;
	    width: 295px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.chichirido {
	    top: 280px;
	    left: 321px;
	    width: 77px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.chimurro {
	    top: 344px;
	    left: 250px;
	    width: 200px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.chino-de-playones{
		top: 297px;
	    left: 617px;
	    width: 42px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.chontaduro {
		top: 526px;
	    left: 3px;
	    width: 257px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.choromando {
		top: 338px;
	    left: 413px;
	    width: 120px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.choromando-alto-medio {
	    top: 443px;
	    left: 431px;
	    width: 71px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.chupadero {
	    top: 415px;
	    left: 772px;
	    width: 35px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.churrascal {
	    top: 390px;
	    left: 708px;
	    width: 66px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.chuscal-de-murri {
		top: 658px;
	    left: 397px;
	    width: 127px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.chuscal-tuguridocito {
	    top: 586px;
	    left: 337px;
	    width: 132px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.corcobado {
		top: 288px;
	    left: 917px;
	    width: 76px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.cuchillon {
	    top: 217px;
	    left: 752px;
	    width: 177px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.culantrillales {
		top: 294px;
	    left: 651px;
	    width: 54px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.dabeiba-viejo {
	    top: 471px;
	    left: 518px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-aguila {
		top: 239px;
	    left: 629px;
	    width: 85px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-balso {
	    top: 280px;
	    left: 753px;
	    width: 157px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.el-boton {
	    top: 494px;
	    left: 533px;
	    width: 70px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-caliche {
	    top: 423px;
	    left: 837px;
	    width: 33px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-caliche-1 {
		top: 390px;
	    left: 580px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.el-cocal {
	    top: 382px;
	    left: 597px;
	    width: 26px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-mohan {
	    top: 508px;
	    left: 491px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.el-encierro {
		top: 356px;
	    left: 686px;
	    width: 31px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-espinazo {
	    top: 487px;
	    left: 548px;
	    width: 19px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-grande {
	    top: 354px;
	    left: 736px;
	    width: 49px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.el-jardin {
	    top: 294px;
	    left: 787px;
	    width: 127px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.el-jilguero {
		top: 401px;
	    left: 666px;
	    width: 20px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.el-jordan{
	    top: 443px;
	    left: 817px;
	    width: 26px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.el-jordan-bajo {
	    top: 456px;
	    left: 793px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.el-mango {
	    top: 400px;
	    left: 771px;
	    width: 25px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.el-pital {
	    top: 408px;
	    left: 487px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.el-plan {
	    top: 377px;
	    left: 889px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.el-paramo {
		top: 317px;
	    left: 694px;
	    width: 32px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-retiro{
	    top: 302px;
	    left: 547px;
	    width: 51px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-terco {
	    top: 423px;
	    left: 794px;
	    width: 49px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.el-tigre {
	    top: 381px;
	    left: 848px;
	    width: 49px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.el-toro {
		top: 303px;
	    left: 487px;
	    width: 30px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.filo-de-la-cruz {
	    top: 376px;
	    left: 708px;
    	width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.guadualito {
		top: 439px;
	    left: 767px;
	    width: 27px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.guineales {
		top: 344px;
	    left: 389px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.anta {
		top: 405px;
	    left: 574px;
	    width: 41px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.jenaturado {
	    top: 763px;
	    left: 219px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.julio-chiquito {
	    top: 592px;
	    left: 312px;
	    width: 64px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-argelia{
		top: 233px;
	    left: 768px;
	    width: 60px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(2109deg) brightness(100%) contrast(80%);
	}
	.la-armenia {
	    top: 447px;
	    left: 823px;
	    width: 97px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-balsita {
	    top: 311px;
	    left: 734px;
	    width: 47px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-chiquita {
	    top: 360px;
	    left: 759px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.la-clara {
	    top: 358px;
	    left: 510px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-danta{
	    top: 337px;
	    left: 716px;
	    width: 49px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-estrella {
	    top: 343px;
	    left: 527px;
	    width: 21px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-falda {
	    top: 357px;
	    left: 649px;
	    width: 47px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-fortuna {
	    top: 411px;
	    left: 862px;
	    width: 74px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-mesa {
		top: 235px;
	    left: 579px;
	    width: 49px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-montana {
	    top: 367px;
	    left: 696px;
	    width: 31px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-montanita {
	    top: 253px;
	    left: 581px;
	    width: 57px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-paloma {
	    top: 368px;
	    left: 767px;
	    width: 87px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-pia {
		top: 413px;
	    left: 558px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-soledad {
	    top: 195px;
	    left: 428px;
	    width: 86px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.llano-de-cruces{
		top: 376px;
	    left: 626px;
	    width: 43px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.llano-gordo {
		top: 270px;
	    left: 493px;
	    width: 33px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.llano-grande {
	    top: 241px;
	    left: 546px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(976%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.llanon {
	    top: 189px;
	    left: 877px;
	    width: 48px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.los-cocos {
	    top: 354px;
	    left: 712px;
	    width: 28px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(1339deg) brightness(100%) contrast(80%);
	}
	.los-naranjos {
		top: 323px;
	    left: 544px;
	    width: 46px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2106deg) brightness(88%) contrast(119%);
	}
	.monos-la-horqueta {
	    top: 432px;
	    left: 558px;
	    width: 100px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.nudillales {
	    top: 300px;
	    left: 408px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.palmichales{
	    top: 321px;
	    left: 461px;
	    width: 41px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.palonegro {
	    top: 385px;
	    left: 792px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.pegado {
	    top: 256px;
	    left: 283px;
	    width: 103px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.playones {
	    top: 321px;
	    left: 587px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.pueblecito {
	    top: 419px;
	    left: 796px;
	    width: 25px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.quiparado {
	    top: 239px;
	    left: 366px;
	    width: 126px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.quiparadosito {
	    top: 230px;
	    left: 440px;
	    width: 81px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.resguardo-indigena-pegado{
	    top: 504px;
	    left: 170px;
	    width: 156px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(2938deg) brightness(100%) contrast(80%);
	}
	.san-agustin {
	    top: 401px;
	    left: 716px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-ignacio {
		top: 376px;
	    left: 679px;
	    width: 26px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.san-jose-de-urama {
	    top: 386px;
	    left: 719px;
	    width: 5px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.santa-teresa {
	    top: 124px;
	    left: 293px;
	    width: 87px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.taparales {
	    top: 188px;
	    left: 287px;
	    width: 89px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.tascon{
	    top: 295px;
	    left: 256px;
	    width: 67px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.tasido {
		top: 192px;
	    left: 568px;
	    width: 87px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.tocunal {
		top: 233px;
	    left: 695px;
	    width: 101px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.tugurido {
		top: 505px;
	    left: 324px;
	    width: 196px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.vallesi {
	    top: 311px;
	    left: 273px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
		top: 406px;
	    left: 530px;
	    width: 26px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(8%) contrast(119%);
	}
</style>
