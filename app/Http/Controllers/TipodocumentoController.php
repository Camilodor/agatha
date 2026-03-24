<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tipodocumento;
class TipodocumentoController extends Controller
{
    public function index()
    {
        $tiposdocumento = Tipodocumento::all();
        return response()->json($tiposdocumento, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $tipodocumento = Tipodocumento::create($request->all());

        return response()->json([
            'message' => 'Tipo de documento creado exitosamente',
            'tipodocumento' => $tipodocumento
        ], 201);
    }

    public function show($id)
    {
        $tipodocumento = Tipodocumento::find($id);

        if (!$tipodocumento) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }

        return response()->json($tipodocumento, 200);
    }

    public function update(Request $request, $id)
    {
        $tipodocumento = Tipodocumento::find($id);

        if (!$tipodocumento) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        $tipodocumento->update($request->all());

        return response()->json([
            'message' => 'Tipo de documento actualizado exitosamente',
            'tipodocumento' => $tipodocumento
        ], 200);
    }

    public function destroy($id)
    {
        $tipodocumento = Tipodocumento::find($id);

        if (!$tipodocumento) {
            return response()->json(['message' => 'Tipo de documento no encontrado'], 404);
        }

        $tipodocumento->delete();

        return response()->json([
            'message' => 'Tipo de documento eliminado exitosamente'
        ], 200);
    }
}
