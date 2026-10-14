<?php

namespace Database\Factories;

use App\Models\ClientLogo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClientLogo> */
class ClientLogoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'sort' => 0,
            'is_published' => true,
        ];
    }
}
