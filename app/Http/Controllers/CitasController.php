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
        $cita= new Citas($data);
        $cita->id_usuario=$user["id"];
        $cita->save();
        return response()->json(['message' => 'Date added succesfully!']);
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
