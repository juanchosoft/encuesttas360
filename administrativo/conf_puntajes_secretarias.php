<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Secretarias.php';
include './admin/classes/ConfiguracionPuntajeSecretaria.php';

// Permisos
$view = SessionData::getPermission(80);
$create = SessionData::getPermission(81);
$edit = SessionData::getPermission(82);
if (!$view) {
    require 'permiso_denegado.php';
}
// Información de Factores
$arr = ConfiguracionPuntajeSecretaria::getAll(null);
$isvalid = $arr['output']['valid'];
$arr = $arr['output']['response'];


//Información de Ejes
$arrSecretaria = Secretarias::getAll(null);
$isvalidEje = $arrSecretaria['output']['valid'];
$arrSecretaria = $arrSecretaria['output']['response'];
$optionSecretarias = '<option value="seleccione">Seleccione...</option>';
foreach ($arrSecretaria as $val) {
    $optionSecretarias .= "<option value='" . $val['id'] . "'>" . $val['secretaria'] . "</option>";
}
$modulo = 'Configuracion Puntajes';
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
    <!-- [ Header ] end -->
    <style>
        /* Estilos del select */
        select {
            padding: 10px;
            font-size: 16px;
        }

        .color-option {
            display: flex;
            align-items: center;
        }

        .color-box {
            width: 280px;
            height: 40px;
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            border: 1px solid #fff;
        }
    </style>
      <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                    <h4 class="text-body mb-0 d-flex align-items-center" >
                    <i class="fas fa-list-ol me-2" style="color: #3e465b !important;font-size: 1.3rem !important;"></i>Configuración Puntajes Secretaría</h4>
                  </div>
                </div>
              </div> 
              <div class="card-body p-0">
                <div class="p-4 code-to-copy">

                  <form class="row g-3 mb-6" id="formupuntajes" role="form" autocomplete="false">
                  <input type="hidden" name="op" id="op" />
                  <input type="hidden" name="idPuntaje" id="idPuntaje" />
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="secretariaId" name="secretariaId">
                        <?php echo $optionSecretarias; ?>
                        </select>
                        <label for="Secretaria">Secretaria<span class="text-danger">*</span></label>
                      </div>
                    </div>


                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select" id="tipo_medicion" name="tipo_medicion">
                            <option selected>Seleccione</option>
                            <option value="Cantidad">Cantidad</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                            <option value="Creación">Creación</option>
                        </select>
                        <label  for="">Tipo Medición<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-floating">
                      <input type="text" class="form-control" id="desde" name="desde"
                      onKeyPress="return soloNumeros(event);" placeholder="" value="" required>
                        <label for="">Desde<span class="text-danger">*</span></label>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-floating">
                      <input type="text" class="form-control" id="hasta" name="hasta"
                      onKeyPress="return soloNumeros(event);" placeholder="" value="" required>
                      <label for="">Hasta<span class="text-danger">*</span></label>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                      <div class="form-floating">
                        <select class="form-select"  id="color" name="color" onchange="updateColorBox()">
                            <option selected>Seleccione</option>
                            <option value="#cd162c">Rojo</option>
                            <option value="#cd7d16">Naranja</option>
                            <option value="#dbd509">Amarillo</option>
                            <option value="#2774f1">Azul</option>
                            <option value="#62af0a">Verde</option>
                            <option value="#f4a460">Marrón Arena</option>
                            <option value="#ff4500">Rojo Naranja</option>
                            <option value="#8b4513">Marrón Oscuro</option>
                            <option value="#6a5acd">Azul Pizarra</option>
                            <option value="#2e8b57">Verde Marino</option>
                            <option value="#ff6347">Tomate</option>
                            <option value="#4682b4">Azul Acero</option>
                            <option value="#00ced1">Turquesa Oscuro</option>
                            <option value="#9400d3">Violeta Oscuro</option>
                            <option value="#dda0dd">Ciruela</option>
                            <option value="#ff1493">Rosa Intenso</option>
                            <option value="#00ff7f">Verde Primavera</option>
                            <option value="#adff2f">Verde Amarillo</option>
                            <option value="#40e0d0">Turquesa</option>
                            <option value="#6495ed">Azul Aciano</option>
                            <option value="#ff69b4">Rosa Fucsia</option>
                            <option value="#7fffd4">Aguamarina</option>
                            <option value="#1e90ff">Azul Dodger</option>
                            <option value="#9932cc">Orquídea Oscura</option>
                            <option value="#dc143c">Carmesí</option>
                            <option value="#b8860b">Dorado Oscuro</option>
                            <option value="#7cfc00">Verde Césped</option>
                            <option value="#ffb6c1">Rosa Claro</option>
                            <option value="#ffa07a">Salmón Claro</option>

                            <!-- Tonos cálidos -->
                            <option value="#e74c3c">Rojo Coral</option>
                            <option value="#f39c12">Naranja Brillante</option>
                            <option value="#e67e22">Mandarina</option>
                            <option value="#d35400">Naranja Intenso</option>
                            <option value="#c0392b">Rojo Granate</option>

                            <!-- Tonos fríos -->
                            <option value="#2980b9">Azul Royal</option>
                            <option value="#3498db">Celeste</option>
                            <option value="#1abc9c">Turquesa Vivo</option>
                            <option value="#16a085">Verde Azulado</option>
                            <option value="#2c3e50">Azul Medianoche</option>

                            <!-- Tonos neutros -->
                            <option value="#95a5a6">Gris Claro</option>
                            <option value="#7f8c8d">Gris Oscuro</option>
                            <option value="#bdc3c7">Plata</option>
                            <option value="#ecf0f1">Blanco Humo</option>
                            <option value="#34495e">Gris Pizarra</option>

                            <!-- Tonos pastel -->
                            <option value="#f5b7b1">Rosa Pastel</option>
                            <option value="#fad7a0">Durazno Claro</option>
                            <option value="#a3e4d7">Aguamarina Suave</option>
                            <option value="#d2b4de">Lavanda</option>
                            <option value="#fcf3cf">Amarillo Pastel</option>

                            <!-- Tonos vibrantes -->
                            <option value="#9b59b6">Púrpura Vivo</option>
                            <option value="#8e44ad">Morado Intenso</option>
                            <option value="#27ae60">Verde Esmeralda</option>
                            <option value="#f4d03f">Amarillo Dorado</option>
                            <option value="#e84393">Fucsia Brillante</option>

                            <!-- Tonos oscuros -->
                            <option value="#1a5276">Azul Marino Oscuro</option>
                            <option value="#145a32">Verde Bosque</option>
                            <option value="#641e16">Vino Oscuro</option>
                            <option value="#512e5f">Morado Profundo</option>
                            <option value="#3d3d3d">Negro Grisáceo</option>

                            <!-- Tonos metálicos -->
                            <option value="#d4ac0d">Oro</option>
                            <option value="#b87333">Cobre</option>
                            <option value="#aab7b8">Platino</option>
                            <option value="#566573">Pizarra Metálico</option>
                            <option value="#d5dbdb">Aluminio</option>
                        </select>
                        <label for="">Color<span class="text-danger">*</span></label>
                      </div>
                    </div>
                   
                      <div class="row g-3 justify-content-end">
                        <div class="col-auto">
                          <button type="button" onclick="UTIL.clearForm('formusuarios');" class="btn btn-phoenix-secondary px-5">Cancelar</button>
                        </div>
                        <?php if ($create && $edit): ?>
                        <div class="col-auto">
                          <button class="btn btn-primary px-5" type="button" onclick="PUNTAJES.save();">Guardar</button>
                        </div>
                        <?php endif; ?>
                      </div>
                    
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>

<!-- TABLA DE USUARIOS -->
        <div class="p-4 code-to-copy">      
           <div class="table-responsive">
                          <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0">
                            <thead>
                            <tr>
                                <th>Editar</th>
                                <th>Item</th>
                                <th>Secretaría</th>
                                <th>Tipo medición</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Color</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                            <?php if ($isvalid && count($arr) > 0): ?>
                            <?php foreach ($arr as $item): ?>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                        onclick="PUNTAJES.edit(<?= htmlspecialchars($item['id']) ?>)">
                                        <i class="uil uil-edit"></i>
                                    </button>
                                </td>
                                <td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?> </td>
                                <td><?php echo htmlspecialchars($item['secretaria'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                </td>
                                <td><?php echo htmlspecialchars($item['tipo_medicion'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['rango_desde'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['rango_hasta'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td
                                    style="background-color: <?php echo htmlspecialchars($item['color'], ENT_QUOTES, 'UTF-8'); ?>;">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                          </table>
                        </div>
                    </div>

        <?php
        include './admin/include/footer.php';
        ?>
      </div>
    </main>
            <script>
                // Actualiza el cuadro de color y el texto según la opción seleccionada
                function updateColorBox() {
                    const select = document.getElementById('color');
                    const colorBox = document.getElementById('colorBox');
                    const colorText = document.getElementById('colorText');
                    // Obtén el color y el texto seleccionados
                    const selectedOption = select.options[select.selectedIndex];
                    const color = selectedOption.value;
                    const text = selectedOption.text;
                    // Aplica el color al cuadro y actualiza el texto
                    colorBox.style.backgroundColor = color;
                    // colorText.textContent = text;
                }
                // Establece el color inicial
                updateColorBox();
            </script>

            <!-- Warning Section Ends -->
            <?php include 'admin/include/gerenic_script.php'; ?>
            <!-- Required Js -->
            <script src="assets/js/vendor-all.min.js"></script>
            <script src="assets/js/plugins/bootstrap.min.js"></script>
            <script src="assets/js/pcoded.min.js"></script>
            <?php include './admin/include/generic_dataTables.php'; ?>
            <script type="text/javascript" src="admin/js/conf_puntajes_secretaria.js"></script>
            <?php include 'admin/include/scriptsgober360.php'; ?>
</body>

</html>