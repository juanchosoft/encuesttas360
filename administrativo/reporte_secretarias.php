<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Proyectos.php';

if (!empty($_GET['reporte']) && isset($_GET['reporte']) && $_GET['reporte'] > 0) {
  $rqst = array('id' => $_GET['reporte']);
  $arr = Proyectos::getAll($rqst);

  $isvalid = $arr['output']['valid'];
  $data = $arr['output']['response'];

  if (count($data) > 0) {

    // Información del cliente y usuario
    $data = $data[0];
    $id = $data['id'] ? $data['id'] : '';
    $dtcreate = $data['created_at'] ? $data['created_at'] : '';

    $date = isset($data['date']) ? ($data['date']) : '';
    $proyecto =  isset($data['proyecto']) ? ($data['proyecto']) : '';
    $provincia =  isset($data['provincia']) ? ($data['provincia']) : '';
    $secretaria = isset($data['secretaria']) ? ($data['secretaria']) : '';
    $departamento = isset($data['departamento']) ? ($data['departamento']) : '';
    $municipio = isset($data['municipio']) ? ($data['municipio']) : '';
    $valor_proyecto = isset($data['valor_proyecto']) ? ($data['valor_proyecto']) : '';
    $porcentaje_ejecucion = isset($data['porcentaje_ejecucion']) ? ($data['porcentaje_ejecucion']) : '';
    $estado = isset($data['estado']) ? ($data['estado']) : '';
    $observaciones = isset($data['observaciones']) ? ($data['observaciones']) : '';

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
        <?php
        include './admin/include/navbar.php';
        ?>
        <?php
        include './admin/include/header.php';
        ?>
    <div>    
        <div class="dashboard-main-wrapper">
            <div class="dashboard-wrapper">
                <div class="dashboard-ecommerce">
                    <div class="container-fluid dashboard-content ">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="page-header">
                                    
                                    <p class="pageheader-text"></p>
                                    <div class="page-breadcrumb">  
                                            <h2 style= "text-align: center;" class="pageheader-title">Reporte de Inversión Secretarias</h2>
                                            <!-- Bootstrap    <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Inicio</a></li>
                                                <li class="breadcrumb-item active" aria-current="page">Reportes</li>--> 
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-xl-2 col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-header p-4">
                                    <?php include 'admin/include/generinc_brand_logo.php'; ?>
                                    
                                        <div class="float-right"> <h3 class="mb-0">Detalle Proyecto NO <?php echo $id; ?></h3>
                                    </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-sm-6">
                                                <h5 class="mb-3"></h5>                                            
                                                <h3 class="text-dark mb-1"></h3>
                                            
                                                <div>REPUBLICA DE COLOMBIA</div>
                                                <div>DEPARTAMENTO DE SANTANDER</div>
                                                <div>GOBERNACIÓN DE SANTANDER</div>
                                            
                                            </div>
                                            <div class="col-sm-6">
                                                <h5 class="mb-3">   </h5>
                                                <h3 class="text-dark mb-1"> </h3>                                            
                                                <div><strong>Pág. 1</strong> de 1 </div>
                                                <div><strong>Código:</strong> </div>
                                                <div><strong>Versión:</strong> 7</div>
                                                <div><strong>Fecha de creación:</strong> <?php echo $date; ?> </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive-sm">
                                            <table class="table table-striped">
                                                <thead>
                                                        <th>Fecha visita</th>
                                                        <th class="right">Provincia</th>
                                                        <th class="center">Municipio</th>
                                                        <th>Secretaria o Entidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>                                              
                                                        <td class="left strong"><?php echo $date; ?></td>
                                                        <td class="left"><?php echo $provincia; ?></td>
                                                        <td class="right"><?php echo $municipio; ?></td>
                                                        <td class="right"><?php echo $secretaria; ?></td>
                                                    </tr>
                                                
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-5">
                                            </div>
                                            <div class="col-lg-4 col-sm-5 ml-auto">
                                                
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                    <p><strong>Valor proyecto: </strong><?php echo '$ ' . number_format( $valor_proyecto, 2, ',', '.'); ?></p>
                                    <P><strong>Proyecto:</strong> <?php echo $proyecto; ?></P>
                                    <P><strong>Estado:</strong> <?php echo $estado; ?></P>
                                    <P><strong>Porcentaje Ejecución:</strong> <?php echo $porcentaje_ejecucion; ?></P>
                                    <P><strong>Observaciones:</strong> <?php echo $observaciones; ?></P>
                                
                                    <?php if (!is_null($data["imagen"])): ?>                <P>
                        <strong>REGISTRO FOTOGRAFICO</strong>
                        <div class="registroFotografico">
                            <img src="assets/img/admin/usuarios<?php echo $data["imagen"] ?>" alt="" width="auto" height="auto">
                        </div>
                    </P>
                <?php endif ?>

            </div> 
        </div>
        <?php
        include './admin/include/footer.php';?>
    </div>

<!-- Bootstrap -->
 <!-- Required Js -->
<script src="assets/js/vendor-all.min.js"></script>
<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/pcoded.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.js"></script>
<script src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.js"></script>
<script src="https://cdn.datatables.net/select/2.0.0/js/select.bootstrap4.js"></script>
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<!-- Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.js"></script>
<!-- DataTables Select -->
<script src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.js"></script>
<script src="https://cdn.datatables.net/select/2.0.0/js/select.bootstrap4.js"></script>
           
<script type="text/javascript" src="admin/js/departamento.js"></script>
<script type="text/javascript" src="admin/js/detalle_visitas.js"></script>
<!--<script>
      setTimeout(function() {
        $("#tbl_departamento_id").val('68')
                    }, 500);

                    setTimeout(function() {
                        DEPARTAMENTO.getMunicipios();
                    }, 1000);
    APORTES.onchangePais();
</script>-->
<script>
    new DataTable('#example', {
    select: true})
</script>

</body>

</html>            