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
        $prospectos = new Prospectos($data);
        $prospectos->confirmacionasistencia=0;
        $prospectos->id_prospecto=$user["id"];
        $prospectos->save();
        $fecha=DB::table('eventos')
        ->select('eventos.created_at')
        ->where('eventos.id','=',$request->input('id_evento'))
        ->first();
        $prospect=DB::table('prospectos')
        ->where('id_evento','=',$request->input('id_evento'))
        ->count();
        $cupo=DB::table('eventos')
        ->where('eventos.id','=',$request->input('id_evento'))
        ->pluck('eventos.cupo')
        ->first();
        $hola="no jala";
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
       return response()->json(['eventos'=>$asistentes]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
