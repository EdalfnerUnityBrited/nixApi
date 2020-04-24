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
        Route::put('foto', 'AuthController@cambioFoto');
        Route::put('user', 'AuthController@cambioDatos');
        Route::put('password', 'AuthController@cambioContrasena');
        Route::post('pagar','AuthController@payment');
    });

});
//faststoragetransaction
Route::group(['middleware' => 'auth:api'], function() {

	Route::group(['prefix' => 'chats'], function () {
		Route::get('', 'ChatsController@index');
		Route::get('', 'ChatsController@proveedor');
	    Route::post('', 'ChatsController@newchat');
	    
	    //Route::get('store', 'ChatsController@store');
	});

		Route::group(['prefix' => 'eventos'], function () {
		Route::get('usuario', 'EventosController@getUserEvents');
		Route::get('todos', 'EventosController@getAllEvents');
	    Route::post('', 'EventosController@newevento');
	    Route::delete('', 'EventosController@destroy');
	    Route::post('buscar','EventosController@buscarEvento');
	    Route::post('buscarEvento','EventosController@store');
	    Route::post('evento','EventosController@getSpecificEvent');
	    Route::get('asistencia','ProspectosController@getUserEvent');
	    //Route::get('store', 'ChatsController@store');
	});

		Route::group(['prefix'=>'imagen'],function(){
		Route::post('','ImagenEventoController@addImage');
		});


});
