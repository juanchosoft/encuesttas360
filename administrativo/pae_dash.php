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
    include './admin/classes/MainPae.php';


    // Obtener permisos
    $permissions = [
        'view' => SessionData::getPermission(70),
        'create' => SessionData::getPermission(71),
        'edit' => SessionData::getPermission(72),
    ];

    // Validación de permiso de visualización
    if (!$permissions['view']) {
        require_once 'permiso_denegado.php';
        exit;
    }


    // Información de Departamentos
    $arrDep = Departamento::getAll(null);
    $isvalid = $arrDep['output']['valid'];
    $arrDep = $arrDep['output']['response'];
    $optionDep = Util::getDepartamentoPrincipal();
    foreach ($arrDep as $val) {
        $optionDep .= "<option " . ($val["codigo_departamento"] == Util::getDepartamentoPrincipal() ? "selected" : "") . " value='" . $val['codigo_departamento'] . "'>" . $val['codigo_departamento'] . " - " . $val['departamento'] . "</option>";
    }

    $codigoMunicipio = $_REQUEST['mun'];
    $parametrosPae = ['codigoMunicipio' => $codigoMunicipio, 'departamentoId' => Util::getDepartamentoPrincipal()];

    //informacion del mail
    $arr = MainPae::getDataMain($parametrosPae);
    $isvalid = $arr['output']['valid'];
    $variables = [
        'disposicion_desechos_pae_enterrado',
        'disposicion_desechos_pae_quemado',
        'disposicion_desechos_pae_reciclan',
        'disposicion_desechos_pae_lombricultura',
        'disposicion_desechos_pae_tiran_lote',
        'disposicion_no_organicos_pae_enterrado',
        'disposicion_no_organicos_pae_quemado',
        'disposicion_no_organicos_pae_reciclan',
        'disposicion_no_organicos_pae_lombricultura',
        'disposicion_no_organicos_pae_tiran_lote',
        'disposicion_no_organicos_pae_otros',
        'posee_ollas_pae_si',
        'posee_ollas_pae_no',
        'posee_cuchillos_pae_si',
        'posee_cuchillos_pae_no',
        'tamano_neveras_principales_nevera_domestica_vertical_2200l',
        'tamano_neveras_principales_nevera_domestica_vertical_1200l',
        'tamano_neveras_principales_nevera_domestica_vertical_400_800L',
        'tamano_neveras_principales_nevera_domestica_vertical_menor_400L',
        'tamano_neveras_principales_nevera_domestica_vertical_otra',
        'tamano_congelador_Congelador_Grande_1400_1600L',
        'tamano_congelador_Congelador_Pequeño_Menor_400L',
        'ninos_foc',
        'neveras',
        'neveras_fun',
        'neveras_buenas',
        'neveras_almacenamiento_si',
        'neveras_almacenamiento_no',
        'congeladores',
        'congeladores_funcionando',
        'estufas',
        'quemadores_estufas',
        'quemadores_estufas_buenos',
        'estufas_gen',
        'licuadoras_industriales',
        'licuadoras_total',
        'licuadoras',
        'cantidad_platos',
        'cantidad_cucharas',
        'cantidad_pocillos',
        'cantidad_tenedores',
        'cantidad_canecas',
        'acceso_alcantarillado_si',
        'acceso_alcantarillado_no',
        'recoleccion_basuras_si',
        'recoleccion_basuras_no',
        'espacio_preparacion_si',
        'espacio_preparacion_no',
        'espacio_almacenamiento_no',
        'espacio_almacenamiento_si',
        'zona_conflicto_si',
        'zona_conflicto_no',
        'algo_frecuente_conflicto',
        'no_frecuencia_conflicto',
        'poco_frecuente_conflicto',
        'cercania_contaminacion_si',
        'cercania_contaminacion_no',
        'concepto_sanitario_si',
        'concepto_sanitario_no',
        'complemento_preparado_sitio_si',
        'complemento_preparado_sitio_no',
        'complemento_industrializado_si',
        'complemento_industrializado_no',
        'almuerzo_preparado_sitio_si',
        'almuerzo_preparado_sitio_no',
        'almuerzo_trasportado_no',
        'almuerzo_trasportado_si',
        'lavamanos_personal_si',
        'lavamanos_personal_no',
        'sanitario_personal_si',
        'sanitario_personal_no',
        'almacenamiento_personal_si',
        'almacenamiento_personal_no',
        'caracterizaciones',
        'zona_rural',
        'zona_urbana',
        'acceso_agua_si',
        'acceso_agua_no',
        'acceso_agua_intermitente',
        'almacena_alto_suelo_si',
        'almacena_alto_suelo_no',
        'almacena_balde',
        'almacena_canasta',
        'almacena_estante',
        'almacena_ninguno',
        'almacena_na',
        'acueducto',
        'embotellada',
        'lluvia',
        'carrotanque',
        'rios_quebradas',
        'otros_agua',
        'pozo_agua',
        'acceso_electricidad_si',
        'acceso_electricidad_no',
        'acceso_electricidad_intermitente',
        'electricidad',
        'gas_natural',
        'lena',
        'desecho',
        'no_aplica',
        'petroleo_gasolina',
        'comedor_escolar_si',
        'comedor_escolar_no',
        'no_tiene_concepto',
        'si_tiene_favorable',
        'si_favorable_requerimientos',
        'si_desfavorable',
        'estado_sede_antiguo_activo',
        'estado_sede_nuevo_activo',
        'estado_sede_cierre_temporal',
        'estado_techo_almacenamiento_bueno',
        'estado_techo_almacenamiento_malo',
        'estado_techo_almacenamiento_regular',
        'estado_paredes_bueno',
        'estado_paredes_regular',
        'estado_paredes_malo',
        'material_paredes_preparacion_ladrillo',
        'material_paredes_preparacion_prefabricado',
        'material_paredes_preparacion_otros',
        'material_paredes_preparacion_bahareque',
        'estado_piso_bueno',
        'estado_piso_regular',
        'estado_piso_malo',
        'material_piso_preparacion_baldosa',
        'material_piso_cemento',
        'material_piso_ladrillo',
        'material_piso_preparacion_madera',
        'material_piso_preparacion_otros',
        'estado_techo_bueno',
        'estado_techo_regular',
        'estado_techo_malo',
        'material_techo_preparacion_zinc',
        'material_techo_eternit',
        'material_techo_teja_barro',
        'material_techo_preparacion_plastico',
        'material_techo_preparacion_sin_techo',
        'material_techo_preparacion_concreto',
        'material_techo_preparacion_metal_acero',
        'material_techo_preparacion_paja',
        'material_techo_preparacion_otros',
        'estado_paredes_almacenamiento_bueno',
        'estado_paredes_almacenamiento_regular',
        'estado_paredes_almacenamiento_malo',
        'material_paredes_almacenamiento_bloque',
        'material_paredes_almacenamiento_bahareque',
        'material_paredes_almacenamiento_prefabricado',
        'material_paredes__almacenamiento_madera',
        'material_paredes_almacenamiento_otros',
        'estado_piso_almacenamiento_bueno',
        'estado_piso_almacenamiento_regular',
        'estado_piso_almacenamiento_malo',
        'material_piso_almacenamiento_bloque',
        'material_piso_almacenamiento_cemento',
        'material_piso_almacenamiento_ladrillo',
        'material_piso_almacenamiento_madera',
        'material_piso_almacenamiento_otros',
        'material_piso_almacenamiento_baldosa',
        'estado_techo_almacenamiento_bueno',
        'estado_techo_almacenamiento_regular',
        'estado_techo_almacenamiento_malo',
        'material_techo_almacenamiento_eternit',
        'material_techo_almacenamiento_tejas',
        'material_techo_almacenamiento_plastico',
        'material_techo_almacenamiento_zinc',
        'material_techo_almacenamiento_concreto',
        'material_techo_almacenamiento_otros',
        'material_techo_almacenamiento_metal',
        'posee_cucharones_pae_si',
        'posee_cucharones_pae_no'
    ];


    foreach ($variables as $variable) {
        $$variable = isset($arr['output'][$variable]) ? $arr['output'][$variable] : 0;
    }


    //calculos dashboard
    function calcular_porcentaje($valor, $total)
    {
        return $total > 0 ? ($valor * 100) / $total : 0;
    }

    $neveras_malas = $neveras - $neveras_fun;
    $porcentaje_neveras = calcular_porcentaje($neveras_fun, $neveras);

    $congeladores_malas = $congeladores - $congeladores_funcionando;
    $porcentaje_congeladores = calcular_porcentaje($congeladores_funcionando, $congeladores);

    $quemadores_malas = $quemadores_estufas - $quemadores_estufas_buenos;
    $porcentaje_quemadores = calcular_porcentaje($quemadores_estufas_buenos, $quemadores_estufas);

    $total_licuadoras = $licuadoras_total + $licuadoras_industriales;
    $licuadoras_malas = $licuadoras_industriales - $licuadoras;
    $porcentaje_licuadoras = calcular_porcentaje($licuadoras, $licuadoras_industriales);

    $porcentaje_alm_no = calcular_porcentaje($espacio_almacenamiento_no, $caracterizaciones);
    $porcentaje_alm_si = calcular_porcentaje($espacio_almacenamiento_si, $caracterizaciones);

    $porcentaje_prepa_si = calcular_porcentaje($espacio_preparacion_si, $caracterizaciones);
    $porcentaje_prepa_no = calcular_porcentaje($espacio_preparacion_no, $caracterizaciones);

    $porcentaje_prepa_sitio_si = calcular_porcentaje($almuerzo_preparado_sitio_si, $caracterizaciones);
    $porcentaje_prepa_sitio_no = calcular_porcentaje($almuerzo_preparado_sitio_no, $caracterizaciones);

    $porcentaje_transporte_almuer_si = calcular_porcentaje($almuerzo_trasportado_si, $caracterizaciones);
    $porcentaje_transporte_almuer_no = calcular_porcentaje($almuerzo_trasportado_no, $caracterizaciones);

    $porcentaje_complemento_prepa_sitio_si = calcular_porcentaje($complemento_preparado_sitio_si, $caracterizaciones);
    $porcentaje_complemento_prepa_sitio_no = calcular_porcentaje($complemento_preparado_sitio_no, $caracterizaciones);

    $porcentaje_complemento_industri_sitio_si = calcular_porcentaje($complemento_industrializado_si, $caracterizaciones);
    $porcentaje_complemento_industri_sitio_no = calcular_porcentaje($complemento_industrializado_no, $caracterizaciones);

    $porcentaje_armado_no_frecuente = calcular_porcentaje($no_frecuencia_conflicto, $caracterizaciones);
    $porcentaje_armado_poco = calcular_porcentaje($poco_frecuente_conflicto, $caracterizaciones);
    $porcentaje_armado_algo = calcular_porcentaje($algo_frecuente_conflicto, $caracterizaciones);

    $porcentaje_cercania_contaminacion_si = calcular_porcentaje($cercania_contaminacion_si, $caracterizaciones);
    $porcentaje_cercania_contaminacion_no = calcular_porcentaje($cercania_contaminacion_no, $caracterizaciones);

    $porcentaje_acceso_agua_si = calcular_porcentaje($acceso_agua_si, $caracterizaciones);
    $porcentaje_acceso_agua_no = calcular_porcentaje($acceso_agua_no, $caracterizaciones);
    $porcentaje_acceso_agua_intermitente = calcular_porcentaje($acceso_agua_intermitente, $caracterizaciones);

    $porcentaje_zona_conflicto_si = calcular_porcentaje($zona_conflicto_si, $caracterizaciones);
    $porcentaje_zona_conflicto_no = calcular_porcentaje($zona_conflicto_no, $caracterizaciones);

    $porcentaje_almacena_alto_suelo_si = calcular_porcentaje($almacena_alto_suelo_si, $caracterizaciones);
    $porcentaje_almacena_alto_suelo_no = calcular_porcentaje($almacena_alto_suelo_no, $caracterizaciones);

    $porcentaje_acceso_electricidad_si = calcular_porcentaje($acceso_electricidad_si, $caracterizaciones);
    $porcentaje_acceso_electricidad_no = calcular_porcentaje($acceso_electricidad_no, $caracterizaciones);
    $porcentaje_acceso_electricidad_intermitente = calcular_porcentaje($acceso_electricidad_intermitente, $caracterizaciones);

    $porcentaje_comedor_escolar_si = calcular_porcentaje($comedor_escolar_si, $caracterizaciones);
    $porcentaje_comedor_escolar_no = calcular_porcentaje($comedor_escolar_no, $caracterizaciones);

    $porcentaje_estado_sede_antiguo_activo = calcular_porcentaje($estado_sede_antiguo_activo, $caracterizaciones);
    $porcentaje_estado_sede_nuevo_activo = calcular_porcentaje($estado_sede_nuevo_activo, $caracterizaciones);
    $porcentaje_estado_sede_cierre_temporal = calcular_porcentaje($estado_sede_cierre_temporal, $caracterizaciones);

    $porcentaje_estado_techo_almacenamiento_bueno = calcular_porcentaje($estado_techo_almacenamiento_bueno, $caracterizaciones);
    $porcentaje_estado_techo_almacenamiento_malo = calcular_porcentaje($estado_techo_almacenamiento_malo, $caracterizaciones);
    $porcentaje_estado_techo_almacenamiento_regular = calcular_porcentaje($estado_techo_almacenamiento_regular, $caracterizaciones);

    $porcentaje_estado_paredes_bueno = calcular_porcentaje($estado_paredes_bueno, $caracterizaciones);
    $porcentaje_estado_paredes_malo = calcular_porcentaje($estado_paredes_malo, $caracterizaciones);
    $porcentaje_estado_paredes_regular = calcular_porcentaje($estado_paredes_regular, $caracterizaciones);

    $porcentaje_estado_piso_bueno = calcular_porcentaje($estado_piso_bueno, $caracterizaciones);
    $porcentaje_estado_piso_malo = calcular_porcentaje($estado_piso_malo, $caracterizaciones);
    $porcentaje_estado_piso_regular = calcular_porcentaje($estado_piso_regular, $caracterizaciones);

    $porcentaje_estado_techo_bueno = calcular_porcentaje($estado_techo_bueno, $caracterizaciones);
    $porcentaje_estado_techo_malo = calcular_porcentaje($estado_techo_malo, $caracterizaciones);
    $porcentaje_estado_techo_regular = calcular_porcentaje($estado_techo_regular, $caracterizaciones);

    $porcentaje_estado_paredes_almacenamiento_bueno = calcular_porcentaje($estado_paredes_almacenamiento_bueno, $caracterizaciones);
    $porcentaje_estado_paredes_almacenamiento_regular = calcular_porcentaje($estado_paredes_almacenamiento_regular, $caracterizaciones);
    $porcentaje_estado_paredes_almacenamiento_malo = calcular_porcentaje($estado_paredes_almacenamiento_malo, $caracterizaciones);

    $porcentaje_estado_piso_almacenamiento_bueno = calcular_porcentaje($estado_piso_almacenamiento_bueno, $caracterizaciones);
    $porcentaje_estado_piso_almacenamiento_regular = calcular_porcentaje($estado_piso_almacenamiento_regular, $caracterizaciones);
    $porcentaje_estado_piso_almacenamiento_malo = calcular_porcentaje($estado_piso_almacenamiento_malo, $caracterizaciones);

    $porcentaje_posee_ollas_pae_si = calcular_porcentaje($posee_ollas_pae_si, $caracterizaciones);
    $porcentaje_posee_ollas_pae_no = calcular_porcentaje($posee_ollas_pae_no, $caracterizaciones);

    $porcentaje_posee_cuchillos_pae_si = calcular_porcentaje($posee_cuchillos_pae_si, $caracterizaciones);
    $porcentaje_posee_cuchillos_pae_no = calcular_porcentaje($posee_cuchillos_pae_no, $caracterizaciones);

    $porcentaje_posee_cucharones_pae_si = calcular_porcentaje($posee_cucharones_pae_si, $caracterizaciones);
    $porcentaje_posee_cucharones_pae_no = calcular_porcentaje($posee_cucharones_pae_no, $caracterizaciones);

    $porcentaje_cant_ninos_pae_sentados_todos = calcular_porcentaje($cant_ninos_pae_sentados_todos, $caracterizaciones);
    $porcentaje_cant_ninos_pae_mas_75 = calcular_porcentaje($cant_ninos_pae_mas_75, $caracterizaciones);



    // Valores en porcentaje (0–100)
    $valor  = $porcentaje_posee_cucharones_pae_no;
    $valor1 = $porcentaje_posee_cuchillos_pae_no;
    $valor2 = $porcentaje_posee_ollas_pae_no;
    $valor3 = $almacena_ninguno;
    $valor4 = $porcentaje_almacena_alto_suelo_no;
    $valor5 = $porcentaje_estado_techo_almacenamiento_malo;
    $valor6 = $porcentaje_estado_paredes_malo;
    $valor7 = $porcentaje_estado_piso_almacenamiento_malo;
    $valor8 = $porcentaje_estado_techo_malo;
    $valor9 = $porcentaje_estado_paredes_almacenamiento_malo;
    $valor10 = $porcentaje_estado_piso_malo;
    $valor11 = $porcentaje_acceso_agua_intermitente;
    $valor12 =  $porcentaje_acceso_electricidad_intermitente;
    $valor13 =  $porcentaje_prepa_sitio_no;
    $valor14 = $porcentaje_complemento_prepa_sitio_no;
    $valor15 = $porcentaje_complemento_industri_sitio_no;
    $valor16 = $porcentaje_comedor_escolar_no;
    $valor17 = $no_tiene_concepto;
    $valor18 = $porcentaje_cant_ninos_pae_mas_75;
    $valor19 = $porcentaje_estado_sede_antiguo_activo;
    $valor20 = $estado_techo_almacenamiento_malo;


    // Función para determinar la clase según el valor bien si esta bajito
    function getColorClass($valor)
    {
        if ($valor >= 1 && $valor <= 20) {
            return 'bg-success text-white'; // Verde
        } elseif ($valor >= 21 && $valor <= 35) {
            return 'bg-warning text-dark';  // Amarillo
        } elseif ($valor >= 36 && $valor <= 60) {
            return 'bg-orange text-white';  // Naranja (agregar clase personalizada)
        } elseif ($valor >= 61 && $valor <= 1500) {
            return 'bg-danger text-white';  // Rojo
        } else {
            return ''; // Sin clase si está fuera de rango
        }
    }

    // Asignar clases de color por cada valor
    $colorClase  = getColorClass($valor);
    $colorClase1 = getColorClass($valor1);
    $colorClase2 = getColorClass($valor2);
    $colorClase3 = getColorClass($valor3);
    $colorClase4 = getColorClass($valor4);
    $colorClase5 = getColorClass($valor5);
    $colorClase6 = getColorClass($valor6);
    $colorClase7 = getColorClass($valor7);
    $colorClase8 = getColorClass($valor8);
    $colorClase9 = getColorClass($valor9);
    $colorClase10 = getColorClass($valor10);
    $colorClase11 = getColorClass($valor11);
    $colorClase12 = getColorClass($valor12);
    $colorClase13 = getColorClass($valor13);
    $colorClase14 = getColorClass($valor14);
    $colorClase15 = getColorClass($valor15);
    $colorClase16 = getColorClass($valor16);
    $colorClase17 = getColorClass($valor17);
    $colorClase18 = getColorClass($valor18);
    $colorClase19 = getColorClass($valor19);
    $colorClase20 = getColorClass($valor20);
    //=========================================================================//==================================

    // Función para asignar clase de color mal si esta bajito

    function getColorClassb($valora)
    {
        if ($valora >= 1 && $valora <= 20) {
            return 'bg-danger text-white'; // Rojo
        } elseif ($valora >= 21 && $valora <= 35) {
            return 'bg-orange text-white'; // Naranja
        } elseif ($valora >= 36 && $valora <= 60) {
            return 'bg-warning text-dark'; // Amarillo
        } elseif ($valora >= 61 && $valora <= 100) {
            return 'bg-success text-white'; // Verde
        }
        return ''; // Por si el valor está fuera de rango
    }

    // Definir valores
    $valora  = $porcentaje_neveras;
    $valora1 = $porcentaje_congeladores;
    $valora2 = $porcentaje_quemadores;
    $valora3 = $porcentaje_licuadoras;
    $valora4 = $porcentaje_alm_no;
    $valora5 = $porcentaje_prepa_no;
    $valora9 = $porcentaje_transporte_almuer_no;
    $valora10 = $porcentaje_cercania_contaminacion_no;
    $valora11 = $porcentaje_zona_conflicto_no;
    $valora12 = $porcentaje_armado_no_frecuente;


    // Aplicar colores a cada uno
    $colorClasea = getColorClassb($valora);
    $colorClasea1 = getColorClassb($valora1);
    $colorClasea2 = getColorClassb($valora2);
    $colorClasea3 = getColorClassb($valora3);
    $colorClasea4 = getColorClassb($valora4);
    $colorClasea5 = getColorClassb($valora5);
    $colorClasea6 = getColorClassb($valora6);
    $colorClasea7 = getColorClassb($valora7);
    $colorClasea8 = getColorClassb($valora8);
    $colorClasea9 = getColorClassb($valora9);
    $colorClasea10 = getColorClassb($valora10);
    $colorClasea11 = getColorClassb($valora11);
    $colorClasea12 = getColorClassb($valora12);

    $departamento = new Departamento();
    $santander = $departamento->getAll(["id" => 21]);
    $santander = $santander["output"]["response"]["0"];
    $code = Util::getDepartamentoPrincipal();
    $mapa = null;

    if (!is_null($code)) {
        $arr = Ciudad::getAll(array('codigo_departamento' => $code));
        $finalMunicipios = $arr['output']['response'];
        $arrApoyoDep = Ciudad::getApoyoByCodigoDepartamento(array('codigo_departamento' => $code));
    }
    ?>
<body class="dashboard-body">
    <style>
        .bg-orange {
            background-color: #fd7e14 !important;
            /* Bootstrap orange-500 */
            color: #fff !important;
        }
        .nav-link {
    color: #333;
    background-color: transparent;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    text-decoration: none;
}

.nav-link:hover {
    background-color: #e0e0e0;
    color: #000;
}

.nav-link.active {
    /* background-color:rgb(10, 78, 26); Verde profesional tipo Bootstrap */
    color: #fff;
    font-weight: bold;
}
.santander img {
    display: block;
    margin: 0 auto;
    max-width: 100%;
}
    </style>
<?php include 'admin/include/scriptsgober360.php'; ?>
    <?php
    include './admin/include/navbar.php';
    ?>
    <!-- [ navigation menu ] end -->
    <!-- [ Header ] start -->
    <?php
    include './admin/include/header.php';
    ?>
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="content">
        <div>
          <div class="col-11 col-xl-11 mx-auto">
            <div class="card shadow-none border my-4" data-component-card="data-component-card">
              <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                  <div class="col-12 col-md">
                        <h4 class="text-body mb-0 d-flex align-items-center" ><i class="uil uil-monitor fs-6"></i>Dashboard Pae</h4>
                  </div>
                </div>
              </div>

                <div class="row">
                    <div class="col-md-12 col-xl-12">
                        <div id="containerDataPae" name="containerDataPae">
                            <div class="row">
                            <div class="card-body">
                                <h4 class="text-center mb-4" style="font-size: 18px;">
                                    <i style="color:red" class="fas fa-map-marker-alt mr-2"></i> Filtrar por Municipios
                                </h4>
                                    <input type="hidden" name="op" id="op" />
                                    <input type="hidden" name="id" id="id" />
                                    <input type="hidden" name="filtro" id="filtro" value="vereda" />
                                    <input type="hidden" name="filtroVeredaById" id="filtroVeredaById" value="si" />
                                    <!-- INICIO SELECT DEP Y MUNICIPIO -->
                                    <div class="row g-3 mx-0 px-2">
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-floating">
                                                <select onchange="DEPARTAMENTO.getMunicipios();" class="form-control" id="tbl_departamento_id" name="tbl_departamento_id">
                                                    <?php echo $optionDep; ?>
                                                </select>
                                                <label for="validationCustom05">Departamento<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-floating">
                                                <select class="form-control" id="tbl_municipio_id" onchange="PAE_DASHBOARD.updateUrlMunicipio(this);" name="tbl_municipio_id">
                                                </select>
                                                <label for="validationCustom05">Municipio<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- FIN SELECT DEP Y MUNICIPIO -->
                                </div>
                                <!-- Sección de informacion caracterización PAE -->
                                <div class="col-md-12 col-xl-12">
                                    <div class="px-3 mb-5">
                                        <div class="row justify-content-between mb-4">
                                            <div class="col-6 text-center border-translucent border-end pb-4">
                                                <span class="uil fs-5 lh-1 uil-file-search-alt text-primary"></span>
                                                <h5>Total Sedes Caracterizadas</h5>
                                                <h2><?php echo number_format($caracterizaciones, 0); ?></h2>
                                            </div>
                                            <div class="col-6 text-center border-translucent border-end pb-4">
                                                <span class="uil fs-5 lh-1 uil-mountains text-primary"></span>
                                                <h5>Caracterizaciones Zona Rural</h5>
                                                <h2><?php echo number_format($zona_rural, 0); ?></b></h2>
                                            </div>
                                            <hr>
                                            <div class="col-6 text-center border-translucent border-end pb-4">
                                                <span class="uil fs-5 lh-1 uil-building text-primary"></span>
                                                <h5>Caracterizaciones Zona Urbana</h5>
                                                <h2><?php echo number_format($zona_urbana, 0); ?></b></h2>
                                            </div>
                                            <div class="col-6 text-center border-translucent border-end pb-4">
                                                <span class="uil fs-5 lh-1 uil-kid text-primary"></span>
                                                <h5>Niños Focalizados</h5>
                                                <h2><?php echo number_format($ninos_foc, 0); ?></b></h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Fin seccion de informacion caracterización PAE -->
                                 <!-- INICIO DE SECCION DE OPCIONES PAE -->
                                
                                <div class="row">
                                    <div class="col-sm-auto">
                                        <div class="sticky-top" style="margin-top:-72px; padding-top:72px; margin-left: 70px">
                                        <div class="nav nav-tabs" id="v-pills">
                                        <a class="nav-link ps-0 ps-sm-3 active" id="btn-seccion1" href="javascript:void(0);" onclick="mostrarSeccion('seccion1')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.1 13.34l2.83-2.83L3.91 3.5c-.78-.78-2.05-.78-2.83 0-.78.78-.78 2.05 0 2.83l7.02 7.01zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L9.7 14.7l.71.71 4.27-4.27z"/>
                                                <path d="M2.1 11.05l.69.69L11.05 2.1l-.69-.69c-.78-.78-2.05-.78-2.83 0L2.1 8.22c-.78.78-.78 2.05 0 2.83z"/>
                                            </svg>
                                            Utensilios de Restaurante
                                        </a>
                                        <a class="nav-link ps-0 ps-sm-3" id="btn-seccion2" href="javascript:void(0);" onclick="mostrarSeccion('seccion2')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M18 2.01L6 2c-1.1 0-2 .89-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.11-.9-1.99-2-1.99zM18 20H6V4h12v16z"/>
                                                <circle cx="8" cy="6.5" r="1.5"/>
                                                <circle cx="16" cy="6.5" r="1.5"/>
                                                <circle cx="12" cy="6.5" r="1.5"/>
                                                <rect x="7" y="10" width="10" height="6" rx="1"/>
                                            </svg>
                                        Elementos Cocina
                                        </a>
                                        <a class="nav-link ps-0 ps-sm-3" id="btn-seccion3" href="javascript:void(0);" onclick="mostrarSeccion('seccion3')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5h3V9h4v3h3l-5 5z"/>
                                                        <circle cx="12" cy="6" r="1"/>
                                                    </svg>
                                        Estado Lugares de Preparación y Almacenamiento
                                        </a>
                                        <a class="nav-link ps-0 ps-sm-3" id="btn-seccion4" href="javascript:void(0);" onclick="mostrarSeccion('seccion4')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 4h5v5H4V4zm6 0h5v5h-5V4zm6 0h4v5h-4V4zM4 10h5v5H4v-5zm6 0h5v5h-5v-5zm6 0h4v5h-4v-5zM4 16h5v4H4v-4zm6 0h5v4h-5v-4zm6 0h4v4h-4v-4z"/>
                                                <circle cx="6.5" cy="6.5" r="1"/>
                                                <circle cx="12.5" cy="6.5" r="1"/>
                                                <circle cx="18" cy="6.5" r="1"/>
                                            </svg>
                                        Materiales Preparación y Almacenamiento PAE
                                        </a>

                                        <a class="nav-link ps-0 ps-sm-3" id="btn-seccion5" href="javascript:void(0);" onclick="mostrarSeccion('seccion5')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12,3L2,12H5V20H19V12H22L12,3M12,8.75A2.25,2.25 0 0,1 14.25,11A2.25,2.25 0 0,1 12,13.25A2.25,2.25 0 0,1 9.75,11A2.25,2.25 0 0,1 12,8.75M12,15C13.11,15 14.11,15.45 14.83,16.17L16.24,14.76C15.22,13.74 13.66,13 12,13C10.34,13 8.78,13.74 7.76,14.76L9.17,16.17C9.89,15.45 10.89,15 12,15Z" />
                                            </svg>
                                        Acceso a Servicios Públicos y Tipo
                                        </a>

                                        <a class="nav-link ps-0 ps-sm-3" id="btn-seccion6" href="javascript:void(0);" onclick="mostrarSeccion('seccion6')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12,2A2,2 0 0,1 14,4A2,2 0 0,1 12,6A2,2 0 0,1 10,4A2,2 0 0,1 12,2M21,9V7L15,1H9L3,7V9H21M21,10H3V15H5V20A1,1 0 0,0 6,21H10A1,1 0 0,0 11,20V15H13V20A1,1 0 0,0 14,21H18A1,1 0 0,0 19,20V15H21V10Z" />
                                            </svg>
                                        Restaurante PAE
                                        </a>
                                        <a class="nav-link ps-0 ps-sm-3" id="btn-seccion7" href="javascript:void(0);" onclick="mostrarSeccion('seccion7')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z" />
                                            </svg>
                                        Estado Sedes Educativas
                                        </a>
                                        </div>
                                        </div>
                                    </div>

                            <div class="row">
                                <div class="col-sm-12">
                                <div id="seccion1" class="seccion" >

                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <img src="assets/img/utensilios.png" alt="Restaurante" width="130px">
                                                <h5 class="card-title mt-2">Utensilios de Restaurante</h5>
                                            </div>

                                            <!-- Cards internas organizadas horizontalmente -->
                                            <div class="row justify-content-center g-3">

                                                
                                                <!-- CARD: Posesión Cucharones -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Porcentaje Cucharones PAE</h4>
                                                                
                                                                <!-- Primer gráfico: cantidad -->
                                                                <div style="display:none"  id="graficoCucharonesContainer"></div>
                                                            
                                                                <br>
                                                                <!-- <h4 class="card-title">Porcentaje</h4> -->
                                                                <!-- Segundo gráfico: porcentaje -->
                                                                <div id="graficoCucharonesPorcentajeContainer" style="margin-top: 20px;"></div>

                                                                <div style="margin-top: 20px;">
                                                                    <h6 class="card-text">
                                                                        Instituciones que sí tienen:
                                                                        <b id="posee_cucharones_pae_si"><?php echo number_format($posee_cucharones_pae_si, 0, ',', '.'); ?></b><br>
                                                                        Instituciones que no tienen:
                                                                        <b id="posee_cucharones_pae_no"><?php echo number_format($posee_cucharones_pae_no, 0, ',', '.'); ?></b>
                                                                    </h6>
                                                                    <hr>
                                                                    <h6 style="display:none" class="card-text">
                                                                        <b>Porcentaje:</b><br><br>
                                                                        Sí:
                                                                        <b id="porcentaje_posee_cucharones_pae_si"><?php echo number_format($porcentaje_posee_cucharones_pae_si, 2, ',', '.'); ?>%</b><br>
                                                                        No:
                                                                        <div class="p-2 rounded <?= $colorClase; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase; ?>">
                                                                                <b id="porcentaje_posee_cucharones_pae_no"><?= number_format($valor, 2, ',', '.'); ?>%</b>
                                                                            </div>
                                                                        </div>
                                                                    </h6>
                                                                </div>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: Posesión cuchillos PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Porcentaje Posesión Cuchillos PAE</h4>

                                                                <!-- Gráfica de cantidad -->
                                                                <div style="display:none" id="graficoCuchillosContainer"></div>

                                                                <br>

                                                                <!-- Gráfica de porcentaje -->
                                                                <div id="graficoCuchillosPorcentajeContainer" style="margin-top: 20px;"></div>

                                                                <div style="margin-top: 20px;">
                                                                    <h6 class="card-text">
                                                                        Sí:
                                                                        <b id="posee_cuchillos_pae_si"><?php echo number_format($posee_cuchillos_pae_si, 0, ',', '.'); ?></b><br>
                                                                        No:
                                                                        <b id="posee_cuchillos_pae_no"><?php echo number_format($posee_cuchillos_pae_no, 0, ',', '.'); ?></b>
                                                                    </h6>
                                                                    <hr>
                                                                    <h6  style="display:none" class="card-text">
                                                                        <b>Porcentaje:</b><br><br>
                                                                        Sí:
                                                                        <b id="porcentaje_posee_cuchillos_pae_si"><?php echo number_format($porcentaje_posee_cuchillos_pae_si, 2, ',', '.'); ?>%</b><br>
                                                                        No:
                                                                        <div class="p-2 rounded <?= $colorClase1; ?>" style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase1; ?>">
                                                                                <b id="porcentaje_posee_cuchillos_pae_no"><?= number_format($valor, 2, ',', '.'); ?>%</b>
                                                                            </div>
                                                                        </div>
                                                                    </h6>
                                                                </div>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h5 class="card-title">Porcentaje Posesión ollas PAE</h5>
                                                                <!-- Gráfica cantidad -->
                                                                <div style="display:none" id="graficoOllasContainer"></div>
                                                                <br>
                                                                <!-- Gráfica porcentaje -->
                                                                <div id="graficoOllasPorcentajeContainer" style="margin-top: 20px;"></div>

                                                                <div style="margin-top: 20px;">
                                                                    <h6 class="card-text">
                                                                        Sí:
                                                                        <b id="posee_ollas_pae_si"><?php echo number_format($posee_ollas_pae_si, 0, ',', '.'); ?></b><br>
                                                                        No:
                                                                        <b id="posee_ollas_pae_no"><?php echo number_format($posee_ollas_pae_no, 0, ',', '.'); ?></b>
                                                                    </h6>
                                                                    <hr>
                                                                    <h6 style="display:none" class="card-text">
                                                                        <b>Porcentaje:</b><br><br>
                                                                        Sí:
                                                                        <b id="porcentaje_posee_ollas_pae_si"><?php echo number_format($porcentaje_posee_ollas_pae_si, 2, ',', '.'); ?>%</b><br>
                                                                        No:
                                                                        <div class="p-2 rounded <?= $colorClase1; ?>" style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase2; ?>">
                                                                                <b id="porcentaje_posee_ollas_pae_no"><?= number_format($valor, 2, ',', '.'); ?>%</b>
                                                                            </div>
                                                                        </div>
                                                                    </h6>
                                                                </div>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- CARD: Menaje -->
                                                <div class="col-12 col-sm-6 col-md-2 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body text-start">
                                                        <center>
                                                            <h4 class="card-title">Menaje</h4>
                                                            <div id="graficoMenajeContainer"></div>
                                                            <br>
                                                            <div>
                                                                <h6  class="card-text">Platos: <b id="cantidad_platos"><?php echo number_format($cantidad_platos, 0, ',', '.'); ?></b></h6>
                                                                <h6 class="card-text">Cucharas: <b id="cantidad_cucharas"><?php echo number_format($cantidad_cucharas, 0, ',', '.'); ?></b></h6>
                                                                <h6 class="card-text">Tenedores: <b id="cantidad_tenedores"><?php echo number_format($cantidad_tenedores, 0, ',', '.'); ?></b></h6>
                                                                <h6 class="card-text">Pocillos: <b id="cantidad_pocillos"><?php echo number_format($cantidad_pocillos, 0, ',', '.'); ?></b></h6>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                        </center>
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>
                                    </div>
                                    </div> 
    <!-- INICIIO DE LA SECCION 2 -->
                                    <div class="col-sm-12">
                                        <div id="seccion2" class="seccion" style="display:none;" class="card">
                                            <div class="card-body">
                                                <div class="text-center mb-4">
                                                    <img src="assets/img/tosta.png" alt="Restaurante" width="150px">
                                                    <h5 class="card-title mt-2">Elementos Cocinas</h5>
                                                </div>
                                                <!-- Cards internas organizadas horizontalmente -->
                                                <div class="row justify-content-center g-3">
                                                    <!-- Card: neveras PAE -->
                                                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="card h-100 shadow-sm">
                                                            <div class="card-body">
                                                                <center>
                                                                    <h4 class="card-title">Neveras</h4>
                                                                    <div id="graficoNeverasContainer" style="margin-top: 20px;"></div>
                                                                    <h6 class="card-text">Total en General: <b>
                                                                            <?php echo $neveras; ?></b></h6>
                                                                    <h6 class="card-text">Total Funcionando: <b>
                                                                            <?php echo $neveras_fun; ?></b></h6>
                                                                    <h6 class="card-text">Total Malas:
                                                                        <b><?php echo $neveras_malas; ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Porcentaje Buenas: <b>
                                                                            <div class="p-2 rounded <?= $colorClasea; ?>"
                                                                                style="display: inline-block; min-width: 100px; text-align: center;">
                                                                                <div
                                                                                    class="p-2 rounded <?= $colorClasea; ?>">
                                                                                    <b><?= number_format($valora, 2); ?>%</b>
                                                                                </div>
                                                                        </b>
                                                                    </h6>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Card: congeladores PAE -->
                                                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="card h-100 shadow-sm">
                                                            <div class="card-body">
                                                                <center>
                                                                    <h4 class="card-title">Congeladores</h4>
                                                                    <!-- Gráfico para congeladores -->
                                                                    <div id="graficoCongeladoresContainer" style="margin-top: 20px;"></div>

                                                                    <h6 class="card-text">Total en General: <b>
                                                                            <?php echo $congeladores; ?></b></h6>
                                                                    <h6 class="card-text">Total Funcionando: <b>
                                                                            <?php echo $congeladores_funcionando; ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Total Malos:
                                                                        <b><?php echo $congeladores_malas; ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Porcentaje Buenas: <b>
                                                                            <div class="p-2 rounded <?= $colorClasea1; ?>"
                                                                                style="display: inline-block; min-width: 100px; text-align: center;">
                                                                                <div
                                                                                    class="p-2 rounded <?= $colorClasea1; ?>">
                                                                                    <b><?= number_format($valora1, 2); ?>%</b>
                                                                                </div>
                                                                        </b>
                                                                    </h6>
                                                                </center>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Card: Estufas y Quemadores PAE -->
                                                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="card h-100 shadow-sm">
                                                            <div class="card-body">
                                                                <center>
                                                                    <h4 class="card-title">Estufas Y Quemadores</h4>
                                                                    <div id="graficoEstufasContainer" style="margin-top: 20px;"></div>

                                                                    <h6 class="card-text">Total Estufas: <b>
                                                                            <?php echo $estufas; ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Total Quemadores: <b>
                                                                            <?php echo $quemadores_estufas; ?></b></h6>
                                                                    <h6 class="card-text">Total Quemadores Buenos:
                                                                        <b><?php echo $quemadores_estufas_buenos; ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Porcentaje Buenas: <b>
                                                                            <div class="p-2 rounded <?= $colorClasea2; ?>"
                                                                                style="display: inline-block; min-width: 100px; text-align: center;">
                                                                                <div
                                                                                    class="p-2 rounded <?= $colorClasea2; ?>">
                                                                                    <b><?= number_format($valora2, 2); ?>%</b>
                                                                                </div>
                                                                        </b>
                                                                    </h6>
                                                                </center>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Card: Licuadoras PAE -->
                                                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="card h-100 shadow-sm">
                                                            <div class="card-body">
                                                                <center>
                                                                    <h4 class="card-title">Licuadoras</h4>
                                                                    <div id="graficoLicuadorasContainer" style="margin-top: 20px;"></div>

                                                                    <h6 class="card-text">Total Licuadoras: <b>
                                                                            <?php echo $total_licuadoras; ?></b></h6>
                                                                    <h6 class="card-text">Total Industriales: <b>
                                                                            <?php echo $licuadoras_industriales; ?></b></h6>
                                                                    <h6 class="card-text">Total Buenas Industriales:
                                                                        <b><?php echo $licuadoras; ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Porcentaje Buenas Industriales:
                                                                        <b>
                                                                            <div class="p-2 rounded <?= $colorClasea1; ?>" 
                                                                            
                                                                                style="display: inline-block; min-width: 100px; text-align: center;">
                                                                                <div
                                                                                    class="p-2 rounded <?= $colorClasea1; ?>">
                                                                                    <b><?= number_format($valora3, 2); ?>%</b>
                                                                                </div>
                                                                                <!-- // aqui cambie clasea de 3 a 1  -->
                                                                        </b>
                                                                    </h6>
                                                                </center>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Card: Tamaños neveras y Congeladores PAE -->
                                                    <div class="col-sm-6 col-md-5 col-lg-4">
                                                        <div class="card h-100 shadow-sm">
                                                            <div class="card-body">
                                                                <center>
                                                                    <h4 class="card-title">Tamaños neveras y Congeladores
                                                                    </h4>
                                                                    <div id="graficoTamanoNeverasContainer" style="height: 280px; margin-bottom: 20px;"></div>

                                                                    <h6 style="display:none" class="card-text">
                                                                        Nevera Domestica Vertical 2200 Litros =
                                                                        <b><?php echo number_format($tamano_neveras_principales_nevera_domestica_vertical_2200l, 0); ?></b>
                                                                        <br>
                                                                        Nevera Domestica Vertical 1200 Litros =
                                                                        <b><?php echo number_format($tamano_neveras_principales_nevera_domestica_vertical_1200l, 0); ?></b>
                                                                        <br>
                                                                        Nevera Domestica Vertical entre 400_800 Litros =
                                                                        <b><?php echo number_format($tamano_neveras_principales_nevera_domestica_vertical_400_800L, 0); ?></b>
                                                                        <br>
                                                                        Nevera Domestica Vertical menor de 400 Litros =
                                                                        <b><?php echo number_format($tamano_neveras_principales_nevera_domestica_vertical_menor_400L, 0); ?></b>
                                                                        <br>
                                                                        Nevera Domestica Vertical Otro Tamaño =
                                                                        <b><?php echo number_format($tamano_neveras_principales_nevera_domestica_vertical_otra, 0); ?></b>
                                                                        <br>
                                                                        Congelador Grande Entre 1400 y 1600 Litros =
                                                                        <b><?php echo number_format($tamano_congelador_Congelador_Grande_1400_1600L, 0); ?></b>
                                                                        <br>
                                                                        Congelador Grande Menor a 400 Litros =
                                                                        <b><?php echo number_format($tamano_congelador_Congelador_Pequeño_Menor_400L, 0); ?></b>

                                                                    </h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> <!-- end row -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                <!-- INICIO DE SECCION 3 -->
                                <div class="col-sm-12">
                                    <div id="seccion3" class="seccion" style="display:none;" class="card">
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <img src="assets/img/elementococina.png" alt="Restaurante" width="120px">
                                                <h5 class="card-title mt-2">Estado Lugares de Preparación y Almacenamiento
                                                </h5>
                                            </div>

                                            <!-- Cards internas organizadas horizontalmente -->
                                            <div class="row justify-content-center g-3">
                                                <!-- Card: almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h5 class="card-title">Espacio de Almacenamiento</h5>
                                                                <div id="graficoAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Tienen Espacio de Almacenamiento:
                                                                    <br>
                                                                    Si=
                                                                    <b><?php echo number_format($espacio_almacenamiento_si, 0); ?></b>
                                                                    <br>
                                                                    No=<b><?php echo number_format($espacio_almacenamiento_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje:
                                                                    <br>
                                                                    Si=
                                                                    <b><?php echo number_format($porcentaje_alm_si, 2); ?>%</b>
                                                                    <br>
                                                                    No =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClasea4; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClasea4; ?>">
                                                                                <b><?= number_format($valora4, 2); ?>%</b>
                                                                            </div>
                                                                        </div>
                                                                    </b>
                                                                </h6>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: preparacion PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h5 class="card-title">Espacio de Preparación</h5>
                                                                <div id="graficoPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Tienen Espacio de Preparación <br>
                                                                    Si:
                                                                    <b><?php echo number_format($espacio_preparacion_si, 0); ?></b>
                                                                    <br>
                                                                    No=
                                                                    <b><?php echo number_format($espacio_preparacion_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text"> <br>Porcentaje:<br> Si=
                                                                    <b><?php echo number_format($porcentaje_prepa_si, 2); ?>%</b>
                                                                    <br>
                                                                    No=
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClasea5; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClasea5; ?>">
                                                                                <b><?= number_format($valora5, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Almacenamiento en Tarimas o Estibas
                                                                    Alto
                                                                    del
                                                                    Suelo</h4>
                                                                <div id="graficoAlmacenamientoTarimasContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Si=
                                                                    <b><?php echo number_format($almacena_alto_suelo_si, 0); ?></b>
                                                                    <br> No=
                                                                    <b><?php echo number_format($almacena_alto_suelo_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> <br> Si=
                                                                    <b><?php echo number_format($porcentaje_almacena_alto_suelo_si, 2); ?>%</b>
                                                                    <br> No =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase4; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase4; ?>">
                                                                                <b><?= number_format($valor4, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: elementos almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Elementos Utilizados para el
                                                                    almacenamiento
                                                                    de alimentos</h4>
                                                                <div id="graficoElementosAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Baldes=
                                                                    <b><?php echo number_format($almacena_balde, 0); ?></b>
                                                                    <br>
                                                                    Canastillas =
                                                                    <b><?php echo number_format($almacena_canasta, 0); ?></b>
                                                                    <br>
                                                                    Estantes=
                                                                    <b><?php echo number_format($almacena_estante, 0); ?></b>
                                                                    <br>
                                                                    Cajas=
                                                                    <b><?php echo number_format($almacena_caja, 0); ?></b>
                                                                    <hr>
                                                                    No Aplica=
                                                                    <b><?php echo number_format($almacena_na, 0); ?></b>
                                                                    <br>
                                                                    Ninguno=
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase3; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase3; ?>">
                                                                                <b><?= number_format($valor3, 2); ?>%</b>
                                                                            </div>
                                                                        </div>
                                                                    </b>

                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: estado techo almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Estado Techo Almacenamiento</h4>
                                                                <div id="graficoEstadoTechoAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_techo_almacenamiento_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_techo_almacenamiento_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_techo_almacenamiento_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_techo_almacenamiento_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_techo_almacenamiento_regular, 2); ?>%</b>
                                                                    <br> Malo =
                                                                    <div class="p-2 rounded <?= $colorClase5; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase5; ?>">
                                                                            <b><?= number_format($valor5, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: estado paredes almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Estado Paredes Almacenamiento</h4>
                                                                <div id="graficoEstadoParedesAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_paredes_almacenamiento_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_paredes_almacenamiento_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_paredes_almacenamiento_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_paredes_almacenamiento_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_paredes_almacenamiento_regular, 2); ?>%</b>
                                                                    <br> Malo =
                                                                    <div class="p-2 rounded <?= $colorClase9; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase9; ?>">
                                                                            <b><?= number_format($valor9, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: estado piso almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Estado Piso Almacenamiento</h4>
                                                                <div id="graficoEstadoPisoAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_piso_almacenamiento_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_piso_almacenamiento_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_piso_almacenamiento_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_piso_almacenamiento_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_piso_almacenamiento_regular, 2); ?>%</b>
                                                                    <br> Malo =
                                                                    <div class="p-2 rounded <?= $colorClase7; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase7; ?>">
                                                                            <b><?= number_format($valor7, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: estado techo Preparacion PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Estado Techo Preparación</h4>
                                                                <div id="graficoEstadoTechoPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_techo_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_techo_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_techo_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_techo_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_techo_regular, 2); ?>%</b>
                                                                    <br> Malo =
                                                                    <div class="p-2 rounded <?= $colorClase8; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase8; ?>">
                                                                            <b><?= number_format($valor8, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: estado paredes preparacion PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Estado Paredes Preparación</h4>
                                                                <div id="graficoEstadoParedesPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_paredes_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_paredes_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_paredes_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_paredes_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_paredes_regular, 2); ?>%</b>
                                                                    <br>
                                                                    Malo =
                                                                    <div class="p-2 rounded <?= $colorClase6; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase6; ?>">
                                                                            <b><?= number_format($valor6, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: estado paredes preparacion PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Estado Piso Preparación</h4>
                                                                <div id="graficoEstadoPisoPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_piso_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_piso_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_piso_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_piso_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_piso_regular, 2); ?>%</b>
                                                                    <br> Malo =
                                                                    <div class="p-2 rounded <?= $colorClase10; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase10; ?>">
                                                                            <b><?= number_format($valor10, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!-- end row -->
                                        </div>
                                    </div>
                                </div>
    <!-- INICIO DE SECCION 4 -->
                                <div class="col-sm-12">
                                    <div id="seccion4" class="seccion" style="display:none;" class="card">
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <img src="assets/img/construccionlogo.png" alt="Restaurante" width="120px">
                                                <h4 class="card-title mt-2">Materiales Preparación y Almacenamiento PAE</h4>
                                            </div>
                                            <!-- Cards internas organizadas horizontalmente -->
                                            <div class="row justify-content-center g-3">

                                                <!-- Card: techo almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo Material Techo Almacenamiento
                                                                </h4>
                                                                <div id="graficoMaterialTechoAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">
                                                                    Eternit =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_eternit, 0); ?></b>
                                                                    <br>
                                                                    Teja Barro, arcilla =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_tejas, 0); ?></b>
                                                                    <br>
                                                                    Teja Plastica =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_plastico, 0); ?></b>
                                                                    <br>
                                                                    Teja Zinc =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_zinc, 0); ?></b>
                                                                    <br>
                                                                    Concreto =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_concreto, 0); ?></b>
                                                                    <br>
                                                                    Metal =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_metal, 0); ?></b>
                                                                    <br>
                                                                    Paja, Madera, Tablones=
                                                                    <b><?php echo number_format($material_techo_almacenamiento_paja, 0); ?></b>
                                                                    <br>
                                                                    Otros =
                                                                    <b><?php echo number_format($material_techo_almacenamiento_otros, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: materiales paredes almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo Material Paredes Almacenamiento
                                                                </h4>
                                                                <div id="graficoMaterialParedesAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bloque =
                                                                    <b><?php echo number_format($material_paredes_almacenamiento_bloque, 0); ?></b>
                                                                    <br>
                                                                    Drywall, Alglomerado =
                                                                    <b><?php echo number_format($material_paredes_almacenamiento_prefabricado, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Bahareque,Madera, <br> Tablones =
                                                                    <b><?php echo number_format($material_paredes_almacenamiento_bahareque, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Otros =
                                                                    <b><?php echo number_format($material_paredes_almacenamiento_otros, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: materiales piso almacenamiento PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo Material Piso Almacenamiento
                                                                </h4>
                                                                <div id="graficoMaterialPisoAlmacenamientoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">
                                                                    baldosa =
                                                                    <b><?php echo number_format($material_piso_almacenamiento_baldosa, 0); ?></b>
                                                                    <br>
                                                                    Cemento, Gravilla =
                                                                    <b><?php echo number_format($material_piso_almacenamiento_cemento, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Ladrillo =
                                                                    <b><?php echo number_format($material_piso_almacenamiento_ladrillo, 0); ?></b>
                                                                    <br>
                                                                    Madera, Tablones =
                                                                    <b><?php echo number_format($material_piso_almacenamiento_madera, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Otros =
                                                                    <b><?php echo number_format($material_piso_almacenamiento_otros, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: techo preparacion PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo Material Techo Preparación</h4>
                                                                <div id="graficoMaterialTechoPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Zinc =
                                                                    <b><?php echo number_format($material_techo_preparacion_zinc, 0); ?></b>
                                                                    <br>
                                                                    Eternit =
                                                                    <b><?php echo number_format($material_techo_eternit, 0); ?></b>
                                                                    <br>
                                                                    Teja Barro, Arcilla =
                                                                    <b><?php echo number_format($material_techo_teja_barro, 0); ?></b>
                                                                    <br>
                                                                    Metal, Acero =
                                                                    <b><?php echo number_format($material_techo_preparacion_metal_acero, 0); ?></b>
                                                                    <br>
                                                                    Concreto =
                                                                    <b><?php echo number_format($material_techo_preparacion_concreto, 0); ?></b>
                                                                    <br>
                                                                    Paja, Madera =
                                                                    <b><?php echo number_format($material_techo_preparacion_paja, 0); ?></b>

                                                                    <br>
                                                                    Tejas Plasticas =
                                                                    <b><?php echo number_format($material_techo_preparacion_plastico, 0); ?></b>
                                                                    <br>
                                                                    otros =
                                                                    <b><?php echo number_format($material_techo_preparacion_otros, 0); ?></b>
                                                                    <br>

                                                                    Sin Techo =
                                                                    <b><?php echo number_format($material_techo_preparacion_sin_techo, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: tipo material paredes PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo Material Paredes Preparación
                                                                </h4>
                                                                <div id="graficoMaterialParedesPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> BLoque, Ladrillo, Adobe =
                                                                    <b><?php echo number_format($material_paredes_preparacion_ladrillo, 0); ?></b>
                                                                    <br> Bahareque, Madera, Tablones =
                                                                    <b><?php echo number_format($material_paredes_preparacion_bahareque, 0); ?></b>
                                                                    <br> Drywall, Lamina, polietileno,<br> aglomerado =
                                                                    <b><?php echo number_format($material_paredes_preparacion_prefabricado, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Otros Materiales =
                                                                    <b><?php echo number_format($material_paredes_preparacion_otros, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: tipo material piso PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo Material Piso Preparación</h4>
                                                                <div id="graficoMaterialPisoPreparacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Baldosa =
                                                                    <b><?php echo number_format($material_piso_preparacion_baldosa, 0); ?></b>
                                                                    <br>
                                                                    <br>Cemento, Gravilla =
                                                                    <b><?php echo number_format($material_piso_cemento, 0); ?></b>
                                                                    <br>
                                                                    <br>Madera, Tablones =
                                                                    <b><?php echo number_format($material_piso_preparacion_madera, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Otros Materiales =
                                                                    <b><?php echo number_format($material_piso_preparacion_otros, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!-- end row -->
                                        </div>
                                    </div>
                                </div>
    <!-- INICIO SECCION 5 -->
                                <div class="col-sm-12">
                                    <div id="seccion5" class="seccion" style="display:none;" class="card">
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <img src="assets/img/luz.png" alt="" width="100px">
                                                <h4 class="card-title mt-2">Acceso a servicios publicos y tipo</h4>
                                            </div>
                                            <!-- Cards internas organizadas horizontalmente -->
                                            <div class="row justify-content-center g-3">

                                                <!-- Card: accceso a agua PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Acceso al sistema de Agua</h4>
                                                                <div id="graficoAccesoAguaContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> si=
                                                                    <b><?php echo number_format($acceso_agua_si, 0); ?></b>
                                                                    <br>
                                                                    No=
                                                                    <b><?php echo number_format($acceso_agua_no, 0); ?></b>
                                                                    <br>
                                                                    Intermitente=
                                                                    <b><?php echo number_format($acceso_agua_intermitente, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Si=
                                                                    <b><?php echo number_format($porcentaje_acceso_agua_si, 2); ?>%</b>
                                                                    <br> No =
                                                                    <b><?php echo number_format($porcentaje_acceso_agua_no, 2); ?>%</b>
                                                                    <br> Intermitente =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase11; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase11; ?>">
                                                                                <b><?= number_format($valor11, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: tipo  accceso a agua PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Obtención del agua para el PAE</h4>
                                                                <div id="graficoObtencionAguaContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Acueducto=
                                                                    <b><?php echo number_format($acueducto, 0); ?></b>
                                                                    <br>
                                                                    Rios, Quebradas =
                                                                    <b><?php echo number_format($rios_quebradas, 0); ?></b>
                                                                    <br>
                                                                    Agua embotellada=
                                                                    <b><?php echo number_format($embotellada, 0); ?></b>
                                                                    <br>
                                                                    Agua LLuvia=
                                                                    <b><?php echo number_format($lluvia, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Carrotanque=
                                                                    <b><?php echo number_format($carrotanque, 0); ?></b>
                                                                    <br>
                                                                    Pozos=
                                                                    <b><?php echo number_format($pozo_agua, 0); ?></b>
                                                                    <br>
                                                                    Otros Metodos=
                                                                    <b><?php echo number_format($otros_agua, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: tipo  accceso a electricidad PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Acceso a Electricidad</h4>

                                                                <!-- Contenedor del gráfico -->
                                                                <div id="graficoAccesoElectricidadContainer" style="height: 180px; margin-bottom: 10px;"></div>

                                                                <h6 class="card-text"> Si=
                                                                    <b><?php echo number_format($acceso_electricidad_si, 0); ?></b>
                                                                    <br> No=
                                                                    <b><?php echo number_format($acceso_electricidad_no, 0); ?></b>
                                                                    <br> Intermitente=
                                                                    <b><?php echo number_format($acceso_electricidad_intermitente, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Si=
                                                                    <b><?php echo number_format($porcentaje_acceso_electricidad_si, 2); ?>%</b>
                                                                    <br> No =
                                                                    <b><?php echo number_format($porcentaje_acceso_electricidad_no, 2); ?>%</b>
                                                                    <br> Intermitente =
                                                                    <div class="p-2 rounded <?= $colorClase12; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase12; ?>">
                                                                            <b><?= number_format($valor12, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Card: tipo  uso a electricidad PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Tipo de elemento de cocción Alimentos
                                                                </h4>
                                                                <div id="graficoTipoCoccionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Gas Natural o Pipeta Gas=
                                                                    <b><?php echo number_format($gas_natural, 0); ?></b>
                                                                    <br>
                                                                    Leña, Carbon de leña o mineral =
                                                                    <b><?php echo number_format($lena, 0); ?></b>
                                                                    <br>
                                                                    Electricidad=
                                                                    <b><?php echo number_format($electricidad, 0); ?></b>
                                                                    <br>
                                                                    Petroleo, Gasolina,Kerosene o alcohol=
                                                                    <b><?php echo number_format($petroleo_gasolina, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Materiales de desecho=
                                                                    <b><?php echo number_format($desecho, 0); ?></b>
                                                                    <br>
                                                                    No Aplica=
                                                                    <b><?php echo number_format($no_aplica, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: discposicion de basuras y alcantarillado PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Disposición Basuras y alcantarillado
                                                                </h4>
                                                                <div id="graficoBasuraAlcantarilladoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Recolección Basuras: <br>
                                                                    Si=
                                                                    <b><?php echo number_format($recoleccion_basuras_si, 0); ?></b>
                                                                    <br>
                                                                    No=
                                                                    <b><?php echo number_format($recoleccion_basuras_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Acceso Alcantarillado:
                                                                    <br>Si=
                                                                    <b><?php echo number_format($acceso_alcantarillado_si, 0); ?></b>
                                                                    <br>No=
                                                                    <b><?php echo number_format($acceso_alcantarillado_no, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: discposicion de residusos organicos PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Disposición de Desechos Orgánicos
                                                                </h4>
                                                                <div id="graficoDesechosOrganicosContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">
                                                                    Enterrados =
                                                                    <b><?php echo number_format($disposicion_desechos_pae_enterrado, 0); ?></b>
                                                                    <br>
                                                                    Quemados =
                                                                    <b><?php echo number_format($disposicion_desechos_pae_quemado, 0); ?></b>
                                                                    <br>
                                                                    Rciclados =
                                                                    <b><?php echo number_format($disposicion_desechos_pae_reciclan, 0); ?></b>

                                                                    <br>
                                                                    para lombricultura =
                                                                    <b><?php echo number_format($disposicion_desechos_pae_lombricultura, 0); ?></b>
                                                                    <hr>
                                                                    <br>
                                                                    Botados en lotes, <br> o otras zonas =
                                                                    <b><?php echo number_format($disposicion_desechos_pae_tiran_lote, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: discposicion de desechos no organicos PAE -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Disposición de Desechos No Organicos
                                                                </h4>
                                                                <div id="graficoDesechosNoOrganicosContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">
                                                                    Enterrados =
                                                                    <b><?php echo number_format($disposicion_no_organicos_pae_enterrado, 0); ?></b>
                                                                    <br>
                                                                    Quemados =
                                                                    <b><?php echo number_format($disposicion_no_organicos_pae_quemado, 0); ?></b>
                                                                    <br>
                                                                    Rciclados =
                                                                    <b><?php echo number_format($disposicion_no_organicos_pae_reciclan, 0); ?></b>

                                                                    <br>
                                                                    para lombricultura =
                                                                    <b><?php echo number_format($disposicion_no_organicos_pae_lombricultura, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Botados en lotes, o otras zonas =
                                                                    <b><?php echo number_format($disposicion_no_organicos_pae_tiran_lote, 0); ?></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>
                                    </div>
                                </div>
    <!-- INICIO SECCION 6 -->
                                <div class="col-sm-12">
                                    <div id="seccion6" class="seccion" style="display:none;" class="card">
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <img src="assets/img/restaurant.png" alt="" width="100px">
                                                <h4 class="card-title mt-2">Restaurante PAE</h4>
                                            </div>
                                            <!-- Cards internas organizadas horizontalmente -->
                                            <div class="row justify-content-center g-3">
                                                <!-- Card: preparacion almuerzo -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Almuerzo Preparado en sitio</h4>
                                                                <div id="graficoAlmuerzoPreparadoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Almuerzo <br> Si=
                                                                    <b><?php echo number_format($almuerzo_preparado_sitio_si, 0); ?></b>
                                                                    <br>
                                                                    No=<b><?php echo number_format($almuerzo_preparado_sitio_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje:
                                                                    <br> Si=
                                                                    <b><?php echo number_format($porcentaje_prepa_sitio_si, 2); ?>%</b>
                                                                    <br>
                                                                    No =
                                                                    <div class="p-2 rounded <?= $colorClase13; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase13; ?>">
                                                                            <b><?= number_format($valor13, 2); ?>%</b>
                                                                        </div></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: transporte almuerzo -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Almuerzo transportado (Comida
                                                                    Caliente)
                                                                </h4>
                                                                <div id="graficoAlmuerzoTransportadoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Almuerzo : <br>
                                                                    Si=
                                                                    <b><?php echo number_format($almuerzo_trasportado_si, 0); ?></b>
                                                                    <br>
                                                                    No=
                                                                    <b><?php echo number_format($almuerzo_trasportado_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje:
                                                                    <br> Si=
                                                                    <b><?php echo number_format($porcentaje_transporte_almuer_si, 2); ?>%</b>
                                                                    <br>
                                                                    No =
                                                                    <div class="p-2 rounded <?= $colorClasea9; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClasea9; ?>">
                                                                            <b><?= number_format($valora9, 2); ?>%</b>
                                                                        </div></b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: preparacion complento -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Preparación Complemento</h4>
                                                                <div id="graficoComplementoPreparadoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Preparado en sitio:
                                                                    <br> Si=
                                                                    <b><?php echo number_format($complemento_preparado_sitio_si, 0); ?></b>
                                                                    <br>
                                                                    No=<b><?php echo number_format($complemento_preparado_sitio_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Si=
                                                                    <b><?php echo number_format($porcentaje_complemento_prepa_sitio_si, 2); ?>%</b>
                                                                    <br>
                                                                    No =
                                                                    <div class="p-2 rounded <?= $colorClase14; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase14; ?>">
                                                                            <b><?= number_format($valor14, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: preparacion complento -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Complemento industrializado</h4>
                                                                <div id="graficoComplementoIndustrializadoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Complemento (Industrializado):
                                                                    <br>
                                                                    Si=
                                                                    <b><?php echo number_format($complemento_industrializado_si, 0); ?></b>
                                                                    <br>
                                                                    No=
                                                                    <b><?php echo number_format($complemento_industrializado_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje:
                                                                    <br> Si=
                                                                    <b><?php echo number_format($porcentaje_complemento_industri_sitio_si, 2); ?>%</b>
                                                                    <br>
                                                                    No =
                                                                    <div class="p-2 rounded <?= $colorClase15; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase15; ?>">
                                                                            <b><?= number_format($valor15, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: posee comedor escolar -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Posee Comedor Escolar</h4>
                                                                <div id="graficoComedorEscolarContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Si=
                                                                    <b><?php echo number_format($comedor_escolar_si, 0); ?></b>
                                                                    <br> No=
                                                                    <b><?php echo number_format($comedor_escolar_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <br>
                                                                <h6 class="card-text">Porcentaje: <br> <br> Si=
                                                                    <b><?php echo number_format($porcentaje_comedor_escolar_si, 2); ?>%</b>
                                                                    <br> No =
                                                                    <div class="p-2 rounded <?= $colorClase16; ?>"
                                                                        style="display: inline-block; min-width: 100px; text-align: center;">
                                                                        <div class="p-2 rounded <?= $colorClase16; ?>">
                                                                            <b><?= number_format($valor16, 2); ?>%</b>
                                                                        </div>
                                                                    </div>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: concepto sanitoario -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Concepto Sanitario Comedor Escolar
                                                                </h4>
                                                                <div id="graficoConceptoSanitarioContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Si Tiene Concepto Favorable=
                                                                    <b><?php echo number_format($si_tiene_favorable, 0); ?></b>
                                                                    <br>
                                                                    <br>
                                                                    Si Tiene Concepto Favorable con Requerimientos =
                                                                    <b><?php echo number_format($si_favorable_requerimientos, 0); ?></b>
                                                                    <br>
                                                                    <hr>
                                                                    Si tiene Concepto Desfavorable=
                                                                    <b><?php echo number_format($si_desfavorable, 0); ?></b>
                                                                    <br>
                                                                    No tiene Concepto=
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase17; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase17; ?>">
                                                                                <b><?= number_format($valor17, 0); ?></b>
                                                                            </div>
                                                                        </div>
                                                                    </b>
                                                                    <br>
                                                                    <br>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: concepto sanitoario -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Cantidad niños Sentados</h4>
                                                                <div id="graficoCantidadNinosSentadosContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Si=
                                                                    <b><?php echo number_format($cant_ninos_pae_sentados_todos, 0); ?></b>
                                                                    <br> No=
                                                                    <b><?php echo number_format($posee_cucharones_pae_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> <br> Si=
                                                                    <b><?php echo number_format($porcentaje_cant_ninos_pae_sentados_todos, 2); ?>%</b>
                                                                    <br> No =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase18; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase18; ?>">
                                                                                <b><?= number_format($valor18, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: baterias sanitarsa -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">baterias sanitarias pae</h5>
                                                                <div id="graficoBateriasSanitariasContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                    <h6 class="card-text">Bateria Sanitaria Personal
                                                                        Manipulador
                                                                        de
                                                                        Alimentos:
                                                                        <br> Si=
                                                                        <b><?php echo number_format($sanitario_personal_si, 0); ?></b>
                                                                        <br>
                                                                        No=
                                                                        <b><?php echo number_format($sanitario_personal_no, 0); ?></b>
                                                                    </h6>
                                                                    <h6 class="card-text">Lavamanos para Personal
                                                                        Manipulador de
                                                                        Alimentos: <br> Si=
                                                                        <b><?php echo number_format($lavamanos_personal_si, 0); ?></b>
                                                                        <br>
                                                                        No=
                                                                        <b><?php echo number_format($lavamanos_personal_no, 0); ?></b>
                                                                    </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: cercania contaminacion -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title">Cercanía Fuentes de contaminación PAE
                                                                </h4>
                                                                <div id="graficoCercaniaContaminacionContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Ubicación cerca: <br> Si=
                                                                    <b><?php echo number_format($cercania_contaminacion_si, 0); ?></b>
                                                                    <br>
                                                                    No=<b><?php echo number_format($cercania_contaminacion_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentajes: <br> Si=
                                                                    <b><?php echo number_format($porcentaje_cercania_contaminacion_si, 2); ?>%</b>
                                                                    <br> No =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClasea10; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClasea10; ?>">
                                                                                <b><?= number_format($valora10, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: cercania conflicto armado -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h4 class="card-title"> Ubicación en Zona de Conflicto
                                                                    Armado
                                                                </h4>
                                                                <div id="graficoZonaConflictoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text">Ubicado Zona de Conflicto: <br> Si=
                                                                    <b><?php echo number_format($zona_conflicto_si, 0); ?></b>
                                                                    <br>
                                                                    No=<b><?php echo number_format($zona_conflicto_no, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Si=
                                                                    <b><?php echo number_format($porcentaje_zona_conflicto_si, 2); ?>%</b>
                                                                    <br> No =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClasea11; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClasea11; ?>">
                                                                                <b><?= number_format($valora11, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: cercania conflicto armado afectacion -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h5 class="card-title">Afectación PAE por Conflicto Armado
                                                                </h5>
                                                                <div id="graficoAfectacionConflictoContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Algo Frecuente=
                                                                    <b><?php echo number_format($algo_frecuente_conflicto, 0); ?></b>
                                                                    <br> Poco Frecuente=
                                                                    <b><?php echo number_format($poco_frecuente_conflicto, 0); ?></b>
                                                                    <br> No Frecuente=
                                                                    <b><?php echo number_format($no_frecuencia_conflicto, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: Algo Frecuente=
                                                                    <b><?php echo number_format($porcentaje_armado_algo, 2); ?>%</b>
                                                                    <br> Poco Frecuente =
                                                                    <b><?php echo number_format($porcentaje_armado_poco, 2); ?>%</b>
                                                                    <br> No Frecuente =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClasea12; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClasea12; ?>">
                                                                                <b><?= number_format($valora12, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>
                                    </div>
                                </div>
    <!-- INICIO SECCION 7 -->
                                <div class="col-sm-12">
                                    <div id="seccion7" class="seccion" style="display:none;" class="card">
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <img src="assets/img/colegio.png" alt="" width="100px">
                                                <h4 class="card-title mt-2">Estado Sedes Educativas</h4>
                                            </div>
                                            <!-- Cards internas organizadas horizontalmente -->
                                            <div class="row justify-content-center g-3">

                                                <!-- Card: estado sedes educativas -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h5 class="card-title">Estado Sede Educativa</h5>
                                                                <div id="graficoEstadoSedeEducativaContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Nuevo Activo =
                                                                    <b><?php echo number_format($estado_sede_nuevo_activo, 0); ?></b>
                                                                    <br> Antiguo Activo =
                                                                    <b><?php echo number_format($estado_sede_antiguo_activo, 0); ?></b>
                                                                    <br> Cierre Temporal =
                                                                    <b><?php echo number_format($estado_sede_cierre_temporal, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Nuevo Activo =
                                                                    <b><?php echo number_format($porcentaje_estado_sede_nuevo_activo, 2); ?>%</b>
                                                                    <br> Cierre Temporal =
                                                                    <b><?php echo number_format($porcentaje_estado_sede_cierre_temporal, 2); ?>%</b>
                                                                    <br> Antiguo Activo =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase19; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase19; ?>">
                                                                                <b><?= number_format($valor19, 2); ?>%</b>
                                                                            </div>
                                                                    </b>

                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card: estado techos sedes educativas -->
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body">
                                                            <center>
                                                                <h5 class="card-title">Estado Techo Sede Educativa</h5>
                                                                <div id="graficoEstadoTechoSedeEducativaContainer" style="height: 180px; margin-bottom: 20px;"></div>

                                                                <h6 class="card-text"> Bueno =
                                                                    <b><?php echo number_format($estado_techo_almacenamiento_bueno, 0); ?></b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($estado_techo_almacenamiento_regular, 0); ?></b>
                                                                    <br> Malo =
                                                                    <b><?php echo number_format($estado_techo_almacenamiento_malo, 0); ?></b>
                                                                </h6>
                                                                <hr>
                                                                <h6 class="card-text">Porcentaje: <br> Bueno =
                                                                    <b><?php echo number_format($porcentaje_estado_techo_almacenamiento_bueno, 2); ?>%</b>
                                                                    <br> Regular =
                                                                    <b><?php echo number_format($porcentaje_estado_techo_almacenamiento_regular, 2); ?>%</b>
                                                                    <br> Malo =
                                                                    <b>
                                                                        <div class="p-2 rounded <?= $colorClase20; ?>"
                                                                            style="display: inline-block; min-width: 100px; text-align: center;">
                                                                            <div class="p-2 rounded <?= $colorClase20; ?>">
                                                                                <b><?= number_format($valor20, 2); ?>%</b>
                                                                            </div>
                                                                    </b>
                                                                </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end row -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    </center>
                </div>

                <div class="row g-4 mt-4 p-4">
                <!-- Gráfica: municipios con sedes educativas -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 table-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i style="color:red" class="bi bi-geo-alt-fill"></i>Municipios con Sedes Educativas con Problemas de Infraestructura</h5>
                        </div>
                      <div class="pt-4">
                        <div id="barchartesteo" style="height: 100%;"></div>
                        </div>
                      </div>
                </div>

                <!-- Gráfica: Estado sedes educativas -->
                <div class="col-12 col-md-6">
                    <div class="card h-100 table-card shadow-sm border">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h5 class="mb-0">
                            <i class="bi bi-geo-alt-fill" style="color:red;"></i> Estado Sedes Educativas
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Tabla de leyenda -->
                            <div class="table-border-style mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle text-center tabla-colores mb-0">
                                <thead class="table-light">
                                    <tr>
                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Color</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>0</td>
                                    <td>0</td>
                                    <td><div class="color-circle"></div></td>
                                    </tr>
                                    <tr>
                                    <td>1</td>
                                    <td>2</td>
                                    <td><div class="color-circle color-red"></div></td>
                                    </tr>
                                    <tr>
                                    <td>3</td>
                                    <td>4</td>
                                    <td><div class="color-circle color-yellow"></div></td>
                                    </tr>
                                    <tr>
                                    <td>5</td>
                                    <td>6</td>
                                    <td><div class="color-circle color-blue"></div></td>
                                    </tr>
                                    <tr>
                                    <td>7</td>
                                    <td>+</td>
                                    <td><div class="color-circle color-green"></div></td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
                        </div>

                </div>
                <!-- Fin grafica estado sedes educativas -->
            </div>

                
                 
            <div class="col-12">
                <div class="card shadow-none border" data-component-card="data-component-card">
                    <div class="card-header p-4 border-bottom bg-body">
                    <div class="row g-3 justify-content-between align-items-center">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                        <h4 class="text-body mb-0">Mapa <?php echo Util::nombreDelProyecto(); ?></h4>
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-toggle="modal" data-target="#exampleModalCenter">
                            <img src="assets/images/geoloca.png" alt="Geolocalización" style="width: 30px; height: 30px; object-fit: contain;">
                            <span>Geolocalización</span>
                        </button>
                        </div>
                        <div class="col-md-12" style="position: static; overflow-x: auto;">
                        <div class="cuerpoMapa w-12">
                            <div class="santander munis">
                            <?php require_once "admin/mapa_putumayo/mapa_pae.php"; ?>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- MODAL GEOLOCALIZACIÓN -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

        <!-- Encabezado estilizado -->
        <div class="modal-header bg-primary justify-content-between align-items-center">
            <h5 class="modal-title text-white m-0 w-100 text-center" id="exampleModalCenterTitle">
            Geolocalización
            </h5>
            <button type="button" class="close btn-close-white text-white position-absolute end-0 me-3" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Cuerpo con mapa -->
        <div class="modal-body">
            <div id="map" style="height: 600px; width: 100%;"></div>
        </div>

        </div>
    </div>

    </div>
            <!-- Google Maps JavaScript API -->
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbqZVEnSpIPF5dKe89pju2rmQaM58wvY0&callback=initMap"></script>
    <script>
        let map;
        let trafficLayer, transitLayer, bicycleLayer;

        // Coordenadas iniciales desde PHP (Util)
        const initialLocation = {
            lat: <?php echo Util::getLatitudDepartamentoPrincipal(); ?>,
            lng: <?php echo Util::getLongitudDepartamentoPrincipal(); ?>
        };

        function initMap() {
            if (typeof google !== 'undefined' && google.maps) {
                // Crear el mapa con coordenadas ya definidas
                map = new google.maps.Map(document.getElementById("map"), {
                    center: initialLocation,
                    zoom: 12,
                });

                map.addListener("click", (event) => {
                    const lat = event.latLng.lat();
                    const lng = event.latLng.lng();

                    document.getElementById("lat").innerText = lat.toFixed(6);
                    document.getElementById("lng").innerText = lng.toFixed(6);

                    new google.maps.Marker({
                        position: event.latLng,
                        map: map,
                    });
                });

                trafficLayer = new google.maps.TrafficLayer();
                transitLayer = new google.maps.TransitLayer();
                bicycleLayer = new google.maps.BicyclingLayer();

                document.getElementById("trafficLayerToggle").addEventListener("change", (e) => {
                    trafficLayer.setMap(e.target.checked ? map : null);
                });
                document.getElementById("transitLayerToggle").addEventListener("change", (e) => {
                    transitLayer.setMap(e.target.checked ? map : null);
                });
                document.getElementById("bicycleLayerToggle").addEventListener("change", (e) => {
                    bicycleLayer.setMap(e.target.checked ? map : null);
                });
                document.getElementById("terrainToggle").addEventListener("change", (e) => {
                    map.setMapTypeId(e.target.checked ? "terrain" : "roadmap");
                });
            } else {
                console.error('Google Maps API no está disponible.');
            }
        }

        // Inicializar el mapa al abrir el modal
        $('#exampleModalCenter').on('shown.bs.modal', function () {
            initMap();
        });
    </script>


     <?php include 'admin/include/gerenic_script.php'; ?>
     <script src="assets/js/vendor-all.min.js"></script>
     <script src="assets/js/plugins/bootstrap.min.js"></script>
     <script src="assets/js/pcoded.min.js"></script>

     <!-- prism Js -->
     <script src="assets/js/plugins/prism.js"></script>


     <script src="admin/js/pae_mapa_geo.js"></script>
     <script src="admin/js/pae_dash.js"></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
     <script src="assets/js/pages/chart-apex.js"></script>
     </script>
     <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-sm">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title h4" id="mySmallModalLabel"> Grafico Elementos Utilizados para el
                         almacenamiento de alimentos</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                             aria-hidden="true">&times;</span></button>
                 </div>
                 <div class="modal-body">
                     <div id="pie-chart-1" style="width:100%"></div>
                 </div>
             </div>
         </div>
     </div>
     </div>

     <script type="text/javascript" src="admin/js/departamento.js"></script>
     <script>
         setTimeout(function() {
             DEPARTAMENTO.getMunicipiosOpcionSelectTodos();
         }, 500);
     </script>
     <script>
         const datosMenaje = [
             <?php echo (int)$cantidad_platos; ?>,
             <?php echo (int)$cantidad_cucharas; ?>,
             <?php echo (int)$cantidad_tenedores; ?>,
             <?php echo (int)$cantidad_pocillos; ?>
         ];

         const datosCucharones = [
             <?php echo (int)$posee_cucharones_pae_si; ?>,
             <?php echo (int)$posee_cucharones_pae_no; ?>
         ];
         const datosCucharonesPorcentaje = [
             <?php echo (float)str_replace(',', '.', number_format($porcentaje_posee_cucharones_pae_si, 2)); ?>,
             <?php echo (float)str_replace(',', '.', number_format($valor, 2)); ?>
         ];
         const datosCuchillos = [
             <?php echo (int)$posee_cuchillos_pae_si; ?>,
             <?php echo (int)$posee_cuchillos_pae_no; ?>
         ];

         const datosCuchillosPorcentaje = [
             <?php echo (float)str_replace(',', '.', number_format($porcentaje_posee_cuchillos_pae_si, 2)); ?>,
             <?php echo (float)str_replace(',', '.', number_format($valor, 2)); ?>
         ];
         const datosOllas = [
             <?php echo (int)$posee_ollas_pae_si; ?>,
             <?php echo (int)$posee_ollas_pae_no; ?>
         ];

         const datosOllasPorcentaje = [
             <?php echo (float)str_replace(',', '.', number_format($porcentaje_posee_ollas_pae_si, 2)); ?>,
             <?php echo (float)str_replace(',', '.', number_format($valor, 2)); ?>
         ];
         const datosNeveras = [
             <?php echo (int)$neveras_fun; ?>,
             <?php echo (int)$neveras_malas; ?>
         ];

         const porcentajeNeveras = [
             <?php echo (float)str_replace(',', '.', number_format($valora, 2)); ?>,
             <?php echo (float)(100 - $valora); ?>
         ];
         const porcentajeCongeladores = [
             <?php echo number_format($valora1, 2, '.', ''); ?>,
             <?php echo number_format(100 - $valora1, 2, '.', ''); ?>
         ];
         const porcentajeEstufas = [
             <?php echo number_format($valora2, 2, '.', ''); ?>,
             <?php echo number_format(100 - $valora2, 2, '.', ''); ?>
         ];
         const porcentajeLicuadoras = [
             <?php echo number_format($valora3, 2, '.', ''); ?>,
             <?php echo number_format(100 - $valora3, 2, '.', ''); ?>
         ];
         const datosTamanoNeveras = [
             <?php echo (int)$tamano_neveras_principales_nevera_domestica_vertical_2200l; ?>,
             <?php echo (int)$tamano_neveras_principales_nevera_domestica_vertical_1200l; ?>,
             <?php echo (int)$tamano_neveras_principales_nevera_domestica_vertical_400_800L; ?>,
             <?php echo (int)$tamano_neveras_principales_nevera_domestica_vertical_menor_400L; ?>,
             <?php echo (int)$tamano_neveras_principales_nevera_domestica_vertical_otra; ?>,
             <?php echo (int)$tamano_congelador_Congelador_Grande_1400_1600L; ?>,
             <?php echo (int)$tamano_congelador_Congelador_Pequeño_Menor_400L; ?>
         ];
         const porcentajeAlmacenamiento = [
             <?php echo (float)$porcentaje_alm_si; ?>,
             <?php echo (float)$valora4; ?>
         ];
         const porcentajePreparacion = [
             <?php echo (float)$porcentaje_prepa_si; ?>,
             <?php echo (float)$valora5; ?>
         ];
         const porcentajeAlmacenamientoTarimas = [
             <?php echo (float)$porcentaje_almacena_alto_suelo_si; ?>,
             <?php echo (float)$valor4; ?>
         ];
         const datosElementosAlmacenamiento = [
             <?php echo (int)$almacena_balde; ?>,
             <?php echo (int)$almacena_canasta; ?>,
             <?php echo (int)$almacena_estante; ?>,
             <?php echo (int)$almacena_caja; ?>,
             <?php echo (int)$almacena_na; ?>
         ];
         const datosEstadoTechoAlmacenamiento = [
             <?php echo (int)$estado_techo_almacenamiento_bueno; ?>,
             <?php echo (int)$estado_techo_almacenamiento_regular; ?>,
             <?php echo (int)$estado_techo_almacenamiento_malo; ?>
         ];
         const datosEstadoParedesAlmacenamiento = [
             <?php echo (int)$estado_paredes_almacenamiento_bueno; ?>,
             <?php echo (int)$estado_paredes_almacenamiento_regular; ?>,
             <?php echo (int)$estado_paredes_almacenamiento_malo; ?>
         ];
         const datosEstadoPisoAlmacenamiento = [
             <?php echo (int)$estado_piso_almacenamiento_bueno; ?>,
             <?php echo (int)$estado_piso_almacenamiento_regular; ?>,
             <?php echo (int)$estado_piso_almacenamiento_malo; ?>
         ];
         const datosEstadoTechoPreparacion = [
             <?php echo (int)$estado_techo_bueno; ?>,
             <?php echo (int)$estado_techo_regular; ?>,
             <?php echo (int)$estado_techo_malo; ?>
         ];
         const datosEstadoParedesPreparacion = [
             <?php echo (int)$estado_paredes_bueno; ?>,
             <?php echo (int)$estado_paredes_regular; ?>,
             <?php echo (int)$estado_paredes_malo; ?>
         ];
         const datosEstadoPisoPreparacion = [
             <?php echo (int)$estado_piso_bueno; ?>,
             <?php echo (int)$estado_piso_regular; ?>,
             <?php echo (int)$estado_piso_malo; ?>
         ];
         const datosMaterialTechoAlmacenamiento = [
             <?php echo (int)$material_techo_almacenamiento_eternit; ?>,
             <?php echo (int)$material_techo_almacenamiento_tejas; ?>,
             <?php echo (int)$material_techo_almacenamiento_plastico; ?>,
             <?php echo (int)$material_techo_almacenamiento_zinc; ?>,
             <?php echo (int)$material_techo_almacenamiento_concreto; ?>,
             <?php echo (int)$material_techo_almacenamiento_metal; ?>,
             <?php echo (int)$material_techo_almacenamiento_paja; ?>,
             <?php echo (int)$material_techo_almacenamiento_otros; ?>
         ];
         const datosMaterialParedesAlmacenamiento = [
             <?php echo (int)$material_paredes_almacenamiento_bloque; ?>,
             <?php echo (int)$material_paredes_almacenamiento_prefabricado; ?>,
             <?php echo (int)$material_paredes_almacenamiento_bahareque; ?>,
             <?php echo (int)$material_paredes_almacenamiento_otros; ?>
         ];
         const datosMaterialPisoAlmacenamiento = [
             <?php echo (int)$material_piso_almacenamiento_baldosa; ?>,
             <?php echo (int)$material_piso_almacenamiento_cemento; ?>,
             <?php echo (int)$material_piso_almacenamiento_ladrillo; ?>,
             <?php echo (int)$material_piso_almacenamiento_madera; ?>,
             <?php echo (int)$material_piso_almacenamiento_otros; ?>
         ];
         const datosMaterialTechoPreparacion = [
             <?php echo (int)$material_techo_preparacion_zinc; ?>,
             <?php echo (int)$material_techo_eternit; ?>,
             <?php echo (int)$material_techo_teja_barro; ?>,
             <?php echo (int)$material_techo_preparacion_metal_acero; ?>,
             <?php echo (int)$material_techo_preparacion_concreto; ?>,
             <?php echo (int)$material_techo_preparacion_paja; ?>,
             <?php echo (int)$material_techo_preparacion_plastico; ?>,
             <?php echo (int)$material_techo_preparacion_otros; ?>,
             <?php echo (int)$material_techo_preparacion_sin_techo; ?>
         ];
         const datosMaterialParedesPreparacion = [
             <?php echo (int)$material_paredes_preparacion_ladrillo; ?>,
             <?php echo (int)$material_paredes_preparacion_bahareque; ?>,
             <?php echo (int)$material_paredes_preparacion_prefabricado; ?>,
             <?php echo (int)$material_paredes_preparacion_otros; ?>
         ];
         const datosMaterialPisoPreparacion = [
             <?php echo (int)$material_piso_preparacion_baldosa; ?>,
             <?php echo (int)$material_piso_cemento; ?>,
             <?php echo (int)$material_piso_preparacion_madera; ?>,
             <?php echo (int)$material_piso_preparacion_otros; ?>
         ];
         const datosAccesoAgua = [
             <?php echo (int)$acceso_agua_si; ?>,
             <?php echo (int)$acceso_agua_no; ?>,
             <?php echo (int)$acceso_agua_intermitente; ?>
         ];
         const datosObtencionAgua = [
             <?php echo (int)$acueducto; ?>,
             <?php echo (int)$rios_quebradas; ?>,
             <?php echo (int)$embotellada; ?>,
             <?php echo (int)$lluvia; ?>,
             <?php echo (int)$carrotanque; ?>,
             <?php echo (int)$pozo_agua; ?>,
             <?php echo (int)$otros_agua; ?>
         ];
         const datosAccesoElectricidad = [
             <?php echo (int)$acceso_electricidad_si; ?>,
             <?php echo (int)$acceso_electricidad_no; ?>,
             <?php echo (int)$acceso_electricidad_intermitente; ?>
         ];
         const datosTipoCoccion = [
             <?php echo (int)$gas_natural; ?>,
             <?php echo (int)$lena; ?>,
             <?php echo (int)$electricidad; ?>,
             <?php echo (int)$petroleo_gasolina; ?>,
             <?php echo (int)$desecho; ?>,
             <?php echo (int)$no_aplica; ?>
         ];
         const datosBasuraAlcantarillado = [
             <?php echo (int)$recoleccion_basuras_si; ?>,
             <?php echo (int)$recoleccion_basuras_no; ?>,
             <?php echo (int)$acceso_alcantarillado_si; ?>,
             <?php echo (int)$acceso_alcantarillado_no; ?>
         ];
         const datosDesechosOrganicos = [
             <?php echo (int)$disposicion_desechos_pae_enterrado; ?>,
             <?php echo (int)$disposicion_desechos_pae_quemado; ?>,
             <?php echo (int)$disposicion_desechos_pae_reciclan; ?>,
             <?php echo (int)$disposicion_desechos_pae_lombricultura; ?>,
             <?php echo (int)$disposicion_desechos_pae_tiran_lote; ?>
         ];
         const datosDesechosNoOrganicos = [
             <?php echo (int)$disposicion_no_organicos_pae_enterrado; ?>,
             <?php echo (int)$disposicion_no_organicos_pae_quemado; ?>,
             <?php echo (int)$disposicion_no_organicos_pae_reciclan; ?>,
             <?php echo (int)$disposicion_no_organicos_pae_lombricultura; ?>,
             <?php echo (int)$disposicion_no_organicos_pae_tiran_lote; ?>
         ];
         const datosAlmuerzoPreparado = [
             <?php echo (int)$almuerzo_preparado_sitio_si; ?>,
             <?php echo (int)$almuerzo_preparado_sitio_no; ?>
         ];
         const datosAlmuerzoTransportado = [
             <?php echo (int)$almuerzo_trasportado_si; ?>,
             <?php echo (int)$almuerzo_trasportado_no; ?>
         ];
         const datosComplementoPreparado = [
             <?php echo (int)$complemento_preparado_sitio_si; ?>,
             <?php echo (int)$complemento_preparado_sitio_no; ?>
         ];
         const datosComplementoIndustrializado = [
             <?php echo (int)$complemento_industrializado_si; ?>,
             <?php echo (int)$complemento_industrializado_no; ?>
         ];
         const datosComedorEscolar = [
             <?php echo (int)$comedor_escolar_si; ?>,
             <?php echo (int)$comedor_escolar_no; ?>
         ];
         const datosConceptoSanitario = [
             <?php echo (int)$si_tiene_favorable; ?>,
             <?php echo (int)$si_favorable_requerimientos; ?>,
             <?php echo (int)$si_desfavorable; ?>,
             <?php echo (int)$valor17; ?>
         ];
         const datosCantidadNinosSentados = [
             <?php echo (int)$cant_ninos_pae_sentados_todos; ?>,
             <?php echo (int)$posee_cucharones_pae_no; ?>
         ];
         const datosBateriasSanitarias = [
             <?php echo (int)$sanitario_personal_si; ?>,
             <?php echo (int)$sanitario_personal_no; ?>,
             <?php echo (int)$lavamanos_personal_si; ?>,
             <?php echo (int)$lavamanos_personal_no; ?>
         ];
         const datosCercaniaContaminacion = [
             <?php echo (int)$cercania_contaminacion_si; ?>,
             <?php echo (int)$cercania_contaminacion_no; ?>
         ];
         const datosZonaConflicto = [
             <?php echo (int)$zona_conflicto_si; ?>,
             <?php echo (int)$zona_conflicto_no; ?>
         ];
         const datosAfectacionConflicto = [
             <?php echo (int)$algo_frecuente_conflicto; ?>,
             <?php echo (int)$poco_frecuente_conflicto; ?>,
             <?php echo (int)$no_frecuencia_conflicto; ?>
         ];
         const datosEstadoSedeEducativa = [
             <?php echo (int)$estado_sede_nuevo_activo; ?>,
             <?php echo (int)$estado_sede_antiguo_activo; ?>,
             <?php echo (int)$estado_sede_cierre_temporal; ?>
         ];
         const datosEstadoTechoSedeEducativa = [
             <?php echo (int)$estado_techo_almacenamiento_bueno; ?>,
             <?php echo (int)$estado_techo_almacenamiento_regular; ?>,
             <?php echo (int)$estado_techo_almacenamiento_malo; ?>
         ];

         function mostrarSeccion(id) {
             // Oculta todas las secciones
             const secciones = document.querySelectorAll('.seccion');
             secciones.forEach(seccion => {
                 seccion.style.display = 'none';
             });

             // Muestra solo la sección seleccionada
             document.getElementById(id).style.display = 'block';

             // Actualiza las pestañas activas
             const tabs = document.querySelectorAll('.nav-link');
             tabs.forEach(tab => {
                 tab.classList.remove('active');
             });
             document.getElementById('btn-' + id).classList.add('active');
         }

         PAE_DASHBOARD.generarGraficaBarraProvinceasEnSedeEducativasConProblemas();
     </script>
 </script>
 <?php include 'admin/include/scriptsgober360.php'; ?>
 </body>

 </html>