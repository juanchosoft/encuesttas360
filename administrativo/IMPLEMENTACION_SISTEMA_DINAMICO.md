# 🚀 Implementación del Sistema Dinámico de Preguntas - Guía Rápida

## 📋 Resumen Ejecutivo

Se ha implementado un **sistema 100% dinámico** para gestionar preguntas y subpreguntas del estudio de votaciones, administrable completamente desde la base de datos sin necesidad de modificar código.

---

## ✅ Archivos Creados/Modificados

### 🆕 Nuevos Archivos

1. **`admin/classes/PreguntaGrilla.php`**
   - Clase PHP para CRUD de preguntas
   - Métodos: `getAll()`, `obtenerPreguntasConSubpreguntas()`, `save()`, `delete()`

2. **`admin/db/migracion_preguntas_dinamicas.sql`**
   - Script SQL para mejorar estructura de tabla
   - Configuración inicial (3 preguntas + 3 subpreguntas)
   - Vista `vw_preguntas_grilla_completas`

3. **`admin/db/migracion_guardado_json.sql`**
   - Migración para usar campos JSON flexibles
   - Compatibilidad con estructura existente

4. **`admin/db/SISTEMA_PREGUNTAS_DINAMICAS.md`**
   - Documentación técnica completa del sistema

### 📝 Archivos Modificados

1. **`candidato.php`**
   - Carga preguntas desde BD
   - Renderiza headers dinámicamente
   - Renderiza filas con botones según configuración BD

2. **`admin/js/votaciones_grilla.js`**
   - Lógica 100% dinámica
   - Soporta N preguntas y M subpreguntas
   - Condiciones configurables desde BD

3. **`admin/ajax/rqst.php`**
   - 5 nuevos endpoints para preguntas
   - `preguntasgrillaget`, `preguntasgrillaobtenerconsubpreguntas`, etc.

---

## 🔧 Pasos de Implementación

### Paso 1: Ejecutar Scripts SQL ✅

```bash
# Conectar a MySQL/MariaDB
mysql -u root -p estadisticas_db

# Ejecutar migraciones EN ORDEN
source admin/db/migracion_preguntas_dinamicas.sql
source admin/db/migracion_guardado_json.sql
```

**Verificar:**
```sql
SELECT * FROM vw_preguntas_grilla_completas;
```

Deberías ver 3 preguntas principales + 3 subpreguntas.

---

### Paso 2: Verificar Archivos PHP ✅

Archivos ya están en su lugar:
- ✅ `admin/classes/PreguntaGrilla.php`
- ✅ `candidato.php` (modificado)
- ✅ `admin/ajax/rqst.php` (modificado)

---

### Paso 3: Verificar JavaScript ✅

Archivo ya actualizado:
- ✅ `admin/js/votaciones_grilla.js` (versión dinámica)
- 📄 `admin/js/votaciones_grilla_original_backup.js` (backup del original)

---

### Paso 4: Probar Funcionamiento 🧪

1. **Abrir grilla.php**
2. **Hacer clic en "Ver Estudio"**
3. **Verificar:**
   - ✅ Headers de tabla vienen de BD
   - ✅ Botones toggle se renderizan según configuración
   - ✅ Lógica condicional funciona
   - ✅ Subpreguntas aparecen para candidatos aprobados

---

## 🎯 Cómo Usar el Sistema

### Agregar una 4ta Pregunta Principal

```sql
INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden, 
 opciones_respuesta, habilita_subpreguntas, condicion_habilitacion, 
 habilitado, tbl_usuario_id)
VALUES
('pregunta', '¿CONFÍA EN ESTE CANDIDATO?', 'confia', 4,
 '["mucho", "poco", "nada"]', FALSE, NULL, TRUE, 2);
```

**Resultado:** Aparece automáticamente después de las 3 preguntas existentes.

### Agregar una 4ta Subpregunta

```sql
-- Obtener ID de pregunta padre
SET @pregunta_votaria_id = (
    SELECT id FROM tbl_preguntas_sub_preguntas_grilla 
    WHERE codigo_pregunta = 'votaria' LIMIT 1
);

INSERT INTO tbl_preguntas_sub_preguntas_grilla
(tipo_pregunta, texto_pregunta, codigo_pregunta, orden,
 pregunta_padre_id, habilitado, tbl_usuario_id)
VALUES
('subpregunta', 'Si todos se retiran, ¿por quién votaría? P(D)', 
 'pd', 4, @pregunta_votaria_id, TRUE, 2);
```

**Resultado:** Aparece automáticamente después de P(A), P(B), P(C).

### Cambiar Texto de una Pregunta

```sql
UPDATE tbl_preguntas_sub_preguntas_grilla
SET texto_pregunta = 'NUEVO TEXTO DE LA PREGUNTA'
WHERE codigo_pregunta = 'conoce';
```

**Resultado:** Texto actualizado en toda la interfaz sin tocar código.

### Cambiar Opciones de Respuesta

```sql
UPDATE tbl_preguntas_sub_preguntas_grilla
SET opciones_respuesta = '["excelente", "bueno", "regular", "malo"]'
WHERE codigo_pregunta = 'imagen';
```

**Resultado:** Los botones se renderiz an con las nuevas opciones.

---

## 📊 Estructura de Datos

### Tabla: `tbl_preguntas_sub_preguntas_grilla`

```
id | tipo_pregunta | texto_pregunta | codigo_pregunta | orden | opciones_respuesta | ...
1  | pregunta      | CONOCE...      | conoce          | 1     | ["si","no"]        |
2  | pregunta      | IMAGEN...      | imagen          | 2     | ["favorable",...] |
3  | pregunta      | VOTARIA...     | votaria         | 3     | ["si","no"]        |
4  | subpregunta   | SI LAS...      | pa              | 1     | NULL               |
5  | subpregunta   | SI P(A)...     | pb              | 2     | NULL               |
6  | subpregunta   | SI P(B)...     | pc              | 3     | NULL               |
```

---

## 🔍 Validación

### ¿Cómo saber si funcionó?

1. **Verificar BD:**
   ```sql
   SELECT codigo_pregunta, texto_pregunta, orden 
   FROM vw_preguntas_grilla_completas
   ORDER BY tipo_pregunta, orden;
   ```

2. **Verificar Interfaz:**
   - Abrir candidato.php
   - Headers de tabla = textos de BD
   - Botones = opciones de BD

3. **Verificar Lógica:**
   - Seleccionar "NO" en primera pregunta
   - Verificar que siguientes se deshabilitan
   - Seleccionar "SÍ" en todas
   - Verificar que aparecen subpreguntas

---

## 🐛 Troubleshooting

### Problema: No aparecen las preguntas

**Solución:**
```sql
-- Verificar que hay preguntas
SELECT * FROM tbl_preguntas_sub_preguntas_grilla WHERE habilitado = TRUE;

-- Si no hay, ejecutar:
source admin/db/migracion_preguntas_dinamicas.sql
```

### Problema: Error en JavaScript

**Solución:**
```bash
# Verificar que se reemplazó el archivo
ls -la admin/js/votaciones_grilla*

# Debería mostrar:
# votaciones_grilla.js (nuevo dinámico)
# votaciones_grilla_original_backup.js (backup)
# votaciones_grilla_v2.js (fuente del nuevo)
```

### Problema: No se guardan las respuestas

**Solución:**
```sql
-- Verificar que columnas JSON existen
SHOW COLUMNS FROM tbl_grilla_candidato_respuestas LIKE '%json%';
SHOW COLUMNS FROM tbl_grilla_preguntas_adicionales LIKE '%json%';

-- Si no existen:
source admin/db/migracion_guardado_json.sql
```

---

## 📈 Ventajas del Sistema

| Antes | Ahora |
|-------|-------|
| 3 preguntas fijas en código | N preguntas desde BD |
| 3 subpreguntas fijas | M subpreguntas desde BD |
| Cambiar texto = editar PHP | Cambiar texto = UPDATE SQL |
| Agregar pregunta = modificar 5 archivos | Agregar pregunta = INSERT SQL |
| Lógica hardcodeada | Lógica configurable |

---

## 🎓 Próximos Pasos Opcionales

### Crear Interfaz de Administración

Puedes crear un CRUD visual para que los usuarios NO técnicos administren preguntas:

```
admin/preguntas_grilla.php
- Listar preguntas
- Agregar/editar/eliminar
- Cambiar orden
- Preview en tiempo real
```

### Asociar Preguntas por Grilla

Actualmente todas las grillas usan las mismas preguntas. Si necesitas diferentes preguntas por grilla:

```sql
ALTER TABLE tbl_preguntas_sub_preguntas_grilla
ADD COLUMN tbl_grilla_id INT NULL;

-- NULL = todas las grillas
-- ID específico = solo esa grilla
```

---

## ✅ Checklist Final

- [ ] ✅ Scripts SQL ejecutados
- [ ] ✅ Archivos PHP en su lugar
- [ ] ✅ JavaScript actualizado
- [ ] ✅ Funcionalidad probada
- [ ] ✅ Agregar una pregunta de prueba
- [ ] ✅ Verificar guardado de respuestas
- [ ] ✅ Verificar resultados en tiempo real

---

## 📞 Soporte

Si tienes dudas o problemas:

1. **Revisar logs:**
   - Consola del navegador (F12)
   - Logs de PHP (`error_log`)
   - Logs de MySQL

2. **Consultar documentación:**
   - `admin/db/SISTEMA_PREGUNTAS_DINAMICAS.md`

3. **Verificar estructura BD:**
   ```sql
   DESCRIBE tbl_preguntas_sub_preguntas_grilla;
   SELECT * FROM vw_preguntas_grilla_completas;
   ```

---

## 🎉 ¡Listo!

El sistema está completamente implementado y funcional. Ahora puedes:

✅ Agregar preguntas sin tocar código
✅ Modificar textos en segundos
✅ Escalar a cualquier número de preguntas
✅ Administrar todo desde la base de datos

**¡El cliente puede gestionar sus propios estudios de manera independiente!**
