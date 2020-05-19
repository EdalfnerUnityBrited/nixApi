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
     * Guardar imagenes de evento
     * Aqui se obtiene el json desde la aplicaión y se guarda el array de objetos en una variable, la cual entrará a un foreach que cuardará cada objeto en la base de datos para al final mandar el mensaje de que todo estuvo correcto
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
     * Se obtiene un objeto desde el json, se crea un nuevo objeto tipo Imagenevento y al final se guarda para después enviar el mensaje de que todo fue satisfactorio
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
     * Aqui se obtiene desde el request el string con el cual se identifica la imagen para después buscar similitudes y borrar la imagen en la base de datos
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
