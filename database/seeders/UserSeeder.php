<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'nichola',
            'password' => Hash::make('rahasia123'),
            'name' => 'Nichola Saputra',
            'token' => 'mytoken'
        ]);

        User::create([
            'username' => 'nichola2',
            'password' => Hash::make('rahasia1232'),
            'name' => 'Nichola Saputra2',
            'token' => 'mytoken2'
        ]);
    }
}
