<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['jwt.auth'])->group(function () {
    // Login
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // User
    Route::get('user',[UserController::class,'detailUser']);
    Route::post('user/update',[UserController::class,'updateUser']);
    Route::post('user/change-password',[UserController::class,'changePassword']);

    // Project
    Route::get('project',[ProjectController::class,'listProject']);
    Route::get('project/{code}',[ProjectController::class,'detailProject']);
    Route::post('project/create',[ProjectController::class,'createProject']);
    Route::post('project/update',[ProjectController::class,'updateProject']);
    Route::delete('project/{code}',[ProjectController::class,'deleteProject']);

    // Task
    Route::post('task/create',[TaskController::class,'createTask']);
    Route::post('task/update',[TaskController::class,'updateTask']);
    Route::get('task/{id}',[TaskController::class,'detailTask']);
    Route::delete('task/{id}',[TaskController::class,'deleteTask']);
});
