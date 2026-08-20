<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';
include './admin/classes/Departamento.php';

// Permisos - SOLO ADMINISTRADOR puede acceder a Usuarios
$view    = SessionData::getPermission(1);
$create  = SessionData::getPermission(2);
$edit    = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);

// Validar que tenga permiso de ver
if (!$view) {
  require 'permiso_denegado.php';
  exit;
}

// Validar que sea Administrador (solo ellos pueden gestionar usuarios)
if (!SessionData::administrador()) {
  require 'permiso_denegado.php';
  exit;
}

// Información de Usuarios
$arr = Usuario::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];
$modulo = 'Usuarios del sistema';

// Información de Departamentos
$arrDep = Departamento::getAll(null);
$arrDep = $arrDep['output']['response'] ?? [];

$optionDep = Util::getDepartamentoPrincipal();
foreach ($arrDep as $val) {
  $selected = ($val["codigo_departamento"] == Util::getDepartamentoPrincipal()) ? "selected" : "";
  $optionDep .= "<option $selected value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<body>
<main class="main" id="top">

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

    .content{
      padding-top: 14px !important;
      padding-bottom: 40px !important;
    }
    @media (min-width: 1400px){
      .container-xxl-saas{ max-width: 1500px; }
    }

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
      position:absolute;
      inset:-2px;
      background: linear-gradient(135deg, rgba(32,66,127,.18), rgba(19,43,82,.06), rgba(46,88,168,.16));
      filter: blur(18px);
      opacity:.65;
      z-index:0;
    }
    .saas-hero > *{ position:relative; z-index:1; }

    .chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:7px 12px;
      border-radius: 999px;
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

    .title{
      font-weight: 900;
      color: var(--ink);
      margin: 0;
    }
    .sub{
      color: var(--muted);
      font-weight: 600;
      margin-top: 4px;
    }

    .form-floating>.form-control,
    .form-floating>.form-select{
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.12);
      box-shadow: none;
    }
    .form-control:focus,
    .form-select:focus{
      border-color: rgba(32,66,127,.45) !important;
      box-shadow: 0 0 0 .25rem rgba(32,66,127,.12) !important;
    }

    .btn-brand{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border: 0 !important;
      border-radius: 14px;
      font-weight: 900;
      padding: 12px 18px;
      box-shadow: 0 14px 30px rgba(32,66,127,.18);
      transition: transform .16s ease, box-shadow .16s ease;
      color:#fff;
    }
    .btn-brand:hover{ transform: translateY(-2px); box-shadow: 0 18px 44px rgba(32,66,127,.25); color:#fff; }

    .btn-soft{
      background: rgba(32,66,127,.08);
      border: 1px solid rgba(32,66,127,.18) !important;
      color: var(--brand2);
      border-radius: 14px;
      font-weight: 900;
      padding: 12px 16px;
    }

    .uploader-wrap{
      border: 1px dashed rgba(32,66,127,.25);
      border-radius: 16px;
      background: rgba(32,66,127,.04);
      padding: 12px;
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

    .avatar{
      width: 44px;
      height: 44px;
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.10);
      object-fit: cover;
      background: #fff;
    }

    .modal-header-brand{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      border: 0;
    }
    .modal-icon{
      width: 44px; height: 44px;
      border-radius: 14px;
      background: rgba(255,255,255,.18);
      display:flex;
      align-items:center;
      justify-content:center;
      margin-right: 12px;
    }
    .modal-footer{
      border-top: 1px solid rgba(15,23,42,.08) !important;
    }
  </style>

  <div class="content">
    <div class="container-fluid container-xxl-saas">

      <div class="saas-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div>
            <div class="chip mb-2"><i class="fas fa-users"></i> Administración</div>
            <h2 class="title mb-1">Usuarios del sistema</h2>
            <div class="sub">Crea, edita y administra permisos con una experiencia SaaS limpia y rápida.</div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="chip"><i class="fas fa-user-shield"></i> Solo Administrador</span>
            <span class="chip"><i class="fas fa-lock"></i> Control de accesos</span>
          </div>
        </div>
      </div>

      <div class="card card-pro mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-user-edit" style="color: var(--brand); font-size: 1.2rem;"></i>
              <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">Formulario de usuarios</h4>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:700;">Completa la información y guarda.</div>
          </div>
        </div>

        <div class="card-body">
          <form class="row g-3" id="formusuarios" role="form" autocomplete="off">
            <input type="hidden" name="op" id="op" value="" />
            <input type="hidden" name="id" id="id" value="" />

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating">
                <input class="form-control" type="text" id="nombre" name="nombre" placeholder="Ingrese nombres" required />
                <label for="nombre">Nombres <span class="text-danger">*</span></label>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating">
                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese apellidos" required />
                <label for="apellido">Apellidos <span class="text-danger">*</span></label>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating">
                <select class="form-select" id="tipo" name="tipo" required>
                  <option value="">-- Seleccione tipo --</option>
                  <option value="Administrador">Administrador</option>
                  <option value="Investigador">Investigador</option>
                  <option value="Visor">Visor</option>
                  <option value="Operativo">Operativo</option>
                  <option value="Encuestador">Encuestador</option>
                  <option value="Cliente">Cliente</option>
                </select>
                <label for="tipo">Tipo de Usuario <span class="text-danger">*</span></label>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating">
                <select onchange="if(window.DEPARTAMENTO && DEPARTAMENTO.getMunicipios) DEPARTAMENTO.getMunicipios();" class="form-select" id="tbl_departamento_id" name="tbl_departamento_id" required>
                  <?= $optionDep ?>
                </select>
                <label for="tbl_departamento_id">Departamento <span class="text-danger">*</span></label>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3" id="alcaldia-container">
              <div class="form-floating">
                <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id" required></select>
                <label for="tbl_municipio_id">Alcaldía <span class="text-danger">*</span></label>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating">
                <input type="email" class="form-control" id="nickname" name="nickname" placeholder="Usuario" required>
                <label for="nickname">Correo <span class="text-danger">*</span></label>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating position-relative">
                <input type="password" class="form-control" id="hashpass" name="hashpass" placeholder="Contraseña" required>
                <label for="hashpass">Contraseña <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-3 border-0 bg-transparent"
                        onclick="togglePassword('hashpass', this)">
                  <i class="fas fa-eye text-secondary"></i>
                </button>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
              <div class="form-floating position-relative">
                <input type="password" autocomplete="new-password" class="form-control" id="hashpass1" name="hashpass1" placeholder="Repite la contraseña" required>
                <label for="hashpass1">Repita la Contraseña <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-3 border-0 bg-transparent"
                        onclick="togglePassword('hashpass1', this)">
                  <i class="fas fa-eye text-secondary"></i>
                </button>
              </div>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
              <div class="form-floating">
                <select class="form-select" id="habilitado" name="habilitado">
                  <option value="si">Sí</option>
                  <option value="no">No</option>
                </select>
                <label for="habilitado">Habilitado</label>
              </div>
            </div>

            <?php if ($create && $edit): ?>
            <div class="col-12">
              <label class="form-label" style="font-weight:900;color:var(--ink);">Foto</label>
              <div class="uploader-wrap">
                <iframe id="ifm" name="ifm" src="upload.php" width="100%" height="260" scrolling="no" frameborder="0" style="border: none;"></iframe>
              </div>
            </div>
            <?php endif; ?>

            <div class="col-12">
              <div class="d-flex justify-content-end gap-2 flex-wrap">
                <button type="button" onclick="if(window.UTIL && UTIL.clearForm) UTIL.clearForm('formusuarios'); else document.getElementById('formusuarios').reset();" class="btn btn-soft px-4">
                  <i class="fas fa-xmark me-2"></i>Cancelar
                </button>

                <?php if ($create && $edit): ?>
                <button class="btn btn-brand px-4" type="button" onclick="USUARIO.validateData();">
                  <i class="fas fa-floppy-disk me-2"></i>Guardar
                </button>
                <?php endif; ?>
              </div>
            </div>

          </form>
        </div>
      </div>

      <div class="table-wrap p-3 p-lg-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <h4 class="title mb-1">Listado de usuarios</h4>
            <div class="sub">Administra edición, permisos y estado de cada usuario.</div>
          </div>
          <div class="chip"><i class="fas fa-table"></i> DataTable</div>
        </div>

        <div class="table-responsive">
          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 align-middle">
            <thead>
              <tr>
                <th>Editar</th>
                <th>Permisos</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Tipo</th>
                <th>Usuario</th>
                <th>Habilitado</th>
                <th>Foto</th>
              </tr>
            </thead>
            <tbody class="dt-list">
              <?php if ($isvalid && !empty($arr)): ?>
                <?php foreach ($arr as $item): ?>
                  <?php $img = !empty($item["img"]) ? "assets/img/admin/" . h($item["img"]) : 'assets/img/santander.png'; ?>
                  <tr>
                    <td>
                      <button type="button" class="btn btn-sm btn-primary" title="Editar" onclick="USUARIO.editData(<?= (int)$item['id'] ?>)">
                        <i class="uil uil-edit"></i>
                      </button>
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-phoenix-warning"
                              title="Asignar Permisos"
                              data-bs-toggle="modal"
                              data-bs-target="#myModalPermisos"
                              onclick="if(window.PERMISOS && PERMISOS.editpermission) PERMISOS.editpermission(<?= (int)$item['id'] ?>);">
                        <i class="fas fa-sitemap" style="color: var(--brand);"></i>
                      </button>
                    </td>
                    <td><?= h($item['nombre']) ?></td>
                    <td><?= h($item['apellido']) ?></td>
                    <td><?= h($item['tipo']) ?></td>
                    <td><?= h($item['nickname']) ?></td>
                    <td>
                      <?php if (strtolower((string)$item['habilitado']) === 'si'): ?>
                        <span class="badge bg-success-subtle text-success fw-bold">Sí</span>
                      <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger fw-bold">No</span>
                      <?php endif; ?>
                    </td>
                    <td><img class="avatar" src="<?= $img ?>" alt="Imagen"></td>
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

      <!-- MODAL PERMISOS -->
      <div class="modal fade" id="myModalPermisos" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalPermisosLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
          <div class="modal-content border-0 shadow-lg">

            <div class="modal-header modal-header-brand">
              <div class="d-flex align-items-center">
                <div class="modal-icon">
                  <i class="fas fa-shield-alt text-white fs-4"></i>
                </div>
                <div>
                  <h5 class="modal-title text-white mb-0" id="modalPermisosLabel">
                    <i class="fas fa-key me-2"></i>Gestión de Permisos
                  </h5>
                  <p class="text-white-50 small mb-0 mt-1">Administra accesos y permisos del sistema</p>
                </div>
              </div>
              <button class="btn btn-link p-2" type="button" data-bs-dismiss="modal" aria-label="Close" onclick="if(window.UTIL && UTIL.clearForm) UTIL.clearForm('formpermission');">
                <i class="fas fa-times fs-5 text-white"></i>
              </button>
            </div>

            <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto; background-color: #f8f9fa;">
              <div class="card card-pro mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <div>
                    <h6 class="mb-1" style="font-weight:900;color:var(--ink);">
                      <i class="fas fa-tasks me-2" style="color:var(--brand);"></i>Selección rápida
                    </h6>
                    <small class="text-muted">Activa o desactiva todos los permisos con un clic.</small>
                  </div>
                  <div class="form-check form-switch form-switch-lg">
                    <input class="form-check-input" type="checkbox" id="check_permisos" onchange="if(window.PERMISOS && PERMISOS.checkAll) PERMISOS.checkAll();" style="cursor: pointer;">
                    <label class="form-check-label ms-2 fw-bold" for="check_permisos" style="cursor: pointer; color: var(--brand2);">
                      Todos los permisos
                    </label>
                  </div>
                </div>
              </div>

              <form id="formpermission" autocomplete="off">
                <div id="permission"><!-- dinámico --></div>
              </form>
            </div>

            <div class="modal-footer bg-white">
              <button class="btn btn-soft px-4" type="button" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cancelar
              </button>
              <button class="btn btn-brand px-4" type="button" onclick="if(window.PERMISOS && PERMISOS.savepermission) PERMISOS.savepermission();">
                <i class="fas fa-check me-2"></i>Guardar Permisos
              </button>
            </div>

          </div>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>

    </div>
  </div>

</main>

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>

<?php include './admin/include/generic_dataTables.php'; ?>

<!-- Tus JS (pueden fallar, pero ya no te tumban guardar porque abajo forzamos USUARIO) -->
<script defer src="admin/js/departamento.js"></script>
<script defer src="admin/js/lib/data-md5.js"></script>
<script defer src="admin/js/usuario.js"></script>
<script defer src="admin/js/permisos.js"></script>

<script>
  // ==========================
  // ✅ PARCHE DEFINITIVO: USUARIO SIEMPRE EXISTE
  // ==========================
  window.USUARIO = window.USUARIO || {};

  // Endpoint por defecto del sistema (ajusta si el tuyo es otro)
  const USUARIO_ENDPOINT = "admin/ajax/generic_request.php"; // <-- si tu endpoint real es otro, cambia SOLO esta línea

  function postJSON(url, data) {
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body: new URLSearchParams(data).toString()
    }).then(r => r.json());
  }

  function formToObj(formId){
    const f = document.getElementById(formId);
    const fd = new FormData(f);
    const obj = {};
    fd.forEach((v,k)=> obj[k]=String(v ?? "").trim());
    return obj;
  }

  function toast(msg){
    // Si tienes SweetAlert2 o UTIL, úsalo; si no, alert simple
    if (window.Swal) return Swal.fire({ icon:"info", title:"", text: msg });
    alert(msg);
  }

  // ✅ validar + guardar (create/edit)
  USUARIO.validateData = USUARIO.validateData || function () {
    const data = formToObj("formusuarios");

    // Validaciones mínimas
    if (!data.nombre || !data.apellido || !data.tipo || !data.tbl_departamento_id || !data.tbl_municipio_id || !data.nickname) {
      return toast("Por favor completa todos los campos obligatorios.");
    }
    if (!data.hashpass || !data.hashpass1) {
      return toast("Por favor ingresa y repite la contraseña.");
    }
    if (data.hashpass !== data.hashpass1) {
      return toast("Las contraseñas no coinciden.");
    }

    // Define operación (si hay id -> editar, si no -> crear)
    const isEdit = data.id && String(data.id).trim() !== "";
    data.op = isEdit ? "usuarioedit" : "usuariocreate";

    // Si tu sistema usa md5 en el backend, puedes enviarla normal o md5:
    // data.hashpass = (window.hex_md5) ? hex_md5(data.hashpass) : data.hashpass;
    // data.hashpass1 = data.hashpass;

    // Loader si existe
    if (window.UTIL && UTIL.cursorWait) UTIL.cursorWait();

    postJSON(USUARIO_ENDPOINT, data)
      .then(res => {
        if (window.UTIL && UTIL.cursorNormal) UTIL.cursorNormal();

        // Manejo flexible según tu API
        const ok = (res && (res.valid === true || res.output?.valid === true || res.output?.ok === true || res.ok === true));
        const msg = res?.msg || res?.output?.msg || (ok ? "Guardado correctamente." : "No fue posible guardar.");

        if (!ok) {
          console.error("Respuesta backend:", res);
          return toast(msg);
        }

        toast(msg);
        // Recargar para refrescar tabla (simple y seguro)
        setTimeout(()=> location.reload(), 600);
      })
      .catch(err => {
        if (window.UTIL && UTIL.cursorNormal) UTIL.cursorNormal();
        console.error(err);
        toast("Error de red o del servidor al guardar.");
      });
  };

  // ✅ cargar data para editar (si tu backend lo soporta)
  USUARIO.editData = USUARIO.editData || function (id) {
    if (!id) return;

    if (window.UTIL && UTIL.cursorWait) UTIL.cursorWait();

    postJSON(USUARIO_ENDPOINT, { op: "usuarioget", id: id })
      .then(res => {
        if (window.UTIL && UTIL.cursorNormal) UTIL.cursorNormal();

        const row = res?.response?.[0] || res?.output?.response?.[0] || res?.data?.[0] || null;
        if (!row) {
          console.error("Respuesta backend:", res);
          return toast("No se pudo cargar el usuario para editar.");
        }

        document.getElementById("id").value = row.id ?? id;
        document.getElementById("nombre").value = row.nombre ?? "";
        document.getElementById("apellido").value = row.apellido ?? "";
        document.getElementById("tipo").value = row.tipo ?? "";
        document.getElementById("nickname").value = row.nickname ?? "";
        document.getElementById("habilitado").value = (row.habilitado ?? "si");

        // departamento/municipio si vienen
        if (row.tbl_departamento_id || row.codigo_departamento) {
          document.getElementById("tbl_departamento_id").value = row.tbl_departamento_id || row.codigo_departamento;
          if (window.DEPARTAMENTO && DEPARTAMENTO.getMunicipios) {
            DEPARTAMENTO.getMunicipios();
            setTimeout(() => {
              if (row.tbl_municipio_id || row.codigo_municipio) {
                const mid = row.tbl_municipio_id || row.codigo_municipio;
                const sel = document.getElementById("tbl_municipio_id");
                if (sel) sel.value = mid;
              }
            }, 800);
          }
        }

        // seguridad: no rellenar contraseñas
        document.getElementById("hashpass").value = "";
        document.getElementById("hashpass1").value = "";

        toast("Usuario cargado. Ahora puedes actualizar y guardar.");
      })
      .catch(err => {
        if (window.UTIL && UTIL.cursorNormal) UTIL.cursorNormal();
        console.error(err);
        toast("Error al cargar el usuario.");
      });
  };

  // ==========================
  // Helpers existentes
  // ==========================
  setTimeout(function() {
    if (typeof DEPARTAMENTO !== "undefined" && typeof DEPARTAMENTO.getMunicipios === "function") {
      DEPARTAMENTO.getMunicipios();
    }
  }, 600);

  function togglePassword(id, btn){
    var el = document.getElementById(id);
    if(!el) return;
    var isPass = el.getAttribute("type") === "password";
    el.setAttribute("type", isPass ? "text" : "password");
    if(btn){
      var icon = btn.querySelector("i");
      if(icon){
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
      }
    }
  }
</script>

<?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
