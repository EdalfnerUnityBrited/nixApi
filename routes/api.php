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


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
  
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });

});
//faststoragetransaction
Route::group(['middleware' => 'auth:api'], function() {

	Route::group(['prefix' => 'chats'], function () {
		Route::get('', 'ChatsController@index');
	    Route::post('', 'ChatsController@newchat');
	    
	    //Route::get('store', 'ChatsController@store');
	});

		Route::group(['prefix' => 'eventos'], function () {
		Route::get('', 'EventosController@index');
	    Route::post('', 'EventosController@newevento');
	    
	    //Route::get('store', 'ChatsController@store');
	});
});
