<?php
    include './admin/include/head.php';
function getUrl()
{
    $port = $_SERVER["SERVER_PORT"];
    $nameServer = $port != "80" ? $_SERVER['SERVER_NAME'] . ":" . $port : $_SERVER['SERVER_NAME'];
    $url = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $nameServer,
        $_SERVER['REQUEST_URI']
    );
    $final =  str_replace(basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php", "", $url);
    $exists = strpos($final, "?");
    if ($exists == !false) {
        $final =  substr($final, 0, $exists);
        return $final;
    } else {
        return $final;
    }
}

require_once './admin/include/generic_classes.php';
include './admin/classes/Ciudad.php';
include './admin/classes/Estado.php';
require './admin/classes/Departamento.php';
include './admin/db/colores.php';
include './admin/classes/Main.php';
include './admin/classes/Detalle.php';
include './admin/classes/Cuenta.php';
include './admin/classes/Cuentapro.php';
include './admin/classes/Secreinversion.php';
include './admin/classes/Munnovisitados.php';




// Obtener permisos
$permissions = [
    'view' => SessionData::getPermission(1),
    'create' => SessionData::getPermission(2),
    'edit' => SessionData::getPermission(3),
   
];

// Validación de permiso de visualización
if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}

//informacion del mail
$arr = Main::getDataMain(null);
$isvalid = $arr['output']['valid'];
$visitas = $arr['output']['visitas'];
$apoyos = $arr['output']['apoyos'];
$municipios = $arr['output']['municipios'];
$veredas = $arr['output']['veredas'];
$provincia = $arr['output']['provincia'];
$porcentaje_veredas = $arr['output']['porcentaje_veredas'];
$porcentaje_municipios = $arr['output']['porcentaje_municipios'];
$inversionsec = $arr['output']['inversionsec'];
$valorproyectos = $arr['output']['valorproyectos'];
$secretaria = $arr['output']['secretaria'];
$sumaproyectos = $arr['output']['sumaproyectos'];
$visitarpendiente = 87 - $municipios;


$departamento = new Departamento();
$santander = $departamento->getAll(["id" => 21]);
$santander = $santander["output"]["response"]["0"];
$code = null;
$mapa = null;

if (isset($_GET['depto_id']) && in_array($_GET['depto_id'], [1, 12, 21])) {
    switch ($_GET['depto_id']) {

        case '21':
            $code = $santander["codigo_departamento"];
            $mapa = "admin/mapa-santander/mapa.php";
            break;
    }
}
if (!is_null($code)) {
    $arr = Ciudad::getAll(array('codigo_departamento' => $code));
    $finalMunicipios = $arr['output']['response'];
    $arrApoyoDep = Ciudad::getApoyoByCodigoDepartamento(array('codigo_departamento' => $code));
}
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">

  <body>
    <!-- ===============================================-->
    <!--    INCLUDES-->
    <!-- ===============================================-->
    <main class="main" id="top">
    <?php
    include './admin/include/navbar.php';
    ?>
        <?php
    include './admin/include/header.php';
    ?>
    <!-- ===============================================-->
    <!--  CONTENT-->
    <!-- ===============================================-->
      <div class="content">
        <div class="pb-5">
          <div class="row g-4">
            <div class="col-12 col-xxl-6">
              <div class="mb-8">
                <h2 class="mb-2">Dashboard</h2>
                <h5 class="text-body-tertiary fw-semibold">Aquí encontrarás información Importante sobre tu comunidad</h5>
              </div>
              <div class="row align-items-center g-4">
                <div class="col-12 col-md-auto">
                  <div class="d-flex align-items-center"><span class="fa-stack" style="min-height: 46px;min-width: 46px;"><span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-success-light" data-fa-transform="down-4 rotate--10 left-4"></span><span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-success" data-fa-transform="up-4 right-3 grow-2"></span><span class="fa-stack-1x fa-solid fa-star text-success " data-fa-transform="shrink-2 up-8 right-6"></span></span>
                    <div class="ms-3">
                      <h4 class="mb-0">57 new orders</h4>
                      <p class="text-body-secondary fs-9 mb-0">Awating processing</p>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-auto">
                  <div class="d-flex align-items-center"><span class="fa-stack" style="min-height: 46px;min-width: 46px;"><span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-warning-light" data-fa-transform="down-4 rotate--10 left-4"></span><span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-warning" data-fa-transform="up-4 right-3 grow-2"></span><span class="fa-stack-1x fa-solid fa-pause text-warning " data-fa-transform="shrink-2 up-8 right-6"></span></span>
                    <div class="ms-3">
                      <h4 class="mb-0">5 orders</h4>
                      <p class="text-body-secondary fs-9 mb-0">On hold</p>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-auto">
                  <div class="d-flex align-items-center"><span class="fa-stack" style="min-height: 46px;min-width: 46px;"><span class="fa-solid fa-square fa-stack-2x dark__text-opacity-50 text-danger-light" data-fa-transform="down-4 rotate--10 left-4"></span><span class="fa-solid fa-circle fa-stack-2x stack-circle text-stats-circle-danger" data-fa-transform="up-4 right-3 grow-2"></span><span class="fa-stack-1x fa-solid fa-xmark text-danger " data-fa-transform="shrink-2 up-8 right-6"></span></span>
                    <div class="ms-3">
                      <h4 class="mb-0">15 products</h4>
                      <p class="text-body-secondary fs-9 mb-0">Out of stock</p>
                    </div>
                  </div>
                </div>
              </div>
              <hr class="bg-body-secondary mb-6 mt-4" />
              <div class="row flex-between-center mb-4 g-3">
                <div class="col-auto">
                  <h3>Total sells</h3>
                  <p class="text-body-tertiary lh-sm mb-0">Payment received across all channels</p>
                </div>
                <div class="col-8 col-sm-4">
                  <select class="form-select form-select-sm" id="select-gross-revenue-month">
                    <option>Mar 1 - 31, 2022</option>
                    <option>April 1 - 30, 2022</option>
                    <option>May 1 - 31, 2022</option>
                  </select>
                </div>
              </div>
              <div class="echart-total-sales-chart" style="min-height:320px;width:100%"></div>
            </div>
            <div class="col-12 col-xxl-6">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                        <div>
                          <h5 class="mb-1">Total orders<span class="badge badge-phoenix badge-phoenix-warning rounded-pill fs-9 ms-2"><span class="badge-label">-6.8%</span></span></h5>
                          <h6 class="text-body-tertiary">Last 7 days</h6>
                        </div>
                        <h4>16,247</h4>
                      </div>
                      <div class="d-flex justify-content-center px-4 py-6">
                        <div class="echart-total-orders" style="height:85px;width:115px"></div>
                      </div>
                      <div class="mt-2">
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item bg-primary me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Completed</h6>
                          <h6 class="text-body fw-semibold mb-0">52%</h6>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="bullet-item bg-primary-subtle me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Pending payment</h6>
                          <h6 class="text-body fw-semibold mb-0">48%</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                        <div>
                          <h5 class="mb-1">New customers<span class="badge badge-phoenix badge-phoenix-warning rounded-pill fs-9 ms-2"> <span class="badge-label">+26.5%</span></span></h5>
                          <h6 class="text-body-tertiary">Last 7 days</h6>
                        </div>
                        <h4>356</h4>
                      </div>
                      <div class="pb-0 pt-4">
                        <div class="echarts-new-customers" style="height:180px;width:100%;"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex justify-content-between">
                        <div>
                          <h5 class="mb-2">Top coupons</h5>
                          <h6 class="text-body-tertiary">Last 7 days</h6>
                        </div>
                      </div>
                      <div class="pb-4 pt-3">
                        <div class="echart-top-coupons" style="height:115px;width:100%;"></div>
                      </div>
                      <div>
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item bg-primary me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Percentage discount</h6>
                          <h6 class="text-body fw-semibold mb-0">72%</h6>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item bg-primary-lighter me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Fixed card discount</h6>
                          <h6 class="text-body fw-semibold mb-0">18%</h6>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="bullet-item bg-info-dark me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Fixed product discount</h6>
                          <h6 class="text-body fw-semibold mb-0">10%</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                      <div class="d-flex justify-content-between">
                        <div>
                          <h5 class="mb-2">Paying vs non paying</h5>
                          <h6 class="text-body-tertiary">Last 7 days</h6>
                        </div>
                      </div>
                      <div class="d-flex justify-content-center pt-3 flex-1">
                        <div class="echarts-paying-customer-chart" style="height:100%;width:100%;"></div>
                      </div>
                      <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                          <div class="bullet-item bg-primary me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Paying customer</h6>
                          <h6 class="text-body fw-semibold mb-0">30%</h6>
                        </div>
                        <div class="d-flex align-items-center">
                          <div class="bullet-item bg-primary-subtle me-2"></div>
                          <h6 class="text-body fw-semibold flex-1 mb-0">Non-paying customer</h6>
                          <h6 class="text-body fw-semibold mb-0">70%</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       
        <div class="row gx-6">
          <div class="col-12 col-xl-6">
            <div data-list='{"valueNames":["country","users","transactions","revenue","conv-rate"],"page":5}'>
              <div class="mb-5 mt-7">
                <h3>Top regions by revenue</h3>
                <p class="text-body-tertiary">Where you generated most of the revenue</p>
              </div>
              <div class="table-responsive scrollbar">
                <table class="table fs-10 mb-0">
                  <thead>
                    <tr>
                      <th class="sort border-top border-translucent ps-0 align-middle" scope="col" data-sort="country" style="width:32%">COUNTRY</th>
                      <th class="sort border-top border-translucent align-middle" scope="col" data-sort="users" style="width:17%">USERS</th>
                      <th class="sort border-top border-translucent text-end align-middle" scope="col" data-sort="transactions" style="width:16%">TRANSACTIONS</th>
                      <th class="sort border-top border-translucent text-end align-middle" scope="col" data-sort="revenue" style="width:20%">REVENUE</th>
                      <th class="sort border-top border-translucent text-end pe-0 align-middle" scope="col" data-sort="conv-rate" style="width:17%">CONV. RATE</th>
                    </tr>
                  </thead>
                  <tr>
                    <td></td>
                    <td class="align-middle py-4">
                      <h4 class="mb-0 fw-normal">377,620</h4>
                    </td>
                    <td class="align-middle text-end py-4">
                      <h4 class="mb-0 fw-normal">236</h4>
                    </td>
                    <td class="align-middle text-end py-4">
                      <h4 class="mb-0 fw-normal">$15,758</h4>
                    </td>
                    <td class="align-middle text-end py-4 pe-0">
                      <h4 class="mb-0 fw-normal">10.32%</h4>
                    </td>
                  </tr>
                  <tbody class="list" id="table-regions-by-revenue">
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">1. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/india.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">India</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">92896<span class="text-body-tertiary fw-semibold ms-2">(41.6%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">67<span class="text-body-tertiary fw-semibold ms-2">(34.3%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$7560<span class="text-body-tertiary fw-semibold ms-2">(36.9%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>14.01%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">2. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/china.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">China</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">50496<span class="text-body-tertiary fw-semibold ms-2">(32.8%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">54<span class="text-body-tertiary fw-semibold ms-2">(23.8%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$6532<span class="text-body-tertiary fw-semibold ms-2">(26.5%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>23.56%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">3. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/usa.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">USA</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">45679<span class="text-body-tertiary fw-semibold ms-2">(24.3%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">35<span class="text-body-tertiary fw-semibold ms-2">(19.7%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$5432<span class="text-body-tertiary fw-semibold ms-2">(16.9%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>10.23%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">4. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/south-korea.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">South Korea</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">36453<span class="text-body-tertiary fw-semibold ms-2">(19.7%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">22<span class="text-body-tertiary fw-semibold ms-2">(9.54%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$4673<span class="text-body-tertiary fw-semibold ms-2">(11.6%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>8.85%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">5. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/vietnam.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">Vietnam</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">15007<span class="text-body-tertiary fw-semibold ms-2">(11.9%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">17<span class="text-body-tertiary fw-semibold ms-2">(6.91%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$2456<span class="text-body-tertiary fw-semibold ms-2">(10.2%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>6.01%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">6. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/russia.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">Russia</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">54215<span class="text-body-tertiary fw-semibold ms-2">(32.9%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">38<span class="text-body-tertiary fw-semibold ms-2">(7.91%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$3254<span class="text-body-tertiary fw-semibold ms-2">(12.4%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>6.21%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">7. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/australia.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">Australia</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">54789<span class="text-body-tertiary fw-semibold ms-2">(12.7%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">32<span class="text-body-tertiary fw-semibold ms-2">(14.0%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$3215<span class="text-body-tertiary fw-semibold ms-2">(5.72%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>12.02%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">8. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/england.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">England</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">14785<span class="text-body-tertiary fw-semibold ms-2">(12.9%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">11<span class="text-body-tertiary fw-semibold ms-2">(32.91%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$4745<span class="text-body-tertiary fw-semibold ms-2">(10.2%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>8.01%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">9. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/indonesia.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">Indonesia</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">32156<span class="text-body-tertiary fw-semibold ms-2">(32.2%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">89<span class="text-body-tertiary fw-semibold ms-2">(12.0%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$2456<span class="text-body-tertiary fw-semibold ms-2">(23.2%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>9.07%</h6>
                      </td>
                    </tr>
                    <tr>
                      <td class="white-space-nowrap ps-0 country" style="width:32%">
                        <div class="d-flex align-items-center">
                          <h6 class="mb-0 me-3">10. </h6><a href="#!">
                            <div class="d-flex align-items-center"><img src="assets/img/country/japan.png" alt="" width="24" />
                              <p class="mb-0 ps-3 text-primary fw-bold fs-9">Japan</p>
                            </div>
                          </a>
                        </div>
                      </td>
                      <td class="align-middle users" style="width:17%">
                        <h6 class="mb-0">12547<span class="text-body-tertiary fw-semibold ms-2">(12.7%)</span></h6>
                      </td>
                      <td class="align-middle text-end transactions" style="width:17%">
                        <h6 class="mb-0">21<span class="text-body-tertiary fw-semibold ms-2">(14.91%)</span></h6>
                      </td>
                      <td class="align-middle text-end revenue" style="width:17%">
                        <h6 class="mb-0">$2541<span class="text-body-tertiary fw-semibold ms-2">(23.2%)</span></h6>
                      </td>
                      <td class="align-middle text-end pe-0 conv-rate" style="width:17%">
                        <h6>20.01%</h6>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="row align-items-center py-1">
                <div class="pagination d-none"></div>
                <div class="col d-flex fs-9">
                  <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
                </div>
                <div class="col-auto d-flex">

                  <button class="btn btn-link px-1 me-1" type="button" title="Previous" data-list-pagination="prev"><span class="fas fa-chevron-left me-2"></span>Previous</button>
                  <button class="btn btn-link px-1 ms-1" type="button" title="Next" data-list-pagination="next">Next<span class="fas fa-chevron-right ms-2"></span></button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl-6">
            <div class="mx-n4 mx-lg-n6 ms-xl-0 h-100">
              <div class="h-100 w-100">
                <div class="h-100 bg-body-emphasis" id="map" style="min-height: 300px;"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis pt-6 pb-9 border-top">
          <div class="row g-6">
            <div class="col-12 col-xl-6">
              <div class="me-xl-4">
                <div>
                  <!-- <h3>Projection vs actual</h3> -->
                  <h3>Visitas realizadas a provincias</h3>
                  <p class="mb-1 text-body-tertiary">Actual earnings vs projected earnings</p>
                </div>
                <div class="echart-projection-actual" style="height:300px; width:100%"></div>
              </div>
            </div>
            <div class="col-12 col-xl-6">
              <div>
                <h3>Visitas por mes a municipios</h3>
                <p class="mb-1 text-body-tertiary">Rate of customers returning to your shop over time</p>
              </div>
              <div class="echart-returning-customer" style="height:300px;"></div>
            </div>
          </div>
        </div>
        <?php
        include './admin/include/footer.php';
        ?>
      </div>
      <div class="modal fade" id="searchBoxModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-phoenix-modal="data-phoenix-modal" style="--phoenix-backdrop-opacity: 1;">
        <div class="modal-dialog">
          <div class="modal-content mt-15 rounded-pill">
            <div class="modal-body p-0">
              <div class="search-box navbar-top-search-box" data-list='{"valueNames":["title"]}' style="width: auto;">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                  <input class="form-control search-input fuzzy-search rounded-pill form-control-lg" type="search" placeholder="Search..." aria-label="Search" />
                  <span class="fas fa-search search-box-icon"></span>

                </form>
                <div class="btn-close position-absolute end-0 top-50 translate-middle cursor-pointer shadow-none" data-bs-dismiss="search">
                  <button class="btn btn-link p-0" aria-label="Close"></button>
                </div>
                <div class="dropdown-menu border start-0 py-0 overflow-hidden w-100">
                  <div class="scrollbar-overlay" style="max-height: 30rem;">
                    <div class="list pb-3">
                      <h6 class="dropdown-header text-body-highlight fs-10 py-2">24 <span class="text-body-quaternary">results</span></h6>
                      <hr class="my-0" />
                      <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Recently Searched </h6>
                      <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-clock-rotate-left" data-fa-transform="shrink-2"></span> Store Macbook</div>
                          </div>
                        </a>
                        <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-clock-rotate-left" data-fa-transform="shrink-2"></span> MacBook Air - 13″</div>
                          </div>
                        </a>

                      </div>
                      <hr class="my-0" />
                      <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Products</h6>
                      <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">
                          <div class="file-thumbnail me-2"><img class="h-100 w-100 object-fit-cover rounded-3" src="assets/img/products/60x60/3.png" alt="" /></div>
                          <div class="flex-1">
                            <h6 class="mb-0 text-body-highlight title">MacBook Air - 13″</h6>
                            <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">8GB Memory - 1.6GHz - 128GB Storage</span></p>
                          </div>
                        </a>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">
                          <div class="file-thumbnail me-2"><img class="img-fluid" src="assets/img/products/60x60/3.png" alt="" /></div>
                          <div class="flex-1">
                            <h6 class="mb-0 text-body-highlight title">MacBook Pro - 13″</h6>
                            <p class="fs-10 mb-0 d-flex text-body-tertiary"><span class="fw-medium text-body-tertiary text-opactity-85">30 Sep at 12:30 PM</span></p>
                          </div>
                        </a>

                      </div>
                      <hr class="my-0" />
                      <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Quick Links</h6>
                      <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-link text-body" data-fa-transform="shrink-2"></span> Support MacBook House</div>
                          </div>
                        </a>
                        <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-link text-body" data-fa-transform="shrink-2"></span> Store MacBook″</div>
                          </div>
                        </a>

                      </div>
                      <hr class="my-0" />
                      <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Files</h6>
                      <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"><span class="fa-solid fa-file-zipper text-body" data-fa-transform="shrink-2"></span> Library MacBook folder.rar</div>
                          </div>
                        </a>
                        <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-file-lines text-body" data-fa-transform="shrink-2"></span> Feature MacBook extensions.txt</div>
                          </div>
                        </a>
                        <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"> <span class="fa-solid fa-image text-body" data-fa-transform="shrink-2"></span> MacBook Pro_13.jpg</div>
                          </div>
                        </a>

                      </div>
                      <hr class="my-0" />
                      <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Members</h6>
                      <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center" href="pages/members.html">
                          <div class="avatar avatar-l status-online  me-2 text-body">
                            <img class="rounded-circle " src="assets/img/team/40x40/10.webp" alt="" />

                          </div>
                          <div class="flex-1">
                            <h6 class="mb-0 text-body-highlight title">Carry Anna</h6>
                            <p class="fs-10 mb-0 d-flex text-body-tertiary">anna@technext.it</p>
                          </div>
                        </a>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="pages/members.html">
                          <div class="avatar avatar-l  me-2 text-body">
                            <img class="rounded-circle " src="assets/img/team/40x40/12.webp" alt="" />

                          </div>
                          <div class="flex-1">
                            <h6 class="mb-0 text-body-highlight title">John Smith</h6>
                            <p class="fs-10 mb-0 d-flex text-body-tertiary">smith@technext.it</p>
                          </div>
                        </a>

                      </div>
                      <hr class="my-0" />
                      <h6 class="dropdown-header text-body-highlight fs-9 border-bottom border-translucent py-2 lh-sm">Related Searches</h6>
                      <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"><span class="fa-brands fa-firefox-browser text-body" data-fa-transform="shrink-2"></span> Search in the Web MacBook</div>
                          </div>
                        </a>
                        <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                          <div class="d-flex align-items-center">

                            <div class="fw-normal text-body-highlight title"> <span class="fa-brands fa-chrome text-body" data-fa-transform="shrink-2"></span> Store MacBook″</div>
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
      <script>
        var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
        var navbarTop = document.querySelector('.navbar-top');
        if (navbarTopStyle === 'darker') {
          navbarTop.setAttribute('data-navbar-appearance', 'darker');
        }

        var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
        var navbarVertical = document.querySelector('.navbar-vertical');
        if (navbarVertical && navbarVerticalStyle === 'darker') {
          navbarVertical.setAttribute('data-navbar-appearance', 'darker');
        }
      </script>
       <?php
    include './admin/include/asistentevirtual.php';
    ?>
    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->


    <div class="offcanvas offcanvas-end settings-panel border-0" id="settings-offcanvas" tabindex="-1" aria-labelledby="settings-offcanvas">
      <div class="offcanvas-header align-items-start border-bottom flex-column border-translucent">
        <div class="pt-1 w-100 mb-6 d-flex justify-content-between align-items-start">
          <div>
            <h5 class="mb-2 me-2 lh-sm"><span class="fas fa-palette me-2 fs-8"></span>Theme Customizer</h5>
            <p class="mb-0 fs-9">Explore different styles according to your preferences</p>
          </div>
          <button class="btn p-1 fw-bolder" type="button" data-bs-dismiss="offcanvas" aria-label="Close"><span class="fas fa-times fs-8"> </span></button>
        </div>
        <button class="btn btn-phoenix-secondary w-100" data-theme-control="reset"><span class="fas fa-arrows-rotate me-2 fs-10"></span>Reset to default</button>
      </div>
      <div class="offcanvas-body scrollbar px-card" id="themeController">
        <div class="setting-panel-item mt-0">
          <h5 class="setting-panel-item-title">Color Scheme</h5>
          <div class="row gx-2">
            <div class="col-4">
              <input class="btn-check" id="themeSwitcherLight" name="theme-color" type="radio" value="light" data-theme-control="phoenixTheme" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherLight"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="assets/img/generic/default-light.png" alt=""/></span><span class="label-text">Light</span></label>
            </div>
            <div class="col-4">
              <input class="btn-check" id="themeSwitcherDark" name="theme-color" type="radio" value="dark" data-theme-control="phoenixTheme" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherDark"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="assets/img/generic/default-dark.png" alt=""/></span><span class="label-text"> Dark</span></label>
            </div>
            <div class="col-4">
              <input class="btn-check" id="themeSwitcherAuto" name="theme-color" type="radio" value="auto" data-theme-control="phoenixTheme" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="themeSwitcherAuto"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="assets/img/generic/auto.png" alt=""/></span><span class="label-text"> Auto</span></label>
            </div>
          </div>
        </div>
        <div class="border border-translucent rounded-3 p-4 setting-panel-item bg-body-emphasis">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="setting-panel-item-title mb-1">RTL </h5>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input ms-auto" type="checkbox" data-theme-control="phoenixIsRTL" />
            </div>
          </div>
          <p class="mb-0 text-body-tertiary">Change text direction</p>
        </div>
        <div class="border border-translucent rounded-3 p-4 setting-panel-item bg-body-emphasis">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="setting-panel-item-title mb-1">Support Chat </h5>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input ms-auto" type="checkbox" data-theme-control="phoenixSupportChat" />
            </div>
          </div>
          <p class="mb-0 text-body-tertiary">Toggle support chat</p>
        </div>
        <div class="setting-panel-item">
          <h5 class="setting-panel-item-title">Navigation Type</h5>
          <div class="row gx-2">
            <div class="col-6">
              <input class="btn-check" id="navbarPositionVertical" name="navigation-type" type="radio" value="vertical" data-theme-control="phoenixNavbarPosition" data-page-url="documentation/layouts/vertical-navbar.html" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionVertical"> <span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="assets/img/generic/default-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/default-dark.png" alt=""/></span><span class="label-text">Vertical</span></label>
            </div>
            <div class="col-6">
              <input class="btn-check" id="navbarPositionHorizontal" name="navigation-type" type="radio" value="horizontal" data-theme-control="phoenixNavbarPosition" data-page-url="documentation/layouts/horizontal-navbar.html" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionHorizontal"> <span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="assets/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/top-default-dark.png" alt=""/></span><span class="label-text"> Horizontal</span></label>
            </div>
            <div class="col-6">
              <input class="btn-check" id="navbarPositionCombo" name="navigation-type" type="radio" value="combo" data-theme-control="phoenixNavbarPosition" data-page-url="documentation/layouts/combo-navbar.html" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionCombo"> <span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="assets/img/generic/nav-combo-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/nav-combo-dark.png" alt=""/></span><span class="label-text"> Combo</span></label>
            </div>
            <div class="col-6">
              <input class="btn-check" id="navbarPositionTopDouble" name="navigation-type" type="radio" value="dual-nav" data-theme-control="phoenixNavbarPosition" data-page-url="documentation/layouts/dual-nav.html" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarPositionTopDouble"> <span class="rounded d-block"><img class="img-fluid img-prototype d-dark-none" src="assets/img/generic/dual-light.png" alt=""/><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/dual-dark.png" alt=""/></span><span class="label-text"> Dual nav</span></label>
            </div>
          </div>
        </div>
        <div class="setting-panel-item">
          <h5 class="setting-panel-item-title">Vertical Navbar Appearance</h5>
          <div class="row gx-2">
            <div class="col-6">
              <input class="btn-check" id="navbar-style-default" type="radio" name="config.name" value="default" data-theme-control="phoenixNavbarVerticalStyle" />
              <label class="btn d-block w-100 btn-navbar-style fs-9" for="navbar-style-default"> <img class="img-fluid img-prototype d-dark-none" src="assets/img/generic/default-light.png" alt="" /><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/default-dark.png" alt="" /><span class="label-text d-dark-none"> Default</span><span class="label-text d-light-none">Default</span></label>
            </div>
            <div class="col-6">
              <input class="btn-check" id="navbar-style-dark" type="radio" name="config.name" value="darker" data-theme-control="phoenixNavbarVerticalStyle" />
              <label class="btn d-block w-100 btn-navbar-style fs-9" for="navbar-style-dark"> <img class="img-fluid img-prototype d-dark-none" src="assets/img/generic/vertical-darker.png" alt="" /><img class="img-fluid img-prototype d-light-none" src="assets/img/generic/vertical-lighter.png" alt="" /><span class="label-text d-dark-none"> Darker</span><span class="label-text d-light-none">Lighter</span></label>
            </div>
          </div>
        </div>
        <div class="setting-panel-item">
          <h5 class="setting-panel-item-title">Horizontal Navbar Shape</h5>
          <div class="row gx-2">
            <div class="col-6">
              <input class="btn-check" id="navbarShapeDefault" name="navbar-shape" type="radio" value="default" data-theme-control="phoenixNavbarTopShape" data-page-url="documentation/layouts/horizontal-navbar.html" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarShapeDefault"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="assets/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="assets/img/generic/top-default-dark.png" alt=""/></span><span class="label-text">Default</span></label>
            </div>
            <div class="col-6">
              <input class="btn-check" id="navbarShapeSlim" name="navbar-shape" type="radio" value="slim" data-theme-control="phoenixNavbarTopShape" data-page-url="documentation/layouts/horizontal-navbar.html#horizontal-navbar-slim" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarShapeSlim"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="assets/img/generic/top-slim.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="assets/img/generic/top-slim-dark.png" alt=""/></span><span class="label-text"> Slim</span></label>
            </div>
          </div>
        </div>
        <div class="setting-panel-item">
          <h5 class="setting-panel-item-title">Horizontal Navbar Appearance</h5>
          <div class="row gx-2">
            <div class="col-6">
              <input class="btn-check" id="navbarTopDefault" name="navbar-top-style" type="radio" value="default" data-theme-control="phoenixNavbarTopStyle" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarTopDefault"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="assets/img/generic/top-default.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="assets/img/generic/top-style-darker.png" alt=""/></span><span class="label-text">Default</span></label>
            </div>
            <div class="col-6">
              <input class="btn-check" id="navbarTopDarker" name="navbar-top-style" type="radio" value="darker" data-theme-control="phoenixNavbarTopStyle" />
              <label class="btn d-inline-block btn-navbar-style fs-9" for="navbarTopDarker"> <span class="mb-2 rounded d-block"><img class="img-fluid img-prototype d-dark-none mb-0" src="assets/img/generic/navbar-top-style-light.png" alt=""/><img class="img-fluid img-prototype d-light-none mb-0" src="assets/img/generic/top-style-lighter.png" alt=""/></span><span class="label-text d-dark-none">Darker</span><span class="label-text d-light-none">Lighter</span></label>
            </div>
          </div>
        </div><a class="bun btn-primary d-grid mb-3 text-white mt-5 btn btn-primary" href="https://themes.getbootstrap.com/product/phoenix-admin-dashboard-webapp-template/" target="_blank">Purchase template</a>
      </div>
    </div><a class="card setting-toggle" href="#settings-offcanvas" data-bs-toggle="offcanvas">
      <div class="card-body d-flex align-items-center px-2 py-1">
        <div class="position-relative rounded-start" style="height:34px;width:28px">
          <div class="settings-popover"><span class="ripple"><span class="fa-spin position-absolute all-0 d-flex flex-center"><span class="icon-spin position-absolute all-0 d-flex flex-center">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.7369 12.3941L19.1989 12.1065C18.4459 11.7041 18.0843 10.8487 18.0843 9.99495C18.0843 9.14118 18.4459 8.28582 19.1989 7.88336L19.7369 7.59581C19.9474 7.47484 20.0316 7.23291 19.9474 7.03131C19.4842 5.57973 18.6843 4.28943 17.6738 3.20075C17.5053 3.03946 17.2527 2.99914 17.0422 3.12011L16.393 3.46714C15.6883 3.84379 14.8377 3.74529 14.1476 3.3427C14.0988 3.31422 14.0496 3.28621 14.0002 3.25868C13.2568 2.84453 12.7055 2.10629 12.7055 1.25525V0.70081C12.7055 0.499202 12.5371 0.297594 12.2845 0.257272C10.7266 -0.105622 9.16879 -0.0653007 7.69516 0.257272C7.44254 0.297594 7.31623 0.499202 7.31623 0.70081V1.23474C7.31623 2.09575 6.74999 2.8362 5.99824 3.25599C5.95774 3.27861 5.91747 3.30159 5.87744 3.32493C5.15643 3.74527 4.26453 3.85902 3.53534 3.45302L2.93743 3.12011C2.72691 2.99914 2.47429 3.03946 2.30587 3.20075C1.29538 4.28943 0.495411 5.57973 0.0322686 7.03131C-0.051939 7.23291 0.0322686 7.47484 0.242788 7.59581L0.784376 7.8853C1.54166 8.29007 1.92694 9.13627 1.92694 9.99495C1.92694 10.8536 1.54166 11.6998 0.784375 12.1046L0.242788 12.3941C0.0322686 12.515 -0.051939 12.757 0.0322686 12.9586C0.495411 14.4102 1.29538 15.7005 2.30587 16.7891C2.47429 16.9504 2.72691 16.9907 2.93743 16.8698L3.58669 16.5227C4.29133 16.1461 5.14131 16.2457 5.8331 16.6455C5.88713 16.6767 5.94159 16.7074 5.99648 16.7375C6.75162 17.1511 7.31623 17.8941 7.31623 18.7552V19.2891C7.31623 19.4425 7.41373 19.5959 7.55309 19.696C7.64066 19.7589 7.74815 19.7843 7.85406 19.8046C9.35884 20.0925 10.8609 20.0456 12.2845 19.7729C12.5371 19.6923 12.7055 19.4907 12.7055 19.2891V18.7346C12.7055 17.8836 13.2568 17.1454 14.0002 16.7312C14.0496 16.7037 14.0988 16.6757 14.1476 16.6472C14.8377 16.2446 15.6883 16.1461 16.393 16.5227L17.0422 16.8698C17.2527 16.9907 17.5053 16.9504 17.6738 16.7891C18.7264 15.7005 19.4842 14.4102 19.9895 12.9586C20.0316 12.757 19.9474 12.515 19.7369 12.3941ZM10.0109 13.2005C8.1162 13.2005 6.64257 11.7893 6.64257 9.97478C6.64257 8.20063 8.1162 6.74905 10.0109 6.74905C11.8634 6.74905 13.3792 8.20063 13.3792 9.97478C13.3792 11.7893 11.8634 13.2005 10.0109 13.2005Z" fill="#2A7BE4"></path>
                  </svg></span></span></span></div>
        </div><small class="text-uppercase text-body-tertiary fw-bold py-2 pe-2 ps-1 rounded-end">customize</small>
      </div>
    </a>
    <!-- ===============================================-->
    <!--   TODOS LOS SCRIPTS DE GOBER360-->
    <!-- ===============================================-->
    <?php include 'admin/include/scriptsgober360.php'; ?>
  </body>

</html>