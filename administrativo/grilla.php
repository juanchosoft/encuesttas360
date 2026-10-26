<?php
include './admin/include/head.php';

require './admin/include/generic_classes.php';
include './admin/classes/Usuario.php';
include './admin/classes/CargosPublicos.php';
include './admin/classes/Departamento.php';
include './admin/classes/GrillaPlantilla.php';

$view = SessionData::hasPermission('estudios.grilla.view');
$create = SessionData::hasPermission('estudios.grilla.create');
$edit = SessionData::hasPermission('estudios.grilla.update');
$delete = SessionData::hasPermission('estudios.grilla.delete');

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

$respPlantillas = GrillaPlantilla::getAll([]);
$plantillas = $respPlantillas['output']['response'] ?? [];

$arrCargosPub = CargosPublicos::getAll(null);
$cargosPublicos = $arrCargosPub['output']['response'] ?? [];

$arrDep = Departamento::getAll(null);
$departamentos = $arrDep['output']['response'] ?? [];
$depPrincipal = Util::getDepartamentoPrincipal();

function gr_h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$totalPlantillas = count($plantillas);
$totalActivas = 0;
$totalCandidatosTipo = 0;
$totalEnUso = 0;

foreach ($plantillas as $pl) {
    if (($pl['habilitado'] ?? '') === 'si') {
        $totalActivas++;
    }
    if (($pl['tipo'] ?? '') === 'candidatos') {
        $totalCandidatosTipo++;
    }
    if ((int)($pl['total_preguntas_uso'] ?? 0) > 0) {
        $totalEnUso++;
    }
}
?>
<!DOCTYPE html>
<html lang="es-CO" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

</head>
<body class="g360-page">
<main class="main" id="top">
    <?php include './admin/include/navbar.php'; ?>
    <?php include './admin/include/header.php'; ?>

    <div class="content">
        <div class="g360-shell">

            <section class="g360-hero">
                <div class="g360-hero-grid">
                    <div>
                        <div class="g360-eyebrow">
                            <i class="fas fa-users"></i>
                            Cuestionarios y Estudios · Configuración Sencilla
                        </div>
                        <h1>Preguntas Grilla</h1>
                        <p>
                            Cree tablas reutilizables de candidatos, atributos o emociones en 4 pasos sencillos y úselas dentro de cualquier pregunta de su cuestionario.
                        </p>
                    </div>
                    <div class="g360-kpis">
                        <div class="g360-kpi">
                            <strong id="kpiTotalPlantillas"><?= (int)$totalPlantillas ?></strong>
                            <span>Tablas creadas</span>
                        </div>
                        <div class="g360-kpi">
                            <strong id="kpiTotalActivas"><?= (int)$totalActivas ?></strong>
                            <span>Tablas activas</span>
                        </div>
                        <div class="g360-kpi">
                            <strong id="kpiTotalCandidatos"><?= (int)$totalCandidatosTipo ?></strong>
                            <span>De candidatos</span>
                        </div>
                        <div class="g360-kpi">
                            <strong id="kpiTotalEnUso"><?= (int)$totalEnUso ?></strong>
                            <span>En uso en encuestas</span>
                        </div>
                    </div>
                </div>
            </section>

            <div id="grMensajeGlobal" class="gr-alert-banner d-none" role="alert"></div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="mb-1" style="font-size:24px;font-weight:800;">Mis tablas disponibles</h2>
                    <span class="text-muted" style="font-size:16px;">Seleccione una tabla para editarla, duplicarla o cree una nueva con el asistente paso a paso.</span>
                </div>
                <?php if ($create): ?>
                    <button type="button" class="gr-btn gr-btn-primary" id="btnNuevaTabla">
                        <i class="fas fa-plus-circle"></i>
                        Crear nueva tabla de candidatos
                    </button>
                <?php endif; ?>
            </div>

            <section class="gr-wizard-shell d-none" id="grWizardContainer">
                <div class="gr-wizard-header">
                    <div>
                        <span class="gr-badge gr-badge-blue mb-1" id="grPasoIndicadorTexto">Paso 1 de 4</span>
                        <h2 class="mb-0" id="grWizardTitulo" style="font-size:24px;font-weight:800;">Nueva tabla de candidatos</h2>
                    </div>
                    <div class="gr-steps-nav" id="grStepsNav">
                        <button type="button" class="gr-step-pill active" data-step="1">1. ¿Qué quiere preguntar?</button>
                        <button type="button" class="gr-step-pill" data-step="2">2. Candidatos o renglones</button>
                        <button type="button" class="gr-step-pill" data-step="3">3. Preguntas por candidato</button>
                        <button type="button" class="gr-step-pill" data-step="4">4. Así lo verá el encuestador</button>
                    </div>
                </div>

                <div class="gr-wizard-body">
                    <input type="hidden" id="grPlantillaId" value="0">

                    <div id="grAvisoEnUso" class="gr-alert-banner gr-alert-error d-none"></div>

                    <div class="gr-step-pane" id="grStep1">
                        <h3 class="mb-2" style="font-size:22px;font-weight:800;">Paso 1 de 4: Empiece con una plantilla rápida o desde cero</h3>
                        <p class="gr-help">Puede elegir un modelo listo para ahorrar tiempo; después podrá cambiar cualquier nombre o pregunta.</p>

                        <div class="gr-template-cards">
                            <button type="button" class="gr-tpl-btn" data-preset="gobernacion">
                                <strong><i class="fas fa-user-check text-primary me-1"></i> Grilla de gobernación</strong>
                                <span>Conocimiento (Sí/No), y si lo conoce: imagen, ideología, si votaría por él y asociaciones.</span>
                            </button>
                            <button type="button" class="gr-tpl-btn" data-preset="alcaldia">
                                <strong><i class="fas fa-city text-primary me-1"></i> Grilla de alcaldía</strong>
                                <span>Conocimiento, imagen favorable o desfavorable e intención de voto por candidato del municipio.</span>
                            </button>
                            <button type="button" class="gr-tpl-btn" data-preset="escala15">
                                <strong><i class="fas fa-star-half-alt text-primary me-1"></i> Escala 1 a 5 (Atributos)</strong>
                                <span>Calificación de 1 a 5 para varios temas, servicios o atributos en una sola tabla.</span>
                            </button>
                            <button type="button" class="gr-tpl-btn" data-preset="emociones">
                                <strong><i class="fas fa-smile text-primary me-1"></i> Emociones</strong>
                                <span>Evalúa qué emoción despierta cada personaje o institución a partir de una tarjeta de opciones.</span>
                            </button>
                        </div>

                        <div class="row g-4">
                            <div class="col-12 col-lg-7">
                                <label class="gr-label" for="grNombre">Nombre de la tabla <span class="text-danger">*</span></label>
                                <span class="gr-help">Un nombre claro para identificarla cuando arme el cuestionario (ej. "Candidatos Gobernación Santander" o "Candidatos Alcaldía Bucaramanga").</span>
                                <input type="text" id="grNombre" class="gr-input" placeholder="Ej: Candidatos Gobernación">
                            </div>

                            <div class="col-12 col-lg-5">
                                <label class="gr-label" for="grTipo">¿Qué tipo de tabla es?</label>
                                <span class="gr-help">Define cómo se presenta al encuestador y en los reportes.</span>
                                <select id="grTipo" class="gr-select">
                                    <option value="candidatos">Tabla de candidatos o personajes</option>
                                    <option value="escala">Tabla de calificación o escala por temas</option>
                                    <option value="libre">Otra tabla personalizada</option>
                                </select>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="gr-label" for="grLibreto">Indicaciones para el encuestador (opcional)</label>
                                <span class="gr-help">Aparecerá en un recuadro destacado arriba de la tabla ("Lea al encuestado").</span>
                                <textarea id="grLibreto" class="gr-textarea" rows="3" placeholder="Ej: Ahora le voy a mencionar varias personalidades para que me diga si las conoce..."></textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="gr-label" for="grNotaTabulacion">Nota interna de análisis o tabulación (opcional)</label>
                                <span class="gr-help">Solo la ven los administradores al configurar y analizar resultados.</span>
                                <textarea id="grNotaTabulacion" class="gr-textarea" rows="3" placeholder="Ej: Tabular conocimiento e imagen sobre el total de encuestados..."></textarea>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="gr-label" for="grPresentacion">Forma de mostrar al encuestador</label>
                                <select id="grPresentacion" class="gr-select">
                                    <option value="tarjetas">Un candidato por pantalla (Recomendado en celular)</option>
                                    <option value="tabla">Tabla completa en una sola vista</option>
                                    <option value="auto">Automático según el tamaño de pantalla</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="gr-label" for="grHabilitado">Estado de la tabla</label>
                                <select id="grHabilitado" class="gr-select">
                                    <option value="si">Activa (disponible para cuestionarios)</option>
                                    <option value="no">Inactiva (oculta en nuevos cuestionarios)</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="gr-label" for="grDescripcion">Descripción corta (opcional)</label>
                                <input type="text" id="grDescripcion" class="gr-input" placeholder="Ej: Estudio departamental">
                            </div>
                        </div>
                    </div>

                    <div class="gr-step-pane d-none" id="grStep2">
                        <h3 class="mb-2" style="font-size:22px;font-weight:800;">Paso 2 de 4: Candidatos o renglones de la tabla</h3>
                        <p class="gr-help">Puede pegarlos uno por renglón, agregarlos uno por uno o traer los candidatos ya registrados en Personal Político.</p>

                        <div class="card p-3 mb-4" style="background:#F8FAFC;border:2px solid #D0D5DD;border-radius:14px;">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="gr-label" for="grFiltroCargo">Traer candidatos registrados por cargo</label>
                                    <select id="grFiltroCargo" class="gr-select">
                                        <option value="">Todos los cargos</option>
                                        <?php foreach ($cargosPublicos as $cp): ?>
                                            <option value="<?= gr_h($cp['id'] ?? '') ?>"><?= gr_h($cp['nombre'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="gr-label" for="grFiltroDep">Departamento</label>
                                    <select id="grFiltroDep" class="gr-select">
                                        <option value="">Todos los departamentos</option>
                                        <?php foreach ($departamentos as $dep): ?>
                                            <?php $codD = $dep['codigo_departamento'] ?? ''; ?>
                                            <option value="<?= gr_h($codD) ?>" <?= ($codD == $depPrincipal ? 'selected' : '') ?>>
                                                <?= gr_h($codD . ' - ' . ($dep['departamento'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="button" class="gr-btn gr-btn-outline w-100" id="btnTraerCandidatos">
                                        <i class="fas fa-download"></i> Traer candidatos registrados
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12 col-lg-5">
                                <label class="gr-label" for="grPegarFilas">Pegar varios nombres (uno por renglón)</label>
                                <span class="gr-help">Escriba o pegue una lista de nombres y pulse "Agregar a la lista".</span>
                                <textarea id="grPegarFilas" class="gr-textarea mb-2" rows="5" placeholder="Ejemplo:&#10;Candidato 1&#10;Candidato 2&#10;Candidato 3"></textarea>
                                <button type="button" class="gr-btn gr-btn-outline w-100" id="btnAgregarPegados">
                                    <i class="fas fa-list-ul"></i> Agregar estos nombres a la lista
                                </button>
                            </div>

                            <div class="col-12 col-lg-7">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="gr-label mb-0">Lista actual (<span id="grContadorFilas">0</span>)</label>
                                    <button type="button" class="gr-btn gr-btn-outline" id="btnAgregarFilaIndividual">
                                        <i class="fas fa-plus"></i> Agregar un renglón
                                    </button>
                                </div>
                                <div id="grFilasContainer"></div>
                            </div>
                        </div>
                    </div>

                    <div class="gr-step-pane d-none" id="grStep3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <h3 class="mb-1" style="font-size:22px;font-weight:800;">Paso 3 de 4: Preguntas que se harán por cada candidato</h3>
                                <p class="gr-help mb-0">Defina qué se pregunta de cada candidato, sus opciones (una por renglón) y cuándo debe mostrarse.</p>
                            </div>
                            <button type="button" class="gr-btn gr-btn-primary" id="btnAgregarColumna">
                                <i class="fas fa-plus"></i> Agregar pregunta por candidato
                            </button>
                        </div>

                        <div id="grColumnasContainer"></div>
                    </div>

                    <div class="gr-step-pane d-none" id="grStep4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <h3 class="mb-1" style="font-size:22px;font-weight:800;">Paso 4 de 4: Así lo verá el encuestador</h3>
                                <p class="gr-help mb-0">Pruebe aquí mismo las respuestas. Si marca que NO conoce a un candidato, verá cómo se pasa automáticamente al siguiente.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="gr-btn gr-btn-outline active" id="btnVistaCelular">
                                    <i class="fas fa-mobile-alt"></i> Vista Celular (Tarjetas)
                                </button>
                                <button type="button" class="gr-btn gr-btn-outline" id="btnVistaComputador">
                                    <i class="fas fa-desktop"></i> Vista Computador (Tabla)
                                </button>
                            </div>
                        </div>

                        <div id="grPreviewArea" class="gr-preview-box"></div>
                    </div>
                </div>

                <div class="gr-wizard-footer">
                    <button type="button" class="gr-btn gr-btn-outline" id="btnCancelarWizard">
                        Cancelar y volver a la lista
                    </button>
                    <div class="d-flex gap-3">
                        <button type="button" class="gr-btn gr-btn-outline d-none" id="btnPasoAnterior">
                            <i class="fas fa-arrow-left"></i> Paso anterior
                        </button>
                        <button type="button" class="gr-btn gr-btn-primary" id="btnPasoSiguiente">
                            Siguiente paso <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="button" class="gr-btn gr-btn-success d-none" id="btnGuardarPlantilla">
                            <i class="fas fa-check-circle"></i> Guardar tabla de candidatos
                        </button>
                    </div>
                </div>
            </section>

            <section id="grListaSection">
                <div id="grCardsContainer" class="gr-card-grid"></div>
            </section>

        </div>
    </div>
</main>

<?php include 'admin/include/gerenic_script.php'; ?>
<?php include './admin/include/generic_dataTables.php'; ?>
<script>
    window.GRILLA_PERMISOS = {
        view: <?= $view ? 'true' : 'false' ?>,
        create: <?= $create ? 'true' : 'false' ?>,
        edit: <?= $edit ? 'true' : 'false' ?>,
        delete: <?= $delete ? 'true' : 'false' ?>
    };
</script>
<script type="text/javascript" src="admin/js/grilla.js"></script>
<?php include 'admin/include/scriptsgober360.php'; ?>
</body>
</html>
