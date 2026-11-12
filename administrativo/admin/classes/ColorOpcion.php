<?php

class ColorOpcion
{
    public const PALETA = [
        '#1F77B4', '#FF7F0E', '#2CA02C', '#D62728', '#9467BD', '#8C564B', '#E377C2', '#17BECF',
        '#BCBD22', '#393B79', '#E6AB02', '#1B9E77', '#A6761D', '#E7298A', '#66A61E', '#D95F02',
        '#7570B3', '#3182BD', '#31A354', '#CE6DBD', '#843C39', '#637939', '#0B7285', '#8E24AA',
    ];

    public const SIN_DATO = '#C9CED6';

    private static $existeColumna = null;

    public static function disponible(PDO $pdo, DbConection $db): bool
    {
        if (self::$existeColumna === null) {
            $stmt = $pdo->query("SHOW COLUMNS FROM " . $db->getTable('tbl_opciones_respuesta') . " LIKE 'color'");
            self::$existeColumna = $stmt && $stmt->fetch(PDO::FETCH_NUM) !== false;
        }
        return self::$existeColumna;
    }

    public static function completarPregunta(PDO $pdo, DbConection $db, int $preguntaId): int
    {
        if ($preguntaId <= 0 || !self::disponible($pdo, $db)) {
            return 0;
        }
        $t = $db->getTable('tbl_opciones_respuesta');
        $stmt = $pdo->prepare("SELECT id, color FROM {$t} WHERE tbl_pregunta_id = :p ORDER BY COALESCE(eliminado, 0), COALESCE(orden, 999999), id");
        $stmt->execute([':p' => $preguntaId]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $uso = array_fill_keys(self::PALETA, 0);
        foreach ($filas as $f) {
            $c = strtoupper((string) $f['color']);
            if (isset($uso[$c])) {
                $uso[$c]++;
            }
        }
        $upd = $pdo->prepare("UPDATE {$t} SET color = :c WHERE id = :id");
        $n = 0;
        foreach ($filas as $f) {
            if ($f['color'] !== null && $f['color'] !== '') {
                continue;
            }
            $color = array_search(min($uso), $uso, true);
            $uso[$color]++;
            $upd->execute([':c' => $color, ':id' => (int) $f['id']]);
            $n++;
        }
        return $n;
    }

    public static function completarTodas(PDO $pdo, DbConection $db): int
    {
        if (!self::disponible($pdo, $db)) {
            return 0;
        }
        $t = $db->getTable('tbl_opciones_respuesta');
        $ids = $pdo->query("SELECT DISTINCT tbl_pregunta_id FROM {$t} WHERE color IS NULL OR color = ''")->fetchAll(PDO::FETCH_COLUMN);
        $n = 0;
        foreach ($ids as $id) {
            $n += self::completarPregunta($pdo, $db, (int) $id);
        }
        return $n;
    }

    public static function mapaPregunta(PDO $pdo, DbConection $db, int $preguntaId): array
    {
        $t = $db->getTable('tbl_opciones_respuesta');
        if (!self::disponible($pdo, $db)) {
            return [];
        }
        self::completarPregunta($pdo, $db, $preguntaId);
        $stmt = $pdo->prepare("SELECT id, color FROM {$t} WHERE tbl_pregunta_id = :p");
        $stmt->execute([':p' => $preguntaId]);
        $mapa = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $f) {
            $mapa[(string) $f['id']] = $f['color'] ?: self::SIN_DATO;
        }
        return $mapa;
    }
}
