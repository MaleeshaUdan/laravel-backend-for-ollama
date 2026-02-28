<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return redirect('/chat');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    
    Route::post('/api/sessions', [ChatController::class, 'createSession']);
    Route::put('/api/sessions/{session}', [ChatController::class, 'updateSession']);
    Route::delete('/api/sessions/{session}', [ChatController::class, 'deleteSession']);
    Route::get('/api/sessions/{session}/messages', [ChatController::class, 'getSessionMessages']);
    Route::post('/api/sessions/{session}/message', [ChatController::class, 'sendMessage']);
});
