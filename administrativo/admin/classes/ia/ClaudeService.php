<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Anthropic\Client;
use Anthropic\RequestOptions;
use Anthropic\Core\Exceptions\RateLimitException;
use Anthropic\Core\Exceptions\APIException;
use GuzzleHttp\Client as GuzzleClient;
use Composer\CaBundle\CaBundle;

class ClaudeService
{
    private Client $client;
    private string $model;
    private int $maxTokens;
    private int $maxTokensInforme;
    private string $effort;
    private string $effortInforme;

    public function __construct()
    {
        $cfg = parse_ini_file(__DIR__ . '/../../../config.ini', true);
        $cfg = $cfg['anthropic'] ?? [];

        $caBundle = CaBundle::getSystemCaRootBundlePath();
        $guzzle = new GuzzleClient([
            'verify' => $caBundle ?: true,
            'timeout' => 120,
        ]);

        $this->client = new Client(
            apiKey: $cfg['api_key'] ?? '',
            requestOptions: RequestOptions::with(timeout: 120.0, transporter: $guzzle),
        );
        $this->model = $cfg['model'] ?? 'claude-sonnet-5';
        $this->maxTokens = (int) ($cfg['max_tokens'] ?? 16000);
        $this->maxTokensInforme = (int) ($cfg['max_tokens_informe'] ?? 32000);
        $this->effort = $cfg['effort'] ?? 'medium';
        $this->effortInforme = $cfg['effort_informe'] ?? 'high';
    }

    /**
     * @param array $messages lista de mensajes en formato de la API (role/content)
     * @param array $system bloques de system prompt
     * @param array|null $tools definiciones de herramientas
     * @return \Anthropic\Messages\Message
     */
    public function crearMensaje(array $messages, array $system, ?array $tools = null, bool $paraInforme = false, ?string $container = null)
    {
        try {
            return $this->client->messages->create(
                maxTokens: $paraInforme ? $this->maxTokensInforme : $this->maxTokens,
                messages: $messages,
                model: $this->model,
                system: $system,
                thinking: ['type' => 'adaptive'],
                tools: $tools,
                container: $container,
                outputConfig: ['effort' => $paraInforme ? $this->effortInforme : $this->effort],
            );
        } catch (RateLimitException $e) {
            throw new RuntimeException('Se alcanzó el límite de solicitudes a la API de Anthropic. Intenta de nuevo en unos minutos.');
        } catch (APIException $e) {
            $mensaje = $e->getMessage();
            if (stripos($mensaje, 'api_key') !== false || stripos($mensaje, 'authentic') !== false) {
                throw new RuntimeException('La clave de API de Anthropic no es válida o no está configurada.');
            }
            if (stripos($mensaje, 'timeout') !== false) {
                throw new RuntimeException('La API de Anthropic tardó demasiado en responder. Intenta de nuevo.');
            }
            throw new RuntimeException('Error al comunicarse con la API de Anthropic: ' . $mensaje);
        } catch (\Throwable $e) {
            throw new RuntimeException('Error inesperado al generar la respuesta: ' . $e->getMessage());
        }
    }

    public static function bloqueAContenido($bloque): array
    {
        return json_decode(json_encode($bloque), true);
    }
}
