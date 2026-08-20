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
	.bonilla {
	    top: 581px;
	    left: 306px;
	    width: 64px;
	}
	.el-roble {
		top: 246px;
	    left: 283px;
	    width: 353px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(62deg) brightness(118%) contrast(119%) ;
	}
	.el-rosario {
	    top: 49px;
	    left: 444px;
	    width: 201px;
	    filter: invert(418%) sepia(189%) saturate(206%) hue-rotate(2deg) brightness(98%) contrast(140%) ;
	}
	.embalse {
	    top: 217px;
	    left: 258px;
	    width: 247px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(143deg) brightness(188%) contrast(119%) ;
	}
	.la-pena {
	    top: 612px;
	    left: 323px;
	    width: 103px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.la-piedra {
	    top: 518px;
	    left: 296px;
	    width: 182px;
	    filter: invert(15%) sepia(109%) saturate(256%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.la-sonadora{
	    top: 608px;
	    left: 350px;
	    width: 164px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.los-naranjos {
	    top: 407px;
	    left: 272px;
	    width: 151px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(43deg) brightness(88%) contrast(119%) ;
	}
	.quebrada-arriba {
		top: 501px;
	    left: 416px;
	    width: 199px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.santa-rita {
	    top: 5px;
	    left: 457px;
	    width: 119px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.zona-urbana {
		top: 484px;
	    left: 415px;
	    width: 54px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
