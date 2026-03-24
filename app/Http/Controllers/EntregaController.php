<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Mercancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntregaController extends Controller
{
    // 📌 Listar todas las entregas
    public function index()
    {
        $entregas = Entrega::with(['mercancias', 'usuarios', 'despachos'])->get();
        return response()->json($entregas, 200);
    }

    // 📌 Crear nueva entrega
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_remesa' => 'required|exists:mercancias,numero_remesa',
            'despachos_id' => 'required|exists:despachos,id',
            'nombre_recibe' => 'required|string',
            'numero_celular_recibe' => 'required|string',
            'fecha_entrega' => 'required|date',
            'estado_entrega' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 🔹 Buscar mercancia por numero_remesa
        $mercancia = Mercancia::where('numero_remesa', $request->numero_remesa)->first();

        if (!$mercancia) {
            return response()->json(['message' => 'Mercancía no encontrada'], 404);
        }

        // 🔹 Obtener usuario del token
        $usuario = $request->user();

        // Crear entrega
        $entrega = Entrega::create([
            'mercancias_id' => $mercancia->id,
            'despachos_id' => $request->despachos_id,
            'usuarios_id' => $usuario->id,
            'nombre_recibe' => $request->nombre_recibe,
            'numero_celular_recibe' => $request->numero_celular_recibe,
            'fecha_entrega' => $request->fecha_entrega,
            'estado_entrega' => $request->estado_entrega,
            'observaciones' => $request->observaciones,
        ]);

        $entrega->load(['mercancias', 'usuarios', 'despachos']);

        return response()->json([
            'message' => 'Entrega registrada exitosamente',
            'entrega' => $entrega
        ], 201);
    }

    // 📌 Mostrar entrega por ID
    public function show($id)
    {
        $entrega = Entrega::with(['mercancias', 'usuarios', 'despachos'])->find($id);

        if (!$entrega) {
            return response()->json(['message' => 'Entrega no encontrada'], 404);
        }

        return response()->json($entrega, 200);
    }

    // 📌 Actualizar entrega
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'numero_remesa' => 'required|exists:mercancias,numero_remesa',
            'despachos_id' => 'required|exists:despachos,id',
            'nombre_recibe' => 'required|string',
            'numero_celular_recibe' => 'required|string',
            'fecha_entrega' => 'required|date',
            'estado_entrega' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $entrega = Entrega::find($id);

        if (!$entrega) {
            return response()->json(['message' => 'Entrega no encontrada'], 404);
        }

        // 🔹 Buscar mercancia por numero_remesa
        $mercancia = Mercancia::where('numero_remesa', $request->numero_remesa)->first();

        if (!$mercancia) {
            return response()->json(['message' => 'Mercancía no encontrada'], 404);
        }

        // 🔹 Usuario del token
        $usuario = $request->user();

        $entrega->update([
            'mercancias_id' => $mercancia->id,
            'despachos_id' => $request->despachos_id,
            'usuarios_id' => $usuario->id,
            'nombre_recibe' => $request->nombre_recibe,
            'numero_celular_recibe' => $request->numero_celular_recibe,
            'fecha_entrega' => $request->fecha_entrega,
            'estado_entrega' => $request->estado_entrega,
            'observaciones' => $request->observaciones,
        ]);

        $entrega->load(['mercancias', 'usuarios', 'despachos']);

        return response()->json([
            'message' => 'Entrega actualizada exitosamente',
            'entrega' => $entrega
        ], 200);
    }

    // 📌 Eliminar entrega
    public function destroy($id)
    {
        $entrega = Entrega::find($id);

        if (!$entrega) {
            return response()->json(['message' => 'Entrega no encontrada'], 404);
        }

        $entrega->delete();

        return response()->json([
            'message' => 'Entrega eliminada exitosamente'
        ], 200);
    }
}