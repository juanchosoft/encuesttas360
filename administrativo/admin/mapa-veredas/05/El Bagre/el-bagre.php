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
	.aguacates{
	    top: 538px;
	    left: 257px;
	    width: 67px;
	}
	.alto-del-berrugoso {
	    top: 614px;
	    left: 358px;
	    width: 23px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.alto-de-sabalito {
	    top: 128px;
	    left: 390px;
	    width: 47px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.arenas-blancas {
	    top: 643px;
	    left: 404px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.amaceri {
	    top: 270px;
	    left: 239px;
	    width: 107px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.arenales {
		top: 296px;
	    left: 342px;
	    width: 49px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.baldios-de-la-nacion {
	    top: 58px;
	    left: 403px;
	    width: 279px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.bamba {
	    top: 415px;
	    left: 288px;
	    width: 121px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.boca-del-guamo {
	    top: 84px;
	    left: 218px;
	    width: 83px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.borrachera{
	    top: 578px;
	    left: 277px;
	    width: 60px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.brojola {
	    top: 584px;
	    left: 261px;
	    width: 45px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.cabecera-municipal {
		top: 499px;
	    left: 226px;
	    width: 46px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.chaparrosa {
	    top: 557px;
	    left: 414px;
	    width: 36px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.chirita {
	    top: 352px;
	    left: 399px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.el-castillo{
		top: 278px;
	    left: 316px;
	    width: 51px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-oso {
	    top: 286px;
	    left: 405px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.el-pedral {
	    top: 572px;
	    left: 387px;
	    width: 49px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-perico {
	    top: 531px;
	    left: 384px;
	    width: 48px;
		filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-pital {
	    top: 87px;
	    left: 249px;
	    width: 47px;
	    z-index: 999;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.el-real {
	    top: 462px;
	    left: 262px;
	    width: 33px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-socorro {
	    top: 712px;
	    left: 406px;
	    width: 54px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.guachi {
		top: 108px;
	    left: 281px;
	    width: 113px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-aduana{
	    top: 157px;
	    left: 348px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-arenosa {
	    top: 183px;
	    left: 292px;
	    width: 55px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-bonga {
	    top: 595px;
	    left: 270px;
	    width: 96px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-corona {
	    top: 455px;
	    left: 378px;
	    width: 79px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-llana {
	    top: 258px;
	    left: 381px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-lucha {
	    top: 503px;
	    left: 325px;
	    width: 63px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-primavera{
		top: 254px;
	    left: 308px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-rica {
	    top: 352px;
	    left: 317px;
	    width: 46px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.las-claras {
	    top: 301px;
	    left: 380px;
	    width: 75px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(233deg) brightness(100%) contrast(80%);
	}
	.las-claritas {
	    top: 491px;
	    left: 385px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.las-dantas{
		top: 479px;
	    left: 283px;
	    width: 50px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-negritas {
	    top: 661px;
	    left: 358px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.luis-cano {
	    top: 514px;
	    left: 262px;
	    width: 63px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.matanza {
	    top: 560px;
	    left: 245px;
	    width: 52px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.medios-de-maniceria {
	    top: 103px;
	    left: 329px;
	    width: 92px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.mellizos {
	    top: 388px;
	    left: 390px;
	    width: 45px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.muqui {
	    top: 225px;
	    left: 354px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.negras-intermedias {
	    top: 672px;
	    left: 318px;
	    width: 60px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.puerto-claver{
		top: 381px;
	    left: 297px;
	    width: 16px;
	    z-index: 9999;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.rio-viejo {
		top: 183px;
	    left: 245px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.sabalito-sinai {
		top: 129px;
	    left: 229px;
	    width: 78px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.san-pedro {
	    top: 5px;
	    left: 221px;
	    width: 118px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%);
	}
	.san-carlos {
	    top: 257px;
	    left: 253px;
	    width: 65px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.san-cayetano{
	    top: 708px;
	    left: 393px;
	    width: 50px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-barbara {
	    top: 214px;
	    left: 339px;
	    width: 64px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.santa-barbarita {
	    top: 178px;
	    left: 328px;
	    width: 36px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.santa-isabel {
	    top: 464px;
	    left: 324px;
	    width: 67px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.santa-margarita {
	    top: 379px;
	    left: 280px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.santa-rosa {
	    top: 297px;
	    left: 274px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1834deg) brightness(100%) contrast(80%);
	}
	.santa-teresa {
	    top: 353px;
	    left: 342px;
	    width: 71px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.villa-grande {
	    top: 575px;
	    left: 340px;
	    width: 53px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.villa-hermosa {
	    top: 608px;
	    left: 375px;
	    width: 74px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.villa-ucuru{
	    top: 534px;
	    left: 305px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.zona-urbana {
	    top: 505px;
	    left: 226px;
	    width: 26px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
