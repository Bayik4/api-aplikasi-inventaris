<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthUserTest extends TestCase
{
    public function testRegisterUser()
    {
        $this->post('/api/users', [
            "name" => "ayik",
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])
            ->assertJson([
                'status' => true,
                'data' => [],
                'message' => "Success register user"
            ])
            ->assertStatus(201);
    }

    public function testRegisterUserInvalid()
    {
        $this->post('/api/users', [
            "name" => "",
            "email" => "ayik",
            "password" => "123456"
        ])
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ],
                    "email" => [
                        "The email field must be a valid email address."
                    ],
                    "password" => [
                        "The password field must be at least 8 characters."
                    ]
                ]
            ])
            ->assertStatus(400);
    }

    public function testRegisterUserEmailAlreadyExist()
    {
        $this->testRegisterUser();

        $this->post('/api/users', [
            "name" => "ayik",
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email has already been taken."
                    ]
                ]
            ])
            ->assertStatus(400);
    }

    public function testUserLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])->assertStatus(200);
    }

    public function testUserLoginFailed()
    {
        $this->testRegisterUser();

        $this->post('/api/users/login', [
            "email" => "ayik12@gmail.com",
            "password" => "12345678"
        ])->assertJson([
            'status' => false,
            'data' => [],
            'message' => 'Email or password wrong'
        ])->assertStatus(400);
    }

    public function testUserLoginInvalid()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "email" => "ayik",
            "password" => "1234567"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email field must be a valid email address."
                    ],
                    "password" => [
                        "The password field must be at least 8 characters."
                    ]
                ]
            ]);
    }

    public function testUserLogout()
    {
        $this->seed([UserSeeder::class]);

        $user = User::first();

        Sanctum::actingAs($user);

        $this->delete('/api/users/logout')->assertJson([
            'status' => true,
            'data' => [],
            'message' => 'You now logout'
        ])->assertStatus(200);
    }
}
