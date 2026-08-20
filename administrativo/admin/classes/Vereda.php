<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Vereda
{

  public function __construct() {}

  /**
   * Metodo para actualizar la descripcion de la vereda
   */
  public static function updateDescripcionVereda($rqst)
  {
    $codigo_departamento = isset($rqst['codigo_departamento']) ? ($rqst['codigo_departamento']) : '';
    $codigo_muncipio = isset($rqst['codigo_muncipio']) ? ($rqst['codigo_muncipio']) : '';
    $nombre_vereda = isset($rqst['nombre_vereda']) ? ($rqst['nombre_vereda']) : '';
    $observaciones = isset($rqst['observaciones']) ? ($rqst['observaciones']) : '';

    $db = new DbConection();
    $pdo = $db->openConect();

    $q1 = "UPDATE  " . $db->getTable('tbl_vereda') . "
      SET observaciones='" . $observaciones . "'
      WHERE departamento_id = $codigo_departamento AND municipio_id = $codigo_muncipio  AND nombre_vereda = '$nombre_vereda' LIMIT 1 ";

    $result = $pdo->query($q1);

    $arrjson = array('output' => array('valid' => true, 'response' => $nombre_vereda));
    $db->closeConect();
    return $arrjson;
  }

  public static function getVeredasInfo($rqst)
  {
    $db = new DbConection();
    $pdo = $db->openConect();

    $q = "SELECT tbl_batallones.sigla AS batallon, tbl_brigadas.sigla AS brigada, tbl_departamentos.departamento, tbl_ciudades.municipio, tbl_vereda.nombre_vereda, tbl_vereda.id
        FROM (((" . $db->getTable('tbl_vereda') . " 
        INNER JOIN " . $db->getTable('tbl_brigadas') . "   ON tbl_vereda.tbl_brigada_id = tbl_brigadas.id) 
        INNER JOIN " . $db->getTable('tbl_batallones') . "   ON tbl_vereda.tbl_batallon_id = tbl_batallones.id) 
        INNER JOIN " . $db->getTable('tbl_departamentos') . "   ON tbl_vereda.departamento_id = tbl_departamentos.codigo_departamento) 
        INNER JOIN " . $db->getTable('tbl_ciudades') . "   ON tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio ";
    $result = $pdo->query($q);
    $arr = array();
    if ($result) {
      foreach ($result as $valor) {
        $arr[] = $valor;
      }
      $arrjson = array('output' => array('valid' => true, 'response' => $arr));
    } else {
      $arrjson = Util::error_no_result();
    }
    $db->closeConect();
    return $arrjson;
  }

  /**
   * Obtener la informacion de las veredas
   */
  public static function getAll($rqst)
{
    $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
    $municipio_id = isset($rqst['municipio_id']) ? intval($rqst['municipio_id']) : 0;

    $db = new DbConection();
    $pdo = $db->openConect();

    try {
        // Construir la consulta base
        $query = "SELECT * FROM " . $db->getTable('tbl_vereda');
        $params = [];

        // Agregar condiciones WHERE si es necesario
        if ($id > 0) {
            $query .= " WHERE id = :id";
            $params[':id'] = $id;
        } elseif ($municipio_id > 0) {
            $query .= " WHERE municipio_id = :municipio_id";
            $params[':municipio_id'] = $municipio_id;
        }

        // Agregar ORDER BY con el nombre correcto de la columna
        $query .= " ORDER BY nombre_vereda ASC"; // Asegúrate de que esta es la columna correcta

        // Preparar y ejecutar la consulta
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $veredas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si se solicita información del municipio
        $municipioData = [];
        if ($municipio_id > 0) {
            $stmtMunicipio = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ciudades_accion_unificada') . " WHERE codigo_muncipio = :municipio_id");
            $stmtMunicipio->execute([':municipio_id' => $municipio_id]);
            $municipioData = $stmtMunicipio->fetchAll(PDO::FETCH_ASSOC);
        }

        // Construir la respuesta
        $response = [
            'output' => [
                'valid' => true,
                'response' => $veredas,
                'municipio' => $municipioData
            ]
        ];
    } catch (Exception $e) {
        $response = Util::error_general($e->getMessage());
    } finally {
        $db->closeConect();
    }

    return $response;
}
public static function guardarCompromiso($rqst)
{
    // Validar que los datos obligatorios no estén vacíos
    if (empty($rqst['cantidad']) || empty($rqst['actor'])) {
        return ['output' => ['valid' => false, 'response' => '❌ Faltan datos obligatorios']];
    }

    // Obtener los datos
    $cantidad = intval($rqst['cantidad']);
    $actor = intval($rqst['actor']);
    $observaciones = isset($rqst['observaciones']) ? trim($rqst['observaciones']) : '';

    // Conectar a la base de datos
    $db = new DbConection();
    $pdo = $db->openConect();

    try {
        // Insertar en la base de datos
        $query = "INSERT INTO tbl_compromisos_pilares_factores (cantidad, tbl_actor_id, observaciones) 
                  VALUES (:cantidad, :actor, :observaciones)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':cantidad' => $cantidad,
            ':actor' => $actor,
            ':observaciones' => $observaciones
        ]);

        return ['output' => ['valid' => true, 'response' => '✅ Compromiso guardado correctamente']];
    } catch (Exception $e) {
        return ['output' => ['valid' => false, 'response' => "❌ Error en la base de datos: " . $e->getMessage()]];
    } finally {
        $db->closeConect();
    }
}



  /**
   * Informacion de las veredas que no tienen datos ingresados
   */
  public static function getVeredasSinDatos($rqst)
  {

    $social = isset($rqst['social']) ? ($rqst['social']) : 'no';
    $economico = isset($rqst['economico']) ? ($rqst['economico']) : 'no';
    $armado = isset($rqst['armado']) ? ($rqst['armado']) : 'no';

    $db = new DbConection();
    $pdo = $db->openConect();

    //Social
    if ($social === 'si') {
      $q = "SELECT tbl_vereda.id, tbl_vereda.nombre_vereda, tbl_vereda.codigo_vereda, tbl_ciudades.municipio
          FROM " . $db->getTable('tbl_vereda') . "," . $db->getTable('tbl_ciudades') . "
          WHERE tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio AND
          tbl_vereda.id NOT IN (SELECT vereda_id FROM " . $db->getTable('tbl_resultados_social') . "  )";
    }

    //Economico
    if ($economico === 'si') {
      $q = "SELECT tbl_vereda.id, tbl_vereda.nombre_vereda, tbl_vereda.codigo_vereda, tbl_ciudades.municipio
          FROM " . $db->getTable('tbl_vereda') . "," . $db->getTable('tbl_ciudades') . "
          WHERE tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio AND
          tbl_vereda.id NOT IN (SELECT vereda_id FROM " . $db->getTable('tbl_resultados_economico') . "  )";
    }

    //Armado
    if ($armado === 'si') {
      $q = "SELECT tbl_vereda.id, tbl_vereda.nombre_vereda, tbl_vereda.codigo_vereda, tbl_ciudades.municipio
          FROM " . $db->getTable('tbl_vereda') . "," . $db->getTable('tbl_ciudades') . "
          WHERE tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio AND
          tbl_vereda.id NOT IN (SELECT vereda_id FROM " . $db->getTable('tbl_resultados_armado') . "  )";
    }
    $result = $pdo->query($q);
    $arr = array();
    if ($result) {
      foreach ($result as $valor) {
        $arr[] = $valor;
      }
      $arrjson = array('output' => array('valid' => true, 'response' => $arr));
    } else {
      $arrjson = Util::error_no_result();
    }
    $db->closeConect();
    return $arrjson;
  }



  /**
   * Metodo para calcular veredas Criticas
   */
  public static function veredasCriticasCONSULTA($rqst)
  {
      $codigoDepartamento = isset($rqst['departamento']) ? intval($rqst['departamento']) : 0;
      $codigoMunicipio = isset($rqst['municipio']) ? intval($rqst['municipio']) : 0;
      $pilarId = isset($rqst['pilar']) ? intval($rqst['pilar']) : 0;
      $rangoId = isset($rqst['rango']) ? intval($rqst['rango']) : 0;
  
      if (empty($codigoDepartamento) || empty($codigoMunicipio) || empty($pilarId) || empty($rangoId)) {
          return array("output" => array(
              "valid" => false,
              "response" => array(
                  "code" => "103",
                  "content" => " Faltan datos que son requeridos.",
                  "debug" => compact("codigoDepartamento", "codigoMunicipio", "pilarId", "rangoId")
              )
          ));
      }
  
      $db = new DbConection();
  
      try {
          $puntajes = Util::sb_db_get("SELECT * FROM " . $db->getTable('tbl_puntajes') . " WHERE id =  $rangoId", false);
  
          $qIngresoInformacionCantidad = "SELECT 
              tbl_vereda.id AS vereda_id, 
              tbl_vereda.nombre_vereda,
              tbl_ciudades_accion_unificada.codigo_departamento, 
              tbl_ciudades_accion_unificada.codigo_muncipio,
              tbl_factores.tec_pilar_id AS pilar_id, 
              IFNULL(SUM(tbl_factores.puntaje), 0) AS puntaje_vereda
          FROM 
              " . $db->getTable('tbl_ciudades_accion_unificada') . " 
          LEFT JOIN 
              " . $db->getTable('tbl_vereda') . "  
              ON tbl_ciudades_accion_unificada.codigo_muncipio = tbl_vereda.municipio_id 
          LEFT JOIN 
              " . $db->getTable('tbl_ingreso_informacion') . "   
              ON tbl_vereda.id = tbl_ingreso_informacion.tbl_vereda_id 
          LEFT JOIN 
              " . $db->getTable('tbl_factores') . " 
              ON tbl_ingreso_informacion.tbl_factor_id = tbl_factores.id
              AND tbl_factores.tec_pilar_id = $pilarId
          WHERE 
              tbl_ciudades_accion_unificada.codigo_departamento = $codigoDepartamento  
              AND tbl_ciudades_accion_unificada.codigo_muncipio = $codigoMunicipio 
          GROUP BY 
              tbl_vereda.id, tbl_vereda.nombre_vereda, tbl_factores.tec_pilar_id
          HAVING puntaje_vereda > 0  -- FILTRA SOLO LAS VEREDAS QUE TIENEN PUNTAJE
          ORDER BY 
              tbl_vereda.nombre_vereda ASC";
  
          $cantidades = Util::sb_db_get($qIngresoInformacionCantidad, false);
  
          // Inicializar color neutro por defecto
          $colorDefecto = Util::getColorNeutroMapa();
  
          // Verificar si $puntajes tiene datos o está vacío
          $puntajesVacios = isset($puntajes['output']['response']['code']) && $puntajes['output']['response']['code'] == 104;
          $puntajesValidos = !$puntajesVacios && is_array($puntajes);
  
          $resultado = [];
  
          foreach ($cantidades as $cantidad) {
            $puntajeVereda = isset($cantidad['puntaje_vereda']) ? intval($cantidad['puntaje_vereda']) : 0;
            $color = $colorDefecto; // Color por defecto si no hay coincidencias
        
            // Filtrar solo los datos que están en el rango seleccionado
            if ($puntajesValidos) {
                foreach ($puntajes as $puntaje) {
                    if ($puntajeVereda >= $puntaje['rango_desde'] && $puntajeVereda <= $puntaje['rango_hasta']) {
                        $color = $puntaje['color'];
                        break;
                    }
                }
            }
        
            // Solo agregar las veredas que tienen un color asignado diferente al gris
            if ($color !== $colorDefecto) {
                $veredaData = [
                    'id' => $cantidad['vereda_id'],
                    'nombre_vereda' => $cantidad['nombre_vereda'],
                    'puntaje_vereda' => $puntajeVereda,
                    'color_calculado' => $color
                ];
                $resultado[] = $veredaData;
            }
        }
        
  
          return array('output' => array('valid' => true, 'response' => $resultado));
  
      } catch (Exception $e) {
          return Util::error_general("Error generando los colores: " . $e->getMessage());
      } finally {
          $db->closeConect();
      }
  }
  public static function getFactoresPorVereda($veredaId)
  {
      $db = new DbConection();

      try {
          $q = "SELECT 
                  f.tipo AS factor,
                  i.valor AS cantidad,
                  f.tipo_medicion AS unidad_medida
                FROM " . $db->getTable('tbl_ingreso_informacion') . " i
                INNER JOIN " . $db->getTable('tbl_factores') . " f
                ON i.tbl_factor_id = f.id
                WHERE i.tbl_vereda_id = :veredaId";

          $stmt = $db->openConect()->prepare($q);
          $stmt->bindParam(':veredaId', $veredaId, PDO::PARAM_INT);
          $stmt->execute();
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

          // Asegurarse de que los datos están correctamente codificados en UTF-8 para las tildes
          foreach ($result as &$row) {
              $row['factor'] = utf8_encode($row['factor']);
              $row['unidad_medida'] = utf8_encode($row['unidad_medida']);
          }


          if (empty($result)) {
              return array('output' => array('valid' => false, 'error' => 'No se encontraron datos.'));
          }


          return array('output' => array('valid' => true, 'response' => $result));

      } catch (Exception $e) {
          return array('output' => array('valid' => false, 'error' => $e->getMessage()));
      } finally {
          $db->closeConect();
      }
  }


  /**
   * Metodo para obtener las veredas 5 más criticas por batallon o brigada
   */
  public static function getVeredasCriticasByBatallonIdOrByBrigadaId($rqst)
  {

    include 'Estado.php';

    $tbl_brigada_id = isset($rqst['tbl_brigada_id']) ? intval($rqst['tbl_brigada_id']) : 0;
    $tbl_batallon_id = isset($rqst['tbl_batallon_id']) ? intval($rqst['tbl_batallon_id']) : 0;
    $limit = isset($rqst['limit']) ? intval($rqst['limit']) : 8;
    $filtro = isset($rqst['filtro']) ? ($rqst['filtro']) : '';
    $puntaje = 200;
    $q = "";

    if ($tbl_brigada_id == 0 && $tbl_batallon_id  == 0) {
      return Util::error_no_result();
    }

    $db = new DbConection();
    $pdo = $db->openConect();

    // Filtro por brigada
    if ($tbl_brigada_id  > 0) {
      $q = "SELECT tbl_ciudades.id AS tbl_ciudad_id,
        tbl_vereda.id AS tbl_vereda_id,
        tbl_vereda.nombre_vereda,
        tbl_vereda.carpeta_svg,
        tbl_vereda.nombre_svg,
        tbl_ciudades.municipio,
        tbl_ciudades.codigo_muncipio,
        tbl_ciudades.codigo_departamento,
        tbl_brigadas.id as tbl_brigada_id,
        tbl_brigadas.sigla AS brigada, 
        tbl_batallones.sigla AS batallon, 
        tbl_vereda.color, 
        tbl_vereda.puntaje
        FROM ((" . $db->getTable('tbl_vereda') . " INNER JOIN " . $db->getTable('tbl_ciudades') . " ON tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio) 
        INNER JOIN " . $db->getTable('tbl_batallones') . " ON tbl_vereda.tbl_batallon_id = tbl_batallones.id) 
        INNER JOIN " . $db->getTable('tbl_brigadas') . " ON tbl_vereda.tbl_brigada_id = tbl_brigadas.id 
        WHERE tbl_brigadas.id =  $tbl_brigada_id AND tbl_vereda.puntaje >= $puntaje ORDER BY tbl_vereda.puntaje DESC LIMIT $limit";
    }

    // Filtro por Batallon
    if ($tbl_batallon_id  > 0) {

      $q = "SELECT tbl_ciudades.id AS tbl_ciudad_id,
        tbl_vereda.id AS tbl_vereda_id,
        tbl_vereda.nombre_vereda,
        tbl_vereda.carpeta_svg,
        tbl_vereda.nombre_svg,
        tbl_ciudades.municipio,
        tbl_ciudades.codigo_muncipio,
        tbl_ciudades.codigo_departamento,
        tbl_brigadas.id as tbl_brigada_id,
        tbl_brigadas.sigla AS brigada, 
        tbl_batallones.sigla AS batallon, 
        tbl_vereda.color, 
        tbl_vereda.puntaje
        FROM ((" . $db->getTable('tbl_vereda') . " INNER JOIN " . $db->getTable('tbl_ciudades') . " ON tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio) 
        INNER JOIN " . $db->getTable('tbl_batallones') . " ON tbl_vereda.tbl_batallon_id = tbl_batallones.id) 
        INNER JOIN " . $db->getTable('tbl_brigadas') . " ON tbl_vereda.tbl_brigada_id = tbl_brigadas.id 
        WHERE tbl_batallones.id = $tbl_batallon_id AND tbl_vereda.puntaje >= $puntaje ORDER BY tbl_vereda.puntaje DESC LIMIT $limit";
    }
    $result = $pdo->query($q);
    $arr = array();
    $arrEstadoVeredasCriticas = array();
    if ($result) {
      foreach ($result as $valor) {
        $arrTemp = array();
        $arrTemp['tbl_vereda_id'] = $valor['tbl_vereda_id'];
        $arrTemp['tbl_ciudad_id'] = $valor['tbl_ciudad_id'];
        $arrTemp['nombre_vereda'] = $valor['nombre_vereda'];
        $arrTemp['carpeta_svg'] = $valor['carpeta_svg'];
        $arrTemp['nombre_svg'] = $valor['nombre_svg'];
        $arrTemp['municipio'] = $valor['municipio'];
        $arrTemp['codigo_muncipio'] = $valor['codigo_muncipio'];
        $arrTemp['codigo_departamento'] = $valor['codigo_departamento'];
        $arrTemp['tbl_brigada_id'] = $valor['tbl_brigada_id'];
        $arrTemp['brigada'] = $valor['brigada'];
        $arrTemp['batallon'] = $valor['batallon'];
        $arrTemp['color'] = $valor['color'];
        $arrTemp['puntaje'] = $valor['puntaje'];
        $arrTemp['clase'] = Util::getClasePorColor($valor['color']);
        $arr[] = $arrTemp;

        //Consultamos la informacion del estado de la VEREDA
        $codigo_departamento = $valor['codigo_departamento'];
        $codigo_muncipio = $valor['codigo_muncipio'];
        $nombre_vereda = $valor['nombre_vereda'];
        $rqstVereda =  array('codigo_departamento' => $codigo_departamento, 'codigo_muncipio' => $codigo_muncipio, 'vereda' => $nombre_vereda);
        $resultVereda = Estado::getEstadoFactorArmadoSocialEcon($rqstVereda);
        if ($resultVereda) {
          $arrEstadoVeredasCriticas[] = $resultVereda['output'];
        }
      }
      if (count($arr) > 0) {
        $arrjson = array('output' => array('valid' => true, 'response' => $arr, 'filtro' => $filtro, 'estados' => $arrEstadoVeredasCriticas));
      } else {
        $arrjson = Util::error_no_result();
      }
    } else {
      $arrjson = Util::error_no_result();
    }
    $db->closeConect();
    return $arrjson;
  }

  /**
   * Metodo para mostrar la información de las veredas seleccionadas en Modulo de "Veredas Críticas - Selección personalizada"
   */
  public static function getVeredasSeleccionadasCriticasByBatallonIdOrByBrigadaId($rqst)
  {
    include 'Estado.php';

    $tbl_brigada_id = isset($rqst['tbl_brigada_id']) ? intval($rqst['tbl_brigada_id']) : 0;
    $tbl_batallon_id = isset($rqst['tbl_batallon_id']) ? intval($rqst['tbl_batallon_id']) : 0;
    $filtro = isset($rqst['filtro']) ? ($rqst['filtro']) : '';
    $tbl_vereda_ids = isset($rqst['tbl_vereda_id']) ? ($rqst['tbl_vereda_id']) : '';

    $arr = array();
    $arrEstadoVeredasCriticas = array();
    $q = "";

    if ($tbl_brigada_id == 0 && $tbl_batallon_id  == 0) {
      return Util::error_no_result();
    }

    if ($tbl_vereda_ids == "") {
      return Util::info_general('Debe selecionar al menos una vereda');
    }
    $arrchkVeredaId = explode(',', $tbl_vereda_ids);
    $contador = count($arrchkVeredaId);

    $db = new DbConection();
    $pdo = $db->openConect();


    for ($i = 0; $i < $contador; $i++) {

      if (intval($arrchkVeredaId[$i]) > 0) {
        $q = "SELECT tbl_ciudades.id AS tbl_ciudad_id,
          tbl_vereda.id AS tbl_vereda_id,
          tbl_vereda.nombre_vereda,
          tbl_vereda.carpeta_svg,
          tbl_vereda.nombre_svg,
          tbl_ciudades.municipio,
          tbl_ciudades.codigo_muncipio,
          tbl_ciudades.codigo_departamento,
          tbl_brigadas.id as tbl_brigada_id,
          tbl_brigadas.sigla AS brigada, 
          tbl_batallones.sigla AS batallon, 
          tbl_batallones.id AS tbl_batallon_id, 
          tbl_vereda.color, 
          tbl_vereda.puntaje
          FROM ((" . $db->getTable('tbl_vereda') . " INNER JOIN " . $db->getTable('tbl_ciudades') . " ON tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio) 
          INNER JOIN " . $db->getTable('tbl_brigadas') . " ON tbl_vereda.tbl_brigada_id = tbl_brigadas.id) 
          INNER JOIN " . $db->getTable('tbl_batallones') . " ON tbl_vereda.tbl_batallon_id = tbl_batallones.id
          WHERE tbl_vereda.id = $arrchkVeredaId[$i]";

        $result = $pdo->query($q);
        if ($result) {
          foreach ($result as $valor) {
            $arrTemp = array();
            $arrTemp['tbl_vereda_id'] = $valor['tbl_vereda_id'];
            $arrTemp['tbl_ciudad_id'] = $valor['tbl_ciudad_id'];
            $arrTemp['nombre_vereda'] = $valor['nombre_vereda'];
            $arrTemp['carpeta_svg'] = $valor['carpeta_svg'];
            $arrTemp['nombre_svg'] = $valor['nombre_svg'];
            $arrTemp['municipio'] = $valor['municipio'];
            $arrTemp['codigo_muncipio'] = $valor['codigo_muncipio'];
            $arrTemp['codigo_departamento'] = $valor['codigo_departamento'];
            $arrTemp['tbl_brigada_id'] = $valor['tbl_brigada_id'];
            $arrTemp['brigada'] = $valor['brigada'];
            $arrTemp['batallon'] = $valor['batallon'];
            $arrTemp['color'] = $valor['color'];
            $arrTemp['puntaje'] = $valor['puntaje'];
            $arrTemp['clase'] = Util::getClasePorColor($valor['color']);
            $arr[] = $arrTemp;

            //Consultamos la informacion del estado de la VEREDA
            $codigo_departamento = $valor['codigo_departamento'];
            $codigo_muncipio = $valor['codigo_muncipio'];
            $nombre_vereda = $valor['nombre_vereda'];
            $rqstVereda =  array('codigo_departamento' => $codigo_departamento, 'codigo_muncipio' => $codigo_muncipio, 'vereda' => $nombre_vereda);
            $resultVereda = Estado::getEstadoFactorArmadoSocialEcon($rqstVereda);
            if ($resultVereda) {
              $arrEstadoVeredasCriticas[] = $resultVereda['output'];
            }
          }
        }
      }
    }

    if (count($arr) > 0) {
      $arrjson = array('output' => array('valid' => true, 'response' => $arr, 'filtro' => $filtro, 'estados' => $arrEstadoVeredasCriticas));
    } else {
      $arrjson = Util::error_no_result();
    }

    $db->closeConect();
    return $arrjson;
  }

  public static function getSoloInformacionVeredasCriticasV2($rqst)
  {

    include 'Estado.php';

    $tbl_brigada_id = isset($rqst['tbl_brigada_id']) ? intval($rqst['tbl_brigada_id']) : 0;
    $tbl_batallon_id = isset($rqst['tbl_batallon_id']) ? intval($rqst['tbl_batallon_id']) : 0;
    $limit = isset($rqst['limit']) ? intval($rqst['limit']) : 5;
    $filtro = isset($rqst['filtro']) ? ($rqst['filtro']) : '';
    $puntaje = 0;
    $q = "";

    if ($tbl_brigada_id == 0 && $tbl_batallon_id  == 0) {
      return Util::error_no_result();
    }

    $db = new DbConection();
    $pdo = $db->openConect();

    // Filtro por brigada
    if ($tbl_brigada_id  > 0) {
      $q = "SELECT tbl_brigadas.sigla AS brigada, 
        tbl_batallones.id AS batallon_id, 
        tbl_brigadas.id AS brigada_id, 
        tbl_batallones.sigla AS batallon, 
        tbl_ciudades.municipio, 
        tbl_vereda.id AS tbl_vereda_id, 
        tbl_vereda.nombre_vereda, 
        tbl_vereda.nombre_svg, 
        tbl_vereda.carpeta_svg, 
        tbl_ciudades.codigo_departamento, 
        tbl_vereda.color, 
        tbl_vereda.puntaje
        FROM ((" . $db->getTable('tbl_vereda') . " INNER JOIN " . $db->getTable('tbl_ciudades') . " ON tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio) 
        INNER JOIN " . $db->getTable('tbl_brigadas') . " ON tbl_vereda.tbl_brigada_id = tbl_brigadas.id) 
        INNER JOIN " . $db->getTable('tbl_batallones') . " ON tbl_vereda.tbl_batallon_id = tbl_batallones.id
        WHERE tbl_brigadas.id = $tbl_brigada_id 
        ORDER BY tbl_ciudades.municipio, tbl_vereda.nombre_vereda ASC LIMIT $limit";
    }

    // Filtro por Batallon
    if ($tbl_batallon_id  > 0) {
      $q = "SELECT tbl_brigadas.sigla AS brigada, 
        tbl_batallones.id AS batallon_id, 
        tbl_brigadas.id AS brigada_id, 
        tbl_batallones.sigla AS batallon, 
        tbl_ciudades.municipio, 
        tbl_vereda.id AS tbl_vereda_id, 
        tbl_vereda.nombre_vereda, 
        tbl_vereda.nombre_svg, 
        tbl_vereda.carpeta_svg, 
        tbl_ciudades.codigo_departamento, 
        tbl_vereda.color, 
        tbl_vereda.puntaje
        FROM ((" . $db->getTable('tbl_vereda') . " INNER JOIN " . $db->getTable('tbl_ciudades') . " ON tbl_vereda.municipio_id = tbl_ciudades.codigo_muncipio) 
        INNER JOIN " . $db->getTable('tbl_brigadas') . " ON tbl_vereda.tbl_brigada_id = tbl_brigadas.id) 
        INNER JOIN " . $db->getTable('tbl_batallones') . " ON tbl_vereda.tbl_batallon_id = tbl_batallones.id
        WHERE tbl_batallones.id = $tbl_batallon_id 
        ORDER BY tbl_ciudades.municipio, tbl_vereda.nombre_vereda ASC LIMIT $limit";
    }
    $result = $pdo->query($q);
    $arr = array();
    if ($result) {
      foreach ($result as $valor) {
        $arr[] = $valor;
      }
      if (count($arr) > 0) {
        $arrjson = array('output' => array('valid' => true, 'response' => $arr, 'filtro' => $filtro));
      } else {
        $arrjson = Util::error_no_result();
      }
    } else {
      $arrjson = Util::error_no_result();
    }
    $db->closeConect();
    return $arrjson;
  }
}
