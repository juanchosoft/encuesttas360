<?php

/**
 * Clase ImportadorFormulas
 *
 * Gestiona la importación masiva de fórmulas desde CSV
 *
 * @package EstadisticasGobierno
 * @subpackage Classes
 */
class ImportadorFormulas
{
    /**
     * Importa fórmulas desde datos JSON
     *
     * @param array $rqst Request con el JSON de fórmulas
     * @return array Array con valid y response
     */
    public static function importar($rqst)
    {
        if (empty($rqst['formulas'])) {
            return Util::error_missing_data_description("No se recibieron datos de fórmulas");
        }

        $formulas = json_decode($rqst['formulas'], true);

        if (!is_array($formulas) || count($formulas) === 0) {
            return Util::error_missing_data_description("El formato de datos es inválido");
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $success = 0;
        $errors = 0;
        $duplicates = 0;
        $errorMessages = [];

        try {
            $userId = SessionData::getUserId();

            // Preparar statement de verificación de duplicados
            $stmtCheck = $pdo->prepare(
                "SELECT id FROM " . $db->getTable('tbl_formulas') . " WHERE sigla = :sigla"
            );

            // Preparar statement de inserción
            $stmtInsert = $pdo->prepare(
                "INSERT INTO " . $db->getTable('tbl_formulas') . "
                (indicador, sigla, tipo_indicador, formula, explicacion, observaciones,
                 enunciado_antes, enunciado_ahora, notas_adicionales,
                 habilitado, tbl_usuario_id, dtcreate)
                VALUES
                (:indicador, :sigla, :tipo_indicador, :formula, :explicacion, :observaciones,
                 :enunciado_antes, :enunciado_ahora, :notas_adicionales,
                 'si', :tbl_usuario_id, NOW())"
            );

            foreach ($formulas as $index => $formula) {
                // Validar campos obligatorios
                if (empty($formula['indicador']) || empty($formula['sigla']) || empty($formula['formula'])) {
                    $errors++;
                    $errorMessages[] = "Fila " . ($index + 1) . ": Faltan campos obligatorios (indicador, sigla, fórmula)";
                    continue;
                }

                // Verificar duplicados por sigla
                $stmtCheck->execute([':sigla' => $formula['sigla']]);
                if ($stmtCheck->rowCount() > 0) {
                    $duplicates++;
                    $errorMessages[] = "Fila " . ($index + 1) . ": Sigla '" . $formula['sigla'] . "' ya existe";
                    continue;
                }

                // Insertar fórmula
                try {
                    $stmtInsert->execute([
                        ':indicador' => $formula['indicador'],
                        ':sigla' => $formula['sigla'],
                        ':tipo_indicador' => $formula['tipo_indicador'] ?? null,
                        ':formula' => $formula['formula'],
                        ':explicacion' => $formula['explicacion'] ?? null,
                        ':observaciones' => $formula['observaciones'] ?? null,
                        ':enunciado_antes' => $formula['enunciado_antes'] ?? null,
                        ':enunciado_ahora' => $formula['enunciado_ahora'] ?? null,
                        ':notas_adicionales' => $formula['notas_adicionales'] ?? null,
                        ':tbl_usuario_id' => $userId
                    ]);

                    $success++;

                } catch (PDOException $e) {
                    $errors++;
                    $errorMessages[] = "Fila " . ($index + 1) . ": Error al insertar - " . $e->getMessage();
                }
            }

            $db->closeConect();

            $arrjson = array(
                'output' => array(
                    'valid' => true,
                    'response' => array(
                        'success' => $success,
                        'errors' => $errors,
                        'duplicates' => $duplicates,
                        'total' => count($formulas),
                        'messages' => $errorMessages
                    )
                )
            );

            return $arrjson;

        } catch (Exception $e) {
            $db->closeConect();
            return Util::error_general("Error en la importación: " . $e->getMessage());
        }
    }

    /**
     * Valida el formato de un archivo CSV
     *
     * @param string $csvContent Contenido del CSV
     * @param boolean $hasHeader Si tiene encabezado
     * @return array Array con valid y errores
     */
    public static function validarCSV($csvContent, $hasHeader = true)
    {
        $errors = [];
        $lines = explode("\n", $csvContent);

        if (count($lines) < 2) {
            $errors[] = "El archivo está vacío o tiene muy pocas filas";
            return ['valid' => false, 'errors' => $errors];
        }

        $startIndex = $hasHeader ? 1 : 0;
        $minColumns = 3; // indicador, sigla, formula

        for ($i = $startIndex; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;

            $values = str_getcsv($line);

            if (count($values) < $minColumns) {
                $errors[] = "Fila " . ($i + 1) . ": Faltan columnas (mínimo 3: indicador, sigla, fórmula)";
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }

    /**
     * Genera un CSV de ejemplo
     *
     * @return string Contenido del CSV de ejemplo
     */
    public static function generarPlantilla()
    {
        $csv = "INDICADOR,SIGLA,TIPO_INDICADOR,FORMULA,EXPLICACION,OBSERVACIONES,ENUNCIADO_ANTES,ENUNCIADO_AHORA,NOTAS_ADICIONALES\n";
        $csv .= "\"Índice de Favorabilidad\",\"IF\",\"Electoral\",\"IF = (Respuestas Positivas / Total Respuestas) * 100\",\"Mide el porcentaje de respuestas favorables\",\"Se utiliza para medir aceptación\",\"¿Qué tan favorable?\",\"¿Cuál es su nivel de favorabilidad?\",\"Fundamental para tendencias\"\n";
        $csv .= "\"Margen de Error\",\"ME\",\"Estadístico\",\"ME = Z * √(p * (1-p) / n)\",\"Calcula el margen de error estadístico\",\"95% de confianza (Z=1.96)\",\"Calcular margen\",\"Determinar el margen de error\",\"A mayor muestra, menor error\"\n";
        $csv .= "\"Intención de Voto\",\"IV\",\"Electoral\",\"IV = (Votantes Candidato / Total Decididos) * 100\",\"Porcentaje de intención de voto\",\"No incluye indecisos\",\"¿Por quién votaría?\",\"¿Cuál es su intención de voto?\",\"Considerar margen de error\"\n";

        return $csv;
    }
}
