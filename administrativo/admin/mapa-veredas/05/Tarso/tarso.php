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
	.cannan{
	    top: 82px;
	    left: 336px;
	    width: 542px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(946deg) brightness(118%) contrast(119%);
	}
	.cascabel {
	    top: 510px;
	    left: 159px;
	    width: 101px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.chaguany {
	    top: 283px;
	    left: 134px;
	    width: 90px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.la-arboleda {
	    top: 585px;
	    left: 233px;
	    width: 114px;
        filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-cedron {
		top: 651px;
	    left: 301px;
	    width: 189px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.el-plan {
	    top: 518px;
	    left: 252px;
	    width: 101px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-herradura {
	    top: 136px;
	    left: 27px;
	    width: 122px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-linda {
	    top: 479px;
	    left: 329px;
	    width: 239px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.los-dolores {
	    top: 364px;
	    left: 159px;
	    width: 124px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.morron{
	    top: 409px;
	    left: 92px;
	    width: 93px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.mulatico {
		top: 149px;
	    left: 256px;
	    width: 234px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.mulato {
	    top: 435px;
	    left: 256px;
	    width: 113px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.el-retiro {
		top: 519px;
	    left: 391px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.patio-bonito {
	    top: 239px;
	    left: 382px;
	    width: 219px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}	
	.san-francisco {
	    top: 4px;
	    left: 108px;
	    width: 293px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.tacamocho{
	    top: 502px;
	    left: 326px;
	    width: 145px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 454px;
	    left: 433px;
	    width: 42px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
