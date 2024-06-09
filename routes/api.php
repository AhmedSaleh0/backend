<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\INeed\INeedController;
use App\Http\Controllers\INeed\INeedRequestController;
use App\Http\Controllers\INeed\INeedReactionController;
use App\Http\Controllers\ICan\ICanController;
use App\Http\Controllers\Inspire\InspireController;
use App\Http\Controllers\Inspire\InspireCommentController;
use App\Http\Controllers\Inspire\InspireReactionController;
use App\Http\Controllers\Inspire\InspireUserSaveController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserImageController;
use App\Http\Controllers\User\UserSkillController;
use App\Http\Controllers\Communications\ContactController;
use App\Http\Controllers\Communications\NewsletterController;
use App\Http\Controllers\Credits\CreditsController;
use App\Http\Controllers\ICan\ICanReactionController;
use App\Http\Controllers\ICan\ICanRequestController;
use App\Http\Controllers\Skill\SkillController;


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
Route::prefix('ican')->group(function () {
    Route::get('/posts', [ICanController::class, 'index']);
    Route::post('/posts', [ICanController::class, 'store']);
    Route::get('/posts/{id}', [ICanController::class, 'show']);
    Route::put('/posts/{id}', [ICanController::class, 'update']);
    Route::delete('/posts/{id}', [ICanController::class, 'destroy']);

    Route::prefix('requests')->middleware('auth:api')->group(function () {
        Route::get('/', [ICanRequestController::class, 'index'])->name('ican-requests.index');
        Route::post('/apply', [ICanRequestController::class, 'apply'])->name('ican-requests.apply');
        Route::post('/{request_id}/accept', [ICanRequestController::class, 'accept'])->name('ican-requests.accept');
        Route::post('/{request_id}/reject', [ICanRequestController::class, 'reject'])->name('ican-requests.reject');
        Route::get('/{request_id}', [ICanRequestController::class, 'show'])->name('ican-requests.show');
        Route::delete('/{request_id}', [ICanRequestController::class, 'destroy'])->name('ican-requests.destroy');
    });

    Route::prefix('reactions')->group(function () {
        Route::get('/{ican_id}', [ICanReactionController::class, 'index']);
        Route::post('/{ican_id}', [ICanReactionController::class, 'store'])->middleware('auth:api');
        Route::get('/{id}', [ICanReactionController::class, 'show']);
        Route::put('/{id}', [ICanReactionController::class, 'update'])->middleware('auth:api');
        Route::delete('/{id}', [ICanReactionController::class, 'destroy'])->middleware('auth:api');
    });
});

// I-Need Routes
Route::prefix('ineed')->group(function () {
    // Public routes
    Route::get('/posts', [INeedController::class, 'index']);
    Route::get('/posts/{id}', [INeedController::class, 'show']);

    // Routes requiring authentication
    Route::middleware('auth:api')->group(function () {
        Route::post('/posts', [INeedController::class, 'store']);
        Route::put('/posts/{id}', [INeedController::class, 'update']);
        Route::delete('/posts/{id}', [INeedController::class, 'destroy']);

        Route::prefix('requests')->group(function () {
            // INeedRequest routes
            Route::get('/', [INeedRequestController::class, 'index'])->name('ineed-requests.index');
            Route::post('/apply', [INeedRequestController::class, 'apply'])->name('ineed-requests.apply');
            Route::post('/{request_id}/accept', [INeedRequestController::class, 'accept'])->name('ineed-requests.accept');
            Route::post('/{request_id}/reject', [INeedRequestController::class, 'reject'])->name('ineed-requests.reject');
            Route::get('/{request_id}', [INeedRequestController::class, 'show'])->name('ineed-requests.show');
            Route::delete('/{request_id}', [INeedRequestController::class, 'destroy'])->name('ineed-requests.destroy');
        });
    });

    // Reactions routes
    Route::prefix('reactions')->group(function () {
        Route::get('/{ineed_id}', [INeedReactionController::class, 'index']);
        Route::get('/{id}', [INeedReactionController::class, 'show']);

        // Routes requiring authentication
        Route::middleware('auth:api')->group(function () {
            Route::post('/{ineed_id}', [INeedReactionController::class, 'store']);
            Route::put('/{id}', [INeedReactionController::class, 'update']);
            Route::delete('/{id}', [INeedReactionController::class, 'destroy']);
        });
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
