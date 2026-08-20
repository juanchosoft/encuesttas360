<!DOCTYPE html>
<html lang="es">
<head>
  <title>Mapa Vereda</title>
  <meta charset="UTF-8">
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
	.aguas-frias{
	    top: 504px;
	    left: 217px;
	    width: 117px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);

	}
	.altavista-central {
	    top: 534px;
	    left: 292px;
	    width: 110px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.barro-blanco {
	    top: 453px;
	    left: 822px;
	    width: 75px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.el-picacho {
	    top: 253px;
	    left: 445px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(41deg) brightness(118%) contrast(119%);
	}
	.buga-patio-bonito {
	    top: 552px;
	    left: 239px;
	    width: 78px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.el-astillero{
	    top: 359px;
	    left: 95px;
	    width: 133px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
		
	}
	.boqueron {
	    top: 168px;
	    left: 184px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.el-carmelo {
	    top: 252px;
	    left: 403px;
	    width: 56px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-cerro {
	    top: 626px;
	    left: 792px;
	    width: 65px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);
	}	
	.el-corazon-el-morro {
	    top: 429px;
	    left: 200px;
	    width: 160px;
	    filter: invert(48%) sepia(37%) saturate(176%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.el-jardin {
	    top: 605px;
	    left: 298px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}	
	.el-llano {
	    top: 270px;
	    left: 246px;
	    width: 48px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}	
	.el-llano-santa-elena {
	    top: 618px;
	    left: 762px;
	    width: 57px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(20deg) brightness(100%) contrast(80%);
	}
	.el-patio {
	    top: 321px;
	    left: 135px;
	    width: 144px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.el-placer{
		top: 526px;
	    left: 779px;
	    width: 63px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(209deg) brightness(100%) contrast(80%);
	}
	.el-uvito{
	    top: 297px;
	    left: 175px;
	    width: 88px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(309deg) brightness(100%) contrast(80%);
	}	
	.el-plan {
		top: 588px;
	    left: 669px;
	    width: 99px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.el-retiro {
	    top: 477px;
	    left: 102px;
	    width: 59px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.el-salado {
	    top: 534px;
	    left: 127px;
	    width: 122px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-aldea{
	    top: 106px;
	    left: 41px;
	    width: 70px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-esperanza {
	    top: 565px;
	    left: 310px;
	    width: 88px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.la-florida {
	    top: 720px;
	    left: 140px;
	    width: 83px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(846deg) brightness(118%) contrast(119%);
	}
	.la-frisola {
	    top: 134px;
	    left: 27px;
	    width: 135px;
	    filter: invert(5%) sepia(10%) saturate(976%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-ilusion {
	    top: 233px;
	    left: 297px;
	    width: 81px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(175deg) brightness(118%) contrast(119%);
	}
	.la-loma {
		top: 378px;
	    left: 254px;
	    width: 80px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-palma {
		top: 358px;
	    left: 135px;
	    width: 160px;
	    filter: invert(15%) sepia(19%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.la-playas{
	    top: 330px;
	    left: 211px;
	    width: 74px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(317deg) brightness(100%) contrast(80%);
	}
	.las-palmas {
	    top: 540px;
	    left: 575px;
	    width: 154px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.la-sucia {
	    top: 57px;
	    left: 7px;
	    width: 53px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.la-suiza {
	    top: 166px;
	    left: 8px;
	    width: 117px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.la-cuchilla {
		top: 256px;
	    left: 177px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.la-verde {
	    top: 617px;
	    left: 211px;
	    width: 128px;
	    filter: invert(45%) sepia(69%) saturate(276%) hue-rotate(1846deg) brightness(118%) contrast(119%);
	}
	.mazo {
	    top: 417px;
	    left: 735px;
	    width: 108px;
	    filter: invert(48%) sepia(89%) saturate(1076%) hue-rotate(339deg) brightness(100%) contrast(80%);
	}
	.media-luna {
	    top: 431px;
	    left: 679px;
	    width: 109px;
	    filter: invert(78%) sepia(99%) saturate(96%) hue-rotate(206deg) brightness(88%) contrast(119%);
	}
	.montanita {
	    top: 632px;
	    left: 126px;
	    width: 86px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(338deg) brightness(100%) contrast(80%);
	}
	.pajarito {
	    top: 293px;
	    left: 375px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(76%) hue-rotate(146deg) brightness(100%) contrast(80%);
	}
	.pedregal-alto {
		top: 310px;
	    left: 289px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.santa-elena-central {
	    top: 586px;
	    left: 756px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}	
	.naranjal{
		top: 233px;
	    left: 181px;
	    width: 72px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(14deg) brightness(119%) contrast(119%);
	}	
	.piedras-blancas {
		top: 280px;
	    left: 638px;
	    width: 211px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%);
	}
	.potrera-miserenga {
	    top: 50px;
	    left: 45px;
	    width: 162px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.potrerito {
		top: 664px;
	    left: 130px;
	    width: 101px;
	    filter: invert(8%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%);
	}
	.san-jose-de-la-montana{
	    top: 202px;
	    left: 267px;
	    width: 72px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(3038deg) brightness(100%) contrast(80%);
	}
	.san-jose-de-manzanillo {
	    top: 579px;
	    left: 369px;
	    width: 61px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(118deg) brightness(100%) contrast(80%);
	}
	.san-pablo {
	    top: 497px;
	    left: 264px;
	    width: 126px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(56deg) brightness(100%) contrast(80%);
	}
	.travesias {
	    top: 299px;
	    left: 259px;
	    width: 45px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}
	.sector-central {
		top: 76px;
	    left: 78px;
	    width: 173px;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(1325deg) brightness(118%) contrast(119%);
	}	
	.urquita {
	    top: 24px;
	    left: 12px;
	    width: 209px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}
	.san-antonio-de-prado{
		top: 670px;
	    left: 203px;
	    width: 111px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.volcana-guayabal {
	    top: 136px;
	    left: 77px;
	    width: 143px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(80deg) brightness(88%) contrast(119%);
	}
	.piedra-gorda {
	    top: 485px;
	    left: 771px;
	    width: 77px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(172deg) brightness(100%) contrast(80%);
	}
	.san-vicente {
	    top: 242px;
	    left: 273px;
	    width: 47px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.santo-domingo {
		top: 314px;
	    left: 419px;
	    width: 93px;
	    filter: invert(480%) sepia(109%) saturate(116%) hue-rotate(918deg) brightness(818%) contrast(30%);
	}
	.santa-rita {
	    top: 700px;
	    left: 451px;
	    width: 95px;
	    filter: invert(15%) sepia(19%) saturate(876%) hue-rotate(2276deg) brightness(118%) contrast(119%);
	}
	.santa-cruz{
	    top: 353px;
	    left: 423px;
	    width: 58px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(126deg) brightness(118%) contrast(200%);		
	}
	.santa-barbara {
	    top: 217px;
	    left: 458px;
	    width: 73px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(229deg) brightness(118%) contrast(200%);
	}
	.san-martin{
	    top: 642px;
	    left: 592px;
	    width: 57px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%);
	}
	.san-lorenzo {
		top: 257px;
	    left: 542px;
	    width: 129px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(158deg) brightness(100%) contrast(80%);
	}
	.san-juan {
	    top: 153px;
	    left: 388px;
	    width: 49px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(834deg) brightness(100%) contrast(80%);
	}
	.san-jose {
	    top: 741px;
	    left: 210px;
	    width: 57px;
	    filter: invert(48%) sepia(79%) saturate(176%) hue-rotate(198deg) brightness(100%) contrast(150%);
	}
	.yolombo {
	    top: 234px;
	    left: 343px;
	    width: 92px;
	    filter: invert(48%) sepia(89%) saturate(1276%) hue-rotate(138deg) brightness(100%) contrast(80%);
	}
	.yarumalito {
		top: 376px;
	    left: 39px;
	    width: 138px;
	    filter: invert(48%) sepia(19%) saturate(876%) hue-rotate(841deg) brightness(118%) contrast(119%);
	}	
	.zona-urbana{
		top: 243px;
	    left: 271px;
	    width: 473px;
	    filter: invert(73%) sepia(89%) saturate(973%) hue-rotate(164deg) brightness(119%) contrast(89%);
	}
	
</style>
