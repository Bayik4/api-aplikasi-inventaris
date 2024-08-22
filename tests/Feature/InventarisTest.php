<?php

namespace Tests\Feature;

use App\Models\Inventaris;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class InventarisTest extends TestCase
{
    public function testCreateInventarisSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])->assertStatus(200);

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $this->post('/api/inventaris', [
            'code' => "SPK-1",
            'stuf_name' => "speaker",
            'category' => "electronics",
            'amount' => 1,
            'condition' => "good",
            'purchase_date' => "2024/07/05",
            'information' => "looting"
        ], [
            "Authorization" => $token
        ])->assertStatus(201);
    }

    public function testCreateInventarisExist()
    {
        $this->testCreateInventarisSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $data = Inventaris::latest()->first();

        $this->post('/api/inventaris', [
            'code' => "SPK-1",
            'stuf_name' => "speaker",
            'category' => "electronics",
            'amount' => 1,
            'condition' => "good",
            'purchase_date' => "2024/07/05",
            'information' => "looting"
        ], [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "data" => [
                    "stuf_name" => $data->stuf_name,
                    "amount" => 2
                ],
                "message" => "Data already exists, the amount of data is increased"
            ]);
    }

    public function testCreateInventarisInvalid()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])->assertStatus(200);

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $this->post('/api/inventaris', [
            'code' => "",
            'stuf_name' => "speaker",
            'category' => "electronics",
            'amount' => 1,
            'condition' => "good",
            'purchase_date' => "2024/07/05",
            'information' => "looting"
        ], [
            "Authorization" => $token
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "code" => [
                    "The code field is required."
                ]
            ]
        ]);
    }

    public function testUpdateInventarisSuccess()
    {
        $this->testCreateInventarisSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $data = Inventaris::latest()->first();

        $this->put('/api/inventaris/' . $data->id, [
            'code' => "SPK-2",
            'stuf_name' => "speaker",
            'category' => "electronics",
            'amount' => 1,
            'condition' => "good",
            'purchase_date' => "2024/07/05",
            'information' => "looting"
        ], [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "data" => [
                    "stuf_name" => $data->stuf_name
                ],
                "message" => "Update data success"
            ]);
    }

    public function testDeleteInventarisSuccess()
    {
        $this->testCreateInventarisSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $data = Inventaris::latest()->first();

        $this->delete('/api/inventaris/' . $data->id, [], [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "data" => [],
                "message" => "Delete data success"
            ]);
    }

    public function testGetInventarisSuccess()
    {
        $this->testCreateInventarisSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $this->get('/api/inventaris', [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Success get data"
            ]);
    }

    public function testGetInventarisEmpty()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/users/login', [
            "email" => "ayik@gmail.com",
            "password" => "12345678"
        ])->assertStatus(200);

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $this->get('/api/inventaris', [
            "Authorization" => $token
        ])->assertStatus(404)
            ->assertJson([
                "status" => false,
                "data" => [],
                "message" => "Record is empty"
            ]);
    }

    public function testGetInventariByIdSuccess()
    {
        $this->testCreateInventarisSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $data = Inventaris::latest()->first();

        $this->get('/api/inventaris/' . $data->id, [
            "Authorization" => $token
        ])->assertStatus(200)
            ->assertJson([
                "status" => true,
                "message" => "Get data success"
            ]);
    }

    public function testGetInventariByIdFailed()
    {
        $this->testCreateInventarisSuccess();

        $token = PersonalAccessToken::where('tokenable_id', Auth::user()->id)->first();

        $data = Inventaris::latest()->first();

        $id = $data->id + 1;

        $this->get('/api/inventaris/' . $id, [
            "Authorization" => $token
        ])->assertStatus(400)
            ->assertJson([
                "status" => false,
                "data" => [
                    "errors" => [
                        "Data with id " . $id . " is not exist"
                    ]
                ],
                "message" => "Get data failed"
            ]);
    }
}
