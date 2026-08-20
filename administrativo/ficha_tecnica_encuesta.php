<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/FichaTecnicaEncuesta.php';
include './admin/classes/EspacioGeografico.php';

// Variables de configuracion - logo, municipio, departamento....
include './admin/include/generic_info_configuracion.php';

// Permisos
$view    = SessionData::getPermission(18);
$create  = SessionData::getPermission(19);
$edit    = SessionData::getPermission(20);
$permits = SessionData::getPermission(21);
if (!$view) { require 'permiso_denegado.php'; exit; }

// Datos
$resp = FichaTecnicaEncuesta::getAll(null);
$isvalid = $resp['output']['valid'] ?? false;
$arr = $resp['output']['response'] ?? [];
$modulo = 'Ficha Técnica Encuesta';

// Espacio geográfico
$espacioGeo = EspacioGeografico::getAll(null);
$espacioGeoResponse = $espacioGeo['output']['response'] ?? [];
$optionEspacioGeo = "<option value=''>Seleccione...</option>";
foreach ($espacioGeoResponse as $eg) {
  $id = htmlspecialchars($eg['id'] ?? '', ENT_QUOTES, 'UTF-8');
  $tp = htmlspecialchars($eg['tipo_estudio'] ?? '', ENT_QUOTES, 'UTF-8');
  $ob = htmlspecialchars($eg['observaciones'] ?? '', ENT_QUOTES, 'UTF-8');
  $optionEspacioGeo .= "<option value='{$id}'>{$tp} - {$ob}</option>";
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <style>
    :root{
      --nav-blue:#20427F;
      --nav-blue-2:#132b52;
      --card-radius: 16px;
      --soft-shadow: 0 18px 50px rgba(2,6,23,.10);
      --soft-border: rgba(15,23,42,.08);
      --muted:#64748b;
      --ink:#0f172a;
      --bg:#f6f8fb;
    }

    body{ background: var(--bg); }

    /* ✅ IMPORTANTÍSIMO: anulamos empujes del template */
    .content{
      padding-top: 0 !important;
      margin-top: 0 !important;
    }

    /* ✅ wrapper que vamos a “alinear” por JS */
    .page-wrap{
      margin-top: 0px;      /* lo ajusta JS */
      padding-top: 0 !important;
      padding-bottom: 24px;
    }

    /* ===== Page Head (SaaS) ===== */
    .saas-pagehead{
      margin-top: 0px; /* ✅ quitamos aire extra */
      border-radius: var(--card-radius);
      background:
        radial-gradient(900px 260px at 10% 0%, rgba(32,66,127,.20), transparent 55%),
        radial-gradient(700px 240px at 90% 0%, rgba(46,88,168,.18), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.90));
      border: 1px solid var(--soft-border);
      box-shadow: var(--soft-shadow);
      padding: 16px 16px;
    }

    .saas-title{
      display:flex; align-items:center; gap:12px; flex-wrap:wrap;
    }
    .saas-icon{
      width:44px; height:44px; border-radius:14px;
      display:grid; place-items:center;
      background: linear-gradient(135deg, var(--nav-blue), var(--nav-blue-2));
      color:#fff;
      box-shadow: 0 10px 24px rgba(32,66,127,.30);
    }
    .saas-title h3{ margin:0; font-weight:900; letter-spacing:-.2px; color: var(--ink); }
    .saas-sub{ color: var(--muted); font-size:.92rem; margin-top:2px; }

    .chipbar{ display:flex; gap:8px; flex-wrap:wrap; margin-top:10px; }
    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:6px 10px;
      border-radius: 999px;
      border:1px solid rgba(2,6,23,.08);
      background: rgba(255,255,255,.86);
      font-size:.82rem;
      color:#0f172a;
      line-height: 1;
    }
    .chip i{ color: var(--nav-blue); }

    /* ===== Cards ===== */
    .saas-card{
      border-radius: var(--card-radius);
      border: 1px solid var(--soft-border);
      box-shadow: var(--soft-shadow);
      overflow:hidden;
    }
    .saas-card .card-header{
      background: rgba(255,255,255,.92) !important;
      border-bottom: 1px solid var(--soft-border) !important;
      padding: 14px 16px !important;
    }
    .saas-card .card-body{ padding: 16px !important; }

    .section-title{
      display:flex; align-items:center; justify-content:space-between; gap:10px;
      margin: 6px 0 12px;
    }
    .section-title .left{
      display:flex; align-items:center; gap:10px;
    }
    .section-title .badge-soft{
      background: rgba(32,66,127,.10);
      color: var(--nav-blue);
      border: 1px solid rgba(32,66,127,.18);
      font-weight:800;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: .8rem;
    }
    .section-title h5{ margin:0; font-weight:900; letter-spacing:-.2px; color: var(--ink); }

    .form-floating > .form-control,
    .form-floating > .form-select{
      border-radius: 14px !important;
      border: 1px solid rgba(15,23,42,.10) !important;
    }
    textarea.form-control{ min-height: 110px; }
    .help-mini{ color:#64748b; font-size:.82rem; margin-top:6px; }

    /* ===== Sticky actions (mobile friendly) ===== */
    .sticky-actions{
      position: sticky;
      bottom: 12px;
      z-index: 20;
      background: rgba(255,255,255,.88);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(15,23,42,.08);
      border-radius: 16px;
      padding: 10px;
      box-shadow: 0 18px 40px rgba(2,6,23,.12);
      margin-top: 12px;
    }

    /* ===== Table ===== */
    .table-shell{
      border-radius: var(--card-radius);
      overflow:hidden;
      border: 1px solid var(--soft-border);
    }
    .dt-nowrap th, .dt-nowrap td{ white-space: nowrap; }
    .table thead th{ font-weight:900; }

    @media (max-width: 576px){
      .saas-pagehead{ padding: 14px; }
      .saas-icon{ width:40px; height:40px; border-radius: 13px; }
      textarea.form-control{ min-height: 120px; }
    }
  </style>
</head>

<body class="">
  <!-- Pre-loader -->
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  <?php include './admin/include/navbar.php'; ?>
  <?php include './admin/include/header.php'; ?>

  <div class="content">
    <div class="page-wrap">
      <div class="col-11 col-xl-11 mx-auto">

        <!-- ===== SaaS Page Head ===== -->
        <div class="saas-pagehead">
          <div class="saas-title">
            <div class="saas-icon"><i class="fas fa-file-signature"></i></div>
            <div>
              <h3><?= h($modulo) ?></h3>
              <div class="saas-sub">Registro, edición y listado. Todo queda guardado como ficha técnica oficial.</div>
            </div>
          </div>

          <div class="chipbar">
            <div class="chip"><i class="fas fa-shield-alt"></i> Permisos: <?= $view ? 'View' : '—' ?><?= $create ? ' · Create' : '' ?><?= $edit ? ' · Edit' : '' ?></div>
            <div class="chip"><i class="fas fa-database"></i> Registros: <?= (int)count($arr) ?></div>
            <div class="chip"><i class="fas fa-map-marked-alt"></i> Espacios geo: <?= (int)count($espacioGeoResponse) ?></div>
          </div>

          <div class="mt-2">
            <span id="spanEncuesta" class="small text-muted"></span>
            <span id="spanModulo" class="d-none">Ingreso y listado de <?= h($modulo) ?></span>
          </div>
        </div>

        <!-- ===== FORM CARD ===== -->
        <div class="saas-card card my-4" data-component-card="data-component-card">
          <div class="card-header">
            <div class="section-title">
              <div class="left">
                <span class="badge-soft"><i class="fas fa-pen-nib me-2"></i>Formulario</span>
                <h5 class="mb-0">Crear / Editar Ficha Técnica</h5>
              </div>
              <div class="text-muted small d-none d-md-block">
                Consejo: selecciona primero el <b>Espacio Geográfico</b> para autollenar tipo y universo.
              </div>
            </div>
          </div>

          <div class="card-body">
            <form class="row g-3" id="formfichaTecnicaEncuesta" role="form" autocomplete="off">
              <input type="hidden" name="op" id="op" />
              <input type="hidden" name="idFichaTecnicaEncuesta" id="idFichaTecnicaEncuesta" />

              <!-- =======================
                   BLOQUE 1: CONTEXTO
              ======================== -->
              <div class="col-12">
                <div class="section-title">
                  <div class="left">
                    <span class="badge-soft"><i class="fas fa-layer-group me-2"></i>Contexto</span>
                    <h5 class="mb-0">Información General</h5>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <select class="form-select" id="tbl_espacio_geografico_id" name="tbl_espacio_geografico_id" required>
                    <?= $optionEspacioGeo; ?>
                  </select>
                  <label for="tbl_espacio_geografico_id">Espacio Geográfico <span class="text-danger">*</span></label>
                </div>
                <div class="help-mini">Define el alcance y te autocompleta: tipo encuesta, periodo y universo.</div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <textarea id="realizada_por_o_encomendada_por" name="realizada_por_o_encomendada_por"
                    class="form-control"
                    placeholder="La persona natural o jurídica que la realizó y quién la encomendó." rows="4"></textarea>
                  <label for="realizada_por_o_encomendada_por">Realizada por / encomendada por <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="fuente_financiacion" name="fuente_financiacion" class="form-control"
                    placeholder="Fuente de financiación" rows="4"></textarea>
                  <label for="fuente_financiacion">Fuente de financiación</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="tipo_tamano_muestra_y_procedimiento_utilizado" name="tipo_tamano_muestra_y_procedimiento_utilizado" required>
                    <option value="probabilistico">Probabilístico</option>
                    <option value="no_probabilistico">No probabilístico</option>
                    <option value="parametrico">Paramétrico</option>
                  </select>
                  <label for="tipo_tamano_muestra_y_procedimiento_utilizado">Tipo de muestra y procedimiento</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="tipo_encuesta" name="tipo_encuesta" required disabled>
                    <option value="">NA</option>
                    <option value="Nacional">Nacional</option>
                    <option value="Departamental">Departamental</option>
                    <option value="Municipal">Municipal</option>
                  </select>
                  <label for="tipo_encuesta">Tipo Encuesta</label>
                </div>
                <div class="help-mini">Se autocompleta desde el Espacio Geográfico.</div>
              </div>

              <!-- =======================
                   BLOQUE 2: MUESTRA
              ======================== -->
              <div class="col-12 mt-2">
                <div class="section-title">
                  <div class="left">
                    <span class="badge-soft"><i class="fas fa-chart-pie me-2"></i>Muestra</span>
                    <h5 class="mb-0">Tamaño, universo y margen</h5>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <input id="tamano_muestra" name="tamano_muestra" class="form-control"
                    onKeyPress="return soloNumeros(event);" placeholder="Número">
                  <label for="tamano_muestra">Tamaño de muestra</label>
                </div>
                <div class="help-mini">Al cambiar, recalcula el margen de error.</div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="poblacion_objetivo" name="poblacion_objetivo" required>
                    <option value="habitantes">Habitantes</option>
                    <option value="votantes">Votantes</option>
                  </select>
                  <label for="poblacion_objetivo">Población Objetivo <span class="text-danger">*</span></label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <input type="text" disabled id="universo_representado" name="universo_representado" class="form-control"
                    placeholder="Universo representado.">
                  <label for="universo_representado" id="universo_representado_label">Universo representado</label>
                </div>
                <div class="help-mini">Se autocompleta desde el Espacio Geográfico según población objetivo.</div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="nivel_confiabilidad_porcentaje" name="nivel_confiabilidad_porcentaje" required>
                    <option value="1.95">95</option>
                    <option value="1.99">99</option>
                    <option value="1.90">90</option>
                    <option value="1.85">85</option>
                  </select>
                  <label for="nivel_confiabilidad_porcentaje">Nivel de confiabilidad (%)</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <input type="number" class="form-control" id="margen_error_porcentaje" name="margen_error_porcentaje"
                    placeholder="Margen" disabled readonly>
                  <label for="margen_error_porcentaje">Margen de error (%)</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="procedimiento_utilizado" name="procedimiento_utilizado" class="form-control"
                    placeholder="Procedimiento utilizado" rows="4"></textarea>
                  <label for="procedimiento_utilizado">Procedimiento Utilizado</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <textarea id="tipo_tamano_muestra_y_procedimiento_utilizado_descripcion"
                    name="tipo_tamano_muestra_y_procedimiento_utilizado_descripcion" class="form-control"
                    placeholder="Descripción del tipo/tamaño de muestra y procedimiento" rows="4"></textarea>
                  <label for="tipo_tamano_muestra_y_procedimiento_utilizado_descripcion">Descripción</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-floating">
                  <textarea id="tipo_estudio_descripcion" name="tipo_estudio_descripcion" class="form-control"
                    placeholder="Descripción del estudio" rows="4"></textarea>
                  <label for="tipo_estudio_descripcion">Descripción del estudio</label>
                </div>
              </div>

              <!-- =======================
                   BLOQUE 3: CONTENIDO
              ======================== -->
              <div class="col-12 mt-2">
                <div class="section-title">
                  <div class="left">
                    <span class="badge-soft"><i class="fas fa-align-left me-2"></i>Contenido</span>
                    <h5 class="mb-0">Temas, preguntas e indagados</h5>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="temas_concretos" name="temas_concretos" class="form-control"
                    placeholder="Temas concretos" rows="4"></textarea>
                  <label for="temas_concretos">Temas concretos</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="texto_literal_de_la_encuesta_o_preguntas" name="texto_literal_de_la_encuesta_o_preguntas"
                    class="form-control"
                    placeholder="Texto literal de preguntas y orden" rows="4"></textarea>
                  <label for="texto_literal_de_la_encuesta_o_preguntas">Texto literal de preguntas</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="candidatos_personas_instituciones_indagados"
                    name="candidatos_personas_instituciones_indagados" class="form-control"
                    placeholder="Candidatos/personas/entidades" rows="4"></textarea>
                  <label for="candidatos_personas_instituciones_indagados">Indagados</label>
                </div>
              </div>

              <!-- =======================
                   BLOQUE 4: CAMPO
              ======================== -->
              <div class="col-12 mt-2">
                <div class="section-title">
                  <div class="left">
                    <span class="badge-soft"><i class="fas fa-map-pin me-2"></i>Campo</span>
                    <h5 class="mb-0">Lugar, fecha y estado</h5>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <input type="text" class="form-control" disabled readonly
                    id="espacio_geografico_fecha_o_periodo_que_se_realizo"
                    name="espacio_geografico_fecha_o_periodo_que_se_realizo"
                    placeholder="Espacio geográfico y periodo" value="">
                  <label for="espacio_geografico_fecha_o_periodo_que_se_realizo">Espacio geográfico y periodo</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <input type="text" class="form-control datetimepicker"
                    id="espacio_geografico_fecha" name="espacio_geografico_fecha"
                    placeholder="Fecha o periodo" value="">
                  <label for="espacio_geografico_fecha">Fecha / periodo</label>
                </div>
                <div class="help-mini">Puedes escribir o usar el selector.</div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <select class="form-select" id="espacio_geografico_fecha_estado" name="espacio_geografico_fecha_estado" required>
                    <option value="Activa">Activa</option>
                    <option value="Cerrada">Cerrada</option>
                  </select>
                  <label for="espacio_geografico_fecha_estado">Estado</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <input type="text" class="form-control" id="metodo_recoleccion" name="metodo_recoleccion"
                    placeholder="Método de recolección" value="">
                  <label for="metodo_recoleccion">Método de recolección</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="proposito_del_estudio" name="proposito_del_estudio" class="form-control"
                    placeholder="Propósito del estudio" rows="4"></textarea>
                  <label for="proposito_del_estudio">Propósito del estudio</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="estadisticos_responsables" name="estadisticos_responsables" class="form-control"
                    placeholder="Responsables" rows="4"></textarea>
                  <label for="estadisticos_responsables">Estadísticos responsables</label>
                </div>
              </div>

              <!-- =======================
                   BLOQUE 5: NOTAS
              ======================== -->
              <div class="col-12 mt-2">
                <div class="section-title">
                  <div class="left">
                    <span class="badge-soft"><i class="fas fa-sticky-note me-2"></i>Notas</span>
                    <h5 class="mb-0">Declaración y avisos</h5>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-floating">
                  <textarea id="declaracion" name="declaracion" class="form-control"
                    placeholder="Declaración" rows="4"></textarea>
                  <label for="declaracion">Declaración</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-8">
                <div class="form-floating">
                  <textarea id="avisos" name="avisos" class="form-control"
                    placeholder="Avisos o comentarios adicionales" rows="4"></textarea>
                  <label for="avisos">Avisos</label>
                </div>
              </div>

              <div class="col-sm-12 col-md-6">
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" id="habilitado" name="habilitado" value="si" checked>
                  <label class="form-check-label fw-bold" for="habilitado">Ficha Técnica Habilitada</label>
                </div>
                <small class="text-muted">Desmarque si desea deshabilitar esta ficha técnica.</small>
              </div>

              <!-- Sticky actions (mobile) -->
              <div class="col-12">
                <div class="sticky-actions">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">
                      <i class="fas fa-info-circle me-1"></i>
                      Recuerda: al cambiar Espacio Geográfico se ajusta universo y tipo encuesta.
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                      <button type="button"
                        onclick="UTIL.clearForm('formfichaTecnicaEncuesta'), FICHATECNICAENCUESTA.emptyCells()"
                        class="btn btn-phoenix-secondary px-4">
                        Cancelar
                      </button>
                      <?php if ($create && $edit): ?>
                        <button type="button" class="btn btn-primary px-4"
                          onclick="FICHATECNICAENCUESTA.validateData();">
                          Guardar
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>

        <!-- ===== TABLE CARD ===== -->
        <div class="saas-card card mb-4">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2">
                <span class="badge-soft"><i class="fas fa-table me-2"></i>Listado</span>
                <h5 class="mb-0 fw-bold">Fichas Técnicas</h5>
              </div>
              <div class="text-muted small">
                Tip: usa buscar para filtrar (DataTable).
              </div>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-shell">
              <div class="table-responsive">
                <table id="dynamictable" class="table table-striped table-sm fs-9 mb-0 dt-nowrap">
                  <thead>
                    <tr class="border-1">
                      <th>Habilitada</th>
                      <th>Acciones</th>
                      <th>Realizada por</th>
                      <th>Fuente financiación</th>
                      <th>Tipo muestra/proc.</th>
                      <th>Temas</th>
                      <th>Indagados</th>
                      <th>Espacio y periodo</th>
                      <th>Margen (%)</th>
                      <th>Tipo estudio</th>
                      <th>Propósito</th>
                      <th>Universo</th>
                      <th>Método</th>
                      <th>Conf. (%)</th>
                      <th>Estadísticos</th>
                      <th>Declaración</th>
                      <th>Avisos</th>
                    </tr>
                  </thead>
                  <tbody class="list">
                    <?php if ($isvalid && count($arr) > 0): ?>
                      <?php foreach ($arr as $item): ?>
                        <tr>
                          <td>
                            <?php if (($item['habilitado'] ?? '') === 'si'): ?>
                              <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Activo</span>
                            <?php else: ?>
                              <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Inactivo</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="d-flex gap-1">
                              <?php if ($edit): ?>
                                <button type="button" class="btn btn-sm btn-primary" title="Editar"
                                  onclick="FICHATECNICAENCUESTA.editData(<?= (int)($item['id'] ?? 0) ?>)">
                                  <i class="uil uil-edit"></i>
                                </button>
                              <?php endif; ?>
                              <?php if ($create): ?>
                                <button type="button" class="btn btn-sm"
                                  style="background:linear-gradient(135deg,#1e7e34,#155724);color:#fff;border:0;border-radius:8px;"
                                  title="Duplicar como nuevo registro"
                                  onclick="FICHATECNICAENCUESTA.duplicar(<?= (int)($item['id'] ?? 0) ?>)">
                                  <i class="fas fa-copy"></i>
                                </button>
                              <?php endif; ?>
                            </div>
                          </td>
                          <td><?= h($item['realizada_por_o_encomendada_por'] ?? '') ?></td>
                          <td><?= h($item['fuente_financiacion'] ?? '') ?></td>
                          <td><?= h($item['tipo_tamano_muestra_y_procedimiento_utilizado'] ?? '') ?></td>
                          <td><?= h($item['temas_concretos'] ?? '') ?></td>
                          <td><?= h($item['candidatos_personas_instituciones_indagados'] ?? '') ?></td>
                          <td><?= h($item['espacio_geografico_fecha_o_periodo_que_se_realizo'] ?? '') ?></td>
                          <td><?= h($item['margen_error_porcentaje'] ?? '') ?></td>
                          <td><?= h($item['tipo_estudio'] ?? '') ?></td>
                          <td><?= h($item['proposito_del_estudio'] ?? '') ?></td>
                          <td><?= h($item['universo_representado'] ?? '') ?></td>
                          <td><?= h($item['metodo_recoleccion'] ?? '') ?></td>
                          <td><?= h($item['nivel_confiabilidad_porcentaje'] ?? '') ?></td>
                          <td><?= h($item['estadisticos_responsables'] ?? '') ?></td>
                          <td><?= h($item['declaracion'] ?? '') ?></td>
                          <td><?= h($item['avisos'] ?? '') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="17" class="text-center py-4 text-muted">No hay registros.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <?php include './admin/include/footer.php'; ?>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <?php include 'admin/include/gerenic_script.php'; ?>

  <script src="assets/js/vendor-all.min.js"></script>
  <script src="assets/js/plugins/jquery.min.js"></script>
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/pcoded.min.js"></script>

  <?php include './admin/include/generic_dataTables.php'; ?>

  <script src="vendors/flatpickr/flatpickr.min.js"></script>
  <script type="text/javascript" src="admin/js/fichatecnicaencuesta.js"></script>

  <!-- ✅ FIX MASTER: ALINEA CONTRA EL HEADER REAL (sube o baja) -->
  <script>
    $(function(){

      function getHeaderEl(){
        // ✅ lista de candidatos (Phoenix / PCoded / otros)
        return document.querySelector('.pcoded-header')
          || document.querySelector('.navbar-top')
          || document.querySelector('header')
          || document.querySelector('.navbar')
          || null;
      }

      function alignWrapToHeader(){
        var wrap = document.querySelector('.page-wrap');
        if (!wrap) return;

        var headerEl = getHeaderEl();
        if (!headerEl) return;

        var headerRect = headerEl.getBoundingClientRect();
        var wrapRect   = wrap.getBoundingClientRect();

        // queremos que wrap arranque justo debajo del header (con 10px de aire)
        var desiredTop = headerRect.bottom + 10;
        var delta = desiredTop - wrapRect.top;

        // ✅ limita el ajuste para evitar saltos raros
        if (delta > 220) delta = 220;
        if (delta < -220) delta = -220;

        // delta positivo baja, delta negativo sube
        wrap.style.marginTop = delta + "px";
      }

      // aplica varias veces por si el template carga con delays
      setTimeout(alignWrapToHeader, 20);
      setTimeout(alignWrapToHeader, 120);
      setTimeout(alignWrapToHeader, 300);
      setTimeout(alignWrapToHeader, 600);

      window.addEventListener('resize', function(){
        setTimeout(alignWrapToHeader, 80);
      });

      // Helper: recalcular margen con delay (evita carreras con AJAX/autollenado)
      function safeRecalcMargin(delayMs = 120){
        setTimeout(function(){
          if (!window.FICHATECNICAENCUESTA || typeof FICHATECNICAENCUESTA.margenError !== "function") return;

          var uni = ($("#universo_representado").val() || "").toString().trim();
          var ncm = ($("#nivel_confiabilidad_porcentaje").val() || "").toString().trim();
          var tm  = ($("#tamano_muestra").val() || "").toString().trim();

          if (uni === "" || tm === "" || ncm === "") {
            $("#margen_error_porcentaje").val("");
            return;
          }

          FICHATECNICAENCUESTA.margenError();
        }, delayMs);
      }

      // Conectar select Espacio Geográfico
      $("#tbl_espacio_geografico_id").off("change.__saasfix").on("change.__saasfix", function(){
        if (window.FICHATECNICAENCUESTA && typeof FICHATECNICAENCUESTA.getInformacionEspacioGeografico === "function") {
          FICHATECNICAENCUESTA.getInformacionEspacioGeografico(this.value);
        }
        safeRecalcMargin(280);
      });

      $("#tamano_muestra").off("keyup.__saasfix change.__saasfix").on("keyup.__saasfix change.__saasfix", function(){
        safeRecalcMargin(120);
      });

      $("#nivel_confiabilidad_porcentaje").off("change.__saasfix").on("change.__saasfix", function(){
        safeRecalcMargin(120);
      });

      $("#poblacion_objetivo").off("change.__saasfix").on("change.__saasfix", function(){
        if (window.FICHATECNICAENCUESTA && typeof FICHATECNICAENCUESTA.getValidarUniverso === "function") {
          FICHATECNICAENCUESTA.getValidarUniverso(this.value);
        }
        safeRecalcMargin(180);
      });

      // Flatpickr
      if (typeof flatpickr !== "undefined" && $(".datetimepicker").length) {
        $(".datetimepicker").each(function(){
          flatpickr(this, { enableTime:false, dateFormat:"Y-m-d" });
        });
      }

      // DataTable seguro
      if ($.fn.DataTable && $("#dynamictable").length) {
        if (!$.fn.DataTable.isDataTable("#dynamictable")) {
          $("#dynamictable").DataTable({
            pageLength: 25,
            order: [],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" }
          });
        }
      }

      safeRecalcMargin(320);
    });
  </script>

  <?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
