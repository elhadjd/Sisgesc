<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('apps')->insert([
            [
                'app_name' => 'faturacao',
                'empresa' => '1',
                'estado' => 'Inativo',
                'image' => 'faturation.png',
            ],

            [
                'app_name' => 'configuracoes',
                'empresa' => '1',
                'estado' => 'Inativo',
                'image' => 'config.png',
            ],
            [
                'app_name' => 'funcionarios',
                'empresa' => '1',
                'estado' => 'Inativo',
                'image' => 'funcionarios.png',
            ],
            [
                'app_name' => 'compra',
                'empresa' => '1',
                'estado' => 'Inativo',
                'image' => 'purchur.jpg',
            ],
            [
                'app_name' => 'pontodevenda',
                'empresa' => '1',
                'estado' => 'Inativo',
                'image' => 'point-of-sale.png',
            ]
        ]);
    }
}