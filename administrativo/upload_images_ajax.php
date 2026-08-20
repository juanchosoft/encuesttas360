<?php
declare(strict_types=1);

session_start();

header('Content-Type: text/plain; charset=utf-8');

// =============================
// CONFIG
// =============================
$uploaddir = __DIR__ . '/assets/img/admin/';      // ruta física
$publicDir = 'assets/img/admin/';                // ruta pública (la que devolvemos)

$maxBytes  = 2 * 1024 * 1024; // 2MB recomendado (puedes subirlo a 5MB si quieres)
$allowedExt = ['jpg', 'jpeg', 'png', 'bmp'];
$allowedMime = [
  'image/jpeg',
  'image/png',
  'image/bmp',
  'image/x-ms-bmp',
];

// Inicializa sesión
$_SESSION['file']["nombrearchivo"] = "";

// =============================
// VALIDACIONES BÁSICAS
// =============================
if (!isset($_FILES['userfile']) || !is_array($_FILES['userfile'])) {
  http_response_code(400);
  echo "";
  exit;
}

$f = $_FILES['userfile'];

if (!empty($f['error'])) {
  http_response_code(400);
  echo "";
  exit;
}

if (empty($f['tmp_name']) || !is_uploaded_file($f['tmp_name'])) {
  http_response_code(400);
  echo "";
  exit;
}

if (!isset($f['size']) || (int)$f['size'] <= 0) {
  http_response_code(400);
  echo "";
  exit;
}

if ((int)$f['size'] > $maxBytes) {
  http_response_code(413); // Payload Too Large
  echo "";
  exit;
}

// =============================
// CREA DIRECTORIO SI NO EXISTE
// =============================
if (!is_dir($uploaddir)) {
  // 0755 recomendado; recursive true por si falta la ruta
  if (!mkdir($uploaddir, 0755, true) && !is_dir($uploaddir)) {
    http_response_code(500);
    echo "";
    exit;
  }
}

// =============================
// EXTENSIÓN Y MIME REAL
// =============================
$originalName = (string)($f['name'] ?? '');
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if ($ext === 'jpeg') $ext = 'jpg';

if (!in_array($ext, $allowedExt, true)) {
  http_response_code(415);
  echo "";
  exit;
}

// Detecta MIME real
$mime = '';
if (function_exists('finfo_open')) {
  $fi = finfo_open(FILEINFO_MIME_TYPE);
  if ($fi) {
    $mime = (string)finfo_file($fi, $f['tmp_name']);
    finfo_close($fi);
  }
}

if ($mime === '' || !in_array($mime, $allowedMime, true)) {
  // Segundo intento con getimagesize
  $imgInfo = @getimagesize($f['tmp_name']);
  if (!$imgInfo || empty($imgInfo['mime']) || !in_array($imgInfo['mime'], $allowedMime, true)) {
    http_response_code(415);
    echo "";
    exit;
  }
  $mime = (string)$imgInfo['mime'];
}

// =============================
// GENERA NOMBRE SEGURO ÚNICO
// =============================
$rand = bin2hex(random_bytes(10)); // 20 chars
$hash = hash('sha256', $originalName . '|' . (string)time() . '|' . $rand);
$filename = substr($hash, 0, 22) . '_' . $rand . '.' . $ext;

$uploadfile = $uploaddir . $filename;
$publicPath = $publicDir . $filename;

// =============================
// MUEVE ARCHIVO
// =============================
if (!move_uploaded_file($f['tmp_name'], $uploadfile)) {
  http_response_code(500);
  echo "";
  exit;
}

// Ajusta permisos del archivo (opcional)
@chmod($uploadfile, 0644);

// =============================
// GUARDA EN SESIÓN (compatibilidad con tu flujo actual)
// =============================
$_SESSION['file']["nombrearchivo"] = $filename;

// Guardar binario si lo necesitas después (ojo: sesión puede crecer; pero lo dejo como lo tenías)
$contenido_binario = @file_get_contents($uploadfile);
if ($contenido_binario !== false) {
  $_SESSION['file']["contenidooarchivo"] = $contenido_binario;
  $_SESSION['file']["tipoarchivo"] = $mime;
  $_SESSION['file']["tamanio"] = (int)$f['size'];
}

// =============================
// RESPUESTA: SOLO RUTA PÚBLICA
// =============================
echo $publicPath;
exit;
