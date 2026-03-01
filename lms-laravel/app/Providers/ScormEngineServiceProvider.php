<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\ScormEngine\ScormEngineGateway;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use ScormEngineSdk\Auth\BearerTokenAuthStrategy;
use ScormEngineSdk\Client\ScormEngineClient;
use ScormEngineSdk\Client\ScormEngineClientFactory;
use ScormEngineSdk\Configuration\Configuration;
use ScormEngineSdk\Transport\Psr18Transport;

class ScormEngineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ScormEngineClient::class, function (Application $app): ScormEngineClient {
            $baseUrl = (string) config('scorm_engine.base_url');
            $timeout = (float) config('scorm_engine.timeout_seconds', 15);
            $token = $this->resolveAdminToken($app, $baseUrl, $timeout);

            $configuration = new Configuration(
                baseUrl: $baseUrl,
                defaultAuthStrategy: new BearerTokenAuthStrategy($token)
            );

            $transport = new Psr18Transport(new GuzzleClient([
                'timeout' => $timeout,
            ]));

            $httpFactory = new HttpFactory();
            $clientFactory = new ScormEngineClientFactory();

            return $clientFactory->createDefault(
                configuration: $configuration,
                transport: $transport,
                requestFactory: $httpFactory,
                streamFactory: $httpFactory
            );
        });

        $this->app->singleton(ScormEngineGateway::class, function (Application $app): ScormEngineGateway {
            return new ScormEngineGateway($app->make(ScormEngineClient::class));
        });
    }

    private function resolveAdminToken(Application $app, string $baseUrl, float $timeout): string
    {
        $token = trim((string) config('scorm_engine.admin_token'));
        if ($token !== '' && !$this->isJwtExpired($token)) {
            return $token;
        }

        // For local development, recover automatically when token is missing/expired.
        if ($app->environment('local')) {
            $generated = $this->requestDevAdminToken($baseUrl, $timeout);
            if ($generated !== null) {
                return $generated;
            }
        }

        return $token;
    }

    private function requestDevAdminToken(string $baseUrl, float $timeout): ?string
    {
        try {
            $client = new GuzzleClient(['timeout' => $timeout]);
            $response = $client->post(rtrim($baseUrl, '/') . '/auth/dev-token', [
                'json' => [
                    'userId' => '11111111-1111-1111-1111-111111111111',
                    'roles' => ['ADMIN'],
                ],
            ]);

            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return null;
            }

            /** @var mixed $decoded */
            $decoded = json_decode((string) $response->getBody(), true);
            if (!is_array($decoded)) {
                return null;
            }

            $token = $decoded['token'] ?? null;
            if (!is_string($token) || trim($token) === '') {
                return null;
            }

            return trim($token);
        } catch (GuzzleException) {
            return null;
        }
    }

    private function isJwtExpired(string $token): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        $payload = json_decode($this->decodeBase64Url($parts[1]), true);
        if (!is_array($payload) || !isset($payload['exp']) || !is_numeric($payload['exp'])) {
            return false;
        }

        return ((int) $payload['exp']) <= (time() + 30);
    }

    private function decodeBase64Url(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder > 0) {
            $input .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($input, '-_', '+/'), true);
        return is_string($decoded) ? $decoded : '';
    }
}
