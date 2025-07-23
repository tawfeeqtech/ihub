<?php
// php artisan queue:work

// use Illuminate\Cache\RateLimiting\Limit;
// use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\V1\{
    AuthController,
    PackageController,
    WorkspaceController,
    BookingController,
    ConversationController,
    MessageController,
    ProfileController,
    ServiceController,
    ServiceRequestController,
    FcmController,
    SettingController,
    GovernorateController
};
use Illuminate\Support\Facades\Broadcast;

// RateLimiter::for('auth', function ($request) {
//     return Limit::perMinute(10)->by($request->ip());
// });

// RateLimiter::for('public-api', function ($request) {
//     return Limit::perMinute(60)->by($request->ip());
// });

// RateLimiter::for('user-api', function ($request) {
//     return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
// });

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });
        Route::middleware('auth:sanctum')->put('/update-device-token', [FcmController::class, 'updateDeviceToken']);

        Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('/phone/verify', [AuthController::class, 'verifyPhone']);
    });


    Route::middleware('throttle:public-api')->group(function () {
        Route::get('/workspaces', [WorkspaceController::class, 'index']);
        Route::get('/governorates', [GovernorateController::class, 'index']);
        Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show']);
        Route::get('/workspaces/{workspace}/packages', [PackageController::class, 'index']);
        Route::get('/workspaces/{workspace}/services', [ServiceController::class, 'index']);
    });


    Route::middleware(['auth:sanctum', 'throttle:user-api'])->group(function () {

        Route::get('/users/me', [ProfileController::class, 'show']);
        Route::put('/users/me', [ProfileController::class, 'update']);
        Route::put('/users/me/avatar', [ProfileController::class, 'uploadProfileImage']);
        Route::post('/store-lang', [ProfileController::class, 'updateUserLang']);

        Route::apiResource('bookings', BookingController::class)->only(['store', 'show']);
        Route::get('/bookings', [BookingController::class, 'index']); // with ?status=
        Route::get('/bookings/{booking}/service-requests', [ServiceRequestController::class, 'index']);
        Route::post('/bookings/{booking}/service-requests', [ServiceRequestController::class, 'store']);


        Route::post('/conversations', [ConversationController::class, 'store']);
        Route::get('/conversations', [ConversationController::class, 'index']);
        Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);
        Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
        Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);

        // Route::get('/notifications', fn(Request $r) => $r->user()->notifications()->latest()->get());
        // Route::patch('/notifications/mark-all-read', fn(Request $r) => tap($r->user()->unreadNotifications)->markAsRead());
        Route::get('/static/{key}', [SettingController::class, 'show']);


        // Route::get('/notifications', function (Request $request) {
        //     return $request->user()->notifications()->latest()->get();
        // });

        // Route::post('/notifications/mark-as-read', function (Request $request) {
        //     $request->user()->unreadNotifications->markAsRead();
        //     return response()->json(['message' => 'All notifications marked as read']);
        // });
    });
});
