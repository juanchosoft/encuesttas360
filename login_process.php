<?php
/**
 * Fase B — Login ciudadano desactivado.
 */
header('Content-Type: application/json; charset=utf-8');
http_response_code(403);
echo json_encode([
  'status' => 'error',
  'message' => 'El acceso con cuenta ciudadana está desactivado. Puedes participar desde el inicio.',
  'redirect' => 'index.php',
], JSON_UNESCAPED_UNICODE);
exit;
