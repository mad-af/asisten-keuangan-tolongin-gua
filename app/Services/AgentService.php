<?php

namespace App\Services;

class AgentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AgentChatService $agentChat, protected AgentToolService $agentTool) {}

    public function chat(string $message): array
    {
        $response = $this->agentChat->agentOrchestratorChat($message);
        $orchestrator = $this->decodeOrchestratorResponse($response);

        $this->agentTool->call($orchestrator);

        return [];
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
}
