<?php
use App\Http\Controllers\{
    UserController,
    UserReportsController
};

use Illuminate\Support\Facades\Route;

// Rutas Privadas - Token
Route::middleware('auth:sanctum')->group(function () {
    // Users - actions
    Route::get('/user/{name}', [UserController::class, 'userConsult']);
    Route::post('/user/upload-image-profile', [UserController::class, 'uploadImageProfile']);
    Route::get('/users', [UserController::class, 'users']);
    Route::post('/auth/logout', [UserController::class, 'logout']);


    // Reports - user - alert
    Route::post('/reports', [UserReportsController::class, 'userReportCreate']);
    Route::post('/reports/create', [UserReportsController::class, 'userReportCreate']);
});

/*Route::middleware('auth:sanctum')->prefix('reports')->group(function () {
    Route::get('/', [UserReportsController::class, 'index']);         // Listar reportes (paginados)
    Route::post('/', [UserReportsController::class, 'store']);        // Crear un nuevo reporte
    Route::get('/{id}', [UserReportsController::class, 'show']);      // Ver un reporte específico
    Route::put('/{id}', [UserReportsController::class, 'update']);    // Actualizar reporte
    Route::delete('/{id}', [UserReportsController::class, 'destroy']); // Eliminar reporte
});*/

// Rutas - Auth
Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
});
 