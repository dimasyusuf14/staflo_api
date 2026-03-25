<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/profile', [UserController::class, 'profile']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/users/role/{role}', [UserController::class, 'getByRole']);
});

// Register routes
Route::post('/register/director', [RegisterController::class, 'registerDirector']);
Route::post('/register/manager', [RegisterController::class, 'registerManager'])->middleware('auth:sanctum');
Route::post('/create-user', [RegisterController::class, 'createUser'])->middleware('auth:sanctum');
