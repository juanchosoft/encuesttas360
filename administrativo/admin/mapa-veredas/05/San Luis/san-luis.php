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
	.altavista{
	    top: 484px;
	    left: 544px;
	    width: 193px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.buenos-aires {
	    top: 130px;
	    left: 61px;
	    width: 63px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cuba {
	    top: 327px;
	    left: 250px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-trique {
		top: 302px;
	    left: 271px;
	    width: 110px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-cruce {
	    top: 367px;
	    left: 328px;
	    width: 64px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.el-jordan{
	    top: 512px;
	    left: 266px;
	    width: 86px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.el-olivo {
		top: 358px;
	    left: 200px;
	    width: 71px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-popal {
	    top: 297px;
	    left: 105px;
	    width: 79px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-palacio {
		top: 361px;
	    left: 423px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-pescado {
	    top: 459px;
	    left: 49px;
	    width: 115px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-prodigio {
	    top: 220px;
	    left: 689px;
	    width: 144px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-silencio {
	    top: 395px;
	    left: 76px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-socorro {
	    top: 237px;
	    left: 220px;
	    width: 89px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-arabia{
	    top: 303px;
	    left: 340px;
	    width: 131px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-gaviota{
	    top: 186px;
	    left: 290px;
	    width: 115px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.la-arauca {
	    top: 549px;
	    left: 449px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-habana{
	    top: 419px;
	    left: 288px;
	    width: 111px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-porvenir {
	    top: 148px;
	    left: 36px;
	    width: 90px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-independencia {
	    top: 310px;
	    left: 573px;
	    width: 169px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-cumbre {
	    top: 381px;
	    left: 513px;
	    width: 142px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-estrella {
	    top: 120px;
	    left: 123px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.la-garrucha {
	    top: 396px;
	    left: 368px;
	    width: 61px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-josefina {
	    top: 483px;
	    left: 428px;
	    width: 85px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-merced{
	    top: 139px;
	    left: 113px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-cristalina {
    	top: 278px;
	    left: 538px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-mesa {
	    top: 483px;
	    left: 340px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.las-confusas {
	    top: 382px;
	    left: 665px;
	    width: 222px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-margaritas {
	    top: 269px;
	    left: 576px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-aurora {
	    top: 145px;
	    left: 180px;
	    width: 46px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-linda {
	    top: 424px;
	    left: 213px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.la-tebaida {
	    top: 346px;
	    left: 136px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.los-planes {
	    top: 154px;
	    left: 285px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-palmera {
	    top: 505px;
	    left: 143px;
	    width: 133px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.manizales {
	    top: 196px;
	    left: 123px;
	    width: 87px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-francisco {
		top: 266px;
	    left: 148px;
	    width: 130px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1058deg) brightness(100%) contrast(80%);
	}	
	.san-pablo{
	    top: 357px;
	    left: 10px;
	    width: 132px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.minarrica {
	    top: 225px;
	    left: 33px;
	    width: 114px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(162deg) brightness(119%) contrast(119%);
	}
	.los-medios {
	    top: 268px;
	    left: 783px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.monteloro {
	    top: 458px;
	    left: 505px;
	    width: 124px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.la-palma {
	    top: 313px;
	    left: 465px;
	    width: 95px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.salambrina{
	    top: 574px;
	    left: 229px;
	    width: 148px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.san-antonio {
	    top: 111px;
	    left: 189px;
	    width: 117px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.villanueva {
		top: 101px;
	    left: 66px;
	    width: 54px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.montenegro{
	    top: 455px;
	    left: 119px;
	    width: 78px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.viboral {
	    top: 129px;
	    left: 319px;
	    width: 83px;
		filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-miguel {
	    top: 307px;
	    left: 332px;
	    width: 48px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.santa-rita {
	    top: 254px;
	    left: 382px;
	    width: 82px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
	    top: 353px;
	    left: 423px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.santa-barbara {
	    top: 301px;
	    left: 4px;
	    width: 109px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.sopetran {
	    top: 191px;
	    left: 218px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.santa-rosa {
	    top: 463px;
	    left: 184px;
	    width: 114px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.zona-urbana{
	    top: 327px;
	    left: 247px;
	    width: 27px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
