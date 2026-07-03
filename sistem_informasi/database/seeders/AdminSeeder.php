<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin2@gmail.com'],
            [
                'name' => 'Admin2',
                'password' => Hash::make('kalisawahsonggon2'),
                'role' => 'admin'
            ]
        );

    }
}
