<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/include/generic_info_configuracion.php';

include './admin/classes/FichaTecnicaEncuesta.php';

$view = SessionData::hasPermission('encuestas.preguntas.view');
if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

$viewFicha = SessionData::hasPermission('estudios.ficha_tecnica.view');

function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function fichaLabel($row) {
    $temas = trim((string)($row['temas_concretos'] ?? ''));
    if ($temas !== '') {
        return $temas;
    }
    $realizada = trim((string)($row['realizada_por_o_encomendada_por'] ?? ''));
    if ($realizada !== '') {
        return $realizada;
    }
    return 'Ficha #' . (int)($row['id'] ?? 0);
}

function cellText($s, $max = 80) {
    $s = trim((string)$s);
    if ($s === '') {
        return '<span class="text-muted">—</span>';
    }
    $full = h($s);
    if (mb_strlen($s) <= $max) {
        return '<span class="qst-cell-text" title="' . $full . '">' . $full . '</span>';
    }
    return '<span class="qst-cell-text" title="' . $full . '">' . h(mb_substr($s, 0, $max)) . '…</span>';
}

$resp = FichaTecnicaEncuesta::getAllConResumenPreguntas();
$isvalid = $resp['output']['valid'] ?? false;
$arr = $resp['output']['response'] ?? [];
$modulo = 'Cuestionario de Preguntas';

$totalFichasKpi = is_array($arr) ? count($arr) : 0;
$totalFichasActivasKpi = 0;
$totalPreguntasKpi = 0;
$totalPreguntasActivasKpi = 0;

if (is_array($arr)) {
    foreach ($arr as $ficha) {
        if (($ficha['habilitado'] ?? '') === 'si') {
            $totalFichasActivasKpi++;
        }
        $totalPreguntasKpi += (int)($ficha['total_preguntas'] ?? 0);
        $totalPreguntasActivasKpi += (int)($ficha['preguntas_activas'] ?? 0);
    }
}

$errMsg = '';
if (isset($_GET['err'])) {
    if ($_GET['err'] === 'ficha') {
        $errMsg = 'La ficha técnica solicitada no existe o no está disponible.';
    } elseif ($_GET['err'] === 'eliminada') {
        $errMsg = 'La ficha técnica fue eliminada y no puede editarse.';
    }
}
?>
<!-- *******************inicio body************************* -->

<body class="qst-page">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <style>
    :root{
      --qst-navy-900:#0A2248;
      --qst-blue-700:#20427F;
      --qst-blue-600:#2D63BD;
      --qst-blue-500:#4B8CF7;
      --qst-success:#12B981;
      --qst-warning:#F59E0B;
      --qst-danger:#E5484D;
      --qst-text:#101828;
      --qst-muted:#667085;
      --qst-light:#98A2B3;
      --qst-line:#E5EAF1;
      --qst-r-xxl:30px;
      --qst-r-xl:24px;
      --qst-shadow-soft:0 12px 34px rgba(15,23,42,.065);
    }

    body.qst-page{
      margin:0;
      color:var(--qst-text);
      font-family:"Inter",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      background:
        radial-gradient(920px 500px at 3% -5%,rgba(75,140,247,.12),transparent 64%),
        linear-gradient(180deg,#F8FAFD 0%,#F2F5FA 100%);
    }

    .content{
      padding-top:18px !important;
      padding-bottom:38px !important;
      margin-top:0 !important;
    }

    .qst-shell{
      width:100%;
      max-width:1660px;
      margin:0 auto;
      padding:0 18px;
    }

    .qst-hero{
      position:relative;
      overflow:hidden;
      min-height:200px;
      margin-bottom:16px;
      padding:28px 30px;
      border:1px solid rgba(255,255,255,.12);
      border-radius:var(--qst-r-xxl);
      color:#fff;
      background:
        radial-gradient(550px 275px at 9% 0%,rgba(75,140,247,.35),transparent 66%),
        linear-gradient(135deg,#173E7B 0%,#102A56 47%,#07162E 100%);
      box-shadow:0 30px 80px rgba(8,28,63,.24);
    }

    .qst-hero-grid{
      display:grid;
      grid-template-columns:minmax(0,1fr) auto;
      gap:28px;
      align-items:center;
    }

    .qst-eyebrow{
      display:inline-flex;
      align-items:center;
      gap:8px;
      min-height:32px;
      margin-bottom:13px;
      padding:7px 11px;
      border:1px solid rgba(255,255,255,.14);
      border-radius:999px;
      color:rgba(255,255,255,.88);
      background:rgba(255,255,255,.075);
      font-size:.67rem;
      font-weight:800;
      letter-spacing:.62px;
      text-transform:uppercase;
    }

    .qst-live-dot{
      width:7px;
      height:7px;
      border-radius:50%;
      background:#5DE4A0;
      box-shadow:0 0 16px rgba(93,228,160,.45);
    }

    .qst-hero h1{
      margin:0;
      color:#fff;
      font-family:"Manrope","Inter",sans-serif;
      font-size:clamp(1.85rem,3vw,2.6rem);
      line-height:1.04;
      font-weight:800;
      letter-spacing:-1px;
    }

    .qst-hero h1 span{ color:#B7D0FF; }

    .qst-hero p{
      max-width:780px;
      margin:11px 0 0;
      color:rgba(255,255,255,.70);
      font-size:.91rem;
      line-height:1.6;
    }

    .qst-kpis{
      display:grid;
      grid-template-columns:repeat(2,minmax(130px,1fr));
      gap:10px;
    }

    .qst-kpi{
      min-height:108px;
      padding:14px;
      border:1px solid rgba(255,255,255,.12);
      border-radius:18px;
      background:rgba(255,255,255,.06);
      backdrop-filter:blur(10px);
    }

    .qst-kpi strong{
      display:block;
      margin-top:8px;
      color:#fff;
      font-size:1.55rem;
      font-weight:800;
      letter-spacing:-.55px;
    }

    .qst-kpi span{
      display:block;
      margin-top:5px;
      color:rgba(255,255,255,.58);
      font-size:.62rem;
      font-weight:700;
    }

    .qst-kpi-icon{
      width:34px;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:11px;
      color:#fff;
      background:rgba(255,255,255,.12);
      font-size:.85rem;
    }

    .qst-panel{
      overflow:hidden;
      border:1px solid var(--qst-line);
      border-radius:var(--qst-r-xl);
      background:#fff;
      box-shadow:var(--qst-shadow-soft);
    }

    .qst-panel-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:16px 18px;
      border-bottom:1px solid #EDF0F5;
      background:linear-gradient(180deg,#FFFFFF,#FBFCFF);
    }

    .qst-panel-head strong{
      display:block;
      font-size:.84rem;
      font-weight:800;
    }

    .qst-panel-head span{
      display:block;
      margin-top:2px;
      color:var(--qst-light);
      font-size:.66rem;
      font-weight:600;
    }

    .qst-btn{
      min-height:40px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:8px 14px;
      border-radius:12px;
      font-size:.72rem;
      font-weight:800;
      text-decoration:none !important;
      transition:transform .18s ease, box-shadow .18s ease;
    }

    .qst-btn-primary{
      border:0;
      color:#fff !important;
      background:linear-gradient(135deg,var(--qst-blue-500),var(--qst-blue-700));
      box-shadow:0 11px 23px rgba(32,66,127,.22);
    }

    .qst-btn-primary:hover{ transform:translateY(-2px); }

    .qst-btn-soft{
      border:1px solid #D7E2F2;
      color:var(--qst-blue-700) !important;
      background:#fff;
    }

    .qst-table-wrap{ padding:0 18px 18px; }

    .qst-table{
      width:100%;
      margin:0;
    }

    .qst-table thead th{
      color:#475467;
      font-size:.68rem;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.4px;
      border-bottom:1px solid #E8EDF5 !important;
      background:#F8FAFD;
    }

    .qst-table tbody td{
      vertical-align:middle;
      font-size:.78rem;
      border-color:#EEF2F7 !important;
    }

    .qst-cell-text{
      display:inline-block;
      max-width:320px;
    }

    .qst-badge{
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:5px 10px;
      border-radius:999px;
      font-size:.64rem;
      font-weight:800;
    }

    .qst-badge-ok{
      color:#067647;
      background:#ECFDF3;
      border:1px solid #ABEFC6;
    }

    .qst-badge-off{
      color:#B42318;
      background:#FEF3F2;
      border:1px solid #FECDCA;
    }

    .qst-badge-muted{
      color:#475467;
      background:#F2F4F7;
      border:1px solid #E4E7EC;
    }

    .qst-empty{
      padding:42px 18px;
      text-align:center;
      color:var(--qst-muted);
    }

    @media (max-width:992px){
      .qst-hero-grid{ grid-template-columns:1fr; }
      .qst-kpis{ grid-template-columns:repeat(2,1fr); }
    }

    @media (max-width:576px){
      .qst-kpis{ grid-template-columns:1fr; }
      .qst-panel-head{ flex-direction:column; align-items:flex-start; }
    }
  </style>

  <div class="content">
    <div class="qst-shell">

      <section class="qst-hero">
        <div class="qst-hero-grid">
          <div>
            <div class="qst-eyebrow">
              <span class="qst-live-dot"></span>
              Estadística360 · Questionnaire Intelligence
            </div>
            <h1>Cuestionario de <span>Preguntas</span></h1>
            <p>
              Seleccione una ficha técnica para administrar su cuestionario:
              capítulos, enunciados, opciones, importación masiva y estado de cada pregunta.
            </p>
            <span id="spanModulo" class="d-none"><?= h($modulo) ?></span>
          </div>

          <div class="qst-kpis">
            <div class="qst-kpi">
              <div class="qst-kpi-icon"><i class="fas fa-clipboard-list"></i></div>
              <strong><?= (int)$totalFichasKpi ?></strong>
              <span>Fichas técnicas</span>
            </div>
            <div class="qst-kpi">
              <div class="qst-kpi-icon"><i class="fas fa-circle-check"></i></div>
              <strong><?= (int)$totalFichasActivasKpi ?></strong>
              <span>Fichas activas</span>
            </div>
            <div class="qst-kpi">
              <div class="qst-kpi-icon"><i class="fas fa-circle-question"></i></div>
              <strong><?= (int)$totalPreguntasKpi ?></strong>
              <span>Preguntas registradas</span>
            </div>
            <div class="qst-kpi">
              <div class="qst-kpi-icon"><i class="fas fa-toggle-on"></i></div>
              <strong><?= (int)$totalPreguntasActivasKpi ?></strong>
              <span>Preguntas activas</span>
            </div>
          </div>
        </div>
      </section>

      <section class="qst-panel">
        <div class="qst-panel-head">
          <div>
            <strong>Fichas técnicas con cuestionario</strong>
            <span>Cada fila abre el editor de preguntas de esa ficha.</span>
          </div>
          <?php if ($viewFicha): ?>
          <a href="ficha_tecnica_encuesta.php" class="qst-btn qst-btn-soft">
            <i class="fas fa-file-lines"></i>
            Gestionar fichas técnicas
          </a>
          <?php endif; ?>
        </div>

        <div class="qst-table-wrap">
          <?php if (!$isvalid || empty($arr)): ?>
            <div class="qst-empty">
              <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
              <strong>No hay fichas técnicas disponibles.</strong>
              <p class="mb-0 mt-2 small">
                <?php if ($viewFicha): ?>
                  Cree una ficha en <a href="ficha_tecnica_encuesta.php">Ficha Técnica Encuesta</a> y vuelva aquí.
                <?php else: ?>
                  Solicite permisos para crear fichas técnicas o contacte al administrador.
                <?php endif; ?>
              </p>
            </div>
          <?php else: ?>
            <table class="table qst-table" id="tblCuestionariosHub">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Cuestionario / Ficha</th>
                  <th>Realizada por</th>
                  <th>Estado ficha</th>
                  <th class="text-center">Preguntas</th>
                  <th class="text-center">Activas</th>
                  <th class="text-end">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($arr as $ficha):
                  $fid = (int)($ficha['id'] ?? 0);
                  $label = fichaLabel($ficha);
                  $habilitado = ($ficha['habilitado'] ?? '') === 'si';
                  $totalPreg = (int)($ficha['total_preguntas'] ?? 0);
                  $activas = (int)($ficha['preguntas_activas'] ?? 0);
                ?>
                <tr>
                  <td><?= $fid ?></td>
                  <td><?= cellText($label, 100) ?></td>
                  <td><?= cellText($ficha['realizada_por_o_encomendada_por'] ?? '', 60) ?></td>
                  <td>
                    <?php if ($habilitado): ?>
                      <span class="qst-badge qst-badge-ok"><i class="fas fa-check"></i> Activa</span>
                    <?php else: ?>
                      <span class="qst-badge qst-badge-off"><i class="fas fa-ban"></i> Inactiva</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <span class="qst-badge qst-badge-muted"><?= $totalPreg ?></span>
                  </td>
                  <td class="text-center">
                    <span class="qst-badge qst-badge-muted"><?= $activas ?></span>
                  </td>
                  <td class="text-end">
                    <a href="cuestionario_preguntas.php?ficha_id=<?= $fid ?>"
                       class="qst-btn qst-btn-primary">
                      <i class="fas fa-list-check"></i>
                      Gestionar preguntas
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </section>

    </div>
  </div>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <?php if ($errMsg !== ''): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Atención', text: <?= json_encode($errMsg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> });
      }
    });
  </script>
  <?php endif; ?>

  <?php if ($isvalid && !empty($arr)): ?>
  <?php include './admin/include/generic_dataTables.php'; ?>
  <script>
    $(function () {
      if ($.fn.DataTable && $('#tblCuestionariosHub').length) {
        $('#tblCuestionariosHub').DataTable({
          order: [[0, 'desc']],
          pageLength: 25,
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
          },
          columnDefs: [{ orderable: false, targets: 6 }]
        });
      }
    });
  </script>
  <?php endif; ?>

  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
