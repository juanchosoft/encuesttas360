# Guía de Importación de Preguntas vía CSV

Esta guía explica cómo estructurar correctamente el archivo CSV para importar preguntas al sistema de encuestas.

## Estructura General del CSV

El archivo CSV debe contener una fila de encabezado con los nombres de las columnas, seguida de las filas de datos con las preguntas a importar.

### Columnas Requeridas

| Columna | Tipo | Obligatorio | Descripción |
|---------|------|-------------|-------------|
| `tbl_ficha_tecnica_encuesta_id` | Número | **Sí** | ID de la Ficha Técnica de la Encuesta a la que pertenece la pregunta |
| `texto_pregunta` | Texto | **Sí** | El texto completo de la pregunta |
| `tipo_pregunta` | Texto | No | Tipo de pregunta (ver tipos permitidos abajo) |
| `orden` | Número | No | Orden de visualización de la pregunta (default: secuencial) |
| `opcion_1` | Texto | No* | Primera opción de respuesta |
| `opcion_2` | Texto | No* | Segunda opción de respuesta |
| `opcion_3` | Texto | No* | Tercera opción de respuesta |
| `opcion_4` | Texto | No* | Cuarta opción de respuesta |
| `opcion_5` | Texto | No* | Quinta opción de respuesta |
| `tbl_usuario_id` | Número | No | ID del usuario que crea la pregunta (default: 1) |

**Nota:** Las opciones de respuesta son obligatorias según el tipo de pregunta seleccionado.

---

## Tipos de Pregunta Permitidos

### 1. Dicotomica
Pregunta de Sí/No o dos opciones mutuamente excluyentes.

**Opciones requeridas:** 2 (generalmente "Sí" y "No")

**Ejemplo:**
```csv
tbl_ficha_tecnica_encuesta_id,texto_pregunta,tipo_pregunta,orden,opcion_1,opcion_2,opcion_3,opcion_4,opcion_5,tbl_usuario_id
1,¿Votaría por el candidato en las próximas elecciones?,Dicotomica,1,Sí,No,,,,2
```

---

### 2. Preguntas_Ordinales
Pregunta con opciones que tienen un orden jerárquico o ranking (primero, segundo, tercero, etc.).

**Opciones requeridas:** 2 o más

**Ejemplo:**
```csv
tbl_ficha_tecnica_encuesta_id,texto_pregunta,tipo_pregunta,orden,opcion_1,opcion_2,opcion_3,opcion_4,opcion_5,tbl_usuario_id
1,¿Cuál es su nivel de educación?,Preguntas_Ordinales,2,Primaria,Secundaria,Universitaria,Postgrado,,2
```

---

### 3. Preguntas_Cardinales
Pregunta con escala numérica o de intensidad (muy satisfecho, satisfecho, neutral, etc.).

**Opciones requeridas:** 3 o más (generalmente 5)

**Ejemplo:**
```csv
tbl_ficha_tecnica_encuesta_id,texto_pregunta,tipo_pregunta,orden,opcion_1,opcion_2,opcion_3,opcion_4,opcion_5,tbl_usuario_id
1,¿Qué tan satisfecho está con el gobierno actual?,Preguntas_Cardinales,3,Muy Satisfecho,Satisfecho,Neutral,Insatisfecho,Muy Insatisfecho,2
```

---

### 4. Seleccion_Multiple_unica_respuesta
Pregunta de selección múltiple donde solo se puede escoger una opción.

**Opciones requeridas:** 2 o más

**Ejemplo:**
```csv
tbl_ficha_tecnica_encuesta_id,texto_pregunta,tipo_pregunta,orden,opcion_1,opcion_2,opcion_3,opcion_4,opcion_5,tbl_usuario_id
1,¿Cómo califica la imagen del candidato?,Seleccion_Multiple_unica_respuesta,4,Excelente,Buena,Regular,Mala,Muy Mala,2
```

---

### 5. Seleccion_Multiple_multiple_respuesta
Pregunta de selección múltiple donde se pueden escoger varias opciones.

**Opciones requeridas:** 2 o más

**Nota:** El sistema establecerá automáticamente `limite_respuesta_multiple = 1` por defecto. Este campo se puede modificar luego desde el formulario web.

**Ejemplo:**
```csv
tbl_ficha_tecnica_encuesta_id,texto_pregunta,tipo_pregunta,orden,opcion_1,opcion_2,opcion_3,opcion_4,opcion_5,tbl_usuario_id
1,¿Cuáles temas son prioritarios para usted?,Seleccion_Multiple_multiple_respuesta,5,Salud,Educación,Seguridad,Empleo,Infraestructura,2
```

---

## Campos Automáticos

Los siguientes campos se asignan automáticamente durante la importación:

- **`dtcreate`**: Fecha y hora de creación (se establece automáticamente con NOW())
- **`limite_respuesta_multiple`**: Se establece en 1 por defecto
- **`habilitado`**: Se establece en "si" por defecto (la pregunta estará activa)
- **`tbl_usuario_id`**: Si no se proporciona, se usa el ID del usuario en sesión o 1 por defecto

---

## Reglas y Validaciones

### ✅ Reglas Obligatorias

1. **El archivo debe tener extensión `.csv`**
2. **Debe incluir una fila de encabezado** con los nombres exactos de las columnas
3. **Todas las filas deben tener el mismo número de columnas** que el encabezado
4. **`tbl_ficha_tecnica_encuesta_id` debe existir** en la tabla de fichas técnicas
5. **`texto_pregunta` no puede estar vacío**
6. **Debe haber al menos una opción de respuesta** (opcion_1)

### ⚠️ Consideraciones Importantes

- Las celdas vacías deben dejarse sin contenido entre las comas
- No usar comillas dobles dentro del texto a menos que sea necesario (el CSV las usa como delimitador)
- Si el texto contiene comas, debe estar entre comillas dobles: `"Pregunta con, coma"`
- Los saltos de línea dentro de una celda NO están soportados
- El orden de las columnas debe respetarse exactamente como se muestra
- Las opciones se numeran del 1 al 5 (máximo 5 opciones por pregunta)
- Si no necesita las 5 opciones, deje las columnas vacías pero mantenga las comas separadoras

---

## Ejemplo Completo de CSV

### Contenido del archivo `preguntas.csv`:

```csv
tbl_ficha_tecnica_encuesta_id,texto_pregunta,tipo_pregunta,orden,opcion_1,opcion_2,opcion_3,opcion_4,opcion_5,tbl_usuario_id
1,¿Cómo es la imagen que tiene de Alexander para su candidatura?,Seleccion_Multiple_unica_respuesta,1,Excelente,Buena,Regular,Mala,Muy Mala,2
1,¿Votaría por Alexander en las próximas elecciones?,Dicotomica,2,Sí,No,,,,2
1,¿Qué tan satisfecho está con el gobierno actual?,Preguntas_Cardinales,3,Muy Satisfecho,Satisfecho,Neutral,Insatisfecho,Muy Insatisfecho,2
1,¿Cuál es su nivel de educación?,Preguntas_Ordinales,4,Primaria,Secundaria,Universitaria,Postgrado,,2
1,¿Cuáles son los temas prioritarios para usted?,Seleccion_Multiple_multiple_respuesta,5,Salud,Educación,Seguridad,Empleo,Infraestructura,2
```

---

## Proceso de Importación

### Pasos para Importar:

1. **Preparar el archivo CSV** siguiendo la estructura de esta guía
2. **Verificar** que el ID de la ficha técnica existe en el sistema
3. **Guardar el archivo** con codificación UTF-8 (para caracteres especiales como tildes)
4. En el módulo de **Preguntas**, hacer clic en **"Subir Preguntas"**
5. **Cargar el archivo CSV** desde el modal
6. Hacer clic en **"Cargar Archivo"**
7. Esperar la confirmación de importación

### Mensajes Esperados:

**✅ Éxito:**
```
Importación completada. N preguntas importadas.
```

**❌ Error:**
El sistema mostrará un mensaje detallado indicando:
- En qué línea ocurrió el error
- Qué tipo de error (falta campo obligatorio, formato incorrecto, etc.)
- Cantidad de preguntas importadas antes del error

---

## Solución de Problemas Comunes

### Error: "Field 'limite_respuesta_multiple' doesn't have a default value"
**Solución:** Este error ya está corregido. Asegúrese de usar la versión actualizada del sistema.

### Error: "El número de columnas no coincide con la cabecera"
**Causa:** Alguna fila tiene más o menos comas que el encabezado.
**Solución:** Verifique que todas las filas tengan exactamente 10 columnas (incluyendo las vacías).

### Error: "Faltan datos obligatorios para la pregunta"
**Causa:** Falta `tbl_ficha_tecnica_encuesta_id` o `texto_pregunta`.
**Solución:** Complete estos campos obligatorios en todas las filas.

### Error: "El archivo CSV no existe en la ruta especificada"
**Causa:** El archivo no se cargó correctamente.
**Solución:** Vuelva a cargar el archivo antes de hacer clic en "Cargar Archivo".

### Tildes y caracteres especiales se ven mal
**Causa:** El archivo no está guardado en UTF-8.
**Solución:**
- En Excel: "Guardar como" → CSV UTF-8 (delimitado por comas)
- En Notepad++: Encoding → UTF-8
- En LibreOffice: Guardar con conjunto de caracteres Unicode (UTF-8)

---

## Auditoría de Importaciones

El sistema registra automáticamente cada importación en la tabla `tbl_auditoria_preguntas` con la siguiente información:

- Usuario que realizó la importación
- Nombre del archivo
- Fecha y hora
- Cantidad de preguntas importadas
- Cantidad de opciones importadas
- Estado (completado, error, parcial)
- Dirección IP
- Navegador utilizado
- Mensaje de error (si aplica)

---

## Plantilla Descargable

Puede descargar la plantilla CSV de ejemplo desde el módulo de Preguntas:

**Botón:** "Descargar Plantilla (Preguntas)"

Esta plantilla incluye:
- Los encabezados correctos
- 5 ejemplos de diferentes tipos de preguntas
- La estructura exacta que el sistema espera

---

## Notas Finales

- **Máximo 5 opciones por pregunta** en el CSV. Si necesita más, use el formulario web.
- **No hay límite** en la cantidad de preguntas que puede importar en un solo archivo.
- Las preguntas importadas quedan **habilitadas por defecto**.
- Puede **editar** las preguntas importadas desde el formulario web posteriormente.
- El campo **`limite_respuesta_multiple`** solo es relevante para preguntas de tipo "Seleccion_Multiple_multiple_respuesta".

---

## Contacto y Soporte

Si tiene problemas con la importación, verifique:
1. El formato del CSV según esta guía
2. Los mensajes de error específicos
3. Los logs de auditoría en la base de datos

Para soporte adicional, contacte al administrador del sistema con:
- El archivo CSV que intentó importar
- El mensaje de error completo
- La fecha y hora del intento
