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
     * Display a listing of the resource.
     *
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
     * Show the form for creating a new resource.
     *
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
     * Store a newly created resource in storage.
     *
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
     * Display the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function buscarEvento(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $separar=$request->input('nombre_evento');
        $evento = DB::table('eventos')
                        ->select('eventos.*')
                        ->where('nombre_evento', 'like', '%'.$separar.'%')
                        ->first();

        
        //return response()->json(['eventos'=>$separar[1]]);  
    
                return response()->json(['eventos'=>$evento]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSpecificEvent(Request $request)
    {
        $data = json_decode($request->getContent(), true);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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
                return response()->json(['eventos'=>"Event deleted succesfully!"]);   
    }
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
    public function searchId(Request $request){
    	$evento=DB::table('eventos')
    	->where('id','=', $request->input('cupo'))
    	->first();
    	return response()->json(['eventos'=>$evento]);
    }
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
    public function getEventId(Request $request){
        $evento =DB::table('eventos')
                ->where('id','=', $request->input('cupo'))
                ->first();
    return response()->json(['eventos' =>$evento]);  
    }

    public function getUserData(Request $request){
        $usuario =DB::table('eventos')
                ->join('users', 'eventos.id_creador', '=', 'users.id')
                ->select('users.*')
                ->where('eventos.id', $request->input('nombre_evento'))
                ->first();
                 return response()->json(['usuario' =>$usuario]);
    }
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
}
