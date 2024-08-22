<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// auth route
Route::post('/users', [AuthController::class, 'register']);
Route::post('/users/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    // auth route
    Route::delete('/users/logout', [AuthController::class, 'logout']);

    // profile/user route
    Route::get('/users/current', [UserController::class, 'index']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    // inventaris route
    Route::apiResource('/inventaris', InventarisController::class);
});
