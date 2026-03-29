<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\TaskController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
Route::get('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum']);

// User routes - menggunakan middleware auth dengan guard sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    // GET endpoints
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/profile', [UserController::class, 'profile']);
    Route::post('/users/profile/update', [UserController::class, 'updateProfile']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/role/{role}', [UserController::class, 'getByRole']);

    // POST endpoints (untuk create, update, delete)
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/users/{id}/update', [UserController::class, 'update']);
    Route::post('/users/{id}/delete', [UserController::class, 'destroy']);

    // Position routes
    Route::get('/positions', [PositionController::class, 'index']);
    Route::get('/positions/levels', [PositionController::class, 'getLevels']);
    Route::get('/positions/level/{level}', [PositionController::class, 'getByLevel']);
    Route::get('/positions/creatable', [PositionController::class, 'getCreatablePositions']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::post('/positions/{id}/delete', [PositionController::class, 'destroy']);

    // Bucket routes (Level 1 & 2 only)
    Route::get('/buckets', [BucketController::class, 'index']);
    Route::post('/buckets', [BucketController::class, 'store']);
    Route::post('/buckets/{id}/delete', [BucketController::class, 'destroy']);

    // Task routes
    Route::get('/tasks/statuses', [TaskController::class, 'statuses']);
    Route::get('/tasks/priorities', [TaskController::class, 'priorities']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::post('/tasks/{id}/update', [TaskController::class, 'update']);
    Route::post('/tasks/{id}/description', [TaskController::class, 'updateDescription']);
    Route::post('/tasks/{id}/assignee-save', [TaskController::class, 'assigneeSave']);
    Route::post('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::post('/tasks/{taskId}/subtasks/{subtaskId}/status', [TaskController::class, 'updateSubtaskStatus']);
    Route::post('/tasks/{id}/attachments', [TaskController::class, 'uploadAttachment']);
    Route::post('/tasks/{id}/delete', [TaskController::class, 'destroy']);
});

// Register routes
Route::post('/register/director', [RegisterController::class, 'registerDirector']);
Route::post('/register/manager', [RegisterController::class, 'registerManager'])->middleware(['auth:sanctum']);
Route::post('/create-user', [RegisterController::class, 'createUser'])->middleware(['auth:sanctum']);
