<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);

// Debug routes
Route::prefix('debug')->group(function () {
    Route::get('/', [DebugController::class, 'showEnvironment']);
    Route::prefix('env')->group(function () {
        Route::get('/', [DebugController::class, 'getEnvironmentInstructions']);
        Route::get('/{variable}', [DebugController::class, 'getEnvironmentVariable']);
    });
    Route::get('/lasterror', [DebugController::class, 'getLastError']);
    Route::get('/dir', [DebugController::class, 'mapProjectFiles']);
    Route::get('/file', [DebugController::class, 'getFileContent']);
    Route::post('/body', [DebugController::class, 'showBody']);
})->middleware(['dev.env']);

// User routes
Route::prefix('/user')->middleware(['db.safe'])->group(function () {
    Route::get('/', [UserController::class, 'get']);
    Route::post('/', [UserController::class, 'create']);
    Route::put('/', [UserController::class, 'update'])->middleware(['auth']);
    Route::prefix('/info')->middleware(['auth.basic'])->group(function () {
        Route::get('/me', [UserController::class, 'self']);
        Route::get('/{id}', [UserController::class, 'info']);
    });
    Route::get('/picture', function () {
        Route::post('/upload', [UserController::class, 'setPicture']);
    })->middleware(['auth']);
    Route::get('/exists', [UserController::class, 'exists']);
    Route::get('/auth', [UserController::class, 'authenticate'])->middleware(['auth.basic']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/resend-code', [UserController::class, 'resendCode'])->middleware(['auth.basic']);
});

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'getUserchat']);
});
Route::middleware(['auth'])->prefix('message')->group(function () {
    Route::get('/{chatUuid}', [MessageController::class, 'getmessage']); // Get message in a chat
    Route::post('/', [MessageController::class, 'sendMessage']); // Send a message
    Route::delete('/{id}', [MessageController::class, 'deleteMessage']); // Delete a message
});
