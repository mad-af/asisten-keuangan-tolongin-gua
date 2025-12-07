<?php

namespace App\Services;

use App\Models\SystemContext;

class AgentChatService
{
    public function __construct(protected KolosalApiClient $chatClient) {}

    private function agentOrchestratorMessages(string $message, ?array $premessages = null): array
    {
        $content = $this->systemContext('orchestrator', ['today' => date('Y-m-d')]);

        $messages = [
            [
                'role' => 'system',
                'content' => $content,
            ],
        ];

        if ($premessages !== null) {
            $messages = array_merge($messages, $premessages);
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        return $messages;
    }

    private function agentPersonaMessages(?string $message, ?array $premessages): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemContext('persona'),
            ],
        ];

        if ($premessages !== null) {
            $messages = array_merge($messages, $premessages);
        }

        if ($message !== null) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $message,
            ];
        }

        return $messages;
    }

    private function newAgentFinanceAnalyzeMessages(string $context, string $deviceId): array
    {
        $content = $this->systemContext('finance_analyzer_device', ['device_id' => $deviceId, 'today' => date('Y-m-d')]);

        return [
            [
                'role' => 'system',
                'content' => $content,
            ],
            [
                'role' => 'user',
                'content' => $context,
            ],
        ];
    }

    private function agentFinanceAnalyzeMessages(string $message): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemContext('finance_analyzer', ['today' => date('Y-m-d')]),
            ],
            [
                'role' => 'assistant',
                'content' => $message,
            ],
        ];

        return $messages;
    }

    private function systemContext(string $key, array $vars = []): string
    {
        $content = SystemContext::cachedContent($key);
        if ($content === null) {
            return '';
        }
        foreach ($vars as $k => $v) {
            $content = str_replace('{{'.$k.'}}', (string) $v, $content);
        }

        return $content;
    }

    public function agentOrchestratorChat(string $message, ?array $premessages = null): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentOrchestratorMessages($message, $premessages),
        ])->messageContent();
    }

    public function agentPersonaChat(string $message, ?array $premessages): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentPersonaMessages($message, $premessages),
        ])->messageContent();
    }

    public function agentFinanceAnalyzeChat(string $message): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentFinanceAnalyzeMessages($message),
        ])->messageContent();
    }

    public function newAgentFinanceAnalyzeChat(string $context, string $deviceId): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->newAgentFinanceAnalyzeMessages($context, $deviceId),
        ])->messageContent();
    }

    public function agentOrchestrator(string $message, ?array $premessages = null): array
    {
        return $this->retry(function () use ($message, $premessages) {
            $response = $this->agentOrchestratorChat($message, $premessages);

            return $this->decodeOrchestratorResponse($response);
        });
    }

    public function agentFinanceAnalyze(string $message): FinanceAnalyzeResult
    {
        return $this->retry(function () use ($message) {
            $response = $this->agentFinanceAnalyzeChat($message);

            return $this->decodeFinanceAnalyzeResponse($response);
        });
    }

    protected function decodeOrchestratorResponse(string $response): array
    {
        $result = [];

        // Ambil semua blok seperti:
        // transaction_out [amount:60000; note:beli telur; date:2025-12-03]
        preg_match_all('/(\w+)\s*\[([^\]]+)\]/', $response, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $functionName = $match[1];
            $rawParams = $match[2];

            // Pecah "key:value; key:value"
            $params = [];
            foreach (explode(';', $rawParams) as $pair) {
                $pair = trim($pair);
                if ($pair === '') {
                    continue;
                }

                [$key, $value] = array_map('trim', explode(':', $pair, 2));

                // Optional: cast number
                if (is_numeric($value)) {
                    $value = $value + 0; // biar otomatis int/float
                }

                $params[$key] = $value;
            }

            $result[] = [
                'function' => $functionName,
                'param' => $params,
            ];
        }

        return $result;
    }

    protected function decodeFinanceAnalyzeResponse(string $response): FinanceAnalyzeResult
    {
        return FinanceAnalyzeResult::parse($response);
    }

    protected function retry(callable $fn, int $maxRetries = 2, int $delayMs = 200)
    {
        $attempt = 0;

        while (true) {
            try {
                return $fn();
            } catch (\Throwable $e) {
                $attempt++;

                if ($attempt > $maxRetries) {
                    throw $e; // give up after max retries
                }

                // optional: sleep before retry
                usleep($delayMs * 1000);
            }
        }
    }
}
