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
<?php include(__DIR__ . "/../../mapa_veredas.php"); ?>    	</div>
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
	.arbolitos {
	    top: 490px;
	    left: 153px;
	    width: 277px;
	}
	.cartagueno {
		top: 348px;
	    left: 531px;
	    width: 107px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(13deg) brightness(118%) contrast(119%);
	}
	.el-socorro {
	    top: 472px;
	    left: 448px;
	    width: 127px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.la-horcona {
	    top: 423px;
	    left: 589px;
	    width: 109px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.la-loma {
		top: 8px;
	    left: 139px;
	    width: 407px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.la-pescadora {
		top: 495px;
	    left: 372px;
	    width: 107px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.la-quiebra{
	    top: 367px;
	    left: 613px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.palmichal{
		top: 364px;
	    left: 213px;
	    width: 292px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.palo-blanco {
	    top: 359px;
	    left: 659px;
	    width: 101px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.travesias {
	    top: 414px;
	    left: 543px;
	    width: 91px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.zona-urbana {
		top: 448px;
	    left: 493px;
	    width: 46px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
