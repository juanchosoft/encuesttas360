<?php

// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
$codigo_departamento = '';
$codigoMunicipioConfiguracion = '';
$pilarConfiguracion = '';
$GOOGLE_MAPS_API_KEY = '';

$configIniPath = __DIR__ . '/../../config.ini';
if (is_file($configIniPath)) {
  $cfgIni = parse_ini_file($configIniPath, true);
  if (is_array($cfgIni)) {
    $GOOGLE_MAPS_API_KEY = trim((string)(($cfgIni['google_maps']['api_key'] ?? '')));
  }
}

if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
  $codigo_departamento = $configuracionAplicacion[0]['codigo_departamento'] ?? '';
  $codigoMunicipioConfiguracion = $configuracionAplicacion[0]['codigo_municipio'] ?? '';
  $pilarConfiguracion = $configuracionAplicacion[0]['tbl_pilar_id'] ?? '';
}
?>


<input type="hidden"  value="<?php echo $codigo_departamento; ?>" id="departamentoConfiguracionInput" name="departamentoConfiguracionInput">
