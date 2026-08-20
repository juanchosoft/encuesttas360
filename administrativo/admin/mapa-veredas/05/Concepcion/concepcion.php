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
	.aragon {
	    top: 170px;
	    left: 87px;
	    width: 241px;
	}
	.barro-blanco {
		top: 422px;
	    left: 45px;
	    width: 184px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.el-remango{
	    top: 299px;
	    left: 648px;
	    width: 116px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.embalse {
		top: 679px;
	    left: 574px;
	    width: 155px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.la-candelaria {
		top: 14px;
	    left: 406px;
	    width: 344px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%) ;
	}
	.la-cejita{
	    top: 344px;
	    left: 25px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.la-clara {
	    top: 201px;
	    left: 517px;
	    width: 119px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%) ;
	}
	.la-fatima {
	    top: 192px;
	    left: 720px;
	    width: 178px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-palma {
	    top: 148px;
	    left: 210px;
	    width: 114px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-piedad {
	    top: 279px;
	    left: 456px;
	    width: 228px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.las-frias {
	    top: 448px;
	    left: 179px;
	    width: 128px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.las-mercedes{
	    top: 461px;
	    left: 543px;
	    width: 267px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.la-sonadora{
	    top: 372px;
	    left: 604px;
	    width: 157px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.la-trinidad {
	    top: 465px;
	    left: 396px;
	    width: 319px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(259deg) brightness(100%) contrast(150%) ;
	}
	.morro-reyes {
		top: 154px;
	    left: 353px;
	    width: 180px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%) ;
	}
	.palmichal{
	    top: 317px;
	    left: 147px;
	    width: 166px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(315deg) brightness(98%) contrast(40%) ;
	}
	.pelaez {
	    top: 95px;
	    left: 326px;
	    width: 171px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.san-bertolome{
		top: 38px;
	    left: 344px;
	    width: 111px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.san-juan-alto{
	    top: 254px;
	    left: 3px;
	    width: 113px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(94deg) brightness(118%) contrast(119%) ;
	}
	.san-juan-llano {
		top: 277px;
	    left: 71px;
	    width: 141px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.san-pedro-penol-parte-alta {
	    top: 464px;
	    left: 277px;
	    width: 170px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.san-pedro-penol-parte-baja {
	    top: 559px;
	    left: 255px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.santa-ana {
	    top: 306px;
	    left: 340px;
	    width: 137px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.santa-gertrudis {
		top: 296px;
	    left: 232px;
	    width: 199px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.tafetanes {
	    top: 97px;
	    left: 286px;
	    width: 83px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 309px;
	    left: 254px;
	    width: 49px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
