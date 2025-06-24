<?php

namespace Database\Seeders;

use App\Models\Retailer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RetailerTableSeeder extends Seeder
{
    public function run(): void
    {
        $retailers = Retailer::factory()->count(5)->create();

        $retailers->each(function ($retailer) {
            $retailer->wallet()->create([
                'balance' => fake()->randomFloat(2, 100, 1000),
            ]);
        });
    }
}
