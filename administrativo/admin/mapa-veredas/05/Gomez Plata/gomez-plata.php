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
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.angosturita {
	    top: 529px;
	    left: 264px;
	    width: 137px;
	}
	.balsas {
		top: 409px;
	    left: 399px;
	    width: 102px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.caldera {
		top: 576px;
	    left: 410px;
	    width: 111px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.canaveral {
		top: 120px;
	    left: 339px;
	    width: 172px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.chilimaco {
	    top: 607px;
	    left: 368px;
	    width: 100px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.caracolal {
		top: 371px;
	    left: 405px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.cestillal {
		top: 301px;
	    left: 346px;
	    width: 50px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.claritas {
	    top: 434px;
	    left: 179px;
	    width: 189px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-arbolito {
	    top: 343px;
	    left: 337px;
	    width: 106px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-brasil{
	    top: 382px;
	    left: 619px;
	    width: 90px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.el-cerro {
	    top: 328px;
	    left: 426px;
	    width: 135px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.el-guayabo {
	    top: 264px;
	    left: 284px;
	    width: 96px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-indio {
	    top: 329px;
	    left: 530px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-oso {
		top: 83px;
	    left: 348px;
	    width: 132px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.el-tablon{
		top: 346px;
	    left: 270px;
	    width: 152px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.garzon {
	    top: 101px;
	    left: 549px;
	    width: 168px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.juntas {
	    top: 4px;
	    left: 438px;
	    width: 93px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-acequia {
	    top: 424px;
	    left: 476px;
	    width: 57px;
	}
	.la-arenera {
	    top: 184px;
	    left: 286px;
	    width: 94px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.la-bonita {
		top: 430px;
	    left: 411px;
	    width: 267px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-clara {
	    top: 686px;
	    left: 452px;
	    width: 126px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-estrella {
		top: 644px;
	    left: 444px;
	    width: 63px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-hondura{
		top: 247px;
	    left: 363px;
	    width: 119px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-primavera {
	    top: 299px;
	    left: 352px;
	    width: 191px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-region {
	    top: 521px;
	    left: 380px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.quebradona {
	    top: 205px;
	    left: 433px;
	    width: 219px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.vega-botero {
		top: 281px;
	    left: 538px;
	    width: 183px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.san-matias {
		top: 64px;
	    left: 430px;
	    width: 190px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.santa-elena{
		top: 511px;
	    left: 418px;
	    width: 200px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.trapichera {
	    top: 401px;
	    left: 581px;
	    width: 96px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 454px;
	    left: 364px;
	    width: 51px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
