<?php
require './admin/include/generic_classes.php';
include './admin/classes/Pregunta.php';
include './admin/classes/FichaTecnicaEncuesta.php';
include './admin/classes/RespuestaCuestionario.php';

// Validar acceso según opción activa (ANTES de incluir archivos que generan HTML)
$config = Util::getInformacionConfiguracion();
$opcionActivaWeb = $config[0]['opcion_activa_web'] ?? '';

if ($opcionActivaWeb !== 'cuestionario' && $opcionActivaWeb !== 'ambos') {
    if ($opcionActivaWeb === 'sondeo') {
        header('Location: sondeo.php');
    } else {
        header('Location: grilla.php');
    }
    exit();
}

include './admin/include/generic_info_configuracion.php';

// Obtener ID de ficha técnica desde URL
$fichaTecnicaId = isset($_GET['f']) ? intval($_GET['f']) : 0;

// Obtener ID del votante logueado
$votanteId = SessionData::getUserId();

// Datos de ubicación del votante para filtro geográfico
$codigoDeptoVotante     = SessionData::getCodigoDepartamentoSessionVotante() ?? '';
$codigoMunicipioVotante = SessionData::getCodigoMunicipioSessionVotante() ?? '';

/**
 * Verifica si una ficha técnica es visible para el votante según el espacio geográfico.
 * - Nacional  → visible para todos
 * - Departamental → visible si el depto del votante está en el espacio
 * - Municipal → visible si el municipio del votante está en el espacio
 */
function fichaVisibleParaVotante($ficha, $codigoDepto, $codigoMunicipio) {
    $db  = new DbConection();
    $pdo = $db->openConect();

    $tblEG   = $db->getTable('tbl_espacio_geografico');
    $tblFTE  = $db->getTable('tbl_ficha_tecnica_encuestas');
    $tblXD   = $db->getTable('tbl_espacio_geografico_x_departamentos_x_ciudades');

    // Obtener tipo_estudio del espacio geográfico asociado a la ficha
    $stmtEg = $pdo->prepare(
        "SELECT eg.tipo_estudio FROM $tblEG eg
         JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = eg.id
         WHERE fte.id = :ficha_id LIMIT 1"
    );
    $stmtEg->execute([':ficha_id' => $ficha['id']]);
    $eg = $stmtEg->fetch(PDO::FETCH_ASSOC);

    if (!$eg) { $db->closeConect(); return true; } // Sin espacio geográfico → visible
    $tipoEstudio = strtolower($eg['tipo_estudio']);

    if ($tipoEstudio === 'nacional') { $db->closeConect(); return true; }

    if ($tipoEstudio === 'departamental') {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM $tblXD xd
             JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id
             WHERE fte.id = :ficha_id AND CAST(xd.codigo_departamento AS UNSIGNED) = CAST(:depto AS UNSIGNED)"
        );
        $stmt->execute([':ficha_id' => $ficha['id'], ':depto' => $codigoDepto]);
        $visible = (int)$stmt->fetchColumn() > 0;
    } else {
        // Municipal
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM $tblXD xd
             JOIN $tblFTE fte ON fte.tbl_espacio_geografico_id = xd.tbl_espacio_geografico_id
             WHERE fte.id = :ficha_id AND CAST(xd.codigo_ciudad AS UNSIGNED) = CAST(:municipio AS UNSIGNED)"
        );
        $stmt->execute([':ficha_id' => $ficha['id'], ':municipio' => $codigoMunicipio]);
        $visible = (int)$stmt->fetchColumn() > 0;
    }

    $db->closeConect();
    return $visible;
}

// Si no viene ID, mostrar selector de encuestas
$todasFichasTecnicas = [];
$encuestasPendientes = [];
$encuestasContestadas = [];
$mostrarSelector = false;

if ($fichaTecnicaId === 0) {
    $mostrarSelector = true;
    $todasFichasTecnicasResult = FichaTecnicaEncuesta::getAll(['solo_habilitadas' => true]);
    if ($todasFichasTecnicasResult['output']['valid']) {
        $todasFichasTecnicas = $todasFichasTecnicasResult['output']['response'];

        $fichasVisibles = [];
        foreach ($todasFichasTecnicas as $ficha) {
            // Filtro geográfico: omitir fichas fuera del alcance del votante
            if (!fichaVisibleParaVotante($ficha, $codigoDeptoVotante, $codigoMunicipioVotante)) continue;

            $fichasVisibles[] = $ficha;

            $verificacion = RespuestaCuestionario::verificarSiYaContesto([
                'ficha_tecnica_id' => $ficha['id'],
                'votante_id' => $votanteId
            ]);

            $contestada = $verificacion['output']['contestada'] ?? false;

            if ($contestada) $encuestasContestadas[] = $ficha;
            else $encuestasPendientes[] = $ficha;
        }
        $todasFichasTecnicas = $fichasVisibles;
    }
}

// Variables del cuestionario
$fichaTecnica = null;
$preguntas = [];
$encuestaYaContestada = false;

if ($fichaTecnicaId > 0) {
    $verificacion = RespuestaCuestionario::verificarSiYaContesto([
        'ficha_tecnica_id' => $fichaTecnicaId,
        'votante_id' => $votanteId
    ]);

    $encuestaYaContestada = $verificacion['output']['contestada'] ?? false;

    if ($encuestaYaContestada) {
        header('Location: encuesta.php?ya_contestada=1');
        exit;
    }

    $fichaTecnicaResult = FichaTecnicaEncuesta::getAll(['id' => $fichaTecnicaId]);
    if ($fichaTecnicaResult['output']['valid'] && !empty($fichaTecnicaResult['output']['response'])) {
        $fichaTecnica = $fichaTecnicaResult['output']['response'][0];

        // Verificar acceso geográfico al acceder directamente por URL
        if (!fichaVisibleParaVotante($fichaTecnica, $codigoDeptoVotante, $codigoMunicipioVotante)) {
            header('Location: encuesta.php');
            exit;
        }

        $preguntasResult = Pregunta::getAll(['tbl_ficha_tecnica_encuesta_id' => $fichaTecnicaId]);
        if ($preguntasResult['output']['valid']) $preguntas = $preguntasResult['output']['response'];
    } else {
        $mostrarSelector = true;
        $todasFichasTecnicasResult = FichaTecnicaEncuesta::getAll([]);
        if ($todasFichasTecnicasResult['output']['valid']) {
            $todasFichasTecnicas = $todasFichasTecnicasResult['output']['response'];

            foreach ($todasFichasTecnicas as $ficha) {
                $verificacion = RespuestaCuestionario::verificarSiYaContesto([
                    'ficha_tecnica_id' => $ficha['id'],
                    'votante_id' => $votanteId
                ]);

                $contestada = $verificacion['output']['contestada'] ?? false;

                if ($contestada) $encuestasContestadas[] = $ficha;
                else $encuestasPendientes[] = $ficha;
            }
        }
    }
}

// Información del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$logo = !empty($configuracionAplicacion[0]['logo']) ? $configuracionAplicacion[0]['logo'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= $mostrarSelector ? 'Seleccionar Encuesta' : 'Cuestionario - ' . htmlspecialchars($fichaTecnica['tema'] ?? 'Encuesta') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Fonts + Bootstrap -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    :root{
      --brand:#13357b;
      --brand2:#0b1a89;
      --ink:#0f172a;
      --muted:#64748b;
      --bg:#f6f8fc;
      --card:#ffffff;
      --stroke:rgba(15,23,42,.12);
      --shadow: 0 18px 45px rgba(2,6,23,.10);
      --shadow2: 0 10px 22px rgba(2,6,23,.08);
      --r-xl: 22px;
      --r-lg: 18px;
      --r-md: 14px;
    }

    body{
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: radial-gradient(1000px 600px at 20% 0%, rgba(19,53,123,.18), transparent 60%),
                  radial-gradient(900px 560px at 90% 10%, rgba(11,26,137,.14), transparent 55%),
                  var(--bg);
      color: var(--ink);
    }

    .page-wrap{ padding: 32px 0 48px; }
    .shell{ max-width: 1020px; margin: 0 auto; padding: 0 16px; }

    /* Header hero */
    .hero{
      position: relative;
      border-radius: var(--r-xl);
      overflow: hidden;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      color: #fff;
      box-shadow: var(--shadow);
      padding: 22px 22px;
      margin-bottom: 18px;
    }
    .hero:before{
      content:"";
      position:absolute; inset:-2px;
      background:
        radial-gradient(520px 200px at 15% 15%, rgba(255,255,255,.22), transparent 60%),
        radial-gradient(600px 260px at 80% 20%, rgba(255,255,255,.14), transparent 60%),
        linear-gradient(180deg, rgba(255,255,255,.08), transparent 50%);
      pointer-events:none;
    }
    .hero .row{ position:relative; z-index:1; }
    .hero-title{ font-weight: 800; letter-spacing: -.6px; margin:0; }
    .hero-sub{ opacity: .92; margin: 6px 0 0; color: rgba(255,255,255,.90); }
    .hero-chip{
      display:inline-flex; align-items:center; gap:8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(255,255,255,.14);
      border: 1px solid rgba(255,255,255,.22);
      font-weight: 700;
      font-size: 12px;
    }
    .hero-logo{
      width: 52px; height: 52px; border-radius: 14px;
      background: rgba(255,255,255,.14);
      border:1px solid rgba(255,255,255,.22);
      display:flex; align-items:center; justify-content:center;
      overflow:hidden;
    }
    .hero-logo img{ width: 44px; height:auto; display:block; }
    .hero-actions .btn{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 800;
    }
    .btn-brand{
      background:#fff; color: var(--brand2);
      border: 0;
      box-shadow: 0 10px 22px rgba(0,0,0,.18);
    }
    .btn-brand:hover{ background:#f1f5ff; color:var(--brand2); }
    .btn-ghost{
      background: rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.22);
      color:#fff;
    }
    .btn-ghost:hover{ background: rgba(255,255,255,.18); color:#fff; }

    /* Cards */
    .panel{
      background: var(--card);
      border:1px solid var(--stroke);
      border-radius: var(--r-xl);
      box-shadow: var(--shadow2);
      overflow:hidden;
    }
    .panel-header{
      padding: 16px 18px;
      border-bottom: 1px solid var(--stroke);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      background: linear-gradient(180deg, rgba(19,53,123,.07), transparent);
    }
    .panel-title{ margin:0; font-weight: 900; letter-spacing: -.4px; }
    .panel-sub{ margin:0; color: var(--muted); font-weight:600; font-size: 13px; }

    .list-card{
      padding: 16px;
      border-radius: var(--r-lg);
      border: 1px solid var(--stroke);
      background:#fff;
      box-shadow: 0 10px 22px rgba(2,6,23,.06);
    }
    .tag{
      display:inline-flex; align-items:center; gap:7px;
      padding: 6px 10px;
      border-radius: 999px;
      font-weight:800;
      font-size: 12px;
      border: 1px solid transparent;
    }
    .tag-pend{
      background: rgba(245,158,11,.16);
      border-color: rgba(245,158,11,.22);
      color: #92400e;
    }
    .tag-ok{
      background: rgba(34,197,94,.14);
      border-color: rgba(34,197,94,.22);
      color: #166534;
    }

    .btn-soft{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 900;
    }
    .btn-soft-primary{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border:0;
      color:#fff;
      box-shadow: 0 12px 25px rgba(19,53,123,.22);
    }
    .btn-soft-primary:hover{ filter: brightness(1.03); color:#fff; }
    .btn-soft-outline{
      background:#fff;
      border:1px solid rgba(19,53,123,.26);
      color: var(--brand2);
    }
    .btn-soft-outline:hover{ background: rgba(19,53,123,.06); }

    /* Scroll mode */
    .wizard{
      padding: 18px;
    }
    .wizard-top{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      flex-wrap:wrap;
      padding: 12px 14px;
      border-radius: 18px;
      border:1px solid var(--stroke);
      background: linear-gradient(180deg, rgba(19,53,123,.06), rgba(255,255,255,.92));
    }
    .progress{
      height: 10px;
      border-radius: 999px;
      background: rgba(2,6,23,.08);
    }
    .progress-bar{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border-radius: 999px;
      transition: width .35s ease;
    }
    .wizard-help{
      color: var(--muted);
      font-weight: 700;
      font-size: 13px;
    }

    /* Capítulo header separador */
    .capitulo-header{
      margin-top: 28px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .capitulo-header:first-child{ margin-top: 0; }
    .capitulo-badge{
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #fff;
      background: linear-gradient(135deg,#20427F,#132b52);
      white-space: nowrap;
    }
    .capitulo-line{
      flex: 1;
      height: 2px;
      background: linear-gradient(90deg, rgba(32,66,127,.3), transparent);
      border-radius: 999px;
    }

    .q-card{
      position: relative;
      margin-top: 16px;
      border-radius: calc(var(--r-xl) + 2px);
      border:1px solid rgba(15,23,42,.10);
      background: linear-gradient(180deg, #ffffff, #fcfdff);
      box-shadow: 0 18px 40px rgba(2,6,23,.08);
      overflow:hidden;
      transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }
    .q-card::before{
      content:"";
      position:absolute;
      inset:0 0 auto 0;
      height: 4px;
      background: linear-gradient(90deg, var(--brand), #3b82f6, #93c5fd);
      opacity:.92;
    }
    .q-card:hover{
      transform: translateY(-2px);
      box-shadow: 0 22px 44px rgba(2,6,23,.10);
    }
    .q-card.answered{
      border-color: rgba(34,197,94,.30);
      box-shadow: 0 18px 38px rgba(34,197,94,.12);
    }
    .q-head{
      padding: 0;
      border-bottom: 1px solid var(--stroke);
    }
    .q-capitulo{
      display: inline-block;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .7px;
      color: #fff;
      background: linear-gradient(135deg,#20427F,#132b52);
      padding: 4px 12px;
      border-radius: 0 0 8px 0;
    }
    .q-capitulo-wrap{
      padding: 0;
      border-bottom: 1px solid rgba(19,53,123,.1);
    }
    .q-head-meta{
      display: flex; flex-direction: column;
      padding: 14px 16px 10px;
      background: linear-gradient(180deg, rgba(19,53,123,.09), rgba(19,53,123,.03));
      border-bottom: 1px solid rgba(19,53,123,.08);
      gap: 4px;
    }
    .q-numeral{
      display:inline-flex;
      align-items:center;
      gap:8px;
      width: fit-content;
      max-width: 100%;
      font-size: 12px;
      font-weight: 900;
      color: #13357b;
      line-height: 1.3;
      background: rgba(255,255,255,.88);
      border:1px solid rgba(19,53,123,.14);
      border-radius: 999px;
      padding: 6px 12px;
      box-shadow: 0 6px 14px rgba(2,6,23,.04);
    }
    .q-kicker{
      display:flex; align-items:center; justify-content:space-between;
      color: var(--muted);
      font-weight: 800;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .8px;
    }
    .q-head-body{
      padding: 12px 16px 14px;
    }
    .q-texto-adicional{
      font-size: 13px;
      font-weight: 700;
      color: #1e293b;
      margin: 0 0 6px;
    }
    .q-enunciado{
      font-size: 13px;
      color: #000000;
      font-style: italic;
      line-height: 1.5;
      padding: 8px 12px;
      background: rgba(250, 3, 3, 0.99);
      border-left: 3px solid rgba(32,66,127,.35);
      border-radius: 0 8px 8px 0;
      margin-bottom: 2px;
    }
    .q-enunciado-comun{
      font-size: 18px;
      font-weight: 800;
      color: #020202;
  
      padding: 14px 18px;
      background: linear-gradient(135deg, rgba(19,53,123,.08), rgba(19,53,123,.03));
      border: 1px solid rgba(19,53,123,.12);
      border-left: 4px solid var(--brand);
      border-radius: 0 var(--r-lg) var(--r-lg) 0;
      margin: 18px 0 12px;
      box-shadow: 0 4px 12px rgba(2,6,23,.04);
    }
    .q-title{
      margin: 8px 0 0;
      font-weight: 900;
      letter-spacing: -.4px;
      font-size: 19px;
      line-height: 1.32;
      padding: 0 16px 16px;
      border-top: 1px solid rgba(15,23,42,.08);
      padding-top: 14px;
      color:#0f172a;
    }
    .q-body{ padding: 16px 16px 18px; }
    .hint{
      display:flex; gap:10px; align-items:flex-start;
      padding: 10px 12px;
      border-radius: 14px;
      background: rgba(19,53,123,.06);
      border:1px solid rgba(19,53,123,.12);
      color: #334155;
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 12px;
    }
    .hint i{ margin-top: 2px; color: var(--brand2); }

    /* opciones estilo pro */
    .opts-vertical{ display: flex; flex-direction: column; gap: 10px; }
    .opts-horizontal{ display: flex; flex-wrap: wrap; gap: 10px; }
    .opts-compact{
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(72px, 1fr));
      gap: 10px;
      margin-top: 4px;
    }
    .opt{
      position:relative;
      display:flex;
      gap: 10px;
      align-items: center;
      padding: 12px 14px;
      border: 1px solid rgba(15,23,42,.11);
      border-radius: 18px;
      transition: all .18s ease;
      cursor:pointer;
      background: linear-gradient(180deg, #ffffff, #f8fafc);
      user-select:none;
      min-height: 58px;
    }
    .opt-h{
      flex: 1 1 auto;
      min-width: 70px;
      justify-content: center;
      text-align: center;
      flex-direction: column;
      gap: 7px;
      padding: 14px 10px;
    }
    .opt-compact{
      min-width: 0;
      min-height: 76px;
      justify-content: center;
      text-align: center;
      flex-direction: column;
      gap: 7px;
      padding: 9px 6px 10px;
      border-radius: 20px;
      background: linear-gradient(180deg, #ffffff, #f8fbff);
      box-shadow: 0 8px 18px rgba(2,6,23,.05);
    }
    .opt-h .form-check-input{ margin: 0 auto; }
    .opt-compact .form-check-input{ position:absolute; opacity:0; pointer-events:none; width:0; height:0; }
    .opt:hover{
      border-color: rgba(19,53,123,.30);
      transform: translateY(-1px);
      box-shadow: 0 12px 22px rgba(2,6,23,.08);
    }
    .opt.selected{
      border-color: rgba(19,53,123,.35);
      background: linear-gradient(180deg, rgba(19,53,123,.10), rgba(19,53,123,.05));
      box-shadow: 0 14px 26px rgba(19,53,123,.13);
    }
    .opt .form-check-input{
      flex-shrink: 0;
      width: 18px;
      height: 18px;
      accent-color: var(--brand);
    }
    .opt .form-check-label{
      font-weight: 800;
      color: #0f172a;
      line-height: 1.25;
    }
    .opt-compact-check{
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border:2px solid rgba(148,163,184,.55);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size: 9px;
      color:#fff;
      background:#fff;
      transition: all .18s ease;
    }
    .opt-compact-body{
      display:flex;
      flex-direction:column;
      align-items:center;
      gap: 2px;
      line-height:1;
    }
    .opt-compact-value{
      font-size: 22px;
      font-weight: 900;
      color:#0f172a;
      letter-spacing:-.4px;
    }
    .opt-compact-caption{
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .6px;
      color: var(--muted);
      font-weight: 900;
    }
    .opt-compact.selected .opt-compact-check{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border-color: transparent;
      box-shadow: 0 6px 12px rgba(19,53,123,.18);
    }
    .opt-compact.selected .opt-compact-value{
      color: var(--brand2);
    }

    .respuesta-texto{
      border-radius: 16px;
      border: 1px solid rgba(15,23,42,.14);
      padding: 12px 14px;
      min-height: 110px;
      font-weight: 650;
    }
    .respuesta-texto:focus{
      border-color: rgba(19,53,123,.35);
      box-shadow: 0 0 0 .2rem rgba(19,53,123,.12);
    }

    /* bottom nav */
    .wizard-nav{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      margin-top: 14px;
      padding: 12px;
      border-radius: 18px;
      border:1px solid var(--stroke);
      background: rgba(255,255,255,.88);
      backdrop-filter: blur(10px);
      position: sticky;
      bottom: 12px;
      z-index: 20;
    }

    .nav-left, .nav-right{ display:flex; gap:10px; align-items:center; }
    .btn-nav{
      border-radius: 14px;
      padding: 10px 14px;
      font-weight: 900;
      border: 1px solid rgba(15,23,42,.12);
      background: #fff;
      color: #0f172a;
    }
    .btn-nav:hover{ background: rgba(19,53,123,.06); border-color: rgba(19,53,123,.26); }
    .btn-next{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border: 0;
      color: #fff;
      box-shadow: 0 12px 25px rgba(19,53,123,.22);
    }
    .btn-next:hover{ filter: brightness(1.03); color:#fff; }
    .btn-send{
      background: linear-gradient(135deg, #16a34a, #22c55e);
      border: 0;
      color: #fff;
      box-shadow: 0 12px 25px rgba(34,197,94,.20);
    }
    .btn-send:hover{ filter: brightness(1.03); color:#fff; }

    .status-pill{
      display:inline-flex; align-items:center; gap:8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(15,23,42,.06);
      border:1px solid rgba(15,23,42,.10);
      font-weight: 900;
      color:#0f172a;
      font-size: 12px;
    }
    .status-pill .dot{
      width: 10px; height: 10px; border-radius: 50%;
      background: rgba(100,116,139,.6);
    }
    .status-pill.ok .dot{ background: rgba(34,197,94,.95); }
    .status-pill.warn .dot{ background: rgba(245,158,11,.95); }

    /* Modal */
    .modal-content{ border-radius: 18px; border: 1px solid rgba(15,23,42,.10); }
    .modal-header{ border-bottom: 1px solid rgba(255,255,255,.18); }
    .modal-body{ background: #fff; }
    .modal-footer{ border-top: 1px solid rgba(15,23,42,.10); }



    /* Navegación real por capítulos */
    .chapter-shell{
      position: relative;
    }
    .chapter-slide{
      display: none;
      animation: fadeChapter .24s ease both;
    }
    .chapter-slide.active{
      display: block;
    }
    @keyframes fadeChapter{
      from{ opacity:0; transform: translateY(10px); }
      to{ opacity:1; transform: translateY(0); }
    }
    .chapter-intro-card{
      display:grid;
      grid-template-columns: auto 1fr auto;
      gap: 14px;
      align-items:center;
      border-radius: var(--r-xl);
      border:1px solid rgba(19,53,123,.15);
      background:
        radial-gradient(500px 180px at 10% 0%, rgba(19,53,123,.12), transparent 60%),
        linear-gradient(180deg, #fff, rgba(248,250,252,.94));
      box-shadow: 0 14px 30px rgba(2,6,23,.07);
      padding: 16px;
      margin-bottom: 16px;
    }
    .chapter-intro-icon,
    .next-chapter-icon{
      width: 48px;
      height: 48px;
      border-radius: 16px;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#fff;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 12px 24px rgba(19,53,123,.20);
      flex-shrink:0;
    }
    .chapter-eyebrow{
      color: var(--brand2);
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .8px;
      margin-bottom: 3px;
    }
    .chapter-intro-content h3{
      margin:0;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: -.5px;
      color:#0f172a;
      line-height:1.15;
    }
    .chapter-intro-content p{
      margin: 6px 0 0;
      color: var(--muted);
      font-size: 13px;
      line-height:1.5;
      font-weight: 650;
    }
    .chapter-intro-count{
      min-width: 92px;
      text-align:center;
      border-radius: 18px;
      padding: 10px 12px;
      background: rgba(19,53,123,.07);
      border:1px solid rgba(19,53,123,.13);
    }
    .chapter-intro-count strong{
      display:block;
      font-size: 24px;
      line-height:1;
      color: var(--brand2);
      font-weight: 900;
    }
    .chapter-intro-count span{
      display:block;
      margin-top: 4px;
      color: var(--muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing:.5px;
    }
    .next-chapter-card{
      display:flex;
      align-items:center;
      gap: 12px;
      margin-top: 16px;
      padding: 14px;
      border-radius: var(--r-xl);
      border: 1px dashed rgba(19,53,123,.30);
      background: rgba(19,53,123,.055);
    }
    .next-chapter-card span{
      display:block;
      color: var(--muted);
      font-size: 12px;
      font-weight: 850;
      text-transform: uppercase;
      letter-spacing:.4px;
    }
    .next-chapter-card strong{
      display:block;
      margin-top: 3px;
      color:#0f172a;
      font-size: 16px;
      font-weight: 900;
      line-height:1.2;
    }
    .next-chapter-card.final{
      border-style: solid;
      background: rgba(34,197,94,.08);
      border-color: rgba(34,197,94,.22);
    }
    .next-chapter-card.final .next-chapter-icon{
      background: linear-gradient(135deg, #16a34a, #22c55e);
    }
    .wizard-nav-center{
      flex: 1;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      gap: 4px;
      text-align:center;
      min-width: 160px;
    }
    .next-title-mini{
      color: var(--muted);
      font-size: 11px;
      line-height: 1.2;
      font-weight: 800;
    }
    .q-footer-status{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 10px;
      margin-top: 14px;
    }
    .q-status-left,
    .q-status-right{
      font-size: 12px;
    }

    /* Responsive tweaks */
    @media (max-width: 768px){
      .page-wrap{ padding: 18px 0 34px; }
      .shell{ padding: 0 10px; }
      .panel-header{ align-items:flex-start; flex-direction:column; }
      .hero{ padding: 18px; border-radius: 20px; }
      .hero-title{ font-size: 21px; }
      .hero-chip{ font-size: 11px; padding: 7px 10px; }
      .hero-actions{ width:100%; }
      .hero-actions .btn{ flex: 1 1 0; padding: 10px 8px; font-size: 13px; }
      .wizard{ padding: 12px; }
      .wizard-top{ padding: 12px; }
      .chapter-intro-card{
        grid-template-columns: auto 1fr;
        padding: 14px;
        gap: 12px;
      }
      .chapter-intro-count{
        grid-column: 1 / -1;
        display:flex;
        align-items:center;
        justify-content:center;
        gap: 8px;
        min-width: 0;
      }
      .chapter-intro-count strong{ font-size: 20px; }
      .chapter-intro-count span{ margin:0; }
      .chapter-intro-content h3{ font-size: 18px; }
      .chapter-intro-content p{ font-size: 12px; }
      .q-card{ border-radius: 20px; margin-top: 12px; }
      .q-title{ font-size: 16px; line-height:1.38; padding-left: 14px; padding-right: 14px; }
      .q-body{ padding: 12px 14px 14px; }
      .opts-horizontal{
        display:grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
      }
      .opts-compact{
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px;
      }
      .opt-h{ min-width:0; width:100%; }
      .opt{ padding: 11px 10px; border-radius: 15px; }
      .opt .form-check-label{ font-size: 13px; }
      .opt-compact{ min-height: 72px; padding: 8px 4px 10px; }
      .opt-compact-value{ font-size: 19px; }
      .opt-compact-caption{ font-size: 9px; }
      .q-footer-status{ align-items:flex-start; flex-direction:column; gap: 6px; }
      .wizard-nav{
        left: 10px;
        right: 10px;
        bottom: 8px;
        display:grid;
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 10px;
      }
      .nav-left,
      .nav-right{
        width:100%;
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
      }
      .nav-left{ grid-template-columns: 1fr; }
      .wizard-nav-center{ order:-1; width:100%; min-width:0; }
      .btn-nav{ width:100%; padding: 11px 10px; font-size: 13px; }
      .btn-send{ grid-column: 1 / -1; }
      .next-chapter-card{ align-items:flex-start; padding: 12px; }
      .next-chapter-card strong{ font-size: 14px; }
    }

    @media (max-width: 420px){
      .opts-horizontal{ grid-template-columns: 1fr; }
      .opts-compact{ grid-template-columns: repeat(5, minmax(0, 1fr)); }
      .hero-logo{ width:46px; height:46px; }
      .chapter-intro-icon,
      .next-chapter-icon{ width:42px; height:42px; border-radius: 14px; }
      .list-card{ padding: 13px; }
      .btn-soft{ width:100%; }
    }
  </style>
</head>

<body>
<?php include './admin/include/menusecond.php'; ?>

<div class="page-wrap">
  <div class="shell" id="cuestionario_container" data-ficha-tecnica-id="<?= $fichaTecnicaId ?>">

    <!-- HERO -->
    <div class="hero">
      <div class="row g-3 align-items-center">
        <div class="col-auto">
          <div class="hero-logo">
            
              <i class="fa-solid fa-clipboard-list" style="font-size:22px;"></i>
       
          </div>
        </div>
        <div class="col">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <h2 class="hero-title">
              <?= $mostrarSelector ? 'Encuestas disponibles' : 'Cuestionario guiado' ?>
            </h2>
            <span class="hero-chip">
              <i class="fa-solid fa-shield-heart"></i>
              Seguro • Intuitivo • Paso a paso
            </span>
          </div>
          <p class="hero-sub mb-0">
            <?= $mostrarSelector
              ? 'Selecciona la encuesta que deseas responder. Verás cuáles están pendientes y cuáles ya completaste.'
              : 'Responde todas las preguntas a tu ritmo. Puedes guardar borrador y revisar antes de enviar.'; ?>
          </p>
        </div>

        <?php if(!$mostrarSelector): ?>
          <div class="col-12 col-lg-auto hero-actions d-flex gap-2 justify-content-lg-end">
            <button type="button" class="btn btn-ghost" id="btnGuardarBorrador">
              <i class="fa-solid fa-floppy-disk me-2"></i>Guardar borrador
            </button>
            <button type="button" class="btn btn-brand" id="btnRevisarResumen">
              <i class="fa-solid fa-list-check me-2"></i>Revisar
            </button>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- CONTENIDO -->
    <div class="panel">

      <?php if ($mostrarSelector): ?>
        <div class="panel-header">
          <div>
            <h4 class="panel-title mb-1">Selecciona una encuesta</h4>
            <p class="panel-sub mb-0">Pendientes primero. Las completadas quedan bloqueadas, pero puedes ver tus respuestas.</p>
          </div>
          <div class="d-none d-md-flex align-items-center gap-2">
            <span class="status-pill <?= (count($encuestasPendientes)>0 ? 'warn':'ok'); ?>">
              <span class="dot"></span>
              Pendientes: <?= count($encuestasPendientes) ?>
            </span>
            <span class="status-pill ok">
              <span class="dot"></span>
              Completadas: <?= count($encuestasContestadas) ?>
            </span>
          </div>
        </div>

        <div class="wizard">
          <?php if (isset($_GET['enviada'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:16px;">
              <i class="fas fa-circle-check me-2"></i>
              <strong>Encuesta enviada correctamente.</strong> Tus respuestas ya quedaron registradas.
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (isset($_GET['ya_contestada'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-radius:16px;">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Ya contestaste esa encuesta.</strong> No puedes volver a responderla.
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (count($encuestasPendientes) > 0): ?>
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
              <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-user me-2"></i>Encuestas pendientes</h5>
              <div class="text-muted fw-semibold" style="font-size:13px;">
                <i class="fa-solid fa-hand-pointer me-1"></i>Haz clic en "Comenzar"
              </div>
            </div>

            <div class="row g-3">
              <?php foreach ($encuestasPendientes as $ficha): ?>
                <div class="col-12">
                  <div class="list-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                      <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                          <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-clipboard-question me-2 text-primary"></i><?= htmlspecialchars($ficha['tema']) ?>
                          </h5>
                          <span class="tag tag-pend"><i class="fa-solid fa-hourglass-half"></i>Pendiente</span>
                        </div>

                        <div class="mt-2 text-muted fw-semibold" style="font-size: 13px;">
                          <?php if (!empty($ficha['realizada_por_o_encomendada_por'])): ?>
                            <div><i class="fa-solid fa-user-pen me-2"></i><b>Realizada por:</b> <?= htmlspecialchars($ficha['realizada_por_o_encomendada_por']) ?></div>
                          <?php endif; ?>
                          <?php if (!empty($ficha['fecha_realizacion'])): ?>
                            <div class="mt-1"><i class="fa-solid fa-calendar-day me-2"></i><b>Fecha:</b> <?= date('d/m/Y', strtotime($ficha['fecha_realizacion'])) ?></div>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="d-flex gap-2">
                        <button class="btn btn-soft btn-soft-primary" onclick="location.href='?f=<?= $ficha['id'] ?>'">
                          <i class="fas fa-play-circle me-2"></i>Comenzar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (count($encuestasContestadas) > 0): ?>
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 mb-2">
              <h5 class="mb-0 fw-bold"><i class="fas fa-check-circle text-success me-2"></i>Encuestas contestadas</h5>
              <div class="text-muted fw-semibold" style="font-size:13px;">
                <i class="fa-solid fa-eye me-1"></i>Puedes ver tus respuestas
              </div>
            </div>

            <div class="row g-3">
              <?php foreach ($encuestasContestadas as $ficha): ?>
                <div class="col-12">
                  <div class="list-card" style="opacity:.92;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                      <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                          <h5 class="mb-0 fw-bold">
                            <i class="fa-solid fa-clipboard-check me-2 text-success"></i><?= htmlspecialchars($ficha['tema']) ?>
                          </h5>
                          <span class="tag tag-ok"><i class="fa-solid fa-circle-check"></i>Completada</span>
                        </div>

                        <div class="mt-2 text-muted fw-semibold" style="font-size: 13px;">
                          <?php if (!empty($ficha['realizada_por_o_encomendada_por'])): ?>
                            <div><i class="fa-solid fa-user-pen me-2"></i><b>Realizada por:</b> <?= htmlspecialchars($ficha['realizada_por_o_encomendada_por']) ?></div>
                          <?php endif; ?>
                          <?php if (!empty($ficha['fecha_realizacion'])): ?>
                            <div class="mt-1"><i class="fa-solid fa-calendar-day me-2"></i><b>Fecha:</b> <?= date('d/m/Y', strtotime($ficha['fecha_realizacion'])) ?></div>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <button class="btn btn-soft btn-soft-outline" disabled>
                          <i class="fas fa-lock me-2"></i>Ya contestada
                        </button>
                        <button class="btn btn-soft btn-soft-primary" onclick="verMisRespuestas(<?= $ficha['id'] ?>)">
                          <i class="fas fa-eye me-2"></i>Ver respuestas
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (count($todasFichasTecnicas) === 0): ?>
            <div class="alert alert-warning text-center" style="border-radius:16px;">
              No hay encuestas disponibles.
            </div>
          <?php endif; ?>
        </div>

      <?php else: ?>

        <!-- INFO -->
        <div class="panel-header">
          <div>
            <h4 class="panel-title mb-1">Información de la encuesta</h4>
            <p class="panel-sub mb-0">Responde paso a paso. Puedes guardar borrador y revisar antes de enviar.</p>
          </div>
          <div class="d-none d-md-flex gap-2">
            <span class="status-pill warn" id="pillStatus">
              <span class="dot"></span>
              <span id="pillText">Sin completar</span>
            </span>
          </div>
        </div>

        <div class="wizard">
          <div class="row g-3">
            <div class="col-12">
              <div class="list-card">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                  <div>
                    <div class="fw-bold" style="font-size: 16px;">
                      <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                      <?= htmlspecialchars($fichaTecnica['tema'] ?? 'Encuesta') ?>
                    </div>
                    <div class="text-muted fw-semibold mt-1" style="font-size: 13px;">
                      <?php if (!empty($fichaTecnica['realizada_por_o_encomendada_por'])): ?>
                        <div><i class="fa-solid fa-user-pen me-2"></i><b>Realizada por:</b> <?= htmlspecialchars($fichaTecnica['realizada_por_o_encomendada_por']) ?></div>
                      <?php endif; ?>
                      <?php if (!empty($fichaTecnica['fecha_realizacion'])): ?>
                        <div class="mt-1"><i class="fa-solid fa-calendar-day me-2"></i><b>Fecha:</b> <?= date('d/m/Y', strtotime($fichaTecnica['fecha_realizacion'])) ?></div>
                      <?php endif; ?>
                    </div>
                  </div>

                </div>
              </div>
            </div>


            <!-- FORM (hidden inputs siguen igual para compatibilidad) -->
            <div class="col-12">
              <form id="form_cuestionario" class="m-0">
                <input type="hidden" name="ficha_tecnica_id" value="<?= $fichaTecnicaId ?>">

                <?php if (count($preguntas) > 0): ?>
                  <?php
                    // Agrupar preguntas por capítulo y por bloque de enunciado.
                    // IMPORTANTE: la lógica de inputs/names se mantiene igual para no dañar el guardado.
                    $capitulos = [];
                    foreach ($preguntas as $index => $pregunta) {
                      $capitulo = trim((string)($pregunta['capitulo'] ?? ''));
                      if ($capitulo === '') $capitulo = 'Capítulo general';

                      $enunciado = $pregunta['enunciado_pregunta'] ?? '';
                      $textoAdicional = $pregunta['texto_adicional'] ?? '';
                      $groupKey = md5($capitulo . '||' . $enunciado . '||' . $textoAdicional);

                      if (!isset($capitulos[$capitulo])) {
                        $capitulos[$capitulo] = [
                          'titulo' => $capitulo,
                          'grupos' => []
                        ];
                      }

                      if (!isset($capitulos[$capitulo]['grupos'][$groupKey])) {
                        $capitulos[$capitulo]['grupos'][$groupKey] = [
                          'capitulo' => $capitulo,
                          'enunciado' => $enunciado,
                          'texto_adicional' => $textoAdicional,
                          'preguntas' => []
                        ];
                      }

                      $capitulos[$capitulo]['grupos'][$groupKey]['preguntas'][] = [
                        'index' => $index,
                        'data' => $pregunta
                      ];
                    }

                    $capitulosLista = array_values($capitulos);
                    $totalCapitulos = count($capitulosLista);
                  ?>

                  <div class="chapter-shell" id="chapterShell" data-total-chapters="<?= $totalCapitulos ?>">
                    <?php foreach ($capitulosLista as $capIndex => $capituloItem): ?>
                      <?php
                        $tituloCapitulo = $capituloItem['titulo'];
                        $nextTitulo = ($capIndex + 1 < $totalCapitulos) ? $capitulosLista[$capIndex + 1]['titulo'] : '';
                        $cantidadPreguntasCapitulo = 0;
                        foreach ($capituloItem['grupos'] as $tmpGrupo) {
                          $cantidadPreguntasCapitulo += count($tmpGrupo['preguntas']);
                        }
                      ?>

                      <section class="chapter-slide <?= $capIndex === 0 ? 'active' : '' ?>"
                               data-chapter-index="<?= $capIndex ?>"
                               data-chapter-title="<?= htmlspecialchars($tituloCapitulo) ?>"
                               data-next-title="<?= htmlspecialchars($nextTitulo) ?>">

                        <?php foreach ($capituloItem['grupos'] as $grupo): ?>
                          <?php if (!empty($grupo['enunciado']) || !empty($grupo['texto_adicional'])): ?>
                            <div class="q-enunciado-comun">
                              <?php if (!empty($grupo['texto_adicional'])): ?>
                                <div class="mb-2 fw-bold" style="font-size:15px; color:#0f172a;">
                                  <?= htmlspecialchars($grupo['texto_adicional']) ?>
                                </div>
                              <?php endif; ?>
                              <?php if (!empty($grupo['enunciado'])): ?>
                                <div style="font-style: Roboto, Arial, sans-serif; color:#000; line-height:1.6;">
                                  <?= htmlspecialchars($grupo['enunciado']) ?>
                                </div>
                              <?php endif; ?>
                            </div>
                          <?php endif; ?>

                          <?php foreach ($grupo['preguntas'] as $item): ?>
                            <?php
                              $index = $item['index'];
                              $pregunta = $item['data'];
                              $tipoPregunta = $pregunta['tipo_pregunta'];
                              $inputType = $tipoPregunta === 'Seleccion_Multiple_multiple_respuesta' ? 'checkbox' : 'radio';
                              $tieneOpciones = (!empty($pregunta['opciones']) && is_array($pregunta['opciones']));
                              $textoPregunta = htmlspecialchars($pregunta['texto_pregunta']);
                            ?>
                            <div class="q-card pregunta-card"
                                 data-index="<?= $index ?>"
                                 data-chapter-index="<?= $capIndex ?>"
                                 data-pregunta-id="<?= $pregunta['id'] ?>">

                              <div class="q-head">
                                <?php if (!empty($pregunta['numeral'])): ?>
                                  <div class="q-head-meta">
                                    <div class="q-numeral"><?= htmlspecialchars($pregunta['numeral']) ?></div>
                                  </div>
                                <?php endif; ?>
                                <div class="q-title">
                                  <?= $textoPregunta ?>
                                </div>
                              </div>

                              <div class="q-body">
                                <?php if (!$tieneOpciones): ?>
                                  <div class="hint">
                                    <i class="fa-solid fa-lightbulb"></i>
                                    <div>Escribe tu respuesta con claridad.</div>
                                  </div>
                                  <textarea class="form-control respuesta-texto"
                                            name="respuesta_texto_<?= $pregunta['id'] ?>"
                                            placeholder="Escribe tu respuesta..."
                                            maxlength="800"></textarea>
                                  <div class="text-end mt-2 text-muted fw-semibold" style="font-size:12px;">
                                    <span class="charCount">0</span>/800
                                  </div>
                                <?php else: ?>
                                  <?php
                                    $maxLen = 0;
                                    foreach ($pregunta['opciones'] as $op) {
                                      $len = mb_strlen($op['texto']);
                                      if ($len > $maxLen) $maxLen = $len;
                                    }
                                    $totalOpciones = count($pregunta['opciones']);
                                    $esHorizontal = ($maxLen <= 30 && $totalOpciones <= 8);
                                    $esCompacta = ($maxLen <= 3 && $totalOpciones >= 3 && $totalOpciones <= 10);
                                    $wrapClass = $esCompacta ? 'opts-compact' : ($esHorizontal ? 'opts-horizontal' : 'opts-vertical');
                                  ?>
                                  <div class="opts-wrap <?= $wrapClass ?>">
                                    <?php foreach ($pregunta['opciones'] as $opcion): ?>
                                      <?php
                                        $opId = $opcion['id'];
                                        $opTextoPlano = trim((string)($opcion['texto'] ?? ''));
                                        $opTexto = htmlspecialchars($opTextoPlano);
                                        $textoEsNumerico = preg_match('/^\d{1,2}$/', $opTextoPlano);
                                      ?>
                                      <label class="opt <?= $esHorizontal ? 'opt-h' : '' ?> <?= $esCompacta ? 'opt-compact' : '' ?>">
                                        <input class="form-check-input"
                                          type="<?= $inputType ?>"
                                          name="respuesta_<?= $pregunta['id'] ?><?= ($inputType === 'checkbox' ? '[]' : '') ?>"
                                          value="<?= $opId ?>">
                                        <?php if ($esCompacta): ?>
                                          <span class="opt-compact-check"><i class="fa-solid fa-check"></i></span>
                                          <div class="opt-compact-body">
                                            <span class="opt-compact-value"><?= $opTexto ?></span>
                                            <span class="opt-compact-caption"><?= $textoEsNumerico ? 'puntaje' : 'opción' ?></span>
                                          </div>
                                        <?php else: ?>
                                          <div class="form-check-label"><?= $opTexto ?></div>
                                        <?php endif; ?>
                                      </label>
                                    <?php endforeach; ?>
                                  </div>
                                <?php endif; ?>

                                <div class="q-footer-status">
                                  <div class="text-muted fw-semibold q-status-left">
                                    <i class="fa-solid fa-circle-check me-2 text-success"></i>
                                    <span class="qStatusText">Sin responder aún</span>
                                  </div>
                                  <div class="text-muted fw-semibold q-status-right">
                                    <i class="fa-solid fa-shield me-2"></i>
                                    Tu información se guarda de forma segura.
                                  </div>
                                </div>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        <?php endforeach; ?>

                      </section>
                    <?php endforeach; ?>
                  </div>

                  <!-- NAV BOTTOM -->
                  <div class="wizard-nav">
                    <div class="nav-left">
                      <button type="button" class="btn-nav" id="btnPrevChapter">
                        <i class="fa-solid fa-arrow-left me-2"></i>Anterior
                      </button>
                    </div>

                    <div class="wizard-nav-center"></div>

                    <div class="nav-right">
                      <button type="button" class="btn-nav btn-next" id="btnNextChapter">
                        Siguiente<i class="fa-solid fa-arrow-right ms-2"></i>
                      </button>
                      <button type="submit" class="btn-nav btn-send d-none" id="btnSend">
                        <i class="fa-solid fa-paper-plane me-2"></i>Enviar
                      </button>
                    </div>
                  </div>

                <?php else: ?>
                  <div class="alert alert-info text-center" style="border-radius:16px;">
                    No hay preguntas registradas para esta encuesta.
                  </div>
                <?php endif; ?>
              </form>
            </div>
          </div>
        </div>

      <?php endif; ?>
    </div>

  </div>
</div>

<!-- Modal para Ver Respuestas -->
<div class="modal fade" id="modalVerRespuestas" tabindex="-1" aria-labelledby="modalVerRespuestasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #13357b, #0b1a89);">
        <h5 class="modal-title" id="modalVerRespuestasLabel" style="color: #fff; font-weight:900;">
          <i class="fas fa-clipboard-check me-2"></i>Mis respuestas
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="contenedor-respuestas">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 mb-0 fw-semibold text-muted">Cargando tus respuestas...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-soft btn-soft-outline" data-bs-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Resumen -->
<div class="modal fade" id="modalResumen" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #13357b, #0b1a89);">
        <h5 class="modal-title" style="color:#fff; font-weight:900;">
          <i class="fa-solid fa-list-check me-2"></i>Resumen de respuestas
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="resumenBody" class="text-muted fw-semibold"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-soft btn-soft-outline" data-bs-dismiss="modal">Seguir respondiendo</button>
        <button type="button" class="btn btn-soft btn-soft-primary" id="btnIrEnviar">
          <i class="fa-solid fa-paper-plane me-2"></i>Ir a enviar
        </button>
      </div>
    </div>
  </div>
</div>

<?php include './admin/include/perfil.php'; ?>
<?php include './admin/include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="admin/js/perfil.js"></script>
<script src="admin/js/lib/util.js"></script>

<?php if ($mostrarSelector): ?>
<script>
  function verMisRespuestas(fichaTecnicaId) {
    const modal = new bootstrap.Modal(document.getElementById('modalVerRespuestas'));
    modal.show();

    $('#contenedor-respuestas').html(`
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 mb-0 fw-semibold text-muted">Cargando tus respuestas...</p>
      </div>
    `);

    $.ajax({
      url: 'admin/ajax/rqst.php',
      type: 'POST',
      dataType: 'json',
      data: { op: 'respuestavotante', ficha_tecnica_id: fichaTecnicaId },
      success: function(response) {
        if (response.output && response.output.valid) {
          const data = response.output.response;
          const fechaRespuesta = data.fecha_respuesta;
          const respuestas = data.respuestas || [];

          let html = `
            <div class="alert alert-info" style="border-radius:16px;">
              <i class="fas fa-calendar-alt me-2"></i>
              <strong>Fecha de respuesta:</strong> ${formatearFecha(fechaRespuesta)}
            </div>
          `;

          if (respuestas.length === 0) {
            html += '<div class="alert alert-warning" style="border-radius:16px;">No se encontraron respuestas.</div>';
          } else {
            respuestas.forEach((respuesta, index) => {
              html += `
                <div class="card mb-3" style="border-radius:16px; border:1px solid rgba(15,23,42,.10);">
                  <div class="card-body">
                    <div class="text-muted fw-semibold" style="font-size:12px;">Pregunta ${index + 1}</div>
                    <div class="fw-bold mb-2">${escapeHtml(respuesta.texto_pregunta || '')}</div>
              `;

              if (respuesta.opciones_seleccionadas && respuesta.opciones_seleccionadas.length > 0) {
                html += `<div class="mt-2">
                  <div class="fw-bold mb-1">Respuesta:</div>
                  <ul class="list-unstyled ms-2 mb-0">`;
                respuesta.opciones_seleccionadas.forEach(opcion => {
                  html += `<li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>${escapeHtml(opcion)}</li>`;
                });
                html += `</ul></div>`;
              }

              if (respuesta.respuesta_texto) {
                html += `
                  <div class="mt-2">
                    <div class="fw-bold mb-1">Respuesta:</div>
                    <div class="alert alert-light mb-0" style="border-radius:14px;">
                      ${escapeHtml(respuesta.respuesta_texto)}
                    </div>
                  </div>
                `;
              }

              html += `</div></div>`;
            });
          }

          $('#contenedor-respuestas').html(html);
        } else {
          $('#contenedor-respuestas').html(`
            <div class="alert alert-danger" style="border-radius:16px;">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Error al cargar las respuestas.
            </div>
          `);
        }
      },
      error: function() {
        $('#contenedor-respuestas').html(`
          <div class="alert alert-danger" style="border-radius:16px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error de conexión. Por favor, intenta nuevamente.
          </div>
        `);
      }
    });
  }

  function formatearFecha(fechaStr) {
    const fecha = new Date(fechaStr);
    const opciones = { year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' };
    return fecha.toLocaleDateString('es-ES', opciones);
  }

  function escapeHtml(str){
    return String(str || '')
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }
</script>
<?php else: ?>

<script>
(function(){
  const fichaId = <?= (int)$fichaTecnicaId ?>;
  const storageKey = "encuesta_borrador_" + fichaId;

  const cards = Array.from(document.querySelectorAll(".pregunta-card"));
  const total = cards.length;
  const chapterSlides = Array.from(document.querySelectorAll(".chapter-slide"));
  const totalChapters = chapterSlides.length;
  let currentChapter = 0;

  const form = document.getElementById("form_cuestionario");
  const progress = document.getElementById("progress_bar");
  const stepText = document.getElementById("stepText");
  const pctText  = document.getElementById("pctText");
  const helpText = document.getElementById("wizardHelp");
  const pillCounter = document.getElementById("pillCounter");
  const counterText = document.getElementById("counterText");
  const pillStatus = document.getElementById("pillStatus");
  const pillText = document.getElementById("pillText");
  const pillMini = document.getElementById("pillMini");
  const miniText = document.getElementById("miniText");
  const btnGuardarBorrador = document.getElementById("btnGuardarBorrador");
  const btnRevisarResumen = document.getElementById("btnRevisarResumen");
  const modalResumenEl = document.getElementById("modalResumen");
  const resumenBody = document.getElementById("resumenBody");
  const btnIrEnviar = document.getElementById("btnIrEnviar");
  const btnPrevChapter = document.getElementById("btnPrevChapter");
  const btnNextChapter = document.getElementById("btnNextChapter");
  const btnSend = document.getElementById("btnSend");
  const nextChapterText = document.getElementById("nextChapterText");

  // Contador chars
  document.querySelectorAll(".respuesta-texto").forEach(tx => {
    tx.addEventListener("input", () => {
      const cc = tx.closest(".q-body").querySelector(".charCount");
      if(cc) cc.textContent = String(tx.value.length);
      setAnsweredStateForCard(tx.closest(".pregunta-card"));
      autosave();
    });
  });

  // Radios / checkboxes
  document.querySelectorAll(".pregunta-card input[type='radio'], .pregunta-card input[type='checkbox']").forEach(inp => {
    inp.addEventListener("change", () => {
      setAnsweredStateForCard(inp.closest(".pregunta-card"));
      autosave();
    });
  });

  // Guardar borrador
  if(btnGuardarBorrador){
    btnGuardarBorrador.addEventListener("click", () => {
      autosave(true);
      Swal.fire({ icon:'success', title:'Borrador guardado', text:'Tus respuestas se guardaron en este dispositivo.', confirmButtonText:'Listo' });
    });
  }

  // Resumen
  if(btnRevisarResumen){
    btnRevisarResumen.addEventListener("click", () => {
      buildResumen();
      new bootstrap.Modal(modalResumenEl).show();
    });
  }
  if(btnIrEnviar){
    btnIrEnviar.addEventListener("click", () => {
      const m = bootstrap.Modal.getInstance(modalResumenEl);
      if(m) m.hide();
      showChapter(totalChapters - 1, true);
      const nav = document.querySelector(".wizard-nav");
      if(nav) nav.scrollIntoView({ behavior:"smooth", block:"center" });
    });
  }

  if(btnPrevChapter){
    btnPrevChapter.addEventListener("click", () => {
      if(currentChapter > 0) showChapter(currentChapter - 1, true);
    });
  }

  if(btnNextChapter){
    btnNextChapter.addEventListener("click", async () => {
      if(currentChapter >= totalChapters - 1) return;

      const chapterCards = getChapterCards(currentChapter);
      const answeredInChapter = chapterCards.filter(c => isCardAnswered(c)).length;
      const totalInChapter = chapterCards.length;

      if(answeredInChapter < totalInChapter){
        const res = await Swal.fire({
          icon:'warning',
          title:'Capítulo incompleto',
          html:`<div style="font-weight:700;color:#334155;">En este capítulo llevas <b>${answeredInChapter}/${totalInChapter}</b> preguntas respondidas.<br>¿Deseas avanzar al siguiente capítulo?</div>`,
          showCancelButton:true,
          confirmButtonText:'Sí, avanzar',
          cancelButtonText:'Seguir respondiendo',
          reverseButtons:true
        });
        if(!res.isConfirmed){
          const firstInvalid = chapterCards.find(c => !isCardAnswered(c));
          if(firstInvalid) firstInvalid.scrollIntoView({ behavior:'smooth', block:'center' });
          return;
        }
      }

      showChapter(currentChapter + 1, true);
    });
  }

  // Submit
  form.addEventListener("submit", async (e) => {
    if(e.defaultPrevented) return;
    e.preventDefault();

    // Validar todas
    let firstInvalid = null;
    for(let i = 0; i < total; i++){
      if(!isCardAnswered(cards[i])){
        if(!firstInvalid) firstInvalid = cards[i];
      }
    }

    const answered = countAnswered();

    if(firstInvalid){
      const res = await Swal.fire({
        icon:'warning',
        title:'Preguntas sin responder',
        html: `<div style="font-weight:700;color:#334155;">Respondiste <b>${answered}/${total}</b> preguntas.<br>¿Deseas enviar de todas formas o continuar respondiendo?</div>`,
        showCancelButton:true,
        confirmButtonText:'Enviar de todas formas',
        cancelButtonText:'Continuar respondiendo',
        reverseButtons:true
      });
      if(!res.isConfirmed){
        firstInvalid.scrollIntoView({ behavior:'smooth', block:'center' });
        return;
      }
    } else {
      const res = await Swal.fire({
        icon:'success',
        title:'Confirmar envío',
        html: `<div style="font-weight:700;color:#334155;">Respondiste <b>${answered}/${total}</b> preguntas. ¿Enviar ahora?<br><br><span style="color:#64748b;font-size:13px;">Después de enviar no podrás editar tus respuestas.</span></div>`,
        showCancelButton:true,
        confirmButtonText:'Sí, enviar',
        cancelButtonText:'Revisar',
        reverseButtons:true
      });
      if(!res.isConfirmed) return;
    }

    const payload = buildSubmissionPayload();
    if(payload.preguntas.length === 0){
      await Swal.fire({
        icon:'warning',
        title:'Sin respuestas para enviar',
        text:'Debes responder al menos una pregunta antes de enviar la encuesta.',
        confirmButtonText:'Entendido'
      });
      return;
    }

    setSubmittingState(true);

    try {
      const body = new URLSearchParams();
      body.set("op", "respuestasave");
      body.set("data", JSON.stringify(payload));

      const response = await fetch("admin/ajax/rqst.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: body.toString()
      });

      const raw = await response.text();
      let result = null;

      try {
        result = JSON.parse(raw);
      } catch (parseError) {
        throw new Error(raw || "La respuesta del servidor no es JSON válido.");
      }

      if(result?.output?.valid){
        localStorage.removeItem(storageKey);
        await Swal.fire({
          icon:'success',
          title:'Encuesta enviada',
          text:'Tus respuestas fueron guardadas correctamente.',
          confirmButtonText:'Aceptar'
        });
        window.location.href = "encuesta.php?enviada=1";
        return;
      }

      const errorMsg = result?.output?.response?.content || result?.output?.message || "No fue posible guardar la encuesta.";
      await Swal.fire({
        icon:'error',
        title:'No se pudo guardar',
        text:errorMsg,
        confirmButtonText:'Revisar'
      });
    } catch (error) {
      await Swal.fire({
        icon:'error',
        title:'Error de envío',
        text:error?.message || 'Ocurrió un problema al enviar la encuesta.',
        confirmButtonText:'Reintentar'
      });
    } finally {
      setSubmittingState(false);
    }
  });

  // Restaurar borrador e iniciar mostrando solo el primer capítulo
  restore();
  cards.forEach(card => setAnsweredStateForCard(card));
  showChapter(0, false);
  updateUI();

  function getChapterCards(chapterIndex){
    return cards.filter(card => Number(card.getAttribute("data-chapter-index")) === Number(chapterIndex));
  }

  function showChapter(chapterIndex, shouldScroll){
    if(totalChapters <= 0) return;
    currentChapter = Math.max(0, Math.min(chapterIndex, totalChapters - 1));

    chapterSlides.forEach((slide, i) => {
      slide.classList.toggle("active", i === currentChapter);
    });

    updateUI();

    if(shouldScroll){
      const top = document.querySelector(".wizard-top") || document.getElementById("form_cuestionario");
      if(top) top.scrollIntoView({ behavior:"smooth", block:"start" });
    }
  }

  function chapterStats(chapterIndex){
    const chapterCards = getChapterCards(chapterIndex);
    const answered = chapterCards.filter(c => isCardAnswered(c)).length;
    return { answered, total: chapterCards.length };
  }

  function isCardAnswered(card){
    const textarea = card.querySelector("textarea");
    const radios = card.querySelectorAll("input[type='radio']");
    const checks = card.querySelectorAll("input[type='checkbox']");
    if(textarea) return (textarea.value||"").trim().length >= 2;
    if(radios.length) return Array.from(radios).some(r=>r.checked);
    if(checks.length) return Array.from(checks).some(c=>c.checked);
    return true;
  }

  function syncOptionSelectionClasses(card){
    card.querySelectorAll(".opt").forEach(opt => {
      const input = opt.querySelector("input");
      opt.classList.toggle("selected", !!(input && input.checked));
    });
  }

  function setAnsweredStateForCard(card){
    const status = card.querySelector(".qStatusText");
    const answered = isCardAnswered(card);
    syncOptionSelectionClasses(card);
    if(status){
      status.textContent = answered ? "Respuesta lista ✅" : "Sin responder aún";
      status.style.color = answered ? "#166534" : "#64748b";
    }
    card.classList.toggle("answered", answered);
    updateUI();
  }

  function countAnswered(){
    return cards.filter(c => isCardAnswered(c)).length;
  }

  function updateUI(){
    if(total <= 0) return;
    const answered = countAnswered();
    const pct = Math.round((answered/total)*100);
    const currentSlide = chapterSlides[currentChapter];
    const chapterTitle = currentSlide ? (currentSlide.getAttribute("data-chapter-title") || "Capítulo") : "Capítulo";
    const nextTitle = currentSlide ? (currentSlide.getAttribute("data-next-title") || "") : "";
    const st = chapterStats(currentChapter);

    if(progress) progress.style.width = pct + "%";
    if(stepText) stepText.textContent = answered + "/" + total + " respondidas";
    if(pctText) pctText.textContent = pct + "%";
    if(counterText) counterText.textContent = answered + "/" + total + " respondidas";

    if(btnPrevChapter){
      btnPrevChapter.disabled = currentChapter === 0;
      btnPrevChapter.style.opacity = currentChapter === 0 ? ".55" : "1";
    }

    if(btnNextChapter){
      btnNextChapter.classList.toggle("d-none", currentChapter >= totalChapters - 1);
    }
    if(btnSend){
      btnSend.classList.toggle("d-none", currentChapter < totalChapters - 1);
    }

    const allDone = (answered === total);
    const chapterDone = (st.total > 0 && st.answered === st.total);

    if(pillStatus){
      pillStatus.classList.toggle("warn", !allDone);
      pillStatus.classList.toggle("ok", allDone);
      if(pillText) pillText.textContent = allDone ? "Lista para enviar" : "En progreso";
    }
    if(pillMini){
      pillMini.classList.toggle("warn", !chapterDone);
      pillMini.classList.toggle("ok", chapterDone);
      if(miniText) miniText.textContent = "Capítulo " + (currentChapter + 1) + "/" + totalChapters + " · " + st.answered + "/" + st.total;
    }
    if(nextChapterText){
      nextChapterText.textContent = nextTitle ? "Siguiente: " + nextTitle : "Último capítulo · revisa y envía";
    }
  }

  function autosave(force=false){
    try{
      const payload = {};
      cards.forEach(card=>{
        const pid = card.getAttribute("data-pregunta-id");
        const textarea = card.querySelector("textarea");
        const radios = card.querySelectorAll("input[type='radio']");
        const checks = card.querySelectorAll("input[type='checkbox']");
        if(textarea){ payload[pid] = { type:"text", value: textarea.value || "" }; }
        else if(radios.length){ const sel = Array.from(radios).find(r=>r.checked); payload[pid] = { type:"radio", value: sel ? sel.value : "" }; }
        else if(checks.length){ payload[pid] = { type:"check", value: Array.from(checks).filter(c=>c.checked).map(c=>c.value) }; }
      });
      localStorage.setItem(storageKey, JSON.stringify({ payload, t: Date.now() }));
    }catch(e){}
  }

  function buildSubmissionPayload(){
    const payload = {
      ficha_tecnica_id: fichaId,
      preguntas: []
    };

    cards.forEach(card => {
      const preguntaId = Number(card.getAttribute("data-pregunta-id"));
      const textarea = card.querySelector("textarea");
      const radios = card.querySelectorAll("input[type='radio']");
      const checks = card.querySelectorAll("input[type='checkbox']");
      const item = {
        pregunta_id: preguntaId,
        opciones: [],
        texto: ""
      };

      if(textarea){
        item.texto = (textarea.value || "").trim();
      } else if(radios.length){
        const selected = Array.from(radios).find(r => r.checked);
        if(selected) item.opciones = [Number(selected.value)];
      } else if(checks.length){
        item.opciones = Array.from(checks)
          .filter(c => c.checked)
          .map(c => Number(c.value));
      }

      if(item.opciones.length > 0 || item.texto.length > 0){
        payload.preguntas.push(item);
      }
    });

    return payload;
  }

  function setSubmittingState(isSubmitting){
    if(btnSend){
      btnSend.disabled = isSubmitting;
      btnSend.innerHTML = isSubmitting
        ? '<i class="fa-solid fa-spinner fa-spin me-2"></i>Enviando...'
        : '<i class="fa-solid fa-paper-plane me-2"></i>Enviar';
    }

    if(btnPrevChapter) btnPrevChapter.disabled = isSubmitting || currentChapter === 0;
    if(btnNextChapter) btnNextChapter.disabled = isSubmitting;
    if(btnGuardarBorrador) btnGuardarBorrador.disabled = isSubmitting;
    if(btnRevisarResumen) btnRevisarResumen.disabled = isSubmitting;
  }

  function restore(){
    try{
      const raw = localStorage.getItem(storageKey);
      if(!raw) return;
      const data = JSON.parse(raw);
      if(!data || !data.payload) return;
      Object.keys(data.payload).forEach(pid=>{
        const item = data.payload[pid];
        const card = document.querySelector(`.pregunta-card[data-pregunta-id="${pid}"]`);
        if(!card) return;
        if(item.type === "text"){ const tx = card.querySelector("textarea"); if(tx){ tx.value = item.value || ""; const cc = card.querySelector(".charCount"); if(cc) cc.textContent = String(tx.value.length); } }
        if(item.type === "radio"){ const r = card.querySelector(`input[type="radio"][value="${item.value}"]`); if(r) r.checked = true; }
        if(item.type === "check" && Array.isArray(item.value)){ item.value.forEach(v=>{ const c = card.querySelector(`input[type="checkbox"][value="${v}"]`); if(c) c.checked = true; }); }
        setAnsweredStateForCard(card);
      });
    }catch(e){}
  }

  function buildResumen(){
    const rows = [];
    cards.forEach((card, i) => {
      const title = card.querySelector(".q-title") ? card.querySelector(".q-title").textContent.trim() : ("Pregunta " + (i+1));
      const textarea = card.querySelector("textarea");
      const radios = card.querySelectorAll("input[type='radio']");
      const checks = card.querySelectorAll("input[type='checkbox']");
      let ans = "";
      if(textarea){ ans = (textarea.value || "").trim(); }
      else if(radios.length){ const sel = Array.from(radios).find(r=>r.checked); if(sel){ const lab = sel.closest("label.opt"); ans = lab ? lab.querySelector(".form-check-label").textContent.trim() : "Seleccionado"; } }
      else if(checks.length){ ans = Array.from(checks).filter(c=>c.checked).map(s=>{ const lab = s.closest("label.opt"); return lab ? lab.querySelector(".form-check-label").textContent.trim() : "Seleccionado"; }).join(", "); }
      rows.push(`<div class="card mb-2" style="border-radius:16px;border:1px solid rgba(15,23,42,.10);"><div class="card-body"><div class="text-muted fw-semibold" style="font-size:12px;">${i+1}/${total}</div><div class="fw-bold mb-2">${escapeHtml(title)}</div><div class="alert alert-light mb-0" style="border-radius:14px;">${ans ? escapeHtml(ans) : '<span class="text-danger fw-bold">Sin respuesta</span>'}</div></div></div>`);
    });
    resumenBody.innerHTML = rows.join("");
  }

  function escapeHtml(str){
    return String(str||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;");
  }

})();
</script>

<?php endif; ?>
</body>
</html>
