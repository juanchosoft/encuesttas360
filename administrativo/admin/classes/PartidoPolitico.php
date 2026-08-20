<?php
class PartidoPolitico
{

    public function __construct()
    {

    }

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT * FROM " . $db->getTable('tbl_partidos_politicos');
        $params = [];

        if ($id > 0) {
            $q .= " WHERE id = :id";
            $params[':id'] = $id;
        }

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($arr) {
                $arrjson = array('output' => array('valid' => true, 'response' => $arr));
            } else {
                $arrjson = array('output' => array('valid' => true, 'response' => []));
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener los datos de partidos políticos.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function save($rqst)
    {
        // Sanear y obtener los datos de la solicitud.
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $nombre_partido = isset($rqst['nombre_partido']) ? trim($rqst['nombre_partido']) : '';
        $logo = isset($rqst['logo']) ? trim($rqst['logo']) : null; // Asumimos que el logo es una cadena de texto (ruta/nombre)
        $resolucion = isset($rqst['resolucion']) ? trim($rqst['resolucion']) : null;
        $representante_legal = isset($rqst['representante_legal']) ? trim($rqst['representante_legal']) : null;
        $email_contacto = isset($rqst['email_contacto']) ? trim($rqst['email_contacto']) : null;

        // Por defecto, usar default.png
        $image = 'default.png';

        // Solo usar la imagen si hay una NUEVA cargada en esta sesión
        if (isset($_SESSION['file']['nombrearchivo']) && !empty($_SESSION['file']['nombrearchivo'])) {
            $image = $_SESSION['file']['nombrearchivo'];
        }

        // Validaciones básicas.
        if (empty($nombre_partido)) {
            return Util::error_missing_data_description('El nombre del partido es requerido.');
        }
        if (empty($resolucion)) {
          return Util::error_missing_data_description('El resolucion del partido es requerido.');
        }

        // Si se espera subir un archivo de logo, aquí podrías llamar a una función similar a loadPdf
        // pero adaptada para logos y el campo 'logo' en la DB.
        // Por ahora, asumimos que 'logo' es una cadena de texto que viene del $rqst directamente.
        // Si necesitas subir archivos de logo, esta es la sección donde integrarías esa lógica.
        // Ejemplo de cómo podrías manejar un archivo de logo si lo subes:
        // $uploadedLogoName = null;
        // if (isset($files['logo']) && $files['logo']['error'] === UPLOAD_ERR_OK) {
        //     $uploadedLogoName = self::handleLogoUpload($files['logo']); // Debes crear esta función
        // }
        // if ($uploadedLogoName) {
        //     $logo = $uploadedLogoName; // Usa el nombre del archivo subido como valor para 'logo'
        // }
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            if ($id > 0) {
                $table = $db->getTable('tbl_partidos_politicos');
                $q = "SELECT id FROM $table WHERE id = " . $id;
                $result = $pdo->query($q);
                if ($result) {
                  $arrfieldscomma = array(
                      'nombre_partido' => $nombre_partido,
                      'resolucion' => $resolucion,
                      'representante_legal' => $representante_legal,
                      'email_contacto' => $email_contacto
                  );
                  if (isset($_SESSION['file']['nombrearchivo']) && !empty($_SESSION['file']['nombrearchivo'])) {
                    $arrfieldscomma['logo'] = $_SESSION['file']['nombrearchivo'];
                  }
                  $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());
                  $q = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
                  $result = $pdo->query($q);
                  if (!$result) {
                      $arrjson = Util::error_general('Actualizando los datos de inestabilidad armada');
                  } else {
                      $arrjson = array('output' => array('valid' => true, 'id' => $id));
                  }
              } else {
                $arrjson = Util::error_general('Partido politico no existe');
              }
            } else {
                $q = "INSERT INTO " . $db->getTable('tbl_partidos_politicos') . " (nombre_partido, logo, resolucion, representante_legal, email_contacto)
                      VALUES (:nombre_partido, :logo, :resolucion, :representante_legal, :email_contacto)";

                $stmt = $pdo->prepare($q);
                $arrparam = array(
                    ':nombre_partido' => $nombre_partido,
                    ':logo' => $image,
                    ':resolucion' => $resolucion,
                    ':representante_legal' => $representante_legal,
                    ':email_contacto' => $email_contacto,
                );
                if ($stmt->execute($arrparam)) {
                    $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
                } else {
                    $arrjson = Util::error_general('Error al insertar el partido político.');
                }
            }
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Guardando partido politico');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    /**
     * Ejemplo de función para manejar la subida de un archivo de logo.
     * Deberías implementar esto según tus necesidades (validación, renombrado, guardado).
     * Esta función es solo un placeholder.
     *
     * @param array $fileData El array $_FILES para el logo.
     * @return string|null El nombre del archivo guardado o null si falla.
     */
    /*
    public static function handleLogoUpload($fileData)
    {
        // Define la ruta donde se guardarán los logos
        include_once "../../contants.php"; // Asegúrate de que WWW_ROOT_LOGOS esté definido aquí
        $ruta_logos = WWW_ROOT_LOGOS; // Ejemplo: /var/www/html/assets/logos/

        if (!file_exists($ruta_logos)) {
            mkdir($ruta_logos, 0777, true); // Crear el directorio si no existe
        }

        $name_parts = explode(".", $fileData['name']);
        $extension = mb_strtolower(end($name_parts)); // Obtener la extensión del archivo

        // Validar la extensión (ej. jpg, png, svg)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        if (!in_array($extension, $allowedExtensions)) {
            error_log("Intento de subida de logo con extensión no permitida: " . $extension);
            return null;
        }

        // Generar un nombre de archivo único para evitar colisiones
        $uniqueFileName = uniqid('logo_', true) . '.' . $extension;
        $destinationPath = $ruta_logos . $uniqueFileName;

        if (move_uploaded_file($fileData['tmp_name'], $destinationPath)) {
            return $uniqueFileName; // Devuelve solo el nombre del archivo para almacenar en la DB
        } else {
            error_log("Error al mover el archivo de logo a: " . $destinationPath);
            return null;
        }
    }
    */
}
