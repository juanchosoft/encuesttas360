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
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.albania{
		top: 348px;
	    left: 683px;
	    width: 104px;
	}
	.caracol {
		top: 7px;
	    left: 434px;
	    width: 233px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-balsal {
		top: 188px;
	    left: 138px;
	    width: 199px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-morro {
		top: 192px;
	    left: 73px;
	    width: 268px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-bosque {
	    top: 197px;
	    left: 526px;
	    width: 225px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.el-corcovado {
	    top: 355px;
	    left: 502px;
	    width: 145px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-porvenir {
	    top: 465px;
	    left: 485px;
	    width: 281px;
        filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-volcan {
	    top: 405px;
	    left: 744px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-zancudo {
	    top: 175px;
	    left: 413px;
	    width: 133px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.falda-del-cauca{
	    top: 173px;
	    left: 248px;
	    width: 174px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-meseta {
	    top: 380px;
	    left: 304px;
	    width: 238px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-pena {
		top: 404px;
	    left: 624px;
    	width: 72px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.loma-del-guamo {
		top: 640px;
	    left: 367px;
	    width: 241px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(257deg) brightness(100%) contrast(80%) ;
	}
	.los-micos {
		top: 333px;
	    left: 581px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(71deg) brightness(100%) contrast(80%) ;
	}
	.otramina{
	    top: 234px;
	    left: 289px;
	    width: 186px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.pueblito-los-bolivares {
		top: 478px;
	    left: 758px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.sinifana {
		top: 598px;
	    left: 214px;
	    width: 221px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.sitio-viejo {
	    top: 272px;
	    left: 442px;
	    width: 97px;
	}
	.zona-urbana {
		top: 378px;
	    left: 490px;
	    width: 46px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
