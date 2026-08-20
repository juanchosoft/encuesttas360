<?php
session_start();
/**
 * en este archivo se atienden todas las peticiones AJAX
 */
$rqst = $_REQUEST;
$op = isset($rqst['op']) ? $rqst['op'] : '';
header("Content-type: application/javascript; charset=utf-8");
header("Cache-Control: max-age=15, must-revalidate");
header('Access-Control-Allow-Origin: *');

include '../classes/DbConection.php';
include '../classes/Util.php';
include '../classes/SessionData.php';

switch ($op) {
  // Rutas para el módulo: EspacioGeografico
    case 'espacioGeograficoget':
        include '../classes/EspacioGeografico.php';
        echo json_encode(EspacioGeografico::getAll($rqst));
        break;

    case 'espacioGeograficosave':
        include '../classes/EspacioGeografico.php';
        echo json_encode(EspacioGeografico::save($rqst));
        break;

    case 'espacioGeograficoduplicate':
        include '../classes/EspacioGeografico.php';
        echo json_encode(EspacioGeografico::duplicate($rqst));
        break;

/*     case 'espacioGeograficodelete':
        include '../classes/EspacioGeografico.php';
        echo json_encode(EspacioGeografico::delete($rqst));
        break; */

    // Rutas para el módulo: Votantes
    case 'votantesget':
        include '../classes/Votantes.php';
        echo json_encode(Votantes::getAll($rqst));
        break;

    case 'votantessave':
        include '../classes/Votantes.php';
        echo json_encode(Votantes::save($rqst));
        break;

    case 'votantesdelete':
        include '../classes/Votantes.php';
        echo json_encode(Votantes::delete($rqst));
        break;

    case 'votantesavailable':
        include '../classes/Votantes.php';
        echo json_encode(Votantes::available($rqst));
        break;

    // Rutas para el módulo: FichaTecnicaEncuesta
    case 'fichaTecnicaEncuestaget':
        include '../classes/FichaTecnicaEncuesta.php';
        echo json_encode(FichaTecnicaEncuesta::getAll($rqst));
        break;

    case 'fichaTecnicaEncuestasave':
        include '../classes/FichaTecnicaEncuesta.php';
        echo json_encode(FichaTecnicaEncuesta::save($rqst));
        break;

    case 'fichaTecnicaEncuestadelete':
        include '../classes/FichaTecnicaEncuesta.php';
        echo json_encode(FichaTecnicaEncuesta::delete($rqst));
        break;

    case 'fichaTecnicaEncuestaduplicar':
        include '../classes/FichaTecnicaEncuesta.php';
        echo json_encode(FichaTecnicaEncuesta::duplicate($rqst));
        break;

    case 'fichaTecnicaEncuestaupdatetemas':
        include '../classes/FichaTecnicaEncuesta.php';
        echo json_encode(FichaTecnicaEncuesta::updateTemas($rqst));
        break;

    // Rutas para el módulo: Grilla
    case 'grillaget':
        include '../classes/Grilla.php';
        echo json_encode(Grilla::getAll($rqst));
        break;

    case 'grillasave':
        include '../classes/Grilla.php';
        echo json_encode(Grilla::save($rqst));
        break;

    case 'grilladelete':
        include '../classes/Grilla.php';
        echo json_encode(Grilla::delete($rqst));
        break;

    case 'grillacandidatoguardarrespuestas':
        include '../classes/GrillaCandidatoRespuesta.php';
        echo json_encode(GrillaCandidatoRespuesta::guardarRespuestas($rqst));
        break;

    // Rutas para Análisis de Estudio
    case 'analisisestudioget':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::getAll($rqst));
        break;

    case 'analisisestudiogetbyid':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::getById($rqst));
        break;

    case 'analisisestudiosave':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::save($rqst));
        break;

    case 'analisisestudiodelete':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::delete($rqst));
        break;

    case 'analisisestudiogetcandidatos':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::getCandidatosByGrilla($rqst));
        break;

    case 'analisisestudiogetresultados':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::getResultadosGrillaCandidato($rqst));
        break;

    case 'analisisestudiogetcalculos':
        include '../classes/AnalisisCalculos.php';
        $analisis_id = isset($rqst['analisis_id']) ? intval($rqst['analisis_id']) : 0;
        $calculos = AnalisisCalculos::obtenerCalculos($analisis_id);
        echo json_encode(['output' => ['valid' => true, 'response' => $calculos]]);
        break;

    case 'analisisestudiobuscarexistente':
        include '../classes/AnalisisEstudio.php';
        echo json_encode(AnalisisEstudio::buscarAnalisisExistente($rqst));
        break;

    case 'grillacandidatoguardarpreguntasadicionales':
        include '../classes/GrillaCandidatoRespuesta.php';
        echo json_encode(GrillaCandidatoRespuesta::guardarPreguntasAdicionales($rqst));
        break;

    case 'grillacandidatoverificarvotoduplicado':
        include '../classes/GrillaCandidatoRespuesta.php';
        echo json_encode(GrillaCandidatoRespuesta::verificarVotoDuplicado($rqst));
        break;

    case 'grillacandidatoresultadosentiemporeal':
        include '../classes/GrillaCandidatoRespuesta.php';
        echo json_encode(GrillaCandidatoRespuesta::obtenerResultadosEnTiempoReal($rqst));
        break;

    case 'grillacandidatodemografia':
        include '../classes/GrillaCandidatoRespuesta.php';
        echo json_encode(GrillaCandidatoRespuesta::obtenerDemografiaVotantes($rqst));
        break;

    // Rutas para el módulo: Preguntas de Grilla
    case 'preguntasgrillaget':
        include '../classes/PreguntaGrilla.php';
        echo json_encode(PreguntaGrilla::getAll($rqst));
        break;

    case 'preguntasgrillaobtenerconsubpreguntas':
        include '../classes/PreguntaGrilla.php';
        echo json_encode(PreguntaGrilla::obtenerPreguntasConSubpreguntas($rqst));
        break;

    case 'preguntasgrillaporid':
        include '../classes/PreguntaGrilla.php';
        echo json_encode(PreguntaGrilla::obtenerPreguntaPorId($rqst));
        break;

    case 'preguntasgrillasave':
        include '../classes/PreguntaGrilla.php';
        echo json_encode(PreguntaGrilla::save($rqst));
        break;

    case 'preguntasgrilladelete':
        include '../classes/PreguntaGrilla.php';
        echo json_encode(PreguntaGrilla::delete($rqst));
        break;

    // Rutas para el módulo: Sondeo
    case 'sondeoget':
        include '../classes/Sondeo.php';
        echo json_encode(Sondeo::getAll($rqst));
        break;

    case 'sondeosave':
        include '../classes/Sondeo.php';
        echo json_encode(Sondeo::save($rqst));
        break;

    case 'sondeotoggle':
        include '../classes/Sondeo.php';
        echo json_encode(Sondeo::toggleHabilitado($rqst));
        break;

    case 'sondeodelete':
        include '../classes/Sondeo.php';
        echo json_encode(Sondeo::delete($rqst));
        break;

    // Rutas para el módulo: Resultados de Sondeos
    case 'respuestasondeogetsondeosdisp':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getSondeosDisponibles($rqst));
        break;

    case 'respuestasondeogetestadisticascompletas':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasCompletas($rqst));
        break;

    case 'respuestasondeogetestadisticasgenerales':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasGenerales($rqst));
        break;

    case 'respuestasondeogetestadisticasporideologia':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasPorIdeologia($rqst));
        break;

    case 'respuestasondeogetestadisticasporgenero':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasPorGenero($rqst));
        break;

    case 'respuestasondeogetestadisticasporedad':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasPorEdad($rqst));
        break;

    case 'respuestasondeogetestadisticasporingresos':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasPorIngresos($rqst));
        break;

    case 'respuestasondeogetestadisticasporeducacion':
        include '../classes/RespuestaSondeo.php';
        echo json_encode(RespuestaSondeo::getEstadisticasPorEducacion($rqst));
        break;

  // Rutas para el módulo:  subir excel de preguntas
  case 'preguntas_upload':
    include '../classes/ImportadorPreguntas.php';
    echo json_encode(ImportadorPreguntas::uploadFile());
    break;

  // Rutas para el módulo:  Preguntas
  case 'preguntasave':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::save($rqst));
    break;

  case 'preguntaenunciadosave':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::saveEnunciado($rqst));
    break;

  case 'preguntaenunciadogroupsave':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::saveEnunciadoGroup($rqst));
    break;

  case 'preguntaenunciadoselected':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::saveEnunciadoSelected($rqst));
    break;

  case 'preguntahabilitadosave':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::saveHabilitado($rqst));
    break;

  case 'preguntadelete':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::delete($rqst));
    break;

  case 'preguntasavebatch':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::saveBatch($rqst));
    break;

  case 'preguntareasignar':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::reasignar($rqst));
    break;

  case 'preguntaupdatenumeraladicional':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::updateNumeralAdicional($rqst));
    break;

  case 'preguntarenamecapitulo':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::renameCapitulo($rqst));
    break;

  case 'preguntaget':
    include '../classes/Pregunta.php';
    echo json_encode(Pregunta::getAll($rqst));
    break;

  // Rutas para el módulo: Encuesta
  case 'encuestaget':
    include '../classes/Encuesta.php';
    echo json_encode(Encuesta::getAll($rqst));
    break;

  case 'encuestasave':
    include '../classes/Encuesta.php';
    echo json_encode(Encuesta::save($rqst));
    break;

  case 'encuestadelete':
    include '../classes/Encuesta.php';
    echo json_encode(Encuesta::delete($rqst));
    break;

  // Partidos Politicos
  case 'partidopoliticoget':
    include '../classes/PartidoPolitico.php';
    echo json_encode(PartidoPolitico::getAll($rqst));
    break;
  case 'partidopoliticosave':
    include '../classes/PartidoPolitico.php';
    echo json_encode(PartidoPolitico::save($rqst));
    break;

  // Clientes
  case 'clienteget':
    include '../classes/Cliente.php';
    echo json_encode(Cliente::getAll($rqst));
    break;
  case 'clientesave':
    include '../classes/Cliente.php';
    echo json_encode(Cliente::save($rqst));
    break;
  case 'clientedelete':
    include '../classes/Cliente.php';
    echo json_encode(Cliente::delete($rqst));
    break;
  case 'clientebuscar':
    include '../classes/Cliente.php';
    echo json_encode(Cliente::buscar($rqst));
    break;

  case 'participanteget':
    include '../classes/Participantes.php';
    echo json_encode(Participantes::getAll($rqst));
    break;

  case 'participantesave':
    include '../classes/Participantes.php';
    echo json_encode(Participantes::save($rqst));
    break;


  case 'getSedesInformacionMapaPae':
    include '../classes/Mapa.php';
    echo json_encode(Mapa::getMapaSedesPAE($rqst));
    break;

  case 'getFactoresPorMunicipiosByPilarIdPorColores':
    include '../classes/Colombia.php';
    echo json_encode(Colombia::calcularColorDelDepartamentoByPilarId($rqst));
    break;
  case 'graficasPorFatoresVeredasPorColor':
    include '../classes/Colombia.php';
    echo json_encode(Colombia::calcularColorPorVeredasGeneralByPilarId($rqst));
    break;
  case 'pms_getconf':
    include '../classes/Configuracion.php';
    echo json_encode(Configuracion::getAll($rqst));
    break;
  case 'pms_getconfJS':
    include '../classes/Configuracion.php';
    echo json_encode(Configuracion::getCodigoDepartamentoConfiguracion($rqst));
    break;
  case 'pms_confsave':
    include '../classes/Configuracion.php';
    echo json_encode(Configuracion::save($rqst));
    break;

  //llamado AJAX linea
  case 'getlinea':
    include '../classes/Linea.php';
    echo json_encode(Linea::getAll($rqst));
    break;
  case 'savelinea':
    include '../classes/Linea.php';
    echo json_encode(Linea::save($rqst));
    break;
  case 'gettic':
    include_once '../classes/PcTic.php';
    echo json_encode(PcTic::getAll($rqst));
    break;

  case 'savetic':
    // file_put_contents('debug_log.txt', print_r($rqst, true)); // DEBUG TEMPORAL
    include_once '../classes/PcTic.php';
    echo json_encode(PcTic::save($rqst));
    break;

  //llamado AJAX estrategia
  case 'getestrategia':
    include '../classes/Estrategia.php';
    echo json_encode(Estrategia::getAll($rqst));
    break;
  case 'estrategiasave':
    include '../classes/Estrategia.php';
    echo json_encode(Estrategia::save($rqst));
    break;

  //Llamados AJAX Mapa
  case 'getmapainformacionsecretaria':
    include '../classes/Mapa.php';
    echo json_encode(Mapa::getInformacionSecretariaEnMapaGoogle($rqst));
    break;
  case 'getmapafactores':
    include '../classes/Mapa.php';
    echo json_encode(Mapa::getMapaByFactores($rqst));
    break;
  case 'getmapapilaresbymunicipioId':
    include '../classes/Mapa.php';
    echo json_encode(Mapa::getMapaByPilarByMunicipioId($rqst));
    break;
  //Llamados AJAX Puntaje
  case 'getPuntaje':
    include '../classes/Configuracion_Puntaje.php';
    echo json_encode(Configuracion_Puntaje::getAll($rqst));
    break;
  case 'configuracionpuntajesave':
    include '../classes/Configuracion_Puntaje.php';
    echo json_encode(Configuracion_Puntaje::save($rqst));
    break;
  //Llamados AJAX Puntaje Secretaria
  case 'getPuntajeSecretaria':
    include '../classes/ConfiguracionPuntajeSecretaria.php';
    echo json_encode(ConfiguracionPuntajeSecretaria::getAll($rqst));
    break;
  case 'configuracionpuntajesecretariasave':
    include '../classes/ConfiguracionPuntajeSecretaria.php';
    echo json_encode(ConfiguracionPuntajeSecretaria::save($rqst));
    break;

  //Llamados AJAX Pilar
  case 'getPilar':
    include '../classes/Pilar.php';
    echo json_encode(Pilar::getAll($rqst));
    break;


  //Llamados AJAX Main
  case 'gettotalvisitasporprovincia':
    include '../classes/Main.php';
    echo json_encode(Main::getTotalVisitasPorProvinciasPorAnios($rqst));
    break;
  case 'gettotalvisitaspormesmunicipio':
    include '../classes/Main.php';
    echo json_encode(Main::getTotalVisitasPorMesAMunicipios($rqst));
    break;
  //Grafica promedio secretaria
  case 'getpromediops2025porsecretaria':
    include '../classes/Main.php';
    echo json_encode(Main::getPromedioPs2025PorSecretaria($rqst));
    break;


  //Llamados AJAX Permisos
  case 'pms_usrpermission':
    include '../classes/Permiso.php';
    echo json_encode(Permiso::permisos($rqst));
    break;

  case 'pms_usrsavepermission':
    include '../classes/Permiso.php';
    echo json_encode(Permiso::savePermisos($rqst));
    break;

  case 'pms_usrlogin':
    include '../classes/Usuario.php';
    echo json_encode(Usuario::login($rqst));
    break;


  case 'gestionarimagen_save':
    include '../classes/Galeria.php';
    echo json_encode(Gellery::save($rqst, $_FILES));
    break;

  case 'pms_showimage':
    include '../classes/Galeria.php';
    echo json_encode(Gellery::getAll($rqst));
    break;

  case 'pms_deleteimage':
    include '../classes/Galeria.php';
    echo json_encode(Gellery::deleteFile($rqst));
    break;

  //Llamados AJAX Usuario
  case 'pms_usrsave':
    // Util::verify_user_app_access();
    include '../classes/Usuario.php';
    echo json_encode(Usuario::save($rqst));
    break;

  case 'pms_usrget':
    // Util::verify_user_app_access();
    include '../classes/Usuario.php';
    echo json_encode(Usuario::getAll($rqst));
    break;


  case 'pms_usravailable':
    // Util::verify_user_app_access();
    include '../classes/Usuario.php';
    echo json_encode(Usuario::available($rqst));
    break;
  // Fin Llamados AJAX Usuario


  case 'getveredasbycolor_munic':
    include '../classes/Ciudad.php';
    echo json_encode(Ciudad::getVeredasPorColorCiudadId($rqst));
    break;
  case 'getveredasbycolor_munic2021':
    include '../classes/Ciudad.php';
    echo json_encode(Ciudad::getVeredasPorColorCiudadId2021($rqst));
    break;

  // Llamados de operatividad
  case 'operatividadsave':
    include '../classes/Operatividad.php';
    echo json_encode(Operatividad::save($rqst));
    break;

  case 'operatividadeget':
    // Util::verify_user_app_access();
    include '../classes/Operatividad.php';
    echo json_encode(Operatividad::getAll($rqst));
    break;

  case 'operatividadupdate':
    // Util::verify_user_app_access();
    include '../classes/Operatividad.php';
    echo json_encode(Operatividad::update($rqst));
    break;

  // Llamados casos de inestabilidad social
  case 'socialessave':
    // Util::verify_user_app_access();
    include '../classes/Sociales.php';
    echo json_encode(Sociales::save($rqst));
    break;

  case 'socialget':
    // Util::verify_user_app_access();
    include '../classes/Sociales.php';
    echo json_encode(Sociales::getAll($rqst));
    break;

  // Llamados municipios
  case 'ciudadget':
    // Util::verify_user_app_access();
    include '../classes/Ciudad.php';
    echo json_encode(Ciudad::getAll($rqst));
    break;

  // Llamados veredas
  case 'veredaget':
    // Util::verify_user_app_access();
    include '../classes/Vereda.php';
    echo json_encode(Vereda::getAll($rqst));
    break;

  case 'upd_descrip_vereda':
    include '../classes/Vereda.php';
    echo json_encode(Vereda::updateDescripcionVereda($rqst));
    break;

  // Candidatos


  case 'pms_candidatosave':
    include '../classes/Candidatos.php';
    echo json_encode(Candidatos::save($rqst));
    break;

  case 'pms_candidatoget':
    include '../classes/Candidatos.php';
    echo json_encode(Candidatos::getAll($rqst));
    break;


  case 'data_map':
    // Util::verify_user_app_access();
    include '../classes/Mapa.php';
    echo json_encode(Mapa::getAll($rqst));
    break;


  case 'getfactoresbymunicVersion2022':
    include '../classes/Ciudad.php';
    include '../classes/Estado.php';
    echo json_encode(Ciudad::getFactoresInestabilidadPorCiudad($rqst));
    break;

  // Llamados Información
  case 'saveinfo':
    // Util::verify_user_app_access();
    include '../classes/Informacion.php';
    include '../classes/Estado.php';
    echo json_encode(Informacion::save($rqst));
    break;


  case 'getresultadosmunicipio':
    include '../classes/Resultados.php';
    echo json_encode(Resultados::getAll($rqst));
    break;

  case 'getresultadosvereda':
    include '../classes/Resultados.php';
    echo json_encode(Resultados::getAllVeredaId($rqst));
    break;

  case 'get_veredas_by_nivel':
    // include '../classes/Resultados.php';
    include '../mapa-veredas/veredas.php';
    break;

  case 'ingresovotaciones_save':
    include '../classes/Votaciones.php';
    echo json_encode(Votaciones::save($rqst));
    break;
  case 'pms_getvotacion':
    include '../classes/Votaciones.php';
    echo json_encode(Votaciones::getAll($rqst));
    break;
  case 'pms_votacionupdate':
    include '../classes/Votaciones.php';
    echo json_encode(Votaciones::update($rqst));
    break;



  case 'get_graficos':
    include '../classes/Grafico.php';
    echo json_encode(Grafico::getData($rqst));
    break;

  case 'getGraficoTemaInteres':
    include '../classes/Grafico2022.php';
    //echo json_encode(Grafico2022::getData($rqst));
    break;

  case 'calcularPuntajeDepartamento':
    include '../classes/Puntaje.php';
    include '../classes/Estado.php';
    include '../classes/EstadoDepartamento.php';
    echo json_encode(Puntaje::calcularPuntajeDepartamento($rqst));
    break;

  case 'calcularPuntajeBrigada':
    include '../classes/Puntaje.php';
    include '../classes/EstadoBrigada.php';
    echo json_encode(Puntaje::calcularPuntajeBrigada($rqst));
    break;

  case 'get_veredas_criticas':
    include '../classes/Vereda.php';
    echo json_encode(Vereda::getVeredasCriticasByBatallonIdOrByBrigadaId($rqst));
    break;

  // CONSULTA PARA VEREDAS CRITICAS
  case 'getVeredasCriticas':
    include '../classes/Vereda.php';
    echo json_encode(Vereda::veredasCriticasCONSULTA($rqst));
    break;
  case 'getFactoresVereda':
    include '../classes/Vereda.php';
    $response = Vereda::getFactoresPorVereda($_REQUEST['veredaId']);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    break;



  case 'get_veredas_criticas_data_basica':
    include '../classes/Vereda.php';
    echo json_encode(Vereda::getSoloInformacionVeredasCriticasV2($rqst));
    break;

  case 'get_veredas_criticas_seleccion':
    include '../classes/Vereda.php';
    echo json_encode(Vereda::getVeredasSeleccionadasCriticasByBatallonIdOrByBrigadaId($rqst));
    break;

  case 'get_veredas_sin_desplazamiento':
    include '../classes/Votaciones.php';
    echo json_encode(Votaciones::getVeredasSinDesplazamiento($rqst));
    break;

  case 'get_veredas_sin_desplazamiento_brigadas':
    include '../classes/Votaciones.php';
    echo json_encode(Votaciones::getVeredasSinDesplazamientoBrigada($rqst));
    break;




  //Llamados AJAX Lideres
  case 'pms_lidersave':
    include '../classes/Lideres.php';
    echo json_encode(Lideres::save($rqst));
    break;

  case 'pms_liderget':
    include '../classes/Lideres.php';
    echo json_encode(Lideres::getAll($rqst));
    break;



  case 'pms_lideravailable':
    include '../classes/Lideres.php';
    echo json_encode(Lideres::available($rqst));
    break;


  //llamados visitas municipios

  case 'pms_visitamunget':
    include '../classes/Detalle.php';
    echo json_encode(Detalle::getAll($rqst));
    break;

  case 'pms_cuentavisita':
    include '../classes/Cuenta.php';
    echo json_encode(Cuenta::getAll($rqst));
    break;

  case 'pms_cuentaprovincia':
    include '../classes/Cuentapro.php';
    echo json_encode(Cuentapro::getAll($rqst));
    break;


  case 'pms_save_visita':
    include '../classes/Visitas.php';
    echo json_encode(Visitas::save($_POST));
    break;

  case 'pms_update_visita':
    include '../classes/Visitas.php';
    echo json_encode(Visitas::save($_POST));
    break;

  case 'pms_visitasget':
    include '../classes/Visitas.php';
    echo json_encode(Visitas::getAll($_POST));
    break;


  //Llamados AJAX VISITAS MUNICIPIOS
  case 'pms_visitas':
    include '../classes/Visitas.php';
    echo json_encode(Visitas::save($rqst));
    break;

  case 'pms_visitasget':
    include '../classes/Visitas.php';
    echo json_encode(Visitas::getAll($rqst));
    break;


  case 'spi_visitasg_get':
    include '../classes/Visitasg.php';
    echo json_encode(Visitasg::getAll($rqst));
    break;

  case 'spi_visitasg_get_aspas':
    include '../classes/VisitasgAspas.php';
    echo json_encode(VisitasgAspas::getAll($rqst));
    break;


  case 'spi_visitas_save':
    include '../classes/Visitasg.php';
    echo json_encode(Visitasg::save($rqst));
    break;

  case 'spi_visitas_save_aspas':
    include '../classes/VisitasgAspas.php';
    echo json_encode(VisitasgAspas::save($rqst));
    break;

  case 'spi_visitasg_save':
    include '../classes/Visitasg.php';
    echo json_encode(Visitasg::save($_POST));
    break;



  case 'savefiltros':
    include '../classes/Filtros.php';
    echo json_encode(Filtros::save($rqst));
    break;

  case 'deletefiltros':
    include '../classes/Filtros.php';
    echo json_encode(Filtros::delete($rqst));
    break;

  case 'getPersonasByFiltroId':
    include '../classes/Filtros.php';
    echo json_encode(Filtros::getPersonasByFiltroId($rqst));
    break;



  // Compromisos
  case 'guardarCompromiso':
    include '../classes/CompromisosFactorPilar.php';
    echo json_encode(CompromisosFactorPilar::guardarCompromiso($rqst));
    break;

  case 'getCompromisosFactores':
    include '../classes/CompromisosFactorPilar.php';
    echo json_encode(CompromisosFactorPilar::getCompromisosFactores($rqst));
    break;

  // Rutas para el módulo: Fórmulas
  case 'formulasget':
    include '../classes/Formula.php';
    echo json_encode(Formula::getAll($rqst));
    break;

  case 'formulasgetbyid':
    include '../classes/Formula.php';
    echo json_encode(Formula::getById($rqst));
    break;

  case 'formulassave':
    include '../classes/Formula.php';
    echo json_encode(Formula::save($rqst));
    break;

  case 'formulasdelete':
    include '../classes/Formula.php';
    echo json_encode(Formula::delete($rqst));
    break;

  case 'formulassearch':
    include '../classes/Formula.php';
    echo json_encode(Formula::search($rqst));
    break;

  case 'formulasimport':
    include '../classes/ImportadorFormulas.php';
    echo json_encode(ImportadorFormulas::importar($rqst));
    break;

  // Rutas para Dashboard - Estadísticas Principales
  case 'dashboardgrillas':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getGrillas($rqst));
    break;

  case 'dashboardestadisticas':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getEstadisticasPrincipales($rqst));
    break;

  case 'dashboardideologia':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getVotantesPorIdeologia($rqst));
    break;

  case 'dashboardgenero':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getVotantesPorGenero($rqst));
    break;

  case 'dashboardedad':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getVotantesPorEdad($rqst));
    break;

  case 'dashboardanalisismes':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getAnalisisPorMes($rqst));
    break;

  case 'dashboardtopcandidatos':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getTopCandidatos($rqst));
    break;

  case 'dashboardingresos':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getVotantesPorIngresos($rqst));
    break;

  case 'dashboardgrillasestado':
    include '../classes/Dashboard.php';
    echo json_encode(Dashboard::getGrillasPorEstado($rqst));
    break;

  // Rutas para el módulo: RespuestaCuestionario
  case 'respuestasave':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::save($rqst));
    break;

  // Rutas para el módulo: CertificacionEncuestador
  case 'certificacionsave':
    include '../classes/CertificacionEncuestador.php';
    echo json_encode(CertificacionEncuestador::save($rqst));
    break;

  case 'certificacionget':
    include '../classes/CertificacionEncuestador.php';
    echo json_encode(CertificacionEncuestador::getAll($rqst));
    break;

  case 'certificaciondetalle':
    include '../classes/CertificacionEncuestador.php';
    echo json_encode(CertificacionEncuestador::getDetalle($rqst));
    break;

  case 'certificacionbyvotante':
    include '../classes/CertificacionEncuestador.php';
    echo json_encode(CertificacionEncuestador::getByVotante($rqst));
    break;

  case 'respuestaget':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::getAll($rqst));
    break;

  case 'respuestadetalle':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::getDetalle($rqst));
    break;

  case 'votantesdisponiblesget':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::getVotantesDisponibles($rqst));
    break;

  case 'estadisticascuestionario':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::getEstadisticas($rqst));
    break;

  case 'votantesrespondieron':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::getVotantesQueRespondieron($rqst));
    break;

  case 'votantesnorespondieron':
    include '../classes/RespuestaCuestionario.php';
    echo json_encode(RespuestaCuestionario::getVotantesQueNoRespondieron($rqst));
    break;

  default:
    echo 'OPERACION NO DISPONIBLE';
    break;
}
