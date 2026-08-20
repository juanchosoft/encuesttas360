<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Votantes.php';
include './admin/classes/Departamento.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

$view = (SessionData::administrador() || SessionData::superAdministrador()) ? true : false;
if (!$view) {
  require 'permiso_denegado.php';
  exit;
}

// Informacion de departamentos
$departamentos = Departamento::getAll(null);
$departamentosResponse = $departamentos['output']['response'] ?? [];
$optionDep = "";
foreach ($departamentosResponse as $dep) {
  $optionDep .= "<option value='" . htmlspecialchars($dep['codigo_departamento'], ENT_QUOTES, 'UTF-8') . "'>" .
                htmlspecialchars($dep['codigo_departamento'], ENT_QUOTES, 'UTF-8') . " - " .
                htmlspecialchars($dep['departamento'], ENT_QUOTES, 'UTF-8') . "</option>";
}

// Información de Votantes
$arr = Votantes::getAll(null);
$isvalid = $arr['output']['valid'] ?? false;
$arr = $arr['output']['response'] ?? [];
$modulo = 'Votantes';

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
    --shadow2: 0 20px 60px rgba(2, 6, 23, .12);
  }

  body{ background: var(--bg); }
  .content{ padding-top: 14px !important; padding-bottom: 40px !important; }

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
    margin: 10px 0 16px 0;
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

  .title{ font-weight: 900; color: var(--ink); margin: 0; }
  .sub{ color: var(--muted); font-weight: 600; margin-top: 4px; }

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

  .section-card{
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 16px;
    background: rgba(255,255,255,.90);
    padding: 14px;
  }
  .section-title{
    font-weight: 900;
    color: var(--ink);
    margin: 0 0 10px 0;
    display:flex;
    align-items:center;
    gap:10px;
  }
  .section-title .dot{
    width: 12px;
    height: 12px;
    border-radius: 99px;
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
  .btn-brand:hover{
    transform: translateY(-2px);
    box-shadow: 0 18px 44px rgba(32,66,127,.25);
  }

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
              <h2 class="title mb-1">Gestión de Votantes</h2>
              <div class="sub">Registra, edita y elimina votantes de forma rápida y segura.</div>
            </div>
          </div>      
        </div>
      </div>

      <!-- FORM -->
      <div class="card card-pro mb-4">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-user-check" style="color: var(--brand); font-size: 1.2rem;"></i>
              <h4 class="mb-0" style="font-weight: 900; color: var(--ink);">
                Ingreso y listado de <?= h($modulo) ?>
              </h4>
            </div>
            <div class="text-muted" style="font-size:12px;font-weight:700;">
              Campos con * son obligatorios.
            </div>
          </div>
          <div class="small text-muted fw-semibold mt-2" id="spanEncuesta"></div>
        </div>

        <div class="card-body">
          <form class="row g-3" id="formvotantes" role="form" autocomplete="off">
            <input type="hidden" name="op" id="op" />
            <input type="hidden" name="idVotantes" id="idVotantes" />
            <input type="hidden" id="password2" name="password2" value="">

            <!-- DATOS PRINCIPALES -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Datos del votante</div>

                <div class="row g-3">
                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                        placeholder="Nombre completo del votante" required>
                      <label for="nombre_completo">Nombre completo <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select ocultar-select" id="tbl_departamento_id" name="tbl_departamento_id">
                        <?= $optionDep ?>
                      </select>
                      <label for="tbl_departamento_id">Departamento <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select" id="tbl_municipio_id" name="tbl_municipio_id"></select>
                      <label for="tbl_municipio_id">Municipio <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="comuna" name="comuna" placeholder="Comuna">
                      <label for="comuna">Comuna</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Barrio">
                      <label for="barrio">Barrio</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <select class="form-select" id="ideologia" name="ideologia" required>
                        <option value="" selected disabled>Seleccione la tendencia ideológica política</option>
                        <option value="izquierda">Izquierda</option>
                        <option value="centro_izquierda">Centro izquierda</option>
                        <option value="centro">Centro</option>
                        <option value="centro_derecha">Centro derecha</option>
                        <option value="derecha">Derecha</option>
                        <option value="sin_definir">Sin definir</option>
                      </select>
                      <label for="ideologia">Ideología política <span class="text-danger">*</span></label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- PERFIL -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Perfil</div>

                <div class="row g-3">
                  <div class="col-12 col-md-3">
                    <div class="form-floating">
                      <select class="form-select" id="rango_edad" name="rango_edad" required>
                        <option value="" selected disabled>Seleccione el grupo etario</option>
                        <option value="18-25">18-25</option>
                        <option value="26-35">26-35</option>
                        <option value="36-45">36-45</option>
                        <option value="46-55">46-55</option>
                        <option value="56-65">56-65</option>
                        <option value="66+">66+</option>
                      </select>
                      <label for="rango_edad">Rango de edad <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-3">
                    <div class="form-floating">
                      <select class="form-select" id="nivel_ingresos" name="nivel_ingresos" required>
                        <option value="" selected disabled>Seleccione el nivel de ingresos</option>
                        <option value="menos_1_salario">Menos de 1 salario</option>
                        <option value="1-2_salarios">1-2 salarios</option>
                        <option value="3-5_salarios">3-5 salarios</option>
                        <option value="6-10_salarios">6-10 salarios</option>
                        <option value="mas_10_salarios">Más de 10 salarios</option>
                      </select>
                      <label for="nivel_ingresos">Nivel socioeconómico <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-3">
                    <div class="form-floating">
                      <select class="form-select" id="genero" name="genero" required>
                        <option value="" selected disabled>Seleccione la identidad de género</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="otro">Otro</option>
                        <option value="prefiero_no_decir">Prefiero no decir</option>
                      </select>
                      <label for="genero">Género <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-3">
                    <div class="form-floating">
                      <select class="form-select" id="nivel_educacion" name="nivel_educacion">
                        <option value="" selected disabled>Seleccione el máximo nivel educativo alcanzado</option>
                        <option value="primaria_incompleta">Primaria incompleta</option>
                        <option value="primaria_completa">Primaria completa</option>
                        <option value="secundaria_incompleta">Secundaria incompleta</option>
                        <option value="secundaria_completa">Secundaria completa</option>
                        <option value="tecnico">Técnico</option>
                        <option value="tecnologo">Tecnólogo</option>
                        <option value="universitario_incompleto">Universitario incompleto</option>
                        <option value="universitario_completo">Universitario completo</option>
                        <option value="posgrado">Posgrado</option>
                      </select>
                      <label for="nivel_educacion">Nivel educativo</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-3">
                    <div class="form-floating">
                      <select class="form-select" id="ocupacion" name="ocupacion" required>
                        <option value="" selected disabled>Seleccione la ocupación</option>
                        <option value="Empleado">Empleado</option>
                        <option value="Auto Empleado">Auto Empleado</option>
                        <option value="Empresario">Empresario</option>
                        <option value="Comerciante">Comerciante</option>
                        <option value="Independiente">Independiente</option>
                      </select>
                      <label for="ocupacion">Ocupación <span class="text-danger">*</span></label>
                    </div>
                  </div>

                  <div class="col-12 col-md-3">
                    <div class="form-floating">
                      <select class="form-select" id="estado" name="estado" required>
                        <option value="" selected disabled>Seleccione el estado de la cuenta</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                      </select>
                      <label for="estado">Estado de la cuenta <span class="text-danger">*</span></label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- ACCESO -->
            <div class="col-12">
              <div class="section-card">
                <div class="section-title"><span class="dot"></span> Acceso</div>

                <div class="row g-3">
                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="email" name="email"
                        placeholder="Correo electrónico" onblur="VOTANTES.checkAvailability(this)">
                      <label for="email">Correo electrónico</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="username" name="username"
                        placeholder="Nombre de usuario" onblur="VOTANTES.checkAvailability(this)">
                      <label for="username">Nombre de usuario</label>
                    </div>
                  </div>

                  <div class="col-12 col-md-4">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="password" name="password"
                        placeholder="Contraseña" required>
                      <label for="password">Contraseña</label>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- ACTION BAR -->
            <div class="col-12">
              <div class="action-bar">
                <div class="action-inner">
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="chip"><i class="fas fa-circle-info"></i> Listo para guardar</span>
                    <span class="text-muted" style="font-size:12px;font-weight:700;">
                      Tip: usa “Editar” para actualizar un votante.
                    </span>
                  </div>

                  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <button type="button" onclick="VOTANTES.emptyCells();" class="btn btn-soft px-4">
                      <i class="fas fa-xmark me-2"></i>Cancelar
                    </button>
                    <button class="btn btn-brand px-4" type="button" onclick="VOTANTES.validateData();">
                      <i class="fas fa-floppy-disk me-2"></i>Guardar
                    </button>
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
            <h4 class="title mb-1">Listado de Votantes</h4>
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
                <th>Tipo</th>
                <th>Encuestado por</th>
                <th>Nombre completo</th>
                <th>Ideología</th>
                <th>Edad</th>
                <th>Nivel socioeconómico</th>
                <th>Género</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Nivel educativo</th>
                <th>Ocupación</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody class="list">
              <?php if ($isvalid && count($arr) > 0): ?>
                <?php foreach ($arr as $item): ?>
                  <?php $id = (int)($item['id'] ?? 0); ?>
                  <tr>
                    <td>
                      <button type="button" class="btn btn-sm btn-primary" title="Editar"
                        onclick="VOTANTES.editData(<?= $id ?>)">
                        <i class="uil uil-edit"></i>
                      </button>
                    </td>
                    <td>
                     <button type="button" class="btn btn-sm btn-danger" title="Eliminar"
                      onclick="VOTANTES.deleteData(<?= (int)$item['id'] ?>)">
                      <i class="uil uil-trash-alt"></i>
                    </button>

                    </td>
                    <td><?= h($item['tipo_registro'] ?? 'Encuestado') ?></td>
                    <td><?= h($item['encuestador_nombre_completo'] ?? 'Sin asignar') ?></td>
                    <td><?= h($item['nombre_completo'] ?? '') ?></td>
                    <td><?= h($item['ideologia'] ?? '') ?></td>
                    <td><?= h($item['rango_edad'] ?? '') ?></td>
                    <td><?= h($item['nivel_ingresos'] ?? '') ?></td>
                    <td><?= h($item['genero'] ?? '') ?></td>
                    <td><?= h($item['codigo_departamento'] ?? '') ?></td>
                    <td><?= h($item['codigo_municipio'] ?? '') ?></td>
                    <td><?= h($item['nivel_educacion'] ?? '') ?></td>
                    <td><?= h($item['ocupacion'] ?? '') ?></td>
                    <td><?= h($item['estado'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="14" class="text-center py-4 text-muted">No se encontraron registros.</td>
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
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <script src="admin/js/departamentoDama.js"></script>
  <script type="text/javascript" src="./admin/js/lib/data-md5.js"></script>
  <script type="text/javascript" src="admin/js/votantes.js"></script>

  <?php include './admin/include/generic_dataTables.php'; ?>
  <?php include 'admin/include/scriptsgober360.php'; ?>

  <script src="vendors/flatpickr/flatpickr.min.js"></script>
  <script>
    const departamento = $("#departamentoConfiguracionInput").val();
    $("#tbl_departamento_id").val(departamento);
    DEPARTAMENTO.getMunicipios();
  </script>
</body>
</html>
