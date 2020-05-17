<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;

class AuthController extends Controller
{
    public function signup(Request $request) {
        $data = json_decode($request->getContent(), true);
        
        Validator::make($data, [
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:App\User',
            'password' => 'required|string|confirmed',
        ])->validate();

        $user = new User($data);
        $user->password = Hash::make($user->password);
        $user->fotoPerfil="https://i.pinimg.com/originals/c7/ea/97/c7ea97eebcc9ae38ca27939a34713ff0.jpg";
        $user->createAsStripeCustomer();
        return response()->json([
                    'message' => 'Successfully created user!'], 201);
    }

    public function signupFG(Request $request) {
        $data = json_decode($request->getContent(), true);
        
        Validator::make($data, [
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:App\User',
            'password' => 'required|string|confirmed',
        ])->validate();

        $user = new User($data);
        $user->password = Hash::make($user->password);
        $user->createAsStripeCustomer();
        return response()->json([
                    'message' => 'Successfully created user!'], 201);
    }

    public function login(Request $request) {
        $data = json_decode($request->getContent(), true);
        
        Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ])->validate();

        $credentials = ['email' => $data['email'], 'password' => $data['password']];
        if (!Auth::attempt($credentials)) {
            return response()->json([
                        'message' => 'Unauthorized'], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                                    $tokenResult->token->expires_at)
                            ->toDateTimeString(),
                            'usuario'=>$user
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 
            'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    public function cambioFoto(Request $request)
    {
        
        $user = $request->user();
        $user->fotoPerfil = $request->input('fotoPerfil');
        $user->save();
        return response()->json(['message'=>'Successfully updated photo']);
    }
    public function cambioDatos(Request $request)
    {
        $data = json_decode($request->getContent(), true);
         Validator::make($data, [
            'name' => 'required|string',
            'apellidoP'=>'required|string',
            'apellidoM'=>'required|string',
            'fechaNac' => 'required|string',
            'telefono' => 'required|string',
        ])->validate();
        $user=$request->user();
        $user->name= $request->input('name');
        $user->apellidoP= $request->input('apellidoP');
        $user->apellidoM=$request->input('apellidoM');
        $user->fechaNac=$request->input('fechaNac');
        $user->telefono=$request->input('telefono');
        $user->save();
        return response()->json(['message'=>'Successfully updated data']);
    }
    public function cambioContrasena(Request $request){
        $data = json_decode($request->getContent(), true);
         Validator::make($data, [
            'password' => 'required|string|confirmed',
        ])->validate();
         $user=$request->user();
         $user->password=$request->input('password');
         $user->password = Hash::make($user->password);
         $user->save();
         return response()->json(['message'=>'Successfully updated password']);
    }
    public function payment(Request $request){


        $user=$request->user();
        $user->addPaymentMethod($request->input('paymentMethodId'));
        $stripeCharge = $user->charge(100, $request->input('paymentMethodId'));
        return response()->json(['message'=>'Successfully added card!']);
    
    }
    public function existeciaCuenta(Request $request){
        $user=DB::table('users')
        ->select('users.*')
        ->where('users.email','=',$request->input('fotoPerfil'))
        ->first();
         return response()->json(['usuario'=>$user]);   
    }
    public function retrievePayment(Request $request){
    	$user=$request->user();
    	if ($user->hasPaymentMethod()) {
    	$paymentMethod = $user->defaultPaymentMethod();
    	


    	return response()->json(['message'=>'OK']);
		}
    	
    	return response()->json(['message'=>'No tienes metodos']);
    }
}
