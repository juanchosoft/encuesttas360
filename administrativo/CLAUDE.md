# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Communication Guidelines

**IMPORTANTE**: Siempre responde en español. Actúa como un experto en las tecnologías de este proyecto (PHP, MariaDB, JavaScript/jQuery, Bootstrap, SVG, sistemas de información geográfica). Proporciona explicaciones técnicas detalladas, mejores prácticas y soluciones optimizadas basadas en los patrones establecidos en este repositorio.

## Project Overview

This is a PHP-based government statistics and electoral data management system with geospatial mapping capabilities. The system tracks political data, surveys (encuestas), polls (sondeos), candidates, political parties, and voter information across Colombian departments and municipalities.

## Technology Stack

- **Backend**: PHP 7+ with PDO for database connections
- **Database**: MariaDB (via Docker container "mariadb")
- **Frontend**: Vanilla JavaScript with jQuery, Chart.js, and interactive SVG maps
- **Admin Panel**: Bootstrap-based admin interface with modular JavaScript controllers
- **Development Environment**: Docker (database: `mariadb`, user: `root`, password: `root123`, database: `estadisticas_db`)

## Architecture

### Core Structure

The application follows a procedural PHP architecture with object-oriented class components:

1. **Entry Points**: Root PHP files (`dashboard.php`, `index.php`, `encuestas.php`, etc.) serve as page controllers
2. **Class Layer**: Business logic in `admin/classes/` (100+ model classes)
3. **AJAX Router**: `admin/ajax/rqst.php` is the central AJAX endpoint handling all client-server communication
4. **Database Layer**: `admin/classes/DbConection.php` provides PDO connection management
5. **Session Management**: `admin/classes/SessionData.php` handles user sessions and permissions
6. **Utility Functions**: `admin/classes/Util.php` contains shared helper methods

### Request Flow

1. User interacts with frontend (e.g., `encuestas.php`)
2. JavaScript controller (e.g., `admin/js/encuestas.js`) sends AJAX request
3. Request routed through `admin/ajax/rqst.php` based on `op` parameter
4. Corresponding class method executes (e.g., `Encuesta::save()`)
5. JSON response returned to client

### Database Connection

Configuration is hardcoded in `admin/classes/DbConection.php`:
- Host: `mariadb` (Docker service name)
- User: `root`
- Password: `root123`
- Database: `estadisticas_db`

### Key Modules

#### Electoral/Political System
- **Encuestas** (Surveys): Technical survey data with sample sizes, sources, dates
- **Sondeos** (Polls): Political polling data
- **Candidatos** (Candidates): Candidate information
- **PartidoPolitico** (Political Parties): Party management
- **Votantes** (Voters): Voter registration and tracking
- **Participantes**: Participants in electoral processes
- **FichaTecnicaEncuesta**: Technical survey specifications
- **Grilla**: Grid/matrix data structures

#### Geographic/Administrative
- **Departamento**: Colombian departments (states)
- **Ciudad**: Municipalities (cities)
- **Vereda**: Rural subdivisions
- **EspacioGeografico**: Geographic spaces

#### Interactive Maps

The system features extensive SVG-based departmental maps located in `admin/mapa_*` directories:
- Each department has its own map folder (e.g., `mapa_antioquia`, `mapa_santander`)
- Maps are interactive SVG files with clickable municipalities
- Generic map utilities: `generic_clases_mapa.php`, `generic_municipios_svg_render.php`
- Color-coded regions based on data thresholds

### Session & Permissions

The system implements role-based access control:
- Roles: `SuperAdministrador`, `Administrador`, `Alcalde`, `Auxiliar_Alcalde`, `Secretario_Despacho`, `Auxiliar`
- Permissions checked via `SessionData::getPermission($id)` with numeric permission IDs
- Each page validates view/create/edit permissions before rendering
- Session user data structure includes: `id`, `nombre`, `apellido`, `tipo`, `permisos[]`, `configuracion`

### JavaScript Controllers

Frontend controllers follow a consistent pattern:
- Initialization on document ready: `$(document).on('ready', init);`
- AJAX calls via utility function: `UTIL.callAjaxRqstPOST(q, callback)`
- Response handling with standard format: `{output: {valid: boolean, response: data}}`
- DataTables integration for data grids
- Chart.js for visualizations

### Styles and CSS

**IMPORTANTE**: Todos los estilos personalizados de la aplicación se encuentran centralizados en:
- **Archivo principal de estilos**: `assets/css/style.css`

Este archivo contiene todos los estilos CSS customizados del proyecto, incluyendo:
- Estilos del sistema de login con fondo de video
- Diseño de dashboard interactivo (cards, banners, iconos)
- Estilos de mapas de visitas departamentales
- Diseño de grillas y tablas de candidatos
- Estilos para toggles y botones personalizados
- Estilos de modales y tabs customizados
- Media queries para diseño responsive

**Patrón de trabajo con CSS:**
- SIEMPRE modificar `assets/css/style.css` para cambios de estilos
- NO crear archivos CSS adicionales a menos que sea absolutamente necesario
- Mantener organización por secciones con comentarios descriptivos
- Incluir media queries para responsividad móvil
- Seguir convenciones de nomenclatura existentes (`.tabla_grilla`, `.toggle-btn`, etc.)

## Common Development Tasks

### Adding a New CRUD Module

1. **Create Model Class** in `admin/classes/`:
   ```php
   class MyModule {
       public static function getAll($rqst) {
           $db = new DbConection();
           $pdo = $db->openConect();
           // Query logic
           $db->closeConect();
           return $arrjson;
       }

       public static function save($rqst) {
           // Validation
           // INSERT or UPDATE logic
           return Util::error_missing_data() or success array
       }
   }
   ```

2. **Add AJAX Routes** in `admin/ajax/rqst.php`:
   ```php
   case 'mymoduleget':
       include '../classes/MyModule.php';
       echo json_encode(MyModule::getAll($rqst));
       break;

   case 'mymodulesave':
       include '../classes/MyModule.php';
       echo json_encode(MyModule::save($rqst));
       break;
   ```

3. **Create JavaScript Controller** in `admin/js/mymodule.js`:
   ```javascript
   const MYMODULE = {
       get: function() {
           const q = { op: 'mymoduleget' };
           UTIL.callAjaxRqstPOST(q, MYMODULE.getHandler);
       },
       save: function() {
           const q = { op: 'mymodulesave', /* fields */ };
           UTIL.callAjaxRqstPOST(q, MYMODULE.saveHandler);
       }
   };
   ```

4. **Create Page** `mymodule.php`:
   - Include `admin/include/head.php` for session/auth
   - Check permissions with `SessionData::getPermission()`
   - Include generic classes with `admin/include/generic_classes.php`
   - Load JavaScript controller

### Database Operations

Standard patterns in model classes:

**SELECT:**
```php
$db = new DbConection();
$pdo = $db->openConect();
$q = "SELECT * FROM " . $db->getTable('tbl_myTable') . " WHERE id = :id";
$stmt = $pdo->prepare($q);
$stmt->execute([':id' => $id]);
$arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
$db->closeConect();
```

**INSERT:**
```php
$q = "INSERT INTO " . $db->getTable('tbl_myTable') . " (field1, field2) VALUES (:field1, :field2)";
$stmt = $pdo->prepare($q);
$stmt->execute([':field1' => $value1, ':field2' => $value2]);
$id = $pdo->lastInsertId();
```

**UPDATE:**
```php
$q = "UPDATE " . $db->getTable('tbl_myTable') . " SET field1 = :field1 WHERE id = :id";
$stmt = $pdo->prepare($q);
$stmt->execute([':field1' => $value1, ':id' => $id]);
```

### Working with Maps

To add or modify departmental maps:
1. SVG maps are in `admin/mapa_[departamento]/`
2. Maps use path elements with `id` attributes matching municipality codes
3. Colors applied via `fill` attribute based on data ranges
4. Use `Util::getColorByPuntaje($puntaje)` for standardized color mapping
5. Generic render logic in `generic_municipios_svg_render.php`

### Error Handling

Use standardized error responses from `Util.php`:
- `Util::error_missing_data()` - Missing required data
- `Util::error_missing_data_description($msg)` - Missing data with custom message
- `Util::error_general($msg)` - General error
- `Util::error_no_result()` - No database results
- `Util::error_user_already_exist()` - Duplicate user
- `Util::error_registroduplicado($msg)` - Duplicate record

Success response format:
```php
return ['output' => ['valid' => true, 'response' => $data]];
```

### Configuration Utilities

Important utility methods in `Util.php`:
- `Util::getDepartamentoPrincipal()` - Returns primary department code ("86" for Putumayo)
- `Util::getCodigoMunicipioPrincipal()` - Returns primary municipality code
- `Util::getAnioActual()` - Current year
- `Util::calcularMargenError($poblacion, $muestra, $valorZ)` - Calculate survey margin of error
- `Util::make_hash_pass($pass)` - Password hashing
- `Util::getUrl()` - Generate current URL

## Database Schema Notes

- Main database: `estadisticas_db`
- Schema files in `admin/db/` (notably `estadisticas_db.sql`)
- Table naming convention: `tbl_[entity]` (e.g., `tbl_encuestas`, `tbl_candidatos`)
- Common fields: `id`, `dtcreate`, `tbl_usuario_id`, `habilitado`
- Geographic tables: `tbl_departamentos`, `tbl_ciudades_accion_unificada`, `tbl_vereda`

## Important Patterns

### Permission Validation
```php
$permissions = [
    'view' => SessionData::getPermission(70),
    'create' => SessionData::getPermission(71),
    'edit' => SessionData::getPermission(72),
];

if (!$permissions['view']) {
    require_once 'permiso_denegado.php';
    exit;
}
```

### AJAX Operation Naming
Format: `[module][action]`
- Examples: `encuestaget`, `encuestasave`, `encuestadelete`
- Get operations: `[module]get`
- Save operations (create/update): `[module]save`
- Delete operations: `[module]delete`

### File Uploads
Use session-based file handling via `$_SESSION['pms_archivo']` with fields:
- `nombrearchivo`, `tipoarchivo`, `contenidooarchivo`, `tamanio`, `error`

## Git Workflow

Current branch: `main` (also the main branch for PRs)

Modified files in current session:
- `admin/classes/DbConection.php`
- `admin/classes/FichaTecnicaEncuesta.php`
- `admin/ajax/rqst.php`
- `admin/js/fichatecnicaencuesta.js`
- `ficha_tecnica_encuesta.php`
- `linea.php`
- `partidos_politicos.php`

## Project-Specific Details

### Department Focus
The system is configured for **Putumayo** department (code "86"):
- Center coordinates: 1.146794, -76.647874
- Principal municipality: 86001
- Configuration in `Util.php` methods starting with `getDepartamento*`

### Color-Coding System
Data visualization uses 5-tier color system:
- Estable (Stable): `#387905` (green) - 0-180 points
- Bajo (Low): `#0041FE` (blue) - 181-360 points
- Medio (Medium): `#FEE300` (yellow) - 361-540 points
- Alto (High): `#F2860D` (orange) - 541-720 points
- Crítico (Critical): `#FC0707` (red) - 721-1000 points

## Testing

No automated test suite is currently present. Test manually through the web interface with different user roles and permissions.

## Dependencies

Key libraries (in `vendors/` and `plugins/`):
- jQuery
- Chart.js
- DataTables
- TinyMCE
- Bootstrap
- Mapael (for maps)
- DateRangePicker
