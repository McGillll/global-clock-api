<?php

use App\Http\Controllers\CountdownController;
use Illuminate\Support\Facades\Route;

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
});

Route::get('shared/{token}', [CountdownController::class, 'sharedShow']);
