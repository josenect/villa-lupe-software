<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si ya existe un admin
        if (!User::where('rol', User::ROL_ADMIN)->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@villalupe.com',
                'password' => Hash::make('admin123'),
                'rol' => User::ROL_ADMIN,
                'activo' => true,
            ]);

            $this->command->info('Usuario administrador creado exitosamente.');
        } else {
            $this->command->warn('Ya existe un usuario administrador.');
        }
    }
}
