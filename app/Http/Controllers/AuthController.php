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
    /*Creacion de la cuenta
        En esta parte utilizando los metodos de laravel obtenemos desde la aplicacion un objeto en json el cual vamos a decodificar y a guardar en un objeto, para después verificar que los datos existan, que las contraseñas estén confirmadas y que el email no esté repetido. Después de validar todos estos campos se procede a crear el usuario utilizando la clase user, se encripta la contraseña, se coloca una imágen de default en lo que el usuario la cambia, se añade al usuario a nuestra lista de clientes de stripe y se envia el mensaje de que el usuario ha sido creado satisfactoriamente
    */  
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
    /*Crear cuenta con Facebook o Google
        Aqui al igual que en el metodo de arriba, se obtienen todos los datos del json que se mandó y se verifican que todos los campos sean correctos, para solo encriptar la contraseña y después crear el usuario además de añadirlo a nuestra lista de clientes para en dado caso que el usuario quiera realizar un pago en linea y al final se envía el mensaje de que se ha creado el usuario correctamente
    */
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
    /*Inicio de sesión
        En esta parte se obtiene el json y se convierte, para después validar el email, el password y el remember_me, en dado caso que estos campos estén correctamente validados se pasará a realizar la validación de que el email y el passsword coincidan con los del usuario, en dado caso que sean incorrectos solo se envia el mensaje de que no está autorizado. En dado caso que si esté autorizado se crea un token para que no tenga que iniciar sesión cada vez, y se manda el usuario y el token

    */
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
    /*Cerrar Sesión
        En este caso se quita el token de iniciar sesión y se manda el mesaje de que todo estuvo correcto
    */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 
            'Successfully logged out']);
    }
    /*Enviar la informacion del usuario
        Se envia un json con los datos del usuario
    */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    /*Cambio de foto
        Primeramente se obtiene el usuario que está actualmenete logueado para después proceder a obtener del request el atributo "fotoperfil", ahora solo se guardan cambios y se retorna el mensaje de que todo fue un exito.
    */
    public function cambioFoto(Request $request)
    {
        
        $user = $request->user();
        $user->fotoPerfil = $request->input('fotoPerfil');
        $user->save();
        return response()->json(['message'=>'Successfully updated photo']);
    }
    /*Cambio de informacion
        Primeramente se obtienen los datos de un json, el cual se valida que los datos que se espera recibir sean correctos. Entonces una vez validados los datos se procederá a obtener el usuario con el cual se realizó la petición, se actualizan los datos se guardan cambios y al final se retorna la respuesta de que todo estuvo correcto
    */
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
    /*Cambio de contraseña
        Aqui se obtienen los datos y se decodifica el json para después validar que la contraseña cuente con una confirmación de contraseña y que sea string. Después se procede a obtener el usuario con el cual se ha realizado la petición, se cambia la contraseña. Se encripta y se guarda, para despues enviar el mensaje de que estuvo todo correcto
    */
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

    //Documentación posterior, función no terminada
    public function payment(Request $request){


        $user=$request->user();
        $stripeCharge = $user->charge(10000, $request->input('paymentMethodId'),[
            "description" => 'Prueba de mensaje para stripe',
            'receipt_email' => 'edalfnerunity@gmail.com',
        ]);
        return response()->json(['message'=>'Successfully added card!']);
    
    }
    /*Verificar email
        En este apartado se verifica que el email ingresado exista, en el caso de que si exista el correo se envía un objeto de tipo usuario, en caso contrario la respuesta es nula
    */
    public function existeciaCuenta(Request $request){
        $user=DB::table('users')
        ->select('users.*')
        ->where('users.email','=',$request->input('fotoPerfil'))
        ->first();
         return response()->json(['usuario'=>$user]);   
    }

}
