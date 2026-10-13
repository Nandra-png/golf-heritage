<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course:slug}', [CourseController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{course:slug}', [CourseController::class, 'update']);
        Route::delete('/courses/{course:slug}', [CourseController::class, 'destroy']);

        Route::post('/courses/{course:slug}/reviews', [ReviewController::class, 'store']);
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    });
});
