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
	    background: url('admin/mapa-veredas/05/Segovia/img/base.png');
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
	.baldios-de-la-nacion {
		top: 68px;
	    left: 473px;
	    width: 421px;
	}
		.el-cenizo {
    top: 301px;
    left: 63px;
    width: 199px;
	}
	.campo-alegre {
top: 544px;
    left: 246px;
    width: 121px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.chorro-lindo {
    top: 485px;
    left: 199px;
    width: 36px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.cianurada {
	    top: 709px;
	    left: 357px;
	    width: 29px;
	    z-index: 998;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.el-tesoro {
    top: 280px;
    left: 298px;
    width: 43px;
    z-index: 	8;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.cuturu-abajo {
top: 388px;
    left: 184px;
    width: 64px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.cuturu-arriba {
top: 443px;
    left: 213px;
    width: 84px;
	}	
	.arenales {
    top: 302px;
    left: 424px;
    width: 146px;
	}	
		.el-carmen {
			top: 424px;
	    left: 632px;
	    width: 62px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-aporriado{
    top: 628px;
    left: 329px;
    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.bocas-de-chicamoque{
			top: 631px;
	    left: 364px;
	    width: 74px;
	}
	.el-pescado {
    top: 287px;
    left: 200px;
    width: 178px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}

	.fraguas {
     top: 383px;
    left: 61px;
    width: 101px;
	}
	.la-palma {
top: 221px;
    left: 431px;
    width: 108px;
	}
	.la-po{
top: 400px;
    left: 393px;
    width: 116px;
	}
	.laureles{
    top: 430px;
    left: 150px;
    width: 53px;
	}
	.marmajito{
		top: 713px;
	    left: 380px;
	    width: 12px;
	    z-index: 999;
	}
	.marmoles {
	    top: 333px;
	    left: 414px;
	    width: 80px;
	}
	.el-aguacate {
    top: 345px;
    left: 555px;
    width: 86px;
	}	
	.mata {
    top: 363px;
    left: 3px;
    width: 171px;
	}
	.monte-frio{
top: 258px;
    left: 382px;
    width: 104px;
	}
	.popales {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	}
		.el-hechal {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	}
	.puerto-calavera {
top: 552px;
    left: 150px;
    width: 90px;
	}
	.tamar-alto{
		    width: 60px;
    left: 731px;
    top: 253px;
	}
	.quebradona {
top: 414px;
    left: 302px;
    width: 119px;
	}
	.juan-tereso {
    width: 64px;
    top: 504px;
    left: 147px;
}
	.el-cristo {
    top: 362px;
    left: 111px;
    width: 94px;
	}	
	.san-jose-del-pescado{
    top: 183px;
    left: 492px;
    width: 87px;
	}
	.la-palma-n-1{
    top: 298px;
    left: 325px;
    width: 120px;
	}
	.san-miguelito {
	    top: 400px;
	    left: 327px;
	    width: 142px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.santa-isabel-de-amara {
top: 242px;
    left: 324px;
    width: 112px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}

	.la-manuela{
top: 356px;
    left: 599px;
    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.popales{
	    top: 547px;
	    left: 270px;
	    width: 98px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
    top: 674px;
    left: 310px;
    width: 42px;
	}
</style>
