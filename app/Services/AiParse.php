<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AiParse
{
    public function __construct(
        protected KolosalApiClient $kolosal
    ) {}

    public function parse(string $text): ?array
    {
        try {
            $prompt = $this->buildPrompt($text);

            $response = $this->kolosal->chatCompletions([
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 300,
            ]);

            // Jika API error
            if (! $response->isOk()) {
                Log::error('AI parse failed: ' . $response->getError());
                return null;
            }

            // Ambil message konten
            $content = $response->messages()[0]['content'] ?? null;

            if (! $content) {
                return null;
            }

            // Decode langsung
            $json = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }

            // Extract JSON dari teks
            if (preg_match('/\{.*\}/s', $content, $m)) {
                $json2 = json_decode($m[0], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $json2;
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('AI parse exception: ' . $e->getMessage());
            return null;
        }
    }

    protected function buildPrompt(string $text): string
    {
        return "
Convert the following user message into JSON with fields:
type: 'income' or 'expense'
amount: number (integer)
currency: 'IDR'
description: short description
date: yyyy-mm-dd or null

Message: \"{$text}\"

Respond with ONLY valid JSON.
";
    }
}
