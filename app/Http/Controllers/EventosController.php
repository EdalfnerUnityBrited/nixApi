<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Cupo;
use App\Eventos;
use App\Prospectos;
use Carbon\Carbon;
use App\Notificaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EventosController extends Controller
{
    /**
     * Obtener los eventos del usuario
     * En esta parte se obtiene el usuario que ha realizado la petición, la fecha de ahora la guardamos en una variable y realizamos la consulta donde los eventos sean del usuario y la fecha sea después de el dia actual
     * @return \Illuminate\Http\Response
     */
    public function getUserEvents(Request $request)
    {
        $user =$request->user();
$now = Carbon::now();
       $eventos =DB::table('eventos')
                ->join('users', 'eventos.id_creador', '=', 'users.id')
                ->select('eventos.*','users.telefono', 'users.name')
                ->where('id_creador', $user["id"])
                ->where('fecha','>',$now)
                ->get();
                return response()->json(['eventos'=>$eventos]);
    
    }

    /*Se obtienen los datos desde el json, la fecha de hoy y el usuario para que a partir de esos datos se cree un nuevo objeto del modelo Eventos, en el cual se guardarán los datos extras que se requieran tales como el usuario que no creó, la tendencia y la fecha en que se creó el evento

    Después dehacer el evento, ahora se hace un nuevo prospecto con el evento que se acaba de crear y con el id del usuario en el estado de prospecto se va a poner que el estado del prospecto es el creador.

    Al final se manda la respuesta de que todo se creó correctamente
    */
    public function newevento(Request $request)
    {
        $data = json_decode($request->getContent(), true);
 		$now = Carbon::now();
        $user = $request->user();
        $evento = new Eventos($data);
        $evento->id_creador=$user["id"];
        $evento->tendencia=0;
        $evento->created_at= $now;
        $evento->save();
        $prospecto= new Prospectos();
        $prospecto->estado='creador';
        $prospecto->confirmacionasistencia='0';
        $prospecto->id_evento=$evento["id"];
        $prospecto->id_prospecto=$user["id"];
        $prospecto->save();
        return response()->json([
                    'eventos' => $evento], 201);
    }
    /**
     * Obtener todos los eventos
     * Aqui se obtiene la fecha actual y a partir de esa se hace una consulta donde el evento sea público y la fecha sea después de la fecha actual y se retoran esos eventos
     * @return \Illuminate\Http\Response
     */
    public function getAllEvents()
    {
    	$now = Carbon::now();
         $eventos = DB::table('eventos')
         ->where('privacidad','0')
         ->where('fecha','>',$now)
         ->get();
        return response()->json(['eventos'=>$eventos]);
    }

    /**
     * Buscar eventos
     * En esta parte se obtienen todos los parametros para realizar la búsqueda y se guardan en variables. Despues mediante los query scopes se van construyendo las consultas y al final se obtienen los eventos y se envían a la aplicación
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nombre_evento    = $request->get('nombre');
        $categoria_evento = $request->get('categoria');
        $cover = $request->get('cover');
        $lugar = $request->get('municipio');
        $fechaInicio = $request->get('fechaIni');
        $cupo = $request->get('cupo');
        $fechaFinal = $request->get('fechaFin');

        $eventos = Eventos::orderBy('id','DESC')
        ->nombre_evento($nombre_evento)
        ->categoria_evento($categoria_evento)
        ->cover($cover)
        ->lugar($lugar)
        ->fechaInicio($fechaInicio, $fechaFinal)
        ->cupo($cupo)
        ->get();

        return response()->json(['eventos'=>$eventos]);
    }

    /**
     * Tendencias
     * Aqui se obtiene la fecha actual y después se obtienen todos los eventos que estén en tendencia y que estén después de la fecha actual
     * 
     * @return \Illuminate\Http\Response
     */
    public function tendencia(Request $request)
    {
    	$now = Carbon::now();
       $evento = DB::table('eventos')
                        ->select('eventos.*')
                        ->where('tendencia', '=', '1')
                        ->where('fecha','>',$now)
                        ->orderBy('fecha', 'asc')
                        ->get();

                return response()->json(['eventos'=>$evento]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * Lo que se hace aqui es buscar el evento por nombre, y se obtiene el primer evento con ese nombre y se envía ese evento
     * @return \Illuminate\Http\Response
     */
    public function buscarEvento(Request $request)
    {
        $separar=$request->input('nombre_evento');
        $evento = DB::table('eventos')
                        ->select('eventos.*')
                        ->where('nombre_evento', 'like', '%'.$separar.'%')
                        ->first();
    
                return response()->json(['eventos'=>$evento]);
    }

    /**
     * Obtener datos del evento tanto, toda la información y las imagenes relacionadas con ese evento para mostrarla en la informacion expandida del evento. Es entonces que se envían los eventos y también las imagenes
     *
     * @return \Illuminate\Http\Response
     */
    public function getSpecificEvent(Request $request)
    {
        $evento = DB::table('eventos')
                        ->select('eventos.*')
                        ->where('nombre_evento', $request->input('nombre_evento'))
                        ->first();
        $imagen = DB::table('eventos')
                        ->join('imageneventos', 'imageneventos.id_evento', '=', 'eventos.id')
                        ->select('imageneventos.imagen')
                        ->where('eventos.id', $evento->id)
                        ->get();
        return response()->json(['eventos'=>$evento,'imagenEventos'=>$imagen]);
    }

    /**
     * Eliminar el evento
     * Se obtiene la fecha actual para poder actualizar la fecha en que se muestran las notificaciones, despues se procede a buscar el evento por el id, se obtiene los datos del evento, se borra el objeto, se borran todos los prospectos del evento y al final se camiban las notificaciones para que se muestren en el instante y se cambia el menssaje de la notificación para decir que se ha cancelado el evento
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    { 
        $now= Carbon::now();
        $data = json_decode($request->getContent(), true);
        $eventos =DB::table('eventos')
                ->where('id','=', $request->input('id'))
                ->first();
        $evento =DB::table('eventos')
                ->where('id','=', $request->input('id'))
                ->delete();
        $prospecto= DB::table('prospectos')
        		->where('id_evento','=',$request->input('id'))
        		->delete();
        Notificaciones::where('id_evento', $request->input('id'))
                        ->update(['contenido' => "El evento ".$eventos->nombre_evento." ha sido eliminado"]);
        Notificaciones::where('id_evento', $request->input('id'))
                        ->update(['fechaInicio' => $now]);
        Notificaciones::where('id_evento', $request->input('id'))
                        ->update(['tipoNotificacion' => 4]);
                return response()->json(['eventos'=>"Event deleted succesfully!"]);   
    }
    /*Aaqui lo que se hace es corroborar si el evento está lleno o no, primeramente se busca el cupo del  evento por el nombre, después se obtienen la cuenta de todos los asistentes que se han confirmado y si el cupo ya está lleno se manda un mensaje de que ya está lleno

    */
    public function cupo(Request $request)
    {
    	$data= json_decode($request->getContent(), true);
    	$prospect=DB::table('prospectos')
        ->where('id_evento','=',$request->input('nombre_evento'))
        ->where('estado','=','confirmado')
        ->count();
    	$cupo=DB::table('eventos')
        ->where('eventos.id','=',$request->input('nombre_evento'))
        ->pluck('eventos.cupo')
        ->first();
        $text=DB::table('prospectos')
        	->select('id_evento')
        	->where('id_evento','=',$request->input('nombre_evento'))
        	->first();
        $text->id_evento='0';
        if ($prospect<$cupo) {
        	$text->id_evento='1';
        }
        return response()->json(['eventoLleno'=>$text]);
    }
    /*Aqui se busca evento por id, se obtiene el id desde el request y al final se envía el evento
    */
    public function searchId(Request $request){
    	$evento=DB::table('eventos')
    	->where('id','=', $request->input('cupo'))
    	->first();
    	return response()->json(['eventos'=>$evento]);
    }
    /*Al momento de actualizar lo que se hace es solicitar todos los campos del request para cambiár los datos y así poder guardar todos esos cambios en variables para que al final se pueda cguardar los cambios

    */
    public function actualizarEvento(Request $request){
    	$data= json_decode($request->getContent(), true);
    	$eventos=Eventos::find($request->input('id'));
        $eventos->nombre_evento=$request->input('nombre_evento');
        $eventos->privacidad=$request->input('privacidad');
        $eventos->categoria_evento=$request->input('categoria_evento');
        $eventos->fecha=$request->input('fecha');
        $eventos->hora=$request->input('hora');
        $eventos->lugar=$request->input('lugar');
        $eventos->descripcion=$request->input('descripcion');
        $eventos->cupo=$request->input('cupo');
        $eventos->cover=$request->input('cover');
        $eventos->fotoPrincipal=$request->input('fotoPrincipal');
        $eventos->municipio=$request->input('municipio');
    	
    	$eventos->save();
    	return response()->json(['eventos'=>"Event updated succesfully!"]);
    }
    /*Al momento de invitar a personas lo primero que se realiza es corroborar que el email exista y despues de corroborar que existe se procede a ver si no ha sido invitado al evento o si no ha confirmado asistencia al evento. Dspués de haber verificado el correo se procede a añadir al usuario a la tabla de prospectos con motivo de invitación y al final se hace la notificación poniendo el evento, la fecha y en la fecha se pone instantaneamente

    */
    public function invitar(Request $request){

    	$usuario=User::where('email',$request->input('cupo'))->first();
    	if (is_null($usuario)) {
    		return response()->json([
                        'message' => 'No se encontró'], 404);
    	}
    	$eventos=Prospectos::where('id_evento','=',$request->input('cover'))
    					->where('id_prospecto',$usuario["id"])
    					->first();
    	if (is_null($eventos)) {
    		$evento=DB::table('eventos')
                        ->where('id',$request->input('cover'))
                        ->first();
    			$now = Carbon::now();
    	$prospecto=new Prospectos();
    	$prospecto->estado='invitado';
    	$prospecto->confirmacionasistencia=0;
    	$prospecto->id_prospecto=$usuario["id"];
    	$prospecto->id_evento=$request->input('cover');
    	$prospecto->invited_at=$now;
    	$prospecto->save();
                        $notificaciones= new Notificaciones();
                        $notificaciones->id_receptor=$usuario["id"];
                        $notificaciones->id_evento=$request->input('cover');
                        $notificaciones->fechaFin=$evento->fecha;
                        $notificaciones->fechaInicio=$now;
                        $notificaciones->contenido=("Has sido invitado al evento ".$evento->nombre_evento." el dia ".$evento->fecha."");
                        $notificaciones->tipoNotificacion=2;
                        $notificaciones->save();
    	return response()->json(['message' => 'User added succesfully']);

    						}					
    	return response()->json(['message' => 'User added unsuccesfully'],404);
    	
    }
    /*Obtener los eventos por ID
        Aqui se busca evento por id, se obtiene el id desde el request y al final se envía el evento
    */
    public function getEventId(Request $request){
        $evento =DB::table('eventos')
                ->where('id','=', $request->input('cupo'))
                ->first();
    return response()->json(['eventos' =>$evento]);  
    }
/*Obtener los datos del usuario
A partir del id del evento se buscan los datos del usuario que ha creado el evento y se obtiene solo un resultado mandando todo el usuario para mostrar en la vista los datos en dado caso de que quieran saber quien es el creador del evento
*/
    public function getUserData(Request $request){
        $usuario =DB::table('eventos')
                ->join('users', 'eventos.id_creador', '=', 'users.id')
                ->select('users.*')
                ->where('eventos.id', $request->input('nombre_evento'))
                ->first();
                 return response()->json(['usuario' =>$usuario]);
    }
    /*Obtener los usuarios confirmados
      Primeramente se une la tabla de prospectos, eventos y usuarios para que los prospectos que estén confirmados y los muestra enviando el nombre en orden alfabetico
    */
    public function getInvitedUsers(Request $request){
                $usuarios =DB::table('prospectos')
                ->join('eventos', 'eventos.id', '=', 'prospectos.id_evento')
                ->join('users', 'users.id', '=', 'prospectos.id_prospecto')
                ->select('users.*')
                ->where('eventos.id', $request->input('nombre_evento'))
                ->where('prospectos.estado','confirmado')
                ->orderBy('users.name','ASC')
                ->get();
                return response()->json(['usuarios' =>$usuarios]);
    }

    public function confirmarAsistencia(Request $request){

        $user=$request->user();
        DB::table('prospectos')
        ->where('id_evento',$request->input('id_evento'))
        ->where('id_prospecto',$user["id"])
        ->where('estado',"confirmado")
        ->update(['confirmacionasistencia'=>1]);
        return response()->json(['message' =>'Asistencia confirmada']);
    }
}
