<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/PartidoPolitico.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Permisos
$view    = SessionData::getPermission(10);
$create  = SessionData::getPermission(11);
$edit    = SessionData::getPermission(12);
$permits = SessionData::getPermission(13);

if (!$view) {
  require 'permiso_denegado.php';
  exit;
}

// Información de PartidoPolitico
$arr = PartidoPolitico::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];
$modulo = 'Partidos políticos';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<body class="">
  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

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
    }
    table.dataTable thead th{
      font-weight: 900 !important;
      color: var(--ink) !important;
      white-space: nowrap;
    }

    .logo-mini{
      width: 64px; height: 64px;
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.10);
      object-fit: cover;
      background: #fff;
      box-shadow: 0 10px 22px rgba(2, 6, 23, .08);
    }

    .btn-delete{
      border-radius: 12px;
      font-weight: 900;
      box-shadow: 0 12px 24px rgba(220,53,69,.18);
      transition: transform .15s ease, box-shadow .15s ease;
    }
    .btn-delete:hover{
      transform: translateY(-2px);
      box-shadow: 0 18px 40px rgba(220,53,69,.22);
    }
  </style>

  <div class="content">
    <div class="container-fluid container-xxl-saas">

      <!-- HERO -->
      <div class="saas-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
              <h2 class="title mb-1">Partidos Políticos</h2>
              <div class="sub">Crea, actualiza y administra los partidos con logo y datos oficiales.</div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="chip"><i class="fas fa-shield-alt"></i> Acceso controlado</span>
            <span class="chip"><i class="fas fa-table"></i> DataTable</span>
          </div>
        </div>
      </div>

      <!-- FORM -->
      <div class="card card-pro mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-flag" style="color: var(--brand); font-size: 1.2rem;"></i>
              <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">Ingreso de Partidos</h4>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:700;">Campos con * son obligatorios.</div>
          </div>
        </div>

        <div class="card-body">
          <form class="row g-3" id="formpartidospoliticos" role="form" autocomplete="off">
            <input type="hidden" name="op" id="op" />
            <input type="hidden" name="idPartido" id="idPartido" />

            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Información del Partido</div>

                <div class="row g-3">
                  <div class="col-12">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="nombre_partido" name="nombre_partido"
                        placeholder="Ingrese el nombre del partido" required>
                      <label for="nombre_partido">Nombre del Partido <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="representante_legal" name="representante_legal"
                        placeholder="Ingrese el nombre del representante legal" required>
                      <label for="representante_legal">Representante Legal <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="resolucion" name="resolucion"
                        placeholder="Ingrese la resolución de reconocimiento">
                      <label for="resolucion">Resolución de Reconocimiento</label>
                    </div>
                  </div>

                  <!-- Upload -->
                  <div class="col-12 col-lg-6">
                    <div class="section-card">
                      <div class="section-title"><span class="dot"></span> Logo del Partido</div>
                      <div class="text-muted" style="font-size:12px;font-weight:700; margin-bottom:10px;">
                        Sube un logo en JPG/PNG para mostrarlo en el sistema.
                      </div>

                      <div class="dropzone dropzone-multiple p-0" id="my-awesome-dropzone" data-dropzone="data-dropzone"
                        style="min-height: 100px; padding: 10px;">
                        <iframe id="ifm" name="ifm" src="upload.php" width="100%" height="220" scrolling="no" frameborder="0"
                          style="border:none;"></iframe>
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
                    <span class="chip"><i class="fas fa-circle-info"></i> Listo para guardar</span>
                    <span class="text-muted" style="font-size:12px;font-weight:700;">Tip: usa “Editar” para actualizar un partido.</span>
                  </div>

                  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <button type="button" onclick="UTIL.clearForm('formpartidospoliticos');" class="btn btn-soft px-4">
                      <i class="fas fa-xmark me-2"></i>Cancelar
                    </button>

                    <?php if ($create && $edit): ?>
                      <button class="btn btn-brand px-4" type="button" onclick="PARTIDOS_POLITICOS.validateData();">
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
      <div class="table-wrap p-3 p-lg-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <h4 class="title mb-1">Listado de Partidos</h4>
            <div class="sub">Administra los partidos registrados en el sistema.</div>
          </div>
          <div class="chip"><i class="fas fa-table"></i> DataTable</div>
        </div>

        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 align-middle">
            <thead>
              <tr class="border-1">
                <th>Acciones</th>
                <th>Partido</th>
                <th>Representante</th>
                <th>Resolución</th>
                <th>Email</th>
                <th>Logo</th>
              </tr>
            </thead>

            <!-- ✅ Cambié "list" por "dt-list" para evitar choques con list.min.js -->
            <tbody class="dt-list">
              <?php if ($isvalid && count($arr) > 0): ?>
                <?php foreach ($arr as $item): ?>
                  <?php
                    $logoFile = $item['logo'] ?? '';
                    $img = !empty($logoFile) ? "assets/img/admin/" . h($logoFile) : 'assets/img/generic/default.png';
                    $nombre = $item['nombre_partido'] ?? '';
                  ?>
                  <tr>
                    <td>
                      <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-primary" title="Editar"
                          onclick="PARTIDOS_POLITICOS.editData(<?= (int)$item['id'] ?>)">
                          <i class="uil uil-edit"></i>
                        </button>

                        <?php if ($permits): ?>
                          <button type="button"
                            class="btn btn-sm btn-danger btn-delete btn-delete-partido"
                            title="Eliminar"
                            data-id="<?= (int)$item['id'] ?>"
                            data-nombre="<?= h($nombre) ?>">
                            <i class="fas fa-trash"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>

                    <td><?= h($item['nombre_partido'] ?? '') ?></td>
                    <td><?= h($item['representante_legal'] ?? '') ?></td>
                    <td><?= h($item['resolucion'] ?? '') ?></td>
                    <td><?= h($item['email_contacto'] ?? '') ?></td>
                    <td>
                      <img class="logo-mini" src="<?= $img ?>" alt="Logo" />
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">No se encontraron registros.</td>
                </tr>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </div>

  <!-- ✅ 1) Script base del sistema (jQuery, util, etc.) -->
  <?php include 'admin/include/gerenic_script.php'; ?>

  <!-- ✅ 2) DataTables antes (si tu include lo requiere) -->
  <?php include './admin/include/generic_dataTables.php'; ?>

  <!-- ✅ 3) Tu JS al final (así ya existe jQuery y util) -->
  <script src="admin/js/partidos_politicos.js"></script>

  <?php include 'admin/include/scriptsgober360.php'; ?>

  <script>
    // ✅ Confirmación pro para eliminar (SweetAlert2 si existe)
    $(document).on("click", ".btn-delete-partido", function(){
      const id = $(this).data("id");
      const nombre = $(this).data("nombre") || "Partido";

      if (!window.PARTIDOS_POLITICOS || typeof window.PARTIDOS_POLITICOS.deleteData !== "function") {
        if (typeof Swal !== "undefined") {
          Swal.fire({ icon:"info", title:"deleteData() no disponible", text:"No existe PARTIDOS_POLITICOS.deleteData(id). Revisa que partidos_politicos.js esté cargando bien." });
        } else {
          alert("No existe PARTIDOS_POLITICOS.deleteData(id). Revisa que partidos_politicos.js esté cargando bien.");
        }
        return;
      }

      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "¿Eliminar partido?",
          html: `<div style="text-align:left">
                  <div style="font-weight:900; margin-bottom:6px;">${nombre}</div>
                  <div style="color:#b91c1c; font-weight:800;">Esta acción no se puede deshacer.</div>
                 </div>`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar",
          reverseButtons: true,
          focusCancel: true
        }).then((r) => { if (r.isConfirmed) window.PARTIDOS_POLITICOS.deleteData(id); });
      } else {
        if (confirm("¿Está seguro que desea eliminar este partido?\n\n" + nombre)) {
          window.PARTIDOS_POLITICOS.deleteData(id);
        }
      }
    });
  </script>

</body>
</html>
