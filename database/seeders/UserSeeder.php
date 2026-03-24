<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nombre_usuario' => 'admin',
            'nombres' => 'Admin',
            'apellidos' => 'Principal',
            'tipo_documento_id' => 1,
            'numero_documento' => 123456789,
            'celular' => 3001234567,
            'direccion' => 'Bogotá',
            'ciudad' => 'Bogotá',
            'email' => 'admin@example.com',
            'contrasena' => Hash::make('admin123'),
            'tipo_rol_id' => 1
        ]);

        User::create([
            'nombre_usuario' => 'bodeguero',
            'nombres' => 'Bodeguero',
            'apellidos' => 'Sistema',
            'tipo_documento_id' => 1,
            'numero_documento' => 987654321,
            'celular' => 3109876543,
            'direccion' => 'Medellín',
            'ciudad' => 'Medellín',
            'email' => 'bodeguero@example.com',
            'contrasena' => Hash::make('bodeguero123'),
            'tipo_rol_id' => 2
        ]);

        User::create([
            'nombre_usuario' => 'conductor',
            'nombres' => 'Conductor',
            'apellidos' => 'Sistema',
            'tipo_documento_id' => 1,
            'numero_documento' => 192837465,
            'celular' => 3112233445,
            'direccion' => 'Cali',
            'ciudad' => 'Cali',
            'email' => 'conductor@example.com',
            'contrasena' => Hash::make('conductor123'),
            'tipo_rol_id' => 3
        ]);

        User::create([
            'nombre_usuario' => 'cliente',
            'nombres' => 'Cliente',
            'apellidos' => 'Sistema',
            'tipo_documento_id' => 1,
            'numero_documento' => 564738291,
            'celular' => 3209988776,
            'direccion' => 'Cartagena',
            'ciudad' => 'Cartagena',
            'email' => 'cliente@example.com',
            'contrasena' => Hash::make('cliente123'),
            'tipo_rol_id' => 4
        ]);
    }
}