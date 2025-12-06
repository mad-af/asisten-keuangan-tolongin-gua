<?php

namespace App\Services;

use App\Enums\MessageType;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageService
{
    public function __construct(
        protected AgentService $agent
    ) {}

    public function sendByUser(Request $request)
    {
        $userMessage = $request->input('message');
        $userId = $request->input('user_id');

        Message::create([
            'user_id' => $userId,
            'body' => $userMessage,
            'type' => MessageType::user,
        ]);

        $this->assistantReply($userMessage, $userId);
    }

    public function getByUserId(string|int $userId)
    {
        return Message::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function latestByUserId(string|int $userId)
    {
        return Message::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    protected function assistantReply(string $message, string $userId)
    {
        sleep(1);
        $responseMessage = "Maaf, saya tidak mengerti. Silakan coba lagi.";
        $response = $this->agent->chat($message, $userId);
        if (!empty($response)) {
            $responseMessage = $response->personaChat();
        }

        Message::create([
            'user_id' => $userId,
            'body' => $responseMessage,
            'type' => MessageType::assistant,
        ]);
    }

    /**
     * Suntikkan `WHERE device_id = ?` ke query SQL untuk keamanan.
     */
    private function injectDeviceIdFilter(string $sql, string $deviceId): string
    {
        // Pastikan hanya query SELECT
        if (! preg_match('/^\s*SELECT/i', $sql)) {
            throw new \InvalidArgumentException('Hanya query SELECT yang diizinkan.');
        }

        // Tambahkan WHERE device_id = ?
        if (stripos($sql, 'WHERE') !== false) {
            $sql = preg_replace('/\bWHERE\b/i', "WHERE device_id = '$deviceId' AND", $sql, 1);
        } else {
            $sql .= " WHERE device_id = '$deviceId'";
        }

        return $sql;
    }

    /**
     * Ubah hasil query jadi teks ringkas untuk Persona Chat.
     */
    private function formatFinanceResultsForPersona(array $results): string
    {
        if (empty($results)) {
            return 'Tidak ada data keuangan ditemukan.';
        }

        $lines = [];
        foreach ($results as $r) {
            $data = $r['data'];
            if (empty($data)) {
                continue;
            }

            // Ambil baris pertama dan kolom apa saja
            $row = (array) $data[0];
            if (count($row) === 1) {
                $value = reset($row);
                $lines[] = (string) $value;
            } else {
                $lines[] = json_encode($row, JSON_UNESCAPED_UNICODE);
            }
        }

        return implode("\n", $lines) ?: 'Tidak ada data yang sesuai.';
    }
}
