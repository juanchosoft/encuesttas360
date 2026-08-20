<?php
require_once 'admin/include/session.php';
require_once 'admin/include/header.php';
require_once 'admin/include/menu.php';

// Verificar permisos de usuario (opcional)
// if (!in_array($_SESSION['usuario']['tipo'], ['Administrador', 'SuperAdministrador'])) {
//     header('Location: dashboard.php');
//     exit;
// }

// Obtener datos de auditoría
$db = new DbConection();
$pdo = $db->openConect();

$query = "SELECT a.*, u.nombre, u.apellido, e.tema as encuesta_tema 
          FROM tbl_auditoria_preguntas a 
          LEFT JOIN tbl_usuarios u ON a.tbl_usuario_id = u.id 
          LEFT JOIN tbl_encuestas e ON a.tbl_encuesta_id = e.id 
          ORDER BY a.fecha_subida DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConect();
?>

<div class="pc-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Auditoría de Preguntas</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item">Auditoría de Preguntas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Registro de Auditoría de Preguntas</h5>
                        <span class="d-block m-t-5">Historial de preguntas subidas por los usuarios</span>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tabla-auditoria">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Usuario</th>
                                        <th>Archivo</th>
                                        <th>Preguntas</th>
                                        <th>Opciones</th>
                                        <th>Fecha y Hora</th>
                                        <th>Estado</th>
                                        <th>Encuesta</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registros as $index => $registro): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($registro['nombre'] . ' ' . $registro['apellido']) ?></td>
                                        <td><?= htmlspecialchars($registro['archivo_nombre']) ?></td>
                                        <td><?= $registro['cantidad_preguntas'] ?></td>
                                        <td><?= $registro['cantidad_opciones'] ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($registro['fecha_subida'])) ?></td>
                                        <td>
                                            <?php if ($registro['estado'] == 'completado'): ?>
                                                <span class="badge bg-success">Completado</span>
                                            <?php elseif ($registro['estado'] == 'parcial'): ?>
                                                <span class="badge bg-warning">Parcial</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Error</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $registro['encuesta_tema'] ? htmlspecialchars($registro['encuesta_tema']) : '<em>No especificada</em>' ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detalleModal<?= $registro['id'] ?>">
                                                <i class="fas fa-info-circle"></i> Detalles
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal de Detalles -->
                                    <div class="modal fade" id="detalleModal<?= $registro['id'] ?>" tabindex="-1" aria-labelledby="detalleModalLabel<?= $registro['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detalleModalLabel<?= $registro['id'] ?>">Detalles de Auditoría</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Usuario:</strong> <?= htmlspecialchars($registro['nombre'] . ' ' . $registro['apellido']) ?></p>
                                                            <p><strong>Archivo:</strong> <?= htmlspecialchars($registro['archivo_nombre']) ?></p>
                                                            <p><strong>Preguntas subidas:</strong> <?= $registro['cantidad_preguntas'] ?></p>
                                                            <p><strong>Opciones subidas:</strong> <?= $registro['cantidad_opciones'] ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Fecha y hora:</strong> <?= date('d/m/Y H:i:s', strtotime($registro['fecha_subida'])) ?></p>
                                                            <p><strong>IP:</strong> <?= htmlspecialchars($registro['ip_address']) ?></p>
                                                            <p><strong>Navegador:</strong> <?= htmlspecialchars($registro['navegador']) ?></p>
                                                            <p><strong>Estado:</strong> 
                                                                <?php if ($registro['estado'] == 'completado'): ?>
                                                                    <span class="badge bg-success">Completado</span>
                                                                <?php elseif ($registro['estado'] == 'parcial'): ?>
                                                                    <span class="badge bg-warning">Parcial</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Error</span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if ($registro['mensaje_error']): ?>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="alert alert-danger">
                                                                <h6>Mensaje de error:</h6>
                                                                <p><?= nl2br(htmlspecialchars($registro['mensaje_error'])) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>

<!-- DataTables JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#tabla-auditoria').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "order": [[5, 'desc']], // Ordenar por fecha de subida (descendente)
            "pageLength": 10,
            "responsive": true
        });
    });
</script>

<?php
require_once 'admin/include/footer.php';
?>