<?php

use App\Http\Controllers\CountdownController;
use Illuminate\Support\Facades\Route;

// Auth endpoints
Route::post('auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('auth/register', [\App\Http\Controllers\AuthController::class, 'register']);

// Public shared view
Route::get('shared/{token}', [CountdownController::class, 'sharedShow']);

// Protected admin API (requires Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    // Logout
    Route::post('auth/logout', [\App\Http\Controllers\AuthController::class, 'authLogout']);
    Route::prefix('countdowns')->group(function () {
        Route::get('/', [CountdownController::class, 'index']);
        Route::post('/', [CountdownController::class, 'store']);
        Route::get('{countdown}', [CountdownController::class, 'show']);
        Route::put('{countdown}', [CountdownController::class, 'update']);
        Route::patch('{countdown}', [CountdownController::class, 'update']);
        Route::delete('{countdown}', [CountdownController::class, 'destroy']);
    });

    Route::prefix('sequences')->group(function () {
        Route::get('/', [CountdownController::class, 'sequenceIndex']);
        Route::post('/', [CountdownController::class, 'sequenceStore']);
        Route::get('{sequence}', [CountdownController::class, 'sequenceShow']);
        Route::put('{sequence}', [CountdownController::class, 'sequenceUpdate']);
        Route::patch('{sequence}', [CountdownController::class, 'sequenceUpdate']);
        Route::post('{sequence}/share', [CountdownController::class, 'shareSequence']);
        Route::post('{sequence}/start', [CountdownController::class, 'sequenceStart']);
        Route::post('{sequence}/pause', [CountdownController::class, 'sequencePause']);
        Route::post('{sequence}/resume', [CountdownController::class, 'sequenceResume']);
        Route::post('{sequence}/stop', [CountdownController::class, 'sequenceStop']);
    });
});
