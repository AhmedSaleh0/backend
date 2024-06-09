<?php

use App\Http\Controllers\Auth\SocialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InspireController;
use App\Http\Controllers\ICanController;
use App\Http\Controllers\INeedController;
use App\Http\Controllers\CreditsController;
use App\Http\Controllers\INeedRequestController;
use App\Http\Controllers\InspireCommentController;
use App\Http\Controllers\InspireReactionController;
use App\Http\Controllers\InspireUserSaveController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSkillController;


// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

    // Password reset routes
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp']); // Updated to send OTP
    Route::post('/verify-otp', [AuthController::class, 'verifyResetOtp']); // New route to verify OTP
    Route::post('/reset-password', [AuthController::class, 'resetPassword']); // Updated to reset password using OTP
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');

    Route::get('/facebook', [SocialController::class, 'redirectToFacebook']);
    Route::get('/facebook/callback', [SocialController::class, 'handleFacebookCallback']);
    Route::post('/facebook/data-deletion', [SocialController::class, 'dataDeletionRequest']);

    Route::get('/google', [SocialController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [SocialController::class, 'handleGoogleCallback']);
});

// User Routes (Require authentication)
Route::middleware('auth:api')->group(function () {
    Route::get('/user/details', [UserController::class, 'getUserDetails']);
    Route::put('/user', [UserController::class, 'updateUser']);
    Route::put('/user/username', [UserController::class, 'updateUsername']);
});

Route::post('/user/profile', [UserController::class, 'updateUserProfile'])->middleware('auth:api');

// User Images Routes
Route::prefix('user-images')->middleware('auth:api')->group(function () {
    Route::get('/', [UserImageController::class, 'index']);
    Route::post('/', [UserImageController::class, 'store']);
    Route::get('/{user_image}', [UserImageController::class, 'show']);
    Route::put('/{user_image}', [UserImageController::class, 'update']);
    Route::delete('/{user_image}', [UserImageController::class, 'destroy']);
});

// User Skills Routes
Route::apiResource('user-skills', UserSkillController::class)->middleware('auth:api');

// Inspire Routes
Route::prefix('inspire')->group(function () {
    // Public routes
    Route::get('/posts', [InspireController::class, 'index']);
    Route::get('/posts/{inspire_id}/comments', [InspireCommentController::class, 'index']);
    Route::get('/posts/{inspire_id}/reactions', [InspireReactionController::class, 'index']);

    // Routes requiring authentication
    Route::middleware('auth:api')->group(function () {
        Route::post('/posts', [InspireController::class, 'store']);
        Route::get('/posts/{id}', [InspireController::class, 'show']);
        Route::put('/posts/{id}', [InspireController::class, 'update']);
        Route::delete('/posts/{id}', [InspireController::class, 'destroy']);

        // Comments routes
        Route::post('/posts/{inspire_id}/comments', [InspireCommentController::class, 'store']);
        Route::get('/comments/{id}', [InspireCommentController::class, 'show']);
        Route::put('/comments/{id}', [InspireCommentController::class, 'update']);
        Route::delete('/comments/{id}', [InspireCommentController::class, 'destroy']);

        // Reactions routes
        Route::post('/posts/{inspire_id}/reactions', [InspireReactionController::class, 'store']);
        Route::get('/reactions/{id}', [InspireReactionController::class, 'show']);
        Route::put('/reactions/{id}', [InspireReactionController::class, 'update']);
        Route::delete('/reactions/{id}', [InspireReactionController::class, 'destroy']);

        // User saves routes
        Route::get('/user/saves', [InspireUserSaveController::class, 'index']);
        Route::post('/posts/{inspire_id}/save', [InspireUserSaveController::class, 'store']);
        Route::delete('/saves/{id}', [InspireUserSaveController::class, 'destroy']);
    });
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

    Route::prefix('requests')->middleware('auth:api')->group(function () {
        // INeedRequest routes
        Route::get('/', [INeedRequestController::class, 'index'])->name('ineed-requests.index');
        Route::post('/apply', [INeedRequestController::class, 'apply'])->name('ineed-requests.apply');
        Route::post('/{id}/accept', [INeedRequestController::class, 'accept'])->name('ineed-requests.accept');
        Route::post('/{id}/reject', [INeedRequestController::class, 'reject'])->name('ineed-requests.reject');
        Route::get('/{id}', [INeedRequestController::class, 'show'])->name('ineed-requests.show');
        Route::delete('/{id}', [INeedRequestController::class, 'destroy'])->name('ineed-requests.destroy');
    });
});

// Credits and Payments
Route::prefix('credits')->middleware('auth:api')->group(function () {
    Route::get('/', [CreditsController::class, 'balance']);
    Route::post('/add', [CreditsController::class, 'addCredits']);
    Route::post('/deduct', [CreditsController::class, 'deductCredits']);
    Route::post('/purchase', [CreditsController::class, 'purchaseCredits']);
});

// Skills Routes
Route::prefix('skills')->middleware('auth:api')->group(function () {
    Route::get('/', [SkillController::class, 'index']);
    Route::post('/', [SkillController::class, 'store']);
    Route::get('/{id}', [SkillController::class, 'show']);
    Route::put('/{id}', [SkillController::class, 'update']);
    Route::delete('/{id}', [SkillController::class, 'destroy']);
});
// Newsletter Routes
Route::prefix('newsletter')->middleware('api')->group(function () {
    Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
});

// Contact Routes
Route::prefix('contact')->middleware('api')->group(function () {
    Route::post('/send', [ContactController::class, 'send'])->name('contact.send');
});
