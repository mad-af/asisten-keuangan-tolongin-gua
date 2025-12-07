<?php

use App\Http\Controllers\CacheController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\KolosalChatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ExtendTimeout;
use Illuminate\Support\Facades\Route;

Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('api.kolosal.chat');

Route::post('/users/register', [UserController::class, 'register'])->name('api.users.register');
Route::post('/users/setup', [UserController::class, 'setup'])->name('api.users.setup');
Route::get('/users/me', [UserController::class, 'me'])->name('api.users.me');

Route::get('/messages/{user_id}', [ChatController::class, 'getMessagesByUserId'])->name('api.messages.user');
Route::get('/messages/{user_id}/latest', [ChatController::class, 'getLatestMessageByUserId'])->name('api.messages.user.latest');
Route::post('/messages/{user_id}/fallback', [ChatController::class, 'createFallbackByUserId'])->name('api.messages.user.fallback');
Route::post('/chat/send', [ChatController::class, 'sendMessageByUser'])
    ->middleware(ExtendTimeout::class)
    ->name('api.chat.send');

Route::get('/cache/clear', [CacheController::class, 'clear'])->name('api.cache.clear');

Route::get('/transactions', [TransactionController::class, 'index'])->name('api.transactions.index');
Route::get('/transactions/cashflow', [TransactionController::class, 'cashflow'])->name('api.transactions.cashflow');
Route::get('/transactions/stats-month', [TransactionController::class, 'statsMonth'])->name('api.transactions.stats_month');
