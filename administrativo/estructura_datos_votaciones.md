# Estructura de Datos del Sistema de Votaciones

## Resumen

El sistema de estudio de votaciones ahora guarda **TODA** la información que el usuario selecciona, dividida en dos componentes principales:

### 1. Respuestas Principales (tabla: `tbl_grilla_candidato_respuestas`)

Guarda las respuestas de las 3 preguntas principales para **cada candidato**:

| Campo                   | Descripción                 | Valores posibles                           |
| ----------------------- | --------------------------- | ------------------------------------------ |
| `tbl_grilla_id`         | ID de la grilla del estudio | INT                                        |
| `tbl_participante_id`   | ID del candidato evaluado   | INT (FK a `tbl_participantes`)             |
| `tbl_usuario_id`        | ID del usuario que responde | INT (FK a `tbl_usuarios`)                  |
| `conoce_candidato`      | ¿Conoce al candidato?       | `si` / `no`                                |
| `imagen_candidato`      | Imagen del candidato        | `favorable` / `desfavorable` / `no_aplica` |
| `votaria_por_candidato` | ¿Votaría por él/ella?       | `si` / `no` / `no_aplica`                  |

**Ejemplo de datos guardados:**

```sql
-- Candidato ID 6: APROBADO (conoce=si, imagen=favorable, votaria=si)
INSERT INTO tbl_grilla_candidato_respuestas
VALUES (1, 6, 123, 'si', 'favorable', 'si', NOW());

-- Candidato ID 14: NO APROBADO (conoce=si, imagen=desfavorable, votaria=no_aplica)
INSERT INTO tbl_grilla_candidato_respuestas
VALUES (1, 14, 123, 'si', 'desfavorable', 'no_aplica', NOW());
```

---

### 2. Preguntas Adicionales (tabla: `tbl_grilla_preguntas_adicionales`)

Guarda las respuestas de las preguntas adicionales para **candidatos aprobados**:

| Campo            | Descripción                                               | Valores                             |
| ---------------- | --------------------------------------------------------- | ----------------------------------- |
| `tbl_grilla_id`  | ID de la grilla del estudio                               | INT                                 |
| `tbl_usuario_id` | ID del usuario que responde                               | INT (FK a `tbl_usuarios`)           |
| `pregunta_pa`    | P(A): Si las elecciones fueran hoy, ¿por quién votaría?   | INT (ID del candidato seleccionado) |
| `pregunta_pb`    | P(B): Si su candidato P(A) se retira, ¿por quién votaría? | INT (ID del candidato seleccionado) |
| `pregunta_pc`    | P(C): Si su candidato P(B) se retira, ¿por quién votaría? | INT (ID del candidato seleccionado) |

**Ejemplo de datos guardados:**

```sql
-- Usuario 123 seleccionó:
-- P(A) = Candidato 6 (letra A)
-- P(B) = Candidato 8 (letra B)
-- P(C) = Candidato 6 (letra A nuevamente)
INSERT INTO tbl_grilla_preguntas_adicionales
VALUES (1, 123, 6, 8, 6, NOW());
```

---

## Flujo de Datos Completo

### Paso 1: Usuario responde las 3 preguntas principales para TODOS los candidatos

**Request enviado desde JavaScript:**

```javascript
{
  op: "grillacandidatoguardarrespuestas",
  grilla_id: 1,
  respuestas: JSON.stringify({
    "6": {
      "conoce": "si",
      "imagen": "favorable",
      "votaria": "si"
    },
    "8": {
      "conoce": "si",
      "imagen": "favorable",
      "votaria": "si"
    },
    "14": {
      "conoce": "si",
      "imagen": "desfavorable",
      "votaria": "no_aplica"
    },
    "15": {
      "conoce": "no",
      "imagen": "no_aplica",
      "votaria": "no_aplica"
    }
  })
}
```

**Resultado en base de datos:**

```sql
-- 4 registros insertados en tbl_grilla_candidato_respuestas
-- (uno por cada candidato)
SELECT * FROM tbl_grilla_candidato_respuestas WHERE tbl_grilla_id = 1 AND tbl_usuario_id = 123;

+----+---------------+---------------------+----------------+-------------------+---------------------+-----------------------+
| id | tbl_grilla_id | tbl_participante_id | tbl_usuario_id | conoce_candidato  | imagen_candidato    | votaria_por_candidato |
+----+---------------+---------------------+----------------+-------------------+---------------------+-----------------------+
| 1  | 1             | 6                   | 123            | si                | favorable           | si                    |
| 2  | 1             | 8                   | 123            | si                | favorable           | si                    |
| 3  | 1             | 14                  | 123            | si                | desfavorable        | no_aplica             |
| 4  | 1             | 15                  | 123            | no                | no_aplica           | no_aplica             |
+----+---------------+---------------------+----------------+-------------------+---------------------+-----------------------+
```

---

### Paso 2: Sistema identifica candidatos aprobados

**Candidatos aprobados** son aquellos que cumplen:
- `conoce_candidato = 'si'`
- `imagen_candidato = 'favorable'`
- `votaria_por_candidato = 'si'`

En el ejemplo: **Candidatos 6 y 8** son aprobados.

**Asignación automática de letras:**
- Candidato 6 → Letra A
- Candidato 8 → Letra B

---

### Paso 3: Usuario responde preguntas adicionales P(A), P(B), P(C)

**Request enviado desde JavaScript:**

```javascript
{
  op: "grillacandidatoguardarpreguntasadicionales",
  grilla_id: 1,
  pregunta_pa: 6,  // P(A): Votaría por candidato A (ID 6)
  pregunta_pb: 8,  // P(B): Si A se retira, votaría por B (ID 8)
  pregunta_pc: 6   // P(C): Si B se retira, votaría por A (ID 6)
}
```

**Resultado en base de datos:**

```sql
-- 1 registro insertado en tbl_grilla_preguntas_adicionales
SELECT * FROM tbl_grilla_preguntas_adicionales WHERE tbl_grilla_id = 1 AND tbl_usuario_id = 123;

+----+---------------+----------------+-------------+-------------+-------------+
| id | tbl_grilla_id | tbl_usuario_id | pregunta_pa | pregunta_pb | pregunta_pc |
+----+---------------+----------------+-------------+-------------+-------------+
| 1  | 1             | 123            | 6           | 8           | 6           |
+----+---------------+----------------+-------------+-------------+-------------+
```

---

## Consultas SQL Útiles

### 1. Obtener todos los candidatos aprobados de una grilla

```sql
SELECT
    gcr.tbl_participante_id,
    p.nombre_completo AS candidato,
    p.foto,
    COUNT(DISTINCT gcr.tbl_usuario_id) AS total_aprobaciones
FROM tbl_grilla_candidato_respuestas gcr
INNER JOIN tbl_participantes p ON gcr.tbl_participante_id = p.id
WHERE gcr.tbl_grilla_id = 1
  AND gcr.conoce_candidato = 'si'
  AND gcr.imagen_candidato = 'favorable'
  AND gcr.votaria_por_candidato = 'si'
GROUP BY gcr.tbl_participante_id, p.nombre_completo, p.foto
ORDER BY total_aprobaciones DESC;
```

---

### 2. Obtener resultados de preguntas adicionales con nombres de candidatos

```sql
SELECT
    gpa.tbl_grilla_id,
    gpa.tbl_usuario_id,

    -- P(A)
    gpa.pregunta_pa AS pa_candidato_id,
    pa.nombre_completo AS pa_candidato_nombre,

    -- P(B)
    gpa.pregunta_pb AS pb_candidato_id,
    pb.nombre_completo AS pb_candidato_nombre,

    -- P(C)
    gpa.pregunta_pc AS pc_candidato_id,
    pc.nombre_completo AS pc_candidato_nombre,

    gpa.dtcreate
FROM tbl_grilla_preguntas_adicionales gpa
LEFT JOIN tbl_participantes pa ON gpa.pregunta_pa = pa.id
LEFT JOIN tbl_participantes pb ON gpa.pregunta_pb = pb.id
LEFT JOIN tbl_participantes pc ON gpa.pregunta_pc = pc.id
WHERE gpa.tbl_grilla_id = 1;
```

---

### 3. Obtener votación completa de un usuario específico

```sql
SELECT
    'Respuestas Principales' AS tipo,
    gcr.tbl_participante_id AS candidato_id,
    p.nombre_completo AS candidato,
    CONCAT(
        'Conoce: ', gcr.conoce_candidato, ' | ',
        'Imagen: ', gcr.imagen_candidato, ' | ',
        'Votaría: ', gcr.votaria_por_candidato
    ) AS respuestas
FROM tbl_grilla_candidato_respuestas gcr
INNER JOIN tbl_participantes p ON gcr.tbl_participante_id = p.id
WHERE gcr.tbl_grilla_id = 1 AND gcr.tbl_usuario_id = 123

UNION ALL

SELECT
    'Pregunta Adicional P(A)' AS tipo,
    gpa.pregunta_pa AS candidato_id,
    pa.nombre_completo AS candidato,
    'Si las elecciones fueran hoy' AS respuestas
FROM tbl_grilla_preguntas_adicionales gpa
LEFT JOIN tbl_participantes pa ON gpa.pregunta_pa = pa.id
WHERE gpa.tbl_grilla_id = 1 AND gpa.tbl_usuario_id = 123

UNION ALL

SELECT
    'Pregunta Adicional P(B)' AS tipo,
    gpa.pregunta_pb AS candidato_id,
    pb.nombre_completo AS candidato,
    'Si su candidato P(A) se retira' AS respuestas
FROM tbl_grilla_preguntas_adicionales gpa
LEFT JOIN tbl_participantes pb ON gpa.pregunta_pb = pb.id
WHERE gpa.tbl_grilla_id = 1 AND gpa.tbl_usuario_id = 123

UNION ALL

SELECT
    'Pregunta Adicional P(C)' AS tipo,
    gpa.pregunta_pc AS candidato_id,
    pc.nombre_completo AS candidato,
    'Si su candidato P(B) se retira' AS respuestas
FROM tbl_grilla_preguntas_adicionales gpa
LEFT JOIN tbl_participantes pc ON gpa.pregunta_pc = pc.id
WHERE gpa.tbl_grilla_id = 1 AND gpa.tbl_usuario_id = 123;
```

---

### 4. Estadísticas agregadas: ¿Quién es el candidato favorito?

```sql
-- Contar votos por candidato en P(A) (primera preferencia)
SELECT
    p.id,
    p.nombre_completo,
    COUNT(*) AS total_votos_pa,
    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM tbl_grilla_preguntas_adicionales WHERE tbl_grilla_id = 1)), 2) AS porcentaje
FROM tbl_grilla_preguntas_adicionales gpa
INNER JOIN tbl_participantes p ON gpa.pregunta_pa = p.id
WHERE gpa.tbl_grilla_id = 1
GROUP BY p.id, p.nombre_completo
ORDER BY total_votos_pa DESC;
```

---

## Archivos Modificados

### Frontend (JavaScript)

**Archivo:** `admin/js/votaciones_grilla.js`

**Cambios realizados:**

1. ✅ Agregado objeto `preguntasAdicionales` para almacenar respuestas de P(A), P(B), P(C)
2. ✅ Función `guardarPreguntaAdicional()` para capturar selección de candidatos
3. ✅ Validación de preguntas adicionales antes de guardar
4. ✅ Método `guardarPreguntasAdicionales()` que envía request al backend
5. ✅ Handler actualizado para guardar en dos pasos: respuestas principales → preguntas adicionales

---

### Backend (PHP)

**Archivo:** `admin/classes/GrillaCandidatoRespuesta.php`

**Cambios realizados:**

1. ✅ Método `guardarPreguntasAdicionales()` con validaciones completas
2. ✅ Validación de existencia de candidatos seleccionados
3. ✅ Validación de existencia de grilla
4. ✅ Transacciones SQL para garantizar integridad de datos
5. ✅ Método `verificarTablaPreguntasAdicionales()` para autocreación de tabla

---

**Archivo:** `admin/ajax/rqst.php`

**Cambios realizados:**

1. ✅ Nuevo endpoint: `grillacandidatoguardarpreguntasadicionales`

---

### Base de Datos

**Archivo:** `admin/db/crear_tabla_grilla_respuestas.sql`

**Tablas creadas:**

1. ✅ `tbl_grilla_candidato_respuestas` - Respuestas principales
2. ✅ `tbl_grilla_preguntas_adicionales` - Preguntas adicionales
3. ✅ Vistas: `vw_grilla_resumen_candidatos` y `vw_grilla_preguntas_adicionales_resumen`

---

## Validaciones Implementadas

### En JavaScript:

1. ✅ Validar que todas las respuestas principales estén completas
2. ✅ Si hay candidatos aprobados, validar que P(A), P(B), P(C) estén respondidas
3. ✅ No permitir guardar si faltan respuestas

### En PHP:

1. ✅ Validar que `grilla_id` sea válido
2. ✅ Validar que la grilla exista en la base de datos
3. ✅ Validar que los candidatos seleccionados existan en `tbl_participantes`
4. ✅ Validar que al menos una pregunta adicional tenga respuesta
5. ✅ Usar transacciones SQL para garantizar atomicidad

---

## Integridad de Datos

### Constraints únicos:

```sql
-- Un usuario solo puede responder UNA VEZ para cada candidato en una grilla
UNIQUE KEY unique_respuesta (tbl_grilla_id, tbl_participante_id, tbl_usuario_id)

-- Un usuario solo puede responder UNA VEZ las preguntas adicionales de una grilla
UNIQUE KEY unique_respuesta_adicional (tbl_grilla_id, tbl_usuario_id)
```

### Comportamiento al re-guardar:

Si un usuario vuelve a guardar respuestas:
- Se eliminan las respuestas anteriores (DELETE)
- Se insertan las nuevas respuestas (INSERT)
- Esto permite correcciones sin crear duplicados

---

## Testing Manual

### Test 1: Guardar respuestas completas

1. Seleccionar respuestas para todos los candidatos
2. Verificar que al menos 2 candidatos sean aprobados (A, B)
3. Responder P(A), P(B), P(C)
4. Hacer clic en "Guardar"
5. ✅ Verificar mensaje de éxito
6. ✅ Verificar datos en base de datos

### Test 2: Intentar guardar sin completar preguntas adicionales

1. Seleccionar respuestas para todos los candidatos
2. Verificar que hay candidatos aprobados
3. NO responder P(A), P(B), P(C)
4. Hacer clic en "Guardar"
5. ✅ Debe mostrar alerta indicando que faltan las preguntas adicionales

### Test 3: Guardar sin candidatos aprobados

1. Seleccionar respuestas donde NINGÚN candidato sea aprobado
2. Hacer clic en "Guardar"
3. ✅ Debe guardar solo las respuestas principales
4. ✅ NO debe insertar nada en `tbl_grilla_preguntas_adicionales`

---

## Conclusión

Ahora el sistema guarda **TODA** la información:

✅ **Respuestas principales** de cada candidato (conoce, imagen, votaría)
✅ **Preguntas adicionales** P(A), P(B), P(C) con IDs de candidatos seleccionados
✅ **Validaciones completas** en frontend y backend
✅ **Integridad de datos** garantizada con transacciones SQL
✅ **Consultas SQL** preparadas para reportes y análisis

El request que antes era incompleto:

```javascript
// ANTES (incompleto)
{
  op: "grillacandidatoguardarrespuestas",
  grilla_id: 1,
  respuestas: '{"6":{"conoce":"si","imagen":"favorable","votaria":"si"}}'
}
```

Ahora se complementa con:

```javascript
// AHORA (completo)
// Request 1: Respuestas principales
{
  op: "grillacandidatoguardarrespuestas",
  grilla_id: 1,
  respuestas: '{"6":{"conoce":"si","imagen":"favorable","votaria":"si"}, "8":{...}, "14":{...}}'
}

// Request 2: Preguntas adicionales (automático si hay candidatos aprobados)
{
  op: "grillacandidatoguardarpreguntasadicionales",
  grilla_id: 1,
  pregunta_pa: 6,
  pregunta_pb: 8,
  pregunta_pc: 6
}
```

**¡Todo queda registrado correctamente en la base de datos!** 🎉
