<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Visitasg
{

  public function __construct() {}

  public static function getAll($rqst)
  {
    $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
    $tipoUsuario = SessionData::getUserType();
    $codigoMunicipio = SessionData::getCodigoMunicipio();


    $db = new DbConection();
    $pdo = $db->openConect();

    // Consulta base
    $q = "SELECT tbl_gestora.*, tbl_ciudades.municipio, tbl_acciong.accion
              FROM " . $db->getTable('tbl_gestora') . "  
              INNER JOIN " . $db->getTable('tbl_ciudades') . " 
              ON tbl_gestora.tbl_municipio_id = tbl_ciudades.codigo_muncipio
              INNER JOIN " . $db->getTable('tbl_acciong') . " 
              ON tbl_gestora.tbl_acciong_id = tbl_acciong.id";
    $params = [];

    if ($id > 0) {
      $q .= " WHERE tbl_gestora.id = :id";
      $params[':id'] = $id;
    }

    if(Util::Auxiliar_Alcalde() == $tipoUsuario || Util::Alcalde() == $tipoUsuario){
      $q .= " AND tbl_ciudades.codigo_muncipio = :codigo_muncipio";
      $params[':codigo_muncipio'] = $codigoMunicipio;
    }

    $q .= " ORDER BY tbl_gestora.id DESC";
    $stmt = $pdo->prepare($q);
    $stmt->execute($params);

    $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $db->closeConect();


    if ($arr) {
      return [
        'output' => [
          'valid' => true,
          'response' => $arr
        ]
      ];
    } else {
      return Util::error_no_result();
    }
  }



  public static function save($rqst)
  {
      $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
      $tbl_departamento_id = isset($rqst['tbl_departamento_id']) ? intval($rqst['tbl_departamento_id']) : 0;
      $tbl_municipio_id = isset($rqst['tbl_municipio_id']) ? intval($rqst['tbl_municipio_id']) : 0;
      $date = $rqst['date'] ?? '';
      $desc_actividad = $rqst['desc_actividad'] ?? '';
      $inversion = $rqst['inversion'] ?? '';
      $poblacion = $rqst['poblacion'] ?? '';
      $tbl_acciong_id = $rqst['tbl_acciong_id'] ?? 22;
      $provincia = $rqst['provincia'] ?? '';
      $tbl_usuario_id = intval($_SESSION['session_user']['id']);
      $foto1 = $rqst['foto1'] ?? '';
      $foto2 = $rqst['foto2'] ?? '';
      $foto3 = $rqst['foto3'] ?? '';
      $foto4 = $rqst['foto4'] ?? '';
      

      $linea = $rqst['linea'] ?? '';
      $estrategia = $rqst['estrategia'] ?? '';
      $campana = $rqst['campana'] ?? '';
      $actividad = $rqst['actividad'] ?? '';
      $link = $rqst['link'] ?? '';

      $tipoUsuario = SessionData::getUserType();
      $codigoMunicipio = SessionData::getCodigoMunicipio();

      $mensaje = "Debe seleccionar el municipio correspondiente al cual pertenece.";
      if (Util::Auxiliar_Alcalde() == $tipoUsuario || Util::Alcalde() == $tipoUsuario || Util::Secretario_Despacho() == $tipoUsuario) {
          if ($tbl_municipio_id !== $codigoMunicipio) {
              return Util::error_general($mensaje);
          }
      }

      $db = new DbConection();
      $pdo = $db->openConect();
      $pdo->beginTransaction(); 

      try {
          if ($id > 0) {
              // Actualizar si el ID existe
              $q0 = "SELECT 1 FROM " . $db->getTable('tbl_gestora') . " WHERE id = :id";
              $stmt = $pdo->prepare($q0);
              $stmt->execute([':id' => $id]);

              if ($stmt->fetch()) {
                  $q = "UPDATE " . $db->getTable('tbl_gestora') . " 
                        SET date = :date, desc_actividad = :desc_actividad, inversion = :inversion, 
                            poblacion = :poblacion, provincia = :provincia, tbl_acciong_id = :tbl_acciong_id, 
                            foto1 = :foto1, foto2 = :foto2, foto3 = :foto3, foto4 = :foto4, 
                            tbl_departamento_id = :tbl_departamento_id, tbl_municipio_id = :tbl_municipio_id,
                            linea = :linea, estrategia = :estrategia, campana = :campana, actividad = :actividad, link = :link
                        WHERE id = :id";

                  $params = [
                      ':date' => $date,
                      ':desc_actividad' => $desc_actividad,
                      ':inversion' => $inversion,
                      ':poblacion' => $poblacion,
                      ':provincia' => $provincia,
                      ':tbl_acciong_id' => $tbl_acciong_id,
                      ':tbl_departamento_id' => $tbl_departamento_id,
                      ':tbl_municipio_id' => $tbl_municipio_id,
                      ':id' => $id,
                      ':foto1' => $foto1,
                      ':foto2' => $foto2,
                      ':foto3' => $foto3,
                      ':foto4' => $foto4,
                      ':linea' => $linea,
                      ':estrategia' => $estrategia,
                      ':campana' => $campana,
                      ':actividad' => $actividad,
                      ':link' => $link,
                  ];

                  $stmt = $pdo->prepare($q);
                  $stmt->execute($params);
                  $arrjson = ['output' => ['valid' => true, 'id' => $id]];
              } else {
                  throw new Exception('No se encontró la visita para actualizar.');
              }
          } else {
              // Insertar nuevo registro
              if (!empty($date) && !empty($poblacion) && $tbl_departamento_id > 0 && !empty($desc_actividad) && $tbl_municipio_id > 0 ) {
                  $q = "INSERT INTO " . $db->getTable('tbl_gestora') . " 
                        (dtcreate, date, poblacion, tbl_acciong_id, desc_actividad, provincia, inversion, 
                        foto1, foto2, foto3, foto4, tbl_departamento_id, tbl_municipio_id, tbl_usuario_id,
                        linea, estrategia, campana, actividad, link) 
                        VALUES (:dtcreate, :date, :poblacion, :tbl_acciong_id, :desc_actividad, :provincia, :inversion, 
                        :foto1, :foto2, :foto3, :foto4, :tbl_departamento_id, :tbl_municipio_id, :tbl_usuario_id,
                        :linea, :estrategia, :campana, :actividad, :link)";

                  $params = [
                      ':dtcreate' => Util::date_now_server(),
                      ':date' => $date,
                      ':poblacion' => $poblacion,
                      ':tbl_acciong_id' => $tbl_acciong_id,
                      ':desc_actividad' => $desc_actividad,
                      ':provincia' => $provincia,
                      ':inversion' => $inversion,
                      ':tbl_departamento_id' => $tbl_departamento_id,
                      ':tbl_municipio_id' => $tbl_municipio_id,
                      ':tbl_usuario_id' => $tbl_usuario_id,
                      ':foto1' => $foto1,
                      ':foto2' => $foto2,
                      ':foto3' => $foto3,
                      ':foto4' => $foto4,
                      ':linea' => $linea,
                      ':estrategia' => $estrategia,
                      ':campana' => $campana,
                      ':actividad' => $actividad,
                      ':link' => $link,
                  ];

                  $stmt = $pdo->prepare($q);
                  $stmt->execute($params);
                  $arrjson = ['output' => ['valid' => true, 'response' => $pdo->lastInsertId()]];
              } else {
                  throw new Exception('Faltan datos para insertar la visita.');
              }
          }

          $pdo->commit(); 
      } catch (Exception $e) {
          $pdo->rollBack(); 
          $arrjson = Util::error_general($e->getMessage());
      }

      $db->closeConect();
      return $arrjson;
  }


  /*   public static function loadPhoto($imagen)
  {
    include_once "../../constants.php";
    if ($imagen['size'] > 0) {
      if ($imagen['error'] < 1) {
        $type_file = explode("/", $imagen['type']);
        if ($type_file['0'] == 'image') {
          $ruta_img = WWW_ROOT_GESTORA;
          if (!file_exists($ruta_img)) {
            mkdir($ruta_img, 0777, true);
          }
          $nombre_archivo = rand() . '.' . $type_file['1'];
          if (move_uploaded_file($imagen['tmp_name'], $ruta_img . $nombre_archivo)) {
            return $nombre_archivo;
          } else {
            return null;
          }
        } else {
          return null;
        }
      } else {
        return null;
      }
    } else {
      return null;
    }
  } */
}