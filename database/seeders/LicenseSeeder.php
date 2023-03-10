<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Licensas')->insert([
            'id_empresa' => '1',
            'Tempo' => '1',
            'created_at' => date("YY-m-d H:M:S"),
            'created_at' => date("YY-m-d H:M:S"),
        ]);
    }
}