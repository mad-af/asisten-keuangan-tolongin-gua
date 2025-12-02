<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KolosalChatController;

Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('api.kolosal.chat');

