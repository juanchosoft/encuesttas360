<?php
/**
 * Clase que contiene toda la informacion para utilizar
 * durante una sesion de session_user activo
 */
class SessionData {

    public static function getKey() {
        return 'e1ca41c9c29a354fea64d33228f45503';
    }

    public static function getRandom() {
        if (isset($_SESSION['random'])) {
            $_SESSION['random'] = sha1(rand(100, 2000));
        }
        return $_SESSION['random'];
    }

    public static function getPermission($id) {
        if (isset($_SESSION['session_user'])) {
            $permisos = $_SESSION['session_user']['permisos'];
            return (in_array($id, $permisos));
        } else {
            return false;
        }
    }

    public static function getUserId() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['id'];
        } else {
            return sha1(rand(100, 2000));
        }
    }
    public static function getUserType() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['tipo'];
        }
        return "";
    }

    public static function getKeyUser() {
        if (isset($_SESSION['session_user'])) {
            $userid = $_SESSION['session_user']['id'];
            return md5($userid . SessionData::getKey() . SessionData::getRandom());
        } else {
            return md5(rand(100, 2000));
        }
    }

    public static function getKeyGeneric() {
        return md5(SessionData::getKey() . SessionData::getRandom());
    }

    public static function getUserFullName() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['nombre'] . ' ' . $_SESSION['session_user']['apellido'];
        } else {
            return "";
        }
    }
    public static function getNombreUsuario() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['nombre'];
        } else {
            return "";
        }
    }
    public static function getFotoUsuario() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['img'];
        } else {
            return "";
        }
    }

    public static function getCodigoMunicipio() {
        if (isset($_SESSION['session_user'])) {
            return ($_SESSION['session_user']['tbl_municipio_id']);
        } else {
            return 000;
        }
    }

    public static function getCodigoDepartamento() {
        if (isset($_SESSION['session_user'])) {
            return ($_SESSION['session_user']['tbl_departamento_id']);
        } else {
            return 000;
        }
    }

    public static function getSecretaria() {
        if (isset($_SESSION['session_user'])) {
            return intval($_SESSION['session_user']['tbl_secretarias_id']);
        } else {
            return 000;
        }
    }
    public static function getConfiguracionAplicacion() {
        return isset($_SESSION['session_user']['configuracion'])
            ? intval($_SESSION['session_user']['configuracion'])
            : null;
    }
    public static function getConfiguracionAplicacionLogo() {
        return isset($_SESSION['session_user']['configuracion'])
            ? intval($_SESSION['session_user']['configuracion'][0]['logo'])
            : null;
    }
    public static function getConfiguracionAplicacionSecretaria() {
        return isset($_SESSION['session_user']['configuracion'])
            ? ($_SESSION['session_user']['configuracion'][0]['tbl_secretaria_id'])
            : null;
    }
    public static function getConfiguracionAplicacionDepartamento() {
        return isset($_SESSION['session_user']['configuracion'])
            ? ($_SESSION['session_user']['configuracion'][0]['codigo_departamento'])
            : null;
    }

    public static function getAvatar() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['img'] != "" ? 'assets/img/admin/usuarios/'.$_SESSION['session_user']['img'] : 'dist/img/logoblanco.png';
        }
    }

    /**
     * Valida si el usuario actual es de un tipo específico
     * @param string $tipo Tipo de usuario a validar
     * @return bool
     */
    public static function esTipoUsuario($tipo) {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['tipo'] == $tipo;
        }
        return false;
    }

    /**
     * Obtiene el tipo de usuario actual
     * @return string Tipo de usuario o cadena vacía
     */
    public static function getTipoUsuario() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['tipo'];
        }
        return "";
    }

    // ========== MÉTODOS ESPECÍFICOS POR TIPO DE USUARIO ==========
    // Solo los 6 roles válidos del sistema

    public static function administrador() {
        return self::esTipoUsuario("Administrador");
    }

    public static function superAdministrador() {
        return self::esTipoUsuario("SuperAdministrador");
    }

    public static function investigador() {
        return self::esTipoUsuario("Investigador");
    }

    public static function visor() {
        return self::esTipoUsuario("Visor");
    }

    public static function operativo() {
        return self::esTipoUsuario("Operativo");
    }

    public static function encuestador() {
        return self::esTipoUsuario("Encuestador");
    }

    public static function cliente() {
        return self::esTipoUsuario("Cliente");
    }

    /**
     * Verifica si el usuario tiene un rol administrativo (solo Administrador)
     * @return bool
     */
    public static function esAdministrativo() {
        return self::administrador();
    }

    /**
     * Verifica si el usuario tiene acceso a análisis avanzados (Investigador o Administrador)
     * @return bool
     */
    public static function esInvestigadorOAdmin() {
        return self::investigador() || self::administrador();
    }

    public static function getNombreCaja() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['caja'][0]['codigo'] ;
        } else {
            return "";
        }
    }

    public static function getIdCaja() {
        if (isset($_SESSION['session_user'])) {
            return $_SESSION['session_user']['caja'][0]['id'];
        } else {
            return "";
        }
    }

    public static function getAvatarGeneric() {
        return 'dist/img/user.svg';
    }

    public static function getImageProduct($img) {
        if ($img !="" && file_exists("assets/img/admin/" . $img)) {
            return 'assets/img/admin/'.$img;
        }else{
            return 'assets/img/spider-logo.jpg';
        }
    }

    /**
     * CONFIGURACION DEL SISTEMA DE VARIABLES IMPORTANTES
     */

    public static function getConfigSistema() {
        return isset($_SESSION['session_user']) ? $_SESSION['session_user']['config'][0] : "";
    }

    public static function getConfigPrecioProd() {
        return isset($_SESSION['session_user']) ? $_SESSION['session_user']['config'][0]['config_precio_productos'] : "1";
    }

    public static function getConfigImpresionPOS() {
        return isset($_SESSION['session_user']) && $_SESSION['session_user']['config'][0]['impresion_termica'] == 'si'
        ? 'si' : 'no';
    }
}
