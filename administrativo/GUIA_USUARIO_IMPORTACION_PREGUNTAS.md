# Guía para Importar Preguntas desde Excel/CSV

**Versión para Usuario Final**

Esta guía le ayudará a cargar preguntas de manera masiva al sistema usando un archivo de Excel (CSV).

---

## ¿Cuándo usar esta opción?

Use la importación masiva cuando necesite:
- Agregar **muchas preguntas** a la vez (10, 20, 50 o más)
- Tener las preguntas ya preparadas en Excel
- Ahorrar tiempo en lugar de escribir pregunta por pregunta

---

## Paso 1: Descargar la Plantilla

1. Entre al módulo **"Cuestionario De Preguntas"**
2. Busque el botón verde que dice **"Descargar Plantilla (Preguntas)"**
3. Haga clic para descargar el archivo `preguntas.csv`
4. Ábralo con Excel o Google Sheets

---

## Paso 2: Entender la Plantilla

Al abrir el archivo verá estas columnas:

| Columna | ¿Qué va aquí? | ¿Es obligatorio? |
|---------|---------------|------------------|
| **tbl_ficha_tecnica_encuesta_id** | El número ID de la encuesta | SÍ |
| **texto_pregunta** | La pregunta completa | SÍ |
| **tipo_pregunta** | El tipo de pregunta (ver más abajo) | NO |
| **orden** | En qué orden aparece (1, 2, 3...) | NO |
| **opcion_1** | Primera opción de respuesta | SÍ |
| **opcion_2** | Segunda opción de respuesta | SÍ |
| **opcion_3** | Tercera opción de respuesta | NO |
| **opcion_4** | Cuarta opción de respuesta | NO |
| **opcion_5** | Quinta opción de respuesta | NO |
| **tbl_usuario_id** | Su número de usuario | NO |

---

## Paso 3: ¿Cómo llenar cada columna?

### 📌 tbl_ficha_tecnica_encuesta_id

**¿Qué es?** El número de identificación de la Ficha Técnica de la Encuesta a la que pertenecen estas preguntas.

**¿Dónde lo encuentro?**
1. Vaya al módulo "Ficha Técnica Encuesta"
2. Busque la encuesta que desea
3. El número ID aparece en la primera columna de la tabla

**Ejemplo:** Si su encuesta tiene ID 1, ponga **1** en todas las filas

---

### 📌 texto_pregunta

**¿Qué es?** La pregunta completa que verá el encuestador.

**Ejemplos correctos:**
- `¿Votaría por este candidato en las próximas elecciones?`
- `¿Cómo califica la gestión actual del alcalde?`
- `¿Cuál es su nivel de satisfacción con los servicios públicos?`

**Recomendaciones:**
- Sea claro y específico
- No use saltos de línea (enter) dentro de la pregunta
- Si tiene comas, no se preocupe, el sistema las maneja

---

### 📌 tipo_pregunta

**¿Qué es?** El tipo de respuesta que espera.

**Tipos disponibles:**

**1. Dicotomica** - Pregunta de Sí/No (dos opciones)
```
Ejemplo: ¿Votaría por este candidato?
Opciones: Sí | No
```

**2. Seleccion_Multiple_unica_respuesta** - Varias opciones, se elige solo una
```
Ejemplo: ¿Cómo califica la gestión?
Opciones: Excelente | Buena | Regular | Mala | Muy Mala
```

**3. Seleccion_Multiple_multiple_respuesta** - Varias opciones, se pueden elegir varias
```
Ejemplo: ¿Qué temas son prioritarios?
Opciones: Salud | Educación | Seguridad | Empleo | Vivienda
```

**4. Preguntas_Cardinales** - Escala de intensidad
```
Ejemplo: ¿Qué tan satisfecho está?
Opciones: Muy Satisfecho | Satisfecho | Neutral | Insatisfecho | Muy Insatisfecho
```

**5. Preguntas_Ordinales** - Opciones con orden jerárquico
```
Ejemplo: ¿Cuál es su nivel de estudios?
Opciones: Primaria | Secundaria | Universitaria | Postgrado
```

**Si no sabe cuál elegir:** Deje esta columna vacía y el sistema usará "Seleccion_Multiple_unica_respuesta"

---

### 📌 orden

**¿Qué es?** El número que indica en qué posición aparece la pregunta.

**Ejemplo:**
- Primera pregunta: ponga **1**
- Segunda pregunta: ponga **2**
- Tercera pregunta: ponga **3**

**Consejo:** Si deja esta columna vacía, el sistema las ordenará automáticamente.

---

### 📌 opcion_1, opcion_2, opcion_3, opcion_4, opcion_5

**¿Qué son?** Las opciones de respuesta que verá el encuestador.

**Reglas importantes:**
- **DEBE tener al menos 2 opciones** (opcion_1 y opcion_2)
- Puede tener hasta 5 opciones máximo
- Si no necesita las 5, deje las últimas vacías

**Ejemplos:**

**Para pregunta Sí/No:**
```
opcion_1: Sí
opcion_2: No
opcion_3: (vacío)
opcion_4: (vacío)
opcion_5: (vacío)
```

**Para calificación:**
```
opcion_1: Excelente
opcion_2: Buena
opcion_3: Regular
opcion_4: Mala
opcion_5: Muy Mala
```

**Para pregunta de satisfacción:**
```
opcion_1: Muy Satisfecho
opcion_2: Satisfecho
opcion_3: Neutral
opcion_4: Insatisfecho
opcion_5: Muy Insatisfecho
```

---

### 📌 tbl_usuario_id

**¿Qué es?** Su número de usuario en el sistema.

**¿Debo llenarlo?** NO es necesario. Si lo deja vacío, el sistema usará su usuario automáticamente.

---

## Paso 4: Ejemplo Completo

Así debería verse su archivo de Excel/CSV:

| tbl_ficha_tecnica_encuesta_id | texto_pregunta | tipo_pregunta | orden | opcion_1 | opcion_2 | opcion_3 | opcion_4 | opcion_5 | tbl_usuario_id |
|------|----------------|---------------|-------|----------|----------|----------|----------|----------|---------|
| 1 | ¿Votaría por este candidato? | Dicotomica | 1 | Sí | No |  |  |  | 2 |
| 1 | ¿Cómo califica su gestión? | Seleccion_Multiple_unica_respuesta | 2 | Excelente | Buena | Regular | Mala | Muy Mala | 2 |
| 1 | ¿Qué tan satisfecho está con los servicios? | Preguntas_Cardinales | 3 | Muy Satisfecho | Satisfecho | Neutral | Insatisfecho | Muy Insatisfecho | 2 |

---

## Paso 5: Guardar el Archivo

**MUY IMPORTANTE:** Debe guardar el archivo en formato CSV.

### Si usa Microsoft Excel:
1. Haga clic en **"Archivo"** → **"Guardar como"**
2. En **"Tipo"** seleccione: **"CSV UTF-8 (delimitado por comas)"**
3. Guarde el archivo

### Si usa Google Sheets:
1. Haga clic en **"Archivo"** → **"Descargar"**
2. Seleccione **"Valores separados por comas (.csv)"**

### Si usa LibreOffice Calc:
1. Haga clic en **"Archivo"** → **"Guardar como"**
2. En **"Tipo de archivo"** seleccione: **"Texto CSV"**
3. Asegúrese de seleccionar **"Unicode (UTF-8)"** en el conjunto de caracteres

---

## Paso 6: Subir el Archivo al Sistema

1. Entre al módulo **"Cuestionario De Preguntas"**
2. Haga clic en el botón verde **"Subir Preguntas"**
3. Se abrirá una ventana
4. Haga clic en **"Seleccionar archivo"** y busque su archivo CSV
5. Una vez cargado, haga clic en **"Cargar Archivo"**
6. Espere unos segundos...
7. El sistema le mostrará un mensaje de confirmación

**Mensaje de éxito:**
```
Importación completada. 15 preguntas importadas.
```

---

## Errores Comunes y Soluciones

### ❌ "No se ha seleccionado ningún archivo"
**Problema:** No cargó el archivo antes de hacer clic en "Cargar Archivo"
**Solución:** Primero seleccione el archivo y espere a que se cargue, luego haga clic en "Cargar Archivo"

---

### ❌ "Faltan datos obligatorios para la pregunta"
**Problema:** Le falta el ID de la encuesta o el texto de alguna pregunta
**Solución:** Revise que TODAS las filas tengan:
- El número de ID de la encuesta (columna A)
- El texto de la pregunta (columna B)

---

### ❌ "El número de columnas no coincide"
**Problema:** Alguna fila tiene más o menos columnas
**Solución:**
1. Cuente las comas en cada fila
2. Todas deben tener la misma cantidad
3. Si una opción está vacía, déjela vacía pero mantenga las comas

---

### ❌ Las tildes se ven raras (�, Ã±, etc.)
**Problema:** El archivo no se guardó en formato UTF-8
**Solución:**
1. Abra el archivo nuevamente
2. Guárdelo como "CSV UTF-8" (no solo "CSV")
3. Vuelva a intentar la importación

---

### ❌ "Se requiere al menos una opción de respuesta"
**Problema:** No puso ninguna opción en opcion_1 y opcion_2
**Solución:** Todas las preguntas deben tener al menos 2 opciones de respuesta

---

## Consejos Útiles

### ✅ Antes de subir el archivo:
- Revise que todas las preguntas estén completas
- Verifique que el ID de la encuesta sea correcto
- Asegúrese de que todas las preguntas tengan al menos 2 opciones
- Guarde el archivo en formato CSV UTF-8

### ✅ Durante el llenado:
- Use Excel o Google Sheets, son más fáciles que editar el CSV directo
- Copie y pegue las opciones comunes para ser más rápido
- Numere el orden de forma secuencial (1, 2, 3, 4...)

### ✅ Después de importar:
- Verifique en la tabla que todas las preguntas aparezcan
- Puede editar cualquier pregunta haciendo clic en el botón de editar
- Si algo salió mal, puede eliminar y volver a importar

---

## ¿Necesita Ayuda?

Si después de seguir esta guía aún tiene problemas:

1. **Revise el mensaje de error** que aparece en pantalla
2. **Verifique su archivo CSV** siguiendo los ejemplos de esta guía
3. **Intente con pocas preguntas primero** (2 o 3) para probar
4. **Contacte al administrador** si el problema persiste

**Recuerde:** Puede descargar la plantilla de ejemplo en cualquier momento desde el botón "Descargar Plantilla (Preguntas)"

---

## Resumen Rápido

1. ✅ Descargue la plantilla
2. ✅ Llene las columnas (mínimo: ID encuesta, pregunta, 2 opciones)
3. ✅ Guarde como CSV UTF-8
4. ✅ Suba al sistema desde "Subir Preguntas"
5. ✅ Verifique que se importaron correctamente

**¡Listo! Ya puede usar las preguntas importadas en sus encuestas.**
