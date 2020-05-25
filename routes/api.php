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

/*
Como la documentación nos dice, aqui se especifican las rutas de la api para poder relacionarlas con los controladores
y así poder realizar la llamada a la api desde una URL
*/
/*
El prefix sirve principalmente para colocar en la ruta el "prefijo auth", para que cuando queramos llamar a un metodo de
este prefijo sea más sencillo encontrarlo en la URL sería auth/"nombre de la siguiente ruta"
A continuación se describe para que sirve cada ruta en este prefijo
*/
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');//Esta ruta sirve para relacionar la funcion de iniciar sesion
    Route::post('signup', 'AuthController@signup');//Esta ruta sirve para relacionar la función de crear cuenta
    Route::post('signupfg', 'AuthController@signupFG');//Esta ruta sirve para relacionar la función de iniciar sesion con Facebook o Google
    Route::post('verificar','AuthController@existeciaCuenta');//Esta ruta sirve para relacionar la función que verifica que si exista una cuenta con el correo
  //El middleware auth api es el que le pone candados a estas funciones. En dado caso que el usuario necesite realizar alguna de estas opciones necesitará primero haber ingresado a la aplicación
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');//Esta ruta sirve para relacionar la función para cerrar la cuenta del usuario
        Route::get('user', 'AuthController@user');//Esta ruta nos sirve para obtener los datos del usuario
        Route::put('foto', 'AuthController@cambioFoto');//Esta ruta nos sirve para cambiar la foto del usuario
        Route::put('user', 'AuthController@cambioDatos');//Esta ruta nos sirve para cambiar los datos del usuario
        Route::put('password', 'AuthController@cambioContrasena');//Esta ruta nos sirve para cambiar la contraseña
        Route::post('pagar','AuthController@payment');//Esta ruta nos sirve para realizar un pago
    });

});
//Aqui también se usa el mmidleware auth para no permitir que usuarios no logueados realicen funciones especiales
Route::group(['middleware' => 'auth:api'], function() {

	Route::group(['prefix' => 'chats'], function () {
		Route::get('usuario', 'ChatsController@index');//Aqui se obtienen los chats del usuario
		Route::get('proveedor', 'ChatsController@proveedor');//Aqui se obtienen los chats del proveedor
	    Route::post('', 'ChatsController@newchat');//Aqui se crea un nuevo chat
	});

		Route::group(['prefix' => 'eventos'], function () {
		Route::get('usuario', 'EventosController@getUserEvents');//Aqui se obtienen los eventos del usuario
		Route::get('todos', 'EventosController@getAllEvents');//Aqui se obtienen todos los eventos
	    Route::post('', 'EventosController@newevento');//Aqui se crea un nuevo evento
	    Route::delete('', 'EventosController@destroy');//Aqui se elimina un evento
	    Route::post('buscar','EventosController@buscarEvento');//Aqui se busca un evento por nombre
	    Route::post('buscarEvento','EventosController@store');//Aqui se realiza la busqueda con filtros del evento
	    Route::post('evento','EventosController@getSpecificEvent');//Aqui se obtiene el evento con sus imagenes
	    Route::get('asistencia','ProspectosController@getUserEvent');//Aqui se obtienen todos los eventos relacionados con el usuario ya sea de motivo de interés, que lo haya creado o que haya dicho que irá
	    Route::post('asistir', 'ProspectosController@store');//Aqui es en donde se guarda en la base de datos si va a ir o si lo guarda con motivo de interés el evento
	    Route::post('ir','ProspectosController@confAsis'); //Aqui es en donde se obtienen todos los prospectos que irán a cierto evento
	    Route::get('tendencia','EventosController@tendencia');//Aqui se obtienen todos los eventos que estén marcados con motivo de tendencia
	    Route::post('cupo','EventosController@cupo');//Aqui se verifica si el evento ya se llenó a su máxima capacidad
	    Route::post('idEvento','EventosController@searchId');//Aqui se busca un evento por su ID
	    Route::post('actualizar','EventosController@actualizarEvento');//Aqui se actualiza un evento
	    Route::post('invitar','EventosController@invitar');//En esta parte se realizan las invitaciones de un evento a los usuarios
	    Route::post('id','EventosController@getEventId');//Aqui se busca un evento por ID
	    Route::post('invitados','EventosController@getInvitedUsers');//Aqui se obtienen todos los usuarios que han sido invitados a un evento
	    Route::post('creador','EventosController@getUserData');//Aqui se obtienen los datos del creador del evento
	});

		Route::group(['prefix'=>'imagen'],function(){
		Route::post('','ImagenEventoController@addImage');//Aqui se añaden las imagenes a un evento
		Route::post('unaImagen','ImagenEventoController@addOne');//Aqui de añade una imagen al evento
		Route::post('erase','ImagenEventoController@eraseImage');//Aqui se borra una imagen del evento
		Route::post('articulo','ImagenArticuloController@addImages');//Aqui se añaden las imagenes de los articulos
		Route::post('paquete','ImagenPaqueteController@addImages');//Aqui se añaden las imagenes de los paquetes
		});
		Route::group(['prefix'=>'proveedor'],function(){
		Route::post('cita','CitasController@agendarCita');//Aqui se crea una cita
		Route::post('nuevoServicio','CatalogoServicioController@newService');//Aqui se crea un nuevo servicio
		Route::post('articulo','ArticuloController@crearArticulo');//Aqui se crea un nuevo articulo
		Route::post('paquete','PaqueteController@addPaquete');//Aqui se crea un nuevo paquete
		Route::post('buscarArticulo','ArticuloController@buscarArticulo');//Aqui se busca un articulo aplicando filtros
		Route::post('buscarPaquete','PaqueteController@buscarPaquete');//Aqui se busca un paquete aplicando filtros
		Route::post('buscarServicio','CatalogoServicioController@buscarServicio');//Aqui se busca un servicio aplicando filtros
		Route::post('zonaServicio','ZonaController@municipio');
		Route::post('paqueteArticulo','PaqueteArticuloController@nuevoArticuloPaquete');
		Route::post('articuloServicio','ArticuloController@getServiceArticle');
		Route::post('paqueteServicio','PaqueteController@getPaqueteServicio');
		Route::get('usuarioServicio','CatalogoServicioController@getUserService');
		Route::post('serviceId','CatalogoServicioController@getServiceId');
		Route::post('actualizarServicio','CatalogoServicioController@actualizarServicio');
		Route::post('articuloId','ArticuloController@getArticle');
		Route::post('actualizarArticulo','ArticuloController@updateArticle');
		Route::post('borrarPaquete','PaqueteController@erasePackage');
		Route::post('borrarArticulo','ArticuloController@eraseArticle');
		Route::post('borrarServicio','CatalogoServicioController@eraseService');
		Route::post('municipioServicio','ZonaController@buscarZona');
		});
		Route::get('notificaciones','NotificacionesController@getUser');//Aqui se obtienen las notificaciones del usuario

});
