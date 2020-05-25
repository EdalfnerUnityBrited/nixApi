<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Cupo;
use App\Eventos;
use App\Imagenpaquete;
use Carbon\Carbon;
use App\Paquete;
use App\Cotizaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function newCotizacion(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $cotizacion= new Cotizaciones($data);
        $cotizacion->estado='guardado';
        $cotizacion->save();
        return response()->json([
                    'cotizacion' => $cotizacion], 201);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserCot(Request $request)
    {
        $user=$request->user();
        $cotizaciones= DB::table('cotizaciones')
        ->join('eventos', 'cotizaciones.id_evento', '=', 'eventos.id')
        ->join('users', 'eventos.id_creador', '=', 'users.id')
        ->where('users.id',$user["id"])
        ->select('cotizaciones.*')
        ->get();

         return response()->json([
                    'cotizaciones' => $cotizaciones]);
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
