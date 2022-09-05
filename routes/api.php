<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.create');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});
