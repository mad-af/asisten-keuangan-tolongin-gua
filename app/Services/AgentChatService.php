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
                'content' => "
You are the Orchestrator. Be concise. Use TOON-style outputs only. Do NOT add extra commentary.

Available functions:
- transaction_in: store an income transaction
- transaction_out: store an expense transaction
- persona_chat: user-facing conversational reply with persona
- report: generate chart image for user's transactions
- query: run SQL query on user's transactions DB
- suggestion: give financial suggestions

Decision rules (priority):
1. If user requests DB read/write, charts, or computations -> choose functions (tool).
2. If user asks for explanations or short advice -> persona_chat or suggestion (reply).
3. If request is multi-step or ambiguous -> produce plan (reason) then functions.
4. If uncertain, prefer a short plan (reason).

Output format (MANDATORY):
1) First line: '[K]: func1,func2,...' where K = number of functions chosen.

Rules:
- Always output only the TOON block (no explanation).
- Keep function list minimal and relevant.
- Use single-word function names as listed above.
End.
                ",
            ],
            [
                'role' => 'user',
                'content' => $message,
            ],
        ];
    }

    private function agentPersonaMessages(?string $message): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => `

                `,
            ],
        ];

        if ($message !== null) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $message,
            ];
        }

        return $messages;
    }

    public function agentOrchestratorChat(string $message): ChatResponse
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentOrchestratorMessages($message),
        ]);
    }

    public function agentPersonaChat(?string $message): ChatResponse
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentPersonaMessages($message),
        ]);
    }
}

// Fiture:
// - Transaction In: Mengelola transaksi keuangan pengguna masuk.
// - Transaction Out: Mengelola transaksi keuangan pengguna keluar.
// - Persona Chat: Mengelola percakapan dengan pengguna asisten keuangan.
// - Report: Membuat laporan keuangan pengguna.
// - Query: Membuat kueri keuangan pengguna.
// - Suggestion: Memberikan saran keuangan pengguna.


