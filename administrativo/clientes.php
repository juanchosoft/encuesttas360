<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Cliente.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Permisos
$view    = SessionData::getPermission(86);
$create  = SessionData::getPermission(87);
$edit    = SessionData::getPermission(88);
$permits = SessionData::getPermission(89);

if (!$view) { require 'permiso_denegado.php'; exit; }

// Información de Clientes
$arr = Cliente::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];
$modulo = 'Clientes';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<body class="">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

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
      --shadow2: 0 20px 60px rgba(2, 6, 23, .12);
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

    .action-bar{ position: sticky; bottom: 12px; z-index: 10; margin-top: 16px; }
    .action-inner{
      border-radius: 18px;
      border: 1px solid var(--line);
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      box-shadow: var(--shadow);
      padding: 12px;
      display:flex; align-items:center; justify-content: space-between;
      gap: 10px; flex-wrap: wrap;
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

    .btn-delete-client{
      border-radius: 12px;
      font-weight: 900;
      box-shadow: 0 12px 24px rgba(220,53,69,.18);
      transition: transform .15s ease, box-shadow .15s ease;
    }
    .btn-delete-client:hover{
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
            <?php if (!empty($logo)): ?>
              <img src="<?= h($logo) ?>" alt="Logo" style="height:52px;" class="img-fluid img-thumbnail">
            <?php endif; ?>
            <div>
              <div class="chip mb-2"><i class="fas fa-users"></i> CRM</div>
              <h2 class="title mb-1">Gestión de Clientes</h2>
              <div class="sub">Registra, edita y organiza clientes con un flujo limpio y rápido.</div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="chip"><i class="fas fa-database"></i> Base de clientes</span>
            <span class="chip"><i class="fas fa-shield-alt"></i> Acceso controlado</span>
          </div>
        </div>
      </div>

      <!-- FORM -->
      <div class="card card-pro mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-users" style="color: var(--brand); font-size: 1.2rem;"></i>
              <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">Formulario de Clientes</h4>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:700;">Campos con * son obligatorios.</div>
          </div>
        </div>

        <div class="card-body">
          <form class="row g-3" id="formclientes" role="form" autocomplete="off">
            <input type="hidden" name="op" id="op" />
            <input type="hidden" name="idCliente" id="idCliente" />

            <!-- Identificación -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Información de Identificación</div>
                <div class="row g-3">
                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select" id="identificacion_tipo" name="identificacion_tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="NIT">NIT</option>
                        <option value="CE">Cédula de Extranjería</option>
                        <option value="PASAPORTE">Pasaporte</option>
                        <option value="TI">Tarjeta de Identidad</option>
                      </select>
                      <label for="identificacion_tipo">Tipo de Identificación <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="identificacion_num" name="identificacion_num" placeholder="Número" required>
                      <label for="identificacion_num">Número de Identificación <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="dv" name="dv" placeholder="DV">
                      <label for="dv">Dígito de Verificación</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Personal -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Información Personal</div>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Nombre completo o razón social" required>
                      <label for="nombre_completo">Nombre Completo / Razón Social <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <select class="form-select" id="tipo_cliente" name="tipo_cliente">
                        <option value="">Seleccione...</option>
                        <option value="Natural">Persona Natural</option>
                        <option value="Juridica">Persona Jurídica</option>
                        <option value="Empresa">Empresa</option>
                        <option value="Gobierno">Entidad Gubernamental</option>
                      </select>
                      <label for="tipo_cliente">Tipo de Cliente</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="date" class="form-control" id="cumpleanos" name="cumpleanos">
                      <label for="cumpleanos">Fecha de Cumpleaños</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <select class="form-select" id="habilitado" name="habilitado" required>
                        <option value="SI">Activo</option>
                        <option value="NO">Inactivo</option>
                      </select>
                      <label for="habilitado">Estado <span class="text-danger">*</span></label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contacto -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Información de Contacto</div>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" required>
                      <label for="direccion">Dirección <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Ciudad o municipio">
                      <label for="ubicacion">Ciudad / Municipio</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Barrio o sector">
                      <label for="barrio">Barrio / Sector</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono fijo">
                      <label for="telefono">Teléfono Fijo</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular">
                      <label for="celular">Celular</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                      <label for="email">Correo Electrónico</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contacto adicional -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Contacto Adicional</div>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="contacto" name="contacto" placeholder="Nombre del contacto">
                      <label for="contacto">Nombre del Contacto</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-6">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="tel_contacto" name="tel_contacto" placeholder="Teléfono del contacto">
                      <label for="tel_contacto">Teléfono del Contacto</label>
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
                    <span class="text-muted" style="font-size:12px;font-weight:700;">Tip: usa “Editar” para actualizar un cliente.</span>
                  </div>

                  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <button type="button" onclick="UTIL.clearForm('formclientes');" class="btn btn-soft px-4">
                      <i class="fas fa-xmark me-2"></i>Cancelar
                    </button>

                    <?php if ($create && $edit): ?>
                      <button class="btn btn-brand px-4" type="button" onclick="CLIENTES.validateData();">
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
            <h4 class="title mb-1">Listado de Clientes</h4>
            <div class="sub">Consulta y edita rápidamente desde la tabla.</div>
          </div>
          <div class="chip"><i class="fas fa-table"></i> DataTable</div>
        </div>

        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 align-middle">
            <thead>
              <tr class="border-1">
                <th>Acciones</th>
                <th>Tipo ID</th>
                <th>Número ID</th>
                <th>Nombre Completo</th>
                <th>Teléfono</th>
                <th>Celular</th>
                <th>Email</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if ($isvalid && count($arr) > 0): ?>
                <?php foreach ($arr as $item): ?>
                  <tr>
                    <td>
                      <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-primary" title="Editar"
                          onclick="CLIENTES.editData(<?= (int)$item['id'] ?>)">
                          <i class="uil uil-edit"></i>
                        </button>

                        <?php if ($permits): ?>
                          <button type="button"
                            class="btn btn-sm btn-danger btn-delete-client"
                            title="Eliminar"
                            data-id="<?= (int)$item['id'] ?>"
                            data-nombre="<?= h($item['nombre_completo'] ?? '') ?>"
                            data-doc="<?= h(($item['identificacion_tipo'] ?? '') . ' ' . ($item['identificacion_num'] ?? '')) ?>">
                            <i class="fas fa-trash"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>

                    <td><?= h($item['identificacion_tipo'] ?? '') ?></td>
                    <td><?= h($item['identificacion_num'] ?? '') ?></td>
                    <td><?= h($item['nombre_completo'] ?? '') ?></td>
                    <td><?= h($item['telefono'] ?? '') ?></td>
                    <td><?= h($item['celular'] ?? '') ?></td>
                    <td><?= h($item['email'] ?? '') ?></td>
                    <td>
                      <?php if (($item['habilitado'] ?? '') === 'SI'): ?>
                        <span class="badge bg-success-subtle text-success fw-bold">Activo</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">Inactivo</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">No se encontraron registros.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </div>

  <!-- ✅ Scripts: ORDEN CORRECTO (para evitar "CLIENTES is not defined") -->
  <?php include 'admin/include/gerenic_script.php'; ?>

  <!-- Core (si gerenic_script ya incluye vendor-all, puedes quitar estos 3) -->
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <!-- ✅ Tu JS GLOBAL (el que te pasé) -->
  <script type="text/javascript" src="admin/js/clientes.js"></script>

  <!-- DataTables -->
  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>

  <!-- ✅ Eliminar: se ejecuta cuando YA existe CLIENTES -->
  <script>
    $(document).on("click", ".btn-delete-client", function () {
      const id = $(this).data("id");
      const nombre = $(this).data("nombre") || "Cliente";
      const doc = $(this).data("doc") || "";

      if (!window.CLIENTES || typeof window.CLIENTES.deleteData !== "function") {
        alert("No se encontró CLIENTES.deleteData(). Verifica que cargue admin/js/clientes.js");
        return;
      }

      if (typeof Swal !== "undefined") {
        Swal.fire({
          title: "¿Eliminar cliente?",
          html: `<div style="text-align:left">
                  <div style="font-weight:900; margin-bottom:6px;">${nombre}</div>
                  <div style="color:#64748b; font-weight:700;">${doc}</div>
                 </div>
                 <hr>
                 <div style="color:#b91c1c; font-weight:800;">Esta acción no se puede deshacer.</div>`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Sí, eliminar",
          cancelButtonText: "Cancelar",
          reverseButtons: true,
          focusCancel: true
        }).then((result) => {
          if (result.isConfirmed) window.CLIENTES.deleteData(id);
        });
      } else {
        if (confirm("¿Está seguro que desea eliminar este cliente?\n\n" + nombre + "\n" + doc)) {
          window.CLIENTES.deleteData(id);
        }
      }
    });
  </script>

</body>
</html>
