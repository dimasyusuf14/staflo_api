<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private const GOOGLE_AUTH_URL  = 'https://oauth2.googleapis.com/token';
    private const FCM_SCOPE        = 'https://www.googleapis.com/auth/firebase.messaging';
    private const ACCESS_TOKEN_TTL = 3500; // seconds (token expires in 3600, cache 100s early)

    private Client $http;
    private array  $credentials;

    public function __construct()
    {
        $this->http        = new Client(['timeout' => 10]);
        $this->credentials = $this->loadCredentials();
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Send a push notification to a single device FCM token.
     *
     * @param  string  $fcmToken  Device FCM registration token
     * @param  string  $title     Notification title
     * @param  string  $body      Notification body
     * @param  array   $data      Optional key-value data payload (string values only)
     * @return bool    True on success, false on failure
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (empty($fcmToken)) {
            return false;
        }

        $projectId   = $this->credentials['project_id'];
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            Log::error('FCM: Could not obtain Google access token.');
            return false;
        }

        $payload = [
            'message' => [
                'token'        => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data'         => $this->stringifyData($data),
                'android'      => [
                    'priority' => 'high',
                    'notification' => [
                        'sound'        => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $this->http->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse()
                ? (string) $e->getResponse()->getBody()
                : $e->getMessage();

            Log::error('FCM: Failed to send notification.', [
                'fcm_token' => substr($fcmToken, 0, 20) . '...',
                'error'     => $responseBody,
            ]);

            return false;
        }
    }

    /**
     * Send a push notification to multiple device FCM tokens (batch).
     * Returns the count of successfully sent notifications.
     *
     * @param  string[]  $fcmTokens
     */
    public function sendToMultiple(array $fcmTokens, string $title, string $body, array $data = []): int
    {
        $successCount = 0;

        foreach ($fcmTokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    // -------------------------------------------------------------------------
    // Authentication — Google OAuth2 via JWT (RFC 7523)
    // -------------------------------------------------------------------------

    /**
     * Return a cached Google access token, refreshing it when expired.
     */
    private function getAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', self::ACCESS_TOKEN_TTL, function () {
            return $this->fetchAccessToken();
        });
    }

    /**
     * Exchange a signed JWT for a Google OAuth2 access token.
     */
    private function fetchAccessToken(): ?string
    {
        $jwt = $this->buildJwt();
        if (! $jwt) {
            return null;
        }

        try {
            $response = $this->http->post(self::GOOGLE_AUTH_URL, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'   => $jwt,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            return $body['access_token'] ?? null;
        } catch (RequestException $e) {
            $responseBody = $e->hasResponse()
                ? (string) $e->getResponse()->getBody()
                : $e->getMessage();

            Log::error('FCM: Failed to fetch access token.', ['error' => $responseBody]);

            return null;
        }
    }

    /**
     * Build a signed RS256 JWT for Google service-account authentication.
     */
    private function buildJwt(): ?string
    {
        $now         = time();
        $clientEmail = $this->credentials['client_email'] ?? null;
        $privateKey  = $this->credentials['private_key']  ?? null;

        if (! $clientEmail || ! $privateKey) {
            Log::error('FCM: Service account credentials are incomplete (missing client_email or private_key).');
            return null;
        }

        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $payload = $this->base64UrlEncode(json_encode([
            'iss'   => $clientEmail,
            'scope' => self::FCM_SCOPE,
            'aud'   => self::GOOGLE_AUTH_URL,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "{$header}.{$payload}";

        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if (! $privateKeyResource) {
            Log::error('FCM: Could not load private key from service account credentials.');
            return null;
        }

        $signature = '';
        openssl_sign($signingInput, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        return "{$signingInput}." . $this->base64UrlEncode($signature);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * FCM data payload only accepts string values.
     */
    private function stringifyData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[(string) $key] = is_string($value) ? $value : (string) json_encode($value);
        }
        return $result;
    }

    /**
     * Load the Firebase service account credentials JSON.
     * Path is configured via FIREBASE_CREDENTIALS env variable.
     */
    private function loadCredentials(): array
    {
        $path = config('firebase.credentials_path');

        if (! $path || ! file_exists($path)) {
            Log::warning('FCM: Service account file not found. Ensure FIREBASE_CREDENTIALS is set in .env.');
            return [];
        }

        $content = file_get_contents($path);
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || ($decoded['type'] ?? '') !== 'service_account') {
            Log::error('FCM: Invalid service account JSON file.');
            return [];
        }

        return $decoded;
    }
}
