# Guía: carga de preguntas por Excel

Desde la pantalla de preguntas de una encuesta (`cuestionario_preguntas.php?ficha_id=…`).

## Botones
- **Plantilla Excel**: descarga `plantilla_preguntas.xlsx` (instrucciones, hojas «Preguntas» y «Opciones», un ejemplo y listas desplegables).
- **Subir Excel**: abre la carga con revisión previa.
- **Exportar a Excel**: saca las preguntas de esta encuesta en el mismo formato (para editarlas o llevarlas a otra encuesta).

## Pasos
1. Descargue la plantilla.
2. Hoja **Preguntas**: una fila por pregunta. Azules = obligatorias (Código, Pregunta, Tipo); grises = opcionales.
3. Hoja **Opciones**: una fila por opción, repitiendo el **Código** de su pregunta. Sin límite de opciones.
4. «Subir Excel» → elija el archivo → «Revisar archivo». Verá cada fila con su estado (Nueva, Ya existe, Omitida, Error) y sus avisos.
5. Si no hay errores, «Importar preguntas». Las preguntas quedan **habilitadas** al final de la encuesta, en el orden del archivo.

## Reglas
| Tipo (lista desplegable) | Opciones |
|---|---|
| Sí / No | Exactamente 2. Vacías: se crean «Sí» y «No». |
| Escoge una sola opción | Mínimo 2. |
| Escoge varias opciones | Mínimo 2. «Máx. selecciones» limita cuántas se marcan (vacío = todas). |
| Escala de calificación | Vacías: se crea la escala 1 a 5. |
| Nivel de satisfacción / intensidad | Mínimo 2. |
| Tabla de candidatos | No lleva opciones. «Tabla de candidatos» = nombre exacto de una tabla existente (hoja «Listas»). Vacío: se crea sin tabla y con aviso. |
| Respuesta abierta | No lleva opciones. |

- **Capítulo / Enunciado de la sección** vacíos repiten los de la fila anterior. Un capítulo nuevo reinicia el enunciado.
- **Cuenta como**: ninguno, sí, no, favorable, desfavorable. **Acción**: continuar o terminar (si termina, escriba el mensaje; si no, se usa «Gracias por su tiempo.»).
- **Duplicadas**: una pregunta con el mismo numeral y texto de otra ya existente (o repetida en el archivo) se informa y **no se agrega**.
- **Todo o nada**: con un solo error no se guarda ninguna pregunta del archivo.
- Máximo 300 preguntas y 5 MB por archivo.

## Qué se configura después, a mano
Visibilidad condicionada, saltos entre preguntas, voto escalonado (se omite al importar), exclusiones, variables de cruce y equivalencias del votante.

## Respaldo CSV
Si el servidor no tuviera la extensión ZIP, el botón descarga una plantilla CSV (separador `;`, una fila por opción; las filas de opciones adicionales dejan vacías las columnas de la pregunta). La carga acepta `.xlsx` y `.csv`.

## Para producción
Ejecutar `migrar_produccion.php` (aplica `fase-l-001-auditoria-importacion`, que crea la tabla de auditoría si falta). No requiere Node.
