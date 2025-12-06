<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExtendTimeout
{
    public function handle(Request $request, Closure $next)
    {
        try {
            @set_time_limit(60);
            if (function_exists('ini_set')) {
                @ini_set('max_execution_time', '60');
            }
        } catch (\Throwable $e) {}
        return $next($request);
    }
}

