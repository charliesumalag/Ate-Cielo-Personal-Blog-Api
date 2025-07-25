<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('post', PostController::class);
Route::post('/register', [AuthController::class, 'register']);
Route::put('/updateprofile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/', [AuthController::class, 'dashboard'])->middleware('auth:sanctum');
