<?php
include 'admin/include/head.php';
require './admin/include/generic_classes.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Validar permisos
$view    = SessionData::getPermission(22);
$create  = SessionData::getPermission(23);
$edit    = SessionData::getPermission(24);
$permits = SessionData::getPermission(25);

if (!$view) { require 'permiso_denegado.php'; exit; }

include './admin/classes/Departamento.php';
include './admin/classes/PartidoPolitico.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Participantes.php';

$arr = Participantes::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];

// Informacion de departamentos
$departamentos = Departamento::getAll(null);
$departamentosResponse = $departamentos['output']['response'] ?? [];
$optionDep = "";
foreach ($departamentosResponse as $dep) {
  $optionDep .= "<option value='" . htmlspecialchars($dep['codigo_departamento'], ENT_QUOTES, 'UTF-8') . "'>" .
                htmlspecialchars($dep['codigo_departamento'], ENT_QUOTES, 'UTF-8') . " - " .
                htmlspecialchars($dep['departamento'], ENT_QUOTES, 'UTF-8') . "</option>";
}

// Informacion de los cargos publicos
$arrCargosPub = CargosPublicos::getAll(null);
$arrCargosPub = $arrCargosPub['output']['response'] ?? [];
$optionCargosPub = "";
foreach ($arrCargosPub as $val) {
  $optionCargosPub .= "<option value='" . htmlspecialchars($val['id'], ENT_QUOTES, 'UTF-8') . "'>" .
                      htmlspecialchars($val['rama'], ENT_QUOTES, 'UTF-8') . " - " .
                      htmlspecialchars($val['nombre'], ENT_QUOTES, 'UTF-8') . "</option>";
}

$modulo = 'Participantes Políticos';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<style>
  :root{
    --brand:#20427F;
    --brand2:#132b52;
    --brand3:#2e58a8;
    --ink:#0f172a;
    --muted:#64748b;
    --bg:#f6f8fb;
    --card:#ffffff;
    --line: rgba(15, 23, 42, .08);
    --radius:18px;
    --shadow: 0 14px 40px rgba(2, 6, 23, .08);
  }
  body{ background: var(--bg); }
  .content{ padding-top: 14px !important; padding-bottom: 40px !important; }
  @media (min-width: 1400px){ .container-xxl-saas{ max-width: 1500px; } }

  .saas-hero{
    background: radial-gradient(1200px 600px at 15% 15%, rgba(46,88,168,.18), transparent 55%),
                radial-gradient(900px 500px at 80% 30%, rgba(32,66,127,.16), transparent 55%),
                linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.80));
    border: 1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 18px;
    position: relative;
    overflow: hidden;
    margin-bottom: 16px;
  }
  .saas-hero:before{
    content:"";
    position:absolute; inset:-2px;
    background: linear-gradient(135deg, rgba(32,66,127,.18), rgba(19,43,82,.06), rgba(46,88,168,.16));
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
    font-weight: 800;
    font-size: 12px;
  }

  .card-pro{
    border: 1px solid var(--line) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    background: var(--card) !important;
    overflow: hidden;
  }
  .card-pro .card-header{
    background: rgba(255,255,255,.88) !important;
    border-bottom: 1px solid var(--line) !important;
    padding: 16px 18px !important;
  }
  .card-pro .card-body{ padding: 18px; }

  .title{ font-weight: 900; color: var(--ink); margin: 0; }
  .sub{ color: var(--muted); font-weight: 600; margin-top: 4px; }

  .form-floating>.form-control, .form-floating>.form-select{
    border-radius: 14px;
    border: 1px solid rgba(15,23,42,.12);
    box-shadow: none;
  }
  .form-control:focus,.form-select:focus{
    border-color: rgba(32,66,127,.45) !important;
    box-shadow: 0 0 0 .25rem rgba(32,66,127,.12) !important;
  }

  .section-card{
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 16px;
    background: rgba(255,255,255,.90);
    padding: 14px;
  }
  .section-title{
    font-weight: 900; color: var(--ink);
    margin: 0 0 10px 0;
    display:flex; align-items:center; gap:10px;
  }
  .section-title .dot{
    width: 12px; height: 12px; border-radius: 99px;
    background: linear-gradient(135deg, var(--brand3), var(--brand2));
    box-shadow: 0 10px 22px rgba(32,66,127,.18);
  }

  .btn-brand{
    background: linear-gradient(135deg, var(--brand), var(--brand2));
    border: 0 !important;
    border-radius: 14px;
    font-weight: 900;
    padding: 12px 18px;
    box-shadow: 0 14px 30px rgba(32,66,127,.18);
    transition: transform .16s ease, box-shadow .16s ease;
  }
  .btn-brand:hover{ transform: translateY(-2px); box-shadow: 0 18px 44px rgba(32,66,127,.25); }

  .btn-soft{
    background: rgba(32,66,127,.08);
    border: 1px solid rgba(32,66,127,.18) !important;
    color: var(--brand2);
    border-radius: 14px;
    font-weight: 900;
    padding: 12px 16px;
  }

  .action-bar{
    position: sticky;
    bottom: 12px;
    z-index: 10;
    margin-top: 16px;
  }
  .action-inner{
    border-radius: 18px;
    border: 1px solid var(--line);
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow);
    padding: 12px;
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
  }

  .table-wrap{
    border: 1px solid var(--line);
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--shadow);
    overflow: hidden;
    padding: 16px;
  }
  table.dataTable thead th{
    font-weight: 900 !important;
    color: var(--ink) !important;
    white-space: nowrap;
  }

  .avatar-mini{
    width:56px; height:56px;
    border-radius: 16px;
    object-fit: cover;
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: 0 10px 22px rgba(2, 6, 23, .08);
    background:#fff;
  }

  /* Select2 SaaS */
  .select2-container--bootstrap4 .select2-selection--multiple{
    min-height: calc(3.5rem + 2px);
    padding: .65rem .9rem;
    border-radius: 14px;
    border: 1px solid rgba(15,23,42,.12);
    display:flex; align-items:center; flex-wrap:wrap;
    box-shadow:none;
  }
  .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple{
    border-color: rgba(32,66,127,.45) !important;
    box-shadow: 0 0 0 .25rem rgba(32,66,127,.12) !important;
  }
  .select2-container--bootstrap4 .select2-selection__choice{
    background: rgba(32,66,127,.10) !important;
    border: 1px solid rgba(32,66,127,.18) !important;
    border-radius: 999px !important;
    padding: 4px 10px !important;
    font-weight: 800 !important;
    color: var(--brand2) !important;
    margin-top: .2rem !important;
    margin-bottom: .2rem !important;
  }
  .select2-container--bootstrap4 .select2-search__field{
    min-height: 28px;
    margin-top: 0;
    margin-bottom: 0;
  }

  /* ✅ Botón eliminar pro */
  .btn-danger-pro{
    background: linear-gradient(135deg, #dc3545, #9b1c28);
    border: 0 !important;
    border-radius: 12px;
    font-weight: 900;
    box-shadow: 0 10px 20px rgba(220,53,69,.18);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-danger-pro:hover{
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(220,53,69,.24);
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

      <!-- HERO -->
      <div class="saas-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3 flex-wrap">
          
            <div>
            
              <h2 class="title mb-1">Participantes Políticos</h2>
              <div class="sub">Registra candidatos, asigna partido(s), cargo público, puntos y estado.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- FORM CARD -->
      <div class="card card-pro mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-user-tie" style="color: var(--brand); font-size: 1.2rem;"></i>
              <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">
                Listado e Ingreso de <?= h($modulo) ?>
              </h4>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:700;">
              Campos con * son obligatorios.
            </div>
          </div>
        </div>

        <div class="card-body">
          <form id="formparticipantes" class="row g-3" role="form" autocomplete="off">
            <input type="hidden" name="op" id="op" />
            <input type="hidden" name="idParticipante" id="idParticipante" />

            <!-- Sección: Postulación -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Postulación</div>

                <div class="row g-3">
                  <div class="col-12">
                    <label for="partidoPoliticoId" class="form-label fw-bold">
                      Partido Político <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="partidoPoliticoId" name="partidoPoliticoId[]" multiple="multiple" required>
                      <?php
                        $arrPartidos = PartidoPolitico::getAll(null);
                        $arrPartidos = $arrPartidos['output']['response'] ?? [];
                        foreach ($arrPartidos as $val) {
                          echo "<option value='" . h($val['id']) . "'>" . h($val['nombre_partido']) . "</option>";
                        }
                      ?>
                    </select>
                    <small class="text-muted fw-semibold d-block mt-1">Puedes seleccionar uno o varios partidos.</small>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="cargoPublicoId" name="cargoPublicoId"
                        onchange="PARTICIPANTES.handleCargoPublicoChange();">
                        <?= $optionCargosPub ?>
                      </select>
                      <label for="cargoPublicoId">Cargo público <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <!-- Modalidad -->
                  <div class="col-12 col-md-4 col-lg-2" id="div-modalidad" style="display:none;">
                    <div class="form-floating">
                      <select class="form-select" id="modalidad" name="modalidad">
                        <option value="Lista Cerrada">Lista Cerrada</option>
                        <option value="Preferente">Preferente</option>
                        <option value="Voto Por Candidato">Voto Por Candidato</option>
                      </select>
                      <label for="modalidad">Modalidad</label>
                    </div>
                  </div>

                  <!-- Número tarjetón -->
                  <div class="col-12 col-md-4 col-lg-2" id="div-numero-candidato" style="display:none;">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="numero_candidato" name="numero_candidato">
                        <option value="seleccione">Seleccione</option>
                        <?php for($i=1; $i<=1000; $i++): ?>
                          <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                      </select>
                      <label for="numero_candidato">N. Tarjetón</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="puntos" name="puntos"
                        placeholder="Puntos" onKeyPress="return soloNumeros(event);">
                      <label for="puntos">Puntos (+)</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-floating">
                      <select class="form-select" id="favorito" name="favorito">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                      </select>
                      <label for="favorito">Favorito <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" id="habilitado" name="habilitado" value="si" checked>
                      <label class="form-check-label fw-bold" for="habilitado">Participante habilitado</label>
                    </div>
                    <small class="text-muted fw-semibold">Desmarca para deshabilitar</small>
                  </div>

                  <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-floating">
                      <select class="form-select" id="cliente" name="cliente">
                        <option value="no">No</option>
                        <option value="si">Sí</option>
                      </select>
                      <label for="cliente">Es Cliente <span class="text-danger">*</span></label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Sección: Ubicación -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Ubicación</div>

                <div class="row g-3">
                  <div class="col-12 col-md-4 departamento-municipio-fields d-none">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id">
                        <?= $optionDep ?>
                      </select>
                      <label for="tbl_departamento_id">Departamento <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4 departamento-municipio-fields d-none">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"></select>
                      <label for="tbl_municipio_id">Municipio <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-8">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                        placeholder="Nombre completo">
                      <label for="nombre_completo">Nombre completo del candidato <span class="text-danger">*</span></label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Foto -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Foto</div>

                <div class="dropzone dropzone-multiple p-0" id="dropzone-foto1" data-dropzone="data-dropzone"
                  style="min-height: 100px; padding: 10px;">
                  <iframe id="ifm1" name="ifm1" src="upload.php" width="100%" height="230" scrolling="no"
                    frameborder="0" style="border:none;"></iframe>
                </div>

                <small class="text-muted fw-semibold d-block mt-2">
                  Sube una imagen clara (JPG/PNG). Se mostrará en el listado.
                </small>
              </div>
            </div>

            <!-- Action bar -->
            <div class="col-12">
              <div class="action-bar">
                <div class="action-inner">
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="chip"><i class="fas fa-circle-info"></i> Listo para guardar</span>
                    <span class="text-muted" style="font-size:12px;font-weight:700;">
                      Tip: marca “Favorito” para destacar en reportes.
                    </span>
                  </div>

                  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <button type="button" onclick="PARTICIPANTES.emptyCells();" class="btn btn-soft px-4">
                      <i class="fas fa-xmark me-2"></i>Cancelar
                    </button>
                      <?php if ($create && $edit): ?>
                        <button class="btn btn-brand px-4" type="button" onclick="PARTICIPANTES.validateData();">
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

      <!-- TABLA -->
      <div class="table-wrap">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <h4 class="title mb-1">Listado de Participantes</h4>
            <div class="sub">Edita o elimina registros desde la tabla.</div>
          </div>
          <div class="chip"><i class="fas fa-table"></i> DataTable</div>
        </div>

        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 align-middle">
            <thead>
              <tr class="border-1">
                <th>Editar</th>
                <th>Eliminar</th>
                <th>Postulación</th>
                <th>Nombre</th>
                <th>Cliente</th>
                <th>Favorito</th>
                <th>Puntos</th>
                <th>Foto</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if ($isvalid && count($arr) > 0): ?>
                <?php foreach ($arr as $item): ?>
                  <?php $id = (int)($item['id'] ?? 0); ?>
                  <?php $img = "admin/get_foto_participante.php?id=" . $id; ?>

                  <tr>
                    <td>
                      <?php if ($edit): ?>
                        <button type="button" class="btn btn-sm btn-primary" title="Editar"
                          onclick="PARTICIPANTES.editData(<?= $id ?>)">
                          <i class="uil uil-edit"></i>
                        </button>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if ($permits): ?>
                        <button type="button" class="btn btn-sm btn-danger-pro" title="Eliminar"
                          onclick="PARTICIPANTES.deleteData(<?= $id ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
                      <?php else: ?>
                        <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>

                    <td><?= h($item['cargo_publico'] ?? '') ?></td>
                    <td><?= h($item['nombre_completo'] ?? '') ?></td>

                    <td>
                      <?php if (($item['cliente'] ?? '') === 'si'): ?>
                        <span class="badge bg-primary-subtle text-primary fw-bold">Sí</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">No</span>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if (($item['favorito'] ?? '') === 'si'): ?>
                        <span class="badge bg-warning-subtle text-warning fw-bold"><i class="fas fa-star me-1"></i>Sí</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">No</span>
                      <?php endif; ?>
                    </td>

                    <td><?= h($item['puntos'] ?? '') ?></td>

                    <td>
                      <img class="avatar-mini"
                        src="<?= h($img) ?>"
                        alt="Foto <?= h($item['nombre_completo'] ?? '') ?>"
                        onerror="this.src='assets/img/generic/default.png'">
                    </td>

                    <td><?= h($item['nombre_departamento'] ?? '') ?></td>
                    <td><?= h($item['nombre_municipio'] ?? '') ?></td>

                    <td>
                      <?php if (($item['habilitado'] ?? '') === 'si'): ?>
                        <span class="badge bg-success-subtle text-success fw-bold">
                          <i class="fas fa-check-circle me-1"></i>Activo
                        </span>
                      <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger fw-bold">
                          <i class="fas fa-times-circle me-1"></i>Inactivo
                        </span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="11" class="text-center py-4 text-muted">No se encontraron registros.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </div>

 <?php include 'admin/include/gerenic_script.php'; ?>

<!-- Select2 (CSS arriba en el <head> ideal, pero lo dejamos aquí si quieres) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Core (si gerenic_script ya los trae, puedes quitar duplicados) -->
<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>
<script src="assets/js/plugins/prism.js"></script>

<!-- ✅ IMPORTANTE: primero DEPARTAMENTO, luego PARTICIPANTES -->
<script src="admin/js/departamento.js"></script>
<script src="admin/js/participantes.js"></script>

<script>
  $(document).ready(function () {

    // Select2
    const $sel = $("#partidoPoliticoId");
    if ($sel.length && typeof $sel.select2 === "function") {
      $sel.select2({
        theme: "bootstrap4",
        width: "100%",
        placeholder: "Selecciona uno o varios partidos",
        allowClear: true
      });
    }

    // Set depto config (si existe el input)
    const departamento = $("#departamentoConfiguracionInput").val() || "";
    if (departamento) {
      $("#tbl_departamento_id").val(departamento).trigger("change");
    }

    // Cargar municipios SOLO si existe DEPARTAMENTO
    if (window.DEPARTAMENTO && typeof DEPARTAMENTO.getMunicipios === "function") {
      DEPARTAMENTO.getMunicipios();
    } else {
      console.log("⚠️ DEPARTAMENTO no está definido o no tiene getMunicipios()");
    }

    // Ajuste UI por cargo SOLO si existe PARTICIPANTES
    if (window.PARTICIPANTES && typeof PARTICIPANTES.handleCargoPublicoChange === "function") {
      PARTICIPANTES.handleCargoPublicoChange();
    } else {
      console.log("⚠️ PARTICIPANTES no está definido o no tiene handleCargoPublicoChange()");
    }

  });
</script>

<?php include './admin/include/generic_dataTables.php'; ?>
<?php include 'admin/include/scriptsgober360.php'; ?>

</body>
</html>
