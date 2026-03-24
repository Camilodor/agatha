<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Entrega;
use App\Models\Devolucion;

class FotoController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Mapa de entidades soportadas
    // ─────────────────────────────────────────────────────────────
    private function resolverModelo(string $entidad): ?string
    {
        return match ($entidad) {
            'usuarios'    => User::class,
            'vehiculos'   => Vehiculo::class,
            'entregas'    => Entrega::class,
            'devoluciones'=> Devolucion::class,
            default       => null,
        };
    }

    // ─────────────────────────────────────────────────────────────
    // POST /api/fotos/{entidad}/{id}
    // Sube o reemplaza la foto de cualquier entidad
    //
    // Ejemplos:
    //   POST /api/fotos/usuarios/1
    //   POST /api/fotos/vehiculos/3
    //   POST /api/fotos/entregas/2
    //   POST /api/fotos/devoluciones/5
    // ─────────────────────────────────────────────────────────────
    public function subir(Request $request, string $entidad, int $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:5000',
        ]);

        $modelClass = $this->resolverModelo($entidad);

        if (!$modelClass) {
            return response()->json([
                'message' => "Entidad '$entidad' no válida. Use: usuarios, vehiculos, entregas, devoluciones"
            ], 400);
        }

        $registro = $modelClass::find($id);

        if (!$registro) {
            return response()->json([
                'message' => ucfirst($entidad) . ' no encontrado'
            ], 404);
        }

        // 🗑️ Si ya tiene foto → eliminar el archivo anterior del storage
        if ($registro->foto_url) {
            $rutaAnterior = $this->extraerRutaStorage($registro->foto_url);
            if ($rutaAnterior && Storage::disk('public')->exists($rutaAnterior)) {
                Storage::disk('public')->delete($rutaAnterior);
            }
        }

        // 📁 Guardar nueva foto en storage/app/public/fotos/{entidad}/
        $archivo  = $request->file('foto');
        $nombre   = time() . '_' . $archivo->getClientOriginalName();
        $carpeta  = "fotos/{$entidad}";
        $ruta     = $archivo->storeAs($carpeta, $nombre, 'public');
        $url      = asset('storage/' . $ruta);

        // 💾 Guardar URL en la columna foto_url de la tabla
        $registro->update(['foto_url' => $url]);

        return response()->json([
            'message'  => 'Foto subida correctamente',
            'entidad'  => $entidad,
            'id'       => $id,
            'foto_url' => $url,
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /api/fotos/{entidad}/{id}
    // Elimina la foto de una entidad
    // ─────────────────────────────────────────────────────────────
    public function eliminar(string $entidad, int $id)
    {
        $modelClass = $this->resolverModelo($entidad);

        if (!$modelClass) {
            return response()->json([
                'message' => "Entidad '$entidad' no válida."
            ], 400);
        }

        $registro = $modelClass::find($id);

        if (!$registro) {
            return response()->json([
                'message' => ucfirst($entidad) . ' no encontrado'
            ], 404);
        }

        if (!$registro->foto_url) {
            return response()->json([
                'message' => 'Este registro no tiene foto'
            ], 404);
        }

        // 🗑️ Eliminar archivo del storage
        $ruta = $this->extraerRutaStorage($registro->foto_url);
        if ($ruta && Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }

        $registro->update(['foto_url' => null]);

        return response()->json([
            'message' => 'Foto eliminada correctamente'
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────
    // Extrae la ruta relativa dentro de storage/public
    // a partir de la URL completa guardada en BD
    // Ej: "http://...../storage/fotos/usuarios/abc.jpg"
    //  →  "fotos/usuarios/abc.jpg"
    // ─────────────────────────────────────────────────────────────
    private function extraerRutaStorage(string $url): ?string
    {
        $base = asset('storage/');
        if (str_starts_with($url, $base)) {
            return substr($url, strlen($base) + 1); // +1 por el "/"
        }
        // Fallback: intentar extraer desde "/storage/"
        $pos = strpos($url, '/storage/');
        if ($pos !== false) {
            return substr($url, $pos + strlen('/storage/'));
        }
        return null;
    }
}