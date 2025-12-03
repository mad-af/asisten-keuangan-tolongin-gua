<?php

namespace App\Services;

class AgentChatService
{
    private KolosalApiClient $chatClient;
    
    public function __construct()
    {
        $this->chatClient = app(KolosalApiClient::class);
    }

    private function agentOrchestratorMessages(string $message): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'Anda adalah asisten keuangan yang membantu pengguna dalam mengelola keuangan mereka.',
            ],
            [
                'role' => 'user',
                'content' => $message,
            ],
        ];
    }

    private function agentPersonaMessages(string $message): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'Anda adalah asisten keuangan yang membantu pengguna dalam mengelola keuangan mereka.',
            ],
            [
                'role' => 'assitant',
                'content' => $message,
            ],
        ];
    }

    public function agentOrchestratorChat(string $message): ChatResponse
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentOrchestratorMessages($message),
        ]);
    }

    public function agentPersonaChat(string $message): ChatResponse
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentPersonaMessages($message),
        ]);
    }
}
