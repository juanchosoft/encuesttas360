<?php

include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/VisitasgAspas.php';
include './admin/classes/Linea.php';
include './admin/classes/Estrategia.php';

if (!empty($_POST['reporte']) && isset($_POST['reporte']) && $_POST['reporte'] > 0) {
    $rqst = array('id' => intval($_POST['reporte']));
    $arr = VisitasgAspas::getAll($rqst);

    $isvalid = $arr['output']['valid'];
    $data = $arr['output']['response'];
    if (!empty($data)) {
        // Información del cliente y usuario
        $data = $data[0];
        $id = $data['id'] ?? '';
        $dtcreate = $data['created_at'] ?? '';
        $date = $data['date'] ?? '';
        $item = $data['item'] ?? '';
        $provincia = $data['provincia'] ?? '';
        $poblacion = $data['poblacion'] ?? '';
        $departamento = $data['departamento'] ?? '';
        $municipio = $data['municipio'] ?? '';
        $desc_actividad = $data['desc_actividad'] ?? '';
        $estrategia = $data['estrategia'] ?? '';
        $linea = $data['linea'] ?? '';
        $linea = $data['linea_nombre'] ?? '';
        $estrategia = $data['estrategia_nombre'] ?? '';
        $campana = $data['campana'] ?? '';
        $link = $data['link'] ?? '';
        $foto1 = $data['foto1'] ?? null;
        $foto2 = $data['foto2'] ?? null;
        $foto3 = $data['foto3'] ?? null;
        $foto4 = $data['foto4'] ?? null;
    } else {
        echo "<script>
            alert('Sin resultados');
            window.location = 'cuadro_control_visitasaspas.php';
        </script>";
        return;
    }
} else {
    echo "<script>
        alert('Debes enviar un reporte válido para generar el documento');
        window.location = 'cuadro_control_visitasaspas.php';
    </script>";
    return;
}
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.min.js"></script>
<!-- DataTables Select -->
<script src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/select/2.0.0/js/select.bootstrap4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
  <body>
    <main class="main" id="top">
    <?php include 'admin/include/scriptsgober360.php'; ?>
    <?php
    include './admin/include/navbar.php';
    ?>
        <?php
    include './admin/include/header.php';
    ?>
    <div class="content">
        <div class="container-fluid">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- [ breadcrumb ] start -->
                            <div class="page-header">
                                <div class="page-block">
                                    <div class="row align-items-center">
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- [ breadcrumb ] end -->
                            <!-- [ Main Content ] start -->
                           <style>
                                .acta-formal {
                                    font-family: 'Georgia', serif;
                                    font-size: 15px;
                                    line-height: 1.6;
                                    color: #222;
                                    background-color: #fff;
                                    padding: 40px;
                                    border: 1px solid #ccc;
                                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                }

                                .acta-formal h2, .acta-formal h3 {
                                    text-align: center;
                                    margin-bottom: 10px;
                                }

                                .acta-formal h2 {
                                    font-size: 20px;
                                    font-weight: bold;
                                    border-bottom: 2px solid #444;
                                    padding-bottom: 5px;
                                    margin-bottom: 30px;
                                    text-transform: uppercase;
                                }

                                .acta-formal .info-header {
                                    display: flex;
                                    justify-content: space-between;
                                    flex-wrap: wrap;
                                    margin-bottom: 20px;
                                }

                                .acta-formal .info-header .left,
                                .acta-formal .info-header .right {
                                    width: 48%;
                                }

                                .acta-formal .info-header p {
                                    margin: 4px 0;
                                }

                                .bloque-datos p {
                                    margin: 5px 0;
                                    text-align: justify;
                                }

                                .registroFotografico {
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    gap: 25px;
                                    margin-top: 30px;
                                }

                                .registroFotografico img {
                                    width: 100%;
                                    max-width: 650px;
                                    aspect-ratio: 1 / 1;
                                    object-fit: cover;
                                    border: 2px solid #444;
                                    border-radius: 5px;
                                }

                                .encabezado-acta {
                                    text-align: center;
                                    margin-bottom: 30px;
                                }

                                .encabezado-acta img {
                                    max-width: 200px;
                                    margin-bottom: 10px;
                                }

                                .tabla-visita {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-top: 20px;
                                    margin-bottom: 30px;
                                }

                                .tabla-visita th, .tabla-visita td {
                                    border: 1px solid #aaa;
                                    padding: 8px 12px;
                                    text-align: center;
                                }

                                .tabla-visita th {
                                    background-color: #f5f5f5;
                                    font-weight: bold;
                                }

                                .separador {
                                    border-top: 1px solid #999;
                                    margin: 30px 0;
                                }
                            </style>

                            <div class="acta-formal">
                                <div class="encabezado-acta">
                                    <img src="assets/img/logosf.png" alt="Logo Gobernación">
                                    <p><strong>REPÚBLICA DE COLOMBIA</strong></p>
                                    <p><strong>DEPARTAMENTO DE SANTANDER</strong></p>
                                    <p>GOBERNACIÓN DE SANTANDER</p>
                                </div>

                                <h2>ACTA DE GESTIÓN ASPAS N° <?php echo htmlspecialchars($id); ?></h2>

                                <div class="info-header">
                                    <div class="left">
                                        <p><strong>Pág.:</strong> 1 de 1</p>
                                        <p><strong>Código:</strong> 005</p>
                                        <p><strong>Versión:</strong> 7</p>
                                    </div>
                                    <div class="right">
                                    <div><strong>Fecha de creación:</strong>  <?php echo htmlspecialchars($date); ?></div>
                                    <div><?php echo htmlspecialchars($dtcreate); ?></div>
                                    <div><strong>Fecha de visita:</strong> <?php echo htmlspecialchars($date); ?></div>
                                    </div>
                                </div>

                                <table class="tabla-visita">
                                    <thead>
                                        <tr>
                                            <th>Fecha visita</th>
                                            <th>Provincia</th>
                                            <th>Municipio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo htmlspecialchars($date); ?></td>
                                            <td><?php echo htmlspecialchars($provincia); ?></td>
                                            <td><?php echo htmlspecialchars($municipio); ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="bloque-datos">
                                    <p><strong>Línea:</strong> <?php echo htmlspecialchars($linea); ?></p>
                                    <p><strong>Nombre de campaña:</strong> <?php echo htmlspecialchars($campana); ?></p>
                                    <p><strong>Estrategia:</strong> <?php echo htmlspecialchars($estrategia); ?></p>
                                    <p><strong>Población impactada:</strong> <?php echo htmlspecialchars($poblacion); ?></p>
                                    <p><strong>Link relacionado:</strong> <a href="<?php echo htmlspecialchars($link); ?>" target="_blank"><?php echo htmlspecialchars($link); ?></a></p>
                                    <p><strong>Detalle de la actividad:</strong><br><?php echo nl2br(htmlspecialchars($desc_actividad)); ?></p>
                                </div>

                                <div class="separador"></div>

                                <h3>Registro Fotográfico</h3>
                                <div class="registroFotografico">
                                    <?php if (!empty($foto1)): ?>
                                        <img src="<?php echo htmlspecialchars($foto1); ?>" alt="Foto 1">
                                    <?php endif; ?>
                                    <?php if (!empty($foto2)): ?>
                                        <img src="<?php echo htmlspecialchars($foto2); ?>" alt="Foto 2">
                                    <?php endif; ?>
                                    <?php if (!empty($foto3)): ?>
                                        <img src="<?php echo htmlspecialchars($foto3); ?>" alt="Foto 3">
                                    <?php endif; ?>
                                    <?php if (!empty($foto4)): ?>
                                        <img src="<?php echo htmlspecialchars($foto4); ?>" alt="Foto 4">
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'admin/include/gerenic_script.php'; ?>

    <!-- Required Js -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/pcoded.min.js"></script>

    <!-- prism Js -->
    <script src="assets/js/plugins/prism.js"></script>
    <script src="assets/js/plugins/apexcharts.min.js"></script>

    <script src="admin/js/gestora_social.js"></script>

</body>

</html>