<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
        $user->save();
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
                    'usuario' => $user,
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
}
