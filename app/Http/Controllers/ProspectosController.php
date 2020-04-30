<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Prospectos;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProspectosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserEvent(Request $request)
    {
        $user = $request->user();
        $eventos = DB::table('prospectos')
            ->join('eventos', 'eventos.id', '=', 'prospectos.id_evento')
            ->select('eventos.*','prospectos.estado')
            ->where('id_prospecto',$user["id"])
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
        $data = json_decode($request->getContent(), true);
        $today=today();
        $user = $request->user();
        $pros=DB::table('prospectos')
        ->select('prospectos.estado')
        ->where('id_prospecto','=',$user["id"])
        ->where('id_evento','=',$request->input('id_evento'))
        ->first();
        $mensaje="Ya estas registrado";
        if(is_null($pros)){

            $prospectos = new Prospectos($data);
            $prospectos->confirmacionasistencia=0;
            $prospectos->id_prospecto=$user["id"];
            $prospectos->save();
            $mensaje="Registrado correctamente";
        }
        else if($pros->estado!=$request->input('estado')){
            if ($request->input('estado')=='cancelar') {
                DB::table('prospectos')
                ->where('id_evento','=',$request->input('id_evento') )
                ->where('id_prospecto','=',$user["id"])
                ->delete();
                $mensaje="Borrado correctamente";
            }
            else{
                 DB::table('prospectos')
                    ->where('id_evento','=',$request->input('id_evento') )
                    ->where('id_prospecto','=',$user["id"])
                    ->update(['prospectos.estado' => $request->input('estado')]);
                    $mensaje="Actualizado correctamente";
            }
           
        }

        $fecha=DB::table('eventos')
        ->select('eventos.created_at')
        ->where('eventos.id','=',$request->input('id_evento'))
        ->first();
        $prospect=DB::table('prospectos')
        ->where('id_evento','=',$request->input('id_evento'))
        ->where('estado','=','confirmado')
        ->count();
        $cupo=DB::table('eventos')
        ->where('eventos.id','=',$request->input('id_evento'))
        ->pluck('eventos.cupo')
        ->first();
        
        $created = new Carbon($fecha->created_at);
        $now = Carbon::now();
        $difference = ($created->diff($now)->days < 1)
        ? 'today'
        : $created->diffForHumans($now);
        $asistentes=$prospect/$cupo;
        if ($difference<30) {
            if ($asistentes>0.6) {
                DB::table('eventos')
                    ->where('id','=',$request->input('id_evento') )
                    ->update(['tendencia' => 1]);
            }
        }
       return response()->json([$mensaje]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confAsis(Request $request)
    {
        $user = $request->user();
        $pros=DB::table('prospectos')
        ->select('prospectos.*')
        ->where('id_prospecto','=',$user["id"])
        ->where('id_evento','=',$request->input('id_evento'))
        ->first();
         return response()->json(['prospectos'=>$pros]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
