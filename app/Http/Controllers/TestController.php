<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('TestPage', [
            'serverTime' => now()->toIso8601String(),
            'message' => 'Hello from Laravel',
            'user' => [
                'id' => $request->user()?->id,
                'name' => $request->user()?->name ?? 'Guest',
                'email' => $request->user()?->email,
            ],
        ]);
    }
}
