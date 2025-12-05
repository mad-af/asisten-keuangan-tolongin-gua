<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\KolosalChatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('api.kolosal.chat');

Route::post('/users/register', [UserController::class, 'register'])->name('api.users.register');
Route::post('/users/setup', [UserController::class, 'setup'])->name('api.users.setup');
Route::get('/users/me', [UserController::class, 'me'])->name('api.users.me');

Route::get('/messages/{user_id}', [ChatController::class, 'getMessagesByUserId'])->name('api.messages.user');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('api.chat.send');

Route::get('/transactions', [TransactionController::class, 'index'])->name('api.transactions.index');
Route::get('/transactions/cashflow', [TransactionController::class, 'cashflow'])->name('api.transactions.cashflow');
