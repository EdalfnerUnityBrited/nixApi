<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chats;
use App\User;
use Illuminate\Support\Facades\DB;
class ChatsController extends Controller
{


     public function newchat(Request $request) {
        $data = json_decode($request->getContent(), true);
        $proveedor= DB::table('catalogo_servicios')
        ->where('catalogo_servicios.id',$request->input('id_catalogo'))
        ->pluck('catalogo_servicios.id_usuario')
        ->first();


        $user = $request->user();
        $verify= DB::table('chats')
            ->where('id_usuario',$user["id"])
            ->where('id_proveedor',$proveedor)
            ->first();

        if (is_null($verify)) {
        $chats = new Chats($data);
        $chats->id_usuario=$user["id"];
        $chats->id_proveedor=$proveedor;
        $chats->save();
        return response()->json([
                    'message' => 'Successfully created chat!'], 201);
        }
        return response()->json([
                    'message' => 'El chat ya existe'], 201);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
       $chats =DB::table('chats')
                ->join('users', 'chats.id_proveedor', '=', 'users.id')
                ->select('users.id','users.email', 'users.name')
                ->where('id_usuario', $user["id"])
                ->get();


        return response()->json(['chats'=>$chats]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function proveedor(Request $request)
    {
        $user = $request->user();
       $chats =DB::table('chats')
                ->join('users', 'chats.id_usuario', '=', 'users.id')
                ->select('users.id','users.email', 'users.name')
                ->where('id_proveedor', $user["id"])
                ->get();


        return response()->json(['chats'=>$chats]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $chats = DB::table('chats')->get();
        return response()->json(['chats'=>$chats]);
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
