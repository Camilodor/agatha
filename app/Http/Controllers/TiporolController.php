<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tiporol;
class TiporolController extends Controller
{
    public function index()
    {
        $tiposrol = Tiporol::all();
        return response()->json($tiposrol, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $tiporol = Tiporol::create($request->all());

        return response()->json([
            'message' => 'Tipo de rol creado exitosamente',
            'tiporol' => $tiporol
        ], 201);
    }

    public function show($id)
    {
        $tiporol = Tiporol::find($id);

        if (!$tiporol) {
            return response()->json(['message' => 'Tipo de rol no encontrado'], 404);
        }

        return response()->json($tiporol, 200);
    }

    public function update(Request $request, $id)
    {
        $tiporol = Tiporol::find($id);

        if (!$tiporol) {
            return response()->json(['message' => 'Tipo de rol no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $tiporol->update($request->all());

        return response()->json([
            'message' => 'Tipo de rol actualizado exitosamente',
            'tiporol' => $tiporol
        ], 200);
    }

    public function destroy($id)
    {
        $tiporol = Tiporol::find($id);

        if (!$tiporol) {
            return response()->json(['message' => 'Tipo de rol no encontrado'], 404);
        }

        $tiporol->delete();

        return response()->json([
            'message' => 'Tipo de rol eliminado exitosamente'
        ], 200);
    }
}
