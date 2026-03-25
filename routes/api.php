<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PositionController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
Route::get('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum']);

// User routes - menggunakan middleware auth dengan guard sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    // GET endpoints
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/profile', [UserController::class, 'profile']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/role/{role}', [UserController::class, 'getByRole']);

    // POST endpoints (untuk create, update, delete)
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/users/{id}/update', [UserController::class, 'update']);
    Route::post('/users/{id}/delete', [UserController::class, 'destroy']);

    // Position routes
    Route::get('/positions', [PositionController::class, 'index']);
    Route::get('/positions/level/{level}', [PositionController::class, 'getByLevel']);
    Route::get('/positions/creatable', [PositionController::class, 'getCreatablePositions']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::post('/positions/{id}/delete', [PositionController::class, 'destroy']);
});

// Register routes
Route::post('/register/director', [RegisterController::class, 'registerDirector']);
Route::post('/register/manager', [RegisterController::class, 'registerManager'])->middleware(['auth:sanctum']);
Route::post('/create-user', [RegisterController::class, 'createUser'])->middleware(['auth:sanctum']);
