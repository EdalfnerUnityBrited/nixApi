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

class ImagenEventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addImage(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $imagenes=collect($data)->all();
        foreach ($imagenes as $imagen) {
            $image= new Imageneventos($imagen);
            $image->save();
        }
        
        return response()->json(['message'=>'Successfully added photos'], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addOne(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $image= new Imageneventos($data);
        $image->save();
        return response()->json(['message'=>'Successfully added photos'], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function eraseImage(Request $request)
    {
        DB::table('imageneventos')
        ->where('imagen', '=', $request->input('imagen'))
        ->delete();
        return response()->json(['message'=>'Successfully erased photos'], 201);
    
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
