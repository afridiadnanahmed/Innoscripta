<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('password/forgot', [PasswordResetController::class, 'sendResetLink'])->name('password-forgot');
Route::post('password/reset', [PasswordResetController::class, 'resetPassword'])->name('password-reset');

// NewsAPI
Route::get('fetch-news', [NewsController::class, 'fetchNewsFromNewsApi']);
// NYT API
Route::get('fetch-nyt-news', [NewsController::class, 'fetchNewsFromNYT']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/articles', [ArticleController::class, 'getPaginatedArticles']);
    Route::get('/articles/search', [ArticleController::class, 'search']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
}); 