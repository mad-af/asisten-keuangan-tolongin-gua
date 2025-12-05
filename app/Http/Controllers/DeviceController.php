<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Http\Request;
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

