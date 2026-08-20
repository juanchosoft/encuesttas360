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
	.buga {
	    top: 330px;
	    left: 384px;
	    width: 64px;
	}
	.castalia {
	    top: 308px;
	    left: 285px;
	    width: 42px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.cestillala {
	    top: 355px;
	    left: 383px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.cauca {
	    top: 3px;
	    left: 300px;
   		width: 463px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.el-castillo {
	    top: 150px;
	    left: 274px;
	    width: 153px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(324deg) brightness(100%) contrast(119%);
	}
	.el-zacatin {
	    top: 327px;
	    left: 250px;
	    width: 65px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.guacamayal{
	    top: 251px;
	    left: 294px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-aguada{
	    top: 399px;
	    left: 136px;
	    width: 123px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.la-cabana {
	    top: 237px;
	    left: 456px;
	    width: 73px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.la-cascada {
		top: 156px;
	    left: 224px;
	    width: 99px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-estrella {
	    top: 354px;
	    left: 297px;
	    width: 129px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-fe {
	    top: 183px;
	    left: 400px;
	    width: 95px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(265deg) brightness(100%) contrast(80%);
	}
	.la-hermosa{
	    top: 300px;
	    left: 492px;
	    width: 68px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-leona{
	    top: 215px;
	    left: 343px;
	    width: 112px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-pista{
	    top: 270px;
	    left: 353px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.la-pradera {
	    top: 152px;
	    left: 193px;
	    width: 103px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.la-raya {
	    top: 559px;
	    left: 158px;
	    width: 120px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.la-selva{
	    top: 398px;
	    left: 212px;
	    width: 199px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}
	.la-sola{
	    top: 216px;
	    left: 293px;
	    width: 65px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}	
	.la-soledad {
	    top: 319px;
	    left: 549px;
	    width: 142px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.la-vina{
		top: 289px;
	    left: 429px;
	    width: 49px;
	    filter: invert(98%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.los-aguacates {
	    top: 268px;
	    left: 286px;
	    width: 32px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.palenque {
	    top: 300px;
	    left: 382px;
	    width: 64px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.palenquito {
	    top: 271px;
	    left: 387px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.palocabildo{
	    top: 341px;
	    left: 482px;
	    width: 97px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.palo-santo {
	    top: 301px;
	    left: 309px;
	    width: 91px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.quebradona {
	    top: 340px;
	    left: 418px;
	    width: 128px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.rio-frio {
	    top: 442px;
	    left: 213px;
	    width: 273px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-ramon {
	    top: 239px;
	    left: 440px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.vallecitos {
	    top: 333px;
	    left: 529px;
	    width: 75px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(158deg) brightness(119%) contrast(119%);
	}
	.volcan-colorado{
	    top: 332px;
	    left: 193px;
	    width: 85px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 284px;
	    left: 297px;
	    width: 69px;
	    z-index: 999;
        filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>