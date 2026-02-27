<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 📌 Solo lista básica (para tabla principal)
    public function index()
    {
        // Solo traemos los campos mínimos

    $users = User::all(); // trae todos los campos de la tabla users
    return response()->json($users, 200);

    }

    // 📌 Consultar todos los datos de un usuario por ID
    public function show($id)
    {
        // Aquí sí cargamos las relaciones completas
        $user = User::with(['tipoDocumento', 'tipoRol'])->find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user, 200);
    }

    // 📌 Crear usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|unique:users,nombre_usuario',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'tipo_documento_id' => 'required|integer|exists:tiposdocumento,id',
            'numero_documento' => 'required|string|unique:users,numero_documento',
            'celular' => 'required|integer|unique:users,celular',
            'direccion'=> 'required|string',
            'ciudad'=> 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'contrasena' => 'required|string|min:6',
            'tipo_rol_id' => 'required|integer|exists:tiposrol,id',
        ]);

        $user = User::create([
            'nombre_usuario' => $request->nombre_usuario,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'tipo_documento_id' => $request->tipo_documento_id,
            'numero_documento' => $request->numero_documento,
            'celular' => $request->celular,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'email' => $request->email,
            'contrasena' => Hash::make($request->contrasena),
            'tipo_rol_id' => $request->tipo_rol_id,
        ]);

        $user->load(['tipoDocumento', 'tipoRol']);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'usuario' => $user
        ], 201);
    }

    // 📌 Actualizar usuario
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $user->update([
            'nombre_usuario' => $request->nombre_usuario ?? $user->nombre_usuario,
            'nombres' => $request->nombres ?? $user->nombres,
            'apellidos' => $request->apellidos ?? $user->apellidos,
            'tipo_documento_id' => $request->tipo_documento_id ?? $user->tipo_documento_id,
            'numero_documento' => $request->numero_documento ?? $user->numero_documento,
            'celular' => $request->celular ?? $user->celular,
            'direccion' => $request->direccion ?? $user->direccion,
            'ciudad' => $request->ciudad ?? $user->ciudad,
            'email' => $request->email ?? $user->email,
            'contrasena' => $request->filled('contrasena') 
                ? Hash::make($request->contrasena) 
                : $user->contrasena,
            'tipo_rol_id' => $request->tipo_rol_id ?? $user->tipo_rol_id,
        ]);

        return response()->json($user, 200);
    }

    // 📌 Eliminar usuario
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado'], 200);
    }
}
