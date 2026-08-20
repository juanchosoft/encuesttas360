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
	    width: 900px;
	    height: 900px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.las-animas{
	    top: 544px;
	    left: 365px;
	    width: 127px;
	}
	.arenas-blancas {
		top: 518px;
	    left: 587px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.baldio {
		top: 3px;
	    left: 521px;
	    width: 211px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.cruces {
	    top: 348px;
	    left: 486px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.boqueron {
	    top: 666px;
	    left: 461px;
	    width: 63px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.cestillal {
	    top: 467px;
	    left: 436px;
	    width: 82px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-canal {
	    top: 164px;
	    left: 441px;
	    width: 142px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-castillo {
	    top: 453px;
	    left: 627px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-dorado {
	    top: 462px;
	    left: 544px;
	    width: 42px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-encanto{
	    top: 755px;
	    left: 192px;
	    width: 245px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-guaico {
	    top: 630px;
	    left: 173px;
	    width: 146px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.naranjal {
		top: 207px;
	    left: 412px;
	    width: 74px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-retiro {
	    top: 762px;
	    left: 347px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-rio {
		top: 628px;
	    left: 362px;
	    width: 118px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-taparo{
	    top: 644px;
	    left: 299px;
	    width: 79px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-tigrillo {
	    top: 369px;
	    left: 576px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.guamoco {
		top: 676px;
	    left: 602px;
	    width: 64px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.guayabito {
	    top: 714px;
	    left: 265px;
	    width: 91px;
	}
	.el-jardin {
	    top: 338px;
	    left: 534px;
	    width: 68px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-aldea {
	    top: 708px;
	    left: 566px;
	    width: 72px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-areiza {
		top: 271px;
	    left: 631px;
	    width: 103px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-clara {
	    top: 478px;
	    left: 535px;
	    width: 64px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-blanquita-la-esperanza{
		top: 711px;
	    left: 349px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-gardenia {
	    top: 505px;
	    left: 495px;
	    width: 57px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-guyana {
	    top: 704px;
	    left: 169px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-gurria {
	    top: 569px;
	    left: 357px;
	    width: 71px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-manguita {
		top: 368px;
	    left: 362px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-maria {
		top: 683px;
	    left: 483px;
	    width: 107px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-picardia {
	    top: 463px;
	    left: 584px;
	    width: 63px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%) ;
	}
	.la-sanadora {
	    top: 542px;
	    left: 462px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-vetilla {
	    top: 179px;
	    left: 546px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-vibora {
	    top: 605px;
	    left: 247px;
	    width: 100px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%) ;
	}
	.los-mangos{
	    top: 475px;
	    left: 228px;
	    width: 170px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%) ;
	}
	.los-toros {
	    top: 93px;
	    left: 461px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.manzanillo {
	    top: 614px;
	    left: 422px;
	    width: 92px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.maria-teresa {
		top: 608px;
	    left: 187px;
	    width: 69px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.mondragon {
	    top: 400px;
	    left: 491px;
	    width: 103px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.monos {
	    top: 572px;
	    left: 582px;
	    width: 102px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.montebello {
		top: 636px;
	    left: 521px;
	    width: 102px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.naranjitos{
	    top: 743px;
	    left: 498px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.las-pavas {
	    top: 490px;
	    left: 650px;
	    width: 81px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.pinto-limon {
	    top: 384px;
	    left: 433px;
	    width: 33px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.pocoro {
	    top: 496px;
	    left: 482px;
	    width: 87px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.portachuelo {
	    top: 758px;
	    left: 546px;
	    width: 102px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%) ;
	}
	.risaralda {
		top: 724px;
	    left: 432px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.romazon{
	    top: 408px;
	    left: 440px;
	    width: 70px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.salazar {
		top: 491px;
	    left: 367px;
	    width: 98px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.san-miguel {
	    top: 419px;
	    left: 661px;
	    width: 72px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.tinita {
		top: 260px;
	    left: 448px;
	    width: 143px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.tintacita {
	    top: 309px;
	    left: 384px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
		top: 601px;
	    left: 293px;
	    width: 86px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
