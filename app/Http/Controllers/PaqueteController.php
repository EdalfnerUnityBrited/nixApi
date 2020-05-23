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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class PaqueteController extends Controller
{
    /**Crear Paquete
     * Primeramente se decodifica el contenido enviado desde la aplicación para poder transformarlo a objetos
     * una vez transformado a objetos, se llama al modelo de Paquete para crear un articulo con el objeto
     * recibido por parte del request para después guardar el objeto y retornar que se ha creado satisfactoriamente
     *
     * @return \Illuminate\Http\Response
     */
    public function addPaquete(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $user= $request->user();
        $paquete= new Paquete($data);
        $paquete->save();
        return response()->json([
                    'paquete' => $paquete], 201);
    }

    /** Busqueda de Paquetes 
     * En esta función se guardan en variables los nombres que se obtienen de la aplicación dependiendo de
     * el parametro que se esta enviando. Es entonces que utilizando el metodo de QueryScope se llaman a las
     * funciones enviando las variables y realizando la búsqueda y obteniendo todos los resultados encontrados
     * despues de obtener los resultados se envían a la aplicación mediante un json
     *
     * @return \Illuminate\Http\Response
     */
    public function buscarPaquete(Request $request)
    {
        $nombre      = $request->get('nombre');
        $precioInicio = $request->get('precioIni');
        $precioFinal  = $request->get('precioFin');


        $paquete = Paquete::orderBy('id','DESC')
        //->nombre($nombre)
        ->precio($precioInicio, $precioFinal)
        ->get();

        return response()->json(['paquete'=>$paquete]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPaqueteServicio(Request $request)
    {
        $paquete=DB::table('paquetes')
        ->where('id_servicio',$request->input('id'))
        ->get();

        return response()->json(['paquetes'=>$paquete]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function erasePackage(Request $request)
    {
        DB::table('paquetes')
        ->where('id', '=', $request->input('id'))
        ->delete();
        return response()->json(['message'=>'borrado correctamente']);
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
