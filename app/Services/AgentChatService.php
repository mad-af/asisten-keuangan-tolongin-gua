<?php

namespace App\Services;

class AgentChatService
{

    public function __construct(protected KolosalApiClient $chatClient)
    {
     
    }

    private function agentOrchestratorMessages(string $message): array
    {
        return [
            [
                'role' => 'system',
                'content' => "
You are the Orchestrator. Be concise. Use TOON-style outputs only. Do NOT add extra commentary.

Available functions:
- transaction_in(int amount, string note, string date)
  > Store an income transaction with amount, note, and a strict date (YYYY-MM-DD).
- transaction_out(int amount, string note, string date)
  > Store an expense transaction with amount, note, and a strict date (YYYY-MM-DD).
- persona_chat(string reason)
  > Generate a natural, user-facing reply using the reasoning summary provided.
- finance_analyze_chat(string context)
  > Provide financial insights or explanations based on the given analysis context.

Date rules:
- Use ISO date format YYYY-MM-DD for all date parameters.
- If user omits date, default to today's date in YYYY-MM-DD.
- Acknowledge that your training data is not current. The current date is ".date('Y-m-d').". Always use this date when referring to 'today'

Decision rules (priority):
1. If user requests DB read/write, charts, or computations -> choose functions (tool).
2. If user asks for explanations or short advice -> persona_chat or suggestion (reply).
3. If request is multi-step or ambiguous -> produce plan (reason) then functions.
4. If uncertain, prefer a short plan (reason).

Output format (MANDATORY):
1) The first line MUST ALWAYS end with persona_chat [reason:...]. Example: '[K]: func1 [key:value],func2 [key:value],...,persona_chat [reason:...]' where K is the exact total number of functions listed in the first line, including persona_chat as the final function.
2) Each function must include parameters inside square brackets: func_name [key:value; key:value].
3) If a function requires complex parameters, use a TOON object: func_name [{key:value; key2:value2}].
4) Persona_chat is mandatory and must always be the final function in the list.
5) The 'reason' parameter must be a full English summary of all reasoning and actions taken by the Orchestrator.

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
                'content' => '
                ',
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

    private function agentFinanceAnalyzeMessages(?string $message): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => '
                ',
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

    public function agentOrchestratorChat(string $message): String
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentOrchestratorMessages($message),
        ])->messageContent();
    }

    public function agentPersonaChat(?string $message): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentPersonaMessages($message),
        ])->messageContent();
    }

    public function agentFinanceAnalyzeChat(?string $message): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentFinanceAnalyzeMessages($message),
        ])->messageContent();
    }
}

