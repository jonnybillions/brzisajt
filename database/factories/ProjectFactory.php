<?php

namespace Database\Factories;

use App\Enums\Industry;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'industry' => fake()->randomElement(Industry::cases()),
            'url' => fake()->url(),
            'sort' => 0,
            'is_published' => true,
        ];
    }
}
