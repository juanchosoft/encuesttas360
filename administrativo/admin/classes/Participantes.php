<?php
class Participantes
{
    public function __construct()
    {

    }

    public static function getAll($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $cargoPublicoId = isset($rqst['cargoPublicoId']) ? intval($rqst['cargoPublicoId']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT 
                p.id, 
                p.tbl_cargo_publico_id, 
                p.nombre_completo, 
                p.codigo_departamento, 
                p.codigo_municipio, 
                p.dtcreate, 
                p.foto, 
                p.habilitado,
                p.numero_candidato,
                p.favorito,
                p.puntos,
                p.cliente,
                p.modalidad,
                cp.nombre AS cargo_publico, 
                cp.sigla AS sigla_cargo,
                d.departamento AS nombre_departamento,
                c.municipio AS nombre_municipio,
                GROUP_CONCAT(pxp.tbl_partido_politico_id) AS partidoPoliticoIds,
                GROUP_CONCAT(pp.nombre_partido SEPARATOR ', ') AS nombres_partidos
              FROM " . $db->getTable('tbl_participantes') . " p
              LEFT JOIN " . $db->getTable('tbl_participantes_x_partidos_politicos') . " pxp
                ON p.id = pxp.tbl_participante_id
              LEFT JOIN " . $db->getTable('tbl_partidos_politicos') . " pp
                ON pxp.tbl_partido_politico_id = pp.id
              LEFT JOIN " . $db->getTable('tbl_cargos_publicos') . " cp
                ON p.tbl_cargo_publico_id = cp.id
              LEFT JOIN " . $db->getTable('tbl_departamentos') . " d
                ON p.codigo_departamento = d.codigo_departamento
              LEFT JOIN " . $db->getTable('tbl_ciudades') . " c
                ON p.codigo_municipio = c.codigo_muncipio";
        $params = [];

        if ($id > 0) {
            $q .= " WHERE p.id = :id";
            $params[':id'] = $id;
        }

        // Filtrar por cargo público si se proporciona
        if( $cargoPublicoId > 0){
            $q .= " WHERE p.tbl_cargo_publico_id = :cargoPublicoId";
            $params[':cargoPublicoId'] = $cargoPublicoId;
        }

        $q .= " GROUP BY p.id";

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
            error_log("Error en getAll Participante: " . $e->getMessage());
            $arrjson = Util::error_general('Error al obtener los datos de participantes.');
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $tbl_cargo_publico_id = isset($rqst['cargo_publico_id']) ? intval($rqst['cargo_publico_id']) : 0;
        $nombre_completo = isset($rqst['nombre_completo']) ? trim($rqst['nombre_completo']) : '';
        $codigo_departamento = isset($rqst['codigo_departamento']) ? ($rqst['codigo_departamento']) : null;
        $codigo_municipio = isset($rqst['codigo_municipio']) ? ($rqst['codigo_municipio']) : null;
        $habilitado = isset($rqst['habilitado']) ? trim($rqst['habilitado']) : '';
        $foto = null;
        $numero_candidato = isset($rqst['numero_candidato']) ? intval($rqst['numero_candidato']) : 0;
        $favorito = isset($rqst['favorito']) ? trim($rqst['favorito']) : '';
        $puntos = isset($rqst['puntos']) ? intval($rqst['puntos']) : 0;
        $cliente = isset($rqst['cliente']) ? trim($rqst['cliente']) : '';
        $modalidad = isset($rqst['modalidad']) ? trim($rqst['modalidad']) : '';

        // Recibe la cadena de IDs de partidos separados por comas y la convierte en un array.
        $partidoPoliticoIds = isset($rqst['partidoPoliticoIds']) && $rqst['partidoPoliticoIds'] !== '' 
                               ? array_map('intval', explode(',', $rqst['partidoPoliticoIds'])) 
                               : [];

        $cargosSinUbicacion = [1, 2]; // 1 para Presidente, 2 para Senador.
        // IDs de cargos que solo requieren departamento (municipio null)
        $cargosSoloDepartamento = [4, 5]; // 4 para Gobernador, 5 para Diputado.
        // IDs de cargos que requieren ambos (departamento y municipio)
        $cargosDepartamentoMunicipio = [6, 7]; // 6 para Alcalde, 7 para Concejal.

        $codigo_departamento = isset($rqst['tbl_departamento_id']) ? ($rqst['tbl_departamento_id']) : null;
        $codigo_municipio = isset($rqst['tbl_municipio_id']) ? ($rqst['tbl_municipio_id']) : null;

        if (in_array($tbl_cargo_publico_id, $cargosSinUbicacion)) {
            $codigo_departamento = null;
            $codigo_municipio = null;
        } elseif (in_array($tbl_cargo_publico_id, $cargosSoloDepartamento)) {
            $codigo_municipio = null;
            $codigo_departamento = isset($rqst['codigo_departamento']) ? ($rqst['codigo_departamento']) : null;
        } elseif (in_array($tbl_cargo_publico_id, $cargosDepartamentoMunicipio)) {
            $codigo_departamento = isset($rqst['codigo_departamento']) ? ($rqst['codigo_departamento']) : null;
            $codigo_municipio = isset($rqst['codigo_municipio']) ? ($rqst['codigo_municipio']) : null;
        }

        // Solo validamos si los campos no fueron anulados a null.
        if (!is_null($codigo_departamento) && empty($codigo_departamento)) {
            return Util::error_missing_data('El departamento es requerido para este cargo.');
        }
        if (!is_null($codigo_municipio) && empty($codigo_municipio)) {
            return Util::error_missing_data('El municipio es requerido para este cargo.');
        }

        // Validaciones básicas.
        if (empty($nombre_completo)) {
            return Util::error_missing_data_description('El nombre completo del participante es requerido.');
        }
        if ($tbl_cargo_publico_id == 0) {
            return Util::error_missing_data_description('El cargo público es requerido.');
        }
        if (empty($partidoPoliticoIds)) {
            return Util::error_missing_data_description('Debe seleccionar al menos un Partido Político.');
        }

        if (empty($cliente)) {
            return Util::error_missing_data_description('El campo "Es Cliente" es requerido.');
        }

        if (empty($favorito)) {
            return Util::error_missing_data_description('El campo "Es Favorito" es requerido.');
        }

        // Manejo de la imagen cargada
        $foto_blob = null;
        if (isset($_SESSION['file']['nombrearchivo']) && !empty($_SESSION['file']['nombrearchivo'])) {
            $foto = $_SESSION['file']['nombrearchivo'];

            // Obtener el contenido binario de la foto
            if (isset($_SESSION['file']['contenidooarchivo']) && !empty($_SESSION['file']['contenidooarchivo'])) {
                $foto_blob = $_SESSION['file']['contenidooarchivo'];
            }

            // Limpiar la sesión después de usar el archivo para evitar reuso accidental
            unset($_SESSION['file']);
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $pdo->beginTransaction();

        try {
            $participante_id = $id;

            if ($id > 0) {
                $table = $db->getTable('tbl_participantes');
                $arrfieldscomma = array(
                    'tbl_cargo_publico_id' => $tbl_cargo_publico_id,
                    'nombre_completo' => $nombre_completo,
                    'codigo_departamento' => $codigo_departamento,
                    'codigo_municipio' => $codigo_municipio,
                    'habilitado' => $habilitado,
                    'numero_candidato' => $numero_candidato,
                    'favorito' => $favorito,
                    'puntos'  => $puntos,
                    'cliente' => $cliente,
                    'modalidad' => $modalidad
                );

                // Solo añadir la 'foto' a la actualización si se subió una nueva
                if ($foto !== null) {
                    $arrfieldscomma['foto'] = $foto;
                }

                $arrfieldsnocomma = array('dtcreate' => Util::date_now_server());

                // Si hay foto_blob, usar PDO prepared statement para actualizarla
                if ($foto_blob !== null) {
                    $q = "UPDATE " . $table . " SET
                            tbl_cargo_publico_id = :cargo_publico_id,
                            nombre_completo = :nombre_completo,
                            codigo_departamento = :codigo_departamento,
                            codigo_municipio = :codigo_municipio,
                            habilitado = :habilitado,
                            numero_candidato = :numero_candidato,
                            favorito = :favorito,
                            puntos = :puntos,
                            cliente = :cliente,
                            modalidad = :modalidad,
                            foto = :foto,
                            foto_blob = :foto_blob,
                            dtcreate = NOW()
                          WHERE id = :id";

                    $stmt = $pdo->prepare($q);
                    $stmt->bindValue(':cargo_publico_id', $tbl_cargo_publico_id, PDO::PARAM_INT);
                    $stmt->bindValue(':nombre_completo', $nombre_completo, PDO::PARAM_STR);
                    $stmt->bindValue(':codigo_departamento', $codigo_departamento);
                    $stmt->bindValue(':codigo_municipio', $codigo_municipio);
                    $stmt->bindValue(':habilitado', $habilitado, PDO::PARAM_STR);
                    $stmt->bindValue(':numero_candidato', $numero_candidato);
                    $stmt->bindValue(':favorito', $favorito, PDO::PARAM_STR);
                    $stmt->bindValue(':puntos', $puntos, PDO::PARAM_INT);
                    $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
                    $stmt->bindValue(':modalidad', $modalidad, PDO::PARAM_STR);
                    $stmt->bindValue(':foto', $foto, PDO::PARAM_STR);
                    $stmt->bindValue(':foto_blob', $foto_blob, PDO::PARAM_LOB);
                    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

                    $result = $stmt->execute();
                } else {
                    // Si no hay foto_blob nueva, usar el método tradicional
                    $q = Util::make_query_update($table, "id = '$id'", $arrfieldscomma, $arrfieldsnocomma);
                    $result = $pdo->query($q);
                }

                if (!$result) {
                    throw new Exception('Error al actualizar el candidato.');
                }

                $delete_q = "DELETE FROM " . $db->getTable('tbl_participantes_x_partidos_politicos') . " WHERE tbl_participante_id = :participante_id";
                $delete_stmt = $pdo->prepare($delete_q);
                $delete_stmt->bindValue(':participante_id', $participante_id, PDO::PARAM_INT);
                if (!$delete_stmt->execute()) {
                    throw new Exception('Error al eliminar relaciones de partidos existentes.');
                }

            } else {
                $q = "INSERT INTO " . $db->getTable('tbl_participantes') . " (tbl_cargo_publico_id, nombre_completo, codigo_departamento, codigo_municipio, foto, foto_blob, habilitado, numero_candidato, favorito, puntos, cliente, modalidad)
                    VALUES (:tbl_cargo_publico_id, :nombre_completo, :codigo_departamento, :codigo_municipio, :foto, :foto_blob, :habilitado, :numero_candidato, :favorito, :puntos, :cliente, :modalidad)";

                $stmt = $pdo->prepare($q);
                $stmt->bindValue(':tbl_cargo_publico_id', $tbl_cargo_publico_id, PDO::PARAM_INT);
                $stmt->bindValue(':nombre_completo', $nombre_completo, PDO::PARAM_STR);
                $stmt->bindValue(':codigo_departamento', $codigo_departamento);
                $stmt->bindValue(':codigo_municipio', $codigo_municipio);
                $stmt->bindValue(':foto', $foto, PDO::PARAM_STR);
                $stmt->bindValue(':foto_blob', $foto_blob, PDO::PARAM_LOB);
                $stmt->bindValue(':habilitado', $habilitado, PDO::PARAM_STR);
                $stmt->bindValue(':numero_candidato', $numero_candidato);
                $stmt->bindValue(':favorito', $favorito, PDO::PARAM_STR);
                $stmt->bindValue(':puntos', $puntos, PDO::PARAM_INT);
                $stmt->bindValue(':cliente', $cliente, PDO::PARAM_STR);
                $stmt->bindValue(':modalidad', $modalidad, PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    throw new Exception('Error al guardar la información participante.');
                }
                $participante_id = $pdo->lastInsertId();
            }

            $insert_relation_q = "INSERT INTO " . $db->getTable('tbl_participantes_x_partidos_politicos') . " (tbl_partido_politico_id, tbl_participante_id)
                                  VALUES (:tbl_partido_politico_id, :tbl_participante_id)";
            $insert_relation_stmt = $pdo->prepare($insert_relation_q);

            foreach ($partidoPoliticoIds as $partidoId) {
                $insert_relation_stmt->bindValue(':tbl_partido_politico_id', $partidoId, PDO::PARAM_INT);
                $insert_relation_stmt->bindValue(':tbl_participante_id', $participante_id, PDO::PARAM_INT);
                if (!$insert_relation_stmt->execute()) {
                    throw new Exception('Error al guardar los partidos políticos: ' . $partidoId);
                }
            }
            $pdo->commit();
            $arrjson = array('output' => array('valid' => true, 'id' => $participante_id));

        } catch (Exception $e) {
            $pdo->rollBack();
            $arrjson = Util::error_general('Error al guardar la información del participante: '  . $e->getMessage());
        } finally {
            $db->closeConect();
        }

        return $arrjson;
    }
}
