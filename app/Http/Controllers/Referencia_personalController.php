<?php

namespace App\Http\Controllers;

use App\Models\Referencia_personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Referencia_personalController extends Controller
{
    public function index()
    {
         $referencias_personales = Referencia_personal::with(['usuarios', 'tipos_documento'])->get();
        return response()->json($referencias_personales, 200);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'usuarios_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'parentezco' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:20',
            'tipo_documento_id' => 'required|exists:tiposdocumento,id',
            'numero_celular' => 'required|string|max:20',
            'numero_direccion' => 'required|string|max:255',
        ]);

        $referencias_personales = Referencia_personal::create($request->all());

        $referencias_personales->load(['usuarios', 'tipos_documento']);

        return response()->json([
            'message' => 'Referencia personal creada exitosamente.',
            'r_personal' => $referencias_personales
        ], 201);
    }

    public function show($id)
    {
        $referencias_personales = Referencia_personal::with(['usuarios', 'tipos_documento'])->findOrFail($id);
        return response()->json($referencias_personales, 200);
    }

    
    public function update(Request $request, $id)
    {
        $referencias_personales = Referencia_personal::findOrFail($id);
        
        if (!$referencias_personales) {
            return response()->json(['message' => 'Referencia no encontrada'], 404);
        }

        $request->validate([
            'usuarios_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'parentezco' => 'required|string|max:255',
            'numero_documento' => 'required|string|max:20',
            'tipo_documento_id' => 'required|exists:tiposdocumento,id',
            'numero_celular' => 'required|string|max:20',
            'numero_direccion' => 'required|string|max:255',
        ]);
        $referencias_personales->update($request->all());

        $referencias_personales->load(['usuarios', 'tipos_documento']);

        return response()->json([
            'message' => 'Referencia personal actualizada exitosamente.',
            'r_personal' => $referencias_personales
        ], 200);
    }

    
    public function destroy($id)
    {
        $referencias_personales = Referencia_personal::findOrFail($id);
        $referencias_personales->delete();

        return response()->json([
            'message' => 'Referencia personal eliminada exitosamente.'
        ], 200);
    }
}

