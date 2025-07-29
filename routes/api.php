<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRequestController;
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
// // // Office 365 Authentication Routes
// Route::get('/auth/office365', [AuthController::class, 'redirectToOffice365']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/events/get', [UserController::class, 'getEvents']);
    Route::post('/event/add', [UserController::class, 'addEvent']);
    Route::post('/event/update', [UserController::class, 'updateEvent']);
    Route::delete('/event/delete', [UserController::class, 'deleteEvent']);

    //Request Submission Routes
    Route::post('/research_submit', [UserRequestController::class, 'submitRequest']);
    Route::post('/research_update', [UserRequestController::class, 'updateRequest']);
    Route::delete('/research_delete', [UserRequestController::class, 'deleteRequest']);
    Route::post('/research_approve', [UserRequestController::class, 'approveRequest']);
    Route::post('/research_reject', [UserRequestController::class, 'rejectRequest']);
    Route::get('/research_get-all', [UserRequestController::class, 'getAllRequests']);
    Route::post('/research_get_user', [UserRequestController::class, 'getRequestById']);
    // User participation routes
    Route::post('/event/participate', [UserController::class, 'participateInEvent']);
    Route::post('/event/participation-status', [UserController::class, 'getUserParticipationStatus']);

    Route::post('/user/events', [UserController::class, 'userEvents']);
    Route::post('/event/users', [UserController::class, 'eventUsers']);

    Route::post('/event/users/all', [UserController::class, 'eventUsersAll']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutFromAll']);
});
