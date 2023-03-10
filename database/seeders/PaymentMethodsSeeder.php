<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('methodo_de_pagamento')->insert([
            [
                'nome' => 'Numerario',
                'valor' => 0,
            ],
            [
                'nome' => 'Transferencia',
                'valor' => 0,
            ],
            [
                'nome' => 'Multicaixa',
                'valor' => 0,
            ]
        ]);
    }
}
