<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Str;

class DeviceService
{
    public function generateToken(): string
    {
        return hash('sha256', Str::random(64) . microtime());
    }

    public function registerOrUpdate(string $deviceId, string $name, ?array $info = null): Device
    {
        $device = Device::firstOrNew(['device_id' => $deviceId]);
        $device->device_name = $name;
        if ($info !== null) {
            $device->device_info = $info;
        }
        $device->device_token = $this->generateToken();
        $device->last_seen = now();
        $device->save();
        return $device;
    }

    public function getByToken(?string $token): ?Device
    {
        if (! $token) return null;
        return Device::where('device_token', $token)->first();
    }

    public function revoke(Device $device): void
    {
        $device->device_token = null;
        $device->save();
    }
}

