<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testGetUserUnauthenticate()
    {
        $this->get('/api/users/current', [
            "Authorization" => "token",
            "Accept" => "application/json"
        ])->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }

    public function testGetUserSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])->assertStatus(200);

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $this->get('/api/users/current', [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Success get user"
            ]);
    }

    public function testUpdateProfileSuccess()
    {
        $this->testGetUserSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $user = User::latest()->first();

        $this->put('/api/users/' . $user->id, [
            "name" => "ayikarbail",
            "email" => "ayik@gmail.com",
            "age" => 20,
            "gender" => "male"
        ], [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "data" => [
                    "userId" => $user->id
                ],
                "message" => "Update data success"
            ]);
    }

    public function testUpdateProfileFailed()
    {
        $this->testGetUserSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $user = User::latest()->first();

        $this->put('/api/users/' . $user->id, [
            "name" => "",
            "email" => "ayik",
            "age" => 20,
            "gender" => "male"
        ], [
            "Authorization" => $token
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ],
                    "email" => [
                        "The email field must be a valid email address."
                    ]
                ]
            ]);
    }

    public function testUpdateProfileInvalid()
    {
        $this->testGetUserSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $user = User::latest()->first();

        $this->put('/api/users/' . $user->id + 1, [
            "name" => "ayikarbail",
            "email" => "ayik@gmail.com",
            "age" => 20,
            "gender" => "male"
        ], [
            "Authorization" => $token
        ])->assertStatus(400)
            ->assertJson([
                "status" => false,
                "data" => [],
                "message" => "Your not allowed to update this data"
            ]);
    }
}
