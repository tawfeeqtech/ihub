<?php

// use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\WorkspaceImageController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/check', function () {
//     $filePath = 'test.txt';
//     Storage::disk('custom_public')->put($filePath, 'Hello World!');
//     return 'File uploaded to: ' . public_path('uploads/' . $filePath);
// });

// Route::get('/test-broadcast', function () {
//     broadcast(new \App\Events\MessageSent(\App\Models\Message::latest()->first()));
// });

Broadcast::routes(['middleware' => ['web', 'auth']]);
// Broadcast::routes(); // تم إزالة الـ middleware بشكل كامل مؤقتاً
Route::get('select-language/{code}', [App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('translation-manager.switch');

Route::middleware(['auth'])->group(function () {
    // Route::post('/store-token', [DeviceTokenController::class, 'store']);


    Route::get('/admin/workspaces/{workspace}/upload-images', [WorkspaceImageController::class, 'create'])
        ->name('admin.upload-images.create');
    Route::post('/admin/workspaces/{workspace}/upload-images', [WorkspaceImageController::class, 'store'])
        ->name('admin.upload-images.store');
    Route::delete('/admin/workspaces/images/{image}', [WorkspaceImageController::class, 'destroy'])
        ->name('admin.workspace-images.destroy');
});

// Route::get('/test-firebase', function () {
//     try {
//         // $credentialsPath = config('services.firebase.credentials');
//         $credentialsPath = base_path() . '\storage\app\firebase\firebase-credentials.json';
//         $factory = (new \Kreait\Firebase\Factory())->withServiceAccount($credentialsPath);
//         $messaging = $factory->createMessaging();
//         return response()->json([
//             'message' => 'Firebase initialized successfully',
//             'project_id' => $factory->create()->getProjectId(),
//             'is_readable' => is_readable($credentialsPath),
//             'file_exists' => file_exists($credentialsPath),
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => $e->getMessage(),
//             'config' => config('services.firebase'),
//             'is_readable' => is_readable($credentialsPath),
//             'file_exists' => file_exists($credentialsPath),
//         ], 500);
//     }
// });

Route::get('/test-firebase', function () {
    try {
        $filePath = base_path() . '/storage/app/firebase/firebase-credentials.json';
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File does not exist at: ' . $filePath], 500);
        }
        if (!is_readable($filePath)) {
            return response()->json(['error' => 'File is not readable at: ' . $filePath], 500);
        }
        $factory = (new \Kreait\Firebase\Factory())->withServiceAccount($filePath);
        $messaging = $factory->createMessaging(); // Use createMessaging() instead of create()
        return response()->json(['message' => 'Firebase initialized successfully', 'ss' => $messaging]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
