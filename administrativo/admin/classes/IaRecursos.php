<?php

require_once __DIR__ . '/DbConection.php';

class IaRecursos
{
    private const MAX_BYTES = 8388608;
    private const MAX_LADO = 2600;
    private const TIPOS_VALIDOS = ['logo', 'foto', 'otro'];

    public static function directorio(): string
    {
        $dir = __DIR__ . '/../uploads/ia_recursos/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!is_dir($dir . 'cache/')) {
            @mkdir($dir . 'cache/', 0755, true);
        }
        $ht = $dir . '.htaccess';
        if (!is_file($ht)) {
            @file_put_contents($ht, "Options -Indexes\n<FilesMatch \"\\.(php|phtml|phar)$\">\n  Require all denied\n</FilesMatch>\n");
        }
        return $dir;
    }

    public static function guardarArchivoSubido(int $userId, array $archivo, string $tipo = 'foto'): array
    {
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($archivo['tmp_name'] ?? '')) {
            throw new RuntimeException('No se pudo recibir la imagen.');
        }
        if ((int) $archivo['size'] > self::MAX_BYTES) {
            throw new RuntimeException('La imagen supera el máximo de 8 MB.');
        }
        $bytes = (string) file_get_contents($archivo['tmp_name']);
        return self::guardarBytes($userId, $bytes, (string) ($archivo['name'] ?? 'imagen'), $tipo, null);
    }

    public static function guardarBytes(int $userId, string $bytes, string $nombre, string $tipo, ?string $origen): array
    {
        if ($bytes === '' || strlen($bytes) > self::MAX_BYTES) {
            throw new RuntimeException('La imagen está vacía o supera el máximo de 8 MB.');
        }
        $info = @getimagesizefromstring($bytes);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true)) {
            throw new RuntimeException('El archivo no es una imagen válida (se aceptan JPG, PNG, GIF y WEBP).');
        }
        $img = @imagecreatefromstring($bytes);
        if (!$img) {
            throw new RuntimeException('No fue posible leer la imagen.');
        }
        $w = imagesx($img);
        $h = imagesy($img);
        if (max($w, $h) > self::MAX_LADO) {
            $esc = self::MAX_LADO / max($w, $h);
            $nuevo = imagescale($img, max(1, (int) round($w * $esc)), max(1, (int) round($h * $esc)), IMG_BICUBIC);
            imagedestroy($img);
            $img = $nuevo;
            $w = imagesx($img);
            $h = imagesy($img);
        }
        $conAlfa = $info[2] !== IMAGETYPE_JPEG;
        imagesavealpha($img, true);
        ob_start();
        if ($conAlfa) {
            imagepng($img, null, 6);
            $ext = 'png';
            $mime = 'image/png';
        } else {
            imagejpeg($img, null, 90);
            $ext = 'jpg';
            $mime = 'image/jpeg';
        }
        $salida = (string) ob_get_clean();
        imagedestroy($img);

        $tipo = in_array($tipo, self::TIPOS_VALIDOS, true) ? $tipo : 'foto';
        $nombre = mb_substr(trim(preg_replace('/[^\p{L}\p{N} ._-]+/u', '', pathinfo($nombre, PATHINFO_FILENAME))), 0, 120);
        $nombre = $nombre !== '' ? $nombre : 'imagen';
        $hash = sha1($salida);

        $db = new DbConection();
        $pdo = $db->openConect();
        try {
            $t = $db->getTable('tbl_ia_recursos');
            $stmt = $pdo->prepare("SELECT * FROM $t WHERE tbl_usuario_id = :u AND hash_sha1 = :h LIMIT 1");
            $stmt->execute([':u' => $userId, ':h' => $hash]);
            $existente = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existente && is_file(self::directorio() . $existente['ruta'])) {
                return self::publico($existente);
            }
            $archivo = $userId . '_' . $hash . '.' . $ext;
            if (file_put_contents(self::directorio() . $archivo, $salida) === false) {
                throw new RuntimeException('No fue posible guardar la imagen en el servidor.');
            }
            $ins = $pdo->prepare("INSERT INTO $t (tbl_usuario_id, nombre, tipo, mime, ruta, ancho, alto, tamano, hash_sha1, origen, dt_create)
                                  VALUES (:u, :n, :t, :m, :r, :w, :h, :s, :hs, :o, NOW())");
            $ins->execute([':u' => $userId, ':n' => $nombre, ':t' => $tipo, ':m' => $mime, ':r' => $archivo, ':w' => $w, ':h' => $h, ':s' => strlen($salida), ':hs' => $hash, ':o' => $origen !== null ? mb_substr($origen, 0, 500) : null]);
            $id = (int) $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM $t WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return self::publico($stmt->fetch(PDO::FETCH_ASSOC));
        } finally {
            $db->closeConect();
        }
    }

    public static function descargarUrl(int $userId, string $url, string $nombre = '', string $tipo = 'foto'): array
    {
        $partes = parse_url(trim($url));
        if (!$partes || strtolower($partes['scheme'] ?? '') !== 'https' || empty($partes['host']) || isset($partes['user']) || (isset($partes['port']) && (int) $partes['port'] !== 443)) {
            throw new RuntimeException('Solo se permiten enlaces https públicos.');
        }
        $bytes = self::descargarSeguro(trim($url), 0);
        if ($nombre === '') {
            $nombre = pathinfo((string) ($partes['path'] ?? ''), PATHINFO_FILENAME) ?: 'imagen';
        }
        return self::guardarBytes($userId, $bytes, $nombre, $tipo, trim($url));
    }

    private static function ipsPublicas(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = [$host];
        } else {
            $ips = gethostbynamel($host) ?: [];
        }
        if (!$ips) {
            throw new RuntimeException('No se pudo resolver el servidor de la imagen.');
        }
        foreach ($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                throw new RuntimeException('El enlace apunta a una dirección no permitida.');
            }
        }
        return $ips;
    }

    private static function descargarSeguro(string $url, int $saltos): string
    {
        if ($saltos > 3) {
            throw new RuntimeException('El enlace redirige demasiadas veces.');
        }
        $p = parse_url($url);
        if (!$p || strtolower($p['scheme'] ?? '') !== 'https' || empty($p['host'])) {
            throw new RuntimeException('Solo se permiten enlaces https públicos.');
        }
        $host = $p['host'];
        $ips = self::ipsPublicas($host);

        $cabeceras = [];
        $cuerpo = '';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_RESOLVE => [$host . ':443:' . $ips[0]],
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; Estadisticas360/1.0)',
            CURLOPT_HTTPHEADER => ['Accept: image/avif,image/webp,image/png,image/jpeg,*/*;q=0.8'],
            CURLOPT_HEADERFUNCTION => static function ($c, $linea) use (&$cabeceras) {
                if (strpos($linea, ':') !== false) {
                    [$k, $v] = explode(':', $linea, 2);
                    $cabeceras[strtolower(trim($k))] = trim($v);
                }
                return strlen($linea);
            },
            CURLOPT_WRITEFUNCTION => static function ($c, $datos) use (&$cuerpo) {
                $cuerpo .= $datos;
                return strlen($cuerpo) > self::MAX_BYTES ? 0 : strlen($datos);
            },
        ]);
        $caBundle = null;
        if (class_exists('Composer\CaBundle\CaBundle')) {
            $caBundle = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
        } elseif (is_file(__DIR__ . '/../../vendor/composer/ca-bundle/res/cacert.pem')) {
            $caBundle = __DIR__ . '/../../vendor/composer/ca-bundle/res/cacert.pem';
        }
        if ($caBundle) {
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }
        curl_exec($ch);
        $codigo = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch);
        curl_close($ch);

        if ($codigo >= 300 && $codigo < 400 && !empty($cabeceras['location'])) {
            $destino = $cabeceras['location'];
            if (strpos($destino, '//') === 0) {
                $destino = 'https:' . $destino;
            } elseif ($destino[0] === '/') {
                $destino = 'https://' . $host . $destino;
            }
            return self::descargarSeguro($destino, $saltos + 1);
        }
        if ($error !== 0 && strlen($cuerpo) <= self::MAX_BYTES) {
            throw new RuntimeException('No fue posible descargar la imagen (error de red ' . $error . ').');
        }
        if (strlen($cuerpo) > self::MAX_BYTES) {
            throw new RuntimeException('La imagen supera el máximo de 8 MB.');
        }
        if ($codigo !== 200) {
            throw new RuntimeException('El servidor de la imagen respondió con el código ' . $codigo . '.');
        }
        return $cuerpo;
    }

    private static function publico(array $f): array
    {
        return [
            'id' => (int) $f['id'],
            'nombre' => $f['nombre'],
            'tipo' => $f['tipo'],
            'ancho' => (int) $f['ancho'],
            'alto' => (int) $f['alto'],
            'origen' => $f['origen'],
        ];
    }

    public static function cargar(int $id, int $userId, bool $esAdmin): ?array
    {
        if ($id <= 0) {
            return null;
        }
        $db = new DbConection();
        $pdo = $db->openConect();
        $stmt = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ia_recursos') . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $f = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConect();
        if (!$f || (!$esAdmin && (int) $f['tbl_usuario_id'] !== $userId)) {
            return null;
        }
        $f['ruta_completa'] = self::directorio() . $f['ruta'];
        return is_file($f['ruta_completa']) ? $f : null;
    }

    public static function listar(int $userId, bool $esAdmin): array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "SELECT * FROM " . $db->getTable('tbl_ia_recursos') . ($esAdmin ? '' : ' WHERE tbl_usuario_id = :u') . " ORDER BY id DESC LIMIT 60";
        $stmt = $pdo->prepare($q);
        $stmt->execute($esAdmin ? [] : [':u' => $userId]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->closeConect();
        return array_map([self::class, 'publico'], $filas);
    }

    public static function imagenGd(string $ruta)
    {
        $img = @imagecreatefromstring((string) file_get_contents($ruta));
        if ($img) {
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }
        return $img ?: null;
    }

    private static function tieneTransparencia($img): bool
    {
        $w = imagesx($img);
        $h = imagesy($img);
        for ($y = 0; $y < $h; $y += max(1, intdiv($h, 24))) {
            for ($x = 0; $x < $w; $x += max(1, intdiv($w, 24))) {
                if (((imagecolorat($img, $x, $y) >> 24) & 0x7F) >= 40) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function luminancia(int $c): float
    {
        return 0.299 * (($c >> 16) & 255) + 0.587 * (($c >> 8) & 255) + 0.114 * ($c & 255);
    }

    private static function logoConAlfa(array $recurso)
    {
        $src = self::imagenGd($recurso['ruta_completa']);
        if (!$src || self::tieneTransparencia($src)) {
            return $src;
        }
        if (max(imagesx($src), imagesy($src)) > 900) {
            $esc = imagescale($src, 900, -1, IMG_BICUBIC);
            imagedestroy($src);
            $src = $esc;
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $parche = max(2, (int) round(min($w, $h) * 0.03));
        $esquinas = [[0, 0], [$w - $parche, 0], [0, $h - $parche], [$w - $parche, $h - $parche]];
        $sr = $sg = $sb = 0.0;
        $n = 0;
        foreach ($esquinas as [$ex, $ey]) {
            for ($y = $ey; $y < $ey + $parche; $y++) {
                for ($x = $ex; $x < $ex + $parche; $x++) {
                    $c = imagecolorat($src, $x, $y);
                    $sr += ($c >> 16) & 255;
                    $sg += ($c >> 8) & 255;
                    $sb += $c & 255;
                    $n++;
                }
            }
        }
        $br = $sr / max(1, $n);
        $bg = $sg / max(1, $n);
        $bb = $sb / max(1, $n);
        $dist = [];
        $distancias = [];
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($src, $x, $y);
                $d = sqrt((((($c >> 16) & 255) - $br) ** 2 + ((($c >> 8) & 255) - $bg) ** 2 + (($c & 255) - $bb) ** 2));
                $distancias[] = (int) $d;
                $dist[$y * $w + $x] = $d;
            }
        }
        sort($distancias);
        $rango = max(25.0, (float) $distancias[(int) (count($distancias) * 0.985)]);

        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($src, $x, $y);
                $a = max(0.0, min(1.0, $dist[$y * $w + $x] / $rango));
                $a = $a ** 0.72;
                if ($a < 0.13) {
                    imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, 0, 0, 0, 127));
                    continue;
                }
                imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, ($c >> 16) & 255, ($c >> 8) & 255, $c & 255, (int) round(127 * (1 - $a))));
            }
        }
        imagedestroy($src);
        return $out;
    }

    public static function rutaLogo(array $recurso, string $variante): string
    {
        if ($variante === 'original') {
            return self::recortarTransparente($recurso, 'original');
        }
        $color = $variante === 'blanco' ? 255 : 0;
        $dest = self::directorio() . 'cache/' . $recurso['id'] . '_' . $recurso['hash_sha1'] . '_v2_' . $variante . '.png';
        if (is_file($dest)) {
            return $dest;
        }
        $src = self::logoConAlfa($recurso);
        $w = imagesx($src);
        $h = imagesy($src);
        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, $color, $color, $color, 127));
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $a = (imagecolorat($src, $x, $y) >> 24) & 0x7F;
                if ($a < 127) {
                    imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $color, $color, $color, $a));
                }
            }
        }
        imagedestroy($src);
        $recorte = imagecropauto($out, IMG_CROP_TRANSPARENT);
        if ($recorte) {
            imagedestroy($out);
            $out = $recorte;
            imagesavealpha($out, true);
        }
        imagepng($out, $dest, 6);
        imagedestroy($out);
        return $dest;
    }

    private static function recortarTransparente(array $recurso, string $variante): string
    {
        $dest = self::directorio() . 'cache/' . $recurso['id'] . '_' . $recurso['hash_sha1'] . '_v2_' . $variante . '.png';
        if (is_file($dest)) {
            return $dest;
        }
        $src = self::logoConAlfa($recurso);
        $recorte = imagecropauto($src, IMG_CROP_TRANSPARENT);
        $final = $recorte ?: $src;
        imagesavealpha($final, true);
        imagepng($final, $dest, 6);
        return $dest;
    }

    public static function rutaFondo(array $recurso, int $anchoPx, int $altoPx, int $brillo): string
    {
        $brillo = max(5, min(100, $brillo));
        $dest = self::directorio() . 'cache/' . $recurso['id'] . '_' . $recurso['hash_sha1'] . "_f{$anchoPx}x{$altoPx}_{$brillo}.jpg";
        if (is_file($dest)) {
            return $dest;
        }
        $src = self::imagenGd($recurso['ruta_completa']);
        $sw = imagesx($src);
        $sh = imagesy($src);
        $objetivo = $anchoPx / $altoPx;
        if ($sw / $sh > $objetivo) {
            $cw = (int) round($sh * $objetivo);
            $cx = (int) round(($sw - $cw) / 2);
            $cy = 0;
            $ch = $sh;
        } else {
            $cw = $sw;
            $ch = (int) round($sw / $objetivo);
            $cx = 0;
            $cy = (int) round(($sh - $ch) / 2);
        }
        $out = imagecreatetruecolor($anchoPx, $altoPx);
        imagefill($out, 0, 0, imagecolorallocate($out, 0, 0, 0));
        imagecopyresampled($out, $src, 0, 0, $cx, $cy, $anchoPx, $altoPx, $cw, $ch);
        imagedestroy($src);
        imagealphablending($out, true);
        $alfa = (int) round(127 * ($brillo / 100));
        imagefilledrectangle($out, 0, 0, $anchoPx, $altoPx, imagecolorallocatealpha($out, 0, 0, 0, $alfa));
        imagejpeg($out, $dest, 88);
        imagedestroy($out);
        return $dest;
    }

    public static function paleta(array $recurso): array
    {
        $src = self::logoConAlfa($recurso);
        $pequena = imagescale($src, 64, -1, IMG_BILINEAR_FIXED);
        imagedestroy($src);
        $w = imagesx($pequena);
        $h = imagesy($pequena);
        $suma = [0, 0, 0];
        $n = 0;
        $mejor = null;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($pequena, $x, $y);
                if ((($c >> 24) & 0x7F) > 40) {
                    continue;
                }
                $r = ($c >> 16) & 255;
                $g = ($c >> 8) & 255;
                $b = $c & 255;
                $max = max($r, $g, $b);
                $min = min($r, $g, $b);
                if ($max > 235 && $min > 215) {
                    continue;
                }
                $suma[0] += $r;
                $suma[1] += $g;
                $suma[2] += $b;
                $n++;
                $sat = $max > 0 ? ($max - $min) / $max : 0;
                if ($sat > 0.12 && $max > 70 && ($mejor === null || $sat * $max > $mejor[3])) {
                    $mejor = [$r, $g, $b, $sat * $max];
                }
            }
        }
        imagedestroy($pequena);
        $base = $n > 0 ? [(int) ($suma[0] / $n), (int) ($suma[1] / $n), (int) ($suma[2] / $n)] : [60, 66, 80];
        $oscuro = self::mezclar($base, [20, 24, 36], 0.72);
        $acento = self::hslForzado($mejor ? [$mejor[0], $mejor[1], $mejor[2]] : [90, 100, 160], 0.5, 0.5);
        return [
            'primario' => self::hex($oscuro),
            'secundario' => self::hex(self::mezclar($oscuro, [255, 255, 255], 0.28)),
            'acento' => self::hex($acento),
            'suave' => self::hex(self::mezclar($acento, [255, 255, 255], 0.86)),
            'texto' => '#2E3440',
        ];
    }

    private static function mezclar(array $a, array $b, float $t): array
    {
        return [(int) round($a[0] + ($b[0] - $a[0]) * $t), (int) round($a[1] + ($b[1] - $a[1]) * $t), (int) round($a[2] + ($b[2] - $a[2]) * $t)];
    }

    private static function hslForzado(array $c, float $satMin, float $luz): array
    {
        [$r, $g, $b] = [$c[0] / 255, $c[1] / 255, $c[2] / 255];
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $d = $max - $min;
        if ($d == 0) {
            $h = 0.72;
        } elseif ($max == $r) {
            $h = fmod(($g - $b) / $d, 6) / 6;
        } elseif ($max == $g) {
            $h = (($b - $r) / $d + 2) / 6;
        } else {
            $h = (($r - $g) / $d + 4) / 6;
        }
        $h = $h < 0 ? $h + 1 : $h;
        $l1 = ($max + $min) / 2;
        $s1 = $d == 0 ? 0 : $d / (1 - abs(2 * $l1 - 1));
        $s = max($s1, $satMin);
        $q = $luz < 0.5 ? $luz * (1 + $s) : $luz + $s - $luz * $s;
        $p = 2 * $luz - $q;
        $canal = static function (float $t) use ($p, $q): int {
            $t = $t < 0 ? $t + 1 : ($t > 1 ? $t - 1 : $t);
            $v = $t < 1 / 6 ? $p + ($q - $p) * 6 * $t : ($t < 1 / 2 ? $q : ($t < 2 / 3 ? $p + ($q - $p) * (2 / 3 - $t) * 6 : $p));
            return (int) round($v * 255);
        };
        return [$canal($h + 1 / 3), $canal($h), $canal($h - 1 / 3)];
    }

    public static function hex(array $c): string
    {
        return sprintf('#%02X%02X%02X', $c[0], $c[1], $c[2]);
    }
}
