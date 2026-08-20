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
	.bellavista{
	    top: 247px;
	    left: 423px;
	    width: 127px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);		
	}
	.cajon-largo {
	    top: 438px;
	    left: 279px;
	    width: 69px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-clavel {
	    top: 471px;
	    left: 402px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-concilio {
	    top: 368px;
	    left: 519px;
	    width: 218px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(356deg) brightness(118%) contrast(119%);
	}
	.chaquiro-abajo {
	    top: 424px;
	    left: 455px;
	    width: 133px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.chaquiro-arriba {
	    top: 479px;
	    left: 452px;
	    width: 147px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-junco {
	    top: 518px;
	    left: 683px;
	    width: 111px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-leon {
	    top: 15px;
	    left: 349px;
	    width: 201px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-roblal {
	    top: 70px;
	    left: 142px;
	    width: 174px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.la-amagacena{
	    top: 200px;
	    left: 309px;
	    width: 178px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-botija {
	    top: 429px;
	    left: 712px;
	    width: 154px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-chuchita {
	    top: 616px;
	    left: 648px;
	    width: 109px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.la-clara-arriba {
	    top: 139px;
	    left: 211px;
	    width: 226px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.la-granizo {
	    top: 138px;
	    left: 49px;
	    width: 249px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-gulunga-abajo{
	    top: 521px;
	    left: 469px;
	    width: 142px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-gulunga-arriba {
	    top: 540px;
	    left: 376px;
	    width: 120px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-humareda {
	    top: 450px;
	    left: 240px;
	    width: 232px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-margarita {
	    top: 367px;
	    left: 248px;
	    width: 167px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-liboriana{
		top: 212px;
	    left: 5px;
	    width: 256px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);	
	}
	.la-ovejita {
	    top: 344px;
	    left: 251px;
	    width: 122px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.las-andes {
	    top: 17px;
	    left: 289px;
	    width: 124px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-siberia {
	    top: 570px;
	    left: 573px;
	    width: 118px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-taborda {
		top: 436px;
	    left: 557px;
	    width: 134px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1206deg) brightness(88%) contrast(119%);
	}
	.la-troya {
	    top: 211px;
	    left: 481px;
	    width: 103px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.llanadas {
	    top: 265px;
	    left: 520px;
	    width: 91px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-sierva {
	    top: 438px;
	    left: 148px;
	    width: 149px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.montanita {
	    top: 405px;
	    left: 399px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.montebello-abajo {
	    top: 280px;
	    left: 363px;
	    width: 142px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.montebello-arriba{
	    top: 259px;
	    left: 216px;
	    width: 190px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.morritos {
	    top: 462px;
	    left: 648px;
	    width: 90px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.penalisa {
	    top: 437px;
	    left: 780px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.tarqui {
		top: 110px;
	    left: 485px;
	    width: 106px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 399px;
	    left: 408px;
	    width: 88px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
