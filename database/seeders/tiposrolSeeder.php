<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tiporol;

class tiposrolSeeder extends Seeder
{
    public function run()
    {
        DB::table('tiposrol')->insert([
            ['nombre' => 'Administrador'],
            ['nombre' => 'Bodeguero'],
            ['nombre' => 'Conductor'],
            ['nombre' => 'Cliente'],
        ]);
    }
}
