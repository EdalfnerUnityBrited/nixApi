<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Cupo;
use App\Eventos;
use App\Prospectos;
use App\CatalogoServicio;
use Carbon\Carbon;
use App\Notificaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CatalogoServicioController extends Controller
{
    /** Busqueda de Servicios 
     * En esta función se guardan en variables los nombres que se obtienen de la aplicación dependiendo de
     * el parametro que se esta enviando. Es entonces que utilizando el metodo de QueryScope se llaman a las
     * funciones enviando las variables y realizando la búsqueda y obteniendo todos los resultados encontrados
     * al igual que en paquetes y en artículos se retornan en un Json hacia la aplicación
     *
     * @return \Illuminate\Http\Response
     */
    public function buscarServicio(Request $request)
    {
        $nombre       = $request->input('nombre');
        $categoria    = $request->input('categoriaevento');

        $servicio = CatalogoServicio::orderBy('id','DESC')
        ->nombre($nombre) 
        ->categoria($categoria)
        ->get();

        return response()->json(['servicios'=>$servicio]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserService(Request $request)
    {
        $user= $request->user();
        $servicio= DB::table('catalogo_servicios')
        ->where('id_usuario',$user["id"])
        ->get();

        return response()->json(['servicios'=>$servicio]);
    }

    /**Crear Servicio
     * Primeramente se decodifica el contenido enviado desde la aplicación para poder transformarlo a objetos
     * una vez transformado a objetos, se llama al modelo de CatalogoServicio para crear un servicio con el objeto
     * recibdo por parte del request para después guardar el objeto y retornar que se ha creado satisfactoriamente
     *
     * @return \Illuminate\Http\Response
     */
    public function newService(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $user= $request->user();
        $servicio= new CatalogoServicio($data);
        $servicio->calificacion=5;
        $servicio->id_usuario=$user["id"];
        $servicio->save();
        return response()->json([
                    'servicio' => $servicio], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getServiceId(Request $request)
    {

        $servicio= DB::table('catalogo_servicios')
        ->where('id',$request->input('id'))
        ->first();

        return response()->json(['servicio'=>$servicio]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizarServicio(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $servicio = CatalogoServicio::find($request->input('id'));
        $servicio->nombre=$request->input('nombre');
        $servicio->categoriaevento=$request->input('categoriaevento');
        $servicio->direccion=$request->input('direccion');
        $servicio->telefono=$request->input('telefono');
        $servicio->horarioApertura=$request->input('horarioApertura');
        $servicio->horarioCierre=$request->input('horarioCierre');
        $servicio->lunes=$request->input('lunes');
        $servicio->martes=$request->input('martes');
        $servicio->miercoles=$request->input('miercoles');
        $servicio->jueves=$request->input('jueves');
        $servicio->viernes=$request->input('viernes');
        $servicio->sabado=$request->input('sabado');
        $servicio->domingo=$request->input('domingo');
        $servicio->nombreProveedor=$request->input('nombreProveedor');
        $servicio->save();
        return response()->json(['servicio'=>$servicio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function eraseService(Request $request)
    {
        DB::table('catalogo_servicios')
        ->where('id', '=', $request->input('id'))
        ->delete();
        return response()->json(['message'=>'Borrado satisfactoriamente']);
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
