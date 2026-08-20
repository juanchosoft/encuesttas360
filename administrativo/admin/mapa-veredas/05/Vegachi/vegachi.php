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
	.belgica{
	    top: 442px;
	    left: 309px;
	    width: 87px;
	}
	.bellavista {
	    top: 663px;
	    left: 96px;
	    width: 160px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.corinto {
	    top: 236px;
	    left: 591px;
	    width: 306px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-cinco {
	    top: 412px;
	    left: 549px;
	    width: 186px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.el-cascajo {
	    top: 418px;
	    left: 52px;
	    width: 146px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.el-churu {
	    top: 324px;
	    left: 141px;
	    width: 149px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.el-jabon {
	    top: 451px;
	    left: 216px;
	    width: 127px;
        filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-olvido {
	    top: 333px;
	    left: 633px;
	    width: 145px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.el-pescado {
	    top: 204px;
	    left: 123px;
	    width: 204px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.el-tigre{
	    top: 293px;
	    left: 263px;
	    width: 147px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-alejandria {
	    top: 468px;
	    left: 181px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-ceiba {
	    top: 432px;
	    left: 422px;
	    width: 192px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.la-clarita {
	    top: 323px;
	    left: 478px;
	    width: 187px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(257deg) brightness(100%) contrast(80%) ;
	}
	.la-cristalina {
		top: 513px;
	    left: 323px;
	    width: 141px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.la-gallinera{
	    top: 533px;
	    left: 144px;
	    width: 113px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-sierra {
	    top: 502px;
	    left: 242px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.la-sonadora {
	    top: 614px;
	    left: 219px;
	    width: 128px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-union {
	    top: 391px;
	    left: 705px;
	    width: 192px;
	}
	.mata-arriba {
		top: 275px;
	    left: 228px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(342deg) brightness(118%) contrast(119%) ;
	}
	.mata-baja {
	    top: 62px;
	    left: 180px;
	    width: 90px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.mona {
	    top: 257px;
	    left: 370px;
	    width: 246px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(158deg) brightness(88%) contrast(119%) ;
	}
	.piedrancha {
	    top: 540px;
	    left: 4px;
	    width: 172px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-juan {
	    top: 378px;
	    left: 334px;
	    width: 130px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(57deg) brightness(100%) contrast(80%) ;
	}
	.san-pascual {
	    top: 573px;
	    left: 226px;
	    width: 77px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.zapatillo {
	    top: 581px;
	    left: 361px;
	    width: 127px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.zona-urbana {
	    top: 598px;
	    left: 319px;
	    width: 34px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
