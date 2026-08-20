<?php

/**
 * Clase que contiene todas las operaciones utilizadas sobre la base de datos
 * @author SPIDERSOFTWARE
 */
class Visitas
{
  public function __construct() {}

  public static function getAll($rqst)
  {
    $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
    $tipo = $rqst['tipo'] ?? '';
    $tbl_municipio_id = $rqst['tbl_municipio_id'] ?? '';

    $tipoUsuario = SessionData::getUserType();
    $codigoMunicipio = SessionData::getCodigoMunicipio();

    $db  = new DbConection();
    $pdo = $db->openConect();

    $q = "SELECT v.*,
                 c.municipio AS municipio,
                 s.secretaria
          FROM " . $db->getTable('tbl_visitas') . " v
          INNER JOIN " . $db->getTable('tbl_ciudades_accion_unificada') . " c
            ON v.tbl_municipio_id = c.codigo_muncipio
          LEFT JOIN " . $db->getTable('tbl_secretarias') . " s
            ON v.tbl_secretarias_id = s.id
          WHERE 1=1";
    $params = [];

    if ($id > 0) {
      $q .= " AND v.id = :id";
      $params[':id'] = $id;
    }
    if (!empty($tbl_municipio_id)) {
      $q .= " AND v.tbl_municipio_id = :tbl_municipio_id";
      $params[':tbl_municipio_id'] = $tbl_municipio_id;
    }
    if (!empty($tipo)) {
      $q .= " AND v.tipo = :tipo";
      $params[':tipo'] = $tipo;
    }

    // Filtro por rol (usa alias 'c')
    if (Util::Auxiliar_Alcalde() == $tipoUsuario || Util::Alcalde() == $tipoUsuario) {
      $q .= " AND c.codigo_muncipio = :codigo_muncipio";
      $params[':codigo_muncipio'] = $codigoMunicipio;
    }

    $q .= " ORDER BY v.date DESC";

    $stmt = $pdo->prepare($q);
    $stmt->execute($params);
    $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $db->closeConect();
    return $arr ? ['output' => ['valid' => true, 'response' => $arr]] : Util::error_no_result();
  }

  public static function getAllCom($rqst)
  {
    $id = intval($rqst['id'] ?? 0);
    $tbl_municipio_id = $rqst['tbl_municipio_id'] ?? '';

    $db  = new DbConection();
    $pdo = $db->openConect();

    $q = "SELECT v.*, c.municipio, s.secretaria
          FROM " . $db->getTable('tbl_visitas') . " v
          INNER JOIN " . $db->getTable('tbl_ciudades') . " c
            ON v.tbl_municipio_id = c.codigo_muncipio
          LEFT JOIN " . $db->getTable('tbl_secretarias') . " s
            ON v.tbl_secretarias_id = s.id
          WHERE v.tipo = 'Compromiso'";
    $params = [];

    if ($id > 0) {
      $q .= " AND v.id = :id";
      $params[':id'] = $id;
    }
    if (!empty($tbl_municipio_id)) {
      $q .= " AND v.tbl_municipio_id = :tbl_municipio_id";
      $params[':tbl_municipio_id'] = $tbl_municipio_id;
    }

    $q .= " ORDER BY v.id DESC";

    $st = $pdo->prepare($q);
    $st->execute($params);
    $arr = $st->fetchAll(PDO::FETCH_ASSOC);

    $db->closeConect();
    return $arr ? ['output' => ['valid' => true, 'response' => $arr]] : Util::error_no_result();
  }

  public static function save($rqst)
  {
    $id = intval($rqst['id'] ?? 0);
    $tbl_departamento_id = intval($rqst['tbl_departamento_id'] ?? 0);
    $tbl_municipio_id    = intval($rqst['tbl_municipio_id'] ?? 0);

    // Fecha segura (acepta d/m/Y o Y-m-d)
    $dateIn = trim($rqst['date'] ?? '');
    if ($dateIn !== '') {
      if (preg_match('#^\d{2}/\d{2}/\d{4}$#', $dateIn)) {
        [$d,$m,$Y] = explode('/', $dateIn);
        $date = sprintf('%04d-%02d-%02d', (int)$Y, (int)$m, (int)$d);
      } elseif (preg_match('#^\d{4}-\d{2}-\d{2}$#', $dateIn)) {
        $date = $dateIn;
      } else {
        return Util::error_general('Formato de fecha no válido.');
      }
    } else {
      $date = '';
    }

    $tipo          = $rqst['tipo'] ?? '';
    $entidad       = $rqst['entidad'] ?? '';
    $cargo         = $rqst['cargo'] ?? '';
    $beneficiario  = $rqst['beneficiario'] ?? '';
    $provincia     = $rqst['provincia'] ?? '';
    if ($provincia === '' && !empty($rqst['subregion'])) $provincia = $rqst['subregion']; // mapeo subregión → provincia
    $observaciones = $rqst['observaciones'] ?? '';
    $compromisos   = $rqst['compromisos'] ?? '';
    $compromisopac = $rqst['compromisopac'] ?? '';
    $respuesta     = $rqst['respuesta'] ?? '';
    $tbl_secretarias_id = $rqst['tbl_secretarias_id'] ?? null;

    $tbl_usuario_id = intval($_SESSION['session_user']['id'] ?? 0);
    $imgNew = $_SESSION['file']['nombrearchivo'] ?? ''; // si no suben nueva, puede venir vacío

    // Reglas de rol/municipio
    $tipoUsuario     = SessionData::getUserType();
    $codigoMunicipio = SessionData::getCodigoMunicipio();
    if (Util::Auxiliar_Alcalde() == $tipoUsuario || Util::Alcalde() == $tipoUsuario || Util::Secretario_Despacho() == $tipoUsuario) {
      if ($tbl_municipio_id !== $codigoMunicipio) {
        return Util::error_general("Debe seleccionar el municipio correspondiente al cual pertenece.");
      }
    }

    $db  = new DbConection();
    $pdo = $db->openConect();

    if ($id > 0) {
      // UPDATE (parametrizado)
      $st0 = $pdo->prepare("SELECT img FROM " . $db->getTable('tbl_visitas') . " WHERE id = :id");
      $st0->execute([':id' => $id]);
      $row0 = $st0->fetch(PDO::FETCH_ASSOC);
      if (!$row0) {
        $db->closeConect();
        return Util::error_general('No se encontró la visita.');
      }

      $imgToSave = $imgNew !== '' ? $imgNew : ($row0['img'] ?? '');

      $q = "UPDATE " . $db->getTable('tbl_visitas') . "
            SET date=:date, tipo=:tipo, entidad=:entidad, provincia=:provincia,
                cargo=:cargo, beneficiario=:beneficiario, observaciones=:observaciones,
                compromisos=:compromisos, compromisopac=:compromisopac, respuesta=:respuesta,
                tbl_secretarias_id=:tbl_secretarias_id, tbl_departamento_id=:tbl_departamento_id,
                tbl_municipio_id=:tbl_municipio_id, img=:img
            WHERE id=:id";
      $params = [
        ':date'               => $date,
        ':tipo'               => $tipo,
        ':entidad'            => $entidad,
        ':provincia'          => $provincia,
        ':cargo'              => $cargo,
        ':beneficiario'       => $beneficiario,
        ':observaciones'      => $observaciones,
        ':compromisos'        => $compromisos,
        ':compromisopac'      => $compromisopac,
        ':respuesta'          => $respuesta,
        ':tbl_secretarias_id' => $tbl_secretarias_id,
        ':tbl_departamento_id'=> $tbl_departamento_id,
        ':tbl_municipio_id'   => $tbl_municipio_id,
        ':img'                => $imgToSave,
        ':id'                 => $id
      ];
      $st = $pdo->prepare($q);
      if ($st->execute($params)) {
        $db->closeConect();
        return ['output' => ['valid' => true, 'id' => $id, 'img' => $imgToSave]];
      } else {
        $db->closeConect();
        return Util::error_general('Actualizando los datos de la visita');
      }

    } else {
      // INSERT — valida obligatorios (AND)
      if ($date === '' || $entidad === '' || $tbl_departamento_id <= 0 || $beneficiario === '') {
        $db->closeConect();
        return Util::error_general('Faltan campos obligatorios: fecha, tipo de visita, departamento, estado.');
      }

      $q = "INSERT INTO " . $db->getTable('tbl_visitas') . "
            (created_at, date, entidad, cargo, tipo, beneficiario, provincia,
             observaciones, compromisos, compromisopac, respuesta, img,
             tbl_secretarias_id, tbl_departamento_id, tbl_municipio_id, tbl_usuario_id)
            VALUES (:created_at, :date, :entidad, :cargo, :tipo, :beneficiario, :provincia,
                    :observaciones, :compromisos, :compromisopac, :respuesta, :img,
                    :tbl_secretarias_id, :tbl_departamento_id, :tbl_municipio_id, :tbl_usuario_id)";
      $st = $pdo->prepare($q);
      $ok = $st->execute([
        ':created_at'         => Util::date_now_server(),
        ':date'               => $date,
        ':entidad'            => $entidad,
        ':cargo'              => $cargo,
        ':tipo'               => $tipo,
        ':beneficiario'       => $beneficiario,
        ':provincia'          => $provincia,
        ':observaciones'      => $observaciones,
        ':compromisos'        => $compromisos,
        ':compromisopac'      => $compromisopac,
        ':respuesta'          => $respuesta,
        ':img'                => $imgNew,
        ':tbl_secretarias_id' => $tbl_secretarias_id,
        ':tbl_departamento_id'=> $tbl_departamento_id,
        ':tbl_municipio_id'   => $tbl_municipio_id,
        ':tbl_usuario_id'     => $tbl_usuario_id,
      ]);

      if ($ok) {
        $idNew = $pdo->lastInsertId();
        $db->closeConect();
        return ['output' => ['valid' => true, 'response' => $idNew]];
      } else {
        $db->closeConect();
        return Util::error_general('Ingresando los datos de la visita');
      }
    }
  }
}