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
	.atausi{
	    top: 404px;
	    left: 219px;
	    width: 107px;
	}
	.barrancas {
	    top: 45px;
	    left: 505px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.cabras {
	    top: 251px;
	    left: 706px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.carautica {
		top: 378px;
	    left: 514px;
	    width: 106px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.cabritas {
	    top: 191px;
	    left: 732px;
	    width: 75px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%) ;
	}
	.carauta {
	    top: 407px;
	    left: 423px;
	    width: 107px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.chimurro {
	    top: 247px;
	    left: 249px;
	    width: 100px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.chontaduro {
	    top: 351px;
	    left: 305px;
	    width: 99px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%) ;
	}
	.chontauduro-paramillo {
	    top: 103px;
	    left: 624px;
	    width: 67px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.chuscal-de-musinga{
		top: 288px;
	    left: 639px;
	    width: 55px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%) ;
	}
	.cuevas {
	    top: 258px;
	    left: 388px;
	    width: 176px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.curadientes {
	    top: 263px;
	    left: 528px;
	    width: 115px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%) ;
	}
	.curbata {
	    top: 559px;
	    left: 93px;
	    width: 175px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-campina {
	    top: 168px;
	    left: 539px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(347deg) brightness(100%) contrast(80%) ;
	}
	.el-llano {
	    top: 199px;
	    left: 590px;
	    width: 44px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.el-paso {
	    top: 186px;
	    left: 611px;
	    width: 34px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.el-pozo {
		top: 234px;
	    left: 539px;
	    width: 69px;
	}
	.el-salado {
	    top: 458px;
	    left: 511px;
	    width: 86px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%) ;
	}
	.el-tambo {
	    top: 223px;
	    left: 503px;
	    width: 89px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.el-cerro {
	    top: 302px;
	    left: 715px;
	    width: 82px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.el-guayabo {
	    top: 349px;
	    left: 596px;
	    width: 91px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.fuemuia {
		top: 85px;
	    left: 525px;
	    width: 104px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%) ;
	}
	.guaguas {
	    top: 304px;
	    left: 305px;
	    width: 98px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.la-cabana{
		top: 139px;
	    left: 782px;
	    width: 32px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%) ;
	}
	.la-clara {
		top: 459px;
	    left: 570px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.la-herradura {
		top: 205px;
	    left: 810px;
	    width: 63px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%) ;
	}
	.la-honda {
	    top: 248px;
	    left: 840px;
	    width: 49px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.la-hondita {
	    top: 271px;
	    left: 838px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.la-marina {
		top: 131px;
	    left: 257px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%) ;
	}
	.las-azules{
	    top: 359px;
	    left: 725px;
	    width: 47px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.las-cruces {
	    top: 187px;
	    left: 798px;
	    width: 38px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.loma-los-indios {
	    top: 166px;
	    left: 757px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.monos {
	    top: 128px;
	    left: 580px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%) ;
	}
	.montanon{
	    top: 89px;
	    left: 503px;
	    width: 81px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.murindo {
		top: 18px;
	    left: 554px;
	    width: 89px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%) ;
	}
	.musinga {
	    top: 228px;
	    left: 653px;
	    width: 64px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.musinguita {
	    top: 281px;
	    left: 651px;
	    width: 93px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.noboga {
		top: 145px;
	    left: 685px;
	    width: 74px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.nobogacita {
	    top: 175px;
	    left: 645px;
	    width: 49px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%) ;
	}	
	.nore {
	    top: 276px;
	    left: 776px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%) ;
	}
	.pantanos {
	    top: 292px;
	    left: 2px;
	    width: 300px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%) ;
	}	
	.pegado{
	    top: 174px;
	    left: 256px;
	    width: 197px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%) ;
	}
	.piedras {
	    top: 201px;
	    left: 624px;
	    width: 31px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.piedras-blancas {
	    top: 242px;
	    left: 622px;
	    width: 39px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%) ;
	}
	.ponton {
	    top: 299px;
	    left: 831px;
	    width: 69px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%) ;
	}	
	.quiparado {
	    top: 450px;
	    left: 312px;
	    width: 214px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(325deg) brightness(118%) contrast(119%) ;
	}
	.rio-verde {
	    top: 43px;
	    left: 576px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%) ;
	}
	.san-andres{
	    top: 37px;
	    left: 546px;
	    width: 54px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%) ;
	}
	.san-lazaro {
		top: 282px;
	    left: 720px;
	    width: 72px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%) ;
	}
	.san-mateo {
	    top: 406px;
	    left: 284px;
	    width: 171px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%) ;
	}
	.san-miguel {
	    top: 359px;
	    left: 397px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%) ;
	}
	.venados {
	    top: 460px;
	    left: 304px;
	    width: 453px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%) ;
	}
	.zona-urbana {
		top: 234px;
	    left: 781px;
	    width: 37px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}
</style>
