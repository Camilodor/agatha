<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TipopagoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\MercanciaController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\TiporolController;
use App\Http\Controllers\TipodocumentoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\QrDespachoController;

// ── Ruta pública ──────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ── Rutas protegidas con JWT ───────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::get('me',         [AuthController::class, 'me']);
    Route::post('logout',    [AuthController::class, 'logout']);

    // Usuarios
    Route::apiResource('usuarios', UserController::class);

    // Catálogos
    Route::apiResource('tipospago',       TipopagoController::class);
    Route::apiResource('tiporol',         TiporolController::class);
    Route::apiResource('tipodocumentos',  TipodocumentoController::class);

    // Vehículos
    Route::apiResource('vehiculos', VehiculoController::class);

    // Proveedores y Productos
    Route::apiResource('proveedores', ProveedorController::class);
    Route::apiResource('productos',   ProductoController::class);

    // Mercancías
    Route::apiResource('mercancias', MercanciaController::class);

    // Despachos (Planillas)
    Route::apiResource('despachos', DespachoController::class);

    // ── QR de Despacho ────────────────────────────────────────────
    // GET  /api/despachos/{id}/qr           → imagen PNG del QR (para imprimir)
    // GET  /api/despachos/{id}/qr-data      → JSON con datos del QR (para app móvil)
    // POST /api/despachos/{id}/qr/regenerar → regenera el QR manualmente
    Route::get('despachos/{id}/qr',            [QrDespachoController::class, 'imagen']);
    Route::get('despachos/{id}/qr-data',       [QrDespachoController::class, 'data']);
    Route::post('despachos/{id}/qr/regenerar', [QrDespachoController::class, 'regenerar']);

    // Entregas
    Route::apiResource('entregas', EntregaController::class);

    // Devoluciones
    Route::apiResource('devoluciones', DevolucionController::class);

    // ── Seguimientos ─────────────────────────────────────────────
    // GET    /api/seguimientos              → lista (filtrada si es cliente)
    // GET    /api/seguimientos/{id}         → ver uno por ID
    // PUT    /api/seguimientos/{id}         → actualizar estado manualmente
    // DELETE /api/seguimientos/{id}         → eliminar
    //
    // 🔍 Ruta extra: ver seguimiento por número de remesa
    // GET    /api/seguimientos/remesa/{numero_remesa}
    Route::get('seguimientos/remesa/{numero_remesa}', [SeguimientoController::class, 'porRemesa']);

    Route::apiResource('seguimientos', SeguimientoController::class)->except(['store']);
    // ⚠️ store está excluido: los seguimientos se crean automáticamente
    //    al crear mercancía (booted en Mercancia.php)
    //    y se actualizan en Despacho, Entrega, Devolucion

    // ── Fotos ─────────────────────────────────────────────────────
    // POST   /api/fotos/{entidad}/{id}  → subir o reemplazar foto
    // DELETE /api/fotos/{entidad}/{id}  → eliminar foto
    //
    // Entidades válidas: usuarios, vehiculos, entregas, devoluciones
    //
    // Ejemplos:
    //   POST   /api/fotos/usuarios/1       (foto de perfil)
    //   POST   /api/fotos/vehiculos/3      (foto del vehículo)
    //   POST   /api/fotos/entregas/2       (evidencia de entrega)
    //   POST   /api/fotos/devoluciones/5   (evidencia de devolución)
    Route::post('fotos/{entidad}/{id}',   [FotoController::class, 'subir']);
    Route::delete('fotos/{entidad}/{id}', [FotoController::class, 'eliminar']);
});