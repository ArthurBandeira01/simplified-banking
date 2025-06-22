<?php

namespace Database\Factories;

use App\Models\Retailer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retailer>
 */
class RetailerFactory extends Factory
{
    protected $model = Retailer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'cnpj' => $this->generateCnpj(),
            'email' => $this->faker->unique()->companyEmail(),
            'password' => Hash::make('password'),
        ];
    }

    private function generateCnpj(): string
    {
        return preg_replace('/\D/', '', $this->faker->cnpj());
    }
}
