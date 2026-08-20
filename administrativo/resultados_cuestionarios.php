<?php
include './admin/include/head.php';
require_once 'admin/include/generic_classes.php';
include './admin/classes/FichaTecnicaEncuesta.php';

// Validar permisos
$view = SessionData::getPermission(74);
if (!$view) {
    require_once 'permiso_denegado.php';
    exit;
}

// Obtener fichas técnicas disponibles
$fichasTecnicas = FichaTecnicaEncuesta::getAll([]);
$fichasTecnicas = $fichasTecnicas['output']['response'] ?? [];
$modulo = 'Resultados de Cuestionarios';
?>

<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-card.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .stats-card.blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stats-card.orange {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .stats-card.purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-card .stats-number {
        font-size: 3rem;
        font-weight: bold;
        margin: 10px 0;
    }

    .stats-card .stats-label {
        font-size: 1rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stats-card i {
        font-size: 2.5rem;
        opacity: 0.3;
        float: right;
    }

    .progress-wrapper {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .progress-bar-custom {
        height: 30px;
        border-radius: 15px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.6s ease;
    }

    .table-wrapper {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .nav-tabs-custom {
        border-bottom: 2px solid #667eea;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #666;
        font-weight: 600;
        padding: 15px 25px;
    }

    .nav-tabs-custom .nav-link.active {
        color: #667eea;
        border-bottom: 3px solid #667eea;
        background: transparent;
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .badge-respondio {
        background: #38ef7d;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
    }

    .badge-pendiente {
        background: #fa709a;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
    }

    .empty-state {
        text-align: center;
        padding: 50px;
        color: #999;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ navigation menu ] start -->
    <?php include './admin/include/navbar.php'; ?>
    <!-- [ navigation menu ] end -->

    <!-- [ Header ] start -->
    <?php include './admin/include/header.php'; ?>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="content">
        <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 col-md">
                            <h4 class="text-body mb-0 d-flex align-items-center">
                                <i class="fa-solid fa-chart-line me-2" style="color: #3e465b !important;font-size: 1.3rem !important;"></i>
                                <span><?php echo $modulo; ?></span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Filtro de Ficha Técnica -->
                    <div class="filter-section">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <label for="ficha_tecnica_select" class="form-label fw-bold">
                                    <i class="fa-solid fa-filter me-2"></i>Selecciona una Ficha Técnica
                                </label>
                                <select class="form-select form-select-lg" id="ficha_tecnica_select">
                                    <option value="">Selecciona una ficha técnica...</option>
                                    <?php foreach ($fichasTecnicas as $ficha): ?>
                                        <option value="<?= $ficha['id'] ?>">
                                            <?= htmlspecialchars($ficha['realizada_por_o_encomendada_por']) ?>
                                            (<?= date('d/m/Y', strtotime($ficha['tipo_tamano_muestra_y_procedimiento_utilizado'])) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary btn-lg mt-4" id="btn_cargar_datos" disabled>
                                    <i class="fa-solid fa-sync me-2"></i>Cargar Estadísticas
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Generales -->
                    <div id="estadisticas_container" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stats-card blue">
                                    <i class="fa-solid fa-users"></i>
                                    <div class="stats-number" id="total_votantes">0</div>
                                    <div class="stats-label">Total Votantes</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card green">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <div class="stats-number" id="total_respondieron">0</div>
                                    <div class="stats-label">Han Respondido</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card orange">
                                    <i class="fa-solid fa-clock"></i>
                                    <div class="stats-number" id="total_no_respondieron">0</div>
                                    <div class="stats-label">Pendientes</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card purple">
                                    <i class="fa-solid fa-chart-pie"></i>
                                    <div class="stats-number" id="porcentaje_respuestas">0%</div>
                                    <div class="stats-label">Completado</div>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div class="row">
                            <div class="col-12">
                                <div class="progress-wrapper">
                                    <h5 class="mb-3">
                                        <i class="fa-solid fa-tasks me-2"></i>Progreso de Respuestas
                                    </h5>
                                    <div class="progress" style="height: 30px; border-radius: 15px;">
                                        <div class="progress-bar progress-bar-custom"
                                             id="progress_bar"
                                             role="progressbar"
                                             style="width: 0%;"
                                             aria-valuenow="0"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                            <strong id="progress_text">0%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Últimas Respuestas -->
                        <div class="row">
                            <div class="col-12">
                                <div class="table-wrapper">
                                    <h5 class="mb-3">
                                        <i class="fa-solid fa-history me-2"></i>Últimas 10 Respuestas
                                    </h5>
                                    <div class="table-responsive">
                                        <table id="tabla_ultimas_respuestas" class="table table-striped table-sm fs-9 mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Votante</th>
                                                    <th>Email</th>
                                                    <th>Fecha Respuesta</th>
                                                    <th>Preguntas Contestadas</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Se llena dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs de Votantes -->
                        <div class="row">
                            <div class="col-12">
                                <div class="table-wrapper">
                                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="votantesTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="respondieron-tab"
                                                    data-bs-toggle="tab" data-bs-target="#respondieron"
                                                    type="button" role="tab">
                                                <i class="fa-solid fa-check-circle me-2"></i>
                                                Votantes que Respondieron
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="noRespondieron-tab"
                                                    data-bs-toggle="tab" data-bs-target="#noRespondieron"
                                                    type="button" role="tab">
                                                <i class="fa-solid fa-clock me-2"></i>
                                                Votantes Pendientes
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="votantesTabsContent">
                                        <!-- Tab de Votantes que Respondieron -->
                                        <div class="tab-pane fade show active" id="respondieron" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tabla_respondieron" class="table table-striped table-sm fs-9 mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Email</th>
                                                            <th>Género</th>
                                                            <th>Edad</th>
                                                            <th>Ideología</th>
                                                            <th>Fecha Respuesta</th>
                                                            <th>Preguntas</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Se llena dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab de Votantes que NO Respondieron -->
                                        <div class="tab-pane fade" id="noRespondieron" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tabla_no_respondieron" class="table table-striped table-sm fs-9 mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Email</th>
                                                            <th>Username</th>
                                                            <th>Género</th>
                                                            <th>Edad</th>
                                                            <th>Ideología</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Se llena dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado vacío -->
                    <div id="empty_state" class="row">
                        <div class="col-12">
                            <div class="table-wrapper">
                                <div class="empty-state">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <h4>Selecciona una ficha técnica</h4>
                                    <p>Para ver las estadísticas y resultados de los cuestionarios</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <!-- [ Footer ] start -->
    <?php include './admin/include/footer.php'; ?>
    <!-- [ Footer ] end -->
  </div>
  </main>

  <!-- Scripts Base -->
  <?php include 'admin/include/gerenic_script.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <!-- Script del módulo -->
  <script src="admin/js/resultados_cuestionarios.js"></script>

  <script>
    RESULTADOS_CUESTIONARIOS.init();
  </script>
  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
