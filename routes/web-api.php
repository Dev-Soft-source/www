<?php

use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'web'], function () {
    Route::group(['prefix' => 'media'], function () {
        Route::post('/process', [MediaController::class, 'process']);
        Route::post('/revert', [MediaController::class, 'revert']);
        Route::get('/load', [MediaController::class, 'load']);
        Route::post('image_again_upload', [MediaController::class, 'uploadImage']);
    });
    
});

// Email verification API endpoint
Route::get('/email-verify/token/{token}', [EmailVerificationController::class, 'verifyToken']);



Route::group(['prefix' => 'web', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json(['data' => $user, 'status' => 'Success']);
    });
});
