<?php
/**
 * GeminiService — Google Gemini API integration with resilience.
 *
 * Features:
 * - Model priority: gemini-2.5-flash-lite (primary) → gemini-2.5-flash (fallback)
 * - Automatic retry: up to 3 attempts, 2-second delay between retries
 * - Graceful HTTP 503 handling with user-friendly message
 * - 503 response logging
 * - SSL diagnostics for local development
 * - Never crashes the chatbot
 */

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private bool   $debugSsl;
    private ?string $sslCertPath = null;

    /** Model priority: try in this order */
    private array $models = [
        'gemini-2.5-flash-lite',
        'gemini-2.5-flash',
    ];

    /** Maximum retry attempts per model */
    private int $maxRetries = 3;

    /** Seconds to wait between retries */
    private int $retryDelay = 2;

    /** Friendly message returned on 503 / overload */
    private const MSG_OVERLOADED = '🤖 Maslaki AI est temporairement occupé. Veuillez réessayer dans quelques secondes.';

    // ── Constructor ──────────────────────────────────────────────

    public function __construct()
    {
        $this->loadEnv();

        $this->apiKey   = getenv('GEMINI_API_KEY') ?: '';
        $this->debugSsl = (getenv('GEMINI_DEBUG_SSL') === 'true');

        $this->sslCertPath = $this->detectCaBundle();
    }

    // ── Public API ───────────────────────────────────────────────

    /**
     * Send a prompt to Gemini with automatic retries and model fallback.
     *
     * Flow:
     *   1. Try primary model (gemini-2.5-flash-lite) up to 3 times
     *   2. On persistent 503, try fallback model (gemini-2.5-flash) up to 3 times
     *   3. Return friendly message if all attempts fail with 503
     *   4. Return error details for non-503 failures
     *
     * @param string $prompt  User question.
     * @param string $system  Optional system instruction.
     * @return array{success:bool, reply:string|null, error:string|null, http_code:int|null, raw:mixed, model_used:string|null}
     */
    public function ask(string $prompt, string $system = ''): array
    {
        // ── Validate API key ─────────────────────────────────────
        if (empty($this->apiKey) || $this->apiKey === 'YOUR_KEY') {
            return [
                'success'    => false,
                'reply'      => null,
                'error'      => 'Clé API Gemini non configurée. Ajoutez GEMINI_API_KEY dans le fichier .env',
                'http_code'  => null,
                'raw'        => null,
                'model_used' => null,
            ];
        }

        // ── Try each model in priority order ─────────────────────
        foreach ($this->models as $model) {
            $result = $this->tryModel($model, $prompt, $system);

            // Success — return immediately
            if ($result['success']) {
                $result['model_used'] = $model;
                return $result;
            }

            // Non-503 error — no point trying another model
            // (auth errors, bad requests, etc. affect all models)
            if ($result['http_code'] !== 503) {
                $result['model_used'] = $model;
                return $result;
            }

            // 503 — log and try next model
            $this->log503($model, $result);
        }

        // ── All models exhausted — return friendly message ───────
        return [
            'success'    => false,
            'reply'      => null,
            'error'      => self::MSG_OVERLOADED,
            'http_code'  => 503,
            'raw'        => null,
            'model_used' => null,
        ];
    }

    /**
     * Get diagnostics about the service configuration.
     */
    public function diagnostics(): array
    {
        return [
            'api_key_set'       => !empty($this->apiKey) && $this->apiKey !== 'YOUR_KEY',
            'api_key_preview'   => $this->apiKey ? substr($this->apiKey, 0, 8) . '...' : 'NON DÉFINIE',
            'models'            => $this->models,
            'max_retries'       => $this->maxRetries,
            'retry_delay'       => $this->retryDelay,
            'ssl_debug'         => $this->debugSsl,
            'ssl_cert_path'     => $this->sslCertPath,
            'ssl_cert_exists'   => $this->sslCertPath && file_exists($this->sslCertPath),
            'curl_available'    => function_exists('curl_init'),
            'openssl_available' => extension_loaded('openssl'),
        ];
    }

    // ── Retry Logic ──────────────────────────────────────────────

    /**
     * Try a single model with automatic retries on 503.
     *
     * @return array{success:bool, reply:string|null, error:string|null, http_code:int|null, raw:mixed}
     */
    private function tryModel(string $model, string $prompt, string $system): array
    {
        $lastResult = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            $result = $this->sendRequest($model, $prompt, $system);
            $lastResult = $result;

            // Success
            if ($result['success']) {
                return $result;
            }

            // Non-503 error — don't retry (won't help)
            if ($result['http_code'] !== 503) {
                return $result;
            }

            // 503 — log this attempt
            $this->log503($model, $result, $attempt);

            // Wait before retry (except on last attempt)
            if ($attempt < $this->maxRetries) {
                sleep($this->retryDelay);
            }
        }

        // All retries exhausted for this model
        return $lastResult;
    }

    // ── HTTP Request ─────────────────────────────────────────────

    /**
     * Send a single API request to the specified model.
     *
     * @return array{success:bool, reply:string|null, error:string|null, http_code:int|null, raw:mixed}
     */
    private function sendRequest(string $model, string $prompt, string $system): array
    {
        $url     = $this->baseUrl . $model . ':generateContent?key=' . $this->apiKey;
        $payload = $this->buildPayload($prompt, $system);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        // SSL configuration
        if ($this->debugSsl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        } elseif ($this->sslCertPath) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, $this->sslCertPath);
        }

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        // ── cURL-level error ─────────────────────────────────────
        if ($curlErrno !== 0) {
            $sslHint = '';
            if (strpos($curlError, 'SSL') !== false || $curlErrno === 60) {
                $sslHint = ' Conseil: ajoutez GEMINI_DEBUG_SSL=true dans .env pour le développement local.';
            }
            return [
                'success'   => false,
                'reply'     => null,
                'error'     => 'Erreur cURL (' . $curlErrno . '): ' . $curlError . $sslHint,
                'http_code' => $httpCode,
                'raw'       => null,
            ];
        }

        $decoded = json_decode($response, true);

        // ── HTTP error ───────────────────────────────────────────
        if ($httpCode !== 200) {
            $errMsg = 'Erreur API Gemini (HTTP ' . $httpCode . ')';
            if (isset($decoded['error']['message'])) {
                $errMsg .= ': ' . $decoded['error']['message'];
            }

            // 503 — return a special structure so the retry logic can detect it
            return [
                'success'   => false,
                'reply'     => null,
                'error'     => $errMsg,
                'http_code' => (int) $httpCode,
                'raw'       => $decoded,
            ];
        }

        // ── Extract text from response ───────────────────────────
        $text = $this->extractText($decoded);

        if ($text === null) {
            return [
                'success'   => false,
                'reply'     => null,
                'error'     => 'Aucune réponse textuelle reçue de Gemini.',
                'http_code' => $httpCode,
                'raw'       => $decoded,
            ];
        }

        return [
            'success'   => true,
            'reply'     => $text,
            'error'     => null,
            'http_code' => $httpCode,
            'raw'       => $decoded,
        ];
    }

    // ── 503 Logging ──────────────────────────────────────────────

    /**
     * Log a 503 response to the PHP error log.
     */
    private function log503(string $model, array $result, int $attempt = 0): void
    {
        $timestamp  = date('Y-m-d H:i:s');
        $apiMessage = $result['raw']['error']['message'] ?? 'Unknown error';
        $logLine    = "[GeminiService 503] {$timestamp} | Model: {$model} | Attempt: {$attempt}/{$this->maxRetries} | Message: {$apiMessage}";

        error_log($logLine);
    }

    // ── Private Helpers ──────────────────────────────────────────

    private function buildPayload(string $prompt, string $system): array
    {
        $parts = [];

        if ($system !== '') {
            $parts[] = ['text' => $system];
        }

        $parts[] = ['text' => $prompt];

        return [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => $parts,
                ],
            ],
            'generationConfig' => [
                'temperature'     => 0.7,
                'topP'            => 0.95,
                'topK'            => 40,
                'maxOutputTokens' => 1024,
            ],
        ];
    }

    private function extractText(?array $decoded): ?string
    {
        if (!$decoded) {
            return null;
        }

        // Standard path: candidates[0].content.parts[0].text
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $decoded['candidates'][0]['content']['parts'][0]['text'];
            return $this->cleanText($text);
        }

        // Fallback: check for promptFeedback with block reason
        if (isset($decoded['promptFeedback']['blockReason'])) {
            return 'Réponse bloquée par les filtres de sécurité: ' . $decoded['promptFeedback']['blockReason'];
        }

        return null;
    }

    private function cleanText(string $text): string
    {
        return trim($text);
    }

    private function detectCaBundle(): ?string
    {
        $paths = [
            // XAMPP
            'C:\\xampp\\php\\extras\\ssl\\cacert.pem',
            // WAMP
            'C:\\wamp64\\bin\\php\\php8.2.12\\extras\\ssl\\cacert.pem',
            // Laragon
            'C:\\laragon\\bin\\php\\php-8.2.12\\extras\\ssl\\cacert.pem',
            // Linux
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            // macOS
            '/usr/local/etc/openssl/cert.pem',
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $iniCurl    = ini_get('curl.cainfo');
        $iniOpenssl = ini_get('openssl.cafile');

        if ($iniCurl && file_exists($iniCurl)) {
            return $iniCurl;
        }

        if ($iniOpenssl && file_exists($iniOpenssl)) {
            return $iniOpenssl;
        }

        return null;
    }

    private function loadEnv(): void
    {
        $envFile = dirname(__DIR__) . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key   = trim($key);
                $value = trim($value);
                if ($key !== '' && getenv($key) === false) {
                    putenv("$key=$value");
                    $_ENV[$key]    = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}
