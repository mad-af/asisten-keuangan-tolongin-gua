<?php

namespace App\Http\Middleware;

use App\Services\DeviceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceAuth
{
    public function __construct(protected DeviceService $devices) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('device_token');
        $device = $this->devices->getByToken($token);
        if (! $device) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->attributes->set('device', $device);
        return $next($request);
    }
}

