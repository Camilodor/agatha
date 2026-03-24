<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'usuarios_id',
        'numero_placas',
        'nombre_marca_vehiculo',
        'nombre_propietario_vehiculo',
        'documento_propietario_vehiculo',
        'numero_celular_propietario',
        'direccion_propietario',
        'ciudad_propietario',
        'numero_modelo_anio',
        'color_vehiculo',
        'fecha_vencimiento_soat',
        'fecha_vencimiento_tecno',
        'nombre_satelital',
        'usuario_satelital',
        'contrasena_satelital',
        'capacidad_carga',
        'foto_url',          
    ];

    public function conductor()
    {
        return $this->belongsTo(User::class, 'usuarios_id');
    }
}