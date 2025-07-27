<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostLikeController;
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
Route::get('/users/{user}/posts', [PostController::class, 'getPostsByUser'])->middleware('auth:sanctum');


Route::get('/posts/{post}/comments', [CommentsController::class, 'index']);
Route::post('/posts/{post}/comments', [CommentsController::class, 'store'])->middleware('auth:sanctum');
Route::post('/comments/{comment}/reply', [CommentsController::class, 'reply'])->middleware('auth:sanctum');
Route::put('/comments/{comment}', [CommentsController::class, 'update']);
Route::delete('/comments/{comment}', [CommentsController::class, 'destroy']);


Route::post('/posts/{post}/like', [PostLikeController::class, 'like']);
Route::delete('/posts/{post}/unlike', [PostLikeController::class, 'unlike']);


Route::post('/comments/{comment}/like', [CommentLikeController::class, 'like']);
Route::delete('/comments/{comment}/unlike', [CommentLikeController::class, 'unlike']);
