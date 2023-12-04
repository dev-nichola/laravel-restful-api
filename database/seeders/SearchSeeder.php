<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'nichola')->first();
        for($i = 0; $i < 20; $i++)
        {
            Contact::create([
                "first_name" => "first" . $i,
                "last_name" => "last" . $i,
                "email" => "test" . $i. "@nichola.com",
                "phone" => "11111" . $i,
                "user_id" => $user->id
            ]);
        }
    }
}
