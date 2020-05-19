<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Cupo;
use App\Eventos;
use App\Imageneventos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class NotificacionesController extends Controller
{
    /**
     * Obtener notificaciones
     * Aqui se obtienen las notificaciones del usuario que ha solicitado la petición, primeramente se obtiene la fecha de hoy y el usuario. Después llamamos a la tabla notificaciones donde la fecha actual se encuentre entre la fecha de inicio de la notificacion y la fecha final de la notificacion. Y se obtienen todas las notificaciones para mandarlas en el json
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
        $now= Carbon::now();
        $user= $request->user();
        $notificaciones= DB::table('notificaciones')
        ->where('fechaInicio','<',$now)
        ->where('fechaFin','>',$now)
        ->where('id_receptor',$user["id"])
        ->get();
        return response()->json(['notificaciones'=>$notificaciones]);

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
