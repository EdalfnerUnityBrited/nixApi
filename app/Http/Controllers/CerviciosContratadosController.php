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
        $hora= DB::table('eventos')
            ->where('eventos.id',$request->input('id_evento'))
            ->pluck('eventos.hora')
            ->first();
        $fecha= DB::table('eventos')
            ->where('eventos.id',$request->input('id_evento'))
            ->pluck('eventos.fecha')
            ->first();
        $nombre_evento= DB::table('eventos')
            ->where('eventos.id',$request->input('id_evento'))
            ->pluck('eventos.nombre_evento')
            ->first();
        $id_proveedor= DB::table('catalogo_servicios')
            ->where('catalogo_servicios.id',$request->input('id_servicio'))
            ->pluck('catalogo_servicios.id_usuario')
            ->first();
        $now = Carbon::now();
                        $notificaciones= new Notificaciones();
                        $notificaciones->id_receptor=$id_proveedor;
                        $notificaciones->id_evento=$request->input('id_evento');
                        $notificaciones->fechaFin=$fecha;
                        $notificaciones->fechaInicio=$now;
                        $notificaciones->contenido=("Tienes un servicio para el evento ".$nombre_evento." el dia ".$fecha."");
                        $notificaciones->tipoNotificacion=1;
                        $notificaciones->save();
       $contratacion=new Servicioscontratados();
       if ($request->input('metodo_pago')=='linea') {
           $contratacion->estado_servicio='pendiente';
       }
       else{
        $contratacion->estado_servicio='solicitado';
       }
        
        $contratacion->fecha=$fecha;
        $contratacion->hora=$hora;
        $contratacion->metodo_pago=$request->input('metodo_pago');
        $contratacion->id_servicio=$request->input('id_servicio');
        $contratacion->desglose=$request->input('desglose');
        $contratacion->id_evento=$request->input('id_evento');
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
        $now= Carbon::now()->subDays(3);
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre','eventos.lugar')
        ->where('catalogo_servicios.id_usuario',$user["id"])
        ->where('servicioscontratados.fecha','>',$now)
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
        $now= Carbon::now()->subDays(3);
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre')
        ->where('eventos.id_creador',$user["id"])
        ->where('servicioscontratados.fecha','>',$now)
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
    public function anticipoPendiente(Request $request)
    {
        $now= Carbon::now();
        $servicio= DB::table('servicioscontratados')
        ->where('fecha','<',Carbon::now()->addDays(7))
        ->where('estado_servicio','solicitado')
        ->delete();
        

        return response()->json(['message'=>'Borrado satisfactoriamente']);

    }

    public function borrarContrat(Request $request)
    {
        
        $now= Carbon::now()->addDays(7);
        $fecha= DB::table('servicioscontratados')
        ->where('id',$request->input('id'))
        ->pluck('servicioscontratados.fecha')
        ->first();

        $fechaSer= new Carbon($fecha);
        if ($fechaSer<$now) {
            return response()->json(['message'=>'Se tiene que cancelar con mas de una semana de anticipacion'],404);
        }


        $id_proveedor=DB::table('catalogo_servicios')
        ->where('id',$request->input('id_servicio'))
        ->pluck('id_usuario')
        ->first();


        $notificaciones=DB::table('notificaciones')
        ->join('eventos', 'eventos.id', '=', 'notificaciones.id_evento')
        ->where('notificaciones.id_receptor',$id_proveedor)
        ->where('eventos.id', $request->input('id_evento'))
        ->update(['contenido' => ("El servicio ".$request->input('nombre')." para el evento ".$request->input('nombre_evento')." ha sido cancelado")]);
        
        $servicio= DB::table('servicioscontratados')
        ->where('id',$request->input('id'))
        ->delete();
        return response()->json(['message'=>'Borrado correctamente']);

    }

    public function calificar(Request $request){

        $id_servicio=DB::table('servicioscontratados')
        ->where('servicioscontratados.id',$request->input('id_servicio'))
        ->pluck('servicioscontratados.id_servicio')
        ->first();

        $id_evento=DB::table('servicioscontratados')
        ->where('servicioscontratados.id',$request->input('id_servicio'))
        ->pluck('servicioscontratados.id_evento')
        ->first();

        $existe= DB::table('calificacion')
        ->where('id_evento',$id_evento)
        ->first();

        if (is_null($existe)) {
                    DB::table('calificacion')->insert(
        ['calificacion' => $request->input('calificacion'), 'id_servicio' => $id_servicio, 'id_evento'=> $id_evento]
        );

        $calificacion=DB::table('calificacion')
                    ->where('id_servicio', $id_servicio)
                    ->avg('calificacion');

        DB::table('catalogo_servicios')
        ->where('id',$id_servicio)
        ->update(['calificacion'=>$calificacion]);

        return response()->json(['message'=>'Calificado']);
        }



        return response()->json(['message'=>'Ya lo calificaste'],404);
                
    }

        public function getHistUserContra(Request $request)
    {
        $now= Carbon::now()->subDays(3);
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre')
        ->where('estado_servicio','pagado')
        ->orWhere('estado_servicio','calificado')
        ->where('eventos.id_creador',$user["id"])
        ->where('servicioscontratados.fecha','<',$now)
        ->get();
         return response()->json(['contrataciones'=>$contratacion]);
    }

        public function getHistContrataciones(Request $request)
    {
        $now= Carbon::now()->subDays(3);
        $user= $request->user();
        $contratacion= DB::table('servicioscontratados')
        ->join('catalogo_servicios', 'catalogo_servicios.id', '=', 'servicioscontratados.id_servicio')
        ->join('eventos', 'eventos.id', '=', 'servicioscontratados.id_evento')
        ->select('servicioscontratados.*','eventos.nombre_evento','catalogo_servicios.nombre','eventos.lugar')
        ->where('estado_servicio','pagado')
        ->orWhere('estado_servicio','calificado')
        ->where('catalogo_servicios.id_usuario',$user["id"])
        ->where('servicioscontratados.fecha','<',$now)
        ->get();
         return response()->json(['contrataciones'=>$contratacion]);
    }

}
