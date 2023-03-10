<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'apelido' => 'Admin',
                'email' => 'admin@gmail.com',
                'nome_completo' => 'Administrador',
                'nivel' => 'Administrador',
                'password' => '$2y$10$abLBYBFqKz/AcHPRZRBu/uMrFnRjcCEQjKoEd3d4tIlRmeKAWz1zm'
            ],
            [
                'apelido' => 'Administrador do sistema',
                'email' => 'elhadjd73@gmail.com',
                'nome_completo' => 'Administrador do sistema',
                'nivel' => 'Administrador do sistema',
                'password' => '$2y$10$EtOByse9f.5QqFpxnPm/HeiTrWEnH5jngb5KnDr7CXOWtuFEl19mi'
            ]
        ]);
    }
}