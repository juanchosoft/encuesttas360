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
	.bartolo-quemado {
	    top: 94px;
	    left: 456px;
	    width: 247px;
	}
	.bebarmeno {
	    top: 359px;
	    left: 121px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.san-ignacio {
	    top: 474px;
	    left: 168px;
	    width: 93px;
	    filter: invert(218%) sepia(189%) saturate(206%) hue-rotate(5deg) brightness(98%) contrast(140%);
	}
	.campo-alegre {
		top: 372px;
	    left: 189px;
	    width: 142px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(348deg) brightness(88%) contrast(119%);
	}
	.bellaluz {
	    top: 170px;
	    left: 221px;
	    width: 206px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}	
	.canal {
	    top: 14px;
	    left: 300px;
	    width: 192px;
	    filter: invert(15%) sepia(59%) saturate(576%) hue-rotate(418deg) brightness(100%) contrast(119%);
	}
	.chagerado {
	    top: 491px;
	    left: 145px;
	    width: 318px;
	    filter: invert(15%) sepia(19%) saturate(576%) hue-rotate(118deg) brightness(100%) contrast(119%);
	}	
	.chibugado{
	    top: 475px;
	    left: 306px;
	    width: 286px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.coredo{
	    top: 261px;
	    left: 653px;
	    width: 145px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);		
	}
	.el-hobo {
	    top: 58px;
	    left: 678px;
	    width: 133px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(173deg) brightness(88%) contrast(119%);
	}
	.guagua {
	    top: 154px;
	    left: 385px;
	    width: 161px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.isla {
	    top: 305px;
	    left: 384px;
	    width: 331px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.jedega {
	    top: 449px;
	    left: 444px;
	    width: 140px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.murindo-guamal{
	    top: 42px;
	    left: 200px;
	    width: 145px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.murindo-viejo{
	    top: 76px;
	    left: 318px;
	    width: 160px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.narangue{
	    top: 405px;
	    left: 280px;
	    width: 175px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.opogado {
	    top: 366px;
	    left: 43px;
	    width: 266px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(120deg) brightness(100%) contrast(150%);
	}
	.pital {
	    top: 5px;
	    left: 476px;
	    width: 231px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(262deg) brightness(118%) contrast(119%);
	}
	.santa-fe{
	    top: 123px;
	    left: 625px;
	    width: 234px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}
	.turriquitado-alto{
	    top: 386px;
	    left: 562px;
	    width: 181px;
	    filter: invert(180%) sepia(89%) saturate(206%) hue-rotate(115deg) brightness(98%) contrast(40%);
	}	
	.turriquitado-llano {
	    top: 597px;
	    left: 551px;
	    width: 198px;
	    filter: invert(15%) sepia(119%) saturate(276%) hue-rotate(894deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 102px;
	    left: 233px;
	    width: 36px;
        filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>