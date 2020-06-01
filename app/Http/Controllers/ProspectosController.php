<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Prospectos;
use App\User;
use Carbon\Carbon;
use App\Notificaciones;
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

    /**Obtener los eventos del usuario
     * En esta función se obtiene el usuario que ha realizado la petición para después unir la tabla prospectos con la tabla eventos y así obtener los eventos a los cuales va a asistir el usuario o ha guardado con motivo de interés o ha creado
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserEvent(Request $request)
    {
        $now= Carbon::now();
        $user = $request->user();
        $eventos = DB::table('prospectos')
            ->join('eventos', 'eventos.id', '=', 'prospectos.id_evento')
            ->select('eventos.*','prospectos.estado')
            ->where('id_prospecto',$user["id"])
            ->where('eventos.fecha','>', $now)
            ->get();
        return response()->json(['eventos'=>$eventos]);
    }

    /**Asistencia de evento
     * En este apartado se realiza la parte de en dado caso que un usuario quiera asistir a un evento o guarde el evento con motivo de interés, además de que se corrobora el número de asistentes a un evento. El cual ayuda para ponerlo en el apartado de tendencias
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Primeramente se decodifica el json que se ha enviado desde la aplicación
        $data = json_decode($request->getContent(), true);
        //Esta función nos ayuda a obtener la fecha de hoy y almacenarla en una variable
        $today=today();
        $hola='';
        //Después se obtiene el usuario actual y el evento a partir de su ID y si es que el usuario no se ha registrado anteriormente
        $user = $request->user();
        $pros=DB::table('prospectos')
        ->select('prospectos.estado')
        ->where('id_prospecto','=',$user["id"])
        ->where('id_evento','=',$request->input('id_evento'))
        ->first();
        $evento=DB::table('eventos')
                        ->where('id',$request->input('id_evento'))
                        ->first();
        $mensaje="Ya estas registrado";
        //En este primer condicionante se verifica que el usuario no se haya registrado anteriormente en el evento, de lo contrario se entrará a este condicionante en el cual se creará un nuevo objeto a partir del modelo prospectos el cual procederá a registrarse
        if(is_null($pros)){

            $prospectos = new Prospectos($data);
            $prospectos->confirmacionasistencia=0;
            $prospectos->id_prospecto=$user["id"];
            $prospectos->save();
            $mensaje="Registrado correctamente";
            //En esta función se crea la notificación para mostrarse 3 dias antes del evento mostrando el mensaje de que el evento que tienen esta por llegar, se llenan todos los campos y al final se crea ese objeto
                    if($request->input('estado')=="confirmado"){
                        $currentDate = new Carbon($evento->fecha);
                        $notificaciones= new Notificaciones();
                        $notificaciones->id_receptor=$user["id"];
                        $notificaciones->id_evento=$request->input('id_evento');
                        $notificaciones->fechaFin=$evento->fecha;
                        $notificaciones->fechaInicio=$currentDate->subDays(3);
                        $notificaciones->contenido=("No olvides que tienes el evento ".$evento->nombre_evento." el dia ".$evento->fecha."");
                        $notificaciones->tipoNotificacion=1;
                        $notificaciones->save();
                    }
        }
        //En este else if se corrobora que el estado del prospecto con el que se habia guardado en la base de datos sea diferente a la que se está enviando desde la aplicación
        else if($pros->estado!=$request->input('estado')){
            //En el caso que el estado de asistencia sea cancelar significa que ya no va a ir al evento por consiguiente se borra el prospecto y la notificación corresponiente a ese evento
            if ($request->input('estado')=='cancelar') {
                DB::table('prospectos')
                ->where('id_evento','=',$request->input('id_evento') )
                ->where('id_prospecto','=',$user["id"])
                ->delete();
                $mensaje="Borrado correctamente";
                DB::table('notificaciones')
                        ->where('id_evento',$request->input('id_evento'))
                        ->where('id_receptor','=',$user["id"])
                        ->delete();
            }
            else{
                //En caso que el estado no sea cancelar se verifica que es lo que tiene el estado, en dado caso que sea me interesa se actualizará el estado y se borrará la notificación porque no es seguro si va a ir
                 DB::table('prospectos')
                    ->where('id_evento','=',$request->input('id_evento') )
                    ->where('id_prospecto','=',$user["id"])
                    ->update(['prospectos.estado' => $request->input('estado')]);
                    $mensaje="Actualizado correctamente";
                    if ($request->input('estado')=='me interesa') {
                        DB::table('notificaciones')
                        ->where('id_evento',$request->input('id_evento'))
                        ->where('id_receptor','=',$user["id"])
                        ->delete();
                    }
                    else if($request->input('estado')=="confirmado"){
                    //En caso que el estado sea confirmado sea confirmado se crea la notificación que te muestra un mensaje con el nombre y la fecha del evento y se guarda la notificación
                        $currentDate = new Carbon($evento->fecha);
                        $notificaciones= new Notificaciones();
                        $notificaciones->id_receptor=$user["id"];
                        $notificaciones->id_evento=$request->input('id_evento');
                        $notificaciones->fechaFin=$evento->fecha;
                        $notificaciones->fechaInicio=$currentDate->subDays(3);
                        $notificaciones->contenido=("No olvides que tienes el evento ".$evento->nombre_evento." el dia ".$evento->fecha."");
                        $notificaciones->tipoNotificacion=1;
                        $notificaciones->save();
                    }
                    
            }
           
        }
        /*Para el caso de las tendencias se obtiene primero los datos del evento, el cupo del evento y los que ya han confirmado asistencia al evento
        */
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
        /*Primeramente se transforma la fecha a Carbo, esto de carbon nos sirve para manipular las fechas y añadir o quitar los tiempos que se requieran aqui lo que primeramente se hace es verificar que los días que han pasado desde que se creó el evento no pasen el mes. Una vez hecho esto se dividen los prospectos entre el cupo, en dado caso que la proporcion de cupo asistentes pase el 60% se colocará que el evento es tendencia
        */
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
        //Al final solo se retorna el mensaje
       return response()->json(['mensaje'=>$mensaje]);
    }

    /**
     * Obtener asistecia
     *Lo que se hace aqui es obtener la asistencia del usuario que ha realizado la petición para poner en la información expandida del evento y seleccionar el botón que por el momento está activo
     * 
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
