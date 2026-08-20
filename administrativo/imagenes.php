<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Departamento.php';
include './admin/classes/Factores.php';
include './admin/classes/IngresoInformacion.php';
require_once './admin/classes/DbConection.php';

// Conexión a la base de datos
$dbConnection = new DbConection();
$db = $dbConnection->openConect();
try {
    $db->exec("USE " . $dbConnection->getDbName());
} catch (PDOException $e) {
    die("Error al seleccionar la base de datos: " . $e->getMessage());
}


// Cargar departamentos
$optionDep = [];
try {
    $stmt = $db->query("SELECT codigo_departamento AS id, departamento AS nombre FROM tbl_departamentos");
    $optionDep = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar los departamentos: " . $e->getMessage();
}
// Configurar el departamento predeterminado como "Santander"
$defaultDepartamento = 'Putumayo';

// Obtener ID del departamento "Santander"
$stmtSantander = $db->prepare("SELECT codigo_departamento FROM tbl_departamentos WHERE departamento = ?");
$stmtSantander->execute([$defaultDepartamento]);
$santanderId = $stmtSantander->fetchColumn();

// Obtener municipios del departamento "Santander"
$defaultMunicipios = [];
if ($santanderId) {
    $stmtMunicipios = $db->prepare("SELECT codigo_muncipio, municipio FROM tbl_ciudades WHERE codigo_departamento = ?");
    $stmtMunicipios->execute([$santanderId]);
    $defaultMunicipios = $stmtMunicipios->fetchAll(PDO::FETCH_ASSOC);
}

// Ajustar el departamento seleccionado y los municipios mostrados
$selectedDepartamento = $filtros['departamento_id'] ?? $santanderId;
$selectedMunicipio = $filtros['municipio_id'] ?? null;


// Cargar factores
$optionFactores = [];
try {
    $stmt = $db->query("SELECT id, tipo AS nombre FROM tbl_factores");
    $optionFactores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar los factores: " . $e->getMessage();
}

// Procesar filtros
$filtros = [
    'departamento_id' => $_GET['departamento_id'] ?? null,
    'municipio_id' => $_GET['municipio_id'] ?? null,
    'factor_id' => $_GET['factor_id'] ?? null,
    'fecha_inicial' => $_GET['fecha_inicial'] ?? null,
    'fecha_final' => $_GET['fecha_final'] ?? null,
];

$queryAntes = "
    SELECT 
        tbl_ingreso_informacion.foto1, 
        tbl_ingreso_informacion.foto2, 
        tbl_ingreso_informacion.foto3, 
        tbl_ingreso_informacion.foto4, 
        tbl_ingreso_informacion.dtcreate, 
        tbl_ingreso_informacion.observaciones, 
        tbl_ciudades.municipio, 
        tbl_vereda_id AS vereda
    FROM 
        tbl_ingreso_informacion
    INNER JOIN 
        tbl_ciudades ON tbl_ingreso_informacion.codigo_municipio = tbl_ciudades.codigo_muncipio
    LEFT JOIN 
        tbl_vereda ON tbl_ingreso_informacion.tbl_vereda_id = tbl_vereda.id
    WHERE 1=1
";


$paramsAntes = [];

// Si no hay filtros, cargar las imágenes de Bucaramanga como predeterminadas
if (empty($filtros['departamento_id']) && empty($filtros['municipio_id']) && empty($filtros['factor_id']) && empty($filtros['fecha_inicial']) && empty($filtros['fecha_final'])) {
    $queryAntes .= " AND tbl_ciudades.municipio = ?";
    $paramsAntes[] = 'BUCARAMANGA';
} else {
    // Aplicar filtros solo si están definidos
    if (!empty($filtros['departamento_id'])) {
        $queryAntes .= " AND tbl_ciudades.codigo_departamento = ?";
        $paramsAntes[] = $filtros['departamento_id'];
    }
    if (!empty($filtros['municipio_id'])) {
        $queryAntes .= " AND tbl_ingreso_informacion.codigo_municipio = ?";
        $paramsAntes[] = $filtros['municipio_id'];
    }
    if (!empty($filtros['fecha_inicial']) && !empty($filtros['fecha_final'])) {
        $queryAntes .= " AND tbl_ingreso_informacion.dtcreate BETWEEN ? AND ?";
        $paramsAntes[] = $filtros['fecha_inicial'];
        $paramsAntes[] = $filtros['fecha_final'];
    }
}

// Ejecutar consulta
$stmtAntes = $db->prepare($queryAntes);
$stmtAntes->execute($paramsAntes);
$recordsAntes = $stmtAntes->fetchAll(PDO::FETCH_ASSOC);


// Consultar imágenes "después"
$queryDespues = "
    SELECT 
        tbl_ingreso_informacion_x_actualizacion.foto_actualizada_1, 
        tbl_ingreso_informacion_x_actualizacion.foto_actualizada_2, 
        tbl_ingreso_informacion_x_actualizacion.foto_actualizada_3, 
        tbl_ingreso_informacion_x_actualizacion.foto_actualizada_4, 
        tbl_ingreso_informacion_x_actualizacion.dtcreate AS fecha_actualizacion, 
        tbl_ingreso_informacion_x_actualizacion.observaciones_actualizacion, 
        tbl_ciudades.municipio
    FROM 
        tbl_ingreso_informacion_x_actualizacion
    INNER JOIN 
        tbl_ingreso_informacion ON tbl_ingreso_informacion_x_actualizacion.tbl_ingreso_informacion_id = tbl_ingreso_informacion.id
    INNER JOIN 
        tbl_ciudades ON tbl_ingreso_informacion.codigo_municipio = tbl_ciudades.codigo_muncipio
    WHERE 1=1
";



$paramsDespues = [];
if (!empty($filtros['departamento_id'])) {
    $queryDespues .= " AND tbl_ciudades.codigo_departamento = ?";
    $paramsDespues[] = $filtros['departamento_id'];
}
if (!empty($filtros['municipio_id'])) {
    $queryDespues .= " AND tbl_ciudades.codigo_muncipio = ?";
    $paramsDespues[] = $filtros['municipio_id'];
}
if (!empty($filtros['fecha_inicial']) && !empty($filtros['fecha_final'])) {
    $queryDespues .= " AND tbl_ingreso_informacion_x_actualizacion.dtcreate BETWEEN ? AND ?"; // Asegúrate de que `dtcreate` sea el campo correcto
    $paramsDespues[] = $filtros['fecha_inicial'];
    $paramsDespues[] = $filtros['fecha_final'];
}

// Ejecutar consulta "después"
$stmtDespues = $db->prepare($queryDespues);
$stmtDespues->execute($paramsDespues);
$recordsDespues = $stmtDespues->fetchAll(PDO::FETCH_ASSOC);

$dbConnection->closeConect();
?>


<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ navigation  ] start -->
    <?php
    include './admin/include/navbar.php';
    ?>
        <!-- [ navigation  ] end -->
        <!-- [ Header ] start -->
        <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->
	<!-- [ Header ] end -->
	
	

<!-- [ Main Content ] start -->
<div class="content">
    <div class="pcoded-content">
       
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ sample-page ] start -->
            <div class="col-sm-12">
         
              <div class="card shadow-none border my-4" data-component-card="data-component-card">
                <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                      <div class="col-12 col-md">
                      <h4 class="text-body mb-0 d-flex align-items-center">
                        <i class="uil uil-slider-h me-2 fs-4 text-warning"></i> Imágenes
                      </h4>
                      </div>
                    </div>
                  </div>
                <div class="card">

                    <div class="card-body mx-auto" style="max-width: 1000px;">                
                    <form method="GET" action="">
                        <div class="row">
                            <div class="col-md-4">
                              <div class="form-floating">
                                <select class="form-select" name="departamento_id" id="departamentoSelect" onchange="cargarMunicipios()">
                                    <option value="">Seleccione un departamento</option>
                                    <?php foreach ($optionDep as $dep): ?>
                                        <option value="<?= $dep['id']; ?>" 
                                            <?= $dep['id'] == $selectedDepartamento ? 'selected' : ''; ?>>
                                            <?= $dep['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label >Departamento</label>
                              </div>
                            </div>

                            <div class="col-md-4">
                              <div class="form-floating">
                                <select class="form-select" name="municipio_id" id="municipioSelect">
                                    <option value="">Seleccione un municipio</option>
                                    <?php if (!empty($defaultMunicipios) && $selectedDepartamento == $putumayoId): ?>
                                        <?php foreach ($defaultMunicipios as $municipio): ?>
                                            <option value="<?= $municipio['codigo_muncipio']; ?>"
                                                <?= $municipio['codigo_muncipio'] == $selectedMunicipio ? 'selected' : ''; ?>>
                                                <?= $municipio['municipio']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label >Municipio</label>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-floating">
                                <select class="form-select" name="factor_id">
                                    <option value="">Seleccione un factor</option>
                                    <?php foreach ($optionFactores as $factor): ?>
                                        <option value="<?= $factor['id']; ?>" 
                                            <?= isset($filtros['factor_id']) && $filtros['factor_id'] == $factor['id'] ? 'selected' : ''; ?>>
                                            <?= $factor['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label >Factor</label>
                              </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="form-floating">
                                    <input class="form-control datetimepicker flatpickr-input" 
                                        name="fecha_inicial" 
                                        id="fecha_inicial" 
                                        type="date" 
                                        placeholder="dd/mm/yyyy" 
                                        value="<?= htmlspecialchars($_GET['fecha_inicial'] ?? ''); ?>"
                                        data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}'
                                        readonly="readonly">
                                    <label for="fecha_inicial">Fecha Inicial</label>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="form-floating">
                                    <input class="form-control datetimepicker flatpickr-input" 
                                        name="fecha_final" 
                                        id="fecha_final" 
                                        type="text" 
                                        placeholder="dd/mm/yyyy" 
                                        value="<?= htmlspecialchars($_GET['fecha_final'] ?? ''); ?>"
                                        data-date-format="d/m/Y"
                                        data-disable-mobile="true"
                                        readonly="readonly">
                                    <label for="fecha_final">Fecha Final</label>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3 text-center">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </form>

                    <div class="card mt-4">
                        <div class="card-header text-center">
                            <h4>
                                <?= (!empty($recordsAntes) && !empty($recordsAntes[0]['municipio'])) 
                                    ? htmlspecialchars($recordsAntes[0]['municipio']) . " - Antes" 
                                    : "Municipio No Definido - Antes"; ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recordsAntes)): ?>
                                <div id="carouselAntes" class="carousel slide mx-auto" style="max-width: 600px;" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($recordsAntes as $index => $record): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                                <div class="text-center">
                                                    <p><strong>Vereda:</strong> <?= htmlspecialchars($record['vereda'] ?? 'No definida'); ?></p>
                                                    <p><strong>Fecha de Creación:</strong> <?= htmlspecialchars($record['dtcreate']); ?></p>
                                                    <!-- Cuadro de imágenes -->
                                                    <div class="d-flex flex-wrap justify-content-center gap-2 mx-auto" style="width: 200px; height: 200px; overflow: hidden;">
                                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                                            <?php if (!empty($record["foto$i"])): ?>
                                                                <div style="width: 48%; height: 48%;">
                                                                    <img src="<?= htmlspecialchars($record["foto$i"]); ?>" 
                                                                        alt="Foto <?= $i; ?>" 
                                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <p class="mt-3"><strong>Observaciones:</strong> <?= htmlspecialchars($record['observaciones']); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <!-- Controles del carrusel -->
                                    <button class="carousel-control-prev custom-carousel-control" type="button" data-bs-target="#carouselAntes" data-bs-slide="prev">
                                        <div class="control-box">
                                            <span>&larr;</span>
                                            <p>Anterior</p>
                                        </div>
                                    </button>
                                    <button class="carousel-control-next custom-carousel-control" type="button" data-bs-target="#carouselAntes" data-bs-slide="next">
                                        <div class="control-box">
                                            <span>&rarr;</span>
                                            <p>Siguiente</p>
                                        </div>
                                    </button>
                                </div>
                            <?php else: ?>
                                <p>No se encontraron imágenes de antes.</p>
                            <?php endif; ?>
                        </div>
                    </div>


<!-- Estilos personalizados -->
<style>
    /* Botones del carrusel personalizados */
    .custom-carousel-control {
        background-color: transparent !important; 
        border: none !important; 
        width: auto; 
        height: auto; 
    }

    .custom-carousel-control .control-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border: 1px solid black;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.8); 
        color: black;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }

    .custom-carousel-control .control-box:hover {
        background-color: rgba(0, 0, 0, 0.1); 
    }

    .custom-carousel-control .control-box span {
        font-size: 18px; 
    }

    .custom-carousel-control .control-box p {
        margin: 0;
        font-size: 12px;
    }
</style>



                
                
                    <div class="card mt-4">
                        <div class="card-header text-center">
                            <h4>
                                <?= (!empty($recordsDespues) && !empty($recordsDespues[0]['municipio'])) 
                                    ? htmlspecialchars($recordsDespues[0]['municipio']) . " - Después" 
                                    : "Municipio No Definido - Después"; ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recordsDespues)): ?>
                                <div id="carouselDespues" class="carousel slide mx-auto" style="max-width: 600px;" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($recordsDespues as $index => $record): ?>
                                            <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                                                <div class="text-center">
                                                    <p><strong>Fecha de Actualización:</strong> <?= htmlspecialchars($record['fecha_actualizacion'] ?? 'No disponible'); ?></p>
                                                    <!-- Contenedor para centrar imágenes -->
                                                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-2 mx-auto" style="width: 220px; height: 220px; overflow: hidden; position: relative;">
                                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                                            <?php if (!empty($record["foto_actualizada_$i"])): ?>
                                                                <div style="width: 48%; height: 48%;">
                                                                    <img src="<?= htmlspecialchars($record["foto_actualizada_$i"]); ?>" 
                                                                        alt="Foto Actualizada <?= $i; ?>" 
                                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <!-- Observaciones debajo de las imágenes -->
                                                    <p class="mt-3"><strong>Observaciones:</strong> <?= htmlspecialchars($record['observaciones_actualizacion'] ?? 'Sin observaciones'); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <!-- Controles del carrusel -->
                                    <button class="carousel-control-prev custom-carousel-control" type="button" data-bs-target="#carouselDespues" data-bs-slide="prev">
                                        <div class="control-box">
                                            <span>&larr;</span>
                                            <p>Anterior</p>
                                        </div>
                                    </button>
                                    <button class="carousel-control-next custom-carousel-control" type="button" data-bs-target="#carouselDespues" data-bs-slide="next">
                                        <div class="control-box">
                                            <span>&rarr;</span>
                                            <p>Siguiente</p>
                                        </div>
                                    </button>
                                </div>
                            <?php else: ?>
                                <p>No se encontraron imágenes de después.</p>
                            <?php endif; ?>
                        </div>
                    </div>



<!-- Estilos personalizados -->
<style>
    /* Botones del carrusel personalizados */
    .custom-carousel-control {
        background-color: transparent !important; 
        border: none !important;
        width: auto; 
        height: auto;
    }

    .custom-carousel-control .control-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border: 1px solid black;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.8);
        color: black;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }

    .custom-carousel-control .control-box:hover {
        background-color: rgba(0, 0, 0, 0.1); 
    }

    .custom-carousel-control .control-box span {
        font-size: 18px; 
    }

    .custom-carousel-control .control-box p {
        margin: 0;
        font-size: 12px; 
    }
</style>
    <script>
        function cargarMunicipios() {
            const departamentoId = document.getElementById('departamentoSelect').value;
            const municipioSelect = document.getElementById('municipioSelect');
            municipioSelect.innerHTML = '<option value="">Cargando...</option>';

            fetch(`getMunicipios.php?departamento_id=${departamentoId}`)
                .then(response => response.json())
                .then(data => {
                    municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                    if (data.output.valid) {
                        data.output.response.forEach(municipio => {
                            municipioSelect.innerHTML += `<option value="${municipio.id}">${municipio.municipio}</option>`;
                        });
                    } else {
                        municipioSelect.innerHTML = '<option value="">No se encontraron municipios</option>';
                    }
                })
                .catch(() => {
                    municipioSelect.innerHTML = '<option value="">Error al cargar municipios</option>';
                });
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const carouselElement = document.querySelector("#carouselAntes");
            if (carouselElement) {
                const carousel = new bootstrap.Carousel(carouselElement, {
                    interval: 5000,
                    ride: false
                });

                document.querySelector(".carousel-control-prev").addEventListener("click", () => {
                    carousel.prev();
                });
                document.querySelector(".carousel-control-next").addEventListener("click", () => {
                    carousel.next();
                });
            }
        });
    </script>
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/imagenes.js"></script>

    <?php include 'admin/include/scriptsgober360.php'; ?>
    <script src="vendors/flatpickr/flatpickr.min.js"></script>

</body>
</html>