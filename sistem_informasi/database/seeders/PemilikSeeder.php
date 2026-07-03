<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PemilikSeeder extends Seeder
{
    public function run()
    {
   User::firstOrCreate(
    ['email' => 'owner@gmail.com'],
    [
        'name' => 'Owner Kalisawah',
        'password' => Hash::make('KlswhOwner@26'),
        'role' => 'pemilik'
    ]
);
    }
}