<?php
use App\Http\Controllers\{
    UserController,
    UserReportsController
};

use Illuminate\Support\Facades\Route;

// Rutas Privadas - Token
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user/{name}', [UserController::class, 'userConsult']);
    Route::post('/user/upload-image-profile', [UserController::class, 'uploadImageProfile']);

    Route::get('/users', [UserController::class, 'users']);
    Route::post('/usersReport', [UserReportsController::class, 'usersReport']);

    Route::post('/auth/logout', [UserController::class, 'logout']);
});

// Rutas - Auth
Route::prefix('auth')->group(function () {

    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
    
});
 