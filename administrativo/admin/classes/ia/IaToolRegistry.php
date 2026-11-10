<?php

require_once __DIR__ . '/../DbConection.php';
require_once __DIR__ . '/../Util.php';
require_once __DIR__ . '/../Sondeo.php';
require_once __DIR__ . '/../RespuestaSondeo.php';
require_once __DIR__ . '/../FichaTecnicaEncuesta.php';
require_once __DIR__ . '/../AnalisisEstudio.php';
require_once __DIR__ . '/../Dashboard.php';
require_once __DIR__ . '/../RespuestaCuestionario.php';
require_once __DIR__ . '/../PartidoPolitico.php';
require_once __DIR__ . '/../Participantes.php';
require_once __DIR__ . '/../EspacioGeografico.php';
require_once __DIR__ . '/../InformeIA.php';
require_once __DIR__ . '/../CertificacionEncuestador.php';
require_once __DIR__ . '/IaDbConsulta.php';
require_once __DIR__ . '/IaAnalitica.php';
require_once __DIR__ . '/../IaRecursos.php';

class IaToolRegistry
{
    public const DESCRIPCION_GENERAR_INFORME = <<<TXT
Genera un informe HTML guardado en el módulo Informes IA (con descarga a PDF con membrete institucional). Úsala SOLO cuando el usuario pida explícitamente un informe, un reporte o un PDF — nunca por iniciativa propia ni porque una respuesta de chat sería larga. Un informe pedido queda exento del límite de extensión del chat: sé tan completo como los datos lo ameriten. Debe leer como un informe de un analista experto en encuestas y estadística electoral: bien estructurado, con hallazgos concretos e interpretación, nunca una simple lista de cifras. Antes de llamar a esta tool, ya debiste haber consultado con otras tools todos los datos reales que vas a citar — nunca inventes una cifra aquí.

Etiquetas permitidas: h1 h2 h3 h4 p div span table thead tbody tr th td ul ol li b strong i em small blockquote br hr, más <grafico> y <dato/> descritos abajo. Cualquier otra etiqueta (incluida img, a, script, style, caption, tfoot) se elimina automáticamente — para titular una tabla usa un <h4> o un <p><b>texto</b></p> justo antes del <table>, nunca dentro de él; para una fila de "Total", agrégala como una <tr> más dentro de <tbody> con las celdas en <b>negrita</b>, nunca uses <tfoot>. Mantén cada celda de tabla corta (una cifra, un nombre, un porcentaje): si necesitas enumerar muchos elementos (por ejemplo una lista larga de candidatos), hazlo en un <ul> aparte, nunca amontonado dentro de una sola celda — una celda muy larga rompe el ancho de columnas del PDF. Todas las filas de un <table> deben tener exactamente el mismo número de <td>/<th> que el encabezado — nunca omitas ni agregues columnas de más en una fila. El único atributo permitido es class, y solo con estos valores exactos (cualquier otro se elimina):

- s360-kpis (en un <table>) / s360-kpi (en cada <td>) / s360-kpi-value / s360-kpi-label — para mostrar 2 a 4 cifras clave en tarjetas, así:
  <table class="s360-kpis"><tr>
    <td class="s360-kpi"><div class="s360-kpi-value">66.7%</div><div class="s360-kpi-label">Intención de voto</div></td>
    <td class="s360-kpi"><div class="s360-kpi-value">6</div><div class="s360-kpi-label">Respuestas registradas</div></td>
  </tr></table>
- s360-callout + uno de: s360-callout-info / s360-callout-warning / s360-callout-danger / s360-callout-success — para un párrafo destacado (advertencia metodológica, hallazgo importante, dato positivo). Ejemplo: <div class="s360-callout s360-callout-warning">Con solo 6 respuestas, ningún porcentaje es estadísticamente representativo.</div>
- s360-badge-dato / s360-badge-interpretacion (en un <span>) — etiqueta corta antes de una frase, para distinguir explícitamente un dato verificado de tu propia lectura analítica. Ejemplo: <span class="s360-badge-dato">DATO</span> El 66.7% de las respuestas favorecen a X. <span class="s360-badge-interpretacion">INTERPRETACIÓN</span> Esto no es representativo dado el tamaño de muestra.
- s360-quote (en un <div> o <blockquote>) — para resaltar la conclusión o recomendación más importante del informe, una sola vez, cerca del final.
- s360-table-highlight (en un <table>) — para la tabla de resultados más importante del informe, cuando quieras que destaque visualmente sobre las demás.

GRÁFICOS (úsalos siempre que haya cifras por opción; son la forma correcta de graficar). Nunca dibujes barras con caracteres (█, ▇, ■, |||, guiones, emojis): en el PDF salen como signos de interrogación. Usa la etiqueta especial <grafico> con <dato/>, que el sistema convierte en barras de colores idénticas en pantalla y en PDF:
  <grafico tipo="barras" titulo="P3. Situación de El Bagre frente a hace un año">
    <dato etiqueta="Mejor" valor="24.7" color="verde"/>
    <dato etiqueta="Igual" valor="35.6" color="azul"/>
    <dato etiqueta="Peor" valor="38.7" color="rojo"/>
    <dato etiqueta="NS/NR" valor="1.0" color="gris"/>
  </grafico>
- tipo="barras": una barra horizontal por opción (la más legible; úsala por defecto). Los valores son porcentajes de 0 a 100 (unidad="%" por defecto; para conteos usa unidad="" o unidad="personas").
- tipo="dona" o tipo="torta": gráfico circular con leyenda de colores y porcentajes; ideal para composiciones de pocas categorías (género, rangos de edad, estrato, sí/no, aprobación). Usa de 2 a 8 categorías que sumen 100 %; con más categorías o con ranking de candidatos usa barras.
- tipo="apilada": una sola barra al 100 % dividida por opciones, con leyenda; ideal para escalas (muy bueno / bueno / regular / malo) o para sí/no.
- Comparar dos o tres grupos (por ejemplo municipios, género o edad) en las mismas opciones: tipo="barras" series="Hombres|Mujeres" y en cada dato valor="41.2|36.8" (un valor por serie, separados por |).
- Colores permitidos: azul, verde, rojo, naranja, morado, turquesa, amarillo, gris, azul-oscuro. Úsalos con sentido: verde para lo favorable o positivo, rojo para lo desfavorable o negativo, gris para NS/NR u otros, azul o los demás para opciones neutras. Sin color se asignan automáticamente. Máximo 20 datos por gráfico, etiquetas cortas, una cifra por barra con un decimal como máximo.
- Un gráfico ya muestra el valor de cada opción: no repitas los mismos números en una tabla ni en un párrafo.

PÁGINAS DE RESULTADOS (formato profesional de presentación, una por pregunta o tema). Envuelve cada pregunta o tema en <pagina titulo="TEMA CORTO" pregunta="Pregunta: enunciado literal de la pregunta"> ... </pagina>. Dentro va un <grafico> (o dos) y un único bloque <analisis> con 2 a 3 párrafos cortos que interpreten el resultado (qué dice el dato, magnitud de la diferencia, lectura política o de gestión, sin repetir todas las cifras). El sistema dibuja cada pagina en su propia hoja horizontal con banda de título, la pregunta, el gráfico a la izquierda y el análisis a la derecha. Reglas: titulo en 2 a 5 palabras (por ejemplo Gestión del alcalde, Escenario electoral, Demográficos, Problemáticas percibidas); pregunta con el enunciado real; un solo tema por pagina; no pongas dentro de la pagina títulos h1/h2 ni tablas; el análisis máximo 600 caracteres (lo que exceda se recorta). Para una pregunta con más de 12 opciones, parte el gráfico en dos pagina (por ejemplo problemáticas principales y otras). Ejemplo:
  <pagina titulo="Gestión del alcalde" pregunta="Pregunta: ¿Aprueba o desaprueba la gestión del alcalde?">
    <grafico tipo="dona"><dato etiqueta="Desaprueba" valor="82.1" color="rojo"/><dato etiqueta="Aprueba" valor="14.3" color="verde"/><dato etiqueta="NS/NR" valor="3.6" color="gris"/></grafico>
    <analisis><p>La desaprobación alcanza el 82,1 % frente a una aprobación de 14,3 %.</p><p>La brecha supera los 67 puntos.</p></analisis>
  </pagina>

INFORME PROFESIONAL TIPO ESTUDIO (el estándar cuando pidan un informe de una encuesta o estudio para entregar a un cliente). El sistema agrega SOLO una portada (con título, subtítulo, fecha y la marca) y, si pasas ficha_tecnica_id, una página de FICHA TÉCNICA completa tomada de la base de datos: por eso nunca escribas tú la ficha técnica, la portada ni el número de páginas, y pasa siempre ficha_tecnica_id cuando el informe sea de una encuesta. El parámetro subtitulo es el ámbito del estudio (por ejemplo Cimitarra - Santander). Estructura del contenido_html: 1) un h2 Introducción con un párrafo sobre el objetivo y la base de datos del estudio, y una fila de s360-kpis con las cifras clave (muestra efectiva, margen de error, fecha de campo); 2) las pagina de resultados en el orden del cuestionario, empezando por demográficos (género, edad), luego problemáticas, gestión y aprobación, imagen de personajes y por último intención de voto o escenarios; 3) una pagina titulo="Conclusiones generales" (sin atributo pregunta ni gráfico) con un <ol> de 3 a 5 conclusiones, cada una en una o dos frases con su cifra; 4) una pagina titulo="Recomendaciones" con un <ul> de 3 a 5 viñetas accionables, cada una en una o dos frases. Mantén cada una de esas dos pagina por debajo de 900 caracteres en total. Usa solo datos reales consultados y solo respuestas válidas; no menciones calidad del trabajo de campo, encuestas rechazadas ni pendientes salvo que el usuario lo pida.

Informe ejecutivo (cuando pidan algo breve, visual, resumen o solo gráficos): máximo dos o tres líneas de texto por sección, una fila de s360-kpis con las cifras clave, y un gráfico por pregunta con su título (el enunciado corto de la pregunta), sin tablas largas ni párrafos extensos; si el usuario pide solo gráficos, deja como texto únicamente el título, una conclusión de una línea por bloque y la ficha técnica resumida. Respeta el alcance que pida el usuario: si indica qué incluir o excluir (por ejemplo no hablar de la validación o de la calidad del trabajo de campo), cíñete a eso y no lo agregues por iniciativa propia.

Estructura recomendada de un informe completo: título (usar el campo titulo, no repetirlo como h1 salvo que quieras un subtítulo), un párrafo de resumen ejecutivo, una fila de s360-kpis con las cifras más importantes, secciones con h2/h3 (hallazgos, metodología si aplica, desglose demográfico), al menos una tabla de datos real, callouts para advertencias metodológicas (muestra pequeña, sesgo geográfico, margen de error alto, ficha técnica faltante — así como ya haces en el chat), y una s360-quote final con la recomendación u observación principal. Sé generoso en detalle y extensión si los datos lo ameritan — no comprimas un hallazgo rico en una sola frase por ahorrar espacio.

FORMATO DIAPOSITIVAS (formato="diapositivas"): úsalo cuando pidan una presentación, diapositivas, PowerPoint o Excel, cuando entreguen imágenes o logos, o cuando pidan una fuente o un estilo propios. Con este formato el sistema genera desde el mismo contenido tres archivos: PDF en láminas 16:9, PowerPoint (.pptx) y Excel (.xlsx, con una hoja y un gráfico nativo por pregunta); los tres quedan disponibles en Informes IA. Todo el documento usa la fuente Poppins. Reglas:
- La portada, la ficha técnica y la última lámina las arma el sistema. Pasa titulo; subtitulo (separa los renglones con |, por ejemplo "El Bagre, Antioquia|Noviembre 2026"); realizado_por (por ejemplo "Dark Energy SAS"); ficha_tecnica_id; logo_id (recurso de tipo logo: el sistema pone su versión blanca sobre fondo oscuro, la negra sobre fondo claro y la original en las conclusiones, y toma de él la paleta de colores de todo el informe, gráficos incluidos); portada_id (imagen de fondo de la portada y, si no pasas cierre_id, de la última lámina; el sistema le baja el brillo según brillo, de 10 a 90, por defecto 35).
- Las imágenes se pasan siempre por su id de recurso: nunca escribas base64 ni enlaces dentro de contenido_html. Si el usuario adjuntó una imagen en el chat, su id viene en el mensaje; si da un enlace, descárgalo antes con descargar_imagen; con listar_recursos ves las imágenes ya guardadas.
- Cada <pagina> es una lámina y admite uno a tres <grafico>, un <analisis> (hasta 900 caracteres; <analisis max="1400"> lo amplía hasta 1700), una <nota>texto</nota> al pie (por ejemplo "*Los datos corresponden a una respuesta con opción múltiple") y una <table> (comparativo o ranking; se dibuja bajo el gráfico o sola). Una pagina sin gráfico ni análisis, con un <ol> o <ul>, se dibuja como tarjetas: es el formato de las conclusiones y recomendaciones (hasta 6 ítems de un párrafo completo cada uno); si el título contiene "Conclusiones" o "Recomendaciones" lleva el logo original.
- Fuera de las pagina, un <h2> abre una lámina de texto (por ejemplo la introducción) que admite párrafos, s360-kpis, callouts, tablas y listas.
- Atributos de <grafico> adicionales: tipo="barras_v" (barras verticales), "barras" (horizontales, con el valor a la derecha de la barra), "dona", "torta", "apilada"; orden="desc" ordena de mayor a menor; multiple="si" marca preguntas de selección múltiple (no se ajustan a 100); titulo rotula cada gráfico cuando hay varios en la lámina; series="A|B" con valor="x|y" para comparativos. En <dato> puedes agregar n="123" (casos) para que el Excel incluya la columna de casos. Los porcentajes de cada gráfico se ajustan automáticamente para sumar exactamente 100,0.
- Para el ranking de personajes (reconocimiento y balance de imagen) usa ranking_reconocimiento_balance: devuelve las cifras ya calculadas y no debes recalcularlas. Para cruces usa cruzar_preguntas, para márgenes y pruebas calcular_estadistico y para contrastar dos estudios comparar_estudios.
- Después de generar, responde en una o dos líneas qué contiene y que quedaron disponibles el PDF, el PowerPoint y el Excel en Informes IA.
TXT;

    private const HERRAMIENTAS = [
        'consultar_sondeos' => [
            'permiso' => 'estudios.sondeos.view',
            'description' => 'Lista los sondeos definidos en el sistema (nombre, tipo, fechas, si está vigente/habilitado y los nombres de sus candidatos vinculados). Si se da un id, devuelve ese sondeo. Por defecto devuelve una versión resumida (sin descripciones largas ni datos internos de cada candidato) — úsala así para respuestas rápidas. Pon detalle=true SOLO si el usuario pide el desglose completo.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'description' => 'id de un sondeo específico (opcional)'],
                    'detalle' => ['type' => 'boolean', 'description' => 'true devuelve todos los campos, incluidas descripciones y datos completos de cada candidato; por defecto false (resumen)'],
                ],
            ],
            'handler' => 'handleConsultarSondeos',
        ],
        'consultar_resultados_sondeo' => [
            'permiso' => 'resultados.sondeos.view',
            'description' => 'Devuelve las estadísticas completas de un sondeo: totales por opción/candidato con porcentajes, y desglose por ideología, género, edad, nivel de ingresos, nivel educativo, departamento y municipio de los votantes que respondieron.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['tbl_sondeo_id' => ['type' => 'integer', 'description' => 'id del sondeo (obligatorio, ver consultar_sondeos)']],
                'required' => ['tbl_sondeo_id'],
            ],
            'handler' => 'handleConsultarResultadosSondeo',
        ],
        'consultar_ficha_tecnica' => [
            'permiso' => 'estudios.ficha_tecnica.view',
            'description' => 'Lista las fichas técnicas de encuesta: margen de error, nivel de confiabilidad, tamaño de muestra, método de recolección, propósito del estudio, universo representado, estadísticos responsables y fuente de financiación. Usar siempre antes de opinar sobre la significancia estadística de un resultado. Por defecto devuelve solo los campos metodológicos clave; pon detalle=true SOLO si el usuario pide la ficha completa (declaraciones, avisos, texto literal, procedimiento).',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'description' => 'id de una ficha técnica específica (opcional)'],
                    'detalle' => ['type' => 'boolean', 'description' => 'true devuelve la ficha completa; por defecto false (campos metodológicos clave)'],
                ],
            ],
            'handler' => 'handleConsultarFichaTecnica',
        ],
        'consultar_analisis_estudio' => [
            'permiso' => 'analisis.estudio.view',
            'description' => 'Lista los análisis de estudio existentes (candidato, grilla, fórmula usada, resultado calculado y observaciones del investigador).',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un análisis específico (opcional)']],
            ],
            'handler' => 'handleConsultarAnalisisEstudio',
        ],
        'consultar_dashboard_resumen' => [
            'permiso' => 'resultados.sondeos.view',
            'permiso_alt' => 'resultados.cuestionarios.view',
            'description' => 'Devuelve los KPIs generales del sistema (total de votantes, grillas, análisis, candidatos, sondeos, fórmulas), el desglose de votantes por ideología/género/edad/ingresos, los análisis por mes, el top de candidatos por número de análisis, y el estado de las grillas (habilitadas/deshabilitadas).',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['grilla_id' => ['type' => 'integer', 'description' => 'filtrar por una grilla específica (opcional)']],
            ],
            'handler' => 'handleConsultarDashboardResumen',
        ],
        'consultar_cuestionarios' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Devuelve las estadísticas de respuestas de un cuestionario (ficha técnica): total de intentos, respuestas por pregunta, filtros disponibles y KPIs. Es el conjunto de datos con más respuestas reales del sistema.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica del cuestionario (obligatorio)']],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleConsultarCuestionarios',
        ],
        'consultar_preguntas_cuestionario' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Devuelve las preguntas reales de un cuestionario (texto, capítulo, tipo) junto con sus opciones de respuesta y cuántas respuestas recibió cada opción (agregado, nunca el detalle individual de quién respondió qué). Usar esta tool para analizar el contenido de un cuestionario — nunca intentar leer las preguntas con consultar_base_de_datos. Por defecto devuelve cada pregunta con su total de respuestas y las opciones en formato compacto ("opción: n"); pon detalle=true SOLO si el usuario pide el desglose completo por pregunta y opción.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica del cuestionario (obligatorio, ver consultar_ficha_tecnica)'],
                    'detalle' => ['type' => 'boolean', 'description' => 'true devuelve la estructura completa de cada pregunta y opción; por defecto false (formato compacto)'],
                ],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleConsultarPreguntasCuestionario',
        ],
        'buscar_en_preguntas_cuestionarios' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Busca un término (nombre de persona, candidato, funcionario, gobernador, alcalde, o una palabra clave temática) dentro del texto de TODAS las preguntas de TODOS los cuestionarios del sistema, sin necesidad de conocer de antemano a qué ficha técnica pertenecen. Úsala SIEMPRE que te pregunten por una persona o tema y no la encuentres (o no estés seguro de encontrarla) con consultar_personal_politico — muchas figuras públicas (gobernadores, alcaldes, funcionarios) solo existen mencionadas dentro del texto de las preguntas de un cuestionario, nunca como un candidato registrado formalmente, y esta es la única forma de ubicarlas. Devuelve, agrupadas por cuestionario, las preguntas que coinciden.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['termino' => ['type' => 'string', 'description' => 'nombre o palabra clave a buscar (obligatorio, mínimo 3 caracteres)']],
                'required' => ['termino'],
            ],
            'handler' => 'handleBuscarEnPreguntasCuestionarios',
        ],
        'consultar_partidos_politicos' => [
            'permiso' => 'politica.partidos.view',
            'description' => 'Lista los partidos políticos registrados.',
            'input_schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un partido específico (opcional)']]],
            'handler' => 'handleConsultarPartidosPoliticos',
        ],
        'consultar_personal_politico' => [
            'permiso' => 'politica.personal_politico.view',
            'description' => 'Lista los candidatos/personal político registrado, con su partido, cargo público y ubicación geográfica.',
            'input_schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un candidato específico (opcional)']]],
            'handler' => 'handleConsultarPersonalPolitico',
        ],
        'consultar_espacio_geografico' => [
            'permiso' => 'estudios.espacio_geografico.view',
            'description' => 'Lista los espacios geográficos configurados en el sistema.',
            'input_schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer', 'description' => 'id de un espacio específico (opcional)']]],
            'handler' => 'handleConsultarEspacioGeografico',
        ],
        'consultar_votantes' => [
            'permiso' => 'politica.votantes.view',
            'description' => 'Devuelve conteos agregados de votantes activos por dimensión demográfica (ideología, rango de edad, nivel de ingresos, género, nivel educativo). Nunca devuelve datos de identificación individual (nombre, cédula, teléfono, dirección) — solo totales agrupados.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['dimension' => [
                    'type' => 'string',
                    'enum' => ['ideologia', 'rango_edad', 'nivel_ingresos', 'genero', 'nivel_educacion', 'todas'],
                    'description' => 'dimensión a agrupar; "todas" devuelve el conteo por las 5 dimensiones a la vez (por defecto)',
                ]],
            ],
            'handler' => 'handleConsultarVotantes',
        ],
        'consultar_certificacion_resumen' => [
            'permiso' => 'certificaciones.dashboard.view',
            'permiso_alt' => 'certificaciones.view',
            'description' => 'Resumen de validación de encuestadores (evidencias de campo de encuestas/sondeos): totales, pendientes, bien, mal, en revisión, anuladas, % bien sobre revisadas, con audio/GPS, método IA vs manual, desglose por tipo (encuesta/sondeo) y top encuestadores. Devuelve el bloque lectura_como_en_pantalla con las cifras tal como las muestra la pantalla de validación (válidas, rechazadas, pendientes de revisión humana con su desglose, sin revisar, anuladas): úsalo para redactar. Usa esta tool para el estado de la validación de una encuesta (con su ficha) en lugar de consultas SQL. Usa esta tool cuando pregunten cómo va la validación en general, cuántas pendientes hay, o el panorama global. Filtros opcionales por estado, tipo, sondeo/encuesta, fechas o encuestador.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'estado_revision' => [
                        'type' => 'string',
                        'enum' => ['pendiente', 'bien', 'mal', 'en_revision', 'anulada'],
                        'description' => 'filtrar KPIs a un estado (opcional)',
                    ],
                    'origen_tipo' => [
                        'type' => 'string',
                        'enum' => ['sondeo', 'cuestionario'],
                        'description' => 'sondeo o encuesta/cuestionario (opcional)',
                    ],
                    'tbl_sondeo_id' => ['type' => 'integer', 'description' => 'id de un sondeo concreto (opcional)'],
                    'tbl_ficha_tecnica_encuesta_id' => ['type' => 'integer', 'description' => 'id de una ficha técnica/encuesta concreta (opcional)'],
                    'tbl_usuario_id' => ['type' => 'integer', 'description' => 'id del encuestador (opcional)'],
                    'fecha_desde' => ['type' => 'string', 'description' => 'YYYY-MM-DD (opcional)'],
                    'fecha_hasta' => ['type' => 'string', 'description' => 'YYYY-MM-DD (opcional)'],
                    'revision_metodo' => [
                        'type' => 'string',
                        'enum' => ['manual', 'ia', 'ninguno'],
                        'description' => 'método de revisión (opcional)',
                    ],
                    'con_audio' => [
                        'type' => 'string',
                        'enum' => ['0', '1'],
                        'description' => '1=solo con audio, 0=sin audio (opcional)',
                    ],
                ],
            ],
            'handler' => 'handleConsultarCertificacionResumen',
        ],
        'consultar_certificacion_encuestador' => [
            'permiso' => 'certificaciones.dashboard.view',
            'permiso_alt' => 'certificaciones.view',
            'description' => 'Desempeño de validación por encuestador: cuántas evidencias ha registrado, cuántas pendientes/bien/mal/en revisión/anuladas, % válidas (bien), audio, sondeos vs encuestas. Busca por nombre parcial del encuestador o por su id. Úsala cuando pregunten "cómo va X", "cuántas hizo Juan", "quién tiene más pendientes", etc.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'encuestador_nombre' => ['type' => 'string', 'description' => 'nombre o apellido parcial del encuestador (opcional si se da tbl_usuario_id)'],
                    'tbl_usuario_id' => ['type' => 'integer', 'description' => 'id del encuestador (opcional si se da nombre)'],
                    'origen_tipo' => [
                        'type' => 'string',
                        'enum' => ['sondeo', 'cuestionario'],
                        'description' => 'filtrar por tipo (opcional)',
                    ],
                    'tbl_sondeo_id' => ['type' => 'integer', 'description' => 'id de sondeo (opcional)'],
                    'tbl_ficha_tecnica_encuesta_id' => ['type' => 'integer', 'description' => 'id de encuesta/ficha (opcional)'],
                    'fecha_desde' => ['type' => 'string', 'description' => 'YYYY-MM-DD (opcional)'],
                    'fecha_hasta' => ['type' => 'string', 'description' => 'YYYY-MM-DD (opcional)'],
                    'limite' => ['type' => 'integer', 'description' => 'máximo de encuestadores a devolver (1-50, default 20)'],
                ],
            ],
            'handler' => 'handleConsultarCertificacionEncuestador',
        ],
        'consultar_certificaciones' => [
            'permiso' => 'certificaciones.view',
            'permiso_alt' => 'certificaciones.dashboard.view',
            'description' => 'Lista o detalla evidencias de validación de encuestadores (sin audio binario). Sin id: listado filtrable (pendientes, por encuestador, por sondeo/encuesta, fechas; las que la IA dejó para decisión humana son estado en_revision con método ia). Con id: ficha de esa evidencia, historial de revisiones y las correcciones de respuestas hechas por administradores (fecha, quién, motivo). Estados: pendiente, bien (válida), mal, en_revision, anulada. Origen: sondeo o cuestionario (encuesta). No inventes IDs: primero resume o lista y luego pide detalle si hace falta.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'description' => 'id de una validación concreta para ver detalle + historial (opcional)'],
                    'estado_revision' => [
                        'type' => 'string',
                        'enum' => ['pendiente', 'bien', 'mal', 'en_revision', 'anulada'],
                    ],
                    'origen_tipo' => [
                        'type' => 'string',
                        'enum' => ['sondeo', 'cuestionario'],
                    ],
                    'tbl_sondeo_id' => ['type' => 'integer'],
                    'tbl_ficha_tecnica_encuesta_id' => ['type' => 'integer'],
                    'tbl_usuario_id' => ['type' => 'integer', 'description' => 'id del encuestador'],
                    'fecha_desde' => ['type' => 'string'],
                    'fecha_hasta' => ['type' => 'string'],
                    'revision_metodo' => [
                        'type' => 'string',
                        'enum' => ['manual', 'ia', 'ninguno'],
                    ],
                    'con_audio' => [
                        'type' => 'string',
                        'enum' => ['0', '1'],
                    ],
                    'limite' => ['type' => 'integer', 'description' => 'máximo de filas en listado (1-80, default 40)'],
                ],
            ],
            'handler' => 'handleConsultarCertificaciones',
        ],
        'generar_informe_html' => [
            'permiso' => 'ia.informes.create',
            'description' => self::DESCRIPCION_GENERAR_INFORME,
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'titulo' => ['type' => 'string', 'description' => 'título del informe (máx. 150 caracteres)'],
                    'contenido_html' => ['type' => 'string', 'description' => 'cuerpo del informe en HTML restringido a la whitelist de etiquetas indicada'],
                    'subtitulo' => ['type' => 'string', 'description' => 'subtítulo de la portada, normalmente el ámbito del estudio (por ejemplo Cimitarra - Santander); máx. 200 caracteres (opcional)'],
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica de la encuesta: el sistema agrega la página de FICHA TÉCNICA con los datos oficiales (opcional pero obligatorio en informes de una encuesta)'],
                    'formato' => ['type' => 'string', 'enum' => ['documento', 'diapositivas'], 'description' => 'documento (por defecto) o diapositivas (PDF 16:9 + PowerPoint + Excel, con imágenes y fuente Poppins)'],
                    'realizado_por' => ['type' => 'string', 'description' => 'solo diapositivas: entidad que aparece en «Realizado por» (por ejemplo Dark Energy SAS)'],
                    'logo_id' => ['type' => 'integer', 'description' => 'solo diapositivas: id de un recurso de tipo logo'],
                    'portada_id' => ['type' => 'integer', 'description' => 'solo diapositivas: id de la imagen de fondo de la portada'],
                    'cierre_id' => ['type' => 'integer', 'description' => 'solo diapositivas: id de la imagen de la última lámina (por defecto la de la portada)'],
                    'brillo' => ['type' => 'integer', 'description' => 'solo diapositivas: brillo de las imágenes de fondo, de 10 a 90 (por defecto 35)'],
                    'fecha_texto' => ['type' => 'string', 'description' => 'solo diapositivas: texto de fecha de la portada (por ejemplo Noviembre 2026); si el subtitulo ya trae un año, no se agrega'],
                    'paleta' => ['type' => 'object', 'description' => 'solo diapositivas: colores propios en hexadecimal (primario, secundario, acento, suave, texto); por defecto se toman del logo'],
                ],
                'required' => ['titulo', 'contenido_html'],
            ],
            'handler' => 'handleGenerarInformeHtml',
        ],
        'listar_recursos' => [
            'permiso' => 'ia.informes.create',
            'description' => 'Lista las imágenes guardadas del usuario (logos y fotos) con su id, nombre, tipo y tamaño. Los ids se usan en generar_informe_html (logo_id, portada_id, cierre_id). Las imágenes que el usuario adjunta en el chat quedan guardadas aquí automáticamente.',
            'input_schema' => ['type' => 'object', 'properties' => ['tipo' => ['type' => 'string', 'enum' => ['logo', 'foto', 'otro'], 'description' => 'filtrar por tipo (opcional)']]],
            'handler' => 'handleListarRecursos',
        ],
        'descargar_imagen' => [
            'permiso' => 'ia.informes.create',
            'description' => 'Descarga una imagen desde un enlace https público y la guarda como recurso local (se reconvierte y se limita a 8 MB). Devuelve el id del recurso para usarlo en un informe. Usa tipo=logo para logos y tipo=foto para fotografías. Quien pide la imagen es responsable de contar con el permiso de uso.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'url' => ['type' => 'string', 'description' => 'enlace https directo a la imagen (jpg, png, webp o gif)'],
                    'nombre' => ['type' => 'string', 'description' => 'nombre corto del recurso (opcional)'],
                    'tipo' => ['type' => 'string', 'enum' => ['logo', 'foto', 'otro'], 'description' => 'por defecto foto'],
                ],
                'required' => ['url'],
            ],
            'handler' => 'handleDescargarImagen',
        ],
        'ranking_reconocimiento_balance' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Calcula, para las preguntas de imagen de personajes de una encuesta (opciones imagen positiva, neutral, negativa y no lo reconoce), el reconocimiento (positiva + neutral + negativa), el balance de imagen (positiva menos negativa, en puntos porcentuales sobre la base total), el balance entre quienes lo reconocen, y los rankings de balance y de reconocimiento. Usa solo encuestas completas y reales. Las cifras ya están calculadas: no las recalcules.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica (obligatorio)'],
                    'municipio' => ['type' => 'string', 'description' => 'código del municipio para filtrar (opcional)'],
                    'preguntas_ids' => ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'limitar a estas preguntas (opcional; los ids salen de consultar_preguntas_cuestionario)'],
                ],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleRankingReconocimientoBalance',
        ],
        'cruzar_preguntas' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Tabla cruzada de dos preguntas de selección de una encuesta (por ejemplo género por gestión del alcalde): conteos, porcentaje de cada fila y de cada columna, base y prueba chi-cuadrado de asociación al 95 %. Los ids de pregunta salen de consultar_preguntas_cuestionario. Solo encuestas completas y reales. Una asociación no prueba causalidad.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica (obligatorio)'],
                    'pregunta_filas_id' => ['type' => 'integer', 'description' => 'id de la pregunta que va en las filas (obligatorio)'],
                    'pregunta_columnas_id' => ['type' => 'integer', 'description' => 'id de la pregunta que va en las columnas (obligatorio)'],
                    'municipio' => ['type' => 'string', 'description' => 'código del municipio para filtrar (opcional)'],
                ],
                'required' => ['ficha_tecnica_id', 'pregunta_filas_id', 'pregunta_columnas_id'],
            ],
            'handler' => 'handleCruzarPreguntas',
        ],
        'calcular_estadistico' => [
            'permiso' => 'ia.asistente.use',
            'description' => 'Cálculos estadísticos exactos: margen de error de una muestra (tipo=margen_error: n, poblacion opcional, proporcion_pct opcional, confianza), significancia de la diferencia entre dos proporciones (tipo=diferencia_proporciones: n1, n2, p1_pct, p2_pct) y tamaño de muestra necesario (tipo=tamano_muestra: margen_pp, poblacion opcional). Úsala en vez de calcular de memoria.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'tipo' => ['type' => 'string', 'enum' => ['margen_error', 'diferencia_proporciones', 'tamano_muestra']],
                    'n' => ['type' => 'number'], 'n1' => ['type' => 'number'], 'n2' => ['type' => 'number'],
                    'p1_pct' => ['type' => 'number'], 'p2_pct' => ['type' => 'number'], 'proporcion_pct' => ['type' => 'number'],
                    'poblacion' => ['type' => 'number'], 'margen_pp' => ['type' => 'number'], 'confianza' => ['type' => 'number', 'description' => '90, 95 o 99 (por defecto 95)'],
                ],
                'required' => ['tipo'],
            ],
            'handler' => 'handleCalcularEstadistico',
        ],
        'comparar_estudios' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Compara la misma pregunta en dos encuestas (por texto equivalente): porcentaje de cada opción en cada estudio, diferencia en puntos y si la diferencia es significativa al 95 %. Advierte que dos mediciones no son una tendencia salvo que población, método y redacción sean comparables.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_a_id' => ['type' => 'integer'], 'ficha_b_id' => ['type' => 'integer'],
                    'pregunta' => ['type' => 'string', 'description' => 'texto (aproximado) de la pregunta a comparar'],
                ],
                'required' => ['ficha_a_id', 'ficha_b_id', 'pregunta'],
            ],
            'handler' => 'handleCompararEstudios',
        ],
        'leer_informe' => [
            'permiso' => 'ia.informes.create',
            'description' => 'Lee un informe generado antes (título, subtítulo, opciones y el contenido fuente que se escribió para generar_informe_html) para poder revisarlo o corregirlo. Con él puedes preparar una versión corregida y guardarla con actualizar_informe.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'id del informe']],
                'required' => ['id'],
            ],
            'handler' => 'handleLeerInforme',
        ],
        'actualizar_informe' => [
            'permiso' => 'ia.informes.create',
            'description' => 'Reemplaza el contenido o las opciones de un informe en formato diapositivas ya generado, conservando la versión anterior. Envía el contenido_html completo corregido (el mismo lenguaje de generar_informe_html) y, si cambian, titulo, subtitulo y las opciones (realizado_por, logo_id, portada_id, cierre_id, brillo, fecha_texto, paleta). Pasa solo lo que cambia. Úsala cuando el usuario pida ajustar un informe existente (por ejemplo «cambia la lámina 5», «hazla más corta»).',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'description' => 'id del informe'],
                    'contenido_html' => ['type' => 'string', 'description' => 'contenido completo corregido'],
                    'titulo' => ['type' => 'string'], 'subtitulo' => ['type' => 'string'],
                    'realizado_por' => ['type' => 'string'], 'logo_id' => ['type' => 'integer'], 'portada_id' => ['type' => 'integer'],
                    'cierre_id' => ['type' => 'integer'], 'brillo' => ['type' => 'integer'], 'fecha_texto' => ['type' => 'string'],
                    'paleta' => ['type' => 'object'],
                ],
                'required' => ['id'],
            ],
            'handler' => 'handleActualizarInforme',
        ],
        'generar_excel_resultados' => [
            'permiso' => 'ia.informes.create',
            'description' => 'Crea un informe «de datos» con los resultados de todas las preguntas de selección de una encuesta (una hoja y un gráfico nativo por pregunta en Excel, con porcentajes y casos), disponible como Excel y PDF en Informes IA. Úsala cuando pidan el Excel o la base de resultados de una encuesta sin análisis redactado.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica (obligatorio)'],
                    'municipio' => ['type' => 'string', 'description' => 'código del municipio para filtrar (opcional)'],
                    'titulo' => ['type' => 'string', 'description' => 'título del informe (opcional)'],
                ],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleGenerarExcelResultados',
        ],
        'consultar_resultados_encuesta' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Resumen operativo de una encuesta: cuántas encuestas están completas, terminadas por filtro (la persona respondió NO al filtro y se cerró) o a la espera de audio; cuántos dijeron SÍ y cuántos NO al filtro; y el avance contra la meta por municipio y por zona. Solo cuenta encuestas reales (las de prueba no se incluyen). Usa esta herramienta antes que consultar_base_de_datos para preguntas de avance, metas o terminadas por filtro.',
            'input_schema' => [
                'type' => 'object',
                'properties' => ['ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica de la encuesta (obligatorio, ver consultar_ficha_tecnica)']],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleConsultarResultadosEncuesta',
        ],
        'consultar_resultados_tablas_candidatos' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Resultados de las preguntas de tabla de una encuesta (candidatos o ítems, por ejemplo conocimiento e imagen de candidatos, escalas de importancia o emociones): por cada candidato o ítem, cuántas personas respondieron, el porcentaje que lo conoce, el porcentaje de imagen favorable, la intención de voto y la nota promedio. Las tablas de alcaldía salen separadas por municipio. Se puede filtrar por municipio. Con detalle=true devuelve además cada pregunta de la tabla con sus opciones.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica de la encuesta (obligatorio)'],
                    'municipio' => ['type' => 'string', 'description' => 'código del municipio para filtrar (opcional)'],
                    'detalle' => ['type' => 'boolean', 'description' => 'true incluye el detalle por pregunta de cada candidato; por defecto false'],
                ],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleConsultarResultadosTablasCandidatos',
        ],
        'consultar_voto_encuesta' => [
            'permiso' => 'resultados.cuestionarios.view',
            'description' => 'Resultados del voto en cascada de una encuesta (voto directo, voto transferido y voto residual): por cada pregunta de lista, cuántas personas eligieron a cada candidato u opción fija (No sabe, Ninguno) y su porcentaje sobre quienes respondieron esa pregunta. Se puede filtrar por municipio.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'ficha_tecnica_id' => ['type' => 'integer', 'description' => 'id de la ficha técnica de la encuesta (obligatorio)'],
                    'municipio' => ['type' => 'string', 'description' => 'código del municipio para filtrar (opcional)'],
                ],
                'required' => ['ficha_tecnica_id'],
            ],
            'handler' => 'handleConsultarVotoEncuesta',
        ],
        'consultar_base_de_datos' => [
            'permiso' => 'ia.consulta_avanzada.use',
            'description' => IaDbConsulta::DESCRIPCION_TOOL,
            'input_schema' => [
                'type' => 'object',
                'properties' => ['sql' => ['type' => 'string', 'description' => 'una única sentencia SELECT sobre las tablas de dominio permitidas (ver descripción de la tool). Se le forzará un LIMIT 250 automáticamente.']],
                'required' => ['sql'],
            ],
            'handler' => 'handleConsultarBaseDeDatos',
        ],
    ];

    public static function definiciones(array $permisos, bool $superAdmin): array
    {
        $tools = [];
        foreach (self::HERRAMIENTAS as $nombre => $def) {
            $tienePermiso = $superAdmin
                || in_array($def['permiso'], $permisos, true)
                || (isset($def['permiso_alt']) && in_array($def['permiso_alt'], $permisos, true));
            if ($tienePermiso) {
                $tools[] = [
                    'name' => $nombre,
                    'description' => $def['description'],
                    'input_schema' => $def['input_schema'],
                ];
            }
        }
        return $tools;
    }

    private static bool $esAdminActual = false;

    public static function ejecutar(string $nombre, array $input, int $userId, array $permisos, bool $superAdmin): array
    {
        self::$esAdminActual = $superAdmin || in_array('ia.informes.manage', $permisos, true);
        $inicio = microtime(true);
        $def = self::HERRAMIENTAS[$nombre] ?? null;

        if ($def === null) {
            return ['error' => 'herramienta_desconocida', 'mensaje' => "La herramienta '$nombre' no existe."];
        }

        $tienePermiso = $superAdmin
            || in_array($def['permiso'], $permisos, true)
            || (isset($def['permiso_alt']) && in_array($def['permiso_alt'], $permisos, true));

        if (!$tienePermiso) {
            self::registrarLog($userId, $nombre, $input, null, 0, false, 'sin_permiso');
            return ['error' => 'sin_permiso', 'mensaje' => "El usuario no tiene el permiso requerido ({$def['permiso']}) para usar esta herramienta."];
        }

        try {
            $resultado = self::{$def['handler']}($input, $userId);
            $duracionMs = (int) round((microtime(true) - $inicio) * 1000);
            $filas = is_array($resultado) && array_is_list($resultado) ? count($resultado) : null;
            self::registrarLog($userId, $nombre, $input, $filas, $duracionMs, true, null);
            return $resultado;
        } catch (\Throwable $e) {
            $duracionMs = (int) round((microtime(true) - $inicio) * 1000);
            self::registrarLog($userId, $nombre, $input, null, $duracionMs, false, $e->getMessage());
            return ['error' => 'error_interno', 'mensaje' => 'No fue posible ejecutar la consulta.'];
        }
    }

    private static function registrarLog(int $userId, string $tool, array $input, ?int $filas, int $duracionMs, bool $exito, ?string $error): void
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "INSERT INTO " . $db->getTable('tbl_ia_tool_logs')
            . " (tbl_usuario_id, tool_nombre, tool_input, filas_devueltas, duracion_ms, exito, error, dt_create)
                VALUES (:usuario, :tool, :input, :filas, :duracion, :exito, :error, NOW())";
        $stmt = $pdo->prepare($q);
        $stmt->execute([
            ':usuario' => $userId,
            ':tool' => $tool,
            ':input' => json_encode($input),
            ':filas' => $filas,
            ':duracion' => $duracionMs,
            ':exito' => $exito ? 1 : 0,
            ':error' => $error,
        ]);
        $db->closeConect();
    }

    private const CAMPOS_SONDEO_RESUMEN = ['id', 'sondeo', 'tipo_sondeo', 'fecha_inicio', 'fecha_fin', 'habilitado', 'vigente', 'es_trivia'];

    private const CAMPOS_FICHA_RESUMEN = [
        'id', 'temas_concretos', 'habilitado', 'tipo_estudio', 'tipo_encuesta',
        'margen_error_porcentaje', 'nivel_confiabilidad_porcentaje', 'tamano_muestra',
        'metodo_recoleccion', 'universo_representado', 'poblacion_objetivo',
        'fuente_financiacion', 'realizada_por_o_encomendada_por', 'estadisticos_responsables',
        'proposito_del_estudio', 'espacio_geografico_fecha_o_periodo_que_se_realizo',
        'candidatos_personas_instituciones_indagados',
    ];

    private static function esDetalle(array $input): bool
    {
        return filter_var($input['detalle'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private static function proyectar(array $fila, array $campos): array
    {
        return array_intersect_key($fila, array_flip($campos));
    }

    private static function handleConsultarSondeos(array $input): array
    {
        $res = Sondeo::getAll(['id' => (int) ($input['id'] ?? 0)]);
        $filas = $res['output']['response'] ?? [];
        if (self::esDetalle($input) || !is_array($filas) || !array_is_list($filas)) {
            return $filas;
        }

        return array_map(function (array $fila): array {
            $resumen = self::proyectar($fila, self::CAMPOS_SONDEO_RESUMEN);
            $resumen['candidatos'] = array_values(array_filter(array_map(
                fn($c) => is_array($c) ? ($c['nombre_completo'] ?? null) : null,
                $fila['candidatos'] ?? []
            )));
            return $resumen;
        }, $filas);
    }

    private static function handleConsultarResultadosSondeo(array $input): array
    {
        $id = (int) ($input['tbl_sondeo_id'] ?? 0);
        if ($id <= 0) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'tbl_sondeo_id es obligatorio.'];
        }
        if (empty(Sondeo::getAll(['id' => $id])['output']['response'])) {
            return ['error' => 'no_encontrado', 'mensaje' => 'Ese sondeo no existe o fue eliminado.'];
        }
        $res = RespuestaSondeo::getEstadisticasCompletas(['tbl_sondeo_id' => $id]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarFichaTecnica(array $input): array
    {
        $res = FichaTecnicaEncuesta::getAll(['id' => (int) ($input['id'] ?? 0)]);
        $filas = $res['output']['response'] ?? [];
        if (self::esDetalle($input) || !is_array($filas) || !array_is_list($filas)) {
            return $filas;
        }

        return array_map(fn(array $fila): array => self::proyectar($fila, self::CAMPOS_FICHA_RESUMEN), $filas);
    }

    private static function handleConsultarAnalisisEstudio(array $input): array
    {
        $res = AnalisisEstudio::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarDashboardResumen(array $input): array
    {
        $rqst = ['grilla_id' => (int) ($input['grilla_id'] ?? 0)];
        return [
            'principales' => Dashboard::getEstadisticasPrincipales($rqst)['output']['response'] ?? null,
            'ideologia' => Dashboard::getVotantesPorIdeologia($rqst)['output']['response'] ?? null,
            'genero' => Dashboard::getVotantesPorGenero($rqst)['output']['response'] ?? null,
            'edad' => Dashboard::getVotantesPorEdad($rqst)['output']['response'] ?? null,
            'ingresos' => Dashboard::getVotantesPorIngresos($rqst)['output']['response'] ?? null,
            'analisis_por_mes' => Dashboard::getAnalisisPorMes($rqst)['output']['response'] ?? null,
            'top_candidatos' => Dashboard::getTopCandidatos($rqst)['output']['response'] ?? null,
            'grillas_por_estado' => Dashboard::getGrillasPorEstado($rqst)['output']['response'] ?? null,
        ];
    }

    private static function handleConsultarCuestionarios(array $input): array
    {
        $id = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($id <= 0) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'ficha_tecnica_id es obligatorio.'];
        }
        if (empty(FichaTecnicaEncuesta::getAll(['id' => $id])['output']['response'])) {
            return ['error' => 'no_encontrado', 'mensaje' => 'Esa ficha técnica no existe o fue eliminada.'];
        }
        $res = RespuestaCuestionario::getEstadisticas(['ficha_tecnica_id' => $id]);
        return $res['output']['response'] ?? $res;
    }

    private static function handleConsultarPreguntasCuestionario(array $input): array
    {
        $id = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($id <= 0) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'ficha_tecnica_id es obligatorio.'];
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $stmt = $pdo->prepare("SELECT p.id, p.texto_pregunta, p.tipo_pregunta, p.capitulo, p.orden
                                FROM " . $db->getTable('tbl_preguntas') . " p
                                INNER JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON f.id = p.tbl_ficha_tecnica_encuesta_id
                                WHERE p.tbl_ficha_tecnica_encuesta_id = :id AND p.habilitado = 'si'
                                  AND (p.eliminado = 0 OR p.eliminado IS NULL)
                                  AND (f.eliminado = 'no' OR f.eliminado IS NULL)
                                ORDER BY COALESCE(p.orden, 999999), p.id");
        $stmt->execute([':id' => $id]);
        $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($preguntas)) {
            $db->closeConect();
            return ['preguntas' => [], 'mensaje' => 'Esta ficha técnica no existe, fue eliminada o no tiene preguntas habilitadas registradas.'];
        }

        $stmt = $pdo->prepare("SELECT o.id, o.tbl_pregunta_id, o.texto_opcion, o.orden
                                FROM " . $db->getTable('tbl_opciones_respuesta') . " o
                                INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON p.id = o.tbl_pregunta_id
                                WHERE p.tbl_ficha_tecnica_encuesta_id = :id
                                  AND (p.eliminado = 0 OR p.eliminado IS NULL)
                                  AND (o.eliminado = 0 OR o.eliminado IS NULL)
                                ORDER BY o.tbl_pregunta_id, COALESCE(o.orden, 999999), o.id");
        $stmt->execute([':id' => $id]);
        $opcionesPorPregunta = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $opcionesPorPregunta[$fila['tbl_pregunta_id']][] = $fila;
        }

        $stmt = $pdo->prepare("SELECT r.tbl_pregunta_id, r.tbl_opcion_respuesta_id, COUNT(*) AS total
                                FROM " . $db->getTable('tbl_cuestionario_respuestas') . " r
                                INNER JOIN " . $db->getTable('tbl_cuestionario_intentos') . " i ON i.id = r.tbl_intento_id
                                INNER JOIN " . $db->getTable('tbl_preguntas') . " p ON p.id = r.tbl_pregunta_id
                                WHERE p.tbl_ficha_tecnica_encuesta_id = :id
                                  AND (p.eliminado = 0 OR p.eliminado IS NULL)
                                  AND (i.es_prueba = 0 OR i.es_prueba IS NULL)
                                  AND r.tbl_grilla_fila_id IS NULL
                                  AND r.tbl_grilla_columna_id IS NULL
                                GROUP BY r.tbl_pregunta_id, r.tbl_opcion_respuesta_id");
        $stmt->execute([':id' => $id]);
        $conteosPorPregunta = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $conteosPorPregunta[$fila['tbl_pregunta_id']][$fila['tbl_opcion_respuesta_id'] ?? 'texto_libre'] = (int) $fila['total'];
        }

        $db->closeConect();

        $resultado = [];
        $detalle = self::esDetalle($input);
        foreach ($preguntas as $pregunta) {
            $pid = $pregunta['id'];
            $conteos = $conteosPorPregunta[$pid] ?? [];

            if (!$detalle) {
                $compacta = [
                    'id' => (int) $pid,
                    'pregunta' => $pregunta['texto_pregunta'],
                    'capitulo' => $pregunta['capitulo'],
                    'total_respuestas' => array_sum($conteos),
                ];
                if ($compacta['total_respuestas'] > 0) {
                    $compacta['opciones'] = array_map(
                        fn(array $o): string => $o['texto_opcion'] . ': ' . ($conteos[$o['id']] ?? 0),
                        $opcionesPorPregunta[$pid] ?? []
                    );
                }
                $resultado[] = $compacta;
                continue;
            }

            $opciones = [];
            foreach ($opcionesPorPregunta[$pid] ?? [] as $opcion) {
                $opciones[] = [
                    'texto_opcion' => $opcion['texto_opcion'],
                    'total_respuestas' => $conteos[$opcion['id']] ?? 0,
                ];
            }
            $resultado[] = [
                'pregunta' => $pregunta['texto_pregunta'],
                'capitulo' => $pregunta['capitulo'],
                'tipo_pregunta' => $pregunta['tipo_pregunta'],
                'opciones' => $opciones,
                'respuestas_de_texto_libre' => $conteos['texto_libre'] ?? 0,
            ];
        }

        return ['preguntas' => $resultado];
    }

    private static function handleBuscarEnPreguntasCuestionarios(array $input): array
    {
        $termino = trim((string) ($input['termino'] ?? ''));
        if (mb_strlen($termino) < 3) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'termino es obligatorio y debe tener al menos 3 caracteres.'];
        }

        $db = new DbConection();
        $pdo = $db->openConect();

        $like = '%' . addcslashes($termino, '%_\\') . '%';
        $stmt = $pdo->prepare("SELECT p.id, p.tbl_ficha_tecnica_encuesta_id, p.texto_pregunta, p.enunciado_pregunta, p.capitulo,
                                       f.temas_concretos, f.habilitado AS ficha_habilitada
                                FROM " . $db->getTable('tbl_preguntas') . " p
                                INNER JOIN " . $db->getTable('tbl_ficha_tecnica_encuestas') . " f ON f.id = p.tbl_ficha_tecnica_encuesta_id
                                WHERE p.habilitado = 'si'
                                  AND (p.eliminado = 0 OR p.eliminado IS NULL)
                                  AND (f.eliminado = 'no' OR f.eliminado IS NULL)
                                  AND (p.texto_pregunta LIKE :like OR p.enunciado_pregunta LIKE :like)
                                ORDER BY p.tbl_ficha_tecnica_encuesta_id, COALESCE(p.orden, 999999), p.id
                                LIMIT 50");
        $stmt->execute([':like' => $like]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->closeConect();

        if (empty($filas)) {
            return ['cuestionarios' => [], 'mensaje' => 'No se encontró ninguna pregunta que mencione "' . $termino . '" en ningún cuestionario del sistema.'];
        }

        $porFicha = [];
        foreach ($filas as $fila) {
            $fid = $fila['tbl_ficha_tecnica_encuesta_id'];
            if (!isset($porFicha[$fid])) {
                $porFicha[$fid] = [
                    'ficha_tecnica_id' => $fid,
                    'nombre_cuestionario' => $fila['temas_concretos'],
                    'ficha_habilitada' => $fila['ficha_habilitada'],
                    'preguntas_coincidentes' => [],
                ];
            }
            $porFicha[$fid]['preguntas_coincidentes'][] = [
                'pregunta' => $fila['texto_pregunta'],
                'enunciado' => $fila['enunciado_pregunta'],
                'capitulo' => $fila['capitulo'],
            ];
        }

        return ['cuestionarios' => array_values($porFicha)];
    }

    private static function handleConsultarPartidosPoliticos(array $input): array
    {
        $res = PartidoPolitico::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarPersonalPolitico(array $input): array
    {
        $res = Participantes::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarEspacioGeografico(array $input): array
    {
        $res = EspacioGeografico::getAll(['id' => (int) ($input['id'] ?? 0)]);
        return $res['output']['response'] ?? [];
    }

    private static function opcionesInforme(array $input, int $userId): array
    {
        $opciones = ['formato' => 'diapositivas'];
        foreach (['logo_id', 'portada_id', 'cierre_id'] as $k) {
            $id = (int) ($input[$k] ?? 0);
            if ($id > 0) {
                if (IaRecursos::cargar($id, $userId, self::$esAdminActual) === null) {
                    return ['error' => 'recurso_no_encontrado', 'mensaje' => 'El recurso ' . $id . ' no existe o no es del usuario. Usa listar_recursos o descargar_imagen.'];
                }
                $opciones[$k] = $id;
            }
        }
        foreach (['realizado_por', 'fecha_texto'] as $k) {
            if (isset($input[$k]) && trim((string) $input[$k]) !== '') {
                $opciones[$k] = mb_substr(trim((string) $input[$k]), 0, 120);
            }
        }
        if (isset($input['brillo'])) {
            $opciones['brillo'] = max(10, min(90, (int) $input['brillo']));
        }
        if (isset($input['paleta']) && is_array($input['paleta'])) {
            $opciones['paleta'] = array_intersect_key($input['paleta'], array_flip(['primario', 'secundario', 'acento', 'suave', 'texto']));
        }
        return $opciones;
    }

    private static function handleGenerarInformeHtml(array $input, int $userId): array
    {
        $titulo = trim((string) ($input['titulo'] ?? ''));
        $contenido = (string) ($input['contenido_html'] ?? '');

        if ($titulo === '' || trim($contenido) === '') {
            return ['error' => 'parametro_faltante', 'mensaje' => 'titulo y contenido_html son obligatorios.'];
        }

        $fichaId = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($fichaId > 0 && empty(FichaTecnicaEncuesta::getAll(['id' => $fichaId])['output']['response'])) {
            return ['error' => 'no_encontrado', 'mensaje' => 'La ficha técnica indicada no existe o fue eliminada.'];
        }

        $opciones = null;
        if (($input['formato'] ?? 'documento') === 'diapositivas') {
            $opciones = self::opcionesInforme($input, $userId);
            if (isset($opciones['error'])) {
                return $opciones;
            }
        }

        $id = InformeIA::crear($userId, $titulo, $contenido, $fichaId > 0 ? $fichaId : null, (string) ($input['subtitulo'] ?? ''), $opciones);

        return [
            'id' => $id,
            'titulo' => $titulo,
            'mensaje' => $opciones !== null
                ? 'El informe se guardó correctamente en Informes IA (id ' . $id . '). Ya están disponibles el PDF en diapositivas, el PowerPoint y el Excel.'
                : 'El informe se guardó correctamente en el módulo Informes IA (id ' . $id . '). El usuario puede verlo y descargarlo en PDF desde ahí.',
        ];
    }

    private static function handleListarRecursos(array $input, int $userId): array
    {
        $todos = IaRecursos::listar($userId, self::$esAdminActual);
        $tipo = (string) ($input['tipo'] ?? '');
        return ['recursos' => $tipo !== '' ? array_values(array_filter($todos, static fn ($r) => $r['tipo'] === $tipo)) : $todos];
    }

    private static function handleDescargarImagen(array $input, int $userId): array
    {
        try {
            $r = IaRecursos::descargarUrl($userId, (string) ($input['url'] ?? ''), (string) ($input['nombre'] ?? ''), (string) ($input['tipo'] ?? 'foto'));
            return ['recurso' => $r, 'mensaje' => 'Imagen guardada como recurso ' . $r['id'] . '.'];
        } catch (RuntimeException $e) {
            return ['error' => 'descarga_fallida', 'mensaje' => $e->getMessage()];
        }
    }

    private static function fichaValida(int $fichaId): ?array
    {
        if ($fichaId <= 0) {
            return ['error' => 'parametro_faltante', 'mensaje' => 'ficha_tecnica_id es obligatorio.'];
        }
        if (empty(FichaTecnicaEncuesta::getAll(['id' => $fichaId])['output']['response'])) {
            return ['error' => 'no_encontrado', 'mensaje' => 'Esa ficha técnica no existe o fue eliminada.'];
        }
        return null;
    }

    private static function handleRankingReconocimientoBalance(array $input): array
    {
        $fichaId = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($err = self::fichaValida($fichaId)) {
            return $err;
        }
        $filas = IaAnalitica::rankingImagen($fichaId, isset($input['municipio']) ? (string) $input['municipio'] : null, array_map('intval', (array) ($input['preguntas_ids'] ?? [])));
        if (!$filas) {
            return ['error' => 'sin_datos', 'mensaje' => 'La encuesta no tiene preguntas de imagen de personajes con respuestas.'];
        }
        return ['personajes' => $filas, 'definiciones' => 'Reconocimiento = positiva + neutral + negativa sobre la base total. Balance = positiva menos negativa, en puntos porcentuales sobre la base total.'];
    }

    private static function handleCruzarPreguntas(array $input): array
    {
        $fichaId = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($err = self::fichaValida($fichaId)) {
            return $err;
        }
        return IaAnalitica::cruce($fichaId, (int) ($input['pregunta_filas_id'] ?? 0), (int) ($input['pregunta_columnas_id'] ?? 0), isset($input['municipio']) ? (string) $input['municipio'] : null);
    }

    private static function handleCalcularEstadistico(array $input): array
    {
        return IaAnalitica::estadistico($input);
    }

    private static function handleCompararEstudios(array $input): array
    {
        foreach (['ficha_a_id', 'ficha_b_id'] as $k) {
            if ($err = self::fichaValida((int) ($input[$k] ?? 0))) {
                return $err;
            }
        }
        return IaAnalitica::compararEstudios((int) $input['ficha_a_id'], (int) $input['ficha_b_id'], (string) ($input['pregunta'] ?? ''));
    }

    private static function informePropio(int $id, int $userId): ?array
    {
        $inf = InformeIA::cargar($id);
        if ($inf === null || (!self::$esAdminActual && (int) $inf['tbl_usuario_id'] !== $userId)) {
            return null;
        }
        return $inf;
    }

    private static function handleLeerInforme(array $input, int $userId): array
    {
        $inf = self::informePropio((int) ($input['id'] ?? 0), $userId);
        if ($inf === null) {
            return ['error' => 'no_encontrado', 'mensaje' => 'Ese informe no existe o no es del usuario.'];
        }
        return [
            'id' => (int) $inf['id'], 'titulo' => $inf['titulo'], 'subtitulo' => $inf['subtitulo'], 'ficha_tecnica_id' => $inf['ficha_tecnica_id'],
            'formato' => InformeIA::esPresentacion($inf) ? 'diapositivas' : 'documento',
            'opciones' => InformeIA::opciones($inf),
            'contenido_html' => InformeIA::esPresentacion($inf) ? $inf['fuente_dsl'] : $inf['contenido_html'],
        ];
    }

    private static function handleActualizarInforme(array $input, int $userId): array
    {
        $inf = self::informePropio((int) ($input['id'] ?? 0), $userId);
        if ($inf === null) {
            return ['error' => 'no_encontrado', 'mensaje' => 'Ese informe no existe o no es del usuario.'];
        }
        if (!InformeIA::esPresentacion($inf)) {
            return ['error' => 'formato_no_editable', 'mensaje' => 'Solo se pueden actualizar informes en formato diapositivas; genera uno nuevo con generar_informe_html.'];
        }
        $cambios = [];
        foreach (['titulo', 'subtitulo'] as $k) {
            if (isset($input[$k])) {
                $cambios[$k] = (string) $input[$k];
            }
        }
        if (isset($input['contenido_html']) && trim((string) $input['contenido_html']) !== '') {
            $cambios['contenido'] = (string) $input['contenido_html'];
        }
        $nuevas = self::opcionesInforme($input, $userId);
        if (isset($nuevas['error'])) {
            return $nuevas;
        }
        unset($nuevas['formato']);
        if ($nuevas) {
            $cambios['opciones'] = array_merge(InformeIA::opciones($inf), $nuevas);
        }
        if (!$cambios) {
            return ['error' => 'sin_cambios', 'mensaje' => 'No se indicó ningún cambio.'];
        }
        if (!InformeIA::actualizar((int) $inf['id'], $userId, $cambios)) {
            return ['error' => 'error_interno', 'mensaje' => 'No fue posible actualizar el informe.'];
        }
        return ['id' => (int) $inf['id'], 'mensaje' => 'Informe actualizado; la versión anterior quedó guardada. El PDF, el PowerPoint y el Excel ya reflejan los cambios.'];
    }

    private static function handleGenerarExcelResultados(array $input, int $userId): array
    {
        $fichaId = (int) ($input['ficha_tecnica_id'] ?? 0);
        if ($err = self::fichaValida($fichaId)) {
            return $err;
        }
        $municipio = isset($input['municipio']) && $input['municipio'] !== '' ? (string) $input['municipio'] : null;
        $preguntas = IaAnalitica::preguntasFicha($fichaId, $municipio);
        $intentos = IaAnalitica::intentosCompletos($fichaId, $municipio);
        if ($intentos <= 0) {
            return ['error' => 'sin_datos', 'mensaje' => 'La encuesta no tiene encuestas completas.'];
        }
        $dsl = '';
        $n = 0;
        foreach ($preguntas as $p) {
            if ($p['respuestas'] <= 0 || count($p['opciones']) < 2) {
                continue;
            }
            $n++;
            $multiple = $p['respuestas'] > $intentos * 1.02;
            $base = $multiple ? $intentos : $p['respuestas'];
            $datos = '';
            foreach ($p['opciones'] as $o) {
                $datos .= '<dato etiqueta="' . htmlspecialchars($o['texto'], ENT_QUOTES, 'UTF-8') . '" valor="' . round($o['n'] / $base * 100, 2) . '" n="' . $o['n'] . '"/>';
            }
            $dsl .= '<pagina titulo="' . htmlspecialchars('P' . $n . '. ' . mb_substr((string) ($p['capitulo'] ?: 'Resultados'), 0, 40), ENT_QUOTES, 'UTF-8') . '" pregunta="' . htmlspecialchars($p['texto'], ENT_QUOTES, 'UTF-8') . '">'
                . '<grafico tipo="barras"' . ($multiple ? ' multiple="si"' : '') . '>' . $datos . '</grafico>'
                . ($multiple ? '<nota>*Los datos corresponden a una respuesta con opción múltiple</nota>' : '') . '</pagina>';
        }
        if ($dsl === '') {
            return ['error' => 'sin_datos', 'mensaje' => 'No hay preguntas de selección con respuestas.'];
        }
        $titulo = trim((string) ($input['titulo'] ?? '')) !== '' ? trim((string) $input['titulo']) : 'Resultados de la encuesta';
        $id = InformeIA::crear($userId, $titulo, $dsl, $fichaId, '', ['formato' => 'diapositivas']);
        return ['id' => $id, 'preguntas_incluidas' => $n, 'encuestas_completas' => $intentos, 'mensaje' => 'Se creó el informe de datos (id ' . $id . '). El Excel con una hoja y un gráfico por pregunta, y el PDF, están disponibles en Informes IA.'];
    }

    private static function agregadoEncuesta(string $metodo, array $input): array
    {
        require_once __DIR__ . '/../ResultadosCuestionarioAgregado.php';
        $ficha = (int)($input['ficha_tecnica_id'] ?? 0);
        if ($ficha <= 0) {
            return ['error' => 'parametro_requerido', 'mensaje' => 'ficha_tecnica_id es obligatorio.'];
        }
        $rqst = ['ficha_id' => $ficha, 'estado' => 'completo'];
        if (!empty($input['municipio'])) {
            $rqst['municipio'] = (string)$input['municipio'];
        }
        $res = ResultadosCuestionarioAgregado::$metodo($rqst);
        if (empty($res['output']['valid'])) {
            return ['error' => 'sin_datos', 'mensaje' => 'No fue posible calcular los resultados de la encuesta.'];
        }
        return $res['output']['response'];
    }

    private static function handleConsultarResultadosEncuesta(array $input, int $userId): array
    {
        $r = self::agregadoEncuesta('resumen', $input);
        if (isset($r['error'])) {
            return $r;
        }
        foreach ($r['avance_municipios'] as &$m) {
            if (count($m['zonas']) > 12) {
                $m['zonas'] = array_slice($m['zonas'], 0, 12);
                $m['zonas_recortadas'] = true;
            }
        }
        unset($m);
        return $r;
    }

    private static function handleConsultarResultadosTablasCandidatos(array $input, int $userId): array
    {
        $r = self::agregadoEncuesta('grillas', $input);
        if (isset($r['error'])) {
            return $r;
        }
        $detalle = !empty($input['detalle']);
        foreach ($r['grillas'] as &$g) {
            foreach ($g['tablas'] as &$t) {
                foreach ($t['filas'] as &$f) {
                    if (!$detalle) {
                        unset($f['columnas']);
                    }
                }
                unset($f);
            }
            unset($t);
        }
        unset($g);
        return $r;
    }

    private static function handleConsultarVotoEncuesta(array $input, int $userId): array
    {
        return self::agregadoEncuesta('listas', $input);
    }

    private static function handleConsultarBaseDeDatos(array $input): array
    {
        $sql = (string) ($input['sql'] ?? '');
        return IaDbConsulta::ejecutar($sql);
    }

    private const DIMENSIONES_VOTANTES = ['ideologia', 'rango_edad', 'nivel_ingresos', 'genero', 'nivel_educacion'];

    private static function handleConsultarVotantes(array $input): array
    {
        $dimension = (string) ($input['dimension'] ?? 'todas');
        $dimensiones = $dimension === 'todas' || !in_array($dimension, self::DIMENSIONES_VOTANTES, true)
            ? self::DIMENSIONES_VOTANTES
            : [$dimension];

        $db = new DbConection();
        $pdo = $db->openConect();
        $resultado = [];
        foreach ($dimensiones as $col) {
            $q = "SELECT {$col} AS valor, COUNT(*) AS total FROM " . $db->getTable('tbl_votantes') . "
                  WHERE estado = 'activo' GROUP BY {$col} ORDER BY total DESC";
            $stmt = $pdo->query($q);
            $resultado[$col] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $db->closeConect();

        return $resultado;
    }

    private static function filtrosCertificacionDesdeInput(array $input): array
    {
        $rqst = [];
        foreach ([
            'estado_revision',
            'origen_tipo',
            'revision_metodo',
            'fecha_desde',
            'fecha_hasta',
            'con_audio',
        ] as $k) {
            if (isset($input[$k]) && $input[$k] !== '' && $input[$k] !== null) {
                $rqst[$k] = (string) $input[$k];
            }
        }
        foreach (['tbl_sondeo_id', 'tbl_ficha_tecnica_encuesta_id', 'tbl_usuario_id'] as $k) {
            if (isset($input[$k]) && (int) $input[$k] > 0) {
                $rqst[$k] = (int) $input[$k];
            }
        }
        return $rqst;
    }

    private static function handleConsultarCertificacionResumen(array $input): array
    {
        $rqst = self::filtrosCertificacionDesdeInput($input);
        $res = CertificacionEncuestador::getDashboardKpis($rqst);
        if (!($res['output']['valid'] ?? false)) {
            return ['error' => 'consulta_fallida', 'mensaje' => 'No fue posible obtener el resumen de validación de encuestadores.'];
        }
        $data = $res['output']['response'] ?? [];
        $estados = $data['por_estado'] ?? [];
        $metodos = $data['en_revision_por_metodo'] ?? [];
        $bien = (int)($estados['bien'] ?? 0);
        $mal = (int)($estados['mal'] ?? 0);
        $concluidas = $bien + $mal;
        $lectura = [
            'total' => (int)($data['total'] ?? 0),
            'validas' => $bien,
            'rechazadas' => $mal,
            'pendientes_de_revision_humana' => (int)($estados['en_revision'] ?? 0),
            'de_los_cuales_por_fallo_de_la_ia' => (int)($metodos['ninguno'] ?? 0),
            'de_los_cuales_por_baja_confianza_de_la_ia' => (int)($metodos['ia'] ?? 0),
            'sin_revisar' => (int)($estados['pendiente'] ?? 0),
            'anuladas' => (int)($estados['anulada'] ?? 0),
            'porcentaje_validas_sobre_concluidas' => $concluidas > 0 ? round($bien / $concluidas * 100, 1) : null,
            'concluidas_valida_o_rechazada' => $concluidas,
        ];
        unset($data['pct_bien_sobre_revisadas'], $data['revisadas']);
        return [
            'lectura_como_en_pantalla' => $lectura,
            'resumen' => $data,
            'glosario' => [
                'pendiente' => 'Aún sin revisión de calidad',
                'bien' => 'Válida / aprobada',
                'mal' => 'Rechazada / no válida',
                'en_revision' => 'Pendiente de revisión humana: la validación automática no pudo concluir (confianza baja o fallo de la IA) y una persona debe decidir',
                'anulada' => 'Anulada',
                'cuestionario' => 'Encuesta (ficha técnica)',
                'sondeo' => 'Sondeo',
            ],
        ];
    }

    private static function handleConsultarCertificacionEncuestador(array $input): array
    {
        $rqst = self::filtrosCertificacionDesdeInput($input);
        if (!empty($input['encuestador_nombre'])) {
            $rqst['encuestador_nombre'] = trim((string) $input['encuestador_nombre']);
        }
        if (!empty($input['nombre'])) {
            $rqst['nombre'] = trim((string) $input['nombre']);
        }
        if (isset($input['limite'])) {
            $rqst['limite'] = (int) $input['limite'];
        }

        $tieneBusqueda = !empty($rqst['tbl_usuario_id'])
            || !empty($rqst['encuestador_nombre'])
            || !empty($rqst['nombre']);
        if (!$tieneBusqueda) {
            // Sin nombre/id: top encuestadores del universo filtrado
            $rqst['limite'] = $rqst['limite'] ?? 15;
        }

        $res = CertificacionEncuestador::getResumenPorEncuestador($rqst);
        if (!($res['output']['valid'] ?? false)) {
            return ['error' => 'consulta_fallida', 'mensaje' => 'No fue posible consultar el desempeño por encuestador.'];
        }
        return $res['output']['response'] ?? [];
    }

    private static function handleConsultarCertificaciones(array $input): array
    {
        $id = (int) ($input['id'] ?? 0);
        if ($id > 0) {
            $det = CertificacionEncuestador::getDetalle(['id' => $id]);
            if (!($det['output']['valid'] ?? false)) {
                return ['error' => 'no_encontrada', 'mensaje' => 'No se encontró la validación indicada.'];
            }
            $cert = $det['output']['response'] ?? [];
            $cert = self::sanitizarCertificacionParaIa($cert);

            $hist = CertificacionEncuestador::getHistorial(['id' => $id]);
            $historial = ($hist['output']['valid'] ?? false) ? ($hist['output']['response'] ?? []) : [];
            $historial = array_map(static function ($h) {
                unset($h['payload_json'], $h['transcripcion_completa'], $h['ia_json']);
                if (isset($h['transcripcion']) && is_string($h['transcripcion']) && mb_strlen($h['transcripcion']) > 1200) {
                    $h['transcripcion'] = mb_substr($h['transcripcion'], 0, 1200) . '…';
                }
                if (isset($h['comentario']) && is_string($h['comentario']) && mb_strlen($h['comentario']) > 800) {
                    $h['comentario'] = mb_substr($h['comentario'], 0, 800) . '…';
                }
                return $h;
            }, $historial);

            return [
                'certificacion' => $cert,
                'historial' => $historial,
                'ediciones_respuestas' => self::edicionesDeCertificacion($id),
            ];
        }

        $rqst = self::filtrosCertificacionDesdeInput($input);
        $res = CertificacionEncuestador::getAll($rqst);
        if (!($res['output']['valid'] ?? false)) {
            return ['error' => 'consulta_fallida', 'mensaje' => 'No fue posible listar certificaciones.'];
        }
        $rows = $res['output']['response'] ?? [];
        $limite = isset($input['limite']) ? max(1, min(80, (int) $input['limite'])) : 40;
        $total = count($rows);
        $slice = array_slice($rows, 0, $limite);
        $listado = [];
        foreach ($slice as $r) {
            $listado[] = [
                'id' => (int) ($r['id'] ?? 0),
                'fecha' => $r['fecha_certificacion'] ?? null,
                'estado' => $r['estado_revision'] ?? null,
                'metodo' => $r['revision_metodo'] ?? null,
                'encuestador' => trim(($r['encuestador_nombre'] ?? '') . ' ' . ($r['encuestador_apellido'] ?? '')),
                'encuestado' => $r['votante_nombre'] ?? null,
                'origen' => $r['origen_tipo'] ?? null,
                'sondeo' => $r['sondeo_nombre'] ?? null,
                'encuesta' => $r['cuestionario_nombre'] ?? null,
                'audio_segundos' => isset($r['audio_duracion_segundos']) ? (int) $r['audio_duracion_segundos'] : 0,
                'tiene_gps' => !empty($r['latitud']) && !empty($r['longitud']),
            ];
        }

        return [
            'total_coincidencias' => $total,
            'devueltas' => count($listado),
            'truncado' => $total > $limite,
            'listado' => $listado,
        ];
    }

    private static function edicionesDeCertificacion(int $id): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $st = $pdo->prepare("SELECT l.dtcreate AS fecha, l.motivo, l.estado_antes, l.estado_despues,
                    CONCAT_WS(' ', u.nombre, u.apellido) AS editor
                FROM " . $db->getTable('tbl_certificacion_respuestas_log') . " l
                LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON u.id = l.tbl_usuario_id
                WHERE l.tbl_certificacion_id = :id ORDER BY l.dtcreate DESC, l.id DESC LIMIT 10");
            $st->execute([':id' => $id]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        } finally {
            $db->closeConect();
        }
    }

    private static function sanitizarCertificacionParaIa(array $cert): array
    {
        unset($cert['detalle_intento'], $cert['revision_ia_json']);
        if (isset($cert['revision_transcripcion']) && is_string($cert['revision_transcripcion'])
            && mb_strlen($cert['revision_transcripcion']) > 1200) {
            $cert['revision_transcripcion'] = mb_substr($cert['revision_transcripcion'], 0, 1200) . '…';
        }
        unset(
            $cert['audio_base64'],
            $cert['audio_blob'],
            $cert['audio_binario'],
            $cert['audio_data']
        );
        if (isset($cert['revision_comentario']) && is_string($cert['revision_comentario'])
            && mb_strlen($cert['revision_comentario']) > 1200) {
            $cert['revision_comentario'] = mb_substr($cert['revision_comentario'], 0, 1200) . '…';
        }
        if (isset($cert['ia_comentario']) && is_string($cert['ia_comentario'])
            && mb_strlen($cert['ia_comentario']) > 1200) {
            $cert['ia_comentario'] = mb_substr($cert['ia_comentario'], 0, 1200) . '…';
        }
        // Respuestas Q&A: limitar tamaño
        foreach (['respuestas_sondeo', 'respuestas_cuestionario'] as $k) {
            if (!isset($cert[$k]) || !is_array($cert[$k])) {
                continue;
            }
            if (count($cert[$k]) > 40) {
                $cert[$k] = array_slice($cert[$k], 0, 40);
                $cert[$k . '_truncado'] = true;
            }
        }
        return $cert;
    }
}
