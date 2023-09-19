<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

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

Route::namespace('Api')->group(function() {
  Route::post('auth/login', 'AuthController@login');
  Route::group(['middleware' => 'auth:sanctum'], function () {
    // auth
    Route::get('auth/user', 'AuthController@user');
    Route::post('auth/logout', 'AuthController@logout');
    Route::get('/user', function (Request $request) {
      return new UserResource($request->user());
    });
    // dashboard
    Route::get('dashboard/widgets', 'DashboardController@widgets');
    Route::get('dashboard/recentdocs', 'DashboardController@recentDocs');
    // config
    Route::post('config', 'ConfigController@save');
    // users and roles
    Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_MANAGE_PERMISSIONS);
    Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_MANAGE_USERS);
    Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_MANAGE_PERMISSIONS);
    Route::put('users/{user}', 'UserController@update');
    Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_MANAGE_PERMISSIONS);
    Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_MANAGE_PERMISSIONS);
    Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_MANAGE_PERMISSIONS);
    
    // contacts
    Route::get('contacts/functions', 'ContactController@functions');
    Route::apiResource('contacts', 'ContactController');
    // companies
    Route::get('companies/regions', 'CompanyController@regions');
    Route::get('companies/activities', 'CompanyController@activities');
    Route::apiResource('companies', 'CompanyController');
    Route::apiResource('tags', 'TagController');
    // documents
    Route::get('documents/latest', 'DocumentController@latest');
    Route::get('documents/stats', 'DocumentController@stats');
    Route::post('documents/terminate/{id}', 'DocumentController@terminate');
    Route::post('documents/cancel/{id}', 'DocumentController@cancel');
    Route::apiResource('documents', 'DocumentController');
    Route::apiResource('units', 'UnitController');
    // inventory
    Route::apiResource('stocks', 'StockController');
    Route::apiResource('storehouses', 'StorehouseController');
    Route::apiResource('purchases', 'PurchaseController');
    Route::apiResource('sales', 'SaleController');
    // products
    Route::post('uploadimage', 'ProductController@upload');
    Route::apiResource('products', 'ProductController');
    Route::post('categories/reorder', 'CategoryController@reorder');
    Route::apiResource('categories', 'CategoryController');

    // stats
    Route::get('stats/purchases', 'StatsController@purchases');
    Route::get('stats/sales', 'StatsController@sales');
    Route::get('stats/stores', 'StatsController@salesByStorehouse');
  });
});
