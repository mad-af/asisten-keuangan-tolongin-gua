<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KolosalApiClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('kolosal.base_url');
        $this->apiKey = config('kolosal.api_key');
        $this->model = config('kolosal.model');
    }

    protected function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function chatCompletions(array $payload): array
    {
        $start = microtime(true);
        if (!isset($payload['messages']) || !is_array($payload['messages'])) {
            return [
                'ok' => false,
                'status' => 400,
                'response' => null,
                'error' => 'Missing required field: messages',
                'meta' => ['request_id' => null, 'elapsed_ms' => (int) ((microtime(true)-$start)*1000)],
            ];
        }

        $res = Http::withHeaders($this->authHeaders())
            ->post(rtrim($this->baseUrl,'/').'/v1/chat/completions', $payload);

        $elapsed = (int) ((microtime(true)-$start)*1000);
        $reqId = $res->header('x-request-id') ?? null;

        if ($res->successful()) {
            return [
                'ok' => true,
                'status' => $res->status(),
                'response' => $res->json(),
                'error' => null,
                'meta' => ['request_id' => $reqId, 'elapsed_ms' => $elapsed],
            ];
        }

        $body = null;
        try { $body = $res->json(); } catch (\Throwable $e) { $body = null; }
        return [
            'ok' => false,
            'status' => $res->status(),
            'response' => $body,
            'error' => is_array($body) && isset($body['error']) ? (string) $body['error'] : 'Request failed',
            'meta' => ['request_id' => $reqId, 'elapsed_ms' => $elapsed],
        ];
    }
}
