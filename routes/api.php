<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth.basic')->get('/user-basic', function (Request $request) {
    return $request->user();
});

//Route::apiResource('/products','Api\ProductController');
//Route::apiResource('/users','Api\UserController');
Route::get('categories/custom1','Api\CategoryController@custom1');
Route::get('products/custom1','Api\ProductController@custom1');
Route::get('products/custom2','Api\ProductController@custom2');
Route::get('categories/report1','Api\CategoryController@report1');
Route::get('users/custom1','Api\UserController@custom1');
Route::get('products/custom3','Api\ProductController@custom3');
Route::get('products/listwithcategories','Api\ProductController@listwithcategories');

Route::apiResources([
    'products' => 'Api\ProductController',
    'users' => 'Api\UserController',
    'categories' => 'Api\CategoryController',
]);

Route::post('/auth/login','Api\AuthController@login');

Route::middleware('api-token')->group(function() {
    Route::get('/auth/token', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'name' => $user->name,
            'access_token' => $user->api_token,
            'time' => time()
        ]);
    });
});