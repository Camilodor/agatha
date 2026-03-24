<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehiculoController extends Controller
{
    // 📌 Listar todos los vehículos con datos del conductor (usuario)
    public function index()
    {
        $vehiculos = Vehiculo::with('conductor:id,numero_documento,nombres,apellidos')->get();

        $vehiculos = $vehiculos->map(function ($vehiculo) {
            return [
                'id' => $vehiculo->id,
                'numero_placas' => $vehiculo->numero_placas,
                'nombre_marca_vehiculo' => $vehiculo->nombre_marca_vehiculo,
                'nombre_propietario_vehiculo' => $vehiculo->nombre_propietario_vehiculo,
                'documento_propietario_vehiculo' => $vehiculo->documento_propietario_vehiculo,
                'numero_celular_propietario' => $vehiculo->numero_celular_propietario,
                'direccion_propietario' => $vehiculo->direccion_propietario,
                'ciudad_propietario' => $vehiculo->ciudad_propietario,
                'numero_modelo_anio' => $vehiculo->numero_modelo_anio,
                'color_vehiculo' => $vehiculo->color_vehiculo,
                'fecha_vencimiento_soat' => $vehiculo->fecha_vencimiento_soat,
                'fecha_vencimiento_tecno' => $vehiculo->fecha_vencimiento_tecno,
                'nombre_satelital' => $vehiculo->nombre_satelital,
                'usuario_satelital' => $vehiculo->usuario_satelital,
                'contrasena_satelital' => $vehiculo->contrasena_satelital,
                'capacidad_carga' => $vehiculo->capacidad_carga,
                'usuario' => [
                    'id' => $vehiculo->conductor->id ?? null,
                    'numero_documento' => $vehiculo->conductor->numero_documento ?? null,
                    'nombres' => $vehiculo->conductor->nombres ?? null,
                    'apellidos' => $vehiculo->conductor->apellidos ?? null,
                    'nombre_completo' => trim(($vehiculo->conductor->nombres ?? '') . ' ' . ($vehiculo->conductor->apellidos ?? '')),
                ]
            ];
        });

        return response()->json($vehiculos, 200);
    }

    // 📌 Crear vehículo asociado a un usuario (por número de documento)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_documento' => 'required|exists:users,numero_documento',
            'numero_placas' => 'required|string|max:20',
            'nombre_marca_vehiculo' => 'required|string|max:50',
            'nombre_propietario_vehiculo' => 'required|string|max:100',
            'documento_propietario_vehiculo' => 'required|string|max:50',
            'numero_celular_propietario' => 'required|string|max:50',
            'direccion_propietario' => 'required|string|max:50',
            'ciudad_propietario' => 'required|string|max:50',
            'numero_modelo_anio' => 'required|string|max:10',
            'color_vehiculo' => 'required|string|max:30',
            'fecha_vencimiento_soat' => 'nullable|date',
            'fecha_vencimiento_tecno' => 'nullable|date',
            'nombre_satelital' => 'nullable|string|max:50',
            'usuario_satelital' => 'nullable|string|max:50',
            'contrasena_satelital' => 'nullable|string|max:50',
            'capacidad_carga' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario = User::where('numero_documento', $request->numero_documento)->first();

        $vehiculo = Vehiculo::create(array_merge(
            $request->except('numero_documento'),
            ['usuarios_id' => $usuario->id]
        ))->load('conductor:id,numero_documento,nombres,apellidos');

        return response()->json([
            'message' => 'Vehículo creado exitosamente',
            'vehiculo' => $vehiculo
        ], 201);
    }

    // 📌 Mostrar vehículo
    public function show($id)
    {
        $vehiculo = Vehiculo::with('conductor:id,numero_documento,nombres,apellidos')->find($id);
        if (!$vehiculo) {
            return response()->json(['message' => 'Vehículo no encontrado'], 404);
        }
        return response()->json($vehiculo, 200);
    }

    // 📌 Actualizar vehículo
    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::find($id);
        if (!$vehiculo) {
            return response()->json(['message' => 'Vehículo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_documento' => 'required|exists:users,numero_documento',
            'numero_placas' => 'required|string|max:20',
            'nombre_marca_vehiculo' => 'required|string|max:50',
            'nombre_propietario_vehiculo' => 'required|string|max:100',
            'documento_propietario_vehiculo' => 'required|string|max:50',
            'numero_celular_propietario' => 'required|string|max:50',
            'direccion_propietario' => 'required|string|max:50',
            'ciudad_propietario' => 'required|string|max:50',
            'numero_modelo_anio' => 'required|string|max:10',
            'color_vehiculo' => 'required|string|max:30',
            'fecha_vencimiento_soat' => 'nullable|date',
            'fecha_vencimiento_tecno' => 'nullable|date',
            'nombre_satelital' => 'nullable|string|max:50',
            'usuario_satelital' => 'nullable|string|max:50',
            'contrasena_satelital' => 'nullable|string|max:50',
            'capacidad_carga' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario = User::where('numero_documento', $request->numero_documento)->first();

        $vehiculo->update(array_merge(
            $request->except('numero_documento'),
            ['usuarios_id' => $usuario->id]
        ));

        $vehiculo->load('conductor:id,numero_documento,nombres,apellidos');

        return response()->json([
            'message' => 'Vehículo actualizado exitosamente',
            'vehiculo' => $vehiculo
        ], 200);
    }

    // 📌 Eliminar vehículo
    public function destroy($id)
    {
        $vehiculo = Vehiculo::find($id);
        if (!$vehiculo) {
            return response()->json(['message' => 'Vehículo no encontrado'], 404);
        }

        $vehiculo->delete();
        return response()->json(['message' => 'Vehículo eliminado correctamente'], 200);
    }
}
