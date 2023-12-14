<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/import', [UserController::class, 'import'])->name('import');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/user', [UserController::class, 'create']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'remove']);

    Route::post('/user/change-password', [AuthController::class, 'changePassword']);

    Route::post('/leave', [LeaveController::class, 'create']);
    Route::put('/leave/{id}', [LeaveController::class, 'update']);
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::get('/leave/{id}', [LeaveController::class, 'show']);
    Route::get('/leave/doc-download/{id}', [LeaveController::class, 'download']);
});


