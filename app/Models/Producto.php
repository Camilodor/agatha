<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    
    protected $table = 'productos';

    protected $fillable = [
        'proveedores_id',
        'nombre',
        'descripcion',
        
    ];

     public function proveedores()
    {
        return $this->belongsTo(Proveedor::class, 'proveedores_id');
    }
    public function mercancias()
{
    return $this->belongsToMany(
        Mercancia::class,
        'mercancia_producto',
        'producto_id',
        'mercancia_id'
    )->withPivot('cantidad')->withTimestamps();
}
}
