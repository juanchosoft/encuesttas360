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
	.barcino {
		top: 669px;
	    left: 433px;
	    width: 60px;
	}
	.california {
	    top: 346px;
	    left: 465px;
	    width: 82px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.castalia {
		top: 606px;
	    left: 549px;
	    width: 63px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%) ;
	}
	.corinto {
		top: 395px;
	    left: 400px;
	    width: 111px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%) ;
	}
	.el-cedron {
		top: 387px;
	    left: 544px;
	    width: 129px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.hoyo-grande {
		top: 5px;
	    left: 229px;
	    width: 185px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}	
	.la-berrio{
	    top: 528px;
	    left: 536px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-envidia{
	    top: 476px;
	    left: 521px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;		
	}
	.la-gomez {
	    top: 348px;
	    left: 483px;
	    width: 31px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.la-pica {
		top: 640px;
	    left: 511px;
	    width: 79px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-union {
	    top: 578px;
	    left: 562px;
	    width: 92px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.lourdes {
		top: 575px;
	    left: 494px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.morron{
	    top: 200px;
	    left: 347px;
	    width: 99px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.mulatico{
		top: 620px;
	    left: 423px;
	    width: 117px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.mulato{
	    top: 539px;
	    left: 404px;
	    width: 110px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.patudal {
	    top: 531px;
	    left: 533px;
	    width: 103px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%) ;
	}
	.san-francisco {
		top: 520px;
	    left: 461px;
	    width: 87px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.santa-barbara{
	    top: 387px;
	    left: 509px;
	    width: 43px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.sevilla {
	    top: 349px;
	    left: 418px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.sinai{
	    top: 288px;
	    left: 416px;
	    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
		top: 486px;
	    left: 527px;
	    width: 64px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
