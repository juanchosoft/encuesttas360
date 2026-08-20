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
	    height: 700px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.canaveralejo{
	    top: 285px;
	    left: 252px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-doctora {
	    top: 213px;
	    left: 375px;
	    width: 394px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.las-lomitas {
		top: 154px;
	    left: 390px;
	    width: 278px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.maria-auxiliadora {
	    top: 132px;
	    left: 552px;
	    width: 182px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.pan-de-azucar {
	    top: 185px;
	    left: 134px;
	    width: 207px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.san-jose {
	    top: 278px;
	    left: 316px;
	    width: 104px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.zona-urbana{
	    top: 5px;
	    left: 136px;
	    width: 449px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
</style>
