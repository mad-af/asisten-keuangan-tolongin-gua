<?php

use App\Http\Controllers\KolosalChatController;
use Illuminate\Support\Facades\Route;

Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('api.kolosal.chat');

