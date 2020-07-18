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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check', function () {
        return ['success' => true];
    });

    Route::get('/logout', \App\Http\Actions\Auth\LogoutAction::class);
    Route::post('/site', \App\Http\Actions\Site\CreateSiteAction::class);

    Route::get('/sites', \App\Http\Actions\Site\ListSitesAction::class);

    Route::prefix('/site/{id}')
        ->middleware(App\Http\Middleware\SiteOwnerMiddleware::class)
        ->group(function () {
            Route::delete('', \App\Http\Actions\Site\DeleteSiteAction::class);
            Route::post('/name', \App\Http\Actions\Site\RenameSiteAction::class);

            Route::get('/user/{user_id}/owner', \App\Http\Actions\Site\ChangeOwnerAction::class);
        });

    Route::prefix('/site/{id}')
        ->middleware(App\Http\Middleware\SiteOwnerAdminMiddleware::class)
        ->group(function () {
            Route::post('/user', \App\Http\Actions\Site\AssignUserAction::class);
            Route::delete('/user/{user_id}', \App\Http\Actions\Site\RemoveUserAction::class);

            Route::get('/user/{user_id}/role/{role}', \App\Http\Actions\Site\ChangeUserRoleAction::class);

            Route::post('/type', \App\Http\Actions\SiteType\CreateSiteTypeAction::class);
        });
});
