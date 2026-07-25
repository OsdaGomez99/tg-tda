<?php

namespace Database\Seeders;

use App\Models\Semestre;
use Illuminate\Database\Seeder;

class SemestreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semestre = Semestre::updateOrCreate(['nombre' => '2026-1']);
        $semestre->activar();
    }
}
