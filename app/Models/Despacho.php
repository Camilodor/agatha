<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    use HasFactory;

    protected $table = 'despachos';

    protected $fillable = [
        'vehiculos_id',
        'usuarios_id',
        'tipo_pago_id',
        'fecha_despacho',
        'negociacion',
        'anticipo',
        'saldo',
        'observaciones_mer',
        'qr_url',            
    ];

    // ─── Relaciones ───────────────────────────────────────

    /**
     * Un despacho tiene MUCHAS mercancías (pivot despacho_mercancia)
     */
    public function mercancias()
    {
        return $this->belongsToMany(
            Mercancia::class,
            'despacho_mercancia',
            'despacho_id',
            'mercancia_id'
        )->withTimestamps();
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculos_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuarios_id');
    }

    public function tipopago()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
    }

    // ─── NOTA ─────────────────────────────────────────────
    // El seguimiento se actualiza en DespachoController@store
    // después del attach(), porque aquí el pivot aún no existe.
}