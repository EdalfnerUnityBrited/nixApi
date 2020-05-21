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
use App\Articulo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ArticuloController extends Controller
{
    /**Crear Articulo
     * Primeramente se decodifica el contenido enviado desde la aplicación para poder transformarlo a objetos
     * una vez transformado a objetos, se llama al modelo de Articulo para crear un articulo con el objeto
     * recibdo por parte del request para después guardar el objeto y retornar que se ha creado satisfactoriamente
     *
     * @return \Illuminate\Http\Response
     */
    public function crearArticulo(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $servicio= new Articulo($data);
        $servicio->save();
        return response()->json([
                    'articulo' => $servicio], 201);   
    }

    /** Busqueda de Articulos 
     * En esta función se guardan en variables los nombres que se obtienen de la aplicación dependiendo de
     * el parametro que se esta enviando. Es entonces que utilizando el metodo de QueryScope se llaman a las
     * funciones enviando las variables y realizando la búsqueda y obteniendo todos los resultados encontrados
     *
     * @return \Illuminate\Http\Response
     */
    public function buscarArticulo(Request $request)
    {
        $nombre       = $request->get('nombre');
        $precioInicio = $request->get('precioIni');
        $precioFinal  = $request->get('precioFin');
        $precioPor    = $request->get('precioPor');
        $categoria    = $request->get('categoria');

        $paquete = Paquete::orderBy('id','DESC')
        ->nombre($nombre_evento)
        ->precio($precioInicio, $precioFinal)
        ->precioPor($precioPor)
        ->categoria($categoria)
        ->get();

        return response()->json(['paquete'=>$paquete]);
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
