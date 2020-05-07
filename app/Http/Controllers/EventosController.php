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

       $eventos =DB::table('eventos')
                ->join('users', 'eventos.id_creador', '=', 'users.id')
                ->select('eventos.*','users.telefono', 'users.name')
                ->where('id_creador', $user["id"])
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
         $eventos = DB::table('eventos')
         ->where('privacidad','0')
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
        $data = json_decode($request->getContent(), true);
        $evento =DB::table('eventos')
                ->where('id','=', $request->input('id'))
                ->delete();
        $prospecto= DB::table('prospectos')
        		->where('id_evento','=',$request->input('id'))
        		->delete();
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
        $categoria='0';
        if ($prospect<$cupo) {
        	$categoria='1';
        }
        return response()->json([$categoria]);
    }
}
