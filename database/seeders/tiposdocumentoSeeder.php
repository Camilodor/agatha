<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tipodocumento;

class tiposdocumentoSeeder extends Seeder
{
    public function run()
    {
        DB::table('tiposdocumento')->insert([
            ['nombre' => 'Cédula de Ciudadanía'],
            ['nombre' => 'Tarjeta de Identidad'],
            ['nombre' => 'Pasaporte'],
            ['nombre' => 'Cédula de Extranjería'],
        ]);
    }
}
