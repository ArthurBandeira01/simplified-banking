<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::factory()
            ->count(10)
            ->create();

        $customers->each(function (Customer $customer) {
            $customer->wallet()->create([
                'balance' => fake()->randomFloat(2, 100, 1000),
            ]);
        });
    }
}
