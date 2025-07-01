<?php

use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegistrationController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\PreferenceController;
use App\Http\Controllers\Api\V1\SourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Routes
    Route::post('/register', RegistrationController::class)->name('register');
    Route::post('/login', [LoginController::class, 'store'])->name('login');
    Route::post('/forgot-password', ForgotPasswordController::class)->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])
        ->middleware('guest')
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');

    Route::get('/ping', function () {
        return ['message' => 'pong'];
    });

    // Protected Routes
    Route::middleware(['throttle:60,1', 'auth:sanctum'])->group(function () {
        Route::get('/me', function () {
            return response()->json(['data' => auth()->user(), 'message' => 'User retrieved successfully']);
        });

        Route::delete('/logout', [LoginController::class, 'destroy']);

        Route::get('/articles', [ArticleController::class, 'index']);
        Route::get('/articles/{article}', [ArticleController::class, 'show']);

        Route::get('/sources', SourceController::class);
        Route::get('/categories', CategoryController::class);
        Route::get('/authors', AuthorController::class);
        Route::get('/preferences', [PreferenceController::class, 'show']);
        Route::patch('/preferences', [PreferenceController::class, 'update']);
    });
});
