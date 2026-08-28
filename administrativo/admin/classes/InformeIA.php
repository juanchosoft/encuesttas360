<?php

class InformeIA
{
    private const TAGS_PERMITIDAS = '<h1><h2><h3><h4><p><div><span><table><thead><tbody><tr><th><td>'
        . '<ul><ol><li><b><strong><i><em><small><blockquote><br><hr>';
    private const MAX_CONTENIDO = 60000;

    /**
     * Vocabulario cerrado de clases visuales que el HTML generado puede usar.
     * El CSS real de cada clase vive en informes_ia.js (vista) y en
     * ia_informe_pdf.php (PDF, subconjunto compatible con TCPDF) — nunca en
     * el HTML que llega aquí. Cualquier otra clase, o cualquier otro
     * atributo (style, id, on*, data-*), se descarta.
     */
    private const CLASES_PERMITIDAS = [
        's360-kpis', 's360-kpi', 's360-kpi-value', 's360-kpi-label', 's360-kpi-trend',
        's360-callout', 's360-callout-info', 's360-callout-warning', 's360-callout-danger', 's360-callout-success',
        's360-badge', 's360-badge-dato', 's360-badge-interpretacion',
        's360-quote', 's360-section', 's360-lead', 's360-table-highlight', 's360-cite',
    ];

    public static function sanitizarHtml(string $html): string
    {
        if (mb_strlen($html) > self::MAX_CONTENIDO) {
            $html = mb_substr($html, 0, self::MAX_CONTENIDO);
        }
        $html = strip_tags($html, self::TAGS_PERMITIDAS);
        return self::sanitizarAtributos($html);
    }

    private static function sanitizarAtributos(string $html): string
    {
        $resultado = preg_replace_callback(
            '/<(\/?)([a-zA-Z0-9]+)((?:\s+[a-zA-Z-]+(?:\s*=\s*"[^"]*")?)*)\s*(\/?)>/',
            function (array $m): string {
                [, $cierre, $tag, $atributos, $autocierre] = $m;

                if ($cierre === '/' || trim($atributos) === '') {
                    return "<{$cierre}{$tag}{$autocierre}>";
                }

                if (preg_match('/class\s*=\s*"([^"]*)"/i', $atributos, $mc)) {
                    $clases = array_values(array_intersect(preg_split('/\s+/', trim($mc[1])), self::CLASES_PERMITIDAS));
                    if (!empty($clases)) {
                        return '<' . $tag . ' class="' . implode(' ', $clases) . '"' . $autocierre . '>';
                    }
                }

                return "<{$tag}{$autocierre}>";
            },
            $html
        );

        return $resultado ?? '';
    }

    public static function crear(int $userId, string $titulo, string $contenidoHtml): int
    {
        $titulo = mb_substr(trim($titulo), 0, 150);
        $html = self::sanitizarHtml($contenidoHtml);

        $db = new DbConection();
        $pdo = $db->openConect();
        $q = "INSERT INTO " . $db->getTable('tbl_ia_informes') . " (tbl_usuario_id, titulo, contenido_html, dt_create)
              VALUES (:usuario, :titulo, :html, NOW())";
        $stmt = $pdo->prepare($q);
        $stmt->execute([':usuario' => $userId, ':titulo' => $titulo, ':html' => $html]);
        $id = (int) $pdo->lastInsertId();
        $db->closeConect();
        return $id;
    }

    public static function getAll($rqst)
    {
        $userId = SessionData::getUserId();
        $verTodos = SessionData::superAdministrador() || SessionData::hasPermission('ia.informes.manage');

        $db = new DbConection();
        $pdo = $db->openConect();

        $q = "SELECT i.id, i.titulo, i.tbl_usuario_id, i.dt_create,
                     CONCAT(u.nombre, ' ', u.apellido) AS autor
              FROM " . $db->getTable('tbl_ia_informes') . " i
              LEFT JOIN " . $db->getTable('tbl_usuarios') . " u ON u.id = i.tbl_usuario_id";
        $params = [];

        if (!$verTodos) {
            $q .= " WHERE i.tbl_usuario_id = :usuario";
            $params[':usuario'] = $userId;
        }

        $q .= " ORDER BY i.dt_create DESC";

        try {
            $stmt = $pdo->prepare($q);
            $stmt->execute($params);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arrjson = ['output' => ['valid' => true, 'response' => $arr ?: []]];
        } catch (PDOException $e) {
            $arrjson = Util::error_general('Al obtener el listado de informes.');
        } finally {
            $db->closeConect();
        }
        return $arrjson;
    }

    public static function getById($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $informe = self::cargar($id);
        if ($informe === null) {
            return Util::error_no_result();
        }
        if (!self::puedeAcceder($informe)) {
            return Util::error_general('No tienes permiso para ver este informe.');
        }

        return ['output' => ['valid' => true, 'response' => $informe]];
    }

    public static function delete($rqst)
    {
        $id = isset($rqst['id']) ? intval($rqst['id']) : 0;
        if ($id <= 0) {
            return Util::error_missing_data();
        }

        $informe = self::cargar($id);
        if ($informe === null) {
            return Util::error_no_result();
        }

        $puedeManage = SessionData::superAdministrador() || SessionData::hasPermission('ia.informes.manage');
        $esDueno = (int) $informe['tbl_usuario_id'] === (int) SessionData::getUserId();
        if (!$puedeManage && !($esDueno && SessionData::hasPermission('ia.informes.delete'))) {
            return Util::error_general('No tienes permiso para eliminar este informe.');
        }

        $db = new DbConection();
        $pdo = $db->openConect();
        $stmt = $pdo->prepare("DELETE FROM " . $db->getTable('tbl_ia_informes') . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $db->closeConect();

        return ['output' => ['valid' => true, 'response' => $id]];
    }

    public static function cargar(int $id): ?array
    {
        $db = new DbConection();
        $pdo = $db->openConect();
        $stmt = $pdo->prepare("SELECT * FROM " . $db->getTable('tbl_ia_informes') . " WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConect();
        return $fila ?: null;
    }

    public static function puedeAcceder(array $informe): bool
    {
        if (SessionData::superAdministrador() || SessionData::hasPermission('ia.informes.manage')) {
            return true;
        }
        return (int) $informe['tbl_usuario_id'] === (int) SessionData::getUserId();
    }
}
