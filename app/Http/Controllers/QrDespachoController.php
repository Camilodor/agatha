<?php

namespace App\Http\Controllers;

use App\Models\Despacho;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrDespachoController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // 🔧 MÉTODO INTERNO: genera el JSON que va dentro del QR
    //
    // Contiene solo los datos clave que necesita el conductor
    // para realizar la entrega
    // ─────────────────────────────────────────────────────────────
    public static function buildQrData(Despacho $despacho): array
    {
        // Cargar relaciones si no están cargadas
        $despacho->loadMissing(['mercancias.tipopago', 'vehiculo', 'usuario']);

        return [
            'numero_planilla' => $despacho->id,
            'fecha_despacho'  => $despacho->fecha_despacho,

            // Vehículo
            'vehiculo' => [
                'placas' => $despacho->vehiculo?->numero_placas,
                'marca'  => $despacho->vehiculo?->nombre_marca_vehiculo,
            ],

            // Mercancías — solo datos de entrega
            'mercancias' => $despacho->mercancias->map(fn($m) => [
                'numero_remesa' => $m->numero_remesa,
                'destino'       => $m->destino_mercancia,
                'destinatario'  => [
                    'nombre'    => $m->nombre_destinatario,
                    'celular'   => $m->celular_destinatario,
                    'direccion' => $m->direccion_destinatario,
                ],
                'peso'          => $m->peso,
                'unidades'      => $m->unidades,
            ])->values()->toArray(),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // 🔧 MÉTODO INTERNO: genera el PNG del QR y lo guarda en storage
    //
    // Devuelve la URL pública del PNG guardado
    // ─────────────────────────────────────────────────────────────
    public static function generarYGuardar(Despacho $despacho): string
    {
        $contenido = json_encode(self::buildQrData($despacho));

        // Generar SVG con BaconQrCode (no requiere extensión GD)
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer   = new Writer($renderer);
        $svgData  = $writer->writeString($contenido);

        // Guardar en storage/app/public/qr/despachos/planilla_{id}.svg
        $carpeta  = 'qr/despachos';
        $nombre   = "planilla_{$despacho->id}.svg";
        $ruta     = "{$carpeta}/{$nombre}";

        Storage::disk('public')->put($ruta, $svgData);

        $url = asset("storage/{$ruta}");

        // Persistir la URL en la tabla despachos
        $despacho->update(['qr_url' => $url]);

        return $url;
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/despachos/{id}/qr
    //
    // Devuelve la imagen PNG del QR directamente
    // (para mostrar en pantalla o incrustar en PDF para imprimir)
    // ─────────────────────────────────────────────────────────────
    public function imagen($id)
    {
        $despacho = Despacho::with(['mercancias', 'vehiculo', 'usuario'])->find($id);

        if (!$despacho) {
            return response()->json(['message' => 'Despacho no encontrado'], 404);
        }

        // Si ya existe el SVG guardado, servirlo directamente
        $ruta = "qr/despachos/planilla_{$id}.svg";
        if (Storage::disk('public')->exists($ruta)) {
            return response(
                Storage::disk('public')->get($ruta),
                200
            )->header('Content-Type', 'image/svg+xml');
        }

        // Si no existe, generarlo ahora
        self::generarYGuardar($despacho);

        return response(
            Storage::disk('public')->get($ruta),
            200
        )->header('Content-Type', 'image/svg+xml');
    }

    // ─────────────────────────────────────────────────────────────
    // GET /api/despachos/{id}/qr-data
    //
    // Devuelve el JSON con los datos del QR
    // (para que la app móvil lo consuma al escanear)
    // ─────────────────────────────────────────────────────────────
    public function data($id)
    {
        $despacho = Despacho::with(['mercancias', 'vehiculo', 'usuario'])->find($id);

        if (!$despacho) {
            return response()->json(['message' => 'Despacho no encontrado'], 404);
        }

        return response()->json([
            'qr_url'  => $despacho->qr_url,
            'payload' => self::buildQrData($despacho),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/despachos/{id}/qr/regenerar
    //
    // Regenera el QR manualmente (útil si se edita el despacho)
    // ─────────────────────────────────────────────────────────────
    public function regenerar($id)
    {
        $despacho = Despacho::with(['mercancias', 'vehiculo', 'usuario'])->find($id);

        if (!$despacho) {
            return response()->json(['message' => 'Despacho no encontrado'], 404);
        }

        $url = self::generarYGuardar($despacho);

        return response()->json([
            'message' => 'QR regenerado correctamente',
            'qr_url'  => $url,
        ], 200);
    }
}