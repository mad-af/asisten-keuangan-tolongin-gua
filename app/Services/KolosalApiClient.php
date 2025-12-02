<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * KolosalApiClient
 * - Model is always read from config('kolosal.model') or env, never from request payload.
 * - Returns ChatResponse DTO for clean handling.
 */
class KolosalApiClient
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $model; // always from env/config

    public function __construct()
    {
        $this->baseUrl = rtrim(config('kolosal.base_url'), '/');
        $this->apiKey = config('kolosal.api_key');
        $this->model = config('kolosal.model');
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Send chat completion request.
     * Accepts ChatRequest or array (converted internally) but model will NOT be taken from payload.
     */
    public function chatCompletions(ChatRequest|array $request): ChatResponse
    {
        $start = microtime(true);

        if (is_array($request)) {
            try {
                $request = ChatRequest::fromArray($request);
            } catch (\InvalidArgumentException $e) {
                return ChatResponse::error(400, $e->getMessage(), null, ['elapsed_ms' => $this->elapsedMs($start)]);
            }
        }

        // Ensure the model is always our configured model (ignore any model coming from request)
        $payload = $request->toArray();
        $payload['model'] = $this->model;

        $res = Http::withHeaders($this->headers())
            ->post($this->baseUrl.'/v1/chat/completions', $payload);

        $elapsed = $this->elapsedMs($start);
        $reqId = $res->header('x-request-id') ?? null;

        if ($res->successful()) {
            return ChatResponse::success($res->status(), $res->json(), ['request_id' => $reqId, 'elapsed_ms' => $elapsed]);
        }

        $body = null;
        try {
            $body = $res->json();
        } catch (\Throwable $e) {
            $body = null;
        }

        $errorMessage = is_array($body) && isset($body['error'])
            ? (string) ($body['error']['message'] ?? $body['error'])
            : ($body['message'] ?? 'Request failed');

        return ChatResponse::error($res->status(), $errorMessage, $body, ['request_id' => $reqId, 'elapsed_ms' => $elapsed]);
    }

    protected function elapsedMs(float $start): int
    {
        return (int) ((microtime(true) - $start) * 1000);
    }
}

/* ----------------------
   DTOs (recommended: move to App\Dtos or App\Http\Requests in real project)
   ---------------------- */

class ChatRequest
{
    public array $messages;

    public ?int $max_tokens;

    public function __construct(array $messages, ?int $max_tokens = null)
    {
        if (empty($messages)) {
            throw new \InvalidArgumentException('messages must be a non-empty array');
        }

        $this->messages = $messages;
        $this->max_tokens = $max_tokens;
    }

    public static function fromArray(array $payload): self
    {
        if (! isset($payload['messages']) || ! is_array($payload['messages'])) {
            throw new \InvalidArgumentException('Missing required field: messages');
        }

        return new self(
            $payload['messages'],
            $payload['max_tokens'] ?? null,
        );
    }

    public function toArray(): array
    {
        $out = [
            'messages' => $this->messages,
        ];

        if ($this->max_tokens !== null) {
            $out['max_tokens'] = $this->max_tokens;
        }

        return $out;
    }
}

class ChatResponse
{
    public bool $ok;

    public int $status;

    public mixed $data; // decoded JSON or null

    public ?string $error;

    public array $meta;

    private function __construct(bool $ok, int $status, mixed $data, ?string $error, array $meta = [])
    {
        $this->ok = $ok;
        $this->status = $status;
        $this->data = $data;
        $this->error = $error;
        $this->meta = $meta;
    }

    public static function success(int $status, mixed $data, array $meta = []): self
    {
        return new self(true, $status, $data, null, $meta);
    }

    public static function error(int $status, string $error, mixed $data = null, array $meta = []): self
    {
        return new self(false, $status, $data, $error, $meta);
    }

    // small helpers
    public function isOk(): bool
    {
        return $this->ok;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getChoices(): array
    {
        if (! is_array($this->data)) {
            return [];
        }
        $choices = $this->data['choices'] ?? null;

        return is_array($choices) ? $choices : [];
    }

    public function messages(): array
    {
        $out = [];
        foreach ($this->getChoices() as $c) {
            $m = $c['message'] ?? null;
            if (is_array($m)) {
                $out[] = [
                    'role' => $m['role'] ?? null,
                    'content' => $m['content'] ?? null,
                ];
            }
        }

        return $out;
    }
}
