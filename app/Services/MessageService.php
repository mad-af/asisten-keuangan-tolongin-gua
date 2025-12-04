<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function __construct(
        protected AgentService $agent,
        protected AgentChatService $agentChat
    ) {}

    public function send(Request $request)
    {
        $userMessage = $request->input('message');
        $deviceId = $request->input('device_id');

        // 1. Simpan pesan pengguna terlebih dahulu
        $userMessageModel = Message::create([
            'from' => $deviceId,
            'to' => 'agent',
            'body' => $userMessage,
        ]);

        // 2. Minta Orchestrator untuk menentukan tindakan
        $agentResponse = $this->agent->chat($userMessage);
        $functions = $agentResponse['data'] ?? [];

        $finalReply = 'Maaf, saya tidak bisa memproses permintaan Anda.';

        // 3. Proses setiap fungsi yang dikembalikan
        foreach ($functions as $funcCall) {
            $functionName = $funcCall['function'] ?? null;
            $params = $funcCall['args'] ?? [];

            if ($functionName === 'transaction_in' || $functionName === 'transaction_out') {
                // Simpan transaksi ke tabel transactions
                Transaction::create([
                    'device_id' => $deviceId,
                    'type' => $functionName === 'transaction_in' ? 'IN' : 'OUT',
                    'amount' => $params[0] ?? 0,
                    'note' => $params[1] ?? '',
                    'date' => $params[2] ?? now()->format('Y-m-d'),
                ]);

                // Balasan langsung dari persona (biasanya di funcCall terakhir)
                if (isset($params['result'])) {
                    $finalReply = $this->agentChat->agentPersonaChat($params['result'], null);
                }
            } elseif ($functionName === 'finance_analyze_chat') {
                $context = $params[0] ?? $userMessage;

                // Dapatkan query SQL dari Finance Analyzer
                $financeQueries = $this->agentChat->agentFinanceAnalyze($context);

                $analysisResults = [];
                foreach ($financeQueries as $q) {
                    // ✅ Tambahkan filter device_id ke setiap query
                    $sql = $this->injectDeviceIdFilter($q['sql'], $deviceId);
                    $data = DB::select($sql);
                    $analysisResults[] = [
                        'data' => $data,
                        'reason' => $q['reason'],
                    ];
                }

                // Format hasil untuk Persona
                $insightText = $this->formatFinanceResultsForPersona($analysisResults);

                // ✅ Kirim insight ke Persona Chat untuk diubah jadi bahasa Indonesia ramah
                $finalReply = $this->agentChat->agentPersonaChat($insightText, null);
            } elseif ($functionName === 'persona_chat') {
                // Biasanya ini fallback atau reply langsung
                $reason = $params['reason'] ?? 'Tidak ada penjelasan.';
                $finalReply = $this->agentChat->agentPersonaChat($reason, null);
            }
        }

        // 4. Simpan balasan agen
        Message::create([
            'from' => 'agent',
            'to' => $deviceId,
            'body' => $finalReply,
        ]);
    }

    /**
     * Suntikkan `WHERE device_id = ?` ke query SQL untuk keamanan.
     */
    private function injectDeviceIdFilter(string $sql, string $deviceId): string
    {
        // Pastikan hanya query SELECT
        if (!preg_match('/^\s*SELECT/i', $sql)) {
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
