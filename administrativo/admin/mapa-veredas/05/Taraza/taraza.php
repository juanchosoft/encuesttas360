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
	}
	.agualinda{
	    top: 41px;
	    left: 267px;
	    width: 32px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);		
	}
	.anaparci {
		top: 393px;
	    left: 370px;
	    width: 94px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.blanco {
	    top: 283px;
	    left: 183px;
	    width: 26px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.canon-de-iglesias {
	    top: 364px;
	    left: 264px;
	    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(356deg) brightness(118%) contrast(119%);
	}
	.area-sin-levantar {
	    top: 301px;
	    left: 147px;
	    width: 238px;
	    filter: invert(65%) sepia(69%) saturate(876%) hue-rotate(1976deg) brightness(118%) contrast(119%);
	}
	.barro-blanco {
	    top: 336px;
	    left: 571px;
	    width: 82px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.chuchui {
	    top: 256px;
	    left: 336px;
	    width: 120px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.curumana {
	    top: 359px;
	    left: 332px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.doradas-altas {
	    top: 638px;
	    left: 597px;
	    width: 73px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.doradas-bajas{
		top: 535px;
	    left: 615px;
	    width: 173px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-doce {
	    top: 442px;
	    left: 503px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-guaimaro {
	    top: 207px;
	    left: 229px;
	    width: 192px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-porvenir {
	    top: 243px;
	    left: 126px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-rayo {
		top: 294px;
	    left: 350px;
	    width: 188px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-cabana{
		top: 199px;
	    left: 168px;
	    width: 53px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-caucana {
	    top: 166px;
	    left: 175px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-cidra {
		top: 194px;
	    left: 112px;
	    width: 68px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-esperanza {
		top: 51px;
	    left: 210px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-primavera {
	    top: 59px;
	    left: 195px;
	    width: 151px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-linda {
	    top: 214px;
	    left: 192px;
	    width: 65px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}	
	.la-union {
	    top: 306px;
	    left: 299px;
	    width: 70px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.las-acacias {
	    top: 300px;
	    left: 356px;
	    width: 62px;
	    filter: invert(65%) sepia(89%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.neri {
	    top: 572px;
	    left: 596px;
	    width: 42px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1206deg) brightness(88%) contrast(119%);
	}
	.pecora {
	    top: 169px;
	    left: 248px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.pecoralia {
		top: 45px;
	    left: 296px;
	    width: 73px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.matecana {
	    top: 157px;
	    left: 350px;
	    width: 144px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.piedra-brava {
	    top: 219px;
	    left: 167px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.popales {
	    top: 304px;
	    left: 224px;
	    width: 96px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.potrero-largo{
		top: 455px;
	    left: 639px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.puqui {
		top: 464px;
	    left: 363px;
	    width: 185px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.puri {
	    top: 429px;
	    left: 617px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.quinteron {
	    top: 113px;
	    left: 173px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.rancho-viejo {
		top: 161px;
    	left: 285px;
    	width: 89px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}	
	.resguardo-indigena-jaidusabi {
	    top: 387px;
	    left: 239px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.rincon-santo {
	    top: 4px;
	    left: 294px;
	    width: 74px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.san-antonio{
	    top: 373px;
	    left: 526px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.santa-clara {
		top: 9px;
    	left: 209px;
    	width: 119px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.tahami {
	    top: 632px;
	    left: 594px;
	    width: 128px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.tamaco {
	    top: 315px;
	    left: 516px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2129deg) brightness(118%) contrast(200%);
	}
	.tenerife {
	    top: 242px;
	    left: 430px;
	    width: 75px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.tesorito {
		top: 499px;
	    left: 596px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.tornoban {
		top: 99px;
    	left: 334px;
    	width: 26px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.zona-urbana {
	    top: 248px;
	    left: 417px;
	    width: 16px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
