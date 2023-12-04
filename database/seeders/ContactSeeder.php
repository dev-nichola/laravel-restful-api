<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'nichola')->first();
        Contact::create([
            "first_name" => 'Nichola',
            "last_name" => "Saputra",
            "email" => "test@gmail.com",
            "phone" => "08123456789",
            "user_id" => $user->id,
        ]);
    }
}
