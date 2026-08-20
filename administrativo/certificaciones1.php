<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/CertificacionEncuestador.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// ✅ PON TU KEY EN EL CONFIG:
// $GOOGLE_MAPS_API_KEY = 'TU_KEY_AQUI';

$permissions = [
  'view' => SessionData::administrador() || SessionData::superAdministrador(),
];

if (!$permissions['view']) {
  require 'permiso_denegado.php';
  exit;
}

// Obtener todas las certificaciones
$arr = CertificacionEncuestador::getAll([]);
$isvalid = $arr['output']['valid'] ?? false;
$certificaciones = $arr['output']['response'] ?? [];

$modulo = 'Detalles Encuestas';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$GOOGLE_MAPS_API_KEY = $GOOGLE_MAPS_API_KEY ?? '';
?>

<style>
  :root{
    --brand:#20427F;
    --brand2:#132b52;
    --ink:#0f172a;
    --muted:#64748b;
    --bg:#f6f8fb;
    --line: rgba(15, 23, 42, .08);
    --radius:18px;
    --shadow: 0 14px 40px rgba(2, 6, 23, .08);
  }
  body{ background: var(--bg); }
  .content{ padding-top: 14px !important; padding-bottom: 40px !important; }
  @media (min-width: 1400px){ .container-xxl-saas{ max-width: 1600px; } }

  .card-pro{
    border: 1px solid var(--line) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    background: #fff !important;
    overflow: hidden;
  }
  .card-pro .card-header{
    background: rgba(255,255,255,.92) !important;
    border-bottom: 1px solid var(--line) !important;
    padding: 16px 18px !important;
  }
  .title{ font-weight: 900; color: var(--ink); margin: 0; }
  .sub{ color: var(--muted); font-weight: 600; margin-top: 4px; }

  .table-wrap{
    border: 1px solid var(--line);
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--shadow);
    overflow: hidden;
  }

  /* ✅ TABLA MÁS PEQUEÑA Y COMPACTA */
  #tblCertificaciones{
    width: 100% !important;
  }
  #tblCertificaciones thead th{
    font-weight: 900 !important;
    color: var(--ink) !important;
    white-space: nowrap;
    font-size: .78rem;           /* ✅ más pequeña */
  }
  #tblCertificaciones td{
    font-size: .78rem;           /* ✅ más pequeña */
    vertical-align: middle;
  }
  .dt-compact td, .dt-compact th{ padding: .42rem .45rem !important; } /* ✅ más compacto */

  /* ✅ DataTables (paginación, buscador y select) */
  .dataTables_wrapper .dataTables_filter input,
  .dataTables_wrapper .dataTables_length select{
    border-radius: 12px !important;
    border: 1px solid rgba(2,6,23,.12) !important;
    padding: .35rem .55rem !important;
    font-size: .82rem !important;
  }
  .dataTables_wrapper .dataTables_filter label,
  .dataTables_wrapper .dataTables_length label{
    font-weight: 800;
    color: var(--muted);
    font-size: .82rem;
  }
  .dataTables_wrapper .dataTables_info{
    font-size: .82rem;
    color: var(--muted);
    font-weight: 700;
    padding-top: .65rem;
  }
  .dataTables_wrapper .dataTables_paginate{
    padding-top: .65rem;
  }
  .dataTables_wrapper .paginate_button{
    border-radius: 12px !important;
    padding: .22rem .6rem !important;
    font-size: .82rem !important;
  }

  /* ✅ Modal PRO */
  .modal-pro .modal-content{
    border: 1px solid var(--line);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(2,6,23,.22);
  }
  .modal-pro .modal-header{
    background: linear-gradient(135deg, rgba(32,66,127,.10), rgba(19,43,82,.06));
    border-bottom: 1px solid var(--line);
  }
  .modal-pro .modal-title{
    font-weight: 900;
    color: var(--ink);
  }
  .kpi{
    border: 1px solid var(--line);
    border-radius: 16px;
    background: #fff;
    padding: 12px 14px;
  }
  .kpi .label{ color: var(--muted); font-weight: 700; font-size: 12px; }
  .kpi .value{ color: var(--ink); font-weight: 900; font-size: 14px; margin-top: 2px; }
  .map-box{
    border: 1px solid var(--line);
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
  }
  #mapCanvas{
    width: 100%;
    height: 320px;
  }
  @media (max-width: 768px){
    #mapCanvas{ height: 260px; }
  }
</style>

<body class="">
  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <div class="content">
    <div class="container-fluid container-xxl-saas">

      <div class="card card-pro my-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <h3 class="title mb-1"><i class="fas fa-shield-alt me-2" style="color:var(--brand)"></i><?= h($modulo) ?></h3>
              <div class="sub">Todas las encuestas registradas con audio y GPS</div>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:800;">
              Total: <?= (int)count($certificaciones) ?>
            </div>
          </div>
        </div>

        <div class="card-body p-3 p-lg-4">
          <div class="table-wrap p-3">
            <div class="table-responsive">
              <table id="tblCertificaciones" class="table table-striped table-hover dt-compact align-middle">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Encuestador</th>
                    <th>Votante</th>
                    <th>Origen</th>
                    <th>GPS</th>
                    <th>Audio</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($certificaciones as $cert): ?>
                  <?php
                    $id = (int)($cert['id'] ?? 0);
                    $fechaTxt = '';
                    if (!empty($cert['fecha_certificacion'])) {
                      try {
                        $fecha = new DateTime($cert['fecha_certificacion']);
                        $fechaTxt = $fecha->format('d/m/Y H:i');
                      } catch(Exception $e){ $fechaTxt = h($cert['fecha_certificacion']); }
                    }

                    // Origen badge
                    $origenTipo = $cert['origen_tipo'] ?? '';
                    $badge = 'secondary';
                    $icon  = 'fa-user';
                    $texto = 'Registro Simple';

                    if ($origenTipo === 'sondeo') {
                      $badge = 'primary';
                      $icon = 'fa-poll';
                      $texto = 'Sondeo: ' . h($cert['sondeo_nombre'] ?? 'N/A');
                    } elseif ($origenTipo === 'cuestionario') {
                      $badge = 'info';
                      $icon = 'fa-clipboard-list';
                      $texto = 'Cuestionario: ' . h($cert['cuestionario_nombre'] ?? 'N/A');
                    }

                    $hasGps = !empty($cert['latitud']) && !empty($cert['longitud']);
                    $hasAudio = !empty($cert['audio_duracion_segundos']);
                  ?>
                  <tr>
                    <td><span class="fw-bold"><?= $id ?></span></td>
                    <td class="text-nowrap"><?= h($fechaTxt) ?></td>
                    <td><?= h(($cert['encuestador_nombre'] ?? '') . ' ' . ($cert['encuestador_apellido'] ?? '')) ?></td>
                    <td><?= h($cert['votante_nombre'] ?? '') ?></td>
                    <td class="text-nowrap">
                      <span class="badge bg-<?= h($badge) ?>">
                        <i class="fas <?= h($icon) ?> me-1"></i><?= $texto ?>
                      </span>
                    </td>
                    <td class="text-nowrap">
                      <?php if ($hasGps): ?>
                        <span class="badge bg-success-subtle text-success fw-bold">
                          <i class="fas fa-location-dot me-1"></i>OK
                        </span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                      <?php if ($hasAudio): ?>
                        <span class="badge bg-success">
                          <i class="fas fa-microphone me-1"></i><?= (int)$cert['audio_duracion_segundos'] ?>s
                        </span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Sin</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                      <button type="button"
                        class="btn btn-sm btn-primary"
                        onclick="CERTIFICACIONES.verDetalle(<?= $id ?>)">
                        <i class="fas fa-eye me-1"></i>Detalle
                      </button>

                      <?php if ($hasGps): ?>
                        <a class="btn btn-sm btn-outline-primary"
                           target="_blank"
                           href="https://www.google.com/maps?q=<?= h($cert['latitud']) ?>,<?= h($cert['longitud']) ?>">
                          <i class="fas fa-map-marker-alt me-1"></i>Mapa
                        </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

  <!-- ✅ Modal Detalle PRO -->
  <div class="modal fade modal-pro" id="modalDetalleCertificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-shield-alt me-2" style="color:var(--brand)"></i>
            Detalle de la Encuesta
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" id="modalDetalleCertificacionBody">
          <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x" style="color:var(--brand)"></i>
            <p class="mt-3 mb-0" style="font-weight:800;color:var(--muted)">Cargando información...</p>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-xmark me-1"></i>Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'admin/include/gerenic_script.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>

  <!-- ✅ KEY para Maps en JS (se administra desde config) -->
  <script>
    window.GOOGLE_MAPS_API_KEY = "<?= h($GOOGLE_MAPS_API_KEY) ?>";
  </script>

  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= h($GOOGLE_MAPS_API_KEY) ?>&callback=initMap">
  </script>

  <script type="text/javascript" src="admin/js/certificaciones.js"></script>

  <!-- ✅ DataTables init (paginación + tamaño + sin tocar backend) -->
  <script>
    $(document).ready(function () {
      // Si ya estaba inicializada por alguna lib, la destruimos seguro
      if ($.fn.DataTable.isDataTable('#tblCertificaciones')) {
        $('#tblCertificaciones').DataTable().destroy();
      }

      $('#tblCertificaciones').DataTable({
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        ordering: true,
        searching: true,
        responsive: true,
        autoWidth: false,
        deferRender: true,
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
        },
        columnDefs: [
          { targets: [0,5,6,7], orderable: true },
          { targets: [7], searchable: false } // acciones no buscar
        ]
      });
    });
  </script>

</body>
</html>
