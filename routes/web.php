<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\KolosalChatController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'index'])->name('wellcome.index');

Route::get('/choose-your-setup', [PageController::class, 'chooseYourSetup'])->name('choose-your-setup.index');

Route::get('/chat', [PageController::class, 'chat'])->name('chat.index');

Route::get('/transactions', [PageController::class, 'transactions'])->name('transactions.index');

Route::post('/enter', [PageController::class, 'enter'])->name('enter');

Route::get('/test', [TestController::class, 'index'])->name('test.index');
Route::post('/kolosal/chat', [KolosalChatController::class, 'completions'])->name('kolosal.chat');

Route::controller(ChatController::class)->group(function () {
    // Route::get('/chat', 'index')->name('chat.index');
    Route::get('/messages/{user_id}', 'getMessages')->name('chat.messages');
    Route::post('/chat/send', 'sendMessage')->name('chat.send');
});
