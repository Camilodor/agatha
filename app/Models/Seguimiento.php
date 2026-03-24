<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'mercancias_id',
        'estado',
        'observaciones',
    ];

    public function mercancia()
    {
        return $this->belongsTo(Mercancia::class, 'mercancias_id');
    }
}