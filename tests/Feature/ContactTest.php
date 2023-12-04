<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;

class ContactTest extends TestCase
{
public function testCreateSuccess()
{
    $this->seed([UserSeeder::class]);

    $this->post('/api/contacts', [
        "first_name" => 'Nichola',
        "last_name" => "Saputra",
        "email" => "nichola@gmail.com",
        "phone" => "+6285124123"
    ], [
        "Authorization" => 'mytoken'
    ])->assertStatus(201)
    ->assertJson([
        "data" => [
        "first_name" => 'Nichola',
        "last_name" => "Saputra",
        "email" => "nichola@gmail.com",
        "phone" => "+6285124123"
        ]
    ]);
        }

public function testCreateFailed()
{
    $this->seed([UserSeeder::class]);

    $this->post('/api/contacts', [
        "first_name" => '',
        "last_name" => "Saputra",
        "email" => "nicholafsdf.com",
        "phone" => "+6285124123"
    ], [
        "Authorization" => 'mytoken'
    ])->assertStatus(400)
    ->assertJson([
        "errors" => [
            "first_name" => [
                "The first name field is required."
            ],
            "email" => [
                "The email field must be a valid email address."
            ]
        ]
    ]);
}

public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'mytoken'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Nichola',
                    'last_name' => 'Saputra',
                    'email' => 'test@gmail.com',
                    'phone' => '08123456789',
                ]
            ]);
    }

public function testGetNotFound()
{
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();

    $this->get('/api/contacts/' . ($contact->id + 1), [
        'Authorization' => 'mytoken'
    ])->assertStatus(404)
        ->assertJson([
            'errors' => [
                "message" => [
                    "not found"
                ]
            ]
        ]);

}

public function testGetOtherUserContact()
{
    $this->seed([UserSeeder::class, ContactSeeder::class]);
    $contact = Contact::query()->limit(1)->first();

    $this->get('/api/contacts/' . $contact->id, [
        'Authorization' => 'mytoken2'
    ])->assertStatus(404)
        ->assertJson([
            'errors' => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
}

public function testUpdateSuccess()
{
    $this->seed([UserSeeder::class, ContactSeeder::class]);

    $contact = Contact::query()->limit(1)->first();

    $this->put('/api/contacts/' . $contact->id, [
        'first_name' => 'Nichola2',
                'last_name' => 'Saputra2',
                'email' => 'test2@gmail.com',
                'phone' => '081234567892',
    ],
    [
        'Authorization' => 'mytoken'
    ],


    )->assertStatus(200)
        ->assertJson([
            'data' => [
                'first_name' => 'Nichola2',
                'last_name' => 'Saputra2',
                'email' => 'test2@gmail.com',
                'phone' => '081234567892',
            ]
        ]);


}

public function testUpdateValidationError()
{
    $this->seed([UserSeeder::class, ContactSeeder::class]);

    $contact = Contact::query()->limit(1)->first();

    $this->put('/api/contacts/' . $contact->id, [
        'first_name' => '',
                'last_name' => 'Saputra2',
                'email' => 'test2@gmail.com',
                'phone' => '081234567892',
    ],
    [
        'Authorization' => 'mytoken'
    ],


    )->assertStatus(400 )
        ->assertJson([
            'errors' => [
                'first_name' => [
                    "The first name field is required."
                ],

            ]
        ]);

}

public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'mytoken'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . ($contact->id + 1), [], [
            'Authorization' => 'mytoken'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'mytoken'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=11111', [
            'Authorization' => 'mytoken'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada', [
            'Authorization' => 'mytoken'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'mytoken'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }

}
