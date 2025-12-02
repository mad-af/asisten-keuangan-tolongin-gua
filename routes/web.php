<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\KolosalChatController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('TestPage', [
        'serverTime' => now()->toIso8601String(),
        'message' => 'Welcome via Inertia',
        'user' => [
            'id' => request()->user()?->id,
            'name' => request()->user()?->name ?? 'Guest',
            'email' => request()->user()?->email,
        ],
    ]);
});

Route::get('/test', [TestController::class, 'index'])->name('test.index');
Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('kolosal.chat');
