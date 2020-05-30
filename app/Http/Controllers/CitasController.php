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
use App\Notificaciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CitasController extends Controller
{
    /**
     * Agendar Cita
     * Se obtienen los datos del json y se decodifican para guardarlos en una variable para después obtener el usuario y se crea el objeto con el modelo de citas, y se guarda la cita para enviar el mensaje de que la cita se ha hecho correctamente
     * @return \Illuminate\Http\Response
     */
    public function agendarCita(Request $request)
    {
        $data= json_decode($request->getContent(), true);
        $user= $request->user();
        $now= Carbon::now();

        $fecha= new Carbon($request->input('fecha'));
        if ($fecha<$now) {
           return response()->json(['error' => 'la fecha esta en el pasado'],404);
        }
        else{
            $evento= DB::table('eventos')
            ->where('id',$request->input('id_evento'))
            ->pluck('eventos.fecha')
            ->first();
            $fechaEvento= new Carbon($evento);
            $fechaEvento=$fechaEvento->subDays(7);
            if ($fechaEvento<$fecha) {
                return response()->json(['error' => 'la fecha tiene que ser una semana antes'],404);
            }
            else{
        $fecha=DB::table('citas')
        ->where('fecha',$request->input('fecha'))
        ->where('id_servicio',$request->input('id_servicio'))
        ->first();
        if (is_null($fecha)) {
        $fecha=DB::table('servicioscontratados')
        ->where('fecha',$request->input('fecha'))
        ->where('id_servicio',$request->input('id_servicio'))
        ->first();
        if (is_null($fecha)) {
        $horario=DB::table('catalogo_servicios')
        ->where('id',$request->input('id_servicio'))
        ->pluck('catalogo_servicios.horarioApertura')
        ->first();
        $horaDos=Carbon::createFromTimeString($horario);
        $hora = Carbon::createFromTimeString($request->input('hora'));
        if ($horaDos<$hora) {
        $id_proveedor=DB::table('catalogo_servicios')
        ->where('id',$request->input('id_servicio'))
        ->pluck('catalogo_servicios.id_usuario')
        ->first();
        $cita= new Citas();
        $cita->id_proveedor=$id_proveedor;
        $cita->id_servicio=$request->input('id_servicio');
        $cita->id_evento=$request->input('id_evento');
        $cita->fecha=$request->input('fecha');
        $cita->hora=$request->input('hora');
        $cita->id_usuario=$user["id"];
        $cita->save();

        $nombre_evento= DB::table('eventos')
            ->where('eventos.id',$request->input('id_evento'))
            ->pluck('eventos.nombre_evento')
            ->first();
        $nombre_servicio=DB::table('catalogo_servicios')
        ->where('catalogo_servicios.id',$request->input('id_servicio'))
        ->pluck('catalogo_servicios.nombre')
        ->first();

        $notificaprove= new Notificaciones();
        $notificaprove->id_receptor=$id_proveedor;
        $notificaprove->contenido=("Tienes una cita para el anticipo del pago del servicio ".$nombre_servicio." el dia ".$request->input('fecha')."");
        $notificaprove->fechaInicio=Carbon::now();
        $notificaprove->fechaFin=$request->input('fecha');
        $notificaprove->tipoNotificacion=4;
        $notificaprove->id_evento=$request->input('id_evento');
        $notificaprove->save();

        $notificar= new Notificaciones();
        $notificar->id_receptor=$user["id"];
        $notificar->contenido=("Tienes una cita para el anticipo del pago del servicio ".$nombre_servicio." el dia ".$request->input('fecha')."");
        $notificar->fechaInicio=Carbon::now();
        $notificar->fechaFin=$request->input('fecha');
        $notificar->tipoNotificacion=4;
        $notificar->id_evento=$request->input('id_evento');
        $notificar->save();

            return response()->json(['message' => 'Date added succesfully!']);
        }
        else{
            return response()->json(['message'=>'La hora no está disponible'],404);   
        }
        }
        else{
            return response()->json(['messagessss'=>'La fecha esta ocupada'],404);
        }
        }
        else{
            return response()->json(['message'=>'La fecha esta ocupada'],404);
        }
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userCitas(Request $request)
    {
        $user=$request->user();
        $citas=DB::table('citas')
        ->where('id_usuario',$user["id"])
        ->get();
        return response()->json(['citas'=>$citas]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function provCitas(Request $request)
    {
        $user=$request->user();
        $citas=DB::table('citas')
        ->where('id_proveedor',$user["id"])
        ->get();
        return response()->json(['citas'=>$citas]);
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
