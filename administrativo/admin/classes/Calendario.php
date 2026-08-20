<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Calendario
{

    public function __construct() {}

    public static function getAll($rqst)
    {

        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        // Base query
        $sql = "SELECT id, titulo, etiqueta, fecha_inicio, fecha_fin, 
                       IF(dia = 1, 'Si', 'No') AS todo_el_dia, descripcion, 
                       repeticion, tipo, dtcreate_at, hora_inicio, hora_fin
                FROM " . $db->getTable('tbl_eventos') . " WHERE 1=1";

        $params = [];

        // Filtros opcionales
        if (!empty($rqst['id'])) {
            $sql .= " AND id = :id";
            $params[':id'] = intval($rqst['id']);
        }

        if (!empty($rqst['etiqueta'])) {
            $sql .= " AND etiqueta = :etiqueta";
            $params[':etiqueta'] = $rqst['etiqueta'];
        }

        if (!empty($rqst['fecha_inicio']) && !empty($rqst['fecha_fin'])) {
            $sql .= " AND fecha_inicio >= :fecha_inicio AND fecha_fin <= :fecha_fin";
            $params[':fecha_inicio'] = $rqst['fecha_inicio'];
            $params[':fecha_fin'] = $rqst['fecha_fin'];
        }

        $sql .= " ORDER BY fecha_inicio ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Construir la respuesta del calendario
        $responseEventosCalendario = [];

        foreach ($data as $evento) {
            // Determinar className según la etiqueta
            $etiqueta = strtolower($evento['etiqueta'] ?? '');
            switch ($etiqueta) {
                case 'personal':
                    $className = 'text-primary';
                    break;
                case 'trabajo':
                    $className = 'text-success';
                    break;
                case 'cita':
                    $className = 'text-danger';
                    break;
                case 'cumpleaños':
                    $className = 'text-primary';
                    break;
                case 'importante':
                    $className = 'text-danger';
                    break;
                case 'familiar':
                    $className = 'text-success';
                    break;
                default:
                    $className = 'text-info';
                    break;
            }

            $item = [
                'id' => $evento['id'],
                'tipo' => $evento['tipo'],
                'etiqueta' => $evento['etiqueta'],
                'dtcreate_at' => $evento['dtcreate_at'],
                'repeticion' => $evento['repeticion'],
                'fecha_inicio' => $evento['fecha_inicio'],
                'fecha_inicio' => $evento['fecha_inicio'],
                'hora_inicio' => $evento['hora_inicio'],
                'hora_fin' => $evento['hora_fin'],
                // Para el calendario
                'title' => $evento['titulo'],
                'start' => $evento['fecha_inicio'],
                'description' => $evento['descripcion'],
                'className' => $className,
                'allDay' => ($evento['todo_el_dia'] === 'Si')
            ];

            // Agrega fecha_fin si existe
            if (!empty($evento['fecha_fin'])) {
                $item['end'] = $evento['fecha_fin'];
            }

            $responseEventosCalendario[] = $item;
        }

        $db->closeConect();
        if ($data) {
            return [
                'output' => [
                    'valid' => true,
                    'response' => $responseEventosCalendario
                ]
            ];
        } else {
            return Util::error_no_result();
        }
    }

    public static function save($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        $titulo = isset($rqst['titulo']) ? trim($rqst['titulo']) : '';
        $etiqueta = isset($rqst['etiqueta']) ? trim($rqst['etiqueta']) : '';
        $fecha_inicio = isset($rqst['fecha_inicio']) ? $rqst['fecha_inicio'] : null;
        $fecha_fin = isset($rqst['fecha_fin']) ? $rqst['fecha_fin'] : null;
        $dia = isset($rqst['dia']) ? 1 : 0; // checkbox: marcado=1, desmarcado=0
        $descripcion = isset($rqst['descripcion']) ? trim($rqst['descripcion']) : '';
        $repeticion = isset($rqst['repeticion']) ? trim($rqst['repeticion']) : '';
        $tipo = isset($rqst['tipo']) ? trim($rqst['tipo']) : '';
        $hora_inicio = isset($rqst['hora_inicio']) ? trim($rqst['hora_inicio']) : '';
        $hora_fin = isset($rqst['hora_fin']) ? trim($rqst['hora_fin']) : '';

        $tbl_usuario_id =  $_SESSION['session_user']['id'];
        $db = new DbConection();
        $pdo = $db->openConect();


        if($titulo == "" || $etiqueta == "" || $fecha_inicio == "" || $fecha_fin == "" || $tbl_usuario_id == "") {
            return Util::error_missing_data();
        }
        // Validar que la fecha de inicio no sea mayor que la fecha de fin
        if ($fecha_inicio > $fecha_fin) {
            return Util::error_general('La fecha de inicio no puede ser mayor que la fecha de fin');
        }
        // Validar que la fecha de fin no sea menor que la fecha de inicio
        if ($fecha_fin < $fecha_inicio) {
            return Util::error_general('La fecha de fin no puede ser menor que la fecha de inicio');
        }
    
        if ($id > 0) {
            //actualiza la informacion
            $q = "SELECT id FROM " . $db->getTable('tbl_eventos') . " WHERE id = " . $id;
            $result = $pdo->query($q);
            if ($result) {
                $table = $db->getTable('tbl_eventos');
                $arrfieldscomma = array(
                    'titulo' => $titulo,
                    'etiqueta' => $etiqueta,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'dia' => $dia,
                    'descripcion' => $descripcion,
                    'repeticion' => $repeticion,
                    'tipo' => $tipo,
                    'tbl_usuario_id' => $tbl_usuario_id,
                    'hora_inicio' => $hora_inicio,
                    'hora_fin' => $hora_fin,
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
            if ($titulo != "" && $fecha_fin  != "") {
                $q = "INSERT INTO " . $db->getTable('tbl_eventos') . " (dtcreate_at, fecha_fin, titulo, etiqueta, fecha_inicio, tbl_usuario_id, descripcion, dia, repeticion, tipo, hora_inicio, hora_fin)
                VALUES ( " . Util::date_now_server() . ", :fecha_fin, :titulo, :etiqueta, :fecha_inicio, :tbl_usuario_id, :descripcion, :dia, :repeticion, :tipo, :hora_inicio, :hora_fin)";
                $result = $pdo->prepare($q);
                $arrparam = array(
                    ':fecha_fin' => $fecha_fin,
                    ':titulo' => $titulo,
                    ':etiqueta' => $etiqueta,
                    ':fecha_inicio' => $fecha_inicio,
                    ':tbl_usuario_id' => $tbl_usuario_id,
                    'repeticion' => $repeticion,
                    ':descripcion' => $descripcion,
                    ':dia' => $dia,
                    ':tipo' => $tipo,
                    ':hora_inicio' => $hora_inicio,
                    ':hora_fin' => $hora_fin
                );
                if ($result->execute($arrparam)) {
                    $arrjson = array('output' => array('valid' => true, 'response' => $pdo->lastInsertId()));
                } else {
                    $arrjson = Util::error_general(' Al guardar el evento');
                }
            } else {
                $arrjson = Util::error_missing_data();
            }
        }
        $db->closeConect();
        return $arrjson;
    }

    public static function delete($rqst)
    {

        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;

        $db = new DbConection();
        $pdo = $db->openConect();

        $sql = "DELETE FROM " . $db->getTable('tbl_eventos') . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $db->closeConect();
            return [
                'output' => [
                    'valid' => true,
                    'response' => 'Evento eliminado correctamente'
                ]
            ];
        } else {
            $db->closeConect();
            return Util::error_general('Error al eliminar el evento');
        }
    }

}
