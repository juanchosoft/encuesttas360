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
	.el-escobero {
	    top: 308px;
	    left: 199px;
	    width: 232px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(2162deg) brightness(118%) contrast(119%);
	}
	.el-vallano {
		top: 364px;
	    left: 50px;
	    width: 368px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.las-palmas {
	    top: 112px;
	    left: 395px;
	    width: 313px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.pantanillo {
	    top: 113px;
	    left: 607px;
	    width: 285px;
	    filter: invert(78%) sepia(99%) saturate(496%) hue-rotate(2348deg) brightness(88%) contrast(119%);
	}
	.perico {
	    top: 76px;
	    left: 494px;
	    width: 254px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.santa-catalina {
	    top: 220px;
	    left: 259px;
	    width: 185px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.zona-urbana {
		top: 135px;
	    left: 7px;
	    width: 306px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
</style>
