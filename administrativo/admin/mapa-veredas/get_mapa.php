<?php
include_once './../classes/DbConection.php';
include_once './../classes/Util.php';
include './../classes/Estado.php';
include './../db/colores.php';

function getUrl($urlMapa)
{
  $port = $_SERVER["SERVER_PORT"];

  $nameServer = $port != "80" ? $_SERVER['SERVER_NAME'] . ":" . $port : $_SERVER['SERVER_NAME'];

  $url = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $nameServer,
    $_SERVER['REQUEST_URI']
  );

  $url = str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);

  $partsUrl = explode("/", $urlMapa);
  unset($partsUrl[(count($partsUrl) - 1)]);
  $url .= $partsUrl[1] . "/";
  return $url;
}


$webroot = getUrl($_REQUEST["url"]);

$vereda = isset($_REQUEST["options"]["vereda"]) ? $_REQUEST["options"]["vereda"] : null;

$puntaje = isset($_REQUEST["puntaje"]) ? $_REQUEST["puntaje"] : 0; // ESTE PUNTAJE ES EL QUE SE CALCULÓ DE LA NUEVA FORMA (municipio)

$veredas = Estado::getDataVeredas($_REQUEST["options"]["codigo_departamento"], $_REQUEST["options"]["codigo_muncipio"], $vereda, $puntaje);

$codeDept = $_REQUEST["options"]["codigo_departamento"];
$codeMun = $_REQUEST["options"]["codigo_muncipio"];

if (!empty($veredas['cantidades'])) 
  $arr =   $veredas['cantidades']['output']['response'];
  $arr2021 =   $veredas['cantidades2021']['output']['response'];
  $isvalid = $veredas['cantidades']['output']['valid'];
?>


  <div class="row">
    <div class="col-sm-6">
      <div class="form-horizontal">
        <div class="col-md-6 offset-md-4 mt-3 mb-4">
          <div class="card-body p-0">
            <table class="table table-sm">
              <div class="text-center">
                <h5 class="titu-Consolidado mb-0">CANTIDAD VEREDAS 2021</h5>
              </div>
              <tbody>
                <?php                
                $c = count($arr2021);
                if ($isvalid) {
                  for ($i = 0; $i < $c; $i++) {
                    if ($arr2021[$i]['color2021'] != "") { ?>
                      <tr>
                        <th bgcolor="<?php echo $arr2021[$i]['color2021']; ?>">
                        <th>
                          <span class="elementcolor" style="background-color: <?php echo $arr2021[$i]['color2021']; ?>"></span>
                          <?php echo $arr2021[$i]['CuentaDecolor']; ?> VEREDAS
                        </th>
                        <th> <a href="#" onclick="ESTADO_GENERAL.showDataMunicipio2021('<?php echo $arr2021[$i]['color2021']; ?>')" role="button" data-target="#dato_veredas" class="btn btn-xs  btn-primary btn-w-100p btn-mw-300" data-toggle="modal">Ver</a></th>
                        </center>
                      </tr>
                <?php
                    }
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-horizontal">
        <div class="col-md-6 offset-md-4 mt-3 mb-4">
          <div class="card-body p-0">
            <table class="table table-sm">
              <div class="text-center">
                <h5 class="titu-Consolidado mb-0">CANTIDAD VEREDAS 2022</h5>
              </div>
              <tbody>
                <?php
                $c = count($arr);
                if ($isvalid) {
                  for ($i = 0; $i < $c; $i++) {
                    if ($arr[$i]['color'] != "") { ?>
                      <tr>
                        <th bgcolor="<?php echo $arr[$i]['color']; ?>">
                        <th>
                          <span class="elementcolor" style="background-color: <?php echo $arr[$i]['color']; ?>"></span>
                          <?php echo $arr[$i]['CuentaDecolor']; ?> VEREDAS
                        </th>
                        <th> <a href="#" onclick="ESTADO_GENERAL.showDataMunicipio('<?php echo $arr[$i]['color']; ?>')" role="button" data-target="#dato_veredas" class="btn btn-xs  btn-primary btn-w-100p btn-mw-300" data-toggle="modal">Ver</a></th>
                        </center>
                      </tr>
                <?php
                    }
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php

include("../" . $_REQUEST["url"].".php");
?>

<style>
  <?php if (!is_null($vereda)) : ?>#mapa {
    height: 460px !important;
  }

  <?php endif ?>
</style>
