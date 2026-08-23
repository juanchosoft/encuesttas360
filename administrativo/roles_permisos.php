<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Role.php';

requireAnyPermission(['configuracion.roles.view', 'configuracion.roles.manage']);
$canManage = SessionData::hasPermission('configuracion.roles.manage');

$rolesRes = Role::getAll();
$roles = $rolesRes['output']['response'] ?? [];
$modulo = 'Roles y Permisos';

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<body>
<main class="main" id="top">

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <style>
    :root{ --brand:#20427F; --brand2:#132b52; --ink:#0f172a; --muted:#64748b; --bg:#f6f8fb; --line: rgba(15,23,42,.08); --radius:18px; --shadow: 0 14px 40px rgba(2,6,23,.08); }
    body{ background: var(--bg); }
    .content{ padding-top:14px !important; padding-bottom:40px !important; }
    .saas-hero{ background: linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,255,255,.80)); border:1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); padding:18px; margin-bottom:16px; }
    .chip{ display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border-radius:999px; background: rgba(32,66,127,.08); border:1px solid rgba(32,66,127,.14); color: var(--brand2); font-weight:800; font-size:12px; }
    .card-pro{ border:1px solid var(--line) !important; border-radius: var(--radius) !important; box-shadow: var(--shadow) !important; background:#fff !important; overflow:hidden; }
    .card-pro .card-header{ background: rgba(255,255,255,.88) !important; border-bottom:1px solid var(--line) !important; padding:16px 18px !important; }
    .btn-brand{ background: linear-gradient(135deg, var(--brand), var(--brand2)); border:0 !important; border-radius:14px; font-weight:900; padding:12px 18px; color:#fff; }
    .btn-brand:hover{ color:#fff; }
    .btn-soft{ background: rgba(32,66,127,.08); border:1px solid rgba(32,66,127,.18) !important; color: var(--brand2); border-radius:14px; font-weight:900; padding:12px 16px; }
    .table-wrap{ border:1px solid var(--line); border-radius:18px; background:#fff; box-shadow: var(--shadow); overflow:hidden; }
    .badge-sistema{ background: rgba(32,66,127,.10); color: var(--brand2); font-weight:800; }
    .badge-custom{ background: rgba(16,185,129,.12); color:#047857; font-weight:800; }
    .module-group{ border:1px solid var(--line); border-radius:14px; margin-bottom:10px; overflow:hidden; }
    .module-group-header{ background: rgba(32,66,127,.06); padding:10px 14px; font-weight:900; color:var(--ink); display:flex; align-items:center; justify-content:space-between; cursor:pointer; }
    .module-group-body{ padding:10px 14px; display:flex; flex-wrap:wrap; gap:10px 22px; }
    .perm-check-label{ font-size:13px; color:var(--ink); }
    .perm-check-key{ font-size:11px; color:var(--muted); display:block; }
  </style>

  <div class="content">
    <div class="container-fluid container-xxl-saas">

      <div class="saas-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div>
            <div class="chip mb-2"><i class="fas fa-user-shield"></i> RBAC</div>
            <h2 class="title mb-1">Roles y Permisos</h2>
            <div class="sub">Crea roles personalizados y asigna permisos por clave, sin tocar código.</div>
          </div>
          <?php if ($canManage): ?>
          <button class="btn btn-brand" type="button" onclick="ROLES.nuevo();">
            <i class="fas fa-plus me-2"></i>Nuevo rol
          </button>
          <?php endif; ?>
        </div>
      </div>

      <div class="table-wrap p-3 p-lg-4">
        <div class="table-responsive">
          <table class="table table-striped table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Acciones</th>
                <th>Nombre</th>
                <th>Clave (role_key)</th>
                <th>Tipo</th>
                <th>Permisos</th>
                <th>Usuarios</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($roles as $r): ?>
                <tr>
                  <td>
                    <?php if ($canManage): ?>
                      <button type="button" class="btn btn-sm btn-primary" title="Editar" onclick="ROLES.editar(<?= (int) $r['id'] ?>)">
                        <i class="uil uil-edit"></i>
                      </button>
                      <?php if ((int) $r['is_system'] === 0): ?>
                        <button type="button" class="btn btn-sm btn-danger" title="Eliminar" onclick="ROLES.eliminar(<?= (int) $r['id'] ?>, '<?= h($r['name']) ?>')">
                          <i class="uil uil-trash-alt"></i>
                        </button>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-muted">Solo lectura</span>
                    <?php endif; ?>
                  </td>
                  <td><?= h($r['name']) ?></td>
                  <td><code><?= h($r['role_key']) ?></code></td>
                  <td>
                    <?php if ((int) $r['is_system'] === 1): ?>
                      <span class="badge badge-sistema">Sistema</span>
                    <?php else: ?>
                      <span class="badge badge-custom">Personalizado</span>
                    <?php endif; ?>
                  </td>
                  <td><?= (int) $r['total_permisos'] ?></td>
                  <td><?= (int) $r['total_usuarios'] ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($roles)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No hay roles registrados.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php include './admin/include/footer.php'; ?>
    </div>
  </div>

  <!-- MODAL ROL -->
  <div class="modal fade" id="modalRol" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header" style="background: linear-gradient(135deg, var(--brand), var(--brand2));">
          <h5 class="modal-title text-white mb-0"><i class="fas fa-user-shield me-2"></i>Rol</h5>
          <button class="btn btn-link p-2" type="button" data-bs-dismiss="modal" aria-label="Close">
            <i class="fas fa-times fs-5 text-white"></i>
          </button>
        </div>
        <div class="modal-body p-4" style="max-height:70vh; overflow-y:auto; background:#f8f9fa;">
          <form id="formrol" autocomplete="off">
            <input type="hidden" id="rol_id" name="id" value="0">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Nombre del rol</label>
                <input type="text" class="form-control" id="rol_name" name="name" placeholder="ej. Coordinador de Campo" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Descripción</label>
                <input type="text" class="form-control" id="rol_description" name="description" placeholder="Opcional">
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0 fw-bold">Permisos</h6>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="rol_check_all" onchange="ROLES.toggleAll(this.checked);">
                <label class="form-check-label fw-bold" for="rol_check_all">Marcar / desmarcar todos</label>
              </div>
            </div>
            <div id="rol_permisos_container"><!-- dinámico --></div>
          </form>
        </div>
        <div class="modal-footer bg-white">
          <button class="btn btn-soft px-4" type="button" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-brand px-4" type="button" onclick="ROLES.guardar();">
            <i class="fas fa-check me-2"></i>Guardar
          </button>
        </div>
      </div>
    </div>
  </div>

</main>

<?php include 'admin/include/gerenic_script.php'; ?>

<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>

<script>
  const ROLES_CAN_MANAGE = <?= $canManage ? 'true' : 'false' ?>;
</script>
<script defer src="admin/js/roles_permisos.js"></script>

<?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
