<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referencia_laboral extends Model
{
    use HasFactory;
    
    protected $table = 'referencias_laborales';

    protected $fillable = [
        'usuarios_id',
        'nombre',
        'apellido',
        'parentezco',
        'numero_documento',
        'tipo_documento_id',
        'numero_celular',
        'numero_direccion',
    ];
        public function usuarios()
    {
        return $this->belongsTo(User::class, 'usuarios_id');
    }

    public function tipos_documento()
    {
        return $this->belongsTo(Tipodocumento::class, 'tipo_documento_id');
    }
}
