<?php

use App\Http\Controllers\{
    UsersController,
    UsersReportsController
};

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users', [UsersController::class, 'users']);
Route::post('/login', [UsersController::class, 'login']);
Route::post('/user', [UsersController::class, 'store']);

Route::post('/usersReport', [UsersReportsController::class, 'usersReport']);
