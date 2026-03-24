<?php

namespace App\Http\Controllers;

use App\Models\Referencia_laboral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Referencia_laboralController extends Controller
{
    public function index()
    {
       $referencias_laborales = Referencia_laboral::with(['usuarios', 'tipos_documento'])->get();
        return response()->json($referencias_laborales, 200);
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

        $referencias_laborales = Referencia_laboral::create($request->all());

          $referencias_laborales->load(['usuarios', 'tipos_documento']);

        return response()->json([
            'message' => 'Referencia laboral creada exitosamente.',
            'referencias_laborales' => $referencias_laborales
        ], 201);
    }

    public function show($id)
    {
        $referencias_laborales = Referencia_laboral::with(['usuarios', 'tipos_documento'])->find($id);
        return response()->json($referencias_laborales, 200);
    }

    
    public function update(Request $request, $id)
    {
        $referencias_laborales = Referencia_laboral::find($id);
        
        if (!$referencias_laborales) {
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
        $referencias_laborales->update($request->all());

        $referencias_laborales->load(['usuarios', 'tipos_documento']);

        return response()->json([
            'message' => 'Referencia personal actualizada exitosamente.',
            'r_personal' => $referencias_laborales
        ], 200);
    }

    
    public function destroy($id)
    {
        $referencias_laborales = Referencia_laboral::find($id);
        $referencias_laborales->delete();

        return response()->json([
            'message' => 'Referencia personal eliminada exitosamente.'
        ], 200);
    }
}

