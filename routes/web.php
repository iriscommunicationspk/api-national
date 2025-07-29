<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Office 365 Authentication Routes with session support
Route::get('auth/office365', [AuthController::class, 'redirectToOffice365']);
Route::get('api/auth/office365/callback', [AuthController::class, 'handleOffice365Callback']);

Route::get('/', function () {
    return view('welcome');
});
