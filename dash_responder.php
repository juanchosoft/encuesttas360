<?php
session_start();

if (!isset($_SESSION['session_user']) || !isset($_SESSION['session_user']['id'])) {
  header('Location: index.php');
  exit();
}

require './admin/include/generic_classes.php';
require_once './admin/classes/Sondeo.php';
require_once './admin/classes/FichaTecnicaEncuesta.php';
require_once './admin/classes/RespuestaCuestionario.php';

// Configuración activa
$config        = Util::getInformacionConfiguracion();
$opcionActiva  = $config[0]['opcion_activa_web'] ?? 'sondeo';
$logoConfig    = !empty($config[0]['logo']) ? $config[0]['logo'] : '';

// Logo 360
$logo360 = 'assets/img/360 Estadisticas-04.png';

// Datos del usuario
$userId        = (int) SessionData::getUserId();
$nombreUsuario = $_SESSION['session_user']['nombre_completo']
  ?? $_SESSION['session_user']['usuario']
  ?? 'Usuario';

$partes       = explode(' ', trim($nombreUsuario));
$primerNombre = $partes[0] ?? 'Usuario';

$codigoDepto     = SessionData::getCodigoDepartamentoSessionVotante() ?? '';
$codigoMunicipio = SessionData::getCodigoMunicipioSessionVotante() ?? '';

/**
 * Verifica si una ficha técnica es visible para el votante según el espacio geográfico.
 */
function fichaVisibleParaVotante($ficha, $codigoDepto, $codigoMunicipio) {
  $db  = new DbConection();
  $pdo = $db->openConect();

  $tblEG  = $db->getTable('tbl_espacio_geografico');
  $tblFTE = $db->getTable('tbl_ficha_tecnica_encuestas');
  $tblXD  = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');

  $stmtEg = $pdo->prepare(
    "SELECT eg.tipo_estudio FROM $tblEG eg
     JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = eg.id
     WHERE fte.id = :ficha_id LIMIT 1"
  );

  $stmtEg->execute([':ficha_id' => $ficha['id']]);
  $eg = $stmtEg->fetch(PDO::FETCH_ASSOC);

  if (!$eg) {
    $db->closeConect();
    return true;
  }

  $tipo = strtolower($eg['tipo_estudio']);

  if ($tipo === 'nacional') {
    $db->closeConect();
    return true;
  }

  if ($tipo === 'departamental') {
    $stmt = $pdo->prepare(
      "SELECT COUNT(*) FROM $tblXD xd
       JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id
       WHERE fte.id = :ficha_id
       AND CAST(xd.codigo_departamento AS UNSIGNED) = CAST(:depto AS UNSIGNED)"
    );

    $stmt->execute([
      ':ficha_id' => $ficha['id'],
      ':depto'   => $codigoDepto
    ]);

    $visible = (int)$stmt->fetchColumn() > 0;
  } else {
    $stmt = $pdo->prepare(
      "SELECT COUNT(*) FROM $tblXD xd
       JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id
       WHERE fte.id = :ficha_id
       AND CAST(xd.codigo_ciudad AS UNSIGNED) = CAST(:municipio AS UNSIGNED)"
    );

    $stmt->execute([
      ':ficha_id'  => $ficha['id'],
      ':municipio' => $codigoMunicipio
    ]);

    $visible = (int)$stmt->fetchColumn() > 0;
  }

  $db->closeConect();
  return $visible;
}

// ─────────────────────────────────────────────
// SONDEOS
// ─────────────────────────────────────────────
$sondeosDisponibles = [];
$sondeosContestados = [];

if ($opcionActiva === 'sondeo' || $opcionActiva === 'ambos') {
  $arrSondeos   = Sondeo::getSondeosFiltrados(null);
  $listaSondeos = $arrSondeos['output']['response'] ?? [];

  $sondeosVotados = SessionData::getUserId()
    ? Sondeo::getSondeosVotadosPorUsuario($userId)
    : [];

  $respuestasUsuario = SessionData::getUserId()
    ? Sondeo::getRespuestasUsuarioPorSondeo($userId)
    : [];

  foreach ($listaSondeos as &$s) {
    $yaVotado = in_array($s['id'], $sondeosVotados);

    $s['contestado'] = $yaVotado;
    $s['respuesta_usuario'] = $yaVotado && isset($respuestasUsuario[$s['id']])
      ? $respuestasUsuario[$s['id']]['respuesta_texto']
      : '';

    if ($yaVotado) {
      $sondeosContestados[] = $s;
    } else {
      $sondeosDisponibles[] = $s;
    }
  }

  unset($s);
}

// ─────────────────────────────────────────────
// ENCUESTAS / FICHAS TÉCNICAS
// ─────────────────────────────────────────────
$encuestasPendientes  = [];
$encuestasCompletadas = [];

if ($opcionActiva === 'cuestionario' || $opcionActiva === 'ambos') {
  $fichasResult = FichaTecnicaEncuesta::getAll(['solo_habilitadas' => true]);
  $todasFichas  = $fichasResult['output']['response'] ?? [];

  foreach ($todasFichas as $ficha) {
    if (!fichaVisibleParaVotante($ficha, $codigoDepto, $codigoMunicipio)) {
      continue;
    }

    $verif = RespuestaCuestionario::verificarSiYaContesto([
      'ficha_tecnica_id' => $ficha['id'],
      'votante_id'       => $userId,
    ]);

    $contestada = $verif['output']['contestada'] ?? false;

    if ($contestada) {
      $encuestasCompletadas[] = $ficha;
    } else {
      $encuestasPendientes[] = $ficha;
    }
  }
}

// Totales
$totalPendientes  = count($sondeosDisponibles) + count($encuestasPendientes);
$totalCompletados = count($sondeosContestados) + count($encuestasCompletadas);

$hayAlgo   = ($totalPendientes + $totalCompletados) > 0;
$todoListo = $hayAlgo && ($totalPendientes === 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mis formularios | 360 Estadísticas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900;950&display=swap" rel="stylesheet">

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

 <link rel="stylesheet" href="./css/responder.css?v=<?= time(); ?>">
</head>

<body>

<?php include './admin/include/menusecond.php'; ?>
<?php include './admin/include/perfil.php'; ?>

<div class="page-wrap">
  <div class="shell">

    <!-- HERO -->
    <div class="hero">
      <div class="hero-content">

        <div class="hero-logo">
          <img src="<?= htmlspecialchars($logo360) ?>" alt="360 Estadísticas">
        </div>

        <div>
          <span class="hero-kicker">
            <i class="fa-solid fa-list-check"></i>
            Formularios activos
          </span>

          <h1 class="hero-title">
            Hola <?= htmlspecialchars($primerNombre) ?>, tienes formularios disponibles
          </h1>

          <p class="hero-subtitle">
            Responde tus sondeos o encuestas pendientes. Cuando completes todo, podrás consultar los resultados consolidados.
          </p>
        </div>

        <div class="hero-stats">
          <div class="hero-stat">
            <strong><?= $totalPendientes ?></strong>
            <span>Pendientes</span>
          </div>

          <div class="hero-stat">
            <strong><?= $totalCompletados ?></strong>
            <span>Completados</span>
          </div>
        </div>

      </div>
    </div>

    <?php if ($todoListo): ?>
      <div class="all-done">
        <div class="all-done-ico">
          <i class="fas fa-check-circle"></i>
        </div>

        <div>
          <h4>¡Todo al día!</h4>
          <p>Completaste todos los formularios disponibles. Ya puedes ver los resultados.</p>
        </div>

        <a href="resultado.php" class="btn btn-result">
          <i class="fas fa-chart-bar me-2"></i>
          Ver resultados
        </a>
      </div>
    <?php endif; ?>

    <?php if (!$hayAlgo): ?>
      <div class="panel">
        <div class="empty">
          <i class="fas fa-inbox"></i>
          <h4 style="font-weight:950;color:#061326;">No hay formularios disponibles</h4>
          <p class="mb-0">No encontramos formularios activos para tu ubicación en este momento.</p>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($sondeosDisponibles) || !empty($sondeosContestados)): ?>
      <div class="panel">
        <div class="panel-header">
          <div>
            <h5 class="panel-title">
              <i class="fas fa-poll me-2"></i>
              Sondeos
            </h5>
            <p class="panel-sub">
              Selecciona y responde el sondeo disponible.
            </p>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <?php if (!empty($sondeosDisponibles)): ?>
              <span class="badge-count badge-pend">
                <i class="fas fa-clock"></i>
                Pendientes: <?= count($sondeosDisponibles) ?>
              </span>
            <?php endif; ?>

            <?php if (!empty($sondeosContestados)): ?>
              <span class="badge-count badge-ok">
                <i class="fas fa-check-circle"></i>
                Completados: <?= count($sondeosContestados) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="panel-body">
          <?php if (!empty($sondeosDisponibles)): ?>
            <p class="section-hint">
              <i class="fas fa-hand-pointer me-1"></i>
              Pendientes primero. Haz clic en “Comenzar”.
            </p>

            <?php foreach ($sondeosDisponibles as $s): ?>
              <div class="item-card">
                <div class="item-ico sondeo-ico">
                  <i class="fas fa-poll"></i>
                </div>

                <div class="item-meta">
                  <div class="item-name">
                    <?= htmlspecialchars($s['sondeo'] ?? 'Sondeo') ?>
                  </div>

                  <div class="item-by">
                    <?= htmlspecialchars($s['descripcion_sondeo'] ?? 'Selecciona tu opción y confirma tu participación.') ?>
                  </div>

                  <div class="mt-2">
                    <span class="tag-pend">
                      <i class="fas fa-hourglass-half"></i>
                      Pendiente
                    </span>
                  </div>
                </div>

                <a href="sondeo_new.php" class="btn btn-start">
                  <i class="fas fa-play"></i>
                  Comenzar
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($sondeosContestados)): ?>
            <?php if (!empty($sondeosDisponibles)): ?>
              <hr class="my-1">
            <?php endif; ?>

            <p class="section-hint">
              <i class="fas fa-check-circle me-1"></i>
              Sondeos completados
            </p>

            <?php foreach ($sondeosContestados as $s): ?>
              <div class="item-card done">
                <div class="item-ico done-ico">
                  <i class="fas fa-check-circle"></i>
                </div>

                <div class="item-meta">
                  <div class="item-name">
                    <?= htmlspecialchars($s['sondeo'] ?? 'Sondeo') ?>
                  </div>

                  <div class="item-by">
                    <?= htmlspecialchars($s['descripcion_sondeo'] ?? 'Sondeo completado.') ?>
                  </div>

                  <div class="mt-2">
                    <span class="tag-ok">
                      <i class="fas fa-check-circle"></i>
                      Completado
                    </span>
                  </div>
                </div>

                <a href="resultado.php" class="btn btn-view">
                  <i class="fas fa-chart-bar"></i>
                  Ver resultados
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($encuestasPendientes) || !empty($encuestasCompletadas)): ?>
      <div class="panel">
        <div class="panel-header">
          <div>
            <h5 class="panel-title">
              <i class="fas fa-clipboard-list me-2"></i>
              Encuestas
            </h5>

            <p class="panel-sub">
              Selecciona y responde la encuesta disponible.
            </p>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <?php if (!empty($encuestasPendientes)): ?>
              <span class="badge-count badge-pend">
                <i class="fas fa-clock"></i>
                Pendientes: <?= count($encuestasPendientes) ?>
              </span>
            <?php endif; ?>

            <?php if (!empty($encuestasCompletadas)): ?>
              <span class="badge-count badge-ok">
                <i class="fas fa-check-circle"></i>
                Completadas: <?= count($encuestasCompletadas) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="panel-body">
          <?php if (!empty($encuestasPendientes)): ?>
            <p class="section-hint">
              <i class="fas fa-hand-pointer me-1"></i>
              Pendientes primero. Haz clic en “Comenzar”.
            </p>

            <?php foreach ($encuestasPendientes as $e): ?>
              <div class="item-card">
                <div class="item-ico encuesta-ico">
                  <i class="fas fa-file-alt"></i>
                </div>

                <div class="item-meta">
                  <div class="item-name">
                    <?= htmlspecialchars($e['texto_literal_de_la_encuesta_o_preguntas'] ?? $e['tema'] ?? 'Encuesta') ?>
                  </div>

                  <div class="item-by">
                    Realizada por:
                    <?= htmlspecialchars($e['realizada_por_o_encomendada_por'] ?? '360 Estadísticas') ?>
                  </div>

                  <div class="mt-2">
                    <span class="tag-pend">
                      <i class="fas fa-hourglass-half"></i>
                      Pendiente
                    </span>
                  </div>
                </div>

                <a href="encuesta.php?f=<?= (int)$e['id'] ?>" class="btn btn-start">
                  <i class="fas fa-play"></i>
                  Comenzar
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($encuestasCompletadas)): ?>
            <?php if (!empty($encuestasPendientes)): ?>
              <hr class="my-1">
            <?php endif; ?>

            <p class="section-hint">
              <i class="fas fa-check-circle me-1"></i>
              Encuestas completadas
            </p>

            <?php foreach ($encuestasCompletadas as $e): ?>
              <div class="item-card done">
                <div class="item-ico done-ico">
                  <i class="fas fa-check-circle"></i>
                </div>

                <div class="item-meta">
                  <div class="item-name">
                    <?= htmlspecialchars($e['texto_literal_de_la_encuesta_o_preguntas'] ?? $e['tema'] ?? 'Encuesta') ?>
                  </div>

                  <div class="item-by">
                    Realizada por:
                    <?= htmlspecialchars($e['realizada_por_o_encomendada_por'] ?? '360 Estadísticas') ?>
                  </div>

                  <div class="mt-2">
                    <span class="tag-ok">
                      <i class="fas fa-check-circle"></i>
                      Completada
                    </span>
                  </div>
                </div>

                <a href="resultado.php" class="btn btn-view">
                  <i class="fas fa-chart-bar"></i>
                  Ver resultados
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php include './admin/include/footer.php'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="admin/js/lib/data-md5.js"></script>
<script src="admin/js/perfil.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const sp = document.getElementById('spinner');
    if (sp) {
      sp.style.display = 'none';
    }
  });
</script>

</body>
</html>
