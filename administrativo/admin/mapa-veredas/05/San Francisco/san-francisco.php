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
	.altavista{
		top: 185px;
	    left: 680px;
	    width: 204px;
	}
	.asiento-grande {
	    top: 112px;
	    left: 24px;
	    width: 90px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.boqueron {
		top: 112px;
	    left: 180px;
	    width: 176px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-arrebol {
	    top: 529px;
	    left: 321px;
	    width: 142px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-brillante{
	    top: 554px;
	    left: 220px;
	    width: 181px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);		
	}
	.canada-honda {
	    top: 141px;
	    left: 122px;
	    width: 64px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.comejenes {
	    top: 226px;
	    left: 277px;
	    width: 141px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-castillo-venecia {
	    top: 206px;
	    left: 213px;
	    width: 75px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-jardin-buenos-aires {
	    top: 176px;
	    left: 160px;
	    width: 70px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-jardin-de-aquitania {
	    top: 532px;
	    left: 486px;
	    width: 130px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.el-pajui{
		top: 142px;
	    left: 38px;
	    width: 57px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-porton {
	    top: 203px;
	    left: 418px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-porvenir {
		top: 260px;
	    left: 225px;
	    width: 89px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-tagual {
	    top: 31px;
	    left: 112px;
	    width: 100px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.el-venado-chumurro {
	    top: 382px;
	    left: 284px;
	    width: 159px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.farallones{
	    top: 176px;
	    left: 89px;
	    width: 45px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.guacales {
	    top: 24px;
	    left: 64px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.la-arauca {
	    top: 78px;
	    left: 517px;
	    width: 219px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-eresma {
	    top: 99px;
	    left: 116px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.la-cristalina{
	    top: 349px;
	    left: 653px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);	
	}
	.la-esperanza {
	    top: 166px;
	    left: 68px;
	    width: 38px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-fe {
	    top: 312px;
	    left: 724px;
	    width: 135px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.la-floresta {
	    top: 465px;
	    left: 359px;
	    width: 155px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.la-holanda {
	    top: 263px;
	    left: 514px;
	    width: 193px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-honda {
		top: 264px;
	    left: 284px;
	    width: 180px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.la-loma {
		top: 209px;
	    left: 274px;
	    width: 82px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-florida {
	    top: 406px;
	    left: 623px;
	    width: 158px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.la-lora {
	    top: 89px;
	    left: 142px;
	    width: 113px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-maravilla {
	    top: 70px;
	    left: 17px;
	    width: 59px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-nutria-caunzales{
	    top: 348px;
	    left: 156px;
	    width: 167px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.las-aguadas {
	    top: 33px;
	    left: 68px;
	    width: 59px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-veta {
		top: 5px;
	    left: 140px;
	    width: 42px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.los-yerbales {
	    top: 380px;
	    left: 523px;
	    width: 135px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.miraflores{
		top: 572px;
	    left: 427px;
	    width: 78px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.pailania {
	    top: 77px;
	    left: 23px;
	    width: 17px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.pocitos {
	    top: 350px;
	    left: 585px;
	    width: 93px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.rancho-largo {
	    top: 212px;
	    left: 118px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-agustin{
	    top: 634px;
	    left: 359px;
	    width: 131px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-isidro {
	    top: 128px;
	    left: 81px;
	    width: 55px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.san-pedro-buenos-aires{
		top: 318px;
	    left: 418px;
	    width: 152px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.san-rafael {
	    top: 282px;
	    left: 446px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.santa-isabel{
	    top: 84px;
	    left: 156px;
	    width: 46px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(1304deg) brightness(118%) contrast(119%);
	}
	.zona-urbana {
	    top: 104px;
	    left: 64px;
	    width: 15px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
