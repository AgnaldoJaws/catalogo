<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoOwnersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@catalogo.local'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin', 'phone' => '11900000000']
        );

        // Alguns donos de negócios
        for ($i=1; $i<=8; $i++) {
            User::updateOrCreate(
                ['email' => "owner{$i}@catalogo.local"],
                ['name' => "Owner {$i}", 'password' => Hash::make('password'), 'role' => 'owner', 'phone' => '119'.str_pad((string)rand(10000000,99999999),8,'0',STR_PAD_LEFT)]
            );
        }
    }
}
