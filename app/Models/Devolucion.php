<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';

    protected $fillable = [
        'mercancias_id',
        'usuarios_id',
        'despachos_id',
        'fecha_devolucion',
        'motivo_devolucion',
        'estado_devolucion',
        'observaciones',
        'foto_url',          //  foto de evidencia de devolución
    ];

    public function mercancias()
    {
        return $this->belongsTo(Mercancia::class, 'mercancias_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'usuarios_id');
    }

    public function despachos()
    {
        return $this->belongsTo(Despacho::class, 'despachos_id');
    }

    // ─── Evento: al registrar devolución → actualiza seguimiento ─
    // updateOrCreate: cambia estado, NO crea un registro nuevo
    protected static function booted()
    {
        static::created(function ($devolucion) {
            if ($devolucion->mercancias) {
                Seguimiento::updateOrCreate(
                    ['mercancias_id' => $devolucion->mercancias_id],
                    [
                        'estado'       => 'Devolucion exitosa',
                        'observaciones' => $devolucion->observaciones,
                    ]
                );
            }
        });
    }
}