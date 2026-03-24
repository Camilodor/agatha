<?php

namespace App\Http\Controllers;

use App\Models\Despacho;
use App\Models\Mercancia;
use App\Models\Vehiculo;
use App\Models\Seguimiento;
use App\Http\Controllers\QrDespachoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DespachoController extends Controller
{
    // ─────────────────────────────────────────────
    // 📋 LISTAR TODOS LOS DESPACHOS (Planillas)
    // ─────────────────────────────────────────────
    public function index()
    {
        $despachos = Despacho::with([
            'mercancias.proveedores',
            'mercancias.productos',
            'mercancias.tipopago',
            'vehiculo',
            'usuario',
            'tipopago',
        ])->get();

        return response()->json(
            $despachos->map(fn($d) => $this->formatPlanilla($d)),
            200
        );
    }

    // ─────────────────────────────────────────────
    // ➕ CREAR DESPACHO (Nueva Planilla)
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Vehículo identificado por placas
            'numero_placas'      => 'required|exists:vehiculos,numero_placas',

            // Tipo de pago del DESPACHO (lo que le pagan al conductor)
            'tipo_pago_id'       => 'required|exists:tipospago,id',

            'fecha_despacho'     => 'required|date',
            'negociacion'        => 'required|numeric|min:0',
            'anticipo'           => 'required|numeric|min:0',
            'observaciones_mer'  => 'nullable|string',

            // Mercancías: array de número de remesa
            'remesas'            => 'required|array|min:1',
            'remesas.*'          => 'required|string|exists:mercancias,numero_remesa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ✅ Validar que anticipo no sea mayor que la negociación
        if ($request->anticipo > $request->negociacion) {
            return response()->json([
                'message' => 'El anticipo no puede ser mayor que la negociación'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::where('numero_placas', $request->numero_placas)->first();

            // 🔢 Saldo calculado automáticamente
            $saldo = $request->negociacion - $request->anticipo;

            $despacho = Despacho::create([
                'vehiculos_id'    => $vehiculo->id,
                'usuarios_id'     => auth()->id(),
                'tipo_pago_id'    => $request->tipo_pago_id,
                'fecha_despacho'  => $request->fecha_despacho,
                'negociacion'     => $request->negociacion,
                'anticipo'        => $request->anticipo,
                'saldo'           => $saldo,
                'observaciones_mer' => $request->observaciones_mer,
            ]);

            // 🔗 Asociar las mercancías por número de remesa
            $mercanciasIds = Mercancia::whereIn('numero_remesa', $request->remesas)
                ->pluck('id');

            $despacho->mercancias()->attach($mercanciasIds);

            // 📍 Actualizar (NO crear nuevo) el seguimiento de cada mercancía
            // updateOrCreate busca por mercancias_id y actualiza el estado existente
            foreach ($despacho->mercancias as $mercancia) {
                Seguimiento::updateOrCreate(
                    ['mercancias_id' => $mercancia->id],
                    [
                        'estado'       => 'En camino al destino',
                        'observaciones' => $despacho->observaciones_mer,
                    ]
                );
            }

            $despacho->load([
                'mercancias.proveedores',
                'mercancias.productos',
                'mercancias.tipopago',
                'vehiculo',
                'usuario',
                'tipopago',
            ]);

            // 📸 Generar QR PNG de la planilla automáticamente
            QrDespachoController::generarYGuardar($despacho);

            DB::commit();

            return response()->json([
                'message'  => 'Planilla de despacho creada exitosamente',
                'planilla' => $this->formatPlanilla($despacho)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el despacho',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // 🔍 VER UNA PLANILLA POR ID
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $despacho = Despacho::with([
            'mercancias.proveedores',
            'mercancias.productos',
            'mercancias.tipopago',
            'vehiculo',
            'usuario',
            'tipopago',
        ])->find($id);

        if (!$despacho) {
            return response()->json(['message' => 'Despacho no encontrado'], 404);
        }

        return response()->json($this->formatPlanilla($despacho), 200);
    }

    // ─────────────────────────────────────────────
    // ✏️ ACTUALIZAR DESPACHO
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $despacho = Despacho::find($id);

        if (!$despacho) {
            return response()->json(['message' => 'Despacho no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_placas'      => 'required|exists:vehiculos,numero_placas',
            'tipo_pago_id'       => 'required|exists:tipospago,id',
            'fecha_despacho'     => 'required|date',
            'negociacion'        => 'required|numeric|min:0',
            'anticipo'           => 'required|numeric|min:0',
            'observaciones_mer'  => 'nullable|string',
            'remesas'            => 'required|array|min:1',
            'remesas.*'          => 'required|string|exists:mercancias,numero_remesa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($request->anticipo > $request->negociacion) {
            return response()->json([
                'message' => 'El anticipo no puede ser mayor que la negociación'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $vehiculo = Vehiculo::where('numero_placas', $request->numero_placas)->first();
            $saldo    = $request->negociacion - $request->anticipo;

            $despacho->update([
                'vehiculos_id'    => $vehiculo->id,
                'tipo_pago_id'    => $request->tipo_pago_id,
                'fecha_despacho'  => $request->fecha_despacho,
                'negociacion'     => $request->negociacion,
                'anticipo'        => $request->anticipo,
                'saldo'           => $saldo,
                'observaciones_mer' => $request->observaciones_mer,
            ]);

            // 🔄 Sincronizar mercancías (elimina las anteriores y pone las nuevas)
            $mercanciasIds = Mercancia::whereIn('numero_remesa', $request->remesas)
                ->pluck('id');

            $despacho->mercancias()->sync($mercanciasIds);

            $despacho->load([
                'mercancias.proveedores',
                'mercancias.productos',
                'mercancias.tipopago',
                'vehiculo',
                'usuario',
                'tipopago',
            ]);

            // 📸 Regenerar QR con los datos actualizados
            QrDespachoController::generarYGuardar($despacho);

            DB::commit();

            return response()->json([
                'message'  => 'Planilla de despacho actualizada exitosamente',
                'planilla' => $this->formatPlanilla($despacho)
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el despacho',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // 🗑️ ELIMINAR DESPACHO
    // ─────────────────────────────────────────────
    public function destroy($id)
    {
        $despacho = Despacho::find($id);

        if (!$despacho) {
            return response()->json(['message' => 'Despacho no encontrado'], 404);
        }

        // El cascade en la migración borra los registros del pivot automáticamente
        $despacho->delete();

        return response()->json([
            'message' => 'Planilla de despacho eliminada exitosamente'
        ], 200);
    }

    // ─────────────────────────────────────────────
    // 🧱 FORMATO DE PLANILLA (estructura para el frontend)
    // ─────────────────────────────────────────────
    private function formatPlanilla(Despacho $despacho): array
    {
        // Conductor: usuario asignado al vehículo
        $conductor = $despacho->vehiculo?->conductor;

        // ── QR base64 PNG para el PDF (evita CORS en el frontend) ──
        $qrBase64 = null;
        $qrRuta   = "qr/despachos/planilla_{$despacho->id}.svg";
        if (\Storage::disk('public')->exists($qrRuta)) {
            $svgContent = \Storage::disk('public')->get($qrRuta);
            // Convertir SVG a PNG usando Imagick si está disponible, o devolver SVG base64
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->readImageBlob($svgContent);
                    $imagick->setImageFormat('png');
                    $imagick->resizeImage(300, 300, \Imagick::FILTER_LANCZOS, 1);
                    $pngData  = $imagick->getImageBlob();
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($pngData);
                } catch (\Exception $e) {
                    // Fallback: devolver SVG base64 (el frontend lo maneja)
                    $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                }
            } else {
                // Sin Imagick: devolver SVG base64
                $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
            }
        }

        return [
            // ── ENCABEZADO DE PLANILLA ──────────────────
            'numero_planilla'   => $despacho->id,
            'fecha_despacho'    => $despacho->fecha_despacho,
            'observaciones'     => $despacho->observaciones_mer,
            'qr_url'            => $despacho->qr_url,
            'qr_base64'         => $qrBase64,  // ✅ base64 listo para pdfmake sin CORS

            // ── VEHÍCULO ────────────────────────────────
            'vehiculo' => [
                'id'             => $despacho->vehiculo?->id,
                'numero_placas'  => $despacho->vehiculo?->numero_placas,
                'marca'          => $despacho->vehiculo?->nombre_marca_vehiculo,
                'modelo'         => $despacho->vehiculo?->numero_modelo_anio,
                'color'          => $despacho->vehiculo?->color_vehiculo,
                'capacidad_carga'=> $despacho->vehiculo?->capacidad_carga,
            ],

            // ── CONDUCTOR (usuario asignado al vehículo) ─
            'conductor' => [
                'id'               => $conductor?->id,
                'nombre_completo'  => trim(($conductor?->nombres ?? '') . ' ' . ($conductor?->apellidos ?? '')),
                'numero_documento' => $conductor?->numero_documento,
                'celular'          => $conductor?->celular,
            ],

            // ── USUARIO QUE REGISTRÓ LA PLANILLA ────────
            'registrado_por' => [
                'id'              => $despacho->usuario?->id,
                'nombre_completo' => trim(($despacho->usuario?->nombres ?? '') . ' ' . ($despacho->usuario?->apellidos ?? '')),
            ],

            // ── PAGO AL CONDUCTOR ────────────────────────
            'pago' => [
                'tipo_pago'   => $despacho->tipopago?->nombre ?? null,
                'negociacion' => $despacho->negociacion,
                'anticipo'    => $despacho->anticipo,
                'saldo'       => $despacho->saldo,  // calculado automáticamente
            ],

            // ── MERCANCÍAS EN ESTE DESPACHO ───────────────
            'mercancias' => $despacho->mercancias->map(function ($m) {
                return [
                    'id'                  => $m->id,
                    'numero_remesa'       => $m->numero_remesa,
                    'fecha_ingreso'       => $m->fecha_ingreso,
                    'origen'              => $m->origen_mercancia,
                    'destino'             => $m->destino_mercancia,
                    'peso'                => $m->peso,
                    'unidades'            => $m->unidades,
                    'valor_declarado'     => $m->valor_declarado,
                    'valor_flete'         => $m->valor_flete,
                    'valor_total'         => $m->valor_total,
                    'tipo_pago_mercancia' => $m->tipopago?->nombre ?? null,
                    'observaciones'       => $m->observaciones,

                    // Remitente
                    'remitente' => [
                        'nombre'    => $m->nombre_remitente,
                        'documento' => $m->documento_remitente,
                        'direccion' => $m->direccion_remitente,
                        'celular'   => $m->celular_remitente,
                    ],

                    // Destinatario
                    'destinatario' => [
                        'nombre'    => $m->nombre_destinatario,
                        'documento' => $m->documento_destinatario,
                        'direccion' => $m->direccion_destinatario,
                        'celular'   => $m->celular_destinatario,
                    ],

                    // Proveedor de esa mercancía
                    'proveedor' => [
                        'id'     => $m->proveedores?->id,
                        'nombre' => $m->proveedores?->nombre,
                    ],

                    // Productos dentro de esa mercancía
                    'productos' => $m->productos->map(fn($p) => [
                        'id'       => $p->id,
                        'nombre'   => $p->nombre,
                        'cantidad' => $p->pivot->cantidad,
                    ]),
                ];
            }),

            // ── TOTALES CONSOLIDADOS DEL DESPACHO ────────
            'totales' => [
                'total_mercancias'    => $despacho->mercancias->count(),
                'total_unidades'      => $despacho->mercancias->sum('unidades'),
                'total_peso'          => $despacho->mercancias->sum('peso'),
                'total_valor_flete'   => $despacho->mercancias->sum('valor_flete'),
                'total_valor_declarado' => $despacho->mercancias->sum('valor_declarado'),
            ],
        ];
    }
}