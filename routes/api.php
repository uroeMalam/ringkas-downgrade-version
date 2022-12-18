<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;

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
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('welcome', [AuthController::class, 'welcome'])->name('welcome');
Route::get('reject', [AuthController::class, 'reject'])->name('reject');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile/{id}', [AuthController::class, 'profile']);

    Route::post('upload', [UserController::class, 'store']); // simpan audio ke google bucket
    Route::post('transkip', [UserController::class, 'hitYou']); // untuk get summary dari nisa
    Route::post('submit', [UserController::class, 'submit']); // untuk menyimpan semua data ke db
    Route::post('delete', [UserController::class, 'delete']); // untuk menghapus audio dari google bucket
});
