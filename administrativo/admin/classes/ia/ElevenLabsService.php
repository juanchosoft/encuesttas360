<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Composer\CaBundle\CaBundle;

final class ElevenLabsService
{
    private const BASE_URL = 'https://api.elevenlabs.io/v1';
    private const TTS_MAX_CHARS = 2500;

    private static ?array $cfgCache = null;
    private static ?string $caBundleCache = null;
    private static bool $caBundleResuelto = false;

    private static function cfg(): array
    {
        if (self::$cfgCache === null) {
            $cfg = parse_ini_file(__DIR__ . '/../../../config.ini', true);
            self::$cfgCache = $cfg['elevenlabs'] ?? [];
        }
        return self::$cfgCache;
    }

    private static function caBundle(): ?string
    {
        if (!self::$caBundleResuelto) {
            self::$caBundleCache = CaBundle::getSystemCaRootBundlePath() ?: null;
            self::$caBundleResuelto = true;
        }
        return self::$caBundleCache;
    }

    private static function curlBase(string $apiKey): \CurlHandle
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['xi-api-key: ' . $apiKey],
        ]);
        $caBundle = self::caBundle();
        if ($caBundle) {
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }
        return $ch;
    }

    public static function transcribir(string $rutaAudioLocal): string
    {
        $cfg = self::cfg();
        $apiKey = $cfg['api_key'] ?? '';
        if ($apiKey === '' || $apiKey === 'REEMPLAZAR_CON_TU_API_KEY_DE_ELEVENLABS') {
            throw new RuntimeException('La clave de API de ElevenLabs no está configurada.');
        }

        $ch = self::curlBase($apiKey);
        curl_setopt_array($ch, [
            CURLOPT_URL => self::BASE_URL . '/speech-to-text',
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POSTFIELDS => [
                'model_id' => $cfg['stt_model'] ?? 'scribe_v1',
                'language_code' => 'es',
                'file' => new \CURLFile($rutaAudioLocal),
            ],
        ]);

        $respuesta = curl_exec($ch);
        $error = curl_error($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($respuesta === false) {
            throw new RuntimeException('Error de red al transcribir el audio: ' . $error);
        }
        if ($codigo !== 200) {
            throw new RuntimeException('ElevenLabs respondió con error al transcribir (HTTP ' . $codigo . ').');
        }

        $datos = json_decode($respuesta, true);
        $texto = trim((string) ($datos['text'] ?? ''));
        if ($texto === '') {
            throw new RuntimeException('No se pudo reconocer ningún texto en el audio.');
        }

        return $texto;
    }

    public static function sintetizar(string $texto): string
    {
        $cfg = self::cfg();
        $apiKey = $cfg['api_key'] ?? '';
        $voiceId = $cfg['voice_id'] ?? '';
        if ($apiKey === '' || $apiKey === 'REEMPLAZAR_CON_TU_API_KEY_DE_ELEVENLABS') {
            throw new RuntimeException('La clave de API de ElevenLabs no está configurada.');
        }
        if ($voiceId === '' || $voiceId === 'REEMPLAZAR_CON_TU_VOICE_ID') {
            throw new RuntimeException('El voice_id de ElevenLabs no está configurado.');
        }

        $texto = mb_substr($texto, 0, self::TTS_MAX_CHARS);

        $ch = self::curlBase($apiKey);
        curl_setopt_array($ch, [
            CURLOPT_URL => self::BASE_URL . '/text-to-speech/' . rawurlencode($voiceId) . '?output_format=mp3_44100_128',
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => [
                'xi-api-key: ' . $apiKey,
                'Content-Type: application/json',
                'Accept: audio/mpeg',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'text' => $texto,
                'model_id' => $cfg['tts_model'] ?? 'eleven_flash_v2_5',
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $respuesta = curl_exec($ch);
        $error = curl_error($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($respuesta === false) {
            throw new RuntimeException('Error de red al generar el audio: ' . $error);
        }
        if ($codigo !== 200 || $respuesta === '') {
            throw new RuntimeException('ElevenLabs respondió con error al generar el audio (HTTP ' . $codigo . ').');
        }

        return $respuesta;
    }

    public static function limpiarMarkdown(string $texto): string
    {
        $texto = preg_replace('/```[\s\S]*?```/', '', $texto);
        $texto = preg_replace('/`([^`]+)`/', '$1', $texto);
        $texto = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $texto);
        $texto = preg_replace('/[*_#>]+/', '', $texto);
        $texto = preg_replace('/^\s*[-•]\s+/m', '', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        return trim($texto);
    }
}
