<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Cupo;
use App\Eventos;
use App\Citas;
use App\Imageneventos;
use App\Servicioscontratados;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CerviciosContratadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newContratado(Request $request)
    {
        $hora= DB::table('catalogo_servicios')
            ->join('cotizaciones', 'cotizaciones.id_servicio', '=', 'catalogo_servicios.id')
            ->join('eventos', 'cotizaciones.id_evento', '=', 'eventos.id')
            ->where('catalogo_servicios.id',$request->input('id_catalogo'))
            ->pluck('eventos.hora')
            ->first();
        $fecha= DB::table('catalogo_servicios')
            ->join('cotizaciones', 'catalogo_servicios.id', '=', 'cotizaciones.id_servicio')
            ->join('eventos', 'eventos.id', '=', 'cotizaciones.id_evento')
            ->where('catalogo_servicios.id',$request->input('id_catalogo'))
            ->pluck('eventos.fecha')
            ->first();
        $id= DB::table('catalogo_servicios')
            ->join('cotizaciones', 'catalogo_servicios.id', '=', 'cotizaciones.id_servicio')
            ->join('eventos', 'eventos.id', '=', 'cotizaciones.id_evento')
            ->where('catalogo_servicios.id',$request->input('id_catalogo'))
            ->pluck('eventos.id')
            ->first();
        $contratacion=new Servicioscontratados();
        $contratacion->estado_servicio='pendiente';
        $contratacion->fecha=$fecha;
        $contratacion->hora=$hora;
        $contratacion->metodo_pago=$request->input('metodo_pago');
        $contratacion->id_servicio=$request->input('id_catalogo');
        $contratacion->id_evento=$id;
        $contratacion->save();
        return response()->json(['message'=>'Servicio contratado']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
