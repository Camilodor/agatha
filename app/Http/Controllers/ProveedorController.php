<?php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    // 📌 Listar todos los proveedores con datos del usuario
    public function index()
    {
        $proveedores = Proveedor::with('usuario:id,numero_documento,nombres,apellidos')->get();

        // Transformamos para incluir el nombre completo y documento
        $proveedores = $proveedores->map(function ($proveedor) {
            return [
                'id' => $proveedor->id,
                'nombre' => $proveedor->nombre,
                'descripcion' => $proveedor->descripcion,
                'usuario' => [
                    'id' => $proveedor->usuario->id ?? null,
                    'numero_documento' => $proveedor->usuario->numero_documento ?? null,
                    'nombres' => $proveedor->usuario->nombres ?? null,
                    'apellidos' => $proveedor->usuario->apellidos ?? null,
                    'nombre_completo' => trim(($proveedor->usuario->nombres ?? '') . ' ' . ($proveedor->usuario->apellidos ?? '')),
                ]
            ];
        });

        return response()->json($proveedores, 200);
    }

    // 📌 Mostrar un proveedor específico con datos del usuario
    public function show($id)
    {
        $proveedor = Proveedor::with('usuario:id,numero_documento,nombres,apellidos')
            ->findOrFail($id);

        $data = [
            'id' => $proveedor->id,
            'nombre' => $proveedor->nombre,
            'descripcion' => $proveedor->descripcion,
            'usuario' => [
                'id' => $proveedor->usuario->id ?? null,
                'numero_documento' => $proveedor->usuario->numero_documento ?? null,
                'nombres' => $proveedor->usuario->nombres ?? null,
                'apellidos' => $proveedor->usuario->apellidos ?? null,
                'nombre_completo' => trim(($proveedor->usuario->nombres ?? '') . ' ' . ($proveedor->usuario->apellidos ?? '')),
            ]
        ];

        return response()->json($data, 200);
    }

     public function store(Request $request)
    {
        $request->validate([
            'numero_documento' => 'required|exists:users,numero_documento',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $usuario = User::where('numero_documento', $request->numero_documento)->first();

        $proveedor = Proveedor::create([
            'usuarios_id' => $usuario->id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ])->load('usuario:id,numero_documento,nombres,apellidos');

      return response()->json([
    'message' => 'Proveedor creado exitosamente.',
    'proveedor' => $proveedor
], 201);
    }

    // 📌 Actualizar proveedor
    public function update(Request $request, $id)
    {
        $request->validate([
            'numero_documento' => 'required|exists:users,numero_documento',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $usuario = User::where('numero_documento', $request->numero_documento)->first();

        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update([
            'usuarios_id' => $usuario->id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        $proveedor->load('usuario:id,numero_documento,nombres,apellidos');

        return response()->json([
            'message' => 'Proveedor actualizado exitosamente.',
            'proveedor' => $proveedor
        ], 200);
    }

    // 📌 Eliminar proveedor
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return response()->json([
            'message' => 'Proveedor eliminado exitosamente.'
        ], 200);
    }
}