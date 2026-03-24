<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
     protected $table = 'proveedores';

    protected $fillable = ['usuarios_id', 'nombre', 'descripcion'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuarios_id'); 
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'proveedores_id');
    }
}
