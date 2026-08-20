# Guía Completa: Sistema de Administración de Preguntas de Grilla

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Interfaz del CRUD](#interfaz-del-crud)
4. [Gestión de Preguntas Principales](#gestión-de-preguntas-principales)
5. [Gestión de Subpreguntas](#gestión-de-subpreguntas)
6. [Vista Previa](#vista-previa)
7. [Asociación por Grilla](#asociación-por-grilla)
8. [Casos de Uso Comunes](#casos-de-uso-comunes)
9. [Solución de Problemas](#solución-de-problemas)

---

## 📖 Introducción

El Sistema de Administración de Preguntas permite gestionar de forma visual y sin conocimientos técnicos todas las preguntas y subpreguntas que se muestran en los estudios de votación.

### ✨ Características Principales

- ✅ **Interfaz visual completa** - No requiere editar código o SQL
- ✅ **CRUD completo** - Crear, Leer, Actualizar, Eliminar
- ✅ **Vista previa en tiempo real** - Ver cómo se verán las preguntas antes de guardar
- ✅ **Pestañas organizadas** - Preguntas principales, subpreguntas y preview separados
- ✅ **Asociación por grilla** - Diferentes preguntas para diferentes estudios
- ✅ **Validación automática** - Evita errores en configuración

---

## 🔐 Acceso al Sistema

### URL de Acceso

```
http://localhost:8080/Github/EstadisticasGobierno/admin/preguntas_grilla.php
```

### Permisos Requeridos

- **Rol**: Administrador o SuperAdministrador
- **Permisos**:
  - Ver (ID: 1)
  - Crear (ID: 2)
  - Editar (ID: 3)

Si no tienes acceso, contacta al administrador del sistema.

---

## 🖥️ Interfaz del CRUD

### Estructura de la Interfaz

La interfaz está dividida en **3 pestañas principales**:

#### 1️⃣ Preguntas Principales

Muestra todas las preguntas que se hacen **por cada candidato**.

**Columnas de la tabla:**

| Columna                  | Descripción                          | Ejemplo                       |
| ------------------------ | ------------------------------------ | ----------------------------- |
| **Orden**                | Posición en la que aparece           | 1, 2, 3                       |
| **Código**               | Identificador único en el código     | `conoce`, `imagen`, `votaria` |
| **Texto de la Pregunta** | Pregunta visible para el usuario     | "¿CONOCE O NO LO CONOCE?"     |
| **Opciones**             | Botones de respuesta disponibles     | si, no                        |
| **Condición**            | Cuándo habilita siguientes preguntas | Si responde SÍ                |
| **Estado**               | Activa / Inactiva                    | ✅ Activa                      |
| **Acciones**             | Editar ✏️ / Eliminar 🗑️                | -                             |

#### 2️⃣ Subpreguntas

Muestra las preguntas que **solo se hacen si hay candidatos aprobados**.

**Columnas de la tabla:**

| Columna                     | Descripción                 | Ejemplo                           |
| --------------------------- | --------------------------- | --------------------------------- |
| **Orden**                   | Posición de la subpregunta  | 1, 2, 3                           |
| **Código**                  | Identificador único         | `pa`, `pb`, `pc`                  |
| **Texto de la Subpregunta** | Pregunta visible            | "SI LAS ELECCIONES FUERAN HOY..." |
| **Pregunta Padre**          | Pregunta principal asociada | "VOTARIA POR EL O POR ELLA"       |
| **Estado**                  | Activa / Inactiva           | ✅ Activa                          |
| **Acciones**                | Editar ✏️ / Eliminar 🗑️       | -                                 |

#### 3️⃣ Vista Previa

Muestra cómo se verán las preguntas en la interfaz real de votación.

---

## ➕ Gestión de Preguntas Principales

### Crear Nueva Pregunta Principal

**Paso 1:** Clic en botón "Nueva Pregunta"

**Paso 2:** Completar el formulario:

#### Campos del Formulario

**1. Tipo** *(Requerido)*
```
Seleccionar: "Pregunta Principal"
```

**2. Código Único** *(Requerido)*
```
Formato: Solo letras minúsculas, sin espacios
Ejemplos válidos: conoce, imagen, votaria, confia
Ejemplos INVÁLIDOS: Conoce, ima gen, votaría
```

**3. Texto de la Pregunta** *(Requerido)*
```
Formato: Texto libre, mayúsculas recomendadas
Ejemplo: ¿CONOCE AL CANDIDATO?
```

**4. Orden** *(Requerido)*
```
Formato: Número entero positivo
Define en qué posición aparece (1 = primera, 2 = segunda, etc.)
```

**5. Opciones de Respuesta** *(JSON)*
```json
Formato: Array JSON con las opciones
Ejemplos:
  ["si", "no"]
  ["favorable", "desfavorable"]
  ["mucho", "poco", "nada"]
```

**6. Habilita siguientes preguntas** *(Checkbox)*
```
✅ Marcado: Al responder esta pregunta se habilita la siguiente
❌ Desmarcado: No habilita nada
```

**7. Condición** *(Dropdown)*
```
Opciones:
  - Ninguna: No tiene condición
  - Si responde SÍ: Solo habilita si respuesta es "si"
  - Si responde FAVORABLE: Solo habilita si respuesta es "favorable"
  - Si todas las anteriores son SÍ: Habilita solo si TODAS son positivas
```

**8. Requiere todas anteriores en SÍ** *(Checkbox)*
```
✅ Marcado: Esta pregunta solo se habilita si todas las anteriores son SÍ
❌ Desmarcado: Se habilita según la condición de la pregunta anterior
```

**9. Activa** *(Checkbox)*
```
✅ Marcado: La pregunta se muestra en la interfaz
❌ Desmarcado: La pregunta está oculta (no se elimina, solo se desactiva)
```

**Paso 3:** Clic en "Guardar"

### Ejemplo Práctico: Crear Pregunta "Confianza"

```
Tipo: Pregunta Principal
Código Único: confia
Texto: ¿CONFÍA EN ESTE CANDIDATO?
Orden: 4
Opciones: ["mucho", "poco", "nada"]
Habilita subpreguntas: ❌ No
Condición: Ninguna
Requiere todas anteriores: ❌ No
Activa: ✅ Sí
```

### Editar Pregunta Existente

**Paso 1:** En la tabla, clic en botón ✏️ "Editar"

**Paso 2:** Modificar los campos necesarios

**Paso 3:** Clic en "Guardar"

**⚠️ Advertencia:** Si cambias el **Código Único**, asegúrate de que no esté siendo usado en código personalizado.

### Eliminar Pregunta

**Paso 1:** En la tabla, clic en botón 🗑️ "Eliminar"

**Paso 2:** Confirmar la eliminación

**⚠️ Advertencia:** Esta acción es **irreversible**. La pregunta y sus subpreguntas asociadas se eliminarán permanentemente.

---

## 📝 Gestión de Subpreguntas

Las subpreguntas son preguntas especiales que **solo se muestran cuando hay candidatos aprobados** (que respondieron positivamente a todas las preguntas principales).

### Crear Nueva Subpregunta

**Paso 1:** Clic en botón "Nueva Pregunta"

**Paso 2:** Completar el formulario:

#### Campos del Formulario (Subpreguntas)

**1. Tipo** *(Requerido)*
```
Seleccionar: "Subpregunta"
```

**2. Código Único** *(Requerido)*
```
Ejemplos: pa, pb, pc, pd
```

**3. Texto de la Subpregunta** *(Requerido)*
```
Ejemplo: SI LAS ELECCIONES FUERAN HOY UD POR QUIEN VOTARIA P(A)
```

**4. Orden** *(Requerido)*
```
Define en qué posición aparece entre las subpreguntas
```

**5. Pregunta Principal Asociada** *(Dropdown)*
```
Selecciona la pregunta principal que activa esta subpregunta
Generalmente: "VOTARIA POR EL O POR ELLA"
```

**6. Activa** *(Checkbox)*
```
✅ Marcado: Se muestra cuando hay candidatos aprobados
❌ Desmarcado: Está oculta
```

**Nota:** Las subpreguntas NO tienen opciones de respuesta fijas. Las opciones son los **candidatos aprobados** de forma dinámica.

### Ejemplo Práctico: Crear Subpregunta P(D)

```
Tipo: Subpregunta
Código Único: pd
Texto: SI LOS 3 ANTERIORES SE RETIRAN ¿POR QUIÉN VOTARÍA? P(D)
Orden: 4
Pregunta Padre: "VOTARIA POR EL O POR ELLA"
Activa: ✅ Sí
```

---

## 👁️ Vista Previa

La pestaña "Vista Previa" muestra cómo se verán las preguntas en la interfaz real.

### Qué Verás

1. **Tabla simulada** con un candidato de prueba
2. **Botones toggle** (deshabilitados) mostrando las opciones de respuesta
3. **Tarjetas de subpreguntas** mostrando cómo aparecerán

### Cuándo Actualizar

La vista previa se actualiza automáticamente cuando:
- Haces clic en la pestaña "Vista Previa"
- Guardas una pregunta nueva
- Editas una pregunta existente

---

## 🔗 Asociación por Grilla

Esta funcionalidad permite que **diferentes grillas usen diferentes conjuntos de preguntas**.

### Conceptos

**Preguntas Globales:**
- `tbl_grilla_id = NULL`
- Se usan en **todas las grillas**
- Ideales para preguntas estándar (conoce, imagen, votaría)

**Preguntas Específicas:**
- `tbl_grilla_id = ID de la grilla`
- Solo se usan en **esa grilla específica**
- Ideales para estudios especiales

### Cómo Activar

**Paso 1:** Ejecutar migración SQL

```bash
mysql -u root -p estadisticas_db < admin/db/migracion_preguntas_por_grilla.sql
```

**Paso 2:** Al crear/editar pregunta, aparecerá campo "Grilla Asociada"

**Paso 3:** Seleccionar:
- **Todas las grillas** (NULL) → Pregunta global
- **Grilla específica** → Solo para esa grilla

### Ejemplo de Uso

**Caso:** Tienes 2 grillas diferentes:

1. **Grilla "Elecciones Presidenciales 2026"**
   - Preguntas globales: Conoce, Imagen, Votaría
   - Pregunta específica: "¿Apoya el plan de gobierno del candidato?"

2. **Grilla "Alcaldía Municipal"**
   - Preguntas globales: Conoce, Imagen, Votaría
   - Pregunta específica: "¿Conoce el trabajo del candidato en su localidad?"

---

## 💡 Casos de Uso Comunes

### Caso 1: Agregar 4ta Pregunta Principal

**Objetivo:** Agregar pregunta "¿Recomendaría al candidato?"

**Solución:**

1. Clic en "Nueva Pregunta"
2. Completar:
   ```
   Tipo: Pregunta Principal
   Código: recomienda
   Texto: ¿RECOMENDARÍA AL CANDIDATO?
   Orden: 4
   Opciones: ["si", "no"]
   Habilita subpreguntas: ❌ No
   Condición: Ninguna
   Activa: ✅ Sí
   ```
3. Guardar
4. Verificar en "Vista Previa"

### Caso 2: Cambiar Texto de Pregunta Existente

**Objetivo:** Cambiar "CONOCE O NO LO CONOCE" por "¿HA ESCUCHADO DEL CANDIDATO?"

**Solución:**

1. En pestaña "Preguntas Principales"
2. Buscar la pregunta con código `conoce`
3. Clic en ✏️ Editar
4. Modificar campo "Texto de la Pregunta"
5. Guardar
6. **¡Listo!** El cambio se refleja automáticamente en todos los estudios

### Caso 3: Desactivar Temporalmente una Pregunta

**Objetivo:** Ocultar pregunta sin eliminarla

**Solución:**

1. Clic en ✏️ Editar la pregunta
2. Desmarcar checkbox "Activa"
3. Guardar
4. La pregunta ya no aparece en la interfaz, pero no se eliminó

Para reactivarla: Editar y volver a marcar "Activa"

### Caso 4: Cambiar Orden de Preguntas

**Objetivo:** Mover "IMAGEN" antes de "CONOCE"

**Solución:**

1. Editar pregunta "IMAGEN"
   - Cambiar Orden de `2` a `1`
2. Editar pregunta "CONOCE"
   - Cambiar Orden de `1` a `2`
3. Recargar página
4. Orden actualizado

### Caso 5: Agregar Opciones de Respuesta Personalizadas

**Objetivo:** Crear pregunta con 3 opciones en lugar de 2

**Solución:**

1. Nueva Pregunta
2. En "Opciones de Respuesta":
   ```json
   ["excelente", "regular", "malo"]
   ```
3. Guardar
4. La interfaz mostrará 3 botones automáticamente

---

## 🐛 Solución de Problemas

### Problema 1: No puedo acceder a la página

**Síntoma:** Muestra "Permiso denegado"

**Solución:**
- Verifica que tu usuario sea Administrador o SuperAdministrador
- Contacta al administrador para que te asigne los permisos necesarios

### Problema 2: Error "Código de pregunta ya existe"

**Síntoma:** Al guardar sale error de código duplicado

**Solución:**
- Cambia el "Código Único" por uno diferente
- Los códigos deben ser únicos en toda la tabla

### Problema 3: La pregunta no aparece en la interfaz

**Síntoma:** Guardé la pregunta pero no se ve en el estudio

**Solución:**
1. Verifica que checkbox "Activa" esté marcado
2. Verifica el orden (debe ser secuencial: 1, 2, 3...)
3. Recarga la página del estudio de votaciones

### Problema 4: Error en JSON de opciones

**Síntoma:** "El formato de opciones no es válido"

**Solución:**
- Verifica sintaxis JSON:
  - ✅ Correcto: `["si", "no"]`
  - ❌ Incorrecto: `['si', 'no']` (comillas simples)
  - ❌ Incorrecto: `[si, no]` (sin comillas)
  - ❌ Incorrecto: `["si" "no"]` (falta coma)

### Problema 5: Las subpreguntas no se muestran

**Síntoma:** Hay candidatos aprobados pero no aparecen subpreguntas

**Solución:**
1. Verifica que las preguntas principales tengan:
   - "Habilita subpreguntas" = ✅ Marcado
   - "Condición" configurada correctamente
2. Verifica que las subpreguntas:
   - Tengan "Pregunta Padre" asignada
   - Estén "Activas"

### Problema 6: Vista previa no se actualiza

**Síntoma:** Guardé cambios pero vista previa sigue igual

**Solución:**
1. Cambia a otra pestaña
2. Vuelve a la pestaña "Vista Previa"
3. Esto fuerza la recarga

---

## 📊 Resumen de Ventajas

| Antes (Sin CRUD)              | Ahora (Con CRUD)         |
| ----------------------------- | ------------------------ |
| Editar SQL manualmente        | ✅ Interfaz visual        |
| Conocer estructura de BD      | ✅ Formularios guiados    |
| Riesgo de errores de sintaxis | ✅ Validación automática  |
| Editar código PHP/JS          | ✅ Sin tocar código       |
| Esperar al programador        | ✅ Autonomía total        |
| Sin vista previa              | ✅ Preview en tiempo real |

---

## 🎯 Próximos Pasos Recomendados

1. ✅ **Familiarizarse con la interfaz** - Explora las 3 pestañas
2. ✅ **Prueba en desarrollo** - Crea una pregunta de prueba
3. ✅ **Verifica en el estudio** - Abre candidato.php y verifica que aparezca
4. ✅ **Capacita al equipo** - Comparte esta guía con otros usuarios
5. ✅ **Personaliza según necesidad** - Adapta preguntas a tus estudios

---

## 📞 Soporte

Si tienes problemas o dudas:

1. **Revisa esta guía** - La mayoría de casos están cubiertos
2. **Verifica logs del navegador** - Presiona F12 → Consola
3. **Contacta soporte técnico** - Proporciona capturas de pantalla del error

---

**Última actualización:** 2025-10-23
**Versión:** 1.0
**Autor:** Sistema de Estadísticas Gobierno
