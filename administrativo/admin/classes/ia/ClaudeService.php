<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Anthropic\Client;
use Anthropic\RequestOptions;
use Anthropic\Core\Exceptions\RateLimitException;
use GuzzleHttp\Client as GuzzleClient;
use Composer\CaBundle\CaBundle;
use Anthropic\Core\Conversion;
use Anthropic\Core\Conversion\DumpState;
use Anthropic\Lib\Streaming\MessageAccumulator;

class ClaudeService
{
    private const MAX_SALIDA_MODELO = 128000;

    private Client $client;
    private string $model;
    private int $maxTokens;
    private int $maxTokensInforme;
    private string $effort;
    private string $effortInforme;
    private bool $streaming;

    public function __construct()
    {
        $cfg = parse_ini_file(__DIR__ . '/../../../config.ini', true);
        $cfg = $cfg['anthropic'] ?? [];

        $caBundle = CaBundle::getSystemCaRootBundlePath();
        $guzzle = new GuzzleClient([
            'verify' => $caBundle ?: true,
            'timeout' => 200,
        ]);

        $this->client = new Client(
            apiKey: $cfg['api_key'] ?? '',
            requestOptions: RequestOptions::with(timeout: 200.0, transporter: $guzzle),
        );
        $this->model = $cfg['model'] ?? 'claude-sonnet-5';
        $this->maxTokens = min(self::MAX_SALIDA_MODELO, max(1024, (int) ($cfg['max_tokens'] ?? 16000)));
        $this->maxTokensInforme = min(self::MAX_SALIDA_MODELO, max(1024, (int) ($cfg['max_tokens_informe'] ?? 32000)));
        $this->effort = $cfg['effort'] ?? 'medium';
        $this->effortInforme = $cfg['effort_informe'] ?? 'high';
        $this->streaming = (string) ($cfg['streaming'] ?? '1') !== '0';
    }

    /**
     * @param array $messages lista de mensajes en formato de la API (role/content)
     * @param array $system bloques de system prompt
     * @param array|null $tools definiciones de herramientas
     * @return \Anthropic\Messages\Message
     */
    public function crearMensaje(array $messages, array $system, ?array $tools = null, bool $paraInforme = false, ?string $container = null, bool $sinHerramientas = false)
    {
        $args = [
            'maxTokens' => $paraInforme ? $this->maxTokensInforme : $this->maxTokens,
            'messages' => $messages,
            'model' => $this->model,
            'system' => $system,
            'thinking' => ['type' => 'adaptive'],
            'tools' => $tools,
            'container' => $container,
            'toolChoice' => $sinHerramientas ? ['type' => 'none'] : null,
            'outputConfig' => ['effort' => $paraInforme ? $this->effortInforme : $this->effort],
        ];
        $reintentos = 0;
        while (true) {
            try {
                return $this->enviar($args);
            } catch (RateLimitException $e) {
                throw new RuntimeException('Se alcanzó el límite de solicitudes al servicio de IA. Intenta de nuevo en unos minutos.');
            } catch (\Throwable $e) {
                $mensaje = $e->getMessage();
                if ($reintentos < 1 && self::esTransitorio($mensaje)) {
                    $reintentos++;
                    sleep(3);
                    continue;
                }
                throw new RuntimeException(self::mensajeParaUsuario($e));
            }
        }
    }

    private function enviar(array $args)
    {
        if ($this->streaming) {
            try {
                $acumulador = MessageAccumulator::forMessages();
                foreach ($this->client->messages->createStream(...$args) as $evento) {
                    $acumulador->accumulate($evento);
                }
                return $acumulador->message();
            } catch (\LogicException | \TypeError $e) {
            }
        }
        return $this->client->messages->create(...$args);
    }

    private static function esTransitorio(string $mensaje): bool
    {
        foreach (['timeout', 'timed out', 'overloaded', '529', '503', '502', 'connection', 'cURL error', 'SSL', 'stream'] as $pista) {
            if (stripos($mensaje, $pista) !== false) {
                return true;
            }
        }
        return false;
    }

    private static function mensajeParaUsuario(\Throwable $e): string
    {
        $mensaje = $e->getMessage();
        if (stripos($mensaje, 'credit balance') !== false) {
            return 'La cuenta del servicio de IA no tiene saldo suficiente. Recarga el crédito de la API para que Yamil pueda responder.';
        }
        if (stripos($mensaje, 'api_key') !== false || stripos($mensaje, 'authentic') !== false) {
            return 'La clave del servicio de IA no es válida o no está configurada.';
        }
        if (stripos($mensaje, 'overloaded') !== false || stripos($mensaje, '529') !== false) {
            return 'El servicio de IA está saturado en este momento. No se modificó nada; intenta de nuevo en un minuto.';
        }
        if (self::esTransitorio($mensaje)) {
            return 'Se cortó la conexión con el servicio de IA antes de terminar. No se modificó nada; puedes reintentar.';
        }
        return 'El servicio de IA no pudo completar la solicitud. No se modificó nada; puedes reintentar. [detalle: ' . mb_substr(preg_replace('/\s+/u', ' ', $mensaje), 0, 120) . ']';
    }

    public static function bloqueAContenido($bloque): array
    {
        return json_decode(json_encode(Conversion::dump_unknown($bloque, new DumpState())), true);
    }
}
