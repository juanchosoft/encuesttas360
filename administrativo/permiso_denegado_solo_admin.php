<?php
// Validar permisos de usuario administrador
$userType = SessionData::getUserType();
$isAdmin = ($userType === Util::Administrador() || $userType === Util::SuperAdministrador());
if (!$isAdmin) {
  require 'permiso_denegado.php';
}