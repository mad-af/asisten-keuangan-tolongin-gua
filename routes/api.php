<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KolosalChatController;
use App\Http\Controllers\DeviceController;

Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('api.kolosal.chat');

Route::post('/devices/register', [DeviceController::class, 'register']);
Route::post('/devices/dummy-setup', [DeviceController::class, 'dummySetup']);
Route::middleware('device.auth')->group(function () {
    Route::post('/devices/revoke', [DeviceController::class, 'revoke']);
    Route::get('/devices/me', [DeviceController::class, 'me']);
});
