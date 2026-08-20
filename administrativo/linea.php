<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Linea.php';

// Permisos
$view = SessionData::getPermission(80);
$create = SessionData::getPermission(81);
$edit = SessionData::getPermission(82);
if (!$view) {
    require 'permiso_denegado.php';
}
$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if(!$isAdmin){
    require 'permiso_denegado.php';
}

//Información de Linea
$arr = Linea::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];
$modulo = 'Lineas';
?>

<body class="">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ navigation menu ] start -->
    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->
    <div class="content">
        <div>
            <div class="col-11 col-xl-11 mx-auto">
                <div class="card shadow-none border my-4" data-component-card="data-component-card">
                    <div class="card-header p-4 border-bottom bg-body">
                        <div class="row g-3 justify-content-between align-items-center">
                            <div class="col-12 col-md">
                                <h4 class="text-body mb-0 d-flex align-items-center">
                                    <i class="fas fa-file-lines me-2"
                                        style="color: #3e465b !important;font-size: 1.3rem !important;"></i> Formulario
                                    linea
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body m-4">
                        <form id="formulinea" role="form" autocomplete="false">
                            <input type="hidden" name="op" id="op" />
                            <input type="hidden" name="id" id="id" />
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            placeholder="Ingrese nombres" required>
                                        <label for="nombre">Nombre Línea<span class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control"
                                            placeholder="Ingrese el nombre de los secretarios" id="descripcion"
                                            name="descripcion" style="height: 50px"></textarea>
                                        <label for="descripcion">Descripción<span
                                                class="text-danger mb-1">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-3 justify-content-end">
                                    <div class="col-auto">
                                        <button type="button" onclick="UTIL.clearForm('formulinea');"
                                            class="btn btn-phoenix-secondary px-5">Cancelar</button>
                                    </div>
                                    <?php if ($create && $edit): ?>
                                    <div class="col-auto">
                                        <button class="btn btn-primary px-5" type="button"
                                            onclick="LINEA.savedata();">Guardar</button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
                <div class="contenedor  mb-5">
                    <div class="contenido">
                        <div class="card">
                            <h5 class="card-header" style="color: #37474f; font-size: 16px; text-align: center;">Listado
                                Linea</h5>
                            <div class="card-body table-border-style mb-4">
                                <!-- Tabla de datos -->
                                <div class="table-responsive">
                                    <table id="dynamictable" class="table table-striped mb-0">
                                        <thead>
                                            <tr class="border-1">
                                                <th>Editar</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($isvalid && !empty($arr)): ?>
                                            <?php foreach ($arr as $item): ?>
                                            <?php
                                                    $id = htmlspecialchars($item['id'] ?? '');
                                                    $nombre = htmlspecialchars($item['nombre'] ?? '');
                                                    $descripcion = htmlspecialchars($item['descripcion'] ?? '');
                                                    ?>
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                                        onclick="LINEA.editData(<?= htmlspecialchars($item['id']) ?>)">
                                                        <i class="uil uil-edit"></i>
                                                    </button>
                                                </td>
                                                <td><?= $nombre ?></td>
                                                <td><?= $descripcion ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include './admin/include/footer.php'; ?>
        <!-- Warning Section Ends -->
        <?php include 'admin/include/gerenic_script.php'; ?>
        <!-- Required Js -->
        <script src="assets/js/vendor-all.min.js"></script>
        <script src="assets/js/plugins/bootstrap.min.js"></script>
        <script src="assets/js/pcoded.min.js"></script>
        <script type="text/javascript" src="admin/js/linea.js"></script>
        <?php include './admin/include/generic_dataTables.php'; ?>
        <?php include 'admin/include/scriptsgober360.php'; ?>

</body>

</html>