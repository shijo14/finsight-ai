<?php

use App\Http\Controllers\Api\ChatApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes return JSON and are intended for programmatic / AJAX access.
| They use the default web (session) guard so the existing authentication
| works without extra setup. To enable token-based access, swap the guard
| to 'auth:sanctum' after installing Laravel Sanctum.
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    // Chatbot endpoints
    Route::get('/chat', [ChatApiController::class, 'index'])->name('api.chat.index');
    Route::post('/chat', [ChatApiController::class, 'store'])->name('api.chat.store');
    Route::delete('/chat', [ChatApiController::class, 'clear'])->name('api.chat.clear');
    Route::get('/chat/{id}', [ChatApiController::class, 'show'])->name('api.chat.show');
    Route::delete('/chat/{id}', [ChatApiController::class, 'destroy'])->name('api.chat.destroy');
});
