<?php

use App\Http\Controllers\WorkspaceImageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/check', function () {
    $filePath = 'test.txt';
    Storage::disk('custom_public')->put($filePath, 'Hello World!');
    return 'File uploaded to: ' . public_path('uploads/' . $filePath);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/workspaces/{workspace}/upload-images', [WorkspaceImageController::class, 'create'])
        ->name('admin.upload-images.create');
    Route::post('/admin/workspaces/{workspace}/upload-images', [WorkspaceImageController::class, 'store'])
        ->name('admin.upload-images.store');
    Route::delete('/admin/workspaces/images/{image}', [WorkspaceImageController::class, 'destroy'])
        ->name('admin.workspace-images.destroy');
});
