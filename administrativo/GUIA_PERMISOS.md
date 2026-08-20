# Guía del Sistema de Permisos Automáticos

## Descripción General

El sistema asigna automáticamente permisos cuando se crea o edita un usuario según su tipo. Los permisos se almacenan en la tabla `tbl_usuarios_has_tbl_permisos`.

---

## Los 6 Tipos de Usuario del Sistema

### 1. **Administrador**
- **Total de permisos**: 85 (TODOS los permisos del sistema)
- **Acceso**: Completo a todos los módulos sin restricciones
- **Uso recomendado**: Director del proyecto, gerente general

### 2. **Investigador**
- **Total de permisos**: 44
- **Acceso completo a**:
  - ✅ **Análisis de Estudio** (Ver, Crear, Editar, Eliminar) - Permisos 50, 51, 52, 53
  - ✅ **Fórmulas** (Ver, Crear, Editar, Eliminar) - Permisos 46, 47, 48, 49
  - ✅ **Grilla** (Ver, Crear, Editar, Eliminar) - Permisos 42, 43, 44, 45
  - ✅ **Preguntas Grilla** (Ver, Crear, Editar, Eliminar) - Permisos 38, 39, 40, 41
  - ✅ **Votantes** (Ver, Crear, Editar) - Permisos 26, 27, 28
  - ✅ **Personal Político** (Ver, Crear, Editar) - Permisos 22, 23, 24
  - ✅ **Sondeos** (Ver, Crear, Editar) - Permisos 34, 35, 36
  - ✅ **Partidos Políticos** (Ver, Crear, Editar) - Permisos 10, 11, 12
  - ✅ **Preguntas** (Ver, Crear, Editar) - Permisos 30, 31, 32
  - ✅ **Espacio Geográfico** (Ver, Crear, Editar) - Permisos 14, 15, 16
  - ✅ **Ficha Técnica Encuesta** (Ver, Crear, Editar) - Permisos 18, 19, 20
  - ✅ **Línea** (Ver, Crear, Editar) - Permisos 80, 81, 82
  - ✅ **Estrategia** (Ver, Crear, Editar) - Permisos 83, 84, 85
  - 👁️ **Usuarios** (Solo Ver) - Permiso 1
- **Uso recomendado**: Analistas estadísticos, investigadores que realizan análisis avanzados

### 3. **Visor**
- **Total de permisos**: 14
- **Acceso solo de visualización** (NO puede crear ni editar):
  - 👁️ Usuarios - Permiso 1
  - 👁️ Partidos Políticos - Permiso 10
  - 👁️ Espacio Geográfico - Permiso 14
  - 👁️ Ficha Técnica Encuesta - Permiso 18
  - 👁️ Personal Político - Permiso 22
  - 👁️ Votantes - Permiso 26
  - 👁️ Preguntas - Permiso 30
  - 👁️ Sondeos - Permiso 34
  - 👁️ Preguntas Grilla - Permiso 38
  - 👁️ Grilla - Permiso 42
  - 👁️ Fórmulas - Permiso 46
  - 👁️ Análisis de Estudio - Permiso 50
  - 👁️ Línea - Permiso 80
  - 👁️ Estrategia - Permiso 83
- **Uso recomendado**: Consultores externos, auditores, observadores

### 4. **Operativo**
- **Total de permisos**: 31
- **Acceso Ver + Crear + Editar** (sin módulos avanzados de análisis):
  - 👁️ **Usuarios** (Solo Ver) - Permiso 1
  - ✏️ **Partidos Políticos** (Ver, Crear, Editar) - Permisos 10, 11, 12
  - ✏️ **Espacio Geográfico** (Ver, Crear, Editar) - Permisos 14, 15, 16
  - ✏️ **Ficha Técnica Encuesta** (Ver, Crear, Editar) - Permisos 18, 19, 20
  - ✏️ **Personal Político** (Ver, Crear, Editar) - Permisos 22, 23, 24
  - ✏️ **Votantes** (Ver, Crear, Editar) - Permisos 26, 27, 28
  - ✏️ **Preguntas** (Ver, Crear, Editar) - Permisos 30, 31, 32
  - ✏️ **Sondeos** (Ver, Crear, Editar) - Permisos 34, 35, 36
  - ✏️ **Preguntas Grilla** (Ver, Crear, Editar) - Permisos 38, 39, 40
  - ✏️ **Grilla** (Ver, Crear, Editar) - Permisos 42, 43, 44
  - ✏️ **Fórmulas** (Ver, Crear, Editar) - Permisos 46, 47, 48
- **SIN acceso a**: Análisis de Estudio (módulo avanzado reservado para Investigador)
- **Uso recomendado**: Personal operativo, coordinadores de campo, asistentes

### 5. **Encuestador**
- **Total de permisos**: 4
- **Acceso completo solo a**:
  - 📋 **Votantes** (Ver, Crear, Editar, Permisos) - Permisos 26, 27, 28, 29
- **Uso recomendado**: Encuestadores de campo, recolectores de datos de votantes

### 6. **Cliente**
- **Total de permisos**: 2
- **Acceso limitado a visualización de grillas**:
  - 👁️ **Preguntas Grilla** (Ver) - Permiso 38
  - 👁️ **Grilla** (Ver) - Permiso 42
- **Uso recomendado**: Clientes externos que solo necesitan ver resultados de grillas
- **Nota**: Se pueden agregar más permisos en el futuro según necesidades

---

## Cómo Usar en el Código PHP

### 1. Validar Tipo de Usuario (Método Genérico)

```php
// Validar cualquier tipo de usuario
if (SessionData::esTipoUsuario("Investigador")) {
    // Código específico para investigadores
    echo "Eres investigador";
}

// Obtener el tipo actual
$tipoActual = SessionData::getTipoUsuario();
echo "Usuario tipo: " . $tipoActual;
```

### 2. Métodos Específicos por Tipo

```php
// Verificar Administrador
if (SessionData::administrador()) {
    // Código para administradores
    echo "Acceso completo al sistema";
}

// Verificar Investigador
if (SessionData::investigador()) {
    // Mostrar opciones de análisis avanzado
    echo "Puedes acceder a Análisis de Estudio y Fórmulas";
}

// Verificar Visor
if (SessionData::visor()) {
    // Ocultar botones de edición
    echo "Solo puedes visualizar, no editar";
}

// Verificar Operativo
if (SessionData::operativo()) {
    // Mostrar opciones operativas
    echo "Puedes crear y editar datos operativos";
}

// Verificar Encuestador
if (SessionData::encuestador()) {
    // Solo mostrar módulo de votantes
    echo "Solo tienes acceso al módulo de Votantes";
}

// Verificar Cliente
if (SessionData::cliente()) {
    // Mostrar solo grillas
    echo "Solo puedes ver resultados de grillas";
}
```

### 3. Métodos de Validación Compuesta

```php
// Verificar si es Administrador (acceso total)
if (SessionData::esAdministrativo()) {
    echo "Tienes acceso administrativo completo";
}

// Verificar si puede acceder a análisis avanzados (Investigador o Administrador)
if (SessionData::esInvestigadorOAdmin()) {
    // Mostrar módulo de Análisis de Estudio
    // Mostrar calculadora de fórmulas
    echo "Puedes realizar análisis estadísticos avanzados";
}
```

### 4. Ejemplo Completo de Validación en una Página

```php
<?php
include './admin/include/head.php';

// Verificar permiso de visualización del módulo
$view = SessionData::getPermission(50); // Análisis de Estudio - Ver

if (!$view) {
    require 'permiso_denegado.php';
    exit;
}

// Validar tipo de usuario para funciones avanzadas
if (SessionData::esInvestigadorOAdmin()) {
    // Mostrar calculadora y herramientas avanzadas
    $mostrarCalculadora = true;
} else {
    // Solo mostrar vista básica
    $mostrarCalculadora = false;
}

// Personalizar interfaz según tipo
if (SessionData::visor()) {
    // Ocultar todos los botones de edición
    $soloLectura = true;
} elseif (SessionData::cliente()) {
    // Redirigir a página de grillas
    header('Location: grilla.php');
    exit;
}
?>
```

### 5. Ejemplo en Menú de Navegación

```php
<?php if (SessionData::administrador() || SessionData::investigador()): ?>
    <!-- Menú de Análisis Avanzado -->
    <li class="nav-item">
        <a href="analisis_estudio.php" class="nav-link">
            <i class="fas fa-chart-line"></i> Análisis de Estudio
        </a>
    </li>
    <li class="nav-item">
        <a href="formulas.php" class="nav-link">
            <i class="fas fa-calculator"></i> Fórmulas
        </a>
    </li>
<?php endif; ?>

<?php if (!SessionData::cliente()): ?>
    <!-- Menú completo para todos excepto Cliente -->
    <li class="nav-item">
        <a href="votantes.php" class="nav-link">
            <i class="fas fa-users"></i> Votantes
        </a>
    </li>
<?php endif; ?>

<?php if (SessionData::visor() || SessionData::cliente()): ?>
    <!-- Mensaje para usuarios de solo lectura -->
    <div class="alert alert-info">
        <i class="fas fa-eye"></i> Modo solo lectura
    </div>
<?php endif; ?>
```

---

## Uso en JavaScript

```javascript
// El tipo de usuario se pasa desde PHP al frontend
const USUARIO_TIPO = '<?= SessionData::getTipoUsuario() ?>';

// Validar tipo en JavaScript
if (USUARIO_TIPO === 'Visor' || USUARIO_TIPO === 'Cliente') {
    // Ocultar botones de edición
    $('.btn-edit').hide();
    $('.btn-delete').hide();
    $('.btn-save').hide();

    // Deshabilitar inputs
    $('input, textarea, select').attr('disabled', true);
}

if (USUARIO_TIPO === 'Investigador' || USUARIO_TIPO === 'Administrador') {
    // Mostrar funciones avanzadas
    $('.advanced-tools').show();
    $('.calculator-section').show();
}

if (USUARIO_TIPO === 'Cliente') {
    // Ocultar todo excepto grillas
    $('.module-votantes').hide();
    $('.module-sondeos').hide();
    // Solo mostrar grillas
    $('.module-grilla').show();
}
```

---

## Verificación de Permisos en Base de Datos

### Consulta para ver permisos de un usuario

```sql
-- Ver todos los permisos de un usuario
SELECT
    u.nombre,
    u.apellido,
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as total_permisos,
    GROUP_CONCAT(p.id ORDER BY p.id) as lista_permisos
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
LEFT JOIN tbl_permisos p ON uhp.tbl_permisos_id = p.id
WHERE u.email = 'usuario@ejemplo.com'
GROUP BY u.id;

-- Ver permisos detallados con descripciones
SELECT
    u.nombre,
    u.apellido,
    u.tipo,
    p.id as permiso_id,
    p.descripcion,
    uhp.dtcreate as fecha_asignacion
FROM tbl_usuarios u
INNER JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
INNER JOIN tbl_permisos p ON uhp.tbl_permisos_id = p.id
WHERE u.email = 'usuario@ejemplo.com'
ORDER BY p.id;

-- Contar permisos por tipo de usuario (verificar asignación correcta)
SELECT
    u.tipo,
    COUNT(uhp.tbl_permisos_id) as total_permisos,
    GROUP_CONCAT(DISTINCT u.nombre) as usuarios_ejemplo
FROM tbl_usuarios u
LEFT JOIN tbl_usuarios_has_tbl_permisos uhp ON u.id = uhp.tbl_usuarios_id
GROUP BY u.tipo
ORDER BY total_permisos DESC;
```

**Resultados esperados:**
- Administrador: 85 permisos
- Investigador: 44 permisos
- Operativo: 31 permisos
- Visor: 14 permisos
- Encuestador: 4 permisos
- Cliente: 2 permisos

---

## Flujo de Asignación Automática

1. **Usuario crea/edita un usuario** en [usuarios.php](usuarios.php)
2. **Selecciona el tipo** del dropdown (Administrador, Investigador, Visor, Operativo, Encuestador, Cliente)
3. **Formulario se envía** a [admin/ajax/rqst.php](admin/ajax/rqst.php)
4. **Se ejecuta** `Usuario::save()` en [admin/classes/Usuario.php](admin/classes/Usuario.php)
5. **Sistema detecta el tipo** y busca el array `$arrchk` correspondiente (líneas 206-281)
6. **Elimina permisos anteriores** del usuario
7. **Asigna todos los permisos** del array `$arrchk` automáticamente
8. **Guarda en** `tbl_usuarios_has_tbl_permisos`

---

## Mejores Prácticas

### ✅ Hacer

- Usar `SessionData::esTipoUsuario()` para validaciones genéricas
- Usar métodos específicos (`investigador()`, `visor()`, `cliente()`) para código más legible
- Validar permisos usando `SessionData::getPermission($id)` para acceso granular a módulos
- Usar `esInvestigadorOAdmin()` para validaciones de análisis avanzado
- Verificar permisos en AMBOS lados: backend (PHP) y frontend (JavaScript)

### ❌ Evitar

- NO comparar directamente `$_SESSION['session_user']['tipo']`
- NO usar strings hardcoded para tipos de usuario
- NO modificar manualmente `tbl_usuarios_has_tbl_permisos` para estos tipos
- NO crear roles personalizados sin actualizar [Usuario.php](admin/classes/Usuario.php)
- NO confiar solo en validaciones de frontend (siempre validar en backend)

---

## Troubleshooting

### Problema 1: Los permisos no se asignan automáticamente

**Solución**: Verificar que el tipo de usuario esté incluido en el array `in_array()` de [Usuario.php](admin/classes/Usuario.php):

```php
// Líneas 322 y 369 aproximadamente
if (in_array($tipo, ["Administrador", "Investigador", "Visor", "Operativo", "Encuestador", "Cliente"])) {
    // Asignación automática
}
```

### Problema 2: Usuario no puede acceder a un módulo

**Diagnóstico**:
1. Verificar permisos en base de datos:
   ```sql
   SELECT COUNT(*) FROM tbl_usuarios_has_tbl_permisos WHERE tbl_usuarios_id = ?
   ```
2. Verificar el ID de permiso correcto en la página
3. Verificar que el módulo valide con `SessionData::getPermission($id)`

### Problema 3: Método SessionData no existe

**Error**: `Call to undefined method SessionData::alcalde()`

**Causa**: Se está usando un método de un rol que ya no existe

**Solución**: Actualizar el código para usar solo los 6 roles válidos:
- `administrador()`
- `investigador()`
- `visor()`
- `operativo()`
- `encuestador()`
- `cliente()`

### Problema 4: Cliente puede ver más de lo permitido

**Diagnóstico**:
1. Verificar que el usuario sea tipo "Cliente"
2. Verificar que solo tenga permisos 38 y 42
3. Verificar que las páginas validen correctamente los permisos

**Solución**: Agregar validaciones adicionales en las páginas:
```php
if (SessionData::cliente()) {
    // Redirigir si intenta acceder a módulo no permitido
    if (!in_array($moduloActual, ['grilla', 'preguntas_grilla'])) {
        header('Location: grilla.php');
        exit;
    }
}
```

---

## Resumen de IDs de Permisos por Módulo

| Módulo | Ver | Crear | Editar | Eliminar |
|--------|-----|-------|--------|----------|
| **Usuarios** | 1 | 2 | 3 | 4 |
| **Partidos Políticos** | 10 | 11 | 12 | 13 |
| **Espacio Geográfico** | 14 | 15 | 16 | 17 |
| **Ficha Técnica Encuesta** | 18 | 19 | 20 | 21 |
| **Personal Político** | 22 | 23 | 24 | 25 |
| **Votantes** | 26 | 27 | 28 | 29 |
| **Preguntas** | 30 | 31 | 32 | 33 |
| **Sondeos** | 34 | 35 | 36 | 37 |
| **Preguntas Grilla** | 38 | 39 | 40 | 41 |
| **Grilla** | 42 | 43 | 44 | 45 |
| **Fórmulas** | 46 | 47 | 48 | 49 |
| **Análisis de Estudio** | 50 | 51 | 52 | 53 |
| **Línea** | 80 | 81 | 82 | - |
| **Estrategia** | 83 | 84 | 85 | - |

---

## Archivos del Sistema de Permisos

- **Definición de Permisos**: [admin/classes/Usuario.php](admin/classes/Usuario.php) (líneas 206-281)
- **Validación de Sesión**: [admin/classes/SessionData.php](admin/classes/SessionData.php) (líneas 118-182)
- **Formulario de Usuarios**: [usuarios.php](usuarios.php) (líneas 85-98)
- **AJAX Router**: [admin/ajax/rqst.php](admin/ajax/rqst.php)
- **JavaScript Usuario**: [admin/js/usuario.js](admin/js/usuario.js)
- **Base de Datos**: `tbl_permisos`, `tbl_usuarios_has_tbl_permisos`

---

## Tabla Resumen de Roles

| Rol | Permisos | Puede Ver | Puede Crear/Editar | Análisis Avanzado |
|-----|----------|-----------|-------------------|-------------------|
| **Administrador** | 85 | ✅ Todo | ✅ Todo | ✅ Sí |
| **Investigador** | 44 | ✅ Módulos principales | ✅ Análisis, estadísticas, datos | ✅ Sí |
| **Visor** | 14 | ✅ Todo | ❌ No | ❌ Solo ver |
| **Operativo** | 31 | ✅ Módulos operativos | ✅ Datos operativos | ❌ No |
| **Encuestador** | 4 | 👥 Solo Votantes | 👥 Solo Votantes | ❌ No |
| **Cliente** | 2 | 📊 Solo Grillas | ❌ No | ❌ No |

---

**Última actualización**: Sistema con 6 roles únicos
**Versión**: 2.0 - Roles simplificados y optimizados
