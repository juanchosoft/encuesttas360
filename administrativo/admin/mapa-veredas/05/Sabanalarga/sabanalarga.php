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
	.el-clavel{
	    top: 528px;
	    left: 468px;
	    width: 51px;
	}
	.el-encanto {
		top: 480px;
	    left: 466px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-junco {
		top: 665px;
	    left: 260px;
	    width: 153px;
		filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-socorro {
	    top: 408px;
	    left: 548px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-tambo{
	    top: 432px;
	    left: 470px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.el-oro {
	    top: 332px;
	    left: 549px;
	    width: 60px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(54deg) brightness(118%) contrast(119%);
	}
	.el-madero {
	    top: 721px;
	    left: 386px;
	    width: 71px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.el-placer {
	    top: 670px;
	    left: 438px;
	    width: 151px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.filo-de-los-perez {
	    top: 345px;
	    left: 595px;
	    width: 48px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-aurora {
	    top: 53px;
	    left: 504px;
	    width: 99px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-ceja {
		top: 699px;
	    left: 385px;
	    width: 59px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.la-ermita{
	    top: 667px;
	    left: 385px;
	    width: 52px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-loma {
	    top: 222px;
	    left: 545px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-meseta {
	    top: 260px;
	    left: 446px;
	    width: 111px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-pedrona {
	    top: 747px;
	    left: 400px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.la-travesia {
	    top: 677px;
	    left: 427px;
	    width: 34px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.llano-de-oro{
	    top: 277px;
	    left: 586px;
	    width: 38px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-encuentros {
		top: 393px;
	    left: 444px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.los-tendidos {
	    top: 350px;
	    left: 495px;
	    width: 84px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.machado {
	    top: 662px;
	    left: 373px;
	    width: 18px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.macanal{
	    top: 155px;
	    left: 549px;
	    width: 64px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);	
	}
	.mal-paso {
	    top: 618px;
	    left: 407px;
	    width: 118px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.membrillal {
	    top: 342px;
	    left: 331px;
	    width: 178px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.niquia {
	    top: 466px;
	    left: 489px;
	    width: 112px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.orobajo {
	    top: 3px;
	    left: 427px;
	    width: 160px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.remartin {
	    top: 170px;
	    left: 378px;
	    width: 136px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.san-cristobal {
	    top: 539px;
	    left: 273px;
	    width: 207px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.nohava {
	    top: 212px;
	    left: 469px;
	    width: 93px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.san-pedro {
	    top: 578px;
	    left: 418px;
	    width: 166px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.santa-maria {
	    top: 176px;
	    left: 582px;
	    width: 61px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.tesorerito{
	    top: 542px;
	    left: 393px;
	    width: 122px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.tesorero {
	    top: 528px;
	    left: 475px;
	    width: 84px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
	    top: 564px;
	    left: 410px;
	    width: 23px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
