<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
   public function index(Request $request)
{
    $query = Producto::with('proveedores');

    // 🔥 Filtro dinámico por proveedor
    if ($request->has('proveedor_id')) {
        $query->where('proveedores_id', $request->proveedor_id);
    }

    $productos = $query->get();

    return response()->json($productos, 200);
}

    public function store(Request $request)
    {
        $request->validate([
            'proveedores_id' => 'required|exists:proveedores,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $productos = Producto::create($request->all());
        $productos->load(['proveedores',  ]);

        return response()->json([
            'message' => 'Producto creado exitosamente.',
            'producto' => $productos
        ], 201);
    }

    public function show($id)
    {
        $productos = Producto::with(['proveedores', ])->findOrFail($id);
        return response()->json($productos, 200);
    }

    public function update(Request $request, $id)
    {
        $productos = Producto::findOrFail($id);
        $productos->update($request->all());
        $productos->load(['proveedores',  ]);

        return response()->json([
            'message' => 'Producto actualizado exitosamente.',
            'producto' => $productos
        ], 200);
    }

    public function destroy($id)
    {
        $productos = Producto::findOrFail($id);
        $productos->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente.'
        ], 200);
    }
}

