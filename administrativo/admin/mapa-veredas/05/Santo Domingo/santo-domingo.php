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
	.bajo-cantayus{
		top: 177px;
	    left: 829px;
	    width: 62px;
	}
	.botero {
	    top: 160px;
	    left: 117px;
	    width: 29px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cubiletes {
		top: 172px;
	    left: 386px;
	    width: 88px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-bazal {
	    top: 174px;
	    left: 611px;
	    width: 35px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-brasil{
	    top: 240px;
	    left: 679px;
	    width: 215px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.dantas {
	    top: 501px;
	    left: 692px;
	    width: 153px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.el-anime {
		top: 570px;
	    left: 551px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-chilcal {
	    top: 181px;
	    left: 356px;
	    width: 48px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-limon {
	    top: 139px;
	    left: 509px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-rayo {
	    top: 294px;
	    left: 503px;
	    width: 171px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.el-rosario{
	    top: 496px;
	    left: 459px;
	    width: 138px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-saltillo {
	    top: 469px;
	    left: 326px;
	    width: 155px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-tambo {
	    top: 293px;
	    left: 348px;
	    width: 114px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-uvito {
	    top: 246px;
	    left: 78px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.faldas-del-nus {
	    top: 195px;
	    left: 493px;
	    width: 103px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.guadualejo{
	    top: 168px;
	    left: 573px;
	    width: 121px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-aldea {
	    top: 273px;
	    left: 159px;
	    width: 104px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-comba {
	    top: 121px;
	    left: 273px;
	    width: 111px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-palma {
	    top: 184px;
	    left: 877px;
	    width: 16px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-delgadita{
	    top: 181px;
	    left: 787px;
	    width: 61px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);	
	}
	.la-primavera {
	    top: 115px;
	    left: 146px;
	    width: 149px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-quiebra {
		top: 140px;
	    left: 466px;
	    width: 55px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.las-animas {
	    top: 473px;
	    left: 552px;
	    width: 173px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-trinidad {
		top: 457px;
	    left: 278px;
	    width: 107px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.los-naranjos {
	    top: 613px;
	    left: 428px;
	    width: 165px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.los-planes {
	    top: 226px;
	    left: 349px;
	    width: 176px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-beatrices {
	    top: 189px;
	    left: 48px;
	    width: 77px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.montebello {
	    top: 213px;
	    left: 225px;
	    width: 151px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.moro {
	    top: 383px;
	    left: 302px;
	    width: 100px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.piedra-gorda{
	    top: 132px;
	    left: 97px;
	    width: 154px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.playa-rica {
		top: 571px;
	    left: 797px;
	    width: 97px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.playas {
	    top: 397px;
	    left: 222px;
	    width: 109px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.playas-del-nare {
	    top: 649px;
	    left: 579px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.porce{
	    top: 51px;
	    left: 216px;
	    width: 201px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.porcesito {
	    top: 120px;
	    left: 230px;
	    width: 26px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.quebradona{
	    top: 313px;
	    left: 647px;
	    width: 110px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.raudal {
	    top: 316px;
	    left: 217px;
	    width: 140px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.reyes{
	    top: 421px;
	    left: 381px;
	    width: 178px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.san-francisco {
		top: 216px;
	    left: 615px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.san-javier{
	    top: 378px;
	    left: 551px;
	    width: 149px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.san-jose{
	    top: 327px;
	    left: 424px;
	    width: 116px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.san-luis {
		top: 594px;
	    left: 630px;
	    width: 220px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-pedro {
		top: 405px;
	    left: 108px;
	    width: 243px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.santa-gertrudis {
	    top: 262px;
	    left: 516px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.santiago{
	    top: 81px;
	    left: 373px;
	    width: 144px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.vainillal {
	    top: 239px;
	    left: 6px;
	    width: 89px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.versalles{
	    top: 173px;
	    left: 689px;
	    width: 16px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);		
	}
	.zona-urbana {
	    top: 413px;
	    left: 376px;
	    width: 52px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
