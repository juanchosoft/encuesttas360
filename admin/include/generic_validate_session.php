<?php
/**
 * Validación de sesión ciudadana.
 * Fase B: el funnel público NO exige session_user.
 */

ini_set('session.cache_expire', 200000);
ini_set('session.cache_limiter', 'none');
ini_set('session.cookie_lifetime', 28800);
ini_set('session.gc_maxlifetime', 200000);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Páginas / AJAX públicos (participación sin login)
$script = basename((string)($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? ''));
$publicScripts = [
  'index.php',
  'registro.php',
  'encuesta.php',
  'sondeo_new.php',
  'sondeo.php',
  'agradecimiento.php',
  'rqst.php',
  'login.php',
  'login_process.php',
  'dash_responder.php',
  'resultado.php',
  'politica.php',
  'nosotros.php',
  'contacto.php',
  'logout.php',
];

$isPublic =
  isset($_REQUEST['route_map']) ||
  in_array($script, $publicScripts, true);

if (!isset($_SESSION['session_user']) && !$isPublic) {
  header('Location: logout.php');
  exit;
}
