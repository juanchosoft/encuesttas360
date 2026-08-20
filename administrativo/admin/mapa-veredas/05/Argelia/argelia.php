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
	.alto-bonito{
	    top: 298px;
	    left: 504px;
	    width: 45px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.alto-de-samana {
	    top: 304px;
	    left: 682px;
	    width: 119px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.arenillal {
	    top: 291px;
	    left: 330px;
	    width: 41px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.cabecera-municipal {
	    top: 285px;
	    left: 195px;
	    width: 103px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.chamberry{
		top: 346px;
	    left: 480px;
	    width: 159px;
	    filter: invert(15%) sepia(19%) saturate(76%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.buenavista {
	    top: 307px;
	    left: 411px;
	    width: 64px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.buenos-aires {
		top: 382px;
	    left: 406px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.claras {
	    top: 417px;
	    left: 505px;
	    width: 133px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-bosque {
	    top: 371px;
	    left: 372px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-bujio {
	    top: 375px;
	    left: 392px;
	    width: 29px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.el-rosario{
	    top: 275px;
	    left: 357px;
	    width: 70px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-cabuyo {
	    top: 333px;
	    left: 205px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-diamante {
	    top: 352px;
	    left: 287px;
	    width: 46px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-dragal {
	    top: 278px;
	    left: 409px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-fresnito {
	    top: 321px;
	    left: 279px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.el-guadual{
		top: 402px;
	    left: 226px;
	    width: 37px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-llano {
	    top: 309px;
	    left: 280px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-oro {
	    top: 394px;
	    left: 148px;
	    width: 59px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-pital {
		top: 471px;
	    left: 317px;
	    width: 138px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-peru{
	    top: 283px;
	    left: 460px;
	    width: 51px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);	
	}
	.la-primavera {
	    top: 487px;
	    left: 147px;
	    width: 87px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-quiebra {
		top: 439px;
	    left: 277px;
	    width: 52px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-plan {
	    top: 436px;
	    left: 302px;
	    width: 153px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-silencio {
		top: 346px;
	    left: 237px;
	    width: 67px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-tesoro {
	    top: 252px;
	    left: 320px;
	    width: 65px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.el-zancudo {
	    top: 380px;
	    left: 192px;
	    width: 78px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-recreo {
	    top: 500px;
	    left: 227px;
	    width: 50px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.guadualito {
	    top: 262px;
	    left: 783px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.guaimaral {
	    top: 465px;
	    left: 12px;
	    width: 154px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.guayabal{
	    top: 340px;
	    left: 338px;
	    width: 35px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.la-arabia {
	    top: 415px;
	    left: 402px;
	    width: 68px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-arboleda {
		top: 426px;
	    left: 255px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-estrella {
	    top: 254px;
	    left: 279px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.la-julia{
		top: 303px;
	    left: 161px;
	    width: 68px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.la-manuela {
	    top: 352px;
	    left: 423px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-margarita{
	    top: 401px;
	    left: 439px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-mina {
	    top: 252px;
	    left: 301px;
	    width: 64px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-plata{
		top: 442px;
	    left: 170px;
	    width: 117px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-reina {
	    top: 340px;
	    left: 391px;
	    width: 73px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.mesones{
	    top: 293px;
	    left: 529px;
	    width: 169px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.san-luis {
	    top: 416px;
	    left: 103px;
	    width: 145px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.rancho-largo {
		top: 311px;
	    left: 3px;
	    width: 197px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.san-agustin {
	    top: 491px;
	    left: 192px;
	    width: 56px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-juan{
	    top: 344px;
	    left: 532px;
	    width: 147px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-pablo {
	    top: 389px;
	    left: 17px;
	    width: 162px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.santa-ines{
	    top: 258px;
	    left: 338px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);		
	}
	.santa-teresa {
	    top: 393px;
	    left: 261px;
	    width: 42px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.tabanales {
		top: 237px;
	    left: 260px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.villeta-florida {
	    top: 364px;
	    left: 291px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.yarumal{
		top: 380px;
	    left: 346px;
	    width: 64px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 322px;
	    left: 243px;
	    width: 31px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
