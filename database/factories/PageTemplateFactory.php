<?php

namespace Database\Factories;

use App\Enums\PageType;
use App\Models\PageTemplate;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PageTemplate> */
class PageTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'page_type' => fake()->randomElement(PageType::cases()),
            'name' => fake()->words(2, true),
            'sort' => 0,
        ];
    }
}
