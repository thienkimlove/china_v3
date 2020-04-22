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
Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::group(['middleware' => ['auth.jwt']], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('customer', 'AuthController@customer');
        Route::get('refresh', 'AuthController@refresh');
        Route::post('change-pass', 'AuthController@pass');
        Route::post('update-user', 'AuthController@update');

        Route::get('orders', 'ApiController@orders');
        Route::get('carts', 'ApiController@carts');
        Route::post('add-cart', 'ApiController@addCart');
        Route::post('delete-cart-item', 'ApiController@deleteCartItem');
        Route::post('create-order', 'ApiController@createOrder');
    });

});