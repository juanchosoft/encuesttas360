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
	.colmenas {
	    top: 608px;
	    left: 380px;
	    width: 181px;
	}
	.el-higueron {
		top: 540px;
	    left: 175px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.el-tambo {
	    top: 226px;
	    left: 335px;
	    width: 224px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%) ;
	}
	.fatima {
	    top: 479px;
	    z-index: 888;
	    left: 386px;
	    width: 95px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%) ;
	}
	.guamito {
	    top: 15px;
	    left: 534px;
	    width: 213px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.la-loma {
		top: 466px;
	    left: 248px;
	    width: 115px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}	
	.la-miel{
		top: 496px;
	    left: 104px;
	    width: 123px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-milagrosa {
	    top: 132px;
	    left: 349px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.la-playa {
	    top: 322px;
	    left: 296px;
	    width: 87px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(1229deg) brightness(118%) contrast(200%) ;
	}
	.llanadas {
		top: 396px;
	    left: 439px;
	    width: 127px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.lomitas {
		top: 129px;
	    left: 489px;
	    width: 262px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.piedras{
	    top: 540px;
	    left: 402px;
	    width: 137px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.san-gerardo{
	    top: 356px;
	    left: 345px;
	    width: 104px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.san-jose{
	    top: 343px;
	    left: 148px;
	    width: 165px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-miguel{
	    top: 104px;
	    left: 577px;
	    width: 220px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.san-nicolas {
	    top: 4px;
	    left: 398px;
	    width: 165px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(287deg) brightness(88%) contrast(119%) ;
	}
	.san-rafael {
	    top: 498px;
	    left: 299px;
	    width: 117px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(114deg) brightness(118%) contrast(200%) ;
	}
	.zona-urbana {
	    top: 151px;
	    left: 406px;
	    width: 117px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
