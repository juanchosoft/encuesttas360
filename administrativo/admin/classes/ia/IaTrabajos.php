<?php

class IaTrabajos
{
    private static function directorio(): string
    {
        $dir = __DIR__ . '/../../uploads/ia_tmp/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function tomarCandado(int $userId)
    {
        $f = @fopen(self::directorio() . 'candado_usuario_' . $userId . '.lock', 'c');
        if (!$f) {
            return false;
        }
        if (!flock($f, LOCK_EX | LOCK_NB)) {
            fclose($f);
            return null;
        }
        return $f;
    }

    public static function liberarCandado($f): void
    {
        if (is_resource($f)) {
            flock($f, LOCK_UN);
            fclose($f);
        }
    }

    private static function ruta(string $id): string
    {
        return self::directorio() . 'trabajo_' . preg_replace('/[^a-f0-9]/', '', $id) . '.json';
    }

    public static function crear(int $userId): string
    {
        foreach (glob(self::directorio() . 'trabajo_*.json') ?: [] as $viejo) {
            if (@filemtime($viejo) < time() - 86400) {
                @unlink($viejo);
            }
        }
        $id = bin2hex(random_bytes(12));
        self::escribir($id, ['estado' => 'ejecutando', 'usuario' => $userId, 'inicio' => time()]);
        return $id;
    }

    public static function escribir(string $id, array $datos): void
    {
        $actual = self::leer($id) ?? [];
        file_put_contents(self::ruta($id), json_encode(array_merge($actual, $datos), JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public static function leer(string $id): ?array
    {
        $ruta = self::ruta($id);
        if (!is_file($ruta)) {
            return null;
        }
        $d = json_decode((string) file_get_contents($ruta), true);
        return is_array($d) ? $d : null;
    }

    public static function cerrarConexion(string $contenido): void
    {
        header('Connection: close');
        header('Content-Length: ' . strlen($contenido));
        echo $contenido;
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        }
    }
}
