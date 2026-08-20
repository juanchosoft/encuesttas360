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
	.aguacatal {
	    top: 168px;
	    left: 520px;
	    width: 63px;
	    z-index: 999;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.alto-seco {
	    top: 224px;
	    left: 357px;
	    width: 139px;
	    filter: invert(48%) sepia(59%) saturate(876%) hue-rotate(104deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.canaduzales {
		top: 308px;
	    left: 424px;
	    width: 46px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.atezal {
	    top: 271px;
	    left: 329px;
	    width: 107px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.cruces {
	    top: 303px;
	    left: 429px;
	    width: 329px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(324deg) brightness(100%) contrast(119%);
	}
	.el-barro {
		top: 318px;
	    left: 392px;
	    width: 130px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.el-cantaro{
		top: 5px;
	    left: 465px;
	    width: 84px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(872deg) brightness(100%) contrast(80%);
	}
	.el-filo{
		top: 221px;
	    left: 521px;
	    width: 66px;
	    z-index: 9999;
	    filter: invert(88%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-morro {
		top: 183px;
	    left: 496px;
	    width: 76px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1173deg) brightness(88%) contrast(119%);
	}
	.el-penol {
		top: 114px;
	    left: 532px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-roble {
	    top: 237px;
	    left: 455px;
	    width: 79px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-vergel {
	    top: 240px;
	    left: 503px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(265deg) brightness(100%) contrast(80%);
	}
	.la-chorrera{
	    top: 226px;
	    left: 484px;
	    width: 195px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-cienaga{
	    top: 344px;
	    left: 213px;
	    width: 135px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-cordillera{
	    top: 124px;
	    left: 564px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-lejia {
	    top: 137px;
	    left: 620px;
	    width: 90px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.llanadas {
	    top: 430px;
	    left: 142px;
	    width: 139px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.loma-grande{
	    top: 188px;
	    left: 400px;
	    width: 100px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}
	.media-loma{
	    top: 63px;
	    left: 531px;
	    width: 88px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}	
	.montana-adentro {
	    top: 300px;
	    left: 327px;
	    width: 107px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.montebello{
	    top: 307px;
	    left: 226px;
	    width: 111px;
	    filter: invert(98%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-antonio {
	    top: 412px;
	    left: 290px;
	    width: 145px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.san-julian {
	    top: 394px;
	    left: 212px;
	    width: 99px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.san-miguel {
	    top: 118px;
	    left: 528px;
	    width: 113px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.santa-gertrudis{
	    top: 133px;
	    left: 452px;
	    width: 81px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(1276deg) brightness(118%) contrast(119%);		
	}
	.travesias {
		top: 121px;
	    left: 429px;
	    width: 48px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.malvaza {
	    top: 601px;
	    left: 621px;
	    width: 193px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.zona-urbana {
	    top: 360px;
	    left: 416px;
	    width: 19px;
	    z-index: 999;
        filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>