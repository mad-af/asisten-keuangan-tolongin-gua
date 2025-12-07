<?php

namespace App\Services;

use App\Models\Message;
use App\Models\OrchestratorMemory;
use Illuminate\Support\Facades\Log;

class MemoryService
{
    public function __construct(protected AgentChatService $agentChat) {}

    public function summarizeAndStoreByUserId(string|int $userId): ?OrchestratorMemory
    {
        $messages = Message::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse();
        $premessages = $messages->map(function (Message $m) {
            $role = $m->type->value ?? (string) $m->type;

            return ['role' => $role, 'content' => (string) $m->body];
        })->all();

        if (count($premessages) < 2) {
            return null;
        }

        try {
            $summary = $this->agentChat->agentMemoryChat($premessages);

            $memory = OrchestratorMemory::updateOrCreate(
                ['user_id' => $userId],
                [
                    'content' => "Previous conversation context:\n".(string) $summary,
                    'metadata' => [
                        'message_count' => count($premessages),
                        'updated_at' => now()->toIso8601String(),
                        'source' => 'api.memory.summarize',
                    ],
                ],
            );

            Log::info('memory_summarize_store', ['user_id' => (string) $userId, 'length' => strlen((string) $summary)]);
        } catch (\Exception $e) {
            Log::error('memory_summarize_store_error', ['user_id' => (string) $userId, 'error' => $e->getMessage()]);
        }

        return $memory;
    }
}
