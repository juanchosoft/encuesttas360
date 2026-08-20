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
	.bella-sola{
	    top: 436px;
	    left: 153px;
	    width: 167px;
	}
	.bijagual {
	    top: 599px;
	    left: 309px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.caceri {
		top: 541px;
	    left: 68px;
	    width: 111px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-guamo {
		top: 489px;
	    left: 226px;
	    width: 127px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.cano-pescado {
	    top: 34px;
	    left: 94px;
	    width: 310px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.colorado {
	    top: 244px;
	    left: 199px;
	    width: 14px;
	    z-index: 9999;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2229deg) brightness(118%) contrast(200%) ;
	}
	.granada {
	    top: 165px;
	    left: 368px;
	    width: 85px;
        filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.hoyo-grande {
	    top: 230px;
	    left: 121px;
	    width: 273px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.la-arenosa {
		top: 605px;
	    left: 216px;
	    width: 114px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.la-concepcion{
	    top: 328px;
	    left: 100px;
	    width: 111px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-concha {
	    top: 369px;
	    left: 192px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-esperanza {
	    top: 239px;
	    left: 362px;
	    width: 129px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.las-flores {
	    top: 116px;
	    left: 387px;
	    width: 13px;
	    z-index: 9999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(257deg) brightness(100%) contrast(80%) ;
	}
	.la-trinidad {
	    top: 142px;
	    left: 427px;
	    width: 273px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-ye{
	    top: 339px;
	    left: 246px;
	    width: 101px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.londres {
	    top: 4px;
	    left: 205px;
	    width: 145px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.madre-de-dios {
	    top: 86px;
	    left: 439px;
	    width: 76px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.puerto-gaitan {
	    top: 704px;
	    left: 315px;
	    width: 60px;
	}
	.quebrada-cienaga {
	    top: 560px;
	    left: 132px;
	    width: 154px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(342deg) brightness(118%) contrast(119%) ;
	}
	.san-mateo {
	    top: 230px;
	    left: 595px;
	    width: 115px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.san-pablo {
	    top: 269px;
	    left: 458px;
	    width: 375px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(158deg) brightness(88%) contrast(119%) ;
	}
	.san-pedro {
	    top: 383px;
	    left: 408px;
	    width: 243px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.santa-maria {
		top: 27px;
	    left: 380px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(57deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
	    z-index: 9999;
	    top: 158px;
	    left: 392px;
	    width: 22px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
