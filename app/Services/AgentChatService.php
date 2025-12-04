<?php

namespace App\Services;

class AgentChatService
{
    public function __construct(protected KolosalApiClient $chatClient) {}

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
  > Analyze user's financial data by reading the transaction table and return insights based on the context.

Date rules:
- Use ISO date format YYYY-MM-DD for all date parameters.
- If user omits date, default to today's date in YYYY-MM-DD.
- Acknowledge that your training data is not current. The current date is " . date('Y-m-d') . ". Always use this date when referring to 'today'

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

    private function agentPersonaMessages(?string $message, ?array $premessages): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => '
You are a friendly financial assistant designed for small business owners who may not understand bookkeeping. You communicate naturally, simply, and supportively, as if chatting on WhatsApp. Your goal is to help users understand their finances, answer questions, and provide clear insights based on the context or the CSV financial data provided (if any).

Behavior Guidelines:
* Always reply **in Indonesian**, using a warm, clear, conversational tone.
* Keep answers **short, direct, and easy to understand**. Avoid jargon.
* Assume the user may not be familiar with financial concepts—explain things in a simple way when needed.
* If the user provides financial data or mentions transactions, you may offer insights, summaries, or suggestions.
* If CSV data is provided, read it carefully and base your response strictly on the data.
* If no data is provided, still try to be helpful based on the user’s question.
* Be supportive, non-judgmental, and practical.
* You do not execute functions or tools; you simply generate natural conversational replies.

Reasoning:
* You will receive a reasoning summary before responding.
* Use the reasoning to guide your answer, but **never show the reasoning** to the user.
* Your job is ONLY to produce a polished, friendly, Indonesian-language reply for the end-user.

Tone & Style:
* Warm, human, and approachable.
* Sounds like a helpful WhatsApp assistant.
* No long paragraphs; break ideas into small, digestible lines if needed.
* Keep things positive and solution-oriented.

What to Avoid:
* No technical explanations unless asked.
* No robotic or overly formal responses.
* No TOON formatting or tool-call syntax.
* No English in final replies (unless the user asks for it).
End.
                ',
            ],
        ];

        if ($premessages !== null) {
            $finalpremessages = array_map(function ($msg) {
                return [
                    'role' => 'assistant',
                    'content' => $msg,
                ];
            }, $premessages);
            $messages = array_merge($messages, $finalpremessages);
        }

        if ($message !== null) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $message,
            ];
        }

        return $messages;
    }

    private function agentFinanceAnalyzeMessages(string $message): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => "
You are the Finance Analyzer. Be extremely concise. Your job is to read transaction data from the database and produce accurate SQL queries that fetch exactly what the Orchestrator needs.

Inputs:
- 'context': a natural-language instruction telling what the Orchestrator is trying to decide or analyze.
- Access: READ-only access to table `transactions`. No inserts/updates/deletes.

Schema:
- Table: transactions
- Columns: id, type ('IN' or 'OUT'), amount (integer), note (string), date (string YYYY-MM-DD)

Responsibilities:
1. Interpret 'context' and determine required financial data.
2. Produce one or more strict SELECT SQL queries (no pseudocode).
3. For each query produce a one-line concise reason (1 sentence max).
4. If context is unclear, return a short clarification question instead of guessing.
5. Never perform orchestration, tool calls, TOON formats, or direct replies to the user.

Rules:
- Only SELECT queries allowed.
- Use ISO dates (YYYY-MM-DD) when filtering.
- Acknowledge that your training data is not current. The current date is " . date('Y-m-d') . ". Always use this date when referring to 'today'
- Keep reasoning short and precise.
- Output MUST follow this exact custom format (no JSON):

If N queries are returned, output exactly:

[N]{sql;reason}:
    QUERY_SQL_1;reason_1
    QUERY_SQL_2;reason_2
    ...
    QUERY_SQL_N;reason_N

Notes on format:
- N is the integer count of queries (e.g., 1, 2, 3).
- Each line after the header contains the SQL statement, then a semicolon (`;`), then the concise reasoning.
- SQL must be single-line or semicolon-terminated if using multiple statements; avoid comments.
- Do not add extra text before/after the block.

Examples (FORMAT ONLY — do not copy the SQL content):

Single query:
[1]{sql;reason}:
    SELECT ... ;short reason explaining why this query answers the context

Multiple queries:
[2]{sql;reason}:
    SELECT ... ;reason for query 1
    SELECT ... ;reason for query 2

Note: Examples illustrate ONLY the output structure, NOT the logic or SQL content.
Always ensure the header number N matches the count of query lines that follow.
End.
                ",
            ],
            [
                'role' => 'assistant',
                'content' => $message,
            ],
        ];

        return $messages;
    }

    public function agentOrchestratorChat(string $message): string
    {
        return $this->chatClient->chatCompletions([
            'max_tokens' => 1000,
            'messages' => $this->agentOrchestratorMessages($message),
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

    public function agentOrchestrator(string $message): array
    {
        return $this->retry(function () use ($message) {
            $response = $this->agentOrchestratorChat($message);
            return $this->decodeOrchestratorResponse($response);
        });
    }

    public function agentFinanceAnalyze(string $message): array
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

    protected function decodeFinanceAnalyzeResponse(string $response): array
    {
        $lines = preg_split("/\r\n|\n|\r/", trim($response));

        // skip header
        array_shift($lines);

        $queries = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            [$sql, $reason] = explode(';', $line, 2);

            $queries[] = [
                'sql' => trim($sql),
                'reason' => trim($reason),
            ];
        }

        return $queries;
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
