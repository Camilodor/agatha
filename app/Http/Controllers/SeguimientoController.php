<?php

namespace App\Http\Controllers;

use App\Models\Seguimiento;
use App\Models\Mercancia;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class SeguimientoController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // 📋 LISTAR SEGUIMIENTOS
    //
    // - Si el usuario es CLIENTE (tiene proveedor asociado):
    //   solo ve los seguimientos de SUS mercancías
    // - Si es ADMIN u otro rol: ve todos
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $usuario = auth()->user();

        // Buscar si el usuario autenticado es un proveedor (rol cliente)
        $proveedor = Proveedor::where('usuarios_id', $usuario->id)->first();

        $query = Seguimiento::with([
            'mercancia.proveedores',
            'mercancia.despachos.vehiculo.conductor',
            'mercancia.despachos.usuario',
        ]);

        // Si tiene proveedor asociado → filtrar solo sus mercancías
        if ($proveedor) {
            $query->whereHas('mercancia', function ($q) use ($proveedor) {
                $q->where('proveedores_id', $proveedor->id);
            });
        }

        $seguimientos = $query->orderBy('updated_at', 'desc')->get();

        return response()->json(
            $seguimientos->map(fn($s) => $this->formatSeguimiento($s)),
            200
        );
    }

    // ─────────────────────────────────────────────────────────────
    // 🔍 VER SEGUIMIENTO DE UNA MERCANCÍA POR NÚMERO DE REMESA
    //
    // GET /api/seguimientos/remesa/{numero_remesa}
    // ─────────────────────────────────────────────────────────────
    public function porRemesa($numero_remesa)
    {
        $usuario   = auth()->user();
        $proveedor = Proveedor::where('usuarios_id', $usuario->id)->first();

        $mercancia = Mercancia::where('numero_remesa', $numero_remesa)->first();

        if (!$mercancia) {
            return response()->json(['message' => 'Mercancía no encontrada'], 404);
        }

        // Si es cliente, verificar que la mercancía le pertenece
        if ($proveedor && $mercancia->proveedores_id !== $proveedor->id) {
            return response()->json(['message' => 'No autorizado para ver este seguimiento'], 403);
        }

        $seguimiento = Seguimiento::with([
            'mercancia.proveedores',
            'mercancia.despachos.vehiculo.conductor',
            'mercancia.despachos.usuario',
        ])->where('mercancias_id', $mercancia->id)->first();

        if (!$seguimiento) {
            return response()->json(['message' => 'Seguimiento no encontrado'], 404);
        }

        return response()->json($this->formatSeguimiento($seguimiento), 200);
    }

    // ─────────────────────────────────────────────────────────────
    // 🔍 VER UN SEGUIMIENTO POR ID
    // ─────────────────────────────────────────────────────────────
    public function show($id)
    {
        $usuario   = auth()->user();
        $proveedor = Proveedor::where('usuarios_id', $usuario->id)->first();

        $seguimiento = Seguimiento::with([
            'mercancia.proveedores',
            'mercancia.despachos.vehiculo.conductor',
            'mercancia.despachos.usuario',
        ])->find($id);

        if (!$seguimiento) {
            return response()->json(['message' => 'Seguimiento no encontrado'], 404);
        }

        // Si es cliente, verificar que la mercancía le pertenece
        if ($proveedor && $seguimiento->mercancia?->proveedores_id !== $proveedor->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($this->formatSeguimiento($seguimiento), 200);
    }

    // ─────────────────────────────────────────────────────────────
    // ✏️ ACTUALIZAR ESTADO MANUALMENTE (solo admin)
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $seguimiento = Seguimiento::find($id);

        if (!$seguimiento) {
            return response()->json(['message' => 'Seguimiento no encontrado'], 404);
        }

        $validated = $request->validate([
            'estado'       => 'required|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        // ✅ update() → modifica el registro existente, no crea uno nuevo
        $seguimiento->update($validated);

        return response()->json([
            'message'      => 'Seguimiento actualizado correctamente',
            'seguimiento'  => $this->formatSeguimiento(
                $seguimiento->load([
                    'mercancia.proveedores',
                    'mercancia.despachos.vehiculo.conductor',
                    'mercancia.despachos.usuario',
                ])
            )
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────
    // 🗑️ ELIMINAR (solo admin, caso excepcional)
    // ─────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $seguimiento = Seguimiento::find($id);

        if (!$seguimiento) {
            return response()->json(['message' => 'Seguimiento no encontrado'], 404);
        }

        $seguimiento->delete();

        return response()->json(['message' => 'Seguimiento eliminado correctamente'], 200);
    }

    // ─────────────────────────────────────────────────────────────
    // 🧱 FORMATO ENRIQUECIDO DEL SEGUIMIENTO
    // ─────────────────────────────────────────────────────────────
    private function formatSeguimiento(Seguimiento $s): array
    {
        $mercancia = $s->mercancia;

        // Tomamos el despacho más reciente asociado a esta mercancía
        $despacho  = $mercancia?->despachos?->sortByDesc('created_at')->first();
        $vehiculo  = $despacho?->vehiculo;
        $conductor = $vehiculo?->conductor;  // relación en Vehiculo → belongsTo(User)
        $registradoPor = $despacho?->usuario;

        return [
            // ── SEGUIMIENTO ──────────────────────────────────────
            'id'            => $s->id,
            'estado_actual' => $s->estado,
            'observaciones' => $s->observaciones,
            'ultima_actualizacion' => $s->updated_at?->format('Y-m-d H:i:s'),
            'fecha_ingreso_bodega' => $s->created_at?->format('Y-m-d H:i:s'),

            // ── MERCANCÍA ────────────────────────────────────────
            'mercancia' => $mercancia ? [
                'id'             => $mercancia->id,
                'numero_remesa'  => $mercancia->numero_remesa,
                'fecha_ingreso'  => $mercancia->fecha_ingreso,
                'origen'         => $mercancia->origen_mercancia,
                'destino'        => $mercancia->destino_mercancia,
                'peso'           => $mercancia->peso,
                'unidades'       => $mercancia->unidades,
                'valor_declarado'=> $mercancia->valor_declarado,
                'valor_flete'    => $mercancia->valor_flete,

                // Remitente
                'remitente' => [
                    'nombre'    => $mercancia->nombre_remitente,
                    'documento' => $mercancia->documento_remitente,
                    'celular'   => $mercancia->celular_remitente,
                ],

                // Destinatario
                'destinatario' => [
                    'nombre'    => $mercancia->nombre_destinatario,
                    'documento' => $mercancia->documento_destinatario,
                    'celular'   => $mercancia->celular_destinatario,
                    'direccion' => $mercancia->direccion_destinatario,
                ],

                // Proveedor (cliente dueño de la mercancía)
                'proveedor' => $mercancia->proveedores ? [
                    'id'     => $mercancia->proveedores->id,
                    'nombre' => $mercancia->proveedores->nombre,
                ] : null,
            ] : null,

            // ── DESPACHO (si ya salió) ───────────────────────────
            'despacho' => $despacho ? [
                'numero_planilla' => $despacho->id,
                'fecha_despacho'  => $despacho->fecha_despacho,
                'fecha_salida'    => $despacho->created_at?->format('Y-m-d H:i:s'),

                // Quién registró el despacho (operador en bodega)
                'registrado_por' => $registradoPor ? [
                    'nombre_completo' => trim(($registradoPor->nombres ?? '') . ' ' . ($registradoPor->apellidos ?? '')),
                ] : null,

                // Vehículo que lleva la mercancía
                'vehiculo' => $vehiculo ? [
                    'numero_placas' => $vehiculo->numero_placas,
                    'marca'         => $vehiculo->nombre_marca_vehiculo,
                    'modelo'        => $vehiculo->numero_modelo_anio,
                    'color'         => $vehiculo->color_vehiculo,
                ] : null,

                // Conductor asignado al vehículo
                'conductor' => $conductor ? [
                    'nombre_completo'  => trim(($conductor->nombres ?? '') . ' ' . ($conductor->apellidos ?? '')),
                    'numero_documento' => $conductor->numero_documento,
                    'celular'          => $conductor->celular,
                    'foto_url'         => $conductor->foto_url,   // 📸 foto de perfil del conductor
                ] : null,

                // 📸 Foto del vehículo
                'foto_vehiculo' => $vehiculo?->foto_url,
            ] : null,

            // ── FOTO DE EVIDENCIA (entrega o devolución) ─────────
            'evidencia' => $this->resolverEvidencia($mercancia),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Busca si la mercancía tiene entrega o devolución con foto
    // ─────────────────────────────────────────────────────────────
    private function resolverEvidencia($mercancia): ?array
    {
        if (!$mercancia) return null;

        // Verificar entrega
        $entrega = $mercancia->entregas()->latest()->first();
        if ($entrega) {
            return [
                'tipo'      => 'entrega',
                'fecha'     => $entrega->fecha_entrega,
                'estado'    => $entrega->estado_entrega,
                'recibe'    => $entrega->nombre_recibe,
                'celular'   => $entrega->numero_celular_recibe,
                'foto_url'  => $entrega->foto_url,   // 📸 foto de la entrega
            ];
        }

        // Verificar devolución
        $devolucion = $mercancia->devoluciones()->latest()->first();
        if ($devolucion) {
            return [
                'tipo'     => 'devolucion',
                'fecha'    => $devolucion->fecha_devolucion,
                'estado'   => $devolucion->estado_devolucion,
                'motivo'   => $devolucion->motivo_devolucion,
                'foto_url' => $devolucion->foto_url,  // 📸 foto de la devolución
            ];
        }

        return null;
    }
}