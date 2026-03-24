<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $this->call([
        tiposdocumentoSeeder::class,
        TiposrolSeeder::class,
        TipospagoSeeder::class,
        UserSeeder::class,
        Referencias_personalesSeeder::class,
        Referencias_laboralesSeeder::class,
      
    ]);
}
}
