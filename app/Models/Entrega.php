<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $table = 'entregas';

    protected $fillable = [
        'mercancias_id',
        'despachos_id',
        'usuarios_id',
        'nombre_recibe',
        'numero_celular_recibe',
        'observaciones',
        'fecha_entrega',
        'estado_entrega',
        'foto_url',          // foto de evidencia de entrega
    ];

    public function mercancias()
    {
        return $this->belongsTo(Mercancia::class, 'mercancias_id');
    }

    public function despachos()
    {
        return $this->belongsTo(Despacho::class, 'despachos_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'usuarios_id');
    }

    // ─── Evento: al registrar entrega → actualiza seguimiento ─
    //  updateOrCreate: cambia estado, NO crea un registro nuevo
    protected static function booted()
    {
        static::created(function ($entrega) {
            if ($entrega->mercancias) {
                Seguimiento::updateOrCreate(
                    ['mercancias_id' => $entrega->mercancias_id],
                    [
                        'estado'       => 'Entrega exitosa',
                        'observaciones' => $entrega->observaciones,
                    ]
                );
            }
        });
    }
}