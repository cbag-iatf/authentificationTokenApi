<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
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

Route::group(["namespace" => "Api"], function () {
    Route::post('register', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(["middleware" => "auth:sanctum"], function () {

    Route::get('profile', [UserController::class, 'getProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::apiResource("users", UserController::class); // Les routes "users.*" de l'API