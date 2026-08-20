<?php

include './admin/include/head.php';
require './admin/include/generic_classes.php';

include './admin/classes/Visitas.php';


// Informacion del proyecto
$configuracionAplicacion = Util::getInformacionConfiguracion();
$nombreProyecto = '';
$logo = '';
if (!empty($configuracionAplicacion[0])) {
  $nombreProyecto = $configuracionAplicacion[0]['nombre_proyecto'] ?? '';
  $logo = $configuracionAplicacion[0]['logo'] ?? '';
}

if (!empty($_POST['reporte']) && isset($_POST['reporte']) && $_POST['reporte'] > 0) {
    $rqst = array('id' => $_POST['reporte']);
    $arr = Visitas::getAll($rqst);

    $isvalid = $arr['output']['valid'];
    $data = $arr['output']['response'];

    if (count($data) > 0) {

        // Información del cliente y usuario
        $data = $data[0];
        $id = $data['id'] ? $data['id'] : '';
        $dtcreate = $data['created_at'] ? $data['created_at'] : '';     
        $tipo =isset($data['tipo']) ? ($data['tipo']) : '';
        $date = isset($data['date']) ? ($data['date']) : '';
        $entidad =  isset($data['entidad']) ? ($data['entidad']) : '';
        $provincia =  isset($data['provincia']) ? ($data['provincia']) : '';
        $cargo = isset($data['cargo']) ? ($data['cargo']) : '';
        $departamento = isset($data['departamento']) ? ($data['departamento']) : '';
        $municipio = isset($data['municipio']) ? ($data['municipio']) : '';
        $vereda = isset($data['vereda']) ? ($data['vereda']) : '';
        $beneficiario = isset($data['beneficiario']) ? ($data['beneficiario']) : '';
        $responsable = isset($data['responsable']) ? ($data['responsable']) : '';
        $observaciones = isset($data['observaciones']) ? ($data['observaciones']) : '';
        $compromisos = isset($data['compromisos']) ? ($data['compromisos']) : '';

        $img = $data['img'] ?? null;
        $imgBasePath = "assets/img/admin/";
        $imgMostrar = !empty($data["img"]) ? $imgBasePath . htmlspecialchars($data["img"]) : 'dist/img/logorelsinf.png';
    } else {
?>
<script type='text/javascript'>
    alert('Sin resultados');
    window.location = 'detalle_visitas.php';
</script>
<?php
        return;
    }
} else { ?>
<script type='text/javascript'>
    alert('Debes enviar una reporte para generar el documento');
    window.location = 'detalle_visitas.php';
</script>
<?php
    return;
}
?>

<body class="">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=IBM+Plex+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Tomorrow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <body class="">

        <style>
            .acta-formal {
                font-family: "Roboto", sans-serif;
                font-optical-sizing: auto;
                font-weight: 300;
                font-style: normal;
                font-variation-settings: "wdth"100;
                font-size: 15px;
                line-height: 1.6;
                color: #222;
                background-color: #fff;
                padding: 40px;
                border: 1px solid #ccc;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .acta-formal h2,
            .acta-formal h3 {
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

            .tabla-visita th,
            .tabla-visita td {
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
                                    <h4 class="text-body mb-0 d-flex align-items-center"><i
                                            class="uil uil-clipboard-alt fs-6"></i>Reporte</h4>
                                </div>
                            </div>
                        </div>
                        <div class="content">
                            <div class="mt-4">
                                <div class="row g-4">
                                    <div class="col-11 col-xl-11 mx-auto">
                                        <div class="mb-9">
                                            <div class="card shadow-none border my-4"
                                                data-component-card="data-component-card">
                                                <div class="card-header p-4 border-bottom bg-body">
                                                    <div class="row g-3 justify-content-between align-items-center">
                                                        <div class="col-12 col-md">
                                                            <div class="acta-formal">
                                                                <div class="encabezado-acta">
                                                                    <?php if (!empty($logo)): ?>
                                                                    <img src="<?php echo htmlspecialchars($logo); ?>"
                                                                        alt="Logo" style="height: 90px;"
                                                                        class="img-fluid img-thumbnail">
                                                                    <?php endif; ?>
                                                                    <p><strong>REPÚBLICA DE COLOMBIA</strong></p>
                                                                    <p><strong>
                                                                            <?php echo htmlspecialchars($nombreProyecto); ?>
                                                                            </h2></strong></p>
                                                                </div>

                                                                <h2>ACTA DE VISITA GOBERNADOR N°
                                                                    <?php echo htmlspecialchars($id); ?></h2>

                                                                <div class="info-header">
                                                                    <div class="left">
                                                                        <p><strong>Pág.:</strong> 1 de 1</p>
                                                                        <p><strong>Código:</strong> 005</p>
                                                                        <p><strong>Versión:</strong> 7</p>
                                                                        <p><strong>Fecha de visita:</strong>
                                                                            <?php echo htmlspecialchars($date); ?></p>
                                                                    </div>
                                                                    <div class="right">

                                                                    </div>
                                                                </div>

                                                                <table class="tabla-visita">

                                                                    <thead>
                                                                        <tr>
                                                                            <th>Fecha visita</th>
                                                                            <th>Subregion</th>
                                                                            <th>Municipio</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                        <tr>
                                                                            <td><?php echo htmlspecialchars($date); ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($provincia); ?>
                                                                            </td>
                                                                            <td><?php echo htmlspecialchars($municipio); ?>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <div class="bloque-datos">
                                                                    <p><strong>Estado:</strong>
                                                                        <?php echo htmlspecialchars($beneficiario); ?>
                                                                    </p>
                                                                    <p><strong>Motivo Visita:</strong>
                                                                        <?php echo htmlspecialchars($tipo); ?></p>
                                                                    <p><strong>Motivo Visita:</strong>
                                                                        <?php echo htmlspecialchars($compromisos); ?>
                                                                    </p>

                                                                </div>

                                                                <h3>Registro Fotográfico</h3>
                                                                <div class="registroFotografico">
                                                                    <img src="<?php echo $imgMostrar; ?>"
                                                                        alt="Imagen líder" class="img-fluid">

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php include './admin/include/footer.php';?>
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

                        <script>
                            new DataTable('#example', {
                                select: true
                            });
                        </script>

                        <script>
                            setTimeout(function() {
                                $("#tbl_departamento_id").val('68')
                            }, 500);
                            setTimeout(function() {
                                DEPARTAMENTO.getMunicipios();
                            }, 1000);
                            APORTES.onchangePais();
                        </script>
                        <?php include 'admin/include/scriptsgober360.php'; ?>
    </body>

    </html>