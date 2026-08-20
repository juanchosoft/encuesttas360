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
	.campo-alegre{
	    top: 498px;
	    left: 384px;
	    width: 101px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cedeno-abajo {
	    top: 541px;
	    left: 524px;
	    width: 38px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cedeno-alto {
		top: 593px;
	    left: 419px;
	    width: 131px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-hacha {
	    top: 278px;
	    left: 458px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-libano{
		top: 232px;
	    left: 514px;
	    width: 134px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.corozal {
		top: 406px;
	    left: 492px;
	    width: 159px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.el-encanto {
	    top: 379px;
	    left: 480px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-rayo {
	    top: 367px;
	    left: 532px;
	    width: 94px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-tacon {
	    top: 325px;
	    left: 188px;
	    width: 178px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-tambor{
	    top: 262px;
	    left: 478px;
	    width: 91px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.guayabal {
		top: 538px;
	    left: 544px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-alacena {
	    top: 203px;
	    left: 406px;
	    width: 119px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-argentina {
	    top: 497px;
	    left: 464px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.la-betania {
		top: 528px;
	    left: 328px;
	    width: 190px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(2171deg) brightness(100%) contrast(80%);
	}
	.la-florida{
	    top: 550px;
	    left: 493px;
	    width: 48px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-juventud {
	    top: 148px;
	    left: 411px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-liborina {
	    top: 531px;
	    left: 425px;
	    width: 69px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-matilde {
		top: 480px;
	    left: 447px;
	    width: 71px;
	}
	.la-mesa {
	    top: 342px;
	    left: 472px;
	    width: 36px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-mirla {
		top: 508px;
	    left: 572px;
	    width: 34px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-oculta {
	    top: 5px;
	    left: 470px;
	    width: 241px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-pastora {
	    top: 534px;
	    left: 582px;
	    width: 43px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.manzanares {
		top: 632px;
	    left: 453px;
	    width: 117px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.nudillales {
	    top: 513px;
	    left: 516px;
	    width: 68px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.otrabanda {
	    top: 408px;
	    left: 414px;
	    width: 76px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-virgen {
	    top: 121px;
	    left: 486px;
	    width: 105px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.pescadero {
	    top: 301px;
	    left: 560px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.piedra-moler {
	    top: 502px;
	    left: 473px;
	    width: 37px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.rio-claro{
	    top: 440px;
	    left: 474px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.rio-frio {
	    top: 213px;
	    left: 319px;
	    width: 122px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.san-antonio {
	    top: 335px;
	    left: 317px;
	    width: 127px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.san-isidro {
	    top: 312px;
	    left: 486px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.san-luis {
	    top: 297px;
	    left: 415px;
	    width: 79px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(33deg) brightness(118%) contrast(119%);
	}
	.san-nicolas {
		top: 167px;
	    left: 466px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1058deg) brightness(100%) contrast(80%);
	}
	.san-pedro {
	    top: 462px;
	    left: 500px;
	    width: 65px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.santa-teresa {
	    top: 174px;
	    left: 496px;
	    width: 88px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.travesias {
	    top: 645px;
	    left: 558px;
	    width: 32px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 403px;
		left: 461px;
	    width: 32px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
