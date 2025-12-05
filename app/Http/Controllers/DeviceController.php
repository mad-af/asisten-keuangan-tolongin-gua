<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\Device;
use App\Models\Transaction;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;

class DeviceController extends Controller
{
    public function __construct(protected DeviceService $devices) {}

    public function register(Request $request)
    {
        $data = $request->validate([
            'device_id' => ['required', 'string'],
            'device_name' => ['required', 'string'],
            'device_info' => ['nullable', 'array'],
        ]);

        $device = $this->devices->registerOrUpdate($data['device_id'], $data['device_name'], $data['device_info'] ?? null);
        $token = $device->device_token;

        $cookie = Cookie::make('device_token', $token, 60 * 24 * 365, '/', null, true, true, false, 'Lax');

        return response()->json([
            'device_id' => $device->device_id,
            'device_name' => $device->device_name,
            'token' => $token,
        ])->withCookie($cookie);
    }

    public function dummySetup(Request $request)
    {
        $device = Device::firstOrNew(['device_id' => 'dummy-device']);
        $device->device_name = 'Dummy User';
        $device->device_info = ['mode' => 'dummy'];
        $device->device_token = $this->devices->generateToken();
        $device->last_seen = now();
        $device->save();

        Transaction::where('device_id', $device->device_id)->delete();

        $inNotes = [
            'Penjualan harian warung',
            'Pesanan online pelanggan',
            'Penjualan minuman dingin',
            'Penjualan roti & kue lokal',
            'Penjualan pulsa & paket data',
            'Penjualan sarapan nasi uduk',
            'Penjualan kopi tubruk',
            'Penjualan mie goreng',
            'Penjualan sembako',
            'Penjualan es teh',
        ];
        $outNotes = [
            'Pembelian stok sembako',
            'Beli bahan baku dapur',
            'Bayar listrik kios',
            'Isi ulang gas LPG',
            'Bayar air PDAM',
            'Pembelian kemasan plastik',
            'Beli es batu',
            'Bayar sewa ruko',
            'Gaji karyawan harian',
            'Perawatan peralatan dapur',
        ];

        $start = Carbon::now()->subMonths(6)->startOfDay();
        $end = Carbon::now()->startOfDay();
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $count = random_int(0, 3);
            for ($i = 0; $i < $count; $i++) {
                $isIn = random_int(0, 100) < 55; // ~55% pemasukan
                $note = $isIn
                    ? $inNotes[array_rand($inNotes)]
                    : $outNotes[array_rand($outNotes)];
                $amount = $isIn
                    ? random_int(25000, 350000)
                    : random_int(15000, 500000);

                Transaction::create([
                    'device_id' => $device->device_id,
                    'type' => $isIn ? TransactionType::IN : TransactionType::OUT,
                    'amount' => $amount,
                    'note' => $note,
                    'date' => $cursor->toDateString(),
                ]);
            }
            $cursor->addDay();
        }

        $token = $device->device_token;
        $cookie = Cookie::make('device_token', $token, 60 * 24 * 365, '/', null, true, true, false, 'Lax');

        return response()->json([
            'device_id' => $device->device_id,
            'device_name' => $device->device_name,
            'token' => $token,
        ])->withCookie($cookie);
    }

    public function revoke(Request $request)
    {
        /** @var \App\Models\Device|null $device */
        $device = $request->attributes->get('device');
        if (! $device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $this->devices->revoke($device);
        $forget = Cookie::forget('device_token', '/', null, true, true, false, 'Lax');

        return response()->json(['ok' => true])->withCookie($forget);
    }

    public function me(Request $request)
    {
        /** @var \App\Models\Device|null $device */
        $device = $request->attributes->get('device');
        if (! $device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'device_id' => $device->device_id,
            'device_name' => $device->device_name,
        ]);
    }
}
