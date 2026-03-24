<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Referencia_laboral;

class Referencias_laboralesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('referencias_laborales')->insert([
            [
                'usuarios_id' => 1,
                'nombre' => 'Julio',
                'apellido' => 'Pérez',
                'parentezco' => 'Jefe inmediato',
                'numero_documento' => '123454589',
                'tipo_documento_id' => 1, // CC
                'numero_celular' => '3001239067',
                'numero_direccion' => 'Calle 123 #45-67'
            ],
            [
                'usuarios_id' => 2,
                'nombre' => 'Marío',
                'apellido' => 'Gómez',
                'parentezco' => 'Jefe inmediato',
                'numero_documento' => '977654321',
                'tipo_documento_id' => 2, // TI
                'numero_celular' => '3017652321',
                'numero_direccion' => 'Carrera 12 #34-56'
            ],
            [
                'usuarios_id' => 3,
                'nombre' => 'Andrey',
                'apellido' => 'Jaramillo',
                'parentezco' => 'Jefe inmediato',
                'numero_documento' => '456789123',
                'tipo_documento_id' => 1, // CC
                'numero_celular' => '3029867543',
                'numero_direccion' => 'Av. Siempre Viva 742'
            ],
        ]);

}
}
