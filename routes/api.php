<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

//test route

Route::get('/test', function (Request $request) {
    return 'tested';
});

Route::post('/login', [AuthController::class, 'Login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/events/get', [UserController::class, 'getEvents']);
    Route::post('/event/add', [UserController::class, 'addEvent']);
    Route::post('/event/update', [UserController::class, 'updateEvent']);

    Route::post('/user/events', [UserController::class, 'userEvents']);
    Route::post('/event/users', [UserController::class, 'eventUsers']);

    Route::post('/event/users/all', [UserController::class, 'eventUsersAll']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutFromAll']);
});

