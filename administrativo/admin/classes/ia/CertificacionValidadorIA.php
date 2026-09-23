<?php

require_once __DIR__ . '/ClaudeService.php';
require_once __DIR__ . '/ElevenLabsService.php';

final class CertificacionValidadorIA
{
    private const ESTADOS_VEREDICTO = ['bien', 'mal'];

    public static function validarPreview(array $certificacion): array
    {
        $audioBase64 = (string) ($certificacion['audio_base64'] ?? '');
        if (trim($audioBase64) === '') {
            return [
                'valid' => false,
                'error' => 'sin_audio',
                'message' => 'No hay audio para validar con IA. Use revisión manual.',
            ];
        }

        $rutaTmp = null;
        try {
            $rutaTmp = self::materializarAudioTemporal($audioBase64, (string) ($certificacion['audio_formato'] ?? 'audio/webm'));
            $transcripcion = ElevenLabsService::transcribir($rutaTmp);
            $payloadQa = self::construirPayloadQa($certificacion);
            $ia = self::evaluarConClaude($transcripcion, $payloadQa, $certificacion);

            return [
                'valid' => true,
                'transcripcion' => $transcripcion,
                'veredicto' => $ia['veredicto'],
                'comentario' => $ia['comentario'],
                'nota_calidad' => $ia['nota_calidad'],
                'coincidencias' => $ia['coincidencias'],
                'confianza' => $ia['confianza'],
                'ia_json' => $ia,
            ];
        } catch (RuntimeException $e) {
            return [
                'valid' => false,
                'error' => 'validacion_ia',
                'message' => $e->getMessage(),
            ];
        } finally {
            if ($rutaTmp !== null && is_file($rutaTmp)) {
                @unlink($rutaTmp);
            }
        }
    }

    private static function materializarAudioTemporal(string $audioBase64, string $formato): string
    {
        $raw = $audioBase64;
        if (stripos($raw, 'base64,') !== false) {
            $parts = explode('base64,', $raw, 2);
            $raw = $parts[1] ?? '';
        }
        $bin = base64_decode($raw, true);
        if ($bin === false || $bin === '') {
            throw new RuntimeException('No se pudo decodificar el audio de la validación.');
        }

        $maxBytes = 25 * 1024 * 1024;
        if (strlen($bin) > $maxBytes) {
            throw new RuntimeException('El audio supera el tamaño máximo permitido (25MB).');
        }

        $carpeta = __DIR__ . '/../../uploads/ia_tmp/';
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true) && !is_dir($carpeta)) {
            throw new RuntimeException('No se pudo preparar el directorio temporal de audio.');
        }

        $ext = 'webm';
        $fmt = strtolower($formato);
        if (strpos($fmt, 'mpeg') !== false || strpos($fmt, 'mp3') !== false) {
            $ext = 'mp3';
        } elseif (strpos($fmt, 'wav') !== false) {
            $ext = 'wav';
        } elseif (strpos($fmt, 'ogg') !== false) {
            $ext = 'ogg';
        } elseif (strpos($fmt, 'mp4') !== false || strpos($fmt, 'm4a') !== false) {
            $ext = 'm4a';
        }

        $ruta = $carpeta . 'cert_' . bin2hex(random_bytes(12)) . '.' . $ext;
        if (file_put_contents($ruta, $bin) === false) {
            throw new RuntimeException('No se pudo escribir el audio temporal.');
        }
        return $ruta;
    }

    private static function construirPayloadQa(array $cert): array
    {
        $items = [];
        if (($cert['origen_tipo'] ?? '') === 'cuestionario') {
            foreach (($cert['respuestas_cuestionario'] ?? []) as $p) {
                $items[] = [
                    'pregunta' => (string) ($p['texto_pregunta'] ?? ''),
                    'respuestas' => array_values(array_map('strval', $p['respuestas'] ?? [])),
                ];
            }
        } elseif (($cert['origen_tipo'] ?? '') === 'sondeo') {
            foreach (($cert['respuestas_sondeo'] ?? []) as $r) {
                $items[] = [
                    'pregunta' => 'Respuesta de sondeo',
                    'respuestas' => array_values(array_filter([
                        (string) ($r['candidato_nombre'] ?? ''),
                        (string) ($r['opcion_texto'] ?? ''),
                    ])),
                ];
            }
        }
        return $items;
    }

    private static function evaluarConClaude(string $transcripcion, array $qa, array $cert): array
    {
        $origen = (string) ($cert['origen_tipo'] ?? 'registro_simple');
        $meta = [
            'origen' => $origen,
            'sondeo' => $cert['sondeo_nombre'] ?? null,
            'cuestionario' => $cert['cuestionario_nombre'] ?? null,
            'latitud' => $cert['latitud'] ?? null,
            'longitud' => $cert['longitud'] ?? null,
            'precision_metros' => $cert['precision_metros'] ?? null,
            'audio_duracion_segundos' => $cert['audio_duracion_segundos'] ?? null,
        ];

        $system = [[
            'type' => 'text',
            'text' => 'Eres un auditor de calidad de encuestas de campo. Debes decidir si la encuesta está BIEN o MAL '
                . 'comparando la transcripción del audio con las respuestas registradas en el formulario/sondeo. '
                . 'El veredicto principal SOLO depende de esa coherencia. En nota_calidad puedes señalar lenguaje, GPS o manipulación. '
                . 'Responde ÚNICAMENTE un JSON válido sin markdown, con este schema exacto: '
                . '{"veredicto":"bien|mal","comentario":"string","nota_calidad":{"lenguaje":"string","gps":"string","manipulacion":"string"},'
                . '"coincidencias":[{"pregunta":"string","respuesta_registrada":"string","mencionado_en_audio":true,"observacion":"string"}],'
                . '"confianza":0.0}. No inventes preguntas ni respuestas que no estén en el payload.',
        ]];

        $userText = "Metadatos:\n" . json_encode($meta, JSON_UNESCAPED_UNICODE)
            . "\n\nRespuestas registradas:\n" . json_encode($qa, JSON_UNESCAPED_UNICODE)
            . "\n\nTranscripción del audio:\n" . $transcripcion;

        $claude = new ClaudeService();
        $mensaje = $claude->crearMensaje(
            [['role' => 'user', 'content' => $userText]],
            $system,
            null,
            false,
            null
        );

        $texto = self::extraerTextoMensaje($mensaje);
        $json = self::parsearJsonEstricto($texto);
        $veredicto = strtolower(trim((string) ($json['veredicto'] ?? '')));
        if (!in_array($veredicto, self::ESTADOS_VEREDICTO, true)) {
            throw new RuntimeException('La IA no devolvió un veredicto válido (bien|mal).');
        }

        return [
            'veredicto' => $veredicto,
            'comentario' => trim((string) ($json['comentario'] ?? '')),
            'nota_calidad' => [
                'lenguaje' => (string) (($json['nota_calidad']['lenguaje'] ?? 'ok')),
                'gps' => (string) (($json['nota_calidad']['gps'] ?? 'ok')),
                'manipulacion' => (string) (($json['nota_calidad']['manipulacion'] ?? 'ok')),
            ],
            'coincidencias' => is_array($json['coincidencias'] ?? null) ? $json['coincidencias'] : [],
            'confianza' => isset($json['confianza']) ? (float) $json['confianza'] : 0.0,
        ];
    }

    private static function extraerTextoMensaje($mensaje): string
    {
        $bloques = $mensaje->content ?? [];
        $partes = [];
        foreach ($bloques as $bloque) {
            $arr = ClaudeService::bloqueAContenido($bloque);
            if (($arr['type'] ?? '') === 'text' && isset($arr['text'])) {
                $partes[] = (string) $arr['text'];
            }
        }
        $texto = trim(implode("\n", $partes));
        if ($texto === '') {
            throw new RuntimeException('La IA no devolvió texto utilizable.');
        }
        return $texto;
    }

    private static function parsearJsonEstricto(string $texto): array
    {
        $candidato = trim($texto);
        if (preg_match('/\{[\s\S]*\}/', $candidato, $m)) {
            $candidato = $m[0];
        }
        $datos = json_decode($candidato, true);
        if (!is_array($datos)) {
            throw new RuntimeException('La respuesta de la IA no es un JSON válido.');
        }
        return $datos;
    }
}
