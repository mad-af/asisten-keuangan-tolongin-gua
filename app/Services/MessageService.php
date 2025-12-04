<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Message;
use App\Models\Transaction;

class MessageService
{
    public function __construct(
        protected AiParse $parser
    ) {}

    public function message(Request $request): array
    {
        // 1. Find or create device
        $device = Device::firstOrCreate(
            ['device_id' => $request->device_id],
            ['name' => $request->device_name ?? null]
        );

        // 2. Store raw message
        $msg = Message::create([
            'device_id' => $device->id,
            'body'      => $request->message,
            'type'      => 'text',
        ]);

        // 3. Parse using AI
        $parsed = $this->parser->parse($request->message);

        if ($parsed && isset($parsed['amount'], $parsed['type'])) {
            Transaction::create([
                'device_id'   => $device->id,
                'message_id'  => $msg->id,
                'amount'      => $parsed['amount'],
                'currency'    => $parsed['currency'] ?? 'IDR',
                'type'        => $parsed['type'],
                'description' => $parsed['description'] ?? null,
                'date'        => $parsed['date'] ?? now()->toDateString(),
                'raw_parsed'  => $parsed,
            ]);
        }

        return [
            'device'   => $device,
            'messages' => Message::where('device_id', $device->id)->orderBy('created_at')->get(),
        ];
    }
}
