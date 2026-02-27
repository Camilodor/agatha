<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\Referencia_laboralController;
use App\Http\Controllers\Referencia_personalController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SeguimientoController;

// Ruta pública
Route::post('/login', [AuthController::class, 'login'])->name('login');


// Rutas protegidas con JWT
Route::middleware('auth:api')->group(function () {


    Route::get('me', [AuthController::class, 'me']);

    Route::post('logout', [AuthController::class, 'logout']);

     Route::apiResource('usuarios', UserController::class);

    // Tipos de pago
    Route::apiResource('tipospago', TipopagoController::class);

    // Tipos de rol
    Route::apiResource('tiporol', TiporolController::class);

    // Vehículos
    Route::apiResource('vehiculos', VehiculoController::class);

    // Despachos
    Route::apiResource('despachos', DespachoController::class);

    // Mercancías
    Route::apiResource('mercancias', MercanciaController::class);

    // Entregas
    Route::apiResource('entregas', EntregaController::class);

    // Devoluciones
    Route::apiResource('devoluciones', DevolucionController::class);

    // Productos
    Route::apiResource('productos', ProductoController::class);

    // Proveedores
    Route::apiResource('proveedores', ProveedorController::class);

    // Tipo de documento
    Route::apiResource('tipodocumentos', TipodocumentoController::class);

    // Referencia laboral
    Route::apiResource('referencias-laborales', Referencia_laboralController::class);

    // Referencia personal
    Route::apiResource('referencias-personales', Referencia_personalController::class);

    Route::apiResource('seguimientos', SeguimientoController::class);
});
