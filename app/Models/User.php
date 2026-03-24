<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nombre_usuario',
        'nombres',
        'apellidos',
        'tipo_documento_id',
        'numero_documento',
        'celular',
        'direccion',
        'ciudad',
        'email',
        'contrasena',
        'tipo_rol_id',
        'foto_url',         
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function tipoRol()
    {
        return $this->belongsTo(TipoRol::class, 'tipo_rol_id');
    }

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}