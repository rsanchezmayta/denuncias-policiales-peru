<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@denuncias.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin12345'),
            ]
        );

        $this->command->info('Usuario admin@denuncias.test creado (contraseña: admin12345)');
    }
}
