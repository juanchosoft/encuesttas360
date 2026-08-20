<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/include/generic_info_configuracion.php';

// Permisos
$view = SessionData::getPermission(1);
if (!$view) {
  require 'permiso_denegado.php';
}
$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if (!$isAdmin) {
  require 'permiso_denegado.php';
}
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
  <?php include './admin/include/navbar.php'; ?>
  <!-- [ navigation menu ] end -->

  <!-- [ Header ] start -->
  <?php include './admin/include/header.php'; ?>
  <!-- [ Header ] end -->

  <div class="content">
    <div class="col-11 col-xl-11 mx-auto">
        <div class="card-body">
        <table class="tabla_grilla" > 
            <thead class="table-header">
                <tr>
                    <th>CANDIDATOS</th>
                    <th>CONOCE AL CANDIDATO</th>
                    <th>IMAGEN FAVORABLE O DESFAVORABLE</th>
                    <th>VOTARÍA POR ÉL Ó ELLA?</th>
                </tr>
            </thead>
            <tbody> 
                <!-- Fila 1 --> 
                <tr> 
                    <td>
                        <img src="assets/img/candidato.png" alt="Foto candidato">Nombre Y Apellido
                    </td> 
                    <td>
                        <div class="toggle"> 
                            <button class="toggle-btn si active">
                                <i class="fas fa-check"></i>
                            </button> 
                            <button class="toggle-btn no">
                                <i class="fas fa-times"></i>
                            </button> 
                        </div> 
                    </td>
                    <td> 
                        <div class="toggle"> 
                            <button class="toggle-btn si active">SÍ</button>
                            <button class="toggle-btn no">NO</button> 
                        </div>
                    </td> 
                    <td> 
                        <div class="toggle"> 
                            <button class="toggle-btn si active">SÍ</button> 
                            <button class="toggle-btn no">NO</button> 
                        </div> 
                    </td> 
                </tr> 
                <!-- Fila 2 --> 
                <tr> 
                    <td>
                        <img src="assets/img/candidato2.png" alt="Foto candidato">Nombre Y Apellido
                    </td> 
                    <td>
                        <div class="toggle"> 
                            <button class="toggle-btn si active">
                                <i class="fas fa-check"></i>
                            </button> 
                            <button class="toggle-btn no">
                                <i class="fas fa-times"></i>
                            </button> 
                        </div> 
                    </td>
                    <td> 
                        <div class="toggle"> 
                            <button class="toggle-btn si active">SÍ</button>
                            <button class="toggle-btn no">NO</button> 
                        </div>
                    </td> 
                    <td> 
                        <div class="toggle">
                            <button class="toggle-btn si active">SÍ</button>
                            <button class="toggle-btn no">NO</button>
                        </div> 
                    </td>
                </tr> 
                <!-- Fila 3 -->
                <tr> 
                    <td>
                        <img src="assets/img/candidato.png" alt="Foto candidato">Nombre Y Apellido
                    </td>
                    <td>
                        <div class="toggle"> 
                            <button class="toggle-btn si active">
                                <i class="fas fa-check"></i>
                            </button> 
                            <button class="toggle-btn no">
                                <i class="fas fa-times"></i>
                            </button> 
                        </div> 
                    </td>
                    <td class="no-aplica">NO APLICA</td>
                    <td class="no-aplica">NO APLICA</td> 
                </tr> 
                <!-- Fila 4 -->
                <tr>
                    <td>
                        <img src="assets/img/candidato.png" alt="Foto candidato">Nombre Y Apellido
                    </td>
                    <td>
                        <div class="toggle"> 
                            <button class="toggle-btn si active">
                                <i class="fas fa-check"></i>
                            </button> 
                            <button class="toggle-btn no">
                                <i class="fas fa-times"></i>
                            </button> 
                        </div> 
                    </td>
                    <td> 
                        <div class="toggle">
                            <button class="toggle-btn si">SÍ</button>
                            <button class="toggle-btn no active">NO</button> 
                        </div> 
                    </td> 
                    <td> 
                        <div class="toggle">
                            <button class="toggle-btn si">SÍ</button>
                            <button class="toggle-btn no active">NO</button>
                        </div> 
                    </td> 
                </tr>
                <!-- Fila 5 -->
                <tr> 
                    <td>
                        <img src="assets/img/candidato2.png" alt="Foto candidato">Nombre Y Apellido
                    </td>
                    <td>
                        <div class="toggle"> 
                            <button class="toggle-btn si active">
                                <i class="fas fa-check"></i>
                            </button> 
                            <button class="toggle-btn no">
                                <i class="fas fa-times"></i>
                            </button> 
                        </div> 
                    </td> 
                    <td class="no-aplica">NO APLICA</td>
                    <td class="no-aplica">NO APLICA</td> 
                </tr> 
            </tbody> 
        </table>
          <script>
            document.querySelectorAll('.toggle').forEach(group => {
              const buttons = group.querySelectorAll('.toggle-btn');
              buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                  buttons.forEach(b => b.classList.remove('active'));
                  btn.classList.add('active');
                });
              });
            });
          </script>

    </div>

    <?php include './admin/include/footer.php'; ?>
  </div>

  <?php include './admin/include/gerenic_script.php'; ?>
   <?php include 'admin/include/scriptsgober360.php'; ?>
  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
</body>
</html>