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
	    width: 1000px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.alto-la-compania{
	    top: 504px;
	    left: 349px;
	    width: 80px;
	    filter: invert(48%) sepia(79%) saturate(876%) hue-rotate(8deg) brightness(118%) contrast(119%);
	}
	.cantor {
	    top: 81px;
	    left: 313px;
	    width: 157px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.chaparral {
	    top: 527px;
	    left: 150px;
	    width: 184px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-calvario {
	    top: 245px;
	    left: 371px;
	    width: 153px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-canelo{
	    top: 301px;
	    left: 657px;
	    width: 106px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.compania-abajo {
	    top: 594px;
	    left: 374px;
	    width: 146px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.corrientes {
		top: 350px;
	    left: 719px;
	    width: 170px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-carmelo {
	    top: 328px;
	    left: 621px;
	    width: 118px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-coral {
	    top: 368px;
	    left: 177px;
	    width: 151px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-guaciro {
	    top: 333px;
	    left: 354px;
	    width: 102px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-perpetuo-socorro{
		top: 442px;
	    left: 267px;
	    width: 117px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-porvenir {
	    top: 349px;
	    left: 504px;
	    width: 137px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-potrero {
	    top: 543px;
	    left: 402px;
	    width: 107px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.guamal {
	    top: 426px;
	    left: 725px;
	    width: 138px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}	
	.cachumbal {
	    top: 244px;
	    left: 289px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.guamito {
		top: 47px;
	    left: 194px;
	    width: 147px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(2171deg) brightness(100%) contrast(80%);
	}
	.la-cabana{
	    top: 470px;
	    left: 671px;
	    width: 96px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-enea {
	    top: 379px;
	    left: 303px;
	    width: 123px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-floresta {
	    top: 474px;
	    left: 444px;
	    width: 174px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-honda {
	    top: 493px;
	    left: 550px;
	    width: 123px;
	    filter: invert(5%) sepia(50%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-magdalena {
    	top: 213px;
	    left: 408px;
	    width: 290px;
	    filter: invert(48%) sepia(19%) saturate(476%) hue-rotate(141deg) brightness(118%) contrast(119%);
	}
	.la-pena {
	    top: 556px;
	    left: 493px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-porquera {
	    top: 666px;
	    left: 189px;
	    width: 160px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.las-cruces {
	    top: 206px;
	    left: 170px;
	    width: 121px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.santa-rita{
		top: 404px;
	    left: 150px;
	    width: 163px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.las-hojas {
	    top: 630px;
	    left: 312px;
	    width: 168px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-travesia {
	    top: 367px;
	    left: 425px;
	    width: 98px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.ovejas {
	    top: 119px;
	    left: 96px;
	    width: 119px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.montegrande {
	    top: 115px;
	    left: 399px;
	    width: 248px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(24deg) brightness(118%) contrast(119%);
	}
	.las-frias {
	    top: 215px;
	    left: 625px;
	    width: 142px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.penolcito{
		top: 487px;
	    left: 654px;
	    width: 166px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.piedra-gorda {
	    top: 435px;
	    left: 602px;
	    width: 162px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(46deg) brightness(88%) contrast(119%);
	}
	.potrerito {
		top: 428px;
	    left: 810px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.san-antonio-la-compania {
		top: 528px;
	    left: 302px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.san-cristobal{
		top: 295px;
	    left: 495px;
	    width: 147px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 175px;
	    left: 227px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1158deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 4px;
	    left: 322px;
	    width: 246px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.retiro {
	    top: 76px;
	    left: 779px;
	    width: 219px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-nicolas {
	    top: 204px;
	    left: 288px;
	    width: 135px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.santa-ana {
	    top: 422px;
	    left: 424px;
	    width: 168px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.santa-isabel {
	    top: 292px;
	    left: 741px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 468px;
	    left: 416px;
	    width: 45px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
