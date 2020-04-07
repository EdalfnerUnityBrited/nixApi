<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Cupo;
use App\Eventos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EventosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserEvents(Request $request)
    {
        $user =$request->user();

        $evento =DB::table('eventos')
                ->join('users', 'eventos.id_creador', '=', 'users.id')
                ->select('eventos.*','users.telefono', 'users.name')
                ->where('id_creador', $user["id"])
                ->get();
                return response()->json(['eventos'=>$evento]);
    
    }


    public function newevento(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        Validator::make($data, [
          //  'id_proveedor'    => 'required',
        ])->validate();




        $user = $request->user();
        $evento = new Eventos($data);
        $evento->id_creador=$user["id"];
        $evento->save();

        
        $eventoActual= $evento;
        DB::table('imageneventos')->insert([
            'imagen' => $request->input('fotoevento'),
            'id_evento' => $eventoActual->id,
            ]);
        return response()->json([
                    'message' => 'Event created succesfully'], 201);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllEvents()
    {
         $eventos = DB::table('eventos')->get();
        return response()->json(['eventos'=>$eventos]);
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
