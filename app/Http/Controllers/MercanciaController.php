<?php

namespace App\Http\Controllers;

use App\Models\Mercancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MercanciaController extends Controller
{
    /**
     * Listar todas las mercancías
     */
    public function index()
    {
        $mercancias = Mercancia::with([
            'proveedores',
            'tipopago',
            'usuarios',
            'despachos',
            'entregas',
            'devoluciones',
            'seguimientos'
        ])->get();

        return response()->json($mercancias, 200);
    }

    /**
     * Crear una nueva mercancía
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'proveedores_id' => 'required|exists:proveedores,id',
                'fecha_ingreso' => 'required|date',
                'numero_remesa' => 'required|string|max:50',
                'origen_mercancia' => 'required|string|max:100',
                'destino_mercancia' => 'required|string|max:100',
                'nombre_remitente' => 'required|string|max:100',
                'documento_remitente'=> 'required|string|max:50',
                'direccion_remitente'=> 'required|string|max:150',
                'celular_remitente' => 'required|string|max:20',
                'nombre_destinatario'=> 'required|string|max:100',
                'documento_destinatario'=> 'required|string|max:50',
                'direccion_destinatario'=> 'required|string|max:150',
                'celular_destinatario'=> 'required|string|max:20',
                'valor_declarado' => 'required|numeric',
                'valor_flete' => 'required|numeric',
                'valor_total' => 'required|numeric',
                'peso' => 'required|numeric',
                'observaciones' => 'nullable|string',
                'tipo_pago_id' => 'required|exists:tipospago,id',

                // 🔥 Productos
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Usuario autenticado
            $data['usuarios_id'] = Auth::id();

            // Calcular unidades automáticamente
            $data['unidades'] = collect($request->productos)->sum('cantidad');

            $mercancia = Mercancia::create($data);

            // Guardar productos asociados
            $productosData = [];

            foreach ($request->productos as $producto) {
                $productosData[$producto['producto_id']] = [
                    'cantidad' => $producto['cantidad']
                ];
            }

            $mercancia->productos()->attach($productosData);

            $mercancia->load([
                'proveedores',
                'tipopago',
                'usuarios',
                'productos'
            ]);

            return response()->json([
                'message' => 'Mercancía ingresada exitosamente',
                'mercancia' => $mercancia
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al crear la mercancía',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una mercancía específica
     */
    public function show($id)
    {
        $mercancia = Mercancia::with([
            'proveedores',
            'tipopago',
            'usuarios',
            'despachos',
            'entregas',
            'devoluciones',
            'seguimientos'
        ])->find($id);

        if (!$mercancia) {
            return response()->json(['message' => 'Mercancía no encontrada'], 404);
        }

        return response()->json($mercancia, 200);
    }

    /**
     * Actualizar una mercancía
     */
    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'proveedores_id' => 'required|exists:proveedores,id',
                'fecha_ingreso' => 'required|date',
                'numero_remesa' => 'required|string|max:50',
                'origen_mercancia' => 'required|string|max:100',
                'destino_mercancia' => 'required|string|max:100',
                'nombre_remitente' => 'required|string|max:100',
                'documento_remitente'=> 'required|string|max:50',
                'direccion_remitente'=> 'required|string|max:150',
                'celular_remitente' => 'required|string|max:20',
                'nombre_destinatario'=> 'required|string|max:100',
                'documento_destinatario'=> 'required|string|max:50',
                'direccion_destinatario'=> 'required|string|max:150',
                'celular_destinatario'=> 'required|string|max:20',
                'valor_declarado' => 'required|numeric',
                'valor_flete' => 'required|numeric',
                'valor_total' => 'required|numeric',
                'peso' => 'required|numeric',
                'unidades' => 'required|integer',
                'observaciones' => 'nullable|string',
                'tipo_pago_id' => 'required|exists:tipospago,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $mercancia = Mercancia::find($id);

            if (!$mercancia) {
                return response()->json(['message' => 'Mercancía no encontrada'], 404);
            }

            $data = $request->all();

            // Mantener el usuario original
            $data['usuarios_id'] = $mercancia->usuarios_id;

            $mercancia->update($data);

            $mercancia->load([
                'proveedores',
                'tipopago',
                'usuarios',
                'despachos',
                'entregas',
                'devoluciones',
                'seguimientos'
            ]);

            return response()->json([
                'message' => 'Mercancía actualizada exitosamente',
                'mercancia' => $mercancia
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al actualizar la mercancía',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una mercancía
     */
    public function destroy($id)
    {
        $mercancia = Mercancia::find($id);

        if (!$mercancia) {
            return response()->json(['message' => 'Mercancía no encontrada'], 404);
        }

        $mercancia->delete();

        return response()->json([
            'message' => 'Mercancía eliminada exitosamente'
        ], 200);
    }
}