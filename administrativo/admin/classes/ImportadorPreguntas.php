<?php

/**
 * Clase ImportadorPreguntas
 * Gestiona la importación de preguntas y sus opciones desde un archivo CSV.
 */
class ImportadorPreguntas
{
    /**
     * Importa preguntas y sus opciones desde un archivo CSV.
     * @param string $csvFilePath La ruta al archivo CSV.
     * @return array Resultado de la importación.
     */
    public static function importFromCsv($csvFilePath, $userId = null, $encuestaId = null)
    {
        $db = new DbConection();
        $pdo = $db->openConect();

        // Inicializar variables para auditoría
        $fileName = basename($csvFilePath);
        $importedCount = 0;
        $optionsCount = 0;
        $errors = [];
        $auditStatus = 'completado';
        $errorMessage = null;
        $userId = $userId ?? (isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : 1);
        $ipAddress = Util::get_real_ipaddress();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        try {
            if (!file_exists($csvFilePath)) {
                $auditStatus = 'error';
                $errorMessage = 'El archivo CSV no existe en la ruta especificada. Ruta: ' . $csvFilePath;
                throw new Exception($errorMessage);
            }

            $file = fopen($csvFilePath, 'r');
            if ($file === FALSE) {
                $auditStatus = 'error';
                $errorMessage = 'No se pudo abrir el archivo CSV.';
                throw new Exception($errorMessage);
            }

            // Leer la cabecera del CSV para mapear columnas
            $headers = fgetcsv($file);
            if ($headers === FALSE) {
                fclose($file);
                $auditStatus = 'error';
                $errorMessage = 'El archivo CSV está vacío o no tiene cabecera.';
                throw new Exception($errorMessage);
            }
            // Recortar espacios en los nombres de las cabeceras
            $headers = array_map('trim', $headers);
            $lineNum = 1;

            // Sentencia preparada para insertar preguntas
            $stmtPregunta = $pdo->prepare(
                "INSERT INTO " . $db->getTable('tbl_preguntas') . "
                (tbl_ficha_tecnica_encuesta_id, texto_pregunta, tipo_pregunta, orden, limite_respuesta_multiple, tbl_usuario_id, dtcreate)
                VALUES (:tbl_ficha_tecnica_encuesta_id, :texto_pregunta, :tipo_pregunta, :orden, :limite_respuesta_multiple, :tbl_usuario_id, NOW())"
            );

            // Sentencia preparada para insertar opciones de respuesta
            $stmtOpcion = $pdo->prepare(
                "INSERT INTO " . $db->getTable('tbl_opciones_respuesta') . " 
                (tbl_pregunta_id, texto_opcion, orden, dtcreate) 
                VALUES (:tbl_pregunta_id, :texto_opcion, :orden, NOW())"
            );

            while (($rowData = fgetcsv($file)) !== FALSE) {
                $lineNum++;
                $pdo->beginTransaction();

                try {
                    // Asegurarse de que $rowData tenga la misma cantidad de elementos que $headers
                    if (count($headers) != count($rowData)) {
                        throw new Exception("Error de formato en la línea {$lineNum}: El número de columnas no coincide con la cabecera.");
                    }
                    $data = array_combine($headers, $rowData);

                    // CAMPOS ACTUALIZADOS
                    $tbl_ficha_tecnica_encuesta_id = isset($data['tbl_ficha_tecnica_encuesta_id']) ? intval($data['tbl_ficha_tecnica_encuesta_id']) : 0;
                    $texto_pregunta = isset($data['texto_pregunta']) ? trim($data['texto_pregunta']) : '';
                    $tipo_pregunta = isset($data['tipo_pregunta']) ? trim($data['tipo_pregunta']) : null;
                    $orden = isset($data['orden']) ? intval($data['orden']) : null;
                    $tbl_usuario_id = isset($data['tbl_usuario_id']) ? intval($data['tbl_usuario_id']) : 1;

                    if ($tbl_ficha_tecnica_encuesta_id === 0 || empty($texto_pregunta)) {
                        throw new Exception("Faltan datos obligatorios para la pregunta (ID de Ficha Técnica o Texto de Pregunta) en la línea {$lineNum}.");
                    }

                    // Inserción de pregunta
                    $stmtPregunta->bindValue(':tbl_ficha_tecnica_encuesta_id', $tbl_ficha_tecnica_encuesta_id, PDO::PARAM_INT);
                    $stmtPregunta->bindValue(':texto_pregunta', $texto_pregunta, PDO::PARAM_STR);
                    $stmtPregunta->bindValue(':tipo_pregunta', $tipo_pregunta, is_null($tipo_pregunta) ? PDO::PARAM_NULL : PDO::PARAM_STR);
                    $stmtPregunta->bindValue(':orden', $orden, is_null($orden) ? PDO::PARAM_NULL : PDO::PARAM_INT);
                    $stmtPregunta->bindValue(':limite_respuesta_multiple', 1, PDO::PARAM_INT); // Valor por defecto
                    $stmtPregunta->bindValue(':tbl_usuario_id', $tbl_usuario_id, PDO::PARAM_INT);

                    if (!$stmtPregunta->execute()) {
                        throw new Exception("Error al insertar la pregunta '{$texto_pregunta}' en la línea {$lineNum}. Detalles: " . implode(" ", $stmtPregunta->errorInfo()));
                    }
                    $preguntaId = $pdo->lastInsertId();

                    // Insertar opciones de respuesta
                    $opcionOrden = 0;
                    for ($i = 1; $i <= 5; $i++) { // Asume hasta 5 opciones por pregunta
                        $opcionKey = 'opcion_' . $i;
                        if (isset($data[$opcionKey]) && !empty(trim($data[$opcionKey]))) {
                            $opcionOrden++;
                            $texto_opcion = trim($data[$opcionKey]);

                            $stmtOpcion->bindValue(':tbl_pregunta_id', $preguntaId, PDO::PARAM_INT);
                            $stmtOpcion->bindValue(':texto_opcion', $texto_opcion, PDO::PARAM_STR);
                            $stmtOpcion->bindValue(':orden', $opcionOrden, PDO::PARAM_INT);

                            if (!$stmtOpcion->execute()) {
                                throw new Exception("Error al insertar la opción '{$texto_opcion}' para la pregunta en la línea {$lineNum}. Detalles: " . implode(" ", $stmtOpcion->errorInfo()));
                            }
                            $optionsCount++;
                        }
                    }

                    $pdo->commit();
                    $importedCount++;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $errors[] = $e->getMessage();
                }
            }

            fclose($file);

            // Si hay errores pero se importaron algunas preguntas, el estado es parcial
            if (!empty($errors) && $importedCount > 0) {
                $auditStatus = 'parcial';
                $errorMessage = implode('; ', $errors);
            }

            // Registrar la auditoría
            self::registrarAuditoria($pdo, $userId, $fileName, $importedCount, $optionsCount, $ipAddress, $userAgent, $auditStatus, $errorMessage, $encuestaId);

            if (empty($errors)) {
                return array('output' => array('valid' => true, 'response' => "Importación completada. {$importedCount} preguntas importadas."));
            } else {
                throw new Exception("Importación completada con errores. {$importedCount} preguntas importadas. Errores: " . implode('; ', $errors));
            }
        } catch (Exception $e) {
            // Si ocurrió una excepción general, registrar la auditoría con estado de error
            if ($auditStatus !== 'error') {
                $auditStatus = 'error';
                $errorMessage = $e->getMessage();
                self::registrarAuditoria($pdo, $userId, $fileName, $importedCount, $optionsCount, $ipAddress, $userAgent, $auditStatus, $errorMessage, $encuestaId);
            }
            return Util::error_general($e->getMessage());
        } finally {
            $db->closeConect();
        }
    }

    public static function uploadFile()
    {
        // Manejo del archivo cargado
        if (isset($_SESSION['file']['nombrearchivo']) && !empty($_SESSION['file']['nombrearchivo'])) {
            $csvFile = $_SESSION['file']['nombrearchivo'];
            // Limpiar la sesión después de usar el archivo para evitar reuso accidental
            // unset($_SESSION['pms_archivo']['nombrearchivo']);
        } else {
            return array('output' => array('valid' => false, 'response' => array('content' => "No se ha seleccionado ningún archivo.")));
        }

        // Obtener el ID del usuario actual desde la sesión
        $userId = SessionData::getUserId();

        // Obtener el ID de la ficha técnica de encuesta si está disponible en la sesión
        $fichaTecnicaId = isset($_SESSION['ficha_tecnica_encuesta_id']) ? $_SESSION['ficha_tecnica_encuesta_id'] : null;

        // Importar el archivo CSV
        $csvFilePath = '../../assets/img/admin/' . $csvFile;
        return ImportadorPreguntas::importFromCsv($csvFilePath, $userId, $fichaTecnicaId);
    }
    
    /**
     * Registra la información de auditoría de la importación de preguntas
     * @param PDO $pdo Conexión a la base de datos
     * @param int $userId ID del usuario que realizó la importación
     * @param string $fileName Nombre del archivo importado
     * @param int $preguntasCount Cantidad de preguntas importadas
     * @param int $opcionesCount Cantidad de opciones importadas
     * @param string $ipAddress Dirección IP del usuario
     * @param string $userAgent Navegador del usuario
     * @param string $status Estado de la importación (completado, error, parcial)
     * @param string $errorMsg Mensaje de error si hubo alguno
     * @param int $encuestaId ID de la encuesta relacionada (opcional)
     * @return bool Resultado de la operación
     */
    private static function registrarAuditoria($pdo, $userId, $fileName, $preguntasCount, $opcionesCount, $ipAddress, $userAgent, $status, $errorMsg, $encuestaId = null)
    {
        try {
            $query = "INSERT INTO tbl_auditoria_preguntas
                    (tbl_usuario_id, archivo_nombre, cantidad_preguntas, cantidad_opciones,
                    fecha_subida, ip_address, navegador, estado, mensaje_error, tbl_ficha_tecnica_encuesta_id)
                    VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)"; 
                    
            $stmt = $pdo->prepare($query);
            return $stmt->execute([
                $userId,
                $fileName,
                $preguntasCount,
                $opcionesCount,
                $ipAddress,
                $userAgent,
                $status,
                $errorMsg,
                $encuestaId
            ]);
        } catch (Exception $e) {
            // Si falla el registro de auditoría, solo lo registramos en el log pero no interrumpimos el proceso
            error_log("Error al registrar auditoría: " . $e->getMessage());
            return false;
        }
    }
}
