<?php
// Ejemplo mínimo: solo Altamira con color verde
$colores_veredas = [
    ['nombre_vereda' => 'Altamira', 'color_calculado' => '#00ff00'] // verde
];
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const colores = <?php echo json_encode($colores_veredas); ?>;
    colores.forEach(item => {
        const clase = item.nombre_vereda.toLowerCase().replace(/\s+/g, '-'); // altamira
        const color = item.color_calculado;
        const el = document.querySelector('#mapa img.' + clase);
        if (el) {
            el.style.filter = `drop-shadow(0 0 2px ${color}) brightness(1.2) saturate(2)`;
        } else {
            console.warn("Vereda no encontrada:", clase);
        }
    });
});
</script>

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
	    width: 800px;
	    height: 800px;
	    margin: 0 auto;
	}
	#mapa img {
	    position: absolute;
	    filter: invert(48%) sepia(19%) saturate(2476%) hue-rotate(146deg) brightness(118%) contrast(119%);
	}
	.la-loma {
	    top: 214px;
	    left: 64px;
	    width: 109px;
	}
	.la-loma-parte-baja {
	    top: 246px;
	    left: 51px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}
	.la-pena {
	    top: 243px;
	    left: 128px;
	    width: 80px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(146deg) brightness(88%) contrast(119%) ;
	}		
	.alto-bonito {
	    top: 212px;
	    left: 151px;
	    width: 55px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}	
	.los-rastrojos {
	    top: 233px;
	    left: 178px;
	    width: 57px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(846deg) brightness(118%) contrast(119%) ;
	}	
	.la-cascada {
	    top: 184px;
	    left: 192px;
	    width: 85px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1846deg) brightness(100%) contrast(80%) ;
	}	
	.el-morron {
	    top: 124px;
	    left: 226px;
	    width: 66px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(1446deg) brightness(100%) contrast(80%) ;
	}	
	.la-saltadera {
	    top: 90px;
	    left: 191px;
	    width: 62px;
	    filter: invert(78%) sepia(99%) saturate(996%) hue-rotate(206deg) brightness(88%) contrast(119%) ;
	}
	.altamira {
	    top: 77px;
	    left: 235px;
	    width: 60px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.el-guaico {
	    top: 1px;
	    left: 209px;
	    width: 125px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.san-pedro {
	    top: 98px;
	    left: 243px;
	    width: 76px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%) ;
	}	
	.el-buey-colmenas {
	    top: 100px;
	    left: 303px;
	    width: 133px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.santa-catalina {
	    top: 148px;
	    left: 312px;
	    width: 122px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}	
	.quebradanegra {
	    top: 135px;
	    left: 405px;
	    width: 130px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}	
	.san-luis {
	    top: 177px;
	    left: 260px;
	    width: 88px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}		
	.la-victoria {
	    top: 228px;
	    left: 232px;
	    width: 133px;
	    filter: invert(13%) sepia(59%) saturate(473%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}		
	.el-chagualo {
	    top: 220px;
	    left: 289px;
	    width: 142px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.el-buey {
	    top: 168px;
	    left: 518px;
	    width: 82px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}	
	.la-labor {
	    top: 273px;
	    left: 339px;
	    width: 141px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(215deg) brightness(118%) contrast(119%) ;
	}	
	.la-cordillera {
	    top: 293px;
	    left: 435px;
	    width: 105px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(915deg) brightness(118%) contrast(119%) ;
	}
	.yarumal {
	    top: 227px;
	    left: 518px;
	    width: 130px;
	    filter: invert(48%) sepia(37%) saturate(476%) hue-rotate(1915deg) brightness(118%) contrast(119%) ;
	}
	.san-bartolo {
	    top: 185px;
	    left: 596px;
	    width: 107px;
	    filter: invert(48%) sepia(37%) saturate(476%) hue-rotate(165deg) brightness(118%) contrast(119%) ;
	}	
	.guayaquil {
	    top: 200px;
	    left: 676px;
	    width: 104px;
	    filter: invert(48%) sepia(57%) saturate(1876%) hue-rotate(104deg) brightness(118%) contrast(119%) ;
	}
	.el-silencio {
	    top: 246px;
	    left: 684px;
	    width: 115px;
	    filter: invert(100%) sepia(33%) saturate(976%) hue-rotate(263deg) brightness(118%) contrast(119%) ;
	}	
	.aures-arriba {
	    top: 306px;
	    left: 557px;
	    width: 148px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(304deg) brightness(119%) contrast(119%) ;
	}
	.el-carrizal{
	    top: 228px;
	    right: 169px;
	    width: 96px;
	    filter: invert(30%) sepia(388%) saturate(3002%) hue-rotate(80deg) brightness(945%) contrast(60%) ;
	}	
	.carrizales {
	    top: 435px;
	    right: 197px;
	    width: 114px;
	    filter: invert(30%) sepia(388%) saturate(3002%) hue-rotate(80deg) brightness(945%) contrast(60%) ;
	}
	.quebradona-abajo {
	    top: 367px;
	    right: 263px;
	    width: 63px;
	    filter: invert(48%) sepia(19%) saturate(76%) hue-rotate(8846deg) brightness(118%) contrast(119%) ;
	}	 
	.quebradona-arriba {
	    top: 349px;
	    right: 182px;
	    width: 103px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(146deg) brightness(88%) contrast(119%) ;
	}
	.combia {
	    top: 202px;
	    right: 183px;
	    width: 59px;
	    filter: invert(30%) sepia(388%) saturate(3002%) hue-rotate(80deg) brightness(945%) contrast(60%) ;
	}
	.piedra-candela {
	    top: 327px;
	    left: 199px;
	    width: 161px;
	    filter: invert(30%) sepia(388%) saturate(3002%) hue-rotate(80deg) brightness(945%) contrast(60%) ;
	}			
	.portugal {
	    top: 300px;
	    left: 110px;
	    width: 113px;
	    filter: invert(48%) sepia(19%) saturate(76%) hue-rotate(8846deg) brightness(118%) contrast(119%) ;
	}	
	.santa-ana {
	    top: 358px;
	    left: 132px;
	    width: 79px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}	
	.chagualal {
	    top: 383px;
	    left: 64px;
	    width: 125px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(304deg) brightness(119%) contrast(119%) ;
	}
	.naranjal {
	    top: 445px;
	    left: 37px;
	    width: 158px;
	    filter: invert(30%) sepia(388%) saturate(3002%) hue-rotate(80deg) brightness(945%) contrast(60%) ;
	}	
	.el-erizo {
	    top: 405px;
	    left: 363px;
	    width: 124px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(146deg) brightness(88%) contrast(119%) ;
	}
	.quebradanegra {
	    top: 135px;
	    left: 405px;
	    width: 130px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}
	.san-vicente {
	    top: 383px;
	    left: 146px;
	    width: 66px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(50%) ;
	}	
	.san-jose {
	    top: 295px;
	    left: 206px;
	    width: 43px;
	    filter: invert(4%) sepia(1%) saturate(976%) hue-rotate(16deg) brightness(118%) contrast(50%) ;
	}
	.la-polka {
	    top: 465px;
	    left: 440px;
	    width: 56px;
	    filter: invert(4%) sepia(1%) saturate(976%) hue-rotate(16deg) brightness(118%) contrast(50%) ;
	}
	.canaveral {
	    top: 293px;
	    left: 49px;
	    width: 94px;
	    filter: invert(4%) sepia(1%) saturate(976%) hue-rotate(16deg) brightness(118%) contrast(50%) ;
	}
	.morro-gordo {
	    top: 327px;
	    left: 17px;
	    width: 87px;
	    filter: invert(13%) sepia(59%) saturate(473%) hue-rotate(64deg) brightness(119%) contrast(119%) ;
	}
	.llanadas {
	    top: 467px;
	    left: 2px;
	    width: 136px;
	    filter: invert(133%) sepia(59%) saturate(2073%) hue-rotate(553deg) brightness(119%) contrast(119%) ;
	}
	.primavera {
	    top: 407px;
	    left: 23px;
	    width: 48px;
	    filter: invert(100%) sepia(33%) saturate(976%) hue-rotate(104deg) brightness(118%) contrast(59%) ;
	}
	.guayabal {
	    top: 650px;
	    left: 172px;
	    width: 150px;
	    filter: invert(100%) sepia(33%) saturate(976%) hue-rotate(104deg) brightness(118%) contrast(59%) ;
	}	
	.purima {
	    top: 584px;
	    left: 284px;
	    width: 92px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}
	.llanogrande {
	    top: 513px;
	    left: 37px;
	    width: 101px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(146deg) brightness(118%) contrast(119%) ;
	}	
	.monteloro {
	    top: 505px;
	    left: 116px;
	    width: 85px;
	    filter: invert(48%) sepia(19%) saturate(276%) hue-rotate(26deg) brightness(118%) contrast(19%) ;
	}	
	.pantano-negro {
	    top: 433px;
	    left: 186px;
	    width: 100px;
	    filter: invert(48%) sepia(19%) saturate(976%) hue-rotate(46deg) brightness(118%) contrast(50%) ;
	}
	.el-vesubio {
	    top: 579px;
	    left: 87px;
	    width: 96px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(215deg) brightness(118%) contrast(119%) ;
	}
	.corinto {
	    top: 592px;
	    left: 160px;
	    width: 54px;
	    filter: invert(48%) sepia(19%) saturate(996%) hue-rotate(146deg) brightness(88%) contrast(119%) ;
	}	
	.san-bernardo {
	    top: 501px;
	    left: 195px;
	    width: 71px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(112deg) brightness(100%) contrast(80%) ;
	}	
	.la-perdida {
	    top: 576px;
	    left: 191px;
	    width: 98px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}		
	.el-volcan {
	    top: 541px;
	    left: 250px;
	    width: 82px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(215deg) brightness(118%) contrast(119%) ;
	}	
	.el-granadillo {
	    top: 526px;
	    left: 304px;
	    width: 86px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(304deg) brightness(119%) contrast(119%) ;
	}
	.la-nubia {
	    top: 524px;
	    left: 355px;
	    width: 47px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(174deg) brightness(119%) contrast(119%) ;
	}
	.el-carmelo {
	    top: 491px;
	    left: 372px;
	    width: 50px;
	    filter: invert(133%) sepia(59%) saturate(973%) hue-rotate(1904deg) brightness(119%) contrast(59%) ;
	}		
	.la-esperanza {
	    top: 449px;
	    left: 312px;
	    width: 82px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(215deg) brightness(118%) contrast(119%) ;
	}	
	.la-circita {
	    top: 487px;
	    left: 256px;
	    width: 94px;
	    filter: invert(48%) sepia(89%) saturate(276%) hue-rotate(334deg) brightness(100%) contrast(80%) ;
	}
	.la-samaria {
	    top: 487px;
	    left: 218px;
	    width: 51px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}
	.betulia {
	    top: 456px;
	    left: 260px;
	    width: 60px;
	    filter: invert(48%) sepia(37%) saturate(876%) hue-rotate(304deg) brightness(118%) contrast(119%) ;
	}
	.alto-de-letras {
	    top: 436px;
	    left: 279px;
	    width: 72px;
	    filter: invert(100%) sepia(104%) saturate(4526%) hue-rotate(1259deg) brightness(118%) contrast(119%) ;
	}
	.sotayac {
		z-index: 999;
	    top: 647px;
	    left: 238px;
	    width: 40px;
	    filter: invert(100%) sepia(104%) saturate(10526%) hue-rotate(49deg) brightness(118%) contrast(119%) ;
	}	
	.mata-de-guadua {
	    top: 636px;
	    left: 224px;
	    width: 66px;
	    filter: invert(130%) sepia(109%) saturate(1973%) hue-rotate(7deg) brightness(119%) contrast(119%) ;
	}
	.zona-urbana {
	    top: 418px;
	    left: 343px;
	    width: 30px;
	    filter: invert(13%) sepia(102%) saturate(50%) hue-rotate(5000deg) brightness(1%) contrast(100%) ;
	}	
	.la-florida {
	    top: 445px;
	    left: 121px;
	    width: 69px;
	    filter: invert(130%) sepia(109%) saturate(273%) hue-rotate(0deg) brightness(119%) contrast(119%) ;
	}			
</style>


