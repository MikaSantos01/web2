<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // <- IMPORTAR AQUI
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@biblioteca.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
