<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\OptionalAuth;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Common\RatingController;
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
use App\Http\Controllers\Skill\SkillQueryController;
use App\Http\Controllers\Communications\ConversationController;
use App\Http\Controllers\Communications\MessageController;

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

    // Password reset routes
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyResetOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');

    Route::get('/facebook', [SocialController::class, 'redirectToFacebook']);
    Route::get('/facebook/callback', [SocialController::class, 'handleFacebookCallback']);
    Route::post('/facebook/data-deletion', [SocialController::class, 'dataDeletionRequest']);

    Route::get('/google', [SocialController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [SocialController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->middleware('throttle:6,1')
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['auth:api', 'signed'])
        ->name('verification.verify');

    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('/details', [UserController::class, 'getUserDetails']);
    Route::get('/{user_id}', [UserController::class, 'getUserById']); 
    Route::put('/', [UserController::class, 'updateUser']);
    Route::put('/username', [UserController::class, 'updateUsername']);
    Route::post('/profile', [UserController::class, 'updateUserProfile']);

    // User Images Routes
    Route::prefix('images')->group(function () {
        Route::get('/', [UserImageController::class, 'index']);
        Route::post('/', [UserImageController::class, 'store']);
        Route::get('/{user_image}', [UserImageController::class, 'show']);
        Route::put('/{user_image}', [UserImageController::class, 'update']);
        Route::delete('/{user_image}', [UserImageController::class, 'destroy']);
    });

    // User Skills Routes
    Route::prefix('skills')->group(function () {
        Route::get('/', [UserSkillController::class, 'index'])->name('user-skills.index');
        Route::post('/', [UserSkillController::class, 'store'])->name('user-skills.store');
        Route::delete('/{user_skill}', [UserSkillController::class, 'destroy'])->name('user-skills.destroy');
    });
});

// Inspire Routes
Route::prefix('inspire')->group(function () {
    // Public routes
    Route::get('/', [InspireController::class, 'index'])->middleware(OptionalAuth::class);
    Route::get('/my', [InspireController::class, 'myInspire'])->name('inspire.my')->middleware('auth:api');
    Route::get('/user/{user_id}', [InspireController::class, 'userInspire'])->middleware(OptionalAuth::class);
    Route::get('/{inspire_id}', [InspireController::class, 'show'])->middleware(OptionalAuth::class);
    Route::get('/{inspire_id}/comments', [InspireCommentController::class, 'index']);
    Route::get('/{inspire_id}/comments/{comment_id}', [InspireCommentController::class, 'show']);
    Route::get('/{inspire_id}/reactions', [InspireReactionController::class, 'index']);

    // Routes requiring authentication
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [InspireController::class, 'store']);
        Route::put('/{inspire_id}', [InspireController::class, 'update']);
        Route::delete('/{inspire_id}', [InspireController::class, 'destroy']);

        // Comments routes
        Route::post('/{inspire_id}/comments', [InspireCommentController::class, 'store']);
        Route::put('/{inspire_id}/comments/{comment_id}', [InspireCommentController::class, 'update']);
        Route::delete('/{inspire_id}/comments/{comment_id}', [InspireCommentController::class, 'destroy']);

        // Reactions routes
        Route::post('/{inspire_id}/reactions', [InspireReactionController::class, 'store']);
        Route::delete('/{inspire_id}/reactions', [InspireReactionController::class, 'destroy']);
        Route::get('/my/liked', [InspireReactionController::class, 'myLikedInspire'])->name('inspire-reactions.myLiked');

        // User saves routes
        Route::get('/user/saves', [InspireUserSaveController::class, 'index']);
        Route::post('/{inspire_id}/save', [InspireUserSaveController::class, 'store']);
        Route::delete('/saves/{id}', [InspireUserSaveController::class, 'destroy']);
    });
});

// I-Can Routes
Route::prefix('ican')->group(function () {
    // Routes for ICan requests should come first to avoid conflict
    Route::prefix('requests')->middleware('auth:api')->group(function () {
        Route::get('/', [ICanRequestController::class, 'index'])->name('ican-requests.index');
        Route::post('/apply', [ICanRequestController::class, 'apply'])->name('ican-requests.apply');
        Route::post('/{request_id}/accept', [ICanRequestController::class, 'accept'])->name('ican-requests.accept');
        Route::post('/{request_id}/reject', [ICanRequestController::class, 'reject'])->name('ican-requests.reject');
        Route::get('/{ican_id}', [ICanRequestController::class, 'show'])->name('ican-requests.show');
        Route::delete('/{request_id}', [ICanRequestController::class, 'destroy'])->name('ican-requests.destroy');
    });

    Route::get('/', [ICanController::class, 'index'])->middleware(OptionalAuth::class);
    Route::get('/my', [ICanController::class, 'myIcan'])->name('ican.my')->middleware('auth:api');
    Route::get('/user/{user_id}', [ICanController::class, 'userIcan'])->middleware(OptionalAuth::class);
    Route::get('/{ican_id}', [ICanController::class, 'show'])->middleware(OptionalAuth::class);

    // Routes requiring authentication
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ICanController::class, 'store']);
        Route::put('/{ican_id}', [ICanController::class, 'update']);
        Route::delete('/{ican_id}', [ICanController::class, 'destroy']);

        Route::prefix('reactions')->group(function () {
            Route::get('/{ican_id}', [ICanReactionController::class, 'index']);
            Route::post('/{ican_id}', [ICanReactionController::class, 'store']);
            Route::delete('/{ican_id}/reactions', [ICanReactionController::class, 'destroy']);
            Route::get('/my/liked', [ICanReactionController::class, 'myLikedIcan'])->name('ican-reactions.myLiked');
        });
    });
});

// I-Need Routes
Route::prefix('ineed')->group(function () {
    // Routes for INeed requests should come first to avoid conflict
    Route::prefix('requests')->middleware('auth:api')->group(function () {
        Route::get('/', [INeedRequestController::class, 'index'])->name('ineed-requests.index');
        Route::post('/apply', [INeedRequestController::class, 'apply'])->name('ineed-requests.apply');
        Route::post('/{request_id}/accept', [INeedRequestController::class, 'accept'])->name('ineed-requests.accept');
        Route::post('/{request_id}/reject', [INeedRequestController::class, 'reject'])->name('ineed-requests.reject');
        Route::get('/{ineed_id}', [INeedRequestController::class, 'show'])->name('ineed-requests.show');
        Route::delete('/{request_id}', [INeedRequestController::class, 'destroy'])->name('ineed-requests.destroy');
    });

    // Public routes
    Route::get('/', [INeedController::class, 'index'])->middleware(OptionalAuth::class);
    Route::get('/my', [INeedController::class, 'myINeed'])->name('ineed.my')->middleware('auth:api');
    Route::get('/user/{user_id}', [INeedController::class, 'userINeed'])->middleware(OptionalAuth::class);
    Route::get('/{ineed_id}', [INeedController::class, 'show'])->middleware(OptionalAuth::class);

    // Routes requiring authentication
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [INeedController::class, 'store']);
        Route::put('/{ineed_id}', [INeedController::class, 'update']);
        Route::delete('/{ineed_id}', [INeedController::class, 'destroy']);

        // Reactions routes
        Route::prefix('reactions')->group(function () {
            Route::get('/{ineed_id}', [INeedReactionController::class, 'index']);
            Route::post('/{ineed_id}', [INeedReactionController::class, 'store']);
            Route::delete('/{ineed_id}/reactions', [INeedReactionController::class, 'destroy']);
            Route::get('/my/liked', [INeedReactionController::class, 'myLikedINeed'])->name('ineed-reactions.myLiked');
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
    Route::get('/category/{categoryId}/sub-categories', [SkillQueryController::class, 'subCategories']);
    Route::get('/sub-category/{subCategoryId}/skills', [SkillQueryController::class, 'skillsBySubCategory']);
    Route::get('/category/{categoryId}/skills', [SkillQueryController::class, 'skillsByCategory']);
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

Route::prefix('conversations')->middleware('auth:api')->group(function () {
    // Routes for ConversationController
    Route::get('/', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/', [ConversationController::class, 'store'])->name('conversations.store');

    // Routes for MessageController
    Route::post('/messages/show', [MessageController::class, 'index'])->name('conversations.messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
});

Route::middleware('auth:api')->group(function () {
    Route::post('/ratings/ineed', [RatingController::class, 'storeINeed']);
    Route::post('/ratings/ican', [RatingController::class, 'storeICan']);
    Route::post('/ratings/ineed', [RatingController::class, 'indexINeed']);
    Route::post('/ratings/ican', [RatingController::class, 'indexICan']);
    Route::get('/ratings/my/ineed', [RatingController::class, 'myRatingsINeed']);
    Route::get('/ratings/my/ican', [RatingController::class, 'myRatingsICan']);
    Route::patch('/ratings/{rating}/status', [RatingController::class, 'updateStatus']);
});
