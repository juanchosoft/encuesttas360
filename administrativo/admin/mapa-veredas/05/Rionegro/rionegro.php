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
	.abreito{
	    top: 236px;
	    left: 376px;
	    width: 83px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.abreo {
	    top: 234px;
	    left: 422px;
	    width: 117px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.aeropuerto {
	    top: 147px;
	    left: 314px;
	    width: 95px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-rosal {
	    top: 378px;
	    left: 602px;
	    width: 89px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.cabeceras-de-llanogrande {
		top: 500px;
	    left: 182px;
	    width: 275px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.barro-blanco {
	    top: 330px;
	    left: 365px;
	    width: 128px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.chipre {
	    top: 385px;
	    left: 426px;
	    width: 132px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.cimarronas {
		top: 271px;
	    left: 613px;
	    width: 72px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.cuchillas-de-san-jose {
	    top: 150px;
	    left: 433px;
	    width: 131px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-capiro {
		top: 594px;
	    left: 296px;
	    width: 261px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.el-carmin {
		top: 141px;
	    left: 366px;
	    width: 108px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-higueron {
	    top: 647px;
	    left: 235px;
	    width: 116px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-tablazo{
	    top: 297px;
	    left: 122px;
	    width: 207px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-convencion{
	    top: 249px;
	    left: 224px;
	    width: 139px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(309deg) brightness(100%) contrast(80%);
	}	
	.chachafruto {
		top: 309px;
	    left: 376px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.galicia {
	    top: 164px;
	    left: 595px;
	    width: 111px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.guayabito {
	    top: 459px;
	    left: 161px;
	    width: 192px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-mosca {
	    top: 68px;
	    left: 478px;
	    width: 111px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-mosquita {
	    top: 108px;
	    left: 241px;
	    width: 87px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-quiebra {
	    top: 131px;
	    left: 79px;
	    width: 168px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.los-pinos {
		top: 79px;
	    left: 634px;
	    width: 186px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.mampuesto {
	    top: 88px;
	    left: 368px;
	    width: 137px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.playa-rica {
	    top: 147px;
	    left: 222px;
	    width: 130px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.rio-abajo {
	    top: 4px;
	    left: 646px;
	    width: 151px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-antonio{
	    top: 468px;
	    left: 446px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.san-luis {
	    top: 2px;
	    left: 552px;
	    width: 134px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.santa-ana {
	    top: 430px;
	    left: 544px;
	    width: 138px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.pontezuela{
	    top: 639px;
	    left: 323px;
	    width: 111px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-barbara{
		top: 69px;
	    left: 566px;
	    width: 112px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.santa-teresa {
	    top: 733px;
	    left: 454px;
	    width: 86px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.tablacito {
	    top: 406px;
	    left: 121px;
	    width: 173px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.tres-puertas {
		top: 396px;
	    left: 305px;
	    width: 160px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.vilachuaga {
	    top: 530px;
	    left: 407px;
	    width: 114px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.yarumal {
	    top: 179px;
	    left: 81px;
	    width: 192px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-laja {
	    top: 169px;
	    left: 515px;
	    width: 132px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
		top: 297px;
	    left: 715px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.san-lorenzo {
		top: 257px;
	    left: 542px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.zona-urbana{
		top: 295px;
	    left: 466px;
	    width: 223px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
