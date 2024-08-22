<?php

namespace Database\Seeders;

use App\Models\Inventaris;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            Inventaris::create([
                "code" => "BR-" . $i + 1,
                "stuf_name" => "barang " . $i + 1,
                "category" => "stuf",
                "amount" => 1,
                "condition" => "good",
                "purchase_date" => now(),
                "information" => "okewww"
            ]);
        }
    }
}
