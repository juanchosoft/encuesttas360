<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/EspacioGeografico.php';
include './admin/classes/Departamento.php';

// Variables config (logo, etc.)
include './admin/include/generic_info_configuracion.php';

// Permisos
$view    = SessionData::getPermission(14);
$create  = SessionData::getPermission(15);
$edit    = SessionData::getPermission(16);
$permits = SessionData::getPermission(17);
if (!$view) { require 'permiso_denegado.php'; exit; }

// Datos
$resp = EspacioGeografico::getAll(null);
$isvalidEspacio = $resp['output']['valid'] ?? false;
$arr = $resp['output']['response'] ?? [];
$modulo = 'Espacio Geográfico';

// Departamentos
$arrDepResp = Departamento::getAll(null);
$arrDep = $arrDepResp['output']['response'] ?? [];

$optionDep = '<option value="" selected disabled>Seleccione un departamento</option>';
foreach ($arrDep as $dep) {
  $cd = htmlspecialchars($dep['codigo_departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $nm = htmlspecialchars($dep['departamento'] ?? '', ENT_QUOTES, 'UTF-8');
  $optionDep .= "<option value='{$cd}'>{$cd} - {$nm}</option>";
}
$optionDep .= "<option value='00'>00 - Estudio Nacional (Todos los departamentos)</option>";

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function tipoBadge($tipo){
  $t = strtolower(trim((string)$tipo));
  if ($t === 'nacional') return ['bg'=>'badge-brand','icon'=>'fa-globe-americas','txt'=>'Nacional'];
  if ($t === 'departamental') return ['bg'=>'badge-info','icon'=>'fa-layer-group','txt'=>'Departamental'];
  if ($t === 'municipal') return ['bg'=>'badge-warning','icon'=>'fa-city','txt'=>'Municipal'];
  return ['bg'=>'badge-muted','icon'=>'fa-circle-info','txt'=>($tipo ?: 'N/A')];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Choices -->
  <link rel="stylesheet" href="admin/js/lib/choices.min.css" />

  <style>
    :root{
      --brand:#20427F;
      --brand2:#132b52;
      --brand3:#2e58a8;

      --white:#fff;
      --ink:#0f172a;
      --muted:#64748b;
      --bg:#f6f8fb;
      --card:#ffffff;
      --line: rgba(15, 23, 42, .08);

      --radius-xl: 22px;
      --radius-lg: 16px;

      --shadow-soft: 0 12px 30px rgba(2, 6, 23, .10);
      --shadow-mid:  0 18px 40px rgba(2, 6, 23, .14);
    }

    body{ background: var(--bg) !important; }

    /* =========================================================
       ✅ FIX GLOBAL: raya azul + contenido muy abajo del header
       (sin tocar header.php ni navbar.php)
       ========================================================= */

    /* En muchos themes el offset viene en .content o en main container */
    .main .content{
      padding-top: 0 !important;
      margin-top: 0 !important;
      border-top: 0 !important;
      box-shadow: none !important;
      outline: 0 !important;
      position: relative;
    }

    /* Quita pseudo-elementos que suelen dibujar líneas */
    .main .content::before,
    .main .content::after{
      content: none !important;
      display: none !important;
    }

    /* La “raya azul” muchas veces es un borde o shadow del contenedor superior */
    .pcoded-main-container,
    .pcoded-content,
    .pcoded-header,
    .navbar-top,
    #navbarDefault,
    header{
      border-top: 0 !important;
      border-bottom: 0 !important;
      box-shadow: none !important;
      outline: 0 !important;
    }

    /* Si tu template mete un divider/hairline */
    .navbar-bottom-line,
    .header-bottom-line,
    .topbar-divider,
    .content-divider{
      display:none !important;
      height:0 !important;
      background:transparent !important;
    }

    /* Subimos el contenido “pegado” al header pero con aire pro */
    .main .content > .container-fluid{
      margin-top: 12px !important;
      padding-top: 0 !important;
      padding-bottom: 38px !important;
    }

    @media (max-width: 991.98px){
      .main .content > .container-fluid{
        margin-top: 10px !important;
      }
    }

    @media (min-width: 1400px){ .container-xxl-saas{ max-width: 1500px; } }

    /* =========================================================
       ✅ TU UI SAAS (igual que venías)
       ========================================================= */

    /* HERO SaaS */
    .saas-hero{
      background:
        radial-gradient(1200px 600px at 15% 15%, rgba(46,88,168,.18), transparent 55%),
        radial-gradient(900px 500px at 80% 30%, rgba(32,66,127,.16), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.82));
      border: 1px solid var(--line);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-soft);
      padding: 18px;
      position: relative;
      overflow: hidden;
      margin: 0 0 16px 0; /* ✅ quitamos el margin-top que te bajaba más */
    }
    .saas-hero:before{
      content:"";
      position:absolute; inset:-2px;
      background: linear-gradient(135deg, rgba(32,66,127,.20), rgba(19,43,82,.06), rgba(46,88,168,.18));
      filter: blur(18px);
      opacity:.65;
      z-index:0;
    }
    .saas-hero > *{ position:relative; z-index:1; }

    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:7px 12px; border-radius: 999px;
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.14);
      color: var(--brand2);
      font-weight: 900;
      font-size: 12px;
      white-space: nowrap;
    }

    .title{ font-weight: 1000; color: var(--ink); margin: 0; }
    .sub{ color: var(--muted); font-weight: 600; margin-top: 4px; }

    /* Cards Pro */
    .card-pro{
      border: 1px solid var(--line) !important;
      border-radius: var(--radius-xl) !important;
      box-shadow: var(--shadow-soft) !important;
      background: var(--card) !important;
      overflow: hidden;
    }
    .card-pro .card-header{
      background: rgba(255,255,255,.92) !important;
      border-bottom: 1px solid var(--line) !important;
      padding: 16px 18px !important;
    }
    .card-pro .card-body{ padding: 18px; }

    .section-card{
      border: 1px solid rgba(15,23,42,.08);
      border-radius: var(--radius-lg);
      background: rgba(255,255,255,.92);
      padding: 14px;
    }
    .section-title{
      font-weight: 1000; color: var(--ink);
      margin: 0 0 10px 0;
      display:flex; align-items:center; gap:10px;
    }
    .section-title .dot{
      width: 12px; height: 12px; border-radius: 99px;
      background: linear-gradient(135deg, var(--brand3), var(--brand2));
      box-shadow: 0 10px 22px rgba(32,66,127,.18);
    }

    /* Inputs */
    .form-floating>.form-control, .form-floating>.form-select{
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.12);
      box-shadow: none;
    }
    .form-control:focus,.form-select:focus{
      border-color: rgba(32,66,127,.45) !important;
      box-shadow: 0 0 0 .25rem rgba(32,66,127,.12) !important;
    }

    /* Buttons */
    .btn-brand{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border: 0 !important;
      border-radius: 14px;
      font-weight: 1000;
      padding: 12px 18px;
      box-shadow: 0 14px 30px rgba(32,66,127,.18);
      transition: transform .16s ease, box-shadow .16s ease;
      color: #fff !important;
    }
    .btn-brand:hover{ transform: translateY(-2px); box-shadow: 0 18px 44px rgba(32,66,127,.25); }

    .btn-soft{
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.18) !important;
      color: var(--brand2) !important;
      border-radius: 14px;
      font-weight: 1000;
      padding: 12px 16px;
    }

    .btn-mini{
      border-radius: 12px !important;
      font-weight: 1000 !important;
      padding: .48rem .70rem !important;
    }

    /* Action bar sticky */
    .action-bar{
      position: sticky;
      bottom: 12px;
      z-index: 10;
      margin-top: 14px;
    }
    .action-inner{
      border-radius: 18px;
      border: 1px solid var(--line);
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow-mid);
      padding: 12px;
      display:flex;
      align-items:center;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }

    /* Tabla pro */
    .table-wrap{
      border: 1px solid var(--line);
      border-radius: var(--radius-xl);
      background: #fff;
      box-shadow: var(--shadow-soft);
      overflow: hidden;
      padding: 16px;
    }
    .dt-nowrap th, .dt-nowrap td{ white-space: nowrap; }
    table.dataTable thead th{
      font-weight: 1000 !important;
      color: var(--ink) !important;
    }

    /* Badges pro */
    .badge-pro{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: .42rem .62rem;
      border-radius: 999px;
      font-weight: 1000;
      letter-spacing: .2px;
      border: 1px solid rgba(15,23,42,.10);
    }
    .badge-brand{ background: rgba(32,66,127,.10); color: var(--brand2); }
    .badge-info{ background: rgba(13,110,253,.10); color: #0b5ed7; }
    .badge-warning{ background: rgba(255,193,7,.18); color: #7a5a00; }
    .badge-muted{ background: rgba(100,116,139,.14); color: #334155; }

    .metric-pill{
      display:inline-flex; align-items:center; gap:8px;
      padding: .34rem .58rem;
      border-radius: 999px;
      background: rgba(15,23,42,.04);
      border: 1px solid rgba(15,23,42,.08);
      font-weight: 900;
      color: #334155;
      font-size: 12px;
    }

    /* Modal brutal */
    .modal-pro .modal-content{
      border-radius: 22px;
      border: 1px solid rgba(15,23,42,.10);
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(2,6,23,.22);
    }
    .modal-pro .modal-header{
      background:
        radial-gradient(900px 420px at 20% 20%, rgba(46,88,168,.22), transparent 60%),
        linear-gradient(135deg, var(--brand), var(--brand2));
      color: #fff;
      border-bottom: 0;
      padding: 16px 18px;
    }
    .modal-pro .modal-title{
      font-weight: 1000;
      letter-spacing: .2px;
    }
    .modal-pro .btn-close{
      filter: invert(1);
      opacity: .9;
    }
    .modal-body{ background: #fff; }
    .modal-scroll{
      max-height: calc(100vh - 220px);
      overflow: auto;
    }
    .muted{ color: var(--muted); font-weight: 700; }

    /* Choices */
    .choices__inner{
      border-radius: 14px !important;
      border: 1px solid rgba(15,23,42,.12) !important;
      min-height: calc(3.5rem + 2px);
      padding-top: 10px !important;
    }
    .choices.is-focused .choices__inner{
      border-color: rgba(32,66,127,.45) !important;
      box-shadow: 0 0 0 .25rem rgba(32,66,127,.12) !important;
    }
  </style>
</head>

<body class="">
  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  <main class="main" id="top">
    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">
      <div class="container-fluid container-xxl-saas">

        <!-- HERO -->
        <div class="saas-hero">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
    
              <div>
               
                <h2 class="title mb-1"><?= h($modulo) ?></h2>
                <div class="sub">Configura el alcance del estudio (Nacional / Departamental / Municipal) y sus métricas.</div>
              </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="chip"><i class="fas fa-circle-info"></i> Campos con * obligatorios</span>
              <span class="chip"><i class="fas fa-shield-alt"></i> Módulo Admin</span>
            </div>
          </div>
        </div>

        <!-- FORM -->
        <div class="card card-pro mb-4">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list-check" style="color: var(--brand); font-size: 1.2rem;"></i>
                <h4 class="mb-0" style="font-weight: 1000; color: var(--ink);">
                  Ingreso y listado de <?= h($modulo) ?>
                </h4>
                <span id="spanEncuesta" class="ms-2 text-muted" style="font-weight:800;"></span>
              </div>
              <div class="text-muted" style="font-size:12px;font-weight:800;">
                Selecciona el tipo de estudio para habilitar bloques de ubicación.
              </div>
            </div>
          </div>

          <div class="card-body">
            <form class="row g-3" id="formespacioGeografico" role="form" autocomplete="off">
              <input type="hidden" name="op" id="op" />
              <input type="hidden" name="idEspacioGeografico" id="idEspacioGeografico" />

              <!-- Sección: Información -->
              <div class="col-12">
                <div class="section-card">
                  <div class="section-title"><span class="dot"></span> Información general</div>

                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <div class="form-floating">
                        <input type="text" class="form-control" id="observaciones" name="observaciones"
                               placeholder="Ingrese observaciones" value="">
                        <label for="observaciones">Observaciones <span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-12 col-md-6">
                      <div class="form-floating">
                        <select class="form-select" id="tipo_estudio" name="tipo_estudio" required>
                          <option value="" selected disabled>Seleccione el tipo de estudio</option>
                          <option value="Nacional">Nacional</option>
                          <option value="Departamental">Departamental</option>
                          <option value="Municipal">Municipal</option>
                        </select>
                        <label for="tipo_estudio">Tipo de estudio <span class="text-danger">*</span></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sección: Ubicación dinámica -->
              <div class="col-12">
                <div class="section-card">
                  <div class="section-title"><span class="dot"></span> Ubicación</div>

                  <div class="row g-3" id="dynamic-geo-container"></div>

                  <div class="text-end mt-2" id="add-geo-button-container" style="display:none;">
                    <button type="button" class="btn btn-soft btn-sm" id="add-departamento-btn">
                      <i class="fas fa-plus me-1"></i> Agregar más departamento
                    </button>
                  </div>

                  <small class="text-muted fw-semibold d-block mt-2">
                    Nacional y Departamental permiten seleccionar múltiples municipios por departamento.
                  </small>
                </div>
              </div>

              <!-- Sección: Métricas -->
              <div class="col-12">
                <div class="section-card">
                  <div class="section-title"><span class="dot"></span> Métricas</div>

                  <div class="row g-3">
                    <div class="col-12 col-md-3">
                      <div class="form-floating">
                        <input type="number" class="form-control" id="numero_votantes" name="numero_votantes"
                               placeholder="Número de votantes" value="" required>
                        <label for="numero_votantes">Número de votantes <span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-12 col-md-3">
                      <div class="form-floating">
                        <input type="number" class="form-control" id="cantidad_poblacion" name="cantidad_poblacion"
                               placeholder="Cantidad de población" value="" required>
                        <label for="cantidad_poblacion">Cantidad de población <span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <!-- Solo municipal -->
                    <div class="col-12">
                      <div class="row g-3" id="municipal-fields" style="display:none;">
                        <div class="col-12 col-md-3">
                          <div class="form-floating">
                            <input type="number" class="form-control" id="numero_comunas" name="numero_comunas" placeholder="Comunas">
                            <label for="numero_comunas">Número de comunas</label>
                          </div>
                        </div>
                        <div class="col-12 col-md-3">
                          <div class="form-floating">
                            <input type="number" class="form-control" id="numero_zonas" name="numero_zonas" placeholder="Zonas">
                            <label for="numero_zonas">Número de zonas</label>
                          </div>
                        </div>
                        <div class="col-12 col-md-3">
                          <div class="form-floating">
                            <input type="number" class="form-control" id="numero_veredas" name="numero_veredas" placeholder="Veredas">
                            <label for="numero_veredas">Número de veredas</label>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <!-- Action bar -->
              <div class="col-12">
                <div class="action-bar">
                  <div class="action-inner">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <span class="chip"><i class="fas fa-circle-check"></i> Listo para guardar</span>
                      <span class="text-muted" style="font-size:12px;font-weight:800;">
                        Tip: “Nacional” permite varios departamentos.
                      </span>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                      <button type="button" onclick="ESPACIOGEOGRAFICO.reload();" class="btn btn-soft px-4">
                        <i class="fas fa-xmark me-2"></i>Cancelar
                      </button>

                      <?php if ($create && $edit): ?>
                        <button type="button" onclick="ESPACIOGEOGRAFICO.validateData();" class="btn btn-brand px-4">
                          <i class="fas fa-floppy-disk me-2"></i>Guardar
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>

        <!-- TABLA UPGRADE -->
        <div class="table-wrap">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
              <h4 class="title mb-1">Listado de Espacios Geográficos</h4>
              <div class="sub">Ahora incluye chips + botón “Ver Geografías” con modal pro.</div>
            </div>
            <div class="chip"><i class="fas fa-table"></i> DataTable</div>
          </div>

          <div class="table-responsive">
            <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 align-middle dt-nowrap">
              <thead>
                <tr class="border-1">
                  <th>Acciones</th>
                  <th>Observaciones</th>
                  <th>Tipo</th>
                  <th>Comunas</th>
                  <th>Zonas</th>
                  <th>Veredas</th>
                  <th>Población</th>
                  <th>Votantes</th>
                  <th>Creación</th>
                </tr>
              </thead>
              <tbody class="list">
                <?php if ($isvalidEspacio && count($arr) > 0): ?>
                  <?php foreach ($arr as $item): ?>
                    <?php
                      $id = (int)($item['id'] ?? 0);
                      $tb = tipoBadge($item['tipo_estudio'] ?? '');
                    ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <?php if ($edit): ?>
                            <button type="button" class="btn btn-mini btn-primary" title="Editar"
                              onclick="ESPACIOGEOGRAFICO.editData(<?= $id ?>)">
                              <i class="uil uil-edit"></i>
                            </button>
                          <?php endif; ?>

                          <button type="button" class="btn btn-mini btn-soft" title="Ver geografías"
                            onclick="ESPACIOGEOGRAFICO.verGeografias(<?= $id ?>)">
                            <i class="fas fa-map-location-dot me-1"></i> Ver
                          </button>

                          <?php if ($create): ?>
                            <button type="button" class="btn btn-mini"
                              style="background:linear-gradient(135deg,#1e7e34,#155724);color:#fff;border:0;"
                              title="Duplicar como nuevo registro"
                              onclick="ESPACIOGEOGRAFICO.duplicar(<?= $id ?>)">
                              <i class="fas fa-copy"></i>
                            </button>
                          <?php endif; ?>
                        </div>
                      </td>

                      <td><?= h($item['observaciones'] ?? '') ?></td>

                      <td>
                        <span class="badge-pro <?= h($tb['bg']) ?>">
                          <i class="fas <?= h($tb['icon']) ?>"></i>
                          <?= h($tb['txt']) ?>
                        </span>
                      </td>

                      <td><span class="metric-pill"><i class="fas fa-building"></i> <?= h(($item['numero_comunas'] ?? '') === '' ? '—' : number_format((float)$item['numero_comunas'], 0, ',', '.')) ?></span></td>
                      <td><span class="metric-pill"><i class="fas fa-draw-polygon"></i> <?= h(($item['numero_zonas'] ?? '') === '' ? '—' : number_format((float)$item['numero_zonas'], 0, ',', '.')) ?></span></td>
                      <td><span class="metric-pill"><i class="fas fa-tree"></i> <?= h(($item['numero_veredas'] ?? '') === '' ? '—' : number_format((float)$item['numero_veredas'], 0, ',', '.')) ?></span></td>

                      <td><?= h(($item['cantidad_poblacion'] ?? '') === '' ? '' : number_format((float)$item['cantidad_poblacion'], 0, ',', '.')) ?></td>
                      <td><?= h(($item['numero_votantes'] ?? '') === '' ? '' : number_format((float)$item['numero_votantes'], 0, ',', '.')) ?></td>
                      <td><?= h($item['dtcreate'] ?? '') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="9" class="text-center py-4 text-muted">No hay registros.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <?php include './admin/include/footer.php'; ?>
      </div>
    </div>
  </main>

  <!-- MODAL UPGRADE -->
  <div class="modal fade modal-pro" id="modalGeografias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <div class="modal-title h5 mb-0">
              <i class="fas fa-map-marked-alt me-2"></i> Geografías del Estudio
            </div>
            <div class="opacity-75" style="font-weight:800; font-size: 12px;">
              Detalle por departamento y municipios seleccionados
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div id="modalGeografiasBody" class="modal-scroll">
            <div class="text-center py-5">
              <i class="fas fa-spinner fa-spin fa-3x" style="color: var(--brand);"></i>
              <p class="mt-3 muted">Cargando información...</p>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid rgba(15,23,42,.08);">
          <button type="button" class="btn btn-soft" data-bs-dismiss="modal">
            <i class="fas fa-xmark me-2"></i>Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'admin/include/gerenic_script.php'; ?>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/jquery.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <?php include './admin/include/generic_dataTables.php'; ?>

  <script src="admin/js/lib/choices.min.js"></script>

  <script>
    const DEPARTAMENTO_OPTIONS_HTML = <?= json_encode($optionDep) ?>;

    $(function(){
      if ($.fn.DataTable && $("#dynamictable").length) {
        if (!$.fn.DataTable.isDataTable("#dynamictable")) {
          $("#dynamictable").DataTable({
            pageLength: 25,
            order: [[8, "desc"]],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" }
          });
        }
      }
    });
  </script>

  <script src="admin/js/espacio_geografico.js"></script>
  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
