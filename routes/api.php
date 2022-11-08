<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserAPIController;
use App\Http\Controllers\Api\LoginAPIController;
use App\Http\Controllers\Api\RoleAPIController;
use App\Http\Controllers\Api\CountryAPIController;
use App\Http\Controllers\Api\StateAPIController;
use App\Http\Controllers\Api\CityAPIController;
use App\Http\Controllers\Api\HobbyAPIController;
use App\Http\Controllers\Api\PermissionAPIController;
use App\Http\Controllers\Api\PermissionRoleController;
use App\Http\Controllers\Api\ForgotPasswordAPIController;



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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'prefix' => 'v1',
], function () {
    Route::get('batch-request',[UserAPIController::class, 'batchRequest']);
    Route::post('login',[LoginAPIController::class, 'login']);
    Route::post('forgot-password',[ForgotPasswordAPIController::class,'sendResetLinkEmail']);

    Route::group([
        'middleware' => ['auth:api','check.permission'],
    ], function() {
        Route::get('auth-batch-request',[UserAPIController::class, 'batchRequest']);
        Route::resource('users', UserAPIController::class,['only' => ['show', 'index','store','destroy']]);
        Route::post('update-user/{id}',[UserAPIController::class, 'updateUser']);
        Route::get('delete-multiple-users/{id}',[UserAPIController::class, 'deleteAll']);
        Route::get('users-export',[UserAPIController::class, 'exportUser']);
        Route::post('users-import',[UserAPIController::class, 'importBulk']);

        Route::get('logout',[LoginAPIController::class, 'logout']);
        Route::post('change-password',[LoginAPIController::class, 'changePassword']);


        Route::resource('roles', RoleAPIController::class);
        Route::get('delete-multiple-roles/{id}',[RoleAPIController::class, 'deleteAll']);
        Route::get('roles-export',[RoleAPIController::class, 'export']);
        Route::post('roles-import',[RoleAPIController::class, 'import']);

        Route::resource('countries', CountryAPIController::class);
        Route::get('delete-multiple-countries/{id}',[CountryAPIController::class, 'deleteAll']);
        Route::get('country-export',[CountryAPIController::class, 'export']);
        Route::post('country-import',[CountryAPIController::class, 'import']);

        Route::resource('states', StateAPIController::class);
        Route::get('delete-multiple-state/{id}',[StateAPIController::class, 'deleteAll']);
        Route::get('state-export',[StateAPIController::class, 'export']);
        Route::post('state-import',[StateAPIController::class, 'import']);

        Route::resource('cities', CityAPIController::class);
        Route::get('delete-multiple-city/{id}',[CityAPIController::class, 'deleteAll']);
        Route::get('city-export',[CityAPIController::class, 'export']);
        Route::post('city-import',[CityAPIController::class, 'import']);

        Route::resource('hobbies', HobbyAPIController::class);
        Route::get('delete-multiple-hobby/{id}',[HobbyAPIController::class, 'deleteAll']);
        Route::get('hobby-export',[HobbyAPIController::class, 'export']);
        Route::post('hobby-import',[HobbyAPIController::class, 'import']);

        Route::resource('permissions', PermissionAPIController::class);
        Route::get('delete-multiple-permission/{id}',[PermissionAPIController::class, 'deleteAll']);
        Route::get('permission-export',[PermissionAPIController::class, 'export']);
        Route::post('permission-import',[PermissionAPIController::class, 'import']);
        Route::post('set-unset-permission-to-role',[PermissionAPIController::class, 'setUnsetPermission']);
        Route::get('get-permission-to-role/{id}',[PermissionAPIController::class, 'getPermissions']);

        //Route::post('set-unset-permission-to-role',[PermissionRoleController::class, 'setUnsetPermission']);
        //Route::get('get-permission-to-role/{id}',[PermissionRoleController::class, 'getPermissions']);
    });
    

});
