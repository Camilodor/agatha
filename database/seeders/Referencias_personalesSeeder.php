<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Referencia_personal;

class Referencias_personalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('referencias_personales')->insert([
            [
                'usuarios_id' => 1,
                'nombre' => 'Carlos',
                'apellido' => 'Pérez',
                'parentezco' => 'Hermano',
                'numero_documento' => '123456789',
                'tipo_documento_id' => 1, // CC
                'numero_celular' => '3001234567',
                'numero_direccion' => 'Calle 123 #45-67'
            ],
            [
                'usuarios_id' => 2,
                'nombre' => 'María',
                'apellido' => 'Gómez',
                'parentezco' => 'Amiga',
                'numero_documento' => '987654321',
                'tipo_documento_id' => 2, // TI
                'numero_celular' => '3017654321',
                'numero_direccion' => 'Carrera 12 #34-56'
            ],
            [
                'usuarios_id' => 3,
                'nombre' => 'Andrés',
                'apellido' => 'Ramírez',
                'parentezco' => 'Primo',
                'numero_documento' => '456789123',
                'tipo_documento_id' => 1, // CC
                'numero_celular' => '3029876543',
                'numero_direccion' => 'Av. Siempre Viva 742'
            ],
        ]);

}
}
