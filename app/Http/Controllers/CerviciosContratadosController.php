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
use App\Notificaciones;
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
            ->where('catalogo_servicios.id',$request->input('id_servicio'))
            ->pluck('eventos.hora')
            ->first();
        $fecha= DB::table('catalogo_servicios')
            ->join('cotizaciones', 'catalogo_servicios.id', '=', 'cotizaciones.id_servicio')
            ->join('eventos', 'eventos.id', '=', 'cotizaciones.id_evento')
            ->where('catalogo_servicios.id',$request->input('id_servicio'))
            ->pluck('eventos.fecha')
            ->first();
        $id= DB::table('catalogo_servicios')
            ->join('cotizaciones', 'catalogo_servicios.id', '=', 'cotizaciones.id_servicio')
            ->join('eventos', 'eventos.id', '=', 'cotizaciones.id_evento')
            ->where('catalogo_servicios.id',$request->input('id_servicio'))
            ->pluck('eventos.id')
            ->first();
        $nombre_evento= DB::table('catalogo_servicios')
            ->join('cotizaciones', 'catalogo_servicios.id', '=', 'cotizaciones.id_servicio')
            ->join('eventos', 'eventos.id', '=', 'cotizaciones.id_evento')
            ->where('catalogo_servicios.id',$request->input('id_servicio'))
            ->pluck('eventos.nombre_evento')
            ->first();
        $id_proveedor= DB::table('catalogo_servicios')
            ->where('catalogo_servicios.id',$request->input('id_servicio'))
            ->pluck('catalogo_servicios.id_usuario')
            ->first();
        $now = Carbon::now();
                        $notificaciones= new Notificaciones();
                        $notificaciones->id_receptor=$id_proveedor;
                        $notificaciones->id_evento=$id;
                        $notificaciones->fechaFin=$fecha;
                        $notificaciones->fechaInicio=$now;
                        $notificaciones->contenido=("Tienes un servicio para el evento ".$nombre_evento." el dia ".$fecha."");
                        $notificaciones->tipoNotificacion=1;
                        $notificaciones->save();
       $contratacion=new Servicioscontratados();
        $contratacion->estado_servicio='pendiente';
        $contratacion->fecha=$fecha;
        $contratacion->hora=$hora;
        $contratacion->metodo_pago=$request->input('metodo_pago');
        $contratacion->id_servicio=$request->input('id_servicio');
        $contratacion->desglose=$request->input('desglose');
        $contratacion->id_evento=$id;
        $contratacion->save();
        return response()->json(['message'=>'servicio contratado satisfactoriamente']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContrataciones(Request $request)
    {
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre','eventos.lugar')
        ->where('catalogo_servicios.id_usuario',$user["id"])
        ->get();
         return response()->json(['contrataciones'=>$contratacion]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserContra(Request $request)
    {
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre')
        ->where('eventos.id_creador',$user["id"])
        ->get();
         return response()->json(['contrataciones'=>$contratacion]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contId(Request $request)
    {
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre','eventos.lugar')
        ->where('servicioscontratados.id',$request->input('id'))
        ->get();
         return response()->json(['contrataciones'=>$contratacion]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getevent(Request $request)
    {
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->join('users', 'users.id', '=', 'eventos.id_creador')
        ->select('servicioscontratados.desglose','eventos.*','users.name','users.apellidoP','users.apellidoM','users.telefono','users.email','catalogo_servicios.nombre','servicioscontratados.estado_servicio')
        ->where('servicioscontratados.id',$request->input('id'))
        ->first();
         return response()->json(['contrataciones'=>$contratacion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cambioEstado(Request $request)
    {
        $servicio=Servicioscontratados::find($request->input('id'));
        $servicio->estado_servicio = $request->input('estado_servicio');
        $servicio->save();
        return response()->json(['message'=>'Actualizado contratacion de servicio']);
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
