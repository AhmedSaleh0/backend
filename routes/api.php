<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InspireController;
use App\Http\Controllers\ICanController;
use App\Http\Controllers\INeedController;
use App\Http\Controllers\CreditsController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSkillController;

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});

Route::middleware('auth:api')->group(function () {
    Route::put('/user/{id}', [UserController::class, 'updateUser']);
    Route::delete('/user/{id}', [UserController::class, 'deleteUser']);
});

// User Images Routes
Route::prefix('user-images')->middleware('auth:api')->group(function () {
    Route::get('/', [UserImageController::class, 'index']);
    Route::post('/', [UserImageController::class, 'store']);
    Route::get('/{user_image}', [UserImageController::class, 'show']);
    Route::put('/{user_image}', [UserImageController::class, 'update']);
    Route::delete('/{user_image}', [UserImageController::class, 'destroy']);
});

// User Skills Routes
Route::apiResource('user-skills', UserSkillController::class);

// Inspire Routes
Route::prefix('inspire')->middleware('auth:api')->group(function () {
    Route::get('/posts', [InspireController::class, 'index']);
    Route::post('/posts', [InspireController::class, 'store']);
    Route::get('/posts/{id}', [InspireController::class, 'show']);
    Route::put('/posts/{id}', [InspireController::class, 'update']);
    Route::delete('/posts/{id}', [InspireController::class, 'destroy']);
});

// I-Can Routes
Route::prefix('ican')->middleware('auth:api')->group(function () {
    Route::get('/posts', [ICanController::class, 'index']);
    Route::post('/posts', [ICanController::class, 'store']);
    Route::get('/posts/{id}', [ICanController::class, 'show']);
    Route::put('/posts/{id}', [ICanController::class, 'update']);
    Route::delete('/posts/{id}', [ICanController::class, 'destroy']);
});

// I-Need Routes
Route::prefix('ineed')->middleware('auth:api')->group(function () {
    Route::get('/posts', [INeedController::class, 'index']);
    Route::post('/posts', [INeedController::class, 'store']);
    Route::get('/posts/{id}', [INeedController::class, 'show']);
    Route::put('/posts/{id}', [INeedController::class, 'update']);
    Route::delete('/posts/{id}', [INeedController::class, 'destroy']);
});

// Credits and Payments
Route::prefix('credits')->middleware('auth:api')->group(function () {
    Route::get('/', [CreditsController::class, 'balance']);
    Route::post('/add', [CreditsController::class, 'addCredits']);
    Route::post('/deduct', [CreditsController::class, 'deductCredits']);
    Route::post('/purchase', [CreditsController::class, 'purchaseCredits']);
});


// Skills Routes
Route::apiResource('skills', SkillController::class)->middleware('auth:api');
