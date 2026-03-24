<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tipopago;

class TipospagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('tipospago')->insert([
            ['nombre' => 'Crédito'],
            ['nombre' => 'Contraentrega'],
            ['nombre' => 'Contado'],
        ]);
    }
}
