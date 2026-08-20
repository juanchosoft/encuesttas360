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
	.agua-mala{
		top: 318px;
	    left: 408px;
	    width: 158px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);		
	}
	.alto-colorado {
		top: 374px;
	    left: 525px;
	    width: 97px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-calvario {
	    top: 387px;
	    left: 331px;
	    width: 75px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.el-cedral {
		top: 389px;
	    left: 599px;
	    width: 76px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(356deg) brightness(118%) contrast(119%);
	}
	.buenos-aires-parte-alta {
	    top: 418px;
	    left: 614px;
	    width: 103px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.buenos-aires-parte-baja {
	    top: 454px;
	    left: 541px;
	    width: 181px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-chocho {
		top: 29px;
	    left: 587px;
	    width: 228px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-golfo {
	    top: 664px;
	    left: 428px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-guaico {
	    top: 579px;
	    left: 458px;
	    width: 174px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(364deg) brightness(119%) contrast(119%);
	}
	.el-guasimo{
		top: 374px;
	    left: 84px;
	    width: 107px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-mestizo {
	    top: 42px;
	    left: 408px;
	    width: 152px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-rincon {
	    top: 233px;
	    left: 318px;
	    width: 161px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-ruano {
	    top: 160px;
	    left: 618px;
	    width: 128px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.la-cienaga {
		top: 396px;
	    left: 420px;
	    width: 195px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-clara-arriba{
		top: 313px;
	    left: 493px;
	    width: 226px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.las-estancias {
	    top: 452px;
	    left: 370px;
	    width: 127px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.llano-arriba {
	    top: 564px;
	    left: 662px;
	    width: 56px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.llanos-de-san-juan {
	    top: 483px;
	    left: 350px;
	    width: 124px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.llanos-de-aguirre{
	    top: 178px;
	    left: 316px;
	    width: 74px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);	
	}
	.loma-hermosa {
	    top: 228px;
	    left: 149px;
	    width: 226px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.los-alticos {
	    top: 144px;
	    left: 517px;
	    width: 151px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.los-cedros {
	    top: 10px;
	    left: 480px;
	    width: 232px;
	    filter: invert(65%) sepia(89%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-cordillera{
		top: 173px;
	    left: 661px;
	    width: 48px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.matasano {
	    top: 489px;
	    left: 474px;
	    width: 237px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(1206deg) brightness(88%) contrast(119%);
	}
	.mestizal {
	    top: 674px;
	    left: 391px;
	    width: 97px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(829deg) brightness(118%) contrast(200%);
	}
	.monte-frio {
	    top: 556px;
	    left: 555px;
	    width: 151px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.los-guayabos {
	    top: 202px;
	    left: 446px;
	    width: 197px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.murrapala {
	    top: 629px;
	    left: 415px;
	    width: 112px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.palo-blanco {
	    top: 190px;
	    left: 529px;
	    width: 145px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.pantanillo{
		top: 425px;
	    left: 373px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1072deg) brightness(100%) contrast(80%);
	}
	.pesquinal {
	    top: 593px;
	    left: 337px;
	    width: 95px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.pie-de-cuesta {
	    top: 380px;
	    left: 391px;
	    width: 102px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.piedra-negra {
		top: 617px;
	    left: 383px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(159deg) brightness(100%) contrast(80%);
	}
	.poleal {
	    top: 220px;
	    left: 613px;
	    width: 151px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2229deg) brightness(118%) contrast(200%);
	}	
	.quimbayito {
	    top: 338px;
	    left: 320px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.quimbayo {
	    top: 126px;
	    left: 411px;
	    width: 175px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.rio-verde{
	    top: 248px;
	    left: 226px;
	    width: 164px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.tafetanes {
	    top: 98px;
	    left: 206px;
	    width: 242px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.zona-urbana {
		top: 328px;
	    left: 336px;
	    width: 53px;
	    z-index: 999;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%);
	}
</style>
