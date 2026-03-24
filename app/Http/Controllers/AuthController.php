<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{

public function login(Request $request)
{
    $request->validate([
        'login' => 'required|string',  
        'contrasena' => 'required|string'
    ]);

    $user = User::where('nombre_usuario', $request->login)
                ->orWhere('email', $request->login)
                ->with('tipoRol') 
                ->first();

    if (!$user || !Hash::check($request->contrasena, $user->contrasena)) {
        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }

   
    $token = JWTAuth::fromUser($user);

    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',//y
        'user' => [
            'id' => $user->id,
            'nombre_usuario' => $user->nombre_usuario,
            'email' => $user->email,
            'rol' => $user->tipoRol->nombre ?? null, 
        ]
    ]);
}

public function me()
{
    $user = auth()->user()->load('tipoRol'); 
    return response()->json($user);
}

  
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
