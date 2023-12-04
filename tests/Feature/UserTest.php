<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{


public function testRegisterSuccess()
{
    $this->post('/api/users', [
        'username' => 'nichola',
        'password' => 'rahasia123',
        'name' => 'Nichola Saputra'
    ])
    ->assertStatus(201)
    ->assertJson(["data" => [
        'username' => 'nichola',
        'name' => 'Nichola Saputra'
    ]]);
}

public function testRegisterFailed()
{
    $this->post('/api/users', [
        'username' => '',
        'password' => '',
        'name' => ''
    ])
    ->assertStatus(400)
    ->assertJson(["errors" => [
        'username' => [
            'The username field is required.'
        ],
        'password' => [
            'The password field is required.'
        ],
        'name' => [
            'The name field is required.'
        ]

    ]]);
}

public function testRegisterUsernameAlreadyExist()
{
    $this->testRegisterSuccess();
    $this->post('/api/users', [
        'username' => 'nichola',
        'password' => 'rahasia123',
        'name' => 'Nichola Saputra'
    ])
    ->assertStatus(400)
    ->assertJson(["errors" => [
        'username' => [
            'username already registered'
        ],
    ]]);
}

public function testloginSuccess()
{
    $this->seed([UserSeeder::class]);

    $this->post('/api/users/login', [
        'username' => 'nichola',
        'password' => 'rahasia123',
    ])
    ->assertStatus(200)
    ->assertJson([
        "data" => [
            'username' => 'nichola',
            'name' => 'Nichola Saputra'
        ]
    ]);

    $user = User::where('username', 'nichola')->first();

    assertNotNull($user->token);
}


public function testloginFailed()
{
    $this->post('/api/users/login', [
        'username' => 'nichola',
        'password' => 'rahasia123',
    ])
    ->assertStatus(401)
    ->assertJson([
        "errors" => [
            'message' => [
                'username or password wrong'
            ]
        ]
    ]);
}

public function testlogingWrongPassword()
{

    $this->seed([UserSeeder::class]);
    $this->post('/api/users/login', [
        'username' => 'nichola',
        'password' => 'salah',
    ])
    ->assertStatus(401)
    ->assertJson([
        "errors" => [
            'message' => [
                'username or password wrong'
            ]
        ]
    ]);
}

public function testGetSuccess()
{
    $this->seed([UserSeeder::class]);

    $this->get('/api/users/current', [
        'Authorization' => 'mytoken'
    ])->assertStatus(200)
    ->assertJson([
        "data" => [
            "username" => "nichola",
            "name" => "Nichola Saputra"
        ]
    ]);
}

public function testGetUnauthorize()
{
    $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorize'
                    ]
                ]
            ]);
}

public function testGetInvalidToken()
{
    $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorize'
                    ]
                ]
            ]);
}

public function testUpdateNameSuccess()
{
    $this->seed([UserSeeder::class]);
    $oldUser = User::where('username', 'nichola')->first();

    $this->patch('/api/users/current',[
        'name' => 'Makanan'
    ],
     [
        'Authorization' => 'mytoken'
    ])->assertStatus(200)
    ->assertJson([
        "data" => [
            "username" => "nichola",
            "name" => "Makanan"
        ]
    ]);

    $newUser = User::where('username', 'nichola')->first();
    self::assertNotEquals($oldUser->name,$newUser->name);

}

public function testUpdatePasswordSuccess()
{
    $this->seed([UserSeeder::class]);
    $oldUser = User::where('username', 'nichola')->first();

    $this->patch('/api/users/current',[
        'password' => 'baru'
    ],
     [
        'Authorization' => 'mytoken'
    ])->assertStatus(200)
    ->assertJson([
        "data" => [
            "username" => "nichola",
            "name" => "Nichola Saputra"
        ]
    ]);

    $newUser = User::where('username', 'nichola')->first();

    self::assertNotEquals($oldUser->password,$newUser->password);
}

public function testUpdateFailed()
{
    $this->seed([UserSeeder::class]);
    $oldUser = User::where('username', 'nichola')->first();

    $this->patch('/api/users/current',[
        'name' => 'MakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakananMakanan'
    ],
     [
        'Authorization' => 'mytoken'
    ])->assertStatus(400)
    ->assertJson([
        "errors" => [
            'name' => [
                "The name field must not be greater than 100 characters."
            ]
        ]
    ]);

}

public function testLogoutSuccess()
{
    $this->seed([UserSeeder::class]);

    $this->delete('/api/users/logout',headers:[
        'Authorization' => 'mytoken'
    ])
    ->assertStatus(200)
    ->assertJson([
        'data' => true
    ]);

}

public function testLogoutFailed()
{
    $this->seed([UserSeeder::class]);

    $this->delete('/api/users/logout', [
        'Authorization' => 'mytoken'
    ])
    ->assertStatus(401)
    ->assertJson([
        'errors' => [
            'message' => [
                'unauthorize'
            ]
        ]
    ]);
}


}
