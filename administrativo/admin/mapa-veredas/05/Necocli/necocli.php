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
	    width: 1000px;
	    height: 1000px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	}
	.agua-clara{
	    top: 798px;
	    left: 421px;
	    width: 69px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.algodon-abajo {
	    top: 654px;
	    left: 671px;
	    width: 46px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.algodon-arriba {
	    top: 632px;
	    left: 710px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.brisas-del-rio {
	    top: 639px;
	    left: 583px;
	    width: 30px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.almacigo-abajo {
	    top: 280px;
	    left: 322px;
	    width: 90px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.almacigo-arriba {
	    top: 396px;
	    left: 365px;
	    width: 51px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.alto-carito {
	    top: 531px;
	    left: 468px;
	    width: 111px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.alto-de-rosario {
	    top: 330px;
	    left: 598px;
	    width: 30px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.ampe {
	    top: 645px;
	    left: 425px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.arizal {
	    top: 406px;
	    left: 313px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.barro-arriba {
	    top: 691px;
	    left: 517px;
	    width: 75px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.bobal-carito {
	    top: 581px;
	    left: 468px;
	    width: 78px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.botijuela {
	    top: 454px;
	    left: 393px;
	    width: 87px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.cabanas{
	    top: 210px;
	    left: 168px;
	    width: 218px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.calle-larga{
		top: 232px;
	    left: 440px;
	    width: 109px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}	
	.caiman-nuevo {
	    top: 859px;
	    left: 322px;
	    width: 184px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.caiman-viejo {
	    top: 762px;
	    left: 348px;
	    width: 74px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.calducho {
	    top: 199px;
	    left: 329px;
	    width: 98px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.caribia{
	    top: 410px;
	    left: 454px;
	    width: 140px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.carlo-abajo {
	    top: 656px;
	    left: 356px;
	    width: 53px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.casa-blanca {
	    top: 699px;
	    left: 348px;
	    width: 44px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.cienaga-mulaticos {
		top: 451px;
	    left: 792px;
	    width: 96px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.corcobado-abajo {
	    top: 473px;
	    left: 550px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.el-barro-abajo {
	    top: 723px;
	    left: 460px;
	    width: 69px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.el-bejuco {
		top: 257px;
	    left: 357px;
	    width: 73px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-bobal{
	    top: 642px;
	    left: 356px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.el-caballo {
	    top: 367px;
	    left: 175px;
	    width: 91px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-carlos {
	    top: 689px;
	    left: 392px;
	    width: 44px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-carreto {
	    top: 208px;
	    left: 541px;
	    width: 107px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-cativo {
	    top: 365px;
	    left: 634px;
	    width: 62px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-cerro {
	    top: 233px;
	    left: 392px;
	    width: 148px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.el-chejal {
		top: 439px;
	    left: 528px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.el-comejen {
	    top: 642px;
	    left: 465px;
	    width: 97px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.el-gorgojito {
	    top: 597px;
	    left: 699px;
	    width: 54px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.el-mellito-alto {
	    top: 308px;
	    left: 476px;
	    width: 130px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.el-reparo {
	    top: 489px;
	    left: 750px;
	    width: 55px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.guacamaya {
	    top: 364px;
	    left: 392px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.el-retiro {
	    top: 180px;
	    left: 466px;
	    width: 74px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-tigre {
	    top: 777px;
	    left: 472px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-totumo {
	    top: 778px;
	    left: 350px;
	    width: 77px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.el-vale-pavas {
	    top: 493px;
	    left: 330px;
	    width: 105px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.el-venao-sevilla {
	    top: 571px;
	    left: 412px;
	    width: 70px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.el-volao{
		top: 370px;
	    left: 724px;
	    width: 43px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.gariton {
	    top: 306px;
	    left: 402px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.giganton {
	    top: 59px;
	    left: 579px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.iguana-central {
	    top: 191px;
	    left: 583px;
	    width: 75px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.iguana-porvenir {
	    top: 203px;
	    left: 626px;
	    width: 89px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.iguanita{
	    top: 214px;
	    left: 534px;
	    width: 49px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.la-cana {
		top: 714px;
	    left: 375px;
	    width: 69px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.la-ceibita {
	    top: 822px;
	    left: 345px;
	    width: 93px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.la-cenizosa {
	    top: 664px;
	    left: 578px;
	    width: 59px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.la-comarca {
	    top: 564px;
	    left: 565px;
	    width: 40px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.la-culebriada {
	    top: 640px;
	    left: 606px;
	    width: 79px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(276deg) brightness(118%) contrast(119%);
	}
	.la-escoba {
		top: 602px;
	    left: 369px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(29deg) brightness(118%) contrast(200%);
	}
	.la-magdalena {
	    top: 421px;
	    left: 667px;
	    width: 40px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-merced {
		top: 270px;
	    left: 493px;
	    width: 79px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.la-olga {
		top: 749px;
	    left: 416px;
	    width: 71px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-puya{
	    top: 568px;
	    left: 900px;
	    width: 80px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.la-salada {
	    top: 441px;
	    left: 693px;
	    width: 41px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.las-changas {
	    top: 350px;
	    left: 682px;
	    width: 62px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.las-palmeras {
	    top: 510px;
	    left: 796px;
	    width: 101px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.laureles {
	    top: 378px;
	    left: 571px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(271deg) brightness(100%) contrast(80%);
	}
	.la-yoki-cenizosa{
		top: 754px;
	    left: 500px;
	    width: 76px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.lechugal {
	    top: 244px;
	    left: 6px;
	    width: 225px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.limoncito {
	    top: 488px;
	    left: 474px;
	    width: 93px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.loma-de-piedra {
		top: 599px;
	    left: 532px;
	    width: 43px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.los-mulatos {
	    top: 188px;
	    left: 404px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.los-naranjos {
	    top: 146px;
	    left: 597px;
	    width: 58px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.marimonda-el-cerro {
	    top: 312px;
	    left: 197px;
	    width: 103px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(182deg) brightness(88%) contrast(119%);
	}
	.marimonda-mulatos {
	    top: 320px;
	    left: 237px;
	    width: 115px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.mello-villavicencio {
	    top: 509px;
	    left: 696px;
	    width: 80px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.miramar {
		top: 596px;
	    left: 596px;
	    width: 122px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.moncholo{
	    top: 469px;
	    left: 325px;
	    width: 95px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(2029deg) brightness(118%) contrast(200%);
	}
	.mulaticos-la-fe {
	    top: 398px;
	    left: 769px;
	    width: 68px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.mulaticos-la-union {
	    top: 414px;
	    left: 684px;
	    width: 60px;
	    filter: invert(48%) sepia(89%) saturate(976%) hue-rotate(1338deg) brightness(100%) contrast(80%);
	}
	.mulaticos-palestina {
	    top: 377px;
	    left: 724px;
	    width: 64px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.mulaticos-piedrecitas {
	    top: 419px;
	    left: 622px;
	    width: 51px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(1339deg) brightness(100%) contrast(80%);
	}
	.nueva-esperanza {
	    top: 557px;
	    left: 749px;
	    width: 100px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(2106deg) brightness(88%) contrast(119%);
	}
	.nueva-luz {
		top: 837px;
	    left: 486px;
	    width: 28px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.patillal {
	    top: 694px;
	    left: 938px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.piedrecita{
	    top: 428px;
	    left: 441px;
	    width: 55px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(6deg) brightness(118%) contrast(119%);
	}
	.pitamorral {
	    top: 261px;
	    left: 672px;
	    width: 64px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.pueblo-nuevo {
	    top: 612px;
	    left: 516px;
	    width: 93px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.punta-gorda {
	    top: 159px;
	    left: 494px;
	    width: 96px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.rio-necocli {
	    top: 474px;
	    left: 233px;
	    width: 128px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.san-isidro {
	    top: 499px;
	    left: 642px;
	    width: 78px;
	    filter: invert(48%) sepia(537%) saturate(1476%) hue-rotate(350deg) brightness(118%) contrast(19%);
	}	
	.san-joaquin {
	    top: 323px;
	    left: 452px;
	    width: 53px;
	    filter: invert(48%) sepia(79%) saturate(456%) hue-rotate(267deg) brightness(100%) contrast(50%);
	}	
	.san-jose-de-mulatos{
	    top: 646px;
	    left: 845px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(2938deg) brightness(100%) contrast(80%);
	}
	.san-sebastian {
	    top: 513px;
	    left: 258px;
	    width: 90px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.santa-cerro {
	    top: 658px;
	    left: 772px;
	    width: 37px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.santa-cruz-del-cerro {
		top: 627px;
	    left: 795px;
	    width: 87px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(1534deg) brightness(100%) contrast(50%);
	}	
	.santa-rosa {
	    top: 496px;
	    left: 539px;
	    width: 76px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.santa-rosa-de-los-palmares {
	    top: 452px;
	    left: 721px;
	    width: 62px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(241deg) brightness(118%) contrast(119%);
	}
	.santa-rosa-de-puya{
	    top: 682px;
	    left: 887px;
	    width: 61px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.sucio-arriba {
	    top: 316px;
	    left: 617px;
	    width: 74px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.tulapita {
	    top: 716px;
	    left: 577px;
	    width: 68px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.umbito {
	    top: 581px;
	    left: 831px;
	    width: 107px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.vale-adentro {
		top: 515px;
	    left: 409px;
	    width: 68px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.vara-santa {
	    top: 473px;
	    left: 696px;
	    width: 67px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.vena-de-palma {
		top: 447px;
	    left: 610px;
	    width: 108px;
	    filter: invert(150%) sepia(109%) saturate(511%) hue-rotate(18046deg) brightness(118%) contrast(19%);
	}
	.villa-nueva {
	    top: 540px;
	    left: 585px;
	    width: 120px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.virgen-del-cobre {
	    top: 561px;
	    left: 306px;
	    width: 108px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.yoky-machena {
	    top: 753px;
	    left: 559px;
	    width: 68px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.zapata {
	    top: 123px;
	    left: 548px;
	    width: 58px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.zapatica{
	    top: 125px;
	    left: 589px;
	    width: 66px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
</style>
