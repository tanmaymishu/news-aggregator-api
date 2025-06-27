<?php

use App\Http\Controllers\Api\V1\PersonalizedArticleController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegistrationController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\SourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Routes
    Route::post('/register', RegistrationController::class)->name('register');
    Route::post('/login', [LoginController::class, 'store'])->name('login');

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/ping', function () {
            return ['message' => 'pong'];
        });

        Route::delete('/logout', [LoginController::class, 'destroy']);

        Route::get('/articles', [PersonalizedArticleController::class, 'index']);
        Route::get('/sources', SourceController::class);
        Route::get('/categories', CategoryController::class);
        Route::get('/authors', AuthorController::class);
    });
});
