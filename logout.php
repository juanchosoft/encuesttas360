<?php
/**
 * Cierra sesión y vuelve al inicio público (sin bucle).
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'] ?? '/', $p['domain'] ?? '', !empty($p['secure']), !empty($p['httponly']));
}
session_destroy();
header('Location: index.php');
exit;
