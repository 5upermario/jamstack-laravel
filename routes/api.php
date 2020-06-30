<?php

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

Route::post('/login', \App\Http\Actions\Auth\LoginAction::class);
Route::post('/registration', \App\Http\Actions\Auth\RegistrationAction::class);

Route::middleware('auth:sanctum')->get('/logout', \App\Http\Actions\Auth\LogoutAction::class);

Route::middleware('auth:sanctum')->get('/check', function () {
    return ['success' => true];
});
