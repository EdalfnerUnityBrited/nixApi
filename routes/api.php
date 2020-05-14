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
    Route::post('signupfg', 'AuthController@signupFG');
    Route::post('verificar','AuthController@existeciaCuenta');
  
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
		Route::get('usuario', 'ChatsController@index');
		Route::get('proveedor', 'ChatsController@proveedor');
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
	    Route::post('asistir', 'ProspectosController@store');
	    Route::post('ir','ProspectosController@confAsis');
	    Route::get('tendencia','EventosController@tendencia');
	    Route::post('cupo','EventosController@cupo');
	    Route::post('idEvento','EventosController@searchId');
	    Route::post('actualizar','EventosController@actualizarEvento');
	    Route::post('invitar','EventosController@invitar');
	    Route::post('id','EventosController@getEventId');
	    Route::post('invitados','EventosController@getInvitedUsers');
	    Route::post('creador','EventosController@getUserData');
	});

		Route::group(['prefix'=>'imagen'],function(){
		Route::post('','ImagenEventoController@addImage');
		Route::post('unaImagen','ImagenEventoController@addOne');
		Route::post('erase','ImagenEventoController@eraseImage');
		});
		Route::group(['prefix'=>'proveedor'],function(){
		Route::post('cita','CitasController@agendarCita');
		});
		Route::get('notificaciones','NotificacionesController@getUser');

});
