<?php
include './admin/include/head.php';
require './admin/include/generic_classes.php';
include './admin/classes/Calendario.php';

// Permisos
$view = SessionData::getPermission(1);
$create = SessionData::getPermission(2);
$edit = SessionData::getPermission(3);
$permits = SessionData::getPermission(4);
if (!$view) {
    require 'permiso_denegado.php';
}
//Información de Secretarias
$arr = Calendario::getAll(null);
$isvalid = $arr['output']['valid'];
$eventosCalendario = $arr['output']['response'];
$modulo = 'Calendario';
?>
<script>
  const responseEventosCalendario = <?= json_encode($eventosCalendario) ?> ;
  console.log(responseEventosCalendario);
</script>

<body class="">

  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <?php
    include './admin/include/navbar.php';
    ?>

  <?php
    include './admin/include/header.php';
    ?>
  <div class="content">
    <div class="row g-0 mb-4 align-items-center">
      <div class="col-5 col-md-6">
        <h4 class="mb-0 text-body-emphasis fw-bold fs-md-6"><span
            class="calendar-day d-block d-md-inline mb-1"></span><span
            class="px-3 fw-thin text-body-quaternary d-none d-md-inline">|</span><span class="calendar-date"></span>
        </h4>
      </div>
      <div class="col-7 col-md-6 d-flex justify-content-end">
        <button class="btn btn-link text-body px-0 me-2 me-md-4"><span class="fa-solid fa-sync fs-10 me-2"></span><span
            class="d-none d-md-inline">Sincronizar</span></button>
        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addEventModal">
          <span class="fas fa-plus pe-2 fs-10"></span>Add nueva tarea</button>
      </div>
    </div>
    <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 border-y border-translucent">
      <div class="row py-3 gy-3 gx-0">
        <div class="col-6 col-md-4 order-1 d-flex align-items-center">
          <button class="btn btn-sm btn-phoenix-primary px-4" data-event="today">Hoy</button>
        </div>
        <div class="col-12 col-md-4 order-md-1 d-flex align-items-center justify-content-center">
          <button class="btn icon-item icon-item-sm shadow-none text-body-emphasis p-0" type="button" data-event="prev"
            title="Previous"><span class="fas fa-chevron-left"></span></button>
          <h3 class="px-3 text-body-emphasis fw-semibold calendar-title mb-0"> </h3>
          <button class="btn icon-item icon-item-sm shadow-none text-body-emphasis p-0" type="button" data-event="next"
            title="Next"><span class="fas fa-chevron-right"></span></button>
        </div>
        <div class="col-6 col-md-4 ms-auto order-1 d-flex justify-content-end">
          <div>
            <div class="btn-group btn-group-sm" role="group">
              <button class="btn btn-phoenix-secondary active-view" data-fc-view="dayGridMonth">Mes</button>
              <button class="btn btn-phoenix-secondary" data-fc-view="timeGridWeek">Semana</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="calendar-outline mt-6 mb-9" id="appCalendar"></div>
    <?php
        include './admin/include/footer.php';
        ?>
  </div>
  <div class="modal fade" id="searchBoxModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true"
    data-phoenix-modal="data-phoenix-modal" style="--phoenix-backdrop-opacity: 1;">
    <div class="modal-dialog">
      <div class="modal-content mt-15 rounded-pill">
        <div class="modal-body p-0">
          <div class="search-box navbar-top-search-box" data-list='{"valueNames":["title"]}' style="width: auto;">
            <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
              <input class="form-control search-input fuzzy-search rounded-pill form-control-lg" type="search"
                placeholder="Search..." aria-label="Search" />
              <span class="fas fa-search search-box-icon"></span>

            </form>
            <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none"
              data-bs-dismiss="search">
              <button class="btn btn-link p-0" aria-label="Close"></button>
            </div>
            <div class="dropdown-menu border start-0 py-0 overflow-hidden w-100">
              <div class="scrollbar-overlay" style="max-height: 30rem;">
                <div class="list pb-3">
                  <h6 class="dropdown-header text-body-highlight fs-10 py-2">24 <span
                      class="text-body-quaternary">results</span></h6>
                  <hr class="my-0" />
                  <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">
                    Recently Searched </h6>
                  <div class="py-2"><a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-clock-rotate-left"
                            data-fa-transform="shrink-2"></span> Store Macbook</div>
                      </div>
                    </a>
                    <a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-clock-rotate-left"
                            data-fa-transform="shrink-2"></span> MacBook Air - 13″</div>
                      </div>
                    </a>

                  </div>
                  <hr class="my-0" />
                  <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">
                    Products</h6>
                  <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center"
                      href="../apps/e-commerce/landing/product-details.html">
                      <div class="file-thumbnail me-2"><img class="h-100 w-100 object-fit-cover rounded-3"
                          src="../assets/img/products/60x60/3.png" alt="" /></div>
                      <div class="flex-1">
                        <h6 class="mb-0 text-body-highlight title">MacBook Air - 13″</h6>
                        <p class="fs-10 mb-0 d-flex text-body-tertiary"><span
                            class="fw-medium text-body-tertiary text-opactity-85">8GB Memory - 1.6GHz - 128GB
                            Storage</span></p>
                      </div>
                    </a>
                    <a class="dropdown-item py-2 d-flex align-items-center"
                      href="../apps/e-commerce/landing/product-details.html">
                      <div class="file-thumbnail me-2"><img class="img-fluid" src="../assets/img/products/60x60/3.png"
                          alt="" /></div>
                      <div class="flex-1">
                        <h6 class="mb-0 text-body-highlight title">MacBook Pro - 13″</h6>
                        <p class="fs-10 mb-0 d-flex text-body-tertiary"><span
                            class="fw-medium text-body-tertiary text-opactity-85">30 Sep at 12:30 PM</span></p>
                      </div>
                    </a>

                  </div>
                  <hr class="my-0" />
                  <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Quick
                    Links</h6>
                  <div class="py-2"><a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-link text-body"
                            data-fa-transform="shrink-2"></span> Support MacBook House</div>
                      </div>
                    </a>
                    <a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-link text-body"
                            data-fa-transform="shrink-2"></span> Store MacBook″</div>
                      </div>
                    </a>

                  </div>
                  <hr class="my-0" />
                  <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Files
                  </h6>
                  <div class="py-2"><a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-file-zipper text-body"
                            data-fa-transform="shrink-2"></span> Library MacBook folder.rar</div>
                      </div>
                    </a>
                    <a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-file-lines text-body"
                            data-fa-transform="shrink-2"></span> Feature MacBook extensions.txt</div>
                      </div>
                    </a>
                    <a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-image text-body"
                            data-fa-transform="shrink-2"></span> MacBook Pro_13.jpg</div>
                      </div>
                    </a>

                  </div>
                  <hr class="my-0" />
                  <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">
                    Members</h6>
                  <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center"
                      href="../pages/members.html">
                      <div class="avatar avatar-l status-online  me-2 text-body">
                        <img class="rounded-circle " src="../assets/img/team/40x40/10.webp" alt="" />

                      </div>
                      <div class="flex-1">
                        <h6 class="mb-0 text-body-highlight title">Carry Anna</h6>
                        <p class="fs-10 mb-0 d-flex text-body-tertiary">anna@technext.it</p>
                      </div>
                    </a>
                    <a class="dropdown-item py-2 d-flex align-items-center" href="../pages/members.html">
                      <div class="avatar avatar-l  me-2 text-body">
                        <img class="rounded-circle " src="../assets/img/team/40x40/12.webp" alt="" />

                      </div>
                      <div class="flex-1">
                        <h6 class="mb-0 text-body-highlight title">John Smith</h6>
                        <p class="fs-10 mb-0 d-flex text-body-tertiary">smith@technext.it</p>
                      </div>
                    </a>

                  </div>
                  <hr class="my-0" />
                  <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">
                    Related Searches</h6>
                  <div class="py-2"><a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"><span
                            class="fa-brands fa-firefox-browser text-body" data-fa-transform="shrink-2"></span> Search
                          in the Web MacBook</div>
                      </div>
                    </a>
                    <a class="dropdown-item" href="../apps/e-commerce/landing/product-details.html">
                      <div class="d-flex align-items-center">

                        <div class="fw-normal text-body-highlight title"> <span class="fa-brands fa-chrome text-body"
                            data-fa-transform="shrink-2"></span> Store MacBook″</div>
                      </div>
                    </a>

                  </div>
                </div>
                <div class="text-center">
                  <p class="fallback fw-bold fs-7 d-none">No Result Found.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border border-translucent"></div>
    </div>
  </div>
  <div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content border border-translucent">
        <form id="addEventForm" autocomplete="off">
          <input type="hidden" name="op" id="op" />
          <input type="hidden" name="id" id="id" />
          <div class="modal-header px-card border-0">
            <div class="w-100 d-flex justify-content-between align-items-start">
              <div>
                <h5 class="mb-0 lh-sm text-body-highlight">Agregar</h5>
                <div class="mt-2">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" id="inlineRadio1" type="radio" name="calendarTask" value="Evento"
                      checked />
                    <label class="form-check-label" for="inlineRadio1" style="color:black !important">Evento</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" id="inlineRadio2" type="radio" name="calendarTask" value="Tarea" />
                    <label class="form-check-label" for="inlineRadio2" style="color:black !important">Tarea</label>
                  </div>
                </div>
              </div>

              <button class="btn p-1 fs-10 text-body" type="button" data-bs-dismiss="modal" aria-label="Close">Descartar
              </button>
            </div>
          </div>
          <div class="modal-body p-card py-0">
            <div class="form-floating mb-3">
              <input class="form-control" id="titulo" type="text" name="titulo" required="required"
                placeholder="Event title" />
              <label for="eventTitle">Titulo</label>
            </div>
            <div class="form-floating mb-5">
              <select class="form-select" id="etiqueta" name="etiqueta">
                <option value="Negocios" selected="selected">Necogios</option>
                <option value="Personal">Personal</option>
                <option value="Cita">Cita</option>
                <option value="Cumpleaños">Cumpleaños</option>
                <option value="Familiar">Familiar</option>
              </select>
              <label for="eventLabel">Etiqueta</label>
            </div>
            <!-- Fila 1: Fecha y hora de inicio -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="flatpickr-input-container">
                  <div class="form-floating">
                    <input class="form-control datetimepicker" id="fecha_inicio" name="fecha_inicio" type="text"
                      data-options='{"disableMobile":true,"dateFormat":"Y-m-d","allowInput":true}' />
                    <span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
                    <label class="ps-6" for="fecha_inicio">Empieza</label>
                  </div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="flatpickr-input-container">
                  <div class="form-floating">
                    <input class="form-control datetimepicker" id="hora_inicio" type="text"
                      data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true,"allowInput":true}' />
                    <label for="hora_inicio">Hora Inicio</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fila 2: Fecha y hora de fin -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="flatpickr-input-container">
                  <div class="form-floating">
                    <input class="form-control datetimepicker" id="fecha_fin" name="fecha_fin" type="text"
                      data-options='{"disableMobile":true,"dateFormat":"Y-m-d","allowInput":true}' />
                    <span class="uil uil-calendar-alt flatpickr-icon text-body-tertiary"></span>
                    <label class="ps-6" for="fecha_fin">Termina</label>
                  </div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="flatpickr-input-container">
                  <div class="form-floating">
                    <input class="form-control datetimepicker" id="hora_fin" type="text"
                      data-options='{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true,"allowInput":true}' />
                    <label for="hora_fin">Hora Termina</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="dia" name="dia" />
              <label class="form-check-label" for="eventAllDay" style="color:black !important;">Todo el dia
              </label>
            </div>
            <div class="form-floating my-5">
              <textarea class="form-control" id="descripcion" placeholder="Ingrese su descripcion acá"
                name="descripcion" style="height: 128px"></textarea>
              <label for="eventDescription">Descripción</label>
            </div>
            <!-- <div class="form-floating mb-3">
              <select class="form-select" id="repeticion" name="repeticion">
                <option value="" selected="selected">No Repetir</option>
                <option value="Diario">Diariamente </option>
                <option value="Semanalmente">Semanalmente</option>
                <option value="Mensualmente">Mensualmente</option>
                <option value="Diario_sin_festivos">Diario exepto dias Festivos</option>
              </select>
            </div> -->
          </div>
          <button class="btn btn-primary px-5" type="button" onclick="CALENDARIO.savedata();">Guardar</button>
        </form>
      </div>
    </div>
  </div>
  </div>

  <!-- ===============================================-->
  <!--    JavaScripts-->
  <!-- ===============================================-->
  <script src="vendors/popper/popper.min.js"></script>
  <script src="vendors/bootstrap/bootstrap.min.js"></script>
  <script src="vendors/anchorjs/anchor.min.js"></script>
  <script src="vendors/is/is.min.js"></script>
  <script src="vendors/fontawesome/all.min.js"></script>
  <script src="vendors/lodash/lodash.min.js"></script>
  <script src="vendors/list.js/list.min.js"></script>
  <script src="vendors/feather-icons/feather.min.js"></script>
  <script src="vendors/dayjs/dayjs.min.js"></script>
  <script src="vendors/fullcalendar/index.global.min.js"></script>
  <script src="vendors/flatpickr/flatpickr.min.js"></script>
  <script src="vendors/dayjs/dayjs.min.js"></script>
  <script src="assets/js/phoenix.js"></script>
  <script type="text/javascript" src="admin/js/calendar.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const checkboxDia = document.getElementById('dia');
      const horaInicio = document.getElementById('hora_inicio');
      const horaFin = document.getElementById('hora_fin');

      checkboxDia.addEventListener('change', function () {
        if (this.checked) {
          horaInicio.value = '00:00';
          horaFin.value = '23:59';
          horaInicio.setAttribute('readonly', true);
          horaFin.setAttribute('readonly', true);
        } else {
          horaInicio.value = '';
          horaFin.value = '';
          horaInicio.removeAttribute('readonly');
          horaFin.removeAttribute('readonly');
        }
      });
    });
  </script>
</body>

</html>