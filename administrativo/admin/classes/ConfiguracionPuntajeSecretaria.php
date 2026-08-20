<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class ConfiguracionPuntajeSecretaria
{

  public function __construct() {}

  public static function getAll($rqst)
  {
    $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

    $db = new DbConection();
    $pdo = $db->openConect();

    try {
      if ($id > 0) {
        $query = "SELECT * FROM " . $db->getTable('tbl_puntajes_secretarias') . " WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      } else {
        $query = "SELECT tbl_puntajes_secretarias.*, tbl_secretarias.secretaria AS secretaria
                        FROM " . $db->getTable('tbl_puntajes_secretarias') . "
                        INNER JOIN " . $db->getTable('tbl_secretarias') . " ON tbl_puntajes_secretarias.tbl_secretaria_id = tbl_secretarias.id
                        ORDER BY tbl_secretarias.secretaria ASC, tbl_puntajes_secretarias.rango_desde ASC";
        $stmt = $pdo->prepare($query);
      }
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($results) {
        $response = array('output' => array('valid' => true, 'response' => $results));
      } else {
        $response = Util::error_no_result();
      }
    } catch (Exception $e) {
      $response = Util::error_general($e->getMessage());
    } finally {
      $db->closeConect();
    }

    return $response;
  }

  public static function save($rqst)
  {
    $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
    $tbl_secretaria_id = isset($rqst['secretariaId']) ? intval($rqst['secretariaId']) : 0;
    $desde = isset($rqst['desde']) ? intval($rqst['desde']) : 0;
    $hasta = isset($rqst['hasta']) ? intval($rqst['hasta']) : 0;
    $tipo_medicion = isset($rqst['tipo_medicion']) ? ($rqst['tipo_medicion']) : '';
    $color = isset($rqst['color']) ? ($rqst['color']) : '';
    $tbl_usuario_id =  2;

    $db = new DbConection();
    $pdo = $db->openConect();

    if ($tbl_secretaria_id == 0 || $desde == 0 && $hasta == 0 || $color == "" || $color == "Seleccione" || $tipo_medicion == "" || $tipo_medicion == "seleccione") {
      return Util::error_missing_data();
    }
    // Validar el rango excluyendo el registro actual si está editando
    if (!ConfiguracionPuntajeSecretaria::validarRango($desde, $hasta, $tbl_secretaria_id, $tipo_medicion, $id)) {
      return Util::error_general('El rango se cruza con un rango existente.');
    }

    if ($id > 0) {
      //actualiza la informacion
      $q = "SELECT id FROM " . $db->getTable('tbl_puntajes_secretarias') . " WHERE id = " . $id;
      $result = $pdo->query($q);
      if ($result) {
        $table = $db->getTable('tbl_puntajes_secretarias');
        $arrfieldscomma = array(
          'tbl_secretaria_id' => $tbl_secretaria_id,
          'rango_desde' => $desde,
          'rango_hasta' => $hasta,
          'tipo_medicion' => $tipo_medicion,
          'tbl_usuario_id' => $tbl_usuario_id,
          'color' => $color
        );
        $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());
        $q = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
        $result = $pdo->query($q);
        if (!$result) {
          $arrjson = Util::error_general();
        } else {
          $arrjson = array('output' => array('valid' => true, 'id' => $id));
        }
      } else {
        $arrjson = Util::error_general();
      }
    } else {
      $q = "INSERT INTO " . $db->getTable('tbl_puntajes_secretarias') . " (dtcreate, rango_desde, rango_hasta, tbl_secretaria_id, tbl_usuario_id, tipo_medicion, color)
                    VALUES ( " . Util::date_now_server() . ", :rango_desde, :rango_hasta, :tbl_secretaria_id, :tbl_usuario_id, :tipo_medicion, :color)";
      $result = $pdo->prepare($q);
      $arrparam = array(
        ':rango_desde' => $desde,
        ':rango_hasta' => $hasta,
        ':tbl_secretaria_id' => $tbl_secretaria_id,
        ':tbl_usuario_id' => $tbl_usuario_id,
        ':tipo_medicion' => $tipo_medicion,
        ':color' => $color
      );
      if ($result->execute($arrparam)) {
        $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
      } else {
        $arrjson = Util::error_general(' Al guardar ingreso de configuración');
      }
    }
    $db->closeConect();
    return $arrjson;
  }


  public static function validarRango_otro($rangoDesde, $rangoHasta, $tbl_secretaria_id, $pilarId, $tipoMedicion)
  {
    try {

      $db = new DbConection();
      $pdo = $db->openConect();

      // Consulta para verificar si existe solapamiento
      $query = "
                SELECT COUNT(*) AS total
                FROM " . $db->getTable('tbl_puntajes_secretarias') . "
                WHERE tbl_secretaria_id = :tbl_secretaria_id
                AND tipo_medicion = :tipoMedicion
                AND (
                    (:rangoDesde BETWEEN rango_desde AND rango_hasta)
                    OR (:rangoHasta BETWEEN rango_desde AND rango_hasta)
                    OR (rango_desde BETWEEN :rangoDesde AND :rangoHasta)
                    OR (rango_hasta BETWEEN :rangoDesde AND :rangoHasta)
                )
            ";

      // Preparar y ejecutar la consulta
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(':tbl_secretaria_id', $tbl_secretaria_id, PDO::PARAM_INT);
      $stmt->bindParam(':tipoMedicion', $tipoMedicion, PDO::PARAM_STR);
      $stmt->bindParam(':rangoDesde', $rangoDesde, PDO::PARAM_INT);
      $stmt->bindParam(':rangoHasta', $rangoHasta, PDO::PARAM_INT);
      $stmt->execute();

      // Obtener el resultado
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['total'] == 0; // True si no hay solapamientos, False si hay
    } catch (Exception $e) {
      return false;
    } finally {
      $db->closeConect();
    }
  }

  public static function validarRango($desde, $hasta, $tbl_secretaria_id, $tipo_medicion, $idExcluir = null)
  {
    $db = new DbConection();
    $pdo = $db->openConect();

    $query = "SELECT id FROM " . $db->getTable('tbl_puntajes_secretarias') . " 
                        WHERE tbl_secretaria_id = :tbl_secretaria_id
                        AND tipo_medicion = :tipo_medicion 
                        AND (
                            (rango_desde BETWEEN :desde AND :hasta) 
                            OR (rango_hasta BETWEEN :desde AND :hasta)
                            OR (:desde BETWEEN rango_desde AND rango_hasta)
                            OR (:hasta BETWEEN rango_desde AND rango_hasta)
                        )";
    if ($idExcluir) {
      $query .= " AND id != :idExcluir";
    }

    $stmt = $pdo->prepare($query);
    $params = [
      ':tbl_secretaria_id' => $tbl_secretaria_id,
      ':tipo_medicion' => $tipo_medicion,
      ':desde' => $desde,
      ':hasta' => $hasta
    ];

    if ($idExcluir) {
      $params[':idExcluir'] = $idExcluir;
    }

    $stmt->execute($params);

    $db->closeConect();

    return $stmt->rowCount() === 0;
  }
}
